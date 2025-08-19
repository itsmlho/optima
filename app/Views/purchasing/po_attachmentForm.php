<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
    <style>
        .card-stats {
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .card-stats:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .card-stats::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 100%);
        }
        
        .form-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .btn-action {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        
        .btn-action:hover {
            transform: scale(1.05);
        }
        
        .section-header {
            background: linear-gradient(135deg,rgb(249, 249, 249) 0%,rgb(248, 248, 248) 100%);
            color: black;
            padding: 1rem 1.5rem;
            border-radius: 10px 10px 0 0;
            margin-bottom: 0;
        }
        
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.6rem 1rem;
            transition: all 0.2s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .form-section {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }
    </style>
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <!-- Or for RTL support -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <style>
        .form-control {
            max-height: 38px !important;
        }
    </style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center">
    <a href="<?= base_url('/purchasing/po-attachment') ?>" class="btn btn-light btn-action">
        <i class="fas fa-arrow-left me-2"></i>Back to List
    </a>
</div>
<div class="container-fluid">
    <form action="<?= $mode == "update" ? base_url('/purchasing/save-update-po-attachment/'.$id_po) : base_url('/purchasing/store-po-attachment') ?>" method="post" id="poAttachmentForm">
        <?= csrf_field() ?>
        
        <!-- Basic Information Section -->
        <div class="form-section">
            <h5 class="section-header">
                <i class="fas fa-info-circle me-2"></i>Basic Information
            </h5>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-12">
                        <label for="no_po" class="form-label">PO Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="no_po" placeholder="Masukkan Nomor PO" value="<?= $po["no_po"] ?? "" ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="invoice_no" class="form-label">Invoice Number <span class="text-secondary">(optional)</span></label>
                        <input type="text" class="form-control" name="invoice_no" placeholder="Masukkan Invoice Number" value="<?= $po["invoice_no"] ?? "" ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="invoice_date" class="form-label">Invoice Date <span class="text-secondary">(optional)</span></label>
                        <input type="date" class="form-control" name="invoice_date"  value="<?= $po["invoice_date"] ?? "" ?>">
                    </div>
                    
                    <div class="col-md-4">
                        <label for="bl_date" class="form-label">BL Date <span class="text-secondary">(optional)</span></label>
                        <input type="date" class="form-control" name="bl_date"  value="<?= $po["bl_date"] ?? "" ?>">
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="tanggal_po" class="form-label">PO Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal_po" value="<?= $po["tanggal_po"] ?? date("Y-m-d") ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label for="id_supplier" class="form-label">Supplier <span class="text-danger">*</span></label>
                        <select name="id_supplier" class="form-select select2-sm" required>
                            <option value="">Select Supplier...</option>
                            <?php foreach ($suppliers as $item): ?>
                                <option value="<?= $item['id_supplier'] ?>" <?= ($po["supplier_id"] ?? "") == $item["id_supplier"] ? "selected" : "" ?>><?= esc($item['nama_supplier']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="tipe_po" class="form-label">PO Type <span class="text-danger">*</span></label>
                        <select name="tipe_po" class="form-select select2-sm" required>
                            <option value="">-- Pilih PO Type --</option>
                            <option value="Attachment" <?= ($po["tipe_po"] ?? "") == "Attachment" ? "selected" : "" ?>>Attachment</option>
                            <option value="Battery" <?= ($po["tipe_po"] ?? "") == "Battery" ? "selected" : "" ?>>Battery</option>
                        </select>
                    </div>


                    <div class="col-12">
                        <label for="keterangan_po" class="form-label">Keterangan PO <span class="text-secondary">(optional)</span></label>
                        <input type="text" class="form-control" name="keterangan_po" placeholder="Masukkan Keterangan PO" value="<?= $po["keterangan_po"] ?? "" ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Attachment/Battery Details Section -->
        <div class="form-section">
            <h5 class="section-header">
                <i class="fas fa-battery-full me-2"></i>Attachment/Battery Details
            </h5>
            <div class="card-body">
                <!-- Attachment Fields (shown when PO Type is Attachment) -->
                <div id="attachment-fields" class="row g-3" style="display: none;">
                    <div class="col-md-4">
                        <label class="form-label">Tipe <span class="text-danger">*</span></label>
                        <select name="att_tipe" id="att_tipe" class="form-select select2-sm">
                            <option value="">Select Tipe...</option>
                            <?php if(!empty($attachments)): $uniqueTipe = array_unique(array_filter(array_map(fn($a)=>$a['tipe']??'', $attachments))); foreach($uniqueTipe as $tp): ?>
                                <option value="<?= esc($tp) ?>" <?= ($detail['tipe']??'')===$tp?'selected':''; ?>><?= esc($tp) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Merk <span class="text-danger">*</span></label>
                        <select name="att_merk" id="att_merk" class="form-select select2-sm">
                            <option value="">Select Merk...</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Model <span class="text-danger">*</span></label>
                        <select name="attachment_id" id="att_model" class="form-select select2-sm">
                            <option value="">Select Model...</option>
                        </select>
                    </div>
                </div>

                <!-- Battery Fields (shown when PO Type is Battery) -->
                <div id="battery-fields" class="row g-3" style="display: none;">
                    <div class="col-md-6">
                        <label for="baterai_id" class="form-label">Battery Type <span class="text-danger">*</span></label>
                        <select name="baterai_id" class="form-select select2-sm">
                            <option value="">Select Battery Type...</option>
                            <?php if (!empty($baterais)): ?>
                                <?php foreach ($baterais as $item): ?>
                                    <option value="<?= $item['id'] ?>" <?= ($detail["baterai_id"] ?? "") == $item["id"] ? "selected" : "" ?>>
                                        <?= esc($item['merk_baterai'] . ' - ' . $item['tipe_baterai']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="charger_id" class="form-label">Charger Type <span class="text-danger">*</span></label>
                        <select name="charger_id" class="form-select select2-sm">
                            <option value="">Select Charger Type...</option>
                            <?php if (!empty($chargers)): ?>
                                <?php foreach ($chargers as $item): ?>
                                    <option value="<?= $item['id_charger'] ?>" <?= ($detail["charger_id"] ?? "") == $item["id_charger"] ? "selected" : "" ?>>
                                        <?= esc($item['merk_charger'] . ' - ' . $item['tipe_charger']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <!-- Common Fields -->
                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <label for="serial_number" class="form-label">Serial Number <span class="text-secondary">(optional)</span></label>
                        <input type="text" class="form-control" name="serial_number" placeholder="Enter serial number" value="<?= $detail["serial_number"] ?? "" ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="qty" class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="qty" value="<?= $detail["qty"] ?? 1 ?>" min="1" required>
                    </div>
                    <div class="col-12">
                        <label for="keterangan" class="form-label">Notes <span class="text-secondary">(optional)</span></label>
                        <textarea class="form-control" name="keterangan" rows="3" placeholder="Additional notes or specifications"><?= $detail["keterangan"] ?? "" ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="row g-4">
            <div class="col-12">
                <div class="card form-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('/purchasing/po-attachment') ?>" class="btn btn-secondary btn-action">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-action">
                                <i class="fas fa-save me-2"></i>Save PO Attachment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
    <script>
$(function(){
    const allAttachments = <?= json_encode($attachments ?? []) ?>; // each: id_attachment, tipe, merk, model
    const $tipe = $('#att_tipe');
    const $merk = $('#att_merk');
    const $model = $('#att_model');
    const preTipe = '<?= esc($detail['tipe'] ?? '') ?>';
    const preMerk = '<?= esc($detail['merk'] ?? '') ?>';
    const preAttachmentId = '<?= esc($detail['attachment_id'] ?? '') ?>';

    function resetMerk(){ $merk.html('<option value="">Select Merk...</option>').trigger('change'); }
    function resetModel(){ $model.html('<option value="">Select Model...</option>').trigger('change'); }

    function populateMerk(){
        const t = $tipe.val();
        resetMerk(); resetModel();
        if(!t) return;
        const merkSet = [...new Set(allAttachments.filter(a=>a.tipe===t).map(a=>a.merk).filter(Boolean))];
        merkSet.forEach(m=> $merk.append(`<option value="${m}">${m}</option>`));
        if(preMerk){ $merk.val(preMerk).trigger('change'); }
    }
    function populateModel(){
        const t = $tipe.val();
        const m = $merk.val();
        resetModel();
        if(!t||!m) return;
        const models = allAttachments.filter(a=>a.tipe===t && a.merk===m);
        models.forEach(row=>{
            const sel = preAttachmentId && preAttachmentId == row.id_attachment ? 'selected' : '';
            $model.append(`<option value="${row.id_attachment}" ${sel}>${row.model}</option>`);
        });
        $model.trigger('change');
    }
    $tipe.on('change', function(){ populateMerk(); });
    $merk.on('change', function(){ populateModel(); });

    // Show/hide sections based on PO Type
    function toggleSections(){
        const poType = $('select[name="tipe_po"]').val();
        if(poType==='Attachment'){ $('#attachment-fields').show(); $('#battery-fields').hide(); }
        else if(poType==='Battery'){ $('#attachment-fields').hide(); $('#battery-fields').show(); }
        else { $('#attachment-fields').hide(); $('#battery-fields').hide(); }
    }
    $('select[name="tipe_po"]').on('change select2:select', toggleSections);
    toggleSections();

    // Initialize Select2 (ensure after dynamic options)
    $('.select2-sm').select2({ theme:'bootstrap-5' });

    // Pre-populate if editing
    if(preTipe){ populateMerk(); }
    if(preTipe && preMerk){ populateModel(); }
});
    </script>
    <?php if (session()->getFlashdata('errors')): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Submit!',
                    html: `
                        <ul style="text-align:left;">
                            <?php foreach (session()->getFlashdata('errors') as $field => $error): ?>
                                <li><strong><?= esc($field) ?>:</strong> <?= esc($error) ?></li>
                            <?php endforeach ?>
                        </ul>
                    `,
                    confirmButtonText: 'Oke'
                });
            });
        </script>
    <?php endif; ?>
<?= $this->endSection() ?>