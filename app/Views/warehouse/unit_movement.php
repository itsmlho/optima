<?= $this->extend('layouts/base') ?>

<?php
/**
 * Surat Jalan / Unit Movement - Warehouse
 * BADGE/CARD: Optima stat-card bg-*-soft for stats; badge-soft-* in JS (movement/type).
 */
$stats = $stats ?? [];
$location_types = $location_types ?? [];
$component_types = $component_types ?? [];
$movement_purposes = $movement_purposes ?? [];
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
    <div class="d-flex gap-2"></div>
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
                    <i class="fas fa-sync me-1"></i>Reset
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card shadow-sm">
    <div class="card-header bg-light d-flex align-items-center justify-content-between">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Surat Jalan</h5>
        <button class="btn btn-primary btn-sm" onclick="showCreateModal()">
            <i class="fas fa-plus me-1"></i>Buat Surat Jalan
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="movementTable">
                <thead class="bg-light">
                    <tr>
                        <th>No. SJ</th>
                        <th>No. Movement</th>
                        <th>Barang</th>
                        <th>Rute</th>
                        <th>Tanggal</th>
                        <th>Driver</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-spinner fa-spin me-2"></i>Memuat data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Modal: lebar + body scrollable (header/footer tetap) -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus me-2 text-primary"></i>Buat Surat Jalan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createForm">
                    <input type="hidden" name="component_type" value="FORKLIFT">
                    <input type="hidden" name="unit_id" value="">
                    <input type="hidden" name="component_id" value="">

                    <div class="mb-3">
                        <label class="form-label">Tipe Surat Jalan <span class="text-danger">*</span></label>
                        <select class="form-select" name="movement_purpose" id="movementPurposeSelect" onchange="onMovementPurposeChange()" required>
                            <?php foreach ($movement_purposes as $code => $label): ?>
                                <option value="<?= esc($code) ?>" <?= $code === 'INTERNAL_TRANSFER' ? 'selected' : '' ?>><?= esc($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="alert alert-warning py-2 small mb-3 d-none" id="scrapPurposeHint">
                        <strong>Jual scrab:</strong> setelah semua checkpoint selesai, setiap <strong>unit (Forklift)</strong> pada SJ ini akan otomatis berstatus <strong>SOLD</strong> di inventory.
                    </div>

                    <div class="alert alert-light border mb-3">
                        <div class="fw-semibold mb-1">Alur pengisian:</div>
                        <div class="small text-muted">1) Isi rute perjalanan (asal -> transit -> tujuan), 2) isi daftar barang yang dibawa.</div>
                    </div>

                    <div class="border rounded p-3 mb-3">
                        <div class="fw-semibold mb-2"><i class="fas fa-route me-1 text-primary"></i>Rute Perjalanan</div>
                        <div class="small text-muted mb-3">
                            <strong>Lokasi Asal / Tujuan</strong> = nama titik yang dipakai semua pihak (contoh: &ldquo;Gate Loading Gudang&rdquo;, &ldquo;Workshop POS 2&rdquo;, &ldquo;Lobby Customer X&rdquo;) — boleh gedung, ruangan, atau nama area; ini yang muncul di SJ/cetak dan diisi satpam per <em>titik rute</em>.
                            <strong>Tipe Asal / Tujuan</strong> = klasifikasi internal perusahaan (POS 1–5 = area operasi bernomor, <strong>bukan</strong> wajib ada pos satpam bernama &ldquo;POS 1&rdquo;; Gudang/Customer/Lainnya untuk pelaporan &amp; filter). Pilih tipe yang paling mendekati, lalu tulis lokasi bebas yang jelas di kolom lokasi.
                        </div>

                        <div class="row">
                            <div class="col-md-7">
                                <div class="mb-3">
                                    <label class="form-label">Lokasi Asal <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="origin_location" id="originLocationInput" placeholder="Contoh: Area Loading POS 1" required>
                                </div>
                            </div>
                            <div class="col-md-5">
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
                                        <option value="OTHER">Lainnya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3" id="originTypeOtherGroup" style="display:none;">
                                <label class="form-label">Keterangan Tipe Asal (Lainnya)</label>
                                <input type="text" class="form-control" id="originTypeOtherInput" placeholder="Misal: Site Proyek A">
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label mb-0">Transit (opsional)</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addTransitRow()">
                                    <i class="fas fa-plus me-1"></i>Tambah Transit
                                </button>
                            </div>
                            <div id="transitContainer"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-7">
                                <div class="mb-3">
                                    <label class="form-label">Lokasi Tujuan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="destination_location" id="destinationLocationInput" placeholder="Contoh: Workshop POS 2 - Gate Utama" required>
                                </div>
                            </div>
                            <div class="col-md-5">
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
                                        <option value="OTHER">Lainnya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3" id="destinationTypeOtherGroup" style="display:none;">
                                <label class="form-label">Keterangan Tipe Tujuan (Lainnya)</label>
                                <input type="text" class="form-control" id="destinationTypeOtherInput" placeholder="Misal: Site Proyek B">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="destinationRecipientInput">Nama penerima di tujuan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="destination_recipient_name" id="destinationRecipientInput" maxlength="120" placeholder="Petugas / penerima barang di lokasi tujuan" required>
                        </div>
                    </div>

                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-semibold"><i class="fas fa-boxes me-1 text-primary"></i>Barang yang Dibawa</div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItemRow()">
                                <i class="fas fa-plus me-1"></i>Tambah Barang
                            </button>
                        </div>
                        <div class="small text-muted mb-2">Untuk tipe <strong>Forklift / Unit</strong>, pilih unit dari dropdown pencarian.</div>
                        <div id="movementItemsContainer"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Perpindahan <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" name="movement_date" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Nama Driver</label>
                                <input type="text" class="form-control" name="driver_name" placeholder="Nama driver">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">No. Kendaraan</label>
                                <input type="text" class="form-control" name="vehicle_number" placeholder="Contoh: B 1234 ABC">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Jenis Kendaraan</label>
                                <input type="text" class="form-control" name="vehicle_type" placeholder="Mis. Pickup, Box, Motor">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alasan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Alasan pengiriman / perpindahan barang..." required></textarea>
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
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
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
const _guardSjBaseUrl = '<?= base_url('surat-jalan') ?>';

/** Label titik rute untuk tampilan (nilai DB tetap ORIGIN / TRANSIT / DESTINATION). */
function stopTypeDisplayLabel(code) {
    const u = String(code || '').toUpperCase();
    if (u === 'ORIGIN') return 'Asal';
    if (u === 'DESTINATION') return 'Tujuan';
    if (u === 'TRANSIT') return 'Transit';
    return code || '-';
}

// Global AJAX setup for CSRF
$.ajaxSetup({
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    beforeSend: function(xhr, settings) {
        if (settings.type === 'POST' && settings.data instanceof FormData) {
            settings.data.append(CSRF_TOKEN_NAME, CSRF_HASH);
        } else if (settings.type === 'POST' && settings.contentType && String(settings.contentType).indexOf('application/json') !== -1) {
            try {
                const parsed = JSON.parse(settings.data || '{}');
                parsed[CSRF_TOKEN_NAME] = CSRF_HASH;
                settings.data = JSON.stringify(parsed);
            } catch (e) {
                // keep original payload
            }
        } else if (settings.type === 'POST' && typeof settings.data === 'string') {
            settings.data += '&' + CSRF_TOKEN_NAME + '=' + CSRF_HASH;
        } else if (settings.type === 'POST') {
            settings.data = settings.data || {};
            settings.data[CSRF_TOKEN_NAME] = CSRF_HASH;
        }
    }
});

/** Bootstrap 5 modal (layout tidak memuat plugin jQuery .modal()) */
function optimaBsModalShow(elementId) {
    var el = document.getElementById(elementId);
    if (!el) {
        return;
    }
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        bootstrap.Modal.getOrCreateInstance(el).show();
    } else if (window.jQuery && typeof jQuery.fn.modal === 'function') {
        jQuery('#' + elementId).modal('show');
    }
}

function optimaBsModalHide(elementId) {
    var el = document.getElementById(elementId);
    if (!el) {
        return;
    }
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        var inst = bootstrap.Modal.getInstance(el);
        if (inst) {
            inst.hide();
        }
    } else if (window.jQuery && typeof jQuery.fn.modal === 'function') {
        jQuery('#' + elementId).modal('hide');
    }
}

/** Buka lembar SJ di tab baru dan picu dialog cetak browser (autoprint=1). */
function openSuratJalanPrintPreview(movementId) {
    var url = _movementBaseUrl + 'warehouse/movements/printMovement/' + movementId + '?autoprint=1';
    var w = window.open(url, '_blank', 'noopener,noreferrer');
    if (!w) {
        if (window.OptimaNotify) {
            OptimaNotify.warning('Izinkan pop-up untuk pratinjau cetak surat jalan.');
        } else {
            alert('Izinkan pop-up untuk pratinjau cetak surat jalan.');
        }
    }
}

function copyTextFallback(text) {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.setAttribute('readonly', '');
    ta.style.position = 'absolute';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    try {
        document.execCommand('copy');
    } finally {
        document.body.removeChild(ta);
    }
}

function copyDetailDash(val) {
    var t = String(val == null ? '' : val).trim();
    return t === '' ? '-' : t;
}

/** Teks untuk dibagikan ke grup satpam (label jelas per baris). */
function copyDetailSjVerificationToClipboard() {
    var sj = copyDetailDash($('#detailModal').data('copySj'));
    var code = copyDetailDash($('#detailModal').data('copyCode'));
    var driver = copyDetailDash($('#detailModal').data('copyDriver'));
    var vehicle = copyDetailDash($('#detailModal').data('copyVehicle'));
    var vehicleType = copyDetailDash($('#detailModal').data('copyVehicleType'));
    var notes = copyDetailDash($('#detailModal').data('copyNotes'));
    var recipient = copyDetailDash($('#detailModal').data('copyRecipient'));
    var guardLink = copyDetailDash($('#detailModal').data('copyGuardLink'));

    var text = [
        'No. SJ : ' + sj,
        'Kode Verifikasi : ' + code,
        'Nama penerima (tujuan): ' + recipient,
        'Driver: ' + driver,
        'No. Kendaraan: ' + vehicle,
        'Jenis Kendaraan: ' + vehicleType,
        'Alasan: ' + notes
    ].join('\n');
    if (guardLink !== '-') {
        text += '\nLink Satpam: ' + guardLink;
    }

    if (sj === '-' && code === '-') {
        if (window.OptimaNotify) {
            OptimaNotify.warning('Tidak ada data untuk disalin');
        }
        return;
    }

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            if (window.OptimaNotify) {
                OptimaNotify.success('Info surat jalan disalin (siap kirim ke grup)');
            }
        }).catch(function() {
            copyTextFallback(text);
            if (window.OptimaNotify) {
                OptimaNotify.success('Info surat jalan disalin');
            }
        });
    } else {
        copyTextFallback(text);
        if (window.OptimaNotify) {
            OptimaNotify.success('Info surat jalan disalin');
        }
    }
}

function copyGuardLinkOnly() {
    var guardLink = copyDetailDash($('#detailModal').data('copyGuardLink'));
    if (guardLink === '-') {
        if (window.OptimaNotify) {
            OptimaNotify.warning('Link satpam belum tersedia');
        }
        return;
    }
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(guardLink).then(function() {
            if (window.OptimaNotify) {
                OptimaNotify.success('Link satpam berhasil disalin');
            }
        }).catch(function() {
            copyTextFallback(guardLink);
            if (window.OptimaNotify) {
                OptimaNotify.success('Link satpam berhasil disalin');
            }
        });
    } else {
        copyTextFallback(guardLink);
        if (window.OptimaNotify) {
            OptimaNotify.success('Link satpam berhasil disalin');
        }
    }
}

$(document).ready(function() {
    $(document).on('click', '#btnCopySjAndCode', function(e) {
        e.preventDefault();
        copyDetailSjVerificationToClipboard();
    });
    $(document).on('click', '#btnCopyGuardLink', function(e) {
        e.preventDefault();
        copyGuardLinkOnly();
    });

    loadMovements();
    initMovementItemUI();
    onMovementPurposeChange();

    // Select2 dasar untuk field kategori lokasi
    if ($.fn.select2) {
        $('#movementPurposeSelect, #originTypeSelect, #destinationTypeSelect').select2({
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
            $('#movementTable tbody').html('<tr><td colspan="8" class="text-center text-danger py-4">Error memuat data</td></tr>');
        }
    });
}

function renderMovementTable(data) {
    if (!data || data.length === 0) {
        $('#movementTable tbody').html('<tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data</td></tr>');
        return;
    }

    let html = '';
    data.forEach(item => {
        const statusBadge = getMovementStatusBadge(item.status);
        const date = new Date(item.movement_date).toLocaleDateString('id-ID');

        const itemsCount = parseInt(item.total_items || 0, 10) || 0;
        const routeText = item.last_stop_name ? (item.origin_location + ' → ' + item.last_stop_name) : (item.origin_location + ' → ' + item.destination_location);
        // Build item label: unit or component
        let itemLabel = '<span class="text-muted">-</span>';
        if (item.no_unit || item.no_unit_na) {
            itemLabel = (item.no_unit_na || item.no_unit) + '<br><small class="text-muted">' + (item.merk_unit || '') + '</small>';
        } else if (item.component_label) {
            itemLabel = item.component_label;
        }

        html += '<tr>';
        html += '<td><strong>' + (item.surat_jalan_number || '-') + '</strong></td>';
        html += '<td><small>' + (item.movement_number || '-') + '</small></td>';
        html += '<td>' + itemLabel + '<br><small class="text-muted">Items: ' + itemsCount + ' · ' + getComponentBadge(item.component_type) + ' · ' + getMovementPurposeBadge(item.movement_purpose) + '</small></td>';
        html += '<td>' + routeText + '<br><small class="text-muted">Checkpoint: ' + (item.last_checkpoint_status || '-') + '</small></td>';
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
        'FORKLIFT':   '<span class="badge badge-soft-blue">Forklift</span>',
        'ATTACHMENT': '<span class="badge badge-soft-cyan">Attachment</span>',
        'CHARGER':    '<span class="badge badge-soft-yellow">Charger</span>',
        'BATTERY':    '<span class="badge badge-soft-green">Baterai</span>',
        'FORK':       '<span class="badge badge-soft-orange">Fork</span>',
        'SPAREPART':  '<span class="badge badge-soft-purple">Sparepart</span>',
        'OTHERS':     '<span class="badge badge-soft-gray">Others</span>'
    };
    return badges[type] || type || '-';
}

function getMovementPurposeBadge(purpose) {
    if (!purpose || purpose === 'INTERNAL_TRANSFER') {
        return '<span class="badge badge-soft-cyan">Pindah</span>';
    }
    if (purpose === 'SCRAP_SALE') {
        return '<span class="badge badge-soft-orange">Scrab</span>';
    }
    return '<span class="badge badge-soft-gray">' + purpose + '</span>';
}

function onMovementPurposeChange() {
    const v = ($('#movementPurposeSelect').val() || '').toUpperCase();
    if (v === 'SCRAP_SALE') {
        $('#scrapPurposeHint').removeClass('d-none');
    } else {
        $('#scrapPurposeHint').addClass('d-none');
    }
}

function onComponentTypeChange() {
    const type = $('#componentTypeSelect').val();
    const row = $('#componentIdRow');
    const select = $('#componentIdSelect');
    const label = $('#componentIdLabel');
    const unitCol = $('#unitSelectCol');

    if (type === 'FORKLIFT' || !type) {
        // Forklift: show unit selector (optionally link a unit), hide component dropdown
        row.hide();
        select.val('');
        unitCol.show();
        return;
    }

    if (type === 'OTHERS') {
        unitCol.hide();
        row.hide();
        $('#unitSelect').val('');
        return;
    }
    // For non-FORKLIFT types: hide unit select, show component dropdown
    unitCol.hide();
    const $u = $('#unitSelect');
    $u.val('');
    if ($u.hasClass('select2-hidden-accessible')) {
        $u.trigger('change');
    }
    row.show();

    const labels = {
        'ATTACHMENT': 'Pilih Attachment',
        'CHARGER':    'Pilih Charger',
        'BATTERY':    'Pilih Baterai',
        'FORK':       'Pilih Fork',
        'SPAREPART':  'Pilih Sparepart'
    };
    label.text(labels[type] || 'Pilih Komponen');

    select.html('<option value="">Memuat...</option>');
    select.prop('disabled', true);

    $.ajax({
        url: _movementBaseUrl + 'warehouse/movements/getComponentsByType',
        type: 'GET',
        data: { type: type },
        success: function(res) {
            let html = '<option value="">-- Pilih --</option>';
            if (res.success && res.data && res.data.length > 0) {
                res.data.forEach(c => {
                    const loc = c.location ? ` — ${c.location}` : '';
                    const status = c.status ? ` [${c.status}]` : '';
                    html += `<option value="${c.id}">${c.label}${loc}${status}</option>`;
                });
            } else {
                html = '<option value="">-- Tidak ada data --</option>';
            }
            select.html(html);
            select.prop('disabled', false);
            if ($.fn.select2 && select.hasClass('select2-hidden-accessible')) {
                select.trigger('change');
            }
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
    if ($('#movementItemsContainer .movement-item-row').length === 0) {
        addItemRow();
    }
    optimaBsModalShow('createModal');
}

function loadUnitsForSelect() {
    const $sel = $('#unitSelect');
    if (!$sel.length) return;
    if ($sel.hasClass('select2-hidden-accessible')) {
        try {
            $sel.select2('destroy');
        } catch (e) { /* ignore */ }
    }
    const Ou = window.OptimaUnitSelect2;
    if ($.fn.select2 && Ou && typeof Ou.templateResult === 'function') {
        $sel.select2({
            dropdownParent: $('#createModal'),
            width: '100%',
            placeholder: '-- Unit Utama (Opsional) --',
            allowClear: true,
            minimumInputLength: 1,
            theme: 'bootstrap-5',
            ajax: {
                url: _movementBaseUrl + 'warehouse/movements/getAvailableUnits',
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return { q: params.term || '' };
                },
                processResults: function(res) {
                    const out = [];
                    if (res.success && Array.isArray(res.data)) {
                        res.data.forEach(function(unit) {
                            out.push({
                                id: String(unit.id_inventory_unit),
                                text: unit.no_unit || unit.no_unit_na || ('UNIT-' + unit.id_inventory_unit),
                                no_unit: unit.no_unit || unit.no_unit_na,
                                serial_number: unit.serial_number || '',
                                merk: unit.merk_unit || '',
                                model_unit: unit.model_unit || '',
                                jenis: unit.tipe || '',
                                lokasi: unit.lokasi || unit.lokasi_unit || ''
                            });
                        });
                    }
                    return { results: out };
                }
            },
            templateResult: function(i) { return Ou.templateResult(i, {}); },
            templateSelection: function(i) { return Ou.templateSelection(i, {}); },
            escapeMarkup: function(m) { return m; }
        });
    }
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

    const itemsPayload = collectItemsPayload();
    if (!itemsPayload.length) {
        if (window.OptimaNotify) OptimaNotify.warning('Minimal 1 barang harus diisi');
        else alert('Minimal 1 barang harus diisi');
        return;
    }
    const firstItem = itemsPayload[0] || {};

    const payload = {
        unit_id: firstItem.unit_id || null,
        component_id: firstItem.component_id || null,
        component_type: firstItem.component_type || 'FORKLIFT',
        origin_location: formData.get('origin_location'),
        origin_type: formData.get('origin_type'),
        destination_location: formData.get('destination_location'),
        destination_recipient_name: (formData.get('destination_recipient_name') || '').trim(),
        destination_type: formData.get('destination_type'),
        movement_date: formData.get('movement_date'),
        driver_name: formData.get('driver_name') || '',
        vehicle_number: formData.get('vehicle_number') || '',
        vehicle_type: formData.get('vehicle_type') || '',
        notes: formData.get('notes') || '',
        movement_purpose: formData.get('movement_purpose') || 'INTERNAL_TRANSFER',
        items: itemsPayload,
        stops: collectStopsPayload()
    };

    $.ajax({
        url: _movementBaseUrl + 'warehouse/movements/createMovement',
        type: 'POST',
        data: JSON.stringify(payload),
        contentType: 'application/json',
        success: function(res) {
            if (res.success) {
                optimaBsModalHide('createModal');
                form.reset();
                $('#movementPurposeSelect').val('INTERNAL_TRANSFER').trigger('change');
                onMovementPurposeChange();
                loadMovements();

                // Set datetime again
                const now = new Date();
                now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                $('input[name="movement_date"]').val(now.toISOString().slice(0, 16));

                var okMsg = 'Surat Jalan berhasil dibuat!\nNo. Movement: ' + res.data.movement_number + '\nNo. SJ: ' + (res.data.surat_jalan_number || '-');
                if (res.data.verification_code) {
                    okMsg += '\nKode verifikasi (untuk satpam): ' + res.data.verification_code;
                }
                if (window.OptimaNotify) OptimaNotify.success(okMsg);
                else alert(okMsg);
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
    var mid = parseInt(id, 10);
    if (!mid || mid < 1) {
        if (window.OptimaNotify) {
            OptimaNotify.error('ID movement tidak valid.');
        } else {
            alert('ID movement tidak valid.');
        }
        return;
    }
    $.ajax({
        url: _movementBaseUrl + 'warehouse/movements/getMovementDetail/' + mid,
        type: 'GET',
        success: function(res) {
            if (res.success && res.data && res.data.movement) {
                try {
                    showMovementDetailModal(res.data);
                } catch (e) {
                    console.error(e);
                    if (window.OptimaNotify) {
                        OptimaNotify.error('Gagal menampilkan detail.');
                    } else {
                        alert('Gagal menampilkan detail.');
                    }
                }
            } else if (window.OptimaNotify) {
                OptimaNotify.error(res.message || 'Data tidak ditemukan.');
            } else {
                alert(res.message || 'Data tidak ditemukan.');
            }
        },
        error: function() {
            if (window.OptimaNotify) {
                OptimaNotify.error('Gagal memuat detail surat jalan.');
            } else {
                alert('Gagal memuat detail surat jalan.');
            }
        }
    });
}

function formatMovementDate(isoStr, withTime) {
    if (!isoStr) {
        return '-';
    }
    const d = new Date(isoStr);
    if (Number.isNaN(d.getTime())) {
        return String(isoStr);
    }
    return withTime ? d.toLocaleString('id-ID') : d.toLocaleDateString('id-ID');
}

/** Escape text for safe HTML insertion */
function escDetailHtml(s) {
    if (s === null || s === undefined) {
        return '';
    }
    const d = document.createElement('div');
    d.textContent = String(s);
    return d.innerHTML;
}

function showMovementDetailModal(data) {
    const movement = data.movement;
    const unit = data.unit;
    const items = data.items || [];
    const stops = data.stops || [];

    const statusBadge = getMovementStatusBadge(movement.status);
    const componentBadge = getComponentBadge(movement.component_type);
    const date = formatMovementDate(movement.movement_date, true);
    const createdDate = formatMovementDate(movement.created_at, true);
    const confirmedDate = movement.confirmed_at ? formatMovementDate(movement.confirmed_at, true) : '-';

    const hasCode = !!movement.verification_code;
    const leftCol = hasCode ? 'col-md-8' : 'col-12';
    const guardUrl = (_guardSjBaseUrl || '').replace(/\/$/, '');
    const sjNo = String(movement.surat_jalan_number || '').trim();
    const verifyCode = String(movement.verification_code || '').trim();
    const hasGuardLink = !!(guardUrl && sjNo && verifyCode);
    const guardLink = hasGuardLink
        ? (guardUrl + '?surat_jalan_number=' + encodeURIComponent(sjNo) + '&verification_code=' + encodeURIComponent(verifyCode))
        : '';

    let content = '<div class="row g-3 align-items-start">';
    content += '<div class="' + leftCol + '">';
    content += '<div class="row g-2 small">';
    content += '<div class="col-sm-4 text-sm-end text-muted">No. SJ</div><div class="col-sm-8"><strong class="fs-6">' + escDetailHtml(movement.surat_jalan_number || '-') + '</strong></div>';
    content += '<div class="col-sm-4 text-sm-end text-muted">No. Movement</div><div class="col-sm-8">' + escDetailHtml(movement.movement_number || '-') + '</div>';
    content += '<div class="col-sm-4 text-sm-end text-muted">Tipe Komponen</div><div class="col-sm-8">' + componentBadge + '</div>';
    content += '<div class="col-sm-4 text-sm-end text-muted">Tipe Surat Jalan</div><div class="col-sm-8">' + getMovementPurposeBadge(movement.movement_purpose) + '</div>';
    content += '<div class="col-sm-4 text-sm-end text-muted">Status</div><div class="col-sm-8">' + statusBadge + '</div>';
    content += '<div class="col-sm-4 text-sm-end text-muted">Tanggal</div><div class="col-sm-8">' + escDetailHtml(date) + '</div>';
    content += '<div class="col-sm-4 text-sm-end text-muted">Dibuat Oleh</div><div class="col-sm-8">' + escDetailHtml(movement.creator_name || '-') + '</div>';
    content += '<div class="col-sm-4 text-sm-end text-muted">Tanggal Dibuat</div><div class="col-sm-8">' + escDetailHtml(createdDate) + '</div>';
    content += '</div></div>';

    if (hasCode) {
        content += '<div class="col-md-4">';
        content += '<div class="border rounded-3 p-3 bg-light text-center h-100">';
        content += '<div class="text-muted small mb-1">Kode verifikasi (satpam)</div>';
        content += '<div class="display-5 fw-bold text-danger font-monospace lh-sm py-1 user-select-all">' + escDetailHtml(String(movement.verification_code)) + '</div>';
        content += '<p class="small text-muted mb-2 mb-md-3">Konfirmasi satpam</p>';
        content += '<button type="button" class="btn btn-outline-primary btn-sm w-100 mb-2" id="btnCopyGuardLink"><i class="fas fa-link me-1"></i>Salin link satpam</button>';
        content += '<button type="button" class="btn btn-primary btn-sm w-100" id="btnCopySjAndCode"><i class="fas fa-copy me-1"></i>Salin info untuk satpam</button>';
        content += '</div></div>';
    }

    content += '</div><hr class="my-3">';

    content += '<div class="row">';
    content += '<div class="col-md-6"><div class="alert alert-secondary mb-0">';
    content += '<h6 class="fw-bold">Asal:</h6>';
    content += '<p class="mb-1"><strong>Lokasi:</strong> ' + movement.origin_location + '</p>';
    content += '<p class="mb-0"><strong>Tipe:</strong> ' + movement.origin_type + '</p>';
    content += '</div></div>';
    content += '<div class="col-md-6"><div class="alert alert-info mb-0">';
    content += '<h6 class="fw-bold">Tujuan:</h6>';
    content += '<p class="mb-1"><strong>Lokasi:</strong> ' + movement.destination_location + '</p>';
    content += '<p class="mb-1"><strong>Nama penerima:</strong> ' + escDetailHtml(movement.destination_recipient_name || '-') + '</p>';
    content += '<p class="mb-0"><strong>Tipe:</strong> ' + movement.destination_type + '</p>';
    content += '</div></div>';
    content += '</div><hr>';

    content += '<div class="alert alert-warning mb-0">';
    content += '<h6 class="fw-bold">Detail Pengiriman:</h6>';
    content += '<p class="mb-1"><strong>Driver:</strong> ' + (movement.driver_name || '-') + '</p>';
    content += '<p class="mb-1"><strong>No. Kendaraan:</strong> ' + (movement.vehicle_number || '-') + '</p>';
    content += '<p class="mb-1"><strong>Jenis Kendaraan:</strong> ' + (movement.vehicle_type || '-') + '</p>';
    content += '<p class="mb-0"><strong>Alasan:</strong> ' + (movement.notes || '-') + '</p>';
    content += '</div>';

    if (items.length > 0) {
        content += '<hr><h6 class="fw-bold">Daftar Barang</h6><ul class="mb-2">';
        items.forEach(function(it, i) {
            const label = (it.print_description || it.component_type || '-') + ' | qty ' + (it.qty || 1);
            content += '<li>#' + (i + 1) + ' - ' + label + '</li>';
        });
        content += '</ul>';
    }
    if (stops.length > 0) {
        content += '<h6 class="fw-bold">Rute</h6><ol class="mb-0">';
        stops.forEach(function(st) {
            content += '<li>' + (st.location_name || '-') + ' <small class="text-muted">(' + stopTypeDisplayLabel(st.stop_type) + ')</small></li>';
        });
        content += '</ol>';
    }

    if (movement.confirmed_at) {
        content += '<hr><div class="alert alert-success mb-0">';
        content += '<h6 class="fw-bold">Konfirmasi Penerimaan:</h6>';
        content += '<p class="mb-1"><strong>Dikonfirmasi Oleh:</strong> ' + (movement.confirmer_name || '-') + '</p>';
        content += '<p class="mb-0"><strong>Tanggal:</strong> ' + confirmedDate + '</p>';
        content += '</div>';
    }

    $('#detailContent').html(content);

    $('#detailModal').data({
        copySj: String(movement.surat_jalan_number || '').trim(),
        copyCode: String(movement.verification_code || '').trim(),
        copyRecipient: movement.destination_recipient_name,
        copyDriver: movement.driver_name,
        copyVehicle: movement.vehicle_number,
        copyVehicleType: movement.vehicle_type,
        copyNotes: movement.notes,
        copyGuardLink: guardLink
    });

    // Action buttons
    let actions = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>';
    actions += '<button type="button" class="btn btn-outline-dark" onclick="openSuratJalanPrintPreview(' + movement.id + ')"><i class="fas fa-print me-1"></i>Cetak SJ</button>';

    if (movement.status === 'DRAFT') {
        actions += '<button class="btn btn-primary" onclick="startMovement(' + movement.id + ')"><i class="fas fa-truck me-1"></i>Jalankan</button>';
        actions += '<button class="btn btn-danger" onclick="cancelMovement(' + movement.id + ')"><i class="fas fa-times me-1"></i>Batal</button>';
    } else if (movement.status === 'IN_TRANSIT') {
        actions += '<button class="btn btn-success" onclick="confirmArrival(' + movement.id + ')"><i class="fas fa-check me-1"></i>Konfirmasi Tiba</button>';
    }

    $('#detailActions').html(actions);
    optimaBsModalShow('detailModal');
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
            '<label class="form-label">Alasan (opsional)</label>' +
            '<textarea id="optimaNotes" class="form-control" rows="2" placeholder="Alasan / catatan pengiriman..."></textarea>' +
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
                        optimaBsModalHide('detailModal');
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
                        optimaBsModalHide('detailModal');
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
                        optimaBsModalHide('detailModal');
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

function initMovementItemUI() {
    if ($('#movementItemsContainer .movement-item-row').length === 0) {
        addItemRow();
    }
}

function addItemRow() {
    const idx = Date.now();
    const html = `
        <div class="border rounded p-2 mb-2 movement-item-row" data-row="${idx}">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-lg-2">
                    <label class="form-label form-label-sm mb-1">Tipe Barang</label>
                    <select class="form-select form-select-sm item-type" onchange="onItemTypeChange(${idx}, this.value)">
                        <option value="FORKLIFT">Forklift / Unit</option>
                        <option value="ATTACHMENT">Attachment</option>
                        <option value="CHARGER">Charger</option>
                        <option value="BATTERY">Baterai</option>
                        <option value="FORK">Fork</option>
                        <option value="SPAREPART">Sparepart</option>
                        <option value="OTHERS">Others</option>
                    </select>
                </div>
                <div class="col-12 col-lg-5">
                    <div class="item-unit-wrap">
                        <label class="form-label form-label-sm mb-1">Pilih Barang / Unit</label>
                        <select class="form-select form-select-sm item-unit" data-kind="unit"></select>
                    </div>
                    <div class="item-others-wrap" style="display:none;">
                        <label class="form-label form-label-sm mb-1">Keterangan barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm item-others-notes" maxlength="5000" placeholder="Nama / jenis barang (Others)">
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <label class="form-label form-label-sm mb-1">Qty</label>
                    <input type="number" class="form-control form-control-sm item-qty" value="1" min="1">
                </div>
                <div class="col-6 col-lg-3 text-lg-end">
                    <label class="form-label form-label-sm mb-1 d-none d-lg-block">&nbsp;</label>
                    <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeItemRow(${idx})"><i class="fas fa-trash me-1"></i>Hapus</button>
                </div>
            </div>
            <div class="row g-2 mt-1 item-component-row" style="display:none;">
                <div class="col-12">
                    <label class="form-label form-label-sm mb-1">Pilih Komponen</label>
                    <select class="form-select form-select-sm item-component" data-kind="component">
                        <option value="">-- Pilih komponen --</option>
                    </select>
                </div>
            </div>
        </div>`;
    $('#movementItemsContainer').append(html);
    setupItemUnitSelect(idx);
}

function removeItemRow(idx) {
    $('[data-row="' + idx + '"]').remove();
}

function setupItemUnitSelect(idx) {
    const $el = $('[data-row="' + idx + '"] .item-unit');
    const Ou = window.OptimaUnitSelect2;
    if (!$el.length || !$.fn.select2) return;
    $el.select2({
        dropdownParent: $('#createModal'),
        width: '100%',
        placeholder: 'Cari unit...',
        minimumInputLength: 1,
        ajax: {
            url: _movementBaseUrl + 'warehouse/movements/getAvailableUnits',
            dataType: 'json',
            delay: 300,
            data: function(params){ return { q: params.term || '' }; },
            processResults: function(res){
                const out = [];
                if (res.success && Array.isArray(res.data)) {
                    res.data.forEach(function(u){
                        out.push({
                            id: String(u.id_inventory_unit),
                            text: [u.no_unit || u.no_unit_na || ('UNIT-'+u.id_inventory_unit), (u.merk_unit || ''), (u.model_unit || ''), (u.serial_number ? ('SN:' + u.serial_number) : '')].filter(Boolean).join(' - '),
                            no_unit: u.no_unit || u.no_unit_na || ('UNIT-'+u.id_inventory_unit),
                            serial_number: u.serial_number || '',
                            merk: u.merk_unit || '',
                            model_unit: u.model_unit || ''
                        });
                    });
                }
                return { results: out };
            }
        },
        templateResult: function(i){ return Ou && Ou.templateResult ? Ou.templateResult(i,{}) : i.text; },
        templateSelection: function(i){
            if (!i || i.loading) return i.text || '';
            const no = i.no_unit || i.text || '';
            const mm = [i.merk || '', i.model_unit || ''].filter(Boolean).join(' ');
            const sn = i.serial_number ? ('SN:' + i.serial_number) : '';
            return [no, mm, sn].filter(Boolean).join(' | ');
        },
        escapeMarkup: function(m){ return m; }
    });
}

function onItemTypeChange(idx, type) {
    const $row = $('[data-row="' + idx + '"]');
    const $comp = $row.find('.item-component');
    const $unitWrap = $row.find('.item-unit-wrap');
    const $othersWrap = $row.find('.item-others-wrap');
    const $compRow = $row.find('.item-component-row');
    if (type === 'FORKLIFT') {
        $unitWrap.show();
        $compRow.hide();
        $othersWrap.hide();
        $row.find('.item-others-notes').val('');
        return;
    }
    if (type === 'OTHERS') {
        $unitWrap.hide();
        $compRow.hide();
        $othersWrap.show();
        return;
    }
    $unitWrap.hide();
    $compRow.show();
    $othersWrap.hide();
    $row.find('.item-others-notes').val('');
    loadComponentOptions($comp, type);
}

function loadComponentOptions($target, type) {
    $.get(_movementBaseUrl + 'warehouse/movements/getComponentsByType', { type: type }, function(res) {
        let html = '<option value="">-- pilih --</option>';
        if (res.success && Array.isArray(res.data)) {
            res.data.forEach(function(c){ html += '<option value="' + c.id + '">' + c.label + '</option>'; });
        }
        $target.html(html);
    });
}

function addTransitRow() {
    const html = `
        <div class="row g-2 mb-2 transit-row">
            <div class="col-md-5"><input class="form-control form-control-sm transit-location" placeholder="Lokasi transit"></div>
            <div class="col-md-5">
                <select class="form-select form-select-sm transit-type">
                    <option value="POS_1">POS 1</option><option value="POS_2">POS 2</option><option value="POS_3">POS 3</option>
                    <option value="POS_4">POS 4</option><option value="POS_5">POS 5</option><option value="WAREHOUSE">Gudang</option>
                    <option value="CUSTOMER_SITE">Customer Site</option><option value="OTHER">Other</option>
                </select>
            </div>
            <div class="col-md-2"><button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="$(this).closest('.transit-row').remove()"><i class="fas fa-times"></i></button></div>
        </div>`;
    $('#transitContainer').append(html);
}

function collectItemsPayload() {
    const items = [];
    $('#movementItemsContainer .movement-item-row').each(function() {
        const $row = $(this);
        const type = ($row.find('.item-type').val() || 'FORKLIFT').toUpperCase();
        const othersNotes = type === 'OTHERS'
            ? String($row.find('.item-others-notes').val() || '').trim()
            : '';
        items.push({
            component_type: type,
            unit_id: type === 'FORKLIFT' ? ($row.find('.item-unit').val() || null) : null,
            component_id: (type !== 'FORKLIFT' && type !== 'OTHERS') ? ($row.find('.item-component').val() || null) : null,
            qty: parseInt($row.find('.item-qty').val() || '1', 10),
            item_notes: type === 'OTHERS' ? othersNotes : null
        });
    });
    return items;
}

function collectStopsPayload() {
    const stops = [{
        stop_type: 'ORIGIN',
        location_name: $('#originLocationInput').val() || '',
        location_type: $('#originTypeSelect').val() || 'OTHER'
    }];
    $('#transitContainer .transit-row').each(function() {
        stops.push({
            stop_type: 'TRANSIT',
            location_name: $(this).find('.transit-location').val() || '',
            location_type: $(this).find('.transit-type').val() || 'OTHER'
        });
    });
    stops.push({
        stop_type: 'DESTINATION',
        location_name: $('#destinationLocationInput').val() || '',
        location_type: $('#destinationTypeSelect').val() || 'OTHER'
    });
    return stops;
}
</script>
<?= $this->endSection() ?>
