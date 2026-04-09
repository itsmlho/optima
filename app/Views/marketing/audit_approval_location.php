<?= $this->extend('layouts/base') ?>

<?php
/**
 * Audit Approval - Halaman Tunggal
 * Menggabungkan: Pengajuan Unit (ADD_UNIT, dll), Request Lokasi Baru, Approve Audit Lokasi
 * 
 * BADGE SYSTEM: Menggunakan Optima Badge Standards (optima-pro.css)
 * Direct CSS classes - tidak perlu JavaScript helper function
 * 
 * Quick Reference:
 * - Status DRAFT           → <span class="badge badge-soft-gray">Draft</span>
 * - Status PRINTED         → <span class="badge badge-soft-cyan">Printed</span>
 * - Status IN_PROGRESS     → <span class="badge badge-soft-yellow">In Progress</span>
 * - Status RESULTS_ENTERED → <span class="badge badge-soft-blue">Results Entered</span>
 * - Status PENDING_APPROVAL → <span class="badge badge-soft-orange">Pending</span>
 * - Status APPROVED        → <span class="badge badge-soft-green">Approved</span>
 * - Status REJECTED        → <span class="badge badge-soft-red">Rejected</span>
 * - Result MATCH           → <span class="badge badge-soft-green">MATCH</span>
 * - Result EXTRA_UNIT      → <span class="badge badge-soft-yellow">Extra</span>
 * - Result MISMATCH_*      → <span class="badge badge-soft-orange">Beda</span>
 * 
 * See optima-pro.css line ~2030 for complete badge standards
 */
$stats = $stats ?? [];
?>

<?= $this->section('content') ?>

<!-- Statistics Cards -->
<div class="row mt-3 mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-primary-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-clipboard-list stat-icon text-primary"></i>
                </div>
                <div>
                    <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
                    <div class="text-muted">Total</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-warning-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-hourglass-half stat-icon text-warning"></i>
                </div>
                <div>
                    <div class="stat-value"><?= $stats['pending_approval'] ?? 0 ?></div>
                    <div class="text-muted">Pending Approval</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-success-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-check-circle stat-icon text-success"></i>
                </div>
                <div>
                    <div class="stat-value"><?= $stats['approved'] ?? 0 ?></div>
                    <div class="text-muted">Approved</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-danger-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-times-circle stat-icon text-danger"></i>
                </div>
                <div>
                    <div class="stat-value"><?= $stats['rejected'] ?? 0 ?></div>
                    <div class="text-muted">Rejected</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 1. Request Lokasi Baru (prioritas tertinggi) -->
<div class="card table-card mb-4">
    <div class="card-header bg-warning-soft d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0">
                <i class="fas fa-map-marker-alt me-2 text-warning"></i>
                Request Lokasi Baru
            </h5>
            <p class="text-muted small mb-0">Lokasi baru yang diajukan oleh Service untuk diapprove</p>
        </div>
        <button class="btn btn-sm btn-outline-secondary" onclick="loadPendingLocationRequests()">
            <i class="fas fa-sync me-1"></i>Refresh
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0" id="locationRequestsTable">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Customer</th>
                        <th>Nama Lokasi</th>
                        <th>Area</th>
                        <th>Alamat</th>
                        <th>Diajukan Oleh</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="locationRequestsBody">
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-spinner fa-spin me-2"></i>Memuat data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 2. Pengajuan Unit (ADD_UNIT, UNIT_SWAP, dll) -->
<div class="card table-card mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0">
                <i class="fas fa-plus-circle me-2 text-primary"></i>
                Pengajuan Unit
            </h5>
            <p class="text-muted small mb-0">Tambah Unit, Tukar Unit, Tandai Spare — dari tim Service</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <select class="form-select form-select-sm" id="filterUnitRequestStatus" style="width:160px;" onchange="loadUnitRequests()">
                <option value="SUBMITTED">Menunggu Review</option>
                <option value="APPROVED">Approved</option>
                <option value="REJECTED">Rejected</option>
                <option value="">Semua</option>
            </select>
            <button class="btn btn-sm btn-outline-secondary" onclick="loadUnitRequests()"><i class="fas fa-sync"></i></button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0" id="unitRequestsTable">
                <thead class="table-light">
                    <tr>
                        <th>No. Audit</th>
                        <th>Customer</th>
                        <th>Lokasi</th>
                        <th>Jenis</th>
                        <th>Unit</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="unitRequestsBody">
                    <tr><td colspan="7" class="text-center py-3"><i class="fas fa-spinner fa-spin me-2"></i>Memuat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 3. Approve Audit Lokasi (hasil verifikasi per lokasi - review) -->
<div class="card table-card mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0">
                <i class="fas fa-check-circle me-2 text-success"></i>
                Approve Audit Lokasi
            </h5>
            <p class="text-muted small mb-0">
                Hasil verifikasi audit per lokasi — review dan approve
                <span class="ms-2 text-info">
                    <i class="bi bi-info-circle me-1"></i>
                    <small>Tip: Audit dengan perbedaan unit akan memerlukan penyesuaian harga</small>
                </span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary" onclick="loadPendingApprovals()">
                <i class="fas fa-sync me-1"></i>Refresh
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0" id="pendingTable">
                <thead class="table-light">
                    <tr>
                        <th>No. Audit</th>
                        <th>Customer</th>
                        <th>Lokasi</th>
                        <th>Tanggal Audit</th>
                        <th>Kontrak Unit</th>
                        <th>Actual Unit</th>
                        <th>Selisih</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="pendingTableBody">
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-spinner fa-spin me-2"></i>Memuat data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 4. Riwayat Approval (gabungan Lokasi Baru + Audit Lokasi, Rollback di menu detail) -->
<div class="card table-card mt-4 mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2 text-primary"></i>
                Riwayat Approval
            </h5>
            <p class="text-muted small mb-0">Lokasi Baru & Audit Lokasi yang sudah diapprove — Rollback tersedia di menu Detail (Lokasi Baru)</p>
        </div>
        <button class="btn btn-sm btn-outline-secondary" onclick="loadApprovalHistory()">
            <i class="fas fa-sync me-1"></i>Refresh
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 table-sm" id="allTable">
                <thead class="table-light">
                    <tr>
                        <th>Tipe</th>
                        <th>No/Kode</th>
                        <th>Customer</th>
                        <th>Lokasi</th>
                        <th>Tanggal</th>
                        <th>Approved Oleh</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="allTableBody">
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-spinner fa-spin me-2"></i>Memuat data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Detail Modal (Riwayat Approval - Lokasi Baru) - dengan tombol Rollback -->
<div class="modal fade" id="locationRequestDetailModal" tabindex="-1" aria-labelledby="locDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light py-3">
                <h5 class="modal-title" id="locDetailModalLabel">
                    <i class="fas fa-map-marker-alt me-2"></i>Detail Lokasi Baru (Approved)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="locationRequestDetailBody">
                <!-- Content loaded via JS -->
            </div>
            <div class="modal-footer bg-light py-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-warning" id="locDetailRollbackBtn" onclick="doRollbackFromDetail()">
                    <i class="fas fa-undo me-1"></i>Rollback ke PENDING
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal (Riwayat Approval - Approve Audit Lokasi) -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light py-3">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="fas fa-clipboard-check me-2"></i>Detail Audit Lokasi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <!-- Content loaded via AJAX -->
            </div>
            <div class="modal-footer bg-light py-3" id="detailModalFooter">
                <!-- Actions loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Approve Confirm Modal -->
<div class="modal fade" id="auditApproveConfirmModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-3" style="background-color:#198754;color:#fff;">
                <h5 class="modal-title" style="color:#fff;"><i class="fas fa-check-circle me-2"></i>Konfirmasi Approve Audit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="filter:invert(1);"></button>
            </div>
            <div class="modal-body" id="approveConfirmBody">
                <!-- populated by approveAudit() -->
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="doApproveAudit()">
                    <i class="fas fa-check me-1"></i>Ya, Approve
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tolak Audit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Alasan Penolakan</label>
                    <textarea class="form-control" id="rejectNotes" rows="3" placeholder="Alasan penolakan (opsional)"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="confirmReject()">Tolak Audit</button>
            </div>
        </div>
    </div>
</div>

<!-- Approve Location + Units Modal -->
<div class="modal fade" id="approveLocationWithUnitsModal" tabindex="-1" aria-labelledby="approveLocModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-success text-dark py-3">
                <h5 class="modal-title" id="approveLocModalLabel">
                    <i class="fas fa-check-circle me-2"></i>Approve Lokasi + Unit
                </h5>
                <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Lokasi <strong id="approveLocName"></strong> memiliki unit yang diajukan. Centang unit yang akan diapprove, atau klik "Approve Lokasi Saja" untuk menolak semua unit.</p>
                <div class="mb-3">
                    <label class="form-label">Kontrak / PO <span class="text-muted">(wajib jika approve unit)</span></label>
                    <select class="form-select" id="approveLocKontrakSelect">
                        <option value="">-- Pilih Kontrak --</option>
                    </select>
                    <div class="form-text">Pilih kontrak yang sesuai dengan lokasi audit ini</div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width:40px">Include</th>
                                <th>No Unit</th>
                                <th>S/N</th>
                                <th>Merk/Model</th>
                                <th>Kapasitas</th>
                                <th>Harga Sewa (Rp)</th>
                            </tr>
                        </thead>
                        <tbody id="approveLocUnitsBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-outline-success" onclick="approveLocationOnly()">
                    <i class="fas fa-map-marker-alt me-1"></i>Approve Lokasi Saja
                </button>
                <button type="button" class="btn btn-success" onclick="confirmApproveLocationWithUnits()">
                    <i class="fas fa-check me-1"></i>Approve Lokasi + Unit
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Review Pengajuan Unit -->
<div class="modal fade" id="unitRequestModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-clipboard-check me-2"></i>Review Pengajuan Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="unitRequestDetailContent"></div>
            <div class="modal-footer bg-light" id="unitRequestActionFooter" style="display:none;">
                <input type="hidden" id="unitRequestId">
                <input type="hidden" id="unitRequestType">
                <input type="hidden" id="unitRequestCustomerId">
                <div class="w-100 mb-2">
                    <textarea class="form-control" id="unitRequestReviewNotes" rows="2" placeholder="Catatan review (opsional)"></textarea>
                </div>
                <div class="w-100 d-flex justify-content-between align-items-center">
                    <span class="text-muted small" id="unitRequestHint"></span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-danger" id="btnRejectUnitRequest" onclick="processUnitRequest('REJECT')">
                            <i class="fas fa-times me-1"></i>Tolak
                        </button>
                        <button type="button" class="btn btn-success" id="btnApproveUnitRequest" onclick="processUnitRequest('APPROVE')">
                            <i class="fas fa-check me-1"></i>Approve
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Location Request Modal -->
<div class="modal fade" id="rejectLocationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tolak Request Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Lokasi: <strong id="rejectLocationName"></strong></p>
                <div class="mb-3">
                    <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="rejectLocationNotes" rows="3" placeholder="Alasan penolakan (opsional)"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="confirmRejectLocation()">Tolak Lokasi</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
let selectedAuditId = null;
let selectedLocationRequestId = null;
let pendingLocationRequests = [];  // Cache for approve-with-units flow

const BASE_AUDIT = '<?= base_url() ?>';
// CSRF token untuk semua POST request
const CSRF_NAME  = '<?= csrf_token() ?>';
const CSRF_HASH  = '<?= csrf_hash() ?>';
function csrfBody(extra) {
    const p = new URLSearchParams(extra || {});
    p.set(CSRF_NAME, CSRF_HASH);
    return p.toString();
}
// Pengajuan Unit: pakai service/unit_audit (sama seperti Audit Unit - sudah terbukti jalan)
const URL_GET_AUDIT_REQUESTS = '<?= base_url('service/unit_audit/getAuditRequests') ?>';
const URL_GET_AUDIT_DETAIL = '<?= base_url('service/unit_audit/getAuditDetail') ?>';
const URL_APPROVE_REJECT = '<?= base_url('service/unit_audit') ?>';
const FETCH_TIMEOUT_MS = 15000;

function formatPriceRp(n) {
    const num = parseInt(n, 10) || 0;
    return num.toLocaleString('id-ID');
}
function parsePriceRp(str) {
    if (str === '' || str == null) return 0;
    const cleaned = String(str).replace(/[.\s]/g, '').replace(',', '.');
    return parseFloat(cleaned) || 0;
}

function fetchWithTimeout(url, options = {}) {
    const ctrl = new AbortController();
    const t = setTimeout(() => ctrl.abort(), FETCH_TIMEOUT_MS);
    return fetch(url, { ...options, signal: ctrl.signal })
        .finally(() => clearTimeout(t));
}

// Initialize (urutan: Request Lokasi dulu, lalu Pengajuan Unit, Approve Audit Lokasi, Riwayat)
document.addEventListener('DOMContentLoaded', function() {
    loadPendingLocationRequests();
    loadUnitRequests();
    loadPendingApprovals();
    loadApprovalHistory();
});

// ─── Pengajuan Unit (ADD_UNIT, dll) ─────────────────────────────────
function loadUnitRequests() {
    const status = document.getElementById('filterUnitRequestStatus')?.value || 'SUBMITTED';
    const tbody = document.getElementById('unitRequestsBody');
    if (!tbody) return;
    const url = URL_GET_AUDIT_REQUESTS + '?status=' + encodeURIComponent(status);
    fetchWithTimeout(url)
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status + ' ' + res.statusText);
            return res.json();
        })
        .then(data => {
            if (data.success) renderUnitRequestsTable(data.data);
            else { tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-4">Gagal memuat data</td></tr>'; }
        })
        .catch((err) => {
            const msg = err.name === 'AbortError' ? 'Timeout—periksa koneksi' : (err.message || 'Gagal memuat');
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-4">' + msg + '</td></tr>';
            if (typeof console !== 'undefined' && console.error) console.error('loadUnitRequests:', url, err);
        });
}

function renderUnitRequestsTable(data) {
    const tbody = document.getElementById('unitRequestsBody');
    if (!data || data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Belum ada pengajuan</td></tr>';
        return;
    }
    const typeMap = { 'LOCATION_MISMATCH':'Lokasi Berbeda','UNIT_SWAP':'Tukar Unit','ADD_UNIT':'Tambah Unit','MARK_SPARE':'Tandai Spare','UNIT_MISSING':'Unit Hilang','OTHER':'Lainnya' };
    const statusMap = { 'SUBMITTED':'<span class="badge badge-soft-yellow">Menunggu</span>','APPROVED':'<span class="badge badge-soft-green">Approved</span>','REJECTED':'<span class="badge badge-soft-red">Rejected</span>' };
    tbody.innerHTML = data.map(item => {
        const typeLabel = typeMap[item.request_type] || item.request_type;
        const statusBadge = statusMap[item.status] || item.status;

        // Unit info: for ADD_UNIT show proposed unit, for others show existing unit
        let unitInfo = '-';
        if (item.request_type === 'ADD_UNIT') {
            unitInfo = item.no_unit
                ? `<strong>${item.no_unit}</strong>${item.serial_number ? '<br><small class="text-muted">' + item.serial_number + '</small>' : ''}`
                : '<span class="text-muted fst-italic">Unit baru</span>';
        } else {
            unitInfo = item.no_unit
                ? (item.no_unit + (item.serial_number ? '<br><small class="text-muted">' + item.serial_number + '</small>' : ''))
                : '-';
        }

        const proposed = typeof item.proposed_data === 'string' ? JSON.parse(item.proposed_data || '{}') : (item.proposed_data || {});
        let action;
        if (item.status === 'SUBMITTED') {
            action = `<button class="btn btn-sm btn-primary" onclick="openUnitRequestReview(${item.id})"><i class="fas fa-eye me-1"></i>Review</button>`;
        } else {
            action = `<button class="btn btn-sm btn-outline-secondary" onclick="openUnitRequestDetail(${item.id})">Detail</button>`;
        }
        return `<tr>
            <td><strong>${item.audit_number || '-'}</strong></td>
            <td>${item.customer_name || '-'}</td>
            <td>${item.lokasi_kontrak || '-'}</td>
            <td>${typeLabel}</td>
            <td class="small">${unitInfo}</td>
            <td>${statusBadge}</td>
            <td>${action}</td>
        </tr>`;
    }).join('');
}

function openUnitRequestReview(id) { openUnitRequestModal(id, true); }
function openUnitRequestDetail(id) { openUnitRequestModal(id, false); }

function formatRp(n) {
    return parseInt(n || 0).toLocaleString('id-ID');
}

// Attach Rupiah live-formatting to a display input; syncs raw value to a hidden input
function initRpInput(displayId, hiddenId) {
    const disp = document.getElementById(displayId);
    const hid  = document.getElementById(hiddenId);
    if (!disp || !hid) return;
    // Format existing value
    if (hid.value) {
        disp.value = parseInt(hid.value).toLocaleString('id-ID');
    }
    disp.addEventListener('input', function() {
        const raw = this.value.replace(/\D/g, '');
        hid.value  = raw;
        this.value = raw ? parseInt(raw).toLocaleString('id-ID') : '';
    });
    disp.addEventListener('focus', function() {
        // Move cursor to end
        const len = this.value.length;
        this.setSelectionRange(len, len);
    });
}

function openUnitRequestModal(id, isReview) {
    const body   = document.getElementById('unitRequestDetailContent');
    const footer = document.getElementById('unitRequestActionFooter');
    body.innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>';
    footer.style.display = 'none';
    document.getElementById('unitRequestId').value = id;
    document.getElementById('unitRequestType').value = '';
    document.getElementById('unitRequestCustomerId').value = '';
    document.getElementById('unitRequestReviewNotes').value = '';
    document.getElementById('unitRequestHint').textContent = '';
    $('#unitRequestModal').modal('show');

    fetch(URL_GET_AUDIT_DETAIL + '/' + id)
        .then(res => { if (!res.ok) throw new Error('HTTP ' + res.status); return res.json(); })
        .then(res => {
            if (!res.success) { body.innerHTML = '<div class="alert alert-danger">Gagal memuat detail</div>'; return; }
            const item     = res.data;
            const proposed = typeof item.proposed_data === 'string' ? JSON.parse(item.proposed_data || '{}') : (item.proposed_data || {});
            const current  = typeof item.current_data  === 'string' ? JSON.parse(item.current_data  || '{}') : (item.current_data  || {});
            const typeMap  = { ADD_UNIT:'Tambah Unit', UNIT_SWAP:'Tukar Unit', MARK_SPARE:'Tandai Spare', LOCATION_MISMATCH:'Lokasi Berbeda', UNIT_MISSING:'Unit Hilang', OTHER:'Lainnya' };

            document.getElementById('unitRequestType').value       = item.request_type;
            document.getElementById('unitRequestCustomerId').value = item.customer_id || '';

            // ── Header info bar ──────────────────────────────────────
            const headerHtml = `
                <div class="alert alert-light border mb-3 py-2">
                    <div class="row gx-4 gy-1 small">
                        <div class="col-auto"><strong>No. Audit:</strong> ${item.audit_number || '-'}</div>
                        <div class="col-auto"><strong>Customer:</strong> ${item.customer_name || '-'}</div>
                        <div class="col-auto"><strong>Lokasi:</strong> ${item.lokasi_kontrak || '-'}</div>
                        <div class="col-auto"><strong>Jenis:</strong> <span class="badge badge-soft-blue">${typeMap[item.request_type] || item.request_type}</span></div>
                        <div class="col-auto"><strong>Diajukan Oleh:</strong> ${item.submitter_name || '-'}</div>
                    </div>
                    ${item.notes ? `<div class="mt-1 small text-muted"><strong>Catatan Service:</strong> ${item.notes}</div>` : ''}
                </div>`;

            // ── Per-type review form ─────────────────────────────────
            let detailHtml = '';
            let hint       = '';

            if (item.request_type === 'ADD_UNIT') {
                hint = 'Wajib pilih kontrak dan isi harga sewa sebelum approve.';
                detailHtml = `
                <div class="row g-3">
                    <div class="col-md-5">
                        <div class="card h-100">
                            <div class="card-header py-2 bg-light"><strong>Unit yang Diajukan</strong></div>
                            <div class="card-body py-3">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><th class="text-muted" style="width:110px">No. Unit</th><td><strong>${item.no_unit || '<span class="text-muted">-</span>'}</strong></td></tr>
                                    <tr><th class="text-muted">Serial No.</th><td>${item.serial_number || '-'}</td></tr>
                                    <tr><th class="text-muted">Model</th><td>${(item.merk_unit || '') + ' ' + (item.model_unit || '') || '-'}</td></tr>
                                    <tr><th class="text-muted">Spare?</th><td><span class="badge ${proposed.is_spare ? 'badge-soft-orange' : 'badge-soft-gray'}">${proposed.is_spare ? 'Spare' : 'Unit Reguler'}</span></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card h-100 border-primary">
                            <div class="card-header py-2 bg-primary text-white"><strong>Data Marketing (wajib diisi)</strong></div>
                            <div class="card-body py-3">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Kontrak <span class="text-danger">*</span></label>
                                    <select class="form-select" id="reviewKontrakId">
                                        <option value="">-- Memuat kontrak... --</option>
                                    </select>
                                    <div class="form-text">Pilih kontrak aktif customer yang akan menerima unit ini.</div>
                                </div>
                                <div class="mb-3" id="wrapHargaSewa">
                                    <label class="form-label fw-bold">Harga Sewa / Unit / Bulan <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control review-rp-input" id="reviewHargaSewaDisplay"
                                            inputmode="numeric" placeholder="0" autocomplete="off">
                                        <input type="hidden" id="reviewHargaSewa">
                                    </div>
                                    <div class="form-text">Contoh: 2.500.000</div>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="reviewIsSpare" ${proposed.is_spare ? 'checked' : ''}>
                                    <label class="form-check-label" for="reviewIsSpare">Tandai sebagai Spare Unit</label>
                                    <div class="form-text text-warning" id="spareHargaNote" style="display:none">Harga sewa tidak berlaku untuk spare unit.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
                // Load contracts async
                if (item.customer_id) {
                    fetch(`<?= base_url('marketing/unit-audit/getContractsForCustomer/') ?>${item.customer_id}`)
                        .then(r => r.json())
                        .then(r => {
                            const sel = document.getElementById('reviewKontrakId');
                            if (!sel) return;
                            if (r.success && r.data.length) {
                                sel.innerHTML = '<option value="">-- Pilih Kontrak --</option>' +
                                    r.data.map(c => `<option value="${c.id}">${c.no_kontrak}${c.customer_po_number ? ' / ' + c.customer_po_number : ''} (${c.status})</option>`).join('');
                                // Pre-select if request already has kontrak_id
                                if (item.kontrak_id) sel.value = item.kontrak_id;
                            } else {
                                sel.innerHTML = '<option value="">Tidak ada kontrak aktif</option>';
                            }
                        });
                }
                // Init Rp formatting and spare toggle after DOM is ready
                setTimeout(() => {
                    initRpInput('reviewHargaSewaDisplay', 'reviewHargaSewa');
                    const spareChk = document.getElementById('reviewIsSpare');
                    if (spareChk) {
                        const toggleSpare = () => {
                            const isSpare = spareChk.checked;
                            const wrap    = document.getElementById('wrapHargaSewa');
                            const note    = document.getElementById('spareHargaNote');
                            const inp     = document.getElementById('reviewHargaSewaDisplay');
                            if (wrap)  wrap.style.opacity = isSpare ? '0.45' : '1';
                            if (inp)   inp.disabled       = isSpare;
                            if (note)  note.style.display = isSpare ? 'block' : 'none';
                        };
                        spareChk.addEventListener('change', toggleSpare);
                        toggleSpare(); // run once for initial state
                    }
                }, 50);

            } else if (item.request_type === 'UNIT_SWAP') {
                hint = 'Isi harga sewa untuk unit pengganti sebelum approve.';
                detailHtml = `
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-header py-2 bg-danger text-white"><strong>Unit Lama (Ditarik)</strong></div>
                            <div class="card-body py-3">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><th class="text-muted" style="width:90px">No. Unit</th><td><strong>${current.no_unit || item.no_unit || '-'}</strong></td></tr>
                                    <tr><th class="text-muted">Serial</th><td>${current.serial || item.serial_number || '-'}</td></tr>
                                    <tr><th class="text-muted">Kontrak</th><td>${item.no_kontrak || '-'}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-header py-2 bg-success text-white"><strong>Unit Baru (Pengganti)</strong></div>
                            <div class="card-body py-3">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><th class="text-muted" style="width:90px">Unit ID</th><td><strong>${proposed.new_unit_id || '-'}</strong></td></tr>
                                    <tr><th class="text-muted">Catatan</th><td>${proposed.swap_reason || '-'}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-primary">
                            <div class="card-header py-2 bg-primary text-white"><strong>Data Marketing</strong></div>
                            <div class="card-body py-3">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Harga Sewa Unit Baru <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control review-rp-input" id="reviewHargaSewaDisplay"
                                            inputmode="numeric" placeholder="0" autocomplete="off"
                                            value="${proposed.harga_sewa ? parseInt(proposed.harga_sewa).toLocaleString('id-ID') : ''}">
                                        <input type="hidden" id="reviewHargaSewa" value="${proposed.harga_sewa || ''}">
                                    </div>
                                    <div class="form-text">Kosongkan jika sama dengan unit lama.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
                // Init Rp format for UNIT_SWAP harga input
                setTimeout(() => initRpInput('reviewHargaSewaDisplay', 'reviewHargaSewa'), 50);
                hint = 'Review dan konfirmasi penandaan spare unit.';
                detailHtml = `
                <div class="card">
                    <div class="card-header py-2 bg-light"><strong>Unit yang Ditandai Spare</strong></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr><th class="text-muted" style="width:110px">No. Unit</th><td><strong>${item.no_unit || '-'}</strong></td></tr>
                                    <tr><th class="text-muted">Serial No.</th><td>${item.serial_number || '-'}</td></tr>
                                    <tr><th class="text-muted">Model</th><td>${(item.merk_unit || '') + ' ' + (item.model_unit || '') || '-'}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr><th class="text-muted" style="width:110px">Kontrak</th><td>${item.no_kontrak || '-'}</td></tr>
                                    <tr><th class="text-muted">Lokasi</th><td>${item.lokasi_kontrak || '-'}</td></tr>
                                </table>
                            </div>
                        </div>
                        <div class="alert alert-warning py-2 mt-2 mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            Menyetujui pengajuan ini akan menandai unit sebagai <strong>Spare Unit</strong> di dalam kontrak.
                        </div>
                    </div>
                </div>`;

            } else if (item.request_type === 'LOCATION_MISMATCH') {
                hint = 'Verifikasi bahwa perubahan lokasi unit sudah sesuai.';
                detailHtml = `
                <div class="row g-3">
                    <div class="col-md-5">
                        <div class="card h-100">
                            <div class="card-header py-2 bg-danger text-white"><strong>Lokasi Lama</strong></div>
                            <div class="card-body py-3">
                                <p class="mb-1"><strong>${current.location || item.lokasi_kontrak || '-'}</strong></p>
                                <p class="mb-0 text-muted small">Unit: ${item.no_unit || '-'} | S/N: ${item.serial_number || '-'}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-1 d-flex align-items-center justify-content-center">
                        <i class="fas fa-arrow-right fa-2x text-muted"></i>
                    </div>
                    <div class="col-md-5">
                        <div class="card h-100">
                            <div class="card-header py-2 bg-success text-white"><strong>Lokasi Baru (Diusulkan)</strong></div>
                            <div class="card-body py-3">
                                <p class="mb-1"><strong>${proposed.new_location || '-'}</strong></p>
                                <p class="mb-0 text-muted small">Alasan: ${proposed.reason || item.notes || '-'}</p>
                            </div>
                        </div>
                    </div>
                </div>`;

            } else if (item.request_type === 'UNIT_MISSING') {
                hint = 'Pilih tindakan yang akan diambil untuk unit yang hilang.';
                detailHtml = `
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header py-2 bg-warning"><strong>Unit Dilaporkan Hilang</strong></div>
                            <div class="card-body py-3">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><th class="text-muted" style="width:110px">No. Unit</th><td><strong>${item.no_unit || '-'}</strong></td></tr>
                                    <tr><th class="text-muted">Serial No.</th><td>${item.serial_number || '-'}</td></tr>
                                    <tr><th class="text-muted">Kontrak</th><td>${item.no_kontrak || '-'}</td></tr>
                                    <tr><th class="text-muted">Lokasi</th><td>${item.lokasi_kontrak || '-'}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-primary">
                            <div class="card-header py-2 bg-primary text-white"><strong>Tindakan</strong></div>
                            <div class="card-body py-3">
                                <label class="form-label fw-bold">Tindakan yang Diambil <span class="text-danger">*</span></label>
                                <div class="d-flex flex-column gap-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="missingAction" id="missingRecord" value="record" checked>
                                        <label class="form-check-label" for="missingRecord">
                                            <strong>Catat Saja</strong>
                                            <div class="text-muted small">Unit tetap di kontrak, dicatat sebagai laporan hilang</div>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="missingAction" id="missingPull" value="pull">
                                        <label class="form-check-label" for="missingPull">
                                            <strong>Pull dari Kontrak</strong>
                                            <div class="text-muted small">Unit dikeluarkan dari kontrak (status PULLED)</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            } else {
                // OTHER / fallback
                detailHtml = `
                <div class="card">
                    <div class="card-header py-2">Data Pengajuan</div>
                    <div class="card-body">
                        <pre class="mb-0 small">${JSON.stringify(proposed, null, 2)}</pre>
                    </div>
                </div>`;
            }

            body.innerHTML = headerHtml + detailHtml;
            document.getElementById('unitRequestHint').textContent = hint;

            if (isReview) {
                footer.style.display = 'block';
            }
        })
        .catch(() => {
            body.innerHTML = '<div class="alert alert-danger">Gagal memuat detail. Coba lagi.</div>';
        });
}

function processUnitRequest(action) {
    const id          = document.getElementById('unitRequestId').value;
    const notes       = document.getElementById('unitRequestReviewNotes').value;
    const requestType = document.getElementById('unitRequestType').value;
    const endpoint    = action === 'APPROVE' ? 'approveRequest' : 'rejectRequest';

    let extraData = {};

    if (action === 'APPROVE') {
        if (requestType === 'ADD_UNIT') {
            const kontrakId = document.getElementById('reviewKontrakId')?.value;
            const hargaSewa = document.getElementById('reviewHargaSewa')?.value;   // hidden raw value
            const isSpare   = document.getElementById('reviewIsSpare')?.checked ? '1' : '0';
            if (!kontrakId) {
                if (window.OptimaNotify) OptimaNotify.error('Pilih kontrak terlebih dahulu.');
                else alert('Pilih kontrak terlebih dahulu.');
                return;
            }
            // Spare unit: harga sewa boleh kosong (0)
            if (isSpare === '0' && (!hargaSewa || parseFloat(hargaSewa) <= 0)) {
                if (window.OptimaNotify) OptimaNotify.error('Isi harga sewa per unit per bulan.');
                else alert('Isi harga sewa per unit per bulan.');
                return;
            }
            extraData = { kontrak_id: kontrakId, harga_sewa: isSpare === '1' ? '0' : hargaSewa, is_spare: isSpare };
        }

        if (requestType === 'UNIT_SWAP') {
            const hargaSewa = document.getElementById('reviewHargaSewa')?.value;   // hidden raw value
            if (hargaSewa) extraData.harga_sewa = hargaSewa;
        }

        if (requestType === 'UNIT_MISSING') {
            const actionEl = document.querySelector('input[name="missingAction"]:checked');
            extraData.missing_action = actionEl ? actionEl.value : 'record';
        }
    }

    const btn = action === 'APPROVE'
        ? document.getElementById('btnApproveUnitRequest')
        : document.getElementById('btnRejectUnitRequest');
    btn.disabled = true;
    const origHTML = btn.innerHTML;
    btn.innerHTML  = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';

    fetch(URL_APPROVE_REJECT + '/' + endpoint + '/' + id, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: csrfBody({ notes, ...extraData })
    }).then(r => r.json()).then(res => {
        btn.disabled  = false;
        btn.innerHTML = origHTML;
        if (res.success) {
            $('#unitRequestModal').modal('hide');
            if (window.OptimaNotify) OptimaNotify.success(res.message);
            else alert(res.message);
            loadUnitRequests();
        } else {
            if (window.OptimaNotify) OptimaNotify.error(res.message || 'Gagal');
            else alert(res.message || 'Gagal');
        }
    }).catch(() => {
        btn.disabled  = false;
        btn.innerHTML = origHTML;
        if (window.OptimaNotify) OptimaNotify.error('Koneksi gagal. Coba lagi.');
        else alert('Koneksi gagal. Coba lagi.');
    });
}

function loadPendingApprovals() {
    const tbody = document.getElementById('pendingTableBody');
    if (!tbody) return;
    const url = '<?= base_url('marketing/unit-audit/getPendingApprovals') ?>';
    fetchWithTimeout(url)
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status + ' ' + res.statusText);
            return res.json();
        })
        .then(data => {
            if (data.success) renderPendingTable(data.data || []);
            else tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger py-4">Gagal memuat data</td></tr>';
        })
        .catch((err) => {
            const msg = err.name === 'AbortError' ? 'Timeout—periksa koneksi' : (err.message || 'Gagal memuat');
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger py-4">' + msg + '</td></tr>';
            if (typeof console !== 'undefined' && console.error) console.error('loadPendingApprovals:', url, err);
        });
}

function loadPendingLocationRequests() {
    const tbody = document.getElementById('locationRequestsBody');
    if (!tbody) return;
    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-3"><i class="fas fa-spinner fa-spin me-2"></i>Memuat...</td></tr>';
    const url = '<?= base_url('marketing/unit-audit/getPendingLocationRequests') ?>';
    fetchWithTimeout(url)
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status + ' ' + res.statusText);
            return res.json();
        })
        .then(data => {
            if (data.success) renderLocationRequestsTable(data.data);
            else {
                const errMsg = (data.message || 'Gagal memuat data');
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger py-3">' + errMsg + '</td></tr>';
                if (typeof console !== 'undefined' && console.error) console.error('loadPendingLocationRequests:', url, data);
            }
        })
        .catch((err) => {
            const msg = err.name === 'AbortError' ? 'Timeout—periksa koneksi' : (err.message || 'Gagal memuat');
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger py-3">' + msg + '</td></tr>';
            if (typeof console !== 'undefined' && console.error) console.error('loadPendingLocationRequests:', url, err);
        });
}

function doRollback(id) {
    fetch(`<?= base_url('marketing/unit-audit/rollbackLocationRequest/') ?>${id}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: csrfBody({ notes: '' })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (window.OptimaNotify) OptimaNotify.success(data.message);
            else alert(data.message);
            const locModal = document.getElementById('locationRequestDetailModal');
            if (locModal && bootstrap.Modal.getInstance(locModal)) bootstrap.Modal.getInstance(locModal).hide();
            loadPendingLocationRequests();
            loadApprovalHistory();
        } else {
            if (window.OptimaNotify) OptimaNotify.error(data.message || 'Gagal rollback');
            else alert(data.message || 'Gagal rollback');
        }
    });
}

function renderLocationRequestsTable(requests) {
    const tbody = document.getElementById('locationRequestsBody');
    pendingLocationRequests = requests || [];
    
    if (!requests || requests.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Tidak ada request lokasi baru</td></tr>';
        return;
    }
    
    tbody.innerHTML = requests.map(req => {
        const date = req.created_at ? new Date(req.created_at).toLocaleDateString('id-ID') : '-';
        return `
            <tr>
                <td><strong>${req.location_code || '-'}</strong></td>
                <td>${req.customer_name || '-'} <small class="text-muted">(${req.customer_code || ''})</small></td>
                <td>${req.location_name || '-'}</td>
                <td><span class="badge badge-soft-blue">${req.area_name || '-'}</span></td>
                <td class="small">${req.address || '-'}, ${req.city || ''}</td>
                <td>${req.requested_by_name || '-'}</td>
                <td>${date}</td>
                <td>
                    ${(req.add_unit_requests && req.add_unit_requests.length) ? `<span class="badge badge-soft-cyan me-1">${req.add_unit_requests.length} unit</span>` : ''}
                    <button class="btn btn-sm btn-success me-1" onclick="approveLocationRequest(${req.id})" title="Approve">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="showRejectLocationModal(${req.id}, '${(req.location_name || '').replace(/'/g, "\\'")}')">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function approveLocationRequest(id) {
    const req = pendingLocationRequests.find(r => r.id == id);
    const hasUnits = req && req.add_unit_requests && req.add_unit_requests.length > 0;
    
    if (hasUnits) {
        showApproveLocationWithUnitsModal(req);
        return;
    }
    
    if (window.OptimaConfirm && window.OptimaConfirm.approve) {
        OptimaConfirm.approve({ text: 'Approve lokasi ini?', onConfirm: function() { doApproveLocationRequest(id, null, {}); } });
    } else {
        if (confirm('Approve lokasi ini?')) doApproveLocationRequest(id, null, {});
    }
}

function showApproveLocationWithUnitsModal(req) {
    selectedLocationRequestId = req.id;
    document.getElementById('approveLocName').textContent = req.location_name || 'Lokasi #' + req.id;
    
    const $sel = $('#approveLocKontrakSelect');
    $sel.html('<option value="">-- Memuat kontrak... --</option>');
    
    fetch(`<?= base_url('marketing/unit-audit/getContractsForCustomer/') ?>${req.customer_id}`)
        .then(res => res.json())
        .then(data => {
            $sel.empty().append('<option value="">-- Pilih Kontrak --</option>');
            const contracts = data.data || [];
            contracts.forEach(k => {
                $sel.append($('<option>').val(k.id).text((k.no_kontrak || '') + (k.customer_po_number ? ' | ' + k.customer_po_number : '')));
            });
            if (contracts.length === 0) {
                $sel.append($('<option value="" disabled>-- Customer belum punya kontrak. Buat kontrak terlebih dahulu. --</option>'));
            }
        });
    
    const tbody = document.getElementById('approveLocUnitsBody');
    tbody.innerHTML = (req.add_unit_requests || []).map(au => {
        const proposed = typeof au.proposed_data === 'string' ? JSON.parse(au.proposed_data || '{}') : (au.proposed_data || {});
        const noUnit = au.no_unit || 'UNIT-' + (proposed.unit_id || '');
        const sn = au.serial_number || '-';
        const merkModel = au.merk_model || '-';
        const kapasitas = au.kapasitas || '-';
        return `<tr>
            <td class="text-center"><input type="checkbox" class="form-check-input unit-include-cb" data-request-id="${au.id}" checked></td>
            <td><strong>${noUnit}</strong></td>
            <td>${sn}</td>
            <td class="small">${merkModel}</td>
            <td class="small">${kapasitas}</td>
            <td><div class="input-group input-group-sm"><span class="input-group-text">Rp</span><input type="text" class="form-control price-input text-end" data-request-id="${au.id}" placeholder="0" inputmode="numeric" value="0"></div></td>
        </tr>`;
    }).join('');
    
    tbody.querySelectorAll('.unit-include-cb').forEach(cb => {
        cb.addEventListener('change', function() {
            const tr = this.closest('tr');
            const inp = tr.querySelector('.price-input');
            tr.classList.toggle('table-secondary', !this.checked);
            if (inp) inp.disabled = !this.checked;
        });
    });
    tbody.querySelectorAll('.price-input').forEach(inp => {
        inp.addEventListener('focus', function() {
            const v = parsePriceRp(this.value);
            this.value = v > 0 ? String(v) : '';
        });
        inp.addEventListener('blur', function() {
            const v = parsePriceRp(this.value);
            this.value = formatPriceRp(v);
        });
        inp.dispatchEvent(new Event('blur'));
    });
    
    $('#approveLocationWithUnitsModal').modal('show');
}

function approveLocationOnly() {
    if (window.OptimaConfirm && window.OptimaConfirm.approve) {
        OptimaConfirm.approve({
            title: 'Approve Lokasi Saja',
            text: 'Approve lokasi tanpa unit? Semua unit yang diajukan akan ditolak.',
            confirmText: '<i class="fas fa-map-marker-alt me-1"></i>Ya, Approve Lokasi Saja',
            onConfirm: function() {
                doApproveLocationRequest(selectedLocationRequestId, null, {});
                $('#approveLocationWithUnitsModal').modal('hide');
            }
        });
    } else {
        if (confirm('Approve lokasi tanpa unit? Semua unit akan ditolak.')) {
            doApproveLocationRequest(selectedLocationRequestId, null, {});
            $('#approveLocationWithUnitsModal').modal('hide');
        }
    }
}

function confirmApproveLocationWithUnits() {
    const included = {};
    document.querySelectorAll('#approveLocUnitsBody .unit-include-cb:checked').forEach(cb => {
        included[cb.getAttribute('data-request-id')] = true;
    });
    const prices = {};
    let hasEmpty = false;
    document.querySelectorAll('#approveLocUnitsBody tr').forEach(tr => {
        const cb = tr.querySelector('.unit-include-cb');
        const inp = tr.querySelector('.price-input');
        if (!cb || !cb.checked || !inp) return;
        const rid = inp.getAttribute('data-request-id');
        const val = parsePriceRp(inp.value);
        prices[rid] = val < 0 ? 0 : val;
        if (String(inp.value).trim() === '') hasEmpty = true;
    });
    if (Object.keys(prices).length === 0) {
        if (window.OptimaNotify) OptimaNotify.warning('Centang minimal 1 unit untuk approve, atau gunakan "Approve Lokasi Saja"');
        else alert('Centang minimal 1 unit untuk approve, atau gunakan "Approve Lokasi Saja"');
        return;
    }
    const kontrakId = $('#approveLocKontrakSelect').val();
    if (!kontrakId) {
        if (window.OptimaNotify) OptimaNotify.warning('Pilih kontrak terlebih dahulu');
        else alert('Pilih kontrak terlebih dahulu');
        return;
    }
    if (hasEmpty) {
        if (window.OptimaNotify) OptimaNotify.warning('Input harga sewa untuk setiap unit yang dicentang (boleh Rp 0)');
        else alert('Input harga sewa untuk setiap unit yang dicentang (boleh Rp 0)');
        return;
    }
    doApproveLocationRequest(selectedLocationRequestId, kontrakId, prices);
    $('#approveLocationWithUnitsModal').modal('hide');
}

function doApproveLocationRequest(locId, kontrakId, unitPrices) {
    const params = new URLSearchParams();
    params.append(CSRF_NAME, CSRF_HASH);
    params.append('notes', '');
    if (kontrakId) params.append('kontrak_id', kontrakId);
    if (Object.keys(unitPrices).length) params.append('unit_prices', JSON.stringify(unitPrices));
    
    fetch(`<?= base_url('marketing/unit-audit/approveLocationRequest/') ?>${locId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params.toString()
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (window.OptimaNotify) OptimaNotify.success(data.message);
            else alert(data.message);
            loadPendingLocationRequests();
            loadApprovalHistory();
        } else {
            if (window.OptimaNotify) OptimaNotify.error(data.message);
            else alert(data.message);
        }
    });
}

function showRejectLocationModal(id, name) {
    selectedLocationRequestId = id;
    document.getElementById('rejectLocationName').textContent = name;
    document.getElementById('rejectLocationNotes').value = '';
    $('#rejectLocationModal').modal('show');
}

function confirmRejectLocation() {
    const notes = document.getElementById('rejectLocationNotes').value;
    
    fetch(`<?= base_url('marketing/unit-audit/rejectLocationRequest/') ?>${selectedLocationRequestId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: csrfBody({ notes: notes })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (window.OptimaNotify) OptimaNotify.success(data.message);
            else alert(data.message);
            $('#rejectLocationModal').modal('hide');
            loadPendingLocationRequests();
        } else {
            if (window.OptimaNotify) OptimaNotify.error(data.message);
            else alert(data.message);
        }
    });
}

function loadApprovalHistory() {
    const tbody = document.getElementById('allTableBody');
    if (!tbody) return;
    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin me-2"></i>Memuat...</td></tr>';
    const url = '<?= base_url('marketing/unit-audit/getApprovalHistory') ?>';
    fetchWithTimeout(url)
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status + ' ' + res.statusText);
            return res.json();
        })
        .then(data => {
            if (data.success) renderApprovalHistoryTable(data.data || []);
            else tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger py-4">Gagal memuat data</td></tr>';
        })
        .catch((err) => {
            const msg = err.name === 'AbortError' ? 'Timeout—periksa koneksi' : (err.message || 'Gagal memuat');
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger py-4">' + msg + '</td></tr>';
            if (typeof console !== 'undefined' && console.error) console.error('loadApprovalHistory:', url, err);
        });
}

function renderPendingTable(audits) {
    const tbody = document.getElementById('pendingTableBody');
    if (!tbody) return;
    const list = Array.isArray(audits) ? audits : [];
    if (list.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">Tidak ada audit menunggu approval</td></tr>';
        return;
    }

    tbody.innerHTML = list.map(audit => {
        const difference = (audit.actual_total_units || 0) - (audit.kontrak_total_units || 0);
        const diffClass = difference !== 0 ? 'text-danger' : 'text-success';

        return `
            <tr>
                <td><strong>${audit.audit_number}</strong></td>
                <td>${audit.customer_name || '-'}</td>
                <td>${audit.location_name || '-'}</td>
                <td>${formatDate(audit.audit_date)}</td>
                <td><span class="badge badge-soft-blue">${audit.kontrak_total_units || 0}</span></td>
                <td><span class="badge badge-soft-blue">${audit.actual_total_units || 0}</span></td>
                <td class="${diffClass}"><strong>${difference > 0 ? '+' : ''}${difference}</strong></td>
                <td>${getStatusBadge(audit.status)}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="viewDetail(${audit.id})">
                        <i class="fas fa-eye"></i> Review
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

let selectedLocationRequestForRollback = null;

function renderApprovalHistoryTable(items) {
    const tbody = document.getElementById('allTableBody');
    if (!tbody) return;
    const list = Array.isArray(items) ? items : [];
    if (list.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Belum ada approval</td></tr>';
        return;
    }
    tbody.innerHTML = list.map(item => {
        const tipeLabel = item.type === 'LOCATION_REQUEST' ? '<span class="badge badge-soft-cyan">Lokasi Baru</span>' : '<span class="badge badge-soft-blue">Audit Lokasi</span>';
        const tgl = item.tanggal ? formatDate(item.tanggal) : '-';
        const onClick = item.type === 'LOCATION_REQUEST'
            ? `viewLocationRequestDetail(${item.id}, '${(item.location_name || '').replace(/'/g, "\\'")}')`
            : `viewDetail(${item.id})`;
        return `
            <tr>
                <td>${tipeLabel}</td>
                <td><strong>${item.code || '-'}</strong></td>
                <td>${item.customer_name || '-'} ${item.customer_code ? '<small class="text-muted">(' + item.customer_code + ')</small>' : ''}</td>
                <td>${item.location_name || '-'}</td>
                <td>${tgl}</td>
                <td>${item.approved_by_name || '-'}</td>
                <td>${getStatusBadge(item.status)}</td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary" onclick="${onClick}" title="Detail">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function viewLocationRequestDetail(id, name) {
    selectedLocationRequestForRollback = id;
    const body = document.getElementById('locationRequestDetailBody');
    body.innerHTML = '<div class="text-center py-3"><i class="fas fa-spinner fa-spin me-2"></i>Memuat...</div>';
    fetch(`<?= base_url('marketing/unit-audit/getLocationRequestDetail/') ?>${id}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success || !data.data) {
                body.innerHTML = '<div class="alert alert-warning">Data tidak ditemukan</div>';
                return;
            }
            const req = data.data;
            const dateApproved = req.approved_at ? new Date(req.approved_at).toLocaleDateString('id-ID') : '-';
            const dateRequested = req.created_at ? new Date(req.created_at).toLocaleDateString('id-ID') : '-';
            const alamat = [req.address, req.city].filter(Boolean).join(', ') || '-';
            let unitsHtml = '';
            const units = req.add_unit_requests || [];
            if (units.length > 0) {
                unitsHtml = `
                    <h6 class="mt-3 mb-2"><i class="fas fa-truck me-2"></i>Daftar Unit (${units.length})</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No Unit</th>
                                    <th>S/N</th>
                                    <th>Merk/Model</th>
                                    <th>Kapasitas</th>
                                    <th class="text-end">Harga Sewa</th>
                                    <th class="text-center">Spare</th>
                                    <th class="text-center">Status</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${units.map(u => {
                                    const statusBadge = u.status === 'APPROVED' ? '<span class="badge badge-soft-green">Approved</span>' : '<span class="badge badge-soft-red">Rejected</span>';
                                    const harga = u.status === 'APPROVED' && (u.harga_sewa != null) ? formatCurrency(u.harga_sewa) : '-';
                                    return `<tr>
                                        <td><strong>${u.no_unit || '-'}</strong></td>
                                        <td class="small">${u.serial_number || '-'}</td>
                                        <td class="small">${u.merk_model || '-'}</td>
                                        <td class="small">${u.kapasitas || '-'}</td>
                                        <td class="text-end">${harga}</td>
                                        <td class="text-center">${u.is_spare ? 'Ya' : '-'}</td>
                                        <td class="text-center">${statusBadge}</td>
                                        <td class="small">${u.review_notes || '-'}</td>
                                    </tr>`;
                                }).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            } else {
                unitsHtml = '<p class="text-muted small mt-2 mb-0">Tidak ada unit yang diajukan</p>';
            }
            body.innerHTML = `
                <dl class="row mb-0">
                    <dt class="col-sm-4">Kode</dt><dd class="col-sm-8"><strong>${req.location_code || '-'}</strong></dd>
                    <dt class="col-sm-4">Customer</dt><dd class="col-sm-8">${req.customer_name || '-'}</dd>
                    <dt class="col-sm-4">Nama Lokasi</dt><dd class="col-sm-8">${req.location_name || '-'}</dd>
                    <dt class="col-sm-4">Area</dt><dd class="col-sm-8">${req.area_name || '-'}</dd>
                    <dt class="col-sm-4">Alamat</dt><dd class="col-sm-8">${alamat}</dd>
                    <dt class="col-sm-4">Diajukan Oleh</dt><dd class="col-sm-8">${req.requested_by_name || '-'}</dd>
                    <dt class="col-sm-4">Diajukan Pada</dt><dd class="col-sm-8">${dateRequested}</dd>
                    <dt class="col-sm-4">Approved Oleh</dt><dd class="col-sm-8">${req.approved_by_name || '-'}</dd>
                    <dt class="col-sm-4">Approved Pada</dt><dd class="col-sm-8">${dateApproved}</dd>
                </dl>
                ${unitsHtml}
            `;
            $('#locationRequestDetailModal').modal('show');
        })
        .catch(() => { body.innerHTML = '<div class="alert alert-danger">Gagal memuat detail</div>'; });
}

function doRollbackFromDetail() {
    if (!selectedLocationRequestForRollback) return;
    const id = selectedLocationRequestForRollback;
    const name = document.querySelector('#locationRequestDetailBody dd:nth-of-type(3)')?.textContent?.trim() || 'Lokasi';
    if (window.OptimaConfirm && window.OptimaConfirm.danger) {
        OptimaConfirm.danger({
            title: 'Rollback Lokasi',
            text: 'Rollback lokasi "' + name + '" ke PENDING? Unit yang sudah ditambahkan ke kontrak akan dihapus.',
            confirmText: '<i class="fas fa-undo me-1"></i>Ya, Rollback',
            onConfirm: function() { doRollback(id); }
        });
    } else {
        if (confirm('Rollback lokasi "' + name + '" ke PENDING? Unit yang sudah ditambahkan ke kontrak akan dihapus.')) doRollback(id);
    }
}

function viewDetail(id) {
    selectedAuditId = id;

    fetch(`<?= base_url('marketing/unit-audit/getApprovalDetail/') ?>${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderDetailModal(data.data);
            }
        });
}

function renderDetailModal(audit) {
    const body = document.getElementById('detailModalBody');
    const footer = document.getElementById('detailModalFooter');

    const difference = audit.actual_total_units - audit.kontrak_total_units;
    const hasDiscrepancy = difference !== 0;
    const hasExtraUnits = (audit.items || []).some(item => item.result === 'EXTRA_UNIT');
    const hasMissingUnits = (audit.items || []).some(item => item.result === 'NO_UNIT_IN_KONTRAK');
    const discrepancyItems = (audit.items || []).filter(i => i.result !== 'MATCH');

    // Build per-discrepancy action summary
    let actionRowsHtml = '';
    discrepancyItems.forEach(item => {
        const parsed = parseNotesObj(item.notes);
        const reason = (parsed.reasons && parsed.reasons[0]) || '';
        let kondisi = '', tindakan = '', badgeCls = 'badge-soft-gray';
        switch (item.result) {
            case 'NO_UNIT_IN_KONTRAK':
                kondisi = 'Unit tidak ditemukan di lapangan';
                tindakan = '<i class="fas fa-minus-circle me-1"></i>Pull dari kontrak';
                badgeCls = 'badge-soft-red'; break;
            case 'MISMATCH_NO_UNIT':
                if (reason === 'UNIT_SWAP') {
                    kondisi = 'Unit fisik berbeda dengan kontrak';
                    const actualDisplay = (item.actual_no_unit && item.actual_no_unit !== item.expected_no_unit)
                        ? item.actual_no_unit
                        : (parsed.keterangan || 'baru');
                    tindakan = '<i class="fas fa-exchange-alt me-1"></i>Swap unit di kontrak → ' + esc(actualDisplay);
                    badgeCls = 'badge-soft-orange';
                } else if (reason === 'LOCATION_MISMATCH') {
                    kondisi = 'Unit berada di lokasi yang salah';
                    tindakan = '<i class="fas fa-map-marker-alt me-1"></i>Update lokasi unit di kontrak';
                    badgeCls = 'badge-soft-blue';
                } else {
                    kondisi = 'Perbedaan unit terdeteksi';
                    tindakan = '<i class="fas fa-info-circle me-1"></i>Dicatat';
                }
                break;
            case 'MISMATCH_SPARE':
                kondisi = 'Unit ditandai sebagai Spare Unit';
                tindakan = '<i class="fas fa-tag me-1"></i>Update status spare di kontrak';
                badgeCls = 'badge-soft-cyan'; break;
            case 'MISMATCH_SPEC':
                kondisi = 'Spesifikasi unit berbeda';
                tindakan = '<i class="fas fa-info-circle me-1"></i>Dicatat saja'; break;
            case 'EXTRA_UNIT':
                kondisi = 'Unit tambahan ditemukan di lapangan';
                tindakan = '<i class="fas fa-plus-circle me-1"></i>Tambah ke kontrak';
                badgeCls = 'badge-soft-green'; break;
            case 'ADD_UNIT':
                kondisi = 'Unit ditambahkan ke kontrak di lokasi ini';
                tindakan = '<i class="fas fa-plus-circle me-1"></i>Tambah ke kontrak';
                badgeCls = 'badge-soft-green'; break;
        }
        const ket = parsed.keterangan ? ` <span class="text-muted small">— ${esc(parsed.keterangan)}</span>` : '';
        actionRowsHtml += `<tr>
            <td><strong>${esc(item.expected_no_unit || item.actual_no_unit || '-')}</strong></td>
            <td class="small">${kondisi}${ket}</td>
            <td><span class="badge ${badgeCls} small">${tindakan}</span></td>
        </tr>`;
    });
    let itemsHtml = '';
    const priceNeededItems = [];
    audit.items.forEach((item, idx) => {
        const resultBadge = getResultBadge(item.result);
        const notesText = formatNotes(item.notes);
        const parsed = parseNotesObj(item.notes);
        const reason = (parsed.reasons && parsed.reasons[0]) || '';
        let rowClass = '';
        switch (item.result) {
            case 'NO_UNIT_IN_KONTRAK':
                rowClass = 'table-danger'; break;
            case 'MISMATCH_NO_UNIT':
                rowClass = (reason === 'LOCATION_MISMATCH') ? 'table-info' : 'table-warning';
                // UNIT_SWAP: no price needed — uses existing harga_sewa from the old row
                break;
            case 'MISMATCH_SPARE':
            case 'MISMATCH_SPEC':
            case 'MISMATCH_SERIAL':
                rowClass = 'table-warning'; break;
            case 'EXTRA_UNIT':
                rowClass = 'table-success';
                priceNeededItems.push({ no: item.actual_no_unit, idx: idx, result: 'EXTRA_UNIT' });
                break;
            case 'ADD_UNIT':
                rowClass = 'table-success';
                priceNeededItems.push({ no: item.actual_no_unit || item.expected_no_unit, idx: idx, result: 'ADD_UNIT' });
                break;
        }
        // For UNIT_SWAP, resolve the "actual" unit to display:
        // prefer actual_no_unit (if different from expected), fallback to keterangan (mechanic's free text)
        let displayActualNo = item.actual_no_unit;
        let displayActualSerial = item.actual_serial;
        if (item.result === 'MISMATCH_NO_UNIT' && reason === 'UNIT_SWAP') {
            if (!item.actual_no_unit || item.actual_no_unit === item.expected_no_unit) {
                const ket = parsed.keterangan || '';
                displayActualNo = ket ? ket + ' <span class="text-muted small">(catatan)</span>' : '—';
                displayActualSerial = null;
            }
        }
        itemsHtml += `
            <tr class="${rowClass}">
                <td class="text-center small">${idx + 1}</td>
                <td class="small fw-semibold">${esc(item.expected_no_unit || '—')}</td>
                <td class="small text-muted">${esc(item.expected_serial || '—')}</td>
                <td class="small fw-semibold">${displayActualNo || '—'}</td>
                <td class="small text-muted">${esc(displayActualSerial || '—')}</td>
                <td>${resultBadge}</td>
                <td class="small">${notesText}</td>
            </tr>
        `;
    });

    // Store for use in confirm modal
    window._auditPriceNeededItems = priceNeededItems;
    window._auditActionRowsHtml   = actionRowsHtml;
    window._auditDiscrepancyCount = discrepancyItems.length;

    body.innerHTML = `
        <!-- Header cards -->
        <div class="row g-2 mb-3">
            <div class="col-md-3"><div class="card bg-light h-100"><div class="card-body py-2"><div class="text-muted small">No. Audit</div><div class="fw-bold small">${esc(audit.audit_number)}</div></div></div></div>
            <div class="col-md-3"><div class="card bg-light h-100"><div class="card-body py-2"><div class="text-muted small">Customer</div><div class="fw-bold small">${esc(audit.customer_name || '-')}</div></div></div></div>
            <div class="col-md-3"><div class="card bg-light h-100"><div class="card-body py-2"><div class="text-muted small">Lokasi</div><div class="fw-bold small">${esc(audit.location_name || '-')}</div></div></div></div>
            <div class="col-md-3"><div class="card bg-light h-100"><div class="card-body py-2"><div class="text-muted small">Kontrak</div><div class="fw-bold small">${esc(audit.no_kontrak || '-')}</div></div></div></div>
        </div>

        <!-- Kontrak vs Actual compact -->
        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="card border-0 bg-light text-center py-2">
                    <div class="text-muted small mb-1">Kontrak Unit</div>
                    <div class="fs-4 fw-bold">${audit.kontrak_total_units || 0}</div>
                    <div class="text-muted small">Spare: ${audit.kontrak_spare_units || 0}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="card border-0 bg-light text-center py-2">
                    <div class="text-muted small mb-1">Aktual (Audit)</div>
                    <div class="fs-4 fw-bold ${hasDiscrepancy ? 'text-danger' : 'text-success'}">${audit.actual_total_units || 0}</div>
                    <div class="text-muted small">Spare: ${audit.actual_spare_units || 0}
                        ${hasDiscrepancy ? `<span class="ms-2 badge badge-soft-red">Selisih ${difference > 0 ? '+' : ''}${difference}</span>` : '<span class="ms-2 badge badge-soft-green">Sesuai</span>'}
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" id="approveAuditKontrakId" value="${audit.kontrak_id || ''}">

        <!-- Detail Unit Table -->
        <div class="mb-3">
            <h6 class="fw-semibold mb-2"><i class="fas fa-list-check me-2 text-primary"></i>Detail Unit</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width:40px">No</th>
                            <th>No Unit <small class="text-muted">(Kontrak)</small></th>
                            <th>Serial <small class="text-muted">(Kontrak)</small></th>
                            <th>No Unit <small class="text-muted">(Aktual)</small></th>
                            <th>Serial <small class="text-muted">(Aktual)</small></th>
                            <th>Hasil</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>${itemsHtml}</tbody>
                </table>
            </div>
        </div>

        <!-- Catatan Mekanik -->
        <div class="mb-3">
            <label class="form-label fw-semibold text-muted small">Catatan Mekanik</label>
            <div class="p-2 bg-light rounded small">${formatMechanicNotes(audit.mechanic_notes)}</div>
        </div>

        <!-- Catatan Marketing -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Catatan Marketing</label>
            <textarea class="form-control" id="marketingNotes" rows="2" placeholder="Catatan untuk approval ini...">${audit.marketing_notes || ''}</textarea>
        </div>

        ${audit.actual_total_units === 0 ? `
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="deactivateLocation">
                <label class="form-check-label text-danger fw-semibold" for="deactivateLocation">
                    <i class="fas fa-ban me-1"></i>Nonaktifkan lokasi ini setelah approve
                </label>
                <div class="form-text">Tidak ada unit ditemukan di lokasi ini. Centang untuk menonaktifkan lokasi setelah approve.</div>
            </div>
        </div>` : ''}
    `;

    // Setup footer buttons based on status
    if (audit.status === 'PENDING_APPROVAL') {
        footer.innerHTML = `
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="button" class="btn btn-danger" onclick="showRejectModal()">Tolak</button>
            <button type="button" class="btn btn-success" onclick="approveAudit()">
                <i class="fas fa-check me-1"></i>Approve
            </button>
        `;

        // No need to load contracts — kontrak_id is already on the audit record
    } else {
        footer.innerHTML = `
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        `;
    }

    $('#detailModal').modal('show');
}

function loadContractsForCustomer(customerId, selectedKontrakId) {
    const select = document.getElementById('kontrakSelect');
    if (!select) return;
    
    select.innerHTML = '<option value="">-- Memuat kontrak... --</option>';
    
    fetch(`<?= base_url('marketing/unit-audit/getContractsForCustomer/') ?>${customerId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data) {
                let options = '<option value="">-- Pilih Kontrak/PO --</option>';
                data.data.forEach(k => {
                    const selected = (k.id == selectedKontrakId) ? ' selected' : '';
                    const label = k.no_kontrak || k.customer_po_number || ('Kontrak #' + k.id);
                    const status = k.status || '';
                    const statusBadge = status === 'ACTIVE' ? '(Aktif)' : `(${status})`;
                    options += `<option value="${k.id}"${selected}>${label} ${statusBadge}</option>`;
                });
                select.innerHTML = options;
                
                // Update hidden field
                if (selectedKontrakId) {
                    document.getElementById('selectedKontrakId').value = selectedKontrakId;
                }
            } else {
                select.innerHTML = '<option value="">-- Tidak ada kontrak --</option>';
            }
        })
        .catch(() => {
            select.innerHTML = '<option value="">-- Gagal memuat kontrak --</option>';
        });
    
    // Handle select change
    select.addEventListener('change', function() {
        document.getElementById('selectedKontrakId').value = this.value;
    });
}

function showRejectModal() {
    $('#detailModal').modal('hide');
    $('#rejectModal').modal('show');
}

function confirmReject() {
    const notes = document.getElementById('rejectNotes').value;

    fetch(`<?= base_url('marketing/unit-audit/rejectLocation/') ?>${selectedAuditId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: csrfBody({ notes: notes })
    })
    .then(res => res.json())
    .then(data => {
        if (window.OptimaNotify) OptimaNotify.info(data.message);
        else alert(data.message);
        $('#rejectModal').modal('hide');
        loadPendingApprovals();
        loadApprovalHistory();
    });
}

function approveAudit() {
    // Build confirm modal content from stored data
    const priceNeededItems = window._auditPriceNeededItems || [];
    const actionRowsHtml   = window._auditActionRowsHtml || '';
    const discrepancyCount = window._auditDiscrepancyCount || 0;

    const actionTable = discrepancyCount === 0
        ? `<div class="alert alert-success py-2 mb-3"><i class="fas fa-check-circle me-2"></i>Semua unit sesuai. Tidak ada perbedaan ditemukan.</div>`
        : `<div class="card mb-3 border-primary">
            <div class="card-header py-2 bg-primary text-white"><i class="fas fa-tasks me-2"></i><strong>Tindakan yang Akan Dilakukan</strong></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light"><tr><th style="width:110px">Unit</th><th>Kondisi di Lapangan</th><th>Tindakan Otomatis</th></tr></thead>
                    <tbody>${actionRowsHtml}</tbody>
                </table>
            </div>
           </div>`;

    let priceInputsHtml = '';
    if (priceNeededItems.length > 0) {
        const rows = priceNeededItems.map(it => `
            <tr>
                <td class="small fw-semibold align-middle">${esc(it.no || '-')}</td>
                <td class="small align-middle">${it.result === 'ADD_UNIT' ? 'Unit Ditambahkan' : 'Unit Tambahan (Extra)'}</td>
                <td>
                    <div class="input-group input-group-sm" style="max-width:200px">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control audit-harga-input" data-idx="${it.idx}"
                            placeholder="Isi harga sewa" inputmode="numeric"
                            oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                    </div>
                </td>
            </tr>`).join('');
        priceInputsHtml = `
            <div class="card mb-3 border-warning">
                <div class="card-header py-2 bg-warning text-dark fw-semibold">
                    <i class="fas fa-tag me-2"></i>Harga Sewa Unit Tambahan
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light"><tr><th>No Unit</th><th>Jenis</th><th style="width:220px">Harga Sewa / Bulan</th></tr></thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>
                <div class="card-footer text-muted small py-2">
                    <i class="fas fa-info-circle me-1"></i>Wajib diisi untuk unit yang baru ditambahkan ke kontrak.
                </div>
            </div>`;
    }

    document.getElementById('approveConfirmBody').innerHTML = actionTable + priceInputsHtml;
    $('#auditApproveConfirmModal').modal('show');
}

function doApproveAudit() {
    const marketingNotes = document.getElementById('marketingNotes')?.value || '';
    const deactivateLocation = document.getElementById('deactivateLocation')?.checked ? '1' : '0';
    const kontrakId = document.getElementById('approveAuditKontrakId')?.value || '';

    const itemPrices = {};
    document.querySelectorAll('.audit-harga-input').forEach(input => {
        const idx = input.dataset.idx;
        const val = input.value.trim();
        if (val) itemPrices[idx] = val;
    });

    const btn = document.querySelector('#auditApproveConfirmModal .btn-success');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memproses...'; }

    fetch(`<?= base_url('marketing/unit-audit/approveLocation/') ?>${selectedAuditId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: csrfBody({
            marketing_notes:     marketingNotes,
            deactivate_location: deactivateLocation,
            kontrak_id:          kontrakId,
            item_prices:         JSON.stringify(itemPrices),
        }),
    })
    .then(res => res.json())
    .then(data => {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-check me-1"></i>Ya, Approve'; }
        if (data.success) {
            $('#auditApproveConfirmModal').modal('hide');
            $('#detailModal').modal('hide');
            if (window.OptimaNotify) OptimaNotify.success(data.message);
            else alert(data.message);
            loadPendingApprovals();
            loadApprovalHistory();
        } else {
            if (window.OptimaNotify) OptimaNotify.error(data.message || 'Gagal approve audit');
            else alert(data.message || 'Gagal approve audit');
        }
    })
    .catch(() => {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-check me-1"></i>Ya, Approve'; }
        if (window.OptimaNotify) OptimaNotify.error('Terjadi kesalahan');
        else alert('Terjadi kesalahan');
    });
}

function getStatusBadge(status) {
    const badges = {
        'DRAFT': '<span class="badge badge-soft-gray">Draft</span>',
        'PRINTED': '<span class="badge badge-soft-cyan">Printed</span>',
        'IN_PROGRESS': '<span class="badge badge-soft-yellow">In Progress</span>',
        'RESULTS_ENTERED': '<span class="badge badge-soft-blue">Results Entered</span>',
        'PENDING_APPROVAL': '<span class="badge badge-soft-orange">Pending</span>',
        'APPROVED': '<span class="badge badge-soft-green">Approved</span>',
        'REJECTED': '<span class="badge badge-soft-red">Rejected</span>'
    };
    return badges[status] || status;
}

function getResultBadge(result) {
    const badges = {
        'MATCH':             '<span class="badge badge-soft-green">Sesuai</span>',
        'NO_UNIT_IN_KONTRAK':'<span class="badge badge-soft-red">Unit Tidak Ada</span>',
        'EXTRA_UNIT':        '<span class="badge badge-soft-yellow">Unit Tambahan</span>',
        'MISMATCH_NO_UNIT':  '<span class="badge badge-soft-orange">Unit Beda</span>',
        'MISMATCH_SERIAL':   '<span class="badge badge-soft-orange">Serial Beda</span>',
        'MISMATCH_SPEC':     '<span class="badge badge-soft-orange">Spesifikasi Beda</span>',
        'MISMATCH_SPARE':    '<span class="badge badge-soft-cyan">Tandai Spare</span>'
    };
    return badges[result] || `<span class="badge badge-soft-gray">${result}</span>`;
}

function formatNotes(notes) {
    if (!notes) return '—';
    let parsed;
    try { parsed = JSON.parse(notes); } catch(e) { return notes; }
    const parts = [];
    if (parsed.reasons && parsed.reasons.length) {
        const labels = parsed.reasons.map(r => {
            if (r === 'LOCATION_MISMATCH') {
                const locName = parsed.extra && parsed.extra.target_location_name;
                return locName ? 'Lokasi salah \u2192 ' + locName : 'Lokasi salah';
            }
            const reasonLabels = {
                'UNIT_SWAP':    'Unit beda',
                'MARK_SPARE':   'Tandai Spare',
                'UNIT_MISSING': 'Unit tidak ada'
            };
            return reasonLabels[r] || r;
        });
        parts.push(labels.join(', '));
    }
    if (parsed.keterangan && parsed.keterangan.trim()) parts.push(parsed.keterangan.trim());
    return parts.length ? parts.join(' — ') : '—';
}

function parseNotesObj(notes) {
    if (!notes) return { reasons: [], keterangan: '', extra: {} };
    try { return JSON.parse(notes); } catch(e) { return { reasons: [], keterangan: notes, extra: {} }; }
}

function esc(s) {
    if (s == null) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function formatMechanicNotes(notes) {
    if (!notes) return '—';
    let parsed;
    try { parsed = JSON.parse(notes); } catch(e) { return notes; }
    const statusMap = { 'sesuai': 'Sesuai', 'tidak_sesuai': 'Tidak Sesuai' };
    const status = statusMap[parsed.field_status] || parsed.field_status || '-';
    const count = parsed.items_count !== undefined ? ` (${parsed.items_count} unit diperiksa)` : '';
    return `Status lapangan: <strong>${status}</strong>${count}`;
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(amount);
}
</script>
<?= $this->endSection() ?>
