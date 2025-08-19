<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<style>
    .card-stats:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); }
    .table-card, .card-stats, .filter-card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-color: #667eea; color: white !important; }
    /* .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; } */
    .modal-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0; }
    .bg-orange { background-color: #fd7e14 !important; color: white; }
    .dropdown-item i { min-width: 20px; text-align: center; }
    .filter-card { background: #f8f9fa; }
    .progress { height: 22px; font-size: 0.75rem; background-color: #e9ecef; border-radius: 10px; }
    .progress-bar { font-weight: 600; background-color: #6c757d !important; color: #ffffff; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <!-- Enhanced Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-primary text-white h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1"><?= $total ?? 0 ?></h2>    
                        <h6 class="card-title text-uppercase small opacity-75 fw-semibold">Total PO</h6>
                    </div>
                    <i class="fas fa-truck fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-warning text-white h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1"><?= $pending ?? 0 ?></h2>
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
                        <h2 class="fw-bold mb-1"><?= $completed ?? 0 ?></h2>
                        <h6 class="card-title text-uppercase small opacity-75 fw-semibold">Completed</h6>
                    </div>
                    <i class="fas fa-flag-checkered fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card filter-card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-filter me-2"></i>Filters
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="filterStatus" class="form-label">Status</label>
                            <select id="filterStatus" class="form-select" onchange="applyFilters()">
                                <option value="all">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filterSupplier" class="form-label">Supplier</label>
                            <select id="filterSupplier" class="form-select" onchange="applyFilters()">
                                <option value="all">All Suppliers</option>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?= esc($supplier['id_supplier']) ?>"><?= esc($supplier['nama_supplier']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filterDateFrom" class="form-label">Date From</label>
                            <input type="date" id="filterDateFrom" class="form-control" value="<?= date("Y-m-01"); ?>" onchange="applyFilters()">
                        </div>
                        <div class="col-md-3">
                            <label for="filterDateTo" class="form-label">Date To</label>
                            <input type="date" id="filterDateTo" class="form-control" value="<?= date("Y-m-t"); ?>" onchange="applyFilters()">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Data Table -->
    <div class="card table-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold m-0">Purchase Order Unit List</h5>
            <a href="<?= base_url('/purchasing/po-unitForm') ?>" class="btn btn-primary btn-action">
                <i class="fas fa-plus me-2"></i>New PO
            </a>
        </div>
        <div class="card-body">
            <table id="poUnitTable" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>PO Number</th>
                        <th>Date</th>
                        <th>Supplier</th>
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
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>PO Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="poDetailsContent">
                <!-- Content loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-edit-po" data-id-po="" onclick="editPO(this)">Edit PO</button>
                <button type="button" class="btn btn-info" id="btn-print-po" data-id-po="" onclick="printPO(this)">Print PO</button>
            </div>
        </div>
    </div>
</div>

<!-- Templates for Modal Content -->
<template id="content-po-detail">
    <div class="row">
        <div class="col-lg-12 text-end small text-muted">Created at : {created_at}<br />Updated at : {updated_at}</div>
        <div class="col-lg-6">
            <table class="table table-sm table-borderless">
                <tbody>
                    <tr><td style="width: 120px;"><strong>No PO</strong></td><td>: {no_po}</td></tr>
                    <tr><td><strong>Tanggal PO</strong></td><td>: {tanggal_po}</td></tr>
                    <tr><td><strong>Supplier</strong></td><td>: {supplier}</td></tr>
                    <tr><td><strong>Tipe PO</strong></td><td>: {tipe_po}</td></tr>
                </tbody>
            </table>
        </div>
        <div class="col-lg-6">
            <table class="table table-sm table-borderless">
                <tbody>
                    <tr><td style="width: 120px;"><strong>Invoice No</strong></td><td>: {invoice_no}</td></tr>
                    <tr><td><strong>Invoice Date</strong></td><td>: {invoice_date}</td></tr>
                    <tr><td><strong>BL Date</strong></td><td>: {bl_date}</td></tr>
                    <tr><td class="align-top"><strong>Keterangan</strong></td><td class="align-top">: {keterangan}</td></tr>
                </tbody>
            </table>
        </div>
        <div class="col-lg-12 mt-3">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" style="font-size:9pt;">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center align-middle" rowspan="2">No</th>
                            <th class="text-center align-middle" rowspan="2">Verifikasi</th>
                            <th class="text-center align-middle" colspan="5">Data Unit</th>
                            <th class="text-center align-middle" rowspan="2">Kapasitas</th>
                            <th class="text-center align-middle" colspan="4">Mesin</th>
                            <th class="text-center align-middle" colspan="4">Baterai</th>
                            <!-- <th class="text-center align-middle" colspan="3">Charger</th> -->
                            <th class="text-center align-middle" rowspan="2">SN Mast</th>
                            <th class="text-center align-middle" rowspan="2">Jenis Mast</th>
                            <th class="text-center align-middle" rowspan="2">Jenis Ban</th>
                            <th class="text-center align-middle" rowspan="2">Roda</th>
                            <!-- <th class="text-center align-middle" rowspan="2">SN Attachment</th>
                            <th class="text-center align-middle" rowspan="2">Attachment</th> -->
                            <th class="text-center align-middle" rowspan="2">Kondisi Penjualan</th>
                            <th class="text-center align-middle" rowspan="2">Valve</th>
                        </tr>
                        <tr>
                            <th class="text-center align-middle">S/N</th>
                            <th class="text-center align-middle">Merk</th>
                            <th class="text-center align-middle">Model</th>
                            <th class="text-center align-middle">Tipe</th>
                            <th class="text-center align-middle">Jenis</th>
                            <th class="text-center align-middle">S/N</th>
                            <th class="text-center align-middle">Merk</th>
                            <th class="text-center align-middle">Model</th>
                            <th class="text-center align-middle">Bahan Bakar</th>
                            <th class="text-center align-middle">S/N</th>
                            <th class="text-center align-middle">Merk</th>
                            <th class="text-center align-middle">Tipe</th>
                            <th class="text-center align-middle">Jenis</th>
                            <!-- <th class="text-center align-middle">S/N</th>
                            <th class="text-center align-middle">Merk</th>
                            <th class="text-center align-middle">Tipe</th> -->
                        </tr>
                    </thead>
                    <tbody id="tbody-po-detail">
                        <!-- Rows injected here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<template id="row-data-detail">
    <tr>
        <td class="align-middle text-center">{no}</td>
        <td class="align-middle text-center">{status_verifikasi}</td>
        <td class="align-middle text-center">{sn}</td>
        <td class="align-middle text-center">{merk_unit}</td>
        <td class="align-middle text-center">{model_unit}</td>
        <td class="align-middle text-center">{tipe_unit}</td>
        <td class="align-middle text-center">{jenis_unit}</td>
        <td class="align-middle text-center">{kapasitas}</td>
        <td class="align-middle text-center">{sn_mesin}</td>
        <td class="align-middle text-center">{merk_mesin}</td>
        <td class="align-middle text-center">{model_mesin}</td>
        <td class="align-middle text-center">{bahan_bakar}</td>
        <td class="align-middle text-center">{sn_baterai}</td>
        <td class="align-middle text-center">{merk_baterai}</td>
        <td class="align-middle text-center">{tipe_baterai}</td>
        <td class="align-middle text-center">{jenis_baterai}</td>
        <!-- <td class="align-middle text-center">{sn_charger}</td>
        <td class="align-middle text-center">{merk_charger}</td> -->
        <!-- <td class="align-middle text-center">{tipe_charger}</td> -->
        <td class="align-middle text-center">{sn_mast}</td>
        <td class="align-middle text-center">{jenis_mast}</td>
        <td class="align-middle text-center">{jenis_ban}</td>
        <td class="align-middle text-center">{roda}</td>
        <!-- <td class="align-middle text-center">{sn_attachment}</td> -->
        <!-- <td class="align-middle text-center">{aksesoris}</td> -->
        <td class="align-middle text-center">{kondisi_penjualan}</td>
        <td class="align-middle text-center">{valve}</td>
    </tr>
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
        poTable = $('#poUnitTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('/purchasing/api/get-data-po/unit') ?>', // Pastikan URL dan Route ini benar
                type: 'POST',
                data: function(d) {
                    d.status = $("#filterStatus").val();
                    d.supplier = $("#filterSupplier").val();
                    d.start_date = $("#filterDateFrom").val();
                    d.end_date = $("#filterDateTo").val();
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>'; // Menambahkan CSRF untuk keamanan
                    console.log(d);
                }
            },
            columns: [
                { data: 'no_po' },
                { data: 'tanggal_po' },
                { data: 'nama_supplier' },
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
                        const processed = parseInt(row.processed_items, 10);
                        const rejected = parseInt(row.rejected_items, 10);
                        
                        if (isNaN(total) || total === 0) {
                            return `<span class="text-muted small fst-italic">Tidak Ada Item</span>`;
                        }

                        const percentage = total > 0 ? Math.round((processed / total) * 100) : 0;
                        
                        let warningIcon = '';
                        if (rejected > 0) {
                            warningIcon = `<i class="fas fa-exclamation-triangle text-danger ms-2" title="${rejected} item tidak sesuai"></i>`;
                        }

                        return `
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1" title="${processed} dari ${total} item diproses">
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
                        let specialActionButtons = '';
                        if (row.status === 'Selesai dengan Catatan') {
                            specialActionButtons = `
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-primary" href="#" onclick="reverifyPO(${data})"><i class="fas fa-sync-alt me-2"></i>Verifikasi Ulang</a></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="cancelPO(${data})"><i class="fas fa-ban me-2"></i>Selesaikan (Batal)</a></li>
                            `;
                        }

                        return `
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#" onclick="viewPO(${data})"><i class="fas fa-eye me-2"></i>Lihat Detail</a></li>
                                    <li><a class="dropdown-item" href="<?= base_url('purchasing/edit-po-unit/') ?>${data}"><i class="fas fa-edit me-2"></i>Edit PO</a></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="deletePO(${data})"><i class="fas fa-trash me-2"></i>Hapus PO</a></li>
                                    ${specialActionButtons}
                                </ul>
                            </div>
                        `;
                    }
                }
            ],
            order: [[1, 'desc']],
            responsive: true,
            // PENTING: Menambahkan kembali drawCallback untuk statistik dinamis
            drawCallback: function(settings) {
                const api = this.api();
                const json = api.ajax.json();

                // Perbarui statistik card jika data ada di response JSON
                if (json && json.stats) {
                    const stats = json.stats;
                    $('#stat-total').text(stats.total ?? 0);
                    $('#stat-pending').text(stats.pending ?? 0);
                    $('#stat-catatan').text(stats.selesai_catatan ?? 0);
                    $('#stat-completed').text(stats.completed ?? 0);
                }
            }
        });
    });

    function applyFilters() {
        poTable.ajax.reload();
    }

    // --- FUNGSI BARU: Verifikasi Ulang ---
    function reverifyPO(id) {
        Swal.fire({
            title: 'Verifikasi Ulang PO?',
            text: "Status item yang 'Tidak Sesuai' akan diubah kembali menjadi 'Belum Dicek'. PO akan masuk kembali ke antrian verifikasi. Lanjutkan?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Lanjutkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '<?= base_url('/purchasing/reverify-po/') ?>' + id,
                    data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' }, // Kirim CSRF Token
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', 'PO telah dikembalikan ke antrian verifikasi.', 'success');
                            poTable.ajax.reload();
                        } else {
                            Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Gagal!', 'Tidak dapat terhubung ke server.', 'error');
                    }
                });
            }
        });
    }

    // --- FUNGSI BARU: Selesaikan (Batal) ---
    function cancelPO(id) {
        Swal.fire({
            title: 'Selesaikan dan Batalkan PO?',
            text: "Status PO ini akan diubah menjadi 'Cancelled'. Aksi ini tidak dapat dibatalkan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Batalkan PO!',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '<?= base_url('/purchasing/cancel-po/') ?>' + id,
                    data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' }, // Kirim CSRF Token
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Dibatalkan!', 'PO telah berhasil dibatalkan.', 'success');
                            poTable.ajax.reload();
                        } else {
                            Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Gagal!', 'Tidak dapat terhubung ke server.', 'error');
                    }
                });
            }
        });
    }

    function applyFilters() {
        poTable.ajax.reload();
    }

    function printPO(data) {
        const id = data.dataset.idPo;
        window.open("<?= base_url("purchasing/print-po-unit/") ?>"+id, "_blank");
    }

    function viewPO(id) {
        $("#btn-print-po").attr("data-id-po",id);
        $("#btn-edit-po").attr("data-id-po",id);
        $.ajax({
            type: "get",
            url: "<?= base_url("purchasing/api/po-unit/") ?>" + id,
            beforeSend: function () {
                $('#viewPOModal').modal('show');
                $("#poDetailsContent").html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-2x"></i><h5 class="mt-3">Loading...</h5></div>');
            },
            success: function (response) {
                let template = $('#content-po-detail').html();
                const po = response.po;
                template = template.replace('{no_po}', po.no_po || '-')
                    .replace('{tanggal_po}', po.tanggal_po || '-')
                    .replace('{supplier}', po.nama_supplier || '-')
                    .replace('{tipe_po}', po.tipe_po || '-')
                    .replace('{invoice_no}', po.invoice_no || '-')
                    .replace('{invoice_date}', po.invoice_date || '-')
                    .replace('{bl_date}', po.bl_date || '-')
                    .replace('{keterangan}', po.keterangan_po || '-')
                    .replace('{created_at}', po.created_at || '-')
                    .replace('{updated_at}', po.updated_at || '-');

                let rowTemplate = $('#row-data-detail').html();
                let rowHtml = '';
                response.details?.forEach((detail, index) => {
                    let row = rowTemplate
                        .replace('{no}', index + 1)
                        .replace('{sn}', detail?.serial_number_po ?? '-')
                        .replace('{merk_unit}', detail?.merk_unit ?? '-')
                        .replace('{model_unit}', detail?.model_unit ?? '-')
                        .replace('{tipe_unit}', detail?.nama_tipe_unit ?? '-')
                        .replace('{jenis_unit}', detail?.nama_departemen ?? '-')
                        .replace('{kapasitas}', detail?.kapasitas_unit ?? '-')
                        .replace('{sn_mesin}', detail?.sn_mesin_po ?? '-')
                        .replace('{merk_mesin}', detail?.merk_mesin ?? '-')
                        .replace('{model_mesin}', detail?.model_mesin ?? '-')
                        .replace('{bahan_bakar}', detail?.bahan_bakar ?? '-')
                        .replace('{sn_baterai}', detail?.sn_baterai_po ?? '-')
                        .replace('{merk_baterai}', detail?.merk_baterai ?? '-')
                        .replace('{tipe_baterai}', detail?.tipe_baterai ?? '-')
                        .replace('{jenis_baterai}', detail?.jenis_baterai ?? '-')
                        .replace('{sn_charger}', detail?.sn_charger_po ?? '-')
                        .replace('{merk_charger}', detail?.merk_charger ?? '-')
                        .replace('{tipe_charger}', detail?.tipe_charger ?? '-')
                        .replace('{sn_mast}', detail?.sn_mast_po ?? '-')
                        .replace('{jenis_mast}', detail?.tipe_mast ?? '-')
                        .replace('{jenis_ban}', detail?.tipe_ban ?? '-')
                        .replace('{roda}', detail?.tipe_roda ?? '-')
                        .replace('{sn_attachment}', detail?.sn_attachment_po ?? '-')
                        .replace('{aksesoris}', detail?.attachment ?? '-')
                        .replace('{kondisi_penjualan}', detail?.status_penjualan ?? '-')
                        .replace('{status_verifikasi}', (() => {
                            const status = detail?.status_verifikasi ?? '-';
                            if (status === 'Belum Dicek') return `<span class="badge bg-warning text-dark">Belum Dicek</span>`;
                            if (status === 'Sesuai') return `<span class="badge bg-success">Sesuai</span>`;
                            if (status === 'Tidak Sesuai') return `<span class="badge bg-danger">Tidak Sesuai</span>`;
                            return `<span class="badge bg-dark">${status}</span>`;
                        })())
                        .replace('{valve}', detail?.jumlah_valve ?? '-');
                    rowHtml += row;
                });

                $('#poDetailsContent').html(template);
                $('#poDetailsContent').find('#tbody-po-detail').html(rowHtml);
            },
            error: function () {
                $("#poDetailsContent").html('<div class="text-center text-danger p-5"><i class="fas fa-exclamation-triangle fa-2x"></i><h5 class="mt-3">Gagal memuat data.</h5></div>');
            }
        });
    }

    function editPO(data) {
        const id = data.dataset.idPo;
        window.location.href = `<?= base_url('/purchasing/edit-po-unit/') ?>${id}`;
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
                    url: '<?= base_url('/purchasing/delete-po-unit/') ?>' + id,
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
