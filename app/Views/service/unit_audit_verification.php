<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<?php $stats = $stats ?? []; ?>

<!-- Page Header -->
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('service') ?>">Service</a></li>
                <li class="breadcrumb-item active">Unit Verification</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-clipboard-check me-2 text-primary"></i>Unit Verification
        </h4>
        <p class="text-muted small mb-0">Verifikasi data unit dari hasil audit — perbaiki data yang tidak sesuai, langsung update ke inventory tanpa approval</p>
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
        <div class="card bg-info text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-white-50">In Progress</div>
                        <div class="fs-4 fw-bold"><?= $stats['in_progress'] ?? 0 ?></div>
                    </div>
                    <div><i class="fas fa-tools fa-2x opacity-50"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-warning text-dark">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-dark-50">Results Entered</div>
                        <div class="fs-4 fw-bold"><?= $stats['results_entered'] ?? 0 ?></div>
                    </div>
                    <div><i class="fas fa-edit fa-2x opacity-50"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
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
    <div class="col-md-2">
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
</div>

<!-- Audit List - Data yang perlu diverifikasi -->
<div class="card shadow-sm">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Audit — Verifikasi Unit</h5>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" id="statusFilter" onchange="loadAudits()" style="width: 180px;">
                <option value="" selected>Semua Status</option>
                <option value="PRINTED">Printed</option>
                <option value="IN_PROGRESS">In Progress</option>
                <option value="RESULTS_ENTERED">Results Entered</option>
                <option value="PENDING_APPROVAL">Menunggu Approval</option>
                <option value="APPROVED">Approved</option>
            </select>
            <button class="btn btn-sm btn-outline-secondary" onclick="loadAudits()" title="Refresh">
                <i class="fas fa-sync"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="auditsTable">
                <thead>
                    <tr>
                        <th>No. Audit</th>
                        <th>Customer</th>
                        <th>Lokasi</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Unit</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="auditsTableBody">
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-spinner fa-spin me-2"></i>Memuat data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadAudits();
});

function loadAudits() {
    const status = document.getElementById('statusFilter').value;
    let url = '<?= base_url('service/unit-audit/getLocationAudits') ?>';
    if (status) url += '?status=' + encodeURIComponent(status);

    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderAuditsTable(data.data);
            } else {
                document.getElementById('auditsTableBody').innerHTML =
                    '<tr><td colspan="7" class="text-center text-danger py-4">Gagal memuat data</td></tr>';
            }
        })
        .catch(() => {
            document.getElementById('auditsTableBody').innerHTML =
                '<tr><td colspan="7" class="text-center text-danger py-4">Gagal memuat data</td></tr>';
        });
}

function renderAuditsTable(audits) {
    const tbody = document.getElementById('auditsTableBody');

    if (!audits || audits.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data audit</td></tr>';
        return;
    }

    tbody.innerHTML = audits.map(audit => {
        const statusBadge = getStatusBadge(audit.status);
        const canVerify = ['PRINTED', 'IN_PROGRESS', 'RESULTS_ENTERED'].includes(audit.status);

        return `
            <tr>
                <td><strong>${audit.audit_number || '-'}</strong></td>
                <td>${audit.customer_name || '-'}</td>
                <td>${audit.location_name || '-'}</td>
                <td>${formatDate(audit.audit_date)}</td>
                <td>${statusBadge}</td>
                <td>${audit.kontrak_total_units || 0} unit</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        ${canVerify ? `
                            <a href="<?= base_url('service/unit-verification/unit/') ?>${audit.id}/1" class="btn btn-primary" title="Verifikasi Unit">
                                <i class="fas fa-clipboard-check me-1"></i>Verifikasi Unit
                            </a>
                        ` : ''}
                        <a href="<?= base_url('service/unit-audit/inputResults/') ?>${audit.id}" class="btn btn-outline-secondary" title="Input Hasil">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="<?= base_url('service/unit-verification/print/') ?>${audit.id}" target="_blank" class="btn btn-outline-secondary" title="Print">
                            <i class="fas fa-print"></i>
                        </a>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function getStatusBadge(status) {
    const badges = {
        'DRAFT': '<span class="badge badge-soft-gray">Draft</span>',
        'PRINTED': '<span class="badge badge-soft-cyan">Printed</span>',
        'IN_PROGRESS': '<span class="badge badge-soft-yellow">In Progress</span>',
        'RESULTS_ENTERED': '<span class="badge badge-soft-blue">Results Entered</span>',
        'PENDING_APPROVAL': '<span class="badge badge-soft-orange">Pending Approval</span>',
        'APPROVED': '<span class="badge badge-soft-green">Approved</span>',
        'REJECTED': '<span class="badge badge-soft-red">Rejected</span>'
    };
    return badges[status] || status;
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>
<style>
.bg-orange { background-color: #fd7e14 !important; }
</style>
<?= $this->endSection() ?>
