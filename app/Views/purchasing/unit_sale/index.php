<?= $this->extend('layouts/base') ?>

<?php
/**
 * Penjualan Unit — Daftar & Form Buat Baru
 *
 * BADGE STANDARDS:
 * - Status COMPLETED → badge-soft-green
 * - Status CANCELLED → badge-soft-red
 * - Metode CASH     → badge-soft-green
 * - Metode TRANSFER → badge-soft-blue
 * - Metode CEK      → badge-soft-cyan
 * - Metode KREDIT   → badge-soft-orange
 *
 * PERMISSION: purchasing.unit_sale.*
 */

helper('global_permission');
$can_create = canPerformAction('purchasing', 'unit_sale', 'create');
$can_delete = canPerformAction('purchasing', 'unit_sale', 'delete');
?>

<?= $this->section('title') ?><?= lang('Purchasing.unit_sale') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>"><?= lang('App.dashboard') ?></a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('/purchasing') ?>"><?= lang('Purchasing.module_name') ?></a></li>
        <li class="breadcrumb-item active"><?= lang('Purchasing.unit_sale') ?></li>
    </ol>
</nav>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-primary-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-truck stat-icon text-primary"></i>
                </div>
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
                <div class="me-3">
                    <i class="bi bi-calendar-year stat-icon text-info"></i>
                </div>
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
                <div class="me-3">
                    <i class="bi bi-calendar-month stat-icon text-warning"></i>
                </div>
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
                <div class="me-3">
                    <i class="bi bi-cash-coin stat-icon text-success"></i>
                </div>
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
                <i class="bi bi-receipt me-2 text-primary"></i><?= lang('Purchasing.unit_sale') ?>
            </h5>
            <p class="text-muted small mb-0"><?= lang('Purchasing.sale_forklift_units') ?></p>
        </div>
        <div class="d-flex gap-2">
            <?php if ($can_create): ?>
            <button type="button" class="btn btn-primary" id="btnJualUnit" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="fas fa-plus me-1"></i><?= lang('Purchasing.sell_unit') ?>
            </button>
            <?php else: ?>
            <button type="button" class="btn btn-secondary" disabled title="<?= lang('App.access_denied') ?>">
                <i class="fas fa-lock me-1"></i><?= lang('Purchasing.sell_unit') ?>
            </button>
            <?php endif; ?>
            <button type="button" class="btn btn-outline-primary" onclick="refreshTable()">
                <i class="fas fa-sync-alt me-1"></i><?= lang('Common.refresh') ?>
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card-body pb-0">
        <div class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="text" id="filterSearch" class="form-control form-control-sm" placeholder="<?= lang('Purchasing.search_sale_placeholder') ?>">
            </div>
            <div class="col-md-2">
                <select id="filterStatus" class="form-select form-select-sm">
                    <option value=""><?= lang('Purchasing.all_status') ?></option>
                    <option value="COMPLETED"><?= lang('Common.completed') ?></option>
                    <option value="CANCELLED"><?= lang('Common.cancelled') ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" id="filterDateFrom" class="form-control form-control-sm" placeholder="Dari tanggal">
            </div>
            <div class="col-md-2">
                <input type="date" id="filterDateTo" class="form-control form-control-sm" placeholder="Sampai tanggal">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-outline-secondary w-100" onclick="applyFilters()">
                    <i class="fas fa-filter me-1"></i><?= lang('Common.filter') ?>
                </button>
            </div>
        </div>
    </div>

    <div class="card-body pt-0">
        <div class="table-responsive">
            <table id="unitSaleTable" class="table table-striped table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th><?= lang('Purchasing.sale_document_no') ?></th>
                        <th><?= lang('App.unit') ?></th>
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

<!-- ─── Create Modal ──────────────────────────────────────── -->
<div class="modal fade" id="createModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="createModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">
                    <i class="bi bi-receipt me-2 text-primary"></i><?= lang('Purchasing.record_sale') ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= lang('Common.close') ?>"></button>
            </div>
            <form id="createSaleForm" novalidate>
                <div class="modal-body">
                    <!-- Nomor Dokumen -->
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
                            <div class="invalid-feedback"><?= lang('Purchasing.sale_document_no') ?> wajib diisi.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('Purchasing.sale_date') ?> <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_jual" name="tanggal_jual" required>
                            <div class="invalid-feedback"><?= lang('Purchasing.sale_date') ?> wajib diisi.</div>
                        </div>
                    </div>

                    <!-- Unit -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('App.unit') ?> <span class="text-danger">*</span></label>
                        <select class="form-select" id="unit_id" name="unit_id" required style="width:100%">
                        </select>
                        <div class="invalid-feedback"><?= lang('App.unit') ?> wajib dipilih.</div>
                        <div id="unitInfoBox" class="mt-2 p-2 rounded border bg-light d-none small">
                            <span id="unitInfoText"></span>
                        </div>
                    </div>

                    <!-- Pembeli -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('Purchasing.buyer_name') ?> <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_pembeli" name="nama_pembeli"
                                   maxlength="255" required>
                            <div class="invalid-feedback"><?= lang('Purchasing.buyer_name') ?> wajib diisi.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('Purchasing.buyer_phone') ?></label>
                            <input type="text" class="form-control" id="telepon_pembeli" name="telepon_pembeli"
                                   placeholder="08xxxxxxxxxx" maxlength="30">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('Purchasing.buyer_address') ?></label>
                        <input type="text" class="form-control" id="alamat_pembeli" name="alamat_pembeli"
                               maxlength="500">
                    </div>

                    <!-- Keuangan -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('Purchasing.sale_price') ?> <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="harga_jual" name="harga_jual"
                                   placeholder="0" required inputmode="numeric">
                            <div class="invalid-feedback"><?= lang('Purchasing.sale_price') ?> wajib diisi.</div>
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
                            <div class="invalid-feedback"><?= lang('Purchasing.payment_method') ?> wajib dipilih.</div>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('Purchasing.receipt_no') ?></label>
                            <input type="text" class="form-control" id="no_kwitansi" name="no_kwitansi"
                                   maxlength="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('Common.notes') ?></label>
                            <input type="text" class="form-control" id="keterangan" name="keterangan"
                                   maxlength="1000">
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

    var _baseUrl   = (typeof BASE_URL !== 'undefined') ? BASE_URL : '<?= base_url() ?>';
    var _saleTable    = null;
    var _saveSaleLabel = '<i class="fas fa-save me-1"></i><?= lang('Purchasing.save_sale') ?>';

    // ── DataTable ──────────────────────────────────────────
    function initTable() {
        _saleTable = $('#unitSaleTable').DataTable({
            processing : true,
            serverSide : false,
            ajax       : {
                url  : _baseUrl + 'purchasing/unit-sale/getSalesData',
                type : 'GET',
                data : function (d) {
                    d.status    = $('#filterStatus').val();
                    d.date_from = $('#filterDateFrom').val();
                    d.date_to   = $('#filterDateTo').val();
                },
            },
            columns: [
                { data: 'no_dokumen',   className: 'align-middle' },
                { data: 'unit',         className: 'align-middle' },
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
            order  : [[2, 'desc']],
            pageLength : 25,
        });
    }

    // ── Filters ────────────────────────────────────────────
    window.applyFilters = function () {
        if (_saleTable) { _saleTable.ajax.reload(); }
    };

    window.refreshTable = function () {
        if (_saleTable) { _saleTable.ajax.reload(); }
    };

    $('#filterSearch').on('keyup', function () {
        if (_saleTable) { _saleTable.search($(this).val()).draw(); }
    });

    // ── Select2 — Unit ─────────────────────────────────────
    function initUnitSelect2() {
        $('#unit_id').select2({
            dropdownParent : $('#createModal'),
            placeholder    : '<?= lang('Purchasing.search_unit_placeholder') ?>',
            allowClear     : true,
            minimumInputLength: 0,
            ajax: {
                url      : _baseUrl + 'purchasing/unit-sale/getEligibleUnits',
                dataType : 'json',
                delay    : 250,
                data     : function (params) { return { q: params.term || '' }; },
                processResults: function (res) {
                    if (!res.success) { return { results: [] }; }
                    return { results: res.results };
                },
                cache: true,
            },
        });

        $('#unit_id').on('select2:select', function (e) {
            var d = e.params.data;
            var info = [];
            if (d.merk_model) { info.push('<strong>' + $('<div>').text(d.merk_model).html() + '</strong>'); }
            if (d.serial_number) { info.push('SN: <span class="font-monospace">' + $('<div>').text(d.serial_number).html() + '</span>'); }
            if (d.status_name) { info.push('Status: <span class="badge badge-soft-blue">' + $('<div>').text(d.status_name).html() + '</span>'); }
            if (info.length) {
                $('#unitInfoText').html(info.join(' &nbsp;|&nbsp; '));
                $('#unitInfoBox').removeClass('d-none');
            }
        });

        $('#unit_id').on('select2:clear select2:unselect', function () {
            $('#unitInfoBox').addClass('d-none');
            $('#unitInfoText').html('');
        });
    }

    // ── Generate Number ────────────────────────────────────
    $('#btnGenNumber').on('click', function () {
        var btn = $(this).prop('disabled', true);
        $.get(_baseUrl + 'purchasing/unit-sale/generateNumber', function (res) {
            if (res.success) { $('#no_dokumen').val(res.no_dokumen); }
        }).always(function () { btn.prop('disabled', false); });
    });

    // ── Harga Jual Formatting ──────────────────────────────
    $('#harga_jual').on('input', function () {
        var raw  = $(this).val().replace(/[^0-9]/g, '');
        var int  = parseInt(raw, 10);
        $(this).val(isNaN(int) ? '' : int.toLocaleString('id-ID'));
    });

    // ── Form Submit ────────────────────────────────────────
    $('#createSaleForm').on('submit', function (e) {
        e.preventDefault();

        var form = this;
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        var btn      = $('#btnSubmitSale').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>');
        $('#createAlert').addClass('d-none').html('');

        var hargaRaw = $('#harga_jual').val().replace(/\./g, '').replace(/,/g, '.');

        $.ajax({
            url     : _baseUrl + 'purchasing/unit-sale/store',
            type    : 'POST',
            data    : {
                [window.csrfTokenName]: window.csrfTokenValue,
                no_dokumen        : $('#no_dokumen').val(),
                unit_id           : $('#unit_id').val(),
                tanggal_jual      : $('#tanggal_jual').val(),
                nama_pembeli      : $('#nama_pembeli').val(),
                alamat_pembeli    : $('#alamat_pembeli').val(),
                telepon_pembeli   : $('#telepon_pembeli').val(),
                harga_jual        : hargaRaw,
                metode_pembayaran : $('#metode_pembayaran').val(),
                no_kwitansi       : $('#no_kwitansi').val(),
                keterangan        : $('#keterangan').val(),
            },
            dataType: 'json',
            success : function (res) {
                if (res.success) {
                    // Redirect to detail page
                    window.location.href = res.detail_url;
                } else {
                    var msg = res.message || '<?= lang('App.error_occurred') ?>';
                    if (res.errors) {
                        var errList = Object.values(res.errors).map(function (e) { return '<li>' + e + '</li>'; }).join('');
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

    // ── Modal reset ────────────────────────────────────────
    $('#createModal').on('hidden.bs.modal', function () {
        var form = document.getElementById('createSaleForm');
        form.reset();
        form.classList.remove('was-validated');
        $('#unit_id').val(null).trigger('change');
        $('#unitInfoBox').addClass('d-none').find('#unitInfoText').html('');
        $('#createAlert').addClass('d-none').html('').removeClass('alert alert-danger alert-success');
        $('#btnSubmitSale').prop('disabled', false).html(_saveSaleLabel);
    });

    // Set default tanggal_jual = hari ini
    $('#createModal').on('show.bs.modal', function () {
        if (!$('#tanggal_jual').val()) {
            $('#tanggal_jual').val(new Date().toISOString().slice(0, 10));
        }
    });

    // ── Init ───────────────────────────────────────────────
    $(function () {
        initTable();
        initUnitSelect2();
    });

}());
</script>

<?= $this->endSection() ?>
