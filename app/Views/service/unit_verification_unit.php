<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<!-- Unit Verification styles are in optima-pro.css (.vf-table, .vf-check, .acc-item, .vf-panel-title, .vf-loading-overlay) -->

<?php
$audit      = $audit ?? [];
$item       = $item ?? [];
$index      = $index ?? 1;
$total      = $totalUnits ?? 1;
$unitDetail = $unitDetail ?? null;
$components = $components ?? ['battery' => null, 'charger' => null, 'attachment' => null];

$id      = (int)($item['id'] ?? 0);
$unitId  = (int)($item['unit_id'] ?? 0);
$result  = $item['result'] ?? 'MATCH';

$allAccessories = [
    'LAMPU UTAMA'          => 'Lampu (Utama, Mundur, Sign, Stop)',
    'ROTARY LAMP'          => 'Rotary Lamp',
    'SENSOR PARKING'       => 'Sensor Parking',
    'HORN SPEAKER'         => 'Horn Speaker',
    'APAR 1 KG'            => 'APAR 1 KG',
    'APAR 3 KG'            => 'APAR 3 KG',
    'BEACON'               => 'Beacon',
    'TELEMATIC'            => 'Telematic',
    'BLUE SPOT'            => 'Blue Spot',
    'RED LINE'             => 'Red Line',
    'WORK LIGHT'           => 'Work Light',
    'BACK BUZZER'          => 'Back Buzzer',
    'CAMERA AI'            => 'Camera AI',
    'CAMERA'               => 'Camera',
    'SPEED LIMITER'        => 'Speed Limiter',
    'LASER FORK'           => 'Laser Fork',
    'VOICE ANNOUNCER'      => 'Voice Announcer',
    'HORN KLASON'          => 'Horn Klason',
    'BIO METRIC'           => 'Bio Metric',
    'ACRYLIC'              => 'Acrylic',
    'P3K'                  => 'P3K',
    'SPARS ARRESTOR'       => 'Spars Arrestor',
    'SAFETY BELT INTERLOC' => 'Safety Belt Interloc',
];
$dbAccessories = $unitDetail['aksesoris_list'] ?? [];
?>

<!-- ─── Page Header ─── -->
<div class="d-flex align-items-start justify-content-between mb-3 flex-wrap gap-2">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('service/unit-verification') ?>">Unit Verification</a></li>
                <li class="breadcrumb-item active">Verifikasi Unit</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-clipboard-check me-2 text-primary"></i>Verifikasi Unit
            <span class="badge badge-soft-blue ms-1"><?= $index ?> / <?= $total ?></span>
        </h4>
        <p class="text-muted small mb-0">
            <strong><?= esc($audit['audit_number'] ?? '-') ?></strong> &middot;
            <?= esc($audit['customer_name'] ?? '-') ?> &mdash; <?= esc($audit['location_name'] ?? '-') ?>
        </p>
    </div>
    <a href="<?= base_url('service/unit-verification/print/' . ($audit['id'] ?? 0)) ?>" class="btn btn-outline-primary btn-sm">
        <i class="fas fa-print me-1"></i>Print Lokasi
    </a>
</div>

<!-- ─── Audit Info Cards ─── -->
<div class="row g-2 mb-3">
    <?php foreach ([
        ['Status Audit', esc($audit['status'] ?? 'DRAFT')],
        ['No. Kontrak',  '<span class="font-monospace small">' . esc($audit['no_kontrak_masked'] ?? '-') . '</span>'],
        ['Tanggal Audit', isset($audit['audit_date']) ? date('d M Y', strtotime($audit['audit_date'])) : '-'],
        ['Mekanik',      esc($audit['mechanic_name'] ?? '-')],
    ] as [$lbl, $val]): ?>
    <div class="col-6 col-md-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="text-muted text-xs"><?= $lbl ?></div>
                <div class="fw-semibold small"><?= $val ?></div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Loading state while AJAX fetches master data -->
<div id="vfPageLoader" class="text-center py-5">
    <div class="spinner-border text-primary mb-2"></div>
    <div class="text-muted small">Memuat data unit dari database…</div>
</div>

<!-- ─── Main Form (hidden until data loads) ─── -->
<form id="unitVerifyForm" class="d-none">
    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrfInput">
    <input type="hidden" name="audit_id"  value="<?= (int)($audit['id'] ?? 0) ?>">
    <input type="hidden" name="unit_id"   value="<?= $unitId ?>">
    <!-- audit summary (required by submitAuditResults inside saveUnitVerificationFromAudit) -->
    <input type="hidden" name="summary[actual_total_units]"  value="<?= (int)($audit['actual_total_units'] ?? $audit['kontrak_total_units'] ?? 0) ?>">
    <input type="hidden" name="summary[actual_spare_units]"  value="<?= (int)($audit['actual_spare_units'] ?? $audit['kontrak_spare_units'] ?? 0) ?>">
    <input type="hidden" name="summary[actual_has_operator]" value="<?= (int)($audit['actual_has_operator'] ?? 0) ?>">
    <input type="hidden" name="summary[mechanic_notes]"      value="<?= esc($audit['mechanic_notes'] ?? '') ?>">
    <input type="hidden" name="summary[service_notes]"       value="<?= esc($audit['service_notes'] ?? '') ?>">
    <!-- audit item result (kept hidden — not editable here) -->
    <input type="hidden" name="items[<?= $id ?>][result]"          value="<?= esc($result) ?>">
    <input type="hidden" name="items[<?= $id ?>][actual_no_unit]"  value="<?= esc($item['actual_no_unit'] ?? $item['expected_no_unit'] ?? '') ?>">

    <!-- ════ PANEL 1: VERIFIKASI DATA UNIT ════ -->
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
            <div class="vf-panel-title">
                <i class="fas fa-forklift text-primary me-2"></i>
                <h6 class="mb-0 fw-semibold">Verifikasi Data Unit</h6>
                <?php
                $resultMap = [
                    'MATCH'              => ['badge-soft-green', 'Match'],
                    'MISMATCH_NO_UNIT'   => ['badge-soft-red',   'No Unit Beda'],
                    'MISMATCH_SERIAL'    => ['badge-soft-red',   'Serial Beda'],
                    'MISMATCH_SPEC'      => ['badge-soft-orange', 'Spek Beda'],
                    'MISMATCH_SPARE'     => ['badge-soft-orange', 'Spare Beda'],
                    'NO_UNIT_IN_KONTRAK' => ['badge-soft-red',   'Tidak di Kontrak'],
                    'EXTRA_UNIT'         => ['badge-soft-cyan',   'Extra Unit'],
                    'ADD_UNIT'           => ['badge-soft-cyan',   'Tambah Unit'],
                ];
                [$rbg, $rlbl] = $resultMap[$result] ?? ['badge-soft-gray', $result];
                ?>
                <span class="badge <?= $rbg ?> ms-2 small"><?= $rlbl ?></span>
            </div>
            <div class="btn-group btn-group-sm align-items-center">
                <?php if (!empty($hasPrev)): ?>
                    <a href="<?= base_url('service/unit-verification/unit/' . ($audit['id'] ?? 0) . '/' . max(1,$index-1)) ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-chevron-left"></i> Sebelumnya
                    </a>
                <?php endif; ?>
                <?php if (!empty($hasNext)): ?>
                    <a href="<?= base_url('service/unit-verification/unit/' . ($audit['id'] ?? 0) . '/' . min($total,$index+1)) ?>" class="btn btn-outline-secondary">
                        Berikutnya <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body p-0 position-relative">
            <div class="table-responsive">
                <table class="table table-bordered vf-table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Database</th>
                            <th>Real Lapangan</th>
                            <th class="text-center">Sesuai</th>
                        </tr>
                    </thead>
                    <tbody>

                        <!-- No Unit (readonly) -->
                        <tr data-field="no_unit">
                            <td>No Unit <span class="text-danger">*</span></td>
                            <td id="db-no-unit" class="font-monospace">-</td>
                            <td>
                                <input type="text" class="form-control form-control-sm bg-light"
                                       value="<?= esc($item['actual_no_unit'] ?? $item['expected_no_unit'] ?? '') ?>"
                                       readonly>
                            </td>
                            <td><span class="vf-check neutral" data-field="no_unit">☐</span></td>
                        </tr>

                        <!-- Pelanggan (readonly) -->
                        <tr data-field="pelanggan">
                            <td>Pelanggan <span class="text-danger">*</span></td>
                            <td id="db-pelanggan">-</td>
                            <td class="text-muted small">—</td>
                            <td><span class="vf-check neutral" data-field="pelanggan">☐</span></td>
                        </tr>

                        <!-- Lokasi (readonly) -->
                        <tr data-field="lokasi">
                            <td>Lokasi</td>
                            <td id="db-lokasi">-</td>
                            <td class="text-muted small">—</td>
                            <td><span class="vf-check neutral" data-field="lokasi">☐</span></td>
                        </tr>

                        <!-- Serial Number -->
                        <tr data-field="serial_number">
                            <td>Serial Number <span class="text-danger">*</span></td>
                            <td id="db-serial-number" class="font-monospace">-</td>
                            <td>
                                <input type="text" class="form-control form-control-sm vf-input"
                                       name="items[<?= $id ?>][actual_serial]"
                                       id="rl-serial-number"
                                       data-field="serial_number"
                                       value="<?= esc($item['actual_serial'] ?? '') ?>"
                                       placeholder="Serial Number aktual">
                                <!-- also save to master -->
                                <input type="hidden" name="master[serial_number]" id="master-serial-number"
                                       value="<?= esc($item['actual_serial'] ?? '') ?>">
                            </td>
                            <td><button type="button" class="vf-check neutral" data-field="serial_number">☐</button></td>
                        </tr>

                        <!-- Tahun Unit -->
                        <tr data-field="tahun_unit">
                            <td>Tahun Unit</td>
                            <td id="db-tahun-unit">-</td>
                            <td>
                                <input type="text" class="form-control form-control-sm vf-input"
                                       name="master[tahun_unit]" id="rl-tahun-unit"
                                       data-field="tahun_unit"
                                       value="" placeholder="Tahun aktual">
                            </td>
                            <td><button type="button" class="vf-check neutral" data-field="tahun_unit">☐</button></td>
                        </tr>

                        <!-- Departemen -->
                        <tr data-field="departemen_id">
                            <td>Departemen <span class="text-danger">*</span></td>
                            <td id="db-departemen">-</td>
                            <td>
                                <select class="form-select form-select-sm vf-select"
                                        name="master[departemen_id]" id="rl-departemen"
                                        data-field="departemen_id">
                                    <option value="">— pilih —</option>
                                </select>
                            </td>
                            <td><button type="button" class="vf-check neutral" data-field="departemen_id">☐</button></td>
                        </tr>

                        <!-- Tipe Unit -->
                        <tr data-field="tipe_unit_id">
                            <td>Tipe Unit <span class="text-danger">*</span></td>
                            <td id="db-tipe-unit">-</td>
                            <td>
                                <select class="form-select form-select-sm vf-select"
                                        name="master[tipe_unit_id]" id="rl-tipe-unit"
                                        data-field="tipe_unit_id">
                                    <option value="">— pilih —</option>
                                </select>
                            </td>
                            <td><button type="button" class="vf-check neutral" data-field="tipe_unit_id">☐</button></td>
                        </tr>

                        <!-- Kapasitas Unit -->
                        <tr data-field="kapasitas_unit_id">
                            <td>Kapasitas Unit <span class="text-danger">*</span></td>
                            <td id="db-kapasitas-unit">-</td>
                            <td>
                                <select class="form-select form-select-sm vf-select"
                                        name="master[kapasitas_unit_id]" id="rl-kapasitas"
                                        data-field="kapasitas_unit_id">
                                    <option value="">— pilih —</option>
                                </select>
                            </td>
                            <td><button type="button" class="vf-check neutral" data-field="kapasitas_unit_id">☐</button></td>
                        </tr>

                        <!-- Model Unit -->
                        <tr data-field="model_unit_id">
                            <td>Model Unit <span class="text-danger">*</span></td>
                            <td id="db-model-unit">-</td>
                            <td>
                                <select class="form-select form-select-sm vf-select"
                                        name="master[model_unit_id]" id="rl-model-unit"
                                        data-field="model_unit_id">
                                    <option value="">— pilih —</option>
                                </select>
                            </td>
                            <td><button type="button" class="vf-check neutral" data-field="model_unit_id">☐</button></td>
                        </tr>

                        <!-- Model Mesin -->
                        <tr data-field="model_mesin_id">
                            <td>Model Mesin <span class="text-danger">*</span></td>
                            <td id="db-model-mesin">-</td>
                            <td>
                                <select class="form-select form-select-sm vf-select"
                                        name="master[model_mesin_id]" id="rl-model-mesin"
                                        data-field="model_mesin_id">
                                    <option value="">— pilih —</option>
                                </select>
                            </td>
                            <td><button type="button" class="vf-check neutral" data-field="model_mesin_id">☐</button></td>
                        </tr>

                        <!-- SN Mesin -->
                        <tr data-field="sn_mesin">
                            <td>SN Mesin <span class="text-danger">*</span></td>
                            <td id="db-sn-mesin" class="font-monospace">-</td>
                            <td>
                                <input type="text" class="form-control form-control-sm vf-input"
                                       name="master[sn_mesin]" id="rl-sn-mesin"
                                       data-field="sn_mesin"
                                       value="" placeholder="SN Mesin aktual">
                            </td>
                            <td><button type="button" class="vf-check neutral" data-field="sn_mesin">☐</button></td>
                        </tr>

                        <!-- Model Mast -->
                        <tr data-field="model_mast_id">
                            <td>Model Mast <span class="text-danger">*</span></td>
                            <td id="db-model-mast">-</td>
                            <td>
                                <select class="form-select form-select-sm vf-select"
                                        name="master[model_mast_id]" id="rl-model-mast"
                                        data-field="model_mast_id">
                                    <option value="">— pilih —</option>
                                </select>
                            </td>
                            <td><button type="button" class="vf-check neutral" data-field="model_mast_id">☐</button></td>
                        </tr>

                        <!-- SN Mast -->
                        <tr data-field="sn_mast">
                            <td>SN Mast <span class="text-danger">*</span></td>
                            <td id="db-sn-mast" class="font-monospace">-</td>
                            <td>
                                <input type="text" class="form-control form-control-sm vf-input"
                                       name="master[sn_mast]" id="rl-sn-mast"
                                       data-field="sn_mast"
                                       value="" placeholder="SN Mast aktual">
                            </td>
                            <td><button type="button" class="vf-check neutral" data-field="sn_mast">☐</button></td>
                        </tr>

                        <!-- Tinggi Mast -->
                        <tr data-field="tinggi_mast">
                            <td>Tinggi Mast <span class="text-danger">*</span></td>
                            <td id="db-tinggi-mast">-</td>
                            <td>
                                <input type="text" class="form-control form-control-sm vf-input"
                                       name="master[tinggi_mast]" id="rl-tinggi-mast"
                                       data-field="tinggi_mast"
                                       value="" placeholder="Tinggi Mast aktual">
                            </td>
                            <td><button type="button" class="vf-check neutral" data-field="tinggi_mast">☐</button></td>
                        </tr>

                        <!-- Keterangan Unit -->
                        <tr data-field="keterangan">
                            <td>Keterangan Unit <span class="text-danger">*</span></td>
                            <td id="db-keterangan">-</td>
                            <td>
                                <textarea class="form-control form-control-sm vf-input"
                                          name="master[keterangan]" id="rl-keterangan"
                                          data-field="keterangan" rows="2"
                                          placeholder="Keterangan aktual"></textarea>
                            </td>
                            <td><button type="button" class="vf-check neutral" data-field="keterangan">☐</button></td>
                        </tr>

                        <!-- Hour Meter -->
                        <tr data-field="hour_meter">
                            <td>Hour Meter (HM)</td>
                            <td id="db-hour-meter">-</td>
                            <td>
                                <input type="text" class="form-control form-control-sm vf-input"
                                       name="master[hour_meter]" id="rl-hour-meter"
                                       data-field="hour_meter"
                                       value="" placeholder="HM aktual">
                            </td>
                            <td><button type="button" class="vf-check neutral" data-field="hour_meter">☐</button></td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ════ PANEL 2: VERIFIKASI TAMBAHAN (Spare, Operator, Catatan) ════ -->
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-light py-2">
            <div class="vf-panel-title">
                <i class="fas fa-user-check text-warning me-2"></i>
                <h6 class="mb-0 fw-semibold">Verifikasi Tambahan</h6>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered vf-table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Database</th>
                            <th>Real Lapangan</th>
                            <th class="text-center">Sesuai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Spare?</td>
                            <td>
                                <?php if (!empty($item['expected_is_spare'])): ?>
                                    <span class="badge badge-soft-cyan">YES — Spare</span>
                                <?php else: ?>
                                    <span class="badge badge-soft-gray">NO — Unit Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <select class="form-select form-select-sm" name="items[<?= $id ?>][actual_is_spare]">
                                    <option value="0" <?= ($item['actual_is_spare'] ?? 0) == 0 ? 'selected' : '' ?>>NO — Unit Aktif</option>
                                    <option value="1" <?= ($item['actual_is_spare'] ?? 0) == 1 ? 'selected' : '' ?>>YES — Spare</option>
                                </select>
                            </td>
                            <td><span class="vf-check <?= $result === 'MISMATCH_SPARE' ? 'mismatch' : 'neutral' ?>"><?= $result === 'MISMATCH_SPARE' ? '✗' : '☐' ?></span></td>
                        </tr>
                        <tr>
                            <td>Operator Hadir?</td>
                            <td class="text-muted">—</td>
                            <td>
                                <select class="form-select form-select-sm" name="items[<?= $id ?>][actual_operator_present]">
                                    <option value="0" <?= ($item['actual_operator_present'] ?? 0) == 0 ? 'selected' : '' ?>>NO — Tidak Hadir</option>
                                    <option value="1" <?= ($item['actual_operator_present'] ?? 0) == 1 ? 'selected' : '' ?>>YES — Hadir</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Catatan</td>
                            <td class="text-muted">—</td>
                            <td>
                                <textarea class="form-control form-control-sm" rows="2"
                                          name="items[<?= $id ?>][notes]"
                                          placeholder="Catatan khusus untuk unit ini"><?= esc($item['notes'] ?? '') ?></textarea>
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ════ PANEL 3: VERIFIKASI DATA ATTACHMENT ════ -->
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-light py-2">
            <div class="vf-panel-title">
                <i class="fas fa-puzzle-piece text-info me-2"></i>
                <h6 class="mb-0 fw-semibold">Verifikasi Data Attachment</h6>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered vf-table mb-0">
                    <thead class="table-light">
                        <tr><th>Item</th><th>Database</th><th>Real Lapangan</th><th class="text-center">Sesuai</th></tr>
                    </thead>
                    <tbody>
                        <!-- Attachment -->
                        <tr data-field="attachment_id">
                            <td>Attachment <span class="text-danger">*</span></td>
                            <td id="db-attachment">-</td>
                            <td>
                                <select class="form-select form-select-sm vf-select"
                                        name="master[attachment_id]" id="rl-attachment"
                                        data-field="attachment_id">
                                    <option value="">— Tidak ada / Tidak berubah —</option>
                                </select>
                            </td>
                            <td><button type="button" class="vf-check neutral" data-field="attachment_id">☐</button></td>
                        </tr>
                        <!-- SN Attachment (informational from DB) -->
                        <tr>
                            <td>SN Attachment <span class="text-danger">*</span></td>
                            <td id="db-sn-attachment" class="font-monospace">-</td>
                            <td class="text-muted small"><em>Ikut attachment terpilih</em></td>
                            <td><span class="vf-check neutral">☐</span></td>
                        </tr>
                        <!-- Baterai -->
                        <tr data-field="baterai_id">
                            <td>Baterai <span class="text-danger">*</span></td>
                            <td id="db-baterai">-</td>
                            <td>
                                <select class="form-select form-select-sm vf-select"
                                        name="master[baterai_id]" id="rl-baterai"
                                        data-field="baterai_id">
                                    <option value="">— Tidak ada / Tidak berubah —</option>
                                </select>
                            </td>
                            <td><button type="button" class="vf-check neutral" data-field="baterai_id">☐</button></td>
                        </tr>
                        <!-- SN Baterai -->
                        <tr>
                            <td>SN Baterai <span class="text-danger">*</span></td>
                            <td id="db-sn-baterai" class="font-monospace">-</td>
                            <td class="text-muted small"><em>Ikut baterai terpilih</em></td>
                            <td><span class="vf-check neutral">☐</span></td>
                        </tr>
                        <!-- Charger -->
                        <tr data-field="charger_id">
                            <td>Charger <span class="text-danger">*</span></td>
                            <td id="db-charger">-</td>
                            <td>
                                <select class="form-select form-select-sm vf-select"
                                        name="master[charger_id]" id="rl-charger"
                                        data-field="charger_id">
                                    <option value="">— Tidak ada / Tidak berubah —</option>
                                </select>
                            </td>
                            <td><button type="button" class="vf-check neutral" data-field="charger_id">☐</button></td>
                        </tr>
                        <!-- SN Charger -->
                        <tr>
                            <td>SN Charger <span class="text-danger">*</span></td>
                            <td id="db-sn-charger" class="font-monospace">-</td>
                            <td class="text-muted small"><em>Ikut charger terpilih</em></td>
                            <td><span class="vf-check neutral">☐</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ════ PANEL 4: VERIFIKASI AKSESORIS UNIT ════ -->
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
            <div class="vf-panel-title">
                <i class="fas fa-boxes text-secondary me-2"></i>
                <h6 class="mb-0 fw-semibold">Verifikasi Aksesoris Unit</h6>
            </div>
            <span id="accSummary" class="badge badge-soft-gray small">0 / <?= count($allAccessories) ?></span>
        </div>
        <div class="card-body">
            <div class="small text-muted mb-3">
                Klik item untuk toggle sesuai / tidak. Perubahan akan tersimpan saat klik "Simpan".
            </div>
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-2" id="accessoryGrid">
                <?php foreach ($allAccessories as $key => $label):
                    $active = in_array($key, $dbAccessories);
                ?>
                <div class="col">
                    <div class="acc-item <?= $active ? 'active' : '' ?>" data-acc="<?= $key ?>">
                        <span class="acc-icon"><?= $active ? '✓' : '☐' ?></span>
                        <span><?= $label ?></span>
                        <input type="hidden" name="accessories[]" value="<?= $key ?>" <?= $active ? '' : 'disabled' ?>>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ─── Bottom Nav ─── -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex gap-2">
            <?php if (!empty($hasPrev)): ?>
                <a href="<?= base_url('service/unit-verification/unit/' . ($audit['id'] ?? 0) . '/' . max(1,$index-1)) ?>"
                   class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-chevron-left me-1"></i>Unit Sebelumnya
                </a>
            <?php endif; ?>
            <?php if (!empty($hasNext)): ?>
                <a href="<?= base_url('service/unit-verification/unit/' . ($audit['id'] ?? 0) . '/' . min($total,$index+1)) ?>"
                   class="btn btn-outline-secondary btn-sm">
                    Unit Berikutnya<i class="fas fa-chevron-right ms-1"></i>
                </a>
            <?php endif; ?>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('service/unit-verification') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-list me-1"></i>Kembali
            </a>
            <button type="button" class="btn btn-primary btn-sm" id="btnSaveVf" onclick="saveUnitVerification()">
                <i class="fas fa-save me-1"></i>Simpan Perubahan
            </button>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
const UNIT_ID    = <?= $unitId ?>;
const MASTER_URL = '<?= base_url('service/unit-audit/unit-master-data/') ?>' + UNIT_ID;
const SAVE_URL   = '<?= base_url('service/unit-audit/save-unit-verification') ?>';

// Mapping: field key → { dbEl: '#db-xxx', rlEl: '#rl-xxx', dbVal: null, dbId: null }
const FIELD_MAP = {
    no_unit:          { dbEl: '#db-no-unit',       rlEl: null,           type: 'readonly' },
    pelanggan:        { dbEl: '#db-pelanggan',      rlEl: null,           type: 'readonly' },
    lokasi:           { dbEl: '#db-lokasi',         rlEl: null,           type: 'readonly' },
    serial_number:    { dbEl: '#db-serial-number',  rlEl: '#rl-serial-number', type: 'text' },
    tahun_unit:       { dbEl: '#db-tahun-unit',     rlEl: '#rl-tahun-unit',    type: 'text' },
    departemen_id:    { dbEl: '#db-departemen',     rlEl: '#rl-departemen',    type: 'select', dbIdKey: 'departemen_id' },
    tipe_unit_id:     { dbEl: '#db-tipe-unit',      rlEl: '#rl-tipe-unit',     type: 'select', dbIdKey: 'tipe_unit_id' },
    kapasitas_unit_id:{ dbEl: '#db-kapasitas-unit', rlEl: '#rl-kapasitas',     type: 'select', dbIdKey: 'kapasitas_unit_id' },
    model_unit_id:    { dbEl: '#db-model-unit',     rlEl: '#rl-model-unit',    type: 'select', dbIdKey: 'model_unit_id' },
    model_mesin_id:   { dbEl: '#db-model-mesin',    rlEl: '#rl-model-mesin',   type: 'select', dbIdKey: 'model_mesin_id' },
    sn_mesin:         { dbEl: '#db-sn-mesin',       rlEl: '#rl-sn-mesin',      type: 'text' },
    model_mast_id:    { dbEl: '#db-model-mast',     rlEl: '#rl-model-mast',    type: 'select', dbIdKey: 'model_mast_id' },
    sn_mast:          { dbEl: '#db-sn-mast',        rlEl: '#rl-sn-mast',       type: 'text' },
    tinggi_mast:      { dbEl: '#db-tinggi-mast',    rlEl: '#rl-tinggi-mast',   type: 'text' },
    keterangan:       { dbEl: '#db-keterangan',     rlEl: '#rl-keterangan',    type: 'text' },
    hour_meter:       { dbEl: '#db-hour-meter',     rlEl: '#rl-hour-meter',    type: 'text' },
    attachment_id:    { dbEl: '#db-attachment',     rlEl: '#rl-attachment',    type: 'select', dbIdKey: 'attachment_current_id' },
    baterai_id:       { dbEl: '#db-baterai',        rlEl: '#rl-baterai',       type: 'select', dbIdKey: 'baterai_current_id' },
    charger_id:       { dbEl: '#db-charger',        rlEl: '#rl-charger',       type: 'select', dbIdKey: 'charger_current_id' },
};

// DB values populated from AJAX
const DB_VALUES = {};

// ── Populate options helper ──────────────────────────────────────────────────
function populateSelect(el, options, currentId) {
    const sel = document.querySelector(el);
    if (!sel) return;
    // Keep first placeholder option
    while (sel.options.length > 1) sel.remove(1);
    options.forEach(o => {
        const opt = new Option(o.name, o.id);
        sel.add(opt);
    });
    if (currentId) sel.value = String(currentId);
}

// ── Update row checklist ─────────────────────────────────────────────────────
function updateRowCheck(field) {
    const cfg = FIELD_MAP[field];
    if (!cfg || cfg.type === 'readonly') {
        // readonly rows just show neutral
        return;
    }
    const dbVal  = DB_VALUES[field];
    const rlEl   = cfg.rlEl ? document.querySelector(cfg.rlEl) : null;
    const btnEl  = document.querySelector(`.vf-check[data-field="${field}"]`);
    if (!btnEl || !rlEl) return;

    let rlVal = rlEl.tagName === 'SELECT' ? rlEl.value : rlEl.value.trim();
    const dbValStr = dbVal != null ? String(dbVal) : '';
    const rlValStr = rlVal ? String(rlVal) : '';

    if (!rlValStr || !dbValStr) {
        btnEl.className = 'vf-check neutral';
        btnEl.innerHTML = '☐';
    } else if (rlValStr === dbValStr) {
        btnEl.className = 'vf-check match';
        btnEl.innerHTML = '✓';
    } else {
        btnEl.className = 'vf-check mismatch';
        btnEl.innerHTML = '✗';
    }
}

// ── Checklist click: if mismatch → reset to DB value ────────────────────────
document.querySelectorAll('button.vf-check').forEach(btn => {
    btn.addEventListener('click', function() {
        const field = this.dataset.field;
        const cfg = FIELD_MAP[field];
        if (!cfg || cfg.type === 'readonly' || !cfg.rlEl) return;

        // If currently mismatch, reset to DB value
        if (this.classList.contains('mismatch')) {
            const rlEl = document.querySelector(cfg.rlEl);
            if (rlEl && DB_VALUES[field] != null) {
                rlEl.value = String(DB_VALUES[field]);
                updateRowCheck(field);
            }
        }
        // If match, clear (let user re-enter)
        else if (this.classList.contains('match')) {
            const rlEl = document.querySelector(cfg.rlEl);
            if (rlEl) {
                rlEl.value = '';
                updateRowCheck(field);
            }
        }
    });
});

// ── Watch all Real Lapangan inputs/selects ───────────────────────────────────
document.querySelectorAll('.vf-input, .vf-select').forEach(el => {
    const field = el.dataset.field;
    if (!field) return;
    el.addEventListener('change', () => {
        updateRowCheck(field);
        // Mirror text to master hidden if needed
        const masterInput = document.getElementById('master-' + field.replace('_id', ''));
        if (masterInput && el.tagName !== 'SELECT') masterInput.value = el.value;
    });
    el.addEventListener('input', () => {
        updateRowCheck(field);
    });
});

// ── Accessories toggle ───────────────────────────────────────────────────────
function updateAccSummary() {
    const active = document.querySelectorAll('.acc-item.active').length;
    document.getElementById('accSummary').textContent = active + ' / <?= count($allAccessories) ?>';
}

document.querySelectorAll('.acc-item').forEach(item => {
    item.addEventListener('click', function() {
        const isActive = this.classList.toggle('active');
        this.querySelector('.acc-icon').textContent = isActive ? '✓' : '☐';
        const hid = this.querySelector('input[type=hidden]');
        if (hid) hid.disabled = !isActive;
        updateAccSummary();
    });
});
updateAccSummary();

// ── Load master data from API ────────────────────────────────────────────────
async function loadMasterData() {
    if (!UNIT_ID) {
        document.getElementById('vfPageLoader').classList.add('d-none');
        document.getElementById('unitVerifyForm').classList.remove('d-none');
        return;
    }
    try {
        const resp = await fetch(MASTER_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const json = await resp.json();
        if (!json.success) throw new Error(json.message || 'Gagal memuat data');

        const unit   = json.unit   || {};
        const opts   = json.options || {};
        const comp   = json.components || {};

        // ── Populate DB column text ──
        const pelanggan = '<?= esc($audit['customer_name'] ?? '-') ?>';
        const lokasi    = '<?= esc($audit['location_name'] ?? '-') ?>';

        const dbFill = {
            'db-no-unit':      unit.no_unit,
            'db-pelanggan':    pelanggan,
            'db-lokasi':       lokasi,
            'db-serial-number':unit.serial_number,
            'db-tahun-unit':   unit.tahun_unit,
            'db-departemen':   unit.departemen_name,
            'db-tipe-unit':    unit.tipe_unit_name,
            'db-kapasitas-unit': unit.kapasitas_name,
            'db-model-unit':   unit.model_unit_name,
            'db-model-mesin':  unit.model_mesin_full,
            'db-sn-mesin':     unit.sn_mesin,
            'db-model-mast':   unit.model_mast_name,
            'db-sn-mast':      unit.sn_mast,
            'db-tinggi-mast':  unit.tinggi_mast,
            'db-keterangan':   unit.keterangan,
            'db-hour-meter':   unit.hour_meter,
            'db-attachment':   comp.attachment ? [(comp.attachment.tipe||''), (comp.attachment.merk||''), (comp.attachment.model||'')].join(' ').trim() : '-',
            'db-sn-attachment':comp.attachment ? (comp.attachment.sn_attachment || '-') : '-',
            'db-baterai':      comp.battery ? [(comp.battery.merk_baterai||''), (comp.battery.tipe_baterai||'')].join(' ').trim() : '-',
            'db-sn-baterai':   comp.battery ? (comp.battery.sn_baterai || '-') : '-',
            'db-charger':      comp.charger ? [(comp.charger.merk_charger||''), (comp.charger.tipe_charger||'')].join(' ').trim() : '-',
            'db-sn-charger':   comp.charger ? (comp.charger.sn_charger || '-') : '-',
        };
        Object.entries(dbFill).forEach(([id, val]) => {
            const el = document.getElementById(id);
            if (el) el.textContent = val || '-';
        });

        // ── Store DB values for checklist comparison ──
        DB_VALUES['serial_number']     = unit.serial_number;
        DB_VALUES['tahun_unit']        = unit.tahun_unit;
        DB_VALUES['departemen_id']     = unit.departemen_id;
        DB_VALUES['tipe_unit_id']      = unit.tipe_unit_id;
        DB_VALUES['kapasitas_unit_id'] = unit.kapasitas_unit_id;
        DB_VALUES['model_unit_id']     = unit.model_unit_id;
        DB_VALUES['model_mesin_id']    = unit.model_mesin_id;
        DB_VALUES['sn_mesin']          = unit.sn_mesin;
        DB_VALUES['model_mast_id']     = unit.model_mast_id;
        DB_VALUES['sn_mast']           = unit.sn_mast;
        DB_VALUES['tinggi_mast']       = unit.tinggi_mast;
        DB_VALUES['keterangan']        = unit.keterangan;
        DB_VALUES['hour_meter']        = unit.hour_meter;
        DB_VALUES['attachment_current_id'] = unit.attachment_inventory_attachment_id;
        DB_VALUES['baterai_current_id']    = unit.baterai_inventory_attachment_id;
        DB_VALUES['charger_current_id']    = unit.charger_inventory_attachment_id;

        // ── Populate dropdowns ──
        populateSelect('#rl-departemen',  opts.departemen  || [], unit.departemen_id);
        populateSelect('#rl-tipe-unit',   opts.tipe_unit   || [], unit.tipe_unit_id);
        populateSelect('#rl-kapasitas',   opts.kapasitas   || [], unit.kapasitas_unit_id);
        populateSelect('#rl-model-unit',  opts.model_unit  || [], unit.model_unit_id);
        populateSelect('#rl-model-mesin', opts.model_mesin || [], unit.model_mesin_id);
        populateSelect('#rl-model-mast',  opts.model_mast  || [], unit.model_mast_id);

        // Component dropdowns — pre-select current attachment/battery/charger
        populateSelect('#rl-attachment', opts.attachment || [], unit.attachment_inventory_attachment_id);
        populateSelect('#rl-baterai',    opts.baterai    || [], unit.baterai_inventory_attachment_id);
        populateSelect('#rl-charger',    opts.charger    || [], unit.charger_inventory_attachment_id);

        // ── Set text inputs from DB ──
        const textDefaults = {
            '#rl-serial-number': unit.serial_number,
            '#rl-tahun-unit':    unit.tahun_unit,
            '#rl-sn-mesin':      unit.sn_mesin,
            '#rl-sn-mast':       unit.sn_mast,
            '#rl-tinggi-mast':   unit.tinggi_mast,
            '#rl-keterangan':    unit.keterangan,
            '#rl-hour-meter':    unit.hour_meter,
        };
        Object.entries(textDefaults).forEach(([sel, val]) => {
            const el = document.querySelector(sel);
            if (el && val) el.value = val;
        });

        // ── Initial checklist status ──
        Object.keys(FIELD_MAP).forEach(f => updateRowCheck(f));

        // ── Show form ──
        document.getElementById('vfPageLoader').classList.add('d-none');
        document.getElementById('unitVerifyForm').classList.remove('d-none');

    } catch (e) {
        document.getElementById('vfPageLoader').innerHTML =
            `<div class="alert alert-warning d-inline-block">Gagal memuat data unit: ${e.message}</div>`;
        // Show form anyway so user can still save audit result
        document.getElementById('unitVerifyForm').classList.remove('d-none');
    }
}

// ── Save ─────────────────────────────────────────────────────────────────────
async function saveUnitVerification() {
    const btn = document.getElementById('btnSaveVf');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan…';

    const form = document.getElementById('unitVerifyForm');
    const fd   = new FormData(form);
    const payload = new URLSearchParams();
    for (const [k, v] of fd.entries()) payload.append(k, v);

    try {
        const resp = await fetch(SAVE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: payload.toString(),
        });
        const json = await resp.json();

        // Update CSRF token
        if (json.csrf_hash) {
            document.getElementById('csrfInput').value = json.csrf_hash;
        }

        if (json.success) {
            btn.classList.replace('btn-primary', 'btn-success');
            btn.innerHTML = '<i class="fas fa-check me-1"></i>Tersimpan';
            setTimeout(() => window.location.reload(), 900);
        } else {
            alert('Gagal menyimpan: ' + (json.message || 'Error tidak diketahui'));
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i>Simpan Perubahan';
        }
    } catch (e) {
        alert('Terjadi kesalahan jaringan');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i>Simpan Perubahan';
    }
}

// ── Init ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', loadMasterData);
</script>
<?= $this->endSection() ?>
