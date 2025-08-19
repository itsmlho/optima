<?php
$di = $di ?? [];
$spk = $spk ?? [];
$s = $spesifikasi ?? [];
$status = strtoupper((string)($di['status'] ?? ''));
$placeholder = ($status === 'DIAJUKAN' || $status === 'SUBMITTED');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Instruction</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.1/css/bootstrap.min.css" integrity="sha512-Ez0cGzNzHR1tYAv56860NLspgUGuQw16GiOOp/I2LuTmpSK9xDXlgJz3XN4cnpXWDmkNBKXR/VDMTCnAaEooxA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @page { size: A4; margin: 10mm; }
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            font-size: 11px; 
            color: #212529;
            background-color: #FFF;
        }
        .header-title { font-size: 18px; font-weight: bold; text-align:center; margin-bottom: 2px; color: #000; }
        .header-subtitle { font-size: 16px; text-align:center; color:#495057; margin-bottom:15px; }
        .logo { max-height: 50px; }

        .section-box {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
        }
        .section-header {
            background-color: #f8f9fa;
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
            font-size: 12px;
            color: #000;
        }
        .section-body {
            padding: 0.75rem;
        }

        .label { color:#6c757d; font-size: 10px; }
        .value { color:#212529; font-weight: 600; }
        .info-pair { margin-bottom: 0.5rem; }

        .sig-section { text-align:center; }
        .sig-title { color:#6c757d; }
        .sig-stamp { 
            transform: rotate(-15deg); 
            opacity: 0.8; 
            font-size: 10px; 
            color: #dc2626; 
            border: 2px solid #dc2626; 
            padding: 5px 10px; 
            border-radius: 5px; 
            display: inline-block; 
            margin-top: 8px;
            margin-bottom: 8px;
        }
        .sig-name { font-weight: bold; color: #000; }
        .sig-placeholder {
            padding-top: 40px;
            color: #adb5bd;
        }

    </style>
</head>
<body onload="window.print()" onafterprint="window.close()">

<div class="container-fluid">
    <!-- SECTION: Header -->
    <div class="row align-items-center mb-3">
        <div class="col-4 d-flex align-items-center">
            <img src="<?= base_url('assets/images/company-logo.svg') ?>" class="logo me-2" alt="logo"/>
        </div>
        <div class="col-4">
            <div class="header-title">PT. SARANA MITRA LUAS</div>
            <div class="header-subtitle">DELIVERY INSTRUCTION</div>
        </div>
        <div class="col-4 text-end" style="font-size: 9px; color: #6c757d;">
            <?php if (!empty($di['created_at'])): ?>Dibuat pada: <?= esc($di['created_at']) ?><br><?php endif; ?>
            <?php if (!empty($di['updated_at'])): ?>Diperbarui pada: <?= esc($di['updated_at']) ?><?php endif; ?>
        </div>
    </div>

    <!-- SECTION: Informasi Umum -->
    <div class="section-box">
        <div class="section-header">Informasi Umum</div>
        <div class="section-body">
            <div class="row">
                <div class="col-6 info-pair"><span class="label">No DI:</span><br><span class="value"><?= esc($di['nomor_di'] ?? '-') ?></span></div>
                <div class="col-6 info-pair"><span class="label">No SPK:</span><br><span class="value"><?= esc($spk['nomor_spk'] ?? $spk['no_spk'] ?? '-') ?></span></div>
                <div class="col-6 info-pair"><span class="label">Kontrak/PO:</span><br><span class="value"><?= esc($di['po_kontrak_nomor'] ?? $spk['po_kontrak_nomor'] ?? $spk['kontrak_no'] ?? '-') ?></span></div>
                <div class="col-6 info-pair"><span class="label">Status:</span><br><span class="value"><?= esc($status) ?></span></div>
                <div class="col-6 info-pair"><span class="label">Nama Perusahaan:</span><br><span class="value"><?= esc($di['pelanggan'] ?? $spk['pelanggan'] ?? $spk['customer_name'] ?? '-') ?></span></div>
                <div class="col-6 info-pair"><span class="label">PIC:</span><br><span class="value"><?= esc($spk['pic'] ?? '-') ?></span></div>
                <div class="col-6 info-pair"><span class="label">Contact Person:</span><br><span class="value"><?= esc($spk['kontak'] ?? '-') ?></span></div>
                <div class="col-6 info-pair"><span class="label">Alamat Pengiriman:</span><br><span class="value"><?= esc($di['lokasi'] ?? $spk['lokasi'] ?? '-') ?></span></div>
            </div>
        </div>
    </div>

    <!-- SECTION: Rencana Pengiriman -->
    <div class="section-box">
        <div class="section-header">Rencana Pengiriman</div>
        <div class="section-body">
            <div class="row">
                <div class="col-6 info-pair"><span class="label">Tanggal Kirim:</span><br><span class="value"><?= esc($di['tanggal_kirim'] ?? '-') ?></span></div>
                <div class="col-6 info-pair"><span class="label">Estimasi Sampai:</span><br><span class="value"><?= esc($di['estimasi_sampai'] ?? '-') ?></span></div>
                <div class="col-6 info-pair"><span class="label">Kendaraan & No Polisi:</span><br><span class="value"><?= esc($di['kendaraan'] ?? '-') ?> / <?= esc($di['no_polisi_kendaraan'] ?? '-') ?></span></div>
                <div class="col-6 info-pair"><span class="label">Nama Supir:</span><br><span class="value"><?= esc($di['nama_supir'] ?? '-') ?></span></div>
                <div class="col-6 info-pair"><span class="label">No SIM Supir:</span><br><span class="value"><?= esc($di['no_sim_supir'] ?? '-') ?></span></div>
                <div class="col-6 info-pair"><span class="label">No HP Supir:</span><br><span class="value"><?= esc($di['no_hp_supir'] ?? '-') ?></span></div>
            </div>
        </div>
    </div>

    <!-- SECTION: Detail Unit Disiapkan (dari SPK) -->
    <div class="section-box">
        <div class="section-header">Detail Unit Disiapkan (dari SPK)</div>
        <div class="section-body">
            <?php
                $unit = $s['selected']['unit'] ?? null; 
                $attachment = $s['selected']['attachment'] ?? null;
                $aksesoris = (!empty($spk['persiapan_aksesoris_tersedia']) ? $spk['persiapan_aksesoris_tersedia'] : (is_array($s['aksesoris'] ?? null) ? implode(', ', $s['aksesoris']) : ($s['aksesoris'] ?? '')));

                // Debug: show data structure in HTML comments
                echo '<!-- DEBUG DI STATUS: ' . $status . ' -->';
                echo '<!-- DEBUG PLACEHOLDER: ' . ($placeholder ? 'true' : 'false') . ' -->';
                echo '<!-- DEBUG SPK KEYS: ' . implode(', ', array_keys($spk)) . ' -->';
                echo '<!-- DEBUG DI KEYS: ' . implode(', ', array_keys($di)) . ' -->';
                echo '<!-- DEBUG SPESIFIKASI KEYS: ' . implode(', ', array_keys($s)) . ' -->';

                $details = [
                    ['ID Unit', $unit['no_unit'] ?? ''], ['Serial Number', $unit['serial_number'] ?? ''],
                    ['Merk', $unit['merk_unit'] ?? ''], ['Model', $unit['model_unit'] ?? ''],
                    ['Jenis Unit', $unit['jenis_unit'] ?? $s['jenis_unit'] ?? ''], ['Tipe Unit', $unit['tipe_jenis'] ?? $s['tipe_jenis'] ?? ''],
                    ['Kapasitas', $unit['kapasitas_name'] ?? $s['kapasitas_id_name'] ?? ''], ['Mast', $s['mast_id_name'] ?? ''],
                    ['SN Attachment', $attachment['sn_attachment_formatted'] ?? ''], ['SN Mast', $unit['sn_mast_formatted'] ?? ''],
                    ['SN Baterai', $unit['sn_baterai_formatted'] ?? ''], ['SN Charger', $unit['sn_charger_formatted'] ?? ''],
                    ['Valve', $s['valve_id_name'] ?? ''], ['Roda', $s['roda_id_name'] ?? ''],
                    ['Ban', $s['ban_id_name'] ?? ''], ['Aksesoris', $aksesoris],
                ];
            ?>
            <div class="row">
                <?php foreach($details as $detail): ?>
                <div class="col-6 info-pair">
                    <span class="label"><?= esc($detail[0]) ?>:</span><br>
                    <span class="value"><?= esc($placeholder ? '..................................................' : ($detail[1] ?: '-')) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- SECTION: Catatan -->
    <div class="mb-3">
        <strong>Catatan Delivery:</strong> 
        <span class="value"><?= esc($di['catatan_sampai'] ?: '..................................................') ?></span>
    </div>

    <!-- SECTION: Tanda Tangan -->
    <div class="row mt-4">
        <div class="col sig-section">
            <div class="sig-title">Marketing</div>
            <?php 
                // Get marketing approval from SPK created_by or marketing name
                $marketingName = $spk['created_by_name'] ?? $spk['marketing_name'] ?? $spk['created_by'] ?? '';
                $marketingApproved = !empty($marketingName);
                
                if ($marketingApproved) {
                    echo '<div class="sig-stamp">APPROVED</div>';
                    echo '<div class="sig-name">(' . esc($marketingName) . ')</div>';
                } else {
                    echo '<div class="sig-placeholder">(.........................)</div>';
                }
            ?>
        </div>
        <div class="col sig-section">
            <div class="sig-title">Bag. PDI Pengecekan</div>
            <?php if (!empty($spk['pdi_tanggal_approve'])): ?>
                <div class="sig-stamp">APPROVED</div>
                <div class="sig-name">(<?= esc($spk['pdi_mekanik'] ?? '') ?>)</div>
            <?php else: ?>
                <div class="sig-placeholder">(..........................)</div>
            <?php endif; ?>
        </div>
        <div class="col sig-section">
            <div class="sig-title">Bag. Delivery</div>
            <?php if (!empty($di['perencanaan_tanggal_approve'])): ?>
                <div class="sig-stamp">APPROVED</div>
                <div class="sig-name">(T Herry Christian)</div>
            <?php else: ?>
                <div class="sig-placeholder">(..........................)</div>
            <?php endif; ?>
        </div>
    </div>

</div>

</body>
</html>
