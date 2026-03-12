<?= $this->extend('layouts/base') ?>

<?php
/**
 * Contract Edit (Kontrak Edit) - Marketing
 * BADGE/CARD: Optima badge-soft-* for status; card-header bg-light.
 */
helper('simple_rbac');
$can_create = can_create('marketing');
$status = $contract['status'] ?? '';
$statusMap = ['ACTIVE' => 'badge-soft-green', 'EXPIRED' => 'badge-soft-red', 'PENDING' => 'badge-soft-yellow', 'CANCELLED' => 'badge-soft-gray'];
$statusClass = $statusMap[$status] ?? 'badge-soft-gray';
?>

<?= $this->section('content') ?>

<div class="mb-3 d-flex align-items-center gap-2">
    <a href="<?= base_url('marketing/kontrak') ?>" class="btn btn-sm btn-outline-secondary">
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
                    <input type="hidden" name="customer_id" value="<?= (int)$contract['customer_id'] ?>">
                    <?= csrf_field() ?>

                    <!-- ROW 1 – Contract No / PO Number -->
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

                    <!-- ROW 2 – Customer (Read-only) -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Customer <span class="text-danger">*</span></label>
                            <select class="form-select" id="editCustomerSelect" disabled>
                                <option value="">Loading customers…</option>
                            </select>
                            <small class="text-muted">Customer cannot be changed after contract creation. Location is now tracked per unit.</small>
                        </div>
                    </div>

                    <!-- ROW 3 – Dates -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="start_date"
                                   value="<?= esc($contract['tanggal_mulai'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="end_date"
                                   value="<?= esc($contract['tanggal_berakhir'] ?? '') ?>" required>
                        </div>
                    </div>

                    <!-- ROW 4 – Type / Billing / Status -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Rental Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="rental_type">
                                <?php foreach (['CONTRACT' => 'Formal Contract', 'PO_ONLY' => 'PO-Based Only', 'DAILY_SPOT' => 'Daily/Spot'] as $val => $lbl): ?>
                                <option value="<?= $val ?>" <?= ($contract['rental_type'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Billing Period <span class="text-danger">*</span></label>
                            <select class="form-select" name="jenis_sewa">
                                <option value="BULANAN" <?= ($contract['jenis_sewa'] ?? '') === 'BULANAN' ? 'selected' : '' ?>>Monthly</option>
                                <option value="HARIAN"  <?= ($contract['jenis_sewa'] ?? '') === 'HARIAN'  ? 'selected' : '' ?>>Daily</option>
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
                    </div>

                    <!-- ROW 5 – Financial (Auto-calculated) -->
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

                    <!-- ROW 6 – Notes -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea class="form-control" name="catatan" rows="3"><?= esc($contract['catatan'] ?? '') ?></textarea>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex gap-2 pt-2 border-top">
                        <button type="submit" class="btn btn-primary px-4" id="btnSave">
                            <i class="fas fa-save me-1"></i>Save Changes
                        </button>
                        <a href="<?= base_url('marketing/kontrak') ?>" class="btn btn-outline-secondary">
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
const LIST_URL        = '<?= base_url('marketing/kontrak') ?>';
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
                setTimeout(() => window.location.href = LIST_URL, 1500);
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
    loadContractTotals(); // Auto-calculate from units
});
</script>
<?= $this->endSection() ?>
