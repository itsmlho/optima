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
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<style>
    /* General Styles */
    .table-card { border: none; border-radius: 15px; box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1); overflow: hidden; }
    .card-stats:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); }
    .modal-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0; }
    .bg-orange { background-color: #fd7e14 !important; color: white; }
    
    /* Progress Bar */
    .progress { height: 22px; font-size: 0.75rem; background-color: #e9ecef; border-radius: 10px; }
    .progress-bar { font-weight: 600; background-color: #6c757d !important; color: #ffffff; }
    
    /* PO List (Left Panel) - for Attachment tab */
    .po-group-header { cursor: pointer; background-color: #f8f9fa; border-bottom: 1px solid #e9ecef; padding: 0.75rem 1.25rem; transition: background-color 0.2s ease; }
    .po-group-header:hover { background-color: #e9ecef; }
    .po-group-header .arrow-icon { transition: transform 0.3s ease; }
    .po-group-header.open .arrow-icon { transform: rotate(180deg); }
    .item-child-item { display: none; padding-left: 2.5rem; border-left: 3px solid #dee2e6; }
    .item-child-item:hover { border-left-color: #0d6efd; }
    .list-group-item.active { background-color: #e9ecef; border-color: #dee2e6; color: #212529; }
    
    /* Verification Components */
    .verification-component { border: 1px solid #dee2e6; border-radius: .5rem; margin-bottom: 1rem; transition: all 0.3s ease; overflow: hidden; border-left: 5px solid #6c757d; }
    .verification-component[data-status="sesuai"] { border-left-color: #198754; background-color: #f6fff8; }
    .verification-component[data-status="tidak-sesuai"] { border-left-color: #dc3545; background-color: #fff5f5; }
    .component-header { padding: .75rem 1.25rem; background-color: #f8f9fa; border-bottom: 1px solid #dee2e6; }
    .component-body { padding: 1.25rem; }
    .note-input-group, .sn-input-group { display: none; margin-top: 1rem; }
    .btn-verify-action.active { background-color: #e9ecef; box-shadow: inset 0 2px 4px rgba(0,0,0,.1); }
    
    /* Tab Content Containers */
    .tab-pane { min-height: 500px; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-primary text-white h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1" id="stat-total-po">0</h2>
                        <h6 class="card-title text-uppercase small opacity-75 fw-semibold">Total PO</h6>
                    </div>
                    <i class="fas fa-file-invoice fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-warning text-white h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1" id="stat-pending">0</h2>
                        <h6 class="card-title text-uppercase small opacity-75 fw-semibold">Pending</h6>
                    </div>
                    <i class="fas fa-clock fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-orange text-white h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1" id="stat-catatan">0</h2>
                        <h6 class="card-title text-uppercase small opacity-75 fw-semibold">Sebagian Reject</h6>
                    </div>
                    <i class="fas fa-exclamation-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-success text-white h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1" id="stat-completed">0</h2>
                        <h6 class="card-title text-uppercase small opacity-75 fw-semibold">Completed</h6>
                    </div>
                    <i class="fas fa-check-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card with Tabs -->
    <div class="card table-card">
        <!-- Tab Navigation -->
        <div class="card-body p-0">
            <ul class="nav nav-tabs nav-fill mb-0" id="poVerificationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="unit-tab" data-bs-toggle="tab" data-bs-target="#unit-verification" type="button" role="tab">
                        <i class="fas fa-truck me-1"></i>
                        <span>Unit</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="attachment-tab" data-bs-toggle="tab" data-bs-target="#attachment-verification" type="button" role="tab">
                        <i class="fas fa-puzzle-piece me-1"></i>
                        <span>Attachment</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="sparepart-tab" data-bs-toggle="tab" data-bs-target="#sparepart-verification" type="button" role="tab">
                        <i class="fas fa-cogs me-1"></i>
                        <span>Sparepart</span>
                    </button>
                </li>
            </ul>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- ========== UNIT VERIFICATION TAB ========== -->
            <div class="tab-pane fade show active" id="unit-verification" role="tabpanel">
                <div class="card-header border-top d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold m-0">
                        <i class="fas fa-truck me-2"></i>Purchase Order Unit - Verification
                    </h5>
                    <a href="<?= base_url('/purchasing/po-unitForm') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-2"></i>New PO Unit
                    </a>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <div class="card filter-card mb-4">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="fas fa-filter me-2"></i>Filters</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="filterStatusUnit" class="form-label">Status</label>
                                    <select id="filterStatusUnit" class="form-select">
                                        <option value="all">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filterSupplierUnit" class="form-label">Supplier</label>
                                    <select id="filterSupplierUnit" class="form-select">
                                        <option value="all">All Suppliers</option>
                                        <?php if (isset($suppliers) && is_array($suppliers)): ?>
                                            <?php foreach ($suppliers as $supplier): ?>
                                                <option value="<?= esc($supplier['id_supplier']) ?>"><?= esc($supplier['nama_supplier']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filterDateFromUnit" class="form-label">Date From</label>
                                    <input type="date" id="filterDateFromUnit" class="form-control" value="<?= date('Y-m-01'); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="filterDateToUnit" class="form-label">Date To</label>
                                    <input type="date" id="filterDateToUnit" class="form-control" value="<?= date('Y-m-t'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <table id="poUnitTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Date</th>
                                <th>Supplier</th>
                                <th>Status</th>
                                <th>Progres Verifikasi</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <!-- ========== ATTACHMENT VERIFICATION TAB ========== -->
            <div class="tab-pane fade" id="attachment-verification" role="tabpanel">
                <div class="card-header border-top">
                    <h5 class="card-title fw-bold m-0 text-center">
                        <i class="fas fa-puzzle-piece me-2"></i>Purchase Order Attachment & Battery - Verification
                    </h5>
                </div>

                <div class="card-body p-0">
                    <div class="row g-0">
                        <!-- Left Panel: Item List -->
                        <div class="col-md-4 border-end">
                            <div class="card-header text-center bg-light">
                                <h6 class="fw-bold m-0">Item untuk Diverifikasi</h6>
                            </div>
                            <div class="list-group list-group-flush" id="attachment-item-list" style="max-height: 600px; overflow-y: auto;">
                                <!-- Items will be loaded here -->
                                <?php if (empty($detailGroup)): ?>
                                    <div class="list-group-item">Tidak ada item yang perlu diverifikasi.</div>
                                <?php else: ?>
                                    <?php foreach ($detailGroup as $key => $value): ?>
                                        <div class="list-group-item po-group-header" onclick="toggleAttachmentDropdown(this)" data-po-id="<?= $key ?>">
                                            <div class="d-flex w-100 justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($value["no_po"]) ?></h6>
                                                    <p class="mb-0 text-muted small">Sisa: <span id="lbl-remain-po-<?= $key ?>"><?= count($value["data"]) ?> Item</span></p>
                                                </div>
                                                <i class="fas fa-chevron-down arrow-icon"></i>
                                            </div>
                                        </div>
                                        <?php foreach ($value['data'] as $item): ?>
                                            <a href="#" class="list-group-item list-group-item-action item-child-item child-po-<?= $key ?>" 
                                               data-item='<?= json_encode($item) ?>' 
                                               id="list-item-<?= $item['id_po_item'] ?>">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <?php if ($item['item_type'] === 'Attachment'): ?>
                                                            <i class="fas fa-paperclip fa-2x text-secondary"></i>
                                                        <?php else: ?>
                                                            <i class="fas fa-battery-full fa-2x text-success"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="flex-grow-1" style="min-width: 0;">
                                                        <?php 
                                                            $itemName = ($item['item_type'] === 'Attachment') 
                                                                ? ($item['attachment_name'] ?? 'N/A') 
                                                                : (($item['merk_baterai'] ?? '') . ' ' . ($item['tipe_baterai'] ?? ''));
                                                        ?>
                                                        <h6 class="mb-1 fw-bold text-truncate" title="<?= esc($itemName) ?>"><?= esc($itemName) ?></h6>
                                                        <p class="mb-0 text-muted small">Tipe: <strong><?= esc($item['item_type'] ?? 'N/A') ?></strong></p>
                                                    </div>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Right Panel: Detail View -->
                        <div class="col-md-8">
                            <div id="attachment-detail-view-container" class="p-4">
                                <div class="text-center p-5">
                                    <i class="fas fa-hand-pointer fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Pilih item dari daftar di sebelah kiri untuk verifikasi.</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========== SPAREPART VERIFICATION TAB ========== -->
            <div class="tab-pane fade" id="sparepart-verification" role="tabpanel">
                <div class="card-header border-top d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold m-0">
                        <i class="fas fa-cogs me-2"></i>Purchase Order Sparepart - Verification
                    </h5>
                    <a href="<?= base_url('/purchasing/po-sparepartForm') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-2"></i>New PO Sparepart
                    </a>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <div class="card filter-card mb-4">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="fas fa-filter me-2"></i>Filters</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="filterStatusSparepart" class="form-label">Filter Status</label>
                                    <select id="filterStatusSparepart" class="form-select">
                                        <option value="">Semua Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="completed">Completed</option>
                                        <option value="Selesai dengan Catatan">Selesai dengan Catatan</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filterSupplierSparepart" class="form-label">Filter Supplier</label>
                                    <select id="filterSupplierSparepart" class="form-select">
                                        <option value="">Semua Supplier</option>
                                        <?php if (isset($suppliers) && is_array($suppliers)): ?>
                                            <?php foreach ($suppliers as $supplier): ?>
                                                <option value="<?= esc($supplier['nama_supplier']) ?>"><?= esc($supplier['nama_supplier']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filterDateFromSparepart" class="form-label">Dari Tanggal</label>
                                    <input type="date" id="filterDateFromSparepart" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label for="filterDateToSparepart" class="form-label">Sampai Tanggal</label>
                                    <input type="date" id="filterDateToSparepart" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- DataTable -->
                    <table id="poSparepartTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No. PO</th>
                                <th>Tanggal</th>
                                <th>Supplier</th>
                                <th>Status</th>
                                <th>Progres Verifikasi</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal Verifikasi Attachment -->
<div class="modal fade" id="modalAttachmentVerification" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAttachmentVerificationLabel">
                    <i class="fas fa-clipboard-check me-2"></i>Formulir Inspeksi Item
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formAttachmentVerification">
                    <input type="hidden" id="attachment_item_id">
                    <input type="hidden" id="attachment_po_id">
                    <p class="text-muted mb-4">Periksa setiap komponen di bawah ini dan isi informasi yang diperlukan.</p>
                    <div id="attachment-verification-components"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-submit-attachment-verification" disabled>Submit Verifikasi</button>
            </div>
        </div>
    </div>
</div>

<!-- View PO Unit Modal -->
<div class="modal fade" id="viewPOUnitModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>PO Unit Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="poUnitDetailsContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- View PO Sparepart Modal -->
<div class="modal fade" id="viewPOSparepartModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Detail Purchase Order Sparepart</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="poSparepartDetailsContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // ========================================
    // GLOBAL VARIABLES & UTILITIES
    // ========================================
    const baseURL = '<?= base_url() ?>';
    const csrfTokenName = '<?= csrf_token() ?>';
    const csrfHash = '<?= csrf_hash() ?>';
    
    let totalStats = { total: 0, pending: 0, catatan: 0, completed: 0 };
    let poUnitTable, poSparepartTable;

    $(document).ready(function() {
        console.log('🔧 PO Verification Page Initialized');
        
        // Initialize all tabs
        initializeUnitTab();
        initializeAttachmentTab();
        initializeSparepartTab();
        
        // Tab switching handler
        $('#poVerificationTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            const targetTab = $(e.target).attr('data-bs-target');
            console.log('Tab switched to:', targetTab);
            
            // Trigger reload for DataTables when switching
            if (targetTab === '#unit-verification' && poUnitTable) {
                poUnitTable.ajax.reload();
            } else if (targetTab === '#sparepart-verification' && poSparepartTable) {
                poSparepartTable.ajax.reload();
            }
        });
    });

    function updateGlobalStatistics() {
        $('#stat-total-po').text(totalStats.total);
        $('#stat-pending').text(totalStats.pending);
        $('#stat-catatan').text(totalStats.catatan);
        $('#stat-completed').text(totalStats.completed);
    }

    // ========================================
    // UNIT VERIFICATION TAB
    // ========================================
    function initializeUnitTab() {
        poUnitTable = $('#poUnitTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('/purchasing/api/get-data-po/unit') ?>',
                type: 'POST',
                data: function(d) {
                    d.status = $("#filterStatusUnit").val();
                    d.supplier = $("#filterSupplierUnit").val();
                    d.start_date = $("#filterDateFromUnit").val();
                    d.end_date = $("#filterDateToUnit").val();
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                }
            },
            columns: [
                { data: 'no_po' },
                { data: 'tanggal_po' },
                { data: 'nama_supplier' },
                { 
                    data: 'status',
                    render: function(data) {
                        const badgeClass = {
                            'pending': 'bg-warning', 
                            'approved': 'bg-success', 
                            'completed': 'bg-success', 
                            'cancelled': 'bg-danger',
                            'Selesai dengan Catatan': 'bg-orange'
                        }[data] || 'bg-secondary';
                        return `<span class="badge ${badgeClass}">${data}</span>`;
                    }
                },
                { 
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        const total = parseInt(row.total_items, 10);
                        const processed = parseInt(row.processed_items, 10);
                        const rejected = parseInt(row.rejected_items, 10);
                        
                        if (isNaN(total) || total === 0) {
                            return `<span class="text-muted small fst-italic">Tidak Ada Item</span>`;
                        }

                        const percentage = total > 0 ? Math.round((processed / total) * 100) : 0;
                        let warningIcon = rejected > 0 ? `<i class="fas fa-exclamation-triangle text-danger ms-2" title="${rejected} item tidak sesuai"></i>` : '';

                        return `
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1" title="${processed} dari ${total} item diproses">
                                    <div class="progress-bar progress-bar-striped" role="progressbar" style="width: ${percentage}%;">
                                        ${processed} / ${total}
                                    </div>
                                </div>
                                ${warningIcon}
                            </div>
                        `;
                    }
                },
                { 
                    data: 'id_po',
                    orderable: false,
                    render: function(data, type, row) {
                        let specialActionButtons = '';
                        if (row.status === 'Selesai dengan Catatan') {
                            specialActionButtons = `
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-primary" href="#" onclick="reverifyUnitPO(${data})"><i class="fas fa-sync-alt me-2"></i>Verifikasi Ulang</a></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="cancelUnitPO(${data})"><i class="fas fa-ban me-2"></i>Selesaikan (Batal)</a></li>
                            `;
                        }

                        return `
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#" onclick="viewUnitPO(${data})"><i class="fas fa-eye me-2"></i>Lihat Detail</a></li>
                                    <li><a class="dropdown-item" href="<?= base_url('purchasing/edit-po-unit/') ?>${data}"><i class="fas fa-edit me-2"></i>Edit PO</a></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteUnitPO(${data})"><i class="fas fa-trash me-2"></i>Hapus PO</a></li>
                                    ${specialActionButtons}
                                </ul>
                            </div>
                        `;
                    }
                }
            ],
            order: [[1, 'desc']],
            responsive: true,
            drawCallback: function(settings) {
                const api = this.api();
                const json = api.ajax.json();
                if (json && json.stats) {
                    totalStats = json.stats;
                    updateGlobalStatistics();
                }
            }
        });

        // Filter events
        $('#filterStatusUnit, #filterSupplierUnit, #filterDateFromUnit, #filterDateToUnit').on('change', function() {
            poUnitTable.ajax.reload();
        });
    }

    function viewUnitPO(id) {
        console.log('View Unit PO:', id);
        // Implementation from po_unit.php
    }

    function deleteUnitPO(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data PO ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'DELETE',
                    url: '<?= base_url('/purchasing/delete-po-unit/') ?>' + id,
                    success: function(data) {
                        if (data.success) {
                            Swal.fire('Dihapus!', 'PO berhasil dihapus.', 'success');
                            poUnitTable.ajax.reload();
                        } else {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus PO.', 'error');
                        }
                    }
                });
            }
        });
    }

    function reverifyUnitPO(id) {
        console.log('Reverify Unit PO:', id);
        // Implementation here
    }

    function cancelUnitPO(id) {
        console.log('Cancel Unit PO:', id);
        // Implementation here
    }

    // ========================================
    // ATTACHMENT VERIFICATION TAB
    // ========================================
    function initializeAttachmentTab() {
        // Handle item click
        $('#attachment-item-list').on('click', '.item-child-item', function(e) {
            e.preventDefault();
            $('.item-child-item').removeClass('active');
            $(this).addClass('active');
            const itemData = $(this).data('item');
            $('#attachment-detail-view-container').html(createAttachmentDetailCard(itemData));
        });

        // Handle verification submit
        $('#btn-submit-attachment-verification').on('click', submitAttachmentVerification);
    }

    function toggleAttachmentDropdown(element) {
        const poId = $(element).data('po-id');
        $(element).toggleClass('open');
        $(`.child-po-${poId}`).slideToggle('fast');
    }

    function createAttachmentDetailCard(data) {
        const h = (str) => str ? String(str).replace(/</g, '&lt;') : "-";
        const itemName = (data.item_type === 'Attachment') ? data.attachment_name : `${data.merk_baterai} ${data.tipe_baterai}`;
        
        return `
            <div class="animate__animated animate__fadeIn">
                <div class="card">
                    <div class="card-header p-3 text-center bg-light">
                        <h5 class="fw-bold m-0"><i class="fas fa-info-circle me-2 text-secondary"></i>Detail: ${h(itemName)}</h5>
                    </div>
                    <div class="card-body p-4">
                        <table class="table table-sm table-borderless">
                            <tr><td width="30%"><strong>Tipe Item</strong></td><td>: <span class="badge bg-secondary">${h(data.item_type)}</span></td></tr>
                            <tr><td><strong>PO Number</strong></td><td>: ${h(data.no_po)}</td></tr>
                            <tr><td class="align-top"><strong>Keterangan</strong></td><td class="align-top">: ${h(data.keterangan)}</td></tr>
                        </table>
                    </div>
                    <div class="card-footer text-center">
                        <button onclick="prepareAttachmentVerificationModal(this)" class="btn btn-success" data-item='${JSON.stringify(data)}'>
                            <i class="fas fa-check-circle"></i> Verifikasi Item
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    function prepareAttachmentVerificationModal(element) {
        const data = $(element).data('item');
        const itemName = (data.item_type === 'Attachment') ? data.attachment_name : `${data.merk_baterai} ${data.tipe_baterai}`;
        
        $('#modalAttachmentVerificationLabel').text(`Inspeksi: ${itemName}`);
        $('#attachment_item_id').val(data.id_po_item);
        $('#attachment_po_id').val(data.po_id);

        const container = $('#attachment-verification-components');
        container.empty();

        if (data.item_type === 'Attachment') {
            container.append(createComponentHTML({ id: 'attachment', label: 'Attachment', sn: true, desc: data.attachment_name }));
        } else {
            container.append(createComponentHTML({ id: 'baterai', label: 'Baterai', sn: true, desc: `${data.merk_baterai} ${data.tipe_baterai}` }));
            container.append(createComponentHTML({ id: 'charger', label: 'Charger', sn: true, desc: `${data.merk_charger} ${data.tipe_charger}` }));
        }
        
        $('#modalAttachmentVerification').modal('show');
    }

    function createComponentHTML(component) {
        const snInputHTML = component.sn ? 
            `<div class="sn-input-group"><label for="sn_${component.id}" class="form-label small fw-bold">Serial Number</label><input type="text" class="form-control sn-input" id="sn_${component.id}"></div>` : '';
        
        return `
            <div class="verification-component" data-component="${component.id}" data-status="menunggu">
                <div class="component-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">${component.label}</h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-success btn-verify-action" onclick="setAttachmentComponentStatus('${component.id}', 'sesuai', this)">
                            <i class="fas fa-check"></i> Sesuai
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-verify-action" onclick="setAttachmentComponentStatus('${component.id}', 'tidak-sesuai', this)">
                            <i class="fas fa-times"></i> Tidak Sesuai
                        </button>
                    </div>
                </div>
                <div class="component-body">
                    <p class="spec-details mb-0">${component.desc}</p>
                    ${snInputHTML}
                    <div class="note-input-group">
                        <label for="note_${component.id}" class="form-label small fw-bold text-danger">Catatan Ketidaksesuaian</label>
                        <textarea class="form-control note-input" id="note_${component.id}" rows="2"></textarea>
                    </div>
                </div>
            </div>
        `;
    }

    function setAttachmentComponentStatus(componentId, status, button) {
        const component = $(`[data-component="${componentId}"]`);
        component.attr('data-status', status);
        $(button).addClass('active').siblings().removeClass('active');
        
        const snGroup = component.find('.sn-input-group');
        const noteGroup = component.find('.note-input-group');
        
        if (status === 'sesuai') {
            if (snGroup.length) snGroup.slideDown('fast');
            noteGroup.slideUp('fast');
        } else if (status === 'tidak-sesuai') {
            snGroup.slideUp('fast');
            noteGroup.slideDown('fast');
        }
        
        checkAllAttachmentVerified();
    }

    function checkAllAttachmentVerified() {
        const totalComponents = $('.verification-component').length;
        const verifiedComponents = $('.verification-component').filter((i, el) => $(el).attr('data-status') !== 'menunggu').length;
        $('#btn-submit-attachment-verification').prop('disabled', totalComponents !== verifiedComponents);
    }

    function submitAttachmentVerification() {
        if (window._verifyingAttachment) return;
        
        let finalStatus = 'Sesuai';
        let fullNotes = [];
        const snData = {};

        $('.verification-component').each(function() {
            const component = $(this);
            const componentId = component.data('component');
            const status = component.attr('data-status');

            if (status === 'tidak-sesuai') {
                finalStatus = 'Tidak Sesuai';
                const note = component.find('.note-input').val();
                if (note) {
                    fullNotes.push(`${component.find('h6').text()}: ${note}`);
                }
            }

            if (component.find('.sn-input').length) {
                if(componentId === 'baterai' || componentId === 'attachment') {
                    snData['serial_number'] = component.find('.sn-input').val();
                } else {
                    snData[`serial_number_${componentId}`] = component.find('.sn-input').val();
                }
            }
        });
        
        if (finalStatus === 'Sesuai' && !snData['serial_number']) {
            Swal.fire({icon:'warning', title:'SN Wajib', text:'Serial number wajib diisi untuk status Sesuai.'});
            return;
        }
        
        const idItem = $('#attachment_item_id').val();
        const poId = $('#attachment_po_id').val();
        updateAttachmentStatusVerifikasi(idItem, poId, finalStatus, snData, fullNotes.join('; '));
    }

    function updateAttachmentStatusVerifikasi(itemId, poId, status, snData = {}, catatan = '') {
        window._verifyingAttachment = true;
        $('#btn-submit-attachment-verification').prop('disabled', true);
        
        $.ajax({
            type: "POST",
            url: "<?= base_url('warehouse/purchase-orders/verify-po-attachment') ?>",
            data: {
                id_item: itemId,
                po_id: poId,
                status: status,
                catatan_verifikasi: catatan,
                ...snData,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: "JSON",
            beforeSend: () => Swal.showLoading(),
            success: function(response) {
                window._verifyingAttachment = false;
                $('#btn-submit-attachment-verification').prop('disabled', false);
                Swal.close();
                
                if (response.success) {
                    $('#modalAttachmentVerification').modal('hide');
                    Swal.fire('Berhasil!', 'Verifikasi berhasil!', 'success');
                    
                    let sisaElem = $(`#lbl-remain-po-${poId}`);
                    let sisaCount = parseInt(sisaElem.text()) - 1;
                    sisaElem.text(`${sisaCount} Item`);
                    
                    $(`#list-item-${itemId}`).fadeOut(500, function() { 
                        $(this).remove(); 
                        if (sisaCount === 0) {
                            $(`[data-po-id="${poId}"]`).fadeOut(500);
                        }
                    });

                    $('#attachment-detail-view-container').html(`
                        <div class="text-center p-5">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-muted">Verifikasi berhasil! Silakan pilih item lain.</h5>
                        </div>
                    `);
                } else {
                    Swal.fire('Error!', response.message || 'Terjadi kesalahan.', 'error');
                }
            },
            error: (xhr) => {
                window._verifyingAttachment = false;
                $('#btn-submit-attachment-verification').prop('disabled', false);
                Swal.fire("Error", "Terjadi kesalahan tak terduga.", "error");
                console.error(xhr.responseText);
            }
        });
    }

    // ========================================
    // SPAREPART VERIFICATION TAB
    // ========================================
    function initializeSparepartTab() {
        poSparepartTable = $('#poSparepartTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('/purchasing/po-sparepart') ?>',
                type: 'POST',
                data: function(d) {
                    d.status = $('#filterStatusSparepart').val();
                    d.supplier = $('#filterSupplierSparepart').val();
                    d.start_date = $('#filterDateFromSparepart').val();
                    d.end_date = $('#filterDateToSparepart').val();
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                }
            },
            columns: [
                { data: 'no_po' },
                { 
                    data: 'tanggal_po',
                    render: function(data) {
                        return new Date(data).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
                    }
                },
                { data: 'nama_supplier' },
                { 
                    data: 'status',
                    render: function(data) {
                        const badgeClass = {
                            'pending': 'bg-warning', 
                            'approved': 'bg-success', 
                            'completed': 'bg-success', 
                            'cancelled': 'bg-danger',
                            'Selesai dengan Catatan': 'bg-orange'
                        }[data] || 'bg-secondary';
                        return `<span class="badge ${badgeClass}">${data}</span>`;
                    }
                },
                { 
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        const total = parseInt(row.total_items, 10);
                        const sesuai = parseInt(row.sesuai_items, 10);
                        const processed = parseInt(row.processed_items, 10);
                        const rejected = parseInt(row.rejected_items, 10);
                        
                        if (isNaN(total) || total === 0) {
                            return `<span class="text-muted small fst-italic">Tidak Ada Item</span>`;
                        }

                        const percentage = Math.round((sesuai / total) * 100);
                        let warningIcon = rejected > 0 ? `<i class="fas fa-exclamation-triangle text-danger ms-2" title="${rejected} item tidak sesuai"></i>` : '';

                        return `
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1" title="${sesuai} dari ${total} item sesuai">
                                    <div class="progress-bar progress-bar-striped" role="progressbar" style="width: ${percentage}%;">
                                        ${processed} / ${total}
                                    </div>
                                </div>
                                ${warningIcon}
                            </div>
                        `;
                    }
                },
                { 
                    data: 'id_po', 
                    orderable: false, 
                    render: function(data, type, row) {
                        let resolveButton = '';
                        if (row.status === 'Selesai dengan Catatan') {
                            resolveButton = `<li><a class="dropdown-item text-success" href="#" onclick="resolveSparepartPO(${data})"><i class="fas fa-check-double me-2"></i>Tandai Selesai</a></li>`;
                        }

                        return `
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="viewSparepartPO(${data})"><i class="fas fa-eye me-2"></i>Lihat Detail</a></li>
                                    <li><a class="dropdown-item" href="<?= base_url('purchasing/edit-po-sparepart/') ?>${data}"><i class="fas fa-edit me-2"></i>Edit PO</a></li>
                                    ${resolveButton}
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteSparepartPO(${data})"><i class="fas fa-trash me-2"></i>Hapus PO</a></li>
                                </ul>
                            </div>
                        `;
                    }
                }
            ],
            responsive: true,
            language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json" }
        });

        // Filter events
        $('#filterStatusSparepart, #filterSupplierSparepart, #filterDateFromSparepart, #filterDateToSparepart').on('change', function() {
            poSparepartTable.ajax.reload();
        });
    }

    function viewSparepartPO(id) {
        console.log('View Sparepart PO:', id);
        // Implementation here
    }

    function deleteSparepartPO(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data PO ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?= base_url('purchasing/delete-po-sparepart/') ?>${id}`,
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', 'Data PO telah dihapus.', 'success');
                            poSparepartTable.ajax.reload();
                        } else {
                            Swal.fire('Gagal!', response.message || 'Gagal menghapus data.', 'error');
                        }
                    }
                });
            }
        });
    }

    function resolveSparepartPO(id) {
        Swal.fire({
            title: 'Selesaikan PO ini?',
            text: "Pastikan semua masalah dengan supplier sudah selesai. Status akan diubah menjadi 'Completed'.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Ya, Selesaikan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?= base_url('purchasing/resolve-po-sparepart/') ?>${id}`,
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', 'Status PO telah diubah menjadi Completed.', 'success');
                            poSparepartTable.ajax.reload();
                        } else {
                            Swal.fire('Gagal!', response.message || 'Gagal mengubah status.', 'error');
                        }
                    }
                });
            }
        });
    }

    // Initialize DataTable for sorting and search functionality
    $(document).ready(function() {
        $('#poUnitTable, #poSparepartTable').DataTable({
            processing: true,
            pageLength: 25,
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: [-1] } // Disable sorting on last column (actions)
            ]
        });
    });
</script>

<?= $this->endSection() ?>
