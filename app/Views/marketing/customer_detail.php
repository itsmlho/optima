<?= $this->extend('layouts/base') ?>

<?php
/**
 * Customer Detail Module - Marketing
 * BADGE SYSTEM: Optima badge-soft-* (optima-pro.css). Active → badge-soft-green, Inactive → badge-soft-gray.
 */
$customer = $customer ?? [];
$locations = $locations ?? [];
$id = $customer['id'] ?? 0;
$customerName = $customer['customer_name'] ?? 'Customer #' . $id;
?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('marketing/kontrak') ?>"><i class="fas fa-users me-1"></i>Customers</a></li>
                <li class="breadcrumb-item active"><?= esc($customerName) ?></li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-building me-2 text-primary"></i>Customer Detail
        </h4>
        <p class="text-muted small mb-0"><?= esc($customer['customer_code'] ?? '') ?></p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= base_url('marketing/kontrak') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
        <button class="btn btn-primary btn-sm" onclick="editCustomer()">
            <i class="fas fa-edit me-1"></i>Edit Customer
        </button>
        <button class="btn btn-success btn-sm" onclick="createContract()">
            <i class="fas fa-file-contract me-1"></i>Buat Kontrak
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- Main Content -->
    <div class="col-lg-9">

        <!-- Customer Info Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0 fw-semibold"><i class="fas fa-building me-2 text-primary"></i><?= esc($customerName) ?></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Kode</dt>
                            <dd class="col-sm-8"><?= esc($customer['customer_code'] ?? '-') ?></dd>

                            <dt class="col-sm-4">Nama</dt>
                            <dd class="col-sm-8"><?= esc($customer['customer_name'] ?? '-') ?></dd>

                            <dt class="col-sm-4">Marketing</dt>
                            <dd class="col-sm-8"><?= esc($customer['marketing_name'] ?? '-') ?></dd>

                            <dt class="col-sm-4">Status</dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-<?= ($customer['is_active'] ?? 1) ? 'success' : 'secondary' ?>">
                                    <?= ($customer['is_active'] ?? 1) ? 'Active' : 'Inactive' ?>
                                </span>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Billing Method</dt>
                            <dd class="col-sm-8"><?= esc($customer['default_billing_method'] ?? '-') ?></dd>

                            <dt class="col-sm-4">Created</dt>
                            <dd class="col-sm-8"><?= !empty($customer['created_at']) ? date('d M Y', strtotime($customer['created_at'])) : '-' ?></dd>

                            <dt class="col-sm-4">Updated</dt>
                            <dd class="col-sm-8"><?= !empty($customer['updated_at']) ? date('d M Y', strtotime($customer['updated_at'])) : '-' ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Locations -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Lokasi (<?= count($locations) ?>)</h5>
                <button class="btn btn-sm btn-outline-primary" onclick="addLocation()">
                    <i class="fas fa-plus me-1"></i>Tambah Lokasi
                </button>
            </div>
            <div class="card-body p-0">
                <?php if (empty($locations)): ?>
                    <div class="alert alert-info m-3">
                        <i class="fas fa-info-circle me-2"></i>Belum ada lokasi. Klik "Tambah Lokasi" untuk menambahkan.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Lokasi</th>
                                    <th>Alamat</th>
                                    <th>Kota</th>
                                    <th>Contact Person</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($locations as $loc): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($loc['location_name'] ?? '-') ?></strong>
                                        <?php if (!empty($loc['is_primary'])): ?>
                                            <span class="badge bg-primary ms-1">Primary</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($loc['address'] ?? '-') ?></td>
                                    <td><?= esc($loc['city'] ?? '-') ?></td>
                                    <td><?= esc($loc['contact_person'] ?? '-') ?></td>
                                    <td><?= esc($loc['phone'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge bg-<?= ($loc['is_active'] ?? 1) ? 'success' : 'secondary' ?>">
                                            <?= ($loc['is_active'] ?? 1) ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Contracts List -->
        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-file-contract me-2"></i>Daftar Kontrak</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="contractsTable">
                        <thead class="bg-light">
                            <tr>
                                <th>No Kontrak</th>
                                <th>Lokasi</th>
                                <th>Periode</th>
                                <th>Unit</th>
                                <th>Nilai</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-spinner fa-spin me-2"></i>Memuat kontrak...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Sidebar -->
    <div class="col-lg-3">
        <!-- Quick Stats -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistik</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="display-6 text-primary" id="statContracts">-</div>
                    <div class="text-muted small">Total Kontrak</div>
                </div>
                <div class="text-center mb-3">
                    <div class="display-6 text-success" id="statUnits">-</div>
                    <div class="text-muted small">Total Unit</div>
                </div>
                <div class="text-center">
                    <div class="display-6 text-info" id="statLocations"><?= count($locations) ?></div>
                    <div class="text-muted small">Lokasi</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Aksi Cepat</h6>
            </div>
            <div class="card-body p-2">
                <button class="btn btn-outline-primary btn-sm w-100 mb-2" onclick="createContract()">
                    <i class="fas fa-plus me-1"></i>Buat Kontrak Baru
                </button>
                <button class="btn btn-outline-secondary btn-sm w-100 mb-2" onclick="addLocation()">
                    <i class="fas fa-map-marker-alt me-1"></i>Tambah Lokasi
                </button>
                <button class="btn btn-outline-info btn-sm w-100" onclick="viewAllContracts()">
                    <i class="fas fa-list me-1"></i>Lihat Semua Kontrak
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
const CUSTOMER_ID = <?= $id ?>;
const BASE_URL = '<?= base_url() ?>';

// Load contracts on page load
$(document).ready(function() {
    loadCustomerContracts();
});

function loadCustomerContracts() {
    $.ajax({
        url: BASE_URL + 'marketing/customers/getContracts/' + CUSTOMER_ID,
        type: 'GET',
        success: function(res) {
            if (!res.success || !res.data || !res.data.length) {
                $('#contractsTable tbody').html('<tr><td colspan="7" class="text-center text-muted py-4">Tidak ada kontrak</td></tr>');
                return;
            }

            const contracts = res.data;
            let totalUnits = 0;
            let html = '';

            contracts.forEach(c => {
                const startDate = c.tanggal_mulai ? new Date(c.tanggal_mulai).toLocaleDateString('id-ID') : '-';
                const endDate = c.tanggal_berakhir ? new Date(c.tanggal_berakhir).toLocaleDateString('id-ID') : '-';
                const statusClass = c.status === 'ACTIVE' ? 'success' : c.status === 'EXPIRED' ? 'danger' : 'warning';

                // Get unit count for this contract
                const unitCount = c.total_units || 0;
                totalUnits += unitCount;

                html += '<tr>';
                html += '<td><strong>' + (c.no_kontrak || '-') + '</strong></td>';
                html += '<td>' + (c.location_name || '-') + '</td>';
                html += '<td>' + startDate + ' s/d ' + endDate + '</td>';
                html += '<td>' + unitCount + ' unit</td>';
                html += '<td>' + rupiah(c.nilai_total || 0) + '</td>';
                html += '<td><span class="badge bg-' + statusClass + '">' + (c.status || '-') + '</span></td>';
                html += '<td><a href="' + BASE_URL + 'marketing/kontrak/detail/' + c.id + '" class="btn btn-xs btn-outline-primary">Detail</a></td>';
                html += '</tr>';
            });

            if (contracts.length === 0) {
                html = '<tr><td colspan="7" class="text-center text-muted py-4">Tidak ada kontrak</td></tr>';
            }

            $('#contractsTable tbody').html(html);
            $('#statContracts').text(contracts.length);
            $('#statUnits').text(totalUnits);
        },
        error: function() {
            $('#contractsTable tbody').html('<tr><td colspan="7" class="text-center text-danger py-4">Error memuat kontrak</td></tr>');
        }
    });
}

function rupiah(value) {
    return 'Rp ' + parseFloat(value || 0).toLocaleString('id-ID');
}

function editCustomer() {
    alert('Edit customer feature coming soon');
}

function createContract() {
    // Redirect to create contract with customer pre-selected
    window.location.href = BASE_URL + 'marketing/kontrak/create?customer_id=' + CUSTOMER_ID;
}

function addLocation() {
    alert('Add location feature coming soon');
}

function viewAllContracts() {
    window.location.href = BASE_URL + 'marketing/kontrak?customer_id=' + CUSTOMER_ID;
}
</script>
<?= $this->endSection() ?>
