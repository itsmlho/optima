<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Otomasi Faktur</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333333; background-color: #f4f6f9; margin: 0; padding: 20px; }
        .email-wrapper { max-width: 650px; margin: 0 auto; background: #ffffff; border-radius: 6px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e1e5eb; }
        .header { padding: 25px 30px; background-color: #ffffff; border-bottom: 3px solid #0056b3; text-align: center; }
        .logo-optima { height: 40px; vertical-align: middle; margin-right: 15px; }
        .logo-sml { height: 40px; vertical-align: middle; margin-left: 15px; border-left: 1px solid #ccc; padding-left: 20px; }
        .content { padding: 30px; }
        .content h2 { color: #0056b3; font-size: 20px; margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; background: #ffffff; border: 1px solid #e1e5eb; font-size: 14px; }
        .info-table th { background: #f8f9fa; color: #555; text-align: left; padding: 12px 15px; width: 40%; border-bottom: 1px solid #e1e5eb; border-right: 1px solid #e1e5eb; }
        .info-table td { padding: 12px 15px; border-bottom: 1px solid #e1e5eb; }
        .info-table tr:last-child th, .info-table tr:last-child td { border-bottom: none; }
        .button { display: inline-block; padding: 12px 25px; background-color: #0056b3; color: #ffffff !important; text-decoration: none; border-radius: 4px; font-weight: 600; margin: 15px 0; text-align: center; }
        .button:hover { background-color: #004494; }
        .alert-box { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; font-size: 14px; }
        .note { margin-top: 25px; padding: 15px; background: #f8f9fa; border-left: 4px solid #0056b3; font-size: 13px; color: #555; }
        .footer { background-color: #f8f9fa; padding: 20px 30px; text-align: center; font-size: 12px; color: #6c757d; border-top: 1px solid #e1e5eb; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <img src="<?= base_url('assets/images/logo-optima.png') ?>" alt="OPTIMA" class="logo-optima">
            <img src="<?= base_url('assets/images/company-logo.png') ?>" alt="SML" class="logo-sml">
        </div>

        <div class="content">
            <h2>Pemberitahuan Otomasi Faktur (Invoice)</h2>
            <p><strong>Yth. Tim Accounting / Finance,</strong></p>
            
            <p>Sistem ERP OPTIMA telah secara otomatis meng-generate draft faktur tagihan (invoice) baru yang siap untuk Anda proses lebih lanjut dalam menyiapkan dokumen kepada pelanggan.</p>
            
            <table class="info-table">
                <tr>
                    <th>Nomor Invoice</th>
                    <td><strong><?= esc($invoice['invoice_number'] ?? 'N/A') ?></strong></td>
                </tr>
                <tr>
                    <th>Pelanggan</th>
                    <td><strong><?= esc($customer_name ?? 'N/A') ?></strong></td>
                </tr>
                <tr>
                    <th>Nomor PO/Kontrak</th>
                    <td><?= esc($contract_number ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>Nominal Tagihan</th>
                    <td><strong style="color: #28a745; font-size: 15px;">Rp <?= number_format($invoice['total_amount'] ?? 0, 0, ',', '.') ?></strong></td>
                </tr>
                <tr>
                    <th>Nomor DI (Delivery)</th>
                    <td><?= esc($di['di_number'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>Tgl. Selesai DI</th>
                    <td><?= isset($di['completed_at']) ? date('d F Y', strtotime($di['completed_at'])) : 'N/A' ?></td>
                </tr>
                <tr>
                    <th>Waktu Generate</th>
                    <td><?= isset($invoice['created_at']) ? date('d F Y H:i', strtotime($invoice['created_at'])) : date('d F Y H:i') ?> WIB</td>
                </tr>
            </table>
            
            <div class="alert-box">
                <strong>⚠️ Tindakan Diperlukan:</strong>
                <ol style="margin: 10px 0 0 0; padding-left: 20px; color: #555;">
                    <li>Lakukan verifikasi kembali nominal dan detail pada sistem.</li>
                    <li>Lengkapi faktur dengan dokumen pengantar (BAST, lembar konfirmasi, dsb).</li>
                    <li>Cetak dan persiapkan pengiriman PDF aktual kepada pelanggan.</li>
                    <li>Mutakhirkan (Update) status pengiriman faktur di modul OPTIMA.</li>
                </ol>
            </div>
            
            <div style="text-align: center;">
                <a href="<?= base_url('finance/invoices/' . ($invoice['id'] ?? '')) ?>" class="button">Buka Detail Faktur di Sistem</a>
            </div>
            
            <div class="note">
                <strong>Catatan Sistem:</strong> Faktur tagihan ini otomatis diterbitkan 30 hari sesudah status pengangkutan/DO diselesaikan, merujuk pada regulasi SOP penagihan. Harap pastikan dokumentasi sesuai sebelum pendistribusian akhir ke pelanggan.
            </div>
            
            <p style="margin-top: 25px;">Hormat kami,<br><strong>Sistem Administrator OPTIMA</strong></p>
        </div>
        
        <div class="footer">
            <p><strong>PT SARANA MITRA LUAS Tbk</strong></p>
            <p>OPTIMA - Enterprise Resource Planning System</p>
            <p style="margin-top: 15px;">Email ini dihasilkan otomatis oleh sistem. Mohon tidak membalas pesan ini.</p>
        </div>
    </div>
</body>
</html>
