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
                <li class="breadcrumb-item"><a href="<?= base_url('service/unit-verification') ?>">Unit Verification</a></li>
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
    <div class="col-md-2">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small">Status</div>
                <div class="fw-bold"><?= getStatusBadge($audit['status'] ?? 'DRAFT') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small">No. Kontrak</div>
                <div class="fw-bold font-monospace small"><?= esc($audit['no_kontrak_masked'] ?? '-') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small">Total Unit (Kontrak)</div>
                <div class="fs-4 fw-bold"><?= $audit['kontrak_total_units'] ?? 0 ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small">Spare Unit (Kontrak)</div>
                <div class="fs-4 fw-bold"><?= $audit['kontrak_spare_units'] ?? 0 ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
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
    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
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
                            <th>Pilih Unit<br><small class="text-muted">(Extra/Kurang)</small></th>
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
                            <td class="text-center"><?= ($item['expected_is_spare'] ?? 0) == 1 ? '<span class="badge badge-soft-yellow">YES</span>' : 'NO' ?></td>
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
                            <td class="unit-select-cell">
                                <select class="form-select form-select-sm unit-select-existing" name="items[<?= $item['id'] ?>][unit_id]" data-item-id="<?= $item['id'] ?>" data-selected="<?= (int)($item['unit_id'] ?? 0) ?>">
                                    <option value="">-- Pilih Unit --</option>
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
                                    <option value="ADD_UNIT" <?= ($item['result'] ?? '') == 'ADD_UNIT' ? 'selected' : '' ?>>Tambah Unit (Kurang)</option>
                                </select>
                                <?php
                                // Parse existing notes to pre-fill reason + keterangan
                                $rawNotes     = $item['notes'] ?? '';
                                $existReason  = '';
                                $existKet     = $rawNotes;
                                if ($rawNotes && str_starts_with(trim($rawNotes), '{')) {
                                    $dec = json_decode($rawNotes, true);
                                    if ($dec) {
                                        $existReason = $dec['reasons'][0] ?? '';
                                        $existKet    = $dec['keterangan'] ?? '';
                                    }
                                }
                                $isMismatch = ($item['result'] ?? '') === 'MISMATCH_NO_UNIT';
                                ?>
                                <!-- Reason selector — only visible when MISMATCH_NO_UNIT -->
                                <div class="mismatch-reason-wrap mt-1" style="<?= $isMismatch ? '' : 'display:none' ?>">
                                    <select class="form-select form-select-sm mismatch-reason-select border-warning"
                                        <?= $isMismatch ? 'required' : '' ?>>
                                        <option value="">-- Pilih Alasan --</option>
                                        <option value="UNIT_SWAP"         <?= $existReason === 'UNIT_SWAP'         ? 'selected' : '' ?>>Unit fisik berbeda / diganti</option>
                                        <option value="LOCATION_MISMATCH" <?= $existReason === 'LOCATION_MISMATCH' ? 'selected' : '' ?>>Unit berada di lokasi salah</option>
                                    </select>
                                </div>
                                <!-- Notes input (keterangan) — hidden JSON stored separately -->
                                <input type="text" class="form-control form-control-sm mt-1 notes-keterangan"
                                    value="<?= esc($existKet) ?>"
                                    placeholder="Catatan">
                                <!-- Hidden field carries final notes value (plain text or JSON) -->
                                <input type="hidden" class="notes-json-field"
                                    name="items[<?= $item['id'] ?>][notes]"
                                    value="<?= esc($rawNotes) ?>">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr id="addUnitRowTemplate" class="d-none">
                            <td class="text-center add-unit-no"></td>
                            <td colspan="4" class="text-muted small">Unit baru (tambah ke kontrak)</td>
                            <td><input type="text" class="form-control form-control-sm add-unit-actual-no" placeholder="No Unit"></td>
                            <td><input type="text" class="form-control form-control-sm add-unit-actual-serial" placeholder="Serial"></td>
                            <td><input type="text" class="form-control form-control-sm add-unit-actual-merk" placeholder="Merk/Model"></td>
                            <td><select class="form-select form-select-sm add-unit-actual-spare"><option value="0">NO</option><option value="1">YES</option></select></td>
                            <td></td>
                            <td>
                                <select class="form-select form-select-sm unit-select-add" required>
                                    <option value="">-- Pilih Unit --</option>
                                </select>
                                <input type="text" class="form-control form-control-sm mt-1 add-unit-notes" placeholder="Keterangan">
                            </td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeAddUnitRow(this)"><i class="fas fa-times"></i></button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-outline-success" onclick="addUnitRow()">
                    <i class="fas fa-plus me-1"></i>Tambah Unit (Kurang)
                </button>
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
                <div class="col-md-4">
                    <label class="form-label">Nama Mekanik (Yang Mengecek)</label>
                    <input type="text" class="form-control" name="summary[mechanic_name]"
                        value="<?= esc($audit['mechanic_name'] ?? '') ?>"
                        placeholder="Nama mekanik yang melakukan pengecekan di lapangan">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Catatan Mekanik</label>
                    <textarea class="form-control" name="summary[mechanic_notes]" rows="2"
                        placeholder="Catatan dari hasil audit di lapangan"><?= esc($audit['mechanic_notes'] ?? '') ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Catatan Admin Service</label>
                    <textarea class="form-control" name="summary[service_notes]" rows="2"
                        placeholder="Catatan tambahan dari admin service"><?= esc($audit['service_notes'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between">
        <a href="<?= base_url('service/unit-verification') ?>" class="btn btn-secondary">
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

<?= $this->section('javascript') ?>
<script>
const auditId = <?= $audit['id'] ?? 0 ?>;
const auditStatus = '<?= $audit['status'] ?? 'DRAFT' ?>';
let addUnitRowCounter = 0;
let availableUnits = [];

// Load available units for dropdowns
fetch('<?= base_url('unit_audit/getAvailableUnits') ?>')
    .then(res => res.json())
    .then(data => {
        if (data.success && data.data) {
            availableUnits = data.data;
            renderUnitOptions(document.querySelectorAll('.unit-select-existing'));
            const templateSelect = document.querySelector('#addUnitRowTemplate .unit-select-add');
            if (templateSelect) renderUnitSelectOptions(templateSelect);
        }
    });

function renderUnitSelectOptions(selectEl) {
    if (!selectEl || !availableUnits.length) return;
    const firstOpt = selectEl.querySelector('option');
    selectEl.innerHTML = firstOpt ? firstOpt.outerHTML : '<option value="">-- Pilih Unit --</option>';
    availableUnits.forEach(u => {
        const opt = document.createElement('option');
        opt.value = u.id_inventory_unit || u.id;
        opt.textContent = (u.no_unit || u.nomor_unit || '-') + ' | ' + (u.serial_number || '-') + ' | ' + (u.merk_unit || '') + ' ' + (u.model_unit || '');
        selectEl.appendChild(opt);
    });
}

function renderUnitOptions(selects) {
    selects.forEach(s => {
        renderUnitSelectOptions(s);
        const selected = s.dataset.selected;
        if (selected && selected !== '0') s.value = selected;
    });
}

function addUnitRow() {
    const template = document.getElementById('addUnitRowTemplate');
    const clone = template.cloneNode(true);
    clone.id = '';
    clone.classList.remove('d-none');
    const idx = addUnitRowCounter++;
    const prefix = 'items[new_' + idx + ']';
    const existingRows = document.querySelectorAll('#itemsTable tbody tr:not(#addUnitRowTemplate):not(.add-unit-row)');
    clone.classList.add('add-unit-row');
    clone.querySelector('.add-unit-no').textContent = existingRows.length + 1;
    clone.querySelector('.add-unit-actual-no').name = prefix + '[actual_no_unit]';
    clone.querySelector('.add-unit-actual-serial').name = prefix + '[actual_serial]';
    clone.querySelector('.add-unit-actual-merk').name = prefix + '[actual_merk]';
    clone.querySelector('.add-unit-actual-spare').name = prefix + '[actual_is_spare]';
    clone.querySelector('.unit-select-add').name = prefix + '[unit_id]';
    clone.querySelector('.add-unit-notes').name = prefix + '[notes]';
    const hiddenResult = document.createElement('input');
    hiddenResult.type = 'hidden';
    hiddenResult.name = prefix + '[result]';
    hiddenResult.value = 'ADD_UNIT';
    clone.querySelector('td:nth-child(11)').insertBefore(hiddenResult, clone.querySelector('.unit-select-add'));
    renderUnitSelectOptions(clone.querySelector('.unit-select-add'));
    template.parentNode.insertBefore(clone, template);
}

function removeAddUnitRow(btn) {
    btn.closest('tr').remove();
}

function printAuditForm() {
    window.open(`<?= base_url('service/unit-verification/print/') ?>${auditId}`, '_blank');
}

function markInProgress() {
    OptimaConfirm.generic({
        title: 'Mulai Audit?',
        text: 'Status akan berubah menjadi In Progress.',
        icon: 'question',
        confirmText: 'Ya, Mulai!',
        cancelText: window.lang('cancel'),
        confirmButtonColor: 'primary',
        onConfirm: function() {
            fetch(`<?= base_url('service/unit-audit/markAuditInProgress/') ?>${auditId}`, {
                method: 'POST'
            })
            .then(res => res.json())
            .then(data => {
                OptimaNotify.success(data.message);
                location.reload();
            });
        }
    });
}

function saveResults() {
    // Sync all hidden notes JSON fields before submitting
    document.querySelectorAll('.result-select').forEach(select => {
        syncNotesJson(select.closest('tr'));
    });

    // Validate: MISMATCH_NO_UNIT must have a reason selected
    let missingReason = false;
    document.querySelectorAll('.result-select').forEach(select => {
        if (select.value === 'MISMATCH_NO_UNIT') {
            const row = select.closest('tr');
            const reasonSel = row.querySelector('.mismatch-reason-select');
            if (reasonSel && !reasonSel.value) {
                missingReason = true;
                reasonSel.classList.add('is-invalid');
            }
        }
    });
    if (missingReason) {
        if (window.OptimaNotify) OptimaNotify.error('Pilih alasan untuk setiap baris "No Unit Berbeda" sebelum menyimpan.');
        else alert('Pilih alasan untuk setiap baris "No Unit Berbeda" sebelum menyimpan.');
        return;
    }

    const form = document.getElementById('auditResultForm');
    const formData = new FormData(form);

    fetch('<?= base_url('service/unit-audit/submitAuditResults') ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            OptimaNotify.success(data.message);
            location.reload();
        } else {
            OptimaNotify.error(data.message);
        }
    });
}

function submitToMarketing() {
    OptimaConfirm.generic({
        title: 'Kirim ke Marketing?',
        text: 'Hasil audit akan dikirim ke Marketing untuk approval.',
        icon: 'question',
        confirmText: 'Ya, Kirim!',
        cancelText: window.lang('cancel'),
        confirmButtonColor: 'primary',
        onConfirm: function() {
            const formData = new FormData();
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            fetch(`<?= base_url('service/unit-audit/submitToMarketing/') ?>${auditId}`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    OptimaNotify.success(data.message);
                    window.location.href = '<?= base_url('service/unit-verification') ?>';
                } else {
                    OptimaNotify.error(data.message);
                }
            });
        }
    });
}

function checkResult(select) {
    const row = select.closest('tr');
    const result = select.value;
    const unitCell   = row.querySelector('.unit-select-cell');
    const reasonWrap = row.querySelector('.mismatch-reason-wrap');
    const reasonSel  = row.querySelector('.mismatch-reason-select');

    if (result !== 'MATCH') {
        row.classList.add('table-danger');
    } else {
        row.classList.remove('table-danger');
    }

    // Show/require reason selector only for MISMATCH_NO_UNIT
    if (reasonWrap) {
        const show = result === 'MISMATCH_NO_UNIT';
        reasonWrap.style.display = show ? '' : 'none';
        if (reasonSel) {
            reasonSel.required = show;
            if (!show) reasonSel.value = ''; // clear when hidden
        }
        // Sync notes JSON when result changes
        syncNotesJson(row);
    }

    if (unitCell) {
        unitCell.style.visibility = (result === 'EXTRA_UNIT' || result === 'ADD_UNIT') ? 'visible' : 'hidden';
        const unitSelect = unitCell.querySelector('.unit-select-existing');
        if (unitSelect) unitSelect.required = (result === 'EXTRA_UNIT' || result === 'ADD_UNIT');
    }
}

/**
 * Sync the hidden notes JSON field from reason select + keterangan input.
 * - MISMATCH_NO_UNIT: encode as JSON {"reasons":["REASON"],"keterangan":"...","extra":{}}
 * - Other results: store keterangan as plain text
 */
function syncNotesJson(row) {
    const resultSel  = row.querySelector('.result-select');
    const reasonSel  = row.querySelector('.mismatch-reason-select');
    const ketInput   = row.querySelector('.notes-keterangan');
    const hiddenNote = row.querySelector('.notes-json-field');
    if (!hiddenNote) return;

    const result = resultSel ? resultSel.value : '';
    const ket    = ketInput  ? ketInput.value.trim() : '';

    if (result === 'MISMATCH_NO_UNIT' && reasonSel) {
        const reason = reasonSel.value;
        hiddenNote.value = JSON.stringify({
            reasons:    reason ? [reason] : [],
            keterangan: ket,
            extra:      {}
        });
    } else {
        hiddenNote.value = ket;
    }
}

// Initialize - mark non-matching rows, toggle unit select, attach sync listeners
document.querySelectorAll('.result-select').forEach(select => {
    checkResult(select);
    const row = select.closest('tr');
    // Attach sync on reason/keterangan change
    const reasonSel = row.querySelector('.mismatch-reason-select');
    const ketInput  = row.querySelector('.notes-keterangan');
    if (reasonSel) reasonSel.addEventListener('change', () => syncNotesJson(row));
    if (ketInput)  ketInput.addEventListener('input',   () => syncNotesJson(row));
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
        'DRAFT' => '<span class="badge badge-soft-gray">Draft</span>',
        'PRINTED' => '<span class="badge badge-soft-cyan">Printed</span>',
        'IN_PROGRESS' => '<span class="badge badge-soft-yellow">In Progress</span>',
        'RESULTS_ENTERED' => '<span class="badge badge-soft-blue">Results Entered</span>',
        'PENDING_APPROVAL' => '<span class="badge badge-soft-orange">Pending Approval</span>',
        'APPROVED' => '<span class="badge badge-soft-green">Approved</span>',
        'REJECTED' => '<span class="badge badge-soft-red">Rejected</span>'
    ];
    return $badges[$status] ?? $status;
}
?>
