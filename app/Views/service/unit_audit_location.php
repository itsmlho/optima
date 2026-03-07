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
                <li class="breadcrumb-item active">Audit Unit per Lokasi</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-map-marker-alt me-2 text-primary"></i>Audit Unit per Lokasi
        </h4>
        <p class="text-muted small mb-0">Buat audit untuk lokasi customer — print dokumen untuk mekanik — input hasil audit</p>
    </div>
    <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAuditModal">
            <i class="fas fa-plus me-1"></i>Buat Audit Baru
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

<!-- Audit List -->
<div class="card shadow-sm">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Audit Lokasi</h5>
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
                        <th>Tanggal Audit</th>
                        <th>Status</th>
                        <th>Kontrak Unit</th>
                        <th>Actual Unit</th>
                        <th>Selisih</th>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Audit Lokasi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Customer</label>
                    <select class="form-select" id="modalCustomerSelect" onchange="loadLocationsForCustomer()">
                        <option value="">-- Pilih Customer --</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Lokasi</label>
                    <select class="form-select" id="modalLocationSelect" onchange="loadLocationPreview()">
                        <option value="">-- Pilih Lokasi --</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Audit</label>
                    <input type="date" class="form-control" id="modalAuditDate" value="<?= date('Y-m-d') ?>">
                </div>
                <div id="locationPreview" class="alert alert-info d-none">
                    <strong>Lokasi:</strong> <span id="previewLocationName"></span><br>
                    <strong>Total Unit:</strong> <span id="previewTotalUnits"></span><br>
                    <strong>Spare Unit:</strong> <span id="previewSpareUnits"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="createAudit()">
                    <i class="fas fa-plus me-1"></i>Buat Audit
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let selectedCustomerId = null;
let selectedLocationId = null;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadCustomersForModal();
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

function loadLocationsForCustomer() {
    const customerId = document.getElementById('modalCustomerSelect').value;
    const locationSelect = document.getElementById('modalLocationSelect');

    if (!customerId) {
        locationSelect.innerHTML = '<option value="">-- Pilih Lokasi --</option>';
        document.getElementById('locationPreview').classList.add('d-none');
        return;
    }

    fetch(`<?= base_url('service/unit-audit/getLocationsForCustomer/') ?>${customerId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                locationSelect.innerHTML = '<option value="">-- Pilih Lokasi --</option>';
                data.data.forEach(l => {
                    locationSelect.innerHTML += `<option value="${l.id}" data-total="${l.total_units}" data-spare="${l.spare_units}">${l.location_name} (${l.total_units} unit${l.spare_units > 0 ? ', ' + l.spare_units + ' spare' : ''})</option>`;
                });
            }
        });
}

function loadLocationPreview() {
    const locationId = document.getElementById('modalLocationSelect').value;
    const selectedOption = locationId.options[locationId.selectedIndex];

    if (!locationId) {
        document.getElementById('locationPreview').classList.add('d-none');
        return;
    }

    document.getElementById('locationPreview').classList.remove('d-none');
    document.getElementById('previewLocationName').textContent = selectedOption.text;
    document.getElementById('previewTotalUnits').textContent = selectedOption.dataset.total || 0;
    document.getElementById('previewSpareUnits').textContent = selectedOption.dataset.spare || 0;
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

    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderAuditsTable(data.data);
            }
        });
}

function renderAuditsTable(audits) {
    const tbody = document.getElementById('auditsTableBody');

    if (audits.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data audit</td></tr>';
        return;
    }

    tbody.innerHTML = audits.map(audit => {
        const difference = audit.actual_total_units - audit.kontrak_total_units;
        const differenceClass = difference !== 0 ? 'text-danger' : 'text-success';
        const statusBadge = getStatusBadge(audit.status);

        return `
            <tr>
                <td><strong>${audit.audit_number}</strong></td>
                <td>${audit.customer_name || '-'}</td>
                <td>${audit.location_name || '-'}</td>
                <td>${formatDate(audit.audit_date)}</td>
                <td>${statusBadge}</td>
                <td>${audit.kontrak_total_units || 0}</td>
                <td>${audit.actual_total_units || '-'}</td>
                <td class="${differenceClass}">${audit.actual_total_units ? (difference > 0 ? '+' : '') + difference : '-'}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="viewAuditDetail(${audit.id})" title="Lihat Detail">
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
        'DRAFT': '<span class="badge bg-secondary">Draft</span>',
        'PRINTED': '<span class="badge bg-info">Printed</span>',
        'IN_PROGRESS': '<span class="badge bg-warning">In Progress</span>',
        'RESULTS_ENTERED': '<span class="badge bg-primary">Results Entered</span>',
        'PENDING_APPROVAL': '<span class="badge bg-orange">Pending Approval</span>',
        'APPROVED': '<span class="badge bg-success">Approved</span>',
        'REJECTED': '<span class="badge bg-danger">Rejected</span>'
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
    window.open(`<?= base_url('service/unit-audit/printLocationAudit/') ?>${id}`, '_blank');
}

function inputResults(id) {
    window.location.href = `<?= base_url('service/unit-audit/inputResults/') ?>${id}`;
}

function submitToMarketing(id) {
    if (!confirm('Kirim audit ini ke Marketing untuk approval?')) return;

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
