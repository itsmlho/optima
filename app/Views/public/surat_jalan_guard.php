<?php
/**
 * Halaman publik konfirmasi SJ untuk satpam (tanpa login).
 *
 * @var string $title
 * @var string $companyName
 * @var string $logoUrl
 * @var string $apiLookup
 * @var string $apiCheckpoint
 */
$companyName = $companyName ?? 'PT Sarana Mitra Luas Tbk';
$logoUrl     = $logoUrl ?? base_url('assets/images/company-logo.svg');
$apiLookup   = $apiLookup ?? base_url('surat-jalan/lookup');
$apiCheckpoint = $apiCheckpoint ?? base_url('surat-jalan/submit-checkpoint');
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Konfirmasi Surat Jalan') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --sj-navy: #1a365d;
            --sj-navy-light: #2c5282;
            --sj-line: #e2e8f0;
            --sj-bg: #eef2f7;
        }
        body {
            min-height: 100vh;
            background: linear-gradient(165deg, var(--sj-bg) 0%, #e8ecf3 45%, #dfe6f0 100%);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        .sj-shell {
            max-width: 920px;
            margin: 0 auto;
        }
        .sj-brand-bar {
            background: linear-gradient(135deg, var(--sj-navy) 0%, var(--sj-navy-light) 100%);
            color: #fff;
            border-radius: 14px 14px 0 0;
            padding: 1.1rem 1.35rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .sj-brand-bar img {
            height: 44px;
            width: auto;
            object-fit: contain;
            background: #fff;
            border-radius: 8px;
            padding: 4px 8px;
        }
        .sj-brand-bar h1 {
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: .02em;
        }
        .sj-brand-bar .sub {
            font-size: .78rem;
            opacity: .9;
            margin: 0.15rem 0 0;
        }
        .sj-card {
            border: none;
            border-radius: 0 0 14px 14px;
            box-shadow: 0 12px 40px rgba(26, 54, 93, 0.12);
            overflow: hidden;
        }
        .sj-card .card-body-inner {
            padding: 1.35rem 1.5rem 1.5rem;
        }
        .sj-section-title {
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #64748b;
            margin-bottom: .65rem;
        }
        .sj-detail-panel {
            background: #f8fafc;
            border: 1px solid var(--sj-line);
            border-radius: 10px;
            padding: 1rem 1.1rem;
        }
        .sj-detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: .65rem 1.25rem;
        }
        .sj-kv { font-size: .875rem; }
        .sj-kv .k { color: #64748b; font-size: .72rem; text-transform: uppercase; letter-spacing: .04em; }
        .sj-kv .v { font-weight: 600; color: #1e293b; }
        .sj-route-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            flex-wrap: wrap;
            font-size: .85rem;
            font-weight: 600;
            color: var(--sj-navy);
            background: #e8f0fa;
            border-radius: 8px;
            padding: .45rem .75rem;
            margin-top: .25rem;
        }
        .sj-checklist {
            border: 1px solid var(--sj-line);
            border-radius: 10px;
            background: #fff;
            padding: .65rem .75rem;
            max-height: 420px;
            overflow-y: auto;
        }
        .sj-item-card {
            border: 1px solid var(--sj-line);
            border-radius: 10px;
            padding: .65rem .85rem;
            margin-bottom: .65rem;
            background: #fafbfc;
        }
        .sj-item-card:last-child { margin-bottom: 0; }
        .sj-item-title {
            font-size: .82rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: .5rem;
            line-height: 1.35;
        }
        .sj-item-card .form-check-label { font-size: .8rem; }
        .sj-stop-hint { font-size: .78rem; color: #475569; }
        .sj-flow-banner {
            font-size: .8rem;
            border-radius: 8px;
            padding: .5rem .75rem;
            margin-bottom: .75rem;
        }
        .sj-cp-log {
            font-size: .78rem;
            color: #475569;
            border-top: 1px dashed var(--sj-line);
            margin-top: .85rem;
            padding-top: .75rem;
        }
        .sj-cp-log li { margin-bottom: .35rem; }
        .sj-form-actions .btn-success {
            padding: .55rem 1.25rem;
            font-weight: 600;
        }
        @media (max-width: 576px) {
            .sj-brand-bar { flex-direction: column; text-align: center; }
            .sj-brand-bar img { height: 40px; }
        }
    </style>
</head>
<body>
<div class="container py-4 sj-shell">
    <div class="card sj-card shadow-none">
        <div class="sj-brand-bar">
            <img src="<?= esc($logoUrl) ?>" alt="<?= esc($companyName) ?>" width="120" height="48" decoding="async" onerror="this.style.visibility='hidden'">
            <div>
                <h1><i class="fas fa-shield-halved me-2 opacity-75"></i>Konfirmasi Surat Jalan</h1>
                <p class="sub mb-0"><?= esc($companyName) ?> — formulir resmi petugas keamanan gerbang</p>
            </div>
        </div>
        <div class="card-body card-body-inner bg-white">
            <p class="text-muted small mb-3 mb-md-4">
                Masukkan <strong>nomor SJ</strong> dan <strong>6 digit kode verifikasi</strong> yang dibagikan gudang (misalnya lewat WhatsApp).
                Kode ini <em>bukan</em> teks bebas — harus angka persis seperti yang diterima dari gudang.
            </p>
            <div class="row g-3">
                <div class="col-md-7">
                    <label class="form-label fw-semibold">Nomor Surat Jalan</label>
                    <input id="sjNumber" class="form-control form-control-lg" placeholder="Contoh: SJ202604001" autocomplete="off">
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Kode verifikasi</label>
                    <input id="verifyCode" class="form-control form-control-lg font-monospace" placeholder="6 digit" inputmode="numeric" pattern="[0-9]*" maxlength="12" autocomplete="one-time-code">
                    <div class="form-text">Angka 6 digit dari gudang (sama untuk semua satpam di rute ini).</div>
                </div>
            </div>
            <div class="mt-3 d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary btn-lg" onclick="lookupSJ()">
                    <i class="fas fa-search me-2"></i>Cari Surat Jalan
                </button>
            </div>
            <div id="msgBox" class="mt-3"></div>
        </div>
    </div>

    <div class="card sj-card shadow-sm mt-4 d-none" id="detailCard">
        <div class="card-body card-body-inner">
            <div class="sj-section-title">Ringkasan &amp; konfirmasi</div>
            <div class="sj-detail-panel mb-4">
                <div id="sjMeta"></div>
                <div id="sjRoute" class="mt-2"></div>
                <div id="sjCheckpointLog" class="sj-cp-log d-none"></div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Checklist barang dibawa</label>
                <p class="small text-muted mb-2">Satu baris per jenis barang. Pilih <strong>satu</strong> opsi per barang untuk konfirmasi di titik rute yang Anda pilih di bawah. Minimal satu barang harus &ldquo;Lewat gerbang&rdquo; atau &ldquo;Drop di sini&rdquo;.</p>
                <div id="itemsChecklist" class="sj-checklist"></div>
            </div>

            <div id="sjFlowBanner" class="alert alert-light border sj-flow-banner d-none" role="status"></div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Lokasi (urutan rute)</label>
                    <select id="stopSelect" class="form-select"></select>
                    <div class="form-text sj-stop-hint" id="sjStopHint">Titik yang sudah lewat tidak bisa dipilih lagi — mengikuti urutan rute.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Jenis konfirmasi</label>
                    <select id="statusSelect" class="form-select"></select>
                    <div class="form-text sj-stop-hint">Otomatis menyesuaikan titik rute (tidak bisa &ldquo;Berangkat&rdquo; di tujuan akhir).</div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <label class="form-label">Nama petugas <span class="text-danger">*</span></label>
                    <input id="verifierName" class="form-control" placeholder="Nama lengkap" autocomplete="name">
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. HP petugas</label>
                    <input id="verifierPhone" class="form-control" placeholder="Opsional" autocomplete="tel">
                </div>
            </div>
            <div class="mt-2">
                <label class="form-label">Catatan</label>
                <textarea id="notes" class="form-control" rows="2" placeholder="Contoh: jam keluar gerbang, plat terlihat, dll."></textarea>
            </div>

            <div class="sj-form-actions mt-4 d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-success" id="btnSjSubmit" onclick="submitCheckpoint()">
                    <i class="fas fa-check-circle me-2"></i>Simpan konfirmasi
                </button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="<?= esc(csrf_token()) ?>" id="csrfField" value="<?= esc(csrf_hash()) ?>">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
let currentMovement = null;
let currentStops = [];
let currentItems = [];
let currentCheckpoints = [];
const lookupUrl = <?= json_encode($apiLookup) ?>;
const checkpointUrl = <?= json_encode($apiCheckpoint) ?>;

function escHtml(s) {
    const d = document.createElement('div');
    d.textContent = s == null ? '' : String(s);
    return d.innerHTML;
}

function showMsg(type, text) {
    $('#msgBox').html('<div class="alert alert-' + escHtml(type) + ' py-2 mb-0">' + escHtml(text) + '</div>');
}

function csrfPair() {
    const el = document.getElementById('csrfField');
    if (!el) return {};
    return { name: el.getAttribute('name'), value: el.value };
}

/** Stop dengan id, urut sequence_no */
function getValidStopsSorted() {
    return currentStops.filter(function(s) { return s && s.id; }).sort(function(a, b) {
        return (parseInt(a.sequence_no, 10) || 0) - (parseInt(b.sequence_no, 10) || 0);
    });
}

/** actual_at terisi (normalisasi string kosong / null dari API) */
function hasStopActualAt(s) {
    if (!s) {
        return false;
    }
    const a = s.actual_at;
    if (a === undefined || a === null) {
        return false;
    }
    const t = String(a).trim();
    if (t === '' || t.indexOf('0000-00-00') === 0) {
        return false;
    }
    return true;
}

/** Titik berikutnya yang belum punya konfirmasi (actual_at), mengalir sesuai rute */
function nextPendingStopId() {
    const sorted = getValidStopsSorted();
    for (let i = 0; i < sorted.length; i++) {
        if (!hasStopActualAt(sorted[i])) {
            return String(sorted[i].id);
        }
    }
    return '';
}

function stopById(stopId) {
    const sorted = getValidStopsSorted();
    return sorted.find(function(s) { return String(s.id) === String(stopId); });
}

/**
 * Jenis checkpoint yang sah per posisi rute (hindari "Berangkat" lagi di tujuan, dll.)
 * - titik pertama rute → hanya Berangkat (keluar asal)
 * - titik terakhir → hanya Sampai (tiba di tujuan akhir)
 * - perantara → hanya Transit
 */
function allowedStatusesForStopId(stopId) {
    const sorted = getValidStopsSorted();
    const s = stopById(stopId);
    if (!sorted.length || !stopId || !s) {
        return ['BERANGKAT'];
    }
    const seq = parseInt(s.sequence_no, 10) || 0;
    const seqs = sorted.map(function(x) { return parseInt(x.sequence_no, 10) || 0; });
    const minSeq = Math.min.apply(null, seqs);
    const maxSeq = Math.max.apply(null, seqs);
    if (seq === minSeq) {
        return ['BERANGKAT'];
    }
    if (seq === maxSeq) {
        return ['SAMPAI'];
    }
    return ['TRANSIT'];
}

const SJ_STATUS_LABELS = {
    BERANGKAT: 'Berangkat — keluar dari titik ini (asal / awal rute)',
    TRANSIT: 'Transit — konfirmasi di titik perantara',
    SAMPAI: 'Sampai — tiba di titik ini (tujuan akhir)'
};

/** Isi ulang dropdown jenis konfirmasi sesuai titik yang dipilih */
function rebuildStatusSelect(stopId) {
    const allowed = allowedStatusesForStopId(stopId);
    let html = '';
    allowed.forEach(function(k) {
        html += '<option value="' + escHtml(k) + '">' + escHtml(SJ_STATUS_LABELS[k] || k) + '</option>';
    });
    $('#statusSelect').html(html);
}

function updateStopHintAndBanner() {
    const sorted = getValidStopsSorted();
    const pending = nextPendingStopId();
    const m = currentMovement || {};
    const st = String(m.status || '').toUpperCase();
    const $banner = $('#sjFlowBanner');
    const $btn = $('#btnSjSubmit');

    if (!sorted.length) {
        $('#sjStopHint').text('Tidak ada data rute — hubungi gudang.');
        $banner.addClass('d-none').empty();
        $btn.prop('disabled', true);
        return;
    }

    if (!pending) {
        $banner.removeClass('d-none').removeClass('alert-warning').addClass('alert-success')
            .html('<i class="fas fa-check-double me-2"></i><strong>Rute lengkap.</strong> Semua titik sudah dikonfirmasi. Tidak perlu simpan lagi kecuali gudang meminta koreksi.');
        $('#sjStopHint').text('Semua titik rute sudah memiliki konfirmasi.');
        $btn.prop('disabled', true);
        return;
    }

    $banner.addClass('d-none').empty();
    $btn.prop('disabled', false);

    const pend = stopById(pending);
    const pendLabel = pend ? ('[' + pend.sequence_no + '] ' + (pend.stop_type || '') + ' — ' + (pend.location_name || '-')) : '';
    const sel = $('#stopSelect').val();
    let hint = '<strong>Alur satu langkah:</strong> saat ini hanya titik <strong>berikutnya</strong> pada rute yang bisa dikonfirmasi. ';
    hint += 'Jenis konfirmasi otomatis disesuaikan (Berangkat di asal, Sampai di tujuan akhir, Transit di tengah). ';
    if (pending && sel === pending) {
        hint += '<br><span class="text-success">Langkah aktif:</span> ' + escHtml(pendLabel) + '.';
    } else if (pending) {
        hint += '<br><span class="text-warning">Peringatan:</span> pilih titik ' + escHtml(pendLabel) + ' — itu satu-satunya opsi yang aktif.';
    }
    $('#sjStopHint').html(hint);
}

function applyDefaultStopAndStatus() {
    const pending = nextPendingStopId();
    if (pending) {
        $('#stopSelect').val(pending);
    }
    const sid = $('#stopSelect').val();
    if (sid) {
        rebuildStatusSelect(sid);
    }
    updateStopHintAndBanner();
}

function lookupSJ() {
    const sj = ($('#sjNumber').val() || '').trim();
    const code = ($('#verifyCode').val() || '').trim();
    if (!sj) {
        showMsg('warning', 'Nomor surat jalan wajib diisi.');
        return;
    }
    $.get(lookupUrl, { surat_jalan_number: sj, verification_code: code }, function(res) {
        if (!res.success) {
            $('#detailCard').addClass('d-none');
            showMsg('danger', res.message || 'Data tidak ditemukan');
            return;
        }
        currentMovement = res.data.movement || {};
        currentStops = res.data.stops || [];
        currentItems = res.data.items || [];
        currentCheckpoints = res.data.checkpoints || [];
        currentStops.forEach(function(st) {
            if (!hasStopActualAt(st)) {
                st.actual_at = null;
            }
        });
        renderDetail();
        showMsg('success', 'Data surat jalan ditemukan. Periksa titik rute (otomatis ke langkah berikutnya), checklist barang, lalu simpan.');
    }).fail(function(xhr) {
        $('#detailCard').addClass('d-none');
        const t = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Gagal memuat data surat jalan.';
        showMsg('danger', t);
    });
}

function purposeLabel(p) {
    const u = (p || '').toUpperCase();
    if (u === 'SCRAP_SALE') return 'Keluar jual scrab';
    return 'Pindah / internal';
}

function statusBadge(status) {
    const u = (status || '').toUpperCase();
    const map = {
        'DRAFT': 'secondary',
        'IN_TRANSIT': 'warning',
        'ARRIVED': 'success',
        'CANCELLED': 'danger'
    };
    const cls = map[u] || 'secondary';
    const label = u.replace(/_/g, ' ') || '-';
    return '<span class="badge text-bg-' + cls + '">' + escHtml(label) + '</span>';
}

function formatDt(iso) {
    if (!iso) return '-';
    const d = new Date(iso);
    return Number.isNaN(d.getTime()) ? escHtml(String(iso)) : escHtml(d.toLocaleString('id-ID'));
}

function renderDetail() {
    if (!currentMovement) return;
    $('#detailCard').removeClass('d-none');

    const m = currentMovement;
    const driver = (m.driver_name || '').trim() || '—';
    const vehicle = (m.vehicle_number || '').trim() || '—';
    const notesH = (m.notes || '').trim() || '—';

    let meta = '<div class="sj-detail-grid">';
    meta += '<div class="sj-kv"><div class="k">No. SJ</div><div class="v">' + escHtml(m.surat_jalan_number || '-') + '</div></div>';
    meta += '<div class="sj-kv"><div class="k">No. Movement</div><div class="v">' + escHtml(m.movement_number || '-') + '</div></div>';
    meta += '<div class="sj-kv"><div class="k">Tipe</div><div class="v">' + escHtml(purposeLabel(m.movement_purpose)) + '</div></div>';
    meta += '<div class="sj-kv"><div class="k">Status sistem</div><div class="v">' + statusBadge(m.status) + '</div></div>';
    meta += '<div class="sj-kv"><div class="k">Driver (dari gudang)</div><div class="v">' + escHtml(driver) + '</div></div>';
    meta += '<div class="sj-kv"><div class="k">No. kendaraan</div><div class="v">' + escHtml(vehicle) + '</div></div>';
    meta += '<div class="sj-kv" style="grid-column: 1 / -1"><div class="k">Catatan gudang</div><div class="v fw-normal">' + escHtml(notesH) + '</div></div>';
    meta += '</div>';
    $('#sjMeta').html(meta);

    const o = m.origin_location || '-';
    const d = m.destination_location || '-';
    $('#sjRoute').html(
        '<div class="k text-muted small text-uppercase mb-1">Rute</div>' +
        '<div class="sj-route-pill"><span>' + escHtml(o) + '</span> <i class="fas fa-arrow-right small opacity-50"></i> <span>' + escHtml(d) + '</span></div>'
    );

    if (Array.isArray(currentCheckpoints) && currentCheckpoints.length > 0) {
        let log = '<div class="fw-semibold text-dark mb-1"><i class="fas fa-history me-1"></i>Riwayat konfirmasi</div><ul class="mb-0 ps-3">';
        currentCheckpoints.forEach(function(c) {
            const st = (c.checkpoint_status || '').replace(/_/g, ' ');
            log += '<li>' + formatDt(c.checkpoint_at) + ' — <strong>' + escHtml(st) + '</strong>';
            if (c.verifier_name) log += ' — ' + escHtml(c.verifier_name);
            log += '</li>';
        });
        log += '</ul>';
        $('#sjCheckpointLog').html(log).removeClass('d-none');
    } else {
        $('#sjCheckpointLog').empty().addClass('d-none');
    }

    const sorted = getValidStopsSorted();
    let opt = '';
    let hasValidStop = sorted.length > 0;
    const pendingId = nextPendingStopId();

    if (hasValidStop && !pendingId) {
        opt = '<option value="">— Semua titik rute sudah dikonfirmasi —</option>';
    } else if (hasValidStop) {
        sorted.forEach(function(s) {
            const done = hasStopActualAt(s);
            const isNext = String(s.id) === String(pendingId) && pendingId !== '';
            const dis = isNext && !done ? '' : ' disabled';
            let tag = '';
            if (done) {
                tag = ' (selesai)';
            } else if (!isNext) {
                tag = ' (nanti — urut rute)';
            } else {
                tag = ' ← langkah Anda';
            }
            opt += '<option value="' + escHtml(String(s.id)) + '"' + dis + '>[' + escHtml(String(s.sequence_no)) + '] ' +
                escHtml((s.stop_type || '-') + ' — ' + (s.location_name || '-')) + escHtml(tag) + '</option>';
        });
    }
    if (!hasValidStop) {
        $('#stopSelect').html('<option value="">— Tidak ada rute di sistem — hubungi gudang</option>');
    } else {
        $('#stopSelect').html(opt);
    }

    renderItemsChecklist();

    $('#stopSelect').off('change.sjguard').on('change.sjguard', function() {
        const v = $(this).val();
        if (v) {
            rebuildStatusSelect(v);
        }
        updateStopHintAndBanner();
    });

    applyDefaultStopAndStatus();
}

function renderItemsChecklist() {
    if (!Array.isArray(currentItems) || currentItems.length === 0) {
        $('#itemsChecklist').html('<div class="text-muted small">Tidak ada data barang.</div>');
        return;
    }
    let html = '';
    currentItems.forEach(function(it, idx) {
        const itemId = it.id || '';
        const qty = it.qty || 1;
        const desc = it.print_description || (it.component_type || '-');
        const title = '#' + (idx + 1) + ' — ' + desc + ' (qty: ' + qty + ')';
        if (!itemId) {
            html += '<div class="text-muted small mb-2">' + escHtml(title) + ' <em>(baris tanpa ID — checklist tidak tersedia)</em></div>';
            return;
        }
        const idStr = String(itemId);
        const rid = escHtml(idStr);
        html += '<div class="sj-item-card">';
        html += '<div class="sj-item-title">' + escHtml(title) + '</div>';
        html += '<div class="row g-2 align-items-start">';
        html += '<div class="col-12 col-sm-4">';
        html += '<div class="form-check">';
        html += '<input class="form-check-input sj-item-choice" type="radio" name="sj_item_' + rid + '" id="sj_item_' + rid + '_skip" value="skip" checked data-item-id="' + rid + '">';
        html += '<label class="form-check-label text-muted" for="sj_item_' + rid + '_skip">Tidak untuk barang ini</label>';
        html += '</div></div>';
        html += '<div class="col-12 col-sm-4">';
        html += '<div class="form-check">';
        html += '<input class="form-check-input sj-item-choice" type="radio" name="sj_item_' + rid + '" id="sj_item_' + rid + '_arr" value="arrived" data-item-id="' + rid + '">';
        html += '<label class="form-check-label text-success" for="sj_item_' + rid + '_arr"><strong>Lewat gerbang</strong> (ikut jalan)</label>';
        html += '</div></div>';
        html += '<div class="col-12 col-sm-4">';
        html += '<div class="form-check">';
        html += '<input class="form-check-input sj-item-choice" type="radio" name="sj_item_' + rid + '" id="sj_item_' + rid + '_drop" value="drop" data-item-id="' + rid + '">';
        html += '<label class="form-check-label" for="sj_item_' + rid + '_drop"><strong class="text-warning">Drop di titik ini</strong></label>';
        html += '</div></div>';
        html += '</div></div>';
    });
    $('#itemsChecklist').html(html);
}

function collectItemChoices() {
    const checkedItemIds = [];
    const droppedItemIds = [];
    $('.sj-item-choice:checked').each(function() {
        const v = $(this).val();
        const id = $(this).attr('data-item-id');
        if (v === 'arrived' && id) {
            checkedItemIds.push(id);
        }
        if (v === 'drop' && id) {
            droppedItemIds.push(id);
        }
    });
    return { checkedItemIds: checkedItemIds, droppedItemIds: droppedItemIds };
}

function submitCheckpoint() {
    if (!currentMovement) {
        showMsg('warning', 'Cari nomor surat jalan terlebih dahulu.');
        return;
    }
    const stopVal = $('#stopSelect').val();
    if (!stopVal) {
        showMsg('warning', 'Pilih lokasi rute yang valid.');
        return;
    }
    const nextId = nextPendingStopId();
    if (nextId && String(stopVal) !== String(nextId)) {
        showMsg('warning', 'Konfirmasi harus pada titik berikutnya dalam urutan rute saja.');
        return;
    }
    const opt = $('#stopSelect option:selected');
    if (opt.length && opt.is(':disabled')) {
        showMsg('warning', 'Titik ini tidak aktif. Muat ulang data lalu ikuti langkah berikutnya pada rute.');
        return;
    }
    const vName = ($('#verifierName').val() || '').trim();
    if (!vName) {
        showMsg('warning', 'Nama petugas wajib diisi.');
        return;
    }
    const choices = collectItemChoices();
    if (choices.checkedItemIds.length === 0 && choices.droppedItemIds.length === 0) {
        showMsg('warning', 'Checklist: untuk minimal satu barang pilih &ldquo;Lewat gerbang&rdquo; atau &ldquo;Drop di titik ini&rdquo;.');
        return;
    }

    const pair = csrfPair();
    const data = {
        movement_id: currentMovement.id,
        stop_id: stopVal,
        status: $('#statusSelect').val(),
        verifier_name: vName,
        verifier_phone: $('#verifierPhone').val(),
        notes: $('#notes').val(),
        verification_code: $('#verifyCode').val(),
        checkpoint_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
        checked_item_ids: choices.checkedItemIds,
        dropped_item_ids: choices.droppedItemIds
    };
    if (pair.name) {
        data[pair.name] = pair.value;
    }

    $.post(checkpointUrl, data, function(res) {
        if (!res.success) {
            showMsg('danger', res.message || 'Gagal menyimpan.');
            return;
        }
        showMsg('success', res.message || 'Konfirmasi tersimpan. Data ini tercatat di sistem yang sama dengan aplikasi gudang.');
        lookupSJ();
    }).fail(function(xhr) {
        let msg = 'Terjadi kesalahan pada sistem.';
        try {
            const j = xhr.responseJSON;
            if (j && j.message) {
                msg = j.message;
            }
        } catch (e) { /* ignore */ }
        if (xhr.status === 403 && xhr.responseText && xhr.responseText.indexOf('security') !== -1) {
            msg = 'Sesi keamanan habis — muat ulang halaman lalu coba lagi.';
        }
        showMsg('danger', msg);
    });
}

// Deep link support: autofill dari URL lalu auto-cari.
$(function() {
    try {
        const p = new URLSearchParams(window.location.search || '');
        const sj = (p.get('surat_jalan_number') || p.get('suratJalanNumber') || '').trim();
        const code = (p.get('verification_code') || p.get('verificationCode') || '').trim();
        if (sj) {
            $('#sjNumber').val(sj);
        }
        if (code) {
            $('#verifyCode').val(code);
        }
        if (sj && code) {
            lookupSJ();
        }
    } catch (e) {
        // abaikan jika browser lama / URLSearchParams tidak tersedia
    }
});
</script>
</body>
</html>
