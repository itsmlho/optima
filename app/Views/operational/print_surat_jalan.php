<?php
$di = $di ?? [];
$items = $items ?? [];
$vehicle = $vehicle ?? [];
$customer = $customer ?? [];
$workflow = $workflow ?? [];
$companyName = $company_name ?? 'PT Sarana Mitra Luas Tbk';
$companyAddress = $company_address ?? '';
$sjNumber = $surat_jalan_number ?? ('SJ-' . ($di['nomor_di'] ?? date('YmdHis')));
$commandLabel = $command_label ?? '-';
$sjContext = $surat_jalan_context ?? [
    'mode' => 'kirim',
    'title' => 'SURAT JALAN - KIRIM UNIT',
    'purpose' => 'DOKUMEN PERJALANAN BARANG - PENGIRIMAN UNIT - 3 RANGKAP',
    'role_label' => 'KIRIM',
];

if (!function_exists('op_sj_date')) {
    function op_sj_date(?string $value, string $fallback = '-'): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return $fallback;
        }
        $ts = strtotime($value);
        return $ts ? date('d/m/Y', $ts) : $value;
    }
}

if (!function_exists('op_sj_value')) {
    function op_sj_value($value, string $fallback = '-'): string
    {
        $value = trim((string) ($value ?? ''));
        return $value !== '' ? $value : $fallback;
    }
}

$printDate = op_sj_date($vehicle['date'] ?? ($di['tanggal_kirim'] ?? ''), date('d/m/Y'));
$jenisKode = strtoupper((string) ($workflow['jenis_kode'] ?? ''));
$sjMode = strtolower((string) ($sjContext['mode'] ?? 'kirim'));
$actionText = [
    'kirim' => strtoupper((string) ($sjContext['role_label'] ?? 'KIRIM')),
    'tarik' => 'AMBIL',
    'relokasi' => 'PINDAHKAN',
][$sjMode] ?? 'KIRIM';
if ($jenisKode === 'TUKAR') {
    $actionText = $sjMode === 'tarik' ? 'AMBIL' : 'KIRIM';
}
$movementText = [
    'kirim' => 'Unit diantar dengan kendaraan',
    'tarik' => 'Unit ditarik dengan kendaraan',
    'relokasi' => 'Unit direlokasi dengan kendaraan',
][$sjMode] ?? 'Unit dikirim dengan kendaraan';
if ($jenisKode === 'TUKAR') {
    $movementText = $sjMode === 'tarik'
        ? 'Unit lama ditarik dengan kendaraan'
        : 'Unit pengganti diantar dengan kendaraan';
}
$vehicleLine = trim(implode(' / ', array_filter([
    $vehicle['type'] ?? '',
    $vehicle['plate'] ?? '',
])));
$vehicleType = op_sj_value($vehicle['type'] ?? null, 'Kendaraan');
$vehiclePlate = op_sj_value($vehicle['plate'] ?? null, '-');
$receiverName = op_sj_value($di['sampai_penerima_nama'] ?? null, '');
$driverName = op_sj_value($vehicle['driver'] ?? null, '');
$maxRows = 6;
$itemPages = array_chunk($items, $maxRows);
if ($itemPages === []) {
    $itemPages = [[]];
}
$totalPages = count($itemPages);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($sjNumber) ?></title>
    <style>
        @page { size: 8.5in 5.5in; margin: 0.10in; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #fff;
            color: #000;
            font-family: "Times New Roman", Times, serif;
            font-size: 8.6pt;
            font-weight: 700;
            line-height: 1.12;
        }
        .toolbar {
            font-family: Arial, sans-serif;
            text-align: right;
            margin: 8px;
        }
        .toolbar button {
            border: 1px solid #777;
            background: #fff;
            padding: 5px 10px;
            cursor: pointer;
        }
        .sj {
            width: 8.28in;
            max-height: 5.30in;
            margin: 0 auto;
            padding: 0;
            overflow: visible;
        }
        .page-break {
            page-break-after: always;
            break-after: page;
            margin-bottom: 0.20in;
        }
        .top {
            display: grid;
            grid-template-columns: 1.55in 3.12in 3.55in;
            gap: 0.06in;
            align-items: start;
            min-height: 0.98in;
            margin-bottom: 0.05in;
        }
        .logo {
            width: 1.18in;
            height: auto;
            margin: 0.11in 0 0 0.05in;
        }
        .company {
            font-size: 9pt;
            line-height: 1.12;
            padding-top: 0.13in;
        }
        .company strong {
            display: block;
            font-size: 10.5pt;
            margin-bottom: 0.01in;
        }
        .date-box {
            border-bottom: 1px dotted #000;
            padding: 0.035in 0 0.025in 0.05in;
            font-size: 9.5pt;
            text-align: left;
        }
        .cust-box {
            display: grid;
            grid-template-columns: 0.70in 1fr;
            border: 0;
            font-size: 8pt;
            margin-top: 0.01in;
        }
        .cust-box div { padding: 0.022in 0.04in; border-bottom: 1px dotted #000; min-height: 0.18in; }
        .cust-box div:nth-last-child(-n+2) { border-bottom: 0; }
        .title {
            display: grid;
            grid-template-columns: 1.12in 1.42in 1fr;
            align-items: center;
            border: 0;
            margin: 0.02in 0 0.05in 0;
            padding: 0;
        }
        .title strong { font-size: 9.5pt; }
        .doc-kind {
            font-size: 8.8pt;
            font-weight: bold;
            text-decoration: underline;
        }
        .title .num {
            font-weight: bold;
            letter-spacing: .02em;
            font-size: 9pt;
        }
        .vehicle {
            display: grid;
            grid-template-columns: 2.35in 1.00in 0.32in 1.25in;
            gap: 0.05in;
            align-items: center;
            border-bottom: 1px dotted #000;
            padding-bottom: 0.03in;
            margin-bottom: 0.035in;
            font-size: 8.2pt;
            white-space: nowrap;
        }
        .vehicle .line {
            border-bottom: 1px dotted #000;
            text-align: center;
            min-height: 0.16in;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 8.3pt;
            line-height: 1.12;
        }
        .items th, .items td {
            border: 1px solid #000;
            padding: 0.026in 0.045in;
            vertical-align: top;
        }
        .items th {
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
        }
        .qty { width: 1.85in; text-align: center; }
        .name { width: auto; }
        .note { font-size: 7.8pt; margin-left: 0.06in; }
        .item-row td { height: 0.30in; }
        .blank-row td { height: 0.27in; }
        .bottom-area {
            display: grid;
            grid-template-columns: 1.70in 1fr;
            gap: 0.18in;
            margin-top: 0.11in;
            align-items: start;
        }
        .signs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.55in;
            margin-top: 0;
            text-align: center;
            font-size: 8.4pt;
        }
        .sign-space { height: 0.46in; }
        .sign-line {
            border-top: 1px solid #000;
            width: 1.10in;
            margin: 0 auto;
            padding-top: 0.025in;
        }
        .copies {
            margin-top: 0.27in;
            font-size: 7.8pt;
            font-weight: bold;
            width: 1.70in;
        }
        .footer-note {
            display: flex;
            justify-content: space-between;
            clear: both;
            margin-top: 0.035in;
            font-size: 6.4pt;
            font-weight: normal;
        }
        @media print {
            .toolbar { display: none; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .sj { margin: 0; }
            .page-break { margin-bottom: 0; }
        }
    </style>
</head>
<body>
<div class="toolbar">
    <button type="button" onclick="window.print()">Print Surat Jalan</button>
</div>

<?php foreach ($itemPages as $pageIndex => $pageItems): ?>
<?php $pageNo = $pageIndex + 1; ?>
<main class="sj<?= $pageNo < $totalPages ? ' page-break' : '' ?>">
    <section class="top">
        <div><img class="logo" src="<?= base_url('assets/images/company-logo.svg') ?>" alt="SML Rental"></div>
        <div class="company">
            <strong><?= esc($companyName) ?></strong>
            Forklift and Crane - Rental, Repair and Sales<br>
            Jl. Raya Cikarang - Cibarusah No. 150,<br>
            Cikarang Selatan Bekasi Telp. (021)<br>
            89902188
        </div>
        <div>
            <div class="date-box">Cikarang, <?= esc($printDate) ?></div>
            <div class="cust-box">
                <div><strong>Company</strong></div>
                <div><?= esc(op_sj_value($customer['name'] ?? null)) ?></div>
                <div><strong>Name</strong></div>
                <div><?= esc(op_sj_value($customer['location'] ?? null)) ?></div>
            </div>
        </div>
    </section>

    <section class="title">
        <strong>SURAT JALAN</strong>
        <span class="doc-kind"><?= esc(str_replace('SURAT JALAN - ', '', (string) ($sjContext['title'] ?? ''))) ?></span>
        <span class="num">
            <?= esc($sjNumber) ?>
            <?php if ($totalPages > 1): ?>
                /<?= esc((string) $pageNo) ?>
            <?php endif; ?>
        </span>
    </section>

    <section class="vehicle">
        <span><?= esc($movementText) ?></span>
        <span class="line"><?= esc($vehicleType) ?></span>
        <strong>No.</strong>
        <span class="line"><?= esc($vehiclePlate) ?></span>
    </section>

    <table class="items">
        <thead>
        <tr>
            <th class="qty">Banyaknya</th>
            <th class="name">Nama Barang</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($pageItems as $item): ?>
            <tr class="item-row">
                <td class="qty"><?= esc($item['print_qty'] ?? 1) ?> unit</td>
                <td class="name">
                    <strong><?= esc($item['print_name'] ?? '-') ?></strong>
                    <?php
                    $line = trim(implode(' ', array_filter([
                        !empty($item['print_no']) ? 'No. ' . $item['print_no'] : '',
                    ])));
                    $note = trim((string) ($item['keterangan'] ?? ''));
                    if ($note === '' && $sjMode === 'tarik') {
                        $note = $workflow['tujuan_label'] ?? '';
                    }
                    ?>
                    <?php if ($line !== ''): ?><span class="note"><?= esc($line) ?></span><?php endif; ?>
                    <?php if ($note !== ''): ?><span class="note">(<?= esc($note) ?>)</span><?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php for ($i = count($pageItems); $i < $maxRows; $i++): ?>
            <tr class="blank-row"><td>&nbsp;</td><td>&nbsp;</td></tr>
        <?php endfor; ?>
        </tbody>
    </table>

    <section class="bottom-area">
        <div class="copies">
            Putih : SML<br>
            Merah : Customer<br>
            Kuning : Admin
        </div>
        <div class="signs">
            <div>
                <strong>Tanda Terima</strong>
                <div class="sign-space"></div>
                <div class="sign-line"><?= esc($receiverName) ?></div>
            </div>
            <div>
                <strong>Hormat Kami,</strong>
                <div class="sign-space"></div>
                <div class="sign-line">&nbsp;</div>
            </div>
        </div>
    </section>

    <section class="footer-note">
        <span>DI: <?= esc($di['nomor_di'] ?? '-') ?></span>
        <?php if ($totalPages > 1): ?>
            <span>Hal <?= esc((string) $pageNo) ?>/<?= esc((string) $totalPages) ?></span>
        <?php endif; ?>
        <span><?= esc($sjContext['title'] ?? 'SURAT JALAN') ?></span>
    </section>
</main>
<?php endforeach; ?>

<script>
window.addEventListener('load', function () {
    document.title = <?= json_encode($sjNumber) ?>;
    setTimeout(function () { window.print(); }, 400);
});
</script>
</body>
</html>
