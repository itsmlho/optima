<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<?php
/**
 * Unit Verification (Service) — Menampilkan data verifikasi yang sudah dibuat dari Unit Audit.
 * Tidak ada tombol "Buat Verifikasi Baru" — verifikasi dibuat dari halaman Unit Audit.
 * Tampilan: Customer > Lokasi > daftar audit (Print, Input Hasil, Kirim ke Marketing).
 */
$stats = $stats ?? [];
helper('ui');
?>

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
        <p class="text-muted small mb-0">Proses verifikasi unit hasil audit — Print form, Input hasil, Kirim ke Marketing</p>
                        </div>
    <div>
        <a href="<?= base_url('service/unit-audit') ?>" class="btn btn-outline-primary">
            <i class="fas fa-search me-1"></i>Buka Unit Audit
        </a>
                        </div>
                    </div>

<!-- Info banner -->
<div class="alert alert-info border-0 mb-4 py-2 d-flex align-items-center gap-3">
    <i class="fas fa-info-circle fa-lg flex-shrink-0"></i>
    <div class="small">
        Untuk <strong>membuat verifikasi baru</strong>, buka halaman <a href="<?= base_url('service/unit-audit') ?>"><strong>Unit Audit</strong></a> → pilih Customer → pilih Lokasi → klik <strong>Verifikasi</strong>.
        Halaman ini menampilkan data yang sudah dibuat dari Unit Audit.
                        </div>
                            </div>
                            
<!-- Stats -->
<div class="row g-3 mb-4">
    <?php
    $statItems = [
        ['label' => 'Total',          'value' => $stats['total']           ?? 0, 'class' => 'bg-primary text-white'],
        ['label' => 'Draft',          'value' => $stats['draft']           ?? 0, 'class' => 'bg-secondary text-white'],
        ['label' => 'Printed',        'value' => $stats['printed']         ?? 0, 'class' => 'bg-info text-white'],
        ['label' => 'In Progress',    'value' => $stats['in_progress']     ?? 0, 'class' => 'bg-warning text-dark'],
        ['label' => 'Menunggu Approval', 'value' => $stats['pending_approval'] ?? 0, 'class' => 'bg-orange text-white'],
        ['label' => 'Approved',       'value' => $stats['approved']        ?? 0, 'class' => 'bg-success text-white'],
    ];
    foreach ($statItems as $s): ?>
    <div class="col-md-2">
        <div class="card <?= $s['class'] ?>">
            <div class="card-body py-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="small opacity-75"><?= $s['label'] ?></div>
                    <div class="fs-4 fw-bold"><?= $s['value'] ?></div>
                                        </div>
                                    </div>
                                        </div>
                                    </div>
    <?php endforeach; ?>
                            </div>

<!-- Grouped: Customer > Lokasi > Audits -->
<div class="card shadow-sm">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-building me-2"></i>Verifikasi per Customer & Lokasi</h5>
        <div class="d-flex gap-2 align-items-center">
            <select class="form-select form-select-sm" id="statusFilter" onchange="loadGroupedData()" style="width:160px;">
                <option value="">Semua Status</option>
                <option value="PENDING_APPROVAL">Menunggu Approval</option>
                <option value="APPROVED">Approved</option>
                <option value="REJECTED">Rejected</option>
                <option value="DRAFT">Draft</option>
                <option value="PRINTED">Printed</option>
                <option value="IN_PROGRESS">In Progress</option>
                <option value="RESULTS_ENTERED">Results Entered</option>
            </select>
            <button class="btn btn-sm btn-outline-secondary" onclick="loadGroupedData()">
                <i class="fas fa-sync"></i>
                                        </button>
                                    </div>
                                </div>
    <div class="card-body p-0" id="groupedBody">
        <div class="text-center py-5 text-muted">
            <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
            <p class="mb-0">Memuat data...</p>
                            </div>
                        </div>
                    </div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
const BASE_UV = '<?= base_url() ?>';

document.addEventListener('DOMContentLoaded', function() {
    loadGroupedData();
});

function loadGroupedData() {
    const body = document.getElementById('groupedBody');
    body.innerHTML = '<div class="text-center py-5 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-3"></i><p class="mb-0">Memuat data...</p></div>';

    fetch(BASE_UV + 'service/unit-audit/getVerificationGroupedFromAudits')
        .then(r => r.json())
        .then(resp => {
            if (!resp.success) throw new Error(resp.message || 'Gagal memuat data');
            renderGrouped(resp.data);
        })
        .catch(err => {
            body.innerHTML = '<div class="alert alert-danger m-3"><i class="fas fa-exclamation-circle me-2"></i>' + (err.message || 'Gagal memuat data') + '</div>';
        });
}

function renderGrouped(customers) {
    const body = document.getElementById('groupedBody');
    const filterStatus = document.getElementById('statusFilter').value;

    if (!customers || customers.length === 0) {
        body.innerHTML = `
            <div class="text-center py-5 text-muted">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <p class="mb-1">Belum ada data verifikasi</p>
                <p class="small">Buat verifikasi dari halaman <a href="${BASE_UV}service/unit-audit">Unit Audit</a></p>
            </div>`;
            return;
        }
        
    let html = '<div class="p-3" id="uvAccordion">';
    let customerIdx = 0;

    customers.forEach((cust, ci) => {
        // Filter locations if status filter is set
        const filteredLocs = cust.locations.filter(loc => {
            if (!filterStatus) return true;
            return loc.audits.some(a => a.status === filterStatus);
        });
        if (filteredLocs.length === 0) return;

        const expanded = ci < 3;
        const totalAudits = filteredLocs.reduce((sum, loc) => sum + loc.audits.length, 0);

        html += `
        <div class="uv-cust-block border rounded mb-2" id="uvCust${ci}">
            <div class="uv-cust-header p-3 d-flex align-items-center gap-3"
                 class="cursor-pointer" style="background:#f8f9fa;border-radius:6px 6px 0 0;"
                 onclick="toggleBlock('uvCustChild${ci}', this)">
                <span class="uv-caret text-muted" style="transition:transform .2s;display:inline-block;${expanded ? '' : 'transform:rotate(-90deg)'}">
                    <i class="fas fa-chevron-down"></i>
                </span>
                <strong class="text-dark">${escHtml(cust.customer_name)}</strong>
                <span class="badge badge-soft-gray">${filteredLocs.length} lokasi</span>
                <span class="badge badge-soft-cyan">${totalAudits} audit</span>
                </div>
            <div id="uvCustChild${ci}" style="${expanded ? '' : 'display:none'}" class="border-top">`;

        filteredLocs.forEach((loc, li) => {
            const filteredAudits = filterStatus
                ? loc.audits.filter(a => a.status === filterStatus)
                : loc.audits;
            if (filteredAudits.length === 0) return;

            html += `
            <div class="uv-loc-block border-bottom" id="uvLoc${ci}_${li}">
                <div class="uv-loc-header px-4 py-2 d-flex align-items-center gap-3"
                     class="cursor-pointer" style="background:#fafafa;"
                     onclick="toggleBlock('uvLocChild${ci}_${li}', this)">
                    <span class="uv-caret text-muted" style="transition:transform .2s;display:inline-block;">
                        <i class="fas fa-chevron-down"></i>
                    </span>
                    <span><i class="fas fa-map-marker-alt text-muted me-1"></i><strong>${escHtml(loc.location_name)}</strong></span>
                    <span class="badge badge-soft-blue">${filteredAudits.length} audit</span>
                    <span class="small text-muted">Kontrak: ${escHtml(loc.no_kontrak_masked)} | PO: ${escHtml(loc.no_po_masked || '-')}</span>
                    <span class="small">Periode: ${escHtml(loc.periode_text)} — <span class="badge ${(loc.periode_status_text || '').indexOf('Sudah lewat') === 0 ? 'bg-danger' : (loc.periode_status_text || '').indexOf('Tinggal') === 0 ? 'bg-warning text-dark' : 'bg-secondary'}">${escHtml(loc.periode_status_text || '')}</span></span>
                        </div>
                <div id="uvLocChild${ci}_${li}" class="border-top">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No. Audit</th>
                                <th>Tgl Audit</th>
                                <th>Status</th>
                                <th>Unit (Kontrak/Aktual)</th>
                                <th>Discrepancy</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${filteredAudits.map(a => buildAuditRow(a)).join('')}
                        </tbody>
                    </table>
                        </div>
            </div>`;
        });

        html += `</div></div>`;
        customerIdx++;
    });

    html += '</div>';

    if (customerIdx === 0) {
        body.innerHTML = `<div class="text-center py-5 text-muted"><i class="fas fa-filter fa-2x mb-3"></i><p>Tidak ada data untuk filter "${filterStatus}"</p></div>`;
        return;
    }

    body.innerHTML = html;
}

function buildAuditRow(a) {
    const statusMap = {
        'DRAFT':           '<span class="badge badge-soft-gray">Draft</span>',
        'PRINTED':         '<span class="badge badge-soft-cyan">Printed</span>',
        'IN_PROGRESS':     '<span class="badge badge-soft-yellow">In Progress</span>',
        'RESULTS_ENTERED': '<span class="badge badge-soft-blue">Results Entered</span>',
        'PENDING_APPROVAL':'<span class="badge badge-soft-orange">Menunggu Approval</span>',
        'APPROVED':        '<span class="badge badge-soft-green">Approved</span>',
        'REJECTED':        '<span class="badge badge-soft-red">Rejected</span>',
    };
    const statusBadge = statusMap[a.status] || `<span class="badge badge-soft-gray">${a.status}</span>`;
    const discrepancy = a.has_discrepancy
        ? '<span class="badge badge-soft-red">Ada</span>'
        : '<span class="badge badge-soft-green">Tidak Ada</span>';
    const unitInfo = a.kontrak_total_units
        ? `${a.kontrak_total_units} / ${a.actual_total_units != null ? a.actual_total_units : '—'}`
        : '—';
    const auditDate = a.audit_date ? new Date(a.audit_date).toLocaleDateString('id-ID') : '—';

    let actions = '';
    // Print (always available)
    actions += `<button class="btn btn-xs btn-outline-secondary me-1" onclick="printAudit(${a.audit_id})" title="Print Form">
        <i class="fas fa-print"></i>
    </button>`;
    // Verifikasi Unit (per-unit view, always available while editable)
    actions += `<button class="btn btn-xs btn-outline-primary me-1" onclick="verifyUnit(${a.audit_id})" title="Verifikasi Unit (detail)">
        <i class="fas fa-clipboard-check"></i>
    </button>`;
    // Input Hasil (matrix view, only if not yet approved/rejected)
    if (['DRAFT','PRINTED','IN_PROGRESS','RESULTS_ENTERED'].includes(a.status)) {
        actions += `<button class="btn btn-xs btn-outline-warning me-1" onclick="inputResults(${a.audit_id})" title="Input Hasil">
            <i class="fas fa-edit"></i>
        </button>`;
    }
    // Submit to Marketing
    if (a.status === 'RESULTS_ENTERED') {
        actions += `<button class="btn btn-xs btn-outline-info me-1" onclick="submitToMarketing(${a.audit_id})" title="Kirim ke Marketing">
            <i class="fas fa-paper-plane"></i>
        </button>`;
    }
    // View detail
    actions += `<button class="btn btn-xs btn-outline-primary" onclick="viewAudit(${a.audit_id})" title="Lihat Detail">
        <i class="fas fa-eye"></i>
    </button>`;

    return `<tr>
        <td class="small"><strong>${escHtml(a.audit_number)}</strong></td>
        <td class="small">${auditDate}</td>
        <td>${statusBadge}</td>
        <td class="small">${unitInfo}</td>
        <td>${discrepancy}</td>
        <td class="text-end">${actions}</td>
    </tr>`;
}

function toggleBlock(id, headerEl) {
    const child = document.getElementById(id);
    if (!child) return;
    const show = child.style.display === 'none';
    child.style.display = show ? '' : 'none';
    const caret = headerEl ? headerEl.querySelector('.uv-caret') : null;
    if (caret) caret.style.transform = show ? '' : 'rotate(-90deg)';
}

function printAudit(id) {
    window.open(BASE_UV + 'service/unit-verification/print/' + id, '_blank');
}

function inputResults(id) {
    window.location.href = BASE_UV + 'service/unit-audit/inputResults/' + id;
}

function viewAudit(id) {
    window.location.href = BASE_UV + 'service/unit-audit/inputResults/' + id;
}

function verifyUnit(id) {
    // Mulai dari unit pertama (index 1)
    window.location.href = BASE_UV + 'service/unit-verification/unit/' + id + '/1';
}

function submitToMarketing(id) {
                Swal.fire({
        title: 'Kirim ke Marketing?',
        text: 'Verifikasi ini akan dikirim ke Marketing untuk approval.',
        icon: 'question',
                    showCancelButton: true,
        confirmButtonText: 'Ya, Kirim!',
        cancelButtonText: 'Batal'
                }).then((result) => {
        if (!result.isConfirmed) return;
        fetch(BASE_UV + 'service/unit-audit/submitToMarketing/' + id, { method: 'POST' })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    OptimaNotify.success(data.message || 'Berhasil');
                    loadGroupedData();
                    } else {
                    OptimaNotify.error(data.message || 'Gagal');
                }
            })
            .catch(() => OptimaNotify.error('Terjadi kesalahan'));
    });
}

function escHtml(s) {
    if (s == null) return '';
    const d = document.createElement('div');
    d.textContent = String(s);
    return d.innerHTML;
}
</script>
<style>
.bg-orange { background-color: #fd7e14 !important; }
</style>
<?= $this->endSection() ?>
