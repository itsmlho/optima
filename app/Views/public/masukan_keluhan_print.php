<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Print QR') ?></title>
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f9;
            color: #1f2937;
        }
        .sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 8mm auto;
            background: #fff;
            border: 1px solid #e5e7eb;
            padding: 12mm 14mm;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }
        .top {
            text-align: center;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 12px 14px;
            background: linear-gradient(180deg, #ffffff 0%, #f9fbff 100%);
        }
        .logos {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-bottom: 12px;
        }
        .logo-sml {
            height: 52px;
            width: auto;
        }
        .logo-optima {
            height: 52px;
            width: auto;
            border-radius: 8px;
        }
        .title {
            margin: 0 0 6px;
            font-size: 34px;
            line-height: 1.2;
            font-weight: 700;
            color: #0d6efd;
        }
        .subtitle {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }
        .note {
            margin: 10px 0 0;
            font-size: 15px;
            color: #4b5563;
        }
        .center-box {
            text-align: center;
            margin: 16mm 0 0;
        }
        .qr {
            width: 330px;
            height: 330px;
            border: 8px solid #f3f4f6;
            border-radius: 12px;
            padding: 8px;
            background: #fff;
        }
        .scan {
            margin-top: 14px;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: .5px;
        }
        .url {
            margin-top: 10px;
            font-size: 13px;
            color: #6b7280;
            word-break: break-all;
        }
        .print-actions {
            position: fixed;
            right: 16px;
            bottom: 16px;
            display: flex;
            gap: 8px;
        }
        .btn {
            border: 0;
            border-radius: 8px;
            padding: 10px 14px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-primary { background: #0d6efd; color: #fff; }
        .btn-light { background: #e5e7eb; color: #111827; }

        @page { size: A4 portrait; margin: 0; }
        @media print {
            body { background: #fff; }
            .sheet {
                margin: 0;
                border: 0;
                width: 210mm;
                min-height: 297mm;
            }
            .print-actions { display: none !important; }
        }
    </style>
</head>
<body>
    <main class="sheet">
        <section class="top">
            <div class="logos">
                <img src="<?= esc($smlLogoUrl ?? '') ?>" alt="Logo SML" class="logo-sml">
                <img src="<?= esc($optimaLogoUrl ?? '') ?>" alt="Logo OPTIMA" class="logo-optima" onerror="this.style.display='none';document.getElementById('optimaWordmark').style.display='inline-block';">
                <span id="optimaWordmark" style="display:none;font-weight:700;color:#0d6efd;font-size:20px;letter-spacing:.5px;">OPTIMA</span>
            </div>
            <h1 class="title">Masukan &amp; Keluh Kesah</h1>
            <p class="subtitle"><?= esc($companyName ?? 'PT Sarana Mitra Luas Tbk') ?></p>
            <p class="note">Silakan scan QR di bawah untuk mengisi masukan atau keluhan secara anonim.</p>
        </section>

        <section class="center-box">
            <img src="<?= esc($qrUrl ?? '') ?>" alt="QR Masukan Keluhan" class="qr">
            <div class="scan">SCAN DI SINI</div>
            <div class="url"><?= esc($formUrl ?? '') ?></div>
        </section>
    </main>

    <div class="print-actions">
        <button type="button" class="btn btn-light" onclick="window.history.back()">Kembali</button>
        <button type="button" class="btn btn-primary" onclick="window.print()">Print</button>
    </div>
</body>
</html>
