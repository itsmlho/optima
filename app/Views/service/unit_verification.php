<!-- Unit Verification Modal for Complete Action -->
<div class="modal fade" id="unitVerificationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>Unit Verification - <span id="unit-verification-title"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
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
                
                <form id="unitVerificationForm">
                    <input type="hidden" id="verify-work-order-id" name="work_order_id">
                    <input type="hidden" id="verify-unit-id" name="unit_id">
                    
                    <!-- Verifikasi Data Unit -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-truck me-2"></i>Verifikasi Data Unit - WO: <span id="wo-number-display">Loading...</span></h6>
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
                                        <td><input type="text" class="form-control form-control-sm" id="verify-pelanggan" name="pelanggan" placeholder="Nama pelanggan real" required></td>
                                        <td class="text-center"><input type="checkbox" class="form-check-input" id="check-pelanggan"></td>
                                    </tr>
                                    <tr>
                                        <td>Lokasi <span class="text-danger">*</span></td>
                                        <td><input type="text" class="form-control form-control-sm" id="db-lokasi" readonly></td>
                                        <td><input type="text" class="form-control form-control-sm" id="verify-lokasi" name="lokasi" placeholder="Lokasi real" required></td>
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
                                        <td><input type="text" class="form-control form-control-sm" id="verify-tinggi-mast" name="tinggi_mast" placeholder="Tinggi mast real"></td>
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
                                        <td><input type="text" class="form-control form-control-sm" id="db-hour-meter" readonly></td>
                                        <td><input type="number" class="form-control form-control-sm" id="verify-hm" name="hm" placeholder="Hour meter saat ini (contoh: 1250.5)" min="0" step="0.1"></td>
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
                                            <input class="form-check-input" type="checkbox" name="accessories[]" value="CAMERA" id="acc-camera">
                                            <label class="form-check-label" for="acc-camera">
                                                Camera
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
                                            <input class="form-check-input" type="checkbox" name="accessories[]" value="P3K" id="acc-p3k">
                                            <label class="form-check-label" for="acc-p3k">
                                                P3K
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="accessories[]" value="SPARS ARRESTOR" id="acc-spars">
                                            <label class="form-check-label" for="acc-spars">
                                                Spars Arrestor
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="accessories[]" value="SAFETY BELT INTERLOC" id="acc-safety-belt">
                                            <label class="form-check-label" for="acc-safety-belt">
                                                Safety Belt Interloc
                                            </label>
                                        </div>
                                    </div>
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
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="me-auto">
                    <small class="text-muted">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Field yang ditandai * wajib diisi
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" id="btn-print-verification">
                        <i class="fas fa-print me-1"></i>Print
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="btn-save-verification">
                        <i class="fas fa-check me-1"></i>Simpan & Complete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Attachment Modal -->
<div class="modal fade" id="addAttachmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Attachment Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h6 class="alert-heading mb-2">📋 Tambah Data Attachment</h6>
                    <p class="mb-0">Isi data attachment yang akurat sesuai kondisi di lapangan. Data ini akan disimpan ke inventory_attachment dengan status AVAILABLE.</p>
                </div>
                
                <form id="addAttachmentForm">
                    <input type="hidden" id="new-tipe-item" name="tipe_item" value="">
                    
                    <!-- Attachment Fields -->
                    <div id="attachment-fields">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jenis Attachment <span class="text-danger">*</span></label>
                                    <select class="form-select" id="new-attachment-id" name="attachment_id">
                                        <option value="">Pilih Jenis Attachment</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Serial Number Attachment <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="new-sn-attachment" name="sn_attachment" placeholder="Masukkan SN attachment">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" id="baterai-fields" style="display: none;">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Baterai</label>
                                <select class="form-select" id="new-baterai-id" name="baterai_id">
                                    <option value="">Pilih Baterai</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Serial Number Baterai</label>
                                <input type="text" class="form-control" id="new-sn-baterai" name="sn_baterai" placeholder="Masukkan SN baterai">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" id="charger-fields" style="display: none;">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Charger</label>
                                <select class="form-select" id="new-charger-id" name="charger_id">
                                    <option value="">Pilih Charger</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Serial Number Charger</label>
                                <input type="text" class="form-control" id="new-sn-charger" name="sn_charger" placeholder="Masukkan SN charger">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kelengkapan</label>
                                <select class="form-select" id="new-kelengkapan" name="kelengkapan">
                                    <option value="Lengkap">Lengkap</option>
                                    <option value="Tidak Lengkap">Tidak Lengkap</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kondisi Fisik</label>
                                <select class="form-select" id="new-kondisi-fisik" name="kondisi_fisik">
                                    <option value="Baik">Baik</option>
                                    <option value="Rusak Ringan">Rusak Ringan</option>
                                    <option value="Rusak Berat">Rusak Berat</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan Fisik</label>
                        <textarea class="form-control" id="new-catatan-fisik" name="catatan_fisik" rows="3" placeholder="Catatan kondisi fisik attachment..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btn-save-attachment">
                    <i class="fas fa-save me-1"></i>Simpan Attachment
                </button>
            </div>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    console.log('🔧 Unit Verification JavaScript loaded');
    
    // Load Unit Verification Data
    window.loadUnitVerificationData = function(workOrderId, woNumber) {
        console.log('🔍 Loading unit verification data for WO:', workOrderId, 'WO Number:', woNumber);
        
        // Reset modal first
        resetUnitVerificationModal();
        
        // Set modal title and WO number immediately
        let displayWoNumber = woNumber || workOrderId || 'Loading...';
        $('#unitVerificationModal').find('.modal-title').html(`<i class="fas fa-clipboard-check me-2"></i>Verifikasi Unit - ${displayWoNumber}`);
        $('#wo-number-display').text(woNumber || 'Loading...');
        
        $.ajax({
            url: '<?= base_url('service/work-orders/get-unit-verification-data') ?>',
            type: 'POST',
            data: { work_order_id: workOrderId },
            beforeSend: function() {
            },
            success: function(response) {
                console.log('📦 Unit verification data received:', response);
                
                if (response.success && response.data) {
                    let data = response.data;
                    
                    // Debug accessories data
                    console.log('🔍 Raw response data:', data);
                    console.log('🔍 Accessories from response:', data.accessories);
                    
                    // Update header with actual WO number and unit number from response
                    let actualWoNumber = data.work_order?.work_order_number || data.work_order?.wo_number || woNumber || workOrderId || 'N/A';
                    let unitNumber = data.unit?.no_unit || 'N/A';
                    
                    $('#unitVerificationModal').find('.modal-title').html(`<i class="fas fa-clipboard-check me-2"></i>Verifikasi Unit - WO: ${actualWoNumber} | Unit: ${unitNumber}`);
                    $('#wo-number-display').text(actualWoNumber);
                    
                    // Store work order ID
                    $('#verify-work-order-id').val(workOrderId);
                    
                    // Populate all verification fields
                    populateUnitVerificationFields(data);
                    
                } else {
                    showAlert('error', response.message || 'Gagal memuat data unit');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error loading unit verification data:', error);
                showAlert('error', 'Terjadi kesalahan saat memuat data unit');
            }
        });
    };

    // Populate Unit Verification Fields
    function populateUnitVerificationFields(data) {
        console.log('📝 Populating verification fields with data:', data);
        
        // Extract unit data from response
        let unitData = data.unit || {};
        let workOrderData = data.work_order || {};
        let attachmentData = data.attachment || {};
        
        console.log('📝 Unit data extracted:', unitData);
        
        // Set unit ID in hidden field
        if (unitData.id_inventory_unit) {
            $('#verify-unit-id').val(unitData.id_inventory_unit);
            console.log('📝 Unit ID set:', unitData.id_inventory_unit);
        }
        
        // Unit Information - Database values (db-*)
        $('#db-no-unit').val(unitData.no_unit || '');
        $('#db-pelanggan').val(unitData.pelanggan || 'N/A');
        $('#db-lokasi').val(unitData.lokasi || 'N/A');
        $('#db-departemen').val(unitData.departemen_name || '');
        $('#db-tipe-unit').val(unitData.tipe_unit_name || '');
        $('#db-model-unit').val(unitData.model_unit_name || '');
        $('#db-kapasitas-unit').val(unitData.kapasitas_name || '');
        $('#db-serial-number').val(unitData.serial_number || '');
        $('#db-tahun-unit').val(unitData.tahun_unit || '');
        $('#db-keterangan').val(unitData.keterangan || '');
        $('#db-hour-meter').val(unitData.hour_meter || '-');
        
        // Mesin Information
        $('#db-model-mesin').val(unitData.model_mesin_name || '');
        $('#db-sn-mesin').val(unitData.sn_mesin || '');
        $('#db-model-mast').val(unitData.model_mast_name || '');
        $('#db-sn-mast').val(unitData.sn_mast || '');
        $('#db-tinggi-mast').val(unitData.tinggi_mast || '');
        
        // Attachment Information
        $('#db-attachment').val(attachmentData.attachment_name || '');
        $('#db-sn-attachment').val(attachmentData.sn_attachment || '');
        $('#db-baterai').val(attachmentData.baterai_name || '');
        $('#db-sn-baterai').val(attachmentData.sn_baterai || '');
        $('#db-charger').val(attachmentData.charger_name || '');
        $('#db-sn-charger').val(attachmentData.sn_charger || '');
        
        // Pre-fill verification fields (verify-*) with database values
        // No Unit - always readonly and auto-filled from database
        $('#verify-no-unit').val(unitData.no_unit || '').attr('readonly', true);
        $('#verify-pelanggan').val(unitData.pelanggan || 'N/A').attr('readonly', true).addClass('bg-light');
        $('#verify-lokasi').val(unitData.lokasi || 'N/A').attr('readonly', true).addClass('bg-light');
        $('#verify-serial-number').val(unitData.serial_number || '');
        $('#verify-tahun-unit').val(unitData.tahun_unit || '');
        $('#verify-keterangan').val(unitData.keterangan || '');
        $('#verify-hm').val(unitData.hour_meter || '');
        
        // SN fields - auto-fill with database values
        $('#verify-sn-mesin').val(unitData.sn_mesin || '');
        $('#verify-sn-mast').val(unitData.sn_mast || '');
        $('#verify-tinggi-mast').val(unitData.tinggi_mast || '');
        $('#verify-sn-attachment').val(attachmentData.sn_attachment || '');
        $('#verify-sn-baterai').val(attachmentData.sn_baterai || '');
        $('#verify-sn-charger').val(attachmentData.sn_charger || '');
        
        // Populate dropdown options
        populateDropdownOptions(data.options);
        
        // Set selected values for dropdowns
        setSelectedDropdownValues(unitData, attachmentData);
        
        // Show/hide baterai and charger based on departemen
        handleBateraiChargerVisibility(unitData.departemen_name);
        
        // Auto-fill Diverifikasi Oleh from assigned staff (readonly)
        if (data.verified_by || data.assigned_staff) {
            $('#verify-verified-by').val(data.verified_by || data.assigned_staff || '').attr('readonly', true);
        }
        
        // Set current date for verification
        let now = new Date();
        let currentDateTime = now.getFullYear() + '-' + 
            String(now.getMonth() + 1).padStart(2, '0') + '-' + 
            String(now.getDate()).padStart(2, '0') + 'T' + 
            String(now.getHours()).padStart(2, '0') + ':' + 
            String(now.getMinutes()).padStart(2, '0');
        $('#verify-verification-date').val(currentDateTime);
        
        // Handle empty database fields - disable checkbox if DB is empty
        handleEmptyDatabaseFields();
        
        // No auto-checking - let user manually check what's correct
        // Only keep no-unit checkbox checked as it should always be readonly
        $('#check-no-unit').prop('checked', true).prop('disabled', true);
        
        // IMPORTANT: Auto-check pelanggan and lokasi as they are always correct from database
        // Make them readonly immediately and disable checkbox
        $('#check-pelanggan').prop('checked', true).prop('disabled', true);
        $('#check-lokasi').prop('checked', true).prop('disabled', true);
        $('#verify-pelanggan').attr('readonly', true).addClass('bg-light');
        $('#verify-lokasi').attr('readonly', true).addClass('bg-light');
        
        // Populate Aksesoris from database - IMPORTANT: This should auto-check existing accessories
        console.log('🔧 About to populate accessories:', data.accessories);
        populateUnitAccessories(data.accessories || []);
        
        // Setup tooltips for database fields
        setupDatabaseFieldTooltips();
        
        console.log('📝 Field population completed');
    }

    // Populate Unit Accessories
    function populateUnitAccessories(accessories) {
        console.log('🔧 Populating unit accessories:', accessories);
        
        // Clear all accessories checkboxes first
        $('input[name="accessories[]"]').prop('checked', false);
        
        // Check accessories that exist in database
        if (accessories && accessories.length > 0) {
            let checkedCount = 0;
            accessories.forEach(function(accessory) {
                let accessoryValue = accessory.name || accessory.accessory_name || accessory;
                console.log('🔍 Looking for accessory:', accessoryValue);
                
                // Try exact match first
                let checkbox = $(`input[name="accessories[]"][value="${accessoryValue}"]`);
                if (checkbox.length > 0) {
                    checkbox.prop('checked', true);
                    checkedCount++;
                    console.log('✅ Found exact match for:', accessoryValue);
                } else {
                    // Try partial match or alternative names
                    let found = false;
                    $('input[name="accessories[]"]').each(function() {
                        let availableValue = $(this).val();
                        if (availableValue.toLowerCase().includes(accessoryValue.toLowerCase()) || 
                            accessoryValue.toLowerCase().includes(availableValue.toLowerCase())) {
                            $(this).prop('checked', true);
                            checkedCount++;
                            found = true;
                            console.log('✅ Found partial match:', accessoryValue, '→', availableValue);
                            return false; // break
                        }
                    });
                    
                    if (!found) {
                        console.log('❌ No match found for:', accessoryValue);
                        console.log('Available options:', $('input[name="accessories[]"]').map(function() { return $(this).val(); }).get());
                    }
                }
            });
            console.log(`📊 Auto-checked ${checkedCount} accessories out of ${accessories.length}`);
        } else {
            console.log('ℹ️ No accessories found in database');
        }
        
        // Update accessories counter
        updateAccessoriesCount();
        
        console.log('✅ Accessories populated successfully');
    }

    // Update Accessories Count
    function updateAccessoriesCount() {
        let checkedCount = $('input[name="accessories[]"]:checked').length;
        $('#accessories-count').text(checkedCount);
    }

    // Event Handlers for Accessories
    $(document).on('change', 'input[name="accessories[]"]', function() {
        updateAccessoriesCount();
    });

    // Clear All Accessories
    $('#btn-clear-accessories').on('click', function() {
        $('input[name="accessories[]"]').prop('checked', false);
        updateAccessoriesCount();
        showAlert('info', 'Semua aksesoris telah di-uncheck');
    });

    // Select Common Accessories
    $('#btn-select-common').on('click', function() {
        // Select commonly used accessories (sama dengan Marketing/Kontrak)
        let commonAccessories = [
            'LAMPU UTAMA',
            'HORN SPEAKER',
            'APAR 1 KG',
            'BLUE SPOT',
            'BACK BUZZER'
        ];
        
        commonAccessories.forEach(function(accessory) {
            $(`input[name="accessories[]"][value="${accessory}"]`).prop('checked', true);
        });
        
        updateAccessoriesCount();
        showAlert('success', 'Aksesoris umum telah dipilih');
    });

    // Reset Unit Verification Modal
    function resetUnitVerificationModal() {
        console.log('🔄 Resetting unit verification modal');
        
        // Reset all form fields
        $('#unitVerificationForm')[0].reset();
        
        // Reset all checkboxes to unchecked (except no-unit which stays checked)
        $('input[id^="check-"]').prop('checked', false);
        $('#check-no-unit').prop('checked', true).prop('disabled', true);
        
        // Ensure pelanggan and lokasi are unchecked and editable
        $('#check-pelanggan').prop('checked', false);
        $('#check-lokasi').prop('checked', false);
        
        // Reset accessories checkboxes
        $('input[name="accessories[]"]').prop('checked', false);
        updateAccessoriesCount();
        
        // Remove readonly/disabled states from verify fields (except special ones)
        $('input[id^="verify-"], textarea[id^="verify-"], select[id^="verify-"]').each(function() {
            let fieldId = $(this).attr('id');
            
            // Keep no-unit and verified-by readonly
            if (fieldId !== 'verify-no-unit' && fieldId !== 'verify-verified-by') {
                if ($(this).is('select')) {
                    $(this).prop('disabled', false);
                } else {
                    $(this).attr('readonly', false);
                }
                $(this).removeClass('bg-light');
            }
        });
        
        // Clear all database fields
        $('input[id^="db-"], textarea[id^="db-"]').val('');
        
        // Reset dropdown options
        $('select[id^="verify-"]').each(function() {
            let selectElement = $(this);
            let placeholder = selectElement.find('option:first').text();
            selectElement.empty().append(`<option value="">${placeholder}</option>`);
        });
        
        // Hide baterai and charger rows
        $('#baterai-row, #sn-baterai-row, #charger-row, #sn-charger-row').hide();
        
        // Reset modal title
        $('#unitVerificationModal').find('.modal-title').html('<i class="fas fa-clipboard-check me-2"></i>Verifikasi Unit - Loading...');
        $('#wo-number-display').text('Loading...');
        
        console.log('✅ Modal reset completed');
    }


    // Populate dropdown options
    function populateDropdownOptions(options) {
        console.log('📝 Populating dropdown options:', options);
        
        // Populate Departemen dropdown
        if (options.departemen && options.departemen.length > 0) {
            let departemenSelect = $('#verify-departemen');
            departemenSelect.empty().append('<option value="">Pilih Departemen</option>');
            options.departemen.forEach(function(dept) {
                departemenSelect.append(`<option value="${dept.id}">${dept.name}</option>`);
            });
        }
        
        // Populate Tipe Unit dropdown
        if (options.tipe_unit && options.tipe_unit.length > 0) {
            let tipeUnitSelect = $('#verify-tipe-unit');
            tipeUnitSelect.empty().append('<option value="">Pilih Tipe Unit</option>');
            options.tipe_unit.forEach(function(tipe) {
                let displayName = tipe.jenis ? `${tipe.name} - ${tipe.jenis}` : tipe.name;
                tipeUnitSelect.append(`<option value="${tipe.id}">${displayName}</option>`);
            });
        }
        
        // Populate Model Unit dropdown
        if (options.model_unit && options.model_unit.length > 0) {
            let modelUnitSelect = $('#verify-model-unit');
            modelUnitSelect.empty().append('<option value="">Pilih Model Unit</option>');
            options.model_unit.forEach(function(model) {
                modelUnitSelect.append(`<option value="${model.id}">${model.name}</option>`);
            });
        }
        
        // Populate Kapasitas dropdown
        if (options.kapasitas && options.kapasitas.length > 0) {
            let kapasitasSelect = $('#verify-kapasitas-unit');
            kapasitasSelect.empty().append('<option value="">Pilih Kapasitas</option>');
            options.kapasitas.forEach(function(kapasitas) {
                kapasitasSelect.append(`<option value="${kapasitas.id}">${kapasitas.name}</option>`);
            });
        }
        
        // Populate Model Mesin dropdown
        if (options.model_mesin && options.model_mesin.length > 0) {
            let modelMesinSelect = $('#verify-model-mesin');
            modelMesinSelect.empty().append('<option value="">Pilih Model Mesin</option>');
            options.model_mesin.forEach(function(mesin) {
                modelMesinSelect.append(`<option value="${mesin.id}">${mesin.name}</option>`);
            });
        }
        
        // Populate Model Mast dropdown
        if (options.model_mast && options.model_mast.length > 0) {
            let modelMastSelect = $('#verify-model-mast');
            modelMastSelect.empty().append('<option value="">Pilih Model Mast</option>');
            options.model_mast.forEach(function(mast) {
                modelMastSelect.append(`<option value="${mast.id}">${mast.name}</option>`);
            });
        }
        
        // Initialize Select2 for attachment dropdown with AJAX
        $('#verify-attachment').select2({
            placeholder: '🔍 Ketik Serial Number / Tipe / Merk untuk mencari...',
            allowClear: true,
            dropdownParent: $('#unitVerificationModal'),
            width: '250px',
            minimumInputLength: 2, // Require at least 2 characters before searching
            language: {
                inputTooShort: function() {
                    return 'Ketik minimal 2 karakter untuk mencari...';
                },
                searching: function() {
                    return 'Mencari data...';
                },
                noResults: function() {
                    return 'Tidak ada data ditemukan';
                },
                loadingMore: function() {
                    return 'Memuat data...';
                }
            },
            ajax: {
                url: '<?= base_url('service/data-attachment/simple') ?>',
                dataType: 'json',
                delay: 300, // Increased delay for better performance
                data: function (params) {
                    return {
                        q: params.term,
                        type: 'attachment',
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    
                    if (data.success && data.data) {
                        return {
                            results: data.data.map(function(item) {
                                let displayText = '';
                                
                                // Format: Nama Item [SN: xxx] - Status/Lokasi
                                // Start with item name/label
                                displayText = item.label || '(Attachment)';
                                
                                // Add serial number in brackets
                                if (item.sn_attachment) {
                                    displayText += ` [SN: ${item.sn_attachment}]`;
                                } else {
                                    displayText += ` [Tanpa SN]`;
                                }
                                
                                // Add status/location
                                if (item.attachment_status === 'USED' && item.used_by_unit) {
                                    displayText += ` - TERPASANG DI UNIT ${item.used_by_unit}`;
                                } else if (item.attachment_status === 'AVAILABLE') {
                                    if (item.lokasi_penyimpanan) {
                                        displayText += ` - Lokasi: ${item.lokasi_penyimpanan}`;
                                    } else {
                                        displayText += ` - ✅ TERSEDIA`;
                                    }
                                } else if (item.attachment_status === 'MAINTENANCE') {
                                    displayText += ` - 🔧 MAINTENANCE`;
                                }
                                
                                return {
                                    id: item.id,
                                    text: displayText,
                                    data: item,
                                    disabled: false // Allow selection even if used
                                };
                            }),
                            pagination: {
                                more: (data.data.length >= 50)
                            }
                        };
                    }
                    return { results: [] };
                },
                cache: true // Enable caching for better performance
            },
            templateResult: function(item) {
                if (!item.id) return item.text;
                
                // Custom template with color coding
                let $result = $('<div style="padding: 5px 0;"></div>');
                let $text = $('<span></span>').text(item.text);
                
                if (item.data && item.data.attachment_status === 'USED' && item.data.used_by_unit) {
                    $text.css({
                        'color': '#ff6b6b',
                        'font-weight': '500'
                    });
                } else if (item.data && item.data.attachment_status === 'AVAILABLE') {
                    $text.css({
                        'color': '#28a745',
                        'font-weight': '500'
                    });
                } else if (item.data && item.data.attachment_status === 'MAINTENANCE') {
                    $text.css({
                        'color': '#ffa500',
                        'font-weight': '500'
                    });
                }
                
                $result.append($text);
                return $result;
            },
            templateSelection: function(item) {
                // Show shorter text when selected: Nama [SN: xxx]
                if (item.data) {
                    let selectedText = item.data.label || 'Attachment';
                    if (item.data.sn_attachment) {
                        // Extract first part of label if too long
                        let shortLabel = selectedText.length > 30 ? selectedText.substring(0, 30) + '...' : selectedText;
                        return `${shortLabel} [SN: ${item.data.sn_attachment}]`;
                    }
                    return selectedText.length > 40 ? selectedText.substring(0, 40) + '...' : selectedText;
                }
                return item.text;
            }
        });        // Initialize Select2 for baterai dropdown with AJAX
        $('#verify-baterai').select2({
            placeholder: '🔍 Ketik Serial Number / Merk / Tipe baterai...',
            allowClear: true,
            dropdownParent: $('#unitVerificationModal'),
            width: '250px',
            minimumInputLength: 2,
            language: {
                inputTooShort: function() {
                    return 'Ketik minimal 2 karakter untuk mencari...';
                },
                searching: function() {
                    return 'Mencari data...';
                },
                noResults: function() {
                    return 'Tidak ada data ditemukan';
                }
            },
            ajax: {
                url: '<?= base_url('service/data-attachment/simple') ?>',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        q: params.term,
                        type: 'battery',
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    
                    if (data.success && data.data) {
                        return {
                            results: data.data.map(function(item) {
                                let displayText = '';
                                
                                // Format: Nama Baterai [SN: xxx] - Status/Lokasi
                                displayText = item.label || '(Baterai)';
                                
                                // Add serial number in brackets
                                if (item.sn_baterai) {
                                    displayText += ` [SN: ${item.sn_baterai}]`;
                                } else {
                                    displayText += ` [Tanpa SN]`;
                                }
                                
                                // Add status/location
                                if (item.attachment_status === 'USED' && item.used_by_unit) {
                                    displayText += ` - TERPASANG DI UNIT ${item.used_by_unit}`;
                                } else if (item.attachment_status === 'AVAILABLE') {
                                    if (item.lokasi_penyimpanan) {
                                        displayText += ` - Lokasi: ${item.lokasi_penyimpanan}`;
                                    } else {
                                        displayText += ` - ✅ TERSEDIA`;
                                    }
                                } else if (item.attachment_status === 'MAINTENANCE') {
                                    displayText += ` - 🔧 MAINTENANCE`;
                                }
                                
                                return {
                                    id: item.id,
                                    text: displayText,
                                    data: item
                                };
                            }),
                            pagination: {
                                more: (data.data.length >= 50)
                            }
                        };
                    }
                    return { results: [] };
                },
                cache: true
            },
            templateResult: function(item) {
                if (!item.id) return item.text;
                
                let $result = $('<div style="padding: 5px 0;"></div>');
                let $text = $('<span></span>').text(item.text);
                
                if (item.data && item.data.attachment_status === 'AVAILABLE') {
                    $text.css({'color': '#28a745', 'font-weight': '500'});
                } else if (item.data && item.data.attachment_status === 'USED') {
                    $text.css({'color': '#ff6b6b', 'font-weight': '500'});
                }
                
                $result.append($text);
                return $result;
            },
            templateSelection: function(item) {
                // Show shorter text when selected: Nama [SN: xxx]
                if (item.data) {
                    let selectedText = item.data.label || 'Baterai';
                    if (item.data.sn_baterai) {
                        let shortLabel = selectedText.length > 30 ? selectedText.substring(0, 30) + '...' : selectedText;
                        return `${shortLabel} [SN: ${item.data.sn_baterai}]`;
                    }
                    return selectedText.length > 40 ? selectedText.substring(0, 40) + '...' : selectedText;
                }
                return item.text;
            }
        });        // Initialize Select2 for charger dropdown with AJAX
        $('#verify-charger').select2({
            placeholder: '🔍 Ketik Serial Number / Merk / Tipe charger...',
            allowClear: true,
            dropdownParent: $('#unitVerificationModal'),
            width: '250px',
            minimumInputLength: 2,
            language: {
                inputTooShort: function() {
                    return 'Ketik minimal 2 karakter untuk mencari...';
                },
                searching: function() {
                    return 'Mencari data...';
                },
                noResults: function() {
                    return 'Tidak ada data ditemukan';
                }
            },
            ajax: {
                url: '<?= base_url('service/data-attachment/simple') ?>',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        q: params.term,
                        type: 'charger',
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    
                    if (data.success && data.data) {
                        return {
                            results: data.data.map(function(item) {
                                let displayText = '';
                                
                                // Format: Nama Charger [SN: xxx] - Status/Lokasi
                                displayText = item.label || '(Charger)';
                                
                                // Add serial number in brackets
                                if (item.sn_charger) {
                                    displayText += ` [SN: ${item.sn_charger}]`;
                                } else {
                                    displayText += ` [Tanpa SN]`;
                                }
                                
                                // Add status/location
                                if (item.attachment_status === 'USED' && item.used_by_unit) {
                                    displayText += ` - TERPASANG DI UNIT ${item.used_by_unit}`;
                                } else if (item.attachment_status === 'AVAILABLE') {
                                    if (item.lokasi_penyimpanan) {
                                        displayText += ` - Lokasi: ${item.lokasi_penyimpanan}`;
                                    } else {
                                        displayText += ` - ✅ TERSEDIA`;
                                    }
                                } else if (item.attachment_status === 'MAINTENANCE') {
                                    displayText += ` - 🔧 MAINTENANCE`;
                                }
                                
                                return {
                                    id: item.id,
                                    text: displayText,
                                    data: item
                                };
                            }),
                            pagination: {
                                more: (data.data.length >= 50)
                            }
                        };
                    }
                    return { results: [] };
                },
                cache: true
            },
            templateResult: function(item) {
                if (!item.id) return item.text;
                
                let $result = $('<div style="padding: 5px 0;"></div>');
                let $text = $('<span></span>').text(item.text);
                
                if (item.data && item.data.attachment_status === 'AVAILABLE') {
                    $text.css({'color': '#28a745', 'font-weight': '500'});
                } else if (item.data && item.data.attachment_status === 'USED') {
                    $text.css({'color': '#ff6b6b', 'font-weight': '500'});
                }
                
                $result.append($text);
                return $result;
            },
            templateSelection: function(item) {
                // Show shorter text when selected: Nama [SN: xxx]
                if (item.data) {
                    let selectedText = item.data.label || 'Charger';
                    if (item.data.sn_charger) {
                        let shortLabel = selectedText.length > 30 ? selectedText.substring(0, 30) + '...' : selectedText;
                        return `${shortLabel} [SN: ${item.data.sn_charger}]`;
                    }
                    return selectedText.length > 40 ? selectedText.substring(0, 40) + '...' : selectedText;
                }
                return item.text;
            }
        });        console.log('📝 Dropdown options populated successfully');
    }

    // Set selected values for dropdowns
    function setSelectedDropdownValues(unitData, attachmentData) {
        console.log('📝 Setting selected dropdown values');
        
        // Set departemen
        if (unitData.departemen_id) {
            $('#verify-departemen').val(unitData.departemen_id);
        }
        
        // Set tipe unit
        if (unitData.tipe_unit_id) {
            $('#verify-tipe-unit').val(unitData.tipe_unit_id);
        }
        
        // Set model unit
        if (unitData.model_unit_id) {
            $('#verify-model-unit').val(unitData.model_unit_id);
        }
        
        // Set kapasitas
        if (unitData.kapasitas_unit_id) {
            $('#verify-kapasitas-unit').val(unitData.kapasitas_unit_id);
        }
        
        // Set model mesin
        if (unitData.model_mesin_id) {
            $('#verify-model-mesin').val(unitData.model_mesin_id);
        }
        
        // Set model mast
        if (unitData.model_mast_id) {
            $('#verify-model-mast').val(unitData.model_mast_id);
        }
        
        // Set attachment
        if (attachmentData.attachment_id) {
            $('#verify-attachment').val(attachmentData.attachment_id);
        }
        
        // Set baterai
        if (attachmentData.baterai_id) {
            $('#verify-baterai').val(attachmentData.baterai_id);
        }
        
        // Set charger
        if (attachmentData.charger_id) {
            $('#verify-charger').val(attachmentData.charger_id);
        }
        
        console.log('📝 Selected values set successfully');
    }

    // Modal event handlers
    $('#unitVerificationModal').on('hidden.bs.modal', function() {
        console.log('📝 Unit verification modal closed - resetting form');
        resetUnitVerificationModal();
    });
    
    $('#unitVerificationModal').on('show.bs.modal', function() {
        console.log('📝 Unit verification modal opening');
    });

    
    // Check dropdown fields for auto-check
    function checkDropdownFields() {
        $('select[id^="verify-"]').each(function() {
            let verifyField = $(this);
            let fieldName = verifyField.attr('id').replace('verify-', '');
            let dbField = $('#db-' + fieldName);
            
            if (dbField.length > 0) {
                let dbValue = dbField.val() ? dbField.val().trim() : '';
                let selectedText = verifyField.find('option:selected').text();
                
                console.log('🔍 Checking dropdown field:', fieldName, 'DB value:', dbValue, 'Selected text:', selectedText);
                
                // Auto-checking disabled - user must manually check
                console.log('📝 Auto-checking disabled for field:', fieldName);
            }
        });
    }

    // Handle empty database fields - disable checkbox if DB is empty
    function handleEmptyDatabaseFields() {
        $('input[id^="db-"], textarea[id^="db-"]').each(function() {
            let dbField = $(this);
            let fieldName = dbField.attr('id').replace('db-', '');
            let checkbox = $('#check-' + fieldName);
            let dbValue = dbField.val() ? dbField.val().trim() : '';
            
            // If database field is empty, disable the checkbox and add a note
            if (!dbValue && fieldName !== 'no-unit') {
                checkbox.prop('disabled', true);
                checkbox.closest('td').append('<br><small class="text-muted">Data kosong</small>');
                console.log('🚫 Disabled checkbox for empty field:', fieldName);
            } else {
                checkbox.prop('disabled', false);
                console.log('✅ Enabled checkbox for field with data:', fieldName);
            }
        });
        
        // Handle dropdown fields
        let dropdownFields = ['departemen', 'tipe-unit', 'model-unit', 'kapasitas-unit', 'model-mesin', 'model-mast', 'attachment', 'baterai', 'charger'];
        dropdownFields.forEach(function(fieldName) {
            let dbField = $('#db-' + fieldName);
            let checkbox = $('#check-' + fieldName);
            let dbValue = dbField.val() ? dbField.val().trim() : '';
            
            if (!dbValue) {
                checkbox.prop('disabled', true);
                checkbox.closest('td').append('<br><small class="text-muted">Data kosong</small>');
                console.log('🚫 Disabled checkbox for empty dropdown field:', fieldName);
            } else {
                checkbox.prop('disabled', false);
                console.log('✅ Enabled checkbox for dropdown field with data:', fieldName);
            }
        });
        
        // Always keep no-unit checkbox checked and disabled (readonly)
        $('#check-no-unit').prop('checked', true).prop('disabled', true);
        console.log('🔒 No-unit checkbox set to checked and disabled');
    }

    // Show/hide baterai and charger based on departemen
    function handleBateraiChargerVisibility(departemenName) {
        console.log('📝 Handling baterai/charger visibility for departemen:', departemenName);
        
        if (departemenName === 'ELECTRIC') {
            // Show baterai and charger fields for ELECTRIC departemen
            $('#baterai-row').show();
            $('#sn-baterai-row').show();
            $('#charger-row').show();
            $('#sn-charger-row').show();
            console.log('📝 Showing baterai/charger fields for ELECTRIC departemen');
        } else {
            // Hide baterai and charger fields for non-ELECTRIC departemen
            $('#baterai-row').hide();
            $('#sn-baterai-row').hide();
            $('#charger-row').hide();
            $('#sn-charger-row').hide();
            console.log('📝 Hiding baterai/charger fields for non-ELECTRIC departemen:', departemenName);
        }
    }

    function handleEmptyDatabaseFields() {
        $('input[id^="db-"], textarea[id^="db-"]').each(function() {
            let dbField = $(this);
            let fieldName = dbField.attr('id').replace('db-', '');
            let checkbox = $('#check-' + fieldName);
            
            // If database field is empty, disable checkbox and add visual indicator
            if (!dbField.val() || dbField.val().trim() === '') {
                checkbox.prop('disabled', true);
                checkbox.closest('td').addClass('text-muted');
                console.log('📝 Disabled checkbox for empty field:', fieldName);
            } else {
                checkbox.prop('disabled', false);
                checkbox.closest('td').removeClass('text-muted');
            }
        });
        
        // Handle dropdown fields
        let dropdownFields = ['departemen', 'tipe-unit', 'model-unit', 'kapasitas-unit', 'model-mesin', 'model-mast', 'attachment', 'baterai', 'charger'];
        dropdownFields.forEach(function(fieldName) {
            let dbField = $('#db-' + fieldName);
            let checkbox = $('#check-' + fieldName);
            
            if (!dbField.val() || dbField.val().trim() === '') {
                checkbox.prop('disabled', true);
                checkbox.closest('td').addClass('text-muted');
                console.log('📝 Disabled checkbox for empty dropdown field:', fieldName);
            } else {
                checkbox.prop('disabled', false);
                checkbox.closest('td').removeClass('text-muted');
            }
        });
        
        // Handle special fields like lokasi
        let specialFields = ['lokasi'];
        specialFields.forEach(function(fieldName) {
            let dbField = $('#db-' + fieldName);
            let checkbox = $('#check-' + fieldName);
            
            if (!dbField.val() || dbField.val().trim() === '' || dbField.val() === 'N/A') {
                checkbox.prop('disabled', true);
                checkbox.closest('td').addClass('text-muted');
                console.log('📝 Disabled checkbox for empty special field:', fieldName);
            } else {
                checkbox.prop('disabled', false);
                checkbox.closest('td').removeClass('text-muted');
            }
        });
    }

    // Handle departemen change to show/hide baterai and charger
    $(document).on('change', '#verify-departemen', function() {
        let selectedText = $(this).find('option:selected').text();
        console.log('📝 Departemen changed to:', selectedText);
        handleBateraiChargerVisibility(selectedText);
    });

    // Checkbox change handler for "Sesuai" functionality
    $(document).on('change', 'input[id^="check-"]', function() {
        let checkbox = $(this);
        let fieldName = checkbox.attr('id').replace('check-', '');
        
        // Special mapping for fields with different naming conventions
        let dbFieldId = 'db-' + fieldName;
        let verifyFieldId = 'verify-' + fieldName;
        
        // Handle special cases for field name mapping
        if (fieldName === 'hm') {
            dbFieldId = 'db-hour-meter';
        }
        
        let dbField = $('#' + dbFieldId);
        let verifyField = $('#' + verifyFieldId);
        
        console.log('📝 Checkbox changed:', fieldName, 'Checked:', checkbox.is(':checked'));
        console.log('📝 DB Field:', dbFieldId, 'Value:', dbField.val());
        console.log('📝 Verify Field:', verifyFieldId);
        
        // Prevent unchecking pelanggan and lokasi - they should always be readonly
        if (fieldName === 'pelanggan' || fieldName === 'lokasi') {
            checkbox.prop('checked', true);
            return false;
        }
        
        if (checkbox.is(':checked')) {
            // VALIDASI: Cek apakah database field ada isinya
            let dbValue = dbField.val();
            
            if (!dbValue || dbValue.trim() === '' || dbValue === '-' || dbValue === 'N/A') {
                // Database kosong, jangan allow checkbox
                checkbox.prop('checked', false);
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Database Kosong',
                    text: 'Field "' + fieldName + '" di database tidak memiliki nilai. Silakan isi manual di kolom Real Lapangan.',
                    timer: 3000
                });
                
                return false;
            }
            
            // Copy database value to verification field and make readonly/disabled
            
            // Check if it's a Select2 field (attachment, baterai, charger)
            if (verifyField.hasClass('select2-hidden-accessible')) {
                // For Select2 fields, we need to copy the text value
                let dbValue = dbField.val();
                
                // Set the input field value to match database (as text display)
                verifyField.val('').trigger('change'); // Clear first
                
                // Create a temporary option with the db value
                if (dbValue && dbValue.trim() !== '') {
                    let newOption = new Option(dbValue, dbValue, true, true);
                    verifyField.append(newOption).trigger('change');
                }
                
                // Disable Select2
                verifyField.prop('disabled', true).trigger('change.select2');
                console.log('📝 Disabled Select2:', fieldName, 'Value:', dbValue);
                
            } else if (verifyField.is('select')) {
                // For regular dropdown fields
                let dbValue = dbField.val();
                let foundOption = false;
                
                verifyField.find('option').each(function() {
                    if ($(this).text() === dbValue || $(this).val() === dbValue) {
                        verifyField.val($(this).val());
                        foundOption = true;
                        return false; // break loop
                    }
                });
                
                if (!foundOption && dbValue) {
                    // If exact match not found, try partial match
                    verifyField.find('option').each(function() {
                        if ($(this).text().includes(dbValue) || dbValue.includes($(this).text())) {
                            verifyField.val($(this).val());
                            return false; // break loop
                        }
                    });
                }
                
                verifyField.prop('disabled', true);
                console.log('📝 Disabled dropdown:', fieldName);
                
            } else {
                // For input/textarea fields
                verifyField.val(dbField.val());
                verifyField.attr('readonly', true);
                console.log('📝 Made readonly:', fieldName);
            }
            
            // Add visual indication that field is locked
            verifyField.addClass('bg-light');
            
        } else {
            // Allow editing (except for special readonly fields)
            if (fieldName !== 'no-unit' && fieldName !== 'verified-by') {
                
                // Check if it's a Select2 field
                if (verifyField.hasClass('select2-hidden-accessible')) {
                    // Enable Select2
                    verifyField.prop('disabled', false).trigger('change.select2');
                    console.log('📝 Enabled Select2:', fieldName);
                    
                } else if (verifyField.is('select')) {
                    verifyField.prop('disabled', false);
                    console.log('📝 Enabled dropdown:', fieldName);
                    
                } else {
                    verifyField.attr('readonly', false);
                    console.log('📝 Enabled editing:', fieldName);
                }
                
                verifyField.removeClass('bg-light');
            }
        }
    });

    // Print Verification button
    $('#btn-print-verification').on('click', function() {
        let workOrderId = $('#verify-work-order-id').val();
        if (workOrderId) {
            window.open('<?= base_url('service/work-orders/print-verification') ?>?wo_id=' + workOrderId, '_blank');
        } else {
            showAlert('error', 'Work Order ID tidak ditemukan');
        }
    });

    // Save Verification button
    $('#btn-save-verification').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        saveUnitVerification();
    });

    // Prevent form submission on Enter key
    $('#unitVerificationForm').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        saveUnitVerification();
        return false;
    });

    // Save Unit Verification function
    let isSubmitting = false;
    function saveUnitVerification() {
        // Prevent double submission
        if (isSubmitting) {
            console.log('⚠️ Form submission already in progress, ignoring duplicate request');
            return;
        }
        
        // Validate required fields before sending
        let workOrderId = $('#verify-work-order-id').val();
        let unitId = $('#verify-unit-id').val();
        
        console.log('🔍 Form validation - WO ID:', workOrderId, 'Unit ID:', unitId);
        
        if (!workOrderId || !unitId) {
            showAlert('error', 'Data Work Order atau Unit tidak lengkap. Silakan tutup modal dan coba lagi.');
            return;
        }
        
        // Disable button and set submitting flag
        isSubmitting = true;
        $('#btn-save-verification').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...');
        
        let formData = $('#unitVerificationForm').serialize();
        console.log('📋 Form data being sent:', formData);
        
        $.ajax({
            url: '<?= base_url('service/work-orders/save-unit-verification') ?>',
            type: 'POST',
            data: formData,
            beforeSend: function() {
            },
            success: function(response) {
                console.log('✅ Server response:', response);
                if (response.success) {
                    showAlert('success', 'Verifikasi berhasil disimpan');
                    $('#unitVerificationModal').modal('hide');
                    
                    // Refresh work orders table - check which tab is active and refresh accordingly
                    setTimeout(function() {
                        let tableRefreshed = false;
                        
                        // Try progressTable first (most common)
                        if (typeof window.progressTable !== 'undefined' && window.progressTable && typeof window.progressTable.ajax === 'object') {
                            window.progressTable.ajax.reload(null, false); // false = don't reset pagination
                            tableRefreshed = true;
                            console.log('✅ Progress table refreshed');
                        }
                        
                        // Also refresh closedTable if it exists (in case user switches tab)
                        if (typeof window.closedTable !== 'undefined' && window.closedTable && typeof window.closedTable.ajax === 'object') {
                            window.closedTable.ajax.reload(null, false);
                            console.log('✅ Closed table refreshed');
                        }
                        
                        // Fallback to workOrdersTable
                        if (!tableRefreshed && typeof window.workOrdersTable !== 'undefined' && window.workOrdersTable && typeof window.workOrdersTable.ajax === 'object') {
                            window.workOrdersTable.ajax.reload(null, false);
                            tableRefreshed = true;
                            console.log('✅ Work orders table refreshed');
                        }
                        
                        // Fallback to workOrderTable (backward compatibility)
                        if (!tableRefreshed && typeof window.workOrderTable !== 'undefined' && window.workOrderTable && typeof window.workOrderTable.ajax === 'object') {
                            window.workOrderTable.ajax.reload(null, false);
                            tableRefreshed = true;
                            console.log('✅ Work order table refreshed');
                        }
                        
                        // Fallback to dataTable
                        if (!tableRefreshed && typeof window.dataTable !== 'undefined' && window.dataTable && typeof window.dataTable.ajax === 'object') {
                            window.dataTable.ajax.reload(null, false);
                            tableRefreshed = true;
                            console.log('✅ Data table refreshed');
                        }
                        
                        // Fallback to jQuery DataTable
                        if (!tableRefreshed && typeof $('#progressWorkOrdersTable').DataTable === 'function') {
                            $('#progressWorkOrdersTable').DataTable().ajax.reload(null, false);
                            tableRefreshed = true;
                            console.log('✅ Progress table refreshed via jQuery');
                        }
                        
                        if (!tableRefreshed && typeof $('#closedWorkOrdersTable').DataTable === 'function') {
                            $('#closedWorkOrdersTable').DataTable().ajax.reload(null, false);
                            tableRefreshed = true;
                            console.log('✅ Closed table refreshed via jQuery');
                        }
                        
                        // Last resort: reload page
                        if (!tableRefreshed) {
                            console.log('⚠️ No DataTable found, reloading page...');
                            window.location.reload();
                        }
                    }, 500); // Small delay to ensure modal is closed
                } else {
                    showAlert('error', response.message || 'Gagal menyimpan verifikasi');
                    // Re-enable button on error
                    isSubmitting = false;
                    $('#btn-save-verification').prop('disabled', false).html('<i class="fas fa-check me-1"></i>Simpan & Complete');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error saving verification:', error);
                console.error('❌ Response text:', xhr.responseText);
                let errorMessage = 'Terjadi kesalahan saat menyimpan verifikasi';
                try {
                    let errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMessage = errorResponse.message;
                    }
                } catch (e) {
                    // Use default error message
                }
                showAlert('error', errorMessage);
                // Re-enable button on error
                isSubmitting = false;
                $('#btn-save-verification').prop('disabled', false).html('<i class="fas fa-check me-1"></i>Simpan & Complete');
            },
            complete: function() {
                // Reset submitting flag after a delay to prevent rapid clicking
                setTimeout(function() {
                    isSubmitting = false;
                }, 2000);
            }
        });
    }

    // Helper function for alerts (if not available globally)
    function showAlert(type, message) {
        // Use OptimaPro notification system if available
        if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
            let toastType = type === 'error' ? 'danger' : type;
            OptimaPro.showNotification(message, toastType);
        } else if (typeof window.showNotification === 'function') {
            // Fallback to global notification system
            let toastType = type === 'success' ? 'success' : 
                           type === 'error' ? 'danger' : 'info';
            window.showNotification(message, toastType);
        } else {
            // Fallback to local alert system
            let alertClass = type === 'success' ? 'alert-success' : 
                            type === 'error' ? 'alert-danger' : 'alert-info';
            
            let alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert" id="unitVerificationAlert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            $('#unitVerificationModal .modal-body').prepend(alertHtml);
            
            if (type === 'success') {
                setTimeout(function() {
                    $('#unitVerificationAlert').alert('close');
                }, 3000);
            }
        }
    }
    
    // Event handlers for + Tambah buttons
    $('#btn-add-attachment').on('click', function() {
        openAddAttachmentModal('attachment');
    });
    
    $('#btn-add-baterai').on('click', function() {
        openAddAttachmentModal('battery');
    });
    
    $('#btn-add-charger').on('click', function() {
        openAddAttachmentModal('charger');
    });
    
    // Auto-fill functionality when SN is selected
    $('#verify-attachment').on('select2:select', function(e) {
        const selectedData = e.params.data;
        if (selectedData && selectedData.data) {
            const attachment = selectedData.data;
            const sn = attachment.sn_attachment;
            const name = attachment.label;
            const kondisi = attachment.kondisi_fisik;
            
            // Check if attachment is currently USED by another unit
            if (attachment.attachment_status === 'USED' && attachment.used_by_unit && attachment.is_used) {
                const currentUnitNo = attachment.used_by_unit;
                const targetUnit = $('#verify-no-unit').val() || 'unit ini';
                
                // Show confirmation modal
                Swal.fire({
                    title: '⚠️ Attachment Sudah Terpasang',
                    html: `
                        <div class="text-start">
                            <p><strong>Attachment:</strong> ${name}</p>
                            <p><strong>Serial Number:</strong> ${sn || '-'}</p>
                            <p class="text-danger"><strong>Status Saat Ini:</strong> TERPASANG DI UNIT <strong>${currentUnitNo}</strong></p>
                            <hr>
                            <p class="text-warning">⚠️ <strong>Perhatian:</strong></p>
                            <ul class="text-start">
                                <li>Jika Anda lanjutkan, attachment akan <strong>DIPINDAHKAN</strong> dari Unit ${currentUnitNo}</li>
                                <li>Attachment akan otomatis <strong>DILEPAS</strong> dari Unit ${currentUnitNo}</li>
                                <li>Attachment akan <strong>DIPASANG</strong> ke ${targetUnit}</li>
                            </ul>
                            <p class="text-info">💡 Pastikan attachment memang akan dipindahkan secara fisik di lapangan.</p>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '✓ Ya, Pindahkan Attachment',
                    cancelButtonText: '✗ Batal',
                    reverseButtons: true,
                    width: '600px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // User confirmed - proceed with selection
                        proceedWithAttachmentSelection(attachment, sn, name);
                        
                        // Show success toast
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: `Attachment akan dipindahkan dari Unit ${currentUnitNo}`,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    } else {
                        // User cancelled - clear the selection
                        $('#verify-attachment').val(null).trigger('change');
                        console.log('❌ User cancelled attachment transfer');
                    }
                });
            } else {
                // Attachment is AVAILABLE - proceed normally
                proceedWithAttachmentSelection(attachment, sn, name);
            }
        }
    });
    
    // Helper function to proceed with attachment selection
    function proceedWithAttachmentSelection(attachment, sn, name) {
        // Fill database fields
        $('#db-attachment').val(name);
        if (sn) {
            $('#db-sn-attachment').val(sn);
        }
        
        // Auto-fill SN field if the SN field exists and is empty
        if ($('#verify-sn-attachment').length && $('#verify-sn-attachment').val() === '' && sn) {
            $('#verify-sn-attachment').val(sn);
        }
        
        // Update tooltips for filled fields
        updateSimpleTooltip($('#db-attachment'));
        if (sn) {
            updateSimpleTooltip($('#db-sn-attachment'));
        }
        
        console.log(`📝 Attachment selected: ${name} (SN: ${sn})`);
    }
    
    $('#verify-baterai').on('select2:select', function(e) {
        const selectedData = e.params.data;
        if (selectedData && selectedData.data) {
            const baterai = selectedData.data;
            const sn = baterai.sn_baterai;
            const name = baterai.label;
            const kondisi = baterai.kondisi_fisik;
            
            // Check if baterai is currently USED by another unit
            if (baterai.attachment_status === 'USED' && baterai.used_by_unit && baterai.is_used) {
                const currentUnitNo = baterai.used_by_unit;
                const targetUnit = $('#verify-no-unit').val() || 'unit ini';
                
                Swal.fire({
                    title: '⚠️ Baterai Sudah Terpasang',
                    html: `
                        <div class="text-start">
                            <p><strong>Baterai:</strong> ${name}</p>
                            <p><strong>Serial Number:</strong> ${sn || '-'}</p>
                            <p class="text-danger"><strong>Status Saat Ini:</strong> TERPASANG DI UNIT <strong>${currentUnitNo}</strong></p>
                            <hr>
                            <p class="text-warning">⚠️ <strong>Perhatian:</strong></p>
                            <ul class="text-start">
                                <li>Jika Anda lanjutkan, baterai akan <strong>DIPINDAHKAN</strong> dari Unit ${currentUnitNo}</li>
                                <li>Baterai akan otomatis <strong>DILEPAS</strong> dari Unit ${currentUnitNo}</li>
                                <li>Baterai akan <strong>DIPASANG</strong> ke ${targetUnit}</li>
                            </ul>
                            <p class="text-info">💡 Pastikan baterai memang akan dipindahkan secara fisik di lapangan.</p>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '✓ Ya, Pindahkan Baterai',
                    cancelButtonText: '✗ Batal',
                    reverseButtons: true,
                    width: '600px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        proceedWithBateraiSelection(baterai, sn, name);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: `Baterai akan dipindahkan dari Unit ${currentUnitNo}`,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    } else {
                        $('#verify-baterai').val(null).trigger('change');
                        console.log('❌ User cancelled baterai transfer');
                    }
                });
            } else {
                proceedWithBateraiSelection(baterai, sn, name);
            }
        }
    });
    
    // Helper function for baterai selection
    function proceedWithBateraiSelection(baterai, sn, name) {
        // Fill database fields
        $('#db-baterai').val(name);
        if (sn) {
            $('#db-sn-baterai').val(sn);
        }
        
        if ($('#verify-sn-baterai').length && $('#verify-sn-baterai').val() === '' && sn) {
            $('#verify-sn-baterai').val(sn);
        }
        
        // Update tooltips for filled fields
        updateSimpleTooltip($('#db-baterai'));
        if (sn) {
            updateSimpleTooltip($('#db-sn-baterai'));
        }
        
        console.log(`📝 Baterai selected: ${name} (SN: ${sn})`);
    }
    
    $('#verify-charger').on('select2:select', function(e) {
        const selectedData = e.params.data;
        if (selectedData && selectedData.data) {
            const charger = selectedData.data;
            const sn = charger.sn_charger;
            const name = charger.label;
            const kondisi = charger.kondisi_fisik;
            
            // Check if charger is currently USED by another unit
            if (charger.attachment_status === 'USED' && charger.used_by_unit && charger.is_used) {
                const currentUnitNo = charger.used_by_unit;
                const targetUnit = $('#verify-no-unit').val() || 'unit ini';
                
                Swal.fire({
                    title: '⚠️ Charger Sudah Terpasang',
                    html: `
                        <div class="text-start">
                            <p><strong>Charger:</strong> ${name}</p>
                            <p><strong>Serial Number:</strong> ${sn || '-'}</p>
                            <p class="text-danger"><strong>Status Saat Ini:</strong> TERPASANG DI UNIT <strong>${currentUnitNo}</strong></p>
                            <hr>
                            <p class="text-warning">⚠️ <strong>Perhatian:</strong></p>
                            <ul class="text-start">
                                <li>Jika Anda lanjutkan, charger akan <strong>DIPINDAHKAN</strong> dari Unit ${currentUnitNo}</li>
                                <li>Charger akan otomatis <strong>DILEPAS</strong> dari Unit ${currentUnitNo}</li>
                                <li>Charger akan <strong>DIPASANG</strong> ke ${targetUnit}</li>
                            </ul>
                            <p class="text-info">💡 Pastikan charger memang akan dipindahkan secara fisik di lapangan.</p>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '✓ Ya, Pindahkan Charger',
                    cancelButtonText: '✗ Batal',
                    reverseButtons: true,
                    width: '600px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        proceedWithChargerSelection(charger, sn, name);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: `Charger akan dipindahkan dari Unit ${currentUnitNo}`,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    } else {
                        $('#verify-charger').val(null).trigger('change');
                        console.log('❌ User cancelled charger transfer');
                    }
                });
            } else {
                proceedWithChargerSelection(charger, sn, name);
            }
        }
    });
    
    // Helper function for charger selection
    function proceedWithChargerSelection(charger, sn, name) {
        // Fill database fields
        $('#db-charger').val(name);
        if (sn) {
            $('#db-sn-charger').val(sn);
        }
        
        if ($('#verify-sn-charger').length && $('#verify-sn-charger').val() === '' && sn) {
            $('#verify-sn-charger').val(sn);
        }
        
        // Update tooltips for filled fields
        updateSimpleTooltip($('#db-charger'));
        if (sn) {
            updateSimpleTooltip($('#db-sn-charger'));
        }
        
        console.log(`📝 Charger selected: ${name} (SN: ${sn})`);
    }
    
    // Open Add Attachment Modal
    function openAddAttachmentModal(type) {
        console.log(`📝 Opening add ${type} modal`);
        
        // Set the type in hidden field
        $('#new-tipe-item').val(type);
        
        // Show/hide appropriate fields based on type
        if (type === 'attachment') {
            $('#attachment-fields').show();
            $('#baterai-fields').hide();
            $('#charger-fields').hide();
            $('#addAttachmentModal .modal-title').html('<i class="fas fa-plus-circle me-2"></i>Tambah Attachment Baru');
            // Load attachment master data
            loadMasterData('attachment', '#new-attachment-id');
        } else if (type === 'battery') {
            $('#attachment-fields').hide();
            $('#baterai-fields').show();
            $('#charger-fields').hide();
            $('#addAttachmentModal .modal-title').html('<i class="fas fa-plus-circle me-2"></i>Tambah Baterai Baru');
            // Load baterai master data
            loadMasterData('baterai', '#new-baterai-id');
        } else if (type === 'charger') {
            $('#attachment-fields').hide();
            $('#baterai-fields').hide();
            $('#charger-fields').show();
            $('#addAttachmentModal .modal-title').html('<i class="fas fa-plus-circle me-2"></i>Tambah Charger Baru');
            // Load charger master data
            loadMasterData('charger', '#new-charger-id');
        }
        
        // Reset form
        $('#addAttachmentForm')[0].reset();
        $('#new-tipe-item').val(type);
        
        // Show modal
        $('#addAttachmentModal').modal('show');
    }
    
    // Load master data for dropdown in modal
    function loadMasterData(type, selectElement) {
        let url = '';
        switch(type) {
            case 'attachment':
                url = '<?= base_url('service/master-attachment') ?>';
                break;
            case 'baterai':
                url = '<?= base_url('service/master-baterai') ?>';
                break;
            case 'charger':
                url = '<?= base_url('service/master-charger') ?>';
                break;
        }
        
        if (url) {
            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        const $select = $(selectElement);
                        $select.empty().append('<option value="">Pilih ' + type.charAt(0).toUpperCase() + type.slice(1) + '</option>');
                        
                        response.data.forEach(function(item) {
                            $select.append(`<option value="${item.id}">${item.text}</option>`);
                        });
                        
                        console.log(`✅ ${type} master data loaded: ${response.data.length} items`);
                    } else {
                        console.error(`❌ Failed to load ${type} master data:`, response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(`❌ Error loading ${type} master data:`, error);
                }
            });
        }
    }
    
    // Save new attachment
    $('#btn-save-attachment').on('click', function() {
        const formData = new FormData($('#addAttachmentForm')[0]);
        const type = $('#new-tipe-item').val();
        
        console.log('🔧 Debug Add Attachment:');
        console.log('Type from form:', type);
        console.log('Form data entries:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
        
        // Validate required fields
        let isValid = true;
        let errorMessage = '';
        
        if (type === 'attachment') {
            if (!$('#new-attachment-id').val()) {
                isValid = false;
                errorMessage = 'Jenis Attachment wajib dipilih';
            } else if (!$('#new-sn-attachment').val()) {
                isValid = false;
                errorMessage = 'Serial Number Attachment wajib diisi';
            }
        } else if (type === 'battery') {
            if (!$('#new-baterai-id').val()) {
                isValid = false;
                errorMessage = 'Jenis Baterai wajib dipilih';
            } else if (!$('#new-sn-baterai').val()) {
                isValid = false;
                errorMessage = 'Serial Number Baterai wajib diisi';
            }
        } else if (type === 'charger') {
            if (!$('#new-charger-id').val()) {
                isValid = false;
                errorMessage = 'Jenis Charger wajib dipilih';
            } else if (!$('#new-sn-charger').val()) {
                isValid = false;
                errorMessage = 'Serial Number Charger wajib diisi';
            }
        }
        
        if (!isValid) {
            alert(errorMessage);
            return;
        }
        
        // Add CSRF token
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        
        $.ajax({
            url: '<?= base_url('service/add-inventory-attachment') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#addAttachmentModal').modal('hide');
                    showAlert('success', response.message || 'Attachment berhasil ditambahkan!');
                    
                    // Refresh the appropriate dropdown
                    refreshAttachmentDropdown(type);
                    
                    // Auto-select the newly created item
                    setTimeout(function() {
                        if (type === 'attachment') {
                            $('#verify-attachment').val(response.data.id).trigger('change');
                        } else if (type === 'battery') {
                            $('#verify-baterai').val(response.data.id).trigger('change');
                        } else if (type === 'charger') {
                            $('#verify-charger').val(response.data.id).trigger('change');
                        }
                    }, 500);
                    
                } else {
                    showAlert('error', response.message || 'Gagal menambahkan attachment');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error adding attachment:', error);
                showAlert('error', 'Terjadi kesalahan saat menambahkan attachment');
            }
        });
    });
    
    // Refresh dropdown after adding new attachment
    function refreshAttachmentDropdown(type) {
        console.log(`📝 Refreshing ${type} dropdown`);
        
        let selectElement;
        let apiUrl;
        
        // Determine which dropdown and API to use
        if (type === 'attachment') {
            selectElement = $('#verify-attachment');
            apiUrl = '<?= base_url('service/data-attachment/simple') ?>';
        } else if (type === 'battery') {
            selectElement = $('#verify-baterai');
            apiUrl = '<?= base_url('service/data-attachment/simple') ?>'; // Same endpoint, filtered by type
        } else if (type === 'charger') {
            selectElement = $('#verify-charger');
            apiUrl = '<?= base_url('service/data-attachment/simple') ?>'; // Same endpoint, filtered by type
        }
        
        if (selectElement && apiUrl) {
            // Save current value
            const currentValue = selectElement.val();
            
            // Clear and reload options
            selectElement.empty().append('<option value="">Pilih...</option>');
            
            // Reload Select2 data
            selectElement.select2({
                placeholder: type === 'attachment' ? 'Ketik untuk mencari attachment...' : 
                           type === 'battery' ? 'Ketik untuk mencari baterai...' : 
                           'Ketik untuk mencari charger...',
                allowClear: true,
                minimumInputLength: 0,
                ajax: {
                    url: apiUrl,
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            type: type // Add type filter for backend
                        };
                    },
                    processResults: function (data) {
                        if (data.success && data.data) {
                            // Filter by type on frontend as well
                            let filteredData = data.data;
                            
                            if (type === 'battery') {
                                filteredData = data.data.filter(item => 
                                    item.label && (item.label.toLowerCase().includes('baterai') || 
                                                 item.label.toLowerCase().includes('battery'))
                                );
                            } else if (type === 'charger') {
                                filteredData = data.data.filter(item => 
                                    item.label && (item.label.toLowerCase().includes('charger') || 
                                                 item.label.toLowerCase().includes('cas'))
                                );
                            }
                            
                            return {
                                results: filteredData.map(function(item) {
                                    // Format: "SN123456 - Attachment Name"
                                    let displayText = item.label;
                                    if (item.sn_attachment) {
                                        displayText = `${item.sn_attachment} - ${displayText}`;
                                    } else if (item.sn_baterai) {
                                        displayText = `${item.sn_baterai} - ${displayText}`;
                                    } else if (item.sn_charger) {
                                        displayText = `${item.sn_charger} - ${displayText}`;
                                    }
                                    
                                    return {
                                        id: item.id,
                                        text: displayText,
                                        data: item
                                    };
                                })
                            };
                        }
                        return { results: [] };
                    },
                    cache: false // Disable cache to always get fresh data
                }
            });
            
            console.log(`✅ ${type} dropdown refreshed`);
        }
    }
    
    // Simple tooltip function for database fields
    function setupDatabaseFieldTooltips() {
        console.log('🔧 Setting up simple database field tooltips');
        
        // Database fields that need tooltips
        const dbFields = [
            '#db-attachment', '#db-sn-attachment', 
            '#db-baterai', '#db-sn-baterai', 
            '#db-charger', '#db-sn-charger'
        ];
        
        dbFields.forEach(function(fieldSelector) {
            const field = $(fieldSelector);
            
            // Update tooltip when value changes
            field.on('input change', function() {
                updateSimpleTooltip($(this));
            });
            
            // Initialize tooltip for current value
            updateSimpleTooltip(field);
        });
        
        console.log('✅ Simple database field tooltips setup completed');
    }
    
    // Update simple tooltip using title attribute
    function updateSimpleTooltip(field) {
        const value = field.val();
        const fieldId = field.attr('id');
        
        if (value && value.trim() !== '') {
            // Set simple title tooltip
            field.attr('title', value.trim());
            console.log(`📝 Simple tooltip set for ${fieldId}: ${value.trim()}`);
        } else {
            // Remove tooltip if field is empty
            field.removeAttr('title');
        }
    }
});
</script>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
.bg-light {
    background-color: #f8f9fa !important;
}

.form-control:read-only {
    background-color: #f8f9fa;
    opacity: 1;
}

.form-select:disabled {
    background-color: #f8f9fa;
    opacity: 1;
}

#check-no-unit:disabled {
    opacity: 0.6;
}

.text-muted {
    font-size: 0.75rem;
}

.modal-title {
    font-weight: 600;
}

#wo-number-display {
    font-weight: bold;
    color: #0d6efd;
}

/* Fix Select2 width to prevent layout breaking - ONLY for Unit Verification Modal */
#unitVerificationModal #verify-attachment,
#unitVerificationModal #verify-baterai,
#unitVerificationModal #verify-charger {
    max-width: 250px !important;
    width: 250px !important;
}

/* Select2 container width control - ONLY for Unit Verification Modal */
#unitVerificationModal .select2-container {
    max-width: 250px !important;
    width: 250px !important;
}

/* Allow text wrapping in Select2 selection - ONLY for Unit Verification Modal */
#unitVerificationModal .select2-container--default .select2-selection--single .select2-selection__rendered {
    white-space: normal !important;
    word-wrap: break-word;
    overflow-wrap: break-word;
    line-height: 1.2;
    padding: 8px 12px;
}

/* Allow text wrapping in dropdown results - ONLY for Unit Verification Modal */
#unitVerificationModal .select2-results__option {
    white-space: normal !important;
    word-wrap: break-word;
    overflow-wrap: break-word;
    line-height: 1.2;
    padding: 8px 12px;
}

/* Set fixed height for Select2 selection to accommodate wrapped text - ONLY for Unit Verification Modal */
#unitVerificationModal .select2-container--default .select2-selection--single {
    height: auto !important;
    min-height: 38px;
}

</style>
