<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Returned Verifications</h4>
            <p class="text-muted mb-0">Antrean unit dengan status RETURNED yang menunggu verifikasi Warehouse</p>
        </div>
        <span class="badge badge-soft-primary px-3 py-2">Queue: <?= count($units ?? []) ?> Unit</span>
    </div>

    <div class="card table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Unit</th>
                            <th>Serial</th>
                            <th>Type</th>
                            <th>Model</th>
                            <th>Workflow</th>
                            <th>Last Update</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($units)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Tidak ada unit pada antrean RETURNED.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($units as $unit): ?>
                            <tr>
                                <td class="fw-semibold"><?= esc($unit['no_unit'] ?: ('ID-' . $unit['id_inventory_unit'])) ?></td>
                                <td><?= esc($unit['serial_number'] ?: '-') ?></td>
                                <td><?= esc($unit['tipe_unit'] ?: '-') ?></td>
                                <td><?= esc(trim(($unit['merk_unit'] ?? '') . ' ' . ($unit['model_unit'] ?? '')) ?: '-') ?></td>
                                <td><span class="badge badge-soft-warning"><?= esc($unit['workflow_status'] ?: '-') ?></span></td>
                                <td><?= !empty($unit['updated_at']) ? esc(date('d/m/Y H:i', strtotime($unit['updated_at']))) : '-' ?></td>
                                <td class="text-end">
                                    <button
                                        class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#verifyReturnedModal"
                                        data-unit-id="<?= (int) $unit['id_inventory_unit'] ?>"
                                        data-unit-no="<?= esc($unit['no_unit'] ?: ('ID-' . $unit['id_inventory_unit'])) ?>"
                                    >
                                        Verify
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="verifyReturnedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="post" action="<?= base_url('/warehouse/returned-verifications/verify') ?>" id="rv-verify-form">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Verify Returned Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="unit_id" id="rv-unit-id">

                    <div class="mb-3">
                        <label class="form-label">Unit</label>
                        <input type="text" class="form-control" id="rv-unit-no" readonly>
                    </div>

                    <div id="rv-detail-loading" class="text-muted small mb-2 d-none">Loading detail unit...</div>

                    <div class="card mb-3">
                        <div class="card-header py-2"><strong>Detail Unit (Database)</strong></div>
                        <div class="card-body py-2">
                            <div class="row g-2 small">
                                <div class="col-md-6"><strong>Serial:</strong> <span id="rv-serial">-</span></div>
                                <div class="col-md-6"><strong>Tahun:</strong> <span id="rv-year">-</span></div>
                                <div class="col-md-6"><strong>Tipe:</strong> <span id="rv-type">-</span></div>
                                <div class="col-md-6"><strong>Model:</strong> <span id="rv-model">-</span></div>
                                <div class="col-md-6"><strong>Departemen:</strong> <span id="rv-dept">-</span></div>
                                <div class="col-md-6"><strong>Kapasitas:</strong> <span id="rv-capacity">-</span></div>
                                <div class="col-md-6"><strong>SN Mesin:</strong> <span id="rv-sn-engine">-</span></div>
                                <div class="col-md-6"><strong>SN Mast:</strong> <span id="rv-sn-mast">-</span></div>
                                <div class="col-md-6"><strong>Model Mast:</strong> <span id="rv-model-mast">-</span></div>
                                <div class="col-md-6"><strong>Tinggi Mast:</strong> <span id="rv-mast-height">-</span></div>
                                <div class="col-md-6"><strong>Hour Meter:</strong> <span id="rv-hm">-</span></div>
                                <div class="col-12"><strong>Keterangan:</strong> <span id="rv-notes">-</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header py-2"><strong>Komponen Terpasang</strong></div>
                        <div class="card-body py-2">
                            <div class="small mb-2">
                                <strong>Attachment:</strong> <span id="rv-attachment">-</span>
                            </div>
                            <div class="small mb-2">
                                <strong>Battery:</strong> <span id="rv-battery">-</span>
                            </div>
                            <div class="small mb-0">
                                <strong>Charger:</strong> <span id="rv-charger">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kesesuaian Data Fisik vs Database <span class="text-danger">*</span></label>
                        <select class="form-select" name="verification_result" id="rv-verification-result" required>
                            <option value="sesuai">Sesuai</option>
                            <option value="tidak_sesuai">Tidak Sesuai (Perlu Koreksi Database)</option>
                        </select>
                    </div>

                    <div class="card mb-3 d-none" id="rv-correction-card">
                        <div class="card-header py-2"><strong>Koreksi Data Unit (Akan update database)</strong></div>
                        <div class="card-body py-2">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Serial Number</label>
                                    <input type="text" class="form-control rv-correction-field" name="serial_number" id="rv-edit-serial" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tahun Unit</label>
                                    <input type="number" min="1900" max="2100" class="form-control rv-correction-field" name="tahun_unit" id="rv-edit-year" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Departemen</label>
                                    <select class="form-select rv-correction-field" name="departemen_id" id="rv-edit-dept" disabled></select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tipe Unit</label>
                                    <select class="form-select rv-correction-field" name="tipe_unit_id" id="rv-edit-type" disabled></select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Model Unit</label>
                                    <select class="form-select rv-correction-field" name="model_unit_id" id="rv-edit-model" disabled></select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Kapasitas</label>
                                    <select class="form-select rv-correction-field" name="kapasitas_unit_id" id="rv-edit-capacity" disabled></select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">SN Mesin</label>
                                    <input type="text" class="form-control rv-correction-field" name="sn_mesin" id="rv-edit-sn-engine" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">SN Mast</label>
                                    <input type="text" class="form-control rv-correction-field" name="sn_mast" id="rv-edit-sn-mast" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Model Mast</label>
                                    <select class="form-select" id="rv-edit-model-mast-name" disabled></select>
                                    <input type="hidden" class="rv-correction-field" name="model_mast_id" id="rv-edit-model-mast" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tinggi Mast</label>
                                    <select class="form-select rv-correction-field" name="tinggi_mast" id="rv-edit-mast-height" disabled></select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Hour Meter</label>
                                    <input type="number" step="0.1" min="0" class="form-control rv-correction-field" name="hour_meter" id="rv-edit-hm" disabled>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Keterangan</label>
                                    <textarea class="form-control rv-correction-field" rows="2" name="keterangan" id="rv-edit-notes" disabled></textarea>
                                </div>
                            </div>
                            <div class="form-text mt-2">Isi kolom yang memang perlu dikoreksi. Perubahan akan dicatat ke timeline unit.</div>
                        </div>
                    </div>

                    <div class="card mb-3 d-none" id="rv-component-card">
                        <div class="card-header py-2"><strong>Verifikasi Komponen (Attachment/Battery/Charger)</strong></div>
                        <div class="card-body py-2">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Attachment</label>
                                    <select class="form-select rv-correction-field" name="attachment_inventory_id" id="rv-edit-attachment" disabled></select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">SN Attachment</label>
                                    <input type="text" class="form-control rv-correction-field" name="sn_attachment" id="rv-edit-sn-attachment" disabled>
                                </div>

                                <div class="col-md-6 rv-electric-only d-none">
                                    <label class="form-label">Battery</label>
                                    <select class="form-select rv-correction-field" name="battery_inventory_id" id="rv-edit-battery" disabled></select>
                                </div>
                                <div class="col-md-6 rv-electric-only d-none">
                                    <label class="form-label">SN Battery</label>
                                    <input type="text" class="form-control rv-correction-field" name="sn_baterai" id="rv-edit-sn-battery" disabled>
                                </div>

                                <div class="col-md-6 rv-electric-only d-none">
                                    <label class="form-label">Charger</label>
                                    <select class="form-select rv-correction-field" name="charger_inventory_id" id="rv-edit-charger" disabled></select>
                                </div>
                                <div class="col-md-6 rv-electric-only d-none">
                                    <label class="form-label">SN Charger</label>
                                    <input type="text" class="form-control rv-correction-field" name="sn_charger" id="rv-edit-sn-charger" disabled>
                                </div>
                            </div>
                            <div class="form-text mt-2">Untuk unit electric, battery dan charger wajib diverifikasi.</div>
                        </div>
                    </div>

                    <div class="alert alert-warning mb-3 d-none" id="rv-change-summary"></div>

                    <div class="card mb-3">
                        <div class="card-header py-2"><strong>Checklist Verifikasi</strong></div>
                        <div class="card-body py-2">
                            <div class="form-check mb-2">
                                <input class="form-check-input rv-check" type="checkbox" id="rv-check-unit" required>
                                <label class="form-check-label" for="rv-check-unit">Data unit fisik sudah dicek dan sesuai</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input rv-check" type="checkbox" id="rv-check-components" required>
                                <label class="form-check-label" for="rv-check-components">Komponen (attachment/battery/charger) sudah diverifikasi</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input rv-check" type="checkbox" id="rv-check-sn" required>
                                <label class="form-check-label" for="rv-check-sn">SN utama (unit/mesin/mast) sudah dipastikan benar</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Hasil Verifikasi <span class="text-danger">*</span></label>
                        <select class="form-select" name="target_status" required>
                            <option value="">Pilih status akhir</option>
                            <?php foreach (($targetStatuses ?? []) as $id => $label): ?>
                                <option value="<?= (int) $id ?>"><?= esc($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Opsional"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Verification</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
<?php $rvSuccessToast = session()->getFlashdata('success'); ?>
<?php $rvErrorToast = session()->getFlashdata('error'); ?>
document.addEventListener('DOMContentLoaded', function () {
    <?php if (!empty($rvSuccessToast)): ?>
    if (window.OptimaNotify && typeof window.OptimaNotify.success === 'function') {
        window.OptimaNotify.success(<?= json_encode((string) $rvSuccessToast) ?>);
    }
    <?php endif; ?>
    <?php if (!empty($rvErrorToast)): ?>
    if (window.OptimaNotify && typeof window.OptimaNotify.error === 'function') {
        window.OptimaNotify.error(<?= json_encode((string) $rvErrorToast) ?>);
    }
    <?php endif; ?>
});

const rvModal = document.getElementById('verifyReturnedModal');
const rvResultSelect = document.getElementById('rv-verification-result');
const rvCorrectionCard = document.getElementById('rv-correction-card');
const rvComponentCard = document.getElementById('rv-component-card');
const rvCorrectionFields = () => document.querySelectorAll('.rv-correction-field');
const rvChangeSummary = document.getElementById('rv-change-summary');
const rvOriginalValues = {};
const rvTipeOptionsCache = [];
const rvMastOptionsCache = [];
const rvComponentMap = {
    attachment: {},
    battery: {},
    charger: {}
};

function normalizeValue(value) {
    if (value === null || value === undefined) return '';
    return String(value).trim();
}

function setSelectOptions(selectEl, options, selectedValue) {
    if (!selectEl) return;
    const selected = selectedValue === null || selectedValue === undefined ? '' : String(selectedValue);
    const html = ['<option value="">- Pilih -</option>'].concat(
        (options || []).map((opt) => {
            const id = String(opt.id ?? '');
            const name = String(opt.name ?? '');
            const isSelected = id === selected ? ' selected' : '';
            return `<option value="${id}"${isSelected}>${name}</option>`;
        })
    );
    selectEl.innerHTML = html.join('');
}

function initSelect2IfNeeded(selector, placeholder) {
    const el = $(selector);
    if (!el.length) return;
    if (el.hasClass('select2-hidden-accessible')) return;
    el.select2({
        width: '100%',
        placeholder: placeholder || '- Pilih -',
        allowClear: true,
        dropdownParent: $('#verifyReturnedModal')
    });
}

function initComponentSelect2(selector, type, selectedId) {
    const el = $(selector);
    if (!el.length) return;
    if (el.hasClass('select2-hidden-accessible')) {
        el.select2('destroy');
    }
    el.empty().append('<option value="">- Pilih -</option>');

    el.select2({
        width: '100%',
        placeholder: 'Pilih komponen...',
        allowClear: true,
        dropdownParent: $('#verifyReturnedModal'),
        escapeMarkup: function(markup) { return markup; },
        minimumInputLength: 0,
        ajax: {
            url: `<?= base_url('service/data-attachment/simple') ?>`,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term || '', type: type };
            },
            processResults: function (data) {
                if (!data.success || !data.data) return { results: [] };
                const seen = new Set();
                const results = [];
                data.data.forEach(function(item) {
                    const id = String(item.id || '');
                    if (!id || seen.has(id)) return;
                    seen.add(id);

                    const sn = item.sn_attachment || item.sn_baterai || item.sn_charger || `NO-SN#${id}`;
                    const statusRaw = (item.attachment_status || '').toUpperCase();
                    const status = statusRaw || '-';
                    const owner = item.installed_unit?.no_unit ? ` | Unit: ${item.installed_unit.no_unit}` : '';
                    const text = `${sn} - ${item.label || '-'}${owner}`;
                    results.push({
                        id: item.id,
                        text: text,
                        status: status,
                        raw: item
                    });
                });
                return { results: results };
            },
            cache: false
        },
        templateResult: function (state) {
            if (!state.id) return state.text;
            const status = (state.status || state.element?.dataset?.status || '').toUpperCase();
            const badgeClass = status === 'AVAILABLE'
                ? 'bg-success'
                : (status === 'IN_USE' ? 'bg-warning text-dark' : 'bg-danger');
            return `<div class="d-flex justify-content-between align-items-center">
                <span>${state.text}</span>
                <span class="badge ${badgeClass} ms-2">${status || '-'}</span>
            </div>`;
        },
        templateSelection: function (state) {
            return state.text || '';
        }
    }).on('select2:select', function (e) {
        const raw = e.params?.data?.raw || {};
        if (type === 'attachment') {
            document.getElementById('rv-edit-sn-attachment').value = raw.sn_attachment || '';
        } else if (type === 'battery') {
            document.getElementById('rv-edit-sn-battery').value = raw.sn_baterai || '';
        } else if (type === 'charger') {
            document.getElementById('rv-edit-sn-charger').value = raw.sn_charger || '';
        }
        updateChangeHighlights();
    });
}

function repopulateTypeByDepartment(selectedTypeId) {
    const deptId = document.getElementById('rv-edit-dept').value || '';
    const typeEl = document.getElementById('rv-edit-type');
    const allowed = rvTipeOptionsCache.filter((row) => String(row.id_departemen || '') === String(deptId || ''));
    const html = ['<option value="">- Pilih -</option>'].concat(allowed.map((row) => {
        const id = String(row.id || '');
        const jenis = row.jenis ? ` - ${row.jenis}` : '';
        const selected = String(selectedTypeId || '') === id ? ' selected' : '';
        return `<option value="${id}"${selected}>${row.name || '-'}${jenis}</option>`;
    }));
    typeEl.innerHTML = html.join('');
    $(typeEl).trigger('change.select2');
}

function toggleElectricOnlyFields() {
    const deptText = ($('#rv-edit-dept option:selected').text() || '').toUpperCase();
    const isElectric = deptText.includes('ELECTRIC');
    document.querySelectorAll('.rv-electric-only').forEach((el) => {
        el.classList.toggle('d-none', !isElectric);
    });
    if (!isElectric) {
        $('#rv-edit-battery').val('').trigger('change');
        $('#rv-edit-charger').val('').trigger('change');
        document.getElementById('rv-edit-sn-battery').value = '';
        document.getElementById('rv-edit-sn-charger').value = '';
    }
}

function populateMastModelOptions(selectedModelName) {
    const modelEl = document.getElementById('rv-edit-model-mast-name');
    if (!modelEl) return;
    const unique = [];
    const seen = new Set();
    rvMastOptionsCache.forEach((row) => {
        const modelName = String(row.model_name || '').trim();
        if (!modelName || seen.has(modelName)) return;
        seen.add(modelName);
        unique.push(modelName);
    });

    const selected = String(selectedModelName || '');
    const html = ['<option value="">- Pilih -</option>'].concat(unique.map((name) => {
        const isSelected = name === selected ? ' selected' : '';
        return `<option value="${name}"${isSelected}>${name}</option>`;
    }));
    modelEl.innerHTML = html.join('');
}

function populateMastHeightOptions(modelName, selectedHeight, selectedMastId) {
    const heightEl = document.getElementById('rv-edit-mast-height');
    const mastIdEl = document.getElementById('rv-edit-model-mast');
    if (!heightEl || !mastIdEl) return;

    const rows = rvMastOptionsCache.filter((row) => String(row.model_name || '') === String(modelName || ''));
    const selectedH = String(selectedHeight || '');
    const selectedId = String(selectedMastId || '');

    const html = ['<option value="">- Pilih -</option>'].concat(rows.map((row) => {
        const h = String(row.height || '');
        const id = String(row.id || '');
        const isSelected = (selectedId !== '' && id === selectedId) || (selectedId === '' && selectedH !== '' && h === selectedH);
        return `<option value="${h}" data-mast-id="${id}"${isSelected ? ' selected' : ''}>${h || '-'}</option>`;
    }));
    heightEl.innerHTML = html.join('');

    const selectedOption = heightEl.options[heightEl.selectedIndex];
    mastIdEl.value = selectedOption?.dataset?.mastId || '';
}

function applyCorrectionMode() {
    const mode = rvResultSelect.value;
    const enable = mode === 'tidak_sesuai';
    rvCorrectionCard.classList.toggle('d-none', !enable);
    rvComponentCard.classList.toggle('d-none', !enable);
    const mastModelUi = document.getElementById('rv-edit-model-mast-name');
    if (mastModelUi) mastModelUi.disabled = !enable;
    rvCorrectionFields().forEach((el) => {
        el.disabled = !enable;
    });
    if (!enable) {
        rvChangeSummary.classList.add('d-none');
        rvChangeSummary.textContent = '';
        rvCorrectionFields().forEach((el) => {
            el.classList.remove('border-warning', 'bg-warning-subtle');
        });
    } else {
        updateChangeHighlights();
    }
}

function updateChangeHighlights() {
    const changed = [];
    rvCorrectionFields().forEach((el) => {
        const key = el.id;
        const oldVal = normalizeValue(rvOriginalValues[key] ?? '');
        const newVal = normalizeValue(el.value);
        const isChanged = oldVal !== newVal;
        el.classList.toggle('border-warning', isChanged);
        el.classList.toggle('bg-warning-subtle', isChanged);
        if (isChanged) {
            const label = el.closest('.col-md-6, .col-12')?.querySelector('label')?.textContent?.trim() || key;
            changed.push(`${label}: "${oldVal || '-'}" -> "${newVal || '-'}"`);
        }
    });

    if (changed.length > 0) {
        rvChangeSummary.classList.remove('d-none');
        rvChangeSummary.innerHTML = `<strong>${changed.length} perubahan terdeteksi:</strong><br>${changed.join('<br>')}`;
    } else {
        rvChangeSummary.classList.add('d-none');
        rvChangeSummary.textContent = '';
    }
}

rvResultSelect.addEventListener('change', applyCorrectionMode);
rvCorrectionFields().forEach((el) => {
    el.addEventListener('input', updateChangeHighlights);
    el.addEventListener('change', updateChangeHighlights);
});

rvModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const unitId = button.getAttribute('data-unit-id') || '';
    const unitNo = button.getAttribute('data-unit-no') || '';
    document.getElementById('rv-unit-id').value = unitId;
    document.getElementById('rv-unit-no').value = unitNo;
    const loading = document.getElementById('rv-detail-loading');
    loading.classList.remove('d-none');

    fetch(`<?= base_url('/warehouse/returned-verifications/detail') ?>/${unitId}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(r => r.json())
        .then(json => {
            if (!json.success) throw new Error(json.message || 'Gagal memuat detail unit');
            const unit = json.data?.unit || {};
            const attachment = json.data?.attachment || null;
            const battery = json.data?.battery || null;
            const charger = json.data?.charger || null;

            document.getElementById('rv-serial').textContent = unit.serial_number || '-';
            document.getElementById('rv-year').textContent = unit.tahun_unit || '-';
            document.getElementById('rv-type').textContent = unit.tipe_unit || '-';
            document.getElementById('rv-model').textContent = `${unit.merk_unit || ''} ${unit.model_unit || ''}`.trim() || '-';
            document.getElementById('rv-dept').textContent = unit.nama_departemen || '-';
            document.getElementById('rv-capacity').textContent = unit.kapasitas_unit || '-';
            document.getElementById('rv-sn-engine').textContent = unit.sn_mesin || '-';
            document.getElementById('rv-sn-mast').textContent = unit.sn_mast || '-';
            document.getElementById('rv-model-mast').textContent = unit.model_mast_name || '-';
            document.getElementById('rv-mast-height').textContent = unit.tinggi_mast || '-';
            document.getElementById('rv-hm').textContent = unit.hour_meter || '-';
            document.getElementById('rv-notes').textContent = unit.keterangan || '-';

            document.getElementById('rv-edit-serial').value = unit.serial_number || '';
            document.getElementById('rv-edit-year').value = unit.tahun_unit || '';
            document.getElementById('rv-edit-sn-engine').value = unit.sn_mesin || '';
            document.getElementById('rv-edit-sn-mast').value = unit.sn_mast || '';
            document.getElementById('rv-edit-model-mast').value = unit.model_mast_id || '';
            document.getElementById('rv-edit-hm').value = unit.hour_meter || '';
            document.getElementById('rv-edit-notes').value = unit.keterangan || '';

            const options = json.data?.options || {};
            const componentOptions = json.data?.component_options || {};
            rvTipeOptionsCache.length = 0;
            (options.tipe_unit || []).forEach((row) => rvTipeOptionsCache.push(row));

            setSelectOptions(document.getElementById('rv-edit-dept'), options.departemen || [], unit.departemen_id);
            repopulateTypeByDepartment(unit.tipe_unit_id);
            setSelectOptions(document.getElementById('rv-edit-model'), options.model_unit || [], unit.model_unit_id);
            setSelectOptions(document.getElementById('rv-edit-capacity'), options.kapasitas || [], unit.kapasitas_unit_id);
            rvMastOptionsCache.length = 0;
            (options.model_mast || []).forEach((row) => rvMastOptionsCache.push(row));
            populateMastModelOptions(unit.model_mast_name || '');
            populateMastHeightOptions(unit.model_mast_name || '', unit.tinggi_mast || '', unit.model_mast_id || '');

            initSelect2IfNeeded('#rv-edit-dept', 'Pilih Departemen');
            initSelect2IfNeeded('#rv-edit-type', 'Pilih Tipe Unit');
            initSelect2IfNeeded('#rv-edit-model', 'Pilih Model Unit');
            initSelect2IfNeeded('#rv-edit-capacity', 'Pilih Kapasitas');
            rvComponentMap.attachment = Object.fromEntries((componentOptions.attachments || []).map((x) => [String(x.id), x]));
            rvComponentMap.battery = Object.fromEntries((componentOptions.batteries || []).map((x) => [String(x.id), x]));
            rvComponentMap.charger = Object.fromEntries((componentOptions.chargers || []).map((x) => [String(x.id), x]));
            initComponentSelect2('#rv-edit-attachment', 'attachment', attachment?.id || null);
            initComponentSelect2('#rv-edit-battery', 'battery', battery?.id || null);
            initComponentSelect2('#rv-edit-charger', 'charger', charger?.id || null);

            rvCorrectionFields().forEach((el) => {
                rvOriginalValues[el.id] = el.value;
            });
            document.getElementById('rv-edit-sn-attachment').value = attachment?.serial_number || '';
            document.getElementById('rv-edit-sn-battery').value = battery?.serial_number || '';
            document.getElementById('rv-edit-sn-charger').value = charger?.serial_number || '';
            rvOriginalValues['rv-edit-sn-attachment'] = document.getElementById('rv-edit-sn-attachment').value;
            rvOriginalValues['rv-edit-sn-battery'] = document.getElementById('rv-edit-sn-battery').value;
            rvOriginalValues['rv-edit-sn-charger'] = document.getElementById('rv-edit-sn-charger').value;
            rvOriginalValues['rv-edit-attachment'] = String(attachment?.id || '');
            rvOriginalValues['rv-edit-battery'] = String(battery?.id || '');
            rvOriginalValues['rv-edit-charger'] = String(charger?.id || '');
            toggleElectricOnlyFields();
            updateChangeHighlights();

            document.getElementById('rv-attachment').textContent = attachment
                ? `${attachment.tipe || ''} ${attachment.merk || ''} ${attachment.model || ''} | SN: ${attachment.serial_number || '-'}`
                : '-';
            document.getElementById('rv-battery').textContent = battery
                ? `${battery.merk_baterai || ''} ${battery.tipe_baterai || ''} | SN: ${battery.serial_number || '-'}`
                : '-';
            document.getElementById('rv-charger').textContent = charger
                ? `${charger.merk_charger || ''} ${charger.tipe_charger || ''} | SN: ${charger.serial_number || '-'}`
                : '-';
        })
        .catch(() => {
            document.getElementById('rv-serial').textContent = '-';
            document.getElementById('rv-year').textContent = '-';
            document.getElementById('rv-type').textContent = '-';
            document.getElementById('rv-model').textContent = '-';
            document.getElementById('rv-dept').textContent = '-';
            document.getElementById('rv-capacity').textContent = '-';
            document.getElementById('rv-sn-engine').textContent = '-';
            document.getElementById('rv-sn-mast').textContent = '-';
            document.getElementById('rv-model-mast').textContent = '-';
            document.getElementById('rv-mast-height').textContent = '-';
            document.getElementById('rv-hm').textContent = '-';
            document.getElementById('rv-notes').textContent = '-';
            document.getElementById('rv-attachment').textContent = '-';
            document.getElementById('rv-battery').textContent = '-';
            document.getElementById('rv-charger').textContent = '-';
        })
        .finally(() => {
            loading.classList.add('d-none');
            applyCorrectionMode();
        });
});

rvModal.addEventListener('hidden.bs.modal', function () {
    document.querySelectorAll('.rv-check').forEach((el) => {
        el.checked = false;
    });
    rvResultSelect.value = 'sesuai';
    applyCorrectionMode();
    document.getElementById('rv-verify-form').reset();
    Object.keys(rvOriginalValues).forEach((key) => delete rvOriginalValues[key]);
});

$(document).on('change', '#rv-edit-dept', function() {
    const selectedType = $('#rv-edit-type').val() || '';
    repopulateTypeByDepartment(selectedType);
    toggleElectricOnlyFields();
    updateChangeHighlights();
});

$(document).on('change', '#rv-edit-model-mast-name', function() {
    populateMastHeightOptions(this.value || '', '', '');
    updateChangeHighlights();
});

$(document).on('change', '#rv-edit-mast-height', function() {
    const selected = this.options[this.selectedIndex];
    document.getElementById('rv-edit-model-mast').value = selected?.dataset?.mastId || '';
    updateChangeHighlights();
});

$(document).on('change', '#rv-edit-type, #rv-edit-model, #rv-edit-capacity, #rv-edit-attachment, #rv-edit-battery, #rv-edit-charger', function() {
    const id = this.id;
    const value = String(this.value || '');
    if (id === 'rv-edit-attachment') {
        document.getElementById('rv-edit-sn-attachment').value = rvComponentMap.attachment[value]?.serial_number || '';
    } else if (id === 'rv-edit-battery') {
        document.getElementById('rv-edit-sn-battery').value = rvComponentMap.battery[value]?.serial_number || '';
    } else if (id === 'rv-edit-charger') {
        document.getElementById('rv-edit-sn-charger').value = rvComponentMap.charger[value]?.serial_number || '';
    }
    updateChangeHighlights();
});
</script>
<?= $this->endSection() ?>

