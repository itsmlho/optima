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
                                        $unit = $s['selected']['unit'] ?? null; 
                                        // Debug: show data structure in HTML comments
                                        echo '<!-- DEBUG STATUS: ' . $status . ' -->';
                                        echo '<!-- DEBUG PLACEHOLDER: ' . ($placeholder ? 'true' : 'false') . ' -->';
                                        echo '<!-- DEBUG SPK KEYS: ' . implode(', ', array_keys($spk)) . ' -->';
                                        echo '<!-- DEBUG USER FIELDS: created_by=' . ($spk['created_by'] ?? 'null') . ', created_by_name=' . ($spk['created_by_name'] ?? 'null') . ', marketing_name=' . ($spk['marketing_name'] ?? 'null') . ' -->';
                                        echo '<!-- DEBUG SESSION: user_name=' . (session()->get('user_name') ?? 'null') . ', username=' . (session()->get('username') ?? 'null') . ', nama=' . (session()->get('nama') ?? 'null') . ' -->';
                                        echo '<!-- DEBUG SPESIFIKASI KEYS: ' . implode(', ', array_keys($s)) . ' -->';
                                        
                                        // Add debug for battery and charger info
                                        echo '<!-- DEBUG BATTERY MODEL: ' . ($s['baterai_model'] ?? 'null') . ' -->';
                                        echo '<!-- DEBUG BATTERY TYPE: ' . ($s['jenis_baterai'] ?? 'null') . ' -->';
                                        echo '<!-- DEBUG CHARGER MODEL: ' . ($s['charger_model'] ?? 'null') . ' -->';
                                        if (isset($unit)) {
                                            echo '<!-- DEBUG UNIT BATTERY: ' . ($unit['baterai_model'] ?? 'null') . ' -->';
                                            echo '<!-- DEBUG UNIT CHARGER: ' . ($unit['charger_model'] ?? 'null') . ' -->';
                                        }
                                    ?>
                                    <div class="mt-2">
                                        <br />
                                        <div class="val"><?= esc($s['jumlah_unit'] ?? ($unit['jumlah_unit'] ?? $spk['jumlah_unit'] ?? '..............................')) ?></div>
                                        <div class="val">
                                            <?php
                                            // Debug: show all possible merk and jenis fields
                                            echo '<!-- DEBUG ALL UNIT INFO FIELDS: ';
                                            echo 's.merk_unit=' . ($s['merk_unit'] ?? 'null') . ', ';
                                            echo 's.jenis_unit=' . ($s['jenis_unit'] ?? 'null') . ', ';
                                            echo 's.tipe_jenis=' . ($s['tipe_jenis'] ?? 'null') . ', ';
                                            echo 's.model_unit=' . ($s['model_unit'] ?? 'null');
                                            if (isset($unit)) {
                                                echo ', unit.merk_unit=' . ($unit['merk_unit'] ?? 'null') . ', ';
                                                echo 'unit.jenis_unit=' . ($unit['jenis_unit'] ?? 'null') . ', ';
                                                echo 'unit.tipe_jenis=' . ($unit['tipe_jenis'] ?? 'null') . ', ';
                                                echo 'unit.model_unit=' . ($unit['model_unit'] ?? 'null');
                                            }
                                            echo ' -->';
                                            
                                            // Combine merk and jenis unit info
                                            $merkUnit = $s['merk_unit'] ?? ($unit['merk_unit'] ?? '');
                                            $jenisUnit = $s['jenis_unit'] ?? ($unit['jenis_unit'] ?? '');
                                            $tipeUnit = $s['tipe_jenis'] ?? ($unit['tipe_jenis'] ?? '');
                                            $modelUnit = $s['model_unit'] ?? ($unit['model_unit'] ?? '');
                                            
                                            $brandTypeInfo = [];
                                            if (!empty($merkUnit)) $brandTypeInfo[] = $merkUnit;
                                            if (!empty($modelUnit)) $brandTypeInfo[] = $modelUnit;
                                            if (!empty($jenisUnit) && !in_array($jenisUnit, $brandTypeInfo)) $brandTypeInfo[] = $jenisUnit;
                                            if (!empty($tipeUnit) && !in_array($tipeUnit, $brandTypeInfo)) $brandTypeInfo[] = $tipeUnit;
                                            
                                            echo !empty($brandTypeInfo) ? esc(implode(' ', $brandTypeInfo)) : '..............................';
                                            ?>
                                        </div>
                                        <div class="val"><?= esc(($s['jenis_baterai'] ?? '') . (!empty($s['jenis_baterai']) && !empty($s['charger_model']) ? ' & ' : '') . ($s['charger_model'] ?? '') ?: '..............................') ?></div>
                                        <div class="val"><?= esc($s['departemen_id_name'] ?? $s['departemen_name'] ?? $s['departemen_id'] ?? '..............................') ?></div>
                                        <div class="val"><?= esc($s['kapasitas_id_name'] ?? $s['kapasitas_name'] ?? $s['kapasitas_id'] ?? '..............................') ?></div>
                                        <div class="val"><?= esc($s['attachment_tipe'] ?? ($s['selected']['attachment']['tipe'] ?? '..............................')) ?></div>
                                        <div class="val"><?= esc(($s['roda_id_name'] ?? '') . (!empty($s['roda_id_name']) && !empty($s['ban_id_name']) ? ' & ' : '') . ($s['ban_id_name'] ?? '') ?: '..............................') ?></div>
                                        <div class="val"><?= esc($s['mast_id_name'] ?? $s['mast_id'] ?? '..............................') ?></div>
                                        <div class="val"><?= esc($s['valve_id_name'] ?? $s['valve_id'] ?? '..............................') ?></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center align-middle">3.</td>
                                <td class="align-middle"><strong>Aksesoris</strong></td>
                                <td class="val"><?php
                                    // Multiple ways the accessories might be stored
                                    $aksText = '..............................';
                                    
                                    // Try parsing from different possible sources
                                    if (!empty($s['aksesoris'])) {
                                        if (is_array($s['aksesoris'])) {
                                            $aksText = implode(', ', $s['aksesoris']);
                                        } else if (is_string($s['aksesoris'])) {
                                            // Try to parse JSON string
                                            try {
                                                $aksArray = json_decode($s['aksesoris'], true);
                                                if (is_array($aksArray) && !empty($aksArray)) {
                                                    $aksText = implode(', ', $aksArray);
                                                } else {
                                                    $aksText = $s['aksesoris']; // Use as-is if not an array
                                                }
                                            } catch (Exception $e) {
                                                $aksText = $s['aksesoris']; // Use as-is if parsing fails
                                            }
                                        }
                                    } else if (!empty($spk['persiapan_aksesoris_tersedia'])) {
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

                    <div class="mb-2 fw-bold">Prepared Detail :</div>
                    <?php $preparedList = $s['prepared_units_detail'] ?? []; ?>
                    <?php if (is_array($preparedList) && count($preparedList) > 1): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width:4%" class="text-center">No</th>
                                    <th style="width:26%">Unit</th>
                                    <th style="width:20%">Serial</th>
                                    <th style="width:20%">Merk/Model</th>
                                    <th style="width:15%">Attachment</th>
                                    <th style="width:15%">Mekanik</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($preparedList as $i => $rowPrepared): ?>
                                <tr>
                                    <td class="text-center align-top"><?= $i+1 ?>.</td>
                                    <td class="align-top">
                                        <div class="val"><?= esc($rowPrepared['unit_label'] ?: ('#'.$rowPrepared['unit_id'])) ?></div>
                                    </td>
                                    <td class="align-top val"><?= esc($rowPrepared['serial_number'] ?? '') ?></td>
                                    <td class="align-top val"><?= esc(trim(($rowPrepared['merk_unit'] ?? '').' '.($rowPrepared['model_unit'] ?? ''))) ?></td>
                                    <td class="align-top val"><?= esc($rowPrepared['attachment_label'] ?? '') ?></td>
                                    <td class="align-top">
                                        <div class="val"><?= esc($rowPrepared['mekanik'] ?? '') ?></div>
                                        <?php if (!empty($rowPrepared['catatan'])): ?><div class="muted">Catatan: <?= esc($rowPrepared['catatan']) ?></div><?php endif; ?>
                                        <?php if (!empty($rowPrepared['timestamp'])): ?><div class="muted">Waktu: <?= esc($rowPrepared['timestamp']) ?></div><?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
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
                            ];
                            $summaryRight = [
                                ['Serial Number', $unit['serial_number'] ?? ''],
                                ['Model', $unit['model_unit'] ?? ''],
                                ['Tipe Unit', $unit['tipe_jenis'] ?? $s['tipe_jenis'] ?? ''],
                                ['Mast', $s['mast_id_name'] ?? ''],
                                ['SN Mast', $unit['sn_mast_formatted'] ?? ''],
                                ['SN Charger', $unit['sn_charger_formatted'] ?? ''],
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

