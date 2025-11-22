<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - <?= esc($app_name) ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #0061f2;
        }
        .header h1 {
            color: #0061f2;
            margin: 0;
            font-size: 28px;
        }
        .content {
            margin: 20px 0;
        }
        .content p {
            margin: 15px 0;
            font-size: 16px;
        }
        .button {
            display: inline-block;
            padding: 15px 30px;
            background-color: #0061f2;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
            text-align: center;
        }
        .button:hover {
            background-color: #0050d0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .link-fallback {
            word-break: break-all;
            color: #0061f2;
            font-size: 12px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?= esc($app_name) ?></h1>
            <p style="color: #666; margin: 5px 0;">Verifikasi Email</p>
        </div>
        
        <div class="content">
            <p>Halo <strong><?= esc($user['first_name']) ?></strong>,</p>
            
            <p>Terima kasih telah mendaftar di sistem <strong><?= esc($app_name) ?></strong>.</p>
            
            <p>Untuk menyelesaikan proses registrasi, silakan verifikasi email Anda dengan mengklik tombol di bawah ini:</p>
            
            <div style="text-align: center;">
                <a href="<?= esc($verification_link) ?>" class="button">Verifikasi Email</a>
            </div>
            
            <div class="link-fallback">
                <p>Jika tombol tidak berfungsi, salin dan buka link berikut di browser Anda:</p>
                <p style="word-break: break-all;"><?= esc($verification_link) ?></p>
            </div>
            
            <div class="warning">
                <p><strong>Penting:</strong></p>
                <p>Link verifikasi ini akan kadaluarsa dalam 24 jam. Setelah email diverifikasi, akun Anda akan menunggu persetujuan admin sebelum dapat digunakan.</p>
            </div>
            
            <p>Jika Anda tidak melakukan registrasi ini, silakan abaikan email ini.</p>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim secara otomatis, mohon jangan membalas email ini.</p>
            <p>Jika ada pertanyaan, hubungi IT Support: <a href="mailto:<?= esc($support_email) ?>"><?= esc($support_email) ?></a></p>
            <p style="margin-top: 20px; color: #999; font-size: 12px;">
                &copy; <?= date('Y') ?> <?= esc($app_name) ?> - PT Sarana Mitra Luas Tbk
            </p>
        </div>
    </div>
</body>
</html>

