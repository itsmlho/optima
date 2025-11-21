<?php
$delivery = $delivery ?? [];
$packingList = $packingList ?? [];
$items = $items ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= 'Packing List-' . esc($packingList['packing_list_no'] ?? 'Unknown') ?></title>
    <!-- jQuery for consistency -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        @page {
            size: A4;
            margin: 1cm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
            line-height: 1.2;
            color: #333;
        }
        
        .print-container { width: 100%; }
        
        /* --- Header --- */
        .document-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 6px;
            border-bottom: 1px solid #000;
            padding-bottom: 6px;
        }
        
        .header-left { 
            display: flex; 
            align-items: center; 
            flex: 1;
        }
        
        .company-logo {
            width: 80px;
            height: auto;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .company-info {
            flex: 1;
            text-align: center;
        }
        
        .company-name {
            font-size: 12pt;
            font-weight: bold;
            color: #000;
            margin-bottom: 2px;
        }
        
        .company-tagline {
            font-size: 8pt;
            color: #666;
            font-style: italic;
        }
        
        .company-address {
            font-size: 7pt;
            color: #666;
        }
        
        .company-phone {
            font-size: 7pt;
            color: #666;
        }
        
        .header-right { border: 1px solid #aaa; }
        .meta-table { border-collapse: collapse; }
        .meta-table td {
            padding: 2px 6px;
            font-size: 8pt;
            border: 1px solid #aaa;
        }
        .meta-table td:first-child {
            font-weight: bold;
            background-color: #f5f5f5;
        }
        
        .document-title {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 6px;
            color: #000;
        }
        
        /* --- Panel Konten --- */
        .content-panel {
            border: 1px solid #ccc;
            margin-bottom: 8px;
            page-break-inside: auto;
        }
        
        .panel-title {
            font-size: 8pt;
            font-weight: bold;
            text-align: center;
            padding: 4px;
            border-bottom: 1px solid #ccc;
            background-color: #f5f5f5;
            color: #000;
        }
        
        .panel-body { 
            padding: 6px; 
        }
        
        /* Info Section */
        .info-section {
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 8px;
            text-transform: uppercase;
            border-bottom: 1px solid #ddd;
            padding-bottom: 4px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 2px 1fr;
            gap: 20px;
            margin-bottom: 10px;
        }
        
        .info-divider { 
            width: 2px;
            background-color: #ddd; 
            margin: 0;
        }
        
        .info-table { 
            width: 100%; 
            border-collapse: collapse;
            font-size: 8pt;
        }
        
        .info-table td { 
            vertical-align: top; 
            padding: 3px 6px;
            border: none;
        }
        
        .info-table .label { 
            width: 40%; 
            font-weight: bold;
            color: #555;
        }
        
        .info-table .separator { 
            width: 15px;
            text-align: center;
            font-weight: bold;
        }
        
        .info-table .value {
            font-weight: normal;
            color: #000;
        }
        
        /* --- Verification Table --- */
        .verification-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
            margin: 0;
            table-layout: fixed;
        }
        
        .verification-table th,
        .verification-table td {
            border: 1px solid #333;
            padding: 3px 4px;
            text-align: left;
            vertical-align: middle;
            word-wrap: break-word;
            line-height: 1.3;
        }
        
        .verification-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
            font-size: 7pt;
            color: #000;
            padding: 4px;
        }
        
        /* Column widths for better layout */
        .verification-table th:nth-child(1) { width: 25%; } /* Item */
        .verification-table th:nth-child(2) { width: 30%; } /* Database */
        .verification-table th:nth-child(3) { width: 30%; } /* Real Lapangan */
        .verification-table th:nth-child(4) { width: 15%; } /* Sesuai */
        
        .verification-table td:nth-child(1) { 
            font-weight: 500;
            background-color: #fafafa;
            font-size: 7pt;
        }
        
        .verification-table td:nth-child(2) {
            font-family: Arial, sans-serif;
            font-size: 7pt;
            background-color: #fff;
        }
        
        .verification-table td:nth-child(3) {
            background-color: #fff;
            font-size: 7pt;
        }
        
        .verification-table td:nth-child(4) {
            text-align: center;
            background-color: #fafafa;
            font-size: 7pt;
        }
        
        .text-center { text-align: center; }
        
        .required {
            color: #dc3545;
            font-weight: bold;
        }
        
        .real-field {
            border-bottom: 1px solid #333;
            min-height: 16px;
            display: inline-block;
            width: 95%;
            padding: 2px 4px;
            margin: 0;
            font-size: 7pt;
        }
        
        .checkbox-symbol {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        
        /* --- Page Layout --- */
        .page {
            padding: 0;
            margin: 0 auto;
            background: white;
            min-height: 100vh;
        }
        
        main {
            margin-bottom: 10px;
        }
        
        /* --- Lain-lain --- */
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
                    content: "Halaman " counter(page) " | Packing List: <?= esc($packingList['packing_list_no'] ?? 'Unknown') ?>";
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
            
            .no-print {
                display: none !important;
            }
            
            /* Hide HTML footer in print - using @page @bottom instead */
            .print-footer {
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
            
            /* Allow content panels to break across pages if needed */
            .content-panel {
                page-break-inside: auto;
                page-break-after: auto;
                margin-bottom: 8px !important;
            }
            
            /* Prevent orphaned content */
            .verification-table tbody {
                orphans: 3;
                widows: 3;
            }
            
            /* Ensure content doesn't overlap with footer */
            .page {
                padding-bottom: 0 !important;
                margin-bottom: 0 !important;
            }
            
            body {
                margin-bottom: 0 !important;
                padding-bottom: 0 !important;
            }
            
            /* Add spacing to prevent content from going too close to footer area */
            .content-panel {
                margin-bottom: 6px !important;
            }
            
            .info-section {
                margin-bottom: 6px !important;
            }
            
            main {
                margin-bottom: 6px !important;
            }
            
            /* Ensure last section has enough space before footer */
            .content-panel:last-of-type {
                margin-bottom: 10px !important;
            }
            
            /* Reduce spacing in verification table */
            .verification-table td {
                padding: 2px 4px !important;
            }
            
            /* Ensure sections have proper spacing */
            .content-panel,
            .info-section {
                margin-bottom: 8px !important;
            }
        }
    </style>
</head>
<body>
    <div class="print-container">
        
        <header class="document-header">
            <div class="header-left">
                <img src="<?= base_url('assets/images/company-logo.svg') ?>" class="company-logo" alt="Logo" onerror="this.style.display='none'"/>
                <div class="company-info">
                    <div class="company-name">PT. SARANA MITRA LUAS Tbk</div>
                    <div class="company-tagline">FORKLIFT & MATERIAL HANDLING EQUIPMENT SOLUTIONS</div>
                    <div class="company-address">Jl. Kenari Utama II Blk. C No.03 & 05A, Cibatu, Kec. Cikarang Pusat, 17550</div>
                    <div class="company-phone">Phone: (021) - 3973 9988, (021) - 8990 2188</div>
                </div>
            </div>
            <div class="header-right">
                <table class="meta-table">
                    <tr>
                        <td>Packing List No</td>
                        <td><?= esc($packingList['packing_list_no'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td>PO Number</td>
                        <td><?= esc($delivery['no_po'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td><?= date('d M Y', strtotime($delivery['delivery_date'] ?? date('Y-m-d'))) ?></td>
                    </tr>
                </table>
            </div>
        </header>
        <h1 class="document-title">PACKING LIST</h1>
        
        <!-- Document Information -->
        <div class="info-section">
            <div class="section-title">INFORMASI PACKING LIST</div>
            <div class="info-grid">
                <div>
                    <table class="info-table">
                        <tr>
                            <td class="label">Packing List No:</td>
                            <td class="separator">:</td>
                            <td class="value"><?= esc($packingList['packing_list_no'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="label">PO Number:</td>
                            <td class="separator">:</td>
                            <td class="value"><?= esc($delivery['no_po'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Supplier:</td>
                            <td class="separator">:</td>
                            <td class="value"><?= esc($delivery['nama_supplier'] ?? '-') ?></td>
                        </tr>
                    </table>
                </div>
                <div class="info-divider"></div>
                <div>
                    <table class="info-table">
                        <tr>
                            <td class="label">Delivery Date:</td>
                            <td class="separator">:</td>
                            <td class="value"><?= date('d/m/Y', strtotime($delivery['delivery_date'] ?? date('Y-m-d'))) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Driver:</td>
                            <td class="separator">:</td>
                            <td class="value"><?= esc($delivery['driver_name'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Status:</td>
                            <td class="separator">:</td>
                            <td class="value"><?= esc($delivery['status'] ?? 'PENDING') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-start; align-items: center; margin-bottom: 4px;">
            <div style="font-size: 5pt; color: #666; text-align: left;">
                <span class="required">*</span> Field yang ditandai wajib diverifikasi
            </div>
        </div>

        <main>
            <?php if (!empty($items)): ?>
                <?php 
                // Display each item individually with #1, #2, etc. labels
                // Each item from $items is already an individual item with assigned SN
                foreach ($items as $itemIndex => $item):
                    // Normalize item_type (handle case sensitivity - database might use lowercase)
                    $itemTypeRaw = $item['item_type'] ?? 'Unit';
                    $itemType = ucfirst(strtolower($itemTypeRaw)); // Normalize to 'Unit', 'Attachment', etc.
                    $itemNumber = $itemIndex + 1;
                    
                    // Get SN directly from item array (same as tab Pengiriman)
                    $serialNumber = '';
                    if (isset($item['serial_number'])) {
                        $serialNumber = trim((string)$item['serial_number']);
                    }
                    
                    // Initialize specDetails as empty array first
                    $specDetails = [];
                    
                    // Get specification details based on item type - same format as Detail Purchase Order > Daftar Items
                    if (strtolower($itemType) === 'unit' || $itemType === 'Unit') {
                        // Format sesuai renderSpecificationDetails di purchasing.php
                        // SN already extracted above
                        $specDetails = [
                            'Departemen' => $item['nama_departemen'] ?? '-',
                            'Jenis Unit' => $item['jenis_unit'] ?? '-',
                            'Brand' => $item['merk_unit'] ?? '-',
                            'Model' => $item['model_unit'] ?? '-',
                            'Tahun' => $item['tahun_unit'] ?? '-',
                            'Kapasitas' => $item['kapasitas_unit'] ?? '-',
                            'Mast Type' => $item['tipe_mast'] ?? '-',
                            'Engine Type' => $item['merk_mesin'] ?? '-',
                            'Model Mesin' => $item['model_mesin'] ?? '-',
                            'Tire Type' => $item['tipe_ban'] ?? '-',
                            'Wheel Type' => $item['tipe_roda'] ?? '-',
                            'Valve' => $item['jumlah_valve'] ?? '-',
                            'Keterangan' => $item['keterangan'] ?? '-',
                            'Serial Number' => !empty($serialNumber) ? $serialNumber : 'Belum ada SN'
                        ];
                    } elseif (strtolower($itemType) === 'attachment' || $itemType === 'Attachment') {
                        // Format sesuai renderSpecificationDetails di purchasing.php
                        $specDetails = [
                            'Tipe Attachment' => $item['tipe_attachment'] ?? '-',
                            'Merk' => $item['merk_attachment'] ?? '-',
                            'Model' => $item['model_attachment'] ?? '-',
                            'Keterangan' => $item['keterangan'] ?? '-',
                            'SN' => !empty($serialNumber) ? $serialNumber : 'Belum ada SN'
                        ];
                    } elseif (strtolower($itemType) === 'battery' || $itemType === 'Battery') {
                        // Format sesuai renderSpecificationDetails di purchasing.php
                        $specDetails = [
                            'Jenis Battery' => $item['jenis_baterai'] ?? '-',
                            'Merk Battery' => $item['merk_baterai'] ?? '-',
                            'Tipe Battery' => $item['tipe_baterai'] ?? '-',
                            'Keterangan' => $item['keterangan'] ?? '-',
                            'SN' => !empty($serialNumber) ? $serialNumber : 'Belum ada SN'
                        ];
                    } elseif (strtolower($itemType) === 'charger' || $itemType === 'Charger') {
                        // Format sesuai renderSpecificationDetails di purchasing.php
                        $specDetails = [
                            'Merk Charger' => $item['merk_charger'] ?? '-',
                            'Tipe Charger' => $item['tipe_charger'] ?? '-',
                            'Keterangan' => $item['keterangan'] ?? '-',
                            'SN' => !empty($serialNumber) ? $serialNumber : 'Belum ada SN'
                        ];
                    } else {
                        // Fallback for unknown item types
                        $specDetails = [
                            'Item Name' => $item['item_name'] ?? '-',
                            'Item Type' => $itemType,
                            'Keterangan' => $item['keterangan'] ?? '-',
                            'SN' => !empty($serialNumber) ? $serialNumber : 'Belum ada SN'
                        ];
                    }
                    
                    // Ensure specDetails is always an array
                    if (empty($specDetails) || !is_array($specDetails)) {
                        $specDetails = [
                            'Item' => $item['item_name'] ?? 'Unknown Item',
                            'SN' => !empty($serialNumber) ? $serialNumber : 'Belum ada SN'
                        ];
                    }
                    
                    // Final safety check - ensure specDetails exists and is array
                    if (!isset($specDetails) || !is_array($specDetails)) {
                        $specDetails = [
                            'Item Name' => $item['item_name'] ?? 'Unknown Item',
                            'Item Type' => $itemType,
                            'SN' => !empty($serialNumber) ? $serialNumber : 'Belum ada SN'
                        ];
                    }
                ?>
                <div class="content-panel">
                    <div class="panel-title"><?= strtoupper($itemType) ?> #<?= $itemNumber ?></div>
                    <div class="panel-body">
                        <table class="verification-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Database</th>
                                    <th>Real Lapangan</th>
                                    <th>Sesuai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($specDetails) && is_array($specDetails)): ?>
                                <?php foreach ($specDetails as $label => $value): ?>
                                    <?php
                                    // Skip empty values but show "Belum ada SN" for serial numbers
                                    if (empty($value) || ($value === '-' && strpos($label, 'SN') === false && $label !== 'Keterangan')) continue;
                                    
                                    // Determine if required field (SN fields)
                                    $isRequired = in_array($label, ['Serial Number', 'SN']);
                                    ?>
                                    <tr>
                                        <td><?= esc($label) ?><?= $isRequired ? ' <span class="required">*</span>' : '' ?></td>
                                        <td><?= esc($value) ?></td>
                                        <td><span class="real-field"></span></td>
                                        <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; color: #999; font-style: italic;">
                                            Tidak ada data spesifikasi untuk item ini
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endforeach; ?>
                <br><br>
                <!-- Simple Signature Section - same as print_verification.php -->
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px; margin-top: 15px; margin-bottom: 10px; font-size: 8pt; page-break-inside: avoid;">
                    <div style="text-align: center;">
                        <div style="font-size: 7pt; margin-bottom: 4px;">Tgl: ___/___/_____</div>
                        <div style="font-weight: bold; margin-bottom: 20px;">Diverifikasi Oleh</div><br>
                        <div style="border-bottom: 1px solid #000; width: 150px; margin: 0 auto 4px; height: 20px;"></div>
                        <div>Verifikator</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 7pt; margin-bottom: 4px;">Tgl: ___/___/_____</div>
                        <div style="font-weight: bold; margin-bottom: 20px;">Disahkan Oleh</div><br>
                        <div style="border-bottom: 1px solid #000; width: 150px; margin: 0 auto 4px; height: 20px;"></div>
                        <div>Supervisor</div>
                    </div>
                </div>
            <?php else: ?>
                <div class="content-panel">
                    <div class="panel-title">VERIFIKASI DATA</div>
                    <div class="panel-body">
                        <p style="text-align: center; color: #666; font-style: italic;">Tidak ada item dalam packing list ini</p>
                        <p style="text-align: center; color: #999; font-size: 7pt; margin-top: 5px;">
                            Debug: Items count = <?= count($items ?? []) ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </main>
        
    </div>
    
    <script>
        // Auto print functionality
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
            }, 1000);
        }
        
        // Initialize
        if (document.readyState === 'complete') {
            initiatePrint();
        } else {
            window.addEventListener('load', function() {
                initiatePrint();
            });
        }
        
        // Close window after print
        window.addEventListener('afterprint', () => {
            setTimeout(function() {
                window.close();
            }, 100);
        });
    </script>
</body>
</html>
