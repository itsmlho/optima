<?= $this->extend('layouts/base') ?>

<?php
// Load global permission helper
helper('global_permission');

// Get permissions for purchasing module
$permissions = get_global_permission('purchasing');
$can_view = $permissions['view'];
$can_create = $permissions['create'];
$can_edit = $permissions['edit'];
$can_delete = $permissions['delete'];
$can_export = $permissions['export'];
?>

<?= $this->section('css') ?>
<!-- CSS umum sudah ada di optima-pro.css -->
<style>
/* Custom purchasing page - Minimal custom styling */

.card-body.p-0 {
    padding: 0 !important;
}

.table-responsive {
    width: 100% !important;
    overflow-x: auto;
    margin: 0;
}

.dataTables_wrapper {
    width: 100% !important;
    padding: 1.5rem;
}

.table {
    margin-bottom: 0;
}

.table thead th {
    background: linear-gradient(180deg, #ffffff 0%, #f8f9fc 100%);
    border-bottom: 2px solid #e3e6f0;
    color: #5a5c69;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.7rem;
    letter-spacing: 0.08em;
    padding: 1rem 0.75rem;
    white-space: nowrap;
}

.table tbody tr {
    transition: all 0.2s ease;
    border-bottom: 1px solid #f1f3f5;
    cursor: pointer;
}

.table tbody tr:hover {
    background-color: #f8f9fc;
    box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    transform: translateY(-1px);
}

.table tbody td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
    color: #5a5c69;
    font-size: 0.875rem;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: #ffffff;
}

.table-striped tbody tr:nth-of-type(even) {
    background-color: #fafbfc;
}

/* Badge Styling - Consistent Colors */
.badge {
  font-size: 12px;
  padding: 6px 10px;
  border-radius: 12px;
}


/* Button Styling */
.btn {
    border-radius: 0.375rem;
    font-weight: 500;
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.8rem;
}

.btn-group .btn {
    margin: 0 0.125rem;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

/* Card Styling */
.card {
    border: 1px solid #e3e6f0;
    border-radius: 0.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.card-header {
    background: linear-gradient(180deg, #ffffff 0%, #f8f9fc 100%);
    border-bottom: 2px solid #e3e6f0;
}

/* Modal Styling */
.modal-header {
    background: linear-gradient(180deg, #ffffff 0%, #f8f9fc 100%);
    border-bottom: 2px solid #e3e6f0;
}

.modal-title {
    color: #5a5c69;
    font-weight: 600;
}

.modal-xl {
    max-width: 1200px;
}

/* Form Styling */
.form-control {
    border: 1px solid #d1d3e2;
    border-radius: 0.375rem;
    padding: 0.625rem 0.875rem;
}

.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.15);
}

.form-label {
    color: #5a5c69;
    font-weight: 600;
    font-size: 0.875rem;
}

/* Progress Bar */
.progress {
    height: 22px;
    font-size: 0.75rem;
    background-color: #e9ecef;
    border-radius: 0.5rem;
}

.progress-bar {
    font-weight: 600;
}

/* Dropdown */
.dropdown-item i {
    min-width: 20px;
    text-align: center;
}

/* Responsive */
@media (max-width: 768px) {
    .nav-tabs .nav-link {
        padding: 0.625rem 1rem;
        font-size: 0.85rem;
    }
    
    .badge {
        font-size: 0.65rem;
        padding: 0.3rem 0.5rem;
    }
    
    .table thead th {
        font-size: 0.65rem;
        padding: 0.75rem 0.5rem;
    }
    
    .table tbody td {
        padding: 0.75rem 0.5rem;
        font-size: 0.8rem;
    }
    
    .dataTables_wrapper {
        padding: 1rem;
    }
    
    /* Export Dropdown Styling */
    .dropdown-menu {
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        border-radius: 10px;
        padding: 0.5rem 0;
        min-width: 200px;
    }
    .dropdown-item {
        padding: 0.75rem 1.5rem;
        transition: all 0.2s ease;
        border-radius: 0;
        display: flex;
        align-items: center;
    }
    .dropdown-item:hover {
        background-color: #f8f9fc;
        color: #4e73df;
        transform: translateX(5px);
    }
    .dropdown-item i {
        width: 20px;
        text-align: center;
    }
    
    /* Premium Export Button Styling */
    .btn-outline-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        color: white;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .btn-outline-success:hover {
        background: linear-gradient(135deg, #218838 0%, #1ea085 100%);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        transform: translateY(-2px);
        color: white;
    }
    
    /* use centralized button styles from optima-pro.css */
}

/* Disabled Table Styling */
.table-disabled {
    opacity: 0.6;
    pointer-events: none;
    user-select: none;
}

.table-disabled tbody tr {
    cursor: not-allowed !important;
}

.table-disabled tbody tr:hover {
    background-color: #f8f9fa !important;
}

.table-disabled .btn {
    pointer-events: none;
    opacity: 0.5;
}

/* Scrollbar */
.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f3f5;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #d1d3e2;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #b7b9cc;
}

.item-badge { 
    font-size: 0.85rem; 
    padding: 0.35rem 0.65rem; 
}

.form-section { 
    border-radius: 10px; 
    box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
    background: white;
}

.section-header { 
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); 
    color: #333; 
    padding: 0.75rem 1rem; 
    border-radius: 10px 10px 0 0; 
    font-weight: 600;
    font-size: 0.95rem;
}

/* Fix Select2 dropdown in scrollable modal */
.select2-dropdown-fixed {
    z-index: 10060 !important; /* Above modal backdrop (1050) */
}

.select2-container--open {
    z-index: 10060 !important;
}

/* Ensure Select2 dropdown stays visible when scrolling */
.modal-body {
    position: relative;
    overflow-y: auto;
}

.select2-container {
    z-index: 10050;
}

/* Fix Select2 search input in dropdown */
.select2-search--dropdown .select2-search__field {
    padding: 4px 8px;
    border: 1px solid #ddd;
}

/* Better Select2 dropdown positioning */
.select2-dropdown {
    border: 1px solid rgba(0,0,0,.15);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.175);
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Success/Error Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>



<!-- Global Filters -->
<div class="card filter-card mb-4">
    <div class="card-body">
        <h5 class="card-title mb-3">
            <i class="fas fa-filter me-2"></i>Global Filters
        </h5>
        <div class="row g-3">
            <div class="col-md-3">
                <label for="globalFilterStatus" class="form-label">Status</label>
                <select id="globalFilterStatus" class="form-select">
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="completed">Completed</option>
                    <option value="Selesai dengan Catatan">Selesai dengan Catatan</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <!-- Supplier filter removed per request -->
            <div class="col-md-3">
                <label for="globalFilterDateFrom" class="form-label">Dari Tanggal</label>
                <input type="date" id="globalFilterDateFrom" class="form-control" value="2025-01-01">
            </div>
            <div class="col-md-3">
                <label for="globalFilterDateTo" class="form-label">Sampai Tanggal</label>
                <input type="date" id="globalFilterDateTo" class="form-control" value="2025-12-31">
            </div>
        </div>
    </div>
</div>

<!-- Main Card -->
<div class="card table-card">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-truck me-2"></i>PO Unit & Attachment
            </h5>
            <div class="btn-group" role="group">
                <?php if ($can_create): ?>
                <button type="button" class="btn btn-primary" id="btnBuatPO">
                    <i class="fas fa-plus me-1"></i>Buat PO
                </button>
                <?php else: ?>
                <button type="button" class="btn btn-secondary" disabled title="Access denied: You do not have permission to create Purchase Orders">
                    <i class="fas fa-lock me-1"></i>Buat PO
                </button>
                <?php endif; ?>
                <button type="button" class="btn btn-outline-primary" onclick="refreshTable()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
                <?php if ($can_export): ?>
                <div class="dropdown">
                    <button class="btn btn-outline-success dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-excel me-1"></i>Export
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                        <li><a class="dropdown-item" href="<?= base_url('purchasing/export_po_progres') ?>">
                            <i class="fas fa-clock me-2 text-success"></i>Export Progres
                        </a></li>
                        <li><a class="dropdown-item" href="<?= base_url('purchasing/export_po_delivery') ?>">
                            <i class="fas fa-truck me-2 text-warning"></i>Export Delivery
                        </a></li>
                        <li><a class="dropdown-item" href="<?= base_url('purchasing/export_po_completed') ?>">
                            <i class="fas fa-check-circle me-2 text-info"></i>Export Completed
                        </a></li>
                    </ul>
                </div>
                <?php else: ?>
                <button type="button" class="btn btn-outline-secondary" disabled title="Access denied: You do not have permission to export data">
                    <i class="fas fa-lock me-1"></i>Export
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

        <!-- Tabs Navigation -->
        <div class="card-header bg-white border-bottom">
            <ul class="nav nav-tabs card-header-tabs" id="poTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="progres-tab" data-bs-toggle="tab" data-bs-target="#progres-pane" type="button" role="tab" aria-controls="progres-pane" aria-selected="true">
                        <i class="fas fa-clock"></i>
                        <span>Progres</span>
                        <span class="badge" id="progres-count">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="delivery-tab" data-bs-toggle="tab" data-bs-target="#delivery-pane" type="button" role="tab" aria-controls="delivery-pane" aria-selected="false">
                        <i class="fas fa-truck"></i>
                        <span>Delivery</span>
                        <span class="badge" id="delivery-count">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-pane" type="button" role="tab" aria-controls="completed-pane" aria-selected="false">
                        <i class="fas fa-check-circle"></i>
                        <span>Completed</span>
                    </button>
                </li>
            </ul>
        </div>

    <!-- Tab Content -->
    <div class="tab-content" id="poTabContent">
        <!-- Progres Tab -->
        <div class="tab-pane fade show active" id="progres-pane" role="tabpanel" aria-labelledby="progres-tab">
            <?php if (!can_view('purchasing')): ?>
            <div class="alert alert-warning m-3">
                <i class="fas fa-lock me-2"></i>
                <strong>Access Denied:</strong> You do not have permission to view Purchase Order details. 
                Please contact your administrator to request access.
            </div>
            <?php endif; ?>
            <div class="card-body p-0">
        <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 <?= !$can_view ? 'table-disabled' : '' ?>" id="unitAttachmentPOTable" style="cursor: pointer;">
                <thead class="table-dark">
                    <tr>
                        <th>No PO</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Status</th>
                        <th>Total Items</th>
                        <th>Progress Verifikasi</th>
                        <th>Status Pengiriman</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data akan diisi oleh DataTable -->
                </tbody>
            </table>
                </div>
            </div>
        </div>
        
        <!-- Delivery Tab -->
        <div class="tab-pane fade" id="delivery-pane" role="tabpanel" aria-labelledby="delivery-tab">
            <?php if (!can_view('purchasing')): ?>
            <div class="alert alert-warning m-3">
                <i class="fas fa-lock me-2"></i>
                <strong>Access Denied:</strong> You do not have permission to view delivery details. 
                Please contact your administrator to request access.
            </div>
            <?php endif; ?>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 <?= !$can_view ? 'table-disabled' : '' ?>" id="poDeliveryTable" style="cursor: pointer;">
                        <thead class="table-dark">
                            <tr>
                                <th>Packing List</th>
                                <th>PO Number</th>
                                <th>Supplier</th>
                                <th>Delivery Date</th>
                                <th>Driver</th>
                                <th>Items</th>
                                <th>Status</th>
                                <th>Actions</th>
                                <th>Print Packing List</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan dimuat via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Completed Tab -->
        <div class="tab-pane fade" id="completed-pane" role="tabpanel" aria-labelledby="completed-tab">
            <?php if (!can_view('purchasing')): ?>
            <div class="alert alert-warning m-3">
                <i class="fas fa-lock me-2"></i>
                <strong>Access Denied:</strong> You do not have permission to view completed orders. 
                Please contact your administrator to request access.
            </div>
            <?php endif; ?>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 <?= !$can_view ? 'table-disabled' : '' ?>" id="unitAttachmentPOCompletedTable" style="cursor: pointer;">
                        <thead class="table-dark">
                            <tr>
                                <th>No PO</th>
                                <th>Tanggal</th>
                                <th>Supplier</th>
                                <th>Status</th>
                                <th>Total Items</th>
                                <th>Progress Verifikasi</th>
                                <th>Status Pengiriman</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan diisi oleh DataTable -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- No modal needed - using existing forms -->

<!-- Create Delivery Modal -->
<div class="modal fade" id="createDeliveryModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Delivery Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createDeliveryForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Buat jadwal pengiriman untuk PO ini
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Delivery Date *</label>
                            <input type="date" class="form-control" name="delivery_date" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Packing List No *</label>
                            <input type="text" class="form-control" name="packing_list_no" required placeholder="Masukkan nomor packing list dari supplier">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Driver Name</label>
                            <input type="text" class="form-control" name="driver_name" placeholder="Nama driver (opsional)">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Driver Phone</label>
                            <input type="text" class="form-control" name="driver_phone" placeholder="Nomor HP driver (opsional)">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Vehicle Info</label>
                            <input type="text" class="form-control" name="vehicle_info" placeholder="Info kendaraan (opsional)">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Vehicle Plate</label>
                            <input type="text" class="form-control" name="vehicle_plate" placeholder="Nomor plat kendaraan (opsional)">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Catatan pengiriman..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Items to Deliver <small class="text-muted">(Pilih berapa unit yang akan dikirim)</small></label>
                        <div id="deliveryItemsList" class="border rounded p-3">
                            <!-- Items will be loaded dynamically -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Delivery</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Serial Numbers Modal -->
<div class="modal fade" id="assignSNModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-barcode me-2"></i>Assign Serial Numbers
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="proceedWithoutSN()" id="proceedWithoutSNBtn">
                        <i class="fas fa-forward me-1"></i>Lanjut Tanpa SN
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <form id="assignSNForm">
                <div class="modal-body">
                    <!-- Simple Delivery Info -->
                    <div class="alert alert-light border mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Packing List:</strong><br>
                                <span id="snModalPackingList" class="text-primary fw-bold">-</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Driver:</strong><br>
                                <span id="snModalDriver" class="text-muted">-</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Delivery Date:</strong><br>
                                <span id="snModalDeliveryDate" class="text-muted">-</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Vehicle:</strong><br>
                                <span id="snModalVehicle" class="text-muted">-</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Items for SN Assignment -->
                    <div class="mb-3">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-barcode me-2"></i>Items to Assign Serial Numbers
                        </h6>
                        <div id="snAssignmentContent">
                            <!-- Content will be loaded dynamically -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Save Serial Numbers
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View PO Detail Modal -->
<div class="modal fade" id="viewPOModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.12);">
            <div class="modal-header" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); color: #2c3e50; border-bottom: 1px solid #e9ecef; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title" style="font-weight: 600;">
                    <i class="fas fa-file-invoice me-2 text-primary"></i>Detail Purchase Order
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="poDetailsContent" style="padding: 0;">
                <!-- Loading State -->
                <div class="text-center p-5" id="poLoadingState">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 class="mt-3 text-muted">Memuat detail PO...</h5>
                </div>
                
                <!-- PO Content (hidden initially) -->
                <div id="poContent" style="display: none;">
                    <!-- Summary Cards -->
                    <div class="p-4" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-bottom: 1px solid #e9ecef;">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                                    <div class="card-body text-center p-3">
                                        <div class="mb-2" id="totalItemsOrdered">
                                            <span class="text-muted">No items</span>
                                        </div>
                                        <small class="text-muted">Total Items Dipesan</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                                    <div class="card-body text-center p-3">
                                        <div class="text-info mb-2">
                                            <i class="fas fa-truck fa-2x"></i>
                                        </div>
                                        <h4 class="mb-1" id="deliveryProgress">0/0</h4>
                                        <small class="text-muted">Pengiriman</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                                    <div class="card-body text-center p-3">
                                        <div class="text-success mb-2">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                        <h4 class="mb-1" id="totalItemsReceived">0</h4>
                                        <small class="text-muted">Items Diterima</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                                    <div class="card-body text-center p-3">
                                        <div class="text-warning mb-2">
                                            <i class="fas fa-clipboard-check fa-2x"></i>
                                        </div>
                                        <h4 class="mb-1" id="verifiedItems">0</h4>
                                        <small class="text-muted">Items Terverifikasi</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- PO Information -->
                    <div class="p-4">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Informasi Purchase Order
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light text-dark me-2" style="min-width: 100px;">No PO:</span>
                                    <span class="fw-bold" id="poNumber">-</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light text-dark me-2" style="min-width: 100px;">Tanggal PO:</span>
                                    <span id="poDate">-</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light text-dark me-2" style="min-width: 100px;">Supplier:</span>
                                    <span id="poSupplier">-</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light text-dark me-2" style="min-width: 100px;">Kontak:</span>
                                    <span id="poContact">-</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light text-dark me-2" style="min-width: 100px;">Status PO:</span>
                                    <span id="poStatus">-</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light text-dark me-2" style="min-width: 100px;">Invoice No:</span>
                                    <span id="poInvoice">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabs for Items and Deliveries -->
                    <div class="px-4">
                        <ul class="nav nav-tabs" id="poDetailTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="items-tab" type="button" role="tab">
                                    <i class="fas fa-list me-2"></i>Daftar Items <span class="badge bg-primary ms-1" id="itemsCount">0</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="deliveries-tab" type="button" role="tab">
                                    <i class="fas fa-truck me-2"></i>Pengiriman <span class="badge bg-info ms-1" id="deliveriesCount">0</span>
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="poDetailTabContent">
                            <!-- Items Tab -->
                            <div class="tab-pane fade show active" id="items-pane" role="tabpanel">
                                <div class="p-3">
                                    <div id="poItemsContent">
                                        <!-- Will be populated dynamically with dropdown style -->
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Deliveries Tab -->
                            <div class="tab-pane fade" id="deliveries-pane" role="tabpanel">
                                <div class="p-3">
                                    <div id="deliveriesContent">
                                        <!-- Will be populated dynamically -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="poModalFooter" style="background-color: #ffffff; border-top: 1px solid #e9ecef; border-radius: 0 0 12px 12px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Close
                </button>
                <?php if (can_view('purchasing')): ?>
                <button type="button" class="btn btn-info" id="printPOBtn" onclick="printPOFromModal()" style="display: none;">
                    <i class="fas fa-print me-2"></i>Print PO
                </button>
                <?php else: ?>
                <button type="button" class="btn btn-secondary" disabled title="Access denied: You do not have permission to print PO" style="display: none;">
                    <i class="fas fa-lock me-2"></i>Print PO
                </button>
                <?php endif; ?>
                <!-- Dynamic action buttons will be added here -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Create PO (Unified - Unit, Attachment, Battery, Charger) -->
<div class="modal fade" id="createPoModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Buat Purchase Order - Unit & Attachment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <form action="<?= base_url('/purchasing/store-unified-po') ?>" method="post" id="unifiedPOForm">
                    <?= csrf_field() ?>
                    
                    <!-- Header PO Section -->
                    <div class="form-section mb-4">
                        <h6 class="section-header">
                            <i class="fas fa-info-circle me-2"></i>Informasi Purchase Order
                        </h6>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="no_po" class="form-label">Nomor PO <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="no_po" id="no_po" required placeholder="Contoh: PO-2025-001">
                                </div>
                                <div class="col-md-4">
                                    <label for="tanggal_po" class="form-label">Tanggal PO <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="tanggal_po" id="tanggal_po" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="id_supplier_modal" class="form-label">Supplier <span class="text-danger">*</span></label>
                                    <select name="id_supplier" id="id_supplier_modal" class="form-select select2-modal" required>
                                        <option value="">Pilih Supplier...</option>
                                        <?php if (isset($suppliers) && is_array($suppliers)): ?>
                                            <?php foreach ($suppliers as $item): ?>
                                                <option value="<?= $item['id_supplier'] ?>"><?= esc($item['nama_supplier']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="invoice_no" class="form-label">Invoice Number</label>
                                    <input type="text" class="form-control" name="invoice_no" id="invoice_no" placeholder="Optional">
                                </div>
                                <div class="col-md-4">
                                    <label for="invoice_date" class="form-label">Invoice Date</label>
                                    <input type="date" class="form-control" name="invoice_date" id="invoice_date">
                                </div>
                                <div class="col-md-4">
                                    <label for="bl_date" class="form-label">BL Date</label>
                                    <input type="date" class="form-control" name="bl_date" id="bl_date">
                                </div>
                                <div class="col-12">
                                    <label for="keterangan_po" class="form-label">Keterangan PO</label>
                                    <textarea class="form-control" name="keterangan_po" id="keterangan_po" rows="2" placeholder="Catatan tambahan (optional)"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Section -->
                    <div class="form-section mb-4">
                        <h6 class="section-header">
                            <i class="fas fa-list me-2"></i>Daftar Item PO
                        </h6>
                        <div class="card-body p-4">
                            <!-- Action Buttons -->
                            <div class="mb-3 d-flex gap-2 flex-wrap">
                                <?php if (can_create('purchasing')): ?>
                                <button type="button" class="btn btn-primary btn-sm" onclick="openItemModal('unit')">
                                    <i class="fas fa-truck me-1"></i>Tambah Unit
                                </button>
                                <button type="button" class="btn btn-success btn-sm" onclick="openItemModal('attachment')">
                                    <i class="fas fa-tools me-1"></i>Tambah Attachment
                                </button>
                                <button type="button" class="btn btn-info btn-sm" onclick="openItemModal('battery')">
                                    <i class="fas fa-battery-full me-1"></i>Tambah Battery
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" onclick="openItemModal('charger')">
                                    <i class="fas fa-plug me-1"></i>Tambah Charger
                                </button>
                                <?php else: ?>
                                <button type="button" class="btn btn-secondary btn-sm" disabled title="Access denied: You do not have permission to add items">
                                    <i class="fas fa-lock me-1"></i>Tambah Unit
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" disabled title="Access denied: You do not have permission to add items">
                                    <i class="fas fa-lock me-1"></i>Tambah Attachment
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" disabled title="Access denied: You do not have permission to add items">
                                    <i class="fas fa-lock me-1"></i>Tambah Battery
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" disabled title="Access denied: You do not have permission to add items">
                                    <i class="fas fa-lock me-1"></i>Tambah Charger
                                </button>
                                <?php endif; ?>
                            </div>

                            <!-- Items Table -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm item-table" id="itemsTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">No</th>
                                            <th style="width: 15%;">Tipe Item</th>
                                            <th style="width: 55%;">Deskripsi</th>
                                            <th style="width: 15%;">Qty</th>
                                            <th style="width: 10%;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTableBody">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Belum ada item. Klik tombol "Tambah" untuk menambahkan item.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Hidden field untuk menyimpan items JSON -->
                            <input type="hidden" name="items_json" id="items_json" value="[]">
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="submit" form="unifiedPOForm" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan Purchase Order
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Item Details (Sub-Modal) -->
<div class="modal fade" id="itemDetailModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="itemModalTitle">Tambah Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="itemModalBody">
                <!-- Content will be loaded dynamically -->
                <div class="text-center p-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                    <p class="mt-3">Loading form...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveItemBtn">
                    <i class="fas fa-check me-2"></i>Tambahkan ke PO
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('javascript') ?>
<!-- Select2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script>
// Wait for jQuery to be loaded
(function() {
    'use strict';
    
    // Check if jQuery is loaded
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded!');
        return;
    }
    
    // Initialize PO system
    $(document).ready(function() {
        console.log('📋 Initializing Purchasing System...');
        
        // Check Bootstrap availability
        if (typeof bootstrap !== 'undefined') {
            console.log('✅ Bootstrap loaded successfully');
        } else {
            console.warn('Bootstrap not detected, modal functionality may be limited');
        }
        
        // Initialize Unit & Attachment PO DataTables
        initUnitAttachmentPOTable();
        initPODeliveryTable();
        initUnitAttachmentPOCompletedTable();
        
        // Initialize Create PO Modal (delay slightly to ensure DOM is ready)
        setTimeout(function() {
            initCreatePOModal();
        }, 100);
        
        // Global filter event handlers
        $('#globalFilterStatus, #globalFilterDateFrom, #globalFilterDateTo').on('change', function() {
            if (unitAttachmentPOTable) {
                unitAttachmentPOTable.ajax.reload();
            }
            if (unitAttachmentPOCompletedTable) {
                unitAttachmentPOCompletedTable.ajax.reload();
            }
        });
        
        // Tab switching event handlers - Following work_orders.php pattern
        // Initialize delivery table when delivery tab is first shown
        $('#delivery-tab').on('shown.bs.tab', function (e) {
            // Force reload delivery table
            if (poDeliveryTable) {
                poDeliveryTable.ajax.reload();
                // Adjust column sizing
                setTimeout(function() {
                    poDeliveryTable.columns.adjust();
                    poDeliveryTable.responsive.recalc();
                }, 100);
            }
            console.log('Delivery tab activated - reloading data');
        });
        
        // Also handle click event for delivery tab
        $('#delivery-tab').on('click', function(e) {
            // Small delay to ensure tab is fully shown
            setTimeout(function() {
                if ($('#delivery-tab').hasClass('active')) {
                    if (poDeliveryTable) {
                        poDeliveryTable.ajax.reload();
                        // Adjust column sizing
                        setTimeout(function() {
                            poDeliveryTable.columns.adjust();
                            poDeliveryTable.responsive.recalc();
                        }, 100);
                    }
                }
            }, 150);
        });

        // Initialize completed table when closed tab is first shown
        $('#completed-tab').on('shown.bs.tab', function (e) {
            // Force reload completed table
            if (unitAttachmentPOCompletedTable) {
                unitAttachmentPOCompletedTable.ajax.reload();
                // Adjust column sizing
                setTimeout(function() {
                    unitAttachmentPOCompletedTable.columns.adjust();
                    unitAttachmentPOCompletedTable.responsive.recalc();
                }, 100);
            }
            console.log('Completed tab activated - reloading data');
        });
        
        // Also handle click event for completed tab
        $('#completed-tab').on('click', function(e) {
            // Small delay to ensure tab is fully shown
            setTimeout(function() {
                if ($('#completed-tab').hasClass('active')) {
                    if (unitAttachmentPOCompletedTable) {
                        unitAttachmentPOCompletedTable.ajax.reload();
                        // Adjust column sizing
                        setTimeout(function() {
                            unitAttachmentPOCompletedTable.columns.adjust();
                            unitAttachmentPOCompletedTable.responsive.recalc();
                        }, 100);
                    }
                }
            }, 150);
        });

        // Ensure Progress tab is active on page load and reload progress table
        $(document).ready(function() {
            // Force Progress tab to be active
            $('#progres-tab').addClass('active').attr('aria-selected', 'true');
            $('#completed-tab').removeClass('active').attr('aria-selected', 'false');
            
            // Show Progress pane and hide Completed pane
            $('#progres-pane').addClass('show active');
            $('#completed-pane').removeClass('show active');
            
            // Reload progress table to ensure data is loaded
            setTimeout(function() {
                if (unitAttachmentPOTable) {
                    unitAttachmentPOTable.ajax.reload();
                }
            }, 100);
        });
    });
})();

        // Update count functions - Following work_orders.php pattern
        function updateProgresCount(count) {
            $('#progres-count').text(count);
        }

        function updateDeliveryCount(count) {
            $('#delivery-count').text(count);
        }

        // Function to refresh tables
// Debounce refreshTable to prevent multiple calls
let refreshTimeout;
function refreshTable() {
    clearTimeout(refreshTimeout);
    refreshTimeout = setTimeout(() => {
    if (unitAttachmentPOTable) {
        unitAttachmentPOTable.ajax.reload();
    }
        if (poDeliveryTable) {
            poDeliveryTable.ajax.reload();
        }
        if (unitAttachmentPOCompletedTable) {
            unitAttachmentPOCompletedTable.ajax.reload();
        }
    }, 100);
}

// Initialize Unit & Attachment PO DataTables
let unitAttachmentPOTable = null;
let poDeliveryTable = null;
let unitAttachmentPOCompletedTable = null;

function initUnitAttachmentPOTable() {
    unitAttachmentPOTable = $('#unitAttachmentPOTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('/purchasing/api/get-unified-po-data') ?>',
            type: 'POST',
            data: function(d) {
                d.po_type = 'unit_attachment'; // Filter for Unit & Attachment
                d.tab_type = 'progres'; // Filter for progres tab
                d.status = $('#globalFilterStatus').val();
                // supplier filter removed
                d.start_date = $('#globalFilterDateFrom').val();
                d.end_date = $('#globalFilterDateTo').val();
            }
        },
        columns: [
            { data: 'no_po', name: 'no_po' },
            { data: 'tanggal_po', name: 'tanggal_po' },
            { data: 'nama_supplier', name: 'nama_supplier' },
            { 
                data: 'status', 
                name: 'status',
                render: function(data) {
                    const badgeClass = getStatusBadgeClass(data);
                    const icon = getStatusIcon(data);
                    return `<span class="badge ${badgeClass}">
                        <i class="fas ${icon} me-1"></i>${data}
                    </span>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    // PROGRES TAB - Get item breakdown from new total columns
                    const totalUnit = parseInt(row.total_unit, 10) || 0;
                    const totalAttachment = parseInt(row.total_attachment, 10) || 0;
                    const totalBattery = parseInt(row.total_battery, 10) || 0;
                    const totalCharger = parseInt(row.total_charger, 10) || 0;
                    
                    if (totalUnit === 0 && totalAttachment === 0 && totalBattery === 0 && totalCharger === 0) {
                        return `<span class="text-muted small fst-italic">
                            <i class="fas fa-box-open me-1"></i>Tidak ada item
                        </span>`;
                    }
                    
                    // Create item badges with icons and colors
                    const itemBadges = [];
                    
                    if (totalUnit > 0) {
                        itemBadges.push(`
                            <span class="badge bg-primary me-1 mb-1">
                                <i class="fas fa-truck me-1"></i>${totalUnit} Unit
                            </span>
                        `);
                    }
                    
                    if (totalAttachment > 0) {
                        itemBadges.push(`
                            <span class="badge bg-info me-1 mb-1">
                                <i class="fas fa-puzzle-piece me-1"></i>${totalAttachment} Attachment
                            </span>
                        `);
                    }
                    
                    if (totalBattery > 0) {
                        itemBadges.push(`
                            <span class="badge bg-warning me-1 mb-1">
                                <i class="fas fa-battery-half me-1"></i>${totalBattery} Baterai
                            </span>
                        `);
                    }
                    
                    if (totalCharger > 0) {
                        itemBadges.push(`
                            <span class="badge bg-success me-1 mb-1">
                                <i class="fas fa-plug me-1"></i>${totalCharger} Charger
                            </span>
                        `);
                    }
                    
                    // Split badges into 2 rows for better layout
                    let row1 = itemBadges.slice(0, 2);
                    let row2 = itemBadges.slice(2);
                    
                    let result = `<div class="d-flex flex-column">`;
                    if (row1.length > 0) {
                        result += `<div class="d-flex flex-wrap">${row1.join('')}</div>`;
                    }
                    if (row2.length > 0) {
                        result += `<div class="d-flex flex-wrap">${row2.join('')}</div>`;
                    }
                    result += `</div>`;
                    
                    return result;
                }
            },
            { 
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    // Calculate verification progress based on warehouse verification status
                    const totalUnit = parseInt(row.total_unit, 10) || 0;
                    const totalAttachment = parseInt(row.total_attachment, 10) || 0;
                    const totalBattery = parseInt(row.total_battery, 10) || 0;
                    const totalCharger = parseInt(row.total_charger, 10) || 0;
                    
                    const totalItems = totalUnit + totalAttachment + totalBattery + totalCharger;
                    const verified = parseInt(row.total_qty_verified, 10) || 0;
                    
                    if (totalItems === 0) {
                        return `<span class="text-muted small fst-italic">-</span>`;
                    }

                    const percentage = totalItems > 0 ? Math.round((verified / totalItems) * 100) : 0;
                    const remaining = totalItems - verified;

                    return `
                        <div class="progress" title="Verifikasi: ${verified} dari ${totalItems} item (${remaining} tersisa)" style="height: 22px;">
                            <div class="progress-bar progress-bar-striped ${percentage >= 100 ? 'bg-success' : percentage > 0 ? 'bg-warning' : 'bg-secondary'}" role="progressbar" style="width: ${percentage}%;">
                                ${verified} / ${totalItems}
                            </div>
                        </div>
                        <small class="text-muted">${percentage}% terverifikasi</small>
                    `;
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    const totalDeliveries = parseInt(row.total_deliveries, 10) || 0;
                    const completedDeliveries = parseInt(row.completed_deliveries, 10) || 0;
                    const inTransitDeliveries = parseInt(row.in_transit_deliveries, 10) || 0;
                    const scheduledDeliveries = parseInt(row.scheduled_deliveries, 10) || 0;
                    
                    // Calculate total items for better understanding
                    const totalUnit = parseInt(row.total_unit, 10) || 0;
                    const totalAttachment = parseInt(row.total_attachment, 10) || 0;
                    const totalBattery = parseInt(row.total_battery, 10) || 0;
                    const totalCharger = parseInt(row.total_charger, 10) || 0;
                    const totalItems = totalUnit + totalAttachment + totalBattery + totalCharger;
                    const receivedItems = parseInt(row.total_qty_received, 10) || 0;
                    
                    if (totalDeliveries === 0) {
                        return `<span class="text-muted small">
                            <i class="fas fa-truck text-muted me-1"></i>Belum ada pengiriman
                        </span>`;
                    }
                    
                    // More descriptive status based on delivery state and verification status
                    let statusText = '';
                    let statusClass = '';
                    let progressBar = '';
                    
                    // Calculate delivered items (items that have been shipped, not necessarily verified)
                    const deliveredItems = receivedItems; // Items that reached warehouse and verified
                    const remainingItems = totalItems - deliveredItems;
                    
                    if (totalDeliveries === 0) {
                        // No deliveries created yet
                        statusText = `<span class="text-muted">
                            <i class="fas fa-truck text-muted me-1"></i>Belum ada pengiriman
                        </span>`;
                    } else if (completedDeliveries === totalDeliveries && deliveredItems >= totalItems) {
                        // All deliveries completed and all items received/verified
                        statusText = `<span class="text-success">
                            <i class="fas fa-check-circle me-1"></i>Semua Item Terkirim & Terverifikasi
                        </span>`;
                        progressBar = `<div class="progress mt-1" style="height: 16px;">
                            <div class="progress-bar bg-success" style="width: 100%;">100%</div>
                        </div>`;
                    } else if (deliveredItems > 0 && deliveredItems < totalItems) {
                        // Partial delivery - some items delivered and verified
                        const deliveredPercentage = Math.round((deliveredItems/totalItems)*100);
                        statusText = `<span class="text-warning">
                            <i class="fas fa-clock me-1"></i>Terkirim: ${deliveredItems}/${totalItems} item
                        </span>`;
                        progressBar = `<div class="progress mt-1" style="height: 16px;">
                            <div class="progress-bar bg-warning" style="width: ${deliveredPercentage}%;">
                                ${deliveredPercentage}%
                            </div>
                        </div>`;
                    } else if (totalDeliveries > 0 && deliveredItems === 0) {
                        // Deliveries created but no items received/verified yet
                        statusText = `<span class="text-info">
                            <i class="fas fa-truck me-1"></i>Dalam Pengiriman: ${totalDeliveries} pengiriman
                        </span>`;
                    } else if (scheduledDeliveries > 0 && deliveredItems === 0) {
                        // Scheduled but not yet started
                        statusText = `<span class="text-primary">
                            <i class="fas fa-calendar me-1"></i>Terjadwal: ${scheduledDeliveries} pengiriman
                        </span>`;
                    }
                    
                    return `
                        <div class="small">
                            <div class="mb-1">${statusText}</div>
                            ${progressBar}
                            <div class="text-muted mt-1">
                                <small>${completedDeliveries}/${totalDeliveries} pengiriman selesai</small>
                            </div>
                        </div>
                    `;
                }
            },
            { 
                data: 'id_po',
                name: 'id_po',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    const totalDeliveries = parseInt(row.total_deliveries, 10) || 0;
                    const completedDeliveries = parseInt(row.completed_deliveries, 10) || 0;
                    
                    // Use total_qty_ordered from backend (already calculated correctly)
                    const totalOrdered = parseInt(row.total_qty_ordered, 10) || 0;
                    // Use total_qty_received from backend (items in deliveries with status "Received")
                    const receivedItems = parseInt(row.total_qty_received, 10) || 0;
                    // Use total_qty_scheduled from backend (items already scheduled for delivery, regardless of status)
                    const scheduledItems = parseInt(row.total_qty_scheduled, 10) || 0;
                    
                    // Calculate remaining items that haven't been scheduled for delivery yet
                    const remainingItems = totalOrdered - scheduledItems;
                    
                    let actionButtons = '';
                    
                    // Dynamic action buttons based on status and progress
                    // Priority 1: Check if all items are already received
                    if (totalOrdered > 0 && receivedItems >= totalOrdered) {
                        // All items received - can mark as completed
                        if (row.status !== 'completed') {
                            actionButtons = `<button class="btn btn-sm btn-success" onclick="completePO(${data}, event)">
                                <i class="fas fa-check-circle me-1"></i>Tandai Selesai
                            </button>`;
                        } else {
                            actionButtons = `<span class="text-success small">
                                <i class="fas fa-check-circle me-1"></i>Selesai
                            </span>`;
                        }
                    } else if (totalOrdered > 0 && scheduledItems >= totalOrdered) {
                        // All items already scheduled for delivery (but not all received yet)
                        // Show delivery tracking button instead of "Buat Jadwal Pengiriman"
                        actionButtons = `<button class="btn btn-sm btn-warning" onclick="trackDeliveries(${data}, event)">
                            <i class="fas fa-truck me-1"></i>Lihat Detail Pengiriman
                        </button>`;
                    } else if (row.status === 'pending' && totalDeliveries === 0) {
                        // No deliveries yet - allow creating delivery schedule
                        actionButtons = `<button class="btn btn-sm btn-success" onclick="createDeliverySchedule(${data}, event)">
                            <i class="fas fa-calendar-plus me-1"></i>Buat Jadwal Pengiriman
                        </button>`;
                    } else if (remainingItems > 0) {
                        // Has remaining items that haven't been scheduled - can create more deliveries
                        const nextDeliverySequence = totalDeliveries + 1;
                        actionButtons = `<button class="btn btn-sm btn-success" onclick="createDeliverySchedule(${data}, event)">
                            <i class="fas fa-calendar-plus me-1"></i>Buat Jadwal Pengiriman (${nextDeliverySequence})
                        </button>`;
                    } else if (totalDeliveries > 0 && completedDeliveries < totalDeliveries) {
                        // Has deliveries but not all completed - show delivery tracking
                        actionButtons = `<button class="btn btn-sm btn-warning" onclick="trackDeliveries(${data}, event)">
                            <i class="fas fa-truck me-1"></i>Lihat Detail Pengiriman
                        </button>`;
                    } else if (row.status === 'Selesai dengan Catatan') {
                        // Special actions for partial rejection
                        actionButtons = `
                            <button class="btn btn-sm btn-warning" onclick="reverifyPO(${data}, event)">
                                <i class="fas fa-sync-alt me-1"></i>Verifikasi Ulang
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="cancelPO(${data}, event)">
                                <i class="fas fa-ban me-1"></i>Batalkan
                            </button>
                        `;
                    } else {
                        // Default - just show status indicator
                        actionButtons = `<span class="text-success small">
                            <i class="fas fa-check-circle me-1"></i>Selesai
                        </span>`;
                    }
                    
                    return actionButtons;
                }
            }
        ],
        createdRow: function(row, data, dataIndex) {
            // Make row clickable
            $(row).on('click', function(e) {
                // Don't trigger if clicking on action buttons
                if (!$(e.target).closest('.btn, .dropdown').length) {
                    viewPODetail(data.id_po, e);
                }
            });
        },
        order: [[1, 'desc']],
        pageLength: 10,
        responsive: true,
        autoWidth: false,
        scrollX: false,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
        },
        drawCallback: function(settings) {
            // Update progress tab badge count based on filtered records
            if (settings && settings.json) {
                updateProgresCount(settings.json.recordsFiltered || settings.json.recordsTotal || 0);
            }
            // Ensure table is properly displayed after drawing
            $(this.api().table().container()).css('width', '100%');
        }
    });
}

// Initialize PO Delivery DataTable
function initPODeliveryTable() {
    poDeliveryTable = $('#poDeliveryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('/purchasing/api/get-delivery-data') ?>',
            type: 'POST',
            data: function(d) {
                d.status = $('#globalFilterStatus').val();
                // supplier filter removed
                d.start_date = $('#globalFilterDateFrom').val();
                d.end_date = $('#globalFilterDateTo').val();
            }
        },
        columns: [
            { data: 'packing_list_no', name: 'packing_list_no' },
            { data: 'no_po', name: 'no_po' },
            { data: 'nama_supplier', name: 'nama_supplier' },
            { data: 'delivery_date', name: 'delivery_date' },
            { data: 'driver_name', name: 'driver_name' },
            { 
                data: 'total_items', 
                name: 'total_items',
                render: function(data, type, row) {
                    // Display items from selected delivery items (serial_numbers)
                    let selectedItems = [];
                    if (row.serial_numbers) {
                        try {
                            const parsed = JSON.parse(row.serial_numbers);
                            // Ensure it's an array
                            selectedItems = Array.isArray(parsed) ? parsed : [];
                        } catch (e) {
                            console.error('Error parsing serial_numbers:', e);
                            selectedItems = [];
                        }
                    }
                    
                    if (!Array.isArray(selectedItems) || selectedItems.length === 0) {
                        return `<span class="text-muted small fst-italic">
                            <i class="fas fa-box-open me-1"></i>Tidak ada item
                        </span>`;
                    }
                    
                    // Count items by type
                    const itemCounts = {
                        unit: 0,
                        attachment: 0,
                        battery: 0,
                        charger: 0
                    };
                    
                    selectedItems.forEach(item => {
                        if (item && itemCounts.hasOwnProperty(item.type)) {
                            itemCounts[item.type] += parseInt(item.qty) || 0;
                        }
                    });
                    
                    const itemBadges = [];
                    if (itemCounts.unit > 0) {
                        itemBadges.push(`
                            <span class="badge bg-primary me-1 mb-1">
                                <i class="fas fa-truck me-1"></i>${itemCounts.unit} Unit
                            </span>
                        `);
                    }
                    if (itemCounts.attachment > 0) {
                        itemBadges.push(`
                            <span class="badge bg-info me-1 mb-1">
                                <i class="fas fa-puzzle-piece me-1"></i>${itemCounts.attachment} Attachment
                            </span>
                        `);
                    }
                    if (itemCounts.battery > 0) {
                        itemBadges.push(`
                            <span class="badge bg-warning me-1 mb-1">
                                <i class="fas fa-battery-half me-1"></i>${itemCounts.battery} Baterai
                            </span>
                        `);
                    }
                    if (itemCounts.charger > 0) {
                        itemBadges.push(`
                            <span class="badge bg-success me-1 mb-1">
                                <i class="fas fa-plug me-1"></i>${itemCounts.charger} Charger
                            </span>
                        `);
                    }
                    // Split badges into 2 rows for better layout
                    let row1 = itemBadges.slice(0, 2);
                    let row2 = itemBadges.slice(2);
                    
                    let result = `<div class="d-flex flex-column">`;
                    if (row1.length > 0) {
                        result += `<div class="d-flex flex-wrap">${row1.join('')}</div>`;
                    }
                    if (row2.length > 0) {
                        result += `<div class="d-flex flex-wrap">${row2.join('')}</div>`;
                    }
                    result += `</div>`;
                    
                    return result;
                }
            },
            { 
                data: 'status', 
                name: 'status',
                render: function(data) {
                    const badgeClass = getDeliveryStatusBadgeClass(data);
                    const icon = getDeliveryStatusIcon(data);
                    return `<span class="badge ${badgeClass}">
                        <i class="fas ${icon} me-1"></i>${data}
                    </span>`;
                }
            },
            { 
                data: 'id_delivery',
                name: 'id_delivery',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    // Action buttons based on status (similar to delivery.php)
                    let actionButtons = '';
                    
                    if (row.status === 'Scheduled') {
                        actionButtons = `
                            <button class="btn btn-sm btn-warning" onclick="assignSerialNumbers(${data})" title="Assign Serial Numbers">
                                <i class="fas fa-barcode me-1"></i>Assign SN
                            </button>
                        `;
                    } else if (row.status === 'In Transit') {
                        actionButtons = `
                            <button class="btn btn-sm btn-success" onclick="markAsReceived(${data})" title="Mark as Received">
                                <i class="fas fa-check-circle me-1"></i>Received
                            </button>
                        `;
                    } else if (row.status === 'Received') {
                        actionButtons = `
                            <span class="text-info small">
                                <i class="fas fa-clock me-1"></i>Dalam Verifikasi
                            </span>
                        `;
                    } else if (row.status === 'Completed') {
                        actionButtons = `
                            <span class="text-success">
                                <i class="fas fa-check-circle"></i> Completed
                            </span>
                        `;
                    }
                    
                    return `<div class="btn-group" role="group">${actionButtons}</div>`;
                }
            },
            {
                data: 'id_delivery',
                name: 'id_delivery',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    const packingListNo = row.packing_list_no || '';
                    return `
                        <button class="btn btn-sm btn-primary" onclick="printPackingList(${data}, '${packingListNo}', event)" title="Print Packing List">
                            <i class="fas fa-print me-1"></i>Print
                        </button>
                    `;
                }
            }
        ],
        order: [[3, 'desc']], // Order by delivery_date descending
        pageLength: 10,
        responsive: true,
        autoWidth: false,
        scrollX: false,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
        },
        drawCallback: function(settings) {
            // Update delivery count
            updateDeliveryCount(settings.json.recordsFiltered || 0);
            // Ensure table is properly displayed after drawing
            $(this.api().table().container()).css('width', '100%');
        },
        createdRow: function(row, data, dataIndex) {
            // Make row clickable
            $(row).on('click', function(e) {
                // Don't trigger if clicking on action buttons
                if (!$(e.target).closest('.btn, .dropdown').length) {
                    viewDeliveryDetail(data.id_delivery, e);
                }
            });
        }
    });
}

// Initialize Completed PO DataTable
function initUnitAttachmentPOCompletedTable() {
    unitAttachmentPOCompletedTable = $('#unitAttachmentPOCompletedTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('/purchasing/api/get-unified-po-data') ?>',
            type: 'POST',
            data: function(d) {
                d.po_type = 'unit_attachment'; // Filter for Unit & Attachment
                d.tab_type = 'completed'; // Filter for completed tab
                d.status = $('#globalFilterStatus').val();
                // supplier filter removed
                d.start_date = $('#globalFilterDateFrom').val();
                d.end_date = $('#globalFilterDateTo').val();
            }
        },
        columns: [
            { data: 'no_po', name: 'no_po' },
            { data: 'tanggal_po', name: 'tanggal_po' },
            { data: 'nama_supplier', name: 'nama_supplier' },
            { 
                data: 'status', 
                name: 'status',
                render: function(data) {
                    const badgeClass = getStatusBadgeClass(data);
                    const icon = getStatusIcon(data);
                    return `<span class="badge ${badgeClass}">
                        <i class="fas ${icon} me-1"></i>${data}
                    </span>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    // COMPLETED TAB - Get item breakdown from new total columns
                    const totalUnit = parseInt(row.total_unit, 10) || 0;
                    const totalAttachment = parseInt(row.total_attachment, 10) || 0;
                    const totalBattery = parseInt(row.total_battery, 10) || 0;
                    const totalCharger = parseInt(row.total_charger, 10) || 0;
                    
                    if (totalUnit === 0 && totalAttachment === 0 && totalBattery === 0 && totalCharger === 0) {
                        return `<span class="text-muted small fst-italic">
                            <i class="fas fa-box-open me-1"></i>Tidak ada item
                        </span>`;
                    }
                    
                    // Create item badges with icons and colors for completed items
                    const itemBadges = [];
                    
                    if (totalUnit > 0) {
                        itemBadges.push(`
                            <span class="badge bg-primary me-1 mb-1">
                                <i class="fas fa-truck me-1"></i>${totalUnit} Unit
                            </span>
                        `);
                    }
                    
                    if (totalAttachment > 0) {
                        itemBadges.push(`
                            <span class="badge bg-info me-1 mb-1">
                                <i class="fas fa-puzzle-piece me-1"></i>${totalAttachment} Attachment
                            </span>
                        `);
                    }
                    
                    if (totalBattery > 0) {
                        itemBadges.push(`
                            <span class="badge bg-warning me-1 mb-1">
                                <i class="fas fa-battery-half me-1"></i>${totalBattery} Baterai
                            </span>
                        `);
                    }
                    
                    if (totalCharger > 0) {
                        itemBadges.push(`
                            <span class="badge bg-success me-1 mb-1">
                                <i class="fas fa-plug me-1"></i>${totalCharger} Charger
                            </span>
                        `);
                    }
                    
                    // Split badges into 2 rows for better layout
                    let row1 = itemBadges.slice(0, 2);
                    let row2 = itemBadges.slice(2);
                    
                    let result = `<div class="d-flex flex-column">`;
                    if (row1.length > 0) {
                        result += `<div class="d-flex flex-wrap">${row1.join('')}</div>`;
                    }
                    if (row2.length > 0) {
                        result += `<div class="d-flex flex-wrap">${row2.join('')}</div>`;
                    }
                    result += `</div>`;
                    
                    return result;
                }
            },
            { 
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    const totalUnit = parseInt(row.total_unit, 10) || 0;
                    const totalAttachment = parseInt(row.total_attachment, 10) || 0;
                    const totalBattery = parseInt(row.total_battery, 10) || 0;
                    const totalCharger = parseInt(row.total_charger, 10) || 0;
                    
                    const totalItems = totalUnit + totalAttachment + totalBattery + totalCharger;
                    const verified = parseInt(row.total_qty_verified, 10) || 0;
                    
                    if (totalItems === 0) {
                        return `<span class="text-muted small fst-italic">-</span>`;
                    }

                    // For completed items, show 100% with same styling as progres tab
                    const percentage = 100;
                    return `
                        <div class="progress" title="Verifikasi selesai: ${verified} dari ${totalItems} item" style="height: 22px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: ${percentage}%;">
                                ${verified} / ${totalItems}
                            </div>
                        </div>
                        <small class="text-muted">${percentage}% terverifikasi</small>
                    `;
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    const totalDeliveries = parseInt(row.total_deliveries, 10) || 0;
                    const completedDeliveries = parseInt(row.completed_deliveries, 10) || 0;
                    
                    // Calculate total items for better understanding
                    const totalUnit = parseInt(row.total_unit, 10) || 0;
                    const totalAttachment = parseInt(row.total_attachment, 10) || 0;
                    const totalBattery = parseInt(row.total_battery, 10) || 0;
                    const totalCharger = parseInt(row.total_charger, 10) || 0;
                    const totalItems = totalUnit + totalAttachment + totalBattery + totalCharger;
                    const receivedItems = parseInt(row.total_qty_received, 10) || 0;
                    
                    if (totalDeliveries === 0) {
                        return `<span class="text-muted small">
                            <i class="fas fa-truck text-muted me-1"></i>Belum ada pengiriman
                        </span>`;
                    }
                    
                    // For completed items, show all delivered and verified
                    const statusText = `<span class="text-success">
                        <i class="fas fa-check-circle me-1"></i>Semua Item Terkirim & Terverifikasi
                    </span>`;
                    const progressBar = `<div class="progress mt-1" style="height: 16px;">
                        <div class="progress-bar bg-success" style="width: 100%;">100%</div>
                    </div>`;
                    
                    return `
                        <div class="small">
                            <div class="mb-1">${statusText}</div>
                            ${progressBar}
                            <div class="text-muted mt-1">
                                <small>${completedDeliveries}/${totalDeliveries} pengiriman selesai</small>
                            </div>
                        </div>
                    `;
                }
            },
            { 
                data: 'id_po',
                name: 'id_po',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    const totalDeliveries = parseInt(row.total_deliveries, 10) || 0;
                    const completedDeliveries = parseInt(row.completed_deliveries, 10) || 0;
                    const totalItems = parseInt(row.total_qty_ordered, 10) || 0;
                    const receivedItems = parseInt(row.total_qty_received, 10) || 0;
                    
                    let actionButtons = '';
                    
                    // For completed items, show status instead of buttons
                    if (row.status === 'completed' || receivedItems >= totalItems) {
                        // All items received and verified - show status
                        actionButtons = `<span class="text-success small">
                            <i class="fas fa-check-circle me-1"></i>Completed
                        </span>`;
                    } else if (row.status === 'Selesai dengan Catatan') {
                        // Special actions for partial rejection
                        <?php if (can_edit('purchasing')): ?>
                        actionButtons = `
                            <button class="btn btn-sm btn-warning" onclick="reverifyPO(${data}, event)">
                                <i class="fas fa-redo me-1"></i>Re-verify
                            </button>
                        `;
                        <?php else: ?>
                        actionButtons = `
                            <button class="btn btn-sm btn-secondary" disabled title="Access denied: You do not have permission to re-verify PO">
                                <i class="fas fa-lock me-1"></i>Re-verify
                            </button>
                        `;
                        <?php endif; ?>
                    } else {
                        // Default status for completed tab
                        actionButtons = `<span class="text-success small">
                            <i class="fas fa-check-circle me-1"></i>Completed
                        </span>`;
                    }
                    
                    return actionButtons;
                }
            }
        ],
        createdRow: function(row, data, dataIndex) {
            // Make row clickable
            $(row).on('click', function(e) {
                // Don't trigger if clicking on action buttons
                if (!$(e.target).closest('.btn, .dropdown').length) {
                    viewPODetail(data.id_po, e);
                }
            });
        },
        order: [[1, 'desc']],
        pageLength: 10,
        responsive: true,
        autoWidth: false,
        scrollX: false,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
        },
        drawCallback: function(settings) {
            // Ensure table is properly displayed after drawing
            $(this.api().table().container()).css('width', '100%');
        }
    });
}

// Utility functions
function getStatusBadgeClass(status) {
    const classes = {
        'pending': 'bg-warning text-dark',
        'approved': 'bg-info text-white',
        'completed': 'bg-success text-white',
        'Selesai dengan Catatan': 'bg-primary text-white',
        'cancelled': 'bg-danger text-white',
        'Pending': 'bg-warning text-dark',
        'Approved': 'bg-info text-white',
        'Completed': 'bg-success text-white',
        'Cancelled': 'bg-danger text-white'
    };
    return classes[status] || 'bg-secondary text-white';
}

function getStatusIcon(status) {
    const icons = {
        'pending': 'fa-clock',
        'approved': 'fa-check-circle',
        'completed': 'fa-check-double',
        'Selesai dengan Catatan': 'fa-exclamation-circle',
        'cancelled': 'fa-times-circle',
        'Pending': 'fa-clock',
        'Approved': 'fa-check-circle',
        'Completed': 'fa-check-double',
        'Cancelled': 'fa-times-circle'
    };
    return icons[status] || 'fa-question-circle';
}

function getDeliveryStatusBadgeClass(status) {
    const classes = {
        'Not Started': 'bg-secondary',
        'Partial': 'bg-warning',
        'Complete': 'bg-success',
        'Over Delivered': 'bg-danger'
    };
    return classes[status] || 'bg-secondary';
}

// Action functions
let currentPOId = null;
let currentDeliveryId = null;

function viewPODetail(poId, event) {
    if (event) event.preventDefault();
    if (typeof $ === 'undefined') return;
    
    currentPOId = poId;
    
    // Show modal with loading state
    $('#viewPOModal').modal('show');
    $('#poLoadingState').show();
    $('#poContent').hide();
    
    // Reset tabs to default state - scope to modal only
    const modalContainer = $('#viewPOModal');
    modalContainer.find('#items-tab').addClass('active').attr('aria-selected', 'true');
    modalContainer.find('#deliveries-tab').removeClass('active').attr('aria-selected', 'false');
    modalContainer.find('#items-pane').addClass('show active');
    modalContainer.find('#deliveries-pane').removeClass('show active');
    
    $.ajax({
        type: 'GET',
        url: '<?= base_url('/purchasing/api/po-detail/') ?>' + poId,
        success: function(response) {
            if (response.success) {
                renderPODetailNew(response.data);
        $('#poLoadingState').hide();
        $('#poContent').show();
        
        // Show print button
        $('#printPOBtn').show();
        
        // Initialize tab switching
        initializePODetailTabs();
        
        // Hide print button when modal is closed
        $('#viewPOModal').on('hidden.bs.modal', function() {
            $('#printPOBtn').hide();
        });
            } else {
                $('#poLoadingState').html(`
                    <div class="text-center text-danger p-5">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <h5 class="mt-3">Gagal memuat data</h5>
                        <p class="text-muted">${response.message || 'Terjadi kesalahan saat memuat detail PO'}</p>
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading PO detail:', error);
            $('#poLoadingState').html(`
                <div class="text-center text-danger p-5">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h5 class="mt-3">Gagal memuat data</h5>
                    <p class="text-muted">Terjadi kesalahan saat memuat detail PO</p>
                </div>
            `);
        }
    });
}

function printPO(poId) {
    if (typeof $ === 'undefined') return;
    
    // Open print window
    const printUrl = '<?= base_url('/purchasing/print_po/') ?>' + poId;
    window.open(printUrl, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
}

function printPOFromModal() {
    if (currentPOId) {
        printPO(currentPOId);
    }
}

function renderPODetailNew(data) {
    if (typeof $ === 'undefined') return;
    
    const po = data.po;
    const items = data.items || [];
    const deliveries = data.deliveries || [];
    const deliveryItems = data.delivery_items || {};
    const summary = data.summary || {};
    
        // Update summary cards with badge format
        const itemTypeBreakdown = summary.item_type_breakdown || {};
        let breakdownHtml = '';
        Object.keys(itemTypeBreakdown).forEach(type => {
            const count = itemTypeBreakdown[type];
            const badgeClass = getItemTypeBadgeClass(type);
            breakdownHtml += `<span class="badge ${badgeClass} me-1">${type} ${count}</span>`;
        });
        $('#totalItemsOrdered').html(breakdownHtml || '<span class="text-muted">0 items</span>');
        
        $('#totalItemsReceived').text(summary.total_items_received || 0);
        $('#deliveryProgress').text(`${summary.completed_deliveries || 0}/${summary.total_deliveries || 0}`);
        $('#verifiedItems').text(summary.verified_items || 0);
    
    // Update PO information
    $('#poNumber').text(po.no_po || '-');
    $('#poDate').text(po.tanggal_po ? new Date(po.tanggal_po).toLocaleDateString('id-ID') : '-');
    $('#poSupplier').text(po.nama_supplier || '-');
    $('#poContact').text(po.kontak_supplier || '-');
    $('#poInvoice').text(po.invoice_no || '-');
    
    // Update status with badge
    const statusBadge = getStatusBadge(po.status);
    $('#poStatus').html(statusBadge);
    
    // Breakdown section removed for simplicity
    
    // Update items tab
    $('#itemsCount').text(items.length);
    renderItemsTable(items, data.summary, deliveries);
    
    // Update deliveries tab
    $('#deliveriesCount').text(deliveries.length);
    renderDeliveriesContent(deliveries, deliveryItems);
}

function getVerificationStatusBadge(status) {
    const statusMap = {
        'Sesuai': 'bg-success',
        'Tidak Sesuai': 'bg-danger', 
        'Belum Dicek': 'bg-secondary',
        'Pending': 'bg-warning'
    };
    
    const textMap = {
        'Sesuai': 'Sesuai',
        'Tidak Sesuai': 'Tidak Sesuai',
        'Belum Dicek': 'Belum Dicek',
        'Pending': 'Pending'
    };
    
    const badgeClass = statusMap[status] || 'bg-secondary';
    const badgeText = textMap[status] || status || 'Unknown';
    
    return `<span class="badge ${badgeClass}">${badgeText}</span>`;
}

// Render Serial Numbers in 2-column format with collapse/expand
function renderSerialNumbers(items, specId) {
    if (!items || items.length === 0) {
        return '';
    }
    
    // Collect all serial numbers from items
    const serialNumbers = [];
    items.forEach((item, index) => {
        // Get serial number based on item type
        let sn = null;
        if (item.item_type === 'Unit') {
            sn = item.serial_number_po || item.serial_number || null;
        } else {
            sn = item.serial_number || null;
        }
        serialNumbers.push({
            index: index + 1,
            sn: sn || ''
        });
    });
    
    // Generate HTML for 2-column layout
    let serialNumbersHtml = '';
    const totalItems = serialNumbers.length;
    const rows = Math.ceil(totalItems / 2);
    
    for (let i = 0; i < rows; i++) {
        const col1Index = i * 2;
        const col2Index = i * 2 + 1;
        
        const item1 = serialNumbers[col1Index];
        const item2 = col2Index < totalItems ? serialNumbers[col2Index] : null;
        
        serialNumbersHtml += `
            <div class="row mb-2">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-light text-dark me-2" style="min-width: 35px; text-align: center;">${item1.index}</span>
                        <code class="flex-grow-1 ${item1.sn ? 'text-success' : 'text-muted fst-italic'}" style="font-size: 0.9em;">${item1.sn || 'Belum ada SN'}</code>
                    </div>
                </div>
                ${item2 ? `
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-light text-dark me-2" style="min-width: 35px; text-align: center;">${item2.index}</span>
                        <code class="flex-grow-1 ${item2.sn ? 'text-success' : 'text-muted fst-italic'}" style="font-size: 0.9em;">${item2.sn || 'Belum ada SN'}</code>
                    </div>
                </div>
                ` : '<div class="col-md-6"></div>'}
            </div>
        `;
    }
    
    // Use specId for unique collapse ID
    const collapseId = 'snCollapse_' + specId.replace(/[^a-zA-Z0-9]/g, '_');
    
    return `
        <div class="mt-3 pt-3 border-top">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0 text-warning">
                    <i class="fas fa-barcode me-2"></i>Serial Number:
                </h6>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="false" aria-controls="${collapseId}" onclick="toggleSNIcon('${collapseId}')">
                    <i class="fas fa-chevron-down" id="icon-${collapseId}"></i>
                </button>
            </div>
            <div class="collapse" id="${collapseId}">
                <div class="p-2" style="background: #f8f9fa; border-radius: 4px; border: 1px solid #e9ecef;">
                    ${serialNumbersHtml}
                </div>
            </div>
        </div>
    `;
}

// Toggle SN icon when collapse is toggled
function toggleSNIcon(collapseId) {
    setTimeout(() => {
        const collapseEl = document.getElementById(collapseId);
        const iconEl = document.getElementById('icon-' + collapseId);
        if (collapseEl && iconEl) {
            if (collapseEl.classList.contains('show')) {
                iconEl.classList.remove('fa-chevron-down');
                iconEl.classList.add('fa-chevron-up');
            } else {
                iconEl.classList.remove('fa-chevron-up');
                iconEl.classList.add('fa-chevron-down');
            }
        }
    }, 100);
}

function getPackingListInfo(deliveries, itemType) {
    if (!deliveries || deliveries.length === 0) {
        return '<span class="text-muted">Belum ada packing list</span>';
    }
    
    let packingInfo = '';
    deliveries.forEach(delivery => {
        if (delivery.serial_numbers) {
            try {
                const serialData = JSON.parse(delivery.serial_numbers);
                const itemCount = serialData.filter(item => 
                    item.type && item.type.toLowerCase() === itemType
                ).reduce((sum, item) => sum + (parseInt(item.qty) || 0), 0);
                
                if (itemCount > 0) {
                    packingInfo += `
                        <div class="mb-1">
                            <span class="badge bg-light text-dark me-2">${delivery.packing_list_no || 'N/A'}</span>
                            <small class="text-muted">: ${itemCount} (${itemType} yang dikirim di packing list ini)</small>
                        </div>
                    `;
                }
            } catch (e) {
                console.error('Error parsing serial_numbers:', e);
            }
        }
    });
    
    return packingInfo || '<span class="text-muted">Belum ada packing list</span>';
}

// Render specification details based on item type
function renderSpecificationDetails(item, itemType) {
    const type = (itemType || item.item_type || 'Unit').toLowerCase();
    
    if (type === 'unit') {
        // Unit specifications
        return `
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-2">
                        <strong>Departemen:</strong> ${item.nama_departemen || '-'}
                    </div>
                    <div class="mb-2">
                        <strong>Jenis Unit:</strong> ${item.jenis_unit || '-'}
                    </div>
                    <div class="mb-2">
                        <strong>Brand:</strong> ${item.merk_unit || '-'}
                    </div>
                    <div class="mb-2">
                        <strong>Model:</strong> ${item.model_unit || '-'}
                    </div>
                    <div class="mb-2">
                        <strong>Tahun:</strong> ${item.tahun_po || '-'}
                    </div>
                    <div class="mb-2">
                        <strong>Kapasitas:</strong> ${item.kapasitas_unit || '-'}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <strong>Mast Type:</strong> ${item.tipe_mast || '-'}
                    </div>
                    <div class="mb-2">
                        <strong>Engine Type:</strong> ${item.merk_mesin || '-'}
                    </div>
                    <div class="mb-2">
                        <strong>Tire Type:</strong> ${item.tipe_ban || '-'}
                    </div>
                    <div class="mb-2">
                        <strong>Wheel Type:</strong> ${item.tipe_roda || '-'}
                    </div>
                    <div class="mb-2">
                        <strong>Valve:</strong> ${item.jumlah_valve || '-'}
                    </div>
                    <div class="mb-2">
                        <strong>Keterangan:</strong> ${item.keterangan || '-'}
                    </div>
                </div>
            </div>
        `;
    } else if (type === 'attachment') {
        // Attachment specifications
        return `
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-2">
                        <strong>Tipe Attachment:</strong> ${item.tipe_attachment || '-'}
                    </div>
                    <div class="mb-2">
                        <strong>Merk:</strong> ${item.merk_attachment || '-'}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <strong>Model:</strong> ${item.model_attachment || '-'}
                    </div>
                    <div class="mb-2">
                        <strong>Keterangan:</strong> ${item.keterangan || '-'}
                    </div>
                </div>
            </div>
        `;
    } else if (type === 'battery') {
        // Battery specifications
        return `
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-2">
                        <strong>Jenis Battery:</strong> ${item.jenis_baterai || '-'}
                    </div>
                    <div class="mb-2">
                        <strong>Merk Battery:</strong> ${item.merk_baterai || '-'}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <strong>Tipe Battery:</strong> ${item.tipe_baterai || '-'}
                    </div>
                    <div class="mb-2">
                        <strong>Keterangan:</strong> ${item.keterangan || '-'}
                    </div>
                </div>
            </div>
        `;
    } else if (type === 'charger') {
        // Charger specifications
        return `
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-2">
                        <strong>Merk Charger:</strong> ${item.merk_charger || '-'}
                    </div>
                    <div class="mb-2">
                        <strong>Tipe Charger:</strong> ${item.tipe_charger || '-'}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <strong>Keterangan:</strong> ${item.keterangan || '-'}
                    </div>
                </div>
            </div>
        `;
    } else {
        // Default/Unknown type - show generic info
        return `
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-2">
                        <strong>Item Name:</strong> ${item.item_name || '-'}
                    </div>
                    <div class="mb-2">
                        <strong>Keterangan:</strong> ${item.keterangan || '-'}
                    </div>
                </div>
            </div>
        `;
    }
}

function renderItemsTable(items, summary = null, deliveries = []) {
    // Group items by specification (item_name), not by type
    const groupedBySpec = {};
    items.forEach(item => {
        const spec = item.item_name || 'Unknown';
        if (!groupedBySpec[spec]) {
            groupedBySpec[spec] = [];
        }
        groupedBySpec[spec].push(item);
    });
    
    // Get delivered count by type from summary
    const deliveredByType = summary ? summary.delivered_by_type || {} : {};
    
    let itemsHtml = '';
    
    if (Object.keys(groupedBySpec).length === 0) {
        itemsHtml = '<div class="text-center p-4"><i class="fas fa-box-open fa-2x text-muted mb-3"></i><p class="text-muted">Tidak ada item</p></div>';
    } else {
        // Create dropdown for each unique specification
        Object.keys(groupedBySpec).forEach((spec, specIndex) => {
            const specItems = groupedBySpec[spec];
            const totalOrdered = specItems.length;
            
            // Get item type for badge and delivered count
            const itemType = specItems[0].item_type || 'Unit';
            const itemTypeLower = itemType.toLowerCase();
            const totalDelivered = deliveredByType[itemTypeLower] || 0;
            
            const badgeClass = getItemTypeBadgeClass(itemType);
            const typeIcon = getItemTypeIcon(itemType);
            
            itemsHtml += `
                <div class="mb-3" style="border-radius: 8px; border: 1px solid #e9ecef; background: #f8f9fa;">
                    <div class="p-3" style="cursor: pointer; border-radius: 8px;" onclick="toggleSpecGroup('${spec.replace(/[^a-zA-Z0-9]/g, '_')}')">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-chevron-down me-2" id="icon-${spec.replace(/[^a-zA-Z0-9]/g, '_')}" style="color: #6c757d;"></i>
                                <i class="${typeIcon} me-2" style="color: #007bff;"></i>
                                <strong>${spec}</strong>
                                <span class="badge ${badgeClass} ms-2">${specItems.length} ${itemType}</span>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">
                                    Sudah Dikirim: ${totalDelivered}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="collapse" id="group-${spec.replace(/[^a-zA-Z0-9]/g, '_')}">
                        <div class="px-3 pb-3">
                            <div class="border-top pt-3">
                                <div class="p-3" style="background: white; border-radius: 6px; border: 1px solid #e9ecef;">
                                    <h6 class="mb-3 text-primary">Spesifikasi Detail:</h6>
                                    ${renderSpecificationDetails(specItems[0], itemType)}
                                    
                                    <!-- Serial Numbers Section -->
                                    ${renderSerialNumbers(specItems, spec.replace(/[^a-zA-Z0-9]/g, '_'))}
                                    
                                    <!-- Packing List Information -->
                                    <div class="mt-3 pt-3 border-top">
                                        <h6 class="mb-2 text-info">
                                            <i class="fas fa-box me-2"></i>Packing List terkait:
                                        </h6>
                                        ${getPackingListInfo(deliveries, itemTypeLower)}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    $('#poItemsContent').html(itemsHtml);
}

// Toggle specification group visibility
function toggleSpecGroup(specId) {
    const group = document.getElementById(`group-${specId}`);
    const icon = document.getElementById(`icon-${specId}`);
    
    if (group.classList.contains('show')) {
        group.classList.remove('show');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    } else {
        group.classList.add('show');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    }
}

function renderDeliveriesContent(deliveries, deliveryItems) {
    let deliveriesHtml = '';
    
    if (deliveries.length === 0) {
        deliveriesHtml = `
            <div class="text-center p-4">
                <i class="fas fa-truck fa-2x text-muted mb-3"></i>
                <h6 class="text-muted">Belum ada pengiriman</h6>
                <p class="text-muted small">Pengiriman akan muncul setelah dibuat</p>
                <?php if (can_edit('purchasing')): ?>
                <button class="btn btn-primary btn-sm" onclick="createDelivery()">
                    <i class="fas fa-plus me-1"></i>Buat Pengiriman
                </button>
                <?php else: ?>
                <button class="btn btn-secondary btn-sm" disabled title="Access denied: You do not have permission to create delivery">
                    <i class="fas fa-lock me-1"></i>Buat Pengiriman
                </button>
                <?php endif; ?>
            </div>
        `;
    } else {
        deliveries.forEach((delivery, index) => {
            const statusBadge = getDeliveryStatusBadge(delivery.status);
            const itemsInDelivery = deliveryItems[delivery.id_delivery] || [];
            
            // Icon based on status
            let statusIcon = 'fa-clock';
            if (delivery.status === 'In Transit') statusIcon = 'fa-truck';
            else if (delivery.status === 'Received') statusIcon = 'fa-check-circle';
            else if (delivery.status === 'Cancelled') statusIcon = 'fa-times-circle';
            
            deliveriesHtml += `
                <div class="card mb-3 border-start border-4 ${delivery.status === 'Received' ? 'border-success' : (delivery.status === 'In Transit' ? 'border-warning' : 'border-secondary')}">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">
                                <i class="fas ${statusIcon} me-2"></i>
                                Pengiriman #${delivery.delivery_sequence || index + 1}
                                ${delivery.packing_list_no ? ` - ${delivery.packing_list_no}` : ''}
                            </h6>
                            <small class="text-muted">
                                ${delivery.expected_date ? 'Expected: ' + new Date(delivery.expected_date).toLocaleDateString('id-ID') : ''}
                                ${delivery.actual_date ? ' | Received: ' + new Date(delivery.actual_date).toLocaleDateString('id-ID') : ''}
                            </small>
                        </div>
                        <div>
                            ${statusBadge}
                            <span class="badge bg-info ms-2">${itemsInDelivery.length} items</span>
                        </div>
                    </div>
                    <div class="card-body">
                        ${delivery.driver_name || delivery.vehicle_info ? `
                            <div class="row mb-3">
                                ${delivery.driver_name ? `
                                    <div class="col-md-6">
                                        <strong>Driver:</strong> ${delivery.driver_name}
                                        ${delivery.driver_phone ? `<br><small class="text-muted">${delivery.driver_phone}</small>` : ''}
                                    </div>
                                ` : ''}
                                ${delivery.vehicle_info ? `
                                    <div class="col-md-6">
                                        <strong>Vehicle:</strong> ${delivery.vehicle_info}
                                        ${delivery.vehicle_plate ? `<br><small class="text-muted">${delivery.vehicle_plate}</small>` : ''}
                                    </div>
                                ` : ''}
                            </div>
                        ` : ''}
                        
                        ${delivery.notes ? `<p class="mb-2"><strong>Notes:</strong> ${delivery.notes}</p>` : ''}
                        
                        ${itemsInDelivery.length > 0 ? `
                            <h6 class="mb-2">Items dalam pengiriman:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Type</th>
                                            <th>Item Name</th>
                                            <th>Serial Number</th>
                                            <th>Verifikasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${itemsInDelivery.map(item => `
                                            <tr>
                                                <td><span class="badge ${getItemTypeBadgeClass(item.item_type)}">${item.item_type}</span></td>
                                                <td>${item.item_name || '-'}</td>
                                                <td><code>${item.serial_number || '-'}</code></td>
                                                <td>${getVerificationStatusBadge(item.status_verifikasi || 'Belum Dicek')}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        ` : '<p class="text-muted mb-0">Tidak ada items dalam pengiriman ini</p>'}
                    </div>
                </div>
            `;
        });
    }
    
    $('#deliveriesContent').html(deliveriesHtml);
}

// Initialize PO Detail tabs - prevent affecting parent page tabs
function initializePODetailTabs() {
    // Scope to modal only - use modal container to limit selector scope
    const modalContainer = $('#viewPOModal');
    
    // Remove any existing event handlers to prevent conflicts
    modalContainer.find('#items-tab, #deliveries-tab').off('click.poDetail');
    
    // Handle tab clicks manually to prevent affecting parent tabs
    modalContainer.find('#items-tab').on('click.poDetail', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        // Manually switch tabs within modal only
        modalContainer.find('#items-tab').addClass('active').attr('aria-selected', 'true');
        modalContainer.find('#deliveries-tab').removeClass('active').attr('aria-selected', 'false');
        modalContainer.find('#items-pane').addClass('show active');
        modalContainer.find('#deliveries-pane').removeClass('show active');
    });
    
    modalContainer.find('#deliveries-tab').on('click.poDetail', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        // Manually switch tabs within modal only
        modalContainer.find('#deliveries-tab').addClass('active').attr('aria-selected', 'true');
        modalContainer.find('#items-tab').removeClass('active').attr('aria-selected', 'false');
        modalContainer.find('#deliveries-pane').addClass('show active');
        modalContainer.find('#items-pane').removeClass('show active');
    });
}

// Helper functions for badges and icons
function getStatusBadge(status) {
    const statusMap = {
        'pending': 'bg-warning',
        'completed': 'bg-success',
        'cancelled': 'bg-danger',
        'in_progress': 'bg-info',
        'PENDING': 'bg-warning',
        'COMPLETED': 'bg-success',
        'CANCELLED': 'bg-danger',
        'IN_PROGRESS': 'bg-info'
    };
    const statusText = {
        'pending': 'Pending',
        'completed': 'Completed',
        'cancelled': 'Cancelled',
        'in_progress': 'In Progress',
        'PENDING': 'Pending',
        'COMPLETED': 'Completed',
        'CANCELLED': 'Cancelled',
        'IN_PROGRESS': 'In Progress'
    };
    const badgeClass = statusMap[status] || 'bg-secondary';
    const text = statusText[status] || status;
    return `<span class="badge ${badgeClass}">${text}</span>`;
}

function getDeliveryStatusBadge(status) {
    const statusMap = {
        'Scheduled': 'bg-info',
        'In Transit': 'bg-warning',
        'Received': 'bg-success',
        'Cancelled': 'bg-danger',
        'SCHEDULED': 'bg-info',
        'IN_TRANSIT': 'bg-warning',
        'RECEIVED': 'bg-success',
        'CANCELLED': 'bg-danger'
    };
    const statusText = {
        'Scheduled': 'Scheduled',
        'In Transit': 'In Transit',
        'Received': 'Received',
        'Cancelled': 'Cancelled',
        'SCHEDULED': 'Scheduled',
        'IN_TRANSIT': 'In Transit',
        'RECEIVED': 'Received',
        'CANCELLED': 'Cancelled'
    };
    const badgeClass = statusMap[status] || 'bg-secondary';
    const text = statusText[status] || status;
    return `<span class="badge ${badgeClass}">${text}</span>`;
}

// Duplicate function removed - using renderPODetailNew instead

function getItemTypeIcon(type) {
    const icons = {
        'Unit': 'fas fa-truck',
        'Attachment': 'fas fa-tools',
        'Battery': 'fas fa-battery-full',
        'Charger': 'fas fa-plug',
        'Sparepart': 'fas fa-cogs'
    };
    return icons[type] || 'fas fa-box';
}

function getItemTypeBadgeClass(type) {
    const classes = {
        'Unit': 'bg-primary',
        'Attachment': 'bg-info', 
        'Battery': 'bg-warning',
        'Charger': 'bg-success',
        'Sparepart': 'bg-secondary'
    };
    return classes[type] || 'bg-secondary';
}

// Modal action functions
function createDeliveryScheduleFromModal(poId) {
    $('#viewPOModal').modal('hide');
    createDeliverySchedule(poId);
}

function trackDeliveriesFromModal(poId) {
    $('#viewPOModal').modal('hide');
    trackDeliveries(poId);
}

function addDeliveryFromModal(poId) {
    $('#viewPOModal').modal('hide');
    addDelivery(poId);
}

function completePOFromModal(poId) {
    $('#viewPOModal').modal('hide');
    completePO(poId);
}

function reverifyPOFromModal(poId) {
    $('#viewPOModal').modal('hide');
    reverifyPO(poId);
}

function cancelPOFromModal(poId) {
    $('#viewPOModal').modal('hide');
    cancelPO(poId);
}

function deletePOFromModal(poId) {
    $('#viewPOModal').modal('hide');
    deletePO(poId);
}

function printPOFromModal() {
    printPO(currentPOId);
}

// Duplicate function removed - using the one at line 1997

function getDeliveryStatusIcon(status) {
    const icons = {
        'Scheduled': 'fa-clock',
        'In Transit': 'fa-truck',
        'Received': 'fa-check-circle',
        'Completed': 'fa-check-double',
        'Cancelled': 'fa-times-circle'
    };
    return icons[status] || 'fa-question-circle';
}

// Delivery action functions
function assignSerialNumbers(deliveryId) {
    currentDeliveryId = deliveryId;
    
    // Load delivery details and items
    loadDeliveryDetailsForSN(deliveryId);
    
    $('#assignSNModal').modal('show');
}

function proceedWithoutSN() {
    if (currentDeliveryId && confirm('Apakah Anda yakin ingin melanjutkan tanpa assign SN? Delivery akan langsung ke status In Transit.')) {
        $('#assignSNModal').modal('hide');
        updateDeliveryStatus(currentDeliveryId, 'In Transit');
    }
}

function loadDeliveryDetailsForSN(deliveryId) {
    // Reset modal content
    $('#snAssignmentContent').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading delivery details...</p>
        </div>
    `);
    
    // Load delivery details
    $.ajax({
        url: `<?= base_url('/purchasing/api/get-delivery-data') ?>`,
        type: 'POST',
        data: { 
            draw: 1, 
            start: 0, 
            length: 1000, // Get more records to find the specific delivery
            search: { value: '' },
            order: [{ column: 0, dir: 'desc' }]
        },
        success: function(response) {
            console.log('Delivery data response:', response);
            if (response.data && response.data.length > 0) {
                const delivery = response.data.find(d => d.id_delivery == deliveryId);
                console.log('Found delivery:', delivery);
                if (delivery) {
                    // Populate delivery info
                    $('#snModalPackingList').text(delivery.packing_list_no || '-');
                    $('#snModalDriver').text(delivery.driver_name || '-');
                    $('#snModalDeliveryDate').text(delivery.delivery_date || '-');
                    $('#snModalVehicle').text(delivery.vehicle_info || '-');
                    
                    // Load items for SN assignment
                    console.log('Delivery po_id:', delivery.po_id, 'delivery_id:', deliveryId);
                    if (delivery.po_id) {
                        loadItemsForSNAssignment(delivery.po_id, deliveryId);
                    } else {
                        showError('PO ID tidak ditemukan dalam data delivery');
                    }
                } else {
                    showError('Delivery tidak ditemukan');
                }
            } else {
                showError('Gagal memuat data delivery');
            }
        },
        error: function() {
            showError('Terjadi kesalahan saat memuat data delivery');
        }
    });
}

function loadItemsForSNAssignment(poId, deliveryId) {
    $.ajax({
        url: `<?= base_url('/purchasing/api/delivery-items/') ?>${poId}?delivery_id=${deliveryId}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                renderSNAssignmentForm(response.items);
            } else {
                showError(response.message || 'Gagal memuat items');
            }
        },
        error: function() {
            showError('Terjadi kesalahan saat memuat items');
        }
    });
}

function renderSNAssignmentForm(items) {
    let html = '';
    
    // Check if items exist
    if (!items || (!items.units?.length && !items.attachments?.length && !items.batteries?.length && !items.chargers?.length)) {
        html = `
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Tidak ada items untuk assign serial numbers.
            </div>
        `;
        $('#snAssignmentContent').html(html);
        return;
    }
    
    // Compact card format
    html += '<div class="row g-3">';
    
    // Units
    if (items.units && items.units.length > 0) {
        items.units.forEach((unit, index) => {
            html += `
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-dark py-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-truck me-2"></i>
                                <strong>Unit Item #${index + 1}</strong>
                                <span class="ms-auto small text-muted">${unit.item_name || 'Unit Item'}</span>
                            </div>
                        </div>
                        <div class="card-body py-2">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">SN Unit:</label>
                                    <input type="text" class="form-control form-control-sm" 
                                           name="serial_number_po_${index}" 
                                           placeholder="Masukkan SN Unit"
                                           value="${unit.serial_number_po || ''}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">SN Engine:</label>
                                    <input type="text" class="form-control form-control-sm" 
                                           name="sn_engine_${index}" 
                                           placeholder="Masukkan SN Mesin"
                                           value="${unit.sn_mesin_po || ''}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">SN Mast:</label>
                                    <input type="text" class="form-control form-control-sm" 
                                           name="sn_mast_${index}" 
                                           placeholder="Masukkan SN Mast"
                                           value="${unit.sn_mast_po || ''}">
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    // Attachments
    if (items.attachments && items.attachments.length > 0) {
        items.attachments.forEach((attachment, index) => {
            html += `
                <div class="col-12">
                    <div class="card border-info">
                        <div class="card-header bg-info text-dark py-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-puzzle-piece me-2"></i>
                                <strong>Attachment Item #${index + 1}</strong>
                                <span class="ms-auto small text-muted">${attachment.item_name || 'Attachment Item'}</span>
                            </div>
                        </div>
                        <div class="card-body py-2">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Serial Number:</label>
                                    <input type="text" class="form-control form-control-sm" 
                                           name="sn_attachment_${index}" 
                                           placeholder="Masukkan Serial Number"
                                           value="${attachment.serial_number || ''}">
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    // Batteries
    if (items.batteries && items.batteries.length > 0) {
        items.batteries.forEach((battery, index) => {
            html += `
                <div class="col-12">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark py-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-battery-full me-2"></i>
                                <strong>Battery Item #${index + 1}</strong>
                                <span class="ms-auto small text-muted">${battery.item_name || 'Battery Item'}</span>
                            </div>
                        </div>
                        <div class="card-body py-2">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Serial Number:</label>
                                    <input type="text" class="form-control form-control-sm" 
                                           name="sn_battery_${index}" 
                                           placeholder="Masukkan Serial Number"
                                           value="${battery.serial_number || ''}">
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    // Chargers
    if (items.chargers && items.chargers.length > 0) {
        items.chargers.forEach((charger, index) => {
            html += `
                <div class="col-12">
                    <div class="card border-success">
                        <div class="card-header bg-success text-dark py-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-plug me-2"></i>
                                <strong>Charger Item #${index + 1}</strong>
                                <span class="ms-auto small text-muted">${charger.item_name || 'Charger Item'}</span>
                            </div>
                        </div>
                        <div class="card-body py-2">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Serial Number:</label>
                                    <input type="text" class="form-control form-control-sm" 
                                           name="sn_charger_${index}" 
                                           placeholder="Masukkan Serial Number"
                                           value="${charger.serial_number || ''}">
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    html += '</div>';
    
    $('#snAssignmentContent').html(html);
}

function showError(message) {
    $('#snAssignmentContent').html(`
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            ${message}
        </div>
    `);
}

function markAsInTransit(deliveryId) {
    if (confirm('Apakah Anda yakin ingin menandai delivery ini sebagai In Transit?')) {
        updateDeliveryStatus(deliveryId, 'In Transit');
    }
}

function markAsReceived(deliveryId) {
    if (confirm('Apakah Anda yakin ingin menandai delivery ini sebagai Received?')) {
        updateDeliveryStatus(deliveryId, 'Received');
    }
}


function updateDeliveryStatus(deliveryId, status) {
    $.ajax({
        url: '<?= base_url('/purchasing/api/update-delivery-status') ?>',
        type: 'POST',
        data: {
            delivery_id: deliveryId,
            status: status
        },
        success: function(response) {
            if (response.success) {
                refreshTable();
                if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                    OptimaPro.showNotification(response.message || 'Status delivery berhasil diupdate', 'success');
                } else {
                    showNotification(response.message || 'Status delivery berhasil diupdate', 'success');
                }
            } else {
                if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                    OptimaPro.showNotification(response.message || 'Gagal mengupdate status delivery', 'error');
                } else {
                    showNotification(response.message || 'Gagal mengupdate status delivery', 'error');
                }
            }
        },
        error: function() {
            if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                OptimaPro.showNotification('Terjadi kesalahan saat mengupdate status delivery', 'error');
            } else {
                showNotification('Terjadi kesalahan saat mengupdate status delivery', 'error');
            }
        }
    });
}

function viewDeliveryDetail(deliveryId, event) {
    if (event) event.preventDefault();
    // Implementation for delivery detail modal
    console.log('View delivery detail:', deliveryId);
}

// Function removed - see the comprehensive version below

function getDeliveryStatusBadgeClass2(status) {
    const classes = {
        'Scheduled': 'bg-secondary',
        'In Transit': 'bg-warning',
        'Received': 'bg-success',
        'Partial': 'bg-info',
        'Cancelled': 'bg-danger'
    };
    return classes[status] || 'bg-secondary';
}

function addNewDelivery(poId) {
    if (typeof Swal === 'undefined') return;
    
    Swal.fire('Info', 'Fitur tambah pengiriman coming soon!', 'info');
}

// Duplicate function removed

    function printPackingList(deliveryId, packingListNo, event) {
        if (event) {
            event.stopPropagation();
        }
        const url = '<?= base_url('purchasing/print-packing-list') ?>?delivery_id=' + deliveryId + '&packing_list=' + encodeURIComponent(packingListNo);
        window.open(url, '_blank');
    }
    
    function printPO(poId, event) {
    if (event) event.preventDefault();
    window.open('<?= base_url('/purchasing/print-po/') ?>' + poId, '_blank');
}

function deletePO(poId, event) {
    if (event) event.preventDefault();
    if (typeof $ === 'undefined' || typeof Swal === 'undefined') return;
    
    // Check permission for deleting PO
    <?php if (!can_manage('purchasing')): ?>
    Swal.fire('Access Denied', 'You do not have permission to delete Purchase Orders', 'error');
    return;
    <?php endif; ?>
    
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Data PO ini akan dihapus secara permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'DELETE',
                url: '<?= base_url('/purchasing/delete-po/') ?>' + poId,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Dihapus!', 'PO berhasil dihapus.', 'success');
                        if (unifiedPOTable) unifiedPOTable.ajax.reload();
                    } else {
                        Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Gagal!', 'Tidak dapat terhubung ke server.', 'error');
                }
            });
        }
    });
}

function reverifyPO(poId) {
    if (typeof $ === 'undefined' || typeof Swal === 'undefined') return;
    
    Swal.fire({
        title: 'Verifikasi Ulang PO?',
        text: 'Status item yang "Tidak Sesuai" akan diubah kembali menjadi "Belum Dicek". PO akan masuk kembali ke antrian verifikasi. Lanjutkan?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Lanjutkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '<?= base_url('/purchasing/reverify-po/') ?>' + poId,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil!', 'PO telah dikembalikan ke antrian verifikasi.', 'success');
                        if (unifiedPOTable) unifiedPOTable.ajax.reload();
                    } else {
                        Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Gagal!', 'Tidak dapat terhubung ke server.', 'error');
                }
            });
        }
    });
}

function cancelPO(poId) {
    if (typeof $ === 'undefined' || typeof Swal === 'undefined') return;
    
    Swal.fire({
        title: 'Selesaikan dan Batalkan PO?',
        text: 'Status PO ini akan diubah menjadi "Cancelled". Aksi ini tidak dapat dibatalkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Batalkan PO!',
        cancelButtonText: 'Tidak'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '<?= base_url('/purchasing/cancel-po/') ?>' + poId,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Dibatalkan!', 'PO telah berhasil dibatalkan.', 'success');
                        if (unifiedPOTable) unifiedPOTable.ajax.reload();
                    } else {
                        Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Gagal!', 'Tidak dapat terhubung ke server.', 'error');
                }
            });
        }
    });
}

// Removed duplicate refreshTable function - using the one above

function exportData() {
    if (typeof Swal === 'undefined') return;
    Swal.fire('Info', 'Export feature coming soon!', 'info');
}

// Wrapper functions for modal actions
// Removed duplicate function - using the one above

// Duplicate functions removed - using the ones at lines 2543-2573

// Action functions from table
function createDeliverySchedule(poId, event) {
    if (event) event.stopPropagation();
    createDelivery(poId);
}

function createDelivery(poId, event) {
    if (event) event.stopPropagation();
    currentPOId = poId;
    
    // Reset form
    $('#createDeliveryForm')[0].reset();
    
    // Set today's date as default
    const today = new Date().toISOString().split('T')[0];
    $('input[name="delivery_date"]').val(today);
    
    // Auto-generate packing list number (will be replaced by backend)
    // $('input[name="packing_list_no"]').val('Auto-generated').prop('readonly', true);
    
    // Load PO items
    loadDeliveryItems(poId);
    
    $('#createDeliveryModal').modal('show');
}

function loadDeliveryItems(poId) {
    $('#deliveryItemsList').html(`
        <div class="text-center py-3">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 mb-0 text-muted small">Loading items...</p>
        </div>
    `);
    
    $.ajax({
        url: `<?= base_url('/purchasing/api/delivery-items/') ?>${poId}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                renderDeliveryItems(response.po, response.items);
            } else {
                $('#deliveryItemsList').html(`
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ${response.message || 'Gagal memuat items'}
                    </div>
                `);
            }
        },
        error: function() {
            $('#deliveryItemsList').html(`
                <div class="alert alert-danger mb-0">
                    <i class="fas fa-times-circle me-2"></i>
                    Terjadi kesalahan saat memuat items
                </div>
            `);
        }
    });
}

function renderDeliveryItems(po, items) {
    let html = '';
    
    // PO Info
    html += `
        <div class="mb-3 p-3 bg-light rounded">
            <div class="row">
                <div class="col-6">
                    <strong>PO Number:</strong><br>
                    <span class="text-primary">${po.nomor_po || po.no_po}</span>
                </div>
                <div class="col-6">
                    <strong>Supplier:</strong><br>
                    <span class="text-primary">${po.nama_supplier}</span>
                </div>
            </div>
        </div>
    `;
    
    // SPK-style Checklist Section with SEPARATED categories
    html += `
                <div class="mb-3">
            <strong>Pilih Item yang akan dikirim</strong>
            <div class="form-text mb-3">Centang item yang ingin dimasukkan ke delivery ini.</div>
                </div>
    `;
    
    // Units Section
    if (items.units && items.units.length > 0) {
        html += `
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0"><i class="fas fa-truck me-2 text-primary"></i>Unit (${items.units.length} items)</h6>
                    <div class="btn-group btn-group-sm">
                        <?php if (can_create('purchasing')): ?>
                        <button type="button" class="btn btn-outline-secondary" onclick="selectAllUnits()">Pilih Semua</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="clearAllUnits()">Bersihkan</button>
                        <?php else: ?>
                        <button type="button" class="btn btn-outline-secondary" disabled title="Access denied">Pilih Semua</button>
                        <button type="button" class="btn btn-outline-secondary" disabled title="Access denied">Bersihkan</button>
                        <?php endif; ?>
            </div>
                </div>
                <div class="border rounded p-2" style="max-height:200px; overflow:auto">
        `;
        
        items.units.forEach((unit, index) => {
            const isDisabled = unit.is_delivered;
            const disabledClass = isDisabled ? 'text-muted' : '';
            const disabledAttr = isDisabled ? 'disabled' : '';
            const deliveredBadge = isDisabled ? '<span class="badge bg-success ms-2">Sudah Terkirim</span>' : '';
            
            // Debug logging
            console.log('Unit ' + unit.id_po_unit + ' is_delivered: ' + isDisabled);
            
            html += `<div class="mb-2 p-2 border rounded ${disabledClass}">
            <div class="form-check">
                <input class="form-check-input delivery-item-checkbox" type="checkbox" 
                       data-type="unit" data-id="${unit.id_po_unit}" 
                       data-name="${unit.item_name}" id="delivery_unit_${index}" 
                       onchange="updateDeliverySelection()" ${disabledAttr}>
                 <label class="form-check-label" for="delivery_unit_${index}" style="width: 95%;">
                     <strong>${index + 1}. ${unit.item_name}</strong>
                     ${deliveredBadge}
                 </label>
            </div>
        </div>`;
        });
        
        html += `</div></div>`;
    }
    
    // Attachments Section
    if (items.attachments && items.attachments.length > 0) {
        html += `
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0"><i class="fas fa-paperclip me-2 text-success"></i>Attachment (${items.attachments.length} items)</h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" onclick="selectAllAttachments()">Pilih Semua</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="clearAllAttachments()">Bersihkan</button>
                    </div>
                </div>
                <div class="border rounded p-2" style="max-height:200px; overflow:auto">
        `;
        
        items.attachments.forEach((attachment, index) => {
            const isDisabled = attachment.is_delivered;
            const disabledClass = isDisabled ? 'text-muted' : '';
            const disabledAttr = isDisabled ? 'disabled' : '';
            const deliveredBadge = isDisabled ? '<span class="badge bg-success ms-2">Sudah Terkirim</span>' : '';
            
            // Debug logging
            console.log('Attachment ' + attachment.id_po_attachment + ' is_delivered: ' + isDisabled);
            
            html += `<div class="mb-2 p-2 border rounded ${disabledClass}">
                <div class="form-check">
                    <input class="form-check-input delivery-item-checkbox" type="checkbox" 
                           data-type="attachment" data-id="${attachment.id_po_attachment}" 
                           data-name="${attachment.item_name}" id="delivery_attachment_${index}" 
                           onchange="updateDeliverySelection()" ${disabledAttr}>
                    <label class="form-check-label" for="delivery_attachment_${index}">
                        <strong>${index + 1}. ${attachment.item_name}</strong>
                        ${deliveredBadge}
                    </label>
                </div>
            </div>`;
        });
        
        html += `</div></div>`;
    }
    
    // Batteries Section
    if (items.batteries && items.batteries.length > 0) {
        html += `
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0"><i class="fas fa-battery-half me-2 text-warning"></i>Battery (${items.batteries.length} items)</h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" onclick="selectAllBatteries()">Pilih Semua</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="clearAllBatteries()">Bersihkan</button>
                    </div>
                </div>
                <div class="border rounded p-2" style="max-height:200px; overflow:auto">
        `;
        
        items.batteries.forEach((battery, index) => {
            const isDisabled = battery.is_delivered;
            const disabledClass = isDisabled ? 'text-muted' : '';
            const disabledAttr = isDisabled ? 'disabled' : '';
            const deliveredBadge = isDisabled ? '<span class="badge bg-success ms-2">Sudah Terkirim</span>' : '';
            
            html += `<div class="mb-2 p-2 border rounded ${disabledClass}">
                <div class="form-check">
                    <input class="form-check-input delivery-item-checkbox" type="checkbox"
                           data-type="battery" data-id="${battery.id_po_attachment}" 
                           data-name="${battery.item_name}" id="delivery_battery_${index}" 
                           onchange="updateDeliverySelection()" ${disabledAttr}>
                    <label class="form-check-label" for="delivery_battery_${index}">
                        <strong>${index + 1}. ${battery.item_name}</strong>
                        ${deliveredBadge}
                    </label>
                </div>
            </div>`;
        });
        
        html += `</div></div>`;
    }
    
    // Chargers Section
    if (items.chargers && items.chargers.length > 0) {
        html += `
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0"><i class="fas fa-plug me-2 text-info"></i>Charger (${items.chargers.length} items)</h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" onclick="selectAllChargers()">Pilih Semua</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="clearAllChargers()">Bersihkan</button>
                    </div>
                </div>
                <div class="border rounded p-2" style="max-height:200px; overflow:auto">
        `;
        
        items.chargers.forEach((charger, index) => {
            const isDisabled = charger.is_delivered;
            const disabledClass = isDisabled ? 'text-muted' : '';
            const disabledAttr = isDisabled ? 'disabled' : '';
            const deliveredBadge = isDisabled ? '<span class="badge bg-success ms-2">Sudah Terkirim</span>' : '';
            
            html += `<div class="mb-2 p-2 border rounded ${disabledClass}">
                <div class="form-check">
                    <input class="form-check-input delivery-item-checkbox" type="checkbox"
                           data-type="charger" data-id="${charger.id_po_attachment}" 
                           data-name="${charger.item_name}" id="delivery_charger_${index}" 
                           onchange="updateDeliverySelection()" ${disabledAttr}>
                    <label class="form-check-label" for="delivery_charger_${index}">
                        <strong>${index + 1}. ${charger.item_name}</strong>
                        ${deliveredBadge}
                    </label>
                </div>
            </div>`;
        });
        
        html += `</div></div>`;
    }
    
    html += `</div>`; // Close checklist section
    
    // Summary
    html += `
        <div class="mt-3 p-3 bg-light rounded">
            <div class="row">
                <div class="col-6">
                    <strong>Total Items Selected:</strong>
                    <span id="deliveryTotalSelected" class="badge bg-primary ms-2">0</span>
                </div>
                <div class="col-6 text-end">
                    <small class="text-muted">Centang minimal satu item untuk dikirim</small>
                </div>
            </div>
        </div>
    `;
    
    $('#deliveryItemsList').html(html);
}

// Helper functions for partial delivery
function updateDeliveryQuantity(checkbox) {
    const qtyInput = checkbox.closest('tr').querySelector('.delivery-qty-input');
    
    if (checkbox.checked) {
        qtyInput.disabled = false;
        qtyInput.value = qtyInput.getAttribute('data-max');
        qtyInput.focus();
    } else {
        qtyInput.disabled = true;
        qtyInput.value = 0;
    }
    
    updateDeliveryTotal();
}

function updateDeliveryTotal() {
    let total = 0;
    document.querySelectorAll('.delivery-qty-input:not([disabled])').forEach(input => {
        const value = parseInt(input.value) || 0;
        total += value;
    });
    
    document.getElementById('deliveryTotalCount').textContent = total;
}

// Notification helper function
function showNotification(message, type = 'info') {
    if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
        OptimaPro.showNotification(message, type);
    } else if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: type === 'success' ? 'Berhasil!' : type === 'error' ? 'Error!' : 'Info',
            text: message,
            icon: type,
            timer: 3000,
            showConfirmButton: false
        });
    } else {
        alert(message);
    }
}

function trackDeliveries(poId, event) {
    if (event) event.stopPropagation();
    viewPODetail(poId, event);
}


function completePO(poId, event) {
    if (event) event.stopPropagation();
    if (typeof $ === 'undefined' || typeof Swal === 'undefined') return;
    
    Swal.fire({
        title: 'Tandai PO Selesai?',
        text: 'Semua items sudah diterima. Tandai PO ini sebagai completed?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Selesai!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '<?= base_url('/purchasing/complete-po/') ?>' + poId,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil!', 'PO berhasil ditandai sebagai completed.', 'success');
                        if (unifiedPOTable) unifiedPOTable.ajax.reload();
                        $('#viewPOModal').modal('hide');
                    } else {
                        Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Gagal!', 'Tidak dapat terhubung ke server.', 'error');
                }
            });
        }
    });
}

// ========================================
// CREATE PO MODAL FUNCTIONS
// ========================================
let poItems = [];
let currentItemType = '';
let editIndex = -1;

// Initialize Create PO Modal
function initCreatePOModal() {
    console.log(' Initializing Create PO Modal...');
    
    // Check if Bootstrap is loaded
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap is not loaded!');
        return;
    }
    
    // Button handler to open modal
    $('#btnBuatPO').off('click').on('click', function() {
        <?php if (!can_create('purchasing')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Access Denied',
            text: 'You do not have permission to create Purchase Orders',
            confirmButtonColor: '#d33'
        });
        return false;
        <?php endif; ?>
        
        console.log('📝 Opening Create PO Modal...');
        $('#createPoModal').modal('show');
    });
    
    // Initialize Select2 for supplier dropdown in main modal
    $('#createPoModal').on('shown.bs.modal', function() {
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2-modal').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#createPoModal'),
                width: '100%'
            });
        }
    });
    
    // Save item button
    $('#saveItemBtn').off('click').on('click', function() {
        const itemData = collectItemData();
        
        if (!itemData) {
            if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                OptimaPro.showNotification('Harap lengkapi semua field yang wajib diisi!', 'warning');
            } else {
                alert('Harap lengkapi semua field yang wajib diisi!');
            }
            return;
        }
        
        if (editIndex >= 0) {
            poItems[editIndex] = itemData;
        } else {
            poItems.push(itemData);
        }
        
        updateItemsTable();
        document.getElementById('items_json').value = JSON.stringify(poItems);
        
        // Hide modal using jQuery (more reliable)
        $('#itemDetailModal').modal('hide');
        
        if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
            OptimaPro.showNotification('Item berhasil ditambahkan!', 'success');
        }
    });
    
    // Form submission validation and AJAX handling
    $('#unifiedPOForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
        
        if (poItems.length === 0) {
            if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                OptimaPro.showNotification('Harap tambahkan minimal satu item ke PO!', 'warning');
            } else {
                alert('Harap tambahkan minimal satu item ke PO!');
            }
            return false;
        }
        
        // Update hidden field
        document.getElementById('items_json').value = JSON.stringify(poItems);
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...');
        
        // Submit via AJAX
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Show success notification using OptimaPro theme
                    if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                        OptimaPro.showNotification(response.message, 'success');
                    } else {
                        // Fallback notification
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                    
                    // Close modal and refresh table
                    $('#createPoModal').modal('hide');
                    if (typeof refreshTable === 'function') {
                        refreshTable();
                    }
                } else {
                    // Show error notification
                    if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                        OptimaPro.showNotification(response.message, 'error');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message
                        });
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                let errorMessage = 'Terjadi kesalahan saat menyimpan PO';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                    OptimaPro.showNotification(errorMessage, 'error');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage
                    });
                }
            },
            complete: function() {
                // Restore button state
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Initialize cascading dropdowns
    initializeUnitDropdowns();
    initializeAttachmentDropdowns();
    initializeBatteryDropdowns();
    initializeChargerDropdowns();
    
    // Reset form when modal is hidden
    $('#createPoModal').on('hidden.bs.modal', function() {
        $('#unifiedPOForm')[0].reset();
        poItems = [];
        updateItemsTable();
        document.getElementById('items_json').value = '[]';
    });
}

// Open modal and load form based on item type
function openItemModal(itemType, index = -1) {
    currentItemType = itemType;
    editIndex = index;
    
    const modalBody = document.getElementById('itemModalBody');
    const modalTitle = document.getElementById('itemModalTitle');
    
    // Set title
    const titles = {
        'unit': 'Tambah Unit Forklift',
        'attachment': 'Tambah Attachment',
        'battery': 'Tambah Battery',
        'charger': 'Tambah Charger'
    };
    modalTitle.textContent = editIndex >= 0 ? titles[itemType].replace('Tambah', 'Edit') : titles[itemType];
    
    // Load form
    modalBody.innerHTML = '<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-3">Loading form...</p></div>';
    
    // Show modal using jQuery (more reliable)
    $('#itemDetailModal').modal('show');
    
    fetch(`<?= base_url('/purchasing/api/get-item-form/') ?>${itemType}`)
        .then(response => response.text())
        .then(html => {
            modalBody.innerHTML = html;
            
            // Initialize Select2 after content loaded
            setTimeout(() => {
                if (typeof $.fn.select2 !== 'undefined') {
                    $('#itemDetailModal .select2-basic').select2({
                        theme: 'bootstrap-5',
                        dropdownParent: $('#itemDetailModal .modal-content'),
                        width: '100%',
                        dropdownAutoWidth: true,
                        // Fix scroll issue in modal
                        dropdownCssClass: 'select2-dropdown-fixed'
                    });
                }
                
                // Ensure disabled state for cascading dropdowns
                if (currentItemType === 'unit') {
                    $('#unit_jenis').prop('disabled', true).trigger('change.select2');
                    $('#unit_model').prop('disabled', true).trigger('change.select2');
                } else if (currentItemType === 'attachment') {
                    $('#att_merk').prop('disabled', true).trigger('change.select2');
                    $('#att_model').prop('disabled', true).trigger('change.select2');
                } else if (currentItemType === 'battery') {
                    $('#battery_merk').prop('disabled', true).trigger('change.select2');
                    $('#battery_tipe').prop('disabled', true).trigger('change.select2');
                } else if (currentItemType === 'charger') {
                    $('#charger_model').prop('disabled', true).trigger('change.select2');
                }
                
                // If editing, populate form
                if (editIndex >= 0) {
                    populateFormForEdit(poItems[editIndex]);
                }
                
                console.log('✅ Unit form loaded and Select2 initialized');
            }, 150);
        })
        .catch(error => {
            modalBody.innerHTML = '<div class="alert alert-danger">Error loading form. Please try again.</div>';
            console.error('Error:', error);
        });
}

// Collect data from modal form based on item type
function collectItemData() {
    const data = {
        item_type: currentItemType
    };
    
    if (currentItemType === 'unit') {
        // Collect unit data (simplified - no tipe)
        data.departemen_id = $('#unit_departemen').val();
        data.tipe_unit_id = $('#unit_jenis').val(); // This is actually jenis/tipe_unit_id
        data.merk_unit = $('#unit_merk').val();
        data.model_unit_id = $('#unit_model').val();
        data.tahun_unit = $('#unit_tahun').val();
        data.kapasitas_id = $('#unit_kapasitas').val();
        data.kondisi_penjualan = $('#unit_kondisi').val();
        
        // Mast components (tinggi_mast will be fetched from tipe_mast table via mast_id)
        data.mast_id = $('#unit_mast').val();
        data.sn_mast = $('#unit_sn_mast').val();
        
        // Engine components
        data.mesin_id = $('#unit_mesin').val();
        data.sn_mesin = $('#unit_sn_mesin').val();
        
        // Other components
        data.ban_id = $('#unit_ban').val();
        data.roda_id = $('#unit_roda').val();
        data.valve_id = $('#unit_valve').val();
        
        data.qty = $('#unit_qty').val();
        data.keterangan = $('#unit_keterangan').val();
        
        // Collect text labels for display
        data._display = {
            departemen_text: $('#unit_departemen option:selected').text(),
            jenis_text: $('#unit_jenis option:selected').text(),
            merk_text: $('#unit_merk option:selected').text(),
            model_text: $('#unit_model option:selected').text(),
            kapasitas_text: $('#unit_kapasitas option:selected').text(),
            kondisi_text: $('#unit_kondisi option:selected').text()
        };
        
        // Validation
        if (!data.departemen_id || !data.tipe_unit_id || !data.merk_unit || !data.model_unit_id || !data.qty) {
            return null;
        }
        
    } else if (currentItemType === 'attachment') {
        data.tipe_attachment = $('#att_tipe').val();
        data.merk_attachment = $('#att_merk').val();
        data.attachment_id = $('#att_model').val(); // This is the final ID from database
        data.serial_number = $('#att_serial_number').val();
        data.qty = $('#att_qty').val();
        data.keterangan = $('#att_keterangan').val();
        
        data._display = {
            tipe_text: $('#att_tipe option:selected').text() || 'Unknown Type',
            merk_text: $('#att_merk option:selected').text() || 'Unknown Brand',
            model_text: $('#att_model option:selected').text() || 'Unknown Model'
        };
        
        console.log('Attachment data collected:', data);
        
        if (!data.attachment_id || !data.qty) {
            console.warn('Attachment validation failed - missing attachment_id or qty');
            return null;
        }
        
    } else if (currentItemType === 'battery') {
        data.jenis_baterai = $('#battery_jenis').val();
        data.merk_baterai = $('#battery_merk').val();
        data.baterai_id = $('#battery_tipe').val(); // This is the final selection (id from database)
        data.serial_number = $('#battery_serial_number').val();
        data.qty = $('#battery_qty').val();
        data.keterangan = $('#battery_keterangan').val();
        
        data._display = {
            jenis_text: $('#battery_jenis option:selected').text(),
            merk_text: $('#battery_merk option:selected').text(),
            tipe_text: $('#battery_tipe option:selected').text(),
            battery_text: $('#battery_merk option:selected').text() + ' - ' + $('#battery_tipe option:selected').text()
        };
        
        if (!data.baterai_id || !data.qty) {
            console.warn('Battery validation failed - missing baterai_id or qty');
            return null;
        }
        
    } else if (currentItemType === 'charger') {
        data.merk_charger = $('#charger_merk').val();
        data.charger_id = $('#charger_model').val(); // This is the final ID from database
        data.serial_number = $('#charger_serial_number').val();
        data.qty = $('#charger_qty').val();
        data.keterangan = $('#charger_keterangan').val();
        
        data._display = {
            merk_text: $('#charger_merk option:selected').text() || 'Unknown Brand',
            model_text: $('#charger_model option:selected').text() || 'Unknown Model',
            charger_text: $('#charger_merk option:selected').text() + ' - ' + $('#charger_model option:selected').text()
        };
        
        console.log('Charger data collected:', data);
        
        if (!data.charger_id || !data.qty) {
            console.warn('Charger validation failed - missing charger_id or qty');
            return null;
        }
    }
    
    return data;
}

// Update items table display
function updateItemsTable() {
    const tbody = document.getElementById('itemsTableBody');
    
    if (poItems.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Belum ada item. Klik tombol "Tambah" untuk menambahkan item.</td></tr>';
        return;
    }
    
    let html = '';
    poItems.forEach((item, index) => {
        const badgeColors = {
            'unit': 'primary',
            'attachment': 'success',
            'battery': 'info',
            'charger': 'warning'
        };
        
        const typeLabels = {
            'unit': 'Unit',
            'attachment': 'Attachment',
            'battery': 'Battery',
            'charger': 'Charger'
        };
        
        let description = '';
        if (item.item_type === 'unit') {
            description = `${item._display.merk_text} ${item._display.model_text} | ${item._display.departemen_text} - ${item._display.jenis_text} | ${item._display.kapasitas_text} | Tahun ${item.tahun_unit} (${item._display.kondisi_text})`;
        } else if (item.item_type === 'attachment') {
            description = `${item._display.tipe_text} | ${item._display.merk_text} - ${item._display.model_text}`;
            if (item.serial_number) description += ` | SN: ${item.serial_number}`;
        } else if (item.item_type === 'battery') {
            description = `${item._display.jenis_text} | ${item._display.merk_text} - ${item._display.tipe_text}`;
            if (item.serial_number) description += ` | SN: ${item.serial_number}`;
        } else if (item.item_type === 'charger') {
            description = `${item._display.merk_text} - ${item._display.model_text}`;
            if (item.serial_number) description += ` | SN: ${item.serial_number}`;
        }
        
        if (item.keterangan) {
            description += `<br><small class="text-muted">Note: ${item.keterangan}</small>`;
        }
        
        html += `
            <tr>
                <td>${index + 1}</td>
                <td><span class="badge bg-${badgeColors[item.item_type]} item-badge">${typeLabels[item.item_type]}</span></td>
                <td>${description}</td>
                <td class="text-center">${item.qty}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteItem(${index})" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Delete item from table
function deleteItem(index) {
    if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showConfirmDialog === 'function') {
        OptimaPro.showConfirmDialog({
            title: 'Hapus Item',
            message: 'Apakah Anda yakin ingin menghapus item ini dari PO?'
        }).then(result => {
            if (result.isConfirmed) {
                poItems.splice(index, 1);
                updateItemsTable();
                document.getElementById('items_json').value = JSON.stringify(poItems);
                OptimaPro.showNotification('Item berhasil dihapus', 'success');
            }
        });
    } else if (confirm('Apakah Anda yakin ingin menghapus item ini dari PO?')) {
        poItems.splice(index, 1);
        updateItemsTable();
        document.getElementById('items_json').value = JSON.stringify(poItems);
    }
}

// Unit form cascading dropdowns (simplified - no tipe)
function initializeUnitDropdowns() {
    console.log(' Initializing Unit Dropdowns (Simplified)...');
    
    // Remove old handlers first to prevent duplicates
    $(document).off('change', '#unit_departemen');
    $(document).off('change', '#unit_jenis');
    $(document).off('change', '#unit_merk');
    
    // Departemen -> Jenis cascading
    $(document).on('change', '#unit_departemen', function() {
        console.log('📍 Departemen changed:', $(this).val());
        const deptId = $(this).val();
        const $jenis = $('#unit_jenis');
        
        // Reset jenis dropdown
        $jenis.html('<option value="">Loading...</option>').prop('disabled', true);
        
        if (!deptId) {
            $jenis.html('<option value="">Pilih Departemen Dulu...</option>').prop('disabled', true);
            return;
        }
        
        // Fetch jenis based on departemen
        $.ajax({
            url: '<?= base_url('/purchasing/api/get-tipe-units') ?>',
            method: 'GET',
            data: { departemen: deptId },
            dataType: 'json',
            success: function(response) {
                console.log('✅ Jenis loaded:', response);
                if (response.success && response.data && response.data.length > 0) {
                    // Group by jenis (unique)
                    const jenisMap = {};
                    response.data.forEach(r => {
                        if (r.jenis && r.id_tipe_unit) {
                            // Use jenis as key, keep first id_tipe_unit encountered
                            if (!jenisMap[r.jenis]) {
                                jenisMap[r.jenis] = r.id_tipe_unit;
                            }
                        }
                    });
                    
                    $jenis.html('<option value="">Pilih Jenis Unit...</option>');
                    Object.keys(jenisMap).sort().forEach(jenisName => {
                        $jenis.append(`<option value="${jenisMap[jenisName]}">${jenisName}</option>`);
                    });
                    $jenis.prop('disabled', false);
                    
                    // Re-initialize Select2 if available
                    if (typeof $.fn.select2 !== 'undefined') {
                        $jenis.select2('destroy').select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $('#itemDetailModal .modal-content'),
                            width: '100%',
                            dropdownAutoWidth: true,
                            dropdownCssClass: 'select2-dropdown-fixed'
                        });
                    }
                } else {
                    console.warn('No jenis data found');
                    $jenis.html('<option value="">Tidak ada data</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading jenis:', error);
                $jenis.html('<option value="">Error loading data</option>');
            }
        });
    });
    
    // Merk -> Model cascading
    $(document).on('change', '#unit_merk', function() {
        console.log('🏷️ Merk changed:', $(this).val());
        const merk = $(this).find('option:selected').data('merk');
        const $model = $('#unit_model');
        
        $model.html('<option value="">Loading...</option>').prop('disabled', true);
        
        if (!merk) {
            $model.html('<option value="">Pilih Brand Dulu...</option>').prop('disabled', true);
            return;
        }
        
        // Load models based on merk
        $.ajax({
            url: '<?= base_url('purchasing/api/get-model-units') ?>',
            method: 'GET',
            data: { merk: merk },
            dataType: 'json',
            success: function(response) {
                console.log('✅ Models loaded:', response);
                if (response.success && response.data && response.data.length > 0) {
                    $model.html('<option value="">Pilih Model...</option>');
                    response.data.forEach(item => {
                        $model.append(`<option value="${item.id_model_unit}">${item.model_unit}</option>`);
                    });
                    $model.prop('disabled', false);
                    
                    // Re-initialize Select2 if available
                    if (typeof $.fn.select2 !== 'undefined') {
                        $model.select2('destroy').select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $('#itemDetailModal .modal-content'),
                            width: '100%',
                            dropdownAutoWidth: true,
                            dropdownCssClass: 'select2-dropdown-fixed'
                        });
                    }
                } else {
                    console.warn('No model data found');
                    $model.html('<option value="">Tidak ada model</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error(' Error loading models:', error);
                $model.html('<option value="">Error loading models</option>');
            }
        });
    });
}

// Attachment form cascading dropdowns
function initializeAttachmentDropdowns() {
    console.log(' Initializing Attachment Dropdowns (Tipe → Merk → Model)...');
    
    // Remove old handlers first
    $(document).off('change', '#att_tipe');
    $(document).off('change', '#att_merk');
    
    // Tipe -> Merk cascading
    $(document).on('change', '#att_tipe', function() {
        const selectedTipe = $(this).val();
        const $merk = $('#att_merk');
        const $model = $('#att_model');
        
        console.log(' Attachment Tipe changed:', selectedTipe);
        
        $merk.html('<option value="">Loading...</option>').prop('disabled', true);
        $model.html('<option value="">Pilih Merk Dulu...</option>').prop('disabled', true);
        
        if (!selectedTipe) {
            $merk.html('<option value="">Pilih Tipe Dulu...</option>').prop('disabled', true);
            return;
        }
        
        // AJAX call to get merk based on tipe
        $.ajax({
            url: '<?= base_url('purchasing/api/get-attachment-merks') ?>',
            method: 'GET',
            data: { tipe: selectedTipe },
            dataType: 'json',
            success: function(response) {
                console.log('✅ Attachment Merk loaded:', response);
                if (response.success && response.data && response.data.length > 0) {
                    $merk.html('<option value="">Pilih Merk...</option>');
                    response.data.forEach(item => {
                        const merkName = item.merk_attachment || item.merk || 'Unknown';
                        $merk.append(`<option value="${merkName}">${merkName}</option>`);
                    });
                    $merk.prop('disabled', false);
                    
                    // Re-initialize Select2
                    if (typeof $.fn.select2 !== 'undefined') {
                        $merk.select2('destroy').select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $('#itemDetailModal .modal-content'),
                            width: '100%',
                            dropdownAutoWidth: true,
                            dropdownCssClass: 'select2-dropdown-fixed'
                        });
                    }
                } else {
                    $merk.html('<option value="">Tidak ada data merk</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error(' Error loading attachment merk:', error);
                $merk.html('<option value="">Error loading data</option>');
            }
        });
    });
    
    // Merk -> Model cascading
    $(document).on('change', '#att_merk', function() {
        const selectedTipe = $('#att_tipe').val();
        const selectedMerk = $(this).val();
        const $model = $('#att_model');
        
        console.log('🏷️ Attachment Merk changed:', selectedMerk);
        
        $model.html('<option value="">Loading...</option>').prop('disabled', true);
        
        if (!selectedTipe || !selectedMerk) {
            $model.html('<option value="">Pilih Merk Dulu...</option>').prop('disabled', true);
            return;
        }
        
        // AJAX call to get model based on tipe and merk
        $.ajax({
            url: '<?= base_url('purchasing/api/get-attachment-models') ?>',
            method: 'GET',
            data: { tipe: selectedTipe, merk: selectedMerk },
            dataType: 'json',
            success: function(response) {
                console.log('✅ Attachment Model loaded:', response);
                if (response.success && response.data && response.data.length > 0) {
                    $model.html('<option value="">Pilih Model...</option>');
                    response.data.forEach(item => {
                        const modelName = item.model_attachment || item.model || 'Unknown';
                        const modelId = item.id_attachment || item.id;
                        $model.append(`<option value="${modelId}">${modelName}</option>`);
                    });
                    $model.prop('disabled', false);
                    
                    // Re-initialize Select2
                    if (typeof $.fn.select2 !== 'undefined') {
                        $model.select2('destroy').select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $('#itemDetailModal .modal-content'),
                            width: '100%',
                            dropdownAutoWidth: true,
                            dropdownCssClass: 'select2-dropdown-fixed'
                        });
                    }
                } else {
                    $model.html('<option value="">Tidak ada data model</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error(' Error loading attachment models:', error);
                $model.html('<option value="">Error loading data</option>');
            }
        });
    });
}

// Battery form cascading dropdowns
function initializeBatteryDropdowns() {
    console.log(' Initializing Battery Dropdowns (Jenis → Merk → Tipe)...');
    
    // Remove old handlers first
    $(document).off('change', '#battery_jenis');
    $(document).off('change', '#battery_merk');
    
    // Jenis -> Merk cascading
    $(document).on('change', '#battery_jenis', function() {
        const selectedJenis = $(this).val();
        const $merk = $('#battery_merk');
        const $tipe = $('#battery_tipe');
        
        console.log(' Battery Jenis changed:', selectedJenis);
        
        $merk.html('<option value="">Loading...</option>').prop('disabled', true);
        $tipe.html('<option value="">Pilih Merk Dulu...</option>').prop('disabled', true);
        
        if (!selectedJenis) {
            $merk.html('<option value="">Pilih Jenis Dulu...</option>').prop('disabled', true);
            return;
        }
        
        // AJAX call to get merk based on jenis
        $.ajax({
            url: '<?= base_url('purchasing/api/get-battery-merks') ?>',
            method: 'GET',
            data: { jenis: selectedJenis },
            dataType: 'json',
            success: function(response) {
                console.log('✅ Battery Merk loaded:', response);
                if (response.success && response.data && response.data.length > 0) {
                    $merk.html('<option value="">Pilih Merk...</option>');
                    response.data.forEach(item => {
                        const merkName = item.merk_baterai || 'Unknown';
                        $merk.append(`<option value="${merkName}">${merkName}</option>`);
                    });
                    $merk.prop('disabled', false);
                    
                    // Re-initialize Select2
                    if (typeof $.fn.select2 !== 'undefined') {
                        $merk.select2('destroy').select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $('#itemDetailModal .modal-content'),
                            width: '100%',
                            dropdownAutoWidth: true,
                            dropdownCssClass: 'select2-dropdown-fixed'
                        });
                    }
                } else {
                    $merk.html('<option value="">Tidak ada data merk</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error(' Error loading battery merk:', error);
                $merk.html('<option value="">Error loading data</option>');
            }
        });
    });
    
    // Merk -> Tipe cascading
    $(document).on('change', '#battery_merk', function() {
        const selectedJenis = $('#battery_jenis').val();
        const selectedMerk = $(this).val();
        const $tipe = $('#battery_tipe');
        
        console.log('🏷️ Battery Merk changed:', selectedMerk);
        
        $tipe.html('<option value="">Loading...</option>').prop('disabled', true);
        
        if (!selectedJenis || !selectedMerk) {
            $tipe.html('<option value="">Pilih Merk Dulu...</option>').prop('disabled', true);
            return;
        }
        
        // AJAX call to get tipe based on jenis and merk
        $.ajax({
            url: '<?= base_url('purchasing/api/get-battery-tipes') ?>',
            method: 'GET',
            data: { jenis: selectedJenis, merk: selectedMerk },
            dataType: 'json',
            success: function(response) {
                console.log('✅ Battery Tipe loaded:', response);
                if (response.success && response.data && response.data.length > 0) {
                    $tipe.html('<option value="">Pilih Tipe...</option>');
                    response.data.forEach(item => {
                        const tipeLabel = item.tipe_baterai || 'Unknown';
                        const batteryId = item.id || item.id_baterai;
                        $tipe.append(`<option value="${batteryId}">${tipeLabel}</option>`);
                    });
                    $tipe.prop('disabled', false);
                    
                    // Re-initialize Select2
                    if (typeof $.fn.select2 !== 'undefined') {
                        $tipe.select2('destroy').select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $('#itemDetailModal .modal-content'),
                            width: '100%',
                            dropdownAutoWidth: true,
                            dropdownCssClass: 'select2-dropdown-fixed'
                        });
                    }
                } else {
                    $tipe.html('<option value="">Tidak ada data tipe</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error(' Error loading battery tipe:', error);
                $tipe.html('<option value="">Error loading data</option>');
            }
        });
    });
}

// Charger form cascading dropdowns
function initializeChargerDropdowns() {
    console.log(' Initializing Charger Dropdowns (Merk → Model)...');
    
    // Remove old handlers first
    $(document).off('change', '#charger_merk');
    
    // Merk -> Model cascading
    $(document).on('change', '#charger_merk', function() {
        const selectedMerk = $(this).val();
        const $model = $('#charger_model');
        
        console.log('🏷️ Charger Merk changed:', selectedMerk);
        
        $model.html('<option value="">Loading...</option>').prop('disabled', true);
        
        if (!selectedMerk) {
            $model.html('<option value="">Pilih Merk Dulu...</option>').prop('disabled', true);
            return;
        }
        
        // AJAX call to get model based on merk
        $.ajax({
            url: '<?= base_url('purchasing/api/get-charger-models') ?>',
            method: 'GET',
            data: { merk: selectedMerk },
            dataType: 'json',
            success: function(response) {
                console.log('✅ Charger Model loaded:', response);
                if (response.success && response.data && response.data.length > 0) {
                    $model.html('<option value="">Pilih Model...</option>');
                    response.data.forEach(item => {
                        const modelName = item.model_charger || item.tipe_charger || 'Standard';
                        const chargerId = item.id_charger || item.id;
                        $model.append(`<option value="${chargerId}">${modelName}</option>`);
                    });
                    $model.prop('disabled', false);
                    
                    // Re-initialize Select2
                    if (typeof $.fn.select2 !== 'undefined') {
                        $model.select2('destroy').select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $('#itemDetailModal .modal-content'),
                            width: '100%',
                            dropdownAutoWidth: true,
                            dropdownCssClass: 'select2-dropdown-fixed'
                        });
                    }
                } else {
                    $model.html('<option value="">Tidak ada data model</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error(' Error loading charger models:', error);
                $model.html('<option value="">Error loading data</option>');
            }
        });
    });
}

        // Create Delivery Form Handler
        $('#createDeliveryForm').on('submit', function(e) {
            e.preventDefault();
            
            if (!currentPOId) {
                showNotification('PO ID tidak ditemukan', 'error');
                return;
            }
            
            // Collect selected items for delivery (simple checklist)
            const selectedItems = [];
            document.querySelectorAll('.delivery-item-checkbox:checked').forEach(checkbox => {
                selectedItems.push({
                    type: checkbox.getAttribute('data-type'),
                    id: checkbox.getAttribute('data-id'),
                    name: checkbox.getAttribute('data-name'),
                    qty: 1 // Always 1 for checklist items
                });
            });
            
            if (selectedItems.length === 0) {
                showNotification('Pilih minimal satu item untuk dikirim', 'warning');
                return;
            }
            
            const formData = {
                po_id: currentPOId,
                delivery_date: $('input[name="delivery_date"]').val(),
                packing_list_no: $('input[name="packing_list_no"]').val(),
                driver_name: $('input[name="driver_name"]').val(),
                driver_phone: $('input[name="driver_phone"]').val(),
                vehicle_info: $('input[name="vehicle_info"]').val(),
                vehicle_plate: $('input[name="vehicle_plate"]').val(),
                notes: $('textarea[name="notes"]').val(),
                items: JSON.stringify(selectedItems)
            };
            
            // Debug logging
            console.log('Form Data:', formData);
            console.log('Selected Items:', selectedItems);
            
            // Submit delivery
            $.ajax({
                url: '<?= base_url('/purchasing/api/create-delivery') ?>',
                type: 'POST',
                data: formData,
                success: function(response) {
                    console.log('Response:', response);
                    if (response.success) {
                        if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                            OptimaPro.showNotification(response.message || 'Delivery schedule berhasil dibuat', 'success');
                        } else {
                            showNotification(response.message || 'Delivery schedule berhasil dibuat', 'success');
                        }
                        $('#createDeliveryModal').modal('hide');
                        refreshTable();
                    } else {
                        if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                            OptimaPro.showNotification(response.message || 'Gagal membuat delivery schedule', 'error');
                        } else {
                            showNotification(response.message || 'Gagal membuat delivery schedule', 'error');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error:', xhr.responseText);
                    if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                        OptimaPro.showNotification('Terjadi kesalahan saat membuat delivery schedule: ' + error, 'error');
                    } else {
                        showNotification('Terjadi kesalahan saat membuat delivery schedule: ' + error, 'error');
                    }
                }
            });
        });
        
        // SN Assignment Form Handler
        $('#assignSNForm').on('submit', function(e) {
            e.preventDefault();
            
            if (!currentDeliveryId) {
                showNotification('Delivery ID tidak ditemukan', 'error');
                return;
            }
            
            // Collect all serial numbers in array format
            const serialNumbers = [];
            
            // Collect unit SNs
            $('input[name^="sn_mast_"]').each(function() {
                const index = $(this).attr('name').split('_')[2];
                const snMast = $(this).val();
                const snEngine = $(`input[name="sn_engine_${index}"]`).val();
                const snUnit = $(`input[name="serial_number_po_${index}"]`).val();
                
                if (snMast || snEngine || snUnit) {
                    serialNumbers.push({
                        type: 'unit',
                        index: index,
                        sn_mast: snMast,
                        sn_engine: snEngine,
                        serial_number: snUnit  // Unit SN goes to serial_number
                    });
                }
            });
            
            // Collect attachment SNs
            $('input[name^="sn_attachment_"]').each(function() {
                const index = $(this).attr('name').split('_')[2];
                const serialNumber = $(this).val();
                
                if (serialNumber) {
                    serialNumbers.push({
                        type: 'attachment',
                        index: index,
                        serial_number: serialNumber
                    });
                }
            });
            
            // Collect battery SNs
            $('input[name^="sn_battery_"]').each(function() {
                const index = $(this).attr('name').split('_')[2];
                const serialNumber = $(this).val();
                
                if (serialNumber) {
                    serialNumbers.push({
                        type: 'battery',
                        index: index,
                        serial_number: serialNumber
                    });
                }
            });
            
            // Collect charger SNs
            $('input[name^="sn_charger_"]').each(function() {
                const index = $(this).attr('name').split('_')[2];
                const serialNumber = $(this).val();
                
                if (serialNumber) {
                    serialNumbers.push({
                        type: 'charger',
                        index: index,
                        serial_number: serialNumber
                    });
                }
            });
            
            // Submit serial numbers
            $.ajax({
                url: '<?= base_url('/purchasing/api/assign-sn') ?>',
                type: 'POST',
                data: {
                    delivery_id: currentDeliveryId,
                    serial_numbers: JSON.stringify(serialNumbers)
                },
                success: function(response) {
                    if (response.success) {
                        if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                            OptimaPro.showNotification(response.message || 'Serial numbers berhasil disimpan', 'success');
                        } else {
                            showNotification(response.message || 'Serial numbers berhasil disimpan', 'success');
                        }
                        $('#assignSNModal').modal('hide');
                        refreshTable();
                    } else {
                        if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                            OptimaPro.showNotification(response.message || 'Gagal menyimpan serial numbers', 'error');
                        } else {
                            showNotification(response.message || 'Gagal menyimpan serial numbers', 'error');
                        }
                    }
                },
                error: function() {
                    if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                        OptimaPro.showNotification('Terjadi kesalahan saat menyimpan serial numbers', 'error');
                    } else {
                        showNotification('Terjadi kesalahan saat menyimpan serial numbers', 'error');
                    }
                }
            });
        });
    
    // Checklist functions for delivery items
    function selectAllDeliveryItems() {
        // Only select items that are not disabled (not already delivered)
        $('.delivery-item-checkbox:not(:disabled)').prop('checked', true);
        updateDeliverySelection();
    }
    
    function clearAllDeliveryItems() {
        // Clear all items including disabled ones
        $('.delivery-item-checkbox').prop('checked', false);
        updateDeliverySelection();
    }
    
    // Category-specific selection functions
    function selectAllUnits() {
        $('input[data-type="unit"]:not(:disabled)').prop('checked', true);
        updateDeliverySelection();
    }
    
    function clearAllUnits() {
        $('input[data-type="unit"]').prop('checked', false);
        updateDeliverySelection();
    }
    
    function selectAllAttachments() {
        $('input[data-type="attachment"]:not(:disabled)').prop('checked', true);
        updateDeliverySelection();
    }
    
    function clearAllAttachments() {
        $('input[data-type="attachment"]').prop('checked', false);
        updateDeliverySelection();
    }
    
    function selectAllBatteries() {
        $('input[data-type="battery"]:not(:disabled)').prop('checked', true);
        updateDeliverySelection();
    }
    
    function clearAllBatteries() {
        $('input[data-type="battery"]').prop('checked', false);
        updateDeliverySelection();
    }
    
    function selectAllChargers() {
        $('input[data-type="charger"]:not(:disabled)').prop('checked', true);
        updateDeliverySelection();
    }
    
    function clearAllChargers() {
        $('input[data-type="charger"]').prop('checked', false);
        updateDeliverySelection();
    }
    
    function updateDeliverySelection() {
        const selectedItems = $('.delivery-item-checkbox:checked');
        const totalSelected = selectedItems.length;
        
        $('#deliveryTotalSelected').text(totalSelected);
        
        // Enable/disable submit button
        const submitBtn = $('#createDeliveryForm button[type="submit"]');
        if (totalSelected > 0) {
            submitBtn.prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
        } else {
            submitBtn.prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
        }
}
</script>
<?= $this->endSection() ?>
