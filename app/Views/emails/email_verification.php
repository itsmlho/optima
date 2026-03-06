<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - <?= esc($app_name) ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333333; background-color: #f4f6f9; margin: 0; padding: 20px; }
        .email-wrapper { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 6px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e1e5eb; }
        .header { padding: 25px 30px; background-color: #ffffff; border-bottom: 3px solid #0056b3; text-align: center; }
        .logo-optima { height: 40px; vertical-align: middle; margin-right: 15px; }
        .logo-sml { height: 40px; vertical-align: middle; margin-left: 15px; border-left: 1px solid #ccc; padding-left: 20px; }
        .content { padding: 30px; }
        .content h2 { color: #0056b3; font-size: 20px; margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .button { display: inline-block; padding: 12px 25px; background-color: #0056b3; color: #ffffff !important; text-decoration: none; border-radius: 4px; font-weight: 600; margin: 25px 0; text-align: center; }
        .button:hover { background-color: #004494; }
        .info-box { background-color: #f8f9fa; border-left: 4px solid #0056b3; padding: 15px; margin: 20px 0; font-size: 14px; }
        .link-fallback { word-break: break-all; color: #0056b3; font-size: 13px; margin-top: 10px; background: #f4f6f9; padding: 10px; border-radius: 4px; }
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
            <h2>Verifikasi Pendaftaran Akun Baru</h2>
            <p>Yth. <strong><?= esc($user['first_name']) ?></strong>,</p>
            
            <p>Data registrasi Anda telah berhasil direkam ke dalam sistem <strong><?= esc($app_name) ?></strong>.</p>
            <p>Sebagai langkah pengamanan sistem otorisasi kami, Anda diwajibkan untuk memverifikasi alamat email ini dengan mengklik tombol berikut:</p>
            
            <div style="text-align: center;">
                <a href="<?= esc($verification_link) ?>" class="button">Verifikasi Email Sekarang</a>
            </div>
            
            <p>Jika tombol di atas tidak dapat diklik, silakan salin dan tempel tautan referensi berikut pada peramban web Anda:</p>
            <div class="link-fallback">
                <?= esc($verification_link) ?>
            </div>
            
            <div class="info-box">
                <strong>Catatan Administratif:</strong>
                <ul style="margin: 8px 0 0 0; padding-left: 20px; color: #555;">
                    <li>Tautan verifikasi ini hanya berlaku selama batas waktu 24 jam.</li>
                    <li>Setelah terverifikasi, akun baru masih memerlukan persetujuan Administrator Sistem (Approval) sesuai prosedur Standard Operating Procedure (SOP).</li>
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

