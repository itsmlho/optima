<?php
$spk = $spk ?? [];
$s   = $spesifikasi ?? [];
$k   = $kontrak_spesifikasi ?? []; // Data kontrak untuk Equipment section
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
        .title { font-size: 16px; font-weight: bold; margin:0; }
        .subtitle { font-size: 15px; color:#555; margin:0; }
        .k-box { height: 16px; width: 16px; border:1px solid #999; display:inline-block; margin-right:2px; }
        .dotted { color:#333; }
        .label { color:#374151; }
        .val   { color:#111827; font-weight: 600; }
        .grid-2 td { width: 25%; }
        .no-border td { border:none !important; }
        .logo { max-height: 46px; }
        .header { display:grid; grid-template-columns:auto 1fr auto; align-items:center; column-gap:10px; }
        .header-center { text-align:center; }
        .header-meta { font-size:10px; color:#6b7280; text-align:right; }
        /* Extra print-friendly blocks for multiple units */
        .unit-card { border:1px solid #9aa1a7; padding:8px; margin-bottom:10px; }
        .unit-title { background:#f8fafc; font-weight:bold; padding:4px 6px; border-bottom:1px solid #9aa1a7; margin:-8px -8px 8px; }
    </style>
</head>
<body onload="window.print()" onafterprint="window.close()">

<div class="container-fluid">
    <div class="header mb-2">
        <img src="<?= base_url('assets/images/company-logo.svg') ?>" class="logo" alt="logo"/>
        <div class="header-center">
            <div class="title">PT. SARANA MITRA LUAS</div>
            <div class="subtitle">SPK ( Persiapan Unit )</div>
            <br/>
        </div>
        <div class="header-meta">
            <?php if (!empty($spk['created_at'])): ?>Created: <?= esc($spk['created_at']) ?><br><?php endif; ?>
            <?php if (!empty($spk['updated_at'])): ?>Updated: <?= esc($spk['updated_at']) ?><?php endif; ?>
        </div>
    </div>

                    <div class="row mb-1">
                        <div class="col-6"><span class="label">No SPK:</span> <span class="val"><?= esc($spk['nomor_spk'] ?? $spk['no_spk'] ?? '-') ?></span></div>
                        <div class="col-6"><span class="label">Kontrak/PO:</span> <span class="val"><?= esc($spk['po_kontrak_nomor'] ?? $spk['kontrak_no'] ?? '-') ?></span></div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-6"><span class="label">Nama Perusahaan:</span> <span class="val"><?= esc($spk['pelanggan'] ?? $spk['customer_name'] ?? '-') ?></span></div>
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
                                 <th style="width:35%">Unit</th>
                                 <th style="width:65%">Keterangan</th>
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
                                        <div>- Total Unit</div>
                                        <div>- Merk & Jenis Unit</div>
                                        <div>- Baterai & Charger</div>
                                        <div>- Departemen</div>
                                        <div>- Kapasitas</div>
                                        <div>- Attachment</div>
                                        <div>- Roda & Ban</div>
                                        <div>- Mast</div>
                                        <div>- Valve</div>
                                    </div>
                                </td>
                                <td class="align-top">
                                    <?php 
                                        // Debug: show data structure in HTML comments
                                        echo '<!-- DEBUG STATUS: ' . $status . ' -->';
                                        echo '<!-- DEBUG PLACEHOLDER: ' . ($placeholder ? 'true' : 'false') . ' -->';
                                        echo '<!-- DEBUG SPK KEYS: ' . implode(', ', array_keys($spk)) . ' -->';
                                        echo '<!-- DEBUG KONTRAK KEYS: ' . implode(', ', array_keys($k)) . ' -->';
                                        echo '<!-- DEBUG SPESIFIKASI KEYS: ' . implode(', ', array_keys($s)) . ' -->';
                                        
                                        // Use kontrak data for Equipment section (data permintaan marketing)
                                        $jumlahUnit = $k['jumlah_dibutuhkan'] ?? $spk['jumlah_unit'] ?? '';
                                        $merkUnit = $k['merk_unit'] ?? '';
                                        $modelUnit = $k['model_unit'] ?? '';
                                        $jenisUnit = $k['kontrak_jenis_unit'] ?? '';
                                        $tipeUnit = $k['kontrak_tipe_unit'] ?? $k['tipe_jenis'] ?? '';
                                        $kapasitasName = $k['kontrak_kapasitas_name'] ?? '';
                                        $departemenName = $k['kontrak_departemen_name'] ?? '';
                                        $mastName = $k['kontrak_mast_name'] ?? '';
                                        $rodaName = $k['kontrak_roda_name'] ?? '';
                                        $banName = $k['kontrak_ban_name'] ?? '';
                                        $valveName = $k['kontrak_valve_name'] ?? '';
                                        
                                        // Attachment dari kontrak (bukan dari spesifikasi SPK)
                                        $attachmentType = $k['attachment_tipe'] ?? '';
                                        
                                        // Battery dan Charger dari kontrak
                                        $batteryType = $k['jenis_baterai'] ?? '';
                                        $chargerType = $k['kontrak_charger_model'] ?? '';
                                        
                                        // Aksesoris dari kontrak
                                        $aksesorisKontrak = '';
                                        if (!empty($k['aksesoris'])) {
                                            if (is_array($k['aksesoris'])) {
                                                $aksesorisKontrak = implode(', ', $k['aksesoris']);
                                            } else {
                                                $aksesorisKontrak = (string)$k['aksesoris'];
                                            }
                                        }
                                    ?>
                                    <div class="mt-2">
                                        <br />
                                        <div class="val"><?= esc($jumlahUnit ?: '..............................') ?></div>
                                        <div class="val">
                                            <?php
                                            // Combine merk and jenis unit info from kontrak
                                            $brandTypeInfo = [];
                                            if (!empty($merkUnit)) $brandTypeInfo[] = $merkUnit;
                                            if (!empty($modelUnit)) $brandTypeInfo[] = $modelUnit;
                                            if (!empty($jenisUnit) && !in_array($jenisUnit, $brandTypeInfo)) $brandTypeInfo[] = $jenisUnit;
                                            if (!empty($tipeUnit) && !in_array($tipeUnit, $brandTypeInfo)) $brandTypeInfo[] = $tipeUnit;
                                            
                                            echo !empty($brandTypeInfo) ? esc(implode(' ', $brandTypeInfo)) : '..............................';
                                            ?>
                                        </div>
                                        <div class="val"><?php
                                            // Combine battery and charger info from kontrak
                                            $combinedInfo = [];
                                            if (!empty($batteryType)) $combinedInfo[] = $batteryType;
                                            if (!empty($chargerType)) $combinedInfo[] = $chargerType;
                                            
                                            echo !empty($combinedInfo) ? esc(implode(' & ', $combinedInfo)) : '..............................';
                                        ?></div>
                                        <div class="val"><?= esc($departemenName ?: '..............................') ?></div>
                                        <div class="val"><?= esc($kapasitasName ?: '..............................') ?></div>
                                        <div class="val"><?= esc($attachmentType ?: '..............................') ?></div>
                                        <div class="val"><?= esc(($rodaName && $banName) ? $rodaName . ' & ' . $banName : ($rodaName ?: ($banName ?: '..............................'))) ?></div>
                                        <div class="val"><?= esc($mastName ?: '..............................') ?></div>
                                        <div class="val"><?= esc($valveName ?: '..............................') ?></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center align-middle">3.</td>
                                <td class="align-middle"><strong>Aksesoris</strong></td>
                                <td class="val"><?php
                                    // Use aksesoris from kontrak (data permintaan marketing)
                                    $aksText = '..............................';
                                    
                                    // Prioritaskan aksesoris dari kontrak_spesifikasi
                                    if (!empty($k['aksesoris'])) {
                                        if (is_array($k['aksesoris'])) {
                                            $aksText = implode(', ', $k['aksesoris']);
                                        } else if (is_string($k['aksesoris'])) {
                                            // Try to parse JSON string
                                            try {
                                                $aksArray = json_decode($k['aksesoris'], true);
                                                if (is_array($aksArray) && !empty($aksArray)) {
                                                    $aksText = implode(', ', $aksArray);
                                                } else {
                                                    $aksText = $k['aksesoris']; // Use as-is if not an array
                                                }
                                            } catch (Exception $e) {
                                                $aksText = $k['aksesoris']; // Use as-is if parsing fails
                                            }
                                        }
                                    } elseif (!empty($s['aksesoris'])) {
                                        // Fallback ke spesifikasi jika kontrak tidak ada
                                        if (is_array($s['aksesoris'])) {
                                            $aksText = implode(', ', $s['aksesoris']);
                                        } else if (is_string($s['aksesoris'])) {
                                            try {
                                                $aksArray = json_decode($s['aksesoris'], true);
                                                if (is_array($aksArray) && !empty($aksArray)) {
                                                    $aksText = implode(', ', $aksArray);
                                                } else {
                                                    $aksText = $s['aksesoris'];
                                                }
                                            } catch (Exception $e) {
                                                $aksText = $s['aksesoris'];
                                            }
                                        }
                                    } elseif (!empty($spk['persiapan_aksesoris_tersedia'])) {
                                        $aksText = $spk['persiapan_aksesoris_tersedia'];
                                    }
                                    
                                    echo esc($aksText);
                                ?></td>
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

                    <div class="mb-2 fw-bold">Detail Unit yang Disiapkan :</div>
                    <?php $preparedList = $s['prepared_units_detail'] ?? []; ?>
                    <?php if (is_array($preparedList) && count($preparedList) >= 1): ?>
                        <?php foreach ($preparedList as $i => $rowPrepared): ?>
                            <div class="unit-card">
                                <div class="unit-title">Unit <?= ($i + 1) ?><?= isset($rowPrepared['unit_label']) ? ' - '.esc($rowPrepared['unit_label']) : '' ?></div>
                                <?php
                                    // Build left/right summaries similar to single-unit block, with graceful fallbacks
                                    $summaryLeft = [
                                        ['ID Unit', $rowPrepared['unit_label'] ?? (isset($rowPrepared['unit_id']) ? '#'.$rowPrepared['unit_id'] : '')],
                                        ['Jenis Unit', $rowPrepared['jenis_unit'] ?? ($s['jenis_unit'] ?? '')],
                                        ['Departemen', $rowPrepared['departemen_name'] ?? ($s['departemen_id'] ?? '')],
                                        ['Attachment', $rowPrepared['sn_attachment_formatted'] ?? ($rowPrepared['attachment_sn'] ?? '')],
                                        ['Baterai', $rowPrepared['sn_baterai_formatted'] ?? ($rowPrepared['baterai_sn'] ?? '')],
                                        ['Valve', $rowPrepared['valve_id_name'] ?? ($s['valve_id_name'] ?? '')],
                                    ];
                                    $summaryRight = [
                                        ['Serial Number', $rowPrepared['serial_number'] ?? ''],
                                        ['Tipe Unit', $rowPrepared['tipe_jenis'] ?? ($s['tipe_jenis'] ?? '')],
                                        ['Kapasitas', $rowPrepared['kapasitas_name'] ?? ($s['kapasitas_id_name'] ?? '')],
                                        ['Mast', $rowPrepared['mast_id_name'] ?? ($s['mast_id_name'] ?? '')],
                                        ['Charger', $rowPrepared['sn_charger_formatted'] ?? ($rowPrepared['charger_sn'] ?? '')],
                                        ['Roda & Ban', trim(
                                            ($rowPrepared['roda_id_name'] ?? $s['roda_id_name'] ?? '') .
                                            ((!empty($rowPrepared['roda_id_name'] ?? $s['roda_id_name'] ?? '') && !empty($rowPrepared['ban_id_name'] ?? $s['ban_id_name'] ?? '')) ? ' & ' : '') .
                                            ($rowPrepared['ban_id_name'] ?? $s['ban_id_name'] ?? '')
                                        )],
                                    ];
                                ?>
                                <table class="table grid-2">
                                    <tbody>
                                        <?php 
                                            $rows = max(count($summaryLeft), count($summaryRight));
                                            for ($ri = 0; $ri < $rows; $ri++): 
                                                $left = $summaryLeft[$ri] ?? ['', ''];
                                                $right = $summaryRight[$ri] ?? ['', ''];
                                        ?>
                                        <tr>
                                            <td class="label"><?= esc($left[0]) ?></td>
                                            <td class="val"><?= esc($left[1] ?: '') ?></td>
                                            <td class="label"><?= esc($right[0]) ?></td>
                                            <td class="val"><?= esc($right[1] ?: '') ?></td>
                                        </tr>
                                        <?php endfor; ?>
                                        <tr>
                                            <td class="label">Aksesoris</td>
                                            <td class="val" colspan="3">
                                                <?php
                                                    // Prefer per-row accessories if provided; fallback to global SPK/spec
                                                    $aksText = '';
                                                    if (!empty($rowPrepared['aksesoris'])) {
                                                        if (is_array($rowPrepared['aksesoris'])) {
                                                            $aksText = implode(', ', $rowPrepared['aksesoris']);
                                                        } else {
                                                            $aksText = (string) $rowPrepared['aksesoris'];
                                                        }
                                                    } elseif (!empty($s['aksesoris'])) {
                                                        if (is_array($s['aksesoris'])) {
                                                            $aksText = implode(', ', $s['aksesoris']);
                                                        } else {
                                                            $try = json_decode((string)$s['aksesoris'], true);
                                                            $aksText = is_array($try) ? implode(', ', $try) : (string)$s['aksesoris'];
                                                        }
                                                    } elseif (!empty($spk['persiapan_aksesoris_tersedia'])) {
                                                        $aksText = (string) $spk['persiapan_aksesoris_tersedia'];
                                                    }
                                                    echo esc($aksText);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label">Mekanik</td>
                                            <td class="val"><?= esc($rowPrepared['mekanik'] ?? '') ?></td>
                                            <td class="label">Waktu</td>
                                            <td class="val"><?= esc($rowPrepared['timestamp'] ?? '') ?></td>
                                        </tr>
                                        <?php if (!empty($rowPrepared['catatan'])): ?>
                                        <tr>
                                            <td class="label">Catatan</td>
                                            <td class="val" colspan="3"><?= esc($rowPrepared['catatan']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php
                            $unit = $s['selected']['unit'] ?? null; 
                            $attachment = $s['selected']['attachment'] ?? null;

                            $summaryLeft = [
                                ['ID Unit', $unit['no_unit'] ?? ''],
                                ['Jenis Unit', $unit['jenis_unit'] ?? $s['jenis_unit'] ?? ''],
                                ['Kapasitas', $unit['kapasitas_name'] ?? $s['kapasitas_id_name'] ?? ''],
                                ['Attachment', $s['selected']['attachment']['sn_attachment_formatted'] ?? ''],
                                ['Baterai', $s['selected']['battery']['sn_baterai_formatted'] ?? ''],
                                ['Valve', $s['valve_id_name'] ?? ''],
                            ];
                            $summaryRight = [
                                ['Serial Number', $unit['serial_number'] ?? ''],
                                ['Tipe Unit', $unit['tipe_jenis'] ?? $s['tipe_jenis'] ?? ''],
                                ['Mast', $s['mast_id_name'] ?? ''],
                                ['Charger', $s['selected']['charger']['sn_charger_formatted'] ?? ''],
                                ['Roda & Ban', ($s['roda_id_name'] ?? '') . (!empty($s['roda_id_name']) && !empty($s['ban_id_name']) ? ' & ' : '') . ($s['ban_id_name'] ?? '')],
                            ];

                            
                        ?>
                        <table class="table grid-2">
                            <tbody>
                                <?php 
                                    $rows = max(count($summaryLeft), count($summaryRight));
                                    for ($i = 0; $i < $rows; $i++): 
                                        $left = $summaryLeft[$i] ?? ['', ''];
                                        $right = $summaryRight[$i] ?? ['', ''];
                                ?>
                                <tr>
                                    <td class="label"><?= esc($left[0]) ?></td>
                                    <td class="val"><?php 
                                        if ($placeholder) {
                                            echo esc('..............................');
                                        } else {
                                            $value = $left[1];
                                            if (is_callable($value)) {
                                                echo esc($value() ?: '');
                                            } else {
                                                echo esc($value ?: '');
                                            }
                                        }
                                    ?></td>
                                    <td class="label"><?= esc($right[0]) ?></td>
                                    <td class="val"><?= esc($placeholder ? '..............................' : ($right[1] ?: '')) ?></td>
                                </tr>
                                <?php endfor; ?>
                                <!-- Aksesoris row spans across both left and right columns -->
                                <tr>
                                    <td class="label">Aksesoris</td>
                                    <td class="val" colspan="3">
                                        <?php 
                                            if ($placeholder) {
                                                echo esc('..............................');
                                            } else {
                                                // Multiple ways the accessories might be stored
                                                $aksText = '';
                                                if (!empty($s['aksesoris'])) {
                                                    if (is_array($s['aksesoris'])) {
                                                        $aksText = implode(', ', $s['aksesoris']);
                                                    } else if (is_string($s['aksesoris'])) {
                                                        try {
                                                            $aksArray = json_decode($s['aksesoris'], true);
                                                            if (is_array($aksArray) && !empty($aksArray)) {
                                                                $aksText = implode(', ', $aksArray);
                                                            } else {
                                                                $aksText = $s['aksesoris'];
                                                            }
                                                        } catch (Exception $e) {
                                                            $aksText = $s['aksesoris'];
                                                        }
                                                    }
                                                } else if (!empty($spk['persiapan_aksesoris_tersedia'])) {
                                                    $aksText = $spk['persiapan_aksesoris_tersedia'];
                                                }
                                                echo esc($aksText ?: '');
                                            }
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    <?php endif; ?>

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

