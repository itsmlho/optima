<?php
$spk = $spk ?? [];
$s   = $spesifikasi ?? [];
$k   = $kontrak_spesifikasi ?? []; // Data kontrak untuk Equipment section
$status = strtoupper((string)($spk['status'] ?? $spk['status_spk'] ?? ''));
$placeholder = ($status === 'SUBMITTED');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= 'SPK-' . str_replace('/', '-', esc($spk['nomor_spk'] ?? $spk['no_spk'] ?? 'Unknown')) ?></title>
    <!-- Completely disable favicon to prevent any icon display -->
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64,">
    <link rel="shortcut icon" type="image/x-icon" href="data:image/x-icon;base64,">
    <!-- Meta tags to control print behavior -->
    <meta name="format-detection" content="telephone=no">
    <meta name="format-detection" content="address=no">
    <meta name="format-detection" content="email=no">
    <!-- Additional print control -->
    <meta name="robots" content="noindex, nofollow">
    <meta name="print-option" content="no-header-footer">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.1/css/bootstrap.min.css" integrity="sha512-Ez0cGzNzHR1tYAv56860NLspgUGuQw16GiOOp/I2LuTmpSK9xDXlgJz3XN4cnpXWDmkNBKXR/VDMTCnAaEooxA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @page { 
            size: A4; 
            margin: 15mm;
        }
        
        /* Hide browser print headers and footers */
        @media print {
            @page {
                margin: 10mm;
                size: A4;
            }
            
            /* Hide browser generated headers/footers */
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            /* Custom footer for each page */
            .print-footer {
                position: fixed;
                bottom: 5mm;
                left: 0;
                right: 0;
                text-align: center;
                font-size: 9px;
                color: #666;
                border-top: 1px solid #ccc;
                padding-top: 2mm;
                background: white;
                z-index: 1000;
            }
            
            /* Hide print instruction when printing */
            #printInstruction {
                display: none !important;
            }
        }
        
        /* Hide browser print headers and footers */
        @media print {
            @page {
                margin: 0;
                size: A4;
                /* Try to remove headers and footers */
                @top-left { content: ""; }
                @top-center { content: ""; }
                @top-right { content: ""; }
                @bottom-left { content: ""; }
                @bottom-center { content: ""; }
                @bottom-right { content: ""; }
            }
            
            /* Hide browser generated headers/footers */
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin: 15mm !important;
                padding: 0 !important;
            }
            
            /* Remove any potential browser URL display */
            * {
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
            }
            
            /* Hide any print-only elements that might show URLs */
            .print-url, .print-header, .print-footer {
                display: none !important;
                visibility: hidden !important;
            }
            
            /* Hide print instruction box when printing */
            #printInstruction {
                display: none !important;
            }
        }
        
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color:#222; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
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
        /* Better spacing for table content */
        .table td {
            min-height: 25px;
        }
        .muted { color:#666; }
        .sig { text-align:center; }
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
        .title { font-size: 16px; font-weight: bold; margin:0; }
        .subtitle { font-size: 15px; color:#555; margin:0; }
        .k-box { height: 16px; width: 16px; border:1px solid #999; display:inline-block; margin-right:2px; }
        .dotted { color:#333; }
        .label { color:#374151; }
        .val   { color:#111827; font-weight: 600; }
        .grid-2 td { width: 25%; }
        .no-border td { border:none !important; }
        .logo { max-height: 46px; }
        .header { display:grid; grid-template-columns:auto 1fr auto; align-items:center; column-gap:10px; }
        .header-center { text-align:center; }
        .header-meta { font-size:10px; color:#6b7280; text-align:right; }
        /* Extra print-friendly blocks for multiple units */
        .unit-card { 
            border:1px solid #9aa1a7; 
            padding:10px; 
            margin-bottom:15px; 
            margin-top:10px;
            page-break-inside: avoid;
            clear: both;
        }
        
        /* For second and subsequent units, reduce spacing */
        .unit-card:nth-child(n+2) {
            margin-top: 15px;
            page-break-before: auto;
        }
        
        .unit-title { 
            background:#f8fafc; 
            font-weight:bold; 
            padding:6px 8px; 
            border-bottom:1px solid #9aa1a7; 
            margin:-10px -10px 10px;
        }
        
        /* Reduced spacing */
        .section-separator {
            margin: 15px 0;
            border-top: 2px solid #dee2e6;
            padding-top: 15px;
        }
        
        /* Better table spacing inside unit cards */
        .unit-card .table {
            margin-bottom: 10px;
        }
        
        .unit-card .table th,
        .unit-card .table td {
            padding: .4rem .6rem;
            line-height: 1.3;
        }
        
        /* Page break controls for better multi-page layout */
        .page-break-before {
            page-break-before: always;
        }
        
        .page-break-after {
            page-break-after: always;
        }
        
        .no-page-break {
            page-break-inside: avoid;
        }
        
        /* Ensure signature section doesn't break awkwardly */
        .signature-section {
            page-break-inside: avoid;
            margin-top: 20px;
            padding-top: 15px;
        }
        
        /* Print footer styling */
        .print-footer {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #666;
            background: white;
            border-top: 1px solid #ddd;
            padding: 5px;
        }
        
        /* Show footer only when printing */
        @media print {
            .print-footer {
                display: block;
                position: fixed;
                bottom: 5mm;
            }
        }
        
        .specification-header {
            background-color: #f8f9fa !important;
            border: 1px solid #dee2e6 !important;
            padding: 8px !important;
            margin-bottom: 10px !important;
            font-weight: bold !important;
        }
        
        .prepared-header {
            background-color: #e8f5e8 !important;
            border: 1px solid #28a745 !important;
            padding: 8px !important;
            margin: 20px 0 10px 0 !important;
            font-weight: bold !important;
        }
    </style>
    
    <script>
        // Enhanced print handling for better cross-browser compatibility
        function initiatePrint() {
            if (window.matchMedia && window.matchMedia('print').matches) {
                return; // Already printing
            }
            
            // Try to hide browser print headers/footers
            try {
                // For Chrome/Edge - disable headers and footers
                if (window.chrome) {
                    const printSettings = {
                        marginType: 1, // NO_MARGINS
                        headerFooterEnabled: false,
                        shouldPrintBackgrounds: true,
                        shouldPrintSelectionOnly: false
                    };
                }
            } catch (e) {
                console.log('Could not set print settings:', e);
            }
            
            // Set a timeout to ensure all content is loaded
            setTimeout(function() {
                try {
                    window.print();
                } catch (e) {
                    console.log('Print failed:', e);
                }
            }, 500);
        }
        
        // Multiple event listeners for better compatibility
        if (document.readyState === 'complete') {
            initiatePrint();
        } else {
            window.addEventListener('load', initiatePrint);
            document.addEventListener('DOMContentLoaded', initiatePrint);
        }
        
        // Handle after print
        window.addEventListener('afterprint', function() {
            setTimeout(function() {
                window.close();
            }, 100);
        });
        
        // Manual print button support
        window.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</head>
<body>

<!-- Print instruction untuk menghilangkan URL dan footer browser -->
<div id="printInstruction" style="position: fixed; top: 10px; left: 10px; background: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; border-radius: 5px; font-size: 12px; z-index: 9999; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
    <strong>📝 Panduan Print:</strong><br>
    • Untuk menghilangkan URL dan tanggal di print, buka <strong>Print Settings</strong><br>
    • Pilih <strong>"More settings"</strong><br>
    • Uncheck <strong>"Headers and footers"</strong><br>
    • Klik <strong>Print</strong>
    <button onclick="document.getElementById('printInstruction').style.display='none'" style="margin-left: 10px; background: #28a745; color: white; border: none; padding: 2px 8px; border-radius: 3px; cursor: pointer;">OK</button>
</div>

<div class="container-fluid">
    <div class="header mb-2">
        <img src="<?= base_url('assets/images/company-logo.svg') ?>" class="logo" alt="logo"/>
        <div class="header-center">
            <div class="title">PT SARANA MITRA LUAS Tbk</div>
            <div class="subtitle">SPK ( Persiapan Unit )</div>
            <br/>
        </div>
        <div class="header-meta">
            <?php if (!empty($spk['created_at'])): ?>Created: <?= esc($spk['created_at']) ?><br><?php endif; ?>
            <?php if (!empty($spk['updated_at'])): ?>Updated: <?= esc($spk['updated_at']) ?><?php endif; ?>
        </div>
    </div>

    <div class="row mb-1">
        <div class="col-6"><span class="label">No SPK:</span> <span class="val"><?= esc($spk['nomor_spk'] ?? $spk['no_spk'] ?? '-') ?></span></div>
        <div class="col-6"><span class="label">Kontrak/PO:</span> <span class="val"><?= esc($spk['po_kontrak_nomor'] ?? $spk['kontrak_no'] ?? '-') ?></span></div>
    </div>
    <div class="row mb-1">
        <div class="col-6"><span class="label">Nama Perusahaan:</span> <span class="val"><?= esc($spk['pelanggan'] ?? $spk['customer_name'] ?? '-') ?></span></div>
        <div class="col-6"><span class="label">Lokasi:</span> <span class="val"><?= esc($spk['lokasi'] ?? '-') ?></span></div>
    </div>
    <div class="row mb-2">
        <div class="col-6"><span class="label">PIC:</span> <span class="val"><?= esc($spk['pic'] ?? '-') ?></span></div>
        <div class="col-6"><span class="label">Kontak:</span> <span class="val"><?= esc($spk['kontak'] ?? '-') ?></span></div>
    </div>

    <!-- Keterangan section untuk kontrak spesifikasi -->
    <div class="mb-2 fw-bold" style="background-color: #f8f9fa; padding: 8px; border: 1px solid #dee2e6;">
        Permintaan Spesifikasi (Data Kontrak)
    </div>

    <table class="table">
        <thead>
            <tr>
                 <th class="text-center" style="width:5%">No.</th>
                 <th style="width:35%">Kategori</th>
                 <th style="width:60%">Detail Spesifikasi</th>
             </tr>
         </thead>
         <tbody>
            <tr>
                <td class="text-center align-middle">1.</td>
                <td class="align-middle"><strong>Delivery Plan :</strong></td>
                <td><?= esc($spk['delivery_plan'] ?? '-') ?></td>
            </tr>
            <tr>
                <td class="text-center align-top">2.</td>
                <td class="align-top"><strong>Equipment :</strong>
                    <div class="mt-2">
                        <div>- Total Unit</div>
                        <div>- Merk & Jenis Unit</div>
                        <div>- Baterai & Charger</div>
                        <div>- Departemen</div>
                        <div>- Kapasitas</div>
                        <div>- Attachment</div>
                        <div>- Roda & Ban</div>
                        <div>- Mast</div>
                        <div>- Valve</div>
                    </div>
                </td>
                <td class="align-top">
                    <?php 
                        // Use kontrak data for Equipment section (data permintaan marketing)
                        $jumlahUnit = $k['jumlah_dibutuhkan'] ?? $spk['jumlah_unit'] ?? '';
                        $merkUnit = $k['merk_unit'] ?? '';
                        $modelUnit = $k['model_unit'] ?? '';
                        $jenisUnit = $k['kontrak_jenis_unit'] ?? $k['jenis_unit'] ?? '';
                        $tipeUnit = $k['kontrak_tipe_unit'] ?? $k['tipe_jenis'] ?? '';
                        $kapasitasName = $k['kontrak_kapasitas_name'] ?? $k['kapasitas_name'] ?? '';
                        $departemenName = $k['kontrak_departemen_name'] ?? $k['departemen_name'] ?? '';
                        $mastName = $k['kontrak_mast_name'] ?? $k['mast_name'] ?? '';
                        $rodaName = $k['kontrak_roda_name'] ?? $k['roda_name'] ?? '';
                        $banName = $k['kontrak_ban_name'] ?? $k['ban_name'] ?? '';
                        $valveName = $k['kontrak_valve_name'] ?? $k['valve_name'] ?? '';
                        
                        // Attachment dari kontrak (bukan dari spesifikasi SPK)
                        $attachmentType = $k['attachment_tipe'] ?? $k['attachment_name'] ?? '';
                        
                        // Battery dan Charger dari kontrak
                        $batteryType = $k['jenis_baterai'] ?? $k['baterai_type'] ?? '';
                        $chargerType = $k['kontrak_charger_model'] ?? $k['charger_model'] ?? '';
                    ?>
                    <div class="mt-2">
                        <br />
                        <div class="val"><?= esc($jumlahUnit ?: '..............................') ?></div>
                        <div class="val">
                            <?php
                            // Combine merk and jenis unit info from kontrak
                            $brandTypeInfo = [];
                            if (!empty($merkUnit)) $brandTypeInfo[] = $merkUnit;
                            if (!empty($modelUnit)) $brandTypeInfo[] = $modelUnit;
                            if (!empty($jenisUnit) && !in_array($jenisUnit, $brandTypeInfo)) $brandTypeInfo[] = $jenisUnit;
                            if (!empty($tipeUnit) && !in_array($tipeUnit, $brandTypeInfo)) $brandTypeInfo[] = $tipeUnit;
                            
                            echo !empty($brandTypeInfo) ? esc(implode(' ', $brandTypeInfo)) : '..............................';
                            ?>
                        </div>
                        <div class="val"><?php
                            // Combine battery and charger info from kontrak
                            $combinedInfo = [];
                            if (!empty($batteryType)) $combinedInfo[] = $batteryType;
                            if (!empty($chargerType)) $combinedInfo[] = $chargerType;
                            
                            echo !empty($combinedInfo) ? esc(implode(' & ', $combinedInfo)) : '..............................';
                        ?></div>
                        <div class="val"><?= esc($departemenName ?: '..............................') ?></div>
                        <div class="val"><?= esc($kapasitasName ?: '..............................') ?></div>
                        <div class="val"><?= esc($attachmentType ?: '..............................') ?></div>
                        <div class="val"><?= esc(($rodaName && $banName) ? $rodaName . ' & ' . $banName : ($rodaName ?: ($banName ?: '..............................'))) ?></div>
                        <div class="val"><?= esc($mastName ?: '..............................') ?></div>
                        <div class="val"><?= esc($valveName ?: '..............................') ?></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="text-center align-middle">3.</td>
                <td class="align-middle"><strong>Aksesoris</strong></td>
                <td class="val"><?php
                    // Use aksesoris from kontrak (data permintaan marketing)
                    $aksText = '..............................';
                    
                    // Prioritaskan aksesoris dari kontrak_spesifikasi
                    if (!empty($k['aksesoris'])) {
                        if (is_array($k['aksesoris'])) {
                            $aksText = implode(', ', $k['aksesoris']);
                        } else if (is_string($k['aksesoris'])) {
                            // Try to parse JSON string
                            try {
                                $aksArray = json_decode($k['aksesoris'], true);
                                if (is_array($aksArray) && !empty($aksArray)) {
                                    $aksText = implode(', ', $aksArray);
                                } else {
                                    $aksText = $k['aksesoris']; // Use as-is if not an array
                                }
                            } catch (Exception $e) {
                                $aksText = $k['aksesoris']; // Use as-is if parsing fails
                            }
                        }
                    } elseif (!empty($s['aksesoris'])) {
                        // Fallback ke spesifikasi jika kontrak tidak ada
                        if (is_array($s['aksesoris'])) {
                            $aksText = implode(', ', $s['aksesoris']);
                        } else if (is_string($s['aksesoris'])) {
                            try {
                                $aksArray = json_decode($s['aksesoris'], true);
                                if (is_array($aksArray) && !empty($aksArray)) {
                                    $aksText = implode(', ', $aksArray);
                                } else {
                                    $aksText = $s['aksesoris'];
                                }
                            } catch (Exception $e) {
                                $aksText = $s['aksesoris'];
                            }
                        }
                    } elseif (!empty($spk['persiapan_aksesoris_tersedia'])) {
                        $aksText = $spk['persiapan_aksesoris_tersedia'];
                    }
                    
                    echo esc($aksText);
                ?></td>
            </tr>
        </tbody>
    </table>

    <!-- Keterangan section untuk detail unit yang disiapkan -->
    <div class="mb-2 fw-bold" style="background-color: #e8f5e8; padding: 8px; border: 1px solid #28a745; margin-top: 20px;">
        Detail Unit yang Disiapkan (Data Aktual SPK Service)
    </div>
    
    <!-- DEBUG: SPK data -->
    <!-- SPK ID: <?= $spk['id'] ?? 'NULL' ?> -->
    <!-- Spesifikasi ID: <?= $s['id'] ?? 'NULL' ?> -->
    <!-- Prepared units count: <?= isset($s['prepared_units_detail']) && is_array($s['prepared_units_detail']) ? count($s['prepared_units_detail']) : 'NULL' ?> -->
    
    <?php 
    // Safe handling for prepared_units_detail
    $preparedList = [];
    if (isset($s['prepared_units_detail']) && is_array($s['prepared_units_detail'])) {
        $preparedList = $s['prepared_units_detail'];
    }
    ?>
    <?php if (is_array($preparedList) && count($preparedList) >= 1): ?>
        <!-- Multi-unit display with enhanced formatting -->
        <div class="mb-2" style="font-style: italic; color: #666;">
            Total Unit Disiapkan: <?= count($preparedList) ?> unit
        </div>
        
        <?php foreach ($preparedList as $i => $rowPrepared): ?>
            <div class="unit-card" style="page-break-inside: avoid;">
                <div class="unit-title" style="background:#28a745; color: white; font-size: 12px;">
                    Unit <?= ($i + 1) ?><?= isset($rowPrepared['unit_label']) ? ' - '.esc($rowPrepared['unit_label']) : '' ?>
                    <?php if (!empty($rowPrepared['serial_number'])): ?>
                        (SN: <?= esc($rowPrepared['serial_number']) ?>)
                    <?php endif; ?>
                </div>
                <?php
                    // Build left/right summaries similar to single-unit block, with graceful fallbacks
                    $summaryLeft = [
                        ['ID Unit', $rowPrepared['unit_label'] ?? (isset($rowPrepared['unit_id']) ? '#'.$rowPrepared['unit_id'] : '')],
                        ['Jenis Unit', $rowPrepared['jenis_unit'] ?? ($s['jenis_unit'] ?? '')],
                        ['Departemen', $rowPrepared['departemen_name'] ?? ($s['departemen_id'] ?? '')],
                        ['Attachment', $rowPrepared['sn_attachment_formatted'] ?? ($rowPrepared['attachment_sn'] ?? $rowPrepared['attachment_display'] ?? '')],
                        ['Baterai', $rowPrepared['sn_baterai_formatted'] ?? ($rowPrepared['baterai_sn'] ?? '')],
                        ['Valve', $rowPrepared['valve_id_name'] ?? ($s['valve_id_name'] ?? '')],
                    ];
                    $summaryRight = [
                        ['Serial Number', $rowPrepared['serial_number'] ?? ''],
                        ['Tipe Unit', $rowPrepared['tipe_jenis'] ?? ($s['tipe_jenis'] ?? '')],
                        ['Kapasitas', $rowPrepared['kapasitas_name'] ?? ($s['kapasitas_id_name'] ?? '')],
                        ['Mast', $rowPrepared['mast_id_name'] ?? ($s['mast_id_name'] ?? '')],
                        ['Charger', $rowPrepared['sn_charger_formatted'] ?? ($rowPrepared['charger_sn'] ?? '')],
                        ['Roda & Ban', trim(
                            ($rowPrepared['roda_id_name'] ?? $s['roda_id_name'] ?? '') .
                            ((!empty($rowPrepared['roda_id_name'] ?? $s['roda_id_name'] ?? '') && !empty($rowPrepared['ban_id_name'] ?? $s['ban_id_name'] ?? '')) ? ' & ' : '') .
                            ($rowPrepared['ban_id_name'] ?? $s['ban_id_name'] ?? '')
                        )],
                    ];
                ?>
                <table class="table grid-2">
                    <tbody>
                        <?php 
                            $rows = max(count($summaryLeft), count($summaryRight));
                            for ($ri = 0; $ri < $rows; $ri++): 
                                $left = $summaryLeft[$ri] ?? ['', ''];
                                $right = $summaryRight[$ri] ?? ['', ''];
                        ?>
                        <tr>
                            <td class="label"><?= esc($left[0]) ?></td>
                            <td class="val"><?= esc($left[1] ?: '') ?></td>
                            <td class="label"><?= esc($right[0]) ?></td>
                            <td class="val"><?= esc($right[1] ?: '') ?></td>
                        </tr>
                        <?php endfor; ?>
                        <tr>
                            <td class="label" style="background-color: #f0f8ff;">Aksesoris</td>
                            <td class="val" colspan="3" style="background-color: #f0f8ff;">
                                <?php
                                    // Prefer per-row accessories if provided; fallback to global SPK/spec
                                    $aksText = '';
                                    if (!empty($rowPrepared['aksesoris'])) {
                                        if (is_array($rowPrepared['aksesoris'])) {
                                            $aksText = implode(', ', $rowPrepared['aksesoris']);
                                        } else {
                                            $aksText = (string) $rowPrepared['aksesoris'];
                                        }
                                    } elseif (!empty($s['aksesoris'])) {
                                        if (is_array($s['aksesoris'])) {
                                            $aksText = implode(', ', $s['aksesoris']);
                                        } else {
                                            $try = json_decode((string)$s['aksesoris'], true);
                                            $aksText = is_array($try) ? implode(', ', $try) : (string)$s['aksesoris'];
                                        }
                                    } elseif (!empty($spk['persiapan_aksesoris_tersedia'])) {
                                        $aksText = (string) $spk['persiapan_aksesoris_tersedia'];
                                    }
                                    echo esc($aksText);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="label" style="background-color: #fff3cd;">Processing Info</td>
                            <td class="val" colspan="3" style="background-color: #fff3cd;">
                                <?php
                                // Show processing timeline for this unit
                                $processInfo = [];
                                if (!empty($rowPrepared['mekanik'])) $processInfo[] = "Mekanik: " . $rowPrepared['mekanik'];
                                if (!empty($rowPrepared['timestamp'])) $processInfo[] = "Waktu: " . $rowPrepared['timestamp'];
                                if (!empty($rowPrepared['status'])) $processInfo[] = "Status: " . $rowPrepared['status'];
                                echo esc(implode(' | ', $processInfo));
                                ?>
                            </td>
                        </tr>
                        <?php if (!empty($rowPrepared['catatan'])): ?>
                        <tr>
                            <td class="label" style="background-color: #f8f9fa;">Catatan</td>
                            <td class="val" colspan="3" style="background-color: #f8f9fa;"><?= esc($rowPrepared['catatan']) ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
        
    <?php else: ?>
        <!-- Single unit display dengan tampilan yang lebih formal -->
        <div class="mb-2" style="font-style: italic; color: #666;">
        </div>
        <?php
            $unit = $s['selected']['unit'] ?? null; 
            $attachment = $s['selected']['attachment'] ?? null;

            $summaryLeft = [
                ['ID Unit', $unit['no_unit'] ?? ''],
                ['Jenis Unit', $unit['jenis_unit'] ?? $s['jenis_unit'] ?? ''],
                ['Kapasitas', $unit['kapasitas_name'] ?? $s['kapasitas_id_name'] ?? ''],
                ['Attachment', $attachment['sn_attachment_formatted'] ?? $unit['attachment_display'] ?? ''],
                ['Baterai', $unit['sn_baterai_formatted'] ?? ''],
                ['Valve', $unit['valve'] ?? $s['valve_id_name'] ?? ''],
            ];
            $summaryRight = [
                ['Serial Number', $unit['serial_number'] ?? ''],
                ['Tipe Unit', $unit['tipe_jenis'] ?? $s['tipe_jenis'] ?? ''],
                ['Mast', $unit['mast'] ?? $s['mast_id_name'] ?? ''],
                ['Charger', $unit['sn_charger_formatted'] ?? ''],
                ['Roda & Ban', ($unit['roda'] ?? $s['roda_id_name'] ?? '') . 
                   (!empty($unit['roda'] ?? $s['roda_id_name'] ?? '') && !empty($unit['ban'] ?? $s['ban_id_name'] ?? '') ? ' & ' : '') . 
                   ($unit['ban'] ?? $s['ban_id_name'] ?? '')],
            ];
        ?>
        <table class="table grid-2">
            <tbody>
                <?php 
                    $rows = max(count($summaryLeft), count($summaryRight));
                    for ($i = 0; $i < $rows; $i++): 
                        $left = $summaryLeft[$i] ?? ['', ''];
                        $right = $summaryRight[$i] ?? ['', ''];
                ?>
                <tr>
                    <td class="label"><?= esc($left[0]) ?></td>
                    <td class="val"><?php 
                        if ($placeholder) {
                            echo esc('..............................');
                        } else {
                            $value = $left[1];
                            if (is_callable($value)) {
                                echo esc($value() ?: '');
                            } else {
                                echo esc($value ?: '');
                            }
                        }
                    ?></td>
                    <td class="label"><?= esc($right[0]) ?></td>
                    <td class="val"><?= esc($placeholder ? '..............................' : ($right[1] ?: '')) ?></td>
                </tr>
                <?php endfor; ?>
                <!-- Aksesoris row spans across both left and right columns -->
                <tr>
                    <td class="label">Aksesoris</td>
                    <td class="val" colspan="3">
                        <?php 
                            if ($placeholder) {
                                echo esc('..............................');
                            } else {
                                // Multiple ways the accessories might be stored
                                $aksText = '';
                                if (!empty($s['aksesoris'])) {
                                    if (is_array($s['aksesoris'])) {
                                        $aksText = implode(', ', $s['aksesoris']);
                                    } else if (is_string($s['aksesoris'])) {
                                        try {
                                            $aksArray = json_decode($s['aksesoris'], true);
                                            if (is_array($aksArray) && !empty($aksArray)) {
                                                $aksText = implode(', ', $aksArray);
                                            } else {
                                                $aksText = $s['aksesoris'];
                                            }
                                        } catch (Exception $e) {
                                            $aksText = $s['aksesoris'];
                                        }
                                    }
                                } else if (!empty($spk['persiapan_aksesoris_tersedia'])) {
                                    $aksText = $spk['persiapan_aksesoris_tersedia'];
                                }
                                echo esc($aksText ?: '');
                            }
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="row mt-2 signature-section">
        <div class="col sig">
            <div class="muted">Marketing</div>
            <?php 
                // Auto-approve untuk marketing karena yang buat SPK adalah marketing
                $currentUser = session()->get('user_name') ?? session()->get('username') ?? session()->get('nama') ?? null;
                $createdBy = $spk['created_by_name'] ?? $spk['created_by'] ?? $spk['marketing_name'] ?? $currentUser;
                
                // Marketing selalu APPROVED karena mereka yang membuat SPK
                echo '<div class="sig-stamp">APPROVED</div>';
                echo '<br/>';
                if ($createdBy && $createdBy !== '') {
                    echo '<div class="sig-name">(' . esc($createdBy) . ')</div>';
                } else {
                    echo '<div class="sig-name">(MARKETING)</div>';
                }
            ?>
        </div>
        <div class="col sig">
            <div class="muted">Bag.Persiapan Unit</div>
            <?php if (!empty($spk['persiapan_unit_tanggal_approve'])): ?>
                <div class="sig-stamp">APPROVED</div>
                <br/>
                <div class="sig-name">(<?= esc($spk['persiapan_unit_mekanik'] ?? '') ?>)</div>
            <?php else: ?>
                <br/><br/>
                <div>(..........................)</div>
            <?php endif; ?>
        </div>
        <div class="col sig">
            <div class="muted">Bag.Fabrikasi</div>
            <?php if (!empty($spk['fabrikasi_tanggal_approve'])): ?>
                <div class="sig-stamp">APPROVED</div>
                <br/>
                <div class="sig-name">(<?= esc($spk['fabrikasi_mekanik'] ?? '') ?>)</div>
            <?php else: ?>
                <br/><br/>
                <div>(..........................)</div>
            <?php endif; ?>
        </div>
        <div class="col sig">
            <div class="muted">Bag.Painting</div>
            <?php if (!empty($spk['painting_tanggal_approve'])): ?>
                <div class="sig-stamp">APPROVED</div>
                <br/>
                <div class="sig-name">(<?= esc($spk['painting_mekanik'] ?? '') ?>)</div>
            <?php else: ?>
                <br/><br/>
                <div>(..........................)</div>
            <?php endif; ?>
        </div>
        <div class="col sig">
            <div class="muted">Bag. PDI Pengecekan</div>
            <?php if (!empty($spk['pdi_tanggal_approve'])): ?>
                <div class="sig-stamp">APPROVED</div>
                <br/>
                <div class="sig-name">(<?= esc($spk['pdi_mekanik'] ?? '') ?>)</div>
            <?php else: ?>
                <br/><br/>
                <div>(..........................)</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Document footer -->
    <div class="mt-3 pt-2" style="border-top: 1px solid #dee2e6; font-size: 9px; color: #666; text-align: center;">
        <div class="row">
            <div class="col-4" style="text-align: left;">
                <strong>SPK No:</strong> <?= esc($spk['nomor_spk'] ?? $spk['no_spk'] ?? '-') ?>
            </div>
            <div class="col-4" style="text-align: center;">
                PT SARANA MITRA LUAS Tbk
            </div>
            <div class="col-4" style="text-align: right;">
                <strong>Tanggal Cetak:</strong> <?= date('d/m/Y H:i') ?>
            </div>
        </div>
    </div>

    <!-- Optional print footer - visible only when printing -->
    <div class="print-footer" id="printFooter">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span>PT SARANA MITRA LUAS Tbk</span>
            <span><strong>SPK (Persiapan Unit)</strong></span>
            <span>Halaman <span id="currentPage">1</span> dari <span id="totalPages">1</span></span>
        </div>
    </div>

</div>

<script>
// Set document title for download filename
document.addEventListener('DOMContentLoaded', function() {
    const spkNumber = '<?= str_replace('/', '-', esc($spk['nomor_spk'] ?? $spk['no_spk'] ?? 'Unknown')) ?>';
    const fileName = 'SPK-' + spkNumber;
    document.title = fileName;
    
    // Add page numbering functionality
    addPageNumbers();
});

// Add page numbering functionality
function addPageNumbers() {
    // Simple page counting for print
    const footer = document.getElementById('printFooter');
    if (footer) {
        // This will be handled by browser's print system
        // JavaScript page counting is limited in print context
        document.getElementById('currentPage').textContent = '1';
        document.getElementById('totalPages').textContent = '1';
    }
}

// Option to toggle footer
function togglePrintFooter(show = true) {
    const footer = document.getElementById('printFooter');
    if (footer) {
        footer.style.display = show ? 'block' : 'none';
    }
}

// Auto-show footer when printing and ensure title is set
window.addEventListener('beforeprint', function() {
    const spkNumber = '<?= str_replace('/', '-', esc($spk['nomor_spk'] ?? $spk['no_spk'] ?? 'Unknown')) ?>';
    document.title = 'SPK-' + spkNumber;
    togglePrintFooter(true);
});
</script>

</body>
</html>