<?= $this->extend('layouts/base') ?>

<?php
/**
 * Purchase Orders Management Module
 *
 * BADGE SYSTEM: Menggunakan Optima Badge Standards (optima-pro.css)
 * Direct CSS classes - tidak perlu JavaScript helper function
 *
 * Quick Reference:
 * - Status DRAFT      → <span class="badge badge-soft-gray">Draft</span>
 * - Status SUBMITTED  → <span class="badge badge-soft-yellow">Submitted</span>
 * - Status APPROVED   → <span class="badge badge-soft-blue">Approved</span>
 * - Status IN_TRANSIT → <span class="badge badge-soft-cyan">In Transit</span>
 * - Status RECEIVED   → <span class="badge badge-soft-green">Received</span>
 * - Count / PO Number → <span class="badge badge-soft-blue font-monospace">PO-001</span>
 *
 * See optima-pro.css line ~2030 for complete badge standards
 */

// Load global permission helper
helper('global_permission');

// Get permissions for purchasing module
$permissions = get_global_permission('purchasing');
$can_view = $permissions['view'];
$can_create = $permissions['create'];
$can_edit = $permissions['edit'];
$can_delete = $permissions['delete'];
$can_export = $permissions['export'];
?>

<?= $this->section('content') ?>

<style>
    #poDeliveryTable {
        table-layout: fixed;
    }

    #poDeliveryTable th,
    #poDeliveryTable td {
        vertical-align: middle;
    }

    #poDeliveryTable .col-packing-list {
        width: 14%;
    }

    #poDeliveryTable .col-po-supplier {
        width: 22%;
    }

    #poDeliveryTable .col-delivery-info {
        width: 16%;
    }

    #poDeliveryTable .col-items {
        width: 24%;
    }

    #poDeliveryTable .col-status {
        width: 10%;
    }

    #poDeliveryTable .col-actions {
        width: 14%;
    }

    .delivery-packing-list {
        display: inline-block;
        max-width: 100%;
        font-weight: 600;
        white-space: normal;
        overflow-wrap: anywhere;
    }

    .delivery-cell-title {
        font-weight: 600;
        line-height: 1.35;
        white-space: normal;
        overflow-wrap: anywhere;
    }

    .delivery-cell-subtitle {
        display: block;
        margin-top: 0.2rem;
        font-size: 0.775rem;
        color: #6c757d;
        line-height: 1.3;
        white-space: normal;
        overflow-wrap: anywhere;
    }

    .delivery-items-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        align-items: center;
    }

    .delivery-items-wrap .badge {
        margin-right: 0;
        white-space: nowrap;
    }

    .delivery-actions {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 0.3rem;
        width: 100%;
    }

    .delivery-actions-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        width: 100%;
    }

    .delivery-actions-left {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        min-width: 0;
        flex: 1 1 auto;
    }

    .delivery-actions-right {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex: 0 0 auto;
    }

    .delivery-action-note {
        display: block;
        width: 100%;
        font-size: 0.76rem;
        line-height: 1.25;
        text-align: left;
    }

    .delivery-status-note {
        font-size: 0.78rem;
        line-height: 1.3;
    }

    @media (max-width: 1400px) {
        #poDeliveryTable {
            table-layout: auto;
        }
    }

    /* Panjang form PO: header & footer tetap, konten di modal-body di-scroll */
    #itemDetailModal .modal-dialog.modal-dialog-scrollable,
    #createPoModal .modal-dialog.modal-dialog-scrollable,
    #assignSNModal .modal-dialog.modal-dialog-scrollable,
    #viewPOModal .modal-dialog.modal-dialog-scrollable,
    #createDeliveryModal .modal-dialog.modal-dialog-scrollable {
        max-height: calc(100vh - 1.5rem);
    }
</style>

<!-- Success/Error Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Main Card -->
<div class="card table-card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0">
                <i class="fas fa-shopping-cart me-2 text-primary"></i><?= lang('App.purchase_orders') ?>
            </h5>
            <p class="text-muted small mb-0">
                <?= lang('Purchasing.purchase_orders_description') ?>
                <span class="ms-2 text-info">
                    <i class="bi bi-info-circle me-1"></i>
                    <small><?= lang('Purchasing.purchase_orders_tip_tabs') ?></small>
                </span>
            </p>
        </div>
        <div class="d-flex gap-2" role="group">
                <?php if ($can_create): ?>
                <button type="button" class="btn btn-primary" id="btnBuatPO">
                    <i class="fas fa-plus me-1"></i><?= lang('App.create') ?> PO
                </button>
                <?php else: ?>
                <button type="button" class="btn btn-secondary" disabled title="<?= lang('App.access_denied') ?>: <?= lang('App.no_permission_create') ?>">
                    <i class="fas fa-lock me-1"></i><?= lang('App.create') ?> PO
                </button>
                <?php endif; ?>
                <button type="button" class="btn btn-outline-primary" onclick="refreshTable()">
                    <i class="fas fa-sync-alt me-1"></i><?= lang('Common.refresh') ?>
                </button>
                <?php if ($can_export): ?>
                <div class="dropdown">
                    <button class="btn btn-outline-success dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-excel me-1"></i><?= lang('Common.export') ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                        <li><a class="dropdown-item" href="<?= base_url('purchasing/export_po_progres') ?>">
                            <i class="fas fa-clock me-2 text-success"></i><?= lang('Common.export') ?> <?= lang('Common.progress') ?>
                        </a></li>
                        <li><a class="dropdown-item" href="<?= base_url('purchasing/export_po_delivery') ?>">
                            <i class="fas fa-truck me-2 text-warning"></i><?= lang('Common.export') ?> <?= lang('App.delivery') ?>
                        </a></li>
                        <li><a class="dropdown-item" href="<?= base_url('purchasing/export_po_completed') ?>">
                            <i class="fas fa-check-circle me-2 text-info"></i><?= lang('Common.export') ?> <?= lang('Common.completed') ?>
                        </a></li>
                    </ul>
                </div>
                <?php else: ?>
                <button type="button" class="btn btn-outline-secondary" disabled title="<?= lang('App.access_denied') ?>: <?= lang('App.no_permission_export') ?>">
                    <i class="fas fa-lock me-1"></i><?= lang('Common.export') ?>
                </button>
                <?php endif; ?>
            </div>
    </div>

        <!-- Tabs Navigation -->
        <div class="card-header border-bottom">
            <ul class="nav nav-tabs card-header-tabs" id="poTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="progres-tab" data-bs-toggle="tab" data-bs-target="#progres-pane" type="button" role="tab" aria-controls="progres-pane" aria-selected="true">
                        <i class="fas fa-clock"></i>
                        <span><?= lang('Common.progress') ?></span>
                        <span class="badge" id="progres-count">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="delivery-tab" data-bs-toggle="tab" data-bs-target="#delivery-pane" type="button" role="tab" aria-controls="delivery-pane" aria-selected="false">
                        <i class="fas fa-truck"></i>
                        <span><?= lang('App.delivery') ?></span>
                        <span class="badge" id="delivery-count">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-pane" type="button" role="tab" aria-controls="completed-pane" aria-selected="false">
                        <i class="fas fa-check-circle"></i>
                        <span><?= lang('Common.completed') ?></span>
                    </button>
                </li>
            </ul>
        </div>

    <!-- Tab Content -->
    <div class="tab-content" id="poTabContent">
        <!-- Progres Tab -->
        <div class="tab-pane fade show active" id="progres-pane" role="tabpanel" aria-labelledby="progres-tab">
            <?php if (!can_view('purchasing')): ?>
            <div class="alert alert-warning m-3">
                <i class="fas fa-lock me-2"></i>
                <strong><?= lang('App.access_restricted') ?>:</strong> <?= lang('App.no_permission_view') ?> <?= lang('App.purchase_orders') ?> <?= strtolower(lang('App.details')) ?>. 
                <?= lang('App.contact_administrator') ?>.
            </div>
            <?php endif; ?>
            <div class="card-body p-0">
        <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 clickable-row <?= !$can_view ? 'table-disabled' : '' ?>" id="unitAttachmentPOTable">
                <thead class="table-light">
                    <tr>
                        <th><?= lang('App.po_number') ?></th>
                        <th><?= lang('Common.date') ?></th>
                        <th><?= lang('App.supplier') ?></th>
                        <th><?= lang('Common.status') ?></th>
                        <th><?= lang('App.total_items') ?></th>
                        <th><?= lang('App.verification_progress') ?></th>
                        <th><?= lang('App.delivery_status') ?></th>
                        <th class="text-center"><?= lang('Common.actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data akan diisi oleh DataTable -->
                </tbody>
            </table>
                </div>
            </div>
        </div>
        
        <!-- Delivery Tab -->
        <div class="tab-pane fade" id="delivery-pane" role="tabpanel" aria-labelledby="delivery-tab">
            <?php if (!can_view('purchasing')): ?>
            <div class="alert alert-warning m-3">
                <i class="fas fa-lock me-2"></i>
                <strong><?= lang('App.access_restricted') ?>:</strong> <?= lang('App.no_permission_view') ?> <?= strtolower(lang('App.delivery')) ?> <?= strtolower(lang('App.details')) ?>. 
                <?= lang('App.contact_administrator') ?>.
            </div>
            <?php endif; ?>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 clickable-row <?= !$can_view ? 'table-disabled' : '' ?>" id="poDeliveryTable">
                        <thead class="table-light">
                            <tr>
                                <th class="col-packing-list"><?= lang('App.packing_list') ?></th>
                                <th class="col-po-supplier">PO / Supplier</th>
                                <th class="col-delivery-info">Delivery</th>
                                <th class="col-items"><?= lang('App.items') ?></th>
                                <th class="col-status"><?= lang('Common.status') ?></th>
                                <th class="text-center col-actions"><?= lang('Common.actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan dimuat via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Completed Tab -->
        <div class="tab-pane fade" id="completed-pane" role="tabpanel" aria-labelledby="completed-tab">
            <?php if (!can_view('purchasing')): ?>
            <div class="alert alert-warning m-3">
                <i class="fas fa-lock me-2"></i>
                <strong><?= lang('App.access_restricted') ?>:</strong> <?= lang('App.no_permission_view') ?> <?= strtolower(lang('Common.completed')) ?> orders. 
                <?= lang('App.contact_administrator') ?>.
            </div>
            <?php endif; ?>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 clickable-row <?= !$can_view ? 'table-disabled' : '' ?>" id="unitAttachmentPOCompletedTable">
                        <thead class="table-light">
                            <tr>
                                <th><?= lang('App.po_number') ?></th>
                                <th><?= lang('Common.date') ?></th>
                                <th><?= lang('App.supplier') ?></th>
                                <th><?= lang('Common.status') ?></th>
                                <th><?= lang('App.total_items') ?></th>
                                <th><?= lang('App.verification_progress') ?></th>
                                <th><?= lang('App.delivery_status') ?></th>
                                <th class="text-center"><?= lang('Common.actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan diisi oleh DataTable -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- No modal needed - using existing forms -->

<!-- Create Delivery Modal -->
<div class="modal fade" id="createDeliveryModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Delivery Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createDeliveryForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Create a delivery schedule for this PO
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Delivery Date *</label>
                            <input type="date" class="form-control" name="delivery_date" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Packing List No *</label>
                            <input type="text" class="form-control" name="packing_list_no" required placeholder="Enter packing list number from supplier">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Driver Name</label>
                            <input type="text" class="form-control" name="driver_name" placeholder="Driver name (optional)">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Driver Phone</label>
                            <input type="text" class="form-control" name="driver_phone" placeholder="Driver phone number (optional)">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Vehicle Info</label>
                            <input type="text" class="form-control" name="vehicle_info" placeholder="Vehicle info (optional)">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Vehicle Plate</label>
                            <input type="text" class="form-control" name="vehicle_plate" placeholder="Vehicle plate number (optional)">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Delivery notes..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Items to Deliver <small class="text-muted">(Select how many units will be delivered)</small></label>
                        <div id="deliveryItemsList" class="border rounded p-3">
                            <!-- Items will be loaded dynamically -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Delivery</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Serial Numbers Modal -->
<div class="modal fade" id="assignSNModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-barcode me-2"></i>Assign Serial Numbers
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="proceedWithoutSN()" id="proceedWithoutSNBtn">
                        <i class="fas fa-forward me-1"></i>Proceed Without SN
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <form id="assignSNForm">
                <div class="modal-body">
                    <!-- Simple Delivery Info -->
                    <div class="alert alert-light border mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Packing List:</strong><br>
                                <span id="snModalPackingList" class="text-primary fw-bold">-</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Driver:</strong><br>
                                <span id="snModalDriver" class="text-muted">-</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Delivery Date:</strong><br>
                                <span id="snModalDeliveryDate" class="text-muted">-</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Vehicle:</strong><br>
                                <span id="snModalVehicle" class="text-muted">-</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Items for SN Assignment -->
                    <div class="mb-3">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-barcode me-2"></i>Items to Assign Serial Numbers
                        </h6>
                        <div id="snAssignmentContent">
                            <!-- Content will be loaded dynamically -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Save Serial Numbers
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View PO Detail Modal -->
<div class="modal fade" id="viewPOModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.12);">
            <div class="modal-header" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); color: #2c3e50; border-bottom: 1px solid #e9ecef; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title" style="font-weight: 600;">
                    <i class="fas fa-file-invoice me-2 text-primary"></i>Detail Purchase Order
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="poDetailsContent" style="padding: 0;">
                <!-- Loading State -->
                <div class="text-center p-5" id="poLoadingState">
                    <i class="fas fa-circle-notch fa-spin text-primary" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">Loading PO details...</h5>
                </div>
                
                <!-- PO Content (hidden initially) -->
                <div id="poContent" style="display: none;">
                    <!-- Summary Cards -->
                    <div class="p-4" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-bottom: 1px solid #e9ecef;">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                                    <div class="card-body text-center p-3">
                                        <div class="mb-2" id="totalItemsOrdered">
                                            <span class="text-muted">No items</span>
                                        </div>
                                        <small class="text-muted">Total Items Ordered</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                                    <div class="card-body text-center p-3">
                                        <div class="text-info mb-2">
                                            <i class="fas fa-truck fa-2x"></i>
                                        </div>
                                        <h4 class="mb-1" id="deliveryProgress">0/0</h4>
                                        <small class="text-muted">Delivery</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                                    <div class="card-body text-center p-3">
                                        <div class="text-success mb-2">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                        <h4 class="mb-1" id="totalItemsReceived">0</h4>
                                        <small class="text-muted">Received Items</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                                    <div class="card-body text-center p-3">
                                        <div class="text-warning mb-2">
                                            <i class="fas fa-clipboard-check fa-2x"></i>
                                        </div>
                                        <h4 class="mb-1" id="verifiedItems">0</h4>
                                        <small class="text-muted">Verified Items</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- PO Information -->
                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-primary mb-0">
                                <i class="fas fa-info-circle me-2"></i>Purchase Order Information
                            </h6>
                            <?php if ($can_edit): ?>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnEditPoInfo" onclick="togglePoInfoEdit(true)">
                                <i class="fas fa-edit me-1"></i>Edit
                            </button>
                            <?php endif; ?>
                        </div>

                        <!-- View Mode -->
                        <div id="poInfoView" class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light text-dark me-2" style="min-width: 100px;">PO Number:</span>
                                    <span class="fw-bold" id="poNumber">-</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light text-dark me-2" style="min-width: 100px;">PO Date:</span>
                                    <span id="poDate">-</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light text-dark me-2" style="min-width: 100px;">Supplier:</span>
                                    <span id="poSupplier">-</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light text-dark me-2" style="min-width: 100px;">Contact:</span>
                                    <span id="poContact">-</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light text-dark me-2" style="min-width: 100px;">PO Status:</span>
                                    <span id="poStatus">-</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light text-dark me-2" style="min-width: 100px;">Invoice No:</span>
                                    <span id="poInvoice">-</span>
                                </div>
                            </div>
                            <div class="col-md-6" id="poViewInvoiceDateRow" style="display:none;">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light text-dark me-2" style="min-width: 100px;">Invoice Date:</span>
                                    <span id="poInvoiceDate">-</span>
                                </div>
                            </div>
                            <div class="col-md-6" id="poViewBlDateRow" style="display:none;">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light text-dark me-2" style="min-width: 100px;">BL Date:</span>
                                    <span id="poBlDate">-</span>
                                </div>
                            </div>
                            <div class="col-12" id="poViewNotesRow" style="display:none;">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-light text-dark me-2 mt-1" style="min-width: 100px;">Notes:</span>
                                    <span id="poNotes" class="text-muted small">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Mode (hidden by default) -->
                        <?php if ($can_edit): ?>
                        <div id="poInfoEditForm" style="display:none;">
                            <div id="poInfoAlert" class="alert d-none mb-3" role="alert"></div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">PO Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="edit_no_po" maxlength="100" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">PO Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control form-control-sm" id="edit_tanggal_po" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm" id="edit_supplier_id" required>
                                        <option value="">-- Pilih Supplier --</option>
                                        <?php if (isset($suppliers) && is_array($suppliers)): ?>
                                            <?php foreach ($suppliers as $s): ?>
                                            <option value="<?= $s['id_supplier'] ?>">
                                                [<?= esc($s['kode_supplier'] ?? '-') ?>] <?= esc($s['nama_supplier']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Invoice No</label>
                                    <input type="text" class="form-control form-control-sm" id="edit_invoice_no" maxlength="100">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Invoice Date</label>
                                    <input type="date" class="form-control form-control-sm" id="edit_invoice_date">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">BL Date</label>
                                    <input type="date" class="form-control form-control-sm" id="edit_bl_date">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Notes</label>
                                    <textarea class="form-control form-control-sm" id="edit_keterangan_po" rows="2" placeholder="Catatan tambahan..."></textarea>
                                </div>
                                <div class="col-12 d-flex gap-2">
                                    <button type="button" class="btn btn-primary btn-sm" id="btnSavePoInfo" onclick="savePoInfo()">
                                        <i class="fas fa-save me-1"></i>Simpan
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="togglePoInfoEdit(false)">
                                        <i class="fas fa-times me-1"></i>Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Tabs for Items and Deliveries -->
                    <div class="px-4">
                        <ul class="nav nav-tabs" id="poDetailTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="items-tab" type="button" role="tab">
                                    <i class="fas fa-list me-2"></i>Items List <span class="badge badge-soft-blue ms-1" id="itemsCount">0</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="deliveries-tab" type="button" role="tab">
                                    <i class="fas fa-truck me-2"></i>Deliveries <span class="badge badge-soft-cyan ms-1" id="deliveriesCount">0</span>
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="poDetailTabContent">
                            <!-- Items Tab -->
                            <div class="tab-pane fade show active" id="items-pane" role="tabpanel">
                                <div class="p-3">
                                    <div id="poItemsContent">
                                        <!-- Will be populated dynamically with dropdown style -->
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Deliveries Tab -->
                            <div class="tab-pane fade" id="deliveries-pane" role="tabpanel">
                                <div class="p-3">
                                    <div id="deliveriesContent">
                                        <!-- Will be populated dynamically -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="poModalFooter" style="background-color: #ffffff; border-top: 1px solid #e9ecef; border-radius: 0 0 12px 12px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Close
                </button>
                <?php if (can_view('purchasing')): ?>
                <button type="button" class="btn btn-outline-secondary" id="printPOBtn" onclick="printPOFromModal()" style="display: none;">
                    <i class="fas fa-print me-2"></i>Print PO
                </button>
                <?php else: ?>
                <button type="button" class="btn btn-secondary" disabled title="Access denied: You do not have permission to print PO" style="display: none;">
                    <i class="fas fa-lock me-2"></i>Print PO
                </button>
                <?php endif; ?>
                <!-- Dynamic action buttons will be added here -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Create PO (Unified - Unit, Attachment, Battery, Charger) -->
<div class="modal fade" id="createPoModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-muted">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Create Purchase Orders
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <form action="<?= base_url('/purchasing/store-unified-po') ?>" method="post" id="unifiedPOForm">
                    <?= csrf_field() ?>
                    
                    <!-- Header PO Section -->
                    <div class="form-section mb-4">
                        <h6 class="section-header">
                            <i class="fas fa-info-circle me-2"></i>Purchase Order Information
                        </h6>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="no_po" class="form-label">PO Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="no_po" id="no_po" required placeholder="Example: PO-2025-001">
                                </div>
                                <div class="col-md-4">
                                    <label for="tanggal_po" class="form-label">PO Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="tanggal_po" id="tanggal_po" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="id_supplier_modal" class="form-label">Supplier <span class="text-danger">*</span></label>
                                    <select name="id_supplier" id="id_supplier_modal" class="form-select select2-supplier-modal" required>
                                        <option value="">Select Supplier...</option>
                                        <?php if (isset($suppliers) && is_array($suppliers)): ?>
                                            <?php foreach ($suppliers as $item): ?>
                                                <option value="<?= $item['id_supplier'] ?>">
                                                    [<?= esc($item['kode_supplier'] ?? '-') ?>] <?= esc($item['nama_supplier']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="invoice_no" class="form-label">Invoice Number</label>
                                    <input type="text" class="form-control" name="invoice_no" id="invoice_no" placeholder="Optional">
                                </div>
                                <div class="col-md-4">
                                    <label for="invoice_date" class="form-label">Invoice Date</label>
                                    <input type="date" class="form-control" name="invoice_date" id="invoice_date">
                                </div>
                                <div class="col-md-4">
                                    <label for="bl_date" class="form-label">BL Date</label>
                                    <input type="date" class="form-control" name="bl_date" id="bl_date">
                                </div>
                                <div class="col-12">
                                    <label for="keterangan_po" class="form-label">Notes</label>
                                    <textarea class="form-control" name="keterangan_po" id="keterangan_po" rows="2" placeholder="Additional notes (optional)"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Section -->
                    <div class="form-section mb-4">
                        <h6 class="section-header">
                            <i class="fas fa-list me-2"></i>PO Item List
                        </h6>
                        <div class="card-body p-4">
                            <!-- Action Buttons -->
                            <div class="mb-3 d-flex gap-2 flex-wrap">
                                <?php if ($can_create): ?>
                                <button type="button" class="btn btn-primary btn-sm" onclick="openItemModal('unit')">
                                    <i class="fas fa-truck me-1"></i>Add Unit
                                </button>
                                <details class="d-inline-block">
                                    <summary class="btn btn-outline-secondary btn-sm" style="cursor:pointer; list-style:none;">Baris PI terpisah (lanjutan)</summary>
                                    <div class="small text-muted mt-1 mb-2" style="max-width:28rem;">Hanya jika vendor memisahkan attachment, baterai, atau charger ke <strong>baris berbeda</strong> di PI. Paket pabrik satu baris cukup lewat <strong>Add Unit</strong>.</div>
                                    <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-success btn-sm" onclick="openItemModal('attachment')">
                                    <i class="fas fa-tools me-1"></i>Add Attachment
                                </button>
                                <button type="button" class="btn btn-primary btn-sm" onclick="openItemModal('battery')">
                                    <i class="fas fa-battery-full me-1"></i>Add Battery
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" onclick="openItemModal('charger')">
                                    <i class="fas fa-plug me-1"></i>Add Charger
                                </button>
                                    </div>
                                </details>
                                <?php else: ?>
                                <button type="button" class="btn btn-secondary btn-sm" disabled title="Access denied: You do not have permission to add items">
                                    <i class="fas fa-lock me-1"></i>Add Unit
                                </button>
                                <?php endif; ?>
                            </div>

                            <!-- Items Table -->
                            <div class="table-responsive">
                                <table class="table table-striped table-sm item-table" id="itemsTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">No</th>
                                            <th style="width: 15%;">Item Type</th>
                                            <th style="width: 55%;">Description</th>
                                            <th style="width: 15%;">Qty</th>
                                            <th style="width: 10%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTableBody">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No items yet. Click the "Add" button to add items.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Hidden field untuk menyimpan items JSON -->
                            <input type="hidden" name="items_json" id="items_json" value="[]">
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="submit" form="unifiedPOForm" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save Purchase Order
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Item Details (Sub-Modal) -->
<div class="modal fade" id="itemDetailModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-muted">
                <h5 class="modal-title" id="itemModalTitle">Add Item</h5>
                <button type="button" class="btn-close btn-close-muted" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="itemModalBody">
                <!-- Content will be loaded dynamically -->
                <div class="text-center p-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                    <p class="mt-3">Loading form...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveItemBtn">
                    <i class="fas fa-check me-2"></i>Add to PO
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add Master Data Modal -->
<?= $this->include('purchasing/components/quick_add_modal') ?>

<?= $this->endSection() ?>


<?= $this->section('javascript') ?>
<!-- Select2 CSS/JS sudah dimuat di base layout -->

<script>
// Wait for jQuery to be loaded
(function() {
    'use strict';
    
    // Check if jQuery is loaded
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded!');
        return;
    }
    
    // Initialize PO system
    $(document).ready(function() {
        console.log('📋 Initializing Purchasing System...');
        
        // Check Bootstrap availability
        if (typeof bootstrap !== 'undefined') {
            console.log('✅ Bootstrap loaded successfully');
        } else {
            console.warn('Bootstrap not detected, modal functionality may be limited');
        }
        
        // Initialize Unit & Attachment PO DataTables
        initUnitAttachmentPOTable();
        initPODeliveryTable();
        initUnitAttachmentPOCompletedTable();
        
        // Initialize Create PO Modal (delay slightly to ensure DOM is ready)
        setTimeout(function() {
            initCreatePOModal();
        }, 100);
        
        // Global status filter handler (date filter handled by helper)
        $('#globalFilterStatus').on('change', function() {
            if (unitAttachmentPOTable) {
                unitAttachmentPOTable.ajax.reload();
            }
            if (poDeliveryTable) {
                poDeliveryTable.ajax.reload();
            }
            if (unitAttachmentPOCompletedTable) {
                unitAttachmentPOCompletedTable.ajax.reload();
            }
        });
        
        // Tab switching event handlers - Following work_orders.php pattern
        // Initialize delivery table when delivery tab is first shown
        $('#delivery-tab').on('shown.bs.tab', function (e) {
            // Force reload delivery table
            if (poDeliveryTable) {
                poDeliveryTable.ajax.reload();
                // Adjust column sizing
                setTimeout(function() {
                    poDeliveryTable.columns.adjust();
                    if (poDeliveryTable.responsive && typeof poDeliveryTable.responsive.recalc === 'function') {
                        poDeliveryTable.responsive.recalc();
                    }
                }, 100);
            }
            console.log('Delivery tab activated - reloading data');
        });
        
        // Also handle click event for delivery tab
        $('#delivery-tab').on('click', function(e) {
            // Small delay to ensure tab is fully shown
            setTimeout(function() {
                if ($('#delivery-tab').hasClass('active')) {
                    if (poDeliveryTable) {
                        poDeliveryTable.ajax.reload();
                        // Adjust column sizing
                        setTimeout(function() {
                            poDeliveryTable.columns.adjust();
                            if (poDeliveryTable.responsive && typeof poDeliveryTable.responsive.recalc === 'function') {
                                poDeliveryTable.responsive.recalc();
                            }
                        }, 100);
                    }
                }
            }, 150);
        });

        // Initialize completed table when closed tab is first shown
        $('#completed-tab').on('shown.bs.tab', function (e) {
            // Force reload completed table
            if (unitAttachmentPOCompletedTable) {
                unitAttachmentPOCompletedTable.ajax.reload();
                // Adjust column sizing
                setTimeout(function() {
                    unitAttachmentPOCompletedTable.columns.adjust();
                    unitAttachmentPOCompletedTable.responsive.recalc();
                }, 100);
            }
            console.log('Completed tab activated - reloading data');
        });
        
        // Also handle click event for completed tab
        $('#completed-tab').on('click', function(e) {
            // Small delay to ensure tab is fully shown
            setTimeout(function() {
                if ($('#completed-tab').hasClass('active')) {
                    if (unitAttachmentPOCompletedTable) {
                        unitAttachmentPOCompletedTable.ajax.reload();
                        // Adjust column sizing
                        setTimeout(function() {
                            unitAttachmentPOCompletedTable.columns.adjust();
                            unitAttachmentPOCompletedTable.responsive.recalc();
                        }, 100);
                    }
                }
            }, 150);
        });

        // Ensure Progress tab is active on page load and reload progress table
        // Force Progress tab to be active
        $('#progres-tab').addClass('active').attr('aria-selected', 'true');
        $('#completed-tab').removeClass('active').attr('aria-selected', 'false');
        
        // Show Progress pane and hide Completed pane
        $('#progres-pane').addClass('show active');
        $('#completed-pane').removeClass('show active');
        
        // Reload progress table to ensure data is loaded
        setTimeout(function() {
            if (unitAttachmentPOTable) {
                unitAttachmentPOTable.ajax.reload();
            }
        }, 100);
    });
})();

        // Update count functions - Following work_orders.php pattern
        function updateProgresCount(count) {
            $('#progres-count').text(count);
        }

        function updateDeliveryCount(count) {
            $('#delivery-count').text(count);
        }

        // Function to refresh tables
// Debounce refreshTable to prevent multiple calls
let refreshTimeout;
function refreshTable() {
    clearTimeout(refreshTimeout);
    refreshTimeout = setTimeout(() => {
    if (unitAttachmentPOTable) {
        unitAttachmentPOTable.ajax.reload();
    }
        if (poDeliveryTable) {
            poDeliveryTable.ajax.reload();
        }
        if (unitAttachmentPOCompletedTable) {
            unitAttachmentPOCompletedTable.ajax.reload();
        }
    }, 100);
}

// Initialize Unit & Attachment PO DataTables
let unitAttachmentPOTable = null;
let poDeliveryTable = null;
let unitAttachmentPOCompletedTable = null;

function reinitializeModalSelect2($element) {
    if (!$element || !$element.length || typeof $.fn.select2 === 'undefined') {
        return;
    }

    if ($element.hasClass('select2-hidden-accessible')) {
        $element.select2('destroy');
    }

    $element.select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#itemDetailModal .modal-content'),
        width: '100%',
        dropdownAutoWidth: true
    });
}

function reloadPurchasingTables() {
    if (unitAttachmentPOTable) unitAttachmentPOTable.ajax.reload(null, false);
    if (poDeliveryTable) poDeliveryTable.ajax.reload(null, false);
    if (unitAttachmentPOCompletedTable) unitAttachmentPOCompletedTable.ajax.reload(null, false);
}

function initUnitAttachmentPOTable() {
    unitAttachmentPOTable = $('#unitAttachmentPOTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('/purchasing/api/get-unified-po-data') ?>',
            type: 'POST',
            data: function(d) {
                d.po_type = 'unit_attachment';
                d.tab_type = 'progres';
                d.status = $('#globalFilterStatus').val();
                // Add date filter from global date picker
                if (window.globalDateRangeStart && window.globalDateRangeEnd) {
                    d.start_date = window.globalDateRangeStart;
                    d.end_date = window.globalDateRangeEnd;
                }
            }
        },
        columns: [
            { data: 'no_po', name: 'no_po' },
            { data: 'tanggal_po', name: 'tanggal_po' },
            { data: 'nama_supplier', name: 'nama_supplier' },
            { 
                data: 'status', 
                name: 'status',
                render: function(data, type, row) {
                    return renderPOStatusBadge(row);
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    // PROGRES TAB - Get item breakdown from new total columns
                    const totalUnit = parseInt(row.total_unit, 10) || 0;
                    const totalAttachment = parseInt(row.total_attachment, 10) || 0;
                    const totalBattery = parseInt(row.total_battery, 10) || 0;
                    const totalCharger = parseInt(row.total_charger, 10) || 0;
                    
                    if (totalUnit === 0 && totalAttachment === 0 && totalBattery === 0 && totalCharger === 0) {
                        return `<span class="text-muted small fst-italic">
                            <i class="fas fa-box-open me-1"></i>No items
                        </span>`;
                    }
                    
                    // Create item badges with icons and colors
                    const itemBadges = [];
                    
                    if (totalUnit > 0) {
                        itemBadges.push(`
                            <span class="badge badge-soft-blue me-1 mb-1">
                                <i class="fas fa-truck me-1"></i>${totalUnit} Unit
                            </span>
                        `);
                    }
                    
                    if (totalAttachment > 0) {
                        itemBadges.push(`
                            <span class="badge badge-soft-cyan me-1 mb-1">
                                <i class="fas fa-puzzle-piece me-1"></i>${totalAttachment} Attachment
                            </span>
                        `);
                    }
                    
                    if (totalBattery > 0) {
                        itemBadges.push(`
                            <span class="badge badge-soft-yellow me-1 mb-1">
                                <i class="fas fa-battery-half me-1"></i>${totalBattery} Battery
                            </span>
                        `);
                    }
                    
                    if (totalCharger > 0) {
                        itemBadges.push(`
                            <span class="badge badge-soft-green me-1 mb-1">
                                <i class="fas fa-plug me-1"></i>${totalCharger} Charger
                            </span>
                        `);
                    }
                    
                    // Split badges into 2 rows for better layout
                    let row1 = itemBadges.slice(0, 2);
                    let row2 = itemBadges.slice(2);
                    
                    let result = `<div class="d-flex flex-column">`;
                    if (row1.length > 0) {
                        result += `<div class="d-flex flex-wrap">${row1.join('')}</div>`;
                    }
                    if (row2.length > 0) {
                        result += `<div class="d-flex flex-wrap">${row2.join('')}</div>`;
                    }
                    result += `</div>`;
                    
                    return result;
                }
            },
            { 
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    // Calculate verification progress based on actual verifiable items in warehouse
                    const totalItems = parseInt(row.total_items_actual, 10) || 0;
                    const verified = parseInt(row.total_qty_verified, 10) || 0;
                    
                    if (totalItems === 0) {
                        return `<span class="text-muted small fst-italic">-</span>`;
                    }

                    const percentage = totalItems > 0 ? Math.round((verified / totalItems) * 100) : 0;
                    const remaining = totalItems - verified;

                    return `
                        <div class="progress" title="Verifikasi: ${verified} dari ${totalItems} item (${remaining} tersisa)" style="height: 22px;">
                            <div class="progress-bar progress-bar-striped ${percentage >= 100 ? 'bg-success' : percentage > 0 ? 'bg-warning' : 'bg-secondary'}" role="progressbar" style="width: ${percentage}%;">
                                ${verified} / ${totalItems}
                            </div>
                        </div>
                        <small class="text-muted">${percentage}% verified</small>
                    `;
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return renderPODeliverySummary(row);
                }
            },
            { 
                data: 'id_po',
                name: 'id_po',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    const totalDeliveries = parseInt(row.total_deliveries, 10) || 0;
                    const completedDeliveries = parseInt(row.completed_deliveries, 10) || 0;
                    
                    // Use total_qty_ordered from backend (already calculated correctly)
                    const totalOrdered = parseInt(row.total_qty_ordered, 10) || 0;
                    // Use total_qty_received from backend (items in deliveries with status "Received")
                    const receivedItems = parseInt(row.total_qty_received, 10) || 0;
                    // Use total_qty_scheduled from backend (items already scheduled for delivery, regardless of status)
                    const scheduledItems = parseInt(row.total_qty_scheduled, 10) || 0;
                    const totalVerificationItems = parseInt(row.total_items_actual, 10) || 0;
                    const verifiedItems = parseInt(row.total_qty_verified, 10) || 0;
                    
                    // Calculate remaining items that haven't been scheduled for delivery yet
                    const remainingItems = totalOrdered - scheduledItems;
                    
                    let actionButtons = '';
                    
                    // Dynamic action buttons based on status and progress
                    // Priority 1: Check if all items are already received
                    if (totalOrdered > 0 && receivedItems >= totalOrdered) {
                        // All items delivered, but only allow manual completion when verification is fully done
                        if (totalVerificationItems > 0 && verifiedItems >= totalVerificationItems && row.status !== 'completed') {
                            actionButtons = `<button class="btn btn-sm btn-success" onclick="completePO(${data}, event)">
                                <i class="fas fa-check-circle me-1"></i>Mark as Completed
                            </button>`;
                        } else if (totalVerificationItems > 0 && verifiedItems < totalVerificationItems) {
                            actionButtons = `<span class="text-warning small">
                                <i class="fas fa-clipboard-check me-1"></i>Awaiting Verification
                            </span>`;
                        } else {
                            actionButtons = `<span class="text-success small">
                                <i class="fas fa-check-circle me-1"></i>Verification Completed
                            </span>`;
                        }
                    } else if (totalOrdered > 0 && scheduledItems >= totalOrdered) {
                        // All items already scheduled for delivery (but not all received yet)
                        // Show delivery tracking button instead of "Buat Jadwal Pengiriman"
                        actionButtons = `<button class="btn btn-sm btn-warning" onclick="trackDeliveries(${data}, event)">
                            <i class="fas fa-truck me-1"></i>View Delivery Details
                        </button>`;
                    } else if (row.status === 'pending' && totalDeliveries === 0) {
                        // No deliveries yet - allow creating delivery schedule
                        actionButtons = `<button class="btn btn-sm btn-success" onclick="createDeliverySchedule(${data}, event)">
                            <i class="fas fa-calendar-plus me-1"></i>Create Delivery Schedule
                        </button>`;
                    } else if (remainingItems > 0) {
                        // Has remaining items that haven't been scheduled - can create more deliveries
                        const nextDeliverySequence = totalDeliveries + 1;
                        actionButtons = `<button class="btn btn-sm btn-success" onclick="createDeliverySchedule(${data}, event)">
                            <i class="fas fa-calendar-plus me-1"></i>Create Delivery Schedule (${nextDeliverySequence})
                        </button>`;
                    } else if (totalDeliveries > 0 && completedDeliveries < totalDeliveries) {
                        // Has deliveries but not all completed - show delivery tracking
                        actionButtons = `<button class="btn btn-sm btn-warning" onclick="trackDeliveries(${data}, event)">
                            <i class="fas fa-truck me-1"></i>View Delivery Details
                        </button>`;
                    } else if (row.status === 'Selesai dengan Catatan') {
                        // Special actions for partial rejection
                        actionButtons = `
                            <button class="btn btn-sm btn-warning" onclick="reverifyPO(${data}, event)">
                                <i class="fas fa-sync-alt me-1"></i>Re-verify
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="cancelPO(${data}, event)">
                                <i class="fas fa-ban me-1"></i>Cancel
                            </button>
                        `;
                    } else {
                        // Default - just show status indicator
                        actionButtons = `<span class="text-success small">
                            <i class="fas fa-check-circle me-1"></i>Completed
                        </span>`;
                    }
                    
                    return actionButtons;
                }
            }
        ],
        createdRow: function(row, data, dataIndex) {
            // Make row clickable
            $(row).on('click', function(e) {
                // Don't trigger if clicking on action buttons
                if (!$(e.target).closest('.btn, .dropdown').length) {
                    viewPODetail(data.id_po, e);
                }
            });
        },
        order: [[1, 'desc']],
        pageLength: 10,
        responsive: true,
        autoWidth: false,
        scrollX: false,
        language: {
            processing: '<span>Memuat data...</span>'
        },
        drawCallback: function(settings) {
            // Update progress tab badge count based on filtered records
            if (settings && settings.json) {
                updateProgresCount(settings.json.recordsFiltered || settings.json.recordsTotal || 0);
            }
            // Ensure table is properly displayed after drawing
            $(this.api().table().container()).css('width', '100%');
        }
    });
}

// Initialize PO Delivery DataTable
function initPODeliveryTable() {
    poDeliveryTable = $('#poDeliveryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('/purchasing/api/get-delivery-data') ?>',
            type: 'POST',
            dataType: 'json',
            data: function(d) {
                d.status = $('#globalFilterStatus').val();
                // Add date filter
                if (window.globalDateRangeStart && window.globalDateRangeEnd) {
                    d.start_date = window.globalDateRangeStart;
                    d.end_date = window.globalDateRangeEnd;
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                console.error('Delivery DataTable AJAX error:', {
                    status: xhr.status,
                    textStatus: textStatus,
                    errorThrown: errorThrown,
                    responsePreview: (xhr.responseText || '').substring(0, 500)
                });

                if (window.OptimaNotify && typeof OptimaNotify.error === 'function') {
                    OptimaNotify.error('Gagal memuat data Delivery. Silakan refresh halaman.');
                }
            }
        },
        columns: [
            {
                data: 'packing_list_no',
                name: 'packing_list_no',
                render: function(data) {
                    const packingListNo = data || '-';
                    return `<span class="badge badge-soft-blue delivery-packing-list">${packingListNo}</span>`;
                }
            },
            {
                data: 'no_po',
                name: 'no_po',
                render: function(data, type, row) {
                    const poNumber = data || '-';
                    const supplierName = row.nama_supplier || 'Supplier tidak tersedia';

                    return `
                        <div>
                            <div class="delivery-cell-title">${poNumber}</div>
                            <span class="delivery-cell-subtitle">${supplierName}</span>
                        </div>
                    `;
                }
            },
            {
                data: 'delivery_date',
                name: 'delivery_date',
                render: function(data, type, row) {
                    const deliveryDate = data || '-';
                    const driverName = row.driver_name || 'Driver belum diisi';

                    return `
                        <div>
                            <div class="delivery-cell-title">${deliveryDate}</div>
                            <span class="delivery-cell-subtitle">
                                <i class="fas fa-user me-1"></i>${driverName}
                            </span>
                        </div>
                    `;
                }
            },
            { 
                data: 'total_items', 
                name: 'total_items',
                render: function(data, type, row) {
                    // Display items from selected delivery items (serial_numbers)
                    let selectedItems = [];
                    if (row.serial_numbers) {
                        try {
                            const parsed = JSON.parse(row.serial_numbers);
                            // Ensure it's an array
                            selectedItems = Array.isArray(parsed) ? parsed : [];
                        } catch (e) {
                            console.error('Error parsing serial_numbers:', e);
                            selectedItems = [];
                        }
                    }
                    
                    if (!Array.isArray(selectedItems) || selectedItems.length === 0) {
                        return `<span class="text-muted small fst-italic">
                            <i class="fas fa-box-open me-1"></i>No items
                        </span>`;
                    }
                    
                    // Count items by type
                    const itemCounts = {
                        unit: 0,
                        attachment: 0,
                        battery: 0,
                        charger: 0
                    };
                    
                    selectedItems.forEach(item => {
                        if (item && itemCounts.hasOwnProperty(item.type)) {
                            itemCounts[item.type] += parseInt(item.qty) || 0;
                        }
                    });
                    
                    const itemBadges = [];
                    const totalSelectedItems = Object.values(itemCounts).reduce((sum, qty) => sum + qty, 0);

                    if (totalSelectedItems > 0) {
                        itemBadges.push(`
                            <span class="badge badge-soft-gray" title="Total selected items">
                                ${totalSelectedItems} Total
                            </span>
                        `);
                    }

                    if (itemCounts.unit > 0) {
                        itemBadges.push(`
                            <span class="badge badge-soft-blue" title="Units">
                                ${itemCounts.unit} Unit
                            </span>
                        `);
                    }
                    if (itemCounts.attachment > 0) {
                        itemBadges.push(`
                            <span class="badge badge-soft-cyan" title="Attachments">
                                ${itemCounts.attachment} Att
                            </span>
                        `);
                    }
                    if (itemCounts.battery > 0) {
                        itemBadges.push(`
                            <span class="badge badge-soft-yellow" title="Batteries">
                                ${itemCounts.battery} Bat
                            </span>
                        `);
                    }
                    if (itemCounts.charger > 0) {
                        itemBadges.push(`
                            <span class="badge badge-soft-green" title="Chargers">
                                ${itemCounts.charger} Chg
                            </span>
                        `);
                    }

                    return `<div class="delivery-items-wrap">${itemBadges.join('')}</div>`;
                }
            },
            { 
                data: 'status', 
                name: 'status',
                render: function(data) {
                    const badgeClass = getDeliveryStatusBadgeClass(data);
                    const icon = getDeliveryStatusIcon(data);
                    return `<span class="badge ${badgeClass}">
                        <i class="fas ${icon} me-1"></i>${data}
                    </span>`;
                }
            },
            { 
                data: 'id_delivery',
                name: 'id_delivery',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    // Action buttons based on status (similar to delivery.php)
                    let actionButtons = '';
                    let actionNote = '';
                    const packingListNo = String(row.packing_list_no || '')
                        .replace(/\\/g, '\\\\')
                        .replace(/'/g, "\\'");
                    const printButton = `
                        <button class="btn btn-sm btn-outline-secondary" onclick="printPackingList(${data}, '${packingListNo}', event)" title="Print Packing List">
                            <i class="fas fa-print"></i>
                        </button>
                    `;
                    
                    if (row.status === 'Scheduled') {
                        actionButtons = `
                            <button class="btn btn-sm btn-warning" onclick="assignSerialNumbers(${data})" title="Assign Serial Numbers">
                                <i class="fas fa-barcode me-1"></i>Assign SN
                            </button>
                        `;
                        actionNote = `<span class="text-warning delivery-action-note"><i class="fas fa-hourglass-half me-1"></i>Waiting serial number assignment</span>`;
                    } else if (row.status === 'In Transit') {
                        actionButtons = `
                            <button class="btn btn-sm btn-success" onclick="markAsReceived(${data})" title="Mark as Received">
                                <i class="fas fa-check-circle me-1"></i>Received
                            </button>
                        `;
                        actionNote = `<span class="text-muted delivery-action-note"><i class="fas fa-truck me-1"></i>Waiting warehouse receipt</span>`;
                    } else if (row.status === 'Received') {
                        actionButtons = '<span></span>';
                        actionNote = `<span class="text-info delivery-action-note"><i class="fas fa-clipboard-check me-1"></i>On verification</span>`;
                    } else if (row.status === 'Completed') {
                        actionButtons = '<span></span>';
                        actionNote = `<span class="text-success delivery-action-note"><i class="fas fa-check-circle me-1"></i>Verification completed</span>`;
                    }
                    
                    return `
                        <div class="delivery-actions">
                            <div class="delivery-actions-row">
                                <div class="delivery-actions-left">${actionButtons}</div>
                                <div class="delivery-actions-right">${printButton}</div>
                            </div>
                            ${actionNote ? `<div>${actionNote}</div>` : ''}
                        </div>
                    `;
                }
            },
        ],
        order: [[2, 'desc']], // Order by delivery date descending
        pageLength: 10,
        responsive: false,
        autoWidth: false,
        scrollX: false,
        language: {
            processing: '<span>Memuat data...</span>'
        },
        drawCallback: function(settings) {
            // Update delivery count
            updateDeliveryCount(settings.json.recordsFiltered || 0);
            // Ensure table is properly displayed after drawing
            $(this.api().table().container()).css('width', '100%');
        },
        createdRow: function(row, data, dataIndex) {
            // Make row clickable
            $(row).on('click', function(e) {
                // Don't trigger if clicking on action buttons
                if (!$(e.target).closest('.btn, .dropdown').length) {
                    viewDeliveryDetail(data.id_delivery, e);
                }
            });
        }
    });
}

// Initialize Completed PO DataTable
function initUnitAttachmentPOCompletedTable() {
    unitAttachmentPOCompletedTable = $('#unitAttachmentPOCompletedTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('/purchasing/api/get-unified-po-data') ?>',
            type: 'POST',
            data: function(d) {
                d.po_type = 'unit_attachment';
                d.tab_type = 'completed';
                d.status = $('#globalFilterStatus').val();
                // Add date filter
                if (window.globalDateRangeStart && window.globalDateRangeEnd) {
                    d.start_date = window.globalDateRangeStart;
                    d.end_date = window.globalDateRangeEnd;
                }
            }
        },
        columns: [
            { data: 'no_po', name: 'no_po' },
            { data: 'tanggal_po', name: 'tanggal_po' },
            { data: 'nama_supplier', name: 'nama_supplier' },
            { 
                data: 'status', 
                name: 'status',
                render: function(data, type, row) {
                    return renderPOStatusBadge(row);
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    // COMPLETED TAB - Get item breakdown from new total columns
                    const totalUnit = parseInt(row.total_unit, 10) || 0;
                    const totalAttachment = parseInt(row.total_attachment, 10) || 0;
                    const totalBattery = parseInt(row.total_battery, 10) || 0;
                    const totalCharger = parseInt(row.total_charger, 10) || 0;
                    
                    if (totalUnit === 0 && totalAttachment === 0 && totalBattery === 0 && totalCharger === 0) {
                        return `<span class="text-muted small fst-italic">
                            <i class="fas fa-box-open me-1"></i>No items
                        </span>`;
                    }
                    
                    // Create item badges with icons and colors for completed items
                    const itemBadges = [];
                    
                    if (totalUnit > 0) {
                        itemBadges.push(`
                            <span class="badge badge-soft-blue me-1 mb-1">
                                <i class="fas fa-truck me-1"></i>${totalUnit} Units
                            </span>
                        `);
                    }
                    
                    if (totalAttachment > 0) {
                        itemBadges.push(`
                            <span class="badge badge-soft-cyan me-1 mb-1">
                                <i class="fas fa-puzzle-piece me-1"></i>${totalAttachment} Attachment
                            </span>
                        `);
                    }
                    
                    if (totalBattery > 0) {
                        itemBadges.push(`
                            <span class="badge badge-soft-yellow me-1 mb-1">
                                <i class="fas fa-battery-half me-1"></i>${totalBattery} Battery
                            </span>
                        `);
                    }
                    
                    if (totalCharger > 0) {
                        itemBadges.push(`
                            <span class="badge badge-soft-green me-1 mb-1">
                                <i class="fas fa-plug me-1"></i>${totalCharger} Charger
                            </span>
                        `);
                    }
                    
                    // Split badges into 2 rows for better layout
                    let row1 = itemBadges.slice(0, 2);
                    let row2 = itemBadges.slice(2);
                    
                    let result = `<div class="d-flex flex-column">`;
                    if (row1.length > 0) {
                        result += `<div class="d-flex flex-wrap">${row1.join('')}</div>`;
                    }
                    if (row2.length > 0) {
                        result += `<div class="d-flex flex-wrap">${row2.join('')}</div>`;
                    }
                    result += `</div>`;
                    
                    return result;
                }
            },
            { 
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    const totalItems = parseInt(row.total_items_actual, 10) || 0;
                    const verified = parseInt(row.total_qty_verified, 10) || 0;
                    
                    if (totalItems === 0) {
                        return `<span class="text-muted small fst-italic">-</span>`;
                    }

                    const percentage = totalItems > 0 ? Math.round((verified / totalItems) * 100) : 0;
                    const remaining = totalItems - verified;

                    return `
                        <div class="progress" title="Verifikasi: ${verified} dari ${totalItems} item (${remaining} tersisa)" style="height: 22px;">
                            <div class="progress-bar progress-bar-striped ${percentage >= 100 ? 'bg-success' : percentage > 0 ? 'bg-warning' : 'bg-secondary'}" role="progressbar" style="width: ${percentage}%;">
                                ${verified} / ${totalItems}
                            </div>
                        </div>
                        <small class="text-muted">${percentage}% verified</small>
                    `;
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return renderPODeliverySummary(row);
                }
            },
            { 
                data: 'id_po',
                name: 'id_po',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    const totalDeliveries = parseInt(row.total_deliveries, 10) || 0;
                    const completedDeliveries = parseInt(row.completed_deliveries, 10) || 0;
                    const totalItems = parseInt(row.total_qty_ordered, 10) || 0;
                    const receivedItems = parseInt(row.total_qty_received, 10) || 0;
                    const totalVerificationItems = parseInt(row.total_items_actual, 10) || 0;
                    const verifiedItems = parseInt(row.total_qty_verified, 10) || 0;
                    
                    let actionButtons = '';
                    
                    // For completed items, show status instead of buttons
                    if (row.status === 'Selesai dengan Catatan') {
                        // Special actions for partial rejection
                        <?php if ($can_edit): ?>
                        actionButtons = `
                            <button class="btn btn-sm btn-warning" onclick="reverifyPO(${data}, event)">
                                <i class="fas fa-redo me-1"></i>Re-verify
                            </button>
                        `;
                        <?php else: ?>
                        actionButtons = `
                            <button class="btn btn-sm btn-secondary" disabled title="Access denied: You do not have permission to re-verify PO">
                                <i class="fas fa-lock me-1"></i>Re-verify
                            </button>
                        `;
                        <?php endif; ?>
                    } else if (totalVerificationItems > 0 && verifiedItems >= totalVerificationItems) {
                        actionButtons = `<span class="text-success small">
                            <i class="fas fa-check-circle me-1"></i>Verification Completed
                        </span>`;
                    } else if (receivedItems >= totalItems) {
                        actionButtons = `<span class="text-info small">
                            <i class="fas fa-truck me-1"></i>Delivery Completed
                        </span>`;
                    } else {
                        // Default status for completed tab
                        actionButtons = `<span class="text-warning small">
                            <i class="fas fa-clock me-1"></i>Pending Verification
                        </span>`;
                    }
                    
                    return actionButtons;
                }
            }
        ],
        createdRow: function(row, data, dataIndex) {
            // Make row clickable
            $(row).on('click', function(e) {
                // Don't trigger if clicking on action buttons
                if (!$(e.target).closest('.btn, .dropdown').length) {
                    viewPODetail(data.id_po, e);
                }
            });
        },
        order: [[1, 'desc']],
        pageLength: 10,
        responsive: true,
        autoWidth: false,
        scrollX: false,
        language: {
            processing: '<span>Memuat data...</span>'
        },
        drawCallback: function(settings) {
            // Ensure table is properly displayed after drawing
            $(this.api().table().container()).css('width', '100%');
        }
    });
}

// Utility functions
function getStatusBadgeClass(status) {
    const classes = {
        'pending': 'badge-soft-yellow',
        'approved': 'badge-soft-cyan',
        'completed': 'badge-soft-green',
        'Selesai dengan Catatan': 'badge-soft-orange',
        'cancelled': 'badge-soft-red',
        'Pending': 'badge-soft-yellow',
        'Approved': 'badge-soft-cyan',
        'Completed': 'badge-soft-green',
        'Cancelled': 'badge-soft-red'
    };
    return classes[status] || 'badge-soft-gray';
}

function getStatusIcon(status) {
    const icons = {
        'pending': 'fa-clock',
        'approved': 'fa-check-circle',
        'completed': 'fa-check-double',
        'Selesai dengan Catatan': 'fa-exclamation-circle',
        'cancelled': 'fa-times-circle',
        'Pending': 'fa-clock',
        'Approved': 'fa-check-circle',
        'Completed': 'fa-check-double',
        'Cancelled': 'fa-times-circle'
    };
    return icons[status] || 'fa-question-circle';
}

function getVerificationMetrics(row) {
    const totalItems = parseInt(row.total_items_actual, 10) || 0;
    const verified = parseInt(row.total_qty_verified, 10) || 0;
    const percentage = totalItems > 0 ? Math.min(100, Math.round((verified / totalItems) * 100)) : 0;

    return {
        totalItems,
        verified,
        remaining: Math.max(0, totalItems - verified),
        percentage
    };
}

function getDeliveryMetrics(row) {
    const totalDeliveries = parseInt(row.total_deliveries, 10) || 0;
    const completedDeliveries = parseInt(row.completed_deliveries, 10) || 0;
    const totalOrdered = parseInt(row.total_qty_ordered, 10) || 0;
    const receivedItems = parseInt(row.total_qty_received, 10) || 0;
    const percentage = totalOrdered > 0 ? Math.min(100, Math.round((receivedItems / totalOrdered) * 100)) : 0;

    return {
        totalDeliveries,
        completedDeliveries,
        totalOrdered,
        receivedItems,
        remainingItems: Math.max(0, totalOrdered - receivedItems),
        percentage
    };
}

function renderPOStatusBadge(row) {
    const rawStatus = row.status || 'pending';
    const normalizedStatus = String(rawStatus).toLowerCase();
    const verification = getVerificationMetrics(row);
    const delivery = getDeliveryMetrics(row);

    let label = rawStatus;
    let badgeClass = getStatusBadgeClass(rawStatus);
    let icon = getStatusIcon(rawStatus);

    if (verification.totalItems > 0 && verification.percentage >= 100) {
        if (rawStatus === 'Selesai dengan Catatan') {
            label = 'Verification Complete (Notes)';
            badgeClass = 'badge-soft-orange';
            icon = 'fa-clipboard-check';
        } else {
            label = 'Verification Completed';
            badgeClass = 'badge-soft-green';
            icon = 'fa-clipboard-check';
        }
    } else if (delivery.totalOrdered > 0 && delivery.receivedItems >= delivery.totalOrdered) {
        label = 'Delivery Completed';
        badgeClass = 'badge-soft-cyan';
        icon = 'fa-truck';
    } else if (delivery.totalDeliveries > 0) {
        label = 'Delivery In Progress';
        badgeClass = 'badge-soft-yellow';
        icon = 'fa-truck';
    } else if (normalizedStatus === 'approved') {
        label = 'Ready for Delivery';
        badgeClass = 'badge-soft-blue';
        icon = 'fa-check-circle';
    }

    return `<span class="badge ${badgeClass}">
        <i class="fas ${icon} me-1"></i>${label}
    </span>`;
}

function renderPODeliverySummary(row) {
    const delivery = getDeliveryMetrics(row);
    const verification = getVerificationMetrics(row);

    if (delivery.totalDeliveries === 0) {
        return `<div class="small">
            <div class="text-muted">
                <i class="fas fa-truck me-1"></i>No deliveries yet
            </div>
            <div class="text-muted mt-1">
                <small><i class="fas fa-clipboard-check me-1"></i>Verification: ${verification.verified}/${verification.totalItems}</small>
            </div>
        </div>`;
    }

    let statusText = '';
    let progressBar = '';

    if (delivery.totalOrdered > 0 && delivery.receivedItems >= delivery.totalOrdered) {
        statusText = `<span class="text-success">
            <i class="fas fa-check-circle me-1"></i>All Deliveries Completed
        </span>`;
        progressBar = `<div class="progress mt-1" style="height: 16px;">
            <div class="progress-bar bg-success" style="width: 100%;">100%</div>
        </div>`;
    } else if (delivery.receivedItems > 0 && delivery.receivedItems < delivery.totalOrdered) {
        statusText = `<span class="text-warning">
            <i class="fas fa-clock me-1"></i>Delivered: ${delivery.receivedItems}/${delivery.totalOrdered} items
        </span>`;
        progressBar = `<div class="progress mt-1" style="height: 16px;">
            <div class="progress-bar bg-warning" style="width: ${delivery.percentage}%;">
                ${delivery.percentage}%
            </div>
        </div>`;
    } else {
        statusText = `<span class="text-primary">
            <i class="fas fa-truck me-1"></i>Scheduled / In Transit
        </span>`;
    }

    const verificationHint = verification.totalItems > 0
        ? (verification.percentage >= 100
            ? `<div class="text-success mt-1"><small><i class="fas fa-clipboard-check me-1"></i>Verification completed</small></div>`
            : `<div class="text-muted mt-1"><small><i class="fas fa-clipboard-check me-1"></i>Verification pending: ${verification.verified}/${verification.totalItems}</small></div>`)
        : '';

    return `
        <div class="small">
            <div class="mb-1">${statusText}</div>
            ${progressBar}
            <div class="text-muted mt-1">
                <small>${delivery.completedDeliveries}/${delivery.totalDeliveries} deliveries completed</small>
            </div>
            ${verificationHint}
        </div>
    `;
}

function getDeliveryStatusBadgeClass(status) {
    const classes = {
        'Not Started': 'bg-secondary',
        'Partial': 'bg-warning',
        'Complete': 'bg-success',
        'Over Delivered': 'bg-danger'
    };
    return classes[status] || 'bg-secondary';
}

// Action functions
let currentPOId = null;
let currentDeliveryId = null;

// ── PO Information Edit ───────────────────────────────────────
function togglePoInfoEdit(show) {
    if (show) {
        // Populate edit form from stored PO data
        const po = $('#viewPOModal').data('po') || {};
        $('#edit_no_po').val(po.no_po || '');
        $('#edit_tanggal_po').val(po.tanggal_po || '');
        $('#edit_supplier_id').val(po.supplier_id || '');
        $('#edit_invoice_no').val(po.invoice_no || '');
        $('#edit_invoice_date').val(po.invoice_date || '');
        $('#edit_bl_date').val(po.bl_date || '');
        $('#edit_keterangan_po').val(po.keterangan_po || '');
        $('#poInfoAlert').addClass('d-none').html('');
        $('#poInfoView').hide();
        $('#poInfoEditForm').show();
        $('#btnEditPoInfo').hide();
    } else {
        $('#poInfoEditForm').hide();
        $('#poInfoView').show();
        $('#btnEditPoInfo').show();
    }
}

function savePoInfo() {
    const poId = currentPOId;
    if (!poId) return;

    const no_po = $('#edit_no_po').val().trim();
    const tanggal_po = $('#edit_tanggal_po').val();
    const supplier_id = $('#edit_supplier_id').val();

    if (!no_po || !tanggal_po || !supplier_id) {
        $('#poInfoAlert').removeClass('d-none alert-success').addClass('alert alert-danger')
            .html('<i class="fas fa-exclamation-triangle me-1"></i>PO Number, PO Date, dan Supplier wajib diisi.');
        return;
    }

    const btn = $('#btnSavePoInfo').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...');

    $.ajax({
        url  : '<?= base_url('purchasing/update-po-info/') ?>' + poId,
        type : 'POST',
        data : {
            [window.csrfTokenName] : window.csrfTokenValue,
            no_po          : no_po,
            tanggal_po     : tanggal_po,
            supplier_id    : supplier_id,
            invoice_no     : $('#edit_invoice_no').val(),
            invoice_date   : $('#edit_invoice_date').val(),
            bl_date        : $('#edit_bl_date').val(),
            keterangan_po  : $('#edit_keterangan_po').val(),
        },
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                // Update view-mode display
                $('#poNumber').text(res.no_po || '-');
                $('#poDate').text(res.tanggal_po || '-');
                $('#poSupplier').text(res.nama_supplier || '-');
                $('#poInvoice').text(res.invoice_no || '-');
                if (res.invoice_date) { $('#poInvoiceDate').text(res.invoice_date); $('#poViewInvoiceDateRow').show(); }
                else { $('#poViewInvoiceDateRow').hide(); }
                if (res.bl_date) { $('#poBlDate').text(res.bl_date); $('#poViewBlDateRow').show(); }
                else { $('#poViewBlDateRow').hide(); }
                if (res.keterangan_po) { $('#poNotes').text(res.keterangan_po); $('#poViewNotesRow').show(); }
                else { $('#poViewNotesRow').hide(); }

                // Update stored po data
                const po = $('#viewPOModal').data('po') || {};
                $.extend(po, {
                    no_po: res.no_po, tanggal_po: res.tanggal_po, nama_supplier: res.nama_supplier,
                    supplier_id: supplier_id, invoice_no: res.invoice_no, invoice_date: res.invoice_date,
                    bl_date: res.bl_date, keterangan_po: res.keterangan_po,
                });
                $('#viewPOModal').data('po', po);

                togglePoInfoEdit(false);

                if (window.OptimaNotify) OptimaNotify.success(res.message || 'Purchase Order berhasil diperbarui.');

                // Refresh DataTable if visible
                if ($.fn.DataTable && $.fn.DataTable.isDataTable('#poTable')) {
                    try { $('#poTable').DataTable().ajax.reload(null, false); } catch(e) {}
                }
            } else {
                const errHtml = res.errors
                    ? '<ul class="mb-0 mt-1">' + Object.values(res.errors).map(e => '<li>' + e + '</li>').join('') + '</ul>'
                    : '';
                $('#poInfoAlert').removeClass('d-none alert-success').addClass('alert alert-danger')
                    .html('<i class="fas fa-exclamation-triangle me-1"></i>' + (res.message || 'Gagal menyimpan.') + errHtml);
            }
        },
        error: function() {
            $('#poInfoAlert').removeClass('d-none alert-success').addClass('alert alert-danger')
                .html('<i class="fas fa-exclamation-triangle me-1"></i>Terjadi kesalahan. Silakan coba lagi.');
        },
        complete: function() {
            btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Simpan');
        },
    });
}

function viewPODetail(poId, event) {
    if (event) event.preventDefault();
    if (typeof $ === 'undefined') return;
    
    currentPOId = poId;
    
    // Show modal with loading state
    $('#viewPOModal').modal('show');
    $('#poLoadingState').show();
    $('#poContent').hide();
    
    // Reset tabs to default state - scope to modal only
    const modalContainer = $('#viewPOModal');
    modalContainer.find('#items-tab').addClass('active').attr('aria-selected', 'true');
    modalContainer.find('#deliveries-tab').removeClass('active').attr('aria-selected', 'false');
    modalContainer.find('#items-pane').addClass('show active');
    modalContainer.find('#deliveries-pane').removeClass('show active');
    
    $.ajax({
        type: 'GET',
        url: '<?= base_url('/purchasing/api/po-detail/') ?>' + poId,
        success: function(response) {
            if (response.success) {
                renderPODetailNew(response.data);
                $('#poLoadingState').hide();
                $('#poContent').show();

                $('#printPOBtn').show();
                initializePODetailTabs();

                $('#viewPOModal').off('hidden.bs.modal.printpo').on('hidden.bs.modal.printpo', function() {
                    $('#printPOBtn').hide();
                    // Reset edit mode when modal closes
                    togglePoInfoEdit(false);
                });
            } else {
                $('#poLoadingState').html(`
                    <div class="text-center text-danger p-5">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <h5 class="mt-3">Error Fetching Data</h5>
                        <p class="text-muted">${response.message || 'An error occurred while loading PO details'}</p>
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading PO detail:', error);
            $('#poLoadingState').html(`
                <div class="text-center text-danger p-5">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h5 class="mt-3">Error Fetching Data</h5>
                    <p class="text-muted">An error occurred while loading PO details</p>
                </div>
            `);
        }
    });
}

function printPO(poId) {
    if (typeof $ === 'undefined') return;
    
    // Open print window
    const printUrl = '<?= base_url('/purchasing/print_po/') ?>' + poId;
    window.open(printUrl, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
}

function printPOFromModal() {
    if (currentPOId) {
        printPO(currentPOId);
    }
}

function getPackingListInfo(deliveries, itemType) {
    if (!deliveries || deliveries.length === 0) {
        return '<span class="text-muted">No packing list yet</span>';
    }
    
    let packingInfo = '';
    deliveries.forEach(delivery => {
        if (delivery.serial_numbers) {
            try {
                const serialData = JSON.parse(delivery.serial_numbers);
                const itemCount = serialData.filter(item => 
                    item.type && item.type.toLowerCase() === itemType
                ).reduce((sum, item) => sum + (parseInt(item.qty) || 0), 0);
                
                if (itemCount > 0) {
                    packingInfo += `
                        <div class="mb-1">
                            <span class="badge bg-light text-dark me-2">${delivery.packing_list_no || 'N/A'}</span>
                            <small class="text-muted">: ${itemCount} (${itemType} delivered in this packing list)</small>
                        </div>
                    `;
                }
            } catch (e) {
                console.error('Error parsing serial_numbers:', e);
            }
        }
    });
    
    return packingInfo || '<span class="text-muted">No packing list yet</span>';
}

// Render specification details based on item type
function renderSpecificationDetails(item, itemType) {
    const type = (itemType || item.item_type || 'Unit').toLowerCase();
    
    if (type === 'unit') {
        // Parse package_flags — nilai sesuai checkbox: fork_standard, battery, charger, attachment, accessories
        let flags = [];
        try { flags = item.package_flags ? JSON.parse(item.package_flags) : []; } catch(e) {}
        const flagMap = {
            fork_standard: ['Fork',      'bg-secondary'],
            battery:       ['Baterai',   'bg-warning text-dark'],
            charger:       ['Charger',   'bg-info text-dark'],
            attachment:    ['Attachment','bg-success'],
            accessories:   ['Aksesori',  'bg-light text-dark border'],
        };
        const flagBadges = flags.length
            ? flags.map(f => flagMap[f] ? `<span class="badge ${flagMap[f][1]} me-1">${flagMap[f][0]}</span>` : '').join('')
            : '<span class="text-muted small">-</span>';

        // Kondisi badge
        const kondisiColor = {Baru:'bg-primary', Bekas:'bg-warning text-dark', Rekondisi:'bg-info text-dark'};
        const kondisi = item.status_penjualan || '-';
        const kondisiBadge = kondisiColor[kondisi]
            ? `<span class="badge ${kondisiColor[kondisi]}">${kondisi}</span>`
            : `<span class="text-muted">-</span>`;

        // Aksesori
        const aksesori = item.unit_accessories ? `<span class="text-dark">${item.unit_accessories}</span>` : '<span class="text-muted">-</span>';

        // Vendor spec (tampilkan penuh)
        const vendorSpec = item.vendor_spec_text
            ? `<div class="p-2 bg-light rounded border" style="font-size:.85em;white-space:pre-wrap;word-break:break-word;">${item.vendor_spec_text}</div>`
            : '<span class="text-muted small">-</span>';

        // Optional component fields — only render if filled
        const optionalFields = [
            ['Mast Type',    item.tipe_mast],
            ['Engine Type',  item.merk_mesin],
            ['Tire Type',    item.tipe_ban],
            ['Wheel Type',   item.tipe_roda],
            ['Valve',        item.jumlah_valve],
            ['Catatan',      item.keterangan],
        ].filter(([, v]) => v && String(v).trim() !== '');

        const optionalHtml = optionalFields.length
            ? `<div class="col-md-6">${optionalFields.map(([label, val]) =>
                `<div class="mb-2"><strong>${label}:</strong> ${val}</div>`
              ).join('')}</div>`
            : '';

        // Unit specifications
        return `
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-2"><strong>Departemen:</strong> ${item.nama_departemen || '-'}</div>
                    <div class="mb-2"><strong>Unit Type:</strong> ${item.jenis_unit || '-'}</div>
                    <div class="mb-2"><strong>Brand:</strong> ${item.merk_unit || '-'}</div>
                    <div class="mb-2"><strong>Model:</strong> ${item.model_unit || '-'}</div>
                    <div class="mb-2"><strong>Tahun:</strong> ${item.tahun_po || '-'}</div>
                    <div class="mb-2"><strong>Kapasitas:</strong> ${item.kapasitas_unit || '-'}</div>
                    <div class="mb-2"><strong>Kondisi:</strong> ${kondisiBadge}</div>
                </div>
                ${optionalHtml}
            </div>
            <div class="mt-2 pt-2 border-top">
                <div class="mb-2">
                    <strong>Paket Termasuk:</strong>&nbsp;${flagBadges}
                </div>
                ${item.unit_accessories ? `<div class="mb-2"><strong>Aksesori:</strong> ${aksesori}</div>` : ''}
            </div>
            ${item.vendor_spec_text ? `
            <div class="mt-2 pt-2 border-top">
                <div class="mb-1"><strong><i class="fas fa-file-alt me-1 text-secondary"></i>Spesifikasi Vendor (PI):</strong></div>
                ${vendorSpec}
            </div>` : ''}
        `;
    } else if (type === 'attachment') {
        // Attachment specifications
        return `
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-2">
                        <strong>Attachment Type:</strong> ${item.tipe_attachment || '-'}
                    </div>
                    <div class="mb-2">
                        <strong>Brand:</strong> ${item.merk_attachment || '-'}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <strong>Model:</strong> ${item.model_attachment || '-'}
                    </div>
                    <div class="mb-2">
                        <strong>Notes:</strong> ${item.keterangan || '-'}
                    </div>
                </div>
            </div>
        `;
    } else if (type === 'battery') {
        // Battery specifications
        return `
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-2">
                        <strong>Battery Type:</strong> ${item.jenis_baterai || '-'}
                    </div>
                    <div class="mb-2">
                        <strong>Battery Brand:</strong> ${item.merk_baterai || '-'}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <strong>Battery Type:</strong> ${item.tipe_baterai || '-'}
                    </div>
                    <div class="mb-2">
                        <strong>Notes:</strong> ${item.keterangan || '-'}
                    </div>
                </div>
            </div>
        `;
    } else if (type === 'charger') {
        // Charger specifications
        return `
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-2">
                        <strong>Charger Brand:</strong> ${item.merk_charger || '-'}
                    </div>
                    <div class="mb-2">
                        <strong>Charger Type:</strong> ${item.tipe_charger || '-'}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <strong>Notes:</strong> ${item.keterangan || '-'}
                    </div>
                </div>
            </div>
        `;
    } else {
        // Default/Unknown type - show generic info
        return `
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-2">
                        <strong>Item Name:</strong> ${item.item_name || '-'}
                    </div>
                    <div class="mb-2">
                        <strong>Notes:</strong> ${item.keterangan || '-'}
                    </div>
                </div>
            </div>
        `;
    }
}

function renderSerialNumbers(items, specId) {
    if (!Array.isArray(items) || items.length === 0) {
        return `
            <div class="mt-3 pt-3 border-top">
                <h6 class="mb-2 text-secondary">
                    <i class="fas fa-barcode me-2"></i>Serial Numbers:
                </h6>
                <span class="text-muted small fst-italic"><i class="fas fa-clock me-1"></i>Serial number belum diisi</span>
            </div>
        `;
    }

    const serialNumbers = items.map((item, index) => {
        const sn = item.serial_number_po || item.serial_number || item.sn_baterai || item.sn_charger || '';
        return { index: index + 1, serialNumber: sn };
    });

    // Check if ALL serial numbers are empty
    const allEmpty = serialNumbers.every(s => !s.serialNumber || s.serialNumber.trim() === '');
    if (allEmpty) {
        return `
            <div class="mt-3 pt-3 border-top" id="serial-group-${specId}">
                <h6 class="mb-2 text-secondary">
                    <i class="fas fa-barcode me-2"></i>Serial Numbers:
                    <span class="badge bg-light text-dark border ms-1">${serialNumbers.length} unit</span>
                </h6>
                <span class="text-warning small fst-italic"><i class="fas fa-exclamation-circle me-1"></i>Serial number belum diisi untuk semua unit</span>
            </div>
        `;
    }

    let serialNumbersHtml = '';
    const totalItems = serialNumbers.length;
    const totalRows = Math.ceil(totalItems / 2);

    for (let i = 0; i < totalRows; i++) {
        const item1 = serialNumbers[i * 2];
        const item2 = (i * 2 + 1) < totalItems ? serialNumbers[i * 2 + 1] : null;

        const renderCell = (s) => {
            const isEmpty = !s.serialNumber || s.serialNumber.trim() === '';
            return `<div class="d-flex align-items-center">
                <span class="badge bg-light text-dark me-2" style="min-width:35px;text-align:center;">${s.index}</span>
                ${isEmpty
                    ? `<span class="text-warning fst-italic small"><i class="fas fa-exclamation-circle me-1"></i>Belum diisi</span>`
                    : `<code class="flex-grow-1 text-success" style="font-size:.9em;">${s.serialNumber}</code>`
                }
            </div>`;
        };

        serialNumbersHtml += `
            <div class="row mb-2">
                <div class="col-md-6">${renderCell(item1)}</div>
                ${item2 ? `<div class="col-md-6">${renderCell(item2)}</div>` : '<div class="col-md-6"></div>'}
            </div>`;
    }

    return `
        <div class="mt-3 pt-3 border-top" id="serial-group-${specId}">
            <h6 class="mb-2 text-secondary">
                <i class="fas fa-barcode me-2"></i>Serial Numbers:
            </h6>
            ${serialNumbersHtml}
        </div>
    `;
}

function renderPODetailNew(data) {
    const po = data?.po || {};
    const items = Array.isArray(data?.items) ? data.items : [];
    const deliveries = Array.isArray(data?.deliveries) ? data.deliveries : [];
    const deliveryItems = data?.delivery_items || {};
    const summary = data?.summary || {};

    const totalItemsOrdered = parseInt(summary.total_items_ordered, 10) || items.length || 0;
    const totalItemsReceived = parseInt(summary.total_items_received, 10) || 0;
    const verifiedItemsCount = (parseInt(summary.verified_items, 10) || 0) + (parseInt(summary.rejected_items, 10) || 0);
    const totalDeliveries = parseInt(summary.total_deliveries, 10) || deliveries.length || 0;
    const completedDeliveries = parseInt(summary.completed_deliveries, 10) || 0;

    const itemBreakdown = summary.item_type_breakdown || {};
    const breakdownHtml = Object.entries(itemBreakdown).map(([type, count]) => {
        const badgeClass = getItemTypeBadgeClass(type);
        const icon = getItemTypeIcon(type);
        return `<span class="badge ${badgeClass} me-1 mb-1"><i class="${icon} me-1"></i>${count} ${type}</span>`;
    }).join('');

    $('#poNumber').text(po.no_po || '-');
    $('#poDate').text(po.tanggal_po || '-');
    $('#poSupplier').text(po.nama_supplier || '-');
    $('#poContact').text(po.contact_person || po.pic_supplier || '-');
    $('#poStatus').html(getStatusBadge(po.status || 'pending'));
    $('#poInvoice').text(po.invoice_no || '-');

    // Extra view-mode fields
    if (po.invoice_date) {
        $('#poInvoiceDate').text(po.invoice_date);
        $('#poViewInvoiceDateRow').show();
    } else { $('#poViewInvoiceDateRow').hide(); }
    if (po.bl_date) {
        $('#poBlDate').text(po.bl_date);
        $('#poViewBlDateRow').show();
    } else { $('#poViewBlDateRow').hide(); }
    if (po.keterangan_po) {
        $('#poNotes').text(po.keterangan_po);
        $('#poViewNotesRow').show();
    } else { $('#poViewNotesRow').hide(); }

    // Store full PO data for edit form
    $('#viewPOModal').data('po', po);

    $('#totalItemsOrdered').html(breakdownHtml || `<span class="text-muted">${totalItemsOrdered} items</span>`);
    $('#deliveryProgress').text(`${completedDeliveries}/${totalDeliveries}`);
    $('#totalItemsReceived').text(totalItemsReceived);
    $('#verifiedItems').text(verifiedItemsCount);
    $('#deliveriesCount').text(totalDeliveries);
    // #itemsCount badge diset di dalam renderItemsTable (jumlah line-group)

    renderItemsTable(items, summary, deliveries);
    renderDeliveriesContent(deliveries, deliveryItems);
}

function renderItemsTable(items, summary = null, deliveries = []) {
    // Group unit items by po_line_group_id (new POs) or item_name (old POs fallback)
    // Non-unit items group by item_name+type
    const groupedBySpec = {};
    items.forEach((item, idx) => {
        let key;
        if ((item.item_type || 'unit').toLowerCase() === 'unit') {
            if (item.po_line_group_id) {
                key = 'grp_' + item.po_line_group_id;
            } else {
                // Fallback: group by item_name (brand+model) like legacy behaviour
                key = 'name_' + (item.item_name || (item.merk_unit || '') + '_' + (item.model_unit || ''));
            }
        } else {
            key = (item.item_name || 'Unknown') + '_' + (item.item_type || '');
        }
        if (!groupedBySpec[key]) groupedBySpec[key] = [];
        groupedBySpec[key].push(item);
    });

    // Update items badge to number of line groups
    $('#itemsCount').text(Object.keys(groupedBySpec).length || 0);

    // Get delivered count by type from summary
    const deliveredByType = summary ? summary.delivered_by_type || {} : {};

    let itemsHtml = '';

    if (Object.keys(groupedBySpec).length === 0) {
        itemsHtml = '<div class="text-center p-4"><i class="fas fa-box-open fa-2x text-muted mb-3"></i><p class="text-muted">Belum ada item di PO ini</p></div>';
    } else {
        Object.values(groupedBySpec).forEach((specItems, specIndex) => {
            const first = specItems[0];
            const itemType = first.item_type || 'Unit';
            const itemTypeLower = itemType.toLowerCase();
            const totalDelivered = deliveredByType[itemTypeLower] || 0;
            const badgeClass = getItemTypeBadgeClass(itemType);
            const typeIcon = getItemTypeIcon(itemType);
            const safeId = 'grp_' + specIndex;

            // Build header label
            const baseLabel = first.item_name || (first.merk_unit ? first.merk_unit + ' ' + (first.model_unit || '') : 'Unknown');

            // Extra info badges for unit
            let extraBadges = '';
            if (itemTypeLower === 'unit') {
                if (first.nama_departemen) extraBadges += `<span class="badge bg-light text-dark border ms-1">${first.nama_departemen}</span>`;
                if (first.kapasitas_unit) extraBadges += `<span class="badge bg-light text-dark border ms-1">${first.kapasitas_unit}</span>`;
                const kondisiColor = {Baru:'bg-primary', Bekas:'bg-warning text-dark', Rekondisi:'bg-info text-dark'};
                const k = first.status_penjualan;
                if (k && kondisiColor[k]) extraBadges += `<span class="badge ${kondisiColor[k]} ms-1">${k}</span>`;
            }

            itemsHtml += `
                <div class="mb-3" style="border-radius: 8px; border: 1px solid #e9ecef; background: #f8f9fa;">
                    <div class="p-3" style="cursor: pointer; border-radius: 8px;" onclick="toggleSpecGroup('${safeId}')">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center flex-wrap gap-1">
                                <i class="fas fa-chevron-down me-2" id="icon-${safeId}" style="color: #6c757d;"></i>
                                <i class="${typeIcon} me-1" style="color: #007bff;"></i>
                                <strong>${baseLabel.trim()}</strong>
                                <span class="badge ${badgeClass} ms-1">${specItems.length} ${itemType}</span>
                                ${extraBadges}
                            </div>
                            <div class="text-end">
                                <small class="text-muted">Delivered: ${totalDelivered}</small>
                            </div>
                        </div>
                    </div>
                    <div class="collapse" id="group-${safeId}">
                        <div class="px-3 pb-3">
                            <div class="border-top pt-3">
                                <div class="p-3" style="background: white; border-radius: 6px; border: 1px solid #e9ecef;">
                                    <h6 class="mb-3 text-primary">Specification Details:</h6>
                                    ${renderSpecificationDetails(first, itemType)}

                                    <!-- Serial Numbers Section -->
                                    ${renderSerialNumbers(specItems, safeId)}

                                    <!-- Packing List Information -->
                                    <div class="mt-3 pt-3 border-top">
                                        <h6 class="mb-2 text-info">
                                            <i class="fas fa-box me-2"></i>Related Packing List:
                                        </h6>
                                        ${getPackingListInfo(deliveries, itemTypeLower)}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }

    $('#poItemsContent').html(itemsHtml);
}

// Toggle specification group visibility
function toggleSpecGroup(specId) {
    const group = document.getElementById(`group-${specId}`);
    const icon = document.getElementById(`icon-${specId}`);
    
    if (group.classList.contains('show')) {
        group.classList.remove('show');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    } else {
        group.classList.add('show');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    }
}

function renderDeliveriesContent(deliveries, deliveryItems) {
    let deliveriesHtml = '';
    
    if (deliveries.length === 0) {
        deliveriesHtml = `
            <div class="text-center p-4">
                <i class="fas fa-truck fa-2x text-muted mb-3"></i>
                <h6 class="text-muted">No deliveries yet</h6>
                <p class="text-muted small">Deliveries will appear once created</p>
                <?php if ($can_edit): ?>
                <button class="btn btn-primary btn-sm" onclick="createDelivery()">
                    <i class="fas fa-plus me-1"></i>Create Delivery
                </button>
                <?php else: ?>
                <button class="btn btn-secondary btn-sm" disabled title="Access denied: You do not have permission to create delivery">
                    <i class="fas fa-lock me-1"></i>Create Delivery
                </button>
                <?php endif; ?>
            </div>
        `;
    } else {
        deliveries.forEach((delivery, index) => {
            const statusBadge = getDeliveryStatusBadge(delivery.status);
            const itemsInDelivery = deliveryItems[delivery.id_delivery] || [];
            
            // Icon based on status
            let statusIcon = 'fa-clock';
            if (delivery.status === 'In Transit') statusIcon = 'fa-truck';
            else if (delivery.status === 'Received') statusIcon = 'fa-check-circle';
            else if (delivery.status === 'Cancelled') statusIcon = 'fa-times-circle';
            
            deliveriesHtml += `
                <div class="card mb-3 border-start border-4 ${delivery.status === 'Received' ? 'border-success' : (delivery.status === 'In Transit' ? 'border-warning' : 'border-secondary')}">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">
                                <i class="fas ${statusIcon} me-2"></i>
                                Delivery #${delivery.delivery_sequence || index + 1}
                                ${delivery.packing_list_no ? ` - ${delivery.packing_list_no}` : ''}
                            </h6>
                            <small class="text-muted">
                                ${delivery.expected_date ? 'Expected: ' + new Date(delivery.expected_date).toLocaleDateString('id-ID') : ''}
                                ${delivery.actual_date ? ' | Received: ' + new Date(delivery.actual_date).toLocaleDateString('id-ID') : ''}
                            </small>
                        </div>
                        <div>
                            ${statusBadge}
                            <span class="badge badge-soft-cyan ms-2">${itemsInDelivery.length} items</span>
                        </div>
                    </div>
                    <div class="card-body">
                        ${delivery.driver_name || delivery.vehicle_info ? `
                            <div class="row mb-3">
                                ${delivery.driver_name ? `
                                    <div class="col-md-6">
                                        <strong>Driver:</strong> ${delivery.driver_name}
                                        ${delivery.driver_phone ? `<br><small class="text-muted">${delivery.driver_phone}</small>` : ''}
                                    </div>
                                ` : ''}
                                ${delivery.vehicle_info ? `
                                    <div class="col-md-6">
                                        <strong>Vehicle:</strong> ${delivery.vehicle_info}
                                        ${delivery.vehicle_plate ? `<br><small class="text-muted">${delivery.vehicle_plate}</small>` : ''}
                                    </div>
                                ` : ''}
                            </div>
                        ` : ''}
                        
                        ${delivery.notes ? `<p class="mb-2"><strong>Notes:</strong> ${delivery.notes}</p>` : ''}
                        
                        ${itemsInDelivery.length > 0 ? `
                            <h6 class="mb-2">Items in Delivery:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Item Name</th>
                                            <th>Serial Number</th>
                                            <th>Verification</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${itemsInDelivery.map(item => `
                                            <tr>
                                                <td><span class="badge ${getItemTypeBadgeClass(item.item_type)}">${item.item_type}</span></td>
                                                <td>${item.item_name || '-'}</td>
                                                <td><code>${item.serial_number || '-'}</code></td>
                                                <td>${getVerificationStatusBadge(item.status_verifikasi || 'Not Checked')}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        ` : '<p class="text-muted mb-0">No items in this delivery</p>'}
                    </div>
                </div>
            `;
        });
    }
    
    $('#deliveriesContent').html(deliveriesHtml);
}

// Initialize PO Detail tabs - prevent affecting parent page tabs
function initializePODetailTabs() {
    // Scope to modal only - use modal container to limit selector scope
    const modalContainer = $('#viewPOModal');
    
    // Remove any existing event handlers to prevent conflicts
    modalContainer.find('#items-tab, #deliveries-tab').off('click.poDetail');
    
    // Handle tab clicks manually to prevent affecting parent tabs
    modalContainer.find('#items-tab').on('click.poDetail', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        // Manually switch tabs within modal only
        modalContainer.find('#items-tab').addClass('active').attr('aria-selected', 'true');
        modalContainer.find('#deliveries-tab').removeClass('active').attr('aria-selected', 'false');
        modalContainer.find('#items-pane').addClass('show active');
        modalContainer.find('#deliveries-pane').removeClass('show active');
    });
    
    modalContainer.find('#deliveries-tab').on('click.poDetail', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        // Manually switch tabs within modal only
        modalContainer.find('#deliveries-tab').addClass('active').attr('aria-selected', 'true');
        modalContainer.find('#items-tab').removeClass('active').attr('aria-selected', 'false');
        modalContainer.find('#deliveries-pane').addClass('show active');
        modalContainer.find('#items-pane').removeClass('show active');
    });
}

// Helper functions for badges and icons
function getStatusBadge(status) {
    const statusMap = {
        'pending': 'bg-warning',
        'completed': 'bg-success',
        'cancelled': 'bg-danger',
        'in_progress': 'bg-info',
        'PENDING': 'bg-warning',
        'COMPLETED': 'bg-success',
        'CANCELLED': 'bg-danger',
        'IN_PROGRESS': 'bg-info'
    };
    const statusText = {
        'pending': 'Pending',
        'completed': 'Completed',
        'cancelled': 'Cancelled',
        'in_progress': 'In Progress',
        'PENDING': 'Pending',
        'COMPLETED': 'Completed',
        'CANCELLED': 'Cancelled',
        'IN_PROGRESS': 'In Progress'
    };
    const badgeClass = statusMap[status] || 'bg-secondary';
    const text = statusText[status] || status;
    return `<span class="badge ${badgeClass}">${text}</span>`;
}

function getDeliveryStatusBadge(status) {
    const statusMap = {
        'Scheduled': 'bg-info',
        'In Transit': 'bg-warning',
        'Received': 'bg-success',
        'Cancelled': 'bg-danger',
        'SCHEDULED': 'bg-info',
        'IN_TRANSIT': 'bg-warning',
        'RECEIVED': 'bg-success',
        'CANCELLED': 'bg-danger'
    };
    const statusText = {
        'Scheduled': 'Scheduled',
        'In Transit': 'In Transit',
        'Received': 'Received',
        'Cancelled': 'Cancelled',
        'SCHEDULED': 'Scheduled',
        'IN_TRANSIT': 'In Transit',
        'RECEIVED': 'Received',
        'CANCELLED': 'Cancelled'
    };
    const badgeClass = statusMap[status] || 'bg-secondary';
    const text = statusText[status] || status;
    return `<span class="badge ${badgeClass}">${text}</span>`;
}

function getVerificationStatusBadge(status) {
    const statusMap = {
        'Sesuai': 'badge-soft-green',
        'Tidak Sesuai': 'badge-soft-red',
        'Belum Dicek': 'badge-soft-yellow',
        'Not Checked': 'badge-soft-yellow'
    };

    const iconMap = {
        'Sesuai': 'fa-check-circle',
        'Tidak Sesuai': 'fa-times-circle',
        'Belum Dicek': 'fa-clock',
        'Not Checked': 'fa-clock'
    };

    const badgeClass = statusMap[status] || 'badge-soft-gray';
    const icon = iconMap[status] || 'fa-question-circle';
    const label = status || 'Unknown';

    return `<span class="badge ${badgeClass}">
        <i class="fas ${icon} me-1"></i>${label}
    </span>`;
}

// Duplicate function removed - using renderPODetailNew instead

function getItemTypeIcon(type) {
    const icons = {
        'Unit': 'fas fa-truck',
        'Attachment': 'fas fa-tools',
        'Battery': 'fas fa-battery-full',
        'Charger': 'fas fa-plug',
        'Sparepart': 'fas fa-cogs'
    };
    return icons[type] || 'fas fa-box';
}

function getItemTypeBadgeClass(type) {
    const classes = {
        'Unit': 'bg-primary',
        'Attachment': 'bg-info', 
        'Battery': 'bg-warning',
        'Charger': 'bg-success',
        'Sparepart': 'bg-secondary'
    };
    return classes[type] || 'bg-secondary';
}

// Modal action functions
function createDeliveryScheduleFromModal(poId) {
    $('#viewPOModal').modal('hide');
    createDeliverySchedule(poId);
}

function trackDeliveriesFromModal(poId) {
    $('#viewPOModal').modal('hide');
    trackDeliveries(poId);
}

function addDeliveryFromModal(poId) {
    $('#viewPOModal').modal('hide');
    addDelivery(poId);
}

function completePOFromModal(poId) {
    $('#viewPOModal').modal('hide');
    completePO(poId);
}

function reverifyPOFromModal(poId) {
    $('#viewPOModal').modal('hide');
    reverifyPO(poId);
}

function cancelPOFromModal(poId) {
    $('#viewPOModal').modal('hide');
    cancelPO(poId);
}

function deletePOFromModal(poId) {
    $('#viewPOModal').modal('hide');
    deletePO(poId);
}

function printPOFromModal() {
    printPO(currentPOId);
}

// Duplicate function removed - using the one at line 1997

function getDeliveryStatusIcon(status) {
    const icons = {
        'Scheduled': 'fa-clock',
        'In Transit': 'fa-truck',
        'Received': 'fa-check-circle',
        'Completed': 'fa-check-double',
        'Cancelled': 'fa-times-circle'
    };
    return icons[status] || 'fa-question-circle';
}

// Delivery action functions
function assignSerialNumbers(deliveryId) {
    currentDeliveryId = deliveryId;
    
    // Load delivery details and items
    loadDeliveryDetailsForSN(deliveryId);
    
    $('#assignSNModal').modal('show');
}

function proceedWithoutSN() {
    if (!currentDeliveryId) return;
    OptimaConfirm.generic({
        title: 'Lanjutkan Tanpa SN?',
        text: 'Delivery akan ditandai sebagai In Transit tanpa serial number.',
        icon: 'warning',
        confirmText: 'Ya, Lanjutkan',
        cancelText: window.lang('cancel'),
        confirmButtonColor: 'warning',
        onConfirm: function() {
            $('#assignSNModal').modal('hide');
            updateDeliveryStatus(currentDeliveryId, 'In Transit');
        }
    });
}

function loadDeliveryDetailsForSN(deliveryId) {
    // Reset modal content
    $('#snAssignmentContent').html(`
        <div class="text-center py-4">
            <i class="fas fa-circle-notch fa-spin text-primary fs-2"></i>
            <p class="mt-2">Loading delivery details...</p>
        </div>
    `);
    
    // Load delivery details
    $.ajax({
        url: `<?= base_url('/purchasing/api/get-delivery-data') ?>`,
        type: 'POST',
        data: { 
            draw: 1, 
            start: 0, 
            length: 1000, // Get more records to find the specific delivery
            search: { value: '' },
            order: [{ column: 0, dir: 'desc' }]
        },
        success: function(response) {
            console.log('Delivery data response:', response);
            if (response.data && response.data.length > 0) {
                const delivery = response.data.find(d => d.id_delivery == deliveryId);
                console.log('Found delivery:', delivery);
                if (delivery) {
                    // Populate delivery info
                    $('#snModalPackingList').text(delivery.packing_list_no || '-');
                    $('#snModalDriver').text(delivery.driver_name || '-');
                    $('#snModalDeliveryDate').text(delivery.delivery_date || '-');
                    $('#snModalVehicle').text(delivery.vehicle_info || '-');
                    
                    // Load items for SN assignment
                    console.log('Delivery po_id:', delivery.po_id, 'delivery_id:', deliveryId);
                    if (delivery.po_id) {
                        loadItemsForSNAssignment(delivery.po_id, deliveryId);
                    } else {
                        showError('PO ID not found in delivery data');
                    }
                } else {
                    showError('Delivery not found');
                }
            } else {
                showError('Failed to load delivery data');
            }
        },
        error: function() {
            showError('An error occurred while loading delivery data');
        }
    });
}

function loadItemsForSNAssignment(poId, deliveryId) {
    $.ajax({
        url: `<?= base_url('/purchasing/api/delivery-items/') ?>${poId}?delivery_id=${deliveryId}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                renderSNAssignmentForm(response.items);
            } else {
                showError(response.message || 'Failed to load items');
            }
        },
        error: function() {
            showError('An error occurred while loading items');
        }
    });
}

function snEscapeAttr(val) {
    if (val == null || val === '') return '';
    return String(val)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;');
}

/**
 * Strip residual "Unknown X" placeholders from stored item_name values.
 * E.g. "awdaw | awdaw | Unknown Jenis | Unknown Departemen | Unknown Kapasitas"
 * becomes "awdaw | awdaw"
 */
function cleanUnitItemName(name) {
    if (!name) return 'Unit';
    const parts = name.split('|').map(p => p.trim()).filter(p => p && !/^Unknown\s/i.test(p));
    return parts.length ? parts.join(' | ') : 'Unit';
}

/** Kumpulkan payload Assign SN dari input bertanda data-sn-delivery-item */
function collectSnAssignmentPayload() {
    const byId = {};
    document.querySelectorAll('[data-sn-delivery-item]').forEach(function(el) {
        const id = el.getAttribute('data-sn-delivery-item');
        const typ = el.getAttribute('data-sn-type') || '';
        const field = el.getAttribute('data-sn-field');
        if (!id || !field) return;
        if (!byId[id]) {
            byId[id] = { id_delivery_item: parseInt(id, 10), type: typ };
        }
        byId[id][field] = el.value;
    });
    const serialNumbers = [];
    Object.keys(byId).forEach(function(k) {
        const row = byId[k];
        const id = row.id_delivery_item;
        const t = row.type;
        if (t === 'unit') {
            const snU = (row.serial_number || '').trim();
            const snE = (row.sn_mesin_po || '').trim();
            const snM = (row.sn_mast_po || '').trim();
            if (snU || snE || snM) {
                serialNumbers.push({
                    id_delivery_item: id,
                    type: 'unit',
                    serial_number: row.serial_number || '',
                    sn_mast_po: row.sn_mast_po || '',
                    sn_mesin_po: row.sn_mesin_po || ''
                });
            }
        } else if (t === 'attachment' || t === 'battery' || t === 'charger') {
            const sn = (row.serial_number || '').trim();
            if (sn) {
                serialNumbers.push({
                    id_delivery_item: id,
                    type: t,
                    serial_number: row.serial_number || ''
                });
            }
        }
    });
    return serialNumbers;
}

function renderSnAccessoryBlock(title, iconClass, line, itemType) {
    if (!line || !line.id_delivery_item) return '';
    const id = line.id_delivery_item;
    const v = snEscapeAttr(line.serial_number || '');
    return `
        <div class="border-top pt-2 mt-2">
            <div class="d-flex align-items-center mb-2">
                <i class="${iconClass} me-2 text-secondary"></i>
                <strong class="small">${title}</strong>
                <span class="ms-auto small text-muted text-truncate" style="max-width:55%" title="${snEscapeAttr(line.item_name || '')}">${line.item_name || ''}</span>
            </div>
            <div class="row g-2">
                <div class="col-md-6 col-lg-4">
                    <label class="form-label small mb-0">SN</label>
                    <input type="text" class="form-control form-control-sm"
                           data-sn-delivery-item="${id}" data-sn-type="${itemType}" data-sn-field="serial_number"
                           placeholder="Serial number" value="${v}">
                </div>
            </div>
        </div>`;
}

function renderSNAssignmentForm(items) {
    let html = '';

    const orphans = items && items.orphans ? items.orphans : {};
    const hasOrphans = (orphans.batteries && orphans.batteries.length) ||
        (orphans.chargers && orphans.chargers.length) ||
        (orphans.attachments && orphans.attachments.length);
    const hasBundles = items && items.unit_bundles && items.unit_bundles.length > 0;
    const hasLegacy = items && (items.units?.length || items.attachments?.length || items.batteries?.length || items.chargers?.length);

    if (!items || (!hasBundles && !hasLegacy && !hasOrphans)) {
        html = `
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                No items available for serial number assignment.
            </div>
        `;
        $('#snAssignmentContent').html(html);
        return;
    }

    html += '<div class="alert alert-light border small mb-3 py-2">';
    html += '<i class="fas fa-info-circle me-1 text-primary"></i> ';
    html += '<strong>Unit + aksesoris berpasangan:</strong> Charger, Baterai, dan Attachment (jika ikut pengiriman) ditampilkan di bawah unit yang dipasangkan. ';
    html += 'Item tambahan yang tidak terpasang ke unit ada di bagian bawah.';
    html += '</div>';

    html += '<div class="row g-3">';

    if (hasBundles) {
        items.unit_bundles.forEach(function(bundle, bundleIndex) {
            const unit = bundle.unit;
            if (!unit || !unit.id_delivery_item) return;
            const uid = unit.id_delivery_item;
            const snMain = snEscapeAttr(unit.serial_number || unit.serial_number_po || '');
            const snEng = snEscapeAttr(unit.sn_mesin_po || '');
            const snMast = snEscapeAttr(unit.sn_mast_po || '');

            html += `
                <div class="col-12">
                    <div class="card border-primary shadow-sm">
                        <div class="card-header bg-primary text-white py-2">
                                <span class="ms-md-auto small text-muted text-truncate" style="max-width:100%">${cleanUnitItemName(unit.item_name)}</span>
                            </div>
                        </div>
                        <div class="card-body py-3">
                            <div class="mb-1"><span class="badge bg-primary">Unit</span></div>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small mb-0">SN Unit</label>
                                    <input type="text" class="form-control form-control-sm"
                                           data-sn-delivery-item="${uid}" data-sn-type="unit" data-sn-field="serial_number"
                                           placeholder="SN Unit" value="${snMain}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small mb-0">SN Engine</label>
                                    <input type="text" class="form-control form-control-sm"
                                           data-sn-delivery-item="${uid}" data-sn-type="unit" data-sn-field="sn_mesin_po"
                                           placeholder="SN Engine" value="${snEng}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small mb-0">SN Mast</label>
                                    <input type="text" class="form-control form-control-sm"
                                           data-sn-delivery-item="${uid}" data-sn-type="unit" data-sn-field="sn_mast_po"
                                           placeholder="SN Mast" value="${snMast}">
                                </div>
                            </div>
                            ${renderSnAccessoryBlock('Charger', 'fas fa-plug', bundle.charger, 'charger')}
                            ${renderSnAccessoryBlock('Baterai', 'fas fa-battery-full', bundle.battery, 'battery')}
                            ${renderSnAccessoryBlock('Attachment', 'fas fa-puzzle-piece', bundle.attachment, 'attachment')}
                        </div>
                    </div>
                </div>`;
        });
    } else {
        if (items.units && items.units.length > 0) {
            items.units.forEach(function(unit, index) {
                const uid = unit.id_delivery_item;
                const snMain = snEscapeAttr(unit.serial_number || unit.serial_number_po || '');
                const snEng = snEscapeAttr(unit.sn_mesin_po || '');
                const snMast = snEscapeAttr(unit.sn_mast_po || '');
                html += `
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white py-2">
                                <span class="ms-auto small text-muted">${cleanUnitItemName(unit.item_name)}</span>
                            </div>
                        </div>
                        <div class="card-body py-2">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">SN Unit:</label>
                                    <input type="text" class="form-control form-control-sm"
                                           data-sn-delivery-item="${uid}" data-sn-type="unit" data-sn-field="serial_number"
                                           name="serial_number_po_${index}"
                                           placeholder="Enter SN Unit" value="${snMain}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">SN Engine:</label>
                                    <input type="text" class="form-control form-control-sm"
                                           data-sn-delivery-item="${uid}" data-sn-type="unit" data-sn-field="sn_mesin_po"
                                           name="sn_engine_${index}"
                                           placeholder="Enter SN Engine" value="${snEng}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">SN Mast:</label>
                                    <input type="text" class="form-control form-control-sm"
                                           data-sn-delivery-item="${uid}" data-sn-type="unit" data-sn-field="sn_mast_po"
                                           name="sn_mast_${index}"
                                           placeholder="Enter SN Mast" value="${snMast}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            });
        }

        if (items.attachments && items.attachments.length > 0) {
            items.attachments.forEach(function(attachment, index) {
                const aid = attachment.id_delivery_item;
                const v = snEscapeAttr(attachment.serial_number || '');
                html += `
                <div class="col-12">
                    <div class="card border-info">
                        <div class="card-header bg-info text-dark py-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-puzzle-piece me-2"></i>
                                <strong>Attachment Item #${index + 1}</strong>
                                <span class="ms-auto small text-muted">${attachment.item_name || 'Attachment Item'}</span>
                            </div>
                        </div>
                        <div class="card-body py-2">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Serial Number:</label>
                                    <input type="text" class="form-control form-control-sm"
                                           data-sn-delivery-item="${aid}" data-sn-type="attachment" data-sn-field="serial_number"
                                           name="sn_attachment_${index}"
                                           placeholder="Enter Serial Number" value="${v}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            });
        }

        if (items.batteries && items.batteries.length > 0) {
            items.batteries.forEach(function(battery, index) {
                const bid = battery.id_delivery_item;
                const v = snEscapeAttr(battery.serial_number || '');
                html += `
                <div class="col-12">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark py-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-battery-full me-2"></i>
                                <strong>Battery Item #${index + 1}</strong>
                                <span class="ms-auto small text-muted">${battery.item_name || 'Battery Item'}</span>
                            </div>
                        </div>
                        <div class="card-body py-2">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Serial Number:</label>
                                    <input type="text" class="form-control form-control-sm"
                                           data-sn-delivery-item="${bid}" data-sn-type="battery" data-sn-field="serial_number"
                                           name="sn_battery_${index}"
                                           placeholder="Enter Serial Number" value="${v}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            });
        }

        if (items.chargers && items.chargers.length > 0) {
            items.chargers.forEach(function(charger, index) {
                const cid = charger.id_delivery_item;
                const v = snEscapeAttr(charger.serial_number || '');
                html += `
                <div class="col-12">
                    <div class="card border-success">
                        <div class="card-header bg-success text-dark py-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-plug me-2"></i>
                                <strong>Charger Item #${index + 1}</strong>
                                <span class="ms-auto small text-muted">${charger.item_name || 'Charger Item'}</span>
                            </div>
                        </div>
                        <div class="card-body py-2">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Serial Number:</label>
                                    <input type="text" class="form-control form-control-sm"
                                           data-sn-delivery-item="${cid}" data-sn-type="charger" data-sn-field="serial_number"
                                           name="sn_charger_${index}"
                                           placeholder="Enter Serial Number" value="${v}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            });
        }
    }

    if (hasOrphans) {
        html += '<div class="col-12 mt-2"><h6 class="text-secondary border-bottom pb-2"><i class="fas fa-box-open me-2"></i>Item tambahan (tidak terpasang ke unit di pengiriman ini)</h6></div>';
        const renderOrphanList = function(rows, label, borderClass, headerBg, icon, typ) {
            if (!rows || !rows.length) return;
            rows.forEach(function(row, idx) {
                const rid = row.id_delivery_item;
                const v = snEscapeAttr(row.serial_number || '');
                html += `
                <div class="col-12">
                    <div class="card ${borderClass}">
                        <div class="card-header ${headerBg} py-2">
                            <div class="d-flex align-items-center">
                                <i class="${icon} me-2"></i>
                                <strong>${label} #${idx + 1}</strong>
                                <span class="ms-auto small text-muted">${row.item_name || label}</span>
                            </div>
                        </div>
                        <div class="card-body py-2">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Serial Number:</label>
                                    <input type="text" class="form-control form-control-sm"
                                           data-sn-delivery-item="${rid}" data-sn-type="${typ}" data-sn-field="serial_number"
                                           placeholder="SN" value="${v}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            });
        };
        renderOrphanList(orphans.chargers, 'Charger', 'border-success', 'bg-success text-dark', 'fas fa-plug', 'charger');
        renderOrphanList(orphans.batteries, 'Baterai', 'border-warning', 'bg-warning text-dark', 'fas fa-battery-full', 'battery');
        renderOrphanList(orphans.attachments, 'Attachment', 'border-info', 'bg-info text-dark', 'fas fa-puzzle-piece', 'attachment');
    }

    html += '</div>';

    $('#snAssignmentContent').html(html);
}

function showError(message) {
    $('#snAssignmentContent').html(`
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            ${message}
        </div>
    `);
}

function markAsInTransit(deliveryId) {
    OptimaConfirm.generic({
        title: 'Mark as In Transit?',
        text: 'Delivery akan ditandai sebagai In Transit.',
        icon: 'question',
        confirmText: 'Ya, Tandai!',
        cancelText: window.lang('cancel'),
        confirmButtonColor: 'primary',
        onConfirm: function() { updateDeliveryStatus(deliveryId, 'In Transit'); }
    });
}

function markAsReceived(deliveryId) {
    OptimaConfirm.approve({
        title: 'Mark as Received?',
        text: 'Delivery akan ditandai sebagai Received.',
        confirmText: 'Ya, Received!',
        cancelText: window.lang('cancel'),
        onConfirm: function() { updateDeliveryStatus(deliveryId, 'Received'); }
    });
}


function updateDeliveryStatus(deliveryId, status) {
    $.ajax({
        url: '<?= base_url('/purchasing/api/update-delivery-status') ?>',
        type: 'POST',
        data: {
            [window.csrfTokenName]: window.getCsrfToken(),
            delivery_id: deliveryId,
            status: status
        },
        success: function(response) {
            if (response.success) {
                refreshTable();
                if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                    OptimaPro.showNotification(response.message || 'Delivery status updated successfully', 'success');
                } else {
                    showNotification(response.message || 'Delivery status updated successfully', 'success');
                }
            } else {
                if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                    OptimaPro.showNotification(response.message || 'Failed to update delivery status', 'error');
                } else {
                    showNotification(response.message || 'Failed to update delivery status', 'error');
                }
            }
        },
        error: function() {
            if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                OptimaPro.showNotification('An error occurred while updating delivery status', 'error');
            } else {
                showNotification('An error occurred while updating delivery status', 'error');
            }
        }
    });
}

function viewDeliveryDetail(deliveryId, event) {
    if (event) event.preventDefault();
    // Implementation for delivery detail modal
    console.log('View delivery detail:', deliveryId);
}

// Function removed - see the comprehensive version below

function getDeliveryStatusBadgeClass2(status) {
    const classes = {
        'Scheduled': 'bg-secondary',
        'In Transit': 'bg-warning',
        'Received': 'bg-success',
        'Partial': 'bg-info',
        'Cancelled': 'bg-danger'
    };
    return classes[status] || 'bg-secondary';
}

function addNewDelivery(poId) {
    OptimaNotify.info('Add new delivery feature coming soon!');
}

// Duplicate function removed

    function printPackingList(deliveryId, packingListNo, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        const url = '<?= base_url('purchasing/print-packing-list') ?>?delivery_id=' + deliveryId + '&packing_list=' + encodeURIComponent(packingListNo);
        const printWindow = window.open('', '_blank');
        if (printWindow) {
            printWindow.opener = null;
            printWindow.location.href = url;
            printWindow.focus();
        } else if (window.OptimaNotify && typeof OptimaNotify.warning === 'function') {
            OptimaNotify.warning('Popup diblokir browser. Izinkan popup untuk membuka hasil print di tab baru.');
        }
    }
    
    function printPO(poId, event) {
    if (event) event.preventDefault();
    window.open('<?= base_url('/purchasing/print-po/') ?>' + poId, '_blank');
}

function deletePO(poId, event) {
    if (event) event.preventDefault();
    
    // Check permission for deleting PO
    <?php if (!can_manage('purchasing')): ?>
    OptimaNotify.error('You do not have permission to delete Purchase Orders');
    return;
    <?php endif; ?>
    
    OptimaConfirm.danger({
        title: 'Are you sure?',
        text: 'This PO data will be permanently deleted!',
        confirmText: 'Yes, delete it!',
        cancelText: window.lang('cancel'),
        onConfirm: function() {
            $.ajax({
                type: 'DELETE',
                url: '<?= base_url('/purchasing/delete-po/') ?>' + poId,
                data: {[window.csrfTokenName]: window.getCsrfToken()},
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        OptimaNotify.success('PO has been deleted successfully.');
                        reloadPurchasingTables();
                    } else {
                        OptimaNotify.error(response.message || 'An error occurred.');
                    }
                },
                error: function() {
                    OptimaNotify.error('Unable to connect to the server.');
                }
            });
        }
    });
}

function reverifyPO(poId) {
    OptimaConfirm.generic({
        title: 'Reverify PO?',
        text: 'This PO will be returned to the verification queue. Are you sure you want to continue?',
        icon: 'question',
        confirmText: 'Yes, continue!',
        cancelText: window.lang('cancel'),
        confirmButtonColor: 'primary',
        onConfirm: function() {
            $.ajax({
                type: 'POST',
                url: '<?= base_url('/purchasing/reverify-po/') ?>' + poId,
                data: { [window.csrfTokenName]: window.getCsrfToken() },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        OptimaNotify.success('PO has been returned to the verification queue.');
                        reloadPurchasingTables();
                    } else {
                        OptimaNotify.error(response.message || 'An error occurred.');
                    }
                },
                error: function() {
                    OptimaNotify.error('Unable to connect to the server.');
                }
            });
        }
    });
}

function cancelPO(poId) {
    OptimaConfirm.danger({
        title: 'Complete and Cancel PO?',
        text: 'The status of this PO will be changed to "Cancelled". This action cannot be undone.',
        confirmText: 'Yes, cancel the PO!',
        cancelText: window.lang('cancel'),
        onConfirm: function() {
            $.ajax({
                type: 'POST',
                url: '<?= base_url('/purchasing/cancel-po/') ?>' + poId,
                data: { [window.csrfTokenName]: window.getCsrfToken() },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        OptimaNotify.success('PO has been cancelled successfully.');
                        reloadPurchasingTables();
                    } else {
                        OptimaNotify.error(response.message || 'An error occurred.');
                    }
                },
                error: function() {
                    OptimaNotify.error('Unable to connect to the server.');
                }
            });
        }
    });
}

// Removed duplicate refreshTable function - using the one above

function exportData() {
    OptimaNotify.info('Export feature coming soon!');
}

// Wrapper functions for modal actions
// Removed duplicate function - using the one above

// Duplicate functions removed - using the ones at lines 2543-2573

// Action functions from table
function createDeliverySchedule(poId, event) {
    if (event) event.stopPropagation();
    createDelivery(poId);
}

function createDelivery(poId, event) {
    if (event) event.stopPropagation();
    currentPOId = poId;
    
    // Reset form
    $('#createDeliveryForm')[0].reset();
    
    // Set today's date as default
    const today = new Date().toISOString().split('T')[0];
    $('input[name="delivery_date"]').val(today);
    
    // Auto-generate packing list number (will be replaced by backend)
    // $('input[name="packing_list_no"]').val('Auto-generated').prop('readonly', true);
    
    // Load PO items
    loadDeliveryItems(poId);
    
    $('#createDeliveryModal').modal('show');
}

function loadDeliveryItems(poId) {
    $('#deliveryItemsList').html(`
        <div class="text-center py-3">
            <i class="fas fa-circle-notch fa-spin text-primary"></i>
            <p class="mt-2 mb-0 text-muted small">Loading items...</p>
        </div>
    `);
    
    $.ajax({
        url: `<?= base_url('/purchasing/api/delivery-items/') ?>${poId}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                renderDeliveryItems(response.po, response.items);
            } else {
                $('#deliveryItemsList').html(`
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ${response.message || 'Failed to load items'}
                    </div>
                `);
            }
        },
        error: function() {
            $('#deliveryItemsList').html(`
                <div class="alert alert-danger mb-0">
                    <i class="fas fa-times-circle me-2"></i>
                    An error occurred while loading items
                </div>
            `);
        }
    });
}

function renderDeliveryItems(po, items) {
    let html = '';
    
    // PO Info
    html += `
        <div class="mb-3 p-3 bg-light rounded">
            <div class="row">
                <div class="col-6">
                    <strong>PO Number:</strong><br>
                    <span class="text-primary">${po.nomor_po || po.no_po}</span>
                </div>
                <div class="col-6">
                    <strong>Supplier:</strong><br>
                    <span class="text-primary">${po.nama_supplier}</span>
                </div>
            </div>
        </div>
    `;
    
    // SPK-style Checklist Section with SEPARATED categories
    html += `
                <div class="mb-3">
            <strong>Select Items to be Delivered</strong>
            <div class="form-text mb-3">Untuk <strong>unit per baris konfigurasi / PI</strong>, isi kolom <strong>Qty kirim ini</strong> (lebih dari 0). Untuk attachment, baterai, atau charger terpisah, centang item di daftar.</div>
                </div>
    `;
    
    // Units: grup konfigurasi (satu baris PI) dengan qty parsial
    if (items.unit_groups && items.unit_groups.length > 0) {
        html += `
            <div class="mb-3">
                <h6 class="mb-2"><i class="fas fa-layer-group me-2 text-primary"></i>Unit per baris konfigurasi / PI</h6>
                <div class="form-text mb-2">Isi <strong>Qty kirim</strong> untuk pengiriman ini (0 = tidak ikut). Maks = sisa belum terkirim.</div>
                <div class="table-responsive border rounded">
                <table class="table table-sm mb-0 align-middle">
                    <thead class="table-light"><tr><th>Konfigurasi</th><th class="text-center" style="width:7rem">Pesan</th><th class="text-center" style="width:7rem">Sudah kirim</th><th class="text-center" style="width:8rem">Sisa</th><th class="text-center" style="width:9rem">Qty kirim ini</th></tr></thead>
                    <tbody>`;
        items.unit_groups.forEach((g, index) => {
            const undel = Array.isArray(g.undelivered_unit_ids) ? g.undelivered_unit_ids : [];
            const rem = typeof g.qty_remaining === 'number' ? g.qty_remaining : undel.length;
            const disabledRow = rem <= 0 ? 'table-secondary' : '';
            html += `<tr class="${disabledRow}">
                <td><small>${index + 1}. ${g.label || g.group_key}</small></td>
                <td class="text-center">${g.qty_ordered}</td>
                <td class="text-center">${g.qty_delivered}</td>
                <td class="text-center">${rem}</td>
                <td class="text-center">
                    <input type="number" class="form-control form-control-sm delivery-group-qty text-center" min="0" max="${rem}" value="0"
                        data-group-key="${g.group_key}"
                        data-name="${(g.label || '').replace(/"/g, '&quot;')}"
                        data-undelivered='${JSON.stringify(undel)}'
                        ${rem <= 0 ? 'disabled' : ''}>
                </td>
            </tr>`;
        });
        html += `</tbody></table></div></div>`;
    } else if (items.units && items.units.length > 0) {
        html += `
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0"><i class="fas fa-truck me-2 text-primary"></i>Unit (${items.units.length} items)</h6>
                    <div class="btn-group btn-group-sm">
                        <?php if ($can_create): ?>
                        <button type="button" class="btn btn-outline-secondary" onclick="selectAllUnits()">Select All</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="clearAllUnits()">Clear</button>
                        <?php else: ?>
                        <button type="button" class="btn btn-outline-secondary" disabled title="Access denied">Select All</button>
                        <button type="button" class="btn btn-outline-secondary" disabled title="Access denied">Clear</button>
                        <?php endif; ?>
            </div>
                </div>
                <div class="border rounded p-2" style="max-height:200px; overflow:auto">
        `;
        
        items.units.forEach((unit, index) => {
            const isDisabled = unit.is_delivered;
            const disabledClass = isDisabled ? 'text-muted' : '';
            const disabledAttr = isDisabled ? 'disabled' : '';
            const deliveredBadge = isDisabled ? '<span class="badge badge-soft-green ms-2">Already Delivered</span>' : '';
            
            console.log('Unit ' + unit.id_po_unit + ' is_delivered: ' + isDisabled);
            
            html += `<div class="mb-2 p-2 border rounded ${disabledClass}">
            <div class="form-check">
                <input class="form-check-input delivery-item-checkbox" type="checkbox" 
                       data-type="unit" data-id="${unit.id_po_unit}" 
                       data-name="${unit.item_name}" id="delivery_unit_${index}" 
                       onchange="updateDeliverySelection()" ${disabledAttr}>
                 <label class="form-check-label" for="delivery_unit_${index}" style="width: 95%;">
                     <strong>${index + 1}. ${unit.item_name}</strong>
                     ${deliveredBadge}
                 </label>
            </div>
        </div>`;
        });
        
        html += `</div></div>`;
    }
    
    // Attachments Section
    if (items.attachments && items.attachments.length > 0) {
        html += `
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0"><i class="fas fa-paperclip me-2 text-success"></i>Attachment (${items.attachments.length} items)</h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" onclick="selectAllAttachments()">Select All</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="clearAllAttachments()">Clear</button>
                    </div>
                </div>
                <div class="border rounded p-2" style="max-height:200px; overflow:auto">
        `;
        
        items.attachments.forEach((attachment, index) => {
            const isDisabled = attachment.is_delivered;
            const disabledClass = isDisabled ? 'text-muted' : '';
            const disabledAttr = isDisabled ? 'disabled' : '';
            const deliveredBadge = isDisabled ? '<span class="badge badge-soft-green ms-2">Already Delivered</span>' : '';
            
            // Debug logging
            console.log('Attachment ' + attachment.id_po_attachment + ' is_delivered: ' + isDisabled);
            
            html += `<div class="mb-2 p-2 border rounded ${disabledClass}">
                <div class="form-check">
                    <input class="form-check-input delivery-item-checkbox" type="checkbox" 
                           data-type="attachment" data-id="${attachment.id_po_attachment}" 
                           data-name="${attachment.item_name}" id="delivery_attachment_${index}" 
                           onchange="updateDeliverySelection()" ${disabledAttr}>
                    <label class="form-check-label" for="delivery_attachment_${index}">
                        <strong>${index + 1}. ${attachment.item_name}</strong>
                        ${deliveredBadge}
                    </label>
                </div>
            </div>`;
        });
        
        html += `</div></div>`;
    }
    
    // Batteries Section
    if (items.batteries && items.batteries.length > 0) {
        html += `
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0"><i class="fas fa-battery-half me-2 text-warning"></i>Battery (${items.batteries.length} items)</h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" onclick="selectAllBatteries()">Select All</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="clearAllBatteries()">Clear</button>
                    </div>
                </div>
                <div class="border rounded p-2" style="max-height:200px; overflow:auto">
        `;
        
        items.batteries.forEach((battery, index) => {
            const isDisabled = battery.is_delivered;
            const disabledClass = isDisabled ? 'text-muted' : '';
            const disabledAttr = isDisabled ? 'disabled' : '';
            const deliveredBadge = isDisabled ? '<span class="badge badge-soft-green ms-2">Already Delivered</span>' : '';
            
            html += `<div class="mb-2 p-2 border rounded ${disabledClass}">
                <div class="form-check">
                    <input class="form-check-input delivery-item-checkbox" type="checkbox"
                           data-type="battery" data-id="${battery.id_po_attachment}" 
                           data-name="${battery.item_name}" id="delivery_battery_${index}" 
                           onchange="updateDeliverySelection()" ${disabledAttr}>
                    <label class="form-check-label" for="delivery_battery_${index}">
                        <strong>${index + 1}. ${battery.item_name}</strong>
                        ${deliveredBadge}
                    </label>
                </div>
            </div>`;
        });
        
        html += `</div></div>`;
    }
    
    // Chargers Section
    if (items.chargers && items.chargers.length > 0) {
        html += `
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0"><i class="fas fa-plug me-2 text-info"></i>Charger (${items.chargers.length} items)</h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" onclick="selectAllChargers()">Select All</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="clearAllChargers()">Clear</button>
                    </div>
                </div>
                <div class="border rounded p-2" style="max-height:200px; overflow:auto">
        `;
        
        items.chargers.forEach((charger, index) => {
            const isDisabled = charger.is_delivered;
            const disabledClass = isDisabled ? 'text-muted' : '';
            const disabledAttr = isDisabled ? 'disabled' : '';
            const deliveredBadge = isDisabled ? '<span class="badge badge-soft-green ms-2">Already Delivered</span>' : '';
            
            html += `<div class="mb-2 p-2 border rounded ${disabledClass}">
                <div class="form-check">
                    <input class="form-check-input delivery-item-checkbox" type="checkbox"
                           data-type="charger" data-id="${charger.id_po_attachment}" 
                           data-name="${charger.item_name}" id="delivery_charger_${index}" 
                           onchange="updateDeliverySelection()" ${disabledAttr}>
                    <label class="form-check-label" for="delivery_charger_${index}">
                        <strong>${index + 1}. ${charger.item_name}</strong>
                        ${deliveredBadge}
                    </label>
                </div>
            </div>`;
        });
        
        html += `</div></div>`;
    }
    
    // Summary
    html += `
        <div class="mt-3 p-3 bg-light rounded">
            <div class="row">
                <div class="col-6">
                    <strong>Total baris terpilih:</strong>
                    <span id="deliveryTotalSelected" class="badge badge-soft-blue ms-2">0</span>
                </div>
                <div class="col-6 text-end">
                    <small class="text-muted">Minimal satu baris: qty kirim &gt; 0 (unit grup) atau centang item</small>
                </div>
            </div>
        </div>
    `;
    
    $('#deliveryItemsList').html(html);
    updateDeliverySelection();
}

// Helper functions for partial delivery
function updateDeliveryQuantity(checkbox) {
    const qtyInput = checkbox.closest('tr').querySelector('.delivery-qty-input');
    
    if (checkbox.checked) {
        qtyInput.disabled = false;
        qtyInput.value = qtyInput.getAttribute('data-max');
        qtyInput.focus();
    } else {
        qtyInput.disabled = true;
        qtyInput.value = 0;
    }
    
    updateDeliveryTotal();
}

function updateDeliveryTotal() {
    let total = 0;
    document.querySelectorAll('.delivery-qty-input:not([disabled])').forEach(input => {
        const value = parseInt(input.value) || 0;
        total += value;
    });
    
    document.getElementById('deliveryTotalCount').textContent = total;
}

// Notification helper function
function showNotification(message, type = 'info') {
    const method = type === 'error' ? 'error' : type === 'warning' ? 'warning' : type === 'success' ? 'success' : 'info';
    OptimaNotify[method](message);
}

function trackDeliveries(poId, event) {
    if (event) event.stopPropagation();
    viewPODetail(poId, event);
}


function completePO(poId, event) {
    if (event) event.stopPropagation();
    
    OptimaConfirm.approve({
        title: 'Complete PO?',
        text: 'Delivery sudah selesai. PO hanya akan di-complete jika semua item juga sudah selesai diverifikasi. Lanjutkan?',
        confirmText: 'Yes, Complete!',
        cancelText: window.lang('cancel'),
        onConfirm: function() {
            $.ajax({
                type: 'POST',
                url: '<?= base_url('/purchasing/complete-po/') ?>' + poId,
                data: {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        OptimaNotify.success('PO has been marked as completed.');
                        reloadPurchasingTables();
                        $('#viewPOModal').modal('hide');
                    } else {
                        OptimaNotify.error(response.message || 'An error occurred.');
                    }
                },
                error: function() {
                    OptimaNotify.error('Unable to connect to the server.');
                }
            });
        }
    });
}

// ========================================
// CREATE PO MODAL FUNCTIONS
// ========================================
let poItems = [];
let currentItemType = '';
let editIndex = -1;

// Initialize Create PO Modal
function initCreatePOModal() {
    console.log(' Initializing Create PO Modal...');
    
    // Check if Bootstrap is loaded
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap is not loaded!');
        return;
    }
    
    // Button handler to open modal
    $('#btnBuatPO').off('click').on('click', function() {
        <?php if (!$can_create): ?>
        OptimaNotify.error('You do not have permission to create Purchase Orders');
        return false;
        <?php endif; ?>
        
        console.log('📝 Opening Create PO Modal...');
        $('#createPoModal').modal('show');
    });
    
    // Initialize Select2 for supplier dropdown in main modal
    $('#createPoModal').on('shown.bs.modal', function() {
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2-modal').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#createPoModal'),
                width: '100%'
            });
            
            // Enhanced Select2 for supplier dropdown with search
            $('.select2-supplier-modal').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#createPoModal'),
                width: '100%',
                placeholder: 'Cari supplier...',
                allowClear: false,
                minimumInputLength: 0
            });
        }
    });
    
    // Save item button
    $('#saveItemBtn').off('click').on('click', function() {
        const itemData = collectItemData();
        
        if (!itemData) {
            OptimaNotify.warning('Please complete all required fields!');
            return;
        }
        
        if (editIndex >= 0) {
            poItems[editIndex] = itemData;
        } else {
            poItems.push(itemData);
        }
        
        updateItemsTable();
        document.getElementById('items_json').value = JSON.stringify(poItems);
        
        // Hide modal using jQuery (more reliable)
        $('#itemDetailModal').modal('hide');
        
        if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
            OptimaPro.showNotification('Item successfully added!', 'success');
        }
    });
    
    // Form submission validation and AJAX handling
    $('#unifiedPOForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
        
        if (poItems.length === 0) {
            OptimaNotify.warning('Please add at least one item to the PO!');
            return false;
        }
        
        // Update hidden field
        document.getElementById('items_json').value = JSON.stringify(poItems);
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...');
        
        // Submit via AJAX
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    OptimaNotify.success(response.message);
                    
                    // Close modal and refresh table
                    $('#createPoModal').modal('hide');
                    if (typeof refreshTable === 'function') {
                        refreshTable();
                    }
                } else {
                    OptimaNotify.error(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                let errorMessage = 'An error occurred while saving the PO';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                OptimaNotify.error(errorMessage);
            },
            complete: function() {
                // Restore button state
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Initialize cascading dropdowns
    initializeUnitDropdowns();
    initializeAttachmentDropdowns();
    initializeBatteryDropdowns();
    initializeChargerDropdowns();
    
    // Reset form when modal is hidden
    $('#createPoModal').on('hidden.bs.modal', function() {
        $('#unifiedPOForm')[0].reset();
        poItems = [];
        updateItemsTable();
        document.getElementById('items_json').value = '[]';
    });
}

// Open modal and load form based on item type
function openItemModal(itemType, index = -1) {
    currentItemType = itemType;
    editIndex = index;
    if (itemType === 'unit' && index < 0 && typeof crypto !== 'undefined' && crypto.randomUUID) {
        window._currentPoLineGroupId = crypto.randomUUID();
    } else if (itemType === 'unit' && index >= 0 && poItems[index] && poItems[index].po_line_group_id) {
        window._currentPoLineGroupId = poItems[index].po_line_group_id;
    }
    
    const modalBody = document.getElementById('itemModalBody');
    const modalTitle = document.getElementById('itemModalTitle');
    
    // Set title
    const titles = {
        'unit': 'Add Forklift Unit',
        'attachment': 'Add Attachment',
        'battery': 'Add Battery',
        'charger': 'Add Charger'
    };
    modalTitle.textContent = editIndex >= 0 ? titles[itemType].replace('Add', 'Edit') : titles[itemType];
    
    // Load form
    modalBody.innerHTML = '<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-3">Loading form...</p></div>';
    
    // Show modal using jQuery (more reliable)
    $('#itemDetailModal').modal('show');
    
    fetch(`<?= base_url('/purchasing/api/get-item-form/') ?>${itemType}`)
        .then(response => response.text())
        .then(html => {
            modalBody.innerHTML = html;
            
            // Initialize Select2 after content loaded
            setTimeout(() => {
                if (typeof $.fn.select2 !== 'undefined') {
                    $('#itemDetailModal .select2-basic').each(function() {
                        reinitializeModalSelect2($(this));
                    });
                }
                
                // Ensure disabled state for cascading dropdowns
                if (currentItemType === 'attachment') {
                    $('#att_merk').prop('disabled', true).trigger('change.select2');
                    $('#att_model').prop('disabled', true).trigger('change.select2');
                } else if (currentItemType === 'battery') {
                    $('#battery_merk').prop('disabled', true).trigger('change.select2');
                    $('#battery_tipe').prop('disabled', true).trigger('change.select2');
                } else if (currentItemType === 'charger') {
                    $('#charger_model').prop('disabled', true).trigger('change.select2');
                }
                
                // If editing, populate form
                if (editIndex >= 0) {
                    populateFormForEdit(poItems[editIndex]);
                }
                
                console.log('✅ Unit form loaded and Select2 initialized');
            }, 150);
        })
        .catch(error => {
            modalBody.innerHTML = '<div class="alert alert-danger">Error loading form. Please try again.</div>';
            console.error('Error:', error);
        });
}

function populateFormForEdit(item) {
    if (!item || item.item_type !== 'unit') return;
    $('#unit_merk_text').val(item.merk_unit || '');
    $('#unit_model_text').val(item.vendor_model_code || item._display?.model_text || '');
    if (item.tahun_unit) $('#unit_tahun').val(item.tahun_unit);
    $('#unit_qty').val(item.qty || 1);
    if ($('#unit_vendor_spec_text').length) $('#unit_vendor_spec_text').val(item.vendor_spec_text || '');
    if ($('#unit_keterangan').length) $('#unit_keterangan').val(item.keterangan || '');
    $('input[name="pkg_flags[]"]').prop('checked', false);
    if (Array.isArray(item.package_flags)) {
        item.package_flags.forEach(v => { $(`input[name="pkg_flags[]"][value="${v}"]`).prop('checked', true); });
    }
    if ($('#unit_baterai_id').length && item.baterai_id) $('#unit_baterai_id').val(String(item.baterai_id)).trigger('change');
    if ($('#unit_charger_id').length && item.charger_id) $('#unit_charger_id').val(String(item.charger_id)).trigger('change');
    if ($('#unit_attachment_id').length && item.attachment_id) $('#unit_attachment_id').val(String(item.attachment_id)).trigger('change');
    if (item.mast_id) $('#unit_mast').val(String(item.mast_id)).trigger('change');
    if (item.mesin_id) $('#unit_mesin').val(String(item.mesin_id)).trigger('change');
    if (item.ban_id) $('#unit_ban').val(String(item.ban_id)).trigger('change');
    if (item.roda_id) $('#unit_roda').val(String(item.roda_id)).trigger('change');
    if (item.valve_id) $('#unit_valve').val(String(item.valve_id)).trigger('change');
}

// Collect data from modal form based on item type
function collectItemData() {
    const data = {
        item_type: currentItemType
    };
    
    if (currentItemType === 'unit') {
        data.merk_unit        = ($('#unit_merk_text').val() || '').trim();
        data.vendor_model_code = ($('#unit_model_text').val() || '').trim();
        data.tahun_unit       = $('#unit_tahun').val();
        data.mast_id          = $('#unit_mast').val();
        data.mesin_id         = $('#unit_mesin').val();
        data.ban_id           = $('#unit_ban').val();
        data.roda_id          = $('#unit_roda').val();
        data.valve_id         = $('#unit_valve').val();
        data.qty              = $('#unit_qty').val();
        data.keterangan       = $('#unit_keterangan').val();
        data.vendor_spec_text = ($('#unit_vendor_spec_text').val() || '').trim();
        data.po_line_group_id = window._currentPoLineGroupId || '';
        data.kondisi_penjualan = 'Baru';
        if (typeof editIndex !== 'undefined' && editIndex >= 0 && poItems[editIndex] && poItems[editIndex].item_type === 'unit' && poItems[editIndex].unit_accessories) {
            data.unit_accessories = poItems[editIndex].unit_accessories;
        } else {
            data.unit_accessories = '';
        }
        const pkg = [];
        $('input[name="pkg_flags[]"]:checked').each(function () { pkg.push($(this).val()); });
        data.package_flags = pkg;
        if ($('#unit_baterai_id').length) data.baterai_id = $('#unit_baterai_id').val() || '';
        if ($('#unit_charger_id').length) data.charger_id = $('#unit_charger_id').val() || '';
        if ($('#unit_attachment_id').length) data.attachment_id = $('#unit_attachment_id').val() || '';

        data._display = {
            merk_text:  data.merk_unit,
            model_text: data.vendor_model_code
        };

        // Validation
        if (!data.merk_unit || !data.vendor_model_code || !data.qty) {
            OptimaNotify.warning('Brand, Model, dan Quantity wajib diisi', 'Perhatian');
            return null;
        }
        
    } else if (currentItemType === 'attachment') {
        data.tipe_attachment = $('#att_tipe').val();
        data.merk_attachment = $('#att_merk').val();
        data.attachment_id = $('#att_model').val(); // This is the final ID from database
        data.serial_number = $('#att_serial_number').val();
        data.qty = $('#att_qty').val();
        data.keterangan = $('#att_keterangan').val();
        
        data._display = {
            tipe_text: $('#att_tipe option:selected').text() || 'Unknown Type',
            merk_text: $('#att_merk option:selected').text() || 'Unknown Brand',
            model_text: $('#att_model option:selected').text() || 'Unknown Model'
        };
        
        console.log('Attachment data collected:', data);
        
        if (!data.attachment_id || !data.qty) {
            console.warn('Attachment validation failed - missing attachment_id or qty');
            return null;
        }
        
    } else if (currentItemType === 'battery') {
        data.jenis_baterai = $('#battery_jenis').val();
        data.merk_baterai = $('#battery_merk').val();
        data.baterai_id = $('#battery_tipe').val(); // This is the final selection (id from database)
        data.serial_number = $('#battery_serial_number').val();
        data.qty = $('#battery_qty').val();
        data.keterangan = $('#battery_keterangan').val();
        
        data._display = {
            jenis_text: $('#battery_jenis option:selected').text(),
            merk_text: $('#battery_merk option:selected').text(),
            tipe_text: $('#battery_tipe option:selected').text(),
            battery_text: $('#battery_merk option:selected').text() + ' - ' + $('#battery_tipe option:selected').text()
        };
        
        if (!data.baterai_id || !data.qty) {
            console.warn('Battery validation failed - missing baterai_id or qty');
            return null;
        }
        
    } else if (currentItemType === 'charger') {
        data.merk_charger = $('#charger_merk').val();
        data.charger_id = $('#charger_model').val(); // This is the final ID from database
        data.serial_number = $('#charger_serial_number').val();
        data.qty = $('#charger_qty').val();
        data.keterangan = $('#charger_keterangan').val();
        
        data._display = {
            merk_text: $('#charger_merk option:selected').text() || 'Unknown Brand',
            model_text: $('#charger_model option:selected').text() || 'Unknown Model',
            charger_text: $('#charger_merk option:selected').text() + ' - ' + $('#charger_model option:selected').text()
        };
        
        console.log('Charger data collected:', data);
        
        if (!data.charger_id || !data.qty) {
            console.warn('Charger validation failed - missing charger_id or qty');
            return null;
        }
    }
    
    return data;
}

// Update items table display
function updateItemsTable() {
    const tbody = document.getElementById('itemsTableBody');
    
    if (poItems.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No items yet. Click the "Add" button to add items.</td></tr>';
        return;
    }
    
    let html = '';
    poItems.forEach((item, index) => {
        const badgeSoftClasses = {
            'unit': 'badge-soft-blue',
            'attachment': 'badge-soft-green',
            'battery': 'badge-soft-cyan',
            'charger': 'badge-soft-yellow'
        };
        
        const typeLabels = {
            'unit': 'Unit',
            'attachment': 'Attachment',
            'battery': 'Battery',
            'charger': 'Charger'
        };
        
        let description = '';
        if (item.item_type === 'unit') {
            description = `${item._display.merk_text} ${item._display.model_text} | Tahun ${item.tahun_unit || '-'}`;
            if (item.vendor_spec_text) {
                const specShort = item.vendor_spec_text.length > 180 ? item.vendor_spec_text.substring(0, 180) + '…' : item.vendor_spec_text;
                description += `<br><small class="text-muted">${specShort.replace(/</g, '&lt;')}</small>`;
            }
        } else if (item.item_type === 'attachment') {
            description = `${item._display.tipe_text} | ${item._display.merk_text} - ${item._display.model_text}`;
            if (item.serial_number) description += ` | SN: ${item.serial_number}`;
        } else if (item.item_type === 'battery') {
            description = `${item._display.jenis_text} | ${item._display.merk_text} - ${item._display.tipe_text}`;
            if (item.serial_number) description += ` | SN: ${item.serial_number}`;
        } else if (item.item_type === 'charger') {
            description = `${item._display.merk_text} - ${item._display.model_text}`;
            if (item.serial_number) description += ` | SN: ${item.serial_number}`;
        }
        
        if (item.keterangan) {
            description += `<br><small class="text-muted">Note: ${item.keterangan}</small>`;
        }
        
        html += `
            <tr>
                <td>${index + 1}</td>
                <td><span class="badge ${badgeSoftClasses[item.item_type] || 'badge-soft-gray'} item-badge">${typeLabels[item.item_type]}</span></td>
                <td>${description}</td>
                <td class="text-center">${item.qty}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteItem(${index})" title="Hapus item" aria-label="Hapus item">
                        <i class="fas fa-trash" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Delete item from table
function deleteItem(index) {
    OptimaConfirm.danger({
        title: 'Hapus Item?',
        text: 'Item ini akan dihapus dari PO.',
        confirmText: 'Ya, Hapus!',
        cancelText: window.lang('cancel'),
        onConfirm: function() {
            poItems.splice(index, 1);
            updateItemsTable();
            document.getElementById('items_json').value = JSON.stringify(poItems);
            OptimaNotify.success('Item berhasil dihapus');
        }
    });
}

/**
 * DIESEL / GASOLINE: baterai & charger tidak relevan — disable checklist + clear master baterai/charger.
 */
function syncPurchasingUnitPkgBatteryChargerFromDepartemen() {
    const $dept = $('#unit_departemen');
    if (!$dept.length) {
        return;
    }

    const upper = ($dept.find('option:selected').text() || '').trim().toUpperCase();
    const isNonElectric = upper === 'DIESEL' || upper === 'GASOLINE';

    const $bat = $('#pkg_battery');
    const $chg = $('#pkg_charger');
    if ($bat.length && $chg.length) {
        if (isNonElectric) {
            $bat.prop('checked', false).prop('disabled', true);
            $chg.prop('checked', false).prop('disabled', true);
            $bat.attr('title', 'Tidak berlaku untuk DIESEL / GASOLINE');
            $chg.attr('title', 'Tidak berlaku untuk DIESEL / GASOLINE');
            $bat.closest('.form-check').addClass('text-muted');
            $chg.closest('.form-check').addClass('text-muted');
        } else {
            $bat.prop('disabled', false);
            $chg.prop('disabled', false);
            $bat.removeAttr('title');
            $chg.removeAttr('title');
            $bat.closest('.form-check').removeClass('text-muted');
            $chg.closest('.form-check').removeClass('text-muted');
        }
    }

    const $batSel = $('#unit_baterai_id');
    const $chgSel = $('#unit_charger_id');
    if ($batSel.length) {
        if (isNonElectric) {
            $batSel.val('').trigger('change');
        }
        $batSel.prop('disabled', isNonElectric);
        $batSel.closest('.col-md-6').toggleClass('text-muted', isNonElectric);
        reinitializeModalSelect2($batSel);
    }
    if ($chgSel.length) {
        if (isNonElectric) {
            $chgSel.val('').trigger('change');
        }
        $chgSel.prop('disabled', isNonElectric);
        $chgSel.closest('.col-md-6').toggleClass('text-muted', isNonElectric);
        reinitializeModalSelect2($chgSel);
    }
}

/**
 * Reload #unit_merk options filtered by departemen_id.
 * Pass null/empty to load all brands.
 */
function loadBrandsForUnit(deptId) {
    const $merk = $('#unit_merk');
    const currentVal = $merk.val();
    $merk.html('<option value="">Loading...</option>').prop('disabled', true);
    $('#unit_model').html('<option value="">Select Brand First...</option>').prop('disabled', true);

    const params = deptId ? { departemen_id: deptId } : {};
    $.ajax({
        url: '<?= base_url('purchasing/api/get-unit-brands') ?>',
        method: 'GET',
        data: params,
        dataType: 'json',
        success: function(res) {
            $merk.html('<option value="">Select Brand...</option>');
            $merk.append('<option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color:#f0f8ff;">➕ Add New Brand</option>');
            $merk.append('<option disabled>─────────────</option>');
            if (res.success && res.data && res.data.length) {
                res.data.forEach(b => {
                    $merk.append(`<option value="${b.id_model_unit}" data-merk="${b.merk_unit}">${b.merk_unit}</option>`);
                });
                $merk.prop('disabled', false);
                // Restore previous selection if still available
                if (currentVal) $merk.val(currentVal).trigger('change.select2');
            } else {
                $merk.html('<option value="">No brands available for this department</option>');
            }
            reinitializeModalSelect2($merk);
        },
        error: function() {
            $merk.html('<option value="">Error loading brands</option>');
        }
    });
}

// Unit form cascading dropdowns (simplified - no tipe)
function initializeUnitDropdowns() {
    console.log(' Initializing Unit Dropdowns (Simplified)...');

    // Remove old handlers first to prevent duplicates
    $(document).off('change', '#unit_departemen');
    $(document).off('change', '#unit_jenis');
    $(document).off('change', '#unit_merk');
    
    // Departemen -> Jenis + Brand cascading
    $(document).on('change', '#unit_departemen', function() {
        console.log('📍 Departemen changed:', $(this).val());
        const deptId = $(this).val();
        const $jenis = $('#unit_jenis');
        const $jenisActions = $('#unit_jenis_actions');

        // Reset jenis & model
        $jenis.html('<option value="">Loading...</option>').prop('disabled', true);
        if ($jenisActions.length) $jenisActions.prop('disabled', true);
        $('#unit_model').html('<option value="">Select Brand First...</option>').prop('disabled', true);

        if (!deptId) {
            $jenis.html('<option value="">Please select a Department first...</option>').prop('disabled', true);
            if ($jenisActions.length) $jenisActions.prop('disabled', true);
            // Reset brands to show all
            loadBrandsForUnit(null);
            syncPurchasingUnitPkgBatteryChargerFromDepartemen();
            return;
        }

        // Load brands filtered by dept (parallel with jenis)
        loadBrandsForUnit(deptId);

        // Fetch jenis based on departemen
        $.ajax({
            url: '<?= base_url('/purchasing/api/get-tipe-units') ?>',
            method: 'GET',
            data: { departemen: deptId },
            dataType: 'json',
            success: function(response) {
                console.log('✅ Jenis loaded:', response);
                if (response.success && response.data && response.data.length > 0) {
                    const jenisMap = {};
                    response.data.forEach(r => {
                        if (r.jenis && r.id_tipe_unit) {
                            if (!jenisMap[r.jenis]) jenisMap[r.jenis] = r.id_tipe_unit;
                        }
                    });
                    $jenis.html('<option value="">Select Unit Type...</option>');
                    $jenis.append('<option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Add New Unit Type</option>');
                    $jenis.append('<option disabled>─────────────</option>');
                    Object.keys(jenisMap).sort().forEach(jenisName => {
                        $jenis.append(`<option value="${jenisMap[jenisName]}">${jenisName}</option>`);
                    });
                    $jenis.prop('disabled', false);
                    if ($jenisActions.length) $jenisActions.prop('disabled', false);
                    reinitializeModalSelect2($jenis);
                } else {
                    console.warn('No jenis data found');
                    $jenis.html('<option value="">No data available</option>');
                    if ($jenisActions.length) $jenisActions.prop('disabled', true);
                }
                syncPurchasingUnitPkgBatteryChargerFromDepartemen();
            },
            error: function(xhr, status, error) {
                console.error('Error loading jenis:', error);
                $jenis.html('<option value="">Error loading data</option>');
                syncPurchasingUnitPkgBatteryChargerFromDepartemen();
            }
        });
    });

    // Merk -> Model cascading (filter by merk + departemen)
    $(document).on('change', '#unit_merk', function() {
        console.log('🏷️ Merk changed:', $(this).val());
        const merk = $(this).find('option:selected').data('merk');
        const $model = $('#unit_model');
        const $modelActions = $('#unit_model_actions');
        
        $model.html('<option value="">Loading...</option>').prop('disabled', true);
        if ($modelActions.length) {
            $modelActions.prop('disabled', true);
        }
        
        if (!merk) {
            $model.html('<option value="">Please select a Brand first...</option>').prop('disabled', true);
            if ($modelActions.length) {
                $modelActions.prop('disabled', true);
            }
            return;
        }
        
        // Load models based on merk + departemen
        $.ajax({
            url: '<?= base_url('purchasing/api/get-model-units') ?>',
            method: 'GET',
            data: { merk: merk, departemen_id: $('#unit_departemen').val() || '' },
            dataType: 'json',
            success: function(response) {
                console.log('✅ Models loaded:', response);
                if (response.success && response.data && response.data.length > 0) {
                    $model.html('<option value="">Select Model...</option>');
                    $model.append('<option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Add New Model</option>');
                    $model.append('<option disabled>─────────────</option>');
                    response.data.forEach(item => {
                        $model.append(`<option value="${item.id_model_unit}">${item.model_unit}</option>`);
                    });
                    $model.prop('disabled', false);
                    
                    // Enable action button
                    if ($modelActions.length) {
                        $modelActions.prop('disabled', false);
                    }
                    
                    // Re-initialize Select2 if available
                    reinitializeModalSelect2($model);
                } else {
                    console.warn('No model data found');
                    $model.html('<option value="">No models available</option>');
                    if ($modelActions.length) {
                        $modelActions.prop('disabled', true);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error(' Error loading models:', error);
                $model.html('<option value="">Error loading models</option>');
            }
        });
    });
}

// Attachment form cascading dropdowns
function initializeAttachmentDropdowns() {
    console.log(' Initializing Attachment Dropdowns (Tipe → Merk → Model)...');
    
    // Remove old handlers first
    $(document).off('change', '#att_tipe');
    $(document).off('change', '#att_merk');
    
    // Tipe -> Merk cascading
    $(document).on('change', '#att_tipe', function() {
        const selectedTipe = $(this).val();
        const $merk = $('#att_merk');
        const $model = $('#att_model');
        
        console.log(' Attachment Tipe changed:', selectedTipe);
        
        $merk.html('<option value="">Loading...</option>').prop('disabled', true);
        $model.html('<option value="">Please select a Brand first...</option>').prop('disabled', true);
        
        if (!selectedTipe) {
            $merk.html('<option value="">Please select a Type first...</option>').prop('disabled', true);
            return;
        }
        
        // AJAX call to get merk based on tipe
        $.ajax({
            url: '<?= base_url('purchasing/api/get-attachment-merks') ?>',
            method: 'GET',
            data: { tipe: selectedTipe },
            dataType: 'json',
            success: function(response) {
                console.log('✅ Attachment Merk loaded:', response);
                if (response.success && response.data && response.data.length > 0) {
                    $merk.html('<option value="">Select Brand...</option>');
                    response.data.forEach(item => {
                        const merkName = item.merk_attachment || item.merk || 'Unknown';
                        $merk.append(`<option value="${merkName}">${merkName}</option>`);
                    });
                    $merk.prop('disabled', false);
                    
                    // Re-initialize Select2
                    reinitializeModalSelect2($merk);
                } else {
                    $merk.html('<option value="">No brands available</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error(' Error loading attachment merk:', error);
                $merk.html('<option value="">Error loading data</option>');
            }
        });
    });
    
    // Merk -> Model cascading
    $(document).on('change', '#att_merk', function() {
        const selectedTipe = $('#att_tipe').val();
        const selectedMerk = $(this).val();
        const $model = $('#att_model');
        
        console.log('🏷️ Attachment Merk changed:', selectedMerk);
        
        $model.html('<option value="">Loading...</option>').prop('disabled', true);
        
        if (!selectedTipe || !selectedMerk) {
            $model.html('<option value="">Please select a Brand first...</option>').prop('disabled', true);
            return;
        }
        
        // AJAX call to get model based on tipe and merk
        $.ajax({
            url: '<?= base_url('purchasing/api/get-attachment-models') ?>',
            method: 'GET',
            data: { tipe: selectedTipe, merk: selectedMerk },
            dataType: 'json',
            success: function(response) {
                console.log('✅ Attachment Model loaded:', response);
                if (response.success && response.data && response.data.length > 0) {
                    $model.html('<option value="">Pilih Model...</option>');
                    response.data.forEach(item => {
                        const modelName = item.model_attachment || item.model || 'Unknown';
                        const modelId = item.id_attachment || item.id;
                        $model.append(`<option value="${modelId}">${modelName}</option>`);
                    });
                    $model.prop('disabled', false);
                    
                    // Re-initialize Select2
                    reinitializeModalSelect2($model);
                } else {
                    $model.html('<option value="">No models available</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error(' Error loading attachment models:', error);
                $model.html('<option value="">Error loading data</option>');
            }
        });
    });
}

// Battery form cascading dropdowns
function initializeBatteryDropdowns() {
    console.log(' Initializing Battery Dropdowns (Jenis → Merk → Tipe)...');
    
    // Remove old handlers first
    $(document).off('change', '#battery_jenis');
    $(document).off('change', '#battery_merk');
    
    // Jenis -> Merk cascading
    $(document).on('change', '#battery_jenis', function() {
        const selectedJenis = $(this).val();
        const $merk = $('#battery_merk');
        const $tipe = $('#battery_tipe');
        
        console.log(' Battery Jenis changed:', selectedJenis);
        
        $merk.html('<option value="">Loading...</option>').prop('disabled', true);
        $tipe.html('<option value="">Please select a Brand first...</option>').prop('disabled', true);
        
        if (!selectedJenis) {
            $merk.html('<option value="">Please select a Type first...</option>').prop('disabled', true);
            return;
        }
        
        // AJAX call to get merk based on jenis
        $.ajax({
            url: '<?= base_url('purchasing/api/get-battery-merks') ?>',
            method: 'GET',
            data: { jenis: selectedJenis },
            dataType: 'json',
            success: function(response) {
                console.log('✅ Battery Merk loaded:', response);
                if (response.success && response.data && response.data.length > 0) {
                    $merk.html('<option value="">Select Brand...</option>');
                    response.data.forEach(item => {
                        const merkName = item.merk_baterai || 'Unknown';
                        $merk.append(`<option value="${merkName}">${merkName}</option>`);
                    });
                    $merk.prop('disabled', false);
                    
                    // Re-initialize Select2
                    reinitializeModalSelect2($merk);
                } else {
                    $merk.html('<option value="">No brands available</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error(' Error loading battery merk:', error);
                $merk.html('<option value="">Error loading data</option>');
            }
        });
    });
    
    // Merk -> Tipe cascading
    $(document).on('change', '#battery_merk', function() {
        const selectedJenis = $('#battery_jenis').val();
        const selectedMerk = $(this).val();
        const $tipe = $('#battery_tipe');
        
        console.log('🏷️ Battery Merk changed:', selectedMerk);
        
        $tipe.html('<option value="">Loading...</option>').prop('disabled', true);
        
        if (!selectedJenis || !selectedMerk) {
            $tipe.html('<option value="">Please select a Brand first...</option>').prop('disabled', true);
            return;
        }
        
        // AJAX call to get tipe based on jenis and merk
        $.ajax({
            url: '<?= base_url('purchasing/api/get-battery-tipes') ?>',
            method: 'GET',
            data: { jenis: selectedJenis, merk: selectedMerk },
            dataType: 'json',
            success: function(response) {
                console.log('✅ Battery Tipe loaded:', response);
                if (response.success && response.data && response.data.length > 0) {
                    $tipe.html('<option value="">Select Type...</option>');
                    response.data.forEach(item => {
                        const tipeLabel = item.tipe_baterai || 'Unknown';
                        const batteryId = item.id || item.id_baterai;
                        $tipe.append(`<option value="${batteryId}">${tipeLabel}</option>`);
                    });
                    $tipe.prop('disabled', false);
                    
                    // Re-initialize Select2
                    reinitializeModalSelect2($tipe);
                } else {
                    $tipe.html('<option value="">No types available</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error(' Error loading battery tipe:', error);
                $tipe.html('<option value="">Error loading data</option>');
            }
        });
    });
}

// Charger form cascading dropdowns
function initializeChargerDropdowns() {
    console.log(' Initializing Charger Dropdowns (Merk → Model)...');
    
    // Remove old handlers first
    $(document).off('change', '#charger_merk');
    
    // Merk -> Model cascading
    $(document).on('change', '#charger_merk', function() {
        const selectedMerk = $(this).val();
        const $model = $('#charger_model');
        
        console.log('🏷️ Charger Merk changed:', selectedMerk);
        
        $model.html('<option value="">Loading...</option>').prop('disabled', true);
        
        if (!selectedMerk) {
            $model.html('<option value="">Please select a Brand first...</option>').prop('disabled', true);
            return;
        }
        
        // AJAX call to get model based on merk
        $.ajax({
            url: '<?= base_url('purchasing/api/get-charger-models') ?>',
            method: 'GET',
            data: { merk: selectedMerk },
            dataType: 'json',
            success: function(response) {
                console.log('✅ Charger Model loaded:', response);
                if (response.success && response.data && response.data.length > 0) {
                    $model.html('<option value="">Pilih Model...</option>');
                    response.data.forEach(item => {
                        const modelName = item.model_charger || item.tipe_charger || 'Standard';
                        const chargerId = item.id_charger || item.id;
                        $model.append(`<option value="${chargerId}">${modelName}</option>`);
                    });
                    $model.prop('disabled', false);
                    
                    // Re-initialize Select2
                    reinitializeModalSelect2($model);
                } else {
                    $model.html('<option value="">No models available</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error(' Error loading charger models:', error);
                $model.html('<option value="">Error loading data</option>');
            }
        });
    });
}

        // Create Delivery Form Handler
        $('#createDeliveryForm').on('submit', function(e) {
            e.preventDefault();
            
            if (!currentPOId) {
                showNotification('PO ID not found', 'error');
                return;
            }
            
            const selectedItems = [];
            document.querySelectorAll('.delivery-group-qty').forEach(inp => {
                const q = parseInt(inp.value, 10) || 0;
                if (q <= 0) return;
                let ids = [];
                try {
                    ids = JSON.parse(inp.getAttribute('data-undelivered') || '[]');
                } catch (e) { ids = []; }
                const max = ids.length;
                const ship = Math.min(q, max);
                if (ship <= 0) return;
                selectedItems.push({
                    type: 'unit_group',
                    group_key: inp.getAttribute('data-group-key'),
                    name: inp.getAttribute('data-name') || 'Unit group',
                    ship_qty: ship,
                    undelivered_unit_ids: ids,
                    qty: ship
                });
            });
            document.querySelectorAll('.delivery-item-checkbox:checked').forEach(checkbox => {
                selectedItems.push({
                    type: checkbox.getAttribute('data-type'),
                    id: checkbox.getAttribute('data-id'),
                    name: checkbox.getAttribute('data-name'),
                    qty: 1
                });
            });
            
            if (selectedItems.length === 0) {
                showNotification('Please select at least one item to deliver', 'warning');
                return;
            }
            
            const formData = {
                [window.csrfTokenName]: window.getCsrfToken(),
                po_id: currentPOId,
                delivery_date: $('input[name="delivery_date"]').val(),
                packing_list_no: $('input[name="packing_list_no"]').val(),
                driver_name: $('input[name="driver_name"]').val(),
                driver_phone: $('input[name="driver_phone"]').val(),
                vehicle_info: $('input[name="vehicle_info"]').val(),
                vehicle_plate: $('input[name="vehicle_plate"]').val(),
                notes: $('textarea[name="notes"]').val(),
                items: JSON.stringify(selectedItems)
            };
            
            // Debug logging
            console.log('Form Data:', formData);
            console.log('Selected Items:', selectedItems);
            
            // Submit delivery
            $.ajax({
                url: '<?= base_url('/purchasing/api/create-delivery') ?>',
                type: 'POST',
                data: formData,
                success: function(response) {
                    console.log('Response:', response);
                    if (response.success) {
                        if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                            OptimaPro.showNotification(response.message || 'Delivery schedule successfully created', 'success');
                        } else {
                            showNotification(response.message || 'Delivery schedule successfully created', 'success');
                        }
                        $('#createDeliveryModal').modal('hide');
                        refreshTable();
                    } else {
                        if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                            OptimaPro.showNotification(response.message || 'Failed to create delivery schedule', 'error');
                        } else {
                            showNotification(response.message || 'Failed to create delivery schedule', 'error');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error:', xhr.responseText);
                    if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                        OptimaPro.showNotification('An error occurred while creating the delivery schedule: ' + error, 'error');
                    } else {
                        showNotification('An error occurred while creating the delivery schedule: ' + error, 'error');
                    }
                }
            });
        });
        
        // SN Assignment Form Handler
        $('#assignSNForm').on('submit', function(e) {
            e.preventDefault();
            
            if (!currentDeliveryId) {
                showNotification('Delivery ID not found', 'error');
                return;
            }
            
            const serialNumbers = collectSnAssignmentPayload();
            
            // Submit serial numbers
            $.ajax({
                url: '<?= base_url('/purchasing/api/assign-sn') ?>',
                type: 'POST',
                data: {
                    [window.csrfTokenName]: window.getCsrfToken(),
                    delivery_id: currentDeliveryId,
                    serial_numbers: JSON.stringify(serialNumbers)
                },
                success: function(response) {
                    if (response.success) {
                        if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                            OptimaPro.showNotification(response.message || 'Serial numbers successfully saved', 'success');
                        } else {
                            showNotification(response.message || 'Serial numbers successfully saved', 'success');
                        }
                        $('#assignSNModal').modal('hide');
                        refreshTable();
                    } else {
                        if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                            OptimaPro.showNotification(response.message || 'Failed to save serial numbers', 'error');
                        } else {
                            showNotification(response.message || 'Failed to save serial numbers', 'error');
                        }
                    }
                },
                error: function() {
                    if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
                        OptimaPro.showNotification('An error occurred while saving serial numbers', 'error');
                    } else {
                        showNotification('An error occurred while saving serial numbers', 'error');
                    }
                }
            });
        });
    
    // Checklist functions for delivery items
    function selectAllDeliveryItems() {
        // Only select items that are not disabled (not already delivered)
        $('.delivery-item-checkbox:not(:disabled)').prop('checked', true);
        updateDeliverySelection();
    }
    
    function clearAllDeliveryItems() {
        // Clear all items including disabled ones
        $('.delivery-item-checkbox').prop('checked', false);
        updateDeliverySelection();
    }
    
    // Category-specific selection functions
    function selectAllUnits() {
        $('input[data-type="unit"]:not(:disabled)').prop('checked', true);
        updateDeliverySelection();
    }
    
    function clearAllUnits() {
        $('input[data-type="unit"]').prop('checked', false);
        updateDeliverySelection();
    }
    
    function selectAllAttachments() {
        $('input[data-type="attachment"]:not(:disabled)').prop('checked', true);
        updateDeliverySelection();
    }
    
    function clearAllAttachments() {
        $('input[data-type="attachment"]').prop('checked', false);
        updateDeliverySelection();
    }
    
    function selectAllBatteries() {
        $('input[data-type="battery"]:not(:disabled)').prop('checked', true);
        updateDeliverySelection();
    }
    
    function clearAllBatteries() {
        $('input[data-type="battery"]').prop('checked', false);
        updateDeliverySelection();
    }
    
    function selectAllChargers() {
        $('input[data-type="charger"]:not(:disabled)').prop('checked', true);
        updateDeliverySelection();
    }
    
    function clearAllChargers() {
        $('input[data-type="charger"]').prop('checked', false);
        updateDeliverySelection();
    }
    
    function updateDeliverySelection() {
        let total = $('.delivery-item-checkbox:checked').length;
        $('.delivery-group-qty').each(function() {
            const q = parseInt($(this).val(), 10) || 0;
            if (q > 0) {
                total += 1;
            }
        });
        $('#deliveryTotalSelected').text(total);
        const submitBtn = $('#createDeliveryForm button[type="submit"]');
        if (total > 0) {
            submitBtn.prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
        } else {
            submitBtn.prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
        }
    }

    $(document).on('input change', '.delivery-group-qty', function() {
        const $inp = $(this);
        let v = parseInt($inp.val(), 10);
        if (Number.isNaN(v)) v = 0;
        const max = parseInt($inp.attr('max'), 10);
        if (!Number.isNaN(max) && v > max) {
            $inp.val(max);
        }
        if (v < 0) {
            $inp.val(0);
        }
        updateDeliverySelection();
    });

    // Auto-open PO detail modal if coming from notification
    <?php if (isset($autoOpenPoId) && $autoOpenPoId): ?>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🔗 Auto-opening PO detail from URL:', <?= $autoOpenPoId ?>);
        setTimeout(function() {
            if (typeof viewPODetail === 'function') {
                viewPODetail(<?= $autoOpenPoId ?>);
            }
        }, 800); // Wait for DataTables to initialize
    });
    <?php endif; ?>
</script>
<?= $this->endSection() ?>
