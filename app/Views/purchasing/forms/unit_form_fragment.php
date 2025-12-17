<!-- Unit Form Fragment -->
<div class="row g-3">
    <!-- Departemen -->
    <div class="col-md-6">
        <label for="unit_departemen" class="form-label">Departemen <span class="text-danger">*</span></label>
        <select id="unit_departemen" class="form-select select2-basic" data-master-type="departemen" required>
            <option value="">Pilih Departemen...</option>
            <option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Tambah Departemen Baru</option>
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
        <label for="unit_jenis" class="form-label">Jenis Unit <span class="text-danger">*</span></label>
        <select id="unit_jenis" class="form-select select2-basic" data-master-type="jenis_unit" required disabled>
            <option value="">Pilih Departemen Dulu...</option>
        </select>
    </div>

    <!-- Brand -->
    <div class="col-md-4">
        <label for="unit_merk" class="form-label">Brand <span class="text-danger">*</span></label>
        <select id="unit_merk" class="form-select select2-basic" data-master-type="brand" required>
            <option value="">Pilih Brand...</option>
            <option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Tambah Brand Baru</option>
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
            <option value="">Pilih Brand Dulu...</option>
        </select>
    </div>

    <!-- Tahun -->
    <div class="col-md-4">
        <label for="unit_tahun" class="form-label">Tahun <span class="text-danger">*</span></label>
        <input type="number" id="unit_tahun" class="form-control" min="1990" max="<?= date('Y') + 2 ?>" value="<?= date('Y') ?>" required>
    </div>

    <!-- Kapasitas -->
    <div class="col-md-4">
        <label for="unit_kapasitas" class="form-label">Kapasitas <span class="text-danger">*</span></label>
        <select id="unit_kapasitas" class="form-select select2-basic" data-master-type="kapasitas" required>
            <option value="">Pilih Kapasitas...</option>
            <option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Tambah Kapasitas Baru</option>
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
        <label for="unit_kondisi" class="form-label">Kondisi <span class="text-danger">*</span></label>
        <select id="unit_kondisi" class="form-select select2-basic" required>
            <option value="Baru">Baru</option>
            <option value="Bekas">Bekas</option>
            <option value="Rekondisi">Rekondisi</option>
        </select>
    </div>

    <!-- Quantity -->
    <div class="col-md-4">
        <label for="unit_qty" class="form-label">Quantity <span class="text-danger">*</span></label>
        <input type="number" id="unit_qty" class="form-control" min="1" value="1" required>
    </div>

    <div class="col-12"><hr class="my-3"></div>
    <div class="col-12"><h6 class="text-muted"><i class="fas fa-cogs me-2"></i>Komponen (Optional)</h6></div>

    <!-- Mast -->
    <div class="col-md-4">
        <label for="unit_mast" class="form-label">Mast Type</label>
        <select id="unit_mast" class="form-select select2-basic" data-master-type="mast">
            <option value="">Pilih Mast...</option>
            <option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Tambah Mast Baru</option>
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
            <option value="">Pilih Engine...</option>
            <option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Tambah Engine Baru</option>
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
            <option value="">Pilih Ban...</option>
            <option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Tambah Tire Baru</option>
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
            <option value="">Pilih Roda...</option>
            <option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Tambah Wheel Baru</option>
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
            <option value="">Pilih Valve...</option>
            <option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Tambah Valve Baru</option>
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
        <label for="unit_keterangan" class="form-label">Keterangan</label>
        <textarea id="unit_keterangan" class="form-control" rows="2" placeholder="Catatan tambahan (optional)"></textarea>
    </div>
</div>

<script>
// Helper functions for unit form
function addModelForBrand() {
    const brandSelect = document.getElementById('unit_merk');
    const selectedOption = brandSelect.options[brandSelect.selectedIndex];
    
    if (!brandSelect.value || !selectedOption) {
        Swal.fire('Perhatian', 'Silakan pilih Brand terlebih dahulu', 'warning');
        return;
    }
    
    const brandName = selectedOption.getAttribute('data-merk');
    QuickAddModal.open('model', 'unit_model', brandName);
}

function refreshModelDropdown() {
    const brandSelect = document.getElementById('unit_merk');
    const selectedOption = brandSelect.options[brandSelect.selectedIndex];
    
    if (!brandSelect.value || !selectedOption) {
        Swal.fire('Perhatian', 'Silakan pilih Brand terlebih dahulu', 'warning');
        return;
    }
    
    const brandName = selectedOption.getAttribute('data-merk');
    
    Swal.fire({
        title: 'Refreshing...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
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
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data berhasil direfresh',
                    timer: 1000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: () => {
            Swal.fire('Error', 'Gagal merefresh data', 'error');
        }
    });
}

function addJenisUnitForDepartemen() {
    const deptSelect = document.getElementById('unit_departemen');
    
    if (!deptSelect.value) {
        Swal.fire('Perhatian', 'Silakan pilih Departemen terlebih dahulu', 'warning');
        return;
    }
    
    QuickAddModal.open('jenis_unit', 'unit_jenis', null, deptSelect.value);
}

function refreshJenisUnitDropdown() {
    const deptSelect = document.getElementById('unit_departemen');
    
    if (!deptSelect.value) {
        Swal.fire('Perhatian', 'Silakan pilih Departemen terlebih dahulu', 'warning');
        return;
    }
    
    Swal.fire({
        title: 'Refreshing...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
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
                $(jenisSelect).empty().append('<option value="">Pilih Jenis Unit...</option>');
                
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
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data berhasil direfresh',
                    timer: 1000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire('Error', response.message || 'Gagal merefresh data', 'error');
            }
        },
        error: (xhr) => {
            console.error('Refresh error:', xhr);
            Swal.fire('Error', 'Gagal merefresh data', 'error');
        }
    });
}
</script>

