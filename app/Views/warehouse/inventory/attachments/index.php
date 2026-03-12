<?= $this->extend('layouts/base') ?>

<?php
/**
 * Attachment, Battery & Charger Inventory - Warehouse
 * BADGE/CARD: Optima badge-soft-* (tabs, status, condition); card-header bg-light; table mb-0.
 */
?>
<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-3">
    <h4 class="fw-bold mb-1">
        <i class="bi bi-puzzle me-2 text-primary"></i>
        Attachment, Battery & Charger Inventory
    </h4>
    <p class="text-muted mb-0">Manage forklift attachments, batteries, and chargers with status tracking and maintenance records</p>
</div>

    <!-- Inventory Table -->
    <div class="card table-card">
        <div class="card-header bg-light">
            <div class="row align-items-center mb-3">
                <div class="col">
                    <h5 class="card-title fw-bold m-0">List Attachment</h5>
                </div>
                <div class="col-auto">
                    <!-- Export Dropdown -->
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-file-export me-1"></i>Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= base_url('warehouse/inventory/attachments/export/attachment') ?>"><i class="fas fa-puzzle-piece me-2"></i>Attachment</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('warehouse/inventory/attachments/export/battery') ?>"><i class="fas fa-battery-half me-2"></i>Battery</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('warehouse/inventory/attachments/export/charger') ?>"><i class="fas fa-plug me-2"></i>Charger</a></li>
                        </ul>
                    </div>

                    <button type="button" class="btn btn-primary" id="btnTambahItem">
                        <i class="fas fa-plus me-1"></i>Add Item
                    </button>
                </div>
            </div>
            
            <!-- Main Type Tabs -->
            <ul class="nav nav-tabs mb-3" id="itemTypeTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="attachment-tab" data-bs-toggle="tab" data-bs-target="#attachment" type="button" role="tab" onclick="applyTypeFilter('attachment')">
                        <i class="fas fa-puzzle-piece me-1"></i>
                        <strong>Attachment</strong>
                        <span class="badge badge-soft-blue ms-1" id="count-attachment"><?= $detailed_stats['by_type']['attachment'] ?? 0 ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="battery-tab" data-bs-toggle="tab" data-bs-target="#battery" type="button" role="tab" onclick="applyTypeFilter('battery')">
                        <i class="fas fa-battery-half me-1"></i>
                        <strong>Battery</strong>
                        <span class="badge badge-soft-green ms-1" id="count-battery"><?= $detailed_stats['by_type']['battery'] ?? 0 ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="charger-tab" data-bs-toggle="tab" data-bs-target="#charger" type="button" role="tab" onclick="applyTypeFilter('charger')">
                        <i class="fas fa-plug me-1"></i>
                        <strong>Charger</strong>
                        <span class="badge badge-soft-yellow ms-1" id="count-charger"><?= $detailed_stats['by_type']['charger'] ?? 0 ?></span>
                    </button>
                </li>
            </ul>
            
            <!-- Status Sub-Tabs -->
            <ul class="nav nav-pills gap-2 mb-0" id="statusFilterTab" role="tablist">
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
                    <button class="nav-link btn-sm" id="used-status-tab" type="button" onclick="applyStatusFilter('USED')">
                        <i class="fas fa-link me-1"></i>
                        Used
                        <span class="badge badge-soft-cyan ms-1" id="count-used"><?= $detailed_stats['by_status']['used'] ?? 0 ?></span>
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
            
            <!-- Additional Filters (shown based on active tab) -->
            <div class="border-top pt-3 mt-3" id="additionalFilters" style="display: none;">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="chip chip-gray small">
                        <i class="fas fa-sliders-h me-1"></i>Models
                    </span>
                    <div class="btn-group btn-group-sm" role="group" id="modelFilterGroup">
                        <!-- Dynamic model buttons will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <table id="inventory-attachment-table" class="table table-striped table-hover mb-0">
                <thead id="table-header">
                    <!-- Dynamic header will be inserted here -->
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal View Attachment Detail -->
<div class="modal fade modal-wide" id="viewAttachmentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Detail Attachment</h5>
                <button type="button" class="btn-close btn-close-muted" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Tabs -->
                <ul class="nav nav-tabs px-3 pt-2" id="attachmentModalTabs" role="tablist">
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
                            <span class="badge badge-soft-gray ms-1" id="attHistoryBadge" style="display:none;"></span>
                        </button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active p-3" id="att-detail-pane" role="tabpanel">
                        <div id="attachmentDetailContent">
                            <!-- Content will be loaded here -->
                        </div>
                    </div>
                    <div class="tab-pane fade" id="att-history-pane" role="tabpanel">
                        <div id="attachmentHistoryContent">
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-history fa-2x mb-2 d-block"></i>
                                <p class="mb-0">Klik tab ini untuk memuat history.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="me-auto">
                    <!-- Action Buttons (Left side) -->
                    <button type="button" class="btn btn-success btn-sm" id="btnAttachToUnit" onclick="openAttachModal()" style="display:none;">
                        <i class="fas fa-link me-1"></i>Install to Unit
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" id="btnSwapUnit" onclick="openSwapModal()" style="display:none;">
                        <i class="fas fa-exchange-alt me-1"></i>Move to Another Unit
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" id="btnDetachFromUnit" onclick="openDetachModal()" style="display:none;">
                        <i class="fas fa-unlink me-1"></i>Detach from Unit
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
                    
                    <!-- Battery Fields -->
                    <div id="battery-fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Battery Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="new-baterai-id" name="baterai_id">
                                        <option value="">Select Battery Type</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Serial Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="new-sn-baterai" name="sn_baterai" placeholder="Enter battery SN">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Charger Fields -->
                    <div id="charger-fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Charger Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="new-charger-id" name="charger_id">
                                        <option value="">Select Charger Type</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Serial Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="new-sn-charger" name="sn_charger" placeholder="Enter charger SN">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Common Fields -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Unit</label>
                                <select class="form-select" id="new-unit-id" name="unit_id">
                                    <option value="">Select Unit (Optional)</option>
                                </select>
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
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js?v=<?= time() ?>"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js?v=<?= time() ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11?v=<?= time() ?>"></script>
<script>
    // Inventory Attachment & Battery Management - Updated <?= date('Y-m-d H:i:s') ?>
    // Global variables - Initialize with default values
    var currentTypeFilter = 'attachment';
    var currentStatusFilter = 'all';
    var currentModelFilter = '';
    var currentAttachmentId = null;
    var attachmentTable = null;


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
        } else if (type === 'battery') {
            headerHtml += '<th>Brand</th>';
            headerHtml += '<th>Type</th>';
            headerHtml += '<th>Models</th>';
        } else if (type === 'charger') {
            headerHtml += '<th>Brand</th>';
            headerHtml += '<th>Type</th>';
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
            // No Item column (always first) - Use no_item from database
            { 
                data: 'no_item',
                render: function(data, type, row) {
                    return data || '-';
                }
            },
            // Tipe Item column (always second)
            { 
                data: 'tipe_item',
                render: function(data, type, row) {
                    const typeMap = {
                        'attachment': '<i class="fas fa-puzzle-piece me-1 text-primary"></i>Attachment',
                        'battery': '<i class="fas fa-battery-half me-1 text-success"></i>Battery',
                        'charger': '<i class="fas fa-plug me-1 text-warning"></i>Charger'
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
        } else if (type === 'battery') {
            columns.push(
                { 
                    data: null,
                    render: function(data, type, row) {
                        return row.jenis_baterai || '-';
                    }
                },
                { 
                    data: null,
                    render: function(data, type, row) {
                        return row.merk_baterai || '-';
                    }
                },
                { 
                    data: null,
                    render: function(data, type, row) {
                        return row.tipe_baterai || '-';
                    }
                }
            );
        } else if (type === 'charger') {
            columns.push(
                { 
                    data: null,
                    render: function(data, type, row) {
                        return row.merk_charger || '-';
                    }
                },
                { 
                    data: null,
                    render: function(data, type, row) {
                        return row.tipe_charger || '-';
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
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                    console.log('Sending data to server:', d);
                },
                error: function(xhr, error, thrown) {
                    console.log('DataTables Ajax Error:');
                    console.log('XHR:', xhr);
                    console.log('Error:', error);
                    console.log('Thrown:', thrown);
                    console.log('Response Text:', xhr.responseText);
                    
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while loading data. Please check the console for details.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
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
                data: $(this).serialize() + '&<?= csrf_token() ?>=<?= csrf_hash() ?>',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#editAttachmentModal').modal('hide');
                        attachmentTable.ajax.reload(null, false);
                        Swal.fire('Berhasil!', response.message, 'success');
                    } else {
                        Swal.fire('Gagal!', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Cannot connect to the server.', 'error');
                }
            });
        });
    }

    window.applyTypeFilter = function(type) {
        console.log('Applying type filter:', type);
        
        // Remove active class from all tabs
        $('.nav-link').removeClass('active');
        
        // Add active class to clicked tab
        if (type === 'attachment') {
            $('#attachment-tab').addClass('active');
            $('.card-title').text('Attachment Stock List');
        } else if (type === 'battery') {
            $('#battery-tab').addClass('active');
            $('.card-title').text('Battery Stock List');
        } else if (type === 'charger') {
            $('#charger-tab').addClass('active');
            $('.card-title').text('Charger Stock List');
        }
        
        // Update current filter
        currentTypeFilter = type;
        currentStatusFilter = 'all'; // Reset status filter when type changes
        currentModelFilter = ''; // Reset model filter
        console.log('Current type filter set to:', currentTypeFilter);
        console.log('Status filter reset to:', currentStatusFilter);
        
        // Reset status tabs to 'All'
        $('#statusFilterTab .nav-link').removeClass('active');
        $('#all-status-tab').addClass('active');
        
        // Show/hide model filter based on type
        if (type === 'battery' || type === 'charger') {
            $('#additionalFilters').show();
            populateModelFilter(type);
        } else {
            $('#additionalFilters').hide();
        }
        
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
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                    console.log('Sending data to server:', d);
                    return d;
                },
                error: function(xhr, error, thrown) {
                    console.log('DataTables Ajax Error:', {xhr, error, thrown});
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while loading data.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
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
        } else if (status === 'USED') {
            $('#used-status-tab').addClass('active');
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

    window.viewAttachment = function(id) {
        console.log('viewAttachment called for ID:', id);
        currentAttachmentId = id; // Store current ID for edit/delete actions
        attachmentHistoryLoaded = false; // Reset so each attachment loads its own history
        currentHistoryAttachmentId = id; // Store for history tab click
        
        $.ajax({
            url: `<?= base_url('warehouse/inventory/attachments/detail/') ?>${id}`,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('#attachmentDetailContent').html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><br><br>Loading attachment details...</div>');
                $('#viewAttachmentModal').modal('show');
            },
            success: function(response) {
                console.log('AJAX Success Response:', response);
                
                if (response.success) {
                    const data = response.data;
                    const detailHtml = createAttachmentDetailHtml(data);
                    $('#attachmentDetailContent').html(detailHtml);
                    
                    // Show/hide action buttons based on attachment status
                    $('#btnAttachToUnit, #btnSwapUnit, #btnDetachFromUnit').hide();
                    
                    if (data.id_inventory_unit === null || data.id_inventory_unit === '' || data.id_inventory_unit === 0) {
                        // Item tidak terpasang → Show "Pasang ke Unit"
                        $('#btnAttachToUnit').show();
                    } else {
                        // Item terpasang → Show "Pindah Unit" & "Lepas dari Unit"
                        $('#btnSwapUnit').show();
                        $('#btnDetachFromUnit').show();
                    }
                } else {
                    const errorHtml = `
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle"></i> Failed to Load Details</h5>
                            <p>${response.message || 'An unknown error occurred'}</p>
                        </div>
                    `;
                    $('#attachmentDetailContent').html(errorHtml);
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', {xhr, status, error});
                console.log('Response Text:', xhr.responseText);
                
                let errorMessage = 'An error occurred while fetching attachment details.';
                
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMessage = errorResponse.message;
                    }
                } catch (e) {
                    errorMessage += ' (Server Error ' + xhr.status + ')';
                }
                
                const errorHtml = `
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-triangle"></i> Error ${xhr.status}</h5>
                        <p>${errorMessage}</p>
                        <details class="mt-2">
                            <summary>Technical Details</summary>
                            <pre class="mt-2 text-muted small">${xhr.responseText}</pre>
                        </details>
                    </div>
                `;
                $('#attachmentDetailContent').html(errorHtml);
            }
        });
    }

    function createAttachmentDetailHtml(data) {
        const h = (str) => {
            if (str === null || str === undefined || str === '') {
                return '-';
            }
            return String(str).replace(/</g, '&lt;').replace(/>/g, '&gt;');
        };
        
        console.log('Creating detail HTML for data:', data);
        
        return `

    <!-- ===== TAB NAVIGATION ===== -->
    <ul class="nav nav-tabs mb-3" id="attachmentDetailTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="att-detail-tab" data-bs-toggle="tab"
                data-bs-target="#att-detail-pane" type="button" role="tab">
                <i class="fas fa-info-circle me-1"></i>Detail
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="att-history-tab" data-bs-toggle="tab"
                data-bs-target="#att-history-pane" type="button" role="tab"
                onclick="loadAttachmentHistory(currentHistoryAttachmentId)">
                <i class="fas fa-history me-1"></i>History
            </button>
        </li>
    </ul>

    <div class="tab-content" id="attachmentDetailTabsContent">
        <!-- Detail Tab -->
        <div class="tab-pane fade show active" id="att-detail-pane" role="tabpanel">
            <div class="row">
                <!-- Basic Attachment Information -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-puzzle-piece me-2"></i><strong>Attachment Information</strong></h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr><td width="40%"><strong>ID Attachment</strong></td><td>: ${h(data.id_inventory_attachment)}</td></tr>
                                <tr><td><strong>Item Type</strong></td><td>: ${h(data.tipe_item)}</td></tr>
                                <tr><td><strong>SN Attachment</strong></td><td>: ${h(data.sn_attachment)}</td></tr>
                                <tr><td><strong>SN Battery</strong></td><td>: ${h(data.sn_baterai)}</td></tr>
                                <tr><td><strong>SN Charger</strong></td><td>: ${h(data.sn_charger)}</td></tr>
                                <tr><td><strong>Unit</strong></td><td>: ${h(data.no_unit)}</td></tr>
                                <tr><td><strong>Status</strong></td><td>: <span class="badge badge-soft-blue">${h(data.attachment_status)}</span></td></tr>
                                <tr><td><strong>Unit Status</strong></td><td>: ${h(data.status_unit_name)}</td></tr>
                                <tr><td><strong>Storage Location</strong></td><td>: ${h(data.lokasi_penyimpanan)}</td></tr>
                                <tr><td><strong>Physical Condition</strong></td><td>: <span class="badge ${data.kondisi_fisik === 'Baik' ? 'badge-soft-green' : data.kondisi_fisik === 'Rusak Berat' ? 'badge-soft-red' : 'badge-soft-yellow'}">${h(data.kondisi_fisik)}</span></td></tr>
                                <tr><td><strong>Completeness</strong></td><td>: <span class="badge ${data.kelengkapan === 'Lengkap' ? 'badge-soft-green' : 'badge-soft-yellow'}">${h(data.kelengkapan)}</span></td></tr>
                                <tr><td><strong>Entry Date</strong></td><td>: ${h(data.tanggal_masuk)}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Purchase Order Information -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-file-invoice me-2"></i><strong>Purchase Order Information</strong></h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr><td width="40%"><strong>PO Number</strong></td><td>: ${h(data.no_po) || 'Manual Entry'}</td></tr>
                                <tr><td><strong>PO Date</strong></td><td>: ${h(data.tanggal_po) || '-'}</td></tr>
                                <tr><td><strong>Supplier</strong></td><td>: ${h(data.nama_supplier) || '-'}</td></tr>
                                <tr><td><strong>PO Status</strong></td><td>: <span class="badge badge-soft-gray">${h(data.status) || '-'}</span></td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i><strong>Additional Information</strong></h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Created At:</strong> ${h(data.created_at)}</p>
                                    <p><strong>Updated At:</strong> ${h(data.updated_at)}</p>
                                </div>
                                <div class="col-md-6">
                                    ${data.catatan_inventory ? `
                                    <p><strong>Inventory Notes:</strong></p>
                                    <p class="text-muted">${h(data.catatan_inventory)}</p>
                                    ` : '<p class="text-muted">No additional notes</p>'}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Tab -->
        <div class="tab-pane fade" id="att-history-pane" role="tabpanel">
            <div id="attachmentHistoryContent">
                <div class="text-center p-4 text-muted">
                    <i class="fas fa-history fa-2x mb-2"></i>
                    <p>Click the <strong>History</strong> tab to load the timeline.</p>
                </div>
            </div>
        </div>
    </div>
    `;
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
                    $('#edit_status').val(data.status);
                    $('#edit_storage_location').val(data.storage_location);
                    $('#edit_physical_condition').val(data.physical_condition);
                    $('#edit_completeness').val(data.completeness);
                    $('#edit_notes').val(data.notes || '');
                    $('#editAttachmentModal').modal('show');
                } else {
                    Swal.fire('Error!', response.message, 'error');
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
            Swal.fire({
                title: 'Delete Attachment',
                text: 'Are you sure you want to delete this attachment item?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `<?= base_url('warehouse/inventory/attachments/delete/') ?>${currentAttachmentId}`,
                        type: 'DELETE',
                        data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                $('#viewAttachmentModal').modal('hide');
                                attachmentTable.ajax.reload();
                                Swal.fire('Berhasil!', response.message, 'success');
                            } else {
                                Swal.fire('Gagal!', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Cannot connect to the server.', 'error');
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
                    select.empty();
                    select.append('<option value="">Select Unit...</option>');
                    
                    response.units.forEach(unit => {
                        // Add data attributes for existing attachments check
                        const option = $('<option></option>')
                            .val(unit.id_inventory_unit)
                            .text(`Unit ${unit.no_unit} - ${unit.model_unit || ''} (${unit.status_unit_name || ''})`)
                            .data('hasAttachment', unit.has_attachment || false)
                            .data('hasBattery', unit.has_battery || false)
                            .data('hasCharger', unit.has_charger || false);
                        
                        select.append(option);
                    });
                    
                    // Initialize Select2 with search
                    if (!select.hasClass('select2-hidden-accessible')) {
                        select.select2({
                            theme: 'bootstrap-5',
                            placeholder: 'Search for a unit...',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: select.closest('.modal')
                        });
                    }
                    
                    // Add change event to show warning if unit has existing attachment
                    select.on('change', function() {
                        checkExistingAttachment($(this));
                    });
                } else {
                    Swal.fire('Error', 'Failed to load unit list', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Unable to connect to the server', 'error');
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
        
        if (itemType === 'attachment' && selectedOption.data('hasAttachment')) {
            hasExisting = true;
            existingTypeName = 'Attachment';
        } else if (itemType === 'battery' && selectedOption.data('hasBattery')) {
            hasExisting = true;
            existingTypeName = 'Battery';
        } else if (itemType === 'charger' && selectedOption.data('hasCharger')) {
            hasExisting = true;
            existingTypeName = 'Charger';
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
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        };
        
        $.ajax({
            url: '<?= base_url('warehouse/inventory/attachments/attach') ?>',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#attachToUnitModal').modal('hide');
                    attachmentTable.ajax.reload();
                    Swal.fire('Successful', response.message, 'success');
                } else {
                    Swal.fire('Failed!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Cannot connect to the server', 'error');
            }
        });
    });
    
    $('#swapUnitForm').on('submit', function(e) {
        e.preventDefault();
        
        const reasonSelect = $('#swap_reason_select').val();
        const reason = reasonSelect === 'custom' ? $('#swap_custom_reason').val() : reasonSelect;
        
        if (!reason) {
            Swal.fire('Error', 'Select or enter a reason for swapping units', 'error');
            return;
        }
        
        const data = {
            attachment_id: $('#swap_attachment_id').val(),
            from_unit_id: $('#swap_from_unit_id').val(),
            to_unit_id: $('#swap_to_unit_id').val(),
            reason: reason,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        };
        
        $.ajax({
            url: '<?= base_url('warehouse/inventory/attachments/swap') ?>',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#swapUnitModal').modal('hide');
                    attachmentTable.ajax.reload();
                    Swal.fire('Berhasil!', response.message, 'success');
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Cannot connect to the server', 'error');
            }
        });
    });
    
    $('#detachFromUnitForm').on('submit', function(e) {
        e.preventDefault();
        
        const reasonSelect = $('#detach_reason_select').val();
        const reason = reasonSelect === 'custom' ? $('#detach_custom_reason').val() : reasonSelect;
        
        if (!reason) {
            Swal.fire('Error', 'Select or enter a reason for detaching from unit', 'error');
            return;
        }
        
        const data = {
            attachment_id: $('#detach_attachment_id').val(),
            reason: reason,
            new_location: $('#detach_new_location').val(),
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        };
        
        $.ajax({
            url: '<?= base_url('warehouse/inventory/attachments/detach') ?>',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#detachFromUnitModal').modal('hide');
                    attachmentTable.ajax.reload();
                    Swal.fire('Berhasil!', response.message, 'success');
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Cannot connect to the server', 'error');
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
            $('#battery-fields').hide();
            $('#charger-fields').hide();
            $('#addItemModal .modal-title').html('<i class="fas fa-plus-circle me-2"></i>Add New Attachment');
            // Load attachment master data
            loadMasterData('attachment', '#new-attachment-id');
        } else if (type === 'battery') {
            $('#attachment-fields').hide();
            $('#battery-fields').show();
            $('#charger-fields').hide();
            $('#addItemModal .modal-title').html('<i class="fas fa-plus-circle me-2"></i>Add New Battery');
            // Load baterai master data
            loadMasterData('baterai', '#new-baterai-id');
        } else if (type === 'charger') {
            $('#attachment-fields').hide();
            $('#battery-fields').hide();
            $('#charger-fields').show();
            $('#addItemModal .modal-title').html('<i class="fas fa-plus-circle me-2"></i>Add New Charger');
            // Load charger master data
            loadMasterData('charger', '#new-charger-id');
        }
        
        // Reset form
        $('#addItemForm')[0].reset();
        $('#new-tipe-item').val(type);
        
        // Load units data
        loadUnitsData();
        
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
                    $select.empty().append('<option value="">Select Unit (Optional)</option>');
                    
                    response.data.forEach(function(unit) {
                        $select.append(`<option value="${unit.id}">${unit.nomor_unit} - ${unit.merk} ${unit.model}</option>`);
                    });
                    
                    console.log(`✅ Units data loaded: ${response.data.length} items`);
                }
            },
            error: function(xhr, status, error) {
                console.error(`❌ Error loading units data:`, error);
            }
        });
    }

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
        } else if (type === 'battery') {
            if (!$('#new-baterai-id').val()) {
                isValid = false;
                errorMessage = 'Battery Type is required';
            } else if (!$('#new-sn-baterai').val()) {
                isValid = false;
                errorMessage = 'Serial Number is required';
            }
        } else if (type === 'charger') {
            if (!$('#new-charger-id').val()) {
                isValid = false;
                errorMessage = 'Charger Type is required';
            } else if (!$('#new-sn-charger').val()) {
                isValid = false;
                errorMessage = 'Serial Number is required';
            }
        }
        
        if (!isValid) {
            Swal.fire('Validation Error', errorMessage, 'warning');
            return;
        }
        
        // Add CSRF token
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        
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
                $('#btn-save-item').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save Item');
                
                if (response.success) {
                    $('#addItemModal').modal('hide');
                    Swal.fire('Success!', response.message || 'Item has been added successfully!', 'success');
                    
                    // Reload the table
                    attachmentTable.ajax.reload();
                    
                } else {
                    Swal.fire('Failed!', response.message || 'Failed to add item', 'error');
                }
            },
            error: function(xhr, status, error) {
                $('#btn-save-item').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save Item');
                console.error('Error adding item:', error);
                Swal.fire('Error!', 'An error occurred while adding the item', 'error');
            }
        });
    });

    // ── Attachment History ──────────────────────────────────────────────
    let attachmentHistoryLoaded = null;

    function loadAttachmentHistory(attachmentId) {
        if (!attachmentId) return;
        if (attachmentHistoryLoaded === attachmentId) return; // already loaded

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
                    const total = response.total || 0;
                    if (total > 0) {
                        $('#attHistoryBadge').text(total).show();
                    }
                    $('#attachmentHistoryContent').html(renderAttachmentTimeline(response.timeline || []));
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

    function renderAttachmentTimeline(timeline) {
        if (!timeline || timeline.length === 0) {
            return `
                <div class="text-center text-muted py-5">
                    <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                    <h6>Belum Ada Aktivitas</h6>
                    <p class="small">Attachment ini belum memiliki history tercatat.</p>
                </div>
            `;
        }

        const colorHexMap = {
            primary: '#0d6efd', success: '#198754', warning: '#ffc107',
            info: '#0dcaf0', secondary: '#6c757d', dark: '#212529', danger: '#dc3545'
        };
        const formatDate = (d) => {
            if (!d) return '-';
            try { return new Date(d).toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}); }
            catch(e){ return d; }
        };
        const h = (s) => {
            if (s === null || s === undefined) return '-';
            return String(s).replace(/</g,'&lt;').replace(/>/g,'&gt;');
        };

        let html = `<div class="p-3">
            <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                <i class="fas fa-history text-primary me-2"></i>
                <span class="fw-semibold">Timeline Attachment</span>
                <span class="badge badge-soft-blue ms-2">${timeline.length} event</span>
            </div>
            <div style="position:relative; padding-left:2.5rem;">
                <div style="position:absolute;left:1rem;top:0;bottom:0;width:2px;background:#dee2e6;border-radius:2px;"></div>`;

        timeline.forEach(item => {
            const color    = item.color || 'secondary';
            const hex      = colorHexMap[color] || '#6c757d';
            const icon     = item.icon || 'fas fa-circle';
            const title    = item.title || '';
            const desc     = item.description || '';
            const user     = item.user || null;
            const ref      = item.ref_number || null;
            const src      = item.source || 'log';
            const srcBadge = src === 'seed' ? `<span class="chip chip-gray ms-1 text-xs">legacy</span>` : '';
            const userHtml = user ? `<div class="text-muted text-xxs" style="margin-top:2px;"><i class="fas fa-user me-1"></i>${h(user)}</div>` : '';
            const refHtml  = ref ? `<span class="chip chip-gray ms-1 text-xs"><i class="fas fa-hashtag me-1"></i>${h(ref)}</span>` : '';

            html += `<div class="timeline-item mb-3" style="position:relative;">
                <div style="position:absolute;left:-2.15rem;top:0.25rem;width:1.25rem;height:1.25rem;border-radius:50%;
                    background:${hex};display:flex;align-items:center;justify-content:center;z-index:1;box-shadow:0 0 0 3px #fff;">
                    <i class="${h(icon)} text-white text-2xs"></i>
                </div>
                <div class="card border-0 shadow-sm" style="border-left:3px solid ${hex} !important;">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-start justify-content-between flex-wrap gap-1">
                            <div><span class="fw-semibold small">${h(title)}</span>${srcBadge}${refHtml}</div>
                            <small class="text-muted text-nowrap"><i class="fas fa-calendar-alt me-1"></i>${h(formatDate(item.date))}</small>
                        </div>
                        ${desc ? `<div class="text-muted small mt-1">${h(desc)}</div>` : ''}
                        ${userHtml}
                    </div>
                </div>
            </div>`;
        });

        html += `</div></div>`;
        return html;
    }
</script>
<?= $this->endSection() ?>
