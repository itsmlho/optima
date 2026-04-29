<?= $this->extend('layouts/base') ?>

<?= $this->section('title') ?><?= esc(lang('Hr.feedback_page_title')) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $stats = $stats ?? ['total' => 0, 'this_month' => 0, 'masukan' => 0, 'keluh_kesah' => 0];
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>"><?= lang('App.dashboard') ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= esc(lang('Hr.feedback_page_title')) ?></li>
    </ol>
</nav>

<!-- Stat cards (sama pola dengan modul daftar OPTIMA) -->
<div class="row mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-primary-soft">
            <div class="d-flex align-items-center">
                <div class="me-3"><i class="bi bi-inboxes stat-icon text-primary"></i></div>
                <div>
                    <div class="stat-value"><?= number_format((int) ($stats['total'] ?? 0)) ?></div>
                    <div class="text-muted"><?= esc(lang('Hr.feedback_stat_total')) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-info-soft">
            <div class="d-flex align-items-center">
                <div class="me-3"><i class="bi bi-calendar-month stat-icon text-info"></i></div>
                <div>
                    <div class="stat-value"><?= number_format((int) ($stats['this_month'] ?? 0)) ?></div>
                    <div class="text-muted"><?= esc(lang('Hr.feedback_stat_this_month')) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-success-soft">
            <div class="d-flex align-items-center">
                <div class="me-3"><i class="bi bi-chat-quote stat-icon text-success"></i></div>
                <div>
                    <div class="stat-value"><?= number_format((int) ($stats['masukan'] ?? 0)) ?></div>
                    <div class="text-muted"><?= esc(lang('Hr.feedback_stat_masukan')) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-warning-soft">
            <div class="d-flex align-items-center">
                <div class="me-3"><i class="bi bi-exclamation-triangle stat-icon text-warning"></i></div>
                <div>
                    <div class="stat-value"><?= number_format((int) ($stats['keluh_kesah'] ?? 0)) ?></div>
                    <div class="text-muted"><?= esc(lang('Hr.feedback_stat_keluh')) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main table -->
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0">
                <i class="bi bi-megaphone me-2 text-primary"></i><?= esc(lang('Hr.feedback_page_title')) ?>
            </h5>
            <p class="text-muted small mb-0"><?= esc(lang('Hr.feedback_page_desc')) ?></p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= esc(base_url('masukan-keluhan')) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-box-arrow-up-right me-1"></i><?= esc(lang('Hr.feedback_open_public')) ?>
            </a>
            <a href="<?= esc(base_url('masukan-keluhan/print')) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm">
                <i class="bi bi-printer me-1"></i>Print Poster
            </a>
            <button type="button" class="btn btn-outline-primary btn-sm" id="btnRefreshCompanyFeedback" title="<?= esc(lang('Hr.feedback_refresh')) ?>">
                <i class="fas fa-sync-alt me-1"></i><?= esc(lang('Hr.feedback_refresh')) ?>
            </button>
        </div>
    </div>
    <div class="card-body pt-0">
        <div class="table-responsive">
            <table id="companyFeedbackTable" class="table table-striped table-hover w-100 align-middle">
                <thead class="table-light">
                    <tr>
                        <th><?= esc(lang('Hr.feedback_table_type')) ?></th>
                        <th><?= esc(lang('Hr.feedback_table_snippet')) ?></th>
                        <th><?= esc(lang('Hr.feedback_table_contact')) ?></th>
                        <th><?= esc(lang('Hr.feedback_table_time')) ?></th>
                        <th style="width: 88px;"><?= esc(lang('Hr.feedback_table_action')) ?></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="feedbackDetailModal" tabindex="-1" aria-labelledby="feedbackDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="feedbackDetailModalLabel">
                    <i class="bi bi-card-text me-2 text-primary"></i><?= esc(lang('Hr.feedback_modal_title')) ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= esc(lang('Common.close')) ?>"></button>
            </div>
            <div class="modal-body">
                <pre id="feedbackDetailBody" class="mb-0 rounded border bg-light p-3 small" style="white-space: pre-wrap; font-family: inherit;"></pre>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
(function () {
    var retries = 0;
    var maxRetries = 40;
    var feedbackTable = null;
    var btnLabelDetail = <?= json_encode('<i class="bi bi-eye me-1"></i>' . lang('Hr.feedback_view_detail')) ?>;

    function notifyInitError() {
        var msg = 'Komponen tabel belum siap. Silakan refresh halaman.';
        if (window.OptimaNotify && typeof OptimaNotify.error === 'function') {
            OptimaNotify.error(msg);
        } else if (typeof window.alertSwal === 'function') {
            window.alertSwal('error', msg, '');
        } else {
            console.error(msg);
        }
    }

    function initFeedbackTable() {
        feedbackTable = $('#companyFeedbackTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: <?= json_encode(base_url('hr/masukan-keluhan/data')) ?>,
                type: 'POST',
                data: function (d) {
                    d[window.csrfTokenName] = window.csrfTokenValue;
                }
            },
            order: [[3, 'desc']],
            pageLength: 25,
            columns: [
                { data: 'type_label', width: '12%' },
                {
                    data: 'message_snip',
                    width: '38%',
                    render: function (d) {
                        return $('<div/>').text(d).html();
                    }
                },
                {
                    data: 'contact',
                    orderable: false,
                    render: function (d) {
                        return d;
                    }
                },
                {
                    data: 'created_at',
                    width: '16%',
                    render: function (d) {
                        if (!d) return '';
                        return $('<span/>').text(d).html();
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    defaultContent: '<button type="button" class="btn btn-sm btn-primary btn-feedback-detail">' + btnLabelDetail + '</button>'
                }
            ],
            language: {
                processing: 'Memproses...',
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ entri',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ entri',
                infoEmpty: 'Menampilkan 0 sampai 0 dari 0 entri',
                infoFiltered: '(disaring dari _MAX_ entri keseluruhan)',
                infoPostFix: '',
                loadingRecords: 'Memuat...',
                zeroRecords: 'Tidak ada data yang cocok',
                emptyTable: 'Belum ada masukan atau keluhan',
                paginate: {
                    first: 'Pertama',
                    previous: 'Sebelumnya',
                    next: 'Berikutnya',
                    last: 'Terakhir'
                },
                aria: {
                    sortAscending: ': aktifkan untuk mengurutkan kolom naik',
                    sortDescending: ': aktifkan untuk mengurutkan kolom turun'
                }
            }
        });

        $('#companyFeedbackTable tbody').on('click', '.btn-feedback-detail', function () {
            var tr = $(this).closest('tr');
            var row = feedbackTable.row(tr).data();
            if (!row || typeof row.message_plain === 'undefined') {
                return;
            }
            document.getElementById('feedbackDetailBody').textContent = row.message_plain;
            var modal = new bootstrap.Modal(document.getElementById('feedbackDetailModal'));
            modal.show();
        });

        $('#btnRefreshCompanyFeedback').on('click', function () {
            var btn = $(this).prop('disabled', true);
            feedbackTable.ajax.reload(function () {
                btn.prop('disabled', false);
            }, false);
        });
    }

    function waitForDataTableAndInit() {
        if (window.jQuery && $.fn && typeof $.fn.DataTable === 'function') {
            initFeedbackTable();
            return;
        }

        retries += 1;
        if (retries >= maxRetries) {
            notifyInitError();
            return;
        }
        window.setTimeout(waitForDataTableAndInit, 250);
    }

    $(waitForDataTableAndInit);
})();
</script>
<?= $this->endSection() ?>
