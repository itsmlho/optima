<form id="attachmentFormFragment">
    <div class="form-section mb-3">
        <h5 class="section-header"><i class="fas fa-paperclip me-2"></i>Attachment Details</h5>
        <div class="card-body p-4">
            <div class="row g-3">
    <!-- CSS Fix untuk Select2 dropdown positioning di modal -->
    <style>
    .select2-container {
        z-index: 9999 !important;
    }
    .select2-dropdown {
        z-index: 9999 !important;
    }
    .modal .select2-container--bootstrap-5 .select2-selection {
        position: relative;
        z-index: 1;
    }
    </style>
    <div class="card-body p-4">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Tipe Item <span class="text-danger">*</span></label>
            <select name="po_type" id="att_po_type" class="form-select">
                <option value="">-- Pilih Tipe --</option>
                <option value="Attachment">Attachment</option>
                <option value="Battery">Battery</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Quantity <span class="text-danger">*</span></label>
            <input type="number" name="qty" id="att_qty" class="form-control" value="1" min="1">
        </div>

        <!-- Attachment Fields (cascading selects) -->
        <div class="col-12 attachment-fields" style="display:none;">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tipe Attachment <span class="text-danger">*</span></label>
                    <select id="att_tipe" name="att_tipe" class="form-select select2-basic">
                        <option value="">-- Pilih Tipe --</option>
                        <?php if(!empty($attachments)): 
                            $tipeList = array_unique(array_filter(array_map(fn($a)=>$a['tipe']??'', $attachments))); 
                            foreach($tipeList as $tp): ?>
                            <option value="<?= esc($tp) ?>"><?= esc($tp) ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Merk <span class="text-danger">*</span></label>
                    <select id="att_merk" name="att_merk" class="form-select select2-basic">
                        <option value="">-- Pilih Tipe Dulu --</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Model <span class="text-danger">*</span></label>
                    <select name="attachment_id" id="att_model" class="form-select select2-basic">
                        <option value="">-- Pilih Merk Dulu --</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Battery Fields -->
        <div class="col-md-6 battery-fields" style="display: none;">
            <label class="form-label">Battery Type <span class="text-danger">*</span></label>
            <select name="baterai_id" id="att_battery_type" class="form-select">
                <option value="">-- Pilih Baterai --</option>
                <?php foreach ($baterais as $item): ?>
                    <option value="<?= esc($item['id']) ?>"><?= esc($item['tipe_baterai']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6 battery-fields" style="display: none;">
            <label class="form-label">Charger Type <span class="text-danger">*</span></label>
            <select name="charger_id" id="att_charger_type" class="form-select">
                <option value="">-- Pilih Charger --</option>
                <?php foreach ($chargers as $item): ?>
                    <option value="<?= esc($item['id_charger']) ?>"><?= esc($item['tipe_charger']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="col-12">
            <label class="form-label">Keterangan</label>
            <textarea class="form-control" name="keterangan" id="att_keterangan" rows="3"></textarea>
        </div>
    </div>
    
    <div class="mt-4 text-end">
        <button type="button" id="saveAttachmentButton" class="btn btn-primary">Simpan Item</button>
    </div>
</form>