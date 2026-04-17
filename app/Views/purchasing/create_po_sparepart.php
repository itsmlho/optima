<?= $this->extend('layouts/base') ?>


<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('/purchasing') ?>"><i class="fas fa-shopping-cart me-1"></i>Purchasing</a></li>
                <li class="breadcrumb-item active">Buat PO Sparepart</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-tools me-2 text-primary"></i>Buat Purchase Order - Sparepart
        </h4>
        <p class="text-muted small mb-0">Input Purchase Order untuk pembelian sparepart</p>
    </div>
    <a href="<?= base_url('/purchasing') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Kembali
    </a>
</div>

<div class="container-fluid">
    <form action="<?= base_url('/purchasing/store-po-sparepart-unified') ?>" method="post" id="poSparepartForm">
        <?= csrf_field() ?>
        
        <!-- Basic Information Section -->
        <div class="form-section">
            <h5 class="section-header"><i class="fas fa-info-circle me-2"></i>Informasi Purchase Order</h5>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="no_po" class="form-label">Nomor PO <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="no_po" id="no_po" placeholder="Contoh: PO-SP-2025-001" required>
                    </div>
                    <div class="col-md-4">
                        <label for="tanggal_po" class="form-label">Tanggal PO <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal_po" id="tanggal_po" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="id_supplier" class="form-label">Supplier <span class="text-danger">*</span></label>
                        <select name="id_supplier" id="id_supplier" class="form-select select2-supplier" required>
                            <option value="">Pilih Supplier...</option>
                            <?php if (isset($suppliers) && is_array($suppliers)): ?>
                                <?php foreach ($suppliers as $item): ?>
                                    <option value="<?= $item['id_supplier'] ?>">
                                        [<?= esc($item['kode_supplier'] ?? '-') ?>] <?= esc($item['nama_supplier']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="invoice_no" class="form-label">Invoice Number</label>
                        <input type="text" class="form-control" name="invoice_no" id="invoice_no" placeholder="Optional">
                    </div>
                    <div class="col-md-4">
                        <label for="invoice_date" class="form-label">Invoice Date</label>
                        <input type="date" class="form-control" name="invoice_date" id="invoice_date">
                    </div>
                    <div class="col-md-4">
                        <label for="bl_date" class="form-label">BL Date</label>
                        <input type="date" class="form-control" name="bl_date" id="bl_date">
                    </div>
                    <div class="col-12">
                        <label for="keterangan_po" class="form-label">Keterangan PO</label>
                        <textarea class="form-control" name="keterangan_po" id="keterangan_po" rows="2" placeholder="Catatan tambahan (optional)"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sparepart Details Section -->
        <div class="form-section">
            <h5 class="section-header"><i class="fas fa-tools me-2"></i>Daftar Sparepart</h5>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="sparepart-table">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40%;">Sparepart</th>
                                <th style="width: 15%;">Quantity</th>
                                <th style="width: 15%;">Satuan</th>
                                <th style="width: 20%;">Keterangan</th>
                                <th style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Rows will be added dynamically -->
                        </tbody>
                    </table>
                </div>
                <button type="button" id="add-sparepart-row" class="btn btn-outline-primary mt-3">
                    <i class="fas fa-plus me-2"></i>Tambah Sparepart
                </button>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="<?= base_url('/purchasing') ?>" class="btn btn-secondary">
                <i class="fas fa-times me-2"></i>Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Simpan PO Sparepart
            </button>
        </div>
    </form>
</div>

<!-- Template untuk baris baru sparepart (disembunyikan) -->
<!-- sparepart options: REMOVED forEach - now loaded via Select2 AJAX -->
<template id="sparepart-row-template">
    <tr>
        <td>
            <select class="form-select select2-sparepart" name="sparepart_id[]" required>
                <option value="">Pilih Sparepart...</option>
            </select>
        </td>
        <td><input type="number" class="form-control" name="qty[]" min="1" value="1" required></td>
        <td>
            <select class="form-select" name="satuan[]" required>
                <option value="Pieces" selected>Pieces</option>
                <option value="Rol">Rol</option>
                <option value="Kaleng">Kaleng</option>
                <option value="Set">Set</option>
                <option value="Pak">Pak</option>
                <option value="Meter">Meter</option>
                <option value="Unit">Unit</option>
                <option value="Jerigen">Jerigen</option>
                <option value="Lembar">Lembar</option>
                <option value="Box">Box</option>
                <option value="Pax">Pax</option>
                <option value="Drum">Drum</option>
                <option value="Batang">Batang</option>
                <option value="Pil">Pil</option>
                <option value="Dus">Dus</option>
                <option value="Kilogram">Kilogram</option>
                <option value="Botol">Botol</option>
                <option value="IBC Tank">IBC Tank</option>
                <option value="Lusin">Lusin</option>
                <option value="Liter">Liter</option>
                <option value="Lot">Lot</option>
            </select>
        </td>
        <td><input type="text" class="form-control" name="keterangan_item[]" placeholder="Catatan (opsional)"></td>
        <td><button type="button" class="btn btn-danger btn-sm btn-remove" aria-label="Hapus baris sparepart"><i class="fas fa-trash" aria-hidden="true"></i></button></td>
    </tr>
</template>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi Select2 untuk supplier dengan search
        $('.select2-supplier').select2({ 
            theme: "bootstrap-5",
            width: '100%',
            placeholder: 'Cari supplier berdasarkan kode atau nama...',
            allowClear: true,
            minimumInputLength: 0 // Allow search from first character
        });

        function addRow() {
            const template = document.getElementById('sparepart-row-template').innerHTML;
            const newRow = $(template);
            $('#sparepart-table tbody').append(newRow);
            
            // Initialize Select2 AJAX for newly added row (avoids loading 14k+ items)
            newRow.find('.select2-sparepart').select2({
                theme: 'bootstrap-5',
                placeholder: 'Cari kode atau deskripsi...',
                width: '100%',
                minimumInputLength: 2,
                ajax: {
                    url: (typeof BASE_URL !== 'undefined' ? BASE_URL : '<?= base_url() ?>') + 'purchasing/api/search-spareparts',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return { q: params.term, page: params.page || 1 };
                    },
                    processResults: function(data) {
                        return { results: data.results, pagination: data.pagination };
                    }
                }
            });
        }

        // Add first row on page load
        addRow();

        // Add row button handler
        $('#add-sparepart-row').on('click', addRow);
        
        // Remove row button handler
        $('#sparepart-table tbody').on('click', '.btn-remove', function() {
            $(this).closest('tr').remove();
        });
        
        // Form validation on submit
        $('#poSparepartForm').on('submit', function(e) {
            if ($('#sparepart-table tbody tr').length === 0) {
                e.preventDefault();
                OptimaPro.showNotification('Harap tambahkan setidaknya satu item sparepart.', 'warning');
                return false;
            }
        });

        // Initialize DataTable for sorting and search functionality
        $('#sparepart-table').DataTable({
            processing: true,
            pageLength: 25,
            order: [[1, 'asc']], // Sort by sparepart name
            columnDefs: [
                { orderable: false, targets: [-1] } // Disable sorting on last column (actions)
            ]
        });
    });
</script>
<?= $this->endSection() ?>

