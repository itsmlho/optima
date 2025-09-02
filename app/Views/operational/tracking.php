<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>
  
  <!-- Search Form -->
  <div class="card mb-4 shadow-sm border-0">
    <div class="card-header">
      <h6 class="mb-0"><i class="fas fa-search me-2"></i>Pencarian Tracking Pengiriman</h6>
    </div>
    <div class="card-body bg-light">
      <form id="trackingSearchForm" class="row g-3 align-items-end">
        <div class="col-md-4">
          <label for="searchType" class="form-label fw-bold">Cari berdasarkan:</label>
          <select class="form-select form-select-lg" id="searchType" name="search_type">
            <option value="kontrak"><i class="fas fa-file-contract"></i> No. Kontrak/PO</option>
            <option value="spk"><i class="fas fa-clipboard-list"></i> No. SPK</option>
            <option value="di"><i class="fas fa-truck-loading"></i> No. DI</option>
          </select>
        </div>
        <div class="col-md-6">
          <label for="searchValue" class="form-label fw-bold">Nomor:</label>
          <div class="input-group input-group-lg">
            <span class="input-group-text"></span>
            <input type="text" class="form-control" id="searchValue" name="search_value" 
                   placeholder="Masukkan nomor yang ingin dilacak..." autocomplete="off">
          </div>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-search me-2"></i> Lacak
          </button>
        </div>
      </form>
      
      <!-- Quick Search Examples -->
      <div class="mt-3">
        <small class="text-muted">
          <strong>Contoh:</strong> 
          <span class="badge bg-secondary me-2">SPK/202508/001</span>
          <span class="badge bg-secondary me-2">DI/202508/001</span>
          <span class="badge bg-secondary">PO-CL-0488</span>
        </small>
      </div>
    </div>
  </div>

  <!-- Tracking Results -->
  <div id="trackingResults" style="display: none;">
    <!-- Summary Info -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Ringkasan Informasi</h6>
            </div>
        </div>
        <div class="card-body">
            <div id="summaryInfo" class="row"></div>
        </div>
    </div>

    <!-- Progress Timeline -->
    <div class="card mb-4 shadow-sm border-0">
      <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-route me-2"></i>Timeline Pengiriman</h6>
      </div>
      <div class="card-body">
        <div id="progressTimeline"></div>
      </div>
    </div>

    <!-- Detailed Steps -->
    <div class="card shadow-sm border-0">
      <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-list-ol me-2"></i>Detail Tahapan Tracking</h6>
      </div>
      <div class="card-body">
        <div class="accordion" id="trackingAccordion"></div>
      </div>
    </div>

    <!-- Audit Trail Section -->
    <div class="card shadow-sm border-0">
      <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-history me-2"></i>Audit Trail & Activity Log</h6>
      </div>
      <div class="card-body">
        <div id="auditTrailContent" class="table-responsive"></div>
      </div>
    </div>
  </div>

  <!-- No Results or Loading -->
  <div id="infoState" class="alert alert-warning" style="display: none;"></div>
</div>

<style>
/* Elegant White Theme - Clean & Professional */
.tracking-timeline { 
  position: relative; 
  padding: 30px 0; 
  background: white; 
  border: 1px solid #e9ecef; 
  border-radius: 12px; 
  margin: 20px 0; 
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.timeline-container { 
  display: flex; 
  justify-content: space-between; 
  position: relative; 
  margin: 30px 20px; 
}

.timeline-step { 
  display: flex; 
  flex-direction: column; 
  align-items: center; 
  position: relative; 
  flex: 1; 
  text-align: center; 
}

.timeline-icon {
  width: 60px; 
  height: 60px; 
  border-radius: 50%; 
  display: flex; 
  align-items: center; 
  justify-content: center; 
  font-size: 24px;
  margin-bottom: 15px; 
  position: relative; 
  z-index: 2; 
  border: 3px solid #e9ecef; 
  background: white;
  color: #6c757d;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08); 
  cursor: pointer; 
  transition: all 0.3s ease;
}

.timeline-icon:hover { 
  transform: scale(1.05); 
  box-shadow: 0 4px 15px rgba(0,0,0,0.12); 
}

.timeline-icon.completed { 
  background: #28a745; 
  color: white;
  border-color: #28a745;
  box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
}

.timeline-icon.current { 
  background: #007bff; 
  color: white;
  border-color: #007bff;
  box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
  animation: currentPulse 2s infinite;
}

.timeline-icon.pending { 
  background: white; 
  color: #adb5bd;
  border-color: #e9ecef;
}

@keyframes currentPulse {
  0% { box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2), 0 0 0 0 rgba(0, 123, 255, 0.4); }
  70% { box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2), 0 0 0 10px rgba(0, 123, 255, 0); }
  100% { box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2), 0 0 0 0 rgba(0, 123, 255, 0); }
}

.timeline-line { 
  position: absolute; 
  top: 30px; 
  left: 50%; 
  right: -50%; 
  height: 3px; 
  background: #e9ecef; 
  z-index: 1; 
  border-radius: 2px;
  transition: all 0.5s ease;
}

.timeline-step:first-child .timeline-line { left: 50%; }
.timeline-step:last-child .timeline-line { right: 50%; }
.timeline-line.completed { background: #28a745; }

.timeline-title { 
  font-weight: 600; 
  font-size: 13px; 
  margin-bottom: 8px; 
  color: #495057; 
}

.timeline-subtitle { 
  font-size: 12px; 
  color: #6c757d; 
  margin-bottom: 5px; 
}

.timeline-date { 
  font-size: 11px; 
  color: #007bff; 
  font-weight: 500; 
  background: #f8f9fa; 
  padding: 3px 8px; 
  border-radius: 6px; 
  border: 1px solid #e9ecef;
}

/* Elegant Accordion Styles */
.accordion-button { 
  font-size: 1rem; 
  padding: 1.25rem; 
  border: none; 
  background: white;
  color: #495057;
  font-weight: 500;
  transition: all 0.3s ease;
  border-bottom: 1px solid #e9ecef;
}

.accordion-button:not(.collapsed) { 
  background: #f8f9fa; 
  color: #495057; 
  box-shadow: none;
  border-bottom: 1px solid #dee2e6;
}

.accordion-button:focus {
  box-shadow: 0 0 0 0.25rem rgba(108, 117, 125, 0.25);
}

.accordion-body { 
  background: white; 
  border-top: 1px solid #e9ecef; 
}

.accordion-body .row { 
  font-size: 0.9rem; 
  margin-bottom: 1rem; 
}

.detail-label { 
  color: #6c757d; 
  font-weight: 500; 
  font-size: 0.85rem; 
  text-transform: uppercase; 
  letter-spacing: 0.5px; 
}

.detail-value { 
  font-weight: 400; 
  color: #495057; 
  padding: 8px 12px; 
  background: #f8f9fa; 
  border-radius: 6px; 
  margin-top: 4px; 
  border: 1px solid #e9ecef;
}

.delay-positive { 
  color: #dc3545; 
  font-weight: 500; 
  background: #f8d7da; 
  padding: 4px 8px; 
  border-radius: 4px; 
  border: 1px solid #f5c6cb;
}

.delay-negative { 
  color: #28a745; 
  font-weight: 500; 
  background: #d4edda; 
  padding: 4px 8px; 
  border-radius: 4px; 
  border: 1px solid #c3e6cb;
}

/* Elegant Info Cards */
.info-pair { 
  background: white; 
  padding: 15px; 
  border-radius: 8px; 
  border: 1px solid #e9ecef; 
  box-shadow: 0 2px 4px rgba(0,0,0,0.05); 
}

.info-pair .label { 
  font-size: 0.85rem; 
  color: #6c757d; 
  font-weight: 500; 
  text-transform: uppercase; 
  letter-spacing: 0.5px; 
}

.info-pair .value { 
  font-size: 1.1rem; 
  color: #495057; 
  font-weight: 500; 
  margin-top: 5px; 
  display: block; 
}

/* Enhanced Responsive */
@media (max-width: 768px) {
  .timeline-container { 
    flex-direction: column; 
    gap: 25px; 
    margin: 20px 10px; 
  }
  
  .timeline-line { display: none; }
  
  .timeline-step { 
    width: 100%; 
    flex-direction: row; 
    text-align: left; 
    justify-content: flex-start; 
    gap: 20px; 
    background: white; 
    padding: 15px; 
    border-radius: 10px; 
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid #e9ecef;
  }
  
  .timeline-icon { 
    margin-bottom: 0; 
    width: 50px; 
    height: 50px; 
    font-size: 20px; 
  }
  
  .timeline-content { flex: 1; }
  .tracking-timeline { padding: 20px 10px; }
}

/* Loading Animation */
.loading-spinner {
  display: inline-block; 
  width: 20px; 
  height: 20px;
  border: 2px solid #f3f3f3; 
  border-top: 2px solid #6c757d;
  border-radius: 50%; 
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Clean Status Badges */
.status-badge-completed { 
  background: #d4edda !important; 
  color: #155724 !important;
  border: 1px solid #c3e6cb !important;
}

.status-badge-current { 
  background: #cce7ff !important; 
  color: #004085 !important;
  border: 1px solid #99d6ff !important;
}

.status-badge-pending { 
  background: #f8f9fa !important; 
  color: #6c757d !important;
  border: 1px solid #e9ecef !important;
}

/* Audit Trail Styles */
.audit-trail-table {
  font-size: 14px;
}

.audit-trail-table th {
  background: #f8f9fa;
  border-bottom: 2px solid #dee2e6;
  font-weight: 600;
  color: #495057;
}

.audit-trail-table td {
  vertical-align: middle;
  border-bottom: 1px solid #dee2e6;
}

.activity-type-badge {
  font-size: 11px;
  padding: 4px 8px;
  border-radius: 12px;
  font-weight: 500;
}

.activity-type-created { background: #d1ecf1; color: #0c5460; }
.activity-type-updated { background: #d4edda; color: #155724; }
.activity-type-status { background: #fff3cd; color: #856404; }
.activity-type-location { background: #f8d7da; color: #721c24; }
.activity-type-price { background: #e2e3e5; color: #383d41; }

.user-info {
  font-size: 12px;
  color: #6c757d;
}

.timestamp-info {
  font-size: 11px;
  color: #6c757d;
  white-space: nowrap;
}

/* Clean Card Headers */
.bg-gradient-primary { 
  background: #495057 !important; 
  color: white !important;
}

.bg-gradient-info { 
  background: #6c757d !important; 
  color: white !important;
}

.bg-gradient-success { 
  background: #28a745 !important; 
  color: white !important;
}

.bg-gradient-warning { 
  background: #ffc107 !important; 
  color: #212529 !important;
}

/* Print Styles */
@media print {
  .no-print { display: none !important; }
  .card { 
    border: 1px solid #dee2e6 !important; 
    box-shadow: none !important; 
  }
  .bg-gradient-primary, .bg-gradient-info, .bg-gradient-success, .bg-gradient-warning { 
    background: #f8f9fa !important; 
    color: #000 !important; 
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchForm = document.getElementById('trackingSearchForm');
  const resultsDiv = document.getElementById('trackingResults');
  const infoStateDiv = document.getElementById('infoState');
  const summaryDiv = document.getElementById('summaryInfo');
  const timelineDiv = document.getElementById('progressTimeline');
  const accordionDiv = document.getElementById('trackingAccordion');

  // Auto-focus search input
  document.getElementById('searchValue').focus();

  // Keyboard shortcuts
  document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'f') {
      e.preventDefault();
      document.getElementById('searchValue').focus();
    }
  });

  // Search form validation and submission
  searchForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const searchValue = document.getElementById('searchValue').value.trim();
    if (!searchValue) {
      showAlert('Mohon masukkan nomor yang ingin dilacak', 'warning');
      return;
    }
    
    const requestData = {
      search_type: document.getElementById('searchType').value,
      search_value: searchValue
    };
    
    console.log('Sending request:', requestData);
    
    showInfoState('<div class="text-center"><div class="loading-spinner me-2"></div>Mencari data tracking...</div>', 'alert-info');
    
    fetch(`<?= base_url('operational/tracking-search') ?>`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json', 
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify(requestData)
    })
    .then(response => {
      console.log('Response status:', response.status);
      console.log('Response headers:', response.headers);
      
      if (!response.ok) {
        return response.text().then(text => {
          console.error('Error response body:', text);
          throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
        });
      }
      return response.json();
    })
    .then(data => {
      console.log('Response data:', data);
      if (data.success && data.data) {
        resultsDiv.style.display = 'block';
        infoStateDiv.style.display = 'none';
        renderTrackingResults(data.data);
        
        // Scroll to results
        resultsDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
        showAlert('Data tracking berhasil ditemukan!', 'success');
      } else {
        showInfoState(`
          <div class="text-center">
            <i class="fas fa-search-minus fa-3x text-muted mb-3"></i>
            <h5>Data tidak ditemukan</h5>
            <p class="mb-0">Pastikan nomor yang Anda masukkan sudah benar.</p>
            <small class="text-muted">Coba gunakan nomor lengkap atau periksa kembali penulisan.</small>
          </div>
        `, 'alert-warning');
      }
    })
    .catch(error => {
      console.error('Fetch Error:', error);
      showInfoState(`
        <div class="text-center">
          <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
          <h5>Terjadi kesalahan</h5>
          <p class="mb-0">Gagal memuat data tracking. Silakan coba lagi.</p>
          <small class="text-muted">Error: ${error.message}</small>
        </div>
      `, 'alert-danger');
    });
  });

  function showInfoState(message, alertClass) {
    resultsDiv.style.display = 'none';
    infoStateDiv.className = `alert ${alertClass}`;
    infoStateDiv.innerHTML = message;
    infoStateDiv.style.display = 'block';
  }

  function showAlert(message, type) {
    // Create toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
      if (toast.parentNode) {
        toast.remove();
      }
    }, 5000);
  }

  function renderTrackingResults(data) {
    renderSummary(data);
    renderTimeline(data);
    renderAccordion(data);
    
    // Load audit trail for units involved
    loadAuditTrail(data);

    // Re-initialize popovers after rendering new elements
    const newPopoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    newPopoverTriggerList.forEach(el => new bootstrap.Popover(el, { html: true, trigger: 'hover focus' }));
  }

  function renderSummary(data) {
    const picInfo = data.spk?.pic ? `${data.spk.pic}${data.spk.kontak ? ' (' + data.spk.kontak + ')' : ''}` : '-';
    const unitInfo = getUnitInfo(data); // Assuming this function exists
    const numbersInfo = `
        <span class="text-muted">PO:</span> <strong class="me-3">${data.po_kontrak_nomor || '-'}</strong>
        <span class="text-muted">SPK:</span> <strong class="me-3">${data.spk?.nomor_spk || '-'}</strong>
        <span class="text-muted">DI:</span> <strong>${data.di?.nomor_di || '-'}</strong>
    `;


    summaryDiv.innerHTML = `
        <div class="row align-items-center">
            <div class="col-lg-7 mb-3">
                <div class="info-pair">
                    <span class="label">Pelanggan & Lokasi</span>
                    <h6 class="value mb-0">${data.di?.pelanggan || data.spk?.pelanggan || '-'}</h6>
                    <small class="text-muted">${data.di?.lokasi || data.spk?.lokasi || '-'}</small>
                </div>
            </div>
            <div class="col-lg-5 mb-3">
                <div class="info-pair">
                    <span class="label">Informasi Unit & PIC</span>
                    <h6 class="value mb-0">${unitInfo}</h6>
                    <small class="text-muted">PIC: ${picInfo}</small>
                </div>
            </div>
            <div class="col-12"><hr class="my-2"></div>
            <div class="col-12">
                 <div class="info-pair text-center">
                    <small class="value">${numbersInfo}</small>
                 </div>
            </div>
        </div>
    `;
  }


  function renderTimeline(data) {
    const steps = getStepsConfig(data);
    let timelineHtml = '<div class="tracking-timeline"><div class="timeline-container">';
    
    steps.forEach((step, index) => {
      const isCompleted = step.actualDate && step.actualDate !== '-';
      const isCurrent = !isCompleted && (index === 0 || (steps[index-1].actualDate && steps[index-1].actualDate !== '-'));
      const statusClass = isCompleted ? 'completed' : (isCurrent ? 'current' : 'pending');
      
      timelineHtml += `
        <div class="timeline-step">
          ${index > 0 ? `<div class="timeline-line ${isCompleted || isCurrent ? 'completed' : ''}"></div>` : ''}
          <div class="timeline-icon ${statusClass}" 
               data-bs-toggle="popover" 
               title="${step.step}" 
               data-bs-content="${step.popoverContent}">
            <i class="${step.icon}"></i>
          </div>
          <div class="timeline-content">
            <div class="timeline-title">${step.step}</div>
            <div class="timeline-subtitle">${step.event}</div>
            ${isCompleted ? `<div class="timeline-date">${formatDateTime(step.actualDate)}</div>` : ''}
          </div>
        </div>
      `;
    });
    
    timelineHtml += '</div></div>';
    timelineDiv.innerHTML = timelineHtml;
  }

  function renderAccordion(data) {
    const steps = getStepsConfig(data);
    let accordionHtml = '';

    steps.forEach((step, index) => {
        const isCompleted = step.actualDate && step.actualDate !== '-';
        const isCurrent = !isCompleted && (index === 0 || (steps[index-1].actualDate && steps[index-1].actualDate !== '-'));
        const statusBadge = isCompleted ? 
          '<span class="badge status-badge-completed">✓ Selesai</span>' : 
          (isCurrent ? '<span class="badge status-badge-current">⟳ Proses</span>' : 
          '<span class="badge status-badge-pending">○ Menunggu</span>');
        
        accordionHtml += `
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading-${index}">
                <button class="accordion-button ${index > 0 ? 'collapsed' : ''}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-${index}" aria-expanded="${index === 0}" aria-controls="collapse-${index}">
                    <div class="d-flex w-100 align-items-center">
                        <i class="${step.icon} me-3 text-primary"></i>
                        ${statusBadge}
                        <strong class="mx-3">${step.step}</strong>
                        <small class="ms-auto text-muted">${isCompleted ? formatDateTime(step.actualDate) : (step.estimatedTime || '')}</small>
                    </div>
                </button>
            </h2>
            <div id="collapse-${index}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" aria-labelledby="heading-${index}">
                <div class="accordion-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="detail-label"><i class="fas fa-user me-1"></i>Penanggung Jawab</div>
                            <div class="detail-value">${step.pic || 'Belum ditentukan'}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label"><i class="fas fa-calendar-plus me-1"></i>Tanggal Rencana</div>
                            <div class="detail-value">${formatDateTime(step.plannedDate)}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label"><i class="fas fa-calendar-check me-1"></i>Tanggal Aktual</div>
                            <div class="detail-value">${formatDateTime(step.actualDate)}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label"><i class="fas fa-clock me-1"></i>Status Waktu</div>
                            <div class="detail-value">${step.delay || 'Belum selesai'}</div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="detail-label"><i class="fas fa-info-circle me-1"></i>Detail & Catatan</div>
                            <div class="detail-value">${step.details || 'Tidak ada catatan tambahan'}</div>
                            ${step.requirements ? `
                            <div class="detail-label mt-3"><i class="fas fa-tasks me-1"></i>Persyaratan</div>
                            <div class="detail-value">${step.requirements}</div>
                            ` : ''}
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label"><i class="fas fa-chart-pie me-1"></i>Informasi Status</div>
                            <div class="detail-value">
                                ${isCompleted ? 
                                  `<span class="badge bg-success">Tahap Selesai</span><br><small class="text-muted">Dilanjutkan ke tahap berikutnya</small>` :
                                  (isCurrent ? 
                                    `<span class="badge bg-primary">Sedang Dikerjakan</span><br><small class="text-muted">Tahap sedang dalam proses</small>` :
                                    `<span class="badge bg-secondary">Menunggu</span><br><small class="text-muted">Menunggu tahap sebelumnya selesai</small>`
                                  )
                                }
                            </div>
                            ${step.estimatedCompletion ? `
                            <div class="detail-label mt-2"><i class="fas fa-hourglass-half me-1"></i>Estimasi Selesai</div>
                            <div class="detail-value">${step.estimatedCompletion}</div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `;
    });
    accordionDiv.innerHTML = accordionHtml;
  }

  function getStepsConfig(data) {
    const spkSpecs = data.spk?.spesifikasi ? JSON.parse(data.spk.spesifikasi) : {};
    
    return [
      {
        step: 'SPK Dibuat', event: 'Marketing', icon: 'fas fa-file-signature',
        plannedDate: data.spk?.created_at, actualDate: data.spk?.created_at,
        pic: data.spk?.created_by_name || 'Marketing',
        details: `Pelanggan: ${data.spk?.pelanggan || '-'}. PIC: ${data.spk?.pic || '-'}. Kontak: ${data.spk?.kontak || '-'}`,
        requirements: 'Dokumen kontrak dan spesifikasi unit telah lengkap',
        estimatedCompletion: formatDateTime(data.spk?.created_at),
        popoverContent: `Dibuat oleh: <strong>${data.spk?.created_by_name || '-'}</strong><br>Tanggal: ${formatDateTime(data.spk?.created_at)}<br>Jenis: ${data.spk?.jenis_spk || '-'}`
      },
      {
        step: 'Persiapan Unit', event: 'Service Team', icon: 'fas fa-tools',
        plannedDate: data.spk?.persiapan_unit_estimasi_mulai, actualDate: data.spk?.persiapan_unit_tanggal_approve,
        pic: data.spk?.persiapan_unit_mekanik || 'Tim Service',
        delay: calculateDelay(data.spk?.persiapan_unit_estimasi_selesai, data.spk?.persiapan_unit_tanggal_approve),
        details: `Unit ID: ${spkSpecs.selected?.unit_id || '-'}. Aksesoris: ${data.spk?.persiapan_aksesoris_tersedia ? JSON.parse(data.spk.persiapan_aksesoris_tersedia).join(', ') : 'Tidak ada data'}`,
        requirements: 'Unit tersedia di inventory dan dalam kondisi baik',
        estimatedCompletion: formatDateTime(data.spk?.persiapan_unit_estimasi_selesai),
        popoverContent: `Mekanik: <strong>${data.spk?.persiapan_unit_mekanik || '-'}</strong><br>Estimasi: ${formatDateTime(data.spk?.persiapan_unit_estimasi_selesai)}`
      },
      {
        step: 'Fabrikasi', event: 'Workshop', icon: 'fas fa-hammer',
        plannedDate: data.spk?.fabrikasi_estimasi_mulai, actualDate: data.spk?.fabrikasi_tanggal_approve,
        pic: data.spk?.fabrikasi_mekanik || 'Tim Fabrikasi',
        delay: calculateDelay(data.spk?.fabrikasi_estimasi_selesai, data.spk?.fabrikasi_tanggal_approve),
        details: `Attachment ID: ${data.spk?.fabrikasi_attachment_id || '-'}. Merk: ${spkSpecs.attachment_merk || '-'}. Tipe: ${spkSpecs.attachment_tipe || '-'}`,
        requirements: 'Fabrikasi attachment dan modifikasi sesuai spesifikasi',
        estimatedCompletion: formatDateTime(data.spk?.fabrikasi_estimasi_selesai),
        popoverContent: `Mekanik: <strong>${data.spk?.fabrikasi_mekanik || '-'}</strong><br>Target: ${formatDateTime(data.spk?.fabrikasi_estimasi_selesai)}`
      },
      {
        step: 'Painting', event: 'Finishing', icon: 'fas fa-paint-brush',
        plannedDate: data.spk?.painting_estimasi_mulai, actualDate: data.spk?.painting_tanggal_approve,
        pic: data.spk?.painting_mekanik || 'Tim Painting',
        delay: calculateDelay(data.spk?.painting_estimasi_selesai, data.spk?.painting_tanggal_approve),
        details: `Finishing dan pengecatan sesuai standar. Departemen: ${spkSpecs.departemen_id || '-'}`,
        requirements: 'Pengecatan dan finishing sesuai spesifikasi pelanggan',
        estimatedCompletion: formatDateTime(data.spk?.painting_estimasi_selesai),
        popoverContent: `Mekanik: <strong>${data.spk?.painting_mekanik || '-'}</strong><br>Selesai: ${formatDateTime(data.spk?.painting_estimasi_selesai)}`
      },
      {
        step: 'PDI Check', event: 'Quality Control', icon: 'fas fa-check-circle',
        plannedDate: data.spk?.pdi_estimasi_mulai, actualDate: data.spk?.pdi_tanggal_approve,
        pic: data.spk?.pdi_mekanik || 'QC Inspector',
        delay: calculateDelay(data.spk?.pdi_estimasi_selesai, data.spk?.pdi_tanggal_approve),
        details: `Catatan PDI: ${data.spk?.pdi_catatan || 'Pemeriksaan standar kualitas'}. Kapasitas: ${spkSpecs.kapasitas_id || '-'}`,
        requirements: 'Unit lulus semua pemeriksaan kualitas dan safety',
        estimatedCompletion: formatDateTime(data.spk?.pdi_estimasi_selesai),
        popoverContent: `Inspector: <strong>${data.spk?.pdi_mekanik || '-'}</strong><br>Hasil: Unit siap kirim`
      },
      {
        step: 'DI Dibuat', event: 'Operational', icon: 'fas fa-file-invoice',
        plannedDate: data.spk?.pdi_tanggal_approve, actualDate: data.di?.dibuat_pada,
        pic: data.di?.dibuat_oleh_name || 'Tim Operational',
        delay: calculateDelay(data.spk?.pdi_tanggal_approve, data.di?.dibuat_pada),
        details: `Nomor DI: ${data.di?.nomor_di || '-'}. Status: ${data.di?.status || '-'}. Catatan: ${data.di?.catatan || 'Tidak ada catatan'}`,
        requirements: 'SPK telah selesai dan siap untuk pengiriman',
        estimatedCompletion: 'Segera setelah PDI selesai',
        popoverContent: `Dibuat oleh: <strong>${data.di?.dibuat_oleh_name || '-'}</strong><br>Tujuan: ${data.di?.lokasi || '-'}`
      },
      {
        step: 'Persiapan Kirim', event: 'Logistics', icon: 'fas fa-calendar-alt',
        plannedDate: data.di?.dibuat_pada, actualDate: data.di?.status === 'DISPATCHED' || data.di?.status === 'ARRIVED' ? data.di?.diperbarui_pada : null,
        pic: 'Tim Logistik',
        delay: calculateDelay(data.di?.tanggal_kirim, data.di?.diperbarui_pada),
        details: `Tanggal Kirim: ${formatDateTime(data.di?.tanggal_kirim)}. Driver: ${data.di?.driver || '-'}. Kendaraan: ${data.di?.vehicle || '-'}`,
        requirements: 'Penjadwalan driver dan kendaraan angkut',
        estimatedTime: formatDateTime(data.di?.tanggal_kirim),
        popoverContent: `Dijadwalkan: ${formatDateTime(data.di?.tanggal_kirim)}<br>Status: Persiapan pengiriman`
      },
      {
        step: 'Berangkat', event: 'On The Way', icon: 'fas fa-truck',
        plannedDate: data.di?.tanggal_kirim, actualDate: data.di?.status === 'DISPATCHED' || data.di?.status === 'ARRIVED' ? data.di?.diperbarui_pada : null,
        pic: data.di?.driver || 'Driver',
        delay: calculateDelay(data.di?.tanggal_kirim, data.di?.diperbarui_pada),
        details: `Kendaraan: ${data.di?.vehicle || '-'}. No. Polisi: ${data.di?.license_plate || '-'}. Rute: ${data.di?.route || 'Standar'}`,
        requirements: 'Unit telah dimuat dan siap dikirim ke lokasi pelanggan',
        estimatedTime: 'Sesuai jadwal pengiriman',
        popoverContent: `Driver: <strong>${data.di?.driver || '-'}</strong><br>Kendaraan: ${data.di?.vehicle || '-'}`
      },
      {
        step: 'Sampai di Tujuan', event: 'Delivered', icon: 'fas fa-flag-checkered',
        plannedDate: data.di?.estimated_arrival, actualDate: data.di?.status === 'ARRIVED' ? data.di?.diperbarui_pada : null,
        pic: data.di?.receiver || 'Penerima',
        delay: calculateDelay(data.di?.estimated_arrival, data.di?.status === 'ARRIVED' ? data.di?.diperbarui_pada : null),
        details: `Diterima oleh: ${data.di?.receiver || 'Belum ada konfirmasi'}. Kondisi: ${data.di?.delivery_condition || 'Baik'}. Lokasi: ${data.di?.lokasi || '-'}`,
        requirements: 'Unit diterima pelanggan dalam kondisi baik dan sesuai spesifikasi',
        estimatedTime: 'Sesuai estimasi perjalanan',
        popoverContent: `Penerima: <strong>${data.di?.receiver || 'Belum konfirmasi'}</strong><br>Lokasi: ${data.di?.lokasi || '-'}`
      }
    ];
  }

  function formatDateTime(dateStr) {
    if (!dateStr || dateStr === '-') return '-';
    try {
      const date = new Date(dateStr);
      if (isNaN(date.getTime())) return dateStr;
      
      const now = new Date();
      const diffMs = now - date;
      const diffDays = Math.floor(diffMs / 86400000);
      
      let formatted = date.toLocaleString('id-ID', {
        day: '2-digit', month: 'short', year: 'numeric', 
        hour: '2-digit', minute: '2-digit'
      }).replace('.', ':');
      
      // Add relative time for recent dates
      if (diffDays === 0) {
        formatted += ' (Hari ini)';
      } else if (diffDays === 1) {
        formatted += ' (Kemarin)';
      } else if (diffDays > 1 && diffDays <= 7) {
        formatted += ` (${diffDays} hari lalu)`;
      }
      
      return formatted;
    } catch (e) { 
      return dateStr; 
    }
  }

  function calculateDelay(plannedStr, actualStr) {
    if (!plannedStr || !actualStr || plannedStr === '-' || actualStr === '-') return '-';
    try {
      const planned = new Date(plannedStr);
      const actual = new Date(actualStr);
      const diffMs = actual - planned;
      
      const diffDays = Math.floor(diffMs / 86400000); // days
      const diffHrs = Math.floor((diffMs % 86400000) / 3600000); // hours
      const diffMins = Math.round(((diffMs % 86400000) % 3600000) / 60000); // minutes

      let result = '';
      if (diffMs < 0) {
        result = `Lebih cepat `;
        if (Math.abs(diffDays) > 0) result += `${Math.abs(diffDays)} hari `;
        if (Math.abs(diffHrs) > 0) result += `${Math.abs(diffHrs)} jam `;
        return `<span class="delay-negative">${result.trim()}</span>`;
      } else if (diffMs > 0) {
        result = `Terlambat `;
        if (diffDays > 0) result += `${diffDays} hari `;
        if (diffHrs > 0) result += `${diffHrs} jam `;
        if (diffDays === 0 && diffHrs === 0) result += `${diffMins} menit`;
        return `<span class="delay-positive">${result.trim()}</span>`;
      } else {
        return 'Tepat Waktu';
      }
    } catch (e) { return '-'; }
  }

  function getUnitInfo(data) {
    const spkSpecs = data.spk?.spesifikasi ? JSON.parse(data.spk.spesifikasi) : {};
    const merk = spkSpecs.merk_unit || '-';
    const tipe = spkSpecs.tipe_jenis || '-';
    const kapasitas = spkSpecs.kapasitas_id || '-';
    return `${merk} ${tipe} (${kapasitas})`;
  }

  function calculateProgress(data) {
    const steps = getStepsConfig(data);
    const completedSteps = steps.filter(step => step.actualDate && step.actualDate !== '-').length;
    return Math.round((completedSteps / steps.length) * 100);
  }

  // Load audit trail for tracking data
  function loadAuditTrail(data) {
    const auditDiv = document.getElementById('auditTrailContent');
    auditDiv.innerHTML = '<div class="text-center"><div class="loading-spinner me-2"></div>Loading audit trail...</div>';
    
    // Collect unit IDs from tracking data
    let unitIds = [];
    
    // From SPK units
    if (data.spk && data.spk.units) {
      unitIds = unitIds.concat(data.spk.units.map(unit => unit.unit_id).filter(Boolean));
    }
    
    // From DI units
    if (data.di && data.di.units) {
      unitIds = unitIds.concat(data.di.units.map(unit => unit.unit_id).filter(Boolean));
    }
    
    // Remove duplicates
    unitIds = [...new Set(unitIds)];
    
    if (unitIds.length === 0) {
      auditDiv.innerHTML = '<div class="text-center text-muted">No unit data available for audit trail</div>';
      return;
    }
    
    // Fetch audit trail data
    fetch(`<?= base_url('operational/audit-trail') ?>`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json', 
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ unit_ids: unitIds })
    })
    .then(response => response.json())
    .then(auditData => {
      if (auditData.success && auditData.data && auditData.data.length > 0) {
        renderAuditTrail(auditData.data);
      } else {
        auditDiv.innerHTML = '<div class="text-center text-muted">No audit trail data found</div>';
      }
    })
    .catch(error => {
      console.error('Error loading audit trail:', error);
      auditDiv.innerHTML = '<div class="text-center text-danger">Error loading audit trail data</div>';
    });
  }
  
  function renderAuditTrail(auditData) {
    const auditDiv = document.getElementById('auditTrailContent');
    
    let html = `
      <table class="table table-sm audit-trail-table">
        <thead>
          <tr>
            <th>Unit</th>
            <th>Activity</th>
            <th>Description</th>
            <th>User</th>
            <th>Date & Time</th>
          </tr>
        </thead>
        <tbody>
    `;
    
    auditData.forEach(log => {
      const activityClass = getActivityTypeClass(log.activity_type);
      const formattedDate = formatDateTime(log.created_at);
      
      html += `
        <tr>
          <td><strong>Unit ${log.unit_id}</strong></td>
          <td>
            <span class="activity-type-badge ${activityClass}">
              ${log.activity_type.replace('_', ' ')}
            </span>
          </td>
          <td>${log.activity_description}</td>
          <td>
            <div>${log.user_name}</div>
            <div class="user-info">${log.user_role}</div>
          </td>
          <td class="timestamp-info">${formattedDate}</td>
        </tr>
      `;
    });
    
    html += '</tbody></table>';
    auditDiv.innerHTML = html;
  }
  
  function getActivityTypeClass(activityType) {
    const typeClasses = {
      'CREATED': 'activity-type-created',
      'UPDATED': 'activity-type-updated', 
      'STATUS_CHANGED': 'activity-type-status',
      'LOCATION_CHANGED': 'activity-type-location',
      'PRICE_CHANGED': 'activity-type-price',
      'KONTRAK_ASSIGNED': 'activity-type-updated',
      'SPK_ASSIGNED': 'activity-type-updated'
    };
    return typeClasses[activityType] || 'activity-type-updated';
  }
});
</script>

<?= $this->endSection() ?>
