<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <h4 class="mb-0">Masukan &amp; Keluh Kesah</h4>
            <small class="text-muted">Data dikirim melalui formulir publik perusahaan (identitas default anonim; kontak hanya jika diisi pengirim).</small>
        </div>
        <div>
            <a href="<?= esc(base_url('masukan-keluhan')) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-box-arrow-up-right me-1"></i>Buka halaman publik
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="companyFeedbackTable" class="table table-hover table-bordered align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>Jenis</th>
                            <th>Cuplikan pesan</th>
                            <th>Kontak</th>
                            <th>Waktu</th>
                            <th style="width: 88px;">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="feedbackDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Isi pesan lengkap</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <pre id="feedbackDetailBody" class="mb-0" style="white-space: pre-wrap; font-family: inherit;"></pre>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
(function () {
    const table = $('#companyFeedbackTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: <?= json_encode(base_url('hr/masukan-keluhan/data')) ?>,
            type: 'POST'
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
                defaultContent: '<button type="button" class="btn btn-sm btn-outline-primary btn-feedback-detail">Lihat</button>'
            }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });

    $('#companyFeedbackTable tbody').on('click', '.btn-feedback-detail', function () {
        const tr = $(this).closest('tr');
        const row = table.row(tr).data();
        if (!row || typeof row.message_plain === 'undefined') {
            return;
        }
        document.getElementById('feedbackDetailBody').textContent = row.message_plain;
        const modal = new bootstrap.Modal(document.getElementById('feedbackDetailModal'));
        modal.show();
    });
})();
</script>
<?= $this->endSection() ?>
