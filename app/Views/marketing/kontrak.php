<?= $this->extend('layouts/base') ?>

<?php
helper('simple_rbac');
$can_view = can_view('marketing');
$can_create = can_create('marketing');
$can_export = can_export('marketing');
?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-3">
    <h4 class="fw-bold mb-1">
        <i class="bi bi-file-earmark-text me-2 text-primary"></i>
        Contracts & Purchase Orders Management
    </h4>
    <p class="text-muted mb-0">Manage formal rental contracts, PO-only agreements, and track contract renewals</p>
</div>

<!-- Statistics Cards -->
<div class="row mt-3 mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-primary-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-file-text stat-icon text-primary"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-total-contracts">0</div>
                    <div class="text-muted">Total Contracts & PO</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-info-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-file-contract stat-icon text-info"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-formal-contracts">0</div>
                    <div class="text-muted">Formal Contracts</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-warning-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-file-invoice stat-icon text-warning"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-po-only">0</div>
                    <div class="text-muted">PO Only</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-success-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-check-circle stat-icon text-success"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-active">0</div>
                    <div class="text-muted">Active</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card mb-3">
    <div class="card-header bg-light">
        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label for="filter_rental_type" class="form-label">Rental Type</label>
                <select class="form-select" id="filter_rental_type">
                    <option value="">All Types</option>
                    <option value="CONTRACT">Contract</option>
                    <option value="PO_ONLY">PO Only</option>
                    <option value="DAILY_SPOT">Daily/Spot</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter_status" class="form-label">Status</label>
                <select class="form-select" id="filter_status">
                    <option value="">All Status</option>
                    <option value="ACTIVE">Active</option>
                    <option value="PENDING">Pending</option>
                    <option value="EXPIRED">Expired</option>
                    <option value="CANCELLED">Cancelled</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="filter_customer" class="form-label">Customer</label>
                <select class="form-select" id="filter_customer">
                    <option value="">All Customers</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-primary w-100" onclick="applyFilters()">
                    <i class="fas fa-search me-1"></i>Apply
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Active Filter Banner (shown when filtered from URL) -->
<div id="activeFilterBanner" style="display:none"></div>

<!-- Contracts Table Card -->
<div class="card table-card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <h5 class="mb-0">Contract & PO Management</h5>
            <!-- View Mode Toggle -->
            <div class="btn-group btn-group-sm" role="group" id="viewModeToggle">
                <button type="button" class="btn btn-secondary active" id="btnFlatView" onclick="switchViewMode('flat')">
                    <i class="fas fa-list me-1"></i>Flat
                </button>
                <button type="button" class="btn btn-outline-secondary" id="btnGroupedView" onclick="switchViewMode('grouped')">
                    <i class="fas fa-layer-group me-1"></i>By Customer
                </button>
            </div>
        </div>
        <div class="d-flex gap-2">
            <?php if ($can_create): ?>
                <button type="button" class="btn btn-primary btn-sm" onclick="openAddContractModal()">
                    <i class="fas fa-plus me-1"></i>Create Contract
                </button>
            <?php endif; ?>
            <?php if ($can_export): ?>
                <button type="button" class="btn btn-success btn-sm" onclick="exportContracts()">
                    <i class="fas fa-file-excel me-1"></i>Export
                </button>
            <?php endif; ?>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="refreshView()">
                <i class="fas fa-sync me-1"></i>Refresh
            </button>
        </div>
    </div>
    <!-- FLAT VIEW body -->
    <div id="flatViewBody" class="card-body p-0" style="overflow: visible;">
        <div class="table-responsive" style="overflow: visible;">
            <table class="table table-striped table-hover mb-0" id="contractsTable">
                <thead class="bg-light">
                    <tr>
                        <th>Kontrak / PO</th>
                        <th>Tipe</th>
                        <th>Billing</th>
                        <th>Periode &amp; Sisa Hari</th>
                        <th>Unit</th>
                        <th>Nilai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables will populate -->
                </tbody>
            </table>
        </div>
    </div>
    <!-- GROUPED VIEW body (inside same card, hidden by default) -->
    <div id="groupedViewBody" style="display:none">
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status"></div>
            <p class="text-muted">Memuat data...</p>
        </div>
    </div>
</div>

<!-- Sprint 1-3: Billing Enhancement Components -->
<?= $this->include('components/renewal_wizard') ?>
<?= $this->include('components/addendum_prorate') ?>
<?= $this->include('components/asset_history') ?>

<!-- Contract Detail Modal REMOVED: 
     Konten detail sudah tersedia di dedicated page kontrak_detail.php.
     Tombol "View Detail" di tabel sudah mengarah ke /marketing/kontrak/detail/{id}.
     Modal ini dihapus untuk menghindari konflik DOM ID (contractInfoContent, dll). -->

<!-- Direct Contract Creation Modal -->
<div class="modal fade modal-wide" id="addContractModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Create New Contract</h5>
                    <small class="text-muted">Direct Contract Creation - No Quotation Required</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addContractForm">
                <div class="modal-body">
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>Contract Creation Workflow
                        </h6>
                        <ol class="mb-0 ps-3">
                            <li><strong>Step 1:</strong> Enter basic contract information (this form)</li>
                            <li><strong>Step 2:</strong> Add unit specifications as needed</li>
                            <li><strong>Step 3:</strong> Create SPK to allocate units from inventory</li>
                        </ol>
                        <hr>
                        <small class="text-muted">
                            <i class="fas fa-lightbulb me-1"></i>
                            <strong>Tip:</strong> Contract value and total units will be calculated automatically based on specifications added.
                        </small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contract Number*</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="contract_number" required>
                                <button class="btn btn-outline-secondary" type="button" id="generateContractNumber" title="Generate Contract Number">
                                    <i class="fas fa-magic"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Client PO Number</label>
                            <input type="text" class="form-control" name="po_number" placeholder="Customer PO Number (if any)">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Customer*</label>
                            <select class="form-select" name="customer_id" id="contractCustomerSelect" required>
                                <option value="">-- Select Customer --</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Location*</label>
                            <select class="form-select" name="customer_location_id" id="contractLocationSelect" required disabled>
                                <option value="">-- Select Customer First --</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date*</label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date*</label>
                            <input type="date" class="form-control" name="end_date" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rental Classification*</label>
                            <select class="form-select" name="rental_type" required>
                                <option value="CONTRACT" selected>Formal Contract</option>
                                <option value="PO_ONLY">PO-Based Only</option>
                                <option value="DAILY_SPOT">Daily/Spot Rental</option>
                            </select>
                            <small class="text-muted">How is this rental documented?</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Billing Period*</label>
                            <select class="form-select" name="jenis_sewa" required>
                                <option value="BULANAN" selected>Monthly</option>
                                <option value="HARIAN">Daily</option>
                            </select>
                            <small class="text-muted">Rental billing period</small>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="catatan" rows="3" placeholder="Additional notes (optional)"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save Contract
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
let contractsTable;

$(document).ready(function() {
    // Load statistics
    loadStatistics();
    
    // Load customers dropdown, then auto-apply URL filters
    loadCustomersDropdown(function() {
        applyUrlFilters();
    });
    
    // Initialize DataTable
    initializeContractsTable();
});

// Auto-apply filters from URL query params (e.g. ?customer_id=5&status=ACTIVE)
function applyUrlFilters() {
    const params = new URLSearchParams(window.location.search);
    let hasFilter = false;
    
    if (params.get('customer_id')) {
        $('#filter_customer').val(params.get('customer_id'));
        hasFilter = true;
    }
    if (params.get('rental_type')) {
        $('#filter_rental_type').val(params.get('rental_type'));
        hasFilter = true;
    }
    if (params.get('status')) {
        $('#filter_status').val(params.get('status'));
        hasFilter = true;
    }
    
    if (hasFilter) {
        // Show active filter banner
        const customerName = $('#filter_customer option:selected').text();
        if (params.get('customer_id') && customerName && customerName !== 'All Customers') {
            $('#activeFilterBanner').html(`<div class="alert alert-info alert-sm mb-3 d-flex justify-content-between align-items-center">
                <span><i class="fas fa-filter me-2"></i>Menampilkan kontrak untuk: <strong>${customerName}</strong></span>
                <button class="btn btn-sm btn-outline-info" onclick="clearAllFilters()"><i class="fas fa-times me-1"></i>Hapus Filter</button>
            </div>`).show();
        }
        applyFilters();
    }
}

// Clear all filters and reload
function clearAllFilters() {
    $('#filter_rental_type').val('');
    $('#filter_status').val('');
    $('#filter_customer').val('');
    $('#activeFilterBanner').hide().empty();
    // Clean URL
    window.history.replaceState({}, '', window.location.pathname);
    applyFilters();
}

// Load statistics
function loadStatistics() {
    $.ajax({
        url: '<?= base_url('marketing/kontrak/stats') ?>',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const stats = response.data;
                $('#stat-total-contracts').text(stats.total_contracts || 0);
                $('#stat-formal-contracts').text(stats.total_formal_contracts || 0);
                $('#stat-po-only').text(stats.total_po_only || 0);
                $('#stat-active').text(stats.total_active || 0);
            }
        },
        error: function() {
            console.error('Failed to load statistics');
        }
    });
}

// Load customers for filter dropdown
function loadCustomersDropdown(callback) {
    $.ajax({
        url: '<?= base_url('marketing/kontrak/customers-dropdown') ?>',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const $select = $('#filter_customer');
                response.data.forEach(customer => {
                    $select.append(new Option(customer.customer_name, customer.id));
                });
            }
            if (typeof callback === 'function') callback();
        },
        error: function() {
            if (typeof callback === 'function') callback();
        }
    });
}

// Initialize DataTable
function initializeContractsTable() {
    contractsTable = $('#contractsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('marketing/kontrak/getDataTable') ?>',
            type: 'POST',
            beforeSend: function(xhr) {
                // Add CSRF token to header
                if (window.csrfToken) {
                    xhr.setRequestHeader('X-CSRFToken', window.csrfToken);
                }
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            },
            data: function(d) {
                // Add CSRF token to POST data
                if (window.csrfToken) {
                    d.csrf_test_name = window.csrfToken;
                }
                // Add custom filters
                d.rental_type = $('#filter_rental_type').val();
                d.status = $('#filter_status').val();
                d.customer_id = $('#filter_customer').val();
            }
        },
        columns: [
            { 
                data: 'contract_number',
                render: function(data, type, row) {
                    const customerName = row.client_name || '—';
                    const contractNo = data || '—';
                    const poNo = row.po ? `<br><small class="text-muted"><i class="fas fa-file-invoice me-1"></i>PO: ${row.po}</small>` : '';
                    return `<div class="fw-semibold">${customerName}</div>
                            <small class="text-muted font-monospace">${contractNo}</small>${poNo}`;
                }
            },
            { data: 'rental_type' },
            { 
                data: 'jenis_sewa',
                render: function(data) {
                    const map = { 'BULANAN': 'Monthly', 'HARIAN': 'Daily' };
                    return map[data] || data || '—';
                }
            },
            { 
                data: 'period',
                render: function(data, type, row) {
                    // Fix invalid dates (year <= 0 or null)
                    function safeDate(str) {
                        if (!str) return null;
                        const d = new Date(str);
                        if (isNaN(d.getTime()) || d.getFullYear() <= 0) return null;
                        return d;
                    }
                    const startStr = row.start_date || null;
                    const endStr   = row.end_date   || null;
                    const start = safeDate(startStr);
                    const end   = safeDate(endStr);
                    const startLabel = start ? start.toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) : '—';
                    const endLabel   = end   ? end.toLocaleDateString('id-ID',   {day:'2-digit', month:'short', year:'numeric'}) : 'Open-ended';
                    
                    let daysHtml = '';
                    if (end && row.status === 'ACTIVE') {
                        const today = new Date();
                        today.setHours(0,0,0,0);
                        const diffMs = end - today;
                        const days = Math.ceil(diffMs / (1000*60*60*24));
                        if (days < 0) {
                            daysHtml = `<br><span class="badge bg-danger">Expired ${Math.abs(days)}h lalu</span>`;
                        } else if (days <= 30) {
                            daysHtml = `<br><span class="badge bg-warning text-dark">${days}h lagi</span>`;
                        } else if (days <= 90) {
                            daysHtml = `<br><span class="badge bg-info">${days}h lagi</span>`;
                        }
                    }
                    return `<small>${startLabel} – ${endLabel}</small>${daysHtml}`;
                }
            },
            { data: 'total_units', className: 'text-center' },
            { data: 'value', className: 'text-end' },
            { data: 'status' },
            { 
                data: null, 
                orderable: false, 
                className: 'text-center',
                render: function(data, type, row) {
                    return buildActionButtons(row.id, row.status, row.days_until_expiry);
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50], [10, 15, 25, 50]],
        language: {
            processing: '<i class="fas fa-spinner fa-spin fa-2x"></i><br>Loading...',
            emptyTable: 'No contracts found',
            zeroRecords: 'No matching contracts found'
        },
        drawCallback: function() {
            // Re-initialize Bootstrap tooltips safely for action buttons in this table
            const $table = $('#contractsTable');
            
            // Dispose existing tooltip instances to avoid "more than one instance" warning
            $table.find('[title]').tooltip('dispose');

            // Initialize tooltips only within this table
            $table.find('[title]').tooltip({
                container: 'body',
                trigger: 'hover'
            });
        }
    });
}

// Apply filters
function applyFilters() {
    if (currentViewMode === 'flat') {
        contractsTable.ajax.reload();
    } else {
        // Reload grouped view with current filters
        groupedData = null;
        loadGroupedView(true);
    }
}

// Refresh table
function refreshTable() {
    contractsTable.ajax.reload();
    loadStatistics();
}

// Export contracts
function exportContracts() {
    const rentalType = $('#filter_rental_type').val();
    const status = $('#filter_status').val();
    const customerId = $('#filter_customer').val();
    
    let url = '<?= base_url('marketing/kontrak/export') ?>?';
    if (rentalType) url += 'rental_type=' + rentalType + '&';
    if (status) url += 'status=' + status + '&';
    if (customerId) url += 'customer_id=' + customerId;
    
    window.location.href = url;
}

// Edit contract — navigate to dedicated edit page
function editContract(id) {
    window.location.href = '<?= base_url('marketing/kontrak/edit') ?>/' + id;
}

/**
 * Shared action button builder — used by BOTH flat DataTable and grouped view.
 * Now shows only View Detail button - all actions moved to modal.
 * @param {number} id          Contract ID
 * @param {string} status      Contract status (ACTIVE, EXPIRED, PENDING, CANCELLED)
 * @param {number|null} days   Days remaining (positive = future, negative = past, null = open-ended)
 */
function buildActionButtons(id, status, days) {
    const canRenew = (status === 'ACTIVE' || status === 'EXPIRED');
    const canAmend = (status === 'ACTIVE');
    
    let renewItem = canRenew ? `<li><a class="dropdown-item" href="#" onclick="event.preventDefault(); openRenewalWizard(${id})"><i class="fas fa-sync-alt text-success me-2"></i>Renewal</a></li>` : '';
    let amendItem = canAmend ? `<li><a class="dropdown-item" href="#" onclick="event.preventDefault(); openAmendmentModal(${id})"><i class="fas fa-calculator text-warning me-2"></i>Change Rate</a></li>` : '';
    let divider = (canRenew || canAmend) ? '<li><hr class="dropdown-divider"></li>' : '';

    return `
        <div class="btn-group">
            <a href="<?= base_url('marketing/kontrak/detail') ?>/${id}" class="btn btn-sm btn-outline-primary" title="View Detail">
                <i class="fas fa-eye"></i>
            </a>
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="<?= base_url('marketing/kontrak/detail') ?>/${id}"><i class="fas fa-eye text-primary me-2"></i>View Detail</a></li>
                <li><a class="dropdown-item" href="<?= base_url('marketing/kontrak/edit') ?>/${id}"><i class="fas fa-edit text-info me-2"></i>Edit Kontrak</a></li>
                ${renewItem}
                ${amendItem}
                ${divider}
                <li><a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); deleteContract(${id})"><i class="fas fa-trash me-2"></i>Hapus Kontrak</a></li>
            </ul>
        </div>`;
}

// View contract units
function viewContractUnits(id) {
    // Open modal with contract units
    $.ajax({
        url: '<?= base_url('marketing/kontrak/units') ?>/' + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                // Display in modal (implement modal UI)
                alert('Contract Units: ' + response.count + ' units');
            }
        }
    });
}

// Delete contract
async function deleteContract(id) {
    const confirmed = await confirmSwal({
        title: 'Hapus Kontrak',
        text: 'Apakah Anda yakin ingin menghapus kontrak ini? Tindakan ini tidak dapat dibatalkan.',
        type: 'delete'
    });
    if (!confirmed) return;
    $.ajax({
        url: '<?= base_url('marketing/kontrak/delete') ?>/' + id,
        type: 'POST',
        data: { <?= csrf_token() ?>: '<?= csrf_hash() ?>' },
        success: function(response) {
            if (response.success) {
                showNotification('Contract deleted successfully', 'success');
                refreshTable();
            } else {
                showNotification(response.message || 'Failed to delete contract', 'error');
            }
        },
        error: function() {
            showNotification('Error deleting contract', 'error');
        }
    });
}

// Show notification
function showNotification(message, type = 'info') {
    // Use your notification system here
    alert(message);
}

// ============================================================================
// SPRINT 1-3: BILLING ENHANCEMENT FUNCTIONS
// ============================================================================

/**
 * Sprint 1: Open Renewal Wizard Modal
 * Shows 5-step wizard for contract renewal
 */
function openRenewalWizard(contractId) {
    // Load contract data
    $.ajax({
        url: '<?= base_url('marketing/kontrak/get') ?>/' + contractId,
        type: 'GET',
        success: function(response) {
            if (response.success && response.data) {
                const contract = response.data;
                
                // Populate renewal wizard with contract data
                $('#renewalParentContractId').val(contractId);
                $('#renewalOldContractNumber').text(contract.no_kontrak);
                $('#renewalCustomerName').text(contract.customer_name || 'N/A');
                $('#renewalOldStartDate').text(contract.tanggal_mulai || 'N/A');
                $('#renewalOldEndDate').text(contract.tanggal_berakhir || 'N/A');
                
                // Calculate suggested dates (gap-free)
                if (contract.tanggal_berakhir) {
                    const oldEndDate = new Date(contract.tanggal_berakhir);
                    const newStartDate = new Date(oldEndDate);
                    newStartDate.setDate(newStartDate.getDate() + 1);
                    
                    const newEndDate = new Date(newStartDate);
                    newEndDate.setFullYear(newEndDate.getFullYear() + 1);
                    newEndDate.setDate(newEndDate.getDate() - 1);
                    
                    $('#renewalStartDate').val(newStartDate.toISOString().split('T')[0]);
                    $('#renewalEndDate').val(newEndDate.toISOString().split('T')[0]);
                }
                
                // Show modal
                $('#renewalWizardModal').modal('show');
            } else {
                showNotification('Failed to load contract data', 'error');
            }
        },
        error: function() {
            showNotification('Error loading contract data', 'error');
        }
    });
}

/**
 * Sprint 3: Open Amendment/Prorate Modal
 * Shows rate change calculator with prorate split
 */
function openAmendmentModal(contractId) {
    // Populate contract dropdown and select this contract
    $.ajax({
        url: '<?= base_url('marketing/kontrak/get-active-contracts') ?>',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const $select = $('#prorateContractId');
                $select.empty().append('<option value="">-- Select active contract --</option>');
                
                response.data.forEach(contract => {
                    $select.append(new Option(contract.no_kontrak + ' - ' + contract.customer_name, contract.id));
                });
                
                // Select current contract
                $select.val(contractId).trigger('change');
                
                // Show modal
                $('#addendumProrateModal').modal('show');
            }
        },
        error: function() {
            showNotification('Error loading contracts', 'error');
        }
    });
}

/**
 * Sprint 3: Open Asset History Modal
 * Shows complete contract timeline with amendments and renewals
 */
function openHistoryModal(contractId) {
    // Show modal immediately
    $('#assetHistoryModal').modal('show');
    
    // Load contract history
    $.ajax({
        url: '<?= base_url('marketing/kontrak/getContractHistory') ?>/' + contractId,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                // Render timeline (component has its own rendering logic)
                renderContractTimeline(response.data);
            } else {
                $('#contractTimelineContent').html(
                    '<div class="alert alert-warning">' +
                    '<i class="fas fa-exclamation-triangle me-2"></i>' +
                    'No history data available for this contract.' +
                    '</div>'
                );
            }
        },
        error: function() {
            $('#contractTimelineContent').html(
                '<div class="alert alert-danger">' +
                '<i class="fas fa-times me-2"></i>' +
                'Error loading contract history.' +
                '</div>'
            );
        }
    });
    
    // Load rate history
    $.ajax({
        url: '<?= base_url('marketing/kontrak/getRateHistory') ?>/' + contractId,
        type: 'GET',
        success: function(response) {
            if (response.success && response.data) {
                renderRateHistory(response.data);
            }
        }
    });
}

/**
 * Render contract timeline in history modal
 */
function renderContractTimeline(events) {
    if (!events || events.length === 0) {
        $('#contractTimelineContent').html('<p class="text-muted">No events found</p>');
        return;
    }
    
    let html = '<div class="timeline">';
    
    events.forEach(event => {
        const iconMap = {
            'contract': 'fa-file-contract text-primary',
            'amendment': 'fa-edit text-warning',
            'renewal': 'fa-sync-alt text-success'
        };
        
        const icon = iconMap[event.type] || 'fa-circle text-secondary';
        
        html += '<div class="timeline-item">' +
                '<div class="timeline-marker"><i class="fas ' + icon + '"></i></div>' +
                '<div class="timeline-content">' +
                '<div class="timeline-time">' + event.date + '</div>' +
                '<h6>' + event.description + '</h6>';
        
        if (event.reason) {
            html += '<p class="text-muted mb-0">' + event.reason + '</p>';
        }
        
        if (event.total_value) {
            html += '<p class="mb-0"><strong>Value:</strong> Rp ' + 
                    parseFloat(event.total_value).toLocaleString('id-ID') + '</p>';
        }
        
        html += '</div></div>';
    });
    
    html += '</div>';
    
    $('#contractTimelineContent').html(html);
}

/**
 * Render rate history chart
 */
function renderRateHistory(rates) {
    if (!rates || rates.length === 0) {
        $('#rateHistoryContent').html('<p class="text-muted">No rate changes found</p>');
        return;
    }
    
    let html = '<div class="table-responsive">' +
               '<table class="table table-sm">' +
               '<thead><tr>' +
               '<th>Date</th><th>Unit</th><th>Old Rate</th><th>New Rate</th><th>Change</th>' +
               '</tr></thead><tbody>';
    
    rates.forEach(rate => {
        const change = rate.new_rate - rate.old_rate;
        const changeClass = change > 0 ? 'text-success' : 'text-danger';
        const changeIcon = change > 0 ? 'fa-arrow-up' : 'fa-arrow-down';
        
        html += '<tr>' +
                '<td>' + rate.date + '</td>' +
                '<td>' + rate.unit_no + '</td>' +
                '<td>Rp ' + parseFloat(rate.old_rate).toLocaleString('id-ID') + '</td>' +
                '<td>Rp ' + parseFloat(rate.new_rate).toLocaleString('id-ID') + '</td>' +
                '<td class="' + changeClass + '">' +
                '<i class="fas ' + changeIcon + ' me-1"></i>' +
                'Rp ' + Math.abs(change).toLocaleString('id-ID') +
                '</td></tr>';
    });
    
    html += '</tbody></table></div>';
    
    $('#rateHistoryContent').html(html);
}

// ============================================================================
// CONTRACT DETAIL MODAL HELPER FUNCTIONS
// ============================================================================

let currentContractId = null;
let currentContractStatus = null;

/**
 * Update modal action buttons visibility based on contract status
 */
function updateModalActionButtons(status) {
    const canRenew = (status === 'ACTIVE' || status === 'EXPIRED');
    const canAmend = (status === 'ACTIVE');
    
    // Show/hide Renewal button
    if (canRenew) {
        $('#btnRenewal').show();
    } else {
        $('#btnRenewal').hide();
    }
    
    // Show/hide Change Rate button
    if (canAmend) {
        $('#btnAmendment').show();
    } else {
        $('#btnAmendment').hide();
    }
}

/**
 * Open Renewal Wizard from Modal
 */
function openRenewalFromModal() {
    if (!currentContractId) return;
    console.log('🔄 Opening renewal wizard for contract ID:', currentContractId);
    $('#contractDetailModal').modal('hide');
    setTimeout(function() {
        openRenewalWizard(currentContractId);
    }, 300);
}

/**
 * Open Amendment Modal from Modal
 */
function openAmendmentFromModal() {
    if (!currentContractId) return;
    console.log('🧮 Opening amendment modal for contract ID:', currentContractId);
    $('#contractDetailModal').modal('hide');
    setTimeout(function() {
        openAmendmentModal(currentContractId);
    }, 300);
}

/**
 * Called from the modal footer "Edit Contract" button.
 * For now opens a simple prompt; can be upgraded to an inline edit tab.
 */

function viewContractDetail(contractId) {
    currentContractId = contractId;
    
    console.log('🔍 Opening contract detail modal for ID:', contractId);
    
    // Reset modal to Overview tab
    $('#overview-tab').tab('show');
    
    // Show modal
    $('#contractDetailModal').modal('show');
    
    // Load overview data immediately
    loadContractOverview(contractId);
    
    // Listen for tab changes to lazy-load data
    $('#units-tab').off('shown.bs.tab').on('shown.bs.tab', function() {
        loadContractUnits(contractId);
    });
    
    $('#history-tab').off('shown.bs.tab').on('shown.bs.tab', function() {
        loadContractHistory(contractId);
    });
    
    $('#documents-tab').off('shown.bs.tab').on('shown.bs.tab', function() {
        loadContractDocuments(contractId);
    });
}

/**
 * Load Contract Overview Tab
 */
function loadContractOverview(contractId) {
    console.log('📄 Loading contract overview...');
    
    $.ajax({
        url: '<?= base_url('marketing/kontrak/get') ?>/' + contractId,
        type: 'GET',
        success: function(response) {
            if (response.success && response.data) {
                const contract = response.data;
                
                // Update modal subtitle
                $('#contractModalSubtitle').text(contract.no_kontrak + ' - ' + (contract.customer_name || 'N/A'));
                
                // Render Contract Information
                let contractHtml = '<div class="row">';
                contractHtml += '<div class="col-md-4 mb-3">';
                contractHtml += '<label class="text-muted small">Contract Number</label>';
                contractHtml += '<p class="fw-bold">' + (contract.no_kontrak || 'N/A') + '</p>';
                contractHtml += '</div>';
                
                contractHtml += '<div class="col-md-4 mb-3">';
                contractHtml += '<label class="text-muted small">Contract Type</label>';
                contractHtml += '<p>' + (contract.rental_type || 'N/A') + '</p>';
                contractHtml += '</div>';
                
                contractHtml += '<div class="col-md-4 mb-3">';
                contractHtml += '<label class="text-muted small">Status</label>';
                let statusClass = contract.status === 'ACTIVE' ? 'success' : (contract.status === 'EXPIRED' ? 'danger' : 'warning');
                contractHtml += '<p><span class="badge bg-' + statusClass + '">' + (contract.status || 'N/A') + '</span></p>';
                contractHtml += '</div>';
                
                contractHtml += '<div class="col-md-4 mb-3">';
                contractHtml += '<label class="text-muted small">PO Number</label>';
                contractHtml += '<p>' + (contract.po_number || 'N/A') + '</p>';
                contractHtml += '</div>';
                
                contractHtml += '<div class="col-md-4 mb-3">';
                contractHtml += '<label class="text-muted small">Start Date</label>';
                contractHtml += '<p>' + (contract.tanggal_mulai || 'N/A') + '</p>';
                contractHtml += '</div>';
                
                contractHtml += '<div class="col-md-4 mb-3">';
                contractHtml += '<label class="text-muted small">End Date</label>';
                contractHtml += '<p>' + (contract.tanggal_berakhir || 'N/A') + '</p>';
                contractHtml += '</div>';
                
                contractHtml += '<div class="col-md-6 mb-3">';
                contractHtml += '<label class="text-muted small">Billing Type</label>';
                contractHtml += '<p>' + (contract.jenis_sewa || 'N/A') + '</p>';
                contractHtml += '</div>';
                
                contractHtml += '<div class="col-md-6 mb-3">';
                contractHtml += '<label class="text-muted small">Billing Method</label>';
                contractHtml += '<p>' + (contract.billing_method || 'N/A') + '</p>';
                contractHtml += '</div>';
                
                contractHtml += '</div>';
                
                $('#contractInfoContent').html(contractHtml);
                
                // Render Customer Information
                let customerHtml = '<p class="mb-2"><strong>Customer Name:</strong><br>' + (contract.customer_name || 'N/A') + '</p>';
                customerHtml += '<p class="mb-2"><strong>Customer Code:</strong><br>' + (contract.customer_code || 'N/A') + '</p>';
                customerHtml += '<p class="mb-0"><strong>Contact:</strong><br>' + (contract.contact_person || 'N/A');
                if (contract.phone) {
                    customerHtml += '<br><i class="fas fa-phone me-1"></i>' + contract.phone;
                }
                customerHtml += '</p>';
                
                $('#customerInfoContent').html(customerHtml);
                
                // Render Financial Summary
                let financialHtml = '<div class="row text-center">';
                financialHtml += '<div class="col-md-6 mb-3">';
                financialHtml += '<label class="text-muted small d-block">Total Units</label>';
                financialHtml += '<h4 class="mb-0 text-primary">' + (contract.total_units || 0) + '</h4>';
                financialHtml += '</div>';
                
                financialHtml += '<div class="col-md-6 mb-3">';
                financialHtml += '<label class="text-muted small d-block">Contract Value</label>';
                const value = contract.total_value || 0;
                financialHtml += '<h4 class="mb-0 text-success">Rp ' + parseFloat(value).toLocaleString('id-ID') + '</h4>';
                financialHtml += '</div>';
                
                if (contract.operator_quantity && contract.operator_quantity > 0) {
                    financialHtml += '<div class="col-12">';
                    financialHtml += '<hr class="my-2">';
                    financialHtml += '<p class="text-muted mb-0"><i class="fas fa-user-tie me-1"></i>Includes ' + contract.operator_quantity + ' operator(s)</p>';
                    if (contract.operator_monthly_rate) {
                        financialHtml += '<small class="text-muted">@ Rp ' + parseFloat(contract.operator_monthly_rate).toLocaleString('id-ID') + '/month</small>';
                    }
                    financialHtml += '</div>';
                }
                
                financialHtml += '</div>';
                
                $('#financialSummaryContent').html(financialHtml);
                
                // Store contract status for action buttons
                currentContractStatus = contract.status;
                
                // Update action buttons visibility based on status
                updateModalActionButtons(contract.status);
                
                console.log('✅ Contract overview loaded successfully');
            } else {
                $('#contractInfoContent').html('<div class="alert alert-warning">Contract details not found</div>');
            }
        },
        error: function() {
            $('#contractInfoContent').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error loading contract details</div>');
            console.error('❌ Failed to load contract overview');
        }
    });
}

/**
 * Load Contract Units & Locations Tab
 */
function loadContractUnits(contractId) {
    console.log('🚚 Loading contract units...');
    
    $('#locationsAccordion').html('<div class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin fa-2x mb-2"></i><p>Loading units...</p></div>');
    
    $.ajax({
        url: '<?= base_url('marketing/kontrak/units') ?>/' + contractId,
        type: 'GET',
        success: function(response) {
            if (response.success && response.data && response.data.length > 0) {
                const locations = {};
                let totalUnits = 0;
                
                // Group units by location
                response.data.forEach(unit => {
                    const locationName = unit.location_name || 'Unknown Location';
                    if (!locations[locationName]) {
                        locations[locationName] = [];
                    }
                    locations[locationName].push(unit);
                    totalUnits++;
                });
                
                // Update total count
                $('#totalUnitsCount').text(totalUnits + ' Unit' + (totalUnits !== 1 ? 's' : ''));
                
                // Render accordion
                let html = '';
                let locationIndex = 0;
                
                for (const [locationName, units] of Object.entries(locations)) {
                    const accordionId = 'location-' + locationIndex;
                    const isFirst = locationIndex === 0;
                    
                    html += '<div class="accordion-item">';
                    html += '<h2 class="accordion-header">';
                    html += '<button class="accordion-button' + (isFirst ? '' : ' collapsed') + '" type="button" data-bs-toggle="collapse" data-bs-target="#' + accordionId + '">';
                    html += '<i class="fas fa-map-marker-alt me-2 text-primary"></i>';
                    html += '<strong>' + locationName + '</strong>';
                    html += '<span class="badge bg-primary ms-2">' + units.length + ' unit(s)</span>';
                    html += '</button></h2>';
                    
                    html += '<div id="' + accordionId + '" class="accordion-collapse collapse' + (isFirst ? ' show' : '') + '">';
                    html += '<div class="accordion-body p-0">';
                    html += '<div class="table-responsive">';
                    html += '<table class="table table-sm table-hover mb-0">';
                    html += '<thead class="bg-light"><tr>';
                    html += '<th>Unit No</th><th>Type</th><th>Brand/Model</th><th>Capacity</th><th>Rate/Month</th><th>Status</th>';
                    html += '</tr></thead><tbody>';
                    
                    units.forEach(unit => {
                        html += '<tr>';
                        html += '<td><strong>' + (unit.unit_no || 'N/A') + '</strong></td>';
                        html += '<td>' + (unit.unit_type || 'N/A') + '</td>';
                        html += '<td>' + (unit.brand_model || 'N/A') + '</td>';
                        html += '<td>' + (unit.capacity || 'N/A') + '</td>';
                        html += '<td class="text-end">Rp ' + (unit.rate_monthly ? parseFloat(unit.rate_monthly).toLocaleString('id-ID') : '0') + '</td>';
                        html += '<td><span class="badge bg-success">Active</span></td>';
                        html += '</tr>';
                    });
                    
                    html += '</tbody></table>';
                    html += '</div></div></div></div>';
                    
                    locationIndex++;
                }
                
                $('#locationsAccordion').html(html);
                console.log('✅ Contract units loaded:', totalUnits);
            } else {
                $('#locationsAccordion').html('<div class="alert alert-info m-3"><i class="fas fa-info-circle me-2"></i>No units found for this contract</div>');
                $('#totalUnitsCount').text('0 Units');
            }
        },
        error: function() {
            $('#locationsAccordion').html('<div class="alert alert-danger m-3"><i class="fas fa-exclamation-triangle me-2"></i>Error loading units</div>');
            console.error('❌ Failed to load contract units');
        }
    });
}

/**
 * Load Contract History Tab
 */
function loadContractHistory(contractId) {
    console.log('📜 Loading contract history...');
    
    // Load timeline
    $.ajax({
        url: '<?= base_url('marketing/kontrak/getContractHistory') ?>/' + contractId,
        type: 'GET',
        success: function(response) {
            if (response.success && response.data) {
                renderContractTimeline(response.data);
                console.log('✅ Contract history loaded');
            } else {
                $('#contractTimelineContent').html('<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No history data available</div>');
            }
        },
        error: function() {
            $('#contractTimelineContent').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error loading history</div>');
        }
    });
    
    // Load rate history
    $.ajax({
        url: '<?= base_url('marketing/kontrak/getRateHistory') ?>/' + contractId,
        type: 'GET',
        success: function(response) {
            if (response.success && response.data) {
                renderRateHistory(response.data);
            } else {
                $('#rateHistoryContent').html('<p class="text-muted">No rate changes found</p>');
            }
        },
        error: function() {
            $('#rateHistoryContent').html('<div class="alert alert-danger">Error loading rate history</div>');
        }
    });
}

/**
 * Load Contract Documents Tab
 */
function loadContractDocuments(contractId) {
    console.log('📎 Loading contract documents...');
    
    $.ajax({
        url: '<?= base_url('marketing/kontrak/documents') ?>/' + contractId,
        type: 'GET',
        success: function(response) {
            if (response.success && response.data && response.data.length > 0) {
                let html = '<div class="list-group list-group-flush">';
                
                response.data.forEach(doc => {
                    const iconMap = {
                        'pdf': 'fa-file-pdf text-danger',
                        'doc': 'fa-file-word text-primary',
                        'docx': 'fa-file-word text-primary',
                        'xls': 'fa-file-excel text-success',
                        'xlsx': 'fa-file-excel text-success',
                        'jpg': 'fa-file-image text-info',
                        'jpeg': 'fa-file-image text-info',
                        'png': 'fa-file-image text-info'
                    };
                    
                    const ext = doc.file_name.split('.').pop().toLowerCase();
                    const icon = iconMap[ext] || 'fa-file text-secondary';
                    
                    html += '<div class="list-group-item d-flex justify-content-between align-items-center">';
                    html += '<div>';
                    html += '<i class="fas ' + icon + ' fa-2x me-3"></i>';
                    html += '<strong>' + doc.file_name + '</strong>';
                    html += '<br><small class="text-muted">Uploaded: ' + doc.uploaded_at + ' by ' + (doc.uploaded_by || 'System') + '</small>';
                    html += '</div>';
                    html += '<div>';
                    html += '<a href="' + doc.file_path + '" class="btn btn-sm btn-primary me-1" download><i class="fas fa-download"></i></a>';
                    html += '<button class="btn btn-sm btn-danger" onclick="deleteDocument(' + doc.id + ')"><i class="fas fa-trash"></i></button>';
                    html += '</div>';
                    html += '</div>';
                });
                
                html += '</div>';
                
                $('#documentsListContent').html(html);
                console.log('✅ Documents loaded:', response.data.length);
            } else {
                $('#documentsListContent').html('<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No documents uploaded yet</div>');
            }
        },
        error: function() {
            $('#documentsListContent').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error loading documents</div>');
            console.error('❌ Failed to load documents');
        }
    });
}

/**
 * Edit Contract from Modal
 */
function editContractFromModal() {
    if (currentContractId) {
        console.log('✏️ Editing contract ID:', currentContractId);
        $('#contractDetailModal').modal('hide');
        // Redirect to edit page
        setTimeout(function() {
            window.location.href = '<?= base_url('marketing/kontrak/edit') ?>/' + currentContractId;
        }, 300);
    } else {
        console.error('❌ No contract ID selected');
        alert('Error: No contract selected');
    }
}

/**
 * Delete Contract from Modal
 */
function deleteContractFromModal() {
    if (!currentContractId) return;
    
    Swal.fire({
        title: 'Delete Contract?',
        text: "This action cannot be undone. All related data will be removed.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('marketing/kontrak/delete') ?>/' + currentContractId,
                type: 'POST',
                data: { 
                    csrf_test_name: window.csrfToken || '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        $('#contractDetailModal').modal('hide');
                        Swal.fire('Deleted!', 'Contract has been deleted.', 'success');
                        refreshTable();
                    } else {
                        Swal.fire('Error', response.message || 'Failed to delete contract', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error deleting contract', 'error');
                }
            });
        }
    });
}

/**
 * Upload Contract Document
 */
function uploadContractDocument() {
    // Implement file upload UI (you can use a separate modal or inline form)
    alert('Upload document functionality - to be implemented with file upload form');
}

/**
 * Delete Document
 */
function deleteDocument(docId) {
    if (confirm('Delete this document?')) {
        $.ajax({
            url: '<?= base_url('marketing/kontrak/deleteDocument') ?>/' + docId,
            type: 'POST',
            data: { 
                csrf_test_name: window.csrfToken || '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    loadContractDocuments(currentContractId);
                    showNotification('Document deleted', 'success');
                }
            }
        });
    }
}

// =========================================
// DIRECT CONTRACT CREATION FUNCTIONS
// =========================================

/**
 * Open direct contract creation modal
 */
function openAddContractModal() {
    // Reset form
    $('#addContractForm')[0].reset();
    
    // Load customers for dropdown
    loadCustomersForContract();
    
    // Show modal
    $('#addContractModal').modal('show');
}

/**
 * Load customers for contract creation dropdown
 */
function loadCustomersForContract() {
    $.ajax({
        url: '<?= base_url('marketing/customer-management/getCustomersForSelect') ?>',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const customerSelect = $('#contractCustomerSelect');
                customerSelect.empty().append('<option value="">-- Select Customer --</option>');
                response.data.forEach(customer => {
                    customerSelect.append(`<option value="${customer.id}">${customer.company_name} (${customer.customer_code})</option>`);
                });
            }
        },
        error: function() {
            showNotification('Error loading customers', 'error');
        }
    });
}

/**
 * Customer change event - load locations
 */
$(document).on('change', '#contractCustomerSelect', function() {
    const customerId = $(this).val();
    const locationSelect = $('#contractLocationSelect');
    
    if (customerId) {
        $.ajax({
            url: `<?= base_url('marketing/customer-management/getCustomerLocations/') ?>${customerId}`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    locationSelect.empty().append('<option value="">-- Select Location --</option>');
                    response.data.forEach(location => {
                        locationSelect.append(`<option value="${location.id}">${location.location_name}</option>`);
                    });
                    locationSelect.prop('disabled', false);
                }
            },
            error: function() {
                showNotification('Error loading locations', 'error');
            }
        });
    } else {
        locationSelect.empty().append('<option value="">-- Select Customer First --</option>').prop('disabled', true);
    }
});

/**
 * Generate contract number
 */
$(document).on('click', '#generateContractNumber', function() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const random = String(Math.floor(Math.random() * 1000)).padStart(3, '0');
    const contractNumber = `CTR-${year}${month}${day}-${random}`;
    $('input[name="contract_number"]').val(contractNumber);
});

/**
 * Handle contract form submission
 */
$(document).on('submit', '#addContractForm', function(e) {
    e.preventDefault();
    
    OptimaPro.showLoading('Creating contract...');
    
    $.ajax({
        url: '<?= base_url('marketing/kontrak/store') ?>',
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            OptimaPro.hideLoading();
            
            if (response.success) {
                showNotification(response.message || 'Contract created successfully', 'success');
                $('#addContractModal').modal('hide');
                $('#addContractForm')[0].reset();
                
                // Reload contract table
                if (contractsTable) {
                    contractsTable.ajax.reload();
                }
                
                // Open detail modal for new contract
                if (response.contract_id) {
                    setTimeout(() => {
                        viewContractDetail(response.contract_id);
                    }, 500);
                }
            } else {
                if (response.errors) {
                    // Display validation errors
                    let errorMsg = '<ul class="mb-0">';
                    for (let field in response.errors) {
                        errorMsg += `<li>${response.errors[field]}</li>`;
                    }
                    errorMsg += '</ul>';
                    showNotification('Validation errors: ' + errorMsg, 'error');
                } else {
                    showNotification(response.message || 'Failed to create contract', 'error');
                }
            }
        },
        error: function(xhr) {
            OptimaPro.hideLoading();
            let errorMsg = 'System error occurred';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            showNotification(errorMsg, 'error');
        }
    });
});

</script>

<style>
/* ──── Grouped View Styles ──── */
.gv-customer-header {
    cursor: pointer;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transition: background 0.2s;
    user-select: none;
}
.gv-customer-header:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
}
.gv-customer-header.collapsed .gv-caret { transform: rotate(-90deg); }
.gv-caret { transition: transform 0.2s ease; display: inline-block; }
.gv-child-table thead th { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.03em; }
.gv-child-table td { vertical-align: middle; font-size: 0.875rem; }
.gv-customer-block { border: 1px solid #dee2e6; border-radius: 8px; margin-bottom: 10px; }
.gv-child-wrap { transition: none; }
#groupedViewBody .table-responsive { overflow: visible; }
#groupedViewBody .dropdown-menu { z-index: 9050; }
.gv-summary-badges .badge { font-size: 0.72rem; }
.gv-search-wrap { padding: 12px 16px; background: #f1f3f5; border-bottom: 1px solid #dee2e6; }

/* ──── Action Button Styling ──── */
.btn-sm {
    padding: 0.35rem 0.75rem;
    font-size: 0.875rem;
}

/* Modal footer button group */
#contractActionButtons .btn {
    min-width: 95px;
}
</style>

<script>
// ──── View Mode State ────────────────────────────────────────────────
let currentViewMode = 'flat';
let groupedData     = null; // cache

function switchViewMode(mode) {
    currentViewMode = mode;
    const flatBtn     = document.getElementById('btnFlatView');
    const groupedBtn  = document.getElementById('btnGroupedView');
    const flatBody    = document.getElementById('flatViewBody');
    const groupedBody = document.getElementById('groupedViewBody');

    if (mode === 'flat') {
        flatBtn.classList.add('active', 'btn-secondary');
        flatBtn.classList.remove('btn-outline-secondary');
        groupedBtn.classList.remove('active', 'btn-secondary');
        groupedBtn.classList.add('btn-outline-secondary');
        flatBody.style.display    = '';
        groupedBody.style.display = 'none';
    } else {
        groupedBtn.classList.add('active', 'btn-secondary');
        groupedBtn.classList.remove('btn-outline-secondary');
        flatBtn.classList.remove('active', 'btn-secondary');
        flatBtn.classList.add('btn-outline-secondary');
        flatBody.style.display    = 'none';
        groupedBody.style.display = '';
        loadGroupedView(false);
    }
}

function refreshView() {
    if (currentViewMode === 'flat') {
        if (contractsTable) contractsTable.ajax.reload(null, false);
    } else {
        groupedData = null;
        loadGroupedView(true);
    }
}

function loadGroupedView(forceReload) {
    if (groupedData && !forceReload) {
        renderGroupedView(groupedData);
        return;
    }

    const body = document.getElementById('groupedViewBody');
    body.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status"></div>
            <p class="text-muted">Memuat data grouped...</p>
        </div>`;

    // Pass current filters — use underscore IDs matching filter form HTML
    const rentalType = $('#filter_rental_type').val() || '';
    const status     = $('#filter_status').val()     || '';
    const customerId = $('#filter_customer').val()   || '';

    const params = new URLSearchParams();
    if (rentalType) params.append('rental_type', rentalType);
    if (status)     params.append('status', status);
    if (customerId) params.append('customer_id', customerId);

    fetch(`<?= base_url('marketing/kontrak/getGrouped') ?>?${params.toString()}`)
        .then(r => r.json())
        .then(resp => {
            if (!resp.success) throw new Error(resp.message || 'Gagal memuat data');
            groupedData = resp.data;
            renderGroupedView(groupedData);
        })
        .catch(err => {
            body.innerHTML = `<div class="alert alert-danger m-3"><i class="fas fa-exclamation-circle me-2"></i>${err.message}</div>`;
        });
}

function renderGroupedView(customers) {
    const body = document.getElementById('groupedViewBody');
    if (!customers || customers.length === 0) {
        body.innerHTML = `<div class="text-center py-5 text-muted"><i class="fas fa-inbox fa-3x mb-3"></i><p>Tidak ada data kontrak</p></div>`;
        return;
    }

    // Search input bar
    let html = `
        <div class="gv-search-wrap d-flex align-items-center gap-2">
            <i class="fas fa-search text-muted"></i>
            <input type="text" id="gvSearch" class="form-control form-control-sm" placeholder="Cari nama customer atau nomor kontrak..." oninput="filterGroupedView(this.value)">
            <span class="text-muted text-nowrap small" id="gvCount">${customers.length} customer</span>
        </div>
        <div class="p-3" id="gvAccordion">`;

    customers.forEach((cust, ci) => {
        const expanded = ci < 3; // auto-open first 3
        const monthlyFmt = cust.monthly_value > 0
            ? 'Rp ' + Number(cust.monthly_value).toLocaleString('id-ID')
            : '—';

        html += `
        <div class="gv-customer-block" data-customer="${escHtml(cust.customer_name)}" id="gvBlock${ci}">
            <!-- Customer Header Row -->
            <div class="gv-customer-header p-3 d-flex align-items-center gap-3 ${expanded ? '' : 'collapsed'}"
                 onclick="toggleCustomerBlock(${ci})">
                <span class="gv-caret text-muted" style="${expanded ? '' : 'transform:rotate(-90deg)'}">
                    <i class="fas fa-chevron-down"></i>
                </span>
                <div class="flex-grow-1">
                    <strong class="text-dark">${escHtml(cust.customer_name)}</strong>
                </div>
                <div class="gv-summary-badges d-flex gap-2 flex-wrap">
                    <span class="badge bg-primary">${cust.total_contracts} kontrak</span>
                    <span class="badge bg-info text-dark">${cust.total_units} unit</span>
                    ${cust.monthly_value > 0 ? `<span class="badge bg-success">${monthlyFmt}/bln</span>` : ''}
                </div>
            </div>
            <!-- Contract Sub-table -->
            <div class="gv-child-wrap" id="gvChild${ci}" style="${expanded ? '' : 'display:none'}">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0 gv-child-table">
                        <thead class="bg-light">
                            <tr>
                                <th style="width:30%">No. Kontrak / PO</th>
                                <th>Tipe</th>
                                <th>Billing</th>
                                <th>Periode &amp; Sisa Hari</th>
                                <th class="text-center">Unit</th>
                                <th class="text-end">Nilai</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${cust.contracts.map(k => buildContractRow(k)).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>`;
    });

    html += `</div>`; // #gvAccordion
    body.innerHTML = html;
    
    // Initialize tooltips for action buttons in grouped view
    setTimeout(() => {
        $('#groupedViewBody [title]').tooltip({
            container: 'body',
            trigger: 'hover'
        });
    }, 100);
}

function buildContractRow(k) {
    // Type badge
    const typeBadge = {
        'CONTRACT':   '<span class="badge bg-primary"><i class="fas fa-file-contract me-1"></i>Contract</span>',
        'PO_ONLY':    '<span class="badge bg-info text-dark"><i class="fas fa-file-invoice me-1"></i>PO Only</span>',
        'DAILY_SPOT': '<span class="badge bg-warning text-dark"><i class="fas fa-calendar-day me-1"></i>Daily</span>',
    }[k.rental_type] || `<span class="badge bg-secondary">${escHtml(k.rental_type||'—')}</span>`;

    // Billing
    const billingMap = { 'BULANAN': 'Monthly', 'HARIAN': 'Daily' };
    const billing = billingMap[k.jenis_sewa?.toUpperCase()] || k.jenis_sewa || '—';

    // Period
    const startLbl = k.start_date ? new Date(k.start_date).toLocaleDateString('id-ID', {day:'2-digit',month:'short',year:'numeric'}) : '—';
    const endLbl   = k.end_date   ? new Date(k.end_date).toLocaleDateString('id-ID',   {day:'2-digit',month:'short',year:'numeric'}) : 'Open-ended';

    let daysBadge = '';
    if (k.days_remaining !== null && k.status === 'ACTIVE') {
        if (k.days_remaining < 0)      daysBadge = `<br><span class="badge bg-danger">Expired ${Math.abs(k.days_remaining)}h lalu</span>`;
        else if (k.days_remaining <= 30) daysBadge = `<br><span class="badge bg-warning text-dark">${k.days_remaining}h lagi</span>`;
        else if (k.days_remaining <= 90) daysBadge = `<br><span class="badge bg-info">${k.days_remaining}h lagi</span>`;
    }

    // Status badge
    const statusColor = { ACTIVE:'success', PENDING:'warning', EXPIRED:'danger', CANCELLED:'secondary' }[k.status] || 'secondary';
    const statusBadge = `<span class="badge bg-${statusColor}">${escHtml(k.status||'—')}</span>`;

    // Contract / PO display
    const kontrakNo = escHtml(k.no_kontrak || '—');
    const poLine = k.po_number ? `<br><small class="text-muted"><i class="fas fa-file-invoice me-1"></i>PO: ${escHtml(k.po_number)}</small>` : '';

    const nilai = k.nilai_total > 0 ? 'Rp ' + Number(k.nilai_total).toLocaleString('id-ID') : '—';

    return `<tr>
        <td><span class="font-monospace small">${kontrakNo}</span>${poLine}</td>
        <td>${typeBadge}</td>
        <td><small>${escHtml(billing)}</small></td>
        <td><small>${startLbl} \u2013 ${endLbl}</small>${daysBadge}</td>
        <td class="text-center">${k.total_units}</td>
        <td class="text-end"><small>${nilai}</small></td>
        <td>${statusBadge}</td>
        <td class="text-center">${buildActionButtons(k.id, k.status, k.days_remaining)}</td>
    </tr>`;
}

function toggleCustomerBlock(ci) {
    const childWrap = document.getElementById(`gvChild${ci}`);
    const header    = childWrap.previousElementSibling;
    const caret     = header.querySelector('.gv-caret');
    const isHidden  = childWrap.style.display === 'none';
    childWrap.style.display = isHidden ? '' : 'none';
    if (isHidden) {
        header.classList.remove('collapsed');
        caret.style.transform = '';
    } else {
        header.classList.add('collapsed');
        caret.style.transform = 'rotate(-90deg)';
    }
}

function filterGroupedView(query) {
    if (!groupedData) return;
    query = query.toLowerCase().trim();
    const blocks = document.querySelectorAll('.gv-customer-block');
    let visible = 0;
    blocks.forEach(block => {
        const customerName = block.getAttribute('data-customer').toLowerCase();
        const text = block.innerText.toLowerCase();
        const match = !query || customerName.includes(query) || text.includes(query);
        block.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    const countEl = document.getElementById('gvCount');
    if (countEl) countEl.textContent = `${visible} customer`;
}

function viewContractBrief(id) {
    // Delegate to existing viewContractDetail if it exists, or openContractDetail
    if (typeof viewContractDetail === 'function') viewContractDetail(id);
    else if (typeof openContractModal === 'function') openContractModal(id);
}

function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
<?= $this->endSection() ?>
