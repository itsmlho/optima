<!-- Unit Form Fragment -->
<div class="row g-3">
    <!-- Departemen -->
    <div class="col-md-6">
        <label for="unit_departemen" class="form-label">Departmen <span class="text-danger">*</span></label>
        <select id="unit_departemen" class="form-select select2-basic" data-master-type="departemen" required>
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
        <label for="unit_tahun" class="form-label">Year <span class="text-danger">*</span></label>
        <input type="number" id="unit_tahun" class="form-control" min="1990" max="<?= date('Y') + 2 ?>" value="<?= date('Y') ?>" required>
    </div>

    <!-- Kapasitas -->
    <div class="col-md-4">
        <label for="unit_kapasitas" class="form-label">Capacity <span class="text-danger">*</span></label>
        <select id="unit_kapasitas" class="form-select select2-basic" data-master-type="kapasitas" required>
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

    <div class="col-12"><hr class="my-3"></div>
    <div class="col-12"><h6 class="text-muted"><i class="fas fa-cogs me-2"></i>Components (Optional)</h6></div>

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

    <!-- Keterangan -->
    <div class="col-12">
        <label for="unit_keterangan" class="form-label">Notes</label>
        <textarea id="unit_keterangan" class="form-control" rows="2" placeholder="Additional notes (optional)"></textarea>
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

