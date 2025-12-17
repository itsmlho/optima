<!-- Charger Form Fragment -->
<div class="row g-3">
    <div class="col-12 mb-2">
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="QuickAddModal.open('charger', 'charger_merk');">
            <i class="fas fa-plus-circle me-1"></i>Tambah Charger Baru
        </button>
    </div>
    <!-- Merk Charger -->
    <div class="col-md-6">
        <label for="charger_merk" class="form-label">Merk Charger <span class="text-danger">*</span></label>
        <select id="charger_merk" class="form-select select2-basic" required>
            <option value="">Pilih Merk...</option>
            <?php 
            if (isset($chargers) && is_array($chargers)) {
                // Get unique merk_charger (no duplicates)
                $uniqueMerk = [];
                foreach ($chargers as $charger) {
                    $merk = $charger['merk_charger'] ?? '';
                    if ($merk && !in_array($merk, $uniqueMerk)) {
                        $uniqueMerk[] = $merk;
                    }
                }
                sort($uniqueMerk);
                foreach ($uniqueMerk as $merk) {
                    echo '<option value="' . esc($merk) . '">' . esc($merk) . '</option>';
                }
            }
            ?>
        </select>
    </div>

    <!-- Model Charger -->
    <div class="col-md-6">
        <label for="charger_model" class="form-label">Model Charger <span class="text-danger">*</span></label>
        <select id="charger_model" class="form-select select2-basic" required disabled>
            <option value="">Pilih Merk Dulu...</option>
        </select>
    </div>

    <!-- Quantity -->
    <div class="col-md-6">
        <label for="charger_qty" class="form-label">Quantity <span class="text-danger">*</span></label>
        <input type="number" id="charger_qty" class="form-control" min="1" value="1" required>
    </div>

    <!-- Keterangan -->
    <div class="col-6">
        <label for="charger_keterangan" class="form-label">Keterangan</label>
        <textarea id="charger_keterangan" class="form-control" rows="2" placeholder="Catatan tambahan (optional)"></textarea>
    </div>
</div>

<!-- No inline script here - handled in purchasing.php main file -->
