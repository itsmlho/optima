<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<?php
/**
 * Unit Verification (Service) — Satu halaman untuk verifikasi unit di lokasi customer.
 * Menggantikan "Audit per Lokasi"; alur: pilih lokasi → print form → mekanik cek → input hasil → kirim ke Marketing.
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
        <p class="text-muted small mb-0">Verifikasi fisik unit di lokasi customer sesuai kontrak — pilih lokasi, print form untuk mekanik, input hasil, kirim ke Marketing.</p>
    </div>
    <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAuditModal">
            <i class="fas fa-plus me-1"></i>Buat Verifikasi Baru
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
        <div class="card bg-info text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-white-50">Printed</div>
                        <div class="fs-4 fw-bold"><?= $stats['printed'] ?? 0 ?></div>
                    </div>
                    <div><i class="fas fa-print fa-2x opacity-50"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-warning text-dark">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-dark-50">In Progress</div>
                        <div class="fs-4 fw-bold"><?= $stats['in_progress'] ?? 0 ?></div>
                    </div>
                    <div><i class="fas fa-tools fa-2x opacity-50"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-orange text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-white-50">Menunggu Approval</div>
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

<!-- Alur singkat: agar user paham tanpa bingung -->
<div class="alert alert-light border mb-4 py-3">
    <h6 class="alert-heading mb-2"><i class="fas fa-route me-2 text-primary"></i>Alur Verifikasi</h6>
    <ol class="mb-0 small text-muted">
        <li><strong>Pilih lokasi</strong> — dari data Customer & Lokasi di bawah, atau tombol "Buat Verifikasi Baru".</li>
        <li><strong>Print form</strong> — bawa ke lokasi untuk dicek mekanik (status: Printed → In Progress).</li>
        <li><strong>Input hasil</strong> — setelah cek lapangan, isi hasil di halaman input (Actual vs Expected).</li>
        <li><strong>Kirim ke Marketing</strong> — untuk approval. Setelah disetujui, status Approved.</li>
    </ol>
</div>

<!-- Data by Customer & Lokasi (sumber: kontrak aktif, sama seperti Contract > By Customer) -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-building me-2"></i>Data Unit per Customer & Lokasi</h5>
        <button class="btn btn-sm btn-outline-secondary" onclick="loadGroupedData()" id="btnRefreshGrouped">
            <i class="fas fa-sync"></i> Refresh
        </button>
    </div>
    <div class="card-body p-0" id="groupedDataBody">
        <div class="text-center py-5 text-muted">
            <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
            <p class="mb-0">Memuat data...</p>
        </div>
    </div>
</div>

<!-- Daftar Verifikasi (audit yang sudah dibuat) -->
<div class="card shadow-sm">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Verifikasi</h5>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" id="statusFilter" onchange="loadAudits()" style="width: 150px;">
                <option value="">Semua Status</option>
                <option value="DRAFT">Draft</option>
                <option value="PRINTED">Printed</option>
                <option value="IN_PROGRESS">In Progress</option>
                <option value="RESULTS_ENTERED">Results Entered</option>
                <option value="PENDING_APPROVAL">Pending Approval</option>
                <option value="APPROVED">Approved</option>
                <option value="REJECTED">Rejected</option>
            </select>
            <button class="btn btn-sm btn-outline-secondary" onclick="loadAudits()">
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
                        <th>No. Kontrak</th>
                        <th>Periode</th>
                        <th>Tanggal Audit</th>
                        <th>Status</th>
                        <th>Unit</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="auditsTableBody">
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

<!-- Create Audit Modal -->
<div class="modal fade" id="createAuditModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Verifikasi Lokasi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Customer</label>
                        <select class="form-select" id="modalCustomerSelect" onchange="loadLocationsForCustomer()">
                            <option value="">-- Pilih Customer --</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Lokasi</label>
                        <select class="form-select" id="modalLocationSelect" onchange="loadLocationPreview()">
                            <option value="">-- Pilih Lokasi --</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Audit</label>
                    <input type="date" class="form-control" id="modalAuditDate" value="<?= date('Y-m-d') ?>" style="max-width: 200px;">
                </div>
                <div id="locationPreview" class="alert alert-info d-none">
                    <div class="row small">
                        <div class="col-md-6">
                            <strong>Lokasi:</strong> <span id="previewLocationName"></span><br>
                            <strong>No. Kontrak:</strong> <span id="previewNoKontrak" class="font-monospace"></span><br>
                            <strong>Periode:</strong> <span id="previewPeriode"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Total Unit:</strong> <span id="previewTotalUnits"></span><br>
                            <strong>Spare Unit:</strong> <span id="previewSpareUnits"></span><br>
                            <div id="previewLastAudit" class="mt-2"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="createAudit()">
                    <i class="fas fa-plus me-1"></i>Buat Verifikasi
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let locationsData = {};

document.addEventListener('DOMContentLoaded', function() {
    loadCustomersForModal();
    loadGroupedData();
    loadAudits();
});

function loadCustomersForModal() {
    fetch('<?= base_url('service/unit-audit/getCustomersWithLocations') ?>')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('modalCustomerSelect');
                select.innerHTML = '<option value="">-- Pilih Customer --</option>';
                data.data.forEach(c => {
                    select.innerHTML += `<option value="${c.id}">${c.customer_name} (${c.total_locations} lokasi, ${c.total_units} unit)</option>`;
                });
            }
        });
}

function loadLocationsForCustomer(locationIdToSelect) {
    const customerId = document.getElementById('modalCustomerSelect').value;
    const locationSelect = document.getElementById('modalLocationSelect');

    if (!customerId) {
        locationSelect.innerHTML = '<option value="">-- Pilih Lokasi --</option>';
        document.getElementById('locationPreview').classList.add('d-none');
        locationsData = {};
        return;
    }

    fetch(`<?= base_url('service/unit-audit/getLocationsForCustomer/') ?>${customerId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                locationSelect.innerHTML = '<option value="">-- Pilih Lokasi --</option>';
                locationsData = {};
                data.data.forEach(l => {
                    locationsData[l.id] = l;
                    const badge = l.last_audit ? '<span class="badge bg-info ms-1">Sudah pernah</span>' : '';
                    const reaudit = l.due_for_reaudit && l.last_audit ? '<span class="badge bg-warning ms-1">Harus audit ulang</span>' : '';
                    locationSelect.innerHTML += `<option value="${l.id}">${l.location_name} (${l.total_units} unit) ${badge}${reaudit}</option>`;
                });
                if (locationIdToSelect) {
                    locationSelect.value = locationIdToSelect;
                    loadLocationPreview();
                }
            }
        });
}

function loadLocationPreview() {
    const locationSelect = document.getElementById('modalLocationSelect');
    const locationId = locationSelect.value;
    const preview = document.getElementById('locationPreview');

    if (!locationId) {
        preview.classList.add('d-none');
        return;
    }

    const loc = locationsData[locationId];
    if (!loc) {
        preview.classList.add('d-none');
        return;
    }

    preview.classList.remove('d-none');
    document.getElementById('previewLocationName').textContent = loc.location_name || '-';
    document.getElementById('previewNoKontrak').textContent = loc.no_kontrak_masked || '-';
    document.getElementById('previewPeriode').textContent = loc.periode_text || '-';
    document.getElementById('previewTotalUnits').textContent = loc.total_units || 0;
    document.getElementById('previewSpareUnits').textContent = loc.spare_units || 0;

    const lastAuditDiv = document.getElementById('previewLastAudit');
    if (loc.last_audit) {
        const la = loc.last_audit;
        const completedAt = la.completed_at ? new Date(la.completed_at).toLocaleDateString('id-ID') : '-';
        const checkedBy = la.checked_by_name || '-';
        const submitter = la.submitter_name || '-';
        const dueText = loc.due_for_reaudit ? '<span class="badge bg-warning">Harus audit ulang</span>' : '<span class="badge bg-success">Dalam periode audit</span>';
        lastAuditDiv.innerHTML = `<strong>Audit terakhir:</strong> ${completedAt}<br>
            <strong>Dicek oleh:</strong> ${checkedBy}<br>
            <strong>Diinput oleh:</strong> ${submitter}<br>${dueText}`;
    } else {
        lastAuditDiv.innerHTML = '<span class="text-muted">Belum pernah di-audit</span>';
    }
}

function createAudit() {
    const customerId = document.getElementById('modalCustomerSelect').value;
    const locationId = document.getElementById('modalLocationSelect').value;
    const auditDate = document.getElementById('modalAuditDate').value;

    if (!customerId || !locationId) {
        alert('Customer dan Lokasi harus dipilih');
        return;
    }

    fetch('<?= base_url('service/unit-audit/createLocationAudit') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `customer_id=${customerId}&customer_location_id=${locationId}&audit_date=${auditDate}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            $('#createAuditModal').modal('hide');
            loadGroupedData();
            loadAudits();
        } else {
            alert(data.message);
        }
    });
}

function loadAudits() {
    const status = document.getElementById('statusFilter').value;
    let url = '<?= base_url('service/unit-audit/getLocationAudits') ?>';
    if (status) url += `?status=${status}`;
    const tbody = document.getElementById('auditsTableBody');
    tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin me-2"></i>Memuat data...</td></tr>';

    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderAuditsTable(data.data);
            } else {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger py-4"><i class="fas fa-exclamation-circle me-2"></i>' + (data.message || 'Gagal memuat data') + '</td></tr>';
            }
        })
        .catch(err => {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger py-4"><i class="fas fa-exclamation-circle me-2"></i>Gagal memuat: ' + (err.message || 'Network error') + '</td></tr>';
        });
}

function loadGroupedData() {
    const body = document.getElementById('groupedDataBody');
    body.innerHTML = '<div class="text-center py-5 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-3"></i><p class="mb-0">Memuat data...</p></div>';

    fetch('<?= base_url('service/unit-audit/getVerificationGrouped') ?>')
        .then(r => r.json())
        .then(resp => {
            if (!resp.success) throw new Error(resp.message || 'Gagal memuat data');
            renderGroupedView(resp.data);
        })
        .catch(err => {
            body.innerHTML = '<div class="alert alert-danger m-3"><i class="fas fa-exclamation-circle me-2"></i>' + (err.message || 'Gagal memuat data') + '</div>';
        });
}

function renderGroupedView(customers) {
    const body = document.getElementById('groupedDataBody');
    if (!customers || customers.length === 0) {
        body.innerHTML = '<div class="text-center py-5 text-muted"><i class="fas fa-inbox fa-3x mb-3"></i><p>Tidak ada data customer/lokasi dengan unit aktif</p></div>';
        return;
    }

    let html = '<div class="p-3" id="uvGroupedAccordion">';
    customers.forEach((cust, ci) => {
        const expanded = ci < 2;
        const locs = cust.locations || [];
        html += `
        <div class="uv-customer-block border rounded mb-2" id="uvBlock${ci}">
            <div class="uv-customer-header p-3 d-flex align-items-center gap-3 ${expanded ? '' : 'collapsed'}" style="cursor:pointer;background:#f8f9fa;" onclick="toggleUvBlock(${ci})">
                <span class="uv-caret text-muted" style="transition:transform 0.2s;display:inline-block;${expanded ? '' : 'transform:rotate(-90deg)'}"><i class="fas fa-chevron-down"></i></span>
                <strong class="text-dark">${escapeHtml(cust.customer_name)}</strong>
                <span class="badge bg-secondary">${locs.length} lokasi</span>
                <span class="badge bg-info">${cust.total_units || 0} unit</span>
            </div>
            <div class="uv-child-wrap border-top bg-white" id="uvChild${ci}" style="${expanded ? '' : 'display:none'}">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Lokasi</th>
                                <th>No. Kontrak</th>
                                <th>Periode</th>
                                <th>Unit</th>
                                <th>Audit terakhir</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${locs.map(loc => buildLocationRow(loc, cust.id)).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>`;
    });
    html += '</div>';
    body.innerHTML = html;
}

function toggleUvBlock(idx) {
    const wrap = document.getElementById('uvChild' + idx);
    const block = document.getElementById('uvBlock' + idx);
    const header = block ? block.querySelector('.uv-customer-header') : null;
    const caret = block ? block.querySelector('.uv-caret') : null;
    if (!wrap || !block) return;
    const show = wrap.style.display === 'none';
    wrap.style.display = show ? '' : 'none';
    if (header) header.classList.toggle('collapsed', !show);
    if (caret) caret.style.transform = show ? '' : 'rotate(-90deg)';
}

function buildLocationRow(loc, customerId) {
    const lastAudit = loc.last_audit;
    let auditInfo = '—';
    if (lastAudit) {
        const completedAt = lastAudit.reviewed_at || lastAudit.audit_completed_date || '';
        const d = completedAt ? new Date(completedAt).toLocaleDateString('id-ID') : '—';
        const due = loc.due_for_reaudit ? ' <span class="badge bg-warning text-dark">Harus audit ulang</span>' : '';
        auditInfo = d + due;
    } else {
        auditInfo = '<span class="text-muted">Belum pernah</span>';
    }
    const unitInfo = (loc.total_units || 0) + (loc.spare_units ? ' (' + loc.spare_units + ' spare)' : '');
    return `
    <tr>
        <td>${escapeHtml(loc.location_name || '-')}</td>
        <td><code class="small">${escapeHtml(loc.no_kontrak_masked || '-')}</code></td>
        <td class="small">${escapeHtml(loc.periode_text || '-')}</td>
        <td>${unitInfo}</td>
        <td class="small">${auditInfo}</td>
        <td class="text-end">
            <button type="button" class="btn btn-sm btn-primary" onclick="openCreateAuditForLocation(${customerId}, ${loc.id})" title="Buat Verifikasi">
                <i class="fas fa-plus me-1"></i>Buat Verifikasi
            </button>
        </td>
    </tr>`;
}

function escapeHtml(s) {
    if (s == null) return '';
    const div = document.createElement('div');
    div.textContent = s;
    return div.innerHTML;
}

function openCreateAuditForLocation(customerId, locationId) {
    document.getElementById('modalCustomerSelect').value = customerId;
    loadLocationsForCustomer(locationId);
    const modalEl = document.getElementById('createAuditModal');
    if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        new bootstrap.Modal(modalEl).show();
    }
}

function renderAuditsTable(audits) {
    const tbody = document.getElementById('auditsTableBody');

    if (audits.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data verifikasi</td></tr>';
        return;
    }

    tbody.innerHTML = audits.map(audit => {
        const statusBadge = getStatusBadge(audit.status);
        const unitInfo = `${audit.kontrak_total_units || 0} / ${audit.actual_total_units ?? '-'}`;

        return `
            <tr>
                <td><strong>${audit.audit_number}</strong></td>
                <td>${audit.customer_name || '-'}</td>
                <td>${audit.location_name || '-'}</td>
                <td><code class="small">${audit.no_kontrak_masked || '-'}</code></td>
                <td class="small">${audit.periode_text || '-'}</td>
                <td>${formatDate(audit.audit_date)}</td>
                <td>${statusBadge}</td>
                <td>${unitInfo}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="viewAuditDetail(${audit.id})" title="Lihat / Input Hasil">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${audit.status === 'DRAFT' ? `
                            <button class="btn btn-outline-success" onclick="printAudit(${audit.id})" title="Print Form">
                                <i class="fas fa-print"></i>
                            </button>
                        ` : ''}
                        ${audit.status === 'PRINTED' || audit.status === 'IN_PROGRESS' ? `
                            <button class="btn btn-outline-warning" onclick="inputResults(${audit.id})" title="Input Hasil">
                                <i class="fas fa-edit"></i>
                            </button>
                        ` : ''}
                        ${audit.status === 'RESULTS_ENTERED' ? `
                            <button class="btn btn-outline-info" onclick="submitToMarketing(${audit.id})" title="Kirim ke Marketing">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        ` : ''}
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
    const date = new Date(dateStr);
    return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

function viewAuditDetail(id) {
    window.location.href = `<?= base_url('service/unit-audit/inputResults/') ?>${id}`;
}

function printAudit(id) {
    window.open(`<?= base_url('service/unit-verification/print/') ?>${id}`, '_blank');
}

function inputResults(id) {
    window.location.href = `<?= base_url('service/unit-audit/inputResults/') ?>${id}`;
}

function submitToMarketing(id) {
    if (!confirm('Kirim verifikasi ini ke Marketing untuk approval?')) return;

    fetch(`<?= base_url('service/unit-audit/submitToMarketing/') ?>${id}`, {
        method: 'POST'
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        loadAudits();
    });
}
</script>
<style>
.bg-orange { background-color: #fd7e14 !important; }
</style>
<?= $this->endSection() ?>
