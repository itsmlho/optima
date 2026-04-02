<?= $this->extend('layouts/base') ?>
<?php
/**
 * PM Schedules — Manage preventive maintenance schedule definitions per unit.
 */
helper('global_permission');
$permissions = get_global_permission('service');
$can_create  = $permissions['create'];
$can_edit    = $permissions['edit'];
$can_delete  = $permissions['delete'];
?>

<?= $this->section('content') ?>

<div class="card table-card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0">
                <i class="bi bi-calendar-check me-2 text-primary"></i>Jadwal Preventive Maintenance
            </h5>
            <p class="text-muted small mb-0">Kelola jadwal PM per unit — kapan dan apa yang harus diperiksa</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('service/pmps') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke PMPS
            </a>
            <?php if ($can_create): ?>
            <button class="btn btn-primary btn-sm" id="btn-add-schedule">
                <i class="fas fa-plus me-1"></i>Tambah Jadwal PM
            </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3 g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold small mb-1">Status Jadwal</label>
                <select id="filter-active" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="1" selected>Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table id="schedules-table" class="table table-hover table-sm align-middle mb-0 w-100">
                <thead class="table-light">
                    <tr class="small text-uppercase">
                        <th>Jadwal</th>
                        <th>Unit</th>
                        <th>Lokasi</th>
                        <th>Trigger</th>
                        <th>Interval</th>
                        <th>PM Terakhir</th>
                        <th>PM Berikutnya</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- ── Add / Edit Schedule Modal ──────────────────────────────────────────── -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleModalLabel">
                    <i class="bi bi-calendar-plus me-2 text-primary"></i>Tambah Jadwal PM
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">

                <!-- ── Informasi Jadwal + Konfigurasi WO ──────────────────── -->
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header py-2">
                                <h6 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Jadwal</h6>
                            </div>
                            <div class="card-body">
                                <input type="hidden" id="schedule-id">

                                <div class="mb-3">
                                    <label for="unit-select" class="form-label fw-semibold">Unit <span class="text-danger">*</span></label>
                                    <select id="unit-select" class="form-select form-select-sm select2-modal" required>
                                        <option value="">— Pilih Unit —</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="schedule-name" class="form-label fw-semibold">Nama Jadwal <span class="text-danger">*</span></label>
                                    <input type="text" id="schedule-name" class="form-control form-control-sm" placeholder="cth: PM Bulanan, PM 250HM">
                                </div>

                                <div class="mb-3">
                                    <label for="trigger-type" class="form-label fw-semibold">Tipe Trigger <span class="text-danger">*</span></label>
                                    <select id="trigger-type" class="form-select form-select-sm">
                                        <option value="CALENDAR">Kalender (setiap N hari)</option>
                                        <option value="HM">Hour Meter (setiap N HM)</option>
                                        <option value="BOTH">Keduanya</option>
                                    </select>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6" id="interval-days-group">
                                        <label class="form-label fw-semibold small">Interval Hari</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" id="interval-days" class="form-control form-control-sm" min="1" placeholder="cth: 30">
                                            <span class="input-group-text">hari</span>
                                        </div>
                                    </div>
                                    <div class="col-6" id="interval-hm-group">
                                        <label class="form-label fw-semibold small">Interval HM</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" id="interval-hm" class="form-control form-control-sm" min="1" placeholder="cth: 250">
                                            <span class="input-group-text">HM</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-0">
                                    <label for="start-date" class="form-label fw-semibold">Tanggal PM Pertama</label>
                                    <input type="date" id="start-date" class="form-control form-control-sm">
                                    <div class="form-text">Kosongkan untuk auto-hitung dari interval</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header py-2">
                                <h6 class="mb-0 fw-bold"><i class="fas fa-cog me-2 text-primary"></i>Konfigurasi Work Order</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="wo-category" class="form-label fw-semibold">Kategori WO</label>
                                    <select id="wo-category" class="form-select form-select-sm">
                                        <option value="">— Pilih Kategori (opsional) —</option>
                                        <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= esc($cat['category_name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="wo-priority" class="form-label fw-semibold">Prioritas WO</label>
                                    <select id="wo-priority" class="form-select form-select-sm">
                                        <option value="">— Pilih Prioritas —</option>
                                        <?php foreach ($priorities as $p): ?>
                                        <option value="<?= $p['id'] ?>"><?= esc($p['priority_name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="schedule-notes" class="form-label fw-semibold">Catatan</label>
                                    <textarea id="schedule-notes" class="form-control form-control-sm" rows="3" placeholder="Catatan tambahan tentang jadwal PM ini…"></textarea>
                                </div>

                                <div id="is-active-group" style="display:none">
                                    <label for="is-active" class="form-label fw-semibold">Status Jadwal</label>
                                    <select id="is-active" class="form-select form-select-sm">
                                        <option value="1">Aktif</option>
                                        <option value="0">Nonaktif</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── Template Checklist ─────────────────────────────────── -->
                <div class="card shadow-sm mt-3">
                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-list-check me-2 text-primary"></i>Template Checklist <small class="text-muted fw-normal ms-1">Item yang harus dicek saat PM</small></h6>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="btn-add-checklist-item">
                            <i class="fas fa-plus me-1"></i>Tambah Item
                        </button>
                    </div>
                    <div class="card-body p-2">
                        <!-- Header row -->
                        <div class="row g-2 px-2 mb-1 d-none d-md-flex">
                            <div class="col-md-5"><small class="fw-semibold text-muted">Nama Item</small></div>
                            <div class="col-md-2"><small class="fw-semibold text-muted">Kategori</small></div>
                            <div class="col-md-2"><small class="fw-semibold text-muted">Tipe Aksi</small></div>
                            <div class="col-md-2"><small class="fw-semibold text-muted">Wajib</small></div>
                            <div class="col-md-1"></div>
                        </div>
                        <div id="checklist-container">
                            <div class="text-center text-muted py-3 fst-italic small" id="checklist-empty-hint">
                                <i class="bi bi-info-circle me-1"></i>Belum ada item checklist. Klik "Tambah Item" untuk memulai.
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /.modal-body -->
            <div class="modal-footer d-flex justify-content-between">
                <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Bidang dengan <span class="text-danger">*</span> wajib diisi</small>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary btn-sm" id="btn-save-schedule">
                        <i class="fas fa-save me-1"></i>Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
// CSRF helper
function getCsrf() {
    return {
        name: window.csrfTokenName,
        hash: (typeof window.getCsrfToken === 'function') ? window.getCsrfToken() : window.csrfToken
    };
}

let schedulesTable;
let checklistCount = 0;

// ── DataTable ──────────────────────────────────────────────────────────────
function initTable() {
    schedulesTable = $('#schedules-table').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        pageLength: 25,
        ajax: {
            url: BASE_URL + 'service/pm-schedules/getSchedules',
            type: 'GET',
            data: d => { d.is_active = $('#filter-active').val(); return d; }
        },
        columns: [
            {
                data: 'schedule_name',
                render: (d, _, row) => `<strong>${d}</strong><br><small class="text-muted">${row.wo_category_name ?? ''}</small>`
            },
            {
                data: 'no_unit',
                render: (d, _, row) => d
                    ? `<strong>${d}</strong><br><small class="text-muted">${row.merk ?? ''} ${row.model ?? ''}</small>`
                    : '<span class="text-muted fst-italic small">—</span>'
            },
            {
                data: 'customer_name',
                render: (d, _, row) => `${d ?? '<span class="text-muted fst-italic">Belum ada kontrak</span>'}<br><small class="text-muted">${row.location_name ?? ''}</small>`
            },
            {
                data: 'trigger_type',
                render: d => {
                    const map = { CALENDAR: 'badge-soft-blue', HM: 'badge-soft-orange', BOTH: 'badge-soft-purple' };
                    return `<span class="badge ${map[d] || 'badge-soft-gray'}">${d}</span>`;
                }
            },
            {
                data: 'interval_days',
                render: (d, _, row) => {
                    let parts = [];
                    if (row.interval_days) parts.push(`${row.interval_days} hari`);
                    if (row.interval_hm)   parts.push(`${row.interval_hm} HM`);
                    return parts.join(' / ') || '<span class="text-muted">—</span>';
                }
            },
            {
                data: 'last_pm_date',
                render: (d, _, row) => d
                    ? `${d}<br><small class="text-muted">${row.last_pm_hm ? row.last_pm_hm + ' HM' : ''}</small>`
                    : '<span class="text-muted fst-italic small">Belum pernah</span>'
            },
            {
                data: 'next_pm_date',
                render: (d, _, row) => {
                    if (!d && !row.next_pm_hm) return '<span class="badge badge-soft-gray">Belum diset</span>';
                    const today = new Date(); today.setHours(0, 0, 0, 0);
                    let html = '';
                    if (d) {
                        const due  = new Date(d);
                        const diff = Math.floor((due - today) / 86400000);
                        const cls  = diff < 0 ? 'text-danger fw-bold' : diff <= 7 ? 'text-warning fw-semibold' : '';
                        html += `<span class="${cls}">${d}</span>`;
                    }
                    if (row.next_pm_hm) html += `<br><small class="text-muted">${row.next_pm_hm} HM</small>`;
                    return html;
                }
            },
            {
                data: 'is_active',
                render: d => d
                    ? '<span class="badge badge-soft-green">Aktif</span>'
                    : '<span class="badge badge-soft-gray">Nonaktif</span>'
            },
            {
                data: 'id', orderable: false, className: 'text-center',
                render: d => `
                    <button class="btn btn-sm btn-outline-primary btn-edit-schedule" data-id="${d}" title="Edit"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-danger btn-delete-schedule" data-id="${d}" title="Nonaktifkan"><i class="bi bi-x-circle"></i></button>`
            },
        ],
        order: [[6, 'asc']],
        language: {
            processing:   'Memuat data…',
            search:       'Cari:',
            lengthMenu:   'Tampilkan _MENU_ data',
            info:         'Menampilkan _START_–_END_ dari _TOTAL_ data',
            infoEmpty:    'Tidak ada data',
            infoFiltered: '(dari _MAX_ total)',
            zeroRecords:  'Tidak ditemukan jadwal yang sesuai',
            emptyTable:   'Belum ada jadwal PM',
            paginate:     { first: 'Pertama', previous: 'Sebelumnya', next: 'Selanjutnya', last: 'Terakhir' }
        },
    });
}

// ── Checklist Template Builder ─────────────────────────────────────────────
function addChecklistRow(item = {}) {
    $('#checklist-empty-hint').hide();
    const i = checklistCount++;
    const actionOptions = ['CHECK','REPLACE','ADJUST','CLEAN','LUBRICATE','OTHER']
        .map(a => `<option value="${a}" ${(item.action_type || 'CHECK') === a ? 'selected' : ''}>${a}</option>`).join('');
    const row = `
    <div class="row g-2 align-items-center mb-2 checklist-row border-bottom pb-2 px-2" data-index="${i}">
        <div class="col-md-5">
            <input type="text" class="form-control form-control-sm ci-item-name" placeholder="Nama item, cth: Cek oli mesin" value="${item.item_name ?? ''}">
        </div>
        <div class="col-md-2">
            <input type="text" class="form-control form-control-sm ci-item-category" placeholder="Kategori" value="${item.item_category ?? ''}">
        </div>
        <div class="col-md-2">
            <select class="form-select form-select-sm ci-action-type">${actionOptions}</select>
        </div>
        <div class="col-md-2">
            <div class="form-check form-switch mb-0">
                <input class="form-check-input ci-required" type="checkbox" role="switch" ${item.is_required != 0 ? 'checked' : ''}>
                <label class="form-check-label small text-muted">Wajib</label>
            </div>
        </div>
        <div class="col-md-1 text-end">
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-ci" title="Hapus baris"><i class="bi bi-trash"></i></button>
        </div>
    </div>`;
    $('#checklist-container').append(row);
}

function getChecklistItems() {
    const items = [];
    $('#checklist-container .checklist-row').each(function(idx) {
        const name = $(this).find('.ci-item-name').val().trim();
        if (!name) return;
        items.push({
            item_order:    idx,
            item_name:     name,
            item_category: $(this).find('.ci-item-category').val().trim() || null,
            action_type:   $(this).find('.ci-action-type').val(),
            is_required:   $(this).find('.ci-required').is(':checked') ? 1 : 0,
        });
    });
    return items;
}

// ── Unit Select (Select2) ──────────────────────────────────────────────────
function loadUnits(selectedId = null) {
    $.getJSON(BASE_URL + 'service/work-orders/units-dropdown', function(res) {
        const $sel = $('#unit-select');
        $sel.empty().append('<option value="">— Pilih Unit —</option>');
        const units = res.data || res;
        if (Array.isArray(units)) {
            units.forEach(u => {
                const label = `${u.no_unit ?? u.id} — ${u.merk ?? ''} ${u.model_unit ?? ''} ${u.pelanggan ? '| ' + u.pelanggan : ''}`;
                $sel.append(new Option(label, u.id, false, u.id == selectedId));
            });
        }
        if ($sel.hasClass('select2-hidden-accessible')) {
            $sel.trigger('change.select2');
        }
    });
}

// ── Trigger Type Visibility ────────────────────────────────────────────────
function updateTriggerFields() {
    const type = $('#trigger-type').val();
    $('#interval-days-group').toggle(type !== 'HM');
    $('#interval-hm-group').toggle(type !== 'CALENDAR');
}

// ── Reset form ─────────────────────────────────────────────────────────────
function resetForm() {
    $('#schedule-id').val('');
    $('#unit-select, #schedule-name, #interval-days, #interval-hm, #start-date, #schedule-notes, #wo-category, #wo-priority').val('');
    $('#trigger-type').val('CALENDAR');
    $('#is-active').val('1');
    $('#is-active-group').hide();
    $('#checklist-container .checklist-row').remove();
    $('#checklist-empty-hint').show();
    checklistCount = 0;
    updateTriggerFields();

    if ($('#unit-select').hasClass('select2-hidden-accessible')) {
        $('#unit-select').val('').trigger('change.select2');
    }
}

// ── Open Add Modal ─────────────────────────────────────────────────────────
function openAddModal() {
    resetForm();
    $('#scheduleModalLabel').html('<i class="bi bi-calendar-plus me-2 text-primary"></i>Tambah Jadwal PM');
    loadUnits();
    new bootstrap.Modal(document.getElementById('scheduleModal')).show();
}

// ── Open Edit Modal ────────────────────────────────────────────────────────
function openEditModal(id) {
    resetForm();
    $.getJSON(BASE_URL + 'service/pm-schedules/get/' + id, function(res) {
        if (!res.success) { OptimaNotify.error(res.message || 'Gagal memuat data'); return; }
        const d = res.data;
        $('#scheduleModalLabel').html('<i class="bi bi-pencil me-2 text-warning"></i>Edit Jadwal PM');
        loadUnits(d.unit_id);
        $('#schedule-id').val(d.id);
        $('#schedule-name').val(d.schedule_name);
        $('#trigger-type').val(d.trigger_type);
        $('#interval-days').val(d.interval_days ?? '');
        $('#interval-hm').val(d.interval_hm ?? '');
        $('#start-date').val(d.next_pm_date ?? '');
        $('#wo-category').val(d.wo_category_id ?? '');
        $('#wo-priority').val(d.priority_id ?? '');
        $('#schedule-notes').val(d.notes ?? '');
        $('#is-active').val(d.is_active);
        $('#is-active-group').show();
        updateTriggerFields();
        (d.checklist_templates || []).forEach(item => addChecklistRow(item));
        if (!d.checklist_templates || !d.checklist_templates.length) $('#checklist-empty-hint').show();
        new bootstrap.Modal(document.getElementById('scheduleModal')).show();
    });
}

// ── Save Schedule ──────────────────────────────────────────────────────────
function saveSchedule() {
    const unitId = $('#unit-select').val();
    const name   = $('#schedule-name').val().trim();

    if (!unitId || !name) {
        OptimaNotify.warning('Unit dan Nama Jadwal wajib diisi', 'Validasi');
        return;
    }

    const id     = $('#schedule-id').val();
    const url    = id
        ? BASE_URL + 'service/pm-schedules/update/' + id
        : BASE_URL + 'service/pm-schedules/store';

    const csrf   = getCsrf();
    const items  = getChecklistItems();
    const payload = {
        [csrf.name]: csrf.hash,
        unit_id:        unitId,
        schedule_name:  name,
        trigger_type:   $('#trigger-type').val(),
        interval_days:  $('#interval-days').val(),
        interval_hm:    $('#interval-hm').val(),
        start_date:     $('#start-date').val(),
        wo_category_id: $('#wo-category').val(),
        priority_id:    $('#wo-priority').val(),
        notes:          $('#schedule-notes').val(),
        is_active:      $('#is-active').val() || 1,
    };
    items.forEach((item, i) => {
        Object.entries(item).forEach(([k, v]) => { payload[`checklist_items[${i}][${k}]`] = v ?? ''; });
    });

    const $btn = $('#btn-save-schedule').prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Menyimpan...');
    $.post(url, payload, function(res) {
        if (res.success) {
            OptimaNotify.success(res.message || 'Jadwal berhasil disimpan');
            bootstrap.Modal.getInstance(document.getElementById('scheduleModal')).hide();
            schedulesTable.ajax.reload();
        } else {
            OptimaNotify.error(res.message || 'Gagal menyimpan jadwal');
        }
    }).fail(() => OptimaNotify.error('Gagal menghubungi server'))
      .always(() => $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Simpan'));
}

// ── Events ─────────────────────────────────────────────────────────────────
$(document).ready(function() {
    initTable();

    // Init Select2 on unit dropdown inside modal
    $('#unit-select').select2({
        dropdownParent: $('#scheduleModal'),
        placeholder: '— Pilih Unit —',
        allowClear: true,
        width: '100%'
    });

    $('#filter-active').on('change', () => schedulesTable.ajax.reload());
    $('#btn-add-schedule').on('click', openAddModal);
    $('#trigger-type').on('change', updateTriggerFields);
    $('#btn-save-schedule').on('click', saveSchedule);
    $('#btn-add-checklist-item').on('click', () => addChecklistRow());

    $(document).on('click', '.btn-remove-ci', function() {
        $(this).closest('.checklist-row').remove();
        if (!$('#checklist-container .checklist-row').length) $('#checklist-empty-hint').show();
    });

    $(document).on('click', '.btn-edit-schedule', function() {
        openEditModal($(this).data('id'));
    });

    $(document).on('click', '.btn-delete-schedule', function() {
        const id = $(this).data('id');
        OptimaConfirm.generic({
            title: 'Nonaktifkan Jadwal PM',
            html: '<p class="mb-0">Jadwal PM ini akan dinonaktifkan.<br>PM Job aktif yang sudah ada tidak akan terpengaruh.</p>',
            icon: 'warning',
            confirmText: 'Nonaktifkan',
            cancelText: 'Batal',
            confirmButtonColor: 'danger',
            onConfirm: function() {
                const csrf = getCsrf();
                $.ajax({
                    url: BASE_URL + 'service/pm-schedules/delete/' + id,
                    type: 'DELETE',
                    data: { [csrf.name]: csrf.hash },
                    success: res => {
                        if (res.success) {
                            OptimaNotify.success(res.message || 'Jadwal berhasil dinonaktifkan');
                            schedulesTable.ajax.reload();
                        } else {
                            OptimaNotify.error(res.message || 'Gagal menonaktifkan jadwal');
                        }
                    },
                    error: () => OptimaNotify.error('Gagal menghubungi server')
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
