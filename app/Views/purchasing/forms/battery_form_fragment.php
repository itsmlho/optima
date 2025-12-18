<!-- Battery Form Fragment -->
<div class="row g-3">
    <div class="col-12 mb-2">
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="QuickAddModal.open('battery', 'battery_jenis');">
            <i class="fas fa-plus-circle me-1"></i>Add Battery
        </button>
    </div>
    <!-- Jenis Battery -->
    <div class="col-md-4">
        <label for="battery_jenis" class="form-label">Battery Type <span class="text-danger">*</span></label>
        <select id="battery_jenis" class="form-select select2-basic" required>
            <option value="">Select Type...</option>
            <?php 
            if (isset($baterais) && is_array($baterais)) {
                // Get unique jenis_baterai (no duplicates)
                $uniqueJenis = [];
                foreach ($baterais as $bat) {
                    $jenis = $bat['jenis_baterai'] ?? '';
                    if ($jenis && !in_array($jenis, $uniqueJenis)) {
                        $uniqueJenis[] = $jenis;
                    }
                }
                sort($uniqueJenis);
                foreach ($uniqueJenis as $jenis) {
                    echo '<option value="' . esc($jenis) . '">' . esc($jenis) . '</option>';
                }
            }
            ?>
        </select>
    </div>

    <!-- Merk Battery -->
    <div class="col-md-4">
        <label for="battery_merk" class="form-label">Battery Brand <span class="text-danger">*</span></label>
        <select id="battery_merk" class="form-select select2-basic" required disabled>
            <option value="">Select Type First...</option>
        </select>
    </div>

    <!-- Tipe Battery -->
    <div class="col-md-4">
        <label for="battery_tipe" class="form-label">Battery Model <span class="text-danger">*</span></label>
        <select id="battery_tipe" class="form-select select2-basic" required disabled>
            <option value="">Select Brand First...</option>
        </select>
    </div>

    <!-- Quantity -->
    <div class="col-md-6">
        <label for="battery_qty" class="form-label">Quantity <span class="text-danger">*</span></label>
        <input type="number" id="battery_qty" class="form-control" min="1" value="1" required>
    </div>

    <!-- Notes -->
    <div class="col-6">
        <label for="battery_keterangan" class="form-label">Notes</label>
        <textarea id="battery_keterangan" class="form-control" rows="2" placeholder="Additional notes (optional)"></textarea>
    </div>
</div>

<!-- No inline script here - handled in purchasing.php main file -->
