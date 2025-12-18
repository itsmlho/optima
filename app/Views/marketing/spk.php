<?= $this->extend('layouts/base') ?>

<?php
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
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><?= lang('Marketing.spk_list') ?></h6>
                <?php if ($can_create): ?>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#spkModal">
                    <i class="fas fa-plus me-1"></i><?= lang('Marketing.create_spk') ?>
                </button>
                <?php else: ?>
                <button class="btn btn-primary btn-sm disabled" title="<?= lang('Marketing.access_denied') ?>">
                    <i class="fas fa-plus me-1"></i><?= lang('Marketing.create_spk') ?>
                </button>
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
        
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <span><?= lang('Marketing.show') ?></span>
                    <select class="form-select form-select-sm" id="entriesPerPage" style="width: auto;">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span><?= lang('Marketing.entries') ?></span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span><?= lang('Marketing.search') ?>:</span>
                    <input type="text" class="form-control form-control-sm" id="spkSearch" placeholder="" style="width: 200px;">
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover table-manual-sort <?= !$can_view ? 'table-disabled' : '' ?>" id="spkList">
                    <thead>
                        <tr>
                            <th><?= lang('Marketing.spk_number') ?></th>
                            <th><?= lang('Marketing.type') ?></th>
                            <th><?= lang('Marketing.contract_po') ?></th>
                            <th><?= lang('Marketing.company_name') ?></th>
                            <th><?= lang('Marketing.pic') ?></th>
                            <th><?= lang('Marketing.contact') ?></th>
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
            
            <!-- Pagination and Info -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div id="spkTableInfo">
                    <?= lang('Marketing.showing') ?> 0 <?= lang('Marketing.to') ?> 0 <?= lang('Marketing.of') ?> 0 <?= lang('Marketing.entries') ?>
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0" id="spkPagination">
                        <!-- Pagination will be generated by JavaScript -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <div class="modal fade" id="spkModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header"><h6 class="modal-title"><?= lang('Marketing.create_spk') ?></h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <form id="spkForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label"><?= lang('Marketing.type') ?></label>
                            <select class="form-select form-select-sm w-auto" name="jenis_spk" id="jenisSpkSelect" required>
                                <option value="UNIT" selected><?= lang('Marketing.spk_unit') ?></option>
                                <option value="ATTACHMENT"><?= lang('Marketing.spk_attachment') ?></option>
                            </select>
                        </div>
                        
                        <!-- Step 1: Select Contract -->
                        <div class="mb-3">
                            <label class="form-label"><?= lang('Marketing.select_contract') ?></label>
                            <select class="form-select" name="kontrak_id" id="kontrakSelect" required>
                                <option value=""><?= lang('Marketing.select_contract') ?></option>
                            </select>
                            <div class="form-text"><?= lang('Marketing.select_contract_help') ?></div>
                        </div>
                        
                        <!-- Step 2: Contract Info -->
                        <div id="kontrakInfoSection" style="display: none;">
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
                        
                        <!-- Target Unit Section (for ATTACHMENT only) - Shows after contract selection -->
                        <div id="attachmentTargetSection" style="display: none;">
                            <div class="card bg-warning bg-opacity-10 border-warning mb-3">
                                <div class="card-header bg-warning bg-opacity-25">
                                    <h6 class="mb-0"><i class="fas fa-bullseye me-2"></i>Target Unit</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label"><?= lang('Marketing.target_unit') ?> <span class="text-danger">*</span></label>
                                        <select class="form-control" name="target_unit_id" id="targetUnitSelect">
                                            <option value="">- <?= lang('Marketing.select_target_unit') ?> -</option>
                                        </select>
                                        <div class="form-text"><?= lang('Marketing.select_unit_receive_replacement') ?></div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label"><?= lang('Marketing.replacement_reason') ?></label>
                                        <textarea class="form-control" name="replacement_reason" id="replacementReason" rows="2" 
                                                  placeholder="<?= lang('Marketing.replacement_reason_example') ?>"></textarea>
                                        <div class="form-text"><?= lang('Marketing.explain_attachment_replacement') ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Step 3: Select Specification -->
                        <div id="spesifikasiSection" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label"><?= lang('Marketing.select_unit_specification') ?></label>
                                <select class="form-select" name="kontrak_spesifikasi_id" id="spesifikasiSelect" required>
                                    <option value="">-- <?= lang('Marketing.select_specification') ?> --</option>
                                </select>
                                <div class="form-text">Select the specification to be processed in this work order</div>
                            </div>
                            
                            <!-- Specification Detail -->
                            <div id="spesifikasiDetail" style="display: none;">
                                <div class="card border-primary mb-3">
                                    <div class="card-header bg-primary text-white9, ">
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
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button"><?= lang('App.close') ?></button>
                        <button class="btn btn-primary" type="submit" id="submitSpkBtn" disabled>Create SPK</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create DI Modal -->
    <div class="modal fade" id="diModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
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
                        <div id="spkTukarWorkflow" style="display:none;" class="mb-3">
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
                                        <div>
                                            <button class="btn btn-sm btn-outline-warning" type="button" id="spkBtnSelectAllTarik">Select All</button>
                                            <button class="btn btn-sm btn-outline-secondary" type="button" id="spkBtnClearTarik">Clear</button>
                                        </div>
                                    </div>
                                    <div id="spkTarikUnitList" class="unit-list" style="max-height:200px; overflow:auto;">
                                        <div class="text-muted small">Loading units from contract...</div>
                                    </div>
                                    <div class="form-text">Selected units will be removed from contract (for replacement)</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <div id="diUnitsPick" class="mt-2" style="display:none">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong id="diPickLabel">Select Units to be Delivered</strong>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary" id="btnSelectAllUnits">Select All</button>
                                        <button type="button" class="btn btn-outline-secondary" id="btnClearUnits">Clear</button>
                                    </div>
                                </div>
                                <div id="diUnitsList" class="border rounded p-2" style="max-height:200px; overflow:auto"></div>
                                <div class="form-text" id="diPickHelp">Check the units you want to include in this DI.</div>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6"><label class="form-label">Delivery Date</label><input type="date" class="form-control" name="tanggal_kirim"></div>
                            <div class="col-6 d-flex align-items-end"><span class="text-muted small">Optional</span></div>
                        </div>
                        <div class="mt-2"><label class="form-label">Notes</label><textarea class="form-control" name="catatan" rows="3" placeholder="Delivery instructions (optional)"></textarea></div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Create DI</button></div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Map status to Bootstrap badge classes per entity
    function statusBadge(entity, status){
        const s = (status||'').toUpperCase();
        const mapSPK = { SUBMITTED:'secondary', IN_PROGRESS:'info', READY:'success', DELIVERED:'primary', COMPLETED:'primary', CANCELLED:'danger' };
        const mapDI  = { SUBMITTED:'secondary', DISPATCHED:'info', ARRIVED:'success', CANCELLED:'danger' };
        const cls = (entity==='DI'?mapDI[s]:mapSPK[s]) || 'secondary';
        return `<span class="badge bg-${cls}">${status}</span>`;
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
    let allSpkData = [];
    let currentFilter = 'all';
    let currentSearchTerm = '';
    let currentPage = 1;
    let entriesPerPage = 10;
    
    // Update statistics cards
    function updateSpkStats(data) {
        const stats = {
            total: 0,
            inProgress: 0,
            ready: 0,
            completed: 0
        };
        
        (data || []).forEach(spk => {
            stats.total++;
            const status = (spk.status || '').toUpperCase();
            if (status === 'IN_PROGRESS') stats.inProgress++;
            else if (status === 'READY') stats.ready++;
            else if (status === 'COMPLETED' || status === 'DELIVERED') stats.completed++;
        });
        
        // Update statistics with null check
        const totalElement = document.getElementById('stat-total-spk');
        const inProgressElement = document.getElementById('stat-in-progress');
        const readyElement = document.getElementById('stat-ready');
        const completedElement = document.getElementById('stat-completed');
        
        if (totalElement) totalElement.textContent = stats.total;
        if (inProgressElement) inProgressElement.textContent = stats.inProgress;
        if (readyElement) readyElement.textContent = stats.ready;
        if (completedElement) completedElement.textContent = stats.completed;
    }
    
    // Apply both status filter and search
    function applyFilters() {
        let filteredData = allSpkData;
        
        // Apply status filter
        if (currentFilter !== 'all') {
            filteredData = filteredData.filter(spk => {
                const status = (spk.status || '').toUpperCase();
                if (currentFilter === 'COMPLETED') {
                    return status === 'COMPLETED' || status === 'DELIVERED';
                }
                return status === currentFilter;
            });
        }
        
        // Apply search filter
        if (currentSearchTerm.trim() !== '') {
            const searchTerm = currentSearchTerm.toLowerCase();
            filteredData = filteredData.filter(spk => {
                return (spk.nomor_spk || '').toLowerCase().includes(searchTerm) ||
                       (spk.pelanggan || '').toLowerCase().includes(searchTerm) ||
                       (spk.po_kontrak_nomor || '').toLowerCase().includes(searchTerm) ||
                       (spk.pic || '').toLowerCase().includes(searchTerm) ||
                       (spk.kontak || '').toLowerCase().includes(searchTerm) ||
                       (spk.jenis_spk || '').toLowerCase().includes(searchTerm);
            });
        }
        
        renderSpkTable(filteredData);
        updatePagination(filteredData);
    }
    
    // Update pagination and info
    function updatePagination(data) {
        const totalEntries = data.length;
        const totalPages = Math.ceil(totalEntries / entriesPerPage);
        const startEntry = totalEntries === 0 ? 0 : (currentPage - 1) * entriesPerPage + 1;
        const endEntry = Math.min(currentPage * entriesPerPage, totalEntries);
        
        // Update info text
        document.getElementById('spkTableInfo').textContent = 
            `Showing ${startEntry} to ${endEntry} of ${totalEntries} entries` +
            (currentSearchTerm.trim() !== '' ? ' (filtered from ' + allSpkData.length + ' total entries)' : '');
        
        // Generate pagination
        const pagination = document.getElementById('spkPagination');
        pagination.innerHTML = '';
        
        if (totalPages <= 1) return;
        
        // Previous button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;">Previous</a>`;
        pagination.appendChild(prevLi);
        
        // Page numbers
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);
        
        if (startPage > 1) {
            const firstLi = document.createElement('li');
            firstLi.className = 'page-item';
            firstLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(1); return false;">1</a>`;
            pagination.appendChild(firstLi);
            
            if (startPage > 2) {
                const dotsLi = document.createElement('li');
                dotsLi.className = 'page-item disabled';
                dotsLi.innerHTML = '<span class="page-link">...</span>';
                pagination.appendChild(dotsLi);
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === currentPage ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>`;
            pagination.appendChild(li);
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const dotsLi = document.createElement('li');
                dotsLi.className = 'page-item disabled';
                dotsLi.innerHTML = '<span class="page-link">...</span>';
                pagination.appendChild(dotsLi);
            }
            
            const lastLi = document.createElement('li');
            lastLi.className = 'page-item';
            lastLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${totalPages}); return false;">${totalPages}</a>`;
            pagination.appendChild(lastLi);
        }
        
        // Next button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;">Next</a>`;
        pagination.appendChild(nextLi);
    }
    
    // Change page function
    function changePage(page) {
        currentPage = page;
        applyFilters();
    }
    
    // Filter SPK data based on status
    function filterSpkData(filter) {
        currentFilter = filter;
        currentPage = 1; // Reset to first page when filtering
        
        // Update active card styling
        document.querySelectorAll('.filter-card').forEach(card => {
            card.classList.remove('active');
        });
        
        const activeCard = document.querySelector(`[data-filter="${filter}"]`);
        if (activeCard) {
            activeCard.classList.add('active');
        }
        
        applyFilters();
    }
    
    // Render SPK table with given data
    function renderSpkTable(data) {
        const tb = document.querySelector('#spkList tbody');
        tb.innerHTML = '';
        
        // Calculate pagination
        const startIndex = (currentPage - 1) * entriesPerPage;
        const endIndex = startIndex + entriesPerPage;
        const paginatedData = data.slice(startIndex, endIndex);
        
        paginatedData.forEach(r=>{
            const tr = document.createElement('tr');
            const diBtn = (r.status === 'READY')
              ? `<button class="btn btn-sm btn-primary buat-di" data-id="${r.id}" data-spk='${JSON.stringify({id:r.id, nomor_spk:r.nomor_spk, po:r.po_kontrak_nomor, pelanggan:r.pelanggan, lokasi:r.lokasi}).replace(/'/g,"&apos;")}' title="Create DI">Create DI</button>`
              : '';
            const aksiBtn = diBtn || '<span class="text-muted">-</span>';
            tr.innerHTML = `<td><a href="#" onclick=\"openDetail(${r.id});return false;\">${r.nomor_spk}</a></td>`+
              `<td><span class=\"badge bg-dark\">${r.jenis_spk||'UNIT'}</span></td>`+
              `<td>${r.po_kontrak_nomor||'-'}</td>`+
              `<td>${r.pelanggan||'-'}</td>`+
              `<td>${r.pic||'-'}</td>`+
              `<td>${r.kontak||'-'}</td>`+
              `<td>${statusBadge('SPK', r.status)}</td>`+
              `<td>${r.jumlah_unit||'-'}</td>`+
              `<td>${aksiBtn}</td>`;
            tb.appendChild(tr);
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
                            const warningText = isInActiveDI && activeDI ? ` <span class="badge bg-warning text-dark">Already in ${activeDI.nomor_di}</span>` : '';
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
    
    function loadSpk(startDate, endDate){
        const url = '<?= base_url('marketing/spk/list') ?>';
        const data = {};
        if (startDate && endDate) {
            data.start_date = startDate;
            data.end_date = endDate;
        }
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        }).then(r=>r.json()).then(j=>{
            const data = j.data || [];
            allSpkData = data; // Store for filtering
            
            // Update statistics
            updateSpkStats(data);
            
            // Apply current filters
            applyFilters();
        });
    }
    function loadKontrakOptions(q){
        const url = new URL('<?= base_url('marketing/spk/kontrak-options') ?>', window.location.origin);
        if(q) url.searchParams.set('q', q);
        const jenisSpkElement = document.querySelector('select[name="jenis_spk"]');
        const jenis = jenisSpkElement ? jenisSpkElement.value : 'UNIT';
        const kontrakStatus = (jenis === 'TUKAR') ? 'Aktif' : 'Pending';
        url.searchParams.set('status', kontrakStatus);
        fetch(url).then(r=>r.json()).then(j=>{
            const dl = document.getElementById('kontrakOptions');
            if (!dl) return; // Skip if kontrakOptions element doesn't exist
            dl.innerHTML = '';
            (j.data||[]).forEach(opt=>{
                const o = document.createElement('option');
                o.value = opt.no_po_marketing || opt.no_kontrak || '';
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
                    <td>${r.no_po_marketing||'-'}</td>
                    <td>${r.pelanggan||'-'}</td>
                    <td>${r.lokasi||'-'}</td>
                    <td><span class="badge bg-dark">${fmt(r.total_spk)}</span></td>
                    <td><span class="badge bg-secondary">${fmt(r.submitted)}</span></td>
                    <td><span class="badge bg-info">${fmt(r.in_progress)}</span></td>
                    <td><span class="badge bg-warning">${fmt(r.ready)}</span></td>
                    <td><span class="badge bg-success">${fmt(r.delivered)}</span></td>
                    <td><span class="badge bg-danger">${fmt(r.cancelled)}</span></td>
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

    // Initialize page date filter using new helper
    initPageDateFilter({
        pickerId: 'spkDateRangePicker',
        onInit: function() {
            console.log('🚀 SPK: Initial load without filter');
            loadSpk(); // Load without date filter
            loadKontrakOptions('');
            loadMonitoring();
        },
        onDateChange: function(startDate, endDate) {
            console.log('📅 SPK: Date filter changed, reloading data');
            loadSpk(startDate, endDate);
        },
        onDateClear: function() {
            console.log('✖️ SPK: Date filter cleared, reloading all data');
            loadSpk(); // Load without date filter
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
            currentFilter = filter;
            
            // Update active tab
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Apply filter
            filterSpkData(filter);
        });
    });
    
    // Add search functionality
    const spkSearchInput = document.getElementById('spkSearch');
    if (spkSearchInput) {
        spkSearchInput.addEventListener('input', (e) => {
            currentSearchTerm = e.target.value;
            currentPage = 1; // Reset to first page when searching
            applyFilters();
        });
    }
    
    // Add entries per page functionality
    const entriesSelect = document.getElementById('entriesPerPage');
    if (entriesSelect) {
        entriesSelect.addEventListener('change', (e) => {
            entriesPerPage = parseInt(e.target.value);
            currentPage = 1; // Reset to first page when changing entries per page
            applyFilters();
        });
    }

    
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
            url.searchParams.set('status', (jenis === 'TUKAR') ? 'Aktif' : 'Pending');
            fetch(url).then(r=>r.json()).then(j=>{
                const rows = j.data||[];
                // Try exact match by no_po_marketing or no_kontrak
                const exact = rows.find(x => x.no_po_marketing === v || x.no_kontrak === v);
                if (exact) {
                    if (exact.pelanggan) pelangganInput.value = exact.pelanggan;
                    if (exact.lokasi) lokasiInput.value = exact.lokasi;
                }
            });
        });

        // New SPK workflow based on contract specifications
        const kontrakSelect = document.getElementById('kontrakSelect');
        const spesifikasiSelect = document.getElementById('spesifikasiSelect');
        const jumlahUnitInput = document.getElementById('jumlahUnitSpk');
        
        // Check URL parameters for pre-selected specification
        const urlParams = new URLSearchParams(window.location.search);
        const preSelectedSpekId = urlParams.get('spesifikasi_id');
        
        // Load available contracts with specifications
        function loadAvailableKontraks() {
            fetch('<?= base_url('marketing/kontrak/get-active-contracts') ?>', {
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
                    let options = '<option value="">-- Select Contract --</option>';
                    if (data.success && data.data && data.data.length > 0) {
                        data.data.forEach(kontrak => {
                            const label = `${kontrak.no_kontrak || 'No Contract'} - ${kontrak.pelanggan || 'Unknown Customer'}`;
                            options += `<option value="${kontrak.id}">${label}</option>`;
                        });
                    } else {
                        options = '<option value="">No contracts available</option>';
                    }
                    
                    if (kontrakSelect) {
                        kontrakSelect.innerHTML = options;
                    }
                    
                    // If we have a pre-selected specification, find and select its contract
                    if (preSelectedSpekId) {
                        findAndSelectKontrakBySpekId(preSelectedSpekId);
                    }
                })
                .catch(error => {
                    console.error('Error loading contracts:', error);
                    if (kontrakSelect) {
                        kontrakSelect.innerHTML = `<option value="">Error loading contracts: ${error.message}</option>`;
                    }
                });
        }
        
        // Find contract by specification ID and auto-select
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
        
        // Handle contract selection
        if (kontrakSelect) {
            kontrakSelect.addEventListener('change', function() {
                const kontrakId = this.value;
                
                if (kontrakId) {
                    // Load contract info
                    loadKontrakInfo(kontrakId);
                    // Load specifications for this contract
                    loadKontrakSpesifikasiForSpk(kontrakId);
                    
                    // Load units for ATTACHMENT if SPK type is ATTACHMENT
                    const jenisSpk = document.getElementById('jenisSpkSelect');
                    if (jenisSpk && jenisSpk.value === 'ATTACHMENT') {
                        loadContractUnitsForAttachment(kontrakId);
                        
                        // Show attachment target section
                        const attachmentSection = document.getElementById('attachmentTargetSection');
                        const targetUnitSelect = document.getElementById('targetUnitSelect');
                        if (attachmentSection) {
                            attachmentSection.style.display = 'block';
                        }
                        if (targetUnitSelect) {
                            targetUnitSelect.setAttribute('required', 'required');
                        }
                    }
                    
                    // Show contract info section
                    const kontrakInfoSection = document.getElementById('kontrakInfoSection');
                    const spesifikasiSection = document.getElementById('spesifikasiSection');
                    if (kontrakInfoSection) kontrakInfoSection.style.display = 'block';
                    if (spesifikasiSection) spesifikasiSection.style.display = 'block';
                } else {
                    // Hide sections
                    const kontrakInfoSection = document.getElementById('kontrakInfoSection');
                    const spesifikasiSection = document.getElementById('spesifikasiSection');
                    const spesifikasiDetail = document.getElementById('spesifikasiDetail');
                    const submitSpkBtn = document.getElementById('submitSpkBtn');
                    const attachmentSection = document.getElementById('attachmentTargetSection');
                    const targetUnitSelect = document.getElementById('targetUnitSelect');
                    
                    if (kontrakInfoSection) kontrakInfoSection.style.display = 'none';
                    if (spesifikasiSection) spesifikasiSection.style.display = 'none';
                    if (attachmentSection) attachmentSection.style.display = 'none';
                    if (targetUnitSelect) {
                        targetUnitSelect.removeAttribute('required');
                        targetUnitSelect.value = '';
                    }
                    if (spesifikasiDetail) spesifikasiDetail.style.display = 'none';
                    if (submitSpkBtn) submitSpkBtn.disabled = true;
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
        
        // Load specifications for selected contract
        function loadKontrakSpesifikasiForSpk(kontrakId) {
            // Get selected SPK type to filter specifications
            const spkTypeElement = document.getElementById('jenisSpkSelect');
            const jenisSpk = spkTypeElement ? spkTypeElement.value : 'UNIT';
            
            fetch(`<?= base_url('marketing/kontrak/spesifikasi/') ?>${kontrakId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">-- Select Specification --</option>';
                    if (data.success && data.data) {
                        
                        // Filter specifications based on SPK type
                        const filteredSpecs = data.data.filter(spek => {
                            if (jenisSpk === 'ATTACHMENT') {
                                // For attachment SPK, show specs that have attachment_type OR no unit specification
                                // This allows for attachment-only specifications
                                const hasAttachment = spek.attachment_type && spek.attachment_type.trim() !== '' && spek.attachment_type.trim() !== 'N/A' && spek.attachment_type !== 'null';
                                const hasNoUnit = !spek.tipe_unit_id || spek.tipe_unit_id === '0' || spek.tipe_unit_id === '' || spek.tipe_unit_id === null;
                                
                                // Show if has attachment spec OR if it's an attachment-only spec (no unit defined)
                                return hasAttachment || hasNoUnit;
                            } else {
                                // For unit SPK, show specs that have unit specifications (tipe_unit_id)
                                const hasUnit = spek.tipe_unit_id && parseInt(spek.tipe_unit_id) > 0;
                                return hasUnit;
                            }
                        });
                        
                        filteredSpecs.forEach(spek => {
                            const available = spek.quantity || 0;
                            
                            // Create display label based on SPK type
                            let displayLabel = '';
                            if (jenisSpk === 'ATTACHMENT') {
                                // For attachment SPK, show attachment info or generic label
                                if (spek.attachment_type && spek.attachment_type !== 'null') {
                                    displayLabel = `${spek.specification_name} - ${spek.attachment_type} ${spek.attachment_brand || ''} (${spek.quantity} qty)`;
                                } else {
                                    // Generic attachment spec (to be customized)
                                    displayLabel = `${spek.specification_name} - Attachment Specification (${spek.quantity} qty)`;
                                }
                            } else {
                                displayLabel = `${spek.specification_name} - ${spek.quantity} Unit (${available > 0 ? available : 0} available)`;
                            }
                            
                            const spekDataEncoded = btoa(encodeURIComponent(JSON.stringify(spek)));
                            options += `<option value="${spek.id_specification}" data-available="${available}" data-spek-encoded="${spekDataEncoded}">${displayLabel}</option>`;
                        });
                        
                        if (filteredSpecs.length === 0) {
                            const typeLabel = jenisSpk === 'ATTACHMENT' ? 'attachment' : 'unit';
                            options = `<option value="">No ${typeLabel} specifications found</option>`;
                        }
                    } else {
                        options = '<option value="">No specifications found</option>';
                    }
                    if (spesifikasiSelect) {
                        spesifikasiSelect.innerHTML = options;
                    } else {
                        console.error('spesifikasiSelect element not found');
                    }
                })
                .catch(error => {
                    console.error('Error loading specifications:', error);
                    if (spesifikasiSelect) {
                        spesifikasiSelect.innerHTML = '<option value="">Error loading specifications</option>';
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
                        if (spesifikasiDetail) spesifikasiDetail.style.display = 'block';
                        
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
                    if (spesifikasiDetail) spesifikasiDetail.style.display = 'none';
                    if (submitSpkBtn) submitSpkBtn.disabled = true;
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
                            <strong>Status:</strong> <span class="badge bg-info">${available > 0 ? 'Tersedia' : 'Proses Pengadaan'}</span>
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
                merk: merk || '',
                status: 'TERSEDIA'
            });
            
            fetch(`<?= base_url('warehouse/inventory/get-attachment-list') ?>?${params}`)
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
                                    <td><span class="badge bg-success">Tersedia</span></td>
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
        
        // Validate SPK form
        function validateSpkForm() {
            const kontrakId = kontrakSelect ? kontrakSelect.value : '';
            const spekId = spesifikasiSelect ? spesifikasiSelect.value : '';
            const jumlahUnit = parseInt(jumlahUnitInput ? jumlahUnitInput.value : 0) || 0;
            const maxUnit = parseInt(jumlahUnitInput ? jumlahUnitInput.max : 0) || 0;
            
            const isValid = kontrakId && spekId && jumlahUnit > 0 && jumlahUnit <= maxUnit;
            const submitBtn = document.getElementById('submitSpkBtn');
            if (submitBtn) submitBtn.disabled = !isValid;
        }
        
        // Handle input validation
        if (jumlahUnitInput) {
            jumlahUnitInput.addEventListener('input', validateSpkForm);
        }
        
        // Initialize on modal show
        const spkModal = document.getElementById('spkModal');
        if (spkModal) {
            spkModal.addEventListener('show.bs.modal', function() {
                loadAvailableKontraks();
                // Reset form
                document.getElementById('spkForm').reset();
                document.getElementById('kontrakInfoSection').style.display = 'none';
                document.getElementById('spesifikasiSection').style.display = 'none';
                document.getElementById('spesifikasiDetail').style.display = 'none';
                document.getElementById('submitSpkBtn').disabled = true;
            });
        }
        
        // Add event listener for SPK type change
        const jenisSpkSelect = document.getElementById('jenisSpkSelect');
        if (jenisSpkSelect) {
            jenisSpkSelect.addEventListener('change', function() {
                
                const attachmentSection = document.getElementById('attachmentTargetSection');
                const targetUnitSelect = document.getElementById('targetUnitSelect');
                const kontrakSelect = document.getElementById('kontrakSelect');
                
                if (this.value === 'ATTACHMENT') {
                    // Show attachment target section
                    if (attachmentSection) attachmentSection.style.display = 'block';
                    if (targetUnitSelect) targetUnitSelect.setAttribute('required', 'required');
                    
                    // Load units for selected contract
                    if (kontrakSelect && kontrakSelect.value) {
                        loadContractUnitsForAttachment(kontrakSelect.value);
                    }
                } else {
                    // Hide attachment target section
                    if (attachmentSection) attachmentSection.style.display = 'none';
                    if (targetUnitSelect) {
                        targetUnitSelect.removeAttribute('required');
                        targetUnitSelect.value = '';
                        targetUnitSelect.innerHTML = '<option value="">- Select Destination Unit -</option>';
                    }
                }
                
                // Reset spesifikasi section when SPK type changes
                const spesifikasiSelect = document.getElementById('spesifikasiSelect');
                const spesifikasiDetail = document.getElementById('spesifikasiDetail');
                const submitSpkBtn = document.getElementById('submitSpkBtn');
                const attachmentInventoryList = document.getElementById('attachmentInventoryList');
                
                if (spesifikasiSelect) {
                    spesifikasiSelect.innerHTML = '<option value="">-- Select Specification --</option>';
                }
                if (spesifikasiDetail) spesifikasiDetail.style.display = 'none';
                if (submitSpkBtn) submitSpkBtn.disabled = true;
                if (attachmentInventoryList) attachmentInventoryList.innerHTML = '';
                
                // Reload specifications for selected contract if any
                if (kontrakSelect && kontrakSelect.value) {
                    loadKontrakSpesifikasiForSpk(kontrakSelect.value);
                }
            });
        }
        
        // Function to load units from contract for ATTACHMENT target
        function loadContractUnitsForAttachment(kontrakId) {
            if (!kontrakId) return;
            
            fetch(`<?= base_url('marketing/kontrak/units/') ?>${kontrakId}`)
                .then(r => r.json())
                .then(data => {
                    const select = document.getElementById('targetUnitSelect');
                    if (!select) return;
                    
                    select.innerHTML = '<option value="">- Select Destination Unit -</option>';
                    
                    if (data.success && Array.isArray(data.data) && data.data.length > 0) {
                        data.data.forEach(unit => {
                            const option = document.createElement('option');
                            option.value = unit.id;
                            option.textContent = `${unit.serial_number} - ${unit.jenis_unit || 'N/A'} (${unit.status || 'N/A'})`;
                            option.dataset.sn = unit.serial_number;
                            select.appendChild(option);
                        });
                    } else {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = '-- No units registered in this contract --';
                        option.disabled = true;
                        select.appendChild(option);
                    }
                })
                .catch(err => {
                    console.error('Failed to load contract units:', err);
                });
        }

        // Updated form submission to handle new workflow
        document.getElementById('spkForm').addEventListener('submit', (e)=>{
            e.preventDefault();
            const fd = new FormData(e.target);
            
            // Validate ATTACHMENT specific fields
            const jenisSpk = fd.get('jenis_spk');
            if (jenisSpk === 'ATTACHMENT') {
                const targetUnitId = fd.get('target_unit_id');
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
                        e.target.reset(); loadSpk(); loadMonitoring();
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
                        
                        loadSpk();
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
    <div class="modal fade" id="spkDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><?= lang('App.detail') ?> SPK <span id="spkNumberHeader" class="text-primary"></span></h6>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="spkDetailBody"><p class="text-muted">Loading...</p></div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-primary" id="btnPrintPdf" href="#" target="_blank" rel="noopener">Print PDF</a>
                    <button class="btn btn-warning" id="btnEditSpk" onclick="editSpk()">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger" id="btnDeleteSpk" onclick="deleteSpk()">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
                                    <div class="progress" style="height:16px">
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
                                    badge: isCompleted ? 'bg-success' : 'bg-secondary',
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
                
                // Reload SPK list
                loadSpk();
                
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
                        
                        // Reload SPK list
                        loadSpk();
                        
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
    </script>
    
    <style>
    /* Ensure the SPK modal body scrolls when content is long */
    #spkModal .modal-body { max-height: 70vh; overflow-y: auto; }
    
    /* Consistent DataTables-like sorting headers */
    </style>

    <!-- SPK Edit Modal -->
    <div class="modal fade" id="spkEditModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
    
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<!-- CSS untuk svcUnitDetailBlock agar konsisten dengan Service -->
<style>
.svcUnitDetailBlock {
    border-left: 3px solid #0d6efd;
    padding-left: 15px;
    background-color: #f8f9fa;
    margin-bottom: 10px;
}

.svcUnitDetailBlock > div {
    padding: 2px 0;
}
</style>
