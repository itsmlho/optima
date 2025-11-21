<div class="row">
    <div class="col-md-4">
        <div class="card table-card">
            <div class="card-header text-center">
                <h5 class="fw-bold m-0">Unit untuk Diverifikasi</h5>
            </div>
            <div class="list-group list-group-flush" id="unit-list">
                <?php if (empty($detailGroup)): ?>
                    <div class="list-group-item">Tidak ada unit yang perlu diverifikasi.</div>
                <?php else: ?>
                    <?php foreach ($detailGroup as $key => $value): ?>
                        <div class="list-group-item po-group-header" onclick="toggleUnitDropdown(this)" data-po-id="<?= $key ?>">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 fw-bold">
                                        <?= htmlspecialchars($value["no_po"]) ?>
                                    </h6>
                                    <p class="mb-0 text-muted small">
                                        <?php 
                                        // Find first item with packing_list_no
                                        $packingListNo = null;
                                        foreach ($value["data"] as $item) {
                                            if (!empty($item["packing_list_no"])) {
                                                $packingListNo = $item["packing_list_no"];
                                                break;
                                            }
                                        }
                                        if ($packingListNo): ?>
                                            Packing List: <?= htmlspecialchars($packingListNo) ?>
                                        <?php endif; ?>
                                        
                                    </p>
                                    <p class="mb-0 text-muted small">
                                        Sisa: <span id="lbl-remain-po-<?= $key ?>"><?= count($value["data"]) ?> Unit</span>
                                        <?php 
                                        // Find first item with tanggal_datang (non-null and non-empty)
                                        // Also check updated_at as fallback if actual_date is NULL (for old data)
                                        $tanggalDatang = null;
                                        foreach ($value["data"] as $item) {
                                            // First priority: use actual_date if available
                                            if (!empty($item["tanggal_datang"]) && $item["tanggal_datang"] !== null && trim($item["tanggal_datang"]) !== '') {
                                                $tanggalDatang = $item["tanggal_datang"];
                                                break;
                                            }
                                        }
                                        // Fallback: if no actual_date found, use updated_at from delivery with status "Received"
                                        if (empty($tanggalDatang)) {
                                            foreach ($value["data"] as $item) {
                                                if (!empty($item["updated_at"]) && isset($item["delivery_status"]) && $item["delivery_status"] === "Received") {
                                                    $tanggalDatang = $item["updated_at"];
                                                    break;
                                                }
                                            }
                                        }
                                        if ($tanggalDatang): ?>
                                            | <span class="text-info"><i class="fas fa-truck me-1"></i>Received: <?= date('d/m/Y', strtotime($tanggalDatang)) ?></span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <i class="fas fa-chevron-down arrow-icon"></i>
                            </div>
                        </div>
                        <?php foreach ($value['data'] as $unit): ?>
                            <a href="#" class="list-group-item list-group-item-action unit-list-item unit-child-item child-po-<?= $key ?>" 
                               data-unit='<?= json_encode($unit) ?>' 
                               id="list-item-<?= $unit['id_po_unit'] ?>">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1" style="min-width: 0;">
                                        <h6 class="mb-1 fw-bold text-truncate" title="<?= esc(($unit['merk_unit'] ?? 'Unknown') . ' | ' . ($unit['model_unit'] ?? 'Unknown')) ?>">
                                            <?= htmlspecialchars(($unit['merk_unit'] ?? 'Unknown') . ' | ' . ($unit['model_unit'] ?? 'Unknown')) ?>
                                        </h6>
                                        
                                        <p class="mb-0 text-muted small">
                                            <?= htmlspecialchars(($unit['jenis'] ?? 'Unknown') . ' | ' . ($unit['nama_departemen'] ?? 'Unknown') . ' | ' . ($unit['kapasitas_unit'] ?? 'Unknown')) ?>
                                             <?php if (!empty($unit['serial_number_po'])): ?>
                                                <span class="text-info"> - SN: <?= esc($unit['serial_number_po']) ?></span>
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
        <div id="unit-detail-view-container">
            <div class="card table-card">
                <div class="card-body text-center p-5">
                    <i class="fas fa-hand-pointer fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Pilih unit dari daftar di sebelah kiri untuk verifikasi.</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Verifikasi Unit -->
<div class="modal fade" id="modalUpdateSN" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
            <div class="modal-header" style="background-color: #ffffff; color: #374151; border-bottom: 1px solid #e5e7eb; padding: 20px 24px;">
                <div style="display: flex; align-items: center; width: 100%;">
                    <div style="width: 40px; height: 40px; background-color: #f3f4f6; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                        <i class="fas fa-clipboard-check" style="font-size: 1.2rem; color: #6b7280;"></i>
                    </div>
                    <div style="flex: 1;">
                        <h5 class="modal-title mb-0" id="modalUpdateSNLabel" style="font-size: 1.1rem; font-weight: 600; color: #111827;">Inspeksi Unit</h5>
                        <small style="color: #6b7280; font-size: 0.85rem;">Verifikasi komponen unit dan tentukan lokasi penyimpanan</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 1rem;"></button>
            </div>
            <div class="modal-body" style="padding: 24px; background-color: #ffffff;">
                <form id="formUpdateSN">
                    <input type="hidden" id="unit_id">
                    <input type="hidden" id="unit_po_id">
                    <div id="unit-verification-components"></div>
                </form>
            </div>
            <div class="modal-footer" style="background-color: #ffffff; border-top: 1px solid #e5e7eb; padding: 16px 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                    <div style="color: #6b7280; font-size: 0.9rem;">
                        <i class="fas fa-info-circle me-2" style="color: #6b7280;"></i>
                        Pastikan semua komponen diverifikasi sebelum submit
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" 
                                style="padding: 8px 16px; border-radius: 6px; font-weight: 500;">
                            Batal
                        </button>
                        <button type="button" class="btn btn-primary" id="btn-submit-unit-verification" disabled 
                                style="padding: 8px 20px; border-radius: 6px; font-weight: 500;">
                            Verifikasi Unit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

