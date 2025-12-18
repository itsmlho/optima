<!-- Charger Form Fragment -->
<div class="row g-3">
    <div class="col-12 mb-2">
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="QuickAddModal.open('charger', 'charger_merk');">
            <i class="fas fa-plus-circle me-1"></i>Add Charger
        </button>
    </div>
    <!-- Merk Charger -->
    <div class="col-md-6">
        <label for="charger_merk" class="form-label">Charger Brand <span class="text-danger">*</span></label>
        <select id="charger_merk" class="form-select select2-basic" required>
            <option value="">Select Brand...</option>
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
        <label for="charger_model" class="form-label">Charger Model <span class="text-danger">*</span></label>
        <select id="charger_model" class="form-select select2-basic" required disabled>
            <option value="">Select Brand First...</option>
        </select>
    </div>

    <!-- Quantity -->
    <div class="col-md-6">
        <label for="charger_qty" class="form-label">Quantity <span class="text-danger">*</span></label>
        <input type="number" id="charger_qty" class="form-control" min="1" value="1" required>
    </div>

    <!-- Notes -->
    <div class="col-6">
        <label for="charger_keterangan" class="form-label">Notes</label>
        <textarea id="charger_keterangan" class="form-control" rows="2" placeholder="Additional notes (optional)"></textarea>
    </div>
</div>

<!-- No inline script here - handled in purchasing.php main file -->
