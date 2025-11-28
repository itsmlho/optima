<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - <?= esc($app_name) ?></title>
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
            font-size: 16px;
            line-height: 1.6;
        }
        .test-box {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 30px 0;
        }
        .test-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #0061f2;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">OPTIMA</div>
            <div class="title"><?= esc($title) ?></div>
        </div>
        
        <div class="test-box">
            <div class="test-icon">✅</div>
            <h3>Queue System Test Berhasil!</h3>
            <p>Email ini berhasil dikirim melalui sistem antrian (queue) background.</p>
        </div>
        
        <div class="content">
            <p>Halo,</p>
            
            <p><?= $message ?></p>
            
            <p>Ini adalah tes untuk memverifikasi bahwa sistem queue email berfungsi dengan baik. Email ini diproses di background dan dikirim melalui sistem antrian file-based yang optimal untuk shared hosting.</p>
        </div>
        
        <div class="info-box">
            <strong>Informasi Test:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Waktu dikirim: <?= esc($timestamp) ?></li>
                <li>Sistem: File-based Queue (100% gratis)</li>
                <li>Processing: Background job</li>
                <li>Hosting: Optimal untuk shared hosting</li>
            </ul>
        </div>
        
        <div class="footer">
            <p><strong><?= esc($app_name) ?></strong></p>
            <p>Sistem manajemen terintegrasi PT Sarana Mitra Luas Tbk</p>
            <p><small>Email ini dikirim otomatis oleh sistem. Jangan balas email ini.</small></p>
        </div>
    </div>
</body>
</html>