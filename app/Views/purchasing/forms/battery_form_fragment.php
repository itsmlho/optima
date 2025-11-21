<!-- Battery Form Fragment -->
<div class="row g-3">
    <!-- Jenis Battery -->
    <div class="col-md-4">
        <label for="battery_jenis" class="form-label">Jenis Battery <span class="text-danger">*</span></label>
        <select id="battery_jenis" class="form-select select2-basic" required>
            <option value="">Pilih Jenis...</option>
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
        <label for="battery_merk" class="form-label">Merk Battery <span class="text-danger">*</span></label>
        <select id="battery_merk" class="form-select select2-basic" required disabled>
            <option value="">Pilih Jenis Dulu...</option>
        </select>
    </div>

    <!-- Tipe Battery -->
    <div class="col-md-4">
        <label for="battery_tipe" class="form-label">Tipe Battery <span class="text-danger">*</span></label>
        <select id="battery_tipe" class="form-select select2-basic" required disabled>
            <option value="">Pilih Merk Dulu...</option>
        </select>
    </div>

    <!-- Quantity -->
    <div class="col-md-6">
        <label for="battery_qty" class="form-label">Quantity <span class="text-danger">*</span></label>
        <input type="number" id="battery_qty" class="form-control" min="1" value="1" required>
    </div>

    <!-- Keterangan -->
    <div class="col-6">
        <label for="battery_keterangan" class="form-label">Keterangan</label>
        <textarea id="battery_keterangan" class="form-control" rows="2" placeholder="Catatan tambahan (optional)"></textarea>
    </div>
</div>

<!-- No inline script here - handled in purchasing.php main file -->
