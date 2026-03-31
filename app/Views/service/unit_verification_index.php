<?= $this->extend('layouts/base') ?>

<?php
/**
 * Unit Verification — daftar customer yang punya audit lokasi eligible + lokasi + Verifikasi per unit (modal WO).
 */
$stats = $stats ?? [];
helper('ui');
?>

<?= $this->section('content') ?>
<div class="unit-audit-page">

<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('service') ?>">Service</a></li>
                <li class="breadcrumb-item active">Unit Verification</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-clipboard-check me-2 text-primary"></i>Unit Verification
        </h4>
        <p class="text-muted small mb-0">
            Daftar otomatis: customer yang memiliki <strong>audit lokasi</strong> pada status eligible verifikasi.
            Urutan: buka <strong>customer</strong> → <strong>lokasi</strong> → tabel unit.
            <strong>Verifikasi</strong> membuka modal isian; <strong>Cetak</strong> di header lokasi = FORM VERIFIKASI UNIT untuk <em>semua unit</em> di lokasi itu (satu lembar per unit); <strong>Cetak</strong> di baris = satu unit saja.
        </p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="stat-card bg-primary-soft">
            <div class="text-muted small">Total Audit</div>
            <div class="fs-4 fw-bold"><?= (int) ($stats['total'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card bg-warning-soft">
            <div class="text-muted small">In Progress</div>
            <div class="fs-4 fw-bold"><?= (int) ($stats['in_progress'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card bg-info-soft">
            <div class="text-muted small">Results Entered</div>
            <div class="fs-4 fw-bold"><?= (int) ($stats['results_entered'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card bg-orange-soft">
            <div class="text-muted small">Pending Approval</div>
            <div class="fs-4 fw-bold"><?= (int) ($stats['pending_approval'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card bg-success-soft">
            <div class="text-muted small">Approved</div>
            <div class="fs-4 fw-bold"><?= (int) ($stats['approved'] ?? 0) ?></div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4 unit-audit">
    <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h6 class="mb-0 fw-semibold"><i class="fas fa-list me-2 text-primary"></i>Customer &amp; lokasi dengan audit aktif</h6>
            <p class="text-muted small mb-0 mt-1">Hanya customer yang punya audit lokasi aktif dan sudah ada data verifikasi per lokasi. Gunakan tombol cetak untuk borang kosong (data dari database), sama layout dengan cetak Work Order.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small" id="uvOverviewMeta">—</span>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="uvRefreshOverview" title="Muat ulang">
                <i class="fas fa-sync me-1"></i>Refresh
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div id="uvOverviewContent">
            <div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Memuat daftar...</div>
        </div>
    </div>
</div>

</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<style>
.bg-orange-soft { background: rgba(253, 126, 20, 0.12); border-radius: 0.5rem; padding: 0.75rem 1rem; }
.unit-audit .uv-location-header-actions { min-height: 42px; }
.unit-audit .uv-location-toggle { flex: 1 1 auto; min-width: 0; }
.unit-audit .uv-btn-print-location { white-space: nowrap; }
</style>
<script>
const UV_BASE = '<?= base_url() ?>';

function uvEsc(s) {
    if (s == null || s === '') return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function uvEscAttr(s) {
    return uvEsc(s).replace(/'/g, '&#39;');
}

function uvBadgeAuditStatus(st) {
    const m = {
        'PRINTED': 'badge-soft-cyan',
        'IN_PROGRESS': 'badge-soft-yellow',
        'RESULTS_ENTERED': 'badge-soft-blue',
        'PENDING_APPROVAL': 'badge-soft-orange',
        'APPROVED': 'badge-soft-green',
        'REJECTED': 'badge-soft-red',
        'DRAFT': 'badge-soft-gray'
    };
    const cls = m[st] || 'badge-soft-gray';
    return `<span class="badge ${cls}">${uvEsc(st || '-')}</span>`;
}

function uvBadgeVerificationState(isVerified) {
    return isVerified
        ? '<span class="badge badge-soft-green">Sudah diverifikasi</span>'
        : '<span class="badge badge-soft-gray">Belum diverifikasi</span>';
}

/** Isi tabel unit + tombol Verifikasi untuk satu lokasi */
function uvHtmlLocationBody(loc) {
    const audit = loc.verification_audit;
    const units = loc.verification_units || [];
    if (audit && units.length) {
        let body = '<div class="table-responsive"><table class="table table-sm table-hover mb-0"><thead class="table-light"><tr>';
        body += '<th>No. Unit</th><th>Serial</th><th>Merk / Model</th><th>Status Verifikasi</th><th class="text-end" style="min-width:155px">Aksi</th></tr></thead><tbody>';
        units.forEach(u => {
            body += `<tr>
                <td class="fw-semibold">${uvEsc(u.no_unit)}</td>
                <td class="small">${uvEsc(u.serial_number || '—')}</td>
                <td class="small">${uvEsc(u.merk_model || '—')}</td>
                <td class="small">${uvBadgeVerificationState(!!u.is_verified)}</td>
                <td class="text-end text-nowrap">
                    <button type="button" class="btn btn-sm btn-outline-secondary uv-btn-print-unit" title="Cetak FORM VERIFIKASI UNIT — unit ini saja"
                        data-audit-id="${u.audit_id}" data-unit-id="${u.unit_id}">
                        <i class="fas fa-print"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-primary uv-btn-verify"
                        data-audit-id="${u.audit_id}" data-unit-id="${u.unit_id}"
                        data-label="${uvEscAttr((audit.audit_number || '') + ' | ' + (u.no_unit || ''))}">
                        <i class="fas fa-clipboard-check me-1"></i>Verifikasi
                    </button>
                </td>
            </tr>`;
        });
        body += '</tbody></table></div>';
        return body;
    }
    if (audit && !units.length) {
        return '<div class="text-muted small p-3">Audit ada tetapi belum ada baris unit di audit ini.</div>';
    }
    return '<div class="text-muted small p-3">Lakukan audit lokasi di halaman Unit Audit terlebih dahulu.</div>';
}

function uvRenderOverview(customers) {
    const $list = $('#uvOverviewContent');
    if (!customers || !customers.length) {
        $list.html(
            '<div class="text-center py-5 text-muted">' +
            '<i class="fas fa-inbox fa-2x mb-3 d-block opacity-50"></i>' +
            'Belum ada customer dengan audit lokasi yang siap untuk verifikasi unit.<br>' +
            '<small>Buat atau lanjutkan audit di halaman <a href="' + UV_BASE + 'service/unit-audit">Unit Audit</a>.</small>' +
            '</div>'
        );
        $('#uvOverviewMeta').text('0 customer');
        return;
    }

    let html = '<div class="accordion accordion-flush" id="uvCustomerAccordion">';
    customers.forEach(cust => {
        const cid = cust.id;
        const accCust = 'uvCust_' + cid;
        const locParentId = 'uvLocAccWrap_' + cid;
        const locations = cust.locations || [];
        const custHead = uvEsc(cust.customer_name || '') + (cust.customer_code
            ? ` <span class="text-muted">(${uvEsc(cust.customer_code)})</span>` : '');
        const locCount = locations.length;
        let totalUnits = 0;
        locations.forEach(function(L) {
            totalUnits += (L.verification_units || []).length;
        });

        let inner = '';
        if (locations.length) {
            inner += `<div class="accordion accordion-flush border rounded overflow-hidden" id="${locParentId}">`;
            locations.forEach(loc => {
                const lid = loc.id;
                const accLoc = locParentId + '_loc_' + lid;
                const head = uvEsc(loc.location_name || '—');
                const addr = uvEsc(loc.address || '');
                const audit = loc.verification_audit;
                const auditLine = audit
                    ? `<div class="small text-muted mt-1">Audit: <strong>${uvEsc(audit.audit_number)}</strong> ${uvBadgeAuditStatus(audit.effective_status || audit.status)}${(audit.total_units > 0 ? ` <span class="badge badge-soft-blue ms-1">${audit.verified_units || 0}/${audit.total_units} unit diverifikasi</span>` : '')}</div>`
                    : '';
                const unitList = loc.verification_units || [];
                const nUnit = unitList.length;
                const auditPk = audit ? audit.id : '';

                inner += `<div class="accordion-item border-bottom border-light">
                    <h3 class="accordion-header uv-location-header-actions d-flex flex-nowrap align-items-center gap-1 py-0 pe-2" id="h_${accLoc}">
                        <button class="accordion-button collapsed uv-location-toggle flex-grow-1 py-2 small" type="button" data-bs-toggle="collapse" data-bs-target="#${accLoc}">
                            <span class="text-start"><i class="fas fa-map-marker-alt me-2 text-primary"></i><strong>${head}</strong>
                            ${nUnit ? `<span class="badge bg-secondary ms-2">${nUnit} unit</span>` : ''}
                            <span class="d-block text-muted fw-normal">${addr}</span>${auditLine}</span>
                        </button>
                        ${auditPk ? `<button type="button" class="btn btn-sm btn-outline-primary align-self-center ms-1 uv-btn-print-location flex-shrink-0" title="Cetak FORM VERIFIKASI UNIT — semua unit di lokasi (1 halaman per unit)"
                            data-audit-id="${auditPk}">
                            <i class="fas fa-print me-1"></i><span class="d-none d-sm-inline">Print Verification</span>
                        </button>` : ''} 
                    </h3>
                    <div id="${accLoc}" class="accordion-collapse collapse" data-bs-parent="#${locParentId}">
                        <div class="accordion-body pt-0 bg-light">${uvHtmlLocationBody(loc)}</div>
                    </div>
                </div>`;
            });
            inner += '</div>';
        } else {
            inner = '<p class="text-muted small mb-0">Tidak ada lokasi</p>';
        }

        html += `<div class="accordion-item border-bottom">
            <h2 class="accordion-header" id="h_${accCust}">
                <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#${accCust}">
                    <span><i class="fas fa-building me-2 text-primary"></i><strong>${custHead}</strong>
                    <span class="badge badge-soft-blue ms-2">${locCount} lokasi</span>
                    ${totalUnits ? `<span class="badge badge-soft-gray ms-1">${totalUnits} unit</span>` : ''}</span>
                </button>
            </h2>
            <div id="${accCust}" class="accordion-collapse collapse" data-bs-parent="#uvCustomerAccordion">
                <div class="accordion-body bg-white py-3">${inner}</div>
            </div>
        </div>`;
    });
    html += '</div>';
    $list.html(html);
    $('#uvOverviewMeta').text(customers.length + ' customer');
}

function uvLoadFullOverview() {
    $('#uvOverviewContent').html('<div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Memuat daftar...</div>');
    $.get(UV_BASE + 'service/unit-audit/getVerificationOverview', function(res) {
        if (!res.success) {
            $('#uvOverviewContent').html('<div class="text-center py-4 text-danger">Gagal memuat data</div>');
            $('#uvOverviewMeta').text('—');
            return;
        }
        uvRenderOverview(res.data || []);
    }).fail(function() {
        $('#uvOverviewContent').html('<div class="text-center py-4 text-danger">Gagal memuat data</div>');
        $('#uvOverviewMeta').text('—');
    });
}

function uvTryOpenFromQuery() {
    const params = new URLSearchParams(window.location.search);
    const audit = parseInt(params.get('audit') || '0', 10);
    const unit = parseInt(params.get('unit') || '0', 10);
    if (audit > 0 && unit > 0 && typeof window.loadUnitVerificationDataForAudit === 'function') {
        window.loadUnitVerificationDataForAudit(audit, unit, 'Audit #' + audit);
        const url = window.location.pathname;
        window.history.replaceState({}, '', url);
    }
}

$(function() {
    uvLoadFullOverview();
    $('#uvRefreshOverview').on('click', uvLoadFullOverview);

    $(document).on('click', '.uv-btn-print-location', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const aid = $(this).data('audit-id');
        if (aid) {
            window.open(UV_BASE + 'service/work-orders/print-verification?audit_id=' + encodeURIComponent(aid), '_blank');
        }
    });

    $(document).on('click', '.uv-btn-print-unit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const aid = $(this).data('audit-id');
        const uid = $(this).data('unit-id');
        if (aid && uid) {
            window.open(UV_BASE + 'service/work-orders/print-verification?audit_id=' + encodeURIComponent(aid) + '&unit_id=' + encodeURIComponent(uid), '_blank');
        }
    });

    $(document).on('click', '.uv-btn-verify', function() {
        const auditId = $(this).data('audit-id');
        const unitId = $(this).data('unit-id');
        const label = $(this).data('label') || '';
        if (typeof window.loadUnitVerificationDataForAudit === 'function') {
            window.loadUnitVerificationDataForAudit(auditId, unitId, label);
        } else {
            alert('Modal verifikasi belum termuat. Muat ulang halaman.');
        }
    });

    window.uvReloadLocationsAfterVerify = function() {
        uvLoadFullOverview();
    };
    setTimeout(uvTryOpenFromQuery, 600);
});
</script>
<?= view('service/unit_verification') ?>
<?= $this->endSection() ?>
