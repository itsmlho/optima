<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<style>
    /* General Layout */
    .table-card { border: none; border-radius: 15px; box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1); overflow: hidden; }
    .modal-header { background-color: #343a40; color: white; border-radius: 15px 15px 0 0; }
    
    /* PO List (Left Panel) */
    .po-group-header { cursor: pointer; background-color: #f8f9fa; border-bottom: 1px solid #e9ecef; padding: 0.75rem 1.25rem; transition: background-color 0.2s ease; }
    .po-group-header:hover { background-color: #e9ecef; }
    .po-group-header .arrow-icon { transition: transform 0.3s ease; }
    .po-group-header.open .arrow-icon { transform: rotate(180deg); }
    .item-child-item { display: none; padding-left: 2.5rem; border-left: 3px solid #dee2e6; }
    .item-child-item:hover { border-left-color: #0d6efd; }
    .list-group-item.active { background-color: #e9ecef; border-color: #dee2e6; color: #212529; }
    .list-group-item.active .text-muted { color: #6c757d !important; }

    /* Elegant Modal Styles */
    .verification-component { border: 1px solid #dee2e6; border-radius: .5rem; margin-bottom: 1rem; transition: all 0.3s ease; overflow: hidden; border-left: 5px solid #6c757d; }
    .verification-component[data-status="sesuai"] { border-left-color: #198754; background-color: #f6fff8; }
    .verification-component[data-status="tidak-sesuai"] { border-left-color: #dc3545; background-color: #fff5f5; }
    .component-header { padding: .75rem 1.25rem; background-color: #f8f9fa; border-bottom: 1px solid #dee2e6; }
    .component-body { padding: 1.25rem; }
    .spec-details { font-size: .875rem; color: #495057; }
    .note-input-group, .sn-input-group { display: none; margin-top: 1rem; }
    .btn-verify-action.active { background-color: #e9ecef; box-shadow: inset 0 2px 4px rgba(0,0,0,.1); }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-4">
        <div class="card table-card">
            <div class="card-header text-center">
                <h5 class="fw-bold m-0">Item untuk Diverifikasi</h5>
            </div>
            <div class="list-group list-group-flush" id="item-list">
                <?php if (empty($detailGroup)): ?>
                    <div class="list-group-item">Tidak ada item yang perlu diverifikasi.</div>
                <?php else: ?>
                    <?php foreach ($detailGroup as $key => $value): ?>
                        <div class="list-group-item po-group-header" onclick="toggleDropdown(this)" data-po-id="<?= $key ?>">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($value["no_po"]) ?></h6>
                                    <p class="mb-0 text-muted small">Sisa: <span id="lbl-remain-po-<?= $key ?>"><?= count($value["data"]) ?> Item</span></p>
                                </div>
                                <i class="fas fa-chevron-down arrow-icon"></i>
                            </div>
                        </div>
                        <?php foreach ($value['data'] as $item): ?>
                            <a href="#" class="list-group-item list-group-item-action item-child-item child-po-<?= $key ?>" 
                               data-item='<?= json_encode($item) ?>' 
                               id="list-item-<?= $item['id_po_item'] ?>">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <?php if ($item['item_type'] === 'Attachment'): ?>
                                            <i class="fas fa-paperclip fa-2x text-secondary"></i>
                                        <?php else: ?>
                                            <i class="fas fa-battery-full fa-2x text-success"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1" style="min-width: 0;">
                                        <?php 
                                            $itemName = ($item['item_type'] === 'Attachment') 
                                                ? ($item['attachment_name'] ?: 'N/A') 
                                                : (($item['merk_baterai'] ? $item['merk_baterai'] . ' ' : '') . $item['tipe_baterai']);
                                        ?>
                                        <h6 class="mb-1 fw-bold text-truncate" title="<?= esc($itemName) ?>"><?= esc($itemName) ?></h6>
                                        <p class="mb-0 text-muted small">Tipe: <strong><?= esc($item['item_type']) ?></strong></p>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div id="detail-view-container">
            <div class="card table-card">
                <div class="card-body text-center p-5">
                    <i class="fas fa-hand-pointer fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Pilih item dari daftar di sebelah kiri untuk verifikasi.</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Verifikasi -->
<div class="modal fade" id="modalVerification" tabindex="-1" aria-labelledby="modalVerificationLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light" id="modalVerificationLabel"><i class="fas fa-clipboard-check me-2"></i>Formulir Inspeksi Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formVerification">
                    <input type="hidden" id="item_id">
                    <input type="hidden" id="po_id">
                    <p class="text-muted mb-4">Periksa setiap komponen di bawah ini dan isi informasi yang diperlukan.</p>
                    <div id="verification-components"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-submit-verification" disabled>Submit Verifikasi</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // --- FUNGSI UTAMA ---
    $(document).ready(function() {
        $('#item-list').on('click', '.item-child-item', function(e) {
            e.preventDefault();
            $('.item-child-item').removeClass('active');
            $(this).addClass('active');
            const itemData = $(this).data('item');
            $('#detail-view-container').html(createDetailCard(itemData));
        });

        $('#btn-submit-verification').on('click', submitVerification);
    });

    function toggleDropdown(element) {
        const poId = $(element).data('po-id');
        $(element).toggleClass('open');
        $(`.child-po-${poId}`).slideToggle('fast');
    }

    // --- LOGIKA MODAL VERIFIKASI ---
    function prepareVerificationModal(element) {
        const data = $(element).data('item');
        const itemName = (data.item_type === 'Attachment') ? data.attachment_name : `${data.merk_baterai} ${data.tipe_baterai}`;
        $('#modalVerificationLabel').text(`Inspeksi: ${itemName}`);
        $('#item_id').val(data.id_po_item);
        $('#po_id').val(data.po_id);

        const container = $('#verification-components');
        container.empty();

        if (data.item_type === 'Attachment') {
            container.append(createComponentHTML({ id: 'attachment', label: 'Attachment', sn: true, desc: data.attachment_name }));
        } else { // Battery
            container.append(createComponentHTML({ id: 'baterai', label: 'Baterai', sn: true, desc: `${data.merk_baterai} ${data.tipe_baterai}` }));
            container.append(createComponentHTML({ id: 'charger', label: 'Charger', sn: true, desc: `${data.merk_charger} ${data.tipe_charger}` }));
        }
        $('#modalVerification').modal('show');
    }

    function createComponentHTML(component) {
        const snInputHTML = component.sn ? `<div class="sn-input-group"><label for="sn_${component.id}" class="form-label small fw-bold">Serial Number</label><input type="text" class="form-control sn-input" id="sn_${component.id}"></div>` : '';
        return `
            <div class="verification-component" data-component="${component.id}" data-status="menunggu">
                <div class="component-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">${component.label}</h6>
                    <div class="btn-group btn-group-sm"><button type="button" class="btn btn-outline-success btn-verify-action" onclick="setComponentStatus('${component.id}', 'sesuai', this)"><i class="fas fa-check"></i> Sesuai</button><button type="button" class="btn btn-outline-danger btn-verify-action" onclick="setComponentStatus('${component.id}', 'tidak-sesuai', this)"><i class="fas fa-times"></i> Tidak Sesuai</button></div>
                </div>
                <div class="component-body">
                    <p class="spec-details mb-0">${component.desc}</p>${snInputHTML}
                    <div class="note-input-group"><label for="note_${component.id}" class="form-label small fw-bold text-danger">Catatan Ketidaksesuaian</label><textarea class="form-control note-input" id="note_${component.id}" rows="2"></textarea></div>
                </div>
            </div>`;
    }

    function setComponentStatus(componentId, status, button) {
        const component = $(`[data-component="${componentId}"]`);
        component.attr('data-status', status);
        $(button).addClass('active').siblings().removeClass('active');
        const snGroup = component.find('.sn-input-group');
        const noteGroup = component.find('.note-input-group');
        if (status === 'sesuai') {
            if (snGroup.length) snGroup.slideDown('fast');
            noteGroup.slideUp('fast');
        } else if (status === 'tidak-sesuai') {
            snGroup.slideUp('fast');
            noteGroup.slideDown('fast');
        }
        checkAllVerified();
    }

    function checkAllVerified() {
        const totalComponents = $('.verification-component').length;
        const verifiedComponents = $('.verification-component').filter((i, el) => $(el).attr('data-status') !== 'menunggu').length;
        $('#btn-submit-verification').prop('disabled', totalComponents !== verifiedComponents);
    }

    function submitVerification() {
    if (window._verifyingAttachment) return; // prevent double submit
    let finalStatus = 'Sesuai';
    let fullNotes = [];
    const snData = {};

        $('.verification-component').each(function() {
            const component = $(this);
            const componentId = component.data('component');
            const status = component.attr('data-status');

            if (status === 'tidak-sesuai') {
                finalStatus = 'Tidak Sesuai';
                const note = component.find('.note-input').val();
                if (note) {
                    fullNotes.push(`${component.find('h6').text()}: ${note}`);
                }
            }

            if (component.find('.sn-input').length) {
                if(componentId === 'baterai' || componentId === 'attachment') {
                    snData['serial_number'] = component.find('.sn-input').val();
                } else {
                    snData[`serial_number_${componentId}`] = component.find('.sn-input').val();
                }
            }
        });
        
        if (finalStatus === 'Sesuai' && !snData['serial_number']) {
            Swal.fire({icon:'warning', title:'SN Wajib', text:'Serial number wajib diisi untuk status Sesuai.'});
            return;
        }
        const idItem = $('#item_id').val();
        const poId = $('#po_id').val();
        updateStatusVerifikasi(idItem, poId, finalStatus, snData, fullNotes.join('; '));
    }

    // --- FUNGSI TAMPILAN DAN AJAX ---
    function createDetailCard(data) {
        const h = (str) => str ? String(str).replace(/</g, '&lt;') : "-";
        const itemName = (data.item_type === 'Attachment') ? data.attachment_name : `${data.merk_baterai} ${data.tipe_baterai}`;
        return `
            <div class="card table-card animate__animated animate__fadeIn">
                <div class="card-header p-3 text-center"><h5 class="fw-bold m-0"><i class="fas fa-info-circle me-2 text-secondary"></i>Detail: ${h(itemName)}</h5></div>
                <div class="card-body p-4">
                    <table class="table table-sm table-borderless">
                        <tr><td width="30%"><strong>Tipe Item</strong></td><td>: <span class="badge bg-secondary">${h(data.item_type)}</span></td></tr>
                        <tr><td><strong>PO Number</strong></td><td>: ${h(data.no_po)}</td></tr>
                        <tr><td class="align-top"><strong>Keterangan</strong></td><td class="align-top">: ${h(data.keterangan)}</td></tr>
                    </table>
                </div>
                <div class="card-footer text-center">
                    <button onclick="prepareVerificationModal(this)" class="btn btn-success" data-item='${JSON.stringify(data)}'><i class="fas fa-check-circle"></i> Verifikasi Item</button>
                </div>
            </div>`;
    }

    function updateStatusVerifikasi(itemId, poId, status, snData = {}, catatan = '') {
        const action = (note = '') => {
            window._verifyingAttachment = true; $('#btn-submit-verification').prop('disabled', true);
            $.ajax({
                type: "POST",
                url: "<?= base_url("warehouse/purchase-orders/verify-po-attachment"); ?>",
                data: {
                    id_item: itemId,
                    po_id: poId,
                    status: status,
                    catatan_verifikasi: note || catatan,
                    ...snData
                },
                dataType: "JSON",
                beforeSend: () => OptimaPro.showLoading('Verifying attachment...'),
                success: function(response) {
                    window._verifyingAttachment = false; $('#btn-submit-verification').prop('disabled', false);
                    OptimaPro.hideLoading();
                    if (response.success) {
                        $('#modalVerification').modal('hide');
                        if (window.OptimaNotify) { OptimaNotify.success('Verifikasi berhasil!'); } else if (window.createOptimaToast) { createOptimaToast({type:'success', title:'Berhasil', message:'Verifikasi berhasil!'}); }
                        
                        let sisaElem = $(`#lbl-remain-po-${poId}`);
                        let sisaCount = parseInt(sisaElem.text()) - 1;
                        sisaElem.text(`${sisaCount} Item`);
                        
                        $(`#list-item-${itemId}`).fadeOut(500, function() { 
                            $(this).remove(); 
                            if (sisaCount === 0) {
                                $(`[data-po-id="${poId}"]`).fadeOut(500);
                            }
                        });

                        $('#detail-view-container').html(`<div class="card table-card"><div class="card-body text-center p-5"><i class="fas fa-check-circle fa-3x text-success mb-3"></i><h5 class="text-muted">Verifikasi berhasil! Silakan pilih item lain.</h5></div></div>`);
                    } else {
                        if (window.OptimaNotify) { OptimaNotify.error(response.message || 'Terjadi kesalahan.'); } else { Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Terjadi kesalahan.' }); }
                    }
                },
                error: (xhr) => {
                    window._verifyingAttachment = false; $('#btn-submit-verification').prop('disabled', false);
                    if (window.OptimaNotify) { OptimaNotify.error('Terjadi kesalahan tak terduga.'); } else { Swal.fire("Error", "Terjadi kesalahan tak terduga.", "error"); }
                    console.error(xhr.responseText);
                }
            });
        };

        if (status === 'Tidak Sesuai') {
            Swal.fire({
                title: 'Verifikasi "Tidak Sesuai"',
                input: 'textarea',
                inputLabel: 'Harap berikan alasan atau catatan',
                inputPlaceholder: 'Contoh: Barang rusak, jumlah kurang, dll...',
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Batal',
                inputValidator: (value) => !value && 'Anda harus mengisi alasan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    action(result.value);
                }
            });
        } else {
            Swal.fire({
                title: 'Konfirmasi Verifikasi',
                text: `Anda akan memverifikasi item ini sebagai "${status}". Lanjutkan?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    action();
                }
            });
        }
    }
</script>
<?= $this->endSection() ?>
