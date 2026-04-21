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

<?= $this->section('css') ?><?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
(() => {
    const BASE_URL = '<?= base_url() ?>';
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
    const fkOptionsCache = {};

    // ── Utility ──────────────────────────────────────────────────────────────

    function currentEntity() { return entitySelector.value; }

    function escHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, c =>
            ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c])
        );
    }

    /** Convert a DB field name to a human-readable label. FK fields use their display_label. */
    function fieldLabel(fieldName, fkDef) {
        if (fkDef) return fkDef.display_label || fieldName;
        return fieldName.replace(/_id$/, '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    }

    async function fetchJson(url, options = {}) {
        const res = await fetch(url, options);
        const data = await res.json().catch(() => ({}));
        if (!res.ok || data.success === false) {
            throw new Error(data.message || `HTTP ${res.status}`);
        }
        return data;
    }

    // ── FK options cache ──────────────────────────────────────────────────────

    async function loadFkOptions(entityKey) {
        if (fkOptionsCache[entityKey]) return fkOptionsCache[entityKey];
        try {
            const resp = await fetchJson(`${BASE_URL}master-data/options/${entityKey}`);
            const opts = (resp.data || []).sort((a, b) => String(a.name).localeCompare(String(b.name)));
            fkOptionsCache[entityKey] = opts;
            return opts;
        } catch (e) {
            return [];
        }
    }

    // ── Select2 for entity selector ───────────────────────────────────────────

    function initEntitySelect2() {
        if (!(window.jQuery && $.fn && $.fn.select2)) return false;
        const $entity = $('#entitySelector');
        if ($entity.data('select2')) $entity.select2('destroy');
        $entity.select2({
            width: '100%',
            placeholder: 'Cari entitas master...',
            allowClear: false,
        });
        $entity.off('select2:select.masterdata change.masterdata');
        $entity.on('select2:select.masterdata', () => loadSchemaAndData());
        $entity.on('change.masterdata', () => loadSchemaAndData());
        return true;
    }

    function ensureSelect2Ready(attempt = 0) {
        if (initEntitySelect2()) return;
        if (attempt >= 40) return;
        setTimeout(() => ensureSelect2Ready(attempt + 1), 100);
    }

    // ── Modal helpers ─────────────────────────────────────────────────────────

    function showModalSafe() {
        if (modal) { modal.show(); return; }
        modalElement.classList.add('show');
        modalElement.style.display = 'block';
        modalElement.removeAttribute('aria-hidden');
        modalElement.setAttribute('aria-modal', 'true');
        document.body.classList.add('modal-open');
    }

    function hideModalSafe() {
        if (modal) { modal.hide(); return; }
        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
        modalElement.setAttribute('aria-hidden', 'true');
        modalElement.removeAttribute('aria-modal');
        document.body.classList.remove('modal-open');
    }

    // ── Notify helpers ────────────────────────────────────────────────────────

    function notifyOk(message) {
        if (window.OptimaNotify?.success) return OptimaNotify.success(message);
        alert(message);
    }

    function notifyError(message) {
        if (window.OptimaNotify?.error) return OptimaNotify.error(message);
        alert(message);
    }

    // ── DataTable ─────────────────────────────────────────────────────────────

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
            columnDefs: [{ orderable: false, targets: -1, searchable: false }],
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

    // ── Render table ──────────────────────────────────────────────────────────

    function renderTable() {
        if (!schema) return;
        destroyDataTableSafe();

        // Exclude virtual __label columns from column structure
        const displayFields = (schema.fields || []).filter(f => !f.name.endsWith('__label'));
        const fk = schema.fk || {};
        const pk = schema.pk;

        head.innerHTML = `<tr>
            ${displayFields.map(f => `<th>${escHtml(fieldLabel(f.name, fk[f.name]))}</th>`).join('')}
            <th class="md-actions">Aksi</th>
        </tr>`;

        if (!rows.length) {
            body.innerHTML = `<tr><td class="text-center text-muted" colspan="${displayFields.length + 1}">Tidak ada data.</td></tr>`;
            return;
        }

        body.innerHTML = rows.map(row => {
            const cols = displayFields.map(f => {
                let val = row[f.name] ?? '';
                // For FK columns, show the resolved label value instead of the raw ID
                const labelKey = f.name + '__label';
                if (fk[f.name] && row[labelKey] !== undefined) {
                    val = row[labelKey] !== null ? row[labelKey] : '-';
                }
                return `<td class="md-cell">${escHtml(String(val))}</td>`;
            }).join('');
            return `<tr>
                ${cols}
                <td class="md-actions">
                    <button class="btn btn-sm btn-outline-primary md-btn me-1" data-action="edit" data-id="${escHtml(String(row[pk]))}"><i class="fas fa-pen me-1"></i>Edit</button>
                    <button class="btn btn-sm btn-outline-danger md-btn" data-action="delete" data-id="${escHtml(String(row[pk]))}"><i class="fas fa-trash me-1"></i>Delete</button>
                </td>
            </tr>`;
        }).join('');

        initDataTableSafe();
    }

    // ── Build form ────────────────────────────────────────────────────────────

    async function buildForm(row = null) {
        if (!schema) return;
        const pk = schema.pk;
        const fk = schema.fk || {};
        const fields = (schema.fields || []).filter(f =>
            !f.primary_key &&
            f.name !== pk &&
            !['created_at', 'updated_at', 'deleted_at'].includes(f.name) &&
            !f.name.endsWith('__label')
        );

        form.innerHTML = fields.map(f => {
            const value = row ? (row[f.name] ?? '') : '';
            const label = escHtml(fieldLabel(f.name, fk[f.name]));
            const required = f.nullable ? '' : 'required';
            const requiredMark = f.nullable ? '' : ' <span class="text-danger">*</span>';

            if (fk[f.name]) {
                // Render a <select> placeholder; options populated asynchronously below
                return `<div class="col-md-6">
                    <label class="form-label fw-semibold">${label}${requiredMark}</label>
                    <select class="form-select" name="${f.name}" id="fk_sel_${f.name}" ${required}>
                        <option value="">-- Pilih ${label} --</option>
                    </select>
                </div>`;
            }

            const inputType = (f.type.includes('int') || f.type.includes('decimal') || f.type.includes('float')) ? 'number' : 'text';
            const step = (f.type.includes('decimal') || f.type.includes('float')) ? 'step="any"' : '';
            const safeVal = String(value).replace(/"/g, '&quot;');
            return `<div class="col-md-6">
                <label class="form-label fw-semibold">${label}</label>
                <input type="${inputType}" ${step} class="form-control" name="${f.name}" value="${safeVal}" ${required}>
            </div>`;
        }).join('');

        // Populate FK <select> dropdowns asynchronously
        for (const [fieldName, fkDef] of Object.entries(fk)) {
            const select = form.querySelector(`#fk_sel_${fieldName}`);
            if (!select) continue;
            const currentVal = row ? String(row[fieldName] ?? '') : '';
            const options = await loadFkOptions(fkDef.entity);
            options.forEach(opt => {
                const el = document.createElement('option');
                el.value = opt.id;
                el.textContent = opt.name;
                if (String(opt.id) === currentVal) el.selected = true;
                select.appendChild(el);
            });
        }
    }

    // ── Load schema + data ────────────────────────────────────────────────────

    async function loadSchemaAndData() {
        const entity = currentEntity();
        if (!entity) return;

        try {
            const schemaResp = await fetchJson(`${BASE_URL}master-data/schema/${entity}`);
            schema = schemaResp.data;

            const listResp = await fetchJson(`${BASE_URL}master-data/list/${entity}?limit=5000`);
            rows = listResp.data || [];
            if (listResp.meta?.effective_pk) schema.pk = listResp.meta.effective_pk;

            info.innerHTML = `<div class="md-entity-meta">
                <span class="md-badge">Entity: ${escHtml(schema.entity)}</span>
                <span class="md-badge">Table: ${escHtml(schema.table)}</span>
                <span class="md-badge">PK: ${escHtml(schema.pk)}</span>
                <span class="md-badge">Rows: ${rows.length}</span>
            </div>`;

            renderTable();
        } catch (e) {
            destroyDataTableSafe();
            schema = null; rows = [];
            head.innerHTML = '';
            body.innerHTML = `<tr><td class="text-danger text-center">Gagal memuat data: ${escHtml(e.message)}</td></tr>`;
            info.innerHTML = '';
        }
    }

    // ── Event listeners ───────────────────────────────────────────────────────

    btnRefresh.addEventListener('click', loadSchemaAndData);

    btnAdd.addEventListener('click', async () => {
        if (!schema) return;
        mode = 'create';
        selectedId = null;
        modalTitle.textContent = `Tambah Data - ${schema.title}`;
        await buildForm();
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
            await buildForm(row);
            showModalSafe();
            return;
        }

        if (action === 'delete') {
            if (!confirm('Yakin hapus data ini?')) return;
            try {
                await fetchJson(`${BASE_URL}master-data/delete/${currentEntity()}/${id}`, { method: 'DELETE' });
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
            ? `${BASE_URL}master-data/update/${entity}/${selectedId}`
            : `${BASE_URL}master-data/create/${entity}`;
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

