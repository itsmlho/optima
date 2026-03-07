<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<?php
$audit = $audit ?? [];
$items = $audit['items'] ?? [];
?>

<!-- Page Header -->
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('service/unit-audit/location') ?>">Audit Unit per Lokasi</a></li>
                <li class="breadcrumb-item active">Input Hasil Audit</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-edit me-2 text-primary"></i>Input Hasil Audit
        </h4>
        <p class="text-muted small mb-0">
            Audit: <strong><?= $audit['audit_number'] ?? '-' ?></strong> |
            <?= $audit['customer_name'] ?? '-' ?> - <?= $audit['location_name'] ?? '-' ?>
        </p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" onclick="window.history.back()">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </button>
        <button class="btn btn-primary" onclick="printAuditForm()">
            <i class="fas fa-print me-1"></i>Print Form
        </button>
    </div>
</div>

<!-- Audit Info -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small">Status</div>
                <div class="fw-bold"><?= getStatusBadge($audit['status'] ?? 'DRAFT') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small">Total Unit (Kontrak)</div>
                <div class="fs-4 fw-bold"><?= $audit['kontrak_total_units'] ?? 0 ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small">Spare Unit (Kontrak)</div>
                <div class="fs-4 fw-bold"><?= $audit['kontrak_spare_units'] ?? 0 ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small">Tanggal Audit</div>
                <div class="fw-bold"><?= date('d-m-Y', strtotime($audit['audit_date'] ?? date('Y-m-d'))) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Input Form -->
<form id="auditResultForm">
    <input type="hidden" name="audit_id" value="<?= $audit['id'] ?? '' ?>">

    <!-- Unit Items Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Detail Unit</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="itemsTable">
                    <thead>
                        <tr class="table-light">
                            <th class="text-center" style="width: 40px;">No</th>
                            <th>No Unit<br><small class="text-muted">(Kontrak)</small></th>
                            <th>Serial Number<br><small class="text-muted">(Kontrak)</small></th>
                            <th>Merk/Model<br><small class="text-muted">(Kontrak)</small></th>
                            <th>Spare?</th>
                            <th>No Unit<br><small class="text-muted">(Actual)</small></th>
                            <th>Serial Number<br><small class="text-muted">(Actual)</small></th>
                            <th>Merk/Model<br><small class="text-muted">(Actual)</small></th>
                            <th>Spare?</th>
                            <th>Operator?</th>
                            <th>Hasil</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($items as $item): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= $item['expected_no_unit'] ?? '-' ?></td>
                            <td><?= $item['expected_serial'] ?? '-' ?></td>
                            <td><?= ($item['expected_merk'] ?? '') . ' ' . ($item['expected_model'] ?? '') ?></td>
                            <td class="text-center"><?= ($item['expected_is_spare'] ?? 0) == 1 ? '<span class="badge bg-warning">YES</span>' : 'NO' ?></td>
                            <td>
                                <input type="text" class="form-control form-control-sm"
                                    name="items[<?= $item['id'] ?>][actual_no_unit]"
                                    value="<?= $item['actual_no_unit'] ?? '' ?>"
                                    placeholder="No Unit Actual">
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm"
                                    name="items[<?= $item['id'] ?>][actual_serial]"
                                    value="<?= $item['actual_serial'] ?? '' ?>"
                                    placeholder="Serial Actual">
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm"
                                    name="items[<?= $item['id'] ?>][actual_merk]"
                                    value="<?= $item['actual_merk'] ?? '' ?>"
                                    placeholder="Merk/Model Actual">
                            </td>
                            <td class="text-center">
                                <select class="form-select form-select-sm"
                                    name="items[<?= $item['id'] ?>][actual_is_spare]">
                                    <option value="0" <?= ($item['actual_is_spare'] ?? 0) == 0 ? 'selected' : '' ?>>NO</option>
                                    <option value="1" <?= ($item['actual_is_spare'] ?? 0) == 1 ? 'selected' : '' ?>>YES</option>
                                </select>
                            </td>
                            <td class="text-center">
                                <select class="form-select form-select-sm"
                                    name="items[<?= $item['id'] ?>][actual_operator_present]">
                                    <option value="0" <?= ($item['actual_operator_present'] ?? 0) == 0 ? 'selected' : '' ?>>NO</option>
                                    <option value="1" <?= ($item['actual_operator_present'] ?? 0) == 1 ? 'selected' : '' ?>>YES</option>
                                </select>
                            </td>
                            <td>
                                <select class="form-select form-select-sm result-select"
                                    name="items[<?= $item['id'] ?>][result]"
                                    onchange="checkResult(this)">
                                    <option value="MATCH" <?= ($item['result'] ?? 'MATCH') == 'MATCH' ? 'selected' : '' ?>>MATCH</option>
                                    <option value="NO_UNIT_IN_KONTRAK" <?= ($item['result'] ?? '') == 'NO_UNIT_IN_KONTRAK' ? 'selected' : '' ?>>Tidak Ada di Kontrak</option>
                                    <option value="EXTRA_UNIT" <?= ($item['result'] ?? '') == 'EXTRA_UNIT' ? 'selected' : '' ?>>Unit Extra</option>
                                    <option value="MISMATCH_NO_UNIT" <?= ($item['result'] ?? '') == 'MISMATCH_NO_UNIT' ? 'selected' : '' ?>>No Unit Berbeda</option>
                                    <option value="MISMATCH_SERIAL" <?= ($item['result'] ?? '') == 'MISMATCH_SERIAL' ? 'selected' : '' ?>>Serial Berbeda</option>
                                    <option value="MISMATCH_SPEC" <?= ($item['result'] ?? '') == 'MISMATCH_SPEC' ? 'selected' : '' ?>>Spesifikasi Berbeda</option>
                                    <option value="MISMATCH_SPARE" <?= ($item['result'] ?? '') == 'MISMATCH_SPARE' ? 'selected' : '' ?>>Status Spare Berbeda</option>
                                </select>
                                <input type="text" class="form-control form-control-sm mt-1"
                                    name="items[<?= $item['id'] ?>][notes]"
                                    value="<?= $item['notes'] ?? '' ?>"
                                    placeholder="Catatan">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Ringkasan Hasil Audit</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Total Unit Ditemukan</label>
                    <input type="number" class="form-control" name="summary[actual_total_units]"
                        value="<?= $audit['actual_total_units'] ?? '' ?>" min="0" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Total Spare Unit</label>
                    <input type="number" class="form-control" name="summary[actual_spare_units]"
                        value="<?= $audit['actual_spare_units'] ?? '' ?>" min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Operator Hadir?</label>
                    <select class="form-select" name="summary[actual_has_operator]">
                        <option value="0" <?= ($audit['actual_has_operator'] ?? 0) == 0 ? 'selected' : '' ?>>TIDAK</option>
                        <option value="1" <?= ($audit['actual_has_operator'] ?? 0) == 1 ? 'selected' : '' ?>>YA</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Selisih Unit</label>
                    <div class="form-control-plaintext">
                        <?php
                        $selisih = ($audit['actual_total_units'] ?? 0) - ($audit['kontrak_total_units'] ?? 0);
                        $selisihClass = $selisih != 0 ? 'text-danger' : 'text-success';
                        ?>
                        <span class="<?= $selisihClass ?> fw-bold">
                            <?= $selisih > 0 ? '+' : '' ?><?= $selisih ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">Catatan Mekanik</label>
                    <textarea class="form-control" name="summary[mechanic_notes]" rows="3"
                        placeholder="Catatan dari hasil audit di lapangan"><?= $audit['mechanic_notes'] ?? '' ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Catatan Admin Service</label>
                    <textarea class="form-control" name="summary[service_notes]" rows="3"
                        placeholder="Catatan tambahan dari admin service"><?= $audit['service_notes'] ?? '' ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between">
        <a href="<?= base_url('service/unit-audit/location') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
        <div class="d-flex gap-2">
            <?php if (in_array($audit['status'] ?? 'DRAFT', ['DRAFT', 'PRINTED'])): ?>
            <button type="button" class="btn btn-warning" onclick="markInProgress()">
                <i class="fas fa-play me-1"></i>Mulai Audit
            </button>
            <?php endif; ?>
            <button type="button" class="btn btn-success" onclick="saveResults()">
                <i class="fas fa-save me-1"></i>Simpan Hasil
            </button>
            <?php if (($audit['status'] ?? '') == 'RESULTS_ENTERED'): ?>
            <button type="button" class="btn btn-primary" onclick="submitToMarketing()">
                <i class="fas fa-paper-plane me-1"></i>Kirim ke Marketing
            </button>
            <?php endif; ?>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const auditId = <?= $audit['id'] ?? 0 ?>;
const auditStatus = '<?= $audit['status'] ?? 'DRAFT' ?>';

function printAuditForm() {
    window.open(`<?= base_url('service/unit-audit/printLocationAudit/') ?>${auditId}`, '_blank');
}

function markInProgress() {
    if (!confirm('Mulai proses audit? Status akan berubah menjadi In Progress.')) return;

    fetch(`<?= base_url('service/unit-audit/markAuditInProgress/') ?>${auditId}`, {
        method: 'POST'
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        location.reload();
    });
}

function saveResults() {
    const form = document.getElementById('auditResultForm');
    const formData = new FormData(form);

    fetch('<?= base_url('service/unit-audit/submitAuditResults') ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    });
}

function submitToMarketing() {
    if (!confirm('Kirim hasil audit ke Marketing untuk approval?')) return;

    fetch(`<?= base_url('service/unit-audit/submitToMarketing/') ?>${auditId}`, {
        method: 'POST'
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            window.location.href = '<?= base_url('service/unit-audit/location') ?>';
        }
    });
}

function checkResult(select) {
    const row = select.closest('tr');
    const result = select.value;

    // Add visual indicator for non-matching results
    if (result !== 'MATCH') {
        row.classList.add('table-danger');
    } else {
        row.classList.remove('table-danger');
    }
}

// Initialize - mark non-matching rows
document.querySelectorAll('.result-select').forEach(select => {
    checkResult(select);
});

// Auto-calculate summary on total units change
document.querySelector('input[name="summary[actual_total_units]"]').addEventListener('change', function() {
    const kontrakTotal = <?= $audit['kontrak_total_units'] ?? 0 ?>;
    const actualTotal = parseInt(this.value) || 0;
    const difference = actualTotal - kontrakTotal;

    const diffDisplay = document.querySelector('.form-control-plaintext .fw-bold');
    diffDisplay.textContent = (difference > 0 ? '+' : '') + difference;
    diffDisplay.className = difference !== 0 ? 'fw-bold text-danger' : 'fw-bold text-success';
});
</script>
<?= $this->endSection() ?>

<?php
function getStatusBadge($status) {
    $badges = [
        'DRAFT' => '<span class="badge bg-secondary">Draft</span>',
        'PRINTED' => '<span class="badge bg-info">Printed</span>',
        'IN_PROGRESS' => '<span class="badge bg-warning">In Progress</span>',
        'RESULTS_ENTERED' => '<span class="badge bg-primary">Results Entered</span>',
        'PENDING_APPROVAL' => '<span class="badge bg-orange">Pending Approval</span>',
        'APPROVED' => '<span class="badge bg-success">Approved</span>',
        'REJECTED' => '<span class="badge bg-danger">Rejected</span>'
    ];
    return $badges[$status] ?? $status;
}
?>
