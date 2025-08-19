<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-truck me-2"></i>Unit Marketing
        </h1>
        <div class="d-sm-flex align-items-center">
            <div class="btn-group" role="group">
                <button class="btn btn-outline-info btn-sm" onclick="refreshData()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                    <i class="fas fa-download me-1"></i>Export
                </button>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                    <i class="fas fa-plus me-1"></i>Tambah Unit
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Unit Marketing</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">89</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="text-xs text-success mt-2">
                        <i class="fas fa-arrow-up me-1"></i>+5 unit baru
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Unit Tersedia</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">72</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="text-xs text-success mt-2">
                        <i class="fas fa-arrow-up me-1"></i>81% ketersediaan
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Unit Disewa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">17</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="text-xs text-warning mt-2">
                        <i class="fas fa-arrow-up me-1"></i>19% utilisasi
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Pendapatan Bulanan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp 125K</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="text-xs text-success mt-2">
                        <i class="fas fa-arrow-up me-1"></i>+22% dari bulan lalu
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Unit Availability Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Utilisasi Unit Bulanan</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="#">6 bulan terakhir</a>
                            <a class="dropdown-item" href="#">1 tahun terakhir</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Export data</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="unitUtilizationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Unit Category Distribution -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Distribusi Kategori Unit</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="unitCategoryChart"></canvas>
                    </div>
                    
                    <!-- Category Legend -->
                    <div class="mt-4 text-center small">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle me-2" style="width: 12px; height: 12px; background: #6366f1;"></div>
                                <span class="text-muted">Excavator</span>
                            </div>
                            <strong>35 unit</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle me-2" style="width: 12px; height: 12px; background: #10b981;"></div>
                                <span class="text-muted">Dump Truck</span>
                            </div>
                            <strong>28 unit</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle me-2" style="width: 12px; height: 12px; background: #f59e0b;"></div>
                                <span class="text-muted">Crane</span>
                            </div>
                            <strong>15 unit</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle me-2" style="width: 12px; height: 12px; background: #ef4444;"></div>
                                <span class="text-muted">Forklift</span>
                            </div>
                            <strong>11 unit</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Unit Management Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Unit Marketing</h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#filterSection">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <button class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-search me-1"></i>Cari
                    </button>
                </div>
            </div>
            
            <!-- Filter Section -->
            <div class="collapse" id="filterSection">
                <div class="border-top pt-3 mt-3">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small">Kategori</label>
                            <select class="form-select form-select-sm">
                                <option value="">Semua Kategori</option>
                                <option value="excavator">Excavator</option>
                                <option value="dump_truck">Dump Truck</option>
                                <option value="crane">Crane</option>
                                <option value="forklift">Forklift</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Status</label>
                            <select class="form-select form-select-sm">
                                <option value="">Semua Status</option>
                                <option value="available">Tersedia</option>
                                <option value="rented">Disewa</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Lokasi</label>
                            <select class="form-select form-select-sm">
                                <option value="">Semua Lokasi</option>
                                <option value="jakarta">Jakarta</option>
                                <option value="surabaya">Surabaya</option>
                                <option value="bandung">Bandung</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-secondary btn-sm w-100">
                                <i class="fas fa-sync me-1"></i>Reset Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="unitMarketingTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Kode Unit</th>
                            <th>Kategori</th>
                            <th>Brand/Model</th>
                            <th>Kapasitas</th>
                            <th>Tarif Rental</th>
                            <th>Status</th>
                            <th>Lokasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($units) && is_array($units)): ?>
                            <?php foreach ($units as $unit): ?>
                                <tr>
                                    <td><strong><?= esc($unit['unit_code']) ?></strong></td>
                                    <td>
                                        <span class="badge bg-info"><?= esc($unit['capacity']) ?></span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= esc($unit['brand']) ?></strong><br>
                                            <small class="text-muted"><?= esc($unit['model']) ?></small>
                                        </div>
                                    </td>
                                    <td><?= esc($unit['capacity']) ?></td>
                                    <td>
                                        <div>
                                            <strong>Rp <?= number_format($unit['daily_rate'], 0, ',', '.') ?></strong><br>
                                            <small class="text-muted">per hari</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $unit['status'] == 'Available' ? 'success' : ($unit['status'] == 'Rented' ? 'warning' : 'secondary') ?>">
                                            <?= esc($unit['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= esc($unit['location']) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-info" onclick="viewUnit('<?= $unit['unit_code'] ?>')" data-bs-toggle="tooltip" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-primary" onclick="editUnit('<?= $unit['unit_code'] ?>')" data-bs-toggle="tooltip" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if ($unit['status'] == 'Available'): ?>
                                                <button class="btn btn-outline-success" onclick="createQuotation('<?= $unit['unit_code'] ?>')" data-bs-toggle="tooltip" title="Buat Penawaran">
                                                    <i class="fas fa-file-invoice"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn btn-outline-danger" onclick="deleteUnit('<?= $unit['unit_code'] ?>')" data-bs-toggle="tooltip" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data unit</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Unit Modal -->
<div class="modal fade" id="addUnitModal" tabindex="-1" aria-labelledby="addUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUnitModalLabel">Tambah Unit Marketing</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUnitForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unitCode" class="form-label">Kode Unit</label>
                                <input type="text" class="form-control" id="unitCode" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unitCategory" class="form-label">Kategori</label>
                                <select class="form-select" id="unitCategory" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="excavator">Excavator</option>
                                    <option value="dump_truck">Dump Truck</option>
                                    <option value="crane">Crane</option>
                                    <option value="forklift">Forklift</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unitBrand" class="form-label">Brand</label>
                                <input type="text" class="form-control" id="unitBrand" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unitModel" class="form-label">Model</label>
                                <input type="text" class="form-control" id="unitModel" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unitCapacity" class="form-label">Kapasitas</label>
                                <input type="text" class="form-control" id="unitCapacity" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unitLocation" class="form-label">Lokasi</label>
                                <select class="form-select" id="unitLocation" required>
                                    <option value="">Pilih Lokasi</option>
                                    <option value="jakarta">Jakarta</option>
                                    <option value="surabaya">Surabaya</option>
                                    <option value="bandung">Bandung</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="dailyRate" class="form-label">Tarif Harian (Rp)</label>
                                <input type="number" class="form-control" id="dailyRate" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="weeklyRate" class="form-label">Tarif Mingguan (Rp)</label>
                                <input type="number" class="form-control" id="weeklyRate" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="monthlyRate" class="form-label">Tarif Bulanan (Rp)</label>
                                <input type="number" class="form-control" id="monthlyRate" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="unitDescription" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="unitDescription" rows="3" placeholder="Deskripsi unit"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveUnit()">Simpan Unit</button>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Data Unit Marketing</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="exportFormat" class="form-label">Format Export</label>
                    <select class="form-select" id="exportFormat">
                        <option value="excel">Excel (.xlsx)</option>
                        <option value="pdf">PDF</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="exportFilter" class="form-label">Filter Data</label>
                    <select class="form-select" id="exportFilter">
                        <option value="all">Semua Data</option>
                        <option value="available">Hanya Unit Tersedia</option>
                        <option value="rented">Hanya Unit Disewa</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="exportData()">Export</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    $('#unitMarketingTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
        }
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize Charts
    initializeCharts();
});

function refreshData() {
    location.reload();
}

function initializeCharts() {
    // Unit Utilization Chart
    const ctx1 = document.getElementById('unitUtilizationChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Excavator',
                data: [65, 59, 80, 81, 56, 55],
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                tension: 0.1
            }, {
                label: 'Dump Truck',
                data: [28, 48, 40, 19, 86, 27],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });

    // Unit Category Chart
    const ctx2 = document.getElementById('unitCategoryChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Excavator', 'Dump Truck', 'Crane', 'Forklift'],
            datasets: [{
                data: [35, 28, 15, 11],
                backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

function viewUnit(unitCode) {
    // Add view unit logic here
    alert('View unit details: ' + unitCode);
}

function editUnit(unitCode) {
    // Add edit unit logic here
    alert('Edit unit: ' + unitCode);
}

function createQuotation(unitCode) {
    // Redirect to quotation page
    window.location.href = `<?= base_url('marketing/penawaran') ?>?unit=${unitCode}`;
}

function deleteUnit(unitCode) {
    if (confirm('Apakah Anda yakin ingin menghapus unit ini?')) {
        // Add delete logic here
        alert('Unit ' + unitCode + ' berhasil dihapus!');
    }
}

function saveUnit() {
    // Add save unit logic here
    alert('Unit berhasil disimpan!');
    $('#addUnitModal').modal('hide');
}

function exportData() {
    const format = document.getElementById('exportFormat').value;
    const filter = document.getElementById('exportFilter').value;
    
    // Add export logic here
    alert(`Export ${format} dengan filter ${filter} berhasil!`);
    $('#exportModal').modal('hide');
}
</script>

<?= $this->endSection() ?>
