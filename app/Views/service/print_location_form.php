<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Verifikasi Unit — <?= esc($location['location_name'] ?? 'Lokasi') ?></title>
    <style>
        /* Margin bawah lebih besar agar area konten tidak overlap footer cetak (position:fixed) */
        @page { size: A4; margin: 12mm 12mm 26mm 12mm; }
        body { font-family: Arial, sans-serif; font-size: 9pt; line-height: 1.3; margin: 0; padding: 15px; color: #333; }

        /* Header mengikuti format print_verification / work_order */
        .document-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header-left {
            display: flex;
            align-items: center;
            flex: 1;
        }
        .company-logo {
            width: 120px;
            height: auto;
            margin-right: 20px;
            flex-shrink: 0;
        }
        .company-info { flex: 1; text-align: center; }
        .company-name { font-size: 16pt; font-weight: bold; color: #000; margin-bottom: 3px; }
        .company-tagline { font-size: 10pt; color: #666; font-style: italic; }
        .company-address { font-size: 8.5pt; color: #555; }
        .company-phone { font-size: 8.5pt; color: #555; }
        .header-right { border: 1px solid #aaa; }
        .meta-table { border-collapse: collapse; }
        .meta-table td {
            padding: 4px 8px;
            font-size: 9pt;
            border: 1px solid #aaa;
        }
        .meta-table td:first-child {
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .document-title { text-align: center; font-size: 16pt; font-weight: bold; text-decoration: underline; margin-bottom: 15px; color: #000; }

        /* Panel konten: izinkan tabel terpotong lintas halaman (tanpa page-break-inside: avoid) */
        .content-panel { border: 1px solid #ccc; margin-bottom: 15px; }
        .panel-title { font-size: 9.5pt; font-weight: bold; text-align: center; padding: 6px; border-bottom: 1px solid #ccc; background-color: #f5f5f5; color: #000; }
        .panel-body { padding: 10px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 9pt; }
        .info-table td { border: 1px solid #ccc; padding: 5px 8px; }
        .info-table .lbl { font-weight: bold; background: #f5f5f5; width: 22%; }
        .unit-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 8.5pt; }
        .unit-table th, .unit-table td { border: 1px solid #333; padding: 4px 5px; }
        .unit-table th { background: #e9ecef; font-weight: bold; text-align: center; }
        .fill-line { display: inline-block; border-bottom: 1px solid #333; min-width: 80px; }
        .check-box { display: inline-block; border: 1px solid #333; width: 12px; height: 12px; margin-right: 5px; vertical-align: middle; }
        .signature-row { display: flex; gap: 20px; margin-top: 30px; }
        .sig-cell { flex: 1; text-align: center; border: 1px solid #ccc; padding: 10px; }
        .sig-cell .name-line { border-bottom: 1px solid #333; margin-top: 40px; padding-bottom: 4px; font-weight: bold; }
        .note-block { border: 1px solid #ccc; padding: 8px; margin-bottom: 15px; min-height: 50px; }

        .page-break { page-break-before: always !important; break-before: page !important; }
        /* Print footer (same pattern as DI / SPK) — ditempatkan di zona margin bawah @page */
        .print-footer {
            position: fixed;
            bottom: 5mm;
            left: 10mm;
            right: 10mm;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 2mm;
            padding-bottom: 1mm;
            background: #fff;
            page-break-inside: avoid;
            break-inside: avoid;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            display: none;
        }

        @media print {
            body {
                padding: 0 8px 0 8px !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .print-footer {
                display: block !important;
                z-index: 0;
            }
            /* Hindari baris tabel terbelah di ujung halaman lalu bertabrakan dengan footer */
            .unit-table tbody tr {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            .signature-row,
            .note-block {
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <?php
    $loc       = $location ?? [];
    $units     = $units ?? [];
    $info      = $kontrak_info ?? [];
    $printDate = $print_date ?? date('d-m-Y');
    $noKontrakMasked = $no_kontrak_masked ?? '-';
    $noPoMasked      = $no_po_masked ?? '-';
    $periodeStatus   = $periode_status ?? '';
    $periodeStart    = !empty($info['tanggal_mulai']) ? date('d/m/Y', strtotime($info['tanggal_mulai'])) : '?';
    $periodeEnd      = !empty($info['tanggal_berakhir']) ? date('d/m/Y', strtotime($info['tanggal_berakhir'])) : '?';
    ?>

    <div class="document-header">
        <div class="header-left">
            <img src="<?= base_url('assets/images/company-logo.svg') ?>" class="company-logo" alt="Logo" onerror="this.style.display='none'"/>
            <div class="company-info">
                <div class="company-name">PT. SARANA MITRA LUAS Tbk</div>
                <div class="company-tagline">FORKLIFT & MATERIAL HANDLING EQUIPMENT SOLUTIONS</div>
                <div class="company-address">Jl. Kenari Utama II Blk. C No.03 & 05A, Cibatu, Kec. Cikarang Pusat, 17550</div>
                <div class="company-phone">Phone: (021) - 3973 9988, (021) - 8990 2188</div>
            </div>
        </div>
        <div class="header-right">
            <table class="meta-table">
                <tr>
                    <td>Form</td>
                    <td>Verifikasi Unit Lokasi</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td><?= $printDate ?></td>
                </tr>
            </table>
        </div>
    </div>
    <h1 class="document-title">FORM VERIFIKASI UNIT DI LOKASI</h1>
    <div class="content-panel">
        <div class="panel-title">Data Lokasi & Unit (Kontrak)</div>
        <div class="panel-body">

    <table class="info-table">
        <tr>
            <td class="lbl">Customer</td>
            <td><?= esc($loc['customer_name'] ?? '-') ?></td>
            <td class="lbl">Lokasi</td>
            <td><?= esc($loc['location_name'] ?? '-') ?></td>
        </tr>
        <tr>
            <td class="lbl">Alamat</td>
            <td colspan="3"><?= esc($loc['address'] ?? '-') ?></td>
        </tr>
        <tr>
            <td class="lbl">No. Kontrak</td>
            <td><strong><?= esc($noKontrakMasked) ?></strong></td>
            <td class="lbl">No. PO</td>
            <td><strong><?= esc($noPoMasked) ?></strong></td>
        </tr>
        <tr>
            <td class="lbl">Periode Kontrak</td>
            <td colspan="3"><?= $periodeStart ?> — <?= $periodeEnd ?></td>
        </tr>
        <tr>
            <td class="lbl">Status Periode</td>
            <td colspan="3"><strong><?= esc($periodeStatus) ?></strong></td>
        </tr>
        <tr>
            <td class="lbl">Total Unit (Kontrak)</td>
            <td><strong><?= count($units) ?></strong></td>
            <td class="lbl">Tanggal Audit</td>
            <td><span class="fill-line" style="min-width:120px;">&nbsp;</span></td>
        </tr>
        <tr>
            <td class="lbl">Mekanik</td>
            <td><span class="fill-line" style="min-width:150px;">&nbsp;</span></td>
            <td class="lbl">PIC Lokasi</td>
            <td><?= esc($loc['contact_person'] ?? '') ?> <?= $loc['phone'] ? '(' . esc($loc['phone']) . ')' : '' ?></td>
        </tr>
    </table>

    <?php if (count($units) > 0): ?>
    <table class="unit-table">
        <thead>
            <tr>
                <th style="width:5%">No</th>
                <th style="width:12%">No Unit (DB)</th>
                <th style="width:14%">Serial (DB)</th>
                <th style="width:18%">Merk / Model (DB)</th>
                <th style="width:8%">Spare?</th>
                <th style="width:13%">No Unit (Lapangan)</th>
                <th style="width:13%">Serial (Lapangan)</th>
                <th style="width:8%">Ada?</th>
                <th style="width:9%">Ket.</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($units as $i => $u): ?>
            <tr>
                <td style="text-align:center"><?= $i + 1 ?></td>
                <td><strong><?= esc($u['no_unit'] ?? '-') ?></strong></td>
                <td><?= esc($u['serial_number'] ?? '-') ?></td>
                <td><?= esc(($u['merk_unit'] ?? '') . ' ' . ($u['model_unit'] ?? '')) ?></td>
                <td style="text-align:center"><?= ($u['is_spare'] ?? 0) ? '✓' : '' ?></td>
                <td><span class="fill-line">&nbsp;</span></td>
                <td><span class="fill-line">&nbsp;</span></td>
                <td style="text-align:center">
                    <span class="check-box"></span> Ya<br>
                    <span class="check-box"></span> Tidak
                </td>
                <td><span class="fill-line">&nbsp;</span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="color:#888;font-style:italic;">Tidak ada data unit di lokasi ini.</p>
    <?php endif; ?>

    <div style="margin-bottom:10px;">
        <strong>Temuan / Ketidaksesuaian:</strong>
        <div>
            <span class="check-box"></span> Semua sesuai &nbsp;
            <span class="check-box"></span> Ada ketidaksesuaian (detail di bawah)
        </div>
    </div>

    <div>
        <strong>Jenis Ketidaksesuaian (jika ada):</strong>
        <div style="margin-top:4px;">
            <span class="check-box"></span> Lokasi unit salah &nbsp;
            <span class="check-box"></span> Unit berbeda &nbsp;
            <span class="check-box"></span> Tandai spare &nbsp;
            <span class="check-box"></span> Unit tidak ada
        </div>
    </div>

    <div style="margin-top:10px;">
        <strong>Keterangan:</strong>
        <div class="note-block">&nbsp;</div>
    </div>

    <div class="signature-row">
        <div class="sig-cell">
            <div class="name-line">&nbsp;</div>
            <div>Mekanik / Admin Service</div>
        </div>
        <div class="sig-cell">
            <div class="name-line">&nbsp;</div>
            <div>PIC Lokasi Customer</div>
        </div>
        <div class="sig-cell">
            <div class="name-line">&nbsp;</div>
            <div>Mengetahui (Manager / Kepala)</div>
        </div>
    </div>
        </div>
    </div>

    <!-- Tidak ada halaman verifikasi tambahan di sini.
         Form verifikasi detail unit akan dipanggil terpisah via print_verification*.php -->

    <div class="print-footer" id="printFooter">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div style="text-align: left; font-size: 8px;">
                <strong>PT SARANA MITRA LUAS Tbk</strong><br>
                <span style="color: #888;">Sistem OPTIMA - Service / Unit Audit</span>
            </div>
            <div style="text-align: center; font-size: 8px;">
                <span id="printDate">Tanggal Cetak: <?= date('d/m/Y H:i') ?></span><br>
                <span style="color: #888;">Dokumen ini dibuat secara otomatis oleh sistem OPTIMA</span>
            </div>
            <div style="text-align: right; font-size: 8px;">
                <span id="pageInfo">Halaman <span id="currentPage">1</span></span><br>
                <span style="color: #888;">Lokasi: <?= esc($loc['location_name'] ?? 'Unknown') ?></span>
            </div>
        </div>
    </div>

    <script>
    // Auto print on load & show footer (following DI print behavior)
    window.addEventListener('load', () => {
        const footer = document.getElementById('printFooter');
        if (footer) footer.style.display = 'block';
        window.print();
    });
    </script>
</body>
</html>
