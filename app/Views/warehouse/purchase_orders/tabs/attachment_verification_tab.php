<div class="row">
    <div class="col-md-4">
        <div class="card table-card">
            <div class="card-header text-center">
                <h5 class="fw-bold m-0">Item untuk Diverifikasi</h5>
            </div>
            <div class="list-group list-group-flush" id="attachment-item-list">
                <?php if (empty($detailGroup)): ?>
                    <div class="list-group-item">Tidak ada item yang perlu diverifikasi.</div>
                <?php else: ?>
                    <?php foreach ($detailGroup as $key => $value): ?>
                        <div class="list-group-item po-group-header" onclick="toggleAttachmentDropdown(this)" data-po-id="<?= $key ?>">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 fw-bold">
                                        <?= htmlspecialchars($value["no_po"]) ?>
                                    </h6>
                                    <p class="mb-0 text-muted small">
                                        <?php if (!empty($value["data"][0]["packing_list_no"])): ?>
                                            Packing List: <?= htmlspecialchars($value["data"][0]["packing_list_no"]) ?>
                                        <?php endif; ?>
                                    </p>
                                    <p class="mb-0 text-muted small">
                                        Sisa: <span id="lbl-remain-attachment-po-<?= $key ?>"><?= count($value["data"]) ?> Item</span>
                                        <?php if (!empty($value["data"][0]["tanggal_datang"])): ?>
                                            | <span class="text-info"><i class="fas fa-truck me-1"></i>Received: <?= date('d/m/Y', strtotime($value["data"][0]["tanggal_datang"])) ?></span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <i class="fas fa-chevron-down arrow-icon"></i>
                            </div>
                        </div>
                        <?php foreach ($value['data'] as $item): ?>
                            <a href="#" class="list-group-item list-group-item-action item-child-item child-po-<?= $key ?>" 
                               data-item='<?= json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP) ?>' 
                               id="list-attachment-item-<?= $item['id_po_attachment'] ?? $item['id_po_attachment'] ?>">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <?php if (($item['item_type'] ?? '') === 'Attachment'): ?>
                                            <i class="fas fa-paperclip fa-2x text-secondary"></i>
                                        <?php else: ?>
                                            <i class="fas fa-battery-full fa-2x text-success"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1" style="min-width: 0;">
                                        <?php 
                                            if (($item['item_type'] ?? '') === 'Attachment') {
                                                $itemName = ($item['merk_attachment'] ?? 'Unknown') . ' | ' . ($item['model_attachment'] ?? 'Unknown') . ' - ' . ($item['tipe_attachment'] ?? 'Unknown');
                                                $badgeColor = 'bg-secondary';
                                            } elseif (($item['item_type'] ?? '') === 'Battery') {
                                                $itemName = ($item['merk_battery'] ?? 'Unknown') . ' | ' . ($item['tipe_battery'] ?? 'Unknown') . ' | ' . ($item['jenis_battery'] ?? 'Unknown');
                                                $badgeColor = 'bg-warning';
                                            } elseif (($item['item_type'] ?? '') === 'Charger') {
                                                $itemName = ($item['merk_charger'] ?? 'Unknown') . ' | ' . ($item['tipe_charger'] ?? 'Unknown');
                                                $badgeColor = 'bg-success';
                                            } else {
                                                $itemName = 'Unknown Item';
                                                $badgeColor = 'bg-secondary';
                                            }
                                        ?>
                                        <h6 class="mb-1 fw-bold text-truncate" title="<?= esc($itemName) ?>"><?= esc($itemName) ?></h6>
                                        <p class="mb-0 text-muted small">
                                            <span class="badge <?= $badgeColor ?> me-1"><?= esc($item['item_type'] ?? 'Unknown') ?></span>
                                            <?php if (!empty($item['serial_number'])): ?>
                                                <span class="text-info">SN: <?= esc($item['serial_number']) ?></span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div id="attachment-detail-view-container">
            <div class="card table-card">
                <div class="card-body text-center p-5">
                    <i class="fas fa-hand-pointer fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Pilih item dari daftar di sebelah kiri untuk verifikasi.</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Verifikasi Attachment -->
<div class="modal fade modal-wide" id="modalAttachmentVerification" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content" style="border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
            <div class="modal-header" style="background-color: #ffffff; color: #374151; border-bottom: 1px solid #e5e7eb; padding: 20px 24px;">
                <div style="display: flex; align-items: center; width: 100%;">
                    <div style="width: 40px; height: 40px; background-color: #f3f4f6; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                        <i class="fas fa-clipboard-check" style="font-size: 1.2rem; color: #6b7280;"></i>
                    </div>
                    <div style="flex: 1;">
                        <h5 class="modal-title mb-0" id="modalAttachmentVerificationLabel" style="font-size: 1.1rem; font-weight: 600; color: #111827;">Inspeksi Item</h5>
                        <small style="color: #6b7280; font-size: 0.85rem;">Verifikasi item dan tentukan lokasi penyimpanan</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 1rem;"></button>
            </div>
            <div class="modal-body" style="padding: 24px; background-color: #ffffff;">
                <form id="formAttachmentVerification">
                    <input type="hidden" id="attachment_item_id">
                    <input type="hidden" id="attachment_po_id">
                    <p class="text-muted mb-4">Periksa setiap komponen di bawah ini dan isi informasi yang diperlukan.</p>
                    <div id="attachment-verification-components"></div>
                </form>
            </div>
            <div class="modal-footer" style="background-color: #ffffff; border-top: 1px solid #e5e7eb; padding: 16px 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                    <div style="color: #6b7280; font-size: 0.9rem;">
                        <i class="fas fa-info-circle me-2" style="color: #6b7280;"></i>
                        Pastikan item diverifikasi sebelum submit
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" 
                                style="padding: 8px 16px; border-radius: 6px; font-weight: 500;">
                            Batal
                        </button>
                        <button type="button" class="btn btn-primary" id="btn-submit-attachment-verification" disabled 
                                style="padding: 8px 20px; border-radius: 6px; font-weight: 500;">
                            Submit Verifikasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

