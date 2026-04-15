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
    <link rel="icon" href="<?= esc(base_url('favicon.ico')) ?>" type="image/x-icon">
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
        .sj-delivery-summary {
            background: #fff;
            border: 1px solid var(--sj-line);
            border-radius: 8px;
            padding: .75rem 1rem;
            font-size: .9rem;
        }
        .sj-delivery-summary .sj-dl-row {
            display: grid;
            grid-template-columns: 10.5rem 1fr;
            gap: .25rem .75rem;
            margin-bottom: .35rem;
        }
        .sj-delivery-summary .sj-dl-row:last-child { margin-bottom: 0; }
        .sj-delivery-summary dt {
            margin: 0;
            color: #64748b;
            font-weight: 600;
            font-size: .78rem;
        }
        .sj-delivery-summary dd { margin: 0; color: #1e293b; }
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
        .sj-checklist-help summary.sj-checklist-help-summary {
            cursor: pointer;
            color: #475569;
            font-weight: 600;
        }
        .sj-checklist-help[open] summary.sj-checklist-help-summary { margin-bottom: .25rem; }
        /* Titik rute aktif (tanpa teks "langkah Anda" di opsi) */
        .sj-stop-select-wrap.sj-stop-active .form-select {
            background-color: #ecfdf5;
            border-color: #6ee7b7;
            box-shadow: 0 0 0 1px rgba(16, 185, 129, 0.15);
        }
        .sj-stop-hint {
            font-size: .8125rem;
            color: #475569;
            line-height: 1.45;
            max-width: 100%;
        }
        .sj-flow-banner {
            font-size: .875rem;
            line-height: 1.45;
            border-radius: 12px;
            padding: .85rem 1rem;
            border-left: 4px solid #0ea5e9;
        }
        .sj-flow-banner.alert-warning {
            border-left-color: #f59e0b;
        }
        .sj-flow-banner.alert-success {
            border-left-color: #22c55e;
        }
        .sj-flow-banner-title {
            font-weight: 700;
            font-size: .8125rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #0369a1;
            margin-bottom: .4rem;
        }
        .sj-flow-banner.alert-warning .sj-flow-banner-title { color: #b45309; }
        .sj-flow-banner.alert-success .sj-flow-banner-title { color: #15803d; }
        .sj-flow-banner-step {
            font-weight: 600;
            color: #0f172a;
            font-size: .9375rem;
            word-break: break-word;
        }
        .sj-route-block {
            background: #f8fafc;
            border: 1px solid var(--sj-line);
            border-radius: 12px;
            padding: 1rem 1.1rem;
            margin-top: .25rem;
        }
        .sj-route-block .form-label { font-size: .9rem; }
        .sj-checklist-actions .btn {
            min-height: 44px;
            padding-left: .85rem;
            padding-right: .85rem;
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
        #checkpointFeedback.alert {
            border-radius: 10px;
            font-size: .9rem;
        }
        @media (max-width: 576px) {
            .sj-brand-bar { flex-direction: column; text-align: center; padding: 1rem 1rem; }
            .sj-brand-bar img { height: 40px; }
            .sj-card .card-body-inner { padding: 1rem 0.85rem 1.15rem; }
            .sj-detail-panel { padding: 0.85rem 0.9rem; }
            .sj-detail-grid {
                grid-template-columns: 1fr;
            }
            .sj-delivery-summary .sj-dl-row {
                grid-template-columns: 1fr;
                gap: 0.1rem 0;
            }
            .sj-delivery-summary .sj-dl-row dt { margin-bottom: 0; }
            .sj-route-block { padding: 0.85rem 0.9rem; }
            #stopSelect,
            #statusSelect {
                min-height: 3rem;
                font-size: 1rem;
                padding-top: 0.55rem;
                padding-bottom: 0.55rem;
            }
            .sj-item-card .form-check {
                padding-top: 0.35rem;
                padding-bottom: 0.35rem;
            }
            .sj-item-card .form-check-input {
                width: 1.15em;
                height: 1.15em;
                margin-top: 0.2em;
            }
            .sj-form-actions .btn-success {
                min-height: 48px;
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
<div class="container px-2 px-sm-3 py-3 py-md-4 sj-shell">
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
                <div class="sj-section-title mt-3 mb-2">Konfirmasi detail pengiriman</div>
                <div class="alert alert-light border mb-0" id="sjDeliveryConfirmBlock">
                    <p class="small text-muted mb-2 mb-md-3">
                        Data di bawah dari <strong>gudang</strong>. Jika di gerbang <strong>berbeda</strong>, pilih &ldquo;Tidak sesuai&rdquo; lalu isi ulang sesuai kendaraan yang Anda lihat (wajib lengkap).
                    </p>
                    <div id="sjDeliverySummary" class="sj-delivery-summary mb-3" aria-live="polite"></div>
                    <div class="mb-0">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="deliveryMatch" id="deliveryMatchYes" value="match" checked autocomplete="off">
                            <label class="form-check-label" for="deliveryMatchYes"><strong>Sesuai</strong> — driver, plat, dan keterangan sama dengan yang lewat gerbang</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="deliveryMatch" id="deliveryMatchNo" value="mismatch" autocomplete="off">
                            <label class="form-check-label" for="deliveryMatchNo"><strong>Tidak sesuai</strong> — isi ulang data sebenarnya di bawah (wajib)</label>
                        </div>
                    </div>
                    <div id="sjDeliveryMismatchWrap" class="d-none border-top pt-3 mt-3">
                        <div class="small fw-semibold text-dark mb-2">Data di lapangan (ganti data gudang)</div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label small mb-0" for="deliveryActualDriver">Driver <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="deliveryActualDriver" maxlength="200" placeholder="Nama driver yang lewat" autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-0" for="deliveryActualVehicle">No. kendaraan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="deliveryActualVehicle" maxlength="80" placeholder="Contoh: B 1234 CDE" autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-0" for="deliveryActualVehicleType">Jenis kendaraan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="deliveryActualVehicleType" maxlength="120" placeholder="Misal: box, pick up, trailer" autocomplete="off">
                            </div>
                            <div class="col-12">
                                <label class="form-label small mb-0" for="deliveryActualReason">Alasan / keterangan (lapangan) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="deliveryActualReason" maxlength="500" placeholder="Singkat: kenapa beda atau apa yang benar" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div id="sjCheckpointLog" class="sj-cp-log d-none mt-3"></div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Checklist barang dibawa</label>
                <p class="small text-muted mb-2">
                    Satu pilihan per barang untuk <strong>titik rute</strong> di bawah. Semua ikut lewat gerbang? <strong>Semua lewat gerbang</strong>. Semua diturunkan di titik ini? <strong>Drop semua barang</strong>.
                    <span class="d-inline-flex flex-wrap gap-1 ms-0 ms-sm-1 mt-1 mt-sm-0 align-items-center sj-checklist-actions">
                        <button type="button" class="btn btn-sm btn-outline-success" id="btnSjAllPass" title="Semua barang = barang ada (lewat gerbang)">Semua lewat gerbang</button>
                        <button type="button" class="btn btn-sm btn-outline-warning" id="btnSjAllDrop" title="Semua barang = drop di titik ini">Drop semua barang</button>
                    </span>
                </p>
                <details class="small text-muted mb-2 sj-checklist-help">
                    <summary class="sj-checklist-help-summary">Penjelasan singkat</summary>
                    <ul class="ps-3 mb-0 mt-1">
                        <li><strong>Barang tidak ada dalam pengiriman</strong> — barang tidak ikut di kendaraan / tidak relevan di titik Anda.</li>
                        <li><strong>Barang ada</strong> — barang ikut lewat gerbang dan lanjut ke titik berikutnya (masih di muatan / lewat tanpa diturunkan di titik Anda).</li>
                        <li><strong>Drop barang</strong> — barang diturunkan atau diterima di lokasi Anda; tidak lanjut rute.</li>
                    </ul>
                    <p class="mb-0 mt-2 small"><strong>Contoh di gerbang tujuan / transit:</strong> yang <em>diturunkan</em> di pos Anda → <strong>Drop barang</strong>. Yang <em>tetap di kendaraan</em> dan tidak Anda terima di sini → <strong>Barang ada</strong>.</p>
                    <p class="mb-0 mt-1"><em>Wajib:</em> isi checklist sesuai titik rute (aturan opsi berbeda untuk berangkat, transit, dan tujuan akhir — lihat petunjuk di bawah).</p>
                </details>
                <p class="small text-muted mb-2" id="sjChecklistModeHint"></p>
                <div id="itemsChecklist" class="sj-checklist"></div>
            </div>

            <div id="sjFlowBanner" class="alert alert-light border sj-flow-banner d-none mb-3" role="status"></div>

            <div class="sj-route-block">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" for="stopSelect">Titik rute</label>
                        <div id="stopSelectWrap" class="sj-stop-select-wrap rounded">
                            <select id="stopSelect" class="form-select" aria-describedby="sjStopHint"></select>
                        </div>
                        <div class="form-text sj-stop-hint mt-1" id="sjStopHint">Setelah data SJ dimuat, petunjuk singkat muncul di sini.</div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" for="statusSelect">Jenis konfirmasi</label>
                        <select id="statusSelect" class="form-select" aria-describedby="sjStatusHint"></select>
                        <div class="form-text sj-stop-hint mt-1" id="sjStatusHint">Mengikuti titik rute yang dipilih.</div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <label class="form-label">Nama petugas <span class="text-danger">*</span></label>
                    <input id="verifierName" class="form-control" placeholder="Nama lengkap" autocomplete="name">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="verifierPhone">No. HP petugas</label>
                    <input id="verifierPhone" class="form-control" type="tel" inputmode="tel" autocomplete="tel" maxlength="20" placeholder="Contoh: 081234567890" aria-describedby="verifierPhoneHint">
                    <div class="form-text" id="verifierPhoneHint">Opsional. Format Indonesia: 08… (10–13 digit) atau +62 8…</div>
                </div>
            </div>
            <div class="mt-2">
                <label class="form-label">Alasan <span class="text-muted">(opsional)</span></label>
                <textarea id="notes" class="form-control" rows="2" placeholder="Opsional: misal jam keluar gerbang, plat terlihat, kondisi muatan, dll."></textarea>
            </div>

            <div id="checkpointFeedback" class="mt-3 d-none" role="status" aria-live="polite"></div>

            <div class="sj-form-actions mt-4 d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-success" id="btnSjSubmit" onclick="submitCheckpoint()">
                    <i class="fas fa-check-circle me-2"></i>Simpan konfirmasi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal sukses: OK → reload + hapus SJ & kode dari layar -->
<div class="modal fade" id="sjSuccessModal" tabindex="-1" aria-labelledby="sjSuccessModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-success" id="sjSuccessModalLabel"><i class="fas fa-circle-check me-2"></i>Konfirmasi berhasil</h5>
            </div>
            <div class="modal-body pt-2 text-center">
                <p class="mb-0" id="sjSuccessModalBody">Konfirmasi berhasil. Data telah disimpan.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-primary px-4" id="sjSuccessModalOk">OK</button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="<?= esc(csrf_token()) ?>" id="csrfField" value="<?= esc(csrf_hash()) ?>">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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

/** Teks titik rute untuk tampilan (nilai DB tetap ORIGIN / TRANSIT / DESTINATION). */
function stopTypeLabelId(code) {
    const u = String(code || '').toUpperCase();
    if (u === 'ORIGIN') return 'Asal';
    if (u === 'DESTINATION') return 'Tujuan';
    if (u === 'TRANSIT') return 'Transit';
    return String(code || '').trim() || '-';
}

/** Label tipe lokasi (selaras form gudang / cetak SJ). */
function locationTypeLabelId(code) {
    const u = String(code || '').toUpperCase();
    const map = {
        POS_1: 'POS 1 (Workshop Utama)',
        POS_2: 'POS 2',
        POS_3: 'POS 3',
        POS_4: 'POS 4',
        POS_5: 'POS 5',
        WAREHOUSE: 'Gudang',
        CUSTOMER_SITE: 'Lokasi Customer',
        OTHER: 'Lainnya'
    };
    return map[u] || (String(code || '').trim() || '');
}

function routeLocationWithType(loc, typeCode) {
    const l = String(loc || '').trim() || '—';
    const t = locationTypeLabelId(typeCode);
    return t ? (l + ' (' + t + ')') : l;
}

/** Tipe lokasi singkat untuk dropdown (POS 5, Gudang, …). */
function locationTypeShort(code) {
    const u = String(code || '').toUpperCase();
    if (u.indexOf('POS_') === 0) {
        return 'POS ' + u.replace('POS_', '');
    }
    return locationTypeLabelId(code) || '';
}

/** Satu baris opsi dropdown — singkat: [no] peran · lokasi · tipe (+ tag status). */
function formatStopSelectOptionText(s, tagSuffix) {
    const seq = String(s.sequence_no != null ? s.sequence_no : '');
    const role = stopTypeLabelId(s.stop_type);
    const loc = String(s.location_name || '').trim() || '—';
    const typ = locationTypeShort(s.location_type);
    const tail = typ ? (' · ' + typ) : '';
    return '[' + seq + '] ' + role + ' · ' + loc + tail + (tagSuffix || '');
}

function showCheckpointFeedback(ok, message) {
    const $fb = $('#checkpointFeedback');
    const cls = ok ? 'alert-success' : 'alert-danger';
    const icon = ok ? 'fa-circle-check' : 'fa-circle-xmark';
    const title = ok ? 'Konfirmasi berhasil' : 'Konfirmasi gagal';
    const body = message || (ok ? 'Data tersimpan.' : 'Periksa pesan lalu coba lagi.');
    $fb.removeClass('d-none alert-success alert-danger').addClass('alert ' + cls)
        .html(
            '<div class="d-flex gap-2 align-items-start">' +
            '<i class="fas ' + icon + ' fa-lg mt-1"></i>' +
            '<div><strong>' + escHtml(title) + '</strong><div class="mt-1">' + escHtml(body) + '</div></div>' +
            '</div>'
        );
    try {
        $fb[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } catch (e) { /* ignore */ }
}

function clearCheckpointFeedback() {
    $('#checkpointFeedback').addClass('d-none').removeClass('alert-success alert-danger').empty();
}

/** Setelah sukses: kosongkan form sensitif, hapus query string SJ, lalu reload. */
function sjSuccessCleanupAndReload() {
    $('#sjNumber').val('');
    $('#verifyCode').val('');
    $('#verifierName').val('');
    $('#verifierPhone').val('');
    $('#notes').val('');
    currentMovement = null;
    currentStops = [];
    currentItems = [];
    currentCheckpoints = [];
    $('#detailCard').addClass('d-none');
    clearCheckpointFeedback();
    $('#msgBox').empty();
    try {
        if (window.history && window.history.replaceState) {
            window.history.replaceState(null, '', window.location.pathname);
        }
    } catch (e) { /* ignore */ }
    window.location.reload();
}

function showSjSuccessModal(message) {
    const msg = message || 'Konfirmasi berhasil. Data telah disimpan.';
    $('#sjSuccessModalBody').text(msg);
    const el = document.getElementById('sjSuccessModal');
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        bootstrap.Modal.getOrCreateInstance(el).show();
    } else {
        window.alert(msg);
        sjSuccessCleanupAndReload();
    }
}

function csrfPair() {
    const el = document.getElementById('csrfField');
    if (!el) return {};
    return { name: el.getAttribute('name'), value: el.value };
}

/** Normalisasi ke bentuk 08… untuk validasi & kirim server (HP Indonesia). */
function normalizeVerifierPhone(raw) {
    let s = String(raw || '').trim().replace(/[\s.-]/g, '');
    if (!s) {
        return '';
    }
    if (s.startsWith('+62')) {
        s = '0' + s.slice(3);
    } else if (s.startsWith('62') && s.length > 2 && s.charAt(2) === '8') {
        s = '0' + s.slice(2);
    } else if (s.charAt(0) === '8' && !s.startsWith('08')) {
        s = '0' + s;
    }
    return s;
}

/** Kosong = valid (opsional). Isi harus pola HP Indonesia. */
function isValidIndoMobileInput(raw) {
    const n = normalizeVerifierPhone(raw);
    if (!n) {
        return true;
    }
    return /^08[1-9]\d{7,11}$/.test(n);
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

/**
 * Pola checklist barang per posisi rute (selaras dengan BERANGKAT / TRANSIT / SAMPAI).
 * - departure: tidak ada Drop (belum turun muatan di asal)
 * - arrival: tidak ada Barang ada (di tujuan akhir muatan harus turun atau tidak relevan)
 * - transit: ketiga opsi
 */
function stopItemChoiceMode(stopId) {
    if (!stopId) {
        return 'transit';
    }
    const allowed = allowedStatusesForStopId(stopId);
    const k = String(allowed[0] || 'TRANSIT').toUpperCase();
    if (k === 'BERANGKAT') {
        return 'departure';
    }
    if (k === 'SAMPAI') {
        return 'arrival';
    }
    return 'transit';
}

function updateChecklistShortcutsAndHint(mode) {
    const $pass = $('#btnSjAllPass');
    const $drop = $('#btnSjAllDrop');
    if (mode === 'departure') {
        $pass.removeClass('d-none');
        $drop.addClass('d-none');
    } else if (mode === 'arrival') {
        $pass.addClass('d-none');
        $drop.removeClass('d-none');
    } else {
        $pass.removeClass('d-none');
        $drop.removeClass('d-none');
    }
    const $hint = $('#sjChecklistModeHint');
    if (!$hint.length) {
        return;
    }
    if (mode === 'departure') {
        $hint.text('Titik berangkat: opsi Drop tidak digunakan — pilih barang yang ikut (Barang ada) atau tidak ikut muatan.');
    } else if (mode === 'arrival') {
        $hint.text('Titik tujuan akhir: opsi Barang ada tidak digunakan — pilih Drop jika diterima/diturunkan di sini, atau tidak ada dalam pengiriman.');
    } else {
        $hint.text('Titik transit: ketiga opsi tersedia (lewat, turun di sini, atau tidak ikut muatan).');
    }
}

/** Isi ulang dropdown jenis konfirmasi — teks mengikuti lokasi & tipe titik (mis. Berangkat dari GUDANG — POS 1). */
function rebuildStatusSelect(stopId) {
    const allowed = allowedStatusesForStopId(stopId);
    const s = stopById(stopId);
    const locDisp = (s && String(s.location_name || '').trim()) ? String(s.location_name).trim() : 'titik ini';
    const ltShort = s ? locationTypeShort(s.location_type) : '';
    const mid = ltShort ? (' — ' + ltShort) : '';
    let html = '';
    allowed.forEach(function(k) {
        let label = k;
        if (k === 'BERANGKAT') {
            label = 'Berangkat dari ' + locDisp + mid;
        } else if (k === 'TRANSIT') {
            label = 'Transit di ' + locDisp + mid;
        } else if (k === 'SAMPAI') {
            label = 'Sampai di ' + locDisp + mid;
        }
        html += '<option value="' + escHtml(k) + '">' + escHtml(label) + '</option>';
    });
    $('#statusSelect').html(html);
    refreshStatusHint();
}

/** Satu baris: jenis konfirmasi yang akan ikut tersimpan (mengikuti titik). */
function refreshStatusHint() {
    const t = ($('#statusSelect option:selected').text() || '').trim();
    $('#sjStatusHint').text(t ? 'Akan tersimpan sebagai: ' + t + '.' : 'Pilih titik rute terlebih dahulu.');
}

/** Hijau lembut di dropdown saat titik yang dipilih = titik berikutnya yang wajib dikonfirmasi. */
function refreshStopSelectHighlight() {
    const pending = nextPendingStopId();
    const v = $('#stopSelect').val();
    const $w = $('#stopSelectWrap');
    if (!$w.length) {
        return;
    }
    if (pending && v && String(v) === String(pending)) {
        $w.addClass('sj-stop-active');
    } else {
        $w.removeClass('sj-stop-active');
    }
}

function updateStopHintAndBanner() {
    const sorted = getValidStopsSorted();
    const pending = nextPendingStopId();
    const m = currentMovement || {};
    const st = String(m.status || '').toUpperCase();
    const $banner = $('#sjFlowBanner');
    const $btn = $('#btnSjSubmit');

    if (!sorted.length) {
        $('#stopSelectWrap').removeClass('sj-stop-active');
        $('#sjStopHint').text('Tidak ada data rute — hubungi gudang.');
        $('#sjStatusHint').text('—');
        $banner.removeClass('alert-info alert-success alert-warning').addClass('alert-light border d-none').empty();
        $btn.prop('disabled', true);
        return;
    }

    if (!pending) {
        $('#stopSelectWrap').removeClass('sj-stop-active');
        $banner.removeClass('d-none alert-light alert-warning alert-info').addClass('alert-success border')
            .html(
                '<div class="sj-flow-banner-title"><i class="fas fa-check-double me-1"></i>Rute selesai</div>' +
                '<p class="mb-0">Semua titik sudah punya konfirmasi. Tidak perlu simpan lagi kecuali gudang meminta ulang.</p>'
            );
        $('#sjStopHint').text('Semua titik pada rute ini sudah dikonfirmasi.');
        $('#sjStatusHint').text('—');
        $btn.prop('disabled', true);
        return;
    }

    $btn.prop('disabled', false);

    const pend = stopById(pending);
    const pendLabel = pend ? formatStopSelectOptionText(pend, '') : '';
    const sel = $('#stopSelect').val();

    const title = '<div class="sj-flow-banner-title"><i class="fas fa-location-dot me-1"></i>Giliran titik ini</div>';
    const body = '<p class="mb-0">Konfirmasi <strong>satu titik</strong> per simpanan, mengikuti urutan rute. Jenis konfirmasi di bawah sudah disesuaikan dengan titik tersebut.</p>';
    const step = '<p class="mb-0 mt-2 sj-flow-banner-step">Sekarang: ' + escHtml(pendLabel) + '</p>';

    if (pending && String(sel) !== String(pending)) {
        $banner.removeClass('d-none alert-light alert-success alert-info').addClass('alert-warning border')
            .html(title + body + step + '<p class="mb-0 mt-2 small">Ubah dropdown &ldquo;Titik rute&rdquo; agar sama dengan baris &ldquo;Sekarang&rdquo;.</p>');
        $('#sjStopHint').text('Pilih titik yang sama dengan kotak kuning di atas (bukan titik lain).');
    } else {
        $banner.removeClass('d-none alert-light alert-success alert-warning').addClass('alert-info border')
            .html(title + body + step);
        $('#sjStopHint').text('Hanya titik berikutnya pada rute yang bisa dipilih. Latar hijau lembut = titik yang sedang giliran.');
    }
    refreshStopSelectHighlight();
    refreshStatusHint();
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

function lookupSJ(opts) {
    opts = opts || {};
    const sj = ($('#sjNumber').val() || '').trim();
    const code = ($('#verifyCode').val() || '').trim();
    if (!sj) {
        showMsg('warning', 'Nomor surat jalan wajib diisi.');
        return;
    }
    if (!opts.skipClearFeedback) {
        clearCheckpointFeedback();
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
        if (!opts.skipTopMsg) {
            showMsg('success', 'Data surat jalan ditemukan. Periksa titik rute (otomatis ke langkah berikutnya), checklist barang, lalu simpan.');
        }
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
    const vehicleType = (m.vehicle_type || '').trim() || '—';
    const recipient = (m.destination_recipient_name || '').trim() || '—';
    const notesH = (m.notes || '').trim() || '—';

    let meta = '<div class="sj-detail-grid">';
    meta += '<div class="sj-kv"><div class="k">No. SJ</div><div class="v">' + escHtml(m.surat_jalan_number || '-') + '</div></div>';
    meta += '<div class="sj-kv"><div class="k">No. Movement</div><div class="v">' + escHtml(m.movement_number || '-') + '</div></div>';
    meta += '<div class="sj-kv"><div class="k">Tipe</div><div class="v">' + escHtml(purposeLabel(m.movement_purpose)) + '</div></div>';
    meta += '<div class="sj-kv"><div class="k">Status sistem</div><div class="v">' + statusBadge(m.status) + '</div></div>';
    meta += '</div>';
    $('#sjMeta').html(meta);

    const sum =
        '<dl class="mb-0">' +
        '<div class="sj-dl-row"><dt>Driver</dt><dd>' + escHtml(driver) + '</dd></div>' +
        '<div class="sj-dl-row"><dt>No. kendaraan</dt><dd>' + escHtml(vehicle) + '</dd></div>' +
        '<div class="sj-dl-row"><dt>Jenis kendaraan</dt><dd>' + escHtml(vehicleType) + '</dd></div>' +
        '<div class="sj-dl-row"><dt>Penerima barang</dt><dd>' + escHtml(recipient) + '</dd></div>' +
        '<div class="sj-dl-row"><dt>Alasan</dt><dd class="fw-normal">' + escHtml(notesH) + '</dd></div>' +
        '</dl>';
    $('#sjDeliverySummary').html(sum);
    $('#deliveryMatchYes').prop('checked', true);
    $('#deliveryMatchNo').prop('checked', false);
    $('#deliveryActualDriver, #deliveryActualVehicle, #deliveryActualVehicleType, #deliveryActualReason').val('');
    $('#sjDeliveryMismatchWrap').addClass('d-none');
    syncDeliveryMismatchUi();

    let oType = m.origin_type;
    let dType = m.destination_type;
    const sortedForRoute = getValidStopsSorted();
    if (sortedForRoute.length >= 1 && (!oType || String(oType).trim() === '')) {
        oType = sortedForRoute[0].location_type;
    }
    if (sortedForRoute.length >= 2 && (!dType || String(dType).trim() === '')) {
        dType = sortedForRoute[sortedForRoute.length - 1].location_type;
    }
    const oTxt = routeLocationWithType(m.origin_location, oType);
    const dTxt = routeLocationWithType(m.destination_location, dType);
    $('#sjRoute').html(
        '<div class="k text-muted small text-uppercase mb-1">Rute</div>' +
        '<div class="sj-route-pill"><span>' + escHtml(oTxt) + '</span> <i class="fas fa-arrow-right small opacity-50"></i> <span>' + escHtml(dTxt) + '</span></div>'
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
            }
            opt += '<option value="' + escHtml(String(s.id)) + '"' + dis + '>' + escHtml(formatStopSelectOptionText(s, tag)) + '</option>';
        });
    }
    if (!hasValidStop) {
        $('#stopSelect').html('<option value="">— Tidak ada rute di sistem — hubungi gudang</option>');
    } else {
        $('#stopSelect').html(opt);
    }

    $('#stopSelect').off('change.sjguard').on('change.sjguard', function() {
        const v = $(this).val();
        if (v) {
            rebuildStatusSelect(v);
        }
        renderItemsChecklist();
        updateStopHintAndBanner();
    });

    applyDefaultStopAndStatus();
    renderItemsChecklist();
}

function renderItemsChecklist() {
    const sid = $('#stopSelect').val() || nextPendingStopId() || '';
    const mode = sid ? stopItemChoiceMode(sid) : 'transit';
    updateChecklistShortcutsAndHint(mode);
    const allowArrived = mode !== 'arrival';
    const allowDrop = mode !== 'departure';
    const colClass = (allowArrived && allowDrop) ? 'col-12 col-sm-4' : 'col-12 col-sm-6';

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
        html += '<div class="' + colClass + '">';
        html += '<div class="form-check">';
        html += '<input class="form-check-input sj-item-choice" type="radio" name="sj_item_' + rid + '" id="sj_item_' + rid + '_skip" value="skip" checked data-item-id="' + rid + '">';
        html += '<label class="form-check-label" for="sj_item_' + rid + '_skip"><strong class="text-body">Barang tidak ada dalam pengiriman</strong></label>';
        html += '</div></div>';
        if (allowArrived) {
            html += '<div class="' + colClass + '">';
            html += '<div class="form-check">';
            html += '<input class="form-check-input sj-item-choice" type="radio" name="sj_item_' + rid + '" id="sj_item_' + rid + '_arr" value="arrived" data-item-id="' + rid + '">';
            html += '<label class="form-check-label text-success" for="sj_item_' + rid + '_arr"><strong>Barang ada</strong></label>';
            html += '</div></div>';
        }
        if (allowDrop) {
            html += '<div class="' + colClass + '">';
            html += '<div class="form-check">';
            html += '<input class="form-check-input sj-item-choice" type="radio" name="sj_item_' + rid + '" id="sj_item_' + rid + '_drop" value="drop" data-item-id="' + rid + '">';
            html += '<label class="form-check-label" for="sj_item_' + rid + '_drop"><strong class="text-warning">Drop di titik ini</strong></label>';
            html += '</div></div>';
        }
        html += '</div></div>';
    });
    $('#itemsChecklist').html(html);
}

function syncDeliveryMismatchUi() {
    const mis = $('#deliveryMatchNo').is(':checked');
    $('#sjDeliveryMismatchWrap').toggleClass('d-none', !mis);
    ['#deliveryActualDriver', '#deliveryActualVehicle', '#deliveryActualVehicleType', '#deliveryActualReason'].forEach(function(sel) {
        const el = document.querySelector(sel);
        if (el) {
            if (mis) {
                el.setAttribute('required', 'required');
            } else {
                el.removeAttribute('required');
            }
        }
    });
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
        showCheckpointFeedback(false, 'Cari surat jalan terlebih dahulu.');
        return;
    }
    const stopVal = $('#stopSelect').val();
    if (!stopVal) {
        showMsg('warning', 'Pilih lokasi rute yang valid.');
        showCheckpointFeedback(false, 'Pilih titik rute yang valid (biasanya sudah otomatis ke langkah berikutnya).');
        return;
    }
    const nextId = nextPendingStopId();
    if (nextId && String(stopVal) !== String(nextId)) {
        showMsg('warning', 'Konfirmasi harus pada titik berikutnya dalam urutan rute saja.');
        showCheckpointFeedback(false, 'Hanya titik berikutnya pada urutan rute yang boleh dikonfirmasi.');
        return;
    }
    const opt = $('#stopSelect option:selected');
    if (opt.length && opt.is(':disabled')) {
        showMsg('warning', 'Titik ini tidak aktif. Muat ulang data lalu ikuti langkah berikutnya pada rute.');
        showCheckpointFeedback(false, 'Titik ini tidak aktif — muat ulang data lalu coba lagi.');
        return;
    }
    const vName = ($('#verifierName').val() || '').trim();
    if (!vName) {
        showMsg('warning', 'Nama petugas wajib diisi.');
        showCheckpointFeedback(false, 'Nama petugas wajib diisi.');
        return;
    }
    const phoneRaw = ($('#verifierPhone').val() || '').trim();
    if (phoneRaw !== '' && !isValidIndoMobileInput(phoneRaw)) {
        showMsg('warning', 'No. HP tidak valid. Gunakan format 08xxxxxxxxxx (10–13 digit) atau +62 8…');
        showCheckpointFeedback(false, 'Contoh: 081234567890. Hapus isian jika tidak ingin mencantumkan nomor HP.');
        return;
    }
    const reason = ($('#notes').val() || '').trim();
    const deliveryMatch = $('input[name="deliveryMatch"]:checked').val() || 'match';
    if (deliveryMatch === 'mismatch') {
        const ad = ($('#deliveryActualDriver').val() || '').trim();
        const av = ($('#deliveryActualVehicle').val() || '').trim();
        const avt = ($('#deliveryActualVehicleType').val() || '').trim();
        const ar = ($('#deliveryActualReason').val() || '').trim();
        if (!ad || !av || !avt || !ar) {
            showMsg('warning', 'Detail tidak sesuai: isi lengkap driver, no. kendaraan, jenis kendaraan, dan alasan lapangan.');
            showCheckpointFeedback(false, 'Pilih &ldquo;Tidak sesuai&rdquo; hanya jika data gudang salah — lalu lengkapi keempat kolom koreksi.');
            return;
        }
    }
    const choices = collectItemChoices();
    const mode = stopItemChoiceMode(stopVal);
    const hasItemRows = Array.isArray(currentItems) && currentItems.some(function(it) {
        return it && it.id;
    });
    if (hasItemRows) {
        if (mode === 'departure') {
            if (choices.droppedItemIds.length > 0) {
                showMsg('warning', 'Di titik berangkat tidak boleh Drop — pilih Barang ada atau tidak ada dalam pengiriman.');
                showCheckpointFeedback(false, 'Titik berangkat: opsi Drop tidak dipakai.');
                return;
            }
            if (choices.checkedItemIds.length === 0) {
                showMsg('warning', 'Di titik berangkat pilih minimal satu barang yang ikut muatan (Barang ada).');
                showCheckpointFeedback(false, 'Minimal satu barang harus Barang ada di titik berangkat.');
                return;
            }
        } else if (mode === 'arrival') {
            if (choices.checkedItemIds.length > 0) {
                showMsg('warning', 'Di titik tujuan akhir tidak boleh Barang ada — gunakan Drop atau tidak ada dalam pengiriman.');
                showCheckpointFeedback(false, 'Titik akhir: Barang ada tidak tersedia.');
                return;
            }
            if (choices.droppedItemIds.length === 0) {
                showMsg('warning', 'Di titik akhir pilih minimal satu barang dengan Drop jika barang diterima di lokasi ini.');
                showCheckpointFeedback(false, 'Minimal satu barang harus Drop di titik tujuan akhir.');
                return;
            }
        } else if (choices.checkedItemIds.length === 0 && choices.droppedItemIds.length === 0) {
            showMsg('warning', 'Checklist: minimal satu barang pilih Barang ada atau Drop (atau tombol pintasan).');
            showCheckpointFeedback(false, 'Pilih minimal satu barang Barang ada atau Drop di titik transit.');
            return;
        }
    }

    clearCheckpointFeedback();
    const pair = csrfPair();
    const dm = $('input[name="deliveryMatch"]:checked').val() || 'match';
    const data = {
        movement_id: currentMovement.id,
        stop_id: stopVal,
        status: $('#statusSelect').val(),
        verifier_name: vName,
        verifier_phone: phoneRaw === '' ? '' : normalizeVerifierPhone(phoneRaw),
        notes: $('#notes').val(),
        verification_code: $('#verifyCode').val(),
        checkpoint_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
        checked_item_ids: choices.checkedItemIds,
        dropped_item_ids: choices.droppedItemIds,
        delivery_match: dm
    };
    if (dm === 'mismatch') {
        data.delivery_actual_driver = ($('#deliveryActualDriver').val() || '').trim();
        data.delivery_actual_vehicle = ($('#deliveryActualVehicle').val() || '').trim();
        data.delivery_actual_vehicle_type = ($('#deliveryActualVehicleType').val() || '').trim();
        data.delivery_actual_reason = ($('#deliveryActualReason').val() || '').trim();
    }
    if (pair.name) {
        data[pair.name] = pair.value;
    }

    $.post(checkpointUrl, data, function(res) {
        if (!res.success) {
            const err = res.message || 'Gagal menyimpan.';
            showMsg('danger', err);
            showCheckpointFeedback(false, err);
            return;
        }
        const okText = res.message || 'Konfirmasi tersimpan. Data tercatat di sistem gudang.';
        showSjSuccessModal(okText);
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
        showCheckpointFeedback(false, msg);
    });
}

/** Satpam: isi cepat — semua barang = Lewat gerbang (ikut jalan). */
$(document).on('click', '#btnSjAllPass', function() {
    $('#itemsChecklist .sj-item-choice[value="arrived"]').each(function() {
        this.checked = true;
    });
});

/** Satpam: isi cepat — semua barang = Drop di titik ini. */
$(document).on('click', '#btnSjAllDrop', function() {
    $('#itemsChecklist .sj-item-choice[value="drop"]').each(function() {
        this.checked = true;
    });
});

$(document).on('change', 'input[name="deliveryMatch"]', function() {
    syncDeliveryMismatchUi();
});

$(document).on('click', '#sjSuccessModalOk', function() {
    const el = document.getElementById('sjSuccessModal');
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const inst = bootstrap.Modal.getInstance(el);
        if (inst) {
            inst.hide();
        }
    }
    sjSuccessCleanupAndReload();
});

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
