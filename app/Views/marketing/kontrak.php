<?= $this->extend('layouts/base') ?>

<?php
/**
 * Contract & PO Management Module
 * 
 * BADGE SYSTEM: Menggunakan Optima Badge Standards (optima-pro.css)
 * Direct CSS classes - tidak perlu JavaScript helper function
 * 
 * Quick Reference:
 * Contract Types:
 * - CONTRACT      → <span class="badge badge-soft-blue">Contract</span>
 * - PO_ONLY       → <span class="badge badge-soft-cyan">PO Only</span>  
 * - DAILY_SPOT    → <span class="badge badge-soft-yellow">Daily</span>
 * 
 * Contract Status:
 * - ACTIVE        → <span class="badge badge-soft-green">ACTIVE</span>
 * - PENDING       → <span class="badge badge-soft-yellow">PENDING</span>
 * - EXPIRED       → <span class="badge badge-soft-red">EXPIRED</span>
 * - CANCELLED     → <span class="badge badge-soft-gray">CANCELLED</span>
 * 
 * Expiry Warnings (3-tier urgency):
 * - Expired       → <span class="badge badge-soft-red">Expired X days ago</span>
 * - Critical <30d → <span class="badge badge-soft-orange">X days left</span>
 * - Monitor 31-90d→ <span class="badge badge-soft-cyan">X days left</span>
 * 
 * Info Badges:
 * - Unit counts   → <span class="badge badge-soft-blue">5 units</span>
 * - Total value   → <span class="badge badge-soft-green">Rp 50.000.000</span>
 * 
 * PLANNED ENHANCEMENT:
 * - Filter system will be replaced with tab-based navigation (similar to Customer Management)
 * - Tabs: All Contracts | Active | Expiring Soon | Expired
 * 
 * See optima-pro.css line ~2030 for complete badge standards
 */
helper('simple_rbac');
$can_view = can_view('marketing');
$can_create = (
    (function_exists('canPerformAction') && canPerformAction('marketing', 'kontrak', 'create'))
    || (function_exists('hasPermission') && hasPermission('marketing.kontrak.create'))
    || (function_exists('hasPermission') && hasPermission('marketing.contract.create'))
);
$can_export = (
    (function_exists('hasPermission') && hasPermission('marketing.kontrak.export'))
    || (function_exists('hasPermission') && hasPermission('marketing.contract.export'))
);
?>

<?= $this->section('content') ?>

<!-- Statistics Cards -->
<div class="row mt-3 mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-3">
        <div class="stat-card bg-primary-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-file-contract fa-3x text-primary opacity-75"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-total-contracts">0</div>
                    <div class="text-muted small">Total Rental</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-3">
        <div class="stat-card bg-success-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-check-circle fa-3x text-success opacity-75"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-active">0</div>
                    <div class="text-muted small">Rental Aktif</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-3">
        <div class="stat-card bg-warning-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning opacity-75"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-expiring">0</div>
                    <div class="text-muted small">Expiring Soon (90d)</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-3">
        <div class="stat-card bg-info-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-truck fa-3x text-info opacity-75"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-total-units">0</div>
                    <div class="text-muted small">Total Units Rented</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Filter Tabs -->
<div class="card mb-3">
    <div class="card-body p-0" style="overflow-x: auto;">
        <ul class="nav nav-tabs flex-nowrap" id="contractStatusTabs" role="tablist" style="min-width: max-content;">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-all" data-tab="all" type="button" role="tab">
                    <i class="fas fa-list me-2"></i><?= lang('Marketing.tab_all_rental') ?>
                    <span class="badge badge-soft-blue ms-2" id="count-all">0</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-active" data-tab="active" type="button" role="tab">
                    <i class="fas fa-check-circle me-2"></i>Active
                    <span class="badge badge-soft-green ms-2" id="count-active">0</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-expiring" data-tab="expiring" type="button" role="tab">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= lang('Marketing.tab_expiring_soon') ?>
                    <span class="badge badge-soft-orange ms-2" id="count-expiring">0</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-expired" data-tab="expired" type="button" role="tab">
                    <i class="fas fa-times-circle me-2"></i>Expired
                    <span class="badge badge-soft-red ms-2" id="count-expired">0</span>
                </button>
            </li>
        </ul>
        
    </div>
</div>

<!-- Filter Bar -->
<div class="card mb-3">
    <div class="card-body py-2">
        <div class="row align-items-end g-2">
            <div class="col-md-4">
                <label class="form-label mb-1 small fw-semibold text-muted">
                    <i class="fas fa-building me-1"></i><?= lang('Marketing.customer') ?>
                </label>
                <select id="filterCustomer" class="form-select form-select-sm" style="width:100%;">
                    <option value="">-- Semua Customer --</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1 small fw-semibold text-muted">
                    <i class="fas fa-file-contract me-1"></i><?= lang('Marketing.rental_type') ?>
                </label>
                <select class="form-select form-select-sm" id="filterRentalType">
                    <option value=""><?= lang('Marketing.select_all') ?></option>
                    <option value="CONTRACT"><?= lang('Marketing.rental_type_contract') ?></option>
                    <option value="PO_ONLY"><?= lang('Marketing.rental_type_po') ?></option>
                    <option value="DAILY_SPOT"><?= lang('Marketing.rental_type_harian') ?></option>
                </select>
            </div>
            <div class="col-md-3" id="filterExpiringPeriodWrap" style="display:none;">
                <label class="form-label mb-1 small fw-semibold text-muted">
                    <i class="fas fa-clock me-1"></i><?= lang('Marketing.period_filter') ?>
                </label>
                <select class="form-select form-select-sm" id="filterExpiringPeriod">
                    <option value="30"><?= lang('Marketing.one_month') ?></option>
                    <option value="90" selected><?= lang('Marketing.three_months') ?></option>
                    <option value="180"><?= lang('Marketing.six_months') ?></option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-outline-secondary btn-sm w-100" onclick="resetFilters()">
                    <i class="fas fa-undo me-1"></i>Reset
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Contracts Table Card -->
<div class="card table-card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0">
                <i class="bi bi-file-earmark-text me-2 text-primary"></i>
                <?= lang('Marketing.rental_management') ?>
            </h5>
            <p class="text-muted small mb-0">
                <?= lang('Marketing.rental_list') ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <?php if ($can_create): ?>
                <button type="button" class="btn btn-primary btn-sm" onclick="openAddContractModal()">
                    <i class="fas fa-plus me-1"></i><?= lang('Marketing.add_rental') ?>
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
    <!-- FLAT VIEW body -->
    <div id="flatViewBody" class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0" id="contractsTable">
                <thead class="bg-light">
                    <tr>
                        <th><?= lang('Marketing.th_contract_po') ?></th>
                        <th><?= lang('Marketing.th_type') ?></th>
                        <th><?= lang('Marketing.th_period_days') ?></th>
                        <th><?= lang('Marketing.total_units') ?></th>
                        <th><?= lang('Marketing.th_value') ?></th>
                        <th><?= lang('App.status') ?? 'Status' ?></th>
                        <th><?= lang('Marketing.actions') ?></th>
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

<!-- Contract Detail Modal REMOVED: 
     Konten detail sudah tersedia di dedicated page kontrak_detail.php.
     Tombol "View Detail" di tabel sudah mengarah ke /marketing/rental/detail/{id}.
     Modal ini dihapus untuk menghindari konflik DOM ID (contractInfoContent, dll). -->

<!-- Direct Contract Creation Modal -->
<div class="modal fade modal-wide" id="addContractModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title"><?= lang('Marketing.add_rental') ?></h5>
                    <small class="text-muted"><?= lang('Marketing.additional_notes_optional') ?></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addContractForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3" id="contractNumberSection">
                            <label class="form-label" id="contractNumberLabel"><?= lang('Marketing.rental_number_label') ?> *</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="contract_number" id="modalContractNumber" required>
                                <button class="btn btn-outline-secondary" type="button" id="generateContractNumber" title="<?= lang('Marketing.generate_rental_number') ?>">
                                    <i class="fas fa-magic"></i>
                                </button>
                            </div>
                            <small class="text-muted" id="contractNumberHint"><?= lang('Marketing.select_customer_first') ?></small>
                        </div>
                        
                        <div class="col-md-6 mb-3" id="poNumberSection">
                            <label class="form-label" id="poNumberLabel"><?= lang('Marketing.client_po_number') ?></label>
                            <input type="text" class="form-control" name="po_number" id="modalPoNumber" placeholder="<?= lang('Marketing.client_po_number') ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= lang('Marketing.customer') ?> *</label>
                            <select class="form-select" name="customer_id" id="contractCustomerSelect" required>
                                <option value="">-- <?= lang('Marketing.customer') ?> --</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= lang('Marketing.location') ?> *</label>
                            <select class="form-select" name="customer_location_id" id="contractLocationSelect" required disabled>
                                <option value=""><?= lang('Marketing.select_customer_first') ?></option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= lang('Marketing.rental_type') ?> *</label>
                            <select class="form-select" name="rental_type" id="modalRentalType" required>
                                <option value="CONTRACT" selected><?= lang('Marketing.rental_type_contract') ?></option>
                                <option value="PO_ONLY"><?= lang('Marketing.rental_type_po') ?></option>
                                <option value="DAILY_SPOT"><?= lang('Marketing.rental_type_harian') ?></option>
                            </select>
                            <small class="text-muted" id="rentalTypeDesc"></small>
                        </div>
                        
                        <div class="col-md-6 mb-3" id="billingPeriodSection">
                            <label class="form-label"><?= lang('Marketing.monthly') ?> / <?= lang('Marketing.daily') ?> *</label>
                            <select class="form-select" name="jenis_sewa" id="modalJenisSewa" required>
                                <option value="BULANAN" selected><?= lang('Marketing.monthly') ?></option>
                                <option value="HARIAN"><?= lang('Marketing.daily') ?></option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= lang('Marketing.start_date') ?> *</label>
                            <input type="date" class="form-control" name="start_date" id="modalStartDate" required>
                        </div>

                        <!-- End date: hidden for PO_ONLY -->
                        <div class="col-md-6 mb-3" id="endDateSection">
                            <label class="form-label" id="endDateLabel"><?= lang('Marketing.end_date') ?> *</label>
                            <input type="date" class="form-control" name="end_date" id="modalEndDate">
                            <small class="text-muted" id="endDateHint"><?= lang('Marketing.contract_end_date_required') ?></small>
                        </div>

                        <!-- PO_ONLY: open-ended notice -->
                        <div class="col-md-6 mb-3" id="openEndedSection" style="display:none;">
                            <label class="form-label"><?= lang('Marketing.end_date_optional') ?></label>
                            <div class="form-control bg-light text-muted" style="cursor:default;">
                                <i class="fas fa-infinity me-1"></i><?= lang('Marketing.open_ended') ?>
                            </div>
                            <small class="text-muted"><?= lang('Marketing.open_ended_notice') ?></small>
                        </div>

                        <!-- payment_due_day: for PO_ONLY only -->
                        <div class="col-md-6 mb-3" id="paymentDueSection" style="display:none;">
                            <label class="form-label"><?= lang('Marketing.payment_due_day') ?></label>
                            <input type="number" class="form-control" name="payment_due_day" min="1" max="31" placeholder="15">
                            <small class="text-muted"><?= lang('Marketing.payment_due_day_help') ?></small>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label"><?= lang('Marketing.notes') ?></label>
                            <textarea class="form-control" name="catatan" rows="3" placeholder="<?= lang('Marketing.additional_notes_optional') ?>"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i><?= lang('App.cancel') ?? 'Batal' ?>
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i><?= lang('Marketing.add_rental') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
const LANG_MARKETING_DI = <?= json_encode([
    'create_di_retrieval' => lang('Marketing.create_di_retrieval'),
    'create_di_retrieval_short' => lang('Marketing.create_di_retrieval_short'),
]) ?>;
const LANG_RENTAL = <?= json_encode([
    'type_contract'       => lang('Marketing.rental_type_contract'),
    'type_po'             => lang('Marketing.rental_type_po'),
    'type_harian'         => lang('Marketing.rental_type_harian'),
    'view_detail'         => lang('Marketing.view_detail'),
    'edit_contract'       => lang('Marketing.edit_contract'),
    'delete_contract'     => lang('Marketing.delete_contract'),
    'change_rate'         => lang('Marketing.change_rate'),
    'renewal'             => lang('Marketing.renewal'),
    'no_data'             => lang('Marketing.no_contract_data'),
    'loading'             => lang('Marketing.loading_data'),
    'search_ph'           => lang('Marketing.search_contracts_ph'),
    'customers_count'     => lang('Marketing.customers_count'),
    'th_contract_po'      => lang('Marketing.th_contract_po'),
    'th_type'             => lang('Marketing.th_type'),
    'th_billing'          => lang('Marketing.th_billing'),
    'th_period'           => lang('Marketing.th_period_days'),
    'th_units'            => lang('Marketing.total_units'),
    'th_value'            => lang('Marketing.th_value'),
    'th_actions'          => lang('Marketing.actions'),
    'days_ago'            => lang('Marketing.days_ago') ?? 'days ago',
    'days_left'           => lang('Marketing.days_left') ?? 'days left',
]) ?>;
/**
 * Contract & PO Management Module - Using Optima Badge Standards (optima-pro.css)
 * 
 * Quick Reference:
 * - ACTIVE status      → <span class="badge badge-soft-green">ACTIVE</span>
 * - PENDING status     → <span class="badge badge-soft-yellow">PENDING</span>
 * - EXPIRED status     → <span class="badge badge-soft-red">EXPIRED</span>
 * - CANCELLED status   → <span class="badge badge-soft-gray">CANCELLED</span>
 * - CONTRACT type      → <span class="badge badge-soft-blue">Contract</span>
 * - PO ONLY type       → <span class="badge badge-soft-cyan">PO Only</span>
 * - DAILY/SPOT type    → <span class="badge badge-soft-yellow">Daily</span>
 * - Expiry < 30 days   → <span class="badge badge-soft-orange">X days left</span>
 * - Expiry 31-90 days  → <span class="badge badge-soft-cyan">X days left</span>
 * - Expired            → <span class="badge badge-soft-red">Expired X days ago</span>
 * 
 * See docs/BADGE_STANDARDS.md for complete guide
 */
const KONTRAK_STATE_KEY = 'kontrak_mgmt_state';

let contractsTable;
let currentTab = (function() {
    try {
        const s = sessionStorage.getItem(KONTRAK_STATE_KEY);
        if (s) { const o = JSON.parse(s); if (o.tab) return o.tab; }
    } catch (e) {}
    return 'all';
})();


function saveKontrakState() {
    try {
        sessionStorage.setItem(KONTRAK_STATE_KEY, JSON.stringify({
            tab: currentTab
        }));
    } catch (e) {}
}

$(document).ready(function() {
    // Restore filter UI from saved state (for back navigation UX)
    applySavedFilterUI();
    
    // Load statistics and update tab badges
    loadStatistics();
    
    // Initialize DataTable
    initializeContractsTable();
    
    // Setup tab event handlers
    setupTabHandlers();
    
    // Initialize customer filter Select2
    initCustomerFilter();
    
    // Cleanup Select2 when modal is closed
    $('#addContractModal').on('hidden.bs.modal', function() {
        if ($('#contractCustomerSelect').data('select2')) {
            $('#contractCustomerSelect').select2('destroy');
        }
        if ($('#contractLocationSelect').data('select2')) {
            $('#contractLocationSelect').select2('destroy');
        }
        // Reset rental-type-dependent field states
        $('#modalRentalType').val('CONTRACT');
        onRentalTypeChange('CONTRACT');
    });
});

// Apply saved filter state to UI (for back navigation)
function applySavedFilterUI() {
    $('#contractStatusTabs button').removeClass('active');
    $('#tab-' + currentTab).addClass('active');
    if (currentTab === 'expiring') {
        $('#filterExpiringPeriodWrap').show();
    } else {
        $('#filterExpiringPeriodWrap').hide();
    }
}

// Setup tab click handlers
function setupTabHandlers() {
    $('#contractStatusTabs button[data-tab]').on('click', function() {
        const tab = $(this).data('tab');
        switchTab(tab);
        saveKontrakState();
    });
    
    $('#filterRentalType, #filterExpiringPeriod').on('change', function() {
        applyFilters();
    });
}

// Initialize customer filter Select2 with AJAX
function initCustomerFilter() {
    $('#filterCustomer').select2({
        placeholder: '-- Semua Customer --',
        allowClear: true,
        width: '100%',
        minimumInputLength: 0,
        ajax: {
            url: '<?= base_url('marketing/rental/customers-dropdown') ?>',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { q: params.term || '' };
            },
            processResults: function(response) {
                if (!response.success) return { results: [] };
                return {
                    results: response.data.map(function(c) {
                        return { id: c.id, text: c.customer_name, code: c.customer_code };
                    })
                };
            },
            cache: true
        },
        templateResult: function(item) {
            if (!item.id) return item.text;
            return $('<span><span class="badge badge-soft-blue me-2 font-monospace" style="font-size:0.7em;">' + (item.code || '') + '</span>' + item.text + '</span>');
        },
        templateSelection: function(item) {
            return item.text;
        }
    });
    
    $('#filterCustomer').on('change', function() {
        applyFilters();
    });
}

// Reset all filters
function resetFilters() {
    if ($('#filterCustomer').data('select2')) {
        $('#filterCustomer').val(null).trigger('change.select2');
    }
    $('#filterRentalType').val('');
    $('#filterExpiringPeriod').val('90');
    applyFilters();
}

// Tab switching function
function switchTab(tab) {
    currentTab = tab;
    
    // Update active tab UI
    $('#contractStatusTabs button').removeClass('active');
    $('#tab-' + tab).addClass('active');
    
    // Show/hide expiring period filter
    if (tab === 'expiring') {
        $('#filterExpiringPeriodWrap').show();
    } else {
        $('#filterExpiringPeriodWrap').hide();
    }
    
    // Reload table with new filter
    applyFilters();
}

// Apply filters and reload DataTable
function applyFilters() {
    if (contractsTable) {
        contractsTable.ajax.reload();
    }
}

// Load statistics and update tab badges
function loadStatistics() {
    $.ajax({
        url: '<?= base_url('marketing/rental/stats') ?>',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const stats = response.data;
                // Update stat cards with relevant info
                $('#stat-total-contracts').text(stats.total_contracts || 0);
                $('#stat-active').text(stats.total_active || 0);
                $('#stat-expiring').text(stats.total_expiring_90 || 0);
                $('#stat-total-units').text(stats.total_units_rented || 0);
                
                // Update tab badges
                $('#count-all').text(stats.total_contracts || 0);
                $('#count-active').text(stats.total_active || 0);
                $('#count-expiring').text(stats.total_expiring_90 || 0);
                $('#count-expired').text(stats.total_expired || 0);
            }
        },
        error: function() {
            console.error('Failed to load statistics');
        }
    });
}

// Initialize DataTable
function initializeContractsTable() {
    contractsTable = $('#contractsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('marketing/rental/getDataTable') ?>',
            type: 'POST',
            beforeSend: function(xhr) {
                // Add CSRF token to header
                if (window.csrfToken) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', window.csrfToken);
                }
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            },
            data: function(d) {
                // Add CSRF token to POST data
                if (window.csrfToken) {
                    d[window.csrfTokenName] = window.csrfToken;
                }
                
                // Add tab-based filtering
                d.tab = currentTab;
                d.customer_id = $('#filterCustomer').val() || '';
                d.rental_type = $('#filterRentalType').val() || '';
                if (currentTab === 'expiring') {
                    d.expiring_days = $('#filterExpiringPeriod').val() || 90;
                }
            }
        },
        columns: [
            { 
                data: 'contract_number',
                render: function(data, type, row, meta) {
                    let customerName = row.client_name || '—';
                    let contractNo   = data || '—';
                    let poLabel      = row.po ? `PO: ${row.po}` : '';

                    if (window.OptimaSearch && typeof OptimaSearch.highlightForMeta === 'function') {
                        customerName = OptimaSearch.highlightForMeta(meta, customerName);
                        contractNo   = OptimaSearch.highlightForMeta(meta, contractNo);
                        if (poLabel) {
                            poLabel = OptimaSearch.highlightForMeta(meta, poLabel);
                        }
                    }

                    const poNo = poLabel
                        ? `<br><small class="text-muted"><i class="fas fa-file-invoice me-1"></i>${poLabel}</small>`
                        : '';

                    return `<div class="fw-semibold">${customerName}</div>
                            <small class="text-muted font-monospace">${contractNo}</small>${poNo}`;
                }
            },
            { 
                data: 'rental_type',
                render: function(data, type, row) {
                    const typeMap = {
                        'CONTRACT':   `<span class="badge badge-soft-blue"><i class="fas fa-file-contract me-1"></i>${LANG_RENTAL.type_contract}</span>`,
                        'PO_ONLY':    `<span class="badge badge-soft-cyan"><i class="fas fa-file-invoice me-1"></i>${LANG_RENTAL.type_po}</span>`,
                        'DAILY_SPOT': `<span class="badge badge-soft-yellow"><i class="fas fa-calendar-day me-1"></i>${LANG_RENTAL.type_harian}</span>`
                    };
                    const billingMap = { 'BULANAN': 'Monthly', 'HARIAN': 'Daily' };
                    const typeBadge = typeMap[data] || `<span class="badge badge-soft-gray">${data || '—'}</span>`;
                    const billing = billingMap[row.jenis_sewa] || row.jenis_sewa || '';
                    const billingHtml = billing ? `<br><small class="text-muted">${billing}</small>` : '';
                    return typeBadge + billingHtml;
                }
            },
            { 
                data: 'period',
                render: function(data, type, row, meta) {
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
                    let startLabel = start ? start.toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) : '—';
                    let endLabel   = end   ? end.toLocaleDateString('id-ID',   {day:'2-digit', month:'short', year:'numeric'}) : 'Open-ended';
                    
                    let daysHtml = '';
                    if (end && row.status === 'ACTIVE') {
                        const today = new Date();
                        today.setHours(0,0,0,0);
                        const diffMs = end - today;
                        const days = Math.ceil(diffMs / (1000*60*60*24));
                        if (days < 0) {
                            daysHtml = `<br><span class="badge badge-soft-red">Expired ${Math.abs(days)}h lalu</span>`;
                        } else if (days <= 30) {
                            daysHtml = `<br><span class="badge badge-soft-orange">${days}h lagi</span>`;
                        } else if (days <= 90) {
                            daysHtml = `<br><span class="badge badge-soft-cyan">${days}h lagi</span>`;
                        }
                    }
                    if (window.OptimaSearch && typeof OptimaSearch.highlightForMeta === 'function') {
                        startLabel = OptimaSearch.highlightForMeta(meta, startLabel);
                        endLabel   = OptimaSearch.highlightForMeta(meta, endLabel);
                    }
                    return `<small>${startLabel} – ${endLabel}</small>${daysHtml}`;
                }
            },
            { 
                data: 'total_units', 
                className: 'text-center',
                render: function(data, type, row) {
                    return `<span class="badge badge-soft-blue">${data || 0}</span>`;
                }
            },
            { 
                data: 'value', 
                className: 'text-end',
                render: function(data, type, row, meta) {
                    if (!data || data === '—') return '—';
                    let v = String(data);
                    if (window.OptimaSearch && typeof OptimaSearch.highlightForMeta === 'function') {
                        v = OptimaSearch.highlightForMeta(meta, v);
                    }
                    return `<span class="text-success fw-semibold">${v}</span>`;
                }
            },
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
        searchDelay: 700,
        language: {
            processing: '<i class="fas fa-spinner fa-spin fa-2x"></i><br><?= lang('Marketing.loading_data') ?>',
            emptyTable: '<?= lang('Marketing.no_contract_data') ?>',
            zeroRecords: '<?= lang('Marketing.no_contract_data') ?>'
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

// Refresh table
function refreshTable() {
    contractsTable.ajax.reload();
    loadStatistics();
}

// Export contracts
function exportContracts() {
    let url = '<?= base_url('marketing/rental/export') ?>?';
    
    // Add current filters
    if (currentTab !== 'all') {
        if (currentTab === 'active') url += 'status=ACTIVE&';
        else if (currentTab === 'expired') url += 'status=EXPIRED&';
        else if (currentTab === 'expiring') {
            url += 'status=ACTIVE&';
            const expiringDays = parseInt($('#filterExpiringPeriod').val()) || 90;
            url += 'expiring_days=' + expiringDays + '&';
        }
    }
    
    const rentalType = $('#filterRentalType').val();
    if (rentalType) {
        url += 'rental_type=' + rentalType + '&';
    }
    
    const customerId = $('#filterCustomer').val();
    if (customerId) {
        url += 'customer_id=' + customerId + '&';
    }
    
    window.location.href = url;
}

// Edit contract — navigate to dedicated edit page
function editContract(id) {
    window.location.href = '<?= base_url('marketing/rental/edit') ?>/' + id;
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
    const canCreateDI = (status === 'ACTIVE' || status === 'EXPIRED');
    const diUrl = '<?= base_url('marketing/di') ?>?create_tarik=1&kontrak_id=';

    let renewItem = canRenew ? `<li><a class="dropdown-item" href="#" onclick="event.preventDefault(); openRenewalWizard(${id})"><i class="fas fa-sync-alt text-success me-2"></i>${LANG_RENTAL.renewal}</a></li>` : '';
    let amendItem = canAmend ? `<li><a class="dropdown-item" href="#" onclick="event.preventDefault(); openAmendmentModal(${id})"><i class="fas fa-calculator text-warning me-2"></i>${LANG_RENTAL.change_rate}</a></li>` : '';
    let diItem = canCreateDI ? `<li><a class="dropdown-item" href="${diUrl}${id}"><i class="fas fa-truck-loading text-danger me-2"></i>${LANG_MARKETING_DI.create_di_retrieval_short}</a></li>` : '';
    let divider = (canRenew || canAmend || canCreateDI) ? '<li><hr class="dropdown-divider"></li>' : '';

    return `
        <div class="btn-group">
            <a href="<?= base_url('marketing/rental/detail') ?>/${id}" class="btn btn-sm btn-outline-primary" title="${LANG_RENTAL.view_detail}">
                <i class="fas fa-eye"></i>
            </a>
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="<?= base_url('marketing/rental/detail') ?>/${id}"><i class="fas fa-eye text-primary me-2"></i>${LANG_RENTAL.view_detail}</a></li>
                <li><a class="dropdown-item" href="<?= base_url('marketing/rental/edit') ?>/${id}"><i class="fas fa-edit text-info me-2"></i>${LANG_RENTAL.edit_contract}</a></li>
                ${renewItem}
                ${amendItem}
                ${diItem}
                ${divider}
                <li><a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); deleteContract(${id})"><i class="fas fa-trash me-2"></i>${LANG_RENTAL.delete_contract}</a></li>
            </ul>
        </div>`;
}

// View contract units
function viewContractUnits(id) {
    // Open modal with contract units
    $.ajax({
        url: '<?= base_url('marketing/rental/units') ?>/' + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                OptimaNotify.info('Contract Units: ' + response.count + ' units');
            }
        }
    });
}

// Delete contract
function deleteContract(id) {
    OptimaConfirm.danger({
        title: 'Hapus Kontrak',
        text: 'Apakah Anda yakin ingin menghapus kontrak ini? Tindakan ini tidak dapat dibatalkan.',
        onConfirm: function() {
            $.ajax({
        url: '<?= base_url('marketing/rental/delete') ?>/' + id,
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
    });
}

// Show notification
function showNotification(message, type = 'info') {
    if (window.OptimaNotify && typeof OptimaNotify[type] === 'function') {
        OptimaNotify[type](message);
    } else if (window.OptimaPro) {
        OptimaPro.showNotification(message, type);
    }
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
        url: '<?= base_url('marketing/rental/get') ?>/' + contractId,
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
        url: '<?= base_url('marketing/rental/get-active-contracts') ?>',
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
        url: '<?= base_url('marketing/rental/getContractHistory') ?>/' + contractId,
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
        url: '<?= base_url('marketing/rental/getRateHistory') ?>/' + contractId,
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
        url: '<?= base_url('marketing/rental/get') ?>/' + contractId,
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
                contractHtml += '<p><span class="badge badge-soft-blue font-monospace">' + (contract.no_kontrak || 'N/A') + '</span></p>';
                contractHtml += '</div>';
                
                contractHtml += '<div class="col-md-4 mb-3">';
                contractHtml += '<label class="text-muted small">Contract Type</label>';
                contractHtml += '<p>' + (contract.rental_type || 'N/A') + '</p>';
                contractHtml += '</div>';
                
                contractHtml += '<div class="col-md-4 mb-3">';
                contractHtml += '<label class="text-muted small">Status</label>';
                let statusClass = contract.status === 'ACTIVE' ? 'badge-soft-green' : (contract.status === 'EXPIRED' ? 'badge-soft-red' : contract.status === 'PENDING' ? 'badge-soft-yellow' : 'badge-soft-gray');
                contractHtml += '<p><span class="badge ' + statusClass + '">' + (contract.status || 'N/A') + '</span></p>';
                contractHtml += '</div>';
                
                contractHtml += '<div class="col-md-4 mb-3">';
                contractHtml += '<label class="text-muted small">PO Number</label>';
                contractHtml += '<p>' + (contract.po_number ? '<span class="badge badge-soft-cyan font-monospace">' + contract.po_number + '</span>' : '<span class="text-muted">N/A</span>') + '</p>';
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
        url: '<?= base_url('marketing/rental/units') ?>/' + contractId,
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
                    html += '<span class="badge badge-soft-blue ms-2">' + units.length + ' unit(s)</span>';
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
                        html += '<td><span class="badge badge-soft-green">Active</span></td>';
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
        url: '<?= base_url('marketing/rental/getContractHistory') ?>/' + contractId,
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
        url: '<?= base_url('marketing/rental/getRateHistory') ?>/' + contractId,
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
        url: '<?= base_url('marketing/rental/documents') ?>/' + contractId,
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
            window.location.href = '<?= base_url('marketing/rental/edit') ?>/' + currentContractId;
        }, 300);
    } else {
        console.error('No contract ID selected');
        OptimaNotify.error('Tidak ada kontrak yang dipilih');
    }
}

/**
 * Delete Contract from Modal
 */
function deleteContractFromModal() {
    if (!currentContractId) return;
    
    OptimaConfirm.danger({
        title: 'Delete Contract?',
        text: 'This action cannot be undone. All related data will be removed.',
        confirmText: 'Yes, delete it!',
        cancelText: window.lang('cancel'),
        onConfirm: function() {
            $.ajax({
                url: '<?= base_url('marketing/rental/delete') ?>/' + currentContractId,
                type: 'POST',
                data: { 
                    [window.csrfTokenName]: window.csrfToken || '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        $('#contractDetailModal').modal('hide');
                        OptimaNotify.success('Contract has been deleted.');
                        refreshTable();
                    } else {
                        OptimaNotify.error(response.message || 'Failed to delete contract');
                    }
                },
                error: function() {
                    OptimaNotify.error('Error deleting contract');
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
    OptimaNotify.info('Upload document functionality - to be implemented');
}

/**
 * Delete Document
 */
function deleteDocument(docId) {
    OptimaConfirm.danger({
        title: 'Hapus Dokumen?',
        text: 'Dokumen yang dihapus tidak dapat dikembalikan.',
        confirmText: 'Ya, Hapus!',
        cancelText: window.lang('cancel'),
        onConfirm: function() {
            $.ajax({
                url: '<?= base_url('marketing/rental/deleteDocument') ?>/' + docId,
                type: 'POST',
                data: { 
                    [window.csrfTokenName]: window.csrfToken || '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        loadContractDocuments(currentContractId);
                        OptimaNotify.success('Dokumen berhasil dihapus');
                    }
                }
            });
        }
    });
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
    
    // Destroy existing Select2 instances
    if ($('#contractCustomerSelect').data('select2')) {
        $('#contractCustomerSelect').select2('destroy');
    }
    if ($('#contractLocationSelect').data('select2')) {
        $('#contractLocationSelect').select2('destroy');
    }
    
    // Reset location dropdown
    $('#contractLocationSelect').empty()
        .append('<option value="">-- Select Customer First --</option>')
        .prop('disabled', true);
    
    // Load customers for dropdown
    loadCustomersForContract();
    
    // Apply default rental type state (CONTRACT selected by default)
    onRentalTypeChange('CONTRACT');

    // Show modal
    $('#addContractModal').modal('show');
}

/**
 * Load customers for contract creation dropdown
 */
function loadCustomersForContract() {
    $.ajax({
        url: '<?= base_url('marketing/rental/customers-dropdown') ?>',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const customerSelect = $('#contractCustomerSelect');
                customerSelect.empty().append('<option value="">-- Select Customer --</option>');
                response.data.forEach(customer => {
                    const code = customer.customer_code || '';
                    // Only store customer name in option text, templates will add badge
                    customerSelect.append(`<option value="${customer.id}" data-code="${code}">${customer.customer_name}</option>`);
                });
                
                // Initialize Select2 with custom template
                customerSelect.select2({
                    placeholder: '-- Select Customer --',
                    allowClear: true,
                    dropdownParent: $('#addContractModal'),
                    width: '100%',
                    templateResult: formatCustomerOption,
                    templateSelection: formatCustomerSelection,
                    escapeMarkup: function(markup) { return markup; } // Allow HTML in templates
                });
            }
        },
        error: function() {
            showNotification('Error loading customers', 'error');
        }
    });
}

/**
 * Format customer option in Select2 dropdown
 */
function formatCustomerOption(customer) {
    if (!customer.id) return customer.text;
    const code = $(customer.element).data('code');
    if (!code) return customer.text;
    
    // Return HTML with badge
    return `<div class="d-flex align-items-center">
        <span class="badge badge-soft-blue me-2 font-monospace text-xxs">${code}</span>
        <span>${customer.text}</span>
    </div>`;
}

/**
 * Format selected customer in Select2
 */
function formatCustomerSelection(customer) {
    if (!customer.id) return customer.text;
    const code = $(customer.element).data('code');
    if (!code) return customer.text;
    
    // Return HTML with badge (escapeMarkup allows this)
    return `<span class="badge badge-soft-blue me-2 font-monospace text-xxs">${code}</span>${customer.text}`;
}

/**
 * Customer change event - load locations
 */
$(document).on('change', '#contractCustomerSelect', function() {
    const customerId = $(this).val();
    const locationSelect = $('#contractLocationSelect');
    
    if (customerId) {
        // Destroy Select2 if exists
        if (locationSelect.data('select2')) {
            locationSelect.select2('destroy');
        }
        
        $.ajax({
            url: `<?= base_url('marketing/rental/locations/') ?>${customerId}`,
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function(xhr) {
                // Add CSRF token
                if (window.csrfTokenName && window.csrfTokenValue) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', window.csrfTokenValue);
                }
            },
            success: function(response) {
                if (response.success) {
                    locationSelect.empty().append('<option value="">-- Select Location --</option>');
                    response.data.forEach(location => {
                        const isPrimary = location.is_primary == 1;
                        const badge = isPrimary ? ' <span class="badge badge-soft-success">Primary</span>' : '';
                        locationSelect.append(`<option value="${location.id}">${location.location_name}${badge}</option>`);
                    });
                    locationSelect.prop('disabled', false);
                    
                    // Initialize Select2 for location
                    locationSelect.select2({
                        placeholder: '-- Select Location --',
                        allowClear: true,
                        dropdownParent: $('#addContractModal'),
                        width: '100%'
                    });
                } else {
                    showNotification(response.message || 'Error loading locations', 'error');
                }
            },
            error: function(xhr) {
                console.error('Location loading error:', xhr);
                const errorMsg = xhr.responseJSON?.message || 'Error loading locations';
                if (typeof window.optimaAssistNotify === 'function') {
                    window.optimaAssistNotify(errorMsg, 'error');
                } else {
                    showNotification(errorMsg, 'error');
                }
            }
        });
    } else {
        // Destroy Select2 if exists
        if (locationSelect.data('select2')) {
            locationSelect.select2('destroy');
        }
        locationSelect.empty().append('<option value="">-- Select Customer First --</option>').prop('disabled', true);
    }
});

// Rental type descriptions (rendered server-side for multi-language support)
const RENTAL_TYPE_DESC = {
    'CONTRACT':   '<?= esc(lang('Marketing.contract_type_notice'),  'js') ?>',
    'PO_ONLY':    '<?= esc(lang('Marketing.open_ended_notice'),     'js') ?>',
    'DAILY_SPOT': '<?= esc(str_replace('{days}', '30', lang('Marketing.max_duration_notice')), 'js') ?>'
};

/**
 * Rental type change — show/hide date sections & update description
 */
function onRentalTypeChange(type) {
    const isPO    = type === 'PO_ONLY';
    const isSpot  = type === 'DAILY_SPOT';

    // End-date section: hidden for PO_ONLY
    $('#endDateSection').toggle(!isPO);
    $('#endDateLabel').text(
        isSpot ? '<?= esc(lang('Marketing.end_date'), 'js') ?> *'
               : '<?= esc(lang('Marketing.end_date'), 'js') ?> *'
    );

    // Open-ended notice: only PO_ONLY
    $('#openEndedSection').toggle(isPO);

    // Payment due day: only PO_ONLY
    $('#paymentDueSection').toggle(isPO);

    // Billing period (jenis_sewa): auto-set & hide for PO/Harian
    if (isPO) {
        $('#modalJenisSewa').val('BULANAN');
        $('#billingPeriodSection').hide();
    } else if (isSpot) {
        $('#modalJenisSewa').val('HARIAN');
        $('#billingPeriodSection').hide();
    } else {
        $('#billingPeriodSection').show();
    }

    // PO_ONLY: contract_number is auto-generated (readonly), po_number becomes required
    if (isPO) {
        $('#modalContractNumber').attr('readonly', true).addClass('bg-light text-muted');
        $('#generateContractNumber').hide();
        $('#contractNumberLabel').html('Nomor Internal <span class="badge badge-soft-gray ms-1" style="font-size:0.7em;">Auto</span>');
        $('#contractNumberHint').text('Dibuat otomatis oleh sistem');
        $('#poNumberLabel').html('<?= esc(lang('Marketing.client_po_number'), 'js') ?> *');
        $('#modalPoNumber').attr('required', true).attr('placeholder', 'Masukkan nomor PO dari customer');
    } else {
        $('#modalContractNumber').removeAttr('readonly').removeClass('bg-light text-muted');
        $('#generateContractNumber').show();
        $('#contractNumberLabel').html('<?= esc(lang('Marketing.rental_number_label'), 'js') ?> *');
        $('#contractNumberHint').text('<?= esc(lang('Marketing.select_customer_first'), 'js') ?>');
        $('#poNumberLabel').text('<?= esc(lang('Marketing.client_po_number'), 'js') ?>');
        $('#modalPoNumber').removeAttr('required').attr('placeholder', '<?= esc(lang('Marketing.client_po_number'), 'js') ?>');
    }

    // Description text
    $('#rentalTypeDesc').text(RENTAL_TYPE_DESC[type] || '');

    // Re-generate number for new type
    generateContractNumberByType(type);
}

$(document).on('change', '#modalRentalType', function() {
    onRentalTypeChange($(this).val());
});

/**
 * Generate rental number via backend (type-aware prefix)
 */
function generateContractNumberByType(rentalType) {
    rentalType = rentalType || $('#modalRentalType').val() || 'CONTRACT';
    $.ajax({
        url: '<?= base_url('marketing/rental/generate-number') ?>',
        method: 'GET',
        data: { rental_type: rentalType },
        success: function(response) {
            if (response.success && response.data) {
                $('#modalContractNumber').val(response.data.contract_number);
                if (window.csrfTokenName && response.csrf_hash) {
                    window.csrfToken = response.csrf_hash;
                }
            }
        },
        error: function() {
            showNotification('<?= esc(lang('Marketing.generate_rental_number'), 'js') ?> failed', 'error');
        }
    });
}

/**
 * Generate contract number (button click)
 */
$(document).on('click', '#generateContractNumber', function() {
    generateContractNumberByType($('#modalRentalType').val() || 'CONTRACT');
});

/**
 * Handle contract form submission
 */
$(document).on('submit', '#addContractForm', function(e) {
    e.preventDefault();
    
    if (window.OptimaPro && typeof OptimaPro.showLoading === 'function') {
        OptimaPro.showLoading('Creating contract...');
    }
    
    $.ajax({
        url: '<?= base_url('marketing/rental/store') ?>',
        method: 'POST',
        data: $(this).serialize() + '&' + encodeURIComponent(window.csrfTokenName) + '=' + encodeURIComponent(window.csrfToken || ''),
        success: function(response) {
            if (window.OptimaPro && typeof OptimaPro.hideLoading === 'function') {
                OptimaPro.hideLoading();
            }
            
            if (response.success) {
                showNotification(response.message || 'Contract created successfully', 'success');
                $('#addContractModal').modal('hide');
                $('#addContractForm')[0].reset();
                
                // Reload contract table
                if (contractsTable) {
                    contractsTable.ajax.reload();
                }
                
                // Open detail for new rental (API returns data.id)
                const newRentalId = (response.data && response.data.id) ? response.data.id : (response.contract_id || null);
                if (newRentalId) {
                    setTimeout(() => {
                        viewContractDetail(newRentalId);
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
            if (window.OptimaPro && typeof OptimaPro.hideLoading === 'function') {
                OptimaPro.hideLoading();
            }
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
