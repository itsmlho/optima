<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Asset View') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background: #f0f4f8; font-family: 'Segoe UI', sans-serif; }
        .asset-card { max-width: 680px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,.10); overflow: hidden; }
        .asset-header { background: linear-gradient(135deg, #1e3a5f 0%, #2d6a9f 100%); color: #fff; padding: 28px 28px 20px; }
        .asset-header .type-badge { background: rgba(255,255,255,.18); border-radius: 8px; padding: 4px 12px; font-size: .75rem; letter-spacing: .08em; text-transform: uppercase; display: inline-block; margin-bottom: 8px; }
        .asset-header h4 { margin: 0; font-size: 1.4rem; font-weight: 700; letter-spacing: .02em; }
        .asset-header .subtitle { opacity: .75; font-size: .88rem; margin-top: 4px; }
        .status-badge { font-size: .78rem; padding: 3px 10px; border-radius: 20px; font-weight: 600; display: inline-block; }
        .status-AVAILABLE { background: #d1fae5; color: #065f46; }
        .status-IN_USE { background: #dbeafe; color: #1e40af; }
        .status-SPARE { background: #e0e7ff; color: #3730a3; }
        .status-MAINTENANCE { background: #fef3c7; color: #92400e; }
        .status-BROKEN { background: #fee2e2; color: #991b1b; }
        .status-RESERVED { background: #f3f4f6; color: #374151; }
        .status-default { background: #f3f4f6; color: #374151; }
        .section-title { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .09em; color: #6b7280; margin-bottom: 10px; padding-bottom: 4px; border-bottom: 1px solid #e5e7eb; }
        .info-row { display: flex; padding: 7px 0; border-bottom: 1px solid #f3f4f6; align-items: flex-start; }
        .info-row:last-child { border-bottom: none; }
        .info-key { width: 140px; min-width: 140px; color: #6b7280; font-size: .82rem; }
        .info-val { flex: 1; font-size: .88rem; font-weight: 500; word-break: break-word; }
        .qr-box { text-align: center; padding: 24px 16px; }
        .qr-box img { width: 150px; height: 150px; border: 4px solid #e5e7eb; border-radius: 8px; }
        .qr-url { font-size: .72rem; color: #9ca3af; word-break: break-all; margin-top: 8px; }
        .unit-block { background: #eff6ff; border-left: 4px solid #3b82f6; border-radius: 8px; padding: 14px 16px; margin-top: 4px; }
        .footer-note { text-align: center; font-size: .72rem; color: #9ca3af; padding: 16px; }
        @media print { body { background: #fff; } .asset-card { box-shadow: none; margin: 0; border-radius: 0; } .no-print { display: none !important; } }
    </style>
</head>
<body>
<?php
    $c        = $component ?? [];
    $tipe     = $tipe_item ?? 'item';
    $unit     = $unit_info ?? null;
    $pubUrl   = $public_url ?? '';
    $typeIcons = ['attachment' => 'fa-puzzle-piece', 'battery' => 'fa-battery-half', 'charger' => 'fa-plug', 'fork' => 'fa-grip-lines-vertical'];
    $typeIcon  = $typeIcons[$tipe] ?? 'fa-box';
    $typeLabel = ucfirst($tipe);
    $itemNum   = $c['item_number'] ?? $c['no_item'] ?? ('ID #' . ($c['id'] ?? '?'));
    $sn        = $c['serial_number'] ?? $c['sn_attachment'] ?? $c['sn_baterai'] ?? $c['sn_charger'] ?? $c['sn_fork'] ?? null;
    $status    = $c['status'] ?? $c['attachment_status'] ?? 'UNKNOWN';
    $condition = $c['physical_condition'] ?? $c['kondisi_fisik'] ?? null;
    $condMap   = ['GOOD' => 'Baik', 'MINOR_DAMAGE' => 'Minor Damage', 'MAJOR_DAMAGE' => 'Rusak Berat'];
    $condLabel = $condMap[$condition] ?? $condition;
    $location  = $c['storage_location'] ?? $c['lokasi_penyimpanan'] ?? null;
    $completeness = $c['completeness'] ?? $c['kelengkapan'] ?? null;
    $receivedAt   = $c['received_at'] ?? $c['tanggal_masuk'] ?? null;
    $updatedAt    = $c['updated_at'] ?? null;
    $fmt = function($d) {
        if (!$d) return '-';
        try { return (new DateTime($d))->format('d M Y'); } catch (\Exception $e) { return $d; }
    };
    $statusClass = 'status-' . (str_replace([' '], '_', $status));
    $qrUrl = $pubUrl ? 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($pubUrl) : null;
?>
<div class="asset-card">
    <!-- Header -->
    <div class="asset-header">
        <div class="type-badge"><i class="fas <?= esc($typeIcon) ?> me-1"></i><?= esc($typeLabel) ?></div>
        <h4><?= esc($itemNum) ?></h4>
        <div class="subtitle"><?= $sn ? esc($sn) : '' ?></div>
        <div class="mt-2">
            <span class="status-badge <?= esc($statusClass) ?>"><?= esc($status) ?></span>
            <?php if ($condLabel): ?>
            <span class="status-badge ms-1" style="background:rgba(255,255,255,.15);color:#fff;"><?= esc($condLabel) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Body -->
    <div class="p-4">

        <!-- Asset Info -->
        <div class="section-title"><i class="fas fa-box me-1"></i>Informasi Aset</div>
        <div class="mb-4">
            <div class="info-row"><span class="info-key">Item Number</span><span class="info-val fw-bold font-monospace"><?= esc($itemNum) ?></span></div>
            <?php if ($sn): ?>
            <div class="info-row"><span class="info-key">Serial Number</span><span class="info-val font-monospace"><?= esc($sn) ?></span></div>
            <?php endif; ?>
            <?php if ($location): ?>
            <div class="info-row"><span class="info-key">Lokasi</span><span class="info-val"><?= esc($location) ?></span></div>
            <?php endif; ?>
            <?php if ($completeness): ?>
            <div class="info-row"><span class="info-key">Kelengkapan</span><span class="info-val"><?= esc($completeness) ?></span></div>
            <?php endif; ?>
            <?php if ($receivedAt): ?>
            <div class="info-row"><span class="info-key">Tanggal Masuk</span><span class="info-val"><?= esc($fmt($receivedAt)) ?></span></div>
            <?php endif; ?>
            <?php if ($updatedAt): ?>
            <div class="info-row"><span class="info-key">Terakhir Update</span><span class="info-val"><?= esc($fmt($updatedAt)) ?></span></div>
            <?php endif; ?>
        </div>

        <!-- Type-Specific Specs -->
        <?php if ($tipe === 'attachment'): ?>
        <?php if ($c['merk'] ?? $c['attachment_merk'] ?? null): ?>
        <div class="section-title"><i class="fas fa-puzzle-piece me-1"></i>Spesifikasi Attachment</div>
        <div class="mb-4">
            <div class="info-row"><span class="info-key">Merk</span><span class="info-val"><?= esc($c['merk'] ?? $c['attachment_merk'] ?? '-') ?></span></div>
            <div class="info-row"><span class="info-key">Tipe</span><span class="info-val"><?= esc($c['tipe'] ?? $c['attachment_tipe'] ?? '-') ?></span></div>
            <div class="info-row"><span class="info-key">Model</span><span class="info-val"><?= esc($c['model'] ?? $c['attachment_model'] ?? '-') ?></span></div>
            <?php if ($c['max_capacity'] ?? null): ?><div class="info-row"><span class="info-key">Kapasitas Maks</span><span class="info-val"><?= esc($c['max_capacity']) ?></span></div><?php endif; ?>
        </div>
        <?php endif; ?>
        <?php elseif ($tipe === 'battery'): ?>
        <?php if ($c['merk_baterai'] ?? null): ?>
        <div class="section-title"><i class="fas fa-battery-half me-1"></i>Spesifikasi Battery</div>
        <div class="mb-4">
            <div class="info-row"><span class="info-key">Merk</span><span class="info-val"><?= esc($c['merk_baterai'] ?? '-') ?></span></div>
            <div class="info-row"><span class="info-key">Tipe</span><span class="info-val"><?= esc($c['tipe_baterai'] ?? '-') ?></span></div>
            <div class="info-row"><span class="info-key">Jenis</span><span class="info-val"><?= esc($c['jenis_baterai'] ?? '-') ?></span></div>
            <?php if ($c['voltage'] ?? null): ?><div class="info-row"><span class="info-key">Voltage</span><span class="info-val"><?= esc($c['voltage']) ?></span></div><?php endif; ?>
            <?php if ($c['ampere'] ?? null): ?><div class="info-row"><span class="info-key">Ampere</span><span class="info-val"><?= esc($c['ampere']) ?></span></div><?php endif; ?>
        </div>
        <?php endif; ?>
        <?php elseif ($tipe === 'charger'): ?>
        <?php if ($c['merk_charger'] ?? null): ?>
        <div class="section-title"><i class="fas fa-plug me-1"></i>Spesifikasi Charger</div>
        <div class="mb-4">
            <div class="info-row"><span class="info-key">Merk</span><span class="info-val"><?= esc($c['merk_charger'] ?? '-') ?></span></div>
            <div class="info-row"><span class="info-key">Tipe</span><span class="info-val"><?= esc($c['tipe_charger'] ?? '-') ?></span></div>
            <?php if ($c['input_voltage'] ?? null): ?><div class="info-row"><span class="info-key">Input Voltage</span><span class="info-val"><?= esc($c['input_voltage']) ?></span></div><?php endif; ?>
            <?php if ($c['output_voltage'] ?? null): ?><div class="info-row"><span class="info-key">Output Voltage</span><span class="info-val"><?= esc($c['output_voltage']) ?></span></div><?php endif; ?>
        </div>
        <?php endif; ?>
        <?php elseif ($tipe === 'fork'): ?>
        <?php if ($c['fork_spec_name'] ?? $c['fork_name'] ?? null): ?>
        <div class="section-title"><i class="fas fa-grip-lines-vertical me-1"></i>Spesifikasi Fork</div>
        <div class="mb-4">
            <div class="info-row"><span class="info-key">Spec Name</span><span class="info-val"><?= esc($c['fork_spec_name'] ?? $c['fork_name'] ?? '-') ?></span></div>
            <div class="info-row"><span class="info-key">Class</span><span class="info-val"><?= esc($c['fork_class'] ?? '-') ?></span></div>
            <?php if ($c['length_mm'] ?? null): ?><div class="info-row"><span class="info-key">Panjang</span><span class="info-val"><?= esc($c['length_mm']) ?> mm</span></div><?php endif; ?>
            <?php if ($c['width_mm'] ?? null): ?><div class="info-row"><span class="info-key">Lebar</span><span class="info-val"><?= esc($c['width_mm']) ?> mm</span></div><?php endif; ?>
            <?php if ($c['capacity_kg'] ?? null): ?><div class="info-row"><span class="info-key">Kapasitas</span><span class="info-val"><?= esc($c['capacity_kg']) ?> kg</span></div><?php endif; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <!-- Unit Assignment -->
        <?php if ($unit): ?>
        <div class="section-title"><i class="fas fa-link me-1"></i>Terpasang di Unit</div>
        <div class="unit-block mb-4">
            <div class="info-row"><span class="info-key">No. Unit</span><span class="info-val fw-bold font-monospace"><?= esc($unit['no_unit'] ?? '-') ?></span></div>
            <div class="info-row"><span class="info-key">Merk / Model</span><span class="info-val"><?= esc(($unit['merk_unit'] ?? '') . ' ' . ($unit['model_unit'] ?? '')) ?></span></div>
            <?php if ($unit['unit_serial_number'] ?? null): ?>
            <div class="info-row"><span class="info-key">S/N Unit</span><span class="info-val font-monospace"><?= esc($unit['unit_serial_number']) ?></span></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- QR Code -->
        <?php if ($qrUrl): ?>
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light d-flex align-items-center justify-content-between">
                <h6 class="mb-0"><i class="fas fa-qrcode me-2"></i><strong>Barcode Aset</strong></h6>
                <span class="badge bg-dark">Public</span>
            </div>
            <div class="card-body p-3 small">
                <div class="text-center border rounded p-2">
                    <img src="<?= esc($qrUrl) ?>" alt="QR Code" style="width:160px;height:160px;">
                    <div class="mt-2">
                        <a href="<?= esc($pubUrl) ?>" target="_blank" class="btn btn-sm btn-dark me-1">
                            <i class="fas fa-link me-1"></i>Link
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- /p-4 -->

    <div class="footer-note">
        Data ini hanya untuk keperluan referensi. &bull; Optima Warehouse System
        <br><span class="text-muted">Diakses: <?= date('d M Y H:i') ?></span>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
