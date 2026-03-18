<script>
// ========================================
// SPAREPART VERIFICATION SCRIPT
// ========================================
(function() {
    $(document).ready(function() {
        $('#sparepart-item-list').on('click', '.item-child-item', function(e) {
            e.preventDefault();
            $('#sparepart-item-list .item-child-item').removeClass('active');
            $(this).addClass('active');
            const itemData = $(this).data('item');
            $('#sparepart-detail-view-container').html(createSparepartDetailCard(itemData));
        });
    });

    window.toggleSparepartDropdown = function(element) {
        const poId = $(element).data('po-id');
        $(element).toggleClass('open');
        $(`.child-po-${poId}`).each(function() {
            $(this).toggleClass('show');
        });
    };

    function createSparepartDetailCard(data) {
        const h = (str) => str ? String(str).replace(/</g, '&lt;') : "-";
        return `
            <div class="card table-card animate__animated animate__fadeIn">
                <div class="card-header p-3 text-center">
                    <h5 class="fw-bold m-0"><i class="fas fa-info-circle me-2 text-secondary"></i>Detail Sparepart: ${h(data.kode)}</h5>
                </div>
                <div class="card-body p-4">
                    <h6><i class="fas fa-cogs pe-2"></i>Informasi Sparepart</h6>
                    <table class="table table-sm table-borderless">
                        <tbody>
                            <tr><td width="30%"><strong>PO Number</strong></td><td>: ${h(data.no_po)}</td></tr>
                            <tr><td><strong>Kode</strong></td><td>: ${h(data.kode)}</td></tr>
                            <tr><td class="align-top"><strong>Deskripsi</strong></td><td class="align-top">: ${h(data.desc_sparepart)}</td></tr>
                            <tr><td><strong>Jumlah Dipesan</strong></td><td>: ${h(data.qty)}</td></tr>
                            <tr><td class="align-top"><strong>Catatan Item</strong></td><td class="align-top">: ${h(data.keterangan)}</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-center">
                    <button onclick="updateSparepartStatusVerifikasi(${data.id}, ${data.po_id || data.id_po}, 'Sesuai')" class="btn btn-success">
                        <i class="fas fa-check-circle"></i> Sesuai
                    </button>
                    <button onclick="updateSparepartStatusVerifikasi(${data.id}, ${data.po_id || data.id_po}, 'Tidak Sesuai')" class="btn btn-danger">
                        <i class="fas fa-times-circle"></i> Tidak Sesuai
                    </button>
                </div>
            </div>`;
    }

    window.updateSparepartStatusVerifikasi = function(itemId, poId, status) {
        const action = (note = '') => {
            if (window._verifyingSparepart) return;
            window._verifyingSparepart = true;
            $.ajax({
                type: "POST",
                url: "<?= base_url('warehouse/purchase-orders/verify-po-sparepart') ?>",
                data: {
                    id_item: itemId,
                    po_id: poId,
                    status: status,
                    catatan_verifikasi: note,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: "JSON",
                beforeSend: () => OptimaPro.showLoading('Verifying sparepart...'),
                success: function(response) {
                    window._verifyingSparepart = false;
                    OptimaPro.hideLoading();
                    if (response.success) {
                        OptimaNotify.success('Verifikasi berhasil disimpan!', 'Berhasil!');
                        
                        let sisaElem = $(`#lbl-remain-sparepart-po-${poId}`);
                        let sisaCount = parseInt(sisaElem.text()) - 1;
                        sisaElem.text(`${sisaCount} Item`);
                        
                        $(`#list-sparepart-item-${itemId}`).fadeOut(500, function() { 
                            $(this).remove(); 
                            if (sisaCount === 0) {
                                $(`[data-po-id="${poId}"]`).fadeOut(500);
                            }
                        });

                        $('#sparepart-detail-view-container').html(`
                            <div class="card table-card">
                                <div class="card-body text-center p-5">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <h5 class="text-muted">Verifikasi berhasil! Silakan pilih item lain.</h5>
                                </div>
                            </div>
                        `);
                    } else {
                        OptimaNotify.error(response.message || 'Terjadi kesalahan.');
                    }
                },
                error: (xhr) => {
                    window._verifyingSparepart = false;
                    OptimaNotify.error('Terjadi kesalahan tak terduga.');
                    console.error(xhr.responseText);
                }
            });
        };

        if (status === 'Tidak Sesuai') {
            OptimaConfirm.danger({
                title: 'Verifikasi "Tidak Sesuai"',
                icon: 'warning',
                confirmText: 'Submit',
                cancelText: window.lang('cancel'),
                html: `
                    <div class="text-start">
                        <label class="form-label">Harap berikan alasan atau catatan</label>
                        <textarea id="optimaSparepartVerifyReason" class="form-control" rows="4" placeholder="Contoh: Barang rusak, jumlah kurang, dll..."></textarea>
                    </div>
                `,
                onConfirm: function() {
                    var el = document.getElementById('optimaSparepartVerifyReason');
                    var val = el ? (el.value || '').trim() : '';
                    if (!val) {
                        OptimaNotify.warning('Anda harus mengisi alasan!', 'Validasi');
                        return;
                    }
                    action(val);
                }
            });
        } else {
            OptimaConfirm.generic({
                title: 'Konfirmasi Verifikasi',
                text: `Anda akan mengubah status item menjadi "${status}". Lanjutkan?`,
                icon: 'question',
                confirmText: 'Ya, Lanjutkan!',
                cancelText: window.lang('cancel'),
                confirmButtonColor: 'primary',
                onConfirm: function() {
                    action();
                }
            });
        }
    };
})();
</script>

