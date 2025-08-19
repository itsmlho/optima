<!-- Enhanced Unit Asset Detail View -->
<?php
// Enhanced status badge configurations with better mapping
$statusClasses = [
    'available' => 'success',
    'rented' => 'primary', 
    'rental' => 'primary',
    'maintenance' => 'warning',
    'retired' => 'secondary',
    'aktif' => 'success',
    'non_aktif' => 'warning',
    'rusak' => 'danger',
    'reserved' => 'info'
];

$assetStatusClasses = [
    'active' => 'success',
    'inactive' => 'warning', 
    'disposed' => 'danger',
    'damaged' => 'danger'
];

// Enhanced: Normalize status values for consistent display
$statusUnit = strtolower($unitAsset['status_unit_name'] ?? $unitAsset['status_unit'] ?? 'unknown');
$statusAsset = strtolower($unitAsset['status_aset'] ?? 'active');

$statusClass = $statusClasses[$statusUnit] ?? 'secondary';
$assetClass = $assetStatusClasses[$statusAsset] ?? 'secondary';

// Enhanced: Get display names with fallbacks
$unitNumber = $unitAsset['tipe_unit_name'] ?? $unitAsset['tipe_unit'] ;
$modelDisplay = $unitAsset['model_unit_display'] ?? $unitAsset['model_unit'] ?? 'Model tidak tersedia';
$statusDisplay = $unitAsset['status_unit_name'] ?? ucfirst($unitAsset['status_unit'] ?? 'Unknown');
$assetDisplay = ucfirst($unitAsset['status_aset'] ?? 'Unknown');
?>

<style>
.info-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid #e0e0e0;
}

.section-title {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #3498db;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e0e0e0;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #555;
    min-width: 120px;
}

.info-value {
    color: #333;
    font-weight: 500;
}

.status-badge { 
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
}

.detail-card:hover {
    transform: translateY(-2px);
}

.card-header-enhanced {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    color: black;
    border-radius: 15px 15px 0 0 !important;
    padding: 1rem 1.5rem;
}

.header-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    color: black;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
}
</style>


<!-- Enhanced Header Section -->
<div class="header-section">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h3 class="mb-2 fw-bold">
                <i class="fas fa-truck me-3"></i><?= esc($unitNumber) ?>
            </h3>
            <p class="mb-0 opacity-75 fs-5"><?= esc($modelDisplay) ?></p>
            <p class="mb-0 opacity-50 mt-1">
                <i class="fas fa-calendar me-1"></i>
                Created: <?= date('M d, Y', strtotime($unitAsset['created_at'] ?? 'now')) ?>
            </p>
        </div>
        <div class="col-md-4 text-end">
            <span class="status-badge badge bg-<?= $statusClass ?> fs-6">
                <i class="fas fa-circle me-3"></i><?= esc($statusDisplay) ?>
            </span>
        </div>
    </div>
</div>

<!-- Enhanced Main Content -->
<div class="row g-4">
    <!-- Unit Information Section -->
    <div class="col-lg-6">
        <div class="info-section">
            <h5 class="section-title">
                <i class="fas fa-truck text-primary me-2"></i>
                Unit Information
            </h5>
            <div class="info-content">
                <div class="info-item">
                    <span class="info-label">Unit Number:</span>
                    <span class="info-value fw-bold text-primary"><?= esc($unitAsset['no_unit'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Serial Number:</span>
                    <span class="info-value"><?= esc($unitAsset['serial_number'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Model Unit:</span>
                    <span class="info-value"><?= esc($unitAsset['model_unit_display'] ?? $unitAsset['model_unit'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Unit Type:</span>
                    <span class="info-value"><?= esc($unitAsset['tipe_unit_name'] ?? $unitAsset['tipe_unit'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Year:</span>
                    <span class="info-value"><?= esc($unitAsset['tahun_unit'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Department:</span>
                    <span class="info-value"><?= esc($unitAsset['departemen_name'] ?? $unitAsset['departemen'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Location:</span>
                    <span class="info-value">
                        <i class="fas fa-map-marker-alt text-danger me-1"></i>
                        <?= esc($unitAsset['lokasi_unit'] ?? '-') ?>
                    </span>
                </div>
                <?php if (!empty($unitAsset['tanggal_kirim'])): ?>
                <div class="info-item">
                    <span class="info-label">Delivery Date:</span>
                    <span class="info-value"><?= date('M d, Y', strtotime($unitAsset['tanggal_kirim'])) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Specifications Section -->
    <div class="col-lg-6">
        <div class="info-section">
            <h5 class="section-title">
                <i class="fas fa-cogs text-info me-2"></i>
                Specifications
            </h5>
            <div class="info-content">
                <div class="info-item">
                    <span class="info-label">Capacity:</span>
                    <span class="info-value fw-bold"><?= esc($unitAsset['kapasitas_unit_display'] ?? $unitAsset['kapasitas_unit'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Mast Type:</span>
                    <span class="info-value"><?= esc($unitAsset['model_mast_display'] ?? $unitAsset['model_mast'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Mast SN:</span>
                    <span class="info-value"><?= esc($unitAsset['sn_mast'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Engine Model:</span>
                    <span class="info-value"><?= esc($unitAsset['model_mesin'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Engine SN:</span>
                    <span class="info-value"><?= esc($unitAsset['sn_mesin'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Attachment:</span>
                    <span class="info-value"><?= esc($unitAsset['model_attachment_display'] ?? $unitAsset['model_attachment'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Attachment SN:</span>
                    <span class="info-value"><?= esc($unitAsset['sn_attachment'] ?? '-') ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Battery & Power Section -->
    <div class="col-lg-6">
        <div class="info-section">
            <h5 class="section-title">
                <i class="fas fa-battery-full me-2"></i>Battery & Power
            </h5>
            <div class="info-content">
                <div class="info-item">
                    <span class="info-label">Battery Model:</span>
                    <span class="info-value"><?= esc($unitAsset['model_baterai'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Battery Serial Number:</span>
                    <span class="info-value"><?= esc($unitAsset['sn_baterai'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Charger Model:</span>
                    <span class="info-value"><?= esc($unitAsset['model_charger'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Charger Serial Number:</span>
                    <span class="info-value"><?= esc($unitAsset['sn_charger'] ?? '-') ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Wheels & Tires Section -->
    <div class="col-lg-6">
        <div class="info-section">
            <h5 class="section-title">
                <i class="fas fa-circle-notch me-2"></i>Wheels & Tires
            </h5>
            <div class="info-content">
                <div class="info-item">
                    <span class="info-label">Wheel Type:</span>
                    <span class="info-value"><?= esc($unitAsset['roda_display'] ?? $unitAsset['roda_name'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tire Type:</span>
                    <span class="info-value"><?= esc($unitAsset['ban_display'] ?? $unitAsset['ban_name'] ?? '-') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Valve:</span>
                    <span class="info-value"><?= esc($unitAsset['valve_display'] ?? $unitAsset['valve_name'] ?? '-') ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    <?php if (!empty($unitAsset['keterangan'])): ?>
    <div class="col-12">
        <div class="card detail-card">
            <div class="card-header card-header-enhanced">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-sticky-note me-2"></i>Additional Notes
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info border-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <?= nl2br(esc($unitAsset['keterangan'])) ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- System Information -->
    <div class="col-12">
        <div class="info-section">
            <h5 class="section-title">
                <i class="fas fa-info-circle me-2"></i>System Information
            </h5>
            <div class="info-content">
                <div class="info-item">
                    <span class="info-label">Created At:</span>
                        <div><?= date('M d, Y H:i', strtotime($unitAsset['created_at'] ?? 'now')) ?></div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Last Updated:</span>
                        <span class="info-value"><?= date('M d, Y H:i', strtotime($unitAsset['updated_at'] ?? 'now')) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>