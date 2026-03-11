<?= $this->extend('layouts/base') ?>

<?php
/**
 * Audit Approval Module
 * 
 * BADGE SYSTEM: Menggunakan Optima Badge Standards (optima-pro.css)
 * Direct CSS classes - tidak perlu JavaScript helper function
 * 
 * Quick Reference:
 * - Status SUBMITTED  → <span class="badge badge-soft-yellow">Menunggu</span>
 * - Status APPROVED   → <span class="badge badge-soft-green">Approved</span>
 * - Status REJECTED   → <span class="badge badge-soft-red">Rejected</span>
 * - Type LOCATION_MISMATCH → <span class="badge badge-soft-cyan">Lokasi Berbeda</span>
 * - Type UNIT_SWAP    → <span class="badge badge-soft-blue">Tukar Unit</span>
 * - Type ADD_UNIT     → <span class="badge badge-soft-green">Tambah Unit</span>
 * - Type MARK_SPARE   → <span class="badge badge-soft-gray">Tandai Spare</span>
 * - Type UNIT_MISSING → <span class="badge badge-soft-red">Unit Hilang</span>
 * 
 * See optima-pro.css line ~2030 for complete badge standards
 */
$stats = $stats ?? [];
?>

<?= $this->section('content') ?>

<!-- Statistics Cards -->
<div class="row mt-3 mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-warning-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-clock stat-icon text-warning"></i>
                </div>
                <div>
                    <div class="stat-value"><?= $stats['submitted'] ?? 0 ?></div>
                    <div class="text-muted">Menunggu Review</div>
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
                    <div class="text-muted">Disetujui</div>
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
                    <div class="text-muted">Ditolak</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Requests Table Card -->
<div class="card table-card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0">
                <i class="fas fa-check-double me-2 text-warning"></i>
                Audit Approval
            </h5>
            <p class="text-muted small mb-0">
                Review dan persetujuan pengajuan perubahan unit dari tim lapangan
                <span class="ms-2 text-info">
                    <i class="bi bi-info-circle me-1"></i>
                    <small>Tip: Gunakan filter status untuk mempersempit daftar pengajuan</small>
                </span>
            </p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <label class="fw-semibold small me-1"><i class="fas fa-filter text-primary me-1"></i>Status:</label>
            <select class="form-select form-select-sm" id="filterStatus" style="width:180px;" onchange="loadRequests()">
                <option value="SUBMITTED">Menunggu Review</option>
                <option value="APPROVED">Approved</option>
                <option value="REJECTED">Rejected</option>
                <option value="">Semua Status</option>
            </select>
            <button class="btn btn-sm btn-outline-secondary" onclick="loadRequests()">
                <i class="fas fa-sync"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0" id="requestsTable">
                <thead class="table-light">
                    <tr>
                        <th>No. Audit</th>
                        <th>Customer / Kontrak</th>
                        <th>Unit Terkait</th>
                        <th>Jenis Pengajuan</th>
                        <th>Diajukan Oleh</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="8" class="text-center py-4"><i class="fas fa-spinner fa-spin me-2"></i>Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Action Modal -->
<div class="modal fade" id="actionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="modalHeader">
                <h5 class="modal-title"><i class="fas fa-clipboard-check me-2"></i>Review Pengajuan Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <div class="text-center py-3"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>
            </div>
            <div class="modal-footer bg-light" id="actionFooter" style="display:none;">
                <input type="hidden" id="requestId">
                <div class="w-100 mb-3 row">
                    <div class="col-12">
                        <label class="form-label fw-bold">Catatan Review (Opsional)</label>
                        <textarea class="form-control" id="reviewNotes" rows="2" placeholder="Alasan persetujuan/penolakan..."></textarea>
                    </div>
                </div>
                <div class="w-100 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <div>
                        <button type="button" class="btn btn-danger me-2" onclick="processRequest('REJECT')">
                            <i class="fas fa-times me-1"></i>Tolak (Reject)
                        </button>
                        <button type="button" class="btn btn-success" onclick="processRequest('APPROVE')">
                            <i class="fas fa-check me-1"></i>Setujui & Terapkan (Approve)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
const BASE_URL_AUDIT = '<?= base_url() ?>';

$(document).ready(function() {
    loadRequests();
});

function loadRequests() {
    const status = $('#filterStatus').val();

    $.ajax({
        url: BASE_URL_AUDIT + 'service/unit_audit/getAuditRequests',
        type: 'GET',
        data: { status: status },
        success: function(res) {
            if (res.success) {
                renderTable(res.data);
            }
        },
        error: function() {
            $('#requestsTable tbody').html('<tr><td colspan="8" class="text-center text-danger py-4">Error memuat data</td></tr>');
        }
    });
}

function renderTable(data) {
    if (!data || data.length === 0) {
        $('#requestsTable tbody').html('<tr><td colspan="8" class="text-center text-muted py-4">Belum ada pengajuan</td></tr>');
        return;
    }

    let html = '';
    data.forEach(item => {
        const isPending = item.status === 'SUBMITTED';
        const statusBadge = getStatusBadge(item.status);
        const typeLabel = getTypeLabel(item.request_type);
        const date = new Date(item.created_at).toLocaleString('id-ID');
        const unitName = item.no_unit ? `${item.no_unit} (${item.serial_number || '-'})` : 'N/A';
        const actionBtn = isPending 
            ? `<button class="btn btn-sm btn-primary" onclick="openReview(${item.id})">Review</button>`
            : `<button class="btn btn-sm btn-outline-secondary" onclick="openDetail(${item.id})">Detail</button>`;

        html += `<tr>
            <td><strong>${item.audit_number || '-'}</strong></td>
            <td>${item.customer_name || '-'}<br><small class="text-muted">${item.no_kontrak || '-'}</small></td>
            <td>${unitName}</td>
            <td>${typeLabel}</td>
            <td>${item.submitter_name || '-'}</td>
            <td><small>${date}</small></td>
            <td>${statusBadge}</td>
            <td>${actionBtn}</td>
        </tr>`;
    });

    $('#requestsTable tbody').html(html);
}

function getStatusBadge(status) {
    const map = {
        'SUBMITTED': '<span class="badge badge-soft-yellow">Menunggu</span>',
        'APPROVED': '<span class="badge badge-soft-green">Approved</span>',
        'REJECTED': '<span class="badge badge-soft-red">Rejected</span>'
    };
    return map[status] || status;
}

function getTypeLabel(type) {
    const map = {
        'LOCATION_MISMATCH': '<span class="badge badge-soft-cyan">Lokasi Berbeda</span>',
        'UNIT_SWAP': '<span class="badge badge-soft-blue">Tukar Unit</span>',
        'ADD_UNIT': '<span class="badge badge-soft-green">Tambah Unit</span>',
        'MARK_SPARE': '<span class="badge badge-soft-gray">Tandai Spare</span>',
        'UNIT_MISSING': '<span class="badge badge-soft-red">Unit Hilang</span>',
        'OTHER': '<span class="badge badge-soft-gray">Lainnya</span>'
    };
    return map[type] || type;
}

function loadDetailContent(id, isReviewing) {
    $('#detailContent').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>');
    
    $.ajax({
        url: BASE_URL_AUDIT + 'service/unit_audit/getAuditDetail/' + id,
        type: 'GET',
        success: function(res) {
            if (res.success) {
                const item = res.data;
                const currentData = JSON.parse(item.current_data || '{}');
                const proposedData = JSON.parse(item.proposed_data || '{}');

                let html = `
                    <div class="row mb-3 bg-light p-3 rounded mx-1">
                        <div class="col-md-6 mb-2"><strong>No. Audit:</strong> ${item.audit_number}</div>
                        <div class="col-md-6 mb-2"><strong>Customer:</strong> ${item.customer_name || '-'}</div>
                        <div class="col-md-6 mb-2"><strong>Kontrak:</strong> ${item.no_kontrak || '-'}</div>
                        <div class="col-md-6 mb-2"><strong>Diajukan Oleh:</strong> ${item.submitter_name || '-'} pada ${new Date(item.created_at).toLocaleString('id-ID')}</div>
                        <div class="col-12 mt-2">
                            <strong>Catatan Admin/Mekanik:</strong><br>
                            <div class="p-2 border bg-white rounded mt-1">${item.notes || '-'}</div>
                        </div>
                    </div>
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Rincian Perubahan (${getTypeLabel(item.request_type)})</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card h-100 border-secondary">
                                <div class="card-header bg-light py-2 fw-semibold">Data Saat Ini</div>
                                <div class="card-body py-2">
                                    <p class="mb-1"><strong>No Unit:</strong> ${currentData.no_unit || '-'}</p>
                                    <p class="mb-1"><strong>Serial:</strong> ${currentData.serial || '-'}</p>
                                    <p class="mb-0"><strong>Lokasi:</strong> ${currentData.lokasi || '-'}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border-primary">
                                <div class="card-header bg-light py-2 fw-semibold text-primary">Pengajuan Berubah Menjadi</div>
                                <div class="card-body py-2">
                                    ${renderProposedData(item.request_type, proposedData)}
                                </div>
                            </div>
                        </div>
                    </div>`;

                // If approved/rejected, show review notes
                if (item.status !== 'SUBMITTED') {
                    html += `
                    <div class="mt-4 alert alert-${item.status === 'APPROVED' ? 'success' : 'danger'}">
                        <h6 class="fw-bold">Hasil Review — ${item.status}</h6>
                        <p class="mb-1"><strong>Oleh:</strong> ${item.reviewer_name || '-'}</p>
                        <p class="mb-1"><strong>Tanggal:</strong> ${item.reviewed_at ? new Date(item.reviewed_at).toLocaleString('id-ID') : '-'}</p>
                        <p class="mb-0"><strong>Catatan Review:</strong> ${item.review_notes || '-'}</p>
                    </div>`;
                }

                if (isReviewing && item.status === 'SUBMITTED') {
                    // Show warning banner
                    html += `
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Perhatian:</strong> Menyetujui pengajuan ini akan secara otomatis mengubah data Unit dan Relasi Kontrak pada database.
                    </div>`;
                }

                $('#detailContent').html(html);

                if (isReviewing && item.status === 'SUBMITTED') {
                    $('#actionFooter').show();
                    $('#requestId').val(item.id);
                    $('#reviewNotes').val('');
                } else {
                    $('#actionFooter').hide();
                }
            }
        }
    });
}

function renderProposedData(type, data) {
    let html = '';
    switch (type) {
        case 'LOCATION_MISMATCH':
            html = `<p class="mb-0"><strong>Lokasi Baru:</strong> ${data.new_location || '-'}</p>`;
            break;
        case 'UNIT_SWAP':
            html = `<p class="mb-1"><strong>Tukar dgn Unit ID:</strong> ${data.new_unit_id || '-'}</p>
                     <p class="mb-0"><strong>Harga Sewa Baru:</strong> ${data.harga_sewa ? '<span class="text-success fw-semibold">Rp ' + parseInt(data.harga_sewa).toLocaleString('id-ID') + '</span>' : '-'}</p>`;
            break;
        case 'ADD_UNIT':
            html = `<p class="mb-1"><strong>Tambah Unit ID:</strong> ${data.unit_id || '-'}</p>
                     <p class="mb-1"><strong>Sebagai Spare:</strong> ${data.is_spare ? 'Ya' : 'Tidak'}</p>
                     <p class="mb-0"><strong>Harga Sewa:</strong> ${data.harga_sewa ? '<span class="text-success fw-semibold">Rp ' + parseInt(data.harga_sewa).toLocaleString('id-ID') + '</span>' : '-'}</p>`;
            break;
        case 'MARK_SPARE':
            html = '<p class="mb-0"><span class="badge badge-soft-gray">Tandai Unit sbg Spare</span></p>';
            break;
        case 'UNIT_MISSING':
            html = `<p class="mb-0"><strong>Lokasi Terakhir:</strong> ${data.last_known_location || '-'}</p>`;
            break;
        default:
            html = `<p class="mb-0">${data.description || '-'}</p>
                    <p class="mb-0"><strong>Lokasi Baru:</strong> ${data.new_location || '-'}</p>`;
    }
    return html;
}

function openReview(id) {
    $('#actionModal').modal('show');
    loadDetailContent(id, true);
}

function openDetail(id) {
    $('#actionModal').modal('show');
    loadDetailContent(id, false);
}

function processRequest(action) {
    const id = $('#requestId').val();
    const notes = $('#reviewNotes').val();
    
    if (action === 'REJECT' && !notes.trim()) {
        alert('Harap isi catatan review jika menolak pengajuan.');
        $('#reviewNotes').focus();
        return;
    }

    if (!confirm(`Apakah Anda yakin ingin mengeksekusi aksi ini?\n\n${action === 'APPROVE' ? 'Perubahan akan otomatis tersimpan ke database.' : ''}`)) {
        return;
    }

    const endpoint = action === 'APPROVE' ? 'approveRequest' : 'rejectRequest';
    
    // Disable buttons
    $('#actionFooter button').prop('disabled', true);

    $.ajax({
        url: BASE_URL_AUDIT + 'service/unit_audit/' + endpoint + '/' + id,
        type: 'POST',
        data: { notes: notes },
        success: function(res) {
            $('#actionFooter button').prop('disabled', false);
            if (res.success) {
                $('#actionModal').modal('hide');
                loadRequests();
                
                // Refresh top stats
                setTimeout(() => location.reload(), 1500);
            } else {
                alert('Gagal: ' + res.message);
            }
        },
        error: function() {
            $('#actionFooter button').prop('disabled', false);
            alert('Terjadi kesalahan jaringan');
        }
    });
}
</script>
<?= $this->endSection() ?>
