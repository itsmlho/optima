<?= $this->extend('layouts/base') ?>

<?php
/**
 * Attachment & Fork Inventory - Warehouse
 * BADGE/CARD: Optima badge-soft-* (tabs, status, condition); card-header bg-light; table mb-0.
 *
 * NOTE: Battery/Charger have been moved to dedicated pages:
 *   /warehouse/inventory/batteries  - Battery Inventory
 *   /warehouse/inventory/chargers   - Charger Inventory
 */
?>
<?= $this->section('content') ?>

<!-- Main Card -->
<div class="card shadow-sm">
    <div class="card-body">
        <!-- Header Row -->
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="bi bi-puzzle me-2 text-primary"></i>
                    Attachment & Fork Inventory
                </h4>
                <p class="text-muted mb-0">Manage forklift attachments and forks with status tracking and maintenance records</p>
            </div>
            <div class="d-flex gap-2">
                <!-- Export Dropdown -->
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-export me-1"></i>Export
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= base_url('warehouse/inventory/attachments/export/attachment') ?>"><i class="fas fa-puzzle-piece me-2"></i>Attachment</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('warehouse/inventory/attachments/export/fork') ?>"><i class="fas fa-grip-lines-vertical me-2"></i>Fork</a></li>
                    </ul>
                </div>
                <button type="button" class="btn btn-primary" id="btnTambahItem">
                    <i class="fas fa-plus me-1"></i>Add Item
                </button>
            </div>
        </div>
        
        <!-- Type Filter Tabs -->
        <ul class="nav nav-pills gap-2 mb-2" id="itemTypeTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="attachment-tab" type="button" onclick="applyTypeFilter('attachment')">
                    <i class="fas fa-puzzle-piece me-1"></i>
                    Attachment
                    <span class="badge badge-soft-blue ms-1" id="count-attachment"><?= $detailed_stats['by_type']['attachment'] ?? 0 ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="fork-tab" type="button" onclick="applyTypeFilter('fork')">
                    <i class="fas fa-grip-lines-vertical me-1"></i>
                    Fork
                    <span class="badge badge-soft-gray ms-1" id="count-fork"><?= $detailed_stats['by_type']['fork'] ?? 0 ?></span>
                </button>
            </li>
        </ul>
        
        <!-- Status Filter Tabs -->
        <ul class="nav nav-pills gap-2 mb-3" id="statusFilterTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active btn-sm" id="all-status-tab" type="button" onclick="applyStatusFilter('all')">
                    <i class="fas fa-list me-1"></i>
                    All
                    <span class="badge badge-soft-gray ms-1" id="count-all"><?= $detailed_stats['by_status']['all'] ?? 0 ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link btn-sm" id="available-status-tab" type="button" onclick="applyStatusFilter('AVAILABLE')">
                    <i class="fas fa-check-circle me-1"></i>
                    Available
                    <span class="badge badge-soft-green ms-1" id="count-available"><?= $detailed_stats['by_status']['available'] ?? 0 ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link btn-sm" id="inuse-status-tab" type="button" onclick="applyStatusFilter('IN_USE')">
                    <i class="fas fa-link me-1"></i>
                    In Use
                    <span class="badge badge-soft-cyan ms-1" id="count-inuse"><?= $detailed_stats['by_status']['in_use'] ?? 0 ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link btn-sm" id="spare-status-tab" type="button" onclick="applyStatusFilter('SPARE')">
                    <i class="fas fa-box me-1"></i>
                    Spare
                    <span class="badge badge-soft-purple ms-1" id="count-spare"><?= $detailed_stats['by_status']['spare'] ?? 0 ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link btn-sm" id="maintenance-status-tab" type="button" onclick="applyStatusFilter('MAINTENANCE')">
                    <i class="fas fa-tools me-1"></i>
                    Maintenance
                    <span class="badge badge-soft-yellow ms-1" id="count-maintenance"><?= $detailed_stats['by_status']['maintenance'] ?? 0 ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link btn-sm" id="broken-status-tab" type="button" onclick="applyStatusFilter('BROKEN')">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Broken
                    <span class="badge badge-soft-red ms-1" id="count-broken"><?= $detailed_stats['by_status']['broken'] ?? 0 ?></span>
                </button>
            </li>
        </ul>
        
        <!-- Table -->
        <table id="inventory-attachment-table" class="table table-striped table-hover mb-0" style="width:100%">
            <thead id="table-header">
                <!-- Dynamic header will be inserted here -->
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal View Attachment Detail -->
<div class="modal fade" id="viewAttachmentModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light py-2">
                <div>
                    <h5 class="modal-title mb-0" id="viewAttachmentModalTitle"><i class="fas fa-eye me-2 text-primary"></i>Detail Item</h5>
                    <p class="text-muted small mb-0" id="viewAttachmentModalSubtitle"></p>
                </div>
                <button type="button" class="btn-close btn-close-muted" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Tabs -->
                <ul class="nav nav-tabs px-3 pt-2 border-bottom" id="attachmentModalTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="att-detail-tab" data-bs-toggle="tab"
                            data-bs-target="#att-detail-pane" type="button" role="tab">
                            <i class="fas fa-info-circle me-1"></i>Detail
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="att-history-tab" data-bs-toggle="tab"
                            data-bs-target="#att-history-pane" type="button" role="tab"
                            onclick="loadAttachmentHistory(currentAttachmentId)">
                            <i class="fas fa-history me-1"></i>History
                            <span class="badge badge-soft-blue ms-1" id="attHistoryBadge" style="display:none;"></span>
                        </button>
                    </li>
                </ul>
                <div class="tab-content" style="min-height:540px;max-height:60vh;overflow-y:auto;">
                    <!-- Detail Pane -->
                    <div class="tab-pane fade show active" id="att-detail-pane" role="tabpanel">
                        <div class="row g-0">
                            <!-- Main content (left) -->
                            <div class="col-lg-8 p-3 border-end">
                                <div id="attachmentDetailContent">
                                    <div class="text-center p-5 text-muted">
                                        <i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i>Loading...
                                    </div>
                                </div>
                            </div>
                            <!-- Sidebar (right) -->
                            <div class="col-lg-4 p-3 bg-light" id="attachmentDetailSidebar">
                                <div id="attachmentQuickInfo">
                                    <!-- Populated by JS -->
                                </div>
                                <div id="attachmentQrSection">
                                    <div class="card shadow-sm mb-3">
                                        <div class="card-header bg-light d-flex align-items-center justify-content-between">
                                            <h6 class="mb-0"><i class="fas fa-qrcode me-2"></i><strong>Barcode Aset</strong></h6>
                                            <span class="badge bg-dark">Public</span>
                                        </div>
                                        <div class="card-body p-2 small" id="attachmentQrBody">
                                            <div class="text-center py-3 text-muted">
                                                <i class="fas fa-spinner fa-spin fa-2x mb-2 d-block text-primary"></i>
                                                <small>Memuat QR...</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- History Pane -->
                    <div class="tab-pane fade" id="att-history-pane" role="tabpanel">
                        <!-- Filter toolbar -->
                        <div class="d-flex align-items-center gap-2 px-3 pt-3 pb-2 border-bottom flex-wrap">
                            <span class="small fw-semibold text-muted me-1"><i class="fas fa-filter me-1"></i>Filter:</span>
                            <select id="attHistoryFilter" class="form-select form-select-sm" style="width:auto;" onchange="applyAttHistoryFilter()">
                                <option value="all">Semua</option>
                                <option value="assign">Dipasang</option>
                                <option value="detach">Dilepas</option>
                                <option value="audit">Audit/SPK/WO</option>
                                <option value="movement">Surat Jalan</option>
                                <option value="update">Update Data</option>
                            </select>
                            <select id="attHistoryGroup" class="form-select form-select-sm" style="width:auto;" onchange="applyAttHistoryFilter()">
                                <option value="document">Group: Dokumen</option>
                                <option value="date">Group: Tanggal</option>
                            </select>
                            <button class="btn btn-sm btn-outline-secondary ms-auto" onclick="resetAttachmentHistory(currentAttachmentId)">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                        <div id="attachmentHistoryContent" class="p-3">
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-history fa-2x mb-2 d-block"></i>
                                <p class="mb-0">Klik tab ini untuk memuat history.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="me-auto d-flex gap-2 align-items-center">
                    <!-- Action Buttons (Left side) -->
                    <button type="button" class="btn btn-success btn-sm" id="btnAttachToUnit" onclick="openAttachModal()" style="display:none;">
                        <i class="fas fa-link me-1"></i>Install to Unit
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" id="btnSwapUnit" onclick="openSwapModal()" style="display:none;">
                        <i class="fas fa-exchange-alt me-1"></i>Move Unit
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" id="btnDetachFromUnit" onclick="openDetachModal()" style="display:none;">
                        <i class="fas fa-unlink me-1"></i>Detach
                    </button>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" id="btnEditAttachment" onclick="editCurrentAttachment()">
                    <i class="fas fa-edit me-1"></i>Edit
                </button>
                <button type="button" class="btn btn-danger" id="btnDeleteAttachment" onclick="deleteCurrentAttachment()">
                    <i class="fas fa-trash me-1"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Edit Stock -->
<div class="modal fade" id="editAttachmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Stock Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAttachmentForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_id"        name="id">
                    <input type="hidden" id="edit_tipe_item" name="tipe_item">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Item Number / Serial Number</label>
                        <input type="text" class="form-control bg-light" id="edit_item_label" readonly>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Serial Number (SN)</label>
                            <input type="text" class="form-control" id="edit_serial_number" name="serial_number" placeholder="SN dari label fisik item">
                            <div class="small text-muted mt-1">Edit SN jika ada koreksi / perubahan</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No Item</label>
                            <input type="text" class="form-control text-uppercase" id="edit_item_number" name="item_number" placeholder="Cth: B02178">
                            <div class="small text-muted mt-1">Harus unik di seluruh sistem</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="AVAILABLE">Available</option>
                            <option value="IN_USE">In Use</option>
                            <option value="SPARE">Spare</option>
                            <option value="MAINTENANCE">Maintenance</option>
                            <option value="BROKEN">Broken</option>
                            <option value="RESERVED">Reserved</option>
                            <option value="SOLD">Sold</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_storage_location" class="form-label fw-semibold">Storage Location</label>
                        <select class="form-select" id="edit_storage_location" name="storage_location">
                            <option value="Workshop">Workshop</option>
                            <option value="WAREHOUSE">Warehouse</option>
                            <option value="POS 1">POS 1</option>
                            <option value="POS 2">POS 2</option>
                            <option value="POS 3">POS 3</option>
                            <option value="POS 4">POS 4</option>
                            <option value="POS 5">POS 5</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_physical_condition" class="form-label fw-semibold">Physical Condition</label>
                        <select class="form-select" id="edit_physical_condition" name="physical_condition">
                            <option value="GOOD">Good</option>
                            <option value="MINOR_DAMAGE">Minor Damage</option>
                            <option value="MAJOR_DAMAGE">Major Damage</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_completeness" class="form-label fw-semibold">Completeness</label>
                        <select class="form-select" id="edit_completeness" name="completeness">
                            <option value="COMPLETE">Complete</option>
                            <option value="INCOMPLETE">Incomplete</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_notes" class="form-label fw-semibold">Notes</label>
                        <textarea class="form-control" id="edit_notes" name="notes" rows="2" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Attach to Unit -->
<div class="modal fade" id="attachToUnitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fas fa-link me-2"></i>Attach to Unit</h5>
                <button type="button" class="btn-close btn-close-muted" data-bs-dismiss="modal"></button>
            </div>
            <form id="attachToUnitForm">
                <div class="modal-body">
                    <input type="hidden" id="attach_attachment_id">
                    <input type="hidden" id="attach_attachment_type">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Select a unit to attach <span id="attach_item_label"></span>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Unit <span class="text-danger">*</span></label>
                        <select class="form-select select2-search" id="attach_unit_id" required>
                            <option value="">Select Unit...</option>
                        </select>
                        <small class="text-muted">Search and select a unit (type unit number or model to search)</small>
                    </div>
                    
                    <div id="attach_existing_warning" class="alert alert-warning" style="display:none;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This unit already has <span id="attach_existing_type"></span>. 
                        Existing item will be automatically detached and returned to the Workshop.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="attach_notes" rows="2" placeholder="e.g., New unit, replacement, etc."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-link me-1"></i>Attach
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Swap Unit -->
<div class="modal fade" id="swapUnitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fas fa-exchange-alt me-2"></i>Swap Unit</h5>
                <button type="button" class="btn-close btn-close-muted" data-bs-dismiss="modal"></button>
            </div>
            <form id="swapUnitForm">
                <div class="modal-body">
                    <input type="hidden" id="swap_attachment_id">
                    <input type="hidden" id="swap_from_unit_id">
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="swap_item_label"></span> will be moved from <strong id="swap_from_unit_label"></strong> to another unit
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Move to Unit <span class="text-danger">*</span></label>
                        <select class="form-select select2-search" id="swap_to_unit_id" required>
                            <option value="">Select Destination Unit...</option>
                        </select>
                        <small class="text-muted">Search and select destination unit (type unit number or model to search)</small>
                    </div>
                    
                    <div id="swap_existing_warning" class="alert alert-warning" style="display:none;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> Destination unit already has <span id="swap_existing_type"></span>. 
                        Existing item will be automatically detached and returned to the Workshop.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Move Reason <span class="text-danger">*</span></label>
                        <select class="form-select" id="swap_reason_select" onchange="toggleSwapReasonInput(this.value)">
                            <option value="">Select Reason...</option>
                            <option value="Emergency - attachment patah">Emergency - Attachment Broken</option>
                            <option value="Swap untuk backup">Swap for Backup</option>
                            <option value="Unit maintenance">Unit Maintenance</option>
                            <option value="Upgrade attachment">Upgrade Attachment</option>
                            <option value="custom">Other Reason...</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="swap_custom_reason_group" style="display:none;">
                        <label class="form-label">Other Reason</label>
                        <textarea class="form-control" id="swap_custom_reason" rows="2" placeholder="Explain the reason for moving the unit..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-exchange-alt me-1"></i>Move Unit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detach from Unit -->
<div class="modal fade" id="detachFromUnitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fas fa-unlink me-2"></i>Detach from Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="detachFromUnitForm">
                <div class="modal-body">
                    <input type="hidden" id="detach_attachment_id">
                    <input type="hidden" id="detach_from_unit_id">
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="detach_item_label"></span> will be detached from <strong id="detach_from_unit_label"></strong>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Detach Reason <span class="text-danger">*</span></label>
                        <select class="form-select" id="detach_reason_select" onchange="toggleDetachReasonInput(this.value)" required>
                            <option value="">Select Reason...</option>
                            <option value="Rusak - perlu repair">Damaged - Needs Repair</option>
                            <option value="Maintenance rutin">Routine Maintenance</option>
                            <option value="Lepas untuk backup">Detach for Backup</option>
                            <option value="Unit pulang rental">Unit Returned from Rental</option>
                            <option value="Upgrade attachment">Upgrade Attachment</option>
                            <option value="custom">Other Reason...</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="detach_custom_reason_group" style="display:none;">
                        <label class="form-label">Custom Reason</label>
                        <textarea class="form-control" id="detach_custom_reason" rows="2" placeholder="Explain the reason for detaching..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Storage Location After Detach</label>
                        <select class="form-select" id="detach_new_location">
                            <option value="Workshop">Workshop</option>
                            <option value="POS 1">POS 1</option>
                            <option value="POS 2">POS 2</option>
                            <option value="POS 3">POS 3</option>
                            <option value="POS 4">POS 4</option>
                            <option value="POS 5">POS 5</option>
                        </select>
                        <small class="text-muted">Item will be stored at this location after detaching</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-unlink me-1"></i>Detach from Unit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>Add New Item
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h6 class="alert-heading mb-2">📋 Add Inventory Item Data</h6>
                    <p class="mb-0">Enter accurate item data according to field conditions. This data will be saved to the inventory with AVAILABLE status.</p>
                </div>
                
                <form id="addItemForm">
                    <input type="hidden" id="new-tipe-item" name="tipe_item" value="">
                    
                    <!-- Attachment Fields -->
                    <div id="attachment-fields">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Attachment Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="new-attachment-id" name="attachment_id">
                                        <option value="">Select Attachment Type</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Serial Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="new-sn-attachment" name="sn_attachment" placeholder="Enter attachment SN">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Battery and Charger fields removed — use dedicated pages -->

                    <!-- Fork Fields -->
                    <div id="fork-fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Fork Spec <span class="text-danger">*</span></label>
                                    <select class="form-select" id="new-fork-id" name="fork_id">
                                        <option value="">Select Fork Spec</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Qty Pairs <span class="text-danger">*</span></label>
                                    <input type="number" min="1" class="form-control" id="new-fork-qty-pairs" name="qty_pairs" value="1">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Common Fields -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Unit <span class="text-muted small fw-normal">(opsional)</span></label>
                                <select class="form-select" id="new-unit-id" name="unit_id">
                                    <option value="">Pilih unit jika sudah terpasang...</option>
                                </select>
                                <div class="small text-muted mt-1">Jika belum terpasang pada unit, kosongkan</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Physical Condition</label>
                                <select class="form-select" id="new-kondisi-fisik" name="physical_condition">
                                    <option value="GOOD">Good</option>
                                    <option value="MINOR_DAMAGE">Minor Damage</option>
                                    <option value="MAJOR_DAMAGE">Major Damage</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Storage Location <span class="text-danger">*</span></label>
                                <select class="form-select" id="new-lokasi" name="storage_location" required>
                                    <option value="">Select Location</option>
                                    <optgroup label="Workshop">
                                        <option value="Workshop" selected>Workshop</option>
                                        <option value="POS 1">POS 1</option>
                                        <option value="POS 2">POS 2</option>
                                        <option value="POS 3">POS 3</option>
                                        <option value="POS 4">POS 4</option>
                                        <option value="POS 5">POS 5</option>
                                    </optgroup>
                                    <optgroup label="Lainnya">
                                        <option value="WAREHOUSE">WAREHOUSE</option>
                                    </optgroup>
                                </select>
                                <small class="text-muted">Location of the item when NOT installed in a unit. If installed, the location is automatically "Installed in Unit X"</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="new-attachment-status" name="status">
                                    <option value="AVAILABLE">Available</option>
                                    <option value="IN_USE">In Use</option>
                                    <option value="SPARE">Spare</option>
                                    <option value="MAINTENANCE">Maintenance</option>
                                    <option value="BROKEN">Broken</option>
                                    <option value="RESERVED">Reserved</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" id="new-catatan" name="catatan" rows="3" placeholder="Additional notes..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="btn-save-item">
                    <i class="fas fa-save me-1"></i>Save Item
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<!-- Select2 JS sudah dimuat di base layout -->
<script>
    // Inventory Attachment & Battery Management - Updated <?= date('Y-m-d H:i:s') ?>
    // Global variables - Initialize with default values
    var currentTypeFilter = 'attachment';
    var currentStatusFilter = 'all';
    var currentModelFilter = '';
    var currentChemistryFilter = '';
    var currentAttachmentId = null;
    var attachmentTable = null;
    var csrfToken = '<?= csrf_hash() ?>';
    var csrfName = '<?= csrf_token() ?>';
    
    // Stats per type for dynamic status filter updates
    var typeStats = {
        attachment: <?= json_encode($detailed_stats['attachment'] ?? ['total' => 0, 'available' => 0, 'in_use' => 0, 'spare' => 0, 'maintenance' => 0, 'broken' => 0]) ?>,
        battery: <?= json_encode($detailed_stats['battery'] ?? ['total' => 0, 'available' => 0, 'in_use' => 0, 'spare' => 0, 'maintenance' => 0, 'broken' => 0]) ?>,
        charger: <?= json_encode($detailed_stats['charger'] ?? ['total' => 0, 'available' => 0, 'in_use' => 0, 'spare' => 0, 'maintenance' => 0, 'broken' => 0]) ?>,
        fork: <?= json_encode($detailed_stats['fork'] ?? ['total' => 0, 'available' => 0, 'in_use' => 0, 'spare' => 0, 'maintenance' => 0, 'broken' => 0]) ?>
    };
    
    // Helper to update CSRF token from response
    function updateCsrfToken(response) {
        if (response && response.csrf_hash) {
            csrfToken = response.csrf_hash;
        }
    }
    
    // Update status filter counts based on selected type
    function updateStatusCounts(type) {
        var stats = typeStats[type] || typeStats.attachment;
        $('#count-all').text(stats.total || 0);
        $('#count-available').text(stats.available || 0);
        $('#count-inuse').text(stats.in_use || 0);
        $('#count-spare').text(stats.spare || 0);
        $('#count-maintenance').text(stats.maintenance || 0);
        $('#count-broken').text(stats.broken || 0);
    }


    $(document).ready(function() {
        console.log('🔧 Inventory Attachment JavaScript loaded');
        console.log('Initial type filter:', currentTypeFilter);
        console.log('Initial status filter:', currentStatusFilter);

        // Initialize DataTable and other code
        setupDataTable();

        // Reset attachment history state when modal closes
        $('#viewAttachmentModal').on('hidden.bs.modal', function() {
            currentAttachmentId = null;
            attachmentHistoryLoaded = null;
            $('#attHistoryBadge').hide().text('');
            $('#att-detail-tab').tab('show');
            $('#attachmentHistoryContent').html(`
                <div class="text-center text-muted py-4">
                    <i class="fas fa-history fa-2x mb-2 d-block"></i>
                    <p class="mb-0">Klik tab ini untuk memuat history.</p>
                </div>
            `);
        });

        // Handle add item button click
        $('#btnTambahItem').on('click', function() {
            const type = currentTypeFilter || 'attachment'; // Use current filter
            console.log('🆕 Add item button clicked for type:', type);
            openAddItemModal(type);
        });

        // Remove duplicate tab handlers since applyTypeFilter handles this
    });

    function createDynamicHeader(type) {
        let headerHtml = '<tr>';
        
        // Common columns for all types
        headerHtml += '<th>No Item</th>';
        headerHtml += '<th>Type</th>';
        
        // Type-specific columns
        if (type === 'attachment') {
            headerHtml += '<th>Brand</th>';
            headerHtml += '<th>Type</th>';
            headerHtml += '<th>Models</th>';
        } else if (type === 'fork') {
            headerHtml += '<th>Fork Spec</th>';
            headerHtml += '<th>Class</th>';
        }
        
        // Common columns continued
        headerHtml += '<th>SN</th>';
        headerHtml += '<th>Physical Condition</th>';
        headerHtml += '<th>Status</th>';
        headerHtml += '<th>Location</th>';
        
        headerHtml += '</tr>';
        
        $('#table-header').html(headerHtml);
    }

    function getDynamicColumns(type) {
        let columns = [
            // No Item column (always first) - item_number field from DB (no_item alias only exists on fork)
            { 
                data: null,
                render: function(data, type, row) {
                    return row.item_number || row.no_item || '-';
                }
            },
            // Tipe Item column (always second)
            { 
                data: 'tipe_item',
                render: function(data, type, row) {
                    const typeMap = {
                        'attachment': '<i class="fas fa-puzzle-piece me-1 text-primary"></i>Attachment',
                        'battery': '<i class="fas fa-battery-half me-1 text-success"></i>Battery',
                        'charger': '<i class="fas fa-plug me-1 text-warning"></i>Charger',
                        'fork': '<i class="fas fa-grip-lines-vertical me-1 text-secondary"></i>Fork'
                    };
                    
                    if (data && typeMap[data]) {
                        return typeMap[data];
                    }
                    
                    // Fallback logic based on available serial numbers
                    if (row.sn_attachment) {
                        return '<i class="fas fa-puzzle-piece me-1 text-primary"></i>Attachment';
                    } else if (row.sn_baterai) {
                        return '<i class="fas fa-battery-half me-1 text-success"></i>Battery';
                    } else if (row.sn_charger) {
                        return '<i class="fas fa-plug me-1 text-warning"></i>Charger';
                    } else if (row.sn_fork || row.fork_spec_name) {
                        return '<i class="fas fa-grip-lines-vertical me-1 text-secondary"></i>Fork';
                    }
                    
                    return '<i class="fas fa-question me-1 text-muted"></i>Unknown';
                }
            }
        ];
        
        // Add type-specific columns
        if (type === 'attachment') {
            columns.push(
                { 
                    data: null,
                    render: function(data, type, row) {
                        return row.attachment_merk || '-';
                    }
                },
                { 
                    data: null,
                    render: function(data, type, row) {
                        return row.attachment_tipe || '-';
                    }
                },
                { 
                    data: null,
                    render: function(data, type, row) {
                        return row.attachment_model || '-';
                    }
                }
            );
        } else if (type === 'fork') {
            columns.push(
                {
                    data: null,
                    render: function(data, type, row) {
                        return row.fork_spec_name || row.fork_name || '-';
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return row.fork_class || '-';
                    }
                }
            );
        }
        
        // Add common columns
        columns.push(
            // SN Column
            { 
                data: null,
                render: function(data, type, row) {
                    // Show the appropriate SN based on tipe_item
                    switch(row.tipe_item) {
                        case 'attachment':
                            return row.sn_attachment || '-';
                        case 'battery':
                            return row.sn_baterai || '-';
                        case 'charger':
                            return row.sn_charger || '-';
                        case 'fork':
                            return row.sn_fork || row.item_number || '-';
                        default:
                            // Fallback: show any available SN
                            return row.sn_attachment || row.sn_baterai || row.sn_charger || '-';
                    }
                }
            },
            // Physical Condition
            { 
                data: 'physical_condition',
                render: function(data, type, row) {
                    if (!data) return '-';
                    const map = {
                        'GOOD':         '<span class="badge badge-soft-green">Good</span>',
                        'MINOR_DAMAGE': '<span class="badge badge-soft-yellow">Minor Damage</span>',
                        'MAJOR_DAMAGE': '<span class="badge badge-soft-red">Major Damage</span>'
                    };
                    return map[data] || `<span class="badge badge-soft-gray">${data}</span>`;
                }
            },
            // Status
            { 
                data: 'status',
                render: function(data, type, row) {
                    if (!data) return '-';
                    const map = {
                        'AVAILABLE':  '<span class="badge badge-soft-green">Available</span>',
                        'IN_USE':     '<span class="badge badge-soft-blue">In Use</span>',
                        'SPARE':      '<span class="badge badge-soft-cyan">Spare</span>',
                        'MAINTENANCE':'<span class="badge badge-soft-yellow">Maintenance</span>',
                        'BROKEN':     '<span class="badge badge-soft-red">Broken</span>',
                        'RESERVED':   '<span class="badge badge-soft-gray">Reserved</span>',
                        'SOLD':       '<span class="badge badge-soft-gray">Sold</span>'
                    };
                    return map[data] || `<span class="badge badge-soft-gray">${data}</span>`;
                }
            },
            // Storage Location
            { 
                data: 'storage_location',
                render: function(data, type, row) {
                    return data || '-';
                }
            }
        );
        
        return columns;
    }

    function setupDataTable() {
        // Create dynamic header based on current filter
        createDynamicHeader(currentTypeFilter);
        
        console.log('Setting up DataTable with type:', currentTypeFilter);
        
        attachmentTable = $('#inventory-attachment-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('warehouse/inventory/attachments') ?>',
                type: 'POST',
                data: function(d) {
                    d.tipe_item = currentTypeFilter;
                    d.status_filter = currentStatusFilter;
                    d.model_filter = currentModelFilter;
                    d.chemistry_filter = currentChemistryFilter;
                    d[csrfName] = csrfToken;
                    console.log('Sending data to server:', d);
                },
                dataSrc: function(response) {
                    updateCsrfToken(response);
                    return response.data || [];
                },
                error: function(xhr, error, thrown) {
                    console.log('DataTables Ajax Error:');
                    console.log('XHR:', xhr);
                    console.log('Error:', error);
                    console.log('Thrown:', thrown);
                    console.log('Response Text:', xhr.responseText);
                    
                    OptimaNotify.error('An error occurred while loading data. Please check the console for details.', 'Error!');
                }
            },
            columns: getDynamicColumns(currentTypeFilter),
            order: [[ 0, "desc" ]]
        });

        // Add click event to table rows
        $('#inventory-attachment-table tbody').on('click', 'tr', function() {
            const data = attachmentTable.row(this).data();
            if (data && data.id_inventory_attachment) {
                currentAttachmentId = data.id_inventory_attachment;
                viewAttachment(data.id_inventory_attachment);
            }
        });

        // Handle edit form submission
        $('#editAttachmentForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_id').val();
            $.ajax({
                url: `<?= base_url('warehouse/inventory/attachments/update/') ?>${id}`,
                type: 'POST',
                data: $(this).serialize() + '&' + csrfName + '=' + csrfToken,
                dataType: 'json',
                success: function(response) {
                    updateCsrfToken(response);
                    if (response.success) {
                        $('#editAttachmentModal').modal('hide');
                        attachmentTable.ajax.reload(null, false);
                        OptimaNotify.success(response.message, 'Berhasil!');
                    } else {
                        OptimaNotify.error(response.message, 'Gagal!');
                    }
                },
                error: function() {
                    OptimaNotify.error('Cannot connect to the server.', 'Error!');
                }
            });
        });
    }

    window.applyTypeFilter = function(type) {
        console.log('Applying type filter:', type);
        
        // Remove active class from all type tabs only (not status tabs)
        $('#itemTypeTab .nav-link').removeClass('active');
        
        // Add active class to clicked tab
        if (type === 'attachment') {
            $('#attachment-tab').addClass('active');
        } else if (type === 'fork') {
            $('#fork-tab').addClass('active');
        }
        
        // Update current filter
        currentTypeFilter = type;
        currentStatusFilter = 'all'; // Reset status filter when type changes
        currentModelFilter = ''; // Reset model filter
        currentChemistryFilter = ''; // Reset chemistry filter
        console.log('Current type filter set to:', currentTypeFilter);
        console.log('Status filter reset to:', currentStatusFilter);
        
        // Reset status tabs to 'All'
        $('#statusFilterTab .nav-link').removeClass('active');
        $('#all-status-tab').addClass('active');
        
        // Reset chemistry filter buttons
        $('#chemistryFilterGroup .btn').removeClass('active');
        $('#chemistryFilterGroup .btn[data-chemistry=""]').addClass('active');
        
        // Update status counts for the selected type
        updateStatusCounts(type);
        
        // Destroy existing table
        if (attachmentTable) {
            attachmentTable.destroy();
            $('#inventory-attachment-table').empty(); // Clear table completely
        }
        
        // Recreate table structure
        $('#inventory-attachment-table').html('<thead id="table-header"></thead><tbody></tbody>');
        
        // Recreate dynamic header
        createDynamicHeader(type);
        
        // Recreate table with new columns
        attachmentTable = $('#inventory-attachment-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('warehouse/inventory/attachments') ?>',
                type: 'POST',
                data: function(d) {
                    d.tipe_item = currentTypeFilter;
                    d.status_filter = currentStatusFilter;
                    d.model_filter = currentModelFilter;
                    d.chemistry_filter = currentChemistryFilter;
                    d[csrfName] = csrfToken;
                    console.log('Sending data to server:', d);
                    return d;
                },
                dataSrc: function(response) {
                    updateCsrfToken(response);
                    return response.data || [];
                },
                error: function(xhr, error, thrown) {
                    console.log('DataTables Ajax Error:', {xhr, error, thrown});
                    OptimaNotify.error('An error occurred while loading data.', 'Error!');
                }
            },
            columns: getDynamicColumns(currentTypeFilter),
            order: [[ 0, "desc" ]]
        });
        
        // Add click event to table rows
        $('#inventory-attachment-table tbody').off('click').on('click', 'tr', function() {
            const data = attachmentTable.row(this).data();
            if (data && data.id_inventory_attachment) {
                viewAttachment(data.id_inventory_attachment);
            }
        });
    }

    // Status Filter Function
    window.applyStatusFilter = function(status) {
        console.log('Applying status filter:', status);
        
        // Remove active class from all status tabs
        $('#statusFilterTab .nav-link').removeClass('active');
        
        // Add active class to clicked status tab
        if (status === 'all') {
            $('#all-status-tab').addClass('active');
        } else if (status === 'AVAILABLE') {
            $('#available-status-tab').addClass('active');
        } else if (status === 'IN_USE') {
            $('#inuse-status-tab').addClass('active');
        } else if (status === 'SPARE') {
            $('#spare-status-tab').addClass('active');
        } else if (status === 'MAINTENANCE') {
            $('#maintenance-status-tab').addClass('active');
        } else if (status === 'BROKEN') {
            $('#broken-status-tab').addClass('active');
        }
        
        // Update current status filter
        currentStatusFilter = status;
        console.log('Current status filter set to:', currentStatusFilter);
        
        // Reload table with new filters
        if (attachmentTable) {
            attachmentTable.ajax.reload();
        }
    }
    
    // Chemistry Type Filter Function (Lead Acid vs Lithium-ion)
    window.applyChemistryFilter = function(chemistry) {
        console.log('Applying chemistry filter:', chemistry);
        
        // Remove active class from all chemistry buttons
        $('#chemistryFilterGroup .btn').removeClass('active');
        
        // Add active class to clicked button
        $(`#chemistryFilterGroup .btn[data-chemistry="${chemistry}"]`).addClass('active');
        
        // Update current chemistry filter
        currentChemistryFilter = chemistry;
        console.log('Current chemistry filter set to:', currentChemistryFilter);
        
        // Reload table with new filters
        if (attachmentTable) {
            attachmentTable.ajax.reload();
        }
    }

    // Populate Model Filter based on type
    function populateModelFilter(type) {
        const $modelFilterGroup = $('#modelFilterGroup');
        $modelFilterGroup.empty();
        
        // Add "All" button
        $modelFilterGroup.append(`
            <button type="button" class="btn btn-sm btn-outline-secondary active" data-model="" onclick="applyModelFilter('')">
                All Models
            </button>
        `);
        
        if (type === 'battery') {
            // Battery model filter disabled - no additional buttons needed
            // Filter will show all battery records
        } else if (type === 'charger') {
            $modelFilterGroup.append(`
                <button type="button" class="btn btn-sm btn-outline-warning" data-model="24V" onclick="applyModelFilter('24V')">
                    24V
                </button>
                <button type="button" class="btn btn-sm btn-outline-warning" data-model="48V" onclick="applyModelFilter('48V')">
                    48V
                </button>
                <button type="button" class="btn btn-sm btn-outline-warning" data-model="80V" onclick="applyModelFilter('80V')">
                    80V
                </button>
            `);
        }
    }

    // Apply Model Filter
    window.applyModelFilter = function(model) {
        console.log('Model filter clicked:', model);
        currentModelFilter = model;
        
        // Update button states
        $('#modelFilterGroup button').removeClass('active');
        if (model === '') {
            $('#modelFilterGroup button[data-model=""]').addClass('active');
        } else {
            $('#modelFilterGroup button[data-model="' + model + '"]').addClass('active');
        }
        
        // Reload table
        if (attachmentTable) {
            attachmentTable.ajax.reload();
        }
    }

    // Global state for current item print/QR
    var _currentAttachmentData = null;

    window.viewAttachment = function(id) {
        console.log('viewAttachment called for ID:', id);
        currentAttachmentId = id;
        attachmentHistoryLoaded = false;
        currentHistoryAttachmentId = id;
        _currentAttachmentData = null;

        // Reset tabs: activate Detail tab, clear history pane
        const detailTabEl = document.getElementById('att-detail-tab');
        const historyTabEl = document.getElementById('att-history-tab');
        if (detailTabEl && historyTabEl) {
            detailTabEl.classList.add('active');
            historyTabEl.classList.remove('active');
        }
        const detailPane = document.getElementById('att-detail-pane');
        const historyPane = document.getElementById('att-history-pane');
        if (detailPane) { detailPane.classList.add('show', 'active'); }
        if (historyPane) { historyPane.classList.remove('show', 'active'); }

        // Reset history pane placeholder
        $('#attachmentHistoryContent').html(`
            <div class="text-center text-muted py-4">
                <i class="fas fa-history fa-2x mb-2 d-block"></i>
                <p class="mb-0">Klik tab ini untuk memuat history.</p>
            </div>
        `);
        $('#attHistoryBadge').hide().text('');

        // Reset modal title/subtitle
        $('#viewAttachmentModalTitle').html('<i class="fas fa-spinner fa-spin me-2 text-muted"></i>Memuat...');
        $('#viewAttachmentModalSubtitle').text('');

        // Show loading state in main content + sidebar
        $('#attachmentDetailContent').html(`
            <div class="text-center py-5 text-muted">
                <i class="fas fa-spinner fa-spin fa-2x mb-2 d-block text-primary"></i>
                <p class="mb-0">Memuat detail...</p>
            </div>
        `);
        $('#attachmentQuickInfo').html('');
        $('#attachmentQrBody').html(`
            <div class="text-center py-3 text-muted">
                <i class="fas fa-spinner fa-spin fa-2x mb-2 d-block text-primary"></i>
                <small>Memuat QR...</small>
            </div>`);
        $('#viewAttachmentModal').modal('show');

        $.ajax({
            url: `<?= base_url('warehouse/inventory/attachments/detail/') ?>${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    _currentAttachmentData = data;
                    renderAttachmentDetail(data);
                    renderAttachmentSidebar(data);

                    // Set modal title
                    const typeIcon = {attachment:'fa-puzzle-piece',battery:'fa-battery-half',charger:'fa-plug',fork:'fa-grip-lines-vertical'}[data.tipe_item] || 'fa-box';
                    const typeLabel = (data.tipe_item||'Item').charAt(0).toUpperCase()+(data.tipe_item||'Item').slice(1);
                    $('#viewAttachmentModalTitle').html(`<i class="fas ${typeIcon} me-2 text-primary"></i>${typeLabel} Detail`);
                    $('#viewAttachmentModalSubtitle').text(data.item_number || data.serial_number || '');

                    // Show/hide unit action buttons
                    $('#btnAttachToUnit, #btnSwapUnit, #btnDetachFromUnit').hide();
                    if (!data.id_inventory_unit || data.id_inventory_unit == '0') {
                        // Not assigned to unit → can install
                        if (data.tipe_item !== 'fork') { // forks managed differently
                            $('#btnAttachToUnit').show();
                        }
                    } else {
                        $('#btnSwapUnit').show();
                        $('#btnDetachFromUnit').show();
                    }

                    // Load public token & QR if available
                    if (data.public_view_token) {
                        showAttachmentQr(data.public_view_token);
                    } else {
                        // Request token generation lazily
                        $.get(`<?= base_url('warehouse/inventory/attachments/get-token/') ?>${id}/${data.tipe_item||'attachment'}`, function(res) {
                            if (res && res.token) {
                                _currentAttachmentData.public_view_token = res.token;
                                _currentAttachmentData.public_url = res.public_url;
                                showAttachmentQr(res.token);
                            }
                        }).fail(function(){
                            // Token generation not available – QR section stays hidden
                        });
                    }
                } else {
                    $('#attachmentDetailContent').html(`<div class="alert alert-danger m-3"><i class="fas fa-exclamation-triangle me-2"></i>${response.message||'Gagal memuat detail.'}</div>`);
                }
            },
            error: function(xhr) {
                let msg = 'Terjadi kesalahan saat memuat detail.';
                try { const r = JSON.parse(xhr.responseText); if(r.message) msg = r.message; } catch(e){}
                $('#attachmentDetailContent').html(`<div class="alert alert-danger m-3"><i class="fas fa-exclamation-triangle me-2"></i>${msg}</div>`);
            }
        });
    }

    function showAttachmentQr(token) {
        const publicUrl = `<?= base_url('attachment-view/') ?>${token}`;
        const qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=' + encodeURIComponent(publicUrl);
        if (_currentAttachmentData) {
            _currentAttachmentData.public_url = publicUrl;
            _currentAttachmentData.public_view_token = token;
        }
        $('#attachmentQrBody').html(`
            <div class="text-center border rounded p-2">
                <img src="${qrUrl}" alt="QR Code" style="width:160px;height:160px;">
                <div class="mt-2">
                    <a href="${publicUrl}" target="_blank" class="btn btn-sm btn-dark me-1">
                        <i class="fas fa-link me-1"></i>Link
                    </a>
                    <button type="button" class="btn btn-sm btn-primary" onclick="downloadAttachmentLabel()">
                        <i class="fas fa-download me-1"></i>Download Barcode Label
                    </button>
                </div>
            </div>
        `);
    }

    function renderAttachmentDetail(data) {
        const h = (s) => (s===null||s===undefined||s==='')?'-':String(s).replace(/</g,'&lt;').replace(/>/g,'&gt;');
        const badge = (val, map) => { const cls = map[val]||'badge-soft-gray'; return `<span class="badge ${cls}">${h(val)}</span>`; };
        const statusMap = {AVAILABLE:'badge-soft-green',IN_USE:'badge-soft-blue',SPARE:'badge-soft-cyan',MAINTENANCE:'badge-soft-yellow',BROKEN:'badge-soft-red',RESERVED:'badge-soft-gray',SOLD:'badge-soft-gray'};
        const condMap = {GOOD:'badge-soft-green',MINOR_DAMAGE:'badge-soft-yellow',MAJOR_DAMAGE:'badge-soft-red'};
        const condLabel = {GOOD:'Good',MINOR_DAMAGE:'Minor Damage',MAJOR_DAMAGE:'Major Damage'};
        const fmt = (d) => { if(!d) return '-'; try{return new Date(d).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'});}catch(e){return d;} };
        const fmtDt = (d) => { if(!d) return '-'; try{return new Date(d).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});}catch(e){return d;} };

        // — Status strip at top —
        const statusVal  = data.status || data.attachment_status || '-';
        const condVal    = data.physical_condition || data.kondisi_fisik || '';
        const condLbl    = condLabel[condVal] || condVal;
        let header = `
        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom flex-wrap">
            <span class="fw-bold font-monospace fs-6">${h(data.item_number||data.no_item||'#'+data.id)}</span>
            ${badge(statusVal, statusMap)}
            ${condVal ? badge(condVal, condMap).replace('>'+h(condVal)+'<', '>'+h(condLbl)+'<') : ''}
        </div>`;

        // — Asset Info card —
        const sn = data.serial_number || data.sn_attachment || data.sn_baterai || data.sn_charger || data.sn_fork || '';
        let assetCard = `
        <div class="card mb-3">
            <div class="card-header bg-light py-2"><h6 class="mb-0 small"><i class="fas fa-box me-2 text-secondary"></i><strong>Informasi Aset</strong></h6></div>
            <div class="card-body p-0">
                <dl class="row mb-0 small px-3 pt-2">
                    <dt class="col-5 text-muted">Item Number</dt><dd class="col-7 fw-bold font-monospace">${h(data.item_number||data.no_item)}</dd>
                    <dt class="col-5 text-muted">Serial Number</dt><dd class="col-7 fw-bold font-monospace">${sn||'-'}</dd>
                    <dt class="col-5 text-muted">Status</dt><dd class="col-7">${badge(statusVal,statusMap)}</dd>
                    <dt class="col-5 text-muted">Kondisi</dt><dd class="col-7">${condVal?badge(condVal,condMap).replace('>'+h(condVal)+'<', '>'+h(condLbl)+'<'):'-'}</dd>
                    <dt class="col-5 text-muted">Kelengkapan</dt><dd class="col-7">${badge(data.completeness||data.kelengkapan||'-',{COMPLETE:'badge-soft-green',INCOMPLETE:'badge-soft-yellow'})}</dd>
                    <dt class="col-5 text-muted">Lokasi Simpan</dt><dd class="col-7">${h(data.storage_location||data.lokasi_penyimpanan)}</dd>
                    <dt class="col-5 text-muted">Tanggal Masuk</dt><dd class="col-7">${fmt(data.received_at||data.tanggal_masuk)}</dd>
                    <dt class="col-5 text-muted">Dibuat</dt><dd class="col-7">${fmtDt(data.created_at)}</dd>
                    <dt class="col-5 text-muted">Diperbarui</dt><dd class="col-7">${fmtDt(data.updated_at)}</dd>
                </dl>
                ${data.notes||data.catatan_inventory?`<div class="px-3 pb-2 pt-1 border-top"><span class="small text-muted"><i class="fas fa-sticky-note me-1"></i></span><span class="small fst-italic">${h(data.notes||data.catatan_inventory)}</span></div>`:''}
            </div>
        </div>`;

        // — Type-specific specs card —
        let specsCard = '';
        const tipe = data.tipe_item || '';
        if (tipe === 'attachment') {
            specsCard = `
            <div class="card mb-3">
                <div class="card-header bg-light py-2"><h6 class="mb-0 small"><i class="fas fa-puzzle-piece me-2 text-primary"></i><strong>Spesifikasi Attachment</strong></h6></div>
                <div class="card-body p-0">
                    <dl class="row mb-0 small px-3 py-2">
                        <dt class="col-5 text-muted">Merk</dt><dd class="col-7 fw-bold">${h(data.merk||data.attachment_merk)}</dd>
                        <dt class="col-5 text-muted">Tipe</dt><dd class="col-7">${h(data.tipe||data.attachment_tipe)}</dd>
                        <dt class="col-5 text-muted">Model</dt><dd class="col-7">${h(data.model||data.attachment_model)}</dd>
                        <dt class="col-5 text-muted">Kapasitas Maks</dt><dd class="col-7">${h(data.max_capacity)}</dd>
                    </dl>
                </div>
            </div>`;
        } else if (tipe === 'battery') {
            specsCard = `
            <div class="card mb-3">
                <div class="card-header bg-light py-2"><h6 class="mb-0 small"><i class="fas fa-battery-half me-2 text-success"></i><strong>Spesifikasi Battery</strong></h6></div>
                <div class="card-body p-0">
                    <dl class="row mb-0 small px-3 py-2">
                        <dt class="col-5 text-muted">Merk</dt><dd class="col-7 fw-bold">${h(data.merk_baterai)}</dd>
                        <dt class="col-5 text-muted">Tipe</dt><dd class="col-7">${h(data.tipe_baterai)}</dd>
                        <dt class="col-5 text-muted">Jenis</dt><dd class="col-7">${h(data.jenis_baterai)}</dd>
                        <dt class="col-5 text-muted">Voltage</dt><dd class="col-7">${h(data.voltage)}</dd>
                        <dt class="col-5 text-muted">Ampere</dt><dd class="col-7">${h(data.ampere)}</dd>
                    </dl>
                </div>
            </div>`;
        } else if (tipe === 'charger') {
            specsCard = `
            <div class="card mb-3">
                <div class="card-header bg-light py-2"><h6 class="mb-0 small"><i class="fas fa-plug me-2 text-warning"></i><strong>Spesifikasi Charger</strong></h6></div>
                <div class="card-body p-0">
                    <dl class="row mb-0 small px-3 py-2">
                        <dt class="col-5 text-muted">Merk</dt><dd class="col-7 fw-bold">${h(data.merk_charger)}</dd>
                        <dt class="col-5 text-muted">Tipe</dt><dd class="col-7">${h(data.tipe_charger)}</dd>
                        <dt class="col-5 text-muted">Input Voltage</dt><dd class="col-7">${h(data.input_voltage)}</dd>
                        <dt class="col-5 text-muted">Output Voltage</dt><dd class="col-7">${h(data.output_voltage)}</dd>
                        <dt class="col-5 text-muted">Frekuensi</dt><dd class="col-7">${h(data.frequency)}</dd>
                    </dl>
                </div>
            </div>`;
        } else if (tipe === 'fork') {
            specsCard = `
            <div class="card mb-3">
                <div class="card-header bg-light py-2"><h6 class="mb-0 small"><i class="fas fa-grip-lines-vertical me-2 text-secondary"></i><strong>Spesifikasi Fork</strong></h6></div>
                <div class="card-body p-0">
                    <dl class="row mb-0 small px-3 py-2">
                        <dt class="col-5 text-muted">Spec Name</dt><dd class="col-7 fw-bold">${h(data.fork_spec_name||data.fork_name)}</dd>
                        <dt class="col-5 text-muted">Class</dt><dd class="col-7">${h(data.fork_class)}</dd>
                        <dt class="col-5 text-muted">Panjang</dt><dd class="col-7">${data.length_mm?h(data.length_mm)+' mm':'-'}</dd>
                        <dt class="col-5 text-muted">Lebar</dt><dd class="col-7">${data.width_mm?h(data.width_mm)+' mm':'-'}</dd>
                        <dt class="col-5 text-muted">Kapasitas</dt><dd class="col-7">${data.capacity_kg?h(data.capacity_kg)+' kg':'-'}</dd>
                        <dt class="col-5 text-muted">Qty Pair</dt><dd class="col-7">${h(data.qty_pairs)}</dd>
                    </dl>
                </div>
            </div>`;
        }

        // — PO Info card (only when PO exists) —
        let poCard = '';
        if (data.no_po) {
            poCard = `
            <div class="card mb-3">
                <div class="card-header bg-light py-2"><h6 class="mb-0 small"><i class="fas fa-file-invoice me-2 text-secondary"></i><strong>Purchase Order</strong></h6></div>
                <div class="card-body p-0">
                    <dl class="row mb-0 small px-3 py-2">
                        <dt class="col-5 text-muted">No. PO</dt><dd class="col-7 fw-bold font-monospace">${h(data.no_po)}</dd>
                        <dt class="col-5 text-muted">Tanggal PO</dt><dd class="col-7">${fmt(data.tanggal_po)}</dd>
                        <dt class="col-5 text-muted">Supplier</dt><dd class="col-7">${h(data.nama_supplier)}</dd>
                        <dt class="col-5 text-muted">Status PO</dt><dd class="col-7">${badge(data.po_status||'-',{APPROVED:'badge-soft-green',PENDING:'badge-soft-yellow'})}</dd>
                    </dl>
                </div>
            </div>`;
        }

        $('#attachmentDetailContent').html(header + assetCard + specsCard + poCard);
    }

    function renderAttachmentSidebar(data) {
        const h = (s) => (s===null||s===undefined||s==='')?'-':String(s).replace(/</g,'&lt;').replace(/>/g,'&gt;');
        const statusMap = {AVAILABLE:'badge-soft-green',IN_USE:'badge-soft-blue',SPARE:'badge-soft-cyan',MAINTENANCE:'badge-soft-yellow',BROKEN:'badge-soft-red',RESERVED:'badge-soft-gray',SOLD:'badge-soft-gray'};
        const typeIcon = {attachment:'fa-puzzle-piece text-primary',battery:'fa-battery-half text-success',charger:'fa-plug text-warning',fork:'fa-grip-lines-vertical text-secondary'};
        const tipe = data.tipe_item || 'attachment';
        const icon = typeIcon[tipe] || 'fa-box text-muted';
        const typeLabel = tipe.charAt(0).toUpperCase()+tipe.slice(1);
        const statusVal = data.status || data.attachment_status || '-';
        const sn = data.serial_number || data.sn_attachment || data.sn_baterai || data.sn_charger || data.sn_fork || '';
        const noUnit = data.no_unit || data.unit_no_polisi || '';

        // Blue outline only when installed on a unit
        const isInstalled = !!noUnit;
        const cardBorder  = isInstalled ? 'border-primary border-opacity-25' : 'border';
        const cardBg      = isInstalled ? 'style="background:rgba(13,110,253,.05)"' : '';

        const unitSn    = data.unit_sn    || data.unit_serial_number || '';
        const unitMerk  = data.unit_merk  || '';
        const unitModel = data.unit_model || '';
        const unitJenis = data.unit_jenis || '';

        const unitRow = isInstalled
            ? `<div class="mt-2 pt-2 border-top">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="fas fa-truck text-primary"></i>
                    <span class="small fw-semibold text-primary">Terpasang di Unit</span>
                </div>
                <dl class="row mb-0 small ms-1">
                    <dt class="col-5 text-muted">No Unit</dt>
                    <dd class="col-7 fw-bold font-monospace">${h(noUnit)}</dd>
                    ${unitSn?`<dt class="col-5 text-muted">S/N Unit</dt><dd class="col-7 font-monospace">${h(unitSn)}</dd>`:''}
                    ${unitMerk?`<dt class="col-5 text-muted">Merk</dt><dd class="col-7">${h(unitMerk)}</dd>`:''}
                    ${unitModel?`<dt class="col-5 text-muted">Model</dt><dd class="col-7">${h(unitModel)}</dd>`:''}
                    ${unitJenis?`<dt class="col-5 text-muted">Jenis</dt><dd class="col-7">${h(unitJenis)}</dd>`:''}
                </dl>
               </div>`
            : `<div class="mt-2 pt-2 border-top text-muted small">
                <i class="fas fa-circle-xmark me-1"></i>Belum terpasang di unit
               </div>`;

        $('#attachmentQuickInfo').html(`
            <div class="card mb-3 ${cardBorder}">
                <div class="card-header py-2 d-flex align-items-center justify-content-between" ${cardBg}>
                    <h6 class="mb-0 small fw-semibold"><i class="fas fa-info-circle me-2 ${isInstalled?'text-primary':'text-muted'}"></i>Quick Info</h6>
                    <span class="badge ${statusMap[statusVal]||'badge-soft-gray'}">${h(statusVal)}</span>
                </div>
                <div class="card-body py-2 px-3">
                    ${unitRow}
                </div>
            </div>
        `);
    }

    function createAttachmentDetailHtml(data) {
        // Legacy fallback — now handled by renderAttachmentDetail
        renderAttachmentDetail(data);
        return '';
    }


    function editAttachment(id) {
        $.ajax({
            url: `<?= base_url('warehouse/inventory/attachments/detail/') ?>${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    const label = data.item_number
                        ? `${data.item_number} — ${data.serial_number || '-'}`
                        : (data.serial_number || `#${data.id}`);
                    $('#edit_id').val(data.id);
                    $('#edit_tipe_item').val(data.tipe_item);
                    $('#edit_item_label').val(label);
                    $('#edit_serial_number').val(data.serial_number || '');
                    $('#edit_item_number').val(data.item_number || '');
                    $('#edit_status').val(data.status);
                    $('#edit_storage_location').val(data.storage_location);
                    $('#edit_physical_condition').val(data.physical_condition);
                    $('#edit_completeness').val(data.completeness);
                    $('#edit_notes').val(data.notes || '');
                    $('#editAttachmentModal').modal('show');
                } else {
                OptimaNotify.error(response.message, 'Error!');
                }
            }
        });
    }

    // Functions for modal action buttons
    window.editCurrentAttachment = function() {
        if (currentAttachmentId) {
            $('#viewAttachmentModal').modal('hide');
            editAttachment(currentAttachmentId);
        }
    }

    window.deleteCurrentAttachment = function() {
        if (currentAttachmentId) {
            OptimaConfirm.danger({
                title: 'Delete Attachment',
                text: 'Are you sure you want to delete this attachment item?',
                icon: 'warning',
                confirmText: 'Yes, Delete',
                cancelText: window.lang('cancel'),
                confirmButtonColor: '#dc3545',
                onConfirm: function() {
                    $.ajax({
                        url: `<?= base_url('warehouse/inventory/attachments/delete/') ?>${currentAttachmentId}`,
                        type: 'DELETE',
                        data: { [csrfName]: csrfToken },
                        dataType: 'json',
                        success: function(response) {
                            updateCsrfToken(response);
                            if (response.success) {
                                $('#viewAttachmentModal').modal('hide');
                                attachmentTable.ajax.reload();
                                OptimaNotify.success(response.message, 'Berhasil!');
                            } else {
                                OptimaNotify.error(response.message, 'Gagal!');
                            }
                        },
                        error: function() {
                            OptimaNotify.error('Cannot connect to the server.', 'Error!');
                        }
                    });
                }
            });
        }
    }

    // ===================================
    // ATTACH / DETACH / SWAP FUNCTIONS
    // ===================================
    
    let currentAttachmentData = null;
    
    // Open Attach to Unit Modal
    window.openAttachModal = function() {
        if (!currentAttachmentId) return;
        
        // Get current attachment data
        $.ajax({
            url: `<?= base_url('warehouse/inventory/attachments/detail/') ?>${currentAttachmentId}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    currentAttachmentData = response.data;
                    
                    // Set form data
                    $('#attach_attachment_id').val(currentAttachmentData.id_inventory_attachment);
                    $('#attach_attachment_type').val(currentAttachmentData.tipe_item);
                    
                    // Set label
                    const label = getAttachmentLabel(currentAttachmentData);
                    $('#attach_item_label').html(`<strong>${label}</strong>`);
                    
                    // Load available units with attachment type
                    loadAvailableUnits('#attach_unit_id', currentAttachmentData.tipe_item);
                    
                    // Show modal
                    $('#viewAttachmentModal').modal('hide');
                    $('#attachToUnitModal').modal('show');
                }
            }
        });
    }
    
    // Open Swap Unit Modal
    window.openSwapModal = function() {
        if (!currentAttachmentId) return;
        
        // Get current attachment data
        $.ajax({
            url: `<?= base_url('warehouse/inventory/attachments/detail/') ?>${currentAttachmentId}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    currentAttachmentData = response.data;
                    
                    // Set form data
                    $('#swap_attachment_id').val(currentAttachmentData.id_inventory_attachment);
                    $('#swap_from_unit_id').val(currentAttachmentData.id_inventory_unit);
                    
                    // Set labels
                    const label = getAttachmentLabel(currentAttachmentData);
                    $('#swap_item_label').html(`<strong>${label}</strong>`);
                    $('#swap_from_unit_label').text(`Unit ${currentAttachmentData.no_unit || currentAttachmentData.id_inventory_unit}`);
                    
                    // Load available units with attachment type
                    loadAvailableUnits('#swap_to_unit_id', currentAttachmentData.tipe_item);
                    
                    // Reset form
                    $('#swap_reason_select').val('');
                    $('#swap_custom_reason').val('');
                    $('#swap_custom_reason_group').hide();
                    
                    // Show modal
                    $('#viewAttachmentModal').modal('hide');
                    $('#swapUnitModal').modal('show');
                }
            }
        });
    }
    
    // Open Detach from Unit Modal
    window.openDetachModal = function() {
        if (!currentAttachmentId) return;
        
        // Get current attachment data
        $.ajax({
            url: `<?= base_url('warehouse/inventory/attachments/detail/') ?>${currentAttachmentId}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    currentAttachmentData = response.data;
                    
                    // Set form data
                    $('#detach_attachment_id').val(currentAttachmentData.id_inventory_attachment);
                    $('#detach_from_unit_id').val(currentAttachmentData.id_inventory_unit);
                    
                    // Set labels
                    const label = getAttachmentLabel(currentAttachmentData);
                    $('#detach_item_label').html(`<strong>${label}</strong>`);
                    $('#detach_from_unit_label').text(`Unit ${currentAttachmentData.no_unit || currentAttachmentData.id_inventory_unit}`);
                    
                    // Reset form
                    $('#detach_reason_select').val('');
                    $('#detach_custom_reason').val('');
                    $('#detach_custom_reason_group').hide();
                    $('#detach_new_location').val('Workshop');
                    
                    // Show modal
                    $('#viewAttachmentModal').modal('hide');
                    $('#detachFromUnitModal').modal('show');
                }
            }
        });
    }
    
    // Helper function to get attachment label
    function getAttachmentLabel(data) {
        if (data.tipe_item === 'attachment') {
            return `Attachment ${data.attachment_merk || ''} ${data.attachment_tipe || ''} (SN: ${data.sn_attachment || '-'})`;
        } else if (data.tipe_item === 'battery') {
            return `Battery ${data.merk_baterai || ''} ${data.tipe_baterai || ''} (SN: ${data.sn_baterai || '-'})`;
        } else if (data.tipe_item === 'charger') {
            return `Charger ${data.merk_charger || ''} ${data.tipe_charger || ''} (SN: ${data.sn_charger || '-'})`;
        } else if (data.tipe_item === 'fork') {
            return `Fork ${data.fork_spec_name || data.fork_name || ''} (${data.item_number || '-'})`;
        }
        return 'Item';
    }
    
    // Load available units for dropdown with Select2
    function loadAvailableUnits(targetSelector, attachmentType = null) {
        $.ajax({
            url: '<?= base_url('warehouse/inventory/attachments/available-units') ?>',
            type: 'GET',
            dataType: 'json',
            data: { 
                attachment_type: attachmentType 
            },
            success: function(response) {
                if (response.success) {
                    const select = $(targetSelector);
                    const unitList = response.units || [];
                    if (select.hasClass('select2-hidden-accessible')) {
                        try {
                            select.select2('destroy');
                        } catch (e) { /* ignore */ }
                    }
                    select.empty();
                    select.append('<option value="">Select Unit...</option>');
                    
                    const Ou = window.OptimaUnitSelect2;
                    const useOu = typeof Ou !== 'undefined' && typeof Ou.optionDataAttributes === 'function';
                    unitList.forEach(unit => {
                        const row = {
                            id: unit.id_inventory_unit,
                            id_inventory_unit: unit.id_inventory_unit,
                            no_unit: unit.no_unit,
                            serial_number: unit.serial_number || '',
                            merk: '',
                            model_unit: unit.model_unit || '',
                            status: unit.status_unit_name || '',
                            lokasi: 'N/A'
                        };
                        var $opt;
                        if (useOu) {
                            const attrs = Ou.optionDataAttributes(row);
                            $opt = $('<option></option>').val(unit.id_inventory_unit).text(Ou.line1FromRow(Ou.normalizeRow(row)));
                            Object.keys(attrs).forEach(function (k) {
                                const v = attrs[k];
                                if (v !== '' && v != null && v !== false) {
                                    $opt.attr(k, v);
                                }
                            });
                        } else {
                            $opt = $('<option></option>')
                                .val(unit.id_inventory_unit)
                                .text(`Unit ${unit.no_unit} - ${unit.model_unit || ''} (${unit.status_unit_name || ''})`);
                        }
                        $opt.attr('data-has-attachment', (unit.has_attachment || 0) ? '1' : '0')
                            .attr('data-has-battery', (unit.has_battery || 0) ? '1' : '0')
                            .attr('data-has-charger', (unit.has_charger || 0) ? '1' : '0')
                            .attr('data-has-fork', (unit.has_fork || 0) ? '1' : '0');
                        select.append($opt);
                    });
                    
                    const s2 = {
                        theme: 'bootstrap-5',
                        placeholder: 'Search for a unit...',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: select.closest('.modal')
                    };
                    if (useOu) {
                        s2.templateResult = function (item) { return Ou.templateResult(item, {}); };
                        s2.templateSelection = function (item) { return Ou.templateSelection(item, {}); };
                    }
                    try {
                        select.select2(s2);
                    } catch (e) {
                        console.error('Select2 init (attach unit):', e);
                    }
                    
                    select.off('change.optimaUnitAttach').on('change.optimaUnitAttach', function () {
                        checkExistingAttachment($(this));
                    });
                } else {
                    if (window.OptimaNotify) {
                        OptimaNotify.error('Failed to load unit list', 'Error');
                    }
                }
            },
            error: function() {
                if (window.OptimaNotify) {
                    OptimaNotify.error('Unable to connect to the server', 'Error');
                }
            }
        });
    }
    
    // Check if selected unit has existing attachment
    function checkExistingAttachment(selectElement) {
        const selectedOption = selectElement.find('option:selected');
        const modalId = selectElement.closest('.modal').attr('id');
        
        let warningDiv, typeSpan, itemType;
        
        if (modalId === 'attachToUnitModal') {
            warningDiv = $('#attach_existing_warning');
            typeSpan = $('#attach_existing_type');
            itemType = $('#attach_attachment_type').val();
        } else if (modalId === 'swapUnitModal') {
            warningDiv = $('#swap_existing_warning');
            typeSpan = $('#swap_existing_type');
            itemType = currentAttachmentData?.tipe_item;
        }
        
        if (!warningDiv || !itemType) return;
        
        // Check if unit has existing item of same type
        let hasExisting = false;
        let existingTypeName = '';
        
        if (itemType === 'attachment' && selectedOption.attr('data-has-attachment') === '1') {
            hasExisting = true;
            existingTypeName = 'Attachment';
        } else if (itemType === 'battery' && selectedOption.attr('data-has-battery') === '1') {
            hasExisting = true;
            existingTypeName = 'Battery';
        } else if (itemType === 'charger' && selectedOption.attr('data-has-charger') === '1') {
            hasExisting = true;
            existingTypeName = 'Charger';
        } else if (itemType === 'fork' && selectedOption.attr('data-has-fork') === '1') {
            hasExisting = true;
            existingTypeName = 'Fork';
        }
        
        if (hasExisting) {
            typeSpan.text(existingTypeName);
            warningDiv.show();
        } else {
            warningDiv.hide();
        }
    }
    
    // Toggle custom reason input
    window.toggleSwapReasonInput = function(value) {
        if (value === 'custom') {
            $('#swap_custom_reason_group').show();
            $('#swap_custom_reason').prop('required', true);
        } else {
            $('#swap_custom_reason_group').hide();
            $('#swap_custom_reason').prop('required', false);
        }
    }
    
    window.toggleDetachReasonInput = function(value) {
        if (value === 'custom') {
            $('#detach_custom_reason_group').show();
            $('#detach_custom_reason').prop('required', true);
        } else {
            $('#detach_custom_reason_group').hide();
            $('#detach_custom_reason').prop('required', false);
        }
    }
    
    // Form submissions
    $('#attachToUnitForm').on('submit', function(e) {
        e.preventDefault();
        
        const data = {
            attachment_id: $('#attach_attachment_id').val(),
            unit_id: $('#attach_unit_id').val(),
            notes: $('#attach_notes').val(),
            [csrfName]: csrfToken
        };

        $.ajax({
            url: '<?= base_url('warehouse/inventory/attachments/attach') ?>',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                updateCsrfToken(response);
                if (response.success) {
                    $('#attachToUnitModal').modal('hide');
                    attachmentTable.ajax.reload();
                    OptimaNotify.success(response.message, 'Successful');
                } else {
                    OptimaNotify.error(response.message, 'Failed!');
                }
            },
            error: function() {
                OptimaNotify.error('Cannot connect to the server', 'Error!');
            }
        });
    });
    
    $('#swapUnitForm').on('submit', function(e) {
        e.preventDefault();
        
        const reasonSelect = $('#swap_reason_select').val();
        const reason = reasonSelect === 'custom' ? $('#swap_custom_reason').val() : reasonSelect;
        
        if (!reason) {
            OptimaNotify.error('Select or enter a reason for swapping units', 'Error');
            return;
        }
        
        const data = {
            attachment_id: $('#swap_attachment_id').val(),
            from_unit_id: $('#swap_from_unit_id').val(),
            to_unit_id: $('#swap_to_unit_id').val(),
            reason: reason,
            [csrfName]: csrfToken
        };

        $.ajax({
            url: '<?= base_url('warehouse/inventory/attachments/swap') ?>',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                updateCsrfToken(response);
                if (response.success) {
                    $('#swapUnitModal').modal('hide');
                    attachmentTable.ajax.reload();
                    OptimaNotify.success(response.message, 'Berhasil!');
                } else {
                    OptimaNotify.error(response.message, 'Gagal!');
                }
            },
            error: function() {
                OptimaNotify.error('Cannot connect to the server', 'Error!');
            }
        });
    });
    
    $('#detachFromUnitForm').on('submit', function(e) {
        e.preventDefault();
        
        const reasonSelect = $('#detach_reason_select').val();
        const reason = reasonSelect === 'custom' ? $('#detach_custom_reason').val() : reasonSelect;
        
        if (!reason) {
            OptimaNotify.error('Select or enter a reason for detaching from unit', 'Error');
            return;
        }
        
        const data = {
            attachment_id: $('#detach_attachment_id').val(),
            reason: reason,
            new_location: $('#detach_new_location').val(),
            [csrfName]: csrfToken
        };

        $.ajax({
            url: '<?= base_url('warehouse/inventory/attachments/detach') ?>',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                updateCsrfToken(response);
                if (response.success) {
                    $('#detachFromUnitModal').modal('hide');
                    attachmentTable.ajax.reload();
                    OptimaNotify.success(response.message, 'Berhasil!');
                } else {
                    OptimaNotify.error(response.message, 'Gagal!');
                }
            },
            error: function() {
                OptimaNotify.error('Cannot connect to the server', 'Error!');
            }
        });
    });

    // Open Add Item Modal
    window.openAddItemModal = function(type) {
        console.log(`📝 Opening add ${type} modal`);
        
        // Set the type in hidden field
        $('#new-tipe-item').val(type);
        
        // Show/hide appropriate fields based on type
        if (type === 'attachment') {
            $('#attachment-fields').show();
            $('#fork-fields').hide();
            $('#addItemModal .modal-title').html('<i class="fas fa-plus-circle me-2"></i>Add New Attachment');
            // Load attachment master data
            loadMasterData('attachment', '#new-attachment-id');
        } else if (type === 'fork') {
            $('#attachment-fields').hide();
            $('#fork-fields').show();
            $('#addItemModal .modal-title').html('<i class="fas fa-plus-circle me-2"></i>Add New Fork Stock');
            loadMasterData('fork', '#new-fork-id');
        }
        
        // Reset form
        $('#addItemForm')[0].reset();
        $('#new-tipe-item').val(type);
        
        // Load available units (lazy load)
        loadAvailableUnitsForAdd();
        
        // Show modal
        $('#addItemModal').modal('show');
    }

    // Load master data for dropdown in modal
    function loadMasterData(type, selectElement) {
        let url = '';
        switch(type) {
            case 'attachment':
                url = '<?= base_url('warehouse/inventory/attachments/master/attachment') ?>';
                break;
            case 'baterai':
                url = '<?= base_url('warehouse/inventory/attachments/master/baterai') ?>';
                break;
            case 'charger':
                url = '<?= base_url('warehouse/inventory/attachments/master/charger') ?>';
                break;
            case 'fork':
                url = '<?= base_url('warehouse/inventory/attachments/master/fork') ?>';
                break;
        }
        
        if (url) {
            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        const $select = $(selectElement);
                        $select.empty().append('<option value="">Pilih ' + type.charAt(0).toUpperCase() + type.slice(1) + '</option>');
                        
                        response.data.forEach(function(item) {
                            $select.append(`<option value="${item.id}">${item.text}</option>`);
                        });
                        
                        console.log(`✅ ${type} master data loaded: ${response.data.length} items`);
                    } else {
                        console.error(`❌ Failed to load ${type} master data:`, response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(`❌ Error loading ${type} master data:`, error);
                }
            });
        }
    }

    // Load units data
    function loadUnitsData() {
        $.ajax({
            url: '<?= base_url('warehouse/inventory/attachments/units') ?>',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    const $select = $('#new-unit-id');
                    if ($select.hasClass('select2-hidden-accessible')) {
                        try {
                            $select.select2('destroy');
                        } catch (e) { /* ignore */ }
                    }
                    $select.empty().append('<option value="">Select Unit (Optional)</option>');
                    const Ou = window.OptimaUnitSelect2;
                    const useOu = typeof Ou !== 'undefined' && typeof Ou.optionDataAttributes === 'function';
                    response.data.forEach(function (unit) {
                        const row = {
                            id: unit.id,
                            id_inventory_unit: unit.id,
                            no_unit: unit.nomor_unit,
                            serial_number: unit.serial_number || '',
                            merk: unit.merk || '',
                            model_unit: unit.model || '',
                            jenis: unit.jenis || '',
                            kapasitas: unit.kapasitas || '',
                            status: unit.status || '',
                            lokasi: unit.lokasi || ''
                        };
                        if (useOu) {
                            const attrs = Ou.optionDataAttributes(row);
                            const label = Ou.line1FromRow(Ou.normalizeRow(row));
                            const $opt = $('<option></option>').val(unit.id).text(label);
                            Object.keys(attrs).forEach(function (k) {
                                const v = attrs[k];
                                if (v !== '' && v != null && v !== false) {
                                    $opt.attr(k, v);
                                }
                            });
                            $select.append($opt);
                        } else {
                            $select.append($('<option></option>').val(unit.id).text(
                                (unit.nomor_unit || '') + ' - ' + (unit.merk || '') + ' ' + (unit.model || '')
                            ));
                        }
                    });
                    if ($.fn.select2 && useOu) {
                        $select.select2({
                            theme: 'bootstrap-5',
                            placeholder: 'Select Unit (Optional)',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#addItemModal'),
                            templateResult: function (i) { return Ou.templateResult(i, {}); },
                            templateSelection: function (i) { return Ou.templateSelection(i, {}); },
                            escapeMarkup: function (m) { return m; }
                        });
                    }
                    console.log(`✅ Units data loaded: ${response.data.length} items`);
                }
            },
            error: function(xhr, status, error) {
                console.error(`❌ Error loading units data:`, error);
            }
        });
    }

    // Lazy load units specifically for Add Item modal
    function loadAvailableUnitsForAdd() {
        const $select = $('#new-unit-id');
        if ($select.hasClass('select2-hidden-accessible')) {
            try { $select.select2('destroy'); } catch(e) {}
        }
        $select.empty().append('<option value="">Pilih unit jika sudah terpasang...</option>');

        $.ajax({
            url: '<?= base_url('warehouse/inventory/attachments/available-units') ?>',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const unitList = response.units || [];
                    const Ou = window.OptimaUnitSelect2;
                    const useOu = typeof Ou !== 'undefined' && typeof Ou.optionDataAttributes === 'function';

                    unitList.forEach(function(unit) {
                        const row = {
                            id: unit.id_inventory_unit,
                            id_inventory_unit: unit.id_inventory_unit,
                            no_unit: unit.no_unit,
                            serial_number: unit.serial_number || '',
                            merk: '',
                            model_unit: unit.model_unit || '',
                            status: unit.status_unit_name || '',
                            lokasi: ''
                        };
                        var $opt;
                        if (useOu) {
                            const attrs = Ou.optionDataAttributes(row);
                            const label = Ou.line1FromRow(Ou.normalizeRow(row));
                            $opt = $('<option></option>').val(unit.id_inventory_unit).text(label);
                            Object.keys(attrs).forEach(function(k) {
                                const v = attrs[k];
                                if (v !== '' && v != null && v !== false) $opt.attr(k, v);
                            });
                        } else {
                            $opt = $('<option></option>').val(unit.id_inventory_unit)
                                .text((unit.no_unit || '') + ' - ' + (unit.model_unit || '') + ' (' + (unit.status_unit_name || '') + ')');
                        }
                        $opt.attr('data-has-battery', (unit.has_battery || 0) ? '1' : '0')
                            .attr('data-has-charger', (unit.has_charger || 0) ? '1' : '0');
                        $select.append($opt);
                    });

                    const s2cfg = {
                        theme: 'bootstrap-5',
                        placeholder: 'Pilih unit jika sudah terpasang...',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#addItemModal')
                    };
                    if (useOu) {
                        s2cfg.templateResult    = function(i) { return Ou.templateResult(i, {}); };
                        s2cfg.templateSelection = function(i) { return Ou.templateSelection(i, {}); };
                        s2cfg.escapeMarkup      = function(m) { return m; };
                    }
                    try { $select.select2(s2cfg); } catch(e) { console.error('Select2 unit (add):', e); }
                }
            }
        });
    }

    // Battery/Charger cascade functions moved to:
    //   /warehouse/inventory/batteries  - Battery Inventory
    //   /warehouse/inventory/chargers   - Charger Inventory

    // Save new item
    $('#btn-save-item').on('click', function() {
        const formData = new FormData($('#addItemForm')[0]);
        const type = $('#new-tipe-item').val();
        
        console.log('🔧 Debug Add Item:');
        console.log('Type from form:', type);
        console.log('Form data entries:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
        
        // Validate required fields
        let isValid = true;
        let errorMessage = '';
        
        if (type === 'attachment') {
            if (!$('#new-attachment-id').val()) {
                isValid = false;
                errorMessage = 'Attachment Type is required';
            } else if (!$('#new-sn-attachment').val()) {
                isValid = false;
                errorMessage = 'Serial Number is required';
            }
        } else if (type === 'fork') {
            if (!$('#new-fork-id').val()) {
                isValid = false;
                errorMessage = 'Fork Spec is required';
            } else if (!$('#new-fork-qty-pairs').val() || parseInt($('#new-fork-qty-pairs').val(), 10) < 1) {
                isValid = false;
                errorMessage = 'Qty Pairs minimal 1';
            }
        }
        
        if (!isValid) {
            OptimaNotify.warning(errorMessage, 'Validation Error');
            return;
        }
        
        // Add CSRF token
        formData.append(csrfName, csrfToken);
        
        $.ajax({
            url: '<?= base_url('warehouse/inventory/attachments/add') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#btn-save-item').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Saving...');
            },
            success: function(response) {
                updateCsrfToken(response);
                $('#btn-save-item').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save Item');
                
                if (response.success) {
                    $('#addItemModal').modal('hide');
                    OptimaNotify.success(response.message || 'Item has been added successfully!', 'Success!');
                    
                    // Reload the table
                    attachmentTable.ajax.reload();
                    
                } else {
                    OptimaNotify.error(response.message || 'Failed to add item', 'Failed!');
                }
            },
            error: function(xhr, status, error) {
                $('#btn-save-item').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save Item');
                console.error('Error adding item:', error);
                OptimaNotify.error('An error occurred while adding the item', 'Error!');
            }
        });
    });

    // ── Attachment History ──────────────────────────────────────────────
    let attachmentHistoryLoaded = null;

    // Cached history data for filter/group-by re-render without re-fetch
    var _cachedTimeline = [];
    var _historyLoadedForId = null;

    function loadAttachmentHistory(attachmentId) {
        if (!attachmentId) return;
        // If same item & already cached, just re-render with current filters
        if (_historyLoadedForId === attachmentId && _cachedTimeline.length >= 0) {
            applyAttHistoryFilter();
            return;
        }

        $('#attachmentHistoryContent').html(`
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                <p class="mt-2 text-muted">Memuat history...</p>
            </div>
        `);

        $.ajax({
            url: `<?= base_url('warehouse/inventory/attachments/history/') ?>${attachmentId}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    attachmentHistoryLoaded = attachmentId;
                    _historyLoadedForId = attachmentId;
                    _cachedTimeline = response.timeline || [];
                    const total = _cachedTimeline.length;
                    if (total > 0) {
                        $('#attHistoryBadge').text(total).show();
                    }
                    applyAttHistoryFilter();
                } else {
                    $('#attachmentHistoryContent').html(`
                        <div class="alert alert-danger m-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>${response.message || 'Gagal memuat history.'}
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                $('#attachmentHistoryContent').html(`
                    <div class="alert alert-danger m-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Terjadi kesalahan saat memuat history.
                        <br><small class="text-muted">${xhr.statusText}</small>
                    </div>
                `);
            }
        });
    }

    function applyAttHistoryFilter() {
        const filterVal = $('#attHistoryFilter').val() || 'all';
        const groupBy   = $('#attHistoryGroup').val() || 'date';

        let items = _cachedTimeline.slice();

        // Filter
        if (filterVal !== 'all') {
            items = items.filter(i => (i.source || 'log') === filterVal || (i.type || '') === filterVal);
        }

        $('#attachmentHistoryContent').html(renderAttachmentTimeline(items, groupBy));
    }

    function resetAttachmentHistory(attachmentId) {
        _historyLoadedForId = null;
        _cachedTimeline = [];
        attachmentHistoryLoaded = false;
        $('#attHistoryBadge').hide().text('');
        loadAttachmentHistory(attachmentId || currentAttachmentId);
    }

    function renderAttachmentTimeline(timeline, groupBy) {
        groupBy = groupBy || 'date';
        if (!timeline || timeline.length === 0) {
            return `
                <div class="text-center text-muted py-5">
                    <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                    <h6>Belum Ada Aktivitas</h6>
                    <p class="small">Tidak ada aktivitas yang cocok dengan filter ini.</p>
                </div>
            `;
        }

        const colorHexMap = {
            primary:'#0d6efd',success:'#198754',warning:'#ffc107',
            info:'#0dcaf0',secondary:'#6c757d',dark:'#212529',danger:'#dc3545',
            purple:'#6f42c1',orange:'#fd7e14',cyan:'#0dcaf0'
        };
        const h = (s) => {
            if (s===null||s===undefined) return '-';
            return String(s).replace(/</g,'&lt;').replace(/>/g,'&gt;');
        };
        const formatDate = (d) => {
            if (!d) return '-';
            try { return new Date(d).toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}); }
            catch(e){ return d; }
        };
        const formatDateShort = (d) => {
            if (!d) return '-';
            try { return new Date(d).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}); }
            catch(e){ return d; }
        };
        const srcBadgeFn = (src) => {
            if (src==='seed') return `<span class="badge badge-soft-gray ms-1" style="font-size:0.65rem;">legacy</span>`;
            if (src==='movement') return `<span class="badge badge-soft-blue ms-1" style="font-size:0.65rem;"><i class="fas fa-truck me-1"></i>Surat Jalan</span>`;
            if (src==='audit_log') return `<span class="badge badge-soft-purple ms-1" style="font-size:0.65rem;"><i class="fas fa-clipboard-check me-1"></i>Audit</span>`;
            return '';
        };

        const buildItemHtml = (item) => {
            const color   = item.color || 'secondary';
            const hex     = colorHexMap[color] || '#6c757d';
            const icon    = item.icon || 'fas fa-circle';
            const title   = item.title || '';
            const desc    = item.description || item.subtitle || '';
            const user    = item.performed_by || item.user || null;
            const ref     = item.ref_number || null;
            const src     = item.source || 'log';
            const userHtml= user ? `<div class="text-muted" style="font-size:0.72rem;margin-top:2px;"><i class="fas fa-user me-1"></i>${h(user)}</div>` : '';
            const refHtml = ref ? `<span class="badge badge-soft-gray ms-1" style="font-size:0.65rem;"><i class="fas fa-hashtag me-1"></i>${h(ref)}</span>` : '';

            let detailsHtml = '';
            if (item.details && typeof item.details === 'object' && !Array.isArray(item.details)) {
                const rows = Object.entries(item.details)
                    .filter(([k,v]) => v!==null&&v!==undefined&&v!=='')
                    .map(([k,v]) => `<tr><td class="text-muted pe-2" style="white-space:nowrap;font-size:0.72rem;">${h(k)}</td><td style="font-size:0.72rem;">${h(v)}</td></tr>`)
                    .join('');
                if (rows) detailsHtml = `<table class="mt-1 w-100">${rows}</table>`;
            }

            return `<div class="timeline-item mb-2" style="position:relative;padding-left:2rem;">
                <div style="position:absolute;left:0;top:0.25rem;width:1.1rem;height:1.1rem;border-radius:50%;
                    background:${hex};display:flex;align-items:center;justify-content:center;z-index:1;box-shadow:0 0 0 2px #fff;">
                    <i class="${h(icon)} text-white" style="font-size:0.5rem;"></i>
                </div>
                <div class="card border-0 shadow-sm mb-0" style="border-left:3px solid ${hex} !important;">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-start justify-content-between flex-wrap gap-1">
                            <div><span class="fw-semibold small">${h(title)}</span>${srcBadgeFn(src)}${refHtml}</div>
                            <small class="text-muted text-nowrap"><i class="fas fa-calendar-alt me-1"></i>${h(formatDate(item.date))}</small>
                        </div>
                        ${desc ? `<div class="text-muted small mt-1">${h(desc)}</div>` : ''}
                        ${detailsHtml}
                        ${userHtml}
                    </div>
                </div>
            </div>`;
        };

        let html = `<div class="p-3">
            <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                <i class="fas fa-history text-primary me-2"></i>
                <span class="fw-semibold">Timeline Aktivitas</span>
                <span class="badge badge-soft-blue ms-2">${timeline.length} event</span>
            </div>`;

        if (groupBy === 'document') {
            // Group by reference_type + reference_number
            const groups = {};
            const order = [];
            timeline.forEach(item => {
                const key = (item.ref_type||item.source||'log') + '|' + (item.ref_number||item.reference_number||'—');
                if (!groups[key]) { groups[key] = []; order.push(key); }
                groups[key].push(item);
            });
            order.forEach(key => {
                const parts = key.split('|');
                const docType = parts[0]; const docNum = parts[1];
                const items = groups[key];
                const firstColor = colorHexMap[items[0].color||'secondary'] || '#6c757d';
                html += `
                <div class="border rounded mb-3" style="border-left:3px solid ${firstColor} !important;overflow:hidden;">
                    <div class="px-3 py-2 d-flex align-items-center gap-2" style="background:rgba(0,0,0,.03);border-bottom:1px solid #dee2e6;">
                        <i class="fas fa-file-alt text-muted" style="font-size:0.8rem;"></i>
                        <span class="fw-semibold small">${h(docType)}</span>
                        ${docNum!=='—'?`<span class="badge badge-soft-gray" style="font-size:0.65rem;">${h(docNum)}</span>`:''}
                        <span class="badge badge-soft-blue ms-auto" style="font-size:0.65rem;">${items.length} event</span>
                    </div>
                    <div class="p-3 position-relative" style="padding-left:1rem!important;">
                        <div style="position:absolute;left:1.5rem;top:0;bottom:0;width:2px;background:#dee2e6;border-radius:2px;"></div>
                        ${items.map(buildItemHtml).join('')}
                    </div>
                </div>`;
            });
        } else {
            // Group by date (default)
            const groups = {};
            const order = [];
            timeline.forEach(item => {
                const d = item.date ? item.date.substring(0,10) : '0000-00-00';
                if (!groups[d]) { groups[d] = []; order.push(d); }
                groups[d].push(item);
            });
            order.forEach(dateKey => {
                const items = groups[dateKey];
                const label = dateKey === '0000-00-00' ? 'Tanggal tidak diketahui' : formatDateShort(dateKey);
                html += `
                <div class="border rounded mb-3" style="overflow:hidden;">
                    <div class="px-3 py-2 d-flex align-items-center gap-2" style="background:rgba(0,0,0,.03);border-bottom:1px solid #dee2e6;">
                        <i class="fas fa-calendar-day text-muted" style="font-size:0.8rem;"></i>
                        <span class="fw-semibold small">${label}</span>
                        <span class="badge badge-soft-blue ms-auto" style="font-size:0.65rem;">${items.length} event</span>
                    </div>
                    <div class="p-3 position-relative" style="padding-left:1rem!important;">
                        <div style="position:absolute;left:1.5rem;top:0;bottom:0;width:2px;background:#dee2e6;border-radius:2px;"></div>
                        ${items.map(buildItemHtml).join('')}
                    </div>
                </div>`;
            });
        }

        html += `</div>`;
        return html;
    }

    function printAttachmentLabel() {
        // Print: open public page in new tab and trigger browser print
        const data = _currentAttachmentData;
        if (!data) { alert('Tidak ada data untuk dicetak.'); return; }
        if (data.public_url) {
            const win = window.open(data.public_url, '_blank');
            if (win) setTimeout(() => win.print(), 1200);
        } else {
            if (window.OptimaNotify) OptimaNotify.warning('QR code belum tersedia. Tunggu sebentar lalu coba lagi.', 'Info');
        }
    }

    async function downloadAttachmentLabel() {
        try {
            const data = _currentAttachmentData;
            if (!data) { alert('Tidak ada data.'); return; }

            const itemNo   = String(data.item_number || data.no_item || '-');
            const serial   = String(data.serial_number || data.sn_attachment || data.sn_baterai || data.sn_charger || data.sn_fork || '-');
            const tipe     = String((data.tipe_item||'item').charAt(0).toUpperCase()+(data.tipe_item||'item').slice(1));
            const merk     = String(data.merk || data.attachment_merk || data.merk_baterai || data.merk_charger || '');
            const model    = String(data.model || data.attachment_model || data.tipe_baterai || data.tipe_charger || data.fork_spec_name || '');
            const status   = String(data.status || data.attachment_status || '-');
            const unit     = String(data.no_unit || '-');
            const publicUrl = String(data.public_url || '');
            const qrUrl    = publicUrl ? 'https://api.qrserver.com/v1/create-qr-code/?size=320x320&data=' + encodeURIComponent(publicUrl) : '';
            const logoUrl  = '<?= base_url('assets/images/company-logo.svg') ?>';

            async function loadImageAsObjectUrl(url) {
                if (!url) return null;
                const res = await fetch(url);
                if (!res.ok) throw new Error('Failed to load image: ' + url);
                const blob = await res.blob();
                return URL.createObjectURL(blob);
            }
            async function loadImg(url) {
                return new Promise(function(resolve, reject) {
                    var img = new Image();
                    img.onload = function() { resolve(img); };
                    img.onerror = reject;
                    img.src = url;
                });
            }

            var canvas = document.createElement('canvas');
            canvas.width = 1500;
            canvas.height = 920;
            var ctx = canvas.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.strokeStyle = '#2c2f39';
            ctx.lineWidth = 5;
            ctx.strokeRect(10, 10, canvas.width - 20, canvas.height - 20);

            // Green accent wave background
            ctx.fillStyle = '#ebf9f0';
            ctx.beginPath();
            ctx.moveTo(0, 0);
            ctx.lineTo(canvas.width, 0);
            ctx.lineTo(canvas.width, 165);
            ctx.bezierCurveTo(canvas.width * 0.68, 112, canvas.width * 0.42, 235, 0, 160);
            ctx.closePath();
            ctx.fill();

            // Secondary accent
            ctx.fillStyle = '#d7f2e1';
            ctx.beginPath();
            ctx.moveTo(0, 155);
            ctx.bezierCurveTo(canvas.width * 0.30, 235, canvas.width * 0.66, 75, canvas.width, 142);
            ctx.lineTo(canvas.width, 205);
            ctx.bezierCurveTo(canvas.width * 0.70, 145, canvas.width * 0.35, 285, 0, 215);
            ctx.closePath();
            ctx.fill();

            // Left detail panel
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(48, 228, 885, 630);
            ctx.fillRect(952, 228, 500, 500);
            ctx.strokeStyle = '#dfe5eb';
            ctx.lineWidth = 2;
            ctx.strokeRect(48, 228, 885, 630);
            ctx.strokeRect(952, 228, 500, 500);

            ctx.fillStyle = '#20232a';
            ctx.font = '700 42px Arial';
            var y = 312;
            function drawRow(label, value) {
                ctx.font = '500 36px Arial';
                ctx.fillStyle = '#6b7280';
                ctx.fillText(label, 88, y);
                ctx.font = '600 40px Arial';
                ctx.fillStyle = '#20232a';
                ctx.fillText(value || '-', 310, y);
                y += 74;
            }
            drawRow('Item #:', itemNo);
            drawRow('Serial:', serial);
            drawRow('Tipe:', tipe);
            drawRow('Merk:', merk);
            drawRow('Model:', model);

            var tmpUrls = [];
            if (logoUrl) {
                try {
                    const logoObjTop = await loadImageAsObjectUrl(logoUrl);
                    tmpUrls.push(logoObjTop);
                    const logoImgTop = await loadImg(logoObjTop);
                    ctx.drawImage(logoImgTop, 78, 28, 300, 120);
                } catch (e) {}
            }
            if (qrUrl) {
                try {
                    const qrObj = await loadImageAsObjectUrl(qrUrl);
                    tmpUrls.push(qrObj);
                    const qrImg = await loadImg(qrObj);
                    ctx.drawImage(qrImg, 980, 256, 444, 444);
                } catch (e) {}
            }

            const dataUrl = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            link.href = dataUrl;
            link.download = 'barcode-label-' + itemNo.replace(/[^a-zA-Z0-9_-]/g, '_') + '.png';
            document.body.appendChild(link);
            link.click();
            link.remove();
            tmpUrls.forEach(function(u) { try { URL.revokeObjectURL(u); } catch (e) {} });
        } catch (err) {
            console.error(err);
            if (window.OptimaNotify) OptimaNotify.error('Gagal generate barcode label.');
        }
    }
</script>
<?= $this->endSection() ?>
