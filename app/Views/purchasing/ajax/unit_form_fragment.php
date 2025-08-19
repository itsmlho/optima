<form id="unitFormFragment">
    <div class="form-section mb-3">
        <h5 class="section-header"><i class="fas fa-truck me-2"></i>Unit Specifications</h5>
        <div class="card-body p-4">
            <div class="row g-1">
                <div class="col-md-4">
                    <label for="jenis_unit" class="form-label">Departemen <span class="text-danger">*</span></label>
                    <select id="jenis_unit" name="jenis_unit" class="form-select select2-basic" required>
                        <option value="">Select Departemen...</option>
                        <?php foreach ($departemens as $item): ?>
                            <option value="<?= esc($item['id_departemen']) ?>"><?= esc($item['nama_departemen']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="tipe_select" class="form-label">Tipe <span class="text-danger">*</span></label>
                    <select id="tipe_select" name="tipe" class="form-select select2-basic" required>
                        <option value="">Select Tipe...</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="jenis_select" class="form-label">Jenis <span class="text-danger">*</span></label>
                    <select id="jenis_select" name="tipe_unit_id" class="form-select select2-basic" required>
                        <option value="">Select Jenis...</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="unit_merk" class="form-label">Brand <span class="text-danger">*</span></label>
                    <select id="unit_merk" name="merk_unit" class="form-select select2-basic" required>
                        <option value="">Select Brand...</option>
                        <?php foreach ($merks as $item): ?>
                            <option value="<?= esc($item['id_model_unit']) ?>" data-merk="<?= esc($item['merk_unit']) ?>"><?= esc($item['merk_unit']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="unit_model" class="form-label">Model <span class="text-danger">*</span></label>
                    <select id="unit_model" name="model_unit_id" class="form-select select2-basic" required>
                        <option value="">Select Brand First</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="unit_tahun" class="form-label">Year <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="tahun_unit" id="unit_tahun" value="<?= date('Y') ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="unit_kapasitas" class="form-label">Capacity <span class="text-danger">*</span></label>
                    <select id="unit_kapasitas" name="kapasitas_id" class="form-select select2-basic" required>
                        <option value="">Select Capacity...</option>
                        <?php foreach ($kapasitas as $item): ?>
                            <option value="<?= esc($item['id_kapasitas']) ?>"><?= esc($item['kapasitas_unit']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="unit_kondisi" class="form-label">Sale Condition <span class="text-danger">*</span></label>
                    <select id="unit_kondisi" name="kondisi_penjualan" class="form-select select2-basic" required>
                        <option value="Baru">New</option>
                        <option value="Bekas">Used</option>
                        <option value="Rekondisi">Reconditioned</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="unit_qty" class="form-label">Qty Unit <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="unit_qty" name="qty_duplicates" value="1" min="1" required>
                </div>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h5 class="section-header"><i class="fas fa-cogs me-2"></i>Component Details</h5>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="unit_mast" class="form-label">Mast Type</label>
                    <select id="unit_mast" name="mast_id" class="form-select select2-basic">
                        <option value="">Select Mast Type...</option>
                        <?php foreach ($masts as $item): ?>
                            <option value="<?= esc($item['id_mast']) ?>"><?= esc($item['tipe_mast']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="unit_engine" class="form-label">Engine Type</label>
                    <select id="unit_engine" name="mesin_id" class="form-select select2-basic">
                        <option value="">Select Engine Type...</option>
                        <?php foreach ($mesins as $item): ?>
                            <option value="<?= esc($item['id']) ?>"><?= esc('('.$item['merk_mesin'].') '.$item['model_mesin'].' | '.$item['bahan_bakar']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="unit_tire" class="form-label">Tire Type</label>
                    <select id="unit_tire" name="ban_id" class="form-select select2-basic">
                        <option value="">Select Tire Type...</option>
                        <?php foreach ($bans as $item): ?>
                            <option value="<?= esc($item['id_ban']) ?>"><?= esc($item['tipe_ban']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="unit_wheel" class="form-label">Wheel Type</label>
                    <select id="unit_wheel" name="roda_id" class="form-select select2-basic">
                        <option value="">Select Wheel Type...</option>
                        <?php foreach ($rodas as $item): ?>
                            <option value="<?= esc($item['id_roda']) ?>"><?= esc($item['tipe_roda']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="unit_valve" class="form-label">Valve Type</label>
                    <select id="unit_valve" name="valve_id" class="form-select select2-basic">
                        <option value="">Select Valve Type...</option>
                        <?php foreach ($valves as $item): ?>
                            <option value="<?= esc($item['id_valve']) ?>"><?= esc($item['jumlah_valve']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4" id="battery_type_container" style="display:none;">
                    <label for="unit_battery" class="form-label">Battery Type</label>
                    <select id="unit_battery" name="baterai_id" class="form-select select2-basic">
                        <option value="">Select Battery Type...</option>
                        <?php foreach ($baterais as $item): ?>
                            <option value="<?= esc($item['id']) ?>"><?= esc($item['tipe_baterai']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label for="keterangan" class="form-label">Notes</label>
                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4 text-end">
        <button type="button" id="saveUnitButton" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan Unit</button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Initialize Select2 with proper dropdown parent for modal
    $('.select2-basic').select2({
        theme: 'bootstrap-5',
        width: '100%',
        dropdownParent: $('#itemModal') // Fix dropdown position in modal
    });

    // Cascading dropdown: Departemen -> Tipe -> Jenis
    $(document).on('change', '#jenis_unit', function() {
        const selectedDept = $(this).val();
        const $tipeDropdown = $('#tipe_select');
        const $jenisDropdown = $('#jenis_select');
        
        // Reset dropdowns berikutnya
        $tipeDropdown.html('<option value="">Loading...</option>').prop('disabled', true);
        $jenisDropdown.html('<option value="">-- Pilih Tipe Dulu --</option>').prop('disabled', true);

        if (!selectedDept) {
            $tipeDropdown.html('<option value="">Select Tipe...</option>').prop('disabled', false);
            return;
        }

        // Load tipe berdasarkan departemen
        $.ajax({
            url: '<?= base_url('/purchasing/api/get-tipe-units') ?>',
            method: 'GET',
            data: { departemen: selectedDept },
            dataType: 'json',
            success: function(response) {
                console.log('Tipe response:', response); // Debug log
                $tipeDropdown.prop('disabled', false);
                
                if (response && response.success && response.data && response.data.length > 0) {
                    // Extract unique tipe values
                    const tipeSet = [...new Set(response.data
                        .map(r => r.tipe)
                        .filter(tipe => tipe && tipe.trim() !== '')
                    )];
                    
                    $tipeDropdown.html('<option value="">Select Tipe...</option>');
                    tipeSet.forEach(function(tipe) {
                        $tipeDropdown.append(`<option value="${tipe}">${tipe}</option>`);
                    });
                } else {
                    $tipeDropdown.html('<option value="">Tidak ada tipe tersedia</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading tipe:', error); // Debug log
                $tipeDropdown.html('<option value="">Gagal memuat tipe</option>').prop('disabled', false);
            }
        });
    });

    $(document).on('change', '#tipe_select', function() {
        const selectedDept = $('#jenis_unit').val();
        const selectedTipe = $(this).val();
        const $jenisDropdown = $('#jenis_select');
        
        $jenisDropdown.html('<option value="">Loading...</option>').prop('disabled', true);

        if (!selectedDept || !selectedTipe) {
            $jenisDropdown.html('<option value="">-- Pilih Tipe Dulu --</option>').prop('disabled', false);
            return;
        }

        // Load jenis berdasarkan departemen dan tipe
        $.ajax({
            url: '<?= base_url('/purchasing/api/get-tipe-units') ?>',
            method: 'GET',
            data: { departemen: selectedDept, tipe: selectedTipe },
            dataType: 'json',
            success: function(response) {
                console.log('Jenis response:', response); // Debug log
                $jenisDropdown.prop('disabled', false);
                
                if (response && response.success && response.data && response.data.length > 0) {
                    $jenisDropdown.html('<option value="">Select Jenis...</option>');
                    
                    // Group by jenis to avoid duplicates and use correct field
                    const jenisMap = {};
                    response.data.forEach(function(item) {
                        if (item.jenis && item.id_tipe_unit) {
                            jenisMap[item.jenis] = item.id_tipe_unit;
                        }
                    });
                    
                    // Add options using the jenis field (not nama_tipe_unit)
                    Object.keys(jenisMap).forEach(function(jenisText) {
                        $jenisDropdown.append(`<option value="${jenisMap[jenisText]}">${jenisText}</option>`);
                    });
                } else {
                    $jenisDropdown.html('<option value="">Tidak ada jenis tersedia</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading jenis:', error); // Debug log
                $jenisDropdown.html('<option value="">Gagal memuat jenis</option>').prop('disabled', false);
            }
        });
    });

    // Show/hide battery type based on electric unit
    $(document).on('change', '#jenis_select', function() {
        const selectedJenisText = $(this).find('option:selected').text().toUpperCase();
        const batteryContainer = $('#battery_type_container');

        if (selectedJenisText.includes('ELECTRIC')) {
            batteryContainer.slideDown();
        } else {
            batteryContainer.slideUp();
        }
    });
});
</script>
