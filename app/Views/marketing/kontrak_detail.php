<?= $this->extend('layouts/base') ?>

<?php
/**
 * Contract Detail (Kontrak Detail) Module - Marketing
 * BADGE SYSTEM: Optima badge-soft-* (optima-pro.css). ACTIVE→green, EXPIRED→red, PENDING→yellow, CANCELLED→gray.
 */
$contract  = $contract ?? [];
$id        = $contract['id'] ?? 0;
$noKontrak = $contract['no_kontrak'] ?? 'Contract #' . $id;
$status    = $contract['status'] ?? '';
$statusMap = ['ACTIVE' => 'badge-soft-green', 'EXPIRED' => 'badge-soft-red', 'PENDING' => 'badge-soft-yellow', 'CANCELLED' => 'badge-soft-gray'];
$statusClass = $statusMap[$status] ?? 'badge-soft-gray';
$tarikRetrievalDi = $tarikRetrievalDi ?? null;
?>

<?= $this->section('content') ?>

<?php
$canRenew  = in_array($status, ['ACTIVE', 'EXPIRED']);
$canAmend  = ($status === 'ACTIVE');
?>

<!-- Page Header -->
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('marketing/kontrak') ?>"><i class="fas fa-file-contract me-1"></i>Kontrak</a></li>
                <li class="breadcrumb-item active"><?= esc($noKontrak) ?></li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-file-contract me-2 text-primary"></i>Contract Detail
        </h4>
        <p class="text-muted small mb-0"><?= esc($noKontrak) ?> &bull; <span class="badge <?= $statusClass ?>"><?= esc($status) ?></span></p>
    </div>
    <!-- Action Buttons -->
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= base_url('marketing/kontrak') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Kembali
        </a>
        <a href="<?= base_url('marketing/rental/edit/' . $id) ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-edit me-1" aria-hidden="true"></i>Edit
        </a>
        <?php if ($canRenew): ?>
        <button type="button" class="btn btn-success btn-sm" id="btnRenewal" onclick="openRenewalWizard(<?= $id ?>)">
            <i class="fas fa-sync-alt me-1" aria-hidden="true"></i>Renewal
        </button>
        <?php endif; ?>
        <?php if ($canAmend): ?>
        <button type="button" class="btn btn-warning btn-sm" id="btnAmendment" onclick="openAmendmentModal(<?= $id ?>)">
            <i class="fas fa-calculator me-1" aria-hidden="true"></i>Change Rate
        </button>
        <?php endif; ?>
        <?php if (in_array($status, ['ACTIVE', 'EXPIRED'])): ?>
            <?php if (!empty($tarikRetrievalDi)): ?>
            <span class="d-inline-flex align-items-center gap-2 flex-wrap">
                <span class="badge badge-soft-green"><i class="fas fa-check me-1"></i><?= lang('Marketing.di_retrieval_already_created') ?></span>
                <span class="text-muted small"><?= esc($tarikRetrievalDi['nomor_di'] ?? '') ?></span>
                <a href="<?= base_url('marketing/di') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-external-link-alt me-1"></i><?= lang('Marketing.di_retrieval_view_open') ?>
                </a>
            </span>
            <?php else: ?>
            <button type="button" class="btn btn-outline-danger btn-sm" id="btnCreateDITarik" onclick="openTarikModal()">
                <i class="fas fa-truck-loading me-1" aria-hidden="true"></i><?= lang('Marketing.create_di_retrieval') ?>
            </button>
            <?php endif; ?>
        <?php endif; ?>
        <button type="button" class="btn btn-danger btn-sm" onclick="deleteContract(<?= $id ?>)">
            <i class="fas fa-trash me-1" aria-hidden="true"></i>Delete
        </button>
    </div>
</div>

<?php if ($status === 'EXPIRED'): ?>
<div class="alert <?= !empty($tarikRetrievalDi) ? 'alert-success' : 'alert-warning' ?> d-flex align-items-center flex-wrap gap-2 mb-3" role="alert">
    <i class="fas <?= !empty($tarikRetrievalDi) ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> me-2 fa-lg"></i>
    <div class="flex-grow-1">
        <?php if (!empty($tarikRetrievalDi)): ?>
            <strong><?= lang('Marketing.contract_expired_di_created') ?></strong>
            <span class="text-muted ms-1"><?= esc($tarikRetrievalDi['nomor_di'] ?? '') ?></span>
        <?php else: ?>
            <strong><?= lang('Marketing.contract_expired_no_di_alert') ?></strong>
        <?php endif; ?>
    </div>
    <?php if (!empty($tarikRetrievalDi)): ?>
        <a href="<?= base_url('marketing/di') ?>" class="btn btn-success btn-sm">
            <i class="fas fa-external-link-alt me-1"></i><?= lang('Marketing.di_retrieval_view_open') ?>
        </a>
    <?php else: ?>
        <button type="button" class="btn btn-warning btn-sm" onclick="openTarikModal()">
            <i class="fas fa-truck-loading me-1"></i><?= lang('Marketing.contract_expired_create_di') ?>
        </button>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Main Content (Left) -->
    <div class="col-lg-9">

        <!-- Tabs -->
        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex align-items-center">
                <i class="fas fa-file-contract me-2"></i>
                <strong><?= esc($noKontrak) ?></strong>
                <span class="ms-auto badge <?= $statusClass ?>"><?= esc($status) ?></span>
            </div>
            <div class="card-body">

                <ul class="nav nav-tabs nav-fill mb-3" id="detailTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-overview" data-bs-toggle="tab"
                                data-bs-target="#pane-overview" type="button" role="tab">
                            <i class="fas fa-info-circle me-1"></i>Overview
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-units" data-bs-toggle="tab"
                                data-bs-target="#pane-units" type="button" role="tab">
                            <i class="fas fa-truck me-1"></i>Units & Locations
                            <span class="badge badge-soft-blue ms-1" id="totalUnitsCount">–</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-history" data-bs-toggle="tab"
                                data-bs-target="#pane-history" type="button" role="tab">
                            <i class="fas fa-history me-1"></i>History
                        </button>
                    </li>

                </ul>

                <div class="tab-content">

                    <!-- ── Overview ── -->
                    <div class="tab-pane fade show active" id="pane-overview" role="tabpanel">
                        <!-- Contract Info -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-file-contract me-2"></i><strong>Contract Information</strong></h6>
                            </div>
                            <div class="card-body" id="contractInfoContent">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i><p>Loading...</p>
                                </div>
                            </div>
                        </div>
                        <!-- Customer + Financial row -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-building me-2"></i><strong>Customer Information</strong></h6>
                                    </div>
                                    <div class="card-body" id="customerInfoContent">
                                        <div class="text-center text-muted py-3">Loading...</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-calculator me-2"></i><strong>Financial Summary</strong></h6>
                                    </div>
                                    <div class="card-body" id="financialSummaryContent">
                                        <div class="text-center text-muted py-3">Loading...</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php $rentalType = $contract['rental_type'] ?? 'CONTRACT'; ?>

                    <?php if ($rentalType === 'PO_ONLY'): ?>
                    <!-- PO Bulanan: Payment Due & PO Info -->
                    <div class="card mt-3 border-cyan">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-file-invoice me-2 text-info"></i>
                                <strong><?= lang('Marketing.po_history') ?></strong>
                                <span class="badge badge-soft-cyan ms-2"><?= lang('Marketing.rental_type_po') ?></span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="text-muted small d-block"><?= lang('Marketing.payment_due_day') ?></label>
                                    <?php $dueDay = $contract['payment_due_day'] ?? null; ?>
                                    <?php if ($dueDay): ?>
                                        <h4 class="mb-0 text-primary">
                                            <?= esc($dueDay) ?> <small class="text-muted fs-6"><?= lang('Marketing.payment_due_day_help') ?></small>
                                        </h4>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small d-block"><?= lang('Marketing.next_payment_due') ?></label>
                                    <?php
                                    if ($dueDay && !empty($contract['tanggal_mulai'])) {
                                        $today   = new \DateTime();
                                        $nextDue = new \DateTime(date('Y-m-') . str_pad($dueDay, 2, '0', STR_PAD_LEFT));
                                        if ($nextDue <= $today) {
                                            $nextDue->modify('+1 month');
                                        }
                                        echo '<span class="fw-semibold text-warning">' . $nextDue->format('d M Y') . '</span>';
                                    } else {
                                        echo '<span class="text-muted">—</span>';
                                    }
                                    ?>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small d-block"><?= lang('Marketing.end_date_optional') ?></label>
                                    <span class="badge badge-soft-cyan"><i class="fas fa-infinity me-1"></i><?= lang('Marketing.open_ended') ?></span>
                                    <small class="d-block text-muted mt-1"><?= lang('Marketing.open_ended_notice') ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($rentalType === 'DAILY_SPOT'): ?>
                    <!-- Harian: Spot Rental Details -->
                    <div class="card mt-3 border-warning">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-calendar-day me-2 text-warning"></i>
                                <strong><?= lang('Marketing.spot_rental_number') ?></strong>
                                <span class="badge badge-soft-yellow ms-2"><?= lang('Marketing.rental_type_harian') ?></span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="text-muted small d-block"><?= lang('Marketing.spot_rental_number') ?></label>
                                    <span class="badge badge-soft-blue font-monospace">
                                        <?= esc($contract['spot_rental_number'] ?? '—') ?>
                                    </span>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted small d-block"><?= lang('Marketing.estimated_duration_days') ?></label>
                                    <strong><?= esc($contract['estimated_duration_days'] ?? '—') ?> days</strong>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted small d-block"><?= lang('Marketing.actual_return_date') ?></label>
                                    <?php $retDate = $contract['actual_return_date'] ?? null; ?>
                                    <?php if ($retDate && date('Y', strtotime($retDate)) > 1): ?>
                                        <span class="fw-semibold text-success"><?= date('d M Y', strtotime($retDate)) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted small d-block"><?= lang('Marketing.fast_track') ?></label>
                                    <?php if (!empty($contract['fast_track'])): ?>
                                        <span class="badge badge-soft-orange"><i class="fas fa-bolt me-1"></i><?= lang('Marketing.fast_track') ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php
                            $durEst = (int)($contract['estimated_duration_days'] ?? 0);
                            $startD = $contract['tanggal_mulai'] ?? null;
                            if ($durEst > 0 && $startD) {
                                $actualDays = $retDate && date('Y', strtotime($retDate)) > 1
                                    ? (int)ceil((strtotime($retDate) - strtotime($startD)) / 86400)
                                    : $durEst;
                                if ($actualDays > $durEst): ?>
                            <div class="alert alert-warning mt-3 mb-0 small">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Actual duration (<?= $actualDays ?> days) exceeds estimated (<?= $durEst ?> days).
                            </div>
                            <?php   elseif ($actualDays > 0):  ?>
                            <div class="alert alert-info mt-3 mb-0 small">
                                <i class="fas fa-info-circle me-1"></i>
                                Duration: <?= $actualDays ?> / <?= $durEst ?> days
                                <?= str_replace('{days}', '30', lang('Marketing.max_duration_notice')) ?>
                            </div>
                            <?php   endif;
                            }
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    </div><!-- /pane-overview -->
                    <div class="tab-pane fade" id="pane-units" role="tabpanel">
                        <div class="card">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-truck me-2"></i><strong>Rented Units by Location</strong></h6>
                                <button class="btn btn-sm btn-primary" onclick="openAddUnitModal(<?= $id ?>, '<?= esc($noKontrak) ?>', '<?= $contract['tanggal_mulai'] ?? '' ?>', '<?= $contract['tanggal_berakhir'] ?? '' ?>')">
                                    <i class="fas fa-plus me-1"></i>Tambah Unit
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div class="accordion" id="locationsAccordion">
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-spinner fa-spin fa-2x mb-2"></i><p>Loading units...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── History ── -->
                    <div class="tab-pane fade" id="pane-history" role="tabpanel">
                        <div class="card mb-3">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-clock me-2"></i><strong>Contract Timeline</strong></h6>
                                <select class="form-select form-select-sm" style="width:auto" id="historyFilter">
                                    <option value="all">All Events</option>
                                    <option value="contract">Contracts</option>
                                    <option value="amendment">Amendments</option>
                                    <option value="renewal">Renewals</option>
                                </select>
                            </div>
                            <div class="card-body" id="contractTimelineContent">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i><p>Loading history...</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i><strong>Rate Changes</strong></h6>
                            </div>
                            <div class="card-body" id="rateHistoryContent">
                                <div class="text-center text-muted py-3">Loading rate history...</div>
                            </div>
                        </div>
                    </div>



                </div><!-- /tab-content -->
            </div><!-- /card-body -->
        </div><!-- /card -->

    </div><!-- /col-lg-9 -->

    <!-- Sidebar (Right) -->
    <div class="col-lg-3">

        <!-- Quick Info Card -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Contract Info</h6>
            </div>
            <div class="card-body p-3">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">ID</dt>
                    <dd class="col-7">#<?= esc($id) ?></dd>

                    <dt class="col-5 text-muted">Status</dt>
                    <dd class="col-7">
                        <span class="badge <?= $statusClass ?>"><?= esc($status) ?></span>
                    </dd>

                    <dt class="col-5 text-muted">Type</dt>
                    <dd class="col-7"><?= esc($contract['rental_type'] ?? '—') ?></dd>

                    <dt class="col-5 text-muted">Billing</dt>
                    <dd class="col-7"><?= esc($contract['jenis_sewa'] ?? '—') ?></dd>

                    <dt class="col-5 text-muted">Start</dt>
                    <dd class="col-7">
                        <?= !empty($contract['tanggal_mulai']) ? date('d M Y', strtotime($contract['tanggal_mulai'])) : '—' ?>
                    </dd>

                    <dt class="col-5 text-muted">End</dt>
                    <dd class="col-7">
                        <?php
                        $endDate = $contract['tanggal_berakhir'] ?? null;
                        echo ($endDate && date('Y', strtotime($endDate)) > 1)
                            ? date('d M Y', strtotime($endDate))
                            : '<em class="text-muted">Open-ended</em>';
                        ?>
                    </dd>

                    <dt class="col-5 text-muted">Created</dt>
                    <dd class="col-7">
                        <?= !empty($contract['created_at']) ? date('d M Y', strtotime($contract['created_at'])) : '—' ?>
                    </dd>
                </dl>
            </div>
        </div>

        <!-- Status note -->
        <div class="alert alert-info small">
            <i class="fas fa-info-circle me-1"></i>
            Changes to Status will automatically update the inventory unit allocation status.
        </div>

    </div><!-- /col-lg-3 -->
</div><!-- /row -->

<!-- Include modals used by action buttons -->
<?= $this->include('components/renewal_wizard') ?>
<?= $this->include('components/addendum_prorate') ?>
<?= $this->include('components/asset_history') ?>
<?= $this->include('components/add_unit_modal') ?>

<?php if (in_array($status, ['ACTIVE', 'EXPIRED']) && empty($tarikRetrievalDi)): ?>
<?php $diRetrievalLang = [
    'modal_title' => lang('Marketing.di_retrieval_modal_title'),
    'alert' => str_replace('{contract}', esc($noKontrak), lang('Marketing.di_retrieval_alert')),
    'command_type' => lang('Marketing.di_retrieval_command_type'),
    'purpose' => lang('Marketing.di_retrieval_purpose'),
    'select_purpose' => lang('Marketing.di_retrieval_select_purpose'),
    'delivery_date' => lang('Marketing.di_retrieval_delivery_date'),
    'location' => lang('Marketing.di_retrieval_location'),
    'notes' => lang('Marketing.di_retrieval_notes'),
    'notes_placeholder' => lang('Marketing.di_retrieval_notes_placeholder'),
    'select_units' => lang('Marketing.di_retrieval_select_units'),
    'select_all' => lang('Marketing.di_retrieval_select_all'),
    'clear_all' => lang('Marketing.di_retrieval_clear_all'),
    'units_selected' => lang('Marketing.di_retrieval_units_selected'),
    'no_units' => lang('Marketing.di_retrieval_no_units'),
    'submit' => lang('Marketing.di_retrieval_submit'),
    'cancel' => lang('Marketing.di_retrieval_cancel'),
    'processing' => lang('Marketing.di_retrieval_processing'),
    'success' => lang('Marketing.di_retrieval_success'),
    'error' => lang('Marketing.di_retrieval_error'),
    'network_error' => lang('Marketing.di_retrieval_network_error'),
    'load_error' => lang('Marketing.di_retrieval_load_error'),
    'min_unit' => lang('Marketing.di_retrieval_min_unit'),
]; ?>
<!-- DI Penarikan (TARIK) Modal — selaras Marketing > DI: jenis/tujuan + badge, deskripsi tujuan -->
<div class="modal fade" id="tarikDIModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-truck-loading me-2"></i><?= $diRetrievalLang['modal_title'] ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning small mb-3">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <?= $diRetrievalLang['alert'] ?>
                </div>
                <form id="tarikDIForm">
                    <input type="hidden" name="kontrak_id" value="<?= $id ?>">
                    <input type="hidden" name="po_kontrak_nomor" value="<?= esc($contract['no_kontrak'] ?? $contract['customer_po_number'] ?? '') ?>">
                    <input type="hidden" name="pelanggan" id="tarikPelanggan" value="">
                    <input type="hidden" name="pelanggan_id" id="tarikPelangganId" value="">
                    <input type="hidden" name="jenis_perintah_kerja_id" id="tarikJenisId" value="">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= $diRetrievalLang['command_type'] ?></label>
                            <div class="form-control d-flex align-items-center gap-2 flex-wrap" style="min-height:38px;">
                                <span class="badge badge-soft-orange">TARIK</span>
                                <span class="text-body">Tarik Unit</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= $diRetrievalLang['purpose'] ?> <span class="text-danger">*</span></label>
                            <select class="form-select" name="tujuan_perintah_kerja_id" id="tarikTujuanSelect" required>
                                <option value=""><?= $diRetrievalLang['select_purpose'] ?></option>
                            </select>
                            <div id="tarikHelpTujuanPerintah" class="di-workflow-help form-text small border-start border-3 border-secondary ps-2 mt-1 text-muted"></div>
                        </div>
                    </div>

                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body py-3 px-3">
                            <div class="small text-uppercase text-muted fw-semibold mb-2"><?= lang('Marketing.contract_po') ?></div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small mb-0 text-muted"><?= lang('Marketing.customer') ?></label>
                                    <input type="text" class="form-control form-control-sm" id="tarikCustomerDisplay" readonly tabindex="-1" value="<?= esc($contract['customer_name'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small mb-0 text-muted"><?= lang('Marketing.contract_po') ?></label>
                                    <input type="text" class="form-control form-control-sm" readonly tabindex="-1" value="<?= esc($contract['no_kontrak'] ?? $contract['customer_po_number'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="customer_location_id" id="tarikCustomerLocationId" value="">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= $diRetrievalLang['delivery_date'] ?></label>
                            <input type="date" class="form-control" name="tanggal_kirim" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('App.customer_location') ?></label>
                            <input type="text" class="form-control bg-light" name="lokasi" id="tarikLokasi" value="" readonly tabindex="-1">
                            <small class="text-muted">Lokasi operasional dari kontrak ini; ID lokasi customer dipetakan otomatis untuk DI.</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= $diRetrievalLang['notes'] ?></label>
                        <textarea class="form-control" name="catatan" rows="2" placeholder="<?= $diRetrievalLang['notes_placeholder'] ?>"></textarea>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0"><i class="fas fa-boxes me-1"></i><?= $diRetrievalLang['select_units'] ?></h6>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="tarikSelectAll"><?= $diRetrievalLang['select_all'] ?></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="tarikClearAll"><?= $diRetrievalLang['clear_all'] ?></button>
                            <span class="badge bg-primary ms-2" id="tarikSelectedCount">0</span> <?= $diRetrievalLang['units_selected'] ?>
                        </div>
                    </div>
                    <div id="tarikUnitsList" class="border rounded p-2" style="max-height:300px;overflow-y:auto">
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-spinner fa-spin me-1"></i><?= lang('Common.loading') ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= $diRetrievalLang['cancel'] ?></button>
                <button type="button" class="btn btn-danger" id="btnSubmitTarikDI" disabled>
                    <i class="fas fa-truck-loading me-1"></i><?= $diRetrievalLang['submit'] ?>
                </button>
            </div>
        </div>
    </div>
</div>
<style>
  #tarikDIModal .select2-container--default .select2-results__option .di-workflow-opt {
    display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; padding: 2px 0;
  }
  #tarikDIModal .select2-container--default .select2-selection--single { min-height: 38px; border-radius: 0.375rem; }
  #tarikDIModal .select2-container--default .select2-selection--single .select2-selection__rendered {
    padding-top: 4px; padding-bottom: 4px; line-height: 1.35;
  }
  #tarikDIModal .select2-container--default .select2-selection--single .di-workflow-opt .badge {
    font-size: 0.7rem; font-weight: 600; letter-spacing: 0.02em;
  }
  #tarikDIModal .di-workflow-help { min-height: 0; white-space: pre-line; line-height: 1.4; }
</style>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="<?= base_url('assets/js/renewal-wizard.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/renewal-wizard.js') ?>"></script>
<script src="<?= base_url('assets/js/addendum-prorate.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/addendum-prorate.js') ?>"></script>
<script>
const CONTRACT_ID = <?= (int)$id ?>;
const LANG_TARIK = <?= json_encode(
    (in_array($status, ['ACTIVE', 'EXPIRED']) && isset($diRetrievalLang))
        ? $diRetrievalLang
        : (object)[]
) ?>;
// BASE_URL is already defined globally in base.php

// ── Helper: format rupiah ────────────────────────────────
function rupiah(v) {
    return 'Rp ' + parseFloat(v || 0).toLocaleString('id-ID');
}

// ── Load Overview ────────────────────────────────────────
function loadOverview() {
    $.ajax({
        url: BASE_URL + 'marketing/rental/get/' + CONTRACT_ID,
        type: 'GET',
        success: function(res) {
            if (!res.success || !res.data) {
                $('#contractInfoContent').html('<div class="alert alert-warning">Contract details not found</div>');
                return;
            }
            const c = res.data;

            // Contract Info grid
            const fields = [
                ['Contract Number', c.no_kontrak],
                ['Contract Type',   c.rental_type],
                ['Status',          '<span class="badge ' + (c.status === 'ACTIVE' ? 'badge-soft-green' : c.status === 'EXPIRED' ? 'badge-soft-red' : 'badge-soft-yellow') + '">' + c.status + '</span>'],
                ['PO Number',       c.po_number],
                ['Start Date',      c.tanggal_mulai],
                ['End Date',        c.tanggal_berakhir || '<em class="text-muted">Open-ended</em>'],
                ['Billing Type',    c.jenis_sewa],
                ['Billing Method',  c.billing_method],
                ['Notes',           c.keterangan || '—'],
            ];

            let html = '<div class="row">';
            fields.forEach(([label, val]) => {
                html += `<div class="col-md-4 mb-3"><label class="text-muted small">${label}</label><p class="mb-0">${val || '—'}</p></div>`;
            });
            html += '</div>';
            $('#contractInfoContent').html(html);

            // Customer Info
            let cust = `<p class="mb-2"><strong>Customer:</strong><br>${c.customer_name || '—'}</p>`;
            cust    += `<p class="mb-2"><strong>Code:</strong><br>${c.customer_code || '—'}</p>`;
            cust    += `<p class="mb-0"><strong>Contact:</strong><br>${c.contact_person || '—'}`;
            if (c.phone) cust += `<br><i class="fas fa-phone me-1"></i>${c.phone}`;
            cust    += '</p>';
            $('#customerInfoContent').html(cust);

            // Financial Summary
            let fin = `<div class="row text-center">
                <div class="col-6 mb-3">
                    <label class="text-muted small d-block">Total Units</label>
                    <h3 class="mb-0 text-primary">${c.total_units || 0}</h3>
                </div>
                <div class="col-6 mb-3">
                    <label class="text-muted small d-block">Contract Value</label>
                    <h5 class="mb-0 text-success">${rupiah(c.total_value)}</h5>
                </div>`;
            if (c.operator_quantity > 0) {
                fin += `<div class="col-12"><hr class="my-2">
                    <p class="text-muted mb-0"><i class="fas fa-user-tie me-1"></i>Includes ${c.operator_quantity} operator(s)</p>`;
                if (c.operator_monthly_rate) fin += `<small class="text-muted">@ ${rupiah(c.operator_monthly_rate)}/month</small>`;
                fin += '</div>';
            }
            fin += '</div>';
            $('#financialSummaryContent').html(fin);
        },
        error: function() {
            $('#contractInfoContent').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error loading contract details</div>');
        }
    });
}

// ── Load Units ──────────────────────────────────────────
function loadUnits() {
    $('#locationsAccordion').html('<div class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin fa-2x mb-2"></i><p>Loading units...</p></div>');
    $.ajax({
        url: BASE_URL + 'marketing/rental/units/' + CONTRACT_ID,
        type: 'GET',
        success: function(res) {
            if (!res.success || !res.data || !res.data.length) {
                $('#locationsAccordion').html('<div class="alert alert-info m-3"><i class="fas fa-info-circle me-2"></i>Belum ada unit di kontrak ini. Klik "Tambah Unit" untuk menambahkan.</div>');
                $('#totalUnitsCount').text('0');
                return;
            }
            var locations = {};
            var total = 0;
            var grandTotal = 0;
            res.data.forEach(function(u) {
                var loc = u.lokasi || u.location_name || 'Unknown Location';
                if (!locations[loc]) locations[loc] = [];
                locations[loc].push(u);
                total++;
            });

            $('#totalUnitsCount').text(total);
            var html = '';
            var idx  = 0;
            for (var loc in locations) {
                if (!locations.hasOwnProperty(loc)) continue;
                var units = locations[loc];
                var aId    = 'loc-' + idx;
                var isOpen = idx === 0;
                var locTotal = 0;
                units.forEach(function(u) {
                    var effectiveRate = u.is_spare ? 0 : parseFloat(u.harga_efektif || u.ku_harga_sewa || u.harga_sewa_bulanan || 0);
                    locTotal += effectiveRate;
                });
                grandTotal += locTotal;

                html += '<div class="accordion-item">';
                html += '<h2 class="accordion-header">';
                html += '<button class="accordion-button' + (isOpen ? '' : ' collapsed') + '" type="button" data-bs-toggle="collapse" data-bs-target="#' + aId + '">';
                html += '<i class="fas fa-map-marker-alt me-2 text-primary"></i>';
                html += '<strong>' + loc + '</strong>';
                html += '<span class="badge badge-soft-blue ms-2">' + units.length + ' unit(s)</span>';
                html += '<span class="ms-auto text-success small">' + rupiah(locTotal) + '/bln</span>';
                html += '</button></h2>';
                html += '<div id="' + aId + '" class="accordion-collapse collapse' + (isOpen ? ' show' : '') + '">';
                html += '<div class="accordion-body p-0"><div class="table-responsive">';
                html += '<table class="table table-sm table-hover mb-0">';
                html += '<thead class="bg-light"><tr>';
                html += '<th>Unit No</th><th>Type</th><th>Brand / Model</th>';
                html += '<th>Capacity</th><th class="text-end">Rate/Month</th><th class="text-center">Spare</th><th>Status</th><th width="80">Action</th>';
                html += '</tr></thead><tbody>';

                units.forEach(function(u) {
                    var brandModel = [u.merk, u.model].filter(Boolean).join(' / ') || '—';
                    var effectiveRate = u.is_spare ? 0 : parseFloat(u.harga_efektif || u.ku_harga_sewa || u.harga_sewa_bulanan || 0);
                    var rateDisplay = rupiah(effectiveRate);
                    var isCustomRate = u.ku_harga_sewa && parseFloat(u.ku_harga_sewa) > 0;

                    // Rate column with custom indicator
                    if (u.is_spare) {
                        rateDisplay = '<span class="text-muted">Rp 0</span>';
                    } else if (isCustomRate) {
                        rateDisplay += ' <small class="text-info" title="Harga khusus kontrak"><i class="fas fa-tag"></i></small>';
                    }

                    html += '<tr>';
                    html += '<td><strong>' + (u.no_unit || u.unit_no || '—') + '</strong></td>';
                    html += '<td>' + (u.jenis_unit || u.unit_type || '—') + '</td>';
                    html += '<td>' + brandModel + '</td>';
                    html += '<td>' + (u.kapasitas || u.capacity || '—') + '</td>';
                    html += '<td class="text-end">' + rateDisplay + '</td>';
                    html += '<td class="text-center">';
                    if (u.is_spare) {
                        html += '<span class="badge badge-soft-gray"><i class="fas fa-shield-alt me-1"></i>Spare</span>';
                    } else {
                        html += '-';
                    }
                    html += '</td>';
                    html += '<td><span class="badge ' + (u.status === 'TERSEDIA' ? 'badge-soft-green' : 'badge-soft-yellow') + '">' + (u.status || 'Active') + '</span></td>';
                    html += '<td>';
                    html += '<button class="btn btn-xs btn-outline-primary me-1" title="Edit Unit" onclick="openEditUnitModal(' + (u.id_inventory_unit || u.id) + ', \'' + (u.no_unit || '') + '\', ' + parseFloat(u.harga_sewa_bulanan || 0) + ', ' + (u.ku_harga_sewa ? parseFloat(u.ku_harga_sewa) : 'null') + ', ' + (u.is_spare ? 1 : 0) + ')"><i class="fas fa-pencil-alt"></i></button>';
                    html += '<button class="btn btn-xs btn-outline-danger" title="Hapus dari Kontrak" onclick="removeUnitFromContract(' + (u.id_inventory_unit || u.id) + ')"><i class="fas fa-times"></i></button>';
                    html += '</td></tr>';
                });

                // Subtotal row
                html += '<tr class="table-light">';
                html += '<td colspan="4" class="text-end"><strong>Subtotal:</strong></td>';
                html += '<td class="text-end"><strong>' + rupiah(locTotal) + '</strong></td>';
                html += '<td colspan="3"></td></tr>';
                html += '</tbody></table></div></div></div></div>';
                idx++;
            }

            // Grand total
            html += '<div class="p-3 bg-light border-top">';
            html += '<div class="d-flex justify-content-between">';
            html += '<h6 class="mb-0"><strong>Grand Total Sewa Unit:</strong></h6>';
            html += '<h6 class="mb-0 text-success"><strong>' + rupiah(grandTotal) + '/bulan</strong></h6>';
            html += '</div></div>';

            $('#locationsAccordion').html(html);
        },
        error: function() {
            $('#locationsAccordion').html('<div class="alert alert-danger m-3"><i class="fas fa-exclamation-triangle me-2"></i>Error loading units</div>');
        }
    });
}

// Edit Unit Modal
function openEditUnitModal(unitId, noUnit, hargaDefault, hargaKu, isSpare) {
    var currentHarga = hargaKu !== null ? hargaKu : '';
    var formattedHarga = currentHarga ? formatRupiahInput(currentHarga) : '';

    var htmlContent = '<div class="mb-3">' +
        '<label class="form-label fw-bold">Unit: ' + noUnit + '</label>' +
        '<p class="text-muted small mb-2">Harga default: ' + rupiah(hargaDefault) + '/bulan</p>' +
        '</div>' +
        '<div class="mb-3">' +
        '<label class="form-label">Harga Sewa Kontrak (Rp)</label>' +
        '<input type="text" class="form-control" id="editUnitHarga" value="' + formattedHarga + '" placeholder="Rp 0">' +
        '<small class="text-muted">Kosongkan untuk menggunakan harga default dari inventory</small>' +
        '</div>' +
        '<div class="form-check">' +
        '<input type="checkbox" class="form-check-input" id="editUnitSpare" ' + (isSpare ? 'checked' : '') + ' onchange="if(this.checked){document.getElementById(\'editUnitHarga\').value=\'Rp 0\';document.getElementById(\'editUnitHarga\').readOnly=true}else{document.getElementById(\'editUnitHarga\').value=\'\';document.getElementById(\'editUnitHarga\').readOnly=false}">' +
        '<label class="form-check-label" for="editUnitSpare"><i class="fas fa-shield-alt me-1"></i>Spare Unit (harga = 0)</label>' +
        '</div>';

    OptimaConfirm.generic({
        title: 'Edit Unit dalam Kontrak',
        html: htmlContent,
        icon: 'question',
        confirmText: '<i class="fas fa-save me-1"></i>Simpan',
        cancelText: window.lang('cancel'),
        confirmButtonColor: '#0d6efd',
        onConfirm: function() {
            var hargaFormatted = (document.getElementById('editUnitHarga') || {}).value || '';
            var harga = String(hargaFormatted).replace(/[^0-9]/g, ''); // Remove all non-numeric characters
            var spare = document.getElementById('editUnitSpare') ? document.getElementById('editUnitSpare').checked : false;

            $.ajax({
                url: BASE_URL + 'marketing/rental/updateUnit',
                type: 'POST',
                data: {
                    kontrak_id: CONTRACT_ID,
                    unit_id: unitId,
                    harga_sewa: harga !== '' ? parseFloat(harga) : null,
                    is_spare: spare ? 1 : 0
                },
                success: function(res) {
                    if (res.success) {
                        loadUnits();
                        if (typeof alertSwal === 'function') {
                            alertSwal('success', res.message || 'Unit berhasil diupdate');
                        }
                    } else {
                        if (typeof alertSwal === 'function') {
                            alertSwal('error', res.message || 'Gagal update unit');
                        } else if (window.OptimaNotify) {
                            OptimaNotify.error(res.message || 'Gagal update unit');
                        } else {
                            alert(res.message || 'Gagal update unit');
                        }
                    }
                },
                error: function() {
                    if (window.OptimaNotify) OptimaNotify.error('Error saat update unit');
                    else alert('Error saat update unit');
                }
            });
        }
    });

    // Apply rupiah formatting on input after modal is shown
    setTimeout(function() {
        var input = document.getElementById('editUnitHarga');
        if (!input || input.dataset.optimaRupiahBound === '1') return;
        input.dataset.optimaRupiahBound = '1';
        input.addEventListener('input', function(e) {
            var value = e.target.value.replace(/[^0-9]/g, '');
            if (value) {
                e.target.value = formatRupiahInput(value);
            }
        });
        input.addEventListener('keypress', function(e) {
            if (e.which < 48 || e.which > 57) {
                e.preventDefault();
            }
        });
    }, 100);
}

// Helper function to format rupiah for input field
function formatRupiahInput(angka) {
    var number_string = angka.toString().replace(/[^0-9]/g, '');
    var split = number_string.split(',');
    var sisa = split[0].length % 3;
    var rupiah = split[0].substr(0, sisa);
    var ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        var separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    return 'Rp ' + rupiah;
}

// Remove unit from contract
function removeUnitFromContract(unitId) {
    OptimaConfirm.danger({
        title: 'Hapus Unit dari Kontrak',
        text: 'Apakah Anda yakin ingin menghapus unit ini dari kontrak?',
        onConfirm: function() {
            $.ajax({
        url: BASE_URL + 'marketing/rental/removeUnit',
        type: 'POST',
        data: {
            kontrak_id: CONTRACT_ID,
            unit_id: unitId
        },
        success: function(res) {
            if (res.success) {
                loadUnits();
                alertSwal('success', res.message || 'Unit berhasil dihapus');
            } else {
                alertSwal('error', res.message || 'Gagal menghapus unit');
            }
        },
        error: function() {
            alertSwal('error', 'Error saat menghapus unit');
        }
    });
        }
    });
}


// ── Load History ────────────────────────────────────────
function loadHistory() {
    // Timeline
    $.ajax({
        url: BASE_URL + 'marketing/rental/getContractHistory/' + CONTRACT_ID,
        type: 'GET',
        success: function(res) {
            if (!res.success || !res.data) {
                $('#contractTimelineContent').html('<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No history data available</div>');
                return;
            }
            const iconMap = { contract: 'fa-file-contract text-primary', amendment: 'fa-edit text-warning', renewal: 'fa-sync-alt text-success' };
            let html = '<div class="timeline">';
            res.data.forEach(ev => {
                const icon = iconMap[ev.type] || 'fa-circle text-secondary';
                html += `<div class="timeline-item">
                    <div class="timeline-marker"><i class="fas ${icon}"></i></div>
                    <div class="timeline-content">
                        <div class="timeline-time">${ev.date}</div>
                        <h6>${ev.description}</h6>`;
                if (ev.reason)      html += `<p class="text-muted mb-0">${ev.reason}</p>`;
                if (ev.total_value) html += `<p class="mb-0"><strong>Value:</strong> ${rupiah(ev.total_value)}</p>`;
                html += `</div></div>`;
            });
            html += '</div>';
            $('#contractTimelineContent').html(html);
        },
        error: function() {
            $('#contractTimelineContent').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error loading history</div>');
        }
    });

    // Rate history
    $.ajax({
        url: BASE_URL + 'marketing/rental/getRateHistory/' + CONTRACT_ID,
        type: 'GET',
        success: function(res) {
            if (!res.success || !res.data || !res.data.length) {
                $('#rateHistoryContent').html('<p class="text-muted">No rate changes found</p>');
                return;
            }
            let html = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Date</th><th>Unit</th><th>Old Rate</th><th>New Rate</th><th>Change</th></tr></thead><tbody>';
            res.data.forEach(r => {
                const diff = r.new_rate - r.old_rate;
                const cls  = diff > 0 ? 'text-success' : 'text-danger';
                const ico  = diff > 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                html += `<tr><td>${r.date}</td><td>${r.unit_no}</td><td>${rupiah(r.old_rate)}</td><td>${rupiah(r.new_rate)}</td>
                    <td class="${cls}"><i class="fas ${ico} me-1"></i>${rupiah(Math.abs(diff))}</td></tr>`;
            });
            html += '</tbody></table></div>';
            $('#rateHistoryContent').html(html);
        },
        error: function() {
            $('#rateHistoryContent').html('<div class="alert alert-danger">Error loading rate history</div>');
        }
    });
}

// ── Load Documents ──────────────────────────────────────
function loadDocuments() {
    $.ajax({
        url: BASE_URL + 'marketing/rental/documents/' + CONTRACT_ID,
        type: 'GET',
        success: function(res) {
            if (!res.success || !res.data || !res.data.length) {
                $('#documentsListContent').html('<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No documents uploaded yet</div>');
                return;
            }
            const iconMap = { pdf: 'fa-file-pdf text-danger', doc: 'fa-file-word text-primary', docx: 'fa-file-word text-primary', xls: 'fa-file-excel text-success', xlsx: 'fa-file-excel text-success', jpg: 'fa-file-image text-info', jpeg: 'fa-file-image text-info', png: 'fa-file-image text-info' };
            let html = '<div class="list-group list-group-flush">';
            res.data.forEach(d => {
                const ext  = d.file_name.split('.').pop().toLowerCase();
                const icon = iconMap[ext] || 'fa-file text-secondary';
                html += `<div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas ${icon} fa-2x me-3"></i>
                        <strong>${d.file_name}</strong>
                        <br><small class="text-muted">Uploaded: ${d.uploaded_at} by ${d.uploaded_by || 'System'}</small>
                    </div>
                    <div>
                        <a href="${d.file_path}" class="btn btn-sm btn-primary me-1" download><i class="fas fa-download"></i></a>
                        <button class="btn btn-sm btn-danger" onclick="deleteDocument(${d.id})"><i class="fas fa-trash"></i></button>
                    </div>
                </div>`;
            });
            html += '</div>';
            $('#documentsListContent').html(html);
        },
        error: function() {
            $('#documentsListContent').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error loading documents</div>');
        }
    });
}

// ── Stubs for modal-based actions (reuse shared functions from kontrak.php pattern) ──

function uploadContractDocument() {
    if (window.OptimaNotify) OptimaNotify.info('Upload document feature coming soon.');
}

function deleteDocument(docId) {
    OptimaConfirm.danger({
        title: 'Hapus Dokumen',
        text: 'Apakah Anda yakin ingin menghapus dokumen ini?',
        onConfirm: function() {
            $.post(BASE_URL + 'marketing/rental/deleteDocument/' + docId, {}, function(res) {
                if (res.success) { loadDocuments(); } else { alertSwal('error', res.message || 'Gagal menghapus dokumen.'); }
            });
        }
    });
}

function deleteContract(id) {
    OptimaConfirm.danger({
        title: 'Hapus Kontrak',
        text: 'Apakah Anda yakin ingin menghapus kontrak ini? Tindakan ini tidak dapat dibatalkan.',
        onConfirm: function() {
            $.ajax({
        url: BASE_URL + 'marketing/rental/delete/' + id,
        type: 'POST',
        success: function(res) {
            if (res.success) {
                window.location.href = BASE_URL + 'marketing/kontrak';
            } else {
                alertSwal('error', res.message || 'Gagal menghapus kontrak.');
            }
        },
        error: function() { alertSwal('error', 'Terjadi kesalahan saat menghapus kontrak.'); }
    });
        }
    });
}

// ── Action functions: Renewal, Amendment, History ────────

// openRenewalWizard(id) is provided by renewal-wizard.js:
// when called with a contractId it skips Step 1 and preloads the contract.

function openAmendmentModal(id) {
    openAddendumProrateCalculator(id);
}


// ── Tab lazy-loading ────────────────────────────────────
$(document).ready(function() {
    // Explicitly ensure Overview tab is active on page load
    // Remove any active classes from other tabs
    $('.nav-link').removeClass('active');
    $('.tab-pane').removeClass('show active');
    
    // Activate Overview tab and pane
    $('#tab-overview').addClass('active');
    $('#pane-overview').addClass('show active');
    
    // Load overview immediately
    loadOverview();

    // Setup lazy loading for other tabs
    $('#tab-units').on('shown.bs.tab', function() { loadUnits(); });
    $('#tab-history').on('shown.bs.tab', function() { loadHistory(); });
});

// ── DI Penarikan (TARIK) from Kontrak Detail ────────────
<?php if (in_array($status, ['ACTIVE', 'EXPIRED'])): ?>

let tarikJenisId = null;
let tarikTujuanOptions = [];
let lastTarikTujuanList = [];
const TARIK_PH_TUJUAN = <?= json_encode('-- ' . lang('Marketing.select_command') . ' --') ?>;

function tarikEscapeHtml(s) {
    return String(s ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/"/g, '&quot;');
}
function tarikWorkflowBadgeClass(kode) {
    const k = String(kode || '').toUpperCase();
    if (k.startsWith('ANTAR')) return 'badge-soft-cyan';
    if (k.startsWith('TARIK')) return 'badge-soft-orange';
    if (k.startsWith('TUKAR')) return 'badge-soft-purple';
    if (k.startsWith('RELOKASI')) return 'badge-soft-green';
    return 'badge-soft-blue';
}
function tarikFormatTujuanSelection(data) {
    if (typeof jQuery === 'undefined' || !data.id) return data.text;
    const el = data.element;
    if (!el) return data.text;
    const kode = el.getAttribute('data-kode');
    const nama = el.getAttribute('data-nama');
    if (!kode) return data.text;
    const bc = tarikWorkflowBadgeClass(kode);
    const html = '<span class="di-workflow-opt d-flex align-items-center gap-2 flex-wrap">' +
        '<span class="badge ' + bc + '">' + tarikEscapeHtml(kode) + '</span>' +
        '<span class="text-body">' + tarikEscapeHtml(nama) + '</span></span>';
    return jQuery(html);
}
function tarikFormatTujuanResult(data) {
    if (typeof jQuery === 'undefined' || !data.id) return data.text;
    const el = data.element;
    if (!el) return data.text;
    const kode = el.getAttribute('data-kode');
    const nama = el.getAttribute('data-nama');
    const desk = (el.getAttribute('data-deskripsi') || '').trim();
    if (!kode) return data.text;
    const bc = tarikWorkflowBadgeClass(kode);
    let html = '<span class="di-workflow-opt di-workflow-opt--open">' +
        '<span class="d-flex align-items-center gap-2 flex-wrap">' +
        '<span class="badge ' + bc + '">' + tarikEscapeHtml(kode) + '</span>' +
        '<span class="text-body fw-medium">' + tarikEscapeHtml(nama) + '</span></span>';
    if (desk) {
        html += '<span class="small text-muted d-block mt-1 ps-0" style="max-width:28rem;">' +
            tarikEscapeHtml(desk).replace(/\n/g, '<br>') + '</span>';
    }
    html += '</span>';
    return jQuery(html);
}
function destroyTarikTujuanSelect2() {
    if (typeof jQuery === 'undefined') return;
    const $t = jQuery('#tarikTujuanSelect');
    if ($t.length && $t.hasClass('select2-hidden-accessible')) {
        $t.off('select2:open.tarikDiZ');
        $t.select2('destroy');
    }
}
function initTarikTujuanSelect2() {
    if (typeof jQuery === 'undefined' || !jQuery.fn.select2) {
        setTimeout(initTarikTujuanSelect2, 80);
        return;
    }
    const $el = jQuery('#tarikTujuanSelect');
    if (!$el.length) return;
    if ($el.hasClass('select2-hidden-accessible')) {
        $el.select2('destroy');
    }
    $el.select2({
        width: '100%',
        dropdownParent: jQuery('#tarikDIModal'),
        placeholder: TARIK_PH_TUJUAN,
        allowClear: false,
        templateResult: tarikFormatTujuanResult,
        templateSelection: tarikFormatTujuanSelection,
        escapeMarkup: function (markup) { return markup; }
    });
    $el.off('select2:open.tarikDiZ').on('select2:open.tarikDiZ', function () {
        jQuery('.select2-dropdown').last().css('z-index', 10060);
    });
}

function resetTarikTujuanHelp() {
    const el = document.getElementById('tarikHelpTujuanPerintah');
    if (el) el.innerHTML = '<span class="text-muted">Pilih tujuan — penjelasan singkat dari master data.</span>';
}
function updateTarikTujuanPerintahHelp() {
    const el = document.getElementById('tarikHelpTujuanPerintah');
    const sel = document.getElementById('tarikTujuanSelect');
    if (!el || !sel) return;
    if (!sel.value) {
        el.innerHTML = '<span class="text-muted">Pilih tujuan — penjelasan singkat dari master data.</span>';
        return;
    }
    const row = lastTarikTujuanList.find(r => String(r.id) === String(sel.value));
    const d = row && row.deskripsi ? String(row.deskripsi).trim() : '';
    if (d) {
        el.textContent = d;
    } else {
        el.innerHTML = '<span class="text-warning"><i class="fas fa-info-circle me-1"></i>Deskripsi tujuan ini kosong di master data.</span>';
    }
}

function openTarikModal() {
    const modal = new bootstrap.Modal(document.getElementById('tarikDIModal'));
    modal.show();
    loadTarikModalData();
}

document.getElementById('tarikDIModal').addEventListener('hidden.bs.modal', function () {
    destroyTarikTujuanSelect2();
    lastTarikTujuanList = [];
    resetTarikTujuanHelp();
    const hidLoc = document.getElementById('tarikCustomerLocationId');
    if (hidLoc) hidLoc.value = '';
});

async function loadTarikModalData() {
    try {
        // 1. Load jenis perintah to find TARIK id
        const jenisRes = await fetch(BASE_URL + 'marketing/get-jenis-perintah-kerja', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const jenisData = await jenisRes.json();
        if (jenisData.success) {
            const tarikJenis = jenisData.data.find(j => j.kode.toUpperCase() === 'TARIK');
            if (tarikJenis) {
                tarikJenisId = tarikJenis.id;
                document.getElementById('tarikJenisId').value = tarikJenisId;

                // 2. Load tujuan options for TARIK (badge Select2 + deskripsi — sama seperti Create DI)
                const tujuanRes = await fetch(BASE_URL + 'marketing/get-tujuan-perintah-kerja?jenis_id=' + tarikJenisId, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const tujuanData = await tujuanRes.json();
                if (tujuanData.success) {
                    tarikTujuanOptions = tujuanData.data;
                    lastTarikTujuanList = tujuanData.data || [];
                    destroyTarikTujuanSelect2();
                    const sel = document.getElementById('tarikTujuanSelect');
                    sel.innerHTML = '';
                    const ph = document.createElement('option');
                    ph.value = '';
                    ph.textContent = TARIK_PH_TUJUAN;
                    sel.appendChild(ph);
                    tujuanData.data.forEach(t => {
                        const opt = document.createElement('option');
                        opt.value = t.id;
                        opt.setAttribute('data-kode', t.kode || '');
                        opt.setAttribute('data-nama', t.nama || '');
                        opt.setAttribute('data-deskripsi', (t.deskripsi || '').trim());
                        opt.textContent = (t.kode ? t.kode + ' - ' : '') + (t.nama || t.kode || '');
                        opt.title = (t.deskripsi || '').trim() || t.nama || '';
                        if (t.kode === 'TARIK_HABIS_KONTRAK') opt.selected = true;
                        sel.appendChild(opt);
                    });
                    initTarikTujuanSelect2();
                    if (typeof jQuery !== 'undefined') {
                        jQuery('#tarikTujuanSelect').off('change.tarikHelp select2:select.tarikHelp').on('change.tarikHelp select2:select.tarikHelp', updateTarikTujuanPerintahHelp);
                    } else {
                        sel.onchange = updateTarikTujuanPerintahHelp;
                    }
                    updateTarikTujuanPerintahHelp();
                }
            }
        }

        // 3. Load contract info for pelanggan/lokasi
        const kontrakRes = await fetch(BASE_URL + 'marketing/rental/get/' + CONTRACT_ID, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const kontrakData = await kontrakRes.json();
        if (kontrakData.success && kontrakData.data) {
            document.getElementById('tarikPelanggan').value = kontrakData.data.customer_name || '';
            document.getElementById('tarikPelangganId').value = kontrakData.data.customer_id || '';
            document.getElementById('tarikLokasi').value = kontrakData.data.lokasi || kontrakData.data.location_name || '';
            const cd = document.getElementById('tarikCustomerDisplay');
            if (cd) cd.value = kontrakData.data.customer_name || '';
            await resolveTarikCustomerLocation(kontrakData.data.customer_id || null);
        }

        // 4. Load units from this contract
        const unitsRes = await fetch(BASE_URL + 'marketing/rental/units/' + CONTRACT_ID, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const unitsData = await unitsRes.json();
        renderTarikUnits(unitsData.success ? (unitsData.data || []) : []);

    } catch (err) {
        console.error('Error loading tarik modal data:', err);
        if (window.OptimaNotify) OptimaNotify.error(LANG_TARIK.load_error || 'Gagal memuat data untuk DI penarikan');
    }
}

/** Satukan Location + Customer Location: isi hidden customer_location_id dari master lokasi customer (padankan nama kontrak). */
function tarikLocationLabel(loc) {
    return (loc.location_name || 'Location') + (loc.city ? ' - ' + loc.city : '');
}

async function resolveTarikCustomerLocation(customerId) {
    const hid = document.getElementById('tarikCustomerLocationId');
    const locInput = document.getElementById('tarikLokasi');
    if (hid) hid.value = '';
    if (!customerId || !locInput) return;

    try {
        const res = await fetch(BASE_URL + 'marketing/kontrak/customer-locations/' + customerId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (!data.success || !Array.isArray(data.data) || data.data.length === 0) {
            return;
        }

        const rows = data.data;
        const needle = (locInput.value || '').trim().toLowerCase();

        let chosen = null;
        if (needle) {
            chosen = rows.find(function (loc) {
                return (loc.location_name || '').toLowerCase() === needle;
            });
            if (!chosen) {
                chosen = rows.find(function (loc) {
                    const ln = (loc.location_name || '').toLowerCase();
                    return ln && (needle.includes(ln) || ln.includes(needle));
                });
            }
        }
        if (!chosen && rows.length === 1) {
            chosen = rows[0];
        }
        if (!chosen && rows.length > 0) {
            chosen = rows[0];
        }

        if (chosen && hid) {
            hid.value = String(chosen.id);
            locInput.value = tarikLocationLabel(chosen);
        }
    } catch (e) {
        console.error('Error resolving customer location for retrieval DI:', e);
    }
}

function renderTarikUnits(units) {
    const container = document.getElementById('tarikUnitsList');
    if (!units.length) {
        container.innerHTML = '<div class="text-center text-muted py-3"><i class="fas fa-info-circle me-1"></i>Tidak ada unit di kontrak ini</div>';
        return;
    }
    const noUnitsText = (LANG_TARIK && LANG_TARIK.no_units) ? LANG_TARIK.no_units : 'Tidak ada unit di kontrak ini';
    if (!units.length) {
        container.innerHTML = '<div class="text-center text-muted py-3"><i class="fas fa-info-circle me-1"></i>' + noUnitsText + '</div>';
        return;
    }
    container.innerHTML = units.map(u => {
        const unitId = u.unit_id || u.id_inventory_unit || u.id;
        const noUnit = u.no_unit || u.unit_label || ('Unit #' + unitId);
        const serial = u.serial_number || '';
        const jenis = u.jenis_unit || (u.merk ? u.merk + ' ' + (u.model || '') : '');
        const location = u.location_name || '';
        return `
        <div class="form-check border-bottom py-2">
            <input class="form-check-input tarik-unit-cb" type="checkbox" name="tarik_units[]" value="${unitId}" id="tarikU_${unitId}" onchange="updateTarikCount()">
            <label class="form-check-label w-100" for="tarikU_${unitId}">
                <div class="d-flex justify-content-between">
                    <div><strong>${noUnit}</strong> ${serial ? '<small class="text-muted">(' + serial + ')</small>' : ''}</div>
                    <small class="text-muted">${jenis}</small>
                </div>
                ${location ? '<small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>' + location + '</small>' : ''}
            </label>
        </div>`;
    }).join('');

    // Select/Clear all handlers
    document.getElementById('tarikSelectAll').onclick = () => {
        container.querySelectorAll('.tarik-unit-cb').forEach(cb => cb.checked = true);
        updateTarikCount();
    };
    document.getElementById('tarikClearAll').onclick = () => {
        container.querySelectorAll('.tarik-unit-cb').forEach(cb => cb.checked = false);
        updateTarikCount();
    };
}

function updateTarikCount() {
    const count = document.querySelectorAll('.tarik-unit-cb:checked').length;
    document.getElementById('tarikSelectedCount').textContent = count;
    document.getElementById('btnSubmitTarikDI').disabled = (count === 0);
}

// Submit DI Penarikan
document.getElementById('btnSubmitTarikDI').addEventListener('click', async function() {
    const form = document.getElementById('tarikDIForm');
    const fd = new FormData(form);

    const checkedUnits = document.querySelectorAll('.tarik-unit-cb:checked');
    if (!checkedUnits.length) {
        if (window.OptimaNotify) OptimaNotify.warning(LANG_TARIK.min_unit || 'Pilih minimal satu unit untuk ditarik');
        return;
    }
    const tujuanId = document.getElementById('tarikTujuanSelect')?.value || '';
    if (!tujuanId) {
        if (window.OptimaNotify) OptimaNotify.warning(LANG_TARIK.select_purpose || 'Pilih tujuan penarikan terlebih dahulu.');
        return;
    }
    const customerLocationId = document.getElementById('tarikCustomerLocationId')?.value || '';
    if (!customerLocationId) {
        if (window.OptimaNotify) {
            OptimaNotify.warning('Lokasi customer tidak dapat dipetakan otomatis. Periksa master lokasi customer atau hubungi admin.');
        }
        return;
    }

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>' + (LANG_TARIK.processing || 'Memproses...');

    try {
        const response = await fetch(BASE_URL + 'marketing/di/create', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd
        });
        const result = await response.json();

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('tarikDIModal')).hide();
            if (window.OptimaNotify) OptimaNotify.success((LANG_TARIK.success || 'DI Penarikan berhasil dibuat') + ': ' + (result.nomor || ''));
            window.location.reload();
        } else {
            if (window.OptimaNotify) OptimaNotify.error(result.message || (LANG_TARIK.error || 'Gagal membuat DI'));
        }
    } catch (err) {
        console.error('Submit tarik DI error:', err);
        if (window.OptimaNotify) OptimaNotify.error(LANG_TARIK.network_error || 'Network error saat membuat DI');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-truck-loading me-1"></i>' + (LANG_TARIK.submit || 'Buat DI Penarikan');
    }
});

<?php endif; ?>
</script>
<?= $this->endSection() ?>

