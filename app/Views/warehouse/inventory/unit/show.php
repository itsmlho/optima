<?= $this->extend('layouts/base') ?>

<?php
helper('global_permission');
helper('accessory');
$permissions = get_global_permission('warehouse');
$can_edit = $permissions['edit'];

// Setup status badges
// status_unit_id canonical mapping:
// 1=AVAILABLE_STOCK, 2=NON_ASSET_STOCK, 3=BOOKED, 4=PREPARATION, 5=READY_TO_DELIVER,
// 6=IN_DELIVERY, 7=RENTAL_ACTIVE, 8=RENTAL_DAILY, 9=TRIAL, 10=BREAKDOWN,
// 11=MAINTENANCE (DI/service), 12=RETURNED, 13=SOLD, 14=RENTAL_INACTIVE, 15=SPARE, 16=NONAKTIF
$statusId = (int)($unit['status_unit_id'] ?? 0);
$badgeClass = 'secondary';
$statusIcon = 'fa-info-circle';
if (in_array($statusId, [1, 9, 12, 15]))          { $badgeClass = 'success';   $statusIcon = 'fa-check-circle'; }
elseif (in_array($statusId, [2, 3]))               { $badgeClass = 'info';      $statusIcon = 'fa-tag'; }
elseif (in_array($statusId, [4, 5, 6]))            { $badgeClass = 'warning';   $statusIcon = 'fa-clock'; }
elseif (in_array($statusId, [7, 8]))               { $badgeClass = 'warning';   $statusIcon = 'fa-industry'; }
elseif (in_array($statusId, [10, 11]))             { $badgeClass = 'danger';    $statusIcon = 'fa-tools'; }
elseif ($statusId === 13)                          { $badgeClass = 'dark';      $statusIcon = 'fa-times-circle'; }
elseif ($statusId === 14)                          { $badgeClass = 'secondary'; $statusIcon = 'fa-pause-circle'; }
elseif ($statusId === 16)                          { $badgeClass = 'dark';      $statusIcon = 'fa-ban'; }

$unitNo = $unit['no_unit'] ?: ($unit['no_unit_na'] ?: 'TEMP-'.$unit['id_inventory_unit']);
$serialNo = $unit['serial_number'] ?? ($unit['serial_no'] ?? null);
// Fall back to tipe_unit (tipe/jenis) when model_unit FK is missing (id=0)
$brand = !empty($unit['merk_unit']) ? $unit['merk_unit'] : ($unit['unit_tipe'] ?? 'N/A');
$model = !empty($unit['model_unit']) ? $unit['model_unit'] : ($unit['unit_jenis'] ?? '');

// Parse aksesoris JSON -> readable labels
$aksesorisRaw  = $unit['aksesoris'] ?? null;
$aksesorisItems = [];
if ($aksesorisRaw) {
    $decoded = json_decode($aksesorisRaw, true);
    if (is_array($decoded)) {
        // Check if associative object {"rotary_lamp": true} vs sequential array ["rotary_lamp", ...]
        $isAssoc = array_keys($decoded) !== range(0, count($decoded) - 1);
        foreach ($decoded as $k => $v) {
            if ($isAssoc) {
                // Object format: key = accessory slug, value = bool/truthy
                if ($v) {
                    $aksesorisItems[] = format_accessory_label($k);
                }
            } else {
                // Array format: value = accessory slug or label string
                if ($v && !is_bool($v)) {
                    $aksesorisItems[] = format_accessory_label($v);
                }
            }
        }
    } else {
        $rawItems = array_values(array_filter(array_map('trim', explode(',', $aksesorisRaw))));
        $aksesorisItems = array_map(static fn ($item) => format_accessory_label($item), $rawItems);
    }
}
?>

<?= $this->section('content') ?>

<!-- Page Header (Native Optima Style) -->
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('warehouse/inventory/unit') ?>"><i class="fas fa-boxes me-1"></i>Unit Inventory</a></li>
                <li class="breadcrumb-item active"><?= esc($unitNo) ?></li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-truck-loading me-2 text-primary"></i>Unit Detail
        </h4>
        <p class="text-muted small mb-0"><strong class="text-dark fs-6"><?= esc($unitNo) ?></strong> &bull; <span class="badge bg-<?= $badgeClass ?>"><?= esc($unit['status_unit_name'] ?? 'Unknown') ?></span></p>
    </div>
    <!-- Action Buttons -->
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= base_url('warehouse/inventory/unit') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Back
        </a>
        <button type="button" class="btn btn-outline-info btn-sm" onclick="fetchUnitHistory(<?= (int)($unit['id_inventory_unit'] ?? 0) ?>)">
            <i class="fas fa-history me-1" aria-hidden="true"></i>Refresh History
        </button>
        <?php if($can_edit): ?>
        <?php if(in_array($statusId, [1, 2, 9, 12, 15])): ?>
        <button type="button" class="btn btn-success btn-sm" onclick="openBookingModal()">
            <i class="fas fa-bookmark me-1"></i>Booking
        </button>
        <?php endif; ?>
        <?php if($statusId === 10): ?>
        <button type="button" class="btn btn-danger btn-sm" onclick="openScrapModal()">
            <i class="fas fa-trash-alt me-1"></i>Scrab Unit
        </button>
        <?php endif; ?>
        <?php if(in_array($statusId, [1, 3, 12])): ?>
        <button type="button" class="btn btn-warning btn-sm" onclick="openChangeStatusModal()">
            <i class="fas fa-exchange-alt me-1"></i>Change Status
        </button>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <!-- Main Content (Left) -->
    <div class="col-lg-9">

        <!-- Main Card & Tabs -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <i class="fas fa-box me-2"></i>
                <strong><?= esc($brand) ?> - <?= esc($model) ?></strong>
                <span class="ms-auto badge bg-dark"><i class="fas fa-barcode me-1"></i> <?= esc($unit['serial_number'] ?? 'N/A') ?></span>
            </div>
            <div class="card-body">

                <?php
                // Tab badge counts
                $activeRentalCount = count(array_filter($rental_history, fn($r) => $r['status'] === 'ACTIVE'));
                $totalRentalCount  = count($rental_history);
                ?>
                <!-- Native Bootstrap 5 Nav Tabs (Ramping: Overview, Spesifikasi, Riwayat, Aktivitas) -->
                <ul class="nav nav-tabs nav-fill mb-3" id="detailTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-overview" data-bs-toggle="tab" data-bs-target="#pane-overview" type="button" role="tab">
                            <i class="fas fa-info-circle me-1"></i>Overview
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-components" data-bs-toggle="tab" data-bs-target="#pane-components" type="button" role="tab">
                            <i class="fas fa-cogs me-1"></i>Spesifikasi
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-riwayat" data-bs-toggle="tab" data-bs-target="#pane-riwayat" type="button" role="tab">
                            <i class="fas fa-file-contract me-1"></i>Riwayat Kontrak
                            <?php if($totalRentalCount > 0): ?>
                            <span class="badge badge-soft-blue ms-1"><?= $totalRentalCount ?></span>
                            <?php endif; ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-aktivitas" data-bs-toggle="tab" data-bs-target="#pane-aktivitas" type="button" role="tab">
                            <i class="fas fa-stream me-1"></i>Aktivitas
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    
                    <!-- ── Overview ── -->
                    <div class="tab-pane fade show active" id="pane-overview" role="tabpanel">
                        <?php
                        // Determine if unit has active contract data
                        $hasActiveContract = !empty($unit['no_kontrak']);
                        $isOnSite = !empty($unit['customer_location_name']);
                        ?>

                        <!-- Temporary Assignment Warning -->
                        <?php if(!empty($unit['is_temporary_assignment'])): ?>
                        <div class="alert alert-warning border-warning d-flex align-items-start gap-2 mb-3 py-2">
                            <i class="fas fa-exchange-alt mt-1 flex-shrink-0"></i>
                            <div class="small">
                                <strong>Unit Pengganti Sementara</strong> —
                                Unit ini sedang bertugas sebagai pengganti sementara.
                                <?php if(!empty($unit['maintenance_location'])): ?>
                                Lokasi maintenance: <strong><?= esc($unit['maintenance_location']) ?></strong>.
                                <?php endif; ?>
                                <?php if(!empty($unit['temporary_for_contract_id'])): ?>
                                Menggantikan unit pada kontrak <a href="<?= base_url('kontrak/detail/'.(int)$unit['temporary_for_contract_id']) ?>" class="alert-link fw-bold">Kontrak #<?= esc($unit['temporary_for_contract_id']) ?></a>.
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Current Location -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i><strong>Current Location</strong></h6>
                            </div>
                            <div class="card-body">
                                <?php if($isOnSite): ?>
                                    <h5 class="mb-1 text-dark fw-bold">
                                        <i class="fas fa-map-marker-alt text-danger me-2 small"></i><?= esc($unit['customer_location_name']) ?>
                                    </h5>
                                    <p class="text-primary mb-1"><i class="fas fa-building me-1"></i> <?= esc($unit['customer_name'] ?? '-') ?></p>
                                    <?php if(!empty($unit['customer_city'])): ?>
                                    <p class="text-muted small mb-1"><i class="fas fa-city me-1"></i><?= esc($unit['customer_city']) ?></p>
                                    <?php endif; ?>
                                    <?php if(!empty($unit['customer_address'])): ?>
                                    <p class="text-muted small mb-0"><i class="fas fa-road me-1"></i><?= esc($unit['customer_address']) ?></p>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <h5 class="mb-0 text-dark fw-bold"><i class="fas fa-warehouse text-primary me-2"></i><?= esc($unit['lokasi_unit'] ?? 'Internal Warehouse') ?></h5>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Active Booking Card -->
                        <?php if(!empty($active_booking)): ?>
                        <?php
                        $bookingFor = '';
                        if(!empty($active_booking['customer_name'])) {
                            $bookingFor = $active_booking['customer_name'] . (!empty($active_booking['customer_code']) ? ' (' . $active_booking['customer_code'] . ')' : '');
                        } elseif(!empty($active_booking['quotation_number'])) {
                            $bookingFor = $active_booking['quotation_prospect'] . ' — ' . $active_booking['quotation_number'];
                        } elseif(!empty($active_booking['customer_name_manual'])) {
                            $bookingFor = $active_booking['customer_name_manual'];
                        }
                        ?>
                        <div class="card mb-3 border-success border-opacity-50">
                            <div class="card-header d-flex align-items-center justify-content-between py-2" style="background:rgba(25,135,84,.07)">
                                <h6 class="mb-0 text-success"><i class="fas fa-bookmark me-2"></i><strong>Unit Ini Sudah Di-Booking</strong></h6>
                                <span class="badge badge-soft-green"><i class="fas fa-circle me-1" style="font-size:.6rem"></i>ACTIVE</span>
                            </div>
                            <div class="card-body py-2">
                                <dl class="row mb-0 small">
                                    <dt class="col-5 text-muted">Booking Untuk</dt>
                                    <dd class="col-7 fw-bold"><?= esc($bookingFor ?: '-') ?></dd>

                                    <?php if(!empty($active_booking['notes'])): ?>
                                    <dt class="col-5 text-muted">Catatan</dt>
                                    <dd class="col-7 fst-italic text-muted"><?= esc($active_booking['notes']) ?></dd>
                                    <?php endif; ?>

                                    <dt class="col-5 text-muted">Di-booking oleh</dt>
                                    <dd class="col-7"><?= esc(trim($active_booking['booked_by_name']) ?: 'System') ?></dd>

                                    <dt class="col-5 text-muted">Waktu Booking</dt>
                                    <dd class="col-7"><?= !empty($active_booking['booked_at']) ? date('d M Y, H:i', strtotime($active_booking['booked_at'])) : '-' ?></dd>
                                </dl>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Active Contract Card -->
                        <?php if($hasActiveContract): ?>
                        <?php
                        $rentalTypeBadge = ['CONTRACT'=>'badge-soft-blue','PO_ONLY'=>'badge-soft-cyan','DAILY_SPOT'=>'badge-soft-yellow'];
                        $rentalTypeCls   = $rentalTypeBadge[$unit['kontrak_rental_type'] ?? ''] ?? 'badge-soft-gray';
                        $endDate  = $unit['kontrak_end_date'] ?? $unit['ku_end_date'] ?? null;
                        $daysLeft = null;
                        $countdownCls = 'badge-soft-green';
                        if($endDate) {
                            $daysLeft = (int)floor((strtotime($endDate) - time()) / 86400);
                            $countdownCls = $daysLeft < 0 ? 'badge-soft-red' : ($daysLeft <= 30 ? 'badge-soft-orange' : 'badge-soft-green');
                        }
                        $isSpare = !empty($unit['ku_is_spare']);
                        ?>
                        <div class="card mb-3 border-primary border-opacity-25">
                            <div class="card-header d-flex align-items-center justify-content-between" style="background:rgba(13,110,253,.05)">
                                <h6 class="mb-0 text-primary"><i class="fas fa-file-contract me-2"></i><strong>Active Contract</strong></h6>
                                <div class="d-flex gap-1 align-items-center">
                                    <?php if($isSpare): ?>
                                    <span class="badge badge-soft-orange"><i class="fas fa-star me-1"></i>SPARE</span>
                                    <?php endif; ?>
                                    <span class="badge <?= $rentalTypeCls ?>"><?= esc($unit['kontrak_rental_type'] ?? '-') ?></span>
                                </div>
                            </div>
                            <div class="card-body py-2">
                                <dl class="row mb-0 small">
                                    <dt class="col-5 text-muted">No. Kontrak</dt>
                                    <dd class="col-7">
                                        <a href="<?= base_url('kontrak/detail/'.(int)$unit['kontrak_id']) ?>" class="fw-bold font-monospace text-decoration-none">
                                            <?= esc($unit['no_kontrak']) ?> <i class="fas fa-external-link-alt small ms-1"></i>
                                        </a>
                                    </dd>

                                    <dt class="col-5 text-muted">Customer</dt>
                                    <dd class="col-7 fw-bold"><?= esc($unit['customer_name'] ?? '-') ?></dd>

                                    <?php if(!empty($unit['customer_po_number'])): ?>
                                    <dt class="col-5 text-muted">PO Number</dt>
                                    <dd class="col-7 font-monospace"><?= esc($unit['customer_po_number']) ?></dd>
                                    <?php endif; ?>

                                    <dt class="col-5 text-muted">Periode</dt>
                                    <dd class="col-7">
                                        <?= !empty($unit['ku_start_date']) ? date('d M Y', strtotime($unit['ku_start_date'])) : '-' ?>
                                        <?php if($endDate): ?>
                                        → <?= date('d M Y', strtotime($endDate)) ?>
                                        <?php else: ?>
                                        → <em class="text-muted">Open Ended</em>
                                        <?php endif; ?>
                                    </dd>

                                    <?php if($daysLeft !== null): ?>
                                    <dt class="col-5 text-muted">Sisa Waktu</dt>
                                    <dd class="col-7">
                                        <span class="badge <?= $countdownCls ?>">
                                            <?php if($daysLeft < 0): ?>
                                            Expired <?= abs($daysLeft) ?> hari lalu
                                            <?php else: ?>
                                            <?= $daysLeft ?> hari lagi
                                            <?php endif; ?>
                                        </span>
                                    </dd>
                                    <?php endif; ?>
                                </dl>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Data & Remarks Row -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-list-alt me-2"></i><strong>Unit Details</strong></h6>
                                    </div>
                                    <div class="card-body">
                                        <dl class="row mb-0 small">
                                            <dt class="col-5 text-muted">Serial Number</dt>
                                            <dd class="col-7 fw-bold font-monospace"><?= esc($unit['serial_number'] ?: '-') ?></dd>

                                            <?php if(!empty($unit['id_po'])): ?>
                                            <dt class="col-5 text-muted">Purchase Order</dt>
                                            <dd class="col-7">
                                                <a href="<?= base_url('purchasing/po/detail/'.(int)$unit['id_po']) ?>" class="font-monospace text-decoration-none fw-bold text-primary">
                                                    PO-<?= esc($unit['id_po']) ?> <i class="fas fa-external-link-alt small ms-1"></i>
                                                </a>
                                            </dd>
                                            <?php endif; ?>

                                            <dt class="col-5 text-muted">Unit Category</dt>
                                            <dd class="col-7 fw-bold"><?= esc($unit['nama_tipe_unit'] ?? '-') ?></dd>

                                            <dt class="col-5 text-muted">Department</dt>
                                            <dd class="col-7 fw-bold"><?= esc($unit['unit_departemen'] ?? 'Unassigned') ?></dd>

                                            <dt class="col-5 text-muted">Registration Date</dt>
                                            <dd class="col-7"><?= !empty($unit['created_at']) ? date('d M Y', strtotime($unit['created_at'])) : '-' ?></dd>

                                            <dt class="col-5 text-muted">Delivery Date</dt>
                                            <dd class="col-7"><?= !empty($unit['tanggal_kirim']) ? date('d M Y', strtotime($unit['tanggal_kirim'])) : '-' ?></dd>

                                            <dt class="col-5 text-muted">Year of Make</dt>
                                            <dd class="col-7"><?= esc($unit['tahun_unit'] ?: 'N/A') ?></dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i><strong>Operations</strong></h6>
                                    </div>
                                    <div class="card-body">
                                        <dl class="row mb-0 small">
                                            <dt class="col-6 text-muted">Hour Meter</dt>
                                            <dd class="col-6 fw-bold"><?= $unit['hour_meter'] !== null ? number_format($unit['hour_meter']).' h' : '-' ?></dd>

                                            <dt class="col-6 text-muted">On Hire Date</dt>
                                            <dd class="col-6"><?= !empty($unit['on_hire_date']) ? date('d M Y', strtotime($unit['on_hire_date'])) : '-' ?></dd>

                                            <dt class="col-6 text-muted">Off Hire Date</dt>
                                            <dd class="col-6"><?= !empty($unit['off_hire_date']) ? date('d M Y', strtotime($unit['off_hire_date'])) : '-' ?></dd>

                                            <dt class="col-6 text-muted">Expected Return</dt>
                                            <dd class="col-6"><?= !empty($unit['expected_return_date']) ? date('d M Y', strtotime($unit['expected_return_date'])) : '-' ?></dd>
                                        </dl>
                                        <?php if(!empty($unit['keterangan'])): ?>
                                        <hr class="my-2">
                                        <p class="mb-0 text-muted" style="font-size:.8rem"><i class="fas fa-sticky-note me-1"></i><em><?= nl2br(esc($unit['keterangan'])) ?></em></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── Technical Specs ── -->
                    <div class="tab-pane fade" id="pane-components" role="tabpanel">

                        <?php if($can_edit): ?>
                        <div class="alert alert-light border small py-2 mb-3 d-flex align-items-center gap-2">
                            <i class="fas fa-info-circle text-primary"></i>
                            Click <strong>"Edit Specs"</strong> on each card to modify the allowed specifications.
                        </div>
                        <?php endif; ?>

                        <div class="row g-3">
                            <!-- ── Engine, Mast & Tyres (Inline Editable) ── -->
                            <div class="col-12">
                                <div class="card" id="card-specs">
                                    <div class="card-header bg-light d-flex align-items-center justify-content-between py-2">
                                        <h6 class="mb-0">
                                            <i class="fas fa-cogs me-2 text-secondary"></i>
                                            <strong>Technical Specifications — Engine, Mast & Tyres</strong>
                                        </h6>
                                        <?php if($can_edit): ?>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-sm btn-outline-primary" id="btnEditSpecs" onclick="toggleSpecEdit(true)">
                                                <i class="fas fa-pencil-alt me-1"></i>Edit Specs
                                            </button>
                                            <button class="btn btn-sm btn-primary d-none" id="btnSaveSpecs" onclick="saveSpecsInline()">
                                                <i class="fas fa-save me-1"></i>Save
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary d-none" id="btnCancelSpecs" onclick="toggleSpecEdit(false)">
                                                Cancel
                                            </button>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="row g-0">
                                            <!-- ── Engine Column ── -->
                                            <div class="col-md-6 border-end">
                                                <div class="px-3 pt-3 pb-2">
                                                    <p class="small fw-semibold text-muted border-bottom pb-1 mb-2">
                                                        <i class="fas fa-fire me-1 text-warning"></i>Engine & Power
                                                    </p>
                                                </div>
                                                <ul class="list-group list-group-flush small">
                                                    <!-- Engine Model -->
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span class="text-muted">Engine Model</span>
                                                        <span class="fw-bold spec-view" id="view-mesin"><?= esc(trim(($unit['merk_mesin'] ?? '').' '.($unit['model_mesin'] ?? '')) ?: '-') ?></span>
                                                        <div class="spec-edit d-none" style="min-width:180px;">
                                                            <select name="model_mesin_id" class="form-select form-select-sm">
                                                                <option value="">-- Select --</option>
                                                                <?php foreach($mesin as $me): ?>
                                                                <option value="<?= $me['id'] ?>" <?= $unit['model_mesin_id'] == $me['id'] ? 'selected' : '' ?>>
                                                                    <?= esc($me['merk_mesin'].' '.$me['model_mesin']) ?>
                                                                </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </li>
                                                    <!-- Engine S/N -->
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span class="text-muted">Engine S/N</span>
                                                        <span class="fw-bold font-monospace spec-view" id="view-sn-mesin"><?= esc($unit['sn_mesin'] ?: '-') ?></span>
                                                        <div class="spec-edit d-none">
                                                            <input type="text" name="sn_mesin" class="form-control form-control-sm" style="min-width:140px;"
                                                                   value="<?= esc($unit['sn_mesin']) ?>" placeholder="Engine S/N">
                                                        </div>
                                                    </li>
                                                    <!-- Capacity (inline editable) -->
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span class="text-muted">Capacity</span>
                                                        <span class="fw-bold spec-view" id="view-kapasitas"><?= esc($unit['kapasitas_display'] ?? '-') ?></span>
                                                        <div class="spec-edit d-none" style="min-width:150px;">
                                                            <select name="kapasitas_unit_id" class="form-select form-select-sm">
                                                                <option value="">-- Select --</option>
                                                                <?php foreach($kapasitas as $kap): ?>
                                                                <option value="<?= $kap['id_kapasitas'] ?>" <?= $unit['kapasitas_unit_id'] == $kap['id_kapasitas'] ? 'selected' : '' ?>>
                                                                    <?= esc($kap['kapasitas']) ?>
                                                                </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </li>
                                                    <!-- Fuel Type — from iu.fuel_type, fallback to engine's department -->
                                                    <?php
                                                    // fuel_type: direct ENUM → engine's dept → unit's own departemen
                                                    $fuelRaw = $unit['fuel_type'] ?? $unit['fuel_type_dept'] ?? $unit['unit_departemen'] ?? '';
                                                    $fuelBadge = ['DIESEL'=>'warning','LPG'=>'info','ELECTRIC'=>'primary','GASOLINE'=>'secondary'];
                                                    $fuelCls   = $fuelBadge[strtoupper($fuelRaw)] ?? 'light';
                                                    ?>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span class="text-muted">Fuel Type</span>
                                                        <!-- VIEW -->
                                                        <span class="spec-view" id="view-fuel">
                                                            <?php if($fuelRaw): ?>
                                                            <span class="badge bg-<?= $fuelCls ?> text-<?= $fuelCls==='light'?'dark':'white' ?>"><?= esc(strtoupper($fuelRaw)) ?></span>
                                                            <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </span>
                                                        <!-- EDIT -->
                                                        <div class="spec-edit d-none" style="min-width:130px;">
                                                            <select name="fuel_type" class="form-select form-select-sm">
                                                                <option value="">-- Select --</option>
                                                                <?php foreach(['DIESEL','LPG','ELECTRIC','GASOLINE'] as $ft): ?>
                                                                <option value="<?= $ft ?>" <?= strtoupper($unit['fuel_type'] ?? '') === $ft ? 'selected' : '' ?>><?= $ft ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </li>
                                                    <!-- Ownership Status -->
                                                    <?php
                                                    $ownRaw = $unit['ownership_status'] ?? '';
                                                    $ownBadge = ['OWNED'=>'success','LEASED'=>'warning','CONSIGNMENT'=>'info'];
                                                    $ownCls   = $ownBadge[$ownRaw] ?? 'secondary';
                                                    ?>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span class="text-muted">Ownership</span>
                                                        <!-- VIEW -->
                                                        <span class="spec-view" id="view-ownership">
                                                            <?php if($ownRaw): ?>
                                                            <span class="badge bg-<?= $ownCls ?>"><?= esc($ownRaw) ?></span>
                                                            <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </span>
                                                        <!-- EDIT -->
                                                        <div class="spec-edit d-none" style="min-width:150px;">
                                                            <select name="ownership_status" class="form-select form-select-sm">
                                                                <option value="">-- Select --</option>
                                                                <?php foreach(['OWNED','LEASED','CONSIGNMENT'] as $os): ?>
                                                                <option value="<?= $os ?>" <?= ($unit['ownership_status'] ?? '') === $os ? 'selected' : '' ?>><?= $os ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </li>
                                                    <!-- Year of Make (read-only) -->
                                                    <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                                                        <span class="text-muted">Year of Make</span>
                                                        <span class="fw-bold"><?= esc($unit['tahun_unit'] ?: '-') ?></span>
                                                    </li>
                                                    <!-- Asset Tag (read-only) -->
                                                    <?php if(!empty($unit['asset_tag'])): ?>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                                                        <span class="text-muted">Asset Tag</span>
                                                        <span class="fw-bold font-monospace"><?= esc($unit['asset_tag']) ?></span>
                                                    </li>
                                                    <?php endif; ?>
                                                    <!-- Hour Meter -->
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span class="text-muted">Hour Meter</span>
                                                        <span class="fw-bold spec-view" id="view-hour-meter"><?= esc($unit['hour_meter'] !== null && $unit['hour_meter'] !== '' ? number_format((float)$unit['hour_meter'], 0, '.', ',').' HM' : '-') ?></span>
                                                        <div class="spec-edit d-none">
                                                            <input type="number" name="hour_meter" class="form-control form-control-sm" style="min-width:120px;"
                                                                   value="<?= esc($unit['hour_meter']) ?>" step="0.1" min="0" placeholder="e.g. 5230">
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>

                                            <!-- ── Mast & Tyres Column ── -->
                                            <div class="col-md-6">
                                                <div class="px-3 pt-3 pb-2">
                                                    <p class="small fw-semibold text-muted border-bottom pb-1 mb-2">
                                                        <i class="fas fa-layer-group me-1 text-secondary"></i>Mast & Tyres
                                                    </p>
                                                </div>
                                                <ul class="list-group list-group-flush small">
                                                    <!-- Mast Type -->
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span class="text-muted">Mast Type</span>
                                                        <span class="fw-bold spec-view" id="view-mast"><?= esc($unit['tipe_mast'] ?? '-') ?></span>
                                                        <div class="spec-edit d-none" style="min-width:180px;">
                                                            <select name="model_mast_id" class="form-select form-select-sm">
                                                                <option value="">-- Select Mast --</option>
                                                                <?php foreach($tipe_mast as $tm): ?>
                                                                <?php $mastLabel = $tm['tipe_mast'] . (!empty($tm['tinggi_mast']) ? ' — '.$tm['tinggi_mast'] : ''); ?>
                                                                <option value="<?= $tm['id_mast'] ?>" <?= $unit['model_mast_id'] == $tm['id_mast'] ? 'selected' : '' ?>>
                                                                    <?= esc($mastLabel) ?>
                                                                </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </li>
                                                    <!-- Mast Height — actual value from unit -->
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span class="text-muted">Mast Height</span>
                                                        <span class="fw-bold spec-view" id="view-mast-height">
                                                            <?php
                                                            $mh = $unit['tinggi_mast'] ?? $unit['mast_tinggi_default'] ?? null;
                                                            echo $mh ? esc($mh) . ' mm' : '-';
                                                            ?>
                                                        </span>
                                                        <div class="spec-edit d-none">
                                                            <input type="text" name="tinggi_mast" class="form-control form-control-sm" style="min-width:100px;"
                                                                   value="<?= esc($unit['tinggi_mast'] ?? '') ?>" placeholder="e.g. 3000">
                                                        </div>
                                                    </li>
                                                    <!-- Mast S/N -->
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span class="text-muted">Mast S/N</span>
                                                        <span class="fw-bold font-monospace spec-view" id="view-sn-mast"><?= esc($unit['sn_mast'] ?: '-') ?></span>
                                                        <div class="spec-edit d-none">
                                                            <input type="text" name="sn_mast" class="form-control form-control-sm" style="min-width:140px;"
                                                                   value="<?= esc($unit['sn_mast']) ?>" placeholder="Mast S/N">
                                                        </div>
                                                    </li>
                                                    <!-- Tyre Type (editable if tipe_ban table exists) -->
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span class="text-muted">Tyre Type</span>
                                                        <span class="fw-bold spec-view" id="view-ban"><?= esc($unit['tipe_ban'] ?? '-') ?></span>
                                                        <div class="spec-edit d-none" style="min-width:180px;">
                                                            <?php if(!empty($tipe_ban)): ?>
                                                            <select name="ban_id" class="form-select form-select-sm">
                                                                <option value="">-- Select --</option>
                                                                <?php foreach($tipe_ban as $tb): ?>
                                                                <option value="<?= $tb['id_ban'] ?>" <?= $unit['ban_id'] == $tb['id_ban'] ? 'selected' : '' ?>><?= esc($tb['tipe_ban']) ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                            <?php else: ?>
                                                            <input type="text" name="ban_id" class="form-control form-control-sm"
                                                                   value="<?= esc($unit['ban_id']) ?>" placeholder="Tyre type">
                                                            <?php endif; ?>
                                                        </div>
                                                    </li>
                                                    <!-- Front & Rear Tyre — read-only from model_unit -->
                                                    <li class="list-group-item bg-light">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="text-muted">Front Tyre</span>
                                                            <span class="fw-bold font-monospace text-muted" title="From model specification">
                                                                <?= esc(($unit['ban_depan'] ?? '') ?: '-') ?>
                                                                <i class="fas fa-lock ms-1 small"></i>
                                                            </span>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item bg-light">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="text-muted">Rear Tyre</span>
                                                            <span class="fw-bold font-monospace text-muted" title="From model specification">
                                                                <?= esc(($unit['ban_belakang'] ?? '') ?: '-') ?>
                                                                <i class="fas fa-lock ms-1 small"></i>
                                                            </span>
                                                        </div>
                                                    </li>
                                                    <!-- Wheel Type -->
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span class="text-muted">Wheel Type</span>
                                                        <span class="fw-bold spec-view" id="view-roda"><?= esc($unit['jenis_roda'] ?? '-') ?></span>
                                                        <div class="spec-edit d-none" style="min-width:150px;">
                                                            <select name="roda_id" class="form-select form-select-sm">
                                                                <option value="">-- Select --</option>
                                                                <?php foreach($jenis_roda as $r): ?>
                                                                <option value="<?= $r['id_roda'] ?>" <?= ($unit['roda_id'] ?? '') == $r['id_roda'] ? 'selected' : '' ?>><?= esc($r['tipe_roda']) ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </li>
                                                    <!-- Valve -->
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span class="text-muted">Valve</span>
                                                        <span class="fw-bold spec-view" id="view-valve"><?= esc($unit['jumlah_valve'] ?? '-') ?></span>
                                                        <div class="spec-edit d-none" style="min-width:150px;">
                                                            <select name="valve_id" class="form-select form-select-sm">
                                                                <option value="">-- Select --</option>
                                                                <?php foreach($valve as $v): ?>
                                                                <option value="<?= $v['id_valve'] ?>" <?= ($unit['valve_id'] ?? '') == $v['id_valve'] ? 'selected' : '' ?>><?= esc($v['jumlah_valve']) ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Accessories -->
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0"><i class="fas fa-tools me-2 text-secondary"></i><strong>Accessories</strong></h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if(empty($aksesorisItems)): ?>
                                        <span class="text-muted small">Standard / No accessories</span>
                                        <?php else: ?>
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php foreach($aksesorisItems as $item): ?>
                                            <span class="badge bg-secondary rounded-pill"><?= esc($item) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Komponen Terpasang Saat Ini -->
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0"><i class="fas fa-puzzle-piece me-2 text-secondary"></i><strong>Komponen Terpasang Saat Ini</strong></h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-2 small">
                                            <?php
                                            $comp   = $current_components ?? ['battery' => null, 'charger' => null, 'attachment' => null];
                                            $hasAny = !empty($comp['battery']) || !empty($comp['charger']) || !empty($comp['attachment']);
                                            ?>
                                            <?php if (!$hasAny): ?>
                                            <div class="col-12"><p class="text-muted mb-0">Tidak ada attachment, charger, atau baterai terpasang.</p></div>
                                            <?php else: ?>
                                            <?php if (!empty($comp['attachment']) && is_array($comp['attachment'])): $a = $comp['attachment']; ?>
                                            <div class="col-md-4">
                                                <span class="badge badge-soft-gray me-1">Attachment</span>
                                                <strong><?= esc(trim(($a['merk'] ?? '').' '.($a['model'] ?? '').' '.($a['tipe'] ?? '')) ?: 'N/A') ?></strong>
                                                <?php if (!empty($a['sn_attachment'])): ?><br><span class="text-muted font-monospace small">S/N: <?= esc($a['sn_attachment']) ?></span><?php endif; ?>
                                            </div>
                                            <?php endif; ?>
                                            <?php if (!empty($comp['charger']) && is_array($comp['charger'])): $c = $comp['charger']; ?>
                                            <div class="col-md-4">
                                                <span class="badge badge-soft-blue me-1">Charger</span>
                                                <strong><?= esc(trim(($c['merk_charger'] ?? '').' '.($c['tipe_charger'] ?? '')) ?: 'N/A') ?></strong>
                                                <?php if (!empty($c['sn_charger'])): ?><br><span class="text-muted font-monospace small">S/N: <?= esc($c['sn_charger']) ?></span><?php endif; ?>
                                            </div>
                                            <?php endif; ?>
                                            <?php if (!empty($comp['battery']) && is_array($comp['battery'])): $b = $comp['battery']; ?>
                                            <div class="col-md-4">
                                                <span class="badge badge-soft-yellow me-1">Baterai</span>
                                                <strong><?= esc(trim(($b['merk_baterai'] ?? '').' '.($b['tipe_baterai'] ?? '').' '.($b['jenis_baterai'] ?? '')) ?: 'N/A') ?></strong>
                                                <?php if (!empty($b['sn_baterai'])): ?><br><span class="text-muted font-monospace small">S/N: <?= esc($b['sn_baterai']) ?></span><?php endif; ?>
                                            </div>
                                            <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes (Inline Editable) -->
                            <div class="col-12">
                                <div class="card" id="card-catatan">
                                    <div class="card-header bg-light d-flex align-items-center justify-content-between py-2">
                                        <h6 class="mb-0"><i class="fas fa-sticky-note me-2 text-warning"></i><strong>Notes</strong></h6>
                                        <?php if($can_edit): ?>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-sm btn-outline-primary" id="btnEditCatatan" onclick="toggleCatatanEdit(true)">
                                                <i class="fas fa-pencil-alt me-1"></i>Edit
                                            </button>
                                            <button class="btn btn-sm btn-primary d-none" id="btnSaveCatatan" onclick="saveCatatanInline()">
                                                <i class="fas fa-save me-1"></i>Save
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary d-none" id="btnCancelCatatan" onclick="toggleCatatanEdit(false)">
                                                Cancel
                                            </button>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body">
                                        <div id="view-catatan" class="text-muted small">
                                            <?php if(!empty($unit['keterangan'])): ?>
                                            <?= nl2br(esc($unit['keterangan'])) ?>
                                            <?php else: ?>
                                            <em class="text-muted">No notes yet.</em>
                                            <?php endif; ?>
                                        </div>
                                        <div id="edit-catatan" class="d-none">
                                            <textarea name="keterangan" id="inputCatatan" class="form-control form-control-sm" rows="4"
                                                      placeholder="Additional notes..."><?= esc($unit['keterangan']) ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div><!-- /row -->
                    </div>

                    <!-- ── Riwayat Kontrak ── -->
                    <div class="tab-pane fade" id="pane-riwayat" role="tabpanel">
                        <?php if(empty($rental_history)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-file-contract fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted mb-0">Belum ada riwayat kontrak untuk unit ini.</p>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle small mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. Kontrak</th>
                                        <th>Customer</th>
                                        <th>Lokasi</th>
                                        <th>Tipe</th>
                                        <th>Mulai</th>
                                        <th>Selesai</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $rhStatusBadge = [
                                    'ACTIVE'        => 'badge-soft-green',
                                    'TEMP_ACTIVE'   => 'badge-soft-cyan',
                                    'PULLED'        => 'badge-soft-gray',
                                    'REPLACED'      => 'badge-soft-gray',
                                    'INACTIVE'      => 'badge-soft-gray',
                                    'MAINTENANCE'   => 'badge-soft-yellow',
                                    'UNDER_REPAIR'  => 'badge-soft-yellow',
                                    'TEMP_REPLACED' => 'badge-soft-orange',
                                    'TEMP_ENDED'    => 'badge-soft-gray',
                                ];
                                $rhTypeBadge = [
                                    'CONTRACT'   => 'badge-soft-blue',
                                    'PO_ONLY'    => 'badge-soft-cyan',
                                    'DAILY_SPOT' => 'badge-soft-yellow',
                                ];
                                foreach($rental_history as $rh):
                                    $rhCls  = $rhStatusBadge[$rh['status']] ?? 'badge-soft-gray';
                                    $rhType = $rhTypeBadge[$rh['rental_type'] ?? ''] ?? 'badge-soft-gray';
                                ?>
                                <tr>
                                    <td>
                                        <?php if(!empty($rh['kontrak_id'])): ?>
                                        <a href="<?= base_url('kontrak/detail/'.(int)$rh['kontrak_id']) ?>" class="font-monospace text-decoration-none fw-bold">
                                            <?= esc($rh['contract_no'] ?: '-') ?>
                                        </a>
                                        <?php else: ?>
                                        <span class="font-monospace"><?= esc($rh['contract_no'] ?: '-') ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($rh['customer'] ?? '-') ?></td>
                                    <td>
                                        <?= esc($rh['location'] ?? '-') ?>
                                        <?php if(!empty($rh['location_city'])): ?>
                                        <br><span class="text-muted" style="font-size:.75rem"><?= esc($rh['location_city']) ?></span>
                                        <?php endif; ?>
                                        <?php if(!empty($rh['is_spare'])): ?>
                                        <br><span class="badge badge-soft-orange" style="font-size:.65rem">Spare</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge <?= $rhType ?>"><?= esc($rh['rental_type'] ?? '-') ?></span></td>
                                    <td><?= !empty($rh['start_date']) ? date('d M Y', strtotime($rh['start_date'])) : '-' ?></td>
                                    <td><?= !empty($rh['end_date']) ? date('d M Y', strtotime($rh['end_date'])) : '<em class="text-muted">Open</em>' ?></td>
                                    <td><span class="badge <?= $rhCls ?>"><?= esc($rh['status']) ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- ── Aktivitas (Unified Timeline) ── -->
                    <div class="tab-pane fade" id="pane-aktivitas" role="tabpanel">
                        <div class="card border-0 mb-0">
                            <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <h6 class="mb-0"><i class="fas fa-stream me-2"></i><strong>Timeline Aktivitas</strong></h6>
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <select id="filter-aktivitas" class="form-select form-select-sm" style="min-width:140px;">
                                        <option value="all">Semua</option>
                                        <option value="REGISTRATION">Registrasi</option>
                                        <option value="SPK">SPK</option>
                                        <option value="MOVEMENT">Movement</option>
                                        <option value="DELIVERY">DI</option>
                                        <option value="CONTRACT">Kontrak</option>
                                        <option value="SERVICE">Work Order</option>
                                        <option value="VERIFICATION">Verifikasi</option>
                                        <option value="COMPONENT">Komponen</option>
                                        <option value="SPAREPART">Sparepart</option>
                                        <option value="STATUS">Status</option>
                                    </select>
                                    <select id="group-aktivitas" class="form-select form-select-sm" style="min-width:160px;">
                                        <option value="document">Group: Dokumen</option>
                                        <option value="date">Group: Tanggal</option>
                                    </select>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-refresh-aktivitas" title="Refresh">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="aktivitas-loader" class="text-center py-5" style="display:none;">
                                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                                    <p class="text-muted mt-2 small">Loading aktivitas...</p>
                                </div>
                                <div id="aktivitas-empty" class="text-center py-5" style="display:none;">
                                    <i class="fas fa-stream fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">Tidak ada aktivitas tercatat.</p>
                                </div>
                                <div id="aktivitas-timeline" class="ps-1" style="display:none;"></div>
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
            <div class="card-header bg-light d-flex align-items-center justify-content-between">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i><strong>Unit Info</strong></h6>
                <a href="<?= base_url('warehouse/inventory/unit/'.$unit['id_inventory_unit'].'/print') ?>" target="_blank" class="btn btn-sm btn-outline-secondary py-0 px-2 text-muted" title="Print Unit Info"><i class="fas fa-print"></i></a>
            </div>
            <div class="card-body p-3">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">ID Unit</dt>
                    <dd class="col-7 fw-bold"><?= esc($unitNo) ?></dd>

                    <dt class="col-5 text-muted">System ID</dt>
                    <dd class="col-7 font-monospace">INT-<?= esc($unit['id_inventory_unit']) ?></dd>

                    <dt class="col-5 text-muted">Status</dt>
                    <dd class="col-7">
                        <span class="badge bg-<?= $badgeClass ?>"><?= esc($unit['status_unit_name'] ?? 'Unknown') ?></span>
                    </dd>

                    <dt class="col-5 text-muted">Asset Status</dt>
                    <dd class="col-7">
                        <?php $assetStat = strtolower($unit['status_aset'] ?? ''); ?>
                        <span class="badge <?= $assetStat === 'active' ? 'bg-primary' : 'bg-secondary' ?>"><?= esc($unit['status_aset'] ?? 'Inactive') ?></span>
                    </dd>

                    <div class="col-12 my-2"><hr class="m-0 text-muted opacity-25"></div>

                    <dt class="col-5 text-muted">Brand</dt>
                    <dd class="col-7 fw-bold"><?= esc($brand ?: '—') ?></dd>

                    <dt class="col-5 text-muted">Model</dt>
                    <dd class="col-7 fw-bold"><?= esc($model ?: '—') ?></dd>
                </dl>
            </div>
        </div>

        <?php if(!empty($public_view_url)): ?>
        <?php
        $barcodeImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=' . rawurlencode($public_view_url);
        $companyLogoUrl = base_url('assets/images/company-logo.svg');
        $barcodeLabelPayload = [
            'unitNo' => (string)($unitNo ?? ''),
            'serialNumber' => (string)($unit['serial_number'] ?? ''),
            'brand' => (string)($brand ?? ''),
            'model' => (string)($model ?? ''),
            'type' => (string)($unit['nama_tipe_unit'] ?? ''),
            'capacity' => (string)($unit['kapasitas_display'] ?? ''),
            'publicUrl' => (string)$public_view_url,
            'qrUrl' => (string)('https://api.qrserver.com/v1/create-qr-code/?size=320x320&data=' . rawurlencode($public_view_url)),
            'logoUrl' => (string)$companyLogoUrl,
        ];
        ?>
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light d-flex align-items-center justify-content-between">
                <h6 class="mb-0"><i class="fas fa-qrcode me-2"></i><strong>Barcode Unit</strong></h6>
                <span class="badge bg-dark">Public</span>
            </div>
            <div class="card-body p-3 small">
                <div class="text-center border rounded p-2">
                    <img
                        src="<?= esc($barcodeImageUrl) ?>"
                        alt="QR public unit view"
                        style="width:160px;height:160px;"
                    >
                    <div class="mt-2">
                        <a href="<?= esc($public_view_url) ?>" target="_blank" class="btn btn-sm btn-dark me-1">
                            <i class="fas fa-link me-1"></i>Link
                        </a>
                        <button type="button" class="btn btn-sm btn-primary" onclick='downloadUnitBarcodeLabel(<?= json_encode($barcodeLabelPayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>)'>
                            <i class="fas fa-download me-1"></i>Download Barcode Label
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php
        $siloStatus = strtoupper((string)($silo['status'] ?? 'BELUM_ADA'));
        $siloBadgeClass = 'bg-secondary';
        if ($siloStatus === 'SILO_TERBIT') $siloBadgeClass = 'bg-success';
        elseif ($siloStatus === 'SILO_EXPIRED') $siloBadgeClass = 'bg-danger';
        elseif ($siloStatus !== 'BELUM_ADA') $siloBadgeClass = 'bg-warning text-dark';

        $siloId = (int)($silo['id_silo'] ?? 0);
        $hasSiloFile = !empty($silo['file_silo']);
        $siloDetailUrl = $siloId > 0 ? base_url('perizinan/silo') : base_url('perizinan/silo');
        $siloDownloadUrl = $siloId > 0 ? base_url('perizinan/download-file/' . $siloId . '/silo') : null;
        ?>
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light d-flex align-items-center justify-content-between">
                <h6 class="mb-0"><i class="fas fa-certificate me-2"></i><strong>SILO</strong></h6>
                <span class="badge <?= $siloBadgeClass ?>"><?= esc($siloStatus) ?></span>
            </div>
            <div class="card-body p-3 small">
                <div class="mb-1"><span class="text-muted">Nomor SILO:</span> <span class="fw-semibold"><?= esc($silo['nomor_silo'] ?? '-') ?></span></div>
                <div class="mb-1"><span class="text-muted">Terbit:</span> <?= !empty($silo['tanggal_terbit_silo']) ? date('d M Y', strtotime($silo['tanggal_terbit_silo'])) : '-' ?></div>
                <div class="mb-2"><span class="text-muted">Expired:</span> <?= !empty($silo['tanggal_expired_silo']) ? date('d M Y', strtotime($silo['tanggal_expired_silo'])) : '-' ?></div>
                <div class="d-flex gap-2">
                    <a href="<?= esc($siloDetailUrl) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt me-1"></i>Detail SILO
                    </a>
                    <?php if($siloDownloadUrl && $hasSiloFile): ?>
                        <a href="<?= esc($siloDownloadUrl) ?>" target="_blank" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-download me-1"></i>Download SILO
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Active Contract Sidebar Card -->
        <?php if(!empty($unit['no_kontrak'])): ?>
        <div class="card shadow-sm mb-3 border-primary border-opacity-25">
            <div class="card-header d-flex align-items-center justify-content-between" style="background:rgba(13,110,253,.06)">
                <h6 class="mb-0 text-primary small"><i class="fas fa-file-contract me-2"></i><strong>Kontrak Aktif</strong></h6>
                <a href="<?= base_url('kontrak/detail/'.(int)$unit['kontrak_id']) ?>" class="btn btn-sm btn-outline-primary py-0 px-2" title="Lihat Kontrak">
                    <i class="fas fa-external-link-alt small"></i>
                </a>
            </div>
            <div class="card-body p-3">
                <p class="fw-bold font-monospace text-primary mb-1 small"><?= esc($unit['no_kontrak']) ?></p>
                <p class="text-dark mb-1 small"><i class="fas fa-building text-muted me-1"></i><?= esc($unit['customer_name'] ?? '-') ?></p>
                <?php
                $sbEndDate  = $unit['kontrak_end_date'] ?? $unit['ku_end_date'] ?? null;
                $sbDaysLeft = null;
                $sbCls = 'badge-soft-green';
                if($sbEndDate) {
                    $sbDaysLeft = (int)floor((strtotime($sbEndDate) - time()) / 86400);
                    $sbCls = $sbDaysLeft < 0 ? 'badge-soft-red' : ($sbDaysLeft <= 30 ? 'badge-soft-orange' : 'badge-soft-green');
                }
                ?>
                <?php if($sbEndDate): ?>
                <div class="mt-2">
                    <span class="badge <?= $sbCls ?> w-100 d-block text-center py-1">
                        <?php if($sbDaysLeft < 0): ?>
                        <i class="fas fa-exclamation-circle me-1"></i>Expired <?= abs($sbDaysLeft) ?> hari lalu
                        <?php elseif($sbDaysLeft <= 30): ?>
                        <i class="fas fa-clock me-1"></i>Berakhir <?= $sbDaysLeft ?> hari lagi
                        <?php else: ?>
                        <i class="fas fa-check-circle me-1"></i>Aktif — <?= $sbDaysLeft ?> hari lagi
                        <?php endif; ?>
                    </span>
                    <p class="text-muted small mt-1 mb-0 text-center"><?= date('d M Y', strtotime($sbEndDate)) ?></p>
                </div>
                <?php else: ?>
                <span class="badge badge-soft-cyan mt-1">Open Ended</span>
                <?php endif; ?>
                <?php if(!empty($unit['ku_is_spare'])): ?>
                <span class="badge badge-soft-orange mt-1"><i class="fas fa-star me-1"></i>Unit Spare</span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- History Timeline Mini Card -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-history me-2"></i><strong>System Timeline</strong></h6>
            </div>
            <div class="card-body p-2" style="max-height: 400px; overflow-y: auto;">
                <div class="text-center py-4" id="history-loader" style="display:none;">
                    <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
                </div>
                <div id="history-timeline-container" class="small px-2">
                    <div class="text-center text-muted py-3 fst-italic">Loading events...</div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     MODAL: BOOK UNIT
══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modal-booking" tabindex="-1" aria-labelledby="modalBookingLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalBookingLabel"><i class="fas fa-bookmark me-2"></i>Booking Unit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    Pilih sumber customer untuk booking unit <strong><?= esc($unitNo) ?></strong>.
                </p>
                <!-- Source tabs -->
                <ul class="nav nav-pills mb-3 nav-fill" id="booking-source-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="btab-customer" data-bs-toggle="pill" data-bs-target="#booking-pane-customer" type="button">
                            <i class="fas fa-building me-1"></i>Customer
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="btab-quotation" data-bs-toggle="pill" data-bs-target="#booking-pane-quotation" type="button">
                            <i class="fas fa-file-alt me-1"></i>Quotation
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="btab-manual" data-bs-toggle="pill" data-bs-target="#booking-pane-manual" type="button">
                            <i class="fas fa-keyboard me-1"></i>Manual
                        </button>
                    </li>
                </ul>
                <div class="tab-content mb-3">
                    <!-- Customer pane -->
                    <div class="tab-pane fade show active" id="booking-pane-customer" role="tabpanel">
                        <div class="mb-2">
                            <input type="text" id="booking-customer-search" class="form-control form-control-sm" placeholder="Cari nama atau kode customer...">
                        </div>
                        <div id="booking-customer-list" style="max-height:200px;overflow-y:auto;" class="border rounded">
                            <div class="text-center text-muted py-3 small"><i class="fas fa-search me-1"></i>Ketik untuk mencari customer</div>
                        </div>
                        <input type="hidden" id="booking-customer-id">
                        <div id="booking-customer-selected" class="mt-2 d-none">
                            <span class="badge badge-soft-blue small" id="booking-customer-selected-label"></span>
                            <button type="button" class="btn btn-link btn-sm p-0 ms-1 text-danger" onclick="clearBookingCustomer()"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <!-- Quotation pane -->
                    <div class="tab-pane fade" id="booking-pane-quotation" role="tabpanel">
                        <div class="mb-2">
                            <input type="text" id="booking-quotation-search" class="form-control form-control-sm" placeholder="Cari nama prospect atau nomor quotation...">
                        </div>
                        <div id="booking-quotation-list" style="max-height:200px;overflow-y:auto;" class="border rounded">
                            <div class="text-center text-muted py-3 small"><i class="fas fa-search me-1"></i>Ketik untuk mencari quotation</div>
                        </div>
                        <input type="hidden" id="booking-quotation-id">
                        <div id="booking-quotation-selected" class="mt-2 d-none">
                            <span class="badge badge-soft-cyan small" id="booking-quotation-selected-label"></span>
                            <button type="button" class="btn btn-link btn-sm p-0 ms-1 text-danger" onclick="clearBookingQuotation()"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <!-- Manual pane -->
                    <div class="tab-pane fade" id="booking-pane-manual" role="tabpanel">
                        <input type="text" id="booking-manual-name" class="form-control form-control-sm mt-1"
                               placeholder="Nama customer / prospect (belum terdaftar)">
                        <div class="form-text">Gunakan ini untuk customer yang belum ditambahkan ke sistem.</div>
                    </div>
                </div>
                <!-- Notes -->
                <div class="mb-0">
                    <label class="form-label small fw-semibold">Catatan <span class="text-muted fw-normal">(opsional)</span></label>
                    <textarea id="booking-notes" class="form-control form-control-sm" rows="2" placeholder="Keterangan tambahan..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success btn-sm" id="btn-submit-booking" onclick="submitBooking()">
                    <i class="fas fa-bookmark me-1"></i>Konfirmasi Booking
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     MODAL: SCRAB UNIT
══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modal-scrap" tabindex="-1" aria-labelledby="modalScrapLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalScrapLabel"><i class="fas fa-trash-alt me-2"></i>Scrab Unit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger border-danger d-flex gap-2 align-items-start py-2 mb-3">
                    <i class="fas fa-exclamation-triangle mt-1 flex-shrink-0"></i>
                    <div class="small">
                        <strong>Perhatian!</strong> Status unit akan berubah ke <strong>SOLD</strong>.
                        Tindakan ini <u>tidak dapat dibatalkan</u>.
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Alasan Scrab <span class="text-danger">*</span></label>
                    <textarea id="scrap-reason" class="form-control form-control-sm" rows="3"
                              placeholder="Jelaskan kondisi unit dan alasan di-scrab..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Estimasi Nilai Jual <span class="text-muted fw-normal">(opsional, IDR)</span></label>
                    <input type="number" id="scrap-estimated-value" class="form-control form-control-sm"
                           placeholder="0" min="0" step="1000">
                </div>
                <div class="mb-0">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="scrap-confirm-check"
                               onchange="document.getElementById('btn-submit-scrap').disabled = !this.checked;">
                        <label class="form-check-label small" for="scrap-confirm-check">
                            Saya konfirmasi unit <strong><?= esc($unitNo) ?></strong> siap untuk di-scrab
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="btn-submit-scrap" disabled onclick="submitScrap()">
                    <i class="fas fa-trash-alt me-1"></i>Scrab Unit
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     MODAL: CHANGE STATUS
══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modal-change-status" tabindex="-1" aria-labelledby="modalChangeStatusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="modalChangeStatusLabel"><i class="fas fa-exchange-alt me-2"></i>Ubah Status Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 d-flex align-items-center gap-2">
                    <span class="small text-muted">Status saat ini:</span>
                    <span class="badge bg-<?= $badgeClass ?>"><?= esc($unit['status_unit_name'] ?? 'Unknown') ?></span>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Status Baru <span class="text-danger">*</span></label>
                    <select id="change-status-new" class="form-select form-select-sm">
                        <option value="">-- Pilih Status --</option>
                        <?php
                        $changeStatusOptions = [
                            1  => 'AVAILABLE STOCK',
                            3  => 'BOOKED',
                            12 => 'RETURNED',
                        ];
                        foreach($changeStatusOptions as $optId => $optLabel):
                            if($optId === $statusId) continue;
                        ?>
                        <option value="<?= $optId ?>"><?= $optLabel ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-semibold">Alasan <span class="text-danger">*</span></label>
                    <textarea id="change-status-reason" class="form-control form-control-sm" rows="2"
                              placeholder="Jelaskan alasan perubahan status..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning btn-sm" onclick="submitChangeStatus()">
                    <i class="fas fa-save me-1"></i>Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
(function(){
    var unitId = <?= (int)($unit['id_inventory_unit'] ?? 0) ?>;
    var baseUnitUrl = <?= json_encode(base_url('warehouse/inventory/unit/')) ?>;
    window._unitId = unitId;
    window._baseUnitUrl = baseUnitUrl;
    if (window.location.hash) { try { window.history.replaceState(null, '', window.location.pathname + window.location.search); } catch(e){} }
    function forceOverviewTab() {
        var t = document.getElementById('tab-overview');
        var p = document.getElementById('pane-overview');
        if (!t || !p) return;
        var container = t.closest('.card');
        if (container) {
            container.querySelectorAll('.nav-link').forEach(function(el){ el.classList.remove('active'); });
            container.querySelectorAll('.tab-pane').forEach(function(el){ el.classList.remove('show','active'); });
        }
        t.classList.add('active');
        p.classList.add('show','active');
        try { if (window.bootstrap && bootstrap.Tab) bootstrap.Tab.getOrCreateInstance(t).show(); } catch(e){}
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function(){ forceOverviewTab(); setTimeout(forceOverviewTab, 100); });
    } else {
        forceOverviewTab();
        setTimeout(forceOverviewTab, 100);
    }
    window.addEventListener('load', function(){ setTimeout(forceOverviewTab, 0); });
    $(document).ready(function(){
        forceOverviewTab();
        fetchUnitHistory(unitId);
    });
})();
    function fetchUnitHistory(uid) {
        var baseUnitUrl = window._baseUnitUrl || '';
        $('#history-timeline-container').hide();
        $('#history-loader').show();
        $.ajax({
            url: baseUnitUrl + uid + '/timeline',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#history-loader').hide();
                    $('#history-timeline-container').html(response.html).fadeIn();
                } else {
                    $('#history-loader').hide();
                    $('#history-timeline-container').html(`<div class="alert alert-light border small text-center text-muted py-2">No history events found.</div>`).show();
                }
            },
            error: function() {
                $('#history-loader').hide();
                $('#history-timeline-container').html(`<div class="alert alert-danger small text-center py-2"><i class="fas fa-wifi"></i> Network error.</div>`).show();
            }
        });
    }

    var UNIT_ID = window._unitId;
    var _aktivitasLoaded = false;

    $('#tab-aktivitas').on('shown.bs.tab', function () {
        loadAktivitas(UNIT_ID);
    });

    $('#filter-aktivitas').on('change', function () {
        _aktivitasLoaded = false;
        loadAktivitas(UNIT_ID);
    });

    $('#btn-refresh-aktivitas').on('click', function () {
        _aktivitasLoaded = false;
        loadAktivitas(UNIT_ID);
    });
    $('#group-aktivitas').on('change', function () {
        _aktivitasLoaded = false;
        loadAktivitas(UNIT_ID);
    });

    function loadAktivitas(uid) {
        var baseUnitUrl = window._baseUnitUrl || '';
        var category = $('#filter-aktivitas').val() || 'all';
        $('#aktivitas-loader').show();
        $('#aktivitas-empty').hide();
        $('#aktivitas-timeline').hide().empty();

        $.ajax({
            url: baseUnitUrl + uid + '/activity',
            type: 'GET',
            data: { category: category },
            dataType: 'json',
            success: function (res) {
                $('#aktivitas-loader').hide();
                if (!res.success || !res.events || res.events.length === 0) {
                    $('#aktivitas-empty').show();
                    return;
                }
                _aktivitasLoaded = true;

                const iconMap = {
                    'box': 'fa-box',
                    'clipboard-list': 'fa-clipboard-list',
                    'truck': 'fa-truck',
                    'shipping-fast': 'fa-shipping-fast',
                    'file-contract': 'fa-file-contract',
                    'tools': 'fa-tools',
                    'check-circle': 'fa-check-circle',
                    'puzzle-piece': 'fa-puzzle-piece',
                    'wrench': 'fa-wrench',
                    'sync-alt': 'fa-sync-alt',
                    // Used by UnitActivityService for KANIBAL / sparepart events
                    'exchange-alt': 'fa-exchange-alt',
                    'toolbox': 'fa-toolbox'
                };

                var refLabels = {
                    'spk': 'No. SPK',
                    'di': 'No. DI',
                    'delivery': 'No. DI',
                    'work_order': 'No. WO',
                    'wo': 'No. WO',
                    'contract': 'No. Kontrak',
                    'movement': 'No. Surat Jalan',
                    'verification': 'WO/Verifikasi',
                    'component': 'Komponen'
                };
                function esc(s) { if (s == null || s === undefined) return ''; return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
                function renderEvent(ev, idx, total) {
                    var dateStr = ev.date ? new Date(ev.date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '—';
                    var icon = iconMap[ev.icon] || 'fa-circle';
                    var color = ev.color || 'secondary';
                    var isLast = idx === total - 1;
                    var url = (ev.meta && ev.meta.url) ? ev.meta.url : null;
                    var refNum = ev.reference_number || ev.description || '';
                    var refNumDisplay = refNum || '-';
                    var refType = (ev.reference_type || '').toLowerCase();
                    if (!refType) {
                        var cat = (ev.category || '').toUpperCase();
                        if (cat === 'SPK') refType = 'spk';
                        else if (cat === 'DELIVERY') refType = 'di';
                        else if (cat === 'SERVICE') refType = 'work_order';
                        else if (cat === 'CONTRACT') refType = 'contract';
                        else if (cat === 'MOVEMENT') refType = 'movement';
                    }
                    var refLabel = refLabels[refType] || 'Referensi';
                    var refLine = url
                        ? '<span class="text-muted small">' + esc(refLabel) + ': </span><a href="' + esc(url) + '" class="text-primary small fw-semibold">' + esc(refNumDisplay) + '</a>'
                        : '<span class="text-muted small">' + esc(refLabel) + ': </span><span class="font-monospace small">' + esc(refNumDisplay) + '</span>';
                    var detailLine = ev.detail ? '<div class="text-muted small mt-1">' + esc(ev.detail) + '</div>' : '';
                    var descLine = (ev.description && ev.description !== refNum) ? '<div class="text-muted small">' + esc(ev.description) + '</div>' : '';

                    return '<div class="d-flex gap-3 mb-0">' +
                        '<div class="flex-shrink-0 text-center" style="width:42px;">' +
                        '<div class="rounded-circle bg-' + color + ' text-white d-flex align-items-center justify-content-center mx-auto mb-0" style="width:36px;height:36px;"><i class="fas ' + icon + ' small"></i></div>' +
                        (!isLast ? '<div class="border-start border-2 border-secondary mx-auto" style="width:2px;min-height:40px;opacity:.25;"></div>' : '') +
                        '</div>' +
                        '<div class="flex-grow-1 pb-4 ' + (!isLast ? 'border-bottom' : '') + '">' +
                        '<div class="d-flex justify-content-between align-items-start flex-wrap gap-1">' +
                        '<div class="min-w-0">' +
                        '<div class="fw-semibold text-dark">' + esc(ev.title || 'Event') + '</div>' +
                        (refLine ? '<div class="mt-1">' + refLine + '</div>' : '') +
                        descLine +
                        detailLine +
                        '</div>' +
                        '<div class="text-end flex-shrink-0">' +
                        '<div class="small text-muted mb-1">' + esc(dateStr) + '</div>' +
                        '<span class="badge bg-' + color + '">' + esc(ev.category || '') + '</span>' +
                        '</div></div></div></div>';
                }

                function docGroupKey(ev) {
                    var t = (ev.reference_type || ev.category || 'misc').toLowerCase();
                    var n = (ev.reference_number || ev.description || 'no-ref').toString();
                    return t + '|' + n;
                }

                function docGroupTitle(ev) {
                    var t = (ev.reference_type || ev.category || 'Dokumen').toString().toUpperCase();
                    var n = (ev.reference_number || ev.description || '-').toString();
                    return t + ' - ' + n;
                }

                function dateGroupKey(ev) {
                    if (!ev.date) return 'Tanpa tanggal';
                    var d = new Date(ev.date);
                    if (isNaN(d.getTime())) return 'Tanpa tanggal';
                    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                }

                var mode = $('#group-aktivitas').val() || 'document';
                var grouped = {};
                var order = [];
                res.events.forEach(function(ev) {
                    var key = mode === 'date' ? dateGroupKey(ev) : docGroupKey(ev);
                    if (!grouped[key]) {
                        grouped[key] = [];
                        order.push(key);
                    }
                    grouped[key].push(ev);
                });

                var html = '';
                order.forEach(function(key) {
                    var items = grouped[key];
                    var groupTitle = mode === 'date' ? key : docGroupTitle(items[0]);
                    html += '<div class="border rounded mb-3 overflow-hidden">';
                    html += '<div class="bg-light px-3 py-2 d-flex justify-content-between align-items-center">';
                    html += '<strong class="small">' + esc(groupTitle) + '</strong>';
                    html += '<span class="badge bg-secondary">' + items.length + ' item</span>';
                    html += '</div>';
                    html += '<div class="p-3">';
                    items.forEach(function(ev, idx) {
                        html += renderEvent(ev, idx, items.length);
                    });
                    html += '</div></div>';
                });

                $('#aktivitas-timeline').html(html).show();
            },
            error: function () {
                $('#aktivitas-loader').hide();
                $('#aktivitas-timeline').html('<div class="alert alert-danger small">Gagal memuat aktivitas.</div>').show();
            }
        });
    }

    // ── INLINE EDIT SPECIFICATIONS ─────────────────────────────────────
    
    function toggleSpecEdit(showEdit) {
        if (showEdit) {
            $('#card-specs .spec-view').addClass('d-none');
            $('#card-specs .spec-edit').removeClass('d-none');
            $('#btnEditSpecs').addClass('d-none');
            $('#btnSaveSpecs, #btnCancelSpecs').removeClass('d-none');
        } else {
            $('#card-specs .spec-view').removeClass('d-none');
            $('#card-specs .spec-edit').addClass('d-none');
            $('#btnEditSpecs').removeClass('d-none');
            $('#btnSaveSpecs, #btnCancelSpecs').addClass('d-none');
        }
    }

    function saveSpecsInline() {
        $('#btnSaveSpecs').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>').addClass('disabled');

        let data = {
            <?= json_encode(csrf_token()) ?>: $(document.querySelector('meta[name=' + <?= json_encode(csrf_token()) ?> + ']')).attr('content'),
            model_mesin_id   : $('select[name="model_mesin_id"]').val(),
            sn_mesin         : $('input[name="sn_mesin"]').val(),
            kapasitas_unit_id: $('select[name="kapasitas_unit_id"]').val(),
            model_mast_id    : $('select[name="model_mast_id"]').val(),
            sn_mast          : $('input[name="sn_mast"]').val(),
            tinggi_mast      : $('input[name="tinggi_mast"]').val(),
            fuel_type        : $('select[name="fuel_type"]').val(),
            ownership_status : $('select[name="ownership_status"]').val(),
            roda_id          : $('select[name="roda_id"]').val(),
            valve_id         : $('select[name="valve_id"]').val(),
            hour_meter       : $('input[name="hour_meter"]').val(),
        };

        // Handle ban_id (select or text input)
        let banInput = $('select[name="ban_id"]').length ? $('select[name="ban_id"]') : $('input[name="ban_id"]');
        if (banInput.length) data.ban_id = banInput.val();

        $.ajax({
            url: (window._baseUnitUrl || '') + (window._unitId || '') + '/inline-update',
            type: 'POST',
            data: data,
            success: function(res) {
                $('#btnSaveSpecs').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save').removeClass('disabled');

                if (res.csrf_hash) $(document.querySelector('meta[name=' + <?= json_encode(csrf_token()) ?> + ']')).attr('content', res.csrf_hash);

                if (res.success) {
                    OptimaNotify.success(res.message, 'Saved');

                    // Refresh view spans
                    $('#view-mesin').text($('select[name="model_mesin_id"] option:selected').text().trim() || '-');
                    $('#view-sn-mesin').text($('input[name="sn_mesin"]').val() || '-');
                    $('#view-kapasitas').text($('select[name="kapasitas_unit_id"] option:selected').text().trim() || '-');
                    $('#view-mast').text($('select[name="model_mast_id"] option:selected').text().trim() || '-');
                    $('#view-sn-mast').text($('input[name="sn_mast"]').val() || '-');
                    let banText = $('select[name="ban_id"]').length ? $('select[name="ban_id"] option:selected').text().trim() : $('input[name="ban_id"]').val();
                    $('#view-ban').text(banText || '-');

                    let mh = $('input[name="tinggi_mast"]').val();
                    $('#view-mast-height').text(mh ? mh + ' mm' : '-');

                    let ft = $('select[name="fuel_type"] option:selected').val();
                    $('#view-fuel').html(ft ? '<span class="badge bg-warning text-white">' + ft + '</span>' : '<span class="text-muted">-</span>');

                    let os = $('select[name="ownership_status"] option:selected').val();
                    let osCls = {OWNED:'success', LEASED:'warning', CONSIGNMENT:'info'}[os] || 'secondary';
                    $('#view-ownership').html(os ? '<span class="badge bg-' + osCls + '">' + os + '</span>' : '<span class="text-muted">-</span>');

                    let rd = $('select[name="roda_id"] option:selected').text().trim();
                    $('#view-roda').text(rd || '-');

                    let vl = $('select[name="valve_id"] option:selected').text().trim();
                    $('#view-valve').text(vl || '-');

                    let hm = $('input[name="hour_meter"]').val();
                    if (hm) {
                        let hmNum = parseFloat(hm);
                        $('#view-hour-meter').text(!isNaN(hmNum) ? hmNum.toLocaleString('id-ID') + ' HM' : hm + ' HM');
                    } else {
                        $('#view-hour-meter').text('-');
                    }

                    toggleSpecEdit(false);
                } else {
                    OptimaNotify.error(res.message);
                }
            },
            error: function() {
                $('#btnSaveSpecs').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save').removeClass('disabled');
                OptimaNotify.error('Failed to connect to server.');
            }
        });
    }

    // ── INLINE EDIT CATATAN ─────────────────────────────────────

    function toggleCatatanEdit(showEdit) {
        if (showEdit) {
            $('#view-catatan').addClass('d-none');
            $('#edit-catatan').removeClass('d-none');
            $('#btnEditCatatan').addClass('d-none');
            $('#btnSaveCatatan, #btnCancelCatatan').removeClass('d-none');
        } else {
            $('#view-catatan').removeClass('d-none');
            $('#edit-catatan').addClass('d-none');
            $('#btnEditCatatan').removeClass('d-none');
            $('#btnSaveCatatan, #btnCancelCatatan').addClass('d-none');
        }
    }

    function saveCatatanInline() {
        $('#btnSaveCatatan').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>').addClass('disabled');

        let catatan = $('#inputCatatan').val();

        let data = {
            <?= json_encode(csrf_token()) ?>: $(document.querySelector('meta[name=' + <?= json_encode(csrf_token()) ?> + ']')).attr('content'),
            keterangan: catatan
        };

        $.ajax({
            url: (window._baseUnitUrl || '') + (window._unitId || '') + '/inline-update',
            type: 'POST',
            data: data,
            success: function(res) {
                $('#btnSaveCatatan').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save').removeClass('disabled');

                if (res.csrf_hash) $(document.querySelector('meta[name=' + <?= json_encode(csrf_token()) ?> + ']')).attr('content', res.csrf_hash);

                if (res.success) {
                    OptimaNotify.success(res.message, 'Saved');

                    let viewHtml = catatan ? catatan.replace(/\n/g, "<br>") : '<em class="text-muted">No notes yet.</em>';
                    $('#view-catatan').html(viewHtml);

                    toggleCatatanEdit(false);
                } else {
                    OptimaNotify.error(res.message);
                }
            },
            error: function() {
                $('#btnSaveCatatan').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save').removeClass('disabled');
                OptimaNotify.error('Failed to connect to server.');
            }
        });
    }

    // ── BOOKING MODAL ─────────────────────────────────────────────────────

    var _bookingSearchTimer = null;
    var _quotationSearchTimer = null;
    var _csrfNameKey = <?= json_encode(csrf_token()) ?>;

    function getCsrfValue() {
        var meta = document.querySelector('meta[name="' + _csrfNameKey + '"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function openBookingModal() {
        document.getElementById('booking-customer-id').value = '';
        document.getElementById('booking-quotation-id').value = '';
        document.getElementById('booking-manual-name').value = '';
        document.getElementById('booking-notes').value = '';
        document.getElementById('booking-customer-search').value = '';
        document.getElementById('booking-quotation-search').value = '';
        document.getElementById('booking-customer-selected').classList.add('d-none');
        document.getElementById('booking-quotation-selected').classList.add('d-none');
        document.getElementById('booking-customer-list').innerHTML = '<div class="text-center text-muted py-3 small"><i class="fas fa-search me-1"></i>Ketik untuk mencari customer</div>';
        document.getElementById('booking-quotation-list').innerHTML = '<div class="text-center text-muted py-3 small"><i class="fas fa-search me-1"></i>Ketik untuk mencari quotation</div>';
        var modal = new bootstrap.Modal(document.getElementById('modal-booking'));
        modal.show();
    }

    // Live search: customers
    document.getElementById('booking-customer-search').addEventListener('input', function() {
        clearTimeout(_bookingSearchTimer);
        var q = this.value.trim();
        _bookingSearchTimer = setTimeout(function() { searchBookingCustomers(q); }, 300);
    });

    function searchBookingCustomers(q) {
        var list = document.getElementById('booking-customer-list');
        list.innerHTML = '<div class="text-center py-2 small text-muted"><i class="fas fa-spinner fa-spin me-1"></i>Mencari...</div>';
        $.ajax({
            url: <?= json_encode(base_url('warehouse/inventory/unit/api/customers')) ?>,
            type: 'GET',
            data: { q: q },
            success: function(res) {
                if (!res.success || !res.data || res.data.length === 0) {
                    list.innerHTML = '<div class="text-center text-muted py-3 small">Tidak ada customer ditemukan.</div>';
                    return;
                }
                var html = '';
                res.data.forEach(function(c) {
                    html += '<div class="px-3 py-2 border-bottom booking-customer-item" style="cursor:pointer;" '
                        + 'onclick="selectBookingCustomer(' + c.id + ', \'' + c.customer_name.replace(/'/g,"\\'")+'\', \'' + (c.customer_code||'').replace(/'/g,"\\'")+'\')"">'
                        + '<strong class="small">' + c.customer_name + '</strong>'
                        + '<span class="badge badge-soft-blue ms-2 small">' + (c.customer_code||'') + '</span>'
                        + '</div>';
                });
                list.innerHTML = html;
            },
            error: function() {
                list.innerHTML = '<div class="text-center text-danger py-2 small">Gagal memuat data.</div>';
            }
        });
    }

    function selectBookingCustomer(id, name, code) {
        document.getElementById('booking-customer-id').value = id;
        document.getElementById('booking-customer-selected-label').textContent = name + (code ? ' (' + code + ')' : '');
        document.getElementById('booking-customer-selected').classList.remove('d-none');
        document.getElementById('booking-customer-list').innerHTML = '';
        document.getElementById('booking-customer-search').value = '';
    }

    function clearBookingCustomer() {
        document.getElementById('booking-customer-id').value = '';
        document.getElementById('booking-customer-selected').classList.add('d-none');
        document.getElementById('booking-customer-list').innerHTML = '<div class="text-center text-muted py-3 small"><i class="fas fa-search me-1"></i>Ketik untuk mencari customer</div>';
    }

    // Live search: quotations
    document.getElementById('booking-quotation-search').addEventListener('input', function() {
        clearTimeout(_quotationSearchTimer);
        var q = this.value.trim();
        _quotationSearchTimer = setTimeout(function() { searchBookingQuotations(q); }, 300);
    });

    function searchBookingQuotations(q) {
        var list = document.getElementById('booking-quotation-list');
        list.innerHTML = '<div class="text-center py-2 small text-muted"><i class="fas fa-spinner fa-spin me-1"></i>Mencari...</div>';
        $.ajax({
            url: <?= json_encode(base_url('warehouse/inventory/unit/api/quotations')) ?>,
            type: 'GET',
            data: { q: q },
            success: function(res) {
                if (!res.success || !res.data || res.data.length === 0) {
                    list.innerHTML = '<div class="text-center text-muted py-3 small">Tidak ada quotation ditemukan.</div>';
                    return;
                }
                var stageBadge = { DRAFT: 'badge-soft-yellow', SENT: 'badge-soft-cyan', ACCEPTED: 'badge-soft-green' };
                var html = '';
                res.data.forEach(function(q) {
                    var cls = stageBadge[q.stage] || 'badge-soft-gray';
                    html += '<div class="px-3 py-2 border-bottom" style="cursor:pointer;" '
                        + 'onclick="selectBookingQuotation(' + q.id_quotation + ', \'' + q.quotation_number.replace(/'/g,"\\'")+'\', \'' + q.prospect_name.replace(/'/g,"\\'")+'\')"">'
                        + '<strong class="small">' + q.prospect_name + '</strong>'
                        + '<span class="badge ' + cls + ' ms-2 small">' + q.stage + '</span>'
                        + '<br><span class="font-monospace text-muted" style="font-size:.75rem">' + q.quotation_number + '</span>'
                        + '</div>';
                });
                list.innerHTML = html;
            },
            error: function() {
                list.innerHTML = '<div class="text-center text-danger py-2 small">Gagal memuat data.</div>';
            }
        });
    }

    function selectBookingQuotation(id, qNumber, prospect) {
        document.getElementById('booking-quotation-id').value = id;
        document.getElementById('booking-quotation-selected-label').textContent = prospect + ' — ' + qNumber;
        document.getElementById('booking-quotation-selected').classList.remove('d-none');
        document.getElementById('booking-quotation-list').innerHTML = '';
        document.getElementById('booking-quotation-search').value = '';
    }

    function clearBookingQuotation() {
        document.getElementById('booking-quotation-id').value = '';
        document.getElementById('booking-quotation-selected').classList.add('d-none');
        document.getElementById('booking-quotation-list').innerHTML = '<div class="text-center text-muted py-3 small"><i class="fas fa-search me-1"></i>Ketik untuk mencari quotation</div>';
    }

    function submitBooking() {
        // Determine active tab
        var activeTab = document.querySelector('#booking-source-tabs .nav-link.active');
        var tabTarget = activeTab ? activeTab.getAttribute('data-bs-target') : '';

        var customerId   = '';
        var quotationId  = '';
        var manualName   = '';

        if (tabTarget === '#booking-pane-customer') {
            customerId = document.getElementById('booking-customer-id').value;
            if (!customerId) { OptimaNotify.error('Pilih customer terlebih dahulu.'); return; }
        } else if (tabTarget === '#booking-pane-quotation') {
            quotationId = document.getElementById('booking-quotation-id').value;
            if (!quotationId) { OptimaNotify.error('Pilih quotation terlebih dahulu.'); return; }
        } else {
            manualName = document.getElementById('booking-manual-name').value.trim();
            if (!manualName) { OptimaNotify.error('Masukkan nama customer.'); return; }
        }

        var notes = document.getElementById('booking-notes').value.trim();
        var btn   = document.getElementById('btn-submit-booking');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memproses...';

        var postData = {};
        postData[_csrfNameKey] = getCsrfValue();
        postData.customer_id           = customerId;
        postData.quotation_id          = quotationId;
        postData.customer_name_manual  = manualName;
        postData.notes                 = notes;

        $.ajax({
            url: <?= json_encode(base_url('warehouse/inventory/unit/' . (int)($unit['id_inventory_unit'] ?? 0) . '/book')) ?>,
            type: 'POST',
            data: postData,
            success: function(res) {
                if (res.csrf_hash) $('meta[name="' + _csrfNameKey + '"]').attr('content', res.csrf_hash);
                if (res.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modal-booking')).hide();
                    OptimaNotify.success(res.message, 'Booking Berhasil');
                    setTimeout(function() { window.location.reload(); }, 1200);
                } else {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-bookmark me-1"></i>Konfirmasi Booking';
                    OptimaNotify.error(res.message);
                }
            },
            error: function() {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-bookmark me-1"></i>Konfirmasi Booking';
                OptimaNotify.error('Gagal terhubung ke server.');
            }
        });
    }

    // ── SCRAP MODAL ───────────────────────────────────────────────────────

    function openScrapModal() {
        document.getElementById('scrap-reason').value = '';
        document.getElementById('scrap-estimated-value').value = '';
        document.getElementById('scrap-confirm-check').checked = false;
        document.getElementById('btn-submit-scrap').disabled = true;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-scrap')).show();
    }

    function submitScrap() {
        var reason = document.getElementById('scrap-reason').value.trim();
        if (!reason) { OptimaNotify.error('Alasan scrab wajib diisi.'); return; }
        if (!document.getElementById('scrap-confirm-check').checked) {
            OptimaNotify.error('Centang konfirmasi terlebih dahulu.'); return;
        }

        var btn = document.getElementById('btn-submit-scrap');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memproses...';

        var postData = {};
        postData[_csrfNameKey]   = getCsrfValue();
        postData.reason           = reason;
        postData.estimated_value  = document.getElementById('scrap-estimated-value').value;

        $.ajax({
            url: <?= json_encode(base_url('warehouse/inventory/unit/' . (int)($unit['id_inventory_unit'] ?? 0) . '/scrap')) ?>,
            type: 'POST',
            data: postData,
            success: function(res) {
                if (res.csrf_hash) $('meta[name="' + _csrfNameKey + '"]').attr('content', res.csrf_hash);
                if (res.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modal-scrap')).hide();
                    OptimaNotify.success(res.message, 'Scrab Berhasil');
                    setTimeout(function() { window.location.reload(); }, 1200);
                } else {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-trash-alt me-1"></i>Scrab Unit';
                    OptimaNotify.error(res.message);
                }
            },
            error: function() {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-trash-alt me-1"></i>Scrab Unit';
                OptimaNotify.error('Gagal terhubung ke server.');
            }
        });
    }

    // ── CHANGE STATUS MODAL ───────────────────────────────────────────────

    function openChangeStatusModal() {
        document.getElementById('change-status-new').value = '';
        document.getElementById('change-status-reason').value = '';
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-change-status')).show();
    }

    function submitChangeStatus() {
        var newStatusId = document.getElementById('change-status-new').value;
        var reason      = document.getElementById('change-status-reason').value.trim();
        if (!newStatusId) { OptimaNotify.error('Pilih status baru.'); return; }
        if (!reason)      { OptimaNotify.error('Alasan perubahan wajib diisi.'); return; }

        var postData = {};
        postData[_csrfNameKey] = getCsrfValue();
        postData.new_status_id = newStatusId;
        postData.reason        = reason;

        $.ajax({
            url: <?= json_encode(base_url('warehouse/inventory/unit/' . (int)($unit['id_inventory_unit'] ?? 0) . '/change-status')) ?>,
            type: 'POST',
            data: postData,
            success: function(res) {
                if (res.csrf_hash) $('meta[name="' + _csrfNameKey + '"]').attr('content', res.csrf_hash);
                if (res.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modal-change-status')).hide();
                    OptimaNotify.success(res.message, 'Status Diubah');
                    setTimeout(function() { window.location.reload(); }, 1200);
                } else {
                    OptimaNotify.error(res.message);
                }
            },
            error: function() {
                OptimaNotify.error('Gagal terhubung ke server.');
            }
        });
    }

    async function downloadUnitBarcodeLabel(payload) {
        try {
            var unitNo = (payload && payload.unitNo) ? String(payload.unitNo) : '-';
            var serial = (payload && payload.serialNumber) ? String(payload.serialNumber) : '-';
            var brand = (payload && payload.brand) ? String(payload.brand) : '-';
            var model = (payload && payload.model) ? String(payload.model) : '-';
            var type = (payload && payload.type) ? String(payload.type) : '-';
            var capacity = (payload && payload.capacity) ? String(payload.capacity) : '-';
            var publicUrl = (payload && payload.publicUrl) ? String(payload.publicUrl) : '';
            var qrUrl = (payload && payload.qrUrl) ? String(payload.qrUrl) : '';
            var logoUrl = (payload && payload.logoUrl) ? String(payload.logoUrl) : '';
            var barcodeValue = unitNo !== '-' ? unitNo : serial;
            var barcodeUrl = 'https://bwipjs-api.metafloor.com/?bcid=code128&text=' + encodeURIComponent(barcodeValue) + '&scale=3&includetext=false&paddingwidth=0&paddingheight=0';

            async function loadImageAsObjectUrl(url) {
                if (!url) return null;
                const res = await fetch(url);
                if (!res.ok) throw new Error('Failed to load image: ' + url);
                const blob = await res.blob();
                return URL.createObjectURL(blob);
            }
            async function loadImg(url) {
                return new Promise(function(resolve, reject) {
                    var img = new Image();
                    img.onload = function() { resolve(img); };
                    img.onerror = reject;
                    img.src = url;
                });
            }

            var canvas = document.createElement('canvas');
            canvas.width = 1500;
            canvas.height = 920;
            var ctx = canvas.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.strokeStyle = '#2c2f39';
            ctx.lineWidth = 5;
            ctx.strokeRect(10, 10, canvas.width - 20, canvas.height - 20);

            // Green accent wave background
            ctx.fillStyle = '#ebf9f0';
            ctx.beginPath();
            ctx.moveTo(0, 0);
            ctx.lineTo(canvas.width, 0);
            ctx.lineTo(canvas.width, 165);
            ctx.bezierCurveTo(canvas.width * 0.68, 112, canvas.width * 0.42, 235, 0, 160);
            ctx.closePath();
            ctx.fill();

            // Secondary accent
            ctx.fillStyle = '#d7f2e1';
            ctx.beginPath();
            ctx.moveTo(0, 155);
            ctx.bezierCurveTo(canvas.width * 0.30, 235, canvas.width * 0.66, 75, canvas.width, 142);
            ctx.lineTo(canvas.width, 205);
            ctx.bezierCurveTo(canvas.width * 0.70, 145, canvas.width * 0.35, 285, 0, 215);
            ctx.closePath();
            ctx.fill();

            // Left detail panel + right QR panel for cleaner composition
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(48, 228, 885, 630);
            ctx.fillRect(952, 228, 500, 500);
            ctx.strokeStyle = '#dfe5eb';
            ctx.lineWidth = 2;
            ctx.strokeRect(48, 228, 885, 630);
            ctx.strokeRect(952, 228, 500, 500);

            ctx.fillStyle = '#20232a';
            ctx.font = '700 42px Arial';
            var y = 312;
            function drawRow(label, value) {
                ctx.fillText(label, 88, y);
                ctx.font = '600 40px Arial';
                ctx.fillText(value || '-', 310, y);
                ctx.font = '700 42px Arial';
                y += 74;
            }
            drawRow('No Unit:', unitNo);
            drawRow('Serial:', serial);
            drawRow('Brand:', brand);
            drawRow('Model:', model);
            drawRow('Type:', type);
            drawRow('Capacity:', capacity);

            var tmpUrls = [];
            if (logoUrl) {
                try {
                    const logoObjTop = await loadImageAsObjectUrl(logoUrl);
                    tmpUrls.push(logoObjTop);
                    const logoImgTop = await loadImg(logoObjTop);
                    ctx.drawImage(logoImgTop, 78, 28, 300, 120);
                } catch (e) {}
            }
            if (qrUrl) {
                try {
                    const qrObj = await loadImageAsObjectUrl(qrUrl);
                    tmpUrls.push(qrObj);
                    const qrImg = await loadImg(qrObj);
                    // Center QR inside its panel (500x500)
                    ctx.drawImage(qrImg, 980, 256, 444, 444);
                } catch (e) {}
            }
            // Barcode 1D intentionally removed per request.

            const dataUrl = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            link.href = dataUrl;
            link.download = 'barcode-label-' + unitNo.replace(/[^a-zA-Z0-9_-]/g, '_') + '.png';
            document.body.appendChild(link);
            link.click();
            link.remove();
            tmpUrls.forEach(function(u) { try { URL.revokeObjectURL(u); } catch (e) {} });
        } catch (err) {
            console.error(err);
            if (window.OptimaNotify) OptimaNotify.error('Gagal generate barcode label.');
        }
    }
</script>
<?= $this->endSection() ?>
