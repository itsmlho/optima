<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - OPTIMA</title>
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
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #0061f2;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            color: #0061f2;
            margin-bottom: 10px;
        }
        .title {
            font-size: 24px;
            font-weight: 600;
            color: #212529;
            margin-bottom: 20px;
        }
        .content {
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 14px 28px;
            background: linear-gradient(135deg, #0061f2 0%, #4d8cff 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 600;
            text-align: center;
        }
        .button:hover {
            background: linear-gradient(135deg, #0050d0 0%, #0061f2 100%);
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #0061f2;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box strong {
            color: #0061f2;
        }
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .link-fallback {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            word-break: break-all;
            color: #495057;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .email-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">OPTIMA</div>
            <div class="title">Reset Password</div>
        </div>

        <div class="content">
            <p>Halo <?= esc($user['first_name'] ?? 'Pengguna') ?>,</p>
            
            <p>Kami menerima permintaan untuk mereset password akun OPTIMA Anda. Klik tombol di bawah untuk membuat password baru:</p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="<?= esc($reset_link) ?>" class="button">
                    <i class="fas fa-key" style="margin-right: 8px;"></i>
                    Reset Password
                </a>
            </div>

            <div class="info-box">
                <strong>Informasi Penting:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Link reset password berlaku selama <?= $expire_hours ?> jam</li>
                    <li>Link hanya dapat digunakan sekali</li>
                    <li>Jika Anda tidak meminta reset password, abaikan email ini</li>
                    <li>Password Anda tidak akan berubah sampai Anda membuat password baru</li>
                </ul>
            </div>

            <div class="link-fallback">
                <strong>Jika tombol di atas tidak berfungsi,</strong> salin dan tempel link berikut di browser Anda:<br><br>
                <?= esc($reset_link) ?>
            </div>

            <div class="warning-box">
                <strong>⚠️ Keamanan:</strong><br>
                Jika Anda tidak melakukan permintaan reset password ini, segera hubungi administrator sistem di <a href="mailto:<?= esc($support_email) ?>"><?= esc($support_email) ?></a> dan abaikan email ini. Password akun Anda tetap aman.
            </div>

            <p style="margin-top: 20px;">
                Jika Anda memiliki pertanyaan atau memerlukan bantuan, jangan ragu untuk menghubungi tim support kami.
            </p>
        </div>

        <div class="footer">
            <p><strong><?= esc($app_name ?? 'OPTIMA') ?></strong></p>
            <p>Sistem Manajemen Operasional Terpadu</p>
            <p>PT SARANA MITRA LUAS Tbk</p>
            <p style="margin-top: 15px;">
                Email ini dikirim secara otomatis. Mohon tidak membalas email ini.
            </p>
            <p>
                Jika Anda memiliki pertanyaan, hubungi tim support di <a href="mailto:<?= esc($support_email) ?>"><?= esc($support_email) ?></a>
            </p>
        </div>
    </div>
</body>
</html>

