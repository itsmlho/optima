<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP - OPTIMA</title>
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
        .otp-box {
            background: linear-gradient(135deg, #0061f2 0%, #4d8cff 100%);
            color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
        }
        .content {
            margin: 20px 0;
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
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #0061f2;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 0;
        }
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .email-container {
                padding: 20px;
            }
            .otp-code {
                font-size: 28px;
                letter-spacing: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">OPTIMA</div>
            <div class="title">Kode OTP untuk Login</div>
        </div>

        <div class="content">
            <p>Halo <?= esc($user['first_name'] ?? 'Pengguna') ?>,</p>
            
            <p>Kami menerima permintaan login ke akun OPTIMA Anda. Gunakan kode OTP berikut untuk menyelesaikan proses login:</p>

            <div class="otp-box">
                <div style="font-size: 14px; margin-bottom: 10px;">Kode OTP Anda:</div>
                <div class="otp-code"><?= esc($otp_code) ?></div>
                <div style="font-size: 12px; margin-top: 10px; opacity: 0.9;">Kode ini berlaku selama <?= $expire_minutes ?> menit</div>
            </div>

            <div class="info-box">
                <strong>Informasi Penting:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Kode OTP hanya berlaku selama <?= $expire_minutes ?> menit</li>
                    <li>Jangan bagikan kode OTP ini kepada siapa pun</li>
                    <li>Maksimal 3 percobaan verifikasi</li>
                    <li>Jika Anda tidak meminta kode ini, abaikan email ini</li>
                </ul>
            </div>

            <div class="warning-box">
                <strong>⚠️ Keamanan:</strong><br>
                Jika Anda tidak melakukan login, segera ubah password akun Anda dan hubungi administrator sistem di <a href="mailto:<?= esc($support_email) ?>"><?= esc($support_email) ?></a>.
            </div>

            <p style="margin-top: 20px;">
                Masukkan kode OTP di halaman verifikasi untuk melanjutkan login.
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

