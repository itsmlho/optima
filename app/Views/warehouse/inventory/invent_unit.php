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

<?= $this->section('content') ?>

    <!-- Inventory Table dengan Tab Terintegrasi - Professional Standard -->
    <div class="card shadow-sm">
        <!-- Tab Filter untuk Status Unit -->
        <div class="card-body p-0">
            <ul class="nav nav-tabs mb-0" id="unitStatusTabs" role="tablist">
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
                        <span class="badge bg-warning ms-1" id="count-rental">0</span>
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
                <small class="text-muted me-2">Filter:</small>
                <div id="stockSubFilters" class="sub-filter-group" style="display: none;">
                    <button class="btn btn-sm btn-outline-success active" data-sub-status="" type="button">All Stock</button>
                    <button class="btn btn-sm btn-outline-success" data-sub-status="1" type="button">Available Stock</button>
                    <button class="btn btn-sm btn-outline-secondary" data-sub-status="2" type="button">Stock Non Aset</button>
                    <button class="btn btn-sm btn-outline-primary" data-sub-status="3" type="button">Booked</button>
                    <button class="btn btn-sm btn-outline-secondary" data-sub-status="9" type="button">Returned</button>
                </div>
                <div id="rentalSubFilters" class="sub-filter-group" style="display: none;">
                    <button class="btn btn-sm btn-outline-warning active" data-sub-status="" type="button">All Rental</button>
                    <button class="btn btn-sm btn-outline-warning" data-sub-status="7" type="button">Rental Active</button>
                    <button class="btn btn-sm btn-outline-secondary" data-sub-status="11" type="button">Rental Inactive</button>
                </div>
                <div id="progressSubFilters" class="sub-filter-group" style="display: none;">
                    <button class="btn btn-sm btn-outline-info active" data-sub-status="" type="button">All Progress</button>
                    <button class="btn btn-sm btn-outline-info" data-sub-status="4" type="button">In Preparation</button>
                    <button class="btn btn-sm btn-outline-success" data-sub-status="5" type="button">Ready to Deliver</button>
                    <button class="btn btn-sm btn-outline-info" data-sub-status="6" type="button">In Delivery</button>
                    <button class="btn btn-sm btn-outline-danger" data-sub-status="8" type="button">Maintenance</button>
                </div>
            </div>
        </div>
        
        <div class="card-header d-flex align-items-center justify-content-between gap-2 flex-wrap">
            <h6 class="card-title mb-0">List of Unit Stock</h6>
            <div class="d-flex gap-2 ms-auto">
                <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" href="#filterCollapse" role="button" aria-expanded="false" aria-controls="filterCollapse" title="Show / Hide Filter">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
                <?php if ($can_export): ?>
                <a href="<?= base_url('warehouse/inventory/export_unit_inventory') ?>" class="btn btn-sm btn-outline-success" id="btnExport" title="Export CSV">
                    <i class="fas fa-file-export me-1"></i>Export Unit
                </a>
                <?php else: ?>
                <button class="btn btn-sm btn-outline-success" disabled onclick="return false;" title="Access Denied">
                    <i class="fas fa-file-export me-1"></i>Export Unit
                </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="collapse" id="filterCollapse">
            <div class="card-body bg-light-subtle border-top">
                <form id="filterForm" class="row gx-3 gy-2 align-items-end">
                    <div class="col-md-6 col-sm-12">
                        <label for="filter_departemen" class="form-label">Department</label>
                        <select id="filter_departemen" class="form-select">
                            <option value="" selected>All Departments</option>
                            <?php if(!empty($departemen_options)): foreach($departemen_options as $d): ?>
                                <option value="<?= esc($d['id_departemen']) ?>"><?= esc($d['nama_departemen']) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>

                    <div class="col-md-6 col-sm-12 d-flex gap-2">
                        <button type="submit" class="btn btn-success flex-grow-1">
                            <i class="fas fa-check me-1"></i> Apply
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
                    <span class="input-group-text"><i class="fas fa-search text-secondary"></i></span>
                    <input type="text" id="unitSearch" class="form-control" placeholder="Search Serial / Location / Brand / Model..." autocomplete="off">
                    <button class="btn btn-outline-secondary" type="button" id="btnClearSearch" title="Clear search"><i class="fas fa-times"></i></button>
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
            <div class="table-responsive">
                <table id="inventory-unit-table" class="table table-sm mb-0 <?= !$can_view ? 'table-disabled' : '' ?>">
                    <thead>
                        <tr>
                        <th>Unit Number</th>
                        <th>Serial Number</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Type</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Location</th>
                        <th>Entry Date  </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

<!-- Modal View Unit Detail - Enhanced Modern Design -->
<div class="modal fade" id="viewUnitModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold"><i class="fas fa-cube me-2 text-secondary"></i>Detailed Unit Information</h5>
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
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
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
                <h5 class="modal-title">Edit Stock Unit</h5>
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
                        <label for="edit_status_unit" class="form-label">Unit Status</label>
                        <select class="form-select" id="edit_status_unit" name="status_unit" required>
                            <option value="7">STOCK ASSET</option>
                            <option value="3">RENTAL</option>
                            <option value="9">SOLD</option>
                            <option value="2">WORKSHOP-DAMAGED</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_lokasi" class="form-label">Unit Location</label>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
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
                { 
                    data: null,
                    title: 'No. Unit',
                    render: function(data, type, row) {
                        let display = '';
                        
                        if (row.no_unit) {
                            // Asset number (no prefix)
                            display = '<span class="badge bg-success">' + row.no_unit + '</span>';
                        } else if (row.no_unit_na) {
                            // Non-Asset number
                            display = '<span class="badge bg-warning text-dark">' + row.no_unit_na + '</span>';
                        } else if (row.status_unit_id == 8 || row.status_unit_id == 2) {
                            // Non-Asset without number - show assign button
                            display = '<span class="badge bg-secondary">TEMP-' + row.id_inventory_unit + '</span> ' +
                                      '<button class="btn btn-xs btn-primary mt-1" onclick="assignNonAssetNumber(' + 
                                      row.id_inventory_unit + ')" title="Assign Nomor Non-Asset">' +
                                      '<i class="fas fa-hashtag"></i></button>';
                        } else {
                            // Asset without number (waiting for conversion)
                            display = '<span class="badge bg-secondary">TEMP-' + row.id_inventory_unit + '</span>';
                        }
                        
                        return display;
                    }
                },
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
                emptyTable:'no data available in table',
                processing:'Processing...',
                info:'Showing _START_ - _END_ of _TOTAL_ entries',
                infoEmpty:'Showing 0 entries',
                paginate:{previous:'Previous', next:'Next'}
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
                    if(r.success){ $('#editUnitModal').modal('hide'); unitTable.ajax.reload(null,false); Swal.fire('Success!', r.message,'success'); }
                    else { Swal.fire('Fail!', r.message,'error'); }
                },
                error:function(){ Swal.fire('Error!','Cannot connect to the server.','error'); }
            });
        });
    });

    function updateDynamicTitle(){
        let parts = ['List of Units'];
        
        if(currentCategoryFilter){
            const categoryMap = { 
                'stock': 'Stock Units',
                'rental': 'Rental',
                'progress': 'Progress',
                'sold': 'Sold Units'
            };
            parts.push(categoryMap[currentCategoryFilter] || 'Category ' + currentCategoryFilter);
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
        $.ajax({
            url: `<?= base_url('warehouse/inventory/get-unit-full-detail/') ?>${id}`,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('#unitDetailContent').html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><br><br>Loading unit details...</div>');
                $('#viewUnitModal').modal('show');
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    currentUnitData = data; // Store data for modal actions
                    const detailHtml = createUnitDetailHtml(data);
                    $('#unitDetailContent').html(detailHtml);
                } else {
                    const errorHtml = `
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle"></i> Failed to Load Details</h5>
                            <p>${response.message || 'An unknown error occurred'}</p>
                        </div>
                    `;
                    $('#unitDetailContent').html(errorHtml);
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', {xhr, status, error});
                console.log('Response Text:', xhr.responseText);
                
                let errorMessage = 'An error occurred while loading unit details.';
                
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
        
        const formatDate = (dateStr) => {
            if (!dateStr || dateStr === '' || dateStr === null) return '-';
            return new Date(dateStr).toLocaleDateString('id-ID', { 
                year: 'numeric', 
                month: 'short', 
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

        const getSiloBadge = (status) => {
            if (!status || status === 'BELUM_ADA') return '<span class="badge bg-secondary">NO SILO</span>';
            const s = status.toUpperCase();
            if (s === 'SILO_TERBIT') return '<span class="badge bg-success">SILO ISSUED</span>';
            if (s === 'SILO_EXPIRED') return '<span class="badge bg-danger">EXPIRED</span>';
            if (s === 'PENGAJUAN_PJK3') return '<span class="badge bg-info">PJK3 SUBMISSION</span>';
            if (s === 'TESTING_PJK3') return '<span class="badge bg-info">PJK3 TESTING</span>';
            if (s === 'SURAT_KETERANGAN_PJK3') return '<span class="badge bg-info">PJK3 LETTER</span>';
            if (s === 'PENGAJUAN_UPTD') return '<span class="badge bg-warning text-dark">UPTD SUBMISSION</span>';
            if (s === 'PROSES_UPTD') return '<span class="badge bg-warning text-dark">UPTD PROCESS</span>';
            return `<span class="badge bg-secondary">${h(status)}</span>`;
        };
        
        // Create simple attachment table
        let attachmentRows = '';
        if (data.attachments && data.attachments.length > 0) {
            attachmentRows = data.attachments.map((att, index) => {
                // Initialize variables
                let name = '';
                let type = '';
                let serialNum = '-';
                let status = 'N/A';
                let condition = 'N/A';
                let location = 'Not Set';
                
                // Determine type based on tipe_item field
                const itemType = (att.tipe_item || '').toLowerCase();
                
                if (itemType === 'battery' || att.sn_baterai) {
                    // BATTERY - build comprehensive name with brand and type
                    const batteryBrand = att.merk_baterai || '';
                    const batteryType = att.baterai_type || '';
                    const batteryJenis = att.jenis_baterai || '';
                    
                    // Build name with available info
                    const nameParts = [];
                    if (batteryBrand) nameParts.push(batteryBrand);
                    if (batteryJenis) nameParts.push(batteryJenis);
                    if (batteryType) nameParts.push(batteryType);
                    
                    name = nameParts.length > 0 ? nameParts.join(' - ') : 'Battery';
                    type = 'BATTERY';
                    serialNum = att.sn_baterai || '-';
                    status = att.attachment_status || att.status_baterai || att.status || 'N/A';
                    condition = att.kondisi_fisik || att.kondisi_baterai || att.kondisi || 'N/A';
                    location = att.smart_location || att.lokasi_penyimpanan || att.lokasi_baterai || 'Not Set';
                } 
                else if (itemType === 'charger' || att.sn_charger) {
                    // CHARGER - build comprehensive name with brand
                    const chargerBrand = att.merk_charger || '';
                    const chargerType = att.charger_type || '';
                    
                    // Build name with available info
                    const nameParts = [];
                    if (chargerBrand) nameParts.push(chargerBrand);
                    if (chargerType) nameParts.push(chargerType);
                    
                    name = nameParts.length > 0 ? nameParts.join(' - ') : 'Charger';
                    type = 'CHARGER';
                    serialNum = att.sn_charger || '-';
                    status = att.attachment_status || att.status_charger || att.status || 'N/A';
                    condition = att.kondisi_fisik || att.kondisi_charger || att.kondisi || 'N/A';
                    location = att.smart_location || att.lokasi_penyimpanan || att.lokasi_charger || 'Not Set';
                }
                else {
                    // REGULAR ATTACHMENT
                    name = att.attachment_name || att.nama_attachment || att.nama || att.name || 'Attachment';
                    type = (att.attachment_type || att.tipe_attachment || att.tipe_item || 'ATTACHMENT').toUpperCase();
                    serialNum = att.sn_attachment || att.serial_number || att.serial_number_attachment || '-';
                    status = att.attachment_status || att.status || att.status_item || 'N/A';
                    condition = att.kondisi_fisik || att.kondisi || att.physical_condition || 'N/A';
                    location = att.smart_location || att.lokasi_penyimpanan || att.lokasi_attachment || att.location || att.lokasi || 'Not Set';
                }
                
                return `
                <tr>
                    <td><strong>${h(name)}</strong></td>
                    <td>${h(type)}</td>
                    <td><code class="small">${h(serialNum)}</code></td>
                    <td><span class="badge bg-light text-dark border">${h(status)}</span></td>
                    <td>${h(condition)}</td>
                    <td><small class="text-muted">${h(location)}</small></td>
                </tr>
                `;
            }).join('');
        } else {
            attachmentRows = '<tr><td colspan="6" class="text-center text-muted py-3">No attachments available</td></tr>';
        }
        
        return `
            <!-- Simple Header -->
            <div class="border-bottom px-3 py-3" style="background-color: #f8f9fa;">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            ${data.no_unit ? `<span class="badge text-dark px-2 py-1" style="background-color: #dce4f7; font-size: 0.75rem; font-weight: 600;">UNIT NO: ${h(data.no_unit)}</span>` : ''}
                            <h5 class="mb-0">${h(data.merk_unit)} ${h(data.model_unit)}</h5>
                        </div>
                        <div class="text-muted small">
                            <i class="fas fa-barcode me-1"></i>Serial Number: <strong>${h(data.serial_number || data.serial_number_po || data.sn_unit || 'Not Available')}</strong>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        ${getStatusBadge(data.status_unit_name)}
                        <div class="small text-muted mt-2">
                            <i class="fas fa-map-marker-alt me-1"></i>${h(data.display_location || data.lokasi_unit || 'Location Not Set')}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation -->
            <ul class="nav nav-tabs px-3 pt-2" style="background-color: #f8f9fa; margin-bottom: 0;">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-unit-info">
                        <i class="fas fa-info-circle me-1"></i>Unit Details
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-customer">
                        <i class="fas fa-user-tie me-1"></i>Customer & Contract
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content p-3">
                <!-- Tab 1: Unit Details -->
                <div class="tab-pane fade show active" id="tab-unit-info">
                    <!-- Basic Information -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr><td width="40%" class="text-muted">Unit Type</td><td><strong>${h(data.nama_tipe_unit)}</strong></td></tr>
                                <tr><td class="text-muted">Capacity</td><td><strong>${h(data.kapasitas_unit)}</strong></td></tr>
                                <tr><td class="text-muted">Year</td><td>${h(data.tahun_unit)}</td></tr>
                                <tr><td class="text-muted">Department</td><td>${h(data.nama_departemen)}</td></tr>
                                <tr><td class="text-muted">HM (Hour Meter)</td><td><span class="badge bg-primary text-white px-2">${h(data.hm_unit) || '0'} Hours</span></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr><td width="40%" class="text-muted">Entry Date</td><td>${formatDate(data.tanggal_masuk)}</td></tr>
                                <tr><td class="text-muted">Update Date</td><td>${formatDate(data.tanggal_update)}</td></tr>
                                <tr><td class="text-muted">Shipping Date</td><td>${formatDate(data.tanggal_kirim)}</td></tr>
                                <tr><td class="text-muted">Location Type</td><td><strong>${h(data.location_label || 'Warehouse')}</strong></td></tr>
                                ${data.workflow_status ? `<tr><td class="text-muted">Workflow</td><td><span class="badge bg-info text-white px-2">${h(data.workflow_status)}</span></td></tr>` : ''}
                            </table>
                        </div>
                    </div>

                    <!-- Specifications -->
                    <h6 class="border-bottom pb-2 mb-3 mt-2"><i class="fas fa-cogs me-2"></i>Specifications</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="mb-2"><span class="badge bg-secondary text-white px-2 py-1" style="font-size: 0.7rem;">MAIN COMPONENTS</span></div>
                            <table class="table table-sm table-borderless mb-0">
                                <tr><td width="40%" class="text-muted">Mast Type</td><td>${h(data.tipe_mast)}</td></tr>
                                <tr><td class="text-muted">Mast Height</td><td>${h(data.tinggi_mast)}</td></tr>
                                <tr><td class="text-muted">Mast SN</td><td><code class="small">${h(data.sn_mast)}</code></td></tr>
                                <tr><td class="text-muted">Engine Brand</td><td><strong>${h(data.merk_mesin)}</strong></td></tr>
                                <tr><td class="text-muted">Engine Model</td><td>${h(data.model_mesin)}</td></tr>
                                <tr><td class="text-muted">Engine SN</td><td><code class="small">${h(data.sn_mesin)}</code></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2"><span class="badge bg-secondary text-white px-2 py-1" style="font-size: 0.7rem;">WHEELS & TIRES</span></div>
                            <table class="table table-sm table-borderless mb-0">
                                <tr><td width="40%" class="text-muted">Tire Type</td><td>${h(data.tipe_ban)}</td></tr>
                                <tr><td class="text-muted">Wheel Type</td><td>${h(data.tipe_roda)}</td></tr>
                                <tr><td class="text-muted">Valves</td><td>${h(data.jumlah_valve)}</td></tr>
                            </table>
                            ${data.aksesoris ? `
                                <div class="mt-3">
                                    <div class="mb-2"><span class="badge bg-secondary text-white px-2 py-1" style="font-size: 0.7rem;">ACCESSORIES</span></div>
                                    <p class="small mb-0 text-muted">${h(data.aksesoris)}</p>
                                </div>
                            ` : ''}
                        </div>
                    </div>

                    <!-- Attachments -->
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-paperclip me-2"></i>Attachments & Components <small class="text-muted">(${data.attachments ? data.attachments.length : 0})</small>
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" style="border: 1px solid #dee2e6;">
                            <thead style="background-color: #e9ecef;">
                                <tr>
                                    <th class="text-uppercase" style="font-size: 0.75rem; font-weight: 600;">Name</th>
                                    <th class="text-uppercase" style="font-size: 0.75rem; font-weight: 600;">Type</th>
                                    <th class="text-uppercase" style="font-size: 0.75rem; font-weight: 600;">Serial Number</th>
                                    <th class="text-uppercase" style="font-size: 0.75rem; font-weight: 600;">Status</th>
                                    <th class="text-uppercase" style="font-size: 0.75rem; font-weight: 600;">Condition</th>
                                    <th class="text-uppercase" style="font-size: 0.75rem; font-weight: 600;">Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${attachmentRows}
                            </tbody>
                        </table>
                    </div>

                    <!-- SILO Dropdown -->
                    <div class="mt-4">
                        <div class="accordion" id="siloAccordion">
                            <div class="accordion-item" style="border: 1px solid #dee2e6;">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#siloCollapse" style="background-color: #f8f9fa;">
                                        <i class="fas fa-certificate me-2"></i><strong>SILO Information</strong>
                                        <span class="ms-2">${getSiloBadge(data.silo_status)}</span>
                                    </button>
                                </h2>
                                <div id="siloCollapse" class="accordion-collapse collapse" data-bs-parent="#siloAccordion">
                                    <div class="accordion-body">
                                        ${data.silo_number ? `
                                        <div class="row">
                                            <div class="col-md-8">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tr><td width="35%">SILO Number</td><td><strong>${h(data.silo_number)}</strong></td></tr>
                                                    <tr><td>Issue Date</td><td>${formatDate(data.silo_issue_date)}</td></tr>
                                                    <tr><td>Expiry Date</td><td class="${data.silo_status === 'SILO_EXPIRED' ? 'text-danger fw-bold' : ''}">${formatDate(data.silo_expiry_date)}</td></tr>
                                                    ${data.silo_pjk3_name ? `<tr><td>PJK3 Company</td><td>${h(data.silo_pjk3_name)}</td></tr>` : ''}
                                                    ${data.silo_pjk3_letter ? `<tr><td>PJK3 Letter No</td><td>${h(data.silo_pjk3_letter)}</td></tr>` : ''}
                                                    ${data.silo_disnaker_location ? `<tr><td>Disnaker Location</td><td>${h(data.silo_disnaker_location)}</td></tr>` : ''}
                                                </table>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                ${data.silo_status === 'SILO_TERBIT' && data.silo_file_path ? `
                                                    <a href="<?= base_url() ?>${data.silo_file_path}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       target="_blank">
                                                        <i class="fas fa-download me-1"></i>Download SILO
                                                    </a>
                                                ` : `
                                                    <button class="btn btn-sm btn-secondary" disabled>
                                                        <i class="fas fa-file-pdf me-1"></i>No File
                                                    </button>
                                                `}
                                            </div>
                                        </div>
                                        ` : `
                                        <div class="text-center text-muted py-3">
                                            <i class="fas fa-info-circle me-2"></i>No SILO data available for this unit
                                        </div>
                                        `}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    ${data.keterangan ? `
                    <div class="mt-3 p-2 border-start border-3 border-info" style="background-color: #f8f9fa;">
                        <small class="text-muted"><strong>Notes:</strong></small>
                        <p class="mb-0 small">${h(data.keterangan)}</p>
                    </div>
                    ` : ''}
                </div>

                <!-- Tab 2: Customer & Contract -->
                <div class="tab-pane fade" id="tab-customer">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-2"><i class="fas fa-building me-1"></i>Customer Information</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr><td width="35%">Name</td><td>${h(data.customer_name)}</td></tr>
                                <tr><td>Code</td><td><small>${h(data.customer_code)}</small></td></tr>
                                <tr><td>Location</td><td>${h(data.customer_location_name)}</td></tr>
                                <tr><td>Address</td><td>${h(data.customer_address)}</td></tr>
                                <tr><td>City</td><td>${h(data.customer_city)}</td></tr>
                                <tr><td>Contact</td><td>${h(data.customer_contact)}</td></tr>
                                <tr><td>Phone</td><td>${h(data.customer_phone)}</td></tr>
                                <tr><td>Email</td><td>${h(data.customer_email)}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-2"><i class="fas fa-handshake me-1"></i>Contract Information</h6>
                            <table class="table table-sm table-borderless mb-3">
                                <tr><td width="35%">Contract No</td><td><small>${h(data.no_kontrak)}</small></td></tr>
                                <tr><td>Status</td><td>${getStatusBadge(data.status_kontrak)}</td></tr>
                                <tr><td>Lease Type</td><td>${h(data.jenis_sewa)}</td></tr>
                                <tr><td>Start Date</td><td>${formatDate(data.kontrak_mulai)}</td></tr>
                                <tr><td>End Date</td><td>${formatDate(data.kontrak_berakhir)}</td></tr>
                                ${data.contract_disconnect_date ? `<tr><td>Disconnect Date</td><td>${formatDate(data.contract_disconnect_date)}</td></tr>` : ''}
                            </table>

                            <h6 class="border-bottom pb-2 mb-2"><i class="fas fa-file-alt me-1"></i>Documents</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr><td width="35%">SPK Number</td><td>${h(data.nomor_spk) || '-'}</td></tr>
                                <tr><td>Delivery Number</td><td>${h(data.nomor_di) || '-'}</td></tr>
                                <tr><td>PO Number</td><td><small>${h(data.no_po)}</small></td></tr>
                                <tr><td>PO Date</td><td>${formatDate(data.tanggal_po)}</td></tr>
                            </table>
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
            title: 'Delete Unit?',
            html: 'This action <b>cannot be undone</b>.<br>Ensure the unit has no active transactions.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
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
                        Swal.fire('Success', r.message || 'Unit deleted', 'success');
                        unitTable.ajax.reload(null,false);
                    } else {
                        Swal.fire('Failed', r.message || 'Unit could not be deleted', 'error');
                    }
                },
                error: function(xhr){
                    let msg = 'Server did not respond';
                    if(xhr.responseJSON && xhr.responseJSON.message){
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', msg, 'error');
                }
            });
        });
    }

    // Assign Non-Asset Number with Gap-Filling Strategy
    function assignNonAssetNumber(unitId) {
        Swal.fire({
            title: 'Assign Nomor Non-Asset?',
            text: 'Nomor akan di-generate otomatis dengan format NA-001 sampai NA-500 (gap-filling)',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Assign',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('warehouse/assignNonAssetNumber') ?>',
                    type: 'POST',
                    data: {
                        id: unitId,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                html: 'Nomor Non-Asset: <strong>' + response.no_unit_na + '</strong>',
                                confirmButtonText: 'OK'
                            });
                            unitTable.ajax.reload(null, false);
                        } else {
                            Swal.fire('Gagal', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Terjadi kesalahan sistem';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            }
        });
    }

    // Variables to store current unit data for modal actions
    let currentUnitData = null;

    // Edit unit from modal detail
    function editUnitFromModal() {
        if (!currentUnitData) {
            Swal.fire('Error', 'Unit data not available', 'error');
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
            Swal.fire('Error', 'Unit data not available', 'error');
            return;
        }
        
        // Close detail modal and call delete function
        $('#viewUnitModal').modal('hide');
        deleteUnit(currentUnitData.id_inventory_unit);
    }
</script>
<?= $this->endSection() ?>
