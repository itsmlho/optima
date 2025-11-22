<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-3">
    <h5 class="mb-3"><i class="fas fa-cog me-2"></i>Page Title</h5>
    
    
<?php
// Load global permission helper
helper('global_permission');

// Get permissions for marketing module
$permissions = get_global_permission('marketing');
$can_view = $permissions['view'];
$can_create = $permissions['create'];
$can_edit = $permissions['edit'];
$can_delete = $permissions['delete'];
$can_export = $permissions['export'];
?>

<?php if (can_export('marketing')): ?>
                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                    <i class="fas fa-download me-1"></i>Export
                </button>
                <?php else: ?>
                <button class="btn btn-outline-secondary btn-sm disabled" onclick="return false;" title="Access Denied">
                    <i class="fas fa-download me-1"></i>Export
                </button>
                <?php endif; ?>
                <?php if (can_create('marketing')): ?>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addQuotationModal">
                    <i class="fas fa-plus me-1"></i>Buat Penawaran
                </button>
                <?php else: ?>
                <button class="btn btn-primary btn-sm disabled" onclick="return false;" title="Access Denied">
                    <i class="fas fa-plus me-1"></i>Buat Penawaran
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Penawaran</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">42</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="text-xs text-success mt-2">
                        <i class="fas fa-arrow-up me-1"></i>+8 penawaran baru
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Penawaran Diterima</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">15</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-double fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="text-xs text-success mt-2">
                        <i class="fas fa-arrow-up me-1"></i>36% tingkat konversi
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Menunggu Respon</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">12</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="text-xs text-muted mt-2">
                        <i class="fas fa-clock me-1"></i>Rata-rata 5 hari
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Nilai Penawaran</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp 1.8M</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="text-xs text-success mt-2">
                        <i class="fas fa-arrow-up me-1"></i>+18% dari bulan lalu
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quotation Management Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Penawaran</h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#filterSection">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <button class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-search me-1"></i>Cari
                    </button>
                </div>
            </div>
            
            <!-- Filter Section -->
            <div class="collapse" id="filterSection">
                <div class="border-top pt-3 mt-3">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small">Status</label>
                            <select class="form-select form-select-sm">
                                <option value="">Semua Status</option>
                                <option value="draft">Draft</option>
                                <option value="sent">Terkirim</option>
                                <option value="accepted">Diterima</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Jenis Layanan</label>
                            <select class="form-select form-select-sm">
                                <option value="">Semua Layanan</option>
                                <option value="rental">Rental Equipment</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="service">Service</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Periode</label>
                            <select class="form-select form-select-sm">
                                <option value="">Semua Periode</option>
                                <option value="this_week">Minggu Ini</option>
                                <option value="this_month">Bulan Ini</option>
                                <option value="this_quarter">Kuartal Ini</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-secondary btn-sm w-100">
                                <i class="fas fa-sync me-1"></i>Reset Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (!$can_view): ?>
        <div class="alert alert-warning m-3">
            <i class="fas fa-lock me-2"></i>
            <strong>Access Denied:</strong> You do not have permission to view penawaran. 
            Please contact your administrator to request access.
        </div>
        <?php endif; ?>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered <?= !$can_view ? 'table-disabled' : '' ?>" id="quotationsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No. Penawaran</th>
                            <th>Klien</th>
                            <th>Layanan</th>
                            <th>Nilai</th>
                            <th>Tanggal Dibuat</th>
                            <th>Valid Hingga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($quotations) && is_array($quotations)): ?>
                            <?php foreach ($quotations as $quotation): ?>
                                <tr>
                                    <td><strong><?= esc($quotation['id']) ?></strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 12px;">
                                                <?= strtoupper(substr($quotation['client'], 0, 2)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-semibold"><?= esc($quotation['client']) ?></div>
                                                <div class="small text-muted"><?= esc($quotation['project']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= esc($quotation['units_requested']) ?></span>
                                    </td>
                                    <td><strong>Rp <?= number_format($quotation['value'], 0, ',', '.') ?></strong></td>
                                    <td><?= date('d M Y', strtotime($quotation['created_at'])) ?></td>
                                    <td><?= date('d M Y', strtotime($quotation['valid_until'])) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $quotation['status'] == 'Approved' ? 'success' : ($quotation['status'] == 'Pending' ? 'warning' : 'secondary') ?>">
                                            <?= esc($quotation['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-info" data-bs-toggle="tooltip" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Download PDF">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data penawaran</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Quotation Modal -->
<div class="modal fade" id="addQuotationModal" tabindex="-1" aria-labelledby="addQuotationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addQuotationModalLabel">Buat Penawaran Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addQuotationForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="clientName" class="form-label">Nama Klien</label>
                                <input type="text" class="form-control" id="clientName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="projectName" class="form-label">Nama Proyek</label>
                                <input type="text" class="form-control" id="projectName" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="serviceType" class="form-label">Jenis Layanan</label>
                                <select class="form-select" id="serviceType" required>
                                    <option value="">Pilih Layanan</option>
                                    <option value="rental">Rental Equipment</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="service">Service</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="validUntil" class="form-label">Valid Hingga</label>
                                <input type="date" class="form-control" id="validUntil" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="unitsRequested" class="form-label">Unit yang Diminta</label>
                        <textarea class="form-control" id="unitsRequested" rows="3" placeholder="Deskripsikan unit yang diminta klien"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="quotationValue" class="form-label">Nilai Penawaran (Rp)</label>
                        <input type="number" class="form-control" id="quotationValue" required>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan</label>
                        <textarea class="form-control" id="notes" rows="3" placeholder="Catatan tambahan untuk penawaran"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveQuotation()">Simpan Penawaran</button>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Data Penawaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="exportFormat" class="form-label">Format Export</label>
                    <select class="form-select" id="exportFormat">
                        <option value="excel">Excel (.xlsx)</option>
                        <option value="pdf">PDF</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="exportPeriod" class="form-label">Periode</label>
                    <select class="form-select" id="exportPeriod">
                        <option value="all">Semua Data</option>
                        <option value="this_month">Bulan Ini</option>
                        <option value="this_quarter">Kuartal Ini</option>
                        <option value="this_year">Tahun Ini</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="exportData()">Export</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    $('#quotationsTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
        }
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function refreshData() {
    location.reload();
}

function saveQuotation() {
    // Add save quotation logic here
    OptimaPro.showNotification('Penawaran berhasil disimpan!', 'success');
    $('#addQuotationModal').modal('hide');
}

function exportData() {
    const format = document.getElementById('exportFormat').value;
    const period = document.getElementById('exportPeriod').value;
    
    // Add export logic here
    OptimaPro.showNotification(`Export ${format} untuk periode ${period} berhasil!`, 'success');
    $('#exportModal').modal('hide');
}
</script>
</div>
<?= $this->endSection() ?>
