<?= $this->extend('layouts/base') ?>

<?php
/**
 * Contract Edit (Kontrak Edit) - Marketing
 * BADGE/CARD: Optima badge-soft-* for status; card-header bg-light.
 */
helper('simple_rbac');
$can_edit = (
    (function_exists('canPerformAction') && canPerformAction('marketing', 'kontrak', 'edit'))
    || (function_exists('hasPermission') && hasPermission('marketing.kontrak.edit'))
    || (function_exists('hasPermission') && hasPermission('marketing.contract.edit'))
    || can_edit('marketing')
);
$status = $contract['status'] ?? '';
$statusMap = ['ACTIVE' => 'badge-soft-green', 'EXPIRED' => 'badge-soft-red', 'PENDING' => 'badge-soft-yellow', 'CANCELLED' => 'badge-soft-gray'];
$statusClass = $statusMap[$status] ?? 'badge-soft-gray';
?>

<?= $this->section('content') ?>

<div class="mb-3 d-flex align-items-center gap-2">
    <a href="<?= base_url('marketing/kontrak/detail/' . (int)$contract['id']) ?>" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back
    </a>
    <div>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-edit me-2 text-primary"></i>Edit Contract
        </h4>
        <small class="text-muted"><?= esc($contract['no_kontrak'] ?? 'Contract #' . $contract['id']) ?></small>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex align-items-center">
                <i class="fas fa-file-contract me-2"></i>
                <strong>Contract Information</strong>
                <span class="ms-auto badge <?= $statusClass ?>"><?= esc($contract['status'] ?? '') ?></span>
            </div>
            <div class="card-body">
                <div id="editAlert"></div>

                <form id="editContractForm">
                    <input type="hidden" id="contractId" value="<?= (int)$contract['id'] ?>">
                    <input type="hidden" name="customer_id" id="hiddenCustomerId" value="<?= (int)$contract['customer_id'] ?>">
                    <?= csrf_field() ?>

                    <!-- ROW 1 – Rental Type & Status -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Rental Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="rental_type" id="editRentalType" onchange="toggleRentalFields()">
                                <?php foreach (['CONTRACT' => 'Formal Contract', 'PO_ONLY' => 'PO-Based Only', 'DAILY_SPOT' => 'Daily/Spot'] as $val => $lbl): ?>
                                <option value="<?= $val ?>" <?= ($contract['rental_type'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select class="form-select" name="status" required>
                                <?php foreach (['ACTIVE' => 'Active', 'PENDING' => 'Pending', 'EXPIRED' => 'Expired', 'CANCELLED' => 'Cancelled'] as $val => $lbl): ?>
                                <option value="<?= $val ?>" <?= ($contract['status'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Billing Period <span class="text-danger">*</span></label>
                            <select class="form-select" name="jenis_sewa" id="editBillingPeriod">
                                <option value="BULANAN" <?= ($contract['jenis_sewa'] ?? '') === 'BULANAN' ? 'selected' : '' ?>>Monthly</option>
                                <option value="HARIAN"  <?= ($contract['jenis_sewa'] ?? '') === 'HARIAN'  ? 'selected' : '' ?>>Daily</option>
                            </select>
                        </div>
                    </div>

                    <!-- ROW 2 – Contract No / PO Number -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contract Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="contract_number"
                                   value="<?= esc($contract['no_kontrak'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Customer PO Number</label>
                            <input type="text" class="form-control" name="po_number"
                                   value="<?= esc($contract['customer_po_number'] ?? '') ?>"
                                   placeholder="Customer PO (optional)">
                        </div>
                    </div>

                    <!-- ROW 3 – Customer (Read-only) -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Customer <span class="text-danger">*</span></label>
                            <?php $hasCustomer = !empty($contract['customer_id']); ?>
                            <select class="form-select" id="editCustomerSelect" <?= $hasCustomer ? 'disabled' : '' ?>>
                                <option value="">Loading customers…</option>
                            </select>
                            <?php if ($hasCustomer): ?>
                            <small class="text-muted">Customer cannot be changed after contract creation. Location is now tracked per unit.</small>
                            <?php else: ?>
                            <small class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Customer belum diisi. Silakan pilih customer untuk kontrak ini.</small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- ROW 4 – Dates -->
                    <div class="row mb-3" id="rowDates">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="start_date"
                                   value="<?= esc($contract['tanggal_mulai'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6" id="colEndDate">
                            <label class="form-label fw-semibold">End Date <span class="text-danger" id="endDateRequired">*</span></label>
                            <input type="date" class="form-control" name="end_date" id="editEndDate"
                                   value="<?= esc($contract['tanggal_berakhir'] ?? '') ?>">
                            <small class="text-muted" id="endDateHelp"></small>
                        </div>
                    </div>

                    <!-- ROW 5 – PO_ONLY specific: Payment Due Day -->
                    <div class="row mb-3" id="rowPoFields" style="display:none;">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Due Day</label>
                            <input type="number" class="form-control" name="payment_due_day" id="editPaymentDueDay"
                                   min="1" max="31" value="<?= esc($contract['payment_due_day'] ?? '') ?>"
                                   placeholder="e.g. 25">
                            <small class="text-muted">Tanggal jatuh tempo pembayaran tiap bulan (1-31)</small>
                        </div>
                    </div>

                    <!-- ROW 5b – DAILY_SPOT specific -->
                    <div class="row mb-3" id="rowDailyFields" style="display:none;">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Estimated Duration (days)</label>
                            <input type="number" class="form-control" name="estimated_duration_days" id="editEstDuration"
                                   min="1" max="30" value="<?= esc($contract['estimated_duration_days'] ?? '') ?>">
                            <small class="text-muted">Max 30 hari</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Spot Rental Number</label>
                            <input type="text" class="form-control" name="spot_rental_number"
                                   value="<?= esc($contract['spot_rental_number'] ?? '') ?>" placeholder="SPT-xxx">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Actual Return Date</label>
                            <input type="date" class="form-control" name="actual_return_date"
                                   value="<?= esc($contract['actual_return_date'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- ROW 6 – Financial (Auto-calculated) -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contract Value (Rp)</label>
                            <input type="text" class="form-control" id="contractValue" 
                                   value="<?= number_format($contract['nilai_total'] ?? 0, 0, ',', '.') ?>" readonly>
                            <small class="text-muted">Auto-calculated from units</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Total Units</label>
                            <input type="text" class="form-control" id="totalUnits"
                                   value="<?= esc($contract['total_units'] ?? 0) ?>" readonly>
                            <small class="text-muted">Auto-calculated from units</small>
                        </div>
                    </div>

                    <!-- ROW 7 – Notes -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea class="form-control" name="catatan" rows="3"><?= esc($contract['catatan'] ?? '') ?></textarea>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex gap-2 pt-2 border-top">
                        <?php if ($can_edit): ?>
                        <button type="submit" class="btn btn-primary px-4" id="btnSave">
                            <i class="fas fa-save me-1"></i>Save Changes
                        </button>
                        <?php endif; ?>
                        <a href="<?= base_url('marketing/kontrak/detail/' . (int)$contract['id']) ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar info -->
    <div class="col-lg-4">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light"><strong>Contract Info</strong></div>
            <div class="card-body small">
                <dl class="mb-0">
                    <dt>ID</dt><dd class="text-muted">#<?= (int)$contract['id'] ?></dd>
                    <dt>Created</dt><dd class="text-muted"><?= esc($contract['created_at'] ?? '-') ?></dd>
                    <dt>Last Updated</dt><dd class="text-muted"><?= esc($contract['updated_at'] ?? '-') ?></dd>
                </dl>
            </div>
        </div>
        <div class="alert alert-info small">
            <i class="fas fa-info-circle me-1"></i>
            Changes to Status will automatically update the inventory unit allocation status.
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
const CONTRACT_ID = <?= (int)$contract['id'] ?>;
const CUSTOMERS_URL   = '<?= base_url('marketing/kontrak/customers') ?>';
const UPDATE_URL      = '<?= base_url('marketing/kontrak/update') ?>/' + CONTRACT_ID;
const DETAIL_URL      = '<?= base_url('marketing/kontrak/detail') ?>/' + CONTRACT_ID;
const UNITS_URL       = '<?= base_url('marketing/kontrak/units') ?>/' + CONTRACT_ID;

// ── Load customers dropdown ──────────────────────────────────────────────────
function loadCustomers() {
    $.getJSON(CUSTOMERS_URL, function(res) {
        const $sel = $('#editCustomerSelect').empty().append('<option value="">-- Select Customer --</option>');
        if (res.success && res.data) {
            res.data.forEach(c => {
                $sel.append(new Option(c.customer_name, c.id));
            });
        }
        // Select the current customer
        const contractCustomerId = <?= json_encode($contract['customer_id'] ?? null) ?>;
        if (contractCustomerId) {
            $('#editCustomerSelect').val(contractCustomerId);
        }
        // Sync hidden input when user changes customer
        $('#editCustomerSelect').on('change', function() {
            $('#hiddenCustomerId').val($(this).val());
        });
    }).fail(function() {
        $('#editCustomerSelect').html('<option value="">Error loading customers</option>');
    });
}

// ── Load and calculate contract totals from units ────────────────────────────
function loadContractTotals() {
    $.getJSON(UNITS_URL, function(res) {
        if (res.success && res.data) {
            let totalValue = 0;
            let totalUnits = 0;
            
            res.data.forEach(function(unit) {
                // Count non-spare units
                if (!unit.is_spare) {
                    const rate = parseFloat(unit.harga_efektif || unit.harga_sewa_bulanan || 0);
                    totalValue += rate;
                }
                totalUnits++;
            });
            
            // Update the display fields
            $('#contractValue').val('Rp ' + totalValue.toLocaleString('id-ID'));
            $('#totalUnits').val(totalUnits);
        }
    }).fail(function() {
        console.error('Failed to load units for totals calculation');
    });
}

// ── Form submit ──────────────────────────────────────────────────────────────
$('#editContractForm').on('submit', function(e) {
    e.preventDefault();
    const $btn = $('#btnSave').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Saving…');
    const $alert = $('#editAlert').html('');

    $.ajax({
        url: UPDATE_URL,
        type: 'POST',
        data: $(this).serialize(),
        success: function(res) {
            if (res.success) {
                $alert.html('<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Contract updated successfully! Redirecting…</div>');
                setTimeout(() => window.location.href = DETAIL_URL, 1500);
            } else {
                const errMsg = res.errors
                    ? '<ul class="mb-0">' + Object.values(res.errors).map(e => `<li>${e}</li>`).join('') + '</ul>'
                    : (res.message || 'An error occurred.');
                $alert.html('<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i>' + errMsg + '</div>');
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save Changes');
            }
        },
        error: function() {
            $alert.html('<div class="alert alert-danger">Server error. Please try again.</div>');
            $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save Changes');
        }
    });
});

// ── Init ─────────────────────────────────────────────────────────────────────
$(document).ready(function() {
    loadCustomers();
    loadContractTotals();
    toggleRentalFields(); // Set initial state based on rental type
});

// ── Toggle fields based on Rental Type ───────────────────────────────────────
function toggleRentalFields() {
    const type = document.getElementById('editRentalType').value;
    const endDateInput  = document.getElementById('editEndDate');
    const endDateReq    = document.getElementById('endDateRequired');
    const endDateHelp   = document.getElementById('endDateHelp');
    const rowPoFields   = document.getElementById('rowPoFields');
    const rowDailyFields= document.getElementById('rowDailyFields');
    const billingSelect = document.getElementById('editBillingPeriod');

    // Reset
    rowPoFields.style.display = 'none';
    rowDailyFields.style.display = 'none';
    endDateInput.required = true;
    endDateReq.style.display = '';
    endDateHelp.textContent = '';

    if (type === 'PO_ONLY') {
        // PO: open-ended, no end date required, show payment due day
        endDateInput.required = false;
        endDateReq.style.display = 'none';
        endDateHelp.textContent = 'Optional — PO Bulanan bersifat open-ended';
        rowPoFields.style.display = '';
        billingSelect.value = 'BULANAN';
        billingSelect.disabled = true;
    } else if (type === 'DAILY_SPOT') {
        // Daily: end date optional (max 30 days), show spot fields
        endDateInput.required = false;
        endDateReq.style.display = 'none';
        endDateHelp.textContent = 'Max 30 hari dari start date';
        rowDailyFields.style.display = '';
        billingSelect.value = 'HARIAN';
        billingSelect.disabled = true;
    } else {
        // CONTRACT: standard, end date required
        billingSelect.disabled = false;
    }
}
</script>
<?= $this->endSection() ?>
