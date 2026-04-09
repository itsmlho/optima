<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Unit View') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php
$unitNo = $unit['no_unit'] ?: ($unit['no_unit_na'] ?: ('TEMP-' . ($unit['id_inventory_unit'] ?? '-')));
$unitName = trim(($unit['merk_unit'] ?? '') . ' ' . ($unit['model_unit'] ?? ''));
$serialNo = $unit['serial_number'] ?? ($unit['serial_no'] ?? null);
$fuelRaw = $unit['fuel_type'] ?? ($unit['fuel_type_dept'] ?? ($unit['unit_departemen'] ?? ''));
$fuelLabel = $fuelRaw ? strtoupper((string)$fuelRaw) : '-';
$siloStatus = strtoupper((string)($silo['status'] ?? 'BELUM_ADA'));
$siloBadge = 'secondary';
if ($siloStatus === 'SILO_TERBIT') $siloBadge = 'success';
elseif ($siloStatus === 'SILO_EXPIRED') $siloBadge = 'danger';
elseif ($siloStatus !== 'BELUM_ADA') $siloBadge = 'warning';
?>
<div class="container py-4" style="max-width: 960px;">
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-primary text-white">
            <strong>Unit Detail (Public View)</strong>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div><small class="text-muted">No Unit</small></div>
                    <div class="fw-bold"><?= esc($unitNo) ?></div>
                </div>
                <div class="col-md-6">
                    <div><small class="text-muted">Serial Number</small></div>
                    <div class="fw-bold"><?= esc($unit['serial_number'] ?? '-') ?></div>
                </div>
                <div class="col-md-6">
                    <div><small class="text-muted">Model</small></div>
                    <div><?= esc($unitName !== '' ? $unitName : '-') ?></div>
                </div>
                <div class="col-md-6">
                    <div><small class="text-muted">Tipe/Jenis</small></div>
                    <div><?= esc(trim(($unit['tipe'] ?? '') . ' ' . ($unit['jenis'] ?? '')) ?: '-') ?></div>
                </div>
                <div class="col-md-6">
                    <div><small class="text-muted">Lokasi</small></div>
                    <div><?= esc($unit['lokasi_unit'] ?? '-') ?></div>
                </div>
                <div class="col-md-6">
                    <div><small class="text-muted">Fuel Type</small></div>
                    <div><?= esc($fuelLabel) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light"><strong>Spesifikasi Unit (Engine, Mast & Tyres)</strong></div>
                <div class="card-body">
                    <dl class="row mb-0 small">
                        <dt class="col-5 text-muted">Engine Model</dt>
                        <dd class="col-7"><?= esc(trim(($unit['merk_mesin'] ?? '') . ' ' . ($unit['model_mesin'] ?? '')) ?: '-') ?></dd>
                        <dt class="col-5 text-muted">Engine S/N</dt>
                        <dd class="col-7 font-monospace"><?= esc($unit['sn_mesin'] ?? '-') ?></dd>
                        <dt class="col-5 text-muted">Capacity</dt>
                        <dd class="col-7"><?= esc($unit['kapasitas_display'] ?? '-') ?></dd>
                        <dt class="col-5 text-muted">Hour Meter</dt>
                        <dd class="col-7"><?= ($unit['hour_meter'] !== null && $unit['hour_meter'] !== '') ? esc(number_format((float)$unit['hour_meter'], 0, '.', ',') . ' HM') : '-' ?></dd>
                        <dt class="col-5 text-muted">Mast Type</dt>
                        <dd class="col-7"><?= esc($unit['tipe_mast'] ?? '-') ?></dd>
                        <dt class="col-5 text-muted">Mast Height</dt>
                        <dd class="col-7"><?= !empty($unit['tinggi_mast']) ? esc($unit['tinggi_mast'] . ' mm') : '-' ?></dd>
                        <dt class="col-5 text-muted">Mast S/N</dt>
                        <dd class="col-7 font-monospace"><?= esc($unit['sn_mast'] ?? '-') ?></dd>
                        <dt class="col-5 text-muted">Tipe Ban</dt>
                        <dd class="col-7"><?= esc($unit['tipe_ban'] ?? '-') ?></dd>
                        <dt class="col-5 text-muted">Ban Depan</dt>
                        <dd class="col-7"><?= esc($unit['ban_depan'] ?? '-') ?></dd>
                        <dt class="col-5 text-muted">Ban Belakang</dt>
                        <dd class="col-7"><?= esc($unit['ban_belakang'] ?? '-') ?></dd>
                        <dt class="col-5 text-muted">Jenis Roda</dt>
                        <dd class="col-7"><?= esc($unit['jenis_roda'] ?? '-') ?></dd>
                        <dt class="col-5 text-muted">Valve</dt>
                        <dd class="col-7"><?= esc($unit['jumlah_valve'] ?? '-') ?></dd>
                        <dt class="col-5 text-muted">Tahun Unit</dt>
                        <dd class="col-7"><?= esc($unit['tahun_unit'] ?? '-') ?></dd>
                        <?php if (!empty($unit['asset_tag'])): ?>
                        <dt class="col-5 text-muted">Asset Tag</dt>
                        <dd class="col-7 font-monospace"><?= esc($unit['asset_tag']) ?></dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light"><strong>Customer & Location</strong></div>
                <div class="card-body">
                    <dl class="row mb-0 small">
                        <dt class="col-5 text-muted">Customer</dt>
                        <dd class="col-7"><?= esc($unit['customer_name'] ?? '-') ?></dd>
                        <dt class="col-5 text-muted">Area</dt>
                        <dd class="col-7"><?= esc($unit['area_name'] ?? '-') ?></dd>
                        <dt class="col-5 text-muted">Lokasi Customer</dt>
                        <dd class="col-7"><?= esc($unit['customer_location_name'] ?? '-') ?></dd>
                        <dt class="col-5 text-muted">Kota</dt>
                        <dd class="col-7"><?= esc($unit['customer_city'] ?? '-') ?></dd>
                        <dt class="col-5 text-muted">Alamat</dt>
                        <dd class="col-7"><?= esc($unit['customer_address'] ?? '-') ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light"><strong>Accessories</strong></div>
                <div class="card-body">
                    <?php if (empty($aksesorisItems)): ?>
                        <span class="text-muted small">Standard / No accessories</span>
                    <?php else: ?>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($aksesorisItems as $item): ?>
                                <span class="badge text-bg-secondary rounded-pill"><?= esc($item) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light"><strong>Komponen Terpasang Saat Ini</strong></div>
                <div class="card-body small">
                    <?php
                    $comp = $current_components ?? ['battery' => null, 'charger' => null, 'attachment' => null];
                    $hasAnyComp = !empty($comp['battery']) || !empty($comp['charger']) || !empty($comp['attachment']);
                    ?>
                    <?php if (!$hasAnyComp): ?>
                        <div class="text-muted">Tidak ada attachment, charger, atau baterai terpasang.</div>
                    <?php else: ?>
                        <?php if (!empty($comp['attachment']) && is_array($comp['attachment'])): $a = $comp['attachment']; ?>
                            <div class="mb-2">
                                <span class="badge text-bg-secondary me-1">Attachment</span>
                                <strong><?= esc(trim(($a['merk'] ?? '') . ' ' . ($a['model'] ?? '') . ' ' . ($a['tipe'] ?? '')) ?: 'N/A') ?></strong>
                                <?php if (!empty($a['sn_attachment'])): ?><div class="text-muted font-monospace">S/N: <?= esc($a['sn_attachment']) ?></div><?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($comp['charger']) && is_array($comp['charger'])): $c = $comp['charger']; ?>
                            <div class="mb-2">
                                <span class="badge text-bg-primary me-1">Charger</span>
                                <strong><?= esc(trim(($c['merk_charger'] ?? '') . ' ' . ($c['tipe_charger'] ?? '')) ?: 'N/A') ?></strong>
                                <?php if (!empty($c['sn_charger'])): ?><div class="text-muted font-monospace">S/N: <?= esc($c['sn_charger']) ?></div><?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($comp['battery']) && is_array($comp['battery'])): $b = $comp['battery']; ?>
                            <div>
                                <span class="badge text-bg-warning me-1">Baterai</span>
                                <strong><?= esc(trim(($b['merk_baterai'] ?? '') . ' ' . ($b['tipe_baterai'] ?? '') . ' ' . ($b['jenis_baterai'] ?? '')) ?: 'N/A') ?></strong>
                                <?php if (!empty($b['sn_baterai'])): ?><div class="text-muted font-monospace">S/N: <?= esc($b['sn_baterai']) ?></div><?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-header bg-light d-flex align-items-center justify-content-between">
            <strong>Status SILO</strong>
            <span class="badge text-bg-<?= esc($siloBadge) ?>"><?= esc($siloStatus) ?></span>
        </div>
        <div class="card-body small">
            <div class="row g-2">
                <div class="col-md-4"><span class="text-muted">Nomor SILO:</span> <span class="fw-semibold"><?= esc($silo['nomor_silo'] ?? '-') ?></span></div>
                <div class="col-md-4"><span class="text-muted">Terbit:</span> <?= !empty($silo['tanggal_terbit_silo']) ? date('d M Y', strtotime($silo['tanggal_terbit_silo'])) : '-' ?></div>
                <div class="col-md-4"><span class="text-muted">Expired:</span> <?= !empty($silo['tanggal_expired_silo']) ? date('d M Y', strtotime($silo['tanggal_expired_silo'])) : '-' ?></div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center gap-2">
            <strong>History Log Unit</strong>
            <select id="history-group-mode" class="form-select form-select-sm" style="width:auto;">
                <option value="document">Group: Dokumen</option>
                <option value="date">Group: Tanggal</option>
            </select>
        </div>
        <div class="card-body">
            <?php if (empty($events)): ?>
                <div class="text-muted">Belum ada activity log.</div>
            <?php else: ?>
                <div id="history-grouped-container"></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php if (!empty($events)): ?>
<script>
(function() {
    const events = <?= json_encode($events, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?> || [];
    const modeSelect = document.getElementById('history-group-mode');
    const container = document.getElementById('history-grouped-container');
    if (!modeSelect || !container) return;

    function escHtml(v) {
        if (v === null || v === undefined) return '';
        return String(v)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatDate(v, withTime) {
        if (!v) return '-';
        const d = new Date(v);
        if (Number.isNaN(d.getTime())) return '-';
        const opt = withTime
            ? { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }
            : { day: '2-digit', month: 'short', year: 'numeric' };
        return d.toLocaleDateString('id-ID', opt);
    }

    function docGroupKey(ev) {
        const t = String(ev.reference_type || ev.category || 'MISC').toUpperCase();
        const n = String(ev.reference_number || ev.description || 'NO-REF');
        const sensitive = t === 'CONTRACT' || t === 'PO' || t === 'PURCHASE_ORDER' || t === 'PURCHASE';
        return sensitive ? t : (t + '|' + n);
    }

    function docGroupTitle(ev) {
        const t = String(ev.reference_type || ev.category || 'DOKUMEN').toUpperCase();
        const n = String(ev.reference_number || ev.description || '-');
        const sensitive = t === 'CONTRACT' || t === 'PO' || t === 'PURCHASE_ORDER' || t === 'PURCHASE';
        return sensitive ? t : (t + ' - ' + n);
    }

    function dateGroupKey(ev) {
        return formatDate(ev.date, false);
    }

    function render() {
        const mode = modeSelect.value || 'document';
        const grouped = {};
        const keys = [];

        events.forEach(function(ev) {
            const key = mode === 'date' ? dateGroupKey(ev) : docGroupKey(ev);
            if (!grouped[key]) {
                grouped[key] = [];
                keys.push(key);
            }
            grouped[key].push(ev);
        });

        let html = '';
        function sanitizeSensitiveText(text, hideRef) {
            if (!text) return '';
            let out = String(text);
            if (hideRef) {
                // Hide contract/PO style numbers in public view, e.g. WF-100179491, PO-2026/01/001
                out = out.replace(/\b(?:WF|PO|KONTRAK|CONTRACT)[-\/: ]?[A-Z0-9][A-Z0-9\-\/]*\b/gi, '').trim();
                out = out.replace(/\s{2,}/g, ' ');
            }
            return out;
        }
        keys.forEach(function(key) {
            const items = grouped[key];
            const title = mode === 'date' ? key : docGroupTitle(items[0]);
            html += '<div class="border rounded mb-3 overflow-hidden">';
            html += '<div class="bg-light px-3 py-2 d-flex justify-content-between align-items-center">';
            html += '<strong class="small">' + escHtml(title) + '</strong>';
            html += '<span class="badge text-bg-secondary">' + items.length + ' item</span>';
            html += '</div>';
            html += '<div class="list-group list-group-flush">';

            items.forEach(function(ev) {
                const refType = String(ev.reference_type || ev.category || '').toUpperCase();
                const hideRef = refType === 'CONTRACT' || refType === 'PO' || refType === 'PURCHASE_ORDER' || refType === 'PURCHASE';
                const safeTitle = sanitizeSensitiveText(ev.title || 'Event', hideRef) || (ev.category ? (String(ev.category) + ' Event') : 'Event');
                const safeDesc = sanitizeSensitiveText(ev.description || '', hideRef);
                const safeDetail = sanitizeSensitiveText(ev.detail || '', hideRef);
                html += '<div class="list-group-item">';
                html += '<div class="d-flex justify-content-between align-items-start gap-2">';
                html += '<div>';
                html += '<div class="fw-semibold">' + escHtml(safeTitle) + '</div>';
                if (safeDesc) html += '<div class="small text-muted">' + escHtml(safeDesc) + '</div>';
                if (safeDetail) html += '<div class="small text-muted">' + escHtml(safeDetail) + '</div>';
                if (!hideRef && ev.reference_number) html += '<div class="small">Ref: <span class="font-monospace">' + escHtml(ev.reference_number) + '</span></div>';
                html += '</div>';
                html += '<div class="text-end">';
                html += '<span class="badge text-bg-secondary">' + escHtml(ev.category || '-') + '</span>';
                html += '<div class="small text-muted mt-1">' + escHtml(formatDate(ev.date, true)) + '</div>';
                html += '</div></div></div>';
            });

            html += '</div></div>';
        });

        container.innerHTML = html;
    }

    modeSelect.addEventListener('change', render);
    render();
})();
</script>
<?php endif; ?>
</body>
</html>

