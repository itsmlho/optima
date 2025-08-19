<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<style>
    .card-stats:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); }
    .table-card, .card-stats, .filter-card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-color: #667eea; color: white !important; }
    /* .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; } */
    .modal-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0; }
    .bg-orange {
        background-color: #fd7e14 !important;
        color: white;
    }
    .dropdown-item i {
        min-width: 20px;
        text-align: center;
    }
    .filter-card {
        background: #f8f9fa;
    }
    .progress {
        height: 22px;
        font-size: 0.75rem;
        background-color: #e9ecef;
        border-radius: 10px;
    }
    .progress-bar {
        font-weight: 600;
        background-color: #6c757d !important;
        color: #ffffff;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <!-- Enhanced Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-primary text-white h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1"><?= $stats['total'] ?? 0 ?></h2>
                        <h6 class="card-title text-uppercase small opacity-75 fw-semibold">Total PO</h6>
                    </div>
                    <i class="fas fa-file-invoice fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-warning text-white h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1"><?= $stats['pending'] ?? 0 ?></h2>
                        <h6 class="card-title text-uppercase small opacity-75 fw-semibold">Pending</h6>
                    </div>
                    <i class="fas fa-clock fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-orange text-white h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1"><?= $stats['Selesai dengan Catatan'] ?? 0 ?></h2>
                        <h6 class="card-title text-uppercase small opacity-75 fw-semibold">Sebagian Reject</h6>
                    </div>
                    <i class="fas fa-exclamation-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-success text-white h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1"><?= $stats['completed'] ?? 0 ?></h2>
                        <h6 class="card-title text-uppercase small opacity-75 fw-semibold">Completed</h6>
                    </div>
                    <i class="fas fa-flag-checkered fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card filter-card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="filterStatus" class="form-label">Filter Status</label>
                    <select id="filterStatus" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="completed">Completed</option>
                        <option value="Selesai dengan Catatan">Selesai dengan Catatan</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterSupplier" class="form-label">Filter Supplier</label>
                    <select id="filterSupplier" class="form-select">
                        <option value="">Semua Supplier</option>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?= esc($supplier['nama_supplier']) ?>"><?= esc($supplier['nama_supplier']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterDateFrom" class="form-label">Dari Tanggal</label>
                    <input type="date" id="filterDateFrom" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="filterDateTo" class="form-label">Sampai Tanggal</label>
                    <input type="date" id="filterDateTo" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card table-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold m-0">Purchase Order Attachment & Battery List</h5>
            <a href="<?= base_url('/purchasing/po-attachmentForm') ?>" class="btn btn-primary btn-action">
                <i class="fas fa-plus me-2"></i>New PO
            </a>
        </div>
        <div class="card-body">
            <table id="poAttachmentTable" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>Nomor PO</th>
                        <th>Supplier</th>
                        <th>PO Date</th>
                        <th>Status</th>
                        <th>Progres Verifikasi</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- View PO Modal -->
<div class="modal fade" id="viewPOModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Detail Purchase Order</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="poDetailsContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Template untuk konten modal (disembunyikan) -->
<template id="po-detail-template">
    <div class="row">
        <div class="col-md-6">
            <table class="table table-sm table-borderless">
                <tr><td style="width: 120px;"><strong>No. PO</strong></td><td>: {no_po}</td></tr>
                <tr><td><strong>Tanggal</strong></td><td>: {tanggal_po}</td></tr>
                <tr><td><strong>Supplier</strong></td><td>: {nama_supplier}</td></tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-sm table-borderless">
                <tr><td style="width: 120px;"><strong>Status PO</strong></td><td>: {status}</td></tr>
                <tr><td class="align-top"><strong>Keterangan</strong></td><td class="align-top">: {keterangan_po}</td></tr>
            </table>
        </div>
        <div class="col-12 mt-3">
            <h6>Item List:</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tipe Item</th>
                            <th>Deskripsi</th>
                            <th>SN Item</th>
                            <th>SN Charger</th>
                            <th>Status Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody id="po-items-tbody"></tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let poTable;

    $(document).ready(function() {
        poTable = $('#poAttachmentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('purchasing/po-attachment') ?>',
                type: 'POST',
                data: function(d) {
                    d.status = $("#filterStatus").val();
                    d.supplier = $("#filterSupplier").val();
                    d.start_date = $("#filterDateFrom").val();
                    d.end_date = $("#filterDateTo").val();
                }
            },
            columns: [
                { data: 'no_po' },
                { data: 'nama_supplier' },
                { data: 'tanggal_po' },
                { 
                    data: 'status',
                    render: function(data) {
                        const badgeClass = {
                            'pending': 'bg-warning', 
                            'approved': 'bg-success', 
                            'completed': 'bg-success', 
                            'cancelled': 'bg-danger',
                            'Selesai dengan Catatan': 'bg-orange'
                        }[data] || 'bg-secondary';
                        return `<span class="badge ${badgeClass}">${data}</span>`;
                    }
                },
                { 
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        const total = parseInt(row.total_items, 10);
                        const sesuai = parseInt(row.sesuai_items, 10);
                        const processed = parseInt(row.processed_items, 10);
                        const rejected = parseInt(row.rejected_items, 10);
                        
                        if (isNaN(total) || total === 0) {
                            return `<span class="text-muted small fst-italic">Tidak Ada Item</span>`;
                        }

                        const percentage = Math.round((sesuai / total) * 100);
                        
                        let warningIcon = '';
                        if (rejected > 0) {
                            warningIcon = `<i class="fas fa-exclamation-triangle text-danger ms-2" title="${rejected} item tidak sesuai"></i>`;
                        }

                        return `
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1" title="${sesuai} dari ${total} item sesuai">
                                    <div class="progress-bar progress-bar-striped" role="progressbar" style="width: ${percentage}%;" aria-valuenow="${percentage}" aria-valuemin="0" aria-valuemax="100">
                                        ${processed} / ${total}
                                    </div>
                                </div>
                                ${warningIcon}
                            </div>
                        `;
                    }
                },
                { 
                    data: 'id_po',
                    orderable: false,
                    render: function(data, type, row) {
                        let resolveButton = '';
                        if (row.status === 'Selesai dengan Catatan') {
                            resolveButton = `<li><a class="dropdown-item text-success" href="#" onclick="resolvePO(${data})"><i class="fas fa-check-double me-2"></i>Tandai Selesai</a></li>`;
                        }

                        return `
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item text" href="#" onclick="viewPO(${data})"><i class="fas fa-eye me-2"></i>Lihat Detail</a></li>
                                    <li><a class="dropdown-item text" href="<?= base_url('purchasing/edit-po-attachment/') ?>${data}"><i class="fas fa-edit me-2"></i>Edit PO</a></li>
                                    ${resolveButton}
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="deletePO(${data})"><i class="fas fa-trash me-2"></i>Hapus PO</a></li>
                                </ul>
                            </div>
                        `;
                    }
                }
            ],
            order: [[2, 'desc']],
            responsive: true,
            language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' }
        });

        // Event listener untuk filter
        $('#filterStatus, #filterSupplier, #filterDateFrom, #filterDateTo').on('change', function() {
            poTable.ajax.reload();
        });
    });

    function printPO(data) {
        const id = data.dataset.idPo;
        window.open("<?= base_url("purchasing/print-po-attachment/") ?>"+id, "_blank");
    }

    function viewPO(id) {
        $.ajax({
            // PERBAIKAN: Menggunakan URL API yang benar
            url: `<?= base_url('purchasing/api/po-attachment/') ?>${id}`,
            type: 'GET',
            dataType: 'JSON',
            beforeSend: function () {
                $('#poDetailsContent').html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-2x"></i><h5 class="mt-3">Loading...</h5></div>');
                $('#viewPOModal').modal('show');
            },
            success: function (response) {
                if (response.po) {
                    const template = $('#po-detail-template').html();
                    const po = response.po;
                    let content = template
                        .replace('{no_po}', po.no_po || '-')
                        .replace('{tanggal_po}', new Date(po.tanggal_po).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }))
                        .replace('{nama_supplier}', po.nama_supplier || '-')
                        .replace('{status}', po.status ? `<span class="badge bg-info">${po.status}</span>` : '-')
                        .replace('{keterangan_po}', po.keterangan_po || '-');

                    $('#poDetailsContent').html(content);

                    let itemsHtml = '';
                    // PERBAIKAN: Menggunakan response.details
                    response.details?.forEach((item, index) => {
                        const statusBadge = {
                            'Belum Dicek': 'bg-secondary',
                            'Sesuai': 'bg-success',
                            'Tidak Sesuai': 'bg-danger'
                        }[item.status_verifikasi] || 'bg-dark';
                        
                        let deskripsi = '';
                        if (item.item_type === 'Attachment') {
                            deskripsi = item.attachment_name;
                        } else { // Battery
                            deskripsi = `${item.merk_baterai || ''} ${item.tipe_baterai || ''}`;
                        }

                        itemsHtml += `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td>${item.item_type || '-'}</td>
                                <td>${deskripsi || '-'}</td>
                                <td class="text-center">${item.serial_number || '-'}</td>
                                <td class="text-center">${item.serial_number_charger || '-'}</td>
                                <td class="text-center"><span class="badge ${statusBadge}">${item.status_verifikasi}</span></td>
                            </tr>
                        `;
                    });
                    $('#po-items-tbody').html(itemsHtml);
                } else {
                     $('#poDetailsContent').html('<div class="text-center p-5 text-danger">Data tidak ditemukan.</div>');
                }
            },
            error: function () {
                $('#poDetailsContent').html('<div class="text-center p-5 text-danger">Gagal memuat data. Silakan coba lagi.</div>');
            }
        });
    }

    function deletePO(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data PO ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    // PERBAIKAN: Menggunakan URL yang benar
                    url: `<?= base_url('purchasing/delete-po-attachment/') ?>${id}`,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', 'Data PO telah dihapus.', 'success');
                            poTable.ajax.reload();
                        } else {
                            Swal.fire('Gagal!', response.message || 'Gagal menghapus data.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Tidak dapat terhubung ke server.', 'error');
                    }
                });
            }
        })
    }
    
    function resolvePO(id) {
        Swal.fire({
            title: 'Selesaikan PO ini?',
            text: "Pastikan semua masalah dengan supplier sudah selesai. Status akan diubah menjadi 'Completed'.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Selesaikan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    // PERBAIKAN: Menggunakan URL yang benar
                    url: `<?= base_url('purchasing/resolve-po-attachment/') ?>${id}`,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', 'Status PO telah diubah menjadi Completed.', 'success');
                            poTable.ajax.reload();
                        } else {
                            Swal.fire('Gagal!', response.message || 'Gagal mengubah status.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Tidak dapat terhubung ke server.', 'error');
                    }
                });
            }
        })
    }

    function editPO(data) {
        const id = data.dataset.idPo;
        window.location.href = `<?= base_url('/purchasing/edit-po-attachment/') ?>${id}`;
    }

    function deletePO(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data PO ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'DELETE',
                    url: '<?= base_url('/purchasing/delete-po-attachment/') ?>' + id,
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            Swal.fire('Dihapus!', 'PO berhasil dihapus.', 'success');
                            poTable.ajax.reload();
                        } else {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus PO.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Gagal!', 'Tidak dapat terhubung ke server.', 'error');
                    }
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>
