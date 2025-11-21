<?= $this->extend('layouts/base') ?>

<?php
// Load global permission helper
helper('global_permission');

// Get permissions for warehouse module
$permissions = get_global_permission('warehouse');
$can_view = $permissions['view'];
$can_create = $permissions['create'];
$can_edit = $permissions['edit'];
$can_delete = $permissions['delete'];
$can_export = $permissions['export'];
?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<style>
    /* CSS umum sudah ada di optima-pro.css */
    /* Hanya custom styling khusus untuk inventory unit table */
    
    /* Clickable rows khusus untuk inventory-unit-table */
    #inventory-unit-table tbody tr {
        cursor: pointer;
        transition: background-color 0.15s ease-in-out;
    }
    
    #inventory-unit-table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.1) !important;
    }
    
    /* Responsive hide columns khusus untuk inventory-unit-table */
    @media (max-width: 992px){
        #inventory-unit-table td:nth-child(5), #inventory-unit-table th:nth-child(5),
        #inventory-unit-table td:nth-child(6), #inventory-unit-table th:nth-child(6),
        #inventory-unit-table td:nth-child(7), #inventory-unit-table th:nth-child(7){ display:none; }
    }
    
    /* Disabled Table Styling */
    .table-disabled {
        opacity: 0.5 !important;
        pointer-events: none !important;
        user-select: none !important;
        cursor: not-allowed !important;
    }
    .table-disabled tbody tr {
        cursor: not-allowed !important;
        pointer-events: none !important;
    }
    .table-disabled tbody tr:hover {
        background-color: #f8f9fa !important;
    }
    .table-disabled .btn {
        pointer-events: none !important;
        opacity: 0.3 !important;
    }
    .table-disabled a {
        pointer-events: none !important;
        cursor: not-allowed !important;
        color: #6c757d !important;
    }
    .table-disabled input, .table-disabled select {
        pointer-events: none !important;
        opacity: 0.5 !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <!-- Inventory Table dengan Tab Terintegrasi -->
    <div class="card table-card">
        <!-- Tab Filter untuk Status Unit -->
        <div class="card-body p-0">
            <ul class="nav nav-tabs nav-fill mb-0" id="unitStatusTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-category="" type="button" role="tab">
                        <i class="fas fa-list me-1"></i>
                        <span>Semua</span>
                        <span class="badge bg-secondary ms-1" id="count-all"><?= $stats['total'] ?? 0 ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="stock-tab" data-category="stock" type="button" role="tab">
                        <i class="fas fa-warehouse me-1"></i>
                        <span>Stock Unit</span>
                        <span class="badge bg-success ms-1" id="count-stock">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rental-tab" data-category="rental" type="button" role="tab">
                        <i class="fas fa-handshake me-1"></i>
                        <span>Rental</span>
                        <span class="badge bg-warning text-dark ms-1" id="count-rental">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="progress-tab" data-category="progress" type="button" role="tab">
                        <i class="fas fa-cogs me-1"></i>
                        <span>Progress</span>
                        <span class="badge bg-info ms-1" id="count-progress">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="sold-tab" data-category="sold" type="button" role="tab">
                        <i class="fas fa-shopping-cart me-1"></i>
                        <span>Terjual</span>
                        <span class="badge bg-dark ms-1" id="count-sold"><?= $stats['sold'] ?? 0 ?></span>
                    </button>
                </li>
            </ul>
        </div>
        
        <!-- Sub-filter untuk setiap kategori -->
        <div class="card-body border-top" id="subFilterContainer" style="display: none;">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <small class="text-muted me-2">Filter Status:</small>
                <div id="stockSubFilters" class="sub-filter-group" style="display: none;">
                    <button class="btn btn-sm btn-outline-success active" data-sub-status="" type="button">Semua Stock</button>
                    <button class="btn btn-sm btn-outline-success" data-sub-status="1" type="button">Available Stock</button>
                    <button class="btn btn-sm btn-outline-secondary" data-sub-status="2" type="button">Stock Non Aset</button>
                    <button class="btn btn-sm btn-outline-primary" data-sub-status="3" type="button">Booked</button>
                    <button class="btn btn-sm btn-outline-secondary" data-sub-status="9" type="button">Returned</button>
                </div>
                <div id="rentalSubFilters" class="sub-filter-group" style="display: none;">
                    <button class="btn btn-sm btn-outline-warning active" data-sub-status="" type="button">Semua Rental</button>
                    <button class="btn btn-sm btn-outline-warning" data-sub-status="7" type="button">Rental Active</button>
                    <button class="btn btn-sm btn-outline-secondary" data-sub-status="11" type="button">Rental Inactive</button>
                </div>
                <div id="progressSubFilters" class="sub-filter-group" style="display: none;">
                    <button class="btn btn-sm btn-outline-info active" data-sub-status="" type="button">Semua Progress</button>
                    <button class="btn btn-sm btn-outline-info" data-sub-status="4" type="button">In Preparation</button>
                    <button class="btn btn-sm btn-outline-success" data-sub-status="5" type="button">Ready to Deliver</button>
                    <button class="btn btn-sm btn-outline-info" data-sub-status="6" type="button">In Delivery</button>
                    <button class="btn btn-sm btn-outline-danger" data-sub-status="8" type="button">Maintenance</button>
                </div>
            </div>
        </div>
        
        <div class="card-header d-flex align-items-center justify-content-between gap-2 flex-wrap">
            <h5 class="card-title fw-bold m-0">Daftar Stok Unit</h5>
            <div class="d-flex gap-2 ms-auto">
                <a class="btn btn-sm btn-primary" data-bs-toggle="collapse" href="#filterCollapse" role="button" aria-expanded="false" aria-controls="filterCollapse" title="Tampilkan / Sembunyikan Filter">
                    <i class="fas fa-filter me-1"></i>Filter
                </a>
                <?php if ($can_export): ?>
                <a href="<?= base_url('warehouse/inventory/export_unit_inventory') ?>" class="btn btn-sm btn-outline-success" id="btnExport" title="Export CSV">
                    <i class="fas fa-file-export me-1"></i>Export Unit
                </a>
                <?php else: ?>
                <a href="#" class="btn btn-sm btn-outline-success disabled" onclick="return false;" title="Access Denied">
                    <i class="fas fa-file-export me-1"></i>Export Unit
                </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="collapse" id="filterCollapse">
            <div class="card-body bg-light-subtle border-top">
                <form id="filterForm" class="row gx-3 gy-2 align-items-end">
                    <div class="col-md-6 col-sm-12">
                        <label for="filter_departemen" class="form-label">Departemen</label>
                        <select id="filter_departemen" class="form-select">
                            <option value="" selected>Semua Departemen</option>
                            <?php if(!empty($departemen_options)): foreach($departemen_options as $d): ?>
                                <option value="<?= esc($d['id_departemen']) ?>"><?= esc($d['nama_departemen']) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>

                    <div class="col-md-6 col-sm-12 d-flex gap-2">
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
            <?php if (!can_view('warehouse')): ?>
            <div class="alert alert-warning m-3">
                <i class="fas fa-lock me-2"></i>
                <strong>Access Denied:</strong> You do not have permission to view unit inventory. 
                Please contact your administrator to request access.
            </div>
            <?php endif; ?>
            <table id="inventory-unit-table" class="table table-striped table-hover <?= !$can_view ? 'table-disabled' : '' ?>" style="width:100%">
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
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal View Unit Detail - Enhanced Modern Design -->
<div class="modal fade" id="viewUnitModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-cube me-2 text-secondary"></i>Detail Unit Lengkap</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="unitDetailContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer bg-light d-flex justify-content-between">
                <div>
                    <button type="button" class="btn btn-warning me-2" onclick="editUnitFromModal()">
                        <i class="fas fa-edit me-1"></i>Edit Unit
                    </button>
                    <button type="button" class="btn btn-danger" onclick="deleteUnitFromModal()">
                        <i class="fas fa-trash me-1"></i>Hapus Unit
                    </button>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
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
    let currentCategoryFilter = '';
    let currentSubStatusFilter = '';
    let unitTable;
    let departemenFilter = '';

    // Mapping kategori ke status IDs
    const categoryStatusMap = {
        'stock': [1, 2, 3, 9], // AVAILABLE_STOCK, STOCK_NON_ASET, BOOKED, RETURNED
        'rental': [7, 11], // RENTAL_ACTIVE, RENTAL_INACTIVE
        'progress': [4, 5, 6, 8], // IN_PREPARATION, READY_TO_DELIVER, IN_DELIVERY, MAINTENANCE
        'sold': [10] // SOLD
    };

    function updateDynamicBadges(stats){
        if(!stats) return;
        $('#count-all').text(stats.total ?? 0);
        
        // Hitung untuk kategori
        const stockCount = (stats.available_stock ?? 0) + (stats.stock_non_aset ?? 0) + (stats.booked ?? 0) + (stats.returned ?? 0);
        const rentalCount = (stats.rental_active ?? 0) + (stats.rental_inactive ?? 0);
        const progressCount = (stats.in_preparation ?? 0) + (stats.ready_to_deliver ?? 0) + (stats.in_delivery ?? 0) + (stats.maintenance ?? 0);
        
        $('#count-stock').text(stockCount);
        $('#count-rental').text(rentalCount);
        $('#count-progress').text(progressCount);
        $('#count-sold').text(stats.sold ?? 0);
    }

    function showSubFilters(category) {
        // Sembunyikan semua sub-filter
        $('.sub-filter-group').hide();
        
        if (category) {
            $('#subFilterContainer').show();
            $(`#${category}SubFilters`).show();
        } else {
            $('#subFilterContainer').hide();
        }
    }

    function getEffectiveStatusFilter() {
        if (currentSubStatusFilter) {
            return currentSubStatusFilter;
        } else if (currentCategoryFilter && categoryStatusMap[currentCategoryFilter]) {
            return categoryStatusMap[currentCategoryFilter].join(',');
        }
        return '';
    }

    $(document).ready(function(){
        unitTable = $('#inventory-unit-table').DataTable({
            processing:true,
            serverSide:true,
            pageLength:25,
            order:[[8,'desc']], // Tanggal Masuk column (index 8, setelah hapus kolom konfirmasi)
            scrollX:true,
            deferRender:true,
            ajax:{
                url:'<?= base_url('warehouse/inventory/invent_unit') ?>',
                type:'POST',
                data:function(d){
                    d.status_unit = getEffectiveStatusFilter();
                    d.departemen_id = departemenFilter;
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
                        if(s.includes('AVAILABLE_STOCK')) cls='bg-success';
                        else if(s.includes('STOCK_NON_ASET')) cls='bg-warning';
                        else if(s.includes('BOOKED')) cls='bg-primary';
                        else if(s.includes('IN_PREPARATION')) cls='bg-info';
                        else if(s.includes('READY_TO_DELIVER')) cls='bg-success';
                        else if(s.includes('IN_DELIVERY')) cls='bg-info';
                        else if(s.includes('RENTAL_ACTIVE')) cls='bg-warning';
                        else if(s.includes('MAINTENANCE')) cls='bg-danger';
                        else if(s.includes('RETURNED')) cls='bg-secondary';
                        else if(s.includes('SOLD')) cls='bg-dark';
                        else if(s.includes('RENTAL_INACTIVE')) cls='bg-secondary';
                        return `<span class="badge ${cls}">${d}</span>`;
                    }
                },
                { data:'lokasi_unit', render:function(data, type, row) {
                    const statusId = parseInt(row.status_unit_id) || 0;
                    
                    // Jika rental aktif (status_unit_id = 7)
                    if (statusId === 7) {
                        // Cek apakah ada customer data
                        if (row.customer_name) {
                            // Tampilkan nama perusahaan (customer) di atas (hijau, bold)
                            // Tampilkan lokasi di bawah (kecil, abu)
                            let html = `<span class="text-success fw-bold"><i class="fas fa-building me-1"></i>${row.customer_name}</span>`;
                            
                            // Tambahkan lokasi di bawah
                            if (row.customer_location_name && row.customer_city) {
                                html += `<br><small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>${row.customer_location_name} - ${row.customer_city}</small>`;
                            } else if (row.customer_location_name) {
                                html += `<br><small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>${row.customer_location_name}</small>`;
                            } else if (data) {
                                html += `<br><small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>${data}</small>`;
                            }
                            
                            return html;
                        } else {
                            // Fallback: tampilkan lokasi_unit dengan format hijau
                            return `<span class="text-success fw-bold"><i class="fas fa-map-marker-alt me-1"></i>${data || 'Customer Location'}</span>`;
                        }
                    }
                    
                    // Untuk status lain, tampilkan lokasi gudang biasa
                    return data || '-';
                } },
                { data:'tanggal_masuk', render:d=> d||'-' }
            ],
            language:{
                emptyTable:'Tidak ada data tersedia',
                processing:'Memproses...',
                info:'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                infoEmpty:'Menampilkan 0 data',
                paginate:{previous:'Sebelumnya', next:'Berikutnya'}
            },
            dom: 'rtip',
            drawCallback: function() {
                // Add click event to table rows for viewing detail
                $('#inventory-unit-table tbody tr').off('click').on('click', function() {
                    const data = unitTable.row(this).data();
                    if (data && data.id_inventory_unit) {
                        viewUnit(data.id_inventory_unit);
                    }
                });
            }
        });

        // Tab kategori click
        $('#unitStatusTabs .nav-link').on('click', function(){
            $('#unitStatusTabs .nav-link').removeClass('active');
            $(this).addClass('active');
            currentCategoryFilter = $(this).data('category');
            currentSubStatusFilter = ''; // Reset sub-filter
            
            showSubFilters(currentCategoryFilter);
            
            // Reset active sub-filter buttons
            $('.sub-filter-group .btn').removeClass('active');
            $('.sub-filter-group .btn[data-sub-status=""]').addClass('active');
            
            updateDynamicTitle();
            unitTable.ajax.reload();
        });
        
        // Sub-filter click handlers
        $(document).on('click', '.sub-filter-group .btn', function() {
            // Update active state
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
            
            currentSubStatusFilter = $(this).data('sub-status');
            updateDynamicTitle();
            unitTable.ajax.reload();
        });
        // Dropdown filters
        $('#filter_departemen').on('change', function(){
            departemenFilter = this.value; updateDynamicTitle(); unitTable.ajax.reload();
        });
        $('#btnResetFilter').on('click', function(){
            currentCategoryFilter = ''; 
            currentSubStatusFilter = '';
            departemenFilter = ''; 
            $('#filter_departemen').val(''); 
            $('#unitStatusTabs .nav-link').removeClass('active');
            $('#unitStatusTabs .nav-link[data-category=""]').addClass('active');
            showSubFilters('');
            updateDynamicTitle(); 
            unitTable.ajax.reload();
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

    function updateDynamicTitle(){
        let parts = ['Daftar Stok Unit'];
        
        if(currentCategoryFilter){
            const categoryMap = { 
                'stock': 'Stock Unit',
                'rental': 'Rental',
                'progress': 'Progress',
                'sold': 'Terjual'
            };
            parts.push(categoryMap[currentCategoryFilter] || 'Kategori ' + currentCategoryFilter);
        }
        
        if(currentSubStatusFilter) {
            const subStatusMap = {
                '1': 'Available Stock',
                '2': 'Stock Non Aset', 
                '3': 'Booked',
                '4': 'In Preparation',
                '5': 'Ready to Deliver',
                '6': 'In Delivery',
                '7': 'Rental Active',
                '8': 'Maintenance',
                '9': 'Returned',
                '10': 'Sold',
                '11': 'Rental Inactive'
            };
            parts.push(subStatusMap[currentSubStatusFilter] || 'Status ' + currentSubStatusFilter);
        }
        
        if(departemenFilter){
            const txt = $('#filter_departemen option:selected').text();
            if(txt) parts.push(txt);
        }
        
        // Update page title if element exists
        if($('#unitTableTitle').length) {
            $('#unitTableTitle').text(parts.join(' - '));
        }
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
                    currentUnitData = data; // Store data for modal actions
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
        
        const formatCurrency = (value) => {
            if (!value || value === '' || value === null) return '-';
            return new Intl.NumberFormat('id-ID', { 
                style: 'currency', 
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value);
        };
        
        const formatDate = (dateStr) => {
            if (!dateStr || dateStr === '' || dateStr === null) return '-';
            return new Date(dateStr).toLocaleDateString('id-ID', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
        };
        
        const getStatusBadge = (status) => {
            const s = (status || '').toUpperCase();
            let cls = 'bg-secondary';
            if (s.includes('AVAILABLE_STOCK')) cls = 'bg-success';
            else if (s.includes('STOCK_NON_ASET')) cls = 'bg-warning text-dark';
            else if (s.includes('BOOKED')) cls = 'bg-primary';
            else if (s.includes('IN_PREPARATION')) cls = 'bg-info';
            else if (s.includes('READY_TO_DELIVER')) cls = 'bg-success';
            else if (s.includes('IN_DELIVERY')) cls = 'bg-info';
            else if (s.includes('RENTAL_ACTIVE')) cls = 'bg-warning text-dark';
            else if (s.includes('MAINTENANCE')) cls = 'bg-danger';
            else if (s.includes('RETURNED')) cls = 'bg-secondary';
            else if (s.includes('SOLD')) cls = 'bg-dark';
            else if (s.includes('RENTAL_INACTIVE')) cls = 'bg-secondary';
            return `<span class="badge ${cls}">${h(status)}</span>`;
        };
        
        console.log('Creating detail HTML for data:', data);
        
        // Create attachment gallery
        let attachmentHtml = '';
        if (data.attachments && data.attachments.length > 0) {
            attachmentHtml = data.attachments.map(att => `
                <div class="col-md-6 mb-3">
                    <div class="card border-start border-primary border-3">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="card-title mb-0 text-primary">${h(att.attachment_name || att.tipe_item)}</h6>
                                <span class="badge bg-${att.attachment_status === 'AVAILABLE' ? 'success' : att.attachment_status === 'USED' ? 'warning' : 'secondary'} rounded-pill">
                                    ${h(att.attachment_status)}
                                </span>
                            </div>
                            <div class="row text-sm">
                                <div class="col-6">
                                    <small class="text-muted">Type:</small><br>
                                    <small>${h(att.attachment_type || att.tipe_item)}</small>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Serial:</small><br>
                                    <small>${h(att.sn_attachment || att.sn_baterai || att.sn_charger)}</small>
                                </div>
                                <div class="col-6 mt-1">
                                    <small class="text-muted">Kondisi:</small><br>
                                    <small class="badge bg-${att.kondisi_fisik === 'Baik' ? 'success' : att.kondisi_fisik === 'Rusak Ringan' ? 'warning' : 'danger'}">${h(att.kondisi_fisik)}</small>
                                </div>
                                <div class="col-6 mt-1">
                                    <small class="text-muted">Kelengkapan:</small><br>
                                    <small class="badge bg-${att.kelengkapan === 'Lengkap' ? 'success' : 'warning'}">${h(att.kelengkapan)}</small>
                                </div>
                                <div class="col-12 mt-2">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt text-${att.is_following_unit ? 'success' : 'secondary'} me-1"></i>
                                        <small class="text-muted me-1">${h(att.location_label || 'Lokasi')}:</small>
                                        <small class="fw-bold text-${att.is_following_unit ? 'success' : 'dark'}">${h(att.smart_location || att.lokasi_penyimpanan || 'Tidak diketahui')}</small>
                                        ${att.is_following_unit ? '<span class="badge bg-success ms-1" style="font-size: 0.6rem;">Mengikuti Unit</span>' : ''}
                                    </div>
                                </div>
                            </div>
                            ${att.catatan_fisik ? `<hr class="my-2"><small class="text-muted">${h(att.catatan_fisik)}</small>` : ''}
                        </div>
                    </div>
                </div>
            `).join('');
        } else {
            attachmentHtml = '<div class="col-12"><div class="alert alert-info mb-0"><i class="fas fa-info-circle me-2"></i>Tidak ada attachment yang terkait dengan unit ini.</div></div>';
        }
        
        return `
            <!-- Header Info Bar -->
            <div class="bg-light border-bottom p-3">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-1 text-dark">${h(data.merk_unit)} ${h(data.model_unit)}</h4>
                        <p class="mb-0 text-muted">
                            <i class="fas fa-barcode me-2 text-secondary"></i>SN: ${h(data.serial_number)} 
                            ${data.no_unit ? `| <i class="fas fa-hashtag me-1 text-secondary"></i>No. Unit: ${h(data.no_unit)}` : ''}
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="mb-2">${getStatusBadge(data.status_unit_name)}</div>
                        <small class="text-muted"><i class="fas fa-map-marker-alt me-1 text-secondary"></i>${h(data.display_location || data.lokasi_unit)}</small>
                        <br><small class="text-muted" style="font-size: 0.7rem;">${h(data.location_label || 'Lokasi')}</small>
                    </div>
                </div>
            </div>

            <!-- Tabbed Content -->
            <div class="p-3">
                <ul class="nav nav-tabs nav-fill mb-3" id="unitDetailTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">
                            <i class="fas fa-info-circle me-1"></i>Informasi Dasar
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button" role="tab">
                            <i class="fas fa-cogs me-1"></i>Spesifikasi
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="customer-tab" data-bs-toggle="tab" data-bs-target="#customer" type="button" role="tab">
                            <i class="fas fa-user-tie me-1"></i>Pelanggan & Area
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="attachments-tab" data-bs-toggle="tab" data-bs-target="#attachments" type="button" role="tab">
                            <i class="fas fa-paperclip me-1"></i>Attachment
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="contract-tab" data-bs-toggle="tab" data-bs-target="#contract" type="button" role="tab">
                            <i class="fas fa-handshake me-1"></i>Kontrak
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="unitDetailTabContent">
                    <!-- Basic Information Tab -->
                    <div class="tab-pane fade show active" id="basic" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-header bg-light text-dark border-bottom">
                                        <h6 class="mb-0"><i class="fas fa-truck me-2 text-secondary"></i>Informasi Unit</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr><td width="40%"><strong>ID Unit</strong></td><td>: ${h(data.id_inventory_unit)}</td></tr>
                                            <tr><td><strong>Serial Number</strong></td><td>: <code>${h(data.serial_number)}</code></td></tr>
                                            <tr><td><strong>No. Unit</strong></td><td>: ${h(data.no_unit) || '<span class="text-muted">Belum ada</span>'}</td></tr>
                                            <tr><td><strong>Merk</strong></td><td>: ${h(data.merk_unit)}</td></tr>
                                            <tr><td><strong>Model</strong></td><td>: ${h(data.model_unit)}</td></tr>
                                            <tr><td><strong>Tipe Unit</strong></td><td>: ${h(data.nama_tipe_unit)}</td></tr>
                                            <tr><td><strong>Kapasitas</strong></td><td>: ${h(data.kapasitas_unit)}</td></tr>
                                            <tr><td><strong>Tahun</strong></td><td>: ${h(data.tahun_unit)}</td></tr>
                                            <tr><td><strong>Departemen</strong></td><td>: ${h(data.nama_departemen)}</td></tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-header bg-light text-dark border-bottom">
                                        <h6 class="mb-0"><i class="fas fa-calendar me-2 text-secondary"></i>Timeline & Status</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr><td width="40%"><strong>Status</strong></td><td>: ${getStatusBadge(data.status_unit_name)}</td></tr>
                                            <tr><td><strong>${h(data.location_label || 'Lokasi')}</strong></td><td>: <i class="fas fa-map-marker-alt text-${data.is_rental_active ? 'success' : 'danger'} me-1"></i>${h(data.display_location || data.lokasi_unit)}</td></tr>
                                            ${data.is_rental_active && data.lokasi_unit ? `<tr><td><strong>Lokasi Gudang</strong></td><td>: <i class="fas fa-warehouse text-secondary me-1"></i>${h(data.lokasi_unit)}</td></tr>` : ''}
                                            <tr><td><strong>Tanggal Masuk</strong></td><td>: ${formatDate(data.tanggal_masuk)}</td></tr>
                                            <tr><td><strong>Tanggal Update</strong></td><td>: ${formatDate(data.tanggal_update)}</td></tr>
                                            <tr><td><strong>Tanggal Kirim</strong></td><td>: ${formatDate(data.tanggal_kirim)}</td></tr>
                                            ${data.workflow_status ? `<tr><td><strong>Workflow</strong></td><td>: <span class="badge bg-info">${h(data.workflow_status)}</span></td></tr>` : ''}
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        ${data.keterangan ? `
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-light text-dark border-bottom">
                                        <h6 class="mb-0"><i class="fas fa-sticky-note me-2 text-secondary"></i>Keterangan</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">${h(data.keterangan)}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        ` : ''}
                    </div>

                    <!-- Specifications Tab -->
                    <div class="tab-pane fade" id="specs" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-light">
                                    <div class="card-header bg-light text-dark border-bottom">
                                        <h6 class="mb-0"><i class="fas fa-cogs me-2 text-secondary"></i>Komponen Utama</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr><td width="35%"><strong>Tipe Mast</strong></td><td>: ${h(data.tipe_mast)}</td></tr>
                                            <tr><td><strong>Tinggi Mast</strong></td><td>: ${h(data.tinggi_mast)}</td></tr>
                                            <tr><td><strong>SN Mast</strong></td><td>: <code>${h(data.sn_mast)}</code></td></tr>
                                            <tr><td><strong>Merk Mesin</strong></td><td>: ${h(data.merk_mesin)}</td></tr>
                                            <tr><td><strong>Model Mesin</strong></td><td>: ${h(data.model_mesin)}</td></tr>
                                            <tr><td><strong>SN Mesin</strong></td><td>: <code>${h(data.sn_mesin)}</code></td></tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-light">
                                    <div class="card-header bg-light text-dark border-bottom">
                                        <h6 class="mb-0"><i class="fas fa-circle me-2 text-secondary"></i>Roda & Ban</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr><td width="35%"><strong>Tipe Ban</strong></td><td>: ${h(data.tipe_ban)}</td></tr>
                                            <tr><td><strong>Tipe Roda</strong></td><td>: ${h(data.tipe_roda)}</td></tr>
                                            <tr><td><strong>Jumlah Valve</strong></td><td>: ${h(data.jumlah_valve)}</td></tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        ${data.aksesoris ? `
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-light text-dark border-bottom">
                                        <h6 class="mb-0"><i class="fas fa-plus-circle me-2 text-secondary"></i>Aksesoris</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">${h(data.aksesoris)}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        ` : ''}
                    </div>

                    <!-- Customer & Area Tab -->
                    <div class="tab-pane fade" id="customer" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-header bg-light text-dark border-bottom">
                                        <h6 class="mb-0"><i class="fas fa-building me-2 text-secondary"></i>Informasi Pelanggan</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr><td width="35%"><strong>Nama</strong></td><td>: ${h(data.customer_name)}</td></tr>
                                            <tr><td><strong>Kode</strong></td><td>: <code>${h(data.customer_code)}</code></td></tr>
                                            <tr><td><strong>Lokasi</strong></td><td>: ${h(data.customer_location_name)}</td></tr>
                                            <tr><td><strong>Alamat</strong></td><td>: ${h(data.customer_address)}</td></tr>
                                            <tr><td><strong>Kota</strong></td><td>: ${h(data.customer_city)}</td></tr>
                                            <tr><td><strong>Contact Person</strong></td><td>: ${h(data.customer_contact)}</td></tr>
                                            <tr><td><strong>Phone</strong></td><td>: ${h(data.customer_phone)}</td></tr>
                                            <tr><td><strong>Email</strong></td><td>: ${h(data.customer_email)}</td></tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-header bg-light text-dark border-bottom">
                                        <h6 class="mb-0"><i class="fas fa-map me-2 text-secondary"></i>Informasi Area</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr><td width="35%"><strong>Area</strong></td><td>: ${h(data.area_name)}</td></tr>
                                            <tr><td><strong>Kode Area</strong></td><td>: <code>${h(data.area_code)}</code></td></tr>
                                            <tr><td><strong>Deskripsi</strong></td><td>: ${h(data.area_description)}</td></tr>
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="card border-light mt-3">
                                    <div class="card-header bg-light text-dark border-bottom">
                                        <h6 class="mb-0"><i class="fas fa-file-invoice me-2 text-secondary"></i>Purchase Order</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr><td width="35%"><strong>No. PO</strong></td><td>: <code>${h(data.no_po)}</code></td></tr>
                                            <tr><td><strong>Tanggal PO</strong></td><td>: ${formatDate(data.tanggal_po)}</td></tr>
                                            <tr><td><strong>Status PO</strong></td><td>: <span class="badge bg-secondary">${h(data.status_po)}</span></td></tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attachments Tab -->
                    <div class="tab-pane fade" id="attachments" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><i class="fas fa-paperclip me-2"></i>Attachment & Komponen</h6>
                            <span class="badge bg-primary rounded-pill">${data.attachments ? data.attachments.length : 0} Items</span>
                        </div>
                        <div class="row">
                            ${attachmentHtml}
                        </div>
                    </div>

                    <!-- Contract Tab -->
                    <div class="tab-pane fade" id="contract" role="tabpanel">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card border-success">
                                    <div class="card-header bg-light text-dark border-bottom">
                                        <h6 class="mb-0"><i class="fas fa-handshake me-2 text-secondary"></i>Informasi Kontrak</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr><td width="35%"><strong>No. Kontrak</strong></td><td>: <code>${h(data.no_kontrak)}</code></td></tr>
                                            <tr><td><strong>Status</strong></td><td>: <span class="badge bg-${data.status_kontrak === 'Aktif' ? 'success' : data.status_kontrak === 'Berakhir' ? 'danger' : 'warning'}">${h(data.status_kontrak)}</span></td></tr>
                                            <tr><td><strong>Jenis Sewa</strong></td><td>: ${h(data.jenis_sewa)}</td></tr>
                                            <tr><td><strong>Mulai</strong></td><td>: ${formatDate(data.kontrak_mulai)}</td></tr>
                                            <tr><td><strong>Berakhir</strong></td><td>: ${formatDate(data.kontrak_berakhir)}</td></tr>
                                            ${data.contract_disconnect_date ? `<tr><td><strong>Disconnect</strong></td><td>: ${formatDate(data.contract_disconnect_date)}</td></tr>` : ''}
                                            ${data.contract_disconnect_stage ? `<tr><td><strong>Stage</strong></td><td>: <span class="badge bg-warning">${h(data.contract_disconnect_stage)}</span></td></tr>` : ''}
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-info">
                                    <div class="card-header bg-light text-dark border-bottom">
                                        <h6 class="mb-0"><i class="fas fa-clipboard-list me-2 text-secondary"></i>SPK & Delivery</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr><td><strong>SPK ID</strong></td><td>: ${h(data.spk_id) || '-'}</td></tr>
                                            <tr><td><strong>DI ID</strong></td><td>: ${h(data.delivery_instruction_id) || '-'}</td></tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
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

    // Variables to store current unit data for modal actions
    let currentUnitData = null;

    // Edit unit from modal detail
    function editUnitFromModal() {
        if (!currentUnitData) {
            Swal.fire('Error', 'Data unit tidak tersedia', 'error');
            return;
        }
        
        // Close detail modal and populate edit modal
        $('#viewUnitModal').modal('hide');
        
        // Populate edit form with current data
        $('#edit_id').val(currentUnitData.id_inventory_unit);
        $('#edit_serial_number').val(currentUnitData.serial_number_po || '');
        $('#edit_merk').val(currentUnitData.merk_unit || '');
        $('#edit_status_unit').val(currentUnitData.status_unit_id || '');
        $('#edit_lokasi').val(currentUnitData.lokasi_unit || '');
        
        // Show edit modal
        $('#editUnitModal').modal('show');
    }

    // Delete unit from modal detail
    function deleteUnitFromModal() {
        if (!currentUnitData) {
            Swal.fire('Error', 'Data unit tidak tersedia', 'error');
            return;
        }
        
        // Close detail modal and call delete function
        $('#viewUnitModal').modal('hide');
        deleteUnit(currentUnitData.id_inventory_unit);
    }
</script>
<?= $this->endSection() ?>
