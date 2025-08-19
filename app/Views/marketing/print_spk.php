<?php
$spk = $spk ?? [];
$s   = $spesifikasi ?? [];
$status = strtoupper((string)($spk['status'] ?? $spk['status_spk'] ?? ''));
$placeholder = ($status === 'SUBMITTED');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK (Persiapan Unit)</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.1/css/bootstrap.min.css" integrity="sha512-Ez0cGzNzHR1tYAv56860NLspgUGuQw16GiOOp/I2LuTmpSK9xDXlgJz3XN4cnpXWDmkNBKXR/VDMTCnAaEooxA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @page { size: A4; margin: 8mm; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color:#222; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #9aa1a7; padding: .3rem .4rem; vertical-align: top; }
        .muted { color:#666; }
        .sig { text-align:center; }
        .sig-stamp { 
            transform: rotate(-15deg); 
            opacity: 0.7; 
            font-size: 10px; 
            color: #dc2626; 
            border: 2px solid #dc2626; 
            padding: 5px 10px; 
            border-radius: 5px; 
            display: inline-block; 
            margin: 5px 0; 
        }
        .sig-name { font-weight: bold; color: #111; }
        .title { font-size: 16px; font-weight: bold; text-align:center; margin-bottom:2px; }
        .subtitle { font-size: 15px; text-align:center; color:#555; margin-bottom:8px; }
        .k-box { height: 16px; width: 16px; border:1px solid #999; display:inline-block; margin-right:2px; }
        .dotted { color:#333; }
        .label { color:#374151; }
        .val   { color:#111827; font-weight: 600; }
        .grid-2 td { width: 25%; }
        .no-border td { border:none !important; }
        .logo { max-height: 50px; }
    </style>
</head>
<body onload="window.print()" onafterprint="window.close()">

<div class="container-fluid">
    <div class="row align-items-center mb-2">
        <div class="col-6 d-flex align-items-center">
            <img src="<?= base_url('assets/images/company-logo.svg') ?>" class="logo me-2" alt="logo"/>
                    </div>
                        <div class="col-6 text-end small text-muted">
                            <?php if (!empty($spk['created_at'])): ?>Created at: <?= esc($spk['created_at']) ?><br><?php endif; ?>
                            <?php if (!empty($spk['updated_at'])): ?>Updated at: <?= esc($spk['updated_at']) ?><?php endif; ?>
                        </div>
                    </div>

                    <div class="title">PT. SARANA MITRA LUAS</div>
                    <div class="subtitle">SPK ( Persiapan Unit )</div>

                    <div class="row mb-1">
                        <div class="col-6"><span class="label">No SPK:</span> <span class="val"><?= esc($spk['nomor_spk'] ?? $spk['no_spk'] ?? '-') ?></span></div>
                        <div class="col-6"><span class="label">Kontrak/PO:</span> <span class="val"><?= esc($spk['po_kontrak_nomor'] ?? $spk['kontrak_no'] ?? '-') ?></span></div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-6"><span class="label">Pelanggan:</span> <span class="val"><?= esc($spk['pelanggan'] ?? $spk['customer_name'] ?? '-') ?></span></div>
                        <div class="col-6"><span class="label">Lokasi:</span> <span class="val"><?= esc($spk['lokasi'] ?? '-') ?></span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><span class="label">PIC:</span> <span class="val"><?= esc($spk['pic'] ?? '-') ?></span></div>
                        <div class="col-6"><span class="label">Kontak:</span> <span class="val"><?= esc($spk['kontak'] ?? '-') ?></span></div>
                    </div>

                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:5%">No.</th>
                                <th style="width:45%">Unit</th>
                                <th style="width:50%">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center align-middle">1.</td>
                                <td class="align-middle"><strong>Delivery Plan :</strong></td>
                                <td><?= esc($spk['delivery_plan'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td class="text-center align-top">2.</td>
                                <td class="align-top"><strong>Equipment :</strong>
                                    <div class="mt-2">
                                        <div>- Tipe (Jenis)</div>
                                        <div>- Merk Unit</div>
                                        <div>- Valve</div>
                                        <div>- Baterai (Jenis)</div>
                                        <div>- Attachment (Tipe)</div>
                                        <div>- Roda</div>
                                        <div>- Departemen</div>
                                        <div>- Kapasitas</div>
                                        <div>- Mast</div>
                                        <div>- Ban</div>
                                    </div>
                                </td>
                                <td class="align-top">
                                    <?php 
                                        $unit = $s['selected']['unit'] ?? null; 
                                        // Debug: show data structure in HTML comments
                                        echo '<!-- DEBUG STATUS: ' . $status . ' -->';
                                        echo '<!-- DEBUG PLACEHOLDER: ' . ($placeholder ? 'true' : 'false') . ' -->';
                                        echo '<!-- DEBUG SPK KEYS: ' . implode(', ', array_keys($spk)) . ' -->';
                                        echo '<!-- DEBUG USER FIELDS: created_by=' . ($spk['created_by'] ?? 'null') . ', created_by_name=' . ($spk['created_by_name'] ?? 'null') . ', marketing_name=' . ($spk['marketing_name'] ?? 'null') . ' -->';
                                        echo '<!-- DEBUG SESSION: user_name=' . (session()->get('user_name') ?? 'null') . ', username=' . (session()->get('username') ?? 'null') . ', nama=' . (session()->get('nama') ?? 'null') . ' -->';
                                        echo '<!-- DEBUG SPESIFIKASI KEYS: ' . implode(', ', array_keys($s)) . ' -->';
                                    ?>
                                    <div class="mt-2">
                                        <br />
                                        <div class="val"><?= esc($s['jenis_unit'] ?? ($unit['jenis_unit'] ?? '..............................')) ?></div>
                                        <div class="val"><?= esc($s['merk_unit'] ?? ($unit['merk_unit'] ?? '..............................')) ?></div>
                                        <div class="val"><?= esc($s['valve_id_name'] ?? $s['valve_id'] ?? '..............................') ?></div>
                                        <div class="val"><?= esc($s['jenis_baterai'] ?? ($unit['jenis_baterai'] ?? '..............................')) ?></div>
                                        <div class="val"><?= esc($s['attachment_tipe'] ?? ($s['selected']['attachment']['tipe'] ?? '..............................')) ?></div>
                                        <div class="val"><?= esc($s['roda_id_name'] ?? $s['roda_id'] ?? '..............................') ?></div>
                                        <div class="val"><?= esc($s['departemen_id_name'] ?? $s['departemen_id'] ?? '..............................') ?></div>
                                        <div class="val"><?= esc($s['kapasitas_id_name'] ?? $s['kapasitas_id'] ?? '..............................') ?></div>
                                        <div class="val"><?= esc($s['mast_id_name'] ?? $s['mast_id'] ?? '..............................') ?></div>
                                        <div class="val"><?= esc($s['ban_id_name'] ?? $s['ban_id'] ?? '..............................') ?></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center align-middle">3.</td>
                                <td class="align-middle"><strong>Aksesoris</strong></td>
                                <td class="val"><?= esc((!empty($s['aksesoris']) && is_array($s['aksesoris'])) ? implode(', ', $s['aksesoris']) : '..............................') ?></td>
                            </tr>
                            <tr>
                                <td class="text-center align-top">4.</td>
                                <td class="align-top"><strong>Processing</strong>
                                    <div class="mt-2">
                                        
                                        <div>Kesiapan Unit</div>
                                        <div>Kesiapan Attachment / Fabrikasi</div>
                                        <div>Kesiapan Painting</div>
                                        <div>PDI Pengecekan</div>
                                    </div>
                                </td>
                                <td class="align-top">
                                    <div class="fw-bold mb-1">Estimasi Tanggal</div>
                                    <div>
                                        <div class="d-flex align-items-center"><?= esc($spk['persiapan_unit_estimasi_mulai'] ?? '') ?> - <?= esc($spk['persiapan_unit_estimasi_selesai'] ?? '') ?></div>
                                        <div class="d-flex align-items-center"><?= esc($spk['fabrikasi_estimasi_mulai'] ?? '') ?> - <?= esc($spk['fabrikasi_estimasi_selesai'] ?? '') ?></div>
                                        <div class="d-flex align-items-center"><?= esc($spk['painting_estimasi_mulai'] ?? '') ?> - <?= esc($spk['painting_estimasi_selesai'] ?? '') ?></div>
                                        <div class="d-flex align-items-center"><?= esc($spk['pdi_estimasi_mulai'] ?? '') ?> - <?= esc($spk['pdi_estimasi_selesai'] ?? '') ?></div>

                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mb-2 fw-bold">Prepared Detail :</div>
                    <?php
                        $unit = $s['selected']['unit'] ?? null; 
                        $attachment = $s['selected']['attachment'] ?? null;

                        $summaryLeft = [
                            ['ID Unit', $unit['no_unit'] ?? ''],
                            ['Merk', $unit['merk_unit'] ?? ''],
                            ['Jenis Unit', $unit['jenis_unit'] ?? $s['jenis_unit'] ?? ''],
                            ['Kapasitas', $unit['kapasitas_name'] ?? $s['kapasitas_id_name'] ?? ''],
                            ['SN Attachment', $attachment['sn_attachment_formatted'] ?? ''],
                            ['SN Baterai', $unit['sn_baterai_formatted'] ?? ''],
                            ['Valve', $s['valve_id_name'] ?? ''],
                            ['Aksesoris', (!empty($spk['persiapan_aksesoris_tersedia']) ? $spk['persiapan_aksesoris_tersedia'] : (is_array($s['aksesoris'] ?? null) ? implode(', ', $s['aksesoris']) : ($s['aksesoris'] ?? '')))],
                        ];
                        $summaryRight = [
                            ['Serial Number', $unit['serial_number'] ?? ''],
                            ['Model', $unit['model_unit'] ?? ''],
                            ['Tipe Unit', $unit['tipe_jenis'] ?? $s['tipe_jenis'] ?? ''],
                            ['Mast', $s['mast_id_name'] ?? ''],
                            ['SN Mast', $unit['sn_mast_formatted'] ?? ''],
                            ['SN Charger', $unit['sn_charger_formatted'] ?? ''],
                            ['Roda', $s['roda_id_name'] ?? ''],
                            ['Ban', $s['ban_id_name'] ?? ''],
                        ];
                    ?>
                    <table class="table grid-2">
                        <tbody>
                            <?php for($i=0;$i<count($summaryLeft);$i++): ?>
                            <tr>
                                <td class="label"><?= esc($summaryLeft[$i][0]) ?></td>
                                <td class="val"><?= esc($placeholder ? '..............................' : ($summaryLeft[$i][1] ?: '')) ?></td>
                                <td class="label"><?= esc($summaryRight[$i][0]) ?></td>
                                <td class="val"><?= esc($placeholder ? '..............................' : ($summaryRight[$i][1] ?: '')) ?></td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>

                    <div class="small text-muted mb-3">
                        <strong>Catatan:</strong> 
                        <?php if (!empty($spk['pdi_catatan'])): ?>
                            <span class="text-dark"><?= esc($spk['pdi_catatan']) ?></span>
                        <?php else: ?>
                            ..........................
                        <?php endif; ?>
                    </div>

                    <div class="row mt-2">
                        <div class="col sig">
                            <div class="muted">Marketing</div>
                            <?php 
                                // Get current user data for signature
                                $currentUser = session()->get('user_name') ?? session()->get('username') ?? session()->get('nama') ?? null;
                                $createdBy = $spk['created_by_name'] ?? $spk['created_by'] ?? $spk['marketing_name'] ?? $currentUser;
                                
                                if ($createdBy && $createdBy !== '') {
                                    echo '<div class="sig-stamp">APPROVED</div>';
                                    echo '<br/>';
                                    echo '<div class="sig-name">(' . esc($createdBy) . ')</div>';
                                } else {
                                    echo '<br/><br/>';
                                    echo '<div>(.........................)</div>';
                                }
                            ?>
                        </div>
                        <div class="col sig">
                            <div class="muted">Bag.Persiapan Unit</div>
                            <?php if (!empty($spk['persiapan_unit_tanggal_approve'])): ?>
                                <div class="sig-stamp">APPROVED</div>
                                <br/>
                                <div class="sig-name">(<?= esc($spk['persiapan_unit_mekanik'] ?? '') ?>)</div>
                            <?php else: ?>
                                <br/><br/>
                                <div>(..........................)</div>
                            <?php endif; ?>
                        </div>
                        <div class="col sig">
                            <div class="muted">Bag.Fabrikasi</div>
                            <?php if (!empty($spk['fabrikasi_tanggal_approve'])): ?>
                                <div class="sig-stamp">APPROVED</div>
                                <br/>
                                <div class="sig-name">(<?= esc($spk['fabrikasi_mekanik'] ?? '') ?>)</div>
                            <?php else: ?>
                                <br/><br/>
                                <div>(..........................)</div>
                            <?php endif; ?>
                        </div>
                        <div class="col sig">
                            <div class="muted">Bag.Painting</div>
                            <?php if (!empty($spk['painting_tanggal_approve'])): ?>
                                <div class="sig-stamp">APPROVED</div>
                                <br/>
                                <div class="sig-name">(<?= esc($spk['painting_mekanik'] ?? '') ?>)</div>
                            <?php else: ?>
                                <br/><br/>
                                <div>(..........................)</div>
                            <?php endif; ?>
                        </div>
                        <div class="col sig">
                            <div class="muted">Bag. PDI Pengecekan</div>
                            <?php if (!empty($spk['pdi_tanggal_approve'])): ?>
                                <div class="sig-stamp">APPROVED</div>
                                <br/>
                                <div class="sig-name">(<?= esc($spk['pdi_mekanik'] ?? '') ?>)</div>
                            <?php else: ?>
                                <br/><br/>
                                <div>(..........................)</div>
                            <?php endif; ?>
                        </div>
    </div>

</div>

</body>
</html>

