<?php
/**
 * Customer PDF Report V2 - Professional Redesign
 * A modern, compact, and detailed PDF report template.
 */

// Data from controller
$customer  = $customerData ?? [];
$contracts = $contractsData ?? [];
$locations = $locationsData ?? [];
$units     = $unitsData ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Report - <?= htmlspecialchars($customer['customer_name'] ?? 'N/A') ?></title>
    <style>
        :root {
            --primary-color: #1e40af; /* Professional blue */
            --primary-light: #dbeafe;
            --secondary-color: #059669; /* Green accent */
            --accent-color: #dc2626; /* Red for status */
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --border-light: #e5e7eb;
            --border-medium: #d1d5db;
            --bg-light: #f9fafb;
            --bg-white: #ffffff;
        }

        @page { 
            size: A4; 
            margin: 10mm 8mm 15mm 8mm;
        }
        
        @media print {
            @page {
                margin: 10mm 8mm 15mm 8mm;
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
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #222;
            background-color: #FFF;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* --- HEADER LAYOUT (Based on print_spk.php) --- */
        .header { 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            padding: 10px 0;
            border-bottom: 2px solid #333;
            margin-bottom: 15px;
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
        
        .company-logo {
            max-height: 60px;
            margin-right: 15px;
        }
        
        .title { 
            font-size: 16px; 
            font-weight: bold; 
            margin: 0; 
            color: #000;
        }
        
        .subtitle { 
            font-size: 15px; 
            color: #555; 
            margin: 0; 
        }
        
        .document-info {
            font-size: 10px;
            color: #333;
            margin-bottom: 5px;
        }
        
        .doc-number, .doc-date {
            margin: 1px 0;
            font-weight: 500;
        }
        
        .header-separator {
            border: none;
            border-top: 1px solid #333;
            margin: 5px 0 15px 0;
        }
        
        /* --- MAIN CONTENT (Based on print_spk.php) --- */
        .main-content {
            margin: 0;
        }

        .section {
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
        
        /* --- CUSTOMER INFO (Based on print_spk.php) --- */
        .info-card {
            margin-bottom: 15px;
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
            color: #374151;
        }
        
        .info-value { 
            flex: 1;
            border-bottom: 1px dotted #ccc;
            min-height: 16px;
            padding-left: 4px;
            font-size: 11px;
            color: #111827;
            font-weight: 600;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .status-active { 
            background: #d4edda;
            color: #155724;
        }
        
        .status-inactive { 
            background: #f8d7da;
            color: #721c24;
        }

        /* --- DATA TABLE (Based on print_spk.php) --- */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        
        .data-table th, .data-table td { 
            border: 1px solid #9aa1a7; 
            padding: .4rem .5rem; 
            vertical-align: top; 
            line-height: 1.3;
        }
        
        .data-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        
        .data-table td {
            min-height: 25px;
        }
        
        .label { color: #374151; }
        .val { color: #111827; font-weight: 600; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }

        /* --- SUMMARY GRID (Based on print_spk.php) --- */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-top: 10px;
        }
        
        .summary-card {
            background: #f8f9fa;
            border: 1px solid #9aa1a7;
            padding: 8px;
            text-align: center;
        }
        
        .summary-value {
            display: block;
            font-size: 12px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 2px;
        }
        
        .summary-label {
            font-size: 9px;
            color: #374151;
        }

        /* --- CONTRACT SECTIONS --- */
        .contract-section {
            margin-bottom: 20px;
            border: 1px solid #9aa1a7;
            border-radius: 4px;
            overflow: hidden;
        }

        .contract-header {
            background: #f8f9fa;
            padding: 10px;
            border-bottom: 1px solid #9aa1a7;
        }

        .contract-header h4 {
            margin: 0 0 5px 0;
            font-size: 12px;
            color: #111827;
            font-weight: bold;
        }

        .contract-info {
            display: flex;
            gap: 15px;
            font-size: 9px;
            color: #6b7280;
        }

        .contract-info span {
            display: flex;
            align-items: center;
        }

        .units-subsection {
            padding: 10px;
        }

        .units-subsection h5 {
            margin: 0 0 8px 0;
            font-size: 10px;
            color: #374151;
            font-weight: bold;
        }

        .no-units {
            padding: 10px;
            text-align: center;
            color: #6b7280;
            font-style: italic;
        }

        /* --- FOOTER --- */
        .print-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #f8f9fa;
            border-top: 1px solid #9aa1a7;
            padding: 8px 15px;
            font-size: 8px;
            color: #6b7280;
            z-index: 1000;
            display: none;
        }

        .print-footer .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .print-footer .footer-left {
            text-align: left;
        }

        .print-footer .footer-center {
            text-align: center;
        }

        .print-footer .footer-right {
            text-align: right;
        }

        .print-footer .company-name {
            font-weight: bold;
            color: #111827;
        }

        .print-footer .system-info {
            color: #888;
        }

        .print-footer .print-date {
            font-weight: bold;
        }

        .print-footer .page-info {
            font-weight: bold;
        }

        .print-footer .document-info {
            color: #888;
        }

        @media print {
            .print-footer {
                display: block !important;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: #f8f9fa;
                border-top: 1px solid #9aa1a7;
                padding: 8px 15px;
                font-size: 8px;
                color: #6b7280;
                z-index: 1000;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-left">
            <img src="<?= base_url('assets/images/company-logo.svg') ?>" class="company-logo" alt="logo"/>
        </div>
        <div class="header-center">
            <div class="title">PT SARANA MITRA LUAS Tbk</div>
            <div class="subtitle">CUSTOMER DETAIL REPORT</div>
        </div>
        <div class="header-right">
            <div class="document-info">
                <div class="doc-number">Customer: <?= htmlspecialchars($customer['customer_code'] ?? 'N/A') ?></div>
                <div class="doc-date">Generated: <?= date('d F Y, H:i') ?></div>
            </div>
        </div>
    </div>
    

    <div class="main-content">
        
        <!-- Customer Information (Based on print_spk.php format) -->
        <div class="info-card">
            <div class="section-title">CUSTOMER INFORMATION</div>
            <div class="info-grid">
                <div>
                    <div class="info-item">
                        <span class="info-label">Customer Name:</span>
                        <span class="info-value"><?= htmlspecialchars($customer['customer_name'] ?? 'N/A') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Customer Code:</span>
                        <span class="info-value"><?= htmlspecialchars($customer['customer_code'] ?? 'N/A') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Area:</span>
                        <span class="info-value"><?= htmlspecialchars($customer['area_name'] ?? 'N/A') ?></span>
                    </div>
                </div>
                <div>
                    <div class="info-item">
                        <span class="info-label">Status:</span>
                        <span class="info-value">
                            <span class="status-badge <?= ($customer['is_active'] ?? 0) ? 'status-active' : 'status-inactive' ?>">
                                <?= ($customer['is_active'] ?? 0) ? 'Active' : 'Inactive' ?>
                            </span>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Member Since:</span>
                        <span class="info-value"><?= isset($customer['created_at']) ? date('d M Y', strtotime($customer['created_at'])) : 'N/A' ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Total Contracts:</span>
                        <span class="info-value"><?= count($contracts) ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (!empty($contracts)): ?>
        <div class="section">
            <div class="section-title">Contracts (<?= count($contracts) ?>)</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Contract No.</th>
                        <th>Location</th>
                        <th>Period</th>
                        <th class="text-center">Duration</th>
                        <th class="text-center">Units</th>
                        <th class="text-right">Value (IDR)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contracts as $contract): ?>
                    <tr>
                        <td><?= htmlspecialchars($contract['no_kontrak'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($contract['location_name'] ?? 'N/A') ?></td>
                        <td><?= date('d/m/y', strtotime($contract['tanggal_mulai'])) ?> - <?= date('d/m/y', strtotime($contract['tanggal_berakhir'])) ?></td>
                        <td class="text-center"><?= (new DateTime($contract['tanggal_berakhir']))->diff(new DateTime($contract['tanggal_mulai']))->days ?> days</td>
                        <td class="text-center"><?= htmlspecialchars($contract['total_units'] ?? '0') ?></td>
                        <td class="text-right"><?= number_format($contract['nilai_total'] ?? 0, 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($contract['status'] ?? 'N/A') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <?php if (!empty($locations)): ?>
        <div class="section">
            <div class="section-title">Customer Locations (<?= count($locations) ?>)</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:25%;">Location Name</th>
                        <th style="width:15%;">Type</th>
                        <th style="width:35%;">Address</th>
                        <th>Contact Person / Phone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($locations as $location): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($location['location_name'] ?? 'N/A') ?>
                            <?= ($location['is_primary'] ?? 0) ? ' <strong style="color:var(--primary-color);">(Primary)</strong>' : '' ?>
                        </td>
                        <td><?= htmlspecialchars(str_replace('_', ' ', $location['location_type'] ?? 'N/A')) ?></td>
                        <td><?= htmlspecialchars($location['address'] . ', ' . $location['city']) ?></td>
                        <td>
                            <?= htmlspecialchars($location['contact_person'] ?? 'N/A') ?>
                            <br><small style="color:var(--text-color-light);"><?= htmlspecialchars($location['phone'] ?? '-') ?></small>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        

        <?php if (!empty($contracts)): ?>
        <div class="section">
            <div class="section-title">Contract Details & Rented Units</div>
            
            <?php foreach ($contracts as $contract): ?>
            <div class="contract-section">
                <div class="contract-header">
                    <h4><?= htmlspecialchars($contract['no_kontrak'] ?? 'N/A') ?> - <?= htmlspecialchars($contract['location_name'] ?? 'N/A') ?></h4>
                    <div class="contract-info">
                        <span><strong>Period:</strong> <?= isset($contract['tanggal_mulai']) ? date('d M Y', strtotime($contract['tanggal_mulai'])) : 'N/A' ?> - <?= isset($contract['tanggal_selesai']) ? date('d M Y', strtotime($contract['tanggal_selesai'])) : 'N/A' ?></span>
                        <span><strong>Value:</strong> IDR <?= number_format($contract['nilai_total'] ?? 0, 0, ',', '.') ?></span>
                        <span><strong>Status:</strong> <?= htmlspecialchars($contract['status'] ?? 'N/A') ?></span>
                    </div>
                </div>
                
                <?php 
                // Get units for this contract
                $contractUnits = array_filter($units, function($unit) use ($contract) {
                    return ($unit['no_kontrak'] ?? '') === ($contract['no_kontrak'] ?? '');
                });
                ?>
                
                <?php if (!empty($contractUnits)): ?>
                <div class="units-subsection">
                    <h5>Units (<?= count($contractUnits) ?>)</h5>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Unit No.</th>
                                <th>Serial Number</th>
                                <th>Type / Model</th>
                                <th>Departemen</th>
                                <th>Kapasitas</th>
                                <th>Mast</th>
                                <th>Attachment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contractUnits as $unit): ?>
                            <tr>
                                <td><?= htmlspecialchars($unit['no_unit'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($unit['serial_number'] ?? 'N/A') ?></td>
                                <td>
                                    <?php 
                                        $typeModel = [];
                                        if (!empty($unit['merk_unit'])) $typeModel[] = $unit['merk_unit'];
                                        if (!empty($unit['model_unit'])) $typeModel[] = $unit['model_unit'];
                                        if (!empty($unit['jenis_unit'])) $typeModel[] = $unit['jenis_unit'];
                                        echo htmlspecialchars(implode(' ', $typeModel) ?: 'N/A');
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($unit['nama_departemen'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($unit['kapasitas_unit'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($unit['tipe_mast'] ?? 'N/A') ?></td>
                                <td>
                                    <?php 
                                        $attachment = [];
                                        if (!empty($unit['tipe_roda'])) $attachment[] = 'Roda: ' . $unit['tipe_roda'];
                                        if (!empty($unit['tipe_ban'])) $attachment[] = 'Ban: ' . $unit['tipe_ban'];
                                        if (!empty($unit['jumlah_valve'])) $attachment[] = 'Valve: ' . $unit['jumlah_valve'];
                                        echo htmlspecialchars(implode(', ', $attachment) ?: 'N/A');
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="no-units">
                    <p><em>No units found for this contract.</em></p>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="section">
            <div class="section-title">Executive Summary</div>
            <div class="summary-grid">
                <div class="summary-card">
                    <span class="summary-value"><?= count($locations) ?></span>
                    <span class="summary-label">Total Locations</span>
                </div>
                <div class="summary-card">
                    <span class="summary-value"><?= count(array_filter($contracts, fn($c) => $c['status'] == 'Aktif')) ?></span>
                    <span class="summary-label">Active Contracts</span>
                </div>
                <div class="summary-card">
                    <span class="summary-value"><?= count($units) ?></span>
                    <span class="summary-label">Total Units Rented</span>
                </div>
                <div class="summary-card">
                    <span class="summary-value">IDR <?= number_format(array_sum(array_column($contracts, 'nilai_total')), 0, ',', '.') ?></span>
                    <span class="summary-label">Total Contract Value</span>
                </div>
            </div>
        </div>
    </div>

<script>
// Auto print when page loads
window.addEventListener('load', function() {
    // Set document title for download filename
    const customerName = '<?= $customer['customer_name'] ?? 'Unknown' ?>';
    const fileName = 'Customer_Report_' + customerName + '_' + new Date().toISOString().split('T')[0];
    document.title = fileName;
    
    // Auto print after a short delay
    setTimeout(function() {
        window.print();
    }, 1000);
});

// Handle after print
window.addEventListener('afterprint', function() {
    setTimeout(function() {
        window.close();
    }, 500);
});

// Manual print button support
window.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
        e.preventDefault();
        window.print();
    }
});

// Show footer on print
window.addEventListener('beforeprint', function() {
    document.getElementById('printFooter').style.display = 'block';
});

window.addEventListener('afterprint', function() {
    document.getElementById('printFooter').style.display = 'none';
});

</script>

<!-- Print Footer -->
<div class="print-footer" id="printFooter" style="display: none;">
    <div class="footer-content">
        <div class="footer-left">
            <div class="company-name">PT SARANA MITRA LUAS Tbk</div>
            <div class="system-info">Sistem OPTIMA - Document Management</div>
        </div>
        <div class="footer-center">
            <div class="print-date">Tanggal Cetak: <?= date('d/m/Y H:i') ?></div>
            <div class="system-info">Dokumen ini dibuat secara otomatis oleh sistem OPTIMA</div>
        </div>
        <div class="footer-right">
            <div class="page-info">Halaman <span id="currentPage">1</span></div>
            <div class="document-info">Customer: <?= htmlspecialchars($customer['customer_code'] ?? 'Unknown') ?></div>
        </div>
    </div>
</div>

</body>
</html>