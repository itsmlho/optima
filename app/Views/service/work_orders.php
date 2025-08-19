<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<!-- Service Theme Application Script -->
<script>
// Apply service theme immediately
(function() {
    document.body.setAttribute('data-division', 'service');
    document.body.classList.add('service-theme');
    console.log('Service theme applied immediately');
})();
</script>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4 service-page-header">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-wrench text-primary me-2"></i>
            <?= $page_title ?>
        </h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWorkOrderModal">
            <i class="fas fa-plus me-2"></i>Add Work Order
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Work Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalWorkOrders">
                                <?= count($workorders ?? []) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                                Open</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="openWorkOrders">
                                <?= count(array_filter($workorders ?? [], function($wo) { return $wo['status'] == 'In Progress'; })) ?>
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
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingWorkOrders">
                                <?= count(array_filter($workorders ?? [], function($wo) { return $wo['status'] == 'Pending'; })) ?>
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
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Completed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="completedWorkOrders">
                                <?= count(array_filter($workorders ?? [], function($wo) { return $wo['status'] == 'Completed'; })) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Work Orders Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Work Orders List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="workOrdersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Work Order ID</th>
                            <th>Unit</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Assigned To</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($workorders) && is_array($workorders)): ?>
                            <?php foreach ($workorders as $wo): ?>
                                <tr>
                                    <td><?= $wo['id'] ?></td>
                                    <td><?= $wo['unit'] ?></td>
                                    <td><?= $wo['type'] ?></td>
                                    <td>
                                        <span class="badge badge-<?= $wo['status'] == 'Completed' ? 'success' : ($wo['status'] == 'In Progress' ? 'primary' : 'warning') ?>">
                                            <?= $wo['status'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $wo['priority'] == 'High' ? 'danger' : ($wo['priority'] == 'Medium' ? 'warning' : 'info') ?>">
                                            <?= $wo['priority'] ?>
                                        </span>
                                    </td>
                                    <td><?= $wo['assigned_to'] ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($wo['created_at'])) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewWorkOrder('<?= $wo['id'] ?>')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editWorkOrder('<?= $wo['id'] ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteWorkOrder('<?= $wo['id'] ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No work orders found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Work Order Modal -->
<div class="modal fade" id="addWorkOrderModal" tabindex="-1" aria-labelledby="addWorkOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addWorkOrderModalLabel">Add New Work Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addWorkOrderForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unitSelect" class="form-label">Unit</label>
                                <select class="form-select" id="unitSelect" required>
                                    <option value="">Select Unit</option>
                                    <option value="FL-001">FL-001 - Toyota 8FG25</option>
                                    <option value="FL-002">FL-002 - Mitsubishi FG25N</option>
                                    <option value="FL-003">FL-003 - Komatsu FG25T</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="workOrderType" class="form-label">Work Order Type</label>
                                <select class="form-select" id="workOrderType" required>
                                    <option value="">Select Type</option>
                                    <option value="Engine Maintenance">Engine Maintenance</option>
                                    <option value="Hydraulic System">Hydraulic System</option>
                                    <option value="Brake Inspection">Brake Inspection</option>
                                    <option value="Oil Change">Oil Change</option>
                                    <option value="General Inspection">General Inspection</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" id="priority" required>
                                    <option value="">Select Priority</option>
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="assignedTo" class="form-label">Assigned To</label>
                                <select class="form-select" id="assignedTo" required>
                                    <option value="">Select Technician</option>
                                    <option value="John Doe">John Doe</option>
                                    <option value="Jane Smith">Jane Smith</option>
                                    <option value="Mike Johnson">Mike Johnson</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveWorkOrder()">Save Work Order</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#workOrdersTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[6, 'desc']]
    });
});

function viewWorkOrder(id) {
    // Implementation for viewing work order
    alert('View Work Order: ' + id);
}

function editWorkOrder(id) {
    // Implementation for editing work order
    alert('Edit Work Order: ' + id);
}

function deleteWorkOrder(id) {
    if (confirm('Are you sure you want to delete this work order?')) {
        // Implementation for deleting work order
        alert('Delete Work Order: ' + id);
    }
}

function saveWorkOrder() {
    // Implementation for saving work order
    alert('Work Order saved successfully!');
    $('#addWorkOrderModal').modal('hide');
}
</script>

<?= $this->endSection() ?> 