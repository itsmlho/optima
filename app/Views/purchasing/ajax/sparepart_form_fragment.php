<form id="sparepartForm">
    <div class="form-section mb-3">
        <h5 class="section-header"><i class="fas fa-tools me-2"></i>Sparepart Details</h5>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Sparepart <span class="text-danger">*</span></label>
                    <select id="sparepart_id" name="sparepart_id" class="form-select select2-basic" required>
                        <option value="">-- Pilih Sparepart --</option>
                        <?php foreach ($spareparts as $item): ?>
                            <option value="<?= esc($item['id_sparepart']) ?>"><?= esc($item['kode'] . ' - ' . $item['desc_sparepart']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="sparepart_qty" name="qty" value="1" min="1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Satuan <span class="text-danger">*</span></label>
                    <select id="sparepart_satuan" name="satuan" class="form-select select2-basic" required>
                        <option value="Pieces" selected>Pieces</option>
                        <option value="Rol">Rol</option>
                        <option value="Kaleng">Kaleng</option>
                        <option value="Set">Set</option>
                        <option value="Pak">Pak</option>
                        <option value="Meter">Meter</option>
                        <option value="Unit">Unit</option>
                        <option value="Jerigen">Jerigen</option>
                        <option value="Lembar">Lembar</option>
                        <option value="Box">Box</option>
                        <option value="Pax">Pax</option>
                        <option value="Drum">Drum</option>
                        <option value="Batang">Batang</option>
                        <option value="Pil">Pil</option>
                        <option value="Dus">Dus</option>
                        <option value="Kilogram">Kilogram</option>
                        <option value="Botol">Botol</option>
                        <option value="IBC Tank">IBC Tank</option>
                        <option value="Lusin">Lusin</option>
                        <option value="Liter">Liter</option>
                        <option value="Lot">Lot</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea class="form-control" id="sparepart_keterangan" name="keterangan" rows="3" placeholder="Catatan (opsional)"></textarea>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4 text-end">
        <button type="button" id="saveSparepartButton" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Simpan Sparepart
        </button>
    </div>
</form>