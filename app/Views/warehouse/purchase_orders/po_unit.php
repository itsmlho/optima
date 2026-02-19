<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<style>
    /* General Layout */
    .table-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .modal-header {
        background-color: #343a40; /* Dark, professional header */
        color: white;
        border-radius: 15px 15px 0 0;
    }
    
    /* PO List (Left Panel) */
    .po-group-header {
        cursor: pointer;
        background-color: #f8f9fa; /* Light grey for group headers */
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
    .unit-child-item {
        display: none;
        padding-left: 2.5rem;
        border-left: 3px solid #dee2e6; /* Neutral border */
    }
    .unit-child-item:hover {
        border-left-color: #0d6efd; /* Blue accent on hover */
    }
    .list-group-item.active {
        background-color: #e9ecef; /* Consistent light grey for active item */
        border-color: #dee2e6;
        color: #212529;
    }
    .list-group-item.active .text-muted {
        color: #6c757d !important;
    }

    /* New Elegant Modal Styles */
    .verification-component {
        border: 1px solid #dee2e6;
        border-radius: .5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        overflow: hidden;
        border-left: 5px solid #6c757d; /* Default neutral state */
    }
    .verification-component[data-status="sesuai"] {
        border-left-color: #198754;
        background-color: #f6fff8;
    }
    .verification-component[data-status="tidak-sesuai"] {
        border-left-color: #dc3545;
        background-color: #fff5f5;
    }
    .component-header {
        padding: .75rem 1.25rem;
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    .component-body {
        padding: 1.25rem;
    }
    .spec-details {
        font-size: .875rem;
        color: #495057;
    }
    .note-input-group, .sn-input-group {
        display: none; /* Hidden by default */
        margin-top: 1rem;
    }
    .btn-verify-action.active {
        background-color: #e9ecef;
        box-shadow: inset 0 2px 4px rgba(0,0,0,.1);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-4">
        <div class="card table-card">
            <div class="card-header text-center">
                <h5 class="fw-bold m-0">Unit untuk Diverifikasi</h5>
            </div>
            <div class="list-group list-group-flush" id="unit-list">
                <?php if (empty($detailGroup)): ?>
                    <div class="list-group-item">Tidak ada unit yang perlu diverifikasi.</div>
                <?php else: ?>
                    <?php foreach ($detailGroup as $key => $value): ?>
                        <div class="list-group-item po-group-header" onclick="toggleDropdown(this)" data-po-id="<?= $key ?>">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($value["no_po"]) ?></h6>
                                    <p class="mb-0 text-muted small">Sisa: <span id="lbl-remain-po-<?= $key ?>"><?= count($value["data"]) ?> Unit</span></p>
                                </div>
                                <i class="fas fa-chevron-down arrow-icon"></i>
                            </div>
                        </div>
                        <?php foreach ($value['data'] as $unit): ?>
                            <a href="#" class="list-group-item list-group-item-action unit-list-item unit-child-item child-po-<?= $key ?>" 
                               data-unit='<?= json_encode($unit) ?>' 
                               id="list-item-<?= $unit['id_po_unit'] ?>">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 fw-bold"><?= htmlspecialchars($unit['merk_unit'] . ' ' . $unit['model_unit']) ?></h6>
                                    <small><?= htmlspecialchars($unit['jenis_unit']) ?></small>
                                </div>
                                <p class="mb-1 text-muted small">Tipe: <?= htmlspecialchars($unit['nama_tipe_unit']) ?> | Kapasitas: <?= htmlspecialchars($unit['kapasitas_unit']) ?></p>
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
                    <h5 class="text-muted">Pilih unit dari daftar di sebelah kiri untuk verifikasi.</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Verifikasi yang Diperbaiki -->
<div class="modal fade modal-wide" id="modalUpdateSN" tabindex="-1" aria-labelledby="modalUpdateSNLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light" id="modalUpdateSNLabel"><i class="fas fa-clipboard-check me-2"></i>Formulir Inspeksi Unit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formUpdateSN">
                    <input type="hidden" id="unit_id">
                    <input type="hidden" id="po_id">
                    <p class="text-muted mb-4">Periksa setiap komponen di bawah ini. Tandai "Sesuai" atau "Tidak Sesuai" dan isi informasi yang diperlukan.</p>
                    <div id="verification-components">
                        <!-- Komponen verifikasi akan di-generate oleh JavaScript -->
                    </div>
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
    // Toast helper (fallback aman bila fungsi global belum ada)
    function unitToast(type, message, title = 'Verifikasi Unit') {
        if (typeof window.createOptimaToast === 'function') {
            createOptimaToast({ type, title, message });
        } else if (window.OptimaPro && typeof OptimaPro.showNotification === 'function') {
            OptimaPro.showNotification(message, type === 'success' ? 'success' : 'error');
        } else {
            console.log(`[${type.toUpperCase()}] ${title}: ${message}`);
        }
    }

    // --- FUNGSI UTAMA ---
    $(document).ready(function() {
        $('#unit-list').on('click', '.unit-list-item', function(e) {
            e.preventDefault();
            $('.unit-list-item').removeClass('active');
            $(this).addClass('active');
            const unitData = $(this).data('unit');
            $('#detail-view-container').html(createDetailCard(unitData));
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
        const data = $(element).data('unit');
        $('#modalUpdateSNLabel').text(`Inspeksi: ${data.merk_unit} ${data.model_unit}`);
        $('#unit_id').val(data.id_po_unit);
        $('#po_id').val(data.po_id);

        const container = $('#verification-components');
        container.empty();

        const components = [
            { id: 'unit', label: 'Unit', sn: true, desc: `${data.merk_unit} ${data.model_unit} | ${data.kapasitas_unit}` },
            { id: 'mesin', label: 'Mesin', sn: true, desc: `${data.merk_mesin} ${data.model_mesin} | ${data.bahan_bakar}` },
            { id: 'baterai', label: 'Baterai', sn: true, desc: `${data.merk_baterai} ${data.tipe_baterai}` },
            { id: 'mast', label: 'Mast', sn: true, desc: data.tipe_mast },
            { id: 'ban', label: 'Ban', sn: false, desc: data.tipe_ban },
            { id: 'kondisi', label: 'Kondisi Fisik', sn: false, desc: `Sesuai PO: ${data.status_penjualan}` }
        ];

        components.forEach(comp => {
            if (comp.desc && comp.desc.trim() !== '-' && !comp.desc.includes('null')) {
                container.append(createComponentHTML(comp));
            }
        });

        $('#modalUpdateSN').modal('show');
    }

    function createComponentHTML(component) {
        const snInputHTML = component.sn ? `
            <div class="sn-input-group">
                <label for="sn_${component.id}" class="form-label small fw-bold">Serial Number</label>
                <input type="text" class="form-control sn-input" id="sn_${component.id}" data-component-id="${component.id}">
            </div>` : '';

        return `
            <div class="verification-component" data-component="${component.id}" data-status="menunggu">
                <div class="component-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">${component.label}</h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-success btn-verify-action" onclick="setComponentStatus('${component.id}', 'sesuai', this)">
                            <i class="fas fa-check"></i> Sesuai
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-verify-action" onclick="setComponentStatus('${component.id}', 'tidak-sesuai', this)">
                            <i class="fas fa-times"></i> Tidak Sesuai
                        </button>
                    </div>
                </div>
                <div class="component-body">
                    <p class="spec-details mb-0">${component.desc}</p>
                    ${snInputHTML}
                    <div class="note-input-group">
                        <label for="note_${component.id}" class="form-label small fw-bold text-danger">Catatan Ketidaksesuaian</label>
                        <textarea class="form-control note-input" id="note_${component.id}" rows="2"></textarea>
                    </div>
                </div>
            </div>
        `;
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
    if (window._verifying) return; // double-submit guard
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
                snData[`sn_${componentId}`] = component.find('.sn-input').val();
            }
        });
        
        // Mandatory SN validation when finalStatus == 'Sesuai'
        if (finalStatus === 'Sesuai' && (!snData['sn_unit'] || !snData['sn_mesin'])) {
            Swal.fire({icon:'warning', title:'SN Wajib', text:'Serial number Unit dan Mesin wajib diisi untuk status Sesuai.'});
            return;
        }
        const idUnit = $('#unit_id').val();
        const poId = $('#po_id').val();
        updateStatusVerifikasi(idUnit, poId, finalStatus, snData, fullNotes.join('; '));
    }

    // --- FUNGSI TAMPILAN DAN AJAX ---
    function createDetailCard(data) {
        const h = (str) => str ? String(str).replace(/</g, '&lt;') : "-";
        return `
            <div class="card table-card animate__animated animate__fadeIn">
                <div class="card-header p-3 text-center">
                    <h5 class="fw-bold m-0"><i class="fas fa-truck-ramp-box me-2 text-secondary"></i>Detail Unit: ${h(data.merk_unit)} ${h(data.model_unit)}</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <h6><i class="fas fa-gear pe-2"></i>Unit</h6>
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr><td width="40%"><strong>Jenis</strong></td><td>: ${h(data.jenis_unit)}</td></tr>
                                    <tr><td><strong>Merk</strong></td><td>: ${h(data.merk_unit)}</td></tr>
                                    <tr><td><strong>Tipe</strong></td><td>: ${h(data.nama_tipe_unit)}</td></tr>
                                    <tr><td><strong>Model</strong></td><td>: ${h(data.model_unit)}</td></tr>
                                    <tr><td><strong>Kapasitas</strong></td><td>: ${h(data.kapasitas_unit)}</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <h6><i class="fas fa-cogs pe-2"></i>Mesin</h6>
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr><td width="40%"><strong>Model</strong></td><td>: ${h(data.model_mesin)}</td></tr>
                                    <tr><td><strong>Merk</strong></td><td>: ${h(data.merk_mesin)}</td></tr>
                                    <tr><td><strong>BBM</strong></td><td>: ${h(data.bahan_bakar)}</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <h6><i class="fas fa-battery-full pe-2"></i>Baterai</h6>
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr><td width="40%"><strong>Tipe</strong></td><td>: ${h(data.tipe_baterai)}</td></tr>
                                    <tr><td><strong>Merk</strong></td><td>: ${h(data.merk_baterai)}</td></tr>
                                    <tr><td><strong>Jenis</strong></td><td>: ${h(data.jenis_baterai)}</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-6 mb-3">
                             <h6><i class="fas fa-circle-info pe-2"></i>Other Info</h6>
                             <table class="table table-sm table-borderless">
                                 <tbody>
                                     <tr><td width="40%"><strong>Mast</strong></td><td>: ${h(data.tipe_mast)}</td></tr>
                                     <tr><td><strong>Ban</strong></td><td>: ${h(data.tipe_ban)}</td></tr>
                                     <tr><td><strong>Kondisi</strong></td><td>: <span class="badge bg-info">${h(data.status_penjualan)}</span></td></tr>
                                     <tr><td class="align-top"><strong>Keterangan</strong></td><td class="align-top">: ${data.keterangan || "-"}</td></tr>
                                 </tbody>
                             </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button onclick="prepareVerificationModal(this)" class="btn btn-success" data-unit='${JSON.stringify(data)}'>
                        <i class="fas fa-check-circle"></i> Verifikasi Unit
                    </button>
                </div>
            </div>`;
    }

    function updateStatusVerifikasi(idUnit, poId, status, snData = {}, catatan = '') {
        const confirmText = `Anda akan memverifikasi unit ini sebagai "${status}". Lanjutkan?`;

        const action = (note = '') => {
            window._verifying = true; $('#btn-submit-verification').prop('disabled', true);
            $.ajax({
                type: "POST",
                url: "<?= base_url("warehouse/purchase-orders/verify-po-unit"); ?>",
                data: {
                    id_unit: idUnit,
                    po_id: poId,
                    status: status,
                    catatan_verifikasi: note || catatan,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                    ...snData
                },
                dataType: "JSON",
                beforeSend: () => {
                    OptimaPro.showLoading('Verifying unit...');
                },
                success: function(r) {
                    window._verifying = false; $('#btn-submit-verification').prop('disabled', false);
                    OptimaPro.hideLoading();
                    if (r.statusCode == 200) {
                        $('#modalUpdateSN').modal('hide');
                        let jumlah = document.querySelectorAll(`.child-po-${poId}`).length;
                        $(`#lbl-remain-po-${poId}`).text(`${jumlah - 1} Unit`);
                        $(`#list-item-${idUnit}`).fadeOut(500, function() { $(this).remove(); });
                        $('#detail-view-container').html(`<div class="card table-card"><div class="card-body text-center p-5"><i class="fas fa-check-circle fa-3x text-success mb-3"></i><h5 class="text-muted">Verifikasi berhasil! Silakan pilih unit lain.</h5></div></div>`);
                        unitToast('success', r.message || 'Unit berhasil diverifikasi.');
                    } else {
                        unitToast('error', r.message || 'Verifikasi gagal.');
                        if(!window.OptimaNotify) { Swal.fire({ icon: 'error', title: 'Error', text: r.message || 'Verifikasi gagal.' }); }
                    }
                },
                error: function(xhr, status, error) {
                    window._verifying = false; $('#btn-submit-verification').prop('disabled', false);
                    Swal.close();
                    const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Terjadi kesalahan tak terduga.';
                    unitToast('error', msg);
                    if(!window.OptimaNotify) { Swal.fire("Error", msg, "error"); }
                }
            });
        };

        Swal.fire({
            title: 'Konfirmasi Verifikasi',
            text: confirmText,
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
</script>
<?= $this->endSection() ?>
