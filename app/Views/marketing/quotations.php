<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<!-- Statistics Cards -->
<div class="row mt-3 mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-primary-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-file-text stat-icon text-primary"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-total-quotations">0</div>
                    <div class="text-muted">Total Quotations</div>
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
                    <div class="text-muted">Pending</div>
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
                    <div class="text-muted">Approved</div>
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
                    <div class="text-muted">Rejected</div>
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
                Quotations Management
            </h5>
            <p class="text-muted small mb-0">Kelola penawaran harga untuk pelanggan</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quotationModal">
            <i class="bi bi-plus-circle me-2"></i>Add Quotation
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="quotationsTable" class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Quotation Number</th>
                        <th>Customer</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Valid Until</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated by DataTables AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Quotation Modal -->
<div class="modal fade" id="quotationModal" tabindex="-1" aria-labelledby="quotationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quotationModalLabel">Add New Quotation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quotationForm">
                <div class="modal-body">
                    <input type="hidden" id="quotationId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quotationNumber" class="form-label">Quotation Number</label>
                                <input type="text" class="form-control" id="quotationNumber" name="quotation_number" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customerId" class="form-label">Customer</label>
                                <select class="form-select" id="customerId" name="customer_id" required>
                                    <option value="">Select Customer</option>
                                    <!-- Options will be loaded dynamically -->
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="validUntil" class="form-label">Valid Until</label>
                                <input type="date" class="form-control" id="validUntil" name="valid_until" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Quotation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
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
                        <button class="nav-link active" id="quotation-info-tab" data-bs-toggle="tab" data-bs-target="#quotation-info-content" type="button">
                            <i class="fas fa-file-alt me-1"></i>Quotation Info
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications-content" type="button">
                            <i class="fas fa-cogs me-1"></i>Specifications (<span id="specCountQuotation">0</span>)
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="quotationDetailTabContent">
                    <!-- Quotation Info Tab -->
                    <div class="tab-pane fade show active" id="quotation-info-content" role="tabpanel">
                        <div id="detailContent">
                            <!-- Content will be loaded dynamically -->
                        </div>
                    </div>

                    <!-- Specifications Tab -->
                    <div class="tab-pane fade" id="specifications-content" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><strong>Request Spesifikasi untuk dasar pembuatan SPK</strong></h6>
                            <div class="btn-group" role="group">
                                <button class="btn btn-primary btn-sm" onclick="openAddSpecificationModal()">
                                    <i class="fas fa-plus me-1"></i>Tambah Unit
                                </button>
                                <button class="btn btn-success btn-sm" onclick="openAddAttachmentModal()">
                                    <i class="fas fa-puzzle-piece me-1"></i>Tambah Attachment
                                </button>
                            </div>
                        </div>
                        <br>

                        <div id="spesifikasiListContract">
                            <p class="text-muted">Memuat spesifikasi...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Specification Modal -->
<div class="modal fade" id="addSpecificationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Add Unit Specification</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSpecificationForm" method="post" action="javascript:void(0)">
                <div class="modal-body">
                    <input type="hidden" name="quotation_id" id="specQuotationId">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Quantity Required</label>
                            <input type="number" class="form-control" name="jumlah_dibutuhkan" min="1" value="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Specification Name</label>
                            <input type="text" class="form-control" name="catatan_spek" placeholder="Optional">
                            <small class="text-muted">Enter description, e.g. "Specification 1", "Spare Unit", "Additional Unit", etc.</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Monthly Rental Price <span class="text-danger" id="hargaRequired">*</span></label>
                            <input type="number" class="form-control" name="harga_per_unit_bulanan" step="0.01" placeholder="Rp per unit per month" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Daily Rental Price</label>
                            <input type="number" class="form-control" name="harga_per_unit_harian" step="0.01" placeholder="Rp per unit per day">
                        </div>
                        
                        <div class="col-12"><hr><h6>Technical Specifications</h6></div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Department <span class="text-danger">*</span></label>
                            <select class="form-select" name="departemen_id" id="specDepartemen" required></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Unit Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="tipe_unit_id" id="specTipeUnit" required>
                                <option value="">-- Select Unit Type --</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Capacity</label>
                            <select class="form-select" name="kapasitas_id" id="specKapasitas">
                                <option value="">-- Select Capacity --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Unit Brand</label>
                            <select class="form-select" name="merk_unit" id="specMerkUnit">
                                <option value="">-- Select Brand --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Battery Type</label>
                            <select class="form-select" name="jenis_baterai" id="specJenisBaterai">
                                <option value="">-- Select Battery --</option>
                            </select>
                            <small class="text-muted">Available for Electric units only</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Charger</label>
                            <select class="form-select" name="charger_id" id="specCharger"></select>
                            <small class="text-muted">Available for Electric units only</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Attachment Type</label>
                            <select class="form-select" name="attachment_tipe" id="specAttachmentTipe"></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Valve</label>
                            <select class="form-select" name="valve_id" id="specValve"></select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Mast</label>
                            <select class="form-select" name="mast_id" id="specMast"></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tire</label>
                            <select class="form-select" name="ban_id" id="specBan"></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Wheel</label>
                            <select class="form-select" name="roda_id" id="specRoda"></select>
                        </div>
                        
                        <!-- Accessories Section -->
                        <div class="col-12"><hr><h6>Unit Accessories</h6></div>
                        <div class="col-12">
                            <div class="row g-2">
                                <!-- Row 1 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="LAMPU UTAMA" id="acc_lampu_utama">
                                        <label class="form-check-label" for="acc_lampu_utama">Main Light</label>
                                        <small class="text-muted">(Main, Reverse, Signal, Stop)</small>
                                    </div>
                                </div>
                                
                                <!-- Row 2 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="BLUE SPOT" id="acc_blue_spot">
                                        <label class="form-check-label" for="acc_blue_spot">Blue Spot</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="RED LINE" id="acc_red_line">
                                        <label class="form-check-label" for="acc_red_line">Red Line</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="WORK LIGHT" id="acc_work_light">
                                        <label class="form-check-label" for="acc_work_light">Work Light</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="ROTARY LAMP" id="acc_rotary_lamp">
                                        <label class="form-check-label" for="acc_rotary_lamp">Rotary Lamp</label>
                                    </div>
                                </div>
                                
                                <!-- Row 3 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="BACK BUZZER" id="acc_back_buzzer">
                                        <label class="form-check-label" for="acc_back_buzzer">Back Buzzer</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="CAMERA AI" id="acc_camera_ai">
                                        <label class="form-check-label" for="acc_camera_ai">Camera AI</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="CAMERA" id="acc_camera">
                                        <label class="form-check-label" for="acc_camera">Camera</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="SENSOR PARKING" id="acc_sensor_parking">
                                        <label class="form-check-label" for="acc_sensor_parking">Sensor Parking</label>
                                    </div>
                                </div>
                                
                                <!-- Row 4 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="SPEED LIMITER" id="acc_speed_limiter">
                                        <label class="form-check-label" for="acc_speed_limiter">Speed Limiter</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="LASER FORK" id="acc_laser_fork">
                                        <label class="form-check-label" for="acc_laser_fork">Laser Fork</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="VOICE ANNOUNCER" id="acc_voice_announcer">
                                        <label class="form-check-label" for="acc_voice_announcer">Voice Announcer</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="HORN SPEAKER" id="acc_horn_speaker">
                                        <label class="form-check-label" for="acc_horn_speaker">Horn Speaker</label>
                                    </div>
                                </div>
                                
                                <!-- Row 5 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="HORN KLASON" id="acc_horn_klason">
                                        <label class="form-check-label" for="acc_horn_klason">Horn Klason</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="BIO METRIC" id="acc_bio_metric">
                                        <label class="form-check-label" for="acc_bio_metric">Bio Metric</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="ACRYLIC" id="acc_acrylic">
                                        <label class="form-check-label" for="acc_acrylic">Acrylic</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="P3K" id="acc_p3k">
                                        <label class="form-check-label" for="acc_p3k">First Aid Kit</label>
                                    </div>
                                </div>
                                
                                <!-- Row 6 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="SAFETY BELT INTERLOC" id="acc_safety_belt">
                                        <label class="form-check-label" for="acc_safety_belt">Safety Belt Interlock</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="SPARS ARRESTOR" id="acc_spars_arrestor">
                                        <label class="form-check-label" for="acc_spars_arrestor">Spark Arrestor</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitSpecificationBtn">Save Specification</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#quotationsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('marketing/quotations/data') ?>',
            type: 'POST',
            error: function(xhr, error, code) {
                console.error('DataTable AJAX error:', xhr.responseText);
                Swal.fire('Error', 'Failed to load data: ' + xhr.responseText, 'error');
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'quotation_number' },
            { data: 'customer_name' },
            { data: 'description' },
            { 
                data: 'amount',
                render: function(data) {
                    return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    var badges = {
                        'draft': 'bg-secondary',
                        'pending': 'bg-warning',
                        'approved': 'bg-success',
                        'rejected': 'bg-danger',
                        'expired': 'bg-dark'
                    };
                    return '<span class="badge ' + (badges[data] || 'bg-secondary') + '">' + data.toUpperCase() + '</span>';
                }
            },
            { data: 'valid_until' },
            { data: 'created_at' },
            { data: 'actions', orderable: false, searchable: false }
        ],
        order: [[7, 'desc']],
        responsive: true,
        language: {
            processing: "Loading...",
            lengthMenu: "Show _MENU_ entries",
            zeroRecords: "No quotations found",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No entries available",
            infoFiltered: "(filtered from _MAX_ total entries)"
        }
    });

    // Load statistics
    loadStatistics();

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
                    Swal.fire('Success', response.message, 'success');
                    $('#quotationModal').modal('hide');
                    table.ajax.reload();
                    loadStatistics();
                    $('#quotationForm')[0].reset();
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr) {
                console.error('Form submission error:', xhr.responseText);
                Swal.fire('Error', 'Failed to save quotation', 'error');
            }
        });
    });

    // Reset form when modal is hidden
    $('#quotationModal').on('hidden.bs.modal', function() {
        $('#quotationForm')[0].reset();
        $('#quotationId').val('');
        $('#quotationModalLabel').text('Add New Quotation');
    });
});

function loadStatistics() {
    $.get('<?= base_url('marketing/quotations/stats') ?>', function(data) {
        $('#stat-total-quotations').text(data.total || 0);
        $('#stat-pending').text(data.pending || 0);
        $('#stat-approved').text(data.approved || 0);
        $('#stat-rejected').text(data.rejected || 0);
    }).fail(function() {
        console.error('Failed to load statistics');
    });
}

function viewQuotation(id) {
    // Set current quotation ID for specifications
    currentQuotationId = id;
    
    $.get('<?= base_url('marketing/quotations/get-quotation/') ?>' + id, function(response) {
        console.log('Response received:', response); // Debug log
        
        // Handle different response formats
        if (response.status === 'error') {
            Swal.fire('Error', response.message || 'Failed to load quotation details', 'error');
            return;
        }
        
        const data = response;
        
        // Check if data is valid
        if (!data.id_quotation) {
            Swal.fire('Error', 'Invalid quotation data received', 'error');
            return;
        }
        
        // Update modal subtitle
        $('#quotationSubtitle').text((data.quotation_number || 'undefined') + ' - ' + (data.prospect_name || 'No Customer'));
        
        var content = `
            <div class="row">
                <div class="col-md-6">
                    <strong>Quotation Number:</strong><br>
                    ${data.quotation_number || 'undefined'}<br><br>
                    <strong>Customer:</strong><br>
                    ${data.prospect_name || 'undefined'}<br><br>
                    <strong>Amount:</strong><br>
                    Rp ${data.total_amount ? parseFloat(data.total_amount).toLocaleString('id-ID') : 'NaN'}
                </div>
                <div class="col-md-6">
                    <strong>Status:</strong><br>
                    <span class="badge bg-${data.stage === 'ACCEPTED' ? 'success' : data.stage === 'SENT' ? 'warning' : 'danger'}">${(data.stage || 'ERROR').toUpperCase()}</span><br><br>
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
        
        // Reset specifications tab to force reload
        $('#specifications-tab').removeClass('loaded');
        $('#spesifikasiListContract').html('<p class="text-muted">Click the Specifications tab to load data...</p>');
        
        $('#detailModal').modal('show');
    }).fail(function(xhr) {
        console.error('Error loading quotation:', xhr);
        let errorMsg = 'Failed to load quotation details';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMsg = xhr.responseJSON.message;
        } else if (xhr.responseText) {
            try {
                const response = JSON.parse(xhr.responseText);
                errorMsg = response.message || errorMsg;
            } catch (e) {
                errorMsg += ' (Server error)';
            }
        }
        Swal.fire('Error', errorMsg, 'error');
    });
}

function editQuotation(id) {
    $.get('<?= base_url('marketing/quotations/get/') ?>' + id, function(data) {
        $('#quotationId').val(data.id);
        $('#quotationNumber').val(data.quotation_number);
        $('#customerId').val(data.customer_id);
        $('#description').val(data.description);
        $('#amount').val(data.amount);
        $('#validUntil').val(data.valid_until);
        $('#notes').val(data.notes);
        $('#quotationModalLabel').text('Edit Quotation');
        $('#quotationModal').modal('show');
    }).fail(function() {
        Swal.fire('Error', 'Failed to load quotation data', 'error');
    });
}

function deleteQuotation(id) {
    Swal.fire({
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
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Deleted!', response.message, 'success');
                        $('#quotationsTable').DataTable().ajax.reload();
                        loadStatistics();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to delete quotation', 'error');
                }
            });
        }
    });
}

// Global quotation variables
let currentQuotationId = null;

// Event handler for specifications tab click
$(document).on('click', '#specifications-tab', function() {
    console.log('Specifications tab clicked, currentQuotationId:', currentQuotationId);
    if (currentQuotationId && !$(this).hasClass('loaded')) {
        loadQuotationSpecifications(currentQuotationId);
        $(this).addClass('loaded');
    } else if (!currentQuotationId) {
        console.warn('No currentQuotationId set');
    } else {
        console.log('Tab already loaded');
    }
});

// Load quotation specifications
function loadQuotationSpecifications(quotationId) {
    console.log('Loading specifications for quotation:', quotationId);
    const container = document.getElementById('spesifikasiListContract');
    if (!container) {
        console.error('spesifikasiListContract container not found!');
        return;
    }
    
    container.innerHTML = '<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Loading specifications...</div>';
    
    fetch(`<?= base_url('marketing/quotations/get-specifications/') ?>${quotationId}`)
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
    
    let html = '';
    specifications.forEach((spec, index) => {
        const isAttachmentSpec = spec.attachment_tipe && (!spec.tipe_unit_id || spec.tipe_unit_id === '0');
        const cardClass = isAttachmentSpec ? 'border-success' : 'border-primary';
        const badgeClass = isAttachmentSpec ? 'bg-success' : 'bg-primary';
        const specType = isAttachmentSpec ? 'Attachment' : 'Unit';
        
        html += `
            <div class="card mb-3 ${cardClass}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <span class="badge ${badgeClass} me-2">${spec.spek_kode || 'QS-' + (index + 1)}</span>
                        <span class="badge bg-light text-dark me-2">${specType}</span>
                        ${spec.specification_name || 'Specification ' + (index + 1)}
                    </h6>
                    <div>
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="editSpecification(${spec.id_specification})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteSpecification(${spec.id_specification})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <small class="text-muted">Quantity</small>
                            <div class="fw-bold">${spec.quantity || 0}</div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Unit Price</small>
                            <div class="fw-bold text-success">Rp ${formatNumber(spec.unit_price || 0)}</div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Total Price</small>
                            <div class="fw-bold text-primary">Rp ${formatNumber(spec.total_price || 0)}</div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Equipment Type</small>
                            <div>${spec.equipment_type || '-'}</div>
                        </div>
                        ${spec.brand ? `
                        <div class="col-md-4">
                            <small class="text-muted">Brand</small>
                            <div>${spec.brand}</div>
                        </div>
                        ` : ''}
                        ${spec.model ? `
                        <div class="col-md-4">
                            <small class="text-muted">Model</small>
                            <div>${spec.model}</div>
                        </div>
                        ` : ''}
                        ${spec.nama_departemen ? `
                        <div class="col-md-4">
                            <small class="text-muted">Department</small>
                            <div>${spec.nama_departemen}</div>
                        </div>
                        ` : ''}
                        ${spec.nama_tipe_unit ? `
                        <div class="col-md-4">
                            <small class="text-muted">Unit Type</small>
                            <div>${spec.nama_tipe_unit} ${spec.jenis ? `(${spec.jenis})` : ''}</div>
                        </div>
                        ` : ''}
                        ${spec.kapasitas ? `
                        <div class="col-md-4">
                            <small class="text-muted">Capacity</small>
                            <div>${spec.kapasitas}</div>
                        </div>
                        ` : ''}
                        ${spec.merk_charger && spec.tipe_charger ? `
                        <div class="col-md-4">
                            <small class="text-muted">Charger</small>
                            <div>${spec.merk_charger} - ${spec.tipe_charger}</div>
                        </div>
                        ` : ''}
                    </div>
                    ${spec.specification_description ? `
                    <div class="mt-2">
                        <small class="text-muted">Description:</small>
                        <div>${spec.specification_description}</div>
                    </div>
                    ` : ''}
                    ${spec.notes ? `
                    <div class="mt-2">
                        <small class="text-muted">Notes:</small>
                        <div>${spec.notes}</div>
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Open Add Specification Modal
function openAddSpecificationModal() {
    if (!currentQuotationId) {
        Swal.fire('Warning', 'Please select a quotation first', 'warning');
        return;
    }
    
    // Reset form
    $('#addSpecificationForm')[0].reset();
    $('#specQuotationId').val(currentQuotationId);
    
    // Reset modal title and button text
    $('#addSpecificationModal .modal-title').text('Add Specification');
    $('#submitSpecificationBtn').text('Save Specification');
    
    // Load dropdown data
    loadDepartemenForSpecification();
    loadTipeUnitForSpecification(); // This will load data but not populate options until dept is selected
    loadKapasitasForSpecification();
    loadUnitBrandsForSpecification();
    loadAttachmentTypesForSpecification();
    loadValvesForSpecification();
    loadMastsForSpecification();
    loadTiresForSpecification();
    loadWheelsForSpecification();
    
    // Initialize battery and charger as disabled
    $('#specJenisBaterai, #specCharger').prop('disabled', true);
    $('#specJenisBaterai').html('<option value="">-- Select Battery --</option>');
    $('#specCharger').html('<option value="">-- Select Charger --</option>');
    
    $('#addSpecificationModal').modal('show');
}

// Open add attachment modal
function openAddAttachmentModal() {
    if (!currentQuotationId) {
        Swal.fire('Warning', 'Please select a quotation first', 'warning');
        return;
    }
    
    // For now, show info message that this will be implemented
    Swal.fire('Info', 'Add Attachment functionality will be implemented soon', 'info');
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
});

// Functions to load dropdown data - consistent with kontrak spesifikasi pattern
function loadDepartemenForSpecification() {
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=departemen', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Department --</option>';
            response.data.forEach(dept => {
                options += `<option value="${dept.id}">${dept.name}</option>`;
            });
            $('#specDepartemen').html(options);
        }
    }).fail(function(xhr) {
        console.error('Failed to load departments:', xhr.responseText);
        $('#specDepartemen').html('<option value="">Error loading departments</option>');
    });
}

function loadTipeUnitForSpecification() {
    $.ajax({
        url: '<?= base_url('marketing/customer-management/getTipeUnit') ?>',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                // Store all unit type data globally for filtering
                window.allTipeUnitData = response.data;
                
                // Initially show placeholder only
                $('#specTipeUnit').html('<option value="">-- Pilih Tipe Unit --</option>');
            }
        },
        error: function() {
            console.error('Error loading tipe unit');
            $('#specTipeUnit').html('<option value="">Error loading unit types</option>');
        }
    });
}

// Function to update Unit Type options based on selected department
function updateTipeUnitOptions() {
    const selectedDept = $('#specDepartemen').val();
    const select = $('#specTipeUnit');
    
    select.empty().append('<option value="">-- Pilih Tipe Unit --</option>');
    
    if (!selectedDept || !window.allTipeUnitData) {
        return;
    }
    
    // Filter and show only jenis for selected department
    const filteredUnits = window.allTipeUnitData.filter(unit => unit.id_departemen == selectedDept);
    const uniqueJenis = [...new Set(filteredUnits.map(unit => unit.jenis))];
    
    uniqueJenis.sort().forEach(jenis => {
        // Find the first unit with this jenis to get the id
        const unitWithJenis = filteredUnits.find(unit => unit.jenis === jenis);
        select.append(`<option value="${unitWithJenis.id_tipe_unit}" data-dept="${selectedDept}">${jenis}</option>`);
    });
}

function loadKapasitasForSpecification() {
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=kapasitas', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Capacity --</option>';
            response.data.forEach(cap => {
                options += `<option value="${cap.id}">${cap.name}</option>`;
            });
            $('#specKapasitas').html(options);
        }
    }).fail(function(xhr) {
        console.error('Failed to load capacities:', xhr.responseText);
        $('#specKapasitas').html('<option value="">Error loading capacities</option>');
    });
}

function loadChargersForSpecification() {
    const selectedDeptText = $('#specDepartemen option:selected').text().toLowerCase();
    const isElectric = selectedDeptText.includes('electric') || selectedDeptText.includes('listrik');
    
    if (!isElectric) {
        $('#specCharger').html('<option value="">Hanya tersedia untuk unit Electric</option>');
        return;
    }
    
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=charger', function(response) {
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
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=merk_unit', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Brand --</option>';
            response.data.forEach(brand => {
                options += `<option value="${brand.id}">${brand.name}</option>`;
            });
            $('#specMerkUnit').html(options);
        }
    }).fail(function(xhr) {
        console.error('Failed to load unit brands:', xhr.responseText);
        $('#specMerkUnit').html('<option value="">Error loading brands</option>');
    });
}

function loadBatteriesForSpecification() {
    const selectedDeptText = $('#specDepartemen option:selected').text().toLowerCase();
    const isElectric = selectedDeptText.includes('electric') || selectedDeptText.includes('listrik');
    
    if (!isElectric) {
        $('#specJenisBaterai').html('<option value="">Hanya tersedia untuk unit Electric</option>');
        return;
    }
    
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=jenis_baterai', function(response) {
        if (response.success) {
            let options = '<option value="">-- Pilih Baterai --</option>';
            response.data.forEach(battery => {
                options += `<option value="${battery.id}">${battery.name}</option>`;
            });
            $('#specJenisBaterai').html(options);
        }
    }).fail(function(xhr) {
        console.error('Failed to load batteries:', xhr.responseText);
        $('#specJenisBaterai').html('<option value="">Error loading batteries</option>');
    });
}

function loadAttachmentTypesForSpecification() {
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=attachment_tipe', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Attachment Type --</option>';
            response.data.forEach(att => {
                options += `<option value="${att.id}">${att.name}</option>`;
            });
            $('#specAttachmentTipe').html(options);
        }
    }).fail(function(xhr) {
        console.error('Failed to load attachment types:', xhr.responseText);
        $('#specAttachmentTipe').html('<option value="">Error loading attachments</option>');
    });
}

function loadValvesForSpecification() {
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=valve', function(response) {
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

function loadMastsForSpecification() {
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=mast', function(response) {
        if (response.success) {
            let options = '<option value="">-- Select Mast --</option>';
            response.data.forEach(mast => {
                options += `<option value="${mast.id}">${mast.name}</option>`;
            });
            $('#specMast').html(options);
        }
    }).fail(function(xhr) {
        console.error('Failed to load masts:', xhr.responseText);
        $('#specMast').html('<option value="">Error loading masts</option>');
    });
}

function loadTiresForSpecification() {
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=ban', function(response) {
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
    $.get('<?= base_url('marketing/spk/spec-options') ?>?type=roda', function(response) {
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

// Handle specification form submission
$('#addSpecificationForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = $('#submitSpecificationBtn');
    
    submitBtn.prop('disabled', true).text('Saving...');
    
    $.ajax({
        url: '<?= base_url('marketing/quotations/add-specification') ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                $('#addSpecificationModal').modal('hide');
                Swal.fire('Success', response.message, 'success');
                
                // Reload specifications
                if (currentQuotationId) {
                    loadQuotationSpecifications(currentQuotationId);
                }
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error adding specification:', error);
            Swal.fire('Error', 'Failed to add specification', 'error');
        },
        complete: function() {
            submitBtn.prop('disabled', false).text('Save Specification');
        }
    });
});

// Edit specification
function editSpecification(specId) {
    // Implementation for editing specification
    Swal.fire('Info', 'Edit specification functionality will be implemented', 'info');
}

// Delete specification
function deleteSpecification(specId) {
    Swal.fire({
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
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', response.message, 'success');
                        
                        // Reload specifications
                        if (currentQuotationId) {
                            loadQuotationSpecifications(currentQuotationId);
                        }
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to delete specification', 'error');
                }
            });
        }
    });
}

// Helper function for number formatting
function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num || 0);
}
</script>
<?= $this->endSection() ?>