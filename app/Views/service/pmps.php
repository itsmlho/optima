<?= $this->extend('layouts/base') ?>
<?php
/**
 * PMPS Dashboard — Preventive Maintenance Planned Service
 */
helper('global_permission');
$permissions = get_global_permission('service');
$can_create  = $permissions['create'];
$can_edit    = $permissions['edit'];
?>

<?= $this->section('content') ?>

<!-- ── Statistics Cards ──────────────────────────────────────────────────── -->
<div class="row mt-3 mb-4">
    <div class="col-xl-2 col-lg-4 col-md-4 col-6 mb-3">
        <div class="stat-card bg-primary-soft">
            <div class="d-flex align-items-center">
                <div class="me-3"><i class="bi bi-calendar-check stat-icon text-primary"></i></div>
                <div>
                    <div class="stat-value" id="stat-total">—</div>
                    <div class="text-muted small">Total PM Jobs</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-4 col-6 mb-3">
        <div class="stat-card bg-danger-soft">
            <div class="d-flex align-items-center">
                <div class="me-3"><i class="bi bi-exclamation-triangle stat-icon text-danger"></i></div>
                <div>
                    <div class="stat-value" id="stat-overdue">—</div>
                    <div class="text-muted small">Overdue</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-4 col-6 mb-3">
        <div class="stat-card bg-warning-soft">
            <div class="d-flex align-items-center">
                <div class="me-3"><i class="bi bi-clock stat-icon text-warning"></i></div>
                <div>
                    <div class="stat-value" id="stat-due-week">—</div>
                    <div class="text-muted small">Jatuh Tempo 7 Hari</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-4 col-6 mb-3">
        <div class="stat-card bg-info-soft">
            <div class="d-flex align-items-center">
                <div class="me-3"><i class="bi bi-gear stat-icon text-info"></i></div>
                <div>
                    <div class="stat-value" id="stat-in-progress">—</div>
                    <div class="text-muted small">Sedang Berjalan</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-4 col-6 mb-3">
        <div class="stat-card bg-success-soft">
            <div class="d-flex align-items-center">
                <div class="me-3"><i class="bi bi-check-circle stat-icon text-success"></i></div>
                <div>
                    <div class="stat-value" id="stat-completed">—</div>
                    <div class="text-muted small">Selesai Bulan Ini</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-4 col-6 mb-3">
        <div class="stat-card bg-secondary-soft">
            <div class="d-flex align-items-center">
                <div class="me-3"><i class="bi bi-list-check stat-icon text-secondary"></i></div>
                <div>
                    <div class="stat-value" id="stat-schedules">—</div>
                    <div class="text-muted small">Jadwal Aktif</div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Global Department Filter -->
<div class="d-flex align-items-center justify-content-end gap-2 mb-3">
    <label class="form-label mb-0 fw-semibold small text-muted"><i class="fas fa-filter me-1"></i>Filter Departemen:</label>
    <select class="form-select form-select-sm" id="globalDeptFilter" style="width:200px;">
        <option value="">Semua Departemen</option>
        <?php foreach ($departemen as $d): ?>
        <option value="<?= $d['id_departemen'] ?>"><?= esc($d['nama_departemen']) ?></option>
        <?php endforeach; ?>
    </select>
</div>
<!-- ── PM Jobs Table ──────────────────────────────────────────────────────── -->
<div class="card table-card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0">
                <i class="bi bi-tools me-2 text-primary"></i>PM Jobs
            </h5>
            <p class="text-muted small mb-0">Daftar pekerjaan preventive maintenance terjadwal</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= base_url('service/pm-schedules') ?>" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-calendar-plus me-1"></i>Kelola Jadwal PM
            </a>
            <?php if ($can_create): ?>
            <button id="btn-generate-jobs" class="btn btn-success btn-sm">
                <i class="bi bi-lightning me-1"></i>Generate PM Jobs
            </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <div class="row mb-3 g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold small mb-1"><i class="fas fa-filter text-primary me-1"></i>Status</label>
                <select id="filter-status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="SCHEDULED">Terjadwal</option>
                    <option value="OVERDUE">Overdue</option>
                    <option value="IN_PROGRESS">Sedang Berjalan</option>
                    <option value="COMPLETED">Selesai</option>
                    <option value="SKIPPED">Dilewati</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small mb-1"><i class="fas fa-calendar me-1 text-primary"></i>Bulan</label>
                <input type="month" id="filter-month" class="form-control form-control-sm" value="<?= date('Y-m') ?>">
            </div>
            <div class="col-md-2">
                <button id="btn-clear-filters" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="bi bi-x-circle me-1"></i>Reset Filter
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="pm-jobs-table" class="table table-hover table-sm align-middle mb-0 w-100">
                <thead class="table-light">
                    <tr class="small text-uppercase">
                        <th>PM Number</th>
                        <th>Unit</th>
                        <th>Lokasi / Customer</th>
                        <th>Jadwal</th>
                        <th>Due Date</th>
                        <th>HM Due</th>
                        <th>Status</th>
                        <th>Work Order</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- ── Complete PM Job Modal ──────────────────────────────────────────────── -->
<div class="modal fade" id="completeJobModal" tabindex="-1" aria-labelledby="completeJobModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="completeJobModalLabel">
                    <i class="bi bi-check-circle me-2 text-success"></i>Selesaikan PM Job
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <input type="hidden" id="complete-job-id">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Pengerjaan <span class="text-danger">*</span></label>
                    <input type="date" id="complete-actual-date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Hour Meter Aktual (HM)</label>
                    <input type="number" step="0.1" id="complete-actual-hm" class="form-control form-control-sm" placeholder="HM saat PM dikerjakan">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan</label>
                    <textarea id="complete-notes" class="form-control form-control-sm" rows="3" placeholder="Catatan hasil PM..."></textarea>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Bidang dengan <span class="text-danger">*</span> wajib diisi</small>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success btn-sm" id="btn-confirm-complete">
                        <i class="bi bi-check-lg me-1"></i>Selesaikan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
// CSRF — use window globals from base layout
function getCsrf() {
    return {
        name: window.csrfTokenName,
        hash: (typeof window.getCsrfToken === 'function') ? window.getCsrfToken() : window.csrfToken
    };
}

// ── Stats ──────────────────────────────────────────────────────────────────
function loadStats() {
    $.getJSON(BASE_URL + 'service/pmps/stats', function(res) {
        if (!res.success) return;
        const d = res.data;
        $('#stat-total').text(d.total ?? 0);
        $('#stat-overdue').text(d.overdue ?? 0);
        $('#stat-due-week').text(d.due_this_week ?? 0);
        $('#stat-in-progress').text(d.in_progress ?? 0);
        $('#stat-completed').text(d.completed_this_month ?? 0);
        $('#stat-schedules').text(d.active_schedules ?? 0);
    });
}

// ── Status badge ───────────────────────────────────────────────────────────
function statusBadge(status) {
    const map = {
        'SCHEDULED':   '<span class="badge badge-soft-blue">Terjadwal</span>',
        'OVERDUE':     '<span class="badge badge-soft-red">Overdue</span>',
        'IN_PROGRESS': '<span class="badge badge-soft-cyan">Berjalan</span>',
        'COMPLETED':   '<span class="badge badge-soft-green">Selesai</span>',
        'SKIPPED':     '<span class="badge badge-soft-gray">Dilewati</span>',
    };
    return map[status] || `<span class="badge badge-soft-gray">${status}</span>`;
}

// ── DataTable ──────────────────────────────────────────────────────────────
let pmTable;
function initTable() {
    pmTable = $('#pm-jobs-table').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        pageLength: 25,
        ajax: {
            url: BASE_URL + 'service/pmps/getPmJobs',
            type: 'GET',
            data: function(d) {
                d.status = $('#filter-status').val();
                d.month  = $('#filter-month').val();
                d.departemen_id = $('#globalDeptFilter').val();
                return d;
            }
        },
        columns: [
            {
                data: 'pm_number', width: '130px',
                render: (d, _, row) => `<a href="${BASE_URL}service/pm-job/${row.id}" class="fw-semibold text-primary text-decoration-none">${d}</a>`
            },
            {
                data: 'no_unit',
                render: (d, _, row) => `<strong>${d ?? '-'}</strong><br><small class="text-muted">${row.merk ?? ''} ${row.model ?? ''}</small>`
            },
            {
                data: 'customer_name',
                render: (d, _, row) => `${d ?? '<span class="text-muted fst-italic">Belum ada kontrak</span>'}<br><small class="text-muted">${row.location_name ?? ''}</small>`
            },
            {
                data: 'schedule_name',
                render: (d, _, row) => {
                    const cls = row.trigger_type === 'HM' ? 'badge-soft-orange' : row.trigger_type === 'BOTH' ? 'badge-soft-purple' : 'badge-soft-blue';
                    return `${d ?? '-'}<br><span class="badge ${cls} small">${row.trigger_type ?? ''}</span>`;
                }
            },
            {
                data: 'due_date',
                render: d => {
                    if (!d) return '<span class="text-muted">-</span>';
                    const due = new Date(d), today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const diff = Math.floor((due - today) / 86400000);
                    const cls  = diff < 0 ? 'text-danger fw-bold' : diff <= 7 ? 'text-warning fw-semibold' : '';
                    return `<span class="${cls}">${d}</span>`;
                }
            },
            { data: 'due_hm', render: d => d ? `<span class="badge badge-soft-blue">${d} HM</span>` : '<span class="text-muted">-</span>' },
            { data: 'display_status', render: d => statusBadge(d) },
            {
                data: 'work_order_number',
                render: (d, _, row) => d
                    ? `<a href="${BASE_URL}service/work-orders/view/${row.work_order_id}" class="badge badge-soft-cyan text-decoration-none" target="_blank">${d}</a>`
                    : '<span class="text-muted small">—</span>'
            },
            {
                data: 'id', orderable: false, className: 'text-center',
                render: (d, _, row) => {
                    const status = row.display_status || row.status;
                    let btns = `<a href="${BASE_URL}service/pm-job/${d}" class="btn btn-sm btn-outline-primary" title="Detail"><i class="bi bi-eye"></i></a> `;
                    if (!row.work_order_id && ['SCHEDULED','OVERDUE'].includes(status)) {
                        btns += `<button class="btn btn-sm btn-outline-success btn-create-wo" data-id="${d}" data-pm="${row.pm_number ?? ''}" title="Buat WO"><i class="bi bi-plus-circle"></i></button> `;
                    }
                    if (['SCHEDULED','OVERDUE','IN_PROGRESS'].includes(status)) {
                        btns += `<button class="btn btn-sm btn-outline-warning btn-complete" data-id="${d}" data-hm="${row.current_hm ?? ''}" data-pm="${row.pm_number ?? ''}" title="Selesaikan"><i class="bi bi-check-lg"></i></button>`;
                    }
                    return btns;
                }
            },
        ],
        order: [[4, 'asc']],
        language: {
            processing:   'Memuat data…',
            search:       'Cari:',
            lengthMenu:   'Tampilkan _MENU_ data',
            info:         'Menampilkan _START_–_END_ dari _TOTAL_ data',
            infoEmpty:    'Tidak ada data',
            infoFiltered: '(dari _MAX_ total)',
            zeroRecords:  'Tidak ditemukan data yang sesuai',
            emptyTable:   'Belum ada PM Job',
            paginate:     { first: 'Pertama', previous: 'Sebelumnya', next: 'Selanjutnya', last: 'Terakhir' }
        },
    });
}

// ── Events ─────────────────────────────────────────────────────────────────
$(document).ready(function() {
    loadStats();
    initTable();

    // Filter
    $('#filter-status, #filter-month, #globalDeptFilter').on('change', () => pmTable.ajax.reload());
    $('#btn-clear-filters').on('click', function() {
        $('#filter-status').val('');
        $('#filter-month').val('<?= date('Y-m') ?>');
        $('#globalDeptFilter').val('');
        pmTable.ajax.reload();
    });

    // Generate PM Jobs
    $('#btn-generate-jobs').on('click', function() {
        OptimaConfirm.generic({
            title: 'Generate PM Jobs',
            html: '<p class="mb-1">Generate PM Job dari semua jadwal yang sudah jatuh tempo?</p><small class="text-muted">Jadwal yang sudah memiliki PM Job aktif akan dilewati.</small>',
            icon: 'question',
            confirmText: 'Generate',
            cancelText: 'Batal',
            confirmButtonColor: 'success',
            onConfirm: function() {
                const $btn = $('#btn-generate-jobs').prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Proses...');
                const csrf = getCsrf();
                $.post(BASE_URL + 'service/pmps/generate', { [csrf.name]: csrf.hash }, function(res) {
                    if (res.success) {
                        OptimaNotify.success(res.message);
                        pmTable.ajax.reload();
                        loadStats();
                    } else {
                        OptimaNotify.error(res.message || 'Gagal generate PM Jobs');
                    }
                }).fail(() => OptimaNotify.error('Gagal menghubungi server'))
                  .always(() => $btn.prop('disabled', false).html('<i class="bi bi-lightning me-1"></i>Generate PM Jobs'));
            }
        });
    });

    // Buat Work Order
    $(document).on('click', '.btn-create-wo', function() {
        const id  = $(this).data('id');
        const pm  = $(this).data('pm') || '#' + id;
        const $btn = $(this);
        OptimaConfirm.generic({
            title: 'Buat Work Order PMPS',
            html: `<p class="mb-0">Buat Work Order untuk <strong>${pm}</strong>?</p>`,
            icon: 'question',
            confirmText: 'Buat WO',
            cancelText: 'Batal',
            confirmButtonColor: 'success',
            onConfirm: function() {
                $btn.prop('disabled', true);
                const csrf = getCsrf();
                $.post(BASE_URL + 'service/pmps/createWorkOrder/' + id, { [csrf.name]: csrf.hash }, function(res) {
                    if (res.success) {
                        OptimaNotify.success(res.message);
                        pmTable.ajax.reload();
                        loadStats();
                    } else {
                        OptimaNotify.error(res.message || 'Gagal membuat Work Order');
                    }
                }).fail(() => OptimaNotify.error('Gagal menghubungi server'))
                  .always(() => $btn.prop('disabled', false));
            }
        });
    });

    // Buka modal selesai
    $(document).on('click', '.btn-complete', function() {
        $('#complete-job-id').val($(this).data('id'));
        $('#complete-actual-hm').val($(this).data('hm') || '');
        $('#complete-actual-date').val('<?= date('Y-m-d') ?>');
        $('#complete-notes').val('');
        new bootstrap.Modal(document.getElementById('completeJobModal')).show();
    });

    // Konfirmasi selesai
    $('#btn-confirm-complete').on('click', function() {
        const jobId = $('#complete-job-id').val();
        const date  = $('#complete-actual-date').val();
        if (!date) { OptimaNotify.warning('Tanggal pengerjaan wajib diisi', 'Validasi'); return; }
        const $btn = $(this).prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Menyimpan...');
        const csrf = getCsrf();
        $.post(BASE_URL + 'service/pmps/complete/' + jobId, {
            [csrf.name]: csrf.hash,
            actual_date: date,
            actual_hm:   $('#complete-actual-hm').val(),
            notes:       $('#complete-notes').val(),
        }, function(res) {
            if (res.success) {
                OptimaNotify.success(res.message);
                bootstrap.Modal.getInstance(document.getElementById('completeJobModal')).hide();
                pmTable.ajax.reload();
                loadStats();
            } else {
                OptimaNotify.error(res.message || 'Gagal menyimpan');
            }
        }).fail(() => OptimaNotify.error('Gagal menghubungi server'))
          .always(() => $btn.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i>Selesaikan'));
    });
});
</script>
<?= $this->endSection() ?>
