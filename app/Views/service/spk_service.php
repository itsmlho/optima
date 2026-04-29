<?= $this->extend('layouts/base') ?>

<?php
/**
 * SPK Service Module
 *
 * BADGE SYSTEM: Menggunakan Optima Badge Standards (optima-pro.css)
 * Quick Reference: QUOTATION → badge-soft-purple, CONTRACT → badge-soft-green,
 * Ready/Complete → badge-soft-green, In Progress → badge-soft-cyan, Cancelled → badge-soft-red.
 * See optima-pro.css line ~2030 for complete badge standards.
 */
// Simple permission check
$can_view = true;
$can_create = true;
$can_edit = true;
$can_delete = true;
$can_export = true;
$departemen_list = $departemen_list ?? [];
?>

<?= $this->section('content') ?>

    <?php if (!$can_view): ?>
    <div class="alert alert-warning">
        <i class="fas fa-lock me-2"></i>
        <strong><?= lang('App.access_denied') ?>:</strong> <?= lang('Service.no_permission_view_spk') ?>. 
        <?= lang('App.contact_administrator') ?>.
    </div>
    <?php else: ?>
    
    <!-- Statistics Cards -->
    <div class="row mt-3 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-primary-soft filter-card cursor-pointer" data-filter="all">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-list-task stat-icon text-primary"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-total-spk">0</div>
                        <div class="text-muted"><?= lang('Marketing.total_spk') ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-info-soft filter-card cursor-pointer" data-filter="IN_PROGRESS">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-arrow-repeat stat-icon text-info"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-in-progress">0</div>
                        <div class="text-muted"><?= lang('Service.in_progress') ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-success-soft filter-card cursor-pointer" data-filter="READY">
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
            <div class="stat-card bg-warning-soft filter-card cursor-pointer" data-filter="COMPLETED">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-flag stat-icon text-warning"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-completed">0</div>
                        <div class="text-muted"><?= lang('Service.completed') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="card-title mb-0">
                    <i class="bi bi-file-earmark-text me-2 text-primary"></i>
                    <?= lang('App.work_orders_spk') ?>
                </h5>
                <p class="text-muted small mb-0">
                    <?= lang('Service.create_track_manage') ?>
                    <span class="ms-2 text-info">
                        <i class="bi bi-info-circle me-1"></i>
                        <small><?= lang('Service.tip_click_filter') ?></small>
                    </span>
                </p>
            </div>
        </div>
        
        <!-- Filter Tabs -->
        <ul class="nav nav-tabs mb-3" id="filterTabs">
            <li class="nav-item">
                <a class="nav-link active filter-tab" href="#" data-filter="all"><?= lang('Common.all') ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link filter-tab" href="#" data-filter="SUBMITTED"><?= lang('Service.submitted') ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link filter-tab" href="#" data-filter="IN_PROGRESS"><?= lang('Service.in_progress') ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link filter-tab" href="#" data-filter="READY"><?= lang('Service.ready') ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link filter-tab" href="#" data-filter="COMPLETED"><?= lang('Service.completed') ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link filter-tab" href="#" data-filter="CANCELLED"><?= lang('Service.cancelled') ?></a>
            </li>
            <li class="nav-item ms-auto">
                <select class="form-select form-select-sm" id="globalDeptFilter" style="width:160px;margin-top:4px;">
                    <option value="">Semua Departemen</option>
                    <?php foreach ($departemen_list as $dept): ?>
                    <option value="<?= esc($dept['id_departemen']) ?>"><?= esc($dept['nama_departemen']) ?></option>
                    <?php endforeach; ?>
                </select>
            </li>
        </ul>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-manual-sort <?= !$can_view ? 'table-disabled' : '' ?>" id="spkList">
                    <thead class="table-light">
                        <tr>
                            <th>SPK No.</th>
                            <th><?= lang('Common.type') ?></th>
                            <th>Source</th>
                            <th><?= lang('Service.company_name') ?></th>
                            <th>Person In Charge (PIC)</th>
                            <th><?= lang('Common.status') ?></th>
                            <th><?= lang('Service.total_units') ?></th>
                            <th data-no-sort><?= lang('Common.action') ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
	</div>

	<!-- Assign Items Modal (Unit + Attachment) -->
	<div class="modal fade" id="assignItemsModal" tabindex="-1">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header"><h6 class="modal-title"><?= lang('Service.select_unit_attachment') ?></h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
				<form id="assignItemsForm">
					<div class="modal-body">
						<input type="hidden" name="spk_id" id="assignSpkId">
						<div class="mb-2">
							<label class="form-label"><?= lang('Service.select_unit_asset') ?></label>
							<input type="text" class="form-control d-none" id="unitSearch" placeholder="<?= lang('Service.search_unit_serial') ?>" autocomplete="off" tabindex="-1" aria-hidden="true">
							<select class="form-select mt-2" id="unitPick" name="unit_id"></select>
							<div class="form-text">Cari di dropdown unit (tampilan standar inventory).</div>
						</div>
						<div class="mb-2">
							<label class="form-label"><?= lang('Service.select_attachment_optional') ?></label>
							<input type="text" class="form-control" id="attSearch" placeholder="<?= lang('Service.search_attachment') ?>" autocomplete="off">
							<select class="form-select mt-2" id="attPick" name="inventory_attachment_id"></select>
							<div class="form-text">Data from inventory_attachment (status 7/8).</div>
						</div>
					</div>
					<div class="modal-footer"><button class="btn btn-secondary" type="button" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button><button class="btn btn-primary" type="submit"><?= lang('Service.save_mark_ready') ?></button></div>
				</form>
			</div>
		</div>
	</div>

	<!-- Approval Stage Modal -->
	<div class="modal fade" id="approvalStageModal" tabindex="-1">
		<div class="modal-dialog modal-lg modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h6 class="modal-title">Approval Confirmation - <span id="approvalStageTitle"></span></h6>
					<button class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<form id="approvalStageForm">
						<!-- Multi-mechanic selection -->
						<div id="mechanicSelectionContainer" class="mb-3">
							<!-- Multi-select dropdown will be initialized here -->
							<div class="text-muted"><?= lang('Service.loading_mechanic') ?></div>
						</div>
						<div class="row">
							<div class="col-6">
								<label class="form-label"><?= lang('Service.estimated_start') ?> <span class="text-danger">*</span></label>
								<input type="date" class="form-control" id="approvalEstimasiMulai" name="estimasi_mulai" required>
							</div>
							<div class="col-6">
								<label class="form-label"><?= lang('Service.estimated_completion') ?> <span class="text-danger">*</span></label>
								<input type="date" class="form-control" id="approvalEstimasiSelesai" name="estimasi_selesai" required>
							</div>
						</div>

						<!-- Stage-specific content -->
						<div id="stageSpecificContent"></div>

						<div class="form-text mt-2">
							<small>This data will appear in the SPK PDF as the estimated work timeline and approval signature.</small>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button class="btn btn-secondary" type="button" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
					<button class="btn btn-success" type="submit" form="approvalStageForm"><?= lang('Service.approve_save') ?></button>
				</div>
			</div>
		</div>
	</div>

	<!-- Detail Modal -->
	<div class="modal fade" id="spkDetailModal" tabindex="-1">
		<div class="modal-dialog modal-lg modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h6 class="modal-title"><?= lang('Service.detail_spk') ?> <span id="spkNumberHeader" class="text-primary"></span></h6>
					<button class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<div id="spkDetailBody">
						<p class="text-muted"><?= lang('Service.detail_spk_loading') ?></p>
					</div>
				</div>
				<div class="modal-footer">
					<div id="modalActionButtons">
						<!-- Buttons will be populated based on status -->
					</div>
					<button class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
				</div>
			</div>
		</div>
	</div>

	<!-- Edit Modal -->
	<div class="modal fade" id="rollbackModal" tabindex="-1">
		<div class="modal-dialog modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header bg-primary text-white">
					<h6 class="modal-title">
						<i class="fas fa-edit me-2"></i><?= lang('Service.edit_stage') ?> - <span id="rollbackStageTitle"></span>
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
					
					<div id="unitChangeSection" class="d-none">
						<div class="mb-3">
							<label class="form-label">Pilih Unit yang Akan Diubah <span class="text-danger">*</span></label>
							<select class="form-select mb-3" id="rollbackUnitIndexSelect" name="unit_index">
								<option value="">-- Pilih Unit yang Akan Diubah --</option>
							</select>
							<div class="mb-3">
								<label class="form-label">Pilih Unit Baru <span class="text-danger">*</span></label>
								<input type="text" class="form-control mb-2 d-none" id="rollbackUnitSearch" placeholder="Cari unit berdasarkan merk/model/SN..." autocomplete="off" tabindex="-1" aria-hidden="true">
								<select class="form-select" id="rollbackUnitSelect" name="unit_id">
									<option value="">-- Pilih Unit Baru --</option>
								</select>
								<div class="form-text">Hanya unit dengan status AVAILABLE yang dapat dipilih.</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
					<button type="button" class="btn btn-primary" id="confirmRollbackBtn">
						<i class="fas fa-save me-1"></i><?= lang('Common.save') ?>
					</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Input Sparepart Modal -->
	<div class="modal fade" id="spkInputSparepartModal" tabindex="-1">
		<div class="modal-dialog modal-xl modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><i class="fas fa-toolbox me-2 text-primary"></i>Items Brought (Spareparts &amp; Tools) – SPK <span id="inputSparepartSpkNumber" class="fw-bold text-primary"></span></h5>
					<button class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<input type="hidden" id="inputSparepartSpkId" value="">

					<!-- History sparepart tersimpan — collapsible, di atas form -->
					<div id="existingSparepartHistory" style="display:none" class="mb-3">
						<div class="card border-0 shadow-sm">
							<div class="card-header py-2 d-flex justify-content-between align-items-center"
								 style="background:linear-gradient(90deg,#e8f5e9,#f1f8ff);cursor:pointer"
								 data-bs-toggle="collapse" data-bs-target="#existingSparepartHistoryCollapse">
								<span class="fw-semibold small">
									<i class="fas fa-clipboard-list me-1 text-success"></i>
									Sparepart Tersimpan
									<span id="existingSparepartCount" class="badge badge-soft-blue ms-1"></span>
								</span>
								<i class="fas fa-chevron-down text-muted small"></i>
							</div>
							<div class="collapse show" id="existingSparepartHistoryCollapse">
								<div id="existingSparepartHistoryBody" class="p-0" style="max-height:300px;overflow-y:auto;"></div>
							</div>
						</div>
					</div>

					<div class="row g-3 mb-3">
						<div class="col-md-6">
							<label class="form-label fw-semibold">Stage <span class="text-danger">*</span></label>
							<div class="btn-group w-100" role="group" id="inputSparepartStageGroup">
								<input type="radio" class="btn-check" name="sparepart_stage" id="stagePersiapan" value="persiapan_unit" checked>
								<label class="btn btn-sm btn-outline-secondary" for="stagePersiapan">Persiapan</label>
								<input type="radio" class="btn-check" name="sparepart_stage" id="stageFabrikasi" value="fabrikasi">
								<label class="btn btn-sm btn-outline-secondary" for="stageFabrikasi">Fabrikasi</label>
								<input type="radio" class="btn-check" name="sparepart_stage" id="stagePainting" value="painting">
								<label class="btn btn-sm btn-outline-secondary" for="stagePainting">Painting</label>
								<input type="radio" class="btn-check" name="sparepart_stage" id="stagePdi" value="pdi">
								<label class="btn btn-sm btn-outline-secondary" for="stagePdi">PDI</label>
							</div>
						</div>
						<div class="col-md-6">
							<label class="form-label fw-semibold">Catatan Umum</label>
							<textarea class="form-control form-control-sm" id="inputSparepartNotes" rows="1" placeholder="Catatan opsional untuk seluruh daftar..."></textarea>
						</div>
					</div>
					<div class="card shadow-sm mb-0">
						<div class="card-header">
							<h6 class="mb-0"><i class="fas fa-toolbox me-2"></i>Items Brought (Spareparts &amp; Tools)</h6>
						</div>
						<div class="card-body p-2">
							<div class="table-responsive">
								<table class="table table-striped table-hover table-sm" id="sparepartInputTable" style="table-layout: fixed; width: 100%;">
									<thead class="table-light">
										<tr>
											<th style="width: 100px;">Type <span class="text-danger">*</span></th>
											<th style="width: 280px;">Item Name <span class="text-danger">*</span></th>
											<th style="width: 70px;">Qty <span class="text-danger">*</span></th>
											<th style="width: 90px;">Unit <span class="text-danger">*</span></th>
											<th style="width: 110px;">Source <span class="text-danger">*</span></th>
											<th style="width: auto;">Notes / Kanibal</th>
											<th style="width: 50px;">Del</th>
										</tr>
									</thead>
									<tbody id="sparepartInputRows"></tbody>
								</table>
							</div>
							<div class="mt-2">
								<button type="button" class="btn btn-success btn-sm" id="btnAddSparepartRow">
									<i class="fas fa-plus"></i> Add Item
								</button>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer d-flex justify-content-between">
					<small class="text-muted"><i class="fas fa-info-circle me-1"></i>Fields dengan <span class="text-danger">*</span> wajib diisi</small>
					<div class="d-flex gap-2">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
						<button type="button" class="btn btn-primary" id="btnSubmitSparepartInput"><i class="fas fa-save me-1"></i>Simpan Sparepart</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Sparepart Validation Modal (shown after PDI approval) -->
	<div class="modal fade" id="spkSparepartValidationModal" tabindex="-1">
		<div class="modal-dialog modal-xl modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header" style="background: linear-gradient(135deg, #1a7a4a 0%, #28a745 100%);">
					<h5 class="modal-title text-white"><i class="fas fa-clipboard-check me-2"></i>Validasi Sparepart – SPK <span id="validationSpkNumber" class="fw-bold"></span></h5>
					<button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<input type="hidden" id="validationSpkId" value="">
					<div class="alert alert-warning py-2 mb-3">
						<i class="fas fa-info-circle me-1"></i>
						<strong>Validasi Pemakaian:</strong> Isi jumlah yang benar-benar digunakan. Sisa otomatis menjadi permintaan pengembalian ke Warehouse.
					</div>
					<div class="mb-3">
						<label class="form-label fw-semibold">Catatan Validasi</label>
						<textarea class="form-control form-control-sm" id="validationNotes" rows="2" placeholder="Catatan pengembalian (opsional)..."></textarea>
					</div>

					<!-- Sparepart Dibawa (validation table) -->
					<div class="card mb-3">
						<div class="card-header py-2">
							<h6 class="mb-0"><i class="fas fa-list-check me-2"></i>Validasi Sparepart yang Dibawa</h6>
						</div>
						<div class="card-body p-0">
							<div class="table-responsive">
								<table class="table table-sm table-bordered mb-0" id="sparepartValidationTable">
									<thead class="table-light">
										<tr>
											<th style="width: 40px;">No</th>
											<th>Type</th>
											<th>Item</th>
											<th style="width: 90px;" class="text-center">Dibawa</th>
											<th style="width: 110px;" class="text-center">Digunakan</th>
											<th style="width: 90px;" class="text-center">Kembali</th>
											<th style="width: 130px;">Unit</th>
											<th style="width: 80px;">Satuan</th>
											<th style="width: 110px;">Status</th>
										</tr>
									</thead>
									<tbody id="sparepartValidationRows">
										<tr><td colspan="9" class="text-center text-muted py-3"><i class="fas fa-spinner fa-spin me-2"></i>Memuat data...</td></tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<!-- Sparepart Tambahan -->
					<div class="card">
						<div class="card-header py-2 d-flex justify-content-between align-items-center">
							<h6 class="mb-0"><i class="fas fa-plus-square me-2 text-success"></i>Sparepart Tambahan <small class="text-muted fw-normal">(tidak direncanakan)</small></h6>
							<button type="button" class="btn btn-sm btn-success" id="btnAddAdditionalSparepartSpk">
								<i class="fas fa-plus me-1"></i>Tambah Baris
							</button>
						</div>
						<div class="card-body p-0">
							<table class="table table-sm table-bordered mb-0" style="table-layout:fixed;width:100%;">
								<colgroup>
									<col style="width:88px;">
									<col>
									<col style="width:56px;">
									<col style="width:76px;">
									<col style="width:92px;">
									<col style="width:125px;">
									<col style="width:46px;">
								</colgroup>
								<thead class="table-light">
									<tr>
										<th class="text-center">Tipe</th>
										<th>Nama Item</th>
										<th class="text-center">Qty</th>
										<th>Satuan</th>
										<th class="text-center">Source</th>
										<th>Catatan / Kanibal</th>
										<th class="text-center">Aksi</th>
									</tr>
								</thead>
								<tbody id="additionalSparepartRowsSpk">
									<tr id="noAdditionalSpkRow">
										<td colspan="7" class="text-center text-muted py-2"><small>Belum ada sparepart tambahan. Klik "Tambah Baris" untuk menambah.</small></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer d-flex justify-content-between">
					<small class="text-muted"><i class="fas fa-info-circle me-1"></i>Kembali = Dibawa − Digunakan (akan dikirim ke Warehouse)</small>
					<div class="d-flex gap-2">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
						<button type="button" class="btn btn-success" id="saveSparepartValidationBtn"><i class="fas fa-check me-1"></i>Validasi &amp; Simpan</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
/* btn-xs for sparepart management panel */
.btn-xs { padding: 1px 5px; font-size: 11px; line-height: 1.5; border-radius: 3px; }
</style>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<!-- Select2 CSS/JS sudah dimuat di base layout -->
<script src="<?= base_url('assets/js/spk-mechanic-multiselect.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/spk-mechanic-multiselect.js') ?>"></script>
<?= $this->include('partials/accessory_js') ?>

<script>
// Global variables
let spkTable; // DataTable instance
let currentFilter = 'all';

/**
 * Fetch unit component data dari inventory_attachment untuk check existing components
 * Defined here (before DOMContentLoaded) so it's available when Select2 auto-triggers change during setup.
 */
async function fetchUnitComponentData(unitId) {
	try {
		const apiUrl = '<?= base_url('warehouse/inventory/unit-components') ?>?unit_id=' + unitId;
		const response = await fetch(apiUrl, {
			method: 'GET',
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
				'Content-Type': 'application/json'
			}
		});
		if (!response.ok) {
			throw new Error(`HTTP ${response.status}: ${response.statusText}`);
		}
		const data = await response.json();
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

// Unified notifier (fallbacks)
function notify(msg, type = 'success') {
	if (window.OptimaNotify && typeof OptimaNotify[type] === 'function') {
		return OptimaNotify[type](msg);
	}
	if (window.OptimaPro && typeof OptimaPro.showNotification === 'function') return OptimaPro.showNotification(msg, type);
	if (typeof showNotification === 'function') return showNotification(msg, type);
	// Final fallback: browser alert (should be rarely hit)
	window.alert(msg);
}

document.addEventListener('DOMContentLoaded', () => {
	
	// Load statistics
	function loadStatistics() {
		fetch('<?= base_url('service/spk/stats') ?>')
			.then(r => r.json())
			.then(data => {
				if (data.success) {
					const stats = data.stats;
					const totalEl = document.getElementById('stat-total-spk');
					const inProgressEl = document.getElementById('stat-in-progress');
					const readyEl = document.getElementById('stat-ready');
					const completedEl = document.getElementById('stat-completed');
					
					if (totalEl) totalEl.textContent = stats.total || 0;
					if (inProgressEl) inProgressEl.textContent = stats.in_progress || 0;
					if (readyEl) readyEl.textContent = stats.ready || 0;
					if (completedEl) completedEl.textContent = stats.completed || 0;
				}
			})
			.catch(e => console.error('Failed to load statistics:', e));
	}
	
	// Initialize DataTable for SPK list
	try {
		spkTable = OptimaDataTable.init('#spkList', {
			ajax: {
				url: '<?= base_url('service/spk/data') ?>',
				type: 'POST',
				data: function(d) {
					d.status_filter = currentFilter;
					d.dept_filter   = $('#globalDeptFilter').val();
					return d;
				},
				error: function(xhr) {
					console.error('❌ SPK DataTable error:', xhr.responseText);
					notify('Failed to load SPK data', 'error');
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
						return `<a href="#" onclick="openDetail(${row.id}, ${row.has_spareparts ? 'true' : 'false'});return false;">${data}</a>`;
					}
				},
				{ 
					data: 'jenis_spk',
					render: function(data) {
						const label = (data || 'UNIT').toUpperCase();
						const map = {
							'UNIT': 'badge-soft-blue',
							'ATTACHMENT': 'badge-soft-purple',
							'SERVICE': 'badge-soft-green'
						};
						const cls = map[label] || 'badge-soft-gray';
						return `<span class="badge ${cls}">${label}</span>`;
					}
				},
				{ 
					data: 'kontrak_id',
					name: 'source',
					orderable: false,
					render: function(data, type, row) {
						const sourceType = !data && row.quotation_number ? 'QUOTATION' : 'CONTRACT';
						return sourceType === 'QUOTATION' 
							? '<span class="badge badge-soft-purple"><i class="fas fa-file-lines me-1"></i>QUOTATION</span>'
							: '<span class="badge badge-soft-green"><i class="fas fa-file-contract me-1"></i>CONTRACT</span>';
					}
				},
				{ data: 'pelanggan', defaultContent: '-' },
				{
					data: 'pic',
					defaultContent: '-',
					render: function(data, type, row) {
						const pic = data || '-';
						const kontak = row.kontak || '';
						return kontak
							? `${pic}<br><small class="text-muted">${kontak}</small>`
							: pic;
					}
				},
				{ 
					data: 'status',
					render: function(data) {
						const statusMap = {
							'SUBMITTED': 'badge-soft-gray',
							'IN_PROGRESS': 'badge-soft-cyan',
							'READY': 'badge-soft-green',
							'COMPLETED': 'badge-soft-blue',
							'DELIVERED': 'badge-soft-blue',
							'CANCELLED': 'badge-soft-red'
						};
						const badgeClass = statusMap[data] || 'badge-soft-gray';
						return `<span class="badge ${badgeClass}">${data || 'N/A'}</span>`;
					}
				},
				{ data: 'jumlah_unit', defaultContent: '-' },
				{ 
					data: null,
					orderable: false,
					searchable: false,
					render: function(data, type, row) {
						let actions = '';
						
						if (row.status === 'SUBMITTED') {
							actions = `<div class="d-flex flex-column align-items-start gap-1"><button class="btn btn-sm btn-success" onclick="prosesSPK(${row.id});return false;"><i class="fas fa-play me-1"></i>Proses SPK</button><small class="text-muted">Menunggu diproses</small></div>`;
						} else if (row.status === 'IN_PROGRESS') {
							// Show approval stage buttons
							const stageStatus = row.stage_status || {};
							const unitStages = stageStatus.unit_stages || {};
							const totalUnits = parseInt(row.jumlah_unit) || 1;
							const isAttachmentSpk = (row.jenis_spk || '').toUpperCase() === 'ATTACHMENT';
							const hasFabrikasi = (row.has_fabrikasi == 1);
							const installStageName = hasFabrikasi ? 'fabrikasi' : 'install';
							const installLabel = hasFabrikasi ? 'Fabrikasi' : 'Install Attach';
							
							// Check completion status
							let persiapanDone = false;
							let fabrikasiDone = false;
							let paintingDone = false;
							let pdiDone = false;
							
							if (Object.keys(unitStages).length > 0) {
								const stageOrder = ['persiapan_unit', installStageName, 'painting', 'pdi'];
								stageOrder.forEach(stage => {
									const completedUnits = Object.keys(unitStages).filter(unitIndex => 
										unitStages[unitIndex][stage] && unitStages[unitIndex][stage].completed
									).length;
									
									if (completedUnits === totalUnits) {
										if (stage === 'persiapan_unit') persiapanDone = true;
										else if (stage === installStageName) fabrikasiDone = true;
										else if (stage === 'painting') paintingDone = true;
										else if (stage === 'pdi') pdiDone = true;
									}
								});
							} else {
								// Fallback to old structure
								persiapanDone = row.persiapan_unit_tanggal_approve ? true : false;
								fabrikasiDone = row.fabrikasi_tanggal_approve ? true : false;
								paintingDone = row.painting_tanggal_approve ? true : false;
								pdiDone = row.pdi_tanggal_approve ? true : false;
							}
							
							// Get next unit number
							const getNextUnitNumber = (stage) => {
								if (Object.keys(unitStages).length > 0 && totalUnits > 1) {
									const completedUnits = Object.keys(unitStages).filter(unitIndex => 
										unitStages[unitIndex][stage] && unitStages[unitIndex][stage].completed
									).length;
									return completedUnits + 1;
								}
								return null;
							};
							
							// Build buttons
							if (!persiapanDone && !isAttachmentSpk) {
								const nextUnit = getNextUnitNumber('persiapan_unit');
								const unitText = nextUnit ? ` #${nextUnit}` : '';
								const unitIndex = nextUnit || 1;
								actions += `<button class="btn btn-sm btn-warning" onclick="openApprovalModal('persiapan_unit', 'Unit Preparation Dept.', ${row.id}, ${unitIndex})">Unit Preparation${unitText}</button>`;
							} else if ((!fabrikasiDone && persiapanDone) || (!fabrikasiDone && isAttachmentSpk)) {
								const nextUnit = getNextUnitNumber(installStageName);
								const unitText = nextUnit ? ` #${nextUnit}` : '';
								const unitIndex = nextUnit || 1;
								actions += `<button class="btn btn-sm btn-warning" onclick="openApprovalModal('${installStageName}', 'Install Dept.', ${row.id}, ${unitIndex})">${installLabel}${unitText}</button>`;
							} else if (!paintingDone) {
								const nextUnit = getNextUnitNumber('painting');
								const unitText = nextUnit ? ` #${nextUnit}` : '';
								const unitIndex = nextUnit || 1;
								actions += `<button class="btn btn-sm btn-warning" onclick="openApprovalModal('painting', 'Painting Dept.', ${row.id}, ${unitIndex})">Painting${unitText}</button>`;
							} else if (!pdiDone) {
								const nextUnit = getNextUnitNumber('pdi');
								const unitText = nextUnit ? ` #${nextUnit}` : '';
								const unitIndex = nextUnit || 1;
								actions += `<button class="btn btn-sm btn-warning" onclick="openApprovalModal('pdi', 'PDI Dept.', ${row.id}, ${unitIndex})">PDI${unitText}</button>`;
							} else {
								actions = '<span class="badge badge-soft-green"><i class="fas fa-check-circle me-1"></i>All Stages Complete</span>';
							}
						} else if (row.status === 'READY') {
							actions = '<span class="badge badge-soft-green"><i class="fas fa-check-circle me-1"></i>Ready for DI</span>';
						} else if (row.status === 'COMPLETED' || row.status === 'DELIVERED') {
							actions = '<span class="badge badge-soft-blue"><i class="fas fa-flag-checkered me-1"></i>Completed</span>';
						}
						
						// Verifikasi Sparepart button: only after PDI done (READY status or all stages complete)
						const isPdiDone = (typeof pdiDone !== 'undefined') ? pdiDone : false;
						if (row.has_unvalidated_spareparts && (row.status === 'READY' || (row.status === 'IN_PROGRESS' && isPdiDone))) {
							const hasOtherBtn = actions.length > 0;
							if (hasOtherBtn) {
								actions += `<button class="btn btn-sm btn-outline-success" onclick="window.showSparepartValidationModal(${row.id},'${row.nomor_spk || ''}');return false;" title="Verifikasi Sparepart"><i class="fas fa-clipboard-check"></i></button>`;
							} else {
								actions += `<button class="btn btn-sm btn-outline-success" onclick="window.showSparepartValidationModal(${row.id},'${row.nomor_spk || ''}');return false;"><i class="fas fa-clipboard-check me-1"></i>Verifikasi Sparepart</button>`;
							}
						}

						// Input Sparepart: only IN_PROGRESS and PDI not yet done
						if (!row.has_spareparts && row.status === 'IN_PROGRESS' && !row.pdi_done) {
							const hasStageBtn = actions.length > 0;
							if (hasStageBtn) {
							actions += `<button class="btn btn-sm btn-outline-warning" onclick="openInputSparepart(${row.id},'${row.nomor_spk || ''}',${row.persiapan_unit_id || ''});return false;" title="Add Sparepart"><i class="fas fa-tools"></i></button>`;
						} else {
							actions = `<button class="btn btn-sm btn-outline-warning" onclick="openInputSparepart(${row.id},'${row.nomor_spk || ''}',${row.persiapan_unit_id || ''});return false;"><i class="fas fa-tools me-1"></i>Add Sparepart</button>`;
							}
						}
						
						return `<div class="d-flex gap-1 align-items-center">${actions || '<span class="text-muted">-</span>'}</div>`;
					}
				}
			],
			drawCallback: function(settings, json) {
				console.log('✅ Service SPK DataTable drawn, rows:', settings.aiDisplay.length);
				
				// Load statistics after table draw
				loadStatistics();
			}
		});
		
		console.log('✅ Service SPK DataTable initialized successfully');
		
	} catch(error) {
		console.error('❌ Failed to initialize SPK DataTable:', error);
		notify('Failed to initialize SPK table', 'error');
	}
	
	// Filter tab click listeners
	document.querySelectorAll('.filter-tab').forEach(tab => {
		tab.addEventListener('click', function(e) {
			e.preventDefault();
			const filter = this.dataset.filter;
			currentFilter = filter;
			
			// Update active tab
			document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
			this.classList.add('active');
			
			// Reload DataTable with new filter
			if (spkTable && spkTable.ajax) {
				spkTable.ajax.reload();
			}
		});
	});
	
	// Load initial statistics
	loadStatistics();

	// Global dept filter change
	document.getElementById('globalDeptFilter').addEventListener('change', function() {
		if (spkTable && spkTable.ajax) {
			spkTable.ajax.reload();
		}
	});

	// Make functions global for compatibility
	window.loadStatistics = loadStatistics;
	window.reloadSpkTable = function() {
		if (spkTable && spkTable.ajax) {
			spkTable.ajax.reload();
		}
	};

	// Proses SPK directly from action column
	window.prosesSPK = function(id) {
		confirmSwal({
			title: 'Proses SPK?',
			text: 'Status SPK akan berubah menjadi IN_PROGRESS.',
			confirmText: 'Ya, Proses',
			icon: 'question'
		}).then(function(confirmed) {
			if (!confirmed) return;
			const formData = new FormData();
			formData.append(window.csrfTokenName, window.csrfTokenValue);
			formData.append('status', 'IN_PROGRESS');
			fetch('<?= base_url('service/spk/update-status/') ?>' + id, {
				method: 'POST',
				headers: {'X-Requested-With': 'XMLHttpRequest'},
				body: formData
			}).then(r => r.json()).then(res => {
				if (res && res.success) {
					notify('SPK berhasil diproses. Status: IN_PROGRESS.', 'success');
					window.reloadSpkTable();
				} else {
					notify(res.message || 'Gagal memproses SPK', 'error');
				}
			}).catch(() => notify('Terjadi kesalahan sistem', 'error'));
		});
	};

	let currentSpkId = null;
	window.openDetail = (id, hasSpareparts = false) => {
		currentSpkId = id;
		const body = document.getElementById('spkDetailBody');
		const headerSpan = document.getElementById('spkNumberHeader');
		body.innerHTML = '<p class="text-muted">Memuat...</p>';

		fetch('<?= base_url('service/spk/detail/') ?>' + id).then(r=>{
			// Check for 401 Unauthorized (session expired)
			if (r.status === 401) {
				notify('Session expired. Please login again.', 'error');
				window.location.href = '<?= base_url('auth/login') ?>';
				return Promise.reject('Unauthorized');
			}
			if (!r.ok) {
				throw new Error(`HTTP error! Status: ${r.status}`);
			}
			return r.json();
		}).then(j=>{
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
					const hasFabrikasi2 = (d.has_fabrikasi == 1);
					const installStageName2 = hasFabrikasi2 ? 'fabrikasi' : 'install';
					const stageOrder = ['persiapan_unit', installStageName2, 'painting', 'pdi'];
					
					// Check if any unit has completed each stage
					Object.keys(unitStages).forEach(unitIndex => {
						const unitStage = unitStages[unitIndex];
						stageOrder.forEach(stage => {
							if (unitStage[stage] && unitStage[stage].completed) {
								if (stage === 'persiapan_unit') persiapanDone = true;
								else if (stage === installStageName2) fabrikasiDone = true;
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
				const hasFabrikasi3 = (d.has_fabrikasi == 1);
				const installStageName3 = hasFabrikasi3 ? 'fabrikasi' : 'install';
				const installLabel3 = hasFabrikasi3 ? 'Fabrikasi' : 'Install Attach';
				
				// Add buttons for incomplete stages
				if (!persiapanDone && !isAttachmentSpk) {
					// Normal workflow: Start with Unit Preparation (UNIT SPK only)
					approvalButtons.push('<button class="btn btn-warning btn-sm" onclick="openApprovalModal(\'persiapan_unit\', \'Unit Preparation Dept.\')">Unit Preparation</button>');
				} else if ((!fabrikasiDone && persiapanDone) || (!fabrikasiDone && isAttachmentSpk)) {
					// Next stage: Install or Fabrikasi
					approvalButtons.push(`<button class="btn btn-warning btn-sm" onclick="openApprovalModal('${installStageName3}', 'Install Dept.')">${installLabel3}</button>`);
				} else if (!paintingDone) {
					approvalButtons.push('<button class="btn btn-warning btn-sm" onclick="openApprovalModal(\'painting\', \'Painting Dept.\')">Painting</button>');
				} else if (!pdiDone) {
					approvalButtons.push('<button class="btn btn-warning btn-sm" onclick="openApprovalModal(\'pdi\', \'PDI Inspection Dept.\')">PDI Inspection</button>');
				}
				
				// Show completed stages with checkmarks (skip Unit Preparation for ATTACHMENT SPK)
				if (persiapanDone && !isAttachmentSpk) approvalButtons.push('<span class="badge badge-soft-green me-1">Unit Preparation</span>');
				if (isAttachmentSpk && !persiapanDone) approvalButtons.push('<span class="badge badge-soft-cyan me-1">Skip Unit Preparation</span>');
				if (fabrikasiDone) approvalButtons.push(`<span class="badge badge-soft-green me-1">${installLabel3}</span>`);
				if (paintingDone) approvalButtons.push('<span class="badge badge-soft-green me-1">Painting</span>');
				if (pdiDone) approvalButtons.push('<span class="badge badge-soft-green me-1">PDI</span>');
				
				// For multi-unit SPK, do not show assignment button; items are accumulated per-cycle
				const showAssign = (d.jumlah_unit||1) === 1 && pdiDone;
				
				// Add edit button if any stages are completed
				const showEdit = persiapanDone || fabrikasiDone || paintingDone || pdiDone;
				
				actionButtons = `
					<a class="btn btn-primary btn-sm" id="btnPrintPdfSvc" href="<?= base_url('service/spk/print/') ?>${id}" target="_blank" rel="noopener"><i class="fas fa-file-pdf me-1"></i>Print PDF</a>
					${hasSpareparts ? `<a class="btn btn-outline-success btn-sm" href="<?= base_url('service/spk/print-sparepart/') ?>${id}" target="_blank" rel="noopener"><i class="fas fa-print me-1"></i>Print Sparepart</a>` : ''}
					${pdiDone
							? (hasSpareparts
								? `<button class="btn btn-success btn-sm" onclick="bootstrap.Modal.getInstance(document.getElementById('spkDetailModal')).hide(); setTimeout(()=>window.showSparepartValidationModal(${id},'${d.nomor_spk}'),300);"><i class="fas fa-clipboard-check me-1"></i>Validasi Sparepart</button>`
								: '<span class="badge badge-soft-gray">Tidak ada sparepart</span>')
							: `<button class="btn btn-warning btn-sm" onclick="bootstrap.Modal.getInstance(document.getElementById('spkDetailModal')).hide(); setTimeout(()=>openInputSparepart(${id},'${d.nomor_spk}',${d.persiapan_unit_id||''}),300);"><i class="fas fa-tools me-1"></i>${hasSpareparts ? 'Tambah Sparepart' : 'Input Sparepart'}</button>`}
					${approvalButtons.join(' ')}
					${showAssign ? '<button class="btn btn-primary btn-sm" onclick="openAssign(' + id + '); bootstrap.Modal.getInstance(document.getElementById(\'spkDetailModal\')).hide();">Pilih Unit & Attachment</button>' : ''}
					${showEdit ? '<button class="btn btn-outline-primary btn-sm edit-spk-btn" data-spk-id="' + id + '" title="Edit Options"><i class="fas fa-edit me-1"></i>Edit</button>' : ''}
				`;
			} else if (status === 'READY' || status === 'DELIVERED' || status === 'COMPLETED') {
				actionButtons = `<a class="btn btn-primary btn-sm" id="btnPrintPdfSvc" href="<?= base_url('service/spk/print/') ?>${id}" target="_blank" rel="noopener"><i class="fas fa-file-pdf me-1"></i>Print PDF</a>${hasSpareparts ? ` <a class="btn btn-success btn-sm" href="<?= base_url('service/spk/print-sparepart/') ?>${id}" target="_blank" rel="noopener"><i class="fas fa-tools me-1"></i>Print Sparepart</a>` : ''}`;
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

			const formatAccessoryLabel = (value) => window.OptimaAccessory
				? window.OptimaAccessory.formatLabel(value)
				: String(value || '').trim();
			const formatAccessoryList = (list) => window.OptimaAccessory
				? window.OptimaAccessory.formatList(Array.isArray(list) ? list : [])
				: (Array.isArray(list) ? list : []);

			// Build accessories text robustly (array or JSON string)
			let aksText = '-';
			if (j.kontrak_spec && j.kontrak_spec.aksesoris) {
				const aks = j.kontrak_spec.aksesoris;
				if (Array.isArray(aks) && aks.length) aksText = formatAccessoryList(aks).join(', ');
				else if (typeof aks === 'string') {
					try { const parsed = JSON.parse(aks); aksText = Array.isArray(parsed) && parsed.length ? formatAccessoryList(parsed).join(', ') : (formatAccessoryLabel(aks.trim()) || '-'); }
					catch(e){ aksText = formatAccessoryLabel(aks.trim()) || '-'; }
				}
			} else if (s && s.aksesoris) {
				if (Array.isArray(s.aksesoris) && s.aksesoris.length) aksText = formatAccessoryList(s.aksesoris).join(', ');
				else if (typeof s.aksesoris === 'string') {
					try { const parsed = JSON.parse(s.aksesoris); aksText = Array.isArray(parsed) && parsed.length ? formatAccessoryList(parsed).join(', ') : (formatAccessoryLabel(s.aksesoris.trim()) || '-'); }
					catch(e){ aksText = formatAccessoryLabel((s.aksesoris||'').trim()) || '-'; }
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
					<div class="col-6"><strong>Source:</strong> <span style="color: ${(d.kontrak_id !== null && d.kontrak_id !== '') ? '#28a745' : '#ffc107'}; font-weight: 500;">${(d.kontrak_id !== null && d.kontrak_id !== '') ? 'From Contract' : (d.quotation_number || 'From Quotation')}</span></div>
					<div class="col-6"><strong>Company Name:</strong> ${d.pelanggan||'-'}</div>
					<div class="col-6"><strong>Location:</strong> ${d.lokasi||'-'}</div>
					<div class="col-6"><strong>Pic:</strong> ${d.pic||'-'}</div>
					<div class="col-6"><strong>Contact:</strong> ${d.kontak||'-'}</div>
					<div class="col-6"><strong>SPK Created:</strong> ${d.dibuat_pada ? (new Date(d.dibuat_pada)).toLocaleDateString('id-ID', {year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'}) : '-'}</div>
					<div class="col-6"><strong>Delivery Plan:</strong> ${d.delivery_plan||'-'}</div>
					<div class="col-12"><hr></div>
					<div class="col-12"><strong>Customer Requirements (from Marketing):</strong></div>
					<div class="col-6"><strong>Total Unit:</strong> ${d.jumlah_unit || 0}</div>
					<div class="col-6"><strong>Department:</strong> ${ks.departemen_id_name || s.departemen_id_name||'-'}</div>
					<div class="col-6"><strong>Type & Brand:</strong> ${[ks.tipe_unit_id_name || s.tipe_jenis, ks.brand_id_name || ks.merk_unit || s.merk_unit].filter(x=>x).join(' ') || '-'}</div>
					<div class="col-6"><strong>Capacity:</strong> ${ks.kapasitas_id_name || s.kapasitas_id_name||'-'}</div>
					<div class="col-6"><strong>Attachment (Type):</strong> ${ks.attachment_id_name || ks.attachment_tipe || s.attachment_tipe||'-'}</div>
					<div class="col-6"><strong>Mast:</strong> ${ks.mast_id_name || s.mast_id_name||'-'}</div>
					<div class="col-6"><strong>Tire:</strong> ${ks.ban_id_name || s.ban_id_name||'-'}</div>
					<div class="col-12"><strong>Accessories :</strong> ${aksText}</div>
					${ks.notes ? `<div class="col-12" style="background-color: #fff3cd; padding: 8px; margin-top: 8px; border-radius: 4px;"><strong><i class="fas fa-sticky-note me-2"></i>Custom Requirements:</strong><br><span style="white-space: pre-line; font-size: 12px;">${ks.notes}</span></div>` : ''}
					

					${status === 'IN_PROGRESS' || status === 'READY' || status === 'DELIVERED' || status === 'COMPLETED' ? `
					<div class="col-12"><hr></div>
					<div class="col-12"><h6 class="mb-2">📋 Status Approval Workflow</h6></div>
					${(() => {
						const totalUnits = parseInt(d.jumlah_unit) || 1;
						const stageStatus = d.stage_status || {};
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
						
						// Fabrication / Install
						const installStageKey = (d.has_fabrikasi == 1) ? 'fabrikasi' : 'install';
						const installStageTitle = (d.has_fabrikasi == 1) ? 'Fabrikasi' : 'Install Attach';
						const fabrikasi = getStageInfo(installStageKey);
						workflowHtml += `<div class="col-6">
							<strong>${d.jenis_spk === 'ATTACHMENT' ? '1' : '2'}. ${installStageTitle}:</strong><br>
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

					<div class="col-12"><hr></div>
					<div class="col-12">
						<h6 class="mb-2"><i class="fas fa-toolbox text-primary me-2"></i>Sparepart & Tools</h6>
						<div id="spkSparepartSection">
							<p class="text-muted small"><i class="fas fa-spinner fa-spin me-1"></i>Memuat data sparepart...</p>
						</div>
					</div>
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
								reloadSpkTable(); // Reload table
							} else {
								notify(result.message || 'Failed to process SPK', 'error');
							}
						});
					});
				}
			}, 100);
			
			new bootstrap.Modal(document.getElementById('spkDetailModal')).show();

			// Fetch sparepart data for this SPK
			fetch('<?= base_url('service/spk/get-spareparts/') ?>' + id, {
				headers: { 'X-Requested-With': 'XMLHttpRequest' }
			}).then(r => r.json()).then(sp => {
				const section = document.getElementById('spkSparepartSection');
				if (!section) return;
				if (!sp.success || !sp.spareparts || sp.spareparts.length === 0) {
					section.innerHTML = '<p class="text-muted small">Belum ada sparepart tercatat untuk SPK ini.</p>';
					return;
				}

				const items = sp.spareparts;
				// Summary counts
				const totalItems  = items.length;
				const whItems     = items.filter(i => parseInt(i.is_from_warehouse) === 1).length;
				const nonwhItems  = totalItems - whItems;
				const validated   = items.filter(i => parseInt(i.sparepart_validated) === 1).length;

				let html = `<div class="d-flex gap-2 mb-2 flex-wrap">
					<span class="badge badge-soft-blue">${totalItems} Items</span>
					<span class="badge badge-soft-green">WH: ${whItems}</span>
					${nonwhItems > 0 ? `<span class="badge badge-soft-yellow">Non-WH: ${nonwhItems}</span>` : ''}
					${validated > 0 ? `<span class="badge badge-soft-green">Validated: ${validated}/${totalItems}</span>` : `<span class="badge badge-soft-gray">Belum Divalidasi</span>`}
				</div>`;

				// Group by stage_name
				const stageOrder = ['persiapan_unit', 'install', 'fabrikasi', 'painting', 'pdi', null];
				const stageLabels = {
					persiapan_unit: '<i class="fas fa-wrench me-1"></i>Persiapan Unit',
					install:        '<i class="fas fa-puzzle-piece me-1"></i>Install Attach',
					fabrikasi:      '<i class="fas fa-industry me-1"></i>Fabrikasi',
					painting:       '<i class="fas fa-paint-roller me-1"></i>Painting',
					pdi:            '<i class="fas fa-clipboard-check me-1"></i>PDI',
					null:           '<i class="fas fa-question-circle me-1"></i>Tidak Ditentukan'
				};
				const stageBadgeClass = {
					persiapan_unit: 'badge-soft-blue',
					install:        'badge-soft-cyan',
					fabrikasi:      'badge-soft-purple',
					painting:       'badge-soft-orange',
					pdi:            'badge-soft-green',
					null:           'badge-soft-gray'
				};
				const grouped = {};
				items.forEach(item => {
					const key = item.stage_name || 'null';
					if (!grouped[key]) grouped[key] = [];
					grouped[key].push(item);
				});

				html += `<div class="table-responsive"><table class="table table-sm table-bordered mb-0">
					<thead class="table-light">
						<tr>
							<th style="width:60px">Type</th>
							<th style="width:70px">Source</th>
							<th>Item</th>
							<th style="width:65px" class="text-center">Bawa</th>
							<th style="width:65px" class="text-center">Pakai</th>
							<th style="width:65px" class="text-center">Kembali</th>
							<th style="width:100px">Unit</th>
							<th style="width:75px" class="text-center">Status</th>
						</tr>
					</thead><tbody>`;

				stageOrder.forEach(stage => {
					const key = stage === null ? 'null' : stage;
					if (!grouped[key] || grouped[key].length === 0) return;

					html += `<tr class="table-secondary">
						<td colspan="8" class="py-1 px-2">
							<span class="badge ${stageBadgeClass[key]} me-1">${stageLabels[key]}</span>
							<small class="text-muted">${grouped[key].length} item</small>
						</td>
					</tr>`;

					grouped[key].forEach(item => {
						const typeBadge = item.item_type === 'tool'
							? '<span class="badge badge-soft-gray" style="font-size:10px">🔧 Tool</span>'
							: '<span class="badge badge-soft-blue" style="font-size:10px">⚙ Part</span>';

						const srcType = (item.source_type || '').toUpperCase();
						let srcBadge;
						if (srcType === 'KANIBAL') {
							const kUnit = item.source_unit_no ? ` (${item.source_unit_no})` : '';
							srcBadge = `<span class="badge badge-soft-orange" style="font-size:10px">♻ Kanibal${kUnit}</span>`;
						} else if (parseInt(item.is_from_warehouse) === 0) {
							srcBadge = '<span class="badge badge-soft-yellow" style="font-size:10px">♻ Bekas</span>';
						} else {
							srcBadge = '<span class="badge badge-soft-green" style="font-size:10px">🏪 WH</span>';
						}

						const qtyUsed    = parseInt(item.quantity_used)    || 0;
						const qtyBrought = parseInt(item.quantity_brought)  || 0;
						const qtyReturn  = Math.max(0, qtyBrought - qtyUsed);
						const validBadge = parseInt(item.sparepart_validated) === 1
							? '<span class="badge badge-soft-green" style="font-size:10px">✓</span>'
							: '<span class="badge badge-soft-gray" style="font-size:10px">-</span>';
						const unitCell = item.unit_no
							? `<span class="badge badge-soft-blue" style="font-size:10px">${item.unit_no}</span>`
							: '<span class="text-muted small">-</span>';

						html += `<tr>
							<td>${typeBadge}</td>
							<td>${srcBadge}</td>
							<td><strong style="font-size:12px">${item.sparepart_name}</strong>${item.sparepart_code ? `<br><small class="text-muted">${item.sparepart_code}</small>` : ''}</td>
							<td class="text-center"><span class="badge badge-soft-cyan" style="font-size:11px">${qtyBrought}</span><br><small class="text-muted" style="font-size:10px">${item.satuan||'PCS'}</small></td>
							<td class="text-center"><span class="badge badge-soft-green" style="font-size:11px">${qtyUsed}</span></td>
							<td class="text-center">${qtyReturn > 0 ? `<span class="badge badge-soft-yellow" style="font-size:11px">${qtyReturn}</span>` : '<span class="text-muted">-</span>'}</td>
							<td>${unitCell}</td>
							<td class="text-center">${validBadge}</td>
						</tr>`;
					});
				});

				html += '</tbody></table></div>';
				section.innerHTML = html;
			}).catch(() => {
				const section = document.getElementById('spkSparepartSection');
				if (section) section.innerHTML = '<p class="text-muted small">Gagal memuat data sparepart.</p>';
			});
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
		document.getElementById('attPick').innerHTML = '<option value="">- (Opsional) -</option>';

		const $unitPick = $('#unitPick');
		if ($unitPick.hasClass('select2-hidden-accessible')) {
			$unitPick.select2('destroy');
		}
		$unitPick.empty().append($('<option value=""></option>'));
		const assignUnitUrl = '<?= base_url('service/data-unit/simple') ?>';
		if (typeof window.OptimaUnitSelect2 !== 'undefined') {
			$unitPick.select2($.extend({}, OptimaUnitSelect2.buildServiceDataUnitSimpleSelect2Config({
				url: assignUnitUrl,
				placeholder: '- Select Unit -',
				dropdownParent: $('#assignItemsModal'),
				minimumInputLength: 1,
				extraSpkRow: true,
				allowedStatuses: null,
				disableAssigned: false,
				extraAjaxParams: function () { return {}; }
			}), {
				escapeMarkup: function (markup) { return markup; }
			}));
		} else {
			$unitPick.append($('<option value="">- Select Unit -</option>'));
		}
		
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
					reloadSpkTable();
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
	/** Query string extras for service/data-unit/simple (exclude_spk_id, spk_department) */
	var getApprovalUnitSimpleExtraAjax = function () { return {}; };
	
	window.openApprovalModal = (stage, stageTitle, spkId, unitIndex = null) => {
		currentApprovalStage = stage;
		currentApprovalSpkId = spkId || currentSpkId; // Use passed spkId or fallback to currentSpkId
		console.log('🆔 SPK ID Debug:', {spkId, currentSpkId, currentApprovalSpkId});
		document.getElementById('approvalStageTitle').textContent = stageTitle;
		
		// Set unit index if provided (for multi-unit SPK editing)
		if (unitIndex !== null) {
			currentEditingUnitIndex = unitIndex;
			console.log('Setting currentEditingUnitIndex to:', unitIndex);
		}
		
		const mechanicContainer = document.getElementById('mechanicSelectionContainer');
		const approvalModalEl = document.getElementById('approvalStageModal');

		if (stage === 'install') {
			// Install: inherit mechanic from Unit Preparation, no selector needed
			if (mechanicContainer) {
				mechanicContainer.innerHTML = '<div class="alert alert-info mb-2 py-2"><i class="fas fa-users me-2"></i><strong>Tim:</strong> Diambil otomatis dari tahap Unit Preparation.</div>';
			}
			currentMechanicSelector = null;
		} else {
			// Initialize multi-mechanic selection AFTER modal is fully visible (shown.bs.modal)
			// Using event listener instead of fixed timeout to avoid race with Bootstrap's 300ms animation
			const _onShownMechanic = function() {
				approvalModalEl.removeEventListener('shown.bs.modal', _onShownMechanic);
				initializeMechanicSelection(stage);
			};
			approvalModalEl.addEventListener('shown.bs.modal', _onShownMechanic);
		}

		// Show the approval modal
		let approvalModal = bootstrap.Modal.getInstance(approvalModalEl);
		if (!approvalModal) {
			approvalModal = new bootstrap.Modal(approvalModalEl);
		}

		// Clear stale content from previous open before showing
		const stageContent = document.getElementById('stageSpecificContent');
		if (stageContent) stageContent.innerHTML = '';

		approvalModal.show();

		// Load stage-specific content (unit picker, attachment fields, etc.)
		loadStageSpecificContent(stage, currentApprovalSpkId);
	};

	// Global variable to store the current mechanic selector instance
	let currentMechanicSelector = null;
	let currentUnitDepartmentId = null; // Track selected unit's department
	
	// Initialize multi-mechanic selection based on stage (NEW: department-based filtering)
	function initializeMechanicSelection(stage, departmentId = null) {
		console.log('🔧 Initializing mechanic selection for stage:', stage, 'Department:', departmentId);
		
		// Check if container exists
		const container = document.getElementById('mechanicSelectionContainer');
		console.log('📦 Container check:', {
			exists: !!container,
			visible: container ? container.offsetHeight > 0 : false,
			display: container ? getComputedStyle(container).display : 'none'
		});
		
		if (!container) {
			console.error('❌ mechanicSelectionContainer not found in DOM');
			return;
		}
		
		// Clear previous instance
		if (currentMechanicSelector) {
			console.log('🗑️ Resetting previous selector');
			currentMechanicSelector.reset();
		}
		
		// Stage-specific configuration (UPDATED: removed allowedRoles, using departmentId)
		const stageConfig = {
			'persiapan_unit': {
				stage: 'persiapan_unit',
				maxMechanics: 2,
				maxHelpers: 2,
				placeholder: 'Select unit preparation team...'
			},
			'install': {
				stage: 'install',
				maxMechanics: 2,
				maxHelpers: 2,
				placeholder: 'Select installation team...'
			},
			'fabrikasi': {
				stage: 'fabrikasi',
				maxMechanics: 2,
				maxHelpers: 2,
				placeholder: 'Select fabrication team...'
			},
			'painting': {
				stage: 'painting',
				maxMechanics: 2,
				maxHelpers: 2,
				placeholder: 'Select painting team...'
			},
			'pdi': {
				stage: 'pdi',
				maxMechanics: 2, // For foreman/supervisor
				maxHelpers: 1,
				placeholder: 'Select PDI inspection team...'
			}
		};
		
		const config = stageConfig[stage] || stageConfig['persiapan_unit'];
		
		// Add department filter (NEW: filter by unit's department)
		if (departmentId) {
			config.departmentId = departmentId;
			currentUnitDepartmentId = departmentId;
		}
		
		// Check if the class is available
		if (typeof window.SPKMechanicMultiSelect === 'undefined') {
			console.error('❌ SPKMechanicMultiSelect class not available');
			return;
		}
		
		// Initialize the multi-select component
		currentMechanicSelector = new window.SPKMechanicMultiSelect('mechanicSelectionContainer', config);
		
		console.log('✅ Mechanic selector initialized for stage:', stage);
		
		// Remove loading message
		setTimeout(() => {
			const loadingMsg = container.querySelector('.text-muted');
			if (loadingMsg && loadingMsg.textContent.includes('Loading')) {
				loadingMsg.remove();
			}
		}, 500);
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
						<select class="form-select" id="approvalUnitPick${suffix}" name="unit_id" required>
							<option value="">- Select Unit -</option>
						</select>
						<div class="form-text">Search by unit number, serial, brand, or model.</div>
					</div>
					<div class="mb-3">
						<label class="form-label">Select Area <span class="text-danger">*</span></label>
						<select class="form-select" id="approvalAreaPick${suffix}" name="area_id" required>
							<option value="">- Select Area -</option>
						</select>
						<div class="form-text">Select an area for this unit. The area determines the assignment of foreman and mechanic for the work order.</div>
					</div>
					<div id="electricFields${suffix}" class="mb-3 d-none">
						<!-- Smart Component Management will inject content here -->
						<div id="electricFieldsContent${suffix}">
							<!-- Legacy fallback if smart detection fails -->
							<div class="alert alert-info">
								<i class="fas fa-battery-full me-2"></i>Unit Electric requires selection of Battery and Charger
							</div>
							<div class="row">
								<div class="col-12 mb-3">
									<label class="form-label">Select Battery <span class="text-danger">*</span></label>
									<select class="form-select" id="batteryPick${suffix}" name="battery_inventory_attachment_id" style="width:100%">
										<option value="">- Select Battery -</option>
									</select>
								</div>
								<div class="col-12">
									<label class="form-label">Select Charger <span class="text-danger">*</span></label>
									<select class="form-select" id="chargerPick${suffix}" name="charger_inventory_attachment_id" style="width:100%">
										<option value="">- Select Charger -</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div id="fabrikasiFields${suffix}" class="mb-3 d-none">
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
			fetch('<?= base_url('service/spk/detail/') ?>' + spkId).then(r=>{
				// Check for 401 Unauthorized (session expired)
				if (r.status === 401) {
					notify('Session expired. Please login again.', 'error');
					window.location.href = '<?= base_url('auth/login') ?>';
					return Promise.reject('Unauthorized');
				}
				if (!r.ok) {
					throw new Error(`HTTP error! Status: ${r.status}`);
				}
				return r.json();
			}).then(j=>{
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
							try { const parsed = JSON.parse(aks); if (Array.isArray(parsed)) aksesoris = parsed; else aksesoris = aks.split(',').map(v => v.trim()).filter(Boolean); }
							catch(e){ aksesoris = aks.split(',').map(v => v.trim()).filter(Boolean); }
						}
					} else if (spesifikasi.aksesoris) {
						if (Array.isArray(spesifikasi.aksesoris)) aksesoris = spesifikasi.aksesoris;
						else if (typeof spesifikasi.aksesoris === 'string' && spesifikasi.aksesoris.trim()) {
							try { const parsed = JSON.parse(spesifikasi.aksesoris); if (Array.isArray(parsed)) aksesoris = parsed; else aksesoris = spesifikasi.aksesoris.split(',').map(v => v.trim()).filter(Boolean); }
							catch(e){ aksesoris = spesifikasi.aksesoris.split(',').map(v => v.trim()).filter(Boolean); }
						}
					}
					let aksesorisCheckboxes = '';
					if (Array.isArray(aksesoris) && aksesoris.length > 0) {
						aksesorisCheckboxes = aksesoris.map(item => 
							`<div class="form-check">
								<input class="form-check-input" type="checkbox" name="aksesoris_tersedia[]" value="${item}" id="aks_${item.replace(/[^\w]+/g, '_')}">
								<label class="form-check-label" for="aks_${item.replace(/[^\w]+/g, '_')}">${window.OptimaAccessory ? window.OptimaAccessory.formatLabel(item) : item}</label>
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
			
		} else if (stage === 'fabrikasi' || stage === 'install') {
			container.innerHTML = `
				<hr>
				<div class="mb-3">
					<label class="form-label fw-semibold">Jenis Instalasi <span class="text-danger">*</span></label>
					<div class="d-flex gap-3 mt-1">
						<div class="form-check">
							<input class="form-check-input" type="radio" name="install_type" id="installTypeFork" value="fork">
							<label class="form-check-label" for="installTypeFork">
								<i class="fas fa-grip-lines me-1 text-warning"></i>Pasang Fork
							</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="radio" name="install_type" id="installTypeAttachment" value="attachment">
							<label class="form-check-label" for="installTypeAttachment">
								<i class="fas fa-puzzle-piece me-1 text-primary"></i>Pasang Attachment
							</label>
						</div>
					</div>
					<div class="form-text">Pilih jenis instalasi yang akan dilakukan pada tahap ini.</div>
				</div>
				<div class="mb-3">
					<label class="form-label">Keterangan <span class="text-muted fw-normal">(jika custom)</span></label>
					<textarea class="form-control form-control-sm" id="fabrikasiKeterangan" name="keterangan_instalasi" rows="2" placeholder="Contoh: Ukuran 1200mm, bahan khusus, modifikasi tambahan..."></textarea>
					<div class="form-text">Isi jika ada ukuran, jenis, atau modifikasi khusus. Data ini akan dicatat di keterangan attachment/fork.</div>
				</div>
				<div id="fabrikasiForkSection" class="d-none">
					<div class="mb-3" id="fabrikasiForkContent">
						<div class="text-muted">Loading fork options...</div>
					</div>
				</div>
				<div id="fabrikasiAttachmentSection" class="d-none">
					<div class="mb-3" id="fabrikasiAttachmentContent">
						<div class="text-muted">Loading attachment options...</div>
					</div>
					<div class="form-text">Data dari inventory_attachment.</div>
				</div>
			`;

			// Listen to radio change to show the relevant section
			document.querySelectorAll('input[name="install_type"]').forEach(function(radio) {
				radio.addEventListener('change', function() {
					const forkSection = document.getElementById('fabrikasiForkSection');
					const attSection  = document.getElementById('fabrikasiAttachmentSection');
					if (this.value === 'fork') {
						forkSection.classList.remove('d-none');
						attSection.classList.add('d-none');
						setupFabrikasiForkManagement();
					} else {
						attSection.classList.remove('d-none');
						forkSection.classList.add('d-none');
						setupFabrikasiAttachmentManagement();
					}
				});
			});

			// If editing, load existing fabrikasi data and pre-select install_type
			if (currentEditingUnitIndex !== null) {
				setTimeout(() => {
					loadExistingUnitData(spkId, currentEditingUnitIndex, stage);
				}, 500);
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
		
		// Group areas by area_type (CENTRAL vs MILL)
		const centralAreas = [];
		const millAreas = [];
		
		areas.forEach(area => {
			if (area.area_type === 'CENTRAL') {
				centralAreas.push(area);
			} else {
				millAreas.push(area);
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
		
		// Add Mill section
		if (millAreas.length > 0) {
			const millHeader = document.createElement('option');
			millHeader.disabled = true;
			millHeader.textContent = '─── MILL AREAS ───';
			millHeader.style.fontWeight = 'bold';
			millHeader.style.backgroundColor = '#f8f9fa';
			millHeader.style.color = '#495057';
			areaSelect.appendChild(millHeader);
			
			millAreas.forEach(area => {
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
	
	// Unit dropdown: sama dengan unit-select2.js + baris ekstra kapasitas/dept/USED IN SPK
	function formatUnitOption(unit) {
		if (!unit.id) {
			return unit.text;
		}
		if (typeof window.OptimaUnitSelect2 === 'undefined') {
			return unit.text;
		}
		return OptimaUnitSelect2.templateResult(unit, { extraSpkRow: true });
	}

	function formatUnitSelection(unit) {
		if (!unit.id) {
			return unit.text;
		}
		if (typeof window.OptimaUnitSelect2 === 'undefined') {
			return unit.text;
		}
		return OptimaUnitSelect2.templateSelection(unit, {});
	}

	function initApprovalUnitPickAjaxSelect2(suffix) {
		const unitPickId = 'approvalUnitPick' + suffix;
		const el = document.getElementById(unitPickId);
		if (!el) {
			return;
		}
		const $unitPick = $(el);
		if ($unitPick.hasClass('select2-hidden-accessible')) {
			$unitPick.select2('destroy');
		}
		$unitPick.empty().append($('<option value=""></option>'));
		const dataUrl = '<?= base_url('service/data-unit/simple') ?>';
		var baseCfg;
		if (typeof window.OptimaUnitSelect2 !== 'undefined') {
			baseCfg = OptimaUnitSelect2.buildServiceDataUnitSimpleSelect2Config({
				url: dataUrl,
				placeholder: '🔍 Search by unit number, SN, brand/model, or unit type...',
				dropdownParent: $('#approvalStageModal .modal-content'),
				minimumInputLength: 1,
				extraSpkRow: true,
				allowedStatuses: ['AVAILABLE_STOCK', 'NON_ASSET_STOCK', 'BOOKED', 'RENTAL_ACTIVE'],
				disableAssigned: true,
				extraAjaxParams: function () {
					return typeof getApprovalUnitSimpleExtraAjax === 'function' ? getApprovalUnitSimpleExtraAjax() : {};
				}
			});
		} else {
			baseCfg = {
				placeholder: '🔍 Search unit…',
				allowClear: true,
				width: '100%',
				dropdownParent: $('#approvalStageModal .modal-content'),
				minimumInputLength: 1
			};
		}
		$unitPick.select2($.extend({}, baseCfg, {
			language: {
				noResults: function () { return 'No units found'; },
				searching: function () { return 'Searching...'; }
			},
			escapeMarkup: function (markup) { return markup; }
		}));

		$unitPick.off('select2:select.optimaUnit').on('select2:select.optimaUnit', function (e) {
				const d = e.params.data || {};
				const elOpt = d.element;
				const unitId = elOpt && elOpt.value ? String(elOpt.value) : (d.id != null ? String(d.id) : '');
				if (!unitId) {
					return;
				}
				const noUnit = elOpt ? (elOpt.getAttribute('data-no-unit') || '') : (d.no_unit != null ? String(d.no_unit) : '');
				const needsNoUnitRaw = elOpt ? elOpt.getAttribute('data-needs-no-unit') : d.needs_no_unit;
				const needsNoUnitBool = needsNoUnitRaw === true || needsNoUnitRaw === 'true';
				const statusUnit = elOpt ? (elOpt.getAttribute('data-status-unit') || '') : (d.status_unit_id != null ? String(d.status_unit_id) : '');
				const departemenId = elOpt ? (elOpt.getAttribute('data-departemen-id') || '') : (d.departemen_id != null ? String(d.departemen_id) : '');
				const departemenName = elOpt ? (elOpt.getAttribute('data-departemen') || '') : (d.departemen || '');
				const displayText = elOpt ? String(elOpt.textContent || '').trim() : String(d.text || '').trim();
					
					// Reset noUnitProcessed flag when changing to a different unit
					if (window.lastSelectedUnitId && window.lastSelectedUnitId !== unitId) {
						console.log('Unit changed, resetting noUnitProcessed flags');
						window.noUnitProcessed = {};
					}
					window.lastSelectedUnitId = unitId;
					
					console.log(`Checking unit ${unitId} for existing components...`);
					console.log(`Department: ${departemenId} (${departemenName})`);
					
					// Determine department type for electric/gasoline/diesel logic
					const isElectric = (departemenName || '').toUpperCase() === 'ELECTRIC';
					const isGasoline = (departemenName || '').toUpperCase() === 'GASOLINE';
					const isDiesel   = (departemenName || '').toUpperCase() === 'DIESEL';
					
				// Re-initialize mechanic selector with unit's department
				if (departemenId && currentApprovalStage) {
					console.log(`🔄 Reloading mechanic list for department ${departemenId} (${departemenName})`);
					if (currentMechanicSelector) {
						currentMechanicSelector.reloadEmployees(departemenId);
					} else {
						initializeMechanicSelection(currentApprovalStage, departemenId);
					}
				}
				
					const isNonElectric = isGasoline || isDiesel;
					
					console.log(`DEBUG: Unit ${unitId} - Department: ${departemenName} (ID: ${departemenId})`);
					console.log(`DEBUG: isElectric: ${isElectric}, isGasoline: ${isGasoline}, isDiesel: ${isDiesel}, isNonElectric: ${isNonElectric}`);
					
					const electricFields = document.getElementById(`electricFields${suffix}`);
					const fabrikasiFields = document.getElementById(`fabrikasiFields${suffix}`);
					
					fetchUnitComponentData(unitId).then(apiData => {
						console.log('API Response for unit', unitId, ':', apiData);
						
						if (apiData && apiData.success) {
							const hasBattery = apiData.battery !== null;
							const hasCharger = apiData.charger !== null;
							const hasAttachment = apiData.attachment !== null;
							
							console.log(`Unit ${unitId} component status: battery=${hasBattery}, charger=${hasCharger}, attachment=${hasAttachment}`);
							
							if (!isNonElectric && (isElectric || hasBattery || hasCharger)) {
								if (electricFields) {
									const existingRenderKey = `component-ui-${unitId}-${suffix}`;
									if (electricFields.querySelector(`[data-render-key="${existingRenderKey}"]`)) {
										console.log('Component UI already rendered for unit', unitId, ', skipping duplicate render');
										return;
									}
									
									const componentOptions = {
										battery: isElectric || hasBattery,
										charger: isElectric || hasCharger,
										attachment: false
									};
									
									console.log('Component options for unit', unitId, ':', componentOptions);
									electricFields.innerHTML = '';
									
									const renderKey = `component-ui-${unitId}-${suffix}`;
									if (!electricFields.querySelector(`[data-render-key="${renderKey}"]`)) {
										const componentUI = generateComponentSelectionUI(apiData, componentOptions, unitId, suffix, 'component');
										electricFields.innerHTML = componentUI;
									}
									electricFields.classList.remove('d-none');
								}
							} else if (isNonElectric && (hasBattery || hasCharger)) {
								if (electricFields) {
									electricFields.innerHTML = `
										<div class="alert alert-warning">
											<i class="fas fa-exclamation-triangle me-2"></i>Unit ${departemenName} does not require a battery and charger.
											<br>The battery and charger installed will be automatically detached from this unit.
										</div>
									`;
									electricFields.classList.remove('d-none');
								}
							} else if (electricFields) {
								electricFields.classList.add('d-none');
							}
							
							if (currentApprovalStage === 'fabrication' || currentApprovalStage === 'fabrikasi' || currentApprovalStage === 'install') {
								if (fabrikasiFields) {
									if (hasAttachment) {
										const existingComponentUI = electricFields?.querySelector('[data-render-key*="component-ui"]');
										if (!existingComponentUI) {
											const attachmentUI = generateComponentSelectionUI(apiData, { battery: false, charger: false, attachment: true }, unitId, suffix, 'attachment');
											fabrikasiFields.innerHTML = attachmentUI;
										} else {
											fabrikasiFields.innerHTML = `
												<div class="alert alert-info">
													<i class="fas fa-tools me-2"></i>Components and attachments for this unit have already been displayed above.
												</div>
											`;
										}
									} else {
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
									fabrikasiFields.classList.remove('d-none');
								}
							} else {
								if (fabrikasiFields) {
									fabrikasiFields.classList.add('d-none');
								}
							}
						} else {
							console.log('No existing components found for unit', unitId);
							
							if (isElectric &&  electricFields) {
								electricFields.classList.remove('d-none');
								console.log(`🔌 Loading electric options with suffix: ${suffix}`);
								loadBatteryOptionsIndividual(suffix);
								loadChargerOptionsIndividual(suffix);
							} else if (electricFields) {
								electricFields.classList.add('d-none');
							}
							
							if ((currentApprovalStage === 'fabrikasi' || currentApprovalStage === 'install') && fabrikasiFields) {
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
								fabrikasiFields.classList.remove('d-none');
								loadAttachmentOptionsIndividual(suffix);
							} else if (fabrikasiFields) {
								fabrikasiFields.classList.add('d-none');
							}
						}
					}).catch(error => {
						console.error('Component detection failed:', error);
						
						if (isElectric && electricFields) {
							electricFields.classList.remove('d-none');
							loadBatteryOptionsIndividual(suffix);
							loadChargerOptionsIndividual(suffix);
						} else if (electricFields) {
							electricFields.classList.add('d-none');
						}
						
						if ((currentApprovalStage === 'fabrikasi' || currentApprovalStage === 'install') && fabrikasiFields) {
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
							fabrikasiFields.classList.remove('d-none');
							loadAttachmentOptionsIndividual(suffix);
						} else if (fabrikasiFields) {
							fabrikasiFields.classList.add('d-none');
						}
					}).finally(() => {
						const unitData = {
							unit_id: unitId,
							departement_name: departemenName
						};
						applyDepartmentalRulesAfterUIGeneration(unitData, suffix);
					});
					
					if ((statusUnit === '1' || statusUnit === '2') && (needsNoUnitBool || needsNoUnitRaw === 'true' || !noUnit || noUnit === '' || noUnit === '0')) {
						window.noUnitProcessed = window.noUnitProcessed || {};
						if (!window.noUnitProcessed[unitId]) {
							showNoUnitConfirmation(unitId, displayText, statusUnit);
						} else {
							console.log('Unit No Unit already processed for unit:', unitId);
						}
					} else {
						console.log('Unit already has no_unit or status not eligible:', displayText, 'Status:', statusUnit, 'No Unit:', noUnit);
					}
		});
	}

	function setupIndividualUnitSearch(suffix, unitIndex) {
		console.log('Setting up unit Select2 with suffix:', suffix, 'unitIndex:', unitIndex);
		const unitPickId = `approvalUnitPick${suffix}`;
		const unitPick = document.getElementById(unitPickId);
		if (!unitPick) {
			console.error('Unit select element not found:', unitPickId);
			return;
		}
		function applyExtraAndInit(extra) {
			extra = extra || {};
			getApprovalUnitSimpleExtraAjax = function () { return extra; };
			initApprovalUnitPickAjaxSelect2(suffix);
		}
		if (!currentApprovalSpkId) {
			console.warn('⚠️ No SPK ID available for department filtering');
			applyExtraAndInit({});
			return;
		}
		const spkDepartmentUrl = `<?= base_url('service/spk-department/') ?>${currentApprovalSpkId}`;
		fetch(spkDepartmentUrl)
			.then(response => {
				if (!response.ok) {
					throw new Error(String(response.status));
				}
				return response.json();
			})
			.then(deptData => {
				const extra = {};
				if (currentApprovalSpkId) {
					extra.exclude_spk_id = currentApprovalSpkId;
				}
				if (deptData.success && deptData.department) {
					extra.spk_department = deptData.department;
				}
				applyExtraAndInit(extra);
			})
			.catch(error => {
				console.error('❌ Error fetching SPK department:', error);
				const extra = {};
				if (currentApprovalSpkId) {
					extra.exclude_spk_id = currentApprovalSpkId;
				}
				applyExtraAndInit(extra);
			});
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
							const serialInfo = item.serial_number || 'SN: -';
							return `<option value="${item.id}" style="white-space: normal; line-height: 1.4; padding: 8px;">
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
							return `<option value="${item.id}">${name} • SN: ${item.serial_number||'-'}</option>`;
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
	
	// Format component options in dropdown: [Item#] Name [SN: xxx] [Status] / Installed info
	function formatComponentOption(option) {
		if (!option.id) return option.text;
		
		const $option = $(option.element);
		const status = $option.data('status') || '';
		const name = $option.data('name') || option.text.split(' • ')[0] || '';
		const itemNumber = $option.data('item-number') || '';
		const serial = $option.data('serial') || '';
		const installedUnit = $option.data('installed-unit') || '';
		
		// Build 2-line display
		let html = '<div style="line-height:1.3; padding:4px 0; border-bottom:1px solid #e9ecef">';
		
		// Line 1: [Item Number Badge] Name [SN: xxx Label] [Status Badge]
		html += '<div class="d-flex align-items-center justify-content-between" style="margin-bottom:3px">';
		html += '<div>';
		if (itemNumber && itemNumber !== '-') {
			html += `<span class="badge badge-soft-blue me-1 font-monospace" style="font-size:0.7rem">${itemNumber}</span>`;
		}
		html += `<strong style="font-size:0.85rem">${name}</strong>`;
		if (serial && serial !== '-') {
			html += `<small class="text-muted ms-1" style="font-size:0.7rem">[SN: ${serial}]</small>`;
		}
		html += '</div>';
		// Status badge on the right
		html += '<div>';
		if (status === 'AVAILABLE' || status === 'SPARE') {
			html += '<span class="badge badge-soft-success" style="font-size:0.7rem">✓ AVAILABLE</span>';
		} else if (status === 'IN_USE') {
			html += '<span class="badge badge-soft-warning" style="font-size:0.7rem">⚠ IN USE</span>';
		} else if (status === 'BROKEN') {
			html += '<span class="badge badge-soft-danger" style="font-size:0.7rem">✗ BROKEN</span>';
		}
		html += '</div>';
		html += '</div>';
		
		// Line 2: Installed unit info (if IN_USE)
		if (status === 'IN_USE' && installedUnit) {
			html += `<div><small class="text-muted" style="font-size:0.7rem; margin-left:0"><i class="fas fa-link me-1"></i>Installed on Unit ${installedUnit}</small></div>`;
		}
		
		html += '</div>';
		return $(html);
	}
	
	// Format selected component in the closed dropdown
	function formatComponentSelection(option) {
		if (!option.id) return option.text;
		
		const $option = $(option.element);
		const itemNumber = $option.data('item-number') || '';
		const name = $option.data('name') || option.text.split(' • ')[0] || '';
		
		if (itemNumber && itemNumber !== '-') {
			return `${itemNumber} - ${name}`.trim();
		}
		
		return name;
	}
	
	function loadBatteryOptionsIndividual(suffix = '') {
		const batteryPickId = suffix ? `batteryPick${suffix}` : 'batteryPick';
		const $batterySelect = $('#' + batteryPickId);
		if (!$batterySelect.length) return;

		if ($batterySelect.hasClass('select2-hidden-accessible')) {
			$batterySelect.select2('destroy');
		}
		$batterySelect.empty().append('<option value=""></option>');

		const batteryApiUrl = '<?= base_url('warehouse/inventory/available-batteries') ?>';

		$batterySelect.select2({
			placeholder: '🔍 Cari baterai (nomor item / SN / merek / tipe)...',
			allowClear: true,
			dropdownParent: $('#approvalStageModal .modal-content'),
			width: '100%',
			minimumInputLength: 0,
			ajax: {
				url: batteryApiUrl,
				type: 'GET',
				dataType: 'json',
				delay: 300,
				data: function(params) { return { q: params.term || '' }; },
				processResults: function(data) {
					if (!Array.isArray(data)) return { results: [] };
					return {
						results: data.map(function(item) {
							const name = [item.merk_baterai, item.tipe_baterai, item.jenis_baterai].filter(Boolean).join(' ').trim() || '-';
							const itemNumber = item.item_number || '-';
							const serialNumber = item.serial_number || '-';
							const isUsed = item.status === 'IN_USE';
							const installedUnit = isUsed && item.installed_unit_no ? `Unit ${item.installed_unit_no}` : '';
							const statusLabel = isUsed ? ` [IN USE${installedUnit ? ' @ ' + installedUnit : ''}]` : '';
							return {
								id: String(item.id),
								text: `${itemNumber} ${name} SN:${serialNumber}${statusLabel}`,
								item_number: itemNumber,
								serial_number: serialNumber,
								status: item.status,
								name: name,
								installed_unit_no: item.installed_unit_no || '',
								installed_unit_sn: item.installed_unit_sn || '',
								installed_unit_merk: item.installed_unit_merk || '',
								installed_unit_model: item.installed_unit_model || '',
								raw: item
							};
						})
					};
				},
				cache: true
			},
			templateResult: function(item) {
				if (item.loading || !item.item_number) return item.text;
				const isUsed = item.status === 'IN_USE';
				const badgeColor = isUsed ? '#dc3545' : '#0d6efd';
				const statusBadge = isUsed ? `<span style="background:#dc3545;color:#fff;font-size:10px;padding:1px 6px;border-radius:3px;margin-left:4px">IN USE</span>` : '';
				return $(`<div style="padding:2px 0;line-height:1.4">
					<div><span style="background:${badgeColor};color:#fff;font-size:10px;padding:1px 7px;border-radius:4px;font-weight:600;letter-spacing:.3px">${item.item_number}</span> <strong style="font-size:13px">${item.name}</strong>${statusBadge}</div>
					<div style="font-size:11px;color:#6c757d;margin-top:2px"><i class="fas fa-barcode" style="font-size:10px;margin-right:3px"></i>SN: ${item.serial_number || '-'}</div>
				</div>`);
			},
			templateSelection: function(item) {
				if (!item.item_number) return item.text;
				return $(`<span><span style="background:#0d6efd;color:#fff;font-size:10px;padding:1px 6px;border-radius:3px;margin-right:4px">${item.item_number}</span>${item.name} &bull; SN: ${item.serial_number || '-'}</span>`);
			},
			language: {
				noResults: function() { return 'Baterai tidak ditemukan'; },
				searching: function() { return 'Mencari...'; },
				inputTooShort: function() { return 'Ketik untuk mencari baterai...'; }
			},
			escapeMarkup: function(markup) { return markup; }
		});

		// Add event listener for IN_USE battery detection
		$batterySelect.off('select2:select.batteryPick').on('select2:select.batteryPick', function(e) {
			const d = e.params.data || {};
			const status = d.status || '';
			const installedUnit = d.installed_unit_no || '';

			console.log(`🔋 Battery selected - Status: ${status}, Installed Unit: ${installedUnit}`);

			if (status === 'IN_USE' && installedUnit) {
				// Show confirmation modal for IN_USE battery
				// Reconstruct a fake element for showUsedComponentAlert compatibility
				const fakeEl = document.createElement('option');
				fakeEl.setAttribute('data-status', status);
				fakeEl.setAttribute('data-name', d.name || '');
				fakeEl.setAttribute('data-item-number', d.item_number || '');
				fakeEl.setAttribute('data-serial', d.serial_number || '');
				fakeEl.setAttribute('data-installed-unit', installedUnit);
				fakeEl.setAttribute('data-installed-sn', d.installed_unit_sn || '');
				fakeEl.setAttribute('data-installed-merk', d.installed_unit_merk || '');
				fakeEl.setAttribute('data-installed-model', d.installed_unit_model || '');
				showUsedComponentAlert(fakeEl, 'battery', suffix);
			}
		});
	}
	
	// Make sure functions are accessible globally
	window.loadBatteryOptionsIndividual = loadBatteryOptionsIndividual;
	
	function loadChargerOptionsIndividual(suffix = '') {
		const chargerPickId = suffix ? `chargerPick${suffix}` : 'chargerPick';
		const $chargerSelect = $('#' + chargerPickId);
		if (!$chargerSelect.length) return;

		if ($chargerSelect.hasClass('select2-hidden-accessible')) {
			$chargerSelect.select2('destroy');
		}
		$chargerSelect.empty().append('<option value=""></option>');

		const chargerApiUrl = '<?= base_url('warehouse/inventory/available-chargers') ?>';

		$chargerSelect.select2({
			placeholder: '🔍 Cari charger (nomor item / SN / merek / tipe)...',
			allowClear: true,
			dropdownParent: $('#approvalStageModal .modal-content'),
			width: '100%',
			minimumInputLength: 0,
			ajax: {
				url: chargerApiUrl,
				type: 'GET',
				dataType: 'json',
				delay: 300,
				data: function(params) { return { q: params.term || '' }; },
				processResults: function(data) {
					if (!Array.isArray(data)) return { results: [] };
					return {
						results: data.map(function(item) {
							const name = [item.merk_charger, item.tipe_charger].filter(Boolean).join(' ').trim() || '-';
							const itemNumber = item.item_number || '-';
							const serialNumber = item.serial_number || '-';
							const isUsed = item.status === 'IN_USE';
							const installedUnit = isUsed && item.installed_unit_no ? `Unit ${item.installed_unit_no}` : '';
							const statusLabel = isUsed ? ` [IN USE${installedUnit ? ' @ ' + installedUnit : ''}]` : '';
							return {
								id: String(item.id),
								text: `${itemNumber} ${name} SN:${serialNumber}${statusLabel}`,
								item_number: itemNumber,
								serial_number: serialNumber,
								status: item.status,
								name: name,
								installed_unit_no: item.installed_unit_no || '',
								installed_unit_sn: item.installed_unit_sn || '',
								installed_unit_merk: item.installed_unit_merk || '',
								installed_unit_model: item.installed_unit_model || '',
								raw: item
							};
						})
					};
				},
				cache: true
			},
			templateResult: function(item) {
				if (item.loading || !item.item_number) return item.text;
				const isUsed = item.status === 'IN_USE';
				const badgeColor = isUsed ? '#dc3545' : '#198754';
				const statusBadge = isUsed ? `<span style="background:#dc3545;color:#fff;font-size:10px;padding:1px 6px;border-radius:3px;margin-left:4px">IN USE</span>` : '';
				return $(`<div style="padding:2px 0;line-height:1.4">
					<div><span style="background:${badgeColor};color:#fff;font-size:10px;padding:1px 7px;border-radius:4px;font-weight:600;letter-spacing:.3px">${item.item_number}</span> <strong style="font-size:13px">${item.name}</strong>${statusBadge}</div>
					<div style="font-size:11px;color:#6c757d;margin-top:2px"><i class="fas fa-barcode" style="font-size:10px;margin-right:3px"></i>SN: ${item.serial_number || '-'}</div>
				</div>`);
			},
			templateSelection: function(item) {
				if (!item.item_number) return item.text;
				return $(`<span><span style="background:#198754;color:#fff;font-size:10px;padding:1px 6px;border-radius:3px;margin-right:4px">${item.item_number}</span>${item.name} &bull; SN: ${item.serial_number || '-'}</span>`);
			},
			language: {
				noResults: function() { return 'Charger tidak ditemukan'; },
				searching: function() { return 'Mencari...'; },
				inputTooShort: function() { return 'Ketik untuk mencari charger...'; }
			},
			escapeMarkup: function(markup) { return markup; }
		});

		// Add event listener for IN_USE charger detection
		$chargerSelect.off('select2:select.chargerPick').on('select2:select.chargerPick', function(e) {
			const d = e.params.data || {};
			const status = d.status || '';
			const installedUnit = d.installed_unit_no || '';

			console.log(`🔌 Charger selected - Status: ${status}, Installed Unit: ${installedUnit}`);

			if (status === 'IN_USE' && installedUnit) {
				const fakeEl = document.createElement('option');
				fakeEl.setAttribute('data-status', status);
				fakeEl.setAttribute('data-name', d.name || '');
				fakeEl.setAttribute('data-item-number', d.item_number || '');
				fakeEl.setAttribute('data-serial', d.serial_number || '');
				fakeEl.setAttribute('data-installed-unit', installedUnit);
				fakeEl.setAttribute('data-installed-sn', d.installed_unit_sn || '');
				fakeEl.setAttribute('data-installed-merk', d.installed_unit_merk || '');
				fakeEl.setAttribute('data-installed-model', d.installed_unit_model || '');
				showUsedComponentAlert(fakeEl, 'charger', suffix);
			}
		});
	}
	
	// Make sure functions are accessible globally
	window.loadChargerOptionsIndividual = loadChargerOptionsIndividual;
	
	// Show kanibal alert for USED items
	function showKanibalAlert(selectedOption, itemType, suffix = '') {
		const installedUnitNo = selectedOption.dataset.installedUnit;
		const installedSN = selectedOption.dataset.installedSn;
		const installedMerk = selectedOption.dataset.installedMerk;
		const installedModel = selectedOption.dataset.installedModel;
		
		confirmSwal({
			title: `${itemType} Sedang Dipakai`,
			text: `${itemType} ini sedang terpasang di Unit ${installedUnitNo || 'N/A'} (${installedMerk || ''} ${installedModel || ''}, SN: ${installedSN || 'N/A'}). Lanjutkan proses kanibal untuk memindahkannya?`,
			confirmText: 'Ya, Pindahkan',
			icon: 'warning'
		}).then((confirmed) => {
			if (confirmed) {
				// User confirmed kanibal - keep selection
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
const serialInfo = item.sn_attachment || item.serial_number || 'SN: -';
						return `<option value="${item.id_inventory_attachment || item.id}" style="white-space: normal; line-height: 1.4; padding: 8px;">
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
							const serialInfo = item.sn_attachment || item.serial_number || 'SN: -';
							return `<option value="${item.id_inventory_attachment || item.id}" style="white-space: normal; line-height: 1.4; padding: 8px;">
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
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
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
					notify('No Unit manual harus diisi dengan angka positif', 'warning');
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
				const $ap = $('#approvalUnitPick');
				if ($ap.length && $ap.hasClass('select2-hidden-accessible')) {
					$ap.val(null).trigger('change');
				} else {
					const el = document.getElementById('approvalUnitPick');
					if (el) el.value = '';
				}
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
			.then(response => {
				if (response.status === 401) {
					notify('Session expired. Please login again.', 'error');
					window.location.href = '<?= base_url('auth/login') ?>';
					return Promise.reject('Unauthorized');
				}
				return response.json();
			})
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
	
	// Fork selection management based on quotation accessories (kontrak_spec)
	function setupFabrikasiForkManagement() {
		const contentDiv = document.getElementById('fabrikasiForkContent');
		if (!contentDiv) return;

		// Default: ok
		window.spkForkStockCheck = { ok: true, reason: '' };

		contentDiv.innerHTML = `
			<div class="mb-2">
				<label class="form-label">Pilih Fork dari Inventori <span class="text-danger">*</span></label>
				<select class="form-select" id="fabrikasiForkPick" name="fork_id" style="width:100%"></select>
				<div class="form-text">Cari berdasarkan item number, SN, atau tipe fork.</div>
			</div>
			<div class="small text-muted mt-2" id="fabrikasiForkQtyHint">Qty pair: -</div>
			<div class="small mt-1 d-none" id="fabrikasiForkStockWarning"></div>
			<hr class="my-3">
			<div>
				<button type="button" class="btn btn-sm btn-outline-secondary" id="btnShowAddForkForm" onclick="toggleAddForkForm()">
					<i class="fas fa-plus me-1"></i>Tambah Fork
				</button>
				<div id="addForkForm" class="d-none mt-3 p-3 border rounded bg-light">
					<p class="small fw-semibold mb-2">Daftarkan fork baru ke inventori</p>
					<div class="mb-2">
						<label class="form-label small">Tipe Fork (Master) <span class="text-danger">*</span></label>
						<select class="form-select form-select-sm" id="newForkMasterId" style="width:100%"></select>
					</div>
					<div class="row g-2 mb-2">
						<div class="col-6">
							<label class="form-label small">Item Number / SN <span class="text-danger">*</span></label>
							<input type="text" class="form-control form-control-sm" id="newForkItemNumber" placeholder="Contoh: FK-001">
						</div>
						<div class="col-6">
							<label class="form-label small">Qty Pairs</label>
							<input type="number" class="form-control form-control-sm" id="newForkQtyPairs" value="1" min="1">
						</div>
					</div>
					<div class="d-flex gap-2">
						<button type="button" class="btn btn-sm btn-primary" onclick="submitAddFork()">
							<i class="fas fa-save me-1"></i>Simpan & Pilih
						</button>
						<button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAddForkForm()">Batal</button>
					</div>
				</div>
			</div>
		`;

		const $forkSelect = $('#fabrikasiForkPick');

		if ($forkSelect.hasClass('select2-hidden-accessible')) {
			$forkSelect.select2('destroy');
		}

		$forkSelect.select2({
			placeholder: 'Cari berdasarkan item number / spec fork...',
			allowClear: true,
			dropdownParent: $('#approvalStageModal .modal-content'),
			width: '100%',
			minimumInputLength: 0,
			ajax: {
				url: '<?= base_url('service/data-attachment/simple') ?>',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return { q: params.term || '', type: 'fork' };
				},
				processResults: function (data) {
					if (data.success && data.data) {
						return {
							results: data.data.map(function(item) {
								const idText = item.sn_fork ? item.sn_fork : '(No Item)';
								return { id: item.id, text: `${idText} - ${item.label}`, data: item };
							})
						};
					}
					return { results: [] };
				},
				cache: false
			}
		});

		$forkSelect.on('select2:select', function(e) {
			const fork = e.params.data?.data;
			if (!fork) return;
			const qtyPairs = fork?.qty_pairs ?? null;
			const qtyAvail = fork?.qty_available_pairs ?? null;
			const isUsed   = fork?.is_used === true;
			$('#fabrikasiForkQtyHint').text('Qty pair: ' + (qtyPairs !== null ? qtyPairs : '-'));
			if (!isUsed && qtyPairs !== null && qtyAvail !== null && parseInt(qtyPairs) > parseInt(qtyAvail)) {
				window.spkForkStockCheck = { ok: false, reason: `Stok fork tidak cukup (butuh ${qtyPairs} pair, tersedia ${qtyAvail} pair).` };
				$('#fabrikasiForkStockWarning').removeClass('d-none').text(window.spkForkStockCheck.reason);
			} else {
				window.spkForkStockCheck = { ok: true, reason: '' };
				$('#fabrikasiForkStockWarning').addClass('d-none').text('');
			}
		});

		$forkSelect.on('select2:clear', function() {
			window.spkForkStockCheck = { ok: true, reason: '' };
			$('#fabrikasiForkQtyHint').text('Qty pair: -');
			$('#fabrikasiForkStockWarning').addClass('d-none').text('');
		});

		// Init master fork Select2 for "Tambah Fork Baru" form
		setTimeout(() => {
			const $masterFork = $('#newForkMasterId');
			if (!$masterFork.length) return;
			if ($masterFork.hasClass('select2-hidden-accessible')) $masterFork.select2('destroy');
			$masterFork.select2({
				placeholder: 'Pilih tipe fork...',
				allowClear: true,
				dropdownParent: $('#approvalStageModal .modal-content'),
				width: '100%',
				minimumInputLength: 0,
				ajax: {
					url: '<?= base_url('service/master-fork') ?>',
					dataType: 'json',
					delay: 250,
					data: function(params) { return { q: params.term || '' }; },
					processResults: function(data) {
						if (data.success && data.data) {
							return {
								results: data.data.map(function(f) {
									const size = f.length_mm ? ` ${f.length_mm}mm` : '';
									const cls  = f.fork_class ? ` (${f.fork_class})` : '';
									return { id: f.id, text: f.name + size + cls };
								})
							};
						}
						return { results: [] };
					},
					cache: false
				}
			});
		}, 200);
	}

	window.toggleAddForkForm = function() {
		const form = document.getElementById('addForkForm');
		if (form) form.classList.toggle('d-none');
	};

	window.submitAddFork = function() {
		const masterId  = document.getElementById('newForkMasterId')?.value;
		const itemNumber = document.getElementById('newForkItemNumber')?.value?.trim();
		const qtyPairs  = parseInt(document.getElementById('newForkQtyPairs')?.value || '1', 10);

		if (!masterId) { notify('Pilih tipe fork terlebih dahulu.', 'warning'); return; }
		if (!itemNumber) { notify('Item number / SN wajib diisi.', 'warning'); return; }
		if (isNaN(qtyPairs) || qtyPairs < 1) { notify('Qty pairs minimal 1.', 'warning'); return; }

		const fd = new FormData();
		fd.append(window.csrfTokenName, window.csrfTokenValue);
		fd.append('fork_id', masterId);
		fd.append('item_number', itemNumber);
		fd.append('qty_pairs', qtyPairs);

		fetch('<?= base_url('service/add-inventory-fork') ?>', {
			method: 'POST',
			headers: { 'X-Requested-With': 'XMLHttpRequest' },
			body: fd
		}).then(r => r.json()).then(res => {
			if (res.success && res.data) {
				notify('Fork berhasil didaftarkan ke inventori.', 'success');
				// Auto-select the new fork in the picker
				const newFork = res.data;
				const idText  = newFork.item_number || '(New)';
				const option  = new Option(`${idText} - ${newFork.fork_name || ''}`, newFork.id, true, true);
				const $pick   = $('#fabrikasiForkPick');
				$pick.append(option).trigger('change');
				window.spkForkStockCheck = { ok: true, reason: '' };
				$('#fabrikasiForkQtyHint').text(`Qty pair: ${newFork.qty_pairs || 1}`);
				// Collapse add form
				document.getElementById('addForkForm')?.classList.add('d-none');
			} else {
				notify(res.message || 'Gagal mendaftarkan fork.', 'error');
			}
		}).catch(() => notify('Terjadi kesalahan sistem.', 'error'));
	};
	
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
					<select class="form-select" id="fabrikasiAttPick" name="new_attachment_id">
						<option value="">-- Select Attachment --</option>
					</select>
					<div class="form-text">
						<i class="fas fa-info-circle me-1"></i>Type to search by Type / Brand / Model / SN
					</div>
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
					<select class="form-select" id="fabrikasiAttPick" name="new_attachment_id">
						<option value="">-- Select Attachment (Optional) --</option>
					</select>
					<div class="form-text">
						<i class="fas fa-info-circle me-1"></i>Type to search by Type / Brand / Model / SN
					</div>
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
	}
	
	// Load available attachments for fabrikasi
	function loadFabrikasiAttachmentOptions() {
		const url = '<?= base_url('service/data-attachment/simple') ?>';
		
		fetch(url)
			.then(response => response.json())
			.then(data => {
				const select = document.getElementById('fabrikasiAttPick');
				if (select && data.data) {
					// Build options with status information matching battery/charger style
					select.innerHTML = '<option value="">-- Select Attachment --</option>' + 
						data.data.map(item => {
							// Parse installed unit data  
							const installedUnit = item.installed_unit || null;
							const isUsed = item.is_used || false;
							const status = isUsed ? 'USED' : 'AVAILABLE';
							
							// Format attachment name (from item.label which includes type, brand, model, SN)
							const parts = (item.label || '').split('•');
							const name = (parts[0] || '').trim();
							const serialInfo = (parts[1] || 'SN: -').trim();
							const locationInfo = parts.length > 2 ? (parts[2] || '').trim() : '';
							
							let optionClass = isUsed ? 'used-unit-option' : 'available-unit-option';
							
							return `<option value="${item.id}" 
									data-status="${status}"
									data-name="${name}"
									data-serial="${serialInfo}"
									data-location="${locationInfo}"
									data-is-used="${isUsed}"
									data-installed-unit="${installedUnit ? installedUnit.no_unit || '' : ''}"
									data-installed-sn="${installedUnit ? installedUnit.serial_number || '' : ''}"
									data-installed-merk="${installedUnit ? installedUnit.merk_unit || '' : ''}"
									data-installed-model="${installedUnit ? installedUnit.model_unit || '' : ''}"
									data-installed-unit-json='${JSON.stringify(installedUnit || null)}'
									class="${optionClass}">
								${name} • ${serialInfo}
							</option>`;
						}).join('');
					
					// Initialize Select2 with custom templates (matching battery/charger style)
					const $attachmentSelect = $('#fabrikasiAttPick');
					if ($attachmentSelect.hasClass('select2-hidden-accessible')) {
						$attachmentSelect.select2('destroy');
					}
					
					$attachmentSelect.select2({
						placeholder: '🔍 Search attachment by Type / Brand / Model / SN...',
						allowClear: true,
						dropdownParent: $('#approvalStageModal .modal-content'),
						width: '100%',
						minimumInputLength: 0,
						language: {
							noResults: function() { return 'No attachment found'; },
							searching: function() { return 'Searching...'; }
						},
						templateResult: formatAttachmentOption,
						templateSelection: formatAttachmentSelection,
						escapeMarkup: function(markup) { return markup; }
					});
					
					// Update availability indicators after loading options
					setTimeout(() => {
						updateDropdownAvailability(select, 'attachment');
					}, 100);
					
					// Add event listener for USED attachment detection
					$attachmentSelect.on('select2:select', function(e) {
						const selectedOption = e.params.data.element;
						const status = selectedOption.getAttribute('data-status');
						const installedUnit = selectedOption.getAttribute('data-installed-unit');
						
						console.log(`🔧 Attachment selected - Status: ${status}, Installed Unit: ${installedUnit}`);
						
						if (status === 'IN_USE' && installedUnit) {
							// Show confirmation modal for IN_USE attachment
							showUsedAttachmentAlert(selectedOption);
						}
					});
				}
			})
			.catch(error => console.log('Error loading fabrikasi attachment options:', error));
	}
	
	// Format attachment option in dropdown (matching battery/charger style)
	function formatAttachmentOption(option) {
		if (!option.id) return option.text;
		
		const $option = $(option.element);
		const status = $option.data('status') || '';
		const name = $option.data('name') || option.text.split(' • ')[0] || '';
		const serial = $option.data('serial') || '';
		const installedUnit = $option.data('installed-unit') || '';
		
		let statusBadge = '';
		let installedInfo = '';
		
		if (status === 'AVAILABLE') {
			statusBadge = '<span class="component-status-badge status-available">✓ Available</span>';
		} else if (status === 'IN_USE') {
			statusBadge = '<span class="component-status-badge status-used">⚠ Used</span>';
			if (installedUnit) {
				installedInfo = `<span class="installed-unit-info">(Unit ${installedUnit})</span>`;
			}
		}
		
		return $('<span><span class="component-item-text">' + name + ' • ' + serial + '</span> ' +
			statusBadge + installedInfo + '</span>');
	}
	
	// Format selected attachment in the closed dropdown
	function formatAttachmentSelection(option) {
		if (!option.id) return option.text;
		
		const $option = $(option.element);
		const name = $option.data('name') || option.text.split(' • ')[0] || '';
		const serial = $option.data('serial') || '';
		
		return $('<span>' + name + ' • ' + serial + '</span>');
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
		
		// Get installed unit data from data attributes
		const installedUnitJson = option.getAttribute('data-installed-unit-json') || option.dataset.installedUnitJson;
		const installedUnit = installedUnitJson ? JSON.parse(installedUnitJson) : null;
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
	
	// Universal function for USED battery/charger alerts
	function showUsedComponentAlert(option, componentType, suffix = '') {
		console.log(`🔋 showUsedComponentAlert called - Type: ${componentType}, Suffix: ${suffix}`);
		
		// Prevent multiple modal creation
		if (window.usedComponentModalShowing) {
			console.log('⚠️ Modal already showing, skipping');
			return;
		}
		window.usedComponentModalShowing = true;
		
		// Extract component info from data attributes
		const installedUnit = option.getAttribute('data-installed-unit') || '';
		const installedSn = option.getAttribute('data-installed-sn') || '';
		const installedMerk = option.getAttribute('data-installed-merk') || '';
		const installedModel = option.getAttribute('data-installed-model') || '';
		const componentName = option.getAttribute('data-name') || '';
		const componentSerial = option.getAttribute('data-serial') || '';
		
		console.log(`🔋 Component: ${componentName}, Installed on Unit: ${installedUnit}`);
		
		if (!installedUnit) {
			console.log('❌ No installed unit data, skipping alert');
			window.usedComponentModalShowing = false;
			return;
		}
		
		// Component labels and icons
		const typeConfig = {
			battery: { label: 'Battery', icon: 'fa-battery-full', color: 'warning' },
			charger: { label: 'Charger', icon: 'fa-charging-station', color: 'warning' },
			attachment: { label: 'Attachment', icon: 'fa-wrench', color: 'warning' }
		};
		const config = typeConfig[componentType] || typeConfig.battery;
		
		const modalHtml = `
			<div class="modal fade" id="usedComponentModal" tabindex="-1" data-modal="kanibal-component">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header bg-${config.color}">
							<h5 class="modal-title">
								<i class="fas ${config.icon} me-2"></i>
								${config.label} Already In Use
							</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
						</div>
						<div class="modal-body">
							<div class="alert alert-${config.color}">
								<strong>Warning!</strong> This ${config.label.toLowerCase()} is currently installed on another unit.
							</div>
							
							<div class="mb-3">
								<strong>Selected ${config.label}:</strong>
								<div class="card bg-light mt-2">
									<div class="card-body py-2">
										<strong>${componentName}</strong> • ${componentSerial}
									</div>
								</div>
							</div>
							
							<div class="mb-3">
								<strong>Currently Installed On:</strong>
								<div class="card bg-light mt-2">
									<div class="card-body py-2">
										<ul class="list-unstyled mb-0">
											<li><strong>Unit Number:</strong> ${installedUnit}</li>
											<li><strong>Serial Number:</strong> ${installedSn}</li>
											<li><strong>Brand & Model:</strong> ${installedMerk} ${installedModel}</li>
										</ul>
									</div>
								</div>
							</div>
							
							<div class="mb-3">
								<strong>Action Required:</strong>
								<ul class="mb-0">
									<li><strong>YES</strong> - Transfer this ${config.label.toLowerCase()} from Unit ${installedUnit} to the selected unit (replacement/cannibalization process)</li>
									<li><strong>NO</strong> - Cancel the selection and choose a different ${config.label.toLowerCase()}</li>
								</ul>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" onclick="cancelUsedComponent('${componentType}', '${suffix}')">
								<i class="fas fa-times me-1"></i>NO, CANCEL
							</button>
							<button type="button" class="btn btn-${config.color}" onclick="confirmUsedComponent('${componentType}', '${suffix}')">
								<i class="fas fa-exchange-alt me-1"></i>YES, TRANSFER
							</button>
						</div>
					</div>
				</div>
			</div>
		`;
		
		// Remove existing modal if any
		const existingModal = document.getElementById('usedComponentModal');
		if (existingModal) {
			const modal = bootstrap.Modal.getInstance(existingModal);
			if (modal) modal.hide();
			existingModal.remove();
			console.log('🔋 Removed existing component modal');
		}
		
		// Add modal to body
		document.body.insertAdjacentHTML('beforeend', modalHtml);
		console.log('🔋 Component modal HTML added to body');
		
		// Show modal
		const modal = new bootstrap.Modal(document.getElementById('usedComponentModal'));
		modal.show();
		console.log('🔋 Component modal shown');
	}
	
	// Make function globally accessible
	window.showUsedComponentAlert = showUsedComponentAlert;
	
	// Cancel USED component selection
	function cancelUsedComponent(componentType, suffix = '') {
		console.log(`❌ cancelUsedComponent called - Type: ${componentType}, Suffix: ${suffix}`);
		
		// Determine select element ID
		const selectId = componentType === 'battery' ? `batteryPick${suffix}` : `chargerPick${suffix}`;
		const $select = $('#' + selectId);
		
		if ($select.length) {
			// Reset Select2 selection
			$select.val('').trigger('change');
			console.log(`✅ ${componentType} selection cleared`);
		} else {
			console.log(`❌ ${selectId} not found`);
		}
		
		// Close modal
		const modalElement = document.getElementById('usedComponentModal');
		if (modalElement) {
			const modal = bootstrap.Modal.getInstance(modalElement);
			if (modal) {
				modal.hide();
				console.log('✅ Component modal closed via Bootstrap instance');
				setTimeout(() => {
					if (document.getElementById('usedComponentModal')) {
						modalElement.remove();
						console.log('✅ Component modal removed after transition');
					}
				}, 300);
			}
		}
		
		// Show info notification
		notify(`Pemilihan ${componentType} dibatalkan`, 'info');
		
		// Reset flag
		window.usedComponentModalShowing = false;
	}
	
	// Make function globally accessible
	window.cancelUsedComponent = cancelUsedComponent;
	
	// Confirm USED component selection (transfer)
	function confirmUsedComponent(componentType, suffix = '') {
		console.log(`🔄 confirmUsedComponent called - Type: ${componentType}, Suffix: ${suffix}`);
		
		// Prevent multiple calls
		if (window.confirmUsedComponentProcessing) {
			return;
		}
		window.confirmUsedComponentProcessing = true;
		
		// Disable button to prevent multiple clicks
		const confirmBtn = document.querySelector('#usedComponentModal .btn-warning');
		if (confirmBtn) {
			confirmBtn.disabled = true;
			confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
		}
		
		// Set flag for backend to handle transfer
		const selectId = componentType === 'battery' ? `batteryPick${suffix}` : `chargerPick${suffix}`;
		const select = document.getElementById(selectId);
		if (select) {
			select.dataset.transferComponent = 'true';
			console.log(`✅ Set transfer flag for ${selectId}`);
		}
		
		// Add hidden input for backend processing
		let transferInput = document.getElementById(`transfer_${componentType}_input${suffix}`);
		if (!transferInput) {
			transferInput = document.createElement('input');
			transferInput.type = 'hidden';
			transferInput.id = `transfer_${componentType}_input${suffix}`;
			transferInput.name = `transfer_${componentType}${suffix}`;
			document.getElementById('approvalStageForm').appendChild(transferInput);
		}
		transferInput.value = 'true';
		console.log(`✅ Created transfer input for ${componentType}`);
		
		// Close modal
		const modalElement = document.getElementById('usedComponentModal');
		if (modalElement) {
			const modal = bootstrap.Modal.getInstance(modalElement);
			if (modal) {
				modal.hide();
				console.log('✅ Component modal closed via Bootstrap instance');
				setTimeout(() => {
					if (document.getElementById('usedComponentModal')) {
						modalElement.remove();
						console.log('✅ Component modal removed after transition');
					}
				}, 300);
			}
		}
		
		// Show success notification
		notify(`${componentType.charAt(0).toUpperCase() + componentType.slice(1)} akan ditransfer saat approval disimpan`, 'success');
		
		// Reset processing flag after delay
		setTimeout(() => {
			window.confirmUsedComponentProcessing = false;
			window.usedComponentModalShowing = false;
		}, 1000);
	}
	
	// Make function globally accessible
	window.confirmUsedComponent = confirmUsedComponent;
	
		// Prevent duplicate event listeners
		if (!window.usedAttachmentModalInitialized) {
			window.usedAttachmentModalInitialized = true;
			console.log('🔧 Used attachment modal event listeners initialized');
			
		// Add CSS for proper modal stacking only
		const style = document.createElement('style');
		style.textContent = `
			/* Z-index management for nested modals */
			body.modal-open {
				overflow: hidden;
			}
			body.modal-open .modal-backdrop {
				z-index: 1040;
			}
			body.modal-open .modal {
				z-index: 1050;
			}
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
		
		// Validate and add multi-mechanic data (skipped for fabrikasi — inherited from persiapan)
		if (currentApprovalStage !== 'install') {
			if (!currentMechanicSelector || !currentMechanicSelector.isValid()) {
				notify('Please select at least one mechanic for this stage', 'error');
				return;
			}
			
			// Get selected mechanics data
			const selectedMechanics = currentMechanicSelector.getSelectedEmployees();
			fd.append('mechanics_data', JSON.stringify(selectedMechanics));
			
			// Set primary mechanic for backwards compatibility
			const primaryMechanic = selectedMechanics.find(m => m.isPrimary);
			if (primaryMechanic) {
				fd.append('mekanik', primaryMechanic.name);
				fd.append('primary_mechanic_id', primaryMechanic.id);
			}
		}
		
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
					notify('Component validation failed:\n\n' + validationErrors.join('\n'), 'error');
					return;
				}
			} else {
				// Fallback to legacy behavior for Electric departments
				const electricFields = document.getElementById('electricFields');
				if (electricFields && !electricFields.classList.contains('d-none')) {
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
						notify('For Electric units, Battery is required!\n\nChoose one:\n- Use existing battery (check the checkbox)\n- Select a new battery from the dropdown', 'warning');
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
		
		// Handle fabrikasi stage — read install_type from radio
		if (currentApprovalStage === 'fabrikasi' || currentApprovalStage === 'install') {
			const installTypeRadio = document.querySelector('input[name="install_type"]:checked');
			const installType = installTypeRadio ? installTypeRadio.value : null;

			if (!installType) {
				notify('Pilih jenis instalasi terlebih dahulu (Pasang Fork atau Pasang Attachment).', 'warning');
				return;
			}
			fd.append('install_type', installType);

			// Append keterangan instalasi (applies to both fork & attachment)
			const keteranganInstalasi = document.getElementById('fabrikasiKeterangan')?.value?.trim() || '';
			if (keteranganInstalasi) fd.append('keterangan_instalasi', keteranganInstalasi);

			if (installType === 'attachment') {
				// --- Attachment mode ---
				const attachmentAction = document.getElementById('fabrikasi_attachment_action')?.value || 'none';
				const existingAttachmentId = document.getElementById('fabrikasi_attachment_existing_id')?.value;
				const newAttachmentId = document.getElementById('fabrikasiAttPick')?.value;
				const transferAttachment = document.getElementById('fabrikasiAttPick')?.dataset.transferAttachment === 'true';
				const transferInput = document.getElementById('transfer_attachment_input');
				const transferFromInput = transferInput?.value === 'true';

				console.log('Fabrikasi Attachment:', {
					attachmentId: newAttachmentId,
					transferMode: transferAttachment || transferFromInput ? 'KANIBAL' : 'NORMAL'
				});

				if (attachmentAction === 'keep_existing' && existingAttachmentId) {
					fd.append('attachment_inventory_attachment_id', existingAttachmentId);
				} else if (attachmentAction === 'replace' && newAttachmentId) {
					fd.append('attachment_inventory_attachment_id', newAttachmentId);
					if (transferAttachment || transferFromInput) fd.append('transfer_attachment', 'true');
				} else if (newAttachmentId) {
					fd.append('attachment_inventory_attachment_id', newAttachmentId);
					if (transferAttachment || transferFromInput) fd.append('transfer_attachment', 'true');
				}
			} else {
				// --- Fork mode ---
				if (window.spkForkStockCheck && window.spkForkStockCheck.ok === false) {
					notify('Stok fork tidak mencukupi: ' + (window.spkForkStockCheck.reason || ''), 'error');
					return;
				}
				// fork_id included automatically via select name="fork_id"
			}
		}
		
		// Use original API (now updated to use new database structure)
		const apiUrl = `<?= base_url('service/spk/approve-stage/') ?>${currentApprovalSpkId}`;
		
		// Add unit_index to form data (use 1 as default if not editing specific unit)
		const unitIndex = currentEditingUnitIndex !== null ? currentEditingUnitIndex : 1;
		fd.append('unit_index', unitIndex);

		// PDI stage: warn if no spareparts have been added yet
		const _doApprovalFetch = () => {
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
				reloadSpkTable();
				
				// Display success message with no_unit info if available
				let successMessage = j.message || 'Approval Successfully Saved';
				
				// Get stage-specific message
				const stageMessages = {
					'persiapan_unit': 'Unit Preparation Successfully Completed',
					'fabrikasi': 'Install Attachment Process Successfully Completed',
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
						alertSwal('info', `Nomor unit ${j.no_unit} telah ditetapkan untuk unit ini.`, 'Nomor Unit Ditetapkan');
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

				// Trigger sparepart validation after PDI approval
				if (currentApprovalStage === 'pdi') {
					const spkIdForValidation = currentApprovalSpkId;
					const spkNumberForValidation = j.spk_number || 'N/A';
					setTimeout(() => checkAndTriggerSparepartValidation(spkIdForValidation, spkNumberForValidation), 1000);
				}
			} else {
				notify(j.message || 'Failed to save approval', 'error');
			}
		}).catch(error => {
			console.error('Fetch error:', error);
			notify('Failed to send request to server', 'error');
		});
		}; // end _doApprovalFetch

		// PDI stage: warn if no spareparts added yet (can't add after PDI is done)
		if (currentApprovalStage === 'pdi') {
			fetch(`<?= base_url('service/spk/get-spareparts/') ?>${currentApprovalSpkId}`, {
				headers: {'X-Requested-With': 'XMLHttpRequest'}
			}).then(r => r.json()).then(sp => {
				const spCount = (sp.success && sp.spareparts) ? sp.spareparts.length : 0;
				if (spCount === 0) {
					let _pdiConfirmed = false;
					const _confirmEl = document.getElementById('optimaConfirmModal');
					const _onHidden = function() {
						_confirmEl.removeEventListener('hidden.bs.modal', _onHidden);
						if (!_pdiConfirmed) {
							const approvalModal = bootstrap.Modal.getInstance(document.getElementById('approvalStageModal'));
							if (approvalModal) approvalModal.hide();
							setTimeout(() => openInputSparepart(currentApprovalSpkId), 400);
						}
					};
					_confirmEl.addEventListener('hidden.bs.modal', _onHidden);
					OptimaConfirm.generic({
						title: 'Belum Ada Sparepart!',
						html: 'SPK ini belum memiliki data sparepart.<br>Setelah PDI selesai, Anda <strong>tidak dapat</strong> menambahkan sparepart lagi.<br><br>Lanjutkan PDI tanpa sparepart?',
						icon: 'warning',
						confirmText: '<i class="fas fa-check me-1"></i>Lanjutkan PDI',
						cancelText: '<i class="fas fa-tools me-1"></i>Tambah Sparepart',
						confirmButtonColor: '#ffc107',
						onConfirm: function() {
							_pdiConfirmed = true;
							_doApprovalFetch();
						}
					});
				} else {
					_doApprovalFetch();
				}
			}).catch(() => _doApprovalFetch());
		} else {
			_doApprovalFetch();
		}
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
				notify('Failed to load SPK data: ' + error.message, 'error');
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
				const editInstallKey = (spk.has_fabrikasi == 1) ? 'fabrikasi' : 'install';
				const editInstallName = (spk.has_fabrikasi == 1) ? 'Fabrikasi' : 'Install Attach';
				
				const allStages = [
					{ key: 'persiapan_unit', name: 'Persiapan Unit', condition: unitStages.persiapan_unit && unitStages.persiapan_unit.completed },
					{ key: editInstallKey, name: editInstallName, condition: unitStages[editInstallKey] && unitStages[editInstallKey].completed },
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
			const editInstallKey1 = (spk.has_fabrikasi == 1) ? 'fabrikasi' : 'install';
			const editInstallName1 = (spk.has_fabrikasi == 1) ? 'Fabrikasi' : 'Install Attach';
			
			const stages = [
				{ key: 'persiapan_unit', name: 'Persiapan Unit', condition: unitStages.persiapan_unit && unitStages.persiapan_unit.completed },
				{ key: editInstallKey1, name: editInstallName1, condition: unitStages[editInstallKey1] && unitStages[editInstallKey1].completed },
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
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">${typeof window.lang === 'function' ? window.lang('cancel') : 'Cancel'}</button>
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
			<div class="modal fade modal-wide" id="${newModalId}" tabindex="-1">
				<div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
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
		} else if (stage === 'fabrikasi' || stage === 'install') {
			// Buka modal fabrikasi/install
			openFabrikasiModal(spkId, stage);
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
		} else if (stage === 'fabrikasi' || stage === 'install') {
			// Buka modal fabrikasi/install dengan info unit
			openFabrikasiModalWithUnit(spkId, unitNumber, stage);
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
			'install': 'Install Dept.',
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
		.then(response => {
			if (response.status === 401) {
				notify('Session expired. Please login again.', 'error');
				window.location.href = '<?= base_url('auth/login') ?>';
				return Promise.reject('Unauthorized');
			}
			return response.json();
		})
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
	function openFabrikasiModal(spkId, stage) {
		const stageKey = stage || 'install';
		console.log('Opening Fabrication/Install Modal for SPK ID:', spkId, 'Stage:', stageKey);
		
		// Set unitIndex to 1 for editing (single unit SPK)
		currentEditingUnitIndex = 1;
		
		// Open existing approval modal with correct stage
		openApprovalModal(stageKey, 'Install Dept.', spkId, 1);
	}
	
	// Function to open fabrication modal with specific unit info
	function openFabrikasiModalWithUnit(spkId, unitNumber, stage) {
		const stageKey = stage || 'install';
		console.log('Opening Fabrication/Install Modal for SPK ID:', spkId, 'Unit:', unitNumber, 'Stage:', stageKey);
		
		// Open existing approval modal with correct stage
		openApprovalModal(stageKey, `Install Dept. (Unit ${unitNumber})`, spkId);
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
								<button class="btn btn-primary btn-sm" onclick="notify('Edit Unit Assignment clicked!', 'info')">
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
								<button class="btn btn-primary btn-sm" onclick="notify('Edit Stage Settings clicked!', 'info')">
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
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">${typeof window.lang === 'function' ? window.lang('cancel') : 'Cancel'}</button>
				<button type="button" class="btn btn-primary" onclick="notify('Save Changes clicked!', 'info')">
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
			'install': 'Install Attach',
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
			.then(response => {
				if (response.status === 401) {
					notify('Session expired. Please login again.', 'error');
					window.location.href = '<?= base_url('auth/login') ?>';
					return Promise.reject('Unauthorized');
				}
				return response.json();
			})
			.then(data => {
				if (data.success && data.data) {
					const spk = data.data;
					let infoHtml = `
						<div class="card">
							<div class="card-body">
								<h6 class="card-title">Informasi SPK</h6>
								<p><strong>Nomor SPK:</strong> ${spk.nomor_spk || '-'}</p>
								<p><strong>Status:</strong> <span class="badge ${getStatusBadgeClass(spk.status)}">${spk.status || '-'}</span></p>
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

	function initRollbackUnitSelect2() {
		const $sel = $('#rollbackUnitSelect');
		if (!$sel.length) return;
		if ($sel.hasClass('select2-hidden-accessible')) {
			$sel.select2('destroy');
		}
		$sel.empty().append($('<option value="">-- Select New Unit --</option>'));
		const rbUrl = '<?= base_url('service/data-unit/simple') ?>';
		if (typeof window.OptimaUnitSelect2 !== 'undefined') {
			$sel.select2($.extend({}, OptimaUnitSelect2.buildServiceDataUnitSimpleSelect2Config({
				url: rbUrl,
				placeholder: '-- Select New Unit --',
				dropdownParent: $('#rollbackModal .modal-content'),
				minimumInputLength: 1,
				extraSpkRow: true,
				allowedStatuses: null,
				disableAssigned: false,
				extraAjaxParams: function () { return {}; }
			}), {
				escapeMarkup: function (m) { return m; }
			}));
		}
	}

	// Load available units for edit (rollback ganti unit)
	function loadAvailableUnitsForEdit() {
		initRollbackUnitSelect2();
	}

	if (document.getElementById('rollbackModal')) {
		$('#rollbackModal').on('show.bs.modal', function () {
			initRollbackUnitSelect2();
		});
	}
	
	// Confirm rollback
	document.getElementById('confirmRollbackBtn').addEventListener('click', function() {
		const reason = document.getElementById('rollbackReason').value.trim();

		if (!reason) {
			notify('Alasan rollback wajib diisi!', 'warning');
			return;
		}
		
		if (currentRollbackStage === 'persiapan_unit') {
			const newUnitId = document.getElementById('rollbackUnitSelect').value;
			const unitIndex = document.getElementById('rollbackUnitIndexSelect').value;

			if (!newUnitId) {
				notify('New unit must be selected!', 'warning');
				return;
			}

			if (!unitIndex) {
				notify('Unit to be changed must be selected!', 'warning');
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
					reloadSpkTable(); // Reload SPK list
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
					reloadSpkTable(); // Reload SPK list
				} else {
					notify(data.message, 'error');
				}
			})
			.catch(error => {
				notify('Gagal melakukan rollback', 'error');
			});
		}
	});
	
	// Helper function to get status badge class (soft badges)
	function getStatusBadgeClass(status) {
		const map = {
			'DRAFT': 'badge-soft-gray',
			'SUBMITTED': 'badge-soft-gray',
			'IN_PROGRESS': 'badge-soft-cyan',
			'READY': 'badge-soft-green',
			'COMPLETED': 'badge-soft-blue',
			'DELIVERED': 'badge-soft-blue',
			'CANCELLED': 'badge-soft-red'
		};
		return map[status] || 'badge-soft-gray';
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
	// TODO(rollback): Implement rollback logic for spk_unit_stages.
	// Required steps:
	//   1. Add route: POST service/spk/rollback-stage/:id  → Service::rollbackStage($id)
	//   2. Controller: validate permission, find the latest spk_unit_stages row for
	//      the given spk_id + stage, set its status back to the previous state, log reason.
	//   3. Return JSON {success, message} and refresh the SPK detail panel.
	//   4. Show rollbackBtn only if the logged-in user has the 'approve_service' permission
	//      and the stage is not the first one (nothing to roll back to).
	function checkRollbackAvailability(spkId, stage) {
		// Rollback button hidden until backend route + logic is implemented (see TODO above).
		const rollbackBtn = document.getElementById('rollbackStageBtn');
		if (rollbackBtn) {
			rollbackBtn.style.display = 'none';
		}
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
								
								const serialNumber = unitInfo.serial_number || '-';
								const tipeUnit = unitInfo.tipe_unit || unitInfo.tipe || '';
								const label = `${unitInfo.no_unit || ''} - ${unitInfo.merk_unit || ''} ${unitInfo.model_unit || ''}`.trim();
								
								option.setAttribute('data-no-unit', unitInfo.no_unit || '');
								option.setAttribute('data-merk-unit', unitInfo.merk_unit || '');
								option.setAttribute('data-model-unit', unitInfo.model_unit || '');
								option.setAttribute('data-serial-number', serialNumber);
								option.setAttribute('data-tipe-unit', tipeUnit);
								
								const tokens = [label];
								if (serialNumber) tokens.push(`SN: ${serialNumber}`);
								if (tipeUnit) tokens.push(tipeUnit);
								option.textContent = tokens.filter(Boolean).join(' | ');
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

// ===== ENHANCEMENT: Smart Unit Component Management =====

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
			const batterySn = battery.serial_number || '-';
			
			html += `
				<!-- Hidden fields untuk data existing battery -->
				<input type="hidden" id="existingBatteryModelId${suffix}" value="${battery.id || ''}">
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
					<select class="form-select" id="batteryPick${suffix}" name="battery_inventory_attachment_id" data-old-battery-id="${battery.id}" style="width:100%">
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
				<select class="form-select" id="batteryPick${suffix}" name="battery_inventory_attachment_id" style="width:100%">
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
			const chargerSn = charger.serial_number || '-';
			
			html += `
				<!-- Hidden fields untuk data existing charger -->
				<input type="hidden" id="existingChargerModelId${suffix}" value="${charger.id || ''}">
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
					<select class="form-select" id="chargerPick${suffix}" name="charger_inventory_attachment_id" data-old-charger-id="${charger.id}" style="width:100%">
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
				<select class="form-select" id="chargerPick${suffix}" name="charger_inventory_attachment_id" style="width:100%">
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
								return `<option value="${item.id}">${name} • SN: ${item.serial_number||'-'}</option>`;
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
								return `<option value="${item.id}">${name} • SN: ${item.sn_attachment||'-'}</option>`;
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
// Renamed to avoid conflict with openFabrikasiModal(spkId) defined above (line ~4591)
async function openFabrikasiModalLegacy(detail) {
  const depId = Number((detail.spec?.departemen_id)||0);
  const attId = Number((detail.spec?.attachment_model_id)||0) || Number((detail.spec?.attachment_id)||0);
  // fetch attachments available
  const att = attId ? await fetch(`<?= base_url('warehouse/inventory/available-attachments') ?>?attachment_id=${attId}`).then(r=>r.json()) : [];
  const chg = depId===2 ? await fetch(`<?= base_url('warehouse/inventory/available-chargers') ?>`).then(r=>r.json()) : [];

  const attOptions = att.map(r=>{
    const name = `${r.tipe||'-'} ${r.merk||''} ${r.model||''}`.trim();
    return `<option value="${r.id}">${name} • SN: ${r.sn_attachment||'-'}</option>`;
  }).join('');
  const chgOptions = chg.map(r=>{
    const name = `${r.merk_charger||'-'} ${r.tipe_charger||''}`.trim();
    return `<option value="${r.id}">${name} • SN: ${r.serial_number||'-'}</option>`;
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
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' + (typeof window.lang === 'function' ? window.lang('cancel') : 'Cancel') + '</button>
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
    if (!res.ok || !j.success) { notify(j.message || 'Gagal', 'error'); return; }
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
		
		alertSwal('warning', `${typeLabel} yang dipilih sudah digunakan di ${conflictingUnitLabel}. Pilih ${typeLabel.toLowerCase()} yang berbeda.`, 'Duplikat Terdeteksi!');
		
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
			showComponentStatusWarning(target, 'attachment');
			updateAllDropdownAvailability('attachment');
		}
		else if (target.matches('select[name*="battery_id"], select[id*="batteryPick"]')) {
			if (!validateDuplicateSelection(target, 'battery')) return;
			showComponentStatusWarning(target, 'battery');
			updateAllDropdownAvailability('battery');
		}
		else if (target.matches('select[name*="charger_id"], select[id*="chargerPick"]')) {
			if (!validateDuplicateSelection(target, 'charger')) return;
			showComponentStatusWarning(target, 'charger');
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

/**
 * Tampilkan warning ketika user memilih komponen dengan status tertentu
 * (BROKEN / RESERVED / SPARE) di picker SPK.
 */
function showComponentStatusWarning(selectElement, type) {
	if (!selectElement || !selectElement.value) return;

	const selectedOption = selectElement.options[selectElement.selectedIndex];
	if (!selectedOption) return;

	// Status komponen bisa diletakkan di data-status, atau dibaca dari text.
	const rawStatus = (selectedOption.getAttribute('data-status') || '').toUpperCase();
	let status = rawStatus;

	// Jika belum ada data-status, coba deteksi kasar dari text option
	if (!status) {
		const text = (selectedOption.textContent || '').toUpperCase();
		if (text.includes('BROKEN')) status = 'BROKEN';
		else if (text.includes('RESERVED')) status = 'RESERVED';
		else if (text.includes('SPARE')) status = 'SPARE';
	}

	if (!status || ['AVAILABLE', 'IN_USE'].includes(status)) {
		return;
	}

	let message = '';
	if (status === 'BROKEN') {
		message = 'Item ini berstatus BROKEN (rusak).\n\nPastikan item sudah diperbaiki dan layak pakai sebelum digunakan untuk SPK ini.';
	} else if (status === 'RESERVED') {
		message = 'Item ini berstatus RESERVED (sudah dibooking).\n\nPastikan ini memang komponen yang dibooking untuk SPK / kontrak ini.';
	} else if (status === 'SPARE') {
		message = 'Item ini berstatus SPARE (stok cadangan).\n\nPastikan penggunaan item ini sudah disetujui sebelum melanjutkan.';
	}

	if (!message) return;

	const ok = window.confirm(message + '\n\nLanjutkan menggunakan item ini?');
	if (!ok) {
		// Batalkan pilihan jika user tidak setuju
		selectElement.value = '';
	}
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

	// ============================================================
	// SPK INPUT SPAREPART MODAL — matches Work Order "Items Brought"
	// ============================================================
	let _spkSpRowCount = 0;

	// ---- helpers for sparepart management panel ----
	const _SP_STAGE_COLORS  = {persiapan_unit:'primary', fabrikasi:'purple', painting:'orange', pdi:'success'};
	const _SP_STAGE_LABELS  = {persiapan_unit:'Persiapan', fabrikasi:'Fabrikasi', painting:'Painting', pdi:'PDI'};
	const _SP_STATUS_MAP    = {
		PENDING : {cls:'badge-soft-yellow', label:'Menunggu'},
		DIAMBIL : {cls:'badge-soft-green',  label:'Diambil'},
		KOSONG  : {cls:'badge-soft-red',    label:'Kosong'},
	};
	const _SP_BASE = '<?= base_url('service/spk/sparepart/') ?>';
	const _SP_CSRF = () => ({[window.csrfTokenName]: window.csrfTokenValue});

	function _renderSpHistory(spareparts) {
		const historySection = document.getElementById('existingSparepartHistory');
		const historyBody    = document.getElementById('existingSparepartHistoryBody');
		const historyCount   = document.getElementById('existingSparepartCount');

		if (!spareparts || spareparts.length === 0) {
			historySection.style.display = 'none';
			return;
		}

		let pendingCount  = spareparts.filter(i => i.pickup_status === 'PENDING').length;
		let diambilCount  = spareparts.filter(i => i.pickup_status === 'DIAMBIL').length;
		let kosongCount   = spareparts.filter(i => i.pickup_status === 'KOSONG').length;

		const rows = spareparts.map(item => {
			const st   = _SP_STATUS_MAP[item.pickup_status] || _SP_STATUS_MAP.PENDING;
			const stage = _SP_STAGE_LABELS[item.stage_name] || item.stage_name || '-';
			const stageCls = _SP_STAGE_COLORS[item.stage_name] || 'gray';
			const src  = parseInt(item.is_from_warehouse) === 1
				? '<span class="badge badge-soft-blue" style="font-size:10px">WH</span>'
				: '<span class="badge badge-soft-yellow" style="font-size:10px">Non-WH</span>';

			// Row style by status
			let rowStyle = '';
			if (item.pickup_status === 'DIAMBIL') rowStyle = 'background:rgba(25,135,84,0.06)';
			if (item.pickup_status === 'KOSONG')  rowStyle = 'background:rgba(220,53,69,0.06)';

			// Action buttons
			let actionBtns = '';
			if (item.pickup_status === 'PENDING') {
				actionBtns = `
					<button class="btn btn-xs btn-outline-success py-0 px-1 sp-status-btn"
						data-id="${item.id}" data-status="DIAMBIL" title="Tandai Diambil">
						<i class="fas fa-check"></i> Diambil
					</button>
					<button class="btn btn-xs btn-outline-danger py-0 px-1 ms-1 sp-status-btn"
						data-id="${item.id}" data-status="KOSONG" title="Tandai Kosong">
						<i class="fas fa-times"></i> Kosong
					</button>`;
			} else if (item.pickup_status === 'DIAMBIL') {
				actionBtns = `
					<button class="btn btn-xs btn-outline-warning py-0 px-1 sp-status-btn"
						data-id="${item.id}" data-status="PENDING" title="Reset ke Menunggu">
						<i class="fas fa-undo"></i>
					</button>`;
			} else if (item.pickup_status === 'KOSONG') {
				actionBtns = `
					<button class="btn btn-xs btn-outline-warning py-0 px-1 sp-status-btn"
						data-id="${item.id}" data-status="PENDING" title="Reset ke Menunggu">
						<i class="fas fa-undo"></i>
					</button>
					<button class="btn btn-xs btn-outline-danger py-0 px-1 ms-1 sp-delete-btn"
						data-id="${item.id}" title="Hapus Item">
						<i class="fas fa-trash"></i>
					</button>`;
			}

			return `<tr style="${rowStyle}" data-sp-id="${item.id}">
				<td class="small text-capitalize">${item.item_type||'-'}</td>
				<td class="small fw-semibold">${item.sparepart_name||item.sparepart_code||'-'}</td>
				<td class="small text-center">${item.quantity_brought||0} <span class="text-muted">${item.satuan||''}</span></td>
				<td class="small">${src}</td>
				<td class="small"><span class="badge badge-soft-${stageCls}" style="font-size:10px">${stage}</span></td>
				<td class="small"><span class="badge ${st.cls}" style="font-size:10px">${st.label}</span></td>
				<td class="small text-nowrap">${actionBtns}</td>
			</tr>`;
		}).join('');

		historyBody.innerHTML = `
			<div class="d-flex gap-2 px-2 pt-2 pb-1 flex-wrap">
				<span class="badge badge-soft-yellow" style="font-size:11px"><i class="fas fa-clock me-1"></i>Menunggu: ${pendingCount}</span>
				<span class="badge badge-soft-green"  style="font-size:11px"><i class="fas fa-check me-1"></i>Diambil: ${diambilCount}</span>
				<span class="badge badge-soft-red"    style="font-size:11px"><i class="fas fa-times me-1"></i>Kosong: ${kosongCount}</span>
			</div>
			<table class="table table-sm table-bordered mb-0" style="font-size:12px">
				<thead class="table-light">
					<tr>
						<th>Type</th><th>Item</th><th class="text-center">Qty</th>
						<th>Sumber</th><th>Stage</th><th>Status</th><th>Aksi</th>
					</tr>
				</thead>
				<tbody>${rows}</tbody>
			</table>`;

		historyCount.textContent = spareparts.length;
		historySection.style.display = 'block';
	}

	// Delegated events for status update & delete buttons inside history panel
	document.getElementById('existingSparepartHistoryBody').addEventListener('click', async function(e) {
		const statusBtn = e.target.closest('.sp-status-btn');
		const deleteBtn = e.target.closest('.sp-delete-btn');

		if (statusBtn) {
			const id     = statusBtn.dataset.id;
			const status = statusBtn.dataset.status;
			statusBtn.disabled = true;
			const fd = new FormData();
			fd.append('pickup_status', status);
			Object.entries(_SP_CSRF()).forEach(([k,v]) => fd.append(k, v));
			try {
				const r = await fetch(_SP_BASE + id + '/status', {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
				const d = await r.json();
				if (d.success) {
					// Reload the history for this SPK
					const spkId = document.getElementById('inputSparepartSpkId').value;
					_reloadSpHistory(spkId);
				} else {
					notify(d.message || 'Gagal memperbarui status', 'error');
					statusBtn.disabled = false;
				}
			} catch { notify('Terjadi kesalahan', 'error'); statusBtn.disabled = false; }
		}

		if (deleteBtn) {
			const id = deleteBtn.dataset.id;
			if (!confirm('Hapus item ini? Tindakan tidak dapat dibatalkan.')) return;
			deleteBtn.disabled = true;
			const fd = new FormData();
			Object.entries(_SP_CSRF()).forEach(([k,v]) => fd.append(k, v));
			try {
				const r = await fetch(_SP_BASE + id + '/delete', {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
				const d = await r.json();
				if (d.success) {
					const spkId = document.getElementById('inputSparepartSpkId').value;
					_reloadSpHistory(spkId);
				} else {
					notify(d.message || 'Gagal menghapus item', 'error');
					deleteBtn.disabled = false;
				}
			} catch { notify('Terjadi kesalahan', 'error'); deleteBtn.disabled = false; }
		}
	});

	function _reloadSpHistory(spkId) {
		const historyBody = document.getElementById('existingSparepartHistoryBody');
		historyBody.innerHTML = '<div class="text-muted small p-2"><i class="fas fa-spinner fa-spin me-1"></i>Memuat...</div>';
		fetch(`<?= base_url('service/spk/get-spareparts/') ?>${spkId}`, {headers:{'X-Requested-With':'XMLHttpRequest'}})
			.then(r => r.json())
			.then(sp => { if (sp.success) _renderSpHistory(sp.spareparts); })
			.catch(() => {});
	}

	window.openInputSparepart = function(id, nomor_spk, unit_id) {
		_spkSpRowCount = 0;
		document.getElementById('inputSparepartSpkId').value = id;
		document.getElementById('inputSparepartSpkNumber').textContent = nomor_spk || ('#' + id);
		document.getElementById('inputSparepartNotes').value = '';
		document.getElementById('sparepartInputRows').innerHTML = '';
		_addSpkSparepartRow();

		// Load & tampilkan panel manajemen sparepart tersimpan
		const historyBody = document.getElementById('existingSparepartHistoryBody');
		historyBody.innerHTML = '<div class="text-muted small p-2"><i class="fas fa-spinner fa-spin me-1"></i>Memuat...</div>';
		document.getElementById('existingSparepartHistory').style.display = 'none';

		fetch(`<?= base_url('service/spk/get-spareparts/') ?>${id}`, {
			headers: {'X-Requested-With': 'XMLHttpRequest'}
		}).then(r => r.json()).then(sp => {
			if (sp.success) _renderSpHistory(sp.spareparts);
		}).catch(() => {});

		bootstrap.Modal.getOrCreateInstance(document.getElementById('spkInputSparepartModal')).show();
	};

	document.getElementById('btnAddSparepartRow').addEventListener('click', function() {
		_addSpkSparepartRow();
	});

	$(document).on('click', '.spk-remove-sp-row', function() {
		$(this).closest('tr').remove();
	});

	function _addSpkSparepartRow() {
		_spkSpRowCount++;
		const rc = _spkSpRowCount;

		const row = `
			<tr>
				<td>
					<select class="form-select form-select-sm" name="sp_item_type_${rc}"
							id="spk_item_type_${rc}"
							onchange="switchSpkItemInput(${rc})" required>
						<option value="sparepart" selected>Sparepart</option>
						<option value="tool">Tool</option>
					</select>
				</td>
				<td>
					<div id="spk_item_input_${rc}">
						<select class="form-select form-select-sm"
								name="sp_name_${rc}"
								id="spk_sparepart_${rc}"
								required>
							<option value="">-- Select Sparepart --</option>
						</select>
						<input type="text"
							   class="form-control form-control-sm d-none"
							   id="spk_sparepart_manual_${rc}"
							   placeholder="Ketik nama sparepart manual"
							   maxlength="255">
						<input type="text"
							   class="form-control form-control-sm d-none"
							   id="spk_tool_input_${rc}"
							   placeholder="e.g., Kunci Inggris 12mm"
							   maxlength="255">
					</div>
				</td>
				<td>
					<input type="number" class="form-control form-control-sm"
						   name="sp_qty_${rc}" value="1" min="1" required>
				</td>
				<td>
					<select class="form-select form-select-sm" name="sp_satuan_${rc}" required>
						<option value="PCS" selected>PCS</option>
						<option value="UNIT">UNIT</option>
						<option value="SET">SET</option>
						<option value="PASANG">PASANG</option>
						<option value="KG">KG</option>
						<option value="GRAM">GRAM</option>
						<option value="METER">METER</option>
						<option value="CM">CM</option>
						<option value="LITER">LITER</option>
						<option value="ML">ML</option>
					</select>
				</td>
				<td>
					<select class="form-select form-select-sm"
							name="sp_source_${rc}"
							id="spk_source_${rc}"
							onchange="toggleSpkKanibalFields(${rc})"
							required>
						<option value="WAREHOUSE" selected>Warehouse</option>
						<option value="BEKAS">Bekas</option>
						<option value="KANIBAL">Kanibal</option>
					</select>
				</td>
				<td>
					<div class="d-none" id="spk_kanibal_fields_${rc}">
						<select class="form-select form-select-sm mb-1"
								id="spk_kanibal_unit_${rc}"
								style="width:100%">
							<option value="">-- Pilih Unit Sumber --</option>
						</select>
						<textarea class="form-control form-control-sm"
								  id="spk_kanibal_reason_${rc}"
								  rows="1"
								  placeholder="Alasan kanibal *"
								  maxlength="255"></textarea>
					</div>
					<div id="spk_notes_${rc}">
						<input type="text" class="form-control form-control-sm"
							   name="sp_notes_${rc}"
							   placeholder="Optional notes..."
							   maxlength="255">
					</div>
				</td>
				<td>
					<button type="button" class="btn btn-danger btn-sm spk-remove-sp-row">
						<i class="fas fa-times"></i>
					</button>
				</td>
			</tr>
		`;

		$('#sparepartInputRows').append(row);

		// Initialize Select2 with AJAX for sparepart dropdown
		setTimeout(function() {
			const $sel = $(`#spk_sparepart_${rc}`);
			if (!$sel.hasClass('select2-hidden-accessible')) {
				$sel.select2({
					placeholder: '-- Ketik untuk cari sparepart --',
					allowClear: true,
					width: '100%',
					dropdownParent: $('#spkInputSparepartModal'),
					minimumInputLength: 2,
					ajax: {
						url: '<?= base_url('service/work-orders/search-spareparts') ?>',
						dataType: 'json',
						delay: 250,
						data: function(params) { return { q: params.term, page: params.page || 1 }; },
						processResults: function(data, params) {
							params.page = params.page || 1;
							return { results: data.results, pagination: { more: data.pagination.more } };
						},
						cache: true
					},
					language: {
						inputTooShort: function() { return 'Ketik minimal 2 karakter...'; },
						searching: function() { return 'Mencari sparepart...'; },
						noResults: function() { return 'Tidak ada sparepart ditemukan'; },
						loadingMore: function() { return 'Memuat lebih banyak...'; }
					}
				});
			}
		}, 150);
	}

	// Toggle sparepart / tool input type
	window.switchSpkItemInput = function(rc) {
		const type = $(`#spk_item_type_${rc}`).val();
		const $drop = $(`#spk_sparepart_${rc}`);
		const $manual = $(`#spk_sparepart_manual_${rc}`);
		const $tool = $(`#spk_tool_input_${rc}`);

		if (type === 'tool') {
			$drop.addClass('d-none').removeAttr('required').removeAttr('name');
			$manual.addClass('d-none').removeAttr('required').removeAttr('name');
			$tool.removeClass('d-none').attr('required', 'required').attr('name', `sp_name_${rc}`);
			if ($drop.hasClass('select2-hidden-accessible')) $drop.select2('destroy');
		} else {
			$tool.addClass('d-none').removeAttr('required').removeAttr('name');
			$manual.addClass('d-none').removeAttr('required').removeAttr('name');
			$drop.removeClass('d-none').attr('required', 'required').attr('name', `sp_name_${rc}`);
		}
	};

	// Toggle KANIBAL fields
	window.toggleSpkKanibalFields = function(rc) {
		const val = $(`#spk_source_${rc}`).val();
		const $kanibal = $(`#spk_kanibal_fields_${rc}`);
		const $notes   = $(`#spk_notes_${rc}`);
		const $unitSel = $(`#spk_kanibal_unit_${rc}`);

		if (val === 'KANIBAL') {
			$kanibal.removeClass('d-none');
			$notes.addClass('d-none');
			// Init Select2 AJAX for unit dropdown if not yet done
			if (!$unitSel.hasClass('select2-hidden-accessible')) {
				if (typeof window.OptimaUnitSelect2 !== 'undefined' && typeof OptimaUnitSelect2.buildWorkOrderSearchUnitsSelect2Config === 'function') {
					$unitSel.select2(OptimaUnitSelect2.buildWorkOrderSearchUnitsSelect2Config({
						url: '<?= base_url('service/work-orders/search-units') ?>',
						dropdownParent: $('#spkInputSparepartModal'),
						csrfTokenName: window.csrfTokenName,
						csrfTokenValue: window.csrfTokenValue,
						language: {
							inputTooShort: function() { return 'Ketik minimal 1 karakter...'; },
							searching: function() { return 'Mencari unit...'; },
							noResults: function() { return 'Unit tidak ditemukan'; }
						}
					}));
				} else {
					console.warn('unit-select2.js tidak termuat — kanibal unit memakai tampilan teks polos.');
					$unitSel.select2({
						placeholder: '-- Cari No. Unit / SN / Model / Tipe --',
						allowClear: true,
						width: '100%',
						dropdownParent: $('#spkInputSparepartModal'),
						minimumInputLength: 1,
						ajax: {
							url: '<?= base_url('service/work-orders/search-units') ?>',
							type: 'POST',
							dataType: 'json',
							delay: 300,
							data: function(params) {
								return {
									[window.csrfTokenName]: window.csrfTokenValue,
									query: params.term
								};
							},
							processResults: function(data) {
								if (!data.success) return { results: [] };
								return {
									results: data.data.map(u => ({
										id: u.no_unit,
										text: u.no_unit + (u.pelanggan ? ' – ' + u.pelanggan : '') + (u.merk_unit ? ' (' + u.merk_unit + ')' : '')
									}))
								};
							},
							cache: true
						},
						language: {
							inputTooShort: function() { return 'Ketik minimal 1 karakter...'; },
							searching: function() { return 'Mencari unit...'; },
							noResults: function() { return 'Unit tidak ditemukan'; }
						}
					});
				}
			}
		} else {
			$kanibal.addClass('d-none');
			$notes.removeClass('d-none');
			if ($unitSel.hasClass('select2-hidden-accessible')) {
				$unitSel.val(null).trigger('change');
			}
		}
	};

	// Manual entry button when Select2 opens
	$(document).on('select2:open', '[id^="spk_sparepart_"]:not([id$="_manual"])', function() {
		const $select = $(this);
		const rc = $select.attr('id').replace('spk_sparepart_', '');
		const $dropdown = $('.select2-dropdown:last');
		$dropdown.find('.spk-manual-entry-btn').remove();
		const btn = $(`<div class="spk-manual-entry-btn" style="padding:10px 15px;cursor:pointer;border-top:1px solid #dee2e6;background:#e3f2fd;text-align:center;font-weight:600;color:#0d47a1;">
			<i class="fas fa-pencil-alt me-2"></i>📝 Input Manual Sparepart
		</div>`);
		btn.on('click', function() {
			$select.select2('close');
			const $manual = $(`#spk_sparepart_manual_${rc}`);
			$select.addClass('d-none').removeAttr('name').removeAttr('required');
			if ($select.hasClass('select2-hidden-accessible')) $select.select2('destroy');
			$manual.removeClass('d-none').attr('name', `sp_name_${rc}`).attr('required', 'required').focus();
		});
		const $search = $dropdown.find('.select2-search');
		if ($search.length) $search.after(btn);
		else $dropdown.find('.select2-results').prepend(btn);
	});

	document.getElementById('btnSubmitSparepartInput').addEventListener('click', function() {
		const rows  = document.querySelectorAll('#sparepartInputRows tr');
		const items = [];
		let valid   = true;

		rows.forEach((row, i) => {
			const rowNum  = i + 1;
			const rc      = row.querySelector('[id^="spk_item_type_"]')?.id.replace('spk_item_type_', '');
			if (!rc) return;

			const itype   = document.getElementById(`spk_item_type_${rc}`)?.value || 'sparepart';
			const source  = document.getElementById(`spk_source_${rc}`)?.value || 'WAREHOUSE';

			// Get item name depending on type
			let name = '';
			let sparepartCode = '';
			if (itype === 'tool') {
				name = document.getElementById(`spk_tool_input_${rc}`)?.value.trim() || '';
			} else {
				const $sel = $(`#spk_sparepart_${rc}`);
				const $manual = $(`#spk_sparepart_manual_${rc}`);
				if ($manual.is(':visible')) {
					name = $manual.val()?.trim() || '';
				} else {
					// Use Select2 data first (has kode/desc props from AJAX response)
					const selData = $sel.select2('data');
					if (selData && selData[0] && selData[0].kode) {
						sparepartCode = selData[0].kode;
						name = selData[0].desc || selData[0].text?.trim() || '';
					} else {
						// Fallback: parse "CODE - NAME" format from value string
						const rawVal = $sel.val()?.trim() || '';
						const dashIdx = rawVal.indexOf(' - ');
						if (dashIdx > -1) {
							sparepartCode = rawVal.substring(0, dashIdx);
							name = rawVal.substring(dashIdx + 3);
						} else {
							name = rawVal;
						}
					}
				}
			}

			const qty     = parseInt(row.querySelector(`[name="sp_qty_${rc}"]`)?.value) || 0;
			const satuan  = row.querySelector(`[name="sp_satuan_${rc}"]`)?.value?.trim() || '';
			const notes   = row.querySelector(`[name="sp_notes_${rc}"]`)?.value?.trim() || '';
			const $unitSel = $(`#spk_kanibal_unit_${rc}`);
			const unitNo  = $unitSel.hasClass('select2-hidden-accessible') ? ($unitSel.val() || '') : ($unitSel[0]?.value || '');
			const reason  = document.getElementById(`spk_kanibal_reason_${rc}`)?.value.trim() || '';

			if (!name)                                          { valid = false; notify(`Item Name kosong (baris ${rowNum})`, 'error'); return; }
			if (qty < 1)                                        { valid = false; notify(`Qty harus > 0 (baris ${rowNum})`, 'error'); return; }
			if (!satuan)                                        { valid = false; notify(`Unit kosong (baris ${rowNum})`, 'error'); return; }
			if (source === 'KANIBAL' && !unitNo)                { valid = false; notify(`No. Unit Kanibal wajib diisi (baris ${rowNum})`, 'error'); return; }

			items.push({
				sparepart_name   : name,
				sparepart_code   : sparepartCode,
				item_type        : itype,
				source_type      : source,
				quantity_brought : qty,
				satuan           : satuan,
				source_unit_no   : source === 'KANIBAL' ? unitNo  : '',
				source_notes     : source === 'KANIBAL' ? reason  : notes,
				is_from_warehouse: source === 'WAREHOUSE' ? 1 : 0
			});
		});

		if (!valid || items.length === 0) return;

		const btn    = this;
		const spkId     = document.getElementById('inputSparepartSpkId').value;
		const notes     = document.getElementById('inputSparepartNotes').value.trim();
		const stageName = document.querySelector('input[name="sparepart_stage"]:checked')?.value || 'persiapan_unit';

		btn.disabled = true;
		btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';

		const fd = new FormData();
		fd.append(window.csrfTokenName, window.csrfTokenValue);
		fd.append('notes', notes);
		fd.append('stage_name', stageName);
		items.forEach((item, i) => {
			Object.keys(item).forEach(key => fd.append(`items[${i}][${key}]`, item[key]));
		});

		fetch(window.baseUrl + 'service/spk/save-sparepart/' + spkId, {
			method : 'POST',
			headers: {'X-Requested-With': 'XMLHttpRequest'},
			body   : fd
		}).then(r => r.json()).then(res => {
			btn.disabled = false;
			btn.innerHTML = '<i class="fas fa-save me-1"></i>Simpan Sparepart';
			if (res.success) {
				notify(res.message || 'Sparepart berhasil disimpan', 'success');
				// Reset form rows and reload management panel
				document.getElementById('sparepartInputRows').innerHTML = '';
				_spkSpRowCount = 0;
				_addSpkSparepartRow();
				_reloadSpHistory(spkId);
				window.reloadSpkTable();
			} else {
				notify(res.message || 'Gagal menyimpan sparepart', 'error');
			}
		}).catch(err => {
			btn.disabled = false;
			btn.innerHTML = '<i class="fas fa-save me-1"></i>Simpan Sparepart';
			notify('Error: ' + err.message, 'error');
		});
	});

	// ==========================================
	// SPAREPART VALIDATION MODAL (after PDI)
	// ==========================================

	/**
	 * Check if SPK has unvalidated spareparts; show modal if needed.
	 */
	window.checkAndTriggerSparepartValidation = function(spkId, spkNumber) {
		if (!spkId) return;
		fetch(window.baseUrl + 'service/spk/check-spareparts/' + spkId, {
			headers: {'X-Requested-With': 'XMLHttpRequest'}
		})
		.then(r => r.json())
		.then(res => {
			if (res.success && res.has_spareparts && !res.all_validated) {
				showSparepartValidationModal(spkId, spkNumber);
			}
		})
		.catch(err => console.error('checkSpkSpareparts error:', err));
	};

	window.showSparepartValidationModal = function(spkId, spkNumber) {
		document.getElementById('validationSpkId').value   = spkId;
		document.getElementById('validationSpkNumber').textContent = spkNumber;
		document.getElementById('validationNotes').value   = '';
		document.getElementById('additionalSparepartRowsSpk').innerHTML = '<tr id="noAdditionalSpkRow"><td colspan="7" class="text-center text-muted py-2"><small>Belum ada sparepart tambahan. Klik "Tambah Baris" untuk menambah.</small></td></tr>';
		_vldSpRowCount = 0;
		loadSparepartsForValidation(spkId);
		bootstrap.Modal.getOrCreateInstance(document.getElementById('spkSparepartValidationModal')).show();
	};

	function loadSparepartsForValidation(spkId) {
		const tbody = document.getElementById('sparepartValidationRows');
		tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-3"><i class="fas fa-spinner fa-spin me-2"></i>Memuat data...</td></tr>';

		fetch(window.baseUrl + 'service/spk/get-spareparts/' + spkId, {
			headers: {'X-Requested-With': 'XMLHttpRequest'}
		})
		.then(r => r.json())
		.then(res => {
			if (res.success) {
				populateValidationTable(res.spareparts, res.units || []);
			} else {
				tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger py-3">Gagal memuat sparepart</td></tr>';
			}
		})
		.catch(() => {
			tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger py-3">Error memuat data</td></tr>';
		});
	}

	function populateValidationTable(spareparts, units) {
		const tbody = document.getElementById('sparepartValidationRows');
		if (!spareparts || spareparts.length === 0) {
			tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-3">Tidak ada sparepart terdaftar</td></tr>';
			return;
		}
		// Build unit options HTML once
		const unitOptions = (units || []).map(u =>
			`<option value="${u.unit_id}">${esc(u.no_unit || 'Unit ' + u.unit_index)}</option>`
		).join('');
		const unitSelectHtml = unitOptions
			? `<select class="form-select form-select-sm unit-select"><option value="">- Pilih Unit -</option>${unitOptions}</select>`
			: '<span class="text-muted small">-</span>';

		tbody.innerHTML = '';
		spareparts.forEach((sp, idx) => {
			const isValidated = parseInt(sp.sparepart_validated) === 1;
			const brought     = parseInt(sp.quantity_brought) || 0;
			const usedVal     = parseInt(sp.quantity_used)    || 0;
			const returnVal   = brought - usedVal;
			const statusBadge = isValidated
				? '<span class="badge badge-soft-green">Validated</span>'
				: '<span class="badge badge-soft-yellow">Belum Validasi</span>';
			const typeLabel = sp.item_type === 'tool' ? '<span class="badge badge-soft-cyan">Tool</span>' : '<span class="badge badge-soft-blue">Sparepart</span>';
			const sourceNote = sp.source_unit_no ? ` (${sp.source_unit_no})` : '';
			const inputAttrs = isValidated ? `value="${usedVal}" readonly class="form-control form-control-sm text-center bg-light"` : `value="${brought}" min="0" max="${brought}" class="form-control form-control-sm text-center qty-used-input"`;
			const unitCell   = isValidated
				? `<span class="badge badge-soft-blue">${esc(sp.unit_no || '-')}</span>`
				: unitSelectHtml;

			tbody.innerHTML += `
				<tr data-sparepart-id="${sp.id}" data-brought="${brought}" data-validated="${isValidated ? 1 : 0}">
					<td class="text-center">${idx + 1}</td>
					<td>${typeLabel}</td>
					<td>${esc(sp.sparepart_name)}${sourceNote}</td>
					<td class="text-center fw-bold text-primary">${brought}</td>
					<td class="text-center"><input type="number" ${inputAttrs} oninput="calculateReturn(this)"></td>
					<td class="text-center fw-bold return-qty" style="${returnVal > 0 ? 'color:#0d6efd;' : ''}">${isValidated ? returnVal : 0}</td>
					<td>${unitCell}</td>
					<td>${esc(sp.satuan)}</td>
					<td>${statusBadge}</td>
				</tr>`;
		});
	}

	function esc(str) {
		const d = document.createElement('div');
		d.textContent = str || '';
		return d.innerHTML;
	}

	window.calculateReturn = function(input) {
		const row     = input.closest('tr');
		const brought = parseInt(row.dataset.brought)  || 0;
		let used      = parseInt(input.value)           || 0;
		if (used < 0)       { used = 0; input.value = 0; }
		if (used > brought) { used = brought; input.value = brought; }
		const returnQty = brought - used;
		const returnCell = row.querySelector('.return-qty');
		returnCell.textContent = returnQty;
		returnCell.style.color = returnQty > 0 ? '#0d6efd' : '';
		returnCell.style.fontWeight = returnQty > 0 ? 'bold' : '';
	};

	// Additional sparepart rows counter (validation modal)
	let _vldSpRowCount = 0;

	function _addValidationAdditionalRow() {
		_vldSpRowCount++;
		const rc = _vldSpRowCount;
		document.getElementById('noAdditionalSpkRow')?.remove();

		const row = `
			<tr id="vld_row_${rc}">
				<td>
					<select class="form-select form-select-sm" name="vld_item_type_${rc}"
							id="vld_item_type_${rc}"
							onchange="switchVldItemInput(${rc})" required>
						<option value="sparepart" selected>Sparepart</option>
						<option value="tool">Tool</option>
					</select>
				</td>
				<td>
					<div id="vld_item_input_${rc}">
						<select class="form-select form-select-sm"
								name="vld_name_${rc}"
								id="vld_sparepart_${rc}"
								required>
							<option value="">-- Select Sparepart --</option>
						</select>
						<input type="text" class="form-control form-control-sm d-none"
							   id="vld_sparepart_manual_${rc}" placeholder="Ketik nama sparepart manual" maxlength="255">
						<input type="text" class="form-control form-control-sm d-none"
							   id="vld_tool_input_${rc}" placeholder="e.g., Kunci Inggris 12mm" maxlength="255">
					</div>
				</td>
				<td>
					<input type="number" class="form-control form-control-sm"
						   name="vld_qty_${rc}" value="1" min="1" required>
				</td>
				<td>
					<select class="form-select form-select-sm" name="vld_satuan_${rc}" required>
						<option value="PCS" selected>PCS</option>
						<option value="UNIT">UNIT</option>
						<option value="SET">SET</option>
						<option value="PASANG">PASANG</option>
						<option value="KG">KG</option>
						<option value="GRAM">GRAM</option>
						<option value="METER">METER</option>
						<option value="CM">CM</option>
						<option value="LITER">LITER</option>
						<option value="ML">ML</option>
					</select>
				</td>
				<td>
					<select class="form-select form-select-sm"
							name="vld_source_${rc}"
							id="vld_source_${rc}"
							onchange="toggleVldKanibalFields(${rc})" required>
						<option value="WAREHOUSE" selected>Warehouse</option>
						<option value="BEKAS">Bekas</option>
						<option value="KANIBAL">Kanibal</option>
					</select>
				</td>
				<td>
					<div class="d-none" id="vld_kanibal_fields_${rc}">
						<select class="form-select form-select-sm mb-1"
								id="vld_kanibal_unit_${rc}" style="width:100%">
							<option value="">-- Pilih Unit Sumber --</option>
						</select>
						<textarea class="form-control form-control-sm"
								  id="vld_kanibal_reason_${rc}" rows="1"
								  placeholder="Alasan kanibal *" maxlength="255"></textarea>
					</div>
					<div id="vld_notes_${rc}">
						<input type="text" class="form-control form-control-sm"
							   name="vld_notes_${rc}" placeholder="Optional notes..." maxlength="255">
					</div>
				</td>
				<td>
					<button type="button" class="btn btn-danger btn-sm vld-remove-sp-row">
						<i class="fas fa-times"></i>
					</button>
				</td>
			</tr>`;

		$('#additionalSparepartRowsSpk').append(row);

		setTimeout(function() {
			const $sel = $(`#vld_sparepart_${rc}`);
			if ($sel.length && !$sel.hasClass('select2-hidden-accessible')) {
				$sel.select2({
					placeholder: '-- Ketik untuk cari sparepart --',
					allowClear: true,
					width: '100%',
					dropdownParent: $('#spkSparepartValidationModal'),
					minimumInputLength: 2,
					ajax: {
						url: '<?= base_url('service/work-orders/search-spareparts') ?>',
						dataType: 'json',
						delay: 250,
						data: function(params) { return { q: params.term, page: params.page || 1 }; },
						processResults: function(data, params) {
							params.page = params.page || 1;
							return { results: data.results, pagination: { more: data.pagination.more } };
						},
						cache: true
					},
					language: {
						inputTooShort: function() { return 'Ketik minimal 2 karakter...'; },
						searching:     function() { return 'Mencari sparepart...'; },
						noResults:     function() { return 'Tidak ada sparepart ditemukan'; },
						loadingMore:   function() { return 'Memuat lebih banyak...'; }
					}
				});
			}
		}, 150);
	}

	window.switchVldItemInput = function(rc) {
		const type    = $(`#vld_item_type_${rc}`).val();
		const $drop   = $(`#vld_sparepart_${rc}`);
		const $manual = $(`#vld_sparepart_manual_${rc}`);
		const $tool   = $(`#vld_tool_input_${rc}`);
		if (type === 'tool') {
			$drop.addClass('d-none').removeAttr('required').removeAttr('name');
			$manual.addClass('d-none').removeAttr('required').removeAttr('name');
			$tool.removeClass('d-none').attr('required','required').attr('name',`vld_name_${rc}`);
			if ($drop.hasClass('select2-hidden-accessible')) $drop.select2('destroy');
		} else {
			$tool.addClass('d-none').removeAttr('required').removeAttr('name');
			$manual.addClass('d-none').removeAttr('required').removeAttr('name');
			$drop.removeClass('d-none').attr('required','required').attr('name',`vld_name_${rc}`);
		}
	};

	window.toggleVldKanibalFields = function(rc) {
		const val      = $(`#vld_source_${rc}`).val();
		const $kanibal = $(`#vld_kanibal_fields_${rc}`);
		const $notes   = $(`#vld_notes_${rc}`);
		const $unitSel = $(`#vld_kanibal_unit_${rc}`);
		if (val === 'KANIBAL') {
			$kanibal.removeClass('d-none');
			$notes.addClass('d-none');
			if (!$unitSel.hasClass('select2-hidden-accessible')) {
				if (typeof window.OptimaUnitSelect2 !== 'undefined' && typeof OptimaUnitSelect2.buildWorkOrderSearchUnitsSelect2Config === 'function') {
					$unitSel.select2(OptimaUnitSelect2.buildWorkOrderSearchUnitsSelect2Config({
						url: '<?= base_url('service/work-orders/search-units') ?>',
						dropdownParent: $('#spkSparepartValidationModal'),
						csrfTokenName: window.csrfTokenName,
						csrfTokenValue: window.csrfTokenValue,
						language: {
							inputTooShort: function() { return 'Ketik minimal 1 karakter...'; },
							searching:     function() { return 'Mencari unit...'; },
							noResults:     function() { return 'Unit tidak ditemukan'; }
						}
					}));
				} else {
					$unitSel.select2({
						placeholder: '-- Cari No. Unit / SN / Model / Tipe --',
						allowClear: true,
						width: '100%',
						dropdownParent: $('#spkSparepartValidationModal'),
						minimumInputLength: 1,
						ajax: {
							url: '<?= base_url('service/work-orders/search-units') ?>',
							type: 'POST',
							dataType: 'json',
							delay: 300,
							data: function(params) {
								return { [window.csrfTokenName]: window.csrfTokenValue, query: params.term };
							},
							processResults: function(data) {
								if (!data.success) return { results: [] };
								return {
									results: data.data.map(u => ({
										id: u.no_unit,
										text: u.no_unit + (u.pelanggan ? ' – ' + u.pelanggan : '')
									}))
								};
							},
							cache: true
						},
						language: {
							inputTooShort: function() { return 'Ketik minimal 1 karakter...'; },
							searching:     function() { return 'Mencari unit...'; },
							noResults:     function() { return 'Unit tidak ditemukan'; }
						}
					});
				}
			}
		} else {
			$kanibal.addClass('d-none');
			$notes.removeClass('d-none');
			if ($unitSel.hasClass('select2-hidden-accessible')) $unitSel.val(null).trigger('change');
		}
	};

	document.getElementById('btnAddAdditionalSparepartSpk').addEventListener('click', function() {
		_addValidationAdditionalRow();
	});

	$(document).on('click', '.vld-remove-sp-row', function() {
		$(this).closest('tr').remove();
		if ($('#additionalSparepartRowsSpk tr').length === 0) {
			$('#additionalSparepartRowsSpk').html(`<tr id="noAdditionalSpkRow"><td colspan="7" class="text-center text-muted py-2"><small>Belum ada sparepart tambahan. Klik "Tambah Baris" untuk menambah.</small></td></tr>`);
		}
	});

	// Manual entry button for validation modal sparepart selects
	$(document).on('select2:open', '[id^="vld_sparepart_"]:not([id$="_manual"])', function() {
		const $select = $(this);
		const rc = $select.attr('id').replace('vld_sparepart_', '');
		const $dropdown = $('.select2-dropdown:last');
		$dropdown.find('.vld-manual-entry-btn').remove();
		const btn = $(`<div class="vld-manual-entry-btn" style="padding:10px 15px;cursor:pointer;border-top:1px solid #dee2e6;background:#e3f2fd;text-align:center;font-weight:600;color:#0d47a1;">
			<i class="fas fa-pencil-alt me-2"></i>📝 Input Manual Sparepart
		</div>`);
		btn.on('click', function() {
			$select.select2('close');
			const $manual = $(`#vld_sparepart_manual_${rc}`);
			$select.addClass('d-none').removeAttr('name').removeAttr('required');
			if ($select.hasClass('select2-hidden-accessible')) $select.select2('destroy');
			$manual.removeClass('d-none').attr('name', `vld_name_${rc}`).attr('required','required').focus();
		});
		const $search = $dropdown.find('.select2-search');
		if ($search.length) $search.after(btn);
		else $dropdown.find('.select2-results').prepend(btn);
	});

	document.getElementById('saveSparepartValidationBtn').addEventListener('click', function() {
		const spkId = document.getElementById('validationSpkId').value;
		const notes = document.getElementById('validationNotes').value.trim();
		const rows  = document.querySelectorAll('#sparepartValidationRows tr[data-sparepart-id]');

		const validationData = [];
		let hasError = false;

		rows.forEach(row => {
			if (parseInt(row.dataset.validated) === 1) return; // skip already validated
			const sparepartId  = row.dataset.sparepartId || row.getAttribute('data-sparepart-id');
			const brought      = parseInt(row.dataset.brought) || 0;
			const usedInput    = row.querySelector('.qty-used-input');
			const quantityUsed = usedInput ? (parseInt(usedInput.value) || 0) : 0;
			const unitSelect   = row.querySelector('.unit-select');
			const unitId       = unitSelect ? (unitSelect.value || null) : null;
			if (quantityUsed < 0 || quantityUsed > brought) {
				hasError = true;
			}
			validationData.push({
				sparepart_id:    sparepartId,
				quantity_used:   quantityUsed,
				quantity_return: brought - quantityUsed,
				unit_id:         unitId
			});
		});

		if (hasError) {
			notify('Jumlah digunakan tidak valid (harus 0 – Dibawa)', 'error');
			return;
		}

		// Collect additional spareparts (same logic as input sparepart modal)
		const additionalSpareparts = [];
		let addValid = true;
		$('#additionalSparepartRowsSpk tr[id^="vld_row_"]').each(function(i) {
			const rc     = $(this).attr('id').replace('vld_row_', '');
			const itype  = $(`#vld_item_type_${rc}`).val() || 'sparepart';
			const source = $(`#vld_source_${rc}`).val() || 'WAREHOUSE';
			const rowNum = i + 1;
			let name = '', sparepartCode = '';
			if (itype === 'tool') {
				name = $(`#vld_tool_input_${rc}`).val()?.trim() || '';
			} else {
				const $sel    = $(`#vld_sparepart_${rc}`);
				const $manual = $(`#vld_sparepart_manual_${rc}`);
				if ($manual.is(':visible')) {
					name = $manual.val()?.trim() || '';
				} else if ($sel.hasClass('select2-hidden-accessible')) {
					const selData = $sel.select2('data');
					if (selData && selData[0] && selData[0].kode) {
						sparepartCode = selData[0].kode;
						name = selData[0].desc || selData[0].text?.trim() || '';
					} else {
						const rawVal = $sel.val()?.trim() || '';
						const dashIdx = rawVal.indexOf(' - ');
						if (dashIdx > -1) { sparepartCode = rawVal.substring(0, dashIdx); name = rawVal.substring(dashIdx + 3); }
						else { name = rawVal; }
					}
				}
			}
			const qty    = parseInt($(`[name="vld_qty_${rc}"]`).val()) || 0;
			const satuan = $(`[name="vld_satuan_${rc}"]`).val()?.trim() || '';
			const notesVal = $(`[name="vld_notes_${rc}"]`).val()?.trim() || '';
			const $unitSel = $(`#vld_kanibal_unit_${rc}`);
			const unitNo   = $unitSel.hasClass('select2-hidden-accessible') ? ($unitSel.val() || '') : ($unitSel[0]?.value || '');
			const reason   = $(`#vld_kanibal_reason_${rc}`).val()?.trim() || '';
			if (!name)                               { addValid = false; notify(`Tambahan baris ${rowNum}: Item Name kosong`, 'error'); return false; }
			if (qty < 1)                             { addValid = false; notify(`Tambahan baris ${rowNum}: Qty harus > 0`, 'error'); return false; }
			if (source === 'KANIBAL' && !unitNo)     { addValid = false; notify(`Tambahan baris ${rowNum}: No. Unit Kanibal wajib diisi`, 'error'); return false; }
			additionalSpareparts.push({
				sparepart_name   : name,
				sparepart_code   : sparepartCode,
				item_type        : itype,
				source_type      : source,
				quantity         : qty,
				satuan           : satuan,
				source_unit_no   : source === 'KANIBAL' ? unitNo  : '',
				notes            : source === 'KANIBAL' ? reason  : notesVal,
				is_from_warehouse: source === 'WAREHOUSE' ? 1 : 0
			});
		});
		if (!addValid) return;

		if (validationData.length === 0 && additionalSpareparts.length === 0) {
			notify('Tidak ada item yang perlu divalidasi', 'warning');
			return;
		}

		const btn = this;
		confirmSwal({
			title: 'Konfirmasi Validasi',
			text: 'Simpan validasi sparepart? Aksi ini tidak dapat dibatalkan.',
			type: 'success',
			confirmText: '<i class="fas fa-check me-1"></i>Ya, Simpan'
		}).then(confirmed => {
			if (!confirmed) return;

			btn.disabled = true;
			btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';

			const fd = new FormData();
			fd.append(window.csrfTokenName, window.csrfTokenValue);
			fd.append('validation_data', JSON.stringify(validationData));
			fd.append('additional_spareparts', JSON.stringify(additionalSpareparts));
			fd.append('notes', notes);

		(window.csrfFetch || window.fetch)(window.baseUrl + 'service/spk/validate-spareparts/' + spkId, {
			method:  'POST',
			body:    fd
		})
			.then(r => r.json())
			.then(res => {
				btn.disabled = false;
				btn.innerHTML = '<i class="fas fa-check me-1"></i>Validasi & Simpan';
				if (res.success) {
					notify(res.message || 'Validasi berhasil disimpan', 'success');
					bootstrap.Modal.getInstance(document.getElementById('spkSparepartValidationModal')).hide();
					if (res.returns_generated > 0) {
						setTimeout(() => {
							notify(
								`${res.returns_generated} permintaan pengembalian sparepart telah dikirim ke Warehouse (status: PENDING).`,
								'info'
							);
						}, 500);
					}
					window.reloadSpkTable();
					// Update CSRF token if returned
					if (res.csrf_hash && window.csrfTokenName) {
						window.csrfTokenValue = res.csrf_hash;
					}
				} else {
					notify(res.message || 'Gagal menyimpan validasi', 'error');
				}
			})
			.catch(err => {
				btn.disabled = false;
				btn.innerHTML = '<i class="fas fa-check me-1"></i>Validasi & Simpan';
				notify('Error: ' + err.message, 'error');
			});
		});
	});
</script>

        </div>
    </div>
    
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

