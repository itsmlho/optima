<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-history text-primary me-2"></i>
            Work Order History
        </h1>
        <a href="<?= base_url('service/work-orders') ?>" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Back to Work Orders
        </a>
    </div>

    <!-- Work Order History Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Completed Work Orders</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="workOrderHistoryTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Work Order ID</th>
                            <th>Unit</th>
                            <th>Type</th>
                            <th>Assigned To</th>
                            <th>Completed At</th>
                            <th>Duration</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($work_order_history) && is_array($work_order_history)): ?>
                            <?php foreach ($work_order_history as $wo): ?>
                                <tr>
                                    <td><?= $wo['id'] ?></td>
                                    <td><?= $wo['unit'] ?></td>
                                    <td><?= $wo['type'] ?></td>
                                    <td><?= $wo['assigned_to'] ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($wo['completed_at'])) ?></td>
                                    <td><?= $wo['duration'] ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewWorkOrderHistory('<?= $wo['id'] ?>')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-success" onclick="downloadReport('<?= $wo['id'] ?>')">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No completed work orders found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#workOrderHistoryTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[4, 'desc']]
    });
});

function viewWorkOrderHistory(id) {
    alert('View Work Order History: ' + id);
}

function downloadReport(id) {
    alert('Download Report for Work Order: ' + id);
}
</script>

<?= $this->endSection() ?> 