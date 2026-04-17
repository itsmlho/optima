<?php
$delivery = $delivery ?? [];
$groups   = $groups   ?? [];

$plNo      = esc($delivery['packing_list_no'] ?? '-');
$poNo      = esc($delivery['no_po']           ?? '-');
$supplier  = esc($delivery['nama_supplier']   ?? '-');
$drv       = esc($delivery['driver_name']     ?? '-');
$veh       = trim(($delivery['vehicle_info'] ?? '') . ' ' . ($delivery['vehicle_plate'] ?? '')) ?: '-';
$vehicle   = esc($veh);
$status    = esc($delivery['status']          ?? '-');
$date      = !empty($delivery['delivery_date']) ? date('d M Y', strtotime($delivery['delivery_date'])) : '-';
$printDate = date('d M Y, H:i');
$totalQty  = array_sum(array_column($groups, 'qty'));
$typeLabel = ['unit' => 'Unit', 'attachment' => 'Attachment', 'battery' => 'Battery', 'charger' => 'Charger'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Packing List - <?= $plNo ?></title>
<style>
@page { size: A4; margin: 12mm 10mm 15mm 10mm; }
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; font-size: 9pt; color: #000; background: #fff; }

.hdr { display: flex; justify-content: space-between; align-items: flex-start;
       border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 8px; }
.hdr-left { display: flex; align-items: center; gap: 12px; }
.hdr-logo  { width: 160px; height: auto; margin-right: 12px; }
.hdr-co .name   { font-size: 12pt; font-weight: bold; }
.hdr-co .sub    { font-size: 7.5pt; color: #555; margin-top: 2px; }
.hdr-co .addr   { font-size: 7pt; color: #666; margin-top: 2px; }
.hdr-meta table { border-collapse: collapse; }
.hdr-meta td    { border: 1px solid #bbb; padding: 3px 8px; font-size: 8.5pt; white-space: nowrap; }
.hdr-meta td:first-child { font-weight: bold; background: #f4f4f4; }

.doc-title { text-align: center; font-size: 13pt; font-weight: bold;
             letter-spacing: 2px; margin: 6px 0; text-transform: uppercase; }

.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 20px;
             border: 1px solid #ccc; padding: 6px 10px; margin-bottom: 8px; background: #fafafa; }
.info-row  { display: flex; gap: 4px; font-size: 8.5pt; padding: 2px 0; }
.info-row .lbl { width: 110px; font-weight: bold; color: #444; flex-shrink: 0; }

.items-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
.items-table th {
    background: #1a3a5c; color: #fff; padding: 5px 6px;
    text-align: left; border: 1px solid #1a3a5c; font-size: 8.5pt; }
.items-table th.center, .items-table td.center { text-align: center; }
.items-table td { border: 1px solid #ccc; padding: 5px 6px; vertical-align: top; }
.items-table tbody tr:nth-child(even) td { background: #f9f9f9; }
.col-no   { width: 28px; }
.col-type { width: 70px; }
.col-qty  { width: 36px; }
.col-sn   { width: 220px; }

.type-badge { display: inline-block; padding: 1px 6px; border-radius: 3px;
              font-size: 7.5pt; font-weight: bold; color: #fff; white-space: nowrap; }
.type-unit       { background: #1a6bab; }
.type-attachment { background: #7b4fa6; }
.type-battery    { background: #c47b0a; }
.type-charger    { background: #1a8a4a; }

.sn-list { list-style: none; padding: 0; margin: 0; }
.sn-list li { padding: 1px 0; font-size: 8pt; line-height: 1.5; }
.sn-list li .sn-num { color: #666; margin-right: 4px; }
.sn-empty { color: #aaa; font-style: italic; font-size: 8pt; }

.approval-section { margin-top: 20px; margin-bottom: 10px; }
.approval-title { font-weight: bold; font-size: 9pt; border-bottom: 1px solid #000;
                  padding-bottom: 3px; margin-bottom: 12px; text-transform: uppercase;
                  letter-spacing: 1px; }
.approval-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
.approval-col  { text-align: center; }
.approval-col .role  { font-weight: bold; font-size: 9pt; margin-bottom: 3px; }
.approval-col .desc  { font-size: 8pt; color: #666; margin-bottom: 10px; }
.approval-col .space { height: 44px; }
.approval-col .sig-line { border-bottom: 1px solid #000; margin: 4px 16px 2px 16px; }
.approval-col .sig-name { font-size: 8pt; color: #666; }
.approval-col .sig-date { font-size: 8pt; color: #666; margin-top: 4px; }

.print-footer { position: fixed; bottom: 0; left: 10mm; right: 10mm;
                padding-top: 4px; border-top: 1px solid #ccc;
                display: flex; justify-content: space-between; align-items: center;
                font-size: 7pt; color: #666; background: #fff; }
/* Push body content up so it doesn't get hidden behind fixed footer */
body { padding-bottom: 20px; }

@media print {
    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
</head>
<body>

<header class="hdr">
    <div class="hdr-left">
        <img src="<?= base_url('assets/images/company-logo.svg') ?>" class="hdr-logo"
             alt="Logo" onerror="this.style.display='none'">
        <div class="hdr-co">
            <div class="name">PT. SARANA MITRA LUAS Tbk</div>
            <div class="sub">FORKLIFT &amp; MATERIAL HANDLING EQUIPMENT SOLUTIONS</div>
            <div class="addr">Jl. Kenari Utama II Blk. C No.03 &amp; 05A, Cibatu, Kec. Cikarang Pusat, 17550</div>
            <div class="addr">Phone: (021) 3973-9988 &nbsp;|&nbsp; (021) 8990-2188</div>
        </div>
    </div>
    <div class="hdr-meta">
        <table>
            <tr><td>Packing List No</td><td><?= $plNo ?></td></tr>
            <tr><td>PO Number</td>      <td><?= $poNo ?></td></tr>
            <tr><td>Tanggal Kirim</td>  <td><?= $date ?></td></tr>
        </table>
    </div>
</header>

<div class="doc-title">Packing List</div>

<div class="info-grid">
    <div>
        <div class="info-row"><span class="lbl">Supplier</span><span>: <?= $supplier ?></span></div>
        <div class="info-row"><span class="lbl">Driver</span><span>: <?= $drv ?></span></div>
        <div class="info-row"><span class="lbl">Kendaraan</span><span>: <?= $vehicle ?></span></div>
    </div>
    <div>
        <div class="info-row"><span class="lbl">Tanggal Kirim</span><span>: <?= $date ?></span></div>
        <div class="info-row"><span class="lbl">Status</span><span>: <?= $status ?></span></div>
        <div class="info-row"><span class="lbl">Total Item</span><span>: <?= $totalQty ?> unit</span></div>
    </div>
</div>

<table class="items-table">
    <thead>
        <tr>
            <th class="col-no center">No</th>
            <th class="col-type center">Tipe</th>
            <th>Deskripsi / Spesifikasi Vendor (PI)</th>
            <th class="col-qty center">Qty</th>
            <th class="col-sn">Serial Number</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($groups)): ?>
        <tr><td colspan="5" style="text-align:center;color:#999;padding:16px;">
            Tidak ada item dalam packing list ini.
        </td></tr>
    <?php else: ?>
        <?php foreach ($groups as $i => $grp):
            $typeLower  = strtolower($grp['item_type'] ?? 'unit');
            $badgeClass = 'type-' . $typeLower;
            $typeText   = $typeLabel[$typeLower] ?? ucfirst($typeLower);
            $spec       = esc($grp['spec'] ?? $grp['item_name'] ?? '-');
            $qty        = (int)($grp['qty'] ?? 1);
            $sns        = $grp['serial_numbers'] ?? [];
        ?>
        <tr>
            <td class="center"><?= $i + 1 ?></td>
            <td class="center">
                <span class="type-badge <?= $badgeClass ?>"><?= $typeText ?></span>
            </td>
            <td><?= $spec ?></td>
            <td class="center"><strong><?= $qty ?></strong></td>
            <td>
                <?php if (empty(array_filter($sns, fn($s) => trim($s) !== ''))): ?>
                    <span class="sn-empty">Belum ada SN</span>
                <?php else: ?>
                    <ul class="sn-list">
                    <?php foreach ($sns as $j => $sn): ?>
                        <li>
                            <span class="sn-num"><?= $j + 1 ?>.</span>
                            <?php if (trim((string)$sn) !== ''): ?>
                                <?= esc($sn) ?>
                            <?php else: ?>
                                <span class="sn-empty">â€”</span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" style="text-align:right;font-weight:bold;padding:5px 8px;">Total</td>
            <td class="center"><strong><?= $totalQty ?></strong></td>
            <td></td>
        </tr>
    </tfoot>
</table>

<div class="approval-section">
    <div class="approval-grid">
        <div class="approval-col">
            <div class="role">Purchasing</div>
            <div class="desc">Pembuat Packing List</div>
            <div class="space"></div>
            <div class="sig-line"></div>
            <div class="sig-name">(...........................)</div>
            <div class="sig-date">Tanggal: __________</div>
        </div>
        <div class="approval-col">
            <div class="role">Admin Warehouse</div>
            <div class="desc">Verifikator Penerimaan</div>
            <div class="space"></div>
            <div class="sig-line"></div>
            <div class="sig-name">(...........................)</div>
            <div class="sig-date">Tanggal: __________</div>
        </div>
        <div class="approval-col">
            <div class="role">Head Warehouse</div>
            <div class="desc">Mengetahui</div>
            <div class="space"></div>
            <div class="sig-line"></div>
            <div class="sig-name">(...........................)</div>
            <div class="sig-date">Tanggal: __________</div>
        </div>
    </div>
</div>

<div class="print-footer">
    <div style="text-align:left;flex:1;"><strong>PT SARANA MITRA LUAS Tbk</strong> | <span style="color:#888;">Sistem OPTIMA - Document Management</span></div>
    <div style="text-align:center;flex:1;">Tanggal Cetak: <?= date('d/m/Y H:i') ?></div>
    <div style="text-align:right;flex:1;">Packing List: <?= $plNo ?> | PO: <?= $poNo ?></div>
</div>

<script>
    function initiatePrint() {
        setTimeout(function() {
            try { window.print(); } catch(e) {}
        }, 500);
    }
    if (document.readyState === 'complete') {
        initiatePrint();
    } else {
        window.addEventListener('load', initiatePrint);
    }
    window.addEventListener('afterprint', function() {
        setTimeout(function() { window.close(); }, 100);
    });
</script>
</body>
</html>