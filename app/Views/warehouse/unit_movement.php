<?= $this->extend('layouts/base') ?>

<?php
/**
 * Surat Jalan / Unit Movement - Warehouse
 * BADGE/CARD: Optima stat-card bg-*-soft for stats; badge-soft-* in JS (movement/type).
 */
$stats = $stats ?? [];
$location_types = $location_types ?? [];
$component_types = $component_types ?? [];
?>
<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('warehouse') ?>">Warehouse</a></li>
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
        <div class="stat-card bg-primary-soft">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total</div>
                    <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
                </div>
                <div><i class="fas fa-clipboard-list fa-2x text-primary opacity-50"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card bg-secondary-soft">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Draft</div>
                    <div class="stat-value"><?= $stats['draft'] ?? 0 ?></div>
                </div>
                <div><i class="fas fa-edit fa-2x text-secondary opacity-50"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card bg-warning-soft">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Dalam Perjalanan</div>
                    <div class="stat-value"><?= $stats['in_transit'] ?? 0 ?></div>
                </div>
                <div><i class="fas fa-truck fa-2x text-warning opacity-50"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card bg-success-soft">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Selesai</div>
                    <div class="stat-value"><?= $stats['arrived'] ?? 0 ?></div>
                </div>
                <div><i class="fas fa-check-circle fa-2x text-success opacity-50"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card bg-danger-soft">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Batal</div>
                    <div class="stat-value"><?= $stats['cancelled'] ?? 0 ?></div>
                </div>
                <div><i class="fas fa-times-circle fa-2x text-danger opacity-50"></i></div>
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
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus me-2 text-primary"></i>Buat Surat Jalan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createForm">
                    <!-- Baris pertama: pilih tipe komponen dulu, baru unit jika diperlukan -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipe Barang / Komponen <span class="text-danger">*</span></label>
                                <select class="form-select" name="component_type" id="componentTypeSelect" onchange="onComponentTypeChange()" required>
                                    <option value="FORKLIFT">Forklift / Unit</option>
                                    <option value="ATTACHMENT">Attachment</option>
                                    <option value="CHARGER">Charger</option>
                                    <option value="BATTERY">Baterai</option>
                                    <option value="SPAREPART">Sparepart</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" id="unitSelectCol">
                            <div class="mb-3">
                                <label class="form-label">Pilih Unit (jika Forklift)</label>
                                <select class="form-select" id="unitSelect" name="unit_id">
                                    <option value="">-- Unit Utama (Opsional) --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="componentIdRow" style="display:none;">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" id="componentIdLabel">Pilih Komponen</label>
                                <select class="form-select" name="component_id" id="componentIdSelect">
                                    <option value="">-- Pilih Komponen --</option>
                                </select>
                                <small class="text-muted">Pilih komponen spesifik yang dipindahkan</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Lokasi Asal <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="origin_location" id="originLocationInput" placeholder="Contoh: Workshop POS 1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipe Asal <span class="text-danger">*</span></label>
                                <select class="form-select" name="origin_type" id="originTypeSelect" onchange="onOriginTypeChange()" required>
                                    <option value="POS_1">POS 1 (Workshop Utama)</option>
                                    <option value="POS_2">POS 2</option>
                                    <option value="POS_3">POS 3</option>
                                    <option value="POS_4">POS 4</option>
                                    <option value="POS_5">POS 5</option>
                                    <option value="WAREHOUSE">Gudang</option>
                                    <option value="CUSTOMER_SITE">Lokasi Customer</option>
                                    <option value="OTHER">Lainnya (ketik manual)</option>
                                </select>
                            </div>
                            <div class="mb-3" id="originTypeOtherGroup" style="display:none;">
                                <label class="form-label">Tipe Asal (Lainnya)</label>
                                <input type="text" class="form-control" id="originTypeOtherInput" placeholder="Misal: Mills Area Jateng">
                                <small class="text-muted">Teks ini akan ikut dicatat di lokasi asal.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Lokasi Tujuan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="destination_location" id="destinationLocationInput" placeholder="Contoh: Workshop POS 2" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipe Tujuan <span class="text-danger">*</span></label>
                                <select class="form-select" name="destination_type" id="destinationTypeSelect" onchange="onDestinationTypeChange()" required>
                                    <option value="POS_1">POS 1 (Workshop Utama)</option>
                                    <option value="POS_2">POS 2</option>
                                    <option value="POS_3">POS 3</option>
                                    <option value="POS_4">POS 4</option>
                                    <option value="POS_5">POS 5</option>
                                    <option value="WAREHOUSE">Gudang</option>
                                    <option value="CUSTOMER_SITE">Lokasi Customer</option>
                                    <option value="OTHER">Lainnya (ketik manual)</option>
                                </select>
                            </div>
                            <div class="mb-3" id="destinationTypeOtherGroup" style="display:none;">
                                <label class="form-label">Tipe Tujuan (Lainnya)</label>
                                <input type="text" class="form-control" id="destinationTypeOtherInput" placeholder="Misal: Mills Area Jatim">
                                <small class="text-muted">Teks ini akan ikut dicatat di lokasi tujuan.</small>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitMovement()">
                    <i class="fas fa-save me-1"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye me-2 text-info"></i>Detail Surat Jalan</h5>
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
// Use BASE_URL from layout (base.php); avoid redeclaring to prevent SyntaxError
var _movementBaseUrl = (typeof BASE_URL !== 'undefined') ? BASE_URL : '<?= base_url() ?>';
const CSRF_TOKEN_NAME = '<?= csrf_token() ?>';
const CSRF_HASH = '<?= csrf_hash() ?>';

// Global AJAX setup for CSRF
$.ajaxSetup({
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    beforeSend: function(xhr, settings) {
        if (settings.type === 'POST' && settings.data instanceof FormData) {
            settings.data.append(CSRF_TOKEN_NAME, CSRF_HASH);
        } else if (settings.type === 'POST' && typeof settings.data === 'string') {
            settings.data += '&' + CSRF_TOKEN_NAME + '=' + CSRF_HASH;
        } else if (settings.type === 'POST') {
            settings.data = settings.data || {};
            settings.data[CSRF_TOKEN_NAME] = CSRF_HASH;
        }
    }
});

$(document).ready(function() {
    loadMovements();
    loadUnitsForSelect();

    // Inisialisasi Select2 untuk semua dropdown di modal create
    if ($.fn.select2) {
        $('#componentTypeSelect, #unitSelect, #componentIdSelect, #originTypeSelect, #destinationTypeSelect').select2({
            dropdownParent: $('#createModal'),
            width: '100%'
        });
    }

    // Set default datetime
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    $('input[name="movement_date"]').val(now.toISOString().slice(0, 16));
});

function loadMovements() {
    const status = $('#filterStatus').val();
    const originType = $('#filterOrigin').val();
    const destinationType = $('#filterDestination').val();
    const dateFrom = $('#filterDateFrom').val();
    const dateTo = $('#filterDateTo').val();

    $.ajax({
        url: _movementBaseUrl + 'warehouse/movements/getMovements',
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

        // Build item label: unit or component
        let itemLabel = '<span class="text-muted">-</span>';
        if (item.no_unit || item.no_unit_na) {
            itemLabel = (item.no_unit || item.no_unit_na) + '<br><small class="text-muted">' + (item.merk_unit || '') + '</small>';
        } else if (item.attachment_item_number) {
            itemLabel = item.attachment_item_number;
        } else if (item.charger_item_number) {
            itemLabel = item.charger_item_number;
        } else if (item.battery_item_number) {
            itemLabel = item.battery_item_number;
        }

        html += '<tr>';
        html += '<td><strong>' + (item.surat_jalan_number || '-') + '</strong></td>';
        html += '<td><small>' + (item.movement_number || '-') + '</small></td>';
        html += '<td>' + itemLabel + '</td>';
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
        'BATTERY': '<span class="badge badge-soft-green">Baterai</span>',
        'SPAREPART': '<span class="badge badge-soft-purple">Sparepart</span>'
    };
    return badges[type] || type || '-';
}

function onComponentTypeChange() {
    const type = $('#componentTypeSelect').val();
    const row = $('#componentIdRow');
    const select = $('#componentIdSelect');
    const label = $('#componentIdLabel');
    const unitCol = $('#unitSelectCol');

    if (type === 'FORKLIFT' || type === 'SPAREPART' || !type) {
        row.hide();
        select.val('');
        unitCol.show();
        return;
    }

    // For ATTACHMENT / CHARGER / BATTERY: show component dropdown, hide unit select
    unitCol.hide();
    $('#unitSelect').val('');
    row.show();

    const labels = { 'ATTACHMENT': 'Pilih Attachment', 'CHARGER': 'Pilih Charger', 'BATTERY': 'Pilih Baterai' };
    label.text(labels[type] || 'Pilih Komponen');

    select.html('<option value="">Memuat...</option>');
    select.prop('disabled', true);

    $.ajax({
        url: _movementBaseUrl + 'warehouse/movements/getComponentsByType',
        type: 'GET',
        data: { type: type },
        success: function(res) {
            let html = '<option value="">-- Pilih --</option>';
            if (res.success && res.data) {
                res.data.forEach(c => {
                    html += `<option value="${c.id}">${c.label} — ${c.location} [${c.status}]</option>`;
                });
            }
            select.html(html);
            select.prop('disabled', false);
        },
        error: function() {
            select.html('<option value="">Error memuat data</option>');
            select.prop('disabled', false);
        }
    });
}

function onOriginTypeChange() {
    const val = $('#originTypeSelect').val();
    if (val === 'OTHER') {
        $('#originTypeOtherGroup').show();
    } else {
        $('#originTypeOtherGroup').hide();
        $('#originTypeOtherInput').val('');
    }
}

function onDestinationTypeChange() {
    const val = $('#destinationTypeSelect').val();
    if (val === 'OTHER') {
        $('#destinationTypeOtherGroup').show();
    } else {
        $('#destinationTypeOtherGroup').hide();
        $('#destinationTypeOtherInput').val('');
    }
}

function showCreateModal() {
    $('#createModal').modal('show');
}

function loadUnitsForSelect() {
    $.ajax({
        url: _movementBaseUrl + 'warehouse/movements/getAvailableUnits',
        type: 'GET',
        success: function(res) {
            if (res.success) {
                let html = '<option value="">-- Unit Utama (Opsional) --</option>';
                res.data.forEach(unit => {
                    const labelNoUnit = unit.no_unit || unit.no_unit_na || ('UNIT-' + unit.id_inventory_unit);
                    const labelMerkModel = (unit.merk_unit || '') + ' ' + (unit.model_unit || '');
                    const sn = unit.serial_number ? (' | SN: ' + unit.serial_number) : '';
                    const cap = unit.tipe ? (' | ' + unit.tipe) : ''; // tipe biasanya berisi tipe+jenis/kategori kapasitas
                    html += '<option value="' + unit.id_inventory_unit + '">' +
                            labelNoUnit + ' - ' + labelMerkModel + sn + cap +
                            '</option>';
                });
                $('#unitSelect').html(html);
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

    // Jika tipe asal/tujuan = OTHER dan user isi teks lain, gabungkan ke lokasi
    const originType = formData.get('origin_type');
    const originOther = $('#originTypeOtherInput').val().trim();
    if (originType === 'OTHER' && originOther) {
        const base = $('#originLocationInput').val().trim();
        const combined = base ? (base + ' - ' + originOther) : originOther;
        formData.set('origin_location', combined);
    }

    const destType = formData.get('destination_type');
    const destOther = $('#destinationTypeOtherInput').val().trim();
    if (destType === 'OTHER' && destOther) {
        const base = $('#destinationLocationInput').val().trim();
        const combined = base ? (base + ' - ' + destOther) : destOther;
        formData.set('destination_location', combined);
    }

    $.ajax({
        url: _movementBaseUrl + 'warehouse/movements/createMovement',
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
                const now = new Date();
                now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                $('input[name="movement_date"]').val(now.toISOString().slice(0, 16));

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
        url: _movementBaseUrl + 'warehouse/movements/getMovementDetail/' + id,
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
    let actions = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>';

    if (movement.status === 'DRAFT') {
        actions += '<button class="btn btn-primary" onclick="startMovement(' + movement.id + ')"><i class="fas fa-truck me-1"></i>Jalankan</button>';
        actions += '<button class="btn btn-danger" onclick="cancelMovement(' + movement.id + ')"><i class="fas fa-times me-1"></i>Batal</button>';
    } else if (movement.status === 'IN_TRANSIT') {
        actions += '<button class="btn btn-success" onclick="confirmArrival(' + movement.id + ')"><i class="fas fa-check me-1"></i>Konfirmasi Tiba</button>';
    }

    $('#detailActions').html(actions);
    $('#detailModal').modal('show');
}

function startMovement(id) {
    OptimaConfirm.generic({
        title: 'Mulai Pengiriman',
        icon: 'question',
        html:
            '<div class="mb-3 text-start">' +
            '<label class="form-label">Nama Driver <span class="text-danger">*</span></label>' +
            '<input id="optimaDriverName" class="form-control" placeholder="Nama driver">' +
            '</div>' +
            '<div class="mb-3 text-start">' +
            '<label class="form-label">No. Kendaraan <span class="text-danger">*</span></label>' +
            '<input id="optimaVehicleNumber" class="form-control" placeholder="Contoh: B 1234 ABC">' +
            '</div>' +
            '<div class="text-start">' +
            '<label class="form-label">Catatan (opsional)</label>' +
            '<textarea id="optimaNotes" class="form-control" rows="2" placeholder="Catatan pengiriman..."></textarea>' +
            '</div>',
        confirmText: '<i class="fas fa-truck me-1"></i> Mulai Kirim',
        cancelText: 'Batal',
        confirmButtonColor: 'primary',
        onConfirm: function() {
            var elDriver = document.getElementById('optimaDriverName');
            var elVehicle = document.getElementById('optimaVehicleNumber');
            var elNotes = document.getElementById('optimaNotes');

            var driverName = (elDriver && elDriver.value) ? elDriver.value.trim() : '';
            var vehicleNumber = (elVehicle && elVehicle.value) ? elVehicle.value.trim() : '';
            var notes = (elNotes && elNotes.value) ? elNotes.value : '';
            if (!driverName || !vehicleNumber) {
                OptimaNotify.warning('Nama driver dan no. kendaraan wajib diisi', 'Validasi');
                return;
            }

            $.ajax({
                url: _movementBaseUrl + 'warehouse/movements/startMovement/' + id,
                type: 'POST',
                data: {
                    driver_name: driverName,
                    vehicle_number: vehicleNumber,
                    notes: notes
                },
                success: function(res) {
                    if (res.success) {
                        $('#detailModal').modal('hide');
                        loadMovements();
                        OptimaNotify.success('Movement dimulai! Driver: ' + driverName);
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
        cancelText: typeof window.lang === 'function' ? window.lang('cancel') : 'Batal',
        onConfirm: function() {
            $.ajax({
                url: _movementBaseUrl + 'warehouse/movements/confirmArrival/' + id,
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
        cancelText: typeof window.lang === 'function' ? window.lang('back') : 'Kembali',
        onConfirm: function() {
            $.ajax({
                url: _movementBaseUrl + 'warehouse/movements/cancelMovement/' + id,
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
