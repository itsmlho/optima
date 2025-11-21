<!-- Attachment Form Fragment -->
<div class="row g-3">
    <!-- Tipe -->
    <div class="col-md-4">
        <label for="att_tipe" class="form-label">Tipe Attachment <span class="text-danger">*</span></label>
        <select id="att_tipe" class="form-select select2-basic" required>
            <option value="">Pilih Tipe...</option>
            <?php 
            if (isset($attachments) && is_array($attachments)) {
                $uniqueTipe = array_unique(array_filter(array_map(fn($a) => $a['tipe'] ?? '', $attachments)));
                foreach ($uniqueTipe as $tipe) {
                    echo '<option value="' . esc($tipe) . '">' . esc($tipe) . '</option>';
                }
            }
            ?>
        </select>
    </div>

    <!-- Merk -->
    <div class="col-md-4">
        <label for="att_merk" class="form-label">Merk <span class="text-danger">*</span></label>
        <select id="att_merk" class="form-select select2-basic" required disabled>
            <option value="">Pilih Tipe Dulu...</option>
        </select>
    </div>

    <!-- Model -->
    <div class="col-md-4">
        <label for="att_model" class="form-label">Model <span class="text-danger">*</span></label>
        <select id="att_model" class="form-select select2-basic" required disabled>
            <option value="">Pilih Merk Dulu...</option>
        </select>
    </div>

    <!-- Quantity -->
    <div class="col-md-6">
        <label for="att_qty" class="form-label">Quantity <span class="text-danger">*</span></label>
        <input type="number" id="att_qty" class="form-control" min="1" value="1" required>
    </div>

    <!-- Keterangan -->
    <div class="col-6">
        <label for="att_keterangan" class="form-label">Keterangan</label>
        <textarea id="att_keterangan" class="form-control" rows="2" placeholder="Catatan tambahan (optional)"></textarea>
    </div>
</div>

<!-- No inline script here - handled in purchasing.php main file -->

