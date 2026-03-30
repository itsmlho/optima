<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unit Verification - Loading...</title>
    <!-- CSRF token for AJAX requests -->
    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token-value" content="<?= csrf_hash() ?>">
    <!-- jQuery for consistency with unit_verification.php -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Setup CSRF header for all jQuery AJAX (no global base.php on this standalone print page)
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof $ !== 'undefined') {
                var csrfName  = document.querySelector('meta[name="csrf-token-name"]').getAttribute('content');
                var csrfValue = document.querySelector('meta[name="csrf-token-value"]').getAttribute('content');
                $.ajaxSetup({
                    headers: { 'X-CSRF-TOKEN': csrfValue },
                    data: {}  // will be merged per-call
                });
                // Also expose for inline use
                window._csrfName  = csrfName;
                window._csrfValue = csrfValue;
            }
        });
    </script>
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
            margin-bottom: 5px;
            page-break-inside: avoid;
        }
        
        .panel-title {
            font-size: 7.5pt;
            font-weight: bold;
            text-align: center;
            padding: 2px;
            border-bottom: 1px solid #ccc;
            background-color: #f5f5f5;
            color: #000;
        }
        
        .panel-body { padding: 4px; }
        
        /* Info Grid */
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
            font-size: 9pt;
        }
        
        .info-table td { 
            vertical-align: top; 
            padding: 4px 6px;
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
        
        hr.section-separator {
            border: 0;
            border-top: 1px solid #eee;
            margin: 10px 0;
        }

        /* --- Verification Table --- */
        .verification-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 6pt;
            margin: 0;
            table-layout: fixed;
        }
        
        .verification-table th,
        .verification-table td {
            border: 1px solid #333;
            padding: 1px;
            text-align: left;
            vertical-align: middle;
            word-wrap: break-word;
            line-height: 1.0;
        }
        
        .verification-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
            font-size: 6pt;
            color: #000;
            padding: 2px 1px;
        }
        
        /* Column widths for better layout */
        .verification-table th:nth-child(1) { width: 22%; } /* Item */
        .verification-table th:nth-child(2) { width: 33%; } /* Database */
        .verification-table th:nth-child(3) { width: 33%; } /* Real Lapangan */
        .verification-table th:nth-child(4) { width: 12%; } /* Sesuai */
        
        .verification-table td:nth-child(1) { 
            font-weight: 500;
            background-color: #fafafa;
            font-size: 8pt;
        }
        
        .verification-table td:nth-child(2) {
            font-family: monospace;
            font-size: 8pt;
            background-color: #fff;
        }
        
        .verification-table td:nth-child(3) {
            background-color: #fff;
            font-size: 8pt;
        }
        
        .verification-table td:nth-child(4) {
            text-align: center;
            background-color: #fafafa;
            font-size: 8pt;
        }
        
        .text-center { text-align: center; }
        
        .required {
            color: #dc3545;
            font-weight: bold;
        }
        
        .real-field {
            border-bottom: 1px solid #333;
            min-height: 14px;
            display: inline-block;
            width: 95%;
            padding: 1px 2px;
            margin: 0;
            font-size: 7pt;
        }
        
        .checkbox-symbol {
            font-size: 12px;
            font-weight: bold;
            color: #333;
        }
        
        /* --- Unit Accessories Checkboxes --- */
        .accessory-checkbox {
            cursor: default;
            font-size: 8pt;
        }
        
        .accessory-checkbox.checked {
            color: #00ff40ff;
            font-weight: bold;
        }

        /* --- Tanda Tangan --- */
        .signatures {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 20px;
            padding: 15px 0;
        }
        .signature { 
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-height: 80px;
        }
        .signature-role { 
            font-weight: bold; 
            font-size: 10pt;
            margin-bottom: 8px;
            color: #000;
            text-align: center;
        }
        .signature-line {
            height: 40px;
            margin: 8px 0;
            border-bottom: 1px solid #000;
        }
        .signature-name { 
            font-size: 8pt; 
            color: #333;
            margin-bottom: 3px;
            text-align: center;
        }
        .signature-date { 
            font-size: 8pt; 
            color: #666;
            text-align: center;
        }
        
        /* --- Print Footer --- */
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
            display: none;
        }
        
        /* --- Lain-lain --- */
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
            
            .no-print { display: none !important; }
            .print-footer { display: block !important; }
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
                        <td>No. WO</td>
                        <td id="print-wo-number">-</td>
                    </tr>
                    <tr>
                        <td>Unit No.</td>
                        <td id="print-unit-number">-</td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td><?= date('d M Y') ?></td>
                    </tr>
                </table>
            </div>
        </header>
        <h1 class="document-title">FORM VERIFIKASI UNIT</h1>
        
        <div style="display: flex; justify-content: flex-start; align-items: center; margin-bottom: 4px;">
            <div style="font-size: 5pt; color: #666; text-align: left;">
                <span class="required">*</span> Field yang ditandai wajib diverifikasi
            </div>
        </div>

        <main>
            <!-- Unit Verification Section -->
            <div class="content-panel">
                <div class="panel-title">VERIFIKASI DATA UNIT</div>
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
                            <tr>
                                <td>No Unit <span class="required">*</span></td>
                                <td id="db-no-unit">-</td>
                                <td><span class="real-field"></span></td>
                                <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                            </tr>
                            <tr>
                                <td>Pelanggan <span class="required">*</span></td>
                                <td id="db-pelanggan">-</td>
                                <td><span class="real-field"></span></td>
                                <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                            </tr>
                            <tr>
                                <td>Lokasi</td>
                                <td id="db-lokasi">-</td>
                                <td><span class="real-field"></span></td>
                                <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                            </tr>
                            <tr>
                                <td>Serial Number <span class="required">*</span></td>
                                <td id="db-serial-number">-</td>
                                <td><span class="real-field"></span></td>
                                <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                            </tr>
                            <tr>
                                <td>Tahun Unit</td>
                                <td id="db-tahun-unit">-</td>
                                <td><span class="real-field"></span></td>
                                <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                            </tr>
                            <tr>
                                <td>Departemen <span class="required">*</span></td>
                                <td id="db-departemen">-</td>
                                <td><span class="real-field"></span></td>
                                <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                            </tr>
                            <tr>
                                <td>Tipe Unit <span class="required">*</span></td>
                                <td id="db-tipe-unit">-</td>
                                <td><span class="real-field"></span></td>
                                <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                            </tr>
                            <tr>
                                <td>Kapasitas Unit <span class="required">*</span></td>
                                <td id="db-kapasitas-unit">-</td>
                                <td><span class="real-field"></span></td>
                                <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                            </tr>
                            <tr>
                                <td>Model Unit <span class="required">*</span></td>
                                <td id="db-model-unit">-</td>
                                <td><span class="real-field"></span></td>
                                <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                            </tr>
                            <tr>
                                <td>Model Mesin <span class="required">*</span></td>
                                <td id="db-model-mesin">-</td>
                                <td><span class="real-field"></span></td>
                                <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                            </tr>
                            <tr>
                                <td>SN Mesin <span class="required">*</span></td>
                                <td id="db-sn-mesin">-</td>
                                <td><span class="real-field"></span></td>
                                <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                            </tr>
                            <tr>
                                <td>Model Mast <span class="required">*</span></td>
                                <td id="db-model-mast">-</td>
                                <td><span class="real-field"></span></td>
                                <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                            </tr>
                            <tr>
                                <td>SN Mast <span class="required">*</span></td>
                                <td id="db-sn-mast">-</td>
                                <td><span class="real-field"></span></td>
                                <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                            </tr>
                            <tr>
                                <td>Tinggi Mast <span class="required">*</span></td>
                                <td id="db-tinggi-mast">-</td>
                                <td><span class="real-field"></span></td>
                                <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                            </tr>
                            <tr>
                                <td>Keterangan Unit <span class="required">*</span></td>
                                <td id="db-keterangan">-</td>
                                <td><span class="real-field"></span></td>
                                <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                            </tr>
                            <tr>
                                <td>Hour Meter (HM)</td>
                                <td id="db-hour-meter">-</td>
                                <td><span class="real-field"></span></td>
                                <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            
            <!-- Attachment Verification Section -->
            <div class="content-panel">
                <div class="panel-title">VERIFIKASI DATA ATTACHMENT</div>
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
                            <tr>
                                <td>Attachment  <span class="required">*</span></td>
                                <td id="db-attachment">-</td>
                                <td><span class="real-field"></span></td>
                                <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                            </tr>
                            <tr>
                                <td>SN Attachment <span class="required">*</span></td>
                                <td id="db-sn-attachment">-</td>
                                <td><span class="real-field"></span></td>
                                <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                            </tr>
                            <tr id="battery-row" style="display: none;">
                                <td>Baterai <span class="required">*</span></td>
                                <td id="db-baterai">-</td>
                                <td><span class="real-field"></span></td>
                                <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                            </tr>
                            <tr id="battery-sn-row" style="display: none;">
                                <td>SN Baterai <span class="required">*</span></td>
                                <td id="db-sn-baterai">-</td>
                                <td><span class="real-field"></span></td>
                                <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                            </tr>
                            <tr id="charger-row" style="display: none;">
                                <td>Charger <span class="required">*</span></td>
                                <td id="db-charger">-</td>
                                <td><span class="real-field"></span></td>
                                <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                            </tr>
                            <tr id="charger-sn-row" style="display: none;">
                                <td>SN Charger <span class="required">*</span></td>
                                <td id="db-sn-charger">-</td>
                                <td><span class="real-field"></span></td>
                                <td class="text-center"><span class="checkbox-symbol">☐</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            </br>
            <!-- Unit Accessories Verification Section -->
            <div class="content-panel">
                <div class="panel-title">VERIFIKASI AKSESORIS UNIT</div>
                <div class="panel-body">
                    <div style="font-size: 6pt; color: #666; margin-bottom: 8px;">
                        ✓ = Terpasang dan berfungsi | ✗ = Tidak ada / rusak | - = Tidak dibutuhkan
                    </div>
                    
                    <!-- Unit Accessories -->
                    <div style="margin-bottom: 15px;">
                        <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; font-size: 8pt;">
                            <div><span class="accessory-checkbox" data-accessory="LAMPU UTAMA">☐</span> Lampu (Utama, Mundur, Sign, Stop)</div>
                            <div><span class="accessory-checkbox" data-accessory="ROTARY LAMP">☐</span> Rotary Lamp</div>
                            <div><span class="accessory-checkbox" data-accessory="SENSOR PARKING">☐</span> Sensor Parking</div>
                            <div><span class="accessory-checkbox" data-accessory="HORN SPEAKER">☐</span> Horn Speaker</div>
                            <div><span class="accessory-checkbox" data-accessory="APAR 1 KG">☐</span> APAR 1 KG</div>
                            <div><span class="accessory-checkbox" data-accessory="APAR 3 KG">☐</span> APAR 3 KG</div>
                            <div><span class="accessory-checkbox" data-accessory="BEACON">☐</span> Beacon</div>
                            <div><span class="accessory-checkbox" data-accessory="TELEMATIC">☐</span> Telematic</div>
                            <div><span class="accessory-checkbox" data-accessory="BLUE SPOT">☐</span> Blue Spot</div>
                            <div><span class="accessory-checkbox" data-accessory="RED LINE">☐</span> Red Line</div>
                            <div><span class="accessory-checkbox" data-accessory="WORK LIGHT">☐</span> Work Light</div>
                            <div><span class="accessory-checkbox" data-accessory="BACK BUZZER">☐</span> Back Buzzer</div>
                            <div><span class="accessory-checkbox" data-accessory="CAMERA AI">☐</span> Camera AI</div>
                            <div><span class="accessory-checkbox" data-accessory="CAMERA MONITOR">☐</span> Camera Monitor</div>
                            <div><span class="accessory-checkbox" data-accessory="SPEED LIMITER">☐</span> Speed Limiter</div>
                            <div><span class="accessory-checkbox" data-accessory="LASER FORK">☐</span> Laser Fork</div>
                            <div><span class="accessory-checkbox" data-accessory="VOICE ANNOUNCER">☐</span> Voice Announcer</div>
                            <div><span class="accessory-checkbox" data-accessory="HORN KLASON">☐</span> Horn Klason</div>
                            <div><span class="accessory-checkbox" data-accessory="BIO METRIC">☐</span> Bio Metric</div>
                            <div><span class="accessory-checkbox" data-accessory="ACRYLIC">☐</span> Acrylic</div>
                            <div><span class="accessory-checkbox" data-accessory="FIRST AID KIT">☐</span> First Aid Kit</div>
                            <div><span class="accessory-checkbox" data-accessory="SPARK ARRESTOR">☐</span> Spark Arrestor</div>
                            <div><span class="accessory-checkbox" data-accessory="SAFETY BELT INTERLOCK">☐</span> Safety Belt Interlock</div>
                            <div><span class="accessory-checkbox" data-accessory="MIRROR">☐</span> Mirror / Spion</div>
                            <div><span class="accessory-checkbox" data-accessory="SAFETY BELT STANDAR">☐</span> Safety Belt Standar</div>
                            <div><span class="accessory-checkbox" data-accessory="LOAD BACKREST">☐</span> Load Backrest</div>
                            <div><span class="accessory-checkbox" data-accessory="FORKS">☐</span> Forks</div>
                            <div><span class="accessory-checkbox" data-accessory="OVERHEAD GUARD">☐</span> Overhead Guard</div>
                            <div><span class="accessory-checkbox" data-accessory="DOCUMENT HOLDER">☐</span> Document Holder</div>
                            <div><span class="accessory-checkbox" data-accessory="TOOL KIT">☐</span> Tool Kit</div>
                            <div><span class="accessory-checkbox" data-accessory="APAR BRACKET">☐</span> APAR + Bracket</div>
                            <div><span class="accessory-checkbox" data-accessory="ANTI STATIC STRAP">☐</span> Anti-Static Strap</div>
                            <div><span class="accessory-checkbox" data-accessory="WHEEL STOPPER CHOCK">☐</span> Wheel Stopper / Chock</div>
                            <div><span class="accessory-checkbox" data-accessory="FORK EXTENSION">☐</span> Fork Extension</div>
                        </div>
                    </div>
                    
                    <!-- Summary Row -->
                    <div style="margin-top: 8px; padding: 4px; background-color: #f9f9f9; border: 1px solid #ddd; font-size: 6pt;">
                        <strong>Total Unit Accessories Terpasang (database):</strong> <span id="accessories-summary">0 item</span>
                    </div>
                </div>
            </div>
            </br>
            <!-- Simple Signature Section -->
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-top: 8px; font-size: 7pt;">
                <div style="text-align: center;">
                    <div style="font-size: 7pt; margin-bottom: 3px;">Tgl: ___/___/_____</div>
                    <div style="font-weight: bold; margin-bottom: 15px;">Diverifikasi Oleh</div>
                    <div style="border-bottom: 1px solid #000; width: 120px; margin: 0 auto 3px; height: 15px;"></div>
                    <div>Mekanik</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 7pt; margin-bottom: 3px;">Tgl: ___/___/_____</div>
                    <div style="font-weight: bold; margin-bottom: 15px;">Disahkan Oleh</div>
                    <div style="border-bottom: 1px solid #000; width: 120px; margin: 0 auto 3px; height: 15px;"></div>
                    <div>Supervisor</div>
                </div>
            </div>
        </main>
        
        <!-- Print Footer -->
        <div class="print-footer" id="printFooter">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="text-align: left; font-size: 8px;">
                    <strong>PT SARANA MITRA LUAS Tbk</strong><br>
                    <span style="color: #888;">Sistem OPTIMA - Unit Verification</span>
                </div>
                <div style="text-align: center; font-size: 8px;">
                    <span id="printDate">Tanggal Cetak: <?= date('d/m/Y H:i') ?></span><br>
                    <span style="color: #888;">Dokumen ini dibuat secara otomatis oleh sistem OPTIMA</span>
                </div>
                <div style="text-align: right; font-size: 8px;">
                    <span id="pageInfo">Halaman <span id="currentPage">1</span></span><br>
                    <span style="color: #888;">WO No: <span id="footer-wo-number">Unknown</span></span>
                </div>
            </div>
        </div>
    </div>
    
    <?= $this->include('partials/accessory_js') ?>
    <script>
        // Check if loaded in iframe or embedded mode
        const isInIframe = window.parent !== window;
        const isEmbedded = new URLSearchParams(window.location.search).get('embedded') === '1';
        
        // Auto print functionality like print_work_order.php
        function initiatePrint() {
            // Don't auto-print if in iframe or embedded mode (let parent handle printing)
            if (isInIframe || isEmbedded) {
                // console.log('Verification loaded in embedded mode - auto-print disabled');
                return;
            }
            
            if (window.matchMedia && window.matchMedia('print').matches) {
                return;
            }
            
            setTimeout(function() {
                try {
                    window.print();
                } catch (e) {
                    // console.log('Print failed:', e);
                }
            }, 1000);
        }
        
        // Load verification data using same method as unit_verification.php
        function loadVerificationData(workOrderId) {
            // console.log('🔍 Loading unit verification data for WO:', workOrderId);
            
            // Use jQuery like unit_verification.php for consistency
            if (typeof $ !== 'undefined') {
                $.ajax({
                    url: '<?= base_url('service/work-orders/get-unit-verification-data') ?>',
                    type: 'POST',
                    data: {
                        work_order_id: workOrderId,
                        [window._csrfName]: window._csrfValue
                    },
                    success: function(response) {
                        // console.log('📦 Unit verification data received:', response);
                        // console.log('📦 Full response structure:', JSON.stringify(response, null, 2));
                        
                        if (response.success && response.data) {
                            let data = response.data;
                            // console.log('📦 Data structure:', JSON.stringify(data, null, 2));
                            // console.log('📦 Unit data:', data.unit);
                            // console.log('📦 Work order data:', data.work_order);
                            
                            // Populate all verification fields
                            populatePrintData(data);
                        } else {
                            console.error('Failed to load data:', response.message);
                            if (window.OptimaNotify) OptimaNotify.error('Failed to load data: ' + (response.message || 'Unknown error'));
                            else alert('Failed to load data: ' + (response.message || 'Unknown error'));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Error loading unit verification data:', error);
                    }
                });
            } else {
                // Fallback to fetch if jQuery not available
                fetch('<?= base_url("service/work-orders/get-unit-verification-data") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token-value"]') || {}).getAttribute('content') || ''
                    },
                    body: 'work_order_id=' + workOrderId
                        + '&' + encodeURIComponent((document.querySelector('meta[name="csrf-token-name"]') || {}).getAttribute('content') || '_csrf')
                        + '=' + encodeURIComponent((document.querySelector('meta[name="csrf-token-value"]') || {}).getAttribute('content') || '')
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        populatePrintData(data.data);
                    } else {
                        console.error('Failed to load data:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error loading verification data:', error);
                });
            }
        }

        function populatePrintData(data) {
            // console.log('📝 Populating print verification data:', data);
            
            // Extract data same as unit_verification.php
            let unitData = data.unit || {};
            let workOrderData = data.work_order || {};
            let attachmentData = data.attachment || {};
            
            // console.log('📝 Unit data extracted:', unitData);
            // console.log('📝 Work order data extracted:', workOrderData);
            // console.log('📝 Attachment data extracted:', attachmentData);
            
            // Debug: Check if elements exist before populating
            // console.log('📝 Checking elements existence:');
            // console.log('- print-wo-number:', document.getElementById('print-wo-number') ? 'EXISTS' : 'NOT FOUND');
            // console.log('- print-unit-number:', document.getElementById('print-unit-number') ? 'EXISTS' : 'NOT FOUND');
            // console.log('- info-no-unit:', document.getElementById('info-no-unit') ? 'EXISTS' : 'NOT FOUND');
            // console.log('- db-no-unit:', document.getElementById('db-no-unit') ? 'EXISTS' : 'NOT FOUND');
            
            // Update page title
            document.title = 'Unit Verification - ' + (workOrderData.work_order_number || workOrderData.wo_number || 'N/A');
            
            // Populate header - check both possible field names
            const woNumber = workOrderData.work_order_number || workOrderData.wo_number || '-';
            // console.log('📝 WO Number to populate:', woNumber);
            
            const printWoElement = document.getElementById('print-wo-number');
            const printUnitElement = document.getElementById('print-unit-number');
            const footerWoElement = document.getElementById('footer-wo-number');
            
            if (printWoElement) {
                printWoElement.textContent = woNumber;
                // console.log('📝 Updated print-wo-number to:', woNumber);
            } else {
                console.error('❌ print-wo-number element not found');
            }
            
            if (printUnitElement) {
                printUnitElement.textContent = unitData.no_unit || '-';
                // console.log('📝 Updated print-unit-number to:', unitData.no_unit || '-');
            } else {
                console.error('❌ print-unit-number element not found');
            }
            
            if (footerWoElement) {
                footerWoElement.textContent = woNumber;
                // console.log('📝 Updated footer-wo-number to:', woNumber);
            } else {
                console.error('❌ footer-wo-number element not found');
            }
            
            // Debug: List all available fields in unitData
            // console.log('📝 Available fields in unitData:');
            for (let key in unitData) {
                // console.log(`   ${key}: ${unitData[key]}`);
            }
            
            // Populate Unit Database values with debug logging
            // console.log('📝 Populating database values...');
            
            const dbElements = [
                { id: 'db-no-unit', value: unitData.no_unit },
                { id: 'db-pelanggan', value: unitData.pelanggan },
                { id: 'db-lokasi', value: unitData.lokasi },
                { id: 'db-serial-number', value: unitData.serial_number },
                { id: 'db-tahun-unit', value: unitData.tahun_unit },
                { id: 'db-departemen', value: unitData.departemen_name },
                { id: 'db-tipe-unit', value: unitData.tipe_unit_name },
                { id: 'db-model-unit', value: unitData.model_unit_name },
                { id: 'db-kapasitas-unit', value: unitData.kapasitas_name },
                { id: 'db-keterangan', value: unitData.keterangan },
                { id: 'db-hour-meter', value: unitData.hour_meter }
            ];
            
            dbElements.forEach(item => {
                const element = document.getElementById(item.id);
                if (element) {
                    element.textContent = item.value || '-';
                    // console.log(`📝 Updated ${item.id} to: ${item.value || '-'}`);
                } else {
                    console.error(`❌ Element ${item.id} not found`);
                }
            });
            
            // Populate Machine Database values
            // console.log('📝 Populating machine values...');
            
            const machineElements = [
                { id: 'db-model-mesin', value: unitData.model_mesin_name },
                { id: 'db-sn-mesin', value: unitData.sn_mesin },
                { id: 'db-model-mast', value: unitData.model_mast_name },
                { id: 'db-sn-mast', value: unitData.sn_mast },
                { id: 'db-tinggi-mast', value: unitData.tinggi_mast }
            ];
            
            machineElements.forEach(item => {
                const element = document.getElementById(item.id);
                if (element) {
                    element.textContent = item.value || '-';
                    // console.log(`📝 Updated ${item.id} to: ${item.value || '-'}`);
                } else {
                    console.error(`❌ Element ${item.id} not found`);
                }
            });
            
            // Populate Attachment Database values
            // console.log('📝 Populating attachment values...');
            // console.log('📝 Attachment data available:', attachmentData);
            
            // Check if unit is ELECTRIC department
            const isElectricUnit = unitData.departemen_name === 'ELECTRIC';
            // console.log('📝 Department Check:');
            // console.log('📝 - Unit Data:', unitData);
            // console.log('📝 - Department Name:', unitData.departemen_name);
            // console.log('📝 - Is Electric Unit:', isElectricUnit);
            // console.log('📝 - Comparison Result:', unitData.departemen_name === 'ELECTRIC');
            
            // Show/hide battery and charger rows based on department
            const batteryRows = $('#battery-row, #battery-sn-row, #charger-row, #charger-sn-row');
            // console.log('📝 Battery/Charger rows found:', batteryRows.length);
            
            if (isElectricUnit) {
                batteryRows.show();
                // console.log('📝 ✅ Showing battery and charger rows for ELECTRIC unit');
            } else {
                batteryRows.hide();
                // console.log('📝 ❌ Hiding battery and charger rows for non-ELECTRIC unit');
                // console.log('📝 ❌ Department is:', unitData.departemen_name, '(not ELECTRIC)');
            }
            
            const attachmentElements = [
                { id: 'db-attachment', value: attachmentData.attachment_name },
                { id: 'db-sn-attachment', value: attachmentData.sn_attachment },
                { id: 'db-baterai', value: attachmentData.baterai_name },
                { id: 'db-sn-baterai', value: attachmentData.sn_baterai },
                { id: 'db-charger', value: attachmentData.charger_name },
                { id: 'db-sn-charger', value: attachmentData.sn_charger }
            ];
            
            attachmentElements.forEach(item => {
                const element = document.getElementById(item.id);
                if (element) {
                    element.textContent = item.value || '-';
                } else {
                    console.error(`❌ Element ${item.id} not found`);
                }
            });

            // Populate Accessories
            // console.log('📝 Populating accessories...');
            populateAccessories(data.accessories || []);
            
            // console.log('📝 Print data population completed');
            
            // Double-check battery/charger visibility after a short delay
            setTimeout(function() {
                const departmentName = unitData.departemen_name || '';
                const isElectric = departmentName === 'ELECTRIC';
                const batteryRows = $('#battery-row, #battery-sn-row, #charger-row, #charger-sn-row');
                
                // console.log('📝 Final check - Department:', departmentName, 'Is Electric:', isElectric);
                
                if (isElectric) {
                    batteryRows.show();
                    // console.log('📝 ✅ Final: Showing battery/charger rows');
                } else {
                    batteryRows.hide();
                    // console.log('📝 ❌ Final: Hiding battery/charger rows for department:', departmentName);
                }
            }, 100);
        }
        
        // Populate Accessories function
        function normalizeAccessoryValue(value) {
            if (window.OptimaAccessory && typeof window.OptimaAccessory.normalizeCheckboxValue === 'function') {
                return window.OptimaAccessory.normalizeCheckboxValue(value);
            }
            return String(value || '').trim().toUpperCase().replace(/[_-]+/g, ' ').replace(/\s+/g, ' ');
        }

        function populateAccessories(accessories) {
            // console.log('🔧 Populating print accessories:', accessories);
            
            let checkedCount = 0;
            
            // Clear all checkboxes first
            document.querySelectorAll('.accessory-checkbox').forEach(checkbox => {
                checkbox.textContent = '☐';
                checkbox.classList.remove('checked');
            });
            
            // Check accessories that exist in database
            if (accessories && accessories.length > 0) {
                accessories.forEach(function(accessory) {
                    let accessoryValue = normalizeAccessoryValue(accessory.name || accessory.accessory_name || accessory);
                    // console.log('🔍 Looking for print accessory:', accessoryValue);
                    
                    // Find checkbox with matching data-accessory value
                    let checkbox = document.querySelector(`[data-accessory="${accessoryValue}"]`);
                    if (checkbox) {
                        checkbox.textContent = '✓';
                        checkbox.classList.add('checked');
                        checkedCount++;
                        // console.log('✅ Found exact match for print:', accessoryValue);
                    } else {
                        // console.log('❌ No match found for print:', accessoryValue);
                    }
                });
            }
            
            // Update summary
            const summaryElement = document.getElementById('accessories-summary');
            if (summaryElement) {
                summaryElement.textContent = `${checkedCount} item`;
            }
            
            // console.log(`📊 Print: Auto-checked ${checkedCount} accessories out of ${accessories.length}`);
        }
        
        // Initialize
        if (document.readyState === 'complete') {
            // Get work order ID from URL
            const urlParams = new URLSearchParams(window.location.search);
            const workOrderId = urlParams.get('wo_id');
            
            if (workOrderId) {
                loadVerificationData(workOrderId);
            }
            
            initiatePrint();
        } else {
            window.addEventListener('load', function() {
                // Get work order ID from URL
                const urlParams = new URLSearchParams(window.location.search);
                const workOrderId = urlParams.get('wo_id');
                
                if (workOrderId) {
                    loadVerificationData(workOrderId);
                }
                
                initiatePrint();
            });
            
            document.addEventListener('DOMContentLoaded', function() {
                // Get work order ID from URL
                const urlParams = new URLSearchParams(window.location.search);
                const workOrderId = urlParams.get('wo_id');
                
                if (workOrderId) {
                    loadVerificationData(workOrderId);
                }
            });
        }
        
        // Show footer when printing
        window.addEventListener('beforeprint', () => {
            const footer = document.getElementById('printFooter');
            if (footer) footer.style.display = 'block';
        });
        
        window.addEventListener('afterprint', () => {
            const footer = document.getElementById('printFooter');
            if (footer) footer.style.display = 'none';
            
            // Only auto close if not in iframe or embedded mode
            if (!isInIframe && !isEmbedded) {
                setTimeout(function() {
                    window.close();
                }, 100);
            }
        });
        
        // Auto print on load
        window.addEventListener('load', () => {
            const footer = document.getElementById('printFooter');
            if (footer) footer.style.display = 'block';
        });
    </script>
</body>
</html>
