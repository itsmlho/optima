<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Verifikasi Unit - <?= $audit['audit_number'] ?? 'Audit' ?></title>
    <style>
        @page { size: A4; margin: 1.5cm; }
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 15px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 8px; }
        .header h1 { margin: 0; font-size: 16px; font-weight: bold; }
        .header h2 { margin: 4px 0 0; font-size: 12px; font-weight: normal; }
        .info-grid { display: table; width: 100%; margin-bottom: 15px; }
        .info-row { display: table-row; }
        .info-cell { display: table-cell; padding: 4px 8px; border: 1px solid #ddd; width: 25%; }
        .info-label { font-weight: bold; background-color: #f5f5f5; }
        .verification-table { width: 100%; border-collapse: collapse; font-size: 8px; margin-bottom: 15px; }
        .verification-table th, .verification-table td { border: 1px solid #333; padding: 3px 5px; text-align: left; }
        .verification-table th { background-color: #f5f5f5; font-weight: bold; text-align: center; }
        .verification-table th:nth-child(1) { width: 20%; }
        .verification-table th:nth-child(2) { width: 35%; }
        .verification-table th:nth-child(3) { width: 35%; }
        .verification-table th:nth-child(4) { width: 10%; }
        .text-center { text-align: center; }
        .real-field { border-bottom: 1px solid #333; min-height: 12px; display: block; }
        .unit-section { margin-bottom: 10px; page-break-inside: avoid; }
        .unit-title { font-weight: bold; background: #e9ecef; padding: 4px 8px; margin-bottom: 0; }
        .signature-section { margin-top: 25px; page-break-inside: avoid; }
        .signature-grid { display: table; width: 100%; }
        .signature-cell { display: table-cell; width: 33%; text-align: center; padding: 30px 10px 10px; vertical-align: top; }
        .signature-line { border-bottom: 1px solid #000; margin-top: 30px; padding-bottom: 4px; }
        .no-print { position: fixed; top: 10px; right: 10px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <button class="no-print btn btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print</button>

    <div class="container">
        <div class="header">
            <h1>FORM VERIFIKASI UNIT DI LOKASI</h1>
            <h2>No. Audit: <?= esc($audit['audit_number'] ?? '-') ?></h2>
        </div>

        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell info-label">Customer</div>
                <div class="info-cell"><?= esc($audit['customer_name'] ?? '-') ?></div>
                <div class="info-cell info-label">Lokasi</div>
                <div class="info-cell"><?= esc($audit['location_name'] ?? '-') ?></div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">No. Kontrak</div>
                <div class="info-cell font-monospace"><?= esc($audit['no_kontrak_masked'] ?? $audit['no_kontrak'] ?? '-') ?></div>
                <div class="info-cell info-label">No. PO</div>
                <div class="info-cell font-monospace"><?= esc($audit['no_po_masked'] ?? '-') ?></div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Periode Kontrak</div>
                <div class="info-cell"><?= esc($audit['periode_text'] ?? '-') ?></div>
                <div class="info-cell info-label">Status Periode</div>
                <div class="info-cell"><strong><?= esc($audit['periode_status_text'] ?? '-') ?></strong></div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Tanggal Audit</div>
                <div class="info-cell"><?= $audit['audit_date'] ? date('d-m-Y', strtotime($audit['audit_date'])) : '-' ?></div>
                <div class="info-cell info-label"></div>
                <div class="info-cell"></div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Total Unit (Kontrak)</div>
                <div class="info-cell text-center"><strong><?= $audit['kontrak_total_units'] ?? 0 ?></strong></div>
                <div class="info-cell info-label">Spare Unit (Kontrak)</div>
                <div class="info-cell text-center"><strong><?= $audit['kontrak_spare_units'] ?? 0 ?></strong></div>
            </div>
        </div>

        <?php
        $items = $audit['items'] ?? [];
        foreach ($items as $idx => $item):
            $unitLabel = ($item['expected_no_unit'] ?? $item['actual_no_unit'] ?? 'Unit ' . ($idx + 1));
        ?>
        <div class="unit-section">
            <div class="unit-title">Unit: <?= esc($unitLabel) ?></div>
            <table class="verification-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Database (Kontrak)</th>
                        <th>Real Lapangan</th>
                        <th>Sesuai</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>No Unit</td>
                        <td><?= esc($item['expected_no_unit'] ?? '-') ?></td>
                        <td><span class="real-field"></span></td>
                        <td class="text-center">☐</td>
                    </tr>
                    <tr>
                        <td>Serial Number</td>
                        <td><?= esc($item['expected_serial'] ?? '-') ?></td>
                        <td><span class="real-field"></span></td>
                        <td class="text-center">☐</td>
                    </tr>
                    <tr>
                        <td>Merk / Model</td>
                        <td><?= esc(trim(($item['expected_merk'] ?? '') . ' ' . ($item['expected_model'] ?? '')) ?: '-') ?></td>
                        <td><span class="real-field"></span></td>
                        <td class="text-center">☐</td>
                    </tr>
                    <tr>
                        <td>Spare</td>
                        <td><?= ($item['expected_is_spare'] ?? 0) == 1 ? 'Ya' : 'Tidak' ?></td>
                        <td><span class="real-field"></span></td>
                        <td class="text-center">☐</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endforeach; ?>

        <?php if (empty($items)): ?>
        <p class="text-muted">Tidak ada unit di lokasi ini.</p>
        <?php endif; ?>

        <div style="margin-top: 15px; border: 1px solid #000; padding: 10px;">
            <strong>Ringkasan Hasil:</strong><br>
            Total Unit Ditemukan: _____________ &nbsp;&nbsp; Spare Unit: _____________ &nbsp;&nbsp; Operator Hadir: ☐ Ya ☐ Tidak
        </div>

        <div style="margin-top: 10px; border: 1px solid #ddd; padding: 10px; min-height: 50px;">
            <strong>Catatan Mekanik:</strong><br>
        </div>

        <div class="signature-section">
            <div class="signature-grid">
                <div class="signature-cell">
                    <div class="signature-line"></div>
                    <div>Mekanik / Auditor</div>
                </div>
                <div class="signature-cell">
                    <div class="signature-line"></div>
                    <div>Admin Service</div>
                </div>
                <div class="signature-cell">
                    <div class="signature-line"></div>
                    <div>Customer / PIC</div>
                </div>
            </div>
        </div>

        <div style="margin-top: 15px; text-align: center; font-size: 9px; color: #666;">
            Dicetak: <?= date('d-m-Y H:i') ?> | <?= esc($audit['audit_number'] ?? '') ?>
        </div>
    </div>
</body>
</html>
