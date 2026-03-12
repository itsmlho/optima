<?= $this->extend('layouts/base') ?>

<?php
/**
 * Unit Inventory (Unit Management) Module
 *
 * BADGE SYSTEM: Menggunakan Optima Badge Standards (optima-pro.css)
 * Direct CSS classes - tidak perlu JavaScript helper function
 *
 * Quick Reference:
 * - Stock / Available  → badge-soft-green
 * - Rented / In Progress → badge-soft-yellow, badge-soft-cyan
 * - Booked / In Delivery → badge-soft-blue
 * - Maintenance        → badge-soft-red
 * - Returned / Inactive → badge-soft-gray
 *
 * See optima-pro.css line ~2030 for complete badge standards
 */

helper('global_permission');
$permissions = get_global_permission('warehouse');
$can_view = $permissions['view'];
$can_create = $permissions['create'];
$can_edit = $permissions['edit'];
$can_delete = $permissions['delete'];
$can_export = $permissions['export'];
?>

<?= $this->section('content') ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white pt-4 pb-3 border-bottom-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="card-title mb-0">
                    <i class="bi bi-box-seam me-2 text-primary"></i>
                    Unit Inventory System
                </h5>
                <p class="text-muted small mb-0">
                    Track, manage, and monitor all equipment units in a single unified registry.
                    <span class="ms-2 text-info"><i class="bi bi-info-circle me-1"></i><small>Tip: Use tabs and filters to narrow by status</small></span>
                </p>
            </div>
            
            <!-- Quick Actions -->
            <div class="d-flex gap-2 mt-3 mt-md-0">
                <?php if ($can_export): ?>
                <a href="<?= base_url('warehouse/inventory/export_unit_inventory') ?>" class="btn btn-outline-success">
                    <i class="fas fa-file-excel me-1"></i> Export Data
                </a>
                <?php endif; ?>
                
                <?php if ($can_create): ?>
                <a href="<?= base_url('warehouse/inventory/unit/create') ?>" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus me-1"></i> Add Unit
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tab Filter (Modern Pilled Design) -->
        <div class="card-header bg-light border-top border-bottom py-2">
            <ul class="nav nav-pills align-items-center py-1 gap-2" id="unitStatusTabs" role="tablist">
                <li class="nav-item me-1" role="presentation">
                    <span class="text-muted small fw-semibold me-2 text-uppercase letter-spacing-1">Master View:</span>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill px-3 py-1" id="all-tab" data-category="" type="button" role="tab">
                        All Records <span class="badge badge-soft-gray ms-1 shadow-sm" id="count-all"><?= $stats['total'] ?? 0 ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 py-1 text-success" id="stock-tab" data-category="stock" type="button" role="tab">
                        Warehouse Stock <span class="badge badge-soft-green ms-1 shadow-sm" id="count-stock">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 py-1 text-warning" id="rental-tab" data-category="rental" type="button" role="tab">
                        Rented <span class="badge badge-soft-yellow ms-1 shadow-sm" id="count-rental">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 py-1 text-info" id="progress-tab" data-category="progress" type="button" role="tab">
                        In Progress <span class="badge badge-soft-cyan ms-1 shadow-sm" id="count-progress">0</span>
                    </button>
                </li>
            </ul>
        </div>
        
        <!-- Sub-filter categories -->
        <div class="card-body border-bottom bg-white py-3" id="subFilterContainer" style="display: none;">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <small class="text-muted fw-bold me-2">Sub Filter:</small>
                <div id="stockSubFilters" class="sub-filter-group" style="display: none;">
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="subFilter" id="sub-all-stock" data-sub-status="" checked>
                        <label class="btn btn-sm btn-outline-secondary" for="sub-all-stock">All Stock</label>
                        
                        <input type="radio" class="btn-check" name="subFilter" id="sub-available" data-sub-status="1">
                        <label class="btn btn-sm btn-outline-success" for="sub-available">Available</label>
                        
                        <input type="radio" class="btn-check" name="subFilter" id="sub-non-asset" data-sub-status="2">
                        <label class="btn btn-sm btn-outline-warning" for="sub-non-asset">Non-Asset</label>
                        
                        <input type="radio" class="btn-check" name="subFilter" id="sub-booked" data-sub-status="3">
                        <label class="btn btn-sm btn-outline-primary" for="sub-booked">Booked</label>
                        
                        <input type="radio" class="btn-check" name="subFilter" id="sub-returned" data-sub-status="9">
                        <label class="btn btn-sm btn-outline-secondary" for="sub-returned">Returned</label>
                    </div>
                </div>
                <!-- Additional Rental and Progress Sub Filters -->
                <div id="rentalSubFilters" class="sub-filter-group" style="display: none;">
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="subFilter" id="sub-all-rental" data-sub-status="" checked>
                        <label class="btn btn-sm btn-outline-warning" for="sub-all-rental">All Rental</label>
                        
                        <input type="radio" class="btn-check" name="subFilter" id="sub-rental-active" data-sub-status="7">
                        <label class="btn btn-sm btn-outline-success" for="sub-rental-active">Active</label>
                        
                        <input type="radio" class="btn-check" name="subFilter" id="sub-rental-inactive" data-sub-status="11">
                        <label class="btn btn-sm btn-outline-secondary" for="sub-rental-inactive">Inactive</label>
                    </div>
                </div>
                <!-- Progress Filters -->
                <div id="progressSubFilters" class="sub-filter-group" style="display: none;">
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="subFilter" id="sub-all-progress" data-sub-status="" checked>
                        <label class="btn btn-sm btn-outline-info" for="sub-all-progress">All Progress</label>
                        
                        <input type="radio" class="btn-check" name="subFilter" id="sub-in-prep" data-sub-status="4">
                        <label class="btn btn-sm btn-outline-warning" for="sub-in-prep">In Preparation</label>
                        
                        <input type="radio" class="btn-check" name="subFilter" id="sub-ready-deliv" data-sub-status="5">
                        <label class="btn btn-sm btn-outline-success" for="sub-ready-deliv">Ready to Deliver</label>

                        <input type="radio" class="btn-check" name="subFilter" id="sub-in-deliv" data-sub-status="6">
                        <label class="btn btn-sm btn-outline-primary" for="sub-in-deliv">In Delivery</label>

                        <input type="radio" class="btn-check" name="subFilter" id="sub-maintenance" data-sub-status="8">
                        <label class="btn btn-sm btn-outline-danger" for="sub-maintenance">Maintenance</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body pt-4">
            <!-- Advanced Toolbar -->
            <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
                <div class="search-box" style="width: 100%; max-width: 400px; position: relative;">
                    <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" id="unitSearch" class="form-control bg-light border-0 ps-5" placeholder="Search by Serial, Location, Brand..." autocomplete="off">
                </div>
                
                <div class="d-flex gap-2 flex-grow-1 flex-md-grow-0 justify-content-end">
                    <select id="filter_departemen" class="form-select form-select-sm" style="max-width: 200px;">
                        <option value="" selected>All Departments</option>
                        <?php if(!empty($departemen_options)): foreach($departemen_options as $d): ?>
                            <option value="<?= esc($d['id_departemen']) ?>"><?= esc($d['nama_departemen']) ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
            </div>

            <?php if (!can_view('warehouse')): ?>
            <div class="alert alert-warning border-0 shadow-sm rounded-3 mb-4">
                <div class="d-flex align-items-center">
                    <div class="bg-warning text-white rounded-circle p-2 me-3 fs-5">
                        <i class="fas fa-lock text-dark"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 text-dark fw-bold"><?= lang('App.access_restricted') ?></h6>
                        <p class="mb-0 text-dark small"><?= lang('App.no_permission_view') ?> <?= strtolower(lang('App.unit_inventory')) ?>. <?= lang('App.contact_administrator') ?>.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- The Main DataTable -->
            <div class="table-responsive">
                <table id="unitTable" class="table table-striped table-hover mb-0 align-middle border-bottom <?= !$can_view ? 'table-disabled' : '' ?>">
                    <thead class="table-light text-muted small text-uppercase letter-spacing-1">
                        <tr>
                            <th class="ps-3 border-0 rounded-start">Unit No</th>
                            <th class="border-0">Serial Number</th>
                            <th class="border-0">Brand & Model</th>
                            <th class="border-0">Department</th>
                            <th class="border-0">Status</th>
                            <th class="border-0">Location</th>
                            <th class="border-0">Reg. Date</th>
                            <th class="text-end pe-3 border-0 rounded-end">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
$(document).ready(function() {
    let currentCategory = '';
    let currentSubStatus = '';

    // Init DataTable
    const table = $('#unitTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 20,
        order: [[6, 'desc']], // Order by Reg Date DESC
        dom: '<"top">rt<"bottom d-flex justify-content-between align-items-center mt-3"ipl><"clear">',
        ajax: {
            url: "<?= base_url('warehouse/inventory/unit/datatable') ?>",
            type: "POST",
            data: function (d) {
                d.status_unit   = currentSubStatus;
                d.category      = currentCategory;
                d.departemen_id = $('#filter_departemen').val();
            },
            dataSrc: function(json) {
                if (json.stats) updateStats(json.stats);
                return json.data;
            }
        },
        columns: [
            { 
                data: 'no_unit',
                className: 'ps-3 fw-bold',
                render: function(data, type, row) {
                    if (!data) return '<span class="badge badge-soft-gray">TEMP-' + row.id_inventory_unit + '</span>';
                    return `<a href="<?= base_url('warehouse/inventory/unit/') ?>${row.id_inventory_unit}" class="text-decoration-none text-success">${data}</a>`;
                }
            },
            { data: 'serial_number' },
            { 
                data: null,
                render: function(data, type, row) {
                    let brand = row.merk_unit || '-';
                    let model = row.model_unit || '-';
                    return `
                        <div class="d-flex flex-column">
                            <span class="fw-semibold text-dark">${brand}</span>
                            <span class="small text-muted">${model}</span>
                        </div>
                    `;
                }
            },
            { data: 'nama_departemen', render: data => data || '-' },
            { 
                data: 'status_unit_name',
                render: function(data, type, row) {
                    const label = data ? data : 'Unknown';
                    const id    = parseInt(row.status_unit_id, 10) || 0;
                    // Map by status_unit_id: 1=stock avail, 2=stock non-aset, 3=booked, 4-6=progress, 7/11=rental, 8=maintenance, 9=returned, 10=sold
                    const badgeMap = {
                        1:  'badge-soft-green',   // AVAILABLE_STOCK
                        2:  'badge-soft-yellow',  // STOCK_NON_ASET
                        3:  'badge-soft-blue',    // BOOKED
                        4:  'badge-soft-cyan',    // IN_PREPARATION
                        5:  'badge-soft-cyan',    // READY_TO_DELIVER
                        6:  'badge-soft-cyan',    // IN_DELIVERY
                        7:  'badge-soft-yellow',  // RENTAL_ACTIVE
                        8:  'badge-soft-red',     // MAINTENANCE
                        9:  'badge-soft-gray',    // RETURNED
                        10: 'badge-soft-gray',    // SOLD
                        11: 'badge-soft-gray',    // RENTAL_INACTIVE
                    };
                    const cls = badgeMap[id] || 'badge-soft-gray';
                    return `<span class="badge ${cls} rounded-pill px-3 py-1 fw-medium shadow-sm">${label}</span>`;
                }
            },
            { 
                data: 'lokasi_unit', 
                render: function(data, type, row) {
                    if (row.status_unit_id == 7 && data) {
                        return '<div class="text-success fw-bold"><i class="fas fa-building me-1"></i> Rented Area</div><small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>' + data + '</small>';
                    }
                    return '<div class="d-flex align-items-center"><i class="fas fa-warehouse text-muted me-2"></i> ' + (data || 'HQ Internal Warehouse') + '</div>';
                }
            },
            { 
                data: 'created_at',
                render: function(data) {
                    if(!data) return '-';
                    return moment(data).format('DD MMM YYYY');
                }
            },
            {
                data: null,
                orderable: false,
                className: 'text-end pe-3',
                render: function (data, type, row) {
                    return `
                        <a href="<?= base_url('warehouse/inventory/unit/') ?>${row.id_inventory_unit}" class="btn btn-sm btn-light text-primary hover-shadow" data-bs-toggle="tooltip" title="View Details">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    `;
                }
            }
        ],
        language: {
            processing: '<div class="text-primary"><i class="fas fa-spinner fa-spin fa-2x"></i><br>Loading units...</div>',
            emptyTable: 'No units found in the inventory.',
        },
        drawCallback: function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });

    // Custom Search
    $('#unitSearch').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Department Filter
    $('#filter_departemen').on('change', function() {
        table.draw();
    });

    // Tab Filters
    $('#unitStatusTabs button').on('click', function(e) {
        e.preventDefault();
        $('#unitStatusTabs button').removeClass('active');
        $(this).addClass('active');

        currentCategory = $(this).data('category');
        currentSubStatus = ''; // Reset substatus
        
        // Handle Sub-filters UI
        if (currentCategory) {
            $('#subFilterContainer').slideDown(200);
            $('.sub-filter-group').hide();
            $(`#${currentCategory}SubFilters`).show();
            // Automatically select 'All' in subfilter
            $(`#${currentCategory}SubFilters input[type=radio]`).first().prop('checked', true);
        } else {
            $('#subFilterContainer').slideUp(200);
        }

        table.draw();
    });

    // Sub Status Filters
    $('input[name="subFilter"]').on('change', function() {
        currentSubStatus = $(this).data('sub-status');
        table.draw();
    });

    function updateStats(stats) {
        if(!stats) return;
        $('#count-all').text(stats.total || 0);
        
        const stockCount = (stats.available_stock||0) + (stats.stock_non_aset||0) + (stats.returned||0);
        $('#count-stock').text(stockCount);

        const rentalCount = (stats.rental_active||0) + (stats.rental_inactive||0);
        $('#count-rental').text(rentalCount);

        const progressCount = (stats.in_preparation||0) + (stats.ready_to_deliver||0) + (stats.in_delivery||0) + (stats.maintenance||0);
        $('#count-progress').text(progressCount);
    }
});
</script>
<style>
.hover-shadow:hover {
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
    background-color: #fff!important;
}
.letter-spacing-1 {
    letter-spacing: 0.5px;
}
</style>
<?= $this->endSection() ?>
