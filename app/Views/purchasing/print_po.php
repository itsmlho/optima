<?php
$po = $po ?? [];
$supplier = $supplier ?? [];
$items = $items ?? [];
$deliveries = $deliveries ?? [];
$status = strtoupper((string)($po['status'] ?? ''));
$placeholder = ($status === 'PENDING' || $status === 'SUBMITTED');
?>
<?php
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
    <title><?= 'PO-' . str_replace('/', '-', esc($po['no_po'] ?? 'Unknown')) ?></title>
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
                margin: 10mm 8mm 15mm 8mm; /* Bottom margin for footer area */
                size: A4;
                @top-left { content: ""; }
                @top-center { content: ""; }
                @top-right { content: ""; }
                @bottom-left { 
                    content: "PT SARANA MITRA LUAS Tbk | Sistem OPTIMA - Document Management";
                    font-size: 7px;
                    color: #666;
                }
                @bottom-center { 
                    content: "Tanggal Cetak: <?= date('d/m/Y H:i') ?>";
                    font-size: 7px;
                    color: #666;
                }
                @bottom-right { 
                    content: "Halaman " counter(page) " | PO No: <?= esc($po['no_po'] ?? 'Unknown') ?>";
                    font-size: 7px;
                    color: #666;
                }
            }
            
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .page {
                padding-bottom: 0 !important;
                margin-bottom: 0 !important;
            }
            
            .page-break {
                page-break-before: always;
            }
            
            .no-print {
                display: none !important;
            }
            
            /* Allow tables to break across pages cleanly */
            table {
                page-break-inside: auto;
                page-break-after: auto;
                margin-bottom: 8px !important;
            }
            
            /* Allow table rows to break */
            tr {
                page-break-inside: auto;
                page-break-after: auto;
            }
            
            /* Prevent breaking inside table header */
            thead {
                display: table-header-group;
            }
            
            /* Allow item cards to break across pages if needed */
            .item-card {
                page-break-inside: auto;
                page-break-after: auto;
                margin-bottom: 10px !important;
            }
            
            /* Serial number section can break if too long */
            .serial-numbers-section {
                page-break-inside: auto;
            }
            
            /* Allow serial number grid to break */
            .serial-numbers-section > div {
                page-break-inside: auto;
            }
            
            /* Prevent orphaned content */
            .item-details {
                orphans: 3;
                widows: 3;
            }
            
            /* Hide HTML footer in print - using @page @bottom instead for auto page numbers */
            .print-footer {
                display: none !important;
            }
            
            /* Ensure content doesn't overlap with footer - content should break above footer area */
            .page {
                padding-bottom: 0 !important;
                margin-bottom: 0 !important;
            }
            
            /* Ensure content breaks before footer area - no content should go into footer space */
            body {
                margin-bottom: 0 !important;
                padding-bottom: 0 !important;
            }
            
            /* Add spacing to prevent content from going too close to footer area */
            .items-section {
                margin-bottom: 6px !important;
            }
            
            .approval-section {
                margin-bottom: 6px !important;
                padding-bottom: 3px !important;
            }
            
            .info-section {
                margin-bottom: 6px !important;
            }
            
            .notes-section {
                margin-bottom: 6px !important;
            }
            
            /* Ensure last section has enough space before footer */
            .approval-section:last-of-type {
                margin-bottom: 10px !important;
                padding-bottom: 5px !important;
            }
            
            .items-section:last-child {
                margin-bottom: 10px !important;
            }
            
            /* Reduce spacing in item details */
            .item-details > div {
                margin-bottom: 4px !important;
            }
            
            /* Add spacing for sections */
            .items-section,
            .approval-section,
            .info-section,
            .notes-section {
                margin-bottom: 10px !important;
            }
            
            /* Ensure sections have minimal spacing */
            .item-card > .item-details > div {
                margin-bottom: 6px !important;
            }
            
            /* Ensure content has proper spacing - reduced for better flow */
            .items-section {
                margin-bottom: 10px !important;
                display: block !important;
                visibility: visible !important;
            }
            
            .approval-section {
                margin-bottom: 10px !important;
            }
            
            .info-section {
                margin-bottom: 10px !important;
            }
            
            .notes-section {
                margin-bottom: 10px !important;
            }
            
            /* Reduce spacing in item details */
            .item-details {
                padding: 8px !important;
            }
            
            /* Reduce spacing between sections */
            .serial-numbers-section {
                margin-top: 6px !important;
                padding-top: 6px !important;
                margin-bottom: 6px !important;
            }
            
            /* Reduce spacing in packing list section */
            .packing-list-section {
                margin-top: 6px !important;
                padding-top: 6px !important;
                margin-bottom: 6px !important;
            }
            
            /* Ensure all content is visible */
            .item-card,
            .item-details,
            .item-header {
                display: block !important;
                visibility: visible !important;
            }
            
            /* Ensure tables are visible */
            .table {
                display: table !important;
                visibility: visible !important;
                width: 100% !important;
            }
        }
        
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 12px; 
            color: #000;
            background-color: #FFF;
            line-height: 1.4;
        }
        
        .page {
            padding: 0;
            margin: 0 auto;
            background: white;
            min-height: 100vh;
        }
        
        /* Header - Consistent with company template */
        .document-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 6px;
        }
        
        .company-info {
            flex: 1;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #000;
            margin-bottom: 2px;
        }
        
        .company-subtitle {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .document-title {
            text-align: center;
            flex: 1;
        }
        
        .document-title h1 {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
            color: #000;
        }
        
        .document-title h2 {
            font-size: 16px;
            margin: 5px 0 0 0;
            color: #000;
        }
        
        .document-meta {
            text-align: right;
            flex: 1;
        }
        
        .doc-number {
            font-size: 12px;
            margin-bottom: 2px;
        }
        
        .doc-date {
            font-size: 11px;
            color: #666;
        }
        
        /* Content Sections */
        .info-section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background: #f8f9fa;
            border: 1px solid #000;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 5px;
            text-align: center;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 8px;
        }
        
        .info-item {
            display: flex;
            margin-bottom: 3px;
        }
        
        .info-label {
            width: 120px;
            font-weight: bold;
            flex-shrink: 0;
            font-size: 11px;
        }
        
        .info-value {
            flex: 1;
            border-bottom: 1px dotted #ccc;
            min-height: 16px;
            padding-left: 4px;
            font-size: 11px;
        }
        
        .info-full {
            grid-column: 1 / -1;
        }
        
        /* Items Section */
        .items-section {
            border: 1px solid #000;
            padding: 8px;
            margin: 15px 0;
            margin-bottom: 20px;
            page-break-inside: auto;
            min-height: 100px;
        }
        
        .items-header {
            background: #e8f4fd;
            border: 1px solid #0ea5e9;
            padding: 4px;
            margin: -8px -8px 8px -8px;
            font-weight: bold;
            text-align: center;
            font-size: 12px;
        }
        
        /* Table styling consistent with company template */
        .table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 12px;
            display: table !important;
            visibility: visible !important;
        }
        .table th, .table td { 
            border: 1px solid #9aa1a7; 
            padding: .4rem .5rem; 
            vertical-align: top; 
            line-height: 1.3;
            display: table-cell !important;
            visibility: visible !important;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        .table td {
            min-height: 25px;
        }
        .table thead {
            display: table-header-group !important;
        }
        .table tbody {
            display: table-row-group !important;
        }
        .table tr {
            display: table-row !important;
        }
        .label { color:#374151; }
        .val   { color:#111827; font-weight: 600; }
        .grid-2 td { width: 25%; }
        
        /* Approval Section */
        .approval-section {
            margin-top: 15px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .approval-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-top: 5px;
        }
        
        .approval-box {
            text-align: center;
            border: 1px solid #000;
            padding: 6px 4px;
            min-height: 60px;
        }
        
        .approval-title {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        
        .approval-subtitle {
            font-size: 8px;
            color: #666;
            margin-bottom: 4px;
        }
        
        .approval-status {
            margin: 4px 0;
            min-height: 15px;
        }
        
        .approved-stamp {
            color: #059669;
            border: 1px solid #059669;
            padding: 2px 4px;
            font-size: 9px;
            font-weight: bold;
            display: inline-block;
            transform: rotate(-15deg);
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            margin: 4px 8px 2px 8px;
        }
        
        .signature-label {
            font-size: 8px;
            color: #666;
        }
        
        /* Notes Section */
        .notes-section {
            margin-top: 15px;
            margin-bottom: 15px;
            border: 1px solid #000;
            padding: 6px;
            page-break-inside: avoid;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 4px;
            font-size: 11px;
        }
        
        .placeholder {
            color: #999;
            font-style: italic;
        }
        
        /* Footer */
        .document-footer {
            margin-top: 8px;
            padding-top: 4px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
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

<div class="page">
    <!-- Document Header -->
    <div class="document-header">
        <img src="<?= base_url('assets/images/company-logo.svg') ?>" class="logo" alt="Company Logo" style="width: 160px; height: auto; margin-right: 50px;"/>
        <div class="document-title">
            <h1>PT. SARANA MITRA LUAS</h1>
            <h2>PURCHASE ORDER</h2>
        </div>
        <div class="document-meta">
            <div class="doc-number"><strong>No: <?= esc($po['no_po'] ?? '-') ?></strong></div>
            <div class="doc-date">Tanggal: <?= date('d F Y', strtotime($po['created_at'] ?? date('Y-m-d'))) ?></div>
        </div>
    </div>

    <!-- Document Information -->
    <div class="info-section">
        <div class="section-title">INFORMASI PURCHASE ORDER</div>
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <span class="info-label">No PO:</span>
                    <span class="info-value"><?= esc($po['no_po'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Supplier:</span>
                    <span class="info-value"><?= esc($supplier['nama_supplier'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Kontak:</span>
                    <span class="info-value"><?= esc($supplier['contact_person'] ?? '-') ?></span>
                </div>
            </div>
            <div>
                <div class="info-item">
                    <span class="info-label">Tanggal PO:</span>
                    <span class="info-value"><?= date('d/m/Y', strtotime($po['created_at'] ?? date('Y-m-d'))) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="info-value"><?= esc($po['status'] ?? 'PENDING') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Telepon:</span>
                    <span class="info-value"><?= esc($supplier['phone'] ?? '-') ?></span>
                </div>
            </div>
        </div>
    </div>


    <!-- Items Section -->
    <div class="items-section" style="margin-bottom: 25px;">
        <div class="items-header">
            DAFTAR ITEM PURCHASE ORDER
        </div>

        <?php if (!empty($items)): ?>
            <?php 
            // Group items by specification (item_name and item_type)
            $groupedItems = [];
            foreach ($items as $item) {
                $key = ($item['item_name'] ?? 'Unknown') . '|' . ($item['item_type'] ?? 'Unit');
                if (!isset($groupedItems[$key])) {
                    $groupedItems[$key] = [
                        'item_name' => $item['item_name'] ?? 'Unknown',
                        'item_type' => $item['item_type'] ?? 'Unit',
                        'total_qty' => 0,
                        'packing_lists' => [],
                        'serial_numbers' => [],
                        'all_items' => [], // Store all items for serial number display
                        'first_item' => $item // Store first item for detailed specs
                    ];
                }
                $groupedItems[$key]['total_qty'] += $item['qty_ordered'] ?? $item['qty'] ?? 1;
                if (!empty($item['packing_lists'])) {
                    $groupedItems[$key]['packing_lists'] = array_merge($groupedItems[$key]['packing_lists'], $item['packing_lists']);
                }
                // Store all items (including those without SN) for serial number display
                $groupedItems[$key]['all_items'][] = $item;
                // Also collect serial numbers separately for backward compatibility
                if (!empty($item['serial_number']) && $item['serial_number'] !== '-') {
                    $groupedItems[$key]['serial_numbers'][] = $item['serial_number'];
                }
            }
            ?>
            
            <?php foreach ($groupedItems as $groupedItem): ?>
                <div class="item-card" style="border: 1px solid #dee2e6; margin-bottom: 15px; border-radius: 8px; overflow: hidden;">
                    <!-- Item Header -->
                    <div class="item-header" style="background: #f8f9fa; padding: 10px 12px; border-bottom: 1px solid #dee2e6;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                            <div>
                                <strong style="font-size: 14px; color: #1f2937;"><?= esc($groupedItem['item_name'] ?? 'Item') ?></strong>
                                <span class="badge" style="background: #007bff; color: white; padding: 3px 8px; border-radius: 4px; font-size: 11px; margin-left: 10px; font-weight: bold;">
                                    <?= esc($groupedItem['total_qty']) ?> <?= esc($groupedItem['item_type'] ?? 'ITEM') ?>
                                </span>
                            </div>
                            <div style="font-size: 11px; color: #6c757d; text-align: right;">
                                <div>Sudah Dikirim: <strong><?= count($groupedItem['packing_lists']) ?></strong></div>
                                <div style="font-size: 10px; color: #9ca3af;">Status: PENDING</div>
                            </div>
                        </div>
                        <div style="font-size: 11px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 5px;">
                            <strong>Item:</strong> <?= esc($groupedItem['item_name'] ?? '-') ?>
                        </div>
                    </div>
                    
                    <!-- Item Details -->
                    <div class="item-details" style="padding: 8px;">
                        <!-- Spesifikasi Detail -->
                        <div style="margin-bottom: 8px;">
                            <strong style="color: #374151; font-size: 12px;">Spesifikasi Detail:</strong>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-top: 6px; font-size: 11px;">
                                <?php 
                                // Use first_item stored in groupedItem
                                $firstItem = $groupedItem['first_item'] ?? null;
                                ?>
                                
                                <?php if ($groupedItem['item_type'] === 'Unit' && $firstItem): ?>
                                    <!-- Unit Specifications -->
                                    <div>
                                        <strong style="color: #374151;">Departemen:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['nama_departemen'] ?? '-') ?></span>
                                    </div>
                                    <div>
                                        <strong style="color: #374151;">Jenis Unit:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['jenis_unit'] ?? '-') ?></span>
                                    </div>
                                    <div>
                                        <strong style="color: #374151;">Brand:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['merk_unit'] ?? '-') ?></span>
                                    </div>
                                    <div>
                                        <strong style="color: #374151;">Model:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['model_unit'] ?? '-') ?></span>
                                    </div>
                                    <div>
                                        <strong style="color: #374151;">Tahun:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['tahun_po'] ?? '-') ?></span>
                                    </div>
                                    <div>
                                        <strong style="color: #374151;">Kapasitas:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['kapasitas_unit'] ?? '-') ?></span>
                                    </div>
                                    <div>
                                        <strong style="color: #374151;">Mast Type:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['tipe_mast'] ?? '-') ?></span>
                                    </div>
                                    <div>
                                        <strong style="color: #374151;">Engine Type:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['merk_mesin'] ?? '-') ?></span>
                                    </div>
                                    <div>
                                        <strong style="color: #374151;">Tire Type:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['tipe_ban'] ?? '-') ?></span>
                                    </div>
                                    <div>
                                        <strong style="color: #374151;">Wheel Type:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['tipe_roda'] ?? '-') ?></span>
                                    </div>
                                    <div>
                                        <strong style="color: #374151;">Valve:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['jumlah_valve'] ?? '-') ?></span>
                                    </div>
                                    <div>
                                        <strong style="color: #374151;">Keterangan:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['keterangan'] ?? '-') ?></span>
                                    </div>
                                    
                                <?php elseif ($groupedItem['item_type'] === 'Attachment' && $firstItem): ?>
                                    <!-- Attachment Specifications -->
                                    <div>
                                        <strong style="color: #374151;">Tipe Attachment:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['tipe_attachment'] ?? '-') ?></span>
                                    </div>
                                    <div>
                                        <strong style="color: #374151;">Merk:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['merk_attachment'] ?? '-') ?></span>
                                    </div>
                                    <div>
                                        <strong style="color: #374151;">Model:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['model_attachment'] ?? '-') ?></span>
                                    </div>
                                    <div>
                                        <strong style="color: #374151;">Keterangan:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['keterangan'] ?? '-') ?></span>
                                    </div>
                                    
                                <?php elseif ($groupedItem['item_type'] === 'Battery' && $firstItem): ?>
                                    <!-- Battery Specifications -->
                                    <div>
                                        <strong style="color: #374151;">Jenis Battery:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['jenis_baterai'] ?? '-') ?></span>
                                    </div>
                                    <div>
                                        <strong style="color: #374151;">Merk Battery:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['merk_baterai'] ?? '-') ?></span>
                                    </div>
                                    <div>
                                        <strong style="color: #374151;">Tipe Battery:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['tipe_baterai'] ?? '-') ?></span>
                                    </div>
                                    <div>
                                        <strong style="color: #374151;">Keterangan:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['keterangan'] ?? '-') ?></span>
                                    </div>
                                    
                                <?php elseif ($groupedItem['item_type'] === 'Charger' && $firstItem): ?>
                                    <!-- Charger Specifications -->
                                    <div>
                                        <strong style="color: #374151;">Merk Charger:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['merk_charger'] ?? '-') ?></span>
                                    </div>
                                    <div>
                                        <strong style="color: #374151;">Tipe Charger:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['tipe_charger'] ?? '-') ?></span>
                                    </div>
                                    <div>
                                        <strong style="color: #374151;">Keterangan:</strong>
                                        <span style="color: #111827;"><?= esc($firstItem['keterangan'] ?? '-') ?></span>
                                    </div>
                                    
                                <?php else: ?>
                                    <!-- Default Specifications -->
                                    <div>
                                        <strong style="color: #374151;">Tipe Item:</strong>
                                        <span style="color: #111827;"><?= esc($groupedItem['item_type'] ?? '-') ?></span>
                                    </div>
                                    <div>
                                        <strong style="color: #374151;">Nama Item:</strong>
                                        <span style="color: #111827;"><?= esc($groupedItem['item_name'] ?? '-') ?></span>
                                    </div>
                                    <div>
                                        <strong style="color: #374151;">Deskripsi:</strong>
                                        <span style="color: #111827;"><?= esc($groupedItem['item_description'] ?? '-') ?></span>
                                    </div>
                                    <div>
                                        <strong style="color: #374151;">Keterangan:</strong>
                                        <span style="color: #111827;">-</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Serial Numbers Section -->
                        <div class="serial-numbers-section" style="margin-top: 6px; padding-top: 6px; border-top: 1px solid #e9ecef;">
                            <div style="margin-bottom: 8px;">
                                <strong style="color: #f59e0b; font-size: 12px;">
                                    <i class="fas fa-barcode" style="margin-right: 5px;"></i>Serial Number:
                                </strong>
                            </div>
                            <div style="padding: 8px; background: #f8f9fa; border-radius: 4px; border: 1px solid #e9ecef; margin-bottom: 10px; page-break-inside: auto;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 10px; page-break-inside: auto;">
                                    <?php 
                                    $allItems = $groupedItem['all_items'] ?? [];
                                    $totalItems = count($allItems);
                                    for ($i = 0; $i < $totalItems; $i += 2): 
                                        $item1 = $allItems[$i] ?? null;
                                        $item2 = ($i + 1 < $totalItems) ? ($allItems[$i + 1] ?? null) : null;
                                        
                                        // Get serial number based on item type
                                        $sn1 = '';
                                        $sn2 = '';
                                        if ($item1) {
                                            if ($item1['item_type'] === 'Unit') {
                                                $sn1 = $item1['serial_number_po'] ?? $item1['serial_number'] ?? '';
                                            } else {
                                                $sn1 = $item1['serial_number'] ?? '';
                                            }
                                        }
                                        if ($item2) {
                                            if ($item2['item_type'] === 'Unit') {
                                                $sn2 = $item2['serial_number_po'] ?? $item2['serial_number'] ?? '';
                                            } else {
                                                $sn2 = $item2['serial_number'] ?? '';
                                            }
                                        }
                                    ?>
                                    <div style="display: flex; align-items: center; gap: 5px;">
                                        <span style="background: #e5e7eb; color: #374151; padding: 2px 6px; border-radius: 3px; font-size: 9px; min-width: 25px; text-align: center;"><?= $i + 1 ?></span>
                                        <code style="flex: 1; <?= $sn1 ? 'color: #059669;' : 'color: #6b7280; font-style: italic;' ?> font-size: 9px;"><?= $sn1 ? esc($sn1) : 'Belum ada SN' ?></code>
                                    </div>
                                    <?php if ($item2): ?>
                                    <div style="display: flex; align-items: center; gap: 5px;">
                                        <span style="background: #e5e7eb; color: #374151; padding: 2px 6px; border-radius: 3px; font-size: 9px; min-width: 25px; text-align: center;"><?= $i + 2 ?></span>
                                        <code style="flex: 1; <?= $sn2 ? 'color: #059669;' : 'color: #6b7280; font-style: italic;' ?> font-size: 9px;"><?= $sn2 ? esc($sn2) : 'Belum ada SN' ?></code>
                                    </div>
                                    <?php else: ?>
                                    <div></div>
                                    <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Packing List Section (Table format only for packing lists) -->
                        <div class="packing-list-section" style="margin-top: 6px; padding-top: 6px; border-top: 1px solid #e9ecef; margin-bottom: 6px;">
                            <strong style="color: #374151;">Packing List Terkait:</strong>
                            <?php if (!empty($groupedItem['packing_lists'])): ?>
                                <table class="table" style="margin-top: 8px; margin-bottom: 10px; font-size: 10px;">
                                    <thead>
                                        <tr>
                                            <th style="width: 20%; background: #f8f9fa;">Packing List No</th>
                                            <th style="width: 15%; background: #f8f9fa;">Tanggal</th>
                                            <th style="width: 20%; background: #f8f9fa;">Driver</th>
                                            <th style="width: 15%; background: #f8f9fa;">Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($groupedItem['packing_lists'] as $pl): ?>
                                            <tr>
                                                <td><?= esc($pl['packing_list_no'] ?? '-') ?></td>
                                                <td><?= esc($pl['delivery_date'] ? date('d/m/Y', strtotime($pl['delivery_date'])) : '-') ?></td>
                                                <td><?= esc($pl['driver_name'] ?? '-') ?></td>
                                                <td class="text-center"><?= esc($pl['qty'] ?? '1') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div style="color: #6c757d; font-style: italic; margin-top: 4px;">Belum ada packing list</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 20px; color: #6c757d; font-style: italic;">
                Tidak ada item
            </div>
        <?php endif; ?>
    </div>

    <!-- Delivery Information -->
    <?php if (!empty($deliveries)): ?>
        <div class="info-section">
            <div class="section-title">INFORMASI PENGIRIMAN</div>
            <div class="info-grid">
                <div>
                    <div class="info-item">
                        <span class="info-label">Total Pengiriman:</span>
                        <span class="info-value"><?= count($deliveries) ?> kali</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status Pengiriman:</span>
                        <span class="info-value">
                            <?php
                                $delivered = 0;
                                $total = count($deliveries);
                                foreach ($deliveries as $delivery) {
                                    if ($delivery['status'] === 'DELIVERED') {
                                        $delivered++;
                                    }
                                }
                                echo $delivered . '/' . $total . ' pengiriman selesai';
                            ?>
                        </span>
                    </div>
                </div>
                <div>
                    <div class="info-item">
                        <span class="info-label">Tanggal Terakhir:</span>
                        <span class="info-value">
                            <?php
                                $lastDelivery = null;
                                foreach ($deliveries as $delivery) {
                                    if ($delivery['delivery_date'] && (!$lastDelivery || $delivery['delivery_date'] > $lastDelivery)) {
                                        $lastDelivery = $delivery['delivery_date'];
                                    }
                                }
                                echo $lastDelivery ? date('d/m/Y', strtotime($lastDelivery)) : '-';
                            ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Progress:</span>
                        <span class="info-value">
                            <?php
                                $progress = $total > 0 ? round(($delivered / $total) * 100) : 0;
                                echo $progress . '%';
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Notes Section -->
    <?php if (!empty($po['notes'])): ?>
        <div class="notes-section">
            <div class="notes-title">CATATAN KHUSUS</div>
            <div><?= nl2br(esc($po['notes'])) ?></div>
        </div>
    <?php endif; ?>

    <!-- Approval Section -->
    <div class="approval-section" style="margin-bottom: 30px;">
        <div class="section-title">PERSETUJUAN & TANDA TANGAN</div>
        
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-top: 15px;">
            <div style="text-align: center;">
                <div style="font-weight: bold; margin-bottom: 5px;">PURCHASING</div>
                <div style="font-size: 10px; color: #666; margin-bottom: 10px;">Pembuat PO</div>
                <?php 
                    $purchasingName = resolvePrintSignerName(
                        $po,
                        ['created_by_full_name', 'created_by_name', 'purchasing_name'],
                        ''
                    );
                    if (!empty($purchasingName)): 
                ?>
                    <div style="color: #059669; border: 2px solid #059669; padding: 3px 8px; font-size: 10px; font-weight: bold; display: inline-block; transform: rotate(-15deg); margin: 10px 0;">APPROVED</div>
                    <br/>
                    <div style="font-size: 10px; margin-top: 5px;">(<?= esc($purchasingName) ?>)</div>
                <?php else: ?>
                    <br/><br/>
                    <div style="border-bottom: 1px solid #000; margin: 10px 20px 5px 20px;"></div>
                    <div style="font-size: 9px; color: #666;">(...........................)</div>
                <?php endif; ?>
                <div style="font-size: 9px; color: #666; margin-top: 5px;">
                    Tanggal: <?= date('d/m/Y', strtotime($po['created_at'] ?? date('Y-m-d'))) ?>
                </div>
            </div>

            <div style="text-align: center;">
                <div style="font-weight: bold; margin-bottom: 5px;">MANAGER</div>
                <div style="font-size: 10px; color: #666; margin-bottom: 10px;">Approval Manager</div>
                <br/><br/>
                <div style="border-bottom: 1px solid #000; margin: 10px 20px 5px 20px;"></div>
                <div style="font-size: 9px; color: #666;">(...........................)</div>
                <div style="font-size: 9px; color: #666; margin-top: 5px;">Tanggal: __________</div>
            </div>

            <div style="text-align: center;">
                <div style="font-weight: bold; margin-bottom: 5px;">SUPPLIER</div>
                <div style="font-size: 10px; color: #666; margin-bottom: 10px;">Konfirmasi Supplier</div>
                <br/><br/>
                <div style="border-bottom: 1px solid #000; margin: 10px 20px 5px 20px;"></div>
                <div style="font-size: 9px; color: #666;">(...........................)</div>
                <div style="font-size: 9px; color: #666; margin-top: 5px;">Tanggal: __________</div>
            </div>

            <div style="text-align: center;">
                <div style="font-weight: bold; margin-bottom: 5px;">WAREHOUSE</div>
                <div style="font-size: 10px; color: #666; margin-bottom: 10px;">Penerima Barang</div>
                <br/><br/>
                <div style="border-bottom: 1px solid #000; margin: 10px 20px 5px 20px;"></div>
                <div style="font-size: 9px; color: #666;">(...........................)</div>
                <div style="font-size: 9px; color: #666; margin-top: 5px;">Tanggal: __________</div>
            </div>
        </div>
    </div>

    <!-- Print Footer - fixed position for every page -->
    <div class="print-footer" id="printFooter" style="display: block;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 2px 0;">
            <div style="text-align: left; font-size: 7px; flex: 1;">
                <strong>PT SARANA MITRA LUAS Tbk</strong> | <span style="color: #888;">Sistem OPTIMA - Document Management</span>
            </div>
            <div style="text-align: center; font-size: 7px; flex: 1;">
                <span>Tanggal Cetak: <?= date('d/m/Y H:i') ?></span>
            </div>
            <div style="text-align: right; font-size: 7px; flex: 1;">
                <span class="page-number">Halaman <span id="currentPageNum">1</span></span> | <span>PO No: <?= esc($po['no_po'] ?? 'Unknown') ?></span>
            </div>
        </div>
    </div>

</div>

<script>
    // Toggle Serial Numbers visibility
    function toggleSerialNumbers(elementId, iconId) {
        const element = document.getElementById(elementId);
        const icon = document.getElementById(iconId);
        
        if (element && icon) {
            if (element.style.display === 'none' || element.style.display === '') {
                element.style.display = 'block';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                element.style.display = 'none';
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }
    }

// Update page number - will be handled by browser print engine
// Note: Fixed footer shows same content on all pages, page numbers need special handling
function updatePageNumber() {
    // Browser print engine handles page numbers automatically
    // This is just for initial display
    const pageNumEl = document.getElementById('currentPageNum');
    if (pageNumEl) {
        pageNumEl.textContent = '1';
    }
}

// Auto print on load
window.addEventListener('load', () => {
    const poNumber = '<?= str_replace('/', '-', esc($po['no_po'] ?? 'Unknown')) ?>';
    document.title = 'PO-' + poNumber;
    updatePageNumber();
    
    // Show footer
    const footer = document.getElementById('printFooter');
    if (footer) {
        footer.style.display = 'block';
    }
});
</script>

</body>
</html>