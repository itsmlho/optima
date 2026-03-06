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
                <li class="breadcrumb-item active">Unit Audit</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-search me-2 text-primary"></i>Unit Audit
        </h4>
        <p class="text-muted small mb-0">Audit unit di lokasi customer — ajukan perubahan jika tidak sesuai</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-white-50">Total Request</div>
                        <div class="fs-4 fw-bold"><?= $stats['total'] ?? 0 ?></div>
                    </div>
                    <div><i class="fas fa-clipboard-list fa-2x opacity-50"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-dark-50">Menunggu Approval</div>
                        <div class="fs-4 fw-bold"><?= $stats['submitted'] ?? 0 ?></div>
                    </div>
                    <div><i class="fas fa-clock fa-2x opacity-50"></i></div>
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

<!-- Step 1: Select Customer -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fas fa-building me-2"></i>Pilih Customer</h5>
    </div>
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-8">
                <label class="form-label">Customer</label>
                <select class="form-select" id="customerSelect" onchange="loadCustomerUnits()">
                    <option value="">-- Pilih Customer --</option>
                </select>
            </div>
            <div class="col-md-4">
                <button class="btn btn-outline-secondary w-100" onclick="loadCustomerList()">
                    <i class="fas fa-sync me-1"></i>Refresh
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Step 2: Unit List for Selected Customer -->
<div class="card shadow-sm mb-4" id="unitListSection" style="display:none;">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-forklift me-2"></i>Daftar Unit — <span id="selectedCustomerName" class="text-primary"></span></h5>
        <span class="badge bg-primary" id="unitCount">0 unit</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="unitTable">
                <thead class="bg-light">
                    <tr>
                        <th>No Unit</th>
                        <th>S/N</th>
                        <th>Merk / Model</th>
                        <th>Lokasi Tercatat</th>
                        <th>Kontrak</th>
                        <th>Status</th>
                        <th>Spare</th>
                        <th width="200">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="8" class="text-center py-4 text-muted">Pilih customer terlebih dahulu</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- History: Past Audit Requests -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Audit Request</h5>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" id="filterStatus" style="width:150px;" onchange="loadAuditHistory()">
                <option value="">Semua Status</option>
                <option value="SUBMITTED">Menunggu</option>
                <option value="APPROVED">Approved</option>
                <option value="REJECTED">Rejected</option>
            </select>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="historyTable">
                <thead class="bg-light">
                    <tr>
                        <th>No. Audit</th>
                        <th>Customer</th>
                        <th>No Unit</th>
                        <th>Jenis Perubahan</th>
                        <th>Status</th>
                        <th>Diajukan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="7" class="text-center py-4"><i class="fas fa-spinner fa-spin me-2"></i>Memuat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Change Request Modal -->
<div class="modal fade" id="requestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Ajukan Perubahan Unit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="requestForm">
                    <input type="hidden" name="customer_id" id="reqCustomerId">
                    <input type="hidden" name="kontrak_id" id="reqKontrakId">
                    <input type="hidden" name="unit_id" id="reqUnitId">

                    <!-- Current Unit Info -->
                    <div class="alert alert-secondary" id="currentUnitInfo">
                        <div class="row small">
                            <div class="col-md-4"><strong>No Unit:</strong> <span id="infoNoUnit">-</span></div>
                            <div class="col-md-4"><strong>Serial:</strong> <span id="infoSerial">-</span></div>
                            <div class="col-md-4"><strong>Lokasi:</strong> <span id="infoLokasi">-</span></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Perubahan <span class="text-danger">*</span></label>
                        <select class="form-select" name="request_type" id="requestType" required onchange="toggleRequestFields()">
                            <option value="LOCATION_MISMATCH">Lokasi Berbeda — Unit di lokasi yang salah</option>
                            <option value="UNIT_SWAP">Unit Berbeda — Tukar unit di kontrak</option>
                            <option value="ADD_UNIT">Tambah Unit — Tambah unit ke kontrak</option>
                            <option value="MARK_SPARE">Tandai Spare — Jadikan unit ini spare</option>
                            <option value="UNIT_MISSING">Unit Tidak Ada — Unit hilang dari lokasi</option>
                            <option value="OTHER">Lainnya</option>
                        </select>
                    </div>

                    <!-- Dynamic fields -->
                    <div id="fieldLocation" class="mb-3">
                        <label class="form-label">Lokasi Aktual (Sebenarnya)</label>
                        <input type="text" class="form-control" name="proposed_location" placeholder="Contoh: PT XYZ - Jakarta Utara">
                    </div>

                    <div id="fieldSwapUnit" class="mb-3" style="display:none;">
                        <label class="form-label">Pilih Unit Pengganti</label>
                        <select class="form-select" name="proposed_unit_id" id="swapUnitSelect">
                            <option value="">-- Pilih Unit --</option>
                        </select>
                    </div>

                    <div id="fieldHargaSewa" class="mb-3" style="display:none;">
                        <label class="form-label">Harga Sewa (Rp/bulan)</label>
                        <input type="number" class="form-control" name="proposed_harga_sewa" placeholder="Opsional">
                    </div>

                    <div id="fieldIsSpare" class="mb-3" style="display:none;">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="proposed_is_spare" id="checkSpare">
                            <label class="form-check-label" for="checkSpare">Unit ini adalah spare</label>
                        </div>
                    </div>

                    <div id="fieldDescription" class="mb-3" style="display:none;">
                        <label class="form-label">Deskripsi Perubahan</label>
                        <textarea class="form-control" name="proposed_description" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Mekanik / Admin</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Jelaskan temuan audit di lapangan..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitAuditRequest()">
                    <i class="fas fa-paper-plane me-1"></i>Submit Request
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
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
const BASE_URL_AUDIT = '<?= base_url() ?>';

$(document).ready(function() {
    loadCustomerList();
    loadAuditHistory();
});

// ── Customer & Units Loading ───────────────────────

function loadCustomerList() {
    $.ajax({
        url: BASE_URL_AUDIT + 'service/unit_audit/getCustomersWithUnits',
        type: 'GET',
        success: function(res) {
            if (res.success) {
                let html = '<option value="">-- Pilih Customer --</option>';
                res.data.forEach(c => {
                    html += `<option value="${c.id}" data-name="${c.customer_name}">${c.customer_name} (${c.customer_code}) — ${c.total_units} unit, ${c.total_contracts} kontrak</option>`;
                });
                $('#customerSelect').html(html);
            }
        }
    });
}

function loadCustomerUnits() {
    const customerId = $('#customerSelect').val();
    if (!customerId) {
        $('#unitListSection').hide();
        return;
    }

    const customerName = $('#customerSelect option:selected').data('name');
    $('#selectedCustomerName').text(customerName);

    $.ajax({
        url: BASE_URL_AUDIT + 'service/unit_audit/getCustomerUnits/' + customerId,
        type: 'GET',
        success: function(res) {
            if (res.success) {
                renderUnitTable(res.data);
                $('#unitListSection').show();
            }
        },
        error: function() {
            $('#unitTable tbody').html('<tr><td colspan="8" class="text-center text-danger py-4">Error memuat data</td></tr>');
        }
    });
}

function renderUnitTable(data) {
    if (!data || data.length === 0) {
        $('#unitTable tbody').html('<tr><td colspan="8" class="text-center text-muted py-4">Tidak ada unit terdaftar</td></tr>');
        $('#unitCount').text('0 unit');
        return;
    }

    $('#unitCount').text(data.length + ' unit');
    let html = '';
    data.forEach(u => {
        const spareBadge = u.is_spare == 1 ? '<span class="badge bg-secondary">Spare</span>' : '-';
        html += `<tr>
            <td><strong>${u.no_unit || '-'}</strong></td>
            <td><small>${u.serial_number || '-'}</small></td>
            <td>${u.merk_unit || ''} ${u.model_unit || ''}</td>
            <td>${u.lokasi_unit || '-'}<br><small class="text-muted">${u.lokasi_kontrak || ''}</small></td>
            <td><small>${u.no_kontrak || '-'}</small></td>
            <td><span class="badge bg-${u.ku_status === 'ACTIVE' ? 'success' : 'warning'}">${u.ku_status}</span></td>
            <td>${spareBadge}</td>
            <td>
                <button class="btn btn-xs btn-outline-success me-1" onclick="markOk(${u.unit_id})" title="Sesuai">
                    <i class="fas fa-check"></i> Sesuai
                </button>
                <button class="btn btn-xs btn-outline-warning" onclick="openChangeRequest(${JSON.stringify(u).replace(/"/g, '&quot;')})" title="Ajukan Perubahan">
                    <i class="fas fa-edit"></i> Ubah
                </button>
            </td>
        </tr>`;
    });
    $('#unitTable tbody').html(html);
}

// ── Change Request ──────────────────────────────────

function markOk(unitId) {
    // Simple confirmation — no request needed for "Sesuai"
    // Could optionally log a "confirmed OK" record
    alert('✅ Unit telah dikonfirmasi sesuai!');
}

function openChangeRequest(unitData) {
    // Populate hidden fields
    $('#reqCustomerId').val($('#customerSelect').val());
    $('#reqKontrakId').val(unitData.kontrak_id);
    $('#reqUnitId').val(unitData.unit_id);

    // Show current info
    $('#infoNoUnit').text(unitData.no_unit || '-');
    $('#infoSerial').text(unitData.serial_number || '-');
    $('#infoLokasi').text(unitData.lokasi_unit || '-');

    // Reset form
    $('#requestForm')[0].reset();
    $('#reqCustomerId').val($('#customerSelect').val());
    $('#reqKontrakId').val(unitData.kontrak_id);
    $('#reqUnitId').val(unitData.unit_id);

    toggleRequestFields();
    $('#requestModal').modal('show');

    // Preload units for swap/add
    loadSwapUnits();
}

function toggleRequestFields() {
    const type = $('#requestType').val();

    // Hide all dynamic fields first
    $('#fieldLocation, #fieldSwapUnit, #fieldHargaSewa, #fieldIsSpare, #fieldDescription').hide();

    switch (type) {
        case 'LOCATION_MISMATCH':
            $('#fieldLocation').show();
            break;
        case 'UNIT_SWAP':
            $('#fieldSwapUnit, #fieldHargaSewa').show();
            break;
        case 'ADD_UNIT':
            $('#fieldSwapUnit, #fieldHargaSewa, #fieldIsSpare').show();
            break;
        case 'MARK_SPARE':
            // No extra fields
            break;
        case 'UNIT_MISSING':
            $('#fieldLocation').show();
            break;
        case 'OTHER':
            $('#fieldLocation, #fieldDescription').show();
            break;
    }
}

function loadSwapUnits() {
    $.ajax({
        url: BASE_URL_AUDIT + 'service/unit_audit/getAvailableUnits',
        type: 'GET',
        success: function(res) {
            if (res.success) {
                let html = '<option value="">-- Pilih Unit --</option>';
                res.data.forEach(u => {
                    html += `<option value="${u.id_inventory_unit}">${u.no_unit || u.no_unit_na || 'UNIT-' + u.id_inventory_unit} — ${u.merk_unit} ${u.model_unit} (${u.pelanggan})</option>`;
                });
                $('#swapUnitSelect').html(html);
            }
        }
    });
}

function submitAuditRequest() {
    const form = document.getElementById('requestForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);

    $.ajax({
        url: BASE_URL_AUDIT + 'service/unit_audit/createAuditRequest',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            if (res.success) {
                $('#requestModal').modal('hide');
                loadAuditHistory();
                alert('✅ ' + res.message + '\nNo. Audit: ' + res.data.audit_number);
            } else {
                alert('Error: ' + res.message);
            }
        },
        error: function() {
            alert('Terjadi kesalahan saat menyimpan');
        }
    });
}

// ── Audit History ───────────────────────────────────

function loadAuditHistory() {
    const status = $('#filterStatus').val();
    const customerId = $('#customerSelect').val();

    $.ajax({
        url: BASE_URL_AUDIT + 'service/unit_audit/getAuditRequests',
        type: 'GET',
        data: { status: status, customer_id: customerId || '' },
        success: function(res) {
            if (res.success) {
                renderHistoryTable(res.data);
            }
        },
        error: function() {
            $('#historyTable tbody').html('<tr><td colspan="7" class="text-center text-danger py-4">Error memuat data</td></tr>');
        }
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
        const typeLabel = getTypeLabel(item.request_type);
        const date = new Date(item.created_at).toLocaleDateString('id-ID');

        html += `<tr>
            <td><strong>${item.audit_number || '-'}</strong></td>
            <td>${item.customer_name || '-'}</td>
            <td>${item.no_unit || item.no_unit_na || '-'}</td>
            <td>${typeLabel}</td>
            <td>${statusBadge}</td>
            <td><small>${date}</small></td>
            <td><button class="btn btn-xs btn-outline-primary" onclick="viewDetail(${item.id})"><i class="fas fa-eye"></i></button></td>
        </tr>`;
    });

    $('#historyTable tbody').html(html);
}

function getStatusBadge(status) {
    const map = {
        'DRAFT': '<span class="badge bg-secondary">Draft</span>',
        'SUBMITTED': '<span class="badge bg-warning text-dark">Menunggu Approval</span>',
        'APPROVED': '<span class="badge bg-success">Approved</span>',
        'REJECTED': '<span class="badge bg-danger">Rejected</span>'
    };
    return map[status] || status;
}

function getTypeLabel(type) {
    const map = {
        'LOCATION_MISMATCH': '<span class="badge bg-info">Lokasi Berbeda</span>',
        'UNIT_SWAP': '<span class="badge bg-primary">Tukar Unit</span>',
        'ADD_UNIT': '<span class="badge bg-success">Tambah Unit</span>',
        'MARK_SPARE': '<span class="badge bg-secondary">Tandai Spare</span>',
        'UNIT_MISSING': '<span class="badge bg-danger">Unit Hilang</span>',
        'OTHER': '<span class="badge bg-dark">Lainnya</span>'
    };
    return map[type] || type;
}

function viewDetail(id) {
    $.ajax({
        url: BASE_URL_AUDIT + 'service/unit_audit/getAuditDetail/' + id,
        type: 'GET',
        success: function(res) {
            if (res.success) {
                showDetailModal(res.data);
            }
        }
    });
}

function showDetailModal(item) {
    const currentData = JSON.parse(item.current_data || '{}');
    const proposedData = JSON.parse(item.proposed_data || '{}');

    let html = `
        <div class="row mb-3">
            <div class="col-md-6"><strong>No. Audit:</strong> ${item.audit_number}</div>
            <div class="col-md-6"><strong>Status:</strong> ${getStatusBadge(item.status)}</div>
            <div class="col-md-6"><strong>Customer:</strong> ${item.customer_name || '-'}</div>
            <div class="col-md-6"><strong>Kontrak:</strong> ${item.no_kontrak || '-'}</div>
            <div class="col-md-6"><strong>Diajukan oleh:</strong> ${item.submitter_name || '-'}</div>
            <div class="col-md-6"><strong>Tanggal:</strong> ${new Date(item.created_at).toLocaleString('id-ID')}</div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <div class="alert alert-secondary">
                    <h6 class="fw-bold">Data Saat Ini</h6>
                    <p class="mb-1"><strong>No Unit:</strong> ${currentData.no_unit || '-'}</p>
                    <p class="mb-1"><strong>Serial:</strong> ${currentData.serial || '-'}</p>
                    <p class="mb-0"><strong>Lokasi:</strong> ${currentData.lokasi || '-'}</p>
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
            <strong>Catatan:</strong> ${item.notes || '-'}
        </div>`;

    if (item.reviewed_by) {
        html += `<div class="alert alert-${item.status === 'APPROVED' ? 'success' : 'danger'}">
            <h6 class="fw-bold">Review</h6>
            <p class="mb-1"><strong>Oleh:</strong> ${item.reviewer_name || '-'}</p>
            <p class="mb-1"><strong>Tanggal:</strong> ${item.reviewed_at ? new Date(item.reviewed_at).toLocaleString('id-ID') : '-'}</p>
            <p class="mb-0"><strong>Catatan:</strong> ${item.review_notes || '-'}</p>
        </div>`;
    }

    $('#detailContent').html(html);
    $('#detailModal').modal('show');
}

function renderProposedData(type, data) {
    let html = '';
    switch (type) {
        case 'LOCATION_MISMATCH':
            html = `<p class="mb-0"><strong>Lokasi Baru:</strong> ${data.new_location || '-'}</p>`;
            break;
        case 'UNIT_SWAP':
            html = `<p class="mb-1"><strong>Unit Pengganti ID:</strong> ${data.new_unit_id || '-'}</p>
                     <p class="mb-0"><strong>Harga Sewa:</strong> ${data.harga_sewa ? 'Rp ' + parseInt(data.harga_sewa).toLocaleString('id-ID') : '-'}</p>`;
            break;
        case 'ADD_UNIT':
            html = `<p class="mb-1"><strong>Unit ID:</strong> ${data.unit_id || '-'}</p>
                     <p class="mb-1"><strong>Spare:</strong> ${data.is_spare ? 'Ya' : 'Tidak'}</p>
                     <p class="mb-0"><strong>Harga Sewa:</strong> ${data.harga_sewa ? 'Rp ' + parseInt(data.harga_sewa).toLocaleString('id-ID') : '-'}</p>`;
            break;
        case 'MARK_SPARE':
            html = '<p class="mb-0">Unit akan ditandai sebagai <strong>Spare</strong></p>';
            break;
        case 'UNIT_MISSING':
            html = `<p class="mb-0"><strong>Lokasi Terakhir:</strong> ${data.last_known_location || '-'}</p>`;
            break;
        default:
            html = `<p class="mb-0">${data.description || '-'}</p>`;
    }
    return html;
}
</script>
<?= $this->endSection() ?>
