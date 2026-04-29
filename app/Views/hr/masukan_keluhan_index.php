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
    var retries = 0;
    var maxRetries = 40; // ~10 detik (40 x 250ms)

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
        var table = $('#companyFeedbackTable').DataTable({
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
                    defaultContent: '<button type="button" class="btn btn-sm btn-outline-primary btn-feedback-detail">Lihat</button>'
                }
            ],
            // Bahasa ID inline — hindari load JSON dari CDN (CORS / preflight di localhost).
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
            var row = table.row(tr).data();
            if (!row || typeof row.message_plain === 'undefined') {
                return;
            }
            document.getElementById('feedbackDetailBody').textContent = row.message_plain;
            var modal = new bootstrap.Modal(document.getElementById('feedbackDetailModal'));
            modal.show();
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
