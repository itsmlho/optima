<div class="modal fade" id="sparepart-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">✅ Validasi Sparepart</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="validation-form">
                <div class="modal-body">
                    <input type="hidden" name="work_order_id" value="<?= $work_order_id ?>">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="sparepart-table">
                            <thead class="table-light">
                                <tr>
                                    <th width="35%">Sparepart</th>
                                    <th width="15%">Jumlah</th>
                                    <th width="35%">Catatan</th>
                                    <th width="15%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($spareparts)): ?>
                                    <tr><td colspan="4" class="text-center text-danger">⚠️ Tidak ada sparepart di sistem.</td></tr>
                                <?php else: ?>
                                    <?php $i = 0; ?>
                                    <tr class="sparepart-row">
                                        <td>
                                            <select name="used_spareparts[<?= $i ?>][sparepart_id]" class="form-select" required>
                                                <option value="">— Pilih Sparepart —</option>
                                                <?php foreach ($spareparts as $sp): ?>
                                                    <option value="<?= $sp['id'] ?>" <?= in_array($sp['id'], $usedIds ?? []) ? 'disabled' : '' ?>>
                                                        <?= $sp['code'] ?> - <?= $sp['name'] ?>
                                                        <?php if (($sp['stock'] ?? 0) < 1): ?> (Habis) <?php endif; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="invalid-feedback">Pilih sparepart dulu</div>
                                        </td>
                                        <td>
                                            <input type="number" name="used_spareparts[<?= $i ?>][used_quantity]" value="1" min="0.001" step="0.001" class="form-control qty" required>
                                        </td>
                                        <td>
                                            <input type="text" name="used_spareparts[<?= $i ?>][notes]" class="form-control notes">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger remove-row">🗑️</button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" id="btn-add-row" class="btn btn-sm btn-info mt-2">➕ Tambah Baris</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Tutup</button>
                    <button type="submit" id="btn-save-validation" class="btn btn-primary">🟢 Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;"></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#btn-add-row').on('click', function () {
        const tbody = $('#sparepart-table tbody');
        const count = tbody.find('.sparepart-row').length;
        const html = `
            <tr class="sparepart-row">
                <td>
                    <select name="used_spareparts[${count}][sparepart_id]" class="form-select" required>
                        <option value="">— Pilih Sparepart —</option>
                        <?php foreach ($spareparts as $sp): ?>
                            <option value="<?= $sp['id'] ?>"><?= $sp['code'] ?> - <?= $sp['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Pilih sparepart</div>
                </td>
                <td>
                    <input type="number" name="used_spareparts[${count}][used_quantity]" value="1" min="0.001" step="0.001" class="form-control qty" required>
                </td>
                <td>
                    <input type="text" name="used_spareparts[${count}][notes]" class="form-control notes">
                </td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row">🗑️</button></td>
            </tr>`;
        tbody.append(html);
    });
    $(document).on('click', '.remove-row', function () { $(this).closest('tr').remove(); });
    $('#btn-save-validation').on('click', function (e) {
        e.preventDefault();
        let hasError = false;
        $('.sparepart-row').each(function () {
            const $sp = $(this).find('select[name*="sparepart_id"]');
            const $qty = $(this).find('input[name*="used_quantity"]');
            $sp.removeClass('is-invalid').next('.invalid-feedback').remove();
            $qty.removeClass('is-invalid').next('.invalid-feedback').remove();
            if (!$sp.val()) {
                hasError = true;
                $sp.addClass('is-invalid').after('<div class="invalid-feedback">Pilih sparepart</div>');
            }
            if (!$qty.val() || parseFloat($qty.val()) <= 0) {
                hasError = true;
                $qty.addClass('is-invalid').after('<div class="invalid-feedback">Isi jumlah</div>');
            }
        });
        if (hasError) {
            showToast('error', '⚠️ Lengkapi data sparepart.');
            return;
        }
        const btn = $(this).prop('disabled', true).html('⏳ Menyimpan...');
        $.post('<?= site_url('work-orders/save-sparepart-validation') ?>', $('#validation-form').serialize())
            .done(res => {
                if (res.status === 'success') {
                    showToast('success', res.message);
                    setTimeout(() => {
                        $('#sparepart-modal').modal('hide');
                        location.reload();
                    }, 800);
                } else {
                    showToast('error', res.message || 'Gagal simpan.');
                }
            })
            .fail(() => showToast('error', '❌ Server error'))
            .always(() => btn.prop('disabled', false).html('🟢 Simpan'));
    });
    function showToast(type, msg) {
        const bg = type === 'success' ? 'success' : 'danger';
        const html = `
            <div class="toast align-items-center text-white bg-${bg} border-0" role="alert">
                <div class="d-flex"><div class="toast-body">${msg}</div>
                <button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`;
        $('#toast-container').append(html);
        new bootstrap.Toast($('#toast-container .toast:last')[0], { autohide: true, delay: 3000 }).show();
    }
});
</script>