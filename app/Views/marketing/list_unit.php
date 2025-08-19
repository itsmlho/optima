<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-list me-2"></i>Unit Catalog
        </h1>
        <div class="d-sm-flex align-items-center">
            <div class="btn-group" role="group">
                <button class="btn btn-outline-info btn-sm" onclick="refreshData()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
                <button class="btn btn-outline-secondary btn-sm" onclick="exportCatalog()">
                    <i class="fas fa-download me-1"></i>Export Catalog
                </button>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                    <i class="fas fa-plus me-1"></i>Add Unit
                </button>
            </div>
        </div>
    </div>

    <!-- Unit Categories Stats -->
    <div class="row mb-4">
        <?php if (isset($unit_categories) && is_array($unit_categories)): ?>
            <?php $colors = ['primary', 'success', 'warning', 'info', 'danger']; $i = 0; ?>
            <?php foreach ($unit_categories as $category => $count): ?>
                <div class="col mb-4">
                    <div class="card border-left-<?= $colors[$i % count($colors)] ?> shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-<?= $colors[$i % count($colors)] ?> text-uppercase mb-1">
                                        <?= $category ?>
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $count ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-truck fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $i++; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Units</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="filterType" class="form-label">Type</label>
                        <select class="form-select" id="filterType">
                            <option value="">All Types</option>
                            <option value="Forklift">Forklift</option>
                            <option value="Excavator">Excavator</option>
                            <option value="Crane">Crane</option>
                            <option value="Dump Truck">Dump Truck</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="filterStatus" class="form-label">Status</label>
                        <select class="form-select" id="filterStatus">
                            <option value="">All Status</option>
                            <option value="Available">Available</option>
                            <option value="Rented">Rented</option>
                            <option value="Maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="filterLocation" class="form-label">Location</label>
                        <select class="form-select" id="filterLocation">
                            <option value="">All Locations</option>
                            <option value="Jakarta">Jakarta</option>
                            <option value="Surabaya">Surabaya</option>
                            <option value="Bandung">Bandung</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="filterBrand" class="form-label">Brand</label>
                        <select class="form-select" id="filterBrand">
                            <option value="">All Brands</option>
                            <option value="Toyota">Toyota</option>
                            <option value="Caterpillar">Caterpillar</option>
                            <option value="Tadano">Tadano</option>
                            <option value="Hino">Hino</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button class="btn btn-primary" onclick="applyFilters()">
                        <i class="fas fa-filter me-2"></i>Apply Filters
                    </button>
                    <button class="btn btn-outline-secondary" onclick="clearFilters()">
                        <i class="fas fa-times me-2"></i>Clear Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Units Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Equipment Catalog</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="unitsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Unit Code</th>
                            <th>Type</th>
                            <th>Brand/Model</th>
                            <th>Capacity</th>
                            <th>Year</th>
                            <th>Status</th>
                            <th>Rental Rate</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($units) && is_array($units)): ?>
                            <?php foreach ($units as $unit): ?>
                                <tr>
                                    <td><strong><?= $unit['unit_code'] ?></strong></td>
                                    <td><?= $unit['type'] ?></td>
                                    <td>
                                        <div>
                                            <strong><?= $unit['brand'] ?></strong><br>
                                            <small class="text-muted"><?= $unit['model'] ?></small>
                                        </div>
                                    </td>
                                    <td><?= $unit['capacity'] ?></td>
                                    <td><?= $unit['year'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $unit['status'] == 'Available' ? 'success' : ($unit['status'] == 'Rented' ? 'warning' : 'info') ?>">
                                            <?= $unit['status'] ?>
                                        </span>
                                    </td>
                                    <td>Rp <?= number_format($unit['rental_rate'], 0, ',', '.') ?>/month</td>
                                    <td><?= $unit['location'] ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-info" onclick="viewUnit(<?= $unit['id'] ?>)" data-bs-toggle="tooltip" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-primary" onclick="editUnit(<?= $unit['id'] ?>)" data-bs-toggle="tooltip" title="Edit Unit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if ($unit['status'] == 'Available'): ?>
                                                <button class="btn btn-outline-success" onclick="createQuotation(<?= $unit['id'] ?>)" data-bs-toggle="tooltip" title="Create Quotation">
                                                    <i class="fas fa-file-invoice"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">No units found</td>
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
                <h5 class="modal-title" id="addUnitModalLabel">Add New Unit to Catalog</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUnitForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unitCode" class="form-label">Unit Code</label>
                                <input type="text" class="form-control" id="unitCode" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unitType" class="form-label">Type</label>
                                <select class="form-select" id="unitType" required>
                                    <option value="">Select Type</option>
                                    <option value="Forklift">Forklift</option>
                                    <option value="Excavator">Excavator</option>
                                    <option value="Crane">Crane</option>
                                    <option value="Dump Truck">Dump Truck</option>
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
                                <label for="unitCapacity" class="form-label">Capacity</label>
                                <input type="text" class="form-control" id="unitCapacity" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unitYear" class="form-label">Year</label>
                                <input type="number" class="form-control" id="unitYear" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unitLocation" class="form-label">Location</label>
                                <select class="form-select" id="unitLocation" required>
                                    <option value="">Select Location</option>
                                    <option value="Jakarta">Jakarta</option>
                                    <option value="Surabaya">Surabaya</option>
                                    <option value="Bandung">Bandung</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rentalRate" class="form-label">Rental Rate (Rp/month)</label>
                                <input type="number" class="form-control" id="rentalRate" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="unitDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="unitDescription" rows="3" placeholder="Enter unit description"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveUnit()">Save Unit</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    $('#unitsTable').DataTable({
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
});

function refreshData() {
    location.reload();
}

function exportCatalog() {
    // Add export logic here
    alert('Export catalog functionality will be implemented');
}

function applyFilters() {
    // Add filter logic here
    alert('Filter functionality will be implemented');
}

function clearFilters() {
    // Clear all filters
    document.getElementById('filterType').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterLocation').value = '';
    document.getElementById('filterBrand').value = '';
    
    // Refresh table
    $('#unitsTable').DataTable().search('').draw();
}

function viewUnit(unitId) {
    // Add view unit logic here
    alert('View unit details: ' + unitId);
}

function editUnit(unitId) {
    // Add edit unit logic here
    alert('Edit unit: ' + unitId);
}

function createQuotation(unitId) {
    // Add create quotation logic here
    alert('Create quotation for unit: ' + unitId);
}

function saveUnit() {
    // Add save unit logic here
    alert('Unit saved successfully!');
    $('#addUnitModal').modal('hide');
}
</script>

<?= $this->endSection() ?> 