<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unit Detail — Public View</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background: #f4f6fa; font-size: .875rem; }
        .page-header {
            background: linear-gradient(135deg, #1a56db 0%, #0e3fad 100%);
            color: #fff; padding: 1.25rem 1.5rem;
            border-radius: .5rem; margin-bottom: 1.25rem;
        }
        .page-header .unit-no  { font-size: 1.35rem; font-weight: 700; letter-spacing: .03em; }
        .page-header .unit-sub { font-size: .85rem; opacity: .85; }
        .card { border: none; box-shadow: 0 1px 4px rgba(0,0,0,.08); border-radius: .5rem; }
        .card-header { background: #f8f9fb; border-bottom: 1px solid #e9ecef; border-radius: .5rem .5rem 0 0 !important; padding: .6rem 1rem; }
        .card-header h6 { margin: 0; font-size: .8rem; font-weight: 600; color: #495057; }
        .section-label { font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: #868e96; border-bottom: 1px solid #e9ecef; padding-bottom: .35rem; margin-bottom: .5rem; }
        .spec-list { list-style: none; padding: 0; margin: 0; }
        .spec-list li { display: flex; justify-content: space-between; align-items: center; padding: .3rem .9rem; border-bottom: 1px solid #f0f0f0; }
        .spec-list li:last-child { border-bottom: none; }
        .spec-list .lbl { color: #6c757d; }
        .spec-list .val { font-weight: 600; text-align: right; max-width: 60%; }
        /* Password overlay — frosted white */
        #pw-overlay { position: fixed; inset: 0; background: rgba(255,255,255,.55); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 9999; display: flex; align-items: center; justify-content: center; }
        #pw-box { background: #fff; border-radius: .75rem; padding: 2rem 2.25rem; width: 340px; max-width: 94vw; box-shadow: 0 8px 40px rgba(0,0,0,.18), 0 2px 8px rgba(0,0,0,.08); }
        #pw-box .logo { font-size: 1.8rem; margin-bottom: .5rem; }
        #pw-box h5 { font-weight: 700; margin-bottom: .25rem; }
        #pw-box p  { color: #6c757d; font-size: .85rem; margin-bottom: 1.25rem; }
        #pw-error  { display: none; color: #dc3545; font-size: .82rem; margin-top: .4rem; }
        /* WO filter bar */
        .filter-bar { background: #f8f9fb; border-bottom: 1px solid #e9ecef; padding: .6rem 1rem; display: flex; flex-wrap: wrap; gap: .5rem; align-items: center; }
        .wo-row { padding: .65rem 1rem; border-bottom: 1px solid #f0f0f0; }
        .wo-row:last-child { border-bottom: none; }
        .wo-row .wo-num  { font-family: monospace; font-size: .8rem; color: #6c757d; }
        .wo-row .wo-type { font-weight: 600; font-size: .85rem; }
        #wo-empty-msg { display: none; padding: .75rem 1rem; color: #6c757d; font-style: italic; font-size: .85rem; }
    </style>
</head>
<body>

<!-- ── Password Overlay ──────────────────────────────────── -->
<div id="pw-overlay">
    <div id="pw-box">
        <div class="logo text-center">🔐</div>
        <h5 class="text-center">Protected View</h5>
        <p class="text-center">Masukkan password untuk melihat detail unit ini.</p>
        <div class="mb-3">
            <input type="password" id="pw-input" class="form-control" placeholder="Password" autocomplete="off">
            <div id="pw-error">Password salah. Coba lagi.</div>
        </div>
        <button class="btn btn-primary w-100" onclick="checkPassword()">
            <i class="fas fa-unlock me-1"></i>Buka
        </button>
    </div>
</div>

<?php
$unitNo    = $unit['no_unit'] ?: ($unit['no_unit_na'] ?: ('TEMP-' . ($unit['id_inventory_unit'] ?? '-')));
$unitName  = trim(($unit['merk_unit'] ?? '') . ' ' . ($unit['model_unit'] ?? ''));
$tipeName  = trim(($unit['tipe'] ?? '') . ' ' . ($unit['jenis'] ?? ''));
$fuelRaw   = strtoupper(trim((string)($unit['fuel_type'] ?? '')));
$statusName = $unit['status_unit'] ?? ($unit['status_unit_name'] ?? 'UNKNOWN');
$statusColors = ['AVAILABLE'=>'success','RENTED'=>'primary','MAINTENANCE'=>'warning','PREPARATION'=>'info','SCRAPPED'=>'secondary'];
$statusBadge  = $statusColors[$statusName] ?? 'secondary';

$siloStatus = strtoupper((string)($silo['status'] ?? 'BELUM_ADA'));
$siloBadge  = ['SILO_TERBIT'=>'success','SILO_EXPIRED'=>'danger'][$siloStatus] ?? ($siloStatus === 'BELUM_ADA' ? 'secondary' : 'warning');

$comp = $current_components ?? ['battery' => null, 'charger' => null, 'attachment' => null];
$hasAnyComp = !empty($comp['battery']) || !empty($comp['charger']) || !empty($comp['attachment']);

function pubFmtDate(?string $d): string {
    if (!$d) return '-';
    $ts = strtotime($d);
    return $ts ? date('d M Y', $ts) : '-';
}
function pubFmtHm($val): string {
    if ($val === null || $val === '') return '-';
    return number_format((float)$val, 0, '.', ',') . ' HM';
}
?>

<!-- ── Page Content ──────────────────────────────────────── -->
<div id="page-content" style="display:none;">
<div class="container py-4" style="max-width: 960px;">

    <!-- ── Header ── -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <div class="unit-no"><i class="fas fa-forklift me-2 opacity-75"></i><?= esc($unitNo) ?></div>
                <div class="unit-sub"><?= esc($unitName ?: '-') ?><?= $tipeName ? ' &bull; ' . esc($tipeName) : '' ?></div>
            </div>
            <span class="badge bg-<?= $statusBadge ?> fs-6 align-self-start"><?= esc($statusName) ?></span>
        </div>
    </div>

    <!-- ── Card 1: Unit Information ── -->
    <div class="card mb-3">
        <div class="card-header">
            <h6><i class="fas fa-id-card me-2 text-primary"></i>Unit Information</h6>
        </div>
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-md-6 border-end">
                    <div class="px-3 pt-3 pb-2">
                        <p class="section-label"><i class="fas fa-hashtag me-1"></i>Identity</p>
                    </div>
                    <ul class="spec-list small">
                        <li class="bg-light">
                            <span class="lbl">No Unit</span>
                            <span class="val font-monospace"><?= esc($unitNo) ?></span>
                        </li>
                        <li>
                            <span class="lbl">Serial Number</span>
                            <span class="val font-monospace"><?= esc($unit['serial_number'] ?: '-') ?></span>
                        </li>
                        <li>
                            <span class="lbl">Model Unit</span>
                            <span class="val"><?= esc($unitName ?: '-') ?></span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <div class="px-3 pt-3 pb-2">
                        <p class="section-label"><i class="fas fa-tag me-1"></i>Classification</p>
                    </div>
                    <ul class="spec-list small">
                        <li>
                            <span class="lbl">Tipe Unit</span>
                            <span class="val"><?= esc($tipeName ?: '-') ?></span>
                        </li>
                        <li>
                            <span class="lbl">Department</span>
                            <span class="val"><?= esc($unit['unit_departemen'] ?? '-') ?></span>
                        </li>
                        <li>
                            <span class="lbl">Year of Make</span>
                            <span class="val"><?= esc($unit['tahun_unit'] ?: '-') ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Card 2: Technical Specifications ── -->
    <div class="card mb-3">
        <div class="card-header">
            <h6><i class="fas fa-cogs me-2 text-secondary"></i>Technical Specifications — Engine, Mast &amp; Tyres</h6>
        </div>
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-md-6 border-end">
                    <div class="px-3 pt-3 pb-2">
                        <p class="section-label"><i class="fas fa-fire me-1 text-warning"></i>Engine &amp; Power</p>
                    </div>
                    <ul class="spec-list small">
                        <li>
                            <span class="lbl">Engine Model</span>
                            <span class="val"><?= esc(trim(($unit['merk_mesin'] ?? '') . ' ' . ($unit['model_mesin'] ?? '')) ?: '-') ?></span>
                        </li>
                        <li>
                            <span class="lbl">Engine S/N</span>
                            <span class="val font-monospace"><?= esc($unit['sn_mesin'] ?? '-') ?></span>
                        </li>
                        <li>
                            <span class="lbl">Capacity</span>
                            <span class="val"><?= esc($unit['kapasitas_display'] ?? ($unit['kapasitas_unit'] ?? '-')) ?></span>
                        </li>
                        <li>
                            <span class="lbl">Fuel Type</span>
                            <span class="val">
                                <?php if ($fuelRaw): ?><span class="badge bg-warning text-dark"><?= esc($fuelRaw) ?></span><?php else: ?>-<?php endif; ?>
                            </span>
                        </li>
                        <li>
                            <span class="lbl">Hour Meter</span>
                            <span class="val"><?= pubFmtHm($unit['hour_meter'] ?? null) ?></span>
                        </li>
                        <?php if (!empty($unit['asset_tag'])): ?>
                        <li class="bg-light">
                            <span class="lbl">Asset Tag</span>
                            <span class="val font-monospace"><?= esc($unit['asset_tag']) ?></span>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-6">
                    <div class="px-3 pt-3 pb-2">
                        <p class="section-label"><i class="fas fa-arrows-alt-v me-1 text-info"></i>Mast &amp; Tyres</p>
                    </div>
                    <ul class="spec-list small">
                        <li>
                            <span class="lbl">Mast Type</span>
                            <span class="val"><?= esc($unit['tipe_mast'] ?? '-') ?></span>
                        </li>
                        <li>
                            <span class="lbl">Mast Height</span>
                            <span class="val"><?= !empty($unit['tinggi_mast']) ? esc($unit['tinggi_mast'] . ' mm') : '-' ?></span>
                        </li>
                        <li>
                            <span class="lbl">Mast S/N</span>
                            <span class="val font-monospace"><?= esc($unit['sn_mast'] ?? '-') ?></span>
                        </li>
                        <li>
                            <span class="lbl">Tyre Type</span>
                            <span class="val"><?= esc($unit['tipe_ban'] ?? '-') ?></span>
                        </li>
                        <li>
                            <span class="lbl">Front Tyre</span>
                            <span class="val"><?= esc($unit['ban_depan'] ?? '-') ?></span>
                        </li>
                        <li>
                            <span class="lbl">Rear Tyre</span>
                            <span class="val"><?= esc($unit['ban_belakang'] ?? '-') ?></span>
                        </li>
                        <li>
                            <span class="lbl">Wheel Type</span>
                            <span class="val"><?= esc($unit['jenis_roda'] ?? '-') ?></span>
                        </li>
                        <li>
                            <span class="lbl">Valve</span>
                            <span class="val"><?= esc($unit['jumlah_valve'] ?? '-') ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Accessories + Components ── -->
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6><i class="fas fa-tools me-2 text-secondary"></i>Accessories</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($aksesorisItems)): ?>
                        <span class="text-muted small">Standard / No accessories</span>
                    <?php else: ?>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($aksesorisItems as $item): ?>
                                <span class="badge bg-secondary rounded-pill"><?= esc($item) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6><i class="fas fa-puzzle-piece me-2 text-secondary"></i>Komponen Terpasang</h6>
                </div>
                <div class="card-body small">
                    <?php if (!$hasAnyComp): ?>
                        <span class="text-muted">Tidak ada komponen terpasang.</span>
                    <?php else: ?>
                        <?php if (!empty($comp['attachment']) && is_array($comp['attachment'])): $a = $comp['attachment']; ?>
                            <div class="mb-2">
                                <span class="badge bg-secondary me-1">Attachment</span>
                                <strong><?= esc(trim(($a['merk'] ?? '') . ' ' . ($a['model'] ?? '') . ' ' . ($a['tipe'] ?? '')) ?: 'N/A') ?></strong>
                                <?php if (!empty($a['sn_attachment'])): ?><div class="text-muted font-monospace">S/N: <?= esc($a['sn_attachment']) ?></div><?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($comp['charger']) && is_array($comp['charger'])): $c = $comp['charger']; ?>
                            <div class="mb-2">
                                <span class="badge bg-primary me-1">Charger</span>
                                <strong><?= esc(trim(($c['merk_charger'] ?? '') . ' ' . ($c['tipe_charger'] ?? '')) ?: 'N/A') ?></strong>
                                <?php if (!empty($c['sn_charger'])): ?><div class="text-muted font-monospace">S/N: <?= esc($c['sn_charger']) ?></div><?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($comp['battery']) && is_array($comp['battery'])): $b = $comp['battery']; ?>
                            <div>
                                <span class="badge bg-warning text-dark me-1">Baterai</span>
                                <strong><?= esc(trim(($b['merk_baterai'] ?? '') . ' ' . ($b['tipe_baterai'] ?? '') . ' ' . ($b['jenis_baterai'] ?? '')) ?: 'N/A') ?></strong>
                                <?php if (!empty($b['sn_baterai'])): ?><div class="text-muted font-monospace">S/N: <?= esc($b['sn_baterai']) ?></div><?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ── SILO ── -->
    <div class="card mb-3">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="mb-0"><i class="fas fa-file-alt me-2"></i>Status SILO</h6>
            <span class="badge bg-<?= esc($siloBadge) ?>"><?= esc($siloStatus) ?></span>
        </div>
        <div class="card-body p-0">
            <ul class="spec-list small">
                <li>
                    <span class="lbl">Nomor SILO</span>
                    <span class="val font-monospace"><?= esc($silo['nomor_silo'] ?? '-') ?></span>
                </li>
                <li>
                    <span class="lbl">Tanggal Terbit</span>
                    <span class="val"><?= pubFmtDate($silo['tanggal_terbit_silo'] ?? null) ?></span>
                </li>
                <li>
                    <span class="lbl">Tanggal Expired</span>
                    <span class="val"><?= pubFmtDate($silo['tanggal_expired_silo'] ?? null) ?></span>
                </li>
            </ul>
        </div>
    </div>

    <!-- ── Work Order History ── -->
    <div class="card mb-3">
        <div class="card-header">
            <h6><i class="fas fa-wrench me-2 text-primary"></i>Work Order History
                <span class="badge bg-secondary ms-1" id="wo-count-badge"><?= count($work_orders) ?></span>
            </h6>
        </div>

        <?php if (!empty($work_orders)): ?>
        <div class="filter-bar">
            <input type="text" id="wo-search" class="form-control form-control-sm"
                   placeholder="Cari nomor / tipe / catatan..." style="max-width:220px;">
            <select id="wo-filter-status" class="form-select form-select-sm" style="width:auto;">
                <option value="">Semua Status</option>
                <?php
                $woStatuses = array_unique(array_filter(array_column($work_orders, 'status')));
                sort($woStatuses);
                foreach ($woStatuses as $s): ?>
                    <option value="<?= esc(strtolower($s)) ?>"><?= esc($s) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="wo-filter-type" class="form-select form-select-sm" style="width:auto;">
                <option value="">Semua Tipe</option>
                <?php
                $woTypes = array_unique(array_filter(array_column($work_orders, 'type')));
                sort($woTypes);
                foreach ($woTypes as $t): ?>
                    <option value="<?= esc(strtolower($t)) ?>"><?= esc($t) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-sm btn-outline-secondary" onclick="resetWoFilters()">
                <i class="fas fa-times me-1"></i>Reset
            </button>
        </div>
        <?php endif; ?>

        <div id="wo-list">
            <?php if (empty($work_orders)): ?>
                <div class="p-3 text-muted small">Belum ada work order.</div>
            <?php else: ?>
                <?php foreach ($work_orders as $wo):
                    $woBadge = $wo['status_color'] ?? 'secondary';
                    $allowedBadge = ['primary','success','danger','warning','info','secondary','dark'];
                    if (!in_array($woBadge, $allowedBadge)) $woBadge = 'secondary';
                ?>
                <div class="wo-row"
                     data-status="<?= esc(strtolower($wo['status'] ?? '')) ?>"
                     data-type="<?= esc(strtolower($wo['type'] ?? '')) ?>"
                     data-search="<?= esc(strtolower(($wo['wo_number'] ?? '') . ' ' . ($wo['type'] ?? '') . ' ' . ($wo['notes'] ?? '') . ' ' . ($wo['technician'] ?? ''))) ?>">
                    <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                        <div>
                            <div class="wo-type"><?= esc($wo['type'] ?? 'Work Order') ?></div>
                            <div class="wo-num"><?= esc($wo['wo_number'] ?? '-') ?></div>
                            <?php if (!empty($wo['notes'])): ?>
                                <div class="text-muted small mt-1"><?= esc($wo['notes']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($wo['technician'])): ?>
                                <div class="small text-muted mt-1"><i class="fas fa-user me-1"></i><?= esc(trim($wo['technician'])) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-<?= esc($woBadge) ?>"><?= esc($wo['status'] ?? '-') ?></span>
                            <div class="small text-muted mt-1"><?= pubFmtDate($wo['date'] ?? null) ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <div id="wo-empty-msg">Tidak ada work order yang sesuai filter.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── Sparepart Usage ── -->
    <?php if (!empty($sparepart_usages)): ?>
    <div class="card mb-3">
        <div class="card-header">
            <h6><i class="fas fa-box-open me-2 text-warning"></i>Pemakaian Sparepart
                <span class="badge bg-secondary ms-1"><?= count($sparepart_usages) ?></span>
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Nama Part</th>
                        <th class="text-center">Qty</th>
                        <th>WO Ref</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($sparepart_usages as $sp): ?>
                    <tr>
                        <td><?= esc($sp['part_name'] ?? '-') ?></td>
                        <td class="text-center"><?= esc($sp['qty'] ?? '-') ?><?= !empty($sp['uom']) ? ' ' . esc($sp['uom']) : '' ?></td>
                        <td class="font-monospace text-muted"><?= esc($sp['wo_ref'] ?? '-') ?></td>
                        <td><?= pubFmtDate($sp['date'] ?? null) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <div class="text-center text-muted small mt-4 pb-3">
        <i class="fas fa-shield-alt me-1"></i>PT Sarana Mitra Luas &mdash; Your Rental Solution
    </div>

</div><!-- /container -->
</div><!-- /page-content -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    /* ── Password ── */
    var PASSWORD = 'yourrentalsolution';
    var SK = 'pub_unit_auth';

    function unlock() {
        document.getElementById('pw-overlay').style.display = 'none';
        document.getElementById('page-content').style.display = 'block';
    }

    if (sessionStorage.getItem(SK) === '1') { unlock(); }

    window.checkPassword = function () {
        var val = document.getElementById('pw-input').value;
        var err = document.getElementById('pw-error');
        if (val === PASSWORD) {
            sessionStorage.setItem(SK, '1');
            err.style.display = 'none';
            unlock();
        } else {
            err.style.display = 'block';
            document.getElementById('pw-input').select();
        }
    };

    document.getElementById('pw-input').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') window.checkPassword();
    });

    /* ── WO Filter ── */
    var searchInput  = document.getElementById('wo-search');
    var statusSelect = document.getElementById('wo-filter-status');
    var typeSelect   = document.getElementById('wo-filter-type');
    var emptyMsg     = document.getElementById('wo-empty-msg');
    var badge        = document.getElementById('wo-count-badge');

    function applyWoFilter() {
        var search = searchInput  ? searchInput.value.toLowerCase().trim() : '';
        var status = statusSelect ? statusSelect.value.toLowerCase()       : '';
        var type   = typeSelect   ? typeSelect.value.toLowerCase()         : '';
        var rows   = document.querySelectorAll('#wo-list .wo-row');
        var visible = 0;

        rows.forEach(function (row) {
            var show = true;
            if (search && (row.dataset.search || '').indexOf(search) === -1) show = false;
            if (status && (row.dataset.status || '') !== status) show = false;
            if (type   && (row.dataset.type   || '') !== type)   show = false;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        if (badge)    badge.textContent = visible;
        if (emptyMsg) emptyMsg.style.display = (visible === 0 && rows.length > 0) ? 'block' : 'none';
    }

    if (searchInput)  searchInput.addEventListener('input',  applyWoFilter);
    if (statusSelect) statusSelect.addEventListener('change', applyWoFilter);
    if (typeSelect)   typeSelect.addEventListener('change',   applyWoFilter);

    window.resetWoFilters = function () {
        if (searchInput)  searchInput.value  = '';
        if (statusSelect) statusSelect.value = '';
        if (typeSelect)   typeSelect.value   = '';
        applyWoFilter();
    };
})();
</script>
</body>
</html>
