<!-- Unit Form Fragment -->
<div class="row g-3">
    <!-- Departemen -->
    <div class="col-md-6">
        <label for="unit_departemen" class="form-label">Departmen</label>
        <select id="unit_departemen" class="form-select select2-basic" data-master-type="departemen">
            <option value="">Select Departmen...</option>
            <option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Add New Departmen</option>
            <option disabled>─────────────</option>
            <?php if (isset($departemens) && is_array($departemens)): ?>
                <?php foreach ($departemens as $dept): ?>
                    <option value="<?= $dept['id_departemen'] ?>"><?= esc($dept['nama_departemen']) ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <!-- Jenis Unit -->
    <div class="col-md-6">
        <label for="unit_jenis" class="form-label">Unit Type <span class="text-danger">*</span></label>
        <select id="unit_jenis" class="form-select select2-basic" data-master-type="jenis_unit" required disabled>
            <option value="">Select Departmen First...</option>
        </select>
    </div>

    <!-- Brand -->
    <div class="col-md-4">
        <label for="unit_merk" class="form-label">Brand <span class="text-danger">*</span></label>
        <select id="unit_merk" class="form-select select2-basic" data-master-type="brand" required>
            <option value="">Select Brand...</option>
            <option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Add New Brand</option>
            <option disabled>─────────────</option>
            <?php if (isset($merks) && is_array($merks)): ?>
                <?php foreach ($merks as $merk): ?>
                    <option value="<?= $merk['id_model_unit'] ?>" data-merk="<?= esc($merk['merk_unit']) ?>">
                        <?= esc($merk['merk_unit']) ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <!-- Model -->
    <div class="col-md-4">
        <label for="unit_model" class="form-label">Model <span class="text-danger">*</span></label>
        <select id="unit_model" class="form-select select2-basic" data-master-type="model" required disabled>
            <option value="">Select Brand First...</option>
        </select>
    </div>

    <!-- Tahun -->
    <div class="col-md-4">
        <label for="unit_tahun" class="form-label">Year</label>
        <input type="number" id="unit_tahun" class="form-control" min="1990" max="<?= date('Y') + 2 ?>" value="<?= date('Y') ?>" placeholder="Opsional — bisa dilengkapi saat verifikasi WH">
    </div>

    <!-- Kapasitas -->
    <div class="col-md-4">
        <label for="unit_kapasitas" class="form-label">Capacity</label>
        <select id="unit_kapasitas" class="form-select select2-basic" data-master-type="kapasitas">
            <option value="">Select Capacity...</option>
            <option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Add New Capacity</option>
            <option disabled>─────────────</option>
            <?php if (isset($kapasitas) && is_array($kapasitas)): ?>
                <?php foreach ($kapasitas as $kap): ?>
                    <option value="<?= $kap['id_kapasitas'] ?>"><?= html_entity_decode($kap['kapasitas_unit']) ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <!-- Kondisi -->
    <div class="col-md-4">
        <label for="unit_kondisi" class="form-label">Condition <span class="text-danger">*</span></label>
        <select id="unit_kondisi" class="form-select select2-basic" required>
            <option value="Baru">New</option>
            <option value="Bekas">Used</option>
            <option value="Rekondisi">Reconditioned</option>
        </select>
    </div>

    <!-- Quantity -->
    <div class="col-md-4">
        <label for="unit_qty" class="form-label">Quantity <span class="text-danger">*</span></label>
        <input type="number" id="unit_qty" class="form-control" min="1" value="1" required>
    </div>

    <div class="col-12">
        <label for="unit_vendor_spec_text" class="form-label">Spesifikasi vendor — paste utuh dari baris PI</label>
        <textarea id="unit_vendor_spec_text" class="form-control font-monospace small" rows="5" placeholder="Tempel teks deskripsi baris proforma invoice tanpa dirangkum (termasuk detail fork, mis. 1220mm fork, jika ada di PI)."></textarea>
        <div class="form-text">Kode/variant model mengikuti master <strong>Brand / Model</strong> di atas. Jika kode pabrik di PI berbeda, cukup tercermin di teks ini; untuk beda model nyata tambahkan baris unit terpisah di PO.</div>
    </div>
    <div class="col-12">
        <span class="form-label d-block mb-1">Isi paket (membantu form verifikasi gudang)</span>
        <div class="d-flex flex-wrap gap-3 align-items-start">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="pkg_fork_std" name="pkg_flags[]" value="fork_standard" checked>
                <label class="form-check-label" for="pkg_fork_std">Fork termasuk paket (standar pabrik)</label>
            </div>
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="pkg_battery" name="pkg_flags[]" value="battery"><label class="form-check-label" for="pkg_battery">Baterai</label></div>
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="pkg_charger" name="pkg_flags[]" value="charger"><label class="form-check-label" for="pkg_charger">Charger</label></div>
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="pkg_attachment" name="pkg_flags[]" value="attachment"><label class="form-check-label" for="pkg_attachment">Attachment</label></div>
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="pkg_acc" name="pkg_flags[]" value="accessories"><label class="form-check-label" for="pkg_acc">Aksesoris (lampu, sabuk, dll.)</label></div>
        </div>
        <div class="form-text mt-1"><i class="fas fa-info-circle text-primary me-1"></i>Departemen <strong>DIESEL</strong> atau <strong>GASOLINE</strong>: centang <strong>Baterai</strong> dan <strong>Charger</strong> dinonaktifkan (hanya relevan untuk unit listrik / ELECTRIC).</div>
        <div class="form-text mt-2"><strong>Fork:</strong> ukuran atau tipe khusus dari PI (mis. <em>1220mm fork</em>) harus ada di teks spesifikasi vendor di atas. Centang &quot;Fork termasuk paket&quot; jika baris PI menyertakan fork sesuai konfigurasi standar pabrik untuk model ini; jika fork non-standar atau hanya dijelaskan di PI, biarkan penjelasan di teks dan sesuaikan ekspektasi verifikasi gudang.</div>
        <div class="form-text mt-1 text-muted">Rincian aksesoris per item (daftar seperti spesifikasi quotation) tidak diisi di halaman PO — checklist lengkap ada saat <strong>verifikasi gudang</strong>. Centang &quot;Aksesoris&quot; di atas hanya menandai bahwa baris PI menyertakan paket aksesoris.</div>
    </div>

    <div class="col-12"><hr class="my-3"></div>
    <details class="col-12 mb-2"><summary class="fw-semibold text-muted cursor-pointer">Komponen lanjutan &amp; link master (opsional)</summary>
    <div class="row g-3 mt-1">

    <!-- Mast -->
    <div class="col-md-4">
        <label for="unit_mast" class="form-label">Mast Type</label>
        <select id="unit_mast" class="form-select select2-basic" data-master-type="mast">
            <option value="">Select Mast...</option>
            <option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Add New Mast</option>
            <option disabled>─────────────</option>
            <?php if (isset($masts) && is_array($masts)): ?>
                <?php foreach ($masts as $mast): ?>
                    <option value="<?= $mast['id_mast'] ?>" data-tinggi-mast="<?= esc($mast['tinggi_mast'] ?? '-') ?>">
                        <?= esc($mast['tipe_mast']) ?>
                        <?php if (!empty($mast['tinggi_mast'])): ?>
                            (<?= esc($mast['tinggi_mast']) ?>)
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        <small class="text-muted" id="unit_tinggi_mast_display"></small>
    </div>

    <!-- Engine -->
    <div class="col-md-4">
        <label for="unit_mesin" class="form-label">Engine Type</label>
        <select id="unit_mesin" class="form-select select2-basic" data-master-type="engine">
            <option value="">Select Engine...</option>
            <option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Add New Engine</option>
            <option disabled>─────────────</option>
            <?php if (isset($mesins) && is_array($mesins)): ?>
                <?php foreach ($mesins as $mesin): ?>
                    <option value="<?= $mesin['id'] ?>">
                        <?= esc("({$mesin['merk_mesin']}) {$mesin['model_mesin']}") ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <!-- Ban -->
    <div class="col-md-4">
        <label for="unit_ban" class="form-label">Tire Type</label>
        <select id="unit_ban" class="form-select select2-basic" data-master-type="tire">
            <option value="">Select Tire...</option>
            <option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Add New Tire</option>
            <option disabled>─────────────</option>
            <?php if (isset($bans) && is_array($bans)): ?>
                <?php foreach ($bans as $ban): ?>
                    <option value="<?= $ban['id_ban'] ?>"><?= esc($ban['tipe_ban']) ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <!-- Roda -->
    <div class="col-md-4">
        <label for="unit_roda" class="form-label">Wheel Type</label>
        <select id="unit_roda" class="form-select select2-basic" data-master-type="wheel">
            <option value="">Select Wheel...</option>
            <option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Add New Wheel</option>
            <option disabled>─────────────</option>
            <?php if (isset($rodas) && is_array($rodas)): ?>
                <?php foreach ($rodas as $roda): ?>
                    <option value="<?= $roda['id_roda'] ?>"><?= esc($roda['tipe_roda']) ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <!-- Valve -->
    <div class="col-md-4">
        <label for="unit_valve" class="form-label">Valve</label>
        <select id="unit_valve" class="form-select select2-basic" data-master-type="valve">
            <option value="">Select Valve...</option>
            <option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Add New Valve</option>
            <option disabled>─────────────</option>
            <?php if (isset($valves) && is_array($valves)): ?>
                <?php foreach ($valves as $valve): ?>
                    <option value="<?= $valve['id_valve'] ?>"><?= esc($valve['jumlah_valve']) ?> Valve</option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <?php if (!empty($baterais) && is_array($baterais)): ?>
    <div class="col-md-6">
        <label for="unit_baterai_id" class="form-label">Baterai (master)</label>
        <select id="unit_baterai_id" class="form-select select2-basic">
            <option value="">—</option>
            <?php foreach ($baterais as $b): ?>
                <option value="<?= (int)($b['id'] ?? 0) ?>"><?= esc(($b['merk_baterai'] ?? '') . ' ' . ($b['tipe_baterai'] ?? '') . ' ' . ($b['jenis_baterai'] ?? '')) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php endif; ?>
    <?php if (!empty($chargers) && is_array($chargers)): ?>
    <div class="col-md-6">
        <label for="unit_charger_id" class="form-label">Charger (master)</label>
        <select id="unit_charger_id" class="form-select select2-basic">
            <option value="">—</option>
            <?php foreach ($chargers as $c): ?>
                <option value="<?= (int)($c['id_charger'] ?? 0) ?>"><?= esc(($c['merk_charger'] ?? '') . ' ' . ($c['tipe_charger'] ?? '')) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php endif; ?>
    <?php if (!empty($attachments) && is_array($attachments)): ?>
    <div class="col-md-12">
        <label for="unit_attachment_id" class="form-label">Attachment (master)</label>
        <select id="unit_attachment_id" class="form-select select2-basic">
            <option value="">—</option>
            <?php foreach ($attachments as $a): ?>
                <option value="<?= (int)($a['id_attachment'] ?? 0) ?>"><?= esc(($a['tipe'] ?? '') . ' | ' . ($a['merk'] ?? '') . ' ' . ($a['model'] ?? '')) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php endif; ?>

    </div></details>

    <!-- Keterangan -->
    <div class="col-12">
        <label for="unit_keterangan" class="form-label">Catatan singkat (opsional)</label>
        <textarea id="unit_keterangan" class="form-control" rows="2" placeholder="Catatan operasional singkat (bukan pengganti teks PI lengkap)"></textarea>
    </div>
</div>

<script>
// Helper functions for unit form
function addModelForBrand() {
    const brandSelect = document.getElementById('unit_merk');
    const selectedOption = brandSelect.options[brandSelect.selectedIndex];
    
    if (!brandSelect.value || !selectedOption) {
        OptimaNotify.warning('Please select a Brand first', 'Attention');
        return;
    }
    
    const brandName = selectedOption.getAttribute('data-merk');
    QuickAddModal.open('model', 'unit_model', brandName);
}

function refreshModelDropdown() {
    const brandSelect = document.getElementById('unit_merk');
    const selectedOption = brandSelect.options[brandSelect.selectedIndex];
    
    if (!brandSelect.value || !selectedOption) {
        OptimaNotify.warning('Please select a Brand first', 'Attention');
        return;
    }
    
    const brandName = selectedOption.getAttribute('data-merk');
    
    OptimaPro.showLoading('Refreshing...');
    
    $.ajax({
        url: '<?= base_url('purchasing/refreshDropdownData') ?>',
        method: 'POST',
        data: { 
            type: 'model',
            brand: brandName
        },
        dataType: 'json',
        success: (response) => {
            if (response.success) {
                const modelSelect = document.getElementById('unit_model');
                QuickAddModal.updateDropdownOptions(modelSelect, response.data);
                OptimaPro.hideLoading();
                OptimaNotify.success('Data successfully refreshed', 'Success');
            } else {
                OptimaPro.hideLoading();
                OptimaNotify.error(response.message);
            }
        },
        error: () => {
            OptimaPro.hideLoading();
            OptimaNotify.error('Failed to refresh data');
        }
    });
}

function addJenisUnitForDepartemen() {
    const deptSelect = document.getElementById('unit_departemen');
    
    if (!deptSelect.value) {
        OptimaNotify.warning('Please select a Department first', 'Attention');
        return;
    }
    
    QuickAddModal.open('jenis_unit', 'unit_jenis', null, deptSelect.value);
}

function refreshJenisUnitDropdown() {
    const deptSelect = document.getElementById('unit_departemen');
    
    if (!deptSelect.value) {
        OptimaNotify.warning('Please select a Department first', 'Attention');
        return;
    }
    
    OptimaPro.showLoading('Refreshing...');
    
    // Use existing endpoint that's already working
    $.ajax({
        url: '<?= base_url('purchasing/api/get-tipe-units') ?>',
        method: 'GET',
        data: { 
            id_departemen: deptSelect.value
        },
        dataType: 'json',
        success: (response) => {
            if (response.success) {
                const jenisSelect = document.getElementById('unit_jenis');
                
                // Clear and rebuild options
                $(jenisSelect).empty().append('<option value="">Select Unit Type...</option>');
                
                if (response.data && response.data.length > 0) {
                    response.data.forEach(item => {
                        const option = new Option(
                            `${item.tipe} - ${item.jenis}`, 
                            item.id_tipe_unit
                        );
                        jenisSelect.add(option);
                    });
                }
                
                // Refresh Select2
                if ($(jenisSelect).hasClass('select2-hidden-accessible')) {
                    $(jenisSelect).trigger('change.select2');
                }
                
                OptimaPro.hideLoading();
                OptimaNotify.success('Data successfully refreshed', 'Success');
            } else {
                OptimaPro.hideLoading();
                OptimaNotify.error(response.message || 'Failed to refresh data');
            }
        },
        error: (xhr) => {
            console.error('Refresh error:', xhr);
            OptimaPro.hideLoading();
            OptimaNotify.error('Failed to refresh data');
        }
    });
}
</script>

