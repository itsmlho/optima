<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; background: #fff; color: #111; }
        .print-header { border-bottom: 3px solid #0d6efd; padding-bottom: 12px; margin-bottom: 20px; }
        .print-header .company { font-size: 11px; color: #555; }
        .section-title { font-size: 11px; text-transform: uppercase; letter-spacing: .6px; color: #6c757d; font-weight: 700; border-bottom: 1px solid #dee2e6; padding-bottom: 4px; margin: 16px 0 10px; }
        .info-table td { padding: 4px 8px; vertical-align: top; }
        .info-table td:first-child { width: 38%; color: #555; font-weight: 500; }
        .info-table td:last-child { font-weight: 600; }
        .badge-status { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #0d6efd; color: #fff; }
        .unit-id { font-size: 22px; font-weight: 800; color: #212529; }
        .unit-subtitle { font-size: 13px; color: #6c757d; }
        .qr-placeholder { width: 80px; height: 80px; border: 2px dashed #adb5bd; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #adb5bd; text-align: center; }
        .acc-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; background: #e9ecef; font-size: 11px; margin: 2px; }
        .footer-note { font-size: 10px; color: #aaa; margin-top: 24px; border-top: 1px solid #e9ecef; padding-top: 8px; }
        @media print {
            body { margin: 0; padding: 12px 16px; }
            .no-print { display: none !important; }
            a { text-decoration: none; color: inherit; }
            @page { margin: 1.5cm; }
        }
    </style>
</head>
<body>
<?php
$unitNo = $unit['no_unit'] ?: ($unit['no_unit_na'] ?: 'TEMP-' . $unit['id_inventory_unit']);
$brand  = ($unit['merk_unit'] ?? '') . ' ' . ($unit['model_unit'] ?? '');
$statusLabel = $unit['status_unit_name'] ?? 'Unknown';
$now = date('d M Y, H:i');
?>

<!-- Print / Back Bar (hidden on print) -->
<div class="no-print mb-3 d-flex gap-2 align-items-center">
    <button class="btn btn-primary btn-sm" onclick="window.print()"><i class="fas fa-print me-1"></i>Print</button>
    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">← Back</a>
    <span class="text-muted small ms-auto">Printed: <?= $now ?></span>
</div>

<div class="container-fluid" style="max-width:860px; margin:0 auto;">

    <!-- Header -->
    <div class="print-header d-flex justify-content-between align-items-start">
        <div>
            <div class="unit-id"><?= esc($unitNo) ?></div>
            <div class="unit-subtitle"><?= esc(trim($brand)) ?></div>
            <div class="mt-1">
                <span class="badge-status" style="background:<?= in_array($unit['status_unit_id'],[1,5]) ? '#198754' : (in_array($unit['status_unit_id'],[8]) ? '#dc3545' : '#fd7e14') ?>">
                    <?= esc($statusLabel) ?>
                </span>
                &nbsp;
                <span style="font-size:11px; color:#555;">S/N: <strong><?= esc($unit['serial_number'] ?? 'N/A') ?></strong></span>
            </div>
        </div>
        <div class="text-end">
            <?php if (!empty($public_view_url)): ?>
                <img
                    src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=<?= rawurlencode($public_view_url) ?>"
                    alt="QR public unit view"
                    style="width:80px;height:80px;border:1px solid #dee2e6;padding:4px;background:#fff;"
                >
            <?php else: ?>
                <div class="qr-placeholder">QR<br>Code</div>
            <?php endif; ?>
            <div style="font-size:10px; color:#aaa; margin-top:4px;">INT-<?= esc($unit['id_inventory_unit']) ?></div>
            <?php if (!empty($public_view_url)): ?>
                <div style="font-size:9px; margin-top:2px;">Scan: <?= esc($public_view_url) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Left column -->
        <div class="col-6">
            <div class="section-title">Identification</div>
            <table class="info-table w-100">
                <tr><td>Unit No. (Asset)</td><td><?= esc($unit['no_unit'] ?: '—') ?></td></tr>
                <tr><td>Unit No. NA</td><td><?= esc($unit['no_unit_na'] ?: '—') ?></td></tr>
                <tr><td>Serial Number</td><td><?= esc($unit['serial_number'] ?: '—') ?></td></tr>
                <tr><td>Year of Make</td><td><?= esc($unit['tahun_unit'] ?: 'N/A') ?></td></tr>
                <tr><td>Registration Date</td><td><?= !empty($unit['created_at']) ? date('d M Y', strtotime($unit['created_at'])) : '—' ?></td></tr>
                <tr><td>Delivery Date</td><td><?= !empty($unit['tanggal_kirim']) ? date('d M Y', strtotime($unit['tanggal_kirim'])) : '—' ?></td></tr>
            </table>

            <div class="section-title">Assignment</div>
            <table class="info-table w-100">
                <?php
                    // Some controller fallbacks may expose `unit_departemen` instead of `nama_departemen`.
                    $deptName = $unit['nama_departemen'] ?? ($unit['unit_departemen'] ?? null);
                ?>
                <tr><td>Department</td><td><?= esc($deptName ?: 'Unassigned') ?></td></tr>
                <tr><td>Location</td><td><?= esc($unit['lokasi_unit'] ?: 'Internal Warehouse') ?></td></tr>
                <?php if (!empty($unit['customer_name'])): ?>
                <tr><td>Customer</td><td><?= esc($unit['customer_name']) ?></td></tr>
                <tr><td>Site</td><td><?= esc($unit['customer_location_name'] ?? '—') ?></td></tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Right column -->
        <div class="col-6">
            <div class="section-title">Engine & Power</div>
            <table class="info-table w-100">
                <tr><td>Brand</td><td><?= esc($unit['merk_unit'] ?? '—') ?></td></tr>
                <tr><td>Model</td><td><?= esc($unit['model_unit'] ?? '—') ?></td></tr>
                <tr><td>Unit Type</td><td><?= esc(trim(($unit['nama_tipe_unit'] ?? '') . ' ' . ($unit['jenis'] ?? ''))) ?: '—' ?></td></tr>
                <tr><td>Capacity</td><td><?= esc($unit['kapasitas_display'] ?? '—') ?></td></tr>
                <tr><td>Fuel Type</td><td><?= esc($unit['fuel_type'] ?? '—') ?></td></tr>
                <tr><td>Engine Model</td><td><?= esc($unit['model_mesin'] ?? '—') ?></td></tr>
                <tr><td>Engine S/N</td><td><?= esc($unit['sn_mesin'] ?: '—') ?></td></tr>
            </table>

            <div class="section-title">Mast & Chassis</div>
            <table class="info-table w-100">
                <tr><td>Mast Type</td><td><?= esc($unit['tipe_mast'] ?? '—') ?></td></tr>
                <tr><td>Mast Height (mm)</td><td><?= esc($unit['tinggi_mast'] ?: '—') ?></td></tr>
                <tr><td>Mast S/N</td><td><?= esc($unit['sn_mast'] ?: '—') ?></td></tr>
            </table>
        </div>
    </div>

    <!-- Accessories row -->
    <?php if (!empty($aksesorisItems)): ?>
    <div class="section-title">Accessories / Attachments</div>
    <div>
        <?php foreach ($aksesorisItems as $item): ?>
        <span class="acc-badge"><?= esc($item) ?></span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Notes -->
    <?php if (!empty($unit['keterangan'])): ?>
    <div class="section-title">Notes</div>
    <p style="font-size:12px; color:#333; margin:0;"><?= nl2br(esc($unit['keterangan'])) ?></p>
    <?php endif; ?>

    <div class="footer-note d-flex justify-content-between">
        <span>Generated: <?= $now ?> &nbsp;|&nbsp; Optima Asset Management</span>
        <span>Unit ID: INT-<?= esc($unit['id_inventory_unit']) ?></span>
    </div>

</div>

<script>
    // Auto-trigger print dialog when opened in a new tab
    window.addEventListener('DOMContentLoaded', function() {
        // Small delay so browser renders fonts / CSS first
        setTimeout(function() { window.print(); }, 600);
    });
</script>
</body>
</html>
