<?= $this->extend('layouts/base') ?>

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

<?= $this->section('css') ?>
<style>
/* Modal z-index for nested modals */
#customerDetailModal { z-index: 1055 !important; }
#contractDetailModal { z-index: 1065 !important; }
#unitDetailModal { z-index: 1075 !important; }

/* Spesifikasi modals - Above contract detail modal */
#addSpesifikasiModal { z-index: 1080 !important; }
#addAttachmentSpesifikasiModal { z-index: 1080 !important; }

/* SPK modal - Above all other modals */
#spkFromKontrakModal { z-index: 1085 !important; }

/* Sweet Alert z-index - Above all modals */
.swal2-container { z-index: 9999 !important; }
.swal2-popup { z-index: 9999 !important; }
.swal2-backdrop { z-index: 9998 !important; }

/* Notification z-index - Above SPK modal */
.alert, .toast-container, .toast, .notification-container, .notification-popup, .optima-notification, .optima-notification-container, .optima-toast, .optima-alert {
    z-index: 10000 !important;
}

/* Global notification override */
[class*="notification"], [class*="toast"], [class*="alert"] {
    z-index: 10000 !important;
}

/* Enhanced Modal Header Styling - Like Card Headers */
.modal-header {
    background: #f8f9fa !important;
    border-bottom: 1px solid #dee2e6;
    padding: 1rem 1.5rem;
}

.modal-header .modal-title {
    color: #495057 !important;
    font-weight: 700 !important;
    font-size: 1.25rem;
}

.modal-header .text-light {
    color: #6c757d !important;
    font-weight: 500;
    font-size: 0.9rem;
}

.modal-header .btn-close {
    filter: none;
    opacity: 0.7;
}

.modal-header .btn-close:hover {
    opacity: 1;
}

/* Accordion styling */
.accordion-button:not(.collapsed) {
    background-color: #e7f3ff;
    color: #0d6efd;
}

.accordion-body {
    padding: 0;
}

/* Unit row hover */
.unit-row:hover {
    background-color: #f8f9fa !important;
    cursor: pointer;
}

/* Customer card on click */
.customer-card {
    cursor: pointer;
    transition: all 0.3s ease;
}

.customer-card:hover {
    transform: translateY(-2px);
}

/* Smooth Tab Transitions - WITHOUT scroll reset */
.nav-link {
    transition: all 0.3s ease;
}

.tab-content {
    position: relative;
}


/* Smooth content loading for spesifikasi */
#spesifikasiListContract {
    transition: opacity 0.3s ease-in-out;
}

#spesifikasiListContract.loading {
    opacity: 0.5;
}

#spesifikasiListContract .card {
    animation: slideInCard 0.4s ease-out;
}

@keyframes slideInCard {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Stagger animation for multiple cards */
#spesifikasiListContract .card:nth-child(1) { animation-delay: 0.05s; }
#spesifikasiListContract .card:nth-child(2) { animation-delay: 0.1s; }
#spesifikasiListContract .card:nth-child(3) { animation-delay: 0.15s; }
#spesifikasiListContract .card:nth-child(4) { animation-delay: 0.2s; }
#spesifikasiListContract .card:nth-child(5) { animation-delay: 0.25s; }

</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats bg-primary text-white h-100">
            <div class="card-body">
                <h2 class="fw-bold mb-1" id="stat-total-customers">0</h2>
                <h6 class="card-title text-uppercase small">Total Customers</h6>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats bg-success text-white h-100">
            <div class="card-body">
                <h2 class="fw-bold mb-1" id="stat-active-customers">0</h2>
                <h6 class="card-title text-uppercase small">Active Customers</h6>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats bg-warning text-white h-100">
            <div class="card-body">
                <h2 class="fw-bold mb-1" id="stat-total-contracts">0</h2>
                <h6 class="card-title text-uppercase small">Total Contracts</h6>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats bg-info text-white h-100">
            <div class="card-body">
                <h2 class="fw-bold mb-1" id="stat-total-units">0</h2>
                <h6 class="card-title text-uppercase small">Total Units</h6>
            </div>
        </div>
    </div>
</div>

<!-- Customer Table Card -->
<div class="card table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Customer Management</h5>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-success" onclick="openAddCustomerModal()">
                <i class="fas fa-plus"></i> Add Customer
            </button>
            <button class="btn btn-sm btn-outline-secondary" onclick="refreshData()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <?php if ($can_export): ?>
            <a href="<?= base_url('marketing/export_customer') ?>" class="btn btn-sm btn-outline-success">
                <i class="fas fa-file-excel"></i> Export
            </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="customerTable" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>Customer Code</th>
                        <th>Customer Name</th>
                        <th>Area</th>
                        <th>Locations</th>
                        <th>Contracts</th>
                        <th>Total Units</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data loaded via DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Customer Detail Modal -->
<div class="modal fade" id="customerDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <div>
                    <h5 class="modal-title">
                        <i class="fas fa-building me-2"></i><span id="customerName">Customer Details</span>
                    </h5>
                    <small class="text-light" id="customerCode"></small>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light btn-sm" id="printCustomerPDF" title="Print PDF Report">
                        <i class="fas fa-file-pdf me-1"></i>Print PDF
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs mb-3" id="customerDetailTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="company-tab" data-bs-toggle="tab" data-bs-target="#company-content" type="button">
                            <i class="fas fa-building me-1"></i>Company Info
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="locations-tab" data-bs-toggle="tab" data-bs-target="#locations-content" type="button">
                            <i class="fas fa-map-marker-alt me-1"></i>Locations (<span id="locationCount">0</span>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="contracts-tab" data-bs-toggle="tab" data-bs-target="#contracts-content" type="button">
                            <i class="fas fa-file-contract me-1"></i>Contracts (<span id="contractCount">0</span>)
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="customerDetailTabContent">
                    <!-- Company Info Tab -->
                    <div class="tab-pane fade show active" id="company-content" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><strong>Company Information</strong></h6>
                                    </div>
                                    <div class="card-body" id="companyInfo">
                                        <div class="text-center text-muted">Loading...</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><strong>Statistics</strong></h6>
                                    </div>
                                    <div class="card-body" id="customerStats">
                                        <div class="text-center text-muted">Loading...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Locations Tab -->
                    <div class="tab-pane fade" id="locations-content" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><strong>Customer Locations</strong></h6>
                            <button class="btn btn-sm btn-info" onclick="openAddLocationModal()">
                                <i class="fas fa-plus me-1"></i>Add Location
                            </button>
                        </div>
                        <div class="row" id="locationsList">
                            <div class="text-center text-muted">Loading locations...</div>
                        </div>
                    </div>

                    <!-- Contracts Tab -->
                    <div class="tab-pane fade" id="contracts-content" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><strong>Customer Contracts</strong></h6>
                            <button class="btn btn-sm btn-primary" onclick="openAddContractModal()">
                                <i class="fas fa-plus me-1"></i>Add Contract
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="contractsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th>No. Kontrak</th>
                                        <th>No. PO</th>
                                        <th>Location</th>
                                        <th>Periode</th>
                                        <th>Total Units</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Contracts loaded dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Contract Detail Modal -->
<div class="modal fade" id="contractDetailModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <div>
                    <h5 class="modal-title">
                        <i class="fas fa-file-contract me-2"></i><strong>Contract Details</strong>
                    </h5>
                    <small class="text-light" id="contractSubtitle"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Contract Information -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><strong>Contract Information</strong></h6>
                    </div>
                    <div class="card-body" id="contractInfo">
                        <div class="text-center text-muted">Loading...</div>
                    </div>
                </div>

                <!-- Contract Tabs -->
                <ul class="nav nav-tabs" id="contractDetailTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="locations-tab-contract" data-bs-toggle="tab" data-bs-target="#locations-content-contract" type="button">
                            <i class="fas fa-map-marker-alt me-1"></i>Locations & Units
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="spesifikasi-tab-contract" data-bs-toggle="tab" data-bs-target="#spesifikasi-content-contract" type="button">
                            <i class="fas fa-cogs me-1"></i>Request Specification (<span id="spekCountContract">0</span>)
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content mt-3" id="contractDetailTabContent">
                    <!-- Locations & Units Tab -->
                    <div class="tab-pane fade show active" id="locations-content-contract" role="tabpanel">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><strong>Locations & Units</strong></h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="accordion" id="locationsAccordion">
                                    <!-- Locations loaded dynamically -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Spesifikasi Tab -->
                    <div class="tab-pane fade" id="spesifikasi-content-contract" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><strong>Request Spesifikasi untuk dasar pembuatan SPK</strong></h6>
                            <div class="btn-group" role="group">
                                <button class="btn btn-primary btn-sm" onclick="openAddSpesifikasiModal()">
                                    <i class="fas fa-plus me-1"></i>Tambah Unit
                                </button>
                                <button class="btn btn-success btn-sm" onclick="openAddAttachmentSpesifikasiModal()">
                                    <i class="fas fa-puzzle-piece me-1"></i>Tambah Attachment
                                </button>
                            </div>
                        </div>
                        <br>

                        <div id="spesifikasiListContract">
                            <p class="text-muted">Memuat spesifikasi...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Unit Detail Modal -->
<div class="modal fade" id="unitDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">
                        <i class="fas fa-truck me-2"></i><strong>Unit Details</strong>
                    </h5>
                    <small class="text-light" id="unitSubtitle"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="unitDetailContent">
                <div class="text-center text-muted">Loading...</div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Attachment Spesifikasi Modal -->
<div class="modal fade" id="addAttachmentSpesifikasiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Tambah Spesifikasi Attachment</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addAttachmentSpesifikasiForm" method="post" action="javascript:void(0)">
                <div class="modal-body">
                    <input type="hidden" name="kontrak_id" id="attachmentSpekKontrakId">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipe Attachment <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="attachment_tipe" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Merk Attachment</label>
                            <input type="text" class="form-control" name="attachment_merk" placeholder="Sesuai Kebutuhan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Dibutuhkan <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="jumlah_dibutuhkan" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Departemen</label>
                            <input type="text" class="form-control" name="nama_departemen">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Harga Bulanan <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="harga_per_unit_bulanan" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Harga Harian</label>
                            <input type="number" class="form-control" name="harga_per_unit_harian" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan Spesifikasi</label>
                            <textarea class="form-control" name="catatan_spek" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Simpan Attachment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCustomerForm">
                <div class="modal-body">
                    <!-- Company Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer_code">Customer Code <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="customer_code" name="customer_code" required maxlength="20">
                                    <button class="btn btn-outline-secondary" type="button" id="generateCustomerCode" title="Generate Customer Code">
                                        <i class="fas fa-magic"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">Unique customer identifier - bisa diisi manual atau generate otomatis</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer_name">Customer Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" required maxlength="255">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Primary Location & Contact -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="location_name">Primary Location Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="location_name" name="location_name" value="Head Office" required maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="primary_location_code">Primary Location Code</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="primary_location_code" name="primary_location_code" maxlength="50">
                                    <button class="btn btn-outline-secondary" type="button" id="generateLocationCode" title="Generate Location Code">
                                        <i class="fas fa-magic"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">Optional - will be auto-generated if empty</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="area_id">Area <span class="text-danger">*</span></label>
                                <select class="form-control" id="area_id" name="area_id" required>
                                    <option value="">Select Area</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="city">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="city" name="city" maxlength="100" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact_person">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phone" name="phone" maxlength="20" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="postal_code">Postal Code</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" maxlength="10">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Primary Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="address" name="address" rows="3" maxlength="500" required></textarea>
                        <small class="form-text text-muted">This will be created as the primary location</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="province">Province <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="province" name="province" maxlength="100" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="postal_code">Postal Code</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" maxlength="10">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Description</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>

                    <!-- Defaults required by backend validation -->
                    <input type="hidden" name="is_active" value="1">
                    <input type="hidden" name="location_type" value="HEAD_OFFICE">
                    <input type="hidden" name="pic_position" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Contract Modal -->
<div class="modal fade" id="addContractModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Tambah Kontrak Baru</h5>
                    <small class="text-muted">Langkah 1: Informasi Dasar Kontrak</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addContractForm">
                <div class="modal-body">
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>Alur Pembuatan Kontrak Baru
                        </h6>
                        <ol class="mb-0 ps-3">
                            <li><strong>Langkah 1:</strong> Isi informasi dasar kontrak (form ini)</li>
                            <li><strong>Langkah 2:</strong> Tambahkan spesifikasi unit yang dibutuhkan</li>
                            <li><strong>Langkah 3:</strong> Buat SPK untuk mengalokasikan unit dari inventory</li>
                        </ol>
                        <hr>
                        <small class="text-muted">
                            <i class="fas fa-lightbulb me-1"></i>
                            <strong>Tips:</strong> Nilai kontrak dan total unit akan dihitung otomatis berdasarkan spesifikasi yang ditambahkan.
                        </small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Kontrak*</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="contract_number" required>
                                <button class="btn btn-outline-secondary" type="button" id="generateContractNumber" title="Generate Nomor Kontrak">
                                    <i class="fas fa-magic"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3"><label class="form-label">No. PO Klien</label><input type="text" class="form-control" name="po_number"></div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Customer*</label>
                            <select class="form-select" id="customerSelect" required>
                                <option value="">-- Pilih Customer --</option>
                            </select>
                            <small class="form-text text-muted">Pilih customer terlebih dahulu</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lokasi*</label>
                            <select class="form-select" name="customer_location_id" id="locationSelect" required disabled>
                                <option value="">-- Pilih Customer Dulu --</option>
                            </select>
                            <small class="form-text text-muted">Lokasi akan muncul setelah pilih customer</small>
                        </div>
                        <div class="col-md-6 mb-3"><label class="form-label">Tanggal Mulai*</label><input type="date" class="form-control" name="start_date" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Tanggal Berakhir*</label><input type="date" class="form-control" name="end_date" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Jenis Sewa</label>
                            <select class="form-select" name="jenis_sewa">
                                <option value="BULANAN" selected>Bulanan</option>
                                <option value="HARIAN">Harian</option>
                            </select>
                        </div>
                        <div class="col-md-6"></div> <!-- Empty space for alignment -->
                        <div class="col-12 mb-3"><label class="form-label">Catatan</label><textarea class="form-control" name="catatan" rows="3" placeholder="Catatan tambahan (opsional)"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="submit_action" id="submitAction" value="save_and_spec">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btnSaveAndSpec" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan & Lanjut ke Spesifikasi
                    </button>
                    <button type="button" id="btnSaveOnly" class="btn btn-outline-primary">Simpan Kontrak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Location Modal -->
<div class="modal fade" id="addLocationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addLocationForm">
                <div class="modal-body">
                    <input type="hidden" name="customer_id" id="locationCustomerId">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="loc_location_name">Location Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="loc_location_name" name="location_name" required maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="loc_location_code">Location Code</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="loc_location_code" name="location_code" maxlength="50">
                                    <button class="btn btn-outline-secondary" type="button" id="generateLocationCodeModal" title="Generate Location Code">
                                        <i class="fas fa-magic"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">Optional - will be auto-generated if empty</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="loc_area_id">Area <span class="text-danger">*</span></label>
                                <select class="form-control" id="loc_area_id" name="area_id" required>
                                    <option value="">Select Area</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="loc_contact_person">Contact Person</label>
                                <input type="text" class="form-control" id="loc_contact_person" name="contact_person" maxlength="255">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="loc_phone">Phone</label>
                                <input type="text" class="form-control" id="loc_phone" name="phone" maxlength="20">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="loc_email">Email</label>
                                <input type="email" class="form-control" id="loc_email" name="email" maxlength="128">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="loc_address">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="loc_address" name="address" rows="3" maxlength="500" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="loc_city">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="loc_city" name="city" required maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="loc_province">Province <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="loc_province" name="province" required maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="loc_postal_code">Postal Code</label>
                                <input type="text" class="form-control" id="loc_postal_code" name="postal_code" maxlength="10">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="loc_notes">Notes</label>
                        <textarea class="form-control" id="loc_notes" name="notes" rows="2" maxlength="255"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="loc_is_primary" name="is_primary" value="1">
                            <label class="form-check-label" for="loc_is_primary">
                                Set as Primary Location
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Location</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Spesifikasi Modal -->
<div class="modal fade" id="addSpesifikasiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Tambah Spesifikasi Unit</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSpesifikasiForm" method="post" action="javascript:void(0)">
                <div class="modal-body">
                    <input type="hidden" name="kontrak_id" id="spekKontrakId">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Unit Dibutuhkan</label>
                            <input type="number" class="form-control" name="jumlah_dibutuhkan" min="1" value="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Spesifikasi Unit</label>
                            <input type="text" class="form-control" name="catatan_spek" placeholder="Opsional">
                            <small class="text-muted">Masukan keterangan, misalnya "Spesifikasi 1", "Unit Spare", "Tambahan Unit", dst.</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Harga Sewa Bulanan <span class="text-danger" id="hargaRequired">*</span></label>
                            <input type="number" class="form-control" name="harga_per_unit_bulanan" step="0.01" placeholder="Rp per unit per bulan" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Harga Sewa Harian</label>
                            <input type="number" class="form-control" name="harga_per_unit_harian" step="0.01" placeholder="Rp per unit per hari">
                        </div>
                        
                        <div class="col-12"><hr><h6>Spesifikasi Teknis</h6></div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Departemen <span class="text-danger">*</span></label>
                            <select class="form-select" name="departemen_id" id="spekDepartemen" required></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tipe Unit <span class="text-danger">*</span></label>
                            <select class="form-select" name="tipe_unit_id" id="spekTipeUnit" required>
                                <option value="">-- Pilih Tipe Unit --</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Kapasitas</label>
                            <select class="form-select" name="kapasitas_id" id="spekKapasitas">
                                <option value="">-- Pilih Kapasitas --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Merk Unit</label>
                            <select class="form-select" name="merk_unit" id="spekMerkUnit">
                                <option value="">-- Pilih Merk --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Baterai</label>
                            <select class="form-select" name="jenis_baterai" id="spekJenisBaterai">
                                <option value="">-- Pilih Baterai --</option>
                            </select>
                            <small class="text-muted">Hanya tersedia untuk unit Electric</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Charger</label>
                            <select class="form-select" name="charger_id" id="spekCharger"></select>
                            <small class="text-muted">Hanya tersedia untuk unit Electric</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Attachment Tipe</label>
                            <select class="form-select" name="attachment_tipe" id="spekAttachmentTipe"></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Valve</label>
                            <select class="form-select" name="valve_id" id="spekValve"></select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Mast</label>
                            <select class="form-select" name="mast_id" id="spekMast"></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ban</label>
                            <select class="form-select" name="ban_id" id="spekBan"></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Roda</label>
                            <select class="form-select" name="roda_id" id="spekRoda"></select>
                        </div>
                        

                        
                        <!-- Accessories Section -->
                        <div class="col-12"><hr><h6>Aksesoris Unit</h6></div>
                        <div class="col-12">
                            <div class="row g-2">
                                <!-- Row 1 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="LAMPU UTAMA" id="acc_lampu_utama">
                                        <label class="form-check-label" for="acc_lampu_utama">Lampu</label>
                                        <small class="text-muted">(Utama, Mundur, Sign, Stop)</small>
                                    </div>
                                </div>
                                
                                <!-- Row 2 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="BLUE SPOT" id="acc_blue_spot">
                                        <label class="form-check-label" for="acc_blue_spot">Blue Spot</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="RED LINE" id="acc_red_line">
                                        <label class="form-check-label" for="acc_red_line">Red Line</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="WORK LIGHT" id="acc_work_light">
                                        <label class="form-check-label" for="acc_work_light">Work Light</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="ROTARY LAMP" id="acc_rotary_lamp">
                                        <label class="form-check-label" for="acc_rotary_lamp">Rotary Lamp</label>
                                    </div>
                                </div>
                                
                                <!-- Row 3 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="BACK BUZZER" id="acc_back_buzzer">
                                        <label class="form-check-label" for="acc_back_buzzer">Back Buzzer</label>
                                    </div>

                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="CAMERA AI" id="acc_camera_ai">
                                        <label class="form-check-label" for="acc_camera_ai">Camera AI</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="CAMERA" id="acc_camera">
                                        <label class="form-check-label" for="acc_camera">Camera</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="SENSOR PARKING" id="acc_sensor_parking">
                                        <label class="form-check-label" for="acc_sensor_parking">Sensor Parking</label>
                                    </div>
                                </div>
                                
                                <!-- Row 4 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="SPEED LIMITER" id="acc_speed_limiter">
                                        <label class="form-check-label" for="acc_speed_limiter">Speed Limiter</label>
                                    </div>

                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="LASER FORK" id="acc_laser_fork">
                                        <label class="form-check-label" for="acc_laser_fork">Laser Fork</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="VOICE ANNOUNCER" id="acc_voice_announcer">
                                        <label class="form-check-label" for="acc_voice_announcer">Voice Announcer</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="HORN SPEAKER" id="acc_horn_speaker">
                                        <label class="form-check-label" for="acc_horn_speaker">Horn Speaker</label>
                                    </div>
                                </div>
                                
                                <!-- Row 5 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="HORN KLASON" id="acc_horn_klason">
                                        <label class="form-check-label" for="acc_horn_klason">Horn Klason</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="BIO METRIC" id="acc_bio_metric">
                                        <label class="form-check-label" for="acc_bio_metric">Bio Metric</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="ACRYLIC" id="acc_acrylic">
                                        <label class="form-check-label" for="acc_acrylic">Acrylic</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="P3K" id="acc_p3k">
                                        <label class="form-check-label" for="acc_p3k">P3K</label>
                                    </div>
                                </div>
                                
                                <!-- Row 6 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="SAFETY BELT INTERLOC" id="acc_safety_belt">
                                        <label class="form-check-label" for="acc_safety_belt">Safety Belt Interloc</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="SPARS ARRESTOR" id="acc_spars_arrestor">
                                        <label class="form-check-label" for="acc_spars_arrestor">Spars Arrestor</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitSpesifikasiBtn">Simpan Spesifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal SPK dari Kontrak -->
<div class="modal fade" id="spkFromKontrakModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Buat SPK</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="spkFromKontrakForm">
                <div class="modal-body">
                    <input type="hidden" name="kontrak_id" id="spkKontrakId">
                    <input type="hidden" name="kontrak_spesifikasi_id" id="spkSpesifikasiId">
                    <div class="mb-3">
                        <label class="form-label">Jenis SPK</label>
                        <select class="form-select" name="jenis_spk" id="spkJenisSpk" required>
                            <option value="UNIT" selected>SPK Unit</option>
                            <option value="ATTACHMENT">SPK Attachment</option>
                        </select>
                    </div>
                    
                    <!-- Target Unit Section (hanya untuk ATTACHMENT) -->
                    <div id="attachmentTargetSection" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Unit Tujuan <span class="text-danger">*</span></label>
                            <select class="form-control" name="target_unit_id" id="spkTargetUnitId">
                                <option value="">- Pilih Unit Tujuan -</option>
                            </select>
                            <div class="form-text">Pilih unit yang akan menerima attachment pengganti</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Alasan Penggantian</label>
                            <textarea class="form-control" name="replacement_reason" id="spkReplacementReason" rows="2" 
                                      placeholder="Contoh: Fork rusak, attachment lama aus, perlu upgrade, dll"></textarea>
                            <div class="form-text">Jelaskan mengapa attachment perlu diganti</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Pelanggan</label>
                        <input type="text" class="form-control" name="pelanggan" id="spkPelanggan" readonly required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">PIC</label>
                        <input type="text" class="form-control" name="pic" id="spkPic" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kontak</label>
                        <input type="text" class="form-control" name="kontak" id="spkKontak" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" class="form-control" name="lokasi" id="spkLokasi" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Delivery Plan</label>
                        <input type="date" class="form-control" name="delivery_plan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Unit <small class="text-muted" id="jumlahUnitHint"></small></label>
                        <input type="number" class="form-control" name="jumlah_unit" id="spkJumlahUnit" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" name="catatan" rows="3" placeholder="Keterangan tambahan untuk SPK ini (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button>
                    <button class="btn btn-primary" type="submit">Buat SPK</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>

<script>
let customerTable;
let currentCustomerId = null;
let currentContractId = null;

$(document).ready(function() {
    console.log('🚀 Initializing Customer Management...');
    
    // Initialize DataTable
    initializeCustomerTable();
    
    // Load statistics
    loadStatistics();
    
    // Setup tab event handlers
    setupTabHandlers();
});

// Initialize DataTable
function initializeCustomerTable() {
    customerTable = $('#customerTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('marketing/customer-management/getCustomers') ?>',
            type: 'POST'
        },
        columns: [
            { 
                data: 'customer_code', 
                name: 'customer_code',
                render: function(data, type, row) {
                    return `<strong class="text-primary">${data || '-'}</strong>`;
                }
            },
            { 
                data: 'customer_name', 
                name: 'customer_name',
                render: function(data, type, row) {
                    return `<div class="fw-bold">${data || '-'}</div>`;
                }
            },
            { 
                data: 'area_name', 
                name: 'area_name',
                render: function(data, type, row) {
                    if (!data) return '<span class="text-muted">-</span>';
                    // Split multiple areas if exists
                    const areas = data.split(', ');
                    if (areas.length > 2) {
                        return `<span class="badge bg-light text-dark me-1">${areas[0]}</span>` +
                               `<span class="badge bg-secondary text-white">+${areas.length - 1}</span>`;
                    }
                    return areas.map(area => 
                        `<span class="badge bg-light text-dark me-1">${area}</span>`
                    ).join('');
                }
            },
            { 
                data: 'locations_count', 
                name: 'locations_count',
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    const count = data || 0;
                    return `<span class="badge bg-info">${count}</span>`;
                }
            },
            { 
                data: 'contracts_count', 
                name: 'contracts_count',
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    const count = data || 0;
                    return `<span class="badge bg-success">${count}</span>`;
                }
            },
            { 
                data: 'total_units', 
                name: 'total_units',
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    const count = data || 0;
                    return `<span class="badge bg-primary">${count}</span>`;
                }
            },
            { 
                data: 'is_active', 
                name: 'is_active',
                className: 'text-center',
                render: function(data, type, row) {
                    const isActive = data == 1;
                    const badgeClass = isActive ? 'bg-success' : 'bg-danger';
                    const text = isActive ? 'ACTIVE' : 'INACTIVE';
                    return `<span class="badge ${badgeClass}">${text}</span>`;
                }
            },
            { 
                data: 'created_at', 
                name: 'created_at',
                render: function(data, type, row) {
                    if (!data) return '<span class="text-muted">-</span>';
                    const date = new Date(data);
                    return `<small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        ${date.toLocaleDateString('id-ID')}
                    </small>`;
                }
            }
        ],
        pageLength: 25,
        order: [[1, 'asc']],
        rowCallback: function(row, data) {
            $(row).css('cursor', 'pointer');
            $(row).off('click').on('click', function() {
                openCustomerDetail(data.id);
            });
        }
    });
}

// Load statistics
function loadStatistics() {
    $.ajax({
        url: '<?= base_url('marketing/customer-management/getCustomerStats') ?>',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const stats = response.data;
                $('#stat-total-customers').text(stats.total_customers || 0);
                $('#stat-active-customers').text(stats.active_customers || 0);
                $('#stat-total-contracts').text(stats.total_contracts || 0);
                $('#stat-total-units').text(stats.total_units || 0);
            }
        }
    });
}

// Open customer detail
function openCustomerDetail(customerId) {
    currentCustomerId = customerId;
    
    // Load customer data using Marketing controller endpoint
    $.ajax({
        url: `<?= base_url('marketing/customer-management/getCustomerDetail') ?>/${customerId}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                displayCustomerDetail(response.data);
                $('#customerDetailModal').modal('show');
            } else {
                showNotification('Failed to load customer details', 'error');
            }
        },
        error: function() {
            showNotification('Error loading customer details', 'error');
        }
    });
}

// Display customer detail
function displayCustomerDetail(data) {
    // Extract data from response structure
    const customer = data.customer || data;
    const stats = data.stats || {};
    const locations = data.locations || [];
    const contracts = data.contracts || [];
    
    // Update modal title
    $('#customerName').text(customer.customer_name || 'Customer Details');
    $('#customerCode').text(customer.customer_code || '');
    
    // Reset tab states - only affect modal tabs, not sidebar
    $('#customerDetailTabs .nav-link').removeClass('active');
    $('#customerDetailTabContent .tab-pane').removeClass('show active').hide();
    
    // Set company tab as active
    $('#company-tab').addClass('active');
    $('#company-content').addClass('show active').show();
    
    // Company Info (Area dihapus karena tidak relevan - setiap lokasi punya area sendiri)
    const companyHtml = `
        <table class="table table-sm table-borderless">
            <tr><td><strong>Customer Code:</strong></td><td>${customer.customer_code || 'N/A'}</td></tr>
            <tr><td><strong>Customer Name:</strong></td><td>${customer.customer_name || 'N/A'}</td></tr>
            <tr><td><strong>Created:</strong></td><td>${customer.created_at ? new Date(customer.created_at).toLocaleDateString('id-ID') : 'N/A'}</td></tr>
            <tr><td><strong>Last Updated:</strong></td><td>${customer.updated_at ? new Date(customer.updated_at).toLocaleDateString('id-ID') : 'N/A'}</td></tr>
            <tr><td><strong>Status:</strong></td><td>
                <span class="badge bg-${customer.is_active == 1 ? 'success' : 'secondary'}">
                    ${customer.is_active == 1 ? 'ACTIVE' : 'INACTIVE'}
                </span>
            </td></tr>
        </table>
    `;
    $('#companyInfo').html(companyHtml);
    
    // Customer Stats
    const statsHtml = `
        <table class="table table-sm table-borderless">
            <tr><td><strong>Total Locations:</strong></td><td><span class="badge bg-info">${stats.total_locations || 0}</span></td></tr>
            <tr><td><strong>Total Contracts:</strong></td><td><span class="badge bg-success">${stats.total_contracts || 0}</span></td></tr>
            <tr><td><strong>Total POs:</strong></td><td><span class="badge bg-warning text-dark">${contracts.filter(c => c.no_po_marketing).length || 0}</span></td></tr>
        </table>
    `;
    $('#customerStats').html(statsHtml);
    
    // Update tab badges
    $('#locationCount').text(stats.total_locations || 0);
    $('#contractCount').text(stats.total_contracts || 0);
    
    // Load contracts when tab is clicked (will be loaded when tab is shown)
}

// Load customer contracts
function loadCustomerContracts(customerId) {
    $.ajax({
        url: `<?= base_url('marketing/customer-management/getCustomerContracts') ?>/${customerId}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                displayContracts(response.data);
                $('#contractCount').text(response.data.length);
            } else {
                $('#contractsTable tbody').html('<tr><td colspan="6" class="text-center text-muted">No contracts found</td></tr>');
            }
        },
        error: function() {
            $('#contractsTable tbody').html('<tr><td colspan="6" class="text-center text-danger">Error loading contracts</td></tr>');
        }
    });
}

// Display contracts
function displayContracts(contracts) {
    let html = '';
    
    contracts.forEach(contract => {
        const statusBadge = getStatusBadge(contract.status);
        html += `
            <tr style="cursor: pointer;" onclick="openContractDetail(${contract.id})">
                <td><strong>${contract.no_kontrak}</strong></td>
                <td>${contract.no_po_marketing || '-'}</td>
                <td>${contract.lokasi || '-'}</td>
                <td>${contract.tanggal_mulai} - ${contract.tanggal_selesai}</td>
                <td class="text-center"><span class="badge bg-primary">${contract.total_units || 0}</span></td>
                <td>${statusBadge}</td>
            </tr>
        `;
    });
    
    $('#contractsTable tbody').html(html || '<tr><td colspan="6" class="text-center text-muted">No contracts found</td></tr>');
}

// Open contract detail
function openContractDetail(contractId) {
    currentContractId = contractId;
    
    $.ajax({
        url: `<?= base_url('marketing/kontrak/detail') ?>/${contractId}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                displayContractDetail(response.data);
                $('#contractDetailModal').modal('show');
            }
        }
    });
}

// Display contract detail - CLEAN DESIGN
function displayContractDetail(contract) {
    $('#contractSubtitle').text(`${contract.no_kontrak} - ${contract.customer_name}`);
    
    const contractHtml = `
        <div class="row g-3">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr><td><strong>No. Kontrak:</strong></td><td>${contract.no_kontrak || '-'}</td></tr>
                    <tr><td><strong>No. PO Marketing:</strong></td><td>${contract.no_po_marketing || '-'}</td></tr>
                    <tr><td><strong>Customer:</strong></td><td>${contract.customer_name || '-'}</td></tr>
                    <tr><td><strong>Lokasi:</strong></td><td>${contract.location_name || '-'}</td></tr>
                    <tr><td><strong>PIC:</strong></td><td>${contract.contact_person || '-'}</td></tr>
                    <tr><td><strong>Kontak:</strong></td><td>${contract.phone || '-'}</td></tr>
                    <tr><td><strong>Alamat:</strong></td><td>${contract.address || 'Alamat belum tersedia'}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    
                    <tr><td><strong>Jenis Sewa:</strong></td><td><span class="badge bg-info">${contract.jenis_sewa || 'BULANAN'}</span></td></tr>
                    <tr><td><strong>Status:</strong></td><td>${getStatusBadge(contract.status)}</td></tr>
                    <tr><td><strong>Tanggal Mulai:</strong></td><td>${contract.tanggal_mulai || '-'}</td></tr>
                    <tr><td><strong>Tanggal Berakhir:</strong></td><td>${contract.tanggal_berakhir || '-'}</td></tr>
                    <tr><td><strong>Total Unit:</strong></td><td><span class="fw-bold text-primary" id="contractTotalUnits">${contract.total_units || 0}</span></td></tr>
                    <tr><td><strong>Nilai Total:</strong></td><td><span class="fw-bold text-success">Rp ${formatNumber(contract.nilai_total || 0)}</span></td></tr>
                    <tr><td><strong>Dibuat Oleh:</strong></td><td>${contract.dibuat_oleh_nama || '-'}</td></tr>
                    <tr><td><strong>Dibuat Pada:</strong></td><td>${contract.dibuat_pada || '-'}</td></tr>
                </table>
            </div>
        </div>
    `;
    $('#contractInfo').html(contractHtml);
    
    // Clear cached content when opening a NEW contract (to force fresh data)
    // This ensures data is reloaded when switching between different contracts
    $('#locationsAccordion').html('');
    $('#spesifikasiListContract').html('');
    
    // Load units for this contract (only for the currently active tab)
    loadContractUnits(currentContractId);
    
    // DON'T auto-load spesifikasi here - let user click tab to load it
    // This prevents unnecessary data loading and preserves scroll position when switching tabs
}

// Load contract units grouped by location - using same endpoint as kontrak.php
function loadContractUnits(contractId) {
    $.ajax({
        url: `<?= base_url('marketing/kontrak/units/') ?>${contractId}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                displayUnitsAccordion(response.data);
            } else {
                console.error('Failed to load units:', response.message);
                $('#locationsAccordion').html('<div class="alert alert-warning">Tidak ada unit ditemukan untuk kontrak ini.</div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading units:', error);
            $('#locationsAccordion').html('<div class="alert alert-danger">Gagal memuat data unit.</div>');
        }
    });
}

// Display units in accordion by location - ENHANCED
function displayUnitsAccordion(units) {
    // Group units by location with better handling
    const locationGroups = {};
    
    units.forEach(unit => {
        // Better location handling
        let locationKey = unit.lokasi || unit.location_name || unit.alamat || 'Lokasi Utama';
        
        // If still empty, try to get from contract data
        if (locationKey === 'Lokasi Utama' && unit.contract_location) {
            locationKey = unit.contract_location;
        }
        
        if (!locationGroups[locationKey]) {
            locationGroups[locationKey] = [];
        }
        locationGroups[locationKey].push(unit);
    });
    
    let html = '';
    let index = 0;
    
    for (const [location, locationUnits] of Object.entries(locationGroups)) {
        html += `
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading${index}">
                    <button class="accordion-button ${index > 0 ? 'collapsed' : ''}" type="button" 
                            data-bs-toggle="collapse" data-bs-target="#collapse${index}">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <strong>${location}</strong>
                        <span class="badge bg-primary ms-2">${locationUnits.length} UNITS</span>
                    </button>
                </h2>
                <div id="collapse${index}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" 
                     data-bs-parent="#locationsAccordion">
                    <div class="accordion-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>No Unit</th>
                                    <th>Merk/Model</th>
                                    <th>Kapasitas</th>
                                    <th>Jenis Unit</th>
                                    <th>Departemen</th>
                                    <th>Harga Bulanan</th>
                                    <th>Harga Harian</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
        `;
        
        locationUnits.forEach(unit => {
            html += `
                <tr class="unit-row" onclick="showUnitDetail(${unit.id})">
                    <td><strong>${unit.no_unit || '-'}</strong></td>
                    <td>${unit.merk || '-'} ${unit.model || ''}</td>
                    <td>${unit.kapasitas || '-'}</td>
                    <td>${unit.jenis_unit || '-'}</td>
                    <td>${unit.departemen || '-'}</td>
                    <td class="text-success fw-bold">Rp ${formatNumber(unit.harga_per_unit_bulanan || unit.harga_bulanan || 0)}</td>
                    <td class="text-info fw-bold">Rp ${formatNumber(unit.harga_per_unit_harian || unit.harga_harian || 0)}</td>
                    <td><span class="badge bg-success">${unit.status || 'TERSEDIA'}</span></td>
                </tr>
            `;
        });
        
        html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
        index++;
    }
    
    $('#locationsAccordion').html(html || '<div class="text-center text-muted p-3">No units found</div>');
    
    // Update total units count in contract detail
    const totalUnits = units.length;
    $('#contractTotalUnits').text(totalUnits);
}

// Load contract spesifikasi - same as kontrak.php
function loadContractSpesifikasi(kontrakId) {
    console.log('=== loadContractSpesifikasi START ===');
    console.log('kontrakId:', kontrakId);
    console.log('typeof kontrakId:', typeof kontrakId);
    
    const container = document.getElementById('spesifikasiListContract');
    if (!container) {
        console.error('spesifikasiListContract container not found!');
        return;
    }
    
    console.log('Container found:', container);
    console.log('Setting loading message...');
    
    // Add loading class for smooth fade
    container.classList.add('loading');
    container.innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><p class="text-muted mt-3">Memuat spesifikasi...</p></div>';
    
    const url = `<?= base_url('marketing/kontrak/spesifikasi/') ?>${kontrakId}`;
    console.log('Fetching URL:', url);
    console.log('Base URL:', '<?= base_url('marketing/kontrak/spesifikasi/') ?>');
    
    fetch(url)
        .then(response => {
            console.log('Response status:', response.status, response.statusText);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(j => {
            console.log('Spesifikasi response:', j);
            if (!j.success) {
                console.error('API returned error:', j.message);
                container.classList.remove('loading');
                container.innerHTML = '<div class="alert alert-danger">Gagal memuat spesifikasi: ' + (j.message || 'Unknown error') + '</div>';
                return;
            }
            
            const spesifikasi = j.data || [];
            const summary = j.summary || {};
            
            console.log('Processing spesifikasi data, count:', spesifikasi.length);
            
            // Update tab counter
            const spekCountElement = document.getElementById('spekCountContract');
            if (spekCountElement) {
                spekCountElement.textContent = spesifikasi.length;
            }
            
            // Small delay for smooth transition
            setTimeout(() => {
                container.classList.remove('loading');
                
                if (spesifikasi.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-cogs fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">Belum ada spesifikasi</h5>
                            <p class="text-muted">Klik "Tambah Unit" atau "Tambah Attachment" untuk menambahkan spesifikasi.</p>
                        </div>
                    `;
                    return;
                }
                
                // Call displayContractSpesifikasiCorrect to render properly
                displayContractSpesifikasiCorrect(spesifikasi);
            }, 200);
        })
        .catch(error => {
            console.error('Error loading spesifikasi:', error);
            container.classList.remove('loading');
            container.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Gagal memuat spesifikasi: ' + error.message + '</div>';
        });
}

// Load all spesifikasi for a customer (from all contracts)
function loadCustomerSpesifikasi(customerId) {
    console.log('=== loadCustomerSpesifikasi START ===');
    console.log('customerId:', customerId);
    
    const container = document.getElementById('spesifikasiList');
    if (!container) {
        console.error('spesifikasiList container not found!');
        return;
    }
    
    console.log('Container found:', container);
    container.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat spesifikasi...</div>';
    
    // First get all contracts for this customer
    $.ajax({
        url: `<?= base_url('marketing/customer-management/getCustomerContracts') ?>/${customerId}`,
        type: 'GET',
        success: function(contractsResponse) {
            if (contractsResponse.success && contractsResponse.data.length > 0) {
                // Get all spesifikasi from all contracts
                let allSpesifikasi = [];
                let contractsProcessed = 0;
                
                contractsResponse.data.forEach(contract => {
                    $.ajax({
                        url: `<?= base_url('marketing/kontrak/spesifikasi/') ?>${contract.id}`,
                        type: 'GET',
                        success: function(spesifikasiResponse) {
                            if (spesifikasiResponse.success && spesifikasiResponse.data) {
                                // Add contract info to each spesifikasi
                                spesifikasiResponse.data.forEach(spek => {
                                    spek.contract_info = {
                                        no_kontrak: contract.no_kontrak,
                                        no_po: contract.no_po_marketing,
                                        location: contract.location_name
                                    };
                                });
                                allSpesifikasi = allSpesifikasi.concat(spesifikasiResponse.data);
                            }
                            
                            contractsProcessed++;
                            if (contractsProcessed === contractsResponse.data.length) {
                                // All contracts processed, display results
                                displayCustomerSpesifikasi(allSpesifikasi);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading spesifikasi for contract:', contract.id, error);
                            contractsProcessed++;
                            if (contractsProcessed === contractsResponse.data.length) {
                                displayCustomerSpesifikasi(allSpesifikasi);
                            }
                        }
                    });
                });
            } else {
                // No contracts found
                displayCustomerSpesifikasi([]);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading contracts:', error);
            container.innerHTML = '<div class="alert alert-danger">Gagal memuat kontrak customer</div>';
        }
    });
}

// Load contract spesifikasi with correct display - EXACT COPY from customer management
function loadContractSpesifikasiCorrect(kontrakId) {
    console.log('loadContractSpesifikasiCorrect called with kontrakId:', kontrakId);
    const container = document.getElementById('spesifikasiListContract');
    if (!container) {
        console.error('spesifikasiListContract container not found!');
        return;
    }
    
    console.log('Container found, setting loading message...');
    container.innerHTML = '<p class="text-muted">Memuat spesifikasi...</p>';
    
    const url = `<?= base_url('marketing/kontrak/spesifikasi/') ?>${kontrakId}`;
    console.log('Fetching URL:', url);
    
    fetch(url)
        .then(response => {
            console.log('Response status:', response.status, response.statusText);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(j => {
            console.log('Spesifikasi response:', j);
            if (!j.success) {
                console.error('API returned error:', j.message);
                container.innerHTML = '<div class="text-danger">Gagal memuat spesifikasi: ' + (j.message || 'Unknown error') + '</div>';
                return;
            }
            
            const spesifikasi = j.data || [];
            const summary = j.summary || {};
            
            console.log('Processing spesifikasi data, count:', spesifikasi.length);
            
            // Update tab counter
            const spekCountElement = document.getElementById('spekCountContract');
            if (spekCountElement) {
                spekCountElement.textContent = spesifikasi.length;
            }
            
            if (spesifikasi.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-clipboard-list fa-3x text-muted"></i>
                        </div>
                        <h5 class="text-muted">Belum Ada Spesifikasi</h5>
                        <p class="text-muted mb-4">
                            Kontrak ini belum memiliki spesifikasi unit yang dibutuhkan.<br>
                            Tambahkan spesifikasi untuk menentukan jenis unit, jumlah, dan harga yang diperlukan.
                        </p>
                        <div class="d-flex flex-column align-items-center gap-2">
                            <button class="btn btn-primary btn-lg" onclick="openAddSpesifikasiModal()">
                                <i class="fas fa-plus me-2"></i>Tambah Spesifikasi Pertama
                            </button>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Setelah menambah spesifikasi, nilai kontrak akan dihitung otomatis
                            </small>
                        </div>
                    </div>
                `;
                return;
            }
            
            displayContractSpesifikasiCorrect(spesifikasi);
        })
        .catch(error => {
            console.error('Error loading specifications:', error);
            container.innerHTML = '<div class="text-danger">Gagal memuat spesifikasi: ' + error.message + '</div>';
        });
}

// Display contract spesifikasi with correct layout - EXACT COPY from customer management
function displayContractSpesifikasiCorrect(spesifikasi) {
    const container = document.getElementById('spesifikasiListContract');
    const spekCountElement = document.getElementById('spekCountContract');
    
    // Update tab counter
    if (spekCountElement) {
        spekCountElement.textContent = spesifikasi.length;
    }
    
    if (spesifikasi.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-clipboard-list fa-3x text-muted"></i>
                </div>
                <h5 class="text-muted">Belum Ada Spesifikasi</h5>
                <p class="text-muted mb-4">
                    Kontrak ini belum memiliki spesifikasi unit yang dibutuhkan.<br>
                    Tambahkan spesifikasi untuk menentukan jenis unit, jumlah, dan harga yang diperlukan.
                </p>
                <div class="d-flex flex-column align-items-center gap-2">
                    <button class="btn btn-primary btn-lg" onclick="openAddSpesifikasiModal()">
                        <i class="fas fa-plus me-2"></i>Tambah Spesifikasi Pertama
                    </button>
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Setelah menambah spesifikasi, nilai kontrak akan dihitung otomatis
                    </small>
                </div>
            </div>
        `;
        return;
    }
    
    let html = ``;
    
    spesifikasi.forEach((spek, index) => {
        console.log(`Processing spek ${index + 1}:`, spek.spek_kode);
        
        try {
            const progress = spek.jumlah_dibutuhkan > 0 ? 
                Math.round((spek.jumlah_tersedia / spek.jumlah_dibutuhkan) * 100) : 0;
            const progressClass = progress === 100 ? 'success' : progress > 0 ? 'warning' : 'secondary';
            
            // Determine if this is an attachment-only specification
            const isAttachmentSpec = spek.attachment_tipe && (!spek.tipe_unit_id || spek.tipe_unit_id === '0');
            const cardClass = isAttachmentSpec ? 'border-success' : 'border-primary';
            const badgeClass = isAttachmentSpec ? 'bg-success' : 'bg-primary';
            const specType = isAttachmentSpec ? 'Attachment' : 'Unit';
        
        html += `
            <div class="card mb-3 ${cardClass}" data-spek-id="${spek.id}" data-jumlah-dibutuhkan="${spek.jumlah_dibutuhkan}" data-jumlah-tersedia="${spek.jumlah_tersedia}" data-is-attachment="${isAttachmentSpec ? 'true' : 'false'}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <span class="badge ${badgeClass} me-2">${spek.spek_kode}</span>
                        <span class="badge bg-light text-dark me-2">${specType}</span>
                        ${spek.catatan_spek || 'Spesifikasi ' + spek.spek_kode}
                    </h6>
                    <div>
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="editSpesifikasi(${spek.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteSpesifikasi(${spek.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <small class="text-muted">Total ${isAttachmentSpec ? 'Attachment' : 'Unit'}</small>
                            <div class="fw-bold">${spek.jumlah_dibutuhkan}</div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Harga Bulanan</small>
                            <div class="fw-bold text-success">Rp ${formatNumber(spek.harga_per_unit_bulanan || 0)}</div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Harga Harian</small>
                            <div class="fw-bold text-info">Rp ${formatNumber(spek.harga_per_unit_harian || 0)}</div>
                        </div>
                    </div>
                    
                    <div class="row g-2 mt-2">
                        <div class="col-md-4">
                            <small class="text-muted">Departemen</small>
                            <div>${spek.nama_departemen || '-'}</div>
                        </div>
                        ${isAttachmentSpec ? `
                            <div class="col-md-4">
                                <small class="text-muted">Tipe Attachment</small>
                                <div>${spek.attachment_tipe || '-'}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Merk Attachment</small>
                                <div>${spek.attachment_merk || 'Sesuai Kebutuhan'}</div>
                            </div>
                        ` : `
                            <div class="col-md-4">
                                <small class="text-muted">Tipe/Jenis</small>
                                <div>${spek.tipe_unit_name || spek.tipe_jenis || '-'}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Kapasitas</small>
                                <div>${spek.kapasitas_name || '-'}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Merk/Model</small>
                                <div>${spek.merk_unit || '-'} ${spek.model_unit || ''}</div>
                            </div>
                            ${spek.attachment_tipe ? `
                                <div class="col-md-4">
                                    <small class="text-muted">Attachment</small>
                                    <div>${spek.attachment_tipe || '-'} ${spek.attachment_merk || ''}</div>
                                </div>
                            ` : ''}
                        `}
                        ${spek.jenis_baterai || spek.charger_name ? `
                            <div class="col-md-4">
                                <small class="text-muted">Baterai/Charger</small>
                                <div>${spek.jenis_baterai || '-'} / ${spek.charger_name || '-'}</div>
                            </div>
                        ` : ''}
                    </div>
                    
                    ${spek.aksesoris && spek.aksesoris.length > 0 ? `
                        <div class="row g-2 mt-2">
                            <div class="col-12">
                                <small class="text-muted">Aksesoris</small>
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                    ${spek.aksesoris.map(acc => `<span class="badge bg-secondary text-white">${acc}</span>`).join('')}
                                </div>
                            </div>
                        </div>
                    ` : ''}
                    
                    ${spek.jumlah_tersedia < spek.jumlah_dibutuhkan ? `
                        <div class="mt-2">
                            <button class="btn btn-sm ${isAttachmentSpec ? 'btn-success' : 'btn-primary'}" onclick="openSpkModalFromKontrak(${spek.id})">
                                <i class="fas fa-file-alt me-1"></i>Buat SPK ${specType}
                            </button>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle me-1"></i>SPK yang dibuat: ${spek.jumlah_spk || 0}
                            </small>
                        </div>
                    ` : `
                        <div class="mt-2">
                            <span class="badge ${isAttachmentSpec ? 'bg-success' : 'bg-success'}">
                                <i class="fas fa-check me-1"></i>SPK ${specType} Lengkap (${spek.jumlah_spk || 0} SPK dibuat)
                            </span>
                        </div>
                    `}
                </div>
            </div>
        `;
        } catch (error) {
            console.error(`Error processing spek ${index + 1}:`, error);
        }
    });
    
    container.innerHTML = html;
}

// Display customer spesifikasi - EXACT COPY from kontrak.php
function displayCustomerSpesifikasi(spesifikasi) {
    const container = document.getElementById('spesifikasiList');
    const spekCountElement = document.getElementById('spekCount');
    
    // Update tab counter
    if (spekCountElement) {
        spekCountElement.textContent = spesifikasi.length;
    }
    
    if (spesifikasi.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-clipboard-list fa-3x text-muted"></i>
                </div>
                <h5 class="text-muted">Belum Ada Spesifikasi</h5>
                <p class="text-muted mb-4">
                    Customer ini belum memiliki spesifikasi unit yang dibutuhkan.<br>
                    Tambahkan spesifikasi untuk menentukan jenis unit, jumlah, dan harga yang diperlukan.
                </p>
                <div class="d-flex flex-column align-items-center gap-2">
                    <button class="btn btn-primary btn-lg" onclick="openAddSpesifikasiModal()">
                        <i class="fas fa-plus me-2"></i>Tambah Spesifikasi Pertama
                    </button>
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Setelah menambah spesifikasi, nilai kontrak akan dihitung otomatis
                    </small>
                </div>
            </div>
        `;
        return;
    }
    
    let html = ``;
    
    spesifikasi.forEach((spek, index) => {
        console.log(`Processing spek ${index + 1}:`, spek.spek_kode);
        
        try {
            const progress = spek.jumlah_dibutuhkan > 0 ? 
                Math.round((spek.jumlah_tersedia / spek.jumlah_dibutuhkan) * 100) : 0;
            const progressClass = progress === 100 ? 'success' : progress > 0 ? 'warning' : 'secondary';
            
            // Determine if this is an attachment-only specification
            const isAttachmentSpec = spek.attachment_tipe && (!spek.tipe_unit_id || spek.tipe_unit_id === '0');
            const cardClass = isAttachmentSpec ? 'border-success' : 'border-primary';
            const badgeClass = isAttachmentSpec ? 'bg-success' : 'bg-primary';
            const specType = isAttachmentSpec ? 'Attachment' : 'Unit';
        
        html += `
            <div class="card mb-3 ${cardClass}" data-spek-id="${spek.id}" data-jumlah-dibutuhkan="${spek.jumlah_dibutuhkan}" data-jumlah-tersedia="${spek.jumlah_tersedia}" data-is-attachment="${isAttachmentSpec ? 'true' : 'false'}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <span class="badge ${badgeClass} me-2">${spek.spek_kode}</span>
                        <span class="badge bg-light text-dark me-2">${specType}</span>
                        ${spek.catatan_spek || 'Spesifikasi ' + spek.spek_kode}
                    </h6>
                    <div>
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="editSpesifikasi(${spek.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteSpesifikasi(${spek.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <small class="text-muted">Total ${isAttachmentSpec ? 'Attachment' : 'Unit'}</small>
                            <div class="fw-bold">${spek.jumlah_dibutuhkan}</div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Harga Bulanan</small>
                            <div class="fw-bold text-success">Rp ${formatNumber(spek.harga_per_unit_bulanan || 0)}</div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Harga Harian</small>
                            <div class="fw-bold text-info">Rp ${formatNumber(spek.harga_per_unit_harian || 0)}</div>
                        </div>
                    </div>
                    
                    <div class="row g-2 mt-2">
                        <div class="col-md-4">
                            <small class="text-muted">Departemen</small>
                            <div>${spek.nama_departemen || '-'}</div>
                        </div>
                        ${isAttachmentSpec ? `
                            <div class="col-md-4">
                                <small class="text-muted">Tipe Attachment</small>
                                <div>${spek.attachment_tipe || '-'}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Merk Attachment</small>
                                <div>${spek.attachment_merk || 'Sesuai Kebutuhan'}</div>
                            </div>
                        ` : `
                            <div class="col-md-4">
                                <small class="text-muted">Tipe/Jenis</small>
                                <div>${spek.tipe_unit_name || spek.tipe_jenis || '-'}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Kapasitas</small>
                                <div>${spek.kapasitas_name || '-'}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Merk/Model</small>
                                <div>${spek.merk_unit || '-'} ${spek.model_unit || ''}</div>
                            </div>
                            ${spek.attachment_tipe ? `
                                <div class="col-md-4">
                                    <small class="text-muted">Attachment</small>
                                    <div>${spek.attachment_tipe || '-'} ${spek.attachment_merk || ''}</div>
                                </div>
                            ` : ''}
                        `}
                        ${spek.jenis_baterai || spek.charger_name ? `
                            <div class="col-md-4">
                                <small class="text-muted">Baterai/Charger</small>
                                <div>${spek.jenis_baterai || '-'} / ${spek.charger_name || '-'}</div>
                            </div>
                        ` : ''}
                    </div>
                    
                    ${spek.aksesoris && spek.aksesoris.length > 0 ? `
                        <div class="row g-2 mt-2">
                            <div class="col-12">
                                <small class="text-muted">Aksesoris</small>
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                    ${spek.aksesoris.map(acc => `<span class="badge bg-secondary text-white">${acc}</span>`).join('')}
                                </div>
                            </div>
                        </div>
                    ` : ''}
                    
                    ${spek.jumlah_tersedia < spek.jumlah_dibutuhkan ? `
                        <div class="mt-2">
                            <button class="btn btn-sm ${isAttachmentSpec ? 'btn-success' : 'btn-primary'}" onclick="openSpkModalFromKontrak(${spek.id})">
                                <i class="fas fa-file-alt me-1"></i>Buat SPK ${specType}
                            </button>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle me-1"></i>SPK yang dibuat: ${spek.jumlah_spk || 0}
                            </small>
                        </div>
                    ` : `
                        <div class="mt-2">
                            <span class="badge ${isAttachmentSpec ? 'bg-success' : 'bg-success'}">
                                <i class="fas fa-check me-1"></i>SPK ${specType} Lengkap (${spek.jumlah_spk || 0} SPK dibuat)
                            </span>
                        </div>
                    `}
                </div>
            </div>
        `;
        } catch (error) {
            console.error(`Error processing spek ${index + 1}:`, error);
        }
    });
    
    console.log('Generated HTML length:', html.length);
    console.log('Setting container HTML...');
    
    try {
        container.innerHTML = html;
        console.log('Container HTML set successfully');
        console.log('Container children count:', container.children.length);
    } catch (error) {
        console.error('Error setting container HTML:', error);
        container.innerHTML = '<div class="text-danger">Error displaying data: ' + error.message + '</div>';
    }
}

// Modal functions for customer, contract, and spesifikasi
function openAddCustomerModal() {
    // Load areas for primary location dropdown
    loadAreas();
    
    // Generate customer code automatically
    generateCustomerCode();
    
    // Clear previous validation states
    clearFormErrors('#addCustomerForm');

    // Show modal
    $('#addCustomerModal').modal('show');
}

// Generate customer code function
function generateCustomerCode() {
    // Generate format: CUST-YYYYMMDD-XXX
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const random = String(Math.floor(Math.random() * 1000)).padStart(3, '0');
    
    const customerCode = `CUST-${year}${month}${day}-${random}`;
    $('#customer_code').val(customerCode);
}

function generateLocationCode() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const random = String(Math.floor(Math.random() * 100)).padStart(2, '0');
    const locationCode = `LOC-${year}${month}${day}-${random}`;
    $('#primary_location_code').val(locationCode);
}

function generateLocationCodeModal() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const random = String(Math.floor(Math.random() * 100)).padStart(2, '0');
    const locationCode = `LOC-${year}${month}${day}-${random}`;
    $('#loc_location_code').val(locationCode);
}

function openAddContractModal() {
    // Load customers for dropdown
    loadCustomers();
    
    // Show modal
    $('#addContractModal').modal('show');
}

function openAddLocationModal() {
    clearFormErrors('#addLocationForm'); // Clear errors on open
    $('#addLocationModal .modal-title').text('Add New Location');
    $('#addLocationForm').removeData('location-id');
    // Clear location code field for new location
    $('#loc_location_code').val('');
    // Set customer ID if we have one
    if (currentCustomerId) {
        $('#locationCustomerId').val(currentCustomerId);
    }
    
    // Load areas for location dropdown
    loadLocationAreas();
    
    // Show modal
    $('#addLocationModal').modal('show');
}

function openEditLocationModal(locationId) {
    clearFormErrors('#addLocationForm');
    
    // Load areas first
    loadLocationAreas();
    
    $.ajax({
        url: `<?= base_url('marketing/customer-management/showCustomerLocation') ?>/${locationId}`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const loc = response.data;
                $('#locationCustomerId').val(loc.customer_id);
                $('#addLocationModal .modal-title').text('Edit Location');
                $('#addLocationForm').data('location-id', locationId);
                $('#loc_location_name').val(loc.location_name);
                $('#loc_location_code').val(loc.location_code || '');
                $('#loc_contact_person').val(loc.contact_person || '');
                $('#loc_phone').val(loc.phone || '');
                $('#loc_address').val(loc.address || '');
                $('#loc_city').val(loc.city || '');
                $('#loc_province').val(loc.province || '');
                $('#loc_postal_code').val(loc.postal_code || '');
                $('#loc_email').val(loc.email || '');
                $('#loc_notes').val(loc.notes || '');
                $('#loc_is_primary').prop('checked', loc.is_primary == 1);
                
                // Set area_id after areas are loaded
                if (loc.area_id) {
                    setTimeout(() => {
                        $('#loc_area_id').val(loc.area_id);
                    }, 100);
                }
                
                $('#addLocationModal').modal('show');
            } else {
                console.error('Failed to load location:', response.message);
                showNotification(response.message || 'Failed to load location', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error loading location:', {xhr, status, error});
            showNotification('Terjadi kesalahan pada sistem', 'error');
        }
    });
}

// Modal functions for spesifikasi - same as kontrak.php
function openAddSpesifikasiModal() {
    // Check if we have a current contract
    if (!currentContractId) {
        showNotification('Pilih kontrak terlebih dahulu', 'warning');
        return;
    }
    
    // Reset form completely
    $('#addSpesifikasiForm')[0].reset();
    
    // Set the contract ID in the form
    $('#spekKontrakId').val(currentContractId);
    
    // Clear any previous form errors
    clearFormErrors('#addSpesifikasiForm');
    
    // Uncheck all accessories
    $('input[name="aksesoris[]"]').prop('checked', false);
    
    // Remove edit mode hidden field if exists
    $('#spekEditId').remove();
    
    // Reset modal title and button text
    $('#addSpesifikasiModal .modal-title').text('Tambah Spesifikasi Unit');
    $('#submitSpesifikasiBtn').text('Simpan Spesifikasi');
    
    // Load dropdown data
    loadDepartemenForSpesifikasi();
    loadTipeUnitForSpesifikasi();
    loadKapasitasForSpesifikasi();
    loadMerkUnitForSpesifikasi();
    loadJenisBateraiForSpesifikasi();
    loadChargerForSpesifikasi();
    loadAttachmentTipeForSpesifikasi();
    loadValveForSpesifikasi();
    loadMastForSpesifikasi();
    loadBanForSpesifikasi();
    loadRodaForSpesifikasi();
    
    // Open the add spesifikasi modal
    $('#addSpesifikasiModal').modal('show');
}

// Event handler for departemen change to filter tipe unit and lock/unlock fields
$(document).on('change', '#spekDepartemen', function() {
    updateTipeUnitOptions();
    updateFieldAvailability();
    loadChargerForSpesifikasi(); // Reload charger when departemen changes
});

function updateFieldAvailability() {
    const selectedDepartemen = $('#spekDepartemen').val();
    const isElectric = selectedDepartemen === '2'; // ELECTRIC has id_departemen = 2
    
    // Lock/unlock Baterai field
    const bateraiField = $('#spekJenisBaterai');
    const chargerField = $('#spekCharger');
    
    if (isElectric) {
        bateraiField.prop('disabled', false);
        chargerField.prop('disabled', false);
        bateraiField.closest('.col-md-4').find('.text-muted').removeClass('text-muted').addClass('text-success');
        chargerField.closest('.col-md-4').find('.text-muted').removeClass('text-muted').addClass('text-success');
    } else {
        bateraiField.prop('disabled', true).val('');
        chargerField.prop('disabled', true).val('');
        bateraiField.closest('.col-md-4').find('.text-muted').removeClass('text-success').addClass('text-muted');
        chargerField.closest('.col-md-4').find('.text-muted').removeClass('text-success').addClass('text-muted');
    }
}

function openAddAttachmentSpesifikasiModal() {
    // Check if we have a current contract
    if (!currentContractId) {
        showNotification('Pilih kontrak terlebih dahulu', 'warning');
        return;
    }
    
    // Set the contract ID in the form
    $('#attachmentSpekKontrakId').val(currentContractId);
    
    // Clear any previous form errors
    clearFormErrors('#addAttachmentSpesifikasiForm');
    
    // Open the add attachment spesifikasi modal
    $('#addAttachmentSpesifikasiModal').modal('show');
}

function editSpesifikasi(spekId) {
    // Edit spesifikasi function (this should be implemented in kontrak.php)
    console.log('Edit spesifikasi:', spekId);
    notify('Fitur edit spesifikasi akan diimplementasikan', 'info');
}

function deleteSpesifikasi(spekId) {
    if (!confirm('Apakah Anda yakin ingin menghapus spesifikasi ini?')) {
        return;
    }
    
    $.ajax({
        url: `<?= base_url('marketing/kontrak/delete-spesifikasi/') ?>${spekId}`,
        type: 'DELETE',
        success: function(response) {
            if (response.success) {
                notify('Spesifikasi berhasil dihapus', 'success');
                // Reload spesifikasi tab
                if (currentContractId) {
                    loadContractSpesifikasi(currentContractId);
                }
            } else {
                notify(response.message || 'Gagal menghapus spesifikasi', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Delete AJAX error:', {xhr, status, error});
            notify('Terjadi kesalahan pada sistem', 'error');
        }
    });
}

// Show unit detail
function showUnitDetail(unitId) {
    $.ajax({
        url: `<?= base_url('marketing/unit-detail') ?>/${unitId}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                displayUnitDetail(response.data);
                $('#unitDetailModal').modal('show');
            }
        }
    });
}

// Display unit detail - COMPREHENSIVE (from kontrak.php)
function displayUnitDetail(unit) {
    $('#unitSubtitle').text(`${unit.no_unit || 'N/A'} - ${unit.merk_unit || ''} ${unit.model_unit || ''}`);
    
    let detailHtml = `
        <div class="row g-4">
            <!-- Basic Information -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-primary">
                        <h6 class="mb-0 text-dark"><i class="fas fa-info-circle me-2"></i><strong>Informasi Dasar</strong></h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-6"><strong>No Unit:</strong></div>
                            <div class="col-6">${unit.no_unit || '-'}</div>
                            
                            <div class="col-6"><strong>Serial Number:</strong></div>
                            <div class="col-6">${unit.serial_number_po || '-'}</div>
                            
                            <div class="col-6"><strong>Merk:</strong></div>
                            <div class="col-6">${unit.merk_unit || '-'}</div>
                            
                            <div class="col-6"><strong>Model:</strong></div>
                            <div class="col-6">${unit.model_unit || '-'}</div>
                            
                            <div class="col-6"><strong>Tahun:</strong></div>
                            <div class="col-6">${unit.tahun_po || '-'}</div>
                            
                            <div class="col-6"><strong>Tipe Unit:</strong></div>
                            <div class="col-6">${unit.nama_tipe_unit || '-'}</div>
                            
                            <div class="col-6"><strong>Kapasitas:</strong></div>
                            <div class="col-6">${unit.kapasitas_unit || '-'}</div>
                            
                            <div class="col-6"><strong>Departemen:</strong></div>
                            <div class="col-6">${unit.nama_departemen || '-'}</div>
                            
                            <div class="col-6"><strong>Status:</strong></div>
                            <div class="col-6"><span class="badge bg-${getStatusBadgeClass(unit.status_unit_name)}">${unit.status_unit_name || '-'}</span></div>
                            
                            <div class="col-6"><strong>Lokasi:</strong></div>
                            <div class="col-6">${unit.lokasi_unit || '-'}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Technical Specifications -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-success">
                        <h6 class="mb-0 text-dark"><i class="fas fa-cogs me-2"></i><strong>Spesifikasi Teknis</strong></h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
    `;
    
    // Mast Information
    if (unit.mast_name || unit.sn_mast_po) {
        detailHtml += `
            <div class="col-12"><h6 class="text-primary border-bottom pb-1 mb-2"><i class="fas fa-arrows-alt-v me-1"></i>Mast</h6></div>
            <div class="col-6"><strong>Model Mast:</strong></div>
            <div class="col-6">${unit.mast_name || '-'}</div>
            <div class="col-6"><strong>SN Mast:</strong></div>
            <div class="col-6">${unit.sn_mast_po || '-'}</div>`;
    }
    
    // Engine Information
    if (unit.mesin_name || unit.sn_mesin_po) {
        detailHtml += `
            <div class="col-12"><h6 class="text-primary border-bottom pb-1 mb-2 mt-2"><i class="fas fa-engine me-1"></i>Mesin</h6></div>
            <div class="col-6"><strong>Model Mesin:</strong></div>
            <div class="col-6">${unit.mesin_name || '-'}</div>
            <div class="col-6"><strong>SN Mesin:</strong></div>
            <div class="col-6">${unit.sn_mesin_po || '-'}</div>`;
    }
    
    // Battery Information
    if (unit.baterai_name || unit.sn_baterai_po) {
        detailHtml += `
            <div class="col-12"><h6 class="text-primary border-bottom pb-1 mb-2 mt-2"><i class="fas fa-battery-full me-1"></i>Baterai</h6></div>
            <div class="col-6"><strong>Model Baterai:</strong></div>
            <div class="col-6">${unit.baterai_name || '-'}</div>
            <div class="col-6"><strong>SN Baterai:</strong></div>
            <div class="col-6">${unit.sn_baterai_po || '-'}</div>`;
    }
    
    // Attachments Information
    if (unit.attachments && unit.attachments.length > 0) {
        detailHtml += `
            <div class="col-12"><h6 class="text-primary border-bottom pb-1 mb-2 mt-2"><i class="fas fa-puzzle-piece me-1"></i>Attachment</h6></div>`;
        unit.attachments.forEach((att, index) => {
            detailHtml += `
                <div class="col-6"><strong>${att.name || 'Attachment ' + (index + 1)}:</strong></div>
                <div class="col-6">${att.merk || '-'}</div>
                <div class="col-6"><strong>SN ${att.name || 'Att'}:</strong></div>
                <div class="col-6">${att.serial_number || '-'}</div>`;
        });
    }
    
    // Wheels and Parts
    if (unit.ban_name || unit.roda_name || unit.valve_name) {
        detailHtml += `
            <div class="col-12"><h6 class="text-primary border-bottom pb-1 mb-2 mt-2"><i class="fas fa-circle me-1"></i>Ban & Roda</h6></div>`;
        if (unit.ban_name) {
            detailHtml += `
                <div class="col-6"><strong>Ban:</strong></div>
                <div class="col-6">${unit.ban_name}</div>`;
        }
        if (unit.roda_name) {
            detailHtml += `
                <div class="col-6"><strong>Roda:</strong></div>
                <div class="col-6">${unit.roda_name}</div>`;
        }
        if (unit.valve_name) {
            detailHtml += `
                <div class="col-6"><strong>Valve:</strong></div>
                <div class="col-6">${unit.valve_name}</div>`;
        }
    }
    
    detailHtml += `
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    
    // Aksesori Terpasang
    if (unit.aksesoris) {
        let aksesoris = [];
        try {
            aksesoris = typeof unit.aksesoris === 'string' ? JSON.parse(unit.aksesoris) : unit.aksesoris;
        } catch (e) {
            aksesoris = unit.aksesoris.split(',').map(item => item.trim()).filter(item => item);
        }
        
        if (aksesoris && aksesoris.length > 0) {
            detailHtml += `
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-info text-dark">
                                <h6 class="mb-0"><i class="fas fa-puzzle-piece me-2"></i><strong>Aksesori Terpasang</strong></h6>
                            </div>
                            <div class="card-body">
                                <div class="row">`;
            
            if (Array.isArray(aksesoris)) {
                aksesoris.forEach((item, index) => {
                    detailHtml += `
                        <div class="col-md-6 mb-2">
                            <i class="fas fa-check-circle text-success me-1"></i>${item}
                        </div>`;
                });
            }
            
            detailHtml += `
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
        }
    }
    
    // Additional Notes
    if (unit.keterangan) {
        detailHtml += `
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Keterangan</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">${unit.keterangan}</p>
                        </div>
                    </div>
                </div>
            </div>`;
    }
    
    $('#unitDetailContent').html(detailHtml);
}

// Helper functions
function formatNumber(num) {
    try {
        return new Intl.NumberFormat('id-ID').format(num || 0);
    } catch (error) {
        console.warn('formatNumber error:', error);
        return (num || 0).toString();
    }
}

function getStatusBadge(status) {
    const statusMap = {
        'Aktif': 'success',
        'Active': 'success',
        'Expired': 'danger',
        'Berakhir': 'danger',
        'Expiring': 'warning',
        'Draft': 'secondary'
    };
    
    const badgeClass = statusMap[status] || 'secondary';
    return `<span class="badge bg-${badgeClass}">${status || 'Unknown'}</span>`;
}

function getStatusBadgeClass(status) {
    if (!status) return 'secondary';
    const statusLower = status.toLowerCase();
    
    if (statusLower.includes('tersedia') || statusLower.includes('available')) return 'success';
    if (statusLower.includes('rental') || statusLower.includes('disewa')) return 'primary';
    if (statusLower.includes('maintenance') || statusLower.includes('rusak')) return 'warning';
    if (statusLower.includes('hilang') || statusLower.includes('lost')) return 'danger';
    
    return 'secondary';
}

function showNotification(message, type) {
    if (window.OptimaPro && typeof OptimaPro.showNotification === 'function') {
        OptimaPro.showNotification(message, type);
        
        // Ensure notification z-index is above modals
        setTimeout(() => {
            $('.optima-notification, .notification-container, .toast-container').css('z-index', '9999');
        }, 100);
    } else {
        alert(message);
    }
}

function refreshData() {
    customerTable.ajax.reload();
    loadStatistics();
    showNotification('Data refreshed', 'success');
}

// ===== Generic form error helpers =====
function showFormErrors(formSelector, errors) {
    Object.keys(errors).forEach(function(field) {
        const messages = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
        const input = $(`${formSelector} [name="${field}"]`);
        if (input.length) {
            input.addClass('is-invalid');
            let feedback = input.siblings('.invalid-feedback');
            if (!feedback.length) {
                // For input-group, add after group; else after input
                const group = input.closest('.input-group');
                if (group.length) {
                    group.after('<div class="invalid-feedback"></div>');
                    feedback = group.next('.invalid-feedback');
                } else {
                    input.after('<div class="invalid-feedback"></div>');
                    feedback = input.next('.invalid-feedback');
                }
            }
            feedback.text(messages[0]).show();
        }
    });
}

function clearFormErrors(formSelector) {
    const form = $(formSelector);
    form.find('.is-invalid').removeClass('is-invalid');
    form.find('.invalid-feedback').remove();
}

// Load customer locations
function loadCustomerLocations(customerId) {
    $.ajax({
        url: `<?= base_url('marketing/customer-management/getCustomerLocations') ?>/${customerId}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                displayLocations(response.data);
                $('#locationCount').text(response.data.length);
            } else {
                $('#locationsList').html('<div class="col-12 text-center text-muted">No locations found</div>');
            }
        },
        error: function() {
            $('#locationsList').html('<div class="col-12 text-center text-danger">Error loading locations</div>');
        }
    });
}

// Display locations
function displayLocations(locations) {
    let html = '';
    
    if (locations.length === 0) {
        html = '<div class="col-12 text-center text-muted">No locations found</div>';
    } else {
        locations.forEach(location => {
            const isPrimary = location.is_primary ? '<span class="badge bg-primary ms-2">Primary</span>' : '';
            html += `
                <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">
                                            <i class="fas fa-map-marker-alt me-2"></i><strong>${location.location_name}</strong>${isPrimary}
                                        </h6>
                                        ${location.location_code ? `<small class="text-muted">Code: ${location.location_code}</small>` : ''}
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary" onclick="openEditLocationModal(${location.id})">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-12">
                                    <strong><i class="fas fa-map-marked-alt me-1"></i> Area:</strong><br>
                                    <span class="badge bg-info">${location.area_name || 'N/A'}</span>
                                </div>
                                <div class="col-12">
                                    <strong><i class="fas fa-map-marker me-1"></i> Alamat:</strong><br>
                                    <span class="text-muted">${location.address || 'Tidak ada alamat'}</span>
                                </div>
                                <div class="col-6">
                                    <strong><i class="fas fa-city me-1"></i> City:</strong><br>
                                    <span class="text-muted">${location.city || '-'}</span>
                                </div>
                                <div class="col-6">
                                    <strong><i class="fas fa-flag me-1"></i> Province:</strong><br>
                                    <span class="text-muted">${location.province || '-'}</span>
                                </div>
                                <div class="col-12"><hr class="my-2"></div>
                                <div class="col-6">
                                    <strong><i class="fas fa-user me-1"></i> Contact Person:</strong><br>
                                    <span class="text-muted">${location.contact_person || '-'}</span>
                                </div>
                                <div class="col-6">
                                    <strong><i class="fas fa-phone me-1"></i> Phone:</strong><br>
                                    <span class="text-muted">${location.phone || '-'}</span>
                                </div>
                                <div class="col-6">
                                    <strong><i class="fas fa-envelope me-1"></i> Email:</strong><br>
                                    <span class="text-muted">${location.email || '-'}</span>
                                </div>
                                <div class="col-6">
                                    <strong><i class="fas fa-id-badge me-1"></i> PIC Position:</strong><br>
                                    <span class="text-muted">${location.pic_position || '-'}</span>
                                </div>
                                ${location.notes ? `
                                <div class="col-12">
                                    <strong><i class="fas fa-sticky-note me-1"></i> Notes:</strong><br>
                                    <span class="text-muted">${location.notes}</span>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    $('#locationsList').html(html);
}

// Setup tab handlers
function setupTabHandlers() {
    // Handle Locations tab
    $('#locations-tab').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Remove active class from modal tabs only and hide all modal content
        $('#customerDetailTabs .nav-link').removeClass('active');
        $('#customerDetailTabContent .tab-pane').removeClass('show active').hide();
        
        // Add active class to locations tab and show content
        $(this).addClass('active');
        $('#locations-content').addClass('show active').show();
        
        // Load locations data
        if (currentCustomerId) {
            loadCustomerLocations(currentCustomerId);
        }
    });
    
    // Handle Contracts tab
    $('#contracts-tab').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Remove active class from modal tabs only and hide all modal content
        $('#customerDetailTabs .nav-link').removeClass('active');
        $('#customerDetailTabContent .tab-pane').removeClass('show active').hide();
        
        // Add active class to contracts tab and show content
        $(this).addClass('active');
        $('#contracts-content').addClass('show active').show();
        
        // Load contracts data
        if (currentCustomerId) {
            loadCustomerContracts(currentCustomerId);
        }
    });
    
    // Handle Company Info tab
    $('#company-tab').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Remove active class from modal tabs only and hide all modal content
        $('#customerDetailTabs .nav-link').removeClass('active');
        $('#customerDetailTabContent .tab-pane').removeClass('show active').hide();
        
        // Add active class to company tab and show content
        $(this).addClass('active');
        $('#company-content').addClass('show active').show();
    });
    
    
    // Handle Locations & Units tab in Contract Details modal
    $('#locations-tab-contract').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Check if data already loaded (to prevent unnecessary reload)
        const locationsContainer = document.getElementById('locationsAccordion');
        const isAlreadyLoaded = locationsContainer && 
                                locationsContainer.innerHTML.trim() !== '' && 
                                !locationsContainer.innerHTML.includes('Memuat locations');
        
        // Remove active class from contract modal tabs only
        $('#contractDetailTabs .nav-link').removeClass('active');
        $('#contractDetailTabContent .tab-pane').removeClass('show active');
        
        // Add active class to locations tab and show content
        $(this).addClass('active');
        $('#locations-content-contract').addClass('show active');
        
        // Only load data if NOT already loaded
        console.log('Locations & Units tab clicked, currentContractId:', currentContractId, 'Already loaded:', isAlreadyLoaded);
        
        if (!isAlreadyLoaded) {
            // Show loading state
            $('#locationsAccordion').html(`
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="text-muted mt-3">Memuat locations & units...</p>
                </div>
            `);
            
            if (currentContractId) {
                // Small delay to ensure smooth transition
                setTimeout(() => {
                    loadContractUnits(currentContractId);
                }, 100);
            } else {
                console.warn('No currentContractId available for locations & units');
                $('#locationsAccordion').html('<div class="alert alert-warning"><i class="fas fa-info-circle me-2"></i>Pilih kontrak terlebih dahulu untuk melihat locations & units</div>');
            }
        }
        // If already loaded, just show it (no reload, scroll position maintained)
    });
    
    // Handle Spesifikasi tab in Contract Details modal
    $('#spesifikasi-tab-contract').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Check if data already loaded (to prevent unnecessary reload)
        const spesifikasiContainer = document.getElementById('spesifikasiListContract');
        const isAlreadyLoaded = spesifikasiContainer && 
                                spesifikasiContainer.innerHTML.trim() !== '' && 
                                !spesifikasiContainer.innerHTML.includes('Memuat spesifikasi');
        
        // Remove active class from contract modal tabs only
        $('#contractDetailTabs .nav-link').removeClass('active');
        $('#contractDetailTabContent .tab-pane').removeClass('show active');
        
        // Add active class to spesifikasi tab and show content
        $(this).addClass('active');
        $('#spesifikasi-content-contract').addClass('show active');
        
        // Only load data if NOT already loaded or if forced reload
        console.log('Spesifikasi tab clicked, currentContractId:', currentContractId, 'Already loaded:', isAlreadyLoaded);
        
        if (!isAlreadyLoaded) {
            if (currentContractId) {
                // Call loadContractSpesifikasi for first time load
                loadContractSpesifikasi(currentContractId);
            } else {
                console.warn('No currentContractId available for spesifikasi');
                $('#spesifikasiListContract').html('<div class="alert alert-warning"><i class="fas fa-info-circle me-2"></i>Pilih kontrak terlebih dahulu untuk melihat spesifikasi</div>');
            }
        }
        // If already loaded, just show it (no reload, scroll position maintained)
    });
}

// Helper functions for modal dropdowns
function loadDepartemenForSpesifikasi() {
    $.ajax({
        url: '<?= base_url('marketing/spk/spec-options') ?>?type=departemen',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const select = $('#spekDepartemen');
                select.empty().append('<option value="">-- Pilih Departemen --</option>');
                response.data.forEach(function(dept) {
                    select.append(`<option value="${dept.id}">${dept.name}</option>`);
                });
            }
        },
        error: function() {
            console.error('Error loading departemen');
        }
    });
}

function loadTipeUnitForSpesifikasi() {
    // Load all tipe unit data with departemen info for filtering
    $.ajax({
        url: '<?= base_url('marketing/customer-management/getTipeUnit') ?>',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                // Store all tipe unit data globally for filtering
                window.allTipeUnitData = response.data;
                
                // Initially show all options
                updateTipeUnitOptions();
            }
        },
        error: function() {
            console.error('Error loading tipe unit');
        }
    });
}

function updateTipeUnitOptions() {
    const select = $('#spekTipeUnit');
    const selectedDepartemen = $('#spekDepartemen').val();
    
    select.empty().append('<option value="">-- Pilih Tipe Unit --</option>');
    
    if (window.allTipeUnitData) {
        // Filter by departemen if selected
        let filteredData = window.allTipeUnitData;
        if (selectedDepartemen) {
            filteredData = window.allTipeUnitData.filter(tipe => tipe.id_departemen == selectedDepartemen);
        }
        
        // Remove duplicates based on tipe and jenis combination
        const uniqueData = [];
        const seen = new Set();
        
        filteredData.forEach(function(tipe) {
            const key = `${tipe.tipe} - ${tipe.jenis}`;
            if (!seen.has(key)) {
                seen.add(key);
                uniqueData.push(tipe);
            }
        });
        
        // Sort by tipe, then by jenis
        uniqueData.sort((a, b) => {
            if (a.tipe !== b.tipe) {
                return a.tipe.localeCompare(b.tipe);
            }
            return a.jenis.localeCompare(b.jenis);
        });
        
        // Populate dropdown with "Tipe - Jenis" format
        uniqueData.forEach(function(tipe) {
            select.append(`<option value="${tipe.id_tipe_unit}">${tipe.tipe} - ${tipe.jenis}</option>`);
        });
    }
}

function loadKapasitasForSpesifikasi() {
    $.ajax({
        url: '<?= base_url('marketing/spk/spec-options') ?>?type=kapasitas',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const select = $('#spekKapasitas');
                select.empty().append('<option value="">-- Pilih Kapasitas --</option>');
                response.data.forEach(function(kap) {
                    select.append(`<option value="${kap.id}">${kap.name}</option>`);
                });
            }
        },
        error: function() {
            console.error('Error loading kapasitas');
        }
    });
}

function loadMerkUnitForSpesifikasi() {
    $.ajax({
        url: '<?= base_url('marketing/spk/spec-options') ?>?type=merk_unit',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const select = $('#spekMerkUnit');
                select.empty().append('<option value="">-- Pilih Merk --</option>');
                response.data.forEach(function(merk) {
                    select.append(`<option value="${merk.id}">${merk.name}</option>`);
                });
            }
        },
        error: function() {
            console.error('Error loading merk unit');
        }
    });
}

function loadJenisBateraiForSpesifikasi() {
    $.ajax({
        url: '<?= base_url('marketing/spk/spec-options') ?>?type=jenis_baterai',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const select = $('#spekJenisBaterai');
                select.empty().append('<option value="">-- Pilih Baterai --</option>');
                response.data.forEach(function(baterai) {
                    select.append(`<option value="${baterai.id}">${baterai.name}</option>`);
                });
            }
        },
        error: function() {
            console.error('Error loading jenis baterai');
        }
    });
}

function loadChargerForSpesifikasi() {
    const selectedDepartemen = $('#spekDepartemen').val();
    const url = selectedDepartemen ? 
        `<?= base_url('marketing/spk/spec-options') ?>?type=charger&departemen_id=${selectedDepartemen}` :
        `<?= base_url('marketing/spk/spec-options') ?>?type=charger`;
    
    $.ajax({
        url: url,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const select = $('#spekCharger');
                select.empty().append('<option value="">-- Pilih Charger --</option>');
                response.data.forEach(function(charger) {
                    select.append(`<option value="${charger.id}">${charger.name}</option>`);
                });
            }
        },
        error: function() {
            console.error('Error loading charger');
        }
    });
}

function loadAttachmentTipeForSpesifikasi() {
    $.ajax({
        url: '<?= base_url('marketing/spk/spec-options') ?>?type=attachment_tipe',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const select = $('#spekAttachmentTipe');
                select.empty().append('<option value="">-- Pilih Attachment Tipe --</option>');
                response.data.forEach(function(attachment) {
                    select.append(`<option value="${attachment.id}">${attachment.name}</option>`);
                });
            }
        },
        error: function() {
            console.error('Error loading attachment tipe');
        }
    });
}

function loadValveForSpesifikasi() {
    $.ajax({
        url: '<?= base_url('marketing/spk/spec-options') ?>?type=valve',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const select = $('#spekValve');
                select.empty().append('<option value="">-- Pilih Valve --</option>');
                response.data.forEach(function(valve) {
                    select.append(`<option value="${valve.id}">${valve.name}</option>`);
                });
            }
        },
        error: function() {
            console.error('Error loading valve');
        }
    });
}

function loadMastForSpesifikasi() {
    $.ajax({
        url: '<?= base_url('marketing/spk/spec-options') ?>?type=mast',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const select = $('#spekMast');
                select.empty().append('<option value="">-- Pilih Mast --</option>');
                response.data.forEach(function(mast) {
                    select.append(`<option value="${mast.id}">${mast.name}</option>`);
                });
            }
        },
        error: function() {
            console.error('Error loading mast');
        }
    });
}

function loadBanForSpesifikasi() {
    $.ajax({
        url: '<?= base_url('marketing/spk/spec-options') ?>?type=ban',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const select = $('#spekBan');
                select.empty().append('<option value="">-- Pilih Ban --</option>');
                response.data.forEach(function(ban) {
                    select.append(`<option value="${ban.id}">${ban.name}</option>`);
                });
            }
        },
        error: function() {
            console.error('Error loading ban');
        }
    });
}

function loadRodaForSpesifikasi() {
    $.ajax({
        url: '<?= base_url('marketing/spk/spec-options') ?>?type=roda',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const select = $('#spekRoda');
                select.empty().append('<option value="">-- Pilih Roda --</option>');
                response.data.forEach(function(roda) {
                    select.append(`<option value="${roda.id}">${roda.name}</option>`);
                });
            }
        },
        error: function() {
            console.error('Error loading roda');
        }
    });
}

function loadAreas() {
    $.ajax({
        url: '<?= base_url('marketing/customer-management/getAreas') ?>',
        method: 'GET',
        success: function(response) {
            console.log('Areas response:', response);
            if (response.success) {
                const areaSelect = $('#area_id');
                areaSelect.empty().append('<option value="">Select Area</option>');
                response.data.forEach(area => {
                    areaSelect.append(`<option value="${area.id}">${area.area_code} - ${area.area_name}</option>`);
                });
            } else {
                console.error('Error loading areas:', response.message);
                notify('Error loading areas: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error loading areas:', {xhr, status, error});
            notify('Error loading areas: ' + error, 'error');
        }
    });
}

function loadLocationAreas() {
    $.ajax({
        url: '<?= base_url('marketing/customer-management/getAreas') ?>',
        method: 'GET',
        success: function(response) {
            console.log('Location Areas response:', response);
            if (response.success) {
                const areaSelect = $('#loc_area_id');
                areaSelect.empty().append('<option value="">Select Area</option>');
                response.data.forEach(area => {
                    areaSelect.append(`<option value="${area.id}">${area.area_code} - ${area.area_name}</option>`);
                });
            } else {
                console.error('Error loading areas for location:', response.message);
                notify('Error loading areas: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error loading areas for location:', {xhr, status, error});
            notify('Error loading areas: ' + error, 'error');
        }
    });
}

function loadCustomers() {
    $.ajax({
        url: '<?= base_url('marketing/customer-management/getCustomers') ?>',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const customerSelect = $('#customerSelect');
                customerSelect.empty().append('<option value="">-- Pilih Customer --</option>');
                response.data.forEach(customer => {
                    customerSelect.append(`<option value="${customer.id}">${customer.customer_name}</option>`);
                });
            }
        },
        error: function() {
            console.error('Error loading customers');
        }
    });
}

// Handle generate customer code button
$(document).on('click', '#generateCustomerCode', function() {
    generateCustomerCode();
});

$(document).on('click', '#generateLocationCode', function() {
    generateLocationCode();
});

$(document).on('click', '#generateLocationCodeModal', function() {
    generateLocationCodeModal();
});

// Handle customer selection in contract modal
$(document).on('change', '#customerSelect', function() {
    const customerId = $(this).val();
    const locationSelect = $('#locationSelect');
    
    if (customerId) {
        // Load locations for selected customer
        $.ajax({
            url: `<?= base_url('marketing/customer-management/getCustomerLocations/') ?>${customerId}`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    locationSelect.empty().append('<option value="">-- Pilih Lokasi --</option>');
                    response.data.forEach(location => {
                        locationSelect.append(`<option value="${location.id}">${location.location_name}</option>`);
                    });
                    locationSelect.prop('disabled', false);
                }
            },
            error: function() {
                console.error('Error loading locations');
            }
        });
    } else {
        locationSelect.empty().append('<option value="">-- Pilih Customer Dulu --</option>').prop('disabled', true);
    }
});

// Handle form submissions
$(document).on('submit', '#addCustomerForm', function(e) {
    e.preventDefault();
    // Clear previous errors before submit
    clearFormErrors('#addCustomerForm');

    $.ajax({
        url: '<?= base_url('marketing/customer-management/storeCustomer') ?>',
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                showNotification(response.message, 'success');
                $('#addCustomerModal').modal('hide');
                $('#addCustomerForm')[0].reset();
                customerTable.ajax.reload();
            } else {
                if (response.errors) {
                    showFormErrors('#addCustomerForm', response.errors);
                    showNotification('Periksa kembali input Anda.', 'error');
                } else {
                    showNotification(response.message || 'Validation failed', 'error');
                }
            }
        },
        error: function() {
            showNotification('Terjadi kesalahan pada sistem', 'error');
        }
    });
});

$(document).on('submit', '#addContractForm', function(e) {
    e.preventDefault();
    clearFormErrors('#addContractForm');

    $.ajax({
        url: '<?= base_url('marketing/kontrak/store') ?>',
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                showNotification(response.message, 'success');
                $('#addContractModal').modal('hide');
                $('#addContractForm')[0].reset();
                customerTable.ajax.reload();
            } else {
                if (response.errors) {
                    showFormErrors('#addContractForm', response.errors);
                }
                showNotification(response.message, 'error');
            }
        },
        error: function() {
            showNotification('Terjadi kesalahan pada sistem', 'error');
        }
    });
});

$(document).on('submit', '#addLocationForm', function(e) {
    e.preventDefault();
    clearFormErrors('#addLocationForm');

    const locId = $('#addLocationForm').data('location-id');
    const url = locId ? `<?= base_url('marketing/customer-management/updateCustomerLocation') ?>/${locId}` : '<?= base_url('marketing/customer-management/storeCustomerLocation') ?>';

    $.ajax({
        url: url,
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                showNotification(response.message, 'success');
                $('#addLocationModal').modal('hide');
                $('#addLocationForm')[0].reset();
                $('#addLocationForm').removeData('location-id');
                
                // Reload locations if we're in customer detail modal
                if (currentCustomerId) {
                    loadCustomerLocations(currentCustomerId);
                }
            } else {
                if (response.errors) {
                    displayFormErrors('#addLocationForm', response.errors);
                    showNotification('Periksa kembali input lokasi.', 'error');
                } else {
                    showNotification(response.message, 'error');
                }
            }
        },
        error: function() {
            showNotification('Terjadi kesalahan pada sistem', 'error');
        }
    });
});

$(document).on('submit', '#addSpesifikasiForm', function(e) {
    e.preventDefault();
    clearFormErrors('#addSpesifikasiForm');

    $.ajax({
        url: '<?= base_url('marketing/kontrak/add-spesifikasi') ?>',
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                showNotification(response.message, 'success');
                $('#addSpesifikasiModal').modal('hide');
                $('#addSpesifikasiForm')[0].reset();
                
                // Reload spesifikasi if we're in a contract detail modal
                if (currentContractId) {
                    loadContractSpesifikasiCorrect(currentContractId);
                }
            } else {
                if (response.errors) {
                    showFormErrors('#addSpesifikasiForm', response.errors);
                }
                showNotification(response.message, 'error');
            }
        },
        error: function() {
            showNotification('Terjadi kesalahan pada sistem', 'error');
        }
    });
});

$(document).on('submit', '#addAttachmentSpesifikasiForm', function(e) {
    e.preventDefault();
    clearFormErrors('#addAttachmentSpesifikasiForm');

    $.ajax({
        url: '<?= base_url('marketing/kontrak/add-spesifikasi') ?>',
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                showNotification(response.message, 'success');
                $('#addAttachmentSpesifikasiModal').modal('hide');
                $('#addAttachmentSpesifikasiForm')[0].reset();
                
                // Reload spesifikasi if we're in a contract detail modal
                if (currentContractId) {
                    loadContractSpesifikasiCorrect(currentContractId);
                }
            } else {
                if (response.errors) {
                    showFormErrors('#addAttachmentSpesifikasiForm', response.errors);
                }
                showNotification(response.message, 'error');
            }
        },
        error: function() {
            showNotification('Terjadi kesalahan pada sistem', 'error');
        }
    });
});

// SPK Modal Functions
function openSpkModalFromKontrak(spekId) {
    console.log('openSpkModalFromKontrak called with spekId:', spekId);
    
    if (!currentContractId) {
        showNotification('Kontrak belum dipilih. Buka detail kontrak terlebih dahulu.', 'error');
        return;
    }
    
    // Set form values
    document.getElementById('spkKontrakId').value = currentContractId;
    document.getElementById('spkSpesifikasiId').value = spekId;
    
    // Populate fields from contract data
    console.log('Current contract data:', window.currentContractData);
    
    if (window.currentContractData) {
        const pelangganField = document.getElementById('spkPelanggan');
        const picField = document.getElementById('spkPic');
        const kontakField = document.getElementById('spkKontak');
        const lokasiField = document.getElementById('spkLokasi');
        
        if (pelangganField) pelangganField.value = window.currentContractData.customer_name || window.currentContractData.pelanggan || '';
        if (picField) picField.value = window.currentContractData.contact_person || window.currentContractData.pic || '';
        if (kontakField) kontakField.value = window.currentContractData.phone || window.currentContractData.kontak || '';
        if (lokasiField) lokasiField.value = window.currentContractData.location_name || window.currentContractData.lokasi || '';
        
        console.log('SPK fields populated:', {
            customer: pelangganField?.value,
            pic: picField?.value,
            phone: kontakField?.value,
            location: lokasiField?.value
        });
    } else {
        console.log('No contract data available, loading contract data...');
        // Load contract data if not available
        loadContractDataForSpk(currentContractId);
    }
    
    // Determine SPK type based on specification type
    let spkType = 'UNIT'; // default
    try {
        const card = document.querySelector(`[data-spek-id="${spekId}"]`);
        if (card && card.getAttribute('data-is-attachment') === 'true') {
            spkType = 'ATTACHMENT';
        }
    } catch(e) { 
        console.warn('Failed to determine SPK type from specification', e); 
    }
    
    // Reset form and set SPK type
    document.querySelector('#spkFromKontrakForm [name="jenis_spk"]').value = spkType;
    document.querySelector('#spkFromKontrakForm [name="delivery_plan"]').value = '';
    document.querySelector('#spkFromKontrakForm [name="jumlah_unit"]').value = '';
    document.querySelector('#spkFromKontrakForm [name="catatan"]').value = '';
    
    // Set maximum units based on specification
    setMaxUnitsForSpk(spekId);
    
    // Load units for ATTACHMENT target if needed
    if (spkType === 'ATTACHMENT') {
        console.log('SPK type is ATTACHMENT, loading units...');
        loadContractUnitsForAttachment(currentContractId);
        
        // Show attachment target section
        const attachmentSection = document.getElementById('attachmentTargetSection');
        const targetUnitSelect = document.getElementById('spkTargetUnitId');
        const jumlahUnitDiv = document.querySelector('[for="spkJumlahUnit"]')?.closest('.mb-3');
        
        if (attachmentSection) {
            attachmentSection.style.display = 'block';
        }
        if (targetUnitSelect) {
            targetUnitSelect.setAttribute('required', 'required');
        }
        if (jumlahUnitDiv) {
            jumlahUnitDiv.style.display = 'none';
        }
    } else {
        console.log('SPK type is UNIT, hiding attachment section...');
        // Hide attachment target section for UNIT type
        const attachmentSection = document.getElementById('attachmentTargetSection');
        const targetUnitSelect = document.getElementById('spkTargetUnitId');
        const jumlahUnitDiv = document.querySelector('[for="spkJumlahUnit"]')?.closest('.mb-3');
        
        if (attachmentSection) {
            attachmentSection.style.display = 'none';
        }
        if (targetUnitSelect) {
            targetUnitSelect.removeAttribute('required');
            targetUnitSelect.value = '';
        }
        if (jumlahUnitDiv) {
            jumlahUnitDiv.style.display = 'block';
        }
    }
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('spkFromKontrakModal'));
    modal.show();
}

// Function to set maximum units based on specification
function setMaxUnitsForSpk(spekId) {
    console.log('Setting max units for SPK, spekId:', spekId);
    
    try {
        // Find the specification card in the DOM
        const card = document.querySelector(`[data-spek-id="${spekId}"]`);
        if (card) {
            const jumlahDibutuhkan = Number(card.getAttribute('data-jumlah-dibutuhkan') || '0');
            const jumlahTersedia = Number(card.getAttribute('data-jumlah-tersedia') || '0');
            const available = Math.max(0, jumlahDibutuhkan - jumlahTersedia);
            
            console.log('Specification data:', {
                jumlahDibutuhkan,
                jumlahTersedia,
                available
            });
            
            const jumlahInput = document.getElementById('spkJumlahUnit');
            const hint = document.getElementById('jumlahUnitHint');
            const formEl = document.getElementById('spkFromKontrakForm');
            
            if (jumlahInput) {
                if (available > 0) {
                    jumlahInput.setAttribute('max', String(available));
                    jumlahInput.setAttribute('placeholder', `Maks ${available} unit`);
                    jumlahInput.setAttribute('min', '1');
                } else {
                    jumlahInput.removeAttribute('max');
                    jumlahInput.removeAttribute('placeholder');
                    jumlahInput.setAttribute('min', '1');
                }
            }
            
            if (hint) {
                hint.textContent = available > 0 ? `(maks ${available} unit)` : '(tidak ada unit tersedia)';
                hint.className = available > 0 ? 'text-muted' : 'text-danger';
            }
            
            if (formEl) {
                formEl.dataset.availableUnits = String(available);
            }
            
            console.log(`Max units set to: ${available}`);
        } else {
            console.warn('Specification card not found for spekId:', spekId);
            // Fallback: set default max
            const jumlahInput = document.getElementById('spkJumlahUnit');
            const hint = document.getElementById('jumlahUnitHint');
            
            if (jumlahInput) {
                jumlahInput.setAttribute('min', '1');
                jumlahInput.removeAttribute('max');
                jumlahInput.removeAttribute('placeholder');
            }
            
            if (hint) {
                hint.textContent = '';
                hint.className = 'text-muted';
            }
        }
    } catch(e) {
        console.error('Failed to set max units for SPK:', e);
    }
}

// Function to load contract data for SPK
function loadContractDataForSpk(kontrakId) {
    if (!kontrakId) return;
    
    console.log('Loading contract data for SPK, kontrakId:', kontrakId);
    
    fetch(`<?= base_url('marketing/kontrak/detail/') ?>${kontrakId}`)
        .then(r => r.json())
        .then(data => {
            console.log('Contract data loaded for SPK:', data);
            if (data && data.success) {
                window.currentContractData = data.data;
                
                // Populate SPK fields
                const pelangganField = document.getElementById('spkPelanggan');
                const picField = document.getElementById('spkPic');
                const kontakField = document.getElementById('spkKontak');
                const lokasiField = document.getElementById('spkLokasi');
                
                if (pelangganField) pelangganField.value = data.data.customer_name || data.data.pelanggan || '';
                if (picField) picField.value = data.data.contact_person || data.data.pic || '';
                if (kontakField) kontakField.value = data.data.phone || data.data.kontak || '';
                if (lokasiField) lokasiField.value = data.data.location_name || data.data.lokasi || '';
                
                console.log('SPK fields populated from loaded data:', {
                    customer: pelangganField?.value,
                    pic: picField?.value,
                    phone: kontakField?.value,
                    location: lokasiField?.value
                });
            }
        })
        .catch(err => {
            console.error('Failed to load contract data for SPK:', err);
        });
}

// Function to load units from contract for ATTACHMENT target
function loadContractUnitsForAttachment(kontrakId) {
    if (!kontrakId) return;
    
    console.log('Loading contract units for ATTACHMENT, kontrakId:', kontrakId);
    
    fetch(`<?= base_url('marketing/kontrak/units/') ?>${kontrakId}`)
        .then(r => r.json())
        .then(response => {
            console.log('Contract units loaded for ATTACHMENT:', response);
            const select = document.getElementById('spkTargetUnitId');
            if (!select) return;
            
            select.innerHTML = '<option value="">- Pilih Unit Tujuan -</option>';
            
            // Check if response has success and data array
            if (response && response.success && response.data && response.data.length > 0) {
                response.data.forEach(unit => {
                    const option = document.createElement('option');
                    option.value = unit.id;
                    option.textContent = `${unit.no_unit} - ${unit.serial_number} - ${unit.jenis_unit || 'N/A'}`;
                    option.dataset.sn = unit.serial_number;
                    select.appendChild(option);
                });
                console.log(`Loaded ${response.data.length} units for ATTACHMENT target`);
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = '-- Tidak ada unit terdaftar di kontrak ini --';
                option.disabled = true;
                select.appendChild(option);
                console.log('No units found for this contract');
            }
        })
        .catch(err => {
            console.error('Failed to load contract units:', err);
        });
}

// Toggle attachment target section based on jenis_spk
$(document).on('change', '#spkJenisSpk', function() {
    const attachmentSection = document.getElementById('attachmentTargetSection');
    const targetUnitSelect = document.getElementById('spkTargetUnitId');
    const jumlahUnitDiv = document.querySelector('[for="spkJumlahUnit"]')?.closest('.mb-3');
    
    if (this.value === 'ATTACHMENT') {
        // Show attachment target section
        if (attachmentSection) attachmentSection.style.display = 'block';
        if (targetUnitSelect) targetUnitSelect.setAttribute('required', 'required');
        
        // Hide jumlah unit (always 1 for attachment)
        if (jumlahUnitDiv) jumlahUnitDiv.style.display = 'none';
        document.getElementById('spkJumlahUnit').value = '1';
        
        // Load units for this contract
        const kontrakId = document.getElementById('spkKontrakId').value;
        if (kontrakId) {
            loadContractUnitsForAttachment(kontrakId);
        }
    } else {
        // Hide attachment target section
        if (attachmentSection) attachmentSection.style.display = 'none';
        if (targetUnitSelect) {
            targetUnitSelect.removeAttribute('required');
            targetUnitSelect.value = '';
        }
        
        // Show jumlah unit
        if (jumlahUnitDiv) jumlahUnitDiv.style.display = 'block';
    }
});

// SPK Form submission
$(document).on('submit', '#spkFromKontrakForm', function(e) {
    e.preventDefault();
    clearFormErrors('#spkFromKontrakForm');
    
    const formData = new FormData(this);
    
    // Validate required fields
    const kontrakId = formData.get('kontrak_id');
    const kontrakSpesifikasiId = formData.get('kontrak_spesifikasi_id');
    
    if (!kontrakId) {
        showNotification('Data kontrak tidak tersedia. Pastikan halaman sudah dimuat dengan benar.', 'error');
        return;
    }
    
    if (!formData.get('pelanggan')) {
        showNotification('Data pelanggan tidak tersedia. Pastikan detail kontrak sudah dimuat.', 'error');
        return;
    }
    
    if (!formData.get('delivery_plan') || !formData.get('jumlah_unit')) {
        showNotification('Lengkapi semua field wajib.', 'error');
        return;
    }
    
    // Validate ATTACHMENT specific fields
    const jenisSpk = formData.get('jenis_spk');
    if (jenisSpk === 'ATTACHMENT') {
        const targetUnitId = formData.get('target_unit_id');
        if (!targetUnitId) {
            showNotification('Unit Tujuan wajib dipilih untuk SPK ATTACHMENT.', 'error');
            return;
        }
        // Force jumlah_unit to 1 for attachment
        formData.set('jumlah_unit', '1');
    }
    
    // Validate maximum units based on specification
    const formEl = document.getElementById('spkFromKontrakForm');
    const availableUnits = Number(formEl?.dataset.availableUnits || '0');
    const requestedUnits = Number(formData.get('jumlah_unit'));
    
    if (availableUnits > 0 && requestedUnits > availableUnits) {
        showNotification(`Jumlah unit melebihi yang tersedia. Maksimal: ${availableUnits} unit`, 'error');
        return;
    }
    
    if (availableUnits === 0) {
        showNotification('Tidak ada unit tersedia untuk spesifikasi ini.', 'error');
        return;
    }
    
    // Debug form data before submission
    console.log('SPK Form Data being submitted:');
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }
    
    // Submit form using fetch API (same as kontrak.php)
    fetch('<?= base_url('marketing/spk/create') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(response => {
        console.log('SPK Create Response Status:', response.status);
        return response.json();
    })
    .then(res => {
        console.log('SPK Create Response:', res);
        if (res.success) {
            showNotification('SPK berhasil dibuat!', 'success');
            const modalEl = document.getElementById('spkFromKontrakModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            if (currentContractId) {
                loadContractSpesifikasiCorrect(currentContractId);
            }
        } else {
            showNotification(res.message || 'Gagal membuat SPK.', 'error');
        }
    })
    .catch(err => {
        console.error('SPK Create Error:', err);
        showNotification('Gagal membuat SPK: ' + err, 'error');
    });
});

// Print PDF functionality
$(document).on('click', '#printCustomerPDF', function() {
    if (!currentCustomerId) {
        showNotification('No customer selected', 'error');
        return;
    }
    
    // Show loading
    const originalText = $(this).html();
    $(this).html('<i class="fas fa-spinner fa-spin me-1"></i>Generating PDF...');
    $(this).prop('disabled', true);
    
    // Generate PDF URL
    const pdfUrl = `<?= base_url('marketing/customer-management/generatePDF/') ?>${currentCustomerId}`;
    
    // Open PDF in new tab
    const newWindow = window.open(pdfUrl, '_blank');
    
    // Reset button after a delay
    setTimeout(() => {
        $(this).html(originalText);
        $(this).prop('disabled', false);
    }, 2000);
    
    // Check if PDF opened successfully
    if (!newWindow || newWindow.closed || typeof newWindow.closed == 'undefined') {
        showNotification('PDF generation failed. Please try again.', 'error');
    } else {
        showNotification('PDF report is being generated...', 'success');
    }
});
</script>

<?= $this->endSection() ?>


