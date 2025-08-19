<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">History Rolling Unit</h1>
            <p class="text-muted">Riwayat aktivitas dan operasi unit forklift</p>
        </div>
        <div>
            <button class="btn btn-success" onclick="exportHistory()">
                <i class="fas fa-download"></i> Export History
            </button>
        </div>
    </div>

    <!-- Filter Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <label for="filterUnit">Filter Unit</label>
                    <select class="form-control" id="filterUnit">
                        <option value="">Semua Unit</option>
                        <option value="FL-001">FL-001</option>
                        <option value="FL-002">FL-002</option>
                        <option value="FL-003">FL-003</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <label for="filterActivity">Filter Aktivitas</label>
                    <select class="form-control" id="filterActivity">
                        <option value="">Semua Aktivitas</option>
                        <option value="Deployment">Deployment</option>
                        <option value="Maintenance">Maintenance</option>
                        <option value="Transport">Transport</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <label for="dateFrom">Dari Tanggal</label>
                    <input type="date" class="form-control" id="dateFrom">
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <label for="dateTo">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="dateTo">
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Aktivitas Unit</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="historyTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kode Unit</th>
                            <th>Aktivitas</th>
                            <th>Lokasi</th>
                            <th>Operator</th>
                            <th>Jam Kerja</th>
                            <th>Konsumsi BBM</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rolling_history)): ?>
                            <?php foreach ($rolling_history as $history): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($history['date'])) ?></td>
                                    <td>
                                        <span class="badge badge-primary"><?= esc($history['unit_code']) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $history['activity'] === 'Deployment' ? 'success' : ($history['activity'] === 'Maintenance' ? 'warning' : 'info') ?>">
                                            <?= esc($history['activity']) ?>
                                        </span>
                                    </td>
                                    <td><?= esc($history['location']) ?></td>
                                    <td><?= esc($history['operator']) ?></td>
                                    <td><?= $history['hours_worked'] ?> jam</td>
                                    <td><?= $history['fuel_consumption'] ?> L</td>
                                    <td><?= esc($history['notes']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data history</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Activities
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($rolling_history) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
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
                                Total Work Hours
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= array_sum(array_column($rolling_history, 'hours_worked')) ?> jam
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                                Total Fuel Consumption
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= array_sum(array_column($rolling_history, 'fuel_consumption')) ?> L
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-gas-pump fa-2x text-gray-300"></i>
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
                                Avg Hours/Day
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($rolling_history) > 0 ? round(array_sum(array_column($rolling_history, 'hours_worked')) / count($rolling_history), 1) : 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#historyTable').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
        },
        order: [[ 0, "desc" ]]
    });

    // Filter functionality
    $('#filterUnit, #filterActivity, #dateFrom, #dateTo').change(function() {
        filterHistory();
    });
});

function filterHistory() {
    var unit = $('#filterUnit').val();
    var activity = $('#filterActivity').val();
    var dateFrom = $('#dateFrom').val();
    var dateTo = $('#dateTo').val();

    var table = $('#historyTable').DataTable();
    
    // Reset all filters
    table.columns().search('').draw();
    
    // Apply filters
    if (unit) {
        table.column(1).search(unit);
    }
    if (activity) {
        table.column(2).search(activity);
    }
    
    table.draw();
}

function exportHistory() {
    // Implementasi export
    showNotification('Export history functionality', 'info');
}
</script>
<?= $this->endSection() ?> 