<?php
/**
 * Flyer A4 panduan satpam untuk konfirmasi surat jalan.
 *
 * @var string $title
 * @var string $companyName
 * @var string $smlLogoUrl
 * @var string $optimaLogoUrl
 * @var string $qrImageUrl
 * @var string $guardPageUrl
 */
$title       = $title ?? 'Panduan Satpam - Konfirmasi Surat Jalan';
$companyName = $companyName ?? 'PT Sarana Mitra Luas Tbk';
$smlLogoUrl  = $smlLogoUrl ?? base_url('assets/images/company-logo.svg');
$optimaLogoUrl = $optimaLogoUrl ?? base_url('assets/images/logo-optima.png');
$qrImageUrl  = $qrImageUrl ?? base_url('assets/images/surat-jalan-qr-satpam.png');
$guardPageUrl = $guardPageUrl ?? base_url('surat-jalan');
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title) ?></title>
    <style>
        :root {
            --ink: #0f172a;
            --muted: #475569;
            --line: #cbd5e1;
            --blue: #0b5ed7;
            --blue-soft: #e8f1fc;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 14px;
            background: #e2e8f0;
            color: var(--ink);
            font-family: "Segoe UI", Arial, sans-serif;
        }
        .sheet {
            max-width: 210mm;
            margin: 0 auto;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 4px 30px rgba(2, 6, 23, 0.12);
            border: 1px solid #dbe3ef;
            padding: 10mm;
        }
        .noprint { margin-bottom: 10px; }
        .noprint button {
            border: 1px solid #94a3b8;
            background: #fff;
            color: #0f172a;
            border-radius: 8px;
            padding: 8px 14px;
            font-weight: 600;
            cursor: pointer;
        }
        .noprint button.primary {
            background: var(--blue);
            color: #fff;
            border-color: var(--blue);
        }
        .header {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 10px 12px;
            background: linear-gradient(180deg, #f8fafc 0%, #eef3fa 100%);
        }
        .brand-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .brand-left,
        .brand-right {
            width: 130px;
            display: flex;
            align-items: center;
        }
        .brand-left {
            justify-content: flex-start;
        }
        .brand-right {
            justify-content: flex-end;
        }
        .brand-left img,
        .brand-right img {
            height: 42px;
            width: auto;
            object-fit: contain;
        }
        .brand-right .optima-logo {
            height: 36px;
        }
        .brand-center {
            flex: 1;
            min-width: 0;
            text-align: center;
        }
        .brand-text {
            min-width: 0;
        }
        .brand-text .company {
            font-size: 11pt;
            font-weight: 700;
            letter-spacing: 0.02em;
        }
        .brand-text .subtitle {
            color: var(--muted);
            font-size: 8pt;
            margin-top: 2px;
        }
        .title-box {
            margin-top: 10px;
            text-align: center;
            border-top: 2px double #334155;
            border-bottom: 2px double #334155;
            padding: 8px 4px;
        }
        .title-box h1 {
            margin: 0;
            font-size: 18pt;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            font-family: "Times New Roman", serif;
        }
        .title-box p {
            margin: 4px 0 0;
            font-size: 9pt;
            color: var(--muted);
        }
        .grid {
            display: grid;
            grid-template-columns: 1.35fr 1fr;
            gap: 14px;
            margin-top: 12px;
        }
        .panel {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 10px 12px;
            background: #fff;
        }
        .section-title {
            font-size: 8pt;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
            margin: 0 0 8px;
            padding-left: 6px;
            border-left: 3px solid var(--blue);
        }
        ol.steps {
            margin: 0;
            padding-left: 18px;
            font-size: 10pt;
            line-height: 1.5;
        }
        ol.steps li { margin-bottom: 8px; }
        .note {
            margin-top: 10px;
            font-size: 8.5pt;
            color: #854d0e;
            border: 1px solid #facc15;
            background: #fef9c3;
            border-radius: 8px;
            padding: 8px 10px;
        }
        .qr-wrap {
            text-align: center;
        }
        .qr-card {
            border: 1px solid #b6c4d8;
            border-radius: 12px;
            padding: 10px;
            background: #fff;
        }
        .qr-card img {
            width: 100%;
            max-width: 280px;
            height: auto;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
        }
        .qr-label {
            margin-top: 8px;
            font-weight: 700;
            font-size: 10pt;
        }
        .url {
            margin-top: 6px;
            font-size: 8pt;
            color: var(--muted);
            word-break: break-all;
        }
        .footer {
            margin-top: 12px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: var(--blue-soft);
            padding: 8px 10px;
            font-size: 9pt;
            color: #1e293b;
        }
        .footer strong { color: #0b3f8f; }

        @media print {
            @page { size: A4 portrait; margin: 8mm; }
            body { background: #fff; padding: 0; }
            .sheet {
                border: none;
                border-radius: 0;
                box-shadow: none;
                max-width: none;
                margin: 0;
                padding: 0;
            }
            .noprint { display: none !important; }
        }
        @media (max-width: 640px) {
            .brand-row {
                flex-wrap: wrap;
                justify-content: center;
                gap: 8px;
            }
            .brand-left,
            .brand-right {
                width: auto;
            }
            .brand-center {
                order: 3;
                width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="sheet">
    <div class="noprint">
        <button class="primary" onclick="window.print()">Cetak Poster</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    <header class="header">
        <div class="brand-row">
            <div class="brand-left">
                <img src="<?= esc($smlLogoUrl) ?>" alt="Logo SML" onerror="this.style.display='none'">
            </div>
            <div class="brand-center">
                <div class="brand-text">
                    <div class="company"><?= esc($companyName) ?></div>
                    <div class="subtitle">Panduan resmi petugas keamanan gerbang</div>
                </div>
            </div>
            <div class="brand-right">
                <img src="<?= esc($optimaLogoUrl) ?>" alt="Logo OPTIMA" class="optima-logo" onerror="this.style.display='none'">
            </div>
        </div>
        <div class="title-box">
            <h1>Panduan Update Surat Jalan</h1>
            <p>Update Surat Jalan (SJ) di aplikasi OPTIMA</p>
        </div>
    </header>

    <section class="grid">
        <div class="panel">
            <h2 class="section-title">Langkah Cepat</h2>
            <ol class="steps">
                <li><strong>Scan QR</strong> di samping untuk buka halaman konfirmasi Surat Jalan.</li>
                <li>Masukkan <strong>No. SJ</strong> dan <strong>Kode Verifikasi</strong> dari gudang.</li>
                <li>Periksa data kendaraan: <strong>driver, no. kendaraan, jenis kendaraan</strong>.</li>
                <li>Jika data berbeda dari kondisi lapangan, pilih <strong>Tidak sesuai</strong> lalu isi koreksi.</li>
                <li>Isi checklist barang sesuai titik rute:
                    <ul>
                        <li><strong>Barang ada</strong> = masih ikut di kendaraan</li>
                        <li><strong>Drop</strong> = diturunkan/diterima di titik ini</li>
                        <li><strong>Tidak ada</strong> = tidak ikut pengiriman</li>
                    </ul>
                </li>
                <li>Pilih <strong>Titik rute aktif</strong> (yang disorot sistem) dan klik <strong>Simpan konfirmasi</strong>.</li>
            </ol>
            <div class="note">
                <strong>Penting:</strong> konfirmasi harus mengikuti urutan rute pada sistem. Jika salah titik, data tidak bisa disimpan.
            </div>
        </div>

        <div class="panel qr-wrap">
            <h2 class="section-title">Scan Untuk Buka Halaman SJ</h2>
            <div class="qr-card">
                <img src="<?= esc($qrImageUrl) ?>" alt="QR Konfirmasi Surat Jalan Satpam">
                <div class="qr-label">Scan QR ini</div>
                <div class="url"><?= esc($guardPageUrl) ?></div>
            </div>
        </div>
    </section>

    <div class="footer">
        <strong>Catatan:</strong> Jika No SJ atau kode verifikasi tidak sesuai, silakan hubungi pembuat surat jalan.</strong>
    </div>
</div>
</body>
</html>

