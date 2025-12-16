<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<!-- Stats Cards -->

<div class="row mt-3 mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-primary-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-building stat-icon text-primary"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-total-supplier"><?= $supplierStats['total'] ?? 0 ?></div>
                    <div class="text-muted">Total Supplier</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-success-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-check-circle stat-icon text-success"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-active-supplier"><?= $supplierStats['active'] ?? 0 ?></div>
                    <div class="text-muted">Active</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-info-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-shield-check stat-icon text-info"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-verified-supplier"><?= $supplierStats['verified'] ?? 0 ?></div>
                    <div class="text-muted">Verified</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-warning-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-receipt stat-icon text-warning"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-total-po"><?= $supplierStats['total_po'] ?? 0 ?></div>
                    <div class="text-muted">Total PO</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Button -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0"> </h4>
            <button type="button" class="btn btn-primary btn-lg" onclick="openAddSupplierModal()">
                <i class="fas fa-plus me-2"></i>Tambah Supplier
            </button>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card table-card">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-building me-2"></i>Daftar Supplier
            </h5>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-warning" onclick="refreshTableData()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
                <a href="<?= base_url('purchasing/export_supplier') ?>" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-file-excel me-1"></i>Export Excel
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="supplierTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Supplier</th>
                        <th>Nama Supplier</th>
                        <th>Business Type</th>
                        <th>Kontak</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($suppliers) && is_array($suppliers)): ?>
                        <?php foreach ($suppliers as $index => $supplier): ?>
                            <tr style="cursor: pointer;" onclick="viewSupplierDetail(<?= $supplier['id_supplier'] ?>)">
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <span class="badge bg-primary"><?= esc($supplier['kode_supplier'] ?? '-') ?></span>
                                </td>
                                <td><strong><?= esc($supplier['nama_supplier']) ?></strong></td>
                                <td><?= esc($supplier['business_type'] ?? '-') ?></td>
                                <td>
                                    <?php if (!empty($supplier['phone'])): ?>
                                        <i class="fas fa-phone me-1"></i><?= esc($supplier['phone']) ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($supplier['email'])): ?>
                                        <i class="fas fa-envelope me-1"></i><?= esc($supplier['email']) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $status = $supplier['status'] ?? 'Inactive';
                                    $badgeClass = $status === 'Active' ? 'bg-success' : 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Supplier Detail Modal -->
<div class="modal fade" id="supplierDetailModal" tabindex="-1" aria-labelledby="supplierDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-muted">
                <h5 class="modal-title" id="supplierDetailModalLabel">
                    <i class="fas fa-building me-2"></i>Detail Supplier
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="supplierDetailContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-info" onclick="changeSupplierStatus()">
                    <i class="fas fa-exchange-alt me-2"></i>Ubah Status
                </button>
                <button type="button" class="btn btn-warning" onclick="editSupplierFromModal()">
                    <i class="fas fa-edit me-2"></i>Edit
                </button>
                <button type="button" class="btn btn-danger" onclick="deleteSupplierFromModal()">
                    <i class="fas fa-trash me-2"></i>Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Supplier Modal -->
<div class="modal fade" id="supplierFormModal" tabindex="-1" aria-labelledby="supplierFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="supplierFormModalLabel">
                    <i class="fas fa-building me-2"></i>Tambah Supplier
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="supplierForm">
                <div class="modal-body">
                    <input type="hidden" id="supplier_id" name="supplier_id">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="kode_supplier" class="form-label">Kode Supplier</label>
                            <input type="text" class="form-control" id="kode_supplier" name="kode_supplier" readonly style="background-color: #f8f9fa;">
                            <div class="form-text">Kode supplier akan dibuat otomatis</div>
                        </div>
                        <div class="col-md-6">
                            <label for="nama_supplier" class="form-label">Nama Supplier <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_supplier" name="nama_supplier" required>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="business_type" class="form-label">Business Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="business_type" name="business_type" required>
                                <option value="">Pilih Business Type...</option>
                                <option value="Distributor">Distributor</option>
                                <option value="Manufacturer">Manufacturer</option>
                                <option value="Wholesaler">Wholesaler</option>
                                <option value="Retailer">Retailer</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="contact_person" class="form-label">Contact Person</label>
                            <input type="text" class="form-control" id="contact_person" name="contact_person">
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control" id="website" name="website" placeholder="https://www.example.com">
                            <div class="form-text">Contoh: https://www.example.com</div>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-12">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Status Modal -->
<div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="changeStatusModalLabel">
                    <i class="fas fa-exchange-alt me-2"></i>Ubah Status Supplier
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="new_status" class="form-label">Status Baru</label>
                    <select class="form-select" id="new_status" name="new_status">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                        <option value="Blacklisted">Blacklisted</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="status_reason" class="form-label">Alasan Perubahan</label>
                    <textarea class="form-control" id="status_reason" name="status_reason" rows="3" placeholder="Masukkan alasan perubahan status..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-info" onclick="saveStatusChange()">
                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let currentSupplierId = null;

$(document).ready(function() {
    // Form submission
    $('#supplierForm').on('submit', function(e) {
        e.preventDefault();
        saveSupplier();
    });
});

// View supplier detail
function viewSupplierDetail(id) {
    currentSupplierId = id;
    
    $.ajax({
        url: '<?= base_url('purchasing/get-supplier') ?>/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const supplier = response.data;
                renderSupplierDetail(supplier);
                $('#supplierDetailModal').modal('show');
            } else {
                Swal.fire('Error!', response.message || 'Gagal mengambil data supplier', 'error');
            }
        },
        error: function() {
            Swal.fire('Error!', 'Terjadi kesalahan saat mengambil data supplier', 'error');
        }
    });
}

// Render supplier detail content
function renderSupplierDetail(supplier) {
    const statusBadge = supplier.status === 'Active' ? 
        '<span class="badge bg-success">Active</span>' : 
        '<span class="badge bg-secondary">Inactive</span>';
    
    const html = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary mb-3">Informasi Dasar</h6>
                <table class="table table-borderless">
                    <tr><td><strong>Kode Supplier:</strong></td><td>${supplier.kode_supplier || '-'}</td></tr>
                    <tr><td><strong>Nama Supplier:</strong></td><td>${supplier.nama_supplier || '-'}</td></tr>
                    <tr><td><strong>Alias:</strong></td><td>${supplier.alias || '-'}</td></tr>
                    <tr><td><strong>Business Type:</strong></td><td>${supplier.business_type || '-'}</td></tr>
                    <tr><td><strong>Status:</strong></td><td>${statusBadge}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary mb-3">Kontak</h6>
                <table class="table table-borderless">
                    <tr><td><strong>Contact Person:</strong></td><td>${supplier.contact_person || '-'}</td></tr>
                    <tr><td><strong>Phone:</strong></td><td>${supplier.phone || '-'}</td></tr>
                    <tr><td><strong>Email:</strong></td><td>${supplier.email || '-'}</td></tr>
                    <tr><td><strong>Website:</strong></td><td>${supplier.website || '-'}</td></tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="text-primary mb-3">Alamat</h6>
                <p class="mb-3">${supplier.address || '-'}</p>
                
                <h6 class="text-primary mb-3">Notes</h6>
                <p>${supplier.notes || '-'}</p>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="card-title">Total Orders</h6>
                        <h3 class="text-primary">${supplier.total_orders || 0}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="card-title">Total Value</h6>
                        <h3 class="text-success">${formatCurrency(supplier.total_value || 0)}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="card-title">Rating</h6>
                        <h3 class="text-warning">${supplier.rating || 0}/5</h3>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#supplierDetailContent').html(html);
}

// Open add supplier modal
function openAddSupplierModal() {
    currentSupplierId = null;
    $('#supplierFormModalLabel').html('<i class="fas fa-building me-2"></i>Tambah Supplier');
    $('#supplierForm')[0].reset();
    $('#supplier_id').val('');
    
    // Generate kode supplier menggunakan API untuk sequential numbering
    generateSupplierCode();
    
    // Show modal
    $('#supplierFormModal').modal('show');
}

// Generate supplier code automatically
function generateSupplierCode() {
    // Generate kode supplier dengan mengecek database dulu
    $.ajax({
        url: '<?= base_url('purchasing/generate-supplier-code') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.code) {
                console.log('Generated kode supplier (DB checked):', response.code);
                $('#kode_supplier').val(response.code);
            } else {
                // Fallback jika API gagal - gunakan sequential number
                const year = new Date().getFullYear();
                const code = 'SUP-' + year + '-001';
                console.log('API failed, using fallback code:', code);
                $('#kode_supplier').val(code);
            }
        },
        error: function() {
            // Fallback jika AJAX gagal - gunakan sequential number
            const year = new Date().getFullYear();
            const code = 'SUP-' + year + '-001';
            console.log('AJAX failed, using fallback code:', code);
            $('#kode_supplier').val(code);
        }
    });
}

// Edit supplier from detail modal
function editSupplierFromModal() {
    if (!currentSupplierId) return;
    
    $.ajax({
        url: '<?= base_url('purchasing/get-supplier') ?>/' + currentSupplierId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const supplier = response.data;
                $('#supplier_id').val(supplier.id_supplier);
                $('#kode_supplier').val(supplier.kode_supplier);
                $('#nama_supplier').val(supplier.nama_supplier);
                $('#business_type').val(supplier.business_type);
                $('#contact_person').val(supplier.contact_person);
                $('#phone').val(supplier.phone);
                $('#email').val(supplier.email);
                $('#website').val(supplier.website);
                $('#address').val(supplier.address);
                $('#notes').val(supplier.notes);
                
                $('#supplierFormModalLabel').html('<i class="fas fa-edit me-2"></i>Edit Supplier');
                $('#supplierDetailModal').modal('hide');
                $('#supplierFormModal').modal('show');
            } else {
                Swal.fire('Error!', response.message || 'Gagal mengambil data supplier', 'error');
            }
        },
        error: function() {
            Swal.fire('Error!', 'Terjadi kesalahan saat mengambil data supplier', 'error');
        }
    });
}

// Change supplier status
function changeSupplierStatus() {
    if (!currentSupplierId) return;
    
    // Get current supplier status and set it in the modal
    $.ajax({
        url: '<?= base_url('purchasing/get-supplier') ?>/' + currentSupplierId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const supplier = response.data;
                $('#new_status').val(supplier.status || 'Active');
                $('#status_reason').val('');
                $('#changeStatusModal').modal('show');
            } else {
                Swal.fire('Error!', response.message || 'Gagal mengambil data supplier', 'error');
            }
        },
        error: function() {
            Swal.fire('Error!', 'Terjadi kesalahan saat mengambil data supplier', 'error');
        }
    });
}

// Save status change
function saveStatusChange() {
    const newStatus = $('#new_status').val();
    const reason = $('#status_reason').val();
    
    if (!newStatus) {
        Swal.fire('Error!', 'Pilih status baru', 'error');
        return;
    }
    
    $.ajax({
        url: '<?= base_url('purchasing/update-supplier-status') ?>/' + currentSupplierId,
        type: 'POST',
        data: {
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
            'status': newStatus,
            'reason': reason
        },
        dataType: 'json',
        success: function(response) {
                          if (response.success) {
                              Swal.fire('Berhasil!', response.message || 'Status supplier berhasil diubah', 'success');
                              $('#changeStatusModal').modal('hide');
                              $('#supplierDetailModal').modal('hide');
                              // Refresh data dengan reload halaman untuk memastikan data terbaru
                              setTimeout(function() {
                                  location.reload();
                              }, 1500);
                          } else {
                Swal.fire('Error!', response.message || 'Gagal mengubah status supplier', 'error');
            }
        },
        error: function() {
            Swal.fire('Error!', 'Terjadi kesalahan saat mengubah status supplier', 'error');
        }
    });
}

// Delete supplier from detail modal
function deleteSupplierFromModal() {
    if (!currentSupplierId) return;
    
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus supplier ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('purchasing/delete-supplier') ?>/' + currentSupplierId,
                type: 'POST',
                data: {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                      success: function(response) {
                          if (response.success) {
                              Swal.fire('Berhasil!', response.message || 'Supplier berhasil dihapus', 'success');
                              $('#supplierDetailModal').modal('hide');
                              // Refresh data dengan reload halaman untuk memastikan data terbaru
                              setTimeout(function() {
                                  location.reload();
                              }, 1500);
                          } else {
                        Swal.fire('Error!', response.message || 'Gagal menghapus supplier', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Terjadi kesalahan saat menghapus supplier', 'error');
                }
            });
        }
    });
}

// Save supplier
function saveSupplier() {
    // Ensure kode supplier is not empty
    const kodeSupplier = $('#kode_supplier').val();
    console.log('Kode supplier before submit:', kodeSupplier);
    
    if (!kodeSupplier || kodeSupplier.trim() === '') {
        console.log('Kode supplier kosong, regenerating...');
        Swal.fire({
            title: 'Kode Supplier Kosong',
            text: 'Kode supplier tidak boleh kosong. Sedang generate ulang...',
            icon: 'warning',
            timer: 2000,
            showConfirmButton: false
        });
        // Regenerate kode supplier
        generateSupplierCode();
        return;
    }
    
    const formData = new FormData($('#supplierForm')[0]);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    
    // Debug: Log semua form data
    console.log('Form data being sent:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
    
    // Double check kode supplier sebelum submit
    const finalKodeSupplier = $('#kode_supplier').val();
    console.log('Final kode supplier before submit:', finalKodeSupplier);
    
        if (!finalKodeSupplier || finalKodeSupplier.trim() === '') {
            console.error('Kode supplier masih kosong! Generating again...');
            // Generate sequential fallback
            const year = new Date().getFullYear();
            const code = 'SUP-' + year + '-001';
            $('#kode_supplier').val(code);
            console.log('Using sequential fallback:', code);
        }
    
    const isEdit = $('#supplier_id').val() !== '';
    const url = isEdit ? 
        '<?= base_url('purchasing/update-supplier') ?>/' + $('#supplier_id').val() : 
        '<?= base_url('purchasing/store-supplier') ?>';
    
    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('Berhasil!', response.message || 'Supplier berhasil disimpan', 'success');
                $('#supplierFormModal').modal('hide');
                // Refresh data tanpa reload halaman
                loadSuppliers();
            } else {
                Swal.fire('Error!', response.message || 'Gagal menyimpan supplier', 'error');
            }
        },
        error: function(xhr) {
            let errorMessage = 'Terjadi kesalahan saat menyimpan supplier';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            Swal.fire('Error!', errorMessage, 'error');
        }
    });
}

// Export data
function exportData() {
    window.open('<?= base_url('purchasing/export-suppliers') ?>', '_blank');
}

// Refresh table
function refreshTable() {
    location.reload();
}

// Refresh supplier table without full page reload
function refreshSupplierTable() {
    // Instead of reloading the page, just reload the current page data
    // This will refresh the table without full page reload
    window.location.href = window.location.href;
}

// Load suppliers data (like SPK pattern)
function loadSuppliers(startDate = null, endDate = null) {
    // Show loading indicator
    const refreshBtn = $('button[onclick="refreshTableData()"]');
    const originalText = refreshBtn.html();
    refreshBtn.html('<i class="fas fa-spinner fa-spin me-1"></i>Loading...').prop('disabled', true);
    
    // Build URL with date parameters
    let url = '<?= base_url('purchasing/suppliers-list') ?>';
    if (startDate && endDate) {
        url += `?start_date=${startDate}&end_date=${endDate}`;
    }
    
    // Fetch suppliers data
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update table body
                updateSupplierTable(data.suppliers);
                
            } else {
                console.error('Failed to load suppliers:', data.message);
                Swal.fire('Error!', 'Gagal memuat data supplier', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading suppliers:', error);
            Swal.fire('Error!', 'Terjadi kesalahan saat memuat data', 'error');
        })
        .finally(() => {
            // Restore button
            refreshBtn.html(originalText).prop('disabled', false);
        });
}

// Update supplier table
function updateSupplierTable(suppliers) {
    const tbody = $('#supplierTable tbody');
    tbody.empty();
    
    suppliers.forEach((supplier, index) => {
        const status = supplier.status || 'Inactive';
        const badgeClass = status === 'Active' ? 'bg-success' : 'bg-secondary';
        
        const row = `
            <tr style="cursor: pointer;" onclick="viewSupplierDetail(${supplier.id_supplier})">
                <td>${index + 1}</td>
                <td>
                    <span class="badge bg-primary">${supplier.kode_supplier || '-'}</span>
                </td>
                <td><strong>${supplier.nama_supplier || '-'}</strong></td>
                <td>${supplier.business_type || '-'}</td>
                <td>
                    ${supplier.phone ? `<i class="fas fa-phone me-1"></i>${supplier.phone}<br>` : ''}
                    ${supplier.email ? `<i class="fas fa-envelope me-1"></i>${supplier.email}` : ''}
                </td>
                <td>
                    <span class="badge ${badgeClass}">${status}</span>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

// Update supplier statistics
function updateSupplierStats(stats) {
    if (stats) {
        $('#stat-total-supplier').text(stats.total || 0);
        $('#stat-active-supplier').text(stats.active || 0);
        $('#stat-verified-supplier').text(stats.verified || 0);
        $('#stat-total-po').text(stats.total_po || 0);
    }
}

// Refresh table data without page reload
function refreshTableData() {
    loadSuppliers();
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(amount);
}

// Initialize DataTable for sorting and search functionality
$(document).ready(function() {
    $('#supplierTable').DataTable({
        processing: true,
        pageLength: 25,
        order: [[1, 'asc']], // Sort by supplier name
        columnDefs: [
            { orderable: false, targets: [-1] } // Disable sorting on last column (actions)
        ]
    });
});
</script>
<?= $this->endSection() ?>

