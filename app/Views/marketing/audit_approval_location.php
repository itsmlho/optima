<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<?php $stats = $stats ?? []; ?>

<!-- Page Header -->
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('marketing') ?>">Marketing</a></li>
                <li class="breadcrumb-item active">Approve Audit Unit</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-check-circle me-2 text-success"></i>Approve Audit Unit
        </h4>
        <p class="text-muted small mb-0">Review dan approve hasil audit unit dari Service</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
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
    <div class="col-md-3">
        <div class="card bg-orange text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-white-50">Pending Approval</div>
                        <div class="fs-4 fw-bold"><?= $stats['pending_approval'] ?? 0 ?></div>
                    </div>
                    <div><i class="fas fa-hourglass-half fa-2x opacity-50"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-white-50">Approved</div>
                        <div class="fs-4 fw-bold"><?= $stats['approved'] ?? 0 ?></div>
                    </div>
                    <div><i class="fas fa-check-circle fa-2x opacity-50"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-white-50">Rejected</div>
                        <div class="fs-4 fw-bold"><?= $stats['rejected'] ?? 0 ?></div>
                    </div>
                    <div><i class="fas fa-times-circle fa-2x opacity-50"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Approvals List -->
<div class="card shadow-sm">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Audit Menunggu Approval</h5>
        <button class="btn btn-sm btn-outline-secondary" onclick="loadPendingApprovals()">
            <i class="fas fa-sync"></i> Refresh
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="pendingTable">
                <thead>
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

<!-- All Audits -->
<div class="card shadow-sm mt-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Approval</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover" id="allTable">
                <thead>
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
    < class="modal-dialog modal-xl">
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
                <td>${audit.kontrak_total_units || 0}</td>
                <td>${audit.actual_total_units || 0}</td>
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
                <td>${audit.kontrak_total_units || 0}</td>
                <td>${audit.actual_total_units || '-'}</td>
                <td class="${diffClass}">${audit.actual_total_units ? (difference > 0 ? '+' : '') + difference : '-'}</td>
                <td>${audit.total_price_adjustment ? formatCurrency(audit.total_price_adjustment) : '-'}</td>
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
                <thead>
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
        'DRAFT': '<span class="badge bg-secondary">Draft</span>',
        'PRINTED': '<span class="badge bg-info">Printed</span>',
        'IN_PROGRESS': '<span class="badge bg-warning">In Progress</span>',
        'RESULTS_ENTERED': '<span class="badge bg-primary">Results Entered</span>',
        'PENDING_APPROVAL': '<span class="badge bg-orange">Pending</span>',
        'APPROVED': '<span class="badge bg-success">Approved</span>',
        'REJECTED': '<span class="badge bg-danger">Rejected</span>'
    };
    return badges[status] || status;
}

function getResultBadge(result) {
    const badges = {
        'MATCH': '<span class="badge bg-success">MATCH</span>',
        'NO_UNIT_IN_KONTRAK': '<span class="badge bg-danger">Tidak Ada</span>',
        'EXTRA_UNIT': '<span class="badge bg-warning">Extra</span>',
        'MISMATCH_NO_UNIT': '<span class="badge bg-danger">No Beda</span>',
        'MISMATCH_SERIAL': '<span class="badge bg-warning">Serial Beda</span>',
        'MISMATCH_SPEC': '<span class="badge bg-warning">Spesifikasi Beda</span>',
        'MISMATCH_SPARE': '<span class="badge bg-info">Spare Beda</span>'
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
<style>
.bg-orange { background-color: #fd7e14 !important; }
.badge.bg-orange { background-color: #fd7e14 !important; }
</style>
<?= $this->endSection() ?>
