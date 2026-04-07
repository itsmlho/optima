<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-3">
    <h4 class="fw-bold mb-1">
        <i class="bi bi-pin-map me-2 text-primary"></i>
        Unit Area Mapping
    </h4>
    <p class="text-muted mb-0">Kelola area untuk setiap unit aktif — kelompokkan berdasarkan area, foreman, dan mekanik</p>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card bg-success-soft">
            <div class="d-flex align-items-center">
                <div class="me-3"><i class="bi bi-check-circle stat-icon text-success"></i></div>
                <div>
                    <div class="stat-value" id="statWithArea"><?= $stats['units_with_area'] ?></div>
                    <div class="text-muted">Unit Sudah Ada Area</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card bg-warning-soft">
            <div class="d-flex align-items-center">
                <div class="me-3"><i class="bi bi-exclamation-circle stat-icon text-warning"></i></div>
                <div>
                    <div class="stat-value" id="statWithoutArea"><?= $stats['units_without_area'] ?></div>
                    <div class="text-muted">Unit Belum Ada Area</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card bg-primary-soft">
            <div class="d-flex align-items-center">
                <div class="me-3"><i class="bi bi-truck stat-icon text-primary"></i></div>
                <div>
                    <div class="stat-value"><?= $stats['active_contract_units'] ?></div>
                    <div class="text-muted">Unit Aktif Kontrak</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card bg-danger-soft">
            <div class="d-flex align-items-center">
                <div class="me-3"><i class="bi bi-geo-alt stat-icon text-danger"></i></div>
                <div>
                    <div class="stat-value" id="statLocationsNoArea"><?= $stats['locations_without_area'] ?></div>
                    <div class="text-muted">Lokasi Belum Ada Area</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Tabs -->
<div class="card shadow-sm">
    <div class="card-header bg-white border-bottom">
        <ul class="nav nav-tabs card-header-tabs" id="mappingTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tabAreaOverview">
                    <i class="bi bi-grid me-1"></i> Ringkasan per Area
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabLocations" id="tabLocationsLink">
                    <i class="bi bi-building me-1"></i> Input Area per Lokasi
                    <span class="badge badge-soft-orange ms-1" id="badgeUnassignedLoc"><?= $stats['locations_without_area'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabUnassigned" id="tabUnassignedLink">
                    <i class="bi bi-question-circle me-1"></i> Unit Belum Ter-mapping
                    <span class="badge badge-soft-orange ms-1" id="badgeUnassigned"><?= $stats['units_without_area'] ?></span>
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        <div class="tab-content">

            <!-- ============================================================ -->
            <!-- TAB 1: Area Overview                                          -->
            <!-- ============================================================ -->
            <div class="tab-pane fade show active p-3" id="tabAreaOverview">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted mb-0 small">Klik baris area untuk melihat daftar unit di area tersebut.</p>
                    <button class="btn btn-sm btn-outline-primary" id="btnRefreshSummary">
                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tableAreaSummary">
                        <thead class="table-light">
                            <tr>
                                <th>Area</th>
                                <th>Tipe</th>
                                <th class="text-center">Foreman</th>
                                <th class="text-center">Mekanik</th>
                                <th class="text-center">Lokasi Customer</th>
                                <th class="text-center">Jumlah Unit</th>
                            </tr>
                        </thead>
                        <tbody id="bodyAreaSummary">
                            <tr><td colspan="6" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- Area Unit Detail Panel (hidden until row click) -->
                <div id="panelAreaUnits" class="d-none mt-3">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2">
                            <span id="panelAreaTitle"><i class="bi bi-list-ul me-1"></i> Unit di Area</span>
                            <button class="btn btn-sm btn-outline-light" id="btnClosePanelUnits">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <div class="card-body p-2">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0" id="tableAreaUnits">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No Unit</th>
                                            <th>Model</th>
                                            <th>Status</th>
                                            <th>Customer</th>
                                            <th>Lokasi</th>
                                            <th>No Kontrak</th>
                                            <th>Berakhir</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyAreaUnits">
                                        <tr><td colspan="7" class="text-center py-3">Pilih area untuk melihat unit</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- TAB 2: Input Area per Customer Location                       -->
            <!-- ============================================================ -->
            <div class="tab-pane fade p-3" id="tabLocations">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" id="filterLocationArea" style="width:180px">
                            <option value="all">Semua Lokasi</option>
                            <option value="unassigned" selected>Belum Ada Area</option>
                            <option value="assigned">Sudah Ada Area</option>
                        </select>
                        <button class="btn btn-sm btn-outline-secondary" id="btnLoadLocations">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-success" id="btnSyncFromContracts">
                            <i class="bi bi-arrow-repeat me-1"></i> Auto-Sync dari Kontrak
                        </button>
                        <button class="btn btn-sm btn-outline-info" id="btnRefreshLocations">
                            <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                        </button>
                    </div>
                </div>

                <div class="alert alert-info border-0 py-2 small mb-3">
                    <i class="bi bi-lightbulb me-1"></i>
                    <strong>Cara input data:</strong> Pilih area untuk setiap lokasi customer → klik Save.
                    Semua unit aktif di lokasi tersebut akan otomatis ter-sync ke area yang dipilih.
                    Gunakan <strong>Auto-Sync dari Kontrak</strong> jika area lokasi sudah di-set sebelumnya.
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tableLocations">
                        <thead class="table-light">
                            <tr>
                                <th>Customer</th>
                                <th>Lokasi</th>
                                <th>Kode Lokasi</th>
                                <th class="text-center">Unit Aktif</th>
                                <th style="min-width:200px">Area (klik untuk ubah)</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="bodyLocations">
                            <tr><td colspan="6" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- TAB 3: Unassigned Units                                       -->
            <!-- ============================================================ -->
            <div class="tab-pane fade p-3" id="tabUnassigned">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted mb-0 small">Unit yang belum memiliki area. Assign area manual di sini, atau gunakan tab "Input Area per Lokasi" untuk assignment bulk.</p>
                    <button class="btn btn-sm btn-outline-secondary" id="btnRefreshUnassigned">
                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tableUnassigned">
                        <thead class="table-light">
                            <tr>
                                <th>No Unit</th>
                                <th>Model</th>
                                <th>Status</th>
                                <th>Customer</th>
                                <th>Lokasi</th>
                                <th>No Kontrak</th>
                                <th style="min-width:180px">Assign Area</th>
                            </tr>
                        </thead>
                        <tbody id="bodyUnassigned">
                            <tr><td colspan="7" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div><!-- /tab-content -->
    </div><!-- /card-body -->
</div><!-- /card -->



<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
const base_url    = '<?= base_url() ?>';
const csrfName    = window.csrfTokenName  || '<?= csrf_token() ?>';
const csrfValue   = window.csrfTokenValue || '<?= csrf_hash() ?>';

const allAreas = <?= json_encode($areas) ?>;

// Build area <select> options HTML
function buildAreaOptions(selectedId) {
    let html = '<option value="">-- Tidak Ada --</option>';
    allAreas.forEach(a => {
        const sel = (selectedId && parseInt(selectedId) === a.id) ? ' selected' : '';
        html += `<option value="${a.id}"${sel}>[${a.area_code}] ${a.area_name}</option>`;
    });
    return html;
}

// CSRF helper
function csrfData(extra) {
    const d = {};
    d[csrfName] = csrfValue;
    return Object.assign(d, extra);
}

// ----------------------------------------------------------------
// TAB 1: Area Summary
// ----------------------------------------------------------------
function loadAreaSummary() {
    $.post(base_url + 'service/area-management/unit-mapping/getAreaSummary', csrfData({}), function(resp) {
        const tbody = $('#bodyAreaSummary');
        tbody.empty();
        if (!resp.success || !resp.data.length) {
            tbody.html('<tr><td colspan="6" class="text-center py-3 text-muted">Belum ada area aktif</td></tr>');
            return;
        }
        resp.data.forEach(row => {
            const unitBadge = row.unit_count > 0
                ? `<span class="badge badge-soft-blue">${row.unit_count}</span>`
                : `<span class="badge badge-soft-gray">0</span>`;
            const typeBadge = row.area_type === 'MILL'
                ? `<span class="badge badge-soft-cyan">${row.area_type}</span>`
                : `<span class="badge badge-soft-purple">${row.area_type}</span>`;

            const foremanHtml = row.foremans
                ? `<small class="text-success">${row.foremans}</small>`
                : `<small class="text-muted">-</small>`;
            const mechHtml = row.mechanics
                ? `<small class="text-primary">${row.mechanics}</small>`
                : `<small class="text-muted">-</small>`;

            tbody.append(`
                <tr class="area-row" data-area-id="${row.id}" data-area-name="[${row.area_code}] ${row.area_name}" style="cursor:pointer">
                    <td>
                        <span class="fw-medium">${row.area_name}</span>
                        <small class="text-muted ms-1">${row.area_code}</small>
                    </td>
                    <td>${typeBadge}</td>
                    <td class="text-center">${row.foreman_count > 0 ? `<span class="badge badge-soft-green">${row.foreman_count}</span>` : '<span class="badge badge-soft-gray">0</span>'}<br>${foremanHtml}</td>
                    <td class="text-center">${row.mechanic_count > 0 ? `<span class="badge badge-soft-blue">${row.mechanic_count}</span>` : '<span class="badge badge-soft-gray">0</span>'}<br>${mechHtml}</td>
                    <td class="text-center"><span class="badge badge-soft-cyan">${row.location_count}</span></td>
                    <td class="text-center">${unitBadge}</td>
                </tr>
            `);
        });
    });
}

// Area row click → load units
$(document).on('click', '.area-row', function() {
    const areaId   = $(this).data('area-id');
    const areaName = $(this).data('area-name');
    $('.area-row').removeClass('table-active');
    $(this).addClass('table-active');

    $('#panelAreaTitle').html(`<i class="bi bi-list-ul me-1"></i> Unit di ${areaName}`);
    $('#bodyAreaUnits').html('<tr><td colspan="7" class="text-center py-3"><div class="spinner-border spinner-border-sm"></div></td></tr>');
    $('#panelAreaUnits').removeClass('d-none');

    $.post(base_url + 'service/area-management/unit-mapping/getAreaUnits', csrfData({area_id: areaId}), function(resp) {
        const tbody = $('#bodyAreaUnits');
        tbody.empty();
        if (!resp.success || !resp.data.length) {
            tbody.html('<tr><td colspan="7" class="text-center py-3 text-muted">Tidak ada unit di area ini</td></tr>');
            return;
        }
        resp.data.forEach(u => {
            tbody.append(`
                <tr>
                    <td><strong>${u.no_unit}</strong></td>
                    <td>${u.model || '-'}</td>
                    <td><span class="badge badge-soft-blue">${u.status || '-'}</span></td>
                    <td>${u.customer_name || '<span class="text-muted">-</span>'}</td>
                    <td>${u.location_name || '<span class="text-muted">-</span>'}</td>
                    <td><small>${u.no_kontrak || '-'}</small></td>
                    <td><small>${u.tanggal_berakhir || '-'}</small></td>
                </tr>
            `);
        });
    });
});

$('#btnClosePanelUnits').on('click', function() {
    $('#panelAreaUnits').addClass('d-none');
    $('.area-row').removeClass('table-active');
});

$('#btnRefreshSummary').on('click', loadAreaSummary);

// ----------------------------------------------------------------
// TAB 2: Customer Locations
// ----------------------------------------------------------------
function loadLocations() {
    const filter = $('#filterLocationArea').val();
    $('#bodyLocations').html('<tr><td colspan="6" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>');

    $.post(base_url + 'service/area-management/unit-mapping/getCustomerLocations', csrfData({area_filter: filter}), function(resp) {
        const tbody = $('#bodyLocations');
        tbody.empty();
        if (!resp.success || !resp.data.length) {
            tbody.html('<tr><td colspan="6" class="text-center py-3 text-muted">Tidak ada data</td></tr>');
            return;
        }
        resp.data.forEach(loc => {
            const currentAreaText = loc.area_id
                ? `<span class="badge badge-soft-blue">[${loc.area_code}] ${loc.area_name}</span>`
                : `<span class="text-muted small">Belum di-assign</span>`;

            tbody.append(`
                <tr>
                    <td><strong>${loc.customer_name}</strong></td>
                    <td>${loc.location_name}</td>
                    <td><small class="text-muted">${loc.location_code || '-'}</small></td>
                    <td class="text-center">
                        ${loc.active_units > 0
                            ? `<span class="badge badge-soft-green">${loc.active_units} unit</span>`
                            : `<span class="text-muted">0</span>`}
                    </td>
                    <td>
                        <select class="form-select form-select-sm loc-area-select" data-loc-id="${loc.id}">
                            ${buildAreaOptions(loc.area_id)}
                        </select>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-primary btn-save-location" data-loc-id="${loc.id}">
                            <i class="bi bi-check-lg"></i> Save
                        </button>
                    </td>
                </tr>
            `);
        });
    });
}

$('#btnLoadLocations, #btnRefreshLocations').on('click', loadLocations);

// Save single location area
$(document).on('click', '.btn-save-location', function() {
    const locId  = $(this).data('loc-id');
    const areaId = $(`.loc-area-select[data-loc-id="${locId}"]`).val();
    const btn    = $(this);

    btn.prop('disabled', true).html('<div class="spinner-border spinner-border-sm"></div>');

    $.post(base_url + 'service/area-management/unit-mapping/assignAreaToLocation',
        csrfData({location_id: locId, area_id: areaId}),
        function(resp) {
            btn.prop('disabled', false).html('<i class="bi bi-check-lg"></i> Save');
            if (resp.success) {
                btn.removeClass('btn-primary').addClass('btn-success');
                setTimeout(() => btn.removeClass('btn-success').addClass('btn-primary'), 2000);
                showToast('success', resp.message);
                updateStats();
            } else {
                showToast('danger', resp.message || 'Gagal menyimpan');
            }
        }
    );
});

// Auto-Sync dari Kontrak
$('#btnSyncFromContracts').on('click', function() {
    if (!confirm('Sync area unit berdasarkan customer_location.area_id dari semua kontrak aktif?\n\nHanya unit yang lokasi kontraknya sudah memiliki area yang akan ter-update.')) return;

    const btn = $(this);
    btn.prop('disabled', true).html('<div class="spinner-border spinner-border-sm me-1"></div> Syncing...');

    $.post(base_url + 'service/area-management/unit-mapping/syncFromContracts', csrfData({}), function(resp) {
        btn.prop('disabled', false).html('<i class="bi bi-arrow-repeat me-1"></i> Auto-Sync dari Kontrak');
        if (resp.success) {
            showToast('success', resp.message);
            loadAreaSummary();
            updateStats();
        } else {
            showToast('danger', resp.message);
        }
    });
});

// ----------------------------------------------------------------
// TAB 3: Unassigned Units
// ----------------------------------------------------------------
function loadUnassigned() {
    $('#bodyUnassigned').html('<tr><td colspan="7" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>');

    $.post(base_url + 'service/area-management/unit-mapping/getUnassignedUnits', csrfData({}), function(resp) {
        const tbody = $('#bodyUnassigned');
        tbody.empty();
        if (!resp.success || !resp.data.length) {
            tbody.html('<tr><td colspan="7" class="text-center py-3 text-success"><i class="bi bi-check-circle me-1"></i> Semua unit sudah ter-mapping ke area</td></tr>');
            return;
        }
        resp.data.forEach(u => {
            tbody.append(`
                <tr>
                    <td><strong>${u.no_unit}</strong></td>
                    <td>${u.model || '-'}</td>
                    <td><span class="badge badge-soft-blue">${u.status || '-'}</span></td>
                    <td>${u.customer_name || '<span class="text-muted">Tanpa Kontrak</span>'}</td>
                    <td><small>${u.location_name || '-'}</small></td>
                    <td><small>${u.no_kontrak || '-'}</small></td>
                    <td>
                        <div class="input-group input-group-sm">
                            <select class="form-select form-select-sm unit-area-select" data-unit-id="${u.id_inventory_unit}">
                                ${buildAreaOptions(null)}
                            </select>
                            <button class="btn btn-outline-primary btn-assign-unit" data-unit-id="${u.id_inventory_unit}">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `);
        });
    });
}

$(document).on('click', '.btn-assign-unit', function() {
    const unitId = $(this).data('unit-id');
    const areaId = $(`.unit-area-select[data-unit-id="${unitId}"]`).val();

    if (!areaId) { showToast('warning', 'Pilih area terlebih dahulu'); return; }

    const btn = $(this);
    btn.prop('disabled', true);

    $.post(base_url + 'service/area-management/unit-mapping/manualAssignUnit',
        csrfData({unit_id: unitId, area_id: areaId}),
        function(resp) {
            if (resp.success) {
                btn.closest('tr').fadeOut(300, function() { $(this).remove(); });
                showToast('success', `Unit ${unitId} → area berhasil diassign`);
                updateStats();
            } else {
                btn.prop('disabled', false);
                showToast('danger', resp.message);
            }
        }
    );
});

$('#btnRefreshUnassigned').on('click', loadUnassigned);

// ----------------------------------------------------------------
// Stats update helper
// ----------------------------------------------------------------
function updateStats() {
    $.post(base_url + 'service/area-management/unit-mapping/getAreaSummary', csrfData({}), function() {
        // Just reload area summary — stats update on full page interaction
    });
}

// ----------------------------------------------------------------
// Toast helper
// ----------------------------------------------------------------
function showToast(type, message) {
    if (typeof Swal !== 'undefined') {
        const iconMap = {success: 'success', danger: 'error', warning: 'warning', info: 'info'};
        Swal.fire({ icon: iconMap[type] || 'info', text: message, timer: 3000, showConfirmButton: false, toast: true, position: 'top-end' });
    } else {
        alert(message);
    }
}

// ----------------------------------------------------------------
// Tab switch lazy loading
// ----------------------------------------------------------------
$('#tabLocationsLink').on('click', function() {
    if ($('#bodyLocations tr td[colspan]').length) loadLocations();
});
$('#tabUnassignedLink').on('click', function() {
    if ($('#bodyUnassigned tr td[colspan]').length) loadUnassigned();
});

// Initial load on page ready
$(document).ready(function() {
    loadAreaSummary();
});
</script>

<?= $this->endSection() ?>
