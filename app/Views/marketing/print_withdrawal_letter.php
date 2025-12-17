<?php
/**
 * SURAT PERINTAH PENARIKAN UNIT (SPPU)
 * Professional withdrawal letter for customer presentation
 * Used for: TARIK and TUKAR workflows
 * Template consistent with DI print format
 */

$di = $di ?? [];
$spk = $spk ?? [];
$units = $units ?? [];
$attachments = $attachments ?? [];
$customerName = $customerName ?? '-';
$customerLocation = $customerLocation ?? '-';
$contractNo = $contractNo ?? '-';
$withdrawalReason = $withdrawalReason ?? '-';
$tujuanDisplay = $tujuanDisplay ?? '-';
$catatanPenting = $catatanPenting ?? '';
$jenis = $jenis ?? '';
$tujuan = $tujuan ?? '';

$diNumber = $di['nomor_di'] ?? '-';
$diDate = $di['tanggal_di'] ?? date('Y-m-d');
$spkNumber = $spk['nomor_spk'] ?? $spk['no_spk'] ?? '-';
$tanggalKirim = $di['tanggal_kirim'] ?? '-';
$namaSupir = $di['nama_supir'] ?? '-';
$noHpSupir = $di['no_hp_supir'] ?? '-';
$kendaraan = $di['kendaraan'] ?? '-';
$noPol = $di['no_polisi_kendaraan'] ?? '-';

// Determine letter title based on jenis
$letterTitle = ($jenis === 'TUKAR') ? 'SURAT PERINTAH PENARIKAN & PENGGANTIAN UNIT' : 'SURAT PERINTAH PENARIKAN UNIT';

// Format dates
$formattedDiDate = $diDate !== '-' ? date('d F Y', strtotime($diDate)) : '-';
$formattedKirimDate = $tanggalKirim !== '-' ? date('d F Y', strtotime($tanggalKirim)) : '-';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPPU - <?= esc($diNumber) ?></title>
    
    <!-- Disable favicon -->
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64,">
    <link rel="shortcut icon" type="image/x-icon" href="data:image/x-icon;base64,">
    
    <!-- Print control meta tags -->
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
            
            .no-print {
                display: none !important;
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
        }
        
        /* Header - Same as DI template */
        .document-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 6px;
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
            width: 140px;
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
        
        /* Reason Box */
        .reason-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px;
            margin: 15px 0;
            font-size: 11px;
            border-radius: 4px;
        }
        
        .reason-box strong {
            color: #856404;
        }
        
        /* Units Table */
        .units-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .units-table th {
            background: #f8f9fa;
            border: 1px solid #9aa1a7;
            padding: 6px 8px;
            font-weight: bold;
            text-align: center;
            font-size: 11px;
        }
        
        .units-table td {
            border: 1px solid #9aa1a7;
            padding: 6px 8px;
            font-size: 11px;
            vertical-align: top;
        }
        
        .units-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .badge-temp {
            background: #fff3cd;
            color: #856404;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #ffc107;
            display: inline-block;
        }
        
        /* Instructions */
        .instructions-box {
            border: 1px solid #0ea5e9;
            background: #e8f4fd;
            padding: 10px;
            margin: 15px 0;
            border-radius: 4px;
        }
        
        .instructions-box h4 {
            font-size: 12px;
            font-weight: bold;
            margin: 0 0 8px 0;
            color: #0369a1;
        }
        
        .instructions-box ol {
            margin: 0;
            padding-left: 20px;
            font-size: 11px;
        }
        
        .instructions-box li {
            margin-bottom: 5px;
        }
        
        /* Signature Section */
        .approval-section {
            margin-top: 20px;
            page-break-inside: avoid;
        }
        
        .approval-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        
        .approval-box {
            text-align: center;
            border: 1px solid #000;
            padding: 8px;
            min-height: 80px;
        }
        
        .approval-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 4px;
        }
        
        .approval-subtitle {
            font-size: 9px;
            color: #666;
            margin-bottom: 30px;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            margin: 6px 10px 4px 10px;
        }
        
        .signature-label {
            font-size: 9px;
            color: #666;
        }
        
        /* Footer - Consistent with DI format */
        .document-footer {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #ccc;
            font-size: 9px;
            color: #666;
            display: none;
        }
        
        .print-footer {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 8px 15mm;
            background: white;
            border-top: 1px solid #ddd;
        }
        
        @media print {
            .print-footer {
                display: block !important;
            }
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
        
        // Show footer when printing
        window.addEventListener('beforeprint', function() {
            const footer = document.getElementById('printFooter');
            if (footer) footer.style.display = 'block';
        });
        
        window.addEventListener('afterprint', function() {
            const footer = document.getElementById('printFooter');
            if (footer) footer.style.display = 'none';
        });
        
        // Auto setup on load
        window.addEventListener('load', function() {
            const diNumber = '<?= str_replace('/', '-', esc($diNumber)) ?>';
            document.title = 'SPPU-' + diNumber;
            const footer = document.getElementById('printFooter');
            if (footer) footer.style.display = 'block';
        });
    </script>
</head>
<body>
    <div class="page">
        <!-- Document Header - Same as DI -->
        <div class="document-header">
            <img src="<?= base_url('assets/images/company-logo.svg') ?>" class="logo" alt="Company Logo" style="width: 160px; height: auto; margin-right: 50px;"/>
            <div class="document-title">
                <h1>PT. SARANA MITRA LUAS</h1>
                <h2><?= strtoupper($letterTitle) ?></h2>
            </div>
            <div class="document-meta">
                <div class="doc-number"><strong>No: <?= esc($diNumber) ?></strong></div>
                <div class="doc-number">SPK: <?= esc($spkNumber) ?></div>
                <div class="doc-date">Tanggal: <?= $formattedDiDate ?></div>
            </div>
        </div>

        <!-- Customer & Document Information -->
        <div class="info-section">
            <div class="section-title">INFORMASI PELANGGAN & DOKUMEN</div>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Nama Pelanggan:</span>
                    <span class="info-value"><?= esc($customerName) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">No. Kontrak/PO:</span>
                    <span class="info-value"><?= esc($contractNo) ?></span>
                </div>
                <div class="info-full info-item">
                    <span class="info-label">Lokasi:</span>
                    <span class="info-value"><?= esc($customerLocation) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal Penarikan:</span>
                    <span class="info-value"><?= $formattedKirimDate ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Nama Supir:</span>
                    <span class="info-value"><?= esc($namaSupir) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">No. HP Supir:</span>
                    <span class="info-value"><?= esc($noHpSupir) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Kendaraan:</span>
                    <span class="info-value"><?= esc($kendaraan) ?> - <?= esc($noPol) ?></span>
                </div>
            </div>
        </div>

        <!-- Withdrawal Reason -->
        <div class="reason-box">
            <strong>⚠️ TUJUAN PENARIKAN:</strong><br>
            <?= esc($tujuanDisplay) ?>
        </div>

        <?php if (!empty($catatanPenting)): ?>
        <!-- Dynamic Important Note -->
        <div class="instructions-box">
            <h4>📌 CATATAN PENTING</h4>
            <p style="margin: 0; font-size: 11px;"><?= esc($catatanPenting) ?></p>
        </div>
        <?php endif; ?>

        <!-- Units to Withdraw -->
        <?php if (count($units) > 0): ?>
        <div class="info-section">
            <div class="section-title">DETAIL UNIT YANG DITARIK</div>
            <table class="units-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">No</th>
                        <th style="width: 10%;">Nomor Unit</th>
                        <th style="width: 25%;">Tipe & Jenis / Merk / Model</th>
                        <th style="width: 12%;">Departemen</th>
                        <th style="width: 10%;">Kapasitas</th>
                        <th style="width: 12%;">Serial Number</th>
                        <th style="width: 7%;">Tahun</th>
                        <th style="width: 20%;">Kelengkapan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($units as $idx => $unit): ?>
                    <tr>
                        <td style="text-align: center;"><?= $idx + 1 ?></td>
                        <td><strong><?= esc($unit['no_unit'] ?? '-') ?></strong></td>
                        <td>
                            <?= esc($unit['unit_tipe'] ?? '-') ?><br>
                            <small style="color: #666;"><?= esc($unit['merk_unit'] ?? '-') ?> - <?= esc($unit['model_unit'] ?? '-') ?></small>
                        </td>
                        <td><?= esc($unit['departemen_nama'] ?? '-') ?></td>
                        <td><?= esc($unit['kapasitas_unit_nama'] ?? '-') ?></td>
                        <td style="font-family: monospace; font-size: 10px;"><?= esc($unit['serial_number'] ?? '-') ?></td>
                        <td style="text-align: center;"><?= esc($unit['tahun_unit'] ?? '-') ?></td>
                        <td style="font-size: 9px;">
                            <?php if (!empty($unit['battery'])): ?>
                                <strong>Battery:</strong><br>
                                <small>
                                    <?= esc($unit['battery']['merk_baterai'] ?? '-') ?> 
                                    <?= esc($unit['battery']['tipe_baterai'] ?? '') ?>
                                    <?php if (!empty($unit['battery']['jenis_baterai'])): ?>
                                        (<?= esc($unit['battery']['jenis_baterai']) ?>)
                                    <?php endif; ?>
                                    <?php if (!empty($unit['battery']['sn_baterai'])): ?>
                                        <br>SN: <?= esc($unit['battery']['sn_baterai']) ?>
                                    <?php endif; ?>
                                </small><br>
                            <?php endif; ?>
                            <?php if (!empty($unit['charger'])): ?>
                                <strong>Charger:</strong><br>
                                <small>
                                    <?= esc($unit['charger']['merk_charger'] ?? '-') ?> 
                                    <?= esc($unit['charger']['tipe_charger'] ?? '') ?>
                                    <?php if (!empty($unit['charger']['sn_charger'])): ?>
                                        <br>SN: <?= esc($unit['charger']['sn_charger']) ?>
                                    <?php endif; ?>
                                </small><br>
                            <?php endif; ?>
                            <?php if (!empty($unit['attachments'])): ?>
                                <?php foreach ($unit['attachments'] as $att): ?>
                                    <strong>Att:</strong> <?= esc($att['tipe'] ?? '-') ?><br>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php if (empty($unit['battery']) && empty($unit['charger']) && empty($unit['attachments'])): ?>
                                <span style="color: #999;">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="8" style="text-align: right; font-weight: bold; background: #f8f9fa;">
                            Total Unit: <?= count($units) ?> unit
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Attachments if any -->
        <?php if (count($attachments) > 0): ?>
        <div class="info-section">
            <div class="section-title">ATTACHMENT YANG DITARIK</div>
            <table class="units-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">No</th>
                        <th style="width: 30%;">Tipe</th>
                        <th style="width: 30%;">Merk</th>
                        <th style="width: 30%;">Model</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attachments as $idx => $att): ?>
                    <tr>
                        <td style="text-align: center;"><?= $idx + 1 ?></td>
                        <td><?= esc($att['att_tipe'] ?? '-') ?></td>
                        <td><?= esc($att['att_merk'] ?? '-') ?></td>
                        <td><?= esc($att['att_model'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Important Instructions -->
        <div class="instructions-box">
            <h4>📋 INSTRUKSI DAN KETENTUAN PENARIKAN</h4>
            <ol>
                <li>Dokumen ini adalah bukti sah penarikan unit oleh PT Sarana Mitra Luas Tbk</li>
                <li>Penerima wajib memeriksa kondisi dan kelengkapan unit. Kerusakan atau kekurangan komponen wajib dicatat dalam Berita Acara (BAP)</li>
                <li>Dengan menandatangani dokumen ini, pelanggan menyatakan unit telah diserahkan sepenuhnya kepada petugas kami sesuai kondisi yang tercatat.</li>
                <?php if ($jenis === 'TUKAR'): ?>
                <li><strong>Pengiriman unit pengganti mengacu pada dokumen Delivery Instruction (DI) terpisah.</strong></li>
                <?php endif; ?>
                <li>Hubungi Divisi Operasional/Marketing kami jika ada kendala teknis atau pertanyaan.</li>
            </ol>
        </div>

        <!-- Signature Section -->
        <div class="approval-section">
            <div class="approval-grid">
                <div class="approval-box">
                    <div class="approval-title">MARKETING</div>
                    <div class="approval-subtitle">Menyetujui Penarikan</div><br>
                    <div class="signature-line"></div>
                    <div class="signature-label">Nama & Tanda Tangan</div>
                </div>
                <div class="approval-box">
                    <div class="approval-title">OPERATIONAL</div>
                    <div class="approval-subtitle">Eksekusi Penarikan</div><br>
                    <div class="signature-line"></div>
                    <div class="signature-label">Nama & Tanda Tangan</div>
                </div>
                <div class="approval-box">
                    <div class="approval-title">CUSTOMER</div>
                    <div class="approval-subtitle">Menerima Penarikan</div><br>
                    <div class="signature-line"></div>
                    <div class="signature-label">Nama & Tanda Tangan</div>
                </div>
            </div>
        </div>

        <!-- Footer - Removed old static footer -->
    </div>

<!-- Print Footer (consistent with DI) -->
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
            <span style="color: #888;">SPPU No: <?= esc($diNumber) ?></span>
        </div>
    </div>
</div>

</body>
</html>
