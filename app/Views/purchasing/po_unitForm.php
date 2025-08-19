<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin: -1.5rem -1.5rem 2rem -1.5rem;
            border-radius: 0 0 20px 20px;
        }
        .form-section {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .section-header {
            background: linear-gradient(135deg,rgb(249, 249, 249) 0%,rgb(248, 248, 248) 100%);
            color: black;
            padding: 1rem 1.5rem;
            border-radius: 10px 10px 0 0;
            margin-bottom: 0;
        }
        .btn-action {
            border-radius: 8px;
        }
        .form-control:focus, .form-select:focus, .select2-container--bootstrap-5 .select2-selection--single:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center">
    <a href="<?= base_url('/purchasing/po-attachment') ?>" class="btn btn-light btn-action">
        <i class="fas fa-arrow-left me-2"></i>Back to List
    </a>
</div>

<div class="container-fluid">
    <form action="<?= $mode == "update" ? base_url('/purchasing/save-update-po-unit/'.($po['id_po'] ?? '')) : base_url('/purchasing/store-po-unit') ?>" method="post" id="poUnitForm">
        <?= csrf_field() ?>
        
        <!-- Basic Information Section -->
        <div class="form-section">
            <h5 class="section-header"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="no_po" class="form-label">PO Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="no_po" id="no_po" placeholder="Masukkan Nomor PO" value="<?= esc($po["no_po"] ?? "") ?>" required>
                        <small class="form-text text-muted">Contoh: PO-Unit-2024-12-0001</small>
                    </div>
                    <div class="col-md-4">
                        <label for="tanggal_po" class="form-label">PO Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal_po" value="<?= esc($po["tanggal_po"] ?? date("Y-m-d")) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="id_supplier" class="form-label">Supplier <span class="text-danger">*</span></label>
                        <select name="id_supplier" class="form-select select2-basic" required>
                            <option value="">Select Supplier...</option>
                            <?php foreach ($suppliers as $item): ?>
                                <option value="<?= $item['id_supplier'] ?>" <?= ($po["supplier_id"] ?? "") == $item["id_supplier"] ? "selected" : "" ?>><?= esc($item['nama_supplier']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="invoice_no" class="form-label">Invoice Number <span class="text-secondary">(optional)</span></label>
                        <input type="text" class="form-control" name="invoice_no" placeholder="Masukkan Invoice Number" value="<?= esc($po["invoice_no"] ?? "") ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="invoice_date" class="form-label">Invoice Date <span class="text-secondary">(optional)</span></label>
                        <input type="date" class="form-control" name="invoice_date" value="<?= esc($po["invoice_date"] ?? "") ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="bl_date" class="form-label">BL Date <span class="text-secondary">(optional)</span></label>
                        <input type="date" class="form-control" name="bl_date" value="<?= esc($po["bl_date"] ?? "") ?>">
                    </div>
                    <div class="col-12">
                        <label for="keterangan_po" class="form-label">Keterangan PO <span class="text-secondary">(optional)</span></label>
                        <input type="text" class="form-control" name="keterangan_po" placeholder="Masukkan Keterangan PO" value="<?= esc($po["keterangan_po"] ?? "") ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Unit Specifications Section -->
        <div class="form-section">
            <h5 class="section-header"><i class="fas fa-truck me-2"></i>Unit Specifications</h5>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="jenis_unit" class="form-label">Departemen <span class="text-danger">*</span></label>
                        <select id="jenis_unit" name="jenis_unit" class="form-select select2-basic" required>
                            <option value="">Select Departemen...</option>
                            <?php foreach ($departemens as $item): ?>
                                <option value="<?= esc($item['id_departemen']) ?>" <?= ($detail['jenis_unit'] ?? '') == $item['id_departemen'] ? 'selected' : '' ?>><?= esc($item['nama_departemen']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="tipe_select" class="form-label">Tipe <span class="text-danger">*</span></label>
                        <select id="tipe_select" name="tipe" class="form-select select2-basic" required>
                            <option value="">Select Tipe...</option>
                            <?php if (!empty($tipe_list) && !empty($detail['tipe'])): foreach($tipe_list as $tp): if($tp==='') continue; ?>
                                <option value="<?= esc($tp) ?>" <?= ($detail['tipe']??'')===$tp?'selected':''; ?>><?= esc($tp) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="jenis_select" class="form-label">Jenis <span class="text-danger">*</span></label>
                        <select id="jenis_select" name="tipe_unit_id" class="form-select select2-basic" required>
                            <option value="">Select Jenis...</option>
                            <?php /* Removed server-side pre-population to prevent duplicates; AJAX will fill */ ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="merk_unit" class="form-label">Brand <span class="text-danger">*</span></label>
                        <select id="merk_unit" name="merk_unit" class="form-select select2-basic" required>
                            <option value="">Select Brand...</option>
                            <?php foreach ($merks as $item): ?>
                                <option value="<?= esc($item['id_model_unit']) ?>" data-merk="<?= esc($item["merk_unit"]); ?>" <?= ($detail["merk_unit"] ?? "") == $item["id_model_unit"] ? "selected" : "" ?>><?= esc($item['merk_unit']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="model_unit_id" class="form-label">Model <span class="text-danger">*</span></label>
                        <select id="model_unit_id" name="model_unit_id" class="form-select select2-basic" required>
                            <option value="">Select Brand First</option>
                            <?php if(!empty($modelsunit)): ?>
                                <?php foreach ($modelsunit as $item): ?>
                                    <option value="<?= $item['id_model_unit'] ?>" <?= ($detail["model_unit_id"] ?? "") == $item["id_model_unit"] ? "selected" : "" ?>><?= esc($item['model_unit']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="tahun_unit" class="form-label">Year <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="tahun_unit" value="<?= esc($detail["tahun_po"] ?? date("Y")) ?>" placeholder="<?= date('Y') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="kapasitas_id" class="form-label">Capacity <span class="text-danger">*</span></label>
                        <select name="kapasitas_id" class="form-select select2-basic" required>
                            <option value="">Select Capacity...</option>
                            <?php foreach ($kapasitas as $item): ?>
                                <option value="<?= $item['id_kapasitas'] ?>" <?= ($detail["kapasitas_id"] ?? "") == $item["id_kapasitas"] ? "selected" : "" ?>><?= html_entity_decode($item['kapasitas_unit']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="kondisi_penjualan" class="form-label">Sale Condition <span class="text-danger">*</span></label>
                        <select name="kondisi_penjualan" class="form-select select2-basic">
                            <option value="Baru" <?= ($detail["status_penjualan"] ?? "") == "Baru" ? "selected" : "" ?>>New</option>
                            <option value="Bekas" <?= ($detail["status_penjualan"] ?? "") == "Bekas" ? "selected" : "" ?>>Used</option>
                            <option value="Rekondisi" <?= ($detail["status_penjualan"] ?? "") == "Rekondisi" ? "selected" : "" ?>>Reconditioned</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="qty_duplicates" class="form-label">Qty Unit <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="qty_duplicates" value="<?= $mode == "update" ? $qty_unit : 1 ?>" <?= $mode == "update" ? "disabled" : "" ?> placeholder="Qty for duplicates unit" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Component Details Section -->
        <div class="form-section">
            <h5 class="section-header"><i class="fas fa-cogs me-2"></i>Component Details</h5>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="mast_id" class="form-label">Mast Type</label>
                        <select name="mast_id" class="form-select select2-basic">
                            <option value="">Select Mast Type...</option>
                            <?php foreach ($masts as $item): ?>
                                <option value="<?= $item['id_mast'] ?>" <?= ($detail["mast_id"] ?? "") == $item["id_mast"] ? "selected" : "" ?>><?= esc($item['tipe_mast']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="mesin_id" class="form-label">Engine Type</label>
                        <select name="mesin_id" class="form-select select2-basic">
                            <option value="">Select Engine Type...</option>
                            <?php foreach ($mesins as $item): ?>
                                <option value="<?= $item['id'] ?>" <?= ($detail["mesin_id"] ?? "") == $item["id"] ? "selected" : "" ?>><?= esc("(".$item['merk_mesin'].") ".$item['model_mesin']." | ".$item['bahan_bakar']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="ban_id" class="form-label">Tire Type</label>
                        <select name="ban_id" class="form-select select2-basic">
                            <option value="">Select Tire Type...</option>
                            <?php foreach ($bans as $item): ?>
                                <option value="<?= $item['id_ban'] ?>" <?= ($detail["ban_id"] ?? "") == $item["id_ban"] ? "selected" : "" ?>><?= esc($item['tipe_ban']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="roda_id" class="form-label">Wheel Type</label>
                        <select name="roda_id" class="form-select select2-basic">
                            <option value="">Select Wheel Type...</option>
                            <?php foreach ($rodas as $item): ?>
                                <option value="<?= $item['id_roda'] ?>" <?= ($detail["roda_id"] ?? "") == $item["id_roda"] ? "selected" : "" ?>><?= esc($item['tipe_roda']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="valve_id" class="form-label">Valve Type</label>
                        <select name="valve_id" class="form-select select2-basic">
                            <option value="">Select Valve Type...</option>
                            <?php foreach ($valves as $item): ?>
                                <option value="<?= $item['id_valve'] ?>" <?= ($detail["valve_id"] ?? "") == $item["id_valve"] ? "selected" : "" ?>><?= esc($item['jumlah_valve']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4" id="battery_type_container" style="display: none;">
                        <label for="baterai_id" class="form-label">Battery Type</label>
                        <select name="baterai_id" class="form-select select2-basic">
                            <option value="">Select Battery Type...</option>
                            <?php foreach ($baterais as $item): ?>
                                <option value="<?= $item['id'] ?>" <?= ($detail["baterai_id"] ?? "") == $item["id"] ? "selected" : "" ?>><?= esc($item['tipe_baterai']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="keterangan" class="form-label">Notes <span class="text-secondary">(optional)</span></label>
                        <textarea class="form-control" name="keterangan" rows="3" placeholder="Additional notes or specifications"><?= esc($detail["keterangan"] ?? "") ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="d-flex justify-content-end mb-4">
            <a href="<?= base_url('/purchasing/po-unit') ?>" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save PO Unit
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2-basic').select2({
                theme: "bootstrap-5",
            });

            $('#merk_unit').on('change', function () {
                const selectedMerk = $(this).find(":selected").data("merk");
                const $modelDropdown = $('#model_unit_id');
                
                $modelDropdown.html('<option value="">Loading...</option>');

                if (!selectedMerk) {
                    $modelDropdown.html('<option value="">Select Brand First</option>');
                    return;
                }

                $.ajax({
                    url: `<?= base_url('purchasing/api/get_model_unit_merk') ?>`,
                    method: 'GET',
                    data:{ "merk":selectedMerk },
                    dataType: 'json',
                    success: function (response) {
                        $modelDropdown.html('<option value="">Select Model</option>');
                        response.data.forEach(item => {
                            $modelDropdown.append(`<option value="${item.id_model_unit}">${item.model_unit}</option>`);
                        });

                        // If in update mode, set the selected model after loading
                        <?php if ($mode == 'update' && !empty($detail['model_unit_id'])): ?>
                            $modelDropdown.val('<?= $detail["model_unit_id"] ?>').trigger('change');
                        <?php endif; ?>
                    },
                    error: function () {
                        $modelDropdown.html('<option value="">Failed to load models</option>');
                    }
                });
            });

            // Trigger change event on page load if in update mode to load models
            <?php if ($mode == 'update' && !empty($detail['merk_unit'])): ?>
                $('#merk_unit').trigger('change');
            <?php endif; ?>

            // Listener untuk menampilkan/menyembunyikan field Battery Type
            $(document).on('change', '#jenis_unit', function() {
                // Ambil TEKS dari opsi yang dipilih dan ubah ke huruf besar
                const selectedJenisText = $(this).find('option:selected').text().toUpperCase();
                const batteryContainer = $('#battery_type_container');

                // Cek apakah teksnya adalah 'ELECTRIC'
                if (selectedJenisText === 'ELECTRIC') {
                    batteryContainer.slideDown(); // Tampilkan dengan animasi
                } else {
                    batteryContainer.slideUp(); // Sembunyikan dengan animasi
                }
            });
        });
    </script>
    <script>
    $(function(){
        const endpoint = '<?= base_url('purchasing/api/get-tipe-units') ?>';
        const $departemen = $('#jenis_unit');
        const $tipe = $('#tipe_select');
        const $jenis = $('#jenis_select');
        let preTipe = '<?= esc($detail['tipe'] ?? '') ?>';
        let preJenisId = '<?= esc($detail['tipe_unit_id'] ?? '') ?>';
        function resetTipe(){ $tipe.html('<option value="">Select Tipe...</option>').trigger('change'); }
        function resetJenis(){ $jenis.html('<option value="">Select Jenis...</option>').trigger('change'); }
        function loadTipe(){
            const dept = $departemen.val();
            resetTipe(); resetJenis();
            if(!dept){ return; }
            $.getJSON(endpoint, {departemen: dept}, function(res){
                if(!res.success) return;
                const tipeSet = [...new Set(res.data.map(r => r.tipe))];
                tipeSet.forEach(t => { if(t) $tipe.append(`<option value="${t}">${t}</option>`); });
                if(preTipe){ $tipe.val(preTipe).trigger('change'); }
            });
        }
        function loadJenis(){
            const dept = $departemen.val();
            const tipeVal = $tipe.val();
            resetJenis();
            if(!dept || !tipeVal){ return; }
            $.getJSON(endpoint, {departemen: dept, tipe: tipeVal}, function(res){
                if(!res.success) return;
                // Deduplicate by "jenis" label while preserving selected ID if present
                const map = {};
                res.data.forEach(r => { if(!r.jenis) return; const key = r.jenis.trim(); (map[key] = map[key] || []).push(r); });
                Object.keys(map).forEach(j => {
                    let row = map[j][0];
                    if(preJenisId){ const m = map[j].find(x => x.id_tipe_unit == preJenisId); if(m) row = m; }
                    const sel = (preJenisId && preJenisId == row.id_tipe_unit) ? 'selected' : '';
                    $jenis.append(`<option value="${row.id_tipe_unit}" ${sel}>${row.jenis}</option>`);
                });
                $jenis.trigger('change');
            });
        }
        $departemen.on('change', function(){ preTipe=''; preJenisId=''; loadTipe(); });
        $tipe.on('change', function(){ loadJenis(); });
        if($departemen.val()){ loadTipe(); }
    });
    </script>
<?= $this->endSection() ?>
