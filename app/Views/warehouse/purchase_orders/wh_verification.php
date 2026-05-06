<?= $this->extend('layouts/base') ?>

<?php 
helper('global_permission');
$permissions = get_global_permission('warehouse');
$can_view = $permissions['view'];
$can_create = $permissions['create'];
$can_edit = $permissions['edit'];
$can_delete = $permissions['delete'];
$can_export = $permissions['export'];

$deliveryGroups = $deliveryGroups ?? [];
$queueCount = 0;
foreach ($deliveryGroups as $dg) {
    $queueCount += count($dg['bundles'] ?? []);
    $queueCount += count($dg['orphans'] ?? []);
}
?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<style>
    /* Scoped: jangan override .modal-header global */
    .wh-po-verify-page .verification-component {
        border-bottom: 1px solid #f1f3f4;
        padding: 12px 16px;
        transition: background-color 0.15s ease;
    }
    .wh-po-verify-page .verification-component:last-child { border-bottom: none; }
    .wh-po-verify-page .verification-component:hover { background-color: #fafbfc; }
    .wh-po-verify-page .component-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }
    .wh-po-verify-page .note-input-group, .wh-po-verify-page .sn-input-group {
        display: none;
        margin-top: 10px;
        padding: 10px 12px;
        background-color: #f8f9fa;
        border-radius: 6px;
        border: 1px solid #e8eaed;
    }
    .wh-po-verify-page .sn-input-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .wh-po-verify-page .btn-verify {
        font-size: 0.85rem;
        padding: 6px 12px;
        margin: 0 3px;
        border-radius: 4px;
        font-weight: 500;
    }
    .wh-po-verify-page .btn-verify.active {
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        transform: scale(1.02);
    }
    #verifyWhPoModal .table-verification-wh { font-size: 0.875rem; }
    #verifyWhPoModal .wh-vendor-spec-pre {
        white-space: pre-wrap;
        max-height: 220px;
        overflow: auto;
        font-size: 0.8rem;
    }
    /* Select2 di dalam tabel verifikasi */
    #unitVerificationFormInline .select2-container { min-width: 100%; }
    #unitVerificationFormInline .select2-container--default .select2-selection--single {
        height: 31px;
        border: 1px solid #333;
        border-radius: 4px;
        font-size: 0.875rem;
    }
    #unitVerificationFormInline .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 29px;
        padding-left: 8px;
        padding-right: 24px;
    }
    #unitVerificationFormInline .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 29px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid wh-po-verify-page">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-1">PO Verification (Gudang)</h4>
            <p class="text-muted mb-0">Purchasing input SN → delivery <em>Received</em> → verifikasi fisik per packing list. Unit, attachment, baterai, dan charger (termasuk baris PI terpisah) dalam satu antrean.</p>
        </div>
        <span class="badge badge-soft-primary px-3 py-2">Antrean: <span id="wh-verify-badge-count"><?= (int) $queueCount ?></span> item</span>
    </div>

    <?= view('warehouse/purchase_orders/delivery_verification_list', [
        'deliveryGroups' => $deliveryGroups,
    ]) ?>
</div>

<div class="modal fade" id="verifyWhPoModal" tabindex="-1" aria-labelledby="verifyWhPoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verifyWhPoModalLabel">Verifikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="wh-verify-modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="btn-submit-unit-verification">
                    <i class="fas fa-check-circle me-2"></i>Submit Verifikasi
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>

<script>
window.baseUrl = '<?= rtrim(base_url(), '/') ?>';
window.whPoVerifyModalMode = true;
</script>

<?php
echo view('warehouse/purchase_orders/tabs/attachment_verification_script');
echo view('warehouse/purchase_orders/tabs/unit_verification_script');
?>

<script>
(function () {
    const modalEl = document.getElementById('verifyWhPoModal');
    let verifyModal = null;
    function getVerifyModal() {
        if (!modalEl) return null;
        if (typeof bootstrap === 'undefined' || !bootstrap.Modal) return null;
        if (!verifyModal) {
            verifyModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        }
        return verifyModal;
    }

    function whDecrementRemainForDelivery(deliveryId) {
        const id = String(deliveryId);
        const $cells = $('.wh-lbl-remain-pl[data-delivery-id="' + id + '"]');
        if (!$cells.length) return;
        let n = parseInt($cells.first().text(), 10) || 0;
        if (n > 0) {
            n -= 1;
            $cells.text(String(n));
        }
        if (window.whPendingByDelivery && Object.prototype.hasOwnProperty.call(window.whPendingByDelivery, id)) {
            window.whPendingByDelivery[id] = Math.max(0, (parseInt(window.whPendingByDelivery[id], 10) || 0) - 1);
        }
    }

    function whBumpBadgeCount(delta) {
        const $b = $('#wh-verify-badge-count');
        if (!$b.length) return;
        let n = parseInt($b.text(), 10) || 0;
        n = Math.max(0, n + delta);
        $b.text(String(n));
    }

    /** Satu item selesai di PL (embed att/bat/chg) tanpa menghapus baris antrean bundle */
    window.whPoVerifyDecrementDeliveryPendingOnly = function (deliveryId) {
        whDecrementRemainForDelivery(deliveryId);
    };

    window.whPoVerifyAfterUnitSuccess = function (deliveryId, unitId) {
        whDecrementRemainForDelivery(deliveryId);
        whBumpBadgeCount(-1);
        $('#wh-queue-bundle-d' + deliveryId + '-u' + unitId).remove();
        if ($('#whVerificationQueueBody .wh-queue-row').length === 0) {
            $('#whVerificationQueueBody').html('<tr class="wh-queue-empty"><td colspan="6" class="text-center text-muted py-4">Tidak ada baris yang menunggu verifikasi.</td></tr>');
        }
        const m = getVerifyModal();
        if (m) m.hide();
    };

    window.whPoVerifyAfterOrphanSuccess = function (deliveryId, attachmentId) {
        whDecrementRemainForDelivery(deliveryId);
        whBumpBadgeCount(-1);
        $('#wh-queue-orphan-d' + deliveryId + '-a' + attachmentId).remove();
        if ($('#whVerificationQueueBody .wh-queue-row').length === 0) {
            $('#whVerificationQueueBody').html('<tr class="wh-queue-empty"><td colspan="6" class="text-center text-muted py-4">Tidak ada baris yang menunggu verifikasi.</td></tr>');
        }
        const m = getVerifyModal();
        if (m) m.hide();
    };

    function applyWhQueueFilters() {
        const po = ($('#whFilterPo').val() || '').trim();
        const pl = ($('#whFilterPl').val() || '').trim();
        const q = ($('#whFilterSearch').val() || '').trim().toLowerCase();
        let visible = 0;
        $('#whVerificationQueueBody tr.wh-queue-row').each(function () {
            const $r = $(this);
            const rpo = ($r.attr('data-po') || '').trim();
            const rdid = String($r.attr('data-delivery-id') || '');
            const search = ($r.attr('data-search') || '').toLowerCase();
            let ok = true;
            if (po && rpo !== po) ok = false;
            if (pl && rdid !== pl) ok = false;
            if (q && search.indexOf(q) === -1) ok = false;
            $r.toggle(ok);
            if (ok) visible++;
        });
    }

    /** Dropdown PL mengikuti PO lewat atribut data-po (cocok persis, bukan substring label). */
    function whSyncPlOptionsForPo() {
        const po = ($('#whFilterPo').val() || '').trim();
        const $pl = $('#whFilterPl');
        const prevPl = $pl.val();
        $pl.find('option').each(function () {
            const $o = $(this);
            if ($o.val() === '') {
                $o.prop('disabled', false);
                return;
            }
            const optPo = ($o.attr('data-po') || '').trim();
            if (!po) {
                $o.prop('disabled', false);
            } else {
                $o.prop('disabled', optPo !== po);
            }
        });
        if (prevPl && $pl.find('option[value="' + prevPl + '"]:enabled').length === 0) {
            $pl.val('');
        }
    }

    $('#whFilterPo').on('change', function () {
        whSyncPlOptionsForPo();
        applyWhQueueFilters();
    });

    $('#whFilterPl').on('change', function () {
        const $sel = $(this).find('option:selected');
        const optPo = ($sel.attr('data-po') || '').trim();
        const plVal = ($sel.val() || '').trim();
        if (plVal && optPo) {
            const curPo = ($('#whFilterPo').val() || '').trim();
            if (curPo !== optPo) {
                $('#whFilterPo').val(optPo);
                whSyncPlOptionsForPo();
            }
        }
        applyWhQueueFilters();
    });

    $('#whFilterSearch').on('input change', function () {
        applyWhQueueFilters();
    });

    $(document).on('click', '.wh-btn-open-verify', function (e) {
        e.preventDefault();
        const $tr = $(this).closest('tr.wh-queue-row');
        const kind = $tr.attr('data-verify-kind');
        const title = $tr.attr('data-modal-title') || 'Verifikasi';
        let raw = $tr.attr('data-payload');
        $('#verifyWhPoModalLabel').text(title);
        $('#wh-verify-modal-body').empty();
        $('#btn-submit-unit-verification').prop('disabled', false).text('Submit Verifikasi');
        let payload;
        try {
            payload = JSON.parse(raw);
        } catch (err) {
            console.error(err);
            return;
        }
        let html = '';
        if (kind === 'bundle' && typeof window.createBundleVerificationCard === 'function') {
            html = window.createBundleVerificationCard(payload);
        } else if (kind === 'orphan' && typeof window.createOrphanAttachmentVerificationCard === 'function') {
            html = window.createOrphanAttachmentVerificationCard(payload);
        }
        $('#wh-verify-modal-body').html(html);
        const m = getVerifyModal();
        if (m) m.show();
        setTimeout(function () {
            if (typeof window.loadUnitVerificationDropdowns === 'function') window.loadUnitVerificationDropdowns();
            if (typeof window.loadAttachmentVerificationDropdowns === 'function') window.loadAttachmentVerificationDropdowns();
            if (typeof window.checkAllAttachmentVerifiedInline === 'function') {
                $('#wh-verify-modal-body .attachment-inline-verify-form').each(function () {
                    window.checkAllAttachmentVerifiedInline($(this));
                });
            }
        }, 120);
    });

    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', function () {
            $('#wh-verify-modal-body').empty();
            $('#btn-submit-unit-verification').prop('disabled', false).text('Submit Verifikasi');
        });
    }

    applyWhQueueFilters();
})();
</script>

<?= $this->endSection() ?>
