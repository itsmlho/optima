<?php
$quotation = $quotation ?? [];
$specs = $specifications ?? [];
$status = strtoupper((string)($quotation['stage'] ?? ''));
helper('accessory');
helper('optima_spec_print');

// Helper untuk format nama user agar tidak menampilkan username
function resolvePrintPersonName(array $data, array $keys, string $fallback): string {
    foreach ($keys as $key) {
        $value = trim((string)($data[$key] ?? ''));
        if ($value !== '') {
            return $value;
        }
    }
    return $fallback;
}

// Helper untuk format mata uang
function formatCurrency($amount, $currency = 'IDR') {
    return $currency . ' ' . number_format($amount, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= 'QUOTATION-' . str_replace('/', '-', esc($quotation['quotation_number'] ?? 'Unknown')) ?></title>
    
    <meta name="robots" content="noindex, nofollow">
    <meta name="format-detection" content="telephone=no">

    <style>
        /* --- GLOBAL VARIABLES --- */
        :root {
            --brand-blue: #1a4f9c; /* Biru Korporat */
            --text-black: #222;
            --border-gray: #ccc;
            --bg-zebra: #f9fafb;
        }

        @page { 
            size: A4 portrait;
            margin: 0;
        }

        * { box-sizing: border-box; }

        html {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body { 
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt; 
            color: var(--text-black); 
            line-height: 1.3;
            margin: 0;
            padding: 10mm;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* --- HEADER (Logo Left, Text Center) --- */
        .header {
            position: relative;
            background: white;
            padding: 8px 0;
            height: 80px;
            border-bottom: 2px solid var(--brand-blue);
            margin-bottom: 15px;
            flex-shrink: 0;
        }

        .logo-wrapper {
            position: absolute;
            top: 5px;
            left: 0;
        }

        .logo-wrapper img {
            height: 65px;
            width: auto;
        }

        .company-text {
            padding: 8px 80px 2px 80px;
            text-align: center;
        }

        .company-name {
            font-size: 15pt;
            font-weight: 800;
            color: var(--brand-blue);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }

        .company-sub {
            font-size: 9pt;
            font-weight: bold;
            color: #555;
            text-transform: uppercase;
            line-height: 1.2;
        }

        /* --- CONTENT WRAPPER --- */
        .content {
            padding: 0;
            flex: 1 0 auto;
        }

        /* --- INFO GRID --- */
        .info-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #ddd;
        }

        .info-box { width: 48%; }

        .info-title {
            font-size: 9pt;
            font-weight: bold;
            color: var(--brand-blue);
            text-transform: uppercase;
            margin-bottom: 5px;
            border-bottom: 1px solid #eee;
        }

        .info-row { display: flex; margin-bottom: 3px; }
        .info-label { width: 80px; color: #555; }
        .info-value { flex: 1; font-weight: 600; }

        /* --- TABLE STYLING --- */
        .quotation-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 9pt;
        }

        .quotation-table th {
            background-color: var(--brand-blue);
            color: #fff;
            padding: 6px;
            text-align: center;
            font-weight: bold;
            font-size: 8pt;
            border: 1px solid var(--brand-blue);
        }

        .quotation-table td {
            padding: 6px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .quotation-table tr:nth-child(even) { background-color: var(--bg-zebra); }

        .col-center { text-align: center; }
        .col-right { text-align: right; }
        
        .unit-title { font-weight: bold; color: var(--brand-blue); }
        .unit-specs { font-size: 9pt; color: #444; margin-top: 3px; padding-left: 8px; border-left: 2px solid #eee; }

        /* --- TERMS SECTION --- */
        .terms-container {
            background-color: #f8f9fa;
            padding: 10px;
            border-left: 4px solid var(--brand-blue);
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
        
        .terms-list { margin: 0; padding-left: 20px; font-size: 9pt; }
        .terms-list li { margin-bottom: 3px; }

        /* --- SIGNATURE SECTION --- */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            margin-bottom: 5px;
            page-break-inside: avoid;
        }

        .sig-box { width: 220px; text-align: center; }
        .sig-line { border-bottom: 1px solid #000; margin-top: 35px; margin-bottom: 5px; }

        /* --- FOOTER --- */
        .footer {
            background: white;
            border-top: 2px solid var(--brand-blue);
            padding: 8px 0;
            display: flex;
            justify-content: space-between;
            font-size: 7pt;
            color: var(--brand-blue);
            line-height: 1.3;
            margin-top: 20px;
            flex-shrink: 0;
        }

        .footer-left { 
            text-align: left; 
            width: 48%;
        }
        
        .footer-right { 
            text-align: right; 
            width: 48%;
        }
        
        .footer-title {
            font-weight: bold;
            color: #000;
            margin-bottom: 2px;
            text-transform: uppercase;
            font-size: 7pt;
        }

        /* Print Settings */
        @media print {
            @page {
                size: A4 portrait;
                margin: 15mm 10mm 18mm 10mm;
            }
            
            body { 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact;
                margin: 0;
                padding: 0;
                font-size: 9pt;
                line-height: 1.2;
                display: table;
                width: 100%;
                table-layout: fixed;
            }
            
            .no-print { display: none; }
            .modal { display: none !important; }
            
            .header {
                display: table-header-group;
                height: 70px;
                padding: 5px 0;
                padding-bottom: 10px;
                border-bottom: 2px solid var(--brand-blue);
                page-break-inside: avoid;
            }
            
            .header::after {
                content: '';
                display: block;
                width: 100%;
                height: 2px;
                background: var(--brand-blue);
                margin-top: 5px;
            }
            
            .logo-wrapper {
                top: 3px;
                left: 0;
            }
            
            .logo-wrapper img {
                height: 60px;
            }
            
            .company-name {
                font-size: 14pt;
            }
            
            .company-sub {
                font-size: 8pt;
            }
            
            .content {
                display: table-row-group;
                padding: 0;
                margin-bottom: 0;
            }
            
            .info-container {
                margin-bottom: 10px;
            }
            
            .quotation-table {
                margin-bottom: 8px;
                font-size: 8pt;
            }
            
            .quotation-table th,
            .quotation-table td {
                padding: 5px;
            }
            
            .terms-container {
                padding: 8px;
                margin-bottom: 8px;
            }
            
            .signature-section {
                margin-top: 8px;
                margin-bottom: 10px;
            }
            
            .sig-line {
                margin-top: 30px;
            }
            
            .footer {
                display: table-footer-group;
                background: white;
                border-top: 2px solid var(--brand-blue);
                padding: 8px 0 6px 0;
                font-size: 6pt;
                line-height: 1.2;
                page-break-inside: avoid;
            }
            
            .footer::before {
                content: '';
                display: block;
                width: 100%;
                height: 2px;
                background: var(--brand-blue);
                margin-bottom: 8px;
            }
            
            .footer > * {
                display: inline-block;
            }
            
            .footer-left,
            .footer-right {
                display: inline-block;
                vertical-align: top;
            }
            
            .footer-left {
                width: 48%;
            }
            
            .footer-right {
                width: 48%;
                text-align: right;
            }
            
            .footer-title {
                font-size: 6pt;
                margin-bottom: 1px;
            }
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--brand-blue);
        }
        
        .modal-title {
            color: var(--brand-blue);
            font-size: 18px;
            font-weight: bold;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }
        
        .terms-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .terms-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        
        .terms-item input[type="checkbox"] {
            margin-top: 3px;
        }
        
        .terms-text {
            flex: 1;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .custom-input {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 3px;
            padding: 5px 8px;
            font-size: 12px;
            margin: 5px 0;
        }
        
        .modal-footer {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        
        .btn-primary {
            background-color: var(--brand-blue);
            color: white;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
    </style>
    
    <script>
        // Modal terms selection functionality
        let selectedTerms = [];
        let customValues = {
            periodeSewa: '',
            biayaMobilisasi: ''
        };
        let customTermsCounter = 0;
        let customTermsData = [];

        function addCustomTerm() {
            customTermsCounter++;
            const container = document.getElementById('customTermsContainer');
            const termDiv = document.createElement('div');
            termDiv.className = 'terms-item';
            termDiv.style.marginBottom = '10px';
            termDiv.id = `customTerm${customTermsCounter}`;
            
            termDiv.innerHTML = `
                <input type="checkbox" name="customTerms" value="${customTermsCounter}" id="customTermCheck${customTermsCounter}" checked>
                <div class="terms-text">
                    <textarea class="custom-input" id="customTermText${customTermsCounter}" 
                              placeholder="Masukkan terms custom disini..." 
                              rows="2" style="width: 100%; resize: vertical;"></textarea>
                </div>
                <button type="button" onclick="removeCustomTerm(${customTermsCounter})" 
                        style="background: #dc3545; color: white; border: none; padding: 5px 10px; 
                               border-radius: 3px; cursor: pointer; margin-left: 5px;">
                    ✕
                </button>
            `;
            
            container.appendChild(termDiv);
        }

        function removeCustomTerm(id) {
            const element = document.getElementById(`customTerm${id}`);
            if (element) {
                element.remove();
            }
        }

        function showTermsModal() {
            document.getElementById('termsModal').style.display = 'block';
        }

        function closeTermsModal() {
            document.getElementById('termsModal').style.display = 'none';
        }

        function updateTermsSelection() {
            selectedTerms = [];
            const checkboxes = document.querySelectorAll('input[name="terms"]:checked');
            checkboxes.forEach(cb => {
                selectedTerms.push(parseInt(cb.value));
            });
            
            // Get custom values
            customValues.periodeSewa = document.getElementById('periodeSewa').value;
            customValues.biayaMobilisasi = document.getElementById('biayaMobilisasi').value;
            
            // Get custom terms
            customTermsData = [];
            const customCheckboxes = document.querySelectorAll('input[name="customTerms"]:checked');
            customCheckboxes.forEach(cb => {
                const id = cb.value;
                const textarea = document.getElementById(`customTermText${id}`);
                if (textarea && textarea.value.trim()) {
                    customTermsData.push(textarea.value.trim());
                }
            });
        }

        function applyTermsAndPrint() {
            updateTermsSelection();
            
            // Update terms display
            const termsContainer = document.getElementById('dynamicTerms');
            const termsTexts = {
                1: `Periode Sewa: ${customValues.periodeSewa || 'Min. 2 Bulan'}`,
                2: 'Sistem Pembayaran 30 Hari setelah invoice diterima',
                3: 'Setiap sewa 10 unit free 1 back up unit dan 1 mekanik standby',
                4: 'Harga sudah termasuk biaya perawatan berkala, pergantian sparepart, perbaikan dan service',
                5: 'Harga sudah termasuk Surat Ijin Layak Operational',
                6: 'Harga sudah termasuk biaya mobilisasi',
                7: `Harga belum termasuk biaya mobilisasi Rp ${customValues.biayaMobilisasi || '4.000.000'}`,
                8: 'Harga sudah termasuk training pengoperasian unit dan refresh training',
                9: 'Laporan preventive maintenance serta evaluasi performance setiap bulannya',
                10: 'Harga termasuk additional safety seperti Apar, Rotary Lamp, Klason dan kebutuhan safety lainnya',
                11: 'Harga belum termasuk PPN 12%'
            };
            
            let termsHtml = '';
            selectedTerms.sort((a, b) => a - b);
            selectedTerms.forEach((termNum, index) => {
                termsHtml += `<li>${termsTexts[termNum]}</li>`;
            });
            
            // Add custom terms
            customTermsData.forEach(customTerm => {
                termsHtml += `<li>${customTerm}</li>`;
            });
            
            termsContainer.innerHTML = termsHtml;
            
            // Close modal and print
            closeTermsModal();
            setTimeout(() => {
                window.print();
            }, 500);
        }

        // Show modal on page load instead of auto-print
        window.onload = function() {
            setTimeout(showTermsModal, 500);
        };
        
        // Handle print button in modal
        window.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                if (document.getElementById('termsModal').style.display !== 'block') {
                    showTermsModal();
                }
            }
        });
    </script>
</head>
<body>

    <div class="header">
        <div class="logo-wrapper">
            <img src="<?= base_url('assets/images/company-logo.svg') ?>" alt="Logo"/>
        </div>
        <div class="company-text">
            <div class="company-name">PT Sarana Mitra Luas Tbk</div>
            <div class="company-sub">Forklift & Material Handling Equipment</div>
            <div class="company-sub">Rental - Sales - Service</div>
        </div>
    </div>
    <br>
    <div class="content">
        <div class="info-container">
            <div class="info-box">
                <div class="info-title">Kepada Yth</div>
                <div style="font-size: 10pt; font-weight: bold; margin-bottom: 2px;">
                    <?= esc($quotation['prospect_name'] ?? 'Pelanggan') ?>
                </div>
                <div class="info-row">
                    <span class="info-label">UP</span>
                    <span class="info-value">: <?= esc($quotation['prospect_contact_person'] ?? '-') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Telp</span>
                    <span class="info-value">: <?= esc($quotation['prospect_phone'] ?? '-') ?></span>
                </div>
            </div>

            <div class="info-box">
                <div class="info-title">Data Penawaran</div>
                <div class="info-row">
                    <span class="info-label">No. Quote</span>
                    <span class="info-value">: <?= esc($quotation['quotation_number'] ?? 'DRAFT') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal</span>
                    <span class="info-value">: <?= date('d F Y', strtotime($quotation['quotation_date'] ?? date('Y-m-d'))) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Perihal</span>
                    <span class="info-value">: <strong>Penawaran Harga Sewa</strong></span>
                </div>
            </div>
        </div>
        <br>
        <div style="margin-bottom: 8px;">
            Dengan hormat,<br>
            Bersama ini kami sampaikan penawaran harga sewa unit dengan rincian sebagai berikut:
        </div>

        <table class="quotation-table">
            <thead>
                <tr>
                    <th class="col-center" style="width: 40px;">No</th>
                    <th>Deskripsi & Spesifikasi Unit</th>
                    <th class="col-center" style="width: 70px;">Qty</th>
                    <th class="col-right" style="width: 140px;">Harga Sewa / Bulan / Unit</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($specs)): ?>
                    <?php $no = 1; foreach ($specs as $spec): ?>
                    <tr>
                        <td class="col-center"><?= $no++ ?></td>
                        <td>
                            <?php if ($spec['specification_type'] === 'UNIT'): ?>
                                <?php
                                $parsedQNotes = parse_optima_spec_tech_notes($spec['notes'] ?? null);
                                $qTech         = $parsedQNotes['tech'];
                                $forkFromMasterQ = '';
                                if (! empty($spec['quotation_fork_name'])) {
                                    $forkFromMasterQ = (string) $spec['quotation_fork_name'];
                                    if (! empty($spec['quotation_fork_class'])) {
                                        $forkFromMasterQ .= ' (' . $spec['quotation_fork_class'] . ')';
                                    }
                                }
                                $attachmentPartsQ = array_filter([
                                    $spec['attachment_type'] ?? '',
                                    $spec['attachment_brand'] ?? '',
                                    $spec['attachment_model'] ?? '',
                                ]);
                                $attachmentFromMasterQ = $attachmentPartsQ !== [] ? trim(implode(' ', $attachmentPartsQ)) : '';
                                $forkDisplayQ          = spk_print_pick_detail($forkFromMasterQ, $qTech['fork'] ?? '');
                                $attachmentDisplayQ  = spk_print_pick_detail($attachmentFromMasterQ, $qTech['attachment'] ?? '');
                                $faModeQ              = optima_print_fork_or_attachment_mode($spec, $qTech, $forkDisplayQ, $attachmentDisplayQ);
                                ?>
                                <div class="unit-title">
                                    <?= !empty($spec['display_unit_type']) ? esc($spec['display_unit_type']) : esc($spec['unit_type'] ?? 'UNIT') ?> 
                                    - <?= strtoupper(esc($spec['display_department'] ?? $spec['department_name'] ?? 'STANDARD')) ?>
                                </div>
                                <div class="unit-specs">
                                    <?= !empty($spec['display_brand']) ? '&bull; Merk: ' . esc($spec['display_brand']) : '' ?>
                                    <?= !empty($spec['display_capacity']) ? ' | Cap. ' . esc($spec['display_capacity']) : '' ?>
                                    <br>
                                    <?= !empty($spec['mast_name']) ? '&bull; Mast ' . esc($spec['mast_name']) : '' ?>
                                    <?= !empty($spec['wheel_name']) ? ' | ' . esc($spec['wheel_name']) : '' ?>
                                    <br>
                                    <?php if (!empty($spec['display_department']) && (stripos($spec['display_department'], 'electric') !== false || stripos($spec['display_department'], 'battery') !== false)): ?>
                                        <?= !empty($spec['jenis_baterai']) ? '&bull; Baterai: ' . esc($spec['jenis_baterai']) : '' ?>
                                    <?php endif; ?>
                                    <?php
                                    $faSep = ! empty($spec['jenis_baterai']) ? ' | ' : '&bull; ';
                                    if ($faModeQ === 'fork' && $forkDisplayQ !== '') {
                                        echo $faSep . 'Fork: ' . esc($forkDisplayQ);
                                    } elseif ($faModeQ === 'attachment' && $attachmentDisplayQ !== '') {
                                        echo $faSep . 'Attachment: ' . esc($attachmentDisplayQ);
                                    }
                                    ?>
                                    <br>
                                    <?= (!empty($spec['unit_accessories']) && $spec['unit_accessories'] !== 'null') ? '&bull; Acc: ' . esc(format_accessory_csv($spec['unit_accessories'])) : '' ?>
                                </div>
                            <?php else: ?>
                                <div class="unit-title">
                                    <?= esc($spec['attachment_type'] ?? 'ATTACHMENT') ?>
                                </div>
                                <div class="unit-specs">
                                    <?= !empty($spec['attachment_brand']) ? '&bull; Merk: ' . esc($spec['attachment_brand']) : '' ?>
                                    <?= !empty($spec['attachment_model']) ? ' - Model: ' . esc($spec['attachment_model']) : '' ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="col-center">
                            <?php 
                            $billableQty = intval($spec['quantity'] ?? 1);
                            $spareQty = intval($spec['spare_quantity'] ?? 0);
                            $totalDelivered = $billableQty + $spareQty;
                            
                            // Display billable quantity
                            echo number_format($billableQty);
                            
                            // Show spare info if exists
                            if ($spareQty > 0) {
                                echo '<br><small style="color: #ff9800; font-weight: bold;">+' . $spareQty . ' Spare</small>';
                                echo '<br><small style="color: #666;">(' . $totalDelivered . ' unit delivered)</small>';
                            }
                            ?>
                        </td>
                        <td class="col-right font-bold">
                            <?php 
                            $priceToShow = 0;
                            if (!empty($spec['monthly_price']) && floatval($spec['monthly_price']) > 0) {
                                $priceToShow = $spec['monthly_price'];
                            } elseif (!empty($spec['daily_price']) && floatval($spec['daily_price']) > 0) {
                                $priceToShow = $spec['daily_price'];
                            } elseif (!empty($spec['total_price']) && floatval($spec['total_price']) > 0) {
                                $priceToShow = $spec['total_price'];
                            }
                            echo formatCurrency($priceToShow, $quotation['currency'] ?? 'IDR');
                            ?>
                        </td>
                    </tr>
                    
                    <?php 
                    // Show operator row if included
                    if (!empty($spec['include_operator']) && intval($spec['include_operator']) === 1): 
                        $operatorQty = intval($spec['operator_quantity'] ?? 1);
                        $operatorMonthly = floatval($spec['operator_monthly_rate'] ?? 0);
                        $operatorDaily = floatval($spec['operator_daily_rate'] ?? 0);
                    ?>
                    <tr style="background-color: #e3f2fd;">
                        <td class="col-center"></td>
                        <td style="padding-left: 20px;">
                            <div class="unit-title" style="color: #1976d2;">
                                <i>└─ TERMASUK OPERATOR</i>
                            </div>
                            <div class="unit-specs" style="color: #555;">
                                <?php if ($operatorMonthly > 0): ?>
                                    &bull; Harga Operator: <?= formatCurrency($operatorMonthly, $quotation['currency'] ?? 'IDR') ?>/bulan/orang
                                <?php endif; ?>
                                <?php if ($operatorDaily > 0): ?>
                                    <?= $operatorMonthly > 0 ? '<br>' : '' ?>
                                    &bull; Harga Harian: <?= formatCurrency($operatorDaily, $quotation['currency'] ?? 'IDR') ?>/hari/orang
                                <?php endif; ?>
                                <?php if (!empty($spec['operator_description'])): ?>
                                    <br>&bull; Keterangan: <?= esc($spec['operator_description']) ?>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="col-center">
                            <?= number_format($operatorQty) ?> org
                        </td>
                        <td class="col-right font-bold">
                            <?php 
                            $operatorPrice = $operatorMonthly > 0 ? $operatorMonthly : ($operatorDaily > 0 ? $operatorDaily : 0);
                            echo formatCurrency($operatorPrice, $quotation['currency'] ?? 'IDR');
                            ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="col-center" style="padding: 20px;">Belum ada unit dipilih.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <br>
        
        <?php 
        // Check if any specification has spare units or operator
        $hasSpareUnits = false;
        $hasOperator = false;
        foreach ($specs as $spec) {
            if (!empty($spec['spare_quantity']) && intval($spec['spare_quantity']) > 0) {
                $hasSpareUnits = true;
            }
            if (!empty($spec['include_operator']) && intval($spec['include_operator']) === 1) {
                $hasOperator = true;
            }
        }
        ?>
        
        <?php if ($hasSpareUnits || $hasOperator): ?>
        <div style="background-color: #fff3cd; border: 1px solid #ffc107; padding: 8px 12px; margin-bottom: 10px; border-radius: 4px;">
            <strong style="color: #856404;">Catatan Penting:</strong>
            <ul style="margin: 5px 0 0 0; padding-left: 20px; color: #856404;">
                <?php if ($hasSpareUnits): ?>
                <li><strong>Unit Spare (Cadangan)</strong> disediakan sebagai backup untuk kontinuitas operasional 24/7 dan <strong>TIDAK DITAGIH</strong>. Jumlah unit yang ditagih sesuai quantity yang tercantum.</li>
                <?php endif; ?>
                <?php if ($hasOperator): ?>
                <li><strong>Harga Operator</strong> sudah termasuk dalam quotation sesuai spesifikasi yang tercantum di atas.</li>
                <?php endif; ?>
            </ul>
        </div>
        <?php endif; ?>
                   
        <div class="terms-container">
            <div style="font-weight: bold; text-decoration: underline; margin-bottom: 5px;">Syarat & Ketentuan (Terms & Conditions):</div>
            <ol class="terms-list" id="dynamicTerms">
                <li>Periode Sewa: <?= !empty($quotation['payment_terms']) ? esc($quotation['payment_terms']) : 'Min. 2 Bulan' ?></li>
                <li>Mobilisasi: <?= !empty($quotation['delivery_terms']) ? esc($quotation['delivery_terms']) : 'Ditanggung Penyewa' ?></li>
                <li>Harga sudah termasuk maintenance & service rutin.</li>
                <li>Harga belum termasuk PPN 12%<?= !$hasOperator ? ', Operator,' : '' ?> dan BBM.</li>
            </ol>
        </div>

        <div style="margin-bottom: 5px;">
            Demikian penawaran ini kami sampaikan. Atas perhatiannya, kami ucapkan terima kasih.
        </div>
        <br>

        <div class="signature-section">
            <div class="sig-box" style="width: 60%; text-align: center;">
                <div style="margin-bottom: 5px; font-weight: bold;">TTD Persetujuan Customer</div>
                <div class="sig-line"></div>
                <!-- Nama customer sengaja dikosongkan agar tidak tampil -->
                <div class="sig-name" style="font-weight: bold; margin-top: 5px;"></div>
            </div>
            <div class="sig-box" style="width: 35%; text-align: center;">
                <div style="margin-bottom: 5px; font-weight: bold;">Hormat Kami,</div>
                <div style="margin-bottom: 8px; font-weight: bold;">Marketing Department</div>
                <div class="sig-line"></div>
                <div class="sig-name" style="font-weight: bold; margin-top: 5px;">
                    <?php
                    $marketingSigner = resolvePrintPersonName(
                        $quotation,
                        ['created_by_name', 'created_by_full_name', 'marketing_full_name', 'marketing_name'],
                        'Marketing Manager'
                    );
                    ?>
                    <?= esc($marketingSigner) ?>
                </div>
                <?php if (!empty($quotation['created_by_phone'])): ?>
                <div style="font-size: 8pt; color: #666; margin-top: 2px;">
                    <?= esc($quotation['created_by_phone']) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <div class="footer">
        <div class="footer-left">
            <div class="footer-title">Office :</div>
            Jl. Kenari Utara II Blk. C No.03 & 05A, Cibatu,<br>
            Kec. Cikarang Pusat, Kabupaten Bekasi, 17550<br>
            Telp : (021) - 3973 9988 / (021) - 8990 2188<br>
            Email : sales@sml.co.id
        </div>
        <div class="footer-right">
            <div class="footer-title">Workshop :</div>
            Jl. Gemalapik, Desa Pasirsari,<br>
            Cikarang Selatan - Kab. Bekasi<br>
            Telp : (021) - 8911 7466 / (021) - 8990 2188
        </div>
    </div>

    <!-- Terms Selection Modal -->
    <div id="termsModal" class="modal no-print">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Pilih Syarat & Ketentuan</div>
                <button class="close-btn" onclick="closeTermsModal()">&times;</button>
            </div>
            
            <div class="terms-grid">
                <div class="terms-item">
                    <input type="checkbox" name="terms" value="1" id="term1" checked>
                    <div class="terms-text">
                        <label for="term1"><strong>1. Periode Sewa:</strong></label>
                        <input type="text" class="custom-input" id="periodeSewa" placeholder="contoh: 2 Bulan" value="2 Tahun">
                    </div>
                </div>
                
                <div class="terms-item">
                    <input type="checkbox" name="terms" value="2" id="term2">
                    <div class="terms-text">
                        <label for="term2"><strong>2. Sistem Pembayaran</strong><br>30 Hari setelah invoice diterima</label>
                    </div>
                </div>
                
                <div class="terms-item">
                    <input type="checkbox" name="terms" value="3" id="term3">
                    <div class="terms-text">
                        <label for="term3"><strong>3. Free Unit & Mekanik</strong><br>Setiap sewa 10 unit free 1 back up unit dan 1 mekanik standby</label>
                    </div>
                </div>
                
                <div class="terms-item">
                    <input type="checkbox" name="terms" value="4" id="term4" checked>
                    <div class="terms-text">
                        <label for="term4"><strong>4. Perawatan & Service</strong><br>Harga sudah termasuk biaya perawatan berkala, pergantian sparepart, perbaikan dan service</label>
                    </div>
                </div>
                
                <div class="terms-item">
                    <input type="checkbox" name="terms" value="5" id="term5">
                    <div class="terms-text">
                        <label for="term5"><strong>5. Surat Ijin Layak Operational</strong><br>Harga sudah termasuk SILO</label>
                    </div>
                </div>
                
                <div class="terms-item">
                    <input type="checkbox" name="terms" value="6" id="term6">
                    <div class="terms-text">
                        <label for="term6"><strong>6. Mobilisasi (Termasuk)</strong><br>Harga sudah termasuk biaya mobilisasi</label>
                    </div>
                </div>
                
                <div class="terms-item">
                    <input type="checkbox" name="terms" value="7" id="term7">
                    <div class="terms-text">
                        <label for="term7"><strong>7. Mobilisasi (Belum Termasuk)</strong></label>
                        <input type="text" class="custom-input" id="biayaMobilisasi" placeholder="contoh: 4.000.000" value="4.000.000">
                    </div>
                </div>
                
                <div class="terms-item">
                    <input type="checkbox" name="terms" value="8" id="term8">
                    <div class="terms-text">
                        <label for="term8"><strong>8. Training</strong><br>Harga sudah termasuk training pengoperasian unit dan refresh training</label>
                    </div>
                </div>
                
                <div class="terms-item">
                    <input type="checkbox" name="terms" value="9" id="term9">
                    <div class="terms-text">
                        <label for="term9"><strong>9. Laporan Maintenance</strong><br>Laporan preventive maintenance serta evaluasi performance setiap bulannya</label>
                    </div>
                </div>
                
                <div class="terms-item">
                    <input type="checkbox" name="terms" value="10" id="term10">
                    <div class="terms-text">
                        <label for="term10"><strong>10. Additional Safety</strong><br>Termasuk Apar, Rotary Lamp, Klason dan kebutuhan safety lainnya</label>
                    </div>
                </div>
                
                <div class="terms-item" style="grid-column: span 2;">
                    <input type="checkbox" name="terms" value="11" id="term11" checked>
                    <div class="terms-text">
                        <label for="term11"><strong>11. PPN</strong><br>Harga belum termasuk PPN 12%</label>
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 20px; padding-top: 15px; border-top: 2px solid var(--brand-blue);">
                <div style="font-weight: bold; color: var(--brand-blue); margin-bottom: 10px;">Tambahan Terms Custom:</div>
                <div id="customTermsContainer">
                    <!-- Custom terms akan ditambahkan di sini -->
                </div>
                <button type="button" class="btn btn-secondary" onclick="addCustomTerm()" style="margin-top: 10px;">
                    + Tambah Terms Custom
                </button>
            </div>
            
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeTermsModal()">Batal</button>
                <button class="btn btn-primary" onclick="applyTermsAndPrint()">Print Quotation</button>
            </div>
        </div>
    </div>

</body>
</html>