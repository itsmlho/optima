<?= $this->extend('layouts/base') ?>

<?php
/**
 * Asset Disposal — Detail Transaksi (Unit or Component)
 *
 * Variables: $sale (array), $sale_type ('unit'|'component'), $bundled_components (array), $title (string)
 * BADGE: badge-soft-green (COMPLETED), badge-soft-red (CANCELLED)
 * PERMISSION: purchasing.unit_sale.delete (for cancel button)
 */

helper('global_permission');
$can_delete = canPerformAction('purchasing', 'unit_sale', 'delete');
$isUnit     = ($sale_type === 'unit');
$cancelUrl  = $isUnit
    ? base_url('purchasing/asset-disposal/cancel/unit/' . $sale['id'])
    : base_url('purchasing/asset-disposal/cancel/component/' . $sale['id']);

$typeBadgeMap = [
    'ATTACHMENT' => 'badge-soft-purple',
    'CHARGER'    => 'badge-soft-cyan',
    'BATTERY'    => 'badge-soft-orange',
    'SPAREPART'  => 'badge-soft-gray',
];
?>

<?= $this->section('title') ?><?= lang('Purchasing.asset_disposal_detail') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>"><?= lang('App.dashboard') ?></a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('/purchasing') ?>"><?= lang('Purchasing.module_name') ?></a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('/purchasing/asset-disposal') ?>"><?= lang('Purchasing.asset_disposal') ?></a></li>
        <li class="breadcrumb-item active"><?= esc($sale['no_dokumen']) ?></li>
    </ol>
</nav>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1">
            <i class="bi bi-receipt me-2 text-primary"></i><?= lang('Purchasing.asset_disposal_detail') ?>
        </h4>
        <p class="text-muted mb-0">
            Dokumen: <span class="font-monospace fw-semibold"><?= esc($sale['no_dokumen']) ?></span>
            &nbsp;
            <?php if ($isUnit): ?>
                <span class="badge badge-soft-blue">UNIT</span>
            <?php else: ?>
                <?php $typeBadge = $typeBadgeMap[$sale['asset_type'] ?? ''] ?? 'badge-soft-gray'; ?>
                <span class="badge <?= $typeBadge ?>"><?= esc($sale['asset_type'] ?? '') ?></span>
            <?php endif; ?>
            &nbsp;
            <?php if ($sale['status'] === 'CANCELLED'): ?>
                <span class="badge badge-soft-red"><?= lang('Common.cancelled') ?></span>
            <?php else: ?>
                <span class="badge badge-soft-green"><?= lang('Common.completed') ?></span>
            <?php endif; ?>
        </p>
    </div>
    <div class="d-flex gap-2">
        <?php if ($sale['status'] === 'COMPLETED' && $can_delete): ?>
        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
            <i class="fas fa-times-circle me-1"></i><?= lang('Purchasing.cancel_sale') ?>
        </button>
        <?php endif; ?>
        <a href="<?= base_url('/purchasing/asset-disposal') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Asset Info (left card) -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <?php if ($isUnit): ?>
                        <i class="bi bi-truck me-2 text-primary"></i><?= lang('Purchasing.unit_info') ?>
                    <?php else: ?>
                        <i class="bi bi-box me-2 text-primary"></i><?= lang('Purchasing.asset_info') ?>
                    <?php endif; ?>
                </h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <?php if ($isUnit): ?>
                        <!-- Unit-specific fields -->
                        <dt class="col-sm-5 text-muted"><?= lang('App.unit_number') ?></dt>
                        <dd class="col-sm-7 fw-semibold font-monospace">
                            <?= esc($sale['no_unit'] ?: ($sale['no_unit_na'] ?? '') ?: 'UNIT-' . $sale['unit_id']) ?>
                        </dd>

                        <dt class="col-sm-5 text-muted"><?= lang('Purchasing.unit_brand_model') ?></dt>
                        <dd class="col-sm-7"><?= esc(trim(($sale['merk_unit'] ?? '') . ' ' . ($sale['model_unit'] ?? ''))) ?: '-' ?></dd>

                        <dt class="col-sm-5 text-muted"><?= lang('Purchasing.unit_type') ?></dt>
                        <dd class="col-sm-7"><?= esc($sale['tipe_unit'] ?? '-') ?></dd>

                        <dt class="col-sm-5 text-muted"><?= lang('Purchasing.unit_capacity') ?></dt>
                        <dd class="col-sm-7"><?= $sale['kapasitas'] ? esc($sale['kapasitas']) . ' ton' : '-' ?></dd>

                        <dt class="col-sm-5 text-muted"><?= lang('App.serial_number') ?></dt>
                        <dd class="col-sm-7 font-monospace"><?= esc($sale['serial_number'] ?? '-') ?></dd>

                        <dt class="col-sm-5 text-muted"><?= lang('Purchasing.unit_year') ?></dt>
                        <dd class="col-sm-7"><?= esc($sale['tahun_unit'] ?? '-') ?></dd>

                        <dt class="col-sm-5 text-muted"><?= lang('Purchasing.status_before') ?></dt>
                        <dd class="col-sm-7">
                            <span class="badge badge-soft-blue"><?= esc($sale['previous_status_name'] ?? '-') ?></span>
                        </dd>

                        <dt class="col-sm-5 text-muted"><?= lang('Purchasing.status_current') ?></dt>
                        <dd class="col-sm-7">
                            <?php if ($sale['status'] === 'CANCELLED'): ?>
                                <span class="badge badge-soft-blue"><?= esc($sale['previous_status_name'] ?? '-') ?></span>
                                <small class="text-muted"><?= lang('Purchasing.status_reverted') ?></small>
                            <?php else: ?>
                                <span class="badge badge-soft-red">SOLD</span>
                            <?php endif; ?>
                        </dd>

                    <?php else: ?>
                        <!-- Component-specific fields -->
                        <dt class="col-sm-5 text-muted"><?= lang('Purchasing.asset_type') ?></dt>
                        <dd class="col-sm-7">
                            <?php $tb = $typeBadgeMap[$sale['asset_type'] ?? ''] ?? 'badge-soft-gray'; ?>
                            <span class="badge <?= $tb ?>"><?= esc($sale['asset_type'] ?? '') ?></span>
                        </dd>

                        <dt class="col-sm-5 text-muted"><?= lang('Purchasing.asset_info') ?></dt>
                        <dd class="col-sm-7 fw-semibold">
                            <?= esc(\App\Models\ComponentSaleModel::getAssetLabel($sale)) ?>
                        </dd>

                        <?php if (!empty($sale['serial_number'])): ?>
                        <dt class="col-sm-5 text-muted"><?= lang('App.serial_number') ?></dt>
                        <dd class="col-sm-7 font-monospace"><?= esc($sale['serial_number']) ?></dd>
                        <?php endif; ?>

                        <?php if (!empty($sale['item_number'])): ?>
                        <dt class="col-sm-5 text-muted">Item Number</dt>
                        <dd class="col-sm-7 font-monospace"><?= esc($sale['item_number']) ?></dd>
                        <?php endif; ?>

                        <?php if (!empty($sale['previous_status'])): ?>
                        <dt class="col-sm-5 text-muted"><?= lang('Purchasing.status_before') ?></dt>
                        <dd class="col-sm-7">
                            <span class="badge badge-soft-blue"><?= esc($sale['previous_status']) ?></span>
                        </dd>
                        <?php endif; ?>

                        <?php if (!empty($sale['linked_unit_sale_id'])): ?>
                        <dt class="col-sm-5 text-muted"><?= lang('Purchasing.sold_with_unit') ?></dt>
                        <dd class="col-sm-7">
                            <span class="badge badge-soft-yellow"><?= lang('Purchasing.sold_with_unit') ?></span>
                            <a href="<?= base_url('purchasing/asset-disposal/detail/unit/' . $sale['linked_unit_sale_id']) ?>" class="ms-1 small">
                                <i class="fas fa-external-link-alt"></i> Lihat Unit
                            </a>
                        </dd>
                        <?php endif; ?>

                        <?php if (($sale['asset_type'] ?? '') === 'SPAREPART'): ?>
                        <dt class="col-sm-12 mt-2">
                            <div class="alert alert-info small mb-0 py-1 px-2">
                                <i class="fas fa-info-circle me-1"></i><?= lang('Purchasing.sparepart_note') ?>
                            </div>
                        </dt>
                        <?php endif; ?>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>

    <!-- Sale Info -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-receipt me-2 text-success"></i><?= lang('Purchasing.sale_info') ?>
                </h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted"><?= lang('Purchasing.sale_document_no') ?></dt>
                    <dd class="col-sm-7 fw-semibold font-monospace"><?= esc($sale['no_dokumen']) ?></dd>

                    <dt class="col-sm-5 text-muted"><?= lang('Purchasing.sale_date') ?></dt>
                    <dd class="col-sm-7"><?= date('d F Y', strtotime($sale['tanggal_jual'])) ?></dd>

                    <dt class="col-sm-5 text-muted"><?= lang('Purchasing.buyer_name') ?></dt>
                    <dd class="col-sm-7 fw-semibold"><?= esc($sale['nama_pembeli']) ?></dd>

                    <?php if ($sale['telepon_pembeli']): ?>
                    <dt class="col-sm-5 text-muted"><?= lang('Purchasing.buyer_phone') ?></dt>
                    <dd class="col-sm-7"><?= esc($sale['telepon_pembeli']) ?></dd>
                    <?php endif; ?>

                    <?php if ($sale['alamat_pembeli']): ?>
                    <dt class="col-sm-5 text-muted"><?= lang('Purchasing.buyer_address') ?></dt>
                    <dd class="col-sm-7"><?= esc($sale['alamat_pembeli']) ?></dd>
                    <?php endif; ?>

                    <dt class="col-sm-5 text-muted"><?= lang('Purchasing.sale_price') ?></dt>
                    <dd class="col-sm-7 fw-bold text-success">
                        Rp <?= number_format((float)$sale['harga_jual'], 0, ',', '.') ?>
                    </dd>

                    <dt class="col-sm-5 text-muted"><?= lang('Purchasing.payment_method') ?></dt>
                    <dd class="col-sm-7">
                        <?php
                            $metodeBadge = ['CASH' => 'badge-soft-green', 'TRANSFER' => 'badge-soft-blue', 'CEK' => 'badge-soft-cyan', 'KREDIT' => 'badge-soft-orange'];
                            $mc = $metodeBadge[$sale['metode_pembayaran']] ?? 'badge-soft-gray';
                        ?>
                        <span class="badge <?= $mc ?>"><?= esc($sale['metode_pembayaran']) ?></span>
                    </dd>

                    <?php if ($sale['no_kwitansi']): ?>
                    <dt class="col-sm-5 text-muted"><?= lang('Purchasing.receipt_no') ?></dt>
                    <dd class="col-sm-7 font-monospace"><?= esc($sale['no_kwitansi']) ?></dd>
                    <?php endif; ?>

                    <?php if (!empty($sale['no_bast'])): ?>
                    <dt class="col-sm-5 text-muted"><?= lang('Purchasing.bast_no') ?></dt>
                    <dd class="col-sm-7 font-monospace"><?= esc($sale['no_bast']) ?></dd>
                    <?php endif; ?>

                    <?php if (!empty($sale['no_invoice'])): ?>
                    <dt class="col-sm-5 text-muted"><?= lang('Purchasing.invoice_no') ?></dt>
                    <dd class="col-sm-7 font-monospace"><?= esc($sale['no_invoice']) ?></dd>
                    <?php endif; ?>

                    <?php if ($sale['keterangan']): ?>
                    <dt class="col-sm-5 text-muted"><?= lang('Common.notes') ?></dt>
                    <dd class="col-sm-7"><?= esc($sale['keterangan']) ?></dd>
                    <?php endif; ?>

                    <dt class="col-sm-5 text-muted"><?= lang('Purchasing.recorded_by') ?></dt>
                    <dd class="col-sm-7"><?= esc(trim($sale['seller_name'] ?? '-')) ?></dd>

                    <dt class="col-sm-5 text-muted"><?= lang('Purchasing.recorded_at') ?></dt>
                    <dd class="col-sm-7 text-muted small">
                        <?= date('d/m/Y H:i', strtotime($sale['created_at'])) ?>
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Bundled Components (unit sales with bundled items) -->
    <?php if ($isUnit && !empty($bundled_components)): ?>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-link me-2 text-info"></i><?= lang('Purchasing.bundled_components') ?>
                    <span class="badge badge-soft-blue ms-1"><?= count($bundled_components) ?></span>
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th><?= lang('Purchasing.sale_document_no') ?></th>
                                <th><?= lang('Purchasing.asset_type') ?></th>
                                <th><?= lang('Purchasing.asset_info') ?></th>
                                <th class="text-end"><?= lang('Purchasing.sale_price') ?></th>
                                <th class="text-center"><?= lang('Common.status') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bundled_components as $bc): ?>
                            <tr>
                                <td class="font-monospace small"><?= esc($bc['no_dokumen']) ?></td>
                                <td>
                                    <?php $bcBadge = $typeBadgeMap[$bc['asset_type'] ?? ''] ?? 'badge-soft-gray'; ?>
                                    <span class="badge <?= $bcBadge ?>"><?= esc($bc['asset_type']) ?></span>
                                </td>
                                <td><?= esc(\App\Models\ComponentSaleModel::getAssetLabel($bc)) ?></td>
                                <td class="text-end text-nowrap">Rp <?= number_format((float)($bc['harga_jual'] ?? 0), 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <?php if (($bc['status'] ?? '') === 'CANCELLED'): ?>
                                        <span class="badge badge-soft-red"><?= lang('Common.cancelled') ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-soft-green"><?= lang('Common.completed') ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Cancellation Info -->
    <?php if ($sale['status'] === 'CANCELLED' && $sale['cancelled_at']): ?>
    <div class="col-12">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h6 class="card-title mb-0">
                    <i class="fas fa-times-circle me-2"></i><?= lang('Purchasing.cancellation_info') ?>
                </h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3 text-muted"><?= lang('Purchasing.cancelled_at') ?></dt>
                    <dd class="col-sm-9"><?= date('d F Y H:i', strtotime($sale['cancelled_at'])) ?></dd>

                    <dt class="col-sm-3 text-muted"><?= lang('Purchasing.cancelled_by') ?></dt>
                    <dd class="col-sm-9"><?= esc(trim($sale['canceller_name'] ?? '-')) ?></dd>

                    <dt class="col-sm-3 text-muted"><?= lang('Purchasing.cancel_reason') ?></dt>
                    <dd class="col-sm-9"><?= esc($sale['cancelled_reason']) ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- ─── Cancel Modal ──────────────────────────────────────── -->
<?php if ($sale['status'] === 'COMPLETED' && $can_delete): ?>
<div class="modal fade" id="cancelModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="cancelModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-danger">
                <h5 class="modal-title text-danger" id="cancelModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= lang('Purchasing.cancel_sale') ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= lang('Common.close') ?>"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-info-circle me-1"></i>
                    <?php if ($isUnit): ?>
                        <?= sprintf(lang('Purchasing.cancel_sale_warning'), esc($sale['previous_status_name'] ?? '-')) ?>
                    <?php else: ?>
                        <?= lang('Purchasing.cancel_component_warning') ?>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold"><?= lang('Purchasing.cancel_reason') ?> <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="cancelledReason" rows="3"
                              placeholder="<?= lang('Purchasing.cancel_reason_placeholder') ?>" maxlength="1000"></textarea>
                    <div class="form-text"><?= lang('Purchasing.cancel_reason_help') ?></div>
                </div>
                <div id="cancelAlert" class="d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('Common.close') ?></button>
                <button type="button" class="btn btn-danger" id="btnConfirmCancel">
                    <i class="fas fa-times-circle me-1"></i><?= lang('Purchasing.confirm_cancel') ?>
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<?php if ($sale['status'] === 'COMPLETED' && $can_delete): ?>
<script>
(function () {
    'use strict';

    var _baseUrl     = (typeof BASE_URL !== 'undefined') ? BASE_URL : '<?= base_url() ?>';
    var _cancelUrl   = '<?= $cancelUrl ?>';
    var _cancelLabel = '<i class="fas fa-times-circle me-1"></i><?= lang('Purchasing.confirm_cancel') ?>';

    $('#btnConfirmCancel').on('click', function () {
        var reason = $('#cancelledReason').val().trim();
        if (!reason) {
            $('#cancelAlert').removeClass('d-none').addClass('alert alert-danger').html('<?= lang('Purchasing.cancel_reason') ?> <?= lang('Common.required') ?>.');
            return;
        }

        var btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>');
        $('#cancelAlert').addClass('d-none').html('').removeClass('alert alert-danger alert-success');

        $.ajax({
            url     : _cancelUrl,
            type    : 'POST',
            data    : {
                [window.csrfTokenName]: window.csrfTokenValue,
                cancelled_reason      : reason,
            },
            dataType: 'json',
            success : function (res) {
                if (res.success) {
                    window.location.reload();
                } else {
                    $('#cancelAlert').removeClass('d-none').addClass('alert alert-danger').html(res.message || '<?= lang('App.error_occurred') ?>');
                    btn.prop('disabled', false).html(_cancelLabel);
                }
            },
            error: function () {
                $('#cancelAlert').removeClass('d-none').addClass('alert alert-danger').html('<?= lang('App.error_occurred') ?>');
                btn.prop('disabled', false).html(_cancelLabel);
            },
        });
    });

    $('#cancelModal').on('hidden.bs.modal', function () {
        $('#cancelledReason').val('');
        $('#cancelAlert').addClass('d-none').html('').removeClass('alert alert-danger');
        $('#btnConfirmCancel').prop('disabled', false).html(_cancelLabel);
    });
}());
</script>
<?php endif; ?>

<?= $this->endSection() ?>
