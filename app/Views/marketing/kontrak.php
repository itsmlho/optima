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
                <a href="<?= base_url('marketing/quotations') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Create New
                </a>
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
                    
                    // History button (semua kontrak)
                    buttons += '<button class="btn btn-sm btn-info me-1" onclick="openHistoryModal(' + row.id + ')" title="View History">' +
                               '<i class="fas fa-history"></i></button>';
                    
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
</script>
<?= $this->endSection() ?>
