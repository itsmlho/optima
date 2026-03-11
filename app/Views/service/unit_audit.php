<?= $this->extend('layouts/base') ?>

<?php
/**
 * Unit Audit (Service)
 * Alur: Pilih Customer → Pilih Lokasi (badge status audit) → Unit dalam accordion per lokasi (seperti detail kontrak)
 */
$stats = $stats ?? [];
helper('ui');
?>

<?= $this->section('content') ?>
<style>
/* Soft active state untuk lokasi terpilih — teks tetap terbaca, tombol konsisten */
.unit-audit .location-item-header { cursor: pointer; transition: background-color .15s ease; }
.unit-audit .location-item-header:hover { background-color: rgba(13, 110, 253, 0.06); }
.unit-audit .location-item-selected .location-item-header {
    background-color: rgba(13, 110, 253, 0.12) !important;
    border-left: 3px solid #0d6efd;
    color: inherit;
}
.unit-audit .location-item-selected .location-item-header .text-muted { color: #5c636a !important; }
.unit-audit .location-item-selected .location-item-header .badge.bg-secondary { background-color: #6c757d !important; color: #fff; }
/* Tombol action tetap outline agar terbaca di background soft */
.unit-audit .location-item-header .btn { flex-shrink: 0; }

/* Select2 z-index: seluruh widget di belakang modal (backdrop 1050, modal 1055) */
.select2-container { z-index: 1 !important; }
.select2-container--open { z-index: 1040 !important; }
.select2-dropdown { z-index: 1040 !important; }

/* Modal Verifikasi: teks dan tabel terbaca */
#verifikasiModal .modal-body label,
#verifikasiModal .modal-body h6 { color: #212529 !important; }
#verifikasiModal #vfUnitTable th { color: #212529; }
#verifikasiModal .cursor-pointer { cursor: pointer; }

/* Hasil per unit: badge tidak wrap dan highlighting sebaris */
#verifikasiModal .vf-hasil-cell .badge { white-space: nowrap; }
#verifikasiModal tr.vf-row-sesuai { background-color: #e9f7ef !important; }
#verifikasiModal tr.vf-row-tidak { background-color: #fde2e1 !important; }

/* Modal Alasan: font sedikit lebih besar agar nyaman dibaca */
#vfAlasanModal .modal-body { font-size: 0.95rem; }
#vfAlasanModal .form-label.small,
#vfAlasanModal .form-check-label.small,
#vfAlasanModal p.small { font-size: 0.95rem !important; }
</style>

<!-- Page Header -->
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('service') ?>">Service</a></li>
                <li class="breadcrumb-item active">Unit Audit</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-search me-2 text-primary"></i>Unit Audit
        </h4>
        <p class="text-muted small mb-0">Audit unit di lokasi customer — pilih lokasi, print form untuk mekanik, input hasil verifikasi, ajukan ke Marketing</p>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body py-3 d-flex justify-content-between align-items-center">
                <div><div class="text-xs text-white-50">Total Request</div><div class="fs-4 fw-bold"><?= $stats['total'] ?? 0 ?></div></div>
                <i class="fas fa-clipboard-list fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body py-3 d-flex justify-content-between align-items-center">
                <div><div class="text-xs">Menunggu Approval</div><div class="fs-4 fw-bold"><?= $stats['submitted'] ?? 0 ?></div></div>
                <i class="fas fa-clock fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body py-3 d-flex justify-content-between align-items-center">
                <div><div class="text-xs text-white-50">Approved</div><div class="fs-4 fw-bold"><?= $stats['approved'] ?? 0 ?></div></div>
                <i class="fas fa-check-circle fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body py-3 d-flex justify-content-between align-items-center">
                <div><div class="text-xs text-white-50">Rejected</div><div class="fs-4 fw-bold"><?= $stats['rejected'] ?? 0 ?></div></div>
                <i class="fas fa-times-circle fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<!-- Step 1: Pilih Customer (Select2 + total lokasi + badge audit) -->
<div class="card shadow-sm mb-3">
    <div class="card-header bg-light">
        <h6 class="mb-0 fw-semibold"><i class="fas fa-building me-2 text-primary"></i>Langkah 1 — Pilih Customer</h6>
    </div>
    <div class="card-body pb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-9">
                <select class="form-select" id="customerSelect" style="width:100%;" onchange="onCustomerChange()">
                    <option value="">-- Pilih Customer --</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-outline-secondary w-100" onclick="loadCustomerList()">
                    <i class="fas fa-sync me-1"></i>Refresh
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Step 2: Lokasi Customer + Unit di dalam tiap lokasi (accordion seperti detail kontrak) -->
<div id="locationSection" style="display:none;" class="card shadow-sm mb-4 unit-audit">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Langkah 2 — Pilih Lokasi (klik untuk lihat unit)</h6>
        <span class="text-muted small" id="locationCount"></span>
    </div>
    <div class="card-body p-0">
        <div id="locationList">
            <div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Memuat lokasi...</div>
        </div>
    </div>
</div>

<!-- Riwayat Audit Request -->
<div class="card shadow-sm">
    <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0 fw-semibold"><i class="fas fa-history me-2"></i>Riwayat Audit Request</h6>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" id="filterStatus" style="width:160px;" onchange="loadAuditHistory()">
                <option value="">Semua Status</option>
                <option value="SUBMITTED">Menunggu Approval</option>
                <option value="APPROVED">Approved</option>
                <option value="REJECTED">Rejected</option>
            </select>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0" id="historyTable">
                <thead class="table-light">
                    <tr>
                        <th>No. Audit</th>
                        <th>Customer</th>
                        <th>Lokasi</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="7" class="text-center py-4"><i class="fas fa-spinner fa-spin me-2"></i>Memuat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ═══ MODAL: Verifikasi Lokasi (per unit dalam grup lokasi) ═══════════════════════════════ -->
<div class="modal fade" id="verifikasiModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom">
                <h5 class="modal-title fw-semibold"><i class="fas fa-clipboard-check me-2 text-primary"></i>Verifikasi Lokasi — Input Hasil per Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="vfCustomerId">
                <input type="hidden" id="vfLocationId">

                <div class="alert alert-light border mb-3" id="vfLocationInfo">
                    <strong>Lokasi:</strong> <span id="vfLocationName">—</span><br>
                    <span class="small text-muted" id="vfLocationAddr"></span>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tanggal Audit <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="vfAuditDate" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Mekanik yang Audit <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="vfMechanicName" placeholder="Nama mekanik yang melakukan audit di lapangan">
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="text-dark fw-semibold mb-2"><i class="fas fa-list-check me-2 text-primary"></i>Hasil per Unit — pilih Sesuai atau Tidak Sesuai untuk tiap unit</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0" id="vfUnitTable">
                            <thead class="table-light">
                                <tr>
                                    <th>No Unit</th>
                                    <th>Serial</th>
                                    <th>Merk / Model</th>
                                    <th class="text-center" style="width:140px;">Hasil di Lapangan</th>
                                    <th class="text-center" style="width:180px;">Alasan tidak sesuai</th>
                                    <th style="width:180px;">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="vfUnitTableBody">
                                <tr><td colspan="6" class="text-center py-3 text-muted">Memuat unit...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark">Ringkasan Hasil Audit (laporan)</label>
                    <div id="vfRingkasanResult" class="py-2 px-3 rounded border bg-light">
                        <span class="badge badge-soft-gray" id="vfRingkasanBadge">—</span>
                        <span class="small text-muted ms-2" id="vfRingkasanText">Pilih hasil per unit di atas</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitVerifikasi()">
                    <i class="fas fa-paper-plane me-1"></i>Kirim ke Marketing
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ═══ MODAL: Alasan Ketidaksesuaian (per unit saat klik Tidak) ═══════════════════════════════ -->
<div class="modal fade" id="vfAlasanModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h6 class="modal-title fw-semibold"><i class="fas fa-exclamation-triangle me-2"></i>Alasan Ketidaksesuaian <span class="text-danger">*</span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-2" id="vfAlasanUnitLabel">Unit: —</p>
                <label class="form-label fw-semibold small">Pilih alasan <span class="text-danger">*</span></label>
                <div class="mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="vfAlasanReason" id="arLocasi" value="LOCATION_MISMATCH">
                        <label class="form-check-label small" for="arLocasi"><i class="fas fa-map-marker-alt text-warning me-1"></i>Lokasi Unit Salah</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="vfAlasanReason" id="arUnitBeda" value="UNIT_SWAP">
                        <label class="form-check-label small" for="arUnitBeda"><i class="fas fa-exchange-alt text-info me-1"></i>Unit Berbeda</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="vfAlasanReason" id="arSpare" value="MARK_SPARE">
                        <label class="form-check-label small" for="arSpare"><i class="fas fa-tag text-secondary me-1"></i>Tandai Spare</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="vfAlasanReason" id="arMissing" value="UNIT_MISSING">
                        <label class="form-check-label small" for="arMissing"><i class="fas fa-question-circle text-danger me-1"></i>Unit Tidak Ada</label>
                    </div>
                </div>

                <div id="vfAlasanLokasiWrapper" class="mb-2 d-none">
                    <label class="form-label fw-semibold small mb-1">Lokasi yang seharusnya</label>
                    <select class="form-select form-select-sm" id="vfAlasanLokasiSelect">
                        <option value="">-- Pilih Customer Location --</option>
                    </select>
                </div>

                <div id="vfAlasanUnitWrapper" class="mb-2 d-none">
                    <label class="form-label fw-semibold small mb-1">Unit yang seharusnya</label>
                    <select class="form-select form-select-sm" id="vfAlasanUnitSelect">
                        <option value="">-- Pilih Unit --</option>
                    </select>
                    <div class="form-text small">Misalnya unit lama breakdown lalu diganti unit lain.</div>
                </div>

                <div id="vfAlasanInfoWrapper" class="mb-2 d-none">
                    <p class="small text-muted mb-0" id="vfAlasanInfoText"></p>
                </div>

                <label class="form-label fw-semibold small">Keterangan (opsional)</label>
                <textarea class="form-control form-control-sm" id="vfAlasanKeterangan" rows="2" placeholder="Temuan untuk unit ini..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning btn-sm" id="vfAlasanSimpan"><i class="fas fa-check me-1"></i>Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- ═══ MODAL: Tambah Unit (Ajuan ke Marketing) ═══════════════ -->
<div class="modal fade" id="tambahUnitModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Tambah Unit ke Lokasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="tuCustomerId">
                <input type="hidden" id="tuLocationId">
                <input type="hidden" id="tuKontrakId">

                <div class="alert alert-light border mb-3">
                    <strong>Lokasi:</strong> <span id="tuLocationName">—</span>
                    <br><span class="small text-muted" id="tuLocationKontrak"></span>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih Unit yang Akan Ditambahkan <span class="text-danger">*</span></label>
                    <select class="form-select" id="tuUnitSelect">
                        <option value="">-- Pilih Unit --</option>
                    </select>
                    <div class="form-text">Unit yang tersedia (belum dalam kontrak aktif)</div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Harga Sewa (Rp/bulan)</label>
                        <input type="number" class="form-control" id="tuHarga" placeholder="Opsional">
                    </div>
                    <div class="col-md-6 d-flex align-items-end pb-1">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tuIsSpare">
                            <label class="form-check-label" for="tuIsSpare">Unit ini adalah <strong>Spare</strong></label>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label fw-semibold">Catatan / Alasan Penambahan</label>
                    <textarea class="form-control" id="tuNotes" rows="3" placeholder="Jelaskan alasan penambahan unit..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="submitTambahUnit()">
                    <i class="fas fa-paper-plane me-1"></i>Ajukan ke Marketing
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ═══ MODAL: Detail Riwayat ═══════════════════════════════════ -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Detail Audit Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
const BASE = '<?= base_url() ?>';
let selectedCustomerId  = null;
let selectedLocationId  = null;
let selectedLocationData = {};

$(document).ready(function() {
    loadCustomerList();
    loadAuditHistory();
});

// ─── Customer (Select2 + total lokasi + badge) ───────────────────

function loadCustomerList() {
    $.get(BASE + 'service/unit_audit/getCustomersForUnitAudit', function(res) {
        if (!res.success) return;
        let html = '<option value="">-- Pilih Customer --</option>';
        res.data.forEach(c => {
            const totalLoc = parseInt(c.total_locations, 10) || 0;
            const belum = parseInt(c.locations_belum_audit, 10) || 0;
            const badge = c.audit_badge || 'belum_audit';
            const badgeLabel = badge === 'sudah_audit' ? 'Sudah audit' : (badge === 'sebagian' ? 'Sebagian' : 'Belum audit');
            const badgeClass = badge === 'sudah_audit' ? 'bg-success' : (badge === 'sebagian' ? 'bg-warning text-dark' : 'bg-secondary');
            html += `<option value="${c.id}" data-name="${escAttr(c.customer_name)}" data-code="${escAttr(c.customer_code||'')}" data-total-loc="${totalLoc}" data-belum="${belum}" data-badge="${badge}" data-badge-label="${escAttr(badgeLabel)}" data-badge-class="${badgeClass}">${esc(c.customer_name)} (${esc(c.customer_code||'')}) — ${totalLoc} lokasi${belum > 0 ? ', ' + belum + ' belum audit' : ''} — ${badgeLabel}</option>`;
        });
        const $sel = $('#customerSelect');
        const hadSelect2 = $sel.hasClass('select2-hidden-accessible');
        if (hadSelect2) $sel.select2('destroy');
        $sel.html(html);
        $sel.select2({
            placeholder: '-- Pilih Customer --',
            allowClear: true,
            width: '100%',
            templateResult: formatCustomerOption,
            templateSelection: formatCustomerSelection
        });
    });
}

function formatCustomerOption(opt) {
    if (!opt.id) return opt.text;
    const $opt = $(opt.element);
    const name = $opt.data('name') || opt.text;
    const code = $opt.data('code') || '';
    const totalLoc = $opt.data('total-loc') || 0;
    const belum = $opt.data('belum') || 0;
    const badgeLabel = $opt.data('badge-label') || '';
    const badgeClass = $opt.data('badge-class') || 'bg-secondary';
    return $('<span class="d-flex align-items-center flex-wrap gap-2"></span>')
        .append($('<strong></strong>').text(name + (code ? ' (' + code + ')' : '')))
        .append($('<span class="text-muted small"></span>').text(totalLoc + ' lokasi' + (belum > 0 ? ', ' + belum + ' belum audit' : '')))
        .append($('<span class="badge ' + badgeClass + ' small"></span>').text(badgeLabel));
}

function formatCustomerSelection(opt) {
    if (!opt.id) return opt.text;
    const $opt = $(opt.element);
    const name = $opt.data('name') || opt.text;
    const code = $opt.data('code') || '';
    const badgeLabel = $opt.data('badge-label') || '';
    const badgeClass = $opt.data('badge-class') || 'bg-secondary';
    return $('<span></span>').append($('<span class="me-2"></span>').text(name + (code ? ' (' + code + ')' : ''))).append($('<span class="badge ' + badgeClass + ' small"></span>').text(badgeLabel));
}

function onCustomerChange() {
    selectedCustomerId = $('#customerSelect').val();
    selectedLocationId = null;
    selectedLocationData = {};

    $('.location-item-units').collapse('hide');
    $('.location-item').removeClass('location-item-selected');

    if (!selectedCustomerId) {
        $('#locationSection').hide();
        return;
    }
    $('#locationSection').show();
    loadLocations();
}

// ─── Locations ──────────────────────────────────────────────────

function loadLocations() {
    $('#locationList').html('<div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Memuat lokasi...</div>');

    $.get(BASE + 'service/unit-audit/getLocationsWithAuditStatus/' + selectedCustomerId, function(res) {
        if (!res.success) {
            $('#locationList').html('<div class="alert alert-danger m-3">' + (res.message || 'Gagal memuat lokasi') + '</div>');
            return;
        }
        renderLocationList(res.data);
    }).fail(function() {
        $('#locationList').html('<div class="alert alert-danger m-3">Gagal memuat lokasi</div>');
    });
}

function renderLocationList(locations) {
    if (!locations || locations.length === 0) {
        $('#locationList').html('<div class="text-center py-4 text-muted">Tidak ada lokasi aktif</div>');
        $('#locationCount').text('0 lokasi');
        return;
    }
    $('#locationCount').text(locations.length + ' lokasi');

    let html = '';
    locations.forEach(loc => {
        const as = loc.audit_status;
        let statusBadge = '<span class="badge badge-soft-gray">Belum Audit</span>';
        let canVerify   = true;
        if (as) {
            if (as.status === 'PENDING_APPROVAL') {
                statusBadge = '<span class="badge badge-soft-yellow">Menunggu Approval</span>';
                canVerify   = false;
            } else if (as.status === 'APPROVED') {
                statusBadge = '<span class="badge badge-soft-green">Approved</span>';
                canVerify   = false;
            } else if (as.status === 'REJECTED') {
                statusBadge = '<span class="badge badge-soft-red">Rejected — Audit ulang</span>';
            } else if (['DRAFT','PRINTED','IN_PROGRESS','RESULTS_ENTERED'].includes(as.status)) {
                statusBadge = '<span class="badge badge-soft-blue">' + as.status + '</span>';
                canVerify   = false;
            }
        }

        const locDataAttr = escAttr(JSON.stringify({
            id: loc.id,
            location_name: loc.location_name,
            address: loc.address || '',
            no_kontrak_masked: loc.no_kontrak_masked || '',
            no_po_masked: loc.no_po_masked || '',
            periode_text: loc.periode_text || '',
            periode_status_text: loc.periode_status_text || '',
            total_units: loc.total_units || 0,
            last_audit_id: as ? as.audit_id : null,
        }));

        const isSelected = selectedLocationId == loc.id;
        const collapseId = 'loc-units-' + loc.id;
        html += `
        <div class="location-item border-bottom ${isSelected ? 'location-item-selected' : ''}" data-loc-id="${loc.id}">
            <div class="location-item-header list-group-item-action d-flex justify-content-between align-items-center flex-wrap gap-2 py-3 px-3" data-loc='${locDataAttr}' onclick="toggleLocationUnits(${loc.id}, this)">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-chevron-down text-muted small location-item-chevron" style="transition: transform .2s; width: 14px; transform: rotate(-90deg);"></i>
                    <div>
                        <strong>${esc(loc.location_name)}</strong>
                        <span class="ms-2 text-muted small">${loc.total_units || 0} unit</span>
                        <br><span class="small text-muted">Kontrak: ${esc(loc.no_kontrak_masked || '')} | PO: ${esc(loc.no_po_masked || '')}</span>
                        <br><span class="small">Periode: ${esc(loc.periode_text || '')} — <span class="badge bg-secondary">${esc(loc.periode_badge_text || loc.periode_status_text || '—')}</span></span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    ${statusBadge}
                    <button class="btn btn-sm btn-outline-secondary" onclick="event.stopPropagation(); printLocationForm(${loc.id})" title="Print Form Mekanik">
                        <i class="fas fa-print me-1"></i>Print
                    </button>
                    ${canVerify ? `
                    <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); openVerifikasi(${loc.id})" title="Input Hasil Verifikasi">
                        <i class="fas fa-clipboard-check me-1"></i>Verifikasi
                    </button>
                    ` : `
                    <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); openVerifikasi(${loc.id})" title="Buat verifikasi baru">
                        <i class="fas fa-redo me-1"></i>Audit Ulang
                    </button>
                    `}
                    <button class="btn btn-sm btn-outline-success" onclick="event.stopPropagation(); openTambahUnit(${loc.id})" title="Tambah Unit ke Lokasi">
                        <i class="fas fa-plus me-1"></i>Tambah Unit
                    </button>
                </div>
            </div>
            <div class="collapse location-item-units" id="${collapseId}" data-loc-id="${loc.id}">
                <div class="p-3 bg-light border-top">
                    <div class="loc-units-placeholder text-center py-3 text-muted small" data-loc-id="${loc.id}">
                        <i class="fas fa-spinner fa-spin me-2"></i>Memuat unit...
                    </div>
                    <div class="table-responsive loc-units-table" data-loc-id="${loc.id}" style="display:none;">
                        <table class="table table-sm table-hover mb-0 bg-white">
                            <thead class="table-light">
                                <tr><th>No Unit</th><th>S/N</th><th>Merk / Model</th><th>Status</th><th>Spare</th></tr>
                            </thead>
                            <tbody class="loc-units-tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>`;
    });
    $('#locationList').html(html);
    if (selectedLocationId) {
        const $collapse = $('#' + 'loc-units-' + selectedLocationId);
        if ($collapse.length && !$collapse.hasClass('show')) {
            $collapse.collapse('show');
            $collapse.siblings('.location-item-header').find('.location-item-chevron').css('transform', 'rotate(0deg)');
            loadUnitsIntoLocation(selectedLocationId);
        }
    }
}

function toggleLocationUnits(locationId, el) {
    const $item = $(el).closest('.location-item');
    const $collapse = $('#' + 'loc-units-' + locationId);
    const isOpening = !$collapse.hasClass('show');

    $('.location-item').removeClass('location-item-selected');
    $('.location-item-units').collapse('hide');
    $('.location-item-chevron').css('transform', 'rotate(-90deg)');

    if (isOpening) {
        $item.addClass('location-item-selected');
        $collapse.collapse('show');
        $item.find('.location-item-chevron').css('transform', 'rotate(0deg)');
        var raw = $(el).attr('data-loc');
        var locData = {};
        try {
            locData = (raw && typeof raw === 'string') ? JSON.parse(raw) : (raw || {});
        } catch (e) {
            locData = {};
        }
        selectedLocationId = locationId;
        selectedLocationData = locData;
        loadUnitsIntoLocation(locationId);
    } else {
        selectedLocationId = null;
        selectedLocationData = {};
    }
}

function loadUnitsIntoLocation(locationId) {
    const $placeholder = $('.loc-units-placeholder[data-loc-id="' + locationId + '"]');
    const $tableWrap = $('.loc-units-table[data-loc-id="' + locationId + '"]');
    const $tbody = $tableWrap.find('.loc-units-tbody');
    $placeholder.show();
    $tableWrap.hide();

    $.get(BASE + 'service/unit-audit/getLocationUnits/' + locationId, function(res) {
        $placeholder.hide();
        if (!res.success) {
            $tbody.html('<tr><td colspan="5" class="text-center text-danger py-3">' + (res.message || 'Gagal') + '</td></tr>');
            $tableWrap.show();
            return;
        }
        const units = res.data || [];
        if (units.length === 0) {
            $tbody.html('<tr><td colspan="5" class="text-center text-muted py-3">Tidak ada unit di lokasi ini</td></tr>');
        } else {
            let html = '';
            units.forEach(u => {
                const spare = u.is_spare == 1 ? '<span class="badge badge-soft-gray">Spare</span>' : '<span class="text-muted">—</span>';
                html += `<tr>
                    <td><strong>${esc(u.no_unit || '—')}</strong></td>
                    <td class="small text-muted">${esc(u.serial_number || '—')}</td>
                    <td>${esc((u.merk_unit || '') + ' ' + (u.model_unit || ''))}</td>
                    <td><span class="badge bg-${u.ku_status === 'ACTIVE' ? 'success' : 'warning'}">${u.ku_status || '—'}</span></td>
                    <td>${spare}</td>
                </tr>`;
            });
            $tbody.html(html);
        }
        $tableWrap.show();
    }).fail(function() {
        $placeholder.hide();
        $tbody.html('<tr><td colspan="5" class="text-center text-danger py-3">Gagal memuat unit</td></tr>');
        $tableWrap.show();
    });
}

// ─── Print per Lokasi ────────────────────────────────────────────

function printLocationForm(locationId) {
    const custId = selectedCustomerId || $('#customerSelect').val();
    if (!custId || !locationId) {
        alert('Pilih customer dan lokasi terlebih dahulu');
        return;
    }
    window.open(BASE + 'service/unit-audit/printLocationForm/' + custId + '/' + locationId, '_blank');
}

// ─── Verifikasi Modal ────────────────────────────────────────────

function openVerifikasi(locationId) {
    const custId = selectedCustomerId || $('#customerSelect').val();
    if (!custId) {
        alert('Pilih customer terlebih dahulu');
        return;
    }

    // Find location data from rendered list or selectedLocationData
    let locName = '', locAddr = '';
    const $row = $('[data-loc]').filter(function() {
        try { return JSON.parse($(this).attr('data-loc')).id == locationId; } catch(e) { return false; }
    });
    if ($row.length) {
        const d = JSON.parse($row.attr('data-loc'));
        locName = d.location_name || '';
        locAddr = d.address || '';
    }

    $('#vfCustomerId').val(custId);
    $('#vfLocationId').val(locationId);
    $('#vfLocationName').text(locName || 'Lokasi #' + locationId);
    $('#vfLocationAddr').text(locAddr);
    $('#vfAuditDate').val(new Date().toISOString().slice(0,10));
    $('#vfMechanicName').val('');
    $('#vfUnitTableBody').html('<tr><td colspan="6" class="text-center py-3"><i class="fas fa-spinner fa-spin me-2"></i>Memuat unit...</td></tr>');
    updateVfRingkasan(0, 0);
    $('#verifikasiModal').modal('show');

    $.get(BASE + 'service/unit-audit/getLocationUnits/' + locationId, function(res) {
        if (!res.success || !res.data || res.data.length === 0) {
            $('#vfUnitTableBody').html('<tr><td colspan="6" class="text-center py-3 text-muted">Tidak ada unit di lokasi ini</td></tr>');
            return;
        }
        let rows = '';
        res.data.forEach(u => {
            const uid = u.unit_id || u.id_inventory_unit || u.id;
            const noUnit = esc(u.no_unit || u.no_unit_na || '—');
            const serial = esc(u.serial_number || '—');
            const merk = esc((u.merk_unit || '') + ' ' + (u.model_unit || ''));
            rows += `<tr data-unit-id="${uid}" data-vf-result="sesuai" data-vf-reasons="[]" data-vf-keterangan="" data-vf-extra="{}">
                <td><strong>${noUnit}</strong></td>
                <td class="small">${serial}</td>
                <td>${merk}</td>
                <td class="text-center vf-hasil-cell">
                    <span class="badge badge-soft-green vf-btn-sesuai cursor-pointer" data-uid="${uid}" title="Sesuai">Sesuai</span>
                    <span class="badge badge-soft-gray vf-btn-tidak cursor-pointer" data-uid="${uid}" title="Tidak sesuai">Tidak sesuai</span>
                </td>
                <td class="small text-muted vf-alasan-cell">—</td>
                <td class="small text-muted vf-ket-cell">—</td>
            </tr>`;
        });
        $('#vfUnitTableBody').html(rows);
        bindVfUnitButtons();
        updateVfRingkasan(res.data.length, 0);
    }).fail(function() {
        $('#vfUnitTableBody').html('<tr><td colspan="6" class="text-center py-3 text-danger">Gagal memuat unit</td></tr>');
    });
}

var vfAlasanUnitId = null;
var vfAlasanExtraCache = {};

function bindVfUnitButtons() {
    $('#vfUnitTableBody').off('click', '.vf-btn-sesuai').on('click', '.vf-btn-sesuai', function() {
        const uid = $(this).data('uid');
        const $row = $('#vfUnitTableBody tr[data-unit-id="' + uid + '"]');
        $row.attr('data-vf-result', 'sesuai').attr('data-vf-reasons', '[]').attr('data-vf-keterangan', '');
        $row.removeClass('vf-row-tidak').addClass('vf-row-sesuai');
        $row.find('.vf-btn-sesuai').removeClass('badge-soft-gray').addClass('badge-soft-green');
        $row.find('.vf-btn-tidak').removeClass('badge-soft-red').addClass('badge-soft-gray');
        $row.find('.vf-alasan-cell').text('—');
        $row.find('.vf-ket-cell').text('—');
        updateVfRingkasanFromTable();
    });
    $('#vfUnitTableBody').off('click', '.vf-btn-tidak').on('click', '.vf-btn-tidak', function() {
        const uid = $(this).data('uid');
        const $row = $('#vfUnitTableBody tr[data-unit-id="' + uid + '"]');
        const noUnit = $row.find('td:first').text().trim();
        vfAlasanUnitId = uid;
        $('#vfAlasanUnitLabel').text('Unit: ' + noUnit);
        $('input[name="vfAlasanReason"]').prop('checked', false);
        $('#vfAlasanKeterangan').val('');
        resetVfAlasanExtra();
        var existingReasons = $row.attr('data-vf-reasons');
        var existingKet = $row.attr('data-vf-keterangan') || '';
        var existingExtra = $row.attr('data-vf-extra') || '{}';
        if (existingReasons && existingReasons !== '[]') {
            try {
                var arr = JSON.parse(existingReasons);
                if (arr.length > 0) {
                    var r = arr[0];
                    $('input[name="vfAlasanReason"][value="' + r + '"]').prop('checked', true);
                }
            } catch (e) {}
            $('#vfAlasanKeterangan').val(existingKet);
        }
        try {
            vfAlasanExtraCache = JSON.parse(existingExtra);
        } catch (e) {
            vfAlasanExtraCache = {};
        }
        onVfAlasanReasonChange();
        $('#vfAlasanModal').modal('show');
    });
}

function resetVfAlasanExtra() {
    $('#vfAlasanLokasiWrapper, #vfAlasanUnitWrapper, #vfAlasanInfoWrapper').addClass('d-none');
    $('#vfAlasanLokasiSelect').val('');
    $('#vfAlasanUnitSelect').val('');
    $('#vfAlasanInfoText').text('');
}

function onVfAlasanReasonChange() {
    resetVfAlasanExtra();
    const reason = $('input[name="vfAlasanReason"]:checked').val();
    if (!reason) return;
    const custId = selectedCustomerId || $('#vfCustomerId').val();
    const locId  = $('#vfLocationId').val();
    if (reason === 'LOCATION_MISMATCH') {
        $('#vfAlasanLokasiWrapper').removeClass('d-none');
        if (!custId) return;
        $.get(BASE + 'service/unit-audit/getLocationsForCustomer/' + custId, function(res) {
            if (!res.success || !res.data) return;
            let opts = '<option value="">-- Pilih Customer Location --</option>';
            res.data.forEach(function(l) {
                const disabled = String(l.id) === String(locId) ? ' disabled' : '';
                opts += `<option value="${l.id}"${disabled}>${esc(l.location_name || '')}</option>`;
            });
            $('#vfAlasanLokasiSelect').html(opts);
            if (vfAlasanExtraCache.target_location_id) {
                $('#vfAlasanLokasiSelect').val(String(vfAlasanExtraCache.target_location_id));
            }
        });
    } else if (reason === 'UNIT_SWAP') {
        $('#vfAlasanUnitWrapper').removeClass('d-none');
        const $sel = $('#vfAlasanUnitSelect');
        if ($sel.hasClass('select2-hidden-accessible')) {
            $sel.select2('destroy');
        }
        $sel.empty().append('<option value="">-- Pilih Unit --</option>');
        // Load semua unit inventory sekali via units-dropdown
        $.get(BASE + 'service/work-orders/units-dropdown', function(res) {
            if (!res.success || !Array.isArray(res.data)) return;
            res.data.forEach(function(unit) {
                const id = unit.id;
                const noUnit = unit.no_unit || unit.nomor_unit || ('UNIT-' + id);
                const jenis = unit.jenis || unit.tipe || '';
                const kapasitas = unit.kapasitas || '';
                const status = unit.status || '';
                const pelanggan = unit.pelanggan || 'Belum Ada Kontrak';
                const lokasi = unit.lokasi || 'N/A';
                // Simpan info lengkap di data-* agar bisa dipakai templateResult
                const text = noUnit;
                const opt = $('<option>')
                    .val(id)
                    .text(text)
                    .attr('data-no', noUnit)
                    .attr('data-jenis', jenis)
                    .attr('data-kapasitas', kapasitas)
                    .attr('data-status', status)
                    .attr('data-pelanggan', pelanggan)
                    .attr('data-lokasi', lokasi);
                $sel.append(opt);
            });

            $sel.select2({
                dropdownParent: $('#vfAlasanModal'),
                width: '100%',
                placeholder: '-- Pilih Unit --',
                templateResult: function (opt) {
                    if (!opt.id) return opt.text;
                    const $o = $(opt.element);
                    const no = $o.data('no') || opt.text;
                    const jenis = $o.data('jenis') || '';
                    const kapasitas = $o.data('kapasitas') || '';
                    const status = $o.data('status') || '';
                    const pelanggan = $o.data('pelanggan') || '';
                    const lokasi = $o.data('lokasi') || '';
                    const statusLabel = status || 'UNKNOWN';
                    const statusBadge = $('<span class="badge badge-soft-green me-1"></span>').text(statusLabel);
                    const locSpan = $('<span></span>').text('📍 ' + lokasi);
                    const $container = $('<div class="d-flex flex-column"></div>');
                    const line1 = $('<div class="fw-semibold"></div>').text('[' + opt.id + '] ' + no + (jenis ? ' ' + jenis : '') + (kapasitas ? ' ' + kapasitas : ''));
                    const line2 = $('<div class="small text-muted"></div>').append(statusBadge).append(locSpan);
                    $container.append(line1).append(line2);
                    return $container;
                },
                templateSelection: function (opt) {
                    if (!opt.id) return opt.text;
                    const $o = $(opt.element);
                    const no = $o.data('no') || opt.text;
                    const jenis = $o.data('jenis') || '';
                    const kapasitas = $o.data('kapasitas') || '';
                    return '[' + opt.id + '] ' + no + (jenis ? ' ' + jenis : '') + (kapasitas ? ' ' + kapasitas : '');
                }
            });

            if (vfAlasanExtraCache.target_unit_id) {
                $sel.val(String(vfAlasanExtraCache.target_unit_id)).trigger('change');
            }
        });
    } else if (reason === 'MARK_SPARE') {
        $('#vfAlasanInfoWrapper').removeClass('d-none');
        $('#vfAlasanInfoText').text('Unit ini akan diajukan sebagai spare sehingga tidak dihitung ke harga sewa kontrak.');
    } else if (reason === 'UNIT_MISSING') {
        $('#vfAlasanInfoWrapper').removeClass('d-none');
        $('#vfAlasanInfoText').text('Unit ini akan diajukan untuk dihapus dari kontrak dan status asetnya diperbarui.');
    }
}

$('input[name="vfAlasanReason"]').on('change', onVfAlasanReasonChange);

$('#vfAlasanSimpan').on('click', function() {
    const reasonInput = $('input[name="vfAlasanReason"]:checked');
    if (!reasonInput.length) {
        alert('Pilih satu alasan ketidaksesuaian');
        return;
    }
    const reason = reasonInput.val();
    const reasons = [reason];
    let extra = {};
    if (reason === 'LOCATION_MISMATCH') {
        const targetLoc = $('#vfAlasanLokasiSelect').val();
        if (!targetLoc) {
            alert('Pilih lokasi yang seharusnya untuk unit ini');
            return;
        }
        extra.target_location_id = targetLoc;
    } else if (reason === 'UNIT_SWAP') {
        const targetUnit = $('#vfAlasanUnitSelect').val();
        if (!targetUnit) {
            alert('Pilih unit yang seharusnya untuk kontrak ini');
            return;
        }
        extra.target_unit_id = targetUnit;
    } else if (reason === 'MARK_SPARE') {
        extra.mark_spare = true;
    } else if (reason === 'UNIT_MISSING') {
        extra.remove_from_contract = true;
    }
    const keterangan = $('#vfAlasanKeterangan').val().trim();
    if (!vfAlasanUnitId) return;
    const $row = $('#vfUnitTableBody tr[data-unit-id="' + vfAlasanUnitId + '"]');
    $row.attr('data-vf-result', 'tidak_sesuai');
    $row.attr('data-vf-reasons', JSON.stringify(reasons));
    $row.attr('data-vf-keterangan', keterangan);
    $row.attr('data-vf-extra', JSON.stringify(extra));
    $row.find('.vf-btn-sesuai').removeClass('badge-soft-green').addClass('badge-soft-gray');
    $row.find('.vf-btn-tidak').removeClass('badge-soft-gray').addClass('badge-soft-red');
    const reasonLabels = [];
    reasons.forEach(function(r) {
        var l = { LOCATION_MISMATCH: 'Lokasi salah', UNIT_SWAP: 'Unit beda', MARK_SPARE: 'Spare', UNIT_MISSING: 'Unit tidak ada' }[r] || r;
        reasonLabels.push(l);
    });
    $row.find('.vf-alasan-cell').text(reasonLabels.join(', ')).removeClass('text-muted');
    $row.find('.vf-ket-cell').text(keterangan || '—').removeClass('text-muted');
    $('#vfAlasanModal').modal('hide');
    vfAlasanUnitId = null;
    updateVfRingkasanFromTable();
});

function updateVfRingkasanFromTable() {
    const total = $('#vfUnitTableBody tr[data-unit-id]').length;
    let tidakCount = 0;
    $('#vfUnitTableBody tr[data-unit-id]').each(function() {
        if ($(this).attr('data-vf-result') === 'tidak_sesuai') tidakCount++;
    });
    updateVfRingkasan(total, total - tidakCount);
}

function updateVfRingkasan(total, sesuaiCount) {
    const $badge = $('#vfRingkasanBadge');
    const $text = $('#vfRingkasanText');
    if (total === 0) {
        $badge.removeClass('badge-soft-green badge-soft-red').addClass('badge-soft-gray').text('—');
        $text.text('Pilih hasil per unit di atas');
        return;
    }
    if (sesuaiCount === total) {
        $badge.removeClass('badge-soft-red badge-soft-gray').addClass('badge-soft-green').text('Sesuai');
        $text.text(total + '/' + total + ' unit sesuai kontrak');
    } else {
        $badge.removeClass('badge-soft-green badge-soft-gray').addClass('badge-soft-red').text('Tidak Sesuai');
        $text.text((total - sesuaiCount) + ' unit tidak sesuai');
    }
}

function submitVerifikasi() {
    const custId       = $('#vfCustomerId').val();
    const locId        = $('#vfLocationId').val();
    const auditDate    = $('#vfAuditDate').val();
    const mechanicName = $('#vfMechanicName').val().trim();

    if (!auditDate) { alert('Tanggal audit wajib diisi'); return; }
    if (!mechanicName) { alert('Mekanik yang audit wajib diisi'); return; }

    const items = [];
    let hasTidakSesuai = false;
    let invalidRow = null;
    $('#vfUnitTableBody tr[data-unit-id]').each(function() {
        const $row = $(this);
        const unitId = $row.attr('data-unit-id');
        const result = $row.attr('data-vf-result') || 'sesuai';
        if (result === 'tidak_sesuai') hasTidakSesuai = true;
        let reasons = [];
        let extra = {};
        try {
            reasons = JSON.parse($row.attr('data-vf-reasons') || '[]');
        } catch (e) {}
        try {
            extra = JSON.parse($row.attr('data-vf-extra') || '{}');
        } catch (e) {}
        const keterangan = $row.attr('data-vf-keterangan') || '';
        if (result === 'tidak_sesuai' && reasons.length === 0) {
            invalidRow = $row.find('td:first').text().trim();
        }
        items.push({ unit_id: unitId, result: result, reasons: reasons, keterangan: keterangan, extra: extra });
    });
    if (items.length === 0) return;
    if (invalidRow) {
        alert('Unit ' + invalidRow + ': pilih alasan ketidaksesuaian (klik Tidak lalu isi alasan).');
        return;
    }

    const fieldStatus = hasTidakSesuai ? 'tidak_sesuai' : 'sesuai';
    const formData = new FormData();
    formData.append('customer_id', custId);
    formData.append('customer_location_id', locId);
    formData.append('audit_date', auditDate);
    formData.append('mechanic_name', mechanicName);
    formData.append('field_status', fieldStatus);
    items.forEach((it, i) => {
        formData.append('items[' + i + '][unit_id]', it.unit_id);
        formData.append('items[' + i + '][result]', it.result);
        (it.reasons || []).forEach(function(r) { formData.append('items[' + i + '][reasons][]', r); });
        formData.append('items[' + i + '][keterangan]', it.keterangan || '');
    });

    const $btn = $('#verifikasiModal .btn-primary');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...');

    $.ajax({
        url: BASE + 'service/unit-audit/createAuditVerification',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            $btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i>Kirim ke Marketing');
            if (res.success) {
                $('#verifikasiModal').modal('hide');
                alert('✅ ' + res.message);
                loadLocations();
                loadAuditHistory();
            } else {
                alert('Error: ' + res.message);
            }
        },
        error: function() {
            $btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i>Kirim ke Marketing');
            alert('Terjadi kesalahan saat menyimpan');
        }
    });
}

// ─── Tambah Unit Modal ───────────────────────────────────────────

function openTambahUnit(locationId) {
    const custId = selectedCustomerId || $('#customerSelect').val();
    if (!custId) { alert('Pilih customer terlebih dahulu'); return; }

    let locName = '', locKontrak = '', kontrakId = '';
    const $row = $('[data-loc]').filter(function() {
        try { return JSON.parse($(this).attr('data-loc')).id == locationId; } catch(e) { return false; }
    });
    if ($row.length) {
        const d = JSON.parse($row.attr('data-loc'));
        locName = d.location_name || '';
        locKontrak = 'Kontrak: ' + (d.no_kontrak_masked || '—') + ' | ' + (d.periode_text || '');
    }

    $('#tuCustomerId').val(custId);
    $('#tuLocationId').val(locationId);
    $('#tuLocationName').text(locName || 'Lokasi #' + locationId);
    $('#tuLocationKontrak').text(locKontrak);
    $('#tuUnitSelect').html('<option value="">-- Memuat unit... --</option>');
    $('#tuHarga').val('');
    $('#tuIsSpare').prop('checked', false);
    $('#tuNotes').val('');

    // Load available units
    $.get(BASE + 'service/unit_audit/getAvailableUnits', function(res) {
        if (res.success) {
            let html = '<option value="">-- Pilih Unit --</option>';
            res.data.forEach(u => {
                html += `<option value="${u.id_inventory_unit}">${esc(u.no_unit || u.no_unit_na || 'UNIT-'+u.id_inventory_unit)} — ${esc(u.merk_unit||'')} ${esc(u.model_unit||'')} (${esc(u.pelanggan||'-')})</option>`;
            });
            $('#tuUnitSelect').html(html);
        }
    });

    // Try to get kontrak_id for location
    $.get(BASE + 'service/unit-audit/getLocationDetails/' + locationId, function(res) {
        if (res.success && res.data) {
            // we'll submit without kontrak_id; the backend will resolve it
        }
    });

    $('#tambahUnitModal').modal('show');
}

function submitTambahUnit() {
    const custId  = $('#tuCustomerId').val();
    const unitId  = $('#tuUnitSelect').val();
    const notes   = $('#tuNotes').val().trim();

    if (!unitId) { alert('Pilih unit terlebih dahulu'); return; }

    const formData = new FormData();
    formData.append('customer_id', custId);
    formData.append('request_type', 'ADD_UNIT');
    formData.append('customer_location_id', $('#tuLocationId').val());
    formData.append('proposed_unit_id', unitId);
    formData.append('proposed_harga_sewa', $('#tuHarga').val() || '');
    formData.append('proposed_is_spare', $('#tuIsSpare').is(':checked') ? '1' : '0');
    formData.append('notes', notes);

    const $btn = $('#tambahUnitModal .btn-success');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...');

    $.ajax({
        url: BASE + 'service/unit_audit/createAuditRequest',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            $btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i>Ajukan ke Marketing');
            if (res.success) {
                $('#tambahUnitModal').modal('hide');
                alert('✅ ' + res.message + '\nNo. Audit: ' + (res.data?.audit_number || ''));
                loadAuditHistory();
            } else {
                alert('Error: ' + res.message);
            }
        },
        error: function() {
            $btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i>Ajukan ke Marketing');
            alert('Terjadi kesalahan');
        }
    });
}

// ─── Riwayat Audit Request ───────────────────────────────────────

function loadAuditHistory() {
    const status   = $('#filterStatus').val();
    const custId   = selectedCustomerId || '';

    $.get(BASE + 'service/unit_audit/getAuditRequests', { status: status, customer_id: custId }, function(res) {
        if (res.success) renderHistoryTable(res.data);
    }).fail(function() {
        $('#historyTable tbody').html('<tr><td colspan="7" class="text-center text-danger py-4">Gagal memuat data</td></tr>');
    });
}

function renderHistoryTable(data) {
    if (!data || data.length === 0) {
        $('#historyTable tbody').html('<tr><td colspan="7" class="text-center text-muted py-4">Belum ada audit request</td></tr>');
        return;
    }
    let html = '';
    data.forEach(item => {
        const statusBadge = getStatusBadge(item.status);
        const typeLabel   = getTypeLabel(item.request_type);
        const date = item.created_at ? new Date(item.created_at).toLocaleDateString('id-ID') : '-';
        html += `<tr>
            <td class="small"><strong>${esc(item.audit_number || '-')}</strong></td>
            <td class="small">${esc(item.customer_name || '-')}</td>
            <td class="small">${esc(item.lokasi_kontrak || '-')}</td>
            <td>${typeLabel}</td>
            <td>${statusBadge}</td>
            <td class="small">${date}</td>
            <td><button class="btn btn-xs btn-outline-primary" onclick="viewDetail(${item.id})"><i class="fas fa-eye"></i></button></td>
        </tr>`;
    });
    $('#historyTable tbody').html(html);
}

function getStatusBadge(s) {
    const m = {
        'SUBMITTED': '<span class="badge badge-soft-yellow">Menunggu</span>',
        'APPROVED':  '<span class="badge badge-soft-green">Approved</span>',
        'REJECTED':  '<span class="badge badge-soft-red">Rejected</span>',
    };
    return m[s] || `<span class="badge badge-soft-gray">${s}</span>`;
}

function getTypeLabel(t) {
    const m = {
        'LOCATION_MISMATCH': '<span class="badge badge-soft-cyan">Lokasi Berbeda</span>',
        'UNIT_SWAP':         '<span class="badge badge-soft-blue">Tukar Unit</span>',
        'ADD_UNIT':          '<span class="badge badge-soft-green">Tambah Unit</span>',
        'MARK_SPARE':        '<span class="badge badge-soft-gray">Tandai Spare</span>',
        'UNIT_MISSING':      '<span class="badge badge-soft-red">Unit Hilang</span>',
        'OTHER':             '<span class="badge badge-soft-gray">Lainnya</span>',
    };
    return m[t] || t;
}

function viewDetail(id) {
    $.get(BASE + 'service/unit_audit/getAuditDetail/' + id, function(res) {
        if (res.success) showDetailModal(res.data);
    });
}

function showDetailModal(item) {
    const currentData  = JSON.parse(item.current_data  || '{}');
    const proposedData = JSON.parse(item.proposed_data || '{}');
    let html = `
        <div class="row mb-3">
            <div class="col-md-6"><strong>No. Audit:</strong> ${esc(item.audit_number)}</div>
            <div class="col-md-6"><strong>Status:</strong> ${getStatusBadge(item.status)}</div>
            <div class="col-md-6 mt-2"><strong>Customer:</strong> ${esc(item.customer_name || '-')}</div>
            <div class="col-md-6 mt-2"><strong>Kontrak:</strong> ${esc(item.no_kontrak || '-')}</div>
            <div class="col-md-6 mt-2"><strong>Diajukan:</strong> ${esc(item.submitter_name || '-')}</div>
            <div class="col-md-6 mt-2"><strong>Tanggal:</strong> ${item.created_at ? new Date(item.created_at).toLocaleString('id-ID') : '-'}</div>
        </div><hr>
        <div class="row">
            <div class="col-md-6">
                <div class="alert alert-secondary">
                    <h6 class="fw-bold">Data Saat Ini</h6>
                    <p class="mb-1"><strong>No Unit:</strong> ${esc(currentData.no_unit || '-')}</p>
                    <p class="mb-1"><strong>Serial:</strong> ${esc(currentData.serial || '-')}</p>
                    <p class="mb-0"><strong>Lokasi:</strong> ${esc(currentData.lokasi || '-')}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="alert alert-info">
                    <h6 class="fw-bold">Perubahan Diajukan</h6>
                    ${renderProposedData(item.request_type, proposedData)}
                </div>
            </div>
        </div>
        <div class="alert alert-light">
            <strong>Jenis:</strong> ${getTypeLabel(item.request_type)}<br>
            <strong>Catatan:</strong> ${esc(item.notes || '-')}
        </div>`;

    if (item.reviewed_by) {
        html += `<div class="alert alert-${item.status === 'APPROVED' ? 'success' : 'danger'}">
            <h6 class="fw-bold">Review Marketing</h6>
            <p class="mb-1"><strong>Oleh:</strong> ${esc(item.reviewer_name || '-')}</p>
            <p class="mb-1"><strong>Tanggal:</strong> ${item.reviewed_at ? new Date(item.reviewed_at).toLocaleString('id-ID') : '-'}</p>
            <p class="mb-0"><strong>Catatan:</strong> ${esc(item.review_notes || '-')}</p>
        </div>`;
    }
    $('#detailContent').html(html);
    $('#detailModal').modal('show');
}

function renderProposedData(type, data) {
    const m = {
        'LOCATION_MISMATCH': `<p class="mb-0"><strong>Lokasi Baru:</strong> ${esc(data.new_location || '-')}</p>`,
        'UNIT_SWAP':  `<p class="mb-1"><strong>Unit Pengganti ID:</strong> ${esc(data.new_unit_id || '-')}</p><p class="mb-0"><strong>Harga Sewa:</strong> ${data.harga_sewa ? 'Rp ' + parseInt(data.harga_sewa).toLocaleString('id-ID') : '-'}</p>`,
        'ADD_UNIT':   `<p class="mb-1"><strong>Unit ID:</strong> ${esc(data.unit_id || data.proposed_unit_id || '-')}</p><p class="mb-1"><strong>Spare:</strong> ${data.is_spare ? 'Ya' : 'Tidak'}</p><p class="mb-0"><strong>Harga:</strong> ${data.harga_sewa ? 'Rp '+parseInt(data.harga_sewa).toLocaleString('id-ID') : '-'}</p>`,
        'MARK_SPARE': '<p class="mb-0">Unit akan ditandai sebagai <strong>Spare</strong></p>',
        'UNIT_MISSING': `<p class="mb-0"><strong>Lokasi Terakhir:</strong> ${esc(data.last_known_location || '-')}</p>`,
    };
    return m[type] || `<p class="mb-0">${esc(data.description || '-')}</p>`;
}

// ─── Utils ───────────────────────────────────────────────────────

function esc(s) {
    if (s == null) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function escAttr(s) {
    return String(s).replace(/'/g, '&#39;').replace(/"/g, '&quot;');
}
</script>
<?= $this->endSection() ?>
