<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Unit Tiba di Lokasi Customer</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f5f5f5; margin: 0; padding: 20px; }
        .email-wrapper { max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #ddd; }
        .header { padding: 20px; background-color: #28a745; color: #ffffff; text-align: center; }
        .header h1 { margin: 0; font-size: 18px; font-weight: bold; }
        .content { padding: 25px; }
        .info-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .info-table th { background: #f8f9fa; text-align: left; padding: 10px; width: 35%; font-weight: bold; border: 1px solid #ddd; }
        .info-table td { padding: 10px; border: 1px solid #ddd; }
        .status-box { background-color: #d4edda; border: 1px solid #c3e6cb; padding: 12px; margin: 15px 0; }
        .footer { background-color: #f8f9fa; padding: 15px; text-align: center; font-size: 11px; color: #666; border-top: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <h1>OPTIMA - PT Sarana Mitra Luas. Tbk</h1>
        </div>

        <div class="content">
            <p><strong>Kepada Yth. Tim Finance, Accounting & Marketing</strong></p>
            
            <p>Pengiriman unit telah <strong>berhasil sampai di lokasi customer</strong> dengan status <strong>RENTAL ACTIVE</strong>.</p>
            
            <div class="status-box">
                <strong>Status:</strong> Unit aktif rental di lokasi customer
            </div>

            <table class="info-table">
                <tr>
                    <th>Nomor DI</th>
                    <td><?= esc($di_number ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>Nomor SPK</th>
                    <td><?= esc($spk_number ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>Pelanggan</th>
                    <td><?= esc($customer_name ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>Lokasi</th>
                    <td><?= esc($customer_location ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>Waktu Tiba</th>
                    <td><?= isset($arrival_date) ? date('d-m-Y H:i', strtotime($arrival_date)) : date('d-m-Y H:i') ?> WIB</td>
                </tr>
                <tr>
                    <th>Unit</th>
                    <td>
                        <?php if (!empty($units) && is_array($units)): ?>
                            <?php foreach ($units as $idx => $unit): ?>
                                <?= esc($unit['no_unit'] ?? 'N/A') ?><?= isset($units[$idx+1]) ? ', ' : '' ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p style="margin: 0;"><strong>OPTIMA - Integrated ERP System</strong></p>
            <p style="margin: 5px 0 0 0;">PT Sarana Mitra Luas. Tbk</p>
        </div>
    </div>
</body>
</html>
