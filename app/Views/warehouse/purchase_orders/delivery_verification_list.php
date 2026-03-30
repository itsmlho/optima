<?php
/** @var list<array<string, mixed>> $deliveryGroups */
$deliveryGroups = $deliveryGroups ?? [];
$jsonFlags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
?>
<div class="row">
    <div class="col-12 col-xl-5">
        <div class="card table-card mb-3">
            <div class="card-header text-center py-2">
                <h5 class="fw-bold m-0">Packing list (Received) — antrean verifikasi</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Packing list</th>
                                <th>PO</th>
                                <th>Received</th>
                                <th class="text-center">Sisa</th>
                                <th class="text-end" style="width: 90px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($deliveryGroups === []): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Tidak ada baris delivery yang menunggu verifikasi.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($deliveryGroups as $idx => $dg): ?>
                                    <?php
                                    $meta = $dg['meta'] ?? [];
                                    $did = (int) ($meta['id_delivery'] ?? 0);
                                    $collapseId = 'pl-expand-' . $did . '-' . $idx;
                                    $plNo = $meta['packing_list_no'] ?? '';
                                    $noPo = $meta['no_po'] ?? '';
                                    $tanggal = $meta['tanggal_datang'] ?? null;
                                    if (empty($tanggal) && ! empty($meta['updated_at'])) {
                                        $tanggal = $meta['updated_at'];
                                    }
                                    $tanggalStr = '';
                                    if (! empty($tanggal)) {
                                        $tanggalStr = date('d/m/Y', strtotime((string) $tanggal));
                                    }
                                    $pending = (int) ($dg['pending_count'] ?? 0);
                                    $bundles = $dg['bundles'] ?? [];
                                    $orphans = $dg['orphans'] ?? [];
                                    ?>
                                    <tr class="table-light">
                                        <td class="fw-semibold"><?= esc($plNo !== '' ? $plNo : '—') ?></td>
                                        <td><?= esc($noPo) ?></td>
                                        <td class="small text-muted"><?= esc($tanggalStr !== '' ? $tanggalStr : '—') ?></td>
                                        <td class="text-center">
                                            <span id="lbl-remain-pl-<?= $did ?>"><?= $pending ?></span>
                                            <span class="text-muted small"> item</span>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#<?= esc($collapseId, 'attr') ?>"
                                                    aria-expanded="false"
                                                    aria-controls="<?= esc($collapseId, 'attr') ?>">
                                                Expand
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="collapse-row">
                                        <td colspan="5" class="p-0 border-0">
                                            <div class="collapse" id="<?= esc($collapseId, 'attr') ?>">
                                                <div class="border-bottom bg-white px-3 py-2">
                                                    <?php if ($bundles === [] && $orphans === []): ?>
                                                        <span class="text-muted small">Tidak ada paket pada baris ini.</span>
                                                    <?php else: ?>
                                                        <div class="small text-muted mb-2">Pilih paket unit atau aksesoris tanpa unit:</div>
                                                        <div class="list-group list-group-flush">
                                                            <?php foreach ($bundles as $bi => $b): ?>
                                                                <?php
                                                                $u = $b['unit'] ?? [];
                                                                $uid = (int) ($u['id_po_unit'] ?? 0);
                                                                $label = trim(($u['merk_unit'] ?? 'Unit') . ' | ' . ($u['model_unit'] ?? ''));
                                                                $payload = [
                                                                    'id_delivery' => $did,
                                                                    'unit' => $u,
                                                                    'embed_accessories' => $b['embed_accessories'] ?? [],
                                                                    'accessories' => $b['accessories'] ?? [],
                                                                ];
                                                                ?>
                                                                <button type="button"
                                                                        class="list-group-item list-group-item-action wh-bundle-pick text-start"
                                                                        id="bundle-line-d<?= $did ?>-u<?= $uid ?>"
                                                                        data-bundle="<?= htmlspecialchars(json_encode($payload, $jsonFlags), ENT_QUOTES, 'UTF-8') ?>">
                                                                    <span class="fw-semibold"><?= esc($label) ?></span>
                                                                    <span class="text-muted small d-block"><?= esc(($u['jenis'] ?? '') . ' · ' . ($u['nama_departemen'] ?? '')) ?></span>
                                                                </button>
                                                            <?php endforeach; ?>
                                                            <?php foreach ($orphans as $oi => $att): ?>
                                                                <?php
                                                                $aid = (int) ($att['id_po_attachment'] ?? 0);
                                                                $payload = [
                                                                    'id_delivery' => $did,
                                                                    'orphan_attachment' => $att,
                                                                ];
                                                                $t = strtolower((string) ($att['item_type'] ?? ''));
                                                                $olabel = 'Aksesoris (tanpa unit)';
                                                                if ($t === 'battery') {
                                                                    $olabel = 'Baterai (tanpa unit)';
                                                                } elseif ($t === 'charger') {
                                                                    $olabel = 'Charger (tanpa unit)';
                                                                } elseif ($t === 'attachment') {
                                                                    $olabel = 'Attachment (tanpa unit)';
                                                                }
                                                                ?>
                                                                <button type="button"
                                                                        class="list-group-item list-group-item-action wh-orphan-pick text-start"
                                                                        id="orphan-line-d<?= $did ?>-a<?= $aid ?>"
                                                                        data-orphan="<?= htmlspecialchars(json_encode($payload, $jsonFlags), ENT_QUOTES, 'UTF-8') ?>">
                                                                    <span class="fw-semibold text-warning"><?= esc($olabel) ?></span>
                                                                    <span class="text-muted small d-block">#<?= $aid ?></span>
                                                                </button>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-7">
        <div id="wh-verification-detail-container">
            <div class="card table-card">
                <div class="card-body text-center p-5">
                    <i class="fas fa-hand-pointer fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Pilih packing list → paket unit atau aksesoris dari daftar kiri.</h5>
                </div>
            </div>
        </div>
    </div>
</div>
