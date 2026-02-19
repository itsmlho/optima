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

<!-- Contracts Table Card -->
<div class="card table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Contract & PO Management</h5>
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
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="refreshTable()">
                <i class="fas fa-sync me-1"></i>Refresh
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0" id="contractsTable">
                <thead class="bg-light">
                    <tr>
                        <th>Contract No</th>
                        <th>Type</th>
                        <th>PO Number</th>
                        <th>Customer</th>
                        <th>Billing</th>
                        <th>Period</th>
                        <th>Units</th>
                        <th>Value</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables will populate -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Sprint 1-3: Billing Enhancement Components -->
<?= $this->include('components/renewal_wizard') ?>
<?= $this->include('components/addendum_prorate') ?>
<?= $this->include('components/asset_history') ?>

<!-- Contract Detail Modal - NEW COMPREHENSIVE VIEW -->
<div class="modal fade modal-wide" id="contractDetailModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-primary">
                <div>
                    <h5 class="modal-title">
                        <i class="fas fa-file-contract me-2"></i><strong>Contract Details</strong>
                    </h5>
                    <small id="contractModalSubtitle">Complete contract information</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs nav-fill mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview-content" type="button" role="tab">
                            <i class="fas fa-info-circle me-1"></i>Overview
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="units-tab" data-bs-toggle="tab" data-bs-target="#units-content" type="button" role="tab">
                            <i class="fas fa-truck me-1"></i>Units & Locations
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-content" type="button" role="tab">
                            <i class="fas fa-history me-1"></i>History
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents-content" type="button" role="tab">
                            <i class="fas fa-file-alt me-1"></i>Documents
                        </button>
                    </li>
                </ul>

                <!-- Tab Contents -->
                <div class="tab-content">
                    <!-- Overview Tab -->
                    <div class="tab-pane fade show active" id="overview-content" role="tabpanel">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-file-contract me-2"></i><strong>Contract Information</strong></h6>
                            </div>
                            <div class="card-body" id="contractInfoContent">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                                    <p>Loading contract details...</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-building me-2"></i><strong>Customer Information</strong></h6>
                                    </div>
                                    <div class="card-body" id="customerInfoContent">
                                        <div class="text-center text-muted py-3">Loading...</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-calculator me-2"></i><strong>Financial Summary</strong></h6>
                                    </div>
                                    <div class="card-body" id="financialSummaryContent">
                                        <div class="text-center text-muted py-3">Loading...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Units & Locations Tab -->
                    <div class="tab-pane fade" id="units-content" role="tabpanel">
                        <div class="card">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-truck me-2"></i><strong>Rented Units by Location</strong></h6>
                                <span class="badge bg-primary" id="totalUnitsCount">0 Units</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="accordion" id="locationsAccordion">
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                                        <p>Loading units...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- History Tab -->
                    <div class="tab-pane fade" id="history-content" role="tabpanel">
                        <div class="card mb-3">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-clock me-2"></i><strong>Contract Timeline</strong></h6>
                                <select class="form-select form-select-sm" style="width: auto;" id="historyFilter">
                                    <option value="all">All Events</option>
                                    <option value="contract">Contracts</option>
                                    <option value="amendment">Amendments</option>
                                    <option value="renewal">Renewals</option>
                                </select>
                            </div>
                            <div class="card-body" id="contractTimelineContent">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                                    <p>Loading history...</p>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i><strong>Rate Changes</strong></h6>
                            </div>
                            <div class="card-body" id="rateHistoryContent">
                                <div class="text-center text-muted py-3">Loading rate history...</div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Tab -->
                    <div class="tab-pane fade" id="documents-content" role="tabpanel">
                        <div class="card">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-paperclip me-2"></i><strong>Contract Documents</strong></h6>
                                <button class="btn btn-sm btn-primary" onclick="uploadContractDocument()">
                                    <i class="fas fa-upload me-1"></i>Upload Document
                                </button>
                            </div>
                            <div class="card-body" id="documentsListContent">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                                    <p>Loading documents...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
                <button type="button" class="btn btn-warning" onclick="editContractFromModal()">
                    <i class="fas fa-edit me-1"></i>Edit Contract
                </button>
                <button type="button" class="btn btn-danger" onclick="deleteContractFromModal()">
                    <i class="fas fa-trash me-1"></i>Delete Contract
                </button>
            </div>
        </div>
    </div>
</div>

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
    
    // Load customers dropdown
    loadCustomersDropdown();
    
    // Initialize DataTable
    initializeContractsTable();
});

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
function loadCustomersDropdown() {
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
            { data: 'contract_number' },
            { data: 'rental_type' },
            { data: 'po' },
            { data: 'client_name' },
            { data: 'jenis_sewa' },
            { data: 'period' },
            { data: 'total_units', className: 'text-center' },
            { data: 'value', className: 'text-end' },
            { data: 'status' },
            { 
                data: null, 
                orderable: false, 
                className: 'text-center',
                render: function(data, type, row) {
                    let buttons = '';
                    
                    // View Detail button - PRIMARY ACTION
                    buttons += '<button class="btn btn-sm btn-info me-1" onclick="viewContractDetail(' + row.id + ')" title="View Details">' +
                               '<i class="fas fa-eye"></i></button>';
                    
                    // Edit button
                    buttons += '<button class="btn btn-sm btn-primary me-1" onclick="editContract(' + row.id + ')" title="Edit Contract">' +
                               '<i class="fas fa-edit"></i></button>';
                    
                    // Renewal button (untuk kontrak yang akan expire dalam 90 hari)
                    if (row.days_until_expiry !== undefined && row.days_until_expiry <= 90 && row.days_until_expiry > 0 && row.status === 'ACTIVE') {
                        buttons += '<button class="btn btn-sm btn-success me-1" onclick="openRenewalWizard(' + row.id + ')" title="Renew Contract">' +
                                   '<i class="fas fa-sync-alt"></i></button>';
                    }
                    
                    // Amendment button (untuk kontrak aktif)
                    if (row.status === 'ACTIVE') {
                        buttons += '<button class="btn btn-sm btn-warning me-1" onclick="openAmendmentModal(' + row.id + ')" title="Change Rate">' +
                                   '<i class="fas fa-calculator"></i></button>';
                    }
                    
                    // Delete button
                    buttons += '<button class="btn btn-sm btn-danger" onclick="deleteContract(' + row.id + ')" title="Delete Contract">' +
                               '<i class="fas fa-trash"></i></button>';
                    
                    return buttons;
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
        }
    });
}

// Apply filters
function applyFilters() {
    contractsTable.ajax.reload();
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

// Edit contract
function editContract(id) {
    window.location.href = '<?= base_url('marketing/kontrak/edit') ?>/' + id;
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
function deleteContract(id) {
    if (confirm('Are you sure you want to delete this contract?')) {
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
        url: '<?= base_url('kontrak/getActiveContracts') ?>',
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
        url: '<?= base_url('kontrak/getContractHistory') ?>/' + contractId,
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
        url: '<?= base_url('kontrak/getRateHistory') ?>/' + contractId,
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
// NEW: CONTRACT DETAIL MODAL FUNCTIONS
// ============================================================================

let currentContractId = null;

/**
 * View Contract Detail - Opens comprehensive detail modal
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
<?= $this->endSection() ?>
