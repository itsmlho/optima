<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid master-data-page">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0">Master Data Center</h4>
            <small class="text-muted">CRUD terpusat untuk data master unit, komponen, status, dan workflow operasional.</small>
        </div>
    </div>

    <div class="card md-card mb-3">
        <div class="card-body">
            <div class="row g-3 md-toolbar p-2">
                <div class="col-md-6">
                    <label class="form-label">Pilih Entitas Master</label>
                    <select id="entitySelector" class="form-select">
                        <?php foreach ($entities as $entity): ?>
                            <option value="<?= esc($entity['key']) ?>" <?= $entity['available'] ? '' : 'disabled' ?>>
                                <?= esc($entity['title']) ?> (<?= esc($entity['table']) ?>)<?= $entity['available'] ? '' : ' - table missing' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end justify-content-md-end">
                    <button id="btnRefreshMaster" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                    </button>
                    <button id="btnAddRow" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Tambah Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card md-card">
        <div class="card-body">
            <div id="entityInfo" class="mb-2 md-subtle"></div>
            <div class="table-responsive md-table-wrap">
                <table class="table table-sm align-middle" id="masterDataTable">
                    <thead id="masterDataHead"></thead>
                    <tbody id="masterDataBody">
                        <tr><td class="text-center text-muted">Pilih entitas untuk memuat data.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="masterDataModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="masterDataModalTitle">Tambah Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="masterDataForm" class="row g-3"></form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSaveMasterData">Simpan</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
(() => {
    const entitySelector = document.getElementById('entitySelector');
    const info = document.getElementById('entityInfo');
    const head = document.getElementById('masterDataHead');
    const body = document.getElementById('masterDataBody');
    const form = document.getElementById('masterDataForm');
    const modalElement = document.getElementById('masterDataModal');
    const modal = (window.bootstrap && window.bootstrap.Modal)
        ? new window.bootstrap.Modal(modalElement)
        : null;
    const btnAdd = document.getElementById('btnAddRow');
    const btnRefresh = document.getElementById('btnRefreshMaster');
    const btnSave = document.getElementById('btnSaveMasterData');
    const modalTitle = document.getElementById('masterDataModalTitle');

    let schema = null;
    let rows = [];
    let mode = 'create';
    let selectedId = null;
    let dataTableInstance = null;

    function initEntitySelect2() {
        if (!(window.jQuery && $.fn && $.fn.select2)) return false;
        const $entity = $('#entitySelector');
        if ($entity.data('select2')) {
            $entity.select2('destroy');
        }
        $entity.select2({
            width: '100%',
            placeholder: 'Cari entitas master...',
            allowClear: false
        });

        // Bind to Select2 events (native change sometimes not fired consistently)
        $entity.off('select2:select.masterdata change.masterdata');
        $entity.on('select2:select.masterdata', () => loadSchemaAndData());
        $entity.on('change.masterdata', () => loadSchemaAndData());
        return true;
    }

    function ensureSelect2Ready(attempt = 0) {
        if (initEntitySelect2()) return;
        if (attempt >= 40) return; // ~4s max retry
        setTimeout(() => ensureSelect2Ready(attempt + 1), 100);
    }

    function showModalSafe() {
        if (modal) {
            modal.show();
            return;
        }
        modalElement.classList.add('show');
        modalElement.style.display = 'block';
        modalElement.removeAttribute('aria-hidden');
        modalElement.setAttribute('aria-modal', 'true');
        document.body.classList.add('modal-open');
    }

    function hideModalSafe() {
        if (modal) {
            modal.hide();
            return;
        }
        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
        modalElement.setAttribute('aria-hidden', 'true');
        modalElement.removeAttribute('aria-modal');
        document.body.classList.remove('modal-open');
    }

    function notifyOk(message) {
        if (window.OptimaNotify?.success) return OptimaNotify.success(message);
        alert(message);
    }

    function notifyError(message) {
        if (window.OptimaNotify?.error) return OptimaNotify.error(message);
        alert(message);
    }

    function currentEntity() {
        return entitySelector.value;
    }

    async function fetchJson(url, options = {}) {
        const res = await fetch(url, options);
        const data = await res.json().catch(() => ({}));
        if (!res.ok || data.success === false) {
            throw new Error(data.message || `HTTP ${res.status}`);
        }
        return data;
    }

    function destroyDataTableSafe() {
        if (window.jQuery && $.fn.DataTable && $.fn.DataTable.isDataTable('#masterDataTable')) {
            $('#masterDataTable').DataTable().destroy();
        }
        dataTableInstance = null;
    }

    function initDataTableSafe() {
        if (!(window.jQuery && $.fn.DataTable)) return;
        dataTableInstance = $('#masterDataTable').DataTable({
            destroy: true,
            responsive: false,
            scrollX: true,
            scrollCollapse: true,
            autoWidth: false,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: -1, searchable: false }
            ],
            language: {
                search: 'Search:',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                infoEmpty: 'No data available',
                zeroRecords: 'No matching records found',
                paginate: { previous: 'Prev', next: 'Next' }
            }
        });
    }

    function renderTable() {
        if (!schema) return;
        destroyDataTableSafe();

        const fields = schema.fields || [];
        const pk = schema.pk;

        head.innerHTML = `<tr>
            ${fields.map(f => `<th>${f.name}</th>`).join('')}
            <th class="md-actions">Aksi</th>
        </tr>`;

        if (!rows.length) {
            body.innerHTML = `<tr><td class="text-center text-muted" colspan="${fields.length + 1}">Tidak ada data.</td></tr>`;
            return;
        }

        body.innerHTML = rows.map(row => {
            const cols = fields.map(f => `<td class="md-cell">${row[f.name] ?? ''}</td>`).join('');
            return `<tr>
                ${cols}
                <td class="md-actions">
                    <button class="btn btn-sm btn-outline-primary md-btn me-1" data-action="edit" data-id="${row[pk]}"><i class="fas fa-pen me-1"></i>Edit</button>
                    <button class="btn btn-sm btn-outline-danger md-btn" data-action="delete" data-id="${row[pk]}"><i class="fas fa-trash me-1"></i>Delete</button>
                </td>
            </tr>`;
        }).join('');

        initDataTableSafe();
    }

    function buildForm(row = null) {
        if (!schema) return;
        const pk = schema.pk;
        const fields = (schema.fields || []).filter(f =>
            !f.primary_key &&
            f.name !== pk &&
            !['created_at', 'updated_at', 'deleted_at'].includes(f.name)
        );

        form.innerHTML = fields.map(f => {
            const value = row ? (row[f.name] ?? '') : '';
            const inputType = (f.type.includes('int') || f.type.includes('decimal') || f.type.includes('float')) ? 'number' : 'text';
            const step = f.type.includes('decimal') || f.type.includes('float') ? 'step="any"' : '';
            return `<div class="col-md-6">
                <label class="form-label">${f.name}</label>
                <input type="${inputType}" ${step} class="form-control" name="${f.name}" value="${String(value).replace(/"/g, '&quot;')}" ${f.nullable ? '' : 'required'}>
            </div>`;
        }).join('');
    }

    async function loadSchemaAndData() {
        const entity = currentEntity();
        if (!entity) return;

        try {
            const schemaResp = await fetchJson(`<?= base_url('master-data/schema') ?>/${entity}`);
            schema = schemaResp.data;
            info.innerHTML = `<div class="md-entity-meta">
                <span class="md-badge">Entity: ${schema.entity}</span>
                <span class="md-badge">Table: ${schema.table}</span>
                <span class="md-badge">PK: ${schema.pk}</span>
                <span class="md-badge">Rows: ${rows.length}</span>
            </div>`;

            const listResp = await fetchJson(`<?= base_url('master-data/list') ?>/${entity}`);
            rows = listResp.data || [];
            if (listResp.meta?.effective_pk) {
                schema.pk = listResp.meta.effective_pk;
            }
            renderTable();
        } catch (e) {
            destroyDataTableSafe();
            schema = null;
            rows = [];
            head.innerHTML = '';
            body.innerHTML = `<tr><td class="text-danger text-center">Gagal memuat data: ${e.message}</td></tr>`;
            info.innerHTML = '';
        }
    }

    btnRefresh.addEventListener('click', loadSchemaAndData);
    // change handler attached via Select2 initEntitySelect2()

    btnAdd.addEventListener('click', () => {
        if (!schema) return;
        mode = 'create';
        selectedId = null;
        modalTitle.textContent = `Tambah Data - ${schema.title}`;
        buildForm();
        showModalSafe();
    });

    body.addEventListener('click', async (e) => {
        const btn = e.target.closest('button[data-action]');
        if (!btn || !schema) return;
        const action = btn.dataset.action;
        const id = btn.dataset.id;
        const pk = schema.pk;
        const row = rows.find(r => String(r[pk]) === String(id));
        if (!row) return;

        if (action === 'edit') {
            mode = 'update';
            selectedId = id;
            modalTitle.textContent = `Edit Data - ${schema.title}`;
            buildForm(row);
            showModalSafe();
            return;
        }

        if (action === 'delete') {
            if (!confirm('Yakin hapus data ini?')) return;
            try {
                await fetchJson(`<?= base_url('master-data/delete') ?>/${currentEntity()}/${id}`, { method: 'DELETE' });
                notifyOk('Data berhasil dihapus');
                await loadSchemaAndData();
            } catch (err) {
                notifyError(err.message);
            }
        }
    });

    btnSave.addEventListener('click', async () => {
        if (!schema) return;
        const payload = Object.fromEntries(new FormData(form).entries());
        const entity = currentEntity();
        const isUpdate = mode === 'update' && selectedId !== null;
        const url = isUpdate
            ? `<?= base_url('master-data/update') ?>/${entity}/${selectedId}`
            : `<?= base_url('master-data/create') ?>/${entity}`;
        const method = isUpdate ? 'PUT' : 'POST';

        try {
            await fetchJson(url, {
                method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            hideModalSafe();
            notifyOk('Data berhasil disimpan');
            await loadSchemaAndData();
        } catch (err) {
            notifyError(err.message);
        }
    });

    modalElement.addEventListener('click', (e) => {
        if (e.target.matches('[data-bs-dismiss="modal"]') || e.target.classList.contains('modal')) {
            hideModalSafe();
        }
    });

    ensureSelect2Ready();
    window.addEventListener('load', () => ensureSelect2Ready());
    loadSchemaAndData();
})();
</script>
<?= $this->endSection() ?>

