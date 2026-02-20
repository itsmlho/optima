<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$contract  = $contract ?? [];
$id        = $contract['id'] ?? 0;
$noKontrak = $contract['no_kontrak'] ?? 'Contract #' . $id;
$status    = $contract['status'] ?? '';
$statusMap = ['ACTIVE' => 'success', 'EXPIRED' => 'danger', 'PENDING' => 'warning', 'CANCELLED' => 'secondary'];
$statusClass = $statusMap[$status] ?? 'secondary';
$canRenew  = in_array($status, ['ACTIVE', 'EXPIRED']);
$canAmend  = ($status === 'ACTIVE');
?>

<!-- Page Header -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('marketing/kontrak') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Back
    </a>
    <div class="flex-grow-1">
        <h4 class="fw-bold mb-0">
            <i class="fas fa-file-contract me-2 text-primary"></i>Contract Detail
        </h4>
        <small class="text-muted"><?= esc($noKontrak) ?></small>
    </div>
    <!-- Action Buttons -->
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= base_url('marketing/kontrak/edit/' . $id) ?>" class="btn btn-primary">
            <i class="fas fa-edit me-1"></i>Edit
        </a>
        <?php if ($canRenew): ?>
        <button type="button" class="btn btn-success" id="btnRenewal" onclick="openRenewalWizard(<?= $id ?>)">
            <i class="fas fa-sync-alt me-1"></i>Renewal
        </button>
        <?php endif; ?>
        <?php if ($canAmend): ?>
        <button type="button" class="btn btn-warning" id="btnAmendment" onclick="openAmendmentModal(<?= $id ?>)">
            <i class="fas fa-calculator me-1"></i>Change Rate
        </button>
        <?php endif; ?>
        <button type="button" class="btn btn-outline-info" onclick="openHistoryModal(<?= $id ?>)">
            <i class="fas fa-history me-1"></i>History
        </button>
        <button type="button" class="btn btn-danger" onclick="deleteContract(<?= $id ?>)">
            <i class="fas fa-trash me-1"></i>Delete
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- Main Content (Left) -->
    <div class="col-lg-9">

        <!-- Tabs -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <i class="fas fa-file-contract me-2"></i>
                <strong><?= esc($noKontrak) ?></strong>
                <span class="ms-auto badge bg-dark"><?= esc($status) ?></span>
            </div>
            <div class="card-body">

                <ul class="nav nav-tabs nav-fill mb-3" id="detailTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-overview" data-bs-toggle="tab"
                                data-bs-target="#pane-overview" type="button" role="tab">
                            <i class="fas fa-info-circle me-1"></i>Overview
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-units" data-bs-toggle="tab"
                                data-bs-target="#pane-units" type="button" role="tab">
                            <i class="fas fa-truck me-1"></i>Units & Locations
                            <span class="badge bg-primary ms-1" id="totalUnitsCount">–</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-history" data-bs-toggle="tab"
                                data-bs-target="#pane-history" type="button" role="tab">
                            <i class="fas fa-history me-1"></i>History
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-documents" data-bs-toggle="tab"
                                data-bs-target="#pane-documents" type="button" role="tab">
                            <i class="fas fa-file-alt me-1"></i>Documents
                        </button>
                    </li>
                </ul>

                <div class="tab-content">

                    <!-- ── Overview ── -->
                    <div class="tab-pane fade show active" id="pane-overview" role="tabpanel">
                        <!-- Contract Info -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-file-contract me-2"></i><strong>Contract Information</strong></h6>
                            </div>
                            <div class="card-body" id="contractInfoContent">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i><p>Loading...</p>
                                </div>
                            </div>
                        </div>
                        <!-- Customer + Financial row -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-building me-2"></i><strong>Customer Information</strong></h6>
                                    </div>
                                    <div class="card-body" id="customerInfoContent">
                                        <div class="text-center text-muted py-3">Loading...</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-calculator me-2"></i><strong>Financial Summary</strong></h6>
                                    </div>
                                    <div class="card-body" id="financialSummaryContent">
                                        <div class="text-center text-muted py-3">Loading...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── Units & Locations ── -->
                    <div class="tab-pane fade" id="pane-units" role="tabpanel">
                        <div class="card">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-truck me-2"></i><strong>Rented Units by Location</strong></h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="accordion" id="locationsAccordion">
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-spinner fa-spin fa-2x mb-2"></i><p>Loading units...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── History ── -->
                    <div class="tab-pane fade" id="pane-history" role="tabpanel">
                        <div class="card mb-3">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-clock me-2"></i><strong>Contract Timeline</strong></h6>
                                <select class="form-select form-select-sm" style="width:auto" id="historyFilter">
                                    <option value="all">All Events</option>
                                    <option value="contract">Contracts</option>
                                    <option value="amendment">Amendments</option>
                                    <option value="renewal">Renewals</option>
                                </select>
                            </div>
                            <div class="card-body" id="contractTimelineContent">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i><p>Loading history...</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i><strong>Rate Changes</strong></h6>
                            </div>
                            <div class="card-body" id="rateHistoryContent">
                                <div class="text-center text-muted py-3">Loading rate history...</div>
                            </div>
                        </div>
                    </div>

                    <!-- ── Documents ── -->
                    <div class="tab-pane fade" id="pane-documents" role="tabpanel">
                        <div class="card">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-paperclip me-2"></i><strong>Contract Documents</strong></h6>
                                <button class="btn btn-sm btn-primary" onclick="uploadContractDocument()">
                                    <i class="fas fa-upload me-1"></i>Upload Document
                                </button>
                            </div>
                            <div class="card-body" id="documentsListContent">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i><p>Loading documents...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div><!-- /tab-content -->
            </div><!-- /card-body -->
        </div><!-- /card -->

    </div><!-- /col-lg-9 -->

    <!-- Sidebar (Right) -->
    <div class="col-lg-3">

        <!-- Quick Info Card -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Contract Info</h6>
            </div>
            <div class="card-body p-3">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">ID</dt>
                    <dd class="col-7">#<?= esc($id) ?></dd>

                    <dt class="col-5 text-muted">Status</dt>
                    <dd class="col-7">
                        <span class="badge bg-<?= $statusClass ?>"><?= esc($status) ?></span>
                    </dd>

                    <dt class="col-5 text-muted">Type</dt>
                    <dd class="col-7"><?= esc($contract['rental_type'] ?? '—') ?></dd>

                    <dt class="col-5 text-muted">Billing</dt>
                    <dd class="col-7"><?= esc($contract['jenis_sewa'] ?? '—') ?></dd>

                    <dt class="col-5 text-muted">Start</dt>
                    <dd class="col-7">
                        <?= !empty($contract['tanggal_mulai']) ? date('d M Y', strtotime($contract['tanggal_mulai'])) : '—' ?>
                    </dd>

                    <dt class="col-5 text-muted">End</dt>
                    <dd class="col-7">
                        <?php
                        $endDate = $contract['tanggal_berakhir'] ?? null;
                        echo ($endDate && date('Y', strtotime($endDate)) > 1)
                            ? date('d M Y', strtotime($endDate))
                            : '<em class="text-muted">Open-ended</em>';
                        ?>
                    </dd>

                    <dt class="col-5 text-muted">Created</dt>
                    <dd class="col-7">
                        <?= !empty($contract['created_at']) ? date('d M Y', strtotime($contract['created_at'])) : '—' ?>
                    </dd>
                </dl>
            </div>
        </div>

        <!-- Status note -->
        <div class="alert alert-info small">
            <i class="fas fa-info-circle me-1"></i>
            Changes to Status will automatically update the inventory unit allocation status.
        </div>

    </div><!-- /col-lg-3 -->
</div><!-- /row -->

<!-- Include modals used by action buttons -->
<?= $this->include('components/renewal_wizard') ?>
<?= $this->include('components/addendum_prorate') ?>
<?= $this->include('components/asset_history') ?>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
const CONTRACT_ID = <?= (int)$id ?>;
const BASE_URL    = '<?= base_url() ?>';

// ── Helper: format rupiah ────────────────────────────────
function rupiah(v) {
    return 'Rp ' + parseFloat(v || 0).toLocaleString('id-ID');
}

// ── Load Overview ────────────────────────────────────────
function loadOverview() {
    $.ajax({
        url: BASE_URL + 'marketing/kontrak/get/' + CONTRACT_ID,
        type: 'GET',
        success: function(res) {
            if (!res.success || !res.data) {
                $('#contractInfoContent').html('<div class="alert alert-warning">Contract details not found</div>');
                return;
            }
            const c = res.data;

            // Contract Info grid
            const fields = [
                ['Contract Number', c.no_kontrak],
                ['Contract Type',   c.rental_type],
                ['Status',          '<span class="badge bg-' + (c.status === 'ACTIVE' ? 'success' : c.status === 'EXPIRED' ? 'danger' : 'warning') + '">' + c.status + '</span>'],
                ['PO Number',       c.po_number],
                ['Start Date',      c.tanggal_mulai],
                ['End Date',        c.tanggal_berakhir || '<em class="text-muted">Open-ended</em>'],
                ['Billing Type',    c.jenis_sewa],
                ['Billing Method',  c.billing_method],
                ['Notes',           c.keterangan || '—'],
            ];

            let html = '<div class="row">';
            fields.forEach(([label, val]) => {
                html += `<div class="col-md-4 mb-3"><label class="text-muted small">${label}</label><p class="mb-0">${val || '—'}</p></div>`;
            });
            html += '</div>';
            $('#contractInfoContent').html(html);

            // Customer Info
            let cust = `<p class="mb-2"><strong>Customer:</strong><br>${c.customer_name || '—'}</p>`;
            cust    += `<p class="mb-2"><strong>Code:</strong><br>${c.customer_code || '—'}</p>`;
            cust    += `<p class="mb-0"><strong>Contact:</strong><br>${c.contact_person || '—'}`;
            if (c.phone) cust += `<br><i class="fas fa-phone me-1"></i>${c.phone}`;
            cust    += '</p>';
            $('#customerInfoContent').html(cust);

            // Financial Summary
            let fin = `<div class="row text-center">
                <div class="col-6 mb-3">
                    <label class="text-muted small d-block">Total Units</label>
                    <h3 class="mb-0 text-primary">${c.total_units || 0}</h3>
                </div>
                <div class="col-6 mb-3">
                    <label class="text-muted small d-block">Contract Value</label>
                    <h5 class="mb-0 text-success">${rupiah(c.total_value)}</h5>
                </div>`;
            if (c.operator_quantity > 0) {
                fin += `<div class="col-12"><hr class="my-2">
                    <p class="text-muted mb-0"><i class="fas fa-user-tie me-1"></i>Includes ${c.operator_quantity} operator(s)</p>`;
                if (c.operator_monthly_rate) fin += `<small class="text-muted">@ ${rupiah(c.operator_monthly_rate)}/month</small>`;
                fin += '</div>';
            }
            fin += '</div>';
            $('#financialSummaryContent').html(fin);
        },
        error: function() {
            $('#contractInfoContent').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error loading contract details</div>');
        }
    });
}

// ── Load Units ──────────────────────────────────────────
function loadUnits() {
    $('#locationsAccordion').html('<div class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin fa-2x mb-2"></i><p>Loading units...</p></div>');
    $.ajax({
        url: BASE_URL + 'marketing/kontrak/units/' + CONTRACT_ID,
        type: 'GET',
        success: function(res) {
            if (!res.success || !res.data || !res.data.length) {
                $('#locationsAccordion').html('<div class="alert alert-info m-3"><i class="fas fa-info-circle me-2"></i>No units found</div>');
                $('#totalUnitsCount').text('0');
                return;
            }
            const locations = {};
            let total = 0;
            res.data.forEach(u => {
                const loc = u.location_name || 'Unknown Location';
                if (!locations[loc]) locations[loc] = [];
                locations[loc].push(u);
                total++;
            });

            $('#totalUnitsCount').text(total);
            let html = '';
            let idx  = 0;
            for (const [loc, units] of Object.entries(locations)) {
                const aId    = 'loc-' + idx;
                const isOpen = idx === 0;
                html += `<div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button${isOpen ? '' : ' collapsed'}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#${aId}">
                      <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                      <strong>${loc}</strong>
                      <span class="badge bg-primary ms-2">${units.length} unit(s)</span>
                    </button>
                  </h2>
                  <div id="${aId}" class="accordion-collapse collapse${isOpen ? ' show' : ''}">
                    <div class="accordion-body p-0">
                      <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                          <thead class="bg-light"><tr>
                            <th>Unit No</th><th>Type</th><th>Brand/Model</th>
                            <th>Capacity</th><th class="text-end">Rate/Month</th><th>Status</th>
                          </tr></thead><tbody>`;
                units.forEach(u => {
                    html += `<tr>
                        <td><strong>${u.unit_no || '—'}</strong></td>
                        <td>${u.unit_type || '—'}</td>
                        <td>${u.brand_model || '—'}</td>
                        <td>${u.capacity || '—'}</td>
                        <td class="text-end">${rupiah(u.rate_monthly)}</td>
                        <td><span class="badge bg-success">Active</span></td>
                    </tr>`;
                });
                html += `</tbody></table></div></div></div></div>`;
                idx++;
            }
            $('#locationsAccordion').html(html);
        },
        error: function() {
            $('#locationsAccordion').html('<div class="alert alert-danger m-3"><i class="fas fa-exclamation-triangle me-2"></i>Error loading units</div>');
        }
    });
}

// ── Load History ────────────────────────────────────────
function loadHistory() {
    // Timeline
    $.ajax({
        url: BASE_URL + 'marketing/kontrak/getContractHistory/' + CONTRACT_ID,
        type: 'GET',
        success: function(res) {
            if (!res.success || !res.data) {
                $('#contractTimelineContent').html('<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No history data available</div>');
                return;
            }
            const iconMap = { contract: 'fa-file-contract text-primary', amendment: 'fa-edit text-warning', renewal: 'fa-sync-alt text-success' };
            let html = '<div class="timeline">';
            res.data.forEach(ev => {
                const icon = iconMap[ev.type] || 'fa-circle text-secondary';
                html += `<div class="timeline-item">
                    <div class="timeline-marker"><i class="fas ${icon}"></i></div>
                    <div class="timeline-content">
                        <div class="timeline-time">${ev.date}</div>
                        <h6>${ev.description}</h6>`;
                if (ev.reason)      html += `<p class="text-muted mb-0">${ev.reason}</p>`;
                if (ev.total_value) html += `<p class="mb-0"><strong>Value:</strong> ${rupiah(ev.total_value)}</p>`;
                html += `</div></div>`;
            });
            html += '</div>';
            $('#contractTimelineContent').html(html);
        },
        error: function() {
            $('#contractTimelineContent').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error loading history</div>');
        }
    });

    // Rate history
    $.ajax({
        url: BASE_URL + 'marketing/kontrak/getRateHistory/' + CONTRACT_ID,
        type: 'GET',
        success: function(res) {
            if (!res.success || !res.data || !res.data.length) {
                $('#rateHistoryContent').html('<p class="text-muted">No rate changes found</p>');
                return;
            }
            let html = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Date</th><th>Unit</th><th>Old Rate</th><th>New Rate</th><th>Change</th></tr></thead><tbody>';
            res.data.forEach(r => {
                const diff = r.new_rate - r.old_rate;
                const cls  = diff > 0 ? 'text-success' : 'text-danger';
                const ico  = diff > 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                html += `<tr><td>${r.date}</td><td>${r.unit_no}</td><td>${rupiah(r.old_rate)}</td><td>${rupiah(r.new_rate)}</td>
                    <td class="${cls}"><i class="fas ${ico} me-1"></i>${rupiah(Math.abs(diff))}</td></tr>`;
            });
            html += '</tbody></table></div>';
            $('#rateHistoryContent').html(html);
        },
        error: function() {
            $('#rateHistoryContent').html('<div class="alert alert-danger">Error loading rate history</div>');
        }
    });
}

// ── Load Documents ──────────────────────────────────────
function loadDocuments() {
    $.ajax({
        url: BASE_URL + 'marketing/kontrak/documents/' + CONTRACT_ID,
        type: 'GET',
        success: function(res) {
            if (!res.success || !res.data || !res.data.length) {
                $('#documentsListContent').html('<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No documents uploaded yet</div>');
                return;
            }
            const iconMap = { pdf: 'fa-file-pdf text-danger', doc: 'fa-file-word text-primary', docx: 'fa-file-word text-primary', xls: 'fa-file-excel text-success', xlsx: 'fa-file-excel text-success', jpg: 'fa-file-image text-info', jpeg: 'fa-file-image text-info', png: 'fa-file-image text-info' };
            let html = '<div class="list-group list-group-flush">';
            res.data.forEach(d => {
                const ext  = d.file_name.split('.').pop().toLowerCase();
                const icon = iconMap[ext] || 'fa-file text-secondary';
                html += `<div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas ${icon} fa-2x me-3"></i>
                        <strong>${d.file_name}</strong>
                        <br><small class="text-muted">Uploaded: ${d.uploaded_at} by ${d.uploaded_by || 'System'}</small>
                    </div>
                    <div>
                        <a href="${d.file_path}" class="btn btn-sm btn-primary me-1" download><i class="fas fa-download"></i></a>
                        <button class="btn btn-sm btn-danger" onclick="deleteDocument(${d.id})"><i class="fas fa-trash"></i></button>
                    </div>
                </div>`;
            });
            html += '</div>';
            $('#documentsListContent').html(html);
        },
        error: function() {
            $('#documentsListContent').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error loading documents</div>');
        }
    });
}

// ── Stubs for modal-based actions (reuse shared functions from kontrak.php pattern) ──

function uploadContractDocument() {
    alert('Upload document feature coming soon.');
}

function deleteDocument(docId) {
    if (!confirm('Delete this document?')) return;
    $.post(BASE_URL + 'marketing/kontrak/deleteDocument/' + docId, {}, function(res) {
        if (res.success) { loadDocuments(); } else { alert(res.message || 'Failed to delete.'); }
    });
}

function deleteContract(id) {
    if (!confirm('Are you sure you want to delete this contract? This action cannot be undone.')) return;
    $.ajax({
        url: BASE_URL + 'marketing/kontrak/delete/' + id,
        type: 'DELETE',
        success: function(res) {
            if (res.success) {
                window.location.href = BASE_URL + 'marketing/kontrak';
            } else {
                alert(res.message || 'Failed to delete contract.');
            }
        },
        error: function() { alert('Error deleting contract.'); }
    });
}

// These reuse the shared renewal/amendment/history modal functions from kontrak.php
// (they are included via the component includes above)
function openRenewalWizard(id) {
    $.ajax({
        url: BASE_URL + 'marketing/kontrak/get/' + id,
        type: 'GET',
        success: function(res) {
            if (res.success && res.data) {
                const c = res.data;
                $('#renewalParentContractId').val(id);
                $('#renewalOldContractNumber').text(c.no_kontrak);
                $('#renewalCustomerName').text(c.customer_name || 'N/A');
                $('#renewalOldStartDate').text(c.tanggal_mulai || 'N/A');
                $('#renewalOldEndDate').text(c.tanggal_berakhir || 'N/A');
                if (c.tanggal_berakhir) {
                    const oldEnd  = new Date(c.tanggal_berakhir);
                    const newStart = new Date(oldEnd); newStart.setDate(newStart.getDate() + 1);
                    const newEnd  = new Date(newStart); newEnd.setFullYear(newEnd.getFullYear() + 1); newEnd.setDate(newEnd.getDate() - 1);
                    $('#renewalStartDate').val(newStart.toISOString().split('T')[0]);
                    $('#renewalEndDate').val(newEnd.toISOString().split('T')[0]);
                }
                $('#renewalWizardModal').modal('show');
            }
        }
    });
}

function openAmendmentModal(id) {
    $.ajax({
        url: BASE_URL + 'marketing/kontrak/get-active-contracts',
        type: 'GET',
        success: function(res) {
            if (res.success) {
                const $sel = $('#prorateContractId');
                $sel.empty().append('<option value="">-- Select active contract --</option>');
                res.data.forEach(c => $sel.append(new Option(c.no_kontrak + ' - ' + c.customer_name, c.id)));
                $sel.val(id).trigger('change');
                $('#addendumProrateModal').modal('show');
            }
        }
    });
}

function openHistoryModal(id) {
    $('#assetHistoryModal').modal('show');
    $.ajax({ url: BASE_URL + 'marketing/kontrak/getContractHistory/' + id, type: 'GET',
        success: function(r) { if (r.success) renderContractTimeline(r.data); }
    });
    $.ajax({ url: BASE_URL + 'marketing/kontrak/getRateHistory/' + id, type: 'GET',
        success: function(r) { if (r.success && r.data) renderRateHistory(r.data); }
    });
}

// ── Tab lazy-loading ────────────────────────────────────
$(document).ready(function() {
    // Load overview immediately
    loadOverview();

    $('#tab-units').on('shown.bs.tab', function() { loadUnits(); });
    $('#tab-history').on('shown.bs.tab', function() { loadHistory(); });
    $('#tab-documents').on('shown.bs.tab', function() { loadDocuments(); });
});
</script>
<?= $this->endSection() ?>
