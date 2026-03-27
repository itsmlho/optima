<?php
$spk = $spk ?? [];
$s   = $spesifikasi ?? [];
$k   = $kontrak_spesifikasi ?? []; // Data quotation_specifications untuk Equipment section
$status = strtoupper((string)($spk['status'] ?? $spk['status_spk'] ?? ''));
$placeholder = ($status === 'SUBMITTED');

if (!function_exists('resolvePrintSignerName')) {
    function resolvePrintSignerName(array $data, array $keys, string $fallback): string
    {
        foreach ($keys as $key) {
            $value = trim((string)($data[$key] ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        return $fallback;
    }
}
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
        /* Page setup for better margins - disable browser headers/footers */
        @page { 
            size: A4; 
            margin: 10mm 8mm 15mm 8mm; /* top right bottom left - further reduced margins for single unit */
        }
        
        @media print {
            @page {
                margin: 10mm 8mm 15mm 8mm; /* top right bottom left - further reduced margins for single unit */
                size: A4;
                /* Disable browser headers and footers */
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
                font-family: Arial, Helvetica, sans-serif;
                font-size: 9.5px; /* Further reduced font size for compact layout */
                color: #222;
                line-height: 1.15; /* Tighter line height */
            }
            
            /* Compact table for print */
            .table { 
                margin-bottom: 8px; 
            }
            
            .table th, .table td { 
                padding: .3rem .4rem; 
                line-height: 1.2;
            }
            
            /* Custom footer for each page */
            .print-footer {
                position: fixed;
                bottom: 3mm;
                left: 8mm;
                right: 8mm;
                text-align: center;
                font-size: 8px;
                color: #666;
                border-top: 1px solid #ddd;
                padding-top: 2mm;
                background: white;
                z-index: 1000;
                page-break-inside: avoid;
                /* Ensure footer doesn't conflict with browser headers */
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            /* Page break handling */
            .page-break {
                page-break-before: always;
            }
            
            /* Ensure content doesn't overlap with footer */
            .main-content {
                margin-bottom: 15mm; /* Reduced for single unit */
            }
            
            /* Better spacing for second page content */
            .prepared-units-section {
                margin-top: 8px; /* Compact spacing from previous section */
                page-break-inside: auto; /* Allow section to break if needed */
            }
            
            /* Ensure unit cards don't break awkwardly */
            .unit-card {
                page-break-inside: avoid;
                margin-bottom: 5px;
                margin-top: 5px;
            }
            
            /* Compact spacing for all SPK types */
            .prepared-units-section .table {
                margin-bottom: 8px;
            }
            
            /* Ensure table headers don't get cut off */
            .table thead {
                page-break-inside: avoid;
                page-break-after: avoid;
            }
            
            /* Better spacing for unit details */
            .unit-detail-section {
                margin-top: 1mm; /* Reduced for single unit */
                page-break-inside: avoid;
            }
            
            /* Auto page break for prepared units section if needed */
            .prepared-units-section:first-of-type {
                page-break-before: auto;
            }
            
            /* Ensure proper spacing for first unit on new page */
            .unit-detail-section:first-child {
                margin-top: 5mm;
            }
            
            /* Hide print instruction when printing */
            #printInstruction {
                display: none !important;
            }
            
            /* Disable browser headers and footers completely */
            @page {
                @top-left { content: ""; }
                @top-center { content: ""; }
                @top-right { content: ""; }
                @bottom-left { content: ""; }
                @bottom-center { content: ""; }
                @bottom-right { content: ""; }
            }
            
            /* Ensure no browser-generated content */
            body::before,
            body::after {
                content: none !important;
                display: none !important;
            }
        }
        
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color:#222; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .table th, .table td { 
            border: 1px solid #9aa1a7; 
            padding: .4rem .5rem; 
            vertical-align: top; 
            line-height: 1.3;
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
        .sig-stamp.approved { 
            color: #16a34a; 
            border-color: #16a34a; 
            background-color: #f0fdf4; 
        }
        .sig-stamp.rejected { 
            color: #dc2626; 
            border-color: #dc2626; 
            background-color: #fef2f2; 
        }
        .sig-name { font-weight: bold; color: #111; }
        .sig-date { font-size: 9px; color: #666; margin-top: 2px; }
        .title { font-size: 16px; font-weight: bold; margin:0; }
        .subtitle { font-size: 15px; color:#555; margin:0; }
        .k-box { height: 16px; width: 16px; border:1px solid #999; display:inline-block; margin-right:2px; }
        .dotted { color:#333; }
        .label { color:#374151; }
        .val   { color:#111827; font-weight: 600; }
        .grid-2 td { width: 25%; }
        .no-border td { border:none !important; }
        .logo { max-height: 60px; }
        .header { 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            padding: 10px 0;
            border-bottom: 2px solid #333;
        }
        .header-left { 
            flex: 0 0 auto; 
            display: flex; 
            align-items: center; 
        }
        .header-center { 
            flex: 1; 
            text-align: center; 
            padding: 0 20px;
        }
        .header-right { 
            flex: 0 0 auto; 
            text-align: right; 
            display: flex; 
            flex-direction: column; 
            align-items: flex-end;
        }
        .document-info {
            font-size: 10px;
            color: #333;
            margin-bottom: 5px;
        }
        .doc-number, .doc-spk, .doc-date {
            margin: 1px 0;
            font-weight: 500;
        }
        .status-badge {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 4px 8px;
            margin-top: 5px;
        }
        .status-label {
            font-size: 9px;
            font-weight: bold;
            color: #495057;
            text-transform: uppercase;
        }
        .header-separator {
            border: none;
            border-top: 1px solid #333;
            margin: 5px 0 15px 0;
        }
        /* Extra print-friendly blocks for multiple units */
        .unit-card { 
            border:1px solid #9aa1a7; 
            padding:8px; 
            margin-bottom:8px; 
            margin-top:5px;
            page-break-inside: avoid;
            clear: both;
        }
        
        /* For second and subsequent units, reduce spacing */
        .unit-card:nth-child(n+2) {
            margin-top: 5px;
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
            margin: 10px 0;
            border-top: 2px solid #dee2e6;
            padding-top: 10px;
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
        
        /* Additional spacing for professional layout */
        .document-header {
            margin-bottom: 10px;
        }
        
        .specification-section {
            margin-bottom: 8px;
        }
        
        /* Ensure proper spacing for multi-page documents */
        .page-content {
            min-height: 250mm; /* A4 height minus margins */
        }
        
        /* Better spacing for unit cards */
        .unit-card {
            margin-bottom: 8px;
            border: none;
            border-radius: 0;
            overflow: visible;
        }
        
        /* Compact signature section for single unit */
        .single-unit-optimization .signature-section {
            margin-top: 10px;
            padding-top: 8px;
        }
        
        .single-unit-optimization .sig {
            margin-bottom: 5px;
        }
        
        .single-unit-optimization .sig .muted {
            font-size: 8px;
            margin-bottom: 2px;
        }
        
        .single-unit-optimization .sig .approval-stamp {
            font-size: 8px;
            padding: 2px 4px;
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
<div class="main-content">

<div class="container-fluid page-content">
    <div class="header document-header mb-1">
        <div class="header-left">
            <img src="<?= base_url('assets/images/company-logo.svg') ?>" class="logo" alt="logo"/>
        </div>
        <div class="header-center">
            <div class="title">PT SARANA MITRA LUAS Tbk</div>
            <div class="subtitle">Work Orders (SPK)</div>
        </div>
        <div class="header-right">
            <div class="document-info">
                <div class="doc-number">No <?= esc($spk['nomor_spk'] ?? $spk['no_spk'] ?? '-') ?></div>
                <?php 
                    // Show quotation number or contract indicator
                    $hasContract = !empty($spk['kontrak_id']);
                    if ($hasContract) {
                        $sourceDisplay = 'Source: Contract-based';
                    } else {
                        // Show actual quotation number
                        $quotationNumber = $spk['quotation_number'] ?? '-';
                        $sourceDisplay = 'No Quotation: ' . $quotationNumber;
                    }
                ?>
                <div class="doc-spk"><?= $sourceDisplay ?></div>
                <div class="doc-date">Tanggal: <?= date('d F Y', strtotime($spk['created_at'] ?? $spk['dibuat_pada'] ?? date('Y-m-d'))) ?></div>
            </div>
            <div class="status-badge">
                <?php 
                    $jenisSpk = $spk['jenis_spk'] ?? 'UNIT';
                    $statusText = ($jenisSpk === 'ATTACHMENT') ? 'Persiapan Attachment' : 'Persiapan Unit';
                ?>
                <span class="status-label"><?= $statusText ?></span>
            </div>
        </div>
    </div>
    
    <!-- Garis pemisah -->
    <hr class="header-separator">
    <div class="row mb-1">
        <div class="col-6"><span class="label">Nama Perusahaan:</span> <span class="val"><?= esc($spk['pelanggan'] ?? $spk['customer_name'] ?? '-') ?></span></div>
        <div class="col-6"><span class="label">Kontak:</span> <span class="val"><?= esc($spk['kontak'] ?? '-') ?></span></div>
    </div>
    <div class="row mb-1">
        <div class="col-6"><span class="label">Lokasi:</span> <span class="val"><?= esc($spk['lokasi'] ?? '-') ?></span></div>
        <div class="col-6"><span class="label">PIC:</span> <span class="val"><?= esc($spk['pic'] ?? '-') ?></span></div>
    </div>
    <br />
    <!-- Keterangan section untuk kontrak spesifikasi -->
    <div class="specification-section">
        <div class="mb-1" style="font-style: italic; color: #666;">
            Permintaan Spesifikasi (Data Quotation Specifications)
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
                        <div>- Departemen</div>
                        <div>- Kapasitas</div>
                        <div>- Attachment</div>
                        <div>- Ban (Tire)</div>
                        <div>- Mast (Tinggi Angkat)</div>
                    </div>
                </td>
                <td class="align-top">
                    <?php 
                        // Use quotation_specifications data for Equipment section (data permintaan marketing)
                        $jumlahUnit = $k['jumlah_dibutuhkan'] ?? $spk['jumlah_unit'] ?? '';
                        $merkUnit = $k['merk_unit'] ?? '';
                        $modelUnit = $k['model_unit'] ?? '';
                        $jenisUnit = $k['kontrak_jenis_unit'] ?? $k['jenis_unit'] ?? '';
                        $tipeUnit = $k['kontrak_tipe_unit'] ?? $k['tipe_jenis'] ?? '';
                        $kapasitasName = $k['kontrak_kapasitas_name'] ?? $k['kapasitas_name'] ?? '';
                        $departemenName = $k['kontrak_departemen_name'] ?? $k['departemen_name'] ?? '';
                        $mastName = $k['kontrak_mast_name'] ?? $k['mast_name'] ?? '';
                        $banName = $k['kontrak_ban_name'] ?? $k['ban_name'] ?? '';
                        
                        // Attachment dari quotation_specifications (bukan dari spesifikasi SPK)
                        $attachmentType = $k['attachment_tipe'] ?? $k['attachment_name'] ?? '';
                        
                        // Notes untuk custom requirements (Battery, Charger, Valve custom, dll)
                        $customNotes = $k['notes'] ?? '';
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
                        <div class="val"><?= esc($departemenName ?: '..............................') ?></div>
                        <div class="val"><?= esc($kapasitasName ?: '..............................') ?></div>
                        <div class="val"><?= esc($attachmentType ?: '..............................') ?></div>
                        <div class="val"><?= esc($banName ?: '..............................') ?></div>
                        <div class="val"><?= esc($mastName ?: '..............................') ?></div>
                    </div>
                </td>
            </tr>
            <?php if (!empty($customNotes)): ?>
            <tr>
                <td class="text-center align-top" style="background-color: #fff3cd;"></td>
                <td class="align-top" style="background-color: #fff3cd;"><strong>Custom Requirements :</strong></td>
                <td class="align-top" style="background-color: #fff3cd; white-space: pre-line; font-size: 8.5px;"><?= nl2br(esc($customNotes)) ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td class="text-center align-middle">3.</td>
                <td class="align-middle"><strong>Aksesoris</strong></td>
                <td class="val"><?php
                    // Use aksesoris from quotation_specifications (data permintaan marketing)
                    $aksText = '..............................';
                    
                    // Prioritaskan aksesoris dari quotation_specifications
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
                        // Fallback ke spesifikasi jika quotation_specifications tidak ada
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
    
    <!-- DEBUG: SPK data -->
    <!-- SPK ID: <?= $spk['id'] ?? 'NULL' ?> -->
    <!-- Spesifikasi ID: <?= $s['id'] ?? 'NULL' ?> -->
    <!-- Prepared units count: <?= isset($s['prepared_units_detail']) && is_array($s['prepared_units_detail']) ? count($s['prepared_units_detail']) : 'NULL' ?> -->
    
    <?php 
    // Get prepared units from spk_unit_stages table
    $preparedList = [];
    if (isset($spk['prepared_units_detail']) && is_array($spk['prepared_units_detail'])) {
        $preparedList = $spk['prepared_units_detail'];
    }
    
    // Detect if this is single unit for optimization
    $isSingleUnit = count($preparedList) === 1;
    $isMultiUnit = count($preparedList) > 1;
    $optimizationClass = $isSingleUnit ? 'single-unit-optimization compact-layout' : '';
    $multiUnitClass = $isMultiUnit ? 'multi-unit' : '';
    ?>
    <!-- Multi-unit display with enhanced formatting -->
    <br />
        <div class="prepared-units-section <?= $optimizationClass ?> <?= $multiUnitClass ?>">
            <div class="mb-2" style="font-style: italic; color: #666; display: flex; justify-content: space-between;">
                <span>Detail Unit yang Disiapkan (Data Aktual SPK Service)</span>
                <span>Total Unit Disiapkan: <?= count($preparedList) ?> unit</span>
            </div>
    <?php if (is_array($preparedList) && count($preparedList) >= 1): ?>
        
        <?php foreach ($preparedList as $i => $rowPrepared): ?>
            <div class="unit-card unit-detail-section <?= $optimizationClass ?>" style="page-break-inside: avoid;">
                <div class="mb-2" style="font-style: italic; color: #666; display: flex; justify-content: space-between;">
                    Unit <?= ($i + 1) ?>
                </div>
                <?php
                    // Build left/right summaries from spk_unit_stages data
                    $summaryLeft = [
                        ['No Unit', $rowPrepared['no_unit'] ?? ''],
                        ['Jenis Unit', $rowPrepared['jenis_unit'] ?? ''],
                        ['Departemen', $rowPrepared['departemen_name'] ?? ''],
                        ['Kapasitas', $rowPrepared['kapasitas_name'] ?? ''],
                        ['Mast', $rowPrepared['mast_name'] ?? ''],
                    ];
                    $summaryRight = [
                        ['Charger', $rowPrepared['charger_sn'] ?? ''],
                        ['Baterai', $rowPrepared['baterai_sn'] ?? ''],
                        ['Attachment', $rowPrepared['attachment_sn'] ?? ''],
                        ['Roda & Ban', trim(
                            ($rowPrepared['roda_name'] ?? '') .
                            ((!empty($rowPrepared['roda_name']) && !empty($rowPrepared['ban_name'])) ? ' & ' : '') .
                            ($rowPrepared['ban_name'] ?? '')
                        )],
                        ['Valve', $rowPrepared['valve_name'] ?? ''],
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
                            <td class="label" style="background-color: #f8f9fa;">Catatan</td>
                            <td class="val" colspan="3" style="background-color: #f8f9fa;">
                                <?= esc($rowPrepared['combined_notes'] ?? '') ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="row mt-2 signature-section <?= $optimizationClass ?>">
        <div class="col sig">
            <div class="muted">Marketing</div>
            <?php 
                // Auto-approve untuk marketing karena yang buat SPK adalah marketing
                $currentUser = trim(((string)session()->get('first_name')) . ' ' . ((string)session()->get('last_name')));
                if ($currentUser === '') {
                    $currentUser = session()->get('nama') ?? null;
                }
                $createdBy = resolvePrintSignerName(
                    $spk,
                    ['created_by_name', 'created_by_full_name', 'marketing_name', 'dibuat_oleh_name'],
                    $currentUser ?: 'MARKETING'
                );
                $createdAt = $spk['created_at'] ?? $spk['dibuat_pada'] ?? null;
                
                // Marketing selalu APPROVED karena mereka yang membuat SPK
                echo '<div class="sig-stamp approved">APPROVED</div>';
                echo '<br/>';
                if ($createdBy && $createdBy !== '') {
                    echo '<div class="sig-name">(' . esc($createdBy) . ')</div>';
                } else {
                    echo '<div class="sig-name">(MARKETING)</div>';
                }
                if ($createdAt) {
                    echo '<div class="sig-date">' . date('d/m/Y H:i', strtotime($createdAt)) . '</div>';
                }
            ?>
        </div>
        <div class="col sig">
            <div class="muted">Bag.Persiapan Unit</div>
            <?php 
            // Check for persiapan_unit stage approval from spk_unit_stages
            $persiapanApproved = false;
            $persiapanMekanik = '';
            if (isset($spk['stage_status']['unit_stages'])) {
                foreach ($spk['stage_status']['unit_stages'] as $unitIndex => $unitStages) {
                    if (isset($unitStages['persiapan_unit']) && $unitStages['persiapan_unit']['completed']) {
                        $persiapanApproved = true;
                        $persiapanMekanik = $unitStages['persiapan_unit']['mekanik'] ?? '';
                        break;
                    }
                }
            }
            ?>
            <?php if ($persiapanApproved): ?>
                <div class="sig-stamp approved">APPROVED</div>
                <br/>
                <div class="sig-name">(<?= esc($persiapanMekanik ?: '') ?>)</div>
                <?php if (isset($spk['stage_status']['unit_stages'])): ?>
                    <?php foreach ($spk['stage_status']['unit_stages'] as $unitIndex => $unitStages): ?>
                        <?php if (isset($unitStages['persiapan_unit']) && $unitStages['persiapan_unit']['completed'] && $unitStages['persiapan_unit']['tanggal_approve']): ?>
                            <div class="sig-date"><?= date('d/m/Y H:i', strtotime($unitStages['persiapan_unit']['tanggal_approve'])) ?></div>
                            <?php break; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php else: ?>
                <br/><br/>
                <div>(..........................)</div>
            <?php endif; ?>
        </div>
        <div class="col sig">
            <div class="muted">Bag.Fabrikasi</div>
            <?php 
            // Check for fabrikasi stage approval from spk_unit_stages
            $fabrikasiApproved = false;
            $fabrikasiMekanik = '';
            if (isset($spk['stage_status']['unit_stages'])) {
                foreach ($spk['stage_status']['unit_stages'] as $unitIndex => $unitStages) {
                    if (isset($unitStages['fabrikasi']) && $unitStages['fabrikasi']['completed']) {
                        $fabrikasiApproved = true;
                        $fabrikasiMekanik = $unitStages['fabrikasi']['mekanik'] ?? '';
                        break;
                    }
                }
            }
            ?>
            <?php if ($fabrikasiApproved): ?>
                <div class="sig-stamp approved">APPROVED</div>
                <br/>
                <div class="sig-name">(<?= esc($fabrikasiMekanik ?: '') ?>)</div>
                <?php if (isset($spk['stage_status']['unit_stages'])): ?>
                    <?php foreach ($spk['stage_status']['unit_stages'] as $unitIndex => $unitStages): ?>
                        <?php if (isset($unitStages['fabrikasi']) && $unitStages['fabrikasi']['completed'] && $unitStages['fabrikasi']['tanggal_approve']): ?>
                            <div class="sig-date"><?= date('d/m/Y H:i', strtotime($unitStages['fabrikasi']['tanggal_approve'])) ?></div>
                            <?php break; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php else: ?>
                <br/><br/>
                <div>(..........................)</div>
            <?php endif; ?>
        </div>
        <div class="col sig">
            <div class="muted">Bag.Painting</div>
            <?php 
            // Check for painting stage approval from spk_unit_stages
            $paintingApproved = false;
            $paintingMekanik = '';
            if (isset($spk['stage_status']['unit_stages'])) {
                foreach ($spk['stage_status']['unit_stages'] as $unitIndex => $unitStages) {
                    if (isset($unitStages['painting']) && $unitStages['painting']['completed']) {
                        $paintingApproved = true;
                        $paintingMekanik = $unitStages['painting']['mekanik'] ?? '';
                        break;
                    }
                }
            }
            ?>
            <?php if ($paintingApproved): ?>
                <div class="sig-stamp approved">APPROVED</div>
                <br/>
                <div class="sig-name">(<?= esc($paintingMekanik ?: '') ?>)</div>
                <?php if (isset($spk['stage_status']['unit_stages'])): ?>
                    <?php foreach ($spk['stage_status']['unit_stages'] as $unitIndex => $unitStages): ?>
                        <?php if (isset($unitStages['painting']) && $unitStages['painting']['completed'] && $unitStages['painting']['tanggal_approve']): ?>
                            <div class="sig-date"><?= date('d/m/Y H:i', strtotime($unitStages['painting']['tanggal_approve'])) ?></div>
                            <?php break; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php else: ?>
                <br/><br/>
                <div>(..........................)</div>
            <?php endif; ?>
        </div>
        <div class="col sig">
            <div class="muted">Bag. PDI Pengecekan</div>
            <?php 
            // Check for pdi stage approval from spk_unit_stages
            $pdiApproved = false;
            $pdiMekanik = '';
            if (isset($spk['stage_status']['unit_stages'])) {
                foreach ($spk['stage_status']['unit_stages'] as $unitIndex => $unitStages) {
                    if (isset($unitStages['pdi']) && $unitStages['pdi']['completed']) {
                        $pdiApproved = true;
                        $pdiMekanik = $unitStages['pdi']['mekanik'] ?? '';
                        break;
                    }
                }
            }
            ?>
            <?php if ($pdiApproved): ?>
                <div class="sig-stamp approved">APPROVED</div>
                <br/>
                <div class="sig-name">(<?= esc($pdiMekanik ?: '') ?>)</div>
                <?php if (isset($spk['stage_status']['unit_stages'])): ?>
                    <?php foreach ($spk['stage_status']['unit_stages'] as $unitIndex => $unitStages): ?>
                        <?php if (isset($unitStages['pdi']) && $unitStages['pdi']['completed'] && $unitStages['pdi']['tanggal_approve']): ?>
                            <div class="sig-date"><?= date('d/m/Y H:i', strtotime($unitStages['pdi']['tanggal_approve'])) ?></div>
                            <?php break; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php else: ?>
                <br/><br/>
                <div>(..........................)</div>
            <?php endif; ?>
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
    
    // Update print date
    const printDate = document.getElementById('printDate');
    if (printDate) {
        const now = new Date();
        const dateStr = now.toLocaleDateString('id-ID') + ' ' + now.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
        printDate.textContent = 'Tanggal Cetak: ' + dateStr;
    }
});

// Handle page numbering
window.addEventListener('afterprint', function() {
    togglePrintFooter(false);
});

// Update page numbers when printing
let currentPage = 1;
window.addEventListener('beforeprint', function() {
    currentPage = 1;
    updatePageNumbers();
});

function updatePageNumbers() {
    const pageInfo = document.getElementById('pageInfo');
    if (pageInfo) {
        pageInfo.innerHTML = 'Halaman <span id="currentPage">' + currentPage + '</span>';
    }
}
</script>

</div> <!-- End main-content -->

<!-- Print Footer -->
<div class="print-footer" id="printFooter" style="display: none;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div style="text-align: left; font-size: 8px;">
            <strong>PT SARANA MITRA LUAS Tbk</strong><br>
            <span style="color: #888;">Sistem OPTIMA - Document Management</span>
        </div>
        <div style="text-align: center; font-size: 8px;">
            <span id="printDate">Tanggal Cetak: <?= date('d/m/Y H:i') ?></span><br>
            <span style="color: #888;">Dokumen ini dibuat secara otomatis oleh sistem OPTIMA</span>
        </div>
        <div style="text-align: right; font-size: 8px;">
            <span id="pageInfo">Halaman <span id="currentPage">1</span></span><br>
            <span style="color: #888;">SPK No: <?= esc($spk['nomor_spk'] ?? $spk['no_spk'] ?? 'Unknown') ?></span>
        </div>
    </div>
</div>

</body>
</html>