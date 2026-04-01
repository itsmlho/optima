<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<?php $isEn = service('request')->getLocale() === 'en'; ?>

<!-- Force browser to reload (Cache Buster) -->
<script>
// Force modal sizes on page load
window.addEventListener('DOMContentLoaded', function() {
    // Add timestamp to force cache invalidation
    console.log('Ã°Å¸â€â€ž Page loaded at:', new Date().toLocaleTimeString(), '- Modals set to 98vw');
});
</script>

<!-- Date Range Filter - Top Right -->
<div class="row mt-3">
    <div class="col-md-12 text-end">
        <div class="d-inline-block">
            <?= view('components/date_range_filter', [
                'id' => 'quotationDateRangePicker'
            ]) ?>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-primary-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-file-text stat-icon text-primary"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-total-quotations">0</div>
                    <div class="text-muted"><?= lang('Marketing.total_quotations') ?></div>
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
                    <div class="stat-value" id="stat-pending">0</div>
                    <div class="text-muted"><?= lang('Marketing.pending') ?></div>
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
                    <div class="stat-value" id="stat-approved">0</div>
                    <div class="text-muted"><?= lang('Common.approved') ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-danger-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-x-circle stat-icon text-danger"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-rejected">0</div>
                    <div class="text-muted"><?= lang('Common.rejected') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quotations Table Card -->
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="card-title mb-0">
                <i class="bi bi-file-text me-2 text-primary"></i>
                <?= lang('Marketing.prospect_quotations') ?>
            </h5>
            <p class="text-muted small mb-0"><?= lang('Marketing.manage_prospects') ?></p>
        </div>
        <div>
            <?= ui_button('export', lang('App.export'), [
                'href' => base_url('marketing/quotations/export'),
                'color' => 'outline-success',
                'size' => 'sm',
                'class' => 'me-2'
            ]) ?>
            <?= ui_button('add', lang('Marketing.add_prospect'), [
                'onclick' => 'openCreateProspectModal()',
                'size' => 'sm'
            ]) ?>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="quotationsTable" class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th><?= lang('Marketing.no') ?></th>
                        <th><?= lang('Marketing.quotation_number') ?></th>
                        <th><?= lang('Marketing.prospect_name') ?></th>
                        <th><?= lang('Marketing.quotation_title') ?></th>
                        <th><?= lang('Marketing.amount') ?></th>
                        <th><?= lang('Marketing.stage') ?></th>
                        <th><?= lang('Marketing.date') ?></th>
                        <th><?= lang('Marketing.actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated by DataTables AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Prospect Modal -->
<div class="modal fade modal-wide" id="createProspectModal" tabindex="-1" aria-labelledby="createProspectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-muted">
                <h5 class="modal-title fw-600" id="createProspectModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Create New Prospect
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createProspectForm" novalidate>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Step 1:</strong> Create prospect with basic company information. 
                        You can add quotation specifications in the next step.
                    </div>
                    
                    <h6 class="mb-3"><i class="fas fa-building me-2"></i>Company Information</h6>
                    
                    <!-- Smart Customer Search -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="linkExistingCustomer">
                            <label class="form-check-label" for="linkExistingCustomer">
                                <i class="fas fa-link me-1"></i>Existing Customer
                            </label>
                        </div>
                    </div>
                    
                    <!-- Customer Search Section -->
                    <div id="customerSearchSection" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-search me-2"></i><?= lang('Marketing.search_link_existing_customer') ?>
                        </div>
                        <div class="mb-3">
                            <label for="customerSearchInput" class="form-label"><?= lang('Marketing.search_customer') ?></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="customerSearchInput" placeholder="<?= lang('Marketing.type_customer_name') ?>">
                                <button type="button" class="btn btn-outline-secondary" id="searchCustomerBtn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div id="customerSearchResults"></div>
                        <input type="hidden" id="selectedCustomerId" name="existing_customer_id">
                    </div>
                    
                    <!-- New Customer Section -->
                    <div id="newCustomerSection">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="prospectCompanyName" class="form-label"><?= lang('Marketing.company_name') ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="prospectCompanyName" name="prospect_name" required>
                                    <small class="form-text text-muted"><?= lang('Marketing.company_name_for_new_customer') ?></small>
                                </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prospectContactPerson" class="form-label"><?= lang('Marketing.contact_person') ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="prospectContactPerson" name="prospect_contact_person" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prospectEmail" class="form-label"><?= lang('App.email') ?></label>
                                <input type="email" class="form-control" id="prospectEmail" name="prospect_email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prospectPhone" class="form-label"><?= lang('App.phone') ?></label>
                                <input type="tel" class="form-control" id="prospectPhone" name="prospect_phone">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="prospectAddress" class="form-label"><?= lang('App.address') ?></label>
                        <textarea class="form-control" id="prospectAddress" name="prospect_address" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prospectCity" class="form-label"><?= lang('App.city') ?></label>
                                <input type="text" class="form-control" id="prospectCity" name="prospect_city">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prospectProvince" class="form-label"><?= lang('App.province') ?></label>
                                <input type="text" class="form-control" id="prospectProvince" name="prospect_province">
                            </div>
                        </div>
                    </div>
                </div>
                    
                    <!-- Quotation Details Section (Always visible) -->
                    <hr>
                    <h6 class="mb-3"><i class="fas fa-file-contract me-2"></i><?= lang('Marketing.quotation_details') ?></h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quotationTitle" class="form-label"><?= lang('Marketing.quotation_title') ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="quotationTitle" name="quotation_title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="validUntil" class="form-label"><?= lang('Marketing.valid_until') ?> <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="validUntil" name="valid_until" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="quotationDescription" class="form-label"><?= lang('App.description') ?></label>
                        <textarea class="form-control" id="quotationDescription" name="quotation_description" rows="2" placeholder="<?= lang('Marketing.brief_description_quotation') ?>"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal']) ?>
                    <?= ui_button('submit', 'Create Prospect', ['type' => 'submit']) ?>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade modal-wider" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="detailModalLabel">Quotation Details</h5>
                    <small class="text-muted" id="quotationSubtitle"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs mb-3" id="quotationDetailTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="quotation-info-tab" data-bs-toggle="tab" data-bs-target="#quotation-info-content" type="button" role="tab" aria-controls="quotation-info-content" aria-selected="true">
                            <i class="fas fa-file-alt me-1"></i>Quotation Info
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications-content" type="button" role="tab" aria-controls="specifications-content" aria-selected="false">
                            <i class="fas fa-cogs me-1"></i>Specifications (<span id="specCountQuotation">0</span>)
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="quotationDetailTabContent">
                    <!-- Quotation Info Tab -->
                    <div class="tab-pane fade show active" id="quotation-info-content" role="tabpanel" aria-labelledby="quotation-info-tab">
                        <div id="detailContent">
                            <!-- Content will be loaded dynamically -->
                        </div>
                    </div>

                    <!-- Specifications Tab -->
                    <div class="tab-pane fade" id="specifications-content" role="tabpanel" aria-labelledby="specifications-tab">
                        <!-- Header with Add Buttons -->
                        <div class="mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-2 mb-md-0"><strong>Specifications for SPK</strong></h6>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <?= ui_button('add', lang('App.unit'), [
                                        'onclick' => 'openAddSpecificationModal()',
                                        'size' => 'sm',
                                        'color' => 'success',
                                        'class' => 'me-2'
                                    ]) ?>
                                    <?= ui_button('add', lang('Marketing.attachment_only'), [
                                        'onclick' => 'openAddAttachmentModal()',
                                        'size' => 'sm',
                                        'color' => 'info'
                                    ]) ?>
                                </div>
                            </div>
                        </div>                        <!-- SPK Creation Info Alert - only visible when quotation is DEAL status -->
                        <div class="alert alert-info d-none" id="spkCreationInfo">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Quotation DEAL!</strong> You can create an SPK based on the specifications that have been made.
                        </div>

                        <div id="spesifikasiListContract">
                            <p class="text-muted">Loading specifications...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <div id="quotationActions">
                    <!-- Action buttons will be populated dynamically -->
                </div>
                <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal', 'color' => 'secondary']) ?>
            </div>
        </div>
    </div>
</div>

<!-- Unified Specification Modal (Add & Edit) -->
<div class="modal fade modal-wide" id="addSpecificationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-muted" id="specModalHeader">
                <h6 class="modal-title fw-600" id="specModalTitle">
                    <i class="fas fa-cogs me-2"></i><?= lang('Marketing.add_unit_specification') ?>
                </h6>
                <button class="btn-close btn-close-muted" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSpecificationForm" method="post" action="javascript:void(0)">
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <input type="hidden" name="id_quotation" id="specQuotationId">
                    <input type="hidden" name="id_specification" id="specId">
                    <input type="hidden" name="specification_type" id="specType" value="UNIT">
                    
                    <!-- Info Box -->
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong><?= $isEn ? 'Input Guide:' : 'Panduan Pengisian:' ?></strong>
                        <?= $isEn
                            ? 'Fill the main fields requested by customer. If there are special technical needs (Battery Type, Charger, special Valve), write them in <strong>Notes</strong> below.'
                            : 'Isi field utama yang customer tanyakan. Jika ada kebutuhan teknis khusus (Battery Type, Charger, Valve khusus), tulis di <strong>Notes</strong> di bagian bawah.' ?>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label"><?= lang('Marketing.quantity_required') ?></label>
                            <input type="number" class="form-control" name="quantity" min="1" value="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= lang('Marketing.specification_name') ?></label>
                            <input type="text" class="form-control" name="specification_name" placeholder="<?= lang('App.optional') ?>">
                            <small class="text-muted"><?= lang('Marketing.enter_description_spec1') ?></small>
                        </div>
                        
                        <!-- Spare Unit Checkbox & Quantity -->
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="includeSpareUnits" value="1">
                                <label class="form-check-label fw-bold text-warning" for="includeSpareUnits">
                                    <i class="fas fa-box-open me-1"></i><?= $isEn ? 'Include Spare Units' : 'Sertakan Unit Cadangan (Spare)' ?>
                                </label>
                                <small class="text-muted d-block ms-4">
                                    <?= $isEn ? 'Add backup units for operational continuity (not charged, for zero downtime)' : 'Tambahkan unit backup untuk kontinuitas operasional (tidak ditagih, untuk jaga-jaga downtime)' ?>
                                </small>
                            </div>
                        </div>
                        
                        <!-- Spare Unit Details (shown when checkbox is checked) -->
                        <div class="col-12" id="spareUnitsContainer" style="display: none;">
                            <div class="alert alert-warning border-warning bg-light">
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-3">
                                        <label class="form-label mb-0 fw-bold">
                                            <i class="fas fa-box me-1"></i><?= $isEn ? 'Spare Quantity' : 'Jumlah Spare' ?>
                                        </label>
                                        <input type="number" class="form-control" name="spare_quantity" id="spareQuantity" min="0" value="0" placeholder="0">
                                        <small class="text-muted"><?= $isEn ? 'Backup units' : 'Unit cadangan' ?></small>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="d-flex align-items-center gap-3">
                                            <div>
                                                <strong><?= $isEn ? 'Billable Units:' : 'Unit yang Ditagih:' ?></strong>
                                                <span id="billableUnitsDisplay" class="badge badge-soft-green ms-2">0</span>
                                            </div>
                                            <div>
                                                <strong><?= $isEn ? 'Spare Units:' : 'Unit Spare:' ?></strong>
                                                <span id="spareUnitsDisplay" class="badge badge-soft-yellow ms-2">0</span>
                                            </div>
                                            <div>
                                                <strong><?= $isEn ? 'Total Delivered:' : 'Total Dikirim:' ?></strong>
                                                <span id="totalUnitsDisplay" class="badge badge-soft-blue ms-2">0</span>
                                            </div>
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            <i class="fas fa-info-circle me-1"></i>
                                            <?= $isEn ? 'Billing calculation: Billable Units Ãƒâ€” Monthly Price' : 'Perhitungan tagihan: Unit yang Ditagih Ãƒâ€” Harga Bulanan' ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Include Operator Checkbox -->
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="include_operator" id="includeOperator" value="1">
                                <label class="form-check-label fw-bold text-info" for="includeOperator">
                                    <i class="fas fa-user-tie me-1"></i><?= $isEn ? 'Include Operator' : 'Termasuk Operator' ?>
                                </label>
                                <small class="text-muted d-block ms-4">
                                    <?= $isEn ? 'Contract includes operator provisioning for this unit.' : 'Kontrak termasuk penyediaan operator untuk unit' ?>
                                </small>
                            </div>
                        </div>
                        
                        <!-- Operator Details (shown when checkbox is checked) - HORIZONTAL LAYOUT -->
                        <div class="col-12" id="operatorDetailsContainer" style="display: none;">
                            <hr class="my-2">
                            <h6 class="text-info mb-3"><i class="fas fa-user-tie me-2"></i><?= $isEn ? 'Operator Details' : 'Detail Operator' ?></h6>
                            <div class="row g-3" id="operatorDetails">
                                <div class="col-md-6">
                                    <label class="form-label"><?= $isEn ? 'Operator Quantity' : 'Jumlah Operator' ?> <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="operator_quantity" id="operatorQuantity" min="1" value="1" placeholder="<?= $isEn ? 'Operator quantity' : 'Jumlah operator' ?>">
                                    <small class="text-muted"><?= $isEn ? 'How many operators per unit' : 'Per unit berapa operator' ?></small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><?= $isEn ? 'Operator Rate Source' : 'Sumber Tarif Operator' ?></label>
                                    <div class="form-control bg-light">
                                        <?= $isEn ? 'Auto from Customer Location in DI ' : 'Otomatis dari Customer Location saat pembuatan DI' ?>
                                    </div>
                                    <small class="text-muted"><?= $isEn ? 'No operator rate input in quotation.' : 'Harga operator tidak diisi di quotation.' ?></small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label"><?= lang('Marketing.monthly_rental_price') ?> <span class="text-danger" id="monthlyPriceRequired">*</span></label>
                            <input type="text" class="form-control" name="unit_price" id="monthlyPrice" inputmode="numeric" autocomplete="off" placeholder="<?= lang('Marketing.rp_per_unit_per_month') ?>">
                            <small class="text-muted"><?= lang('Marketing.fill_one_monthly_or_daily') ?></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= lang('Marketing.daily_rental_price') ?> <span class="text-danger" id="dailyPriceRequired">*</span></label>
                            <input type="text" class="form-control" name="harga_per_unit_harian" id="dailyPrice" inputmode="numeric" autocomplete="off" placeholder="<?= lang('Marketing.rp_per_unit_per_day') ?>">
                            <small class="text-muted"><?= lang('Marketing.fill_one_monthly_or_daily') ?></small>
                        </div>
                        
                        <div class="col-12"><hr><h6><?= lang('Marketing.technical_specifications') ?></h6></div>
                        
                        <div class="col-md-4">
                            <label class="form-label"><?= lang('App.department') ?> <span class="text-danger">*</span></label>
                            <select class="form-select" name="departemen_id" id="specDepartemen" required></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><?= lang('Marketing.unit_type') ?> <span class="text-danger">*</span></label>
                            <select class="form-select" name="tipe_unit_id" id="specTipeUnit" required>
                                <option value=""><?= lang('Marketing.select_unit_type') ?></option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label"><?= lang('Marketing.capacity') ?></label>
                            <select class="form-select" name="kapasitas_id" id="specKapasitas">
                                <option value=""><?= lang('Marketing.select_capacity') ?></option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= lang('Marketing.unit_brand') ?></label>
                            <select class="form-select" name="brand_id" id="specMerkUnit">
                                <option value=""><?= lang('Marketing.select_brand') ?></option>
                            </select>
                        </div>
                        <div class="col-md-6" id="specForkAttachmentWrapper">
                            <label class="form-label">Fork / Attachment</label>
                            <!-- Toggle: Fork or Attachment -->
                            <div class="btn-group w-100 mb-2" role="group" id="forkAttachToggle">
                                <input type="radio" class="btn-check" name="fork_attach_type" id="optNone" value="none" checked autocomplete="off">
                                <label class="btn btn-outline-secondary btn-sm" for="optNone">Tidak Ada</label>

                                <input type="radio" class="btn-check" name="fork_attach_type" id="optFork" value="fork" autocomplete="off">
                                <label class="btn btn-outline-primary btn-sm" for="optFork"><i class="fas fa-tools me-1"></i>Fork Standar</label>

                                <input type="radio" class="btn-check" name="fork_attach_type" id="optAttachment" value="attachment" autocomplete="off">
                                <label class="btn btn-outline-success btn-sm" for="optAttachment"><i class="fas fa-paperclip me-1"></i>Attachment</label>
                            </div>
                            <!-- Fork dropdown -->
                            <div id="specForkSection" style="display:none;">
                                <select class="form-select" name="fork_id" id="specForkId">
                                    <option value="">-- Pilih Jenis Fork --</option>
                                </select>
                                <small class="text-muted">Ukuran fork standar</small>
                            </div>
                            <!-- Attachment dropdown -->
                            <div id="specAttachSection" style="display:none;">
                                <select class="form-select" name="attachment_id" id="specAttachmentTipe">
                                    <option value="">-- Pilih Jenis Attachment --</option>
                                </select>
                                <small class="text-muted">Untuk attachment custom, tulis di Notes</small>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label"><?= lang('Marketing.mast') ?> (Model)</label>
                            <select class="form-select" id="specMastModel">
                                <option value="">Pilih Model Mast</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><?= lang('Marketing.mast') ?> (Tinggi)</label>
                            <select class="form-select" name="mast_id" id="specMastHeight">
                                <option value="">Pilih Tinggi Mast</option>
                            </select>
                            <small class="text-muted">Untuk mast custom, tulis di Notes</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= lang('Marketing.tire') ?> (Ban)</label>
                            <select class="form-select" name="ban_id" id="specBan">
                                <option value="">Pilih Tire (Opsional)</option>
                            </select>
                            <small class="text-muted">Solid atau Pneumatic</small>
                        </div>
                        
                        <!-- Accessories Section -->
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <hr class="w-100">
                        </div>
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><?= lang('Marketing.unit_accessories') ?></h6>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btnSetAksesoriStandar">
                                <i class="fas fa-check-double me-1"></i>Set Aksesori Standar
                            </button>
                        </div>
                        <div class="col-12">
                            <div id="accGridQuotation"></div>
                        </div>

                        <!-- Notes Section -->
                        <div class="col-12"><hr><h6><i class="fas fa-sticky-note me-2"></i>Catatan & Custom Requirements</h6></div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" id="specNotes" rows="7" placeholder="Contoh:
- Battery Type: Lithium-ion 80V/500Ah
- Charger: Merk ABC Tipe XYZ
- Valve: Butuh 4 valve untuk Paper Roll Clamp
- Mast custom: 6 meter dengan side shifter
- Attachment custom
- Permintaan khusus lainnya dari customer"></textarea>
                            <small class="text-muted"><i class="fas fa-lightbulb text-warning me-1"></i>Gunakan field ini untuk mencatat kebutuhan teknis khusus atau permintaan custom dari customer</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal']) ?>
                    <?= ui_button('save', '<span id="submitBtnText">Save Specification</span>', [
                        'type' => 'submit',
                        'id' => 'submitSpecificationBtn'
                    ]) ?>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Attachment Modal -->
<div class="modal fade" id="addAttachmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-muted">
                <h6 class="modal-title fw-600">
                    <i class="fas fa-paperclip me-2"></i>Add Attachment Specification
                </h6>
                <button class="btn-close btn-close-muted" data-bs-dismiss="modal"></button>
            </div>
            <form id="addAttachmentForm" method="post" action="javascript:void(0)">
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <input type="hidden" name="id_quotation" id="attachmentQuotationId">
                    <input type="hidden" name="specification_type" id="attachmentSpecType" value="ATTACHMENT">
                    <input type="hidden" name="category" value="ATTACHMENT">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label"><?= lang('Marketing.quantity_required') ?> <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="quantity" min="1" value="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= lang('Marketing.specification_name') ?></label>
                            <input type="text" class="form-control" name="specification_name" placeholder="<?= lang('App.optional') ?>">
                            <small class="text-muted"><?= lang('Marketing.eg_fork_attachment') ?></small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label"><?= lang('Marketing.monthly_rental_price') ?> <span class="text-muted">(<?= lang('App.optional') ?>)</span></label>
                            <input type="text" class="form-control" name="unit_price" id="attachmentMonthlyPrice" inputmode="numeric" autocomplete="off" placeholder="<?= lang('Marketing.rp_per_unit_per_month') ?>">
                            <small class="text-muted"><?= lang('Marketing.fill_one_monthly_or_daily') ?></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= lang('Marketing.daily_rental_price') ?> <span class="text-muted">(<?= lang('App.optional') ?>)</span></label>
                            <input type="text" class="form-control" name="harga_per_unit_harian" id="attachmentDailyPrice" inputmode="numeric" autocomplete="off" placeholder="<?= lang('Marketing.rp_per_unit_per_day') ?>">
                            <small class="text-muted"><?= lang('Marketing.fill_one_monthly_or_daily') ?></small>
                        </div>
                        
                        <div class="col-12"><hr><h6><?= lang('Marketing.attachment_details') ?></h6></div>
                        
                        <div class="col-md-6">
                            <label class="form-label"><?= lang('Marketing.attachment_type') ?> <span class="text-danger">*</span></label>
                            <select class="form-select" name="attachment_tipe" id="attachmentTipe" required></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= lang('Marketing.attachment_brand') ?></label>
                            <input type="text" class="form-control" name="attachment_merk" placeholder="<?= lang('Marketing.eg_oem_cascade') ?>">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label"><?= lang('Marketing.description_notes') ?></label>
                            <textarea class="form-control" name="specification_description" rows="3" placeholder="<?= lang('Marketing.additional_details_attachment') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal']) ?>
                    <?= ui_button('save', 'Save Attachment', [
                        'type' => 'submit',
                        'id' => 'submitAttachmentBtn'
                    ]) ?>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Unified Customer Location Modal (Select or Add) -->
<div class="modal fade" id="selectCustomerLocationModal" tabindex="-1" aria-labelledby="selectLocationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="selectLocationModalLabel">Customer Location</h5>
                    <small class="text-muted" id="locationModalCustomerName"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" id="locationModalAlert">
                    <i class="fas fa-map-marker-alt"></i> <?= lang('Marketing.please_select_location_or_add_new') ?>
                </div>
                
                <!-- Toggle Buttons -->
                <div class="btn-group w-100 mb-4" role="group" aria-label="Location mode">
                    <button type="button" class="btn btn-outline-primary active" id="btnSelectExisting">
                        <i class="fas fa-list"></i> <?= lang('Marketing.select_existing') ?>
                    </button>
                    <button type="button" class="btn btn-outline-success" id="btnAddNew">
                        <i class="fas fa-plus"></i> <?= lang('Marketing.add_new_location') ?>
                    </button>
                </div>
                
                <!-- Select Existing Location Section -->
                <div id="existingLocationSection">
                    <div class="mb-3">
                        <label for="modalLocationSelect" class="form-label fw-bold"><?= lang('Marketing.existing_locations') ?>:</label>
                        <select class="form-control form-control-lg" id="modalLocationSelect">
                            <option value=""><?= lang('Marketing.select_a_location') ?></option>
                        </select>
                    </div>
                    
                    <!-- Location Details Preview -->
                    <div id="locationPreview" class="card d-none mt-3">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-map-marker-alt text-primary"></i> <?= lang('Marketing.location_details') ?></h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong><?= lang('Marketing.location_name') ?>:</strong></p>
                                    <p class="text-muted" id="preview_location_name">-</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong><?= lang('Marketing.contact_person') ?>:</strong></p>
                                    <p class="text-muted" id="preview_contact_person">-</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <p class="mb-1"><strong><?= lang('App.address') ?>:</strong></p>
                                    <p class="text-muted" id="preview_address">-</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-1"><strong><?= lang('App.city') ?>:</strong></p>
                                    <p class="text-muted" id="preview_city">-</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong><?= lang('App.province') ?>:</strong></p>
                                    <p class="text-muted" id="preview_province">-</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong><?= lang('Marketing.contact_phone') ?>:</strong></p>
                                    <p class="text-muted" id="preview_contact_phone">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Add New Location Section -->
                <div id="addNewLocationSection" style="display: none;">
                    <form id="customerLocationFormUnified">
                        <input type="hidden" name="customer_id" id="unified_customer_id">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="unified_location_name" class="form-label"><?= lang('Marketing.location_name') ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="unified_location_name" name="location_name" required maxlength="255">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="unified_location_code" class="form-label"><?= lang('Marketing.location_code') ?></label>
                                <input type="text" class="form-control" id="unified_location_code" name="location_code" maxlength="50">
                                <small class="form-text text-muted"><?= lang('Marketing.optional_auto_generated') ?></small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="unified_area_id" class="form-label"><?= lang('App.area') ?> <span class="text-danger">*</span></label>
                                <select class="form-control" id="unified_area_id" name="area_id" required>
                                    <option value=""><?= lang('Marketing.select_area') ?></option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="unified_contact_person" class="form-label"><?= lang('Marketing.contact_person') ?></label>
                                <input type="text" class="form-control" id="unified_contact_person" name="contact_person" maxlength="255">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="unified_phone" class="form-label"><?= lang('App.phone') ?></label>
                                <input type="text" class="form-control" id="unified_phone" name="phone" maxlength="20">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="unified_email" class="form-label"><?= lang('App.email') ?></label>
                                <input type="email" class="form-control" id="unified_email" name="email" maxlength="128">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="unified_address" class="form-label"><?= lang('App.address') ?> <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="unified_address" name="address" rows="3" required maxlength="500"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="unified_city" class="form-label"><?= lang('App.city') ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="unified_city" name="city" required maxlength="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="unified_province" class="form-label"><?= lang('App.province') ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="unified_province" name="province" required maxlength="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="unified_postal_code" class="form-label"><?= lang('Marketing.postal_code') ?></label>
                                <input type="text" class="form-control" id="unified_postal_code" name="postal_code" maxlength="10">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="unified_notes" class="form-label"><?= lang('App.notes') ?></label>
                            <textarea class="form-control" id="unified_notes" name="notes" rows="2" maxlength="255"></textarea>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="unified_is_primary" name="is_primary" value="1" checked>
                            <label class="form-check-label" for="unified_is_primary">
                                <?= lang('Marketing.set_as_primary_location') ?>
                            </label>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal', 'color' => 'secondary']) ?>
                <?= ui_button('submit', 'Continue', [
                    'id' => 'continueWithLocationBtn',
                    'icon' => 'fas fa-arrow-right'
                ]) ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Contract Modal -->
<div class="modal fade modal-wide" id="addContractModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Contract & PO</h5>
                    <small class="text-muted d-block" id="contractModalCustomerName"></small>
                    <small class="text-muted">Select existing contract or create new</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addContractForm">
                <div class="modal-body">
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>Information
                        </h6>
                        <p class="mb-0">
                            <strong>Select existing contract</strong> or <strong>create new</strong>. 
                            If you select existing, data will be loaded automatically. If you create new, fill out the form below.
                        </p>
                    </div>
                    
                    <!-- Single Dropdown for Contract/PO Selection -->
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Contract / PO <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" id="contractOrPOSelect" required style="background-color: #f8f9fa;">
                                <option value="">Select Contract / PO</option>
                                <option value="__ADD_NEW__" style="background-color: #e3f2fd; color: #1976d2; font-weight: 600;">
                                    &#43; Add New Contract
                                </option>
                            </select>
                            <small class="form-text text-muted">Search by Contract Number or Customer PO Number, or select "+ Add New Contract"</small>
                        </div>
                    </div>
                    
                    <!-- Contract Form (shown when __ADD_NEW__ or existing selected) -->
                    <div id="contractFormSection" style="display:none;">
                        <hr class="my-4">
                        <h6 class="mb-3">Create New Contract</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contract Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="contract_number" id="contract_number_input" required>
                                    <button class="btn btn-outline-secondary" type="button" id="generateContractNumberBtn" title="Generate Contract Number">
                                        <i class="fas fa-magic"></i>
                                    </button>   
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Customer PO Number</label>
                                <input type="text" class="form-control" name="po_number" id="po_number_input" placeholder="Customer's Purchase Order Number">
                                <small class="text-muted">External PO from customer (if any)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rental Classification</label>
                                <select class="form-select" name="rental_type" id="contract_rental_type">
                                    <option value="CONTRACT" selected>Formal Contract</option>
                                    <option value="PO_ONLY">PO-Based Only</option>
                                    <option value="DAILY_SPOT">Daily/Spot Rental</option>
                                </select>
                                <small class="text-muted">How is this rental documented?</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Customer <span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-light" id="customerNameDisplay" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-light" id="locationNameDisplay" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="start_date" id="contract_start_date" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="end_date" id="contract_end_date" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Billing Period</label>
                                <select class="form-select" name="jenis_sewa" id="contract_jenis_sewa">
                                    <option value="BULANAN" selected>Monthly Rate</option>
                                    <option value="HARIAN">Daily Rate</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Billing Method <i class="fas fa-info-circle text-info" title="How billing cycles are calculated"></i></label>
                                <select class="form-select" name="billing_method" id="contract_billing_method">
                                    <option value="CYCLE" selected>30-Day Rolling Cycle</option>
                                    <option value="PRORATE">Prorate to Month-End</option>
                                    <option value="MONTHLY_FIXED">Fixed Monthly Date</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3" id="billing_start_date_section" style="display:none;">
                                <label class="form-label">Fixed Billing Date</label>
                                <select class="form-select" name="billing_start_date" id="contract_billing_start_date">
                                    <option value="1">1st of Month</option>
                                    <option value="5">5th of Month</option>
                                    <option value="10">10th of Month</option>
                                    <option value="15">15th of Month</option>
                                    <option value="20">20th of Month</option>
                                    <option value="25">25th of Month</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Billing Preview Card -->
                        <div class="card border-primary mb-3" id="billing_preview_card" style="display:none;">
                            <div class="card-header bg-primary bg-opacity-10">
                                <h6 class="mb-0 text-primary">
                                    <i class="fas fa-calendar-alt me-2"></i>Billing Preview
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="billing_preview_content">
                                    <!-- Dynamic content loaded via JavaScript -->
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Billing Notes</label>
                                <textarea class="form-control" name="billing_notes" id="contract_billing_notes" rows="2" placeholder="Special billing instructions (optional)"></textarea>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Contract Notes</label>
                                <textarea class="form-control" name="catatan" id="contract_catatan" rows="3" placeholder="Additional notes (optional)"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden fields -->
                    <input type="hidden" name="quotation_id" id="contractQuotationId">
                    <input type="hidden" name="customer_id" id="customerIdContractNew">
                    <input type="hidden" name="location_id" id="locationIdContractNew">
                    <input type="hidden" name="workflow_completed" value="true">
                    <input type="hidden" id="selectedContractId">
                </div>
                <div class="modal-footer">
                    <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal', 'color' => 'secondary']) ?>
                    <?= ui_button('save', 'Save Contract', ['type' => 'submit']) ?>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create SPK Selection Modal -->
<div class="modal fade modal-wide" id="createSPKModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-muted">
                <div>
                    <h5 class="modal-title">Create SPK from Quotation</h5>
                    <small class="d-block" id="spkModalQuotationInfo"></small>
                </div>
                <button type="button" class="btn-close btn-close-muted" data-bs-dismiss="modal"></button>
            </div>
            <form id="createSPKForm">
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Instructions:</strong> Select specifications and quantities to create SPK. 
                        You can create multiple SPKs from different specifications.
                    </div>
                    
                    <!-- Customer & Contract Details -->
                    <div class="card border-primary mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-building me-2"></i>Customer & Contract Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-2" id="spkCustomerDetails">
                                <div class="col-12 text-muted text-center py-2">
                                    <i class="fas fa-spinner fa-spin me-1"></i> Loading customer details...
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Delivery Date -->
                    <div class="row mb-12">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Estimated Delivery Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="spk_delivery_date" name="delivery_date" required>
                            <small class="text-muted">Target date for unit delivery, default set a 7-day lead time</small>
                        </div>
                    </div>
                    </br>

                    <!-- Specifications List -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Specifications to Create SPK:</label>
                    </div>
                    
                    <div id="spkSpecificationsList" class="border rounded p-3">
                        <!-- Will be populated dynamically -->
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                            <p>Loading specifications...</p>
                        </div>
                    </div>
                    
                    <!-- Hidden fields -->
                    <input type="hidden" id="spk_quotation_id" name="quotation_id">
                    <input type="hidden" id="spk_customer_id" name="customer_id">
                    <input type="hidden" id="spk_contract_id" name="contract_id">
                </div>
                <div class="modal-footer">
                    <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal', 'color' => 'secondary']) ?>
                    <?= ui_button('submit', 'Create Selected SPK(s)', [
                        'type' => 'submit',
                        'id' => 'submitSPKBtn',
                        'icon' => 'fas fa-check'
                    ]) ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<?= $this->include('partials/accessory_js') ?>
<script>
// Render aksesoris grid (quotation form) — runs once; safe for modal reuse
$(function() {
    OptimaAccessory.renderGroupSections('#accGridQuotation',
        ['quotationStandard', 'quotationExtra'], {
        name: 'aksesoris[]',
        idPrefix: 'acc_',
        columnsClass: 'col-md-4 col-sm-6',
        style: 'inline'
    });
});
</script>
<script>
/**
 * Quotations Module - Using Optima Badge Standards (optima-pro.css)
 * 
 * Quick Reference:
 * - ACCEPTED/Success  Ã¢â€ â€™ <span class="badge badge-soft-green">ACCEPTED</span>
 * - SENT/Warning      Ã¢â€ â€™ <span class="badge badge-soft-yellow">SENT</span>
 * - REJECTED/Danger   Ã¢â€ â€™ <span class="badge badge-soft-red">REJECTED</span>
 * - DRAFT/Disabled    Ã¢â€ â€™ <span class="badge badge-soft-gray">DRAFT</span>
 * - Info/Counters     Ã¢â€ â€™ <span class="badge badge-soft-blue">247</span>
 * 
 * See docs/BADGE_STANDARDS.md for complete guide
 */

// Global variable for DataTable
var quotationsTable;

// Alias to keep view code free from direct OptimaUI.fire calls.
window.OptimaUI = window.OptimaUI || {};
window.OptimaUI.fire = function() {
    var fireFn = window.Swal && window.Swal['fire'];
    if (typeof fireFn === 'function') {
        return fireFn.apply(this, arguments);
    }
    return Promise.resolve({ isConfirmed: false, isDenied: false });
};

$(document).ready(function() {
    console.log('Ã°Å¸â€â€ž Initializing Quotations DataTable...');
    
    try {
        // Initialize using OptimaDataTable with minimal config
        quotationsTable = OptimaDataTable.init('#quotationsTable', {
            ajax: {
                url: '<?= base_url('marketing/quotations/data') ?>',
                type: 'POST',
                error: function(xhr, error, code) {
                    console.error('DataTable AJAX error:', xhr.responseText);
                    OptimaUI.fire('Error', 'Failed to load data: ' + xhr.responseText, 'error');
                }
            },
            pageLength: 15,
            lengthMenu: [[10, 15, 25, 50], [10, 15, 25, 50]],
            order: [[6, 'desc']], // Sort by date (column 7) descending
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'quotation_number' },
                { data: 'prospect_name' },
                { data: 'quotation_title' },
                { 
                    data: 'total_amount',
                    render: function(data) {
                        return data || 'Rp 0';
                    }
                },
                { 
                    data: 'workflow_stage',
                    orderable: false
                },
                { data: 'quotation_date' },
                { data: 'actions', orderable: false, searchable: false }
            ],
            rowCallback: function(row, data) {
                // Add pointer cursor and click functionality
                $(row).css('cursor', 'pointer');
                $(row).attr('title', 'Click to view details');
            },
            initComplete: function(settings, json) {
                console.log('Ã¢Å“â€¦ Quotations DataTable initialized successfully');
                
                // Add row click functionality
                $('#quotationsTable tbody').on('click', 'tr', function(e) {
                    // Don't trigger row click if user clicked on action buttons
                    if (!$(e.target).closest('.btn, .dropdown').length) {
                        var data = quotationsTable.row(this).data();
                        if (data && data.id_quotation) {
                            viewQuotation(data.id_quotation);
                        }
                    }
                });
                
                // Load statistics after table is ready
                loadStatistics();
            }
        });
        
        console.log('Ã¢Å“â€¦ Quotations table setup complete');
        
    } catch(error) {
        console.error('Ã¢ÂÅ’ Failed to initialize Quotations DataTable:', error);
        showNotification('Failed to initialize quotations table. Please refresh the page.', 'error');
    }
    
    // Setup date range filter integration
    const dateRangePicker = document.getElementById('quotationDateRangePicker');
    if (dateRangePicker) {
        // Listen for date range changes
        $(dateRangePicker).on('apply.daterangepicker', function(ev, picker) {
            const startDate = picker.startDate.format('YYYY-MM-DD');
            const endDate = picker.endDate.format('YYYY-MM-DD');
            
            console.log('Ã°Å¸â€œâ€¦ Date range changed:', startDate, 'to', endDate);
            
            // Reload table with new date range
            if (quotationsTable && quotationsTable.ajax) {
                quotationsTable.ajax.url(
                    '<?= base_url('marketing/quotations/data') ?>?start_date=' + startDate + '&end_date=' + endDate
                ).load();
            }
            
            // Reload statistics with new date range
            loadStatistics(startDate, endDate);
        });
        
        // Handle reset/clear
        $(dateRangePicker).on('cancel.daterangepicker', function() {
            console.log('Ã°Å¸â€œâ€¦ Date range cleared');
            
            // Reload table without date filter
            if (quotationsTable && quotationsTable.ajax) {
                quotationsTable.ajax.url('<?= base_url('marketing/quotations/data') ?>').load();
            }
            
            // Reload statistics without date filter
            loadStatistics();
        });
    }


    // Form submission
    $('#quotationForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var url = '<?= base_url('marketing/quotations/store') ?>';
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    OptimaUI.fire('Success', response.message, 'success');
                    $('#quotationModal').modal('hide');
                    quotationsTable.ajax.reload();
                    loadStatistics();
                    $('#quotationForm')[0].reset();
                } else {
                    OptimaUI.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr) {
                console.error('Form submission error:', xhr.responseText);
                OptimaUI.fire('Error', 'Failed to save quotation', 'error');
            }
        });
    });

    // Reset form when modal is hidden
    $('#quotationModal').on('hidden.bs.modal', function() {
        $('#quotationForm')[0].reset();
        $('#quotationId').val('');
        $('#quotationModalLabel').text('Add New Quotation');
    });
    
    // === MODAL SIZE ARCHITECTURE ===
    // Ã¢Å“â€¦ Modal sizes now controlled by CSS classes (see optima-pro.css):
    // - detailModal uses .modal-wider (70vw) for comfortable detail viewing
    // - Other modals use Bootstrap classes (modal-xl, modal-lg) based on content complexity
    // No JavaScript forcing needed - CSS architecture handles all sizing properly
    
    // Include Operator toggle functionality
    $(document).on('change', '#includeOperator', function() {
        const isChecked = $(this).is(':checked');
        const operatorDetailsContainer = $('#operatorDetailsContainer');
        
        if (isChecked) {
            operatorDetailsContainer.slideDown(300);
            // Make operator fields required
            $('#operatorQuantity').prop('required', true);
        } else {
            operatorDetailsContainer.slideUp(300);
            // Remove required and clear values
            $('#operatorQuantity')
                .prop('required', false)
                .val('');
        }
    });
    
    // Include Spare Units toggle functionality
    $(document).on('change', '#includeSpareUnits', function() {
        const isChecked = $(this).is(':checked');
        const spareUnitsContainer = $('#spareUnitsContainer');
        
        if (isChecked) {
            spareUnitsContainer.slideDown(300);
            // Update calculation on show
            updateTotalUnitsDisplay();
        } else {
            spareUnitsContainer.slideUp(300);
            // Reset spare quantity when unchecked
            $('#spareQuantity').val('0');
            updateTotalUnitsDisplay();
        }
    });
    
    // Auto-calculate total units when quantity or spare_quantity changes
    $(document).on('input', 'input[name="quantity"], #spareQuantity', function() {
        updateTotalUnitsDisplay(); // Calls global function defined above
    });
    
    // Spare Unit toggle functionality (old logic - deprecated but keep for backward compatibility)
    $(document).on('change', '#isSpareUnit', function() {
        const isChecked = $(this).is(':checked');
        const monthlyPriceField = $('#monthlyPrice');
        const dailyPriceField = $('#dailyPrice');
        
        if (isChecked) {
            // Spare unit selected - disable and clear price fields
            monthlyPriceField.val('0').prop('disabled', true).addClass('bg-light');
            dailyPriceField.val('0').prop('disabled', true).addClass('bg-light');
            
            // Remove required indicators
            $('#monthlyPriceRequired, #dailyPriceRequired').hide();
            
            // Show info message
            if (!$('#spareUnitInfo').length) {
                monthlyPriceField.closest('.col-md-6').append(
                    '<small id="spareUnitInfo" class="text-success d-block mt-1">' +
                    '<i class="fas fa-check-circle"></i> Spare unit - tidak akan ditagih' +
                    '</small>'
                );
            }
        } else {
            // Normal unit - enable price fields
            monthlyPriceField.val('').prop('disabled', false).removeClass('bg-light');
            dailyPriceField.val('').prop('disabled', false).removeClass('bg-light');
            
            // Show required indicators
            $('#monthlyPriceRequired, #dailyPriceRequired').show();
            
            // Remove info message
            $('#spareUnitInfo').remove();
        }
    });

    // === UNIFIED LOCATION MODAL EVENT HANDLERS ===
    // Cleanup: Remove any existing handlers first to prevent duplicates
    $('#continueWithLocationBtn').off('click');
    $('#btnSelectExisting').off('click');
    $('#btnAddNew').off('click');
    $('#modalLocationSelect').off('change');
    
    // Toggle between Select Existing and Add New modes
    $('#btnSelectExisting').on('click', function() {
        $(this).addClass('active');
        $('#btnAddNew').removeClass('active');
        $('#existingLocationSection').show();
        $('#addNewLocationSection').hide();
        $('#continueWithLocationBtn').html('<i class="fas fa-arrow-right"></i> Continue with Selected');
    });
    
    $('#btnAddNew').on('click', function() {
        $(this).addClass('active');
        $('#btnSelectExisting').removeClass('active');
        $('#existingLocationSection').hide();
        $('#addNewLocationSection').show();
        $('#continueWithLocationBtn').html('<i class="fas fa-save"></i> Save & Continue');
        
        // Load areas when switching to Add New mode
        loadAreasForLocation();
    });
    
    // Location select change handler - show preview
    $('#modalLocationSelect').on('change', function() {
        const selectedLocationId = $(this).val();
        const locations = $('#selectCustomerLocationModal').data('existingLocations') || [];
        
        if (selectedLocationId) {
            const selectedLocation = locations.find(loc => loc.id == selectedLocationId);
            if (selectedLocation) {
                $('#preview_location_name').text(selectedLocation.location_name || '-');
                $('#preview_contact_person').text(selectedLocation.contact_person || '-');
                $('#preview_address').text(selectedLocation.address || '-');
                $('#preview_city').text(selectedLocation.city || '-');
                $('#preview_province').text(selectedLocation.province || '-');
                $('#preview_contact_phone').text(selectedLocation.contact_phone || selectedLocation.phone || '-');
                $('#locationPreview').removeClass('d-none');
            }
        } else {
            $('#locationPreview').addClass('d-none');
        }
    });
    
    // Continue button handler for unified modal
    $('#continueWithLocationBtn').on('click', debounce(function() {
        const customerId = $('#selectCustomerLocationModal').data('customerId');
        const quotationId = $('#selectCustomerLocationModal').data('quotationId');
        const dealMessage = $('#selectCustomerLocationModal').data('dealMessage');
        const isAddNewMode = $('#btnAddNew').hasClass('active');
        
        if (isAddNewMode) {
            // Add New Location mode - validate and save
            const locationData = {
                customer_id: customerId,
                location_name: $('#unified_location_name').val().trim(),
                location_code: $('#unified_location_code').val().trim(),
                area_id: $('#unified_area_id').val(),
                contact_person: $('#unified_contact_person').val().trim(),
                phone: $('#unified_phone').val().trim(),
                email: $('#unified_email').val().trim(),
                address: $('#unified_address').val().trim(),
                city: $('#unified_city').val().trim(),
                province: $('#unified_province').val().trim(),
                postal_code: $('#unified_postal_code').val().trim(),
                notes: $('#unified_notes').val().trim(),
                is_primary: $('#unified_is_primary').is(':checked') ? 1 : 0
            };
            
            // Validate required fields
            if (!locationData.location_name || !locationData.address || !locationData.city || 
                !locationData.province || !locationData.area_id) {
                OptimaUI.fire('Error', 'Please fill all required fields (marked with *)', 'error');
                return;
            }
            
            // Mark workflow completed and close modal
            $('#selectCustomerLocationModal').data('workflowCompleted', true);
            $('#selectCustomerLocationModal').modal('hide');
            
            // Save location
            saveCustomerLocation(customerId, locationData, quotationId);
            
        } else {
            // Select Existing mode - validate selection
            const selectedLocation = $('#modalLocationSelect').val();
            
            if (!selectedLocation) {
                OptimaUI.fire('Error', 'Please select a location from the list', 'error');
                return;
            }
            
            // Store location ID globally for contract creation
            window.currentSelectedLocationId = selectedLocation;
            // Location ID stored for contract creation
            
            // Mark workflow completed and close modal
            $('#selectCustomerLocationModal').data('workflowCompleted', true);
            $('#selectCustomerLocationModal').modal('hide');
            
            // Update primary location and continue
            updateCustomerPrimaryLocation(customerId, selectedLocation, quotationId, dealMessage);
        }
    }, 300));
    
    // === CONTRACT MODAL EVENT HANDLERS ===
    
    // Dropdown change handler - show form and load data based on selection
    $('#contractOrPOSelect').on('change', function() {
        const selectedValue = $(this).val();
        const contractFormSection = $('#contractFormSection');
        
        if (selectedValue === '__ADD_NEW__') {
            // Show empty form for new contract
            resetContractForm();
            contractFormSection.slideDown();
            
            // Enable all fields for new entry
            $('#contract_number_input, #po_number_input').prop('disabled', false).prop('readonly', false).removeClass('bg-light');
            $('#contract_start_date, #contract_end_date, #contract_jenis_sewa').prop('disabled', false);
            $('#contract_catatan').prop('readonly', false).removeClass('bg-light');
            $('#generateContractNumberBtn').prop('disabled', false);
            
            // Clear customer and location (will use current quotation's customer)
            $('#customerNameDisplay').val('').prop('readonly', true);
            $('#locationNameDisplay').val('').prop('readonly', true);
            
            // Change button text for creating new contract
            $('#submitContractBtn').html('<i class="fas fa-save me-2"></i>Save New Contract');
            
        } else if (selectedValue) {
            // Load existing contract data - 100% READ-ONLY
            const selectedOption = $(this).find('option:selected');
            const contractData = selectedOption.data('contract');
            
            if (contractData) {
                populateContractForm(contractData);
                contractFormSection.slideDown();
                
                // Make ALL fields READ-ONLY for existing contract (Option 1: Pure Selection)
                $('#contract_number_input, #po_number_input').prop('readonly', true).addClass('bg-light');
                $('#customerNameDisplay, #locationNameDisplay').prop('readonly', true).addClass('bg-light');
                $('#contract_start_date, #contract_end_date, #contract_jenis_sewa').prop('disabled', true).addClass('bg-light');
                $('#contract_catatan').prop('readonly', true).addClass('bg-light');
                $('#generateContractNumberBtn').prop('disabled', true);
                
                // Change button text to indicate linking/using existing contract
                $('#submitContractBtn').html('<i class="fas fa-link me-2"></i>Use This Contract');
            }
            
        } else {
            // No selection - hide form
            contractFormSection.slideUp();
            resetContractForm();
        }
    });
    
    // Auto-load contract by number or PO (mutual search)
    let contractLoadTimeout;
    $('#contract_number_input, #po_number_input').on('input', function() {
        clearTimeout(contractLoadTimeout);
        const isContractNumber = $(this).attr('id') === 'contract_number_input';
        const field = isContractNumber ? 'no_kontrak' : 'customer_po_number';
        const value = $(this).val().trim();
        
        if (value.length >= 3) {
            contractLoadTimeout = setTimeout(() => {
                searchAndLoadContract(field, value);
            }, 500);
        }
    });
    
    // Submit contract form - prevent default form submission
    $('#addContractForm').on('submit', function(e) {
        e.preventDefault(); // Prevent page reload
        
        const quotationId = window.currentContractQuotationId;
        const customerId = window.currentContractCustomerId;
        const selectedContractId = $('#contractOrPOSelect').val();
        
        if (selectedContractId && selectedContractId !== '__ADD_NEW__') {
            // OPTION 1: Link existing contract to quotation (READ-ONLY, no update)
            linkExistingContract(selectedContractId, quotationId);
            
        } else {
            // Create new contract
            const contractNumber = $('#contract_number_input').val().trim();
            const poNumber = $('#po_number_input').val().trim();
            const locationId = $('#locationIdContractNew').val();
            const startDate = $('#contract_start_date').val();
            const endDate = $('#contract_end_date').val();
            
            // Validate required fields for new contract
            if (!contractNumber && !poNumber) {
                OptimaUI.fire('Error', 'Please fill Contract Number OR Customer PO Number', 'error');
                return false;
            }
            
            if (!locationId || !startDate || !endDate) {
                OptimaUI.fire('Error', 'Please fill all required fields', 'error');
                return false;
            }
            
            // Prepare form data for new contract
            const formData = {
                contract_number: contractNumber,
                po_number: poNumber,
                customer_location_id: locationId,
                start_date: startDate,
                end_date: endDate,
                jenis_sewa: $('#contract_jenis_sewa').val(),
                catatan: $('#contract_catatan').val(),
                quotation_id: quotationId,
                workflow_completed: true
            };
            
            saveNewContract(formData);
        }
        
        return false; // Prevent any default form behavior
    });
    
    // Clean up contract form when modal is closed
    // Clean up contract modal when closed
    $('#addContractModal').on('hidden.bs.modal', function() {
        // Reset dropdown to default
        $('#contractOrPOSelect').val('');
        
        // Hide and reset form
        $('#contractFormSection').hide();
        resetContractForm();
        
        // Reset hidden fields
        $('#customerIdContractNew').val('');
        $('#locationIdContractNew').val('');
        $('#contractQuotationId').val('');
        $('#customerNameDisplay').val('');
        $('#locationNameDisplay').val('');
        $('#selectedContractId').val('');
        
        // Hide daily rate calculator
        $('#daily_rate_calculator').hide();
    });

    // Daily Rate Calculator - Show/Hide based on billing period
    $('#contract_jenis_sewa').on('change', function() {
        const jenisSewaValue = $(this).val();
        if (jenisSewaValue === 'HARIAN') {
            $('#daily_rate_calculator').show();
            calculateDailyRateTotal(); // Calculate on show
        } else {
            $('#daily_rate_calculator').hide();
        }
    });

    // Daily Rate Calculator - Auto-calculate when inputs change
    $('#contract_start_date, #contract_end_date, #calc_daily_rate').on('change keyup', function() {
        if ($('#contract_jenis_sewa').val() === 'HARIAN') {
            calculateDailyRateTotal();
        }
    });
    
    // Daily Rate Calculator Function
    function calculateDailyRateTotal() {
        const startDate = $('#contract_start_date').val();
        const endDate = $('#contract_end_date').val();
        const dailyRate = parseFloat($('#calc_daily_rate').val()) || 0;
        
        // Get number of units from quotation (will be set when modal opened)
        const totalUnits = parseInt($('#calc_total_units').val()) || 0;
        
        // Calculate duration in days
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // +1 to include both start and end day
            
            $('#calc_duration_days').val(diffDays);
            
            // Calculate total value: Days Ãƒâ€” Units Ãƒâ€” Daily Rate
            const totalValue = diffDays * totalUnits * dailyRate;
            $('#calc_total_value').val(formatRupiah(totalValue));
        } else {
            $('#calc_duration_days').val('');
            $('#calc_total_value').val('');
        }
    }
    
    // Billing Method Handler - Show/Hide billing start date and preview
    $('#contract_billing_method').on('change', function() {
        const billingMethod = $(this).val();
        
        // Show/hide billing start date field (only for MONTHLY_FIXED)
        if (billingMethod === 'MONTHLY_FIXED') {
            $('#billing_start_date_section').show();
        } else {
            $('#billing_start_date_section').hide();
        }
        
        // Update billing preview
        updateBillingPreview();
    });
    
    // Update billing preview when relevant fields change
    $('#contract_start_date, #contract_end_date, #contract_billing_start_date').on('change', function() {
        updateBillingPreview();
    });
    
    // Billing Preview Function
    function updateBillingPreview() {
        const billingMethod = $('#contract_billing_method').val();
        const startDate = $('#contract_start_date').val();
        const endDate = $('#contract_end_date').val();
        
        if (!startDate || !endDate) {
            $('#billing_preview_card').hide();
            return;
        }
        
        const start = new Date(startDate);
        let previewHTML = '';
        
        switch (billingMethod) {
            case 'CYCLE':
                previewHTML = `
                    <div class="row">
                        <div class="col-md-12">
                            <p class="mb-2"><strong>30-Day Rolling Cycle</strong></p>
                            <ul class="mb-0">
                                <li>Billing starts from: <strong>${formatDate(start)}</strong></li>
                                <li>Next billing: <strong>${formatDate(addDays(start, 30))}</strong></li>
                                <li>Subsequent billings every 30 days</li>
                                <li>Example: Jan 15 Ã¢â€ â€™ Feb 14 Ã¢â€ â€™ Mar 16 Ã¢â€ â€™ Apr 15</li>
                            </ul>
                        </div>
                    </div>
                `;
                break;
                
            case 'PRORATE':
                const endOfFirstMonth = new Date(start.getFullYear(), start.getMonth() + 1, 0);
                const daysInFirstMonth = Math.ceil((endOfFirstMonth - start) / (1000 * 60 * 60 * 24)) + 1;
                const totalDaysInMonth = new Date(start.getFullYear(), start.getMonth() + 1, 0).getDate();
                const proratePercent = ((daysInFirstMonth / totalDaysInMonth) * 100).toFixed(1);
                
                previewHTML = `
                    <div class="row">
                        <div class="col-md-12">
                            <p class="mb-2"><strong>Prorate to Month-End</strong></p>
                            <ul class="mb-0">
                                <li>First billing (prorated): <strong>${formatDate(start)}</strong> to <strong>${formatDate(endOfFirstMonth)}</strong> (${daysInFirstMonth} days = ${proratePercent}%)</li>
                                <li>Subsequent billings: <strong>1st of each month</strong> (full month)</li>
                                <li>Example: Feb 15-28 (prorated 50%) Ã¢â€ â€™ Mar 1-31 (100%) Ã¢â€ â€™ Apr 1-30 (100%)</li>
                            </ul>
                        </div>
                    </div>
                `;
                break;
                
            case 'MONTHLY_FIXED':
                const billingDay = parseInt($('#contract_billing_start_date').val()) || 1;
                const firstBillingDate = new Date(start.getFullYear(), start.getMonth(), billingDay);
                if (firstBillingDate < start) {
                    firstBillingDate.setMonth(firstBillingDate.getMonth() + 1);
                }
                
                previewHTML = `
                    <div class="row">
                        <div class="col-md-12">
                            <p class="mb-2"><strong>Fixed Monthly Date (${billingDay}th)</strong></p>
                            <ul class="mb-0">
                                <li>First billing: <strong>${formatDate(start)}</strong> to <strong>${formatDate(firstBillingDate)}</strong> (prorated)</li>
                                <li>Subsequent billings: <strong>${billingDay}th of each month</strong></li>
                                <li>Example: Jan 20 Ã¢â€ â€™ Feb ${billingDay} Ã¢â€ â€™ Mar ${billingDay} Ã¢â€ â€™ Apr ${billingDay}</li>
                            </ul>
                        </div>
                    </div>
                `;
                break;
        }
        
        $('#billing_preview_content').html(previewHTML);
        $('#billing_preview_card').show();
    }
    
    // Helper function to add days to a date
    function addDays(date, days) {
        const result = new Date(date);
        result.setDate(result.getDate() + days);
        return result;
    }
    
    // Helper function to format date
    function formatDate(date) {
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return `${months[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
    }
    
    // Format number as Rupiah (without Rp symbol)
    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(Math.round(number));
    }

    // === CUSTOMER SEARCH INITIALIZATION (moved from third ready block) ===
    initCustomerSearch();
    
    // Auto-run disabled - enable manually in console if needed: testWorkflowComplete()
});

// Debounce utility function to prevent multiple rapid clicks
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Update total units display (billable + spare = total)
 * Global function accessible from anywhere
 */
function updateTotalUnitsDisplay() {
    const billableUnits = parseInt($('input[name="quantity"]').val()) || 0;
    const spareUnits = parseInt($('#spareQuantity').val()) || 0;
    const totalUnits = billableUnits + spareUnits;
    
    $('#billableUnitsDisplay').text(billableUnits);
    $('#spareUnitsDisplay').text(spareUnits);
    $('#totalUnitsDisplay').text(totalUnits);
}

let currentDateRange = { start: null, end: null };

function loadStatistics(startDate = null, endDate = null) {
    const params = {};
    if (startDate && endDate) {
        params.start_date = startDate;
        params.end_date = endDate;
    }
    
    $.ajax({
        url: '<?= base_url('marketing/quotations/stats') ?>',
        type: 'GET',
        data: params,
        success: function(data) {
            $('#stat-total-quotations').text(data.total || 0);
            $('#stat-pending').text(data.pending || 0);
            $('#stat-approved').text(data.approved || 0);
            $('#stat-rejected').text(data.rejected || 0);
        },
        error: function(xhr, status, error) {
            console.error('Ã¢ÂÅ’ Failed to load quotation statistics:', error);
            console.error('   Response:', xhr.responseText);
        }
    });
}

/**
 * Refresh action buttons in quotation detail modal
 * Called after adding/editing/deleting specifications to update buttons dynamically
 */
function refreshQuotationActions(quotationId) {
    if (!quotationId) return;
    
    $.get('<?= base_url('marketing/quotations/getQuotation/') ?>' + quotationId, function(response) {
        const data = response.data || response;
        
        if (!data.id_quotation) {
            console.error('Invalid quotation data in refreshQuotationActions:', data);
            return;
        }
        
        // Update spec counter in tab
        const specCount = parseInt(data.spec_count || 0);
        $('#specCountQuotation').text(specCount);
        
        // Rebuild action buttons with updated spec_count
        let actionButtons = '';
        const workflowStage = data.workflow_stage || 'PROSPECT';
        const quotationStage = data.stage || '';
        const hasSpecs = specCount > 0;
        
        // Show "Convert to Customer" button for DEAL quotations without customer conversion
        if (workflowStage === 'DEAL' && quotationStage === 'ACCEPTED' && !data.created_customer_id) {
            actionButtons += `<button class="btn btn-success me-2" onclick="convertProspectToCustomer(${data.id_quotation})" title="Convert prospect to permanent customer">
                <i class="fas fa-user-check me-1"></i>Convert to Customer
            </button>`;
        }
        
        // Show info if already converted
        if (data.created_customer_id) {
            actionButtons += `<span class="badge badge-soft-green me-2" title="Converted to customer on ${data.customer_converted_at || 'N/A'}">
                <i class="fas fa-check-circle me-1"></i>Customer Created
            </span>`;
        }
        
        // Always show edit button for quotations that haven't been converted to contract
        if (!data.contract_id) {
            actionButtons += `<button class="btn btn-warning btn-sm me-2" onclick="editQuotation(${data.id_quotation})" title="Tambahkan atau edit keterangan quotation">
                <i class="fas fa-pencil-alt me-1"></i>Edit Keterangan
            </button>`;
        }
        
        // Show delete button for non-contracted quotations
        if (!data.contract_id) {
            actionButtons += `<button class="btn btn-danger me-2" onclick="deleteQuotation(${data.id_quotation})" title="Delete this quotation">
                <i class="fas fa-trash me-1"></i>Delete
            </button>`;
        } else {
            // Show info that quotation is linked to contract
            actionButtons += `<small class="text-muted"><i class="fas fa-link me-1"></i>Linked to Contract #${data.contract_number || data.contract_id}</small>`;
        }
        
        // Show print button ONLY if specifications have been created
        if (hasSpecs) {
            actionButtons += `<button class="btn btn-outline-secondary btn-sm me-2" onclick="printQuotation(${data.id_quotation})" title="Print quotation with specifications">
                <i class="fas fa-print me-1"></i>Print
            </button>`;
        }
        
        // Show history button
        actionButtons += `<button class="btn btn-secondary me-2" onclick="viewQuotationHistory(${data.id_quotation})" title="Lihat riwayat perubahan quotation (revisi, approval, dll)">
            <i class="fas fa-history me-1"></i>History
        </button>`;
        
        // Update action buttons container
        $('#quotationActions').html(actionButtons);
        
        console.log(`Ã¢Å“â€¦ Action buttons refreshed for quotation #${quotationId} (spec_count: ${specCount})`);
    }).fail(function(xhr) {
        console.error('Failed to refresh quotation actions:', xhr.responseText);
    });
}

function viewQuotation(id) {
    // Set current quotation ID for specifications
    currentQuotationId = id;
    
    $.get('<?= base_url('marketing/quotations/getQuotation/') ?>' + id, function(response) {
        // Handle different response formats
        if (response.status === 'error') {
            console.error('API returned error:', response.message);
            $('#detailContent').html('<div class="alert alert-danger">Error: ' + response.message + '</div>');
            $('#quotationActions').html('');
            $('#detailModal').modal('show');
            return;
        }
        
        // Use the data property for detailed information
        const data = response.data || response;
        
        // Check if data is valid
        if (!data.id_quotation) {
            console.error('Invalid quotation data:', data);
            $('#detailContent').html('<div class="alert alert-danger">Invalid quotation data received</div>');
            $('#quotationActions').html('');
            $('#detailModal').modal('show');
            return;
        }
        
        // Update modal subtitle
        $('#quotationSubtitle').text((data.quotation_number || 'undefined') + ' - ' + (data.prospect_name || 'No Customer'));
        
        var content = `
            <div class="row">
                <div class="col-md-6">
                    <strong>Quotation Number:</strong><br>
                    <code class="fs-6">${data.quotation_number || 'undefined'}</code>
                    <span class="badge badge-soft-blue ms-2">v${data.version || 1}</span>
                    ${data.revision_status === 'REVISED' ? '<span class="badge badge-soft-orange ms-1"><i class="fas fa-exclamation-triangle me-1"></i>REVISED</span>' : ''}
                    <br><br>
                    <strong>Customer:</strong><br>
                    ${data.customer_name || data.prospect_name || 'undefined'}<br><br>
                    <strong>Amount:</strong><br>
                    <strong class="text-success">Rp ${data.total_amount ? parseFloat(data.total_amount).toLocaleString('id-ID') : '0'}</strong><br><br>
                    ${data.location_name ? `
                    <strong>Customer Location:</strong><br>
                    <div class="ms-2 p-2 bg-light rounded border">
                        <div class="mb-1"><i class="fas fa-map-marker-alt text-primary me-2"></i><strong>${data.location_name}</strong></div>
                        ${data.location_address ? `<div class="mb-1"><small class="text-muted">${data.location_address}</small></div>` : ''}
                        ${data.pic_name ? `<div class="mb-1"><small><i class="fas fa-user text-info me-1"></i><strong>PIC:</strong> ${data.pic_name}</small></div>` : ''}
                        ${data.pic_phone ? `<div><small><i class="fas fa-phone text-success me-1"></i>${data.pic_phone}</small></div>` : ''}
                    </div><br>
                    ` : ''}
                    
                </div>
                <div class="col-md-6">
                    <strong>Status:</strong><br>
                    <span class="badge ${
                        data.stage === 'ACCEPTED' ? 'badge-soft-green' : 
                        data.stage === 'SENT' ? 'badge-soft-yellow' : 
                        data.stage === 'REJECTED' ? 'badge-soft-red' : 
                        'badge-soft-gray'
                    }">${(data.stage || 'DRAFT').toUpperCase()}</span><br><br>
                    <strong>Valid Until:</strong><br>
                    ${data.valid_until || 'undefined'}<br><br>
                    <strong>Created:</strong><br>
                    ${data.created_at || 'undefined'}
                </div>
            </div>
            <hr>
            <strong>Description:</strong><br>
            ${data.quotation_description || 'undefined'}<br><br>
            ${data.notes ? '<strong>Notes:</strong><br>' + data.notes : ''}
        `;
        $('#detailContent').html(content);
        
        // Populate action buttons based on workflow stage
        let actionButtons = '';
        const workflowStage = data.workflow_stage || 'PROSPECT';
        const quotationStage = data.stage || '';
        const hasSpecs = parseInt(data.spec_count || 0) > 0; // Check if has specifications
        
        // Show "Convert to Customer" button for DEAL quotations without customer conversion
        if (workflowStage === 'DEAL' && quotationStage === 'ACCEPTED' && !data.created_customer_id) {
            actionButtons += `<button class="btn btn-success me-2" onclick="convertProspectToCustomer(${data.id_quotation})" title="Convert prospect to permanent customer">
                <i class="fas fa-user-check me-1"></i>Convert to Customer
            </button>`;
        }
        
        // Show info if already converted
        if (data.created_customer_id) {
            actionButtons += `<span class="badge badge-soft-green me-2" title="Converted to customer on ${data.customer_converted_at || 'N/A'}">
                <i class="fas fa-check-circle me-1"></i>Customer Created
            </span>`;
        }
        
        // Always show edit button for quotations that haven't been converted to contract
        // Allow editing even for DEAL stage to accommodate price/spec changes
        if (!data.contract_id) {
            actionButtons += `<button class="btn btn-warning btn-sm me-2" onclick="editQuotation(${data.id_quotation})" title="Tambahkan atau edit keterangan quotation">
                <i class="fas fa-pencil-alt me-1"></i>Edit Keterangan
            </button>`;
        }
        
        // Show delete button for non-contracted quotations
        // Once it has contract_id, deletion should be prevented
        if (!data.contract_id) {
            actionButtons += `<button class="btn btn-danger me-2" onclick="deleteQuotation(${data.id_quotation})" title="Delete this quotation">
                <i class="fas fa-trash me-1"></i>Delete
            </button>`;
        } else {
            // Show info that quotation is linked to contract
            actionButtons += `<small class="text-muted"><i class="fas fa-link me-1"></i>Linked to Contract #${data.contract_number || data.contract_id}</small>`;
        }
        
        // Show print button ONLY if specifications have been created
        if (hasSpecs) {
            actionButtons += `<button class="btn btn-outline-secondary btn-sm me-2" onclick="printQuotation(${data.id_quotation})" title="Print quotation with specifications">
                <i class="fas fa-print me-1"></i>Print
            </button>`;
        }
        
        // Show history button
        actionButtons += `<button class="btn btn-secondary me-2" onclick="viewQuotationHistory(${data.id_quotation})" title="Lihat riwayat perubahan quotation (revisi, approval, dll)">
            <i class="fas fa-history me-1"></i>History
        </button>`;
        
        $('#quotationActions').html(actionButtons);
        
        // Show/Hide SPK creation button and info based on workflow stage
        const spkButton = document.getElementById('createSpkFromQuotation');
        const spkInfo = document.getElementById('spkCreationInfo');
        
        if (workflowStage === 'DEAL') {
            if (spkButton) spkButton.style.display = 'inline-block';
            if (spkInfo) spkInfo.classList.remove('d-none');
        } else {
            if (spkButton) spkButton.style.display = 'none';
            if (spkInfo) spkInfo.classList.add('d-none');
        }
        
        // Store current quotation data for SPK creation
        window.currentQuotationForSPK = data;
        
        // Reset specifications tab to force reload
        $('#specifications-tab').removeClass('loaded');
        $('#spesifikasiListContract').html('<p class="text-muted">Click the Specifications tab to load data...</p>');
        
        $('#detailModal').modal('show');
        
        // Ensure tabs are properly initialized when modal is shown
        $('#detailModal').on('shown.bs.modal', function() {
            // Reset to first tab (Quotation Info) and ensure proper state
            $('#quotation-info-tab').tab('show');
            $('#quotation-info-tab').addClass('active').attr('aria-selected', 'true');
            $('#specifications-tab').removeClass('active').attr('aria-selected', 'false');
            $('#quotation-info-content').addClass('show active');
            $('#specifications-content').removeClass('show active');
        });
    }).fail(function(xhr) {
        console.error('AJAX Error details:', {
            status: xhr.status,
            statusText: xhr.statusText, 
            responseText: xhr.responseText,
            url: '<?= base_url('marketing/quotations/get-quotation/') ?>' + id
        });
        
        let errorMsg = 'Failed to load quotation details';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMsg = xhr.responseJSON.message;
        } else if (xhr.responseText) {
            try {
                const response = JSON.parse(xhr.responseText);
                errorMsg = response.message || errorMsg;
            } catch (e) {
                errorMsg += ' (Server error: ' + xhr.status + ')';
            }
        }
        
        // Show error in modal instead of SweetAlert
        $('#detailContent').html('<div class="alert alert-danger"><strong>Error:</strong> ' + errorMsg + '<br><small>Status: ' + xhr.status + ' - ' + xhr.statusText + '</small></div>');
        $('#quotationActions').html('');
        $('#detailModal').modal('show');
    });
}

function editQuotation(id) {
    // Fetch quotation data
    $.get('<?= base_url('marketing/quotations/getQuotation/') ?>' + id, function(response) {
        const data = response.data || response;
        
        // Simple form - only description editable, other fields readonly for reference
        var editForm = `
            <form id="editQuotationForm">
                <input type="hidden" name="id_quotation" value="${data.id_quotation}">
                <input type="hidden" name="total_amount" value="${data.total_amount || 0}">
                <input type="hidden" name="valid_until" value="${data.valid_until || ''}">
                
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Info:</strong> Hanya field <strong>Description</strong> yang dapat diedit. Amount dan data lain hanya untuk referensi.
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><strong>Quotation Number:</strong></label>
                            <input type="text" class="form-control bg-light" value="${data.quotation_number || ''}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Customer:</strong></label>
                            <input type="text" class="form-control bg-light" value="${data.customer_name || data.prospect_name || ''}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Amount:</strong></label>
                            <input type="text" class="form-control bg-light" value="Rp ${data.total_amount ? parseFloat(data.total_amount).toLocaleString('id-ID') : '0'}" disabled>
                            <small class="text-muted"><i class="fas fa-lock me-1"></i>Amount dihitung otomatis dari specifications</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><strong>Status:</strong></label>
                            <input type="text" class="form-control bg-light" value="${(data.stage || 'ERROR').toUpperCase()}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Valid Until:</strong></label>
                            <input type="date" class="form-control bg-light" value="${data.valid_until || ''}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Created:</strong></label>
                            <input type="text" class="form-control bg-light" value="${data.created_at || ''}" disabled>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="mb-3">
                    <label class="form-label"><strong>Description / Keterangan:</strong> <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="quotation_description" rows="7" required autofocus>${data.quotation_description || ''}</textarea>
                    <small class="text-muted"><i class="fas fa-edit me-1"></i>Isi keterangan quotation, terms, conditions, atau catatan khusus untuk customer</small>
                </div>
            </form>
        `;
        
        $('#detailContent').html(editForm);
        
        // Change action buttons to Save and Cancel
        var editActions = `
            <button class="btn btn-success me-2" onclick="saveQuotation(${data.id_quotation})" type="button">
                <i class="fas fa-save me-1"></i>Simpan Keterangan
            </button>
            <button class="btn btn-secondary" onclick="viewQuotation(${data.id_quotation})" type="button">
                <i class="fas fa-times me-1"></i>Batal
            </button>
        `;
        $('#quotationActions').html(editActions);
        
    }).fail(function() {
        OptimaUI.fire('Error', 'Failed to load quotation data', 'error');
    });
}

function saveQuotation(id) {
    // Validate form
    var form = document.getElementById('editQuotationForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Get form data
    var formData = new FormData(form);
    
    // Show loading
    OptimaUI.fire({
        title: 'Menyimpan...',
        text: 'Sedang menyimpan keterangan quotation',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Send AJAX request
    $.ajax({
        url: '<?= base_url('marketing/quotations/update/') ?>' + id,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            console.log('Update response:', response); // Debug
            if (response.status === 'success') {
                // Show appropriate message
                let message = 'Keterangan quotation berhasil disimpan';
                let icon = 'success';
                let title = 'Tersimpan!';
                
                if (response.is_revision) {
                    icon = 'info';
                    title = 'Quotation Direvisi!';
                    message = `Keterangan quotation diupdate dan ditandai sebagai REVISED (versi ${response.version})`;
                }
                
                OptimaUI.fire({
                    icon: icon,
                    title: title,
                    text: message,
                    timer: 3000,
                    showConfirmButton: true
                }).then(() => {
                    // Reload table
                    if (typeof quotationsTable !== 'undefined') {
                        quotationsTable.ajax.reload(null, false);
                    }
                    
                    // Reload detail view to show updated version badge
                    setTimeout(() => {
                        viewQuotation(id);
                    }, 500);
                });
            } else {
                OptimaUI.fire('Error', response.message || 'Gagal menyimpan keterangan', 'error');
            }
        },
        error: function(xhr) {
            var errorMsg = 'Gagal menyimpan keterangan quotation';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            OptimaUI.fire('Error', errorMsg, 'error');
        }
    });
}

function deleteQuotation(id) {
    OptimaUI.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('marketing/quotations/delete/') ?>' + id,
                type: 'DELETE',
                data: {
                    [window.csrfTokenName]: window.csrfToken || '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        OptimaUI.fire('Deleted!', response.message, 'success');
                        quotationsTable.ajax.reload();
                        loadStatistics();
                        
                        // Close detail modal if deleted item is currently being viewed
                        if (currentQuotationId == id && $('#detailModal').hasClass('show')) {
                            $('#detailModal').modal('hide');
                        }
                    } else {
                        OptimaUI.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    OptimaUI.fire('Error', 'Failed to delete quotation', 'error');
                }
            });
        }
    });
}

// Convert prospect to permanent customer
function convertProspectToCustomer(quotationId) {
    // Show loading indicator while fetching quotation details
    OptimaUI.fire({
        title: 'Loading...',
        text: 'Fetching quotation details',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Fetch quotation details first
    $.get('<?= base_url('marketing/quotations/getQuotation/') ?>' + quotationId, function(response) {
        const data = response.data || response;
        
        // Show confirmation dialog with prospect details
        OptimaUI.fire({
            title: 'Convert Prospect to Customer?',
            html: `
                <div class="text-start">
                    <p class="mb-3">This will create a permanent customer record from the following prospect:</p>
                    <div class="card">
                        <div class="card-body">
                            <p class="mb-2"><strong>Prospect Name:</strong><br>${data.prospect_name || 'N/A'}</p>
                            <p class="mb-2"><strong>Contact Person:</strong><br>${data.contact_person || 'N/A'}</p>
                            <p class="mb-2"><strong>Phone:</strong><br>${data.phone || 'N/A'}</p>
                            <p class="mb-0"><strong>Quotation:</strong><br>${data.quotation_number || 'N/A'}</p>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>A customer code will be auto-generated and the prospect will become a permanent customer in the system.</small>
                    </div>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-user-check me-1"></i> Yes, Convert to Customer',
            cancelButtonText: window.lang('cancel'),
            width: '600px',
            customClass: {
                popup: 'swal-wide'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show processing indicator
                OptimaUI.fire({
                    title: 'Processing...',
                    text: 'Creating customer record',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Send conversion request
                $.ajax({
                    url: '<?= base_url('marketing/convertProspectToCustomer/') ?>' + quotationId,
                    type: 'POST',
                    data: {
                        [window.csrfTokenName]: window.csrfToken || '<?= csrf_hash() ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            OptimaUI.fire({
                                icon: 'success',
                                title: 'Customer Created!',
                                html: `
                                    <p class="mb-2">${response.message}</p>
                                    ${response.customer_code ? `<p class="mb-0"><strong>Customer Code:</strong> <code>${response.customer_code}</code></p>` : ''}
                                `,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            
                            // Reload quotations table and refresh current quotation detail
                            quotationsTable.ajax.reload();
                            loadStatistics();
                            
                            // Refresh detail modal if it's open
                            if ($('#detailModal').hasClass('show') && currentQuotationId == quotationId) {
                                viewQuotation(quotationId);
                            }
                        } else {
                            OptimaUI.fire({
                                icon: 'error',
                                title: 'Conversion Failed',
                                text: response.message || 'Failed to convert prospect to customer'
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Failed to convert prospect to customer';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        
                        OptimaUI.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg
                        });
                    }
                });
            }
        });
    }).fail(function(xhr) {
        OptimaUI.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to load quotation details'
        });
    });
}

// Helper function for action type badges
function getActionBadge(actionType) {
    const badges = {
        'CREATED': '<span class="badge badge-soft-blue"><i class="fas fa-plus me-1"></i>CREATED</span>',
        'UPDATED': '<span class="badge badge-soft-yellow"><i class="fas fa-edit me-1"></i>UPDATED</span>',
        'REVISED': '<span class="badge badge-soft-orange"><i class="fas fa-exclamation-triangle me-1"></i>REVISED</span>',
        'SENT': '<span class="badge badge-soft-cyan"><i class="fas fa-paper-plane me-1"></i>SENT</span>',
        'ACCEPTED': '<span class="badge badge-soft-green"><i class="fas fa-check-circle me-1"></i>ACCEPTED</span>',
        'REJECTED': '<span class="badge badge-soft-red"><i class="fas fa-times-circle me-1"></i>REJECTED</span>',
        'DEAL': '<span class="badge badge-soft-purple"><i class="fas fa-handshake me-1"></i>DEAL</span>'
    };
    return badges[actionType.toUpperCase()] || `<span class="badge badge-soft-gray">${actionType}</span>`;
}

// Function to view quotation history
function viewQuotationHistory(id) {
    OptimaUI.fire({
        title: '<i class="fas fa-spinner fa-spin"></i> Loading History...',
        text: 'Fetching change history',
        allowOutsideClick: false,
        showConfirmButton: false
    });

    fetch(`<?= base_url('marketing/quotations/history/') ?>${id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(result => {
            if (result.success) {
                let historyHtml = '<div class="table-responsive" style="max-height: 500px; overflow-y: auto;">';
                
                if (result.data && result.data.length > 0) {
                    historyHtml += '<table class="table table-sm table-hover">';
                    historyHtml += '<thead class="table-light sticky-top">';
                    historyHtml += '<tr><th>Version</th><th>Action</th><th>Changed By</th><th>Date</th><th>Changes</th></tr>';
                    historyHtml += '</thead><tbody>';
                    
                    result.data.forEach(h => {
                        const actionBadge = getActionBadge(h.action_type);
                        
                        historyHtml += `<tr>
                            <td><span class="badge badge-soft-blue">v${h.version || 1}</span></td>
                            <td>${actionBadge}</td>
                            <td><small>${h.changed_by_name || h.changed_by_username || '-'}</small></td>
                            <td><small>${formatDateTime(h.changed_at)}</small></td>
                            <td><small class="text-muted">${h.changes_summary || 'No details'}</small></td>
                        </tr>`;
                    });
                    
                    historyHtml += '</tbody></table>';
                } else {
                    historyHtml += '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No history available</div>';
                }
                
                historyHtml += '</div>';
                
                OptimaUI.fire({
                    title: '<i class="fas fa-history"></i> Quotation History',
                    html: historyHtml,
                    width: '800px',
                    showCloseButton: true,
                    showConfirmButton: false,
                    customClass: {
                        container: 'history-modal'
                    }
                });
            } else {
                OptimaUI.fire({
                    icon: 'error',
                    title: 'Error',
                    text: result.message || 'Failed to load history'
                });
            }
        })
        .catch(error => {
            OptimaPro.hideLoading();
            console.error('History fetch error:', error);
            console.error('Error details:', error.message);
            OptimaUI.fire({
                icon: 'error',
                title: 'Failed to fetch history',
                html: `<p class="mb-0">${error.message || 'Unknown error'}</p>
                       <small class="text-muted">Check console for details</small>`,
                footer: 'Make sure you are logged in and have proper permissions'
            });
        });
}

// Helper function to format datetime
function formatDateTime(datetime) {
    if (!datetime) return '-';
    const d = new Date(datetime);
    return d.toLocaleString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Global quotation variables
let currentQuotationId = null;

// Enhanced tab switching with proper cleanup
$(document).on('shown.bs.tab', '#quotationDetailTabs button[data-bs-toggle="tab"]', function (e) {
    const targetTab = $(e.target).attr('data-bs-target');
    
    // Clear any lingering active states
    $('#quotationDetailTabs .nav-link').removeClass('active').attr('aria-selected', 'false');
    $('.tab-pane').removeClass('show active');
    
    // Set correct active state for current tab
    $(e.target).addClass('active').attr('aria-selected', 'true');
    $(targetTab).addClass('show active');
});

// Event handler for specifications tab click
$(document).on('click', '#specifications-tab', function() {
    if (!currentQuotationId) {
        return;
    }
    
    // Check if tab is already loaded
    if ($(this).hasClass('loaded')) {
        return;
    }
    
    // Check quotation workflow stage before loading specifications
    $.get('<?= base_url('marketing/quotations/get-quotation/') ?>' + currentQuotationId, function(response) {
        
        // Handle error responses
        if (response.status === 'error') {
            const container = document.getElementById('spesifikasiListContract');
            container.innerHTML = `<div class="alert alert-danger">Error: ${response.message}</div>`;
            return;
        }
        
        // Use the data property for detailed information
        const quotation = response.data || response;
        
        if (quotation.id_quotation) {
            // Check workflow stage
            if (quotation.workflow_stage === 'PROSPECT') {
                // Show specifications tab but with convert message
                const container = document.getElementById('spesifikasiListContract');
                container.innerHTML = `
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-arrow-circle-up fa-3x text-info"></i>
                        </div>
                        <h5 class="text-info">Convert to Quotation First</h5>
                        <p class="text-muted mb-4">
                            This is currently a prospect. Convert it to quotation to add specifications<br>
                            and define required units, quantities, and pricing.
                        </p>
                        <button class="btn btn-primary btn-lg" onclick="convertToQuotation(${currentQuotationId})">
                            <i class="fas fa-arrow-right me-2"></i>Convert to Quotation
                        </button>
                    </div>
                `;
                $('#specifications-tab').addClass('loaded');
                return;
            }
            
            if (['DEAL', 'NOT_DEAL'].includes(quotation.workflow_stage)) {
                // Load specifications but in read-only mode
                loadQuotationSpecifications(currentQuotationId);
                $('#specifications-tab').addClass('loaded');
                
                // Hide add specification buttons
                $('.btn-primary:contains("Tambah Unit")').hide();
                $('.btn-success:contains("Tambah Attachment")').hide();
                return;
            }
            
            // For QUOTATION and SENT stages, load normally
            loadQuotationSpecifications(currentQuotationId);
            $('#specifications-tab').addClass('loaded');
        } else {
            console.error('Failed to load quotation details:', response);
            const container = document.getElementById('spesifikasiListContract');
            container.innerHTML = `<div class="alert alert-danger">Failed to load quotation details: ${response.message || 'Unknown error'}</div>`;
        }
    }).fail(function(xhr) {
        console.error('AJAX error loading quotation for specifications:', xhr);
        const container = document.getElementById('spesifikasiListContract');
        let errorMsg = 'Network error';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMsg = xhr.responseJSON.message;
        }
        container.innerHTML = `<div class="alert alert-danger">Failed to load quotation details: ${errorMsg}</div>`;
    });
});

// Load quotation specifications
function loadQuotationSpecifications(quotationId) {
    const container = document.getElementById('spesifikasiListContract');
    if (!container) {
        console.error('spesifikasiListContract container not found!');
        return;
    }
    
    container.innerHTML = '<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Loading specifications...</div>';
    
    // Add cache-busting timestamp to ensure fresh data from database
    const timestamp = new Date().getTime();
    fetch(`<?= base_url('marketing/quotations/get-specifications/') ?>${quotationId}?_=${timestamp}`, {
        cache: 'no-store',
        headers: {
            'Cache-Control': 'no-cache, no-store, must-revalidate',
            'Pragma': 'no-cache'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(response => {
            if (!response.success) {
                throw new Error(response.message || 'Failed to load specifications');
            }
            
            const specifications = response.data || [];
            const summary = response.summary || {};
            
            // Debug: Log API response to check if spare_quantity and operator fields are present
            console.log('Ã°Å¸â€Â API Response:', {
                success: response.success,
                totalSpecs: specifications.length,
                firstSpec: specifications[0] ? {
                    id: specifications[0].id_specification,
                    name: specifications[0].specification_name,
                    quantity: specifications[0].quantity,
spare_quantity: specifications[0].spare_quantity,
                    include_operator: specifications[0].include_operator,
                    operator_quantity: specifications[0].operator_quantity,
                    allKeys: Object.keys(specifications[0])
                } : null
            });
            
            // Update tab counter
            $('#specCountQuotation').text(specifications.length);
            
            if (specifications.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-clipboard-list fa-3x text-muted"></i>
                        </div>
                        <h5 class="text-muted">No Specifications Yet</h5>
                        <p class="text-muted mb-4">
                            This quotation doesn't have any specifications yet.<br>
                            Add specifications to define the required units, quantities, and pricing.
                        </p>
                        <button class="btn btn-primary btn-lg" onclick="openAddSpecificationModal()">
                            <i class="fas fa-plus me-2"></i>Add First Specification
                        </button>
                    </div>
                `;
                return;
            }
            
            displayQuotationSpecifications(specifications);
        })
        .catch(error => {
            console.error('Error loading specifications:', error);
            container.innerHTML = `<div class="alert alert-danger">Error loading specifications: ${error.message}</div>`;
        });
}

// Display quotation specifications
function displayQuotationSpecifications(specifications) {
    const container = document.getElementById('spesifikasiListContract');
    
    console.log('Ã°Å¸â€œâ€¹ displayQuotationSpecifications called with:', specifications.length, 'specs');
    
    let html = '';
    specifications.forEach((spec, index) => {
        // Debug: Log each spec to check spare_quantity and include_operator data
        console.log(`Spec #${index + 1}:`, {
            id: spec.id_specification,
            name: spec.specification_name,
            quantity: spec.quantity,
            spare_quantity: spec.spare_quantity,
            include_operator: spec.include_operator,
            operator_quantity: spec.operator_quantity
        });
        
        // Determine specification type
        const specType = spec.specification_type || 'UNIT';
        const isAttachment = specType === 'ATTACHMENT';
        const cardClass = isAttachment ? 'border-success' : 'border-primary';
        const badgeClass = isAttachment ? 'bg-success' : 'bg-primary';
        
        // Build specification details
        const details = [];
        
        // Department and Unit Type
        if (spec.nama_departemen) {
            details.push(`<div class="col-md-3"><small class="text-muted">Department</small><div class="fw-bold">${spec.nama_departemen}</div></div>`);
        }
        
        if (spec.jenis_tipe_unit) {
            details.push(`<div class="col-md-3"><small class="text-muted">Unit Type</small><div class="fw-bold">${spec.jenis_tipe_unit}</div></div>`);
        } else if (spec.nama_tipe_unit) {
            details.push(`<div class="col-md-3"><small class="text-muted">Unit Type</small><div class="fw-bold">${spec.nama_tipe_unit}</div></div>`);
        }
        
        // Capacity
        if (spec.nama_kapasitas) {
            details.push(`<div class="col-md-3"><small class="text-muted">Capacity</small><div class="fw-bold">${spec.nama_kapasitas}</div></div>`);
        }
        
        // Quantity and Spare Unit Badge
        let quantityDisplay = '';
        // CRITICAL: Convert to numbers to avoid string concatenation (10 + 1 = "101" bug)
        const quantity = parseInt(spec.quantity) || 0;
        const spareQuantity = parseInt(spec.spare_quantity) || 0;
        const totalUnits = quantity + spareQuantity;  // Now: 10 + 1 = 11 Ã¢Å“â€œ
        
        // Debug: log spare quantity data
        if (spareQuantity > 0 || spec.include_operator == 1) {
            console.log('Ã°Å¸â€œÂ¦ Spec has spare/operator:', {
                spec_id: spec.id_specification,
                quantity: quantity,
                spare_quantity: spareQuantity,
                include_operator: spec.include_operator,
                operator_quantity: spec.operator_quantity
            });
        }
        
        if (spec.is_spare_unit == 1) {
            // Legacy spare unit (all units are spare)
            quantityDisplay = `
                <div class="col-md-4">
                    <small class="text-muted">Quantity</small>
                    <div class="fw-bold text-primary">
                        ${quantity} unit(s)
                        <span class="badge badge-soft-yellow ms-2">
                            <i class="fas fa-box-open"></i> SPARE UNIT
                        </span>
                    </div>
                </div>`;
        } else if (spareQuantity > 0) {
            // New spare quantity model (billable + spare)
            quantityDisplay = `
                <div class="col-md-6">
                    <small class="text-muted">Quantity</small>
                    <div class="fw-bold text-primary mb-1">
                        ${quantity} billable + ${spareQuantity} spare = ${totalUnits} total delivered
                    </div>
                    <div>
                        <span class="badge badge-soft-green me-1">
                            <i class="fas fa-file-invoice-dollar"></i> ${quantity} Billed
                        </span>
                        <span class="badge badge-soft-yellow">
                            <i class="fas fa-gift"></i> ${spareQuantity} Spare - Not Billed
                        </span>
                    </div>
                </div>`;
        } else {
            // Regular units (no spare)
            quantityDisplay = `
                <div class="col-md-3">
                    <small class="text-muted">Quantity</small>
                    <div class="fw-bold text-primary">${quantity} unit(s)</div>
                </div>`;
        }
        
        details.push(quantityDisplay);
        
        // Operator Information Badge
        if (spec.include_operator && spec.include_operator == 1) {
            const operatorQty = spec.operator_quantity || 1;
            const operatorMonthly = spec.operator_monthly_rate || 0;
            const operatorDaily = spec.operator_daily_rate || 0;
            
            let operatorPriceInfo = '';
            if (operatorMonthly > 0) {
                operatorPriceInfo = `Rp ${formatNumber(operatorMonthly)}/month`;
            }
            if (operatorDaily > 0) {
                operatorPriceInfo += (operatorPriceInfo ? ' + ' : '') + `Rp ${formatNumber(operatorDaily)}/day`;
            }
            
            const operatorInfo = `
                <div class="col-md-6">
                    <small class="text-muted">Operator Service</small>
                    <div>
                        <span class="badge badge-soft-cyan">
                            <i class="fas fa-user-tie"></i> WITH OPERATOR (${operatorQty} person${operatorQty > 1 ? 's' : ''})
                        </span>
                        ${operatorPriceInfo ? `<span class="text-muted ms-2">${operatorPriceInfo}</span>` : ''}
                    </div>
                </div>`;
            
            details.push(operatorInfo);
        }
        
        // Pricing (skip for spare units)
        const monthlyPrice = spec.monthly_price || spec.unit_price || spec.harga_per_unit || 0;
        const dailyPrice = spec.daily_price || spec.harga_per_unit_harian || 0;
        const totalPrice = spec.total_price || (monthlyPrice * (spec.quantity || 0));
        
        if (spec.is_spare_unit != 1) {
            // Only show prices for non-spare units
            if (monthlyPrice > 0) {
                details.push(`<div class="col-md-3"><small class="text-muted">Monthly Price/Unit</small><div class="fw-bold text-success">Rp ${formatNumber(monthlyPrice)}</div></div>`);
            }
            
            if (dailyPrice > 0) {
                details.push(`<div class="col-md-3"><small class="text-muted">Daily Price/Unit</small><div class="fw-bold text-info">Rp ${formatNumber(dailyPrice)}</div></div>`);
            }
            
            details.push(`<div class="col-md-3"><small class="text-muted">Total Price</small><div class="fw-bold text-primary">Rp ${formatNumber(totalPrice)}</div></div>`);
        } else {
            // Spare unit - show NO CHARGE indicator
            details.push(`<div class="col-md-6"><small class="text-muted">Billing Status</small><div class="fw-bold text-warning"><i class="fas fa-gift me-1"></i>TIDAK DITAGIH (No Charge)</div></div>`);
        }
        
        // Brand and Model
        if (spec.merk_unit) {
            details.push(`<div class="col-md-3"><small class="text-muted">Brand</small><div>${spec.merk_unit}</div></div>`);
        }
        
        if (spec.model_unit) {
            details.push(`<div class="col-md-3"><small class="text-muted">Model</small><div>${spec.model_unit}</div></div>`);
        }
        
        // Electric Specific - Battery and Charger
        if (spec.jenis_baterai) {
            details.push(`<div class="col-md-3"><small class="text-muted">Battery Type</small><div><i class="fas fa-battery-full text-warning me-1"></i>${spec.jenis_baterai}</div></div>`);
        }
        
        // Charger - check for various field combinations
        let chargerInfo = '';
        if (spec.merk_charger && spec.tipe_charger) {
            chargerInfo = `${spec.merk_charger} - ${spec.tipe_charger}`;
        } else if (spec.charger_id && spec.charger_id > 0) {
            chargerInfo = `Charger ID: ${spec.charger_id}`;
        }
        
        if (chargerInfo) {
            details.push(`<div class="col-md-4"><small class="text-muted">Charger</small><div><i class="fas fa-charging-station text-success me-1"></i>${chargerInfo}</div></div>`);
        }
        
        // Technical Specifications
        const techSpecs = [];
        if (spec.valve_name) {
            techSpecs.push(`<span class="chip chip-gray me-1"><i class="fas fa-cog me-1"></i>Valve: ${spec.valve_name}</span>`);
        }
        if (spec.mast_name) {
            techSpecs.push(`<span class="chip chip-gray me-1"><i class="fas fa-arrows-alt-v me-1"></i>Mast: ${spec.mast_name}</span>`);
        }
        if (spec.tire_name) {
            techSpecs.push(`<span class="chip chip-gray me-1"><i class="fas fa-circle me-1"></i>Tire: ${spec.tire_name}</span>`);
        }
        if (spec.wheel_name) {
            techSpecs.push(`<span class="chip chip-gray me-1"><i class="fas fa-circle-notch me-1"></i>Wheel: ${spec.wheel_name}</span>`);
        }
        
        // Attachment Information
        if (spec.attachment_tipe) {
            details.push(`<div class="col-md-4"><small class="text-muted">Attachment Type</small><div><i class="fas fa-tools text-success me-1"></i>${spec.attachment_tipe}</div></div>`);
        }
        if (spec.attachment_merk) {
            details.push(`<div class="col-md-4"><small class="text-muted">Attachment Brand</small><div>${spec.attachment_merk}</div></div>`);
        }
        
        // Accessories
        let accessoriesBadges = '';
        if (spec.unit_accessories && spec.unit_accessories.trim() !== '') {
            const accessories = spec.unit_accessories.split(',').map(a => a.trim());
            accessoriesBadges = accessories.map(acc =>
                `<span class="badge badge-soft-blue me-1"><i class="fas fa-plus-circle me-1"></i>${formatAccessoryLabel(acc)}</span>`
            ).join('');
        }
        
        html += `
            <div class="card mb-3 ${cardClass}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge ${badgeClass} me-2">SPEC-${index + 1}</span>
                        <span class="chip chip-gray me-2">${specType}</span>
                        <strong>${spec.specification_name || 'Specification ' + (index + 1)}</strong>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="editSpecification(${spec.id_specification})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteSpecification(${spec.id_specification})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        ${details.join('')}
                    </div>
                    
                    ${techSpecs.length > 0 ? `
                    <div class="mt-3">
                        <small class="text-muted d-block mb-2">Technical Specifications:</small>
                        ${techSpecs.join('')}
                    </div>
                    ` : ''}
                    
                    ${accessoriesBadges ? `
                    <div class="mt-3">
                        <small class="text-muted d-block mb-2">Unit Accessories:</small>
                        ${accessoriesBadges}
                    </div>
                    ` : ''}
                    
                    ${spec.specification_description && spec.specification_description.trim() !== '' ? `
                    <div class="mt-3">
                        <small class="text-muted d-block mb-1">Description:</small>
                        <div class="text-muted">${spec.specification_description}</div>
                    </div>
                    ` : ''}
                    
                    ${spec.notes && spec.notes.trim() !== '' ? `
                    <div class="mt-3">
                        <div class="alert alert-info mb-0">
                            <small class="text-muted d-block mb-1"><i class="fas fa-sticky-note me-2"></i><strong>Custom Requirements / Notes:</strong></small>
                            <div style="white-space: pre-line;">${spec.notes}</div>
                        </div>
                    </div>
                    ` : ''}
                </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Open Add Specification Modal
function openAddSpecificationModal() {
    if (!currentQuotationId) {
        OptimaUI.fire('Warning', 'Please select a quotation first', 'warning');
        return;
    }
    
    // Check quotation workflow stage before allowing specifications
    $.get('<?= base_url('marketing/quotations/get-quotation/') ?>' + currentQuotationId, function(response) {
        // Handle error responses
        if (response.status === 'error') {
            OptimaUI.fire('Error', 'Failed to load quotation: ' + response.message, 'error');
            return;
        }
        
        // Use the data property for detailed information
        const quotation = response.data || response;
        
        if (quotation) {
            // Check workflow stage
            if (quotation.workflow_stage === 'PROSPECT') {
                OptimaUI.fire({
                    title: 'Convert to Quotation First',
                    text: 'This prospect must be converted to quotation before adding specifications.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Convert Now',
                    cancelButtonText: window.lang('cancel')
                }).then((result) => {
                    if (result.isConfirmed) {
                        convertToQuotation(currentQuotationId);
                    }
                });
                return;
            }
            
            if (!['QUOTATION', 'SENT'].includes(quotation.workflow_stage)) {
                OptimaUI.fire('Warning', 'Specifications can only be added to quotations in QUOTATION or SENT stage.', 'warning');
                return;
            }
            
            // If stage is valid, proceed to open specification modal
            proceedWithSpecificationModal();
        } else {
            OptimaUI.fire('Error', 'Failed to load quotation details', 'error');
        }
    }).fail(function() {
        OptimaUI.fire('Error', 'Failed to load quotation details', 'error');
    });
}

function proceedWithSpecificationModal() {
    // Reset form to Add mode
    $('#addSpecificationForm')[0].reset();
    $('#specQuotationId').val(currentQuotationId);
    $('#specId').val(''); // Clear spec ID for add mode
    $('#specType').val('UNIT'); // Set specification type to UNIT
    
    // Reset spare unit checkbox and enable price fields
    $('#isSpareUnit').prop('checked', false).trigger('change');
    
    // Reset modal to Add mode
    $('#specModalHeader').removeClass('bg-primary').addClass('bg-success');
    $('#specModalTitle').html('<i class="fas fa-cogs me-2"></i>Add Unit Specification');
    $('#submitBtnText').text('Save Specification');
    $('#submitSpecificationBtn').removeClass('btn-primary').addClass('btn-success');
    
    // NUCLEAR OPTION: Force modal size to 1800px (HARDCODED)
    // Ã¢Å“â€¦ Modal size controlled by CSS (modal-xl = 1140px)
    // No JavaScript forcing needed
    
    // Load dropdown data
    loadDepartemenForSpecification();
    loadTipeUnitForSpecification(); // This will load data but not populate options until dept is selected
    loadKapasitasForSpecification();
    loadAttachmentTypesForSpecification();
    loadForkTypesForSpecification();
    loadValvesForSpecification();
    loadMastModelsForSpecification();
    loadTiresForSpecification();
    loadWheelsForSpecification();

    // Reset fork/attachment toggle
    $('input[name="fork_attach_type"][value="none"]').prop('checked', true);
    $('#specForkSection, #specAttachSection').hide();
    $('#specForkId').val('');
    $('#specAttachmentTipe').val('');

    // Reset fork/attachment toggle on open
    $('input[name="fork_attach_type"][value="none"]').prop('checked', true);
    $('#specForkSection, #specAttachSection').hide();
    $('#specForkId').val('');
    $('#specAttachmentTipe').val('');

    // Unit Brand must follow selected department to prevent cross-department selection
    $('#specMerkUnit')
        .prop('disabled', true)
        .html('<option value="">-- Select Department First --</option>');
    
    // Initialize battery and charger as disabled
    $('#specJenisBaterai, #specCharger').prop('disabled', true);
    $('#specJenisBaterai').html('<option value="">-- Select Battery --</option>');
    $('#specCharger').html('<option value="">-- Select Charger --</option>');
    
    $('#addSpecificationModal').modal('show');
}

// Open add attachment modal
function openAddAttachmentModal() {
    if (!currentQuotationId) {
        OptimaUI.fire('Warning', 'Please select a quotation first', 'warning');
        return;
    }
    
    // Reset form
    $('#addAttachmentForm')[0].reset();
    $('#attachmentSpecType').val('ATTACHMENT'); // Set specification type to ATTACHMENT
    $('#attachmentQuotationId').val(currentQuotationId);
    
    // Load attachment types
    loadAttachmentTypesForAttachment();
    
    // Show modal
    $('#addAttachmentModal').modal('show');
    
    // Focus on quantity after modal is shown
    setTimeout(() => {
        $('#addAttachmentForm [name="quantity"]').focus();
    }, 300);
}

// Department change handler - handle electric/non-electric filtering
$(document).on('change', '#specDepartemen', function() {
    const selectedDept = $(this).val();
    const selectedDeptText = $(this).find('option:selected').text().toLowerCase();
    
    // Check if selected department is electric
    const isElectric = selectedDeptText.includes('electric') || selectedDeptText.includes('listrik');
    
    // Handle battery and charger field visibility/state
    if (isElectric) {
        // Enable and load data for electric department
        $('#specJenisBaterai, #specCharger').prop('disabled', false);
        loadBatteriesForSpecification();
        loadChargersForSpecification();
    } else {
        // Disable and clear for non-electric departments
        $('#specJenisBaterai, #specCharger').prop('disabled', true);
        $('#specJenisBaterai').html('<option value="">Hanya tersedia untuk unit Electric</option>');
        $('#specCharger').html('<option value="">Hanya tersedia untuk unit Electric</option>');
    }
    
    // Update Unit Type options based on selected department
    updateTipeUnitOptions();

    // Unit Brand is strictly department-based
    if (selectedDept) {
        $('#specMerkUnit').prop('disabled', false);
        loadUnitBrandsForSpecification();
    } else {
        $('#specMerkUnit')
            .val('')
            .prop('disabled', true)
            .html('<option value="">-- Select Department First --</option>');
    }
});

$(document).on('change', '#specMastModel', function() {
    loadMastHeightsForSpecification($(this).val());
});

// Unit Type change handler - handle cascading to other components
$(document).on('change', '#specTipeUnit', function() {
    const selectedTipeUnit = $(this).val();
    const selectedText = $(this).find('option:selected').text();
    
    if (selectedTipeUnit) {
        // Filter kapasitas based on unit type if needed
        // For now we load all kapasitas, but this can be enhanced
        loadKapasitasForSpecification();
        
        // Load other components that may depend on unit type
        loadUnitBrandsForSpecification();
        loadAttachmentTypesForSpecification();
    } else {
        // Clear dependent dropdowns
        $('#specKapasitas').html('<option value="">-- Select Capacity --</option>');
    }
});

// Functions to load dropdown data - consistent with kontrak spesifikasi pattern
function loadDepartemenForSpecification() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=departemen', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Department --</option>';
            response.data.forEach(dept => {
                options += `<option value="${dept.id}">${dept.name}</option>`;
            });
            $('#specDepartemen').html(options);
        } else {
            console.error('Departemen API error:', response.message);
            $('#specDepartemen').html('<option value="">Error: ' + (response.message || 'Unknown error') + '</option>');
        }
    }).fail(function(xhr) {
        console.error('Failed to load departments:', {
            status: xhr.status,
            statusText: xhr.statusText,
            responseText: xhr.responseText
        });
        $('#specDepartemen').html('<option value="">Error loading departments</option>');
    });
}

function loadTipeUnitForSpecification() {
    return $.ajax({
        url: '<?= base_url('marketing/customer-management/getTipeUnit') ?>',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                // Store all unit type data globally for filtering
                window.allTipeUnitData = response.data;
                
                // Initially show placeholder only
                $('#specTipeUnit').html('<option value="">-- Pilih Tipe Unit --</option>');
            } else {
                console.error('API returned error:', response.message);
                $('#specTipeUnit').html('<option value="">Error: ' + (response.message || 'Unknown error') + '</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading tipe unit:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            $('#specTipeUnit').html('<option value="">Error loading unit types</option>');
        }
    });
}

// Function to update Unit Type options based on selected department
function updateTipeUnitOptions() {
    const selectedDept = $('#specDepartemen').val();
    const selectedDeptText = $('#specDepartemen option:selected').text();
    const select = $('#specTipeUnit');
    
    select.empty().append('<option value="">-- Pilih Tipe Unit --</option>');
    
    if (!selectedDept || !window.allTipeUnitData) {
        if (!window.allTipeUnitData) {
            // Try to load tipe unit data if it's not loaded
            loadTipeUnitForSpecification();
        }
        return;
    }
    
    // Filter and show only units for selected department
    const filteredUnits = window.allTipeUnitData.filter(unit => {
        return unit.id_departemen == selectedDept;
    });
    
    if (filteredUnits.length === 0) {
        select.append('<option value="">No unit types available for this department</option>');
        return;
    }
    
    // Group by jenis to avoid duplicates
    const uniqueJenis = [...new Set(filteredUnits.map(unit => unit.jenis))];
    
    uniqueJenis.sort().forEach(jenis => {
        // Find the first unit with this jenis to get the id
        const unitWithJenis = filteredUnits.find(unit => unit.jenis === jenis);
        if (unitWithJenis) {
            select.append(`<option value="${unitWithJenis.id_tipe_unit}" data-dept="${selectedDept}">${jenis}</option>`);
        }
    });
    
    // Also clear dependent dropdowns when unit type changes
    $('#specKapasitas').html('<option value="">-- Select Capacity --</option>');
}

function loadKapasitasForSpecification() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=kapasitas', function(response) {
        if (response.success) {
            // Sort capacity data by numeric value (from smallest to largest)
            const sortedData = response.data.sort((a, b) => {
                // Extract numeric value from capacity name (e.g., "1.5 TON" -> 1.5)
                const numA = parseFloat(a.name.replace(/[^\d.]/g, '')) || 0;
                const numB = parseFloat(b.name.replace(/[^\d.]/g, '')) || 0;
                return numA - numB;
            });
            
            let options = '<option value="">-- Select Capacity --</option>';
            sortedData.forEach(cap => {
                options += `<option value="${cap.id}">${cap.name}</option>`;
            });
            $('#specKapasitas').html(options);
        } else {
            console.error('Kapasitas API error:', response.message);
            $('#specKapasitas').html('<option value="">Error: ' + (response.message || 'Unknown error') + '</option>');
        }
    }).fail(function(xhr) {
        console.error('Failed to load capacities:', {
            status: xhr.status,
            statusText: xhr.statusText,
            responseText: xhr.responseText
        });
        $('#specKapasitas').html('<option value="">Error loading capacities</option>');
    });
}

function loadChargersForSpecification() {
    const selectedDeptId = $('#specDepartemen').val();
    const selectedDeptText = $('#specDepartemen option:selected').text().toLowerCase();
    const isElectric = selectedDeptText.includes('electric') || selectedDeptText.includes('listrik');
    
    if (!isElectric || !selectedDeptId) {
        $('#specCharger').html('<option value="">Hanya tersedia untuk unit Electric</option>');
        return Promise.resolve();
    }
    
    return $.get(`<?= base_url('marketing/spk/spec-options') ?>?type=charger&departemen_id=${selectedDeptId}`, function(response) {
        if (response.success) {
            let options = '<option value="">-- Pilih Charger --</option>';
            response.data.forEach(charger => {
                options += `<option value="${charger.id}">${charger.name}</option>`;
            });
            $('#specCharger').html(options);
        }
    }).fail(function(xhr) {
        console.error('Failed to load chargers:', xhr.responseText);
        $('#specCharger').html('<option value="">Error loading chargers</option>');
    });
}

function loadUnitBrandsForSpecification() {
    const selectedDeptId = $('#specDepartemen').val();

    if (!selectedDeptId) {
        $('#specMerkUnit')
            .val('')
            .prop('disabled', true)
            .html('<option value="">-- Select Department First --</option>');
        return Promise.resolve();
    }

    let endpoint = '<?= base_url('marketing/spk/spec-options') ?>?type=merk_unit';
    endpoint += `&departemen_id=${encodeURIComponent(selectedDeptId)}`;

    $('#specMerkUnit')
        .prop('disabled', false)
        .html('<option value="">Loading brands...</option>');

    return $.get(endpoint, function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Brand --</option>';
            if (response.data.length === 0) {
                options = '<option value="">No brands available for selected department</option>';
            } else {
                response.data.forEach(brand => {
                    // Backend returns {id: model_unit_id, name: "Brand - Model"}
                    options += `<option value="${brand.id}">${brand.name}</option>`;
                });
            }
            $('#specMerkUnit').html(options);
        }
    }).fail(function(xhr) {
        console.error('Ã¢ÂÅ’ Failed to load unit brands:', xhr.responseText);
        $('#specMerkUnit').html('<option value="">Error loading brands</option>');
    });
}

function loadBatteriesForSpecification() {
    const selectedDeptId = $('#specDepartemen').val();
    const selectedDeptText = $('#specDepartemen option:selected').text().toLowerCase();
    const isElectric = selectedDeptText.includes('electric') || selectedDeptText.includes('listrik');
    
    if (!isElectric || !selectedDeptId) {
        $('#specJenisBaterai').html('<option value="">Hanya tersedia untuk unit Electric</option>');
        return Promise.resolve();
    }
    
    return $.get(`<?= base_url('marketing/spk/spec-options') ?>?type=jenis_baterai&departemen_id=${selectedDeptId}`, function(response) {
        if (response.success) {
            let options = '<option value="">-- Pilih Baterai --</option>';
            response.data.forEach(battery => {
                // Backend returns {id: battery_id, name: "Brand - Type (Jenis)"}
                options += `<option value="${battery.id}">${battery.name}</option>`;
            });
            $('#specJenisBaterai').html(options);
        }
    }).fail(function(xhr) {
        console.error('Ã¢ÂÅ’ Failed to load batteries:', xhr.responseText);
        $('#specJenisBaterai').html('<option value="">Error loading batteries</option>');
    });
}

function loadForkTypesForSpecification() {
    return $.get('<?= base_url('marketing/forks') ?>', function(response) {
        let options = '<option value="">-- Pilih Jenis Fork --</option>';
        if (response.success && response.data) {
            response.data.forEach(f => {
                options += `<option value="${f.id}">${f.name}</option>`;
            });
        }
        $('#specForkId').html(options);
    }).fail(function() {
        $('#specForkId').html('<option value="">Error loading forks</option>');
    });
}

// Toggle Fork/Attachment visibility
$(document).on('change', 'input[name="fork_attach_type"]', function() {
    const val = $(this).val();
    $('#specForkSection').toggle(val === 'fork');
    $('#specAttachSection').toggle(val === 'attachment');
    // Clear values when switching
    if (val !== 'fork') $('#specForkId').val('');
    if (val !== 'attachment') $('#specAttachmentTipe').val('');
});

function loadAttachmentTypesForSpecification() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=attachment_tipe', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Attachment Type --</option>';
            response.data.forEach(att => {
                // Backend returns {id: attachment_id, name: "Tipe - Brand Model"}
                options += `<option value="${att.id}">${att.name}</option>`;
            });
            $('#specAttachmentTipe').html(options);
        }
    }).fail(function(xhr) {
        console.error('Failed to load attachment types:', xhr.responseText);
        $('#specAttachmentTipe').html('<option value="">Error loading attachments</option>');
    });
}

function loadAttachmentTypesForAttachment() {
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=attachment_tipe', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Attachment Type --</option>';
            response.data.forEach(att => {
                // Backend returns DISTINCT values with {id: name, name: name}
                options += `<option value="${att.name}">${att.name}</option>`;
            });
            $('#attachmentTipe').html(options);
        }
    }).fail(function(xhr) {
        console.error('Failed to load attachment types:', xhr.responseText);
        $('#attachmentTipe').html('<option value="">Error loading attachments</option>');
    });
}

function loadValvesForSpecification() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=valve', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Valve --</option>';
            response.data.forEach(valve => {
                options += `<option value="${valve.id}">${valve.name}</option>`;
            });
            $('#specValve').html(options);
        }
    }).fail(function(xhr) {
        console.error('Failed to load valves:', xhr.responseText);
        $('#specValve').html('<option value="">Error loading valves</option>');
    });
}

function loadMastModelsForSpecification() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=mast_model', function(response) {
        if (response.success) {
            let options = '<option value="">-- Pilih Model Mast --</option>';
            response.data.forEach(mast => {
                options += `<option value="${mast.id}">${mast.name}</option>`;
            });
            $('#specMastModel').html(options);
            $('#specMastHeight').html('<option value="">Pilih model mast terlebih dahulu</option>');
        }
    }).fail(function(xhr) {
        console.error('Failed to load mast models:', xhr.responseText);
        $('#specMastModel').html('<option value="">Error loading mast models</option>');
        $('#specMastHeight').html('<option value="">Error loading mast heights</option>');
    });
}

function loadMastHeightsForSpecification(mastModelId, selectedMastId = '') {
    const modelName = $('#specMastModel option:selected').text();
    if (!mastModelId || !modelName || modelName.includes('Pilih')) {
        $('#specMastHeight').html('<option value="">Pilih model mast terlebih dahulu</option>');
        return Promise.resolve();
    }

    return $.get(`<?= base_url('marketing/spk/spec-options') ?>?type=mast_height&mast_model=${encodeURIComponent(modelName)}`, function(response) {
        if (response.success) {
            let options = '<option value="">-- Pilih Tinggi Mast --</option>';
            response.data.forEach(mast => {
                options += `<option value="${mast.id}">${mast.name}</option>`;
            });
            $('#specMastHeight').html(options);
            if (selectedMastId) {
                $('#specMastHeight').val(selectedMastId);
            }
        }
    }).fail(function(xhr) {
        console.error('Failed to load mast heights:', xhr.responseText);
        $('#specMastHeight').html('<option value="">Error loading mast heights</option>');
    });
}

function loadTiresForSpecification() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=ban', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Tire --</option>';
            response.data.forEach(tire => {
                options += `<option value="${tire.id}">${tire.name}</option>`;
            });
            $('#specBan').html(options);
        }
    }).fail(function(xhr) {
        console.error('Failed to load tires:', xhr.responseText);
        $('#specBan').html('<option value="">Error loading tires</option>');
    });
}

function loadWheelsForSpecification() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=roda', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Wheel --</option>';
            response.data.forEach(wheel => {
                options += `<option value="${wheel.id}">${wheel.name}</option>`;
            });
            $('#specRoda').html(options);
        }
    }).fail(function(xhr) {
        console.error('Failed to load wheels:', xhr.responseText);
        $('#specRoda').html('<option value="">Error loading wheels</option>');
    });
}

// Rupiah formatter helpers for specification price fields
// Bilingual helper for inline translations
const isEnLocale = (document.documentElement.lang || '').toLowerCase().startsWith('en');
const tr = (idText, enText) => isEnLocale ? enText : idText;
function formatAccessoryLabel(value) {
    return window.OptimaAccessory ? window.OptimaAccessory.formatLabel(value) : String(value || '').trim();
}
const standardAccessories = (window.OptimaAccessory && OptimaAccessory.getGroupItemCodes)
    ? OptimaAccessory.getGroupItemCodes('quotationStandard')
    : ['main_light','work_light','rotary_lamp','back_buzzer','horn_klason','mirror','safety_belt','load_backrest','forks','overhead_guard','document_holder','tool_kit','apar_bracket'];

$(document).on('click', '#btnSetAksesoriStandar', function() {
    const accessoryInputs = $('[name="aksesoris[]"]');
    accessoryInputs.prop('checked', false);
    standardAccessories.forEach((item) => {
        accessoryInputs.filter(`[value="${item}"]`).prop('checked', true);
    });
});

// Utility: Remove all non-digit characters from value
function sanitizeRupiahToNumber(value) {
    if (value === null || value === undefined) {
        return '';
    }
    return String(value).replace(/[^\d]/g, '');
}

// Utility: Parse Rupiah formatted string to integer
function parseRupiahNumber(value) {
    const clean = sanitizeRupiahToNumber(value);
    return clean ? parseInt(clean, 10) : 0;
}

// Utility: Get currency locale based on current language
function getCurrencyLocale() {
    const htmlLang = (document.documentElement.lang || '').toLowerCase();
    return htmlLang.startsWith('en') ? 'en-US' : 'id-ID';
}

// Currency formatter for IDR
const currencyFormatterIDR = new Intl.NumberFormat(getCurrencyLocale(), {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
});

// Format input value as Rupiah currency
function formatRupiahInputValue(value) {
    const clean = sanitizeRupiahToNumber(value);
    if (!clean) {
        return '';
    }
    return currencyFormatterIDR.format(Number(clean)).replace(/\u00A0/g, ' ');
}

// Bind Rupiah formatter to input fields (on blur)
function bindRupiahFormatter(selector) {
    $(document).on('blur', selector, function() {
        const formatted = formatRupiahInputValue($(this).val());
        $(this).val(formatted);
    });
}

// Sanitize FormData field from Rupiah format to plain number
function normalizeRupiahFormData(formData, fieldName) {
    if (!formData.has(fieldName)) {
        return;
    }
    const clean = sanitizeRupiahToNumber(formData.get(fieldName));
    formData.set(fieldName, clean);
}

// Bind formatters to all currency input fields
bindRupiahFormatter('#operatorPriceMonthly, #operatorPriceDaily, #monthlyPrice, #dailyPrice, #attachmentMonthlyPrice, #attachmentDailyPrice');

// Handle specification form submission (unified for Add and Edit)
$('#addSpecificationForm').on('submit', function(e) {
    e.preventDefault();
    
    // Check if this is Add or Edit mode
    const specId = $('#specId').val();
    const isEditMode = specId && specId !== '';
    
    console.log('Form mode:', isEditMode ? 'EDIT' : 'ADD', 'Spec ID:', specId);
    
    // Check if spare unit is selected (oldlegacy field)
    const isSpareUnit = $('#isSpareUnit').is(':checked');
    
    // Check if spare units are included (new field)
    const includeSpare = $('#includeSpareUnits').is(':checked');
    const spareQuantity = includeSpare ? parseInt($('#spareQuantity').val()) || 0 : 0;
    
    // Enhanced validation
    const quantity = parseInt($('#addSpecificationForm [name="quantity"]').val());
    const monthlyPrice = parseRupiahNumber($('#monthlyPrice').val());
    const dailyPrice = parseRupiahNumber($('#dailyPrice').val());
    const departemen = $('#specDepartemen').val();
    const tipeUnit = $('#specTipeUnit').val();
    
    // Client-side validation
    if (!quantity || quantity < 1) {
        OptimaUI.fire(
            tr('Validasi Gagal', 'Validation Error'),
            tr('Mohon isi quantity yang valid (minimal 1)', 'Please enter a valid quantity (minimum 1)'),
            'warning'
        );
        return;
    }
    
    // Validate: at least one price (monthly or daily) must be filled
    // Skip validation if this is old spare unit (backward compatibility) OR if only spare units (no billable units)
    if (!isSpareUnit && quantity > 0 && monthlyPrice === 0 && dailyPrice === 0) {
        OptimaUI.fire(
            tr('Validasi Gagal', 'Validation Error'),
            tr('Isi minimal satu field harga (Harga Sewa Bulanan atau Harian)', 'Please fill in at least one price field (Monthly Rental Price or Daily Rental Price)'),
            'warning'
        );
        return;
    }
    
    if (!departemen) {
        OptimaUI.fire(
            tr('Validasi Gagal', 'Validation Error'),
            tr('Silakan pilih departemen', 'Please select a department'),
            'warning'
        );
        return;
    }
    
    if (!tipeUnit) {
        OptimaUI.fire(
            tr('Validasi Gagal', 'Validation Error'),
            tr('Silakan pilih tipe unit', 'Please select a unit type'),
            'warning'
        );
        $('#specTipeUnit').focus();
        return;
    }
    
    const formData = new FormData(this);
    
    // Sanitize currency fields to plain numbers
    normalizeRupiahFormData(formData, 'operator_price_monthly');
    normalizeRupiahFormData(formData, 'operator_price_daily');
    normalizeRupiahFormData(formData, 'unit_price');
    normalizeRupiahFormData(formData, 'harga_per_unit_harian');
    
    // SPARE UNITS HANDLING
    // Always ensure spare_quantity is sent (checkbox may not submit if unchecked)
    if (includeSpare) {
        // Get value from input field
        const spareQty = parseInt($('#spareQuantity').val()) || 0;
        formData.set('spare_quantity', spareQty.toString());
        console.log('Ã¢Å“â€¦ Spare units enabled - spare_quantity:', spareQty);
    } else {
        // Not included - set to 0
        formData.set('spare_quantity', '0');
        console.log('Ã¢ÂÅ’ Spare units NOT enabled - spare_quantity: 0');
    }
    
    // OPERATOR HANDLING
    // Always ensure include_operator is sent (checkbox may not be in FormData if unchecked)
    const includeOperator = $('#includeOperator').is(':checked');
    if (includeOperator) {
        // Operator included - get values from fields
        formData.set('include_operator', '1');
        const opQty = parseInt($('#operatorQuantity').val()) || 1;
        formData.set('operator_quantity', opQty.toString());
        formData.set('operator_price_monthly', '0');
        formData.set('operator_price_daily', '0');
        console.log('Ã¢Å“â€¦ Operator enabled - quantity:', opQty);
    } else {
        // Operator not included - set all to 0/false
        formData.set('include_operator', '0');
        formData.set('operator_quantity', '0');
        formData.set('operator_price_monthly', '0');
        formData.set('operator_price_daily', '0');
        console.log('Ã¢ÂÅ’ Operator NOT enabled');
    }
    
    // Add CSRF token
    formData.append(window.csrfTokenName, window.csrfToken || '<?= csrf_hash() ?>');
    
    // Ensure spare unit value is sent (checkbox may not be in FormData if unchecked)
    if (!isSpareUnit) {
        formData.set('is_spare_unit', '0');
    }
    
    // DEBUG: Log all FormData being sent
    console.log('Ã°Å¸â€œÂ¤ FormData being sent to server:');
    for (let [key, value] of formData.entries()) {
        if (key.includes('spare') || key.includes('operator')) {
            console.log(`  ${key}:`, value);
        }
    }
    
    const submitBtn = $('#submitSpecificationBtn');
    
    // Determine endpoint based on mode
    const endpoint = isEditMode 
        ? '<?= base_url('marketing/quotations/update-specification') ?>/' + specId
        : '<?= base_url('marketing/quotations/add-specification') ?>';
    
    const actionText = isEditMode ? tr('Memperbarui...', 'Updating...') : tr('Menyimpan...', 'Saving...');
    const successMsg = isEditMode ? tr('Spesifikasi berhasil diperbarui', 'Specification updated successfully') : tr('Spesifikasi berhasil ditambahkan', 'Specification added successfully');
    
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>' + actionText);
    
    $.ajax({
        url: endpoint,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            // DEBUG: Log full response from server
            console.log('Ã°Å¸â€Â Server Response:', response);
            if (response.debug) {
                console.log('Ã°Å¸â€œÅ  DEBUG INFO from controller:', response.debug);
            }
            
            if (response.success) {
                $('#addSpecificationModal').modal('hide');
                OptimaUI.fire(
                    tr('Berhasil', 'Success'),
                    response.message || successMsg,
                    'success'
                );
                
                // Reload specifications and refresh tab
                if (currentQuotationId) {
                    $('#specifications-tab').removeClass('loaded');
                    loadQuotationSpecifications(currentQuotationId);
                    
                    // Refresh action buttons in detail modal by re-fetching quotation data
                    refreshQuotationActions(currentQuotationId);
                    
                    // Reload DataTable to update spec_count and action buttons
                    if (typeof quotationsTable !== 'undefined' && quotationsTable.ajax) {
                        quotationsTable.ajax.reload(null, false); // false = stay on current page
                    }
                }
                
                // Reset form for next entry
                $('#addSpecificationForm')[0].reset();
            } else {
                // Show debug info if available
                let errorMsg = response.message || tr('Gagal menyimpan spesifikasi', 'Failed to save specification');
                if (response.debug) {
                    console.error('Ã¢ÂÅ’ Controller Debug Info:', response.debug);
                    // Add debug info to error message
                    errorMsg += '\n\nDEBUG INFO:\n' + JSON.stringify(response.debug, null, 2);
                }
                
                OptimaUI.fire(
                    tr('Error', 'Error'),
                    errorMsg,
                    'error'
                );
            }
        },
        error: function(xhr, status, error) {
            console.error('Error saving specification:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            
            let errorMessage = tr('Gagal menyimpan spesifikasi', 'Failed to save specification');
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    const errorData = JSON.parse(xhr.responseText);
                    if (errorData.message) errorMessage = errorData.message;
                } catch (e) {
                    console.error('Could not parse error response');
                }
            }
            
            OptimaUI.fire(tr('Error', 'Error'), errorMessage, 'error');
        },
        complete: function() {
            const btnText = $('#specId').val() ? tr('Update Spesifikasi', 'Update Specification') : tr('Simpan Spesifikasi', 'Save Specification');
            submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>' + btnText);
        }
    });
});

// Handle attachment form submission
$('#addAttachmentForm').on('submit', function(e) {
    e.preventDefault();
    
    console.log('Attachment form submitted');
    
    // Enhanced validation
    const quantity = parseInt($('#addAttachmentForm [name="quantity"]').val());
    const monthlyPrice = parseRupiahNumber($('#attachmentMonthlyPrice').val());
    const dailyPrice = parseRupiahNumber($('#attachmentDailyPrice').val());
    const attachmentType = $('#attachmentTipe').val();
    
    // Client-side validation
    if (!quantity || quantity < 1) {
        OptimaUI.fire(
            tr('Validasi Gagal', 'Validation Error'),
            tr('Mohon isi quantity yang valid (minimal 1)', 'Please enter a valid quantity (minimum 1)'),
            'warning'
        );
        return;
    }
    
    // Validate: at least one price (monthly or daily) must be filled
    if (monthlyPrice === 0 && dailyPrice === 0) {
        OptimaUI.fire(
            tr('Validasi Gagal', 'Validation Error'),
            tr('Isi minimal satu field harga (Harga Sewa Bulanan atau Harian)', 'Please fill in at least one price field (Monthly Rental Price or Daily Rental Price)'),
            'warning'
        );
        $('#attachmentMonthlyPrice').focus();
        return;
    }
    
    if (!attachmentType) {
        OptimaUI.fire(
            tr('Validasi Gagal', 'Validation Error'),
            tr('Silakan pilih tipe attachment', 'Please select an attachment type'),
            'warning'
        );
        $('#attachmentTipe').focus();
        return;
    }
    
    const formData = new FormData(this);
    
    // Sanitize currency fields to plain numbers
    normalizeRupiahFormData(formData, 'unit_price');
    normalizeRupiahFormData(formData, 'harga_per_unit_harian');
    
    // Add CSRF token
    formData.append(window.csrfTokenName, window.csrfToken || '<?= csrf_hash() ?>');
    
    const submitBtn = $('#submitAttachmentBtn');
    
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>' + tr('Menyimpan...', 'Saving...'));
    
    $.ajax({
        url: '<?= base_url('marketing/quotations/add-specification') ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                $('#addAttachmentModal').modal('hide');
                OptimaUI.fire(
                    tr('Berhasil', 'Success'),
                    response.message || tr('Attachment berhasil ditambahkan', 'Attachment added successfully'),
                    'success'
                );
                
                // Reload specifications and refresh tab
                if (currentQuotationId) {
                    $('#specifications-tab').removeClass('loaded');
                    loadQuotationSpecifications(currentQuotationId);
                    
                    // Refresh action buttons in detail modal by re-fetching quotation data
                    refreshQuotationActions(currentQuotationId);
                    
                    // Reload DataTable to update spec_count and action buttons
                    if (typeof quotationsTable !== 'undefined' && quotationsTable.ajax) {
                        quotationsTable.ajax.reload(null, false); // false = stay on current page
                    }
                }
                
                // Reset form for next entry
                $('#addAttachmentForm')[0].reset();
            } else {
                OptimaUI.fire(
                    tr('Error', 'Error'),
                    response.message || tr('Gagal menambahkan attachment', 'Failed to add attachment'),
                    'error'
                );
            }
        },
        error: function(xhr, status, error) {
            console.error('Error adding attachment:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            
            let errorMessage = tr('Gagal menambahkan attachment', 'Failed to add attachment');
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    const errorData = JSON.parse(xhr.responseText);
                    if (errorData.message) errorMessage = errorData.message;
                } catch (e) {
                    console.error('Could not parse error response');
                }
            }
            
            OptimaUI.fire(tr('Error', 'Error'), errorMessage, 'error');
        },
        complete: function() {
            submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>' + tr('Simpan Attachment', 'Save Attachment'));
        }
    });
});

// Edit specification - reuse Add Specification modal
function editSpecification(specId) {
    console.log('Ã°Å¸Å¡â‚¬ EDIT - editSpecification called for spec ID:', specId);
    
    if (!currentQuotationId) {
        console.error('No currentQuotationId set!');
        OptimaUI.fire('Error', 'Quotation ID is missing', 'error');
        return;
    }
    
    // First, load specification data
    const apiUrl = `<?= base_url('marketing/quotations/getSpecifications/') ?>${currentQuotationId}`;
    
    $.ajax({
        url: apiUrl,
        method: 'GET',
        success: function(response) {
            if (!response || !response.success) {
                console.error('API returned error or no success flag');
                OptimaUI.fire('Error', response?.message || 'Failed to load specification data', 'error');
                return;
            }
            
            if (!response.data || !Array.isArray(response.data)) {
                console.error('Invalid response data structure');
                OptimaUI.fire('Error', 'Invalid data structure received', 'error');
                return;
            }
            
            // Find the specification with matching ID
            const spec = response.data.find(s => s.id_specification == specId);
            
            if (!spec) {
                console.error('Specification not found in data');
                OptimaUI.fire('Error', 'Specification not found', 'error');
                return;
            }

            // DEBUG: Log critical values from database
            console.log('Ã°Å¸â€œÅ  EDIT - Spec data from database:', {
                id: spec.id_specification,
                quantity: spec.quantity,
                spare_quantity: spec.spare_quantity,
                is_spare_unit: spec.is_spare_unit,
                include_operator: spec.include_operator,
                operator_quantity: spec.operator_quantity,
                operator_monthly_rate: spec.operator_monthly_rate,
                operator_daily_rate: spec.operator_daily_rate,
                harga_per_unit: spec.harga_per_unit,
                monthly_price: spec.monthly_price,
                unit_price: spec.unit_price
            });

            // Change modal title and button for edit mode
            $('#specModalHeader').removeClass('bg-success').addClass('bg-primary');
            $('#specModalTitle').html('<i class="fas fa-edit me-2"></i>Edit Unit Specification');
            $('#submitBtnText').text('Update Specification');
            $('#submitSpecificationBtn').removeClass('btn-success').addClass('btn-primary');
            
            // Set spec ID for update
            $('#specId').val(spec.id_specification);
            $('#specQuotationId').val(currentQuotationId);
            
            // Populate basic fields using name attribute
            $('[name="quantity"]').val(spec.quantity || 1);
            $('[name="specification_name"]').val(spec.specification_name || '');
            
            // Format and set price fields
            // CRITICAL: Parse as number first to avoid decimal point concatenation issue
            // Database DECIMAL(15,2) returns "6500000.00" which sanitizeRupiahToNumber would turn into "650000000"
            const monthlyPrice = parseFloat(spec.harga_per_unit || spec.unit_price || spec.monthly_price || 0);
            const dailyPrice = parseFloat(spec.harga_per_unit_harian || spec.daily_price || 0);
            $('#monthlyPrice').val(monthlyPrice > 0 ? formatRupiahInputValue(Math.round(monthlyPrice)) : '');
            $('#dailyPrice').val(dailyPrice > 0 ? formatRupiahInputValue(Math.round(dailyPrice)) : '');
            
            // Handle spare units
            const spareQuantity = spec.spare_quantity || 0;
            const isLegacySpare = spec.is_spare_unit == 1;
            
            console.log('Ã°Å¸â€Â EDIT - Loading spare unit data:', {
                spare_quantity: spec.spare_quantity,
                is_spare_unit: spec.is_spare_unit,
                spareQuantity: spareQuantity,
                isLegacySpare: isLegacySpare
            });
            
            if (isLegacySpare) {
                // Legacy spare unit - check the old checkbox
                $('#isSpareUnit').prop('checked', true);
                $('#includeSpareUnits').prop('checked', false);
                $('#spareUnitsContainer').hide();
                console.log('Ã¢Å“â€¦ EDIT - Set as LEGACY spare unit');
            } else if (spareQuantity > 0) {
                // New spare quantity model
                console.log('Ã¢Å“â€¦ EDIT - Setting spare quantity:', spareQuantity);
                $('#includeSpareUnits').prop('checked', true);
                $('#spareUnitsContainer').show(); // Use show() instead of slideDown() for immediate display
                $('#spareQuantity').val(spareQuantity);
                // Update display badges (billable + spare = total)
                updateTotalUnitsDisplay();
                console.log('Ã¢Å“â€¦ EDIT - Spare checkbox checked, container shown');
            } else {
                // No spare units
                $('#isSpareUnit').prop('checked', false);
                $('#includeSpareUnits').prop('checked', false);
                $('#spareUnitsContainer').hide();
                console.log('Ã¢â€žÂ¹Ã¯Â¸Â EDIT - No spare units');
            }
            
            console.log('Ã°Å¸â€œâ€¹ Starting to load all dropdown data...');
            
            // STEP 1: Load independent dropdowns first (parallel)
            Promise.all([
                loadDepartemenForSpecification(),
                loadKapasitasForSpecification(),
                loadUnitBrandsForSpecification(),
                loadAttachmentTypesForSpecification(),
                loadForkTypesForSpecification(),
                loadValvesForSpecification(),
                loadMastModelsForSpecification(),
                loadTiresForSpecification(),
                loadWheelsForSpecification(),
                loadTipeUnitForSpecification()
            ]).then(() => {
                console.log('Ã¢Å“â€¦ Independent dropdowns loaded');
                
                // Set independent dropdown values IMMEDIATELY after load
                console.log('Ã°Å¸â€œÅ’ Setting Capacity:', spec.kapasitas_id);
                $('#specKapasitas').val(spec.kapasitas_id || '');
                
                console.log('Ã°Å¸â€œÅ’ Setting Attachment Type:', spec.attachment_tipe);
                $('#specAttachmentTipe').val(spec.attachment_id || '');
                
                console.log('Ã°Å¸â€œÅ’ Setting Valve:', spec.valve_id);
                $('#specValve').val(spec.valve_id || '');
                
                console.log('Ã°Å¸â€œÅ’ Setting Mast:', spec.mast_id);
                if (spec.mast_name) {
                    const mastModelName = String(spec.mast_name).split(' - ')[0].trim();
                    const mastModelOption = $('#specMastModel option').filter(function() {
                        return $(this).text().trim() === mastModelName;
                    }).first();
                    if (mastModelOption.length) {
                        $('#specMastModel').val(mastModelOption.val());
                        loadMastHeightsForSpecification(mastModelOption.val(), spec.mast_id || '');
                    }
                } else if (spec.mast_id) {
                    $('#specMastHeight').val(spec.mast_id);
                }
                
                console.log('Ã°Å¸â€œÅ’ Setting Tire:', spec.ban_id);
                $('#specBan').val(spec.ban_id || '');
                
                console.log('Ã°Å¸â€œÅ’ Setting Wheel:', spec.roda_id);
                $('#specRoda').val(spec.roda_id || '');
                
                // STEP 2: Set department and handle cascading
                $('#specDepartemen').val(spec.departemen_id || '');
                console.log('Ã¢Å“â€¦ Department set to:', spec.departemen_id);

                // Reload Unit Brand after department is selected to ensure strict department filtering
                loadUnitBrandsForSpecification().then(() => {
                    console.log('Ã°Å¸â€œÅ’ Setting Unit Brand:', spec.merk_unit);
                    $('#specMerkUnit').val(spec.brand_id || '');
                });
                
                // Check if electric department
                const deptText = $('#specDepartemen option:selected').text().toLowerCase();
                const isElectric = deptText.includes('electric') || deptText.includes('listrik');
                console.log('Is Electric Department:', isElectric);
                
                // STEP 3: Update Unit Type dropdown based on department
                if (spec.departemen_id && window.allTipeUnitData) {
                    console.log('Ã°Å¸â€œâ€¹ Filtering tipe unit for department:', spec.departemen_id);
                    updateEditTipeUnitOptions(spec.departemen_id).then(() => {
                        $('#specTipeUnit').val(spec.tipe_unit_id || '');
                        console.log('Ã¢Å“â€¦ Unit Type set to:', spec.tipe_unit_id);
                    });
                }
                
                // STEP 4: Handle electric-specific fields
                if (isElectric) {
                    $('#specJenisBaterai').prop('disabled', false).closest('.col-md-4').find('small').show();
                    $('#specCharger').prop('disabled', false).closest('.col-md-4').find('small').show();
                    
                    // DON'T trigger change - load battery/charger directly
                    console.log('Ã°Å¸â€œâ€¹ Loading Battery and Charger for Electric unit...');
                    
                    // Load battery and charger with proper timing
                    Promise.all([
                        loadBatteriesForSpecification(),
                        loadChargersForSpecification()
                    ]).then(() => {
                        console.log('Ã°Å¸â€œÅ’ Setting Battery Type (battery_id):', spec.battery_id);
                        $('#specJenisBaterai').val(spec.battery_id || '');
                        
                        console.log('Ã°Å¸â€œÅ’ Setting Charger:', spec.charger_id);
                        $('#specCharger').val(spec.charger_id || '');
                        
                        console.log('Ã¢Å“â€¦ Battery and Charger loaded and set');
                    });
                } else {
                    $('#specJenisBaterai').prop('disabled', true).val('').closest('.col-md-4').find('small').hide();
                    $('#specCharger').prop('disabled', true).val('').closest('.col-md-4').find('small').hide();
                }
                
                // Handle operator service
                const includeOperator = spec.include_operator == 1;
                
                console.log('Ã°Å¸â€Â EDIT - Loading operator data:', {
                    include_operator: spec.include_operator,
                    includeOperator: includeOperator,
                    operator_quantity: spec.operator_quantity,
                    operator_monthly_rate: spec.operator_monthly_rate,
                    operator_daily_rate: spec.operator_daily_rate
                });
                
                if (includeOperator) {
                    console.log('Ã¢Å“â€¦ EDIT - Setting operator service included');
                    $('#includeOperator').prop('checked', true);
                    $('#operatorDetailsContainer').show(); // Use show() instead of slideDown()
                    $('#operatorQuantity').val(spec.operator_quantity || 1);
                    
                    // Format and set operator prices
                    // CRITICAL: Parse as number first to avoid decimal concatenation (6500000.00 Ã¢â€ â€™ 650000000)
                    const operatorMonthly = parseFloat(spec.operator_monthly_rate || 0);
                    const operatorDaily = parseFloat(spec.operator_daily_rate || 0);
                    $('#operatorPriceMonthly').val(operatorMonthly > 0 ? formatRupiahInputValue(Math.round(operatorMonthly)) : '');
                    $('#operatorPriceDaily').val(operatorDaily > 0 ? formatRupiahInputValue(Math.round(operatorDaily)) : '');
                    console.log('Ã¢Å“â€¦ EDIT - Operator checkbox checked, container shown');
                } else {
                    $('#includeOperator').prop('checked', false);
                    $('#operatorDetailsContainer').hide();
                    console.log('Ã¢â€žÂ¹Ã¯Â¸Â EDIT - No operator service');
                }
                
                // Handle accessories
                $('[name="aksesoris[]"]').prop('checked', false);
                
                const accessoriesData = spec.aksesoris || spec.unit_accessories || '';
                if (accessoriesData && accessoriesData !== '') {
                    const accessories = typeof accessoriesData === 'string' 
                        ? accessoriesData.split(',').map(a => a.trim())
                        : (Array.isArray(accessoriesData) ? accessoriesData : []);
                    
                    accessories.forEach(accessory => {
                        $(`[name="aksesoris[]"][value="${accessory}"]`).prop('checked', true);
                    });
                    
                    console.log('Ã¢Å“â€¦ Accessories loaded:', accessories);
                }
                
                // Handle fork / attachment toggle
                if (spec.fork_id) {
                    $('input[name="fork_attach_type"][value="fork"]').prop('checked', true);
                    $('#specForkSection').show();
                    $('#specAttachSection').hide();
                    $('#specForkId').val(spec.fork_id);
                } else if (spec.attachment_id) {
                    $('input[name="fork_attach_type"][value="attachment"]').prop('checked', true);
                    $('#specAttachSection').show();
                    $('#specForkSection').hide();
                    $('#specAttachmentTipe').val(spec.attachment_id);
                } else {
                    $('input[name="fork_attach_type"][value="none"]').prop('checked', true);
                    $('#specForkSection, #specAttachSection').hide();
                    $('#specForkId').val('');
                    $('#specAttachmentTipe').val('');
                }
                
                // Show modal after ALL data is loaded and set
                console.log('=== ALL DATA LOADED - SHOWING MODAL ===');
                setTimeout(() => {
                    $('#addSpecificationModal').modal('show');
                    console.log('Ã¢Å“â€¦ Modal displayed');
                }, 200);
                
            }).catch(error => {
                console.error('Ã¢ÂÅ’ Error loading dropdown data:', error);
                OptimaUI.fire('Error', 'Failed to load dropdown data: ' + error.message, 'error');
            });
        },
        error: function(xhr, status, error) {
            console.error('=== AJAX ERROR ===');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('XHR Status:', xhr.status);
            console.error('Response Text:', xhr.responseText);
            console.error('Full XHR:', xhr);
            
            let errorMsg = 'Failed to load specification data';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            } else if (xhr.status === 404) {
                errorMsg = 'API endpoint not found (404). Check route configuration.';
            } else if (xhr.status === 500) {
                errorMsg = 'Server error (500). Check server logs.';
            }
            
            OptimaUI.fire('Error', errorMsg, 'error');
        }
    });
}

// Load functions for edit modal
function loadDepartemenForEdit() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=departemen', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Department --</option>';
            response.data.forEach(dept => {
                options += `<option value="${dept.id}">${dept.name}</option>`;
            });
            $('#edit_departemen_id').html(options);
        }
    });
}

function loadTipeUnitForEdit() {
    return $.ajax({
        url: '<?= base_url('marketing/customer-management/getTipeUnit') ?>',
        method: 'GET',
        success: function(response) {
            console.log('Edit Tipe Unit API Response:', response);
            if (response.success) {
                // Store all unit type data globally for filtering (same as Add Specification)
                window.editAllTipeUnitData = response.data;
                console.log('Loaded', response.data.length, 'tipe unit records for edit');
                // Initially show placeholder only
                $('#edit_tipe_unit_id').html('<option value="">-- Select Unit Type --</option>');
            } else {
                console.error('API returned error:', response.message);
                $('#edit_tipe_unit_id').html('<option value="">Error loading unit types</option>');
            }
        },
        error: function(xhr) {
            console.error('Failed to load unit types for edit:', xhr.responseText);
            $('#edit_tipe_unit_id').html('<option value="">Error loading unit types</option>');
        }
    });
}

function updateEditTipeUnitOptions(departemenId) {
    return new Promise((resolve) => {
        const select = $('#specTipeUnit');
        
        select.empty().append('<option value="">-- Select Unit Type --</option>');
        
        if (!departemenId || !window.allTipeUnitData) {
            console.log('No department selected or no tipe unit data available');
            resolve();
            return;
        }
        
        console.log('Edit - Filtering units for department:', departemenId);
        
        // Filter by department (same logic as Add Specification)
        const filteredUnits = window.allTipeUnitData.filter(unit => {
            return unit.id_departemen == departemenId;
        });
        
        console.log('Edit - Filtered Units:', filteredUnits);
        
        if (filteredUnits.length === 0) {
            select.append('<option value="">No unit types available for this department</option>');
            resolve();
            return;
        }
        
        // Group by jenis to avoid duplicates (show JENIS not TIPE)
        const uniqueJenis = [...new Set(filteredUnits.map(unit => unit.jenis))];
        console.log('Edit - Unique jenis found:', uniqueJenis);
        
        uniqueJenis.sort().forEach(jenis => {
            // Find the first unit with this jenis to get the id
            const unitWithJenis = filteredUnits.find(unit => unit.jenis === jenis);
            if (unitWithJenis) {
                console.log('Edit - Adding option:', unitWithJenis.id_tipe_unit, jenis);
                select.append(`<option value="${unitWithJenis.id_tipe_unit}">${jenis}</option>`);
            }
        });
        
        resolve();
    });
}

function loadKapasitasForEdit() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=kapasitas', function(response) {
        if (response.success) {
            // Sort capacity data by numeric value (from smallest to largest)
            const sortedData = response.data.sort((a, b) => {
                const numA = parseFloat(a.name.replace(/[^\d.]/g, '')) || 0;
                const numB = parseFloat(b.name.replace(/[^\d.]/g, '')) || 0;
                return numA - numB;
            });
            
            let options = '<option value="">-- Select Capacity --</option>';
            sortedData.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            $('#edit_kapasitas_id').html(options);
        }
    });
}

function loadUnitBrandsForEdit() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=merk_unit', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Brand --</option>';
            response.data.forEach(item => {
                options += `<option value="${item.name}">${item.name}</option>`;
            });
            $('#edit_brand').html(options);
        }
    });
}

function loadBatteriesForEdit(departemenId) {
    return $.get(`<?= base_url('marketing/spk/spec-options') ?>?type=jenis_baterai&departemen_id=${departemenId}`, function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Battery --</option>';
            response.data.forEach(item => {
                options += `<option value="${item.name}">${item.name}</option>`;
            });
            $('#edit_jenis_baterai').html(options).prop('disabled', false);
        }
    });
}

function loadChargersForEdit(departemenId) {
    return $.get(`<?= base_url('marketing/spk/spec-options') ?>?type=charger&departemen_id=${departemenId}`, function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Charger --</option>';
            response.data.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            $('#edit_charger_id').html(options).prop('disabled', false);
        }
    }).fail(function(xhr) {
        console.error('Failed to load chargers for edit:', xhr.responseText);
        $('#edit_charger_id').html('<option value="">Error loading chargers</option>');
    });
}

function loadAttachmentTypesForEdit() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=attachment_tipe', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Attachment Type --</option>';
            response.data.forEach(item => {
                options += `<option value="${item.name}">${item.name}</option>`;
            });
            $('#edit_attachment_tipe').html(options);
        }
    });
}

function loadValvesForEdit() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=valve', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Valve --</option>';
            response.data.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            $('#edit_valve_id').html(options);
        }
    });
}

function loadMastsForEdit() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=mast', function(response) {
        if (response.success) {
            // Sort mast data by height in MM (from smallest to largest)
            const sortedData = response.data.sort((a, b) => {
                const matchA = a.name.match(/(\d+)\s*MM/i);
                const matchB = b.name.match(/(\d+)\s*MM/i);
                
                const numA = matchA ? parseInt(matchA[1]) : 0;
                const numB = matchB ? parseInt(matchB[1]) : 0;
                
                return numA - numB;
            });
            
            let options = '<option value="">-- Select Mast --</option>';
            sortedData.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            $('#edit_mast_id').html(options);
        }
    });
}

function loadTiresForEdit() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=ban', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Tire --</option>';
            response.data.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            $('#edit_ban_id').html(options);
        }
    });
}

function loadWheelsForEdit() {
    return $.get('<?= base_url('marketing/spk/spec-options') ?>?type=roda', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Wheel --</option>';
            response.data.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            $('#edit_roda_id').html(options);
        }
    });
}

// Department change handler for edit modal
$(document).on('change', '#edit_departemen_id', function() {
    const departemenId = $(this).val();
    const deptText = $(this).find('option:selected').text().toLowerCase();
    const isElectric = deptText.includes('electric') || deptText.includes('listrik');
    
    console.log('Edit department changed:', departemenId, deptText, 'Electric:', isElectric);
    
    // Update unit types based on department
    updateEditTipeUnitOptions(departemenId);
    
    // Handle battery and charger fields visibility
    if (isElectric && departemenId) {
        // Enable and load battery/charger options
        $('#edit_jenis_baterai').prop('disabled', false).removeClass('bg-light');
        $('#edit_charger_id').prop('disabled', false).removeClass('bg-light');
        
        loadBatteriesForEdit(departemenId);
        loadChargersForEdit(departemenId);
    } else {
        // Disable and clear battery/charger fields
        $('#edit_jenis_baterai')
            .html('<option value="">Hanya tersedia untuk unit Electric</option>')
            .prop('disabled', true)
            .addClass('bg-light')
            .val('');
        $('#edit_charger_id')
            .html('<option value="">Hanya tersedia untuk unit Electric</option>')
            .prop('disabled', true)
            .addClass('bg-light')
            .val('');
    }
});

// Handle Edit Specification Form Submit
$(document).on('submit', '#editSpecificationForm', function(e) {
    e.preventDefault();
    
    const specId = $('#edit_spec_id').val();
    const formData = $(this).serialize() + '&' + window.csrfTokenName + '=' + encodeURIComponent(window.csrfToken || '<?= csrf_hash() ?>');
    
    $.ajax({
        url: `<?= base_url('marketing/quotations/update-specification/') ?>${specId}`,
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                $('#editSpecificationModal').modal('hide');
                OptimaUI.fire('Success!', response.message, 'success');
                
                // Reload specifications
                if (currentQuotationId) {
                    loadQuotationSpecifications(currentQuotationId);
                }
            } else {
                OptimaUI.fire('Error', response.message || 'Failed to update specification', 'error');
            }
        },
        error: function(xhr) {
            console.error('Error updating specification:', xhr);
            OptimaUI.fire('Error', 'Failed to update specification: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
        }
    });
});

// Auto-calculate total price in edit form
$(document).on('input', '#edit_quantity, #edit_unit_price', function() {
    const quantity = parseFloat($('#edit_quantity').val()) || 0;
    const unitPrice = parseFloat($('#edit_unit_price').val()) || 0;
    const totalPrice = quantity * unitPrice;
    
    $('#edit_total_price').val(totalPrice);
    $('#edit_total_price_display').val('Rp ' + totalPrice.toLocaleString('id-ID'));
});

// Delete specification
function deleteSpecification(specId) {
    OptimaUI.fire({
        title: 'Are you sure?',
        text: 'This specification will be deleted permanently',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `<?= base_url('marketing/quotations/delete-specification/') ?>${specId}`,
                type: 'DELETE',
                data: {
                    [window.csrfTokenName]: window.csrfToken || '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        OptimaUI.fire('Deleted!', response.message, 'success');
                        
                        // Reload specifications
                        if (currentQuotationId) {
                            loadQuotationSpecifications(currentQuotationId);
                            
                            // Refresh action buttons in detail modal by re-fetching quotation data
                            refreshQuotationActions(currentQuotationId);
                            
                            // Reload DataTable to update spec_count and action buttons
                            if (typeof quotationsTable !== 'undefined' && quotationsTable.ajax) {
                                quotationsTable.ajax.reload(null, false); // false = stay on current page
                            }
                        }
                    } else {
                        OptimaUI.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    console.error('Error deleting specification:', xhr);
                    OptimaUI.fire('Error', 'Failed to delete specification: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
                }
            });
        }
    });
}

// Helper function for number formatting
function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num || 0);
}

// ===== PROSPECT CREATION FUNCTIONS =====

function openCreateProspectModal() {
    // Reset form
    $('#createProspectForm')[0].reset();
    $('.is-invalid').removeClass('is-invalid');
    
    // Reset customer search section
    $('#selectedCustomerId').val('');
    $('#customerSearchResults').html('');
    $('#linkExistingCustomer').prop('checked', false);
    $('#customerSearchSection').hide();
    $('#newCustomerSection').show();
    
    // Ensure proper required attributes
    $('#prospectCompanyName').attr('required', 'required');
    $('#prospectContactPerson').attr('required', 'required');
    
    // Set default valid until date (30 days from now)
    const defaultDate = new Date();
    defaultDate.setDate(defaultDate.getDate() + 30);
    $('#validUntil').val(defaultDate.toISOString().split('T')[0]);
    
    // Show modal and focus first field when ready
    $('#createProspectModal').modal('show');
    
    setTimeout(() => {
        $('#prospectCompanyName').focus();
    }, 300);
}

// Handle prospect creation form submission
$('#createProspectForm').on('submit', function(e) {
    e.preventDefault();
    
    // Custom validation for dynamic form
    let isValid = true;
    const isExistingCustomer = $('#linkExistingCustomer').is(':checked');
    
    // Clear previous validation states
    $('.is-invalid').removeClass('is-invalid');
    
    if (isExistingCustomer) {
        // Validate customer selection
        if (!$('#selectedCustomerId').val()) {
            $('#customerSearchInput').addClass('is-invalid');
            OptimaUI.fire('Validation Error', 'Please select a customer or switch to new customer mode', 'error');
            return false;
        }
    } else {
        // Validate new customer fields
        if (!$('#prospectCompanyName').val().trim()) {
            $('#prospectCompanyName').addClass('is-invalid');
            isValid = false;
        }
        if (!$('#prospectContactPerson').val().trim()) {
            $('#prospectContactPerson').addClass('is-invalid');
            isValid = false;
        }
    }
    
    // Common validation for quotation fields
    if (!$('#quotationTitle').val().trim()) {
        $('#quotationTitle').addClass('is-invalid');
        isValid = false;
    }
    if (!$('#validUntil').val()) {
        $('#validUntil').addClass('is-invalid');
        isValid = false;
    }
    
    if (!isValid) {
        OptimaUI.fire('Validation Error', 'Please fill in all required fields', 'error');
        return false;
    }
    
    const formData = new FormData(this);
    const submitBtn = $(this).find('button[type="submit"]');
    
    // Add workflow stage as prospect
    formData.append('workflow_stage', 'PROSPECT');
    formData.append('quotation_date', new Date().toISOString().split('T')[0]);
    formData.append('stage', 'DRAFT');
    
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Creating...');
    
    $.ajax({
        url: '<?= base_url('marketing/quotations/create-prospect') ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                $('#createProspectModal').modal('hide');
                OptimaUI.fire({
                    title: 'Success!', 
                    text: 'Successfully created prospect: ' + response.message,
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'View Prospect',
                    cancelButtonText: window.lang('close')
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Open the prospect detail
                        viewQuotation(response.data.id_quotation);
                    }
                    
                    // Reload quotations table and statistics
                    quotationsTable.ajax.reload();
                    loadStatistics();
                });
            } else {
                OptimaUI.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error creating prospect:', error);
            console.error('XHR Response:', xhr);
            console.error('Response JSON:', xhr.responseJSON);
            
            let errorMessage = 'Failed to create prospect';
            
            // Check for validation errors
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                // Display detailed validation errors if available
                if (xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    let errorList = '<ul class="text-start">';
                    Object.keys(errors).forEach(field => {
                        errorList += `<li><strong>${field}:</strong> ${errors[field]}</li>`;
                    });
                    errorList += '</ul>';
                    
                    OptimaUI.fire({
                        title: 'Validation Error',
                        html: errorMessage + errorList,
                        icon: 'error',
                        width: '600px'
                    });
                    return;
                }
            }
            
            OptimaUI.fire('Error', errorMessage, 'error');
        },
        complete: function() {
            submitBtn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Create Prospect');
        }
    });
});

// ===== WORKFLOW STAGE FUNCTIONS =====

function convertToQuotation(quotationId) {
    OptimaUI.fire({
        title: 'Convert to Quotation?',
        text: 'Allow adding specifications and sending to customer.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Convert',
        cancelButtonText: window.lang('cancel'),
        customClass: {
            popup: 'swal2-small'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('<?= base_url('marketing/quotations/convert-to-quotation') ?>/' + quotationId, {
                [window.csrfTokenName]: window.csrfToken || '<?= csrf_hash() ?>'
            })
                .done(function(response) {
                    if (response.success) {
                        OptimaUI.fire({
                            title: 'Quotation Created!',
                            text: response.message,
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'Add Specifications Now',
                            cancelButtonText: window.lang('later')
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Open specifications modal
                                addSpecifications(quotationId);
                            }
                        });
                        
                        // Reload table and statistics
                        quotationsTable.ajax.reload();
                        loadStatistics();
                        
                        // If modal is open and this is current quotation, refresh it
                        if (currentQuotationId == quotationId && $('#detailModal').hasClass('show')) {
                            // Reset specifications tab loaded state
                            $('#specifications-tab').removeClass('loaded');
                            
                            // Reload quotation data
                            viewQuotation(quotationId);
                            
                            // If specifications tab is active, reload it
                            if ($('#specifications-tab').hasClass('active')) {
                                setTimeout(() => {
                                    $('#specifications-tab').click();
                                }, 500);
                            }
                        }
                    } else {
                        OptimaUI.fire('Error', response.message, 'error');
                    }
                })
                .fail(function() {
                    OptimaUI.fire('Error', 'Failed to convert prospect', 'error');
                });
        }
    });
}

function openSpecificationsModal(quotationId) {
    // First check if quotation is in correct stage for specifications
    $.get('<?= base_url('marketing/quotations/get/') ?>' + quotationId, function(response) {
        // Handle error responses
        if (response.status === 'error') {
            OptimaUI.fire('Error', 'Failed to load quotation: ' + response.message, 'error');
            return;
        }
        
        // Use the data property for detailed information
        const quotation = response.data || response;
        
        if (quotation && quotation.id_quotation) {
            // Check workflow stage
            if (quotation.workflow_stage === 'PROSPECT') {
                OptimaUI.fire({
                    title: 'Convert to Quotation First',
                    text: 'This prospect must be converted to quotation before adding specifications.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Convert Now',
                    cancelButtonText: window.lang('cancel')
                }).then((result) => {
                    if (result.isConfirmed) {
                        convertToQuotation(quotationId);
                    }
                });
                return;
            }
            
            if (!['QUOTATION', 'SENT'].includes(quotation.workflow_stage)) {
                OptimaUI.fire('Warning', 'Specifications can only be added to quotations in QUOTATION or SENT stage.', 'warning');
                return;
            }
            
            // Open the quotation modal and switch to specifications tab
            viewQuotation(quotationId);
            // Switch to specifications tab after modal loads
            setTimeout(() => {
                $('#specifications-tab').click();
            }, 500);
        } else {
            OptimaUI.fire('Error', 'Failed to load quotation details', 'error');
        }
    }).fail(function() {
        OptimaUI.fire('Error', 'Failed to load quotation details', 'error');
    });
}

function openPrintSpecModal(quotationId) {
    console.log('Opening print spec modal for quotation:', quotationId);
    
    // Get quotation specifications from the correct endpoint
    $.get('<?= base_url('marketing/quotations/getSpecifications/') ?>' + quotationId)
        .done(function(response) {
            console.log('Specifications data received:', response);
            
            if (response.success && response.data) {
                const specs = response.data || [];
                console.log('Specifications:', specs);
                
                if (specs.length === 0) {
                    OptimaUI.fire('Info', 'No specifications found for this quotation', 'info');
                    return;
                }
                
                // Build modal content
                let modalContent = `
                    <div class="modal fade" id="printSpecModal" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Select Specifications to Print</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-sm btn-primary" onclick="selectAllSpecs(true)">
                                            <i class="fas fa-check-square me-1"></i>Select All
                                        </button>
                                        <button type="button" class="btn btn-sm btn-secondary" onclick="selectAllSpecs(false)">
                                            <i class="fas fa-square me-1"></i>Deselect All
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="50"><input type="checkbox" id="selectAll" onchange="toggleAllSpecs(this)" checked></th>
                                                    <th>No</th>
                                                    <th>Description</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody id="specList">
                `;
                
                specs.forEach((spec, index) => {
                    let desc = '';
                    if (spec.specification_type === 'UNIT') {
                        // Format seperti di print
                        const unitTitle = (spec.unit_type && spec.unit_subtype) 
                            ? `${spec.unit_type} ${spec.unit_subtype}` 
                            : (spec.unit_type || 'UNIT');
                        const dept = spec.department_name ? spec.department_name.toUpperCase() : 'STANDARD';
                        desc = `${unitTitle} - ${dept}`;
                        
                        // Tambahkan spesifikasi detail
                        let details = [];
                        if (spec.brand_name) details.push(`Merk: ${spec.brand_name}`);
                        if (spec.capacity_name) details.push(`Cap. ${spec.capacity_name}`);
                        if (spec.mast_name) details.push(`Mast ${spec.mast_name}`);
                        if (spec.wheel_name) details.push(spec.wheel_name);
                        if (spec.jenis_baterai) details.push(`Baterai: ${spec.jenis_baterai}`);
                        if (spec.attachment_type) details.push(`Attachment: ${spec.attachment_type}`);
                        if (spec.unit_accessories && spec.unit_accessories !== 'null') {
                            const accSummary = spec.unit_accessories.split(',').map(a => formatAccessoryLabel(a)).join(', ');
                            details.push(`Acc: ${accSummary}`);
                        }
                        
                        if (details.length > 0) {
                            desc += '<br><small class="text-muted">' + details.join(' | ') + '</small>';
                        }
                    } else {
                        desc = spec.attachment_type || 'ATTACHMENT';
                        let details = [];
                        if (spec.attachment_brand) details.push(`Merk: ${spec.attachment_brand}`);
                        if (spec.attachment_model) details.push(`Model: ${spec.attachment_model}`);
                        if (details.length > 0) {
                            desc += '<br><small class="text-muted">' + details.join(' - ') + '</small>';
                        }
                    }
                    
                    // Tentukan harga dan label
                    let priceValue = 0;
                    let priceLabel = '';
                    
                    if (spec.monthly_price && parseFloat(spec.monthly_price) > 0) {
                        priceValue = parseFloat(spec.monthly_price);
                        priceLabel = '/month';
                    } else if (spec.daily_price && parseFloat(spec.daily_price) > 0) {
                        priceValue = parseFloat(spec.daily_price);
                        priceLabel = '/day';
                    }
                    
                    const priceFormatted = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(priceValue);
                    
                    const priceDisplay = priceValue > 0 ? `${priceFormatted}<small class="text-muted">${priceLabel}</small>` : 'Rp 0';
                    
                    modalContent += `
                        <tr>
                            <td><input type="checkbox" class="spec-checkbox" value="${spec.id_specification}" checked></td>
                            <td>${index + 1}</td>
                            <td>${desc}</td>
                            <td>${spec.quantity || 1}</td>
                            <td>${priceDisplay}</td>
                        </tr>
                    `;
                });
                
                modalContent += `
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" onclick="printSelectedSpecs(${quotationId})">
                                        <i class="fas fa-print me-1"></i>Print Selected
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Remove existing modal if any
                $('#printSpecModal').remove();
                
                // Append and show modal
                $('body').append(modalContent);
                const modal = new bootstrap.Modal(document.getElementById('printSpecModal'));
                modal.show();
                
                console.log('Modal shown');
                
                // Clean up on modal hide
                $('#printSpecModal').on('hidden.bs.modal', function() {
                    $(this).remove();
                });
            } else {
                console.error('Invalid response:', response);
                OptimaUI.fire('Error', 'Failed to load quotation data', 'error');
            }
        })
        .fail(function(xhr, status, error) {
            console.error('AJAX error:', status, error);
            console.error('Response:', xhr.responseText);
            OptimaUI.fire('Error', 'Failed to load specifications: ' + error, 'error');
        });
}

function toggleAllSpecs(checkbox) {
    $('.spec-checkbox').prop('checked', checkbox.checked);
}

function selectAllSpecs(select) {
    $('.spec-checkbox').prop('checked', select);
    $('#selectAll').prop('checked', select);
}

function printSelectedSpecs(quotationId) {
    const selectedSpecs = [];
    $('.spec-checkbox:checked').each(function() {
        selectedSpecs.push($(this).val());
    });
    
    console.log('Selected specs:', selectedSpecs);
    
    if (selectedSpecs.length === 0) {
        OptimaUI.fire('Warning', 'Please select at least one specification', 'warning');
        return;
    }
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('printSpecModal'));
    if (modal) {
        modal.hide();
    }
    
    // Open print with selected specs
    const printUrl = '<?= base_url('marketing/quotations/print/') ?>' + quotationId + '?specs=' + selectedSpecs.join(',');
    console.log('Opening print URL:', printUrl);
    window.open(printUrl, '_blank', 'noopener,noreferrer');
}

function printQuotation(quotationId) {
    // Legacy function - redirect to new modal
    openPrintSpecModal(quotationId);
}

function sendQuotation(quotationId) {
    OptimaUI.fire({
        title: 'Send Quotation?',
        text: 'Mark quotation as sent to customer.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Send',
        cancelButtonText: window.lang('cancel'),
        customClass: {
            popup: 'swal2-small'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('<?= base_url('marketing/quotations/send-quotation') ?>/' + quotationId, {
                [window.csrfTokenName]: window.csrfToken || '<?= csrf_hash() ?>'
            })
                .done(function(response) {
                    if (response.success) {
                        OptimaUI.fire('Success!', 'Quotation sent successfully', 'success');
                        
                        // Reload table and statistics
                        quotationsTable.ajax.reload();
                        loadStatistics();
                        
                        // Refresh modal if open
                        if (currentQuotationId == quotationId && $('#detailModal').hasClass('show')) {
                            viewQuotation(quotationId);
                        }
                    } else {
                        // Check if this is a specifications requirement error
                        if (response.require_specs) {
                            OptimaUI.fire({
                                title: 'Specifications Required',
                                text: response.message,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Add Specifications',
                                cancelButtonText: window.lang('cancel')
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    addSpecifications(quotationId);
                                }
                            });
                        } else {
                            OptimaUI.fire('Error', response.message, 'error');
                        }
                    }
                })
                .fail(function() {
                    OptimaUI.fire('Error', 'Failed to send quotation', 'error');
                });
        }
    });
}

function markAsDeal(quotationId) {
    // First check if customer profile validation is needed
    $.get('<?= base_url('marketing/quotations/customer-profile-status/') ?>' + quotationId, function(response) {
        if (response.success && response.has_customer && !response.is_complete) {
            // Customer profile is not complete - show validation modal
            OptimaUI.fire({
                title: 'Customer Profile Validation Required',
                html: `
                    <div class="text-start">
                        <p><strong>Ã°Å¸â€â€™ Customer Profile Incomplete</strong></p>
                        <p>Before marking this quotation as DEAL, the customer profile must be completed with:</p>
                        <ul class="text-start" style="list-style-type: disc; padding-left: 20px;">
                            <li>Complete customer information</li>
                            <li>At least one valid customer location</li>
                            <li>Contact person details</li>
                            <li>Complete address information</li>
                        </ul>
                        <p><strong>Would you like to:</strong></p>
                    </div>
                `,
                icon: 'warning',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Complete Profile First',
                denyButtonText: 'Mark as Deal Anyway',
                cancelButtonText: window.lang('cancel'),
                customClass: {
                    popup: 'swal2-large'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Open customer profile in new tab
                    if (response.customer_id) {
                        const customerEditUrl = '<?= base_url('customer-management/showCustomer/') ?>' + response.customer_id;
                        window.open(customerEditUrl, '_blank');
                        OptimaUI.fire({
                            title: 'Customer Profile Opened',
                            text: 'Please complete the customer profile in the new tab, then return here to mark as deal.',
                            icon: 'info'
                        });
                    } else {
                        OptimaUI.fire('Error', 'Customer ID not found', 'error');
                    }
                } else if (result.isDenied) {
                    // Proceed with marking as deal without complete profile
                    proceedMarkAsDeal(quotationId, true);
                }
                // If cancelled, do nothing
            });
        } else {
            // Customer profile is complete or no customer exists yet, proceed normally
            proceedMarkAsDeal(quotationId, false);
        }
    }).fail(function() {
        // If profile check fails, proceed with normal flow
        OptimaUI.fire('Warning', 'Could not verify customer profile status. Proceeding with normal flow.', 'warning');
        proceedMarkAsDeal(quotationId, false);
    });
}

function proceedMarkAsDeal(quotationId, skipValidation = false) {
    const confirmText = skipValidation ? 
        'Will mark as deal and automatically create or link the customer record. If automatic customer creation fails, a manual fallback button will remain available.' :
        'Will mark as deal and automatically create or link the customer record.';
        
    OptimaUI.fire({
        title: 'Mark as Deal?',
        text: confirmText,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Deal',
        cancelButtonText: window.lang('cancel'),
        customClass: {
            popup: 'swal2-small'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('<?= base_url('marketing/quotations/markAsDeal') ?>/' + quotationId, {
                [window.csrfTokenName]: window.csrfToken || '<?= csrf_hash() ?>'
            })
                .done(function(response) {
                    if (response.success) {
                        showCustomerLocationModal(
                            response.customer_id,
                            quotationId,
                            response.message,
                            response.needs_manual_customer_creation === true
                        );
                    } else {
                        OptimaUI.fire('Error', response.message, 'error');
                    }
                })
                .fail(function() {
                    OptimaUI.fire('Error', 'Failed to mark as deal', 'error');
                });
        }
    });
}

// Function to handle complete customer profile button
function completeCustomerProfile(quotationId) {
    // Get quotation details to extract customer ID
    $.get('<?= base_url('marketing/quotations/getQuotation/') ?>' + quotationId)
        .done(function(response) {
            if (response.success && response.data && response.data.created_customer_id) {
                const customerEditUrl = '<?= base_url('customer-management/showCustomer/') ?>' + response.data.created_customer_id;
                window.open(customerEditUrl, '_blank');

                OptimaUI.fire({
                    title: 'Customer Profile Opened',
                    text: 'Silakan lengkapi profil customer di tab baru, lalu kembali untuk lanjut Create Contract/SPK.',
                    icon: 'info'
                });
            } else {
                OptimaUI.fire('Error', 'Customer not found for this quotation', 'error');
            }
        })
        .fail(function() {
            OptimaUI.fire('Error', 'Failed to load quotation details', 'error');
        });
}

// Function to handle complete customer contract button
function completeCustomerContract(quotationId) {
    // Get quotation details to extract customer ID and location ID
    $.get('<?= base_url('marketing/quotations/getQuotation/') ?>' + quotationId)
        .done(function(response) {
            if (response.success && response.data) {
                if (!response.data.created_customer_id) {
                    OptimaUI.fire({
                        icon: 'error',
                        title: 'No Customer Linked',
                        text: 'This quotation is not linked to any customer. Please create customer first.',
                        confirmButtonText: 'Create Customer'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            completeCustomerProfile(quotationId);
                        }
                    });
                    return;
                }
                
                // Extract customer location ID from quotation data
                const customerLocationId = response.data.customer_location_id || response.data.location_id;
                console.log('Ã°Å¸â€œÂ Extracted customer location ID from quotation:', customerLocationId);
                
                // Verify customer exists
                $.get('<?= base_url('marketing/customers/get/') ?>' + response.data.created_customer_id)
                    .done(function(customerResponse) {
                        if (!customerResponse.success) {
                            OptimaUI.fire({
                                icon: 'error',
                                title: 'Customer Not Found',
                                text: 'The linked customer (ID: ' + response.data.created_customer_id + ') no longer exists. Please update quotation.',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }
                        
                        // Customer exists, proceed with contract check, passing location ID
                        checkCustomerContracts(response.data.created_customer_id, quotationId, customerLocationId, 'Please select or create a contract to proceed with SPK creation.');
                    })
                    .fail(function(xhr) {
                        OptimaUI.fire({
                            icon: 'error',
                            title: 'Error Verifying Customer',
                            text: 'Could not verify customer existence. Error: ' + (xhr.responseJSON?.message || xhr.statusText),
                            confirmButtonText: 'OK'
                        });
                    });
            } else {
                OptimaUI.fire('Error', 'Failed to load quotation data', 'error');
            }
        })
        .fail(function() {
            OptimaUI.fire('Error', 'Failed to load quotation details', 'error');
        });
}

function markAsNotDeal(quotationId) {
    OptimaUI.fire({
        title: 'Mark as No Deal?',
        text: 'This will close the quotation permanently.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, No Deal',
        cancelButtonText: window.lang('cancel'),
        customClass: {
            popup: 'swal2-small'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('<?= base_url('marketing/quotations/markAsNotDeal') ?>/' + quotationId, {
                [window.csrfTokenName]: window.csrfToken || '<?= csrf_hash() ?>'
            })
                .done(function(response) {
                    if (response.success) {
                        OptimaUI.fire('Success!', response.message, 'success');
                        
                        // Reload table and statistics
                        quotationsTable.ajax.reload();
                        loadStatistics();
                        
                        // Refresh modal if open
                        if (currentQuotationId == quotationId && $('#detailModal').hasClass('show')) {
                            viewQuotation(quotationId);
                        }
                    } else {
                        OptimaUI.fire('Error', response.message, 'error');
                    }
                })
                .fail(function() {
                    OptimaUI.fire('Error', 'Failed to mark as not deal', 'error');
                });
        }
    });
}

// Compatibility bridge after Deal processing.
function showCustomerLocationModal(customerId, quotationId, dealMessage, needsManualCustomerFallback = false) {
    // Location selection has been moved to Create DI stage.
    // Keep this function as a compatibility bridge so legacy callers do not open old modal.
    const resultText = dealMessage
        ? `${dealMessage} Customer Location sekarang dipilih saat Create DI.`
        : 'Quotation berhasil diproses. Customer Location dipilih saat Create DI.';

    OptimaUI.fire({
        title: needsManualCustomerFallback ? 'Deal Processed with Fallback Available' : 'Success!',
        text: resultText,
        icon: needsManualCustomerFallback ? 'warning' : 'success',
        timer: needsManualCustomerFallback ? 3200 : 2200,
        showConfirmButton: false
    });

    if (typeof quotationsTable !== 'undefined' && quotationsTable) {
        quotationsTable.ajax.reload(null, false);
    }

    if (typeof loadStatistics === 'function') {
        loadStatistics();
    }

    if (currentQuotationId == quotationId && $('#detailModal').hasClass('show')) {
        viewQuotation(quotationId);
    }

    return;

    console.log('=== showCustomerLocationModal ===');
    console.log('Customer ID:', customerId);
    console.log('Quotation ID:', quotationId);
    console.log('Fetching locations from:', '<?= base_url('marketing/customers/getLocations') ?>/' + customerId);
    
    // Get customer's existing locations
    $.get('<?= base_url('marketing/customers/getLocations') ?>/' + customerId)
        .done(function(locationResponse) {
            console.log('Location response received:', locationResponse);
            
            let existingLocations = locationResponse.data || locationResponse.locations || [];
            console.log('Customer locations found:', existingLocations.length, 'locations');
            
            if (existingLocations.length > 0) {
                console.log('Location details:', existingLocations);
            }
            
            // Show location selection modal (customerData will be empty for now)
            showLocationSelectionModal(customerId, quotationId, dealMessage, existingLocations, {});
        })
        .fail(function(xhr, status, error) {
            console.error('=== FAILED to get customer locations ===');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('Response:', xhr.responseText);
            console.error('Status Code:', xhr.status);
            
            OptimaUI.fire({
                title: 'Error Loading Locations',
                text: 'Failed to load customer locations. Error: ' + (xhr.responseText || error),
                icon: 'error',
                confirmButtonText: 'Continue Anyway'
            }).then(() => {
                // Fallback - show unified modal with no existing locations (will show Add New by default)
                showLocationSelectionModal(customerId, quotationId, dealMessage, [], {});
            });
        });
}

// Function to show location selection modal (unified)
function showLocationSelectionModal(customerId, quotationId, dealMessage, locations, customerData) {
    console.log('=== showLocationSelectionModal ===');
    console.log('Received locations:', locations);
    console.log('Locations count:', locations ? locations.length : 0);
    
    customerData = customerData || {};
    
    let locationOptions = '<option value="">-- Select a location --</option>';
    
    // Add existing locations to dropdown if any
    if (locations && locations.length > 0) {
        console.log('Building location options for', locations.length, 'locations');
        locations.forEach(function(location) {
            console.log('Processing location:', location);
            let cityProvince = '';
            if (location.city) cityProvince += location.city;
            if (location.province) cityProvince += (cityProvince ? ', ' : '') + location.province;
            
            let displayText = location.location_name;
            if (cityProvince) displayText += ' (' + cityProvince + ')';
            if (location.contact_person) displayText += ' - ' + location.contact_person;
            
            locationOptions += `<option value="${location.id}">${displayText}</option>`;
        });
        console.log('Final location options HTML:', locationOptions);
    } else {
        console.log('No locations to display');
    }

    console.log('Setting dropdown HTML...');
    $('#modalLocationSelect').html(locationOptions);
    console.log('Dropdown updated. Current dropdown HTML:', $('#modalLocationSelect').html());
    
    // Update alert message and UI based on whether locations exist
    let alertMessage;
    if (locations && locations.length > 0) {
        alertMessage = `<i class="fas fa-map-marker-alt"></i> Customer has <strong>${locations.length}</strong> existing location(s). Select one or add new.`;
        // Show "Select Existing" tab by default
        $('#btnSelectExisting').addClass('active');
        $('#btnAddNew').removeClass('active');
        $('#existingLocationSection').show();
        $('#addNewLocationSection').hide();
    } else {
        alertMessage = '<i class="fas fa-info-circle"></i> No existing locations found. Please add new location.';
        // Show "Add New" tab by default
        $('#btnAddNew').addClass('active');
        $('#btnSelectExisting').removeClass('active');
        $('#existingLocationSection').hide();
        $('#addNewLocationSection').show();
        // Load areas when showing Add New by default
        loadAreasForLocation();
    }
    
    $('#locationModalAlert').html(alertMessage);
    
    // Reset form
    $('#customerLocationFormUnified')[0].reset();
    $('#locationPreview').addClass('d-none');
    
    // Populate form with customer data (read-only fields)
    $('#unified_customer_id').val(customerId);
    
    // Pre-fill contact person, phone, email from customer data if available
    if (customerData.contact_person) {
        $('#unified_contact_person').val(customerData.contact_person).prop('readonly', true).addClass('bg-light');
    } else {
        $('#unified_contact_person').prop('readonly', false).removeClass('bg-light');
    }
    
    if (customerData.phone || customerData.contact_phone) {
        $('#unified_phone').val(customerData.phone || customerData.contact_phone).prop('readonly', true).addClass('bg-light');
    } else {
        $('#unified_phone').prop('readonly', false).removeClass('bg-light');
    }
    
    if (customerData.email || customerData.contact_email) {
        $('#unified_email').val(customerData.email || customerData.contact_email).prop('readonly', true).addClass('bg-light');
    } else {
        $('#unified_email').prop('readonly', false).removeClass('bg-light');
    }
    
    // Pre-fill address data from customer if available
    if (customerData.address) {
        $('#unified_address').val(customerData.address).prop('readonly', true).addClass('bg-light');
    } else {
        $('#unified_address').prop('readonly', false).removeClass('bg-light');
    }
    
    if (customerData.city) {
        $('#unified_city').val(customerData.city).prop('readonly', true).addClass('bg-light');
    } else {
        $('#unified_city').prop('readonly', false).removeClass('bg-light');
    }
    
    if (customerData.province) {
        $('#unified_province').val(customerData.province).prop('readonly', true).addClass('bg-light');
    } else {
        $('#unified_province').prop('readonly', false).removeClass('bg-light');
    }
    
    if (customerData.postal_code) {
        $('#unified_postal_code').val(customerData.postal_code).prop('readonly', true).addClass('bg-light');
    } else {
        $('#unified_postal_code').prop('readonly', false).removeClass('bg-light');
    }
    
    // Store data for later use
    $('#selectCustomerLocationModal').data('customerId', customerId);
    $('#selectCustomerLocationModal').data('quotationId', quotationId);
    $('#selectCustomerLocationModal').data('dealMessage', dealMessage);
    $('#selectCustomerLocationModal').data('existingLocations', locations);
    $('#selectCustomerLocationModal').data('customerData', customerData);
    
    // Set customer name in modal title
    if (customerData && customerData.customer_name) {
        $('#locationModalCustomerName').text('Customer: ' + customerData.customer_name);
    } else {
        $('#locationModalCustomerName').text('');
    }
    
    // CRITICAL: Always reset workflowCompleted to false on modal open
    $('#selectCustomerLocationModal').data('workflowCompleted', false);
    console.log('Ã°Å¸â€â€™ Workflow flag reset to FALSE - location selection required');
    
    // Add modal close prevention
    $('#selectCustomerLocationModal').off('hide.bs.modal');
    $('#selectCustomerLocationModal').on('hide.bs.modal', function(e) {
        // Only allow close if workflow completed
        if (!$(this).data('workflowCompleted')) {
            console.log('Modal close prevented - workflow not completed');
            e.preventDefault();
            
            OptimaUI.fire({
                title: 'Location Required',
                text: 'Please select/save a location to continue, or cancel the deal process.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Continue Selection',
                cancelButtonText: window.lang('cancel_deal'),
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isDismissed || result.dismiss === Swal.DismissReason.cancel) {
                    // User wants to cancel the deal process
                    console.log('Ã¢ÂÅ’ User cancelled location selection - workflow NOT completed');
                    
                    // Remove the hide prevention handler first
                    $('#selectCustomerLocationModal').off('hide.bs.modal');
                    
                    // Keep workflow flag as false
                    $('#selectCustomerLocationModal').data('workflowCompleted', false);
                    
                    // Now force close the modal
                    $('#selectCustomerLocationModal').modal('hide');
                    
                    OptimaUI.fire({
                        title: 'Process Cancelled',
                        text: 'The location selection has been cancelled.',
                        icon: 'info',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }
    });
    
    // Show modal
    $('#selectCustomerLocationModal').modal('show');
}

// Function to load areas for location dropdown
function loadAreasForLocation() {
    console.log('Loading areas for location dropdown...');
    $.get('<?= base_url('marketing/customer-management/getAreas') ?>')
        .done(function(response) {
            console.log('Areas response:', response);
            let options = '<option value="">Select Area</option>';
            
            if (response.success && response.data) {
                response.data.forEach(function(area) {
                    options += `<option value="${area.id}">${area.area_name}</option>`;
                });
                console.log('Areas loaded successfully:', response.data.length, 'areas');
            } else {
                console.error('Invalid areas response format:', response);
            }
            
            $('#unified_area_id').html(options);
        })
        .fail(function(xhr) {
            console.error('Failed to load areas:', xhr.responseText);
            $('#unified_area_id').html('<option value="">Failed to load areas</option>');
        });
}

// Event handlers for Bootstrap modals
// Second $(document).ready() block removed - merged into main block above

// Function to save customer location
function saveCustomerLocation(customerId, locationData, quotationId) {
    locationData.customer_id = customerId;
    locationData.is_primary = 1;
    locationData.is_active = 1;
    locationData.location_type = 'HEAD_OFFICE';
    locationData.quotation_id = quotationId; // CRITICAL: Pass quotation ID for workflow tracking
    locationData.workflow_completed = true; // Flag to indicate modal workflow is complete
    locationData[window.csrfTokenName] = window.csrfToken || '<?= csrf_hash() ?>'; // CSRF protection
    
    console.log('Saving customer location with quotation ID:', locationData);
    
    $.ajax({
        url: '<?= base_url('customers/saveLocation') ?>',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(locationData),
        dataType: 'json'
    })
    .done(function(response) {
            console.log('Location save response:', response);
            if (response.success) {
                // Store location ID globally for contract creation
                if (response.data && response.data.location_id) {
                    window.currentSelectedLocationId = response.data.location_id;
                    console.log('Stored new location ID:', window.currentSelectedLocationId);
                }
                
                // Mark workflow as completed in modal
                $('#selectCustomerLocationModal').data('workflowCompleted', true);
                $('#selectCustomerLocationModal').modal('hide');
                
                OptimaUI.fire({
                    title: 'Location Saved!',
                    text: 'Customer location saved successfully. You can now create SPK from this quotation.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Reload DataTable to reflect updated workflow stage
                if (typeof quotationsTable !== 'undefined' && quotationsTable) {
                    quotationsTable.ajax.reload(null, false);
                }
                
                // Do NOT auto-proceed to contract - let user click button
            } else {
                console.error('Ã¢ÂÅ’ Location save failed:', response.message);
                // Keep modal open and show error
                OptimaUI.fire({
                    title: 'Failed to Save Location',
                    text: response.message || 'Failed to save location. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                });
                // DO NOT close modal - user must fix and retry
            }
        })
        .fail(function(xhr) {
            console.error('Ã¢ÂÅ’ AJAX Failed to save location');
            console.error('Status:', xhr.status);
            console.error('Response:', xhr.responseText);
            console.error('Full XHR:', xhr);
            
            let errorMessage = 'Failed to save customer location. ';
            
            if (xhr.status === 404) {
                errorMessage += 'API endpoint not found (404). Please check route configuration.';
            } else if (xhr.status === 500) {
                errorMessage += 'Server error (500). Check server logs for details.';
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage += xhr.responseJSON.message;
            } else {
                errorMessage += 'Unknown error occurred.';
            }
            
            OptimaUI.fire({
                title: 'Error',
                text: errorMessage,
                icon: 'error',
                confirmButtonText: 'Try Again'
            });
            
            // DO NOT close modal or proceed - user must fix the issue
        });
}

// Function to update customer primary location
function updateCustomerPrimaryLocation(customerId, locationId, quotationId, dealMessage) {
    const updateData = {
        customer_id: customerId,
        location_id: locationId,
        quotation_id: quotationId, // CRITICAL: Pass quotation ID for workflow tracking
        workflow_completed: true, // Flag to indicate modal workflow is complete
        [window.csrfTokenName]: window.csrfToken || '<?= csrf_hash() ?>' // Add CSRF token
    };
    
    // Store location globally for contract creation
    window.currentSelectedLocationId = locationId;
    
    $.ajax({
        url: '<?= base_url('customers/setPrimaryLocation') ?>',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(updateData),
        dataType: 'json'
    })
    .done(function(response) {
        console.log('Primary location update response:', response);
        if (response.success) {
            // Mark workflow as completed in modal
            $('#selectCustomerLocationModal').data('workflowCompleted', true);
            $('#selectCustomerLocationModal').modal('hide');
            
            OptimaUI.fire({
                title: 'Location Selected!', 
                text: 'Location selected successfully. You can now create SPK from this quotation.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
            
            // Reload DataTable to reflect updated workflow stage
            if (typeof quotationsTable !== 'undefined' && quotationsTable) {
                quotationsTable.ajax.reload(null, false);
            }
            
            // Do NOT auto-proceed to contract - let user click button
        } else {
            console.error('Ã¢ÂÅ’ Primary location update failed:', response.message);
            OptimaUI.fire({
                title: 'Failed to Update Location',
                text: response.message || 'Failed to update primary location. Please try again.',
                icon: 'error',
                confirmButtonText: 'Try Again'
            });
            // DO NOT close modal
        }
    })
    .fail(function(xhr) {
        console.error('Ã¢ÂÅ’ AJAX Failed to update primary location');
        console.error('Status:', xhr.status);
        console.error('Response:', xhr.responseText);
        console.error('Full XHR:', xhr);
        
        let errorMessage = 'Failed to update customer primary location. ';
        
        if (xhr.status === 404) {
            errorMessage += 'API endpoint not found (404). Please check route configuration.';
        } else if (xhr.status === 500) {
            errorMessage += 'Server error (500). Check server logs for details.';
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage += xhr.responseJSON.message;
        } else {
            errorMessage += 'Unknown error occurred.';
        }
        
        OptimaUI.fire({
            title: 'Error',
            text: errorMessage,
            icon: 'error',
            confirmButtonText: 'Try Again'
        });
        
        // DO NOT close modal or proceed
    });
}

// Function to check customer contracts and proceed to next workflow
function checkCustomerContracts(customerId, quotationId, customerLocationId, message) {
    console.log('Checking customer contracts for customer:', customerId, 'location:', customerLocationId);
    
    // Directly show create/select contract modal (unified modal)
    showCreateContractModal(customerId, quotationId, customerLocationId, message);
}

// Function to show create contract modal
function showCreateContractModal(customerId, quotationId, customerLocationId, message) {
    console.log('showCreateContractModal called:', {customerId, quotationId, customerLocationId, message});
    
    // Store in global variables for contract form
    window.currentContractQuotationId = quotationId;
    window.currentContractCustomerId = customerId;
    window.currentSelectedLocationId = customerLocationId; // Store location ID
    
    console.log('Ã¢Å“â€¦ Stored location ID in window.currentSelectedLocationId:', customerLocationId);
    
    // Show modal immediately with loading state
    $('#addContractModal').modal('show');
    
    // Set dropdown to "Add New Contract" by default and show form
    $('#contractOrPOSelect').val('__ADD_NEW__');
    $('#contractFormSection').show();
    
    // Enable all fields for new entry
    $('#contract_number_input, #po_number_input').prop('disabled', false).prop('readonly', false).removeClass('bg-light');
    $('#contract_start_date, #contract_end_date, #contract_jenis_sewa').prop('disabled', false);
    $('#contract_catatan').prop('readonly', false).removeClass('bg-light');
    $('#generateContractNumberBtn').prop('disabled', false);
    
    // Change button text
    $('#addContractForm button[type="submit"]').html('<i class="fas fa-save me-2"></i>Save New Contract');
    
    // Load customer and location data
    loadCustomersForContract(customerId);
    
    // Update modal title
    $.ajax({
        url: `<?= base_url('marketing/customers/get/') ?>${customerId}`,
        method: 'GET',
        success: function(response) {
            if (response.success && response.data) {
                const customerName = response.data.customer_name;
                $('#contractModalCustomerName').text('Customer: ' + customerName);
                console.log('Customer name set in contract modal:', customerName);
            }
        },
        error: function(xhr) {
            console.error('Error loading customer for contract modal:', xhr);
            $('#contractModalCustomerName').text('Customer: Unknown');
        }
    });
}

// Function to load customer and location data for contract modal (read-only)
function loadCustomersForContract(customerId) {
    console.log('Loading customer and location data for contract modal:', customerId);
    
    const quotationId = window.currentContractQuotationId;
    const locationId = window.currentSelectedLocationId;
    
    // Set quotation ID and customer ID
    $('#contractQuotationId').val(quotationId);
    $('#customerIdContractNew').val(customerId);
    
    // Load existing contracts for this customer
    loadExistingContracts(customerId);
    
    // Get quotation data to populate customer and location
    $.ajax({
        url: `<?= base_url('marketing/quotations/get-quotation/') ?>${quotationId}`,
        method: 'GET',
        success: function(quotationResponse) {
            console.log('Ã°Å¸â€œÂ¥ Quotation data loaded for contract:', quotationResponse);
            
            if (quotationResponse.status === 'success' || quotationResponse.success) {
                const quotation = quotationResponse.data || quotationResponse;
                
                // Set customer name from quotation (prospect_name or customer_name)
                const customerName = quotation.customer_name || quotation.prospect_name || 'N/A';
                
                // Use setTimeout to ensure field is rendered before setting value
                setTimeout(function() {
                    $('#customerNameDisplay').val(customerName);
                    console.log('Ã¢Å“â€¦ Customer name set:', customerName, '| Field value:', $('#customerNameDisplay').val());
                }, 100);
                
                // Set total units for daily rate calculator
                const totalUnits = quotation.total_units || quotation.total_quantity || 0;
                $('#calc_total_units').val(totalUnits);
                console.log('Ã¢Å“â€¦ Total units set for calculator:', totalUnits);
                
                // Set location name if available
                if (quotation.location_name) {
                    setTimeout(function() {
                        $('#locationNameDisplay').val(quotation.location_name);
                        console.log('Ã¢Å“â€¦ Location name set:', quotation.location_name, '| Field value:', $('#locationNameDisplay').val());
                    }, 100);
                    
                    // If we have location from quotation, also set the location ID
                    if (locationId) {
                        $('#locationIdContractNew').val(locationId);
                    }
                } else if (locationId) {
                    // If no location in quotation but we have locationId, fetch location details
                    $.ajax({
                        url: `<?= base_url('marketing/customers/getLocations/') ?>${customerId}`,
                        method: 'GET',
                        success: function(locResponse) {
                            console.log('Ã°Å¸â€œÂ¥ Locations loaded:', locResponse);
                            if (locResponse.success && locResponse.data) {
                                const selectedLocation = locResponse.data.find(loc => loc.id == locationId);
                                if (selectedLocation) {
                                    setTimeout(function() {
                                        $('#locationNameDisplay').val(selectedLocation.location_name);
                                        $('#locationIdContractNew').val(selectedLocation.id);
                                        console.log('Ã¢Å“â€¦ Location set from customer locations:', selectedLocation.location_name);
                                    }, 100);
                                }
                            }
                        },
                        error: function(xhr) {
                            console.error('Ã¢ÂÅ’ Error loading locations:', xhr);
                        }
                    });
                } else {
                    setTimeout(function() {
                        $('#locationNameDisplay').val('No location selected');
                        console.warn('Ã¢Å¡Â Ã¯Â¸Â No location available');
                    }, 100);
                }
            } else {
                console.error('Ã¢ÂÅ’ Invalid quotation response');
            }
        },
        error: function(xhr) {
            console.error('Ã¢ÂÅ’ Error loading quotation for contract:', xhr);
            const errorMsg = xhr.responseJSON?.message || xhr.statusText || 'Unknown error';
            console.error('Quotation error details:', errorMsg);
            
            // Fallback: try to get customer data directly
            $.ajax({
                url: `<?= base_url('marketing/customers/get/') ?>${customerId}`,
                method: 'GET',
                success: function(custResponse) {
                    if (custResponse.success && custResponse.data) {
                        setTimeout(function() {
                            $('#customerNameDisplay').val(custResponse.data.customer_name);
                            console.log('Ã¢Å“â€¦ Customer name set from customer API:', custResponse.data.customer_name);
                        }, 100);
                    }
                },
                error: function(xhr) {
                    console.error('Ã¢ÂÅ’ Error loading customer:', xhr);
                }
            });
        }
    });
}

// Function to load existing contracts for customer
function loadExistingContracts(customerId) {
    console.log('=== loadExistingContracts START ===');
    console.log('Customer ID:', customerId);
    console.log('API URL:', `<?= base_url('marketing/customers/getContracts/') ?>${customerId}`);
    
    $.ajax({
        url: `<?= base_url('marketing/customers/getContracts/') ?>${customerId}`,
        method: 'GET',
        dataType: 'json',
        beforeSend: function(xhr) {
            console.log('Sending AJAX request for contracts...');
        },
        success: function(response) {
            console.log('Ã¢Å“â€¦ Contracts API Response:', response);
            console.log('Response type:', typeof response);
            console.log('Has contracts array?', response.hasOwnProperty('contracts'));
            
            const dropdown = $('#contractOrPOSelect');
            console.log('Dropdown element found:', dropdown.length > 0);
            
            // Keep the default option and "+ Tambah Kontrak Baru"
            dropdown.find('option:not([value=""]):not([value="__ADD_NEW__"])').remove();
            
            if (response.success && response.contracts && response.contracts.length > 0) {
                console.log(`Ã¢Å“â€¦ Found ${response.contracts.length} existing contracts:`, response.contracts);
                
                // Add existing contracts before the "+ Tambah Kontrak Baru" option
                const addNewOption = dropdown.find('option[value="__ADD_NEW__"]').detach();
                console.log('Removed "Add New" option temporarily');
                
                response.contracts.forEach((contract, index) => {
                    const displayText = `${contract.no_kontrak || contract.customer_po_number || 'N/A'} - ${contract.tanggal_mulai || ''} s/d ${contract.tanggal_berakhir || ''}`;
                    console.log(`Adding contract ${index + 1}:`, displayText);
                    
                    const option = $('<option></option>')
                        .val(contract.id)
                        .text(displayText)
                        .data('contract', contract);
                    dropdown.append(option);
                });
                
                // Re-add the "+ Tambah Kontrak Baru" option at the end
                dropdown.append(addNewOption);
                console.log('Re-added "Add New" option');
                
                console.log('Ã¢Å“â€¦ Contracts populated in dropdown. Total options:', dropdown.find('option').length);
            } else {
                console.warn('Ã¢Å¡Â Ã¯Â¸Â No existing contracts found or empty response');
                console.log('Response details - success:', response.success, 'contracts:', response.contracts);
            }
            console.log('=== loadExistingContracts END ===');
        },
        error: function(xhr, status, error) {
            console.error('Ã¢ÂÅ’ Error loading contracts');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('XHR:', xhr);
            console.error('Response Text:', xhr.responseText);
            
            const errorMsg = xhr.responseJSON?.message || xhr.statusText || 'Unknown error';
            console.error('Contract loading error details:', errorMsg);
            
            // Show user-friendly message
            OptimaUI.fire({
                icon: 'info',
                title: 'No Existing Contracts',
                text: 'Could not load existing contracts. You can create a new contract.',
                timer: 2000,
                showConfirmButton: false
            });
            
            console.log('=== loadExistingContracts END (with error) ===');
        }
    });
}

// Function to reset contract form to empty state
function resetContractForm() {
    // Clear all field values
    $('#contract_number_input').val('');
    $('#po_number_input').val('');
    $('#contract_start_date').val('');
    $('#contract_end_date').val('');
    $('#contract_jenis_sewa').val('');
    $('#contract_catatan').val('');
    $('#selectedContractId').val('');
    $('#customerNameDisplay').val('');
    $('#locationNameDisplay').val('');
    
    // Remove readonly state and styling
    $('#contract_number_input, #po_number_input, #customerNameDisplay, #locationNameDisplay').prop('readonly', false).removeClass('bg-light');
    $('#generateContractNumberBtn').prop('disabled', false);
}

// Function to populate contract form with existing contract data
function populateContractForm(contractData) {
    console.log('Populating contract form with:', contractData);
    
    // Basic contract fields
    $('#contract_number_input').val(contractData.no_kontrak || '');
    $('#po_number_input').val(contractData.customer_po_number || '');
    $('#contract_start_date').val(contractData.tanggal_mulai || '');
    $('#contract_end_date').val(contractData.tanggal_berakhir || '');
    $('#contract_jenis_sewa').val(contractData.jenis_sewa || '');
    $('#contract_catatan').val(contractData.catatan || '');
    $('#selectedContractId').val(contractData.id || '');
    
    // Customer and Location fields
    $('#customerIdContract').val(contractData.customer_id || '');
    $('#customerNameDisplay').val(contractData.customer_name || contractData.nama_customer || '');
    
    $('#locationIdContractNew').val(contractData.location_id || '');
    $('#locationNameDisplay').val(contractData.location_name || contractData.alamat || '');
    
    console.log('Ã¢Å“â€¦ Contract form populated with customer:', contractData.customer_name, 'location:', contractData.location_name);
}

// Function to search and load contract by contract number or PO number
function searchAndLoadContract(field, value) {
    const customerId = window.currentContractCustomerId;
    
    console.log(`Searching contract by ${field}:`, value);
    
    $.ajax({
        url: `<?= base_url('customers/searchContract') ?>`,
        method: 'POST',
        data: {
            customer_id: customerId,
            field: field,
            value: value,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.success && response.data) {
                console.log('Contract found:', response.data);
                populateContractForm(response.data);
                
                // Update dropdown to show this contract is selected
                const contractId = response.data.id;
                if ($(`#contractOrPOSelect option[value="${contractId}"]`).length > 0) {
                    $('#contractOrPOSelect').val(contractId).trigger('change');
                }
                
                OptimaUI.fire({
                    icon: 'info',
                    title: 'Contract Found',
                    text: 'Existing contract data loaded',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                console.log('No matching contract found');
            }
        },
        error: function(xhr) {
            console.error('Error searching contract:', xhr);
        }
    });
}

// Function to save new contract
function saveNewContract(formData) {
    console.log('Saving new contract:', formData);
    
    $.ajax({
        url: '<?= base_url('marketing/kontrak/store') ?>',
        method: 'POST',
        data: {
            ...formData,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        },
        success: function(response) {
            console.log('Contract save response:', response);
            
            if (response.success) {
                OptimaUI.fire({
                    title: 'Success!',
                    text: 'Contract created successfully',
                    icon: 'success',
                    timer: 2000
                });
                
                // Update quotation contract complete flag
                updateQuotationContractComplete(formData.quotation_id);
                
                // Close modal and refresh
                $('#addContractModal').modal('hide');
                
                // Refresh quotations table
                if (typeof quotationsTable !== 'undefined' && quotationsTable) {
                    quotationsTable.ajax.reload(null, false);
                }
            } else {
                OptimaUI.fire('Error', response.message || 'Failed to save contract', 'error');
            }
        },
        error: function(xhr) {
            console.error('Error saving contract:', xhr);
            OptimaUI.fire('Error', 'Failed to save contract: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
        }
    });
}

// Function to link existing contract to quotation (Option 1: Pure Selection - No Update)
function linkExistingContract(contractId, quotationId) {
    console.log('Linking existing contract to quotation:', {contractId, quotationId});
    
    // Simply update quotation to mark contract as complete
    // No changes to the contract itself
    $.ajax({
        url: `<?= base_url('marketing/quotations/linkContract') ?>`,
        method: 'POST',
        data: {
            quotation_id: quotationId,
            contract_id: contractId,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        },
        success: function(response) {
            console.log('Contract link response:', response);
            
            if (response.success) {
                OptimaUI.fire({
                    title: 'Success!',
                    text: 'Contract linked to quotation successfully',
                    icon: 'success',
                    timer: 2000
                });
                
                // Update quotation contract complete flag
                updateQuotationContractComplete(quotationId);
                
                // Close modal and refresh
                $('#addContractModal').modal('hide');
                
                // Refresh quotations table
                if (typeof quotationsTable !== 'undefined' && quotationsTable) {
                    quotationsTable.ajax.reload(null, false);
                }
            } else {
                OptimaUI.fire('Error', response.message || 'Failed to link contract', 'error');
            }
        },
        error: function(xhr) {
            console.error('Error linking contract:', xhr);
            OptimaUI.fire('Error', 'Failed to link contract: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
        }
    });
}

// Function to update quotation contract complete flag
function updateQuotationContractComplete(quotationId) {
    console.log('Updating quotation contract complete flag:', quotationId);
    
    $.ajax({
        url: '<?= base_url('marketing/quotations/updateContractComplete') ?>',
        method: 'POST',
        data: {
            quotation_id: quotationId,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        },
        success: function(response) {
            console.log('Quotation contract flag updated:', response);
            if (response.success) {
                OptimaUI.fire({
                    title: 'Workflow Updated!',
                    text: 'Contract stage completed. You can now create SPK.',
                    icon: 'success',
                    timer: 2000
                });
            }
        },
        error: function(xhr) {
            console.error('Error updating contract flag:', xhr);
        }
    });
}

// Function to complete deal workflow (with contract selected)
function completeDealWorkflow(quotationId, successMessage) {
    OptimaUI.fire({
        title: 'Workflow Completed!',
        text: successMessage,
        icon: 'success',
        timer: 2000
    });
    
    // Refresh quotations table to update action buttons
    if (typeof quotationsTable !== 'undefined' && quotationsTable) {
        quotationsTable.ajax.reload(null, false); // Maintain current page
    }
    
    // Load statistics
    loadStatistics();
    
    // Refresh modal if open
    if (typeof currentQuotationId !== 'undefined' && currentQuotationId == quotationId && $('#detailModal').hasClass('show')) {
        setTimeout(() => {
            viewQuotation(quotationId);
        }, 500);
    }
    
    // Close any open modals
    $('.modal').modal('hide');
}

// Function to complete deal workflow with table update (when workflow done)
function completeDealWorkflowWithUpdate(quotationId, successMessage) {
    OptimaUI.fire({
        title: 'Deal Completed!',
        text: successMessage,
        icon: 'success',
        timer: 3000
    });
    
    // Refresh quotations table to update action buttons
    if (typeof quotationsTable !== 'undefined' && quotationsTable) {
        quotationsTable.ajax.reload(null, false);
    }
    
    // Load statistics  
    loadStatistics();
    
    // Refresh modal if open
    if (typeof currentQuotationId !== 'undefined' && currentQuotationId == quotationId && $('#detailModal').hasClass('show')) {
        setTimeout(() => {
            viewQuotation(quotationId);
        }, 500);
    }
    
    // Close any open modals
    $('.modal').modal('hide');
}

function createCustomerFromDeal(quotationId) {
    OptimaUI.fire({
        title: 'Run Customer Fallback?',
        text: 'Use this manual fallback only if automatic customer creation during Deal did not succeed.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Create Customer',
        cancelButtonText: window.lang('cancel')
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('<?= base_url('marketing/quotations/createCustomerFromDeal') ?>/' + quotationId, {
                [window.csrfTokenName]: window.csrfToken || '<?= csrf_hash() ?>'
            })
                .done(function(response) {
                    if (response.success) {
                        OptimaUI.fire({
                            title: 'Customer Fallback Completed',
                            text: response.message,
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'View Customer',
                            cancelButtonText: window.lang('continue')
                        }).then((result) => {
                            if (result.isConfirmed && response.customer_id) {
                                // Redirect to customer detail page
                                window.open('<?= base_url('customer-management/showCustomer/') ?>' + response.customer_id, '_blank');
                            }
                        });
                        
                        // Reload table and statistics
                        quotationsTable.ajax.reload();
                        loadStatistics();
                        
                        // Refresh modal if open
                        if (currentQuotationId == quotationId && $('#detailModal').hasClass('show')) {
                            viewQuotation(quotationId);
                        }
                    } else {
                        OptimaUI.fire('Error', response.message, 'error');
                    }
                })
                .fail(function() {
                    OptimaUI.fire('Error', 'Failed to run customer fallback', 'error');
                });
        }
    });
}

function createContract(quotationId) {
    // Get quotation data first to get customer ID
    $.get('<?= base_url('marketing/quotations/detail') ?>/' + quotationId)
        .done(function(quotationResponse) {
            if (!quotationResponse.success || !quotationResponse.quotation.created_customer_id) {
                OptimaUI.fire('Error', 'Customer must be created before contract. Please mark as deal first.', 'error');
                return;
            }
            
            const customerId = quotationResponse.quotation.created_customer_id;
            
            // Check customer profile completion
            $.get('<?= base_url('marketing/quotations/getCustomerProfileStatus') ?>/' + customerId)
                .done(function(profileResponse) {
                    if (profileResponse.success && !profileResponse.profile_status.complete) {
                        // Customer profile is not complete
                        OptimaUI.fire({
                            title: 'Customer Profile Incomplete',
                            html: `
                                <div class="text-left">
                                    <p class="mb-2">Customer profile must be completed before creating contract.</p>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> 
                                        Customer: <strong>${profileResponse.profile_status.customer_name}</strong>
                                    </div>
                                    <p class="small text-muted">Please complete customer address, contact details, and location information.</p>
                                </div>
                            `,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Complete Profile',
                            cancelButtonText: window.lang('cancel'),
                            confirmButtonColor: '#3085d6'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Open customer edit page in new tab
                                const customerEditUrl = '<?= base_url('customers/edit') ?>/' + customerId;
                                window.open(customerEditUrl, '_blank');
                            }
                        });
                        return;
                    }
                    
                    // Profile is complete, proceed with contract creation
                    OptimaUI.fire({
                        title: 'Create Contract & PO?',
                        text: 'This will create contract and purchase order documents.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Create',
                        cancelButtonText: window.lang('cancel')
                    }).then((result) => {
                        if (result.isConfirmed) {
                            proceedWithContractCreation(quotationId);
                        }
                    });
                })
                .fail(function() {
                    OptimaUI.fire('Error', 'Failed to check customer profile status', 'error');
                });
        })
        .fail(function() {
            OptimaUI.fire('Error', 'Failed to get quotation details', 'error');
        });
}

function proceedWithContractCreation(quotationId) {
    $.post('<?= base_url('marketing/quotations/createContract') ?>/' + quotationId, {
        [window.csrfTokenName]: window.csrfToken || '<?= csrf_hash() ?>'
    })
        .done(function(response) {
            if (response.success) {
                OptimaUI.fire({
                    title: 'Contract Created!',
                    text: response.message,
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'View Contract',
                    cancelButtonText: window.lang('continue')
                }).then((result) => {
                    if (result.isConfirmed && response.contract_id) {
                        // Redirect to contract detail page
                        window.open('<?= base_url('contracts/detail') ?>/' + response.contract_id, '_blank');
                    }
                });
                
                // Reload table to show updated actions
                quotationsTable.ajax.reload();
                
                // Refresh modal if open
                if (currentQuotationId == quotationId && $('#detailModal').hasClass('show')) {
                    viewQuotation(quotationId);
                }
            } else {
                if (response.require_profile_completion) {
                    OptimaUI.fire({
                        title: 'Customer Profile Required',
                        text: response.message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Complete Profile',
                        cancelButtonText: window.lang('cancel')
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const customerEditUrl = '<?= base_url('customers/edit') ?>/' + response.customer_id;
                            window.open(customerEditUrl, '_blank');
                        }
                    });
                } else {
                    OptimaUI.fire('Error', response.message, 'error');
                }
            }
        })
        .fail(function() {
            OptimaUI.fire('Error', 'Failed to create contract', 'error');
        });
}

function createSPK(quotationId) {
    // New workflow: Direct SPK creation without contract requirement
    $.get('<?= base_url('marketing/quotations/getQuotation') ?>/' + quotationId)
        .done(function(quotationResponse) {
            if (!quotationResponse.success || !quotationResponse.data || !quotationResponse.data.created_customer_id) {
                OptimaUI.fire('Error', 'Customer must be created before SPK. Please mark as deal first.', 'error');
                return;
            }
            
            const quotation = quotationResponse.data;
            
            // CONTRACT NOT REQUIRED - Simplified workflow
            // Proceed to SPK creation modal with specification selection
            createSPKFromQuotation(quotationId);
        })
        .fail(function() {
            OptimaUI.fire('Error', 'Failed to get quotation details', 'error');
        });
}

function proceedWithSPKCreation(quotationId) {
    // Redirect to new SPK creation modal with specification selection
    createSPKFromQuotation(quotationId);
}

function addSpecifications(quotationId) {
    // Open the specifications modal directly
    $.ajax({
        url: '<?= base_url('marketing/quotations/addSpecifications') ?>/' + quotationId,
        type: 'POST',
        data: {
            [window.csrfTokenName]: window.csrfToken || '<?= csrf_hash() ?>'
        },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.open_specifications) {
                // Open the quotation detail modal and switch to specifications tab
                viewQuotation(quotationId);
                
                setTimeout(() => {
                    $('#specifications-tab').click();
                    
                    // Show helpful message
                    OptimaUI.fire({
                        title: 'Add Specifications',
                        text: 'Please add at least one specification before sending the quotation.',
                        icon: 'info',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }, 1000);
            } else {
                OptimaUI.fire('Error', response.message || 'Failed to open specifications', 'error');
            }
        },
        error: function() {
            OptimaUI.fire('Error', 'Failed to open specifications', 'error');
        }
    });
}

// Legacy function for backward compatibility
function createCustomer(quotationId) {
    return createCustomerFromDeal(quotationId);
}

// ===== TESTING & DEBUG FUNCTIONS =====

function testWorkflowComplete() {
    console.log('=== TESTING COMPLETE WORKFLOW ===');
    
    // Test 1: Check if quotations table is loaded
    console.log('Test 1: DataTable status');
    if (typeof quotationsTable !== 'undefined') {
        console.log('Ã¢Å“â€¦ DataTable initialized');
    } else {
        console.log('Ã¢ÂÅ’ DataTable not initialized');
        return;
    }
    
    // Test 2: Check if API endpoints are reachable
    console.log('Test 2: Testing API endpoints');
    
    // Test departemen endpoint
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=departemen')
        .done(function(response) {
            console.log('Ã¢Å“â€¦ Departemen API:', response.success ? 'OK' : 'FAILED');
            console.log('   - Data count:', response.data ? response.data.length : 0);
        })
        .fail(function() {
            console.log('Ã¢ÂÅ’ Departemen API: FAILED');
        });
    
    // Test tipe unit endpoint  
    $.get('<?= base_url('marketing/customer-management/getTipeUnit') ?>')
        .done(function(response) {
            console.log('Ã¢Å“â€¦ Tipe Unit API:', response.success ? 'OK' : 'FAILED');
            console.log('   - Data count:', response.data ? response.data.length : 0);
        })
        .fail(function() {
            console.log('Ã¢ÂÅ’ Tipe Unit API: FAILED');
        });
    
    // Test 3: Check workflow stage functions
    console.log('Test 3: Workflow functions');
    const workflowFunctions = ['convertToQuotation', 'openSpecificationsModal', 'sendQuotation', 'markAsDeal', 'markAsNotDeal', 'createCustomer'];
    workflowFunctions.forEach(func => {
        if (typeof window[func] === 'function') {
            console.log('Ã¢Å“â€¦', func, 'function available');
        } else {
            console.log('Ã¢ÂÅ’', func, 'function missing');
        }
    });
    
    // Test 4: Check modal elements
    console.log('Test 4: Modal elements');
    const modals = ['#createProspectModal', '#addSpecificationModal', '#detailModal'];
    modals.forEach(modal => {
        if ($(modal).length > 0) {
            console.log('Ã¢Å“â€¦', modal, 'exists');
        } else {
            console.log('Ã¢ÂÅ’', modal, 'missing');
        }
    });
    
    console.log('=== WORKFLOW TEST COMPLETED ===');
}

// Function to simulate complete workflow for testing
function simulateWorkflow(prospectName = 'Test Prospect ' + Date.now()) {
    console.log('=== SIMULATING COMPLETE WORKFLOW ===');
    
    OptimaUI.fire({
        title: 'Start Workflow Simulation?',
        text: 'This will create a test prospect and walk through the complete quotation workflow.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Start Simulation',
        cancelButtonText: window.lang('cancel')
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('Starting workflow simulation...');
            
            // Step 1: Open create prospect modal
            console.log('Step 1: Opening prospect creation modal');
            openCreateProspectModal();
            
            // Auto-fill form for testing (with delay to ensure modal is open)
            setTimeout(() => {
                console.log('Step 2: Auto-filling prospect form');
                $('#prospectName').val(prospectName);
                $('#prospectEmail').val('test@example.com');
                $('#prospectPhone').val('08123456789');
                $('#prospectCompany').val('Test Company');
                $('#prospectAddress').val('Test Address');
                $('#prospectNotes').val('Created via workflow simulation');
                
                console.log('Ã¢Å“â€¦ Prospect form filled. Please click "Create Prospect" to continue.');
                console.log('Next steps:');
                console.log('1. Click Create Prospect');
                console.log('2. Convert to Quotation');
                console.log('3. Add Specifications');
                console.log('4. Send Quotation');
                console.log('5. Mark as Deal/Not Deal');
            }, 500);
        }
    });
}

// Quick test functions for individual components
function testProspectCreation() {
    console.log('Testing prospect creation...');
    openCreateProspectModal();
}

function testSpecificationModal() {
    if (!currentQuotationId) {
        OptimaUI.fire('Warning', 'Please select a quotation first', 'warning');
        return;
    }
    console.log('Testing specification modal...');
    openAddSpecificationModal();
}

function testCascadingDropdowns() {
    console.log('Testing cascading dropdowns...');
    if (!currentQuotationId) {
        OptimaUI.fire('Info', 'Creating test quotation first...', 'info');
        testProspectCreation();
        return;
    }
    
    openAddSpecificationModal();
    
    setTimeout(() => {
        loadDepartemenForSpecification();
        loadTipeUnitForSpecification();
    }, 500);
}

// Smart Customer Search Functions
function initCustomerSearch() {
    $('#linkExistingCustomer').on('change', function() {
        if (this.checked) {
            $('#customerSearchSection').slideDown();
            $('#newCustomerSection').slideUp();
            // Remove required attributes from hidden fields
            $('#prospectCompanyName').removeAttr('required');
            $('#prospectContactPerson').removeAttr('required');
        } else {
            $('#customerSearchSection').slideUp();
            $('#newCustomerSection').slideDown();
            // Restore required attributes for visible fields
            $('#prospectCompanyName').attr('required', 'required');
            $('#prospectContactPerson').attr('required', 'required');
            $('#selectedCustomerId').val('');
        }
    });
    
    // Auto-search functionality with debouncing
    let searchTimer;
    $('#customerSearchInput').on('input', function() {
        const searchTerm = $(this).val().trim();
        
        // Clear previous timer
        clearTimeout(searchTimer);
        
        // Clear results if search term is too short
        if (searchTerm.length === 0) {
            $('#customerSearchResults').html('');
            return;
        }
        
        if (searchTerm.length < 2) {
            $('#customerSearchResults').html('<div class="alert alert-info"><i class="fas fa-keyboard me-2"></i>Keep typing... (minimum 2 characters)</div>');
            return;
        }
        
        // Debounce search - wait 500ms after user stops typing
        searchTimer = setTimeout(function() {
            searchCustomers();
        }, 500);
    });
    
    // Keep the manual search button functional
    $('#searchCustomerBtn').on('click', function() {
        clearTimeout(searchTimer);
        searchCustomers();
    });
    
    // Handle Enter key
    $('#customerSearchInput').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimer);
            searchCustomers();
        }
    });
}

function searchCustomers() {
    const searchTerm = $('#customerSearchInput').val().trim();
    if (searchTerm.length < 2) {
        $('#customerSearchResults').html('<div class="alert alert-info"><i class="fas fa-keyboard me-2"></i>Please enter at least 2 characters</div>');
        return;
    }
    
    // Show loading indicator with smaller spinner for auto-search
    $('#customerSearchResults').html(`
        <div class="d-flex align-items-center py-2 px-3 bg-light rounded">
            <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <small class="text-muted">Searching for "${searchTerm}"...</small>
        </div>
    `);
    
    $.ajax({
        url: '<?= base_url('marketing/customer-management/searchCustomers') ?>',
        method: 'POST',
        data: { 
            search: searchTerm,
            [window.csrfTokenName]: window.csrfToken || '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.success && response.data.length > 0) {
                let html = '<div class="list-group mt-2">';
                response.data.forEach(function(customer) {
                    const locationDetails = customer.primary_location ? 
                        `<i class="fas fa-map-marker-alt me-1"></i>${customer.primary_location}` : 
                        `<i class="fas fa-building me-1"></i>${customer.location_count} location(s)`;
                    
                    const contactInfo = customer.primary_contact ? 
                        `<i class="fas fa-user me-1"></i>${customer.primary_contact}` + 
                        (customer.primary_phone ? ` <i class="fas fa-phone ms-2 me-1"></i>${customer.primary_phone}` : '') : '';
                    
                    const addressPreview = customer.primary_address ? 
                        `<small class="d-block text-muted mt-1"><i class="fas fa-home me-1"></i>${customer.primary_address.length > 80 ? customer.primary_address.substring(0, 80) + '...' : customer.primary_address}</small>` : '';
                    
                    const locationsPreview = customer.locations_summary ? 
                        `<small class="d-block text-primary mt-1"><i class="fas fa-list me-1"></i>Locations: ${customer.locations_summary}</small>` : '';
                    
                    html += `
                        <div class="list-group-item list-group-item-action customer-search-item" 
                             data-customer-id="${customer.id}" 
                             data-customer-name="${customer.customer_name}"
                             data-customer-code="${customer.customer_code}">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">${customer.customer_name}</h6>
                                <small class="text-muted">${customer.customer_code}</small>
                            </div>
                            <p class="mb-1">${locationDetails}</p>
                            ${contactInfo ? `<p class="mb-1 text-info">${contactInfo}</p>` : ''}
                            ${addressPreview}
                            ${locationsPreview}
                        </div>
                    `;
                });
                html += '</div>';
                $('#customerSearchResults').html(html);
                
                $('.customer-search-item').on('click', function() {
                    const customerId = $(this).data('customer-id');
                    const customerName = $(this).data('customer-name');
                    const customerCode = $(this).data('customer-code');
                    
                    $('#selectedCustomerId').val(customerId);
                    $('#customerSearchInput').val(`${customerName} (${customerCode})`);
                    $('#customerSearchResults').html(`
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>Selected: <strong>${customerName}</strong> 
                            <small class="text-muted">(${customerCode})</small>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="clearCustomerSelection()">
                                <i class="fas fa-times"></i> Clear
                            </button>
                            <div class="mt-2">
                                <small class="text-info">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Customer data will be automatically populated in the quotation.
                                </small>
                            </div>
                        </div>
                    `);
                });
            } else {
                $('#customerSearchResults').html(`
                    <div class="alert alert-info">
                        <i class="fas fa-search me-2"></i>No customers found matching "<strong>${searchTerm}</strong>"
                        <div class="mt-2">
                            <small class="d-block">We searched in: Customer names, codes, location names, and cities</small>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="switchToNewCustomer()">
                                <i class="fas fa-plus me-1"></i>Create new customer instead
                            </button>
                        </div>
                    </div>
                `);
            }
        },
        error: function(xhr) {
            console.warn('Search error:', xhr);
            $('#customerSearchResults').html(`
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Unable to search right now. Please try again.
                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="searchCustomers()">
                        <i class="fas fa-redo me-1"></i>Retry
                    </button>
                </div>
            `);
        }
    });
}

function clearCustomerSelection() {
    $('#selectedCustomerId').val('');
    $('#customerSearchInput').val('');
    $('#customerSearchResults').html('');
}

function switchToNewCustomer() {
    $('#linkExistingCustomer').prop('checked', false).trigger('change');
}

// Reset tabs when modal is hidden
$(document).on('hidden.bs.modal', '#detailModal', function () {
    // Reset to default state - quotation info tab active
    $('#quotation-info-tab').addClass('active').attr('aria-selected', 'true');
    $('#specifications-tab').removeClass('active').attr('aria-selected', 'false');
    $('#quotation-info-content').addClass('show active');
    $('#specifications-content').removeClass('show active');
    
    // Clear current quotation ID
    currentQuotationId = null;
    
    // Unbind the shown.bs.modal event to prevent multiple bindings
    $('#detailModal').off('shown.bs.modal');
});

// Function to create contract for quotation
function createContractForQuotation(quotationId) {
    // Show loading
    OptimaPro.showLoading('Creating contract...');
    
    $.ajax({
        url: `<?= base_url('marketing/quotations/createContract/') ?>${quotationId}`,
        method: 'POST',
        success: function(response) {
            OptimaPro.hideLoading();
            if (response.success) {
                OptimaUI.fire({
                    title: 'Success!',
                    html: `Contract created successfully!<br><strong>${response.contract_number}</strong>`,
                    icon: 'success',
                    confirmButtonText: 'Continue to SPK'
                }).then(() => {
                    // Retry opening SPK modal
                    createSPKFromQuotation(quotationId);
                });
            } else {
                OptimaUI.fire('Error', response.message || 'Failed to create contract', 'error');
            }
        },
        error: function(xhr) {
            OptimaPro.hideLoading();
            console.error('Error creating contract:', xhr);
            const errorMsg = xhr.responseJSON?.message || xhr.statusText || 'Failed to create contract';
            OptimaUI.fire('Error', errorMsg, 'error');
        }
    });
}

// Function to create SPK from quotation specifications
function createSPKFromQuotation(quotationId) {
    console.log('Ã°Å¸Å¡â‚¬ [SPK-QUOTATION] Opening SPK creation modal for quotation:', quotationId);
    
    // Get quotation data with specifications
    $.ajax({
        url: `<?= base_url('marketing/quotations/getQuotation/') ?>${quotationId}`,
        method: 'GET',
        success: function(response) {
            console.log('Ã¢Å“â€¦ [SPK-QUOTATION] Quotation data loaded:', response);
            
            if (!response.success || !response.data) {
                console.error('Ã¢ÂÅ’ [SPK-QUOTATION] Invalid response format:', response);
                OptimaUI.fire('Error', 'Failed to load quotation data', 'error');
                return;
            }
            
            const quotation = response.data;
            console.log('Ã°Å¸â€œâ€¹ [SPK-QUOTATION] Quotation object:', quotation);
            
            // Validate quotation has required data (Customer and Location only)
            if (!quotation.created_customer_id) {
                console.warn('Ã¢Å¡Â Ã¯Â¸Â [SPK-QUOTATION] No customer created for this quotation');
                OptimaUI.fire('Error', 'Customer must be created first. Please mark as deal.', 'error');
                return;
            }
            
            console.log('Ã¢Å“â€¦ [SPK-QUOTATION] Validation passed, contract status:', quotation.created_contract_id ? `Contract ID: ${quotation.created_contract_id}` : 'Ã¢ÂÂ³ No contract (optional)');
            
            // CONTRACT NOT REQUIRED - Can be linked later via SPK page
            // Load specifications for this quotation
            loadSpecificationsForSPK(quotation);
        },
        error: function(xhr, status, error) {
            console.error('Ã¢ÂÅ’ [SPK-QUOTATION] AJAX Error:', {xhr, status, error, responseText: xhr.responseText});
            OptimaUI.fire('Error', 'Failed to load quotation details: ' + error, 'error');
        }
    });
}

// Function to load specifications for SPK creation
function loadSpecificationsForSPK(quotation) {
    console.log('Ã°Å¸â€â€ž [SPK-QUOTATION] Loading specifications for quotation:', quotation.id_quotation);
    
    $.ajax({
        url: `<?= base_url('marketing/quotations/getSpecifications/') ?>${quotation.id_quotation}`,
        method: 'GET',
        success: function(response) {
            console.log('Ã¢Å“â€¦ [SPK-QUOTATION] Specifications loaded:', response);
            
            if (!response.success) {
                console.error('Ã¢ÂÅ’ [SPK-QUOTATION] Failed to load specifications');
                OptimaUI.fire('Error', 'Failed to load specifications', 'error');
                return;
            }
            
            const specifications = response.data || [];
            console.log('Ã°Å¸â€œÂ¦ [SPK-QUOTATION] Total specifications:', specifications.length);
            
            if (specifications.length === 0) {
                OptimaUI.fire({
                    title: 'No Specifications',
                    text: 'This quotation has no specifications yet. Please add specifications first.',
                    icon: 'warning',
                    confirmButtonText: 'Add Specifications'
                }).then((result) => {
                    if (result.isConfirmed) {
                        addSpecifications(quotation.id_quotation);
                    }
                });
                return;
            }
            
            // Populate SPK modal with data
            console.log('Ã¢Å“â€¦ [SPK-QUOTATION] Calling showSPKCreationModal...');
            showSPKCreationModal(quotation, specifications);
        },
        error: function(xhr, status, error) {
            console.error('Ã¢ÂÅ’ [SPK-QUOTATION] Specifications AJAX Error:', {xhr, status, error, responseText: xhr.responseText});
            OptimaUI.fire('Error', 'Failed to load specifications: ' + error, 'error');
        }
    });
}

// Function to show SPK creation modal
function showSPKCreationModal(quotation, specifications) {
    console.log('Ã°Å¸Å½Â¯ [SPK-QUOTATION] Showing SPK modal with specifications:', specifications.length + ' specs');
    console.log('Ã°Å¸â€œâ€¹ [SPK-QUOTATION] Modal data:', {quotation, specifications});
    
    // Set modal title info
    $('#spkModalQuotationInfo').html(`
        <strong>Quotation:</strong> ${quotation.quotation_number || 'N/A'} | 
        <strong>Customer:</strong> ${quotation.prospect_name || 'N/A'}
    `);
    
    // Populate customer details section
    $('#spkCustomerDetails').html(`
        <div class="col-md-6">
            <small class="text-muted">Company</small>
            <div class="fw-bold">${quotation.customer_name || quotation.prospect_name || '-'}</div>
        </div>
        <div class="col-md-6">
            <small class="text-muted">Location</small>
            <div class="fw-bold">${quotation.location_name || '-'}</div>
        </div>
        <div class="col-md-6">
            <small class="text-muted">PIC / Contact</small>
            <div>${quotation.pic_name || '-'} ${quotation.pic_phone ? '/ ' + quotation.pic_phone : ''}</div>
        </div>
        <div class="col-md-6">
            <small class="text-muted">Contract Number</small>
            <div class="fw-bold ${quotation.contract_number && quotation.customer_contract_complete == 1 ? 'text-primary' : 'text-warning'}">
                ${quotation.customer_contract_complete == 1 && quotation.contract_number ? `<code class="text-primary">${quotation.contract_number}</code>` : '<span class="badge badge-soft-yellow">Contract Pending</span>'}
            </div>
        </div>
    `);
    
    // Determine the correct contract ID from available fields
    // Only use contract if workflow is complete (customer_contract_complete = 1)
    const contractId = quotation.customer_contract_complete == 1 
        ? (quotation.created_contract_id || quotation.contract_id || quotation.id_kontrak)
        : null;
    
    // Set hidden fields
    $('#spk_quotation_id').val(quotation.id_quotation);
    $('#spk_customer_id').val(quotation.created_customer_id);
    $('#spk_contract_id').val(contractId || '');
    
    // Debug log
    console.log('Ã°Å¸â€œâ€¹ Setting SPK form fields:');
    console.log('  - quotation_id:', quotation.id_quotation);
    console.log('  - customer_id:', quotation.created_customer_id);
    console.log('  - contract_id:', contractId);
    
    // Set default delivery date (today + 7 days)
    const deliveryDate = new Date();
    deliveryDate.setDate(deliveryDate.getDate() + 7);
    $('#spk_delivery_date').val(deliveryDate.toISOString().split('T')[0]);
    
    // Build specifications list with checkboxes
    let specsHTML = '';
    
    specifications.forEach((spec, index) => {
        const specId = spec.id_specification || spec.id;
        
        // Use database specification_name and specification_description
        let specTitle = spec.specification_name || `Specification #${index + 1}`;
        
        // Determine if this is UNIT or ATTACHMENT based on category
        const specType = spec.category && spec.category.toLowerCase().includes('attachment') ? 'ATTACHMENT' : 'UNIT';
        
        // Use specification_description if available, otherwise build from details
        const specDescription = spec.specification_description && spec.specification_description.trim() !== '' 
            ? spec.specification_description 
            : buildSpecificationDescription(spec);
        
        const maxQty = parseInt(spec.quantity) || 1;
        const existingUnits = parseInt(spec.existing_spk_units) || 0;
        // IMPORTANT: Use 0 if available_units is 0, don't fallback to maxQty!
        const availableUnits = spec.available_units !== undefined && spec.available_units !== null 
            ? parseInt(spec.available_units) 
            : parseInt(spec.quantity) || 1;
        const isFullyCreated = availableUnits <= 0;
        
        // Debug logging
        console.log(`Spec ${specId}:`, {
            maxQty,
            existingUnits,
            availableUnits,
            isFullyCreated,
            rawData: {
                quantity: spec.quantity,
                existing_spk_units: spec.existing_spk_units,
                available_units: spec.available_units
            }
        });
        
        specsHTML += `
            <div class="card mb-2 border">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="form-check">
                            <input class="form-check-input spec-checkbox" type="checkbox" 
                                   id="spec_${specId}" name="specifications[]" value="${specId}" 
                                   data-max-qty="${availableUnits}"
                                   ${isFullyCreated ? 'disabled' : ''}>
                            <label class="form-check-label fw-bold ${isFullyCreated ? 'text-muted' : ''}" for="spec_${specId}">
                                ${specTitle}
                            </label>
                        </div>
                        <span class="badge ${specType === 'UNIT' ? 'badge-soft-blue' : 'badge-soft-green'}">${specType}</span>
                    </div>
                    
                    <div class="row g-2 small">
                        <div class="col-md-3">
                            <strong>Department:</strong> ${spec.nama_departemen || '-'}
                        </div>
                        <div class="col-md-3">
                            <strong>Unit Type:</strong> ${spec.jenis_tipe_unit || spec.nama_tipe_unit || '-'}
                        </div>
                        <div class="col-md-3">
                            <strong>Brand & Model:</strong> ${spec.merk_unit || '-'} ${spec.model_unit ? '- ' + spec.model_unit : ''}
                        </div>
                        <div class="col-md-3">
                            <strong>Capacity:</strong> ${spec.nama_kapasitas || '-'}
                        </div>
                    </div>
                    
                    ${spec.jenis_baterai || spec.attachment_tipe || (spec.merk_charger && spec.tipe_charger) ? `
                    <div class="row g-2 small mt-2">
                        ${spec.jenis_baterai ? `
                        <div class="col-md-4">
                            <strong>Battery:</strong> ${spec.jenis_baterai}
                        </div>
                        ` : ''}
                        ${spec.merk_charger && spec.tipe_charger ? `
                        <div class="col-md-4">
                            <strong>Charger:</strong> ${spec.merk_charger} - ${spec.tipe_charger}
                        </div>
                        ` : ''}
                        ${spec.attachment_tipe || spec.attachment_merk ? `
                        <div class="col-md-4">
                            <strong>Attachment:</strong> ${spec.attachment_tipe || ''}${spec.attachment_tipe && spec.attachment_merk ? ' - ' : ''}${spec.attachment_merk || ''}
                        </div>
                        ` : ''}
                    </div>
                    ` : ''}
                    
                    ${spec.valve_name || spec.mast_name || spec.tire_name || spec.wheel_name ? `
                    <div class="row g-2 small mt-2">
                        ${spec.valve_name ? `
                        <div class="col-md-3">
                            <strong>Valve:</strong> ${spec.valve_name}
                        </div>
                        ` : ''}
                        ${spec.mast_name ? `
                        <div class="col-md-3">
                            <strong>Mast:</strong> ${spec.mast_name}
                        </div>
                        ` : ''}
                        ${spec.tire_name ? `
                        <div class="col-md-3">
                            <strong>Tire:</strong> ${spec.tire_name}
                        </div>
                        ` : ''}
                        ${spec.wheel_name ? `
                        <div class="col-md-3">
                            <strong>Wheel:</strong> ${spec.wheel_name}
                        </div>
                        ` : ''}
                    </div>
                    ` : ''}
                    
                    ${spec.unit_accessories && spec.unit_accessories.trim() !== '' ? `
                    <div class="mt-2 small">
                        <strong>Accessories:</strong> 
                        ${spec.unit_accessories.split(',').map(acc => `<span class="badge badge-soft-blue me-1">${formatAccessoryLabel(acc)}</span>`).join('')}
                    </div>
                    ` : ''}
                    
                    <div class="row g-2 small mt-2">
                        <div class="col-md-6">
                            <strong>Price:</strong> 
                            ${spec.monthly_price || spec.unit_price || spec.harga_per_unit ? 
                                `Rp ${parseFloat(spec.monthly_price || spec.unit_price || spec.harga_per_unit).toLocaleString('id-ID')}/month` : ''}
                            ${spec.daily_price || spec.harga_per_unit_harian ? 
                                ` | Rp ${parseFloat(spec.daily_price || spec.harga_per_unit_harian).toLocaleString('id-ID')}/day` : ''}
                        </div>
                        <div class="col-md-6">
                            <strong>Qty:</strong> ${spec.quantity} unit(s)
                            ${existingUnits > 0 ? `<span class="text-warning ms-2">(${existingUnits} have SPK)</span>` : ''}
                        </div>
                    </div>
                    
                    ${!isFullyCreated ? `
                    <div class="row g-2 mt-2 align-items-center">
                        <div class="col-auto">
                            <small class="text-muted">Select quantity:</small>
                        </div>
                        <div class="col-auto">
                            <input type="number" class="form-control form-control-sm spec-quantity" 
                                   id="qty_${specId}" name="quantities[${specId}]" 
                                   min="1" max="${availableUnits}" value="${availableUnits}" 
                                   style="width: 80px;" disabled>
                        </div>
                        <div class="col-auto">
                            <small class="text-muted">of ${availableUnits} available</small>
                        </div>
                    </div>
                    ` : `
                    <div class="alert alert-secondary py-2 mt-2 mb-0">
                        <i class="fas fa-check-circle me-2"></i>All ${maxQty} unit(s) already have SPK
                    </div>
                    `}
                </div>
            </div>
        `;
    });
    
    if (specsHTML === '') {
        specsHTML = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>No specifications available</div>';
    }
    
    $('#spkSpecificationsList').html(specsHTML);
    
    // Add checkbox change handler
    $('.spec-checkbox').on('change', function() {
        const specId = $(this).val();
        const qtyInput = $(`#qty_${specId}`);
        
        if ($(this).is(':checked')) {
            qtyInput.prop('disabled', false).focus();
        } else {
            qtyInput.prop('disabled', true);
        }
    });
    
    // Show modal
    console.log('Ã°Å¸Å½Â­ [SPK-QUOTATION] All data populated, opening Bootstrap modal...');
    $('#createSPKModal').modal('show');
    console.log('Ã¢Å“â€¦ [SPK-QUOTATION] Modal show() called successfully');
}

// Helper function to build specification description
function buildSpecificationDescription(spec) {
    let parts = [];
    
    // Basic Info - Department and Unit Type
    if (spec.nama_departemen) {
        parts.push(`<strong>Department:</strong> ${spec.nama_departemen}`);
    }
    
    if (spec.jenis_tipe_unit) {
        parts.push(`<strong>Unit Type:</strong> ${spec.jenis_tipe_unit}`);
    } else if (spec.nama_tipe_unit) {
        parts.push(`<strong>Unit Type:</strong> ${spec.nama_tipe_unit}`);
    }
    // Capacity
    if (spec.nama_kapasitas) {
        parts.push(`<strong>Capacity:</strong> ${spec.nama_kapasitas}`);
    }
    
    // Brand and Model
    if (spec.merk_unit) {
        parts.push(`<strong>Brand:</strong> ${spec.merk_unit}`);
    }
    if (spec.model_unit && spec.model_unit.trim() !== '') {
        parts.push(`<strong>Model:</strong> ${spec.model_unit}`);
    }
    
    // Electric Specific - Battery and Charger
    const isElectric = spec.nama_departemen && 
                      (spec.nama_departemen.toLowerCase().includes('electric') || 
                       spec.nama_departemen.toLowerCase().includes('listrik'));
    
    if (isElectric) {
        if (spec.jenis_baterai) {
            parts.push(`<strong>Battery:</strong> ${spec.jenis_baterai}`);
        }
        // Charger info could be added here if available in spec object
    }
    
    // Technical Specs
    const techSpecs = [];
    if (spec.valve_id) techSpecs.push(`Valve`);
    if (spec.mast_id) techSpecs.push(`Mast`);
    if (spec.ban_id) techSpecs.push(`Tire`);
    if (spec.roda_id) techSpecs.push(`Wheel`);
    
    if (techSpecs.length > 0) {
        parts.push(`<strong>Tech:</strong> ${techSpecs.join(', ')}`);
    }
    
    // Attachment details (if ATTACHMENT type)
    if (spec.attachment_tipe) {
        parts.push(`<strong>Attachment:</strong> ${spec.attachment_tipe}`);
    }
    if (spec.attachment_merk) {
        parts.push(`<strong>Att. Brand:</strong> ${spec.attachment_merk}`);
    }
    
    // Accessories
    if (spec.unit_accessories && spec.unit_accessories.trim() !== '') {
        const accessories = spec.unit_accessories.split(',').map(a => a.trim());
        if (accessories.length > 0) {
            const accessoryText = accessories.map(a => formatAccessoryLabel(a));
            parts.push(`<strong>Accessories:</strong> ${accessoryText.slice(0, 3).join(', ')}${accessoryText.length > 3 ? '...' : ''}`);
        }
    }
    
    // Pricing Info
    const prices = [];
    if (spec.monthly_price || spec.unit_price || spec.harga_per_unit) {
        const monthlyPrice = spec.monthly_price || spec.unit_price || spec.harga_per_unit;
        prices.push(`Monthly: Rp ${parseFloat(monthlyPrice).toLocaleString('id-ID')}`);
    }
    if (spec.daily_price || spec.harga_per_unit_harian) {
        const dailyPrice = spec.daily_price || spec.harga_per_unit_harian;
        prices.push(`Daily: Rp ${parseFloat(dailyPrice).toLocaleString('id-ID')}`);
    }
    
    if (prices.length > 0) {
        parts.push(`<strong>Price:</strong> ${prices.join(' | ')}`);
    }
    
    // Quantity in quotation
    if (spec.quantity) {
        parts.push(`<strong>Qty:</strong> ${spec.quantity} unit(s)`);
    }
    
    return parts.length > 0 ? parts.join(' Ã¢â‚¬Â¢ ') : 'No details available';
}

// Handle SPK creation form submission
$('#createSPKForm').on('submit', function(e) {
    e.preventDefault();
    
    // Get selected specifications
    const selectedSpecs = [];
    $('.spec-checkbox:checked').each(function() {
        const specId = $(this).val();
        const quantity = parseInt($(`#qty_${specId}`).val());
        const maxQty = parseInt($(this).data('max-qty'));
        
        if (quantity > maxQty) {
            OptimaUI.fire('Error', `Quantity for specification #${specId} exceeds maximum (${maxQty})`, 'error');
            return false;
        }
        
        selectedSpecs.push({
            specification_id: specId,
            quantity: quantity
        });
    });
    
    if (selectedSpecs.length === 0) {
        OptimaUI.fire('Warning', 'Please select at least one specification', 'warning');
        return false;
    }
    
    // Prepare form data
    const formData = {
        quotation_id: $('#spk_quotation_id').val(),
        customer_id: $('#spk_customer_id').val(),
        contract_id: $('#spk_contract_id').val(),
        delivery_date: $('#spk_delivery_date').val(),
        specifications: selectedSpecs
    };
    
    console.log('=== SPK Form Data Debug ===');
    console.log('Quotation ID:', formData.quotation_id);
    console.log('Customer ID:', formData.customer_id);
    console.log('Contract ID:', formData.contract_id || '(No contract - will be linked later)');
    console.log('Delivery Date:', formData.delivery_date);
    console.log('Specifications:', formData.specifications);
    console.log('===========================');
    
    // Validate required fields (CONTRACT NOW OPTIONAL)
    if (!formData.quotation_id || !formData.customer_id) {
        OptimaUI.fire('Error', 'Missing quotation or customer ID. Please close and reopen the modal.', 'error');
        submitBtn.prop('disabled', false).html('Create Selected SPK(s)');
        return false;
    }
    
    // Disable submit button
    const submitBtn = $('#submitSPKBtn');
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Creating...');
    
    // Add CSRF token to formData
    formData[window.csrfTokenName] = window.csrfToken || '<?= csrf_hash() ?>';
    
    // Submit to backend
    $.ajax({
        url: '<?= base_url('marketing/spk/createFromQuotation') ?>',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(formData),
        success: function(response) {
            submitBtn.prop('disabled', false).html('<i class="fas fa-check me-2"></i>Create Selected SPK(s)');
            
            if (response.success) {
                $('#createSPKModal').modal('hide');
                
                const spkCount = response.spk_count || 1;
                const spkNumbers = response.spk_numbers || [];
                const allAllocated = response.all_specs_allocated || false;
                const statusUpdated = response.status_updated || false;
                
                let message = `${spkCount} SPK(s) created successfully!`;
                if (spkNumbers.length > 0) {
                    message += `\n\nSPK Numbers: ${spkNumbers.join(', ')}`;
                }
                
                // Add status update notification if all specs are allocated
                if (statusUpdated && allAllocated) {
                    message += '\n\nÃ¢Å“â€¦ All specifications completed!\nQuotation marked as CLOSED.';
                }
                
                OptimaUI.fire({
                    title: 'Success!',
                    text: message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Refresh table if needed
                    if (typeof quotationsTable !== 'undefined' && quotationsTable) {
                        quotationsTable.ajax.reload(null, false);
                    }
                });
            } else {
                OptimaUI.fire('Error', response.message || 'Failed to create SPK', 'error');
            }
        },
        error: function(xhr) {
            submitBtn.prop('disabled', false).html('<i class="fas fa-check me-2"></i>Create Selected SPK(s)');
            console.error('Error creating SPK:', xhr);
            OptimaUI.fire('Error', 'Failed to create SPK: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
        }
    });
});


// Global Date Range Picker Callbacks for Quotations
// Note: Callbacks are now handled by setupDataTableDateFilter() mixin
// No need for manual callback definitions anymore!


// Third $(document).ready() block removed - merged into main block above
</script>

<?= $this->endSection() ?>