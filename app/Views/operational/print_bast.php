<?php
$di = $di ?? [];
$spk = $spk ?? [];
$items = $items ?? [];
$vehicle = $vehicle ?? [];
$workflow = $workflow ?? [];
$customer = $customer ?? [];
$companyName = $company_name ?? 'PT Sarana Mitra Luas Tbk';
$companyAddress = $company_address ?? 'Jl. Raya Cikarang - Cibarusah No. 150, Cikarang Selatan, Bekasi';
$bastNumber = $bast_number ?? ('BAST/' . ($di['nomor_di'] ?? date('YmdHis')));
$commandLabel = $command_label ?? '-';

if (!function_exists('op_bast_date')) {
    function op_bast_date($value, string $fallback = '-'): string
    {
        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return $fallback;
        }
        $ts = strtotime($value);

        return $ts ? date('d/m/Y', $ts) : $value;
    }
}

if (!function_exists('op_bast_value')) {
    function op_bast_value($value, string $fallback = '-'): string
    {
        $value = trim((string) ($value ?? ''));

        return $value !== '' ? $value : $fallback;
    }
}

if (!function_exists('op_bast_compact_specs')) {
    function op_bast_compact_specs($value): string
    {
        $parts = array_values(array_filter(array_map('trim', explode('|', (string) ($value ?? '')))));
        $parts = array_filter($parts, static function ($part) {
            return stripos($part, 'SN Unit:') !== 0;
        });

        return implode(' | ', array_slice($parts, 0, 4));
    }
}

$jenisKode = strtoupper((string) ($workflow['jenis_kode'] ?? ''));
$tujuanLabel = op_bast_value($workflow['tujuan_label'] ?? null, '');
$narrative = 'Pihak SML menyerahkan dan pihak customer menerima barang/unit sebagaimana daftar di bawah ini dalam kondisi baik dan lengkap sesuai pemeriksaan bersama.';

$docDate = op_bast_date($vehicle['date'] ?? ($di['tanggal_kirim'] ?? $di['dibuat_pada'] ?? ''), date('d/m/Y'));
$picLine = trim(($customer['pic'] ?? '') . ' / ' . ($customer['phone'] ?? ''), ' /');
$driverLine = trim(($vehicle['driver'] ?? '') . ' / ' . ($vehicle['driver_phone'] ?? ''), ' /');
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($bastNumber) ?></title>
    <style>
        @page { size: A4 portrait; margin: 12mm; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #fff;
            color: #111;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
        }
        .toolbar {
            margin: 10px auto;
            max-width: 210mm;
            text-align: right;
        }
        .toolbar button {
            border: 1px solid #999;
            background: #fff;
            padding: 6px 12px;
            cursor: pointer;
        }
        .page {
            max-width: 186mm;
            margin: 0 auto;
            background: #fff;
        }
        .document-header {
            display: grid;
            grid-template-columns: 36mm 1fr 48mm;
            gap: 8mm;
            align-items: start;
            border-bottom: 2px solid #111;
            padding-bottom: 7px;
            margin-bottom: 10px;
        }
        .logo {
            width: 33mm;
            height: auto;
        }
        .company h1 {
            margin: 0 0 2px;
            font-size: 12pt;
            letter-spacing: .02em;
        }
        .company div {
            font-size: 7.2pt;
            line-height: 1.25;
        }
        .doc-meta {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.4pt;
        }
        .doc-meta td {
            border: 1px solid #111;
            padding: 3px 4px;
            vertical-align: top;
        }
        .doc-meta .label {
            width: 15mm;
            font-weight: bold;
            background: #f5f5f5;
        }
        .title {
            text-align: center;
            margin: 10px 0 9px;
        }
        .title h2 {
            margin: 0;
            font-size: 14pt;
            text-decoration: underline;
            letter-spacing: .04em;
        }
        .title .sub {
            margin-top: 3px;
            font-size: 8.2pt;
            font-weight: bold;
        }
        .purpose-badge {
            display: inline-block;
            margin-top: 4px;
            padding: 2px 8px;
            border: 1px solid #555;
            font-size: 7pt;
            font-weight: normal;
            letter-spacing: .03em;
        }
        .section-title {
            margin-top: 9px;
            padding: 4px 6px;
            border: 1px solid #111;
            background: #f2f2f2;
            font-weight: bold;
            text-align: center;
            font-size: 8.5pt;
        }
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 7px;
        }
        .info-grid td {
            border: 1px solid #333;
            padding: 4px 6px;
            vertical-align: top;
            font-size: 8pt;
        }
        .info-grid .label {
            width: 25mm;
            font-weight: bold;
            background: #f7f7f7;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .items th,
        .items td {
            border: 1px solid #111;
            padding: 4px 5px;
            vertical-align: top;
            font-size: 7.5pt;
        }
        .items th {
            text-align: center;
            font-weight: bold;
            background: #f2f2f2;
        }
        .items strong {
            font-size: 7.8pt;
        }
        .muted {
            color: #444;
            font-size: 6.9pt;
            line-height: 1.2;
            margin-top: 2px;
        }
        .statement {
            border: 1px solid #111;
            padding: 7px 8px;
            margin: 9px 0;
            text-align: justify;
            font-size: 8pt;
            line-height: 1.32;
        }
        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16mm;
            text-align: center;
            margin-top: 12px;
            page-break-inside: avoid;
        }
        .sign-title {
            font-weight: bold;
            font-size: 8pt;
        }
        .sign-sub {
            min-height: 13px;
            color: #555;
            font-size: 7pt;
        }
        .sign-space {
            height: 35mm;
        }
        .sign-name {
            border-top: 1px solid #111;
            padding-top: 4px;
            min-height: 18px;
            font-size: 8pt;
        }
        .notes {
            margin-top: 9px;
            border-top: 1px solid #999;
            padding-top: 4px;
            font-size: 6.8pt;
            color: #444;
            display: flex;
            justify-content: space-between;
        }
        @media print {
            .toolbar { display: none; }
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
<div class="toolbar">
    <button type="button" onclick="window.print()">Print BAST</button>
</div>

<main class="page">
    <header class="document-header">
        <div>
            <img class="logo" src="<?= base_url('assets/images/company-logo.svg') ?>" alt="SML Rental">
        </div>
        <div class="company">
            <h1><?= esc($companyName) ?></h1>
            <div>Forklift and Crane - Rental, Repair and Sales</div>
            <div><?= esc($companyAddress) ?></div>
            <div>Telp. (021) 89902188</div>
        </div>
        <table class="doc-meta">
            <tr>
                <td class="label">No</td>
                <td><?= esc($bastNumber) ?></td>
            </tr>
            <tr>
                <td class="label">DI</td>
                <td><?= esc(op_bast_value($di['nomor_di'] ?? null)) ?></td>
            </tr>
            <tr>
                <td class="label">Tgl</td>
                <td><?= esc($docDate) ?></td>
            </tr>
        </table>
    </header>

    <section class="title">
        <h2>BERITA ACARA SERAH TERIMA</h2>
        <div class="sub"><?= esc($commandLabel) ?></div>
        <div class="purpose-badge">DOKUMEN EKSTERNAL - BUKTI SERAH TERIMA</div>
    </section>

    <div class="section-title">INFORMASI DOKUMEN</div>
    <table class="info-grid">
        <tr>
            <td class="label">Customer</td>
            <td><?= esc(op_bast_value($customer['name'] ?? null)) ?></td>
            <td class="label">PIC / Telp</td>
            <td><?= esc(op_bast_value($picLine)) ?></td>
        </tr>
        <tr>
            <td class="label">Lokasi</td>
            <td colspan="3"><?= esc(op_bast_value($customer['location'] ?? null)) ?></td>
        </tr>
        <tr>
            <td class="label">Kendaraan</td>
            <td><?= esc(op_bast_value($vehicle['type'] ?? null)) ?></td>
            <td class="label">No Polisi</td>
            <td><?= esc(op_bast_value($vehicle['plate'] ?? null)) ?></td>
        </tr>
        <tr>
            <td class="label">Driver / HP</td>
            <td><?= esc(op_bast_value($driverLine)) ?></td>
            <td class="label">Ref. Kontrak / PO</td>
            <td><?= esc(op_bast_value($di['po_kontrak_nomor'] ?? ($spk['po_kontrak_nomor'] ?? null))) ?></td>
        </tr>
    </table>

    <div class="section-title">DAFTAR UNIT / BARANG</div>
    <table class="items">
        <thead>
        <tr>
            <th style="width: 8mm;">No</th>
            <th style="width: 13mm;">Qty</th>
            <th>Nama Unit / Barang</th>
            <th style="width: 22mm;">No Unit</th>
            <th style="width: 26mm;">SN Unit</th>
            <th style="width: 18mm;">Status</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $idx => $item): ?>
            <?php $specs = op_bast_compact_specs($item['print_specs'] ?? ''); ?>
            <tr>
                <td style="text-align:center;"><?= $idx + 1 ?></td>
                <td style="text-align:center;"><?= esc($item['print_qty'] ?? 1) ?> unit</td>
                <td>
                    <strong><?= esc($item['print_name'] ?? '-') ?></strong>
                    <?php if ($specs !== ''): ?>
                        <div class="muted"><?= esc($specs) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($item['keterangan'])): ?>
                        <div class="muted">Ket: <?= esc($item['keterangan']) ?></div>
                    <?php endif; ?>
                </td>
                <td><?= esc(op_bast_value($item['print_no'] ?? null)) ?></td>
                <td><?= esc(op_bast_value($item['print_sn'] ?? null)) ?></td>
                <td style="text-align:center;"><?= esc(op_bast_value($item['print_role'] ?? null)) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (count($items) < 4): ?>
            <?php for ($i = count($items); $i < 4; $i++): ?>
                <tr>
                    <td style="text-align:center;"><?= $i + 1 ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            <?php endfor; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="section-title">PERNYATAAN SERAH TERIMA</div>
    <div class="statement">
        Pada tanggal <?= esc($docDate) ?>, <?= esc($narrative) ?>
        Barang/unit yang tercantum pada dokumen ini telah diperiksa bersama dan dinyatakan
        <?= esc(op_bast_value($di['catatan'] ?? null, 'diterima dalam kondisi baik dan lengkap sesuai dokumen.')) ?>
        Dokumen ini menjadi bukti serah terima antara pihak SML dan customer.
    </div>

    <section class="signatures">
        <div>
            <div class="sign-title">PIHAK SML</div>
            <div class="sign-sub">Delivery / Operational</div>
            <div class="sign-space"></div>
            <div class="sign-name"><?= esc(op_bast_value($di['perencanaan_approved_by'] ?? null, '')) ?></div>
        </div>
        <div>
            <div class="sign-title">PENGEMUDI</div>
            <div class="sign-sub">Driver</div>
            <div class="sign-space"></div>
            <div class="sign-name"><?= esc(op_bast_value($vehicle['driver'] ?? null, '')) ?></div>
        </div>
        <div>
            <div class="sign-title">PENERIMA</div>
            <div class="sign-sub">Customer</div>
            <div class="sign-space"></div>
            <div class="sign-name"><?= esc(op_bast_value($di['sampai_penerima_nama'] ?? null, '')) ?></div>
        </div>
    </section>

    <footer class="notes">
        <span>Dicetak: <?= date('d/m/Y H:i') ?></span>
        <span><?= esc($bastNumber) ?></span>
    </footer>
</main>

<script>
window.addEventListener('load', function () {
    document.title = <?= json_encode($bastNumber) ?>;
    setTimeout(function () { window.print(); }, 400);
});
</script>
</body>
</html>
