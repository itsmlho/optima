<?= $this->extend('layouts/base') ?>

<?php
/**
 * Warehouse Dashboard (Index) - Warehouse
 * CARD: card-header bg-light, table mb-0, stat-card already used.
 */
?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4"><?= $page_title ?></h2>
            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card stat-card bg-primary-soft">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="bi bi-box-seam stat-icon text-primary"></i>
                                </div>
                                <div>
                                    <div class="stat-value"><?= $warehouse_stats['total_units'] ?? 0 ?></div>
                                    <div class="text-muted">Total Units</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card stat-card bg-success-soft">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="bi bi-tools stat-icon text-success"></i>
                                </div>
                                <div>
                                    <div class="stat-value"><?= $warehouse_stats['total_attachments'] ?? 0 ?></div>
                                    <div class="text-muted">Total Attachments</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card stat-card bg-warning-soft">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="bi bi-gear stat-icon text-warning"></i>
                                </div>
                                <div>
                                    <div class="stat-value"><?= $warehouse_stats['total_spareparts'] ?? 0 ?></div>
                                    <div class="text-muted">Total Spareparts</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card stat-card bg-danger-soft">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="bi bi-exclamation-triangle stat-icon text-danger"></i>
                                </div>
                                <div>
                                    <div class="stat-value"><?= count($low_stock_alerts ?? []) ?></div>
                                    <div class="text-muted">Low Stock Items</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Access -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bi bi-grid-3x3-gap me-2"></i>Quick Access</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <a href="<?= base_url('warehouse/inventory-unit') ?>" class="btn btn-outline-primary w-100 py-3">
                                        <i class="bi bi-box-seam fs-4 d-block mb-2"></i>
                                        <strong>Unit Inventory</strong>
                                    </a>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <a href="<?= base_url('warehouse/inventory-attachment') ?>" class="btn btn-outline-success w-100 py-3">
                                        <i class="bi bi-tools fs-4 d-block mb-2"></i>
                                        <strong>Attachment Inventory</strong>
                                    </a>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <a href="<?= base_url('warehouse/inventory-sparepart') ?>" class="btn btn-outline-warning w-100 py-3">
                                        <i class="bi bi-gear fs-4 d-block mb-2"></i>
                                        <strong>Sparepart Inventory</strong>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Transactions -->
            <?php if (!empty($recent_transactions)): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Transactions</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Item</th>
                                            <th>Quantity</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_transactions as $transaction): ?>
                                        <tr>
                                            <td><?= date('d M Y H:i', strtotime($transaction['date'] ?? 'now')) ?></td>
                                            <td><?= $transaction['type'] ?? '-' ?></td>
                                            <td><?= $transaction['item'] ?? '-' ?></td>
                                            <td><?= $transaction['quantity'] ?? '-' ?></td>
                                            <td>
                                                <span class="badge bg-<?= ($transaction['status'] ?? 'secondary') === 'completed' ? 'success' : 'warning' ?>">
                                                    <?= ucfirst($transaction['status'] ?? 'pending') ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Low Stock Alerts -->
            <?php if (!empty($low_stock_alerts)): ?>
            <div class="row">
                <div class="col-12">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Low Stock Alerts</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <?php foreach ($low_stock_alerts as $alert): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= $alert['item_name'] ?? '-' ?></strong>
                                            <span class="text-muted ms-2">(<?= $alert['item_code'] ?? '-' ?>)</span>
                                        </div>
                                        <span class="badge bg-danger">Stock: <?= $alert['quantity'] ?? 0 ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Auto-trigger modal if autoOpenAttachmentId or autoOpenUnitId is set (from notification deep linking)
<?php if (isset($autoOpenAttachmentId) && $autoOpenAttachmentId): ?>
console.log('🔔 Auto-redirect to attachment detail from notification: <?= $autoOpenAttachmentId ?>');
setTimeout(() => {
    // Redirect to attachment inventory page with auto-open parameter
    window.location.href = '<?= base_url('warehouse/inventory-attachment?auto_open=') ?><?= $autoOpenAttachmentId ?>';
}, 500);
<?php elseif (isset($autoOpenUnitId) && $autoOpenUnitId): ?>
console.log('🔔 Auto-redirect to unit detail from notification: <?= $autoOpenUnitId ?>');
setTimeout(() => {
    // Redirect to unit inventory page with auto-open parameter
    window.location.href = '<?= base_url('warehouse/inventory-unit?auto_open=') ?><?= $autoOpenUnitId ?>';
}, 500);
<?php endif; ?>
</script>

<?= $this->endSection() ?>
