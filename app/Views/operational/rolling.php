<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Data Rolling Unit</h1>
            <p class="text-muted">Monitor dan kelola unit forklift yang sedang beroperasi</p>
        </div>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRollingModal">
                <i class="fas fa-plus"></i> Tambah Data Rolling
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Unit Aktif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count(array_filter($rolling_units, function($unit) { return $unit['status'] === 'Active'; })) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
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
                                Unit Maintenance
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count(array_filter($rolling_units, function($unit) { return $unit['status'] === 'Maintenance'; })) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
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
                                Total Engine Hours
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format(array_sum(array_column($rolling_units, 'engine_hours'))) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tachometer-alt fa-2x text-gray-300"></i>
                        </div>
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
                                Total Unit
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($rolling_units) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Unit Rolling</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Kode Unit</th>
                            <th>Brand</th>
                            <th>Model</th>
                            <th>Kapasitas</th>
                            <th>Engine Hours</th>
                            <th>Fuel Type</th>
                            <th>Status</th>
                            <th>Lokasi</th>
                            <th>Operator</th>
                            <th>Last Maintenance</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rolling_units)): ?>
                            <?php foreach ($rolling_units as $unit): ?>
                                <tr>
                                    <td><?= esc($unit['unit_code']) ?></td>
                                    <td><?= esc($unit['brand']) ?></td>
                                    <td><?= esc($unit['model']) ?></td>
                                    <td><?= esc($unit['capacity']) ?></td>
                                    <td><?= number_format($unit['engine_hours']) ?></td>
                                    <td><?= esc($unit['fuel_type']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $unit['status'] === 'Active' ? 'success' : 'warning' ?>">
                                            <?= esc($unit['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= esc($unit['location']) ?></td>
                                    <td><?= esc($unit['operator']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($unit['last_maintenance'])) ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewRollingUnit('<?= $unit['unit_code'] ?>')" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="editRollingUnit('<?= $unit['unit_code'] ?>')" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info" onclick="historyRollingUnit('<?= $unit['unit_code'] ?>')" title="History">
                                                <i class="fas fa-history"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="text-center">Tidak ada data unit rolling</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Rolling Modal -->
<div class="modal fade" id="addRollingModal" tabindex="-1" role="dialog" aria-labelledby="addRollingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRollingModalLabel">Tambah Data Rolling Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addRollingForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unit_code">Kode Unit</label>
                                <input type="text" class="form-control" id="unit_code" name="unit_code" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="brand">Brand</label>
                                <input type="text" class="form-control" id="brand" name="brand" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="model">Model</label>
                                <input type="text" class="form-control" id="model" name="model" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="capacity">Kapasitas</label>
                                <input type="text" class="form-control" id="capacity" name="capacity" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="engine_hours">Engine Hours</label>
                                <input type="number" class="form-control" id="engine_hours" name="engine_hours" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fuel_type">Fuel Type</label>
                                <select class="form-control" id="fuel_type" name="fuel_type" required>
                                    <option value="">Pilih Fuel Type</option>
                                    <option value="Diesel">Diesel</option>
                                    <option value="LPG">LPG</option>
                                    <option value="Electric">Electric</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="">Pilih Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Maintenance">Maintenance</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="location">Lokasi</label>
                                <input type="text" class="form-control" id="location" name="location" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="operator">Operator</label>
                                <input type="text" class="form-control" id="operator" name="operator" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveRollingUnit()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
        }
    });
});

function viewRollingUnit(unitCode) {
    // Implementasi view detail
    showNotification('View details for unit: ' + unitCode, 'info');
}

function editRollingUnit(unitCode) {
    // Implementasi edit
    showNotification('Edit unit: ' + unitCode, 'info');
}

function historyRollingUnit(unitCode) {
    // Redirect ke history page
    window.location.href = '<?= base_url() ?>/unitRolling/history?unit=' + unitCode;
}

function saveRollingUnit() {
    // Implementasi save
    showNotification('Simpan data rolling unit', 'success');
    $('#addRollingModal').modal('hide');
}
</script>
<?= $this->endSection() ?>