<?= $this->extend('layouts/base') ?>

<?php 
helper('global_permission');
$permissions = get_global_permission('warehouse');
$can_view = $permissions['view'];
$can_create = $permissions['create'];
$can_edit = $permissions['edit'];
$can_delete = $permissions['delete'];
$can_export = $permissions['export'];
?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<style>
    .modal-header { background-color: #343a40; color: white; border-radius: 15px 15px 0 0; }
    .verification-component { 
        border-bottom: 1px solid #f1f3f4; 
        padding: 12px 16px;
        transition: background-color 0.15s ease;
    }
    .verification-component:last-child { border-bottom: none; }
    .verification-component:hover { background-color: #fafbfc; }
    .component-row { 
        display: flex; 
        justify-content: space-between; 
        align-items: center;
        margin-bottom: 6px;
    }
    .note-input-group, .sn-input-group { 
        display: none; 
        margin-top: 10px;
        padding: 10px 12px;
        background-color: #f8f9fa;
        border-radius: 6px;
        border: 1px solid #e8eaed;
    }
    .sn-input-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .btn-verify { 
        font-size: 0.85rem; 
        padding: 6px 12px;
        margin: 0 3px;
        border-radius: 4px;
        font-weight: 500;
    }
    .btn-verify.active {
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        transform: scale(1.02);
    }
    .collapse-row > td { border-top: none !important; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="card table-card">
    <div class="alert alert-light border-bottom rounded-0 mb-0 py-2 px-3 small text-muted">
        <strong>Alur singkat:</strong> Purchasing input SN dari vendor → tandai delivery <em>Received</em> → Anda verifikasi fisik per <strong>packing list</strong>.
        Unit dan attachment/charger/baterai pada paket yang sama ditampilkan berurutan. Sparepart tidak ditangani di halaman ini.
    </div>
    <div class="card-body p-3">
        <?= view('warehouse/purchase_orders/delivery_verification_list', [
            'deliveryGroups' => $deliveryGroups ?? [],
        ]) ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>

<script>
const baseUrl = '<?= rtrim(base_url(), '/') ?>';
</script>

<?php
// Attachment helpers harus tersedia sebelum unit (bundle card memanggil createAttachmentDetailCard).
echo view('warehouse/purchase_orders/tabs/attachment_verification_script');
echo view('warehouse/purchase_orders/tabs/unit_verification_script');
?>

<script>
(function() {
    $(document).on('click', '.wh-bundle-pick', function(e) {
        e.preventDefault();
        $('.wh-bundle-pick').removeClass('active');
        $(this).addClass('active');
        const raw = $(this).attr('data-bundle');
        let bundle;
        try {
            bundle = JSON.parse(raw);
        } catch (err) {
            console.error(err);
            return;
        }
        if (typeof window.createBundleVerificationCard === 'function') {
            $('#wh-verification-detail-container').html(window.createBundleVerificationCard(bundle));
            setTimeout(function() {
                if (typeof window.loadUnitVerificationDropdowns === 'function') window.loadUnitVerificationDropdowns();
                if (typeof window.loadAttachmentVerificationDropdowns === 'function') window.loadAttachmentVerificationDropdowns();
                if (typeof window.checkAllAttachmentVerifiedInline === 'function') {
                    $('#wh-verification-detail-container .attachment-inline-verify-form').each(function() {
                        window.checkAllAttachmentVerifiedInline($(this));
                    });
                }
            }, 120);
        }
    });
    $(document).on('click', '.wh-orphan-pick', function(e) {
        e.preventDefault();
        $('.wh-orphan-pick').removeClass('active');
        $(this).addClass('active');
        const raw = $(this).attr('data-orphan');
        let payload;
        try {
            payload = JSON.parse(raw);
        } catch (err) {
            console.error(err);
            return;
        }
        if (typeof window.createOrphanAttachmentVerificationCard === 'function') {
            $('#wh-verification-detail-container').html(window.createOrphanAttachmentVerificationCard(payload));
            setTimeout(function() {
                if (typeof window.loadUnitVerificationDropdowns === 'function') window.loadUnitVerificationDropdowns();
                if (typeof window.loadAttachmentVerificationDropdowns === 'function') window.loadAttachmentVerificationDropdowns();
                if (typeof window.checkAllAttachmentVerifiedInline === 'function') {
                    window.checkAllAttachmentVerifiedInline($('#wh-verification-detail-container .attachment-inline-verify-form').first());
                }
            }, 120);
        }
    });
})();
</script>

<?= $this->endSection() ?>
