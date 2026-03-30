<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<?php
$stats = $stats ?? [];
$location_types = $location_types ?? [];
$component_types = $component_types ?? [];
?>

<!-- Page Header -->
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active">Surat Jalan</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-truck me-2 text-primary"></i>Surat Jalan / Movement
        </h4>
        <p class="text-muted small mb-0">Record perpindahan unit antar workshop (POS) atau lokasi perusahaan</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary btn-sm" onclick="showCreateModal()">
            <i class="fas fa-plus me-1"></i>Buat Surat Jalan
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="card bg-primary text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-white-50">Total</div>
                        <div class="fs-4 fw-bold"><?= $stats['total'] ?? 0 ?></div>
                    </div>
                    <div><i class="fas fa-clipboard-list fa-2x opacity-50"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-secondary text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-white-50">Draft</div>
                        <div class="fs-4 fw-bold"><?= $stats['draft'] ?? 0 ?></div>
                    </div>
                    <div><i class="fas fa-edit fa-2x opacity-50"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-warning text-dark">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-dark-50">Dalam Perjalanan</div>
                        <div class="fs-4 fw-bold"><?= $stats['in_transit'] ?? 0 ?></div>
                    </div>
                    <div><i class="fas fa-truck fa-2x opacity-50"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-success text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-white-50">Selesai</div>
                        <div class="fs-4 fw-bold"><?= $stats['arrived'] ?? 0 ?></div>
                    </div>
                    <div><i class="fas fa-check-circle fa-2x opacity-50"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-danger text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-white-50">Batal</div>
                        <div class="fs-4 fw-bold"><?= $stats['cancelled'] ?? 0 ?></div>
                    </div>
                    <div><i class="fas fa-times-circle fa-2x opacity-50"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-2">
                <label class="form-label small">Status</label>
                <select class="form-select form-select-sm" id="filterStatus" onchange="loadMovements()">
                    <option value="">Semua Status</option>
                    <option value="DRAFT">Draft</option>
                    <option value="IN_TRANSIT">Dalam Perjalanan</option>
                    <option value="ARRIVED">Selesai</option>
                    <option value="CANCELLED">Batal</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Tipe Asal</label>
                <select class="form-select form-select-sm" id="filterOrigin" onchange="loadMovements()">
                    <option value="">Semua Asal</option>
                    <option value="POS_1">POS 1</option>
                    <option value="POS_2">POS 2</option>
                    <option value="POS_3">POS 3</option>
                    <option value="POS_4">POS 4</option>
                    <option value="POS_5">POS 5</option>
                    <option value="WAREHOUSE">Gudang</option>
                    <option value="CUSTOMER_SITE">Lokasi Customer</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Tipe Tujuan</label>
                <select class="form-select form-select-sm" id="filterDestination" onchange="loadMovements()">
                    <option value="">Semua Tujuan</option>
                    <option value="POS_1">POS 1</option>
                    <option value="POS_2">POS 2</option>
                    <option value="POS_3">POS 3</option>
                    <option value="POS_4">POS 4</option>
                    <option value="POS_5">POS 5</option>
                    <option value="WAREHOUSE">Gudang</option>
                    <option value="CUSTOMER_SITE">Lokasi Customer</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Dari Tanggal</label>
                <input type="date" class="form-control form-control-sm" id="filterDateFrom" onchange="loadMovements()">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Sampai Tanggal</label>
                <input type="date" class="form-control form-control-sm" id="filterDateTo" onchange="loadMovements()">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-outline-secondary btn-sm w-100" onclick="resetFilters()">
                    <i class="fas fa-reset me-1"></i>Reset
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card shadow-sm">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Surat Jalan</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="movementTable">
                <thead class="bg-light">
                    <tr>
                        <th>No. SJ</th>
                        <th>No. Movement</th>
                        <th>Unit</th>
                        <th>Tipe</th>
                        <th>Asal</th>
                        <th>Tujuan</th>
                        <th>Tanggal</th>
                        <th>Driver</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="10" class="text-center py-4">
                            <i class="fas fa-spinner fa-spin me-2"></i>Memuat data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Buat Surat Jalan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Pilih Unit</label>
                                <select class="form-select" id="unitSelect" name="unit_id">
                                    <option value="">-- Unit Utama (Opsional) --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipe Komponen</label>
                                <select class="form-select" name="component_type">
                                    <option value="FORKLIFT">Forklift</option>
                                    <option value="ATTACHMENT">Attachment</option>
                                    <option value="CHARGER">Charger</option>
                                    <option value="BATTERY">Baterai</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Lokasi Asal <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="origin_location" placeholder="Contoh: Workshop POS 1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipe Asal <span class="text-danger">*</span></label>
                                <select class="form-select" name="origin_type" required>
                                    <option value="POS_1">POS 1 (Workshop Utama)</option>
                                    <option value="POS_2">POS 2</option>
                                    <option value="POS_3">POS 3</option>
                                    <option value="POS_4">POS 4</option>
                                    <option value="POS_5">POS 5</option>
                                    <option value="WAREHOUSE">Gudang</option>
                                    <option value="CUSTOMER_SITE">Lokasi Customer</option>
                                    <option value="OTHER">Lainnya</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Lokasi Tujuan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="destination_location" placeholder="Contoh: Workshop POS 2" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipe Tujuan <span class="text-danger">*</span></label>
                                <select class="form-select" name="destination_type" required>
                                    <option value="POS_1">POS 1 (Workshop Utama)</option>
                                    <option value="POS_2">POS 2</option>
                                    <option value="POS_3">POS 3</option>
                                    <option value="POS_4">POS 4</option>
                                    <option value="POS_5">POS 5</option>
                                    <option value="WAREHOUSE">Gudang</option>
                                    <option value="CUSTOMER_SITE">Lokasi Customer</option>
                                    <option value="OTHER">Lainnya</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Perpindahan <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" name="movement_date" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Nama Driver</label>
                                <input type="text" class="form-control" name="driver_name" placeholder="Nama driver">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">No. Kendaraan</label>
                                <input type="text" class="form-control" name="vehicle_number" placeholder="Contoh: B 1234 ABC">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Catatan tambahan..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
                <button type="button" class="btn btn-primary" onclick="submitMovement()">
                    <i class="fas fa-save me-1"></i><?= lang('Common.save') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Detail Surat Jalan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Content loaded via AJAX -->
            </div>
            <div class="modal-footer" id="detailActions">
                <!-- Action buttons loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
const BASE_URL = '<?= base_url() ?>';

$(document).ready(function() {
    loadMovements();
    loadUnitsForSelect();

    // Set default datetime
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    $('input[name="movement_date"]').val(now.toISOString().slice(0, 16));
});

/**
 * Populate #unitSelect with shared Optima template (layouts/base loads unit-select2.js).
 */
function rebuildUnitSelectWithOptima(units) {
    const $sel = $('#unitSelect');
    if (!$sel.length) {
        return;
    }
    if ($sel.hasClass('select2-hidden-accessible')) {
        try {
            $sel.select2('destroy');
        } catch (e) { /* ignore */ }
    }
    $sel.empty().append($('<option value=""></option>').text('-- Unit Utama (Opsional) --'));
    const Ou = window.OptimaUnitSelect2;
    const useOu = typeof Ou !== 'undefined' && typeof Ou.optionDataAttributes === 'function';
    (units || []).forEach(function (unit) {
        const id = unit.id_inventory_unit;
        const row = {
            id: id,
            id_inventory_unit: id,
            no_unit: unit.no_unit || unit.no_unit_na,
            serial_number: unit.serial_number || '',
            merk: unit.merk_unit || '',
            model_unit: unit.model_unit || '',
            jenis: unit.tipe || '',
            kapasitas: '',
            status: '',
            lokasi: unit.lokasi || unit.lokasi_unit || '',
            pelanggan: unit.pelanggan || ''
        };
        if (useOu) {
            const attrs = Ou.optionDataAttributes(row);
            const label = Ou.line1FromRow(Ou.normalizeRow(row));
            const $opt = $('<option></option>').val(String(id)).text(label);
            Object.keys(attrs).forEach(function (k) {
                const v = attrs[k];
                if (v !== '' && v != null && v !== false) {
                    $opt.attr(k, v);
                }
            });
            $sel.append($opt);
        } else {
            const t = (unit.no_unit || unit.no_unit_na || ('UNIT-' + id)) + ' — ' + (unit.merk_unit || '') + ' ' + (unit.model_unit || '');
            $sel.append($('<option></option>').val(String(id)).text(t.trim()));
        }
    });
    if ($.fn.select2 && useOu) {
        $sel.select2({
            dropdownParent: $('#createModal'),
            width: '100%',
            placeholder: '-- Unit Utama (Opsional) --',
            allowClear: true,
            minimumResultsForSearch: 0,
            theme: 'bootstrap-5',
            templateResult: function (i) { return Ou.templateResult(i, {}); },
            templateSelection: function (i) { return Ou.templateSelection(i, {}); },
            escapeMarkup: function (m) { return m; }
        });
    }
}

function loadMovements() {
    const status = $('#filterStatus').val();
    const originType = $('#filterOrigin').val();
    const destinationType = $('#filterDestination').val();
    const dateFrom = $('#filterDateFrom').val();
    const dateTo = $('#filterDateTo').val();

    $.ajax({
        url: BASE_URL + 'unit_audit/getMovements',
        type: 'GET',
        data: {
            status: status,
            origin_type: originType,
            destination_type: destinationType,
            date_from: dateFrom,
            date_to: dateTo
        },
        success: function(res) {
            if (res.success) {
                renderMovementTable(res.data);
            }
        },
        error: function() {
            $('#movementTable tbody').html('<tr><td colspan="10" class="text-center text-danger py-4">Error memuat data</td></tr>');
        }
    });
}

function renderMovementTable(data) {
    if (!data || data.length === 0) {
        $('#movementTable tbody').html('<tr><td colspan="10" class="text-center text-muted py-4">Tidak ada data</td></tr>');
        return;
    }

    let html = '';
    data.forEach(item => {
        const statusBadge = getMovementStatusBadge(item.status);
        const date = new Date(item.movement_date).toLocaleDateString('id-ID');

        html += '<tr>';
        html += '<td><strong>' + (item.surat_jalan_number || '-') + '</strong></td>';
        html += '<td><small>' + (item.movement_number || '-') + '</small></td>';
        html += '<td>' + (item.no_unit || item.no_unit_na || '<span class="text-muted">-</span>') + '<br><small class="text-muted">' + (item.merk_unit || '') + '</small></td>';
        html += '<td>' + getComponentBadge(item.component_type) + '</td>';
        html += '<td>' + item.origin_location + '<br><small class="text-muted">' + item.origin_type + '</small></td>';
        html += '<td>' + item.destination_location + '<br><small class="text-muted">' + item.destination_type + '</small></td>';
        html += '<td>' + date + '</td>';
        html += '<td>' + (item.driver_name || '-') + '</td>';
        html += '<td>' + statusBadge + '</td>';
        html += '<td><button class="btn btn-xs btn-outline-primary" onclick="viewMovementDetail(' + item.id + ')"><i class="fas fa-eye"></i></button></td>';
        html += '</tr>';
    });

    $('#movementTable tbody').html(html);
}

function getMovementStatusBadge(status) {
    const badges = {
        'DRAFT': '<span class="badge badge-soft-gray">Draft</span>',
        'IN_TRANSIT': '<span class="badge badge-soft-yellow">Dalam Perjalanan</span>',
        'ARRIVED': '<span class="badge badge-soft-green">Selesai</span>',
        'CANCELLED': '<span class="badge badge-soft-red">Batal</span>'
    };
    return badges[status] || status;
}

function getComponentBadge(type) {
    const badges = {
        'FORKLIFT': '<span class="badge badge-soft-blue">Forklift</span>',
        'ATTACHMENT': '<span class="badge badge-soft-cyan">Attachment</span>',
        'CHARGER': '<span class="badge badge-soft-yellow">Charger</span>',
        'BATTERY': '<span class="badge badge-soft-green">Baterai</span>'
    };
    return badges[type] || type;
}

function showCreateModal() {
    $('#createModal').modal('show');
}

function loadUnitsForSelect() {
    $.ajax({
        url: BASE_URL + 'unit_audit/getAvailableUnits',
        type: 'GET',
        success: function(res) {
            if (res.success && res.data) {
                rebuildUnitSelectWithOptima(res.data);
            }
        }
    });
}

function submitMovement() {
    const form = document.getElementById('createForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);

    $.ajax({
        url: BASE_URL + 'unit_audit/createMovement',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            if (res.success) {
                $('#createModal').modal('hide');
                form.reset();
                loadMovements();

                // Set datetime again
                const now2 = new Date();
                now2.setMinutes(now2.getMinutes() - now2.getTimezoneOffset());
                $('input[name="movement_date"]').val(now2.toISOString().slice(0, 16));

                if (window.OptimaNotify) OptimaNotify.success('Surat Jalan berhasil dibuat!\nNo. Movement: ' + res.data.movement_number + '\nNo. SJ: ' + (res.data.surat_jalan_number || '-'));
                else alert('Surat Jalan berhasil dibuat!\nNo. Movement: ' + res.data.movement_number + '\nNo. SJ: ' + (res.data.surat_jalan_number || '-'));
            } else {
                if (window.OptimaNotify) OptimaNotify.error('Error: ' + res.message);
                else alert('Error: ' + res.message);
            }
        },
        error: function() {
            if (window.OptimaNotify) OptimaNotify.error('Terjadi kesalahan saat menyimpan');
            else alert('Terjadi kesalahan saat menyimpan');
        }
    });
}

function viewMovementDetail(id) {
    $.ajax({
        url: BASE_URL + 'unit_audit/getMovementDetail/' + id,
        type: 'GET',
        success: function(res) {
            if (res.success) {
                showMovementDetailModal(res.data);
            }
        }
    });
}

function showMovementDetailModal(data) {
    const movement = data.movement;
    const unit = data.unit;

    const statusBadge = getMovementStatusBadge(movement.status);
    const componentBadge = getComponentBadge(movement.component_type);
    const date = new Date(movement.movement_date).toLocaleString('id-ID');
    const createdDate = new Date(movement.created_at).toLocaleString('id-ID');
    const confirmedDate = movement.confirmed_at ? new Date(movement.confirmed_at).toLocaleString('id-ID') : '-';

    let content = '<div class="row">';
    content += '<div class="col-md-4"><strong>No. SJ:</strong></div><div class="col-md-8"><strong>' + (movement.surat_jalan_number || '-') + '</strong></div>';
    content += '<div class="col-md-4"><strong>No. Movement:</strong></div><div class="col-md-8">' + (movement.movement_number || '-') + '</div>';
    content += '<div class="col-md-4"><strong>Tipe Komponen:</strong></div><div class="col-md-8">' + componentBadge + '</div>';
    content += '<div class="col-md-4"><strong>Status:</strong></div><div class="col-md-8">' + statusBadge + '</div>';
    content += '<div class="col-md-4"><strong>Tanggal:</strong></div><div class="col-md-8">' + date + '</div>';
    content += '<div class="col-md-4"><strong>Dibuat Oleh:</strong></div><div class="col-md-8">' + (movement.creator_name || '-') + '</div>';
    content += '<div class="col-md-4"><strong>Tanggal Dibuat:</strong></div><div class="col-md-8">' + createdDate + '</div>';
    content += '</div><hr>';

    content += '<div class="row">';
    content += '<div class="col-md-6"><div class="alert alert-secondary mb-0">';
    content += '<h6 class="fw-bold">Asal:</h6>';
    content += '<p class="mb-1"><strong>Lokasi:</strong> ' + movement.origin_location + '</p>';
    content += '<p class="mb-0"><strong>Tipe:</strong> ' + movement.origin_type + '</p>';
    content += '</div></div>';
    content += '<div class="col-md-6"><div class="alert alert-info mb-0">';
    content += '<h6 class="fw-bold">Tujuan:</h6>';
    content += '<p class="mb-1"><strong>Lokasi:</strong> ' + movement.destination_location + '</p>';
    content += '<p class="mb-0"><strong>Tipe:</strong> ' + movement.destination_type + '</p>';
    content += '</div></div>';
    content += '</div><hr>';

    content += '<div class="alert alert-warning mb-0">';
    content += '<h6 class="fw-bold">Detail Pengiriman:</h6>';
    content += '<p class="mb-1"><strong>Driver:</strong> ' + (movement.driver_name || '-') + '</p>';
    content += '<p class="mb-1"><strong>No. Kendaraan:</strong> ' + (movement.vehicle_number || '-') + '</p>';
    content += '<p class="mb-0"><strong>Catatan:</strong> ' + (movement.notes || '-') + '</p>';
    content += '</div>';

    if (movement.confirmed_at) {
        content += '<hr><div class="alert alert-success mb-0">';
        content += '<h6 class="fw-bold">Konfirmasi Penerimaan:</h6>';
        content += '<p class="mb-1"><strong>Dikonfirmasi Oleh:</strong> ' + (movement.confirmer_name || '-') + '</p>';
        content += '<p class="mb-0"><strong>Tanggal:</strong> ' + confirmedDate + '</p>';
        content += '</div>';
    }

    $('#detailContent').html(content);

    // Action buttons
    let actions = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' + (typeof window.lang === 'function' ? window.lang('cancel') : 'Cancel') + '</button>';

    if (movement.status === 'DRAFT') {
        actions += '<button class="btn btn-primary" onclick="startMovement(' + movement.id + ')"><i class="fas fa-truck me-1"></i>' + (typeof window.lang === 'function' ? window.lang('start') : 'Start') + '</button>';
        actions += '<button class="btn btn-danger" onclick="cancelMovement(' + movement.id + ')"><i class="fas fa-times me-1"></i>' + (typeof window.lang === 'function' ? window.lang('cancel') : 'Cancel') + '</button>';
    } else if (movement.status === 'IN_TRANSIT') {
        actions += '<button class="btn btn-success" onclick="confirmArrival(' + movement.id + ')"><i class="fas fa-check me-1"></i>Konfirmasi Tiba</button>';
    }

    $('#detailActions').html(actions);
    $('#detailModal').modal('show');
}

function startMovement(id) {
    OptimaConfirm.generic({
        title: 'Mulai Pengiriman?',
        text: 'Movement akan dimulai.',
        icon: 'question',
        confirmText: 'Ya, Mulai!',
        cancelText: window.lang('cancel'),
        confirmButtonColor: 'primary',
        onConfirm: function() {
            $.ajax({
                url: BASE_URL + 'unit_audit/startMovement/' + id,
                type: 'POST',
                data: {},
                success: function(res) {
                    if (res.success) {
                        $('#detailModal').modal('hide');
                        loadMovements();
                        OptimaNotify.success('Movement dimulai!');
                    } else {
                        OptimaNotify.error('Error: ' + res.message);
                    }
                }
            });
        }
    });
}

function confirmArrival(id) {
    OptimaConfirm.approve({
        title: 'Konfirmasi Tiba?',
        text: 'Unit telah sampai di tujuan.',
        confirmText: 'Ya, Konfirmasi!',
        cancelText: window.lang('cancel'),
        onConfirm: function() {
            $.ajax({
                url: BASE_URL + 'unit_audit/confirmArrival/' + id,
                type: 'POST',
                data: {},
                success: function(res) {
                    if (res.success) {
                        $('#detailModal').modal('hide');
                        loadMovements();
                        OptimaNotify.success('Movement selesai dan lokasi unit diperbarui!');
                    } else {
                        OptimaNotify.error('Error: ' + res.message);
                    }
                }
            });
        }
    });
}

function cancelMovement(id) {
    OptimaConfirm.danger({
        title: 'Batalkan Movement?',
        text: 'Movement ini akan dibatalkan.',
        confirmText: 'Ya, Batalkan!',
        cancelText: window.lang('back'),
        onConfirm: function() {
            $.ajax({
                url: BASE_URL + 'unit_audit/cancelMovement/' + id,
                type: 'POST',
                data: {},
                success: function(res) {
                    if (res.success) {
                        $('#detailModal').modal('hide');
                        loadMovements();
                        OptimaNotify.success('Movement dibatalkan');
                    } else {
                        OptimaNotify.error('Error: ' + res.message);
                    }
                }
            });
        }
    });
}

function resetFilters() {
    $('#filterStatus').val('');
    $('#filterOrigin').val('');
    $('#filterDestination').val('');
    $('#filterDateFrom').val('');
    $('#filterDateTo').val('');
    loadMovements();
}
</script>
<?= $this->endSection() ?>
