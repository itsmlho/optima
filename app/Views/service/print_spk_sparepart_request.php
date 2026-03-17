<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Permintaan Sparepart - <?= esc($spk['nomor_spk'] ?? 'N/A') ?></title>
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64,">
    <link rel="shortcut icon" type="image/x-icon" href="data:image/x-icon;base64,">
    <meta name="print-option" content="no-header-footer">
    <style>
        @page {
            size: A4;
            margin: 10mm 8mm 15mm 8mm;
            @top-left { content: ""; }
            @top-center { content: ""; }
            @top-right { content: ""; }
            @bottom-left { content: ""; }
            @bottom-center { content: ""; }
            @bottom-right { content: ""; }
        }

        @media print {
            @page { margin: 10mm 8mm 15mm 8mm; size: A4; }
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin: 0 !important;
                padding: 0 !important;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 9.5px;
                color: #222;
                line-height: 1.15;
            }
            .no-print { display: none !important; }
            .print-footer {
                position: fixed;
                bottom: 3mm;
                left: 8mm;
                right: 8mm;
                display: block !important;
            }
            body::before, body::after { content: none !important; display: none !important; }
        }

        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #222; }

        .table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .table th, .table td {
            border: 1px solid #9aa1a7;
            padding: .4rem .5rem;
            vertical-align: top;
            line-height: 1.3;
        }
        .table th { background-color: #f8f9fa; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* Header */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 2px solid #333;
        }
        .header-left { flex: 0 0 auto; display: flex; align-items: center; }
        .header-center { flex: 1; text-align: center; padding: 0 20px; }
        .header-right { flex: 0 0 auto; text-align: right; display: flex; flex-direction: column; align-items: flex-end; }
        .logo { max-height: 60px; }
        .title { font-size: 16px; font-weight: bold; margin: 0; }
        .subtitle { font-size: 13px; color: #555; margin: 0; }
        .header-separator { border: none; border-top: 1px solid #333; margin: 5px 0 12px 0; }
        .document-info { font-size: 10px; color: #333; }
        .doc-number { font-weight: bold; margin: 1px 0; }
        .doc-date { margin: 1px 0; }
        .status-badge {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 4px 8px;
            margin-top: 5px;
        }
        .status-label { font-size: 9px; font-weight: bold; color: #495057; text-transform: uppercase; }

        /* Info grid */
        .label { color: #374151; }
        .val { color: #111827; font-weight: 600; }

        /* Document title band */
        .doc-title-band {
            background-color: #f0f4f8;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 8px 12px;
            margin: 10px 0;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 1px;
        }

        /* Signature */
        .sig { text-align: center; }
        .sig-line { border-top: 1px solid #333; margin-top: 50px; padding-top: 4px; font-size: 9px; color: #555; }

        /* Notes box */
        .notes-box { border: 1px solid #9aa1a7; padding: 8px; margin-bottom: 12px; min-height: 40px; font-size: 10px; }

        /* Print footer */
        .print-footer {
            display: none;
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
        }

        /* Loading overlay */
        .loading-print {
            position: fixed; top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255,255,255,0.95);
            padding: 30px; border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            z-index: 9999; text-align: center;
        }
        @media print { .loading-print { display: none !important; } }
    </style>
</head>
<body>
    <div class="loading-print no-print">
        <div style="font-size: 16pt; font-weight: bold; margin-bottom: 10px;">Mempersiapkan Dokumen...</div>
        <div style="font-size: 11pt; color: #666;">Halaman akan dicetak otomatis</div>
    </div>

    <div class="main-content">

        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <img src="<?= base_url('assets/images/company-logo.svg') ?>" class="logo" alt="logo">
            </div>
            <div class="header-center">
                <div class="title">PT SARANA MITRA LUAS Tbk</div>
                <div class="subtitle">Form Permintaan Sparepart</div>
            </div>
            <div class="header-right">
                <div class="document-info">
                    <div class="doc-number">No <?= esc($spk['nomor_spk'] ?? 'N/A') ?></div>
                    <div class="doc-date">Tanggal: <?= date('d F Y', strtotime($spk['dibuat_pada'] ?? $spk['created_at'] ?? 'now')) ?></div>
                </div>
                <div class="status-badge">
                    <span class="status-label">Permintaan Sparepart</span>
                </div>
            </div>
        </div>
        <hr class="header-separator">

        <!-- Document Title Band -->
        <div class="doc-title-band">FORM PERMINTAAN SPAREPART</div>

        <!-- SPK Info -->
        <div class="row mb-1" style="display:flex; gap:0;">
            <div style="flex:1; padding-right:10px;">
                <span class="label">Dasar SPK:</span> <span class="val"><?= esc($spk['nomor_spk'] ?? 'N/A') ?></span><br>
                <span class="label">Customer:</span> <span class="val"><?= esc($spk['nama_customer'] ?? $spk['pelanggan'] ?? 'N/A') ?></span>
            </div>
            <div style="flex:1; padding-left:10px;">
                <span class="label">Jenis SPK:</span> <span class="val"><?= esc($spk['jenis_spk'] ?? 'N/A') ?></span><br>
                <span class="label">Tanggal Permintaan:</span> <span class="val"><?= date('d F Y, H:i') ?> WIB</span>
            </div>
        </div>
        <br>

        <!-- Spareparts Table -->
        <table class="table">
            <thead>
                <tr>
                    <th style="width:5%">No</th>
                    <th style="width:10%">Tipe</th>
                    <th style="width:35%">Nama Item</th>
                    <th style="width:8%">Qty</th>
                    <th style="width:8%">Satuan</th>
                    <th style="width:14%">Sumber</th>
                    <th style="width:20%">Catatan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($spareparts) && count($spareparts) > 0): ?>
                    <?php $no = 1; ?>
                    <?php foreach ($spareparts as $item): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center"><?= esc(strtoupper($item['item_type'] ?? 'SPAREPART')) ?></td>
                            <td><?= esc($item['sparepart_name'] ?? '-') ?></td>
                            <td class="text-center"><?= esc($item['quantity_brought'] ?? 0) ?></td>
                            <td class="text-center"><?= esc(strtoupper($item['satuan'] ?? 'PCS')) ?></td>
                            <td class="text-center">
                                <?php
                                $source = strtoupper($item['source_type'] ?? 'WAREHOUSE');
                                echo esc($source);
                                if ($source === 'KANIBAL' && !empty($item['source_unit_no'])) {
                                    echo '<br><small style="font-size:9px;color:#555;">Unit: ' . esc($item['source_unit_no']) . '</small>';
                                }
                                ?>
                            </td>
                            <td style="font-size:10px;">
                                <?php
                                if (strtoupper($item['source_type'] ?? '') === 'KANIBAL') {
                                    echo esc($item['source_notes'] ?? '-');
                                } else {
                                    echo esc($item['notes'] ?? '-');
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center" style="padding:20px; font-style:italic; color:#999;">
                            Tidak ada sparepart yang direncanakan
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Notes Section -->
        <?php if (!empty($notes)): ?>
        <div class="notes-box">
            <strong style="font-size:10px;">Catatan Tambahan:</strong><br>
            <?= nl2br(esc($notes)) ?>
        </div>
        <?php endif; ?>

        <!-- Signature Section -->
        <table class="table" style="margin-top:30px;">
            <tr>
                <td style="width:33%; text-align:center; height:80px; vertical-align:top;">
                    <strong>Diminta Oleh</strong><br>
                    <small style="color:#555;">(Service)</small>
                    <div class="sig-line">Nama &amp; Tanggal</div>
                </td>
                <td style="width:33%; text-align:center; height:80px; vertical-align:top;">
                    <strong>Disetujui Oleh</strong><br>
                    <small style="color:#555;">(Warehouse)</small>
                    <div class="sig-line">Nama &amp; Tanggal</div>
                </td>
                <td style="width:33%; text-align:center; height:80px; vertical-align:top;">
                    <strong>Diterima Oleh</strong><br>
                    <small style="color:#555;">(Service)</small>
                    <div class="sig-line">Nama &amp; Tanggal</div>
                </td>
            </tr>
        </table>

    </div><!-- end main-content -->

    <!-- Print Footer -->
    <div class="print-footer" id="printFooter">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div style="text-align:left;">
                <strong>PT SARANA MITRA LUAS Tbk</strong><br>
                <span style="color:#888;">Sistem OPTIMA - Document Management</span>
            </div>
            <div style="text-align:center;">
                <span>Tanggal Cetak: <?= date('d/m/Y H:i') ?></span><br>
                <span style="color:#888;">Dokumen dibuat otomatis oleh sistem OPTIMA</span>
            </div>
            <div style="text-align:right;">
                <span>SPK No: <?= esc($spk['nomor_spk'] ?? 'N/A') ?></span><br>
                <span style="color:#888;">Form Permintaan Sparepart</span>
            </div>
        </div>
    </div>

    <script>
        function initiatePrint() {
            if (window.matchMedia && window.matchMedia('print').matches) return;
            setTimeout(function() {
                try { window.print(); } catch(e) { console.log('Print failed:', e); }
            }, 500);
        }

        if (document.readyState === 'complete') {
            initiatePrint();
        } else {
            window.addEventListener('load', initiatePrint);
        }

        window.addEventListener('beforeprint', function() {
            const footer = document.getElementById('printFooter');
            if (footer) footer.style.display = 'block';
            const loading = document.querySelector('.loading-print');
            if (loading) loading.style.display = 'none';
        });

        window.addEventListener('afterprint', function() {
            const footer = document.getElementById('printFooter');
            if (footer) footer.style.display = 'none';
            setTimeout(function() { window.close(); }, 100);
        });
    </script>
</body>
</html>
