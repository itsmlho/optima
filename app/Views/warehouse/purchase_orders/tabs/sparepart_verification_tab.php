<div class="row">
    <div class="col-md-4">
        <div class="card table-card">
            <div class="card-header text-center">
                <h5 class="fw-bold m-0">Sparepart untuk Diverifikasi</h5>
            </div>
            <div class="list-group list-group-flush" id="sparepart-item-list">
                <?php if (empty($detailGroup)): ?>
                    <div class="list-group-item">Tidak ada sparepart yang perlu diverifikasi.</div>
                <?php else: ?>
                    <?php foreach ($detailGroup as $key => $value): ?>
                        <div class="list-group-item po-group-header" onclick="toggleSparepartDropdown(this)" data-po-id="<?= $key ?>">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($value["no_po"]) ?></h6>
                                    <p class="mb-0 text-muted small">Sisa: <span id="lbl-remain-sparepart-po-<?= $key ?>"><?= count($value["data"]) ?> Item</span></p>
                                </div>
                                <i class="fas fa-chevron-down arrow-icon"></i>
                            </div>
                        </div>
                        <?php foreach ($value['data'] as $item): ?>
                            <a href="#" class="list-group-item list-group-item-action item-child-item child-po-<?= $key ?>" 
                               data-item='<?= json_encode($item) ?>' 
                               id="list-sparepart-item-<?= $item['id'] ?>">
                                <div class="d-flex align-items-center">
                                    <div class="me-3"><i class="fas fa-cogs fa-2x text-secondary"></i></div>
                                    <div class="flex-grow-1" style="min-width: 0;">
                                        <h6 class="mb-1 fw-bold text-truncate" title="<?= esc($item['desc_sparepart'] ?? '') ?>"><?= esc($item['kode'] ?? '') ?></h6>
                                        <p class="mb-0 text-muted small">Qty: <strong><?= esc($item['qty'] ?? '') ?></strong></p>
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
        <div id="sparepart-detail-view-container">
            <div class="card table-card">
                <div class="card-body text-center p-5">
                    <i class="fas fa-hand-pointer fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Pilih sparepart dari daftar di sebelah kiri untuk verifikasi.</h5>
                </div>
            </div>
        </div>
    </div>
</div>

