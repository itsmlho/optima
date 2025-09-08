<?php
$di = $di ?? [];
$spk = $spk ?? [];
$s = $spesifikasi ?? [];
$items = $items ?? [];
$unit_item = $unit_item ?? null; // Data unit yang specific untuk DI ini
$status = strtoupper((string)($di['status'] ?? ''));
$placeholder = ($status === 'SUBMITTED' || $status === 'DIAJUKAN');

// Extract unit information from items if unit_item is not provided
if (!$unit_item && !empty($items)) {
    foreach ($items as $item) {
        if ($item['item_type'] === 'UNIT') {
            $unit_item = $item;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Instruction - <?= esc($di['nomor_di'] ?? 'DI') ?></title>
    <!-- Disable favicon and print headers -->
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64,">
    <link rel="shortcut icon" type="image/x-icon" href="data:image/x-icon;base64,">
    <meta name="format-detection" content="telephone=no">
    <meta name="format-detection" content="address=no">
    <meta name="format-detection" content="email=no">
    <meta name="robots" content="noindex, nofollow">
    <meta name="print-option" content="no-header-footer">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.1/css/bootstrap.min.css" integrity="sha512-Ez0cGzNzHR1tYAv56860NLspgUGuQw16GiOOp/I2LuTmpSK9xDXlgJz3XN4cnpXWDmkNBKXR/VDMTCnAaEooxA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @page { 
            size: A4; 
            margin: 15mm;
        }
        
        @media print {
            @page {
                margin: 10mm;
                size: A4;
                @top-left { content: ""; }
                @top-center { content: ""; }
                @top-right { content: ""; }
                @bottom-left { content: ""; }
                @bottom-center { content: ""; }
                @bottom-right { content: ""; }
            }
            
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            #printInstruction {
                display: none !important;
            }
        }
        
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 11px; 
            color: #222;
            background-color: #FFF;
        }
        
        .table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        .table th, .table td { 
            border: 1px solid #9aa1a7; 
            padding: .5rem .6rem; 
            vertical-align: top; 
            line-height: 1.4;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        .table td {
            min-height: 25px;
        }
        
        .muted { color: #666; }
        .sig { text-align: center; }
        .sig-stamp { 
            transform: rotate(-15deg); 
            opacity: 0.7; 
            font-size: 10px; 
            color: #dc2626; 
            border: 2px solid #dc2626; 
            padding: 5px 10px; 
            border-radius: 5px; 
            display: inline-block; 
            margin: 5px 0; 
        }
        .sig-name { font-weight: bold; color: #111; }
        .title { font-size: 16px; font-weight: bold; margin: 0; }
        .subtitle { font-size: 15px; color: #555; margin: 0; }
        .label { color: #374151; }
        .val { color: #111827; font-weight: 600; }
        .logo { max-height: 46px; }
        .header { 
            display: grid; 
            grid-template-columns: auto 1fr auto; 
            align-items: center; 
            column-gap: 10px; 
        }
        .header-center { text-align: center; }
        .header-meta { 
            font-size: 10px; 
            color: #6b7280; 
            text-align: right; 
        }
        
        .unit-card { 
            border: 1px solid #9aa1a7; 
            padding: 10px; 
            margin-bottom: 15px; 
            margin-top: 10px;
            page-break-inside: avoid;
            clear: both;
        }
        .unit-title { 
            background: #f8fafc; 
            font-weight: bold; 
            padding: 6px 8px; 
            border-bottom: 1px solid #9aa1a7; 
            margin: -10px -10px 10px;
        }
        
        .signature-section {
            page-break-inside: avoid;
            margin-top: 20px;
            padding-top: 15px;
        }
        
        .specification-header {
            background-color: #f8f9fa !important;
            border: 1px solid #dee2e6 !important;
            padding: 8px !important;
            margin-bottom: 10px !important;
            font-weight: bold !important;
        }
        
        .delivery-header {
            background-color: #e8f4fd !important;
            border: 1px solid #0ea5e9 !important;
            padding: 8px !important;
            margin: 20px 0 10px 0 !important;
            font-weight: bold !important;
        }
        
        .placeholder {
            color: #9ca3af;
            font-style: italic;
        }
    </style> 
            </style>
    
    <script>
        function initiatePrint() {
            if (window.matchMedia && window.matchMedia('print').matches) {
                return;
            }
            
            setTimeout(function() {
                try {
                    window.print();
                } catch (e) {
                    console.log('Print failed:', e);
                }
            }, 500);
        }
        
        if (document.readyState === 'complete') {
            initiatePrint();
        } else {
            window.addEventListener('load', initiatePrint);
            document.addEventListener('DOMContentLoaded', initiatePrint);
        }
        
        window.addEventListener('afterprint', function() {
            setTimeout(function() {
                window.close();
            }, 100);
        });
    </script>
</head>
<body>

<div class="container">
<div class="container">
    <!-- Header with Logo -->
    <div class="header">
        <div class="header-left">
            <img src="<?= base_url('assets/images/company-logo.svg') ?>" class="logo" alt="Company Logo"/>
            <div class="company-info">
                <div class="company-name">PT. SARANA MITRA LUAS</div>
                <div class="company-subtitle">Forklift & Heavy Equipment Rental</div>
            </div>
        </div>
        <div class="doc-info">
            <div class="doc-title">DELIVERY INSTRUCTION</div>
        </div>
        <div class="doc-details">
            <div class="doc-number">No: <?= esc($di['nomor_di'] ?? '-') ?></div>
            <div class="doc-number">SPK: <?= esc($spk['nomor_spk'] ?? $spk['no_spk'] ?? '-') ?></div>
            <?php if (!empty($di['created_at'])): ?>
                <div class="doc-date">Tanggal: <?= date('d F Y', strtotime($di['created_at'])) ?></div>
            <?php endif; ?>
            <div class="doc-date"> <?= date('d F Y H:i') ?></div>
        </div>
    </div>

    <!-- Customer & Transportation Information Table -->
    <table class="table">
        <tr>
            <th colspan="4">INFORMASI PELANGGAN & TRANSPORTASI</th>
        </tr>
        <tr>
            <td class="table-label">Nama Perusahaan</td>
            <td><?= esc($di['pelanggan'] ?? $spk['pelanggan'] ?? $spk['customer_name'] ?? '-') ?></td>
            <td class="table-label">Jenis Kendaraan</td>
            <td class="<?= $placeholder && empty($di['kendaraan']) ? 'placeholder' : '' ?>">
                <?= esc($di['kendaraan'] ?? ($placeholder ? 'Akan diisi saat pengiriman' : '-')) ?>
            </td>
        </tr>
        <tr>
            <td class="table-label">PIC</td>
            <td><?= esc($spk['pic'] ?? '-') ?></td>
            <td class="table-label">No. Polisi</td>
            <td class="<?= $placeholder && empty($di['no_polisi_kendaraan']) ? 'placeholder' : '' ?>">
                <?= esc($di['no_polisi_kendaraan'] ?? ($placeholder ? 'Akan diisi saat pengiriman' : '-')) ?>
            </td>
        </tr>
        <tr>
            <td class="table-label">Contact Person</td>
            <td><?= esc($spk['kontak'] ?? '-') ?></td>
            <td class="table-label">Nama Supir</td>
            <td class="<?= $placeholder && empty($di['nama_supir']) ? 'placeholder' : '' ?>">
                <?= esc($di['nama_supir'] ?? ($placeholder ? 'Akan diisi saat pengiriman' : '-')) ?>
            </td>
        </tr>
        <tr>
            <td class="table-label">No. Kontrak / PO</td>
            <td><?= esc($di['po_kontrak_nomor'] ?? $spk['po_kontrak_nomor'] ?? $spk['kontrak_no'] ?? '-') ?></td>
            <td class="table-label">No. HP Supir</td>
            <td class="<?= $placeholder && empty($di['no_hp_supir']) ? 'placeholder' : '' ?>">
                <?= esc($di['no_hp_supir'] ?? ($placeholder ? 'Akan diisi saat pengiriman' : '-')) ?>
            </td>
        </tr>
        <tr>
            <td class="table-label">Tanggal Kirim</td>
            <td><?= esc($di['tanggal_kirim'] ?? '-') ?></td>
            <td class="table-label">Alamat Pengiriman</td>
            <td colspan="3"><?= esc($di['lokasi'] ?? $spk['lokasi'] ?? '-') ?></td>
        </tr>
    </table>

    <!-- Unit Information -->
    <div class="unit-box">
        <div class="unit-title">
            Detail Unit Yang Dikirim
            <?php if ($unit_item): ?>
                - <?= esc($unit_item['no_unit'] ?? 'Unit') ?> (<?= esc($unit_item['merk_unit'] ?? '') ?> <?= esc($unit_item['model_unit'] ?? '') ?>)
            <?php endif; ?>
        </div>
        
        <table class="table" style="margin-bottom: 0;">
            <tr>
                <th colspan="4">SPESIFIKASI UNIT</th>
            </tr>
            <?php
                // Prepare unit data
                $unit = $unit_item ?? ($s['selected']['unit'] ?? null);
                
                $unitDetails = [
                    ['ID Unit', $unit['no_unit'] ?? '', 'Serial Number', $unit['serial_number'] ?? ''],
                    ['Merk Unit', $unit['merk_unit'] ?? '', 'Model Unit', $unit['model_unit'] ?? ''],
                    ['Jenis Unit', $unit['jenis_unit'] ?? $s['jenis_unit'] ?? '', 'Tipe Unit', $unit['tipe_jenis'] ?? $s['tipe_jenis'] ?? ''],
                    ['Kapasitas', $unit['kapasitas_name'] ?? $s['kapasitas_id_name'] ?? '', 'Mast', $s['mast_id_name'] ?? ''],
                ];
                
                foreach ($unitDetails as $row) {
                    echo '<tr>';
                    for ($i = 0; $i < 4; $i += 2) {
                        if (isset($row[$i])) {
                            echo '<td class="table-label">' . esc($row[$i]) . '</td>';
                            $value = $placeholder && empty($row[$i+1]) ? '.............................' : ($row[$i+1] ?: '-');
                            echo '<td>' . esc($value) . '</td>';
                        }
                    }
                    echo '</tr>';
                }
            ?>
        </table>
        
        <?php
            // Components and Attachments
            if ($unit_item) {
                $hasComponents = false;
                echo '<table class="table" style="margin-top: 10px;">';
                echo '<tr><th colspan="4">KOMPONEN & ATTACHMENT</th></tr>';
                
                // Battery
                if (!empty($unit_item['sn_baterai'])) {
                    echo '<tr>';
                    echo '<td class="table-label">Baterai</td>';
                    echo '<td>' . esc($unit_item['merk_baterai'] . ' ' . $unit_item['tipe_baterai']) . '</td>';
                    echo '<td class="table-label">SN Baterai</td>';
                    echo '<td>' . esc($unit_item['sn_baterai']) . '</td>';
                    echo '</tr>';
                    $hasComponents = true;
                }
                
                // Charger
                if (!empty($unit_item['sn_charger'])) {
                    echo '<tr>';
                    echo '<td class="table-label">Charger</td>';
                    echo '<td>' . esc($unit_item['merk_charger'] . ' ' . $unit_item['tipe_charger']) . '</td>';
                    echo '<td class="table-label">SN Charger</td>';
                    echo '<td>' . esc($unit_item['sn_charger']) . '</td>';
                    echo '</tr>';
                    $hasComponents = true;
                }
                
                // Attachment
                if (!empty($unit_item['sn_attachment'])) {
                    echo '<tr>';
                    echo '<td class="table-label">Attachment</td>';
                    echo '<td>' . esc($unit_item['attachment_merk'] . ' ' . $unit_item['attachment_model']) . '</td>';
                    echo '<td class="table-label">SN Attachment</td>';
                    echo '<td>' . esc($unit_item['sn_attachment']) . '</td>';
                    echo '</tr>';
                    $hasComponents = true;
                }
                
                if (!$hasComponents) {
                    echo '<tr><td colspan="4" style="text-align: center; font-style: italic;">Tidak ada komponen tambahan</td></tr>';
                }
                
                echo '</table>';
            }
        ?>
    </div>

    <!-- Catatan Khusus -->
    <?php if (!empty($di['catatan']) || !empty($spk['catatan_khusus'])): ?>
        <div class="section">
            <div class="section-title">Catatan Khusus</div>
            <div style="padding: 10px; border: 1px solid #000; background: #f9f9f9;">
                <?= nl2br(esc($di['catatan'] ?? $spk['catatan_khusus'] ?? '')) ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Signature Area -->
    <div class="signature-area">
        <div class="signature-title">Persetujuan dan Tanda Tangan</div>
        
        <div class="signature-row">
            <div class="signature-box">
                <div class="sig-role">PDI PENGECEKAN</div>
                <?php
                    // Check PDI approval from SPK Service
                    $pdiApproved = !empty($spk['pdi_checker_name']); // Assuming this field exists
                    if ($pdiApproved) {
                        echo '<div class="approved-stamp">APPROVED</div>';
                        echo '<div style="font-size: 9px; margin-top: 5px;">' . esc($spk['pdi_checker_name'] ?? '') . '</div>';
                    } else {
                        echo '<div style="margin-top: 20px;">_______________</div>';
                    }
                ?>
                <div class="sig-line"></div>
                <div class="sig-label">Nama & Tanggal</div>
            </div>
            
            <div class="signature-box">
                <div class="sig-role">MARKETING</div>
                <?php
                    // Marketing auto-approved when DI is created
                    if (!empty($di['created_at'])) {
                        echo '<div class="approved-stamp">APPROVED</div>';
                        echo '<div style="font-size: 9px; margin-top: 5px;">' . date('d/m/Y', strtotime($di['created_at'])) . '</div>';
                    } else {
                        echo '<div style="margin-top: 20px;">_______________</div>';
                    }
                ?>
                <div class="sig-line"></div>
                <div class="sig-label">Nama & Tanggal</div>
            </div>
            
            <div class="signature-box">
                <div class="sig-role">OPERATIONAL DELIVERY</div>
                <?php
                    // Approved when delivery preparation is completed
                    $deliveryApproved = ($status === 'APPROVED' || $status === 'DELIVERED');
                    if ($deliveryApproved) {
                        echo '<div class="approved-stamp">APPROVED</div>';
                        if (!empty($di['updated_at'])) {
                            echo '<div style="font-size: 9px; margin-top: 5px;">' . date('d/m/Y', strtotime($di['updated_at'])) . '</div>';
                        }
                    } else {
                        echo '<div style="margin-top: 20px;">_______________</div>';
                    }
                ?>
                <div class="sig-line"></div>
                <div class="sig-label">Nama & Tanggal</div>
            </div>
            
            <div class="signature-box">
                <div class="sig-role">CUSTOMER</div>
                <div style="margin-top: 20px;">_______________</div>
                <div class="sig-line"></div>
                <div class="sig-label">Nama & Tanggal</div>
            </div>
        </div>
        
        <div class="notes">
            <div class="notes-title">CATATAN PENTING:</div>
            <div style="font-size: 11px; line-height: 1.4;">
                • Dokumen ini adalah perintah resmi pengiriman dari Tim Marketing kepada Tim Operasional<br>
                • Pastikan semua informasi unit dan komponen sesuai dengan yang tertera<br>
                • Laporkan segera jika ada ketidaksesuaian atau masalah selama pengiriman
            </div>
        </div>
    </div>
</div>
    </style>
    
    <script>
        function initiatePrint() {
            if (window.matchMedia && window.matchMedia('print').matches) {
                return;
            }
            
            setTimeout(function() {
                try {
                    window.print();
                } catch (e) {
                    console.log('Print failed:', e);
                }
            }, 500);
        }
        
        if (document.readyState === 'complete') {
            initiatePrint();
        } else {
            window.addEventListener('load', initiatePrint);
            document.addEventListener('DOMContentLoaded', initiatePrint);
        }
        
        window.addEventListener('afterprint', function() {
            setTimeout(function() {
                window.close();
            }, 100);
        });
    </script>
</head>
<body>

<div class="container-fluid" style="margin: 0; padding: 15mm;">
    <!-- SECTION: Header -->
    <div class="header mb-4">
        <div class="d-flex align-items-center">
            <img src="<?= base_url('assets/images/company-logo.svg') ?>" class="logo me-2" alt="Company Logo"/>
        </div>
        <div class="header-center">
            <div class="header-title">PT. SARANA MITRA LUAS</div>
            <div class="header-subtitle">DELIVERY INSTRUCTION</div>
        </div>
        <div class="header-meta">
            <div>DI No: <strong><?= esc($di['nomor_di'] ?? '-') ?></strong></div>
            <?php if (!empty($di['created_at'])): ?>
                <div>Dibuat: <?= date('d/m/Y H:i', strtotime($di['created_at'])) ?></div>
            <?php endif; ?>
            <?php if (!empty($di['updated_at'])): ?>
                <div>Diperbarui: <?= date('d/m/Y H:i', strtotime($di['updated_at'])) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- SECTION: Document Information -->
    <table class="table">
        <tr>
            <th colspan="4" style="background-color: #f8f9fa; text-align: center;">INFORMASI DOKUMEN</th>
        </tr>
        <tr>
            <td class="label" style="width: 15%;">No. DI</td>
            <td class="val" style="width: 35%;"><?= esc($di['nomor_di'] ?? '-') ?></td>
            <td class="label" style="width: 15%;">No. SPK</td>
            <td class="val" style="width: 35%;"><?= esc($spk['nomor_spk'] ?? $spk['no_spk'] ?? '-') ?></td>
        </tr>
        <tr>
            <td class="label">Status DI</td>
            <td class="val"><?= esc($status) ?></td>
            <td class="label">Tanggal Kirim</td>
            <td class="val"><?= esc($di['tanggal_kirim'] ?? '-') ?></td>
        </tr>
        <tr>
            <td class="label">Kontrak/PO</td>
            <td class="val"><?= esc($di['po_kontrak_nomor'] ?? $spk['po_kontrak_nomor'] ?? $spk['kontrak_no'] ?? '-') ?></td>
            <td class="label">Estimasi Sampai</td>
            <td class="val"><?= esc($di['estimasi_sampai'] ?? '-') ?></td>
        </tr>
    </table>

    <!-- SECTION: Customer Information -->
    <table class="table">
        <tr>
            <th colspan="4" style="background-color: #f8f9fa; text-align: center;">INFORMASI PELANGGAN</th>
        </tr>
        <tr>
            <td class="label" style="width: 15%;">Nama Perusahaan</td>
            <td class="val" style="width: 35%;"><?= esc($di['pelanggan'] ?? $spk['pelanggan'] ?? $spk['customer_name'] ?? '-') ?></td>
            <td class="label" style="width: 15%;">PIC</td>
            <td class="val" style="width: 35%;"><?= esc($spk['pic'] ?? '-') ?></td>
        </tr>
        <tr>
            <td class="label">Contact Person</td>
            <td class="val"><?= esc($spk['kontak'] ?? '-') ?></td>
            <td class="label">Jenis Perintah</td>
            <td class="val"><?= esc($di['jenis_perintah'] ?? '-') ?></td>
        </tr>
        <tr>
            <td class="label">Alamat Pengiriman</td>
            <td class="val" colspan="3"><?= esc($di['lokasi'] ?? $spk['lokasi'] ?? '-') ?></td>
        </tr>
        <tr>
            <td class="label">Tujuan Perintah</td>
            <td class="val" colspan="3"><?= esc($di['tujuan_perintah'] ?? '-') ?></td>
        </tr>
    </table>

    <!-- SECTION: Transport Information -->
    <table class="table">
        <tr>
            <th colspan="4" style="background-color: #e8f4fd; text-align: center;">INFORMASI TRANSPORTASI</th>
        </tr>
        <tr>
            <td class="label" style="width: 15%;">Jenis Kendaraan</td>
            <td class="val" style="width: 35%;"><?= esc($di['kendaraan'] ?? ($placeholder ? '.....................................' : '-')) ?></td>
            <td class="label" style="width: 15%;">No. Polisi</td>
            <td class="val" style="width: 35%;"><?= esc($di['no_polisi_kendaraan'] ?? ($placeholder ? '.....................................' : '-')) ?></td>
        </tr>
        <tr>
            <td class="label">Nama Supir</td>
            <td class="val"><?= esc($di['nama_supir'] ?? ($placeholder ? '.....................................' : '-')) ?></td>
            <td class="label">No. SIM</td>
            <td class="val"><?= esc($di['no_sim_supir'] ?? ($placeholder ? '.....................................' : '-')) ?></td>
        </tr>
        <tr>
            <td class="label">No. HP Supir</td>
            <td class="val" colspan="3"><?= esc($di['no_hp_supir'] ?? ($placeholder ? '.....................................' : '-')) ?></td>
        </tr>
    </table>

    <!-- SECTION: Unit Details -->
    <div class="delivery-header">
        DETAIL UNIT YANG DIKIRIM
        <?php if ($unit_item): ?>
            - Unit: <?= esc($unit_item['no_unit'] ?? 'Unit') ?> (<?= esc($unit_item['merk_unit'] ?? '') ?> <?= esc($unit_item['model_unit'] ?? '') ?>)
        <?php endif; ?>
    </div>

    <table class="table">
        <tr>
            <th colspan="4" style="background-color: #f8f9fa; text-align: center;">SPESIFIKASI UNIT</th>
        </tr>
        <?php
            // Prepare unit data
            $unit = $unit_item ?? ($s['selected']['unit'] ?? null);
            $attachment = $s['selected']['attachment'] ?? null;
            $aksesoris = (!empty($spk['persiapan_aksesoris_tersedia']) ? $spk['persiapan_aksesoris_tersedia'] : (is_array($s['aksesoris'] ?? null) ? implode(', ', $s['aksesoris']) : ($s['aksesoris'] ?? '')));

            $unitDetails = [
                ['ID Unit', $unit['no_unit'] ?? ''],
                ['Serial Number', $unit['serial_number'] ?? ''],
                ['Merk Unit', $unit['merk_unit'] ?? ''],
                ['Model Unit', $unit['model_unit'] ?? ''],
                ['Jenis Unit', $unit['jenis_unit'] ?? $s['jenis_unit'] ?? ''],
                ['Tipe Unit', $unit['tipe_jenis'] ?? $s['tipe_jenis'] ?? ''],
                ['Kapasitas', $unit['kapasitas_name'] ?? $s['kapasitas_id_name'] ?? ''],
                ['Mast', $s['mast_id_name'] ?? ''],
            ];

            for ($i = 0; $i < count($unitDetails); $i += 2) {
                echo '<tr>';
                for ($j = 0; $j < 2; $j++) {
                    if (isset($unitDetails[$i + $j])) {
                        $detail = $unitDetails[$i + $j];
                        echo '<td class="label" style="width: 15%;">' . esc($detail[0]) . '</td>';
                        echo '<td class="val" style="width: 35%;">' . esc($placeholder && empty($detail[1]) ? '................................................' : ($detail[1] ?: '-')) . '</td>';
                    } else {
                        echo '<td class="label" style="width: 15%;"></td>';
                        echo '<td class="val" style="width: 35%;"></td>';
                    }
                }
                echo '</tr>';
            }
        ?>
    </table>

    <!-- SECTION: Serial Numbers -->
    <table class="table">
        <tr>
            <th colspan="4" style="background-color: #f8f9fa; text-align: center;">SERIAL NUMBERS & ACCESSORIES</th>
        </tr>
        <?php
            $serialDetails = [
                ['SN Attachment', $attachment['sn_attachment_formatted'] ?? ''],
                ['SN Mast', $unit['sn_mast_formatted'] ?? ''],
                ['SN Baterai', $unit['sn_baterai_formatted'] ?? ''],
                ['SN Charger', $unit['sn_charger_formatted'] ?? ''],
                ['Valve', $s['valve_id_name'] ?? ''],
                ['Roda', $s['roda_id_name'] ?? ''],
                ['Ban', $s['ban_id_name'] ?? ''],
                ['Aksesoris', $aksesoris],
            ];

            for ($i = 0; $i < count($serialDetails); $i += 2) {
                echo '<tr>';
                for ($j = 0; $j < 2; $j++) {
                    if (isset($serialDetails[$i + $j])) {
                        $detail = $serialDetails[$i + $j];
                        echo '<td class="label" style="width: 15%;">' . esc($detail[0]) . '</td>';
                        echo '<td class="val" style="width: 35%;">' . esc($placeholder && empty($detail[1]) ? '................................................' : ($detail[1] ?: '-')) . '</td>';
                    } else {
                        echo '<td class="label" style="width: 15%;"></td>';
                        echo '<td class="val" style="width: 35%;"></td>';
                    }
                }
                echo '</tr>';
            }
        ?>
    </table>

    <!-- SECTION: Additional Information -->
    <table class="table">
        <tr>
            <th style="background-color: #f8f9fa; text-align: center; width: 20%;">CATATAN PENGIRIMAN</th>
            <td style="width: 80%; min-height: 60px; vertical-align: top;">
                <?= esc($di['catatan_sampai'] ?? ($placeholder ? 'Catatan pengiriman: .................................................................................................................................................................................................................................................................................................................................' : '')) ?>
            </td>
        </tr>
    </table>

    <!-- SECTION: Approval Signatures -->
    <div class="signature-section">
        <table class="table">
            <tr>
                <th colspan="4" style="background-color: #f8f9fa; text-align: center;">PERSETUJUAN & TANDA TANGAN</th>
            </tr>
            <tr>
                <td class="sig" style="width: 25%;">
                    <div style="font-weight: bold; margin-bottom: 10px;">MARKETING</div>
                    <div style="color: #666; font-size: 10px; margin-bottom: 5px;">Pembuat DI</div>
                    <?php 
                        $marketingName = $spk['created_by_name'] ?? $spk['marketing_name'] ?? $spk['created_by'] ?? '';
                        $marketingApproved = !empty($marketingName);
                        
                        if ($marketingApproved) {
                            echo '<div class="sig-stamp">APPROVED</div>';
                            echo '<div class="sig-name">(' . esc($marketingName) . ')</div>';
                        } else {
                            echo '<div class="sig-placeholder">(.........................)</div>';
                        }
                    ?>
                </td>
                <td class="sig" style="width: 25%;">
                    <div style="font-weight: bold; margin-bottom: 10px;">BAG. PDI</div>
                    <div style="color: #666; font-size: 10px; margin-bottom: 5px;">Pengecekan Unit</div>
                    <?php if (!empty($spk['pdi_tanggal_approve'])): ?>
                        <div class="sig-stamp">APPROVED</div>
                        <div class="sig-name">(<?= esc($spk['pdi_mekanik'] ?? 'PDI Team') ?>)</div>
                    <?php else: ?>
                        <div class="sig-placeholder">(.........................)</div>
                    <?php endif; ?>
                </td>
                <td class="sig" style="width: 25%;">
                    <div style="font-weight: bold; margin-bottom: 10px;">BAG. DELIVERY</div>
                    <div style="color: #666; font-size: 10px; margin-bottom: 5px;">Persiapan Pengiriman</div>
                    <?php if (!empty($di['perencanaan_tanggal_approve'])): ?>
                        <div class="sig-stamp">APPROVED</div>
                        <div class="sig-name">(<?= esc($di['perencanaan_approved_by'] ?? 'Delivery Team') ?>)</div>
                    <?php else: ?>
                        <div class="sig-placeholder">(.........................)</div>
                    <?php endif; ?>
                </td>
                <td class="sig" style="width: 25%;">
                    <div style="font-weight: bold; margin-bottom: 10px;">PENERIMA</div>
                    <div style="color: #666; font-size: 10px; margin-bottom: 5px;">Customer</div>
                    <?php if (!empty($di['sampai_tanggal_approve'])): ?>
                        <div class="sig-stamp">RECEIVED</div>
                        <div class="sig-name">(<?= esc($di['sampai_penerima_nama'] ?? 'Customer') ?>)</div>
                    <?php else: ?>
                        <div class="sig-placeholder">(.........................)</div>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td class="sig">
                    <div style="font-size: 9px; color: #666; margin-top: 5px;">
                        <?php if (!empty($di['created_at'])): ?>
                            Tanggal: <?= date('d/m/Y', strtotime($di['created_at'])) ?>
                        <?php else: ?>
                            Tanggal: ......................
                        <?php endif; ?>
                    </div>
                </td>
                <td class="sig">
                    <div style="font-size: 9px; color: #666; margin-top: 5px;">
                        <?php if (!empty($spk['pdi_tanggal_approve'])): ?>
                            Tanggal: <?= date('d/m/Y', strtotime($spk['pdi_tanggal_approve'])) ?>
                        <?php else: ?>
                            Tanggal: ......................
                        <?php endif; ?>
                    </div>
                </td>
                <td class="sig">
                    <div style="font-size: 9px; color: #666; margin-top: 5px;">
                        <?php if (!empty($di['perencanaan_tanggal_approve'])): ?>
                            Tanggal: <?= date('d/m/Y', strtotime($di['perencanaan_tanggal_approve'])) ?>
                        <?php else: ?>
                            Tanggal: ......................
                        <?php endif; ?>
                    </div>
                </td>
                <td class="sig">
                    <div style="font-size: 9px; color: #666; margin-top: 5px;">
                        <?php if (!empty($di['sampai_tanggal_approve'])): ?>
                            Tanggal: <?= date('d/m/Y', strtotime($di['sampai_tanggal_approve'])) ?>
                        <?php else: ?>
                            Tanggal: ......................
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer with document info -->
    <div style="margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 9px; color: #666; text-align: center;">
        <div>PT. Sarana Mitra Luas - Delivery Instruction <?= esc($di['nomor_di'] ?? '') ?></div>
        <div>Dicetak pada: <?= date('d/m/Y H:i:s') ?> | Halaman 1 dari 1</div>
    </div>
</div>

</body>
</html>
