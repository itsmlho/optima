<?= $this->extend('layouts/base') ?>

<?php
/**
 * Battery & Charger Inventory - Warehouse
 * Combined page: tab Battery | tab Charger.
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
                    <i class="fas fa-battery-half me-2 text-success"></i>
                    Battery &amp; Charger Inventory
                </h4>
                <p class="text-muted mb-0">Kelola inventaris baterai dan charger forklift, status, dan riwayat pemasangan</p>
            </div>
            <div class="d-flex gap-2" id="header-btns-battery">
                <a href="<?= base_url('warehouse/inventory/attachments/export/battery') ?>" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i>Export Battery
                </a>
                <button type="button" class="btn btn-success btn-sm" id="btnAddBattery">
                    <i class="fas fa-plus me-1"></i>Add Battery
                </button>
            </div>
            <div class="d-flex gap-2 d-none" id="header-btns-charger">
                <a href="<?= base_url('warehouse/inventory/attachments/export/charger') ?>" class="btn btn-outline-warning btn-sm">
                    <i class="fas fa-file-excel me-1"></i>Export Charger
                </a>
                <button type="button" class="btn btn-warning btn-sm" id="btnAddCharger">
                    <i class="fas fa-plus me-1"></i>Add Charger
                </button>
            </div>
        </div>

        <!-- Type Tabs -->
        <ul class="nav nav-tabs mb-3" id="bcTypeTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="tab-battery" type="button" role="tab" onclick="switchBCTab('battery')">
                    <i class="fas fa-battery-half me-1 text-success"></i>Battery
                    <span class="badge badge-soft-blue ms-1"><?= ($bat_stats['total'] ?? 0) ?></span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="tab-charger" type="button" role="tab" onclick="switchBCTab('charger')">
                    <i class="fas fa-plug me-1 text-warning"></i>Charger
                    <span class="badge badge-soft-blue ms-1"><?= ($chg_stats['total'] ?? 0) ?></span>
                </button>
            </li>
        </ul>

        <div id="bcTypeTabContent">

        <!-- ═══ BATTERY PANE ═══ -->
        <div id="pane-battery">

            <!-- Chemistry Filter -->
            <div class="mb-2">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-muted small fw-medium"><i class="fas fa-flask me-1"></i>Battery Type:</span>
                    <div class="btn-group btn-group-sm" id="chemistryFilterGroup">
                        <button type="button" class="btn btn-outline-secondary active" data-chemistry="" onclick="applyChemistryFilter('')">All</button>
                        <button type="button" class="btn btn-outline-secondary" data-chemistry="lead_acid" onclick="applyChemistryFilter('lead_acid')"><i class="fas fa-car-battery me-1"></i>Lead Acid</button>
                        <button type="button" class="btn btn-outline-info" data-chemistry="lithium" onclick="applyChemistryFilter('lithium')"><i class="fas fa-bolt me-1"></i>Lithium-ion</button>
                    </div>
                </div>
            </div>

            <!-- Status Filter Tabs -->
            <ul class="nav nav-pills gap-2 mb-3" id="batStatusTab">
                <li class="nav-item"><button class="nav-link active btn-sm" onclick="applyBatStatusFilter('all', this)"><i class="fas fa-list me-1"></i>All <span class="badge badge-soft-gray ms-1"><?= $bat_stats['total'] ?? 0 ?></span></button></li>
                <li class="nav-item"><button class="nav-link btn-sm" onclick="applyBatStatusFilter('AVAILABLE', this)"><i class="fas fa-check-circle me-1"></i>Available <span class="badge badge-soft-green ms-1"><?= $bat_stats['available'] ?? 0 ?></span></button></li>
                <li class="nav-item"><button class="nav-link btn-sm" onclick="applyBatStatusFilter('IN_USE', this)"><i class="fas fa-link me-1"></i>In Use <span class="badge badge-soft-cyan ms-1"><?= $bat_stats['in_use'] ?? 0 ?></span></button></li>
                <li class="nav-item"><button class="nav-link btn-sm" onclick="applyBatStatusFilter('SPARE', this)"><i class="fas fa-box me-1"></i>Spare <span class="badge badge-soft-purple ms-1"><?= $bat_stats['spare'] ?? 0 ?></span></button></li>
                <li class="nav-item"><button class="nav-link btn-sm" onclick="applyBatStatusFilter('MAINTENANCE', this)"><i class="fas fa-tools me-1"></i>Maintenance <span class="badge badge-soft-yellow ms-1"><?= $bat_stats['maintenance'] ?? 0 ?></span></button></li>
                <li class="nav-item"><button class="nav-link btn-sm" onclick="applyBatStatusFilter('BROKEN', this)"><i class="fas fa-exclamation-triangle me-1"></i>Broken <span class="badge badge-soft-red ms-1"><?= $bat_stats['broken'] ?? 0 ?></span></button></li>
            </ul>

            <!-- Battery Table -->
            <table id="battery-table" class="table table-striped table-hover mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th>No Item</th><th>Tipe Kimia</th><th>Merk</th><th>Spesifikasi</th><th>SN</th><th>Kondisi</th><th>Status</th><th>Lokasi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- ═══ CHARGER PANE ═══ -->
        <div id="pane-charger" class="d-none">

            <!-- Status Filter Tabs -->
            <ul class="nav nav-pills gap-2 mb-3" id="chgStatusTab">
                <li class="nav-item"><button class="nav-link active btn-sm" onclick="applyChgStatusFilter('all', this)"><i class="fas fa-list me-1"></i>All <span class="badge badge-soft-gray ms-1"><?= $chg_stats['total'] ?? 0 ?></span></button></li>
                <li class="nav-item"><button class="nav-link btn-sm" onclick="applyChgStatusFilter('AVAILABLE', this)"><i class="fas fa-check-circle me-1"></i>Available <span class="badge badge-soft-green ms-1"><?= $chg_stats['available'] ?? 0 ?></span></button></li>
                <li class="nav-item"><button class="nav-link btn-sm" onclick="applyChgStatusFilter('IN_USE', this)"><i class="fas fa-link me-1"></i>In Use <span class="badge badge-soft-cyan ms-1"><?= $chg_stats['in_use'] ?? 0 ?></span></button></li>
                <li class="nav-item"><button class="nav-link btn-sm" onclick="applyChgStatusFilter('SPARE', this)"><i class="fas fa-box me-1"></i>Spare <span class="badge badge-soft-purple ms-1"><?= $chg_stats['spare'] ?? 0 ?></span></button></li>
                <li class="nav-item"><button class="nav-link btn-sm" onclick="applyChgStatusFilter('MAINTENANCE', this)"><i class="fas fa-tools me-1"></i>Maintenance <span class="badge badge-soft-yellow ms-1"><?= $chg_stats['maintenance'] ?? 0 ?></span></button></li>
                <li class="nav-item"><button class="nav-link btn-sm" onclick="applyChgStatusFilter('BROKEN', this)"><i class="fas fa-exclamation-triangle me-1"></i>Broken <span class="badge badge-soft-red ms-1"><?= $chg_stats['broken'] ?? 0 ?></span></button></li>
            </ul>

            <!-- Charger Table -->
            <table id="charger-table" class="table table-striped table-hover mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th>No Item</th><th>Merk</th><th>Tipe Charger</th><th>SN</th><th>Kondisi</th><th>Status</th><th>Lokasi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        </div><!-- /.tab-content -->

    </div><!-- /.card-body -->
</div><!-- /.card -->


<!-- ═══════════════════════════════════════════════════════════
     BATTERY MODALS
════════════════════════════════════════════════════════════ -->

<!-- Detail / History -->
<div class="modal fade" id="viewBatteryModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light py-2">
                <div>
                    <h5 class="modal-title mb-0"><i class="fas fa-battery-half me-2 text-success"></i>Detail Battery</h5>
                    <p class="text-muted small mb-0" id="viewBatteryModalSubtitle"></p>
                </div>
                <button type="button" class="btn-close btn-close-muted" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <ul class="nav nav-tabs px-3 pt-2 border-bottom" id="batteryModalTabs" role="tablist">
                    <li class="nav-item"><button class="nav-link active" id="bat-detail-tab" data-bs-toggle="tab" data-bs-target="#bat-detail-pane" type="button"><i class="fas fa-info-circle me-1"></i>Detail</button></li>
                    <li class="nav-item"><button class="nav-link" id="bat-history-tab" data-bs-toggle="tab" data-bs-target="#bat-history-pane" type="button" onclick="loadBatteryHistory(currentBatteryId)"><i class="fas fa-history me-1"></i>History <span class="badge badge-soft-blue ms-1" id="batHistoryBadge" style="display:none;"></span></button></li>
                </ul>
                <div class="tab-content" style="height:60vh;overflow-y:auto;">
                    <div class="tab-pane fade show active" id="bat-detail-pane" role="tabpanel">
                        <div class="row g-0">
                            <div class="col-lg-8 p-3 border-end">
                                <div id="batteryDetailContent">
                                    <div class="text-center p-5 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i>Loading...</div>
                                </div>
                            </div>
                            <div class="col-lg-4 p-3 bg-light" id="batteryDetailSidebar">
                                <div id="batteryQuickInfo"></div>
                                <div id="batteryQrSection">
                                    <div class="card shadow-sm mb-3">
                                        <div class="card-header bg-light d-flex align-items-center justify-content-between">
                                            <h6 class="mb-0"><i class="fas fa-qrcode me-2"></i><strong>Barcode Aset</strong></h6>
                                            <span class="badge bg-dark">Public</span>
                                        </div>
                                        <div class="card-body p-2 small" id="batteryQrBody">
                                            <div class="text-center py-3 text-muted">
                                                <i class="fas fa-spinner fa-spin fa-2x mb-2 d-block text-success"></i>
                                                <small>Memuat QR...</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="bat-history-pane" role="tabpanel">
                        <div class="d-flex align-items-center gap-2 px-3 pt-3 pb-2 border-bottom flex-wrap">
                            <span class="small fw-semibold text-muted me-1"><i class="fas fa-filter me-1"></i>Filter:</span>
                            <select id="batHistoryFilter" class="form-select form-select-sm" style="width:auto;" onchange="applyBatHistoryFilter()">
                                <option value="all">Semua</option><option value="assign">Dipasang</option><option value="detach">Dilepas</option><option value="audit">Audit/SPK/WO</option><option value="movement">Surat Jalan</option><option value="update">Update Data</option>
                            </select>
                            <button class="btn btn-sm btn-outline-secondary ms-auto" onclick="loadBatteryHistory(currentBatteryId, true)"><i class="fas fa-sync-alt"></i></button>
                        </div>
                        <div id="batteryHistoryContent" class="p-3"><div class="text-center text-muted py-4"><i class="fas fa-history fa-2x mb-2 d-block"></i><p class="mb-0">Klik tab ini untuk memuat history.</p></div></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="me-auto d-flex gap-2">
                    <button type="button" class="btn btn-success btn-sm" id="btnBatAttachToUnit" onclick="openBatAttachModal()" style="display:none;"><i class="fas fa-link me-1"></i>Install to Unit</button>
                    <button type="button" class="btn btn-warning btn-sm" id="btnBatSwapUnit" onclick="openBatSwapModal()" style="display:none;"><i class="fas fa-exchange-alt me-1"></i>Move Unit</button>
                    <button type="button" class="btn btn-warning btn-sm" id="btnBatDetachFromUnit" onclick="openBatDetachModal()" style="display:none;"><i class="fas fa-unlink me-1"></i>Detach</button>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="editCurrentBattery()"><i class="fas fa-edit me-1"></i>Edit</button>
                <button type="button" class="btn btn-danger" onclick="deleteCurrentBattery()"><i class="fas fa-trash me-1"></i>Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Battery -->
<div class="modal fade" id="editBatteryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light"><h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Battery</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="editBatteryForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id"><input type="hidden" id="edit_tipe_item" name="tipe_item" value="battery">
                    <div class="mb-3"><label class="form-label fw-semibold">Item Number / Serial Number</label><input type="text" class="form-control bg-light" id="edit_item_label" readonly></div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6"><label class="form-label fw-semibold">Serial Number (SN)</label><input type="text" class="form-control" id="edit_serial_number" name="serial_number"></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">No Item</label><input type="text" class="form-control text-uppercase" id="edit_item_number" name="item_number"></div>
                    </div>
                    <div class="mb-3"><label class="form-label fw-semibold">Status <span class="text-danger">*</span></label><select class="form-select" id="edit_status" name="status" required><option value="AVAILABLE">Available</option><option value="IN_USE">In Use</option><option value="SPARE">Spare</option><option value="MAINTENANCE">Maintenance</option><option value="BROKEN">Broken</option><option value="RESERVED">Reserved</option><option value="SOLD">Sold</option></select></div>
                    <div class="mb-3"><label class="form-label fw-semibold">Storage Location</label><select class="form-select" id="edit_storage_location" name="storage_location"><option value="Workshop">Workshop</option><option value="WAREHOUSE">Warehouse</option><option value="POS 1">POS 1</option><option value="POS 2">POS 2</option><option value="POS 3">POS 3</option><option value="POS 4">POS 4</option><option value="POS 5">POS 5</option></select></div>
                    <div class="mb-3"><label class="form-label fw-semibold">Physical Condition</label><select class="form-select" id="edit_physical_condition" name="physical_condition"><option value="GOOD">Good</option><option value="MINOR_DAMAGE">Minor Damage</option><option value="MAJOR_DAMAGE">Major Damage</option></select></div>
                    <div class="mb-3"><label class="form-label fw-semibold">Notes</label><textarea class="form-control" id="edit_notes" name="notes" rows="2"></textarea></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Changes</button></div>
            </form>
        </div>
    </div>
</div>

<!-- Add Battery -->
<div class="modal fade" id="addBatteryModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i class="fas fa-plus-circle me-2 text-success"></i>Add New Battery</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="alert alert-info"><h6 class="alert-heading mb-1"><i class="fas fa-info-circle me-1"></i>Tambah Data Battery</h6><p class="mb-0 small">Pilih tipe kimia terlebih dahulu untuk menentukan prefix nomor item (B = Lead Acid, BL = Lithium).</p></div>
                <form id="addBatteryForm">
                    <input type="hidden" name="tipe_item" value="battery">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tipe Baterai <span class="text-danger">*</span></label>
                            <select class="form-select" id="add-bat-tipe"><option value="">Pilih tipe...</option><option value="LEAD ACID">LEAD ACID</option><option value="LITHIUM">LITHIUM</option></select>
                            <div class="small mt-1" id="add-bat-prefix-badge"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Merk <span class="text-danger">*</span></label>
                            <select class="form-select" id="add-bat-merk" disabled><option value="">Pilih merk...</option></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Spesifikasi <span class="text-danger">*</span></label>
                            <select class="form-select" id="add-baterai-id" name="baterai_id" disabled><option value="">Pilih spesifikasi...</option></select>
                            <div class="small text-muted mt-1" id="add-battery-last-hint"></div>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Serial Number (SN) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add-sn-baterai" name="sn_baterai" placeholder="Cth: BA24001" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No Item <span class="text-muted small fw-normal">(opsional)</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control text-uppercase" id="add-item-number-battery" name="item_number_battery" placeholder="Cth: B02178" disabled>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="add-btn-gen-battery" onclick="addGenBatteryItemNumber()" disabled><i class="fas fa-magic"></i></button>
                            </div>
                            <div class="small text-muted mt-1" id="add-battery-item-hint">Kosongkan untuk otomatis saat simpan</div>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Unit <span class="text-muted small fw-normal">(opsional)</span></label>
                            <select class="form-select" id="add-bat-unit-id" name="unit_id"><option value="">Pilih unit jika sudah terpasang...</option></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Physical Condition</label>
                            <select class="form-select" id="add-bat-kondisi" name="physical_condition"><option value="GOOD">Good</option><option value="MINOR_DAMAGE">Minor Damage</option><option value="MAJOR_DAMAGE">Major Damage</option></select>
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">Storage Location <span class="text-danger">*</span></label>
                            <select class="form-select" id="add-bat-lokasi" name="storage_location" required>
                                <option value="">Pilih lokasi...</option>
                                <optgroup label="Workshop"><option value="Workshop" selected>Workshop</option><option value="POS 1">POS 1</option><option value="POS 2">POS 2</option><option value="POS 3">POS 3</option><option value="POS 4">POS 4</option><option value="POS 5">POS 5</option></optgroup>
                                <optgroup label="Lainnya"><option value="WAREHOUSE">WAREHOUSE</option></optgroup>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status"><option value="AVAILABLE">Available</option><option value="IN_USE">In Use</option><option value="SPARE">Spare</option><option value="MAINTENANCE">Maintenance</option><option value="BROKEN">Broken</option><option value="RESERVED">Reserved</option></select>
                        </div>
                    </div>
                    <div class="mb-3 mt-3"><label class="form-label">Notes</label><textarea class="form-control" name="catatan" rows="2" placeholder="Catatan tambahan..."></textarea></div>
                </form>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-success" id="btn-save-battery"><i class="fas fa-save me-1"></i>Save Battery</button></div>
        </div>
    </div>
</div>

<!-- Attach Battery to Unit -->
<div class="modal fade" id="batAttachToUnitModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header bg-light"><h5 class="modal-title"><i class="fas fa-link me-2"></i>Attach to Unit</h5><button type="button" class="btn-close btn-close-muted" data-bs-dismiss="modal"></button></div>
        <form id="batAttachToUnitForm"><div class="modal-body">
            <input type="hidden" id="bat_attach_id"><input type="hidden" id="bat_attach_type" value="battery">
            <div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Select a unit to attach <span id="bat_attach_item_label" class="fw-semibold"></span></div>
            <div class="mb-3"><label class="form-label">Unit <span class="text-danger">*</span></label><select class="form-select" id="bat_attach_unit_id" required><option value="">Select Unit...</option></select></div>
            <div id="bat_attach_existing_warning" class="alert alert-warning" style="display:none;"><i class="fas fa-exclamation-triangle me-2"></i><strong>Warning:</strong> Unit ini sudah memiliki baterai. Baterai lama akan dilepas otomatis ke Workshop.</div>
            <div class="mb-3"><label class="form-label">Notes (Opsional)</label><textarea class="form-control" id="bat_attach_notes" rows="2"></textarea></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success"><i class="fas fa-link me-1"></i>Attach</button></div>
        </form>
    </div></div>
</div>

<!-- Swap Battery Unit -->
<div class="modal fade" id="batSwapUnitModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header bg-light"><h5 class="modal-title"><i class="fas fa-exchange-alt me-2"></i>Move to Another Unit</h5><button type="button" class="btn-close btn-close-muted" data-bs-dismiss="modal"></button></div>
        <form id="batSwapUnitForm"><div class="modal-body">
            <input type="hidden" id="bat_swap_id"><input type="hidden" id="bat_swap_from_unit_id">
            <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i><span id="bat_swap_item_label" class="fw-semibold"></span> akan dipindah dari <strong id="bat_swap_from_unit_label"></strong></div>
            <div class="mb-3"><label class="form-label">Move to Unit <span class="text-danger">*</span></label><select class="form-select" id="bat_swap_to_unit_id" required><option value="">Select Destination Unit...</option></select></div>
            <div class="mb-3"><label class="form-label">Notes (Opsional)</label><textarea class="form-control" id="bat_swap_notes" rows="2"></textarea></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-warning"><i class="fas fa-exchange-alt me-1"></i>Move Unit</button></div>
        </form>
    </div></div>
</div>

<!-- Detach Battery from Unit -->
<div class="modal fade" id="batDetachFromUnitModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header bg-light"><h5 class="modal-title"><i class="fas fa-unlink me-2"></i>Detach from Unit</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form id="batDetachFromUnitForm"><div class="modal-body">
            <input type="hidden" id="bat_detach_id"><input type="hidden" id="bat_detach_from_unit_id">
            <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i><span id="bat_detach_item_label" class="fw-semibold"></span> akan dilepas dari <strong id="bat_detach_from_unit_label"></strong></div>
            <div class="mb-3"><label class="form-label">Detach Reason <span class="text-danger">*</span></label><select class="form-select" id="bat_detach_reason_select" onchange="toggleBatDetachReason(this.value)" required><option value="">Select Reason...</option><option value="Rusak - perlu repair">Damaged - Needs Repair</option><option value="Maintenance rutin">Routine Maintenance</option><option value="Lepas untuk backup">Detach for Backup</option><option value="Unit pulang rental">Unit Returned from Rental</option><option value="custom">Other Reason...</option></select></div>
            <div class="mb-3" id="bat_detach_custom_reason_group" style="display:none;"><label class="form-label">Custom Reason</label><textarea class="form-control" id="bat_detach_custom_reason" rows="2"></textarea></div>
            <div class="mb-3"><label class="form-label">Storage Location After Detach</label><select class="form-select" id="bat_detach_new_location"><option value="Workshop">Workshop</option><option value="POS 1">POS 1</option><option value="POS 2">POS 2</option><option value="POS 3">POS 3</option><option value="POS 4">POS 4</option><option value="POS 5">POS 5</option></select></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-warning"><i class="fas fa-unlink me-1"></i>Detach from Unit</button></div>
        </form>
    </div></div>
</div>


<!-- ═══════════════════════════════════════════════════════════
     CHARGER MODALS
════════════════════════════════════════════════════════════ -->

<!-- Detail / History -->
<div class="modal fade" id="viewChargerModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light py-2">
                <div>
                    <h5 class="modal-title mb-0"><i class="fas fa-plug me-2 text-warning"></i>Detail Charger</h5>
                    <p class="text-muted small mb-0" id="viewChargerModalSubtitle"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <ul class="nav nav-tabs px-3 pt-2 border-bottom" id="chargerModalTabs" role="tablist">
                    <li class="nav-item"><button class="nav-link active" id="chg-detail-tab" data-bs-toggle="tab" data-bs-target="#chg-detail-pane" type="button"><i class="fas fa-info-circle me-1"></i>Detail</button></li>
                    <li class="nav-item"><button class="nav-link" id="chg-history-tab" data-bs-toggle="tab" data-bs-target="#chg-history-pane" type="button" onclick="loadChargerHistory(currentChargerId)"><i class="fas fa-history me-1"></i>History <span class="badge badge-soft-blue ms-1" id="chgHistoryBadge" style="display:none;"></span></button></li>
                </ul>
                <div class="tab-content" style="height:60vh;overflow-y:auto;">
                    <div class="tab-pane fade show active" id="chg-detail-pane" role="tabpanel">
                        <div class="row g-0">
                            <div class="col-lg-8 p-3 border-end">
                                <div id="chargerDetailContent">
                                    <div class="text-center p-5 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i>Loading...</div>
                                </div>
                            </div>
                            <div class="col-lg-4 p-3 bg-light" id="chargerDetailSidebar">
                                <div id="chargerQuickInfo"></div>
                                <div id="chargerQrSection">
                                    <div class="card shadow-sm mb-3">
                                        <div class="card-header bg-light d-flex align-items-center justify-content-between">
                                            <h6 class="mb-0"><i class="fas fa-qrcode me-2"></i><strong>Barcode Aset</strong></h6>
                                            <span class="badge bg-dark">Public</span>
                                        </div>
                                        <div class="card-body p-2 small" id="chargerQrBody">
                                            <div class="text-center py-3 text-muted">
                                                <i class="fas fa-spinner fa-spin fa-2x mb-2 d-block text-warning"></i>
                                                <small>Memuat QR...</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="chg-history-pane" role="tabpanel">
                        <div class="d-flex align-items-center gap-2 px-3 pt-3 pb-2 border-bottom flex-wrap">
                            <span class="small fw-semibold text-muted me-1"><i class="fas fa-filter me-1"></i>Filter:</span>
                            <select id="chgHistoryFilter" class="form-select form-select-sm" style="width:auto;" onchange="applyChgHistoryFilter()">
                                <option value="all">Semua</option><option value="assign">Dipasang</option><option value="detach">Dilepas</option><option value="audit">Audit/SPK/WO</option><option value="movement">Surat Jalan</option><option value="update">Update Data</option>
                            </select>
                            <button class="btn btn-sm btn-outline-secondary ms-auto" onclick="loadChargerHistory(currentChargerId, true)"><i class="fas fa-sync-alt"></i></button>
                        </div>
                        <div id="chargerHistoryContent" class="p-3"><div class="text-center text-muted py-4"><i class="fas fa-history fa-2x mb-2 d-block"></i><p class="mb-0">Klik tab ini untuk memuat history.</p></div></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="me-auto d-flex gap-2">
                    <button type="button" class="btn btn-success btn-sm" id="btnChgAttachToUnit" onclick="openChgAttachModal()" style="display:none;"><i class="fas fa-link me-1"></i>Install to Unit</button>
                    <button type="button" class="btn btn-warning btn-sm" id="btnChgDetachFromUnit" onclick="openChgDetachModal()" style="display:none;"><i class="fas fa-unlink me-1"></i>Detach</button>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="editCurrentCharger()"><i class="fas fa-edit me-1"></i>Edit</button>
                <button type="button" class="btn btn-danger" onclick="deleteCurrentCharger()"><i class="fas fa-trash me-1"></i>Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Charger -->
<div class="modal fade" id="editChargerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light"><h5 class="modal-title"><i class="fas fa-edit me-2 text-warning"></i>Edit Charger</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form id="editChargerForm"><div class="modal-body">
            <input type="hidden" id="chg_edit_id" name="id"><input type="hidden" id="chg_edit_tipe_item" name="tipe_item" value="charger">
            <div class="mb-3"><label class="form-label fw-semibold">Item Number / Serial Number</label><input type="text" class="form-control bg-light" id="chg_edit_item_label" readonly></div>
            <div class="row g-3 mb-3">
                <div class="col-md-6"><label class="form-label fw-semibold">Serial Number (SN)</label><input type="text" class="form-control" id="chg_edit_serial_number" name="serial_number"></div>
                <div class="col-md-6"><label class="form-label fw-semibold">No Item</label><input type="text" class="form-control text-uppercase" id="chg_edit_item_number" name="item_number"></div>
            </div>
            <div class="mb-3"><label class="form-label fw-semibold">Status <span class="text-danger">*</span></label><select class="form-select" id="chg_edit_status" name="status" required><option value="AVAILABLE">Available</option><option value="IN_USE">In Use</option><option value="SPARE">Spare</option><option value="MAINTENANCE">Maintenance</option><option value="BROKEN">Broken</option><option value="RESERVED">Reserved</option><option value="SOLD">Sold</option></select></div>
            <div class="mb-3"><label class="form-label fw-semibold">Storage Location</label><select class="form-select" id="chg_edit_storage_location" name="storage_location"><option value="Workshop">Workshop</option><option value="WAREHOUSE">Warehouse</option><option value="POS 1">POS 1</option><option value="POS 2">POS 2</option><option value="POS 3">POS 3</option><option value="POS 4">POS 4</option><option value="POS 5">POS 5</option></select></div>
            <div class="mb-3"><label class="form-label fw-semibold">Physical Condition</label><select class="form-select" id="chg_edit_physical_condition" name="physical_condition"><option value="GOOD">Good</option><option value="MINOR_DAMAGE">Minor Damage</option><option value="MAJOR_DAMAGE">Major Damage</option></select></div>
            <div class="mb-3"><label class="form-label fw-semibold">Notes</label><textarea class="form-control" id="chg_edit_notes" name="notes" rows="2"></textarea></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Changes</button></div>
        </form>
        </div>
    </div>
</div>

<!-- Add Charger -->
<div class="modal fade" id="addChargerModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i class="fas fa-plus-circle me-2 text-warning"></i>Add New Charger</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="alert alert-info"><h6 class="alert-heading mb-1"><i class="fas fa-info-circle me-1"></i>Tambah Data Charger</h6><p class="mb-0 small">Pilih merk terlebih dahulu, kemudian pilih tipe charger. Nomor item akan diawali dengan prefix <strong>C</strong>.</p></div>
                <form id="addChargerForm">
                    <input type="hidden" name="tipe_item" value="charger">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label fw-semibold">Merk Charger <span class="text-danger">*</span></label><select class="form-select" id="add-chg-merk"><option value="">Pilih merk...</option></select></div>
                        <div class="col-md-6" id="add-chg-tipe-col"><label class="form-label fw-semibold">Tipe Charger <span class="text-danger">*</span></label><select class="form-select" id="add-charger-id" name="charger_id" disabled><option value="">Pilih tipe...</option></select></div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6"><label class="form-label fw-semibold">Serial Number (SN) <span class="text-muted small fw-normal">(opsional)</span></label><input type="text" class="form-control" id="add-sn-charger" name="sn_charger" placeholder="Cth: CHR-2024-001" disabled></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">No Item <span class="text-muted small fw-normal">(opsional)</span></label><div class="input-group"><input type="text" class="form-control text-uppercase" id="add-item-number-charger" name="item_number_charger" placeholder="Cth: C01545" disabled><button type="button" class="btn btn-outline-secondary btn-sm" id="add-btn-gen-charger" onclick="addGenChargerItemNumber()" disabled><i class="fas fa-magic"></i></button></div><div class="small text-muted mt-1" id="add-charger-last-hint">Kosongkan untuk otomatis saat simpan</div></div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6"><label class="form-label">Unit <span class="text-muted small fw-normal">(opsional)</span></label><select class="form-select" id="add-chg-unit-id" name="unit_id"></select></div>
                        <div class="col-md-6"><label class="form-label">Physical Condition</label><select class="form-select" id="add-chg-kondisi" name="physical_condition"><option value="GOOD">Good</option><option value="MINOR_DAMAGE">Minor Damage</option><option value="MAJOR_DAMAGE">Major Damage</option></select></div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6"><label class="form-label">Storage Location <span class="text-danger">*</span></label><select class="form-select" id="add-chg-lokasi" name="storage_location" required><option value="">Pilih lokasi...</option><optgroup label="Workshop"><option value="Workshop" selected>Workshop</option><option value="POS 1">POS 1</option><option value="POS 2">POS 2</option><option value="POS 3">POS 3</option><option value="POS 4">POS 4</option><option value="POS 5">POS 5</option></optgroup><optgroup label="Lainnya"><option value="WAREHOUSE">WAREHOUSE</option></optgroup></select></div>
                        <div class="col-md-6"><label class="form-label">Status</label><select class="form-select" name="status"><option value="AVAILABLE">Available</option><option value="IN_USE">In Use</option><option value="SPARE">Spare</option><option value="MAINTENANCE">Maintenance</option><option value="BROKEN">Broken</option><option value="RESERVED">Reserved</option></select></div>
                    </div>
                    <div class="mb-3 mt-3"><label class="form-label">Notes</label><textarea class="form-control" name="catatan" rows="2" placeholder="Catatan tambahan..."></textarea></div>
                </form>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-warning" id="btn-save-charger"><i class="fas fa-save me-1"></i>Save Charger</button></div>
        </div>
    </div>
</div>

<!-- Attach Charger to Unit -->
<div class="modal fade" id="chgAttachToUnitModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header bg-light"><h5 class="modal-title"><i class="fas fa-link me-2"></i>Attach to Unit</h5><button type="button" class="btn-close btn-close-muted" data-bs-dismiss="modal"></button></div>
        <form id="chgAttachToUnitForm"><div class="modal-body">
            <input type="hidden" id="chg_attach_id">
            <div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Select a unit to attach <span id="chg_attach_item_label" class="fw-semibold"></span></div>
            <div class="mb-3"><label class="form-label">Unit <span class="text-danger">*</span></label><select class="form-select" id="chg_attach_unit_id" required><option value="">Select Unit...</option></select></div>
            <div class="mb-3"><label class="form-label">Notes (Opsional)</label><textarea class="form-control" id="chg_attach_notes" rows="2"></textarea></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success"><i class="fas fa-link me-1"></i>Attach</button></div>
        </form>
    </div></div>
</div>

<!-- Detach Charger from Unit -->
<div class="modal fade" id="chgDetachFromUnitModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header bg-light"><h5 class="modal-title"><i class="fas fa-unlink me-2"></i>Detach from Unit</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form id="chgDetachFromUnitForm"><div class="modal-body">
            <input type="hidden" id="chg_detach_id"><input type="hidden" id="chg_detach_from_unit_id">
            <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i><span id="chg_detach_item_label" class="fw-semibold"></span> akan dilepas dari <strong id="chg_detach_from_unit_label"></strong></div>
            <div class="mb-3"><label class="form-label">Detach Reason <span class="text-danger">*</span></label><select class="form-select" id="chg_detach_reason_select" onchange="toggleChgDetachReason(this.value)" required><option value="">Select Reason...</option><option value="Rusak - perlu repair">Damaged - Needs Repair</option><option value="Maintenance rutin">Routine Maintenance</option><option value="Lepas untuk backup">Detach for Backup</option><option value="Unit pulang rental">Unit Returned from Rental</option><option value="custom">Other Reason...</option></select></div>
            <div class="mb-3" id="chg_detach_custom_reason_group" style="display:none;"><label class="form-label">Custom Reason</label><textarea class="form-control" id="chg_detach_custom_reason" rows="2"></textarea></div>
            <div class="mb-3"><label class="form-label">Storage Location After Detach</label><select class="form-select" id="chg_detach_new_location"><option value="Workshop">Workshop</option><option value="POS 1">POS 1</option><option value="POS 2">POS 2</option><option value="POS 3">POS 3</option><option value="POS 4">POS 4</option><option value="POS 5">POS 5</option></select></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-warning"><i class="fas fa-unlink me-1"></i>Detach from Unit</button></div>
        </form>
    </div></div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
// Battery & Charger Inventory — <?= date('Y-m-d') ?>
var currentBatteryId   = null;
var currentBatteryData = null;
var batteryTable       = null;
var batStatusFilter    = 'all';
var batChemistryFilter = '';
var _batAddPrefix      = 'B';
var _batHistoryCache    = [];
var _batHistoryLoadedId = null;

var currentChargerId   = null;
var currentChargerData = null;
var chargerTable       = null;
var chgStatusFilter    = 'all';
var _chgHistoryCache    = [];
var _chgHistoryLoadedId = null;

var csrfToken = '<?= csrf_hash() ?>';
var csrfName  = '<?= csrf_token() ?>';

function updateCsrfToken(r) { if (r && r.csrf_hash) csrfToken = r.csrf_hash; }

var chargerTableInitialized = false;

// ───── Tab switching (manual — bypasses Bootstrap tab CSS conflicts) ──────────────
window.switchBCTab = function(tab) {
    if (tab === 'battery') {
        $('#pane-battery').removeClass('d-none');
        $('#pane-charger').addClass('d-none');
        $('#tab-battery').addClass('active');
        $('#tab-charger').removeClass('active');
        $('#header-btns-battery').removeClass('d-none');
        $('#header-btns-charger').addClass('d-none');
        if (batteryTable) batteryTable.columns.adjust();
    } else {
        $('#pane-charger').removeClass('d-none');
        $('#pane-battery').addClass('d-none');
        $('#tab-charger').addClass('active');
        $('#tab-battery').removeClass('active');
        $('#header-btns-charger').removeClass('d-none');
        $('#header-btns-battery').addClass('d-none');
        if (!chargerTableInitialized) {
            chargerTableInitialized = true;
            setupChargerTable();
        } else if (chargerTable) {
            chargerTable.columns.adjust();
        }
    }
};

$(document).ready(function() {
    setupBatteryTable();
    // chargerTable is lazy-initialized on first tab click

    // Battery add btn
    $('#btnAddBattery').on('click', openAddBatteryModal);
    // Charger add btn
    $('#btnAddCharger').on('click', openAddChargerModal);

    // Modal cleanup
    $('#viewBatteryModal').on('hidden.bs.modal', function() {
        currentBatteryId = null; currentBatteryData = null;
        _batHistoryCache = []; _batHistoryLoadedId = null;
        $('#batHistoryBadge').hide().text('');
        $('#batteryHistoryContent').html('<div class="text-center text-muted py-4"><i class="fas fa-history fa-2x mb-2 d-block"></i><p class="mb-0">Klik tab ini untuk memuat history.</p></div>');
    });
    $('#viewChargerModal').on('hidden.bs.modal', function() {
        currentChargerId = null; currentChargerData = null;
        _chgHistoryCache = []; _chgHistoryLoadedId = null;
        $('#chgHistoryBadge').hide().text('');
        $('#chargerHistoryContent').html('<div class="text-center text-muted py-4"><i class="fas fa-history fa-2x mb-2 d-block"></i><p class="mb-0">Klik tab ini untuk memuat history.</p></div>');
    });

    // Edit Battery form
    $('#editBatteryForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_id').val();
        $.ajax({
            url: '<?= base_url('warehouse/inventory/attachments/update/') ?>' + id,
            type: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            data: $(this).serialize() + '&' + csrfName + '=' + csrfToken, dataType: 'json',
            success: function(r) { updateCsrfToken(r); if (r.success) { $('#editBatteryModal').modal('hide'); batteryTable.ajax.reload(null, false); OptimaNotify.success(r.message, 'Berhasil!'); } else OptimaNotify.error(r.message, 'Gagal!'); },
            error: function(xhr) { const r = xhr.responseJSON; OptimaNotify.error(r && r.message ? r.message : 'Koneksi gagal.', 'Error!'); }
        });
    });

    $('#editChargerForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#chg_edit_id').val();
        $.ajax({
            url: '<?= base_url('warehouse/inventory/attachments/update/') ?>' + id,
            type: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            data: $(this).serialize() + '&' + csrfName + '=' + csrfToken, dataType: 'json',
            success: function(r) { updateCsrfToken(r); if (r.success) { $('#editChargerModal').modal('hide'); chargerTable.ajax.reload(null, false); OptimaNotify.success(r.message, 'Berhasil!'); } else OptimaNotify.error(r.message, 'Gagal!'); },
            error: function(xhr) { const r = xhr.responseJSON; OptimaNotify.error(r && r.message ? r.message : 'Koneksi gagal.', 'Error!'); }
        });
    });

    // Save Battery
    $('#btn-save-battery').on('click', function() {
        if (!$('#add-baterai-id').val()) { OptimaNotify.warning('Pilih spesifikasi baterai terlebih dahulu', 'Validasi'); return; }
        if (!$('#add-sn-baterai').val()) { OptimaNotify.warning('Serial Number wajib diisi', 'Validasi'); return; }
        if (!$('#add-bat-lokasi').val()) { OptimaNotify.warning('Lokasi penyimpanan wajib dipilih', 'Validasi'); return; }
        const fd = new FormData($('#addBatteryForm')[0]);
        fd.append(csrfName, csrfToken);
        $.ajax({
            url: '<?= base_url('warehouse/inventory/attachments/add') ?>',
            type: 'POST', data: fd, processData: false, contentType: false,
            beforeSend: function() { $('#btn-save-battery').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Saving...'); },
            success: function(r) {
                updateCsrfToken(r);
                $('#btn-save-battery').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save Battery');
                if (r.success) { $('#addBatteryModal').modal('hide'); batteryTable.ajax.reload(); OptimaNotify.success(r.message || 'Battery berhasil ditambahkan!', 'Berhasil!'); }
                else OptimaNotify.error(r.message || 'Gagal menambahkan battery', 'Gagal!');
            },
            error: function() { $('#btn-save-battery').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save Battery'); OptimaNotify.error('Koneksi gagal.', 'Error!'); }
        });
    });

    // Save Charger
    $('#btn-save-charger').on('click', function() {
        if (!$('#add-charger-id').val()) { OptimaNotify.warning('Pilih tipe charger terlebih dahulu', 'Validasi'); return; }
        if (!$('#add-chg-lokasi').val()) { OptimaNotify.warning('Lokasi penyimpanan wajib dipilih', 'Validasi'); return; }
        const fd = new FormData($('#addChargerForm')[0]);
        fd.append(csrfName, csrfToken);
        $.ajax({
            url: '<?= base_url('warehouse/inventory/attachments/add') ?>',
            type: 'POST', data: fd, processData: false, contentType: false,
            beforeSend: function() { $('#btn-save-charger').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Saving...'); },
            success: function(r) {
                updateCsrfToken(r);
                $('#btn-save-charger').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save Charger');
                if (r.success) { $('#addChargerModal').modal('hide'); chargerTable.ajax.reload(); OptimaNotify.success(r.message || 'Charger berhasil ditambahkan!', 'Berhasil!'); }
                else OptimaNotify.error(r.message || 'Gagal menambahkan charger', 'Gagal!');
            },
            error: function() { $('#btn-save-charger').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save Charger'); OptimaNotify.error('Koneksi gagal.', 'Error!'); }
        });
    });

    // Attach Battery
    $('#batAttachToUnitForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '<?= base_url('warehouse/inventory/attachments/attach') ?>',
            type: 'POST', dataType: 'json',
            data: { attachment_id: $('#bat_attach_id').val(), attachment_type: 'battery', unit_id: $('#bat_attach_unit_id').val(), notes: $('#bat_attach_notes').val(), [csrfName]: csrfToken },
            success: function(r) { updateCsrfToken(r); if (r.success) { $('#batAttachToUnitModal, #viewBatteryModal').modal('hide'); batteryTable.ajax.reload(null, false); OptimaNotify.success(r.message, 'Berhasil!'); } else OptimaNotify.error(r.message, 'Gagal!'); },
            error: function() { OptimaNotify.error('Koneksi gagal.', 'Error!'); }
        });
    });

    // Swap Battery
    $('#batSwapUnitForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '<?= base_url('warehouse/inventory/attachments/swap') ?>',
            type: 'POST', dataType: 'json',
            data: { attachment_id: $('#bat_swap_id').val(), attachment_type: 'battery', from_unit_id: $('#bat_swap_from_unit_id').val(), to_unit_id: $('#bat_swap_to_unit_id').val(), notes: $('#bat_swap_notes').val(), [csrfName]: csrfToken },
            success: function(r) { updateCsrfToken(r); if (r.success) { $('#batSwapUnitModal, #viewBatteryModal').modal('hide'); batteryTable.ajax.reload(null, false); OptimaNotify.success(r.message, 'Berhasil!'); } else OptimaNotify.error(r.message, 'Gagal!'); },
            error: function() { OptimaNotify.error('Koneksi gagal.', 'Error!'); }
        });
    });

    // Detach Battery
    $('#batDetachFromUnitForm').on('submit', function(e) {
        e.preventDefault();
        const reason = $('#bat_detach_reason_select').val() === 'custom' ? $('#bat_detach_custom_reason').val() : $('#bat_detach_reason_select').val();
        $.ajax({
            url: '<?= base_url('warehouse/inventory/attachments/detach') ?>',
            type: 'POST', dataType: 'json',
            data: { attachment_id: $('#bat_detach_id').val(), attachment_type: 'battery', from_unit_id: $('#bat_detach_from_unit_id').val(), reason: reason, new_location: $('#bat_detach_new_location').val(), [csrfName]: csrfToken },
            success: function(r) { updateCsrfToken(r); if (r.success) { $('#batDetachFromUnitModal, #viewBatteryModal').modal('hide'); batteryTable.ajax.reload(null, false); OptimaNotify.success(r.message, 'Berhasil!'); } else OptimaNotify.error(r.message, 'Gagal!'); },
            error: function() { OptimaNotify.error('Koneksi gagal.', 'Error!'); }
        });
    });

    // Attach Charger
    $('#chgAttachToUnitForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '<?= base_url('warehouse/inventory/attachments/attach') ?>',
            type: 'POST', dataType: 'json',
            data: { attachment_id: $('#chg_attach_id').val(), attachment_type: 'charger', unit_id: $('#chg_attach_unit_id').val(), notes: $('#chg_attach_notes').val(), [csrfName]: csrfToken },
            success: function(r) { updateCsrfToken(r); if (r.success) { $('#chgAttachToUnitModal, #viewChargerModal').modal('hide'); chargerTable.ajax.reload(null, false); OptimaNotify.success(r.message, 'Berhasil!'); } else OptimaNotify.error(r.message, 'Gagal!'); },
            error: function() { OptimaNotify.error('Koneksi gagal.', 'Error!'); }
        });
    });

    // Detach Charger
    $('#chgDetachFromUnitForm').on('submit', function(e) {
        e.preventDefault();
        const reason = $('#chg_detach_reason_select').val() === 'custom' ? $('#chg_detach_custom_reason').val() : $('#chg_detach_reason_select').val();
        $.ajax({
            url: '<?= base_url('warehouse/inventory/attachments/detach') ?>',
            type: 'POST', dataType: 'json',
            data: { attachment_id: $('#chg_detach_id').val(), attachment_type: 'charger', from_unit_id: $('#chg_detach_from_unit_id').val(), reason: reason, new_location: $('#chg_detach_new_location').val(), [csrfName]: csrfToken },
            success: function(r) { updateCsrfToken(r); if (r.success) { $('#chgDetachFromUnitModal, #viewChargerModal').modal('hide'); chargerTable.ajax.reload(null, false); OptimaNotify.success(r.message, 'Berhasil!'); } else OptimaNotify.error(r.message, 'Gagal!'); },
            error: function() { OptimaNotify.error('Koneksi gagal.', 'Error!'); }
        });
    });
});

// ═══════════════════════════════════════════════════════════
//  BATTERY DataTable
// ═══════════════════════════════════════════════════════════

function setupBatteryTable() {
    batteryTable = $('#battery-table').DataTable({
        processing: true, serverSide: true,
        ajax: {
            url: '<?= base_url('warehouse/inventory/battery-charger') ?>',
            type: 'POST',
            data: function(d) { d.data_type = 'battery'; d.tipe_item = 'battery'; d.status_filter = batStatusFilter; d.chemistry_filter = batChemistryFilter; d[csrfName] = csrfToken; },
            dataSrc: function(r) { updateCsrfToken(r); return r.data || []; },
            error: function(xhr) { console.error('Battery DataTable error:', xhr.responseText); OptimaNotify.error('Gagal memuat data.', 'Error!'); }
        },
        columns: [
            { data: null, render: function(d,t,row) { return row.item_number || '-'; } },
            { data: null, render: function(d,t,row) {
                const v = row.jenis_baterai || '';
                if (v.includes('LEAD')) return '<span class="badge badge-soft-gray"><i class="fas fa-car-battery me-1"></i>Lead Acid</span>';
                if (v.includes('LITHI')) return '<span class="badge badge-soft-blue"><i class="fas fa-bolt me-1"></i>Lithium</span>';
                return v || '-';
            }},
            { data: null, render: function(d,t,row) { return row.merk_baterai || '-'; } },
            { data: null, render: function(d,t,row) { return row.tipe_baterai || '-'; } },
            { data: null, render: function(d,t,row) { return row.sn_baterai || '-'; } },
            { data: 'physical_condition', render: renderCondition },
            { data: 'status', render: renderStatus },
            { data: 'storage_location', render: function(data) { return data || '-'; } }
        ],
        order: [[0, 'desc']],
        language: { processing: '<i class="fas fa-spinner fa-spin me-2"></i>Memuat data...' }
    });
    $('#battery-table tbody').on('click', 'tr', function() {
        const row = batteryTable.row(this).data();
        if (row && row.id_inventory_attachment) { currentBatteryId = row.id_inventory_attachment; viewBattery(row.id_inventory_attachment); }
    });
}

// ═══════════════════════════════════════════════════════════
//  CHARGER DataTable
// ═══════════════════════════════════════════════════════════

function setupChargerTable() {
    chargerTable = $('#charger-table').DataTable({
        processing: true, serverSide: true,
        ajax: {
            url: '<?= base_url('warehouse/inventory/battery-charger') ?>',
            type: 'POST',
            data: function(d) { d.data_type = 'charger'; d.tipe_item = 'charger'; d.status_filter = chgStatusFilter; d[csrfName] = csrfToken; },
            dataSrc: function(r) { updateCsrfToken(r); return r.data || []; },
            error: function(xhr) { console.error('Charger DataTable error:', xhr.responseText); OptimaNotify.error('Gagal memuat data.', 'Error!'); }
        },
        columns: [
            { data: null, render: function(d,t,row) { return row.item_number || '-'; } },
            { data: null, render: function(d,t,row) { return row.merk_charger || '-'; } },
            { data: null, render: function(d,t,row) { return row.tipe_charger || '-'; } },
            { data: null, render: function(d,t,row) { return row.sn_charger || '-'; } },
            { data: 'physical_condition', render: renderCondition },
            { data: 'status', render: renderStatus },
            { data: 'storage_location', render: function(data) { return data || '-'; } }
        ],
        order: [[0, 'desc']],
        language: { processing: '<i class="fas fa-spinner fa-spin me-2"></i>Memuat data...' }
    });
    $('#charger-table tbody').on('click', 'tr', function() {
        const row = chargerTable.row(this).data();
        if (row && row.id_inventory_attachment) { currentChargerId = row.id_inventory_attachment; viewCharger(row.id_inventory_attachment); }
    });
}

// ───── Shared renderers ──────────────────────────────────────────────────────

function renderCondition(data) {
    const map = { 'GOOD': '<span class="badge badge-soft-green">Good</span>', 'MINOR_DAMAGE': '<span class="badge badge-soft-yellow">Minor Damage</span>', 'MAJOR_DAMAGE': '<span class="badge badge-soft-red">Major Damage</span>' };
    return map[data] || (data ? '<span class="badge badge-soft-gray">' + data + '</span>' : '-');
}
function renderStatus(data) {
    const map = { 'AVAILABLE': '<span class="badge badge-soft-green">Available</span>', 'IN_USE': '<span class="badge badge-soft-blue">In Use</span>', 'SPARE': '<span class="badge badge-soft-cyan">Spare</span>', 'MAINTENANCE': '<span class="badge badge-soft-yellow">Maintenance</span>', 'BROKEN': '<span class="badge badge-soft-red">Broken</span>', 'RESERVED': '<span class="badge badge-soft-gray">Reserved</span>', 'SOLD': '<span class="badge badge-soft-gray">Sold</span>' };
    return map[data] || (data ? '<span class="badge badge-soft-gray">' + data + '</span>' : '-');
}

// ───── Filter functions ──────────────────────────────────────────────────────

window.applyBatStatusFilter = function(status, el) {
    $('#batStatusTab .nav-link').removeClass('active');
    if (el) el.classList.add('active');
    batStatusFilter = status;
    batteryTable.ajax.reload();
};

window.applyChemistryFilter = function(chemistry) {
    $('#chemistryFilterGroup .btn').removeClass('active');
    event.currentTarget.classList.add('active');
    batChemistryFilter = chemistry;
    batteryTable.ajax.reload();
};

window.applyChgStatusFilter = function(status, el) {
    $('#chgStatusTab .nav-link').removeClass('active');
    if (el) el.classList.add('active');
    chgStatusFilter = status;
    chargerTable.ajax.reload();
};

// ═══════════════════════════════════════════════════════════
//  BATTERY VIEW / DETAIL / HISTORY
// ═══════════════════════════════════════════════════════════

function viewBattery(id) {
    currentBatteryId = id;
    $('#batteryDetailContent').html('<div class="text-center p-5 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i>Loading...</div>');
    $('#batteryQuickInfo').html('');
    $('#batteryQrBody').html('<div class="text-center py-3 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2 d-block text-success"></i><small>Memuat QR...</small></div>');
    $('#viewBatteryModalSubtitle').text('');
    var triggerEl = document.querySelector('#batteryModalTabs button[data-bs-target="#bat-detail-pane"]');
    if (triggerEl) bootstrap.Tab.getOrCreateInstance(triggerEl).show();
    $('#viewBatteryModal').modal('show');
    $.ajax({
        url: '<?= base_url('warehouse/inventory/attachments/detail/') ?>' + id,
        type: 'GET', dataType: 'json',
        success: function(r) {
            updateCsrfToken(r);
            if (r.success && r.data) {
                currentBatteryData = r.data;
                renderBatteryDetail(r.data);
                renderBatterySidebar(r.data);
                loadBatteryQr(r.data.id_inventory_attachment, 'battery');
                const inUse = r.data.status === 'IN_USE';
                $('#btnBatAttachToUnit').toggle(!inUse); $('#btnBatSwapUnit').toggle(inUse); $('#btnBatDetachFromUnit').toggle(inUse);
            } else $('#batteryDetailContent').html('<div class="alert alert-danger m-3">Gagal memuat detail.</div>');
        },
        error: function() { $('#batteryDetailContent').html('<div class="alert alert-danger m-3">Koneksi gagal.</div>'); }
    });
}

function renderBatteryDetail(d) {
    const h = (s) => (s===null||s===undefined||s==='')?'-':String(s).replace(/</g,'&lt;').replace(/>/g,'&gt;');
    const badge = (val, map) => { const cls = map[val]||'badge-soft-gray'; return `<span class="badge ${cls}">${h(val)}</span>`; };
    const statusMap = {AVAILABLE:'badge-soft-green',IN_USE:'badge-soft-blue',SPARE:'badge-soft-cyan',MAINTENANCE:'badge-soft-yellow',BROKEN:'badge-soft-red',RESERVED:'badge-soft-gray',SOLD:'badge-soft-gray'};
    const condMap   = {GOOD:'badge-soft-green',MINOR_DAMAGE:'badge-soft-yellow',MAJOR_DAMAGE:'badge-soft-red'};
    const condLabel = {GOOD:'Good',MINOR_DAMAGE:'Minor Damage',MAJOR_DAMAGE:'Major Damage'};
    const fmtDt = (dt) => { if(!dt) return '-'; try{return new Date(dt).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});}catch(e){return dt;} };
    const statusVal = d.status || '-';
    const condVal   = d.physical_condition || '';
    const condLbl   = condLabel[condVal] || condVal;
    const sn        = d.sn_baterai || '';

    const header = `
    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom flex-wrap">
        <span class="fw-bold font-monospace fs-6">${h(d.item_number||'#'+d.id_inventory_attachment)}</span>
        ${badge(statusVal, statusMap)}
        ${condVal ? badge(condVal, condMap).replace('>'+h(condVal)+'<', '>'+h(condLbl)+'<') : ''}
    </div>`;

    const assetCard = `
    <div class="card mb-3">
        <div class="card-header bg-light py-2"><h6 class="mb-0 small"><i class="fas fa-box me-2 text-secondary"></i><strong>Informasi Aset</strong></h6></div>
        <div class="card-body p-0">
            <dl class="row mb-0 small px-3 pt-2">
                <dt class="col-5 text-muted">Item Number</dt><dd class="col-7 fw-bold font-monospace">${h(d.item_number)}</dd>
                <dt class="col-5 text-muted">Serial Number</dt><dd class="col-7 fw-bold font-monospace">${sn||'-'}</dd>
                <dt class="col-5 text-muted">Status</dt><dd class="col-7">${badge(statusVal,statusMap)}</dd>
                <dt class="col-5 text-muted">Kondisi</dt><dd class="col-7">${condVal?badge(condVal,condMap).replace('>'+h(condVal)+'<','>'+h(condLbl)+'<'):'-'}</dd>
                <dt class="col-5 text-muted">Lokasi Simpan</dt><dd class="col-7">${h(d.storage_location)}</dd>
                <dt class="col-5 text-muted">Dibuat</dt><dd class="col-7">${fmtDt(d.created_at)}</dd>
                <dt class="col-5 text-muted">Diperbarui</dt><dd class="col-7">${fmtDt(d.updated_at)}</dd>
            </dl>
            ${(d.notes||d.catatan)?`<div class="px-3 pb-2 pt-1 border-top"><span class="small text-muted"><i class="fas fa-sticky-note me-1"></i></span><span class="small fst-italic">${h(d.notes||d.catatan)}</span></div>`:''}
        </div>
    </div>`;

    const specsCard = `
    <div class="card mb-3">
        <div class="card-header bg-light py-2"><h6 class="mb-0 small"><i class="fas fa-battery-half me-2 text-success"></i><strong>Spesifikasi Battery</strong></h6></div>
        <div class="card-body p-0">
            <dl class="row mb-0 small px-3 py-2">
                <dt class="col-5 text-muted">Merk</dt><dd class="col-7 fw-bold">${h(d.merk_baterai)}</dd>
                <dt class="col-5 text-muted">Spesifikasi</dt><dd class="col-7">${h(d.tipe_baterai)}</dd>
                <dt class="col-5 text-muted">Tipe Kimia</dt><dd class="col-7">${h(d.jenis_baterai)}</dd>
            </dl>
        </div>
    </div>`;

    $('#batteryDetailContent').html(header + assetCard + specsCard);
    $('#viewBatteryModalSubtitle').text(d.item_number || '#' + d.id_inventory_attachment);
}

function renderBatteryQuickInfo(d) {}
function renderBatterySidebar(d) {
    const h = (s) => (s===null||s===undefined||s==='')?'-':String(s).replace(/</g,'&lt;').replace(/>/g,'&gt;');
    const statusMap = {AVAILABLE:'badge-soft-green',IN_USE:'badge-soft-blue',SPARE:'badge-soft-cyan',MAINTENANCE:'badge-soft-yellow',BROKEN:'badge-soft-red',RESERVED:'badge-soft-gray',SOLD:'badge-soft-gray'};
    const statusVal   = d.status || '-';
    const noUnit      = d.no_unit || '';
    const isInstalled = !!noUnit;
    const cardBorder  = isInstalled ? 'border-primary border-opacity-25' : '';
    const cardBg      = isInstalled ? 'style="background:rgba(13,110,253,.05)"' : '';
    const unitRow = isInstalled
        ? `<div class="mt-2 pt-2 border-top">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fas fa-truck text-primary"></i>
                <span class="small fw-semibold text-primary">Terpasang di Unit</span>
            </div>
            <dl class="row mb-0 small ms-1">
                <dt class="col-5 text-muted">No Unit</dt>
                <dd class="col-7 fw-bold font-monospace">${h(noUnit)}</dd>
                ${d.model_unit?`<dt class="col-5 text-muted">Model</dt><dd class="col-7">${h(d.model_unit)}</dd>`:''}
            </dl>
           </div>`
        : `<div class="mt-2 pt-2 border-top text-muted small"><i class="fas fa-circle-xmark me-1"></i>Belum terpasang di unit</div>`;
    $('#batteryQuickInfo').html(`
        <div class="card mb-3 ${cardBorder}">
            <div class="card-header py-2 d-flex align-items-center justify-content-between" ${cardBg}>
                <h6 class="mb-0 small fw-semibold"><i class="fas fa-info-circle me-2 ${isInstalled?'text-primary':'text-muted'}"></i>Quick Info</h6>
                <span class="badge ${statusMap[statusVal]||'badge-soft-gray'}">${h(statusVal)}</span>
            </div>
            <div class="card-body py-2 px-3">${unitRow}</div>
        </div>
    `);
}

function loadBatteryQr(id, type) {
    $.get('<?= base_url('warehouse/inventory/attachments/get-token/') ?>' + id + '/' + type, function(r) {
        if (r.success && r.token) {
            const url = '<?= base_url('attachment-view/') ?>' + r.token;
            const qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=' + encodeURIComponent(url);
            $('#batteryQrBody').html(`
                <div class="text-center border rounded p-2">
                    <img src="${qrUrl}" alt="QR Code" style="width:160px;height:160px;">
                    <div class="mt-2">
                        <a href="${url}" target="_blank" class="btn btn-sm btn-dark me-1"><i class="fas fa-link me-1"></i>Link</a>
                        <a href="${url}" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-download me-1"></i>Download Barcode Label</a>
                    </div>
                </div>
            `);
        } else {
            $('#batteryQrBody').html('<div class="text-center text-muted py-3"><small>QR tidak tersedia.</small></div>');
        }
    });
}

function loadBatteryHistory(id, forceReload) {
    if (!id) return;
    if (!forceReload && _batHistoryLoadedId === id) { applyBatHistoryFilter(); return; }
    $('#batteryHistoryContent').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-success"></i><p class="mt-2 text-muted">Memuat history...</p></div>');
    $.ajax({
        url: '<?= base_url('warehouse/inventory/attachments/history/') ?>' + id,
        type: 'GET', dataType: 'json',
        success: function(r) { if (r.success) { _batHistoryLoadedId = id; _batHistoryCache = r.timeline||[]; if (_batHistoryCache.length) $('#batHistoryBadge').text(_batHistoryCache.length).show(); applyBatHistoryFilter(); } }
    });
}

// ── Shared timeline renderer (matches attachment page API fields) ──────────────
function renderBCTimeline(timeline) {
    if (!timeline || !timeline.length) {
        return '<div class="text-center text-muted py-5"><i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i><h6>Belum Ada Aktivitas</h6></div>';
    }
    const h = (s) => (s===null||s===undefined)?'-':String(s).replace(/</g,'&lt;').replace(/>/g,'&gt;');
    const fmtDt = (d) => { if(!d) return '-'; try{return new Date(d).toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});}catch(e){return d;} };
    const fmtD  = (d) => { if(!d) return '-'; try{return new Date(d).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'});}catch(e){return d;} };
    const colorHex = {primary:'#0d6efd',success:'#198754',warning:'#ffc107',info:'#0dcaf0',secondary:'#6c757d',dark:'#212529',danger:'#dc3545',purple:'#6f42c1',orange:'#fd7e14',cyan:'#0dcaf0'};

    const buildItem = (item) => {
        const hex   = colorHex[item.color||'secondary'] || '#6c757d';
        const icon  = item.icon || 'fas fa-circle';
        const title = item.title || item.event_label || item.event_type || '-';
        const desc  = item.description || item.subtitle || '';
        const user  = item.performed_by || null;
        const ref   = item.ref_number || null;
        let detailsHtml = '';
        if (item.details && typeof item.details === 'object' && !Array.isArray(item.details)) {
            const rows = Object.entries(item.details).filter(([k,v])=>v!==null&&v!==undefined&&v!=='').map(([k,v])=>`<tr><td class="text-muted pe-2" style="white-space:nowrap;font-size:0.72rem;">${h(k)}</td><td style="font-size:0.72rem;">${h(v)}</td></tr>`).join('');
            if (rows) detailsHtml = `<table class="mt-1 w-100">${rows}</table>`;
        }
        return `<div class="timeline-item mb-2" style="position:relative;padding-left:2rem;">
            <div style="position:absolute;left:0;top:0.25rem;width:1.1rem;height:1.1rem;border-radius:50%;background:${hex};display:flex;align-items:center;justify-content:center;z-index:1;box-shadow:0 0 0 2px #fff;">
                <i class="${h(icon)} text-white" style="font-size:0.5rem;"></i>
            </div>
            <div class="card border-0 shadow-sm mb-0" style="border-left:3px solid ${hex} !important;">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-start justify-content-between flex-wrap gap-1">
                        <div><span class="fw-semibold small">${h(title)}</span>${ref?`<span class="badge badge-soft-gray ms-1" style="font-size:0.65rem;"><i class="fas fa-hashtag me-1"></i>${h(ref)}</span>`:''}</div>
                        <small class="text-muted text-nowrap"><i class="fas fa-calendar-alt me-1"></i>${h(fmtDt(item.date||item.created_at))}</small>
                    </div>
                    ${desc?`<div class="text-muted small mt-1">${h(desc)}</div>`:''}
                    ${detailsHtml}
                    ${user?`<div class="text-muted" style="font-size:0.72rem;margin-top:2px;"><i class="fas fa-user me-1"></i>${h(user)}</div>`:''}
                </div>
            </div>
        </div>`;
    };

    // Group by date
    const groups = {}; const order = [];
    timeline.forEach(item => {
        const d = (item.date||item.created_at||'').substring(0,10) || '0000-00-00';
        if (!groups[d]) { groups[d] = []; order.push(d); }
        groups[d].push(item);
    });
    let html = '<div class="p-3"><div class="d-flex align-items-center mb-3 pb-2 border-bottom"><i class="fas fa-history text-primary me-2"></i><span class="fw-semibold">Timeline Aktivitas</span><span class="badge badge-soft-blue ms-2">'+timeline.length+' event</span></div>';
    order.forEach(dateKey => {
        const items = groups[dateKey];
        const label = dateKey==='0000-00-00'?'Tanggal tidak diketahui':fmtD(dateKey);
        html += `<div class="border rounded mb-3" style="overflow:hidden;"><div class="px-3 py-2 d-flex align-items-center gap-2" style="background:rgba(0,0,0,.03);border-bottom:1px solid #dee2e6;"><i class="fas fa-calendar-day text-muted" style="font-size:0.8rem;"></i><span class="fw-semibold small">${label}</span><span class="badge badge-soft-blue ms-auto" style="font-size:0.65rem;">${items.length} event</span></div><div class="p-3 position-relative" style="padding-left:1rem!important;"><div style="position:absolute;left:1.5rem;top:0;bottom:0;width:2px;background:#dee2e6;border-radius:2px;"></div>${items.map(buildItem).join('')}</div></div>`;
    });
    return html + '</div>';
}

function applyBatHistoryFilter() {
    const f = $('#batHistoryFilter').val()||'all';
    let data = f === 'all' ? _batHistoryCache : _batHistoryCache.filter(function(e){ return (e.source||'log')===f||(e.type||'')===f; });
    $('#batteryHistoryContent').html(renderBCTimeline(data));
}

function editCurrentBattery() {
    if (!currentBatteryData) return;
    const d = currentBatteryData;
    $('#edit_id').val(d.id_inventory_attachment); $('#edit_tipe_item').val('battery');
    $('#edit_item_label').val((d.item_number||'')+(d.sn_baterai?' / '+d.sn_baterai:''));
    $('#edit_serial_number').val(d.sn_baterai||''); $('#edit_item_number').val(d.item_number||'');
    $('#edit_status').val(d.status||'AVAILABLE'); $('#edit_storage_location').val(d.storage_location||'Workshop');
    $('#edit_physical_condition').val(d.physical_condition||'GOOD'); $('#edit_notes').val(d.notes||d.catatan||'');
    $('#viewBatteryModal').modal('hide');
    setTimeout(function(){ $('#editBatteryModal').modal('show'); }, 300);
}

function deleteCurrentBattery() {
    if (!currentBatteryId) return;
    if (!confirm('Yakin hapus battery ini? Tindakan ini tidak dapat dibatalkan.')) return;
    $.ajax({
        url: '<?= base_url('warehouse/inventory/attachments/delete/') ?>' + currentBatteryId,
        type: 'POST', data: {[csrfName]: csrfToken}, dataType: 'json',
        success: function(r) { updateCsrfToken(r); if (r.success) { $('#viewBatteryModal').modal('hide'); batteryTable.ajax.reload(null,false); OptimaNotify.success(r.message||'Battery berhasil dihapus','Berhasil!'); } else OptimaNotify.error(r.message,'Gagal!'); },
        error: function() { OptimaNotify.error('Koneksi gagal.','Error!'); }
    });
}

function openBatAttachModal() {
    if (!currentBatteryData) return;
    const d = currentBatteryData;
    $('#bat_attach_id').val(d.id_inventory_attachment); $('#bat_attach_item_label').text(d.item_number||d.sn_baterai||'Battery');
    $('#bat_attach_existing_warning').hide();
    loadUnitsForModal('#bat_attach_unit_id', '#batAttachToUnitModal');
    $('#viewBatteryModal').modal('hide'); setTimeout(function(){ $('#batAttachToUnitModal').modal('show'); }, 300);
}

function openBatSwapModal() {
    if (!currentBatteryData) return;
    const d = currentBatteryData;
    $('#bat_swap_id').val(d.id_inventory_attachment); $('#bat_swap_from_unit_id').val(d.unit_id||'');
    $('#bat_swap_item_label').text(d.item_number||d.sn_baterai||'Battery'); $('#bat_swap_from_unit_label').text(d.no_unit||'-');
    loadUnitsForModal('#bat_swap_to_unit_id', '#batSwapUnitModal');
    $('#viewBatteryModal').modal('hide'); setTimeout(function(){ $('#batSwapUnitModal').modal('show'); }, 300);
}

function openBatDetachModal() {
    if (!currentBatteryData) return;
    const d = currentBatteryData;
    $('#bat_detach_id').val(d.id_inventory_attachment); $('#bat_detach_from_unit_id').val(d.unit_id||'');
    $('#bat_detach_item_label').text(d.item_number||d.sn_baterai||'Battery'); $('#bat_detach_from_unit_label').text(d.no_unit||'-');
    $('#bat_detach_custom_reason_group').hide();
    $('#viewBatteryModal').modal('hide'); setTimeout(function(){ $('#batDetachFromUnitModal').modal('show'); }, 300);
}

function toggleBatDetachReason(v) { $('#bat_detach_custom_reason_group').toggle(v==='custom'); }

// ═══════════════════════════════════════════════════════════
//  CHARGER VIEW / DETAIL / HISTORY
// ═══════════════════════════════════════════════════════════

function viewCharger(id) {
    currentChargerId = id;
    $('#chargerDetailContent').html('<div class="text-center p-5 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i>Loading...</div>');
    $('#chargerQuickInfo').html('');
    $('#chargerQrBody').html('<div class="text-center py-3 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2 d-block text-warning"></i><small>Memuat QR...</small></div>');
    $('#viewChargerModalSubtitle').text('');
    var triggerEl = document.querySelector('#chargerModalTabs button[data-bs-target="#chg-detail-pane"]');
    if (triggerEl) bootstrap.Tab.getOrCreateInstance(triggerEl).show();
    $('#viewChargerModal').modal('show');
    $.ajax({
        url: '<?= base_url('warehouse/inventory/attachments/detail/') ?>' + id,
        type: 'GET', dataType: 'json',
        success: function(r) {
            updateCsrfToken(r);
            if (r.success && r.data) {
                currentChargerData = r.data;
                renderChargerDetail(r.data);
                renderChargerSidebar(r.data);
                loadChargerQr(r.data.id_inventory_attachment, 'charger');
                const inUse = r.data.status === 'IN_USE';
                $('#btnChgAttachToUnit').toggle(!inUse); $('#btnChgDetachFromUnit').toggle(inUse);
            } else $('#chargerDetailContent').html('<div class="alert alert-danger m-3">Gagal memuat detail.</div>');
        },
        error: function() { $('#chargerDetailContent').html('<div class="alert alert-danger m-3">Koneksi gagal.</div>'); }
    });
}

function renderChargerDetail(d) {
    const h = (s) => (s===null||s===undefined||s==='')?'-':String(s).replace(/</g,'&lt;').replace(/>/g,'&gt;');
    const badge = (val, map) => { const cls = map[val]||'badge-soft-gray'; return `<span class="badge ${cls}">${h(val)}</span>`; };
    const statusMap = {AVAILABLE:'badge-soft-green',IN_USE:'badge-soft-blue',SPARE:'badge-soft-cyan',MAINTENANCE:'badge-soft-yellow',BROKEN:'badge-soft-red',RESERVED:'badge-soft-gray',SOLD:'badge-soft-gray'};
    const condMap   = {GOOD:'badge-soft-green',MINOR_DAMAGE:'badge-soft-yellow',MAJOR_DAMAGE:'badge-soft-red'};
    const condLabel = {GOOD:'Good',MINOR_DAMAGE:'Minor Damage',MAJOR_DAMAGE:'Major Damage'};
    const fmtDt = (dt) => { if(!dt) return '-'; try{return new Date(dt).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});}catch(e){return dt;} };
    const statusVal = d.status || '-';
    const condVal   = d.physical_condition || '';
    const condLbl   = condLabel[condVal] || condVal;
    const sn        = d.serial_number || '';

    const header = `
    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom flex-wrap">
        <span class="fw-bold font-monospace fs-6">${h(d.item_number||'#'+d.id_inventory_attachment)}</span>
        ${badge(statusVal, statusMap)}
        ${condVal ? badge(condVal, condMap).replace('>'+h(condVal)+'<', '>'+h(condLbl)+'<') : ''}
    </div>`;

    const assetCard = `
    <div class="card mb-3">
        <div class="card-header bg-light py-2"><h6 class="mb-0 small"><i class="fas fa-box me-2 text-secondary"></i><strong>Informasi Aset</strong></h6></div>
        <div class="card-body p-0">
            <dl class="row mb-0 small px-3 pt-2">
                <dt class="col-5 text-muted">Item Number</dt><dd class="col-7 fw-bold font-monospace">${h(d.item_number)}</dd>
                <dt class="col-5 text-muted">Serial Number</dt><dd class="col-7 fw-bold font-monospace">${sn||'-'}</dd>
                <dt class="col-5 text-muted">Status</dt><dd class="col-7">${badge(statusVal,statusMap)}</dd>
                <dt class="col-5 text-muted">Kondisi</dt><dd class="col-7">${condVal?badge(condVal,condMap).replace('>'+h(condVal)+'<','>'+h(condLbl)+'<'):'-'}</dd>
                <dt class="col-5 text-muted">Lokasi Simpan</dt><dd class="col-7">${h(d.storage_location)}</dd>
                <dt class="col-5 text-muted">Dibuat</dt><dd class="col-7">${fmtDt(d.created_at)}</dd>
                <dt class="col-5 text-muted">Diperbarui</dt><dd class="col-7">${fmtDt(d.updated_at)}</dd>
            </dl>
            ${(d.notes||d.catatan)?`<div class="px-3 pb-2 pt-1 border-top"><span class="small text-muted"><i class="fas fa-sticky-note me-1"></i></span><span class="small fst-italic">${h(d.notes||d.catatan)}</span></div>`:''}
        </div>
    </div>`;

    const specsCard = `
    <div class="card mb-3">
        <div class="card-header bg-light py-2"><h6 class="mb-0 small"><i class="fas fa-plug me-2 text-warning"></i><strong>Spesifikasi Charger</strong></h6></div>
        <div class="card-body p-0">
            <dl class="row mb-0 small px-3 py-2">
                <dt class="col-5 text-muted">Merk</dt><dd class="col-7 fw-bold">${h(d.merk_charger)}</dd>
                <dt class="col-5 text-muted">Tipe Charger</dt><dd class="col-7">${h(d.tipe_charger)}</dd>
            </dl>
        </div>
    </div>`;

    $('#chargerDetailContent').html(header + assetCard + specsCard);
    $('#viewChargerModalSubtitle').text(d.item_number || '#' + d.id_inventory_attachment);
}

function renderChargerQuickInfo(d) {}
function renderChargerSidebar(d) {
    const h = (s) => (s===null||s===undefined||s==='')?'-':String(s).replace(/</g,'&lt;').replace(/>/g,'&gt;');
    const statusMap = {AVAILABLE:'badge-soft-green',IN_USE:'badge-soft-blue',SPARE:'badge-soft-cyan',MAINTENANCE:'badge-soft-yellow',BROKEN:'badge-soft-red',RESERVED:'badge-soft-gray',SOLD:'badge-soft-gray'};
    const statusVal   = d.status || '-';
    const noUnit      = d.no_unit || '';
    const isInstalled = !!noUnit;
    const cardBorder  = isInstalled ? 'border-primary border-opacity-25' : '';
    const cardBg      = isInstalled ? 'style="background:rgba(13,110,253,.05)"' : '';
    const unitRow = isInstalled
        ? `<div class="mt-2 pt-2 border-top">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fas fa-truck text-primary"></i>
                <span class="small fw-semibold text-primary">Terpasang di Unit</span>
            </div>
            <dl class="row mb-0 small ms-1">
                <dt class="col-5 text-muted">No Unit</dt>
                <dd class="col-7 fw-bold font-monospace">${h(noUnit)}</dd>
                ${d.model_unit?`<dt class="col-5 text-muted">Model</dt><dd class="col-7">${h(d.model_unit)}</dd>`:''}
            </dl>
           </div>`
        : `<div class="mt-2 pt-2 border-top text-muted small"><i class="fas fa-circle-xmark me-1"></i>Belum terpasang di unit</div>`;
    $('#chargerQuickInfo').html(`
        <div class="card mb-3 ${cardBorder}">
            <div class="card-header py-2 d-flex align-items-center justify-content-between" ${cardBg}>
                <h6 class="mb-0 small fw-semibold"><i class="fas fa-info-circle me-2 ${isInstalled?'text-primary':'text-muted'}"></i>Quick Info</h6>
                <span class="badge ${statusMap[statusVal]||'badge-soft-gray'}">${h(statusVal)}</span>
            </div>
            <div class="card-body py-2 px-3">${unitRow}</div>
        </div>
    `);
}

function loadChargerQr(id, type) {
    $.get('<?= base_url('warehouse/inventory/attachments/get-token/') ?>' + id + '/' + type, function(r) {
        if (r.success && r.token) {
            const url = '<?= base_url('attachment-view/') ?>' + r.token;
            const qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=' + encodeURIComponent(url);
            $('#chargerQrBody').html(`
                <div class="text-center border rounded p-2">
                    <img src="${qrUrl}" alt="QR Code" style="width:160px;height:160px;">
                    <div class="mt-2">
                        <a href="${url}" target="_blank" class="btn btn-sm btn-dark me-1"><i class="fas fa-link me-1"></i>Link</a>
                        <a href="${url}" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-download me-1"></i>Download Barcode Label</a>
                    </div>
                </div>
            `);
        } else {
            $('#chargerQrBody').html('<div class="text-center text-muted py-3"><small>QR tidak tersedia.</small></div>');
        }
    });
}

function loadChargerHistory(id, forceReload) {
    if (!id) return;
    if (!forceReload && _chgHistoryLoadedId === id) { applyChgHistoryFilter(); return; }
    $('#chargerHistoryContent').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-warning"></i><p class="mt-2 text-muted">Memuat history...</p></div>');
    $.ajax({
        url: '<?= base_url('warehouse/inventory/attachments/history/') ?>' + id,
        type: 'GET', dataType: 'json',
        success: function(r) { if (r.success) { _chgHistoryLoadedId = id; _chgHistoryCache = r.timeline||[]; if (_chgHistoryCache.length) $('#chgHistoryBadge').text(_chgHistoryCache.length).show(); applyChgHistoryFilter(); } }
    });
}

function applyChgHistoryFilter() {
    const f = $('#chgHistoryFilter').val()||'all';
    let data = f === 'all' ? _chgHistoryCache : _chgHistoryCache.filter(function(e){ return (e.source||'log')===f||(e.type||'')===f; });
    $('#chargerHistoryContent').html(renderBCTimeline(data));
}

function editCurrentCharger() {
    if (!currentChargerData) return;
    const d = currentChargerData;
    $('#chg_edit_id').val(d.id_inventory_attachment); $('#chg_edit_tipe_item').val('charger');
    $('#chg_edit_item_label').val((d.item_number||'')+(d.serial_number?' / '+d.serial_number:''));
    $('#chg_edit_serial_number').val(d.serial_number||''); $('#chg_edit_item_number').val(d.item_number||'');
    $('#chg_edit_status').val(d.status||'AVAILABLE'); $('#chg_edit_storage_location').val(d.storage_location||'Workshop');
    $('#chg_edit_physical_condition').val(d.physical_condition||'GOOD'); $('#chg_edit_notes').val(d.notes||d.catatan||'');
    $('#viewChargerModal').modal('hide');
    setTimeout(function(){ $('#editChargerModal').modal('show'); }, 300);
}

function deleteCurrentCharger() {
    if (!currentChargerId) return;
    if (!confirm('Yakin hapus charger ini? Tindakan ini tidak dapat dibatalkan.')) return;
    $.ajax({
        url: '<?= base_url('warehouse/inventory/attachments/delete/') ?>' + currentChargerId,
        type: 'POST', data: {[csrfName]: csrfToken}, dataType: 'json',
        success: function(r) { updateCsrfToken(r); if (r.success) { $('#viewChargerModal').modal('hide'); chargerTable.ajax.reload(null,false); OptimaNotify.success(r.message||'Charger berhasil dihapus','Berhasil!'); } else OptimaNotify.error(r.message,'Gagal!'); },
        error: function() { OptimaNotify.error('Koneksi gagal.','Error!'); }
    });
}

function openChgAttachModal() {
    if (!currentChargerData) return;
    const d = currentChargerData;
    $('#chg_attach_id').val(d.id_inventory_attachment); $('#chg_attach_item_label').text(d.item_number||d.sn_charger||'Charger');
    loadUnitsForModal('#chg_attach_unit_id', '#chgAttachToUnitModal');
    $('#viewChargerModal').modal('hide'); setTimeout(function(){ $('#chgAttachToUnitModal').modal('show'); }, 300);
}

function openChgDetachModal() {
    if (!currentChargerData) return;
    const d = currentChargerData;
    $('#chg_detach_id').val(d.id_inventory_attachment); $('#chg_detach_from_unit_id').val(d.unit_id||'');
    $('#chg_detach_item_label').text(d.item_number||d.sn_charger||'Charger'); $('#chg_detach_from_unit_label').text(d.no_unit||'-');
    $('#chg_detach_custom_reason_group').hide();
    $('#viewChargerModal').modal('hide'); setTimeout(function(){ $('#chgDetachFromUnitModal').modal('show'); }, 300);
}

function toggleChgDetachReason(v) { $('#chg_detach_custom_reason_group').toggle(v==='custom'); }

// ═══════════════════════════════════════════════════════════
//  SHARED: Unit select with AJAX search (type ≥1 char)
// ═══════════════════════════════════════════════════════════

var _unitAjaxUrl = '<?= base_url('warehouse/inventory/attachments/available-units') ?>';

function loadUnitsForModal(selectSel, modalSel, placeholder) {
    placeholder = placeholder || 'Ketik no. unit untuk mencari...';
    const $sel = $(selectSel);
    if ($sel.hasClass('select2-hidden-accessible')) { try { $sel.select2('destroy'); } catch(e){} }
    $sel.empty();
    try {
        $sel.select2({
            theme: 'bootstrap-5',
            placeholder: placeholder,
            allowClear: true,
            width: '100%',
            minimumInputLength: 1,
            dropdownParent: $(modalSel),
            ajax: {
                url: _unitAjaxUrl,
                type: 'GET',
                dataType: 'json',
                delay: 250,
                data: function(params) { return { q: params.term || '' }; },
                processResults: function(r) {
                    if (!r.success) return { results: [] };
                    return {
                        results: (r.units || []).map(function(u) {
                            return { id: u.id_inventory_unit, text: (u.no_unit || u.id_inventory_unit) + (u.model_unit ? ' — ' + u.model_unit : ''), no_unit: u.no_unit, model_unit: u.model_unit || '' };
                        })
                    };
                },
                cache: true
            },
            escapeMarkup: function(m) { return m; },
            templateResult: function(item) {
                if (item.loading) return 'Mencari...';
                return '<div><strong>' + (item.no_unit || item.text) + '</strong>' + (item.model_unit ? '<br><small class="text-muted">' + item.model_unit + '</small>' : '') + '</div>';
            },
            templateSelection: function(item) { return item.no_unit || item.text; }
        });
    } catch(e) {}
}

// ═══════════════════════════════════════════════════════════
//  ADD BATTERY CASCADE
// ═══════════════════════════════════════════════════════════

function openAddBatteryModal() {
    $('#addBatteryForm')[0].reset();
    initAddBatteryCascade();
    loadUnitsForAddModal('#add-bat-unit-id', '#addBatteryModal');
    $('#addBatteryModal').modal('show');
}

function initAddBatteryCascade() {
    _batAddPrefix = 'B';
    $('#add-bat-tipe').val('');
    $('#add-bat-merk').val('').empty().append('<option value="">Pilih merk...</option>').prop('disabled', true);
    $('#add-baterai-id').val('').empty().append('<option value="">Pilih spesifikasi...</option>').prop('disabled', true);
    $('#add-sn-baterai').val('').prop('disabled', true);
    $('#add-item-number-battery').val('').prop('disabled', true);
    $('#add-btn-gen-battery').prop('disabled', true);
    $('#add-bat-prefix-badge').html(''); $('#add-battery-last-hint').html('');
    $('#add-battery-item-hint').text('Kosongkan untuk otomatis saat simpan');

    $('#add-bat-tipe').off('change.addbc').on('change.addbc', function() {
        const tipe = $(this).val();
        $('#add-bat-merk').val('').empty().append('<option value="">Pilih merk...</option>').prop('disabled', true);
        $('#add-baterai-id').val('').empty().append('<option value="">Pilih spesifikasi...</option>').prop('disabled', true);
        $('#add-sn-baterai').val('').prop('disabled', true);
        $('#add-item-number-battery').val('').prop('disabled', true);
        $('#add-btn-gen-battery').prop('disabled', true);
        $('#add-battery-last-hint').html('');
        if (!tipe) { $('#add-bat-prefix-badge').html(''); return; }
        const isLi = (tipe === 'LITHIUM');
        _batAddPrefix = isLi ? 'BL' : 'B';
        const cls = isLi ? 'badge-soft-blue' : 'badge-soft-gray';
        $('#add-bat-prefix-badge').html('<span class="badge '+cls+'">Prefix: <strong>'+_batAddPrefix+'</strong></span> <span id="add-bat-last-inline" class="text-muted small">memuat...</span>');
        $.get('<?= base_url('warehouse/inventory/attachments/last-item-number') ?>', { type: 'battery', prefix: _batAddPrefix }, function(r) { if (r.success) $('#add-bat-last-inline').text('Terakhir: '+(r.last||'belum ada')); });
        $.get('<?= base_url('warehouse/inventory/attachments/master-merk/battery') ?>', { tipe: tipe }, function(r) {
            const $s = $('#add-bat-merk').empty().append('<option value="">Pilih merk...</option>');
            if (r.success && r.data.length) { r.data.forEach(function(it){ $s.append($('<option>').val(it.value).text(it.text)); }); $s.prop('disabled', false); }
        });
    });

    $('#add-bat-merk').off('change.addbc').on('change.addbc', function() {
        const merk = $(this).val(), tipe = $('#add-bat-tipe').val();
        $('#add-baterai-id').val('').empty().append('<option value="">Pilih spesifikasi...</option>').prop('disabled', true);
        $('#add-sn-baterai').val('').prop('disabled', true);
        $('#add-item-number-battery').val('').prop('disabled', true);
        $('#add-btn-gen-battery').prop('disabled', true);
        $('#add-battery-last-hint').html('');
        if (!merk) return;
        $.get('<?= base_url('warehouse/inventory/attachments/master-jenis/battery') ?>', { tipe: tipe, merk: merk }, function(r) {
            const $s = $('#add-baterai-id').empty().append('<option value="">Pilih spesifikasi...</option>');
            if (r.success && r.data.length) { r.data.forEach(function(it){ $s.append($('<option>').val(it.id).text(it.text)); }); $s.prop('disabled', false); }
            $.get('<?= base_url('warehouse/inventory/attachments/last-item-number') ?>', { type: 'battery', prefix: _batAddPrefix }, function(r2) { if (r2.success) $('#add-battery-last-hint').html('Nomor terakhir <strong>'+_batAddPrefix+'</strong>: <strong>'+(r2.last||'belum ada')+'</strong>'); });
        });
    });

    $('#add-baterai-id').off('change.addbc').on('change.addbc', function() {
        const has = !!$(this).val();
        $('#add-sn-baterai').prop('disabled', !has); $('#add-item-number-battery').prop('disabled', !has); $('#add-btn-gen-battery').prop('disabled', !has);
    });
}

function addGenBatteryItemNumber() {
    if (!$('#add-baterai-id').val()) { OptimaNotify.warning('Pilih spesifikasi baterai terlebih dahulu', 'Info'); return; }
    $.get('<?= base_url('warehouse/inventory/attachments/last-item-number') ?>', { type: 'battery', prefix: _batAddPrefix }, function(r) {
        if (r.success) { $('#add-item-number-battery').val(r.suggested); $('#add-battery-item-hint').html('Generated: <strong>'+r.suggested+'</strong> | Terakhir: '+(r.last||'belum ada')); }
    });
}

// ═══════════════════════════════════════════════════════════
//  ADD CHARGER CASCADE
// ═══════════════════════════════════════════════════════════

function openAddChargerModal() {
    $('#addChargerForm')[0].reset();
    initAddChargerCascade();
    loadUnitsForAddModal('#add-chg-unit-id', '#addChargerModal');
    $('#addChargerModal').modal('show');
}

function initAddChargerCascade() {
    $('#add-chg-merk').val('');
    $('#add-charger-id').val('').empty().append('<option value="">Pilih tipe...</option>').prop('disabled', true);
    $('#add-sn-charger, #add-item-number-charger, #add-btn-gen-charger').prop('disabled', true);
    $('#add-charger-last-hint').text('Kosongkan untuk otomatis saat simpan');
    $('#add-item-number-charger').val(''); $('#add-sn-charger').val('');

    $.get('<?= base_url('warehouse/inventory/attachments/master-merk/charger') ?>', function(r) {
        const $s = $('#add-chg-merk').empty().append('<option value="">Pilih merk...</option>');
        if (r.success) { (r.data||[]).forEach(function(it){ $s.append($('<option>').val(it.value).text(it.text)); }); }
    });

    $('#add-chg-merk').off('change.addchg').on('change.addchg', function() {
        const merk = $(this).val();
        $('#add-charger-id').val('').empty().append('<option value="">Pilih tipe...</option>').prop('disabled', true);
        $('#add-sn-charger, #add-item-number-charger, #add-btn-gen-charger').prop('disabled', true);
        $('#add-charger-last-hint').text('Kosongkan untuk otomatis saat simpan');
        if (!merk) { return; }
        $.get('<?= base_url('warehouse/inventory/attachments/master-tipe/charger') ?>', { merk: merk }, function(r) {
            const $s = $('#add-charger-id').empty().append('<option value="">Pilih tipe...</option>').prop('disabled', false);
            if (r.success) { (r.data||[]).forEach(function(it){ $s.append($('<option>').val(it.id).text(it.text)); }); }
        });
    });

    $('#add-charger-id').off('change.addchg').on('change.addchg', function() {
        if ($(this).val()) {
            $('#add-sn-charger, #add-item-number-charger, #add-btn-gen-charger').prop('disabled', false);
            $.get('<?= base_url('warehouse/inventory/attachments/last-item-number') ?>', { type: 'charger', prefix: 'C' }, function(r) { if (r.success) $('#add-charger-last-hint').html('Nomor terakhir <strong>C</strong>: <strong>'+(r.last||'belum ada')+'</strong>'); });
        } else {
            $('#add-sn-charger, #add-item-number-charger, #add-btn-gen-charger').prop('disabled', true);
        }
    });
}

function addGenChargerItemNumber() {
    if (!$('#add-charger-id').val()) { OptimaNotify.warning('Pilih tipe charger terlebih dahulu', 'Info'); return; }
    $.get('<?= base_url('warehouse/inventory/attachments/last-item-number') ?>', { type: 'charger', prefix: 'C' }, function(r) {
        if (r.success) { $('#add-item-number-charger').val(r.suggested); $('#add-charger-last-hint').html('Generated: <strong>'+r.suggested+'</strong> | Terakhir: '+(r.last||'belum ada')); }
    });
}

function loadUnitsForAddModal(selectSel, modalSel) {
    loadUnitsForModal(selectSel, modalSel, 'Ketik no. unit jika sudah terpasang...');
}
</script>
<?= $this->endSection() ?>
