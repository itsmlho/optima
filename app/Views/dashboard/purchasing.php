<?= $this->extend('layouts/base') ?>


<?= $this->section('content') ?>
<!-- Page Header -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-truck me-2"></i>Unit Assets Management
    </h1>
    <div class="d-sm-flex align-items-center">
        <div class="me-3">
            <small class="text-muted">Comprehensive asset management and monitoring system</small>
        </div>
        <button type="button" class="btn btn-primary btn-sm" onclick="exportUnitAssets()">
            <i class="fas fa-download me-1"></i>Export Data
        </button>
    </div>
</div>

<!-- Statistics Cards -->

<div class="row mt-3 mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-primary-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-truck stat-icon text-primary"></i>
                </div>
                <div>
                    <div class="stat-value"><?= isset($stats['total']) ? $stats['total'] : 0 ?></div>
                    <div class="text-muted">Total Unit Assets</div>
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
                    <div class="stat-value"><?= isset($stats['available']) ? $stats['available'] : 0 ?></div>
                    <div class="text-muted">Available Units</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-info-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-gear stat-icon text-info"></i>
                </div>
                <div>
                    <div class="stat-value"><?= isset($stats['maintenance']) ? $stats['maintenance'] : 0 ?></div>
                    <div class="text-muted">Under Maintenance</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-warning-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-exclamation-triangle stat-icon text-warning"></i>
                </div>
                <div>
                    <div class="stat-value"><?= isset($stats['broken']) ? $stats['broken'] : 0 ?></div>
                    <div class="text-muted">Needs Repair</div>
                </div>
            </div>
        </div>
    </div>
</div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats bg-primary text-white h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <h2 class="fw-bold mb-1"><?= isset($stats['total']) ? $stats['total'] : 0 ?></h2>
                    <h6 class="card-title text-uppercase small mb-0">TOTAL UNIT ASSETS</h6>
                </div>
                <div class="ms-3">
                    <i class="fas fa-truck fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats bg-success text-white h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <h2 class="fw-bold mb-1"><?= isset($stats['available']) ? $stats['available'] : 0 ?></h2>
                    <h6 class="card-title text-uppercase small mb-0">AVAILABLE</h6>
                </div>
                <div class="ms-3">
                    <i class="fas fa-check-circle fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats bg-info text-white h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <h2 class="fw-bold mb-1"><?= isset($stats['rented']) ? $stats['rented'] : 0 ?></h2>
                    <h6 class="card-title text-uppercase small mb-0">IN SERVICE</h6>
                </div>
                <div class="ms-3">
                    <i class="fas fa-handshake fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card card-stats bg-warning text-white h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <h2 class="fw-bold mb-1"><?= isset($stats['maintenance']) ? $stats['maintenance'] : 0 ?></h2>
                    <h6 class="card-title text-uppercase small mb-0">MAINTENANCE</h6>
                </div>
                <div class="ms-3">
                    <i class="fas fa-tools fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Enhanced Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card filter-card">
            <div class="card-header bg-transparent border-0 pb-0">
                <h6 class="mb-0 fw-bold text-primary">
                    <i class="fas fa-filter me-2"></i>Advanced Filters
                </h6>
            </div>
            <div class="card-body pt-3">
                <form id="filterForm" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small text-muted fw-semibold">Status Unit</label>
                        <select class="form-select form-select-sm" id="statusFilter" name="status_unit">
                            <option value="">All Status</option>
                            <option value="available">Available</option>
                            <option value="rented">Rented</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="retired">Retired</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted fw-semibold">Department</label>
                        <select class="form-select form-select-sm" id="departmentFilter" name="departemen">
                            <option value="">All Departments</option>
                            <?php if (isset($departments) && is_array($departments)): ?>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?= esc($dept) ?>"><?= esc($dept) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted fw-semibold">Location</label>
                        <select class="form-select form-select-sm" id="locationFilter" name="lokasi_unit">
                            <option value="">All Locations</option>
                            <?php if (isset($locations) && is_array($locations)): ?>
                                <?php foreach ($locations as $location): ?>
                                    <option value="<?= esc($location) ?>"><?= esc($location) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" class="btn btn-primary btn-sm me-2" onclick="applyFilters()">
                            <i class="fas fa-filter me-1"></i>Apply Filters
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetFilters()">
                            <i class="fas fa-undo me-1"></i>Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Unit Assets Table -->
<div class="row">
    <div class="col-12">
        <div class="card table-card">
            <div class="card-header border-0 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-primary">
                        <i class="fas fa-list me-2"></i>Unit Assets Registry
                    </h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="refreshData()">
                            <i class="fas fa-sync-alt me-1"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="unitAssetsTable" class="table table-striped table-hover w-100">
                        <thead>
                            <tr>
                                <th>Unit No</th>
                                <th>Serial Number</th>
                                <th>Model Unit</th>
                                <th>Department</th>
                                <th>Location</th>
                                <th>Unit Status</th>
                                <th>Asset Status</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="spinner-border spinner-border-sm me-2 text-primary" role="status"></div>
                                    <span class="text-muted">Loading unit assets data...</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Create/Edit Modal -->
<div class="modal fade" id="unitAssetModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalTitle">
                    <i class="fas fa-plus me-2"></i>Add Unit Asset
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="unitAssetForm" novalidate>
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row g-4">
                        <!-- Basic Information Section -->
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle me-2"></i>Basic Information
                            </h6>
                        </div>
                        <div class="col-md-4" id="no_unit_container">
                            <label class="form-label fw-semibold">Unit Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="no_unit" id="no_unit_input" readonly>
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Auto-generated unit number</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Serial Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="serial_number" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Unit Status <span class="text-danger">*</span></label>
                            <select class="form-select" name="status_unit" required>
                                <option value="">Select Status</option>
                                <?php if (isset($form_options['status_unit'])): ?>
                                    <?php foreach ($form_options['status_unit'] as $status): ?>
                                        <option value="<?= $status['id_status'] ?>"><?= esc($status['status_unit']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <!-- Location and Department -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Department <span class="text-danger">*</span></label>
                            <select class="form-select" name="departemen" required>
                                <option value="">Select Department</option>
                                <?php if (isset($form_options['departemen'])): ?>
                                    <?php foreach ($form_options['departemen'] as $dept): ?>
                                        <option value="<?= $dept['id_departemen'] ?>"><?= esc($dept['nama_departemen']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Unit Location <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="lokasi_unit" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Delivery Date</label>
                            <input type="date" class="form-control" name="tanggal_kirim">
                        </div>
                        
                        <!-- Unit Specifications Section -->
                        <div class="col-12 mt-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-cogs me-2"></i>Unit Specifications
                            </h6>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Unit Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="tipe_unit" required>
                                <option value="">Select Type</option>
                                <?php if (isset($form_options['tipe_unit'])): ?>
                                    <?php foreach ($form_options['tipe_unit'] as $tipe): ?>
                                        <option value="<?= $tipe['id_tipe_unit'] ?>"><?= esc($tipe['nama_tipe_unit']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Year <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="tahun_unit" min="1990" max="<?= date('Y') ?>" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Brand <span class="text-danger">*</span></label>
                            <select class="form-select" name="merk_unit" id="merkSelect" required>
                                <option value="">Select Brand</option>
                                <?php if (isset($form_options['merk_unit'])): ?>
                                    <?php foreach ($form_options['merk_unit'] as $merk): ?>
                                        <option value="<?= esc($merk['merk_unit']) ?>"><?= esc($merk['merk_unit']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Model <span class="text-danger">*</span></label>
                            <select class="form-select" name="model_unit" id="modelSelect" required>
                                <option value="">Select Model</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Capacity <span class="text-danger">*</span></label>
                            <select class="form-select" name="kapasitas" required>
                                <option value="">Select Capacity</option>
                                <?php if (isset($form_options['kapasitas'])): ?>
                                    <?php foreach ($form_options['kapasitas'] as $kapasitas): ?>
                                        <option value="<?= $kapasitas['id_kapasitas'] ?>"><?= esc($kapasitas['kapasitas_unit']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="statusAsetSelect" class="form-label fw-semibold">Asset Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="statusAsetSelect" name="status_aset" required>
                                <option value="">Select Asset Status</option>
                                <?php if (!empty($form_options['status_aset'])): ?>
                                    <?php foreach ($form_options['status_aset'] as $status): ?>
                                        <option value="<?= esc($status['status']) ?>"><?= esc($status['status']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback">Asset status is required.</div>
                        </div>
                        
                        <!-- Component Details Section -->
                        <div class="col-12 mt-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-wrench me-2"></i>Component Details
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mast Type</label>
                            <select class="form-select" name="tipe_mast">
                                <option value="">Select Mast Type</option>
                                <?php if (isset($form_options['tipe_mast'])): ?>
                                    <?php foreach ($form_options['tipe_mast'] as $mast): ?>
                                        <option value="<?= $mast['id_mast'] ?>"><?= esc($mast['tipe_mast']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mast Serial Number</label>
                            <input type="text" class="form-control" name="sn_mast">
                        </div>
                        
                        <!-- Additional component fields -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Engine Model</label>
                            <select class="form-select" name="model_mesin">
                                <option value="">Select Engine Model</option>
                                <?php if (isset($form_options['mesin'])): ?>
                                    <?php foreach ($form_options['mesin'] as $mesin): ?>
                                        <option value="<?= $mesin['id'] ?>"><?= esc($mesin['model_mesin']) ?> - <?= esc($mesin['bahan_bakar']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Engine Serial Number</label>
                            <input type="text" class="form-control" name="sn_mesin">
                        </div>
                        
                        <!-- More component fields -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Attachment</label>
                            <select class="form-select" name="attachment">
                                <option value="">Select Attachment</option>
                                <?php if (isset($form_options['attachment'])): ?>
                                    <?php foreach ($form_options['attachment'] as $attach): ?>
                                        <option value="<?= $attach['id_attachment'] ?>"><?= esc($attach['attachment']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Attachment Serial Number</label>
                            <input type="text" class="form-control" name="sn_attachment">
                        </div>
                        
                        <!-- Battery and Charger -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Battery Model</label>
                            <select class="form-select" name="model_baterai">
                                <option value="">Select Battery</option>
                                <?php if (isset($form_options['baterai'])): ?>
                                    <?php foreach ($form_options['baterai'] as $baterai): ?>
                                        <option value="<?= $baterai['id'] ?>"><?= esc($baterai['merk_baterai']) ?> - <?= esc($baterai['tipe_baterai']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Battery Serial Number</label>
                            <input type="text" class="form-control" name="sn_baterai">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Charger Model</label>
                            <select class="form-select" name="model_charger">
                                <option value="">Select Charger</option>
                                <?php if (isset($form_options['charger']) && !empty($form_options['charger'])): ?>
                                    <?php foreach ($form_options['charger'] as $charger): ?>
                                        <option value="<?= isset($charger['id']) ? $charger['id'] : $charger['id_charger'] ?>"><?= esc($charger['merk_charger']) ?><?= isset($charger['tipe_charger']) ? ' - ' . esc($charger['tipe_charger']) : '' ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Charger Serial Number</label>
                            <input type="text" class="form-control" name="sn_charger">
                        </div>
                        
                        <!-- Wheels and Tires -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Wheel Type</label>
                            <select class="form-select" name="jenis_roda">
                                <option value="">Select Wheel Type</option>
                                <?php if (isset($form_options['jenis_roda'])): ?>
                                    <?php foreach ($form_options['jenis_roda'] as $roda): ?>
                                        <option value="<?= $roda['id'] ?>"><?= esc($roda['jenis_roda']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tire Type</label>
                            <select class="form-select" name="tipe_ban">
                                <option value="">Select Tire Type</option>
                                <?php if (isset($form_options['tipe_ban'])): ?>
                                    <?php foreach ($form_options['tipe_ban'] as $ban): ?>
                                        <option value="<?= $ban['id'] ?>"><?= esc($ban['tipe_ban']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Valve</label>
                            <select class="form-select" name="valve">
                                <option value="">Select Valve</option>
                                <?php if (isset($form_options['valve'])): ?>
                                    <?php foreach ($form_options['valve'] as $valve): ?>
                                        <option value="<?= $valve['id'] ?>"><?= esc($valve['valve']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <!-- Notes -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea class="form-control" name="keterangan" rows="3" placeholder="Additional notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i><span id="submitText">Save</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Enhanced View Details Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-eye me-2"></i>Unit Asset Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning me-2" id="editUnitAssetBtn" style="display: none;">
                    <i class="fas fa-edit me-1"></i>Edit
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
// Enhanced Unit Assets Management System
let unitAssetsTable;
let currentEditId = null;

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initializing Enhanced Unit Assets Management...');
    
    // Load data immediately with enhanced error handling
    loadUnitAssetsData();
    
    // Initialize form handlers
    initializeFormHandlers();
});

// Enhanced data loading with improved error handling
function loadUnitAssetsData() {
    console.log('📡 Loading unit assets data...');
    
    // Show enhanced loading indicator
    showLoadingTable();
    
    $.ajax({
        url: '<?= base_url('warehouse/unit-assets/simple-data') ?>',
        type: 'GET',
        dataType: 'json',
        timeout: 30000, // 30 second timeout
        success: function(response) {
            console.log('✅ Data loaded successfully:', response);
            
            if (response.success && response.data) {
                renderEnhancedUnitAssetsTable(response.data);
                showAlert('success', `Successfully loaded ${response.data.length} unit assets`);
            } else {
                showError('Failed to load data: ' + (response.message || 'Unknown error'));
                showFallbackData();
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error loading data:', error);
            showError('Error loading data: ' + error);
            showFallbackData();
        }
    });
}

// Enhanced loading indicator
function showLoadingTable() {
    $('#unitAssetsTable tbody').html(`
        <tr>
            <td colspan="8" class="text-center py-5">
                <div class="d-flex flex-column align-items-center">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <span class="text-muted">Loading unit assets data...</span>
                    <small class="text-muted mt-1">Please wait while we fetch the latest information</small>
                </div>
            </td>
        </tr>
    `);
}

// Enhanced table rendering with improved status display
function renderEnhancedUnitAssetsTable(data) {
    console.log('🔧 Rendering enhanced table with', data.length, 'records');
    
    let tableBody = '';
    
    data.forEach(function(unit) {
        // Enhanced status badge with proper text display
        const statusBadge = getEnhancedStatusBadge(unit.status_unit_name || unit.status_unit || 'Unknown');
        const assetBadge = getEnhancedAssetBadge(unit.status_aset || 'Unknown');
        
        tableBody += `
            <tr class="table-row-hover">
                <td><strong class="text-primary">${unit.no_unit || '-'}</strong></td>
                <td>${unit.serial_number || '-'}</td>
                <td><span class="fw-semibold">${unit.model_unit_display || unit.model_unit || '-'}</span></td>
                <td>${unit.departemen_name || unit.departemen || '-'}</td>
                <td><i class="fas fa-map-marker-alt me-1 text-muted"></i>${unit.lokasi_unit || '-'}</td>
                <td>${statusBadge}</td>
                <td>${assetBadge}</td>
                <td>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-info btn-action" onclick="viewUnitAsset('${unit.no_unit}')" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning btn-action" onclick="editUnitAsset('${unit.no_unit}')" title="Edit Asset">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-action" onclick="deleteUnitAsset('${unit.no_unit}')" title="Delete Asset">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    if (tableBody === '') {
        tableBody = `
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No Unit Assets Found</h6>
                        <p class="text-muted mb-0">No unit assets match your current filters</p>
                    </div>
                </td>
            </tr>
        `;
    }
    
    $('#unitAssetsTable tbody').html(tableBody);
    
    // Initialize enhanced DataTable
    initializeEnhancedDataTable();
}

// Enhanced DataTable initialization
function initializeEnhancedDataTable() {
    console.log('🔧 Initializing enhanced DataTable...');
    
    // Destroy existing table if any
    if (unitAssetsTable) {
        unitAssetsTable.destroy();
    }
    
    // Initialize with enhanced options
    unitAssetsTable = $('#unitAssetsTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        order: [[0, 'asc']],
        language: {
            processing: 'Processing...',
            search: 'Search Assets:',
            lengthMenu: 'Show _MENU_ assets',
            info: 'Showing _START_ to _END_ of _TOTAL_ assets',
            infoEmpty: 'Showing 0 to 0 of 0 assets',
            infoFiltered: '(filtered from _MAX_ total assets)',
            paginate: {
                first: 'First',
                previous: 'Previous',
                next: 'Next',
                last: 'Last'
            },
            emptyTable: 'No unit assets available',
            zeroRecords: 'No matching unit assets found'
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        columnDefs: [
            { 
                targets: [5, 6, 7], // Status columns and actions
                orderable: false 
            },
            {
                targets: [0], // Unit number column
                className: 'fw-bold'
            }
        ],
        initComplete: function() {
            console.log('✅ Enhanced DataTable initialized successfully');
            // Add custom styling after initialization
            $('.dataTables_filter input').addClass('form-control-sm').attr('placeholder', 'Search unit assets...');
            $('.dataTables_length select').addClass('form-select form-select-sm');
        }
    });
}

// Enhanced status badge with descriptive text
function getEnhancedStatusBadge(status) {
    // Convert numeric IDs or status names to standardized format
    let normalizedStatus = '';
    
    if (typeof status === 'string') {
        normalizedStatus = status.toLowerCase();
    } else if (typeof status === 'number') {
        // Map numeric IDs to status names (adjust based on your database)
        const statusMap = {
            1: 'available',
            2: 'rented', 
            3: 'maintenance',
            4: 'retired'
        };
        normalizedStatus = statusMap[status] || 'unknown';
    }
    
    const statusConfig = {
        'available': {
            class: 'bg-success',
            text: 'Available',
            icon: 'fa-check-circle'
        },
        'reserved': {
            class: 'bg-info', 
            text: 'Reserved',
            icon: 'fa-clock'
        },
        'rental': {
            class: 'bg-warning text-dark',
            text: 'Rental', 
            icon: 'fa-handshake'
        },
        'rented': {
            class: 'bg-warning text-dark',
            text: 'Rented',
            icon: 'fa-handshake'
        },
        'maintenance': {
            class: 'bg-danger',
            text: 'Maintenance',
            icon: 'fa-tools'
        },
        'retired': {
            class: 'bg-secondary',
            text: 'Retired',
            icon: 'fa-archive'
        }
    };
    
    const config = statusConfig[normalizedStatus] || statusConfig['available'];
    
    return `<span class="status-badge badge ${config.class}">
                <i class="fas ${config.icon} me-1"></i>${config.text}
            </span>`;
}

// Enhanced asset badge
function getEnhancedAssetBadge(status) {
    const assetConfig = {
        'active': {
            class: 'bg-success',
            text: 'Active',
            icon: 'fa-check'
        },
        'inactive': {
            class: 'bg-warning text-dark', 
            text: 'Inactive',
            icon: 'fa-pause'
        },
        'damaged': {
            class: 'bg-danger',
            text: 'Damaged',
            icon: 'fa-exclamation-triangle'
        },
        'disposed': {
            class: 'bg-secondary',
            text: 'Disposed',
            icon: 'fa-trash'
        }
    };
    
    const normalizedStatus = status.toLowerCase();
    const config = assetConfig[normalizedStatus] || assetConfig['active'];
    
    return `<span class="asset-badge badge ${config.class}">
                <i class="fas ${config.icon} me-1"></i>${config.text}
            </span>`;
}

// Enhanced fallback data with better styling
function showFallbackData() {
    console.log('🔧 Showing enhanced fallback demo data...');
    
    const fallbackData = [
        {
            no_unit: 'DEMO-001',
            serial_number: 'SN-DEMO-001',
            model_unit_display: 'Toyota 8FBE15 (Demo)',
            departemen_name: 'Operations',
            lokasi_unit: 'Warehouse A',
            status_unit_name: 'Available',
            status_aset: 'Active'
        },
        {
            no_unit: 'DEMO-002', 
            serial_number: 'SN-DEMO-002',
            model_unit_display: 'Mitsubishi FG25N (Demo)',
            departemen_name: 'Logistics',
            lokasi_unit: 'Warehouse B',
            status_unit_name: 'Rented',
            status_aset: 'Active'
        },
        {
            no_unit: 'DEMO-003',
            serial_number: 'SN-DEMO-003', 
            model_unit_display: 'Komatsu FD30-17 (Demo)',
            departemen_name: 'Maintenance',
            lokasi_unit: 'Service Bay',
            status_unit_name: 'Maintenance',
            status_aset: 'Active'
        }
    ];
    
    renderEnhancedUnitAssetsTable(fallbackData);
    showAlert('warning', 'Using demo data - database connection failed. Please check your connection.');
}

// Enhanced filter functions
function applyFilters() {
    console.log('🔍 Applying enhanced filters...');
    
    const statusFilter = $('#statusFilter').val();
    const departmentFilter = $('#departmentFilter').val();
    const locationFilter = $('#locationFilter').val();
    
    if (unitAssetsTable) {
        // Clear existing search
        unitAssetsTable.search('').draw();
        
        // Apply column-specific filters if needed
        // This would require server-side filtering for best performance
        let searchTerms = [];
        if (statusFilter) searchTerms.push(statusFilter);
        if (departmentFilter) searchTerms.push(departmentFilter);
        if (locationFilter) searchTerms.push(locationFilter);
        
        if (searchTerms.length > 0) {
            unitAssetsTable.search(searchTerms.join(' ')).draw();
        }
    }
    
    showAlert('info', `Filters applied - ${statusFilter || 'All'} status, ${departmentFilter || 'All'} departments, ${locationFilter || 'All'} locations`);
}

function resetFilters() {
    console.log('🔄 Resetting enhanced filters...');
    
    $('#filterForm')[0].reset();
    
    if (unitAssetsTable) {
        unitAssetsTable.search('').draw();
    }
    
    showAlert('info', 'All filters have been reset');
}

// Enhanced refresh function
function refreshData() {
    console.log('🔄 Refreshing unit assets data...');
    showAlert('info', 'Refreshing data...');
    loadUnitAssetsData();
}

// Enhanced CRUD Functions
function showCreateModal() {
    console.log('➕ Opening create modal...');
    currentEditId = null;
    $('#modalTitle').html('<i class="fas fa-plus me-2"></i>Add Unit Asset');
    $('#submitText').text('Save');
    resetEnhancedForm();
    
    // Hide no_unit field for create mode
    $('#no_unit_container').hide();
    $('#no_unit_input').prop('readonly', true).val('');
    
    $('#unitAssetModal').modal('show');
}

function viewUnitAsset(id) {
    console.log('👁️ Viewing unit asset:', id);
    
    // Show loading in modal
    $('#viewContent').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Loading asset details...</p>
        </div>
    `);
    $('#viewModal').modal('show');
    
    $.ajax({
        url: '<?= base_url('warehouse/unit-assets/show') ?>/' + id,
        type: 'GET',
        headers: {
            'Accept': 'text/html'
        },
        success: function(response) {
            $('#viewContent').html(response);
            $('#editUnitAssetBtn').show().off('click').on('click', function() {
                $('#viewModal').modal('hide');
                editUnitAsset(id);
            });
        },
        error: function(xhr, status, error) {
            console.error('Error loading unit details:', error);
            $('#viewContent').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Failed to load unit asset details: ${error}
                </div>
            `);
            showAlert('error', 'Failed to load unit asset details');
        }
    });
}

function editUnitAsset(id) {
    console.log('✏️ Editing unit asset:', id);
    
    currentEditId = id;
    $('#modalTitle').html('<i class="fas fa-edit me-2"></i>Edit Unit Asset');
    $('#submitText').text('Update');
    resetEnhancedForm();
    
    // Show no_unit field for edit mode (readonly)
    $('#no_unit_container').show();
    $('#no_unit_input').prop('readonly', true);
    
    // Load unit data
    $.ajax({
        url: '<?= base_url('warehouse/unit-assets/show') ?>/' + id,
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response && typeof response === 'object') {
                populateEnhancedForm(response);
                $('#unitAssetModal').modal('show');
            } else {
                showAlert('error', 'Invalid response from server');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading unit for edit:', error);
            showAlert('error', 'Failed to load unit asset for editing');
        }
    });
}

function deleteUnitAsset(id) {
    console.log('🗑️ Deleting unit asset:', id);
    
    // Enhanced confirmation dialog
    if (confirm(`Are you sure you want to delete unit asset "${id}"?\n\nThis action cannot be undone and will permanently remove all associated data.`)) {
        
        // Show loading alert
        showAlert('info', 'Deleting unit asset...');
        
        $.ajax({
            url: '<?= base_url('warehouse/unit-assets/delete') ?>/' + id,
            type: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message || 'Unit asset deleted successfully');
                    refreshData(); // Reload data
                } else {
                    showAlert('error', response.message || 'Failed to delete unit asset');
                }
            },
            error: function(xhr, status, error) {
                console.error('Delete error:', error);
                showAlert('error', 'Failed to delete unit asset: ' + error);
            }
        });
    }
}

// Enhanced form management
function populateEnhancedForm(data) {
    console.log('📝 Populating enhanced form with data:', data);
    
    const form = document.getElementById('unitAssetForm');
    
    // Show no_unit field for edit mode and make it readonly
    $('#no_unit_container').show();
    $('#no_unit_input').prop('readonly', true);
    
    // Set values for all fields except merk/model, which need special handling
    Object.keys(data).forEach(key => {
        if (key !== 'merk_unit' && key !== 'model_unit') {
            const field = form.querySelector(`[name="${key}"]`);
            if (field) {
                field.value = data[key] || '';
            }
        }
    });

    // Handle merk and model dependency with enhanced error handling
    const merkSelect = $('#merkSelect');
    const modelSelect = $('#modelSelect');
    const merkId = data.merk_unit;
    const modelId = data.model_unit;

    // Set the merk
    merkSelect.val(merkId);

    // Load models for the selected merk
    modelSelect.html('<option value="">Loading models...</option>');
    if (merkId) {
        $.ajax({
            url: `<?= base_url('api/models-by-merk') ?>/${encodeURIComponent(merkId)}`,
            type: 'GET',
            success: function(response) {
                modelSelect.html('<option value="">Select Model</option>');
                if (response.success && response.data) {
                    response.data.forEach(function(model) {
                        modelSelect.append(
                            `<option value="${model.id_model_unit}">${model.model_unit}</option>`
                        );
                    });
                    // Set the correct model
                    modelSelect.val(modelId);
                } else {
                    console.warn('No models found for merk:', merkId);
                }
            },
            error: function() {
                console.error('Error loading models for merk:', merkId);
                modelSelect.html('<option value="">Error loading models</option>');
            }
        });
    } else {
        modelSelect.html('<option value="">Select Model</option>');
    }
}

function resetEnhancedForm() {
    const form = document.getElementById('unitAssetForm');
    form.reset();
    form.classList.remove('was-validated');
    
    // Reset no_unit field state
    $('#no_unit_container').show();
    $('#no_unit_input').prop('readonly', true);
    
    // Clear validation errors
    clearEnhancedValidationErrors();
    
    // Reset model dropdown
    $('#modelSelect').html('<option value="">Select Model</option>');
}

function clearEnhancedValidationErrors() {
    // Remove all invalid classes
    document.querySelectorAll('.is-invalid').forEach(element => {
        element.classList.remove('is-invalid');
    });
    
    // Clear all feedback messages
    document.querySelectorAll('.invalid-feedback').forEach(feedback => {
        feedback.textContent = '';
    });
}

// Enhanced alert system
function showAlert(type, message) {
    const alertIcons = {
        'success': 'fa-check-circle',
        'warning': 'fa-exclamation-triangle', 
        'info': 'fa-info-circle',
        'error': 'fa-exclamation-circle'
    };
    
    const alertClass = type === 'success' ? 'alert-success' : 
                     type === 'warning' ? 'alert-warning' :
                     type === 'info' ? 'alert-info' : 'alert-danger';
    
    const icon = alertIcons[type] || alertIcons['info'];
    
    const alert = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas ${icon} me-2"></i>
            <strong>${type.charAt(0).toUpperCase() + type.slice(1)}:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Remove existing alerts
    $('.alert').remove();
    
    // Add new alert to the page header area
    $('.page-header').after(alert);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        $('.alert').fadeOut(() => {
            $('.alert').remove();
        });
    }, 5000);
}

function showError(message) {
    showAlert('error', message);
}

// Enhanced form handlers
function initializeFormHandlers() {
    console.log('🔧 Initializing enhanced form handlers...');
    
    // Enhanced modal reset on hide
    $('#unitAssetModal').on('hidden.bs.modal', function() {
        resetEnhancedForm();
        currentEditId = null;
    });
    
    $('#viewModal').on('hidden.bs.modal', function() {
        $('#editUnitAssetBtn').hide();
        $('#viewContent').empty();
    });
    
    // Enhanced form submission
    $('#unitAssetForm').on('submit', function(e) {
        e.preventDefault();
        handleEnhancedFormSubmission();
    });
    
    // Enhanced merk/model dependency
    $('#merkSelect').on('change', function() {
        const selectedMerk = $(this).val();
        const modelSelect = $('#modelSelect');
        
        modelSelect.html('<option value="">Select Model</option>');
        
        if (selectedMerk) {
            modelSelect.html('<option value="">Loading models...</option>');
            
            $.ajax({
                url: `<?= base_url('api/models-by-merk') ?>/${encodeURIComponent(selectedMerk)}`,
                type: 'GET',
                success: function(response) {
                    modelSelect.html('<option value="">Select Model</option>');
                    if (response.success && response.data) {
                        response.data.forEach(function(model) {
                            modelSelect.append(
                                `<option value="${model.id_model_unit}">${model.model_unit}</option>`
                            );
                        });
                    }
                },
                error: function() {
                    modelSelect.html('<option value="">Error loading models</option>');
                }
            });
        }
    });
}

function handleEnhancedFormSubmission() {
    console.log('📤 Handling enhanced form submission...');
    
    const form = document.getElementById('unitAssetForm');
    const formData = new FormData(form);
    const isEdit = currentEditId !== null;
    
    // Show loading state
    $('#submitText').text(isEdit ? 'Updating...' : 'Saving...');
    $('.btn[type="submit"]').prop('disabled', true);
    
    const url = isEdit ? 
        `<?= base_url('warehouse/unit-assets/update') ?>/${currentEditId}` : 
        `<?= base_url('warehouse/unit-assets/store') ?>`;
    
    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message || `Unit asset ${isEdit ? 'updated' : 'created'} successfully`);
                $('#unitAssetModal').modal('hide');
                refreshData();
            } else {
                if (response.errors) {
                    displayEnhancedValidationErrors(response.errors);
                } else {
                    showAlert('error', response.message || 'Failed to save unit asset');
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Form submission error:', error);
            showAlert('error', 'Failed to save unit asset: ' + error);
        },
        complete: function() {
            // Reset button state
            $('#submitText').text(isEdit ? 'Update' : 'Save');
            $('.btn[type="submit"]').prop('disabled', false);
        }
    });
}

function displayEnhancedValidationErrors(errors) {
    clearEnhancedValidationErrors();
    
    Object.keys(errors).forEach(field => {
        const input = document.querySelector(`[name="${field}"]`);
        if (input) {
            input.classList.add('is-invalid');
            
            let feedback = input.nextElementSibling;
            if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                feedback = document.createElement('div');
                feedback.classList.add('invalid-feedback');
                input.parentNode.insertBefore(feedback, input.nextSibling);
            }
            
            feedback.textContent = errors[field];
        }
    });
    
    // Focus on first error field
    const firstError = document.querySelector('.is-invalid');
    if (firstError) {
        firstError.focus();
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

// Export functionality
function exportUnitAssets() {
    console.log('📊 Exporting unit assets...');
    showAlert('info', 'Export functionality will be implemented in the next update');
}

// Page specific initialization  
console.log('✅ Enhanced Unit Assets Management System loaded successfully');
</script>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
// Enhanced page initialization complete
console.log('Enhanced Unit Assets Management ready');
<?= $this->endSection() ?> 
