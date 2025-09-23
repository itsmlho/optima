<?php
$di = $di ?? [];
$spk = $spk ?? [];
$s = $spesifikasi ?? [];
$items = $items ?? [];
$unit_items = $unit_items ?? []; // Array of units for multiple DI pages
$k = $k ?? []; // Kontrak spesifikasi data - added to prevent undefined variable errors
$status = strtoupper((string)($di['status'] ?? ''));
$placeholder = ($status === 'SUBMITTED' || $status === 'DIAJUKAN');

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
                margin: 10mm;
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
        
        /* Header */
        .document-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
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
            margin-bottom: 20px;
        }
        
        .section-title {
            background: #f8f9fa;
            border: 1px solid #000;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .info-item {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            width: 140px;
            font-weight: bold;
            flex-shrink: 0;
        }
        
        .info-value {
            flex: 1;
            border-bottom: 1px dotted #ccc;
            min-height: 18px;
            padding-left: 8px;
        }
        
        .info-full {
            grid-column: 1 / -1;
        }
        
        /* Unit Details */
        .unit-section {
            border: 2px solid #000;
            padding: 15px;
            margin: 20px 0;
        }
        
        .unit-header {
            background: #e8f4fd;
            border: 1px solid #0ea5e9;
            padding: 10px;
            margin: -15px -15px 15px -15px;
            font-weight: bold;
            text-align: center;
            font-size: 14px;
        }
        
        /* Approval Section */
        .approval-section {
            margin-top: 20px;
            page-break-inside: avoid;
        }
        
        .approval-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 15px;
        }
        
        .approval-box {
            text-align: center;
            border: 1px solid #000;
            padding: 15px 10px;
            min-height: 100px;
        }
        
        .approval-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        
        .approval-subtitle {
            font-size: 9px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .approval-status {
            margin: 10px 0;
            min-height: 30px;
        }
        
        .approved-stamp {
            color: #059669;
            border: 2px solid #059669;
            padding: 3px 8px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
            transform: rotate(-15deg);
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            margin: 10px 20px 5px 20px;
        }
        
        .signature-label {
            font-size: 9px;
            color: #666;
        }
        
        /* Notes */
        .notes-section {
            margin-top: 20px;
            border: 1px solid #000;
            padding: 15px;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .placeholder {
            color: #999;
            font-style: italic;
        }
        
        /* Footer */
        .document-footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 10px;
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
                        <span class="info-label">No. DI:</span>
                        <span class="info-value"><?= esc($di['nomor_di'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">No. SPK:</span>
                        <span class="info-value"><?= esc($spk['nomor_spk'] ?? $spk['no_spk'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Kontrak/PO:</span>
                        <span class="info-value"><?= esc($di['po_kontrak_nomor'] ?? $spk['po_kontrak_nomor'] ?? $spk['kontrak_no'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tanggal Kirim:</span>
                        <span class="info-value"><?= esc($di['tanggal_kirim'] ?? '-') ?></span>
                    </div>
                </div>
                <div>
                    <div class="info-item">
                        <span class="info-label">Nama Perusahaan:</span>
                        <span class="info-value"><?= esc($di['pelanggan'] ?? $spk['pelanggan'] ?? $spk['customer_name'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">PIC:</span>
                        <span class="info-value"><?= esc($spk['pic'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Contact Person:</span>
                        <span class="info-value"><?= esc($spk['kontak'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status DI:</span>
                        <span class="info-value"><?= esc($status) ?></span>
                    </div>
                </div>
                <div class="info-full">
                    <div class="info-item">
                        <span class="info-label">Alamat Pengiriman:</span>
                        <span class="info-value"><?= esc($di['lokasi'] ?? $spk['lokasi'] ?? '-') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transportation Information -->
        <div class="info-section">
            <div class="section-title">INFORMASI TRANSPORTASI</div>
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
        </div>

        <!-- Unit Details -->
        <div class="unit-section">
            <div class="unit-header">
                DETAIL UNIT YANG DIKIRIM
                <?php if ($current_unit): ?>
                    - <?= esc($current_unit['no_unit'] ?? 'Unit') ?> (<?= esc($current_unit['merk_unit'] ?? '') ?> <?= esc($current_unit['model_unit'] ?? '') ?>)
                <?php endif; ?>
            </div>

            <div class="info-grid">
                <div>
                    <div class="info-item">
                        <span class="info-label">ID Unit:</span>
                        <span class="info-value"><?= esc($current_unit['no_unit'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Merk Unit:</span>
                        <span class="info-value"><?= esc($current_unit['merk_unit'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Model Unit:</span>
                        <span class="info-value"><?= esc($current_unit['model_unit'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Jenis Unit:</span>
                        <span class="info-value"><?= esc($current_unit['jenis_unit'] ?? $s['jenis_unit'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tipe Unit:</span>
                        <span class="info-value"><?php
                            // Gabungkan Tipe Unit dengan Departemen
                            $tipeUnit = $current_unit['tipe_jenis'] ?? $s['tipe_jenis'] ?? '';
                            $departemen = $current_unit['departemen_name'] ?? $s['departemen_id_name'] ?? $k['departemen_name'] ?? '';
                            $combinedTipe = [];
                            if (!empty($tipeUnit)) $combinedTipe[] = $tipeUnit;
                            if (!empty($departemen)) $combinedTipe[] = $departemen;
                            echo esc(!empty($combinedTipe) ? implode(' ', $combinedTipe) : '-');
                        ?></span>
                    </div>
                </div>
                <div>
                    <div class="info-item">
                        <span class="info-label">Serial Number:</span>
                        <span class="info-value"><?= esc($current_unit['serial_number'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Kapasitas:</span>
                        <span class="info-value"><?= esc($current_unit['kapasitas_name'] ?? $s['kapasitas_id_name'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Mast:</span>
                        <span class="info-value"><?= esc($s['mast_id_name'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Attachment:</span>
                        <span class="info-value"><?= esc($current_unit['attachment_merk'] ?? $s['attachment_tipe'] ?? $k['attachment_tipe'] ?? $k['attachment_name'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Baterai:</span>
                        <span class="info-value"><?php
                            // Format: Merk Model (SN)
                            $batteryMerk = $k['jenis_baterai'] ?? $k['baterai_type'] ?? '';
                            $batterySN = $current_unit['sn_baterai'] ?? '';
                            if (!empty($batteryMerk)) {
                                echo esc($batteryMerk . (!empty($batterySN) ? ' (' . $batterySN . ')' : ''));
                            } else {
                                echo '-';
                            }
                        ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Charger:</span>
                        <span class="info-value"><?php
                            // Format: Merk Model (SN)
                            $chargerMerk = $k['kontrak_charger_model'] ?? $k['charger_model'] ?? '';
                            $chargerSN = $current_unit['sn_charger'] ?? '';
                            if (!empty($chargerMerk)) {
                                echo esc($chargerMerk . (!empty($chargerSN) ? ' (' . $chargerSN . ')' : ''));
                            } else {
                                echo '-';
                            }
                        ?></span>
                    </div>
                </div>
            </div>
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
            
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-top: 15px;">
                <div style="text-align: center;">
                    <div style="font-weight: bold; margin-bottom: 5px;">MARKETING</div>
                    <div style="font-size: 10px; color: #666; margin-bottom: 10px;">Pembuat DI</div>
                    <?php 
                        $marketingName = $spk['created_by_name'] ?? $spk['marketing_name'] ?? $spk['created_by'] ?? '';
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
                        <?php if (!empty($di['created_at'])): ?>
                            Tanggal: <?= date('d/m/Y', strtotime($di['created_at'])) ?>
                        <?php else: ?>
                            Tanggal: __________
                        <?php endif; ?>
                    </div>
                </div>

                <div style="text-align: center;">
                    <div style="font-weight: bold; margin-bottom: 5px;">BAG. PDI</div>
                    <div style="font-size: 10px; color: #666; margin-bottom: 10px;">Pengecekan Unit</div>
                    <?php if (!empty($spk['pdi_tanggal_approve'])): ?>
                        <div style="color: #059669; border: 2px solid #059669; padding: 3px 8px; font-size: 10px; font-weight: bold; display: inline-block; transform: rotate(-15deg); margin: 10px 0;">APPROVED</div>
                        <br/>
                        <div style="font-size: 10px; margin-top: 5px;">(<?= esc($spk['pdi_mekanik'] ?? 'PDI Team') ?>)</div>
                    <?php else: ?>
                        <br/><br/>
                        <div style="border-bottom: 1px solid #000; margin: 10px 20px 5px 20px;"></div>
                        <div style="font-size: 9px; color: #666;">(...........................)</div>
                    <?php endif; ?>
                    <div style="font-size: 9px; color: #666; margin-top: 5px;">
                        <?php if (!empty($spk['pdi_tanggal_approve'])): ?>
                            Tanggal: <?= date('d/m/Y', strtotime($spk['pdi_tanggal_approve'])) ?>
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

        <!-- Document Footer -->
        <div class="document-footer">
            <div>PT. Sarana Mitra Luas - Delivery Instruction <?= esc($di['nomor_di'] ?? '') ?>-U0<?= $unit_index + 1 ?> - Unit ke-<?= $unit_index + 1 ?></div>
            <div>Dicetak pada: <?= date('d/m/Y H:i:s') ?></div>
        </div>
    </div>

<?php endforeach; ?>

</body>
</html>
