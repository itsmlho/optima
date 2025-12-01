<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<style>
    .table-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .modal-header {
        background-color: #343a40;
        color: white;
        border-radius: 15px 15px 0 0;
    }
    .po-group-header {
        cursor: pointer;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 0.75rem 1.25rem;
        transition: background-color 0.2s ease;
    }
    .po-group-header:hover {
        background-color: #e9ecef;
    }
    .po-group-header .arrow-icon {
        transition: transform 0.3s ease;
    }
    .po-group-header.open .arrow-icon {
        transform: rotate(180deg);
    }
    .item-child-item {
        display: none;
        padding-left: 2.5rem;
        border-left: 3px solid #dee2e6;
    }
    .item-child-item:hover {
        border-left-color: #0d6efd;
    }
    .list-group-item.active {
        background-color: #e9ecef;
        border-color: #dee2e6;
        color: #212529;
    }
    .list-group-item.active .text-muted {
        color: #6c757d !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-4">
        <div class="card table-card">
            <div class="card-header text-center">
                <h5 class="fw-bold m-0">Sparepart untuk Diverifikasi</h5>
            </div>
            <div class="list-group list-group-flush" id="item-list">
                <?php if (empty($detailGroup)): ?>
                    <div class="list-group-item">Tidak ada sparepart yang perlu diverifikasi.</div>
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
                               id="list-item-<?= $item['id'] ?>">
                                <div class="d-flex align-items-center">
                                    <div class="me-3"><i class="fas fa-cogs fa-2x text-secondary"></i></div>
                                    <div class="flex-grow-1" style="min-width: 0;">
                                        <h6 class="mb-1 fw-bold text-truncate" title="<?= esc($item['desc_sparepart']) ?>"><?= esc($item['kode']) ?></h6>
                                        <p class="mb-0 text-muted small">Qty: <strong><?= esc($item['qty']) ?></strong></p>
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
                    <h5 class="text-muted">Pilih sparepart dari daftar di sebelah kiri untuk verifikasi.</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function toggleDropdown(element) {
        const poId = $(element).data('po-id');
        $(element).toggleClass('open');
        $(`.child-po-${poId}`).slideToggle('fast');
    }

    $(document).ready(function() {
        $('#item-list').on('click', '.item-child-item', function(e) {
            e.preventDefault();
            $('.item-child-item').removeClass('active');
            $(this).addClass('active');
            const itemData = $(this).data('item');
            $('#detail-view-container').html(createDetailCard(itemData));
        });
    });

    function createDetailCard(data) {
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
                    <button onclick="updateStatusVerifikasi(${data.id}, ${data.id_po}, 'Sesuai')" class="btn btn-success">
                        <i class="fas fa-check-circle"></i> Sesuai
                    </button>
                    <button onclick="updateStatusVerifikasi(${data.id}, ${data.id_po}, 'Tidak Sesuai')" class="btn btn-danger">
                        <i class="fas fa-times-circle"></i> Tidak Sesuai
                    </button>
                </div>
            </div>`;
    }

    function updateStatusVerifikasi(itemId, poId, status) {
        const action = (note = '') => {
            if (window._verifyingSparepart) return; // guard
            window._verifyingSparepart = true;
            $.ajax({
                type: "POST",
                url: "<?= base_url("warehouse/purchase-orders/verify-po-sparepart"); ?>",
                data: {
                    id_item: itemId,
                    po_id: poId, // Kirim juga po_id
                    status: status,
                    catatan_verifikasi: note
                },
                dataType: "JSON",
                beforeSend: () => Swal.showLoading(),
                success: function(response) {
                    window._verifyingSparepart = false;
                    Swal.close();
                    if (response.success) {
                        if (window.OptimaNotify) { OptimaNotify.success('Verifikasi berhasil disimpan!'); }
                        else if (window.createOptimaToast) { createOptimaToast({type:'success', title:'Berhasil', message:'Verifikasi berhasil disimpan!'}); }
                        
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
                    window._verifyingSparepart = false;
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
                text: `Anda akan mengubah status item menjadi "${status}". Lanjutkan?`,
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
