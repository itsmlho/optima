<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<style>
    .card-stats {
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .card-stats:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .table-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .badge-pending {
        background-color: #ffc107;
        color: #000;
    }
    
    .badge-confirmed {
        background-color: #28a745;
        color: #fff;
    }
    
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #6c757d;
        font-weight: 600;
        padding: 1rem 1.5rem;
        transition: all 0.3s ease;
    }
    
    .nav-tabs .nav-link:hover {
        border-bottom-color: #dee2e6;
        color: #495057;
    }
    
    .nav-tabs .nav-link.active {
        border-bottom-color: #667eea;
        color: #667eea;
        background: transparent;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tools text-primary me-2"></i>
            Pemakaian & Pengembalian Sparepart
        </h1>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card card-stats border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Pemakaian</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-usage-total">
                                <?= $stats['usage_total'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card card-stats border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Returns</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-return-pending">
                                <?= $stats['return_pending'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card card-stats border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Confirmed Returns</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stat-return-confirmed">
                                <?= $stats['return_confirmed'] ?? 0 ?>
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

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4" id="sparepartTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="usage-tab" data-bs-toggle="tab" data-bs-target="#usage" type="button" role="tab">
                <i class="fas fa-list-check me-2"></i>Pemakaian
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="returns-tab" data-bs-toggle="tab" data-bs-target="#returns" type="button" role="tab">
                <i class="fas fa-undo me-2"></i>Pengembalian
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="sparepartTabContent">
        <!-- Tab Pemakaian -->
        <div class="tab-pane fade show active" id="usage" role="tabpanel">
            <?php if (isset($usage_table_exists) && !$usage_table_exists): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Tabel <code>work_order_sparepart_usage</code> belum tersedia.
            </div>
            <?php else: ?>
            <div class="card table-card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>Daftar Pemakaian Sparepart
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="usageTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Work Order</th>
                                    <th>Sparepart</th>
                                    <th>Customer</th>
                                    <th>Unit</th>
                                    <th>Mekanik</th>
                                    <th>Dibawa</th>
                                    <th>Digunakan</th>
                                    <th>Dikembalikan</th>
                                    <th>Catatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTable will populate here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Tab Pengembalian -->
        <div class="tab-pane fade" id="returns" role="tabpanel">
            <?php if (isset($return_table_exists) && !$return_table_exists): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Tabel <code>work_order_sparepart_returns</code> belum tersedia. 
                <a href="<?= base_url('warehouse/sparepart-returns') ?>" class="alert-link">Lihat instruksi setup</a>
            </div>
            <?php else: ?>
            <!-- Filter Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-filter me-2"></i>Filter
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="filter-status">
                                <option value="PENDING" selected>Pending</option>
                                <option value="CONFIRMED">Confirmed</option>
                                <option value="ALL">All</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-primary w-100" onclick="applyReturnFilters()">
                                <i class="fas fa-search me-2"></i>Apply Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Returns Table -->
            <div class="card table-card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>Daftar Pengembalian Sparepart
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="returnsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Work Order</th>
                                    <th>Sparepart</th>
                                    <th>Customer</th>
                                    <th>Unit</th>
                                    <th>Mekanik</th>
                                    <th>Dibawa</th>
                                    <th>Digunakan</th>
                                    <th>Dikembalikan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTable will populate here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Detail Usage Modal -->
<div class="modal fade" id="usageDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i>Detail Pemakaian Sparepart
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="usageDetailBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail/Confirm Return Modal -->
<div class="modal fade" id="returnDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i>Detail Pengembalian Sparepart
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="returnDetailBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
$(document).ready(function() {
    <?php if (isset($usage_table_exists) && $usage_table_exists): ?>
    // Initialize Usage DataTable
    const usageTable = $('#usageTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('warehouse/sparepart-usage/get-usage') ?>',
            type: 'POST'
        },
        columns: [
            { data: 'used_at', name: 'used_at' },
            { data: 'work_order_number', name: 'work_order_number' },
            { 
                data: 'sparepart_name',
                render: function(data, type, row) {
                    return `<strong>${data}</strong><br><small class="text-muted">${row.sparepart_code}</small>`;
                }
            },
            { data: 'customer_name', name: 'customer_name' },
            { data: 'unit_number', name: 'unit_number' },
            { 
                data: 'mechanic_name',
                name: 'mechanic_name',
                render: function(data) {
                    return data && data !== '-' ? `<small>${data}</small>` : '-';
                }
            },
            { 
                data: 'quantity_brought',
                render: function(data, type, row) {
                    return `${data} ${row.satuan}`;
                }
            },
            { 
                data: 'quantity_used',
                render: function(data, type, row) {
                    return `<strong class="text-success">${data} ${row.satuan}</strong>`;
                }
            },
            { 
                data: 'quantity_returned',
                render: function(data, type, row) {
                    return data > 0 ? `<span class="text-warning">${data} ${row.satuan}</span>` : '-';
                }
            },
            { 
                data: 'usage_notes',
                render: function(data) {
                    return data && data !== '-' ? data : '-';
                }
            },
            {
                data: 'id',
                orderable: false,
                render: function(data) {
                    return `<button class="btn btn-sm btn-info" onclick="viewUsageDetail(${data})" title="Detail">
                        <i class="fas fa-eye"></i>
                    </button>`;
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            processing: "Memproses...",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });
    <?php endif; ?>

    <?php if (isset($return_table_exists) && $return_table_exists): ?>
    // Initialize Returns DataTable
    const returnsTable = $('#returnsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('warehouse/sparepart-usage/get-returns') ?>',
            type: 'POST',
            data: function(d) {
                d.status = $('#filter-status').val();
            }
        },
        columns: [
            { data: 'created_at', name: 'created_at' },
            { data: 'work_order_number', name: 'work_order_number' },
            { 
                data: 'sparepart_name',
                render: function(data, type, row) {
                    return `<strong>${data}</strong><br><small class="text-muted">${row.sparepart_code}</small>`;
                }
            },
            { data: 'customer_name', name: 'customer_name' },
            { data: 'unit_number', name: 'unit_number' },
            { 
                data: 'mechanic_name',
                name: 'mechanic_name',
                render: function(data) {
                    return data && data !== '-' ? `<small>${data}</small>` : '-';
                }
            },
            { 
                data: 'quantity_brought',
                render: function(data, type, row) {
                    return `${data} ${row.satuan}`;
                }
            },
            { 
                data: 'quantity_used',
                render: function(data, type, row) {
                    return `${data} ${row.satuan}`;
                }
            },
            { 
                data: 'quantity_return',
                render: function(data, type, row) {
                    return `<strong class="text-warning">${data} ${row.satuan}</strong>`;
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    const badgeClass = data === 'PENDING' ? 'badge-pending' : 'badge-confirmed';
                    return `<span class="badge ${badgeClass}">${data}</span>`;
                }
            },
            {
                data: 'id',
                orderable: false,
                render: function(data, type, row) {
                    let buttons = `<button class="btn btn-sm btn-info" onclick="viewReturnDetail(${data})" title="Detail">
                        <i class="fas fa-eye"></i>
                    </button>`;
                    if (row.status === 'PENDING') {
                        buttons += ` <button class="btn btn-sm btn-success" onclick="confirmReturn(${data})" title="Konfirmasi">
                            <i class="fas fa-check"></i>
                        </button>`;
                    }
                    return buttons;
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            processing: "Memproses...",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });

    // View usage detail
    window.viewUsageDetail = function(id) {
        $.ajax({
            url: '<?= base_url('warehouse/sparepart-usage/get-usage-detail') ?>/' + id,
            type: 'GET',
            beforeSend: function() {
                $('#usageDetailBody').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    let html = `
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Work Order:</strong><br>
                                <span class="badge bg-primary">${data.work_order_number || '-'}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Tanggal WO:</strong><br>
                                ${data.report_date_formatted || '-'}
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Sparepart Code:</strong><br>
                                ${data.sparepart_code || '-'}
                            </div>
                            <div class="col-md-6">
                                <strong>Sparepart Name:</strong><br>
                                <strong>${data.sparepart_name || '-'}</strong>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Quantity Brought:</strong><br>
                                <span class="badge bg-info">${data.quantity_brought || 0} ${data.satuan || 'PCS'}</span>
                            </div>
                            <div class="col-md-4">
                                <strong>Quantity Used:</strong><br>
                                <span class="badge bg-success">${data.quantity_used || 0} ${data.satuan || 'PCS'}</span>
                            </div>
                            <div class="col-md-4">
                                <strong>Quantity Returned:</strong><br>
                                ${data.quantity_returned > 0 ? `<span class="badge bg-warning">${data.quantity_returned} ${data.satuan || 'PCS'}</span>` : '<span class="badge bg-secondary">0</span>'}
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Customer:</strong><br>
                                ${data.customer_name || '-'}
                            </div>
                            <div class="col-md-6">
                                <strong>Unit:</strong><br>
                                ${data.unit_number || '-'}
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Mekanik:</strong><br>
                                ${data.mechanic_name || '-'}
                            </div>
                            <div class="col-md-6">
                                <strong>Tanggal Digunakan:</strong><br>
                                ${data.used_at_formatted || '-'}
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Tanggal Dikembalikan:</strong><br>
                                ${data.returned_at_formatted || 'Belum dikembalikan'}
                            </div>
                        </div>
                        ${data.usage_notes ? `<hr><div class="mb-3"><strong>Catatan Pemakaian:</strong><br>${data.usage_notes}</div>` : ''}
                        ${data.return_notes ? `<div class="mb-3"><strong>Catatan Pengembalian:</strong><br>${data.return_notes}</div>` : ''}
                    `;
                    $('#usageDetailBody').html(html);
                    $('#usageDetailModal').modal('show');
                } else {
                    alert('Error: ' + (response.message || 'Gagal memuat data'));
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat memuat data');
            }
        });
    };

    // View return detail
    window.viewReturnDetail = function(id) {
        $.ajax({
            url: '<?= base_url('warehouse/sparepart-usage/get-return-detail') ?>/' + id,
            type: 'GET',
            beforeSend: function() {
                $('#returnDetailBody').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    let html = `
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Work Order:</strong><br>
                                <span class="badge bg-primary">${data.work_order_number || '-'}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Tanggal WO:</strong><br>
                                ${data.report_date_formatted || '-'}
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Sparepart Code:</strong><br>
                                ${data.sparepart_code}
                            </div>
                            <div class="col-md-6">
                                <strong>Sparepart Name:</strong><br>
                                <strong>${data.sparepart_name}</strong>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Quantity Brought:</strong><br>
                                <span class="badge bg-info">${data.quantity_brought} ${data.satuan}</span>
                            </div>
                            <div class="col-md-4">
                                <strong>Quantity Used:</strong><br>
                                <span class="badge bg-success">${data.quantity_used} ${data.satuan}</span>
                            </div>
                            <div class="col-md-4">
                                <strong>Quantity Return:</strong><br>
                                <span class="badge bg-warning">${data.quantity_return} ${data.satuan}</span>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Customer:</strong><br>
                                ${data.customer_name || '-'}
                            </div>
                            <div class="col-md-6">
                                <strong>Unit:</strong><br>
                                ${data.unit_number || '-'}
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Mekanik:</strong><br>
                                ${data.mechanic_name || '-'}
                            </div>
                            <div class="col-md-6">
                                <strong>Status:</strong><br>
                                <span class="badge ${data.status === 'PENDING' ? 'badge-pending' : 'badge-confirmed'}">${data.status}</span>
                            </div>
                        </div>
                        <hr>
                        ${data.return_notes ? `<div class="mb-3"><strong>Catatan:</strong><br>${data.return_notes}</div>` : ''}
                        ${data.confirmed_at ? `<div class="mb-3"><strong>Dikonfirmasi:</strong><br>${data.confirmed_at_formatted} oleh ${data.confirmed_by_name || '-'}</div>` : ''}
                    `;
                    
                    if (data.status === 'PENDING') {
                        html += `
                            <hr>
                            <div class="mb-3">
                                <label class="form-label">Catatan Konfirmasi (Optional)</label>
                                <textarea class="form-control" id="confirm-notes" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                            </div>
                            <div class="d-grid">
                                <button class="btn btn-success" onclick="confirmReturn(${data.id})">
                                    <i class="fas fa-check me-2"></i>Konfirmasi Pengembalian
                                </button>
                            </div>
                        `;
                    }
                    
                    $('#returnDetailBody').html(html);
                    $('#returnDetailModal').modal('show');
                } else {
                    alert('Error: ' + (response.message || 'Gagal memuat data'));
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat memuat data');
            }
        });
    };

    // Apply return filters
    window.applyReturnFilters = function() {
        returnsTable.ajax.reload();
    };

    // Confirm return
    window.confirmReturn = function(id) {
        if (!confirm('Apakah Anda yakin ingin mengonfirmasi pengembalian sparepart ini?')) {
            return;
        }

        const notes = $('#confirm-notes').val() || null;

        $.ajax({
            url: '<?= base_url('warehouse/sparepart-usage/confirm-return') ?>/' + id,
            type: 'POST',
            data: { notes: notes },
            success: function(response) {
                if (response.success) {
                    alert('Pengembalian sparepart berhasil dikonfirmasi');
                    $('#returnDetailModal').modal('hide');
                    returnsTable.ajax.reload();
                } else {
                    alert('Error: ' + (response.message || 'Gagal mengonfirmasi'));
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat mengonfirmasi');
            }
        });
    };
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>

