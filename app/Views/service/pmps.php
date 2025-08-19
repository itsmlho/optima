<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-calendar-check text-primary me-2"></i>
            PMPS - Preventive Maintenance Planned Service
        </h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPmpsModal">
            <i class="fas fa-plus me-2"></i>Schedule PMPS
        </button>
    </div>

    <!-- PMPS Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Due Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">2</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                                This Week</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">5</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
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
                                Completed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">15</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Scheduled</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">22</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PMPS Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">PMPS Schedule</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="pmpsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Unit</th>
                            <th>Service Type</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Last Service</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($pmps_data) && is_array($pmps_data)): ?>
                            <?php foreach ($pmps_data as $pmps): ?>
                                <tr>
                                    <td><?= $pmps['unit'] ?></td>
                                    <td><?= $pmps['type'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($pmps['due_date'])) ?></td>
                                    <td>
                                        <?php
                                        $today = date('Y-m-d');
                                        $dueDate = $pmps['due_date'];
                                        $daysDiff = (strtotime($dueDate) - strtotime($today)) / (60 * 60 * 24);
                                        
                                        if ($daysDiff < 0) {
                                            echo '<span class="badge badge-danger">Overdue</span>';
                                        } elseif ($daysDiff <= 1) {
                                            echo '<span class="badge badge-warning">Due Today</span>';
                                        } elseif ($daysDiff <= 7) {
                                            echo '<span class="badge badge-info">Due This Week</span>';
                                        } else {
                                            echo '<span class="badge badge-success">Scheduled</span>';
                                        }
                                        ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($pmps['last_service'])) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="createWorkOrder('<?= $pmps['unit'] ?>')">
                                            <i class="fas fa-wrench"></i> Create WO
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editPmps('<?= $pmps['unit'] ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No PMPS data found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add PMPS Modal -->
<div class="modal fade" id="addPmpsModal" tabindex="-1" aria-labelledby="addPmpsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPmpsModalLabel">Schedule New PMPS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addPmpsForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pmpsUnit" class="form-label">Unit</label>
                                <select class="form-select" id="pmpsUnit" required>
                                    <option value="">Select Unit</option>
                                    <option value="FL-001">FL-001 - Toyota 8FG25</option>
                                    <option value="FL-002">FL-002 - Mitsubishi FG25N</option>
                                    <option value="FL-003">FL-003 - Komatsu FG25T</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="serviceType" class="form-label">Service Type</label>
                                <select class="form-select" id="serviceType" required>
                                    <option value="">Select Service Type</option>
                                    <option value="Monthly Service">Monthly Service</option>
                                    <option value="3-Month Inspection">3-Month Inspection</option>
                                    <option value="6-Month Overhaul">6-Month Overhaul</option>
                                    <option value="Annual Service">Annual Service</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dueDate" class="form-label">Due Date</label>
                                <input type="date" class="form-control" id="dueDate" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="lastService" class="form-label">Last Service Date</label>
                                <input type="date" class="form-control" id="lastService" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="savePmps()">Schedule PMPS</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#pmpsTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[2, 'asc']]
    });
});

function createWorkOrder(unit) {
    alert('Create Work Order for unit: ' + unit);
    // Redirect to work orders page with unit pre-selected
    window.location.href = '<?= base_url('service/work-orders') ?>?unit=' + unit;
}

function editPmps(unit) {
    alert('Edit PMPS for unit: ' + unit);
}

function savePmps() {
    alert('PMPS scheduled successfully!');
    $('#addPmpsModal').modal('hide');
}
</script>

<?= $this->endSection() ?>

<?php $this->section('scripts'); ?>


<?php
$this->endSection();
?>