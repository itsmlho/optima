<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<style>
    .form-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 25px rgba(0,0,0,0.1);
    }
    
    .section-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }
    
    .section-title {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 0;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .form-floating label {
        font-weight: 500;
        color: #555;
    }
    
    .btn-save {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        color: white;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
    }
    
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
        color: white;
    }
    
    .btn-cancel {
        background: #6c757d;
        border: none;
        color: white;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
    }
    
    .btn-cancel:hover {
        background: #545b62;
        color: white;
    }
    
    /* Fix Select2 stacking issues */
    .select2-container {
        width: 100% !important;
        z-index: 1050;
    }
    
    .select2-container--bootstrap-5 .select2-selection {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        min-height: 58px;
        padding: 1.5rem 0.75rem 0.25rem;
    }
    
    .select2-container--bootstrap-5 .select2-selection__rendered {
        color: #212529;
        padding: 0;
        margin: 0;
        line-height: 1.5;
        font-size: 13;
    }
    
    .select2-dropdown {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        z-index: 1051;
    }
    
    /* Prevent text overflow in options */
    .select2-results__option {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }
    
    /* Improve form validation styling */
    .is-invalid + .select2-container--bootstrap-5 .select2-selection {
        border-color: #dc3545;
    }
    
    .form-floating > .select2-container {
        height: 58px;
    }
    
    .form-floating > .select2-container .select2-selection {
        height: 58px !important;
        border-radius: 0.375rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <a href="<?= base_url('warehouse/unit-assets') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <form id="editUnitAssetForm" method="POST" action="<?= base_url('warehouse/unit-assets/update/' . $unitAsset['no_unit']) ?>">
        <?= csrf_field() ?>
        
        <!-- Validation Errors Display -->
        <div id="validation-errors" class="alert alert-danger" style="display: none;">
            <ul id="error-list"></ul>
        </div>
        
        <!-- Success Message -->
        <div id="success-message" class="alert alert-success" style="display: none;"></div>
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="section-card">
                    <h5 class="section-title">
                        <i class="fas fa-truck text-primary me-2"></i>
                        Basic Information
                    </h5>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="no_unit" name="no_unit" 
                                           value="<?= esc($unitAsset['no_unit'] ?? '') ?>" readonly>
                                    <label for="no_unit">Unit Number *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="serial_number" name="serial_number" 
                                           value="<?= esc($unitAsset['serial_number'] ?? '') ?>" required>
                                    <label for="serial_number">Serial Number *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="tipe_unit" name="tipe_unit" required>
                                        <option value="">Select Unit Type</option>
                                        <?php if (isset($form_options['tipe_unit'])): ?>
                                            <?php foreach ($form_options['tipe_unit'] as $tipe): ?>
                                                <option value="<?= $tipe['id_tipe_unit'] ?>" 
                                                    <?= ($unitAsset['tipe_unit'] == $tipe['id_tipe_unit']) ? 'selected' : '' ?>>
                                                    <?= esc($tipe['nama_tipe_unit']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <label for="tipe_unit">Unit Type *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="model_unit" name="model_unit" required>
                                        <option value="">Select Model Unit</option>
                                        <?php if (isset($form_options['model_unit'])): ?>
                                            <?php foreach ($form_options['model_unit'] as $model): ?>
                                                <option value="<?= $model['id_model_unit'] ?>" 
                                                    <?= ($unitAsset['model_unit'] == $model['id_model_unit']) ? 'selected' : '' ?>>
                                                    <?= esc($model['merk_unit']) ?> - <?= esc($model['model_unit']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <label for="model_unit">Model Unit *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="tahun_unit" name="tahun_unit" 
                                           value="<?= esc($unitAsset['tahun_unit'] ?? '') ?>" min="1990" max="2030">
                                    <label for="tahun_unit">Year</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="status_unit" name="status_unit" required>
                                        <option value="">Select Status</option>
                                            <?php if (isset($form_options['status_unit'])): ?>
                                                <?php foreach ($form_options['status_unit'] as $status): ?>
                                                    <option value="<?= $status['id_status'] ?>" 
                                                        <?= ($unitAsset['status_unit'] == $status['id_status']) ? 'selected' : '' ?>>
                                                        <?= esc($status['status_unit']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    <label for="status_unit">Unit Status *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="departemen" name="departemen" required>
                                        <option value="">Select Department</option>
                                        <?php if (isset($form_options['departemen'])): ?>
                                            <?php foreach ($form_options['departemen'] as $dept): ?>
                                                <option value="<?= $dept['id_departemen'] ?>" 
                                                    <?= ($unitAsset['departemen'] == $dept['id_departemen']) ? 'selected' : '' ?>>
                                                    <?= esc($dept['nama_departemen']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <label for="departemen">Department *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="lokasi_unit" name="lokasi_unit" 
                                           value="<?= esc($unitAsset['lokasi_unit'] ?? '') ?>">
                                    <label for="lokasi_unit">Location</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="tanggal_kirim" name="tanggal_kirim" 
                                           value="<?= esc($unitAsset['tanggal_kirim'] ? date('Y-m-d', strtotime($unitAsset['tanggal_kirim'])) : '') ?>">
                                    <label for="tanggal_kirim">Delivery Date</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="status_aset" name="status_aset">
                                        <option value="1" <?= ($unitAsset['status_aset'] == 1) ? 'selected' : '' ?>>Active</option>
                                        <option value="0" <?= ($unitAsset['status_aset'] == 0) ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                    <label for="status_aset">Asset Status</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Specifications -->
                <div class="section-card">
                    <h5 class="section-title">
                        <i class="fas fa-cogs text-info me-2"></i>
                        Specifications
                    </h5>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="kapasitas_unit" name="kapasitas_unit">
                                        <option value="">Select Capacity</option>
                                        <?php if (isset($form_options['kapasitas'])): ?>
                                            <?php foreach ($form_options['kapasitas'] as $kap): ?>
                                                <option value="<?= $kap['id_kapasitas'] ?>" 
                                                    <?= ($unitAsset['kapasitas_unit'] == $kap['id_kapasitas']) ? 'selected' : '' ?>>
                                                    <?= esc($kap['kapasitas_unit']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <label for="kapasitas_unit">Capacity</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="model_mast" name="model_mast">
                                        <option value="">Select Mast Type</option>
                                        <?php if (isset($form_options['tipe_mast'])): ?>
                                            <?php foreach ($form_options['tipe_mast'] as $mast): ?>
                                                <option value="<?= $mast['id_mast'] ?>" 
                                                    <?= ($unitAsset['model_mast'] == $mast['id_mast']) ? 'selected' : '' ?>>
                                                    <?= esc($mast['tipe_mast']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <label for="model_mast">Mast Type</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="sn_mast" name="sn_mast" 
                                           value="<?= esc($unitAsset['sn_mast'] ?? '') ?>">
                                    <label for="sn_mast">Mast Serial Number</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="model_mesin" name="model_mesin">
                                        <option value="">Select Engine</option>
                                        <?php if (isset($form_options['mesin'])): ?>
                                            <?php foreach ($form_options['mesin'] as $mesin): ?>
                                                <option value="<?= $mesin['id'] ?>" 
                                                    <?= ($unitAsset['model_mesin'] == $mesin['id']) ? 'selected' : '' ?>>
                                                    <?= esc($mesin['merk_mesin']) ?> - <?= esc($mesin['model_mesin']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <label for="model_mesin">Engine Model</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="sn_mesin" name="sn_mesin" 
                                           value="<?= esc($unitAsset['sn_mesin'] ?? '') ?>">
                                    <label for="sn_mesin">Engine Serial Number</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="model_attachment" name="model_attachment">
                                        <option value="">Select Attachment</option>
                                            <?php if (isset($form_options['attachment'])): ?>
                                                <?php foreach ($form_options['attachment'] as $att): ?>
                                                    <option value="<?= $att['id_attachment'] ?>" 
                                                        <?= ($unitAsset['model_attachment'] == $att['id_attachment']) ? 'selected' : '' ?>>
                                                            <?= esc($att['attachment']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                    </select>
                                    <label for="model_attachment">Attachment</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="sn_attachment" name="sn_attachment" 
                                           value="<?= esc($unitAsset['sn_attachment'] ?? '') ?>">
                                    <label for="sn_attachment">Attachment Serial Number</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Battery & Power -->
                <div class="section-card">
                    <h5 class="section-title">
                        <i class="fas fa-battery-full text-success me-2"></i>
                        Battery & Power
                    </h5>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="model_baterai" name="model_baterai">
                                        <option value="">Select Battery</option>
                                        <?php if (isset($form_options['baterai'])): ?>
                                            <?php foreach ($form_options['baterai'] as $bat): ?>
                                                <option value="<?= $bat['id'] ?>" 
                                                    <?= ($unitAsset['model_baterai'] == $bat['id']) ? 'selected' : '' ?>>
                                                    <?= esc($bat['merk_baterai']) ?> - <?= esc($bat['tipe_baterai']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <label for="model_baterai">Battery Model</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="sn_baterai" name="sn_baterai" 
                                           value="<?= esc($unitAsset['sn_baterai'] ?? '') ?>">
                                    <label for="sn_baterai">Battery Serial Number</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="model_charger" name="model_charger">
                                        <option value="">Select Charger</option>
                                        <?php if (isset($form_options['charger'])): ?>
                                            <?php foreach ($form_options['charger'] as $charger): ?>
                                                <option value="<?= $charger['id_charger'] ?>" 
                                                    <?= ($unitAsset['model_charger'] == $charger['id_charger']) ? 'selected' : '' ?>>
                                                    <?= esc($charger['merk_charger']) ?> - <?= esc($charger['tipe_charger']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <label for="model_charger">Charger Model</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="sn_charger" name="sn_charger" 
                                           value="<?= esc($unitAsset['sn_charger'] ?? '') ?>">
                                    <label for="sn_charger">Charger Serial Number</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wheels & Tires -->
                <div class="section-card">
                    <h5 class="section-title">
                        <i class="fas fa-circle-notch text-warning me-2"></i>
                        Wheels & Tires
                    </h5>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="roda" name="roda">
                                        <option value="">Select Wheel Type</option>
                                        <?php if (isset($form_options['jenis_roda'])): ?>
                                            <?php foreach ($form_options['jenis_roda'] as $roda): ?>
                                                <option value="<?= $roda['id_roda'] ?>" 
                                                    <?= ($unitAsset['roda'] == $roda['id_roda']) ? 'selected' : '' ?>>
                                                    <?= esc($roda['tipe_roda']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <label for="roda">Wheel Type</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="ban" name="ban">
                                        <option value="">Select Tire Type</option>
                                        <?php if (isset($form_options['tipe_ban'])): ?>
                                            <?php foreach ($form_options['tipe_ban'] as $ban): ?>
                                                <option value="<?= $ban['id_ban'] ?>" 
                                                    <?= ($unitAsset['ban'] == $ban['id_ban']) ? 'selected' : '' ?>>
                                                    <?= esc($ban['tipe_ban']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <label for="ban">Tire Type</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="valve" name="valve">
                                        <option value="">Select Valve</option>
                                        <?php if (isset($form_options['valve'])): ?>
                                            <?php foreach ($form_options['valve'] as $valve): ?>
                                                <option value="<?= $valve['id_valve'] ?>" 
                                                    <?= ($unitAsset['valve'] == $valve['id_valve']) ? 'selected' : '' ?>>
                                                    <?= esc($valve['jumlah_valve']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <label for="valve">Valve</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Notes -->
                <div class="section-card">
                    <h5 class="section-title">
                        <i class="fas fa-sticky-note text-secondary me-2"></i>
                        Additional Information
                    </h5>
                    <div class="card-body">
                        <div class="form-floating">
                            <textarea class="form-control" id="keterangan" name="keterangan" 
                                      style="height: 100px;"><?= esc($unitAsset['keterangan'] ?? '') ?></textarea>
                            <label for="keterangan">Notes</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="col-lg-4">
                <div class="position-sticky" style="top: 20px;">
                    <div class="card form-card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-save me-2"></i>Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-save">
                                    <i class="fas fa-save me-2"></i>Update Unit Asset
                                </button>
                                <a href="<?= base_url('warehouse/unit-assets') ?>" class="btn btn-cancel">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                            
                            <hr class="my-3">
                            
                            <div class="small text-muted">
                                <div class="mb-2">
                                    <strong>Created:</strong><br>
                                    <?= date('M d, Y H:i', strtotime($unitAsset['created_at'] ?? 'now')) ?>
                                </div>
                                <div>
                                    <strong>Last Updated:</strong><br>
                                    <?= date('M d, Y H:i', strtotime($unitAsset['updated_at'] ?? 'now')) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 for better dropdowns
    try {
        $('.form-select').select2({
            theme: 'bootstrap-5',
            allowClear: true
        });
        
        // Show success notification for initialization
        OptimaPro.showNotification('Form loaded successfully', 'success');
    } catch (error) {
        console.error('Error initializing Select2:', error);
        OptimaPro.showNotification('Error loading form components', 'warning');
    }

    // Form validation and submission
    $('#editUnitAssetForm').on('submit', function(e) {
        e.preventDefault();
        
        // Hide previous error messages
        $('#validation-errors').hide();
        $('#success-message').hide();
        
        // Basic validation for required fields
        const requiredFields = ['serial_number', 'tipe_unit', 'model_unit', 'status_unit', 'departemen'];
        let isValid = true;
        let errors = [];
        
        requiredFields.forEach(function(field) {
            const element = document.getElementById(field);
            if (!element || !element.value.trim()) {
                element.classList.add('is-invalid');
                errors.push(field.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) + ' is required');
                isValid = false;
            } else {
                element.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            // Show validation errors using OptimaPro notification
            OptimaPro.showNotification('Please fill in all required fields: ' + errors.join(', '), 'danger');
            return;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
        submitBtn.disabled = true;

        // Show processing notification
        OptimaPro.showNotification('Updating unit asset...', 'info');
        
        // Submit form via AJAX
        $.ajax({
            url: this.action,
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    OptimaPro.showNotification('Unit asset updated successfully!', 'success');
                    
                    // Redirect after a short delay
                    setTimeout(function() {
                        window.location.href = '<?= base_url("warehouse/unit-assets") ?>';
                    }, 2000);
                } else {
                    // Show validation errors using OptimaPro notification
                    if (response.errors) {
                        let errorMessages = [];
                        $.each(response.errors, function(field, message) {
                            errorMessages.push(message);
                            $('#' + field).addClass('is-invalid');
                        });
                        OptimaPro.showNotification('Validation errors: ' + errorMessages.join(', '), 'danger');
                    } else {
                        OptimaPro.showNotification(response.message || 'Failed to update unit asset.', 'danger');
                    }
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 404) {
                    errorMessage = 'Unit asset not found.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error occurred. Please try again.';
                }
                
                OptimaPro.showNotification(errorMessage, 'danger');
            },
            complete: function() {
                // Restore button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    });
    
    // Clear validation on input change
    $('input, select, textarea').on('change input', function() {
        $(this).removeClass('is-invalid');
        
        // Show notification when field is corrected
        if ($(this).hasClass('was-invalid')) {
            OptimaPro.showNotification('Field corrected', 'info');
            $(this).removeClass('was-invalid');
        }
    });
    
    // Add was-invalid class for tracking
    $('input, select, textarea').on('blur', function() {
        if ($(this).hasClass('is-invalid')) {
            $(this).addClass('was-invalid');
        }
    });
    
    // Auto-hide alerts after 5 seconds - replaced with OptimaPro notifications
    // OptimaPro notifications have their own auto-hide functionality
});
</script>
<?= $this->endSection() ?>
