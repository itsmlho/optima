<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<style>
    .card-stats:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); 
    }
    .table-card, .card-stats { 
        border: none; 
        border-radius: 15px; 
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); 
    }
    .work-order-badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
    }
    .priority-critical { background-color: #dc3545 !important; }
    .priority-high { background-color: #fd7e14 !important; }
    .priority-medium { background-color: #17a2b8 !important; }
    .priority-low { background-color: #6c757d !important; }
    .priority-routine { background-color: #28a745 !important; }
    .status-open { background-color: #17a2b8 !important; }
    .status-assigned { background-color: #007bff !important; }
    .status-in-progress { background-color: #ffc107 !important; color: #000 !important; }
    .status-completed { background-color: #28a745 !important; }
    .status-closed { background-color: #343a40 !important; }
    
    /* Clickable row styling */
    .clickable-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    .clickable-row:hover {
        background-color: rgba(0, 123, 255, 0.1) !important;
    }
    
    /* Dynamic action buttons styling */
    .btn-group-vertical .btn {
        margin-bottom: 2px;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    .btn-group-vertical .btn:last-child {
        margin-bottom: 0;
    }
    
    /* Custom Dropdown Styling */
    .dropdown-menu {
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .dropdown-item {
        padding: 0.5rem 1rem;
        transition: background-color 0.15s ease-in-out;
    }
    .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    .dropdown-item:active {
        background-color: #007bff;
        color: white;
    }
    .dropdown-toggle::after {
        float: right;
        margin-top: 8px;
    }
    
    /* Modal Footer Styling */
    .modal-footer {
        border-top: 1px solid #dee2e6;
        padding: 1rem 1.5rem;
        background-color: #f8f9fa;
    }
    
    .modal-footer .btn {
        min-width: 100px;
        font-weight: 500;
    }
    
    /* Work Order Modal Content */
    #workOrderModal .modal-body {
        max-height: 80vh;
        overflow-y: auto;
    }
    
    #workOrderModal .card {
        margin-bottom: 1.5rem;
    }
    
    #workOrderModal .card:last-child {
        margin-bottom: 0;
    }
    
    /* Modern Work Order Modal Styling */
    .modal-xl {
        max-width: 95%;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .info-grid {
        display: grid;
        gap: 1rem;
    }
    
    .info-item {
        padding: 0.75rem;
        background: rgba(248, 249, 250, 0.8);
        border-radius: 0.5rem;
        border-left: 4px solid #007bff;
        transition: all 0.2s ease;
    }
    
    .info-item:hover {
        background: rgba(248, 249, 250, 1);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .info-item small {
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .component-list {
        max-height: 200px;
        overflow-y: auto;
    }
    
    .component-item {
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
    }
    
    .component-item:hover {
        background: rgba(255, 255, 255, 1);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-color: #007bff;
    }
    
    .component-item:last-child {
        margin-bottom: 0;
    }
    
    .work-description {
        padding: 1rem;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        min-height: 100px;
        font-size: 0.9rem;
        line-height: 1.5;
    }
    
    .card-header {
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        font-weight: 600;
    }
    
    .shadow-sm {
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
    }
    
    /* Force hide modals on page load */
    .modal.show {
        display: none !important;
    }
    
    body.modal-open {
        overflow: auto !important;
        padding-right: 0 !important;
    }
    
    .modal-backdrop {
        display: none !important;
    }
    
    /* Component list styling */
    .component-list {
        max-height: 300px;
        overflow-y: auto;
    }
    
    .component-item {
        background: rgba(248, 249, 250, 0.8);
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 8px;
        transition: all 0.2s ease;
        position: relative;
    }
    
    .component-item:hover {
        background: rgba(255, 255, 255, 1);
        border-color: #007bff;
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
        transform: translateY(-1px);
    }
    
    .component-item:last-child {
        margin-bottom: 0;
    }
    
    .component-sn {
        font-family: 'Courier New', monospace;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    /* Unit info badges */
    .badge.fs-6 {
        font-size: 0.875rem !important;
        padding: 0.5rem 0.75rem;
    }
    
    /* Modal improvements */
    .modal-header {
        border-bottom: 2px solid #f8f9fa;
    }
    
    .modal-body hr {
        border-color: #e9ecef;
        opacity: 0.8;
    }
    
    /* Font monospace for serial numbers */
    .font-monospace {
        font-family: 'Courier New', monospace !important;
        font-weight: 600;
        color: #495057;
        background: rgba(233, 236, 239, 0.3);
        padding: 2px 6px;
        border-radius: 4px;
    }
    
    /* Search results styling */
    .list-group-item-action:hover {
        background-color: rgba(0, 123, 255, 0.1);
        cursor: pointer;
    }
    
    .list-group-item.unit-result:hover,
    .list-group-item.staff-result:hover {
        background-color: rgba(0, 123, 255, 0.1);
        border-color: #007bff;
    }
    
    /* Search input groups */
    .input-group .btn-outline-secondary {
        border-color: #ced4da;
    }
    
    .input-group .btn-outline-secondary:hover {
        background-color: #e9ecef;
        border-color: #adb5bd;
    }
    
    /* Modal improvements for search */
    .modal-body {
        position: relative;
    }
    
    .list-group {
        position: absolute;
        width: 100%;
        z-index: 1050;
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Alert Container -->
<div id="alertContainer" class="mb-3"></div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats bg-primary text-white h-100 filter-card" data-filter="all" style="cursor: pointer;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Total Work Orders</div>
                        <div class="h5 mb-0 font-weight-bold" id="stat-total-work-orders">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats bg-info text-white h-100 filter-card" data-filter="OPEN" style="cursor: pointer;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Open</div>
                        <div class="h5 mb-0 font-weight-bold" id="stat-open">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-folder-open fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats bg-warning text-white h-100 filter-card" data-filter="IN_PROGRESS" style="cursor: pointer;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">In Progress</div>
                        <div class="h5 mb-0 font-weight-bold" id="stat-in-progress">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-cogs fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats bg-success text-white h-100 filter-card" data-filter="COMPLETED" style="cursor: pointer;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Completed</div>
                        <div class="h5 mb-0 font-weight-bold" id="stat-completed">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card table-card mb-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i> Daftar Work Orders</h5>
            <button id="btn-add-wo" class="btn btn-success btn-sm"><i class="fas fa-plus me-1"></i> Tambah Work Order</button>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter Controls -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="filter-status">Status</label>
                    <select id="filter-status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <?php foreach ($statuses as $status): ?>
                        <option value="<?= $status['status_name'] ?>"><?= $status['status_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="filter-priority">Prioritas</label>
                    <select id="filter-priority" class="form-select form-select-sm">
                        <option value="">Semua Prioritas</option>
                        <?php foreach ($priorities as $priority): ?>
                        <option value="<?= $priority['priority_name'] ?>"><?= $priority['priority_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="filter-start-date">Tanggal Mulai</label>
                    <input type="date" id="filter-start-date" class="form-control form-control-sm">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="filter-end-date">Tanggal Akhir</label>
                    <input type="date" id="filter-end-date" class="form-control form-control-sm">
                </div>
            </div>
        </div>
        
        <!-- Table -->
        <div class="table-responsive">
            <table id="workOrdersTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nomor WO</th>
                        <th>Tanggal</th>
                        <th>Unit</th>
                        <th>Tipe</th>
                        <th>Prioritas</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded dynamically via DataTable -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modals Section -->

<!-- Modal Add/Edit Work Order -->
<div class="modal fade" id="workOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="workOrderFormTitle"><i class="fas fa-plus-circle me-2"></i>Tambah Work Order Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="workOrderForm" action="<?= base_url('work-orders/store') ?>" method="post" novalidate>
                    <input type="hidden" id="work_order_id" name="work_order_id">
                    
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Informasi Utama Work Order</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="work_order_number" class="form-label">Nomor Work Order</label>
                                    <input type="text" class="form-control" id="work_order_number" name="work_order_number" readonly>
                                    <div class="form-text">Nomor WO akan terisi otomatis (+1 dari WO terakhir)</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="order_type" class="form-label">Tipe Order <span class="text-danger">*</span></label>
                                    <select class="form-select" id="order_type" name="order_type" required>
                                        <option value="" selected disabled>-- Pilih Tipe Order --</option>
                                        <option value="COMPLAINT">Complaint</option>
                                        <option value="PMPS">PMPS</option>
                                        <option value="FABRIKASI">Fabrikasi</option>
                                        <option value="PERSIAPAN">Persiapan</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="unit_search" class="form-label">Unit <span class="text-danger">*</span></label>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle w-100 text-start" type="button" id="unitDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span id="unitSelectedText">-- Pilih Unit --</span>
                                        </button>
                                        <div class="dropdown-menu w-100 p-2" aria-labelledby="unitDropdownButton" style="max-height: 300px; overflow-y: auto;">
                                            <input type="text" class="form-control mb-2" id="unitSearch" placeholder="Search units..." onkeyup="filterUnits()">
                                            <div id="unitDropdownList">
                                                <!-- Units will be loaded here -->
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="unit_id" name="unit_id" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="" selected disabled>-- Pilih Kategori --</option>
                                        <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" data-priority="<?= $category['default_priority_id'] ?? '' ?>"><?= $category['category_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="subcategory_id" class="form-label">Sub Kategori</label>
                                    <select class="form-select" id="subcategory_id" name="subcategory_id">
                                        <option value="">-- Pilih Sub Kategori (jika ada) --</option>
                                    </select>
                                    <div class="form-text">Sub kategori akan muncul setelah memilih kategori</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="priority_display" class="form-label">Prioritas</label>
                                    <input type="text" class="form-control" id="priority_display" readonly placeholder="Otomatis berdasarkan kategori">
                                    <input type="hidden" id="priority_id" name="priority_id">
                                    <div class="form-text">Prioritas otomatis berdasarkan kategori & sub kategori</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="area" class="form-label">Area</label>
                                    <input type="text" class="form-control" id="area" name="area" placeholder="Lokasi/area kerja">
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="complaint_description" class="form-label">Deskripsi Keluhan <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="complaint_description" name="complaint_description" rows="3" placeholder="Jelaskan keluhan atau permintaan pekerjaan secara detail..." required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-users-cog me-2"></i>Penugasan Staff</h6>
                        </div>
                        <div class="card-body">
                            <!-- First Row: Admin and Foreman -->
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label for="admin_staff_dropdown" class="form-label">Admin</label>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle w-100 text-start" type="button" id="adminDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span id="adminSelectedText">-- Pilih Admin --</span>
                                        </button>
                                        <div class="dropdown-menu w-100 p-2" aria-labelledby="adminDropdownButton" style="max-height: 300px; overflow-y: auto;">
                                            <input type="text" class="form-control mb-2" id="adminSearch" placeholder="Search admin..." onkeyup="filterStaff('admin')">
                                            <div id="adminDropdownList">
                                                <!-- Admin staff will be loaded here -->
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="admin_staff_id" name="admin_staff_id">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="foreman_staff_dropdown" class="form-label">Foreman</label>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle w-100 text-start" type="button" id="foremanDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span id="foremanSelectedText">-- Pilih Foreman --</span>
                                        </button>
                                        <div class="dropdown-menu w-100 p-2" aria-labelledby="foremanDropdownButton" style="max-height: 300px; overflow-y: auto;">
                                            <input type="text" class="form-control mb-2" id="foremanSearch" placeholder="Search foreman..." onkeyup="filterStaff('foreman')">
                                            <div id="foremanDropdownList">
                                                <!-- Foreman staff will be loaded here -->
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="foreman_staff_id" name="foreman_staff_id">
                                </div>
                            </div>
                            
                            <!-- Second Row: Mechanic and Helper -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="mechanic_staff_dropdown" class="form-label">Mekanik</label>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle w-100 text-start" type="button" id="mechanicDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span id="mechanicSelectedText">-- Pilih Mekanik --</span>
                                        </button>
                                        <div class="dropdown-menu w-100 p-2" aria-labelledby="mechanicDropdownButton" style="max-height: 300px; overflow-y: auto;">
                                            <input type="text" class="form-control mb-2" id="mechanicSearch" placeholder="Search mechanic..." onkeyup="filterStaff('mechanic')">
                                            <div id="mechanicDropdownList">
                                                <!-- Mechanic staff will be loaded here -->
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="mechanic_staff_id" name="mechanic_staff_id">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="helper_staff_dropdown" class="form-label">Helper</label>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle w-100 text-start" type="button" id="helperDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span id="helperSelectedText">-- Pilih Helper --</span>
                                        </button>
                                        <div class="dropdown-menu w-100 p-2" aria-labelledby="helperDropdownButton" style="max-height: 300px; overflow-y: auto;">
                                            <input type="text" class="form-control mb-2" id="helperSearch" placeholder="Search helper..." onkeyup="filterStaff('helper')">
                                            <div id="helperDropdownList">
                                                <!-- Helper staff will be loaded here -->
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="helper_staff_id" name="helper_staff_id">
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer d-flex justify-content-between flex-wrap">
                <div class="d-flex align-items-center">
                    <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Fields dengan tanda <span class="text-danger">*</span> wajib diisi</small>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="event.stopPropagation();">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" form="workOrderForm" id="btnSubmitWo" onclick="event.stopPropagation();">
                        <i class="fas fa-save me-1"></i> Simpan Work Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal View Work Order -->
<div class="modal fade" id="viewWorkOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-alt text-primary me-2"></i>
                    Detail Work Order: <span id="viewWoNumberHeader" class="fw-bold">-</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-body-tertiary p-4">
                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="mb-4">
                                    <h6 class="mb-3 text-dark">Informasi Pekerjaan</h6>
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4 text-muted">Tanggal Lapor</dt>
                                        <dd class="col-sm-8" id="viewWoReportDate">-</dd>
                                        <dt class="col-sm-4 text-muted">Tipe Order</dt>
                                        <dd class="col-sm-8" id="viewWoType">-</dd>
                                        <dt class="col-sm-4 text-muted">Kategori</dt>
                                        <dd class="col-sm-8" id="viewWoCategory">-</dd>
                                    </dl>
                                </div>
                                <hr>
                                <div>
                                    <h6 class="mb-3 text-dark">Detail Unit & Komponen</h6>
                                     <dl class="row mb-0">
                                        <dt class="col-sm-4 text-muted">Nomor Unit</dt>
                                        <dd class="col-sm-8 fw-bold text-primary" id="viewUnitNumber">-</dd>
                                        <dt class="col-sm-4 text-muted">Merk & Model</dt>
                                        <dd class="col-sm-8" id="viewUnitModel">-</dd>
                                        <dt class="col-sm-4 text-muted">Tipe Unit</dt>
                                        <dd class="col-sm-8" id="viewUnitType">-</dd>
                                        <dt class="col-sm-4 text-muted">Serial Number</dt>
                                        <dd class="col-sm-8 font-monospace" id="viewUnitSerial">-</dd>
                                        <dt class="col-sm-4 text-muted">Departemen</dt>
                                        <dd class="col-sm-8"><span class="badge bg-info-subtle text-info-emphasis border border-info-subtle" id="viewUnitDepartemen">-</span></dd>
                                        <dt class="col-sm-4 text-muted">Status Unit</dt>
                                        <dd class="col-sm-8"><span class="badge bg-success-subtle text-success-emphasis border border-success-subtle" id="viewUnitStatus">-</span></dd>

                                        <div id="unitComponentsSection" class="contents" style="display: none;">
                                            <dt class="col-sm-4 text-muted pt-2">Attachment</dt>
                                            <dd class="col-sm-8 pt-2" id="viewUnitAttachmentList">-</dd>

                                            <dt id="batteryLabel" class="col-sm-4 text-muted" style="display: none;">Battery</dt>
                                            <dd id="batteryValue" class="col-sm-8" style="display: none;">
                                                <span id="viewUnitBatteryList">-</span>
                                            </dd>
                                            
                                            <dt id="chargerLabel" class="col-sm-4 text-muted" style="display: none;">Charger</dt>
                                            <dd id="chargerValue" class="col-sm-8" style="display: none;">
                                                <span id="viewUnitChargerList">-</span>
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-5">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="text-center p-3 rounded bg-body-secondary mb-4">
                                    <div class="row">
                                        <div class="col-6 border-end">
                                            <label class="small text-muted mb-1">Status</label>
                                            <div><span class="badge fs-6" id="viewWoStatus">-</span></div>
                                        </div>
                                        <div class="col-6">
                                            <label class="small text-muted mb-1">Prioritas</label>
                                            <div><span class="badge fs-6" id="viewWoPriority">-</span></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <h6 class="mb-3 text-dark">Pelanggan & Lokasi</h6>
                                <dl class="row mb-4">
                                    <dt class="col-sm-4 text-muted">Pelanggan</dt>
                                    <dd class="col-sm-8 fw-bold" id="viewUnitCustomer">-</dd>
                                    <dt class="col-sm-4 text-muted">Lokasi</dt>
                                    <dd class="col-sm-8" id="viewUnitLocation">-</dd>
                                    <dt class="col-sm-4 text-muted">Area</dt>
                                    <dd class="col-sm-8" id="viewWoArea">-</dd>
                                </dl>
                                
                                <h6 class="mb-3 text-dark">Penugasan Staff</h6>
                                <ul class="list-unstyled mb-4">
                                    <li class="d-flex align-items-center mb-2"><i class="fas fa-user-shield fa-fw me-2 text-muted"></i> <strong>Admin:</strong> <span class="ms-auto" id="viewWoAdmin">-</span></li>
                                    <li class="d-flex align-items-center mb-2"><i class="fas fa-user-tie fa-fw me-2 text-muted"></i> <strong>Foreman:</strong> <span class="ms-auto" id="viewWoForeman">-</span></li>
                                    <li class="d-flex align-items-center mb-2"><i class="fas fa-user-cog fa-fw me-2 text-muted"></i> <strong>Mekanik:</strong> <span class="ms-auto" id="viewWoMechanic">-</span></li>
                                    <li class="d-flex align-items-center"><i class="fas fa-user-friends fa-fw me-2 text-muted"></i> <strong>Helper:</strong> <span class="ms-auto" id="viewWoHelper">-</span></li>
                                </ul>

                                <h6 class="mb-3 text-dark">Waktu & Tanggal</h6>
                                <dl class="row mb-0">
                                    <dt class="col-sm-4 text-muted">TTR</dt>
                                    <dd class="col-sm-8 fw-bold text-primary" id="viewWoTTR">-</dd>
                                    <dt class="col-sm-4 text-muted">Tgl. Selesai</dt>
                                    <dd class="col-sm-8" id="viewWoCompletionDate">Belum selesai</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="unitAccessoriesSection" class="card mt-4" style="display: none;">
                    <div class="card-header">
                         <h6 class="mb-0"><i class="fas fa-puzzle-piece me-2"></i>Aksesoris Unit</h6>
                    </div>
                    <div class="card-body">
                        <div id="viewUnitAccessoriesList" class="component-list">
                            </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-pencil-alt me-2"></i>Detail Pekerjaan & Catatan</h6>
                    </div>
                    <div class="card-body">
                         <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Keluhan Pelanggan</label>
                                <div class="p-3 rounded bg-light" style="min-height: 120px;" id="viewWoComplaint"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Analisa & Perbaikan</label>
                                <div class="p-3 rounded bg-light" style="min-height: 120px;" id="viewWoRepair"></div>
                            </div>
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label text-muted">Sparepart Digunakan</label>
                                <div class="p-3 rounded bg-light" style="min-height: 80px;" id="viewWoSparepart"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Catatan Tambahan</label>
                                <div class="p-3 rounded bg-light" style="min-height: 80px;" id="viewWoNotes"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-edit-from-view" data-id="" id="btnEditFromView">
                    <i class="fas fa-edit me-1"></i>Edit Work Order
                </button>
                <button type="button" class="btn btn-danger btn-delete-from-view" data-id="" data-wo-number="">
                    <i class="fas fa-trash me-1"></i>Hapus
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
$(document).ready(function() {
    // Force close all modals on page load with multiple methods
    setTimeout(function() {
        // Method 1: jQuery
        $('.modal').modal('hide');
        $('#workOrderModal').modal('hide');
        
        // Method 2: Bootstrap native
        if (window.bootstrap) {
            document.querySelectorAll('.modal').forEach(function(modal) {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) {
                    bsModal.hide();
                }
            });
        }
        
        // Method 3: Force DOM cleanup
        $('.modal').removeClass('show').hide();
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        
        // Method 4: Reset modal attributes
        $('.modal').attr('aria-hidden', 'true').css('display', 'none');
        
        console.log('All modals forcefully closed');
        
        // Remove force hide CSS after cleanup
        setTimeout(function() {
            const style = document.createElement('style');
            style.innerHTML = `
                .modal.show { display: block !important; }
                body.modal-open { overflow: hidden !important; }
                .modal-backdrop { display: block !important; }
            `;
            document.head.appendChild(style);
            console.log('Modal CSS reset - modals can now work normally');
        }, 500);
        
    }, 100);
    
    // Initialize DataTable
    let table = $('#workOrdersTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 25,
        ajax: {
            url: '<?= base_url('work-orders/data') ?>',
            type: 'POST',
            error: function(xhr, error, thrown) {
                console.log('Error loading data:', xhr.responseText);
            }
        },
        columns: [
            { data: 0, orderable: false, searchable: false }, // Row number
            { data: 1 }, // work_order_number
            { data: 2 }, // report_date
            { data: 3 }, // unit_info
            { data: 4 }, // order_type
            { data: 5 }, // priority_badge
            { data: 6 }, // category
            { data: 7 }, // status_badge
            { data: 8, orderable: false, searchable: false } // action
        ],
        order: [[2, 'desc']], // Order by report_date descending
        language: {
            "sProcessing": "Sedang memproses...",
            "sLengthMenu": "Tampilkan _MENU_ data",
            "sZeroRecords": "Tidak ditemukan data yang sesuai",
            "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
            "sInfoFiltered": "(disaring dari _MAX_ total data)",
            "sInfoPostFix": "",
            "sSearch": "Cari:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "Pertama",
                "sPrevious": "Sebelumnya",
                "sNext": "Selanjutnya",
                "sLast": "Terakhir"
            }
        },
        createdRow: function(row, data, dataIndex) {
            // Add click event to row (except action column)
            $(row).addClass('clickable-row');
            // DataTable automatically applies DT_RowAttr to the row, so we can access them directly
            let woId = $(row).attr('data-wo-id');
            let woNumber = $(row).attr('data-wo-number');
            let statusCode = $(row).attr('data-status-code');
            
            // Set additional data for easy access
            $(row).data('wo-id', woId);
            $(row).data('wo-number', woNumber);
            $(row).data('status-code', statusCode);
        }
    });
    
    // Row click event to show detail modal (except when clicking action buttons)
    $('#workOrdersTable tbody').on('click', 'tr.clickable-row', function(e) {
        // Don't trigger if clicking on action buttons
        if ($(e.target).closest('.btn-group-vertical').length > 0) {
            return;
        }
        
        let woId = $(this).data('wo-id');
        let woNumber = $(this).data('wo-number');
        
        if (woId) {
            showWorkOrderDetail(woId, woNumber);
        }
    });

    // Reset form when modal is hidden
    $('#workOrderModal').on('hidden.bs.modal', function() {
        // Reset form inputs
        $('#workOrderForm')[0].reset();
        
        // Reset modal title and action
        $('#workOrderFormTitle').text('Tambah Work Order Baru');
        $('#workOrderForm').attr('action', '<?= base_url('work-orders/store') ?>');
        $('#btnSubmitWo').html('<i class="fas fa-save me-1"></i> Simpan Work Order');
        
        // Reset custom dropdowns
        resetCustomDropdowns();
        
        // Clear form errors
        clearFormErrors();
        
        // Clear hidden work order ID
        $('#work_order_id').val('');
    });
    
    // Function to reset custom dropdowns
    function resetCustomDropdowns() {
        // Reset Unit dropdown
        $('#unitSelectedText').text('-- Pilih Unit --');
        $('#unit_id').val('');
        $('#unitDropdownList').empty();
        
        // Reset Staff dropdowns
        const staffTypes = ['admin', 'foreman', 'mechanic', 'helper'];
        staffTypes.forEach(function(type) {
            $(`#${type}SelectedText`).text(`-- Pilih ${type.charAt(0).toUpperCase() + type.slice(1)} --`);
            $(`#${type}_staff_id`).val('');
            $(`#${type}DropdownList`).empty();
            $(`#${type}Search`).val('');
        });
        
        // Reset subcategory dropdown
        $('#subcategory_id').empty().append('<option value="">-- Pilih Sub Kategori (jika ada) --</option>');
        
        // Reset priority display
        $('#priority_display').val('');
        $('#priority_id').val('');
        
        // Clear work order number (will be auto-generated)
        $('#work_order_number').val('');
    }

    // Submit Work Order Form
    $('#workOrderForm').on('submit', function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        let url = $(this).attr('action');
        
        console.log('📤 Submitting work order form to:', url);
        console.log('📤 Form data prepared');
        
        // Debug: Log form data
        console.log('📤 Form data contents:');
        for (let pair of formData.entries()) {
            console.log('  ' + pair[0] + ': "' + pair[1] + '"');
        }
        
        // Clear previous errors
        clearFormErrors();
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#btnSubmitWo').prop('disabled', true).text('Menyimpan...');
                console.log('📤 Sending request...');
            },
            success: function(response) {
                console.log('✅ Success response:', response);
                if (response.success) {
                    showAlert('success', response.message);
                    console.log('✅ Showing success notification:', response.message);
                    $('#workOrderModal').modal('hide');
                    table.ajax.reload();
                    updateStatistics();
                } else {
                    console.log('❌ Server returned success=false:', response.message);
                    showAlert('error', response.message);
                    console.log('❌ Showing error notification:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log('❌ AJAX Error:', error);
                console.log('❌ Status:', status);
                console.log('❌ Response:', xhr.responseText);
                
                try {
                    let response = JSON.parse(xhr.responseText);
                    if (response.errors) {
                        displayFormErrors(response.errors);
                    } else {
                        showAlert('error', response.message || 'Terjadi kesalahan saat menyimpan data');
                    }
                } catch (e) {
                    console.log('❌ Could not parse error response');
                    showAlert('error', 'Terjadi kesalahan saat menyimpan data');
                }
            },
            complete: function() {
                $('#btnSubmitWo').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan Work Order');
                console.log('📤 Request completed');
            }
        });
    });

    // Show Work Order Detail function
    function showWorkOrderDetail(id, woNumber) {
        $.ajax({
            url: '<?= base_url('work-orders/view') ?>/' + id,
            type: 'GET',
            beforeSend: function() {
                showAlert('info', 'Memuat detail...');
            },
            success: function(response) {
                if (response.success) {
                    hideAlert();
                    populateViewModal(response.data);
                    $('#viewWorkOrderModal').modal('show');
                } else {
                    showAlert('error', response.message || 'Gagal memuat data');
                }
            },
            error: function(xhr, status, error) {
                hideAlert();
                showAlert('error', 'Terjadi kesalahan saat memuat data: ' + error);
            }
        });
    }

    // Status Action Buttons Event Handlers
    
    // Assign Work Order
    $(document).on('click', '.btn-assign', function() {
        let id = $(this).data('id');
        showStatusUpdateModal(id, 'ASSIGNED', 'Assign Work Order', 'Pilih teknisi untuk work order ini');
    });
    
    // Start Work
    $(document).on('click', '.btn-start', function() {
        let id = $(this).data('id');
        updateWorkOrderStatus(id, 'IN_PROGRESS', 'Work order dimulai');
    });
    
    // Pause Work
    $(document).on('click', '.btn-pause', function() {
        let id = $(this).data('id');
        showStatusUpdateModal(id, 'ON_HOLD', 'Pause Work Order', 'Berikan alasan pause');
    });
    
    // Resume Work
    $(document).on('click', '.btn-resume', function() {
        let id = $(this).data('id');
        updateWorkOrderStatus(id, 'IN_PROGRESS', 'Work order dilanjutkan');
    });
    
    // Complete Work
    $(document).on('click', '.btn-complete', function() {
        let id = $(this).data('id');
        showStatusUpdateModal(id, 'COMPLETED', 'Complete Work Order', 'Berikan catatan penyelesaian');
    });
    
    // Close Work Order
    $(document).on('click', '.btn-close-wo', function() {
        let id = $(this).data('id');
        updateWorkOrderStatus(id, 'CLOSED', 'Work order ditutup');
    });
    
    // Reopen Work Order
    $(document).on('click', '.btn-reopen', function() {
        let id = $(this).data('id');
        updateWorkOrderStatus(id, 'PENDING', 'Work order dibuka kembali');
    });
    
    // Cancel Work Order
    $(document).on('click', '.btn-cancel', function() {
        let id = $(this).data('id');
        showStatusUpdateModal(id, 'CANCELLED', 'Cancel Work Order', 'Berikan alasan pembatalan');
    });
    
    // Reassign Work Order
    $(document).on('click', '.btn-reassign', function() {
        let id = $(this).data('id');
        showStatusUpdateModal(id, 'ASSIGNED', 'Reassign Work Order', 'Pilih teknisi baru');
    });

    // Function to update work order status
    function updateWorkOrderStatus(id, status, message) {
        console.log('🚨 updateWorkOrderStatus called with:', { id, status, message, stack: new Error().stack });
        
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin mengubah status work order?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('work-orders/update-status') ?>',
                    type: 'POST',
                    data: {
                        id: id,
                        status: status,
                        notes: message
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert('success', response.message);
                            table.ajax.reload();
                            updateStatistics();
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function() {
                        showAlert('error', 'Gagal mengubah status work order');
                    }
                });
            }
        });
    }
    
    // Function to show status update modal with notes
    function showStatusUpdateModal(id, status, title, placeholder) {
        Swal.fire({
            title: title,
            input: 'textarea',
            inputPlaceholder: placeholder,
            showCancelButton: true,
            confirmButtonText: 'Update',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (!value && (status === 'CANCELLED' || status === 'ON_HOLD')) {
                    return 'Catatan wajib diisi untuk status ini'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('work-orders/update-status') ?>',
                    type: 'POST',
                    data: {
                        id: id,
                        status: status,
                        notes: result.value || ''
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert('success', response.message);
                            table.ajax.reload();
                            updateStatistics();
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function() {
                        showAlert('error', 'Gagal mengubah status work order');
                    }
                });
            }
        });
    }

    // Edit from view modal
    $(document).on('click', '.btn-edit-from-view', function() {
        let id = $(this).data('id');
        
        // Close view modal first
        $('#viewWorkOrderModal').modal('hide');
        
        // Load work order data for editing
        $.ajax({
            url: '<?= base_url('work-orders/edit') ?>/' + id,
            type: 'GET',
            beforeSend: function() {
                // Loading work order data for edit
            },
            success: function(response) {
                if (response.success) {
                    // Wait for view modal to close then open edit modal
                    setTimeout(function() {
                        // Setup modal for editing
                        $('#workOrderFormTitle').html('<i class="fas fa-edit me-2"></i>Edit Work Order');
                        $('#workOrderForm').attr('action', '<?= base_url('work-orders/update') ?>/' + id);
                        $('#btnSubmitWo').html('<i class="fas fa-save me-1"></i> Update Work Order');
                        $('#work_order_id').val(id);
                        
                        // Open modal first to trigger dropdown loading
                        $('#workOrderModal').modal('show');
                        
                        // Wait for modal to be shown and dropdowns loaded, then populate
                        setTimeout(function() {
                            populateEditForm(response.data);
                        }, 1000); // Give dropdowns time to load
                        
                    }, 300);
                } else {
                    Swal.fire('Error', response.message || 'Gagal memuat data work order', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Gagal memuat data work order', 'error');
            }
        });
    });
    
    // Delete from view modal
    $(document).on('click', '.btn-delete-from-view', function(e) {
        e.preventDefault();
        console.log('🚨 Delete button clicked in view modal');
        
        let id = $(this).data('id');
        let woNumber = $(this).data('wo-number');
        
        console.log('🗑️ Delete request for WO:', woNumber, 'ID:', id);
        
        $('#viewWorkOrderModal').modal('hide');
        
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: `Apakah Anda yakin ingin menghapus Work Order ${woNumber}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                console.log('🗑️ Confirmed deletion, sending request...');
                $.ajax({
                    url: '<?= base_url('work-orders/delete') ?>/' + id,
                    type: 'DELETE',
                    beforeSend: function() {
                        console.log('🗑️ Sending delete request to:', '<?= base_url('work-orders/delete') ?>/' + id);
                    },
                    success: function(response) {
                        console.log('✅ Delete response:', response);
                        if (response.success) {
                            showAlert('success', response.message);
                            table.ajax.reload();
                            updateStatistics();
                        } else {
                            console.log('❌ Delete failed:', response.message);
                            showAlert('error', response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('❌ Delete error:', error);
                        console.log('❌ Delete response:', xhr.responseText);
                        showAlert('error', 'Gagal menghapus work order');
                    }
                });
            } else {
                console.log('🗑️ Delete cancelled by user');
            }
        });
    });

    // Delete from DataTable action buttons
    $(document).on('click', '.btn-delete-wo', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Prevent row click event
        
        let id = $(this).data('id');
        let $row = $(this).closest('tr');
        let woNumber = $row.find('td:nth-child(2)').text(); // Get WO number from table row
        
        console.log('🗑️ Delete request from table for WO:', woNumber, 'ID:', id);
        
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: `Apakah Anda yakin ingin menghapus Work Order ${woNumber}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                console.log('🗑️ Confirmed deletion from table, sending request...');
                $.ajax({
                    url: '<?= base_url('work-orders/delete') ?>/' + id,
                    type: 'DELETE',
                    beforeSend: function() {
                        console.log('🗑️ Sending delete request to:', '<?= base_url('work-orders/delete') ?>/' + id);
                    },
                    success: function(response) {
                        console.log('✅ Delete response:', response);
                        if (response.success) {
                            showAlert('success', response.message);
                            table.ajax.reload();
                            updateStatistics();
                        } else {
                            console.log('❌ Delete failed:', response.message);
                            showAlert('error', response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('❌ Delete error:', error);
                        console.log('❌ Delete response:', xhr.responseText);
                        try {
                            let response = JSON.parse(xhr.responseText);
                            showAlert('error', response.message || 'Gagal menghapus work order');
                        } catch (e) {
                            showAlert('error', 'Gagal menghapus work order');
                        }
                    }
                });
            } else {
                console.log('🗑️ Delete cancelled by user');
            }
        });
    });

    // Category change handler for subcategory
    $('#category_id').on('change', function() {
        let categoryId = $(this).val();
        let subcategorySelect = $('#subcategory_id');
        
        subcategorySelect.empty().append('<option value="">Pilih Sub Kategori</option>');
        
        if (categoryId) {
            $.ajax({
                url: '<?= base_url('work-orders/get-subcategories') ?>',
                type: 'POST',
                data: { category_id: categoryId },
                success: function(response) {
                    if (response.success) {
                        $.each(response.data, function(index, subcategory) {
                            subcategorySelect.append(`<option value="${subcategory.id}">${subcategory.subcategory_name}</option>`);
                        });
                    }
                }
            });
        }
    });

    // Helper functions
    function populateEditForm(data) {
        console.log('Populating edit form with data...');
        
        // Extract work order data from nested structure
        let workOrder = data.workOrder || data;
        
        // Basic form fields
        $('#work_order_id').val(workOrder.id);
        $('#work_order_number').val(workOrder.work_order_number);
        $('#order_type').val(workOrder.order_type);
        $('#category_id').val(workOrder.category_id);
        $('#area').val(workOrder.area);
        $('#complaint_description').val(workOrder.complaint_description);
        
        // Handle Unit selection - use loaded dropdown data or make AJAX call
        if (workOrder.unit_id) {
            $('#unit_id').val(workOrder.unit_id);
            
            // First try to find unit in already loaded data
            if (window.allUnits && window.allUnits.length > 0) {
                let unit = window.allUnits.find(u => u.id == workOrder.unit_id);
                if (unit) {
                    let displayName = unit.no_unit + ' - ' + (unit.pelanggan || 'Unknown') + ' (' + (unit.merk_unit + ' ' + unit.model_unit || unit.unit_type || 'Unknown') + ')';
                    $('#unitSelectedText').text(displayName);
                    console.log('✅ Unit populated:', displayName);
                } else {
                    // Fallback: Unit not found in loaded data, make AJAX call
                    $.ajax({
                        url: '<?= base_url('work-orders/units-dropdown') ?>',
                        type: 'GET',
                        success: function(response) {
                            if (response.success) {
                                let unit = response.data.find(u => u.id == workOrder.unit_id);
                                if (unit) {
                                    let displayName = unit.no_unit + ' - ' + (unit.pelanggan || 'Unknown') + ' (' + (unit.merk_unit + ' ' + unit.model_unit || unit.unit_type || 'Unknown') + ')';
                                    $('#unitSelectedText').text(displayName);
                                    console.log('✅ Unit populated (via AJAX):', displayName);
                                } else {
                                    console.log('❌ Unit not found for ID:', workOrder.unit_id);
                                }
                            }
                        },
                        error: function() {
                            console.log('❌ Failed to load unit details');
                        }
                    });
                }
            } else {
                // No units loaded yet, make AJAX call
                $.ajax({
                    url: '<?= base_url('work-orders/units-dropdown') ?>',
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            let unit = response.data.find(u => u.id == workOrder.unit_id);
                            if (unit) {
                                let displayName = unit.no_unit + ' - ' + (unit.pelanggan || 'Unknown') + ' (' + (unit.merk_unit + ' ' + unit.model_unit || unit.unit_type || 'Unknown') + ')';
                                $('#unitSelectedText').text(displayName);
                                console.log('✅ Unit populated (fresh AJAX):', displayName);
                            }
                        }
                    }
                });
            }
        }
        
        // Handle Staff selections using the backend response data
        const staffMappings = [
            { type: 'admin', dataKey: 'adminStaff', fieldId: 'admin_staff_id', textId: 'adminSelectedText' },
            { type: 'foreman', dataKey: 'foremanStaff', fieldId: 'foreman_staff_id', textId: 'foremanSelectedText' },
            { type: 'mechanic', dataKey: 'mechanicStaff', fieldId: 'mechanic_staff_id', textId: 'mechanicSelectedText' },
            { type: 'helper', dataKey: 'helperStaff', fieldId: 'helper_staff_id', textId: 'helperSelectedText' }
        ];
        
        staffMappings.forEach(function(mapping) {
            let staffId = workOrder[mapping.fieldId];
            
            if (staffId && data[mapping.dataKey]) {
                $(`#${mapping.fieldId}`).val(staffId);
                
                // Find staff in the backend response
                let staff = data[mapping.dataKey].find(s => s.id == staffId);
                
                if (staff) {
                    // Try different possible property names for staff name
                    let staffName = staff.staff_name || staff.name || staff.full_name || staff.username || `Staff ${staffId}`;
                    $(`#${mapping.textId}`).text(staffName);
                    console.log(`✅ ${mapping.type} staff: ${staffName}`);
                }
            }
        });
        
        // Handle Priority
        if (workOrder.priority_id && data.priorities) {
            $('#priority_id').val(workOrder.priority_id);
            let priority = data.priorities.find(p => p.id == workOrder.priority_id);
            if (priority) {
                $('#priority_display').val(priority.priority_name);
                console.log('✅ Priority:', priority.priority_name);
            }
        }
        
        // Load subcategories if category is selected
        if (workOrder.category_id && data.subcategories) {
            let subcategorySelect = $('#subcategory_id');
            subcategorySelect.empty().append('<option value="">-- Pilih Sub Kategori (jika ada) --</option>');
            
            if (data.subcategories && data.subcategories.length > 0) {
                data.subcategories.forEach(function(subcategory) {
                    let selected = subcategory.id == workOrder.subcategory_id ? 'selected' : '';
                    subcategorySelect.append(`<option value="${subcategory.id}" ${selected}>${subcategory.subcategory_name}</option>`);
                });
                console.log('✅ Subcategories populated, selected:', workOrder.subcategory_id);
            }
        }
        
        console.log('✅ Edit form populated successfully');
    }

    function populateViewModal(data) {
        // Debug: Log the data structure to understand what we're receiving
        console.log('Work Order Detail Data:', data);
        console.log('Accessories Data:', data.unit_accessories || data.accessories);
        
        // Update modal header with work order number
        $('#viewWoNumberHeader').text(data.work_order_number || '-');
        
        // Work Order Information
        $('#viewWoNumber').text(data.work_order_number || '-');
        $('#viewWoReportDate').text(data.report_date || '-');
        $('#viewWoType').text(data.order_type || '-');
        $('#viewWoPriority').html(data.priority_badge || '<span class="badge bg-secondary">-</span>');
        $('#viewWoCategory').text(data.category_name || '-');
        $('#viewWoStatus').html(data.status_badge || '<span class="badge bg-secondary">-</span>');
        $('#viewWoArea').text(data.area || '-');
        $('#viewWoTTR').text(data.time_to_repair ? data.time_to_repair + ' jam' : '-');
        $('#viewWoCompletionDate').text(data.completion_date || 'Belum selesai');
        
        // Unit Details  
        $('#viewUnitNumber').text(data.unit_number || '-');
        $('#viewUnitModel').text((data.unit_brand && data.model_unit) ? data.unit_brand + ' ' + data.model_unit : '-');
        $('#viewUnitType').text(data.unit_type || '-');
        $('#viewUnitDepartemen').html(data.unit_departemen ? `<span class="badge bg-info">${data.unit_departemen}</span>` : '<span class="badge bg-secondary">-</span>');
        $('#viewUnitSerial').text(data.unit_serial || '-');
        $('#viewUnitLocation').text(data.unit_location || '-');
        $('#viewUnitCustomer').text(data.unit_customer || '-');
        $('#viewUnitStatus').html(data.unit_status ? `<span class="badge bg-success">${data.unit_status}</span>` : '<span class="badge bg-secondary">-</span>');
        
        // Handle Unit Components
        populateUnitComponents(data);
        
        // Handle Unit Accessories
        populateUnitAccessories(data.unit_accessories || data.accessories || []);
        
        // Staff Assignment
        $('#viewWoAdmin').text(data.admin_staff_name || '-');
        $('#viewWoForeman').text(data.foreman_staff_name || '-');
        $('#viewWoMechanic').text(data.mechanic_staff_name || '-');
        $('#viewWoHelper').text(data.helper_staff_name || '-');
        
        // Descriptions and Details  
        $('#viewWoComplaint').html(data.complaint_description ? 
            `<div class="text-dark">${data.complaint_description}</div>` : 
            '<div class="text-muted fst-italic">Tidak ada deskripsi keluhan</div>');
            
        $('#viewWoRepair').html(data.repair_description ? 
            `<div class="text-dark">${data.repair_description}</div>` : 
            '<div class="text-muted fst-italic">Belum ada perbaikan</div>');
            
        $('#viewWoSparepart').html(data.sparepart_used ? 
            `<div class="text-dark">${data.sparepart_used}</div>` : 
            '<div class="text-muted fst-italic">Tidak ada sparepart yang digunakan</div>');
            
        $('#viewWoNotes').html(data.notes ? 
            `<div class="text-dark">${data.notes}</div>` : 
            '<div class="text-muted fst-italic">Tidak ada catatan</div>');
        
        // Set data attributes for buttons
        $('.btn-edit-from-view').data('id', data.id);
        $('.btn-delete-from-view').data('id', data.id).data('wo-number', data.work_order_number);
    }

    function populateUnitComponents(data) {
        // Always populate attachments
        populateUnitAttachments(data.unit_attachments || []);
        
        // Show/hide unit components section
        let hasComponents = false;
        
        // Check if we have any attachments
        if (data.unit_attachments && data.unit_attachments.length > 0) {
            hasComponents = true;
        }
        
        // Handle ELECTRIC department components
        if (data.unit_departemen === 'ELECTRIC') {
            // Show battery section and populate
            $('#batteryLabel, #batteryValue').show();
            populateUnitBatteries(data.unit_batteries || []);
            
            // Show charger section and populate
            $('#chargerLabel, #chargerValue').show();
            populateUnitChargers(data.unit_chargers || []);
            
            // Check if we have electric components
            if ((data.unit_batteries && data.unit_batteries.length > 0) || 
                (data.unit_chargers && data.unit_chargers.length > 0)) {
                hasComponents = true;
            }
        } else {
            // Hide electric components for non-electric units
            $('#batteryLabel, #batteryValue').hide();
            $('#chargerLabel, #chargerValue').hide();
        }
        
        // Show/hide the entire components section
        if (hasComponents) {
            $('#unitComponentsSection').show();
        } else {
            $('#unitComponentsSection').hide();
        }
    }
    
    function populateUnitAttachments(attachments) {
        const container = $('#viewUnitAttachmentList');
        
        if (attachments && attachments.length > 0) {
            let textList = [];
            attachments.forEach(function(attachment, index) {
                let text = `${index + 1}. ${attachment.tipe || 'Attachment'} - ${attachment.merk || 'Unknown'}`;
                if (attachment.model) text += ` ${attachment.model}`;
                if (attachment.sn_attachment) text += ` (SN: ${attachment.sn_attachment})`;
                textList.push(text);
            });
            container.text(textList.join(', '));
        } else {
            container.html('<em class="text-muted">Tidak ada attachment</em>');
        }
    }

    function populateUnitBatteries(batteries) {
        const container = $('#viewUnitBatteryList');
        
        if (batteries && batteries.length > 0) {
            let textList = [];
            batteries.forEach(function(battery, index) {
                let text = `${index + 1}. ${battery.tipe_baterai || 'Battery'} - ${battery.merk_baterai || 'Unknown'}`;
                if (battery.jenis_baterai) text += ` ${battery.jenis_baterai}`;
                if (battery.sn_baterai) text += ` (SN: ${battery.sn_baterai})`;
                textList.push(text);
            });
            container.text(textList.join(', '));
        } else {
            container.html('<em class="text-muted">Tidak ada battery</em>');
        }
    }

    function populateUnitChargers(chargers) {
        const container = $('#viewUnitChargerList');
        
        if (chargers && chargers.length > 0) {
            let textList = [];
            chargers.forEach(function(charger, index) {
                let text = `${index + 1}. ${charger.tipe_charger || 'Charger'} - ${charger.merk_charger || 'Unknown'}`;
                if (charger.sn_charger) text += ` (SN: ${charger.sn_charger})`;
                textList.push(text);
            });
            container.text(textList.join(', '));
        } else {
            container.html('<em class="text-muted">Tidak ada charger</em>');
        }
    }

    function loadSubcategories(categoryId, selectedSubcategoryId = null) {
        $.ajax({
            url: '<?= base_url('work-orders/get-subcategories') ?>',
            type: 'POST',
            data: { category_id: categoryId },
            success: function(response) {
                if (response.success) {
                    let subcategorySelect = $('#subcategory_id');
                    subcategorySelect.empty().append('<option value="">Pilih Sub Kategori</option>');
                    
                    $.each(response.data, function(index, subcategory) {
                        let selected = selectedSubcategoryId == subcategory.id ? 'selected' : '';
                        subcategorySelect.append(`<option value="${subcategory.id}" ${selected}>${subcategory.subcategory_name}</option>`);
                    });
                }
            }
        });
    }
    
    function populateUnitAccessories(accessories) {
        const container = $('#viewUnitAccessoriesList');
        
        console.log('Raw accessories data:', accessories, typeof accessories);
        
        // Handle different data types
        let accessoriesArray = [];
        
        if (typeof accessories === 'string') {
            try {
                accessoriesArray = JSON.parse(accessories);
            } catch (e) {
                // If it's a comma-separated string, split it
                accessoriesArray = accessories.split(',').map(item => item.trim());
            }
        } else if (Array.isArray(accessories)) {
            accessoriesArray = accessories;
        } else {
            console.log('Accessories data is neither array nor string:', accessories);
            container.html('<div class="text-muted fst-italic">Tidak ada aksesoris</div>');
            $('#unitAccessoriesSection').hide();
            return;
        }
        
        console.log('Processed accessories array:', accessoriesArray);
        
        if (accessoriesArray && accessoriesArray.length > 0) {
            // Create simple comma-separated list like complaint format
            let accessoryNames = [];
            
            accessoriesArray.forEach(function(accessory) {
                // Handle if accessory is a string (just the name)
                let accessoryName = '';
                
                if (typeof accessory === 'string') {
                    accessoryName = accessory.trim();
                } else if (typeof accessory === 'object') {
                    accessoryName = accessory.accessory_name || accessory.name || 'Unknown Accessory';
                }
                
                if (accessoryName) {
                    accessoryNames.push(accessoryName);
                }
            });
            
            // Display as simple text like complaint format
            if (accessoryNames.length > 0) {
                container.html(`<div class="text-dark">${accessoryNames.join(', ')}</div>`);
                $('#unitAccessoriesSection').show();
            } else {
                container.html('<div class="text-muted fst-italic">Tidak ada aksesoris</div>');
                $('#unitAccessoriesSection').hide();
            }
        } else {
            container.html('<div class="text-muted fst-italic">Tidak ada aksesoris</div>');
            $('#unitAccessoriesSection').hide();
        }
    }

    function clearFormErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
    }

    function displayFormErrors(errors) {
        $.each(errors, function(field, message) {
            let input = $(`#${field}`);
            input.addClass('is-invalid');
            input.after(`<div class="invalid-feedback">${message}</div>`);
        });
    }

    function showAlert(type, message) {
        // Use OptimaPro notification system if available
        if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
            let toastType = type === 'error' ? 'danger' : type;
            OptimaPro.showNotification(message, toastType);
        } else if (typeof showNotification === 'function') {
            // Fallback to global notification system
            let toastType = type === 'success' ? 'success' : 
                           type === 'error' ? 'danger' : 'info';
            showNotification(message, toastType);
        } else {
            // Fallback to local alert system
            let alertClass = type === 'success' ? 'alert-success' : 
                            type === 'error' ? 'alert-danger' : 'alert-info';
            
            let alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert" id="mainAlert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            $('#alertContainer').html(alertHtml);
            
            if (type === 'success') {
                setTimeout(hideAlert, 3000);
            }
        }
    }

    function hideAlert() {
        $('#mainAlert').alert('close');
    }

    function updateStatistics() {
        $.ajax({
            url: '<?= base_url('work-orders/stats') ?>',
            type: 'GET',
            success: function(response) {
                if (response.status) {  // Backend menggunakan 'status' bukan 'success'
                    $('#stat-total-work-orders').text(response.data.total_work_orders || 0);
                    $('#stat-open').text(response.data.open_work_orders || 0);
                    $('#stat-in-progress').text(response.data.in_progress_work_orders || 0);
                    $('#stat-completed').text(response.data.completed_work_orders || 0);
                    console.log('📊 Statistics updated');
                } else {
                    console.log('❌ Failed to update statistics:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log('❌ Error updating statistics:', error);
                // Don't retry - just skip statistics update
            }
        });
    }

    // Auto-refresh statistics every 30 seconds
    setInterval(updateStatistics, 30000);

    // Print Work Order
    $(document).on('click', '.btn-print', function() {
        let id = $(this).data('id');
        window.open('<?= base_url('work-orders/print') ?>/' + id, '_blank');
    });

    // Export functionality
    $('#exportBtn').on('click', function() {
        window.location.href = '<?= base_url('work-orders/export') ?>';
    });

    // Add Work Order button
    $('#btn-add-wo').on('click', function() {
        console.log('🆕 Opening new work order modal');
        // Auto generate WO number when opening modal
        generateWorkOrderNumber();
        
        // Ensure form is set for create mode
        $('#workOrderFormTitle').html('<i class="fas fa-plus-circle me-2"></i>Tambah Work Order Baru');
        $('#workOrderForm').attr('action', '<?= base_url('work-orders/store') ?>');
        $('#btnSubmitWo').html('<i class="fas fa-save me-1"></i> Simpan Work Order');
        $('#work_order_id').val('');
        
        $('#workOrderModal').modal('show');
    });

    // Auto generate Work Order number
    function generateWorkOrderNumber() {
        $.ajax({
            url: '<?= base_url('work-orders/generate-number') ?>',
            type: 'GET',
            success: function(response) {
                console.log('🔢 WO number generated:', response);
                if (response.success) {
                    $('#work_order_number').val(response.work_order_number);
                } else {
                    console.log('❌ Failed to generate WO number:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log('❌ Error generating work order number:', error);
            }
        });
    }

    // Unit search functionality
    let unitSearchTimeout;
    $('#unit_search').on('input', function() {
        let query = $(this).val().trim();
        clearTimeout(unitSearchTimeout);
        
        if (query.length >= 2) {
            unitSearchTimeout = setTimeout(function() {
                searchUnits(query);
            }, 300);
        } else {
            $('#unit_search_results').hide();
        }
    });

    function searchUnits(query) {
        $.ajax({
            url: '<?= base_url('work-orders/search-units') ?>',
            type: 'POST',
            data: { query: query },
            beforeSend: function() {
                $('#unit_search_results').html('<div class="list-group-item"><i class="fas fa-spinner fa-spin"></i> Mencari...</div>').show();
            },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    let html = '';
                    response.data.forEach(function(unit) {
                        html += `<div class="list-group-item list-group-item-action unit-result" 
                                    data-unit-id="${unit.id_inventory_unit}" 
                                    data-unit-info="${unit.no_unit} - ${unit.pelanggan} (${unit.merk_unit} ${unit.model_unit})">
                                    <div class="fw-bold">${unit.no_unit}</div>
                                    <div class="text-muted small">${unit.pelanggan} | ${unit.merk_unit || 'N/A'} ${unit.model_unit || ''}</div>
                                    <div class="text-primary small">SN: ${unit.serial_number || 'N/A'} | Lokasi: ${unit.lokasi}</div>
                                </div>`;
                    });
                    $('#unit_search_results').html(html).show();
                } else {
                    $('#unit_search_results').html('<div class="list-group-item text-muted">Tidak ada unit yang ditemukan</div>').show();
                }
            },
            error: function() {
                $('#unit_search_results').html('<div class="list-group-item text-danger">Error mencari unit</div>').show();
            }
        });
    }

    // Unit selection
    $(document).on('click', '.unit-result', function() {
        let unitId = $(this).data('unit-id');
        let unitInfo = $(this).data('unit-info');
        
        $('#unit_id').val(unitId);
        $('#unit_search').val(unitInfo);
        $('#unit_search_results').hide();
    });

    // Clear unit search
    $('#btn_clear_unit').on('click', function() {
        $('#unit_search').val('');
        $('#unit_id').val('');
        $('#unit_search_results').hide();
    });

    // Hide search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#unit_search, #unit_search_results').length) {
            $('#unit_search_results').hide();
        }
        // Also hide staff search results
        $('.list-group[id$="_search_results"]').hide();
    });

    // Category change handler for subcategory and auto priority
    $('#category_id').on('change', function() {
        let categoryId = $(this).val();
        let selectedOption = $(this).find('option:selected');
        let defaultPriority = selectedOption.data('priority');
        
        // Load subcategories
        loadSubcategories(categoryId);
        
        // Set auto priority based on category
        if (defaultPriority) {
            setPriority(defaultPriority);
        }
    });

    // Subcategory change handler for auto priority
    $('#subcategory_id').on('change', function() {
        let subcategoryId = $(this).val();
        if (subcategoryId) {
            // Get priority for subcategory
            $.ajax({
                url: '<?= base_url('work-orders/get-subcategory-priority') ?>',
                type: 'POST',
                data: { subcategory_id: subcategoryId },
                success: function(response) {
                    if (response.success && response.priority_id) {
                        setPriority(response.priority_id);
                    }
                }
            });
        }
    });

    function setPriority(priorityId) {
        // Find priority name and set display
        $.ajax({
            url: '<?= base_url('work-orders/get-priority') ?>',
            type: 'POST',
            data: { priority_id: priorityId },
            success: function(response) {
                if (response.success) {
                    $('#priority_id').val(priorityId);
                    $('#priority_display').val(response.priority_name);
                }
            }
        });
    }

    // Staff search functionality
    let staffSearchTimeouts = {};
    $('.staff-search').on('input', function() {
        let $this = $(this);
        let query = $this.val().trim();
        let staffType = $this.data('staff-type');
        let resultDiv = $this.closest('.input-group').next().next();
        
        clearTimeout(staffSearchTimeouts[staffType]);
        
        if (query.length >= 2) {
            staffSearchTimeouts[staffType] = setTimeout(function() {
                searchStaff(query, staffType, resultDiv);
            }, 300);
        } else {
            resultDiv.hide();
        }
    });

    function searchStaff(query, staffType, resultDiv) {
        $.ajax({
            url: '<?= base_url('work-orders/search-staff') ?>',
            type: 'POST',
            data: { query: query, staff_type: staffType },
            beforeSend: function() {
                resultDiv.html('<div class="list-group-item"><i class="fas fa-spinner fa-spin"></i> Mencari...</div>').show();
            },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    let html = '';
                    response.data.forEach(function(staff) {
                        html += `<div class="list-group-item list-group-item-action staff-result" 
                                    data-staff-id="${staff.id}" 
                                    data-staff-name="${staff.staff_name}"
                                    data-staff-type="${staffType.toLowerCase()}">
                                    <div class="fw-bold">${staff.staff_name}</div>
                                    <div class="text-muted small">${staff.position || staffType} | ${staff.department || 'N/A'}</div>
                                </div>`;
                    });
                    resultDiv.html(html).show();
                } else {
                    resultDiv.html('<div class="list-group-item text-muted">Tidak ada staff yang ditemukan</div>').show();
                }
            },
            error: function() {
                resultDiv.html('<div class="list-group-item text-danger">Error mencari staff</div>').show();
            }
        });
    }

    // Staff selection
    $(document).on('click', '.staff-result', function() {
        let staffId = $(this).data('staff-id');
        let staffName = $(this).data('staff-name');
        let staffType = $(this).data('staff-type');
        
        $(`#${staffType}_staff_id`).val(staffId);
        $(`#${staffType}_staff_search`).val(staffName);
        $(this).parent().hide();
    });

    // Clear staff search
    $('.btn-clear-staff').on('click', function() {
        let target = $(this).data('target');
        $(`#${target}_staff_search`).val('');
        $(`#${target}_staff_id`).val('');
        $(`#${target}_search_results`).hide();
    });

    function loadSubcategories(categoryId, selectedSubcategoryId = null) {
        $.ajax({
            url: '<?= base_url('work-orders/get-subcategories') ?>',
            type: 'POST',
            data: { category_id: categoryId },
            success: function(response) {
                if (response.success || response.status) {
                    let subcategorySelect = $('#subcategory_id');
                    subcategorySelect.empty().append('<option value="">-- Pilih Sub Kategori (jika ada) --</option>');
                    
                    let data = response.data || response.subcategories || [];
                    $.each(data, function(index, subcategory) {
                        let selected = selectedSubcategoryId == subcategory.id ? 'selected' : '';
                        subcategorySelect.append(`<option value="${subcategory.id}" ${selected} data-priority="${subcategory.default_priority_id || ''}">${subcategory.subcategory_name}</option>`);
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log('Error loading subcategories:', error);
            }
        });
    }

    // Load initial data when modal opens
    $('#workOrderModal').on('shown.bs.modal', function() {
        loadUnitsDropdown();
        loadAllStaffDropdowns();
    });

    // Global variables to store data - make them globally accessible
    window.allUnits = [];
    window.allStaff = {
        admin: [],
        foreman: [],
        mechanic: [],
        helper: []
    };

    // Units Dropdown Management
    function loadUnitsDropdown() {
        $.ajax({
            url: '<?= base_url('work-orders/units-dropdown') ?>',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    window.allUnits = response.data;
                    displayUnits(window.allUnits);
                }
            },
            error: function(xhr, status, error) {
                console.log('❌ Error loading units:', error);
            }
        });
    }

    function displayUnits(units) {
        let unitList = $('#unitDropdownList');
        unitList.empty();
        
        units.forEach(function(unit) {
            let displayName = unit.no_unit + ' - ' + (unit.pelanggan || 'Unknown') + ' (' + (unit.merk_unit + ' ' + unit.model_unit || unit.unit_type || 'Unknown') + ')';
            unitList.append(`
                <a class="dropdown-item unit-item" href="#" data-unit-id="${unit.id}" data-unit='${JSON.stringify(unit)}'>
                    ${displayName}
                </a>
            `);
        });
    }

    // Unit selection handler
    $(document).on('click', '.unit-item', function(e) {
        e.preventDefault();
        let unitId = $(this).data('unit-id');
        let unitData = $(this).data('unit');
        let displayName = $(this).text();
        
        $('#unitSelectedText').text(displayName);
        $('#unit_id').val(unitId);
    });

    // Unit filter function - make it globally accessible
    window.filterUnits = function() {
        let searchTerm = $('#unitSearch').val().toLowerCase();
        let filteredUnits = window.allUnits.filter(unit => {
            let displayName = (unit.no_unit + ' ' + (unit.pelanggan || '') + ' ' + (unit.merk_unit || '') + ' ' + (unit.model_unit || '') + ' ' + (unit.unit_type || '')).toLowerCase();
            return displayName.includes(searchTerm);
        });
        displayUnits(filteredUnits);
    }

    // Staff Dropdown Management
    function loadStaffDropdown(staffRole, targetId) {
        $.ajax({
            url: '<?= base_url('work-orders/staff-dropdown') ?>',
            type: 'POST',
            data: { staff_role: staffRole },
            success: function(response) {
                if (response.success) {
                    window.allStaff[targetId.toLowerCase()] = response.data;
                    displayStaff(targetId.toLowerCase(), response.data);
                }
            },
            error: function(xhr, status, error) {
                console.log(`❌ Error loading ${staffRole} staff:`, error);
            }
        });
    }

    function displayStaff(staffType, staffList) {
        let dropdownList = $(`#${staffType}DropdownList`);
        dropdownList.empty();
        
        staffList.forEach(function(staff) {
            dropdownList.append(`
                <a class="dropdown-item staff-item" href="#" data-staff-type="${staffType}" data-staff-id="${staff.id}">
                    ${staff.staff_name}
                </a>
            `);
        });
    }

    // Staff selection handler
    $(document).on('click', '.staff-item', function(e) {
        e.preventDefault();
        let staffType = $(this).data('staff-type');
        let staffId = $(this).data('staff-id');
        let staffName = $(this).text();
        
        $(`#${staffType}SelectedText`).text(staffName);
        $(`#${staffType}_staff_id`).val(staffId);
    });

    // Staff filter function - make it globally accessible
    window.filterStaff = function(staffType) {
        let searchTerm = $(`#${staffType}Search`).val().toLowerCase();
        let filteredStaff = window.allStaff[staffType].filter(staff => {
            return staff.staff_name.toLowerCase().includes(searchTerm);
        });
        displayStaff(staffType, filteredStaff);
    }

    function loadAllStaffDropdowns() {
        loadStaffDropdown('ADMIN', 'admin');
        loadStaffDropdown('FOREMAN', 'foreman');
        loadStaffDropdown('MECHANIC', 'mechanic');
        loadStaffDropdown('HELPER', 'helper');
    }




});
</script>
<?= $this->endSection() ?>
