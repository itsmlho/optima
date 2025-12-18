<!-- Attachment Form Fragment -->
<div class="row g-3">
    <div class="col-12 mb-2">
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="QuickAddModal.open('attachment_type', 'att_tipe');">
            <i class="fas fa-plus-circle me-1"></i>Add Attachment
        </button>
    </div>
    <!-- Tipe -->
    <div class="col-md-4">
        <label for="att_tipe" class="form-label">Attachment Type <span class="text-danger">*</span></label>
        <select id="att_tipe" class="form-select select2-basic" required>
            <option value="">Select Type...</option>
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
        <label for="att_merk" class="form-label">Brand <span class="text-danger">*</span></label>
        <select id="att_merk" class="form-select select2-basic" required disabled>
            <option value="">Select Brand First...</option>
        </select>
    </div>

    <!-- Model -->
    <div class="col-md-4">
        <label for="att_model" class="form-label">Model <span class="text-danger">*</span></label>
        <select id="att_model" class="form-select select2-basic" required disabled>
            <option value="">Select Merk First...</option>
        </select>
    </div>

    <!-- Quantity -->
    <div class="col-md-6">
        <label for="att_qty" class="form-label">Quantity <span class="text-danger">*</span></label>
        <input type="number" id="att_qty" class="form-control" min="1" value="1" required>
    </div>

    <!-- Notes  -->
    <div class="col-6">
        <label for="att_keterangan" class="form-label">Notes</label>
        <textarea id="att_keterangan" class="form-control" rows="2" placeholder="Additional notes (optional)"></textarea>
    </div>
</div>

<!-- No inline script here - handled in purchasing.php main file -->

