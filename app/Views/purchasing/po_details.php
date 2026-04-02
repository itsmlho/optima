<?= $this->extend('layouts/base') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">Purchase Order Details</h2>
                <p class="text-muted mb-0">No. PO: <strong><?= esc($po['no_po']) ?></strong></p>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary" onclick="printPO()">
                    <i class="fas fa-print me-1"></i>Print
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="goBack()">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </button>
            </div>
        </div>
    </div>
</div>

<!-- PO Overview -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <strong>PO Number:</strong> <?= esc($po['no_po']) ?>
                    </div>
                    <div class="col-md-6">
                        <strong>PO Date:</strong> <?= date('d/m/Y', strtotime($po['tanggal_po'])) ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Supplier:</strong> <?= esc($supplier['nama_supplier'] ?? 'N/A') ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong> 
                        <span class="badge <?= getStatusBadgeClass($po['status']) ?>"><?= esc($po['status']) ?></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Total Unit Lines:</strong>
                        <?= count($unitItems ?? []) ?> line
                        <?php
                            $totalQty = array_sum(array_column($unitItems ?? [], 'qty'));
                        ?>
                        (<?= $totalQty ?> unit)
                    </div>
                    <div class="col-md-6">
                        <strong>Total Value:</strong> 
                        <span class="fw-bold text-success">
                            <?= number_format($po['total_value'] ?? 0, 0, ',', '.') ?>
                        </span>
                    </div>
                    <?php if (!empty($po['delivery_terms'])): ?>
                    <div class="col-md-6">
                        <strong>Delivery Terms:</strong> <?= esc($po['delivery_terms']) ?>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($po['payment_terms'])): ?>
                    <div class="col-md-6">
                        <strong>Payment Terms:</strong> <?= esc($po['payment_terms']) ?>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($po['keterangan'])): ?>
                    <div class="col-12">
                        <strong>Notes:</strong> <?= esc($po['keterangan']) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Delivery Summary</h5>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <h3 class="text-primary mb-2"><?= count($deliveries) ?></h3>
                    <p class="text-muted mb-3">Total Deliveries</p>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Scheduled:</span>
                        <strong><?= count(array_filter($deliveries, fn($d) => $d['status'] === 'Scheduled')) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>In Transit:</span>
                        <strong><?= count(array_filter($deliveries, fn($d) => $d['status'] === 'In Transit')) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Received:</span>
                        <strong><?= count(array_filter($deliveries, fn($d) => $d['status'] === 'Received')) ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<ul class="nav nav-tabs" id="poDetailsTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="items-tab" data-bs-toggle="tab" data-bs-target="#items" type="button" role="tab">
            <i class="fas fa-list me-1"></i>Items
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="deliveries-tab" data-bs-toggle="tab" data-bs-target="#deliveries" type="button" role="tab">
            <i class="fas fa-truck me-1"></i>Deliveries
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">
            <i class="fas fa-file-alt me-1"></i>Documents
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="poDetailsTabContent">
    <!-- Items Tab -->
    <div class="tab-pane fade show active" id="items" role="tabpanel">
        <div class="card mt-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:3%">#</th>
                                <th>Brand / Model</th>
                                <th>Departemen / Tipe</th>
                                <th>Tahun</th>
                                <th>Kapasitas</th>
                                <th>Kondisi</th>
                                <th class="text-center">Qty</th>
                                <th>Spesifikasi Vendor</th>
                                <th>Paket</th>
                                <th>Status Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($unitItems)): ?>
                                <?php foreach ($unitItems as $idx => $item):
                                    // Parse package_flags (JSON array)
                                    $flags = [];
                                    if (!empty($item['package_flags'])) {
                                        $decoded = json_decode($item['package_flags'], true);
                                        $flags = is_array($decoded) ? $decoded : [];
                                    }
                                    $flagLabels = [
                                        'fork'       => ['Fork',       'bg-secondary'],
                                        'baterai'    => ['Baterai',    'bg-warning text-dark'],
                                        'charger'    => ['Charger',    'bg-info text-dark'],
                                        'attachment' => ['Attachment', 'bg-success'],
                                    ];
                                    // Truncate vendor spec
                                    $specFull = trim($item['vendor_spec_text'] ?? '');
                                    $specShort = mb_strlen($specFull) > 80 ? mb_substr($specFull, 0, 80) . '…' : $specFull;
                                    // Verifikasi badge
                                    $vStatus = $item['status_verifikasi'] ?? 'Belum Dicek';
                                    $vBadge = match($vStatus) {
                                        'Sesuai'        => 'bg-success',
                                        'Tidak Sesuai'  => 'bg-danger',
                                        default         => 'bg-secondary',
                                    };
                                    // Kondisi badge
                                    $kondisi = $item['status_penjualan'] ?? '-';
                                    $kondisiBadge = match($kondisi) {
                                        'Baru'      => 'bg-primary',
                                        'Bekas'     => 'bg-warning text-dark',
                                        'Rekondisi' => 'bg-info text-dark',
                                        default     => 'bg-secondary',
                                    };
                                ?>
                                <tr>
                                    <td class="text-muted small"><?= $idx + 1 ?></td>
                                    <td>
                                        <div class="fw-semibold"><?= esc($item['merk_unit'] ?? '-') ?></div>
                                        <div class="text-muted small"><?= esc($item['model_name'] ?? '-') ?></div>
                                    </td>
                                    <td>
                                        <div><?= esc($item['dept_name'] ?? '-') ?></div>
                                        <div class="text-muted small"><?= esc($item['tipe_name'] ?? '-') ?></div>
                                    </td>
                                    <td><?= $item['tahun_po'] ? esc($item['tahun_po']) : '-' ?></td>
                                    <td><?= esc($item['kapasitas_name'] ?? '-') ?></td>
                                    <td><span class="badge <?= $kondisiBadge ?>"><?= esc($kondisi) ?></span></td>
                                    <td class="text-center fw-bold"><?= (int)($item['qty'] ?? 1) ?></td>
                                    <td>
                                        <?php if ($specShort): ?>
                                            <span title="<?= esc($specFull) ?>" style="cursor:default;font-size:.82em;">
                                                <?= esc($specShort) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php foreach ($flagLabels as $fKey => [$fLabel, $fClass]): ?>
                                            <?php if (in_array($fKey, $flags)): ?>
                                                <span class="badge <?= $fClass ?> me-1"><?= $fLabel ?></span>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        <?php if (!empty($item['unit_accessories'])): ?>
                                            <span class="badge bg-light text-dark border me-1" title="<?= esc($item['unit_accessories']) ?>">Aksesori</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge <?= $vBadge ?>"><?= esc($vStatus) ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        <i class="fas fa-box-open fa-2x mb-2 opacity-50 d-block"></i>
                                        Belum ada unit yang ditambahkan ke PO ini
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Deliveries Tab -->
    <div class="tab-pane fade" id="deliveries" role="tabpanel">
        <div class="card mt-0">
            <div class="card-body">
                <?php if (!empty($deliveries)): ?>
                    <?php foreach ($deliveries as $index => $delivery): ?>
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    Delivery #<?= $delivery['delivery_sequence'] ?> 
                                    <?php if (!empty($delivery['packing_list_no'])): ?>
                                        - <?= esc($delivery['packing_list_no']) ?>
                                    <?php endif; ?>
                                </h6>
                                <span class="badge <?= getDeliveryStatusBadgeClass($delivery['status']) ?>">
                                    <?= esc($delivery['status']) ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <strong>Expected Date:</strong><br>
                                        <?= $delivery['expected_date'] ? date('d/m/Y', strtotime($delivery['expected_date'])) : 'TBD' ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Actual Date:</strong><br>
                                        <?= $delivery['actual_date'] ? date('d/m/Y', strtotime($delivery['actual_date'])) : '-' ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Total Items:</strong><br>
                                        <?= $delivery['total_items'] ?>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Total Value:</strong><br>
                                        <?= number_format($delivery['total_value'], 0, ',', '.') ?>
                                    </div>
                                </div>
                                
                                <?php if (!empty($delivery['keterangan'])): ?>
                                    <div class="mt-3">
                                        <strong>Keterangan:</strong> <?= esc($delivery['keterangan']) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Delivery Items -->
                                <?php if (!empty($delivery['items'])): ?>
                                    <div class="mt-3">
                                        <h6>Items in this delivery:</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Item</th>
                                                        <th>Qty Delivered</th>
                                                        <th>Qty Verified</th>
                                                        <th>Condition</th>
                                                        <th>Verified By</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($delivery['items'] as $item): ?>
                                                        <tr>
                                                            <td><?= esc($item['item_name']) ?></td>
                                                            <td><?= $item['qty_delivered'] ?></td>
                                                            <td><?= $item['qty_verified'] ?></td>
                                                            <td>
                                                                <span class="badge <?= getConditionBadgeClass($item['kondisi_item']) ?>">
                                                                    <?= esc($item['kondisi_item']) ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <?= $item['verified_by'] ? esc($item['verified_by']) : '-' ?>
                                                                <?php if ($item['verified_at']): ?>
                                                                    <br><small class="text-muted"><?= date('d/m/Y H:i', strtotime($item['verified_at'])) ?></small>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Action Buttons -->
                                <div class="mt-3">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="updateDeliveryStatus(<?= $delivery['id_delivery'] ?>)">
                                        <i class="fas fa-edit me-1"></i>Update Status
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="verifyDelivery(<?= $delivery['id_delivery'] ?>)">
                                        <i class="fas fa-check me-1"></i>Verify Items
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-truck fa-3x mb-3 opacity-50"></i>
                        <p>No deliveries scheduled for this PO</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Documents Tab -->
    <div class="tab-pane fade" id="documents" role="tabpanel">
        <div class="card mt-0">
            <div class="card-body">
                <div class="text-center text-muted py-5">
                    <i class="fas fa-file-alt fa-3x mb-3 opacity-50"></i>
                    <p>Document management feature coming soon!</p>
                    <p class="small">This will include packing lists, invoices, and delivery receipts</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Delivery Status Modal -->
<div class="modal fade" id="updateDeliveryStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Update Delivery Status</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="updateDeliveryStatusForm">
                    <input type="hidden" id="deliveryId" name="delivery_id">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="Scheduled">Scheduled</option>
                            <option value="In Transit">In Transit</option>
                            <option value="Received">Received</option>
                            <option value="Partial">Partial</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Actual Date</label>
                        <input type="date" class="form-control" name="actual_date">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveDeliveryStatus()">Save</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>

<script>
// Utility functions
function getStatusBadgeClass(status) {
    const classes = {
        'Pending': 'bg-warning',
        'Approved': 'bg-info',
        'Completed': 'bg-success',
        'Selesai dengan Catatan': 'bg-primary',
        'Cancelled': 'bg-danger'
    };
    return classes[status] || 'bg-secondary';
}

function getItemTypeBadgeClass(type) {
    const classes = {
        'Unit': 'bg-primary',
        'Attachment': 'bg-success',
        'Battery': 'bg-warning',
        'Charger': 'bg-info',
        'Sparepart': 'bg-secondary'
    };
    return classes[type] || 'bg-secondary';
}

function getDeliveryStatusBadgeClass(status) {
    const classes = {
        'Scheduled': 'bg-secondary',
        'In Transit': 'bg-warning',
        'Received': 'bg-success',
        'Partial': 'bg-info',
        'Cancelled': 'bg-danger'
    };
    return classes[status] || 'bg-secondary';
}

function getConditionBadgeClass(condition) {
    const classes = {
        'Baik': 'bg-success',
        'Rusak': 'bg-danger',
        'Kurang': 'bg-warning',
        'Belum Dicek': 'bg-secondary'
    };
    return classes[condition] || 'bg-secondary';
}

// Action functions
function printPO() {
    window.print();
}

function goBack() {
    window.history.back();
}

function updateDeliveryStatus(deliveryId) {
    $('#deliveryId').val(deliveryId);
    $('#updateDeliveryStatusModal').modal('show');
}

function saveDeliveryStatus() {
    const formData = new FormData(document.getElementById('updateDeliveryStatusForm'));
    
    $.ajax({
        url: '<?= base_url('/purchasing/update-delivery-status') ?>',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                OptimaNotify.success(response.message);
                setTimeout(() => location.reload(), 1200);
            } else {
                OptimaNotify.error(response.message);
            }
        },
        error: function() {
            OptimaNotify.error('Failed to update delivery status');
        }
    });
}

function verifyDelivery(deliveryId) {
    OptimaNotify.info('Delivery verification feature coming soon!');
}
</script>
<?= $this->endSection() ?>

<?php
// Helper functions for PHP
function getStatusBadgeClass($status) {
    $classes = [
        'Pending' => 'bg-warning',
        'Approved' => 'bg-info',
        'Completed' => 'bg-success',
        'Selesai dengan Catatan' => 'bg-primary',
        'Cancelled' => 'bg-danger'
    ];
    return $classes[$status] ?? 'bg-secondary';
}

function getItemTypeBadgeClass($type) {
    $classes = [
        'Unit' => 'bg-primary',
        'Attachment' => 'bg-success',
        'Battery' => 'bg-warning',
        'Charger' => 'bg-info',
        'Sparepart' => 'bg-secondary'
    ];
    return $classes[$type] ?? 'bg-secondary';
}

function getDeliveryStatusBadgeClass($status) {
    $classes = [
        'Scheduled' => 'bg-secondary',
        'In Transit' => 'bg-warning',
        'Received' => 'bg-success',
        'Partial' => 'bg-info',
        'Cancelled' => 'bg-danger'
    ];
    return $classes[$status] ?? 'bg-secondary';
}

function getConditionBadgeClass($condition) {
    $classes = [
        'Baik' => 'bg-success',
        'Rusak' => 'bg-danger',
        'Kurang' => 'bg-warning',
        'Belum Dicek' => 'bg-secondary'
    ];
    return $classes[$condition] ?? 'bg-secondary';
}
?>
