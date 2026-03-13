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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i class="fas fa-clipboard-check me-2"></i>Review Pengajuan Unit</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body" id="unitRequestDetailContent"></div>
            <div class="modal-footer bg-light" id="unitRequestActionFooter" style="display:none;">
                <input type="hidden" id="unitRequestId">
                <textarea class="form-control mb-2" id="unitRequestReviewNotes" rows="2" placeholder="Catatan review (opsional)"></textarea>
                <div class="w-100 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-danger" onclick="processUnitRequest('REJECT')"><i class="fas fa-times me-1"></i>Tolak</button>
                    <button type="button" class="btn btn-success" onclick="processUnitRequest('APPROVE')"><i class="fas fa-check me-1"></i>Approve</button>
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
        const unitInfo = item.no_unit ? (item.no_unit + (item.serial_number ? ' / ' + item.serial_number : '')) : '-';
        const proposed = typeof item.proposed_data === 'string' ? JSON.parse(item.proposed_data || '{}') : (item.proposed_data || {});
        const isPendingLocationAddUnit = item.request_type === 'ADD_UNIT' && proposed.customer_location_id && !item.kontrak_id && item.status === 'SUBMITTED';
        let action;
        if (item.status === 'SUBMITTED') {
            if (isPendingLocationAddUnit) {
                action = `<button class="btn btn-sm btn-outline-secondary" disabled title="Approve via Request Lokasi Baru"><i class="fas fa-lock me-1"></i>Via Lokasi</button>`;
            } else {
                action = `<button class="btn btn-sm btn-primary" onclick="openUnitRequestReview(${item.id})">Review</button>`;
            }
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

function openUnitRequestModal(id, isReview) {
    document.getElementById('unitRequestDetailContent').innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>';
    document.getElementById('unitRequestActionFooter').style.display = isReview ? 'block' : 'none';
    document.getElementById('unitRequestId').value = id;
    $('#unitRequestModal').modal('show');
    
    fetch(URL_GET_AUDIT_DETAIL + '/' + id)
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(res => {
            if (!res.success) { document.getElementById('unitRequestDetailContent').innerHTML = '<div class="alert alert-danger">Gagal memuat</div>'; return; }
            const item = res.data;
            const current = JSON.parse(item.current_data || '{}');
            const proposed = JSON.parse(item.proposed_data || '{}');
            const typeMap = { 'ADD_UNIT':'Tambah Unit','UNIT_SWAP':'Tukar Unit','MARK_SPARE':'Tandai Spare','LOCATION_MISMATCH':'Lokasi Berbeda','UNIT_MISSING':'Unit Hilang','OTHER':'Lainnya' };
            let proposedHtml = '';
            if (item.request_type === 'ADD_UNIT') proposedHtml = `<p><strong>Unit ID:</strong> ${proposed.unit_id || '-'} | <strong>Spare:</strong> ${proposed.is_spare ? 'Ya' : 'Tidak'}</p>`;
            else if (item.request_type === 'UNIT_SWAP') proposedHtml = `<p><strong>Unit Baru ID:</strong> ${proposed.new_unit_id || '-'}</p>`;
            else proposedHtml = `<p>${JSON.stringify(proposed)}</p>`;
            const html = `
                <div class="mb-3"><strong>No. Audit:</strong> ${item.audit_number} | <strong>Customer:</strong> ${item.customer_name || '-'} | <strong>Lokasi:</strong> ${item.lokasi_kontrak || '-'}</div>
                <div class="row">
                    <div class="col-md-6"><div class="card"><div class="card-header py-2">Data Saat Ini</div><div class="card-body py-2"><p class="mb-0">Unit: ${current.no_unit || '-'} | S/N: ${current.serial || '-'}</p></div></div></div>
                    <div class="col-md-6"><div class="card"><div class="card-header py-2">Pengajuan (${typeMap[item.request_type] || item.request_type})</div><div class="card-body py-2">${proposedHtml}</div></div></div>
                </div>
                <div class="mt-2"><strong>Catatan:</strong> ${item.notes || '-'}</div>
            `;
            document.getElementById('unitRequestDetailContent').innerHTML = html;
        })
        .catch(() => {
            document.getElementById('unitRequestDetailContent').innerHTML = '<div class="alert alert-danger">Gagal memuat detail</div>';
        });
}

function processUnitRequest(action) {
    const id = document.getElementById('unitRequestId').value;
    const notes = document.getElementById('unitRequestReviewNotes').value;
    const endpoint = action === 'APPROVE' ? 'approveRequest' : 'rejectRequest';
    fetch(URL_APPROVE_REJECT + '/' + endpoint + '/' + id, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: 'notes=' + encodeURIComponent(notes)
    }).then(r => r.json()).then(res => {
        if (res.success) {
            $('#unitRequestModal').modal('hide');
            if (window.OptimaNotify) OptimaNotify.success(res.message);
            loadUnitRequests();
        } else {
            if (window.OptimaNotify) OptimaNotify.error(res.message || 'Gagal');
        }
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
        body: 'notes='
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
        body: `notes=${encodeURIComponent(notes)}`
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

    let itemsHtml = '';
    audit.items.forEach((item, idx) => {
        const resultBadge = getResultBadge(item.result);
        itemsHtml += `
            <tr>
                <td>${idx + 1}</td>
                <td>${item.expected_no_unit || '-'}</td>
                <td>${item.expected_serial || '-'}</td>
                <td>${item.expected_merk || ''} ${item.expected_model || ''}</td>
                <td class="text-center">${item.expected_is_spare == 1 ? 'YES' : 'NO'}</td>
                <td>${item.actual_no_unit || '-'}</td>
                <td>${item.actual_serial || '-'}</td>
                <td>${item.actual_merk || ''} ${item.actual_model || ''}</td>
                <td class="text-center">${item.actual_is_spare == 1 ? 'YES' : 'NO'}</td>
                <td>${resultBadge}</td>
                <td>${item.notes || '-'}</td>
            </tr>
        `;
    });

    // Service notes contains the audit summary
    const auditSummary = audit.service_notes || '';
    const summaryClass = hasDiscrepancy ? 'alert-warning' : 'alert-success';
    const summaryIcon = hasDiscrepancy ? 'exclamation-triangle' : 'check-circle';
    
    body.innerHTML = `
        <!-- Audit Summary Header -->
        ${auditSummary ? `
        <div class="alert ${summaryClass} mb-3">
            <div class="d-flex align-items-start">
                <i class="fas fa-${summaryIcon} me-2 mt-1"></i>
                <div>
                    <strong>Ringkasan Audit:</strong>
                    <div>${auditSummary}</div>
                </div>
            </div>
        </div>
        ` : ''}
        
        <!-- Summary -->
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body">
                        <div class="text-muted small">No. Audit</div>
                        <div class="fw-bold">${audit.audit_number}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body">
                        <div class="text-muted small">Customer</div>
                        <div class="fw-bold">${audit.customer_name || '-'}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body">
                        <div class="text-muted small">Lokasi</div>
                        <div class="fw-bold">${audit.location_name || '-'}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body">
                        <div class="text-muted small">Kontrak</div>
                        <div class="fw-bold">${audit.no_kontrak || '-'}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comparison -->
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">Data Kontrak</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">Total Unit: <strong>${audit.kontrak_total_units || 0}</strong></div>
                            <div class="col-6">Spare Unit: <strong>${audit.kontrak_spare_units || 0}</strong></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">Data Actual (Audit)</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">Total Unit: <strong>${audit.actual_total_units || 0}</strong></div>
                            <div class="col-6">Spare Unit: <strong>${audit.actual_spare_units || 0}</strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contract/PO Selection and Pricing -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-file-contract me-2"></i>Kontrak & Harga
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Pilih Kontrak/PO <span class="text-danger">*</span></label>
                        <select class="form-select" id="kontrakSelect">
                            <option value="">-- Memuat kontrak... --</option>
                        </select>
                        <input type="hidden" id="selectedKontrakId" value="${audit.kontrak_id || ''}">
                        <div class="form-text">Pilih kontrak yang sesuai dengan lokasi audit ini</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Harga per Unit</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="pricePerUnit" placeholder="0" min="0" value="${audit.price_per_unit || ''}">
                        </div>
                    </div>
                    ${hasDiscrepancy ? `
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Selisih Unit</label>
                        <input type="text" class="form-control ${difference > 0 ? 'text-danger' : 'text-success'}" value="${difference > 0 ? '+' : ''}${difference}" readonly>
                    </div>
                    ` : `
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status</label>
                        <div class="form-control-plaintext">
                            <span class="badge badge-soft-green">Sesuai Kontrak</span>
                        </div>
                    </div>
                    `}
                </div>
                ${hasDiscrepancy ? `
                <div class="row g-3 mt-2">
                    <div class="col-md-6 offset-md-6">
                        <label class="form-label">Total Penyesuaian Harga</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control fw-bold" id="totalAdjustment" readonly>
                        </div>
                    </div>
                </div>
                ` : ''}
            </div>
        </div>

        <!-- Notes -->
        <div class="mb-3">
            <label class="form-label">Catatan Mekanik</label>
            <div class="p-2 bg-light rounded small">${audit.mechanic_notes || '-'}</div>
        </div>
        <div class="mb-3">
            <label class="form-label">Catatan Marketing</label>
            <textarea class="form-control" id="marketingNotes" rows="2" placeholder="Catatan untuk approval ini...">${audit.marketing_notes || ''}</textarea>
        </div>

        <!-- Items Table -->
        <h6>Detail Unit</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>No Unit<br><small>(Kontrak)</small></th>
                        <th>Serial<br><small>(Kontrak)</small></th>
                        <th>Merk/Model<br><small>(Kontrak)</small></th>
                        <th>Spare?</th>
                        <th>No Unit<br><small>(Actual)</small></th>
                        <th>Serial<br><small>(Actual)</small></th>
                        <th>Merk/Model<br><small>(Actual)</small></th>
                        <th>Spare?</th>
                        <th>Hasil</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>${itemsHtml}</tbody>
            </table>
        </div>
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

        // Load contracts for customer
        loadContractsForCustomer(audit.customer_id, audit.kontrak_id);

        // Setup price calculation
        if (hasDiscrepancy) {
            document.getElementById('pricePerUnit').addEventListener('input', function() {
                const price = parseFloat(this.value) || 0;
                const total = price * difference;
                document.getElementById('totalAdjustment').value = formatCurrency(total);
            });
        }
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
        body: `notes=${encodeURIComponent(notes)}`
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
    const kontrakId = document.getElementById('selectedKontrakId')?.value || document.getElementById('kontrakSelect')?.value;
    const pricePerUnit = document.getElementById('pricePerUnit')?.value;
    const marketingNotes = document.getElementById('marketingNotes')?.value || '';

    // Validation
    if (!kontrakId) {
        if (window.OptimaNotify) OptimaNotify.warning('Pilih kontrak/PO terlebih dahulu');
        else alert('Pilih kontrak/PO terlebih dahulu');
        return;
    }

    fetch(`<?= base_url('marketing/unit-audit/approveLocation/') ?>${selectedAuditId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `kontrak_id=${kontrakId}&price_per_unit=${pricePerUnit || ''}&marketing_notes=${encodeURIComponent(marketingNotes)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (window.OptimaNotify) OptimaNotify.success(data.message);
            else alert(data.message);
            $('#detailModal').modal('hide');
            loadPendingApprovals();
            loadApprovalHistory();
        } else {
            if (window.OptimaNotify) OptimaNotify.error(data.message || 'Gagal approve audit');
            else alert(data.message || 'Gagal approve audit');
        }
    })
    .catch(() => {
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
        'MATCH': '<span class="badge badge-soft-green">MATCH</span>',
        'NO_UNIT_IN_KONTRAK': '<span class="badge badge-soft-red">Tidak Ada</span>',
        'EXTRA_UNIT': '<span class="badge badge-soft-yellow">Extra</span>',
        'MISMATCH_NO_UNIT': '<span class="badge badge-soft-orange">No Beda</span>',
        'MISMATCH_SERIAL': '<span class="badge badge-soft-orange">Serial Beda</span>',
        'MISMATCH_SPEC': '<span class="badge badge-soft-orange">Spesifikasi Beda</span>',
        'MISMATCH_SPARE': '<span class="badge badge-soft-cyan">Spare Beda</span>'
    };
    return badges[result] || result;
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
