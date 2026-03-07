<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Audit Unit - <?= $audit['audit_number'] ?></title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 100%;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header h2 {
            margin: 5px 0 0;
            font-size: 14px;
            font-weight: normal;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-row {
            display: table-row;
        }
        .info-cell {
            display: table-cell;
            padding: 5px 10px;
            border: 1px solid #ddd;
            width: 25%;
        }
        .info-label {
            font-weight: bold;
            background-color: #f5f5f5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
            font-size: 10px;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-grid {
            display: table;
            width: 100%;
            border: none;
        }
        .signature-row {
            display: table-row;
        }
        .signature-cell {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 40px 20px 20px;
            vertical-align: top;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            margin-top: 40px;
            padding-bottom: 5px;
        }
        .notes-section {
            margin-top: 20px;
            border: 1px solid #000;
            padding: 10px;
            min-height: 80px;
        }
        .notes-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .checklist {
            margin: 10px 0;
        }
        .checklist-item {
            display: inline-block;
            margin-right: 20px;
        }
        .checkbox {
            display: inline-block;
            width: 15px;
            height: 15px;
            border: 1px solid #000;
            margin-right: 5px;
        }
        .no-print {
            position: fixed;
            top: 10px;
            right: 10px;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <button class="no-print btn btn-primary" onclick="window.print()">
        <i class="fas fa-print"></i> Print
    </button>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>FORM AUDIT UNIT</h1>
            <h2>Nomor: <?= $audit['audit_number'] ?></h2>
        </div>

        <!-- Info Section -->
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell info-label">Customer</div>
                <div class="info-cell"><?= $audit['customer_name'] ?? '-' ?></div>
                <div class="info-cell info-label">Kode Customer</div>
                <div class="info-cell"><?= $audit['customer_code'] ?? '-' ?></div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Lokasi</div>
                <div class="info-cell"><?= $audit['location_name'] ?? '-' ?></div>
                <div class="info-cell info-label">Alamat</div>
                <div class="info-cell"><?= $audit['location_address'] ?? '-' ?></div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Nomor Kontrak</div>
                <div class="info-cell"><?= $audit['no_kontrak'] ?? '-' ?></div>
                <div class="info-cell info-label">Tanggal Audit</div>
                <div class="info-cell"><?= date('d-m-Y', strtotime($audit['audit_date'])) ?></div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Total Unit (Kontrak)</div>
                <div class="info-cell text-center"><strong><?= $audit['kontrak_total_units'] ?? 0 ?></strong></div>
                <div class="info-cell info-label">Spare Unit (Kontrak)</div>
                <div class="info-cell text-center"><strong><?= $audit['kontrak_spare_units'] ?? 0 ?></strong></div>
            </div>
        </div>

        <!-- Checklist Section -->
        <div class="checklist">
            <strong>Checklist Audit:</strong>
            <div class="checklist-item"><span class="checkbox"></span> Cek Total Unit</div>
            <div class="checklist-item"><span class="checkbox"></span> Cek Nomor Unit (No Asset)</div>
            <div class="checklist-item"><span class="checkbox"></span> Cek Serial Number</div>
            <div class="checklist-item"><span class="checkbox"></span> Cek Spesifikasi (Merk/Model)</div>
            <div class="checklist-item"><span class="checkbox"></span> Cek Status Spare</div>
            <div class="checklist-item"><span class="checkbox"></span> Cek Operator</div>
        </div>

        <!-- Unit Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>No Unit (Kontrak)</th>
                    <th>Serial Number</th>
                    <th>Merk</th>
                    <th>Model</th>
                    <th>Spare?</th>
                    <th style="width: 80px;">No Unit (Actual)</th>
                    <th style="width: 80px;">Serial (Actual)</th>
                    <th>Merk/Model (Actual)</th>
                    <th>Spare?</th>
                    <th>Operator?</th>
                    <th>Hasil</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $items = $audit['items'] ?? [];
                $no = 1;
                foreach ($items as $item):
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= $item['expected_no_unit'] ?? '-' ?></td>
                    <td><?= $item['expected_serial'] ?? '-' ?></td>
                    <td><?= $item['expected_merk'] ?? '-' ?></td>
                    <td><?= $item['expected_model'] ?? '-' ?></td>
                    <td class="text-center"><?= ($item['expected_is_spare'] ?? 0) == 1 ? 'YES' : 'NO' ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php endforeach; ?>
                <!-- Empty rows for additional notes -->
                <?php for ($i = count($items); $i < max(10, count($items)); $i++): ?>
                <tr>
                    <td class="text-center"><?= $i + 1 ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>

        <!-- Summary Section -->
        <table>
            <tr>
                <th colspan="4" class="text-center">RINGKASAN HASIL AUDIT</th>
            </tr>
            <tr>
                <td style="width: 50%;">Total Unit Ditemukan</td>
                <td class="text-right" style="width: 50%;"></td>
            </tr>
            <tr>
                <td>Total Spare Unit Ditemukan</td>
                <td class="text-right"></td>
            </tr>
            <tr>
                <td>Operator Hadir</td>
                <td class="text-right"></td>
            </tr>
            <tr>
                <td><strong>Selisih Unit</strong></td>
                <td class="text-right"><strong></strong></td>
            </tr>
        </table>

        <!-- Notes Section -->
        <div class="notes-section">
            <div class="notes-title">Catatan Mekanik:</div>
            <div style="min-height: 60px;"></div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-grid">
                <div class="signature-row">
                    <div class="signature-cell">
                        <div class="signature-line"></div>
                        <div>Admin Service</div>
                        <div>Tanggal: _____________</div>
                    </div>
                    <div class="signature-cell">
                        <div class="signature-line"></div>
                        <div>Mekanik / Auditor</div>
                        <div>Tanggal: _____________</div>
                    </div>
                    <div class="signature-cell">
                        <div class="signature-line"></div>
                        <div>Customer / PIC</div>
                        <div>Tanggal: _____________</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div style="margin-top: 20px; text-align: center; font-size: 10px; color: #666;">
            Dicetak pada: <?= date('d-m-Y H:i:s') ?> | <?= $audit['audit_number'] ?>
        </div>
    </div>
</body>
</html>
