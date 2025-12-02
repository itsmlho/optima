<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-3">
    <h5 class="mb-3"><i class="fas fa-cog me-2"></i>Page Title</h5>
    
    
<?php
// Load global permission helper
helper('global_permission');

// Get permissions for marketing module
$permissions = get_global_permission('marketing');
$can_view = $permissions['view'];
$can_create = $permissions['create'];
$can_edit = $permissions['edit'];
$can_delete = $permissions['delete'];
$can_export = $permissions['export'];
?>

<?php if (can_export('marketing')): ?>
                        <button id="btnExport" class="btn btn-sm btn-success" title="Export CSV"><i class="fas fa-file-export"></i> Export</button>
                        <?php else: ?>
                        <button id="btnExport" class="btn btn-sm btn-success disabled" onclick="return false;" title="Access Denied"><i class="fas fa-file-export"></i> Export</button>
                        <?php endif; ?>
                </div>
        </div>
        <div class="collapse" id="mktFilterCollapse">
                <div class="border-top bg-light p-3">
                        <form id="mktFilterForm" class="row g-3 align-items-end">
                                <div class="col-md-4 col-sm-6">
                                        <label class="form-label small mb-1">Tipe / Jenis</label>
                                        <input type="text" id="tipeFilter" class="form-control form-control-sm" placeholder="Forklift, Crane...">
                                </div>
                                <div class="col-md-4 col-sm-6">
                                        <label class="form-label small mb-1">Lokasi</label>
                                        <input type="text" id="lokasiFilter" class="form-control form-control-sm" placeholder="POS 1">
                                </div>
                                <div class="col-md-4 col-sm-12 d-flex gap-2">
                                        <button type="submit" class="btn btn-sm btn-success flex-grow-1"><i class="fas fa-check me-1"></i>Terapkan</button>
                                        <button type="button" id="mktResetFilter" class="btn btn-sm btn-outline-secondary" title="Reset"><i class="fas fa-undo"></i></button>
                                </div>
                        </form>
                </div>
        </div>
        <?php if (!$can_view): ?>
        <div class="alert alert-warning m-3">
            <i class="fas fa-lock me-2"></i>
            <strong>Access Denied:</strong> You do not have permission to view unit management. 
            Please contact your administrator to request access.
        </div>
        <?php endif; ?>
        <div class="card-body pt-2">
            <table id="marketing-units-table" class="table table-sm table-hover w-100 <?= !$can_view ? 'table-disabled' : '' ?>">
                    <thead>
                            <tr>
                                    <th>No. Unit</th>
                                    <th>Serial</th>
                                    <th>Brand / Model</th>
                                    <th>Tipe</th>
                                    <th>Kapasitas</th>
                                    <th>Lokasi</th>
                                    <th>Departemen</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                            </tr>
                    </thead>
            </table>
            <small class="text-muted d-block mt-2">Tampilan readonly dari Inventory Unit status 7 & 8. Filter & tab mengikuti gaya halaman Warehouse.</small>
    </div>
</div>

<!-- Detail Modal (optional simplified) -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Detail Unit</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailBody"><div class="text-muted text-center py-5">Memuat...</div></div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <button class="btn btn-primary btn-sm" id="btnDetailQuote">Buat Penawaran</button>
            </div>
        </div>
    </div>
</div>

    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
let marketingTable;
let mktStatusFilter = '';

function statusBadge(id){
        id = parseInt(id||0);
        const map={7:['success','STOCK ASET'],8:['success','STOCK NON ASET']};
        const cfg = map[id] || ['secondary','UNKNOWN'];
        return `<span class="badge bg-${cfg[0]}">${cfg[1]}</span>`;
}

// Attachment icon dihapus sesuai permintaan, fungsi diganti placeholder bila diperlukan nanti.
function attachmentIcon(){ return ''; }

function initMarketingTable(){
        marketingTable = $('#marketing-units-table').DataTable({
                processing:true,
                serverSide:true,
                ajax:{
                        url:"<?= base_url('marketing/available-units/data') ?>",
                        type:'POST',
                        data:function(d){
                                d.status_tab = mktStatusFilter || '';
                                d.tipe = $('#tipeFilter').val();
                                d.lokasi = $('#lokasiFilter').val();
                                d['<?= csrf_token() ?>'] = $('meta[name="csrf-token"]').attr('content');
                        },
                        dataSrc:function(json){
                                if(json?.csrf_hash){ $('meta[name="csrf-token"]').attr('content', json.csrf_hash); }
                                if(json.error){ console.warn(json.error); }
                                return json.data || [];
                        },
                        error:function(xhr){
                                console.error('Load error', xhr.status, xhr.responseText);
                                alert('Gagal memuat data: '+xhr.status);
                        }
                },
                order:[[0,'asc']],
                columns:[
                        {data:'no_unit', render:d=> d? `<strong>${d}</strong>`:'-'},
                        {data:'serial_number'},
                        {data:null, render:r=>`<div class='brand-cell'><span class='brand'>${r.brand||'-'}</span><span class='model'>${r.model||''}</span></div>`},
                        {data:'type_full'},
                        {data:'capacity'},
                        {data:'lokasi_unit'},
                        {data:'nama_departemen', render:d=> d||'-'},
                        {data:'status_id', orderable:false, render:id=> statusBadge(id)},
                        {data:null, orderable:false, searchable:false, render:r=> actionMenu(r.id)}
                ],
                dom:'rtip'
        });

        // Global search manual debounce -> integrate DataTables search
        let tmr; $('#globalSearch').on('input', function(){
                clearTimeout(tmr); const v=this.value; tmr=setTimeout(()=>{ marketingTable.search(v).draw(); },300);
        });
}

function actionMenu(id){
        return `<div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-h"></i></button>
                <ul class="dropdown-menu dropdown-menu-sm">
                    <li><a class="dropdown-item" href="#" onclick="viewDetail(${id})"><i class='fas fa-eye me-2 text-info'></i>Lihat</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('marketing/quotations') ?>?unit=${id}"><i class='fas fa-file-invoice me-2 text-primary'></i>Quotations</a></li>
                    <li><a class="dropdown-item" href="#" onclick="reserveUnit(${id})"><i class='fas fa-calendar-plus me-2 text-success'></i>Booking</a></li>
                </ul>
        </div>`;
}

function viewDetail(id){
        $('#detailBody').html('<div class="text-center text-muted py-5">Memuat...</div>');
        $('#detailModal').modal('show');
        // Endpoint detail disesuaikan ke warehouse/getUnitFullDetail
        // Gunakan proxy marketing agar konsisten: marketing/unit-detail/{id}
        fetch('<?= base_url('marketing/unit-detail') ?>/'+id)
            .then(r=>r.json())
            .then(j=>{
                if(!j.success){ $('#detailBody').html('<div class="text-danger">Gagal memuat</div>'); return; }
                const d=j.data;
                                $('#detailBody').html(`
                                        <div class="row g-3">
                                                <div class="col-md-6">
                                                        <h6 class="fw-bold mb-2">Informasi Unit</h6>
                                                        <table class="table table-sm mb-0">
                                                                <tr><td class='small text-muted'>No. Unit</td><td><strong>${d.no_unit||'-'}</strong></td></tr>
                                                                <tr><td class='small text-muted'>Serial</td><td>${d.serial_number_po||'-'}</td></tr>
                                                                <tr><td class='small text-muted'>Merk / Model</td><td>${d.merk_unit||'-'} ${d.model_unit||''}</td></tr>
                                                                <tr><td class='small text-muted'>Tipe</td><td>${d.nama_tipe_unit||'-'}</td></tr>
                                                                <tr><td class='small text-muted'>Kapasitas</td><td>${d.kapasitas_unit||'-'}</td></tr>
                                                                <tr><td class='small text-muted'>Tahun</td><td>${d.tahun_po||'-'}</td></tr>
                                                                <tr><td class='small text-muted'>Keterangan</td><td>${d.keterangan||'-'}</td></tr>
                                                        </table>
                                                </div>
                                                <div class="col-md-6">
                                                        <h6 class="fw-bold mb-2">Spesifikasi Teknis</h6>
                                                        <table class="table table-sm mb-0">
                                                                <tr><td class='small text-muted'>Mast</td><td>${d.tipe_mast||'-'} (${d.sn_mast_po||'-'})</td></tr>
                                                                <tr><td class='small text-muted'>Mesin</td><td>${(d.merk_mesin||'-')} ${(d.model_mesin||'')} SN: ${(d.sn_mesin_po||'-')}</td></tr>
                                                                <tr><td class='small text-muted'>Baterai</td><td>${(d.merk_baterai||'-')} ${(d.tipe_baterai||'')} SN: ${(d.sn_baterai_po||'-')}</td></tr>
                                                                <tr><td class='small text-muted'>Ban</td><td>${d.tipe_ban||'-'}</td></tr>
                                                                <tr><td class='small text-muted'>Roda</td><td>${d.tipe_roda||'-'}</td></tr>
                                                                <tr><td class='small text-muted'>Valve</td><td>${d.jumlah_valve||'-'}</td></tr>
                                                        </table>
                                                </div>
                                                <div class="col-12">
                                                        <h6 class="fw-bold mt-3 mb-2">Status</h6>
                                                        <div class="row small">
                                                                <div class="col-md-4"><strong>Status:</strong> ${d.status_unit_name||'-'}</div>
                                                                <div class="col-md-4"><strong>Departemen:</strong> ${d.nama_departemen||'-'}</div>
                                                                <div class="col-md-4"><strong>Lokasi:</strong> ${d.lokasi_unit||'-'}</div>
                                                        </div>
                                                </div>
                                        </div>`);
            })
            .catch(()=> $('#detailBody').html('<div class="text-danger">Error server</div>'));
}

function reserveUnit(id){ alert('Implement booking untuk ID '+id); }
// Tombol SPK dihilangkan sesuai permintaan

function exportCSV(){
    const params={status_tab: $('#statusFilter').val()||'all',tipe:$('#tipeFilter').val(),lokasi:$('#lokasiFilter').val(),length:-1,start:0,draw:1};
    params['<?= csrf_token() ?>']=$('meta[name="csrf-token"]').attr('content');
    $.post('<?= base_url('marketing/available-units/data') ?>',params,function(resp){
                        let csv='NoUnit,Serial,Brand,Model,Tipe,Capacity,Lokasi,Departemen,Status\n';
            (resp.data||[]).forEach(r=>{
                                csv += [r.no_unit||'',r.serial_number||'',r.brand||'',r.model||'',(r.type_full||'').replace(/,/g,' '),r.capacity||'',r.lokasi_unit||'',r.nama_departemen||'',r.status_name||'']
                    .map(v=>`"${(v||'').toString().replace(/"/g,'""')}"`).join(',')+'\n';
            });
            const blob=new Blob([csv],{type:'text/csv;charset=utf-8;'}); const a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='marketing_units.csv'; a.click(); URL.revokeObjectURL(a.href);
            if(resp?.csrf_hash){ $('meta[name="csrf-token"]').attr('content', resp.csrf_hash); }
    },'json').fail(x=>alert('Export gagal: '+x.status));
}

function updateMktBadges(){
        // Hitung cepat per status (reuse endpoint dengan length=1 untuk efisiensi) mirip implementasi sebelumnya
        ['','7','8'].forEach(st=>{
                const params={draw:1,start:0,length:1,status_tab:st||'',tipe:$('#tipeFilter').val(),lokasi:$('#lokasiFilter').val()};
                params['<?= csrf_token() ?>']=$('meta[name="csrf-token"]').attr('content');
                $.post('<?= base_url('marketing/available-units/data') ?>',params,(resp)=>{
                        if(resp?.csrf_hash){ $('meta[name="csrf-token"]').attr('content', resp.csrf_hash); }
                        const count = resp.recordsFiltered||0;
                        if(st==='') $('#mkt-count-all').text(count); else $('#mkt-count-'+st).text(count);
                },'json');
        });
}

$(function(){
        initMarketingTable();
        updateMktBadges();
        $('#mktStatusTabs .nav-link').on('click', function(){
                $('#mktStatusTabs .nav-link').removeClass('active');
                $(this).addClass('active');
                mktStatusFilter = $(this).data('status');
                marketingTable.ajax.reload(()=>updateMktBadges(), false);
        });
        $('#mktFilterForm').on('submit', function(e){ e.preventDefault(); marketingTable.ajax.reload(()=>updateMktBadges(), false); });
        $('#mktResetFilter').on('click', function(){ $('#mktFilterForm')[0].reset(); marketingTable.ajax.reload(()=>updateMktBadges(), false); });
        $('#tipeFilter,#lokasiFilter').on('keyup', function(){ marketingTable.ajax.reload(null,false); });
        $('#btnClearSearch').on('click', function(){ $('#globalSearch').val(''); marketingTable.search('').draw(); updateMktBadges(); });
        $('#btnExport').on('click', exportCSV);
});
</script>
<?= $this->endSection() ?>