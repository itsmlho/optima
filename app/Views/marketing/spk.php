<?= $this->extend('layouts/base') ?>

<?php
/**
 * SPK Marketing Module
 * 
 * BADGE SYSTEM: Menggunakan Optima Badge Standards (optima-pro.css)
 * Direct CSS classes - tidak perlu JavaScript helper function
 * 
 * Quick Reference:
 * - Status SUBMITTED   → <span class="badge badge-soft-yellow">SUBMITTED</span>
 * - Status IN_PROGRESS → <span class="badge badge-soft-cyan">IN_PROGRESS</span>
 * - Status READY       → <span class="badge badge-soft-blue">READY</span>
 * - Status COMPLETED   → <span class="badge badge-soft-green">COMPLETED</span>
 * - Status CANCELLED   → <span class="badge badge-soft-red">CANCELLED</span>
 * - Type UNIT          → <span class="badge badge-soft-blue">UNIT</span>
 * - Type ATTACHMENT    → <span class="badge badge-soft-orange">ATTACHMENT</span>
 * - Source QUOTATION   → <span class="badge badge-soft-purple">QUOTATION</span>
 * - Source CONTRACT    → <span class="badge badge-soft-green">CONTRACT</span>
 * 
 * See optima-pro.css line ~2030 for complete badge standards
 */

// Load global permission helper
helper('global_permission');

// Get permissions for marketing module
$permissions = get_global_permission('marketing');
$can_view = $permissions['view'];
$can_create = $permissions['create'];
$can_edit = $permissions['edit'];
$can_delete = $permissions['delete'];
$can_export = $permissions['export'];
?>

<?= $this->section('content') ?>

    
    <?php if (!$can_view): ?>
    <div class="alert alert-warning">
        <i class="fas fa-lock me-2"></i>
        <strong><?= lang('Marketing.access_denied') ?>:</strong> <?= lang('Marketing.no_permission_view') ?>. 
        <?= lang('Marketing.contact_administrator') ?>.
    </div>
    <?php else: ?>
    
    <!-- Statistics Cards -->
    <div class="row mt-3 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-primary-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-file-text stat-icon text-primary"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-total-spk">0</div>
                        <div class="text-muted"><?= lang('Marketing.total_spk') ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-warning-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-clock stat-icon text-warning"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-in-progress">0</div>
                        <div class="text-muted"><?= lang('Marketing.in_progress') ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-success-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-check-circle stat-icon text-success"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-ready">0</div>
                        <div class="text-muted"><?= lang('Marketing.ready') ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-info-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-check-all stat-icon text-info"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-completed">0</div>
                        <div class="text-muted"><?= lang('Marketing.completed') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="card table-card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="card-title mb-0">
                    <i class="bi bi-file-earmark-text me-2 text-primary"></i>
                    <?= lang('Marketing.spk_list') ?>
                </h5>
                <p class="text-muted small mb-0">
                    Kelola dan pantau Surat Perintah Kerja (SPK) untuk fabrication dan delivery unit
                    <span class="ms-2 text-info">
                        <i class="bi bi-info-circle me-1"></i>
                        <small>Tip: Click baris untuk melihat detail SPK</small>
                    </span>
                </p>
            </div>
            <div class="d-flex gap-2">
                <?php if ($can_create): ?>
                <?= ui_button('add', lang('Marketing.create_spk'), [
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#spkModal',
                    'size' => 'sm'
                ]) ?>
                <?php else: ?>
                <?= ui_button('add', lang('Marketing.create_spk'), [
                    'size' => 'sm',
                    'disabled' => true,
                    'title' => lang('Marketing.access_denied')
                ]) ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Filter Tabs -->
        <ul class="nav nav-tabs mb-3" id="filterTabs">
            <li class="nav-item">
                <a class="nav-link active filter-tab" href="#" data-filter="all"><?= lang('Marketing.all') ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link filter-tab" href="#" data-filter="SUBMITTED"><?= lang('Marketing.submitted') ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link filter-tab" href="#" data-filter="IN_PROGRESS"><?= lang('Marketing.in_progress') ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link filter-tab" href="#" data-filter="READY"><?= lang('Marketing.ready') ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link filter-tab" href="#" data-filter="COMPLETED"><?= lang('Marketing.completed') ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link filter-tab" href="#" data-filter="CANCELLED"><?= lang('Marketing.cancelled') ?></a>
            </li>
        </ul>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 table-manual-sort <?= !$can_view ? 'table-disabled' : '' ?>" id="spkList">
                    <thead class="table-light">
                        <tr>
                            <th><?= lang('Marketing.spk_number') ?></th>
                            <th><?= lang('Marketing.type') ?></th>
                            <th><?= lang('Marketing.contract_po') ?></th>
                            <th>Source</th>
                            <th><?= lang('Marketing.company_name') ?></th>
                            <th class="d-none d-lg-table-cell"><?= lang('Marketing.pic') ?></th>
                            <th class="d-none d-xl-table-cell"><?= lang('Marketing.contact') ?></th>
                            <th><?= lang('App.status') ?></th>
                            <th><?= lang('Marketing.total_units') ?></th>
                            <th data-no-sort><?= lang('Marketing.actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data loaded via JavaScript -->
                    </tbody>
                </table>
            </div>
            <!-- DataTables will add pagination and info here automatically -->
        </div>
    </div>

    <div class="modal fade modal-wide" id="spkModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header"><h6 class="modal-title"><?= lang('Marketing.create_spk') ?></h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <form id="spkForm">
                    <div class="modal-body">
                        <!-- Step Progress Indicator -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center">
                                <div class="step-indicator active" id="step1Indicator">
                                    <span class="step-number">1</span>
                                    <span class="step-label">Type & Contract</span>
                                </div>
                                <div class="step-line"></div>
                                <div class="step-indicator" id="step2Indicator">
                                    <span class="step-number">2</span>
                                    <span class="step-label">Target Unit</span>
                                </div>
                                <div class="step-line"></div>
                                <div class="step-indicator" id="step3Indicator">
                                    <span class="step-number">3</span>
                                    <span class="step-label">Specification</span>
                                </div>
                            </div>
                        </div>

                        <!-- Step 1: SPK Type & Contract -->
                        <div class="card border-primary mb-3" id="step1Card">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-list-alt me-2"></i>Step 1: SPK Type & Contract</h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>New Workflow:</strong> SPK creation now uses specifications from <strong>Quotations</strong>.<br>
                                    <small>📋 Create Quotation → Add Specifications → Mark as DEAL → Create Contract → <strong>Create SPK here</strong></small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-tag me-1"></i><?= lang('Marketing.type') ?> <span class="text-danger">*</span></label>
                                    <select class="form-select" name="jenis_spk" id="jenisSpkSelect" required>
                                        <option value="UNIT" selected><?= lang('Marketing.spk_unit') ?> (New Unit Fabrication)</option>
                                        <option value="ATTACHMENT"><?= lang('Marketing.spk_attachment') ?> (Attachment Replacement Only)</option>
                                    </select>
                                    <div class="form-text" id="spkTypeHelp">
                                        <strong>UNIT:</strong> Full unit fabrication process (Unit Prep → Fabrication → Painting → PDI)<br>
                                        <strong>ATTACHMENT:</strong> Replace attachment on existing unit (Fabrication → Painting → PDI)
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-file-alt me-1"></i>Select DEAL Quotation <span class="text-danger">*</span></label>
                                    <select class="form-select" name="quotation_id" id="kontrakSelect" required>
                                        <option value="">-- Select DEAL Quotation --</option>
                                    </select>
                                    <div class="form-text">
                                        <i class="fas fa-check-circle text-success me-1"></i>Only <strong>DEAL quotations</strong> with customer & specifications are shown.
                                        <br><small class="text-muted">💡 Contract info displayed if available (optional)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Step 2: Contract Info -->
                        <div id="kontrakInfoSection" class="d-none">
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="card-title"><?= lang('Marketing.contract_information') ?></h6>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label"><?= lang('Marketing.customer') ?></label>
                                            <input class="form-control" name="pelanggan" id="inpPelanggan" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label"><?= lang('Marketing.contract_po_number') ?></label>
                                            <input class="form-control" name="po_kontrak_nomor" id="inpPoKontrak" readonly>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label"><?= lang('Marketing.select_location') ?> <span class="text-danger">*</span></label>
                                            <select class="form-select" name="customer_location_id" id="customerLocationSelect" required>
                                                <option value="">-- <?= lang('Marketing.select_location') ?> --</option>
                                            </select>
                                            <div class="form-text"><?= lang('Marketing.select_location_pic_autofill') ?></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label"><?= lang('Marketing.pic_person_in_charge') ?></label>
                                            <input class="form-control" name="pic" id="inpPic" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label"><?= lang('Marketing.pic_contact') ?></label>
                                            <input class="form-control" name="kontak" id="inpKontak" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label"><?= lang('Marketing.location_address') ?></label>
                                            <textarea class="form-control" name="lokasi" id="inpLokasi" rows="2" readonly></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label"><?= lang('Marketing.delivery_plan') ?> <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="delivery_plan" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Step 2: Target Unit Section (for ATTACHMENT only) -->
                        <div id="attachmentTargetSection" class="d-none">
                            <div class="card border-warning mb-3">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fas fa-bullseye me-2"></i>Step 2: Select Target Unit <span class="badge badge-soft-red">REQUIRED for ATTACHMENT</span></h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning mb-3">
                                        <i class="fas fa-info-circle me-2"></i><strong>Important:</strong> Select which unit will receive the new attachment.
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label"><i class="fas fa-forklift me-1"></i><?= lang('Marketing.target_unit') ?> <span class="text-danger">*</span></label>
                                        <div id="targetUnitLoading" class="d-none">
                                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                            <span>Loading units from contract...</span>
                                        </div>
                                        <select class="form-select" name="target_unit_id" id="targetUnitSelect">
                                            <option value="">- <?= lang('Marketing.select_target_unit') ?> -</option>
                                        </select>
                                        <div class="form-text"><?= lang('Marketing.select_unit_receive_replacement') ?></div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label"><i class="fas fa-comment-alt me-1"></i><?= lang('Marketing.replacement_reason') ?></label>
                                        <textarea class="form-control" name="replacement_reason" id="replacementReason" rows="2" 
                                                  placeholder="<?= lang('Marketing.replacement_reason_example') ?>"></textarea>
                                        <div class="form-text"><?= lang('Marketing.explain_attachment_replacement') ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Step 3: Select Specification -->
                        <div id="spesifikasiSection" class="d-none">
                            <div class="card border-success mb-3">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Step 3: Select Specification</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label"><i class="fas fa-clipboard-list me-1"></i><?= lang('Marketing.select_unit_specification') ?> <span class="text-danger">*</span></label>
                                        <select class="form-select" name="kontrak_spesifikasi_id" id="spesifikasiSelect" required>
                                            <option value="">-- <?= lang('Marketing.select_specification') ?> --</option>
                                        </select>
                                        <div class="form-text">
                                            <i class="fas fa-quote-left me-1"></i>Specifications loaded from <strong>Quotation</strong>. 
                                            Only available specs (not yet in SPK) are shown.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Specification Detail -->
                            <div id="spesifikasiDetail" class="d-none">
                                <div class="card border-primary mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Selected Specification Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="spesifikasiInfo">
                                            <!-- Will be populated with specification details -->
                                        </div>
                                        
                                        <!-- Attachment Inventory List (for SPK Attachment) -->
                                        <div id="attachmentInventoryList">
                                            <!-- Will be populated with attachment inventory when SPK type is ATTACHMENT -->
                                        </div>
                                        
                                        <div class="mt-3">
                                            <label class="form-label" for="jumlahUnitSpk" id="jumlahUnitLabel"><?= lang('Marketing.unit_quantity_for_spk') ?></label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="jumlah_unit" id="jumlahUnitSpk" min="1" required placeholder="<?= lang('Marketing.unit_quantity') ?>">
                                                <span class="input-group-text" id="maxUnitInfo"><?= lang('Marketing.from') ?> 0 <?= lang('Marketing.available') ?></span>
                                            </div>
                                            <div class="form-text" id="jumlahUnitFormText"><?= lang('Marketing.enter_unit_quantity_for_spk') ?></div>
                                            <div class="alert alert-info mt-2 d-none" id="attachmentQtyInfo">
                                                <i class="fas fa-info-circle me-2"></i>For ATTACHMENT SPK, quantity is fixed to <strong>1</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <label class="form-label"><?= lang('Marketing.spk_notes') ?></label>
                            <textarea class="form-control" name="catatan" rows="3" placeholder="<?= lang('Marketing.additional_notes_spk_optional') ?>"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <?= ui_button('cancel', lang('App.close'), ['data-bs-dismiss' => 'modal', 'color' => 'secondary']) ?>
                        <?= ui_button('submit', 'Create SPK', ['type' => 'submit', 'id' => 'submitSpkBtn', 'disabled' => true]) ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create DI Modal -->
    <div class="modal fade modal-wide" id="diModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h6 class="modal-title"><?= lang('Marketing.create') ?> Delivery Instruction</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <form id="diForm">
                    <div class="modal-body">
                        <input type="hidden" name="spk_id" id="diSpkId">
                        <div class="mb-2"><label class="form-label">SPK No.</label><input class="form-control" id="diNoSpk" readonly></div>
                        <div class="mb-2"><label class="form-label">Contract/PO</label><input class="form-control" id="diPoNo" readonly></div>
                        <div class="mb-2"><label class="form-label">Customer</label><input class="form-control" id="diPelanggan" readonly></div>
                        <div class="mb-2"><label class="form-label">Location</label><input class="form-control" id="diLokasi" readonly></div>
                        
                        <!-- NEW WORKFLOW: SPK Type -->
                        <div class="mb-2">
                            <label class="form-label">SPK Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="jenis_perintah_kerja_id" id="spkJenisPerintah" required>
                                <option value="">-- Select SPK Type --</option>
                                <!-- Options will be loaded dynamically -->
                            </select>
                            <div class="form-text">Determine the main action to be performed by the operational team</div>
                        </div>
                        
                        <!-- NEW WORKFLOW: SPK Purpose -->
                        <div class="mb-2">
                            <label class="form-label">SPK Purpose <span class="text-danger">*</span></label>
                            <select class="form-select" name="tujuan_perintah_kerja_id" id="spkTujuanPerintah" required disabled>
                                <option value="">-- Select SPK Type first --</option>
                            </select>
                            <div class="form-text">Reason/context for this SPK</div>
                        </div>
                        
                        <!-- EXCHANGE Workflow Section: PULL units from SPK contract -->
                        <div id="spkTukarWorkflow" class="d-none mb-3">
                            <div class="alert alert-info">
                                <i class="fas fa-exchange-alt"></i> 
                                <strong>EXCHANGE Workflow:</strong> Select units from contract to be pulled for replacement
                            </div>
                            
                            <!-- Unit PULL Section for EXCHANGE -->
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fas fa-minus-circle"></i> Units to PULL (from this SPK contract)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="small text-muted">Selected: <span id="spkTarikCount">0</span> units</div>
                                        <div class="d-flex gap-2">
                                            <?= ui_button('select-all', 'Select All', [
                                                'color' => 'outline-warning',
                                                'size' => 'sm',
                                                'type' => 'button',
                                                'id' => 'spkBtnSelectAllTarik'
                                            ]) ?>
                                            <?= ui_button('clear', 'Clear', [
                                                'color' => 'outline-secondary',
                                                'size' => 'sm',
                                                'type' => 'button',
                                                'id' => 'spkBtnClearTarik'
                                            ]) ?>
                                        </div>
                                    </div>
                                    <div id="spkTarikUnitList" class="unit-list max-h-200px-scroll">
                                        <div class="text-muted small">Loading units from contract...</div>
                                    </div>
                                    <div class="form-text">Selected units will be removed from contract (for replacement)</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <div id="diUnitsPick" class="mt-2 d-none">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong id="diPickLabel">Select Units to be Delivered</strong>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary" id="btnSelectAllUnits">Select All</button>
                                        <button type="button" class="btn btn-outline-secondary" id="btnClearUnits">Clear</button>
                                    </div>
                                </div>
                                <div id="diUnitsList" class="border rounded p-2 max-h-200px-scroll"></div>
                                <div class="form-text" id="diPickHelp">Check the units you want to include in this DI.</div>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6"><label class="form-label">Delivery Date</label><input type="date" class="form-control" name="tanggal_kirim"></div>
                            <div class="col-6 d-flex align-items-end"><span class="text-muted small">Optional</span></div>
                        </div>
                        <div class="mt-2"><label class="form-label">Notes</label><textarea class="form-control" name="catatan" rows="3" placeholder="Delivery instructions (optional)"></textarea></div>
                    </div>
                    <div class="modal-footer">
                        <?= ui_button('cancel', 'Cancel', ['data-bs-dismiss' => 'modal', 'color' => 'secondary']) ?>
                        <?= ui_button('submit', 'Create DI', ['type' => 'submit']) ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Link SPK to Contract Modal -->
    <div class="modal fade" id="linkContractModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">
                        <i class="fas fa-link me-2"></i>Link SPK to Contract
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="linkContractForm" onsubmit="submitLinkContract(event)">
                    <div class="modal-body">
                        <input type="hidden" id="link_spk_id" name="spk_id">
                        
                        <div class="mb-3">
                            <label class="form-label">SPK Number</label>
                            <input type="text" class="form-control" id="link_spk_number" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Select Contract <span class="text-danger">*</span></label>
                            <select class="form-select" id="link_contract_id" name="contract_id" required>
                                <option value="">-- Select Contract --</option>
                            </select>
                            <small class="text-muted">Choose the contract for this SPK</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">BAST Date (Optional)</label>
                            <input type="date" class="form-control" id="link_bast_date" name="bast_date">
                            <small class="text-muted">Berita Acara Serah Terima date</small>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> Linking this SPK to a contract will automatically update all related Delivery Instructions and unlock them for invoice generation.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <?= ui_button('cancel', 'Cancel', ['data-bs-dismiss' => 'modal', 'color' => 'secondary']) ?>
                        <?= ui_button('submit', 'Link Contract', ['type' => 'submit', 'icon' => 'fas fa-link']) ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // UI Badge Helper - Generate consistent badge colors based on type (Optima badge-soft-* system)
    function uiBadge(type, text, options = {}) {
        const badgeMap = {
            'active': 'badge-soft-green', 'approved': 'badge-soft-green', 'completed': 'badge-soft-green', 'delivered': 'badge-soft-green',
            'pending': 'badge-soft-yellow', 'ready': 'badge-soft-blue', 'in_progress': 'badge-soft-cyan', 'processing': 'badge-soft-cyan',
            'rejected': 'badge-soft-red', 'cancelled': 'badge-soft-red', 'failed': 'badge-soft-red', 'deleted': 'badge-soft-red',
            'draft': 'badge-soft-gray', 'new': 'badge-soft-blue', 'info': 'badge-soft-cyan', 'warning': 'badge-soft-yellow',
            'created': 'badge-soft-green', 'updated': 'badge-soft-cyan', 'submitted': 'badge-soft-gray', 'success': 'badge-soft-green',
            'primary': 'badge-soft-blue', 'secondary': 'badge-soft-gray', 'danger': 'badge-soft-red',
            'quotation': 'badge-soft-purple', 'contract': 'badge-soft-green'
        };
        const cls = options.softClass || badgeMap[type.toLowerCase()] || 'badge-soft-gray';
        const extraClass = options.class || '';
        const icon = options.icon ? `<i class="${options.icon} me-1"></i>` : '';
        return `<span class="badge ${cls} ${extraClass}">${icon}${text}</span>`;
    }

    // Map status to Optima badge-soft-* classes per entity
    function statusBadge(entity, status){
        const s = (status||'').toUpperCase();
        const mapSPK = { SUBMITTED:'badge-soft-gray', IN_PROGRESS:'badge-soft-cyan', READY:'badge-soft-blue', DELIVERED:'badge-soft-green', COMPLETED:'badge-soft-green', CANCELLED:'badge-soft-red' };
        const mapDI  = { SUBMITTED:'badge-soft-gray', DISPATCHED:'badge-soft-cyan', ARRIVED:'badge-soft-green', CANCELLED:'badge-soft-red' };
        const cls = (entity==='DI'?mapDI[s]:mapSPK[s]) || 'badge-soft-gray';
        return `<span class="badge ${cls}">${status}</span>`;
    }
    
    // Step indicator controller for Create SPK modal
    function setSpkStep(step) {
        const step1 = document.getElementById('step1Indicator');
        const step2 = document.getElementById('step2Indicator');
        const step3 = document.getElementById('step3Indicator');

        if (!step1 || !step2 || !step3) return;

        step1.classList.toggle('active', step >= 1);
        step2.classList.toggle('active', step >= 2);
        step3.classList.toggle('active', step >= 3);
    }
    // Global function for SPK TUKAR workflow unit count (must be global for onchange access)
    function updateSpkTarikCount() {
        const checked = document.querySelectorAll('.spk-tarik-unit-check:checked');
        const countElement = document.getElementById('spkTarikCount');
        if (countElement) {
            countElement.textContent = checked.length;
        }
    }
    
    // Global variables for filtering
    let spkTable; // DataTable instance
    let currentFilter = 'all';
    
    // Load statistics from server
    function loadStatistics() {
        $.ajax({
            url: '<?= base_url('marketing/spk/stats') ?>',
            type: 'POST',
            data: { 
                status_filter: currentFilter,
                [window.csrfTokenName]: window.csrfToken || ''
            },
            beforeSend: function(xhr) {
                if (window.csrfToken) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', window.csrfToken);
                }
            },
            success: function(data) {
                $('#stat-total-spk').text(data.total || 0);
                $('#stat-in-progress').text(data.in_progress || 0);
                $('#stat-ready').text(data.ready || 0);
                $('#stat-completed').text(data.completed || 0);
            },
            error: function(xhr) {
                console.error('Failed to load statistics:', xhr.responseText);
            }
        });
    }
    
    // Filter SPK data based on status (updated for DataTables)
    function filterSpkData(filter) {
        currentFilter = filter;
        
        // Update active card/tab styling
        document.querySelectorAll('.filter-card, .filter-tab').forEach(el => {
            el.classList.remove('active');
        });
        
        const activeElement = document.querySelector(`[data-filter="${filter}"]`);
        if (activeElement) {
            activeElement.classList.add('active');
        }
        
        // Reload DataTable with new filter (server handles it)
        if (spkTable && spkTable.ajax) {
            spkTable.ajax.reload();
        }
    }
    
    // OLD MANUAL RENDERING CODE REMOVED
    // renderSpkTable(), changePage(), updatePagination() removed
    // DataTables handles all rendering, pagination, and sorting automatically
    
    function oldRenderForReference_NOT_USED() {
        // This code is kept for reference only but not executed
        // DataTables now handles all this automatically
        const tb = document.querySelector('#spkList tbody');
        const startIndex = 0;
        const endIndex = 0;
        const paginatedData = [];
        
        paginatedData.forEach(r=>{
            const tr = document.createElement('tr');
            const diBtn = (r.status === 'READY')
              ? `<button class="btn btn-sm btn-primary buat-di" data-id="${r.id}" data-spk='${JSON.stringify({id:r.id, nomor_spk:r.nomor_spk, po:r.po_kontrak_nomor, pelanggan:r.pelanggan, lokasi:r.lokasi}).replace(/'/g,"&apos;")}' title="Create DI">Create DI</button>`
              : '';
            
            // Determine source type based on kontrak_id (since source_type column doesn't exist)
            // If kontrak_id is NULL → created from Quotation without contract → QUOTATION
            // If kontrak_id has value → created with contract → CONTRACT
            const sourceType = (r.kontrak_id === null || r.kontrak_id === '') ? 'QUOTATION' : 'CONTRACT';
            const sourceBadge = sourceType === 'QUOTATION' 
              ? uiBadge('quotation', 'QUOTATION', {icon: 'fas fa-file-lines'})
              : uiBadge('contract', 'CONTRACT', {icon: 'fas fa-file-contract'});
            
            // Link to Contract button (only for QUOTATION source without contract)
            const linkBtn = (sourceType === 'QUOTATION' && !r.contract_linked_at)
              ? `<button class="btn btn-sm btn-outline-warning link-contract" data-spk-id="${r.id}" data-spk-number="${r.nomor_spk}" title="Link to Contract"><i class="fas fa-link"></i> Link</button> `
              : '';
            
            const aksiBtn = linkBtn + (diBtn || '<span class="text-muted">-</span>');
            
            tr.innerHTML = `<td><a href="#" onclick=\"openDetail(${r.id});return false;\">${r.nomor_spk}</a></td>`+
              `<td><span class=\"badge ${(r.jenis_spk||'UNIT')==='ATTACHMENT'?'badge-soft-orange':'badge-soft-blue'}\">${r.jenis_spk||'UNIT'}</span></td>`+
              `<td>${r.po_kontrak_nomor||'-'}</td>`+
              `<td>${sourceBadge}</td>`+
              `<td>${r.pelanggan||'-'}</td>`+
              `<td>${r.pic||'-'}</td>`+
              `<td>${r.kontak||'-'}</td>`+
              `<td>${statusBadge('SPK', r.status)}</td>`+
              `<td>${r.jumlah_unit||'-'}</td>`+
              `<td>${aksiBtn}</td>`;
            tb.appendChild(tr);
        });
        
        // Wire up Link Contract buttons
        tb.querySelectorAll('.link-contract').forEach(btn=>{
            btn.addEventListener('click', (e)=>{
                const spkId = e.currentTarget.getAttribute('data-spk-id');
                const spkNumber = e.currentTarget.getAttribute('data-spk-number');
                showLinkContractModal(spkId, spkNumber);
            });
        });
        
        // Wire up Buat DI buttons
        tb.querySelectorAll('.buat-di').forEach(btn=>{
            btn.addEventListener('click', (e)=>{
                const data = JSON.parse(e.currentTarget.getAttribute('data-spk').replace(/&apos;/g, "'"));
                document.getElementById('diSpkId').value = data.id || '';
                document.getElementById('diNoSpk').value = data.nomor_spk || '';
                document.getElementById('diPoNo').value = data.po || '';
                document.getElementById('diPelanggan').value = data.pelanggan || '';
                document.getElementById('diLokasi').value = data.lokasi || '';
                const diPicEl = document.getElementById('diPic');
                if (diPicEl) diPicEl.value = data.pic || '';
                const diKontakEl = document.getElementById('diKontak');
                if (diKontakEl) diKontakEl.value = data.kontak || '';
                // Load selected items summary or prepared units list
                const sum = document.getElementById('diSelectedSummary');
                const pickWrap = document.getElementById('diUnitsPick');
                const list = document.getElementById('diUnitsList');
                if (sum) { sum.innerHTML = '<span class="text-muted">Loading selected items...</span>'; }
                if (pickWrap) { pickWrap.style.display = 'none'; list.innerHTML = ''; }
                fetch(`<?= base_url('marketing/spk/detail/') ?>${data.id}`).then(r=>r.json()).then(j=>{
                    if (!(j && j.success)) { if(sum) sum.innerHTML = '<span class="text-danger">Failed to load item summary.</span>'; return; }
                    
                    // ENHANCEMENT: Detect SPK type for dynamic labels
                    const spkType = j && j.jenis_spk ? j.jenis_spk.toUpperCase() : 'UNIT';
                    const isAttachmentSpk = (spkType === 'ATTACHMENT');
                    
                    // Update labels based on SPK type
                    const pickLabel = document.getElementById('diPickLabel');
                    const pickHelp = document.getElementById('diPickHelp');
                    if (pickLabel) {
                        pickLabel.textContent = isAttachmentSpk ? 'Select Attachments to be Delivered' : 'Select Units to be Delivered';
                    }
                    if (pickHelp) {
                        pickHelp.textContent = isAttachmentSpk ? 'Check the attachments you want to include in this DI.' : 'Check the units you want to include in this DI.';
                    }
                    
                    console.log('✅ SPK page - Type:', spkType, 'isAttachment:', isAttachmentSpk);
                    
                    const s = j.spesifikasi || {};
                    
                    // Enhanced attachment detection for ATTACHMENT SPK (following di.php logic)
                    if (isAttachmentSpk) {
                        console.log('🔍 DEBUG SPK ATTACHMENT - using di.php logic approach');
                        
                        // For ATTACHMENT SPK, check selected attachment from spesifikasi (like di.php)
                        const selected = j && j.spesifikasi && j.spesifikasi.selected ? j.spesifikasi.selected : {};
                        console.log('🔍 DEBUG spesifikasi.selected:', selected);
                        
                        // Check multiple possible attachment data locations (following di.php logic)
                        let attachmentData = null;
                        if (selected.attachment) {
                            attachmentData = selected.attachment;
                            console.log('✅ Found attachment in selected.attachment (spk.php):', attachmentData);
                        } else if (selected.inventory_attachment_id) {
                            // Try to use inventory_attachment_id if available
                            attachmentData = {
                                id: selected.inventory_attachment_id,
                                label: 'Attachment Item',
                                tipe: 'Attachment',
                                merk: '-'
                            };
                            console.log('✅ Found attachment via inventory_attachment_id (spk.php):', selected.inventory_attachment_id);
                        } else if (j.spesifikasi.attachment_merk || j.spesifikasi.attachment_tipe) {
                            // Fallback to basic attachment info from spesifikasi
                            attachmentData = {
                                id: 'att_' + (j.data?.id || '1'),
                                label: j.spesifikasi.attachment_merk || 'Attachment Item',
                                tipe: j.spesifikasi.attachment_tipe || 'Attachment',
                                merk: j.spesifikasi.attachment_merk || '-'
                            };
                            console.log('✅ Created attachment from spesifikasi fields (spk.php):', attachmentData);
                        }
                        
                        if (attachmentData) {
                            // Show attachment item for ATTACHMENT SPK (same as di.php approach)
                            const attachLabel = attachmentData.label || 'Attachment Item';
                            const attachInfo = attachmentData.tipe ? ` (${attachmentData.tipe} - ${attachmentData.merk || '-'})` : '';
                            const html = `<ul class=\"mb-0\"><li>📎 Attachment: ${attachLabel}${attachInfo}</li></ul>`;
                            if (sum) sum.innerHTML = html;
                            console.log('✅ ATTACHMENT SPK summary displayed (spk.php):', attachLabel);
                            
                            // Also create checkbox list for consistency  
                            if (pickWrap && list) {
                                pickWrap.style.display = 'block';
                                const attachId = attachmentData.id || 'att1';
                                list.innerHTML = `<div class=\"form-check\"><input class=\"form-check-input di-unit-check\" type=\"checkbox\" value=\"${attachId}\" id=\"di_attach_${attachId}\" checked><label class=\"form-check-label\" for=\"di_attach_${attachId}\">1. 📎 ${attachLabel}${attachInfo}</label></div>`;
                                
                                // Select all / clear buttons
                                const btnAll = document.getElementById('btnSelectAllUnits');
                                const btnClr = document.getElementById('btnClearUnits');
                                if (btnAll) btnAll.onclick = ()=>{ document.querySelectorAll('.di-unit-check').forEach(ch=>ch.checked=true); };
                                if (btnClr) btnClr.onclick = ()=>{ document.querySelectorAll('.di-unit-check').forEach(ch=>ch.checked=false); };
                            }
                        } else {
                            // No attachment data found
                            const html = '<div class="text-danger small">No attachment has been prepared for this ATTACHMENT SPK yet.</div>';
                            if (sum) sum.innerHTML = html;
                            console.log('❌ No attachment data found for SPK ATTACHMENT (spk.php)');
                        }
                        return; // Exit early for ATTACHMENT SPK - don't process prepared_units_detail
                    }
                    
                    // If prepared_units_detail exists (multi-unit), render selectable list
                    const details = Array.isArray(s.prepared_units_detail) ? s.prepared_units_detail : [];
                    
                    if (details.length > 0 && pickWrap && list) {
                        pickWrap.style.display = 'block';
                        
                        // Standard unit rendering for UNIT SPK only (ATTACHMENT SPK already handled above)
                        list.innerHTML = details.map((it,idx)=>{
                            const label = (it.unit_label || `${it.no_unit||'-'} - ${it.merk_unit||'-'} ${it.model_unit||''}`);
                            const sn = it.serial_number ? ` [SN: ${it.serial_number}]` : '';
                            const isInActiveDI = it.is_in_active_di || false;
                            const activeDI = it.active_di_info || null;
                            const disabled = isInActiveDI ? 'disabled' : '';
                            const checked = isInActiveDI ? '' : 'checked';
                            const warningText = isInActiveDI && activeDI ? ` ${uiBadge('warning', `Already in ${activeDI.nomor_di}`)}` : '';
                            return `<div class=\"form-check\"><input class=\"form-check-input di-unit-check\" type=\"checkbox\" value=\"${it.unit_id}\" id=\"di_unit_${it.unit_id}\" ${checked} ${disabled}><label class=\"form-check-label\" for=\"di_unit_${it.unit_id}\">${idx+1}. ${label}${sn}${warningText}</label></div>`;
                        }).join('');
                        // Summary
                        const itemType = isAttachmentSpk ? 'attachment' : 'unit';
                        if (sum) sum.innerHTML = `<span class=\"text-success\">${details.length} ${itemType} prepared by Service. Please select units to be delivered.</span>`;
                        // Select all / clear (skip disabled units)
                        const btnAll = document.getElementById('btnSelectAllUnits');
                        const btnClr = document.getElementById('btnClearUnits');
                        if (btnAll) btnAll.onclick = ()=>{ document.querySelectorAll('.di-unit-check:not(:disabled)').forEach(ch=>ch.checked=true); };
                        if (btnClr) btnClr.onclick = ()=>{ document.querySelectorAll('.di-unit-check:not(:disabled)').forEach(ch=>ch.checked=false); };
                    } else {
                        // Standard UNIT SPK handling (original logic) - ATTACHMENT SPK already handled above
                        const u = s.selected && s.selected.unit ? s.selected.unit : null;
                        const a = s.selected && s.selected.attachment ? s.selected.attachment : null;
                        const unit = u ? `${u.no_unit||'-'} - ${u.merk_unit||'-'} ${u.model_unit||''} @ ${u.lokasi_unit||'-'}${u.serial_number?` [SN: ${u.serial_number}]`:''}` : null;
                        const att  = a ? `${a.tipe||'-'} ${a.merk||''} ${a.model||''}${a.sn_attachment?` [SN: ${a.sn_attachment}]`:''}${a.lokasi_penyimpanan?` @ ${a.lokasi_penyimpanan}`:''}` : null;
                        const html = `<ul class=\"mb-0\">${unit?`<li>Unit: ${unit}</li>`:''}${att?`<li>Attachment: ${att}</li>`:''}</ul>`;
                        if (sum) sum.innerHTML = (unit || att) ? html : '<span class="text-muted">No item has been assigned by Service yet.</span>';
                    }
                });
                const modal = new bootstrap.Modal(document.getElementById('diModal'));
                modal.show();
            });
        });
    }
    
    // OLD FUNCTION REMOVED: loadSpk() - replaced with DataTables server-side reload
    // Use spkTable.ajax.reload() instead
    
    function loadKontrakOptions(q){
        const url = new URL('<?= base_url('marketing/spk/kontrak-options') ?>', window.location.origin);
        if(q) url.searchParams.set('q', q);
        const jenisSpkElement = document.querySelector('select[name="jenis_spk"]');
        const jenis = jenisSpkElement ? jenisSpkElement.value : 'UNIT';
        const kontrakStatus = (jenis === 'TUKAR') ? 'ACTIVE' : 'PENDING';
        url.searchParams.set('status', kontrakStatus);
        fetch(url).then(r=>r.json()).then(j=>{
            const dl = document.getElementById('kontrakOptions');
            if (!dl) return; // Skip if kontrakOptions element doesn't exist
            dl.innerHTML = '';
            (j.data||[]).forEach(opt=>{
                const o = document.createElement('option');
                o.value = opt.customer_po_number || opt.no_kontrak || '';
                o.label = opt.label;
                dl.appendChild(o);
            });
        });
    }
    function loadMonitoring(){
        fetch('<?= base_url('marketing/spk/monitoring') ?>').then(r=>r.json()).then(j=>{
            const tb = document.querySelector('#monitoringTable tbody');
            if (!tb) return; // Skip if monitoring table doesn't exist
            tb.innerHTML = '';
            (j.data||[]).forEach(r=>{
                const tr = document.createElement('tr');
                const fmt = (v)=> v==null?0:v;
                tr.innerHTML = `
                    <td>${r.no_kontrak||'-'}</td>
                    <td>${r.customer_po_number||'-'}</td>
                    <td>${r.pelanggan||'-'}</td>
                    <td>${r.lokasi||'-'}</td>
                    <td>${uiBadge('dark', fmt(r.total_spk))}</td>
                    <td>${uiBadge('submitted', fmt(r.submitted))}</td>
                    <td>${uiBadge('in_progress', fmt(r.in_progress))}</td>
                    <td>${uiBadge('ready', fmt(r.ready))}</td>
                    <td>${uiBadge('delivered', fmt(r.delivered))}</td>
                    <td>${uiBadge('cancelled', fmt(r.cancelled))}</td>
                    <td>${r.last_update||'-'}</td>`;
                tb.appendChild(tr);
            });
        });
    }
    document.addEventListener('DOMContentLoaded',()=>{
    // Add global error handler for better debugging
    window.addEventListener('error', function(e) {
        console.error('Global error caught:', e.error, e.filename, e.lineno, e.colno);
    });
    
    window.addEventListener('unhandledrejection', function(e) {
        console.error('Unhandled promise rejection:', e.reason);
    });

    // Initialize DataTable for SPK list
    try {
        spkTable = OptimaDataTable.init('#spkList', {
            ajax: {
                url: '<?= base_url('marketing/spk/data') ?>',
                type: 'POST',
                data: function(d) {
                    d.status_filter = currentFilter;
                    return d;
                },
                error: function(xhr) {
                    console.error('❌ SPK DataTable error:', xhr.responseText);
                    if (typeof showNotification === 'function') {
                        showNotification('Failed to load SPK data', 'error');
                    }
                }
            },
            serverSide: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[0, 'desc']], // Latest first
            columns: [
                { 
                    data: 'nomor_spk',
                    render: function(data, type, row) {
                        return `<a href="#" onclick="openDetail(${row.id});return false;">${data}</a>`;
                    }
                },
                { 
                    data: 'jenis_spk',
                    render: function(data) {
                        const val = data || 'UNIT';
                        const cls = val === 'ATTACHMENT' ? 'badge-soft-orange' : 'badge-soft-blue';
                        return `<span class="badge ${cls}">${val}</span>`;
                    }
                },
                { data: 'po_kontrak_nomor', defaultContent: '-' },
                { 
                    data: 'kontrak_id',
                    name: 'source',
                    orderable: false,
                    render: function(data) {
                        const sourceType = !data ? 'QUOTATION' : 'CONTRACT';
                        return sourceType === 'QUOTATION' 
                            ? typeof uiBadge === 'function' ? uiBadge('quotation', 'QUOTATION', {icon: 'fas fa-file-lines'}) : '<span class="badge badge-soft-purple">QUOTATION</span>'
                            : typeof uiBadge === 'function' ? uiBadge('contract', 'CONTRACT', {icon: 'fas fa-file-contract'}) : '<span class="badge badge-soft-green">CONTRACT</span>';
                    }
                },
                { data: 'pelanggan', defaultContent: '-' },
                { 
                    data: 'pic', 
                    defaultContent: '-',
                    className: 'd-none d-lg-table-cell', // Hide on small/medium screens
                    responsivePriority: 3
                },
                { 
                    data: 'kontak', 
                    defaultContent: '-',
                    className: 'd-none d-xl-table-cell', // Hide on small/medium/large screens
                    responsivePriority: 4
                },
                { 
                    data: 'status',
                    render: function(data) {
                        return typeof statusBadge === 'function' ? statusBadge('SPK', data) : `<span class="badge">${data}</span>`;
                    }
                },
                { data: 'jumlah_unit', defaultContent: '-' },
                { 
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let actions = '';
                        
                        // Link Contract button (if quotation source without contract)
                        if (!row.kontrak_id && !row.contract_linked_at) {
                            actions += `<button class="btn btn-sm btn-outline-warning link-contract" data-spk-id="${row.id}" data-spk-number="${row.nomor_spk}" title="Link to Contract">
                                <i class="fas fa-link"></i> Link
                            </button> `;
                        }
                        
                        // Create DI button (if ready status)
                        if (row.status === 'READY') {
                            const spkData = JSON.stringify({
                                id: row.id,
                                nomor_spk: row.nomor_spk,
                                po: row.po_kontrak_nomor,
                                pelanggan: row.pelanggan,
                                lokasi: row.lokasi
                            }).replace(/'/g, "&apos;");
                            
                            actions += `<button class="btn btn-sm btn-primary buat-di" data-id="${row.id}" data-spk='${spkData}' title="Create DI">
                                Create DI
                            </button>`;
                        }
                        
                        return actions || '<span class="text-muted">-</span>';
                    }
                }
            ],
            drawCallback: function(settings, json) {
                console.log('✅ SPK DataTable drawn, rows:', settings.aiDisplay.length);
                
                // Load statistics after table draw
                loadStatistics();
                
                // Wire up action buttons (Link Contract)
                $('#spkList tbody').off('click', '.link-contract').on('click', '.link-contract', function() {
                    const spkId = $(this).data('spk-id');
                    const spkNumber = $(this).data('spk-number');
                    showLinkContractModal(spkId, spkNumber);
                });
                
                // Wire up Create DI buttons
                $('#spkList tbody').off('click', '.buat-di').on('click', '.buat-di', function() {
                    const spkData = JSON.parse($(this).attr('data-spk').replace(/&apos;/g, "'"));
                    
                    // Populate DI modal fields
                    $('#diSpkId').val(spkData.id || '');
                    $('#diNoSpk').val(spkData.nomor_spk || '');
                    $('#diPoNo').val(spkData.po || '');
                    $('#diPelanggan').val(spkData.pelanggan || '');
                    $('#diLokasi').val(spkData.lokasi || '');
                    
                    const diPicEl = $('#diPic');
                    const diKontakEl = $('#diKontak');
                    if (diPicEl.length) diPicEl.val(spkData.pic || '');
                    if (diKontakEl.length) diKontakEl.val(spkData.kontak || '');
                    
                    // Load selected items summary
                    const sum = $('#diSelectedSummary');
                    const pickWrap = $('#diUnitsPick');
                    const list = $('#diUnitsList');
                    
                    if (sum.length) sum.html('<span class="text-muted">Loading selected items...</span>');
                    if (pickWrap.length) {
                        pickWrap.hide();
                        list.html('');
                    }
                    
                    // Fetch SPK details for items
                    $.get('<?= base_url('marketing/spk/detail/') ?>' + spkData.id, function(response) {
                        if (!response || !response.success) {
                            if (sum.length) sum.html('<span class="text-danger">Failed to load item summary.</span>');
                            return;
                        }
                        
                        const j = response;
                        const spkType = (j.jenis_spk || 'UNIT').toUpperCase();
                        const isAttachmentSpk = (spkType === 'ATTACHMENT');
                        const s = j.spesifikasi || {};
                        
                        // Update labels based on SPK type
                        const pickLabel = $('#diPickLabel');
                        const pickHelp = $('#diPickHelp');
                        if (pickLabel.length) {
                            pickLabel.text(isAttachmentSpk ? 'Select Attachments to be Delivered' : 'Select Units to be Delivered');
                        }
                        if (pickHelp.length) {
                            pickHelp.text(isAttachmentSpk ? 'Check the attachments you want to include in this DI.' : 'Check the units you want to include in this DI.');
                        }
                        
                        // Handle prepared units detail (multi-unit SPK)
                        const details = Array.isArray(s.prepared_units_detail) ? s.prepared_units_detail : [];
                        
                        if (details.length > 0 && pickWrap.length && list.length) {
                            pickWrap.show();
                            
                            const itemsHtml = details.map((it, idx) => {
                                const label = it.unit_label || `${it.no_unit||'-'} - ${it.merk_unit||'-'} ${it.model_unit||''}`;
                                const sn = it.serial_number ? ` [SN: ${it.serial_number}]` : '';
                                const isInActiveDI = it.is_in_active_di || false;
                                const disabled = isInActiveDI ? 'disabled' : '';
                                const checked = isInActiveDI ? '' : 'checked';
                                
                                return `<div class="form-check">
                                    <input class="form-check-input di-unit-check" type="checkbox" value="${it.unit_id}" id="di_unit_${it.unit_id}" ${checked} ${disabled}>
                                    <label class="form-check-label" for="di_unit_${it.unit_id}">${idx+1}. ${label}${sn}</label>
                                </div>`;
                            }).join('');
                            
                            list.html(itemsHtml);
                            
                            if (sum.length) {
                                sum.html(`<span class="text-success">${details.length} unit(s) prepared. Please select units to be delivered.</span>`);
                            }
                            
                            // Select all / clear buttons
                            $('#btnSelectAllUnits').off('click').on('click', function() {
                                $('.di-unit-check:not(:disabled)').prop('checked', true);
                            });
                            $('#btnClearUnits').off('click').on('click', function() {
                                $('.di-unit-check:not(:disabled)').prop('checked', false);
                            });
                        } else {
                            // Single unit/attachment SPK
                            const u = s.selected && s.selected.unit ? s.selected.unit : null;
                            const a = s.selected && s.selected.attachment ? s.selected.attachment : null;
                            
                            let html = '<ul class="mb-0">';
                            if (u) html += `<li>Unit: ${u.no_unit||'-'} - ${u.merk_unit||'-'} ${u.model_unit||''}</li>`;
                            if (a) html += `<li>Attachment: ${a.tipe||'-'} ${a.merk||''} ${a.model||''}</li>`;
                            html += '</ul>';
                            
                            if (sum.length) {
                                sum.html((u || a) ? html : '<span class="text-muted">No item has been assigned by Service yet.</span>');
                            }
                        }
                    });
                    
                    // Show DI modal
                    const diModal = new bootstrap.Modal(document.getElementById('diModal'));
                    diModal.show();
                });
            }
        });
        
        console.log('✅ Marketing SPK DataTable initialized successfully');
        
    } catch(error) {
        console.error('❌ Failed to initialize SPK DataTable:', error);
        if (typeof showNotification === 'function') {
            showNotification('Failed to initialize SPK table', 'error');
        }
    }

    // Page date filter initialization (for backward compatibility)
    initPageDateFilter({
        pickerId: 'spkDateRangePicker',
        onInit: function() {
            console.log('🚀 SPK: Date filter initialized');
            // DataTable already loaded via OptimaDataTable.init()
            loadKontrakOptions('');
            loadMonitoring();
        },
        onDateChange: function(startDate, endDate) {
            console.log('📅 SPK: Date filter changed', startDate, endDate);
            // TODO: Implement date filtering for DataTables if needed
            if (spkTable && spkTable.ajax) {
                spkTable.ajax.reload();
            }
        },
        onDateClear: function() {
            console.log('✖️ SPK: Date filter cleared');
            if (spkTable && spkTable.ajax) {
                spkTable.ajax.reload();
            }
        },
        debug: true
    });
    
    // Initialize SPK workflow dropdowns
    setupSpkWorkflowDropdowns();
    
    // Add filter card click listeners
    document.querySelectorAll('.filter-card').forEach(card => {
        card.addEventListener('click', (e) => {
            const filter = e.currentTarget.getAttribute('data-filter');
            filterSpkData(filter);
        });
    });
    
    // Add filter tab click listeners
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const filter = this.dataset.filter;
            filterSpkData(filter);
        });
    });
    
    // Remove old pagination/search handlers (DataTables handles this)
    // entriesPerPage and spkSearch are now managed by DataTables

    
    // Set default active filter (all)
    const defaultFilter = document.querySelector('[data-filter="all"]');
    if (defaultFilter) {
        defaultFilter.classList.add('active');
    }
    
    const kontrakInput = document.querySelector('input[name="po_kontrak_nomor"]');
    const pelangganInput = document.getElementById('inpPelanggan');
    const lokasiInput = document.getElementById('inpLokasi');
    kontrakInput.addEventListener('input', (e) => {
            const v = e.target.value.trim();
            // fetch as user types (debounce-lite)
            loadKontrakOptions(v);
            // try to find matching option and autofill pelanggan & lokasi from dataset
            const dl = document.getElementById('kontrakOptions');
            if (!dl) return; // Skip if kontrakOptions element doesn't exist
            const match = Array.from(dl.options).find(o => o.value === v);
            if (match) {
                // We can't store custom data in datalist options cross-browser reliably; parse from label first
                // Label format: "<no kontrak> (<no po>) - <pelanggan>"
                if (match.label) {
                    const parts = match.label.split(' - ');
                    if (parts[1]) {
                        pelangganInput.value = parts[1];
                    }
                }
            }
        });
        // Lokasi mengikuti perubahan Pelanggan secara langsung
        pelangganInput.addEventListener('input', ()=>{ /* do not mirror lokasi automatically anymore */ });

        // Override lokasi based on kontrak lookup when focus leaves kontrak field (fetch selected option’s lokasi via API)
        kontrakInput.addEventListener('change', () => {
            const v = kontrakInput.value.trim();
            const url = new URL('<?= base_url('marketing/spk/kontrak-options') ?>', window.location.origin);
            if (v) url.searchParams.set('q', v);
            const spkJenisSelect = document.querySelector('select[name="jenis_spk"]');
            const jenis = spkJenisSelect ? spkJenisSelect.value : 'UNIT';
            url.searchParams.set('status', (jenis === 'TUKAR') ? 'ACTIVE' : 'PENDING');
            fetch(url).then(r=>r.json()).then(j=>{
                const rows = j.data||[];
                // Try exact match by customer_po_number or no_kontrak
                const exact = rows.find(x => x.customer_po_number === v || x.no_kontrak === v);
                if (exact) {
                    if (exact.pelanggan) pelangganInput.value = exact.pelanggan;
                    if (exact.lokasi) lokasiInput.value = exact.lokasi;
                }
            });
        });

        // New SPK workflow based on quotation specifications (contract optional)
        const kontrakSelect = document.getElementById('kontrakSelect');
        const spesifikasiSelect = document.getElementById('spesifikasiSelect');
        const jumlahUnitInput = document.getElementById('jumlahUnitSpk');
        
        // Check URL parameters for pre-selected specification
        const urlParams = new URLSearchParams(window.location.search);
        const preSelectedSpekId = urlParams.get('spesifikasi_id');
        
        // Load DEAL quotations for SPK creation (contract optional)
        // Note: Variable name is kontrakSelect for backward compatibility, but now loads quotations
        function loadAvailableKontraks() {
            console.log('🔍 Loading DEAL quotations with specifications...');
            
            fetch('<?= base_url('marketing/kontrak/get-active-quotations-for-spk') ?>', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('📋 Received DEAL quotations:', data);
                    
                    let options = '<option value="">-- Select DEAL Quotation --</option>';
                    if (data.success && data.data && data.data.length > 0) {
                        data.data.forEach(quotation => {
                            // Build display label
                            const quotationNum = quotation.quotation_number || 'No Number';
                            const customerName = quotation.customer_name || quotation.prospect_name || 'Unknown Customer';
                            const contractNum = quotation.no_kontrak;
                            const contractStatus = quotation.contract_id ? `✅ Contract: ${contractNum}` : '⏳ Contract Pending';
                            const totalUnits = quotation.total_units || 0;
                            const availableUnits = quotation.available_units || 0;
                            const totalSpecs = quotation.total_specs || 0;
                            
                            // Display format: QT-xxx - Customer [9/10 units, 1 spec] - ✅ Contract
                            let label = `${quotationNum} - ${customerName}`;
                            label += ` [${availableUnits}/${totalUnits} units`;
                            if (totalSpecs > 1) {
                                label += `, ${totalSpecs} specs`;
                            }
                            label += `] - ${contractStatus}`;
                            
                            options += `<option value="${quotation.id_quotation}" 
                                data-contract="${quotation.contract_id || ''}" 
                                data-customer="${quotation.created_customer_id}"
                                data-units="${totalUnits}"
                                data-available="${availableUnits}">${label}</option>`;
                        });
                        
                        console.log('✅ Loaded ' + data.data.length + ' DEAL quotations');
                    } else {
                        options = '<option value="">⚠️ No DEAL quotations with available units</option>';
                        console.warn('⚠️ No DEAL quotations found. Please create quotation, add specs, and mark as DEAL first.');
                        
                        if (window.OptimaPro && typeof OptimaPro.showNotification === 'function') {
                            OptimaPro.showNotification('No DEAL quotations found. Create quotation → Add specifications → Mark as DEAL first.', 'info');
                        }
                    }
                    
                    if (kontrakSelect) {
                        kontrakSelect.innerHTML = options;
                    }
                    
                    // If we have a pre-selected specification, find and select its quotation
                    if (preSelectedSpekId) {
                        findAndSelectKontrakBySpekId(preSelectedSpekId);
                    }
                })
                .catch(error => {
                    console.error('❌ Error loading quotations:', error);
                    if (kontrakSelect) {
                        kontrakSelect.innerHTML = `<option value="">Error loading quotations: ${error.message}</option>`;
                    }
                });
        }
        
        // Find quotation by specification ID and auto-select
        function findAndSelectKontrakBySpekId(spekId) {
            fetch(`<?= base_url('marketing/kontrak/find-by-spesifikasi/') ?>${spekId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.kontrak_id) {
                        // Select the contract
                        kontrakSelect.value = data.kontrak_id;
                        // Trigger change event to load contract info and specifications
                        kontrakSelect.dispatchEvent(new Event('change'));
                        
                        // After specifications load, select the target specification
                        setTimeout(() => {
                            if (spesifikasiSelect && spesifikasiSelect.querySelector(`option[value="${spekId}"]`)) {
                                spesifikasiSelect.value = spekId;
                                spesifikasiSelect.dispatchEvent(new Event('change'));
                            }
                        }, 1000);
                    }
                })
                .catch(error => {
                    console.error('Error finding contract for specification:', error);
                });
        }
        
        // Handle quotation selection (kontrakSelect now contains quotation_id)
        if (kontrakSelect) {
            kontrakSelect.addEventListener('change', function() {
                const quotationId = this.value; // Now this is quotation_id, not kontrak_id
                const selectedOption = this.options[this.selectedIndex];
                
                if (quotationId) {
                    // Get contract_id and customer_id from data attributes
                    const contractId = selectedOption.dataset.contract || null;
                    const customerId = selectedOption.dataset.customer || null;
                    const availableUnits = selectedOption.dataset.available || 0;
                    
                    console.log('📋 Selected Quotation:', {quotationId, contractId, customerId, availableUnits});
                    
                    // Show sections immediately (don't wait for AJAX) - REMOVE d-none class
                    const kontrakInfoSection = document.getElementById('kontrakInfoSection');
                    const spesifikasiSection = document.getElementById('spesifikasiSection');
                    if (kontrakInfoSection) {
                        kontrakInfoSection.classList.remove('d-none');
                        kontrakInfoSection.style.display = ''; // Clear any inline style
                        console.log('✅ Step 2 (Customer Info) shown');
                    }
                    if (spesifikasiSection) {
                        spesifikasiSection.classList.remove('d-none');
                        spesifikasiSection.style.display = ''; // Clear any inline style
                        console.log('✅ Step 3 (Specifications) shown');
                    }
                    
                    // Load quotation info (customer, location, etc) - non-blocking
                    loadQuotationInfo(quotationId).catch(err => {
                        console.warn('⚠️ Quotation info failed to load, will use data from specs:', err);
                    });
                    
                    // Load specifications for this quotation
                    loadQuotationSpesifikasiForSpk(quotationId);
                    
                    // Load customer locations if customer_id is available
                    if (customerId) {
                        loadCustomerLocations(customerId);
                    }
                    
                    // Load units for ATTACHMENT if SPK type is ATTACHMENT
                    const jenisSpk = document.getElementById('jenisSpkSelect');
                    if (jenisSpk && jenisSpk.value === 'ATTACHMENT') {
                        if (contractId) {
                            // Contract exists, can load units
                            loadContractUnitsForAttachment(contractId);
                            
                            // Show attachment target section
                            const attachmentSection = document.getElementById('attachmentTargetSection');
                            const targetUnitSelect = document.getElementById('targetUnitSelect');
                            if (attachmentSection) {
                                attachmentSection.classList.remove('d-none');
                            }
                            if (targetUnitSelect) {
                                targetUnitSelect.setAttribute('required', 'required');
                            }
                        } else {
                            // No contract yet, show warning
                            showAttachmentContractWarning();
                        }
                    }
                } else {
                    // Hide sections - ADD d-none class back
                    const kontrakInfoSection = document.getElementById('kontrakInfoSection');
                    const spesifikasiSection = document.getElementById('spesifikasiSection');
                    const spesifikasiDetail = document.getElementById('spesifikasiDetail');
                    const submitSpkBtn = document.getElementById('submitSpkBtn');
                    const attachmentSection = document.getElementById('attachmentTargetSection');
                    const targetUnitSelect = document.getElementById('targetUnitSelect');
                    
                    if (kontrakInfoSection) kontrakInfoSection.classList.add('d-none');
                    if (spesifikasiSection) spesifikasiSection.classList.add('d-none');
                    if (attachmentSection) attachmentSection.classList.add('d-none');
                    if (targetUnitSelect) {
                        targetUnitSelect.removeAttribute('required');
                        targetUnitSelect.value = '';
                    }
                    if (spesifikasiDetail) spesifikasiDetail.classList.add('d-none');
                    if (submitSpkBtn) {
                        submitSpkBtn.disabled = true;
                        submitSpkBtn.classList.add('disabled');
                    }
                }
            });

            // Keep visual step indicator in sync with quotation selection
            kontrakSelect.addEventListener('change', function() {
                if (this.value) {
                    setSpkStep(2);
                } else {
                    setSpkStep(1);
                }
            });
        } else {
            console.error('kontrakSelect element not found');
        }
        
        // Load contract information
        function loadKontrakInfo(kontrakId) {
            fetch(`<?= base_url('marketing/kontrak/get-kontrak/') ?>${kontrakId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.data) {
                        const kontrak = data.data;
                        const inpPelanggan = document.getElementById('inpPelanggan');
                        const inpPoKontrak = document.getElementById('inpPoKontrak');
                        
                        if (inpPelanggan) inpPelanggan.value = kontrak.pelanggan || '';
                        if (inpPoKontrak) inpPoKontrak.value = kontrak.no_kontrak || '';
                        
                        // Load customer locations for this contract's customer
                        if (kontrak.customer_id) {
                            loadCustomerLocations(kontrak.customer_id);
                        }
                    } else {
                        console.error('No contract data received:', data);
                    }
                })
                .catch(error => {
                    console.error('Error loading contract info:', error);
                });
        }
        
        // Load customer locations
        function loadCustomerLocations(customerId) {
            const locationSelect = document.getElementById('customerLocationSelect');
            if (!locationSelect) return;
            
            locationSelect.innerHTML = '<option value="">Loading locations...</option>';
            
            fetch(`<?= base_url('marketing/kontrak/locations/') ?>${customerId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    
                    locationSelect.innerHTML = '<option value="">-- Select Location --</option>';
                    
                    if (data.success && data.data && data.data.length > 0) {
                        data.data.forEach(location => {
                            const option = document.createElement('option');
                            option.value = location.id;
                            option.textContent = `${location.location_name} - ${location.city || 'N/A'}`;
                            option.dataset.pic = location.contact_person || '';
                            option.dataset.phone = location.phone || '';
                            option.dataset.address = location.address || '';
                            option.dataset.city = location.city || '';
                            locationSelect.appendChild(option);
                        });
                    } else {
                        locationSelect.innerHTML = '<option value="">No locations available</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading locations:', error);
                    locationSelect.innerHTML = '<option value="">Error loading locations</option>';
                });
        }
        
        // Handle location selection
        const customerLocationSelect = document.getElementById('customerLocationSelect');
        if (customerLocationSelect) {
            customerLocationSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const inpPic = document.getElementById('inpPic');
                const inpKontak = document.getElementById('inpKontak');
                const inpLokasi = document.getElementById('inpLokasi');
                
                if (this.value && selectedOption) {
                    // Auto-fill PIC, Contact, and Location from selected location
                    if (inpPic) inpPic.value = selectedOption.dataset.pic || '';
                    if (inpKontak) inpKontak.value = selectedOption.dataset.phone || '';
                    if (inpLokasi) {
                        const address = selectedOption.dataset.address || '';
                        const city = selectedOption.dataset.city || '';
                        inpLokasi.value = address + (city ? ', ' + city : '');
                    }
                } else {
                    // Clear fields if no location selected
                    if (inpPic) inpPic.value = '';
                    if (inpKontak) inpKontak.value = '';
                    if (inpLokasi) inpLokasi.value = '';
                }
            });
        }
        
        // Load quotation information (customer, location, contract if exists)
        function loadQuotationInfo(quotationId) {
            return fetch(`<?= base_url('marketing/quotations/getQuotation/') ?>${quotationId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        const quotation = data.data;
                        
                        // Populate customer fields
                        const inpPelanggan = document.getElementById('inpPelanggan');
                        const inpPic = document.getElementById('inpPic');
                        const inpKontak = document.getElementById('inpKontak');
                        const inpLokasi = document.getElementById('inpLokasi');
                        const inpPoKontrak = document.getElementById('inpPoKontrak');
                        
                        if (inpPelanggan) inpPelanggan.value = quotation.customer_name || quotation.prospect_name || '';
                        if (inpPic) inpPic.value = quotation.pic_name || '';
                        if (inpKontak) inpKontak.value = quotation.pic_phone || '';
                        if (inpLokasi) inpLokasi.value = quotation.location_name || '';
                        
                        // Fill contract number if exists
                        if (inpPoKontrak) {
                            const contractNum = quotation.no_kontrak || quotation.kontrak_number || quotation.po_number;
                            if (contractNum) {
                                inpPoKontrak.value = contractNum;
                                inpPoKontrak.classList.remove('text-warning');
                                inpPoKontrak.classList.add('text-success');
                            } else {
                                inpPoKontrak.value = '⏳ Contract Pending';
                                inpPoKontrak.classList.add('text-warning');
                                inpPoKontrak.classList.remove('text-success');
                            }
                        }
                        
                        // Load customer locations
                        if (quotation.created_customer_id) {
                            loadCustomerLocations(quotation.created_customer_id, quotation.location_id);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading quotation info:', error);
                });
        }
        
        // Show warning when ATTACHMENT SPK selected but no contract exists
        function showAttachmentContractWarning() {
            const attachmentSection = document.getElementById('attachmentTargetSection');
            if (attachmentSection) {
                attachmentSection.classList.remove('d-none');
                attachmentSection.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Contract Required for ATTACHMENT SPK</strong><br>
                        <small>
                            ATTACHMENT SPK needs to select a target unit from existing contract units.<br>
                            Please create a contract for this quotation first, or select a different quotation with an active contract.
                        </small>
                    </div>
                `;
            }
        }
        
        // Load specifications from quotation for SPK creation
        function loadQuotationSpesifikasiForSpk(quotationId) {
            console.log('🔍 Loading specifications for quotation:', quotationId);
            
            // Get selected SPK type to filter specifications
            const spkTypeElement = document.getElementById('jenisSpkSelect');
            const jenisSpk = spkTypeElement ? spkTypeElement.value : 'UNIT';
            
            // Load from quotation specifications
            fetch(`<?= base_url('marketing/kontrak/get-quotation-specifications-for-spk/') ?>${quotationId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    console.log('📋 Received quotation specs:', data);
                    
                    // Populate customer info from quotation data (from specs response)
                    if (data.quotation) {
                        const quotation = data.quotation;
                        console.log('📄 Quotation:', quotation.quotation_number);
                        
                        // Populate customer fields from quotation object
                        const inpPelanggan = document.getElementById('inpPelanggan');
                        const inpPic = document.getElementById('inpPic');
                        const inpKontak = document.getElementById('inpKontak');
                        const inpPoKontrak = document.getElementById('inpPoKontrak');
                        
                        if (inpPelanggan) inpPelanggan.value = quotation.customer_name || quotation.prospect_name || '';
                        if (inpPoKontrak) {
                            const contractNum = quotation.no_kontrak || quotation.customer_po_number;
                            inpPoKontrak.value = contractNum || '⏳ Contract Pending';
                            if (contractNum) {
                                inpPoKontrak.classList.remove('text-warning');
                                inpPoKontrak.classList.add('text-success');
                            } else {
                                inpPoKontrak.classList.add('text-warning');
                                inpPoKontrak.classList.remove('text-success');
                            }
                        }
                        
                        // Load customer locations if customer_id is available
                        if (quotation.created_customer_id) {
                            loadCustomerLocations(quotation.created_customer_id);
                        }
                    }
                    
                    let options = '<option value="">-- Select Specification from Quotation --</option>';
                    if (data.success && data.data) {
                        // Filter specifications based on SPK type and available units
                        const filteredSpecs = data.data.filter(spek => {
                            // Check if there are available units
                            const availableUnits = spek.available_units !== undefined ? parseInt(spek.available_units) : parseInt(spek.quantity || 0);
                            if (availableUnits <= 0) {
                                console.log(`⏭️ Skipping spec ${spek.id_specification}: No available units (${availableUnits})`);
                                return false;
                            }
                            
                            if (jenisSpk === 'ATTACHMENT') {
                                // For attachment SPK, show specs that have attachment defined
                                const hasAttachment = (spek.attachment_id && parseInt(spek.attachment_id) > 0) || 
                                                     (spek.tipe_attachment && spek.tipe_attachment.trim() !== '' && spek.tipe_attachment !== 'null');
                                
                                if (hasAttachment) {
                                    console.log(`✅ ATTACHMENT spec ${spek.id_specification}: ${spek.specification_name}`);
                                }
                                return hasAttachment;
                            } else {
                                // For unit SPK, show specs that have unit specifications (tipe_unit_id)
                                const hasUnit = spek.tipe_unit_id && parseInt(spek.tipe_unit_id) > 0;
                                
                                if (hasUnit) {
                                    console.log(`✅ UNIT spec ${spek.id_specification}: ${spek.specification_name}`);
                                }
                                return hasUnit;
                            }
                        });
                        
                        console.log(`📊 Filtered ${filteredSpecs.length} specs for ${jenisSpk} SPK`);
                        
                        filteredSpecs.forEach(spek => {
                            // Use available_units if provided, otherwise use quantity
                            const totalQty = parseInt(spek.quantity) || 0;
                            const availableQty = spek.available_units !== undefined ? parseInt(spek.available_units) : totalQty;
                            
                            // Build specification display name
                            let specName = spek.specification_name || `Spec #${spek.id_specification}`;
                            
                            // Create display label based on SPK type
                            let displayLabel = '';
                            if (jenisSpk === 'ATTACHMENT') {
                                // For attachment SPK, show attachment type and brand
                                const attachType = spek.tipe_attachment || spek.attachment_type || 'Attachment';
                                const attachBrand = spek.merk_attachment || spek.attachment_brand || '';
                                
                                displayLabel = `${specName} - ${attachType}`;
                                if (attachBrand) {
                                    displayLabel += ` ${attachBrand}`;
                                }
                                displayLabel += ` (${availableQty}/${totalQty} available)`;
                            } else {
                                // For unit SPK, show unit type and model
                                const unitType = spek.jenis_tipe_unit || spek.nama_tipe_unit || 'Unit';
                                
                                displayLabel = `${specName} - ${unitType}`;
                                displayLabel += ` (${availableQty}/${totalQty} available)`;
                            }
                            
                            // Encode specification data for later use
                            const spekDataEncoded = btoa(encodeURIComponent(JSON.stringify(spek)));
                            options += `<option value="${spek.id_specification}" data-available="${availableQty}" data-total="${totalQty}" data-spek-encoded="${spekDataEncoded}">${displayLabel}</option>`;
                        });
                        
                        if (filteredSpecs.length === 0) {
                            const typeLabel = jenisSpk === 'ATTACHMENT' ? 'attachment' : 'unit';
                            options = `<option value="">⚠️ No ${typeLabel} specifications available from quotation</option>`;
                            console.warn(`⚠️ No ${typeLabel} specifications found in quotation`);
                        }
                    } else {
                        options = '<option value="">❌ No specifications found in quotation</option>';
                        console.error('❌ No specification data received');
                        
                        if (data.message) {
                            options = `<option value="">⚠️ ${data.message}</option>`;
                        }
                    }
                    if (spesifikasiSelect) {
                        spesifikasiSelect.innerHTML = options;
                        validateSpkForm(); // Re-validate whenever spec list is refreshed
                    } else {
                        console.error('❌ spesifikasiSelect element not found');
                    }
                })
                .catch(error => {
                    console.error('❌ Error loading quotation specifications:', error);
                    if (spesifikasiSelect) {
                        spesifikasiSelect.innerHTML = '<option value="">Error: ' + error.message + '</option>';
                        validateSpkForm(); // Disable button on error
                    }
                });
        }
        
        // Handle specification selection
        if (spesifikasiSelect) {
            spesifikasiSelect.addEventListener('change', function() {
                const spekId = this.value;
                const selectedOption = this.options[this.selectedIndex];
                
                if (spekId && selectedOption) {
                    try {
                        const available = parseInt(selectedOption.getAttribute('data-available')) || 0;
                        const spekDataEncoded = selectedOption.getAttribute('data-spek-encoded') || '';
                        
                        // Decode the spec data
                        const spekDataStr = decodeURIComponent(atob(spekDataEncoded));
                        
                        const spekData = JSON.parse(spekDataStr);
                        
                        // Show specification details
                        displaySpesifikasiDetail(spekData, available);
                        const spesifikasiDetail = document.getElementById('spesifikasiDetail');
                        if (spesifikasiDetail) spesifikasiDetail.classList.remove('d-none');
                        
                        // Set max unit input
                        if (jumlahUnitInput) {
                            jumlahUnitInput.max = available;
                            jumlahUnitInput.value = Math.min(1, available);
                        }
                        const maxUnitInfo = document.getElementById('maxUnitInfo');
                        if (maxUnitInfo) maxUnitInfo.textContent = `dari ${available} perlu diproses`;
                        
                        // Enable submit button if valid
                        validateSpkForm();
                    } catch (error) {
                        console.error('Error processing specification selection:', error);
                    }
                } else {
                    const spesifikasiDetail = document.getElementById('spesifikasiDetail');
                    const submitSpkBtn = document.getElementById('submitSpkBtn');
                    if (spesifikasiDetail) spesifikasiDetail.classList.add('d-none');
                    if (submitSpkBtn) {
                        submitSpkBtn.disabled = true;
                        submitSpkBtn.classList.add('disabled');
                    }
                }
            });

            // Keep visual step indicator in sync with specification selection
            spesifikasiSelect.addEventListener('change', function() {
                if (this.value && this.options[this.selectedIndex]) {
                    setSpkStep(3);
                } else if (kontrakSelect && kontrakSelect.value) {
                    setSpkStep(2);
                } else {
                    setSpkStep(1);
                }
            });
        } else {
            console.error('spesifikasiSelect element not found');
        }
        
        // Display specification details
        function displaySpesifikasiDetail(spek, available) {
            // Simple number formatting function
            function formatCurrency(amount) {
                if (!amount || isNaN(amount)) return '0';
                return parseInt(amount).toLocaleString('id-ID');
            }
            
            // Get SPK type to determine what to display
            const spkTypeSelector = document.querySelector('select[name="jenis_spk"]');
            const jenisSpk = spkTypeSelector ? spkTypeSelector.value : 'UNIT';
            
            // Check if this specification has attachment data
            const hasAttachment = spek.attachment_tipe || spek.attachment_merk;
            const hasUnit = spek.tipe_unit_id && spek.tipe_unit_id !== '0';
            
            let detailHtml = '';
            
            if (jenisSpk === 'ATTACHMENT' || (hasAttachment && !hasUnit)) {
                // For attachment SPK or attachment-only specifications
                detailHtml = `
                    <div class="row g-2">
                        <div class="col-md-6">
                            <strong>Kode Spesifikasi:</strong> ${spek.specification_name || '-'}
                        </div>
                        <div class="col-md-6">
                            <strong>Departemen:</strong> ${spek.nama_departemen || '-'}
                        </div>
                        <div class="col-md-6">
                            <strong>Tipe Attachment:</strong> ${spek.attachment_type || 'General Attachment'}
                        </div>
                        <div class="col-md-6">
                            <strong>Merk Attachment:</strong> ${spek.attachment_brand || 'Sesuai Permintaan'}
                        </div>
                        ${spek.jenis_baterai ? `
                        <div class="col-md-6">
                            <strong>Jenis Baterai:</strong> ${spek.jenis_baterai}
                        </div>` : ''}
                        ${spek.tipe_charger ? `
                        <div class="col-md-6">
                            <strong>Charger:</strong> ${spek.tipe_charger}
                        </div>` : ''}
                        <div class="col-md-6">
                            <strong>Jumlah Dibutuhkan:</strong> ${spek.quantity || '-'}
                        </div>
                        <div class="col-md-6">
                            <strong>Harga/Unit (Bulanan):</strong> Rp ${formatCurrency(spek.monthly_price)}
                        </div>
                        <div class="col-md-6">
                            <strong>Harga/Unit (Harian):</strong> Rp ${formatCurrency(spek.daily_price)}
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong> ${uiBadge('info', available > 0 ? 'Tersedia' : 'Proses Pengadaan')}
                        </div>
                        ${spek.catatan_spek ? `<div class="col-12"><strong>Catatan:</strong> ${spek.catatan_spek}</div>` : ''}
                    </div>
                `;
            } else {
                // For unit SPK, show unit details (original format)
                detailHtml = `
                    <div class="row g-2">
                        <div class="col-md-6">
                            <strong>Kode Spesifikasi:</strong> ${spek.specification_name || '-'}
                        </div>
                        <div class="col-md-6">
                            <strong>Departemen:</strong> ${spek.nama_departemen || '-'}
                        </div>
                        <div class="col-md-6">
                            <strong>Tipe Unit:</strong> ${spek.nama_tipe_unit || '-'}
                        </div>
                        <div class="col-md-6">
                            <strong>Jenis:</strong> ${spek.jenis_unit || '-'}
                        </div>
                        <div class="col-md-6">
                            <strong>Merk/Model:</strong> ${spek.merk_unit || ''} ${spek.model_unit || ''}
                        </div>
                        <div class="col-md-6">
                            <strong>Kapasitas:</strong> ${spek.nama_kapasitas || '-'}
                        </div>
                        ${hasAttachment ? `
                        <div class="col-md-6">
                            <strong>Attachment:</strong> ${spek.attachment_type || '-'} ${spek.attachment_brand || ''}
                        </div>` : ''}
                        ${spek.jenis_baterai ? `
                        <div class="col-md-6">
                            <strong>Baterai:</strong> ${spek.jenis_baterai}
                        </div>` : ''}
                        ${spek.tipe_charger ? `
                        <div class="col-md-6">
                            <strong>Charger:</strong> ${spek.tipe_charger}
                        </div>` : ''}
                        ${spek.tipe_mast ? `
                        <div class="col-md-6">
                            <strong>Mast:</strong> ${spek.tipe_mast}
                        </div>` : ''}
                        ${spek.tipe_ban ? `
                        <div class="col-md-6">
                            <strong>Ban:</strong> ${spek.tipe_ban}
                        </div>` : ''}
                        ${spek.tipe_roda ? `
                        <div class="col-md-6">
                            <strong>Roda:</strong> ${spek.tipe_roda}
                        </div>` : ''}
                        ${spek.jumlah_valve ? `
                        <div class="col-md-6">
                            <strong>Valve:</strong> ${spek.jumlah_valve}
                        </div>` : ''}
                        <div class="col-md-6">
                            <strong>Jumlah Unit:</strong> ${spek.quantity || '-'}
                        </div>
                        <div class="col-md-6">
                            <strong>Harga/Unit (Bulanan):</strong> Rp ${formatCurrency(spek.monthly_price)}
                        </div>
                        <div class="col-md-6">
                            <strong>Harga/Unit (Harian):</strong> Rp ${formatCurrency(spek.daily_price)}
                        </div>
                        ${spek.catatan_spek ? `<div class="col-12"><strong>Catatan:</strong> ${spek.catatan_spek}</div>` : ''}
                    </div>
                `;
            }
            
            const spesifikasiInfo = document.getElementById('spesifikasiInfo');
            if (spesifikasiInfo) {
                spesifikasiInfo.innerHTML = detailHtml;
            } else {
                console.error('spesifikasiInfo element not found');
            }
            
            // Load attachment inventory if this is an attachment SPK
            const jenisSpkSelector = document.querySelector('select[name="jenis_spk"]');
            const currentJenisSpk = jenisSpkSelector ? jenisSpkSelector.value : 'UNIT';
            
            // Update form labels based on SPK type
            const jumlahLabel = document.getElementById('jumlahUnitLabel');
            const jumlahInput = document.getElementById('jumlahUnitSpk');
            const formText = document.getElementById('jumlahUnitFormText');
            
            if (currentJenisSpk === 'ATTACHMENT') {
                if (jumlahLabel) jumlahLabel.textContent = 'Jumlah Attachment untuk SPK ini';
                if (formText) formText.textContent = 'Masukkan jumlah attachment yang akan diproses dalam SPK ini';
                if (jumlahInput) jumlahInput.placeholder = 'Jumlah attachment';
                
                if (spek.attachment_tipe) {
                    loadAttachmentInventory(spek.attachment_tipe, spek.attachment_merk);
                }
            } else {
                if (jumlahLabel) jumlahLabel.textContent = 'Jumlah Unit untuk SPK ini';
                if (formText) formText.textContent = 'Masukkan jumlah unit yang akan diproses dalam SPK ini';
                if (jumlahInput) jumlahInput.placeholder = 'Jumlah unit';
                
                // Clear attachment inventory for unit SPK
                const attachmentInventoryList = document.getElementById('attachmentInventoryList');
                if (attachmentInventoryList) attachmentInventoryList.innerHTML = '';
            }
        }
        
        // Load attachment inventory based on specification
        function loadAttachmentInventory(tipe, merk = '') {
            const params = new URLSearchParams({
                tipe: tipe || '',
                merk: merk || ''
            });
            
            // Use centralized Inventory API endpoint for available attachments
            fetch(`<?= base_url('warehouse/inventory/available-attachments') ?>?${params}`)
                .then(response => response.json())
                .then(data => {
                    
                    const inventoryContainer = document.getElementById('attachmentInventoryList');
                    if (!inventoryContainer) {
                        console.warn('attachmentInventoryList container not found');
                        return;
                    }
                    
                    if (data.success && data.data && data.data.length > 0) {
                        let html = `
                            <div class="mb-3">
                                <h6>Attachment Tersedia (${data.data.length} item)</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Serial Number</th>
                                                <th>Tipe</th>
                                                <th>Merk</th>
                                                <th>Model</th>
                                                <th>Lokasi</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                        `;
                        
                        data.data.forEach(att => {
                            html += `
                                <tr>
                                    <td>${att.sn_attachment || '-'}</td>
                                    <td>${att.tipe || '-'}</td>
                                    <td>${att.merk || '-'}</td>
                                    <td>${att.model || '-'}</td>
                                    <td>${att.lokasi_penyimpanan || '-'}</td>
                                    <td>${uiBadge('success', 'Tersedia')}</td>
                                </tr>
                            `;
                        });
                        
                        html += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;
                        
                        inventoryContainer.innerHTML = html;
                    } else {
                        inventoryContainer.innerHTML = `
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Tidak ada attachment ${tipe} ${merk} yang tersedia di inventory.
                                SPK akan dibuat untuk pengadaan attachment.
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading attachment inventory:', error);
                    const inventoryContainer = document.getElementById('attachmentInventoryList');
                    if (inventoryContainer) {
                        inventoryContainer.innerHTML = `
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Gagal memuat data attachment inventory.
                            </div>
                        `;
                    }
                });
        }
        
        // Validate SPK form (relaxed to avoid over-restricting)
        function validateSpkForm() {
            const kontrakId = kontrakSelect ? kontrakSelect.value : '';
            const spekId = spesifikasiSelect ? spesifikasiSelect.value : '';
            const jumlahUnit = parseInt(jumlahUnitInput ? jumlahUnitInput.value : 0) || 0;
            
            const isValid = !!kontrakId && !!spekId && jumlahUnit > 0;
            console.log(`[SPK] validateSpkForm: kontrakId=${kontrakId}, spekId=${spekId}, jumlahUnit=${jumlahUnit}, isValid=${isValid}`);

            const submitBtn = document.getElementById('submitSpkBtn');
            if (submitBtn) {
                submitBtn.disabled = !isValid;
                // Sync Bootstrap CSS disabled class so the button is visually enabled/disabled correctly.
                // ui_button() adds 'disabled' as a CSS class (not an HTML attribute), so we must
                // manage the class manually alongside the disabled property.
                submitBtn.classList.toggle('disabled', !isValid);
            }
        }
        
        // Handle input validation
        if (jumlahUnitInput) {
            jumlahUnitInput.addEventListener('input', validateSpkForm);
        }
        
        // Initialize on modal show
        const spkModal = document.getElementById('spkModal');
        if (spkModal) {
            spkModal.addEventListener('show.bs.modal', function() {
                // Reset step indicator to Step 1 when modal is opened
                setSpkStep(1);

                loadAvailableKontraks();
                // Reset form
                document.getElementById('spkForm').reset();
                
                // Pre-fill delivery_plan with today's date so user doesn't have to type it manually
                const deliveryPlanInput = document.querySelector('#spkForm input[name="delivery_plan"]');
                if (deliveryPlanInput) {
                    deliveryPlanInput.value = new Date().toISOString().split('T')[0];
                }
                
                // Hide sections using Bootstrap classes
                const kontrakInfo = document.getElementById('kontrakInfoSection');
                const spesifikasiSec = document.getElementById('spesifikasiSection');
                const spesifikasiDet = document.getElementById('spesifikasiDetail');
                const submitBtn = document.getElementById('submitSpkBtn');
                
                if (kontrakInfo) {
                    kontrakInfo.classList.add('d-none');
                    kontrakInfo.style.display = ''; // Clear inline style
                }
                if (spesifikasiSec) {
                    spesifikasiSec.classList.add('d-none');
                    spesifikasiSec.style.display = ''; // Clear inline style
                }
                if (spesifikasiDet) {
                    spesifikasiDet.classList.add('d-none');
                    spesifikasiDet.style.display = ''; // Clear inline style
                }
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('disabled');
                }
            });
        }
        
        // Add event listener for SPK type change
        const jenisSpkSelect = document.getElementById('jenisSpkSelect');
        if (jenisSpkSelect) {
            jenisSpkSelect.addEventListener('change', function() {
                
                const attachmentSection = document.getElementById('attachmentTargetSection');
                const targetUnitSelect = document.getElementById('targetUnitSelect');
                const kontrakSelect = document.getElementById('kontrakSelect');
                const step2Indicator = document.getElementById('step2Indicator');
                const jumlahUnitSpk = document.getElementById('jumlahUnitSpk');
                const attachmentQtyInfo = document.getElementById('attachmentQtyInfo');
                
                if (this.value === 'ATTACHMENT') {
                    // Show attachment target section
                    if (attachmentSection) attachmentSection.classList.remove('d-none');
                    if (targetUnitSelect) targetUnitSelect.setAttribute('required', 'required');
                    if (step2Indicator) step2Indicator.classList.add('active');
                    
                    // Lock jumlah_unit to 1 for ATTACHMENT
                    if (jumlahUnitSpk) {
                        jumlahUnitSpk.value = '1';
                        jumlahUnitSpk.setAttribute('readonly', 'readonly');
                        jumlahUnitSpk.style.backgroundColor = '#e9ecef';
                    }
                    if (attachmentQtyInfo) attachmentQtyInfo.classList.remove('d-none');
                    
                    // Load units for selected contract
                    if (kontrakSelect && kontrakSelect.value) {
                        loadContractUnitsForAttachment(kontrakSelect.value);
                    }
                } else {
                    // Hide attachment target section
                    if (attachmentSection) attachmentSection.classList.add('d-none');
                    if (targetUnitSelect) {
                        targetUnitSelect.removeAttribute('required');
                        targetUnitSelect.value = '';
                        targetUnitSelect.innerHTML = '<option value="">- Select Destination Unit -</option>';
                    }
                    if (step2Indicator) step2Indicator.classList.remove('active');
                    
                    // Unlock jumlah_unit for UNIT SPK
                    if (jumlahUnitSpk) {
                        jumlahUnitSpk.removeAttribute('readonly');
                        jumlahUnitSpk.style.backgroundColor = '';
                        jumlahUnitSpk.value = '';
                    }
                    if (attachmentQtyInfo) attachmentQtyInfo.classList.add('d-none');
                }
                
                // Reset spesifikasi section when SPK type changes
                const spesifikasiSelect = document.getElementById('spesifikasiSelect');
                const spesifikasiDetail = document.getElementById('spesifikasiDetail');
                const submitSpkBtn = document.getElementById('submitSpkBtn');
                const attachmentInventoryList = document.getElementById('attachmentInventoryList');
                
                if (spesifikasiSelect) {
                    spesifikasiSelect.innerHTML = '<option value="">-- Select Specification --</option>';
                }
                if (spesifikasiDetail) spesifikasiDetail.classList.add('d-none');
                if (submitSpkBtn) submitSpkBtn.disabled = true;
                if (attachmentInventoryList) attachmentInventoryList.innerHTML = '';
                
                // Reload specifications for selected quotation if any
                if (kontrakSelect && kontrakSelect.value) {
                    loadQuotationSpesifikasiForSpk(kontrakSelect.value);
                }
            });
        }
        
        // Function to load units from contract for ATTACHMENT target
        function loadContractUnitsForAttachment(kontrakId) {
            if (!kontrakId) return;
            
            const select = document.getElementById('targetUnitSelect');
            const loadingDiv = document.getElementById('targetUnitLoading');
            
            if (!select) return;
            
            // Show loading state
            if (loadingDiv) loadingDiv.classList.remove('d-none');
            if (select) select.disabled = true;
            
            fetch(`<?= base_url('marketing/kontrak/units/') ?>${kontrakId}`)
                .then(r => r.json())
                .then(data => {
                    // Hide loading state
                    if (loadingDiv) loadingDiv.classList.add('d-none');
                    if (select) select.disabled = false;
                    
                    select.innerHTML = '<option value="">- Select Destination Unit -</option>';
                    
                    if (data.success && Array.isArray(data.data) && data.data.length > 0) {
                        data.data.forEach(unit => {
                            const option = document.createElement('option');
                            option.value = unit.id_inventory_unit || unit.id;
                            
                            // Build display text with unit info
                            let displayText = '';
                            if (unit.no_unit) displayText += `Unit #${unit.no_unit} - `;
                            displayText += `${unit.serial_number || 'N/A'}`;
                            if (unit.tipe_jenis || unit.jenis_unit) displayText += ` | ${unit.tipe_jenis || unit.jenis_unit}`;
                            if (unit.status_unit) displayText += ` (${unit.status_unit})`;
                            
                            option.textContent = displayText;
                            option.dataset.sn = unit.serial_number;
                            option.dataset.unit_no = unit.no_unit || '';
                            select.appendChild(option);
                        });
                        
                        // Update step indicator
                        const step2Indicator = document.getElementById('step2Indicator');
                        if (step2Indicator) step2Indicator.classList.add('active');
                    } else {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = '-- No units registered in this contract --';
                        option.disabled = true;
                        select.appendChild(option);
                        
                        // Show alert
                        if (window.OptimaPro && typeof OptimaPro.showNotification==='function') {
                            OptimaPro.showNotification('No units found in selected contract. Please register units first.', 'warning');
                        }
                    }
                })
                .catch(err => {
                    console.error('Failed to load contract units:', err);
                    
                    // Hide loading state
                    if (loadingDiv) loadingDiv.classList.add('d-none');
                    if (select) select.disabled = false;
                    
                    // Show error
                    if (window.OptimaPro && typeof OptimaPro.showNotification==='function') {
                        OptimaPro.showNotification('Failed to load units from contract', 'error');
                    }
                });
        }

        // Updated form submission to handle new workflow
        document.getElementById('spkForm').addEventListener('submit', (e)=>{
            e.preventDefault();
            const fd = new FormData(e.target);
            
            // Validate ATTACHMENT specific fields
            const jenisSpk = fd.get('jenis_spk');
            console.log('🔍 SPK Form Submit - jenis_spk:', jenisSpk);
            
            if (jenisSpk === 'ATTACHMENT') {
                const targetUnitId = fd.get('target_unit_id');
                console.log('📋 ATTACHMENT SPK - target_unit_id:', targetUnitId);
                console.log('📦 All FormData:', Object.fromEntries(fd.entries()));
                
                if (!targetUnitId) {
                    if (window.OptimaPro && typeof OptimaPro.showNotification==='function') {
                        OptimaPro.showNotification('Unit Tujuan wajib dipilih untuk SPK ATTACHMENT', 'error');
                    } else if (typeof showNotification==='function') {
                        showNotification('Unit Tujuan wajib dipilih untuk SPK ATTACHMENT', 'error');
                    } else {
                        alert('Unit Tujuan wajib dipilih untuk SPK ATTACHMENT');
                    }
                    return;
                }
                // Force jumlah_unit to 1 for attachment
                fd.set('jumlah_unit', '1');
            }
            
            // Add specification ID for new workflow
            const spekId = spesifikasiSelect ? spesifikasiSelect.value : '';
            if (spekId) {
                fd.append('kontrak_spesifikasi_id', spekId);
            }
            
            fetch('<?= base_url('marketing/spk/create') ?>',{method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body:fd})
                .then(r=>r.json()).then(j=>{ 
                    if(j.success){ 
                        e.target.reset(); 
                        if (spkTable && spkTable.ajax) spkTable.ajax.reload(); // Reload DataTable
                        loadMonitoring();
                        bootstrap.Modal.getInstance(document.getElementById('spkModal')).hide();
                        if (window.OptimaPro && typeof OptimaPro.showNotification==='function') OptimaPro.showNotification('SPK dibuat: ' + (j.nomor||''), 'success');
                        else if (typeof showNotification==='function') showNotification('SPK dibuat: ' + (j.nomor||''), 'success');
                    } else {
                        const msg = j.message || 'Gagal membuat SPK';
                        if (window.OptimaPro && typeof OptimaPro.showNotification==='function') OptimaPro.showNotification(msg, 'error');
                        else if (typeof showNotification==='function') showNotification(msg, 'error');
                    }
                });
        });
        // DI form submit
        document.getElementById('diForm').addEventListener('submit', (e)=>{
            e.preventDefault();
            const fd = new FormData(e.target);
            
            // Validate required fields (updated for workflow)
            const jenisPerintah = fd.get('jenis_perintah_kerja_id');
            const tujuanPerintah = fd.get('tujuan_perintah_kerja_id');
            
            if (!jenisPerintah || jenisPerintah.trim() === '') {
                alert('Jenis Perintah Kerja harus dipilih.');
                return;
            }
            
            if (!tujuanPerintah || tujuanPerintah.trim() === '') {
                alert('Tujuan Perintah harus dipilih.');
                return;
            }
            
            // If unit checkboxes exist, append unit_ids[]
            const checks = document.querySelectorAll('.di-unit-check');
            if (checks && checks.length) {
                const picked = Array.from(checks).filter(ch=>ch.checked).map(ch=>ch.value);
                if (picked.length === 0) {
                    alert('Select at least one unit for this DI.');
                    return;
                }
                picked.forEach(v=> fd.append('unit_ids[]', v));
            }
            // spk_id already set; backend enforces COMPLETED status
            
            fetch('<?= base_url('marketing/di/create') ?>',{method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body:fd})
                .then(r=>r.json()).then(j=>{
                    if (j && j.success) {
                        bootstrap.Modal.getInstance(document.getElementById('diModal')).hide();
                        
                        // Reset form and validation states
                        const form = e.target;
                        form.reset();
                        form.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
                            el.classList.remove('is-valid', 'is-invalid');
                        });
                        
                        if (spkTable && spkTable.ajax) spkTable.ajax.reload(); // Reload DataTable
                        loadMonitoring();
                        if (window.OptimaPro && typeof OptimaPro.showNotification==='function') OptimaPro.showNotification('DI dibuat: '+ (j.nomor||''), 'success');
                        else if (typeof showNotification==='function') showNotification('DI dibuat: '+ (j.nomor||''), 'success');
                        else alert('DI dibuat: '+ (j.nomor||''));
                    } else {
                        const msg = (j && j.message) ? j.message : 'Gagal membuat DI';
                        if (window.OptimaPro && typeof OptimaPro.showNotification==='function') OptimaPro.showNotification(msg, 'error');
                        else if (typeof showNotification==='function') showNotification(msg, 'error');
                        else alert(msg);
                    }
                });
        });
        
        // Add real-time validation for DI form (updated for workflow)
        function validateDiForm() {
            const jenisSelect = document.getElementById('spkJenisPerintah');
            const tujuanSelect = document.getElementById('spkTujuanPerintah');
            const submitBtn = document.querySelector('#diForm [type="submit"]');
            
            if (!jenisSelect || !tujuanSelect || !submitBtn) return;
            
            function checkValidity() {
                const jenisValid = jenisSelect.value.trim() !== '';
                const tujuanValid = tujuanSelect.value.trim() !== '';
                const isValid = jenisValid && tujuanValid;
                
                // Update visual feedback
                jenisSelect.classList.toggle('is-invalid', !jenisValid && jenisSelect.value !== '');
                jenisSelect.classList.toggle('is-valid', jenisValid);
                
                tujuanSelect.classList.toggle('is-invalid', !tujuanValid && tujuanSelect.value !== '');
                tujuanSelect.classList.toggle('is-valid', tujuanValid);
                
                // Enable/disable submit button
                submitBtn.disabled = !isValid;
            }
            
            jenisSelect.addEventListener('change', checkValidity);
            tujuanSelect.addEventListener('change', checkValidity);
            
            // Initial check
            checkValidity();
        }
        
        // Initialize validation when modal is shown
        document.getElementById('diModal').addEventListener('shown.bs.modal', validateDiForm);
        
        // Reset validation when modal is hidden
        document.getElementById('diModal').addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('diForm');
            if (form) {
                form.reset();
                form.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
                    el.classList.remove('is-valid', 'is-invalid');
                });
                
                // Reset submit button
                const submitBtn = form.querySelector('[type="submit"]');
                if (submitBtn) submitBtn.disabled = true;
            }
        });
        
        // =====================================================
        // WORKFLOW BARU: DYNAMIC DROPDOWN SYSTEM FOR SPK DI - FROM DATABASE
        // =====================================================
        
        // Variables to store workflow data
        let spkJenisPerintahOptions = [];
        
        // Load jenis perintah from API for SPK modal
        async function loadSpkJenisPerintahOptions() {
            try {
                const response = await fetch('<?= base_url('marketing/get-jenis-perintah-kerja') ?>?context=spk', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                });
                const result = await response.json();
                
                if (result.success) {
                    spkJenisPerintahOptions = result.data;
                    populateSpkJenisPerintahDropdown();
                } else {
                    console.error('Failed to load SPK jenis perintah options:', result.message);
                }
            } catch (error) {
                console.error('Error loading SPK jenis perintah options:', error);
            }
        }
        
        // Populate jenis perintah dropdown for SPK modal
        function populateSpkJenisPerintahDropdown() {
            const jenisSelect = document.getElementById('spkJenisPerintah');
            
            if (jenisSelect) {
                jenisSelect.innerHTML = '<option value="">-- Select Command Type --</option>';
                spkJenisPerintahOptions.forEach(option => {
                    const optionElement = document.createElement('option');
                    optionElement.value = option.id;
                    optionElement.textContent = `${option.kode} - ${option.nama}`;
                    optionElement.title = option.deskripsi;
                    jenisSelect.appendChild(optionElement);
                });
            }
        }
        
        // Load tujuan perintah based on jenis for SPK modal
        async function loadSpkTujuanPerintahOptions(jenisId) {
            try {
                const response = await fetch(`<?= base_url('marketing/get-tujuan-perintah-kerja') ?>?jenis_id=${jenisId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                });
                const result = await response.json();
                
                if (result.success) {
                    const tujuanSelect = document.getElementById('spkTujuanPerintah');
                    if (tujuanSelect) {
                        tujuanSelect.innerHTML = '<option value="">-- Select Destination --</option>';
                        tujuanSelect.disabled = false;
                        
                        result.data.forEach(option => {
                            const optionElement = document.createElement('option');
                            optionElement.value = option.id;
                            optionElement.textContent = `${option.kode} - ${option.nama}`;
                            optionElement.title = option.deskripsi;
                            tujuanSelect.appendChild(optionElement);
                        });
                    }
                } else {
                    console.error('Failed to load SPK tujuan perintah options:', result.message);
                }
            } catch (error) {
                console.error('Error loading SPK tujuan perintah options:', error);
            }
        }
        
        // Setup SPK DI workflow dropdowns
        function setupSpkWorkflowDropdowns() {
            const jenisSelect = document.getElementById('spkJenisPerintah');
            const tujuanSelect = document.getElementById('spkTujuanPerintah');
            
            if (!jenisSelect || !tujuanSelect) return;
            
            jenisSelect.addEventListener('change', function() {
                const jenisId = this.value;
                const jenisText = this.selectedOptions[0]?.textContent || '';
                
                // Reset tujuan dropdown
                tujuanSelect.innerHTML = '<option value="">-- Select Destination --</option>';
                tujuanSelect.disabled = true;
                
                // Check if this is TUKAR workflow
                const isTukarWorkflow = jenisText.toUpperCase().includes('TUKAR');
                
                // Show/hide TUKAR workflow section
                handleSpkTukarWorkflowVisibility(isTukarWorkflow);
                
                if (jenisId) {
                    // Load tujuan options from API
                    loadSpkTujuanPerintahOptions(jenisId);
                }
                
                // Trigger validation from existing validateDiForm function
                // No need to call separate validation here as the change event will be caught
            });
        }
        
        // Handle TUKAR workflow visibility and setup
        function handleSpkTukarWorkflowVisibility(isTukarWorkflow) {
            const tukarWorkflow = document.getElementById('spkTukarWorkflow');
            const standardItems = document.getElementById('diUnitsPick'); // Standard item selection
            const itemSummary = document.getElementById('diSelectedSummary');
            
            if (!tukarWorkflow) {
                console.warn('SPK TUKAR workflow element not found');
                return;
            }
            
            if (isTukarWorkflow) {
                // Show TUKAR workflow components
                tukarWorkflow.style.display = 'block';
                
                // Keep standard item selection visible for TUKAR (items KIRIM from SPK)
                // standardItems visibility will be handled by existing SPK selection logic
                if (itemSummary) {
                    itemSummary.innerHTML = '<div class="text-info"><i class="fas fa-exchange-alt"></i> <strong>Mode TUKAR:</strong> Unit KIRIM (dari Service) + Unit TARIK (dari kontrak SPK ini)</div>';
                }
                
                // Load unit TARIK dari kontrak SPK langsung (tidak perlu pilih kontrak lagi)
                loadSpkTarikUnitsFromSpkKontrak();
            } else {
                // Hide TUKAR workflow components
                tukarWorkflow.style.display = 'none';
                
                // Reset TUKAR form fields
                resetSpkTukarWorkflowFields();
                
                // Reset item summary to normal
                if (itemSummary) {
                    itemSummary.innerHTML = '<span class="text-muted">Belum ada ringkasan.</span>';
                }
            }
        }
        
        // Load unit TARIK dari kontrak yang terhubung dengan SPK untuk TUKAR workflow
        function loadSpkTarikUnitsFromSpkKontrak() {
            // Ambil data SPK yang sedang dipilih
            const spkId = document.getElementById('diSpkId').value;
            if (!spkId) {
                console.error('SPK ID not found for TUKAR workflow');
                return;
            }
            
            // Gunakan data SPK yang sudah ada untuk mendapatkan kontrak
            const poNo = document.getElementById('diPoNo').value;
            const pelanggan = document.getElementById('diPelanggan').value;
            
            // Fetch SPK detail untuk mendapatkan kontrak_id
            fetch(`<?= base_url('marketing/spk/detail/') ?>${spkId}`)
                .then(r => r.json())
                .then(j => {
                    if (j && j.success && j.data && j.data.kontrak_id) {
                        // Load units dari kontrak
                        loadSpkTarikUnits(j.data.kontrak_id);
                    } else {
                        console.error('SPK tidak memiliki kontrak yang terhubung. Response:', j);
                        document.getElementById('spkTarikUnitList').innerHTML = 
                            '<div class="text-danger small">SPK ini tidak terhubung dengan kontrak.</div>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching SPK detail:', error);
                    document.getElementById('spkTarikUnitList').innerHTML = 
                        '<div class="text-danger small">Error loading SPK data.</div>';
                });
        }
        
        // Setup kontrak selection change handler for SPK TUKAR workflow
        function setupSpkKontrakChangeHandler() {
            const kontrakSelect = document.getElementById('spkKontrakSelect');
            
            if (!kontrakSelect) return;
            
            kontrakSelect.addEventListener('change', function() {
                if (this.value) {
                    // Get selected option text which contains "no_kontrak - pelanggan"
                    const selectedOption = this.selectedOptions[0];
                    const optionText = selectedOption.textContent;
                    
                    // Parse no_kontrak and pelanggan from option text
                    const parts = optionText.split(' - ');
                    const noKontrak = parts[0] || '';
                    const pelanggan = parts[1] || '';
                    
                    // Auto-populate hidden fields for backend validation
                    document.getElementById('spkPoKontrakNomor').value = noKontrak;
                    document.getElementById('spkPelangganKontrak').value = pelanggan;
                    
                    // Load TARIK units for TUKAR workflow
                    loadSpkTarikUnits(this.value);
                } else {
                    // Reset hidden fields and list
                    document.getElementById('spkPoKontrakNomor').value = '';
                    document.getElementById('spkPelangganKontrak').value = '';
                    document.getElementById('spkTarikUnitList').innerHTML = '<div class="text-muted small">Select a contract first...</div>';
                    document.getElementById('spkTarikCount').textContent = '0';
                }
            });
        }
        
        // Load TARIK units for SPK TUKAR workflow
        async function loadSpkTarikUnits(kontrakId) {
            try {
                const response = await fetch(`<?= base_url('marketing/kontrak/units/') ?>${kontrakId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                });
                const result = await response.json();
                
                if (result.success && result.data.length > 0) {
                    const unitList = document.getElementById('spkTarikUnitList');
                    if (unitList) {
                        const unitsHtml = result.data.map(unit => {
                            const unitLabel = `${unit.no_unit || '-'} - ${unit.merk || '-'} ${unit.model || ''}`;
                            const kapasitas = unit.kapasitas ? ` (${unit.kapasitas})` : '';
                            const jenis = unit.jenis_unit ? ` - ${unit.jenis_unit}` : '';
                            
                            return `
                                <div class="form-check">
                                    <input class="form-check-input spk-tarik-unit-check" type="checkbox" 
                                           value="${unit.id}" id="spk_tarik_unit_${unit.id}" 
                                           name="tarik_units[]" onchange="updateSpkTarikCount()">
                                    <label class="form-check-label" for="spk_tarik_unit_${unit.id}">
                                        ${unitLabel}${kapasitas}${jenis}
                                        <small class="text-muted d-block">SN: ${unit.serial_number || '-'} | Status: ${unit.status || 'TERSEDIA'}</small>
                                    </label>
                                </div>
                            `;
                        }).join('');
                        
                        unitList.innerHTML = unitsHtml;
                        
                        // Setup select all / clear buttons
                        setupSpkTarikUnitButtons();
                    }
                } else {
                    document.getElementById('spkTarikUnitList').innerHTML = 
                        '<div class="text-muted small">Tidak ada unit tersedia di kontrak ini.</div>';
                }
                
                // Reset count
                updateSpkTarikCount();
                
            } catch (error) {
                console.error('Error loading TARIK units for SPK TUKAR:', error);
                document.getElementById('spkTarikUnitList').innerHTML = 
                    '<div class="text-danger small">Error loading units.</div>';
            }
        }
        
        // Setup select all / clear buttons for SPK TARIK units
        function setupSpkTarikUnitButtons() {
            const btnSelectAll = document.getElementById('spkBtnSelectAllTarik');
            const btnClear = document.getElementById('spkBtnClearTarik');
            
            if (btnSelectAll) {
                btnSelectAll.onclick = function() {
                    document.querySelectorAll('.spk-tarik-unit-check').forEach(checkbox => {
                        checkbox.checked = true;
                    });
                    updateSpkTarikCount();
                };
            }
            
            if (btnClear) {
                btnClear.onclick = function() {
                    document.querySelectorAll('.spk-tarik-unit-check').forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    updateSpkTarikCount();
                };
            }
        }
        
        // Reset SPK TUKAR workflow fields
        function resetSpkTukarWorkflowFields() {
            // Reset unit list
            const tarikUnitList = document.getElementById('spkTarikUnitList');
            const tarikCount = document.getElementById('spkTarikCount');
            
            if (tarikUnitList) {
                tarikUnitList.innerHTML = '<div class="text-muted small">Memuat unit dari kontrak...</div>';
            }
            
            if (tarikCount) {
                tarikCount.textContent = '0';
            }
        }
        
        // Initialize SPK workflow dropdowns when modal shown
        document.getElementById('diModal').addEventListener('shown.bs.modal', function() {
            setupSpkWorkflowDropdowns();
            loadSpkJenisPerintahOptions(); // Load initial jenis perintah options
        });
        
        // =====================================================
        // END WORKFLOW BARU FOR SPK
        // =====================================================
    });
    </script>
    <!-- Detail SPK Modal -->
    <div class="modal fade modal-wide" id="spkDetailModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><?= lang('App.detail') ?> SPK <span id="spkNumberHeader" class="text-primary"></span></h6>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="spkDetailBody"><p class="text-muted">Loading...</p></div>
                </div>
                <div class="modal-footer">
                    <?= ui_button('print', 'Print PDF', ['id' => 'btnPrintPdf', 'href' => '#', 'target' => '_blank', 'rel' => 'noopener']) ?>
                    <?= ui_button('edit', 'Edit', ['id' => 'btnEditSpk', 'onclick' => 'editSpk()']) ?>
                    <?= ui_button('delete', 'Delete', ['id' => 'btnDeleteSpk', 'onclick' => 'deleteSpk()']) ?>
                    <?= ui_button('cancel', 'Tutup', ['data-bs-dismiss' => 'modal', 'color' => 'secondary']) ?>
                </div>
            </div>
        </div>
    </div>
    <script>
    // Global variable to store current SPK ID for edit/delete operations
    let currentSpkId = null;
    
    function openDetail(id){
        currentSpkId = id; // Store current SPK ID
        const pdfBtn = document.getElementById('btnPrintPdf');
        if (pdfBtn) { pdfBtn.href = `<?= base_url('marketing/spk/print/') ?>${id}`; }
        const body = document.getElementById('spkDetailBody');
        const headerSpan = document.getElementById('spkNumberHeader');
        body.innerHTML = '<p class="text-muted">Memuat...</p>';
        
        fetch(`<?= base_url('marketing/spk/detail/') ?>${id}`)
            .then(r => {
                // Check for 401 Unauthorized (session expired)
                if (r.status === 401) {
                    alert('Session expired. Please login again.');
                    window.location.href = '<?= base_url('auth/login') ?>';
                    return Promise.reject('Unauthorized');
                }
                if (!r.ok) {
                    throw new Error(`HTTP error! Status: ${r.status}`);
                }
                return r.json();
            })
            .then(j => {
                if (!j.success) { 
                    body.innerHTML = '<div class="text-danger">Gagal memuat detail</div>'; 
                    return; 
                }
            // Main data comes from j.data (the spk table data)
            const d = j.data || {};
            const s = j.spesifikasi || {};
            const ks = j.kontrak_spec || {};
            
            // Update header with SPK number
            if (headerSpan && d.nomor_spk) {
                headerSpan.textContent = '#' + d.nomor_spk;
            }
            
            // Get specification code from the provided spek_kode
            let specDisplay = '-';
            if (ks.spek_kode) {
                specDisplay = ks.spek_kode;
            } else if (s && s.spek_kode) {
                specDisplay = s.spek_kode;
            }
            
            // Process accessories - prioritize kontrak_spec data
            let aksText = '-';
            if (ks && ks.aksesoris) {
                const aks = ks.aksesoris;
                if (Array.isArray(aks) && aks.length > 0) {
                    aksText = aks.join(', ');
                } else if (typeof aks === 'string' && aks.trim()) {
                    try {
                        const parsed = JSON.parse(aks);
                        if (Array.isArray(parsed) && parsed.length > 0) {
                            aksText = parsed.join(', ');
                        } else {
                            aksText = aks;
                        }
                    } catch(e) {
                        aksText = aks;
                    }
                }
            } else if (s && s.aksesoris) {
                if (Array.isArray(s.aksesoris) && s.aksesoris.length > 0) {
                    aksText = s.aksesoris.join(', ');
                } else if (typeof s.aksesoris === 'string' && s.aksesoris.trim()) {
                    try {
                        const parsed = JSON.parse(s.aksesoris);
                        if (Array.isArray(parsed) && parsed.length > 0) {
                            aksText = parsed.join(', ');
                        } else {
                            aksText = s.aksesoris;
                        }
                    } catch(e) {
                        aksText = s.aksesoris;
                    }
                }
            }
            // Selected unit and attachment from controller response
            const u = s.selected && s.selected.unit ? s.selected.unit : null;
            const a = s.selected && s.selected.attachment ? s.selected.attachment : null;
            
            try {
                body.innerHTML = `
                    <div class="row g-2">
                        <div class="col-6"><strong>SPK Type:</strong> ${d.jenis_spk||'-'}</div>
                        <div class="col-6"><strong>Contract/PO:</strong> ${d.po_kontrak_nomor||'-'}</div>
                        <div class="col-6"><strong>Company Name:</strong> ${d.pelanggan||'-'}</div>
                        <div class="col-6"><strong>Location:</strong> ${d.lokasi||'-'}</div>
                        <div class="col-6"><strong>Pic:</strong> ${d.pic||'-'}</div>
                        <div class="col-6"><strong>Contact:</strong> ${d.kontak||'-'}</div>
                        <div class="col-6"><strong>SPK Created:</strong> ${d.dibuat_pada ? (new Date(d.dibuat_pada)).toLocaleDateString('id-ID', {year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'}) : '-'}</div>
                        <div class="col-6"><strong>Delivery Plan:</strong> ${d.delivery_plan||'-'}</div>
                        <div class="col-12"><hr></div>
                        <div class="col-12"><strong>Unit Information:</strong></div>
                        <div class="col-6"><strong>Total Unit:</strong> ${d.jumlah_unit || 0}</div>
                        <div class="col-6"><strong>Department:</strong> ${ks.departemen_id_name || s.departemen_id_name||'-'}</div>
                        <div class="col-6"><strong>Type & Brand:</strong> ${[ks.tipe_unit_id_name || s.tipe_jenis, ks.brand_id_name || ks.merk_unit || s.merk_unit].filter(x=>x).join(' ') || '-'}</div>
                        <div class="col-6"><strong>Capacity:</strong> ${ks.kapasitas_id_name || s.kapasitas_id_name||'-'}</div>
                        <div class="col-6"><strong>Battery (Type):</strong> ${ks.battery_id_name || ks.jenis_baterai || s.jenis_baterai||'-'}</div>
                        <div class="col-6"><strong>Charger:</strong> ${ks.charger_id_name || s.charger_id_name||'-'}</div>
                        <div class="col-6"><strong>Attachment (Type):</strong> ${ks.attachment_id_name || ks.attachment_tipe || s.attachment_tipe||'-'}</div>
                        <div class="col-6"><strong>Valve:</strong> ${ks.valve_id_name || s.valve_id_name||'-'}</div>
                        <div class="col-6"><strong>Mast:</strong> ${ks.mast_id_name || s.mast_id_name||'-'}</div>
                        <div class="col-6"><strong>Wheel:</strong> ${ks.roda_id_name || s.roda_id_name||'-'}</div>
                        <div class="col-6"><strong>Tire:</strong> ${ks.ban_id_name || s.ban_id_name||'-'}</div>
                        <div class="col-12"><strong>Accessories :</strong> ${aksText}</div>
                        <div class="col-12"><hr></div>
                        
                        <!-- Status Approval Workflow Section (Read-only for Marketing) -->
                        <div class="col-12"><h6 class="mb-2">📋 Status Approval Workflow</h6></div>
                        ${(() => {
                            const totalUnits = parseInt(d.jumlah_unit) || 1;
                            const stageStatus = j.stage_status || {};
                            const unitStages = stageStatus.unit_stages || {};
                            
                            let workflowHtml = '';
                            
                            // Multi-unit progress bar
                            if (totalUnits > 1) {
                                const completedUnits = Object.keys(unitStages).filter(unitIndex => {
                                    const unit = unitStages[unitIndex];
                                    return unit.persiapan_unit?.completed && unit.fabrikasi?.completed && 
                                           unit.painting?.completed && unit.pdi?.completed;
                                }).length;
                                
                                workflowHtml += `<div class="col-12 mb-3">
                                    <div class="progress h-16px">
                                        <div class="progress-bar" role="progressbar" style="width:${Math.min(100, Math.round((completedUnits/totalUnits)*100))}%">
                                            ${completedUnits}/${totalUnits} units completed
                                        </div>
                                    </div>
                                </div>`;
                            }
                            
                            workflowHtml += '<div class="row g-2">';
                            
                            // Helper function to get stage completion info
                            const getStageInfo = (stageName) => {
                                let completedCount = 0;
                                let lastMechanic = '-';
                                let lastDate = '-';
                                let mechanics = [];
                                
                                Object.keys(unitStages).forEach(unitIndex => {
                                    const stage = unitStages[unitIndex][stageName];
                                    if (stage?.completed) {
                                        completedCount++;
                                        if (stage.mekanik) lastMechanic = stage.mekanik;
                                        if (stage.tanggal_approve) lastDate = stage.tanggal_approve;
                                        if (stage.mechanics_data) {
                                            mechanics = mechanics.concat(stage.mechanics_data.map(m => m.name || m.id).filter(Boolean));
                                        }
                                    }
                                });
                                
                                const isCompleted = completedCount > 0;
                                const displayMechanics = mechanics.length > 0 ? mechanics.join(', ') : lastMechanic;
                                
                                return {
                                    isCompleted,
                                    completedCount,
                                    totalUnits,
                                    mechanic: displayMechanics,
                                    date: lastDate,
                                    badge: isCompleted ? 'badge-soft-green' : 'badge-soft-gray',
                                    icon: isCompleted ? '✓ Completed' : 'Waiting'
                                };
                            };
                            
                            // Unit Preparation (skip for ATTACHMENT)
                            if (d.jenis_spk !== 'ATTACHMENT') {
                                const persiapan = getStageInfo('persiapan_unit');
                                workflowHtml += `<div class="col-6">
                                    <strong>1. Unit Preparation:</strong><br>
                                    <span class="badge ${persiapan.badge}">${persiapan.icon}</span>
                                    ${persiapan.isCompleted ? `<br><small class="text-muted">
                                        Mechanic: ${persiapan.mechanic}<br>
                                        Date: ${persiapan.date}
                                        ${totalUnits > 1 ? `<br>Units: ${persiapan.completedCount}/${totalUnits}` : ''}
                                    </small>` : ''}
                                </div>`;
                            }
                            
                            // Fabrication
                            const fabrikasi = getStageInfo('fabrikasi');
                            workflowHtml += `<div class="col-6">
                                <strong>${d.jenis_spk === 'ATTACHMENT' ? '1' : '2'}. Fabrication:</strong><br>
                                <span class="badge ${fabrikasi.badge}">${fabrikasi.icon}</span>
                                ${fabrikasi.isCompleted ? `<br><small class="text-muted">
                                    Mechanic: ${fabrikasi.mechanic}<br>
                                    Date: ${fabrikasi.date}
                                    ${totalUnits > 1 ? `<br>Units: ${fabrikasi.completedCount}/${totalUnits}` : ''}
                                </small>` : ''}
                            </div>`;
                            
                            // Painting
                            const painting = getStageInfo('painting');
                            workflowHtml += `<div class="col-6">
                                <strong>${d.jenis_spk === 'ATTACHMENT' ? '2' : '3'}. Painting:</strong><br>
                                <span class="badge ${painting.badge}">${painting.icon}</span>
                                ${painting.isCompleted ? `<br><small class="text-muted">
                                    Mechanic: ${painting.mechanic}<br>
                                    Date: ${painting.date}
                                    ${totalUnits > 1 ? `<br>Units: ${painting.completedCount}/${totalUnits}` : ''}
                                </small>` : ''}
                            </div>`;
                            
                            // PDI
                            const pdi = getStageInfo('pdi');
                            workflowHtml += `<div class="col-6">
                                <strong>${d.jenis_spk === 'ATTACHMENT' ? '3' : '4'}. PDI Inspection:</strong><br>
                                <span class="badge ${pdi.badge}">${pdi.icon}</span>
                                ${pdi.isCompleted ? `<br><small class="text-muted">
                                    Mechanic: ${pdi.mechanic}<br>
                                    Date: ${pdi.date}
                                    ${totalUnits > 1 ? `<br>Units: ${pdi.completedCount}/${totalUnits}` : ''}
                                </small>` : ''}
                            </div>`;
                            
                            workflowHtml += '</div>';
                            
                            return workflowHtml;
                        })()}
                        <div class="col-12"><hr></div>
                        
                        <!-- Item yang dipilih Section (fixed data structure) -->
                        <div class="col-12"><hr></div>
                        ${(() => {
                            const totalUnits = parseInt(d.jumlah_unit||1);
                            
                            // Debug: log available data
                            console.log('📊 Item yang dipilih Debug:', {
                                'j.prepared_units_detail': j.prepared_units_detail,
                                'j.spesifikasi': j.spesifikasi,
                                'j.spesifikasi.prepared_units_detail': j.spesifikasi?.prepared_units_detail,
                                'totalUnits': totalUnits
                            });
                            
                            // Priority 1: Check prepared_units_detail from API response (new workflow)
                            let preparedDetails = [];
                            if (Array.isArray(j.prepared_units_detail) && j.prepared_units_detail.length > 0) {
                                preparedDetails = j.prepared_units_detail;
                                console.log('✅ Using j.prepared_units_detail:', preparedDetails.length, 'units');
                            } else if (j.spesifikasi && Array.isArray(j.spesifikasi.prepared_units_detail) && j.spesifikasi.prepared_units_detail.length > 0) {
                                preparedDetails = j.spesifikasi.prepared_units_detail;
                                console.log('✅ Using j.spesifikasi.prepared_units_detail:', preparedDetails.length, 'units');
                            } else {
                                console.log('⚠️ No prepared_units_detail found in response');
                            }
                            
                            let itemsHtml = '';
                            
                            if (preparedDetails.length > 0) {
                                // New workflow: show distinct prepared units
                                console.log('✅ Using prepared_units_detail:', preparedDetails.length, 'units');
                                itemsHtml = preparedDetails.map((it, idx) => `
                                    <div class="col-12"><strong>Item yang dipilih (${idx+1}):</strong></div>
                                    <div class="col-12 svcUnitDetailBlock">
                                        <div><strong>Unit:</strong> ${it.unit_label || '-'}</div>
                                        <div><strong>Serial Number:</strong> ${it.serial_number || '-'}</div>
                                        <div><strong>Tipe Unit:</strong> ${it.tipe_jenis || '-'}</div>
                                        <div><strong>Merk/Model:</strong> ${(it.merk_unit || '-') + ' ' + (it.model_unit || '')}</div>
                                        ${ it.attachment_label ? `<div><strong>Attachment:</strong> ${it.attachment_label}</div>` : ''}
                                        ${ it.catatan ? `<div><strong>Catatan:</strong> ${it.catatan}</div>` : ''}
                                        ${ it.mekanik ? `<div><strong>Mekanik:</strong> ${it.mekanik}</div>` : ''}
                                        ${ it.timestamp ? `<div class="text-muted"><small>Waktu: ${it.timestamp}</small></div>` : ''}
                                    </div>
                                    <div class="col-12"><hr></div>
                                `).join('');
                            } else if (s.selected && s.selected.unit) {
                                // Legacy workflow: show selected unit detail per jumlah_unit
                                console.log('📝 Using legacy selected unit for', totalUnits, 'units');
                                function renderItemBlock(i, total) {
                                    return `
                                        <div class="col-12"><strong>Item Terpilih${total > 1 ? ' ('+i+')' : ''}:</strong></div>
                                        <div class="col-12 svcUnitDetailBlock">
                                            <div><strong>Unit:</strong> ${s.selected.unit.label || ((s.selected.unit.no_unit || '-') + ' | ' + (s.selected.unit.merk_unit || '-') + ' | ' + (s.selected.unit.model_unit || '-'))}</div>
                                            <div><strong>Serial Number:</strong> ${s.selected.unit.serial_number || '-'}</div>
                                            <div><strong>Tipe Unit:</strong> ${s.selected.unit.tipe_jenis || '-'}</div>
                                            <div><strong>Kapasitas:</strong> ${s.selected.unit.kapasitas_name || '-'}</div>
                                            <div><strong>Mast:</strong> ${s.selected.unit.mast || s.selected.unit.mast_model || '-'}</div>
                                            <div><strong>Roda:</strong> ${s.selected.unit.roda || '-'}</div>
                                            <div><strong>Ban:</strong> ${s.selected.unit.ban || '-'}</div>
                                            <div><strong>Valve:</strong> ${s.selected.unit.valve || '-'}</div>
                                            ${ (s.selected && s.selected.attachment) ? `<div><strong>Attachment:</strong> ${s.selected.attachment.tipe || '-'} | ${s.selected.attachment.merk || '-'} | ${s.selected.attachment.model || '-'}${s.selected.attachment.sn_attachment ? (' [SN: ' + s.selected.attachment.sn_attachment + ']') : ''}${s.selected.attachment.lokasi_penyimpanan ? (' @ ' + s.selected.attachment.lokasi_penyimpanan) : ''}</div>` : ''}
                                            <div><strong>Catatan:</strong> ${(s.selected && s.selected.catatan) ? s.selected.catatan : '-'}</div>
                                        </div>
                                        <div class="col-12"><hr></div>
                                    `;
                                }
                                for (let i = 1; i <= totalUnits; i++) { 
                                    itemsHtml += renderItemBlock(i, totalUnits); 
                                }
                            } else {
                                // No data available
                                console.log('⚠️ No item data available');
                                itemsHtml = `
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> 
                                            Item belum dipilih atau sedang dalam proses persiapan unit.
                                        </div>
                                    </div>
                                `;
                            }
                            
                            return itemsHtml;
                        })()}
                    </div>`;
            } catch(error) {
                body.innerHTML = `<div class="alert alert-danger">Error rendering SPK detail: ${error.message}</div>`;
                console.error('Error rendering SPK detail:', error);
                return;
            }
            
            // Load full unit detail if selected
			if (s.selected && s.selected.unit && s.selected.unit.id) {
				const esc = (str)=>{ if(str===null||str===undefined||str==='') return '-'; return String(str).replaceAll('<','&lt;').replaceAll('>','&gt;'); };
				fetch(`<?= base_url('warehouse/inventory/get-unit-full-detail/') ?>${s.selected.unit.id}`)
				    .then(r => {
				        if (!r.ok) {
				            throw new Error(`Error loading unit details: ${r.status} ${r.statusText}`);
				        }
				        return r.json();
				    })
				    .then(resp => {
					    const host = document.getElementById('svcUnitDetailBlock');
					    if(!host) return;
					    if(!(resp && resp.success && resp.data)){ 
					        host.innerHTML = '<div class="text-danger">Gagal memuat detail unit</div>'; 
					        return; 
					    }
					    const data = resp.data;
					    host.innerHTML = `
					    <div class="row g-2">
                            <div class="col-6"><strong>ID Unit</strong>: ${esc(data.id_inventory_unit)}</div>
                            <div class="col-6"><strong>Serial Number</strong>: ${esc(data.serial_number_po)}</div>
                            <div class="col-6"><strong>Merk</strong>: ${esc(data.merk_unit)}</div>
                            <div class="col-6"><strong>Model</strong>: ${esc(data.model_unit)}</div>
                            <div class="col-6"><strong>Jenis Unit</strong>: ${esc(data.nama_departemen)}</div>
                            <div class="col-6"><strong>Tipe Unit</strong>: ${esc(data.nama_tipe_unit)}</div>
                            <div class="col-6"><strong>Tahun</strong>: ${esc(data.tahun_po)}</div>
                            <div class="col-6"><strong>Kapasitas</strong>: ${esc(data.kapasitas_unit)}</div>
                            <div class="col-6"><strong>Tanggal Masuk</strong>: ${esc(data.tanggal_masuk)}</div>
                            <div class="col-12"><hr></div>
                            <div class="col-6"><strong>Attachment</strong>: ${esc(data.attachment_tipe || '-')}</div>
                            <div class="col-6"><strong>SN Attachment</strong>: ${esc(data.sn_attachment_po)}</div>
                            <div class="col-6"><strong>Mast</strong>: ${esc(data.tipe_mast)}</div>
                            <div class="col-6"><strong>SN Mast</strong>: ${esc(data.sn_mast_po)}</div>
                            <div class="col-6"><strong>Mesin</strong>: ${esc((data.merk_mesin||'-') + ' ' + (data.model_mesin||''))}</div>
                            <div class="col-6"><strong>SN Mesin</strong>: ${esc(data.sn_mesin_po)}</div>
                            <div class="col-6"><strong>Baterai</strong>: ${esc(data.tipe_baterai)}</div>
                            <div class="col-6"><strong>SN Baterai</strong>: ${esc(data.sn_baterai_po)}</div>
                            <div class="col-6"><strong>Charger</strong>: ${esc(data.tipe_charger)}</div>
                            <div class="col-6"><strong>SN Charger</strong>: ${esc(data.sn_charger_po)}</div>
                            <div class="col-6"><strong>Ban</strong>: ${esc(data.tipe_ban)}</div>
                            <div class="col-6"><strong>Roda</strong>: ${esc(data.tipe_roda)}</div>
                            <div class="col-6"><strong>Valve</strong>: ${esc(data.jumlah_valve)}</div>
                            <div class="col-6"><strong>Aksesoris</strong>: ${esc(data.aksesoris_unit)}</div>
                        </div>
                        <div class="col-12"><hr></div>
                        <div class="col-12"><strong>Catatan:</strong> ${esc(data.catatan_unit)}</div>
                        `;
				    })
				    .catch(error => {
				        const host = document.getElementById('svcUnitDetailBlock');
				        if (host) {
				            host.innerHTML = `<div class="alert alert-danger">Error loading unit details: ${error.message}</div>`;
				        }
				        console.error('Error loading unit details:', error);
				    });
			}
            
            // Show the modal after data is loaded
            const modal = document.getElementById('spkDetailModal');
            if (modal) {
                new bootstrap.Modal(modal).show();
            } else {
                console.error('SPK detail modal element not found');
            }
        }).catch(error => {
            body.innerHTML = `<div class="alert alert-danger">Error loading SPK details: ${error.message}</div>`;
            console.error('Error loading SPK details:', error);
            
            // Show the modal even on error
            const modal = document.getElementById('spkDetailModal');
            if (modal) {
                new bootstrap.Modal(modal).show();
            }
        });
    }
    
    // Edit SPK function
    function editSpk() {
        if (!currentSpkId) {
            if (window.OptimaPro && typeof OptimaPro.showNotification === 'function') {
                OptimaPro.showNotification('SPK ID tidak ditemukan', 'error');
            } else if (typeof showNotification === 'function') {
                showNotification('SPK ID tidak ditemukan', 'error');
            } else {
                alert('SPK ID tidak ditemukan');
            }
            return;
        }
        
        // Close detail modal first
        const detailModal = bootstrap.Modal.getInstance(document.getElementById('spkDetailModal'));
        if (detailModal) detailModal.hide();
        
        // Load SPK data for editing
        fetch(`<?= base_url('marketing/spk/detail/') ?>${currentSpkId}`)
            .then(r => r.json())
            .then(j => {
                if (j.success) {
                    // Pre-populate edit form with current data
                    populateEditForm(j.data);
                    // Show edit modal
                    new bootstrap.Modal(document.getElementById('spkEditModal')).show();
                } else {
                    if (window.OptimaPro && typeof OptimaPro.showNotification === 'function') {
                        OptimaPro.showNotification('Gagal memuat data SPK untuk edit', 'error');
                    } else if (typeof showNotification === 'function') {
                        showNotification('Gagal memuat data SPK untuk edit', 'error');
                    } else {
                        alert('Gagal memuat data SPK untuk edit');
                    }
                }
            })
            .catch(error => {
                console.error('Error loading SPK for edit:', error);
                if (window.OptimaPro && typeof OptimaPro.showNotification === 'function') {
                    OptimaPro.showNotification('Error loading SPK data', 'error');
                } else if (typeof showNotification === 'function') {
                    showNotification('Error loading SPK data', 'error');
                } else {
                    alert('Error loading SPK data');
                }
            });
    }
    
    // Delete SPK function with double confirmation
    function deleteSpk() {
        if (!currentSpkId) {
            if (window.OptimaPro && typeof OptimaPro.showNotification === 'function') {
                OptimaPro.showNotification('SPK ID tidak ditemukan', 'error');
            } else if (typeof showNotification === 'function') {
                showNotification('SPK ID tidak ditemukan', 'error');
            } else {
                alert('SPK ID tidak ditemukan');
            }
            return;
        }
        
        // First confirmation
        if (!confirm('Apakah Anda yakin ingin menghapus SPK ini?')) {
            return;
        }
        
        // Second confirmation
        if (!confirm('PERINGATAN: Tindakan ini tidak dapat dibatalkan!\n\nApakah Anda benar-benar yakin ingin menghapus SPK ini?')) {
            return;
        }
        
        // Proceed with deletion
        fetch(`<?= base_url('marketing/spk/delete/') ?>${currentSpkId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            }
        })
        .then(r => r.json())
        .then(j => {
            if (j.success) {
                // Close detail modal
                const detailModal = bootstrap.Modal.getInstance(document.getElementById('spkDetailModal'));
                if (detailModal) detailModal.hide();
                
                // Reload SPK DataTable
                if (spkTable && spkTable.ajax) spkTable.ajax.reload();
                
                if (window.OptimaPro && typeof OptimaPro.showNotification === 'function') {
                    OptimaPro.showNotification('SPK berhasil dihapus', 'success');
                } else if (typeof showNotification === 'function') {
                    showNotification('SPK berhasil dihapus', 'success');
                } else {
                    alert('SPK berhasil dihapus');
                }
            } else {
                const errorMsg = j.message || 'Gagal menghapus SPK';
                if (window.OptimaPro && typeof OptimaPro.showNotification === 'function') {
                    OptimaPro.showNotification(errorMsg, 'error');
                } else if (typeof showNotification === 'function') {
                    showNotification(errorMsg, 'error');
                } else {
                    alert(errorMsg);
                }
            }
        })
        .catch(error => {
            console.error('Error deleting SPK:', error);
            const errorMsg = 'Error deleting SPK: ' + error.message;
            if (window.OptimaPro && typeof OptimaPro.showNotification === 'function') {
                OptimaPro.showNotification(errorMsg, 'error');
            } else if (typeof showNotification === 'function') {
                showNotification(errorMsg, 'error');
            } else {
                alert(errorMsg);
            }
        });
    }
    
    // Function to populate edit form
    function populateEditForm(data) {
        document.getElementById('editSpkId').value = data.id || '';
        document.getElementById('editNomorSpk').value = data.nomor_spk || '';
        document.getElementById('editJenisSpk').value = data.jenis_spk || 'UNIT';
        document.getElementById('editPoKontrak').value = data.po_kontrak_nomor || '';
        document.getElementById('editPelanggan').value = data.pelanggan || '';
        document.getElementById('editPic').value = data.pic || '';
        document.getElementById('editKontak').value = data.kontak || '';
        document.getElementById('editLokasi').value = data.lokasi || '';
        document.getElementById('editDeliveryPlan').value = data.delivery_plan || '';
        document.getElementById('editStatus').value = data.status || 'SUBMITTED';
        document.getElementById('editCatatan').value = data.catatan || '';
    }
    
    // SPK Edit form submission
    document.addEventListener('DOMContentLoaded', function() {
        const spkEditForm = document.getElementById('spkEditForm');
        if (spkEditForm) {
            spkEditForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const spkId = formData.get('id');
                
                // Debug: Log form data
                console.log('SPK Edit Form Data:', {
                    spkId: spkId,
                    jenis_spk: formData.get('jenis_spk'),
                    po_kontrak_nomor: formData.get('po_kontrak_nomor'),
                    pelanggan: formData.get('pelanggan'),
                    status: formData.get('status')
                });
                
                fetch(`<?= base_url('marketing/spk/update/') ?>${spkId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    body: new URLSearchParams(formData)
                })
                .then(r => {
                    console.log('Response status:', r.status);
                    return r.json();
                })
                .then(j => {
                    console.log('Response data:', j);
                    if (j.success) {
                        // Close edit modal
                        const editModal = bootstrap.Modal.getInstance(document.getElementById('spkEditModal'));
                        if (editModal) editModal.hide();
                        
                        // Reload SPK DataTable
                        if (spkTable && spkTable.ajax) spkTable.ajax.reload();
                        
                        // Show success notification
                        const successMsg = 'SPK berhasil diperbarui! Status: ' + (j.data?.status || 'Unknown');
                        if (window.OptimaPro && typeof OptimaPro.showNotification === 'function') {
                            OptimaPro.showNotification(successMsg, 'success');
                        } else if (typeof showNotification === 'function') {
                            showNotification(successMsg, 'success');
                        } else {
                            alert(successMsg);
                        }
                    } else {
                        // Show error notification
                        const errorMsg = j.message || 'Gagal memperbarui SPK';
                        if (window.OptimaPro && typeof OptimaPro.showNotification === 'function') {
                            OptimaPro.showNotification(errorMsg, 'error');
                        } else if (typeof showNotification === 'function') {
                            showNotification(errorMsg, 'error');
                        } else {
                            alert(errorMsg);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error updating SPK:', error);
                    const errorMsg = 'Terjadi kesalahan saat memperbarui SPK: ' + error.message;
                    if (window.OptimaPro && typeof OptimaPro.showNotification === 'function') {
                        OptimaPro.showNotification(errorMsg, 'error');
                    } else if (typeof showNotification === 'function') {
                        showNotification(errorMsg, 'error');
                    } else {
                        alert(errorMsg);
                    }
                });
            });
        }
    });
    
    // Auto-trigger modal if autoOpenSpkId is set (from notification deep linking)
    <?php if (isset($autoOpenSpkId) && $autoOpenSpkId): ?>
    console.log('🔔 Auto-opening SPK modal from notification: <?= $autoOpenSpkId ?>');
    setTimeout(() => {
        if (typeof openDetail === 'function') {
            openDetail(<?= $autoOpenSpkId ?>);
        } else {
            console.error('❌ openDetail function not found');
        }
    }, 800); // Wait for page to fully load
    <?php endif; ?>
    
    // ==========================================
    // Link SPK to Contract Functions
    // ==========================================
    function showLinkContractModal(spkId, spkNumber) {
        document.getElementById('link_spk_id').value = spkId;
        document.getElementById('link_spk_number').value = spkNumber;
        
        console.log('🔍 Loading active contracts...');
        
        // Load active contracts
        fetch('<?= base_url('marketing/kontrak/get-active-contracts') ?>')
            .then(response => {
                console.log('📥 Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('📦 Contracts data:', data);
                const select = document.getElementById('link_contract_id');
                select.innerHTML = '<option value="">-- Select Contract --</option>';
                
                if (data.success && data.data && data.data.length > 0) {
                    console.log(`✅ Found ${data.data.length} active contracts`);
                    data.data.forEach(contract => {
                        const option = document.createElement('option');
                        option.value = contract.id;
                        option.textContent = `${contract.no_kontrak} - ${contract.pelanggan || contract.nama_customer}`;
                        select.appendChild(option);
                    });
                } else {
                    console.warn('⚠️ No active contracts found');
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = '-- No Active Contracts Available --';
                    option.disabled = true;
                    select.appendChild(option);
                }
            })
            .catch(error => {
                console.error('❌ Error loading contracts:', error);
                alert('Failed to load contracts. Please check console for details.');
            });
        
        const modal = new bootstrap.Modal(document.getElementById('linkContractModal'));
        modal.show();
    }
    
    function submitLinkContract(event) {
        event.preventDefault();
        
        const formData = new FormData(document.getElementById('linkContractForm'));
        
        // Add CSRF token
        formData.append(window.csrfTokenName, window.csrfToken || '<?= csrf_hash() ?>');
        
        const submitBtn = event.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Linking...';
        
        console.log('🔗 Linking SPK to contract...');
        
        fetch('<?= base_url('marketing/spk/link-to-contract') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            
            if (data.success) {
                alert(`✅ Success! SPK linked to contract.\\n\\n${data.di_count || 0} Delivery Instruction(s) have been updated and unlocked for invoicing.`);
                bootstrap.Modal.getInstance(document.getElementById('linkContractModal')).hide();
                if (spkTable && spkTable.ajax) spkTable.ajax.reload(); // Reload DataTable
            } else {
                alert('❌ Error: ' + (data.message || 'Failed to link contract'));
            }
        })
        .catch(error => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            console.error('Error:', error);
            alert('❌ Failed to link contract. Please try again.');
        });
    }
    </script>
    
    <!-- SPK modal scroll: see optima-pro.css #spkModal .modal-body -->

    <!-- SPK Edit Modal -->
    <div class="modal fade" id="spkEditModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><?= lang('Marketing.spk_edit') ?></h6>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="spkEditForm">
                    <input type="hidden" id="editSpkId" name="id">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">SPK Number</label>
                                <input type="text" class="form-control" id="editNomorSpk" name="nomor_spk" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">SPK Type</label>
                                <select class="form-select" id="editJenisSpk" name="jenis_spk">
                                    <option value="UNIT">UNIT</option>
                                    <option value="ATTACHMENT">ATTACHMENT</option>
                                    <option value="TUKAR">EXCHANGE</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">PO Contract</label>
                                <input type="text" class="form-control" id="editPoKontrak" name="po_kontrak_nomor">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Customer</label>
                                <input type="text" class="form-control" id="editPelanggan" name="pelanggan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">PIC</label>
                                <input type="text" class="form-control" id="editPic" name="pic">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact</label>
                                <input type="text" class="form-control" id="editKontak" name="kontak">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" id="editLokasi" name="lokasi">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Delivery Plan</label>
                                <input type="date" class="form-control" id="editDeliveryPlan" name="delivery_plan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="editStatus" name="status">
                                    <option value="DRAFT">DRAFT</option>
                                    <option value="SUBMITTED">SUBMITTED</option>
                                    <option value="IN_PROGRESS">IN PROGRESS</option>
                                    <option value="READY">READY</option>
                                    <option value="COMPLETED">COMPLETED</option>
                                    <option value="DELIVERED">DELIVERED</option>
                                    <option value="CANCELLED">CANCELLED</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" id="editCatatan" name="catatan" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <?= ui_button('cancel', 'Cancel', ['data-bs-dismiss' => 'modal', 'color' => 'secondary']) ?>
                        <?= ui_button('save', 'Save Changes', ['type' => 'submit']) ?>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
    
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<!-- svcUnitDetailBlock: see optima-pro.css SPK MARKETING PAGE section -->
