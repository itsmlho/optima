<?php
/**
 * Shared Unit Verification Form Content Partial
 * 
 * This partial contains the form body (tables, panels) used by both:
 * - Work Order modal (unit_verification.php)
 * - Standalone audit verification (unit_audit_verification_form.php)
 * 
 * Variables expected:
 * - $context: 'wo' | 'audit' (determines header label and hidden fields context)
 * - $contextNumber: WO number or Audit number to display
 * - $showHistoryBanner: bool - whether to show verification history banner
 * - $showPrintButton: bool - for standalone mode
 * - $formIdPrefix: string - prefix for form element IDs (default: 'verify')
 */

$context = $context ?? 'wo';
$contextNumber = $contextNumber ?? '';
$showHistoryBanner = $showHistoryBanner ?? true;
$showPrintButton = $showPrintButton ?? false;
$formIdPrefix = $formIdPrefix ?? 'verify';
?>

<!-- Alert Instructions -->
<div class="alert alert-info">
    <h6 class="alert-heading mb-2">📋 Instruksi Verifikasi Data Unit</h6>
    <p class="mb-2">Tujuan verifikasi adalah <strong>memperbaiki data yang tidak sesuai</strong> dan <strong>melengkapi data yang kosong</strong> untuk mendapatkan data yang akurat.</p>
    <div class="row">
        <div class="col-md-4">
            <small><strong>📊 Kolom 1:</strong> Item Verifikasi</small>
        </div>
        <div class="col-md-4">
            <small><strong>💾 Kolom 2:</strong> Data Database (readonly)</small>
        </div>
        <div class="col-md-4">
            <small><strong>👁️ Kolom 3:</strong> Data Real Lapangan</small>
        </div>
    </div>
    <div class="mt-2">
        <small><strong>🎯 Cara Kerja:</strong> Jika SAMA → Centang "Sesuai" | Jika BERBEDA/KOSONG → Isi data real di kolom 3</small>
    </div>
    <small class="text-danger fw-bold">⚠️ Field bertanda * wajib diisi</small>
</div>

<?php if ($showHistoryBanner): ?>
<!-- Verification History Banner -->
<div class="alert alert-warning d-none" id="verification-history-banner">
    <h6 class="alert-heading mb-2">📜 Riwayat Verifikasi</h6>
    <p class="mb-0" id="verification-history-text">Loading...</p>
</div>
<?php endif; ?>

<!-- Verifikasi Data Unit -->
<div class="card mb-3">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fas fa-truck me-2"></i>Verifikasi Data Unit - 
            <?php if ($context === 'wo'): ?>
                WO: <span id="wo-number-display"><?= esc($contextNumber) ?></span>
            <?php else: ?>
                Audit: <span id="audit-number-display"><?= esc($contextNumber) ?></span>
            <?php endif; ?>
        </h6>
    </div>
    <div class="card-body p-0">
        <table class="table table-sm table-bordered mb-0">
            <thead>
                <tr>
                    <th width="25%">Item</th>
                    <th width="35%">Database</th>
                    <th width="35%">Real Lapangan</th>
                    <th width="5%">Sesuai</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>No Unit <span class="text-danger">*</span></td>
                    <td><input type="text" class="form-control form-control-sm" id="db-no-unit" readonly></td>
                    <td><input type="text" class="form-control form-control-sm" id="verify-no-unit" name="no_unit" placeholder="No unit real" readonly></td>
                    <td class="text-center"><input type="checkbox" class="form-check-input" id="check-no-unit" checked disabled></td>
                </tr>
                <tr>
                    <td>Pelanggan <span class="text-danger">*</span></td>
                    <td><input type="text" class="form-control form-control-sm" id="db-pelanggan" readonly></td>
                    <td><input type="text" class="form-control form-control-sm bg-light" id="verify-pelanggan" name="pelanggan" readonly required></td>
                    <td class="text-center"><input type="checkbox" class="form-check-input" id="check-pelanggan" checked disabled></td>
                </tr>
                <tr>
                    <td>Lokasi <span class="text-danger">*</span></td>
                    <td><input type="text" class="form-control form-control-sm" id="db-lokasi" readonly></td>
                    <td>
                        <select class="form-select form-select-sm" id="verify-lokasi" name="lokasi" required>
                            <option value="">Pilih Lokasi</option>
                        </select>
                        <input type="text" class="form-control form-control-sm d-none" id="verify-lokasi-manual" name="lokasi_manual" placeholder="Ketik lokasi manual (double-click untuk kembali ke dropdown)">
                    </td>
                    <td class="text-center"><input type="checkbox" class="form-check-input" id="check-lokasi"></td>
                </tr>
                <tr>
                    <td>Serial Number <span class="text-danger">*</span></td>
                    <td><input type="text" class="form-control form-control-sm" id="db-serial-number" readonly></td>
                    <td><input type="text" class="form-control form-control-sm" id="verify-serial-number" name="serial_number" placeholder="Serial number real"></td>
                    <td class="text-center"><input type="checkbox" class="form-check-input" id="check-serial-number"></td>
                </tr>
                <tr>
                    <td>Tahun Unit <span class="text-danger">*</span></td>
                    <td><input type="text" class="form-control form-control-sm" id="db-tahun-unit" readonly></td>
                    <td><input type="number" class="form-control form-control-sm" id="verify-tahun-unit" name="tahun_unit" placeholder="Tahun real" min="1990" max="2030"></td>
                    <td class="text-center"><input type="checkbox" class="form-check-input" id="check-tahun-unit"></td>
                </tr>
                <tr>
                    <td>Departemen <span class="text-danger">*</span></td>
                    <td><input type="text" class="form-control form-control-sm" id="db-departemen" readonly></td>
                    <td><select class="form-select form-select-sm" id="verify-departemen" name="departemen_id" required><option value="">Pilih Departemen</option></select></td>
                    <td class="text-center"><input type="checkbox" class="form-check-input" id="check-departemen"></td>
                </tr>
                <tr>
                    <td>Tipe Unit <span class="text-danger">*</span></td>
                    <td><input type="text" class="form-control form-control-sm" id="db-tipe-unit" readonly></td>
                    <td><select class="form-select form-select-sm" id="verify-tipe-unit" name="tipe_unit_id" required><option value="">Pilih Tipe Unit</option></select></td>
                    <td class="text-center"><input type="checkbox" class="form-check-input" id="check-tipe-unit"></td>
                </tr>
                <tr>
                    <td>Kapasitas Unit <span class="text-danger">*</span></td>
                    <td><input type="text" class="form-control form-control-sm" id="db-kapasitas-unit" readonly></td>
                    <td><select class="form-select form-select-sm" id="verify-kapasitas-unit" name="kapasitas_unit_id" required><option value="">Pilih Kapasitas</option></select></td>
                    <td class="text-center"><input type="checkbox" class="form-check-input" id="check-kapasitas-unit"></td>
                </tr>
                <tr>
                    <td>Model Unit <span class="text-danger">*</span></td>
                    <td><input type="text" class="form-control form-control-sm" id="db-model-unit" readonly></td>
                    <td><select class="form-select form-select-sm" id="verify-model-unit" name="model_unit_id" required><option value="">Pilih Model</option></select></td>
                    <td class="text-center"><input type="checkbox" class="form-check-input" id="check-model-unit"></td>
                </tr>
                <tr>
                    <td>Model Mesin</td>
                    <td><input type="text" class="form-control form-control-sm" id="db-model-mesin" readonly></td>
                    <td><select class="form-select form-select-sm" id="verify-model-mesin" name="model_mesin_id"><option value="">Pilih Model Mesin</option></select></td>
                    <td class="text-center"><input type="checkbox" class="form-check-input" id="check-model-mesin"></td>
                </tr>
                <tr>
                    <td>SN Mesin</td>
                    <td><input type="text" class="form-control form-control-sm" id="db-sn-mesin" readonly></td>
                    <td><input type="text" class="form-control form-control-sm" id="verify-sn-mesin" name="sn_mesin" placeholder="SN mesin real"></td>
                    <td class="text-center"><input type="checkbox" class="form-check-input" id="check-sn-mesin"></td>
                </tr>
                <tr>
                    <td>Model Mast</td>
                    <td><input type="text" class="form-control form-control-sm" id="db-model-mast" readonly></td>
                    <td><select class="form-select form-select-sm" id="verify-model-mast" name="model_mast_id"><option value="">Pilih Model Mast</option></select></td>
                    <td class="text-center"><input type="checkbox" class="form-check-input" id="check-model-mast"></td>
                </tr>
                <tr>
                    <td>SN Mast</td>
                    <td><input type="text" class="form-control form-control-sm" id="db-sn-mast" readonly></td>
                    <td><input type="text" class="form-control form-control-sm" id="verify-sn-mast" name="sn_mast" placeholder="SN mast real"></td>
                    <td class="text-center"><input type="checkbox" class="form-check-input" id="check-sn-mast"></td>
                </tr>
                <tr>
                    <td>Tinggi Mast</td>
                    <td><input type="text" class="form-control form-control-sm" id="db-tinggi-mast" readonly></td>
                    <td>
                        <select class="form-select form-select-sm" id="verify-tinggi-mast" name="tinggi_mast">
                            <option value="">Pilih Model Mast dulu</option>
                        </select>
                    </td>
                    <td class="text-center"><input type="checkbox" class="form-check-input" id="check-tinggi-mast"></td>
                </tr>
                <tr>
                    <td>Keterangan Unit</td>
                    <td><textarea class="form-control form-control-sm" id="db-keterangan" readonly rows="2"></textarea></td>
                    <td><textarea class="form-control form-control-sm" id="verify-keterangan" name="keterangan" rows="2" placeholder="Keterangan real"></textarea></td>
                    <td class="text-center"><input type="checkbox" class="form-check-input" id="check-keterangan"></td>
                </tr>
                <tr>
                    <td>Hour Meter (HM)</td>
                    <td><input type="text" class="form-control form-control-sm" id="db-hm" readonly></td>
                    <td><input type="number" class="form-control form-control-sm" id="verify-hm" name="hour_meter" placeholder="HM saat ini" step="0.01"></td>
                    <td class="text-center"><input type="checkbox" class="form-check-input" id="check-hm"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Verifikasi Data Attachment -->
<div class="card mb-3">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-puzzle-piece me-2"></i>Verifikasi Data Attachment</h6>
    </div>
    <div class="card-body p-0">
        <table class="table table-sm table-bordered mb-0">
            <thead>
                <tr>
                    <th width="25%">Item</th>
                    <th width="35%">Database</th>
                    <th width="35%">Real Lapangan</th>
                    <th width="5%">Sesuai</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Attachment</td>
                    <td><input type="text" class="form-control form-control-sm" id="db-attachment" readonly></td>
                    <td>
                        <div class="d-flex gap-1">
                            <select class="form-select form-select-sm" id="verify-attachment" name="attachment_id" style="min-width: 200px;">
                                <option value="">Pilih Attachment</option>
                            </select>
                            <button type="button" class="btn btn-success btn-sm" id="btn-add-attachment" title="Tambah Attachment Baru">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </td>
                    <td class="text-center"><input type="checkbox" class="form-check-input" id="check-attachment"></td>
                </tr>
                <tr>
                    <td>SN Attachment</td>
                    <td><input type="text" class="form-control form-control-sm" id="db-sn-attachment" readonly></td>
                    <td><input type="text" class="form-control form-control-sm" id="verify-sn-attachment" name="sn_attachment" placeholder="SN attachment real"></td>
                    <td class="text-center"><input type="checkbox" class="form-check-input" id="check-sn-attachment"></td>
                </tr>
                <tr id="baterai-row" style="display: none;">
                    <td>Baterai</td>
                    <td><input type="text" class="form-control form-control-sm" id="db-baterai" readonly></td>
                    <td>
                        <div class="d-flex gap-1">
                            <select class="form-select form-select-sm" id="verify-baterai" name="baterai_id" style="min-width: 200px;">
                                <option value="">Pilih Baterai</option>
                            </select>
                            <button type="button" class="btn btn-success btn-sm" id="btn-add-baterai" title="Tambah Baterai Baru">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </td>
                    <td class="text-center"><input type="checkbox" class="form-check-input" id="check-baterai"></td>
                </tr>
                <tr id="sn-baterai-row" style="display: none;">
                    <td>SN Baterai</td>
                    <td><input type="text" class="form-control form-control-sm" id="db-sn-baterai" readonly></td>
                    <td><input type="text" class="form-control form-control-sm" id="verify-sn-baterai" name="sn_baterai" placeholder="SN baterai real"></td>
                    <td class="text-center"><input type="checkbox" class="form-check-input" id="check-sn-baterai"></td>
                </tr>
                <tr id="charger-row" style="display: none;">
                    <td>Charger</td>
                    <td><input type="text" class="form-control form-control-sm" id="db-charger" readonly></td>
                    <td>
                        <div class="d-flex gap-1">
                            <select class="form-select form-select-sm" id="verify-charger" name="charger_id" style="min-width: 200px;">
                                <option value="">Pilih Charger</option>
                            </select>
                            <button type="button" class="btn btn-success btn-sm" id="btn-add-charger" title="Tambah Charger Baru">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </td>
                    <td class="text-center"><input type="checkbox" class="form-check-input" id="check-charger"></td>
                </tr>
                <tr id="sn-charger-row" style="display: none;">
                    <td>SN Charger</td>
                    <td><input type="text" class="form-control form-control-sm" id="db-sn-charger" readonly></td>
                    <td><input type="text" class="form-control form-control-sm" id="verify-sn-charger" name="sn_charger" placeholder="SN charger real"></td>
                    <td class="text-center"><input type="checkbox" class="form-check-input" id="check-sn-charger"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Verifikasi Aksesoris Unit -->
<div class="card mb-3">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-tools me-2"></i>Verifikasi Aksesoris Unit</h6>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <small><strong>📋 Instruksi:</strong> Centang aksesoris yang <strong>terpasang dan berfungsi</strong> pada unit ini. Aksesoris yang sudah tercatat akan otomatis ter-checklist.</small>
        </div>
        
        <!-- Aksesoris Unit -->
        <div class="mb-4">
            <h6 class="text-primary mb-3"><i class="fas fa-truck me-2"></i>Aksesoris Unit</h6>
            <div class="row">
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="LAMPU UTAMA" id="acc-lampu">
                        <label class="form-check-label" for="acc-lampu">
                            Lampu (Utama, Mundur, Sign, Stop)
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="ROTARY LAMP" id="acc-rotary">
                        <label class="form-check-label" for="acc-rotary">
                            Rotary Lamp
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="SENSOR PARKING" id="acc-sensor">
                        <label class="form-check-label" for="acc-sensor">
                            Sensor Parking
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="HORN SPEAKER" id="acc-horn">
                        <label class="form-check-label" for="acc-horn">
                            Horn Speaker
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="APAR 1 KG" id="acc-apar1">
                        <label class="form-check-label" for="acc-apar1">
                            APAR 1 KG
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="APAR 3 KG" id="acc-apar3">
                        <label class="form-check-label" for="acc-apar3">
                            APAR 3 KG
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="BEACON" id="acc-beacon">
                        <label class="form-check-label" for="acc-beacon">
                            Beacon
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="TELEMATIC" id="acc-telematic">
                        <label class="form-check-label" for="acc-telematic">
                            Telematic
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aksesoris Keamanan -->
        <div class="mb-4">
            <h6 class="text-success mb-3"><i class="fas fa-shield-alt me-2"></i>Aksesoris Keamanan</h6>
            <div class="row">
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="BLUE SPOT" id="acc-blue-spot">
                        <label class="form-check-label" for="acc-blue-spot">
                            Blue Spot
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="RED LINE" id="acc-red-line">
                        <label class="form-check-label" for="acc-red-line">
                            Red Line
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="WORK LIGHT" id="acc-work-light">
                        <label class="form-check-label" for="acc-work-light">
                            Work Light
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="BACK BUZZER" id="acc-back-buzzer">
                        <label class="form-check-label" for="acc-back-buzzer">
                            Back Buzzer
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="CAMERA AI" id="acc-camera-ai">
                        <label class="form-check-label" for="acc-camera-ai">
                            Camera AI
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="CAMERA MONITOR" id="acc-camera">
                        <label class="form-check-label" for="acc-camera">
                            Camera Monitor
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="SPEED LIMITER" id="acc-speed-limiter">
                        <label class="form-check-label" for="acc-speed-limiter">
                            Speed Limiter
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="LASER FORK" id="acc-laser-fork">
                        <label class="form-check-label" for="acc-laser-fork">
                            Laser Fork
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="VOICE ANNOUNCER" id="acc-voice">
                        <label class="form-check-label" for="acc-voice">
                            Voice Announcer
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aksesoris Lainnya -->
        <div class="mb-4">
            <h6 class="text-warning mb-3"><i class="fas fa-plus-circle me-2"></i>Aksesoris Lainnya</h6>
            <div class="row">
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="HORN KLASON" id="acc-klaxon">
                        <label class="form-check-label" for="acc-klaxon">
                            Horn Klason
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="BIO METRIC" id="acc-bio-metric">
                        <label class="form-check-label" for="acc-bio-metric">
                            Bio Metric
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="ACRYLIC" id="acc-acrylic">
                        <label class="form-check-label" for="acc-acrylic">
                            Acrylic
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="FIRST AID KIT" id="acc-p3k">
                        <label class="form-check-label" for="acc-p3k">
                            First Aid Kit
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="SPARK ARRESTOR" id="acc-spars">
                        <label class="form-check-label" for="acc-spars">
                            Spark Arrestor
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accessories[]" value="SAFETY BELT INTERLOCK" id="acc-safety-belt">
                        <label class="form-check-label" for="acc-safety-belt">
                            Safety Belt Interlock
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="accessories[]" value="MIRROR" id="acc-mirror"><label class="form-check-label" for="acc-mirror">Mirror / Spion</label></div></div>
                <div class="col-md-3 col-sm-6 mb-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="accessories[]" value="SAFETY BELT STANDAR" id="acc-safety-belt-std"><label class="form-check-label" for="acc-safety-belt-std">Safety Belt Standar</label></div></div>
                <div class="col-md-3 col-sm-6 mb-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="accessories[]" value="LOAD BACKREST" id="acc-load-backrest"><label class="form-check-label" for="acc-load-backrest">Load Backrest</label></div></div>
                <div class="col-md-3 col-sm-6 mb-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="accessories[]" value="FORKS" id="acc-forks"><label class="form-check-label" for="acc-forks">Forks</label></div></div>
                <div class="col-md-3 col-sm-6 mb-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="accessories[]" value="OVERHEAD GUARD" id="acc-overhead-guard"><label class="form-check-label" for="acc-overhead-guard">Overhead Guard</label></div></div>
                <div class="col-md-3 col-sm-6 mb-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="accessories[]" value="DOCUMENT HOLDER" id="acc-document-holder"><label class="form-check-label" for="acc-document-holder">Document Holder</label></div></div>
                <div class="col-md-3 col-sm-6 mb-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="accessories[]" value="TOOL KIT" id="acc-tool-kit"><label class="form-check-label" for="acc-tool-kit">Tool Kit</label></div></div>
                <div class="col-md-3 col-sm-6 mb-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="accessories[]" value="APAR BRACKET" id="acc-apar-bracket"><label class="form-check-label" for="acc-apar-bracket">APAR + Bracket</label></div></div>
                <div class="col-md-3 col-sm-6 mb-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="accessories[]" value="ANTI STATIC STRAP" id="acc-anti-static-strap"><label class="form-check-label" for="acc-anti-static-strap">Anti-Static Strap</label></div></div>
                <div class="col-md-3 col-sm-6 mb-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="accessories[]" value="WHEEL STOPPER CHOCK" id="acc-wheel-stopper-chock"><label class="form-check-label" for="acc-wheel-stopper-chock">Wheel Stopper / Chock</label></div></div>
                <div class="col-md-3 col-sm-6 mb-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="accessories[]" value="FORK EXTENSION" id="acc-fork-extension"><label class="form-check-label" for="acc-fork-extension">Fork Extension</label></div></div>
            </div>
        </div>

        <!-- Summary Aksesoris -->
        <div class="alert alert-light">
            <div class="row">
                <div class="col-md-6">
                    <small><strong>🔧 Total Aksesoris Terpilih:</strong> <span id="accessories-count">0</span></small>
                </div>
                <div class="col-md-6">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-clear-accessories">
                        <i class="fas fa-times me-1"></i>Clear All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-select-common">
                        <i class="fas fa-check me-1"></i>Select Common
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Verification Info -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-user-check me-2"></i>Informasi Verifikasi</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Diverifikasi Oleh</label>
                    <input type="text" class="form-control" id="verify-verified-by" name="verified_by" 
                           placeholder="Nama mekanik/teknisi" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Tanggal Verifikasi</label>
                    <input type="datetime-local" class="form-control" id="verify-verification-date" 
                           name="verification_date">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-0">
                    <label class="form-label">Hasil Verifikasi Unit <span class="text-danger">*</span></label>
                    <select class="form-select" id="verify-post-status" name="post_verification_status" required>
                        <option value="">Pilih hasil status unit</option>
                        <option value="1">Available Stock</option>
                        <option value="7">Rental Active</option>
                        <option value="8">Rental Daily</option>
                        <option value="10">Breakdown</option>
                    </select>
                    <small class="form-text text-muted">
                        Gunakan <strong>Breakdown</strong> jika unit hasil pengecekan dinyatakan rusak.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
