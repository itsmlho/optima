<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<style>
.swal-wide {
	width: 600px !important;
}
.swal2-html-container .form-check {
	text-align: left;
}
.swal2-html-container .form-check-label {
	margin-left: 0.5rem;
}
/* Pastikan SweetAlert2 di atas modal */
.swal2-container {
	z-index: 2000 !important;
}
.modal-backdrop.sweetalert-active {
	z-index: 1000 !important;
	pointer-events: none !important;
}
/* Pastikan modal Bootstrap tidak menghalangi pointer saat SweetAlert2 aktif */
.modal.sweetalert-disable {
	pointer-events: none !important;
}

.filter-card {
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid #dee2e6;
}

.filter-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  border-color: #0d6efd;
}

.filter-card.active {
  background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
  color: white;
  border-color: #0d6efd;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(13,110,253,0.3);
}

.filter-card.active .text-muted {
  color: rgba(255,255,255,0.8) !important;
}
</style>

<div class="container-fluid py-3">
  <!-- Statistics Cards -->
  <div class="row mb-4">
    <div class="col-md-2">
      <div class="card filter-card text-center" data-filter="all">
        <div class="card-body py-3">
          <h5 class="mb-1 text-primary" id="totalSPK">0</h5>
          <small class="text-muted">Total SPK</small>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card filter-card text-center" data-filter="SUBMITTED">
        <div class="card-body py-3">
          <h5 class="mb-1 text-secondary" id="submittedSPK">0</h5>
          <small class="text-muted">Submitted</small>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card filter-card text-center" data-filter="IN_PROGRESS">
        <div class="card-body py-3">
          <h5 class="mb-1 text-info" id="inProgressSPK">0</h5>
          <small class="text-muted">In Progress</small>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card filter-card text-center" data-filter="READY">
        <div class="card-body py-3">
          <h5 class="mb-1 text-success" id="readySPK">0</h5>
          <small class="text-muted">Ready</small>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card filter-card text-center" data-filter="COMPLETED">
        <div class="card-body py-3">
          <h5 class="mb-1 text-primary" id="completedSPK">0</h5>
          <small class="text-muted">Completed</small>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card filter-card text-center" data-filter="CANCELLED">
        <div class="card-body py-3">
          <h5 class="mb-1 text-danger" id="cancelledSPK">0</h5>
          <small class="text-muted">Cancelled</small>
        </div>
      </div>
    </div>
  </div>

	<div class="card">
		<div class="card-body">
			<!-- DataTable-style controls -->
			<div class="row mb-3">
				<div class="col-md-6 d-flex align-items-center">
					<label class="me-2">Show</label>
					<select class="form-select form-select-sm me-2" id="entriesPerPage" style="width: auto;">
						<option value="10">10</option>
						<option value="25">25</option>
						<option value="50">50</option>
						<option value="100">100</option>
					</select>
					<span>entries</span>
				</div>
				<div class="col-md-6">
					<div class="input-group input-group-sm">
						<span class="input-group-text">Search:</span>
						<input type="text" class="form-control" id="searchInput" placeholder="Cari No. SPK, Pelanggan, PIC...">
					</div>
				</div>
			</div>

			<div class="table-responsive">
				<table class="table table-sm mb-0" id="spkTable">
					<thead>
						<tr>
							<th>No. SPK</th>
							<th>Pelanggan</th>
							<th>PIC</th>
							<th>Kontak</th>
							<th>Delivery Plan</th>
							<th>Status</th>
							<th>Aksi</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>

			<!-- Pagination -->
			<div class="row mt-3">
				<div class="col-md-6">
					<div id="tableInfo" class="text-muted"></div>
				</div>
				<div class="col-md-6">
					<nav>
						<ul class="pagination pagination-sm justify-content-end mb-0" id="pagination"></ul>
					</nav>
				</div>
			</div>
		</div>
	</div>

	<!-- Assign Items Modal (Unit + Attachment) -->
	<div class="modal fade" id="assignItemsModal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header"><h6 class="modal-title">Pilih Unit & Attachment</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
				<form id="assignItemsForm">
					<div class="modal-body">
						<input type="hidden" name="spk_id" id="assignSpkId">
						<div class="mb-2">
							<label class="form-label">Pilih Unit (Stock Aset/Non Aset)</label>
							<input type="text" class="form-control" id="unitSearch" placeholder="Cari no unit / serial / merk / model" autocomplete="off">
							<select class="form-select mt-2" id="unitPick" name="unit_id"></select>
							<div class="form-text">Ketik untuk mencari, lalu pilih dari daftar.</div>
						</div>
						<div class="mb-2">
							<label class="form-label">Pilih Attachment (opsional)</label>
							<input type="text" class="form-control" id="attSearch" placeholder="Cari tipe/merk/model/SN/lokasi" autocomplete="off">
							<select class="form-select mt-2" id="attPick" name="inventory_attachment_id"></select>
							<div class="form-text">Data dari inventory_attachment (status 7/8).</div>
						</div>
					</div>
					<div class="modal-footer"><button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" type="submit">Simpan & Tandai READY</button></div>
				</form>
			</div>
		</div>
	</div>

	<!-- Approval Stage Modal -->
	<div class="modal fade" id="approvalStageModal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h6 class="modal-title">Konfirmasi Approval - <span id="approvalStageTitle"></span></h6>
					<button class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<form id="approvalStageForm">
					<div class="modal-body">
						<!-- Common fields for all stages -->
						<div class="mb-3">
							<label class="form-label">Nama Mekanik <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="approvalMekanik" name="mekanik" required 
								   placeholder="Masukkan nama mekanik yang bertanggung jawab">
						</div>
						<div class="row">
							<div class="col-6">
								<label class="form-label">Estimasi Mulai <span class="text-danger">*</span></label>
								<input type="date" class="form-control" id="approvalEstimasiMulai" name="estimasi_mulai" required>
							</div>
							<div class="col-6">
								<label class="form-label">Estimasi Selesai <span class="text-danger">*</span></label>
								<input type="date" class="form-control" id="approvalEstimasiSelesai" name="estimasi_selesai" required>
							</div>
						</div>

						<!-- Stage-specific content -->
						<div id="stageSpecificContent"></div>

						<div class="form-text mt-2">
							<small>Data ini akan muncul di PDF SPK sebagai estimasi pekerjaan dan tanda tangan approval.</small>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
						<button class="btn btn-success" type="submit">Approve & Simpan</button>
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
					<h6 class="modal-title">Detail SPK</h6>
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
</div>
<script>
// Global variables
let allSPKData = [];
let filteredSPKData = [];
let currentFilter = 'all';
let currentPage = 1;
let entriesPerPage = 10;

// Unified notifier (fallbacks)
function notify(msg, type='success'){
	if (window.OptimaPro && typeof OptimaPro.showNotification==='function') return OptimaPro.showNotification(msg, type);
	if (typeof showNotification==='function') return showNotification(msg, type);
	alert(msg);
}

document.addEventListener('DOMContentLoaded', () => {
	const tbody = document.querySelector('#spkTable tbody');
	
	const load = () => fetch('<?= base_url('service/spk/list') ?>').then(r=>r.json()).then(j=>{
		allSPKData = j.data || [];
		updateStatistics();
		applyFilters();
	});
	
	function updateStatistics() {
		const total = allSPKData.length;
		const submitted = allSPKData.filter(item => (item.status || '').toUpperCase() === 'SUBMITTED').length;
		const inProgress = allSPKData.filter(item => (item.status || '').toUpperCase() === 'IN_PROGRESS').length;
		const ready = allSPKData.filter(item => (item.status || '').toUpperCase() === 'READY').length;
		const completed = allSPKData.filter(item => (item.status || '').toUpperCase() === 'COMPLETED' || (item.status || '').toUpperCase() === 'DELIVERED').length;
		const cancelled = allSPKData.filter(item => (item.status || '').toUpperCase() === 'CANCELLED').length;
		
		document.getElementById('totalSPK').textContent = total;
		document.getElementById('submittedSPK').textContent = submitted;
		document.getElementById('inProgressSPK').textContent = inProgress;
		document.getElementById('readySPK').textContent = ready;
		document.getElementById('completedSPK').textContent = completed;
		document.getElementById('cancelledSPK').textContent = cancelled;
	}
	
	function applyFilters() {
		const searchTerm = document.getElementById('searchInput').value.toLowerCase();
		
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
	
	function renderSPKTable() {
		const startIndex = (currentPage - 1) * entriesPerPage;
		const endIndex = startIndex + entriesPerPage;
		const dataToShow = filteredSPKData.slice(startIndex, endIndex);
		
		tbody.innerHTML = '';
		dataToShow.forEach(r=>{
			const tr = document.createElement('tr');
			const badge = (s)=>{ const m={SUBMITTED:'secondary',IN_PROGRESS:'info',READY:'success',DELIVERED:'primary',COMPLETED:'primary',CANCELLED:'danger'}; const c=m[(s||'').toUpperCase()]||'secondary'; return `<span class="badge bg-${c}">${s}</span>`; };
			
			// Conditional action button based on status
			let actionBtn = '';
			if (r.status === 'SUBMITTED') {
				actionBtn = '<span class="text-muted">Menunggu diproses</span>';
			} else if (r.status === 'IN_PROGRESS') {
				// Show approval stage buttons directly in table
				const persiapanDone = r.persiapan_unit_tanggal_approve ? true : false;
				const fabrikasiDone = r.fabrikasi_tanggal_approve ? true : false;
				const paintingDone = r.painting_tanggal_approve ? true : false;
				const pdiDone = r.pdi_tanggal_approve ? true : false;
				
				let approvalButtons = [];
				
				// Add active button for current stage
				if (!persiapanDone) {
					approvalButtons.push(`<button class="btn btn-sm btn-warning" onclick="openApprovalModal('persiapan_unit', 'Bag. Persiapan Unit', ${r.id})">Persiapan Unit</button>`);
				} else if (!fabrikasiDone) {
					approvalButtons.push(`<button class="btn btn-sm btn-warning" onclick="openApprovalModal('fabrikasi', 'Bag. Fabrikasi', ${r.id})">Fabrikasi</button>`);
				} else if (!paintingDone) {
					approvalButtons.push(`<button class="btn btn-sm btn-warning" onclick="openApprovalModal('painting', 'Bag. Painting', ${r.id})">Painting</button>`);
				} else if (!pdiDone) {
					approvalButtons.push(`<button class="btn btn-sm btn-warning" onclick="openApprovalModal('pdi', 'Bag. PDI Pengecekan', ${r.id})">PDI Pengecekan</button>`);
				} else {
					// All approvals done - should be READY status already
					approvalButtons.push('<span class="text-info">Menunggu update status ke READY</span>');
				}
				
				// Add small completed badges
				const completedBadges = [];
				if (persiapanDone) completedBadges.push('<small class="badge bg-success me-1">✓ P.Unit</small>');
				if (fabrikasiDone) completedBadges.push('<small class="badge bg-success me-1">✓ Fabrikasi</small>');
				if (paintingDone) completedBadges.push('<small class="badge bg-success me-1">✓ Painting</small>');
				if (pdiDone) completedBadges.push('<small class="badge bg-success me-1">✓ PDI</small>');
				
				actionBtn = approvalButtons.join(' ') + (completedBadges.length > 0 ? '<br>' + completedBadges.join('') : '');
			} else if (r.status === 'READY') {
				actionBtn = '<span class="text-success">Siap untuk delivery</span>';
			} else {
				actionBtn = '<span class="text-muted">-</span>';
			}
			
			tr.innerHTML = `
				<td><a href="#" onclick="viewDetail(${r.id});return false;">${r.nomor_spk}</a></td>
				<td>${r.pelanggan||'-'}</td>
				<td>${r.pic||'-'}</td>
				<td>${r.kontak||'-'}</td>
				<td>${r.delivery_plan||'-'}</td>
				<td>${badge(r.status)}</td>
				<td>${actionBtn}</td>`;
			tbody.appendChild(tr);
		});
		
		// Update table info
		const totalEntries = filteredSPKData.length;
		const start = totalEntries === 0 ? 0 : ((currentPage - 1) * entriesPerPage) + 1;
		const end = Math.min(currentPage * entriesPerPage, totalEntries);
		document.getElementById('tableInfo').textContent = 
			`Showing ${start} to ${end} of ${totalEntries} entries`;
	}
	
	function updatePagination() {
		const totalPages = Math.ceil(filteredSPKData.length / entriesPerPage);
		const pagination = document.getElementById('pagination');
		pagination.innerHTML = '';
		
		// Previous button
		const prevLi = document.createElement('li');
		prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
		prevLi.innerHTML = '<a class="page-link" href="#" onclick="changePage(' + (currentPage - 1) + ')">Previous</a>';
		pagination.appendChild(prevLi);
		
		// Page numbers
		for (let i = 1; i <= totalPages; i++) {
			const li = document.createElement('li');
			li.className = `page-item ${currentPage === i ? 'active' : ''}`;
			li.innerHTML = '<a class="page-link" href="#" onclick="changePage(' + i + ')">' + i + '</a>';
			pagination.appendChild(li);
		}
		
		// Next button
		const nextLi = document.createElement('li');
		nextLi.className = `page-item ${currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}`;
		nextLi.innerHTML = '<a class="page-link" href="#" onclick="changePage(' + (currentPage + 1) + ')">Next</a>';
		pagination.appendChild(nextLi);
	}
	
	window.changePage = function(page) {
		const totalPages = Math.ceil(filteredSPKData.length / entriesPerPage);
		if (page >= 1 && page <= totalPages) {
			currentPage = page;
			renderSPKTable();
			updatePagination();
		}
	}
	
	// Event listeners
	document.getElementById('entriesPerPage').addEventListener('change', function() {
		entriesPerPage = parseInt(this.value);
		currentPage = 1;
		renderSPKTable();
		updatePagination();
	});
	
	document.getElementById('searchInput').addEventListener('input', function() {
		applyFilters();
	});
	
	// Filter card click listeners
	document.querySelectorAll('.filter-card').forEach(card => {
		card.addEventListener('click', function() {
			const filter = this.dataset.filter;
			currentFilter = filter;
			
			// Update active card
			document.querySelectorAll('.filter-card').forEach(c => c.classList.remove('active'));
			this.classList.add('active');
			
			applyFilters();
		});
	});
	
	load();
	
	window.confirmReady = (id) => {
		fetch(`<?= base_url('service/spk/confirm-ready/') ?>${id}`, {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}})
		 .then(r=>r.json()).then(()=>load());
	}

	let currentSpkId = null;
	window.viewDetail = (id) => {
		currentSpkId = id;
		const body = document.getElementById('spkDetailBody');
		body.innerHTML = '<p class="text-muted">Memuat...</p>';

		fetch(`<?= base_url('service/spk/detail/') ?>${id}`).then(r=>r.json()).then(j=>{
			if (!j.success) { body.innerHTML = '<div class="text-danger">Gagal memuat detail</div>'; return; }
            const d = j.data || {};
            const s = j.spesifikasi || {};
			const status = d.status || 'SUBMITTED';
			
			// Update action buttons based on status
			const actionDiv = document.getElementById('modalActionButtons');
			let actionButtons = '';
			
			if (status === 'SUBMITTED') {
				actionButtons = '<button class="btn btn-success btn-sm" id="btnProsesSPK">Proses SPK</button>';
			} else if (status === 'IN_PROGRESS') {
				// Show approval stage buttons based on completion status
				let approvalButtons = [];
				
				// Check which stages are completed
				const persiapanDone = d.persiapan_unit_tanggal_approve ? true : false;
				const fabrikasiDone = d.fabrikasi_tanggal_approve ? true : false;
				const paintingDone = d.painting_tanggal_approve ? true : false;
				const pdiDone = d.pdi_tanggal_approve ? true : false;
				
				// Add buttons for incomplete stages
				if (!persiapanDone) {
					approvalButtons.push('<button class="btn btn-warning btn-sm" onclick="openApprovalModal(\'persiapan_unit\', \'Bag. Persiapan Unit\')">Persiapan Unit</button>');
				} else if (!fabrikasiDone) {
					approvalButtons.push('<button class="btn btn-warning btn-sm" onclick="openApprovalModal(\'fabrikasi\', \'Bag. Fabrikasi\')">Fabrikasi</button>');
				} else if (!paintingDone) {
					approvalButtons.push('<button class="btn btn-warning btn-sm" onclick="openApprovalModal(\'painting\', \'Bag. Painting\')">Painting</button>');
				} else if (!pdiDone) {
					approvalButtons.push('<button class="btn btn-warning btn-sm" onclick="openApprovalModal(\'pdi\', \'Bag. PDI Pengecekan\')">PDI Pengecekan</button>');
				}
				
				// Show completed stages with checkmarks
				if (persiapanDone) approvalButtons.push('<span class="badge bg-success me-1">✓ Persiapan Unit</span>');
				if (fabrikasiDone) approvalButtons.push('<span class="badge bg-success me-1">✓ Fabrikasi</span>');
				if (paintingDone) approvalButtons.push('<span class="badge bg-success me-1">✓ Painting</span>');
				if (pdiDone) approvalButtons.push('<span class="badge bg-success me-1">✓ PDI</span>');
				
				actionButtons = `
					<a class="btn btn-outline-secondary btn-sm" id="btnPrintPdfSvc" href="<?= base_url('service/spk/print/') ?>${id}" target="_blank" rel="noopener">Print PDF</a>
					${approvalButtons.join(' ')}
					${pdiDone ? '<button class="btn btn-primary btn-sm" onclick="openAssign(' + id + '); bootstrap.Modal.getInstance(document.getElementById(\'spkDetailModal\')).hide();">Pilih Unit & Attachment</button>' : ''}
				`;
			} else if (status === 'READY' || status === 'DELIVERED' || status === 'COMPLETED') {
				actionButtons = `<a class="btn btn-outline-secondary btn-sm" id="btnPrintPdfSvc" href="<?= base_url('service/spk/print/') ?>${id}" target="_blank" rel="noopener">Print PDF</a>`;
			}
			
			actionDiv.innerHTML = actionButtons;
			
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

			body.innerHTML = `
				<div class="row g-2">
					<div class="col-6"><strong>No SPK:</strong> ${d.nomor_spk}</div>
					<div class="col-6"><strong>Jenis SPK:</strong> <span class="badge bg-dark">${d.jenis_spk||'UNIT'}</span></div>
					<div class="col-6"><strong>Kontrak/PO:</strong> ${d.po_kontrak_nomor||'-'}</div>
					<div class="col-6"><strong>Pelanggan:</strong> ${d.pelanggan||'-'}</div>
					<div class="col-6"><strong>PIC:</strong> ${d.pic||'-'}</div>
					<div class="col-6"><strong>Kontak:</strong> ${d.kontak||'-'}</div>
					<div class="col-6"><strong>Lokasi:</strong> ${d.lokasi||'-'}</div>
					<div class="col-12"><hr></div>
                    <div class="col-6"><strong>Departemen:</strong> ${s.departemen_id_name||s.departemen_id||'-'}</div>
					<div class="col-6"><strong>Tipe (Jenis):</strong> ${s.tipe_jenis||'-'}</div>
					<div class="col-6"><strong>Merk Unit:</strong> ${s.merk_unit||'-'}</div>
					<div class="col-6"><strong>Valve:</strong> ${s.valve_id_name||s.valve_id||'-'}</div>
					<div class="col-6"><strong>Baterai (Jenis):</strong> ${s.jenis_baterai||'-'}</div>
					<div class="col-6"><strong>Attachment (Tipe):</strong> ${s.attachment_tipe||'-'}</div>
					<div class="col-6"><strong>Roda:</strong> ${s.roda_id_name||s.roda_id||'-'}</div>
					<div class="col-6"><strong>Kapasitas:</strong> ${s.kapasitas_id_name||s.kapasitas_id||'-'}</div>
					<div class="col-6"><strong>Mast:</strong> ${s.mast_id_name||s.mast_id||'-'}</div>
					<div class="col-6"><strong>Ban:</strong> ${s.ban_id_name||s.ban_id||'-'}</div>
					<div class="col-12"><strong>Aksesoris:</strong> ${(Array.isArray(s.aksesoris)?s.aksesoris:[]).join(', ') || '-'}</div>
					
					${status === 'IN_PROGRESS' || status === 'READY' || status === 'DELIVERED' || status === 'COMPLETED' ? `
					<div class="col-12"><hr></div>
					<div class="col-12"><h6 class="mb-2">📋 Status Approval Workflow</h6></div>
					
					<div class="col-12">
						<div class="row g-2">
							<div class="col-6">
								<strong>1. Persiapan Unit:</strong> 
								${d.persiapan_unit_tanggal_approve ? 
									`<span class="badge bg-success">✓ Selesai</span><br>
									<small>Oleh: ${d.persiapan_unit_mekanik||'-'} <br>
									Tanggal: ${d.persiapan_unit_tanggal_approve||'-'}<br>
									Unit ID: ${workflowData.persiapan_unit_id||'-'}</small>` 
									: '<span class="badge bg-warning">Menunggu</span>'}
							</div>
							<div class="col-6">
								<strong>2. Fabrikasi:</strong> 
								${d.fabrikasi_tanggal_approve ? 
									`<span class="badge bg-success">✓ Selesai</span><br>
									<small>Oleh: ${d.fabrikasi_mekanik||'-'} <br>
									Tanggal: ${d.fabrikasi_tanggal_approve||'-'}<br>
									Attachment ID: ${workflowData.fabrikasi_attachment_id||'-'}</small>` 
									: '<span class="badge bg-warning">Menunggu</span>'}
							</div>
							<div class="col-6">
								<strong>3. Painting:</strong> 
								${d.painting_tanggal_approve ? 
									`<span class="badge bg-success">✓ Selesai</span><br>
									<small>Oleh: ${d.painting_mekanik||'-'} <br>
									Tanggal: ${d.painting_tanggal_approve||'-'}</small>` 
									: '<span class="badge bg-warning">Menunggu</span>'}
							</div>
							<div class="col-6">
								<strong>4. PDI Pengecekan:</strong> 
								${d.pdi_tanggal_approve ? 
									`<span class="badge bg-success">✓ Selesai</span><br>
									<small>Oleh: ${d.pdi_mekanik||'-'} <br>
									Tanggal: ${d.pdi_tanggal_approve||'-'}</small>` 
									: '<span class="badge bg-warning">Menunggu</span>'}
							</div>
						</div>
					</div>
					
					${workflowData.persiapan_aksesoris_tersedia ? `
					<div class="col-12"><hr></div>
					<div class="col-12">
						<strong>📦 Aksesoris Tersedia (Persiapan Unit):</strong><br>
						<small class="text-success">${workflowData.persiapan_aksesoris_tersedia}</small>
					</div>` : ''}
					
					${workflowData.pdi_catatan ? `
					<div class="col-12">
						<strong>📝 Catatan PDI:</strong><br>
						<small class="text-info">${workflowData.pdi_catatan}</small>
					</div>` : ''}
					` : ''}
					
					<div class="col-12"><hr></div>
					<div class="col-12"><strong>Item Terpilih:</strong></div>
					<div class="col-12" id="svcUnitDetailBlock">
						${(s.selected && s.selected.unit) ? `
							<div><strong>Unit:</strong> ${s.selected.unit.label || `${s.selected.unit.no_unit || '-'} | ${s.selected.unit.merk_unit || '-'} | ${s.selected.unit.model_unit || '-'} | ${s.selected.unit.jenis_unit || '-'}`}</div>
							<div><strong>Serial Number:</strong> ${s.selected.unit.serial_number || '-'}</div>
							<div><strong>Tipe Unit:</strong> ${s.selected.unit.tipe_jenis || '-'}</div>
							<div><strong>Kapasitas:</strong> ${s.selected.unit.kapasitas_name || '-'}</div>
							<div><strong>Mast:</strong> ${s.selected.unit.mast || s.selected.unit.mast_model || '-'}</div>
							<div><strong>Roda:</strong> ${s.selected.unit.roda || '-'}</div>
							<div><strong>Ban:</strong> ${s.selected.unit.ban || '-'}</div>
							<div><strong>Valve:</strong> ${s.selected.unit.valve || '-'}</div>
						` : (s.selected && s.selected.unit && s.selected.unit.label ? `<div><strong>Unit:</strong> ${s.selected.unit.label}</div>` : '<div class="text-muted">Unit: -</div>')}
						${(s.selected && s.selected.attachment) ? `
							<div><strong>Attachment:</strong> ${s.selected.attachment.tipe || '-'} | ${s.selected.attachment.merk || '-'} | ${s.selected.attachment.model || '-'}${s.selected.attachment.sn_attachment ? ` [SN: ${s.selected.attachment.sn_attachment}]` : ''}${s.selected.attachment.lokasi_penyimpanan ? ` @ ${s.selected.attachment.lokasi_penyimpanan}` : ''}</div>
						` : ''}
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
						
						fetch(`<?= base_url('service/spk/update-status/') ?>${id}`, {
							method: 'POST',
							headers: {'X-Requested-With': 'XMLHttpRequest'},
							body: formData
						}).then(r=>r.json()).then(result=>{
							if (result && result.success) {
								notify('SPK berhasil diproses. Status menjadi IN_PROGRESS.', 'success');
								bootstrap.Modal.getInstance(document.getElementById('spkDetailModal')).hide();
								load(); // Reload table
							} else {
								notify(result.message || 'Gagal memproses SPK', 'error');
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
		document.getElementById('assignSpkId').value = spkId;
		document.getElementById('unitSearch').value = '';
		document.getElementById('attSearch').value = '';
		document.getElementById('unitPick').innerHTML = '';
		document.getElementById('attPick').innerHTML = '<option value="">- (Opsional) -</option>';
		new bootstrap.Modal(document.getElementById('assignItemsModal')).show();
	}
	const searchBox = document.getElementById('unitSearch');
	searchBox.addEventListener('input', function(){
		const q = this.value.trim();
		const url = new URL('<?= base_url('service/data-unit/simple') ?>', window.location.origin);
		if (q) url.searchParams.set('q', q);
		fetch(url).then(r=>r.json()).then(j=>{
			const sel = document.getElementById('unitPick');
			sel.innerHTML = '<option value="">- Pilih Unit -</option>' + (j.data||[]).map(x=>`<option value="${x.id}">${x.label}</option>`).join('');
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
					notify('Item tersimpan. Status SPK menjadi READY.', 'success');
				} else {
					notify(j.message || 'Gagal menyimpan item', 'error');
				}
			});
	});
	
	// Approval Stage Modal Functions
	let currentApprovalStage = '';
	
	window.openApprovalModal = (stage, stageTitle, spkId) => {
		currentApprovalStage = stage;
		currentSpkId = spkId || currentSpkId; // Use passed spkId or fallback to currentSpkId
		document.getElementById('approvalStageTitle').textContent = stageTitle;
		document.getElementById('approvalMekanik').value = '';
		document.getElementById('approvalEstimasiMulai').value = '';
		document.getElementById('approvalEstimasiSelesai').value = '';
		
		// Load stage-specific content
		loadStageSpecificContent(stage, spkId);
		
		new bootstrap.Modal(document.getElementById('approvalStageModal')).show();
	}
	
	function loadStageSpecificContent(stage, spkId) {
		const container = document.getElementById('stageSpecificContent');
		
		if (stage === 'persiapan_unit') {
			// Get SPK details to show requested accessories
			fetch(`<?= base_url('service/spk/detail/') ?>${spkId}`).then(r=>r.json()).then(j=>{
				if (j.success) {
					const spesifikasi = j.spesifikasi || {};
					const aksesoris = spesifikasi.aksesoris || [];
					
					let aksesorisCheckboxes = '';
					if (Array.isArray(aksesoris) && aksesoris.length > 0) {
						aksesorisCheckboxes = aksesoris.map(item => 
							`<div class="form-check">
								<input class="form-check-input" type="checkbox" name="aksesoris_tersedia[]" value="${item}" id="aks_${item.replace(/\s+/g, '_')}">
								<label class="form-check-label" for="aks_${item.replace(/\s+/g, '_')}">${item}</label>
							</div>`
						).join('');
					} else {
						aksesorisCheckboxes = '<p class="text-muted">Tidak ada aksesoris yang diminta</p>';
					}
					
					container.innerHTML = `
						<hr>
						<div class="mb-3">
							<label class="form-label">Pilih Unit <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="approvalUnitSearch" placeholder="Cari no unit / serial / merk / model" autocomplete="off">
							<select class="form-select mt-2" id="approvalUnitPick" name="unit_id" required></select>
							<div class="form-text">Ketik untuk mencari, lalu pilih dari daftar.</div>
						</div>
						<div class="mb-3">
							<label class="form-label">Konfirmasi Aksesoris Tersedia</label>
							<div class="border p-3 rounded bg-light">
								${aksesorisCheckboxes}
							</div>
							<div class="form-text">Centang aksesoris yang sudah tersedia sesuai permintaan Marketing.</div>
						</div>
					`;
					
					// Setup unit search
					setupUnitSearch();
				}
			});
			
		} else if (stage === 'fabrikasi') {
			container.innerHTML = `
				<hr>
				<div class="mb-3">
					<label class="form-label">Pilih Attachment (Opsional)</label>
					<input type="text" class="form-control" id="approvalAttSearch" placeholder="Cari tipe/merk/model/SN/lokasi" autocomplete="off">
					<select class="form-select mt-2" id="approvalAttPick" name="attachment_id"></select>
					<div class="form-text">Khusus untuk fabrikasi attachment. Data dari inventory_attachment.</div>
				</div>
			`;
			
			// Setup attachment search
			setupAttachmentSearch();
			
		} else if (stage === 'pdi') {
			container.innerHTML = `
				<hr>
				<div class="mb-3">
					<label class="form-label">Catatan PDI <span class="text-danger">*</span></label>
					<textarea class="form-control" id="pdiCatatan" name="catatan" rows="4" required 
							  placeholder="Masukkan hasil pengecekan PDI dan konfirmasi bahwa semua sesuai dengan permintaan..."></textarea>
					<div class="form-text">Catatan ini akan menandai SPK siap untuk delivery (status READY).</div>
				</div>
			`;
			
		} else {
			// painting or default - no additional content
			container.innerHTML = '';
		}
	}
	
	function setupUnitSearch() {
		const searchBox = document.getElementById('approvalUnitSearch');
		if (searchBox) {
			searchBox.addEventListener('input', function(){
				const q = this.value.trim();
				const url = new URL('<?= base_url('service/data-unit/simple') ?>', window.location.origin);
				if (q) url.searchParams.set('q', q);
				fetch(url).then(r=>r.json()).then(j=>{
					const sel = document.getElementById('approvalUnitPick');
					sel.innerHTML = '<option value="">- Pilih Unit -</option>' + (j.data||[]).map(x=>`<option value="${x.id}" data-no-unit="${x.no_unit||''}" data-needs-no-unit="${x.needs_no_unit||false}" data-status-unit="${x.status_unit_id||''}">${x.label}</option>`).join('');
				});
			});
			
			// Add change event for unit selection validation
			const unitPick = document.getElementById('approvalUnitPick');
			if (unitPick) {
				unitPick.addEventListener('change', function(){
					const selectedOption = this.options[this.selectedIndex];
					if (selectedOption && selectedOption.value) {
						const noUnit = selectedOption.getAttribute('data-no-unit');
						const needsNoUnit = selectedOption.getAttribute('data-needs-no-unit');
						const statusUnit = selectedOption.getAttribute('data-status-unit');
						
						// Show no_unit confirmation only for STOCK NON ASET (status 8) that doesn't have no_unit
						if (statusUnit === '8' && (needsNoUnit === 'true' || !noUnit || noUnit === '' || noUnit === '0')) {
							// Unit Non Aset belum memiliki valid no_unit, show confirmation
							showNoUnitConfirmation(selectedOption.value, selectedOption.text, statusUnit);
						} else if (statusUnit === '7') {
							// Unit Aset sudah memiliki no_unit, tidak perlu konfirmasi
							console.log('Selected Aset unit - already has no_unit, no confirmation needed');
						} else {
							// Unit already has no_unit or doesn't need it
							console.log('Selected unit already has no_unit or doesn\'t need it');
						}
					}
				});
			}
		}
	}
	
	function showNoUnitConfirmation(unitId, unitLabel, statusUnit) {
		const isNonAset = statusUnit === '8';
		const unitType = isNonAset ? 'Non Aset' : 'Aset';

		// Modal Bootstrap Approval Stage tetap terbuka

		Swal.fire({
			title: `Unit ${unitType} Belum Memiliki No Unit`,
			html: `
				<div class="text-start mb-3">
					<p class="mb-2"><strong>Unit ${unitType} yang dipilih:</strong><br><span class="text-primary">${unitLabel}</span></p>
					<p class="text-warning mb-3"><i class="fas fa-exclamation-triangle"></i> Unit ${unitType} ini belum memiliki <strong>No Unit</strong> (nomor aset).</p>
					<p class="mb-3">Pilih tindakan yang akan dilakukan:</p>
					<div class="form-check mb-2">
						<input class="form-check-input" type="radio" name="noUnitAction" id="generateNoUnit" value="generate" checked>
						<label class="form-check-label" for="generateNoUnit">
							<strong><i class="fas fa-magic text-success"></i> Generate No Unit otomatis</strong><br>
							<small class="text-muted">Sistem akan membuat nomor urut tertinggi + 1${isNonAset ? '' : ' dan mengubah status ke RENTAL'}</small>
						</label>
					</div>
				</div>
			`,
			showCancelButton: true,
			confirmButtonText: '<i class="fas fa-check"></i> Lanjutkan',
			cancelButtonText: '<i class="fas fa-times"></i> Batal',
			focusConfirm: false,
			allowOutsideClick: false,
			customClass: {
				popup: 'swal-wide'
			},
			didOpen: () => {
				// Nonaktifkan pointer-events pada backdrop dan modal Bootstrap agar input SweetAlert2 bisa diisi
				document.querySelectorAll('.modal').forEach(m => m.classList.add('sweetalert-disable'));
				document.querySelectorAll('.modal-backdrop').forEach(b => b.classList.add('sweetalert-active'));

				// Handle radio button changes
				document.querySelectorAll('input[name="noUnitAction"]').forEach(radio => {
					radio.addEventListener('change', function() {
						const manualInput = document.getElementById('manualNoUnitInput');
						const customNoUnitField = document.getElementById('customNoUnit');
						if (this.value === 'manual') {
							manualInput.style.display = 'block';
							// Focus and enable the input field (make it editable)
							setTimeout(() => {
								customNoUnitField.disabled = false;
								customNoUnitField.readOnly = false;
								customNoUnitField.focus();
								// Add event listeners for numeric-only input
								customNoUnitField.addEventListener('keypress', function(e) {
									if (!/[0-9]/.test(e.key) && 
										!['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
										e.preventDefault();
									}
								});
								customNoUnitField.addEventListener('input', function(e) {
									this.value = this.value.replace(/[^0-9]/g, '');
									if (this.value === '0') {
										this.value = '';
									}
								});
								customNoUnitField.addEventListener('paste', function(e) {
									e.preventDefault();
									const paste = (e.clipboardData || window.clipboardData).getData('text');
									const numericPaste = paste.replace(/[^0-9]/g, '');
									if (numericPaste && parseInt(numericPaste) > 0) {
										this.value = numericPaste;
									}
								});
							}, 100);
						} else {
							manualInput.style.display = 'none';
						}
					});
				});
			},
			preConfirm: async () => {
				const action = document.querySelector('input[name="noUnitAction"]:checked').value;
				let noUnit = '';
				if (action === 'generate') {
					noUnit = 'AUTO_GENERATE';
					return { action, noUnit };
				} else if (action === 'manual') {
					const customInput = document.getElementById('customNoUnit');
					noUnit = customInput.value.trim();
					if (!noUnit) {
						Swal.showValidationMessage('No Unit tidak boleh kosong');
						return false;
					}
					if (isNaN(noUnit) || parseInt(noUnit) < 1) {
						Swal.showValidationMessage('No Unit harus berupa angka positif (contoh: 5, 10, 15)');
						return false;
					}
					noUnit = parseInt(noUnit);
					try {
						const checkResponse = await fetch('<?= base_url('service/check-no-unit-exists') ?>', {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json',
								'X-Requested-With': 'XMLHttpRequest'
							},
							body: JSON.stringify({ no_unit: noUnit })
						});
						const checkResult = await checkResponse.json();
						if (checkResult.exists) {
							Swal.showValidationMessage(`No Unit ${noUnit} sudah digunakan oleh unit lain. Pilih nomor yang berbeda.`);
							return false;
						}
						return { action, noUnit };
					} catch (error) {
						console.error('Error checking no_unit:', error);
						Swal.showValidationMessage('Gagal mengecek ketersediaan No Unit. Silakan coba lagi.');
						return false;
					}
				} else if (action === 'continue') {
					noUnit = '';
					return { action, noUnit };
				}
				return { action, noUnit };
			}
		}).then((result) => {
			// Kembalikan pointer-events backdrop dan modal Bootstrap setelah SweetAlert2 ditutup
			document.querySelectorAll('.modal').forEach(m => m.classList.remove('sweetalert-disable'));
			document.querySelectorAll('.modal-backdrop').forEach(b => b.classList.remove('sweetalert-active'));
			if (result.isConfirmed) {
				const { action, noUnit } = result.value;
				if (action === 'generate') {
					window.pendingNoUnitUpdate = { unitId, noUnit: 'AUTO_GENERATE' };
					console.log('Unit No Unit will be auto-generated during approval process');
				} else if (action === 'manual') {
					window.pendingNoUnitUpdate = { unitId, noUnit };
					// Update value di select/unit field pada modal Approval Stage
					const unitPick = document.getElementById('approvalUnitPick');
					if (unitPick) {
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
				} else {
					console.log('Continuing without No Unit (not recommended)');
				}
			} else {
				// User cancelled, reset selection
				document.getElementById('approvalUnitPick').value = '';
			}
		});
	}
	
	function updateUnitNoUnit(unitId, noUnit) {
		// This will be handled during the approval process
		// For now, we just store the intention
		window.pendingNoUnitUpdate = { unitId, noUnit };
		return Promise.resolve();
	}
	
	function setupAttachmentSearch() {
		const attSearch = document.getElementById('approvalAttSearch');
		if (attSearch) {
			attSearch.addEventListener('input', function(){
				const q = this.value.trim();
				const url = new URL('<?= base_url('service/data-attachment/simple') ?>', window.location.origin);
				if (q) url.searchParams.set('q', q);
				fetch(url).then(r=>r.json()).then(j=>{
					const sel = document.getElementById('approvalAttPick');
					sel.innerHTML = '<option value="">- (Opsional) -</option>' + (j.data||[]).map(x=>`<option value="${x.id}">${x.label}</option>`).join('');
				});
			});
		}
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
			
			// Handle pending no_unit update if exists
			if (window.pendingNoUnitUpdate) {
				fd.append('update_no_unit', 'true');
				fd.append('no_unit_action', window.pendingNoUnitUpdate.noUnit);
				// Clear the pending update after using it
				delete window.pendingNoUnitUpdate;
			}
		}
		
		fetch(`<?= base_url('service/spk/approve-stage/') ?>${currentSpkId}`, {
			method: 'POST',
			headers: {'X-Requested-With': 'XMLHttpRequest'},
			body: fd
		}).then(r=>r.json()).then(j=>{
			if (j && j.success) {
				bootstrap.Modal.getInstance(document.getElementById('approvalStageModal')).hide();
				// Reload table to update buttons
				load();
				notify(j.message || 'Approval berhasil disimpan', 'success');
			} else {
				notify(j.message || 'Gagal menyimpan approval', 'error');
			}
		});
	});
});
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?= $this->endSection() ?>
