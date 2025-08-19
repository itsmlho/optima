<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PO Attachment & Battery - <?= $data['po']['no_po'] ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
        }
        .po-info {
            margin-bottom: 20px;
        }
        .po-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .po-info td {
            padding: 5px;
            vertical-align: top;
        }
        .po-info td:first-child {
            font-weight: bold;
            width: 150px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .details-table th,
        .details-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        .details-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        @media print {
            body {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PURCHASE ORDER ATTACHMENT & BATTERY</h1>
        <p>PT. OPTIMA</p>
        <p>Jl. Example Street No. 123, Jakarta</p>
    </div>

    <div class="po-info">
        <table>
            <tr>
                <td>PO Number:</td>
                <td><?= $data['po']['no_po'] ?></td>
                <td>PO Date:</td>
                <td><?= $data['po']['tanggal_po'] ?></td>
            </tr>
            <tr>
                <td>Supplier:</td>
                <td><?= $data['po']['nama_supplier'] ?></td>
                <td>Status:</td>
                <td><?= strtoupper($data['po']['status']) ?></td>
            </tr>
            <tr>
                <td>Invoice No:</td>
                <td><?= $data['po']['invoice_no'] ?: '-' ?></td>
                <td>Invoice Date:</td>
                <td><?= $data['po']['invoice_date'] ?: '-' ?></td>
            </tr>
            <tr>
                <td>BL Date:</td>
                <td><?= $data['po']['bl_date'] ?: '-' ?></td>
                <td>Notes:</td>
                <td><?= $data['po']['keterangan_po'] ?: '-' ?></td>
            </tr>
        </table>
    </div>

    <table class="details-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Model</th>
                <th>Specification</th>
                <th>Serial Number</th>
                <th>Qty</th>
                <th>Status</th>
                <th>Verification</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['details'])): ?>
                <?php foreach ($data['details'] as $index => $detail): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $detail['model'] ?: '-' ?></td>
                        <td><?= $detail['specification'] ?: '-' ?></td>
                        <td><?= $detail['serial_number'] ?: '-' ?></td>
                        <td><?= $detail['qty'] ?: '-' ?></td>
                        <td><?= strtoupper($detail['status'] ?? '-') ?></td>
                        <td><?= $detail['status_verifikasi'] ?: '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">No data available</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Printed on: <?= date('Y-m-d H:i:s') ?></p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html> 