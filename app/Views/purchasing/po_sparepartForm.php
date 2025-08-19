<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<!-- Select2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin: -1.5rem -1.5rem 2rem -1.5rem;
        border-radius: 0 0 20px 20px;
    }
    .form-section {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 2rem;
    }
    .section-header {
        background: linear-gradient(135deg,rgb(249, 249, 249) 0%,rgb(248, 248, 248) 100%);
        color: black;
        padding: 1rem 1.5rem;
        border-radius: 10px 10px 0 0;
        margin-bottom: 0;
    }  
    .btn-action {
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .btn-action:hover {
        transform: scale(1.05);
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    .form-control:focus, .form-select:focus, .select2-container--bootstrap-5 .select2-selection--single:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    #sparepart-table tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center">
    <a href="<?= base_url('/purchasing/po-sparepart') ?>" class="btn btn-light btn-action">
        <i class="fas fa-arrow-left me-2"></i>Back to List
    </a>
</div>

<div class="container-fluid">
    <form action="<?= $mode === 'update' ? base_url('/purchasing/update-po-sparepart/' . ($po['id_po'] ?? '')) : base_url('/purchasing/store-po-sparepart') ?>" method="post" id="poSparepartForm">
        <?= csrf_field() ?>
        
        <!-- Basic Information Section -->
        <div class="form-section">
            <h5 class="section-header"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="no_po" class="form-label">PO Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="no_po" placeholder="Masukkan Nomor PO" value="<?= esc($po['no_po'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="tanggal_po" class="form-label">PO Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal_po" value="<?= esc($po['tanggal_po'] ?? date("Y-m-d")) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="id_supplier" class="form-label">Supplier <span class="text-danger">*</span></label>
                        <select name="id_supplier" class="form-select select2-basic" required>
                            <option value="">Select Supplier...</option>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?= $supplier['id_supplier'] ?>" <?= ($po['supplier_id'] ?? '') == $supplier['id_supplier'] ? 'selected' : '' ?>><?= esc($supplier['nama_supplier']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="keterangan_po" class="form-label">Keterangan PO <span class="text-secondary">(optional)</span></label>
                        <input type="text" class="form-control" name="keterangan_po" placeholder="Masukkan Keterangan PO" value="<?= esc($po['keterangan_po'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Sparepart Details Section -->
        <div class="form-section">
            <h5 class="section-header"><i class="fas fa-tools me-2"></i>Sparepart Items</h5>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table" id="sparepart-table">
                        <thead>
                            <tr>
                                <th style="width: 40%;">Sparepart</th>
                                <th style="width: 15%;">Quantity</th>
                                <th style="width: 15%;">Satuan</th>
                                <th style="width: 20%;">Keterangan</th>
                                <th style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($items)): ?>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td>
                                            <select class="form-select select2-sparepart" name="sparepart_id[]" required>
                                                <option value="">Pilih Sparepart...</option>
                                                <?php foreach ($spareparts as $sparepart): ?>
                                                    <option value="<?= $sparepart['id_sparepart'] ?>" <?= ($item['sparepart_id'] ?? '') == $sparepart['id_sparepart'] ? 'selected' : '' ?>><?= esc($sparepart['kode'] . ' - ' . $sparepart['desc_sparepart']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><input type="number" class="form-control" name="qty[]" min="1" value="<?= esc($item['qty'] ?? 1) ?>" required></td>
                                        <td>
                                            <select class="form-select" name="satuan[]" required>
                                                <?php $satuans = ['Pieces', 'Rol', 'Kaleng', 'Set', 'Pak', 'Meter', 'Unit', 'Jerigen', 'Lembar', 'Box', 'Pax', 'Drum', 'Batang', 'Pil', 'Dus', 'Kilogram', 'Botol', 'IBC Tank', 'Lusin', 'Liter', 'Lot']; ?>
                                                <?php foreach ($satuans as $satuan): ?>
                                                    <option value="<?= $satuan ?>" <?= ($item['satuan'] ?? '') == $satuan ? 'selected' : '' ?>><?= $satuan ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control" name="keterangan_item[]" placeholder="Catatan (opsional)" value="<?= esc($item['keterangan'] ?? '') ?>"></td>
                                        <td><button type="button" class="btn btn-danger btn-remove"><i class="fas fa-trash"></i></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <button type="button" id="add-sparepart-row" class="btn btn-outline-primary mt-3">
                    <i class="fas fa-plus me-2"></i>Tambah Sparepart
                </button>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="d-flex justify-content-end">
            <a href="<?= base_url('/purchasing/po-sparepart') ?>" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save PO Sparepart
            </button>
        </div>
    </form>
</div>

<!-- Template untuk baris baru sparepart (disembunyikan) -->
<template id="sparepart-row-template">
    <tr>
        <td>
            <select class="form-select select2-sparepart" name="sparepart_id[]" required>
                <option value="">Pilih Sparepart...</option>
                <?php foreach ($spareparts as $item): ?>
                    <option value="<?= $item['id_sparepart'] ?>"><?= esc($item['kode'] . ' - ' . $item['desc_sparepart']) ?></option>
                <?php endforeach; ?>
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
        <td><button type="button" class="btn btn-danger btn-remove"><i class="fas fa-trash"></i></button></td>
    </tr>
</template>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi Select2 untuk supplier
        $('.select2-basic').select2({ theme: "bootstrap-5" });
        // Inisialisasi Select2 untuk baris yang sudah ada saat edit
        $('.select2-sparepart').select2({ theme: "bootstrap-5", placeholder: 'Cari kode atau deskripsi...' });

        function addRow() {
            const template = document.getElementById('sparepart-row-template').innerHTML;
            const newRow = $(template);
            $('#sparepart-table tbody').append(newRow);
            newRow.find('.select2-sparepart').select2({ theme: "bootstrap-5", placeholder: 'Cari kode atau deskripsi...' });
        }

        // Jika mode create (tidak ada item), tambahkan satu baris kosong
        if ($('#sparepart-table tbody tr').length === 0) {
            addRow();
        }

        $('#add-sparepart-row').on('click', addRow);
        $('#sparepart-table tbody').on('click', '.btn-remove', function() { $(this).closest('tr').remove(); });
        $('#poSparepartForm').on('submit', function(e) {
            if ($('#sparepart-table tbody tr').length === 0) {
                e.preventDefault();
                OptimaPro.showNotification('Harap tambahkan setidaknya satu item sparepart.', 'warning');
            }
        });
    });
</script>
<?= $this->endSection() ?>
