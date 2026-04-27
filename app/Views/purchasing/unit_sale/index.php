<?= $this->extend('layouts/base') ?>

<?php
/**
 * Asset Disposal — Unified listing & create form
 *
 * BADGE STANDARDS:
 * - Status COMPLETED → badge-soft-green
 * - Status CANCELLED → badge-soft-red
 * - Metode CASH     → badge-soft-green
 * - Metode TRANSFER → badge-soft-blue
 * - Metode CEK      → badge-soft-cyan
 * - Metode KREDIT   → badge-soft-orange
 * - Type UNIT       → badge-soft-blue
 * - Type ATTACHMENT → badge-soft-purple
 * - Type CHARGER    → badge-soft-cyan
 * - Type BATTERY    → badge-soft-orange
 * - Type SPAREPART  → badge-soft-gray
 *
 * PERMISSION: purchasing.unit_sale.*
 */

helper('global_permission');
$can_create = canPerformAction('purchasing', 'unit_sale', 'create');
$can_delete = canPerformAction('purchasing', 'unit_sale', 'delete');
?>

<?= $this->section('title') ?><?= lang('Purchasing.asset_disposal') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>"><?= lang('App.dashboard') ?></a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('/purchasing') ?>"><?= lang('Purchasing.module_name') ?></a></li>
        <li class="breadcrumb-item active"><?= lang('Purchasing.asset_disposal') ?></li>
    </ol>
</nav>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-primary-soft">
            <div class="d-flex align-items-center">
                <div class="me-3"><i class="bi bi-truck stat-icon text-primary"></i></div>
                <div>
                    <div class="stat-value"><?= number_format($stats['total']) ?></div>
                    <div class="text-muted"><?= lang('Purchasing.total_sold') ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-info-soft">
            <div class="d-flex align-items-center">
                <div class="me-3"><i class="bi bi-calendar-year stat-icon text-info"></i></div>
                <div>
                    <div class="stat-value"><?= number_format($stats['this_year']) ?></div>
                    <div class="text-muted"><?= sprintf(lang('Purchasing.this_year'), date('Y')) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-warning-soft">
            <div class="d-flex align-items-center">
                <div class="me-3"><i class="bi bi-calendar-month stat-icon text-warning"></i></div>
                <div>
                    <div class="stat-value"><?= number_format($stats['this_month']) ?></div>
                    <div class="text-muted"><?= lang('Purchasing.this_month') ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-success-soft">
            <div class="d-flex align-items-center">
                <div class="me-3"><i class="bi bi-cash-coin stat-icon text-success"></i></div>
                <div>
                    <div class="stat-value" style="font-size:1.1rem;">
                        Rp&nbsp;<?= number_format((float)($stats['total_revenue'] ?? 0), 0, ',', '.') ?>
                    </div>
                    <div class="text-muted"><?= lang('Purchasing.total_revenue') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0">
                <i class="bi bi-receipt me-2 text-primary"></i><?= lang('Purchasing.asset_disposal') ?>
            </h5>
            <p class="text-muted small mb-0"><?= lang('Purchasing.asset_disposal_desc') ?></p>
        </div>
        <div class="d-flex gap-2">
            <?php if ($can_create): ?>
            <button type="button" class="btn btn-primary" id="btnSellAsset" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="fas fa-plus me-1"></i><?= lang('Purchasing.sell_asset') ?>
            </button>
            <?php else: ?>
            <button type="button" class="btn btn-secondary" disabled title="<?= lang('App.access_denied') ?>">
                <i class="fas fa-lock me-1"></i><?= lang('Purchasing.sell_asset') ?>
            </button>
            <?php endif; ?>
            <button type="button" class="btn btn-outline-primary" onclick="refreshTable()">
                <i class="fas fa-sync-alt me-1"></i><?= lang('Common.refresh') ?>
            </button>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="card-header border-top-0 py-0 bg-transparent">
        <ul class="nav nav-tabs card-header-tabs" id="disposalTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-sales-btn" data-bs-toggle="tab"
                        data-bs-target="#tab-sales" type="button" role="tab">
                    <i class="bi bi-receipt me-1"></i><?= lang('Purchasing.tab_sale_history') ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-unrecorded-btn" data-bs-toggle="tab"
                        data-bs-target="#tab-unrecorded" type="button" role="tab">
                    <i class="fas fa-exclamation-triangle me-1"></i><?= lang('Purchasing.tab_unrecorded') ?>
                    <?php if (($unrecorded_count ?? 0) > 0): ?>
                    <span class="badge badge-soft-orange ms-1"><?= (int)$unrecorded_count ?></span>
                    <?php endif; ?>
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content">
        <!-- ── Tab 1: Riwayat Penjualan ───────────────────── -->
        <div class="tab-pane fade show active" id="tab-sales" role="tabpanel">
            <!-- Filters -->
            <div class="card-body pb-0">
                <div class="row g-2 mb-3">
                    <div class="col-md-3">
                        <input type="text" id="filterSearch" class="form-control form-control-sm" placeholder="<?= lang('Purchasing.search_sale_placeholder') ?>">
                    </div>
                    <div class="col-md-2">
                        <select id="filterAssetType" class="form-select form-select-sm">
                            <option value=""><?= lang('Purchasing.all_types') ?></option>
                            <option value="UNIT">Unit</option>
                            <option value="ATTACHMENT"><?= lang('Purchasing.attachment') ?></option>
                            <option value="CHARGER"><?= lang('Purchasing.charger') ?></option>
                            <option value="BATTERY"><?= lang('Purchasing.battery') ?></option>
                            <option value="SPAREPART"><?= lang('Purchasing.sparepart') ?></option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="filterStatus" class="form-select form-select-sm">
                            <option value=""><?= lang('Purchasing.all_status') ?></option>
                            <option value="COMPLETED"><?= lang('Common.completed') ?></option>
                            <option value="CANCELLED"><?= lang('Common.cancelled') ?></option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" id="filterDateFrom" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <input type="date" id="filterDateTo" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-sm btn-outline-secondary w-100" onclick="applyFilters()">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table id="disposalTable" class="table table-striped table-hover w-100">
                        <thead class="table-light">
                            <tr>
                                <th><?= lang('Purchasing.sale_document_no') ?></th>
                                <th><?= lang('Purchasing.asset_type') ?></th>
                                <th><?= lang('Purchasing.asset_info') ?></th>
                                <th><?= lang('Purchasing.sale_date') ?></th>
                                <th><?= lang('Purchasing.buyer_name') ?></th>
                                <th><?= lang('Purchasing.sale_price') ?></th>
                                <th><?= lang('Purchasing.payment_method') ?></th>
                                <th><?= lang('Common.status') ?></th>
                                <th class="text-center"><?= lang('Common.actions') ?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ── Tab 2: Belum Terdata ───────────────────────── -->
        <div class="tab-pane fade" id="tab-unrecorded" role="tabpanel">
            <div class="card-body pb-0">
                <div class="alert alert-warning small d-flex gap-2 align-items-center py-2 mb-3">
                    <i class="fas fa-exclamation-triangle flex-shrink-0"></i>
                    <span><?= lang('Purchasing.unrecorded_alert') ?></span>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <input type="text" id="searchUnrecorded" class="form-control form-control-sm"
                               placeholder="<?= lang('Purchasing.search_unrecorded_ph') ?>">
                    </div>
                    <div class="col-md-3">
                        <select id="filterUnrecordedType" class="form-select form-select-sm" onchange="refreshUnrecordedTable()">
                            <option value=""><?= lang('Purchasing.all_types') ?></option>
                            <option value="UNIT" selected>Unit</option>
                            <option value="ATTACHMENT"><?= lang('Purchasing.attachment') ?></option>
                            <option value="CHARGER"><?= lang('Purchasing.charger') ?></option>
                            <option value="BATTERY"><?= lang('Purchasing.battery') ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table id="unrecordedTable" class="table table-hover w-100">
                        <thead class="table-light">
                            <tr>
                                <th style="width:120px"><?= lang('Purchasing.asset_type') ?></th>
                                <th><?= lang('Purchasing.asset_info') ?></th>
                                <th class="text-center" style="width:160px"><?= lang('Common.actions') ?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Retroactive Sale Modal (Asset Disposal page) -->
<div class="modal fade" id="modal-disposal-retro-sale" tabindex="-1" aria-labelledby="modalDisposalRetroLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="modalDisposalRetroLabel">
                    <i class="fas fa-handshake me-2"></i><?= lang('Purchasing.retro_modal_title') ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info small d-flex gap-2 align-items-start py-2 mb-3">
                    <i class="fas fa-info-circle mt-1 flex-shrink-0"></i>
                    <div><?= lang('Purchasing.retro_fill_desc') ?> <strong id="retro-asset-label">-</strong>. <?= lang('Purchasing.retro_doc_auto') ?></div>
                </div>
                <input type="hidden" id="retro-asset-type">
                <input type="hidden" id="retro-asset-id">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold"><?= lang('Purchasing.buyer_name') ?> <span class="text-danger">*</span></label>
                        <input type="text" id="retro-nama-pembeli" class="form-control form-control-sm" placeholder="<?= lang('Purchasing.buyer_name_placeholder') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold"><?= lang('Purchasing.sale_date') ?> <span class="text-danger">*</span></label>
                        <input type="date" id="retro-tanggal-jual" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold"><?= lang('Purchasing.buyer_phone') ?></label>
                        <input type="text" id="retro-telepon" class="form-control form-control-sm" placeholder="<?= lang('Common.optional') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold"><?= lang('Purchasing.sale_price') ?></label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="text" id="retro-harga" class="form-control form-control-sm" placeholder="0" inputmode="numeric">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold"><?= lang('Purchasing.buyer_address') ?></label>
                        <input type="text" id="retro-alamat" class="form-control form-control-sm" placeholder="<?= lang('Common.optional') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold"><?= lang('Purchasing.payment_method') ?></label>
                        <select id="retro-metode" class="form-select form-select-sm">
                            <option value="CASH">CASH</option>
                            <option value="TRANSFER" selected>TRANSFER</option>
                            <option value="CEK">CEK</option>
                            <option value="KREDIT">KREDIT</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold"><?= lang('Purchasing.receipt_no') ?></label>
                        <input type="text" id="retro-no-kwitansi" class="form-control form-control-sm" placeholder="<?= lang('Common.optional') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold"><?= lang('Purchasing.bast_no') ?></label>
                        <input type="text" id="retro-no-bast" class="form-control form-control-sm" placeholder="<?= lang('Common.optional') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold"><?= lang('Purchasing.invoice_no') ?></label>
                        <input type="text" id="retro-no-invoice" class="form-control form-control-sm" placeholder="<?= lang('Common.optional') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold"><?= lang('Common.notes') ?></label>
                        <textarea id="retro-keterangan" class="form-control form-control-sm" rows="2" placeholder="<?= lang('Purchasing.notes_placeholder') ?>"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
                <button type="button" class="btn btn-warning btn-sm" id="btn-submit-disposal-retro">
                    <i class="fas fa-save me-1"></i><?= lang('Purchasing.save_sale_data') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- Create Modal — Unified Asset Disposal Form                 -->
<!-- ═══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="createModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="createModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">
                    <i class="bi bi-receipt me-2 text-primary"></i><?= lang('Purchasing.record_sale') ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= lang('Common.close') ?>"></button>
            </div>
            <form id="createSaleForm" novalidate>
                <div class="modal-body">

                    <!-- Asset Type Selector -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('Purchasing.asset_type') ?> <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="asset_type" id="typeUnit" value="UNIT" checked>
                                <label class="form-check-label" for="typeUnit"><span class="badge badge-soft-blue">Unit</span></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="asset_type" id="typeAttachment" value="ATTACHMENT">
                                <label class="form-check-label" for="typeAttachment"><span class="badge badge-soft-purple"><?= lang('Purchasing.attachment') ?></span></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="asset_type" id="typeCharger" value="CHARGER">
                                <label class="form-check-label" for="typeCharger"><span class="badge badge-soft-cyan"><?= lang('Purchasing.charger') ?></span></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="asset_type" id="typeBattery" value="BATTERY">
                                <label class="form-check-label" for="typeBattery"><span class="badge badge-soft-orange"><?= lang('Purchasing.battery') ?></span></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="asset_type" id="typeSparepart" value="SPAREPART">
                                <label class="form-check-label" for="typeSparepart"><span class="badge badge-soft-gray"><?= lang('Purchasing.sparepart') ?></span></label>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- Nomor Dokumen + Tanggal -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('Purchasing.sale_document_no') ?> <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="no_dokumen" name="no_dokumen"
                                       placeholder="SALE-2026-00001" maxlength="50" required>
                                <button class="btn btn-outline-secondary" type="button" id="btnGenNumber"
                                        title="<?= lang('Purchasing.generate_number') ?>">
                                    <i class="fas fa-magic"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('Purchasing.sale_date') ?> <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_jual" name="tanggal_jual" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('Purchasing.bast_no') ?></label>
                            <input type="text" class="form-control" id="no_bast" name="no_bast" maxlength="100" placeholder="<?= lang('Purchasing.bast_no') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('Purchasing.invoice_no') ?></label>
                            <input type="text" class="form-control" id="no_invoice" name="no_invoice" maxlength="100" placeholder="<?= lang('Purchasing.invoice_no') ?>">
                        </div>
                    </div>

                    <!-- SECTION: Unit selection (shown when asset_type=UNIT) -->
                    <div id="sectionUnit">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><?= lang('App.unit') ?> <span class="text-danger">*</span></label>
                            <select class="form-select" id="unit_id" name="unit_id" style="width:100%"></select>
                            <div id="unitInfoBox" class="mt-2 p-2 rounded border bg-light d-none small">
                                <span id="unitInfoText"></span>
                            </div>
                        </div>

                        <!-- Bundled Components -->
                        <div id="bundledSection" class="d-none mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-link me-1 text-muted"></i><?= lang('Purchasing.bundled_components') ?>
                            </label>
                            <p class="text-muted small mb-2"><?= lang('Purchasing.include_components') ?></p>
                            <div id="bundledList" class="border rounded p-2 bg-light">
                                <div class="text-muted small text-center py-2"><?= lang('Purchasing.no_components_attached') ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION: Component selection (shown when asset_type is ATT/CHR/BAT/SPARE) -->
                    <div id="sectionComponent" class="d-none">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" id="componentLabel"><?= lang('Purchasing.asset_info') ?> <span class="text-danger">*</span></label>
                            <select class="form-select" id="asset_id" name="asset_id" style="width:100%"></select>
                        </div>
                        <div id="sparepartNote" class="alert alert-info small d-none">
                            <i class="fas fa-info-circle me-1"></i><?= lang('Purchasing.sparepart_note') ?>
                        </div>
                    </div>

                    <!-- Pembeli -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('Purchasing.buyer_name') ?> <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_pembeli" name="nama_pembeli" maxlength="255" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('Purchasing.buyer_phone') ?></label>
                            <input type="text" class="form-control" id="telepon_pembeli" name="telepon_pembeli" placeholder="08xxxxxxxxxx" maxlength="30">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('Purchasing.buyer_address') ?></label>
                        <input type="text" class="form-control" id="alamat_pembeli" name="alamat_pembeli" maxlength="500">
                    </div>

                    <!-- Keuangan -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('Purchasing.sale_price') ?> <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" id="harga_jual" name="harga_jual" placeholder="0" required inputmode="numeric">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('Purchasing.payment_method') ?> <span class="text-danger">*</span></label>
                            <select class="form-select" id="metode_pembayaran" name="metode_pembayaran" required>
                                <option value=""><?= lang('Purchasing.choose_payment_method') ?></option>
                                <option value="CASH">CASH</option>
                                <option value="TRANSFER">TRANSFER</option>
                                <option value="CEK">CEK</option>
                                <option value="KREDIT">KREDIT</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('Purchasing.receipt_no') ?></label>
                            <input type="text" class="form-control" id="no_kwitansi" name="no_kwitansi" maxlength="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('Common.notes') ?></label>
                            <input type="text" class="form-control" id="keterangan" name="keterangan" maxlength="1000">
                        </div>
                    </div>
                    <!-- Alert area -->
                    <div id="createAlert" class="d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitSale">
                        <i class="fas fa-save me-1"></i><?= lang('Purchasing.save_sale') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<!-- ─── Scripts ───────────────────────────────────────────── -->
<script>
(function () {
    'use strict';

    var _baseUrl       = (typeof BASE_URL !== 'undefined') ? BASE_URL : '<?= base_url() ?>';
    var _saleTable     = null;
    var _saveSaleLabel = '<i class="fas fa-save me-1"></i><?= lang('Purchasing.save_sale') ?>';
    var _currentType   = 'UNIT';

    // ═══════════════════════════════════════════════════════
    // DataTable — unified
    // ═══════════════════════════════════════════════════════
    function initTable() {
        _saleTable = $('#disposalTable').DataTable({
            processing : true,
            serverSide : false,
            ajax       : {
                url  : _baseUrl + 'purchasing/asset-disposal/getSalesData',
                type : 'GET',
                data : function (d) {
                    d.asset_type = $('#filterAssetType').val();
                    d.status     = $('#filterStatus').val();
                    d.date_from  = $('#filterDateFrom').val();
                    d.date_to    = $('#filterDateTo').val();
                },
            },
            columns: [
                { data: 'no_dokumen',   className: 'align-middle' },
                { data: 'asset_type',   className: 'align-middle text-center' },
                { data: 'asset_info',   className: 'align-middle' },
                { data: 'tanggal_jual', className: 'align-middle text-nowrap' },
                { data: 'pembeli',      className: 'align-middle' },
                { data: 'harga_jual',   className: 'align-middle text-end text-nowrap' },
                { data: 'metode',       className: 'align-middle text-center' },
                { data: 'status',       className: 'align-middle text-center' },
                { data: 'actions',      className: 'align-middle text-center', orderable: false, searchable: false },
            ],
            language: {
                processing  : '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                zeroRecords : '<?= lang('App.no_data') ?>',
                info        : '<?= lang('App.showing') ?>',
                infoEmpty   : '<?= lang('App.no_data') ?>',
                paginate    : { previous: '&laquo;', next: '&raquo;' },
            },
            order      : [[3, 'desc']],
            pageLength : 25,
        });
    }

    // ═══════════════════════════════════════════════════════
    // Filters
    // ═══════════════════════════════════════════════════════
    window.applyFilters = function () {
        if (_saleTable) { _saleTable.ajax.reload(); }
    };
    window.refreshTable = function () {
        if (_saleTable) { _saleTable.ajax.reload(); }
    };
    $('#filterSearch').on('keyup', function () {
        if (_saleTable) { _saleTable.search($(this).val()).draw(); }
    });
    $('#filterAssetType, #filterStatus').on('change', function () { applyFilters(); });

    // ═══════════════════════════════════════════════════════
    // Asset Type Toggle in Create Modal
    // ═══════════════════════════════════════════════════════
    $('input[name="asset_type"]').on('change', function () {
        _currentType = $(this).val();
        toggleAssetSections();
    });

    function toggleAssetSections() {
        var isUnit = (_currentType === 'UNIT');
        var isSparepart = (_currentType === 'SPAREPART');

        // Show/hide sections
        $('#sectionUnit').toggleClass('d-none', !isUnit);
        $('#sectionComponent').toggleClass('d-none', isUnit);
        $('#sparepartNote').toggleClass('d-none', !isSparepart);

        // Toggle required attributes
        $('#unit_id').prop('required', isUnit);
        $('#asset_id').prop('required', !isUnit);

        // Update label
        var labels = {
            ATTACHMENT: '<?= lang('Purchasing.attachment') ?>',
            CHARGER:    '<?= lang('Purchasing.charger') ?>',
            BATTERY:    '<?= lang('Purchasing.battery') ?>',
            SPAREPART:  '<?= lang('Purchasing.sparepart') ?>',
        };
        if (!isUnit) {
            $('#componentLabel').html((labels[_currentType] || '<?= lang('Purchasing.asset_info') ?>') + ' <span class="text-danger">*</span>');
            initComponentSelect2(_currentType);
        }

        // Clear selections
        if (isUnit) {
            destroySelect2('#asset_id');
        } else {
            destroySelect2('#unit_id');
            $('#unitInfoBox').addClass('d-none');
            $('#bundledSection').addClass('d-none');
        }
    }

    function destroySelect2(sel) {
        var $el = $(sel);
        if ($el.hasClass('select2-hidden-accessible')) {
            $el.val(null).trigger('change');
            $el.select2('destroy');
        }
        $el.html('');
    }

    // ═══════════════════════════════════════════════════════
    // Select2 — Unit
    // ═══════════════════════════════════════════════════════
    function initUnitSelect2() {
        destroySelect2('#unit_id');
        $('#unit_id').select2({
            dropdownParent : $('#createModal'),
            placeholder    : '<?= lang('Purchasing.search_unit_placeholder') ?>',
            allowClear     : true,
            minimumInputLength: 0,
            ajax: {
                url      : _baseUrl + 'purchasing/asset-disposal/getEligibleUnits',
                dataType : 'json',
                delay    : 250,
                data     : function (params) { return { q: params.term || '' }; },
                processResults: function (res) {
                    if (!res || !res.success) return { results: [] };
                    return { results: res.results };
                },
                error: function (jqXHR) {
                    // Select2 aborts previous requests when user types quickly — ignore those
                    if (jqXHR.statusText === 'abort') return;
                    if (window.OptimaNotify) OptimaNotify.error('Gagal memuat data unit.');
                },
                cache: false,
            },
        });

        $('#unit_id').on('select2:select', function (e) {
            var d = e.params.data;
            var info = [];
            if (d.merk_model)     info.push('<strong>' + escHtml(d.merk_model) + '</strong>');
            if (d.serial_number)  info.push('SN: <span class="font-monospace">' + escHtml(d.serial_number) + '</span>');
            if (d.status_name)    info.push('Status: <span class="badge badge-soft-blue">' + escHtml(d.status_name) + '</span>');
            if (info.length) {
                $('#unitInfoText').html(info.join(' &nbsp;|&nbsp; '));
                $('#unitInfoBox').removeClass('d-none');
            }
            // Fetch attached components for bundled sale
            loadUnitComponents(d.id);
        });

        $('#unit_id').on('select2:clear select2:unselect', function () {
            $('#unitInfoBox').addClass('d-none').find('#unitInfoText').html('');
            $('#bundledSection').addClass('d-none');
            $('#bundledList').html('<div class="text-muted small text-center py-2"><?= lang('Purchasing.no_components_attached') ?></div>');
        });
    }

    // ═══════════════════════════════════════════════════════
    // Select2 — Component (polymorphic)
    // ═══════════════════════════════════════════════════════
    function initComponentSelect2(type) {
        destroySelect2('#asset_id');
        $('#asset_id').select2({
            dropdownParent : $('#createModal'),
            placeholder    : '<?= lang('Purchasing.search_asset_placeholder') ?>',
            allowClear     : true,
            minimumInputLength: 0,
            ajax: {
                url      : _baseUrl + 'purchasing/asset-disposal/getEligibleComponents',
                dataType : 'json',
                delay    : 250,
                data     : function (params) { return { type: type, q: params.term || '' }; },
                processResults: function (res) {
                    if (!res.success) return { results: [] };
                    return { results: res.results };
                },
                cache: false,
            },
        });
    }

    // ═══════════════════════════════════════════════════════
    // Bundled Components — fetch + render checkboxes
    // ═══════════════════════════════════════════════════════
    function loadUnitComponents(unitId) {
        $('#bundledList').html('<div class="text-center py-2"><i class="fas fa-spinner fa-spin"></i></div>');
        $('#bundledSection').removeClass('d-none');

        $.get(_baseUrl + 'purchasing/asset-disposal/getUnitComponents/' + unitId, function (res) {
            if (!res.success || !res.components || !res.components.length) {
                $('#bundledList').html('<div class="text-muted small text-center py-2"><?= lang('Purchasing.no_components_attached') ?></div>');
                return;
            }
            var html = '';
            res.components.forEach(function (c) {
                var badgeMap = { ATTACHMENT: 'badge-soft-purple', BATTERY: 'badge-soft-orange', CHARGER: 'badge-soft-cyan' };
                var badge = badgeMap[c.type] || 'badge-soft-gray';
                html += '<div class="d-flex align-items-center py-1 border-bottom">';
                html += '  <div class="form-check">';
                html += '    <input class="form-check-input bundled-check" type="checkbox" value="' + c.id + '" data-type="' + c.type + '" id="bcomp_' + c.type + '_' + c.id + '">';
                html += '    <label class="form-check-label" for="bcomp_' + c.type + '_' + c.id + '">';
                html += '      <span class="badge ' + badge + ' me-1">' + c.type + '</span>' + escHtml(c.label);
                html += (c.serial ? ' <small class="text-muted font-monospace">SN: ' + escHtml(c.serial) + '</small>' : '');
                html += '    </label>';
                html += '  </div>';
                html += '</div>';
            });
            $('#bundledList').html(html);
        });
    }

    // ═══════════════════════════════════════════════════════
    // Generate Number
    // ═══════════════════════════════════════════════════════
    $('#btnGenNumber').on('click', function () {
        var btn = $(this).prop('disabled', true);
        $.get(_baseUrl + 'purchasing/asset-disposal/generateNumber', function (res) {
            if (res.success) $('#no_dokumen').val(res.no_dokumen);
        }).always(function () { btn.prop('disabled', false); });
    });

    // ═══════════════════════════════════════════════════════
    // Harga Jual Formatting (create modal + retro modal)
    // ═══════════════════════════════════════════════════════
    function formatCurrencyInput(el) {
        var raw = $(el).val().replace(/[^0-9]/g, '');
        var int = parseInt(raw, 10);
        $(el).val(isNaN(int) ? '' : int.toLocaleString('id-ID'));
    }
    $('#harga_jual').on('input', function () { formatCurrencyInput(this); });
    $('#retro-harga').on('input', function () { formatCurrencyInput(this); });

    // ═══════════════════════════════════════════════════════
    // Form Submit
    // ═══════════════════════════════════════════════════════
    $('#createSaleForm').on('submit', function (e) {
        e.preventDefault();

        var form = this;
        var missing = [];
        if (!$('#no_dokumen').val().trim())        missing.push('<?= lang('Purchasing.sale_document_no') ?>');
        if (!$('#tanggal_jual').val())             missing.push('<?= lang('Purchasing.sale_date') ?>');
        if (_currentType === 'UNIT' && !$('#unit_id').val()) missing.push('<?= lang('App.unit') ?>');
        if (_currentType !== 'UNIT' && !$('#asset_id').val()) missing.push('<?= lang('Purchasing.asset_info') ?>');
        if (!$('#nama_pembeli').val().trim())      missing.push('<?= lang('Purchasing.buyer_name') ?>');
        if (!$('#harga_jual').val().trim())         missing.push('<?= lang('Purchasing.sale_price') ?>');
        if (!$('#metode_pembayaran').val())         missing.push('<?= lang('Purchasing.payment_method') ?>');
        if (missing.length) {
            var list = missing.map(function (f) { return '<li>' + f + '</li>'; }).join('');
            $('#createAlert').removeClass('d-none').addClass('alert alert-warning').html('<i class="fas fa-exclamation-triangle me-1"></i>Lengkapi field berikut:<ul class="mb-0 mt-1">' + list + '</ul>');
            return;
        }

        var btn = $('#btnSubmitSale').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>');
        $('#createAlert').addClass('d-none').html('').removeClass('alert alert-danger alert-warning');

        var hargaRaw = $('#harga_jual').val().replace(/\./g, '').replace(/,/g, '.');
        var postData = {
            [window.csrfTokenName]: window.csrfTokenValue,
            asset_type        : _currentType,
            no_dokumen        : $('#no_dokumen').val(),
            tanggal_jual      : $('#tanggal_jual').val(),
            nama_pembeli      : $('#nama_pembeli').val(),
            alamat_pembeli    : $('#alamat_pembeli').val(),
            telepon_pembeli   : $('#telepon_pembeli').val(),
            harga_jual        : hargaRaw,
            metode_pembayaran : $('#metode_pembayaran').val(),
            no_kwitansi       : $('#no_kwitansi').val(),
            no_bast           : $('#no_bast').val(),
            no_invoice        : $('#no_invoice').val(),
            keterangan        : $('#keterangan').val(),
        };

        if (_currentType === 'UNIT') {
            postData.unit_id = $('#unit_id').val();

            // Collect bundled components (price = 0, harga keseluruhan di field utama)
            var bundled = [];
            $('.bundled-check:checked').each(function () {
                bundled.push({
                    type  : $(this).data('type'),
                    id    : parseInt($(this).val(), 10),
                    price : 0,
                });
            });
            if (bundled.length) {
                postData.bundled_components = JSON.stringify(bundled);
            }
        } else {
            postData.asset_id = $('#asset_id').val();
        }

        $.ajax({
            url      : _baseUrl + 'purchasing/asset-disposal/store',
            type     : 'POST',
            data     : postData,
            dataType : 'json',
            success  : function (res) {
                if (res.success) {
                    window.location.href = res.detail_url;
                } else {
                    var msg = res.message || '<?= lang('App.error_occurred') ?>';
                    if (res.errors) {
                        var errList = Object.values(res.errors).map(function (er) { return '<li>' + er + '</li>'; }).join('');
                        msg += '<ul class="mb-0 mt-1">' + errList + '</ul>';
                    }
                    $('#createAlert').removeClass('d-none').addClass('alert alert-danger').html(msg);
                    btn.prop('disabled', false).html(_saveSaleLabel);
                }
            },
            error: function () {
                $('#createAlert').removeClass('d-none').addClass('alert alert-danger').html('<?= lang('App.error_occurred') ?>');
                btn.prop('disabled', false).html(_saveSaleLabel);
            },
        });
    });

    // ═══════════════════════════════════════════════════════
    // Modal lifecycle
    // ═══════════════════════════════════════════════════════
    $('#createModal').on('hidden.bs.modal', function () {
        var form = document.getElementById('createSaleForm');
        form.reset();
        destroySelect2('#unit_id');
        destroySelect2('#asset_id');
        $('#unitInfoBox').addClass('d-none').find('#unitInfoText').html('');
        $('#bundledSection').addClass('d-none');
        $('#bundledList').html('<div class="text-muted small text-center py-2"><?= lang('Purchasing.no_components_attached') ?></div>');
        $('#createAlert').addClass('d-none').html('').removeClass('alert alert-danger alert-success alert-warning');
        $('#btnSubmitSale').prop('disabled', false).html(_saveSaleLabel);
        // Reset to UNIT
        $('#typeUnit').prop('checked', true);
        _currentType = 'UNIT';
        toggleAssetSections();
    });

    $('#createModal').on('shown.bs.modal', function () {
        if (!$('#tanggal_jual').val()) {
            $('#tanggal_jual').val(new Date().toISOString().slice(0, 10));
        }
        // Re-init unit Select2 after modal is fully visible
        initUnitSelect2();
    });

    // ═══════════════════════════════════════════════════════
    // Helpers
    // ═══════════════════════════════════════════════════════
    function escHtml(str) {
        return $('<div>').text(str || '').html();
    }

    // ═══════════════════════════════════════════════════════
    // Unrecorded SOLD Table
    // ═══════════════════════════════════════════════════════
    var _unrecordedTable = null;
    var _unrecordedSearchTimer = null;
    var _retroRequiredMsg  = '<?= lang('Purchasing.retro_required_fields') ?>';
    var _retroSavingLabel  = '<i class="fas fa-spinner fa-spin me-1"></i><?= lang('Purchasing.retro_saving') ?>';
    var _retroSaveLabel    = '<i class="fas fa-save me-1"></i><?= lang('Purchasing.save_sale_data') ?>';
    var _retroSuccessMsg   = '<?= lang('Purchasing.retro_success') ?>';

    function initUnrecordedTable() {
        if (_unrecordedTable) return;
        _unrecordedTable = $('#unrecordedTable').DataTable({
            processing : true,
            serverSide : true,
            ajax: {
                url  : _baseUrl + 'purchasing/asset-disposal/getUnrecordedSold',
                type : 'GET',
                data : function (d) {
                    d.asset_type = $('#filterUnrecordedType').val();
                },
                error: function () {
                    if (window.OptimaNotify) OptimaNotify.error('<?= lang('App.error_occurred') ?>');
                },
            },
            columns: [
                { data: 'asset_type',    orderable: false, className: 'align-middle', width: '120px' },
                {
                    data     : 'asset_no',
                    orderable: false,
                    className: 'align-middle',
                    render   : function (data, type, row, meta) {
                        if (type !== 'display') return data;
                        var hl = window.OptimaSearch ? OptimaSearch.highlightForMeta : function(m, t) { return t; };
                        var noHtml   = hl(meta, escHtml(row.asset_no || '-'));
                        var descHtml = row.asset_desc
                            ? '<div class="text-muted small mb-1">' + escHtml(row.asset_desc) + '</div>' : '';
                        var snHtml   = row.serial_number
                            ? '<div class="text-muted small font-monospace mb-1"><i class="fas fa-barcode me-1"></i>'
                              + hl(meta, escHtml(row.serial_number)) + '</div>' : '';
                        var pillsHtml = '';
                        if (row.tahun_unit)  pillsHtml += '<span class="badge badge-soft-gray me-1"><i class="fas fa-calendar-alt me-1"></i>' + escHtml(row.tahun_unit) + '</span>';
                        if (row.lokasi_unit) pillsHtml += '<span class="badge badge-soft-cyan me-1"><i class="fas fa-map-marker-alt me-1"></i>' + escHtml(row.lokasi_unit) + '</span>';
                        return '<div class="fw-semibold mb-1">' + noHtml + '</div>'
                             + descHtml + snHtml
                             + (pillsHtml ? '<div class="mt-1">' + pillsHtml + '</div>' : '');
                    },
                },
                { data: 'actions', orderable: false, className: 'align-middle text-center', width: '160px' },
            ],
            pageLength : 25,
            language   : { url: _baseUrl + 'assets/vendor/datatables/indonesian.json' },
            drawCallback: function () {
                $('#unrecordedTable').off('click', '.btn-catat-retro').on('click', '.btn-catat-retro', function () {
                    openDisposalRetroModal(
                        $(this).data('asset-type'),
                        $(this).data('asset-id'),
                        $(this).data('asset-no')
                    );
                });
            },
        });

        // Debounced search input — sends request to server
        $('#searchUnrecorded').on('keyup', function () {
            clearTimeout(_unrecordedSearchTimer);
            var val = $(this).val();
            _unrecordedSearchTimer = setTimeout(function () {
                _unrecordedTable.search(val).draw();
            }, 400);
        });
    }

    window.refreshUnrecordedTable = function () {
        if (_unrecordedTable) {
            _unrecordedTable.ajax.reload();
        }
    };

    // Init unrecorded table when tab is shown
    document.getElementById('tab-unrecorded-btn').addEventListener('shown.bs.tab', function () {
        initUnrecordedTable();
    });

    // ═══════════════════════════════════════════════════════
    // Retroactive Sale Modal (disposal page)
    // ═══════════════════════════════════════════════════════
    function openDisposalRetroModal(assetType, assetId, assetNo) {
        $('#retro-asset-type').val(assetType);
        $('#retro-asset-id').val(assetId);
        $('#retro-asset-label').text(assetNo || (assetType + ' #' + assetId));
        // Reset fields
        $('#retro-nama-pembeli, #retro-telepon, #retro-alamat, #retro-no-kwitansi, #retro-no-bast, #retro-no-invoice, #retro-keterangan').val('');
        $('#retro-harga').val('');
        $('#retro-tanggal-jual').val(new Date().toISOString().slice(0, 10));
        $('#retro-metode').val('TRANSFER');
        new bootstrap.Modal(document.getElementById('modal-disposal-retro-sale')).show();
    }

    $('#btn-submit-disposal-retro').on('click', function () {
        var namaPembeli = $('#retro-nama-pembeli').val().trim();
        var tanggalJual = $('#retro-tanggal-jual').val();
        if (!namaPembeli || !tanggalJual) {
            if (window.OptimaNotify) OptimaNotify.error(_retroRequiredMsg);
            return;
        }

        var btn = $(this).prop('disabled', true).html(_retroSavingLabel);

        $.ajax({
            url: _baseUrl + 'purchasing/asset-disposal/storeRetroactive',
            type: 'POST',
            data: {
                [window.csrfTokenName]: window.csrfTokenValue,
                asset_type:          $('#retro-asset-type').val(),
                asset_id:            $('#retro-asset-id').val(),
                nama_pembeli:        namaPembeli,
                tanggal_jual:        tanggalJual,
                telepon_pembeli:     $('#retro-telepon').val().trim(),
                alamat_pembeli:      $('#retro-alamat').val().trim(),
                harga_jual:          $('#retro-harga').val().replace(/\./g, '').replace(/,/g, '.') || '0',
                metode_pembayaran:   $('#retro-metode').val(),
                no_kwitansi:         $('#retro-no-kwitansi').val().trim(),
                no_bast:             $('#retro-no-bast').val().trim(),
                no_invoice:          $('#retro-no-invoice').val().trim(),
                keterangan:          $('#retro-keterangan').val().trim(),
            },
            dataType: 'json',
            success: function (res) {
                btn.prop('disabled', false).html(_retroSaveLabel);
                if (res.success) {
                    if (window.OptimaNotify) OptimaNotify.success(_retroSuccessMsg + ' ' + res.no_dokumen);
                    bootstrap.Modal.getInstance(document.getElementById('modal-disposal-retro-sale'))?.hide();
                    // Reload both tables + update badge count
                    if (_unrecordedTable) _unrecordedTable.ajax.reload();
                    window.refreshTable();
                    // Update badge: decrement or remove
                    var $badge = $('#tab-unrecorded-btn .badge');
                    if ($badge.length) {
                        var count = parseInt($badge.text(), 10) - 1;
                        if (count <= 0) $badge.remove();
                        else $badge.text(count);
                    }
                } else {
                    if (window.OptimaNotify) OptimaNotify.error(res.message || 'Gagal menyimpan.');
                }
            },
            error: function (xhr) {
                btn.prop('disabled', false).html(_retroSaveLabel);
                var msg = xhr.responseJSON?.message || '<?= lang('App.error_occurred') ?>';
                if (window.OptimaNotify) OptimaNotify.error(msg);
            },
        });
    });

    // ═══════════════════════════════════════════════════════
    // Init
    // ═══════════════════════════════════════════════════════
    $(function () {
        initTable();
        // Explicitly activate the first tab so Bootstrap hides the second pane
        var salesTabEl = document.getElementById('tab-sales-btn');
        if (salesTabEl && window.bootstrap) {
            bootstrap.Tab.getOrCreateInstance(salesTabEl).show();
        }
    });

}());
</script>

<?= $this->endSection() ?>
