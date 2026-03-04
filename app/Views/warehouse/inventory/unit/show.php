<?= $this->extend('layouts/base') ?>

<?php
helper('global_permission');
$permissions = get_global_permission('warehouse');
$can_edit = $permissions['edit'];

// Setup status badges
$statusId = $unit['status_unit_id'] ?? 0;
$badgeClass = 'secondary';
$statusIcon = 'fa-info-circle';
if (in_array($statusId, [1, 5])) { $badgeClass = 'success'; $statusIcon='fa-check-circle'; }
else if (in_array($statusId, [3, 4, 6, 7])) { $badgeClass = 'warning'; $statusIcon='fa-clock'; }
else if ($statusId == 8) { $badgeClass = 'danger'; $statusIcon='fa-tools'; }

$unitNo = $unit['no_unit'] ?: ($unit['no_unit_na'] ?: 'TEMP-'.$unit['id_inventory_unit']);
// Fall back to tipe_unit (tipe/jenis) when model_unit FK is missing (id=0)
$brand = !empty($unit['merk_unit']) ? $unit['merk_unit'] : ($unit['unit_tipe'] ?? 'N/A');
$model = !empty($unit['model_unit']) ? $unit['model_unit'] : ($unit['unit_jenis'] ?? '');

// Parse aksesoris JSON → readable labels
$aksesorisRaw  = $unit['aksesoris'] ?? null;
$aksesorisMap  = [
    'rotary_lamp'  => 'Rotary Lamp',
    'back_buzzer'  => 'Back Buzzer',
    'mirror'       => 'Mirror',
    'lampu_sorot'  => 'Work Light',
    'fire_ext'     => 'Fire Extinguisher',
    'safety_belt'  => 'Seat Belt',
    'horn'         => 'Horn',
    'strobe_light' => 'Strobe Light',
];
$aksesorisItems = [];
if ($aksesorisRaw) {
    $decoded = json_decode($aksesorisRaw, true);
    if (is_array($decoded)) {
        foreach ($decoded as $k => $v) {
            if ($v) $aksesorisItems[] = $aksesorisMap[$k] ?? ucwords(str_replace('_', ' ', $k));
        }
    } else {
        $aksesorisItems = array_values(array_filter(array_map('trim', explode(',', $aksesorisRaw))));
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
        <button type="button" class="btn btn-outline-info btn-sm" onclick="fetchUnitHistory(<?= $unit['id_inventory_unit'] ?>)">
            <i class="fas fa-history me-1" aria-hidden="true"></i>Refresh History
        </button>
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

                <!-- Native Bootstrap 5 Nav Tabs -->
                <ul class="nav nav-tabs nav-fill mb-3" id="detailTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-overview" data-bs-toggle="tab" data-bs-target="#pane-overview" type="button" role="tab">
                            <i class="fas fa-info-circle me-1"></i>Overview
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-components" data-bs-toggle="tab" data-bs-target="#pane-components" type="button" role="tab">
                            <i class="fas fa-cogs me-1"></i>Specifications
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-movement" data-bs-toggle="tab" data-bs-target="#pane-movement" type="button" role="tab">
                            <i class="fas fa-route me-1"></i>Movements
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-workorder" data-bs-toggle="tab" data-bs-target="#pane-workorder" type="button" role="tab">
                            <i class="fas fa-tools me-1"></i>Work Orders
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-parts" data-bs-toggle="tab" data-bs-target="#pane-parts" type="button" role="tab">
                            <i class="fas fa-wrench me-1"></i>Spareparts
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-contracts" data-bs-toggle="tab" data-bs-target="#pane-contracts" type="button" role="tab">
                            <i class="fas fa-file-contract me-1"></i>Contracts/Rental
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    
                    <!-- ── Overview ── -->
                    <div class="tab-pane fade show active" id="pane-overview" role="tabpanel">
                        <!-- Location Info -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i><strong>Current Location</strong></h6>
                            </div>
                            <div class="card-body">
                                <?php if($unit['status_unit_id'] == 7 && !empty($unit['customer_location_name'])): ?>
                                    <h5 class="mb-1 text-dark fw-bold"><?= esc($unit['customer_location_name']) ?></h5>
                                    <p class="text-primary mb-0"><i class="fas fa-building me-1"></i> <?= esc($unit['customer_name'] ?? '-') ?></p>
                                <?php else: ?>
                                    <h5 class="mb-0 text-dark fw-bold"><i class="fas fa-warehouse text-primary me-2"></i><?= esc($unit['lokasi_unit'] ?? 'Internal Warehouse') ?></h5>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Data & Remarks Row -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-list-alt me-2"></i><strong>Unit Details</strong></h6>
                                    </div>
                                    <div class="card-body">
                                        <dl class="row mb-0 small">
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
                                                    <!-- Wheel Type (read-only) -->
                                                    <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                                                        <span class="text-muted">Wheel Type</span>
                                                        <span class="fw-bold"><?= esc($unit['jenis_roda'] ?? '-') ?></span>
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

                    <!-- ── Pergerakan / Movement History ── -->
                    <div class="tab-pane fade" id="pane-movement" role="tabpanel">
                        <div class="card border-0 mb-0">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-route me-2"></i><strong>Unit Movement History</strong></h6>
                            </div>
                            <div class="card-body">
                                <!-- Loader -->
                                <div id="movement-loader" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                                    <p class="text-muted mt-2 small">Loading movement history...</p>
                                </div>
                                <!-- Empty state -->
                                <div id="movement-empty" class="text-center py-5" style="display:none;">
                                    <i class="fas fa-route fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">No movement records found for this unit.</p>
                                </div>
                                <!-- Timeline container -->
                                <div id="movement-timeline" class="ps-1" style="display:none;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- ── Work Orders ── -->
                    <div class="tab-pane fade" id="pane-workorder" role="tabpanel">
                        <div class="card border-0 mb-0">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-wrench me-2"></i><strong>Service History (W.O.)</strong></h6>
                                <button class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> New W.O.</button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover mb-0">
                                        <thead class="bg-light small">
                                            <tr>
                                                <th>W.O. No.</th>
                                                <th>Date</th>
                                                <th>Work Type</th>
                                                <th>Technician</th>
                                                <th class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="small">
                                            <?php if(empty($work_orders)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">No service history found.</td>
                                            </tr>
                                            <?php else: foreach($work_orders as $wo): ?>
                                            <tr>
                                                <td class="fw-bold"><a href="#"><?= esc($wo['wo_number']) ?></a></td>
                                                <td><?= date('d M Y', strtotime($wo['date'])) ?></td>
                                                <td><?= esc($wo['type']) ?></td>
                                                <td><?= esc($wo['technician']) ?></td>
                                                <td class="text-center">
                                                    <span class="badge bg-<?= $wo['status'] == 'COMPLETED' ? 'success' : 'warning' ?>">
                                                        <?= esc($wo['status']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endforeach; endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── Spareparts Form ── -->
                    <div class="tab-pane fade" id="pane-parts" role="tabpanel">
                        <div class="card border-0 mb-0">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-cogs me-2"></i><strong>Spare Part Usage</strong></h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover mb-0">
                                        <thead class="bg-light small">
                                            <tr>
                                                <th>Date</th>
                                                <th>W.O. Reference</th>
                                                <th>Part Name</th>
                                                <th class="text-end">Qty Used</th>
                                            </tr>
                                        </thead>
                                        <tbody class="small">
                                            <?php if(empty($sparepart_usages)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">No spare part records found.</td>
                                            </tr>
                                            <?php else: foreach($sparepart_usages as $sp): ?>
                                            <tr>
                                                <td><?= date('d M Y', strtotime($sp['date'])) ?></td>
                                                <td><a href="#"><?= esc($sp['wo_ref']) ?></a></td>
                                                <td class="fw-bold"><?= esc($sp['part_name']) ?></td>
                                                <td class="text-end fw-bold text-primary"><?= esc($sp['qty']) ?> <span class="text-muted fw-normal"><?= esc($sp['uom']) ?></span></td>
                                            </tr>
                                            <?php endforeach; endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── Contract / Rental ── -->
                    <div class="tab-pane fade" id="pane-contracts" role="tabpanel">
                        <div class="card border-0 mb-0">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-file-contract me-2"></i><strong>Contract Rental History</strong></h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover mb-0">
                                        <thead class="bg-light small">
                                            <tr>
                                                <th>Contract No.</th>
                                                <th>Customer & Location</th>
                                                <th>Period</th>
                                                <th class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="small">
                                            <?php if(empty($rental_history)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">No rental contract history found.</td>
                                            </tr>
                                            <?php else: foreach($rental_history as $rt): ?>
                                            <tr>
                                                <td class="fw-bold"><a href="#"><?= esc($rt['contract_no']) ?></a></td>
                                                <td class="fw-bold"><?= esc($rt['customer']) ?></td>
                                                <td><?= date('M Y', strtotime($rt['start_date'])) ?> – <?= date('M Y', strtotime($rt['end_date'])) ?></td>
                                                <td class="text-center">
                                                    <span class="badge bg-<?= $rt['status'] == 'ACTIVE' ? 'success' : 'secondary' ?>">
                                                        <?= esc($rt['status']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endforeach; endif; ?>
                                        </tbody>
                                    </table>
                                </div>
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

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
    $(document).ready(function() {
        // Force Overview tab active on every page load (prevent browser tab-state memory)
        const overviewTab = document.getElementById('tab-overview');
        if (overviewTab) bootstrap.Tab.getOrCreateInstance(overviewTab).show();

        // Auto load history
        fetchUnitHistory(<?= $unit['id_inventory_unit'] ?>);
    });

    function fetchUnitHistory(unitId) {
        $('#history-timeline-container').hide();
        $('#history-loader').show();
        
        $.ajax({
            url: `<?= base_url('warehouse/inventory/unit/') ?>${unitId}/timeline`,
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

    // ── Pergerakan tab: lazy AJAX load ─────────────────────────────────
    let _movementLoaded = false;

    $('#tab-movement').on('shown.bs.tab', function () {
        if (!_movementLoaded) {
            loadMovementHistory(<?= (int)$unit['id_inventory_unit'] ?>);
        }
    });

    function loadMovementHistory(unitId) {
        $('#movement-loader').show();
        $('#movement-empty').hide();
        $('#movement-timeline').hide().empty();

        $.ajax({
            url  : `<?= base_url('warehouse/inventory/unit/') ?>${unitId}/movements`,
            type : 'GET',
            dataType: 'json',
            success: function (res) {
                $('#movement-loader').hide();
                if (!res.success || !res.events || res.events.length === 0) {
                    $('#movement-empty').show();
                    return;
                }
                _movementLoaded = true;

                let iconMap = {
                    delivery : 'fa-truck',
                    rental   : 'fa-file-contract',
                    location : 'fa-map-marker-alt',
                    system   : 'fa-circle'
                };
                let colorMap = {
                    delivery : 'primary',
                    rental   : 'warning',
                    location : 'success',
                    system   : 'info'
                };

                let html = '';
                res.events.forEach(function (ev, idx) {
                    let dateStr  = ev.event_date ? new Date(ev.event_date).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) : '—';
                    let icon     = iconMap[ev.type]  || 'fa-circle';
                    let color    = colorMap[ev.type] || 'secondary';
                    let isLast   = idx === res.events.length - 1;

                    // Build meta key-value rows
                    let metaHtml = '';
                    if (ev.meta && Object.keys(ev.meta).length > 0) {
                        Object.entries(ev.meta).forEach(function ([k, v]) {
                            if (v) metaHtml += `<span class="me-3 text-nowrap"><i class="fas fa-minus me-1 small"></i>${k}: <strong>${v}</strong></span>`;
                        });
                        if (metaHtml) metaHtml = `<div class="text-muted small mt-1 flex-wrap d-flex gap-1">${metaHtml}</div>`;
                    }

                    html += `
                        <div class="d-flex gap-3 mb-0">
                            <!-- Icon + vertical line -->
                            <div class="flex-shrink-0 text-center" style="width:42px;">
                                <div class="rounded-circle bg-${color} text-white d-flex align-items-center justify-content-center mx-auto mb-0" style="width:36px;height:36px;">
                                    <i class="fas ${icon} small"></i>
                                </div>
                                ${!isLast ? '<div class="border-start border-2 border-secondary mx-auto" style="width:2px;min-height:40px;opacity:.25;"></div>' : ''}
                            </div>
                            <!-- Content -->
                            <div class="flex-grow-1 pb-4 ${!isLast ? 'border-bottom' : ''}">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-1">
                                    <div>
                                        <div class="fw-semibold text-dark">${ev.title}</div>
                                        <div class="text-muted small">${ev.subtitle}</div>
                                        ${ev.reference && ev.reference !== '—' ? `<span class="font-monospace small text-primary">${ev.reference}</span>` : ''}
                                        ${metaHtml}
                                    </div>
                                    <div class="text-end flex-shrink-0">
                                        <div class="small text-muted mb-1">${dateStr}</div>
                                        <span class="badge bg-${ev.status_cls || 'secondary'}">${ev.status}</span>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                });

                $('#movement-timeline').html(html).show();
            },
            error: function () {
                $('#movement-loader').hide();
                $('#movement-timeline')
                    .html('<div class="alert alert-danger small">Failed to load movement history.</div>')
                    .show();
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
            '<?= csrf_token() ?>': $('meta[name="<?= csrf_token() ?>"]').attr('content'),
            model_mesin_id   : $('select[name="model_mesin_id"]').val(),
            sn_mesin         : $('input[name="sn_mesin"]').val(),
            kapasitas_unit_id: $('select[name="kapasitas_unit_id"]').val(),
            model_mast_id    : $('select[name="model_mast_id"]').val(),
            sn_mast          : $('input[name="sn_mast"]').val(),
            tinggi_mast      : $('input[name="tinggi_mast"]').val(),
            fuel_type        : $('select[name="fuel_type"]').val(),
            ownership_status : $('select[name="ownership_status"]').val(),
        };

        // Handle ban_id (select or text input)
        let banInput = $('select[name="ban_id"]').length ? $('select[name="ban_id"]') : $('input[name="ban_id"]');
        if (banInput.length) data.ban_id = banInput.val();

        $.ajax({
            url: '<?= base_url("warehouse/inventory/unit/".$unit["id_inventory_unit"]."/inline-update") ?>',
            type: 'POST',
            data: data,
            success: function(res) {
                $('#btnSaveSpecs').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save').removeClass('disabled');

                if (res.csrf_hash) $('meta[name="<?= csrf_token() ?>"]').attr('content', res.csrf_hash);

                if (res.success) {
                    Swal.fire({icon: 'success', title: 'Saved', text: res.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});

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

                    toggleSpecEdit(false);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function() {
                $('#btnSaveSpecs').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save').removeClass('disabled');
                Swal.fire('Error', 'Failed to connect to server.', 'error');
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
            '<?= csrf_token() ?>': $('meta[name="<?= csrf_token() ?>"]').attr('content'),
            keterangan: catatan
        };

        $.ajax({
            url: '<?= base_url("warehouse/inventory/unit/".$unit["id_inventory_unit"]."/inline-update") ?>',
            type: 'POST',
            data: data,
            success: function(res) {
                $('#btnSaveCatatan').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save').removeClass('disabled');

                if (res.csrf_hash) $('meta[name="<?= csrf_token() ?>"]').attr('content', res.csrf_hash);

                if (res.success) {
                    Swal.fire({icon: 'success', title: 'Saved', text: res.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});

                    let viewHtml = catatan ? catatan.replace(/\n/g, "<br>") : '<em class="text-muted">No notes yet.</em>';
                    $('#view-catatan').html(viewHtml);

                    toggleCatatanEdit(false);
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function() {
                $('#btnSaveCatatan').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save').removeClass('disabled');
                Swal.fire('Error', 'Failed to connect to server.', 'error');
            }
        });
    }
</script>
<?= $this->endSection() ?>
