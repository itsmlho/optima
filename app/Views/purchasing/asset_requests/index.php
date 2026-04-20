<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white pt-4 pb-3 border-bottom-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-1">
                <i class="fas fa-tag me-2 text-primary"></i><?= lang('App.asset_requests') ?>
            </h5>
            <p class="text-muted small mb-0">
                Review dan setujui permintaan nomor aset dari Warehouse untuk unit NON_ASSET_STOCK.
            </p>
        </div>
    </div>
</div>

<!-- Filter Tabs + Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom">
        <ul class="nav nav-tabs card-header-tabs" id="statusTabs">
            <li class="nav-item">
                <a class="nav-link active" data-status="" href="#">Semua</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-status="PENDING" href="#">
                    <i class="fas fa-hourglass-half me-1 text-warning"></i>Pending
                    <span class="badge badge-soft-yellow ms-1" id="count-pending"></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-status="APPROVED" href="#">
                    <i class="fas fa-check-circle me-1 text-success"></i>Disetujui
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-status="REJECTED" href="#">
                    <i class="fas fa-times-circle me-1 text-danger"></i>Ditolak
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="tblAssetRequests" class="table table-hover align-middle mb-0 w-100">
                <thead class="table-light">
                    <tr>
                        <th>STOCK No / No Saat Ini</th>
                        <th>Jenis Request</th>
                        <th>Unit</th>
                        <th>Serial Number</th>
                        <th>Diminta Oleh</th>
                        <th>Tgl Request</th>
                        <th>Status</th>
                        <th>Nomor Aset / Baru</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Approve -->
<div class="modal fade" id="modalApprove" tabindex="-1" aria-labelledby="modalApproveLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalApproveLabel">
                    <i class="fas fa-check-circle me-2 text-success"></i>Setujui Permintaan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Masukkan nomor aset resmi untuk unit <strong id="approveStockLabel"></strong>:</p>

                <!-- Last no_unit reference -->
                <div class="alert alert-light border d-flex align-items-center gap-2 py-2 mb-3" id="lastNoUnitInfo">
                    <i class="fas fa-info-circle text-muted"></i>
                    <span class="small text-muted">Memuat nomor terakhir...</span>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nomor Aset <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control font-monospace" id="approveAssetNumber" placeholder="Contoh: 7001" maxlength="50">
                        <button class="btn btn-outline-secondary" type="button" id="btnUseSuggested" title="Gunakan nomor yang disarankan">
                            <i class="fas fa-magic me-1"></i>Gunakan Saran
                        </button>
                    </div>
                    <div class="form-text">Nomor aset harus unik di seluruh sistem.</div>
                </div>
                <input type="hidden" id="approveRequestId">
                <input type="hidden" id="approvedSuggestedNumber">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnConfirmApprove">
                    <i class="fas fa-check me-1"></i>Setujui
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Reject -->
<div class="modal fade" id="modalReject" tabindex="-1" aria-labelledby="modalRejectLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRejectLabel">
                    <i class="fas fa-times-circle me-2 text-danger"></i>Tolak Permintaan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Tolak permintaan nomor aset untuk unit <strong id="rejectStockLabel"></strong>?</p>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Alasan Penolakan <span class="text-muted fw-normal">(opsional)</span></label>
                    <textarea class="form-control" id="rejectNotes" rows="3" placeholder="Catatan untuk tim Warehouse..."></textarea>
                </div>
                <input type="hidden" id="rejectRequestId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnConfirmReject">
                    <i class="fas fa-times me-1"></i>Tolak
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
$(document).ready(function () {
    const baseUrl        = <?= json_encode(base_url()) ?>;
    const datatableUrl   = baseUrl + 'purchasing/asset-requests/datatable';
    const approveBaseUrl = baseUrl + 'purchasing/asset-requests/';
    let currentStatusFilter = '';

    // ── Badge helper ────────────────────────────────────────
    function statusBadge(status) {
        const map = {
            PENDING:  '<span class="badge badge-soft-yellow">Pending</span>',
            APPROVED: '<span class="badge badge-soft-green">Disetujui</span>',
            REJECTED: '<span class="badge badge-soft-red">Ditolak</span>',
        };
        return map[status] || '<span class="badge badge-soft-gray">' + status + '</span>';
    }

    // ── DataTable ────────────────────────────────────────────
    const table = $('#tblAssetRequests').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        order: [[4, 'desc']],
        dom: '<"top">rt<"bottom d-flex justify-content-between align-items-center mt-3"ipl><"clear">',
        ajax: {
            url: datatableUrl,
            type: 'POST',
            data: function (d) {
                d[window.csrfTokenName] = window.csrfTokenValue;
                d.status_filter = currentStatusFilter;
            },
            dataSrc: function (json) {
                if (json.csrf_hash) window.csrfTokenValue = json.csrf_hash;
                return json.data;
            }
        },
        columns: [
            {
                data: 'stock_number',
                render: function (data) {
                    return data ? '<span class="badge badge-soft-cyan">' + data + '</span>' : '-';
                }
            },
            {
                data: 'request_type',
                render: function (data) {
                    if (data === 'CHANGE') return '<span class="badge badge-soft-orange">CHANGE</span>';
                    return '<span class="badge badge-soft-blue">NEW</span>';
                }
            },
            {
                data: 'merk_unit',
                render: function (data, type, row) {
                    const brand   = data || '-';
                    const model   = row.model_unit || '';
                    const jenis   = row.unit_jenis || '';
                    const unitUrl = baseUrl + 'warehouse/inventory/unit/' + row.id_inventory_unit;
                    return '<a href="' + unitUrl + '" target="_blank" class="fw-semibold text-decoration-none">'
                        + brand + (model ? ' ' + model : '')
                        + '</a>'
                        + (jenis ? '<br><small class="text-muted">' + jenis + '</small>' : '');
                }
            },
            { data: 'serial_number', defaultContent: '-', className: 'font-monospace' },
            { data: 'requested_by_name', defaultContent: '-' },
            {
                data: 'requested_at',
                render: function (data) {
                    if (!data) return '-';
                    const d = new Date(data);
                    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                }
            },
            {
                data: 'status',
                render: function (data) { return statusBadge(data); }
            },
            {
                data: 'assigned_no_unit',
                render: function (data, type, row) {
                    if (data) return '<span class="badge badge-soft-green">' + data + '</span>';
                    // For pending CHANGE, show requested number
                    if (row.status === 'PENDING' && row.request_type === 'CHANGE' && row.requested_no_unit) {
                        return '<span class="badge badge-soft-yellow" title="Nomor yang diminta">→ ' + row.requested_no_unit + '</span>';
                    }
                    return '<span class="text-muted">-</span>';
                }
            },
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    if (row.status !== 'PENDING') return '<span class="text-muted">-</span>';
                    const reqType = row.request_type || 'NEW';
                    const reqNo   = row.requested_no_unit || '';
                    return '<div class="d-flex gap-1 justify-content-center">'
                        + '<button class="btn btn-success btn-sm btn-approve" data-id="' + row.id + '" data-stock="' + row.stock_number + '" data-reqtype="' + reqType + '" data-reqno="' + reqNo + '">'
                        + '<i class="fas fa-check me-1"></i>Setujui</button>'
                        + '<button class="btn btn-outline-danger btn-sm btn-reject" data-id="' + row.id + '" data-stock="' + row.stock_number + '">'
                        + '<i class="fas fa-times me-1"></i>Tolak</button>'
                        + '</div>';
                }
            }
        ],
        language: {
            processing:   'Memproses...',
            search:       'Cari:',
            lengthMenu:   'Tampilkan _MENU_ data',
            info:         'Menampilkan _START_ - _END_ dari _TOTAL_ data',
            infoEmpty:    'Tidak ada data',
            zeroRecords:  'Tidak ada data yang cocok',
            paginate: { first: '«', previous: '‹', next: '›', last: '»' },
        },
        drawCallback: function () {
            if (currentStatusFilter === '') {
                const pendingCount = table.rows().data().toArray().filter(function (r) {
                    return r.status === 'PENDING';
                }).length;
                $('#count-pending').text(pendingCount || '');
            }
        }
    });

    // ── Tab filter ───────────────────────────────────────────
    $('#statusTabs .nav-link').on('click', function (e) {
        e.preventDefault();
        $('#statusTabs .nav-link').removeClass('active');
        $(this).addClass('active');
        currentStatusFilter = $(this).data('status') || '';
        table.ajax.reload();
    });

    // ── Approve button (delegate) ────────────────────────────
    $('#tblAssetRequests').on('click', '.btn-approve', function () {
        const id          = $(this).data('id');
        const stock       = $(this).data('stock');
        const reqType     = $(this).data('reqtype') || 'NEW';
        const reqNo       = $(this).data('reqno') || '';
        $('#approveRequestId').val(id);
        $('#approveStockLabel').text(stock);
        $('#approveAssetNumber').val('');
        $('#approvedSuggestedNumber').val('');
        // Show context-aware info based on request type
        if (reqType === 'CHANGE') {
            $('#lastNoUnitInfo').html(
                '<i class="fas fa-exchange-alt text-warning me-2"></i>'
                + '<span class="small">Ganti nomor dari <strong>' + stock + '</strong> ke <strong>' + (reqNo || '?') + '</strong> (diminta warehouse)</span>'
            );
            $('#approveAssetNumber').val(reqNo); // pre-fill with requested number
            $('#btnUseSuggested').prop('disabled', true);
        } else {
            $('#lastNoUnitInfo').html('<i class="fas fa-spinner fa-spin text-muted me-2"></i><span class="small text-muted">Memuat nomor terakhir...</span>');
            $('#btnUseSuggested').prop('disabled', true);
            // Fetch last no_unit + suggestion for NEW type
            $.ajax({
                url: approveBaseUrl + 'suggest-number',
                type: 'GET',
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        const last    = res.last_no_unit || '-';
                        const suggest = res.suggested    || '';
                        $('#approvedSuggestedNumber').val(suggest);
                        $('#lastNoUnitInfo').html(
                            '<i class="fas fa-tag text-primary me-2"></i>'
                            + '<span class="small">'
                            + 'No. aset terakhir: <strong class="text-dark">' + last + '</strong>'
                            + ' &nbsp;|&nbsp; '
                            + 'Saran berikutnya: <strong class="text-success">' + suggest + '</strong>'
                            + '</span>'
                        );
                        $('#btnUseSuggested').prop('disabled', false);
                    } else {
                        $('#lastNoUnitInfo').html('<i class="fas fa-exclamation-circle text-warning me-2"></i><span class="small text-muted">Tidak dapat memuat nomor terakhir.</span>');
                    }
                },
                error: function () {
                    $('#lastNoUnitInfo').html('<i class="fas fa-exclamation-circle text-danger me-2"></i><span class="small text-muted">Gagal memuat nomor terakhir.</span>');
                }
            });
        }

        $('#modalApprove').modal('show');
    });

    // "Gunakan Saran" button
    $('#btnUseSuggested').on('click', function () {
        const suggested = $('#approvedSuggestedNumber').val();
        if (suggested) {
            $('#approveAssetNumber').val(suggested).focus();
        }
    });

    // ── Reject button (delegate) ─────────────────────────────
    $('#tblAssetRequests').on('click', '.btn-reject', function () {
        $('#rejectRequestId').val($(this).data('id'));
        $('#rejectStockLabel').text($(this).data('stock'));
        $('#rejectNotes').val('');
        $('#modalReject').modal('show');
    });

    // ── Submit: Approve ──────────────────────────────────────
    $('#btnConfirmApprove').on('click', function () {
        const id         = $('#approveRequestId').val();
        const assignedNo = $('#approveAssetNumber').val().trim();

        if (!assignedNo) {
            OptimaNotify.warning('Nomor aset tidak boleh kosong.');
            $('#approveAssetNumber').focus();
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...');

        $.ajax({
            url: approveBaseUrl + id + '/approve',
            type: 'POST',
            data: { [window.csrfTokenName]: window.csrfTokenValue, assigned_no_unit: assignedNo },
            dataType: 'json',
            success: function (res) {
                window.csrfTokenValue = res.csrf_hash || window.csrfTokenValue;
                if (res.success) {
                    $('#modalApprove').modal('hide');
                    OptimaNotify.success(res.message);
                    table.ajax.reload(null, false);
                } else {
                    OptimaNotify.error(res.message || 'Gagal menyetujui permintaan.');
                    btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i>Setujui');
                }
            },
            error: function () {
                OptimaNotify.error('Terjadi kesalahan jaringan.');
                btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i>Setujui');
            }
        });
    });

    // ── Submit: Reject ───────────────────────────────────────
    $('#btnConfirmReject').on('click', function () {
        const id    = $('#rejectRequestId').val();
        const notes = $('#rejectNotes').val().trim();
        const btn   = $(this);

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Menolak...');

        $.ajax({
            url: approveBaseUrl + id + '/reject',
            type: 'POST',
            data: { [window.csrfTokenName]: window.csrfTokenValue, reject_notes: notes },
            dataType: 'json',
            success: function (res) {
                window.csrfTokenValue = res.csrf_hash || window.csrfTokenValue;
                if (res.success) {
                    $('#modalReject').modal('hide');
                    OptimaNotify.success(res.message);
                    table.ajax.reload(null, false);
                } else {
                    OptimaNotify.error(res.message || 'Gagal menolak permintaan.');
                    btn.prop('disabled', false).html('<i class="fas fa-times me-1"></i>Tolak');
                }
            },
            error: function () {
                OptimaNotify.error('Terjadi kesalahan jaringan.');
                btn.prop('disabled', false).html('<i class="fas fa-times me-1"></i>Tolak');
            }
        });
    });

    // Reset button state on modal close
    $('#modalApprove').on('hidden.bs.modal', function () {
        $('#btnConfirmApprove').prop('disabled', false).html('<i class="fas fa-check me-1"></i>Setujui');
        $('#btnUseSuggested').prop('disabled', true);
        $('#approvedSuggestedNumber').val('');
        $('#lastNoUnitInfo').html('<i class="fas fa-info-circle text-muted"></i><span class="small text-muted">Memuat nomor terakhir...</span>');
    });
    $('#modalReject').on('hidden.bs.modal', function () {
        $('#btnConfirmReject').prop('disabled', false).html('<i class="fas fa-times me-1"></i>Tolak');
    });
});
</script>
<?= $this->endSection() ?>
