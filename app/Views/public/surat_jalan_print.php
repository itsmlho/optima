<?php
/**
 * Cetak Surat Jalan — mode publik (kode verifikasi) & gudang (login).
 *
 * @var array  $movement
 * @var array  $items
 * @var array  $stops
 * @var array  $checkpoints
 * @var string $companyName
 * @var bool   $isPublicContext (diabaikan pada tampilan cetak; kode verifikasi tidak dicetak)
 * @var bool   $autoPrint      Set true → panggil window.print() setelah halaman siap (query autoprint=1)
 */
$movement    = $movement ?? [];
$items       = $items ?? [];
$stops       = $stops ?? [];
$checkpoints = $checkpoints ?? [];
$companyName = $companyName ?? 'PT Sarana Mitra Luas Tbk';
$autoPrint   = $autoPrint ?? false;

$logoUrl       = base_url('assets/images/company-logo.svg');
$itemCount     = count($items);
$itemsDenseCls = $itemCount > 14 ? ' items-dense' : '';

if (! function_exists('sj_print_format_dt')) {
    function sj_print_format_dt(?string $s): string
    {
        if ($s === null || $s === '') {
            return '-';
        }
        $t = strtotime($s);

        return $t ? date('d/m/Y H:i', $t) : $s;
    }
}

$purpose     = strtoupper((string) ($movement['movement_purpose'] ?? 'INTERNAL_TRANSFER'));
$purposeText = $purpose === 'SCRAP_SALE' ? 'Keluar jual scrab' : 'Pindah / operasional internal';

$statusMap = [
    'DRAFT'      => 'Draft',
    'IN_TRANSIT' => 'Dalam perjalanan',
    'ARRIVED'    => 'Selesai (tiba)',
    'CANCELLED'  => 'Batal',
];
$movStatus = $statusMap[$movement['status'] ?? ''] ?? ($movement['status'] ?? '-');

$cpLabel = [
    'DEPARTED'         => 'Berangkat',
    'TRANSIT_VERIFIED' => 'Transit',
    'ARRIVED'          => 'Sampai',
];

$originLabel      = trim((string) ($movement['origin_location'] ?? '-'));
$destinationLabel = trim((string) ($movement['destination_location'] ?? '-'));

$routeLine = '';
if ($stops !== []) {
    $parts = [];
    foreach ($stops as $s) {
        $parts[] = trim((string) ($s['location_name'] ?? '-'));
    }
    $routeLine = implode(' → ', $parts);
}

$dn = trim((string) ($movement['driver_name'] ?? ''));
$vn = trim((string) ($movement['vehicle_number'] ?? ''));
$nt = trim((string) ($movement['notes'] ?? ''));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Surat Jalan <?= esc($movement['surat_jalan_number'] ?? '') ?></title>
    <style>
        :root {
            --ink: #1a1d21;
            --muted: #5c636a;
            --line: #c8cdd3;
            --accent: #0b5ed7;
            --accent-soft: #e8f1fc;
            --paper: #ffffff;
            --navy: #1a365d;
            --navy-light: #2c5282;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            font-size: 9.5pt;
            line-height: 1.35;
            color: var(--ink);
            margin: 0;
            padding: 8mm 8mm;
            background: #e9ecef;
        }
        .sheet {
            max-width: 210mm;
            margin: 0 auto;
            background: var(--paper);
            padding: 8mm 10mm 10mm;
            box-shadow: 0 4px 24px rgba(0,0,0,.08);
            border-radius: 2px;
        }
        .noprint { margin-bottom: 10px; }
        .noprint button {
            font: inherit;
            padding: .4rem .85rem;
            margin-right: 8px;
            border-radius: 6px;
            border: 1px solid var(--line);
            background: #fff;
            cursor: pointer;
        }
        .noprint button:first-child { background: var(--accent); color: #fff; border-color: var(--accent); }

        /* Kop surat — satu alur vertikal, nada resmi */
        .print-header {
            margin-bottom: 8px;
        }
        .letterhead {
            display: flex;
            align-items: center;
            gap: 0;
            padding-bottom: 6px;
        }
        .letterhead-logo {
            height: 36px;
            width: auto;
            max-width: 110px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .letterhead-vrule {
            width: 1px;
            align-self: stretch;
            min-height: 34px;
            background: #94a3b8;
            margin: 0 10px 0 10px;
            flex-shrink: 0;
        }
        .letterhead-text { flex: 1; min-width: 0; }
        .org-name {
            font-size: 10pt;
            font-weight: 700;
            color: var(--navy);
            letter-spacing: 0.03em;
            text-transform: uppercase;
            margin: 0 0 1px;
            line-height: 1.2;
        }
        .org-tagline {
            font-size: 7pt;
            color: #64748b;
            margin: 0;
            line-height: 1.3;
        }
        .letterhead-rule-double {
            border: none;
            border-top: 2px double var(--navy);
            margin: 4px 0 0;
        }
        .form-title {
            font-family: "Times New Roman", Times, Georgia, "Noto Serif", serif;
            text-align: center;
            font-size: 14pt;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #0f172a;
            margin: 6px 0 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid #475569;
            display: block;
        }
        table.doc-ref-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            border: 1px solid var(--navy);
            margin-bottom: 0;
        }
        table.doc-ref-table td {
            border: 1px solid #94a3b8;
            padding: 4px 6px;
            vertical-align: top;
            width: 25%;
            background: #fafbfc;
        }
        table.doc-ref-table .ref-lbl {
            display: block;
            font-size: 6pt;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-bottom: 2px;
        }
        table.doc-ref-table .ref-val {
            font-size: 9pt;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: 0.02em;
        }
        table.doc-ref-table .ref-val.status-val {
            font-weight: 700;
            color: var(--navy-light);
        }
        @media (max-width: 640px) {
            .letterhead { flex-wrap: wrap; }
            .letterhead-vrule { display: none; }
            table.doc-ref-table td { width: 50%; }
        }

        .section-title {
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--muted);
            margin: 6px 0 3px;
            padding-left: 5px;
            border-left: 2px solid var(--accent);
        }

        table.summary-compact {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 8.5pt;
        }
        table.summary-compact td {
            border: 1px solid var(--line);
            padding: 3px 6px;
            vertical-align: top;
        }
        table.summary-compact td.k {
            width: 18%;
            max-width: 100px;
            background: #f4f6f8;
            font-weight: 600;
            font-size: 7pt;
            color: var(--muted);
            white-space: nowrap;
        }
        table.summary-compact td.v-empty { color: var(--muted); }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 9pt;
        }
        table.data.items-table.items-dense { font-size: 8.25pt; }
        table.data.items-table.items-dense td,
        table.data.items-table.items-dense th { padding: 3px 5px; }
        table.data th,
        table.data td {
            border: 1px solid var(--line);
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
        }
        table.data thead th {
            background: #eef1f4;
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: var(--muted);
        }
        table.data tbody tr:nth-child(even) { background: #fafbfc; }

        .muted { color: var(--muted); font-size: 8pt; }

        .print-footnote {
            font-size: 6.5pt;
            color: #6b7280;
            margin: 4px 0 0;
            line-height: 1.3;
            text-align: center;
        }

        .block-ttd {
            margin-top: 4mm;
            padding-top: 2mm;
            page-break-inside: avoid;
        }
        /* Empat TTD sebaris: di A4 (~180mm isi) ≈ 45mm/kolom — muat untuk cap & tangan */
        .sign-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 4px 4px;
            border: 1px solid var(--line);
            border-radius: 3px;
            padding: 5px 3px 6px;
            background: #fafbfc;
        }
        .sign-cell {
            text-align: center;
            padding: 2px 4px 0;
            min-width: 0;
        }
        .sign-grid .sign-cell:not(:first-child) {
            border-left: 1px solid #e2e8f0;
        }
        .sign-cell .hint {
            font-size: 6.75pt;
            color: var(--muted);
            margin: 0 0 3px;
            line-height: 1.25;
            hyphens: auto;
            word-wrap: break-word;
        }
        .sign-cell .line {
            border-top: 1px solid var(--ink);
            margin-top: 32px;
            padding-top: 4px;
            font-size: 7.25pt;
            font-weight: 600;
            line-height: 1.2;
        }
        .sign-cell .sub {
            font-size: 6.5pt;
            color: var(--muted);
            margin-top: 2px;
        }
        /* Layar sempit: 2×2 — border hanya antara kolom & baris */
        @media (max-width: 720px) {
            .sign-grid {
                grid-template-columns: 1fr 1fr;
            }
            .sign-grid .sign-cell {
                border-left: none !important;
            }
            .sign-grid .sign-cell:nth-child(2n) {
                border-left: 1px solid #e2e8f0 !important;
            }
            .sign-grid .sign-cell:nth-child(n + 3) {
                border-top: 1px solid #e2e8f0;
                padding-top: 10px;
            }
        }

        @media print {
            body { background: #fff; padding: 0; }
            .sheet {
                box-shadow: none;
                border-radius: 0;
                padding: 5mm 8mm 6mm;
                max-width: none;
            }
            .noprint { display: none !important; }
            table.data tbody tr { background: transparent !important; }
            table.data thead { display: table-header-group; }
            table.data tfoot { display: table-footer-group; }
            table.items-table { page-break-inside: auto; }
            table.items-table tr { page-break-inside: avoid; }
            .block-ttd { page-break-inside: avoid; page-break-before: auto; }
            .sign-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
                gap: 6pt 5pt;
                padding: 6pt 3pt 8pt;
            }
            .sign-grid .sign-cell {
                border-top: none !important;
                padding-top: 0 !important;
            }
            .sign-grid .sign-cell:not(:first-child) {
                border-left: 1px solid #bbb !important;
            }
            .sign-cell .hint { font-size: 6pt; }
            .sign-cell .line { margin-top: 28pt; }
        }
    </style>
</head>
<body>
<div class="sheet">
    <div class="noprint">
        <button type="button" onclick="window.print()">Cetak / PDF</button>
        <button type="button" onclick="window.close()">Tutup</button>
    </div>

    <header class="print-header">
        <div class="letterhead">
            <img class="letterhead-logo" src="<?= esc($logoUrl) ?>" alt="" width="110" height="36">
            <div class="letterhead-vrule" aria-hidden="true"></div>
            <div class="letterhead-text">
                <p class="org-name"><?= esc($companyName) ?></p>
                <p class="org-tagline">Pemindahan / pengiriman aset</p>
            </div>
        </div>
        <hr class="letterhead-rule-double">
        <h1 class="form-title">Surat Jalan</h1>
        <table class="doc-ref-table" role="presentation">
            <tr>
                <td>
                    <span class="ref-lbl">Nomor surat jalan</span>
                    <span class="ref-val"><?= esc($movement['surat_jalan_number'] ?? '—') ?></span>
                </td>
                <td>
                    <span class="ref-lbl">Nomor movement</span>
                    <span class="ref-val"><?= esc($movement['movement_number'] ?? '—') ?></span>
                </td>
                <td>
                    <span class="ref-lbl">Tgl. cetak</span>
                    <span class="ref-val"><?= esc(date('d/m/Y, \j\a\m H:i')) ?></span>
                </td>
                <td>
                    <span class="ref-lbl">Status</span>
                    <span class="ref-val status-val"><?= esc($movStatus) ?></span>
                </td>
            </tr>
        </table>
    </header>

    <p class="section-title">Ringkasan pengiriman</p>
    <table class="summary-compact">
        <tr>
            <td class="k">Tipe SJ</td>
            <td><?= esc($purposeText) ?></td>
            <td class="k">Tanggal kirim</td>
            <td><?= esc(sj_print_format_dt($movement['movement_date'] ?? null)) ?></td>
        </tr>
        <tr>
            <td class="k">Asal</td>
            <td colspan="3"><?= esc($originLabel) ?> <span class="muted">(<?= esc($movement['origin_type'] ?? '') ?>)</span></td>
        </tr>
        <tr>
            <td class="k">Tujuan</td>
            <td colspan="3"><?= esc($destinationLabel) ?> <span class="muted">(<?= esc($movement['destination_type'] ?? '') ?>)</span></td>
        </tr>
        <tr>
            <td class="k">Driver</td>
            <td><?= $dn !== '' ? esc($dn) : '<span class="v-empty">—</span>' ?></td>
            <td class="k">No. kendaraan</td>
            <td><?= $vn !== '' ? esc($vn) : '<span class="v-empty">—</span>' ?></td>
        </tr>
        <?php if ($routeLine !== ''): ?>
        <tr>
            <td class="k">Rute</td>
            <td colspan="3"><?= esc($routeLine) ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td class="k">Catatan</td>
            <td colspan="3"><?= $nt !== '' ? esc($nt) : '<span class="v-empty">—</span>' ?></td>
        </tr>
    </table>

    <p class="section-title">Barang yang dibawa</p>
    <table class="data items-table<?= esc($itemsDenseCls, 'attr') ?>">
        <thead>
            <tr>
                <th style="width:34px">No</th>
                <th>Uraian barang</th>
                <th style="width:52px">Qty</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $i => $it): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= esc($it['print_description'] ?? ($it['component_type'] ?? '-')) ?></td>
                <td><?= (int) ($it['qty'] ?? 1) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if ($items === []): ?>
            <tr><td colspan="3" class="muted">Tidak ada barang terlampir.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <?php if ($checkpoints !== []): ?>
    <p class="section-title">Riwayat konfirmasi (sistem)</p>
    <table class="data">
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Status</th>
                <th>Petugas</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($checkpoints as $c): ?>
            <tr>
                <td><?= esc(sj_print_format_dt($c['checkpoint_at'] ?? null)) ?></td>
                <td><?= esc($cpLabel[$c['checkpoint_status'] ?? ''] ?? ($c['checkpoint_status'] ?? '-')) ?></td>
                <td><?= esc($c['verifier_name'] ?? '-') ?></td>
                <td><?= esc($c['notes'] ?? '-') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <div class="block-ttd">
        <p class="section-title" style="margin-top:0">Tanda tangan</p>
        <div class="sign-grid">
            <div class="sign-cell">
                <p class="hint">Yang menyerahkan / sopir</p>
                <div class="line">Pengirim / Driver</div>
                <div class="sub">Nama &amp; tanggal</div>
            </div>
            <div class="sign-cell">
                <p class="hint">Satpam <strong><?= esc($originLabel) ?></strong> — keluar</p>
                <div class="line">Satpam lokasi asal</div>
                <div class="sub">Nama &amp; stempel</div>
            </div>
            <div class="sign-cell">
                <p class="hint">Satpam <strong><?= esc($destinationLabel) ?></strong> — masuk</p>
                <div class="line">Satpam lokasi tujuan</div>
                <div class="sub">Nama &amp; stempel</div>
            </div>
            <div class="sign-cell">
                <p class="hint">Penerima di tujuan</p>
                <div class="line">Penerima</div>
                <div class="sub">Nama &amp; tanggal</div>
            </div>
        </div>
    </div>

    <p class="print-footnote">TTD satpam asal dan tujuan untuk petugas berbeda. Tabel barang dapat berlanjut ke halaman berikutnya.</p>
</div>
<?php if ($autoPrint): ?>
<script>
(function () {
    function runPrint() {
        try {
            window.print();
        } catch (e) { /* ignore */ }
    }
    function schedule() {
        setTimeout(runPrint, 300);
    }
    if (document.readyState === 'complete') {
        schedule();
    } else {
        window.addEventListener('load', schedule);
    }
})();
</script>
<?php endif; ?>
</body>
</html>
