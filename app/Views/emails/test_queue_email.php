<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - <?= esc($app_name) ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333333; background-color: #f4f6f9; margin: 0; padding: 20px; }
        .email-wrapper { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 6px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e1e5eb; }
        .header { padding: 25px 30px; background-color: #ffffff; border-bottom: 3px solid #0056b3; text-align: center; }
        .logo-optima { height: 40px; vertical-align: middle; margin-right: 15px; }
        .logo-sml { height: 40px; vertical-align: middle; margin-left: 15px; border-left: 1px solid #ccc; padding-left: 20px; }
        .content { padding: 30px; }
        .content h2 { color: #0056b3; font-size: 20px; margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .test-box { background-color: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; font-size: 14px; color: #155724; }
        .info-box { background-color: #f8f9fa; border-left: 4px solid #0056b3; padding: 15px; margin: 20px 0; font-size: 14px; }
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
            <h2>Pesan Pengujian Sistem (System Test)</h2>
            <p>Yth. Administrator / IT Support,</p>
            
            <p><?= $message ?></p>
            
            <div class="test-box">
                <strong style="display: block; margin-bottom: 8px;">🚀 Uji Coba Queue Services Berhasil!</strong>
                Surat elektronik ini didistribusikan secara suksesi menggunakan layanan worker background (Queue System), membuktikan integrasi asinkron server beroperasi optimal tanpa isu blocking.
            </div>
            
            <div class="info-box">
                <strong>Bahan Diagnostik (Diagnostic Data):</strong>
                <ul style="margin: 8px 0 0 0; padding-left: 20px; color: #555;">
                    <li><strong>Stempel Waktu (Timestamp):</strong> <?= esc($timestamp) ?></li>
                    <li><strong>Metode Eksekusi:</strong> Latar Belakang / Background Queue Job</li>
                    <li><strong>Lingkungan (Environment):</strong> <?= esc(ENVIRONMENT) ?></li>
                </ul>
            </div>
            
            <p style="margin-top: 25px;">Hormat kami,<br><strong>Sistem Administrator OPTIMA</strong></p>
        </div>
        
        <div class="footer">
            <p><strong>PT SARANA MITRA LUAS Tbk</strong></p>
            <p>OPTIMA</p>
            <p style="margin-top: 15px;">Email ini dihasilkan otomatis oleh sistem. Mohon tidak membalas pesan ini.</p>
        </div>
    </div>
</body>
</html>