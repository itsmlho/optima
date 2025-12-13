<?= $this->extend('layouts/base') ?>

<?php
// Simple permission check
$can_view = true;
$can_create = true;
$can_edit = true;
$can_delete = true;
$can_export = true;
?>

<?= $this->section('content') ?>
<style>
.filter-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}
.filter-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.filter-card.active {
    border: 2px solid #fff;
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}
</style>

    
    <?php if (!$can_view): ?>
    <div class="alert alert-warning">
        <i class="fas fa-lock me-2"></i>
        <strong>Access Denied:</strong> You do not have permission to view SPK Service. 
        Please contact your administrator to request access.
    </div>
    <?php else: ?>
    
    <!-- Statistics Cards -->
    <div class="row mt-3 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-primary-soft filter-card" data-filter="all" style="cursor: pointer;">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-list-task stat-icon text-primary"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-total-spk">0</div>
                        <div class="text-muted">Total SPK</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-info-soft filter-card" data-filter="IN_PROGRESS" style="cursor: pointer;">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-arrow-repeat stat-icon text-info"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-in-progress">0</div>
                        <div class="text-muted">In Progress</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-success-soft filter-card" data-filter="READY" style="cursor: pointer;">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-check-circle stat-icon text-success"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-ready">0</div>
                        <div class="text-muted">Ready</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-warning-soft filter-card" data-filter="COMPLETED" style="cursor: pointer;">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-flag stat-icon text-warning"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-completed">0</div>
                        <div class="text-muted">Completed</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Work Orders (SPK) List</h6>
                <div>
                    <input type="text" class="form-control form-control-sm" id="spkSearch" placeholder="Search SPK..." style="width: 250px;">
                </div>
            </div>
        </div>
        
        <!-- Filter Tabs -->
        <ul class="nav nav-tabs mb-3" id="filterTabs">
            <li class="nav-item">
                <a class="nav-link active filter-tab" href="#" data-filter="all">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link filter-tab" href="#" data-filter="SUBMITTED">Submitted</a>
            </li>
            <li class="nav-item">
                <a class="nav-link filter-tab" href="#" data-filter="IN_PROGRESS">In Progress</a>
            </li>
            <li class="nav-item">
                <a class="nav-link filter-tab" href="#" data-filter="READY">Ready</a>
            </li>
            <li class="nav-item">
                <a class="nav-link filter-tab" href="#" data-filter="COMPLETED">Completed</a>
            </li>
            <li class="nav-item">
                <a class="nav-link filter-tab" href="#" data-filter="CANCELLED">Cancelled</a>
            </li>
        </ul>
        
        <div class="card-body">
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <label class="me-2">Show</label>
                        <select class="form-select form-select-sm d-inline-block" id="entriesPerPage" style="width: auto;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <label class="ms-2">entries</label>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-manual-sort <?= !$can_view ? 'table-disabled' : '' ?>" id="spkList">
                    <thead><tr><th>SPK No.</th><th>Type</th><th>Contract/PO</th><th>Company Name</th><th>PIC</th><th>Contact</th><th>Status</th><th>Total Units</th><th data-no-sort>Action</th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
            
            <!-- Pagination and Info -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div id="spkTableInfo" class="table-info">
                    Showing 0 to 0 of 0 entries
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0" id="spkPagination">
                        <!-- Pagination will be generated by JavaScript -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>
	</div>

	<!-- Assign Items Modal (Unit + Attachment) -->
	<div class="modal fade" id="assignItemsModal" tabindex="-1" style="z-index: 9999;">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header"><h6 class="modal-title">Select Unit & Attachment</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
				<form id="assignItemsForm">
					<div class="modal-body">
						<input type="hidden" name="spk_id" id="assignSpkId">
						<div class="mb-2">
							<label class="form-label">Select Unit (Asset/Non-Asset Stock)</label>
							<input type="text" class="form-control" id="unitSearch" placeholder="Search unit no / serial / brand / model" autocomplete="off">
							<select class="form-select mt-2" id="unitPick" name="unit_id"></select>
							<div class="form-text">Type to search, then select from list.</div>
						</div>
						<div class="mb-2">
							<label class="form-label">Select Attachment (optional)</label>
							<input type="text" class="form-control" id="attSearch" placeholder="Search type/brand/model/SN/location" autocomplete="off">
							<select class="form-select mt-2" id="attPick" name="inventory_attachment_id"></select>
							<div class="form-text">Data from inventory_attachment (status 7/8).</div>
						</div>
					</div>
					<div class="modal-footer"><button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Save & Mark READY</button></div>
				</form>
			</div>
		</div>
	</div>

	<!-- Approval Stage Modal -->
	<div class="modal fade" id="approvalStageModal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h6 class="modal-title">Approval Confirmation - <span id="approvalStageTitle"></span></h6>
					<button class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<form id="approvalStageForm">
					<div class="modal-body">
						<!-- Common fields for all stages -->
						<div class="mb-3">
							<label class="form-label">Mechanics <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="approvalMekanik" name="mekanik" required 
								   placeholder="Enter the name of the responsible mechanic">
						</div>
						<div class="row">
							<div class="col-6">
								<label class="form-label">Estimated Start <span class="text-danger">*</span></label>
								<input type="date" class="form-control" id="approvalEstimasiMulai" name="estimasi_mulai" required>
							</div>
							<div class="col-6">
								<label class="form-label">Estimated Completion <span class="text-danger">*</span></label>
								<input type="date" class="form-control" id="approvalEstimasiSelesai" name="estimasi_selesai" required>
							</div>
						</div>

						<!-- Stage-specific content -->
						<div id="stageSpecificContent"></div>

						<div class="form-text mt-2">
							<small>This data will appear in the SPK PDF as the estimated work timeline and approval signature.</small>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
						<button class="btn btn-success" type="submit">Approve & Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Detail Modal -->
	<div class="modal fade" id="spkDetailModal" tabindex="-1">
		<div class="modal-dialog modal-lg modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h6 class="modal-title">Detail SPK <span id="spkNumberHeader" class="text-primary"></span></h6>
					<button class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<div id="spkDetailBody">
						<p class="text-muted">Memuat...</p>
					</div>
				</div>
				<div class="modal-footer">
					<div id="modalActionButtons">
						<!-- Buttons will be populated based on status -->
					</div>
					<button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Edit Modal -->
	<div class="modal fade" id="rollbackModal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-primary text-white">
					<h6 class="modal-title">
						<i class="fas fa-edit me-2"></i>Edit Stage - <span id="rollbackStageTitle"></span>
					</h6>
					<button class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<div class="alert alert-info">
						<i class="fas fa-info-circle me-2"></i>
						<strong>Info:</strong> Tindakan ini akan memperbarui data stage yang sudah di-approve dengan data yang baru.
					</div>
					
					<div id="rollbackInfo" class="mb-3">
						<!-- Rollback info will be populated here -->
					</div>
					
					<div class="mb-3">
						<label class="form-label">Alasan Edit <span class="text-danger">*</span></label>
						<textarea class="form-control" id="rollbackReason" rows="3" placeholder="Jelaskan alasan melakukan edit..." required></textarea>
						<div class="form-text">Alasan ini akan dicatat dalam audit trail untuk keperluan dokumentasi.</div>
					</div>
					
					<div id="unitChangeSection" style="display: none;">
						<div class="mb-3">
							<label class="form-label">Pilih Unit yang Akan Diubah <span class="text-danger">*</span></label>
							<select class="form-select mb-3" id="rollbackUnitIndexSelect" name="unit_index">
								<option value="">-- Pilih Unit yang Akan Diubah --</option>
							</select>
							<div class="mb-3">
								<label class="form-label">Pilih Unit Baru <span class="text-danger">*</span></label>
								<input type="text" class="form-control mb-2" id="rollbackUnitSearch" placeholder="Cari unit berdasarkan merk/model/SN..." autocomplete="off">
								<select class="form-select" id="rollbackUnitSelect" name="unit_id">
									<option value="">-- Pilih Unit Baru --</option>
								</select>
								<div class="form-text">Hanya unit dengan status AVAILABLE yang dapat dipilih.</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
					<button type="button" class="btn btn-primary" id="confirmRollbackBtn">
						<i class="fas fa-save me-1"></i>Simpan Perubahan
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Global variables (use var to avoid redeclaration errors)
if (typeof window.allSPKData === 'undefined') {
	window.allSPKData = [];
	window.filteredSPKData = [];
	window.currentFilter = 'all';
	window.currentPage = 1;
	window.entriesPerPage = 10;
}

// Use window references
var allSPKData = window.allSPKData;
var filteredSPKData = window.filteredSPKData;
var currentFilter = window.currentFilter;
var currentPage = window.currentPage;
var entriesPerPage = window.entriesPerPage;

// Unified notifier (fallbacks)
function notify(msg, type='success'){
	if (window.OptimaPro && typeof OptimaPro.showNotification==='function') return OptimaPro.showNotification(msg, type);
	if (typeof showNotification==='function') return showNotification(msg, type);
	alert(msg);
}

// Global function for SPA system to load SPK data
window.loadSPKData = function() {
	console.log('🚀 loadSPKData called');
	
	// Use existing load function if available
	if (typeof window.load === 'function') {
		console.log('✅ Using existing load function');
		window.load();
		return;
	}
	
	// Fallback: simple data refresh
	fetch('<?= base_url('service/spk/list') ?>')
		.then(response => response.json())
		.then(data => {
			console.log('✅ SPK data fetched for SPA');
			window.allSPKData = data.data || [];
			allSPKData = window.allSPKData; // Keep local reference
			
			// Call existing functions if available
			if (typeof updateStatistics === 'function') {
				updateStatistics();
			}
			if (typeof applyFilters === 'function') {
				applyFilters();
			}
		})
		.catch(e => {
			console.error('❌ SPK data load failed:', e);
		});
};

document.addEventListener('DOMContentLoaded', () => {
	const tbody = document.querySelector('#spkList tbody');
	
	const load = (startDate = null, endDate = null) => {
		let url = '<?= base_url('service/spk/list') ?>';
		if (startDate && endDate) {
			url += `?start_date=${startDate}&end_date=${endDate}`;
		}
		return fetch(url).then(r=>r.json()).then(j=>{
			window.allSPKData = j.data || [];
			allSPKData = window.allSPKData; // Keep local reference
			updateStatistics();
			applyFilters();
		});
	};
	
	// Load initial data
	load();
	
	// Make load function globally accessible
	window.load = load;
	
	function updateStatistics() {
		const total = allSPKData.length;
		const submitted = allSPKData.filter(item => (item.status || '').toUpperCase() === 'SUBMITTED').length;
		const inProgress = allSPKData.filter(item => (item.status || '').toUpperCase() === 'IN_PROGRESS').length;
		const ready = allSPKData.filter(item => (item.status || '').toUpperCase() === 'READY').length;
		const completed = allSPKData.filter(item => (item.status || '').toUpperCase() === 'COMPLETED' || (item.status || '').toUpperCase() === 'DELIVERED').length;
		const cancelled = allSPKData.filter(item => (item.status || '').toUpperCase() === 'CANCELLED').length;
		
		// Update with null checks
		const totalEl = document.getElementById('stat-total-spk');
		const inProgressEl = document.getElementById('stat-in-progress');
		const readyEl = document.getElementById('stat-ready');
		const completedEl = document.getElementById('stat-completed');
		
		if (totalEl) totalEl.textContent = total;
		if (inProgressEl) inProgressEl.textContent = inProgress;
		if (readyEl) readyEl.textContent = ready;
		if (completedEl) completedEl.textContent = completed;
	}
	
	function applyFilters() {
		const spkSearchEl = document.getElementById('spkSearch');
		const searchTerm = spkSearchEl ? spkSearchEl.value.toLowerCase() : '';
		
		// Filter by status
		let filtered = currentFilter === 'all' ? [...allSPKData] : 
					   currentFilter === 'COMPLETED' ? 
					   allSPKData.filter(item => ['COMPLETED', 'DELIVERED'].includes((item.status || '').toUpperCase())) :
					   allSPKData.filter(item => (item.status || '').toUpperCase() === currentFilter);
		
		// Filter by search term
		if (searchTerm) {
			filtered = filtered.filter(item => {
				return (item.nomor_spk || '').toLowerCase().includes(searchTerm) ||
					   (item.pelanggan || '').toLowerCase().includes(searchTerm) ||
					   (item.pic || '').toLowerCase().includes(searchTerm) ||
					   (item.kontak || '').toLowerCase().includes(searchTerm) ||
					   (item.lokasi || '').toLowerCase().includes(searchTerm);
			});
		}
		
		filteredSPKData = filtered;
		currentPage = 1; // Reset to first page
		renderSPKTable();
		updatePagination();
	}
	
	// Make functions globally accessible for SPA system
	window.updateStatistics = updateStatistics;
	window.applyFilters = applyFilters;
	
	function renderSPKTable() {
		// Get tbody reference each time to ensure it exists
		const tbody = document.querySelector('#spkList tbody');
		if (!tbody) {
			console.error('❌ tbody not found in renderSPKTable');
			return;
		}
		
		const startIndex = (currentPage - 1) * entriesPerPage;
		const endIndex = startIndex + entriesPerPage;
		const dataToShow = filteredSPKData.slice(startIndex, endIndex);
		
		tbody.innerHTML = '';
		dataToShow.forEach(r=>{
			const tr = document.createElement('tr');
			
			// Map status to Bootstrap badge classes
			function statusBadge(entity, status){
				const s = (status||'').toUpperCase();
				const mapSPK = { SUBMITTED:'secondary', IN_PROGRESS:'info', READY:'success', DELIVERED:'primary', COMPLETED:'primary', CANCELLED:'danger' };
				const mapDI  = { SUBMITTED:'secondary', DISPATCHED:'info', ARRIVED:'success', CANCELLED:'danger' };
				const cls = (entity==='DI'?mapDI[s]:mapSPK[s]) || 'secondary';
				return `<span class="badge bg-${cls}">${status}</span>`;
			}
			
			// Show approval stage buttons directly in table like operational/delivery.php
			let actionBtn = '';
			if (r.status === 'SUBMITTED') {
				actionBtn = '<span class="text-muted">Menunggu diproses</span>';
			} else if (r.status === 'IN_PROGRESS') {
				// Show approval stage buttons directly in table
				// Use new stage_status structure if available, otherwise fallback to old structure
				let persiapanDone = false;
				let fabrikasiDone = false;
				let paintingDone = false;
				let pdiDone = false;
				
				if (r.stage_status && r.stage_status.unit_stages) {
					// Use new structure from spk_unit_stages table
					const unitStages = r.stage_status.unit_stages;
					const stageOrder = ['persiapan_unit', 'fabrikasi', 'painting', 'pdi'];
					const totalUnits = parseInt(r.jumlah_unit) || 1;
					
					
					// Check if ALL units have completed each stage (for multi-unit SPK)
					stageOrder.forEach(stage => {
						const completedUnits = Object.keys(unitStages).filter(unitIndex => 
							unitStages[unitIndex][stage] && unitStages[unitIndex][stage].completed
						).length;
						
						
						if (completedUnits === totalUnits) {
							if (stage === 'persiapan_unit') persiapanDone = true;
							else if (stage === 'fabrikasi') fabrikasiDone = true;
							else if (stage === 'painting') paintingDone = true;
							else if (stage === 'pdi') pdiDone = true;
						}
					});
					
				} else {
					// Fallback to old structure
					persiapanDone = r.persiapan_unit_tanggal_approve ? true : false;
					fabrikasiDone = r.fabrikasi_tanggal_approve ? true : false;
					paintingDone = r.painting_tanggal_approve ? true : false;
					pdiDone = r.pdi_tanggal_approve ? true : false;
				}
				
				let approvalButtons = [];
				
				// Determine if this is an ATTACHMENT SPK (skip Persiapan Unit)
				const isAttachmentSpk = (r.jenis_spk && r.jenis_spk.toUpperCase() === 'ATTACHMENT');
				
				// Helper function to get next unit number for multi-unit SPK
				const getNextUnitNumber = (stage) => {
					if (r.stage_status && r.stage_status.unit_stages && r.jumlah_unit > 1) {
						const unitStages = r.stage_status.unit_stages;
						const completedUnits = Object.keys(unitStages).filter(unitIndex => 
							unitStages[unitIndex][stage] && unitStages[unitIndex][stage].completed
						).length;
						return completedUnits + 1; // Next unit to process
					}
					return null; // Single unit or no unit stages
				};
				
				// Add active button for current stage
				if (!persiapanDone && !isAttachmentSpk) {
					// Normal workflow: Start with Persiapan Unit (UNIT SPK only)
					const nextUnit = getNextUnitNumber('persiapan_unit');
					const unitText = nextUnit ? ` #${nextUnit}` : '';
					const unitIndex = nextUnit || 1;
					approvalButtons.push(`<button class="btn btn-sm btn-warning" onclick="openApprovalModal('persiapan_unit', 'Unit Preparation Dept.', ${r.id}, ${unitIndex})">Unit Preparation${unitText}</button>`);
				} else if ((!fabrikasiDone && persiapanDone) || (!fabrikasiDone && isAttachmentSpk)) {
					// Next stage: Fabrikasi (after Persiapan Unit for UNIT SPK, or directly for ATTACHMENT SPK)
					const nextUnit = getNextUnitNumber('fabrikasi');
					const unitText = nextUnit ? ` #${nextUnit}` : '';
					const unitIndex = nextUnit || 1;
					approvalButtons.push(`<button class="btn btn-sm btn-warning" onclick="openApprovalModal('fabrikasi', 'Fabrication Dept.', ${r.id}, ${unitIndex})">Fabrication${unitText}</button>`);
				} else if (!paintingDone) {
					const nextUnit = getNextUnitNumber('painting');
					const unitText = nextUnit ? ` #${nextUnit}` : '';
					const unitIndex = nextUnit || 1;
					approvalButtons.push(`<button class="btn btn-sm btn-warning" onclick="openApprovalModal('painting', 'Painting Dept.', ${r.id}, ${unitIndex})">Painting${unitText}</button>`);
				} else if (!pdiDone) {
					const nextUnit = getNextUnitNumber('pdi');
					const unitText = nextUnit ? ` #${nextUnit}` : '';
					const unitIndex = nextUnit || 1;
					approvalButtons.push(`<button class="btn btn-sm btn-warning" onclick="openApprovalModal('pdi', 'PDI Inspection Dept.', ${r.id}, ${unitIndex})">PDI Inspection${unitText}</button>`);
			} else {
				// All approvals done - should be READY status already
				approvalButtons.push('<span class="text-info">Waiting for READY status update</span>');
			}			// Add small completed badges (skip Unit Preparation badge for ATTACHMENT SPK)
			const completedBadges = [];
			if (persiapanDone && !isAttachmentSpk) completedBadges.push('<small class="badge bg-success me-1">✓ Unit Preparation</small>');
			if (isAttachmentSpk && !persiapanDone) completedBadges.push('<small class="badge bg-info me-1">Skip Unit Preparation</small>'); // Indicator that this step was skipped
			if (fabrikasiDone) completedBadges.push('<small class="badge bg-success me-1">✓ Fabrication</small>');
			if (paintingDone) completedBadges.push('<small class="badge bg-success me-1">✓ Painting</small>');
			if (pdiDone) completedBadges.push('<small class="badge bg-success me-1">✓ PDI</small>');				// No edit button on main page - edit options will be in detail modal
				actionBtn = approvalButtons.join(' ') + (completedBadges.length > 0 ? '<br>' + completedBadges.join('') : '');
			} else if (r.status === 'READY') {
				actionBtn = '<span class="text-success">Ready for delivery</span>';
			} else if (r.status === 'COMPLETED' || r.status === 'DELIVERED') {
				actionBtn = '<span class="text-success">Completed</span>';
			} else {
				actionBtn = '<span class="text-muted">-</span>';
			}
			
			tr.innerHTML = `<td><a href="#" onclick=\"openDetail(${r.id});return false;\">${r.nomor_spk}</a></td>`+
			  `<td><span class=\"badge bg-dark\">${r.jenis_spk||'UNIT'}</span></td>`+
			  `<td>${r.po_kontrak_nomor||'-'}</td>`+
			  `<td>${r.pelanggan||'-'}</td>`+
			  `<td>${r.pic||'-'}</td>`+
			  `<td>${r.kontak||'-'}</td>`+
			  `<td>${statusBadge('SPK', r.status)}</td>`+
			  `<td>${r.jumlah_unit||'-'}</td>`+
			  `<td>${actionBtn}</td>`;
			tbody.appendChild(tr);
		});
		
		// Update table info
		updateTableInfo();
	}
	
	function updatePagination() {
		const totalPages = Math.ceil(filteredSPKData.length / entriesPerPage);
		const pagination = document.getElementById('spkPagination');
		pagination.innerHTML = '';
		
		// Don't show pagination if only 1 page or no data
		if (totalPages <= 1) {
			return;
		}
		
		// Previous button
		const prevLi = document.createElement('li');
		prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
		const prevLink = document.createElement('a');
		prevLink.className = 'page-link';
		prevLink.href = '#';
		prevLink.textContent = '‹';
		prevLink.addEventListener('click', function(e) {
			e.preventDefault();
			e.stopPropagation();
			changePage(currentPage - 1);
		});
		prevLi.appendChild(prevLink);
		pagination.appendChild(prevLi);
		
		// Show page numbers with smart pagination
		const maxVisiblePages = 5;
		let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
		let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
		
		// Adjust startPage if we're near the end
		if (endPage - startPage + 1 < maxVisiblePages) {
			startPage = Math.max(1, endPage - maxVisiblePages + 1);
		}
		
		// First page + ellipsis if needed
		if (startPage > 1) {
			const firstLi = document.createElement('li');
			firstLi.className = 'page-item';
			const firstLink = document.createElement('a');
			firstLink.className = 'page-link';
			firstLink.href = '#';
			firstLink.textContent = '1';
			firstLink.addEventListener('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				changePage(1);
			});
			firstLi.appendChild(firstLink);
			pagination.appendChild(firstLi);
			
			if (startPage > 2) {
				const ellipsisLi = document.createElement('li');
				ellipsisLi.className = 'page-item disabled';
				ellipsisLi.innerHTML = '<span class="page-link">...</span>';
				pagination.appendChild(ellipsisLi);
			}
		}
		
		// Page numbers
		for (let i = startPage; i <= endPage; i++) {
			const li = document.createElement('li');
			li.className = `page-item ${currentPage === i ? 'active' : ''}`;
			const pageLink = document.createElement('a');
			pageLink.className = 'page-link';
			pageLink.href = '#';
			pageLink.textContent = i;
			pageLink.addEventListener('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				changePage(i);
			});
			li.appendChild(pageLink);
			pagination.appendChild(li);
		}
		
		// Last page + ellipsis if needed
		if (endPage < totalPages) {
			if (endPage < totalPages - 1) {
				const ellipsisLi = document.createElement('li');
				ellipsisLi.className = 'page-item disabled';
				ellipsisLi.innerHTML = '<span class="page-link">...</span>';
				pagination.appendChild(ellipsisLi);
			}
			
			const lastLi = document.createElement('li');
			lastLi.className = 'page-item';
			const lastLink = document.createElement('a');
			lastLink.className = 'page-link';
			lastLink.href = '#';
			lastLink.textContent = totalPages;
			lastLink.addEventListener('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				changePage(totalPages);
			});
			lastLi.appendChild(lastLink);
			pagination.appendChild(lastLi);
		}
		
		// Next button
		const nextLi = document.createElement('li');
		nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
		const nextLink = document.createElement('a');
		nextLink.className = 'page-link';
		nextLink.href = '#';
		nextLink.textContent = '›';
		nextLink.addEventListener('click', function(e) {
			e.preventDefault();
			e.stopPropagation();
			changePage(currentPage + 1);
		});
		nextLi.appendChild(nextLink);
		pagination.appendChild(nextLi);
	}
	
	function updateTableInfo() {
		const totalEntries = filteredSPKData.length;
		const start = totalEntries === 0 ? 0 : ((currentPage - 1) * entriesPerPage) + 1;
		const end = Math.min(currentPage * entriesPerPage, totalEntries);
		const tableInfoEl = document.getElementById('spkTableInfo');
		if (tableInfoEl) {
			tableInfoEl.textContent = `Showing ${start} to ${end} of ${totalEntries} entries`;
		}
	}
	
	window.changePage = function(page) {
		const totalPages = Math.ceil(filteredSPKData.length / entriesPerPage);
		if (page >= 1 && page <= totalPages && page !== currentPage) {
			currentPage = page;
			renderSPKTable();
			updatePagination();
			updateTableInfo();
			
			// Scroll to top of table for better UX
			const table = document.getElementById('spkList');
			if (table) {
				table.scrollIntoView({ behavior: 'smooth', block: 'start' });
			}
		}
	}
	
	// Event listeners (with null checks)
	const entriesPerPageEl = document.getElementById('entriesPerPage');
	if (entriesPerPageEl) {
		entriesPerPageEl.addEventListener('change', function() {
			entriesPerPage = parseInt(this.value);
			currentPage = 1;
			renderSPKTable();
			updatePagination();
		});
	}
	
	const spkSearchEl = document.getElementById('spkSearch');
	if (spkSearchEl) {
		spkSearchEl.addEventListener('input', function() {
			applyFilters();
		});
	}
	
	// Filter card click listeners
	document.querySelectorAll('.filter-card').forEach(card => {
		card.addEventListener('click', function() {
			const filter = this.dataset.filter;
			currentFilter = filter;
			
			// Update active card
			document.querySelectorAll('.filter-card').forEach(c => c.classList.remove('active'));
			this.classList.add('active');
			
			// Update tab active state
			document.querySelectorAll('.filter-tab').forEach(tab => {
				if (tab.dataset.filter === filter) {
					tab.classList.add('active');
				} else {
					tab.classList.remove('active');
				}
			});
			
			applyFilters();
		});
	});
	
	// Filter tab click listeners
	document.querySelectorAll('.filter-tab').forEach(tab => {
		tab.addEventListener('click', function(e) {
			e.preventDefault();
			const filter = this.dataset.filter;
			currentFilter = filter;
			
			// Update active tab
			document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
			this.classList.add('active');
			
			// Update card active state
			document.querySelectorAll('.filter-card').forEach(c => {
				if (c.dataset.filter === filter) {
					c.classList.add('active');
				} else {
					c.classList.remove('active');
				}
			});
			
			applyFilters();
		});
	});
	
	load();
	
	window.confirmReady = (id) => {
		fetch('<?= base_url('service/spk/confirm-ready/') ?>' + id, {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}})
		 .then(r=>r.json()).then(()=>load());
	}

	let currentSpkId = null;
	window.openDetail = (id) => {
		currentSpkId = id;
		const body = document.getElementById('spkDetailBody');
		const headerSpan = document.getElementById('spkNumberHeader');
		body.innerHTML = '<p class="text-muted">Memuat...</p>';

		fetch('<?= base_url('service/spk/detail/') ?>' + id).then(r=>r.json()).then(j=>{
			if (!j.success) { body.innerHTML = '<div class="text-danger">Gagal memuat detail</div>'; return; }
            const d = j.data || {};
            const s = j.spesifikasi || {};
			
			// Update modal title with SPK number
			if (headerSpan && d.nomor_spk) {
				headerSpan.textContent = '#' + d.nomor_spk;
			}
			const status = d.status || 'SUBMITTED';
			
			// Update action buttons based on status
			const actionDiv = document.getElementById('modalActionButtons');
			let actionButtons = '';
			
			if (status === 'SUBMITTED') {
				actionButtons = '<button class="btn btn-success btn-sm" id="btnProsesSPK">Proses SPK</button>';
			} else if (status === 'IN_PROGRESS') {
				// Show approval stage buttons based on completion status
				let approvalButtons = [];
				
				// Check which stages are completed using new structure
				let persiapanDone = false;
				let fabrikasiDone = false;
				let paintingDone = false;
				let pdiDone = false;
				
				if (d.stage_status && d.stage_status.unit_stages) {
					// Use new structure from spk_unit_stages table
					const unitStages = d.stage_status.unit_stages;
					const stageOrder = ['persiapan_unit', 'fabrikasi', 'painting', 'pdi'];
					
					// Check if any unit has completed each stage
					Object.keys(unitStages).forEach(unitIndex => {
						const unitStage = unitStages[unitIndex];
						stageOrder.forEach(stage => {
							if (unitStage[stage] && unitStage[stage].completed) {
								if (stage === 'persiapan_unit') persiapanDone = true;
								else if (stage === 'fabrikasi') fabrikasiDone = true;
								else if (stage === 'painting') paintingDone = true;
								else if (stage === 'pdi') pdiDone = true;
							}
						});
					});
				} else {
					// Fallback to old structure
					persiapanDone = d.persiapan_unit_tanggal_approve ? true : false;
					fabrikasiDone = d.fabrikasi_tanggal_approve ? true : false;
					paintingDone = d.painting_tanggal_approve ? true : false;
					pdiDone = d.pdi_tanggal_approve ? true : false;
				}
				
				// Determine if this is an ATTACHMENT SPK (skip Persiapan Unit)
				const isAttachmentSpk = (d.jenis_spk && d.jenis_spk.toUpperCase() === 'ATTACHMENT');
				
				// Add buttons for incomplete stages
				if (!persiapanDone && !isAttachmentSpk) {
					// Normal workflow: Start with Unit Preparation (UNIT SPK only)
					approvalButtons.push('<button class="btn btn-warning btn-sm" onclick="openApprovalModal(\'persiapan_unit\', \'Unit Preparation Dept.\')">Unit Preparation</button>');
				} else if ((!fabrikasiDone && persiapanDone) || (!fabrikasiDone && isAttachmentSpk)) {
					// Next stage: Fabrication (after Unit Preparation for UNIT SPK, or directly for ATTACHMENT SPK)
					approvalButtons.push('<button class="btn btn-warning btn-sm" onclick="openApprovalModal(\'fabrikasi\', \'Fabrication Dept.\')">Fabrication</button>');
				} else if (!paintingDone) {
					approvalButtons.push('<button class="btn btn-warning btn-sm" onclick="openApprovalModal(\'painting\', \'Painting Dept.\')">Painting</button>');
				} else if (!pdiDone) {
					approvalButtons.push('<button class="btn btn-warning btn-sm" onclick="openApprovalModal(\'pdi\', \'PDI Inspection Dept.\')">PDI Inspection</button>');
				}
				
				// Show completed stages with checkmarks (skip Unit Preparation for ATTACHMENT SPK)
				if (persiapanDone && !isAttachmentSpk) approvalButtons.push('<span class="badge bg-success me-1">✓ Unit Preparation</span>');
				if (isAttachmentSpk && !persiapanDone) approvalButtons.push('<span class="badge bg-info me-1">Skip Unit Preparation</span>'); // Indicator that this step was skipped
				if (fabrikasiDone) approvalButtons.push('<span class="badge bg-success me-1">✓ Fabrication</span>');
				if (paintingDone) approvalButtons.push('<span class="badge bg-success me-1">✓ Painting</span>');
				if (pdiDone) approvalButtons.push('<span class="badge bg-success me-1">✓ PDI</span>');
				
				// For multi-unit SPK, do not show assignment button; items are accumulated per-cycle
				const showAssign = (d.jumlah_unit||1) === 1 && pdiDone;
				
				// Add edit button if any stages are completed
				const showEdit = persiapanDone || fabrikasiDone || paintingDone || pdiDone;
				
				actionButtons = `
					<a class="btn btn-outline-secondary btn-sm" id="btnPrintPdfSvc" href="<?= base_url('service/spk/print/') ?>${id}" target="_blank" rel="noopener">Print PDF</a>
					${approvalButtons.join(' ')}
					${showAssign ? '<button class="btn btn-primary btn-sm" onclick="openAssign(' + id + '); bootstrap.Modal.getInstance(document.getElementById(\'spkDetailModal\')).hide();">Pilih Unit & Attachment</button>' : ''}
					${showEdit ? '<button class="btn btn-outline-primary btn-sm edit-spk-btn" data-spk-id="' + id + '" title="Edit Options"><i class="fas fa-edit me-1"></i>Edit</button>' : ''}
				`;
			} else if (status === 'READY' || status === 'DELIVERED' || status === 'COMPLETED') {
				actionButtons = `<a class="btn btn-outline-secondary btn-sm" id="btnPrintPdfSvc" href="<?= base_url('service/spk/print/') ?>${id}" target="_blank" rel="noopener">Print PDF</a>`;
			}
			
			actionDiv.innerHTML = actionButtons;
			
			// Add event listener for edit button
			const editBtn = actionDiv.querySelector('.edit-spk-btn');
			if (editBtn) {
				editBtn.addEventListener('click', function() {
					const spkId = this.getAttribute('data-spk-id');
					showEditOptions(spkId);
					bootstrap.Modal.getInstance(document.getElementById('spkDetailModal')).hide();
				});
			}
			
			snList = (s.selected && s.selected.unit) ? [
				s.selected.unit.serial_number ? `Unit: ${s.selected.unit.serial_number}` : null,
				s.selected.unit.sn_mast ? `Mast: ${s.selected.unit.sn_mast}` : null,
				s.selected.unit.sn_mesin ? `Mesin: ${s.selected.unit.sn_mesin}` : null,
				s.selected.unit.sn_baterai ? `Baterai: ${s.selected.unit.sn_baterai}` : null,
				s.selected.unit.sn_charger ? `Charger: ${s.selected.unit.sn_charger}` : null,
			].filter(Boolean) : [];

			// Get approval workflow data
			const workflowData = {
				persiapan_unit_id: d.persiapan_unit_id,
				persiapan_aksesoris_tersedia: d.persiapan_aksesoris_tersedia,
				fabrikasi_attachment_id: d.fabrikasi_attachment_id,
				pdi_catatan: d.pdi_catatan,
			};

			// Build accessories text robustly (array or JSON string)
			let aksText = '-';
			if (j.kontrak_spec && j.kontrak_spec.aksesoris) {
				const aks = j.kontrak_spec.aksesoris;
				if (Array.isArray(aks) && aks.length) aksText = aks.join(', ');
				else if (typeof aks === 'string') {
					try { const parsed = JSON.parse(aks); aksText = Array.isArray(parsed) && parsed.length ? parsed.join(', ') : (aks.trim() || '-'); }
					catch(e){ aksText = aks.trim() || '-'; }
				}
			} else if (s && s.aksesoris) {
				if (Array.isArray(s.aksesoris) && s.aksesoris.length) aksText = s.aksesoris.join(', ');
				else if (typeof s.aksesoris === 'string') {
					try { const parsed = JSON.parse(s.aksesoris); aksText = Array.isArray(parsed) && parsed.length ? parsed.join(', ') : (s.aksesoris.trim() || '-'); }
					catch(e){ aksText = (s.aksesoris||'').trim() || '-'; }
				}
			}

			const prepared = Array.isArray(j.prepared_units) ? j.prepared_units : [];
			const totalUnits = parseInt(d.jumlah_unit||1);
			const preparedCount = prepared.length;

			// Prefer prepared_units_detail for distinct per-unit display
			const preparedDetails = (j.spesifikasi && Array.isArray(j.spesifikasi.prepared_units_detail)) ? j.spesifikasi.prepared_units_detail : [];
			let itemsHtml = '';
			if (preparedDetails.length > 0) {
				itemsHtml = preparedDetails.map((it, idx) => `
					<div class="col-12"><strong>Item yang dipilih: (${idx+1}):</strong></div>
					<div class="col-12 svcUnitDetailBlock">
						<div><strong>Unit:</strong> ${it.unit_label || '-'}</div>
						<div><strong>Serial Number:</strong> ${it.serial_number || '-'}</div>
						<div><strong>Tipe Unit:</strong> ${it.tipe_jenis || '-'}</div>
						<div><strong>Merk/Model:</strong> ${(it.merk_unit || '-') + ' ' + (it.model_unit || '')}</div>
						${ it.attachment_label ? `<div><strong>Attachment:</strong> ${it.attachment_label}</div>` : ''}
						${ it.catatan ? `<div><strong>Catatan:</strong> ${it.catatan}</div>` : ''}
						${ it.mekanik ? `<div><strong>Mekanik:</strong> ${it.mekanik}</div>` : ''}
						${ it.timestamp ? `<div class=\"text-muted\"><small>Waktu: ${it.timestamp}</small></div>` : ''}
						<div class="col-12"><hr></div>
					</div>
				`).join('');
			} else {
				// Fallback to duplicate the selected unit detail per jumlah_unit
				function renderItemBlock(i, total) {
					return `
						<div class="col-12"><strong>Item Terpilih${total > 1 ? ' ('+i+')' : ''}:</strong></div>
						<div class="col-12 svcUnitDetailBlock">
							${(s.selected && s.selected.unit) ? `
								<div><strong>Unit:</strong> ${s.selected.unit.label || `${s.selected.unit.no_unit || '-'} | ${s.selected.unit.merk_unit || '-'} | ${s.selected.unit.model_unit || '-'} | ${s.selected.unit.jenis_unit || '-'}`}</div>
								<div><strong>Serial Number:</strong> ${s.selected.unit.serial_number || '-'}</div>
								<div><strong>Tipe Unit:</strong> ${s.selected.unit.tipe_jenis || '-'}</div>
								<div><strong>Kapasitas:</strong> ${s.selected.unit.kapasitas_name || '-'}</div>
								<div><strong>Mast:</strong> ${s.selected.unit.mast || s.selected.unit.mast_model || '-'}</div>
								<div><strong>Roda:</strong> ${s.selected.unit.roda || '-'}</div>
								<div><strong>Ban:</strong> ${s.selected.unit.ban || '-'}</div>
								<div><strong>Valve:</strong> ${s.selected.unit.valve || '-'}</div>
							` : (s.selected && s.selected.unit && s.selected.unit.label ? `<div><strong>Unit:</strong> ${s.selected.unit.label}</div>` : '<div class=\"text-muted\">Unit: -</div>')}
							${ (s.selected && s.selected.attachment) ? `
								<div><strong>Attachment:</strong> ${s.selected.attachment.tipe || '-'} | ${s.selected.attachment.merk || '-'} | ${s.selected.attachment.model || '-'}${s.selected.attachment.sn_attachment ? ` [SN: ${s.selected.attachment.sn_attachment}]` : ''}${s.selected.attachment.lokasi_penyimpanan ? ` @ ${s.selected.attachment.lokasi_penyimpanan}` : ''}</div>
							` : ''}
								<div><strong>Catatan:</strong> ${(s.selected && s.selected.catatan) ? s.selected.catatan : '-'}</div>
								<div class="col-12"><hr></div>
						</div>
					`;
				}
				for (let i = 1; i <= totalUnits; i++) { itemsHtml += renderItemBlock(i, totalUnits); }
			}

			// Use kontrak_spec as primary source for specification data if available
			const ks = j.kontrak_spec || {};

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
					${totalUnits>1 ? `<div class="col-6">
						<div class="progress" style="height:14px"><div class="progress-bar" role="progressbar" style="width:${Math.min(100, Math.round((preparedCount/totalUnits)*100))}%">${preparedCount}/${totalUnits} prepared</div></div>
					</div>`:''}
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
					
					${status === 'IN_PROGRESS' || status === 'READY' || status === 'DELIVERED' || status === 'COMPLETED' ? `
					<div class="col-12"><hr></div>
					<div class="col-12"><h6 class="mb-2">📋 Status Approval Workflow</h6></div>
					
					<div class="col-12">
						<div class="row g-2">
							${(d.jenis_spk && d.jenis_spk.toUpperCase() === 'ATTACHMENT') ? `
								<div class="col-12">
									<strong>1. Unit Preparation:</strong> 
									<span class="badge bg-info">Skip (SPK Attachment)</span>
								</div>
								<div class="col-6">
									<strong>2. Fabrication:</strong> 
									${d.fabrikasi_tanggal_approve ? 
										'<span class="badge bg-success">✓ Completed</span><br>' +
										'<small>By: ' + (d.fabrikasi_mekanik||'-') + ' <br>' +
										'Date: ' + (d.fabrikasi_tanggal_approve||'-') + '<br>' +
										'Attachment ID: ' + (workflowData.fabrikasi_attachment_id||'-') + '</small>' 
										: '<span class="badge bg-warning">Waiting</span>'}
								</div>
							` : `
								<div class="col-6">
									<strong>1. Unit Preparation:</strong> 
									${d.stage_status && d.stage_status.unit_stages ? 
										Object.keys(d.stage_status.unit_stages).some(unitIndex => 
											d.stage_status.unit_stages[unitIndex].persiapan_unit && 
											d.stage_status.unit_stages[unitIndex].persiapan_unit.completed
										) ? 
										'<span class="badge bg-success">✓ Completed</span><br>' +
										'<small>Unit: ' + Object.keys(d.stage_status.unit_stages).filter(unitIndex => 
											d.stage_status.unit_stages[unitIndex].persiapan_unit && 
											d.stage_status.unit_stages[unitIndex].persiapan_unit.completed
										).length + '/' + d.jumlah_unit + '</small>' 
										: '<span class="badge bg-warning">Waiting</span>'
									: (d.persiapan_unit_tanggal_approve ? 
										'<span class="badge bg-success">✓ Completed</span><br>' +
										'<small>By: ' + (d.persiapan_unit_mekanik||'-') + ' <br>' +
										'Date: ' + (d.persiapan_unit_tanggal_approve||'-') + '<br>' +
										'Unit ID: ' + (workflowData.persiapan_unit_id||'-') + '</small>' 
										: '<span class="badge bg-warning">Waiting</span>')}
								</div>
								<div class="col-6">
									<strong>2. Fabrication:</strong> 
									${d.stage_status && d.stage_status.unit_stages ? 
										Object.keys(d.stage_status.unit_stages).some(unitIndex => 
											d.stage_status.unit_stages[unitIndex].fabrikasi && 
											d.stage_status.unit_stages[unitIndex].fabrikasi.completed
										) ? 
										'<span class="badge bg-success">✓ Completed</span><br>' +
										'<small>Unit: ' + Object.keys(d.stage_status.unit_stages).filter(unitIndex => 
											d.stage_status.unit_stages[unitIndex].fabrikasi && 
											d.stage_status.unit_stages[unitIndex].fabrikasi.completed
										).length + '/' + d.jumlah_unit + '</small>' 
										: '<span class="badge bg-warning">Waiting</span>'
									: (d.fabrikasi_tanggal_approve ? 
										'<span class="badge bg-success">✓ Completed</span><br>' +
										'<small>By: ' + (d.fabrikasi_mekanik||'-') + ' <br>' +
										'Date: ' + (d.fabrikasi_tanggal_approve||'-') + '<br>' +
										'Attachment ID: ' + (workflowData.fabrikasi_attachment_id||'-') + '</small>' 
										: '<span class="badge bg-warning">Waiting</span>')}
								</div>
							`}
							<div class="col-6">
								<strong>3. Painting:</strong> 
								${d.stage_status && d.stage_status.unit_stages ? 
									Object.keys(d.stage_status.unit_stages).some(unitIndex => 
										d.stage_status.unit_stages[unitIndex].painting && 
										d.stage_status.unit_stages[unitIndex].painting.completed
									) ? 
									'<span class="badge bg-success">✓ Completed</span><br>' +
									'<small>Unit: ' + Object.keys(d.stage_status.unit_stages).filter(unitIndex => 
										d.stage_status.unit_stages[unitIndex].painting && 
										d.stage_status.unit_stages[unitIndex].painting.completed
									).length + '/' + d.jumlah_unit + '</small>' 
									: '<span class="badge bg-warning">Waiting</span>'
								: (d.painting_tanggal_approve ? 
									`<span class="badge bg-success">✓ Completed</span><br>
									<small>By: ${d.painting_mekanik||'-'} <br>
									Date: ${d.painting_tanggal_approve||'-'}</small>` 
									: '<span class="badge bg-warning">Waiting</span>')}
							</div>
							<div class="col-6">
								<strong>4. PDI Done :</strong> 
								${d.stage_status && d.stage_status.unit_stages ? 
									Object.keys(d.stage_status.unit_stages).some(unitIndex => 
										d.stage_status.unit_stages[unitIndex].pdi && 
										d.stage_status.unit_stages[unitIndex].pdi.completed
									) ? 
									'<span class="badge bg-success">✓ Completed</span><br>' +
									'<small>Unit: ' + Object.keys(d.stage_status.unit_stages).filter(unitIndex => 
										d.stage_status.unit_stages[unitIndex].pdi && 
										d.stage_status.unit_stages[unitIndex].pdi.completed
									).length + '/' + d.jumlah_unit + '</small>' 
									: '<span class="badge bg-warning">Waiting</span>'
								: (d.pdi_tanggal_approve ? 
									'<span class="badge bg-success">✓ Completed</span><br>' +
									'<small>By: ' + (d.pdi_mekanik||'-') + ' <br>' +
									'Date: ' + (d.pdi_tanggal_approve||'-') + '</small>' 
									: '<span class="badge bg-warning">Waiting</span>')}
							</div>
						</div>
					</div>
					
					${workflowData.persiapan_aksesoris_tersedia ? `
					<div class="col-12"><hr></div>
					<div class="col-12">
						<strong>📦 Accessories Available (Unit Preparation):</strong><br>
						<small class="text-success">${workflowData.persiapan_aksesoris_tersedia}</small>
					</div>` : ''}
					
					${workflowData.pdi_catatan ? `
					<div class="col-12">
						<strong>📝 PDI Notes:</strong><br>
						<small class="text-info">${workflowData.pdi_catatan}</small>
					</div>` : ''}
					` : ''}
					
					<div class="col-12"><hr></div>
					${itemsHtml}
				</div>`;
			// Detail print unit sudah diambil dari marketing/print_spk.php
			
			// Add event listener for Proses SPK button
			setTimeout(() => {
				const prosesSPKBtn = document.getElementById('btnProsesSPK');
				if (prosesSPKBtn) {
					prosesSPKBtn.addEventListener('click', () => {
						const formData = new FormData();
						formData.append('status', 'IN_PROGRESS');
						
						fetch('<?= base_url('service/spk/update-status/') ?>' + id, {
							method: 'POST',
							headers: {'X-Requested-With': 'XMLHttpRequest'},
							body: formData
						}).then(r=>r.json()).then(result=>{
							if (result && result.success) {
								notify('SPK successfully processed. Status changed to IN_PROGRESS.', 'success');
								bootstrap.Modal.getInstance(document.getElementById('spkDetailModal')).hide();
								load(); // Reload table
							} else {
								notify(result.message || 'Failed to process SPK', 'error');
							}
						});
					});
				}
			}, 100);
			
			new bootstrap.Modal(document.getElementById('spkDetailModal')).show();
		});
	}
	// Service cannot change status directly; use assignment modal
	// Assign Items handlers
	window.openAssign = (spkId) => {
		console.log('openAssign called with SPK ID:', spkId);
		
		// Set values
		document.getElementById('assignSpkId').value = spkId;
		document.getElementById('unitSearch').value = '';
		document.getElementById('attSearch').value = '';
		document.getElementById('unitPick').innerHTML = '';
		document.getElementById('attPick').innerHTML = '<option value="">- (Opsional) -</option>';
		
		// Show modal with proper configuration
		const modalElement = document.getElementById('assignItemsModal');
		const modal = new bootstrap.Modal(modalElement, {
			backdrop: 'static',
			keyboard: false,
			focus: true
		});
		
		modal.show();
		console.log('Modal assignItemsModal shown');
	}
	const searchBox = document.getElementById('unitSearch');
	searchBox.addEventListener('input', function(){
		const q = this.value.trim();
		const url = new URL('<?= base_url('service/data-unit/simple') ?>', window.location.origin);
		if (q) url.searchParams.set('q', q);
		fetch(url).then(r=>r.json()).then(j=>{
			const sel = document.getElementById('unitPick');
			sel.innerHTML = '<option value="">- Select Unit -</option>' + (j.data||[]).map(x=>`<option value="${x.id}">${x.label}</option>`).join('');
		});
	});
	const attSearch = document.getElementById('attSearch');
	attSearch.addEventListener('input', function(){
		const q = this.value.trim();
		const url = new URL('<?= base_url('service/data-attachment/simple') ?>', window.location.origin);
		if (q) url.searchParams.set('q', q);
		fetch(url).then(r=>r.json()).then(j=>{
			const sel = document.getElementById('attPick');
			sel.innerHTML = '<option value="">- (Opsional) -</option>' + (j.data||[]).map(x=>`<option value="${x.id}">${x.label}</option>`).join('');
		});
	});
	document.getElementById('assignItemsForm').addEventListener('submit', function(e){
		e.preventDefault();
		const fd = new FormData(this);
		fetch('<?= base_url('service/spk/assign-items') ?>', {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body: fd})
			.then(r=>r.json()).then(j=>{
				if (j && j.success) {
					bootstrap.Modal.getInstance(document.getElementById('assignItemsModal')).hide();
					load();
					notify('Item saved. SPK status changed to READY.', 'success');
				} else {
					notify(j.message || 'Failed to save item', 'error');
				}
			});
	});
	
	// Approval Stage Modal Functions
	let currentApprovalStage = '';
	let currentApprovalSpkId = null;
	let currentEditingUnitIndex = null; // For multi-unit SPK editing
	
	window.openApprovalModal = (stage, stageTitle, spkId, unitIndex = null) => {
		currentApprovalStage = stage;
		currentApprovalSpkId = spkId || currentSpkId; // Use passed spkId or fallback to currentSpkId
		console.log('🆔 SPK ID Debug:', {spkId, currentSpkId, currentApprovalSpkId});
		document.getElementById('approvalStageTitle').textContent = stageTitle;
		document.getElementById('approvalMekanik').value = '';
		document.getElementById('approvalEstimasiMulai').value = '';
		document.getElementById('approvalEstimasiSelesai').value = '';
		
		// Set unit index if provided (for multi-unit SPK editing)
		if (unitIndex !== null) {
			currentEditingUnitIndex = unitIndex;
			console.log('Setting currentEditingUnitIndex to:', unitIndex);
		}
		
		// Load stage-specific content
		loadStageSpecificContent(stage, currentApprovalSpkId);
		
		new bootstrap.Modal(document.getElementById('approvalStageModal')).show();
	}
	
	// Generate unit form untuk multi-unit support
	function generateUnitForm(unitIndex, totalUnits, isFirst = false) {
		// Always use a simple suffix now that we're handling one unit at a time
		const suffix = '';
		const cardClass = 'border-primary';
		
		return `
			<div class="card mb-3 ${cardClass}">
				<div class="card-header">
					<h6 class="mb-0">
						<i class="fas fa-cogs me-2"></i>
						Unit ${unitIndex} of ${totalUnits}
					</h6>
				</div>
				<div class="card-body">
					<div class="mb-3">
						<label class="form-label">Select Unit <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="approvalUnitSearch${suffix}" placeholder="Search unit number / serial / brand / model" autocomplete="off">
						<select class="form-select mt-2" id="approvalUnitPick${suffix}" name="unit_id" required></select>
						<div class="form-text">Type to search, then select from the list.</div>
					</div>
					<div class="mb-3">
						<label class="form-label">Select Area <span class="text-danger">*</span></label>
						<select class="form-select" id="approvalAreaPick${suffix}" name="area_id" required>
							<option value="">- Select Area -</option>
						</select>
						<div class="form-text">Select an area for this unit. The area determines the assignment of foreman and mechanic for the work order.</div>
					</div>
					<div id="electricFields${suffix}" class="mb-3" style="display: none;">
						<!-- Smart Component Management will inject content here -->
						<div id="electricFieldsContent${suffix}">
							<!-- Legacy fallback if smart detection fails -->
							<div class="alert alert-info">
								<i class="fas fa-battery-full me-2"></i>Unit Electric requires selection of Battery and Charger
							</div>
							<div class="row">
								<div class="col-md-6">
									<label class="form-label">Select Battery <span class="text-danger">*</span></label>
									<select class="form-select" id="batteryPick${suffix}" name="battery_inventory_attachment_id">
										<option value="">- Select Battery -</option>
									</select>
								</div>
								<div class="col-md-6">
									<label class="form-label">Select Charger <span class="text-danger">*</span></label>
									<select class="form-select" id="chargerPick${suffix}" name="charger_inventory_attachment_id">
										<option value="">- Select Charger -</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div id="fabrikasiFields${suffix}" class="mb-3" style="display: none;">
						<!-- Smart Attachment Management untuk Fabrikasi akan inject content di sini -->
						<div id="fabrikasiFieldsContent${suffix}">
							<!-- Legacy fallback if smart detection fails -->
							<div class="alert alert-info">
								<i class="fas fa-tools me-2"></i>Fabrication unit requires selection of Attachment
							</div>
							<div class="row">
								<div class="col-md-12">
									<label class="form-label">Select Attachment <span class="text-danger">*</span></label>
									<select class="form-select" id="attachmentPick${suffix}" name="attachment_id">
										<option value="">- Select Attachment -</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		`;
	}
	
	function loadStageSpecificContent(stage, spkId) {
		const container = document.getElementById('stageSpecificContent');
		
		if (stage === 'persiapan_unit') {
			// Get SPK details to show requested accessories and unit count
			fetch('<?= base_url('service/spk/detail/') ?>' + spkId).then(r=>r.json()).then(j=>{
				if (j.success) {
					const spkData = j.data || {};
					const spesifikasi = j.spesifikasi || {};
					const totalUnits = parseInt(spkData.jumlah_unit || 1);
					const preparedUnits = Array.isArray(j.prepared_units) ? j.prepared_units : [];
					
					console.log('Unit calculation:', {
						totalUnits,
						preparedUnitsLength: preparedUnits.length,
						nextUnitIndex: preparedUnits.length + 1,
						currentEditingUnitIndex: currentEditingUnitIndex
					});
					
					// Use currentEditingUnitIndex if set (for editing), otherwise use next unit index
					const unitIndex = currentEditingUnitIndex || (preparedUnits.length + 1);
					
					// Prefer aksesoris from kontrak_spec for consistency with Marketing
					let aksesoris = [];
					if (j.kontrak_spec && j.kontrak_spec.aksesoris) {
						const aks = j.kontrak_spec.aksesoris;
						if (Array.isArray(aks)) aksesoris = aks;
						else if (typeof aks === 'string' && aks.trim()) {
							try { const parsed = JSON.parse(aks); if (Array.isArray(parsed)) aksesoris = parsed; else aksesoris = [aks.trim()]; }
							catch(e){ aksesoris = [aks.trim()]; }
						}
					} else if (spesifikasi.aksesoris) {
						if (Array.isArray(spesifikasi.aksesoris)) aksesoris = spesifikasi.aksesoris;
						else if (typeof spesifikasi.aksesoris === 'string' && spesifikasi.aksesoris.trim()) {
							try { const parsed = JSON.parse(spesifikasi.aksesoris); if (Array.isArray(parsed)) aksesoris = parsed; else aksesoris = [spesifikasi.aksesoris.trim()]; }
							catch(e){ aksesoris = [spesifikasi.aksesoris.trim()]; }
						}
					}
					
					let aksesorisCheckboxes = '';
					if (Array.isArray(aksesoris) && aksesoris.length > 0) {
						aksesorisCheckboxes = aksesoris.map(item => 
							`<div class="form-check">
								<input class="form-check-input" type="checkbox" name="aksesoris_tersedia[]" value="${item}" id="aks_${item.replace(/\s+/g, '_')}">
								<label class="form-check-label" for="aks_${item.replace(/\s+/g, '_')}">${item}</label>
							</div>`
						).join('');
					} else {
						aksesorisCheckboxes = '<p class="text-muted">No accessories requested</p>';
					}
					
					// Generate unit form for the specific unit index
					const unitFormsHtml = generateUnitForm(unitIndex, totalUnits, true);
					
					// Check if this is editing existing unit (hanya true jika ada data existing di spk_unit_stages)
					// Cek apakah unit ini sudah pernah di-approve sebelumnya
					let isEditing = false;
					if (currentEditingUnitIndex !== null && currentEditingUnitIndex === unitIndex) {
						// Double check: apakah benar-benar ada data di spk_unit_stages untuk unit ini?
						isEditing = preparedUnits.length >= unitIndex;
					}
					
					const editIndicator = isEditing ? `<div class="alert alert-info"><i class="fas fa-edit me-2"></i>Editing Unit ${unitIndex}</div>` : '';
					
					container.innerHTML = `
						<hr>
						<div class="mb-3">
							<h6 class="text-primary">
								<i class="fas fa-truck me-2"></i>
								${isEditing ? 'Edit' : 'Preparation'} Unit ${unitIndex} of ${totalUnits}
							</h6>
							${editIndicator}
							${preparedUnits.length > 0 && !isEditing ? `<div class="alert alert-success"><i class="fas fa-check me-2"></i>${preparedUnits.length} units previously prepared</div>` : ''}
						</div>
						
						${unitFormsHtml}
						
						<div class="mb-3">
							<label class="form-label">Confirm Available Accessories</label>
							<div class="border p-3 rounded bg-light">
								${aksesorisCheckboxes}
							</div>
							<div class="form-text">Check the accessories that are available as requested by Marketing.</div>
						</div>
					`;
					
					// Setup unit search for the specific unit form
					setTimeout(() => {
						console.log('Setting up unit search for unitIndex:', unitIndex);
						setupUnitSearch(unitIndex);
						loadAreaOptions(''); // Pass empty suffix for current implementation
						
						// If editing, try to load existing data
						if (isEditing) {
							loadExistingUnitData(spkId, unitIndex, stage);
						}
					}, 200); // Increased timeout to ensure DOM is ready
				}
			});
			
		} else if (stage === 'fabrikasi') {
			container.innerHTML = `
				<hr>
				<div class="mb-3" id="fabrikasiAttachmentSection">
					<label class="form-label">Attachment Management</label>
					<div id="fabrikasiAttachmentContent">
						<div class="text-muted">Loading attachment options...</div>
					</div>
					<div class="form-text">Specifically for fabrication attachments. Data from inventory_attachment.</div>
				</div>
			`;
			
			// Setup enhanced attachment management
			setupFabrikasiAttachmentManagement();
			
			// If editing, load existing fabrikasi data
			if (currentEditingUnitIndex !== null) {
				setTimeout(() => {
					loadExistingUnitData(spkId, currentEditingUnitIndex, stage);
				}, 500); // Give time for setupFabrikasiAttachmentManagement to complete
			}
			
		} else if (stage === 'pdi') {
			container.innerHTML = `
				<hr>
				<div class="mb-3">
					<label class="form-label">PDI Notes <span class="text-danger">*</span></label>
					<textarea class="form-control" id="pdiCatatan" name="catatan" rows="4" required 
							  placeholder="Enter the results of the PDI check and confirm that everything matches the request..."></textarea>
					<div class="form-text">These notes will mark the SPK as ready for delivery (status READY).</div>
				</div>
			`;
			
		} else {
			// painting or default - no additional content
			container.innerHTML = '';
		}
	}
	
	// We now process one unit at a time
	
	function setupUnitSearch(unitIndex) {
		// For single unit processing, we don't need the complex suffix logic
		const suffix = '';
		setupIndividualUnitSearch(suffix, unitIndex);
	}
	
	// Check if unit already selected in other unit forms
	function isUnitAlreadySelected(unitId, currentSuffix) {
		const allUnitSelects = document.querySelectorAll('select[name="unit_id[]"]');
		for (let select of allUnitSelects) {
			// Skip current form
			if (select.id.includes(currentSuffix)) continue;
			
			// Check if this unit is selected elsewhere
			if (select.value === unitId) {
				return true;
			}
		}
		return false;
	}
	
	// Generic function to populate area dropdown with department headers
	function populateAreaDropdown(areaSelect, areas) {
		if (!areaSelect || !areas) return;
		
		areaSelect.innerHTML = '<option value="">- Select Area -</option>';
		
		// Group areas by area_type (CENTRAL vs BRANCH)
		const centralAreas = [];
		const branchAreas = [];
		
		areas.forEach(area => {
			if (area.area_type === 'CENTRAL') {
				centralAreas.push(area);
			} else {
				branchAreas.push(area);
			}
		});
		
		// Add Central section
		if (centralAreas.length > 0) {
			const centralHeader = document.createElement('option');
			centralHeader.disabled = true;
			centralHeader.textContent = '─── CENTRAL AREAS ───';
			centralHeader.style.fontWeight = 'bold';
			centralHeader.style.backgroundColor = '#f8f9fa';
			centralHeader.style.color = '#495057';
			areaSelect.appendChild(centralHeader);
			
			centralAreas.forEach(area => {
				const option = document.createElement('option');
				option.value = area.id;
				option.textContent = `${area.area_code} - ${area.area_name}`;
				option.style.paddingLeft = '20px';
				areaSelect.appendChild(option);
			});
		}
		
		// Add Branch section
		if (branchAreas.length > 0) {
			const branchHeader = document.createElement('option');
			branchHeader.disabled = true;
			branchHeader.textContent = '─── BRANCH AREAS ───';
			branchHeader.style.fontWeight = 'bold';
			branchHeader.style.backgroundColor = '#f8f9fa';
			branchHeader.style.color = '#495057';
			areaSelect.appendChild(branchHeader);
			
			branchAreas.forEach(area => {
				const option = document.createElement('option');
				option.value = area.id;
				option.textContent = `${area.area_code} - ${area.area_name}`;
				option.style.paddingLeft = '20px';
				areaSelect.appendChild(option);
			});
		}
	}

	// Load area options for SPK Service
	function loadAreaOptions(suffix = '') {
		console.log('Loading areas for element:', `approvalAreaPick${suffix}`);
		fetch('<?= base_url('service/areas') ?>')
			.then(response => {
				console.log('Areas fetch response status:', response.status);
				console.log('Areas fetch response headers:', response.headers);
				return response.json();
			})
			.then(data => {
				console.log('🏢 Areas response received:', data);
				console.log('📊 Areas filtering applied by user scope - Count:', data.data?.length || 0);
				if (data.success && data.data) {
					// Log area types for filtering verification
					const areaTypes = [...new Set(data.data.map(area => area.area_type))];
					console.log('🎯 Available area types after filtering:', areaTypes);
					
					const areaSelect = document.getElementById(`approvalAreaPick${suffix}`);
					if (areaSelect) {
						console.log('✅ Populating area dropdown with', data.data.length, 'filtered areas');
						populateAreaDropdown(areaSelect, data.data);
						console.log('✅ Area dropdown populated successfully');
					} else {
						console.error('❌ Area select element not found:', `approvalAreaPick${suffix}`);
						// Let's also check what elements are actually available
						console.log('Available elements with "area" in ID:', 
							Array.from(document.querySelectorAll('[id*="area" i]')).map(el => el.id)
						);
					}
				} else {
					console.error('❌ Areas API response error - success:', data.success, 'data:', data.data);
					console.error('Full response object:', data);
				}
			})
			.catch(error => {
				console.error('Error loading areas (network/parse error):', error);
			});
	}
	
	function setupIndividualUnitSearch(suffix, unitIndex) {
		console.log('Setting up unit search with suffix:', suffix, 'unitIndex:', unitIndex);
		const searchBoxId = `approvalUnitSearch${suffix}`;
		const unitPickId = `approvalUnitPick${suffix}`;
		const searchBox = document.getElementById(searchBoxId);
		const unitPick = document.getElementById(unitPickId);
		
		console.log('Looking for elements:', searchBoxId, unitPickId);
		console.log('searchBox found:', !!searchBox, 'unitPick found:', !!unitPick);
		
		// Debug: List all elements with similar IDs
		const allElements = document.querySelectorAll('[id*="approvalUnit"]');
		console.log('All approvalUnit elements found:', Array.from(allElements).map(el => el.id));
		
		if (searchBox && unitPick) {
			// Remove existing event listeners first to prevent duplicates
			console.log('Cloning unitPick to remove old event listeners...');
			const newUnitPick = unitPick.cloneNode(true);
			unitPick.parentNode.replaceChild(newUnitPick, unitPick);
			console.log('Event listener cleaned and replaced for unitPick');
			
			console.log('Loading initial units...');
			// First, get SPK department to filter units accordingly
			if (!currentApprovalSpkId) {
				console.warn('⚠️ No SPK ID available for department filtering');
				// Load units without department filtering
				loadUnitsWithoutDepartmentFilter();
				return;
			}
			
			const spkDepartmentUrl = `<?= base_url('service/spk-department/') ?>${currentApprovalSpkId}`;
			console.log('🔍 Fetching SPK department from:', spkDepartmentUrl);
			
			fetch(spkDepartmentUrl)
				.then(response => {
					if (!response.ok) {
						throw new Error(`HTTP ${response.status}: ${response.statusText}`);
					}
					return response.json();
				})
				.then(deptData => {
					console.log('🎯 SPK Department Info:', deptData);
					
					// Load initial units with department filtering
					const url = new URL('<?= base_url('service/data-unit/simple') ?>', window.location.origin);
					if (currentApprovalSpkId) {
						url.searchParams.set('exclude_spk_id', currentApprovalSpkId);
					}
					
					// Add SPK department filter for precise unit matching
					if (deptData.success && deptData.department) {
						url.searchParams.set('spk_department', deptData.department);
						console.log(`🔍 Filtering units to match SPK department: ${deptData.department}`);
					} else {
						console.warn('⚠️ SPK department not found, loading all units');
					}
					
					return fetch(url);
				})
				.catch(error => {
					console.error('❌ Error fetching SPK department:', error);
					console.log('🔄 Falling back to load units without department filtering');
					// Load units without department filtering as fallback
					loadUnitsWithoutDepartmentFilter();
					return null; // Return null to skip the next then block
				})
				.then(r=>{
					// Skip this block if we got null from catch block
					if (r === null) return null;
					
					return r.json();
				})
				.then(j=>{
					// Skip this block if we got null or undefined from previous then
					if (j === null || j === undefined) return;
					
					console.log('🚗 Units loaded from API:', j.data?.length || 0, 'units');
					
					// Log department filtering info for debugging
					if (j.data && j.data.length > 0) {
						const departments = [...new Set(j.data.map(unit => unit.departemen_name).filter(Boolean))];
						console.log('🎯 Departments represented in filtered units:', departments);
					}
					
					// Process and display the units
					processUnitsDisplay(j, suffix);
				})
				.catch(error => {
					console.error('❌ Error loading units:', error);
					processUnitsDisplay({data: []}, suffix);
				});
				
			// Function to load units without department filtering (fallback)
			function loadUnitsWithoutDepartmentFilter() {
				console.log('🔄 Loading units without department filtering...');
				const url = new URL('<?= base_url('service/data-unit/simple') ?>', window.location.origin);
				if (currentApprovalSpkId) {
					url.searchParams.set('exclude_spk_id', currentApprovalSpkId);
				}
				
				fetch(url)
					.then(r => r.json())
					.then(j => {
						console.log('🚗 Units loaded from API (no dept filter):', j.data?.length || 0, 'units');
						
						// Show departments for debugging
						if (j.data && j.data.length > 0) {
							const departments = [...new Set(j.data.map(unit => unit.departemen_name).filter(Boolean))];
							console.log('🎯 All departments in units:', departments);
						}
						
						processUnitsDisplay(j, suffix);
					})
					.catch(error => {
						console.error('❌ Error loading units (fallback):', error);
						processUnitsDisplay({data: []}, suffix);
					});
			}
			
			// Function to process and display units
			function processUnitsDisplay(j, suffix) {
				
				// Use fresh reference after cloning
				const currentUnitPick = document.getElementById(`approvalUnitPick${suffix}`);
				if (currentUnitPick) {
					// Generate options with disabled attribute for already assigned units
					const options = (j.data||[]).map(x => {
						const isAssigned = x.is_assigned_in_spk;
						const disabled = isAssigned ? 'disabled' : '';
						const assignedText = isAssigned ? ' (Already used in this SPK)' : '';
						const serialInfo = x.serial_info || 'SN: -';
						return `<option value="${x.id}" ${disabled} data-no-unit="${x.no_unit||''}" data-needs-no-unit="${x.needs_no_unit||false}" data-status-unit="${x.status_unit_id||''}" data-departemen-id="${x.departemen_id||''}" data-departemen="${x.departemen_name||''}" style="white-space: normal; line-height: 1.4; padding: 8px;">
							${x.label}${assignedText}
							${serialInfo}
						</option>`;
					}).join('');
					currentUnitPick.innerHTML = '<option value="">- Select Unit -</option>' + options;
				}
			};
			
			// Areas are loaded centrally via loadAreaOptions() call
			
			searchBox.addEventListener('input', function(){
				const q = this.value.trim();
				
				// Get SPK department again for search filtering
				if (!currentApprovalSpkId) {
					console.warn('⚠️ No SPK ID for search filtering');
					// Search without department filtering
					searchUnitsWithoutDepartmentFilter(q);
					return;
				}
				
				const spkDepartmentUrl = `<?= base_url('service/spk-department/') ?>${currentApprovalSpkId}`;
				
				fetch(spkDepartmentUrl)
					.then(response => {
						if (!response.ok) {
							throw new Error(`HTTP ${response.status}: ${response.statusText}`);
						}
						return response.json();
					})
					.then(deptData => {
						const url = new URL('<?= base_url('service/data-unit/simple') ?>', window.location.origin);
						if (q) url.searchParams.set('q', q);
						if (currentApprovalSpkId) {
							url.searchParams.set('exclude_spk_id', currentApprovalSpkId);
						}
						
						// Apply SPK department filter for search as well
						if (deptData.success && deptData.department) {
							url.searchParams.set('spk_department', deptData.department);
							console.log(`🔍 Search filtering for department: ${deptData.department}`);
						} else {
							console.warn('⚠️ SPK department not found for search');
						}
						
						return fetch(url);
					})
					.catch(error => {
						console.error('❌ Error fetching SPK department for search:', error);
						console.log('🔄 Falling back to search without department filtering');
						// Search without department filtering as fallback
						searchUnitsWithoutDepartmentFilter(q);
						return null; // Return null to skip the next then block
					})
					.then(r => {
						// Skip this block if we got null from catch block
						if (r === null) return null;
						
						return r.json();
					})
					.then(j => {
						// Skip this block if we got null or undefined from previous then
						if (j === null || j === undefined) return;
						
						processUnitsDisplay(j, suffix);
					})
					.catch(error => {
						console.error('❌ Error searching units:', error);
						processUnitsDisplay({data: []}, suffix);
					});
				
				// Function to search units without department filtering (fallback)
				function searchUnitsWithoutDepartmentFilter(query) {
					const url = new URL('<?= base_url('service/data-unit/simple') ?>', window.location.origin);
					if (query) url.searchParams.set('q', query);
					if (currentApprovalSpkId) {
						url.searchParams.set('exclude_spk_id', currentApprovalSpkId);
					}
					
					fetch(url)
						.then(r => r.json())
						.then(j => {
							console.log(`🔍 Search results (no dept filter): ${j.data?.length || 0} units`);
							processUnitsDisplay(j, suffix);
						})
						.catch(error => {
							console.error('❌ Error searching units (fallback):', error);
							processUnitsDisplay({data: []}, suffix);
						});
				}
			});
			
			// Add change event listener to fresh element
			const freshUnitPick = document.getElementById(`approvalUnitPick${suffix}`);
			if (freshUnitPick) {
				freshUnitPick.addEventListener('change', function(){
				const selectedOption = this.options[this.selectedIndex];
				if (selectedOption && selectedOption.value) {
					// Since we process one unit at a time, no need to check for duplicates
					
					const noUnit = selectedOption.getAttribute('data-no-unit');
					const needsNoUnit = selectedOption.getAttribute('data-needs-no-unit');
					const statusUnit = selectedOption.getAttribute('data-status-unit');
					const departemenId = selectedOption.getAttribute('data-departemen-id');
					const departemenName = selectedOption.getAttribute('data-departemen');
					
					// Get unit component data untuk check existing attachments/battery/charger
					const unitId = selectedOption.value;
					
					// Reset noUnitProcessed flag when changing to a different unit
					// This allows the modal to show again if user selects a different unit
					if (window.lastSelectedUnitId && window.lastSelectedUnitId !== unitId) {
						console.log('Unit changed, resetting noUnitProcessed flags');
						window.noUnitProcessed = {};
					}
					window.lastSelectedUnitId = unitId;
					
					// Universal component detection - check for any unit that might have components
					console.log(`Checking unit ${unitId} for existing components...`);
					console.log(`Department: ${departemenId} (${departemenName})`);
					
					// Check if Electric department (id=2) - needs battery/charger
					const isElectric = departemenId === '2';
					const isGasoline = departemenName === 'GASOLINE';
					const isDiesel = departemenName === 'DIESEL';
					const isNonElectric = isGasoline || isDiesel;
					
					console.log(`DEBUG: Unit ${unitId} - Department: ${departemenName} (ID: ${departemenId})`);
					console.log(`DEBUG: isElectric: ${isElectric}, isGasoline: ${isGasoline}, isDiesel: ${isDiesel}, isNonElectric: ${isNonElectric}`);
					
					const electricFields = document.getElementById(`electricFields${suffix}`);
					
					// Fabrikasi (attachment) berlaku untuk SEMUA unit, bukan hanya departemen tertentu
					const fabrikasiFields = document.getElementById(`fabrikasiFields${suffix}`);
					
					// Smart detection: Try to fetch unit data regardless of department
					// Some units might have components even if not in typical departments
					
					// Add guard to prevent duplicate processing
					if (this.dataset.processing === 'true') {
						console.log('Unit selection already being processed, skipping...');
						return;
					}
					this.dataset.processing = 'true';
					
					fetchUnitComponentData(unitId).then(apiData => {
						console.log('API Response for unit', unitId, ':', apiData);
						
						if (apiData && apiData.success) {
							const hasBattery = apiData.battery !== null;
							const hasCharger = apiData.charger !== null;
							const hasAttachment = apiData.attachment !== null;
							
							console.log(`Unit ${unitId} component status: battery=${hasBattery}, charger=${hasCharger}, attachment=${hasAttachment}`);
							
							// Handle Electric components (battery/charger) - smart detection
							// DEPARTMENTAL RULES: GASOLINE dan DIESEL tidak boleh punya battery/charger
							if (!isNonElectric && (isElectric || hasBattery || hasCharger)) {
								if (electricFields) {
									// Check if component UI already exists for this unit
									const existingRenderKey = `component-ui-${unitId}-${suffix}`;
									if (electricFields.querySelector(`[data-render-key="${existingRenderKey}"]`)) {
										console.log('Component UI already rendered for unit', unitId, ', skipping duplicate render');
										return;
									}
									
									// PERBAIKAN: Untuk persiapan unit, hanya tampilkan battery dan charger
									// Untuk unit electric, selalu tampilkan battery dan charger (baik yang sudah ada maupun yang perlu ditambah)
									const componentOptions = {
										battery: isElectric || hasBattery,  // Tampilkan jika electric department ATAU unit sudah punya battery
										charger: isElectric || hasCharger,  // Tampilkan jika electric department ATAU unit sudah punya charger
										attachment: false  // PERBAIKAN: Attachment tidak ditampilkan di Persiapan Unit, hanya di Fabrikasi
									};
									
									console.log('Component options for unit', unitId, ':', componentOptions);
		 							electricFields.innerHTML = '';
		 							
		 							// Check if already rendered to prevent duplicates
		 							const renderKey = `component-ui-${unitId}-${suffix}`;
		 							if (!electricFields.querySelector(`[data-render-key="${renderKey}"]`)) {
		 								const componentUI = generateComponentSelectionUI(apiData, componentOptions, unitId, suffix, 'component');
		 								electricFields.innerHTML = componentUI;
		 							}
		 							electricFields.style.display = 'block';
								}
							} else if (isNonElectric && (hasBattery || hasCharger)) {
								// GASOLINE/DIESEL unit yang memiliki battery/charger - auto-detach
								if (electricFields) {
									electricFields.innerHTML = `
										<div class="alert alert-warning">
											<i class="fas fa-exclamation-triangle me-2"></i>Unit ${departemenName} does not require a battery and charger.
											<br>The battery and charger installed will be automatically detached from this unit.
										</div>
									`;
									electricFields.style.display = 'block';
								}
							} else if (electricFields) {
								electricFields.style.display = 'none';
							}
							
							// PERBAIKAN: Handle Attachment hanya untuk stage FABRIKASI, bukan Persiapan Unit
							// Di Persiapan Unit, attachment tidak perlu ditampilkan
							if (currentApprovalStage === 'fabrication' || currentApprovalStage === 'fabrikasi') {
								// Handle Attachment untuk SEMUA unit (bukan hanya fabrikasi departments)
								if (fabrikasiFields) {
									if (hasAttachment) {
										// Unit sudah memiliki attachment - berikan pilihan keep existing atau replace
										// Only render attachment UI if component UI hasn't already been rendered
										const existingComponentUI = electricFields?.querySelector('[data-render-key*="component-ui"]');
										if (!existingComponentUI) {
											const attachmentUI = generateComponentSelectionUI(apiData, { battery: false, charger: false, attachment: true }, unitId, suffix, 'attachment');
											fabrikasiFields.innerHTML = attachmentUI;
										} else {
											// Component UI already rendered, just show attachment-only message
											fabrikasiFields.innerHTML = `
												<div class="alert alert-info">
													<i class="fas fa-tools me-2"></i>Components and attachments for this unit have already been displayed above.
												</div>
											`;
										}
									} else {
										// Unit belum memiliki attachment - tampilkan pilihan attachment baru
										fabrikasiFields.innerHTML = `
											<div class="alert alert-info">
												<i class="fas fa-tools me-2"></i>This unit does not have an Attachment. Please select an attachment to install.
											</div>
											<div class="row">
												<div class="col-md-12">
													<label class="form-label">Select Attachment</label>
													<select class="form-select" id="attachmentPick${suffix}" name="attachment_id">
														<option value="">- Select Attachment -</option>
													</select>
												</div>
											</div>
										`;
										loadAttachmentOptionsIndividual(suffix);
									}
									fabrikasiFields.style.display = 'block';
								}
							} else {
								// Jika bukan stage fabrikasi, sembunyikan fabrikasi fields
								if (fabrikasiFields) {
									fabrikasiFields.style.display = 'none';
								}
							}
						} else {
							console.log('No existing components found for unit', unitId);
							
							// Standard department-based logic when no existing components
							if (isElectric && electricFields) {
								electricFields.style.display = 'block';
								console.log(`🔌 Loading electric options with suffix: ${suffix}`);
								// Load basic dropdown options
								loadBatteryOptionsIndividual(suffix);
								loadChargerOptionsIndividual(suffix);
							} else if (electricFields) {
								electricFields.style.display = 'none';
							}
							
							// PERBAIKAN: Fabrikasi hanya berlaku untuk stage FABRIKASI
							if (currentApprovalStage === 'fabrikasi' && fabrikasiFields) {
								fabrikasiFields.innerHTML = `
									<div class="alert alert-info">
										<i class="fas fa-tools me-2"></i>Select the attachment to be installed on this unit.
									</div>
									<div class="row">
										<div class="col-md-12">
											<label class="form-label">Select Attachment</label>
											<select class="form-select" id="attachmentPick${suffix}" name="attachment_id">
												<option value="">- Select Attachment -</option>
											</select>
										</div>
									</div>
								`;
								fabrikasiFields.style.display = 'block';
								loadAttachmentOptionsIndividual(suffix);
							} else if (fabrikasiFields) {
								// Jika bukan stage fabrikasi, sembunyikan fabrikasi fields
								fabrikasiFields.style.display = 'none';
							}
						}
					}).catch(error => {
						console.error('Component detection failed:', error);
						
						// Standard fallback when API fails
						if (isElectric && electricFields) {
							electricFields.style.display = 'block';
							// Load basic dropdown options
							loadBatteryOptionsIndividual(suffix);
							loadChargerOptionsIndividual(suffix);
						} else if (electricFields) {
							electricFields.style.display = 'none';
						}
						
						// PERBAIKAN: Fabrikasi fallback hanya untuk stage FABRIKASI
						if (currentApprovalStage === 'fabrikasi' && fabrikasiFields) {
							fabrikasiFields.innerHTML = `
								<div class="alert alert-info">
									<i class="fas fa-tools me-2"></i>Select the attachment to be installed on this unit.
								</div>
								<div class="row">
									<div class="col-md-12">
										<label class="form-label">Select Attachment</label>
										<select class="form-select" id="attachmentPick${suffix}" name="attachment_id">
											<option value="">- Select Attachment -</option>
										</select>
									</div>
								</div>
							`;
							fabrikasiFields.style.display = 'block';
							loadAttachmentOptionsIndividual(suffix);
						} else if (fabrikasiFields) {
							// Jika bukan stage fabrikasi, sembunyikan fabrikasi fields
							fabrikasiFields.style.display = 'none';
						}
					}).finally(() => {
						// Reset processing flag
						this.dataset.processing = 'false';
						
						// Apply departmental rules after UI is generated
						const unitData = {
							unit_id: unitId,
							departement_name: departemenName
						};
						applyDepartmentalRulesAfterUIGeneration(unitData, suffix);
					});
					
					// Note: Fabrikasi component management is now handled in universal detection above
					
				// Show no_unit confirmation for ANY unit that doesn't have valid no_unit
				// Check both status 1 (ASET) and status 2 (NON_ASET)
				if ((statusUnit === '1' || statusUnit === '2') && (needsNoUnit === 'true' || !noUnit || noUnit === '' || noUnit === '0')) {
					// Check if this unit has already been processed
					window.noUnitProcessed = window.noUnitProcessed || {};
					if (!window.noUnitProcessed[selectedOption.value]) {
						// Unit belum memiliki valid no_unit, show confirmation
						showNoUnitConfirmation(selectedOption.value, selectedOption.text, statusUnit);
					} else {
						console.log('Unit No Unit already processed for unit:', selectedOption.value);
					}
				} else {
					console.log('Unit already has no_unit or status not eligible:', selectedOption.text, 'Status:', statusUnit, 'No Unit:', noUnit);
				}
				}
				});
			}
		}
	}
	
	function setupUnitSearchWithElectric() {
		const searchBox = document.getElementById('approvalUnitSearch');
		if (searchBox) {
			// Load initial units
			const url = new URL('<?= base_url('service/data-unit/simple') ?>', window.location.origin);
			fetch(url).then(r=>r.json()).then(j=>{
				const sel = document.getElementById('approvalUnitPick');
				if (sel) {
					sel.innerHTML = '<option value="">- Select Unit -</option>' + (j.data||[]).map(x=>{
						const serialInfo = x.serial_info || 'SN: -';
						return `<option value="${x.id}" data-no-unit="${x.no_unit||''}" data-needs-no-unit="${x.needs_no_unit||false}" data-status-unit="${x.status_unit_id||''}" data-departemen-id="${x.departemen_id||''}" data-departemen="${x.departemen_name||''}" style="white-space: normal; line-height: 1.4; padding: 8px;">
							${x.label}
							${serialInfo}
						</option>`;
					}).join('');
				}
			});
			
			searchBox.addEventListener('input', function(){
				const q = this.value.trim();
				const url = new URL('<?= base_url('service/data-unit/simple') ?>', window.location.origin);
				if (q) url.searchParams.set('q', q);
				fetch(url).then(r=>r.json()).then(j=>{
					const sel = document.getElementById('approvalUnitPick');
					if (sel) {
						sel.innerHTML = '<option value="">- Select Unit -</option>' + (j.data||[]).map(x=>{
						const serialInfo = x.serial_info || 'SN: -';
						return `<option value="${x.id}" data-no-unit="${x.no_unit||''}" data-needs-no-unit="${x.needs_no_unit||false}" data-status-unit="${x.status_unit_id||''}" data-departemen-id="${x.departemen_id||''}" data-departemen="${x.departemen_name||''}" style="white-space: normal; line-height: 1.4; padding: 8px;">
							${x.label}
							${serialInfo}
						</option>`;
					}).join('');
					}
				});
			});
			
		}
	}
	
	function loadElectricOptions() {
		// Load available batteries (inventory_attachment with baterai_id)
		fetch('<?= base_url('warehouse/inventory/available-batteries') ?>')
			.then(r => r.json())
			.then(data => {
				const batterySelect = document.getElementById('batteryPick');
				if (batterySelect && Array.isArray(data)) {
					batterySelect.innerHTML = '<option value="">- Select Battery -</option>' + 
						data.map(item => {
							const name = `${item.merk_baterai||'-'} ${item.tipe_baterai||''} ${item.jenis_baterai||''}`.trim();
							const serialInfo = item.sn_baterai || 'SN: -';
							return `<option value="${item.id_inventory_attachment}" style="white-space: normal; line-height: 1.4; padding: 8px;">
								${name}
								${serialInfo}
							</option>`;
						}).join('');
				}
			})
			.catch(err => console.log('Error loading batteries:', err));
			
		// Load available chargers
		fetch('<?= base_url('warehouse/inventory/available-chargers') ?>')
			.then(r => r.json())
			.then(data => {
				const chargerSelect = document.getElementById('chargerPick');
				if (chargerSelect && Array.isArray(data)) {
					chargerSelect.innerHTML = '<option value="">- Select Charger -</option>' + 
						data.map(item => {
							const name = `${item.merk_charger||'-'} ${item.tipe_charger||''}`.trim();
							return `<option value="${item.id_inventory_attachment}">${name} • SN: ${item.sn_charger||'-'}</option>`;
						}).join('');
				}
			})
			.catch(err => console.log('Error loading chargers:', err));
	}
	
	function loadElectricOptionsForAvailableSlots(unitData, suffix = '') {
		// Load options hanya untuk slots yang perlu assignment (null values)
		if (!unitData.model_baterai_id) {
			loadBatteryOptionsIndividual(suffix);
		}
		if (!unitData.model_charger_id) {
			loadChargerOptionsIndividual(suffix);
		}
		if (!unitData.model_attachment_id) {
			loadAttachmentOptionsIndividual(suffix);
		}
	}
	
	function loadBatteryOptionsIndividual(suffix = '') {
		const batteryPickId = suffix ? `batteryPick${suffix}` : 'batteryPick';
		fetch('<?= base_url('warehouse/inventory/available-batteries') ?>')
			.then(r => r.json())
			.then(data => {
				const batterySelect = document.getElementById(batteryPickId);
				if (batterySelect && Array.isArray(data)) {
					batterySelect.innerHTML = '<option value="">- Select Battery -</option>' + 
						data.map(item => {
							const name = `${item.merk_baterai||'-'} ${item.tipe_baterai||''} ${item.jenis_baterai||''}`.trim();
							const serialInfo = item.sn_baterai || 'SN: -';
							const isUsed = item.attachment_status === 'USED';
							const installedUnit = isUsed && item.installed_unit_no ? `Unit ${item.installed_unit_no}` : '';
							const statusLabel = isUsed ? ` [USED - ${installedUnit}]` : ' [AVAILABLE]';
							
							return `<option value="${item.id_inventory_attachment}" 
									data-status="${item.attachment_status}" 
									data-installed-unit="${item.installed_unit_no||''}"
									data-installed-sn="${item.installed_unit_sn||''}"
									data-installed-merk="${item.installed_unit_merk||''}"
									data-installed-model="${item.installed_unit_model||''}"
									style="white-space: normal; line-height: 1.4; padding: 8px;" 
									class="${isUsed ? 'used-unit-option' : 'available-unit-option'}">
								${name} • ${serialInfo}${statusLabel}
							</option>`;
						}).join('');
					
					// Add change event listener to check for USED items
					batterySelect.addEventListener('change', function() {
						const selectedOption = this.options[this.selectedIndex];
						if (selectedOption && selectedOption.dataset.status === 'USED') {
							showKanibalAlert(selectedOption, 'Battery', suffix);
						}
					});
					
					// Update availability indicators after loading options
					setTimeout(() => {
						updateDropdownAvailability(batterySelect, 'battery');
					}, 100);
				}
			})
			.catch(err => console.log('Error loading batteries:', err));
	}
	
	// Make sure functions are accessible globally
	window.loadBatteryOptionsIndividual = loadBatteryOptionsIndividual;
	
	function loadChargerOptionsIndividual(suffix = '') {
		const chargerPickId = suffix ? `chargerPick${suffix}` : 'chargerPick';
		console.log(`🔌 Loading charger options for ID: ${chargerPickId}`);
		
		fetch('<?= base_url('warehouse/inventory/available-chargers') ?>')
			.then(r => r.json())
			.then(data => {
				console.log(`🔌 Charger API response:`, data);
				const chargerSelect = document.getElementById(chargerPickId);
				console.log(`🔌 Charger select element:`, chargerSelect);
				
				if (chargerSelect && Array.isArray(data)) {
					const options = '<option value="">- Select Charger -</option>' + 
						data.map(item => {
							const name = `${item.merk_charger||'-'} ${item.tipe_charger||''}`.trim();
							const serialInfo = item.sn_charger || 'SN: -';
							const isUsed = item.attachment_status === 'USED';
							const installedUnit = isUsed && item.installed_unit_no ? `Unit ${item.installed_unit_no}` : '';
							const statusLabel = isUsed ? ` [USED - ${installedUnit}]` : ' [AVAILABLE]';
							
							return `<option value="${item.id_inventory_attachment}" 
									data-status="${item.attachment_status}" 
									data-installed-unit="${item.installed_unit_no||''}"
									data-installed-sn="${item.installed_unit_sn||''}"
									data-installed-merk="${item.installed_unit_merk||''}"
									data-installed-model="${item.installed_unit_model||''}"
									style="white-space: normal; line-height: 1.4; padding: 8px;" 
									class="${isUsed ? 'used-unit-option' : 'available-unit-option'}">
								${name} • ${serialInfo}${statusLabel}
							</option>`;
						}).join('');
					
					chargerSelect.innerHTML = options;
					console.log(`🔌 Charger options loaded: ${data.length} options`);
					
					// Add change event listener to check for USED items
					chargerSelect.addEventListener('change', function() {
						const selectedOption = this.options[this.selectedIndex];
						if (selectedOption && selectedOption.dataset.status === 'USED') {
							showKanibalAlert(selectedOption, 'Charger', suffix);
						}
					});
					
					// Update availability indicators after loading options
					setTimeout(() => {
						updateDropdownAvailability(chargerSelect, 'charger');
					}, 100);
				} else {
					console.log(`🔌 Charger select not found or invalid data:`, {chargerSelect, data});
				}
			})
			.catch(err => console.log('🔌 Error loading chargers:', err));
	}
	
	// Make sure functions are accessible globally
	window.loadChargerOptionsIndividual = loadChargerOptionsIndividual;
	
	// Show kanibal alert for USED items
	function showKanibalAlert(selectedOption, itemType, suffix = '') {
		const installedUnitNo = selectedOption.dataset.installedUnit;
		const installedSN = selectedOption.dataset.installedSn;
		const installedMerk = selectedOption.dataset.installedMerk;
		const installedModel = selectedOption.dataset.installedModel;
		
		Swal.fire({
			icon: 'warning',
			title: `${itemType} In Use`,
			html: `<div class="text-start">
				<p class="mb-2"><strong>Attention!</strong> The selected ${itemType} is currently installed on another unit:</p>
				<div class="alert alert-warning mb-3">
					<strong>Current Unit:</strong><br>
					<i class="fas fa-forklift me-2"></i>Unit No: ${installedUnitNo || 'N/A'}<br>
					<i class="fas fa-barcode me-2"></i>Serial: ${installedSN || 'N/A'}<br>
					<i class="fas fa-cube me-2"></i>${installedMerk || ''} ${installedModel || ''}
				</div>
				<p class="mb-2"><strong>Options:</strong></p>
				<ul class="mb-0">
					<li><strong>YES</strong> - Move the ${itemType} from the old unit to the new unit (cannibalization process)</li>
					<li><strong>CANCEL</strong> - Cancel the selection of this ${itemType}</li>
				</ul>
			</div>`,
			showCancelButton: true,
			confirmButtonText: 'YES, MOVE IT',
			confirmButtonColor: '#ff9800',
			cancelButtonText: 'CANCEL',
			cancelButtonColor: '#6c757d',
			allowOutsideClick: false
		}).then((result) => {
			if (result.isConfirmed) {
				// User confirmed kanibal - keep selection
				console.log(`Kanibal confirmed for ${itemType}:`, selectedOption.value);
				// Add flag to indicate this is a kanibal operation
				selectedOption.dataset.kanibal = 'true';
			} else {
				// User cancelled - reset selection
				const selectId = itemType === 'Battery' ? `batteryPick${suffix}` : `chargerPick${suffix}`;
				const selectElement = document.getElementById(selectId);
				if (selectElement) {
					selectElement.value = '';
				}
			}
		});
	}
	
	// Make function globally accessible
	window.showKanibalAlert = showKanibalAlert;
	
	function loadAttachmentOptionsIndividual(suffix = '') {
		const attachmentPickId = suffix ? `attachmentPick${suffix}` : 'attachmentPick';
		fetch('<?= base_url('warehouse/inventory/available-attachments') ?>')
			.then(r => r.json())
			.then(data => {
				const attachmentSelect = document.getElementById(attachmentPickId);
				if (attachmentSelect && Array.isArray(data)) {
					attachmentSelect.innerHTML = '<option value="">- Select Attachment -</option>' + 
						data.map(item => {
							const serialInfo = item.sn_attachment || 'SN: -';
							return `<option value="${item.id_inventory_attachment}" style="white-space: normal; line-height: 1.4; padding: 8px;">
								${item.nama_barang}
								${serialInfo}
							</option>`;
						}).join('');
					
					// Update availability indicators after loading options
					setTimeout(() => {
						updateDropdownAvailability(attachmentSelect, 'attachment');
					}, 100);
				}
			})
			.catch(err => console.log('Error loading attachments:', err));
	}
	
	// Make sure attachment function is accessible globally
	window.loadAttachmentOptionsIndividual = loadAttachmentOptionsIndividual;
	
	function loadFabrikasiOptionsForAvailableSlots(unitData, suffix = '') {
		// Load options hanya untuk slots yang perlu assignment (null values)
		if (!unitData.model_attachment_id) {
			loadAttachmentOptionsIndividual(suffix);
		}
	}
	
	function loadFabrikasiOptions() {
		// Load all fabrikasi attachment options
		fetch('<?= base_url('warehouse/inventory/available-attachments') ?>')
			.then(r => r.json())
			.then(data => {
				const attachmentSelect = document.getElementById('attachmentPick');
				if (attachmentSelect && Array.isArray(data)) {
					attachmentSelect.innerHTML = '<option value="">- Select Attachment -</option>' + 
						data.map(item => {
							const serialInfo = item.sn_attachment || 'SN: -';
							return `<option value="${item.id_inventory_attachment}" style="white-space: normal; line-height: 1.4; padding: 8px;">
								${item.nama_barang}
								${serialInfo}
							</option>`;
						}).join('');
					
					// Update availability indicators after loading options
					setTimeout(() => {
						updateDropdownAvailability(attachmentSelect, 'attachment');
					}, 100);
				}
			})
			.catch(err => console.log('Error loading attachments:', err));
	}
	
	function showNoUnitConfirmation(unitId, unitLabel, statusUnit) {
		// Status 1 = ASET, Status 2 = STOCK_NON_ASET
		const isAset = statusUnit === '1';
		const isNonAset = statusUnit === '2';
		const unitType = isAset ? 'Aset' : (isNonAset ? 'Non Aset' : 'Unit');

		// Create custom Bootstrap modal instead of SweetAlert2
		const modalHtml = `
			<div class="modal fade" id="noUnitModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Unit ${unitType} No Unit Missing</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
						</div>
						<div class="modal-body">
							<div class="text-start">
								<p><strong>Unit:</strong> ${unitLabel}</p>
								<p class="text-warning mb-3">This unit does not have a Unit No (asset number).</p>
								
								<div class="mb-3">
									<div class="form-check mb-2">
										<input class="form-check-input" type="radio" name="noUnitAction" id="generateNoUnit" value="generate" checked>
										<label class="form-check-label" for="generateNoUnit">
											Generate Unit Number automatically during approval process
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="noUnitAction" id="manualNoUnit" value="manual">
										<label class="form-check-label" for="manualNoUnit">
											Enter Unit Number manually
										</label>
									</div>
								</div>
								
								<div id="manualInputContainer" style="display: none;">
									<label class="form-label">Manual Unit Number:</label>
									<input type="number" class="form-control" id="manualNoUnitInput" placeholder="Enter unit number" min="1">
									<div class="form-text">Enter the unit number to be used.</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
							<button type="button" class="btn btn-primary" id="confirmNoUnitBtn">Continue</button>
						</div>
					</div>
				</div>
			</div>
		`;

		// Remove existing modal if any
		const existingModal = document.getElementById('noUnitModal');
		if (existingModal) {
			existingModal.remove();
		}

		// Add modal to body
		document.body.insertAdjacentHTML('beforeend', modalHtml);

		// Show modal with higher z-index
		const modalElement = document.getElementById('noUnitModal');
		modalElement.style.zIndex = '1060'; // Higher than Bootstrap modal default (1055)
		
		const modal = new bootstrap.Modal(modalElement, {
			backdrop: 'static',
			keyboard: false
		});
		modal.show();

		// Ensure backdrop also has correct z-index
		setTimeout(() => {
			const backdrop = document.querySelector('.modal-backdrop:last-of-type');
			if (backdrop) {
				backdrop.style.zIndex = '1059';
			}
		}, 100);

		// Handle radio button change
		document.getElementById('generateNoUnit').addEventListener('change', function() {
			document.getElementById('manualInputContainer').style.display = 'none';
		});

		document.getElementById('manualNoUnit').addEventListener('change', function() {
			document.getElementById('manualInputContainer').style.display = 'block';
			setTimeout(() => {
				document.getElementById('manualNoUnitInput').focus();
			}, 100);
		});

		// Handle confirm button
		document.getElementById('confirmNoUnitBtn').addEventListener('click', function() {
			const action = document.querySelector('input[name="noUnitAction"]:checked').value;
			
			if (action === 'generate') {
				window.noUnitConfirmed = true; // Set flag that user confirmed
				var resultData = { action: 'generate', noUnit: 'AUTO_GENERATE' };
				handleNoUnitResult(resultData, unitId);
			} else {
				const manualNoUnit = document.getElementById('manualNoUnitInput').value;
				if (!manualNoUnit || manualNoUnit < 1) {
					alert('No Unit manual harus diisi dengan angka positif');
					return;
				}
				window.noUnitConfirmed = true; // Set flag that user confirmed
				handleNoUnitResult({ action: 'manual', noUnit: manualNoUnit }, unitId);
			}
			modal.hide();
		});

		// Handle modal close
		document.getElementById('noUnitModal').addEventListener('hidden.bs.modal', function() {
			// Only reset selection if modal was closed without confirmation (cancel button)
			// If confirmation was done, selection should remain
			if (!window.noUnitConfirmed) {
				document.getElementById('approvalUnitPick').value = '';
			}
			window.noUnitConfirmed = false; // Reset flag
			this.remove();
		});
	}

	function handleNoUnitResult(result, unitId) {
		const { action, noUnit } = result;
		
		// Set flag to prevent modal from showing again for this unit
		window.noUnitProcessed = window.noUnitProcessed || {};
		window.noUnitProcessed[unitId] = true;
		
				if (action === 'generate') {
					window.pendingNoUnitUpdate = { unitId, noUnit: 'AUTO_GENERATE' };
					console.log('Unit No Unit will be auto-generated during approval process');
					const unitPick = document.getElementById('approvalUnitPick');
					if (unitPick) {
				// Ensure unit is still selected
				unitPick.value = unitId;
						// Set data-no-unit attribute pada option yang dipilih
						const selectedOption = unitPick.options[unitPick.selectedIndex];
						if (selectedOption) {
							selectedOption.setAttribute('data-no-unit', noUnit);
							// Jika ada input hidden untuk no_unit, update juga
							let hiddenNoUnit = document.getElementById('hiddenApprovalNoUnit');
							if (!hiddenNoUnit) {
								hiddenNoUnit = document.createElement('input');
								hiddenNoUnit.type = 'hidden';
								hiddenNoUnit.id = 'hiddenApprovalNoUnit';
								hiddenNoUnit.name = 'approval_no_unit';
								unitPick.parentNode.appendChild(hiddenNoUnit);
							}
							hiddenNoUnit.value = noUnit;
						}
					}
					console.log('Unit No Unit will be set to:', noUnit);
		} else if (action === 'manual') {
			window.pendingNoUnitUpdate = { unitId, noUnit: parseInt(noUnit) };
			console.log('Unit No Unit will be set manually to:', noUnit);
			const unitPick = document.getElementById('approvalUnitPick');
			if (unitPick) {
				// Ensure unit is still selected
				unitPick.value = unitId;
				// Set data-no-unit attribute pada option yang dipilih
				const selectedOption = unitPick.options[unitPick.selectedIndex];
				if (selectedOption) {
					selectedOption.setAttribute('data-no-unit', noUnit);
					// Update hidden input untuk manual no unit
					let hiddenNoUnit = document.getElementById('hiddenApprovalNoUnit');
					if (!hiddenNoUnit) {
						hiddenNoUnit = document.createElement('input');
						hiddenNoUnit.type = 'hidden';
						hiddenNoUnit.id = 'hiddenApprovalNoUnit';
						hiddenNoUnit.name = 'approval_no_unit';
						unitPick.parentNode.appendChild(hiddenNoUnit);
					}
					hiddenNoUnit.value = noUnit;
				}
				// Trigger change event to update UI
				unitPick.dispatchEvent(new Event('change', { bubbles: true }));
			}
			console.log('Unit No Unit will be set manually to:', noUnit);
				} else {
					console.log('Continuing without No Unit (not recommended)');
				}
	}
	
	function updateUnitNoUnit(unitId, noUnit) {
		// This will be handled during the approval process
		// For now, we just store the intention
		window.pendingNoUnitUpdate = { unitId, noUnit };
		return Promise.resolve();
	}
	
	
	// Enhanced fabrikasi attachment management
	function setupFabrikasiAttachmentManagement() {
		if (!currentApprovalSpkId) return;
		
		// Get SPK details to find unit_id
		fetch(`<?= base_url('service/spk/detail') ?>/${currentApprovalSpkId}`)
			.then(response => response.json())
			.then(data => {
				if (data.success && data.data) {
					const spk = data.data;
					const unitId = spk.persiapan_unit_id || spk.unit_id;
					
					if (unitId) {
						// Get unit components including attachment
						fetch(`<?= base_url('warehouse/inventory/unit-components') ?>?unit_id=${unitId}`)
							.then(response => response.json())
							.then(componentData => {
								generateFabrikasiAttachmentUI(componentData.attachment, unitId);
							})
							.catch(error => {
								console.log('Error loading unit attachment:', error);
								generateFabrikasiAttachmentUI(null, unitId);
							});
					} else {
						generateFabrikasiAttachmentUI(null, null);
					}
				}
			})
			.catch(error => {
				console.log('Error loading SPK details:', error);
				generateFabrikasiAttachmentUI(null, null);
			});
	}
	
	// Generate attachment UI similar to battery/charger management
	function generateFabrikasiAttachmentUI(existingAttachment, unitId) {
		const contentDiv = document.getElementById('fabrikasiAttachmentContent');
		if (!contentDiv) return;
		
		if (existingAttachment) {
			// Unit has existing attachment
			const attachmentName = `${existingAttachment.tipe || '-'} ${existingAttachment.merk || ''} ${existingAttachment.model || ''}`.trim();
			const attachmentLabel = `${attachmentName} • SN: ${existingAttachment.sn_attachment || '-'}`;
			
			contentDiv.innerHTML = `
				<div class="alert alert-info mb-3">
					<div class="d-flex align-items-center">
						<i class="fas fa-info-circle me-2"></i>
						<div>
							<strong>Existing Attachment Detected:</strong><br>
							<small>${attachmentLabel}</small>
						</div>
					</div>
				</div>
				
				<div class="mb-3">
					<label class="form-label">Select Attachment Action:</label>
					<div class="form-check">
						<input class="form-check-input" type="radio" name="attachment_action" id="attachment_keep" value="keep_existing" checked>
						<label class="form-check-label" for="attachment_keep">
							<strong>Keep Existing</strong> - Use the existing attachment
						</label>
					</div>
					<div class="form-check">
						<input class="form-check-input" type="radio" name="attachment_action" id="attachment_replace" value="replace">
						<label class="form-check-label" for="attachment_replace">
							<strong>Replace</strong> - Replace with another attachment
						</label>
					</div>
				</div>
				
				<div id="attachment_replace_section" style="display: none;">
					<label class="form-label">Select Replacement Attachment:</label>
					<input type="text" class="form-control mb-2" id="fabrikasiAttSearch" placeholder="Search type/brand/model/SN/location" autocomplete="off">
					<select class="form-select" id="fabrikasiAttPick" name="new_attachment_id">
						<option value="">-- Select Attachment --</option>
					</select>
				</div>
				
				<!-- Hidden fields for form submission -->
				<input type="hidden" id="fabrikasi_attachment_existing_id" name="existing_attachment_id" value="${existingAttachment.id_inventory_attachment}">
				<input type="hidden" id="fabrikasi_attachment_action" name="attachment_action" value="keep_existing">
			`;
			
			// Setup event listeners
			setupFabrikasiAttachmentEventListeners();
			loadFabrikasiAttachmentOptions();
			
		} else {
			// Unit doesn't have existing attachment
			contentDiv.innerHTML = `
				<div class="alert alert-warning mb-3">
					<div class="d-flex align-items-center">
						<i class="fas fa-exclamation-triangle me-2"></i>
						<div>
							<strong>No Existing Attachment</strong><br>
							<small>This unit does not have an attachment. You can add a new attachment.</small>
						</div>
					</div>
				</div>
				
				<div class="mb-3">
					<label class="form-label">Select Attachment (Optional):</label>
					<input type="text" class="form-control mb-2" id="fabrikasiAttSearch" placeholder="Search type/brand/model/SN/location" autocomplete="off">
					<select class="form-select" id="fabrikasiAttPick" name="new_attachment_id">
						<option value="">-- Select Attachment (Optional) --</option>
					</select>
				</div>
				
				<!-- Hidden fields for form submission -->
				<input type="hidden" id="fabrikasi_attachment_action" name="attachment_action" value="none">
			`;
			
			// Setup event listeners
			setupFabrikasiAttachmentEventListeners();
			loadFabrikasiAttachmentOptions();
		}
	}
	
	// Setup event listeners for fabrikasi attachment management
	function setupFabrikasiAttachmentEventListeners() {
		// Radio button change handlers
		const keepRadio = document.getElementById('attachment_keep');
		const replaceRadio = document.getElementById('attachment_replace');
		const replaceSection = document.getElementById('attachment_replace_section');
		const actionInput = document.getElementById('fabrikasi_attachment_action');
		
		if (keepRadio && replaceRadio && replaceSection && actionInput) {
			keepRadio.addEventListener('change', function() {
				if (this.checked) {
					replaceSection.style.display = 'none';
					actionInput.value = 'keep_existing';
				}
			});
			
			replaceRadio.addEventListener('change', function() {
				if (this.checked) {
					replaceSection.style.display = 'block';
					actionInput.value = 'replace';
				}
			});
		}
		
		// Search input handler
		const searchInput = document.getElementById('fabrikasiAttSearch');
		if (searchInput) {
			searchInput.addEventListener('input', function() {
				const query = this.value.trim();
				loadFabrikasiAttachmentOptions(query);
			});
		}
	}
	
	// Load available attachments for fabrikasi
	function loadFabrikasiAttachmentOptions(query = '') {
		const url = new URL('<?= base_url('service/data-attachment/simple') ?>', window.location.origin);
		if (query) url.searchParams.set('q', query);
		
		fetch(url)
			.then(response => response.json())
			.then(data => {
				const select = document.getElementById('fabrikasiAttPick');
				if (select) {
					const options = (data.data || []).map(item => 
						`<option value="${item.id}" data-is-used="${item.is_used || false}" data-installed-unit='${JSON.stringify(item.installed_unit || null)}'>${item.label}</option>`
					).join('');
					select.innerHTML = '<option value="">-- Select Attachment --</option>' + options;
					
					// Add change event listener for used attachments (prevent duplicates)
					if (!select.hasAttribute('data-listener-attached')) {
						select.setAttribute('data-listener-attached', 'true');
						select.addEventListener('change', function() {
							const selectedOption = this.options[this.selectedIndex];
							console.log('🔧 Attachment selected:', selectedOption.value, 'isUsed:', selectedOption.dataset.isUsed);
							if (selectedOption && selectedOption.dataset.isUsed === 'true') {
								console.log('🔧 Showing used attachment alert');
								showUsedAttachmentAlert(selectedOption);
							}
						});
					}
				}
			})
			.catch(error => console.log('Error loading fabrikasi attachment options:', error));
	}
	
	// Show modal alert for used attachments
	function showUsedAttachmentAlert(option) {
		console.log('🔧 showUsedAttachmentAlert called with option:', option);
		
		// Prevent multiple modal creation
		if (window.usedAttachmentModalShowing) {
			console.log('⚠️ Modal already showing, skipping');
			return;
		}
		window.usedAttachmentModalShowing = true;
		
		const installedUnit = JSON.parse(option.dataset.installedUnit || 'null');
		console.log('🔧 installedUnit data:', installedUnit);
		if (!installedUnit) {
			console.log('❌ No installed unit data, skipping alert');
			window.usedAttachmentModalShowing = false;
			return;
		}
		
		const modalHtml = `
			<div class="modal fade" id="usedAttachmentModal" tabindex="-1" data-modal="kanibal">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header bg-warning">
							<h5 class="modal-title">
								<i class="fas fa-exclamation-triangle me-2"></i>
								Attachment Already In Use
							</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
						</div>
						<div class="modal-body">
							<div class="alert alert-warning">
								<strong>Warning!</strong> This attachment is already installed on another unit.
							</div>
							<div class="mb-3">
								<strong>Details of Installed Unit:</strong>
								<ul class="list-unstyled mt-2">
									<li><strong>Unit Number:</strong> ${installedUnit.no_unit || 'N/A'}</li>
									<li><strong>Serial Number:</strong> ${installedUnit.serial_number || 'N/A'}</li>
									<li><strong>Brand & Model:</strong> ${installedUnit.merk_unit || ''} ${installedUnit.model_unit || ''}</li>
								</ul>
							</div>
							<div class="mb-3">
								<strong>Options:</strong>
								<ul>
									<li><strong>YES</strong> - Transfer the attachment from the old unit to the new unit (cannibalization process)</li>
									<li><strong>NO</strong> - Cancel the selection of this attachment</li>
								</ul>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="cancelUsedAttachment()">
								<i class="fas fa-times me-1"></i>NO, CANCEL
							</button>
							<button type="button" class="btn btn-warning" onclick="confirmUsedAttachment()">
								<i class="fas fa-exchange-alt me-1"></i>YES, TRANSFER
							</button>
						</div>
					</div>
				</div>
			</div>
		`;
		
		// Remove existing modal and backdrop if any
		const existingModal = document.getElementById('usedAttachmentModal');
		if (existingModal) {
			// Try to close properly first
			const modal = bootstrap.Modal.getInstance(existingModal);
			if (modal) {
				modal.hide();
			}
			existingModal.remove();
			console.log('🔧 Removed existing modal');
		}
		
		// Don't clean up backdrops here - let Bootstrap handle it
		// The approval modal backdrop should remain intact
		
		// Add modal to body
		document.body.insertAdjacentHTML('beforeend', modalHtml);
		console.log('🔧 Modal HTML added to body');
		
		// Show modal
		const modal = new bootstrap.Modal(document.getElementById('usedAttachmentModal'));
		modal.show();
		console.log('🔧 Modal shown');
		
		// Don't modify backdrops - let Bootstrap handle them naturally
	}
	
	// Cancel used attachment selection
	function cancelUsedAttachment() {
		console.log('❌ cancelUsedAttachment called');
		
		const select = document.getElementById('fabrikasiAttPick');
		if (select) {
			select.value = '';
			select.dataset.transferAttachment = 'false';
			console.log('✅ Attachment selection cleared');
		} else {
			console.log('❌ fabrikasiAttPick not found');
		}
		
		// Remove transfer input
		const transferInput = document.getElementById('transfer_attachment_input');
		if (transferInput) {
			transferInput.value = 'false';
			console.log('✅ Transfer input cleared');
		}
		
		// Smooth modal close with proper Bootstrap handling
		const modalElement = document.getElementById('usedAttachmentModal');
		if (modalElement) {
			// Method 1: Try Bootstrap instance with smooth transition
			const modal = bootstrap.Modal.getInstance(modalElement);
			if (modal) {
				// Use Bootstrap's built-in hide method for smooth transition
				modal.hide();
				console.log('✅ Modal closed via Bootstrap instance');
				
				// Wait for Bootstrap transition to complete, then clean up
				setTimeout(() => {
					// Only remove if modal is still in DOM
					if (document.getElementById('usedAttachmentModal')) {
						modalElement.remove();
						console.log('✅ Modal removed after transition');
					}
				}, 300); // Bootstrap modal transition duration
			} else {
				// Fallback: smooth manual close
				modalElement.classList.remove('show');
				modalElement.style.display = 'none';
				modalElement.setAttribute('aria-hidden', 'true');
				modalElement.removeAttribute('aria-modal');
				
				// Don't remove any backdrops - let Bootstrap handle it
				setTimeout(() => {
					// Only remove the kanibal modal element, preserve all backdrops
					modalElement.remove();
					console.log('✅ Modal fallback cleanup completed');
				}, 200);
			}
		} else {
			console.log('❌ usedAttachmentModal not found');
		}
		
		// Show info notification
		notify('Pemilihan attachment dibatalkan', 'info');
		
		// Reset flags
		window.usedAttachmentModalShowing = false;
	}
	
	// Make function globally accessible
	window.cancelUsedAttachment = cancelUsedAttachment;
	
	// Confirm used attachment selection (transfer)
	function confirmUsedAttachment() {
		console.log('🔄 Kanibal: Confirming transfer');
		
		// Prevent multiple calls with immediate check
		if (window.confirmUsedAttachmentProcessing) {
			return;
		}
		
		// Set flag immediately
		window.confirmUsedAttachmentProcessing = true;
		
		// Disable button to prevent multiple clicks
		const confirmBtn = document.querySelector('#usedAttachmentModal .btn-warning');
		if (confirmBtn) {
			confirmBtn.disabled = true;
			confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
		}
		
		// Remove all event listeners from button to prevent multiple calls
		const newConfirmBtn = confirmBtn.cloneNode(true);
		confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
		
		// Set flag for backend to handle transfer
		const select = document.getElementById('fabrikasiAttPick');
		if (select) {
			select.dataset.transferAttachment = 'true';
		}
		
		// Also set hidden input for backend
		let transferInput = document.getElementById('transfer_attachment_input');
		if (!transferInput) {
			transferInput = document.createElement('input');
			transferInput.type = 'hidden';
			transferInput.id = 'transfer_attachment_input';
			transferInput.name = 'transfer_attachment';
			document.getElementById('approvalStageForm').appendChild(transferInput);
		}
		transferInput.value = 'true';
		
		// Smooth modal close with proper Bootstrap handling
		const modalElement = document.getElementById('usedAttachmentModal');
		if (modalElement) {
			// Method 1: Try Bootstrap instance with smooth transition
			const modal = bootstrap.Modal.getInstance(modalElement);
			if (modal) {
				// Use Bootstrap's built-in hide method for smooth transition
				modal.hide();
				console.log('✅ Modal closed via Bootstrap instance');
				
				// Wait for Bootstrap transition to complete, then clean up
				setTimeout(() => {
					// Only remove if modal is still in DOM
					if (document.getElementById('usedAttachmentModal')) {
						modalElement.remove();
						console.log('✅ Modal removed after transition');
					}
				}, 300); // Bootstrap modal transition duration
			} else {
				// Fallback: smooth manual close
				modalElement.classList.remove('show');
				modalElement.style.display = 'none';
				modalElement.setAttribute('aria-hidden', 'true');
				modalElement.removeAttribute('aria-modal');
				
				// Don't remove any backdrops - let Bootstrap handle it
				setTimeout(() => {
					// Only remove the kanibal modal element, preserve all backdrops
					modalElement.remove();
					console.log('✅ Modal fallback cleanup completed');
				}, 200);
			}
		} else {
			console.log('❌ usedAttachmentModal not found');
		}
		
		// No notification here - will show after approve & simpan
		
		// Reset processing flag after delay
		setTimeout(() => {
			window.confirmUsedAttachmentProcessing = false;
			window.usedAttachmentModalShowing = false;
		}, 1000);
	}
	
	// Make function globally accessible
	window.confirmUsedAttachment = confirmUsedAttachment;
	
		// Prevent duplicate event listeners
		if (!window.usedAttachmentModalInitialized) {
			window.usedAttachmentModalInitialized = true;
			console.log('🔧 Used attachment modal event listeners initialized');
			
			// Add CSS for smooth modal transitions
			const style = document.createElement('style');
			style.textContent = `
				.modal.fade {
					transition: opacity 0.3s ease-in-out;
				}
				.modal-backdrop.fade {
					transition: opacity 0.3s ease-in-out;
				}
				.modal.show {
					display: block !important;
				}
				/* Prevent double backdrop */
				body.modal-open {
					overflow: hidden;
				}
				body.modal-open .modal-backdrop {
					z-index: 1040;
				}
				body.modal-open .modal {
					z-index: 1050;
				}
				/* Kanibal modal should be on top of approval modal */
				.modal[data-modal="kanibal"] {
					z-index: 1060;
				}
			`;
			document.head.appendChild(style);
		}
	
	document.getElementById('approvalStageForm').addEventListener('submit', function(e){
		e.preventDefault();
		const fd = new FormData(this);
		fd.append('stage', currentApprovalStage);
		
		// Handle aksesoris checkbox for persiapan_unit
		if (currentApprovalStage === 'persiapan_unit') {
			const checkedAksesoris = [];
			document.querySelectorAll('input[name="aksesoris_tersedia[]"]:checked').forEach(checkbox => {
				checkedAksesoris.push(checkbox.value);
			});
			fd.append('aksesoris_tersedia', JSON.stringify(checkedAksesoris));
			
			// Get enhanced component data for the single unit
			const unitId = document.getElementById('approvalUnitPick')?.value;
			
			if (unitId) {
				const componentData = collectSingleUnitComponentData('', unitId);
				console.log('Component data collected:', componentData);
				fd.append('enhanced_component_data', JSON.stringify([componentData]));
			}
			
			// Verifikasi data unit yang dipilih
			const existingBattery = document.getElementById('existingBatteryModelId')?.value;
			const batteryAction = document.getElementById('batteryAction')?.value;
			const useExistingChecked = document.getElementById('useExistingBattery')?.checked;
			
			console.log('Form Submission Debug:', {
				unitId,
				existingBattery,
				batteryAction,
				useExistingChecked,
				currentApprovalStage
			});
			
			// Handle Enhanced Component Management untuk Multiple Units
			const enhancedComponentData = collectEnhancedComponentDataMultiUnit();
			if (enhancedComponentData && enhancedComponentData.length > 0) {
				// Ambil data unit pertama jika hanya ada satu unit
				let dataToSend = enhancedComponentData;
				if (enhancedComponentData.length === 1) {
					dataToSend = enhancedComponentData[0]; // Send the single object instead of array
				}
				
				const componentDataJson = JSON.stringify(dataToSend);
				console.log('Enhanced Component Data to submit:', enhancedComponentData);
				console.log('JSON data being sent:', componentDataJson);
				fd.append('enhanced_component_data', componentDataJson);
				
				// Also set legacy fields as fallback for Electric units
				if (enhancedComponentData.length === 1) {
					const unitData = enhancedComponentData[0];
					const batteryComponent = unitData.components?.battery;
					const chargerComponent = unitData.components?.charger;
					
					// Set battery_inventory_attachment_id
					if (batteryComponent?.new_inventory_attachment_id) {
						fd.append('battery_inventory_attachment_id', batteryComponent.new_inventory_attachment_id);
					}
					
					// Set charger_inventory_attachment_id
					if (chargerComponent?.new_inventory_attachment_id) {
						fd.append('charger_inventory_attachment_id', chargerComponent.new_inventory_attachment_id);
					}
				}
				
				// Validate each unit's component requirements
				let validationErrors = [];
				
				enhancedComponentData.forEach((unitData, index) => {
					const unitNumber = index + 1;
					
					// Get unit department info from DOM
					const unitPick = document.querySelector(`select[value="${unitData.unit_id}"]`);
					const selectedOption = unitPick ? unitPick.options[unitPick.selectedIndex] : null;
					const departemenId = selectedOption ? selectedOption.getAttribute('data-departemen-id') : null;
					const departemenName = selectedOption ? selectedOption.getAttribute('data-departemen') : 'Unknown';
					
					// Check Electric units (department id = 2)
					if (departemenId === '2') {
						const batteryComponent = unitData.components.battery;
						const chargerComponent = unitData.components.charger;
						
						console.log(`Debug Unit ${unitNumber}:`, {
							batteryComponent,
							chargerComponent,
							departemenId,
							departemenName
						});
						
						// Battery validation: Must have existing (with use_existing action) OR new selection
						const batteryHandled = (batteryComponent.existing_model_id && 
						                       (batteryComponent.action === 'use_existing' || batteryComponent.keep_existing)) || 
						                      batteryComponent.new_inventory_attachment_id;
						
						// Charger validation: Hanya wajib jika ada existing charger atau user memilih untuk assign charger baru
						// Jika tidak ada existing charger dan action = 'skip', maka OK (opsional)
						const chargerHandled = chargerComponent.action === 'skip' || // Charger di-skip = OK
						                       !chargerComponent.existing_model_id || // Tidak ada existing charger = OK (opsional)
						                       (chargerComponent.existing_model_id && 
						                       (chargerComponent.action === 'use_existing' || chargerComponent.keep_existing)) || 
						                       chargerComponent.new_inventory_attachment_id;
						
						console.log(`Debug Unit ${unitNumber} Validation:`, {
							batteryHandled,
							chargerHandled,
							batteryAction: batteryComponent.action,
							chargerAction: chargerComponent.action,
							batteryExistingId: batteryComponent.existing_model_id,
							chargerExistingId: chargerComponent.existing_model_id
						});
						
						if (!batteryHandled) {
							validationErrors.push(`Unit ${unitNumber} (${departemenName}): Battery diperlukan - gunakan yang sudah terpasang atau pilih yang baru`);
						}
						
						if (!chargerHandled) {
							validationErrors.push(`Unit ${unitNumber} (${departemenName}): Charger diperlukan - gunakan yang sudah terpasang atau pilih yang baru`);
						}
					}
					
					// Check Attachment untuk SEMUA unit (opsional - tidak ada yang wajib)
					// Note: Attachment sekarang bersifat universal untuk semua department
					// Tidak ada validasi attachment yang wajib karena ini opsional
				});
				
				// Show validation errors if any
				if (validationErrors.length > 0) {
					alert('Component validation failed:\n\n' + validationErrors.join('\n'));
					return;
				}
			} else {
				// Fallback to legacy behavior for Electric departments
				const electricFields = document.getElementById('electricFields');
				if (electricFields && electricFields.style.display !== 'none') {
					// Check if user chose to use existing components
					const useExistingBattery = document.getElementById('useExistingBattery')?.checked;
					const useExistingCharger = document.getElementById('useExistingCharger')?.checked;
					const existingBatteryId = document.getElementById('existingBatteryModelId')?.value;
					const existingChargerId = document.getElementById('existingChargerModelId')?.value;
					
					const batteryId = document.getElementById('batteryPick')?.value;
					const chargerId = document.getElementById('chargerPick')?.value;
					
					// Enhanced validation: Check if unit actually needs these components
					const unitId = document.querySelector('select[name="unit_id"]')?.value;
					
					// Battery validation: existing + use existing checked OR new selection
					const batteryValid = (useExistingBattery && existingBatteryId) || batteryId;
					// Charger validation: only required if unit actually has charger capability
					const chargerValid = (useExistingCharger && existingChargerId) || chargerId || !existingChargerId;
					
					// Only require battery for Electric units, charger is optional if unit doesn't support it
					if (!batteryValid) {
						alert('For Electric units, Battery is required!\n\nChoose one:\n- Use existing battery (check the checkbox)\n- Select a new battery from the dropdown');
						return;
					}
					
					if (batteryId) fd.append('battery_inventory_attachment_id', batteryId);
					if (chargerId) fd.append('charger_inventory_attachment_id', chargerId);
				}
			}
			
			// Handle pending no_unit update if exists
			if (window.pendingNoUnitUpdate) {
				fd.append('update_no_unit', 'true');
				fd.append('no_unit_action', window.pendingNoUnitUpdate.noUnit);
				// Clear the pending update after using it
				delete window.pendingNoUnitUpdate;
			}
		}
		
		// Handle fabrikasi stage attachment management
		if (currentApprovalStage === 'fabrikasi') {
			const attachmentAction = document.getElementById('fabrikasi_attachment_action')?.value || 'none';
			const existingAttachmentId = document.getElementById('fabrikasi_attachment_existing_id')?.value;
			const newAttachmentId = document.getElementById('fabrikasiAttPick')?.value;
			const transferAttachment = document.getElementById('fabrikasiAttPick')?.dataset.transferAttachment === 'true';
			
			// Also check hidden input for transfer_attachment
			const transferInput = document.getElementById('transfer_attachment_input');
			const transferFromInput = transferInput?.value === 'true';
			
		console.log('Fabrikasi Attachment:', {
			attachmentId: newAttachmentId,
			transferMode: transferAttachment || transferFromInput ? 'KANIBAL' : 'NORMAL'
		});
			
			// Add attachment data to form
			if (attachmentAction === 'keep_existing' && existingAttachmentId) {
				fd.append('attachment_inventory_attachment_id', existingAttachmentId);
			} else if (attachmentAction === 'replace' && newAttachmentId) {
				fd.append('attachment_inventory_attachment_id', newAttachmentId);
				if (transferAttachment || transferFromInput) {
					fd.append('transfer_attachment', 'true');
				}
			} else if (attachmentAction === 'none' && newAttachmentId) {
				fd.append('attachment_inventory_attachment_id', newAttachmentId);
				if (transferAttachment || transferFromInput) {
					fd.append('transfer_attachment', 'true');
				}
			} else if (newAttachmentId) {
				// Fallback: if newAttachmentId exists, use it regardless of action
				fd.append('attachment_inventory_attachment_id', newAttachmentId);
				if (transferAttachment || transferFromInput) {
					fd.append('transfer_attachment', 'true');
				}
			}
		}
		
		// Use original API (now updated to use new database structure)
		const apiUrl = `<?= base_url('service/spk/approve-stage/') ?>${currentApprovalSpkId}`;
		
		// Add unit_index to form data (use 1 as default if not editing specific unit)
		const unitIndex = currentEditingUnitIndex !== null ? currentEditingUnitIndex : 1;
		fd.append('unit_index', unitIndex);
		
		fetch(apiUrl, {
			method: 'POST',
			headers: {'X-Requested-With': 'XMLHttpRequest'},
			body: fd
		}).then(r=>{
			console.log('Response status:', r.status);
			console.log('Response headers:', r.headers);
			return r.json();
		}).then(j=>{
			console.log('Response data:', j);
			if (j && j.success) {
				bootstrap.Modal.getInstance(document.getElementById('approvalStageModal')).hide();
				// Reload table to update buttons
				load();
				
				// Display success message with no_unit info if available
				let successMessage = j.message || 'Approval Successfully Saved';
				
				// Get stage-specific message
				const stageMessages = {
					'persiapan_unit': 'Unit Preparation Successfully Completed',
					'fabrikasi': 'Fabrication Process Successfully Completed',
					'painting': 'Painting Process Successfully Completed',
					'pdi': 'PDI Inspection Successfully Completed'
				};
				const stageSuccessMsg = stageMessages[currentApprovalStage] || 'Stage successfully approved';
				
				// Show notifications based on what happened
				if (j.no_unit) {
					// First alert: Success notification
					notify(stageSuccessMsg, 'success');
					
					// Second alert: No Unit information (after short delay)
					setTimeout(() => {
						Swal.fire({
							icon: 'info',
							title: 'Unit Number Assigned',
							html: `<div class="text-center">
								<p class="mb-2">This unit has been assigned the number:</p>
								<div class="p-2 bg-white bg-opacity-10 rounded">
									<h2 class="display-4 fw-bold text-primary mb-0">${j.no_unit}</h2>
								</div>
							</div>`,
							confirmButtonText: 'OK, Understand',
							confirmButtonColor: '#000000',
							allowOutsideClick: false,
							width: '400px'
						});
					}, 500);
					
					// Third alert: Attachment transfer (if applicable)
					console.log('🔍 Checking attachment_transferred:', j.data.attachment_transferred);
					if (j.data && j.data.attachment_transferred) {
						console.log('✅ Attachment transfer detected, showing alert in 1.5s');
						setTimeout(() => {
							notify('Attachment will be transferred from another unit', 'info');
						}, 1500);
					} else {
						console.log('❌ No attachment transfer detected');
					}
				} else {
					// Standard notification
					notify(stageSuccessMsg, 'success');
					
					// Additional alert: Attachment transfer (if applicable)
					if (j.data && j.data.attachment_transferred) {
						setTimeout(() => {
							notify('Attachment will be transferred from another unit', 'info');
						}, 500);
					}
				}
				
				// Reset editing unit index
				currentEditingUnitIndex = null;
			} else {
				notify(j.message || 'Failed to save approval', 'error');
			}
		}).catch(error => {
			console.error('Fetch error:', error);
			notify('Failed to send request to server', 'error');
		});
	});

	// ==========================================
	// ROLLBACK SYSTEM FUNCTIONALITY
	// ==========================================
	
	// Global variables for rollback
	let currentRollbackSpkId = null;
	let currentRollbackStage = null;
	
	// Show edit options from main page
	window.showEditOptions = function(spkId) {
		console.log('showEditOptions called with SPK ID:', spkId);
		currentRollbackSpkId = spkId;
		
		// Get SPK details first, then try to get units data
		fetch(`<?= base_url('service/spk/edit-options') ?>/${spkId}`, {
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
				'Content-Type': 'application/json'
			}
		})
			.then(response => response.json())
			.then(spkData => {
				console.log('SPK Data:', spkData);
				
				if (spkData.success && spkData.data) {
					// Try to get units data, but don't fail if it doesn't work
					fetch(`<?= base_url('service/spk/units-with-edit') ?>/${spkId}`, {
						headers: {
							'X-Requested-With': 'XMLHttpRequest',
							'Content-Type': 'application/json'
						}
					})
						.then(response => response.json())
						.then(unitsData => {
							console.log('Units Data:', unitsData);
							showEditOptionsModal(spkData, unitsData, spkId);
						})
						.catch(error => {
							console.log('Units data fetch failed, using empty array:', error);
							showEditOptionsModal(spkData, { success: false, data: [] }, spkId);
						});
				} else {
					throw new Error('SPK data not available');
				}
			})
			.catch(error => {
				console.error('Error loading SPK data:', error);
				alert('Failed to load SPK data: ' + error.message);
			});
	}
	
	// Function to show edit options modal (VERY SIMPLE VERSION)
	function showEditOptionsModal(spkData, unitsData, spkId) {
		const spk = spkData.data.spk;
		const stageStatus = spkData.data.stage_status;
		const totalUnits = spk.jumlah_unit || 1;
		const isMultiUnit = totalUnits > 1;

		let editOptionsHtml = `
			<div class="modal-header">
				<h5 class="modal-title">Edit SPK ${spk.nomor_spk}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<div class="d-flex justify-content-between small text-muted border-bottom pb-2 mb-3">
					<span><strong class="text-dark">Status:</strong> ${spk.status}</span>
					<span><strong class="text-dark">Total Unit:</strong> ${totalUnits}</span>
				</div>
		`;

		if (isMultiUnit) {
			// Multi-Unit: Setiap unit dalam 1 baris
			for (let i = 1; i <= totalUnits; i++) {
				// Get unit data from unitsData
				const unitData = unitsData.success && unitsData.data.units ? unitsData.data.units.find(u => u.unit_index === i) : null;
				const unitStages = unitData && unitData.stages ? unitData.stages : {};
				
				const allStages = [
					{ key: 'persiapan_unit', name: 'Persiapan Unit', condition: unitStages.persiapan_unit && unitStages.persiapan_unit.completed },
					{ key: 'fabrikasi', name: 'Fabrikasi', condition: unitStages.fabrikasi && unitStages.fabrikasi.completed },
					{ key: 'painting', name: 'Painting', condition: unitStages.painting && unitStages.painting.completed },
					{ key: 'pdi', name: 'PDI', condition: unitStages.pdi && unitStages.pdi.completed }
				];

				// Buat array untuk stages yang bisa diedit dan yang belum
				const editableStages = allStages.filter(stage => stage.condition);
				const incompleteStages = allStages.filter(stage => !stage.condition);

				editOptionsHtml += `
					<div class="d-flex align-items-center justify-content-between py-2 border-bottom">
						<div class="d-flex align-items-center">
							<span class="fw-bold me-3">Unit #${i}:</span>
							<div class="d-flex gap-2">
				`;

				// Tampilkan stages yang bisa diedit
				editableStages.forEach(stage => {
					editOptionsHtml += `
						<button class="btn btn-primary btn-sm" onclick="directEditStageUnitNew(${spkId}, '${stage.key}', ${i})">
							Edit ${stage.name}
						</button>`;
				});

				// Tampilkan stages yang belum selesai
				incompleteStages.forEach(stage => {
					editOptionsHtml += `
						<button class="btn btn-light btn-sm" disabled>${stage.name}</button>`;
				});

				editOptionsHtml += `
							</div>
						</div>
					</div>`;
			}
		} else {
			// Single-Unit: Langsung tampilkan tombol
			// Get unit data from unitsData for unit 1
			const unitData = unitsData.success && unitsData.data.units ? unitsData.data.units.find(u => u.unit_index === 1) : null;
			const unitStages = unitData && unitData.stages ? unitData.stages : {};
			
			const stages = [
				{ key: 'persiapan_unit', name: 'Persiapan Unit', condition: unitStages.persiapan_unit && unitStages.persiapan_unit.completed },
				{ key: 'fabrikasi', name: 'Fabrikasi', condition: unitStages.fabrikasi && unitStages.fabrikasi.completed },
				{ key: 'painting', name: 'Painting', condition: unitStages.painting && unitStages.painting.completed },
				{ key: 'pdi', name: 'PDI', condition: unitStages.pdi && unitStages.pdi.completed }
			];

			editOptionsHtml += `<div class="row row-cols-1 row-cols-md-2 g-2">`;
			let completedStageFound = false;
			stages.forEach(stage => {
				if (stage.condition) {
					completedStageFound = true;
					editOptionsHtml += `
						<div class="col">
							<button class="btn btn-primary w-100" onclick="directEditStage(${spkId}, '${stage.key}')">
								Edit ${stage.name}
							</button>
						</div>`;
				}
			});

			if (!completedStageFound) {
				editOptionsHtml += `<div class="col-12 text-center text-muted">No stages available for editing.</div>`;
			}
			editOptionsHtml += `</div>`;
		}

		editOptionsHtml += `
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			</div>
		`;

		// Create new modal dynamically to avoid conflicts
		const existingModal = document.querySelector('[id^="editOptionsModal_"]');
		if (existingModal) {
			existingModal.remove();
		}
		
		// Create new modal element
		const newModalId = 'editOptionsModal_' + Date.now();
		const newModalHtml = `
			<div class="modal fade" id="${newModalId}" tabindex="-1">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						${editOptionsHtml}
					</div>
				</div>
			</div>
		`;
		
		// Add to body
		document.body.insertAdjacentHTML('beforeend', newModalHtml);
		
		// Create and show modal
		const modalElement = document.getElementById(newModalId);
		const modal = new bootstrap.Modal(modalElement, {
			backdrop: 'static',
			keyboard: false
		});
		
		// Clean up when modal is hidden
		modalElement.addEventListener('hidden.bs.modal', function() {
			modal.dispose();
			modalElement.remove();
		});
		
		modal.show();
	}

	// Direct edit stage - langsung ke modal tujuan
	window.directEditStage = function(spkId, stage) {
		console.log('directEditStage called with SPK ID:', spkId, 'Stage:', stage);
		
		// Tutup modal edit options dulu
		const editOptionsModal = document.querySelector('[id^="editOptionsModal_"]');
		if (editOptionsModal) {
			const modal = bootstrap.Modal.getInstance(editOptionsModal);
			if (modal) {
				modal.hide();
			}
		}
		
		// Langsung buka modal yang dituju berdasarkan stage
		if (stage === 'persiapan_unit') {
			// Buka modal persiapan unit
			openPersiapanUnitModal(spkId);
		} else if (stage === 'fabrikasi') {
			// Buka modal fabrikasi
			openFabrikasiModal(spkId);
		} else if (stage === 'painting') {
			// Buka modal painting
			openPaintingModal(spkId);
		} else if (stage === 'pdi') {
			// Buka modal PDI
			openPdiModal(spkId);
		}
	}
	
	// Direct edit stage unit - untuk edit unit spesifik
	window.directEditStageUnit = function(spkId, stage, unitNumber) {
		console.log('directEditStageUnit called with SPK ID:', spkId, 'Stage:', stage, 'Unit:', unitNumber);
		
		// Tutup modal edit options dulu
		const editOptionsModal = document.querySelector('[id^="editOptionsModal_"]');
		if (editOptionsModal) {
			const modal = bootstrap.Modal.getInstance(editOptionsModal);
			if (modal) {
				modal.hide();
			}
		}
		
		// Langsung buka modal yang dituju berdasarkan stage dengan info unit
		if (stage === 'persiapan_unit') {
			// Buka modal persiapan unit dengan info unit
			openPersiapanUnitModalWithUnit(spkId, unitNumber);
		} else if (stage === 'fabrikasi') {
			// Buka modal fabrikasi dengan info unit
			openFabrikasiModalWithUnit(spkId, unitNumber);
		} else if (stage === 'painting') {
			// Buka modal painting dengan info unit
			openPaintingModalWithUnit(spkId, unitNumber);
		} else if (stage === 'pdi') {
			// Buka modal PDI dengan info unit
			openPdiModalWithUnit(spkId, unitNumber);
		}
	}

	// NEW: Direct edit stage unit dengan struktur baru
	window.directEditStageUnitNew = function(spkId, stage, unitIndex) {
		console.log('directEditStageUnitNew called with SPK ID:', spkId, 'Stage:', stage, 'Unit Index:', unitIndex);
		
		// Tutup modal edit options dulu
		const editOptionsModal = document.querySelector('[id^="editOptionsModal_"]');
		if (editOptionsModal) {
			const modal = bootstrap.Modal.getInstance(editOptionsModal);
			if (modal) {
				modal.hide();
			}
		}
		
		// Buka modal approval dengan parameter unit_index
		openApprovalModal(stage, getStageDisplayName(stage), spkId, unitIndex);
	}

	// Helper function to get stage display name
	function getStageDisplayName(stage) {
		const stageNames = {
			'persiapan_unit': 'Unit Preparation Dept.',
			'fabrikasi': 'Fabrication Dept.',
			'painting': 'Painting Dept.',
			'pdi': 'PDI Inspection Dept.'
		};
		return stageNames[stage] || stage;
	}
	
	// Function to open unit preparation modal - SIMPLE SOLUTION
	function openPersiapanUnitModal(spkId) {
		console.log('Opening Unit Preparation Modal for SPK ID:', spkId);
		
		// Set unitIndex to 1 for editing (single unit SPK)
		// This prevents the "Unit 2 dari 1" bug
		currentEditingUnitIndex = 1;
		
		// SIMPLE SOLUTION: Open existing approval modal
		// Same as initial modal when filling unit preparation
		openApprovalModal('persiapan_unit', 'Unit Preparation Dept.', spkId, 1);
	}
	
	// Function to open unit preparation modal with specific unit info
	function openPersiapanUnitModalWithUnit(spkId, unitNumber) {
		console.log('Opening Unit Preparation Modal for SPK ID:', spkId, 'Unit:', unitNumber);
		
		// Fetch existing data first, then open modal with pre-populated data
		fetch(`<?= base_url('service/spk/detail') ?>/${spkId}`, {
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
				'Content-Type': 'application/json'
			}
		})
		.then(response => response.json())
		.then(data => {
			if (data.success && data.spk) {
				const spk = data.spk;
				
				// Pre-populate modal with existing data
				if (spk.persiapan_unit_mekanik) {
					document.getElementById('mekanik').value = spk.persiapan_unit_mekanik;
				}
				if (spk.persiapan_unit_estimasi_mulai) {
					document.getElementById('estimasi_mulai').value = spk.persiapan_unit_estimasi_mulai;
				}
				if (spk.persiapan_unit_estimasi_selesai) {
					document.getElementById('estimasi_selesai').value = spk.persiapan_unit_estimasi_selesai;
				}
				
				// Set unit index to pre-select correct unit
				currentEditingUnitIndex = unitNumber;
				
				// Open modal with title showing unit
				openApprovalModal('persiapan_unit', `Unit Preparation Dept. (Unit ${unitNumber})`, spkId);
			} else {
				// Fallback if data cannot be fetched
				openApprovalModal('persiapan_unit', `Unit Preparation Dept. (Unit ${unitNumber})`, spkId);
			}
		})
		.catch(error => {
			console.error('Error fetching SPK data:', error);
			// Fallback if error
			openApprovalModal('persiapan_unit', `Unit Preparation Dept. (Unit ${unitNumber})`, spkId);
		});
	}
	
	// Function to open fabrication modal - SIMPLE SOLUTION
	function openFabrikasiModal(spkId) {
		console.log('Opening Fabrication Modal for SPK ID:', spkId);
		
		// Set unitIndex to 1 for editing (single unit SPK)
		currentEditingUnitIndex = 1;
		
		// SIMPLE SOLUTION: Open existing approval modal
		// Same as initial modal when filling fabrication
		openApprovalModal('fabrikasi', 'Fabrication Dept.', spkId, 1);
	}
	
	// Function to open fabrication modal with specific unit info
	function openFabrikasiModalWithUnit(spkId, unitNumber) {
		console.log('Opening Fabrication Modal for SPK ID:', spkId, 'Unit:', unitNumber);
		
		// SIMPLE SOLUTION: Open existing approval modal with unit info
		openApprovalModal('fabrikasi', `Fabrication Dept. (Unit ${unitNumber})`, spkId);
	}
	
	// Function to open painting modal - SIMPLE SOLUTION
	function openPaintingModal(spkId) {
		console.log('Opening Painting Modal for SPK ID:', spkId);
		
		// Set unitIndex to 1 for editing (single unit SPK)
		currentEditingUnitIndex = 1;
		
		// SIMPLE SOLUTION: Open existing approval modal
		// Same as initial modal when filling painting
		openApprovalModal('painting', 'Painting Dept.', spkId, 1);
	}
	
	// Function to open painting modal with specific unit info
	function openPaintingModalWithUnit(spkId, unitNumber) {
		console.log('Opening Painting Modal for SPK ID:', spkId, 'Unit:', unitNumber);
		
		// SIMPLE SOLUTION: Open existing approval modal with unit info
		openApprovalModal('painting', `Painting Dept. (Unit ${unitNumber})`, spkId);
	}
	
	// Function to open PDI modal - SIMPLE SOLUTION
	function openPdiModal(spkId) {
		console.log('Opening PDI Modal for SPK ID:', spkId);
		
		// Set unitIndex to 1 for editing (single unit SPK)
		currentEditingUnitIndex = 1;
		
		// SIMPLE SOLUTION: Open existing approval modal
		// Same as initial modal when filling PDI
		openApprovalModal('pdi', 'PDI Inspection Dept.', spkId, 1);
	}
	
	// Function to open PDI modal with specific unit info
	function openPdiModalWithUnit(spkId, unitNumber) {
		console.log('Opening PDI Modal for SPK ID:', spkId, 'Unit:', unitNumber);
		
		// SIMPLE SOLUTION: Open existing approval modal with unit info
		openApprovalModal('pdi', `PDI Inspection Dept. (Unit ${unitNumber})`, spkId);
	}

	// Show edit modal - SIMPLIFIED VERSION (LEGACY - tidak digunakan lagi)
	window.showEditModal = function(spkId, stage) {
		console.log('showEditModal called with SPK ID:', spkId, 'Stage:', stage);
		currentRollbackSpkId = spkId;
		currentRollbackStage = stage;
		
		// Create simple edit modal content
		const stageDisplayName = getStageDisplayName(stage);
		const editModalHtml = `
			<div class="modal-header">
				<h5 class="modal-title">
					<i class="fas fa-edit me-2"></i>Edit ${stageDisplayName}
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<div class="alert alert-info">
					<i class="fas fa-info-circle me-2"></i>
					<strong>Info:</strong> Edit ${stageDisplayName} for SPK ID ${spkId}
				</div>
				
				<div class="row">
					<div class="col-12">
						<h6 class="text-primary mb-3">Edit Options:</h6>
					</div>
					<div class="col-md-6 mb-3">
						<div class="card border-primary">
							<div class="card-body text-center">
								<h6 class="card-title text-primary">
									<i class="fas fa-tools me-2"></i>Edit Unit Assignment
								</h6>
								<p class="card-text small">Change unit assignment for this stage</p>
								<button class="btn btn-primary btn-sm" onclick="alert('Edit Unit Assignment clicked!')">
									<i class="fas fa-edit me-1"></i>Edit Unit
								</button>
							</div>
						</div>
					</div>
					<div class="col-md-6 mb-3">
						<div class="card border-primary">
							<div class="card-body text-center">
								<h6 class="card-title text-primary">
									<i class="fas fa-cog me-2"></i>Edit Stage Settings
								</h6>
								<p class="card-text small">Modify stage configuration</p>
								<button class="btn btn-primary btn-sm" onclick="alert('Edit Stage Settings clicked!')">
									<i class="fas fa-cog me-1"></i>Edit Settings
								</button>
							</div>
						</div>
					</div>
				</div>
				
				<div class="alert alert-warning mt-3">
					<i class="fas fa-exclamation-triangle me-2"></i>
					<strong>Warning:</strong> Changes will affect the approved stage. Please confirm before proceeding.
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onclick="alert('Save Changes clicked!')">
					<i class="fas fa-save me-1"></i>Save Changes
				</button>
			</div>
		`;
		
		// Create new modal dynamically
		const existingModal = document.querySelector('[id^="editStageModal_"]');
		if (existingModal) {
			existingModal.remove();
		}
		
		// Create new modal element
		const newModalId = 'editStageModal_' + Date.now();
		const newModalHtml = `
			<div class="modal fade" id="${newModalId}" tabindex="-1">
				<div class="modal-dialog">
					<div class="modal-content">
						${editModalHtml}
					</div>
				</div>
			</div>
		`;
		
		// Add to body
		document.body.insertAdjacentHTML('beforeend', newModalHtml);
		
		// Create and show modal
		const modalElement = document.getElementById(newModalId);
		const modal = new bootstrap.Modal(modalElement, {
			backdrop: 'static',
			keyboard: false
		});
		
		// Clean up when modal is hidden
		modalElement.addEventListener('hidden.bs.modal', function() {
			modal.dispose();
			modalElement.remove();
		});
		
		modal.show();
	}
	
	// Get stage display name
	window.getStageDisplayName = function(stage) {
		const stageNames = {
			'persiapan_unit': 'Persiapan Unit',
			'fabrikasi': 'Fabrikasi',
			'painting': 'Painting',
			'pdi': 'PDI'
		};
		return stageNames[stage] || stage;
	}
	
	// Populate rollback info
	function populateRollbackInfo(spkId, stage) {
		const infoDiv = document.getElementById('rollbackInfo');
		
		fetch(`<?= base_url('service/spk/detail') ?>/${spkId}`)
			.then(response => response.json())
			.then(data => {
				if (data.success && data.data) {
					const spk = data.data;
					let infoHtml = `
						<div class="card">
							<div class="card-body">
								<h6 class="card-title">Informasi SPK</h6>
								<p><strong>Nomor SPK:</strong> ${spk.nomor_spk || '-'}</p>
								<p><strong>Status:</strong> <span class="badge bg-${getStatusColor(spk.status)}">${spk.status || '-'}</span></p>
								<p><strong>Rollback Stage:</strong> ${getStageDisplayName(stage)}</p>
					`;
					
					if (stage === 'persiapan_unit' && spk.persiapan_unit_id) {
						infoHtml += `<p><strong>Current Unit:</strong> Unit ID ${spk.persiapan_unit_id}</p>`;
					}
					
					if (stage === 'fabrikasi' && spk.fabrikasi_tanggal_approve) {
						infoHtml += `<p><strong>Approval Date:</strong> ${spk.fabrikasi_tanggal_approve}</p>`;
					}
					
					infoHtml += `
							</div>
						</div>
					`;
					
					infoDiv.innerHTML = infoHtml;
				}
			})
			.catch(error => {
				infoDiv.innerHTML = '<div class="alert alert-danger">Failed to load SPK information</div>';
			});
	}
	
	// Load SPK units for edit selection
	function loadSpkUnitsForEdit(spkId) {
		fetch(`<?= base_url('service/spk/units-with-edit') ?>/${spkId}`, {
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
				'Content-Type': 'application/json'
			}
		})
			.then(response => response.json())
			.then(data => {
				if (data.success && data.data.units) {
					const units = data.data.units;
					const selectElement = document.getElementById('rollbackUnitIndexSelect');
					
					const options = units.map(unit => {
						const unitInfo = `${unit.no_unit || 'No Unit'} - ${unit.merk || ''} ${unit.model || ''}`.trim();
						return `<option value="${unit.unit_index}">Unit ${unit.unit_index}: ${unitInfo}</option>`;
					}).join('');
					
					selectElement.innerHTML = '<option value="">-- Select Unit to Change --</option>' + options;
				}
			})
			.catch(error => console.log('Error loading SPK units:', error));
	}

	// Load available units for edit
	function loadAvailableUnitsForEdit() {
		const searchInput = document.getElementById('rollbackUnitSearch');
		const selectElement = document.getElementById('rollbackUnitSelect');
		
		// Load initial units
		fetch('<?= base_url('service/data-unit/simple') ?>')
			.then(response => response.json())
			.then(data => {
				if (data.success && data.data) {
					const options = data.data.map(unit => 
						`<option value="${unit.id}">${unit.label}</option>`
					).join('');
					selectElement.innerHTML = '<option value="">-- Select New Unit --</option>' + options;
				}
			})
			.catch(error => console.log('Error loading units:', error));
		
		// Search functionality
		searchInput.addEventListener('input', function() {
			const query = this.value.trim();
			if (query.length >= 2) {
				fetch(`<?= base_url('service/data-unit/simple') ?>?q=${encodeURIComponent(query)}`)
					.then(response => response.json())
					.then(data => {
						if (data.success && data.data) {
							const options = data.data.map(unit => 
								`<option value="${unit.id}">${unit.label}</option>`
							).join('');
							selectElement.innerHTML = '<option value="">-- Select New Unit --</option>' + options;
						}
					})
					.catch(error => console.log('Error searching units:', error));
			}
		});
	}
	
	// Confirm rollback
	document.getElementById('confirmRollbackBtn').addEventListener('click', function() {
		const reason = document.getElementById('rollbackReason').value.trim();
		
		if (!reason) {
			alert('Alasan rollback wajib diisi!');
			return;
		}
		
		if (currentRollbackStage === 'persiapan_unit') {
			const newUnitId = document.getElementById('rollbackUnitSelect').value;
			const unitIndex = document.getElementById('rollbackUnitIndexSelect').value;
			
			if (!newUnitId) {
				alert('New unit must be selected!');
				return;
			}
			
			if (!unitIndex) {
				alert('Unit to be changed must be selected!');
				return;
			}
			
			// Change unit
			const formData = new FormData();
			formData.append('unit_id', newUnitId);
			formData.append('unit_index', unitIndex);
			formData.append('reason', reason);
			
			fetch(`<?= base_url('service/spk/change-unit') ?>/${currentRollbackSpkId}`, {
				method: 'POST',
				headers: {'X-Requested-With': 'XMLHttpRequest'},
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					notify(data.message, 'success');
					bootstrap.Modal.getInstance(document.getElementById('rollbackModal')).hide();
					load(); // Reload SPK list
				} else {
					notify(data.message, 'error');
				}
			})
			.catch(error => {
				notify('Gagal melakukan rollback', 'error');
			});
			
		} else {
			// Regular rollback
			const formData = new FormData();
			formData.append('stage', currentRollbackStage);
			formData.append('reason', reason);
			
			fetch(`<?= base_url('service/spk/rollback-stage') ?>/${currentRollbackSpkId}`, {
				method: 'POST',
				headers: {'X-Requested-With': 'XMLHttpRequest'},
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					notify(data.message, 'success');
					bootstrap.Modal.getInstance(document.getElementById('rollbackModal')).hide();
					load(); // Reload SPK list
				} else {
					notify(data.message, 'error');
				}
			})
			.catch(error => {
				notify('Gagal melakukan rollback', 'error');
			});
		}
	});
	
	// Helper function to get status color
	function getStatusColor(status) {
		const colors = {
			'DRAFT': 'secondary',
			'IN_PROGRESS': 'warning',
			'READY': 'info',
			'COMPLETED': 'success',
			'DELIVERED': 'primary'
		};
		return colors[status] || 'secondary';
	}
	
	// Check rollback availability when opening approval modal
	const originalLoadStageSpecificContent = loadStageSpecificContent;
	loadStageSpecificContent = function(stage, spkId) {
		originalLoadStageSpecificContent(stage, spkId);
		
		// Check rollback availability after a short delay
		setTimeout(() => {
			checkRollbackAvailability(spkId, stage);
		}, 500);
	};
	
	// Function to check rollback availability
	function checkRollbackAvailability(spkId, stage) {
		console.log('Checking rollback availability for SPK:', spkId, 'Stage:', stage);
		
		// For now, just hide rollback button since we're using new structure
		const rollbackBtn = document.getElementById('rollbackStageBtn');
		if (rollbackBtn) {
			rollbackBtn.style.display = 'none';
		}
		
		// TODO: Implement rollback logic for new spk_unit_stages structure
		console.log('Rollback availability checked - using new structure');
	}

	// Function to load existing unit data for editing
	function loadExistingUnitData(spkId, unitIndex, stage) {
		console.log('Loading existing unit data for SPK:', spkId, 'Unit:', unitIndex, 'Stage:', stage);
		
		// Fetch existing data from new API
		fetch(`<?= base_url('service/spk/units-with-edit/') ?>${spkId}`, {
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
				'Content-Type': 'application/json'
			}
		})
			.then(response => response.json())
			.then(data => {
				if (data.success && data.data.units) {
					const unit = data.data.units.find(u => u.unit_index === unitIndex);
					if (unit && unit.persiapan_data) {
						console.log('Found existing unit data:', unit);
						
						// Load unit info if available
						if (unit.unit_info) {
							const unitInfo = unit.unit_info;
							const unitSelect = document.getElementById('approvalUnitPick');
							if (unitSelect) {
								// Create option for existing unit
								const option = document.createElement('option');
								option.value = unitInfo.id_inventory_unit;
								option.textContent = `${unitInfo.no_unit} - ${unitInfo.merk_unit} ${unitInfo.model_unit}`;
								option.selected = true;
								unitSelect.appendChild(option);
								
								// Trigger change event to load area
								unitSelect.dispatchEvent(new Event('change'));
							}
						}
						
						// Load area if available
						if (unit.persiapan_data.area_id) {
							const areaSelect = document.getElementById('approvalAreaPick');
							if (areaSelect) {
								areaSelect.value = unit.persiapan_data.area_id;
							}
						}
						
						// Load accessories if available
						if (unit.persiapan_data.aksesoris_tersedia) {
							try {
								const aksesoris = JSON.parse(unit.persiapan_data.aksesoris_tersedia);
								if (Array.isArray(aksesoris)) {
									aksesoris.forEach(aks => {
										const checkbox = document.getElementById(`aks_${aks.replace(/\s+/g, '_')}`);
										if (checkbox) {
											checkbox.checked = true;
										}
									});
								}
							} catch (e) {
								console.log('Error parsing accessories:', e);
							}
						}
					}
					
					// Handle fabrikasi stage data
					if (stage === 'fabrikasi' && unit.stages && unit.stages.fabrikasi) {
						const fabrikasiData = unit.stages.fabrikasi;
						console.log('Loading existing fabrication data:', fabrikasiData);
						
						// Load existing attachment data for fabrikasi
						if (fabrikasiData.completed && fabrikasiData.attachment_inventory_attachment_id) {
							// Set attachment action to keep existing
							const attachmentActionInput = document.getElementById('fabrikasi_attachment_action');
							if (attachmentActionInput) {
								attachmentActionInput.value = 'keep_existing';
							}
							
							// Set existing attachment ID
							const existingAttachmentIdInput = document.getElementById('fabrikasi_attachment_existing_id');
							if (existingAttachmentIdInput) {
								existingAttachmentIdInput.value = fabrikasiData.attachment_inventory_attachment_id;
							}
							
							// Check the keep existing radio button
							const keepRadio = document.getElementById('attachment_keep');
							if (keepRadio) {
								keepRadio.checked = true;
								keepRadio.dispatchEvent(new Event('change'));
							}
						}
					}
				}
			})
			.catch(error => {
				console.log('Error loading existing unit data:', error);
			});
	}
});
</script>

<script>
// ===== ENHANCEMENT: Smart Unit Component Management =====

/**
 * Fetch unit component data dari inventory_attachment untuk check existing components
 */
async function fetchUnitComponentData(unitId) {
	try {
		console.log('Fetching unit component data for unit:', unitId);
		const apiUrl = '<?= base_url('warehouse/inventory/unit-components') ?>?unit_id=' + unitId;
		console.log('API URL:', apiUrl);
		
		const response = await fetch(apiUrl, {
			method: 'GET',
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
				'Content-Type': 'application/json'
			}
		});
		
		console.log('API Response status:', response.status);
		
		if (!response.ok) {
			throw new Error(`HTTP ${response.status}: ${response.statusText}`);
		}
		
		const data = await response.json();
		console.log('API Response data:', data);
		
		if (data.error) {
			throw new Error(data.error);
		}
		
		return { 
			success: true, 
			unit_id: data.unit_id,
			battery: data.battery,
			charger: data.charger,
			attachment: data.attachment
		};
	} catch (error) {
		console.error('Error fetching unit component data:', error);
		return { success: false, error: error.message };
	}
}

/**
 * Generate smart component selection UI based on existing components
 * @param {Object} apiData - Response dari API unit components
 * @param {Object} options - Object untuk menentukan komponen mana yang ditampilkan {battery: true, charger: true, attachment: true}
 * @param {string} unitId - ID unit untuk tracking
 * @param {string} suffix - Suffix untuk ID unik
 */
function generateComponentSelectionUI(apiData, options = {}, unitId = '', suffix = '', uiType = 'component') {
	let html = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>This unit has installed components. Please choose the desired options:</div>';
	
	console.log('generateComponentSelectionUI called with:', {
		apiData,
		options,
		unitId,
		suffix,
		uiType
	});
	
	// Add unique identifier to prevent duplicate renders with UI type specificity
	const renderKey = `${uiType}-ui-${unitId}-${suffix}`;
	html += `<div data-render-key="${renderKey}">`;
	
	// Handle Battery
	if (apiData.battery || options.battery) {
		html += '<div class="mb-3"><h6><i class="fas fa-battery-full me-2"></i>Battery Management</h6>';
		
		if (apiData.battery) {
			// Unit already has battery - provide options
			const battery = apiData.battery;
			const batteryName = `${battery.merk_baterai || '-'} ${battery.tipe_baterai || ''} ${battery.jenis_baterai || ''}`.trim();
			const batterySn = battery.sn_baterai || '-';
			
			html += `
				<!-- Hidden fields untuk data existing battery -->
				<input type="hidden" id="existingBatteryModelId${suffix}" value="${battery.id_inventory_attachment || ''}">
				<input type="hidden" id="existingBatterySn${suffix}" value="${batterySn}">
				<input type="hidden" id="batteryAction${suffix}" value="use_existing">
				
				<div class="card bg-light mb-2">
					<div class="card-body py-2">
						<small><strong>Battery Installed:</strong> ${batteryName} • SN: ${batterySn}</small>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="useExistingBattery${suffix}" onchange="toggleBatteryOptions('existing', this.checked, '${suffix}')" checked>
							<label class="form-check-label" for="useExistingBattery${suffix}">
								<i class="fas fa-check-circle text-success me-1"></i>Use Existing Battery
							</label>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="replaceBattery${suffix}" onchange="toggleBatteryOptions('replace', this.checked, '${suffix}')">
							<label class="form-check-label" for="replaceBattery${suffix}">
								<i class="fas fa-exchange-alt text-warning me-1"></i>Replace Battery with New
							</label>
						</div>
					</div>
				</div>
				<div id="replaceBatteryOptions${suffix}" style="display: none;" class="mt-2">
					<label class="form-label">Select Replacement Battery <span class="text-danger">*</span></label>
					<select class="form-select" id="batteryPick${suffix}" name="battery_inventory_attachment_id" data-old-battery-id="${battery.id_inventory_attachment}">
						<option value="">- Select New Battery -</option>
					</select>
				</div>`;
		} else if (options.battery) {
			// Department requires battery but no existing battery - need to assign new
			html += `
				<div class="alert alert-warning py-2 mb-2">
					<small><i class="fas fa-exclamation-triangle me-1"></i>This unit requires a Battery</small>
				</div>
				<label class="form-label">Select Battery <span class="text-danger">*</span></label>
				<select class="form-select" id="batteryPick${suffix}" name="battery_inventory_attachment_id">
					<option value="">- Select Battery -</option>
				</select>`;
		}
		
		html += '</div>';
	}
	
	// Handle Charger
	if (apiData.charger || options.charger) {
		html += '<div class="mb-3"><h6><i class="fas fa-plug me-2"></i>Charger Management</h6>';
		
		console.log('DEBUG Charger section:', {
			'apiData.charger': apiData.charger,
			'options.charger': options.charger,
			'apiData.battery': apiData.battery
		});
		
		if (apiData.charger) {
			// Unit already has charger - provide options
			const charger = apiData.charger;
			const chargerName = `${charger.merk_charger || '-'} ${charger.tipe_charger || ''}`.trim();
			const chargerSn = charger.sn_charger || '-';
			
			html += `
				<!-- Hidden fields untuk data existing charger -->
				<input type="hidden" id="existingChargerModelId${suffix}" value="${charger.id_inventory_attachment || ''}">
				<input type="hidden" id="existingChargerSn${suffix}" value="${chargerSn}">
				<input type="hidden" id="chargerAction${suffix}" value="use_existing">
				
				<div class="card bg-light mb-2">
					<div class="card-body py-2">
						<small><strong>Charger Installed:</strong> ${chargerName} • SN: ${chargerSn}</small>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="useExistingCharger${suffix}" onchange="toggleChargerOptions('existing', this.checked, '${suffix}')" checked>
							<label class="form-check-label" for="useExistingCharger${suffix}">
								<i class="fas fa-check-circle text-success me-1"></i>Use Existing Charger
							</label>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-check">
							<input class="form-check-input" type="checkbox" id="replaceCharger${suffix}" onchange="toggleChargerOptions('replace', this.checked, '${suffix}')">
							<label class="form-check-label" for="replaceCharger${suffix}">
								<i class="fas fa-exchange-alt text-warning me-1"></i>Replace Charger with New
							</label>
						</div>
					</div>
				</div>
				<div id="replaceChargerOptions${suffix}" style="display: none;" class="mt-2">
					<label class="form-label">Select Replacement Charger <span class="text-danger">*</span></label>
					<select class="form-select" id="chargerPick${suffix}" name="charger_inventory_attachment_id" data-old-charger-id="${charger.id_inventory_attachment}">
						<option value="">- Select New Charger -</option>
					</select>
				</div>`;
		} else if (options.charger) {
			// Department requires charger but no existing charger - need to assign new
			html += `
				<div class="alert alert-warning py-2 mb-2">
					<small><i class="fas fa-exclamation-triangle me-1"></i>This unit requires a Charger</small>
				</div>
				<label class="form-label">Select Charger <span class="text-danger">*</span></label>
				<select class="form-select" id="chargerPick${suffix}" name="charger_inventory_attachment_id">
					<option value="">- Select Charger -</option>
				</select>`;
		}
		
		html += '</div>';
	}
	
	// Note: Attachment Management dipindahkan ke bagian Fabrikasi, tidak ada di Persiapan Unit
	
	// Load options for replacement dropdowns
	setTimeout(() => {
		console.log(`🔌 Checking elements with suffix: ${suffix}`);
		if (document.getElementById(`batteryPick${suffix}`)) {
			console.log(`🔌 Found batteryPick${suffix}, loading options`);
			loadBatteryOptionsIndividual(suffix);
		}
		if (document.getElementById(`chargerPick${suffix}`)) {
			console.log(`🔌 Found chargerPick${suffix}, loading options`);
			loadChargerOptionsIndividual(suffix);
		}
	}, 100);
	
	html += '</div>'; // Close data-render-key div
	return html;
}

/**
 * Handle battery options toggle - new explicit checkbox approach
 */
function toggleBatteryOptions(type, isChecked, suffix = '') {
	console.log(`toggleBatteryOptions called: type=${type}, isChecked=${isChecked}, suffix=${suffix}`);
	
	const useExistingId = `useExistingBattery${suffix}`;
	const replaceBatteryId = `replaceBattery${suffix}`;
	const replaceOptionsId = `replaceBatteryOptions${suffix}`;
	
	const useExistingCheckbox = document.getElementById(useExistingId);
	const replaceBatteryCheckbox = document.getElementById(replaceBatteryId);
	const replaceOptionsDiv = document.getElementById(replaceOptionsId);
	
	if (type === 'existing') {
		// User clicked "use existing battery"
		if (isChecked) {
			// Use existing - uncheck replace option
			if (replaceBatteryCheckbox) replaceBatteryCheckbox.checked = false;
			if (replaceOptionsDiv) replaceOptionsDiv.style.display = 'none';
		}
	} else if (type === 'replace') {
		// User clicked "replace battery"
		if (isChecked) {
			// Replace - uncheck existing option and show battery picker
			if (useExistingCheckbox) useExistingCheckbox.checked = false;
			if (replaceOptionsDiv) {
				replaceOptionsDiv.style.display = 'block';
				// Load available batteries
				loadBatteryOptionsIndividual(suffix);
			}
		} else {
			// Unchecked replace - hide options
			if (replaceOptionsDiv) replaceOptionsDiv.style.display = 'none';
		}
	}
}

/**
 * Handle charger options toggle - new explicit checkbox approach
 */
function toggleChargerOptions(type, isChecked, suffix = '') {
	const useExistingCheckbox = document.getElementById(`useExistingCharger${suffix}`);
	const replaceCheckbox = document.getElementById(`replaceCharger${suffix}`);
	const selectionSection = document.getElementById(`chargerSelectionSection${suffix}`);
	const actionInput = document.getElementById(`chargerAction${suffix}`);
	const existingModelId = document.getElementById(`existingChargerModelId${suffix}`)?.value;
	const keepExistingInput = document.getElementById(`keepExistingCharger${suffix}`);
	
	console.log(`toggleChargerOptions called: type=${type}, isChecked=${isChecked}, suffix=${suffix}`);
	
	if (type === 'existing') {
		if (isChecked) {
			// User chose to use existing - uncheck replace and hide selection
			replaceCheckbox.checked = false;
			if (selectionSection) selectionSection.style.display = 'none';
			actionInput.value = 'use_existing';
			
			// Set the keepExisting value to true
			if (keepExistingInput) keepExistingInput.value = 'true';
			
			// Ensure the select is not required if using existing
			const chargerPick = document.getElementById(`chargerPick${suffix}`);
			if (chargerPick) {
				chargerPick.removeAttribute('required');
				chargerPick.value = ''; // Clear any selection
			}
			
			console.log(`Use existing charger selected: actionInput.value = ${actionInput.value}, existingModelId = ${existingModelId}, keepExisting = ${keepExistingInput?.value}`);
		} else {
			// User unchecked existing
			actionInput.value = '';
			console.log(`Use existing charger unselected: actionInput.value = ${actionInput.value}`);
		}
	} else if (type === 'replace') {
		if (isChecked) {
			// User chose to replace - uncheck existing and show selection
			useExistingCheckbox.checked = false;
			if (selectionSection) selectionSection.style.display = 'block';
			actionInput.value = 'replace';
			
			// Set the keepExisting value to false
			if (keepExistingInput) keepExistingInput.value = 'false';
			
			// Load available chargers directly
			const chargerPick = document.getElementById(`chargerPick${suffix}`);
			fetch('<?= base_url('warehouse/inventory/available-chargers') ?>')
				.then(r => r.json())
				.then(data => {
					if (chargerPick && Array.isArray(data)) {
						chargerPick.innerHTML = '<option value="">- Select New Charger -</option>' + 
							data.map(item => {
								const name = `${item.merk_charger||'-'} ${item.tipe_charger||''}`.trim();
								return `<option value="${item.id_inventory_attachment}">${name} • SN: ${item.sn_charger||'-'}</option>`;
							}).join('');
						
						// Make the select required since we're replacing
						chargerPick.setAttribute('required', 'required');
						
						// Update availability indicators after loading options
						setTimeout(() => {
							updateDropdownAvailability(chargerPick, 'charger');
						}, 100);
					}
				})
				.catch(err => console.log('Error loading chargers:', err));
		} else {
			// User unchecked replace
			selectionSection.style.display = 'none';
			actionInput.value = '';
		}
	}
}

/**
 * Handle attachment options toggle - new explicit checkbox approach
 */
function toggleAttachmentOptions(type, isChecked, suffix = '') {
	const useExistingCheckbox = document.getElementById(`useExistingAttachment${suffix}`);
	const replaceCheckbox = document.getElementById(`replaceAttachment${suffix}`);
	const selectionSection = document.getElementById(`attachmentSelectionSection${suffix}`);
	const actionInput = document.getElementById(`attachmentAction${suffix}`);
	const existingModelId = document.getElementById(`existingAttachmentModelId${suffix}`)?.value;
	const keepExistingInput = document.getElementById(`keepExistingAttachment${suffix}`);
	
	console.log(`toggleAttachmentOptions called: type=${type}, isChecked=${isChecked}, suffix=${suffix}`);
	
	if (type === 'existing') {
		if (isChecked) {
			// User chose to use existing - uncheck replace and hide selection
			replaceCheckbox.checked = false;
			if (selectionSection) selectionSection.style.display = 'none';
			actionInput.value = 'use_existing';
			
			// Set the keepExisting value to true
			if (keepExistingInput) keepExistingInput.value = 'true';
			
			// Ensure the select is not required if using existing
			const attachmentPick = document.getElementById(`attachmentPick${suffix}`);
			if (attachmentPick) {
				attachmentPick.removeAttribute('required');
				attachmentPick.value = ''; // Clear any selection
			}
			
			console.log(`Use existing attachment selected: actionInput.value = ${actionInput.value}, existingModelId = ${existingModelId}, keepExisting = ${keepExistingInput?.value}`);
		} else {
			// User unchecked existing
			actionInput.value = '';
			console.log(`Use existing attachment unselected: actionInput.value = ${actionInput.value}`);
		}
	} else if (type === 'replace') {
		if (isChecked) {
			// User chose to replace - uncheck existing and show selection
			useExistingCheckbox.checked = false;
			if (selectionSection) selectionSection.style.display = 'block';
			actionInput.value = 'replace';
			
			// Set the keepExisting value to false
			if (keepExistingInput) keepExistingInput.value = 'false';
			
			// Load available attachments directly
			const attachmentPick = document.getElementById(`attachmentPick${suffix}`);
			fetch('<?= base_url('warehouse/inventory/available-attachments') ?>')
				.then(r => r.json())
				.then(data => {
					if (attachmentPick && Array.isArray(data)) {
						attachmentPick.innerHTML = '<option value="">- Select New Attachment -</option>' + 
							data.map(item => {
								const name = `${item.tipe||'-'} ${item.merk||'-'} ${item.model||''}`.trim();
								return `<option value="${item.id_inventory_attachment}">${name} • SN: ${item.sn_attachment||'-'}</option>`;
							}).join('');
						
						// Make the select required since we're replacing
						attachmentPick.setAttribute('required', 'required');
						
						// Update availability indicators after loading options
						setTimeout(() => {
							updateDropdownAvailability(attachmentPick, 'attachment');
						}, 100);
					}
				})
				.catch(err => console.log('Error loading attachments:', err));
		} else {
			// User unchecked replace
			selectionSection.style.display = 'none';
			actionInput.value = '';
		}
	}
}

/**
 * Collect component data untuk multiple units
 */
function collectEnhancedComponentDataMultiUnit() {
	const allUnitsData = [];
	
	// Find all unit selection fields - support both multi-unit dan single unit
	let unitPicks = document.querySelectorAll('select[name="unit_id[]"]'); // Multi-unit (legacy)
	if (unitPicks.length === 0) {
		// Try single unit processing
		unitPicks = document.querySelectorAll('select[name="unit_id"]'); // Single unit (new)
	}
	
	console.log(`Found ${unitPicks.length} units to process for enhanced component data`);
	
	unitPicks.forEach((unitPick, index) => {
		if (unitPick.value) {
			const suffix = unitPick.id.replace('approvalUnitPick', '');
			console.log(`Processing unit #${index+1} with ID=${unitPick.value} and suffix=${suffix}`);
			
			// Get department info for better debugging
			const selectedOption = unitPick.options[unitPick.selectedIndex];
			const departemenId = selectedOption?.getAttribute('data-departemen-id');
			const departemenName = selectedOption?.getAttribute('data-departemen');
			console.log(`Unit department: ${departemenName} (ID: ${departemenId})`);
			
			const unitData = collectSingleUnitComponentData(suffix, unitPick.value);
			if (unitData) {
				console.log(`Collected component data for unit ID ${unitPick.value}:`, unitData);
				allUnitsData.push(unitData);
			} else {
				console.warn(`Failed to collect component data for unit ID ${unitPick.value}`);
			}
		}
	});
	
	return allUnitsData;
}

/**
 * Collect component data untuk single unit dengan suffix
 */
function collectSingleUnitComponentData(suffix, unitId) {
	console.log(`collectSingleUnitComponentData called with suffix='${suffix}', unitId=${unitId}`);
	
	// Try both with and without suffix for backwards compatibility
	const existingBatteryModelId = document.getElementById(`existingBatteryModelId${suffix}`)?.value || document.getElementById(`existingBatteryModelId`)?.value;
	const batteryActionValue = document.getElementById(`batteryAction${suffix}`)?.value || document.getElementById(`batteryAction`)?.value;
	const useExistingBatteryChecked = document.getElementById(`useExistingBattery${suffix}`)?.checked || document.getElementById(`useExistingBattery`)?.checked;
	
	console.log(`Battery data: existingModelId=${existingBatteryModelId}, action=${batteryActionValue}, useExisting=${useExistingBatteryChecked}`);
	
	// Similar approach for charger
	const existingChargerModelId = document.getElementById(`existingChargerModelId${suffix}`)?.value || document.getElementById(`existingChargerModelId`)?.value;
	const useExistingChargerChecked = document.getElementById(`useExistingCharger${suffix}`)?.checked || document.getElementById(`useExistingCharger`)?.checked;
	
	console.log(`Charger data: existingModelId=${existingChargerModelId}, useExisting=${useExistingChargerChecked}`);
	
	const data = {
		unit_id: unitId,
		components: {
			battery: {
				action: useExistingBatteryChecked ? 'use_existing' : 'replace', // use_existing, replace, assign
				existing_model_id: existingBatteryModelId,
				existing_sn: document.getElementById(`existingBatterySn${suffix}`)?.value || document.getElementById(`existingBatterySn`)?.value,
				new_inventory_attachment_id: null,
				keep_existing: useExistingBatteryChecked,
				battery_detected: !!existingBatteryModelId // Flag to show backend we detected a battery
			},
			charger: {
				action: useExistingChargerChecked ? 'use_existing' : 'replace',
				existing_model_id: existingChargerModelId,
				existing_sn: document.getElementById(`existingChargerSn${suffix}`)?.value || document.getElementById(`existingChargerSn`)?.value,
				new_inventory_attachment_id: null,
				keep_existing: useExistingChargerChecked,
				charger_detected: !!document.getElementById(`existingChargerModelId${suffix}`)?.value
			},
			attachment: {
				action: 'keep',
				existing_model_id: document.getElementById(`existingAttachmentModelId${suffix}`)?.value,
				existing_sn: document.getElementById(`existingAttachmentSn${suffix}`)?.value,
				new_inventory_attachment_id: null,
				keep_existing: document.getElementById(`keepExistingAttachment${suffix}`)?.value === 'true',
				attachment_detected: !!document.getElementById(`existingAttachmentModelId${suffix}`)?.value
			}
		}
	};

	// Determine battery action using new explicit approach
	const batteryPick = document.getElementById(`batteryPick${suffix}`);
	const batteryAction = document.getElementById(`batteryAction${suffix}`)?.value;
	
	console.log(`Processing battery for unit ${unitId}:`, {
		batteryAction,
		existingBatteryModelId: data.components.battery.existing_model_id,
		batteryPick: batteryPick?.value
	});
	
	if (data.components.battery.existing_model_id) {
		// Unit has existing battery
		if (batteryAction === 'replace' && batteryPick?.value) {
			// User explicitly chose to replace and selected new battery
			data.components.battery.action = 'replace';
			data.components.battery.new_inventory_attachment_id = batteryPick.value;
			data.components.battery.keep_existing = false;
			console.log(`Unit ${unitId}: Replacing battery with ${batteryPick.value}`);
		} else if (batteryAction === 'use_existing' || document.getElementById(`useExistingBattery${suffix}`)?.checked) {
			// User explicitly chose to use existing battery OR the "use existing" checkbox is checked (default)
			data.components.battery.action = 'use_existing'; // Change from 'keep' to 'use_existing' for clarity
			data.components.battery.new_inventory_attachment_id = data.components.battery.existing_model_id; // Set the existing ID
			data.components.battery.keep_existing = true;
			console.log(`Unit ${unitId}: Using existing battery ${data.components.battery.existing_model_id}`);
		} else {
			// Default to keep existing if no other option selected
			data.components.battery.action = 'use_existing'; // Change from 'keep' to 'use_existing' for clarity
			data.components.battery.new_inventory_attachment_id = data.components.battery.existing_model_id; // Set the existing ID
			data.components.battery.keep_existing = true;
			console.log(`Unit ${unitId}: Default behavior - keeping existing battery ${data.components.battery.existing_model_id}`);
		}
		
		// Force set to keep_existing if no other valid options selected
		if (!data.components.battery.new_inventory_attachment_id && data.components.battery.existing_model_id) {
			data.components.battery.action = 'use_existing'; // Change from 'keep' to 'use_existing' for clarity
			data.components.battery.new_inventory_attachment_id = data.components.battery.existing_model_id; // Set the existing ID
			data.components.battery.keep_existing = true;
		}
	} else {
		// Unit doesn't have battery - must assign new one
		if (batteryPick?.value) {
			data.components.battery.action = 'assign';
			data.components.battery.new_inventory_attachment_id = batteryPick.value;
			data.components.battery.keep_existing = false;
		}
	}
	
	// Add debug logging for battery data being collected
	console.log(`Final battery data for unit ${unitId}:`, data.components.battery);

	// Determine charger action using new explicit approach  
	const chargerPick = document.getElementById(`chargerPick${suffix}`);
	const chargerAction = document.getElementById(`chargerAction${suffix}`)?.value;
	
	console.log(`Processing charger for unit ${unitId}:`, {
		chargerAction,
		existingChargerModelId: data.components.charger.existing_model_id,
		chargerPick: chargerPick?.value
	});
	
	if (data.components.charger.existing_model_id) {
		// Unit has existing charger
		if (chargerAction === 'replace' && chargerPick?.value) {
			// User explicitly chose to replace and selected new charger
			data.components.charger.action = 'replace';
			data.components.charger.new_inventory_attachment_id = chargerPick.value;
			data.components.charger.keep_existing = false;
			console.log(`Unit ${unitId}: Replacing charger with ${chargerPick.value}`);
		} else if (chargerAction === 'use_existing' || document.getElementById(`useExistingCharger${suffix}`)?.checked) {
			// User explicitly chose to use existing charger OR the "use existing" checkbox is checked (default)
			data.components.charger.action = 'use_existing'; // Change from 'keep' to 'use_existing' for clarity
			data.components.charger.new_inventory_attachment_id = data.components.charger.existing_model_id; // Set the existing ID
			data.components.charger.keep_existing = true;
			console.log(`Unit ${unitId}: Using existing charger ${data.components.charger.existing_model_id}`);
		} else {
			// No explicit choice - default to keep existing
			data.components.charger.action = 'use_existing'; // Change from 'keep' to 'use_existing' for clarity
			data.components.charger.new_inventory_attachment_id = data.components.charger.existing_model_id; // Set the existing ID
			data.components.charger.keep_existing = true;
			console.log(`Unit ${unitId}: Default behavior - keeping existing charger ${data.components.charger.existing_model_id}`);
		}
	} else {
		// Unit doesn't have existing charger - untuk unit Electric, charger tetap opsional
		if (chargerPick?.value) {
			// User memilih charger baru
			data.components.charger.action = 'assign';
			data.components.charger.new_inventory_attachment_id = chargerPick.value;
			data.components.charger.keep_existing = false;
			console.log(`Unit ${unitId}: Assigning new charger ${chargerPick.value}`);
		} else {
			// Tidak ada existing charger dan user tidak memilih charger baru = skip charger
			data.components.charger.action = 'skip';
			data.components.charger.new_inventory_attachment_id = null;
			data.components.charger.keep_existing = false;
			console.log(`Unit ${unitId}: No charger required - skipping charger assignment`);
		}
	}

	// Determine attachment action using new explicit approach
	const attachmentPick = document.getElementById(`attachmentPick${suffix}`);
	const attachmentAction = document.getElementById(`attachmentAction${suffix}`)?.value;
	
	if (data.components.attachment.existing_model_id) {
		// Unit has existing attachment
		if (attachmentAction === 'replace' && attachmentPick?.value) {
			// User explicitly chose to replace and selected new attachment
			data.components.attachment.action = 'replace';
			data.components.attachment.new_inventory_attachment_id = attachmentPick.value;
			data.components.attachment.keep_existing = false;
		} else if (attachmentAction === 'use_existing' || document.getElementById(`useExistingAttachment${suffix}`)?.checked) {
			// User explicitly chose to use existing attachment OR the "use existing" checkbox is checked (default)
			data.components.attachment.action = 'use_existing';
			data.components.attachment.keep_existing = true;
		} else {
			// No explicit choice - default to use existing
			data.components.attachment.action = 'use_existing';
			data.components.attachment.keep_existing = true;
		}
	} else {
		// Unit doesn't have attachment - assign if selected
		if (attachmentPick?.value) {
			data.components.attachment.action = 'assign';
			data.components.attachment.new_inventory_attachment_id = attachmentPick.value;
			data.components.attachment.keep_existing = false;
		}
	}

	return data;
}

/**
 * Collect component data for submission dengan action type (Legacy - Single Unit)
 */
function collectEnhancedComponentData() {
	const unitId = document.getElementById('approvalUnitPick')?.value;
	
	if (!unitId) return null;
	
	const data = {
		unit_id: unitId,
		components: {
			battery: {
				action: 'keep', // keep, replace, assign
				existing_model_id: document.getElementById('existingBatteryModelId')?.value,
				existing_sn: document.getElementById('existingBatterySn')?.value,
				new_inventory_attachment_id: null,
				keep_existing: document.getElementById('keepExistingBattery')?.value === 'true'
			},
			charger: {
				action: 'keep',
				existing_model_id: document.getElementById('existingChargerModelId')?.value,
				existing_sn: document.getElementById('existingChargerSn')?.value,
				new_inventory_attachment_id: null,
				keep_existing: document.getElementById('keepExistingCharger')?.value === 'true'
			}
		}
	};

	// Determine battery action
	const batteryPick = document.getElementById('batteryPick');
	const keepExistingBattery = document.getElementById('keepExistingBattery')?.value === 'true';
	
	if (batteryPick?.value && !keepExistingBattery) {
		// User selected new battery and chose to replace existing
		data.components.battery.action = data.components.battery.existing_model_id ? 'replace' : 'assign';
		data.components.battery.new_inventory_attachment_id = batteryPick.value;
	} else if (batteryPick?.value && !data.components.battery.existing_model_id) {
		// User selected battery for unit that doesn't have one
		data.components.battery.action = 'assign';
		data.components.battery.new_inventory_attachment_id = batteryPick.value;
	}

	// Determine charger action
	const chargerPick = document.getElementById('chargerPick');
	const keepExistingCharger = document.getElementById('keepExistingCharger')?.value === 'true';
	
	if (chargerPick?.value && !keepExistingCharger) {
		// User selected new charger and chose to replace existing
		data.components.charger.action = data.components.charger.existing_model_id ? 'replace' : 'assign';
		data.components.charger.new_inventory_attachment_id = chargerPick.value;
	} else if (chargerPick?.value && !data.components.charger.existing_model_id) {
		// User selected charger for unit that doesn't have one
		data.components.charger.action = 'assign';
		data.components.charger.new_inventory_attachment_id = chargerPick.value;
	}

	return data;
}

// ===== END ENHANCEMENT =====
</script>

<script>
async function openFabrikasiModal(detail) {
  const depId = Number((detail.spec?.departemen_id)||0);
  const attId = Number((detail.spec?.attachment_model_id)||0) || Number((detail.spec?.attachment_id)||0);
  // fetch attachments available
  const att = attId ? await fetch(`<?= base_url('warehouse/inventory/available-attachments') ?>?attachment_id=${attId}`).then(r=>r.json()) : [];
  const chg = depId===2 ? await fetch(`<?= base_url('warehouse/inventory/available-chargers') ?>`).then(r=>r.json()) : [];

  const attOptions = att.map(r=>{
    const name = `${r.tipe||'-'} ${r.merk||''} ${r.model||''}`.trim();
    return `<option value="${r.id_inventory_attachment}">${name} • SN: ${r.sn_attachment||'-'}</option>`;
  }).join('');
  const chgOptions = chg.map(r=>{
    const name = `${r.merk_charger||'-'} ${r.tipe_charger||''}`.trim();
    return `<option value="${r.id_inventory_attachment}">${name} • SN: ${r.sn_charger||'-'}</option>`;
  }).join('');

  const html = `
    <form id="fabForm">
      <input type="hidden" name="spk_id" value="${detail.id}">
      <input type="hidden" name="unit_id" value="${detail.persiapan_unit_id||''}">
      <div class="mb-2">
        <label class="form-label">Attachment (inventory)</label>
        <select class="form-select" name="attachment_inventory_id" required>
          <option value="">-- select --</option>${attOptions}
        </select>
      </div>
      ${depId===2 ? `
      <div class="mb-2">
        <label class="form-label">Charger (inventory)</label>
        <select class="form-select" name="charger_inventory_attachment_id" required>
          <option value="">-- select --</option>${chgOptions}
        </select>
      </div>`:''}
      <div class="mb-2">
        <label class="form-label">Mechanic</label>
        <input class="form-control" name="mekanik" required>
      </div>
    </form>
  `;
  // tampilkan modal sendiri (sesuaikan komponen yang ada)
  const modal = new bootstrap.Modal(document.createElement('div'));
  modal._element.classList.add('modal', 'fade');
  modal._element.innerHTML = `
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Fabrication - SPK #${detail.nomor_spk}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          ${html}
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="submitFabrikasi()">Save & Approve Fabrication</button>
        </div>
      </div>
    </div>
  `;
  document.body.appendChild(modal._element);
  modal.show();
  
  window.submitFabrikasi = async ()=>{
    const fd = new FormData(document.getElementById('fabForm'));
    const res = await fetch('<?= base_url('service/spk/approve-fabrikasi') ?>', {method:'POST', body: fd, headers: {'X-Requested-With':'XMLHttpRequest'}});
    const j = await res.json();
    if (!res.ok || !j.success) { alert(j.message || 'Gagal'); return; }
    location.reload();
  };
}
</script>

<script>
// Global error handler untuk debugging
window.addEventListener('error', function(e) {
	console.log('Global error:', e.error);
	if (e.error && e.error.stack) {
	console.log('Stack:', e.error.stack);
	} else {
		console.log('No stack trace available');
	}
});

// Debug untuk API data fetching
console.log('SPK Service: Component management initialized');

/**
 * Validasi duplikasi untuk mencegah pemilihan attachment/battery/charger yang sama
 * pada unit yang berbeda
 */
function validateDuplicateSelection(selectElement, type) {
	const selectedValue = selectElement.value;
	if (!selectedValue) return true; // Allow empty selection
	
	const currentFormId = selectElement.closest('.assignment-form')?.id || selectElement.closest('[id*="unitDetail"]')?.id;
	let conflictingSuffix = null;
	let conflictingUnitLabel = null;
	
	// Get all select elements of the same type across all forms
	const allSelects = document.querySelectorAll(`select[name*="${type}_id"], select[id*="${type}Pick"]`);
	
	allSelects.forEach(otherSelect => {
		// Skip if it's the same element
		if (otherSelect === selectElement) return;
		
		// Check if other select has the same value
		if (otherSelect.value === selectedValue) {
			const otherFormId = otherSelect.closest('.assignment-form')?.id || otherSelect.closest('[id*="unitDetail"]')?.id;
			
			// If different forms/units, we have a conflict
			if (otherFormId !== currentFormId) {
				// Extract suffix to identify unit
				const suffixMatch = otherFormId?.match(/unitDetail(\d+)/);
				conflictingSuffix = suffixMatch ? suffixMatch[1] : 'lain';
				
				// Try to get unit label from the form
				const unitContainer = otherSelect.closest('[id*="unitDetail"]');
				const unitLabelElement = unitContainer?.querySelector('.unit-label, h6, .card-title');
				conflictingUnitLabel = unitLabelElement?.textContent?.trim() || `Unit ${conflictingSuffix}`;
			}
		}
	});
	
	if (conflictingSuffix) {
		// Show alert and clear selection
		const typeLabel = type === 'attachment' ? 'Attachment' : 
		                 type === 'battery' ? 'Battery' : 'Charger';
		
		Swal.fire({
			icon: 'warning',
			title: 'Duplicate Detected!',
			html: `<p><strong>${typeLabel}</strong> that you selected is already used in <strong>${conflictingUnitLabel}</strong>.</p>
			       <p>Please select a different ${typeLabel.toLowerCase()}.</p>`,
			confirmButtonText: 'OK',
			confirmButtonColor: '#f39c12'
		});
		
		// Clear the selection
		selectElement.value = '';
		return false;
	}
	
	return true;
}

/**
 * Update dropdown options dengan indikator item yang sudah dipilih
 */
function updateDropdownAvailability(selectElement, type) {
	// Get all selected values of this type across all forms
	const allSelects = document.querySelectorAll(`select[name*="${type}_id"], select[id*="${type}Pick"]`);
	const selectedValues = [];
	
	allSelects.forEach(select => {
		if (select.value && select !== selectElement) {
			selectedValues.push(select.value);
		}
	});
	
	// Update options to show which items are already selected
	const options = selectElement.querySelectorAll('option');
	options.forEach(option => {
		if (option.value && selectedValues.includes(option.value)) {
			if (!option.textContent.includes('(Already selected)')) {
				option.textContent += ' (Already selected)';
				option.style.color = '#6c757d';
				option.style.fontStyle = 'italic';
			}
		} else {
			// Remove "(Already selected)" if it exists
			option.textContent = option.textContent.replace(' (Already selected)', '');
			option.style.color = '';
			option.style.fontStyle = '';
		}
	});
}

/**
 * Attach event listeners untuk validasi duplikasi
 */
function attachDuplicateValidationListeners() {
	// Use event delegation for dynamically created selects
	document.addEventListener('change', function(e) {
		const target = e.target;
		
		// Check if it's an attachment/battery/charger select
		if (target.matches('select[name*="attachment_id"], select[id*="attachmentPick"]')) {
			if (!validateDuplicateSelection(target, 'attachment')) return;
			updateAllDropdownAvailability('attachment');
		}
		else if (target.matches('select[name*="battery_id"], select[id*="batteryPick"]')) {
			if (!validateDuplicateSelection(target, 'battery')) return;
			updateAllDropdownAvailability('battery');
		}
		else if (target.matches('select[name*="charger_id"], select[id*="chargerPick"]')) {
			if (!validateDuplicateSelection(target, 'charger')) return;
			updateAllDropdownAvailability('charger');
		}
	});
	
	console.log('Duplicate validation listeners attached');
}

/**
 * Update availability indicators for all dropdowns of a specific type
 */
function updateAllDropdownAvailability(type) {
	const allSelects = document.querySelectorAll(`select[name*="${type}_id"], select[id*="${type}Pick"]`);
	allSelects.forEach(select => {
		updateDropdownAvailability(select, type);
	});
}

// Initialize duplicate validation when document is ready
document.addEventListener('DOMContentLoaded', function() {
	attachDuplicateValidationListeners();
});

// Also initialize when SPK modal is opened
document.addEventListener('shown.bs.modal', function(e) {
	if (e.target.id === 'detailSPKModal') {
		setTimeout(() => {
			attachDuplicateValidationListeners();
		}, 500);
	}
});

/**
 * Handle departmental rules for GASOLINE/DIESEL units
 * These units should not have battery or charger components
 */
function handleDepartmentalRules(departmentName, unitId, suffix) {
	console.log(`Applying departmental rules for ${departmentName} unit ${unitId}`);
	
	const isGasoline = departmentName === 'GASOLINE';
	const isDiesel = departmentName === 'DIESEL';
	const isNonElectric = isGasoline || isDiesel;
	
	if (isNonElectric) {
		// Auto-uncheck and disable battery/charger for GASOLINE/DIESEL units
		const batteryCheckbox = document.getElementById(`useExistingBattery${suffix}`);
		const chargerCheckbox = document.getElementById(`useExistingCharger${suffix}`);
		const batterySelect = document.getElementById(`batteryPick${suffix}`);
		const chargerSelect = document.getElementById(`chargerPick${suffix}`);
		
		// Uncheck existing components
		if (batteryCheckbox) {
			batteryCheckbox.checked = false;
			batteryCheckbox.disabled = true;
			batteryCheckbox.parentElement.style.opacity = '0.5';
		}
		
		if (chargerCheckbox) {
			chargerCheckbox.checked = false;
			chargerCheckbox.disabled = true;
			chargerCheckbox.parentElement.style.opacity = '0.5';
		}
		
		// Disable selection dropdowns
		if (batterySelect) {
			batterySelect.disabled = true;
			batterySelect.value = '';
			batterySelect.style.opacity = '0.5';
		}
		
		if (chargerSelect) {
			chargerSelect.disabled = true;
			chargerSelect.value = '';
			chargerSelect.style.opacity = '0.5';
		}
		
		// Auto-check replace options to trigger detachment
		const replaceBatteryCheckbox = document.getElementById(`replaceBattery${suffix}`);
		const replaceChargerCheckbox = document.getElementById(`replaceCharger${suffix}`);
		
		if (replaceBatteryCheckbox) {
			replaceBatteryCheckbox.checked = true;
			replaceBatteryCheckbox.disabled = true;
			
			// Don't select replacement - this will detach existing battery
			const replaceBatterySelect = document.getElementById(`batteryPick${suffix}`);
			if (replaceBatterySelect) {
				replaceBatterySelect.value = '';
				replaceBatterySelect.disabled = true;
			}
		}
		
		if (replaceChargerCheckbox) {
			replaceChargerCheckbox.checked = true;
			replaceChargerCheckbox.disabled = true;
			
			// Don't select replacement - this will detach existing charger
			const replaceChargerSelect = document.getElementById(`chargerPick${suffix}`);
			if (replaceChargerSelect) {
				replaceChargerSelect.value = '';
				replaceChargerSelect.disabled = true;
			}
		}
		
		// Show info message
		const electricFields = document.getElementById(`electricFields${suffix}`);
		if (electricFields) {
			const warningMessage = `
				<div class="alert alert-warning">
					<i class="fas fa-exclamation-triangle me-2"></i>
					<strong>Dept Rules ${departmentName}:</strong><br>
					Unit ${departmentName} does not require a battery or charger.<br>
					Electric components installed will be automatically detached from this unit.
				</div>
			`;
			
			// Prepend warning to existing content
			electricFields.innerHTML = warningMessage + electricFields.innerHTML;
		}
		
		console.log(`Applied departmental rules: disabled battery/charger for ${departmentName} unit`);
	}
}

/**
 * Apply departmental rules when unit is selected
 * Should be called after component UI is generated
 */
function applyDepartmentalRulesAfterUIGeneration(unitData, suffix) {
	console.log('DEBUG: applyDepartmentalRulesAfterUIGeneration called with:', unitData, suffix);
	
	if (unitData && unitData.departement_name) {
		console.log('DEBUG: Applying departmental rules for department:', unitData.departement_name);
		
		// Small delay to ensure UI is fully rendered
		setTimeout(() => {
			handleDepartmentalRules(unitData.departement_name, unitData.unit_id, suffix);
		}, 100);
	} else {
		console.log('DEBUG: No department name found, skipping departmental rules');
	}
}
</script>

        </div>
    </div>
    
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
