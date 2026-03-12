<?= $this->extend('layouts/base') ?>

<?php 
// Load helpers
helper('global_permission');
helper('date');

// Get permissions for warehouse module
$permissions = get_global_permission('warehouse');
$can_view = $permissions['view'];
$can_create = $permissions['create'];
$can_edit = $permissions['edit'];
$can_delete = $permissions['delete'];
$can_export = $permissions['export'];
?>

<?= $this->section('content') ?>

<div class="row mt-3">
    <div class="col-md-12 text-end">
        <div class="d-inline-block">
            <?= view('components/date_range_filter', [
                'id' => 'rejectedItemsDateRangePicker'
            ]) ?>
        </div>
    </div>
</div>


    <!-- Statistics Cards -->
    <div class="row mt-3 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-danger-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-truck stat-icon text-danger"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= count($rejected_units) ?></div>
                        <div class="text-muted">Unit</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-danger-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-puzzle stat-icon text-danger"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= count($rejected_attachments) ?></div>
                        <div class="text-muted">Attachment</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-danger-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-gear stat-icon text-danger"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= count($rejected_spareparts) ?></div>
                        <div class="text-muted">Sparepart</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-primary-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-exclamation-triangle stat-icon text-primary"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= $total_rejected ?></div>
                        <div class="text-muted">Total</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3" id="rejectedTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="unit-tab" data-tab="unit" type="button" role="tab" onclick="switchRejectedTab('unit', this)">
                <i class="fas fa-truck me-1"></i>Unit (<?= count($rejected_units) ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="attachment-tab" data-tab="attachment" type="button" role="tab" onclick="switchRejectedTab('attachment', this)">
                <i class="fas fa-puzzle-piece me-1"></i>Attachment (<?= count($rejected_attachments) ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sparepart-tab" data-tab="sparepart" type="button" role="tab" onclick="switchRejectedTab('sparepart', this)">
                <i class="fas fa-cogs me-1"></i>Sparepart (<?= count($rejected_spareparts) ?>)
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="rejectedTabsContent">
        <!-- Unit Tab -->
        <div class="tab-pane fade show active" id="unit-rejected" role="tabpanel" aria-labelledby="unit-tab">
            <?php if (empty($rejected_units)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No rejected units found.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($rejected_units as $unit): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card border-start border-danger border-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1"><?= esc($unit['no_po']) ?></h6>
                                            <small class="text-muted"><?= esc($unit['merk_unit'] ?? '') ?> <?= esc($unit['model_unit'] ?? '') ?></small>
                                        </div>
                                        <span class="badge badge-soft-red">Unit</span>
                                    </div>
                                    
                                    <!-- Informasi Tambahan -->
                                    <div class="row g-2 mb-2 small">
                                        <?php if (!empty($unit['packing_list_no'])): ?>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Packing List:</small>
                                            <strong><?= esc($unit['packing_list_no']) ?></strong>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($unit['tanggal_sampai'])): ?>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Arrival Date:</small>
                                            <strong><?= date('d/m/Y', strtotime($unit['tanggal_sampai'])) ?></strong>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="col-6">
                                            <small class="text-muted d-block">Verification Date:</small>
                                            <strong><?= format_date_jakarta($unit['tanggal_verifikasi'] ?? null) ?></strong>
                                        </div>
                                        
                                        <div class="col-6">
                                            <small class="text-muted d-block">Verified By:</small>
                                            <strong>
                                                <?php 
                                                $verifierName = trim(($unit['verified_by_name'] ?? '') . ' ' . ($unit['verified_by_lastname'] ?? ''));
                                                if (!empty($verifierName)): 
                                                    echo esc($verifierName);
                                                else: 
                                                    echo '<span class="text-muted">-</span>';
                                                endif; 
                                                ?>
                                            </strong>
                                        </div>
                                    </div>
                                    
                                    <!-- Serial Numbers -->
                                    <?php if (!empty($unit['sn_unit']) || !empty($unit['sn_mesin']) || !empty($unit['sn_mast']) || !empty($unit['sn_baterai'])): ?>
                                    <div class="mb-2">
                                        <small class="text-muted d-block mb-1">Serial Number:</small>
                                        <div class="small">
                                            <?php if (!empty($unit['sn_unit'])): ?>
                                                <span class="badge badge-soft-gray me-1">SN Unit: <?= esc($unit['sn_unit']) ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($unit['sn_mesin'])): ?>
                                                <span class="badge badge-soft-gray me-1">SN Mesin: <?= esc($unit['sn_mesin']) ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($unit['sn_mast'])): ?>
                                                <span class="badge badge-soft-gray me-1">SN Mast: <?= esc($unit['sn_mast']) ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($unit['sn_baterai'])): ?>
                                                <span class="badge badge-soft-gray me-1">SN Battery: <?= esc($unit['sn_baterai']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Reason for Rejection:</small>
                                        <div class="text-danger small"><?= esc($unit['catatan_verifikasi'] ?? 'No notes available') ?></div>
                                    </div>
                                    
                                    <?php if (isset($discrepancies['unit_' . $unit['id_po_unit']])): ?>
                                        <div class="mb-2">
                                            <small class="text-muted d-block mb-1">Discrepancies:</small>
                                            <?php foreach ($discrepancies['unit_' . $unit['id_po_unit']] as $disc): ?>
                                                <div class="small mb-1">
                                                    <span class="badge badge-soft-<?= $disc['discrepancy_type'] === 'Major' ? 'red' : ($disc['discrepancy_type'] === 'Missing' ? 'cyan' : 'yellow') ?> me-1">
                                                        <?= esc($disc['discrepancy_type']) ?>
                                                    </span>
                                                    <strong><?= esc($disc['field_name']) ?>:</strong>
                                                    DB: <span class="text-muted"><?= esc($disc['database_value'] ?? '-') ?></span> | 
                                                    Real: <span class="text-danger"><?= esc($disc['real_value'] ?? '-') ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex justify-content-end mt-2 pt-2 border-top">
                                        <button class="btn btn-sm btn-primary" 
                                                onclick="reverifyUnit(<?= $unit['id_po_unit'] ?>, <?= $unit['po_id'] ?>)">
                                            <i class="fas fa-redo me-1"></i>Re-verify
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Attachment Tab -->
        <div class="tab-pane fade" id="attachment-rejected" role="tabpanel" aria-labelledby="attachment-tab">
            <?php if (empty($rejected_attachments)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No rejected attachments found.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($rejected_attachments as $att): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card border-start border-danger border-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1"><?= esc($att['no_po']) ?></h6>
                                            <small class="text-muted"><?= esc(ucfirst($att['item_type'] ?? 'Attachment')) ?></small>
                                        </div>
                                        <span class="badge badge-soft-red">Attachment</span>
                                    </div>
                                    
                                    <!-- Informasi Tambahan -->
                                    <div class="row g-2 mb-2 small">
                                        <?php if (!empty($att['packing_list_no'])): ?>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Packing List:</small>
                                            <strong><?= esc($att['packing_list_no']) ?></strong>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($att['tanggal_sampai'])): ?>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Arrival Date:</small>
                                            <strong><?= date('d/m/Y', strtotime($att['tanggal_sampai'])) ?></strong>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="col-6">
                                            <small class="text-muted d-block">Verification Date:</small>
                                            <strong><?= format_date_jakarta($att['tanggal_verifikasi'] ?? null) ?></strong>
                                        </div>
                                        
                                        <div class="col-6">
                                            <small class="text-muted d-block">Verified By:</small>
                                            <strong>
                                                <?php 
                                                $verifierName = trim(($att['verified_by_name'] ?? '') . ' ' . ($att['verified_by_lastname'] ?? ''));
                                                if (!empty($verifierName)): 
                                                    echo esc($verifierName);
                                                else: 
                                                    echo '<span class="text-muted">-</span>';
                                                endif; 
                                                ?>
                                            </strong>
                                        </div>
                                    </div>
                                    
                                    <!-- Serial Number -->
                                    <?php if (!empty($att['sn'])): ?>
                                    <div class="mb-2">
                                        <small class="text-muted d-block mb-1">Serial Number:</small>
                                        <span class="badge badge-soft-gray"><?= esc($att['sn']) ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Reason for Rejection:</small>
                                        <div class="text-danger small"><?= esc($att['catatan_verifikasi'] ?? 'No notes available') ?></div>
                                    </div>
                                    
                                    <?php if (isset($discrepancies['attachment_' . $att['id_po_attachment']])): ?>
                                        <div class="mb-2">
                                            <small class="text-muted d-block mb-1">Discrepancies:</small>
                                            <?php foreach ($discrepancies['attachment_' . $att['id_po_attachment']] as $disc): ?>
                                                <div class="small mb-1">
                                                    <span class="badge badge-soft-<?= $disc['discrepancy_type'] === 'Major' ? 'red' : ($disc['discrepancy_type'] === 'Missing' ? 'cyan' : 'yellow') ?> me-1">
                                                        <?= esc($disc['discrepancy_type']) ?>
                                                    </span>
                                                    <strong><?= esc($disc['field_name']) ?>:</strong>
                                                    DB: <span class="text-muted"><?= esc($disc['database_value'] ?? '-') ?></span> | 
                                                    Real: <span class="text-danger"><?= esc($disc['real_value'] ?? '-') ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex justify-content-end mt-2 pt-2 border-top">
                                        <button class="btn btn-sm btn-primary" onclick="reverifyAttachment(<?= $att['id_po_attachment'] ?>, <?= $att['po_id'] ?>)">
                                            <i class="fas fa-redo me-1"></i>Re-verify
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sparepart Tab -->
        <div class="tab-pane fade" id="sparepart-rejected" role="tabpanel" aria-labelledby="sparepart-tab">
            <?php if (empty($rejected_spareparts)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No rejected spareparts found.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($rejected_spareparts as $sp): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card border-start border-danger border-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1"><?= esc($sp['no_po']) ?></h6>
                                            <small class="text-muted"><?= esc($sp['kode'] ?? '') ?> - <?= esc($sp['desc_sparepart'] ?? '') ?></small>
                                        </div>
                                        <span class="badge badge-soft-red">Sparepart</span>
                                    </div>
                                    
                                    <!-- Additional Information -->
                                    <div class="row g-2 mb-2 small">
                                        <?php if (!empty($sp['packing_list_no'])): ?>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Packing List:</small>
                                            <strong><?= esc($sp['packing_list_no']) ?></strong>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($sp['tanggal_sampai'])): ?>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Arrival Date:</small>
                                            <strong><?= date('d/m/Y', strtotime($sp['tanggal_sampai'])) ?></strong>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="col-6">
                                            <small class="text-muted d-block">Verification Date:</small>
                                            <strong><?= format_date_jakarta($sp['tanggal_verifikasi'] ?? null) ?></strong>
                                        </div>
                                        
                                        <div class="col-6">
                                            <small class="text-muted d-block">Verified By:</small>
                                            <strong>
                                                <?php 
                                                $verifierName = trim(($sp['verified_by_name'] ?? '') . ' ' . ($sp['verified_by_lastname'] ?? ''));
                                                if (!empty($verifierName)): 
                                                    echo esc($verifierName);
                                                else: 
                                                    echo '<span class="text-muted">-</span>';
                                                endif; 
                                                ?>
                                            </strong>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">Qty: </small>
                                        <strong><?= esc($sp['qty'] ?? 0) ?></strong>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Reason for Rejection:</small>
                                        <div class="text-danger small"><?= esc($sp['catatan_verifikasi'] ?? 'No notes available') ?></div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-end mt-2 pt-2 border-top">
                                        <button class="btn btn-sm btn-primary" onclick="reverifySparepart(<?= $sp['id'] ?>, <?= $sp['po_id'] ?>)">
                                            <i class="fas fa-redo me-1"></i>Re-verify
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
    // Tab switching function - Simple and direct (works with or without jQuery)
    // Define in global scope to ensure onclick can access it
    window.switchRejectedTab = function(tabName, buttonElement) {
        const targetPaneId = tabName + '-rejected';
        const targetPane = document.getElementById(targetPaneId);
        
        if (!targetPane) {
            console.error('Tab pane not found:', targetPaneId);
            return false;
        }
        
        // Remove active from all buttons
        const allButtons = document.querySelectorAll('#rejectedTabs button');
        allButtons.forEach(btn => btn.classList.remove('active'));
        
        // Add active to clicked button
        if (buttonElement) {
            buttonElement.classList.add('active');
        }
        
        // Hide all panes
        const allPanes = document.querySelectorAll('#rejectedTabsContent .tab-pane');
        allPanes.forEach(pane => {
            pane.style.display = 'none';
            pane.classList.remove('show', 'active');
        });
        
        // Show target pane
        targetPane.style.display = 'block';
        targetPane.classList.add('show', 'active');
        
        return false;
    };
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Hide all tab panes except Unit on initial load
        const allPanes = document.querySelectorAll('#rejectedTabsContent .tab-pane');
        allPanes.forEach(pane => {
            if (pane.id !== 'unit-rejected') {
                pane.style.display = 'none';
                pane.classList.remove('show', 'active');
            } else {
                pane.style.display = 'block';
                pane.classList.add('show', 'active');
            }
        });
    });

    // Re-verification functions
    function reverifyUnit(idUnit, poId) {
        Swal.fire({
            title: 'Re-verify Unit?',
            text: 'Has the new item arrived from the vendor? The status will be reset to "Not Checked" for re-verification.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Reset for Re-verify',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('warehouse/purchase-orders/reverify-unit') ?>',
                    type: 'POST',
                    data: {
                        id_unit: idUnit,
                        po_id: poId,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    dataType: 'JSON',
                    beforeSend: () => OptimaPro.showLoading('Resetting unit status...'),
                    success: function(r) {
                        OptimaPro.hideLoading();
                        if (r.statusCode == 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: r.message || 'Unit status has been reset. Please re-verify.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            OptimaPro.hideLoading();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: r.message || 'Failed to reset unit status.'
                            });
                        }
                    },
                    error: function(xhr) {
                        OptimaPro.hideLoading();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while resetting unit status.'
                        });
                    }
                });
            }
        });
    }

    function reverifyAttachment(idAttachment, poId) {
        Swal.fire({
            title: 'Re-verify Attachment?',
            text: 'Has the new item arrived from the vendor? The status will be reset to "Not Checked" for re-verification.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Reset for Re-verify',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('warehouse/purchase-orders/reverify-attachment') ?>',
                    type: 'POST',
                    data: {
                        id_attachment: idAttachment,
                        po_id: poId,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    dataType: 'JSON',
                    beforeSend: () => OptimaPro.showLoading('Resetting attachment status...'),
                    success: function(r) {
                        OptimaPro.hideLoading();
                        if (r.statusCode == 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: r.message || 'Attachment status has been reset.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            OptimaPro.hideLoading();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: r.message || 'Failed to reset attachment status.'
                            });
                        }
                    },
                    error: function(xhr) {
                        OptimaPro.hideLoading();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while resetting attachment status.'
                        });
                    }
                });
            }
        });
    }

    function reverifySparepart(idSparepart, poId) {
        Swal.fire({
            title: 'Re-verify Sparepart?',
            text: 'Has the new item arrived from the vendor? The status will be reset to "Not Checked" for re-verification.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Reset for Re-verify',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('warehouse/purchase-orders/reverify-sparepart') ?>',
                    type: 'POST',
                    data: {
                        id_sparepart: idSparepart,
                        po_id: poId,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    dataType: 'JSON',
                    beforeSend: () => OptimaPro.showLoading('Resetting sparepart status...'),
                    success: function(r) {
                        OptimaPro.hideLoading();
                        if (r.statusCode == 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: r.message || 'SSparepart status has been reset.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            OptimaPro.hideLoading();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: r.message || 'Failed to reset sparepart status.'
                            });
                        }
                    },
                    error: function(xhr) {
                        OptimaPro.hideLoading();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while resetting sparepart status.'
                        });
                    }
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>

