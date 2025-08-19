<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .table-card { border: none; border-radius: 15px; box-shadow: 0 4px 25px rgba(0,0,0,0.1); }
    .card-stats { 
        border: none; 
        border-radius: 15px; 
        box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .card-stats:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.15);
    }
    .card-stats.active {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        border: 2px solid #0d6efd;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <!-- Statistics Cards (Filters) -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-primary text-white h-100 active" onclick="applyCardFilter('')">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1" id="stat-total"><?= $stats['total'] ?? 0 ?></h2>
                        <h6 class="card-title text-uppercase small opacity-75 fw-semibold">Semua Attachment</h6>
                    </div>
                    <i class="fas fa-puzzle-piece fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-success text-white h-100" onclick="applyCardFilter('7')">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1" id="stat-instock"><?= $stats['in_stock'] ?? 0 ?></h2>
                        <h6 class="card-title text-uppercase small opacity-75 fw-semibold">In Stock</h6>
                    </div>
                    <i class="fas fa-check-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-info text-white h-100" onclick="applyCardFilter('3')">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1" id="stat-rented"><?= $stats['rented'] ?? 0 ?></h2>
                        <h6 class="card-title text-uppercase small opacity-75 fw-semibold">Disewakan</h6>
                    </div>
                    <i class="fas fa-handshake fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-danger text-white h-100" onclick="applyCardFilter('9')">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1" id="stat-sold"><?= $stats['sold'] ?? 0 ?></h2>
                        <h6 class="card-title text-uppercase small opacity-75 fw-semibold">Terjual</h6>
                    </div>
                    <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="card table-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold m-0">Daftar Stok Attachment</h5>
        </div>
        <div class="card-body">
            <table id="inventory-attachment-table" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>SN Attachment</th>
                        <th>SN Charger</th>
                        <th>Kondisi Fisik</th>
                        <th>Kelengkapan</th>
                        <th>Status</th>
                        <th>Lokasi</th>
                        <th>Tanggal Masuk</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal View Attachment Detail -->
<div class="modal fade" id="viewAttachmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Detail Attachment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="attachmentDetailContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Stok Attachment -->
<div class="modal fade" id="editAttachmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Stok Attachment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAttachmentForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id_inventory_attachment">
                    <div class="mb-3">
                        <label class="form-label">SN Attachment</label>
                        <input type="text" class="form-control" id="edit_sn_attachment" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">SN Charger</label>
                        <input type="text" class="form-control" id="edit_sn_charger" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status_unit" class="form-label">Status</label>
                        <select class="form-select" id="edit_status_unit" name="status_unit" required>
                            <option value="7">STOCK ASET</option>
                            <option value="3">RENTAL</option>
                            <option value="9">JUAL</option>
                            <option value="2">WORKSHOP-RUSAK</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_lokasi" class="form-label">Lokasi Penyimpanan</label>
                        <select class="form-select" id="edit_lokasi" name="lokasi_penyimpanan">
                            <option value="POS 1">POS 1</option>
                            <option value="POS 2">POS 2</option>
                            <option value="POS 3">POS 3</option>
                            <option value="POS 4">POS 4</option>
                            <option value="POS 5">POS 5</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_kondisi" class="form-label">Kondisi Fisik</label>
                        <select class="form-select" id="edit_kondisi" name="kondisi_fisik">
                            <option value="Baik">Baik</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_kelengkapan" class="form-label">Kelengkapan</label>
                        <select class="form-select" id="edit_kelengkapan" name="kelengkapan">
                            <option value="Lengkap">Lengkap</option>
                            <option value="Tidak Lengkap">Tidak Lengkap</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentStatusFilter = '';
    let attachmentTable;

    $(document).ready(function() {
        attachmentTable = $('#inventory-attachment-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('warehouse/inventory/invent_attachment') ?>',
                type: 'POST',
                data: function(d) {
                    d.status_unit = currentStatusFilter;
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                },
                error: function(xhr, error, thrown) {
                    console.log('DataTables Ajax Error:');
                    console.log('XHR:', xhr);
                    console.log('Error:', error);
                    console.log('Thrown:', thrown);
                    console.log('Response Text:', xhr.responseText);
                    
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat memuat data. Silakan periksa console untuk detail.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            columns: [
                { 
                    data: 'id_inventory_attachment',
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                { 
                    data: 'sn_attachment',
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                { 
                    data: 'sn_charger',
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                { 
                    data: 'kondisi_fisik',
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                { 
                    data: 'kelengkapan',
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                { 
                    data: 'status_unit_name',
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                { 
                    data: 'lokasi_penyimpanan',
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                { 
                    data: 'tanggal_masuk',
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                {
                    data: 'id_inventory_attachment',
                    orderable: false,
                    render: function(data) {
                        return `
                            <div class=\"dropdown\">
                                <button class=\"btn btn-sm btn-outline-secondary dropdown-toggle\" type=\"button\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                                    <i class=\"fas fa-ellipsis-h\"></i>
                                </button>
                                <ul class=\"dropdown-menu dropdown-menu-end\">
                                    <li><a class=\"dropdown-item\" href=\"#\" onclick=\"viewAttachment(${data})\"><i class=\"fas fa-eye me-2\"></i>Lihat Detail</a></li>
                                    <li><a class=\"dropdown-item\" href=\"#\" onclick=\"editAttachment(${data})\"><i class=\"fas fa-edit me-2\"></i>Edit</a></li>
                                </ul>
                            </div>`;
                    }
                }
            ],
            order: [[ 8, "desc" ]]
        });

        $('#editAttachmentForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_id').val();
            $.ajax({
                url: `<?= base_url('warehouse/inventory/update-attachment/') ?>${id}`,
                type: 'POST',
                data: $(this).serialize() + '&<?= csrf_token() ?>=<?= csrf_hash() ?>',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#editAttachmentModal').modal('hide');
                        attachmentTable.ajax.reload(null, false);
                        Swal.fire('Berhasil!', response.message, 'success');
                    } else {
                        Swal.fire('Gagal!', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Tidak dapat terhubung ke server.', 'error');
                }
            });
        });
    });

    function applyCardFilter(status) {
        $('.card-stats').removeClass('active');
        $(`.card-stats[onclick="applyCardFilter('${status}')"]`).addClass('active');
        
        currentStatusFilter = status;
        attachmentTable.ajax.reload();
    }

    function viewAttachment(id) {
        console.log('viewAttachment called for ID:', id);
        
        $.ajax({
            url: `<?= base_url('warehouse/inventory/get-attachment-detail/') ?>${id}`,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('#attachmentDetailContent').html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><br><br>Memuat detail attachment...</div>');
                $('#viewAttachmentModal').modal('show');
            },
            success: function(response) {
                console.log('AJAX Success Response:', response);
                
                if (response.success) {
                    const data = response.data;
                    const detailHtml = createAttachmentDetailHtml(data);
                    $('#attachmentDetailContent').html(detailHtml);
                } else {
                    const errorHtml = `
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle"></i> Gagal Memuat Detail</h5>
                            <p>${response.message || 'Terjadi kesalahan tidak diketahui'}</p>
                        </div>
                    `;
                    $('#attachmentDetailContent').html(errorHtml);
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', {xhr, status, error});
                console.log('Response Text:', xhr.responseText);
                
                let errorMessage = 'Terjadi kesalahan saat memuat detail attachment.';
                
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMessage = errorResponse.message;
                    }
                } catch (e) {
                    errorMessage += ' (Server Error ' + xhr.status + ')';
                }
                
                const errorHtml = `
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-triangle"></i> Error ${xhr.status}</h5>
                        <p>${errorMessage}</p>
                        <details class="mt-2">
                            <summary>Technical Details</summary>
                            <pre class="mt-2 text-muted small">${xhr.responseText}</pre>
                        </details>
                    </div>
                `;
                $('#attachmentDetailContent').html(errorHtml);
            }
        });
    }

    function createAttachmentDetailHtml(data) {
        const h = (str) => {
            if (str === null || str === undefined || str === '') {
                return '-';
            }
            return String(str).replace(/</g, '&lt;').replace(/>/g, '&gt;');
        };
        
        console.log('Creating detail HTML for data:', data);
        
        return `
            <div class="row">
                <!-- Basic Attachment Information -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-puzzle-piece me-2"></i>Informasi Attachment</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr><td width="40%"><strong>ID Attachment</strong></td><td>: ${h(data.id_inventory_attachment)}</td></tr>
                                <tr><td><strong>SN Attachment</strong></td><td>: ${h(data.sn_attachment)}</td></tr>
                                <tr><td><strong>SN Charger</strong></td><td>: ${h(data.sn_charger)}</td></tr>
                                <tr><td><strong>Status</strong></td><td>: <span class="badge bg-info">${h(data.status_unit_name)}</span></td></tr>
                                <tr><td><strong>Lokasi Penyimpanan</strong></td><td>: ${h(data.lokasi_penyimpanan)}</td></tr>
                                <tr><td><strong>Kondisi Fisik</strong></td><td>: <span class="badge ${data.kondisi_fisik === 'Baik' ? 'bg-success' : data.kondisi_fisik === 'Rusak Berat' ? 'bg-danger' : 'bg-warning'}">${h(data.kondisi_fisik)}</span></td></tr>
                                <tr><td><strong>Kelengkapan</strong></td><td>: <span class="badge ${data.kelengkapan === 'Lengkap' ? 'bg-success' : 'bg-warning'}">${h(data.kelengkapan)}</span></td></tr>
                                <tr><td><strong>Tanggal Masuk</strong></td><td>: ${h(data.tanggal_masuk)}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Purchase Order Information -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Informasi PO</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr><td width="40%"><strong>No. PO</strong></td><td>: ${h(data.no_po) || 'Manual Entry'}</td></tr>
                                <tr><td><strong>Tanggal PO</strong></td><td>: ${h(data.tanggal_po) || '-'}</td></tr>
                                <tr><td><strong>Supplier</strong></td><td>: ${h(data.nama_supplier) || '-'}</td></tr>
                                <tr><td><strong>Status PO</strong></td><td>: <span class="badge bg-secondary">${h(data.status) || '-'}</span></td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Tambahan</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Created At:</strong> ${h(data.created_at)}</p>
                                    <p><strong>Updated At:</strong> ${h(data.updated_at)}</p>
                                </div>
                                <div class="col-md-6">
                                    ${data.catatan_inventory ? `
                                    <p><strong>Catatan Inventory:</strong></p>
                                    <p class="text-muted">${h(data.catatan_inventory)}</p>
                                    ` : '<p class="text-muted">Tidak ada catatan tambahan</p>'}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function editAttachment(id) {
        $.ajax({
            url: `<?= base_url('warehouse/inventory/get-attachment-detail/') ?>${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#edit_id').val(data.id_inventory_attachment);
                    $('#edit_sn_attachment').val(data.sn_attachment);
                    $('#edit_sn_charger').val(data.sn_charger);
                    $('#edit_status_unit').val(data.status_unit);
                    $('#edit_lokasi').val(data.lokasi_penyimpanan);
                    $('#edit_kondisi').val(data.kondisi_fisik);
                    $('#edit_kelengkapan').val(data.kelengkapan);
                    $('#editAttachmentModal').modal('show');
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            }
        });
    }
</script>
<?= $this->endSection() ?>
