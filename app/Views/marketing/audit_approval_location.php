<?= $this->extend('layouts/base') ?>

<?php
/**
 * Approve Audit Unit (Location Audit) Module
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

<!-- Pending Approvals Table Card -->
<div class="card table-card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0">
                <i class="fas fa-check-circle me-2 text-success"></i>
                Approve Audit Unit
            </h5>
            <p class="text-muted small mb-0">
                Review dan approve hasil audit unit dari Service
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
    <div class="card-body">
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

<!-- Riwayat Approval Table Card -->
<div class="card table-card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2 text-primary"></i>
                Riwayat Approval
            </h5>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 table-sm" id="allTable">
                <thead class="table-light">
                    <tr>
                        <th>No. Audit</th>
                        <th>Customer</th>
                        <th>Lokasi</th>
                        <th>Tanggal</th>
                        <th>Kontrak</th>
                        <th>Actual</th>
                        <th>Selisih</th>
                        <th>Adjust. Price</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="allTableBody">
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            <i class="fas fa-spinner fa-spin me-2"></i>Memuat data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Audit Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <!-- Content loaded via AJAX -->
            </div>
            <div class="modal-footer" id="detailModalFooter">
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
                    <textarea class="form-control" id="rejectNotes" rows="3" placeholder="Masukkan alasan penolakan..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="confirmReject()">Tolak Audit</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let selectedAuditId = null;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadPendingApprovals();
    loadAllAudits();
});

function loadPendingApprovals() {
    fetch('<?= base_url('marketing/unit-audit/getPendingApprovals') ?>')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderPendingTable(data.data);
            }
        });
}

function loadAllAudits() {
    fetch('<?= base_url('service/unit-audit/getLocationAudits') ?>')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderAllTable(data.data);
            }
        });
}

function renderPendingTable(audits) {
    const tbody = document.getElementById('pendingTableBody');

    if (audits.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">Tidak ada audit menunggu approval</td></tr>';
        return;
    }

    tbody.innerHTML = audits.map(audit => {
        const difference = audit.actual_total_units - audit.kontrak_total_units;
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

function renderAllTable(audits) {
    const tbody = document.getElementById('allTableBody');

    if (audits.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted py-4">Tidak ada data</td></tr>';
        return;
    }

    tbody.innerHTML = audits.map(audit => {
        const difference = (audit.actual_total_units || 0) - (audit.kontrak_total_units || 0);
        const diffClass = difference !== 0 ? 'text-danger' : 'text-success';

        return `
            <tr>
                <td><strong>${audit.audit_number}</strong></td>
                <td>${audit.customer_name || '-'}</td>
                <td>${audit.location_name || '-'}</td>
                <td>${formatDate(audit.audit_date)}</td>
                <td><span class="badge badge-soft-blue">${audit.kontrak_total_units || 0}</span></td>
                <td><span class="badge badge-soft-blue">${audit.actual_total_units || '-'}</span></td>
                <td class="${diffClass}">${audit.actual_total_units ? (difference > 0 ? '+' : '') + difference : '-'}</td>
                <td>${audit.total_price_adjustment ? '<span class="text-success fw-semibold">' + formatCurrency(audit.total_price_adjustment) + '</span>' : '-'}</td>
                <td>${getStatusBadge(audit.status)}</td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary" onclick="viewDetail(${audit.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
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

    body.innerHTML = `
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

        <!-- Price Input (if has discrepancy) -->
        ${hasDiscrepancy ? `
        <div class="alert alert-info">
            <h6 class="alert-heading">Penyesuaian Harga</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Selisih Unit</label>
                    <input type="text" class="form-control" value="${difference > 0 ? '+' : ''}${difference}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Harga per Unit</label>
                    <input type="number" class="form-control" id="pricePerUnit" placeholder="Harga per unit" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Total Penyesuaian</label>
                    <input type="text" class="form-control" id="totalAdjustment" readonly>
                </div>
            </div>
        </div>
        ` : ''}

        <!-- Notes -->
        <div class="mb-3">
            <label class="form-label">Catatan Mekanik</label>
            <div class="p-2 bg-light rounded">${audit.mechanic_notes || '-'}</div>
        </div>
        <div class="mb-3">
            <label class="form-label">Catatan Service</label>
            <div class="p-2 bg-light rounded">${audit.service_notes || '-'}</div>
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

        // Setup price calculation
        if (hasDiscrepancy) {
            document.getElementById('pricePerUnit').addEventListener('input', function() {
                const price = parseFloat(this.value) || 0;
                const total = price * difference;
                document.getElementById('totalAdjustment').value = total > 0 ? formatCurrency(total) : '-';
            });
        }
    } else {
        footer.innerHTML = `
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        `;
    }

    $('#detailModal').modal('show');
}

function showRejectModal() {
    $('#detailModal').modal('hide');
    $('#rejectModal').modal('show');
}

function confirmReject() {
    const notes = document.getElementById('rejectNotes').value;

    if (!notes.trim()) {
        alert('Alasan penolakan harus diisi');
        return;
    }

    fetch(`<?= base_url('marketing/unit-audit/rejectLocation/') ?>${selectedAuditId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `notes=${encodeURIComponent(notes)}`
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        $('#rejectModal').modal('hide');
        loadPendingApprovals();
        loadAllAudits();
    });
}

function approveAudit() {
    const pricePerUnit = document.getElementById('pricePerUnit')?.value;
    const marketingNotes = document.getElementById('marketingNotes')?.value || '';

    fetch(`<?= base_url('marketing/unit-audit/approveLocation/') ?>${selectedAuditId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `price_per_unit=${pricePerUnit || ''}&marketing_notes=${encodeURIComponent(marketingNotes)}`
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        $('#detailModal').modal('hide');
        loadPendingApprovals();
        loadAllAudits();
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
