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
    .filter-card { background: #f8f9fa; border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <!-- Statistics Cards (Now as Filters) -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-primary text-white h-100" onclick="applyCardFilter('')">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1"><?= $stats['total_jenis'] ?? 0 ?></h2>
                        <h6 class="card-title text-uppercase small opacity-75 fw-semibold">Semua Jenis Item</h6>
                    </div>
                    <i class="fas fa-tags fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-success text-white h-100" onclick="applyCardFilter('tersedia')">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1"><?= $stats['tersedia'] ?? 0 ?></h2>
                        <h6 class="card-title text-uppercase small opacity-75 fw-semibold">Tersedia</h6>
                    </div>
                    <i class="fas fa-check-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-warning text-white h-100" onclick="applyCardFilter('menipis')">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1"><?= $stats['stok_menipis'] ?? 0 ?></h2>
                        <h6 class="card-title text-uppercase small opacity-75 fw-semibold">Stok Menipis</h6>
                    </div>
                    <i class="fas fa-exclamation-triangle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-danger text-white h-100" onclick="applyCardFilter('kosong')">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1"><?= $stats['stok_kosong'] ?? 0 ?></h2>
                        <h6 class="card-title text-uppercase small opacity-75 fw-semibold">Stok Kosong</h6>
                    </div>
                    <i class="fas fa-times-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <!-- <div class="card filter-card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="filterStockStatus" class="form-label">Filter Status Stok</label>
                    <select id="filterStockStatus" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="tersedia">Tersedia</option>
                        <option value="menipis">Stok Menipis (<= 10)</option>
                        <option value="kosong">Stok Kosong</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filterLocation" class="form-label">Filter Lokasi</label>
                    <select id="filterLocation" class="form-select">
                        <option value="">Semua Lokasi</option>
                        <option value="POS 1">POS 1</option>
                        <option value="POS 2">POS 2</option>
                        <option value="POS 3">POS 3</option>
                        <option value="POS 4">POS 4</option>
                        <option value="POS 5">POS 5</option>
                    </select>
                </div>
            </div>
        </div>
    </div> -->


    <!-- Inventory Table -->
    <div class="card table-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold m-0">Daftar Stok Sparepart</h5>
        </div>
        <div class="card-body">
            <table id="inventory-sparepart-table" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode Sparepart</th>
                        <th>Deskripsi</th>
                        <th>Stok</th>
                        <th>Lokasi Rak</th>
                        <th>Terakhir Diperbarui</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Edit Stok -->
<div class="modal fade" id="editStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Stok Sparepart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editStockForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label class="form-label">Kode Sparepart</label>
                        <input type="text" class="form-control" id="edit_kode" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="edit_deskripsi" readonly rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="edit_stok" name="stok" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_lokasi" class="form-label">Lokasi Rak</label>
                        <input type="text" class="form-control" id="edit_lokasi" name="lokasi_rak">
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

<?= $this->section('javascript') ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentStockStatusFilter = '';

    $(document).ready(function() {
        const table = $('#inventory-sparepart-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('warehouse/inventory/invent_sparepart') ?>',
                type: 'POST',
                data: function(d) {
                    // Kirim data filter ke server
                    d.stock_status = currentStockStatusFilter;
                    d.lokasi_rak = $('#filterLocation').val();
                }
            },
            columns: [
                { data: 'id' },
                { data: 'kode' },
                { data: 'desc_sparepart' },
                { data: 'stok' },
                { data: 'lokasi_rak' },
                { data: 'updated_at' },
                {
                    data: 'id',
                    orderable: false,
                    render: function(data, type, row) {
                        return `<button class="btn btn-sm btn-warning" onclick="editStock(${data})"><i class="fas fa-edit"></i> Edit</button>`;
                    }
                }
            ],
            order: [[ 5, "desc" ]]
        });

        // Event listener untuk filter lokasi
        $('#filterLocation').on('change', function() {
            table.ajax.reload();
        });

        $('#editStockForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_id').val();
            $.ajax({
                url: `<?= base_url('warehouse/inventory/update_sparepart/') ?>${id}`,
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#editStockModal').modal('hide');
                        table.ajax.reload(null, false);
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
        // Hapus kelas 'active' dari semua kartu
        $('.card-stats').removeClass('active');
        // Tambahkan kelas 'active' ke kartu yang diklik
        if (status) {
            $(`.card-stats[onclick="applyCardFilter('${status}')"]`).addClass('active');
        } else {
             $(`.card-stats[onclick="applyCardFilter('')"]`).addClass('active');
        }
        
        currentStockStatusFilter = status;
        $('#inventory-sparepart-table').DataTable().ajax.reload();
    }

    function editStock(id) {
        $.ajax({
            url: `<?= base_url('warehouse/inventory/get_sparepart/') ?>${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#edit_id').val(data.id);
                    $('#edit_kode').val(data.kode);
                    $('#edit_deskripsi').val(data.desc_sparepart);
                    $('#edit_stok').val(data.stok);
                    $('#edit_lokasi').val(data.lokasi_rak);
                    $('#editStockModal').modal('show');
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            }
        });
    }
</script>
<?= $this->endSection() ?>
