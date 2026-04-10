<?php
/** @var list<array<string, mixed>> $deliveryGroups */
$deliveryGroups = $deliveryGroups ?? [];
$jsonFlags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;

$queueRows = [];
$poList = [];

foreach ($deliveryGroups as $dg) {
    $meta = $dg['meta'] ?? [];
    $did = (int) ($meta['id_delivery'] ?? 0);
    if ($did <= 0) {
        continue;
    }
    $noPo = trim((string) ($meta['no_po'] ?? ''));
    if ($noPo !== '') {
        $poList[$noPo] = true;
    }

    foreach ($dg['bundles'] ?? [] as $b) {
        $queueRows[] = [
            'type' => 'bundle',
            'delivery_id' => $did,
            'meta' => $meta,
            'bundle' => $b,
        ];
    }
    foreach ($dg['orphans'] ?? [] as $att) {
        $queueRows[] = [
            'type' => 'orphan',
            'delivery_id' => $did,
            'meta' => $meta,
            'orphan' => $att,
        ];
    }
}

$poOptions = array_keys($poList);
sort($poOptions, SORT_STRING);
// Satu baris per delivery (PL) dengan no_po eksak untuk dropdown berjenjang
$plOptions = [];
foreach ($deliveryGroups as $dg) {
    $meta = $dg['meta'] ?? [];
    $did = (int) ($meta['id_delivery'] ?? 0);
    if ($did <= 0) {
        continue;
    }
    $noPoRow = trim((string) ($meta['no_po'] ?? ''));
    $plNoRow = trim((string) ($meta['packing_list_no'] ?? ''));
    $plTitle = $plNoRow !== '' ? $plNoRow : ('Delivery #' . $did);
    $plOptions[] = [
        'id' => $did,
        'no_po' => $noPoRow,
        'pl_no' => $plNoRow,
        'label' => $plTitle . ($noPoRow !== '' ? ' · PO ' . $noPoRow : ''),
    ];
}
usort($plOptions, static function ($a, $b) {
    $c = strcmp($a['no_po'] ?? '', $b['no_po'] ?? '');
    return $c !== 0 ? $c : strcmp($a['label'], $b['label']);
});

$totalQueue = count($queueRows);

$pendingByDelivery = [];
foreach ($deliveryGroups as $dg) {
    $did = (int) (($dg['meta'] ?? [])['id_delivery'] ?? 0);
    if ($did > 0) {
        $pendingByDelivery[$did] = (int) ($dg['pending_count'] ?? 0);
    }
}
?>
<input type="hidden" id="wh-verify-total-queue" value="<?= (int) $totalQueue ?>">

<div class="row g-2 mb-3 align-items-end">
    <div class="col-12 col-md-4 col-lg-3">
        <label for="whFilterPo" class="form-label small text-muted mb-0">Nomor PO</label>
        <select id="whFilterPo" class="form-select form-select-sm" title="Filter baris tabel berdasarkan purchase order">
            <option value="">— Semua PO —</option>
            <?php foreach ($poOptions as $po): ?>
                <option value="<?= esc($po, 'attr') ?>"><?= esc($po) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-12 col-md-4 col-lg-3">
        <label for="whFilterPl" class="form-label small text-muted mb-0">Packing list (Received)</label>
        <select id="whFilterPl" class="form-select form-select-sm" title="Pilih PL untuk PO yang dipilih, atau semua">
            <option value="" data-po="">— Semua packing list —</option>
            <?php foreach ($plOptions as $pl): ?>
                <option value="<?= (int) $pl['id'] ?>" data-po="<?= esc($pl['no_po'], 'attr') ?>" data-pl-no="<?= esc($pl['pl_no'], 'attr') ?>">
                    <?= esc($pl['label']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-12 col-md-4 col-lg-4">
        <label for="whFilterSearch" class="form-label small text-muted mb-0">Cari</label>
        <input type="search" id="whFilterSearch" class="form-control form-control-sm" placeholder="Unit, PL, PO, jenis…" autocomplete="off">
    </div>
</div>

<div class="card table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle" id="whVerificationQueueTable">
                <thead>
                    <tr>
                        <th>Packing list</th>
                        <th>PO</th>
                        <th>Received</th>
                        <th>Jenis</th>
                        <th>Deskripsi</th>
                        <th class="text-end" style="width: 96px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="whVerificationQueueBody">
                    <?php if ($queueRows === []): ?>
                        <tr class="wh-queue-empty">
                            <td colspan="6" class="text-center text-muted py-4">Tidak ada baris yang menunggu verifikasi.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($queueRows as $qr): ?>
                            <?php
                            $meta = $qr['meta'];
                            $did = (int) $qr['delivery_id'];
                            $noPo = $meta['no_po'] ?? '';
                            $plNo = $meta['packing_list_no'] ?? '';
                            $tanggal = $meta['tanggal_datang'] ?? null;
                            if (empty($tanggal) && ! empty($meta['updated_at'])) {
                                $tanggal = $meta['updated_at'];
                            }
                            $tanggalStr = '';
                            if (! empty($tanggal)) {
                                $tanggalStr = date('d/m/Y', strtotime((string) $tanggal));
                            }
                            $searchParts = [$plNo, $noPo];

                            if ($qr['type'] === 'bundle') {
                                $b = $qr['bundle'];
                                $u = $b['unit'] ?? [];
                                $uid = (int) ($u['id_po_unit'] ?? 0);
                                $label = trim(($u['merk_unit'] ?? 'Unit') . ' ' . ($u['model_unit'] ?? ''));
                                $sub = trim(($u['jenis'] ?? '') . ' · ' . ($u['nama_departemen'] ?? ''));
                                $searchParts[] = $label;
                                $searchParts[] = $sub;
                                $payload = [
                                    'id_delivery' => $did,
                                    'unit' => $u,
                                    'embed_accessories' => $b['embed_accessories'] ?? [],
                                    'accessories' => $b['accessories'] ?? [],
                                ];
                                $rowId = 'wh-queue-bundle-d' . $did . '-u' . $uid;
                                $kindLabel = 'Unit + paket';
                                $titleModal = 'Verifikasi unit — ' . $label;
                            } else {
                                $att = $qr['orphan'];
                                $aid = (int) ($att['id_po_attachment'] ?? 0);
                                $t = strtolower((string) ($att['item_type'] ?? ''));
                                $kindLabel = 'Baris PI terpisah';
                                if ($t === 'battery') {
                                    $kindLabel = 'Baterai (terpisah)';
                                } elseif ($t === 'charger') {
                                    $kindLabel = 'Charger (terpisah)';
                                } elseif ($t === 'attachment') {
                                    $kindLabel = 'Attachment (terpisah)';
                                }
                                $desc = trim(
                                    ($att['merk_attachment'] ?? $att['merk_battery'] ?? $att['merk_charger'] ?? '') . ' '
                                    . ($att['model_attachment'] ?? $att['tipe_battery'] ?? $att['tipe_charger'] ?? '')
                                );
                                if ($desc === '') {
                                    $desc = '#' . $aid;
                                }
                                $searchParts[] = $kindLabel;
                                $searchParts[] = $desc;
                                $payload = [
                                    'id_delivery' => $did,
                                    'orphan_attachment' => $att,
                                ];
                                $rowId = 'wh-queue-orphan-d' . $did . '-a' . $aid;
                                $titleModal = 'Verifikasi ' . $kindLabel . ' — ' . $desc;
                                $sub = 'ID barang #' . $aid;
                            }
                            $searchHaystack = mb_strtolower(implode(' ', array_filter($searchParts)));
                            ?>
                            <tr class="wh-queue-row"
                                id="<?= esc($rowId, 'attr') ?>"
                                data-po="<?= esc($noPo, 'attr') ?>"
                                data-delivery-id="<?= $did ?>"
                                data-search="<?= esc($searchHaystack, 'attr') ?>"
                                data-verify-kind="<?= esc($qr['type'], 'attr') ?>"
                                data-modal-title="<?= esc($titleModal, 'attr') ?>"
                                data-payload="<?= htmlspecialchars(json_encode($payload, $jsonFlags), ENT_QUOTES, 'UTF-8') ?>">
                                <td class="fw-semibold"><?= esc($plNo !== '' ? $plNo : '—') ?></td>
                                <td><?= esc($noPo) ?></td>
                                <td class="small text-muted"><?= esc($tanggalStr !== '' ? $tanggalStr : '—') ?></td>
                                <td><span class="badge badge-soft-primary"><?= esc($kindLabel) ?></span></td>
                                <td class="small">
                                    <?php if ($qr['type'] === 'bundle'): ?>
                                        <div class="fw-semibold"><?= esc($label) ?></div>
                                        <div class="text-muted"><?= esc($sub) ?></div>
                                    <?php else: ?>
                                        <div class="fw-semibold"><?= esc($desc) ?></div>
                                        <div class="text-muted"><?= esc($sub) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <span class="d-inline-block text-muted small me-1 lbl-remain-pl-inline" data-delivery-id="<?= $did ?>">
                                        <span class="wh-lbl-remain-pl" data-delivery-id="<?= $did ?>"><?= (int) ($pendingByDelivery[$did] ?? 0) ?></span>
                                    </span>
                                    <button type="button" class="btn btn-sm btn-primary wh-btn-open-verify">Verify</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
window.whPendingByDelivery = <?= json_encode($pendingByDelivery, JSON_UNESCAPED_UNICODE) ?>;
</script>
