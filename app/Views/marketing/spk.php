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
        <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-3">
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
        <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-3">
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
        <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-3">
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
        <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-3">
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
                <button class="btn btn-sm btn-outline-secondary" onclick="refreshSPKTable()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                </button>
                <?php if ($can_create): ?>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#spkModal">
                        <i class="fas fa-plus me-1"></i><?= lang('Marketing.create_spk') ?>
                    </button>
                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="visually-hidden">Toggle</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#spkModal">
                                <i class="fas fa-file-alt me-2 text-primary"></i>Via Quotation
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#directSpkModal">
                                <i class="fas fa-bolt me-2 text-success"></i>Langsung (Direct)
                            </a>
                        </li>
                    </ul>
                </div>
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
        <div class="px-3 pt-3 pb-0 border-bottom">
            <ul class="nav nav-tabs border-0" id="filterTabs">
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
        </div>
        
        <div class="card-body">
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
                                        <option value="UNIT" selected><?= lang('Marketing.spk_unit') ?> (New Unit)</option>
                                        <option value="ATTACHMENT"><?= lang('Marketing.spk_attachment') ?> (Attachment/Fork Replacement)</option>
                                    </select>
                                    <div class="form-text" id="spkTypeHelp">
                                        <strong>UNIT:</strong> Proses lengkap unit baru (Persiapan → Install → Painting → PDI)<br>
                                        <strong>ATTACHMENT:</strong> Penggantian attachment/fork pada unit existing (Install → Painting → PDI)
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-tools me-1"></i>Jenis Proses Install</label>
                                    <div class="form-check form-switch mt-1">
                                        <input class="form-check-input" type="checkbox" role="switch" id="hasFabrikasiToggle" name="has_fabrikasi" value="1">
                                        <label class="form-check-label fw-semibold" for="hasFabrikasiToggle">Ada fabrikasi custom (non-standard)?</label>
                                    </div>
                                    <div class="form-text">
                                        <strong>OFF</strong> — Pasang attach/fork standar off-the-shelf (alur: Install → Painting → PDI)<br>
                                        <strong>ON</strong> — Attach/fork perlu dibuat/dimodifikasi custom, mis. welding, ukuran khusus (alur: Fabrikasi → Painting → PDI)
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
                                            <label class="form-label"><?= lang('Marketing.select_location') ?></label>
                                            <div class="form-control bg-light">
                                                Lokasi customer dipilih saat Create DI.
                                            </div>
                                            <div class="form-text">Tahap SPK tetap global, pemilahan lokasi dilakukan di DI.</div>
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

    <!-- Create DI Modal (aligned with Marketing > DI > Create DI: command type/purpose first, badge Select2, deskripsi bantuan) -->
    <div class="modal fade modal-wide" id="diModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h6 class="modal-title"><?= lang('Marketing.create') ?> Delivery Instruction</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <form id="diForm">
                    <div class="modal-body">
                        <input type="hidden" name="spk_id" id="diSpkId">
                        <input type="hidden" name="tarik_contract_id" id="spkTarikContractId">

                        <!-- Step 1: Jenis & tujuan (sama seperti di.php) -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label"><?= lang('Marketing.command_type') ?> <span class="text-danger">*</span></label>
                                <select class="form-select" name="jenis_perintah_kerja_id" id="spkJenisPerintah" required>
                                    <option value="">-- <?= lang('Marketing.select_command_type') ?> --</option>
                                </select>
                                <div id="spkHelpJenisPerintah" class="di-workflow-help form-text small border-start border-3 border-primary ps-2 mt-1 text-muted"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><?= lang('Marketing.command_purpose') ?> <span class="text-danger">*</span></label>
                                <select class="form-select" name="tujuan_perintah_kerja_id" id="spkTujuanPerintah" required disabled>
                                    <option value="">-- <?= lang('Marketing.select_command_type_first') ?> --</option>
                                </select>
                                <div id="spkHelpTujuanPerintah" class="di-workflow-help form-text small border-start border-3 border-secondary ps-2 mt-1 text-muted"></div>
                            </div>
                        </div>

                        <!-- Ringkasan SPK / kontrak (Customer di atas Contract — alur pilih customer → kontrak) -->
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body py-3 px-3">
                                <div class="small text-uppercase text-muted fw-semibold mb-2"><?= lang('Marketing.contract_po') ?> / SPK</div>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label small mb-0 text-muted"><?= lang('Marketing.customer') ?></label>
                                        <input class="form-control form-control-sm" id="diPelanggan" readonly tabindex="-1">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small mb-0 text-muted"><?= lang('Marketing.po_contract') ?? 'Contract/PO' ?></label>
                                        <input class="form-control form-control-sm" id="diPoNo" readonly tabindex="-1">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small mb-0 text-muted">SPK No.</label>
                                        <input class="form-control form-control-sm" id="diNoSpk" readonly tabindex="-1">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small mb-0 text-muted"><?= lang('Marketing.location') ?></label>
                                        <input class="form-control form-control-sm" id="diLokasi" readonly tabindex="-1">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- EXCHANGE Workflow Section: PULL units from contract -->
                        <div id="spkTukarWorkflow" class="d-none mb-3">
                            <div class="alert alert-info">
                                <i class="fas fa-exchange-alt"></i> 
                                <strong>EXCHANGE Workflow:</strong> Pilih kontrak &amp; unit yang akan ditarik
                            </div>

                            <!-- ANTAR+TARIK: choose customer first, then contract -->
                            <div class="mb-2 d-none" id="spkTarikCustomerSection">
                                <label class="form-label fw-semibold">Customer <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" id="spkTarikCustomerSelect">
                                    <option value="">-- Pilih Customer --</option>
                                </select>
                                <small class="text-muted">Pilih customer terlebih dahulu, lalu pilih kontrak unit lama.</small>
                            </div>
                            
                            <!-- Pilih kontrak unit lama -->
                            <div class="mb-2">
                                <label class="form-label fw-semibold">Kontrak Unit Lama <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" id="spkTarikKontrak">
                                    <option value="">-- Memuat kontrak... --</option>
                                </select>
                                <small class="text-muted">Default: kontrak SPK ini. Pilih lain jika unit lama dari kontrak berbeda.</small>
                            </div>
                            
                            <!-- Unit PULL Section for EXCHANGE -->
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fas fa-minus-circle"></i> Unit yang Akan Ditarik</h6>
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
                        <!-- Customer Location - Required for DI (sama seperti di.php) -->
                        <div class="mb-2">
                            <label class="form-label"><?= lang('App.customer_location') ?> <span class="text-danger">*</span></label>
                            <select class="form-select" name="customer_location_id" id="customerLocationSelect" required disabled>
                                <option value="">-- <?= lang('Marketing.select_location') ?> --</option>
                            </select>
                            <small class="text-muted">Lokasi customer wajib dipilih pada tahap DI.</small>
                        </div>

                        <div class="row g-2">
                            <div class="col-6"><label class="form-label">Delivery Date</label><input type="date" class="form-control" name="tanggal_kirim"></div>
                            <div class="col-6 d-flex align-items-end"><span class="text-muted small">Optional</span></div>
                        </div>
                        <div class="mt-2"><label class="form-label">Notes</label><textarea class="form-control" name="catatan" rows="3" placeholder="Delivery instructions (optional)"></textarea></div>
                    </div>
                    <div class="modal-footer">
                        <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal', 'color' => 'secondary']) ?>
                        <?= ui_button('submit', 'Create DI', ['type' => 'submit']) ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
      /* SPK Create DI modal — Command Type / Purpose Select2 (badge + deskripsi), konsisten dengan marketing/di */
      #diModal .select2-container--default .select2-results__option .di-workflow-opt {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
        padding: 2px 0;
      }
      #diModal .select2-container--default .select2-selection--single {
        min-height: 38px;
        border-radius: 0.375rem;
      }
      #diModal .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-top: 4px;
        padding-bottom: 4px;
        line-height: 1.35;
      }
      #diModal .select2-container--default .select2-selection--single .di-workflow-opt .badge {
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.02em;
      }
      #diModal .di-workflow-help {
        min-height: 0;
        white-space: pre-line;
        line-height: 1.4;
      }
    </style>

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
                        <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal', 'color' => 'secondary']) ?>
                        <?= ui_button('submit', 'Link Contract', ['type' => 'submit', 'icon' => 'fas fa-link']) ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?= $this->include('partials/accessory_js') ?>
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

    /* ── Create DI modal (SPK): Select2 badge + deskripsi — selaras marketing/di ── */
    function escapeHtmlWorkflow(s) {
        return String(s ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/"/g, '&quot;');
    }
    function workflowBadgeSoftClass(kode) {
        const k = String(kode || '').toUpperCase();
        if (k.startsWith('ANTAR')) return 'badge-soft-cyan';
        if (k.startsWith('TARIK')) return 'badge-soft-orange';
        if (k.startsWith('TUKAR')) return 'badge-soft-purple';
        if (k.startsWith('RELOKASI')) return 'badge-soft-green';
        return 'badge-soft-blue';
    }
    function formatWorkflowSelectOptionSelection(data) {
        if (typeof jQuery === 'undefined') return data.text;
        if (!data.id) return data.text;
        const el = data.element;
        if (!el) return data.text;
        const kode = el.getAttribute('data-kode');
        const nama = el.getAttribute('data-nama');
        if (!kode) return data.text;
        const bc = workflowBadgeSoftClass(kode);
        const html = '<span class="di-workflow-opt d-flex align-items-center gap-2 flex-wrap">' +
            '<span class="badge ' + bc + '">' + escapeHtmlWorkflow(kode) + '</span>' +
            '<span class="text-body">' + escapeHtmlWorkflow(nama) + '</span></span>';
        return jQuery(html);
    }
    function formatWorkflowSelectOptionResult(data) {
        if (typeof jQuery === 'undefined') return data.text;
        if (!data.id) return data.text;
        const el = data.element;
        if (!el) return data.text;
        const kode = el.getAttribute('data-kode');
        const nama = el.getAttribute('data-nama');
        const desk = (el.getAttribute('data-deskripsi') || '').trim();
        if (!kode) return data.text;
        const bc = workflowBadgeSoftClass(kode);
        let html = '<span class="di-workflow-opt di-workflow-opt--open">' +
            '<span class="d-flex align-items-center gap-2 flex-wrap">' +
            '<span class="badge ' + bc + '">' + escapeHtmlWorkflow(kode) + '</span>' +
            '<span class="text-body fw-medium">' + escapeHtmlWorkflow(nama) + '</span></span>';
        if (desk) {
            html += '<span class="small text-muted d-block mt-1 ps-0" style="max-width:28rem;">' +
                escapeHtmlWorkflow(desk).replace(/\n/g, '<br>') + '</span>';
        }
        html += '</span>';
        return jQuery(html);
    }
    function destroySpkDiWorkflowSelect2() {
        if (typeof jQuery === 'undefined') return;
        ['#spkJenisPerintah', '#spkTujuanPerintah'].forEach(function (sel) {
            const $el = jQuery(sel);
            if ($el.length && $el.hasClass('select2-hidden-accessible')) {
                $el.off('select2:open.spkDiWorkflowZ');
                $el.select2('destroy');
            }
        });
    }
    function initSpkDiWorkflowCommandSelect2(selectId) {
        if (typeof jQuery === 'undefined' || !jQuery.fn.select2) {
            setTimeout(function () { initSpkDiWorkflowCommandSelect2(selectId); }, 80);
            return;
        }
        const $el = jQuery('#' + selectId);
        if (!$el.length) return;
        if ($el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }
        const $modal = jQuery('#diModal');
        const isTujuan = selectId.indexOf('tujuan') !== -1;
        const phJenis = <?= json_encode('-- ' . lang('Marketing.select_command_type') . ' --') ?>;
        const phTujuan = <?= json_encode('-- ' . lang('Marketing.select_command') . ' --') ?>;
        const phTujuanLocked = <?= json_encode('-- ' . lang('Marketing.select_command_type_first') . ' --') ?>;
        const disabled = $el.prop('disabled');
        $el.select2({
            width: '100%',
            dropdownParent: $modal,
            placeholder: isTujuan ? (disabled ? phTujuanLocked : phTujuan) : phJenis,
            allowClear: false,
            templateResult: formatWorkflowSelectOptionResult,
            templateSelection: formatWorkflowSelectOptionSelection,
            escapeMarkup: function (markup) { return markup; }
        });
        $el.off('select2:open.spkDiWorkflowZ').on('select2:open.spkDiWorkflowZ', function () {
            jQuery('.select2-dropdown').last().css('z-index', 10060);
        });
    }

    /** Optima global assistant: toast / Swal — avoid native alert() */
    window.optimaAssistNotify = function (message, type) {
        type = (type || 'info').toLowerCase();
        if (type === 'danger') {
            type = 'error';
        }
        var msg = (message === null || message === undefined) ? '' : String(message);
        if (window.OptimaNotify) {
            if (type === 'error' && typeof OptimaNotify.error === 'function') {
                return OptimaNotify.error(msg);
            }
            if (type === 'warning' && typeof OptimaNotify.warning === 'function') {
                return OptimaNotify.warning(msg);
            }
            if (type === 'success' && typeof OptimaNotify.success === 'function') {
                return OptimaNotify.success(msg);
            }
            if (typeof OptimaNotify.info === 'function') {
                return OptimaNotify.info(msg);
            }
        }
        if (typeof window.createOptimaToast === 'function') {
            var title = type === 'error' ? 'Error' : (type === 'success' ? 'Berhasil' : (type === 'warning' ? 'Peringatan' : 'Info'));
            if (typeof window.lang === 'function') {
                if (type === 'error') {
                    title = window.lang('error');
                } else if (type === 'success') {
                    title = window.lang('success');
                }
            }
            return window.createOptimaToast({
                type: type === 'error' ? 'error' : type,
                title: title,
                message: msg,
                duration: type === 'error' ? 6000 : 4500
            });
        }
        if (typeof window.alertSwal === 'function' && typeof Swal !== 'undefined') {
            window.alertSwal(type === 'success' ? 'success' : (type === 'error' ? 'error' : 'info'), msg, '');
            return;
        }
        window.alert(msg);
    };

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

    function refreshSPKTable() {
        if (spkTable && spkTable.ajax) spkTable.ajax.reload(null, false);
        loadStatistics();
    }
    
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
        const kontrakStatus = (jenis === 'TUKAR' || jenis === 'ANTAR_TARIK') ? 'ACTIVE' : 'PENDING';
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
                        
                        // Load customer locations if customer_id is available
                        if (j.customer_id) {
                            loadCustomerLocations(j.customer_id);
                            const tarikCustomerSelect = document.getElementById('spkTarikCustomerSelect');
                            if (tarikCustomerSelect) {
                                const label = j.pelanggan || j.customer_name || spkData.pelanggan || ('Customer #' + j.customer_id);
                                tarikCustomerSelect.innerHTML = `<option value="">-- Pilih Customer --</option><option value="${j.customer_id}" selected>${label}</option>`;
                            }
                        } else {
                            const locSel = document.getElementById('customerLocationSelect');
                            if (locSel) {
                                locSel.innerHTML = '<option value="">-- No locations (no contract linked) --</option>';
                                locSel.disabled = true;
                            }
                        }
                        
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
            url.searchParams.set('status', (jenis === 'TUKAR' || jenis === 'ANTAR_TARIK') ? 'ACTIVE' : 'PENDING');
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
            locationSelect.disabled = true;
            
            fetch(`<?= base_url('marketing/kontrak/customer-locations/') ?>${customerId}`, {
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
                        locationSelect.disabled = false;
                    } else {
                        locationSelect.innerHTML = '<option value="">No locations available</option>';
                        locationSelect.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error loading locations:', error);
                    locationSelect.innerHTML = '<option value="">Error loading locations</option>';
                    locationSelect.disabled = true;
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
                        
                        // Customer location is selected in DI stage.
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
                        
                        // Customer location is selected in DI stage.
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
                    window.optimaAssistNotify('Unit Tujuan wajib dipilih untuk SPK ATTACHMENT', 'error');
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
            
            (window.csrfFetch || window.fetch)('<?= base_url('marketing/spk/create') ?>',{method:'POST', body:fd})
                .then(r=>r.json()).then(j=>{ 
                    if(j.success){ 
                        e.target.reset(); 
                        if (spkTable && spkTable.ajax) spkTable.ajax.reload(); // Reload DataTable
                        loadMonitoring();
                        bootstrap.Modal.getInstance(document.getElementById('spkModal')).hide();
                        window.optimaAssistNotify('SPK dibuat: ' + (j.nomor||''), 'success');
                    } else {
                        const msg = j.message || 'Gagal membuat SPK';
                        window.optimaAssistNotify(msg, 'error');
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
                window.optimaAssistNotify('Jenis Perintah Kerja harus dipilih.', 'warning');
                return;
            }
            
            if (!tujuanPerintah || tujuanPerintah.trim() === '') {
                window.optimaAssistNotify('Tujuan Perintah harus dipilih.', 'warning');
                return;
            }
            
            const customerLocationId = fd.get('customer_location_id');
            if (!customerLocationId || customerLocationId.trim() === '') {
                window.optimaAssistNotify('Customer Location wajib dipilih.', 'warning');
                return;
            }
            const checks = document.querySelectorAll('.di-unit-check');
                if (checks && checks.length) {
                    const picked = Array.from(checks).filter(ch=>ch.checked).map(ch=>ch.value);
                    if (picked.length === 0) {
                        window.optimaAssistNotify('Select at least one unit for this DI.', 'warning');
                        return;
                    }
                picked.forEach(v=> fd.append('unit_ids[]', v));
            }
            // spk_id already set; backend enforces COMPLETED status
            
            (window.csrfFetch || window.fetch)('<?= base_url('marketing/di/create') ?>',{method:'POST', body:fd})
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
                        window.optimaAssistNotify('DI dibuat: ' + (j.nomor||''), 'success');
                    } else {
                        const msg = (j && j.message) ? j.message : 'Gagal membuat DI';
                        window.optimaAssistNotify(msg, 'error');
                    }
                });
        });
        
        /** Validasi submit Create DI — dipanggil ulang setelah Select2 / async tujuan */
        let spkDiCheckValidity = function () {};

        function bindSpkDiFormValidationOnce() {
            const form = document.getElementById('diForm');
            if (!form || form.dataset.diValidateBound === '1') return;
            form.dataset.diValidateBound = '1';
            const jenisSelect = document.getElementById('spkJenisPerintah');
            const tujuanSelect = document.getElementById('spkTujuanPerintah');
            const submitBtn = form.querySelector('[type="submit"]');
            if (!jenisSelect || !tujuanSelect || !submitBtn) return;

            function checkValidity() {
                const jenisValid = jenisSelect.value.trim() !== '';
                const tujuanValid = !tujuanSelect.disabled && tujuanSelect.value.trim() !== '';
                const isValid = jenisValid && tujuanValid;
                jenisSelect.classList.toggle('is-invalid', !jenisValid && jenisSelect.value !== '');
                jenisSelect.classList.toggle('is-valid', jenisValid);
                tujuanSelect.classList.toggle('is-invalid', !tujuanValid && !tujuanSelect.disabled && tujuanSelect.value !== '');
                tujuanSelect.classList.toggle('is-valid', tujuanValid);
                submitBtn.disabled = !isValid;
            }
            spkDiCheckValidity = checkValidity;

            jenisSelect.addEventListener('change', checkValidity);
            tujuanSelect.addEventListener('change', checkValidity);
            if (typeof jQuery !== 'undefined') {
                jQuery('#spkJenisPerintah').on('select2:select.spkDiVal select2:clear.spkDiVal', checkValidity);
                jQuery('#spkTujuanPerintah').on('select2:select.spkDiVal select2:clear.spkDiVal', checkValidity);
            }
            checkValidity();
        }

        document.getElementById('diModal').addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('diForm');
            destroySpkDiWorkflowSelect2();
            lastSpkTujuanPerintahList = [];
            resetSpkWorkflowHelpTexts();
            if (form) {
                form.reset();
                form.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
                    el.classList.remove('is-valid', 'is-invalid');
                });
                const submitBtn = form.querySelector('[type="submit"]');
                if (submitBtn) submitBtn.disabled = true;
                const locSel = document.getElementById('customerLocationSelect');
                if (locSel) {
                    locSel.innerHTML = '<option value="">-- <?= esc(lang('Marketing.select_location'), 'js') ?> --</option>';
                    locSel.disabled = true;
                }
                const tu = document.getElementById('spkTujuanPerintah');
                if (tu) {
                    tu.innerHTML = '<option value="">-- <?= esc(lang('Marketing.select_command_type_first'), 'js') ?> --</option>';
                    tu.disabled = true;
                }
            }
        });
        
        // =====================================================
        // WORKFLOW BARU: DYNAMIC DROPDOWN SYSTEM FOR SPK DI - FROM DATABASE
        // =====================================================
        
        // Variables to store workflow data
        let spkJenisPerintahOptions = [];
        let lastSpkTujuanPerintahList = [];

        function resetSpkWorkflowHelpTexts() {
            const hj = document.getElementById('spkHelpJenisPerintah');
            const ht = document.getElementById('spkHelpTujuanPerintah');
            if (hj) {
                hj.innerHTML = '<span class="text-muted">Pilih jenis — penjelasan singkat dari master data.</span>';
            }
            if (ht) {
                ht.innerHTML = '<span class="text-muted">Setelah jenis dipilih, pilih tujuan.</span>';
            }
        }

        function updateSpkJenisPerintahHelp() {
            const el = document.getElementById('spkHelpJenisPerintah');
            if (!el) return;
            const sel = document.getElementById('spkJenisPerintah');
            const id = sel && sel.value;
            if (!id) {
                el.innerHTML = '<span class="text-muted">Pilih jenis — penjelasan singkat dari master data.</span>';
                return;
            }
            const opt = spkJenisPerintahOptions.find(o => String(o.id) === String(id));
            const d = opt && opt.deskripsi ? String(opt.deskripsi).trim() : '';
            if (d) {
                el.textContent = d;
            } else {
                el.innerHTML = '<span class="text-warning"><i class="fas fa-info-circle me-1"></i>Deskripsi jenis ini kosong di master data.</span>';
            }
        }

        function updateSpkTujuanPerintahHelp() {
            const el = document.getElementById('spkHelpTujuanPerintah');
            const sel = document.getElementById('spkTujuanPerintah');
            if (!el || !sel) return;
            if (sel.disabled) {
                el.innerHTML = '<span class="text-muted"><?= esc(lang('Marketing.select_command_type_first'), 'js') ?></span>';
                return;
            }
            if (!sel.value) {
                el.innerHTML = '<span class="text-muted">Pilih tujuan — penjelasan singkat dari master data.</span>';
                return;
            }
            const row = lastSpkTujuanPerintahList.find(r => String(r.id) === String(sel.value));
            const d = row && row.deskripsi ? String(row.deskripsi).trim() : '';
            if (d) {
                el.textContent = d;
            } else {
                el.innerHTML = '<span class="text-warning"><i class="fas fa-info-circle me-1"></i>Deskripsi tujuan ini kosong di master data.</span>';
            }
        }

        function getSpkSelectedJenisKode() {
            const jenisSelect = document.getElementById('spkJenisPerintah');
            const selectedId = parseInt(jenisSelect && jenisSelect.value, 10);
            const opt = spkJenisPerintahOptions.find(o => parseInt(o.id, 10) === selectedId);
            return opt ? String(opt.kode).toUpperCase() : '';
        }

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
        
        // Populate jenis perintah dropdown for SPK modal (+ Select2 badge seperti marketing/di)
        function populateSpkJenisPerintahDropdown() {
            const jenisSelect = document.getElementById('spkJenisPerintah');
            if (!jenisSelect) return;

            jenisSelect.innerHTML = '<option value="">-- <?= esc(lang('Marketing.select_command_type'), 'js') ?> --</option>';
            spkJenisPerintahOptions.forEach(option => {
                const optionElement = document.createElement('option');
                optionElement.value = option.id;
                optionElement.setAttribute('data-kode', option.kode || '');
                optionElement.setAttribute('data-nama', option.nama || '');
                optionElement.setAttribute('data-deskripsi', (option.deskripsi || '').trim());
                optionElement.textContent = `${option.kode} - ${option.nama}`;
                optionElement.title = (option.deskripsi || '').trim() || option.nama || '';
                jenisSelect.appendChild(optionElement);
            });
            initSpkDiWorkflowCommandSelect2('spkJenisPerintah');
            updateSpkJenisPerintahHelp();
            updateSpkTujuanPerintahHelp();
            spkDiCheckValidity();
        }

        async function loadSpkTujuanPerintahOptions(jenisId) {
            try {
                if (typeof jQuery !== 'undefined') {
                    const $t = jQuery('#spkTujuanPerintah');
                    if ($t.length && $t.hasClass('select2-hidden-accessible')) {
                        $t.off('select2:open.spkDiWorkflowZ');
                        $t.select2('destroy');
                    }
                }

                const response = await fetch(`<?= base_url('marketing/get-tujuan-perintah-kerja') ?>?jenis_id=${jenisId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                });
                const result = await response.json();

                const tujuanSelect = document.getElementById('spkTujuanPerintah');
                if (!tujuanSelect) return;

                if (result.success) {
                    lastSpkTujuanPerintahList = result.data || [];
                    tujuanSelect.innerHTML = '<option value="">-- <?= esc(lang('Marketing.select_command'), 'js') ?> --</option>';
                    tujuanSelect.disabled = false;
                    if (typeof jQuery !== 'undefined') {
                        jQuery('#spkTujuanPerintah').prop('disabled', false);
                    }

                    result.data.forEach(option => {
                        const optionElement = document.createElement('option');
                        optionElement.value = option.id;
                        optionElement.setAttribute('data-kode', option.kode || '');
                        optionElement.setAttribute('data-nama', option.nama || '');
                        const desk = (option.deskripsi || '').trim();
                        optionElement.setAttribute('data-deskripsi', desk);
                        optionElement.textContent = `${option.kode} - ${option.nama}`;
                        optionElement.title = desk || option.nama || '';
                        tujuanSelect.appendChild(optionElement);
                    });

                    initSpkDiWorkflowCommandSelect2('spkTujuanPerintah');
                    updateSpkTujuanPerintahHelp();
                    spkDiCheckValidity();
                } else {
                    console.error('Failed to load SPK tujuan perintah options:', result.message);
                }
            } catch (error) {
                console.error('Error loading SPK tujuan perintah options:', error);
            }
        }

        let _spkJenisChangeRaf = null;
        function scheduleSpkJenisChange() {
            if (_spkJenisChangeRaf !== null) cancelAnimationFrame(_spkJenisChangeRaf);
            _spkJenisChangeRaf = requestAnimationFrame(function () {
                _spkJenisChangeRaf = null;
                handleSpkJenisPerintahChange();
            });
        }

        async function handleSpkJenisPerintahChange() {
            const jenisSelect = document.getElementById('spkJenisPerintah');
            const tujuanSelect = document.getElementById('spkTujuanPerintah');
            if (!jenisSelect || !tujuanSelect) return;

            const jenisId = jenisSelect.value;
            if (typeof jQuery !== 'undefined') {
                const $t = jQuery('#spkTujuanPerintah');
                if ($t.length && $t.hasClass('select2-hidden-accessible')) {
                    $t.off('select2:open.spkDiWorkflowZ');
                    $t.select2('destroy');
                }
            }

            tujuanSelect.innerHTML = '<option value="">-- <?= esc(lang('Marketing.select_command_type_first'), 'js') ?> --</option>';
            tujuanSelect.disabled = true;
            if (typeof jQuery !== 'undefined') {
                jQuery('#spkTujuanPerintah').prop('disabled', true);
            }
            lastSpkTujuanPerintahList = [];
            initSpkDiWorkflowCommandSelect2('spkTujuanPerintah');

            const kode = getSpkSelectedJenisKode();
            const isTukarWorkflow = kode === 'TUKAR' || kode === 'ANTAR_TARIK';
            const isAntarTarikSpk = kode === 'ANTAR_TARIK';
            handleSpkTukarWorkflowVisibility(isTukarWorkflow, isAntarTarikSpk);

            if (jenisId) {
                await loadSpkTujuanPerintahOptions(jenisId);
            }

            updateSpkJenisPerintahHelp();
            updateSpkTujuanPerintahHelp();
            spkDiCheckValidity();
        }

        function setupSpkWorkflowDropdowns() {
            const jenisSelect = document.getElementById('spkJenisPerintah');
            const tujuanSelect = document.getElementById('spkTujuanPerintah');
            if (!jenisSelect || !tujuanSelect) return;
            if (jenisSelect.dataset.spkWorkflowBound === '1') return;
            jenisSelect.dataset.spkWorkflowBound = '1';

            jenisSelect.addEventListener('change', scheduleSpkJenisChange);
            tujuanSelect.addEventListener('change', function () {
                updateSpkTujuanPerintahHelp();
                spkDiCheckValidity();
            });
            if (typeof jQuery !== 'undefined') {
                jQuery('#spkJenisPerintah')
                    .on('select2:select.spkDiFlow select2:clear.spkDiFlow', scheduleSpkJenisChange);
                jQuery('#spkTujuanPerintah')
                    .on('select2:select.spkDiFlow select2:clear.spkDiFlow', function () {
                        updateSpkTujuanPerintahHelp();
                        spkDiCheckValidity();
                    });
            }
        }
        
        // Handle TUKAR / ANTAR+TARIK workflow visibility and setup
        function handleSpkTukarWorkflowVisibility(isTukarWorkflow, isAntarTarikSpk = false) {
            const tukarWorkflow = document.getElementById('spkTukarWorkflow');
            const standardItems = document.getElementById('diUnitsPick'); // Standard item selection
            const itemSummary = document.getElementById('diSelectedSummary');
            const tarikCustomerSection = document.getElementById('spkTarikCustomerSection');
            
            if (!tukarWorkflow) {
                console.warn('SPK TUKAR workflow element not found');
                return;
            }
            
            if (isTukarWorkflow) {
                // Show TUKAR / ANTAR+TARIK workflow components
                tukarWorkflow.classList.remove('d-none');
                tukarWorkflow.style.display = '';
                if (tarikCustomerSection) {
                    tarikCustomerSection.classList.toggle('d-none', !isAntarTarikSpk);
                }
                
                // Keep standard item selection visible for TUKAR (items KIRIM from SPK)
                const modeLabel = isAntarTarikSpk ? 'Mode ANTAR+TARIK' : 'Mode TUKAR';
                if (itemSummary) {
                    itemSummary.innerHTML = `<div class="text-info"><i class="fas fa-exchange-alt"></i> <strong>${modeLabel}:</strong> Unit KIRIM (dari SPK ini) + Unit TARIK (dari kontrak yang dipilih)</div>`;
                }
                
                // Setup kontrak change handler dulu, lalu load kontrak + units
                setupSpkKontrakChangeHandler();
                if (isAntarTarikSpk) {
                    setupSpkTarikCustomerSelector();
                    loadSpkTarikUnitsFromSpkKontrak(document.getElementById('spkTarikCustomerSelect')?.value || '');
                } else {
                    loadSpkTarikUnitsFromSpkKontrak();
                }
            } else {
                // Hide TUKAR workflow components
                tukarWorkflow.classList.add('d-none');
                tukarWorkflow.style.display = '';
                if (tarikCustomerSection) tarikCustomerSection.classList.add('d-none');
                
                // Reset TUKAR form fields
                resetSpkTukarWorkflowFields();
                
                // Reset item summary to normal
                if (itemSummary) {
                    itemSummary.innerHTML = '<span class="text-muted">Belum ada ringkasan.</span>';
                }
            }
        }

        function setupSpkTarikCustomerSelector() {
            const customerSelect = document.getElementById('spkTarikCustomerSelect');
            if (!customerSelect) return;
            if (customerSelect.dataset.bound === '1') return;
            customerSelect.dataset.bound = '1';

            customerSelect.addEventListener('change', function() {
                const customerId = this.value || '';
                loadSpkTarikUnitsFromSpkKontrak(customerId);
            });
        }
        
        // Load unit TARIK dari kontrak — load kontrak customer dulu, pre-select kontrak SPK
        function loadSpkTarikUnitsFromSpkKontrak(preferredCustomerId = '') {
            const spkId = document.getElementById('diSpkId').value;
            if (!spkId) {
                console.error('SPK ID not found for TUKAR workflow');
                return;
            }

            const kontrakSelect = document.getElementById('spkTarikKontrak');
            const unitList = document.getElementById('spkTarikUnitList');
            const customerSelect = document.getElementById('spkTarikCustomerSelect');
            if (!kontrakSelect || !unitList) return;

            kontrakSelect.innerHTML = '<option value="">-- Memuat kontrak... --</option>';
            unitList.innerHTML = '<div class="text-muted small">Memuat kontrak...</div>';

            // Fetch SPK detail untuk dapatkan kontrak_id dan customer_id
            fetch(`<?= base_url('marketing/spk/detail/') ?>${spkId}`)
                .then(r => r.json())
                .then(j => {
                    if (!j || !j.success) {
                        unitList.innerHTML = '<div class="text-danger small">Gagal memuat data SPK.</div>';
                        return;
                    }
                    const spkKontrakId = j.data?.kontrak_id || '';
                    const spkCustomerId = j.data?.customer_id || j.customer_id || '';
                    const customerId = preferredCustomerId || (customerSelect?.value || '') || spkCustomerId;

                    if (customerSelect) {
                        const spkCustomerLabel = j.data?.pelanggan || j.data?.customer_name || document.getElementById('diPelanggan')?.value || '';
                        if (!customerSelect.value && spkCustomerId) {
                            customerSelect.innerHTML = `<option value="">-- Pilih Customer --</option><option value="${spkCustomerId}" selected>${spkCustomerLabel || ('Customer #' + spkCustomerId)}</option>`;
                        } else if (customerSelect.value && !customerSelect.querySelector(`option[value="${customerSelect.value}"]`)) {
                            const selected = new Option(customerSelect.value, customerSelect.value, true, true);
                            customerSelect.appendChild(selected);
                        }
                    }

                    // Load kontrak customer (filtered)
                    const tarikUrl = '<?= base_url('marketing/kontrak/get-contracts-for-tarik') ?>' +
                        (customerId ? '?customer_id=' + encodeURIComponent(customerId) : '');

                    fetch(tarikUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(r => r.json())
                        .then(res => {
                            if (res.success && res.data) {
                                kontrakSelect.innerHTML = res.data.map(k =>
                                    `<option value="${k.id}">${k.label} (${k.unit_count} unit)</option>`
                                ).join('');
                                // Pre-select kontrak SPK
                                if (spkKontrakId) {
                                    kontrakSelect.value = String(spkKontrakId);
                                    document.getElementById('spkTarikContractId').value = String(spkKontrakId);
                                } else if (res.data.length > 0) {
                                    kontrakSelect.value = String(res.data[0].id);
                                    document.getElementById('spkTarikContractId').value = String(res.data[0].id);
                                }
                                // Load units dari kontrak yang terpilih
                                if (kontrakSelect.value) loadSpkTarikUnits(kontrakSelect.value);
                            } else {
                                kontrakSelect.innerHTML = '<option value="">Tidak ada kontrak aktif</option>';
                                unitList.innerHTML = '<div class="text-muted small">Tidak ada kontrak untuk customer ini.</div>';
                            }
                        })
                        .catch(err => {
                            console.error('Error fetching contracts for tarik:', err);
                            unitList.innerHTML = '<div class="text-danger small">Error memuat kontrak.</div>';
                        });
                })
                .catch(error => {
                    console.error('Error fetching SPK detail:', error);
                    unitList.innerHTML = '<div class="text-danger small">Error loading SPK data.</div>';
                });
        }
        
        // Setup kontrak selection change handler for SPK TUKAR workflow
        function setupSpkKontrakChangeHandler() {
            const kontrakSelect = document.getElementById('spkTarikKontrak');
            if (!kontrakSelect) return;
            if (kontrakSelect.dataset.changeBound === '1') return;
            kontrakSelect.dataset.changeBound = '1';

            kontrakSelect.addEventListener('change', function() {
                const contractId = this.value;
                document.getElementById('spkTarikContractId').value = contractId || '';
                if (contractId) {
                    loadSpkTarikUnits(contractId);
                } else {
                    document.getElementById('spkTarikUnitList').innerHTML = '<div class="text-muted small">Pilih kontrak untuk memuat unit...</div>';
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
        
        document.getElementById('diModal').addEventListener('shown.bs.modal', function () {
            bindSpkDiFormValidationOnce();
            setupSpkWorkflowDropdowns();
            resetSpkWorkflowHelpTexts();
            loadSpkJenisPerintahOptions();
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
                    <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal', 'color' => 'secondary']) ?>
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
                    if (window.OptimaNotify && typeof OptimaNotify.error === 'function') {
                        OptimaNotify.error('Session expired. Please login again.');
                    }
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

            const formatAccessoryList = (list) => {
                const items = window.OptimaAccessory
                    ? window.OptimaAccessory.formatList(Array.isArray(list) ? list : [])
                    : (Array.isArray(list) ? list : []);
                return items.join(', ');
            };
            
            // Process accessories - prioritize kontrak_spec data
            let aksText = '-';
            if (ks && ks.aksesoris) {
                const aks = ks.aksesoris;
                if (Array.isArray(aks) && aks.length > 0) {
                    aksText = formatAccessoryList(aks);
                } else if (typeof aks === 'string' && aks.trim()) {
                    try {
                        const parsed = JSON.parse(aks);
                        if (Array.isArray(parsed) && parsed.length > 0) {
                            aksText = formatAccessoryList(parsed);
                        } else {
                            aksText = formatAccessoryList(String(aks).split(',').map(v => v.trim()).filter(Boolean)) || aks;
                        }
                    } catch(e) {
                        aksText = formatAccessoryList(String(aks).split(',').map(v => v.trim()).filter(Boolean)) || aks;
                    }
                }
            } else if (s && s.aksesoris) {
                if (Array.isArray(s.aksesoris) && s.aksesoris.length > 0) {
                    aksText = formatAccessoryList(s.aksesoris);
                } else if (typeof s.aksesoris === 'string' && s.aksesoris.trim()) {
                    try {
                        const parsed = JSON.parse(s.aksesoris);
                        if (Array.isArray(parsed) && parsed.length > 0) {
                            aksText = formatAccessoryList(parsed);
                        } else {
                            aksText = formatAccessoryList(String(s.aksesoris).split(',').map(v => v.trim()).filter(Boolean)) || s.aksesoris;
                        }
                    } catch(e) {
                        aksText = formatAccessoryList(String(s.aksesoris).split(',').map(v => v.trim()).filter(Boolean)) || s.aksesoris;
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
            window.optimaAssistNotify('SPK ID tidak ditemukan', 'error');
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
                    window.optimaAssistNotify('Gagal memuat data SPK untuk edit', 'error');
                }
            })
            .catch(error => {
                console.error('Error loading SPK for edit:', error);
                window.optimaAssistNotify('Error loading SPK data', 'error');
            });
    }
    
    // Delete SPK function with double confirmation
    function deleteSpk() {
        if (!currentSpkId) {
            window.optimaAssistNotify('SPK ID tidak ditemukan', 'error');
            return;
        }
        
        var cancelHtml = '<i class="fas fa-times me-1"></i>' + ((typeof window.lang === 'function') ? window.lang('cancel') : 'Batal');
        var confirmHtml = '<i class="fas fa-trash me-1"></i>' + ((typeof window.lang === 'function') ? window.lang('confirm_delete_btn') : 'Ya, hapus');
        if (window.OptimaConfirm && typeof OptimaConfirm.danger === 'function') {
            OptimaConfirm.danger({
                title: 'Hapus SPK?',
                text: '<p class="mb-2">Apakah Anda yakin ingin menghapus SPK ini?</p><p class="mb-0 text-danger"><strong>PERINGATAN:</strong> Tindakan ini tidak dapat dibatalkan.</p>',
                confirmText: confirmHtml,
                cancelText: cancelHtml,
                onConfirm: function () {
                    proceedDeleteSpk();
                }
            });
            return;
        }
        if (!window.confirm('Apakah Anda yakin ingin menghapus SPK ini?')) {
            return;
        }
        if (!window.confirm('PERINGATAN: Tindakan ini tidak dapat dibatalkan!\n\nApakah Anda benar-benar yakin ingin menghapus SPK ini?')) {
            return;
        }
        proceedDeleteSpk();
    }

    function proceedDeleteSpk() {
        
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
                
                window.optimaAssistNotify('SPK berhasil dihapus', 'success');
            } else {
                    const errorMsg = j.message || 'Gagal menghapus SPK';
                    window.optimaAssistNotify(errorMsg, 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting SPK:', error);
            window.optimaAssistNotify('Error deleting SPK: ' + error.message, 'error');
        });
    }
    
    // Function to populate edit form
    function populateEditForm(data) {
        document.getElementById('editSpkId').value = data.id || '';
        document.getElementById('editNomorSpk').value = data.nomor_spk || '';
        document.getElementById('editJenisSpk').value = data.jenis_spk || 'UNIT';
        document.getElementById('editHasFabrikasi').checked = (data.has_fabrikasi == 1);
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
                        window.optimaAssistNotify(successMsg, 'success');
                    } else {
                        // Show error notification
                        const errorMsg = j.message || 'Gagal memperbarui SPK';
                        window.optimaAssistNotify(errorMsg, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error updating SPK:', error);
                    window.optimaAssistNotify('Terjadi kesalahan saat memperbarui SPK: ' + error.message, 'error');
                });
            });
        }
    });
    
    // ==========================================
    // Link SPK to Contract Functions
    // ==========================================
    function showLinkContractModal(spkId, spkNumber) {
        document.getElementById('link_spk_id').value = spkId;
        document.getElementById('link_spk_number').value = spkNumber;
        
        console.log('🔍 Loading active contracts...');
        
        // Load active contracts filtered by this SPK's customer
        fetch('<?= base_url('marketing/kontrak/get-active-contracts') ?>?spk_id=' + spkId)
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
                window.optimaAssistNotify('Failed to load contracts. Please check console for details.', 'error');
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
                const successMsg = 'SPK berhasil di-link ke kontrak. ' + (data.di_count || 0) + ' Delivery Instruction diperbarui.';
                window.optimaAssistNotify(successMsg, 'success');
                bootstrap.Modal.getInstance(document.getElementById('linkContractModal')).hide();
                if (spkTable && spkTable.ajax) spkTable.ajax.reload(); // Reload DataTable
            } else {
                const errorMsg = 'Error: ' + (data.message || 'Failed to link contract');
                window.optimaAssistNotify(errorMsg, 'error');
            }
        })
        .catch(error => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            console.error('Error:', error);
            window.optimaAssistNotify('Failed to link contract. Please try again.', 'error');
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
                                <label class="form-label">Jenis Proses Install</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="editHasFabrikasi" name="has_fabrikasi" value="1">
                                    <label class="form-check-label" for="editHasFabrikasi">Ada fabrikasi custom?</label>
                                </div>
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
                        <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal', 'color' => 'secondary']) ?>
                        <?= ui_button('save', 'Save Changes', ['type' => 'submit']) ?>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
    
    <?php endif; ?>
</div>

<!-- ============================================================
     DIRECT SPK MODAL
     ============================================================ -->
<div class="modal fade" id="directSpkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fas fa-bolt me-2 text-success"></i>Buat SPK Langsung</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="directSpkForm">
                <div class="modal-body">
                    <div class="alert alert-info py-2 mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Buat SPK tanpa melalui alur Quotation. Spesifikasi unit diisi secara manual pada form ini.
                    </div>

                    <!-- ── STEP 1: Customer & Pengiriman ─────────────────────────── -->
                    <div class="card border-primary mb-3">
                        <div class="card-header bg-primary text-white py-2">
                            <h6 class="mb-0"><i class="fas fa-building me-2"></i>Step 1: Customer &amp; Info SPK</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Customer <span class="text-danger">*</span></label>
                                    <select class="form-select" id="dspkCustomerId" name="customer_id" required>
                                        <option value="">-- Pilih Customer --</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Lokasi Customer <small class="text-muted">(opsional)</small></label>
                                    <select class="form-select" id="dspkLocationId" name="customer_location_id" disabled>
                                        <option value="">-- Pilih Customer dulu --</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Kontrak / PO <small class="text-muted">(opsional)</small></label>
                                    <select class="form-select" id="dspkContractId" name="contract_id" disabled>
                                        <option value="">-- Pilih Customer dulu --</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Delivery <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="delivery_date" id="dspkDeliveryDate" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nama Spesifikasi <small class="text-muted">(opsional)</small></label>
                                    <input type="text" class="form-control" name="specification_name" id="dspkSpecName" maxlength="200"
                                        placeholder="e.g. Reach Truck 2T Electric — Gudang B">
                                    <small class="text-muted">Label singkat untuk membedakan spesifikasi ini.</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Jenis SPK</label>
                                    <select class="form-select" name="jenis_spk" id="dspkJenis">
                                        <option value="UNIT" selected>UNIT</option>
                                        <option value="ATTACHMENT">ATTACHMENT</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Jumlah Unit <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="jumlah_unit" id="dspkJumlahUnit" min="1" value="1" required>
                                </div>
                                <!-- has_fabrikasi -->
                                <div class="col-12">
                                    <div class="form-check form-switch mt-1">
                                        <input class="form-check-input" type="checkbox" role="switch" id="dspkHasFabrikasi" name="has_fabrikasi" value="1">
                                        <label class="form-check-label fw-semibold" for="dspkHasFabrikasi">Ada fabrikasi custom (non-standard)?</label>
                                    </div>
                                    <div class="form-text">
                                        <strong>OFF</strong> — Pasang attach/fork standar (alur: Install → Painting → PDI)&nbsp;&nbsp;
                                        <strong>ON</strong> — Perlu dibuat/dimodifikasi custom, mis. welding, ukuran khusus (alur: Fabrikasi → Painting → PDI)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── STEP 2: Spesifikasi Unit (free text, sama dgn quotation) ── -->
                    <div class="card border-success mb-3">
                        <div class="card-header bg-success text-white py-2">
                            <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Step 2: Spesifikasi Unit</h6>
                        </div>
                        <div class="card-body">

                            <!-- Template Selector -->
                            <div class="card border-0 bg-light mb-3">
                                <div class="card-body py-2 px-3">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <i class="fas fa-layer-group text-success"></i>
                                        <span class="fw-semibold text-success small">Gunakan Template:</span>
                                        <select id="dspkTemplateSelect" class="form-select form-select-sm flex-grow-1" style="max-width:280px">
                                            <option value="">-- Pilih template spesifikasi --</option>
                                        </select>
                                        <button type="button" class="btn btn-sm btn-success" id="dspkApplyTemplateBtn" disabled>
                                            <i class="fas fa-magic me-1"></i>Terapkan
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-light border mb-3 py-2">
                                <i class="fas fa-info-circle me-1 text-primary"></i>
                                Isi field utama yang diminta customer. Untuk baterai, charger, roda, atau detail lain &mdash; tuliskan di <strong>Catatan</strong> di bawah.
                            </div>

                            <div class="row g-3">
                                <!-- Free text specs (sama persis dgn quotation) -->
                                <div class="col-md-4">
                                    <label class="form-label">Departemen <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="departemen_text" id="dspkDepartemenText" required autocomplete="off"
                                        placeholder="e.g. Electric, IC Diesel">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tipe Unit <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="tipe_unit_text" id="dspkTipeUnitText" required autocomplete="off"
                                        placeholder="e.g. Counterbalance, Reach Truck">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Kapasitas <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="kapasitas_text" id="dspkKapasitasText" required autocomplete="off"
                                        placeholder="e.g. 1.5 Ton, 3 Ton">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Merk Unit <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="merk_unit_text" id="dspkMerkUnitText" required autocomplete="off"
                                        placeholder="e.g. Toyota, Komatsu, Nichiyu">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Kondisi Unit <span class="text-danger">*</span></label>
                                    <select class="form-select" name="unit_condition" id="dspkUnitCondition" required>
                                        <option value="">-- Pilih Kondisi --</option>
                                        <option value="NEW">Baru (New)</option>
                                        <option value="USED">Bekas (Used)</option>
                                    </select>
                                </div>

                                <!-- ── Harga ─────────────────────────────────────────── -->
                                <div class="col-12 mt-1"><hr class="my-1"><h6 class="mb-0 text-primary"><i class="fas fa-tag me-1"></i>Harga</h6></div>
                                <div class="col-md-6">
                                    <label class="form-label">Harga Sewa Bulanan <small class="text-muted">(opsional)</small></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control" name="monthly_price" id="dspkMonthlyPrice" inputmode="numeric" autocomplete="off"
                                            placeholder="0">
                                    </div>
                                    <small class="text-muted">Per unit per bulan</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Harga Sewa Harian <small class="text-muted">(opsional)</small></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control" name="daily_price" id="dspkDailyPrice" inputmode="numeric" autocomplete="off"
                                            placeholder="0">
                                    </div>
                                    <small class="text-muted">Per unit per hari</small>
                                </div>

                                <!-- ── Spare Units ─────────────────────────────────── -->
                                <div class="col-12 mt-1"><hr class="my-1"></div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="dspkIncludeSpare" value="1">
                                        <label class="form-check-label fw-bold text-warning" for="dspkIncludeSpare">
                                            <i class="fas fa-box-open me-1"></i>Sertakan Unit Cadangan (Spare)
                                        </label>
                                        <small class="text-muted d-block ms-4">Tambahkan unit backup untuk kontinuitas operasional (tidak ditagih, untuk jaga-jaga downtime)</small>
                                    </div>
                                </div>
                                <div class="col-12" id="dspkSpareContainer" style="display:none;">
                                    <div class="alert alert-warning border-warning bg-light py-2">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-md-3">
                                                <label class="form-label mb-0 fw-bold"><i class="fas fa-box me-1"></i>Jumlah Spare</label>
                                                <input type="number" class="form-control" name="spare_quantity" id="dspkSpareQty" min="0" value="0">
                                                <small class="text-muted">Unit cadangan</small>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                                    <div>
                                                        <strong>Unit Ditagih:</strong>
                                                        <span id="dspkBillableDisplay" class="badge badge-soft-green ms-1">0</span>
                                                    </div>
                                                    <div>
                                                        <strong>Unit Spare:</strong>
                                                        <span id="dspkSpareDisplay" class="badge badge-soft-yellow ms-1">0</span>
                                                    </div>
                                                    <div>
                                                        <strong>Total Dikirim:</strong>
                                                        <span id="dspkTotalDisplay" class="badge badge-soft-blue ms-1">0</span>
                                                    </div>
                                                </div>
                                                <small class="text-muted d-block mt-1"><i class="fas fa-info-circle me-1"></i>Perhitungan tagihan: Unit Ditagih × Harga Bulanan</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ── Include Operator ───────────────────────────── -->
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="include_operator" id="dspkIncludeOperator" value="1">
                                        <label class="form-check-label fw-bold text-info" for="dspkIncludeOperator">
                                            <i class="fas fa-user-tie me-1"></i>Termasuk Operator
                                        </label>
                                        <small class="text-muted d-block ms-4">Kontrak termasuk penyediaan operator untuk unit ini</small>
                                    </div>
                                </div>
                                <div class="col-12" id="dspkOperatorContainer" style="display:none;">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Jumlah Operator <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="operator_quantity" id="dspkOperatorQty" min="1" value="1">
                                            <small class="text-muted">Per unit berapa operator</small>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label">Sumber Tarif Operator</label>
                                            <div class="form-control bg-light">Otomatis dari Customer Location saat pembuatan DI</div>
                                            <small class="text-muted">Harga operator tidak diisi di SPK.</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- ── Detail Spesifikasi Teknis ───────────────────── -->
                                <div class="col-12 mt-1">
                                    <hr class="my-1">
                                    <h6 class="mb-0 text-primary"><i class="fas fa-tools me-1"></i>Detail Teknis</h6>
                                    <p class="small text-muted mb-2">Spesifikasi teknis unit yang akan dikirimkan.</p>
                                </div>

                                <!-- Fork / Attachment radio -->
                                <div class="col-12">
                                    <label class="form-label">Fork / Attachment</label>
                                    <div class="btn-group w-100 flex-wrap" role="group">
                                        <input type="radio" class="btn-check" name="fork_attach_type" id="dspkOptFork" value="fork" checked autocomplete="off">
                                        <label class="btn btn-outline-primary btn-sm" for="dspkOptFork"><i class="fas fa-tools me-1"></i>Fork / Garpu</label>
                                        <input type="radio" class="btn-check" name="fork_attach_type" id="dspkOptAttachment" value="attachment" autocomplete="off">
                                        <label class="btn btn-outline-success btn-sm" for="dspkOptAttachment"><i class="fas fa-paperclip me-1"></i>Attachment</label>
                                    </div>
                                    <small class="text-muted d-block mt-1">Pilih jenis Fork atau Attachment yang digunakan pada unit ini.</small>
                                </div>

                                <div class="col-md-6" id="dspkTextForkWrap" style="display:none;">
                                    <label for="dspkDetailFork" class="form-label">Detail Fork</label>
                                    <input type="text" class="form-control" id="dspkDetailFork" maxlength="500" autocomplete="off"
                                        placeholder="e.g. Standard Fork 1150mm, Class IIA">
                                </div>
                                <div class="col-md-6" id="dspkTextAttachWrap" style="display:none;">
                                    <label for="dspkDetailAttachment" class="form-label">Detail Attachment</label>
                                    <input type="text" class="form-control" id="dspkDetailAttachment" maxlength="500" autocomplete="off"
                                        placeholder="e.g. Rotator side-shift, kapasitas 2T">
                                </div>
                                <div class="col-md-6">
                                    <label for="dspkDetailMast" class="form-label">Mast (Tinggi Angkat)</label>
                                    <input type="text" class="form-control" id="dspkDetailMast" maxlength="500" autocomplete="off"
                                        placeholder="e.g. Triplex FFL 5000mm">
                                </div>
                                <div class="col-md-6">
                                    <label for="dspkDetailBan" class="form-label">Ban (Tire)</label>
                                    <input type="text" class="form-control" id="dspkDetailBan" maxlength="500" autocomplete="off"
                                        placeholder="e.g. Solid, 200/50-10">
                                </div>
                                <div class="col-md-6">
                                    <label for="dspkDetailValve" class="form-label">Valve</label>
                                    <input type="text" class="form-control" id="dspkDetailValve" maxlength="500" autocomplete="off"
                                        placeholder="e.g. 2-way, 3-way hydraulic">
                                </div>

                                <!-- ── Master IDs (opsional, collapsible) ────────────── -->
                                <div class="col-12">
                                    <details class="border rounded p-3 mt-1 bg-light" id="dspkMasterDetails">
                                        <summary class="fw-semibold text-muted small">Hubungkan ke Master Data (Opsional)</summary>
                                        <p class="small text-muted mt-2 mb-2">Pilih master ID jika ingin menghubungkan ke data master forklift. Tidak wajib — cukup isi teks di atas.</p>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label small">Departemen Master</label>
                                                <select class="form-select form-select-sm" name="departemen_id" id="dspkDepartemen">
                                                    <option value="">-- Pilih Departemen --</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small">Tipe Unit Master</label>
                                                <select class="form-select form-select-sm" name="tipe_unit_id" id="dspkTipeUnit">
                                                    <option value="">-- Pilih Departemen dulu --</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small">Kapasitas Master</label>
                                                <select class="form-select form-select-sm" name="kapasitas_id" id="dspkKapasitas">
                                                    <option value="">-- Pilih Kapasitas --</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small">Merk Master</label>
                                                <select class="form-select form-select-sm" name="brand_id" id="dspkBrand" disabled>
                                                    <option value="">-- Pilih Departemen dulu --</option>
                                                </select>
                                            </div>
                                            <div class="col-12" id="dspkForkMasterWrap">
                                                <label class="form-label small">Fork Master</label>
                                                <select class="form-select form-select-sm" name="fork_id" id="dspkForkId">
                                                    <option value="">-- Pilih Jenis Fork --</option>
                                                </select>
                                            </div>
                                            <div class="col-12" id="dspkAttachMasterWrap" style="display:none;">
                                                <label class="form-label small">Attachment Master</label>
                                                <select class="form-select form-select-sm" name="attachment_id" id="dspkAttachmentId">
                                                    <option value="">-- Pilih Jenis Attachment --</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small">Mast Model</label>
                                                <select class="form-select form-select-sm" id="dspkMastModel">
                                                    <option value="">-- Pilih Model --</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small">Mast Tinggi</label>
                                                <select class="form-select form-select-sm" name="mast_id" id="dspkMastHeight">
                                                    <option value="">-- Pilih model dulu --</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small">Ban Master</label>
                                                <select class="form-select form-select-sm" name="ban_id" id="dspkBan">
                                                    <option value="">-- Pilih Ban --</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small">Valve Master</label>
                                                <select class="form-select form-select-sm" name="valve_id" id="dspkValve">
                                                    <option value="">-- Pilih Valve --</option>
                                                </select>
                                            </div>
                                        </div>
                                    </details>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Accessories -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0"><i class="fas fa-list-check me-1 text-primary"></i>Aksesoris Unit</label>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="dspkBtnAksStandar">
                                <i class="fas fa-check-double me-1"></i>Set Aksesori Standar
                            </button>
                        </div>
                        <div id="dspkAccGrid"></div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label class="form-label">Catatan / Custom Requirements</label>
                        <textarea class="form-control" id="dspkNotes" rows="3"
                            placeholder="Tambahkan baterai, charger, roda, atau catatan lain di sini sebagai teks bebas."></textarea>
                        <small class="text-muted"><i class="fas fa-lightbulb text-warning me-1"></i>Detail baterai, charger, roda, atau catatan lain sebaiknya dituliskan di sini.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="dspkSubmitBtn">
                        <i class="fas fa-bolt me-1"></i>Buat SPK
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
/* ================================================================
   DIRECT SPK MODAL — JavaScript
   Aligned with quotation spec form fields + spare / operator sections
   ================================================================ */
(function () {
    'use strict';

    const BASE   = '<?= base_url() ?>';
    const SPEC   = BASE + 'marketing/spk/spec-options';
    const TPL    = BASE + 'marketing/quotations/spec-templates';
    const CUST   = BASE + 'marketing/kontrak/customers-dropdown';
    const LOCS   = BASE + 'marketing/customer-management/getCustomerLocations/';
    const CTRCTS = BASE + 'marketing/customer-management/getCustomerContracts/';
    const FORKS  = BASE + 'marketing/forks';
    const TIPE   = BASE + 'marketing/customer-management/getTipeUnit';

    const TECH_START = '[OPTIMA_SPEC_TECH]';
    const TECH_END   = '[/OPTIMA_SPEC_TECH]';

    let dspkReady   = false;
    let allTipeUnit = [];

    // ── OPTIMA_SPEC_TECH helpers ───────────────────────────────────
    function extractDspkSpecTech(notesRaw) {
        const raw = notesRaw || '';
        const s = raw.indexOf(TECH_START);
        const e = raw.indexOf(TECH_END);
        if (s === -1 || e === -1 || e < s) { return { userNotes: raw.trim(), detail: {} }; }
        const before = raw.slice(0, s).replace(/\s*$/, '');
        const after  = raw.slice(e + TECH_END.length).replace(/^\s*/, '');
        const userNotes = [before, after].filter(Boolean).join('\n\n').trim();
        const inner  = raw.slice(s + TECH_START.length, e).trim();
        const detail = {};
        inner.split('\n').forEach(function (line) {
            const m = /^([a-z_]+):\s*(.*)$/.exec(line.trim());
            if (m) { detail[m[1]] = m[2]; }
        });
        return { userNotes: userNotes, detail: detail };
    }

    function buildDspkSpecTechBlock() {
        const fat = (document.querySelector('#directSpkModal input[name="fork_attach_type"]:checked') || {}).value || 'fork';
        const lines = [];
        const push = function (key, val) { if (val && val.trim()) { lines.push(key + ': ' + val.trim().replace(/\n/g, ' ')); } };
        push(fat === 'fork' ? 'fork' : 'attachment',
             fat === 'fork'
                 ? document.getElementById('dspkDetailFork').value
                 : document.getElementById('dspkDetailAttachment').value);
        push('mast',  document.getElementById('dspkDetailMast').value);
        push('ban',   document.getElementById('dspkDetailBan').value);
        push('valve', document.getElementById('dspkDetailValve').value);
        if (!lines.length) { return ''; }
        return TECH_START + '\n' + lines.join('\n') + '\n' + TECH_END;
    }

    function mergeDspkNotes() {
        const userNotes = (document.getElementById('dspkNotes').value || '').trim();
        const block     = buildDspkSpecTechBlock();
        if (!block)     { return userNotes; }
        if (userNotes)  { return userNotes + '\n\n' + block; }
        return block;
    }

    // ── generic helpers ────────────────────────────────────────────
    function fillSelect(selId, rows, placeholder) {
        const sel = document.getElementById(selId);
        if (!sel) { return; }
        const prev = sel.value;
        sel.innerHTML = '<option value="">' + (placeholder || '-- Pilih --') + '</option>';
        (rows || []).forEach(function (r) {
            const o = document.createElement('option');
            o.value = r.id;
            o.textContent = r.name;
            sel.appendChild(o);
        });
        if (prev) { sel.value = prev; }
    }

    function apiGet(url) {
        return fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function (r) { return r.json(); });
    }

    // ── load static dropdowns once (master data for optional section) ──
    function loadAllStaticDropdowns() {
        apiGet(SPEC + '?type=departemen').then(function (j) { fillSelect('dspkDepartemen', j.data, '-- Pilih Departemen --'); });
        apiGet(SPEC + '?type=kapasitas').then(function (j) {
            const sorted = (j.data || []).slice().sort(function (a, b) { return parseFloat(a.name) - parseFloat(b.name); });
            fillSelect('dspkKapasitas', sorted, '-- Pilih Kapasitas --');
        });
        apiGet(SPEC + '?type=mast_model').then(function (j) { fillSelect('dspkMastModel', j.data, '-- Pilih Model Mast --'); });
        apiGet(SPEC + '?type=ban').then(function (j)        { fillSelect('dspkBan', j.data, '-- Pilih Ban --'); });
        apiGet(SPEC + '?type=valve').then(function (j)      { fillSelect('dspkValve', j.data, '-- Pilih Valve --'); });
        apiGet(SPEC + '?type=attachment_tipe').then(function (j) { fillSelect('dspkAttachmentId', j.data, '-- Pilih Attachment --'); });
        apiGet(FORKS).then(function (j) { fillSelect('dspkForkId', j.data, '-- Pilih Fork --'); });
        apiGet(TIPE).then(function (j) { allTipeUnit = j.data || []; });
        loadDspkTemplates();
    }

    function loadDspkTemplates() {
        apiGet(TPL).then(function (j) {
            const sel = document.getElementById('dspkTemplateSelect');
            if (!sel) { return; }
            const prev = sel.value;
            sel.innerHTML = '<option value="">-- Pilih template spesifikasi --</option>';
            (j.data || []).forEach(function (t) {
                const lbl = t.template_name +
                    (t.nama_tipe_unit ? ' (' + t.nama_tipe_unit + (t.jenis_tipe_unit ? ' ' + t.jenis_tipe_unit : '') + ')' : '');
                const o = document.createElement('option');
                o.value = t.id;
                o.textContent = lbl;
                sel.appendChild(o);
            });
            if (prev) { sel.value = prev; }
        });
    }

    // ── customers ─────────────────────────────────────────────────
    // Use Select2 AJAX so all customers are searchable (not limited to first 50)
    function loadCustomers() {
        if (!window.jQuery || !$.fn.select2) { return; }
        const $sel = $('#dspkCustomerId');
        if ($sel.hasClass('select2-hidden-accessible')) { return; }
        $sel.select2({
            dropdownParent: $('#directSpkModal'),
            placeholder: 'Cari / pilih customer...',
            allowClear: true,
            width: '100%',
            ajax: {
                url: CUST,
                type: 'GET',
                dataType: 'json',
                delay: 250,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                data: function (params) {
                    return { q: params.term || '' };
                },
                processResults: function (data) {
                    if (!data || !data.success || !data.data) { return { results: [] }; }
                    return {
                        results: data.data.map(function (c) {
                            const code = c.customer_code ? ' (' + c.customer_code + ')' : '';
                            return { id: c.id, text: (c.customer_name || '') + code };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 0
        });
    }

    function loadLocations(cid) {
        const sel = document.getElementById('dspkLocationId');
        sel.disabled = true;
        sel.innerHTML = '<option value="">Loading...</option>';
        apiGet(LOCS + cid).then(function (j) {
            fillSelect('dspkLocationId', (j.data || []).map(function (l) {
                return { id: l.id, name: l.location_name + (l.city ? ' \u2014 ' + l.city : '') };
            }), '-- Pilih Lokasi --');
            sel.disabled = false;
        });
    }

    function loadContracts(cid) {
        const sel = document.getElementById('dspkContractId');
        sel.disabled = true;
        sel.innerHTML = '<option value="">Loading...</option>';
        apiGet(CTRCTS + cid).then(function (j) {
            const rows = (j.data || []).map(function (c) {
                return { id: c.id, name: (c.no_kontrak || 'ID:' + c.id) + (c.customer_po_number ? ' / ' + c.customer_po_number : '') };
            });
            fillSelect('dspkContractId', rows, '-- Pilih Kontrak (opsional) --');
            sel.disabled = rows.length === 0;
        });
    }

    // ── tipe unit cascade ─────────────────────────────────────────
    function updateTipeUnit(deptId) {
        const sel = document.getElementById('dspkTipeUnit');
        sel.innerHTML = '<option value="">-- Pilih Tipe Unit --</option>';
        if (!deptId || !allTipeUnit.length) { return; }
        const filtered = allTipeUnit.filter(function (u) { return String(u.id_departemen) === String(deptId); });
        const unique   = Array.from(new Set(filtered.map(function (u) { return u.jenis; }))).sort();
        unique.forEach(function (jenis) {
            const u = filtered.find(function (u) { return u.jenis === jenis; });
            if (u) {
                const o = document.createElement('option');
                o.value = u.id_tipe_unit;
                o.textContent = jenis;
                sel.appendChild(o);
            }
        });
    }

    function loadBrands(deptId) {
        const sel = document.getElementById('dspkBrand');
        if (!deptId) { sel.innerHTML = '<option value="">-- Pilih Departemen dulu --</option>'; sel.disabled = true; return; }
        sel.disabled = false;
        sel.innerHTML = '<option value="">Loading...</option>';
        apiGet(SPEC + '?type=merk_unit&departemen_id=' + encodeURIComponent(deptId))
            .then(function (j) { fillSelect('dspkBrand', j.data, '-- Pilih Merk --'); });
    }

    function loadMastHeights(modelVal, selectAfter) {
        const sel = document.getElementById('dspkMastHeight');
        if (!modelVal) { sel.innerHTML = '<option value="">-- Pilih model dulu --</option>'; return; }
        sel.innerHTML = '<option value="">Loading...</option>';
        apiGet(SPEC + '?type=mast_height&model=' + encodeURIComponent(modelVal)).then(function (j) {
            fillSelect('dspkMastHeight', j.data, '-- Pilih Tinggi Mast --');
            if (selectAfter) { sel.value = selectAfter; }
        });
    }

    // ── apply fork/attach toggle UI ───────────────────────────────
    function applyForkAttachUI(val) {
        const isFork   = val === 'fork';
        const isAttach = val === 'attachment';
        document.getElementById('dspkTextForkWrap').style.display   = isFork   ? '' : 'none';
        document.getElementById('dspkTextAttachWrap').style.display = isAttach ? '' : 'none';
        document.getElementById('dspkForkMasterWrap').style.display   = isFork   ? '' : 'none';
        document.getElementById('dspkAttachMasterWrap').style.display = isAttach ? '' : 'none';
        if (!isFork)   { document.getElementById('dspkForkId').value = ''; document.getElementById('dspkDetailFork').value = ''; }
        if (!isAttach) { document.getElementById('dspkAttachmentId').value = ''; document.getElementById('dspkDetailAttachment').value = ''; }
    }

    // ── Spare units display update ────────────────────────────────
    function updateSpareDisplay() {
        const qty    = parseInt(document.getElementById('dspkJumlahUnit').value) || 1;
        const spare  = parseInt(document.getElementById('dspkSpareQty').value)   || 0;
        const total  = qty + spare;
        document.getElementById('dspkBillableDisplay').textContent = qty;
        document.getElementById('dspkSpareDisplay').textContent    = spare;
        document.getElementById('dspkTotalDisplay').textContent    = total;
    }

    // ── apply template ────────────────────────────────────────────
    function applyDspkTemplate(id) {
        if (!id) { return; }
        const btn = document.getElementById('dspkApplyTemplateBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memuat...';

        apiGet(TPL + '/' + id).then(function (res) {
            if (!res.success) {
                window.optimaAssistNotify('Gagal memuat template.', 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-magic me-1"></i>Terapkan';
                return;
            }
            const t = res.data;

            // 1. Master ID dropdowns (open details if needed)
            const deptSel = document.getElementById('dspkDepartemen');
            deptSel.value = t.departemen_id || '';
            deptSel.dispatchEvent(new Event('change'));
            setTimeout(function () {
                document.getElementById('dspkTipeUnit').value = t.tipe_unit_id || '';
                document.getElementById('dspkBrand').value    = t.brand_id     || '';
            }, 450);
            document.getElementById('dspkKapasitas').value = t.kapasitas_id || '';

            // 2. Fork / Attachment toggle
            const fat = t.fork_id ? 'fork' : (t.attachment_id ? 'attachment' : 'fork');
            const radio = document.querySelector('#directSpkModal input[name="fork_attach_type"][value="' + fat + '"]');
            if (radio) { radio.checked = true; }
            applyForkAttachUI(fat);
            setTimeout(function () {
                if (t.fork_id)       { document.getElementById('dspkForkId').value = t.fork_id; }
                if (t.attachment_id) { document.getElementById('dspkAttachmentId').value = t.attachment_id; }
            }, 200);

            // 3. Mast 2-level
            if (t.mast_id) {
                const mastModelSel = document.getElementById('dspkMastModel');
                let matched = false;
                if (t.mast_name) {
                    Array.from(mastModelSel.options).forEach(function (o) {
                        if (o.text === t.mast_name) { mastModelSel.value = o.value; matched = true; }
                    });
                }
                if (matched || mastModelSel.value) { loadMastHeights(mastModelSel.value, t.mast_id); }
            }
            document.getElementById('dspkBan').value   = t.ban_id   || '';
            document.getElementById('dspkValve').value = t.valve_id || '';

            // 4. Free text fields from template name hints
            if (t.nama_departemen) { document.getElementById('dspkDepartemenText').value = t.nama_departemen; }
            if (t.nama_tipe_unit)  { document.getElementById('dspkTipeUnitText').value   = t.nama_tipe_unit + (t.jenis_tipe_unit ? ' ' + t.jenis_tipe_unit : ''); }
            if (t.kapasitas)       { document.getElementById('dspkKapasitasText').value  = t.kapasitas; }
            if (t.nama_merk_unit || t.merk_unit) { document.getElementById('dspkMerkUnitText').value = t.nama_merk_unit || t.merk_unit || ''; }

            // 5. Parse OPTIMA_SPEC_TECH from template notes
            if (t.notes) {
                const parsed = extractDspkSpecTech(t.notes);
                document.getElementById('dspkNotes').value = parsed.userNotes || '';
                document.getElementById('dspkDetailMast').value  = parsed.detail.mast  || '';
                document.getElementById('dspkDetailBan').value   = parsed.detail.ban   || '';
                document.getElementById('dspkDetailValve').value = parsed.detail.valve || '';
                if (fat === 'fork')       { document.getElementById('dspkDetailFork').value       = parsed.detail.fork       || ''; }
                if (fat === 'attachment') { document.getElementById('dspkDetailAttachment').value = parsed.detail.attachment || ''; }
            } else {
                ['dspkNotes','dspkDetailFork','dspkDetailAttachment','dspkDetailMast','dspkDetailBan','dspkDetailValve']
                    .forEach(function (elId) { document.getElementById(elId).value = ''; });
            }

            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check me-1"></i>Diterapkan';
            setTimeout(function () { btn.innerHTML = '<i class="fas fa-magic me-1"></i>Terapkan'; }, 2000);
        }).catch(function () {
            window.optimaAssistNotify('Gagal memuat detail template.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-magic me-1"></i>Terapkan';
        });
    }

    // ── number format helpers ──────────────────────────────────────
    function parseCurrencyInput(val) {
        return parseInt((val || '0').replace(/\D/g, ''), 10) || 0;
    }
    function formatCurrencyInput(el) {
        const raw = el.value.replace(/\D/g, '');
        el.value = raw ? parseInt(raw, 10).toLocaleString('id-ID') : '';
    }

    // ── bind events ────────────────────────────────────────────────
    function bindEvents() {
        // Customer → locations + contracts
        document.getElementById('dspkCustomerId').addEventListener('change', function () {
            const cid = this.value;
            if (cid) { loadLocations(cid); loadContracts(cid); }
            else {
                ['dspkLocationId', 'dspkContractId'].forEach(function (elId) {
                    const s = document.getElementById(elId);
                    s.innerHTML = '<option value="">-- Pilih Customer dulu --</option>';
                    s.disabled = true;
                });
            }
        });
        if (window.jQuery && $.fn.select2) {
            $('#dspkCustomerId').on('select2:select select2:unselect', function () { this.dispatchEvent(new Event('change')); });
        }

        // Departemen master → tipe unit + brand (optional section)
        document.getElementById('dspkDepartemen').addEventListener('change', function () {
            updateTipeUnit(this.value);
            loadBrands(this.value);
        });

        // Mast model → heights
        document.getElementById('dspkMastModel').addEventListener('change', function () {
            loadMastHeights(this.value, null);
        });

        // Fork/Attachment toggle
        document.querySelectorAll('#directSpkModal input[name="fork_attach_type"]').forEach(function (radio) {
            radio.addEventListener('change', function () { applyForkAttachUI(this.value); });
        });

        // Template button
        document.getElementById('dspkTemplateSelect').addEventListener('change', function () {
            document.getElementById('dspkApplyTemplateBtn').disabled = !this.value;
        });
        document.getElementById('dspkApplyTemplateBtn').addEventListener('click', function () {
            applyDspkTemplate(document.getElementById('dspkTemplateSelect').value);
        });

        // Spare units toggle
        document.getElementById('dspkIncludeSpare').addEventListener('change', function () {
            document.getElementById('dspkSpareContainer').style.display = this.checked ? '' : 'none';
            if (!this.checked) { document.getElementById('dspkSpareQty').value = '0'; }
            updateSpareDisplay();
        });
        document.getElementById('dspkSpareQty').addEventListener('input', updateSpareDisplay);
        document.getElementById('dspkJumlahUnit').addEventListener('input', updateSpareDisplay);

        // Include operator toggle
        document.getElementById('dspkIncludeOperator').addEventListener('change', function () {
            document.getElementById('dspkOperatorContainer').style.display = this.checked ? '' : 'none';
            if (!this.checked) { document.getElementById('dspkOperatorQty').value = '1'; }
        });

        // Currency formatting
        ['dspkMonthlyPrice','dspkDailyPrice'].forEach(function (elId) {
            const el = document.getElementById(elId);
            if (el) {
                el.addEventListener('input', function () { formatCurrencyInput(this); });
                el.addEventListener('focus', function () { this.select(); });
            }
        });

        // Submit
        document.getElementById('directSpkForm').addEventListener('submit', function (e) {
            e.preventDefault();
            submitDirectSpk();
        });
    }

    // ── form submit ───────────────────────────────────────────────
    function submitDirectSpk() {
        const btn = document.getElementById('dspkSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';

        const form = document.getElementById('directSpkForm');
        const fd   = new FormData(form);
        const payload = {};
        fd.forEach(function (v, k) { payload[k] = v; });

        // Build merged notes (userNotes + [OPTIMA_SPEC_TECH] block)
        payload.notes = mergeDspkNotes();

        // Numeric prices (strip thousand separators)
        payload.monthly_price = parseCurrencyInput(payload.monthly_price || '0');
        payload.daily_price   = parseCurrencyInput(payload.daily_price   || '0');

        // Spare units
        payload.is_spare_unit   = document.getElementById('dspkIncludeSpare').checked ? 1 : 0;
        payload.spare_quantity  = parseInt(document.getElementById('dspkSpareQty').value) || 0;

        // Operator
        payload.include_operator  = document.getElementById('dspkIncludeOperator').checked ? 1 : 0;
        payload.operator_quantity = parseInt(document.getElementById('dspkOperatorQty').value) || 0;

        // Accessories checkboxes (not captured by FormData since name has [])
        const aksesoris = [];
        document.querySelectorAll('#dspkAccGrid input[name="dspk_aksesoris[]"]:checked').forEach(function (cb) {
            aksesoris.push(cb.value);
        });
        payload.aksesoris = aksesoris;
        delete payload['dspk_aksesoris[]']; // remove FormData artifact if any

        // CSRF
        payload[window.csrfTokenName] = window.csrfToken || window.csrfTokenValue || '';

        fetch(BASE + 'marketing/spk/createDirect', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': window.csrfToken || '' },
            body: JSON.stringify(payload)
        })
        .then(function (r) { return r.json(); })
        .then(function (json) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-bolt me-1"></i>Buat SPK';
            if (json.csrf_hash) { window.csrfToken = json.csrf_hash; window.csrfTokenValue = json.csrf_hash; }
            if (json.success) {
                window.optimaAssistNotify(json.message || 'SPK berhasil dibuat.', 'success');
                bootstrap.Modal.getInstance(document.getElementById('directSpkModal'))?.hide();
                resetDirectSpkForm(form);
                if (typeof refreshSPKTable === 'function') { refreshSPKTable(); }
            } else {
                window.optimaAssistNotify(json.message || 'Gagal membuat SPK', 'error');
            }
        })
        .catch(function (err) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-bolt me-1"></i>Buat SPK';
            console.error('createDirectSPK error:', err);
            window.optimaAssistNotify('Terjadi kesalahan jaringan', 'error');
        });
    }

    function resetDirectSpkForm(form) {
        form.reset();
        ['dspkDetailFork','dspkDetailAttachment','dspkDetailMast','dspkDetailBan','dspkDetailValve','dspkNotes',
         'dspkDepartemenText','dspkTipeUnitText','dspkKapasitasText','dspkMerkUnitText']
            .forEach(function (elId) { const el = document.getElementById(elId); if (el) el.value = ''; });
        ['dspkLocationId','dspkContractId'].forEach(function (elId) {
            const s = document.getElementById(elId);
            s.disabled = true;
            s.innerHTML = '<option value="">-- Pilih Customer dulu --</option>';
        });
        document.getElementById('dspkBrand').disabled = true;
        document.getElementById('dspkSpareContainer').style.display    = 'none';
        document.getElementById('dspkOperatorContainer').style.display = 'none';
        const forkRadio = document.getElementById('dspkOptFork');
        if (forkRadio) { forkRadio.checked = true; applyForkAttachUI('fork'); }
        updateSpareDisplay();
        // Clear accessories
        document.querySelectorAll('#dspkAccGrid input[name="dspk_aksesoris[]"]').forEach(function (cb) { cb.checked = false; });
    }

    // ── init on first modal open ──────────────────────────────────
    document.getElementById('directSpkModal').addEventListener('show.bs.modal', function () {
        if (dspkReady) { return; }
        dspkReady = true;
        loadAllStaticDropdowns();
        loadCustomers();
        bindEvents();
        // Default state: fork selected, spare hidden
        applyForkAttachUI('fork');
        updateSpareDisplay();
        // Accessories grid — uses global OptimaAccessory if available
        if (window.OptimaAccessory) {
            OptimaAccessory.renderGroupSections('#dspkAccGrid',
                ['quotationStandard', 'quotationExtra'], {
                name: 'dspk_aksesoris[]',
                idPrefix: 'dspk_acc_',
                columnsClass: 'col-md-4 col-sm-6',
                style: 'inline'
            });
            document.getElementById('dspkBtnAksStandar').addEventListener('click', function () {
                OptimaAccessory.getGroupItemCodes('quotationStandard').forEach(function (code) {
                    const cb = document.querySelector('#dspkAccGrid input[name="dspk_aksesoris[]"][value="' + code + '"]');
                    if (cb) cb.checked = true;
                });
            });
        }
    });

})();
</script>

<?= $this->endSection() ?>

<!-- svcUnitDetailBlock: see optima-pro.css SPK MARKETING PAGE section -->
