<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<style>
.sp-master-header { border-left: 4px solid #0d6efd; padding-left: .75rem; }
.sp-kode-cell { font-family: monospace; font-size: .85rem; font-weight: 600; color: #0d6efd; }
.sp-desc-cell { font-size: .9rem; }
.sp-date-cell { font-size: .8rem; color: #6c757d; white-space: nowrap; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid">

    <!-- Page Header -->
    <div class="d-flex align-items-start justify-content-between mb-3">
        <div class="sp-master-header">
            <h4 class="fw-bold mb-0">
                <i class="bi bi-gear-wide-connected me-2 text-primary"></i>
                Master Data Sparepart
            </h4>
            <p class="text-muted mb-0 small">Kelola daftar kode dan nama sparepart yang digunakan di seluruh sistem</p>
        </div>
        <button class="btn btn-primary" id="btnAddSparepart">
            <i class="bi bi-plus-lg me-1"></i> Tambah Sparepart
        </button>
    </div>

    <!-- Stats -->
    <div class="row mb-3">
        <div class="col-auto">
            <div class="stat-card bg-primary-soft d-inline-flex align-items-center gap-2 px-3 py-2">
                <i class="bi bi-gear stat-icon text-primary" style="font-size:1.5rem;"></i>
                <div>
                    <div class="fw-bold fs-5" id="stat-total"><?= $total ?? 0 ?></div>
                    <div class="text-muted small">Total Sparepart</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-table me-1"></i> Daftar Sparepart</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="sparepartMasterTable" class="table table-hover table-bordered mb-0 align-middle" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th width="50" class="text-center">No</th>
                            <th width="160">Kode</th>
                            <th>Nama Sparepart</th>
                            <th width="140">Dibuat</th>
                            <th width="140">Diperbarui</th>
                            <th width="100" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- â”€â”€â”€ Add Modal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
<div class="modal fade" id="modalAddSparepart" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Sparepart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kode Sparepart <span class="text-danger">*</span></label>
                    <input type="text" id="addKode" class="form-control text-uppercase" placeholder="Contoh: SP-0001" maxlength="50">
                    <div class="invalid-feedback" id="addKodeError"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama / Deskripsi Sparepart <span class="text-danger">*</span></label>
                    <input type="text" id="addDesc" class="form-control" placeholder="Contoh: Filter Oli Mesin" maxlength="500">
                    <div class="invalid-feedback" id="addDescError"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSaveAdd">
                    <i class="bi bi-save me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- â”€â”€â”€ Edit Modal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
<div class="modal fade" id="modalEditSparepart" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Sparepart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kode Sparepart <span class="text-danger">*</span></label>
                    <input type="text" id="editKode" class="form-control text-uppercase" maxlength="50">
                    <div class="invalid-feedback" id="editKodeError"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama / Deskripsi Sparepart <span class="text-danger">*</span></label>
                    <input type="text" id="editDesc" class="form-control" maxlength="500">
                    <div class="invalid-feedback" id="editDescError"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="btnSaveEdit">
                    <i class="bi bi-save me-1"></i> Perbarui
                </button>
            </div>
        </div>
    </div>
</div>

<!-- â”€â”€â”€ Delete Confirm Modal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
<div class="modal fade" id="modalDeleteSparepart" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-danger"><i class="bi bi-trash3 me-2"></i>Hapus Sparepart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-1">
                <p class="mb-1">Hapus sparepart:</p>
                <p class="fw-bold mb-0" id="deleteSpName">â€”</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="btnConfirmDelete">
                    <i class="bi bi-trash3 me-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
(function () {
    'use strict';

    const BASE_URL  = '<?= base_url() ?>';
    const CSRF_NAME = '<?= csrf_token() ?>';
    let   csrfHash  = '<?= csrf_hash() ?>';

    // â”€â”€ Helper â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function csrfData() {
        return { [CSRF_NAME]: csrfHash };
    }

    function refreshCsrf(response) {
        if (response && response.csrf_hash) csrfHash = response.csrf_hash;
    }

    function resetInputErrors(...ids) {
        ids.forEach(function(id) {
            var el = document.getElementById(id);
            if (el) el.classList.remove('is-invalid');
            var fb = document.getElementById(id + 'Error');
            if (fb) fb.textContent = '';
        });
    }

    function showInputError(id, msg) {
        var el = document.getElementById(id);
        var fb = document.getElementById(id + 'Error');
        if (el) el.classList.add('is-invalid');
        if (fb) fb.textContent = msg;
    }

    function escHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, function(c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c];
        });
    }

    function fmtDate(str) {
        if (!str) return '-';
        var d = new Date(str);
        if (isNaN(d)) return str;
        return d.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
    }

    // â”€â”€ DataTable â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    var table = $('#sparepartMasterTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url:  BASE_URL + 'warehouse/inventory/invent_sparepart',
            type: 'POST',
            data: function(d) { return Object.assign(d, csrfData()); },
            dataSrc: function(json) {
                refreshCsrf(json);
                var el = document.getElementById('stat-total');
                if (el) el.textContent = json.recordsTotal ?? 0;
                return json.data || [];
            }
        },
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center text-muted small',
                render: function(d, t, r, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'kode',
                className: 'sp-kode-cell',
                render: function(d) {
                    return '<span class="badge badge-soft-blue">' + escHtml(d) + '</span>';
                }
            },
            { data: 'desc_sparepart', className: 'sp-desc-cell' },
            {
                data: 'created_at',
                className: 'sp-date-cell',
                render: function(d) { return fmtDate(d); }
            },
            {
                data: 'updated_at',
                className: 'sp-date-cell',
                render: function(d) { return fmtDate(d); }
            },
            {
                data: 'id_sparepart',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(id, t, row) {
                    return '<div class="d-flex gap-1 justify-content-center">' +
                        '<button class="btn btn-sm btn-outline-warning btn-edit" data-id="' + id + '" title="Edit"><i class="bi bi-pencil"></i></button>' +
                        '<button class="btn btn-sm btn-outline-danger btn-delete" data-id="' + id + '" data-name="' + escHtml(row.kode) + ' \u2013 ' + escHtml(row.desc_sparepart) + '" title="Hapus"><i class="bi bi-trash3"></i></button>' +
                        '</div>';
                }
            }
        ],
        order: [[1, 'asc']],
        pageLength: 25,
        language: { url: BASE_URL + 'assets/plugins/datatables/i18n/id.json' }
    });

    // â”€â”€ Open Add Modal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    document.getElementById('btnAddSparepart').addEventListener('click', function() {
        resetInputErrors('addKode', 'addDesc');
        document.getElementById('addKode').value = '';
        document.getElementById('addDesc').value = '';
        new bootstrap.Modal(document.getElementById('modalAddSparepart')).show();
    });

    // â”€â”€ Save Add â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    document.getElementById('btnSaveAdd').addEventListener('click', function() {
        var kode = document.getElementById('addKode').value.trim().toUpperCase();
        var desc = document.getElementById('addDesc').value.trim();

        resetInputErrors('addKode', 'addDesc');
        var ok = true;
        if (!kode) { showInputError('addKode', 'Kode wajib diisi'); ok = false; }
        if (!desc) { showInputError('addDesc', 'Nama sparepart wajib diisi'); ok = false; }
        if (!ok) return;

        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan\u2026';

        $.post(BASE_URL + 'warehouse/inventory/sparepart-master/create',
            Object.assign({}, csrfData(), { kode: kode, desc_sparepart: desc })
        )
        .done(function(res) {
            refreshCsrf(res);
            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalAddSparepart'))?.hide();
                table.ajax.reload(null, false);
                showToast('success', res.message);
            } else {
                showToast('error', res.message || 'Gagal menyimpan');
            }
        })
        .fail(function() { showToast('error', 'Terjadi kesalahan jaringan'); })
        .always(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-save me-1"></i> Simpan';
        });
    });

    // â”€â”€ Open Edit Modal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    $('#sparepartMasterTable').on('click', '.btn-edit', function() {
        var id = this.dataset.id;

        $.ajax({
            url: BASE_URL + 'warehouse/inventory/sparepart-master/' + id,
            type: 'GET',
            dataType: 'json'
        })
        .done(function(res) {
            if (res.success) {
                resetInputErrors('editKode', 'editDesc');
                document.getElementById('editId').value   = res.data.id_sparepart;
                document.getElementById('editKode').value = res.data.kode;
                document.getElementById('editDesc').value = res.data.desc_sparepart;
                new bootstrap.Modal(document.getElementById('modalEditSparepart')).show();
            } else {
                showToast('error', res.message || 'Gagal memuat data');
            }
        })
        .fail(function() { showToast('error', 'Terjadi kesalahan jaringan'); });
    });

    // â”€â”€ Save Edit â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    document.getElementById('btnSaveEdit').addEventListener('click', function() {
        var id   = document.getElementById('editId').value;
        var kode = document.getElementById('editKode').value.trim().toUpperCase();
        var desc = document.getElementById('editDesc').value.trim();

        resetInputErrors('editKode', 'editDesc');
        var ok = true;
        if (!kode) { showInputError('editKode', 'Kode wajib diisi'); ok = false; }
        if (!desc) { showInputError('editDesc', 'Nama sparepart wajib diisi'); ok = false; }
        if (!ok) return;

        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan\u2026';

        $.post(BASE_URL + 'warehouse/inventory/sparepart-master/update/' + id,
            Object.assign({}, csrfData(), { kode: kode, desc_sparepart: desc })
        )
        .done(function(res) {
            refreshCsrf(res);
            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalEditSparepart'))?.hide();
                table.ajax.reload(null, false);
                showToast('success', res.message);
            } else {
                showToast('error', res.message || 'Gagal memperbarui');
            }
        })
        .fail(function() { showToast('error', 'Terjadi kesalahan jaringan'); })
        .always(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-save me-1"></i> Perbarui';
        });
    });

    // â”€â”€ Open Delete Confirm â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    var pendingDeleteId = null;
    $('#sparepartMasterTable').on('click', '.btn-delete', function() {
        pendingDeleteId = this.dataset.id;
        document.getElementById('deleteSpName').textContent = this.dataset.name;
        new bootstrap.Modal(document.getElementById('modalDeleteSparepart')).show();
    });

    // â”€â”€ Confirm Delete â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    document.getElementById('btnConfirmDelete').addEventListener('click', function() {
        if (!pendingDeleteId) return;

        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

        $.post(BASE_URL + 'warehouse/inventory/sparepart-master/delete/' + pendingDeleteId, csrfData())
        .done(function(res) {
            refreshCsrf(res);
            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalDeleteSparepart'))?.hide();
                table.ajax.reload(null, false);
                showToast('success', res.message);
            } else {
                showToast('error', res.message);
            }
        })
        .fail(function() { showToast('error', 'Terjadi kesalahan jaringan'); })
        .always(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-trash3 me-1"></i> Hapus';
            pendingDeleteId = null;
        });
    });

})();
</script>
<?= $this->endSection() ?>

