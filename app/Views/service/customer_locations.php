<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4 py-3">

    <!-- ── Page Header ─────────────────────────────────────────── -->
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-0 fw-semibold">Customer Location Management</h4>
            <small class="text-muted">Kelola lokasi pelanggan, area, dan teknisi terkait</small>
        </div>
        <?php if ($can_create ?? false): ?>
        <button class="btn btn-primary btn-sm" onclick="openAddModal()">
            <i class="fas fa-plus me-1"></i> Tambah Lokasi
        </button>
        <?php endif; ?>
    </div>

    <!-- ── Stats Cards ─────────────────────────────────────────── -->
    <div class="row mt-3 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-primary-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-geo-alt stat-icon text-primary"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= $totalLocations ?></div>
                        <div class="text-muted">Total Lokasi</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-warning-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-hourglass-split stat-icon text-warning"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= $pendingCount ?></div>
                        <div class="text-muted">Menunggu Approval</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Filters ─────────────────────────────────────────────── -->
    <div class="card optima-card mb-3">
        <div class="card-body py-2">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label small mb-1">Customer</label>
                    <div id="filterCustomerWrap" style="position:relative;z-index:1">
                    <select id="filterCustomer" class="form-select form-select-sm" style="width:100%">
                        <option value="">Semua Customer</option>
                        <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= esc($c['customer_name']) ?> (<?= esc($c['customer_code']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label small mb-1">Status Approval</label>
                    <select id="filterStatus" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="APPROVED_NULL">Approved</option>
                        <option value="PENDING">Pending</option>
                        <option value="REJECTED">Rejected</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label small mb-1">Tipe Lokasi</label>
                    <select id="filterLocType" class="form-select form-select-sm">
                        <option value="">Semua Tipe</option>
                        <option value="HEAD_OFFICE">Head Office</option>
                        <option value="BRANCH">Branch</option>
                        <option value="WAREHOUSE">Warehouse</option>
                        <option value="FACTORY">Factory</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label small mb-1">Departemen</label>
                    <select id="filterDepartemen" class="form-select form-select-sm">
                        <option value="">Semua Dept</option>
                        <?php foreach ($departemen as $d): ?>
                        <option value="<?= $d['id_departemen'] ?>"><?= esc($d['nama_departemen']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label small mb-1">&nbsp;</label>
                    <button class="btn btn-outline-secondary btn-sm w-100" onclick="resetFilters()">
                        <span class="material-symbols-rounded" style="font-size:14px;vertical-align:-2px">filter_alt_off</span>
                        Reset Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── DataTable ───────────────────────────────────────────── -->
    <div class="card optima-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="locationsTable" class="table table-hover optima-table mb-0 w-100">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Nama Lokasi</th>
                            <th>Kode</th>
                            <th>Tipe</th>
                            <th>Kota</th>
                            <th>Area</th>
                            <th class="text-center">Unit Aktif</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════
     MODAL: DETAIL LOKASI
════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-map-marker-alt me-2 text-primary"></i><span id="detailTitle">Detail Lokasi</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Tabs -->
                <ul class="nav nav-tabs px-3 pt-2" id="detailTabs">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#paneInfo">
                            <span class="material-symbols-rounded me-1" style="font-size:15px;vertical-align:-2px">info</span>Info
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tabUnits" data-bs-toggle="tab" data-bs-target="#paneUnits">
                            <span class="material-symbols-rounded me-1" style="font-size:15px;vertical-align:-2px">forklift</span>Unit
                            <span id="unitCountBadge" class="badge badge-soft-blue ms-1" style="display:none"></span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tabEmployees" data-bs-toggle="tab" data-bs-target="#paneEmployees">
                            <i class="fas fa-users me-1"></i>Employee
                            <span id="empCountBadge" class="badge badge-soft-green ms-1" style="display:none"></span>
                        </button>
                    </li>
                </ul>
                <div class="tab-content px-3 py-3" style="height:420px;overflow-y:auto">
                    <!-- Info tab -->
                    <div class="tab-pane fade show active" id="paneInfo">
                        <div id="detailInfoContent"><div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div></div></div>
                    </div>
                    <!-- Units tab -->
                    <div class="tab-pane fade" id="paneUnits">
                        <div id="detailUnitsContent"><div class="text-center py-4 text-muted">Klik tab untuk memuat data unit.</div></div>
                    </div>
                    <!-- Employees tab -->
                    <div class="tab-pane fade" id="paneEmployees">
                        <div id="detailEmployeesContent"><div class="text-center py-4 text-muted">Klik tab untuk memuat data teknisi.</div></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php if ($can_edit ?? false): ?>
                <button class="btn btn-outline-primary btn-sm" onclick="openEditFromDetail()">
                    <i class="fas fa-edit me-1"></i> Edit Lokasi
                </button>
                <?php endif; ?>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════
     MODAL: TAMBAH LOKASI
════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2 text-primary"></i>Tambah Lokasi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addForm">
                <div class="modal-body p-0">
                    <div class="px-4 pt-3 pb-2">
                        <div class="alert alert-warning d-flex align-items-start gap-2 mb-3 py-2">
                            <span class="material-symbols-rounded mt-1" style="font-size:18px">pending_actions</span>
                            <small>Lokasi baru memerlukan <strong>persetujuan tim Marketing</strong> sebelum aktif. Request akan muncul di halaman Audit Approval.</small>
                        </div>
                        <div class="row g-3 mb-2">
                            <div class="col-md-8">
                                <label class="form-label">Customer <span class="text-danger">*</span></label>
                                <select class="form-select" name="customer_id" id="addCustomerId" required style="width:100%">
                                    <option value="">Pilih Customer...</option>
                                    <?php foreach ($customers as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= esc($c['customer_name']) ?> (<?= esc($c['customer_code']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tipe Lokasi</label>
                                <select class="form-select" name="location_type">
                                    <option value="BRANCH">Branch</option>
                                    <option value="HEAD_OFFICE">Head Office</option>
                                    <option value="WAREHOUSE">Warehouse</option>
                                    <option value="FACTORY">Factory</option>
                                </select>
                            </div>
                        <div class="col-md-12">
                            <label class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="location_name" required maxlength="100" placeholder="cth: Pabrik Surabaya">
                        </div>
                    </div>
                    </div><!-- /non-scrollable -->
                    <div style="max-height:45vh;overflow-y:auto;padding:0 1.5rem 0.5rem">
                    <div class="row g-3 mt-0">
                        <div class="col-md-12">
                            <label class="form-label">Alamat <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="address" rows="2" required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kota <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="city" required maxlength="100">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="province" required maxlength="100">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kode Pos</label>
                            <input type="text" class="form-control" name="postal_code" maxlength="10">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kontak Person</label>
                            <input type="text" class="form-control" name="contact_person" maxlength="128">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jabatan/Posisi</label>
                            <input type="text" class="form-control" name="pic_position" maxlength="64">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telepon</label>
                            <input type="text" class="form-control" name="phone" maxlength="32">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" maxlength="128">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Catatan</label>
                            <input type="text" class="form-control" name="notes" maxlength="255">
                        </div>
                    </div>
                    </div><!-- /scrollable -->
                </div><!-- /modal-body -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="addSubmitBtn">
                        <i class="fas fa-paper-plane me-1"></i> Kirim Permintaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════
     MODAL: EDIT LOKASI
════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2 text-primary"></i>Edit Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm">
                <input type="hidden" id="editLocationId" name="id">
                <div class="modal-body" style="max-height:65vh;overflow-y:auto">
                    <div id="editFormContent">
                        <div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="editSubmitBtn">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
let locationsTable;
let currentDetailId  = null;
let unitsLoaded      = false;
let employeesLoaded  = false;

// ── Helpers ──────────────────────────────────────────────────

function approvalBadge(status) {
    if (!status || status === 'APPROVED') return '<span class="badge badge-soft-green">Approved</span>';
    if (status === 'PENDING')   return '<span class="badge badge-soft-yellow">Pending</span>';
    if (status === 'REJECTED')  return '<span class="badge badge-soft-red">Rejected</span>';
    return '<span class="badge badge-soft-gray">' + status + '</span>';
}

function locTypeBadge(type) {
    const map = {
        HEAD_OFFICE: 'badge-soft-blue',
        BRANCH:      'badge-soft-cyan',
        WAREHOUSE:   'badge-soft-purple',
        FACTORY:     'badge-soft-orange',
    };
    return `<span class="badge ${map[type] || 'badge-soft-gray'}">${type || '-'}</span>`;
}

// ── DataTable ────────────────────────────────────────────────

$(document).ready(function() {
    // Select2 — customer filter & add modal
    $('#filterCustomer').select2({
        placeholder:    'Semua Customer',
        allowClear:     true,
        width:          '100%',
        dropdownParent: $('#filterCustomerWrap'),
    });
    $('#filterCustomer').on('change', () => locationsTable && locationsTable.ajax.reload());

    // Close filter Select2 (z-index conflict) when add/edit modal opens
    ['#addModal','#editModal'].forEach(function(sel) {
        $(sel).on('show.bs.modal', function() {
            $('#filterCustomer').select2('close');
        });
    });

    $('#addModal').on('shown.bs.modal', function() {
        // Re-init each open (in case modal was previously closed and reopened)
        if ($('#addCustomerId').data('select2')) {
            $('#addCustomerId').select2('destroy');
        }
        $('#addCustomerId').select2({
            placeholder:    'Pilih Customer...',
            allowClear:     true,
            width:          '100%',
            dropdownParent: $('#addModal'),
        });
    });
    $('#addModal').on('hidden.bs.modal', function() {
        if ($('#addCustomerId').data('select2')) {
            $('#addCustomerId').select2('destroy');
        }
    });
    locationsTable = $('#locationsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.BASE_URL + 'service/customer-locations/getData',
            type: 'POST',
            data: function(d) {
                d[window.csrfTokenName] = window.getCsrfToken();
                d.filter_customer      = $('#filterCustomer').val();
                d.filter_area          = $('#filterArea').val();
                d.filter_status        = $('#filterStatus').val();
                d.filter_location_type = $('#filterLocType').val();
                d.filter_departemen    = $('#filterDepartemen').val();
            },
            dataSrc: function(json) {
                return json.data || [];
            },
        },
        columns: [
            { data: null, render: r => `<div class="fw-semibold">${esc(r.customer_name)}</div><small class="text-muted">${esc(r.customer_code)}</small>` },
            { data: null, render: r => {
                let badge = r.is_primary == 1 ? ' <span class="badge badge-soft-blue" style="font-size:10px">Utama</span>' : '';
                return `${esc(r.location_name)}${badge}<br><small class="text-muted">${esc(r.address?.substring(0,50))}…</small>`;
            }},
            { data: 'location_code', render: v => `<code>${esc(v)}</code>` },
            { data: 'location_type', render: v => locTypeBadge(v) },
            { data: null, render: r => `${esc(r.city)}<br><small class="text-muted">${esc(r.province)}</small>` },
            { data: 'areas_list', render: v => v ? v.split(', ').map(a => `<span class="badge badge-soft-cyan me-1">${esc(a)}</span>`).join('') : '<span class="text-muted small">-</span>' },
            { data: 'active_unit_count', className: 'text-center',
              render: v => v > 0 ? `<span class="badge badge-soft-blue">${v} unit</span>` : '<span class="text-muted small">-</span>' },
            { data: 'approval_status', className: 'text-center', render: v => approvalBadge(v) },
            { data: null, className: 'text-center', orderable: false,
              render: r => `<button class="btn btn-sm btn-outline-primary btn-icon-only" onclick="openDetail(${r.id})" title="Detail"><i class="fas fa-eye"></i></button>`,
            },
        ],
        order: [[0, 'asc']],
        pageLength: 25,
        language: {
            emptyTable:     'Belum ada data lokasi',
            info:           'Menampilkan _START_ – _END_ dari _TOTAL_ lokasi',
            infoEmpty:      'Menampilkan 0 lokasi',
            search:         'Cari:',
            searchPlaceholder: 'Cari lokasi, customer, kota...',
            lengthMenu:     'Tampilkan _MENU_ entri',
            paginate: { next: 'Berikutnya', previous: 'Sebelumnya' },
        },
    });

    // Re-draw on filter change (filterCustomer handled by Select2 above)
    ['#filterStatus','#filterLocType','#filterDepartemen'].forEach(sel => {
        $(sel).on('change', () => locationsTable.ajax.reload());
    });
});

function resetFilters() {
    $('#filterCustomer').val(null).trigger('change');
    ['#filterStatus','#filterLocType','#filterDepartemen'].forEach(sel => $(sel).val(''));
    locationsTable.ajax.reload();
}

function esc(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Detail Modal ─────────────────────────────────────────────

function openDetail(id) {
    currentDetailId = id;
    unitsLoaded     = false;
    employeesLoaded = false;
    $('#detailInfoContent').html('<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div></div>');
    $('#detailUnitsContent').html('<div class="text-center py-4 text-muted">Klik tab untuk memuat data unit.</div>');
    $('#detailEmployeesContent').html('<div class="text-center py-4 text-muted">Klik tab untuk memuat data teknisi.</div>');
    $('#unitCountBadge, #empCountBadge').hide();

    // Reset to first tab
    $('#detailTabs .nav-link').removeClass('active');
    $('#detailTabs .nav-link:first').addClass('active');
    $('#paneInfo').addClass('show active');
    $('#paneUnits, #paneEmployees').removeClass('show active');

    $('#detailModal').modal('show');
    loadDetailInfo(id);
}

function loadDetailInfo(id) {
    fetch(window.BASE_URL + 'service/customer-locations/' + id)
        .then(r => r.json())
        .then(res => {
            if (!res.success) { $('#detailInfoContent').html('<div class="alert alert-danger">Gagal memuat data.</div>'); return; }
            const d = res.data;
            $('#detailTitle').text(d.location_name);
            $('#detailModal').data('location', d);

            let approvalHtml = '';
            if (d.approval_status === 'PENDING') {
                approvalHtml = `<div class="alert alert-warning py-2 mb-3">
                    <span class="material-symbols-rounded me-1" style="font-size:16px;vertical-align:-3px">pending_actions</span>
                    Lokasi ini menunggu persetujuan tim Marketing.
                </div>`;
            } else if (d.approval_status === 'REJECTED') {
                approvalHtml = `<div class="alert alert-danger py-2 mb-3">
                    <span class="material-symbols-rounded me-1" style="font-size:16px;vertical-align:-3px">cancel</span>
                    Permintaan lokasi ini ditolak.${d.approval_notes ? ' Catatan: ' + esc(d.approval_notes) : ''}
                </div>`;
            }

            $('#detailInfoContent').html(`
                ${approvalHtml}
                <div class="row g-3">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td class="text-muted pe-2" width="140">Customer</td><td><strong>${esc(d.customer_name)}</strong> <span class="badge badge-soft-gray">${esc(d.customer_code)}</span></td></tr>
                            <tr><td class="text-muted">Kode Lokasi</td><td><code>${esc(d.location_code)}</code></td></tr>
                            <tr><td class="text-muted">Tipe</td><td>${locTypeBadge(d.location_type)}</td></tr>
                            <tr><td class="text-muted">Status</td><td>${approvalBadge(d.approval_status)}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td class="text-muted pe-2" width="140">Alamat</td><td>${esc(d.address)}</td></tr>
                            <tr><td class="text-muted">Kota</td><td>${esc(d.city)}, ${esc(d.province)} ${d.postal_code ? esc(d.postal_code) : ''}</td></tr>
                            <tr><td class="text-muted">Kontak</td><td>${esc(d.contact_person) || '-'}</td></tr>
                            <tr><td class="text-muted">Jabatan</td><td>${esc(d.pic_position) || '-'}</td></tr>
                            <tr><td class="text-muted">Telepon</td><td>${d.phone ? `<a href="tel:${esc(d.phone)}">${esc(d.phone)}</a>` : '-'}</td></tr>
                            <tr><td class="text-muted">Email</td><td>${d.email ? `<a href="mailto:${esc(d.email)}">${esc(d.email)}</a>` : '-'}</td></tr>
                            ${d.notes ? `<tr><td class="text-muted">Catatan</td><td>${esc(d.notes)}</td></tr>` : ''}
                        </table>
                    </div>
                </div>
            `);
        })
        .catch(() => $('#detailInfoContent').html('<div class="alert alert-danger">Koneksi gagal.</div>'));
}

// Lazy-load units tab
$('#tabUnits').on('shown.bs.tab', function() {
    if (unitsLoaded || !currentDetailId) return;
    unitsLoaded = true;
    fetch(window.BASE_URL + 'service/customer-locations/' + currentDetailId + '/units')
        .then(r => r.json())
        .then(res => {
            if (!res.success) { $('#detailUnitsContent').html('<div class="alert alert-danger">Gagal memuat unit.</div>'); return; }
            const units = res.data;
            if (!units.length) {
                $('#detailUnitsContent').html('<div class="text-center py-4 text-muted"><span class="material-symbols-rounded" style="font-size:40px">forklift</span><p>Tidak ada unit aktif di lokasi ini.</p></div>');
                return;
            }
            $('#unitCountBadge').text(units.length).show();
            let rows = units.map(u => `<tr>
                <td><strong>${esc(u.no_pol)}</strong></td>
                <td>${esc(u.serial_number) || '-'}</td>
                <td>${esc(u.jenis) || '-'}</td>
                <td>${esc(u.merk) || '-'} ${u.model ? esc(u.model) : ''}</td>
                <td>${esc(u.area_name) ? `<span class="badge badge-soft-cyan">${esc(u.area_name)}</span>` : '-'}</td>
                <td>${esc(u.departemen) ? `<span class="badge badge-soft-purple">${esc(u.departemen)}</span>` : '-'}</td>
                <td><span class="badge badge-soft-gray">${esc(u.status_name) || '-'}</span></td>
            </tr>`).join('');
            $('#detailUnitsContent').html(`
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead><tr><th>No Pol</th><th>S/N</th><th>Jenis</th><th>Merk/Model</th><th>Area</th><th>Departemen</th><th>Status</th></tr></thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>`);
        })
        .catch(() => $('#detailUnitsContent').html('<div class="alert alert-danger">Koneksi gagal.</div>'));
});

// Lazy-load employees tab
$('#tabEmployees').on('shown.bs.tab', function() {
    if (employeesLoaded || !currentDetailId) return;
    employeesLoaded = true;
    fetch(window.BASE_URL + 'service/customer-locations/' + currentDetailId + '/employees')
        .then(r => r.json())
        .then(res => {
            if (!res.success) { $('#detailEmployeesContent').html('<div class="alert alert-danger">Gagal memuat teknisi.</div>'); return; }
            const emps = res.data;
            if (res.message && !emps.length) {
                $('#detailEmployeesContent').html(`<div class="alert alert-info py-2">${res.message}</div>`);
                return;
            }
            if (!emps.length) {
                $('#detailEmployeesContent').html('<div class="text-center py-4 text-muted"><i class="fas fa-users fa-3x mb-2"></i><p>Tidak ada employee di area ini.</p></div>');
                return;
            }
            $('#empCountBadge').text(emps.length).show();
            const assignBadge = t => ({ PRIMARY:'badge-soft-green', BACKUP:'badge-soft-yellow', TEMPORARY:'badge-soft-orange' }[t] || 'badge-soft-gray');
            let rows = emps.map(e => `<tr>
                <td><strong>${esc(e.nama)}</strong><br><small class="text-muted">${esc(e.staff_code)}</small></td>
                <td><span class="badge badge-soft-blue">${esc(e.role) || '-'}</span></td>
                <td>${e.telepon ? `<a href="tel:${esc(e.telepon)}">${esc(e.telepon)}</a>` : '-'}</td>
                <td><span class="badge ${assignBadge(e.assignment_type)}">${esc(e.assignment_type)}</span></td>
                <td><span class="badge badge-soft-cyan">${esc(e.area_name)}</span></td>
                <td>${esc(e.department_scope) || 'ALL'}</td>
            </tr>`).join('');
            $('#detailEmployeesContent').html(`
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead><tr><th>Nama</th><th>Role</th><th>HP</th><th>Tipe</th><th>Area</th><th>Scope</th></tr></thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>`);
        })
        .catch(() => $('#detailEmployeesContent').html('<div class="alert alert-danger">Koneksi gagal.</div>'));
});

function openEditFromDetail() {
    const loc = $('#detailModal').data('location');
    if (!loc) return;
    $('#detailModal').modal('hide');
    setTimeout(() => openEdit(loc.id), 300);
}

// ── Add Modal ────────────────────────────────────────────────

function openAddModal() {
    document.getElementById('addForm').reset();
    $('#addModal').modal('show');
}

$('#addForm').on('submit', function(e) {
    e.preventDefault();
    const btn = $('#addSubmitBtn');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Mengirim...');

    const fd = new FormData(this);
    fd.append(window.csrfTokenName, window.getCsrfToken());

    fetch(window.BASE_URL + 'service/customer-locations/store', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                $('#addModal').modal('hide');
                OptimaNotify.success(res.message || 'Permintaan lokasi berhasil dikirim.');
                locationsTable.ajax.reload();
            } else {
                let msg = res.message || 'Terjadi kesalahan.';
                if (res.errors) msg += '<ul class="mb-0 mt-1">' + Object.values(res.errors).map(e => `<li>${e}</li>`).join('') + '</ul>';
                OptimaNotify.error(msg);
            }
        })
        .catch(() => OptimaNotify.error('Koneksi gagal.'))
        .finally(() => btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i> Kirim Permintaan'));
});

// ── Edit Modal ───────────────────────────────────────────────

function openEdit(id) {
    $('#editLocationId').val(id);
    $('#editFormContent').html('<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div></div>');
    $('#editModal').modal('show');

    fetch(window.BASE_URL + 'service/customer-locations/' + id)
        .then(r => r.json())
        .then(res => {
            if (!res.success) { $('#editFormContent').html('<div class="alert alert-danger">Gagal memuat data.</div>'); return; }
            const d = res.data;
            const locTypes = ['BRANCH','HEAD_OFFICE','WAREHOUSE','FACTORY'];
            let typeOpts = locTypes.map(t => `<option value="${t}" ${d.location_type === t ? 'selected' : ''}>${t.replace('_',' ')}</option>`).join('');

            $('#editFormContent').html(`
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="location_name" value="${esc(d.location_name)}" required maxlength="100">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tipe Lokasi</label>
                        <select class="form-select" name="location_type">${typeOpts}</select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Alamat <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="address" rows="2" required>${esc(d.address)}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kota <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="city" value="${esc(d.city)}" required maxlength="100">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="province" value="${esc(d.province)}" required maxlength="100">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kode Pos</label>
                        <input type="text" class="form-control" name="postal_code" value="${esc(d.postal_code)}" maxlength="10">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kontak Person</label>
                        <input type="text" class="form-control" name="contact_person" value="${esc(d.contact_person)}" maxlength="128">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jabatan/Posisi</label>
                        <input type="text" class="form-control" name="pic_position" value="${esc(d.pic_position)}" maxlength="64">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telepon</label>
                        <input type="text" class="form-control" name="phone" value="${esc(d.phone)}" maxlength="32">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="${esc(d.email)}" maxlength="128">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Catatan</label>
                        <input type="text" class="form-control" name="notes" value="${esc(d.notes)}" maxlength="255">
                    </div>
                </div>
            `);
        })
        .catch(() => $('#editFormContent').html('<div class="alert alert-danger">Koneksi gagal.</div>'));
}

$('#editForm').on('submit', function(e) {
    e.preventDefault();
    const id  = $('#editLocationId').val();
    const btn = $('#editSubmitBtn');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

    const fd = new FormData(this);
    fd.append(window.csrfTokenName, window.getCsrfToken());

    fetch(window.BASE_URL + 'service/customer-locations/update/' + id, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                $('#editModal').modal('hide');
                OptimaNotify.success(res.message || 'Data lokasi berhasil diperbarui.');
                locationsTable.ajax.reload();
            } else {
                let msg = res.message || 'Terjadi kesalahan.';
                if (res.errors) msg += '<ul class="mb-0 mt-1">' + Object.values(res.errors).map(e => `<li>${e}</li>`).join('') + '</ul>';
                OptimaNotify.error(msg);
            }
        })
        .catch(() => OptimaNotify.error('Koneksi gagal.'))
        .finally(() => btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan Perubahan'));
});
</script>

<?= $this->endSection() ?>
