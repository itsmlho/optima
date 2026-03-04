<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-2 px-md-4">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-transparent px-0 small">
            <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('warehouse/inventory/unit') ?>">Unit Inventory</a></li>
            <li class="breadcrumb-item active">Add Unit</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex align-items-center justify-content-between border-bottom py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-plus-circle me-2 text-primary"></i>Add New Unit</h5>
            <a href="<?= base_url('warehouse/inventory/unit') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
        <div class="card-body p-4">

            <?php if(session()->has('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                <?php foreach(session('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <form action="<?= base_url('warehouse/inventory/unit/store') ?>" method="POST">
            <?= csrf_field() ?>

            <!-- SECTION 1: Identification -->
            <div class="mb-4">
                <h6 class="fw-bold text-uppercase text-muted letter-spacing-1 border-bottom pb-2 mb-3">
                    <i class="fas fa-id-badge me-1"></i> Identification
                </h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Unit No. (Asset) <span class="text-muted small">(optional)</span></label>
                        <input type="text" name="no_unit" class="form-control" value="<?= esc(old('no_unit')) ?>" placeholder="e.g. SA-YYYY-XXXX">
                        <small class="text-muted">Official asset reference number.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Unit No. NA (Non-Asset)</label>
                        <input type="text" name="no_unit_na" class="form-control" value="<?= esc(old('no_unit_na')) ?>" placeholder="Leave blank to auto-generate">
                        <small class="text-muted">Leave empty to auto-generate.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Serial Number</label>
                        <input type="text" name="serial_number" class="form-control" value="<?= esc(old('serial_number')) ?>" placeholder="S/N unit...">
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Specifications -->
            <div class="mb-4">
                <h6 class="fw-bold text-uppercase text-muted letter-spacing-1 border-bottom pb-2 mb-3">
                    <i class="fas fa-cogs me-1"></i> Specifications
                </h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Unit Type</label>
                        <select name="tipe_unit_id" class="form-select">
                            <option value="">-- Select Type --</option>
                            <?php foreach($tipe_unit as $t): ?>
                            <option value="<?= $t['id_tipe_unit'] ?>"><?= esc($t['tipe'].' '.$t['jenis']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Unit Model</label>
                        <select name="model_unit_id" class="form-select">
                            <option value="">-- Select Model --</option>
                            <?php foreach($model_unit as $m): ?>
                            <option value="<?= $m['id_model_unit'] ?>"><?= esc($m['merk_unit'].' '.$m['model_unit']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Capacity</label>
                        <select name="kapasitas_unit_id" class="form-select">
                            <option value="">-- Select Capacity --</option>
                            <?php foreach($kapasitas_unit as $k): ?>
                            <option value="<?= $k['id_kapasitas'] ?>"><?= esc($k['kapasitas']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Mast Type</label>
                        <select name="model_mast_id" class="form-select">
                            <option value="">-- Select Mast --</option>
                            <?php foreach($tipe_mast as $tm): ?>
                            <option value="<?= $tm['id_mast'] ?>"><?= esc($tm['tipe_mast']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Mast Height (mm)</label>
                        <input type="number" name="tinggi_mast" class="form-control" value="<?= esc(old('tinggi_mast')) ?>" placeholder="e.g. 3000">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">S/N Mast</label>
                        <input type="text" name="sn_mast" class="form-control" value="<?= esc(old('sn_mast')) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Engine</label>
                        <select name="model_mesin_id" class="form-select">
                            <option value="">-- Select Engine --</option>
                            <?php foreach($mesin as $me): ?>
                            <option value="<?= $me['id'] ?>"><?= esc($me['merk_mesin'].' '.$me['model_mesin']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Engine S/N</label>
                        <input type="text" name="sn_mesin" class="form-control" value="<?= esc(old('sn_mesin')) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Wheel Type</label>
                        <select name="roda_id" class="form-select">
                            <option value="">-- Select Wheel --</option>
                            <?php foreach($roda as $r): ?>
                            <option value="<?= $r['id_roda'] ?>"><?= esc($r['nama_roda']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tyre Type</label>
                        <select name="ban_id" class="form-select">
                            <option value="">-- Select Tyre --</option>
                            <?php foreach($ban as $b): ?>
                            <option value="<?= $b['id_ban'] ?>"><?= esc($b['nama_ban']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Assignment & Status -->
            <div class="mb-4">
                <h6 class="fw-bold text-uppercase text-muted letter-spacing-1 border-bottom pb-2 mb-3">
                    <i class="fas fa-map-marker-alt me-1"></i> Assignment &amp; Status
                </h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Unit Status <span class="text-danger">*</span></label>
                        <select name="status_unit_id" class="form-select" required>
                            <option value="">-- Select Status --</option>
                            <?php foreach($status_unit as $s): ?>
                            <option value="<?= $s['id_status'] ?>"><?= esc($s['status_unit']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Department</label>
                        <select name="departemen_id" class="form-select">
                            <option value="">-- Select Department --</option>
                            <?php foreach($departemen as $d): ?>
                            <option value="<?= $d['id_departemen'] ?>"><?= esc($d['nama_departemen']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Unit Location</label>
                        <input type="text" name="lokasi_unit" class="form-control" value="<?= esc(old('lokasi_unit')) ?>" placeholder="Warehouse, Site, etc.">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Year of Make</label>
                        <input type="number" name="tahun_unit" class="form-control" value="<?= esc(old('tahun_unit', date('Y'))) ?>" min="2000" max="2099">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Delivery Date</label>
                        <input type="date" name="tanggal_kirim" class="form-control" value="<?= esc(old('tanggal_kirim')) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Additional notes..."><?= esc(old('keterangan')) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="<?= base_url('warehouse/inventory/unit') ?>" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-1"></i> Save Unit
                </button>
            </div>

            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<style>
.letter-spacing-1 { letter-spacing: .5px; }
</style>
