<?php
$di = $di ?? [];
$spk = $spk ?? [];
$s = $spesifikasi ?? [];
$items = $items ?? [];
$unit_items = $unit_items ?? []; // Array of units for multiple DI pages
$k = $k ?? []; // Kontrak spesifikasi data - added to prevent undefined variable errors
$trips = $trips ?? []; // Delivery trips
$status = strtoupper((string)($di['status'] ?? ''));
$placeholder = ($status === 'SUBMITTED' || $status === 'DIAJUKAN');

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

// If no unit_items array provided, create from single unit_item or items
if (empty($unit_items)) {
    $unit_item = $unit_item ?? null;
    if (!$unit_item && !empty($items)) {
        foreach ($items as $item) {
            if ($item['item_type'] === 'UNIT') {
                $unit_item = $item;
                break;
            }
        }
    }
    if ($unit_item) {
        $unit_items = [$unit_item];
    }
}

// If still no units, create empty placeholder
if (empty($unit_items)) {
    $unit_items = [null]; // At least one page
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($di['nomor_di'] ?? 'DI') ?></title>
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
            
            .page-break {
                page-break-before: always;
            }
            
            .no-print {
                display: none !important;
            }
            
            /* Print footer */
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
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
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
            page-break-inside: avoid;
        }
        
        /* Header - Simplified for single page */
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
        
        /* Content Sections - Simplified for single page */
        .info-section {
            margin-bottom: 15px;
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
        
        /* Unit Details - Simplified for single page */
        .unit-section {
            border: 1px solid #000;
            padding: 8px;
            margin: 15px 0;
        }
        
        .unit-header {
            background: #e8f4fd;
            border: 1px solid #0ea5e9;
            padding: 4px;
            margin: -8px -8px 8px -8px;
            font-weight: bold;
            text-align: center;
            font-size: 12px;
        }
        
        /* Approval Section - Simplified for single page */
        .approval-section {
            margin-top: 15px;
            page-break-inside: avoid;
        }
        
        .approval-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
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
        
        /* Notes - Simplified for single page */
        .notes-section {
            margin-top: 15px;
            border: 1px solid #000;
            padding: 6px;
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
        
        /* SPK-style table classes */
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
        .table td {
            min-height: 25px;
        }
        .label { color:#374151; }
        .val   { color:#111827; font-weight: 600; }
        .grid-2 td { width: 25%; }
        
        /* Footer - Simplified for single page */
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

<?php foreach ($unit_items as $unit_index => $current_unit): ?>
    <?php if ($unit_index > 0): ?><div class="page-break"></div><?php endif; ?>
    
    <div class="page">
        <!-- Document Header -->
        <div class="document-header">
            <img src="<?= base_url('assets/images/company-logo.svg') ?>" class="logo" alt="Company Logo" style="width: 160px; height: auto; margin-right: 50px;"/>
            <div class="document-title">
                <h1>PT. SARANA MITRA LUAS</h1>
                <h2>DELIVERY INSTRUCTION</h2>
            </div>
            <div class="document-meta">
                <div class="doc-number"><strong>No: <?= esc($di['nomor_di'] ?? '-') ?></strong></div>
                <div class="doc-number">SPK: <?= esc($spk['nomor_spk'] ?? $spk['no_spk'] ?? '-') ?></div>
                <div class="doc-date">Tanggal: <?= date('d F Y') ?></div>
            </div>
        </div>

        <!-- Document Information -->
        <div class="info-section">
            <div class="section-title">INFORMASI DOKUMEN & PELANGGAN</div>
            <div class="info-grid">
                <div>
                    <div class="info-item">
                        <?php 
                            // Show quotation number or contract indicator
                            $hasContract = !empty($di['contract_id']) || !empty($spk['kontrak_id']) || !empty($di['spk_kontrak_id']);
                            if ($hasContract) {
                                $infoLabel = 'Source:';
                                $infoValue = 'Contract-based';
                            } else {
                                // Show actual quotation number
                                $quotationNumber = $di['quotation_number'] ?? $spk['quotation_number'] ?? '-';
                                $infoLabel = 'No Quotation:';
                                $infoValue = $quotationNumber;
                            }
                        ?>
                        <span class="info-label"><?= $infoLabel ?></span>
                        <span class="info-value"><?= $infoValue ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Nama Perusahaan:</span>
                        <span class="info-value"><?= esc($di['pelanggan'] ?? $spk['pelanggan'] ?? $spk['customer_name'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Alamat Pengiriman:</span>
                        <span class="info-value"><?= esc($di['lokasi'] ?? $spk['lokasi'] ?? '-') ?></span>
                    </div>
                </div>
                <div>
                    <div class="info-item">
                        <span class="info-label">Tanggal Kirim:</span>
                        <span class="info-value"><?= esc($di['tanggal_kirim'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">PIC:</span>
                        <span class="info-value"><?= esc($spk['pic'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Contact Person:</span>
                        <span class="info-value"><?= esc($spk['kontak'] ?? '-') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transportation Information -->
        <div class="info-section">
            <div class="section-title">INFORMASI TRANSPORTASI</div>
            <?php
            $haTrips = !empty($trips) && count($trips) > 0;
            if ($haTrips): ?>
                <table style="width:100%; border-collapse:collapse; font-size:9pt; margin-top:4px;">
                    <thead>
                        <tr style="background:#f0f0f0;">
                            <th style="border:1px solid #ccc; padding:4px 6px; text-align:left;">Trip</th>
                            <th style="border:1px solid #ccc; padding:4px 6px; text-align:left;">Tipe Kendaraan</th>
                            <th style="border:1px solid #ccc; padding:4px 6px; text-align:left;">Kendaraan</th>
                            <th style="border:1px solid #ccc; padding:4px 6px; text-align:left;">No. Polisi</th>
                            <th style="border:1px solid #ccc; padding:4px 6px; text-align:left;">Supir</th>
                            <th style="border:1px solid #ccc; padding:4px 6px; text-align:left;">No. HP</th>
                            <th style="border:1px solid #ccc; padding:4px 6px; text-align:left;">Tgl Kirim</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trips as $trip): ?>
                        <tr>
                            <td style="border:1px solid #ccc; padding:4px 6px;"><?= esc($trip['nomor_trip'] ?? '-') ?></td>
                            <td style="border:1px solid #ccc; padding:4px 6px;"><?= esc($trip['tipe_kendaraan'] ?? '-') ?></td>
                            <td style="border:1px solid #ccc; padding:4px 6px;"><?= esc($trip['kendaraan'] ?? '-') ?></td>
                            <td style="border:1px solid #ccc; padding:4px 6px;"><?= esc($trip['no_polisi'] ?? '-') ?></td>
                            <td style="border:1px solid #ccc; padding:4px 6px;"><?= esc($trip['nama_supir'] ?? '-') ?></td>
                            <td style="border:1px solid #ccc; padding:4px 6px;"><?= esc($trip['no_hp_supir'] ?? '-') ?></td>
                            <td style="border:1px solid #ccc; padding:4px 6px;"><?= $trip['tanggal_kirim'] ? date('d/m/Y', strtotime($trip['tanggal_kirim'])) : '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
            <div class="info-grid">
                <div>
                    <div class="info-item">
                        <span class="info-label">Jenis Kendaraan:</span>
                        <span class="info-value <?= $placeholder && empty($di['kendaraan']) ? 'placeholder' : '' ?>">
                            <?= esc($di['kendaraan'] ?? ($placeholder ? '[Akan diisi saat pengiriman]' : '-')) ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Nama Supir:</span>
                        <span class="info-value <?= $placeholder && empty($di['nama_supir']) ? 'placeholder' : '' ?>">
                            <?= esc($di['nama_supir'] ?? ($placeholder ? '[Akan diisi saat pengiriman]' : '-')) ?>
                        </span>
                    </div>
                </div>
                <div>
                    <div class="info-item">
                        <span class="info-label">No. Polisi:</span>
                        <span class="info-value <?= $placeholder && empty($di['no_polisi_kendaraan']) ? 'placeholder' : '' ?>">
                            <?= esc($di['no_polisi_kendaraan'] ?? ($placeholder ? '[Akan diisi saat pengiriman]' : '-')) ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">No. HP Supir:</span>
                        <span class="info-value <?= $placeholder && empty($di['no_hp_supir']) ? 'placeholder' : '' ?>">
                            <?= esc($di['no_hp_supir'] ?? ($placeholder ? '[Akan diisi saat pengiriman]' : '-')) ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Unit Details -->
        <div class="unit-section">
            <div class="unit-header">
                DETAIL UNIT YANG DIKIRIM
                <?php if ($current_unit): ?>
                    - <?= esc($current_unit['no_unit'] ?? 'Unit') ?> (<?= esc($current_unit['merk_unit'] ?? '') ?> <?= esc($current_unit['model_unit'] ?? '') ?>)
                <?php endif; ?>
            </div>

            <!-- Unit Details Table (exact same format as SPK) -->
            <?php
                // Get prepared units data from SPK (same as print_spk.php)
                $preparedList = [];
                if (isset($spk['prepared_units_detail']) && is_array($spk['prepared_units_detail'])) {
                    $preparedList = $spk['prepared_units_detail'];
                }
                
                // Match prepared unit to the current delivery item by unit_id
                $rowPrepared = null;
                if (!empty($preparedList) && !empty($current_unit['unit_id'])) {
                    foreach ($preparedList as $pu) {
                        if ((int)($pu['unit_id'] ?? 0) === (int)$current_unit['unit_id']) {
                            $rowPrepared = $pu;
                            break;
                        }
                    }
                }
                // Fallback: use loop index (0-based) to match prepared unit position
                if (!$rowPrepared) {
                    $rowPrepared = $preparedList[$unit_index] ?? ($preparedList[0] ?? null);
                }
                
                // Build left/right summaries exactly like SPK
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
                                // Prefer per-row accessories if provided; fallback to global SPK/spec (same as SPK)
                                $aksText = '';
                                $aksRaw = $rowPrepared['aksesoris'] ?? '';
                                if (!empty($aksRaw)) {
                                    if (is_array($aksRaw)) {
                                        $aksList = $aksRaw;
                                    } else {
                                        $decoded = json_decode((string)$aksRaw, true);
                                        $aksList = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : array_filter(array_map('trim', explode(',', (string)$aksRaw)));
                                    }
                                    // Convert snake_case keys to readable label
                                    $aksLabels = array_map(function($k) {
                                        return strtoupper(str_replace(['_', '-'], ' ', $k));
                                    }, $aksList);
                                    $aksText = implode(', ', $aksLabels);
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

        <!-- Special Notes -->
        <?php if (!empty($di['catatan']) || !empty($spk['catatan_khusus'])): ?>
            <div class="notes-section">
                <div class="notes-title">CATATAN KHUSUS PENGIRIMAN</div>
                <div><?= nl2br(esc($di['catatan'] ?? $spk['catatan_khusus'] ?? '')) ?></div>
            </div>
        <?php endif; ?>

        <!-- Approval Section -->
        <div class="approval-section">
            <div class="section-title">PERSETUJUAN & TANDA TANGAN</div>
            
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-top: 15px;">
                <div style="text-align: center;">
                    <div style="font-weight: bold; margin-bottom: 5px;">MARKETING</div>
                    <div style="font-size: 10px; color: #666; margin-bottom: 10px;">Pembuat DI</div>
                    <?php 
                        $marketingName = resolvePrintSignerName(
                            $spk,
                            ['created_by_name', 'created_by_full_name', 'marketing_name', 'dibuat_oleh_name'],
                            ''
                        );
                        if (!empty($marketingName)): 
                    ?>
                        <div style="color: #059669; border: 2px solid #059669; padding: 3px 8px; font-size: 10px; font-weight: bold; display: inline-block; transform: rotate(-15deg); margin: 10px 0;">APPROVED</div>
                        <br/>
                        <div style="font-size: 10px; margin-top: 5px;">(<?= esc($marketingName) ?>)</div>
                    <?php else: ?>
                        <br/><br/>
                        <div style="border-bottom: 1px solid #000; margin: 10px 20px 5px 20px;"></div>
                        <div style="font-size: 9px; color: #666;">(...........................)</div>
                    <?php endif; ?>
                    <div style="font-size: 9px; color: #666; margin-top: 5px;">
                        <?php if (!empty($di['dibuat_pada'])): ?>
                            Tanggal: <?= date('d/m/Y', strtotime($di['dibuat_pada'])) ?>
                        <?php elseif (!empty($di['created_at'])): ?>
                            Tanggal: <?= date('d/m/Y', strtotime($di['created_at'])) ?>
                        <?php else: ?>
                            Tanggal: __________
                        <?php endif; ?>
                    </div>
                </div>

                <div style="text-align: center;">
                    <div style="font-weight: bold; margin-bottom: 5px;">BAG. DELIVERY</div>
                    <div style="font-size: 10px; color: #666; margin-bottom: 10px;">Persiapan Pengiriman</div>
                    <?php if (!empty($di['perencanaan_tanggal_approve'])): ?>
                        <div style="color: #059669; border: 2px solid #059669; padding: 3px 8px; font-size: 10px; font-weight: bold; display: inline-block; transform: rotate(-15deg); margin: 10px 0;">APPROVED</div>
                        <br/>
                        <div style="font-size: 10px; margin-top: 5px;">(<?= esc($di['perencanaan_approved_by'] ?? 'Delivery Team') ?>)</div>
                    <?php else: ?>
                        <br/><br/>
                        <div style="border-bottom: 1px solid #000; margin: 10px 20px 5px 20px;"></div>
                        <div style="font-size: 9px; color: #666;">(...........................)</div>
                    <?php endif; ?>
                    <div style="font-size: 9px; color: #666; margin-top: 5px;">
                        <?php if (!empty($di['perencanaan_tanggal_approve'])): ?>
                            Tanggal: <?= date('d/m/Y', strtotime($di['perencanaan_tanggal_approve'])) ?>
                        <?php else: ?>
                            Tanggal: __________
                        <?php endif; ?>
                    </div>
                </div>

                <div style="text-align: center;">
                    <div style="font-weight: bold; margin-bottom: 5px;">BERANGKAT</div>
                    <div style="font-size: 10px; color: #666; margin-bottom: 10px;">Keberangkatan</div>
                    <?php if (!empty($di['berangkat_tanggal_approve'])): ?>
                        <div style="color: #059669; border: 2px solid #059669; padding: 3px 8px; font-size: 10px; font-weight: bold; display: inline-block; transform: rotate(-15deg); margin: 10px 0;">APPROVED</div>
                        <br/>
                        <div style="font-size: 10px; margin-top: 5px;">(Delivery Team)</div>
                    <?php else: ?>
                        <br/><br/>
                        <div style="border-bottom: 1px solid #000; margin: 10px 20px 5px 20px;"></div>
                        <div style="font-size: 9px; color: #666;">(...........................)</div>
                    <?php endif; ?>
                    <div style="font-size: 9px; color: #666; margin-top: 5px;">
                        <?php if (!empty($di['berangkat_tanggal_approve'])): ?>
                            Tanggal: <?= date('d/m/Y', strtotime($di['berangkat_tanggal_approve'])) ?>
                        <?php else: ?>
                            Tanggal: __________
                        <?php endif; ?>
                    </div>
                </div>

                <div style="text-align: center;">
                    <div style="font-weight: bold; margin-bottom: 5px;">PENERIMA</div>
                    <div style="font-size: 10px; color: #666; margin-bottom: 10px;">Customer</div>
                    <?php if (!empty($di['sampai_tanggal_approve'])): ?>
                        <div style="color: #dc3545; border: 2px solid #dc3545; padding: 3px 8px; font-size: 10px; font-weight: bold; display: inline-block; transform: rotate(-15deg); margin: 10px 0;">RECEIVED</div>
                        <br/>
                        <div style="font-size: 10px; margin-top: 5px;">(<?= esc($di['sampai_penerima_nama'] ?? 'Customer') ?>)</div>
                    <?php else: ?>
                        <br/><br/>
                        <div style="border-bottom: 1px solid #000; margin: 10px 20px 5px 20px;"></div>
                        <div style="font-size: 9px; color: #666;">(...........................)</div>
                    <?php endif; ?>
                    <div style="font-size: 9px; color: #666; margin-top: 5px;">
                        <?php if (!empty($di['sampai_tanggal_approve'])): ?>
                            Tanggal: <?= date('d/m/Y', strtotime($di['sampai_tanggal_approve'])) ?>
                        <?php else: ?>
                            Tanggal: __________
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

<?php endforeach; ?>

<!-- Print Footer (consistent with SPK) -->
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
            <span style="color: #888;">DI No: <?= esc($di['nomor_di'] ?? 'Unknown') ?></span>
        </div>
    </div>
</div>

<script>
// Show footer when printing
window.addEventListener('beforeprint', () => {
    const footer = document.getElementById('printFooter');
    if (footer) footer.style.display = 'block';
});

window.addEventListener('afterprint', () => {
    const footer = document.getElementById('printFooter');
    if (footer) footer.style.display = 'none';
});

// Auto print on load
window.addEventListener('load', () => {
    const diNumber = '<?= str_replace('/', '-', esc($di['nomor_di'] ?? 'Unknown')) ?>';
    document.title = 'DI-' + diNumber;
    const footer = document.getElementById('printFooter');
    if (footer) footer.style.display = 'block';
});
</script>

</body>
</html>
