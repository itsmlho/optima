<?= $this->extend('layouts/base') ?>


<?= $this->section('content') ?>



    <!-- Statistics Cards -->
    <div class="row mt-3 mb-4">
        <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-info-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-list stat-icon text-info"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-usage-total">
                            <?= $stats['usage_total'] ?? 0 ?>
                        </div>
                        <div class="text-muted">Total Pemakaian</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-warning-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-clock stat-icon text-warning"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-return-pending">
                            <?= $stats['return_pending'] ?? 0 ?>
                        </div>
                        <div class="text-muted">Pending Returns</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
            <div class="stat-card bg-success-soft">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-check-circle stat-icon text-success"></i>
                    </div>
                    <div>
                        <div class="stat-value" id="stat-return-confirmed">
                            <?= $stats['return_confirmed'] ?? 0 ?>
                        </div>
                        <div class="text-muted">Confirmed Returns</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card with Tabs -->
    <div class="card table-card shadow mb-4">
        <div class="card-header">
            <div class="row align-items-center mb-3">
                <div class="col">
                    <h5 class="card-title fw-bold m-0">
                        <i class="fas fa-tools text-primary me-2"></i>
                        Pemakaian & Pengembalian Sparepart
                    </h5>
                </div>
            </div>
            
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs mb-3" id="sparepartTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="usage-tab" data-bs-toggle="tab" data-bs-target="#usage" type="button" role="tab" aria-controls="usage" aria-selected="true">
                        <i class="fas fa-list-check me-1"></i>
                        <strong>Pemakaian</strong>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="returns-tab" data-bs-toggle="tab" data-bs-target="#returns" type="button" role="tab" aria-controls="returns" aria-selected="false">
                        <i class="fas fa-undo me-1"></i>
                        <strong>Pengembalian</strong>
                    </button>
                </li>
            </ul>
        </div>
        
        <div class="card-body">
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
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="usageTable">
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
                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Filter Status</label>
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

                    <!-- Returns Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="returnsTable">
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
                    <?php endif; ?>
                </div>
            </div>
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
let usageTable, returnsTable;
let usageTableInitialized = false;
let returnsTableInitialized = false;

$(document).ready(function() {
    // Handle tab switching
    $('#sparepartTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const targetTab = $(e.target).data('bs-target');
        
        // Small delay to ensure tab is fully visible
        setTimeout(function() {
            // Initialize Usage Table when usage tab is shown
            if (targetTab === '#usage' && !usageTableInitialized) {
                <?php if (isset($usage_table_exists) && $usage_table_exists): ?>
                initializeUsageTable();
                <?php endif; ?>
            }
            
            // Initialize Returns Table when returns tab is shown
            if (targetTab === '#returns' && !returnsTableInitialized) {
                <?php if (isset($return_table_exists) && $return_table_exists): ?>
                initializeReturnsTable();
                <?php endif; ?>
            }
        }, 150);
    });
    
    // Force activate Pemakaian tab on page load (default tab)
    $('#usage-tab').tab('show');
    
    // Initialize first tab (Pemakaian) on page load (with delay to ensure DOM is ready)
    setTimeout(function() {
        // Always initialize Pemakaian tab first as default
        <?php if (isset($usage_table_exists) && $usage_table_exists): ?>
        if (!usageTableInitialized) {
            console.log('Initializing default tab: Pemakaian');
            initializeUsageTable();
        }
        <?php endif; ?>
    }, 300);
    
    <?php if (isset($usage_table_exists) && $usage_table_exists): ?>
    function initializeUsageTable() {
        if (usageTableInitialized) {
            console.log('Usage table already initialized');
            return;
        }
        
        // Check if table element exists
        if ($('#usageTable').length === 0) {
            console.error('Usage table element not found');
            return;
        }
        
        // Ensure tab is active before initializing
        if (!$('#usage').hasClass('active')) {
            $('#usage-tab').tab('show');
        }
        
        console.log('Initializing usage table...');
        usageTable = $('#usageTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('warehouse/sparepart-usage/get-usage') ?>',
            type: 'POST'
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
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
        
        usageTableInitialized = true;
        console.log('Usage table initialized successfully');
    }
    <?php endif; ?>

    <?php if (isset($return_table_exists) && $return_table_exists): ?>
    function initializeReturnsTable() {
        if (returnsTableInitialized) {
            console.log('Returns table already initialized');
            return;
        }
        
        // Check if table element exists
        if ($('#returnsTable').length === 0) {
            console.error('Returns table element not found');
            return;
        }
        
        // Check if tab is visible (either active or will be active)
        const returnsTab = $('#returns');
        if (!returnsTab.length) {
            console.error('Returns tab element not found');
            return;
        }
        
        console.log('Initializing returns table...');
        returnsTable = $('#returnsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('warehouse/sparepart-usage/get-returns') ?>',
            type: 'POST',
            data: function(d) {
                d.status = $('#filter-status').val();
            }
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
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
        
        returnsTableInitialized = true;
        console.log('Returns table initialized successfully');
    }
    <?php endif; ?>

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
        if (returnsTable && returnsTableInitialized) {
            returnsTable.ajax.reload();
        }
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
                    if (returnsTable && returnsTableInitialized) {
                        returnsTable.ajax.reload();
                    }
                } else {
                    alert('Error: ' + (response.message || 'Gagal mengonfirmasi'));
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat mengonfirmasi');
            }
        });
    };
});
</script>
<?= $this->endSection() ?>

