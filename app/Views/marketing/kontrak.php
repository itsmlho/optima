<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .card-stats:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); }
    .table-card, .card-stats { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); }
    .modal-header { background: linear-gradient(135deg, #e9ecef 0%, #e9ecef 100%); color: white; border-radius: 15px 15px 0 0; }
    .filter-card.active { 
        transform: translateY(-3px); 
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2); 
        border: 2px solid #fff; 
    }
    .filter-card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.25); 
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <!-- Statistics Cards (Nilai akan diisi oleh JavaScript) -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6"><div class="card card-stats bg-primary text-white h-100 filter-card" data-filter="all" style="cursor: pointer;"><div class="card-body"><h2 class="fw-bold mb-1" id="stat-total">0</h2><h6 class="card-title text-uppercase small">Total Kontrak</h6></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card card-stats bg-success text-white h-100 filter-card" data-filter="Aktif" style="cursor: pointer;"><div class="card-body"><h2 class="fw-bold mb-1" id="stat-active">0</h2><h6 class="card-title text-uppercase small">Kontrak Aktif</h6></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card card-stats bg-warning text-white h-100 filter-card" data-filter="expiring" style="cursor: pointer;"><div class="card-body"><h2 class="fw-bold mb-1" id="stat-expiring">0</h2><h6 class="card-title text-uppercase small">Akan Berakhir</h6></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card card-stats bg-secondary text-white h-100 filter-card" data-filter="Berakhir" style="cursor: pointer;"><div class="card-body"><h2 class="fw-bold mb-1" id="stat-expired">0</h2><h6 class="card-title text-uppercase small">Telah Berakhir</h6></div></div></div>
    </div>

    <!-- Tabel Daftar Kontrak -->
    <div class="card table-card">
        <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
            <h5 class="h5 mb-0 text-gray-800">Daftar Kontrak Rental</h5>
            <button class="btn btn-sm btn-primary d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#addContractModal">
                <span class="fw-semibold">+ Kontrak</span>
            </button>
        </div>
        <div class="card-body">
            <table id="contractsTable" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>No. Kontrak</th>
                        <th>No. PO</th>
                        <th>Nama Perusahaan</th>
                        <th>Periode Kontrak</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data akan dimuat oleh DataTables melalui AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Kontrak -->
<div class="modal fade" id="addContractModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Tambah Kontrak Baru</h5>
                    <small class="text-muted">Langkah 1: Informasi Dasar Kontrak</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addContractForm">
                <div class="modal-body">
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>Alur Pembuatan Kontrak Baru
                        </h6>
                        <ol class="mb-0 ps-3">
                            <li><strong>Langkah 1:</strong> Isi informasi dasar kontrak (form ini)</li>
                            <li><strong>Langkah 2:</strong> Tambahkan spesifikasi unit yang dibutuhkan</li>
                            <li><strong>Langkah 3:</strong> Buat SPK untuk mengalokasikan unit dari inventory</li>
                        </ol>
                        <hr>
                        <small class="text-muted">
                            <i class="fas fa-lightbulb me-1"></i>
                            <strong>Tips:</strong> Nilai kontrak dan total unit akan dihitung otomatis berdasarkan spesifikasi yang ditambahkan.
                        </small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Kontrak*</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="contract_number" required>
                                <button class="btn btn-outline-secondary" type="button" id="generateContractNumber" title="Generate Nomor Kontrak">
                                    <i class="fas fa-magic"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3"><label class="form-label">No. PO Klien</label><input type="text" class="form-control" name="po_number"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Nama Perusahaan*</label><input type="text" class="form-control" name="client_name" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Nama PIC</label><input type="text" class="form-control" name="pic"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Kontak PIC</label><input type="text" class="form-control" name="kontak"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Lokasi/Alamat</label><input type="text" class="form-control" name="lokasi"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Tanggal Mulai*</label><input type="date" class="form-control" name="start_date" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Tanggal Berakhir*</label><input type="date" class="form-control" name="end_date" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Jenis Sewa</label>
                            <select class="form-select" name="jenis_sewa">
                                <option value="BULANAN" selected>Bulanan</option>
                                <option value="HARIAN">Harian</option>
                            </select>
                        </div>
                        <div class="col-md-6"></div> <!-- Empty space for alignment -->
                        <div class="col-12 mb-3"><label class="form-label">Catatan</label><textarea class="form-control" name="catatan" rows="3" placeholder="Catatan tambahan (opsional)"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="submit_action" id="submitAction" value="save_and_spec">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btnSaveAndSpec" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan & Lanjut ke Spesifikasi
                    </button>
                    <button type="button" id="btnSaveOnly" class="btn btn-outline-primary">Simpan Kontrak</button>
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
// Fallback untuk OptimaPro jika belum dimuat
if (typeof OptimaPro === 'undefined') {
    console.log('OptimaPro not found, using fallback');
    window.OptimaPro = {
        showNotification: function(message, type, duration = 5000) {
            console.log(`OptimaPro.showNotification: [${type}] ${message}`);
            const alertClass = type === 'success' ? 'alert-success' : 
                              type === 'error' || type === 'danger' ? 'alert-danger' : 
                              type === 'warning' ? 'alert-warning' : 'alert-info';
            
            const notification = `
                <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                     style="top: 20px; right: 20px; z-index: 9999; max-width: 400px;">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            $('body').append(notification);
            setTimeout(() => $('.alert').fadeOut(), duration);
        },
        showConfirmDialog: function({title, message}) {
            console.log(`OptimaPro.showConfirmDialog: ${title} - ${message}`);
            return new Promise(resolve => {
                if (confirm(`${title}\n${message}`)) {
                    resolve({ isConfirmed: true });
                } else {
                    resolve({ isConfirmed: false });
                }
            });
        },
        formatCurrency: function(amount) {
            return new Intl.NumberFormat('id-ID').format(amount || 0);
        }
    };
    console.log('OptimaPro fallback loaded successfully');
} else {
    console.log('OptimaPro already loaded');
}

// Helper function for number formatting
function formatNumber(num) {
    try {
        return new Intl.NumberFormat('id-ID').format(num || 0);
    } catch (error) {
        console.warn('formatNumber error:', error);
        return (num || 0).toString();
    }
}

// Safe OptimaPro wrapper functions
function safeShowNotification(message, type = 'info', duration = 5000) {
    if (window.OptimaPro && typeof OptimaPro.showNotification === 'function') {
        OptimaPro.showNotification(message, type, duration);
    } else {
        // Fallback to alert or console
        console.log(`[${type.toUpperCase()}] ${message}`);
        if (type === 'error' || type === 'danger') {
            alert(`Error: ${message}`);
        }
    }
}

function safeShowConfirmDialog({title, message}) {
    if (window.OptimaPro && typeof OptimaPro.showConfirmDialog === 'function') {
        return OptimaPro.showConfirmDialog({title, message});
    } else {
        // Fallback to native confirm
        return new Promise(resolve => {
            if (confirm(`${title}\n\n${message}`)) {
                resolve({ isConfirmed: true });
            } else {
                resolve({ isConfirmed: false });
            }
        });
    }
}
$(document).ready(function() {
    console.log('Document ready - initializing DataTable...');
    console.log('Base URL:', '<?= base_url() ?>');
    console.log('AJAX URL:', '<?= base_url('marketing/kontrak/getDataTable') ?>');
    console.log('jQuery version:', $.fn.jquery);
    console.log('DataTables available:', typeof $.fn.DataTable !== 'undefined');
    
    // Check if table element exists
    const tableElement = $('#contractsTable');
    console.log('Table element found:', tableElement.length > 0);
    console.log('Table element:', tableElement);
    
    // Global variable for current filter
    let currentKontrakFilter = 'all';
    
    // Function to apply filter and reload DataTable
    function applyKontrakFilter(filter) {
        currentKontrakFilter = filter;
        
        // Update active card styling
        $('.filter-card').removeClass('active');
        $(`[data-filter="${filter}"]`).addClass('active');
        
        // Safely reload DataTable with new filter
        if (typeof window.contractsTable !== 'undefined' && window.contractsTable) {
            window.contractsTable.ajax.reload();
        } else {
            console.warn('window.contractsTable is undefined in applyKontrakFilter, trying direct access');
            $('#contractsTable').DataTable().ajax.reload();
        }
    }
    
    // Initialize DataTable with error handling
    let contractsTable;
    try {
        contractsTable = $('#contractsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('marketing/kontrak/getDataTable') ?>', // Updated endpoint
            type: 'POST',
            data: function(d) {
                // Add CSRF token to the data
                d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                
                // Add current filter to the data
                d.statusFilter = currentKontrakFilter;
                
                console.log('Sending data:', d);
                return d;
            },
            dataSrc: function(json) {
                console.log('DataTable AJAX Response:', json);
                if(!json){ console.warn('No JSON returned'); return []; }
                if(json.error){ console.error('Server returned error:', json.error); }
                
                // Check for errors
                if (json.error) {
                    console.error('Server Error:', json.error);
                    safeShowNotification('Server Error: ' + json.error, 'danger');
                    return [];
                }
                
                // Update statistics if available
                if (json && json.stats) {
                    $('#stat-total').text(json.stats.total ?? 0);
                    $('#stat-active').text(json.stats.active ?? 0);
                    $('#stat-expiring').text(json.stats.expiring ?? 0);
                    $('#stat-expired').text(json.stats.expired ?? 0);
                }
                
                return json.data || [];
            },
            error: function(xhr, error, thrown) {
                console.error('DataTable AJAX Error:', xhr, error, thrown);
                console.log('Response Text:', xhr.responseText);
                safeShowNotification('Error loading data: ' + error, 'danger');
            }
        },
        columns: [
            { data: 'contract_number', name: 'no_kontrak' },
            { data: 'po', name: 'no_po_marketing' },
            { data: 'client_name', name: 'pelanggan' },
            { data: 'period', name: 'tanggal_mulai' },
            { data: 'status', name: 'status' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']], // Changed to first column (contract_number)
        drawCallback: function(settings) {
            console.log('DataTable draw callback executed');
            const json = this.api().ajax.json();
            console.log('Draw callback - JSON data:', json);
            if(json && json.data && json.data.length===0){
                console.log('No rows rendered. If expected rows exist in DB, check controller Marketing::getDataTable and DB connection.');
            }
            // Make first column (No. Kontrak) clickable to open contract detail modal
            try {
                const api = this.api();
                api.rows({ page: 'current' }).every(function(rowIdx){
                    const rowData = this.data();
                    const node = this.node();
                    if (!rowData) return;
                    const $cell = $(node).find('td').eq(0);
                    if ($cell && $cell.length && !$cell.find('a.contract-link').length) {
                        // Extract text from the HTML content for display
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = rowData.contract_number || '';
                        const contractText = tempDiv.textContent || tempDiv.innerText || '';
                        
                        $cell.html(`<a href="#" class="contract-link text-decoration-none" onclick="openContractDetail(${rowData.id});return false;"><strong>${contractText}</strong></a>`);
                    }
                });
            } catch(e){ console.warn('Failed to attach contract links', e); }
        }
    });
    
    console.log('DataTable initialized successfully');
    
    // Make contractsTable globally accessible
    window.contractsTable = contractsTable;
    
    } catch (error) {
        console.error('Failed to initialize DataTable:', error);
        // Fallback: show error message in table
        $('#contractsTable tbody').html('<tr><td colspan="6" class="text-center text-danger">Error: Failed to load DataTable. Check console for details.</td></tr>');
        // Create a dummy contractsTable object to prevent errors
        contractsTable = {
            ajax: { reload: function() { console.log('Dummy reload called'); } }
        };
        window.contractsTable = contractsTable;
    }

    // Add filter card click listeners
    $('.filter-card').on('click', function() {
        const filter = $(this).data('filter');
        applyKontrakFilter(filter);
    });
    
    // Set default active filter (all)
    $('[data-filter="all"]').addClass('active');
    console.log('Testing AJAX connection...');
    console.log('Base URL:', '<?= base_url() ?>');
    console.log('Full AJAX URL:', '<?= base_url('marketing/kontrak/getDataTable') ?>');
    console.log('CSRF Token:', '<?= csrf_token() ?>');
    console.log('CSRF Hash:', '<?= csrf_hash() ?>');
    
    $.ajax({
        url: '<?= base_url('marketing/kontrak/getDataTable') ?>',
        type: 'POST',
        data: {
            draw: 1,
            start: 0,
            length: 10,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        },
        success: function(response) {
            console.log('AJAX test successful:', response);
            if (response.data && response.data.length > 0) {
                console.log('Data found:', response.data.length, 'records');
                console.log('Sample record:', response.data[0]);
            } else {
                console.log('No data returned from server');
                console.log('Full response:', response);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX test failed:', xhr, status, error);
            console.log('Status:', status);
            console.log('Error:', error);
            console.log('Response:', xhr.responseText);
            console.log('Status code:', xhr.status);
        }
    });

    // Add contract number generation
    function generateContractNumber() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        return `KTR/${year}/${month}/`;
    }

    // Auto-fill contract number when modal opens
    $('#addContractModal').on('show.bs.modal', function() {
        const contractNumberField = $('input[name="contract_number"]');
        if (!contractNumberField.val()) {
            // Generate base contract number
            const baseNumber = generateContractNumber();
            contractNumberField.val(baseNumber);
            
            // Fetch next available number
            fetch('<?= base_url('marketing/kontrak/generate-number') ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        contractNumberField.val(data.contract_number);
                    }
                })
                .catch(err => console.log('Could not auto-generate contract number:', err));
        }
    });

    // Check for duplicate contract number before submitting
    function checkContractNumberDuplicate(contractNumber) {
        return fetch('<?= base_url('marketing/kontrak/check-duplicate') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                contract_number: contractNumber,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            })
        })
        .then(response => response.json())
        .then(data => {
            return data.duplicate || false;
        })
        .catch(err => {
            console.error('Error checking duplicate:', err);
            return false;
        });
    }

    // Validate contract number on blur
    $('input[name="contract_number"]').on('blur', function() {
        const contractNumber = $(this).val().trim();
        if (contractNumber) {
            checkContractNumberDuplicate(contractNumber).then(isDuplicate => {
                if (isDuplicate) {
                    $(this).addClass('is-invalid');
                    if (!$(this).next('.invalid-feedback').length) {
                        $(this).after('<div class="invalid-feedback">Nomor kontrak sudah digunakan. Gunakan tombol generate untuk nomor baru.</div>');
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').remove();
                }
            });
        }
    });

    // Handle generate contract number button
    $('#generateContractNumber').on('click', function() {
        const button = $(this);
        const icon = button.find('i');
        const originalIcon = icon.attr('class');
        
        // Show loading state
        icon.attr('class', 'fas fa-spinner fa-spin');
        button.prop('disabled', true);
        
        fetch('<?= base_url('marketing/kontrak/generate-number') ?>')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('input[name="contract_number"]').val(data.contract_number);
                    safeShowNotification('Nomor kontrak berhasil di-generate', 'success');
                } else {
                    safeShowNotification('Gagal generate nomor kontrak: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(err => {
                console.error('Error generating contract number:', err);
                safeShowNotification('Gagal generate nomor kontrak', 'error');
            })
            .finally(() => {
                // Restore original state
                icon.attr('class', originalIcon);
                button.prop('disabled', false);
            });
    });

    // Primary action buttons
    $('#btnSaveAndSpec').on('click', function() {
        $('#submitAction').val('save_and_spec');
        $('#addContractForm').trigger('submit');
    });
    $('#btnSaveOnly').on('click', function() {
        $('#submitAction').val('save_only');
        $('#addContractForm').trigger('submit');
    });

    $('#addContractForm').on('submit', function(e) {
        e.preventDefault();
        
        const contractId = $(this).data('contract-id');
        const isEdit = contractId ? true : false;
        const action = $('#submitAction').val();
        const url = isEdit ? 
            `<?= base_url('marketing/kontrak/update/') ?>${contractId}` : 
            '<?= base_url('marketing/kontrak/store') ?>';
        
        // Disable buttons to prevent double submit
        const $btnSaveAndSpec = $('#btnSaveAndSpec');
        const $btnSaveOnly = $('#btnSaveOnly');
        if (!$btnSaveAndSpec.data('orig-html')) $btnSaveAndSpec.data('orig-html', $btnSaveAndSpec.html());
        if (!$btnSaveOnly.data('orig-html')) $btnSaveOnly.data('orig-html', $btnSaveOnly.html());
        if (action === 'save_only') {
            $btnSaveOnly.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');
            $btnSaveAndSpec.prop('disabled', true);
        } else {
            $btnSaveAndSpec.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');
            $btnSaveOnly.prop('disabled', true);
        }

    $.ajax({
            url: url,
            method: 'POST',
            data: $(this).serialize() + '&<?= csrf_token() ?>=' + '<?= csrf_hash() ?>',
            dataType: 'json',
        success: function(response) {
                if (response.success) {
            $('#addContractModal').modal('hide');
            if (isEdit) {
                        // Jika edit, reload table dan show success message
                        safeShowNotification(response.message, 'success');
                        // Safely reload DataTable
                        if (typeof window.contractsTable !== 'undefined' && window.contractsTable) {
                            window.contractsTable.ajax.reload();
                        } else {
                            $('#contractsTable').DataTable().ajax.reload();
                        }
                        
                        // Reset form after success
                        $('#addContractForm')[0].reset();
                        $('#addContractForm').removeData('contract-id');
                        $('#addContractForm').attr('action', 'store');
                        $('#addContractModal .modal-title').text('Tambah Kontrak Baru');
                    } else {
                        // Create baru: arahkan sesuai submit_action
                        safeShowNotification(response.message, 'success');
                        // Safely reload DataTable
                        if (typeof window.contractsTable !== 'undefined' && window.contractsTable) {
                            window.contractsTable.ajax.reload();
                        } else {
                            $('#contractsTable').DataTable().ajax.reload();
                        }
                        const goToSpec = ($('#submitAction').val() === 'save_and_spec');
                        const newId = response.data && response.data.id ? response.data.id : null;
                        // Reset form
                        $('#addContractForm')[0].reset();
                        if (goToSpec && newId) {
                            // Buka modal detail kontrak dan tab spesifikasi
                            openContractDetail(newId);
                            setTimeout(() => {
                                const spekTab = document.querySelector('#contractDetailTabs button[data-bs-target="#spesifikasi-content"]');
                                if (spekTab) spekTab.click();
                            }, 400);
                        }
                    }
                } else {
                    // Duplicate contract handling
                    if (response.duplicate && response.existing_id) {
                        const contractNumber = $('input[name="contract_number"]').val();
                        safeShowConfirmDialog({
                            title: 'Nomor Kontrak Sudah Ada',
                            message: `Nomor kontrak "${contractNumber}" sudah digunakan. Klik "Generate Baru" untuk nomor baru, atau "Lihat Kontrak" untuk membuka kontrak yang sudah ada.`
                        }).then(res => {
                            if (res.isConfirmed) {
                                openContractDetail(response.existing_id);
                                setTimeout(() => {
                                    const spekTab = document.querySelector('#contractDetailTabs button[data-bs-target="#spesifikasi-content"]');
                                    if (spekTab) spekTab.click();
                                }, 400);
                            }
                        });
                        
                        // Also show a secondary option to generate new number
                        setTimeout(() => {
                            if (confirm('Apakah Anda ingin generate nomor kontrak baru?')) {
                                $('#generateContractNumber').click();
                            }
                        }, 1000);
                        
                        return;
                    }

                    let msg = response.message || 'Terjadi kesalahan.';
                    if (response.errors && typeof response.errors === 'object') {
                        msg = 'Validasi gagal: ' + Object.values(response.errors).join(', ');
                    }
                    safeShowNotification(msg, 'danger');
                }
            },
            error: function(xhr, status, error) {
                let msg = 'Tidak dapat terhubung ke server.';
                if (xhr && xhr.responseText) {
                    try { const r = JSON.parse(xhr.responseText); if (r.message) msg = r.message; } catch(e) {}
                }
                safeShowNotification(msg, 'danger');
            },
            complete: function() {
                // Re-enable buttons and restore labels
                $btnSaveAndSpec.prop('disabled', false).html($btnSaveAndSpec.data('orig-html'));
                $btnSaveOnly.prop('disabled', false).html($btnSaveOnly.data('orig-html'));
            }
        });
    });
    
    // Reset form when modal is closed
    $('#addContractModal').on('hidden.bs.modal', function() {
        $('#addContractForm')[0].reset();
        $('#addContractForm').removeData('contract-id');
        $('#addContractForm').attr('action', 'store');
        $('#addContractModal .modal-title').text('Tambah Kontrak Baru');
    });

    // ===== SPESIFIKASI FORM HANDLER =====
    
    // Handle add spesifikasi form submission
    $('#addSpesifikasiForm').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Validate required fields on frontend
        const kontrakId = $('#spekKontrakId').val();
        const jumlahDibutuhkan = $('input[name="jumlah_dibutuhkan"]').val();
        const departemenId = $('select[name="departemen_id"]').val();
        const tipeUnitId = $('select[name="tipe_unit_id"]').val();
        const jenisUnit = $('select[name="tipe_jenis"]').val();
        
        // Frontend validation check
    
        if (!kontrakId) {
            safeShowNotification('Kontrak ID tidak ditemukan. Silakan buka detail kontrak terlebih dahulu.', 'error');
            return;
        }
        
        if (!jumlahDibutuhkan || jumlahDibutuhkan <= 0) {
            safeShowNotification('Jumlah unit dibutuhkan harus diisi dengan nilai lebih dari 0.', 'error');
            return;
        }
        
        if (!departemenId) {
            safeShowNotification('Departemen harus dipilih.', 'error');
            return;
        }
        
        if (!tipeUnitId) {
            safeShowNotification('Tipe Unit harus dipilih.', 'error');
            return;
        }
        
        if (!jenisUnit) {
            safeShowNotification('Jenis Unit harus dipilih.', 'error');
            return;
        }
        
        // Get form data with proper serialization
        const formData = $(this).serialize() + '&<?= csrf_token() ?>=' + '<?= csrf_hash() ?>';
        
        // Debug: Log form data to check if kapasitas_id is included
        console.log('Form data being sent:', formData);
        console.log('Kapasitas value:', $('#spekKapasitas').val());
        
        // Check if this is edit mode
        const spekId = $('#spekEditId').val();
        const isEdit = spekId && spekId !== '';
        const url = isEdit ? 
            `<?= base_url('marketing/kontrak/update-spesifikasi/') ?>${spekId}` : 
            '<?= base_url('marketing/kontrak/add-spesifikasi') ?>';
        
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            dataType: 'json',

            success: function(response) {
                if (response.success) {
                    safeShowNotification(response.message, 'success');
                    $('#addSpesifikasiModal').modal('hide');
                    
                    // Reset form
                    $('#addSpesifikasiForm')[0].reset();
                    
                    // Reload spesifikasi tab
                    const contractId = $('#spekKontrakId').val();
                    if (contractId) {
                        loadContractSpesifikasi(contractId);
                    }
                    

                } else {
                    // Show detailed error information
                    
                    let errorMsg = response.message || 'Terjadi kesalahan saat menyimpan spesifikasi';
                    
                    // Show validation errors if any
                    if (response.validation_errors && Object.keys(response.validation_errors).length > 0) {
                        errorMsg += '\nValidation errors: ' + JSON.stringify(response.validation_errors);
                    }
                    
                    // Show database errors if any
                    if (response.db_error && response.db_error.message) {
                        errorMsg += '\nDatabase error: ' + response.db_error.message;
                    }
                    
                    safeShowNotification(errorMsg, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                
                let errorMessage = 'Gagal menambah spesifikasi: ' + error;
                if (xhr.responseText) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        console.warn('Could not parse error response');
                    }
                }
                
                safeShowNotification(errorMessage, 'error');
            }
        });
    }); // End spesifikasi form handler

    // Note: Only one submit handler for addSpesifikasiForm to prevent duplicates

});

function viewContractUnits(contractId) {
    // Validate contract ID
    if (!contractId || contractId == '0' || contractId == 0) {
        safeShowNotification('ID kontrak tidak valid.', 'error');
        return;
    }
    
    // Arahkan ke halaman list_unit.php dengan membawa ID kontrak
    window.location.href = `<?= base_url('marketing/list-unit/') ?>${contractId}`;
}

// Open contract detail modal with multi-specification support
function openContractDetail(id){
    console.log('Opening contract detail for ID:', id);
    
    // Validate contract ID
    if (!id || id == '0' || id == 0) {
        safeShowNotification('ID kontrak tidak valid.', 'error');
        return;
    }
    
    const pdfBtn = document.getElementById('btnPrintContract');
    if (pdfBtn) { pdfBtn.href = `<?= base_url('marketing/kontrak/print/') ?>${id}`; }
    
    // Store contract ID globally for other functions
    window.currentKontrakId = id;
    console.log('Set window.currentKontrakId to:', window.currentKontrakId);
    
    // Reset tabs to info tab
    const infoTab = document.querySelector('#info-tab');
    if (infoTab) {
        console.log('Clicking info tab to reset');
        infoTab.click();
    } else {
        console.error('Info tab not found!');
    }
    
    // Load basic contract info
    loadContractInfo(id);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('contractDetailModal'));
    console.log('Showing modal...');
    modal.show();
    
    // Setup tab event handler after modal is shown
    modal._element.addEventListener('shown.bs.modal', function () {
        console.log('Modal shown, setting up tab event handlers...');
        setupTabEventHandlers();
        
        // Check if tab event handler is working after modal is shown
        setTimeout(() => {
            const tabButtons = document.querySelectorAll('#contractDetailTabs button[data-bs-toggle="tab"]');
            console.log('Found tab buttons:', tabButtons.length);
            tabButtons.forEach((btn, index) => {
                console.log(`Tab ${index}:`, btn.getAttribute('data-bs-target'));
            });
        }, 100);
    });
}

// Setup tab event handlers
function setupTabEventHandlers() {
    console.log('Setting up tab event handler...');
    
    // Remove existing event handlers first
    $('#contractDetailTabs button[data-bs-toggle="tab"]').off('shown.bs.tab');
    
    // Add new event handler
    $('#contractDetailTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        console.log('Tab event triggered!');
        const target = e.target.getAttribute('data-bs-target');
        const contractId = window.currentKontrakId;
        
        console.log('Tab changed to:', target, 'Contract ID:', contractId);
        
        if (target === '#spesifikasi-content' && contractId) {
            console.log('Loading spesifikasi for contract:', contractId);
            loadContractSpesifikasi(contractId);
        } else if (target === '#units-content' && contractId) {
            console.log('Loading units for contract:', contractId);
            loadContractUnits(contractId);
        }
    });
    
    console.log('Tab event handlers setup complete');
}

function loadContractInfo(id) {
    const body = document.getElementById('contractDetailBody');
    if (!body) return;
    
    body.innerHTML = '<p class="text-muted">Memuat...</p>';
    
    fetch(`<?= base_url('marketing/kontrak/detail/') ?>${id}`)
        .then(response => response.json())
        .then(j => {
            if (!j.success) {
                body.innerHTML = '<div class="text-danger">Gagal memuat detail kontrak</div>';
                return;
            }
            
            const d = j.data || {};
            
            // Store contract data globally for SPK creation
            window.currentContractData = d;
            
            body.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6"><strong>No. Kontrak:</strong> ${d.no_kontrak || '-'}</div>
                    <div class="col-md-6"><strong>No. PO Marketing:</strong> ${d.no_po_marketing || '-'}</div>
                    <div class="col-md-6"><strong>Pelanggan:</strong> ${d.pelanggan || '-'}</div>
                    <div class="col-md-6"><strong>Lokasi:</strong> ${d.lokasi || '-'}</div>
                    <div class="col-md-6"><strong>PIC:</strong> ${d.pic || '-'}</div>
                    <div class="col-md-6"><strong>Kontak:</strong> ${d.kontak || '-'}</div>
                    <div class="col-md-6"><strong>Jenis Sewa:</strong> <span class="badge bg-info">${d.jenis_sewa || 'BULANAN'}</span></div>
                    <div class="col-md-6"><strong>Status:</strong> <span class="badge bg-${d.status === 'Aktif' ? 'success' : d.status === 'Pending' ? 'warning' : 'secondary'}">${d.status || '-'}</span></div>
                    <div class="col-md-6"><strong>Tanggal Mulai:</strong> ${d.tanggal_mulai || '-'}</div>
                    <div class="col-md-6"><strong>Tanggal Berakhir:</strong> ${d.tanggal_berakhir || '-'}</div>
                    <div class="col-md-6"><strong>Total Unit:</strong> <span class="fw-bold text-primary">${d.total_units || 0}</span></div>
                    <div class="col-md-6"><strong>Nilai Total:</strong> <span class="fw-bold text-success">Rp ${formatNumber(d.nilai_total || 0)}</span></div>
                    <div class="col-md-6"><strong>Dibuat Oleh:</strong> ${d.dibuat_oleh || '-'}</div>
                    <div class="col-md-6"><strong>Dibuat Pada:</strong> ${d.dibuat_pada || '-'}</div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error loading contract info:', error);
            body.innerHTML = '<div class="text-danger">Gagal memuat detail kontrak</div>';
        });
}

function loadContractSpesifikasi(kontrakId) {
    console.log('loadContractSpesifikasi called with kontrakId:', kontrakId);
    const container = document.getElementById('spesifikasiList');
    if (!container) {
        console.error('spesifikasiList container not found!');
        return;
    }
    
    console.log('Container found, setting loading message...');
    container.innerHTML = '<p class="text-muted">Memuat spesifikasi...</p>';
    
    const url = `<?= base_url('marketing/kontrak/spesifikasi/') ?>${kontrakId}`;
    console.log('Fetching URL:', url);
    
    fetch(url)
        .then(response => {
            console.log('Response status:', response.status, response.statusText);
            console.log('Response headers:', response.headers);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(j => {
            console.log('Spesifikasi response:', j); // Debug log
            if (!j.success) {
                console.error('API returned error:', j.message);
                container.innerHTML = '<div class="text-danger">Gagal memuat spesifikasi: ' + (j.message || 'Unknown error') + '</div>';
                return;
            }
            
            const spesifikasi = j.data || [];
            const summary = j.summary || {};
            
            console.log('Processing spesifikasi data, count:', spesifikasi.length);
            console.log('Summary data:', summary);
            
            // Update tab counter
            const spekCountElement = document.getElementById('spekCount');
            if (spekCountElement) {
                spekCountElement.textContent = spesifikasi.length;
                console.log('Updated tab counter to:', spesifikasi.length);
            } else {
                console.warn('spekCount element not found');
            }
            
            if (spesifikasi.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-clipboard-list fa-3x text-muted"></i>
                        </div>
                        <h5 class="text-muted">Belum Ada Spesifikasi</h5>
                        <p class="text-muted mb-4">
                            Kontrak ini belum memiliki spesifikasi unit yang dibutuhkan.<br>
                            Tambahkan spesifikasi untuk menentukan jenis unit, jumlah, dan harga yang diperlukan.
                        </p>
                        <div class="d-flex flex-column align-items-center gap-2">
                            <button class="btn btn-primary btn-lg" onclick="openAddSpesifikasiModal()">
                                <i class="fas fa-plus me-2"></i>Tambah Spesifikasi Pertama
                            </button>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Setelah menambah spesifikasi, nilai kontrak akan dihitung otomatis
                            </small>
                        </div>
                    </div>
                `;
                return;
            }
            
            let html = `
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center py-2">
                                <div class="fw-bold text-primary">${summary.total_spesifikasi || 0}</div>
                                <small>Spesifikasi</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center py-2">
                                <div class="fw-bold text-success">${summary.total_unit_dibutuhkan || 0}</div>
                                <small>Total Unit</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center py-2">
                                <div class="fw-bold text-warning">${formatNumber((summary.total_nilai_bulanan || 0) + (summary.total_nilai_harian || 0))}</div>
                                <small>Total Nilai</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            spesifikasi.forEach((spek, index) => {
                console.log(`Processing spek ${index + 1}:`, spek.spek_kode);
                
                try {
                    const progress = spek.jumlah_dibutuhkan > 0 ? 
                        Math.round((spek.jumlah_tersedia / spek.jumlah_dibutuhkan) * 100) : 0;
                    const progressClass = progress === 100 ? 'success' : progress > 0 ? 'warning' : 'secondary';
                
                html += `
                    <div class="card mb-3" data-spek-id="${spek.id}" data-jumlah-dibutuhkan="${spek.jumlah_dibutuhkan}" data-jumlah-tersedia="${spek.jumlah_tersedia}">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <span class="badge bg-primary me-2">${spek.spek_kode}</span>
                                ${spek.catatan_spek || 'Spesifikasi ' + spek.spek_kode}
                            </h6>
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editSpesifikasi(${spek.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteSpesifikasi(${spek.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <small class="text-muted">Total Unit</small>
                                    <div class="fw-bold">${spek.jumlah_dibutuhkan}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Harga Bulanan</small>
                                    <div class="fw-bold text-success">Rp ${formatNumber(spek.harga_per_unit_bulanan || 0)}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Harga Harian</small>
                                    <div class="fw-bold text-info">Rp ${formatNumber(spek.harga_per_unit_harian || 0)}</div>
                                </div>
                            </div>
                            
                            <div class="row g-2 mt-2">
                                <div class="col-md-4">
                                    <small class="text-muted">Departemen</small>
                                    <div>${spek.nama_departemen || '-'}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Tipe/Jenis</small>
                                    <div>${spek.tipe_unit_name || spek.tipe_jenis || '-'}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Kapasitas</small>
                                    <div>${spek.kapasitas_name || '-'}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Merk/Model</small>
                                    <div>${spek.merk_unit || '-'} ${spek.model_unit || ''}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Attachment</small>
                                    <div>${spek.attachment_tipe || '-'} ${spek.attachment_merk || ''}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Baterai/Charger</small>
                                    <div>${spek.jenis_baterai || '-'} / ${spek.charger_name || '-'}</div>
                                </div>
                            </div>
                            
                            ${spek.aksesoris && spek.aksesoris.length > 0 ? `
                                <div class="row g-2 mt-2">
                                    <div class="col-12">
                                        <small class="text-muted">Aksesoris</small>
                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                            ${spek.aksesoris.map(acc => `<span class="badge bg-secondary text-white">${acc}</span>`).join('')}
                                        </div>
                                    </div>
                                </div>
                            ` : ''}
                            
                            ${spek.jumlah_tersedia < spek.jumlah_dibutuhkan ? `
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-primary" onclick="openSpkModalFromKontrak(${spek.id})">
                                        <i class="fas fa-file-alt me-1"></i>Buat SPK
                                    </button>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle me-1"></i>SPK yang dibuat: ${spek.jumlah_spk || 0}
                                    </small>
                                </div>
                            ` : `
                                <div class="mt-2">
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>SPK Lengkap (${spek.jumlah_spk || 0} SPK dibuat)
                                    </span>
                                </div>
                            `}
                        </div>
                    </div>
                `;
                } catch (error) {
                    console.error(`Error processing spek ${index + 1}:`, error);
                }
            });
            
            console.log('Generated HTML length:', html.length);
            console.log('Setting container HTML...');
            
            try {
                container.innerHTML = html;
                console.log('Container HTML set successfully');
                console.log('Container children count:', container.children.length);
            } catch (error) {
                console.error('Error setting container HTML:', error);
                container.innerHTML = '<div class="text-danger">Error displaying data: ' + error.message + '</div>';
            }
        })
        .catch(error => {
            console.error('Error loading specifications:', error);
            container.innerHTML = '<div class="text-danger">Gagal memuat spesifikasi: ' + error.message + '</div>';
        });
}

// Single modal for creating SPK from kontrak detail
document.addEventListener('DOMContentLoaded', function() {
    if (!document.getElementById('spkFromKontrakModal')) {
        const modalHtml = `
<div class="modal fade" id="spkFromKontrakModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Buat SPK</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="spkFromKontrakForm">
                <div class="modal-body">
                    <input type="hidden" name="kontrak_id" id="spkKontrakId">
                    <input type="hidden" name="kontrak_spesifikasi_id" id="spkSpesifikasiId">
                    <div class="mb-3">
                        <label class="form-label">Jenis SPK</label>
                        <select class="form-select" name="jenis_spk" required>
                            <option value="UNIT" selected>SPK Unit</option>
                            <option value="ATTACHMENT">SPK Attachment</option>
                            <option value="TUKAR">SPK Tukar</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pelanggan</label>
                        <input type="text" class="form-control" name="pelanggan" id="spkPelanggan" readonly required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">PIC</label>
                        <input type="text" class="form-control" name="pic" id="spkPic" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kontak</label>
                        <input type="text" class="form-control" name="kontak" id="spkKontak" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" class="form-control" name="lokasi" id="spkLokasi" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Delivery Plan</label>
                        <input type="date" class="form-control" name="delivery_plan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Unit <small class="text-muted" id="jumlahUnitHint"></small></label>
                        <input type="number" class="form-control" name="jumlah_unit" id="spkJumlahUnit" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" name="catatan" rows="3" placeholder="Keterangan tambahan untuk SPK ini (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button>
                    <button class="btn btn-primary" type="submit">Buat SPK</button>
                </div>
            </form>
        </div>
    </div>
</div>`;

        const wrapper = document.createElement('div');
        wrapper.innerHTML = modalHtml;
        document.body.appendChild(wrapper);

        // Expose global opener so per-spec buttons can call it
        window.openSpkModalFromKontrak = function(spekId) {
            console.log('openSpkModalFromKontrak called with spekId:', spekId);
            const kontrakId = window.currentKontrakId || document.getElementById('currentKontrakId')?.value;
            console.log('Current kontrakId:', kontrakId);
            if (!kontrakId) {
                safeShowNotification('Kontrak belum dipilih. Buka detail kontrak terlebih dahulu.', 'error');
                return;
            }
            document.getElementById('spkKontrakId').value = kontrakId;
            document.getElementById('spkSpesifikasiId').value = spekId;
            console.log('Set form values - kontrakId:', kontrakId, 'spekId:', spekId);
            
            // Populate fields from contract data
            if (window.currentContractData) {
                const pelangganField = document.getElementById('spkPelanggan');
                const picField = document.getElementById('spkPic');
                const kontakField = document.getElementById('spkKontak');
                const lokasiField = document.getElementById('spkLokasi');
                
                if (pelangganField) pelangganField.value = window.currentContractData.pelanggan || '';
                if (picField) picField.value = window.currentContractData.pic || '';
                if (kontakField) kontakField.value = window.currentContractData.kontak || '';
                if (lokasiField) lokasiField.value = window.currentContractData.lokasi || '';
            }
            
            // Reset other fields
            document.querySelector('#spkFromKontrakForm [name="jenis_spk"]').value = 'UNIT';
            document.querySelector('#spkFromKontrakForm [name="delivery_plan"]').value = '';
            document.querySelector('#spkFromKontrakForm [name="jumlah_unit"]').value = '';
            document.querySelector('#spkFromKontrakForm [name="catatan"]').value = '';
            // Compute available units from current spesifikasi list in DOM if present
            try {
                let available = 0;
                const card = document.querySelector(`[data-spek-id="${spekId}"]`);
                if (card) {
                    const need = Number(card.getAttribute('data-jumlah-dibutuhkan') || '0');
                    const have = Number(card.getAttribute('data-jumlah-tersedia') || '0');
                    available = Math.max(0, need - have);
                }
                const jumlahInput = document.getElementById('spkJumlahUnit');
                const hint = document.getElementById('jumlahUnitHint');
                const formEl = document.getElementById('spkFromKontrakForm');
                if (jumlahInput) {
                    if (available > 0) {
                        jumlahInput.setAttribute('max', String(available));
                        jumlahInput.setAttribute('placeholder', 'Maks ' + available);
                    } else {
                        jumlahInput.removeAttribute('max');
                        jumlahInput.removeAttribute('placeholder');
                    }
                }
                if (hint) {
                    hint.textContent = available > 0 ? `(maks ${available})` : '';
                }
                if (formEl) {
                    formEl.dataset.availableUnits = String(available);
                }
            } catch(e) { console.warn('Failed to set available units hint', e); }
            const modal = new bootstrap.Modal(document.getElementById('spkFromKontrakModal'));
            modal.show();
        };

        // Attach submit handler
        const spkForm = document.getElementById('spkFromKontrakForm');
        if (spkForm) {
            spkForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(spkForm);
                // Add CSRF
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                
                // Validate required fields
                const kontrakId = formData.get('kontrak_id');
                const kontrakSpesifikasiId = formData.get('kontrak_spesifikasi_id');
                console.log('Validation - kontrak_id:', kontrakId, 'kontrak_spesifikasi_id:', kontrakSpesifikasiId);
                
                if (!kontrakId) {
                    safeShowNotification('Data kontrak tidak tersedia. Pastikan halaman sudah dimuat dengan benar.', 'error');
                    return;
                }
                
                // kontrak_spesifikasi_id can be 0 or null when creating SPK from contract without specific spec
                if (!kontrakSpesifikasiId && kontrakSpesifikasiId !== '0' && kontrakSpesifikasiId !== 0) {
                    console.log('kontrak_spesifikasi_id is empty, but kontrak_id is provided - this is OK');
                }
                if (!formData.get('pelanggan')) {
                    safeShowNotification('Data pelanggan tidak tersedia. Pastikan detail kontrak sudah dimuat.', 'error');
                    return;
                }
                if (!formData.get('delivery_plan') || !formData.get('jumlah_unit')) {
                    safeShowNotification('Lengkapi semua field wajib.', 'error');
                    return;
                }
                
                // Client-side limit based on available units
                const available = Number(spkForm.dataset.availableUnits || '0');
                const jumlah = Number(formData.get('jumlah_unit'));
                if (available > 0 && jumlah > available) {
                    safeShowNotification('Jumlah unit melebihi yang dibutuhkan. Maksimal: ' + available, 'error');
                    return;
                }
                
                // Log form data for debugging
                console.log('SPK Form Data:');
                for (let [key, value] of formData.entries()) {
                    console.log(key + ': ' + value);
                }
                
                fetch('<?= base_url('marketing/spk/create') ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(response => {
                    console.log('SPK Create Response Status:', response.status);
                    return response.json();
                })
                .then(res => {
                    console.log('SPK Create Response:', res);
                    if (res.success) {
                        safeShowNotification('SPK berhasil dibuat!', 'success');
                        const modalEl = document.getElementById('spkFromKontrakModal');
                        bootstrap.Modal.getInstance(modalEl)?.hide();
                        if (window.currentKontrakId) loadContractSpesifikasi(window.currentKontrakId);
                    } else {
                        safeShowNotification(res.message || 'Gagal membuat SPK.', 'error');
                    }
                })
                .catch(err => {
                    safeShowNotification('Gagal membuat SPK: ' + err, 'error');
                });
            });
        }
    }
});

function editContract(contractId) {
    // Validate contract ID
    if (!contractId || contractId == '0' || contractId == 0) {
        safeShowNotification('ID kontrak tidak valid.', 'error');
        return;
    }
    
    // Load data kontrak untuk edit
    $.ajax({
        url: `<?= base_url('marketing/kontrak/detail/') ?>${contractId}`,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const contract = response.data;
                // Populate form with existing data
                $('input[name="contract_number"]').val(contract.no_kontrak);
                $('input[name="po_number"]').val(contract.no_po_marketing);
                $('input[name="client_name"]').val(contract.pelanggan);
                $('input[name="lokasi"]').val(contract.lokasi);
                $('input[name="start_date"]').val(contract.tanggal_mulai);
                $('input[name="end_date"]').val(contract.tanggal_berakhir);
                $('input[name="contract_value"]').val(contract.nilai_total);
                $('input[name="total_units"]').val(contract.total_units || 0);
                $('input[name="pic"]').val(contract.pic);
                $('input[name="kontak"]').val(contract.kontak);
                $('select[name="status"]').val(contract.status);
                
                // Change form action to update
                $('#addContractForm').attr('action', 'update');
                $('#addContractForm').data('contract-id', contractId);
                $('#addContractModal .modal-title').text('Edit Kontrak');
                $('#addContractModal').modal('show');
            } else {
                safeShowNotification(response.message, 'error');
            }
        },
        error: function() {
            safeShowNotification('Gagal memuat data kontrak.', 'error');
        }
    });
}

function deleteContract(contractId) {
    // Validate contract ID
    if (!contractId || contractId == '0' || contractId == 0) {
        safeShowNotification('ID kontrak tidak valid.', 'error');
        return;
    }
    
    // Use simple confirm dialog for testing
    if (confirm('Konfirmasi Hapus\n\nApakah Anda yakin ingin menghapus kontrak ini?')) {
        performDelete(contractId);
    }
}

function performDelete(contractId) {
    console.log('Attempting to delete contract with ID:', contractId);
    console.log('CSRF Token:', '<?= csrf_token() ?>');
    console.log('CSRF Hash:', '<?= csrf_hash() ?>');
    
    $.ajax({
        url: `<?= base_url('marketing/kontrak/delete/') ?>${contractId}`,
        method: 'POST',
        data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
        dataType: 'json',
        success: function(response) {
            console.log('Delete response:', response);
            if (response.success) {
                safeShowNotification(response.message, 'success');
                // Safely reload DataTable
                if (typeof window.contractsTable !== 'undefined' && window.contractsTable) {
                    window.contractsTable.ajax.reload();
                } else {
                    console.warn('window.contractsTable is undefined, trying direct DataTable access');
                    $('#contractsTable').DataTable().ajax.reload();
                }
            } else {
                safeShowNotification(response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Delete AJAX error:', xhr, status, error);
            console.log('Status code:', xhr.status);
            console.log('Response headers:', xhr.getAllResponseHeaders());
            console.log('Response text:', xhr.responseText);
            
            // Try to parse error response
            try {
                const errorData = JSON.parse(xhr.responseText);
                console.log('Parsed error response:', errorData);
                if (errorData.message) {
                    safeShowNotification('Error: ' + errorData.message, 'error');
                } else {
                    safeShowNotification('Gagal menghapus kontrak.', 'error');
                }
            } catch (e) {
                console.log('Could not parse error response');
                safeShowNotification('Gagal menghapus kontrak.', 'error');
            }
        }
    });
}

// Tab event handlers for lazy loading
// Note: Tab event handlers are now setup in setupTabEventHandlers() function
// which is called when modal is shown to ensure DOM elements are available

// Load contract units for units tab
function loadContractUnits(contractId) {
    $.ajax({
        url: `<?= base_url('marketing/kontrak/units/') ?>${contractId}`,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let unitsHtml = '';
                if (response.data.length > 0) {
                    unitsHtml = '<div class="table-responsive">';
                    unitsHtml += '<table class="table table-sm table-striped">';
                    unitsHtml += '<thead><tr><th>No Unit</th><th>Spesifikasi</th><th>Merk/Model</th><th>Departemen</th><th>Status Sewa</th><th>Harga</th></tr></thead>';
                    unitsHtml += '<tbody>';
                    
                    response.data.forEach(unit => {
                        const harga = unit.jenis_sewa === 'harian' ? 
                            `Rp ${OptimaPro.formatCurrency(unit.harga_sewa_harian)}/hari` : 
                            `Rp ${OptimaPro.formatCurrency(unit.harga_sewa_bulanan)}/bulan`;
                        
                        unitsHtml += `<tr>
                            <td>${unit.nomor_unit}</td>
                            <td>${unit.spek_kode || '-'}</td>
                            <td>${unit.merk_unit || ''} ${unit.model_unit || ''}</td>
                            <td>${unit.departemen_nama || '-'}</td>
                            <td><span class="badge bg-success">Disewa</span></td>
                            <td>${harga}</td>
                        </tr>`;
                    });
                    
                    unitsHtml += '</tbody></table></div>';
                } else {
                    unitsHtml = '<div class="alert alert-info">Belum ada unit yang di-assign ke kontrak ini.</div>';
                }
                
                $('#unitsList').html(unitsHtml);
                $('#unitsCount').text(response.data.length);
            } else {
                $('#unitsList').html('<div class="alert alert-danger">Gagal memuat unit: ' + response.message + '</div>');
            }
        },
        error: function() {
            $('#unitsList').html('<div class="alert alert-danger">Gagal memuat unit.</div>');
        }
    });
}

// Open add spesifikasi modal
function openAddSpesifikasiModal() {
    const contractId = window.currentKontrakId;
    if (!contractId) {
        safeShowNotification('ID kontrak tidak ditemukan. Silakan buka detail kontrak terlebih dahulu.', 'error');
        return;
    }
    
    console.log('Opening add spesifikasi modal for contract ID:', contractId);
    
    // Reset form
    $('#addSpesifikasiForm')[0].reset();
    $('#spekKontrakId').val(contractId);
    
    // Clear specific fields that might not be cleared by reset()
    $('#spekModelUnit').val('');
    $('#spekAttachmentMerk').val('');
    
    // Uncheck all accessories
    $('input[name="aksesoris[]"]').prop('checked', false);
    
    // Remove edit mode hidden field if exists
    $('#spekEditId').remove();
    
    // Reset modal title and button text
    $('#addSpesifikasiModal .modal-title').text('Tambah Spesifikasi Unit');
    $('#submitSpesifikasiBtn').text('Simpan Spesifikasi');
    
    console.log('Kontrak ID set to form:', $('#spekKontrakId').val()); // Debug
    
    // Reset dropdown states
    $('#spekCharger').prop('disabled', false);
    $('#spekJenisBaterai').prop('disabled', false);
    
    // Load dropdowns
    loadSpesifikasiDropdowns();
    
    // Show modal
    $('#addSpesifikasiModal').modal('show');
}

// Load dropdown options for spesifikasi form
function loadSpesifikasiDropdowns() {
    // Helper function to fill select options
    function fillSelect(selector, items, nameField = 'name') {
        const selectElement = document.querySelector(selector);
        if (!selectElement) return;
        
        const placeholder = selector.includes('Departemen') ? '-- Pilih Departemen --' :
                           selector.includes('TipeUnit') ? '-- Pilih Tipe Unit --' :
                           selector.includes('JenisUnit') ? '-- Pilih Jenis Unit --' :
                           selector.includes('Kapasitas') ? '-- Pilih Kapasitas --' :
                           selector.includes('Charger') ? '-- Pilih Charger --' :
                           selector.includes('Mast') ? '-- Pilih Mast --' :
                           selector.includes('Ban') ? '-- Pilih Ban --' :
                           selector.includes('Roda') ? '-- Pilih Roda --' :
                           selector.includes('Valve') ? '-- Pilih Valve --' : '-- Pilih --';
        
        let options = `<option value="">${placeholder}</option>`;
        if (items && items.length > 0) {
            items.forEach(item => {
                const displayName = item[nameField] || item.nama || item.kapasitas || item.jenis || item.tipe || item.id;
                options += `<option value="${item.id}">${displayName}</option>`;
            });
        }
        selectElement.innerHTML = options;
    }
    
    // Load initial dropdowns
    const initialTypes = [
        { type: 'departemen', selector: '#spekDepartemen' },
        { type: 'kapasitas', selector: '#spekKapasitas' },
        { type: 'merk_unit', selector: '#spekMerkUnit' },
        { type: 'jenis_baterai', selector: '#spekJenisBaterai' },
        { type: 'attachment_tipe', selector: '#spekAttachmentTipe' },
        { type: 'mast', selector: '#spekMast' },
        { type: 'ban', selector: '#spekBan' },
        { type: 'roda', selector: '#spekRoda' },
        { type: 'valve', selector: '#spekValve' }
    ];
    
    initialTypes.forEach(spec => {
        fetch(`<?= base_url('marketing/spk/spec-options') ?>?type=${spec.type}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fillSelect(spec.selector, data.data || []);
                } else {
                    console.warn(`Failed to load ${spec.type}:`, data.message);
                }
            })
            .catch(error => {
                console.error(`Error loading ${spec.type}:`, error);
            });
    });
    
    // Load departemen first, then set up cascading dropdowns
    fetch(`<?= base_url('marketing/spk/spec-options') ?>?type=departemen`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fillSelect('#spekDepartemen', data.data || []);
                setupCascadingDropdowns();
            }
        })
        .catch(error => {
            console.error('Error loading departemen:', error);
        });
}

// Setup cascading dropdown behavior
function setupCascadingDropdowns() {
    // Departemen change - affects tipe_unit, baterai, charger
    $('#spekDepartemen').on('change', function() {
        const departemenId = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const departemenText = selectedOption ? selectedOption.text : '';
        
        // Clear dependent dropdowns
        $('#spekTipeUnit').html('<option value="">-- Pilih Tipe Unit --</option>');
        $('#spekJenisUnit').html('<option value="">-- Pilih Jenis Unit --</option>');
        $('#spekCharger').html('<option value="">-- Pilih Charger --</option>');
        $('#spekJenisBaterai').html('<option value="">-- Pilih Jenis Baterai --</option>');
        
        if (departemenId) {
            // Load tipe_unit (DISTINCT tipe only)
            fetch(`<?= base_url('marketing/spk/spec-options') ?>?type=tipe_unit`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fillSelectOptions('#spekTipeUnit', data.data || []);
                    }
                });
            
            // Check if electric department for baterai & charger
            const isElectric = departemenText.toLowerCase().includes('electric') || 
                              departemenText.toLowerCase().includes('listrik');
            
            if (isElectric) {
                // Load charger for electric units
                fetch(`<?= base_url('marketing/spk/spec-options') ?>?type=charger&departemen_id=${departemenId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            fillSelectOptions('#spekCharger', data.data || []);
                        }
                    });
                
                // Load jenis baterai for electric units
                fetch(`<?= base_url('marketing/spk/spec-options') ?>?type=jenis_baterai`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            fillSelectOptions('#spekJenisBaterai', data.data || []);
                        }
                    });
                    
                // Enable baterai and charger fields
                $('#spekCharger').prop('disabled', false);
                $('#spekJenisBaterai').prop('disabled', false);
            } else {
                // Disable and clear baterai and charger for non-electric
                $('#spekCharger').prop('disabled', true);
                $('#spekJenisBaterai').prop('disabled', true);
                $('#spekCharger').html('<option value="">-- Tidak tersedia untuk unit non-electric --</option>');
                $('#spekJenisBaterai').html('<option value="">-- Tidak tersedia untuk unit non-electric --</option>');
            }
        } else {
            // Reset all fields if no departemen selected
            $('#spekCharger').prop('disabled', false);
            $('#spekJenisBaterai').prop('disabled', false);
        }
    });
    
    // Tipe Unit change - affects jenis unit
    $('#spekTipeUnit').on('change', function() {
        const tipeUnitId = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const tipeUnitName = selectedOption ? selectedOption.text : '';
        
        // Clear jenis unit
        $('#spekJenisUnit').html('<option value="">-- Pilih Jenis Unit --</option>');
        
        if (tipeUnitId && tipeUnitName !== '-- Pilih Tipe Unit --') {
            // Send tipe name instead of ID for backend filtering
            fetch(`<?= base_url('marketing/spk/spec-options') ?>?type=jenis_unit&parent_tipe=${encodeURIComponent(tipeUnitName)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fillSelectOptions('#spekJenisUnit', data.data || []);
                    }
                })
                .catch(error => {
                    console.error('Error loading jenis unit:', error);
                });
        }
    });
}

// Helper function to fill select options
function fillSelectOptions(selector, items, nameField = 'name') {
    const selectElement = document.querySelector(selector);
    if (!selectElement) return;
    
    const currentValue = selectElement.value;
    const placeholder = selectElement.querySelector('option[value=""]')?.textContent || '-- Pilih --';
    
    let options = `<option value="">${placeholder}</option>`;
    if (items && items.length > 0) {
        items.forEach(item => {
            const displayName = item[nameField] || item.nama || item.kapasitas || item.jenis || item.tipe || item.id;
            const selected = item.id == currentValue ? 'selected' : '';
            options += `<option value="${item.id}" ${selected}>${displayName}</option>`;
        });
    }
    selectElement.innerHTML = options;
}

// ===== DUPLICATE BLOCK REMOVED - MOVED TO MAIN DOCUMENT.READY =====

// Debug functions removed - no longer needed

// Edit spesifikasi
function editSpesifikasi(spekId) {
    const contractId = window.currentKontrakId;
    if (!contractId) {
        safeShowNotification('ID kontrak tidak ditemukan. Silakan buka detail kontrak terlebih dahulu.', 'error');
        return;
    }
    
    // Fetch spesifikasi data for editing
    fetch(`<?= base_url('marketing/kontrak/spesifikasi-detail/') ?>${spekId}`)
        .then(response => response.json())
        .then(j => {
            if (!j.success) {
                safeShowNotification('Gagal memuat data spesifikasi: ' + j.message, 'error');
                return;
            }
            
            const spek = j.data || {};
            
            // Populate form with existing data
            $('#spekKontrakId').val(spek.kontrak_id);
            $('input[name="jumlah_dibutuhkan"]').val(spek.jumlah_dibutuhkan);
            $('input[name="catatan_spek"]').val(spek.catatan_spek);
            $('input[name="harga_per_unit_bulanan"]').val(spek.harga_per_unit_bulanan);
            $('input[name="harga_per_unit_harian"]').val(spek.harga_per_unit_harian);
            
            // Set hidden field for edit mode
            if (!$('#spekEditId').length) {
                $('#addSpesifikasiForm').prepend('<input type="hidden" name="spek_id" id="spekEditId">');
            }
            $('#spekEditId').val(spekId);
            
            // Load dropdowns first, then set values
            loadSpesifikasiDropdowns();
            
            // Set dropdown values after a short delay to ensure options are loaded
            setTimeout(() => {
                // Set departemen first without triggering change to avoid clearing other dropdowns
                if (spek.departemen_id) $('#spekDepartemen').val(spek.departemen_id);
                
                // Load all required dropdown data for this spesifikasi
                const loadPromises = [];
                
                // Load tipe_unit options
                if (spek.departemen_id) {
                    loadPromises.push(
                        fetch(`<?= base_url('marketing/spk/spec-options') ?>?type=tipe_unit`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    fillSelectOptions('#spekTipeUnit', data.data || []);
                                    if (spek.tipe_unit_id) $('#spekTipeUnit').val(spek.tipe_unit_id);
                                }
                            })
                    );
                }
                
                // Load jenis_unit options
                if (spek.tipe_unit_id) {
                    // Get tipe unit name from current data
                    const tipeUnitText = spek.tipe_unit_nama || spek.tipe_unit || '';
                    if (tipeUnitText) {
                        loadPromises.push(
                            fetch(`<?= base_url('marketing/spk/spec-options') ?>?type=jenis_unit&parent_tipe=${encodeURIComponent(tipeUnitText)}`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        fillSelectOptions('#spekJenisUnit', data.data || []);
                                        if (spek.tipe_jenis) $('#spekJenisUnit').val(spek.tipe_jenis);
                                    }
                                })
                        );
                    }
                }
                
                // Wait for all dropdowns to load, then set values
                Promise.all(loadPromises).then(() => {
                    // Set other dropdown values
                    if (spek.kapasitas_id) $('#spekKapasitas').val(spek.kapasitas_id);
                    if (spek.merk_unit) $('#spekMerkUnit').val(spek.merk_unit);
                    if (spek.jenis_baterai) $('#spekJenisBaterai').val(spek.jenis_baterai);
                    if (spek.attachment_tipe) $('#spekAttachmentTipe').val(spek.attachment_tipe);
                    if (spek.charger_id) $('#spekCharger').val(spek.charger_id);
                    if (spek.mast_id) $('#spekMast').val(spek.mast_id);
                    if (spek.ban_id) $('#spekBan').val(spek.ban_id);
                    if (spek.roda_id) $('#spekRoda').val(spek.roda_id);
                    if (spek.valve_id) $('#spekValve').val(spek.valve_id);
                    if (spek.model_unit) $('#spekModelUnit').val(spek.model_unit);
                    if (spek.attachment_merk) $('#spekAttachmentMerk').val(spek.attachment_merk);
                });
                
                // Handle accessories checkboxes
                // First uncheck all checkboxes
                $('input[name="aksesoris[]"]').prop('checked', false);
                
                // Then check the selected ones
                if (spek.aksesoris) {
                    let aksesoris;
                    try {
                        aksesoris = typeof spek.aksesoris === 'string' ? JSON.parse(spek.aksesoris) : spek.aksesoris;
                    } catch (e) {
                        console.warn('Failed to parse aksesoris JSON:', spek.aksesoris);
                        aksesoris = [];
                    }
                    
                    if (Array.isArray(aksesoris)) {
                        aksesoris.forEach(function(acc) {
                            $(`input[name="aksesoris[]"][value="${acc}"]`).prop('checked', true);
                        });
                    }
                }
            }, 500);
            
            // Change modal title and button text
            $('#addSpesifikasiModal .modal-title').text('Edit Spesifikasi Unit');
            $('#submitSpesifikasiBtn').text('Update Spesifikasi');
            
            // Show modal
            $('#addSpesifikasiModal').modal('show');
        })
        .catch(error => {
            console.error('Error loading spesifikasi detail:', error);
            safeShowNotification('Gagal memuat data spesifikasi: ' + error.message, 'error');
        });
}

// Delete spesifikasi
function deleteSpesifikasi(spekId) {
    console.log('deleteSpesifikasi called with spekId:', spekId);
    
    if (!spekId) {
        safeShowNotification('ID spesifikasi tidak valid', 'error');
        return;
    }
    
    // Use native confirm for now
    if (!confirm('Apakah Anda yakin ingin menghapus spesifikasi ini?')) {
        return;
    }
    
    const url = `<?= base_url('marketing/kontrak/delete-spesifikasi/') ?>${spekId}`;
    console.log('Delete URL:', url);
    
    // Get fresh CSRF token
    const csrfName = '<?= csrf_token() ?>';
    const csrfHash = $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
    
    const formData = {};
    formData[csrfName] = csrfHash;
    
    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        dataType: 'json',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        beforeSend: function() {
            console.log('Sending delete request with data:', formData);
        },
        success: function(response) {
            console.log('Delete response:', response);
            
            // Update CSRF token if provided
            if (response.csrf_hash) {
                $('meta[name="csrf-token"]').attr('content', response.csrf_hash);
            }
            
            if (response.success) {
                safeShowNotification(response.message, 'success');
                
                // Reload spesifikasi tab
                const contractId = window.currentKontrakId;
                if (contractId) {
                    loadContractSpesifikasi(contractId);
                } else {
                    console.warn('No contract ID found for reload');
                }
            } else {
                safeShowNotification(response.message || 'Gagal menghapus spesifikasi', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Delete AJAX error:', {xhr, status, error});
            console.error('Response text:', xhr.responseText);
            console.error('Status code:', xhr.status);
            
            let errorMessage = 'Terjadi kesalahan pada sistem';
            
            if (xhr.status === 403) {
                errorMessage = 'CSRF token tidak valid. Silakan refresh halaman dan coba lagi.';
            } else if (xhr.status === 404) {
                errorMessage = 'Endpoint tidak ditemukan.';
            } else if (xhr.status === 500) {
                errorMessage = 'Terjadi kesalahan server internal.';
            } else {
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMessage = errorResponse.message;
                    }
                } catch (e) {
                    console.warn('Could not parse error response as JSON');
                }
            }
            
            safeShowNotification(errorMessage, 'error');
        }
    });
}

// Create SPK for specific specification
function createSPKForSpec(spekId) {
    // Redirect to SPK creation page with pre-selected spesifikasi
    // Use runtime JS values: window.currentKontrakId should be set when viewing a contract
    const kontrakId = window.currentKontrakId || document.getElementById('currentKontrakId')?.value;
    if (!kontrakId) {
        console.error('createSPKForSpec: kontrak id not available');
        safeShowNotification('Kontrak belum dipilih. Buka detail kontrak terlebih dahulu.', 'error');
        return;
    }
    const base = '<?= rtrim(base_url(), "\/") ?>';
    window.location.href = `${base}/marketing/spk?kontrak_id=${encodeURIComponent(kontrakId)}&spesifikasi_id=${encodeURIComponent(spekId)}`;
}
</script>
<!-- Contract Detail Modal with Multi-Specification Support -->
<div class="modal fade" id="contractDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Detail Kontrak</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" id="contractDetailTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-content" type="button" role="tab">
                            <i class="fas fa-info-circle me-1"></i>Info Kontrak
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="spesifikasi-tab" data-bs-toggle="tab" data-bs-target="#spesifikasi-content" type="button" role="tab">
                            <i class="fas fa-cogs me-1"></i>Spesifikasi (<span id="spekCount">0</span>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="units-tab" data-bs-toggle="tab" data-bs-target="#units-content" type="button" role="tab">
                            <i class="fas fa-truck me-1"></i>Unit Terkait (<span id="unitsCount">0</span>)
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content mt-3" id="contractDetailTabContent">
                    <!-- Info Kontrak Tab -->
                    <div class="tab-pane fade show active" id="info-content" role="tabpanel">
                        <div id="contractDetailBody">
                            <p class="text-muted">Memuat...</p>
                        </div>
                    </div>

                    <!-- Spesifikasi Tab -->
                    <div class="tab-pane fade" id="spesifikasi-content" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Spesifikasi Unit</h6>
                            <button class="btn btn-primary btn-sm" onclick="openAddSpesifikasiModal()">
                                <i class="fas fa-plus me-1"></i>Tambah Spesifikasi
                            </button>
                        </div>
                        
                        <div id="spesifikasiList">
                            <p class="text-muted">Memuat spesifikasi...</p>
                        </div>
                    </div>

                    <!-- Units Tab -->
                    <div class="tab-pane fade" id="units-content" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Unit yang Terkait (via SPK)</h6>
                            <a href="/marketing/spk" class="btn btn-primary btn-sm">
                                <i class="fas fa-file-alt me-1"></i>Kelola SPK
                            </a>
                        </div>
                        
                        <div id="unitsList">
                            <p class="text-muted">Memuat unit...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Spesifikasi Modal -->
<div class="modal fade" id="addSpesifikasiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Tambah Spesifikasi Unit</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSpesifikasiForm" method="post" action="javascript:void(0)">
                <div class="modal-body">
                    <input type="hidden" name="kontrak_id" id="spekKontrakId">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Unit Dibutuhkan</label>
                            <input type="number" class="form-control" name="jumlah_dibutuhkan" min="1" value="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Catatan Spesifikasi</label>
                            <input type="text" class="form-control" name="catatan_spek" placeholder="Opsional">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Harga Sewa Bulanan</label>
                            <input type="number" class="form-control" name="harga_per_unit_bulanan" step="0.01" placeholder="Rp per unit per bulan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Harga Sewa Harian</label>
                            <input type="number" class="form-control" name="harga_per_unit_harian" step="0.01" placeholder="Rp per unit per hari">
                        </div>
                        
                        <!-- Debug Test Button -->


                        <div class="col-12"><hr><h6>Spesifikasi Teknis</h6></div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Departemen <span class="text-danger">*</span></label>
                            <select class="form-select" name="departemen_id" id="spekDepartemen" required></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tipe Unit <span class="text-danger">*</span></label>
                            <select class="form-select" name="tipe_unit_id" id="spekTipeUnit" required>
                                <option value="">-- Pilih Tipe Unit --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jenis Unit <span class="text-danger">*</span></label>
                            <select class="form-select" name="tipe_jenis" id="spekJenisUnit" required>
                                <option value="">-- Pilih Jenis Unit --</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Kapasitas</label>
                            <select class="form-select" name="kapasitas_id" id="spekKapasitas">
                                <option value="">-- Pilih Kapasitas --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Merk Unit</label>
                            <select class="form-select" name="merk_unit" id="spekMerkUnit">
                                <option value="">-- Pilih Merk --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jenis Baterai</label>
                            <select class="form-select" name="jenis_baterai" id="spekJenisBaterai">
                                <option value="">-- Pilih Jenis Baterai --</option>
                            </select>
                            <small class="text-muted">Hanya tersedia untuk unit Electric</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Attachment Tipe</label>
                            <select class="form-select" name="attachment_tipe" id="spekAttachmentTipe"></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Charger</label>
                            <select class="form-select" name="charger_id" id="spekCharger"></select>
                            <small class="text-muted">Hanya tersedia untuk unit Electric</small>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Mast</label>
                            <select class="form-select" name="mast_id" id="spekMast"></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ban</label>
                            <select class="form-select" name="ban_id" id="spekBan"></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Roda</label>
                            <select class="form-select" name="roda_id" id="spekRoda"></select>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label">Valve</label>
                            <select class="form-select" name="valve_id" id="spekValve"></select>
                        </div>
                        
                        <!-- Accessories Section -->
                        <div class="col-12"><hr><h6>Aksesoris Unit</h6></div>
                        <div class="col-12">
                            <div class="row g-2">
                                <!-- Row 1 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="LAMPU UTAMA" id="acc_lampu_utama">
                                        <label class="form-check-label" for="acc_lampu_utama">Lampu</label>
                                        <small class="text-muted">(Utama, Mundur, Sign, Stop)</small>
                                    </div>
                                </div>
                                
                                <!-- Row 2 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="BLUE SPOT" id="acc_blue_spot">
                                        <label class="form-check-label" for="acc_blue_spot">Blue Spot</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="RED LINE" id="acc_red_line">
                                        <label class="form-check-label" for="acc_red_line">Red Line</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="WORK LIGHT" id="acc_work_light">
                                        <label class="form-check-label" for="acc_work_light">Work Light</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="ROTARY LAMP" id="acc_rotary_lamp">
                                        <label class="form-check-label" for="acc_rotary_lamp">Rotary Lamp</label>
                                    </div>
                                </div>
                                
                                <!-- Row 3 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="BACK BUZZER" id="acc_back_buzzer">
                                        <label class="form-check-label" for="acc_back_buzzer">Back Buzzer</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="CAMERA AI" id="acc_camera_ai">
                                        <label class="form-check-label" for="acc_camera_ai">Camera AI</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="CAMERA" id="acc_camera">
                                        <label class="form-check-label" for="acc_camera">Camera</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="SENSOR PARKING" id="acc_sensor_parking">
                                        <label class="form-check-label" for="acc_sensor_parking">Sensor Parking</label>
                                    </div>
                                </div>
                                
                                <!-- Row 4 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="SPEED LIMITER" id="acc_speed_limiter">
                                        <label class="form-check-label" for="acc_speed_limiter">Speed Limiter</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="LASER FORK" id="acc_laser_fork">
                                        <label class="form-check-label" for="acc_laser_fork">Laser Fork</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="VOICE ANNOUNCER" id="acc_voice_announcer">
                                        <label class="form-check-label" for="acc_voice_announcer">Voice Announcer</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="HORN SPEAKER" id="acc_horn_speaker">
                                        <label class="form-check-label" for="acc_horn_speaker">Horn Speaker</label>
                                    </div>
                                </div>
                                
                                <!-- Row 5 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="HORN KLASON" id="acc_horn_klason">
                                        <label class="form-check-label" for="acc_horn_klason">Horn Klason</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="BIO METRIC" id="acc_bio_metric">
                                        <label class="form-check-label" for="acc_bio_metric">Bio Metric</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="ACRYLIC" id="acc_acrylic">
                                        <label class="form-check-label" for="acc_acrylic">Acrylic</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="APAR 1 KG" id="acc_apar_1kg">
                                        <label class="form-check-label" for="acc_apar_1kg">APAR 1 KG</label>
                                    </div>
                                </div>
                                
                                <!-- Row 6 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="APAR 3 KG" id="acc_apar_3kg">
                                        <label class="form-check-label" for="acc_apar_3kg">APAR 3 KG</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="P3K" id="acc_p3k">
                                        <label class="form-check-label" for="acc_p3k">P3K</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="SAFETY BELT INTERLOC" id="acc_safety_belt">
                                        <label class="form-check-label" for="acc_safety_belt">Safety Belt Interloc</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="BEACON" id="acc_beacon">
                                        <label class="form-check-label" for="acc_beacon">Beacon</label>
                                    </div>
                                </div>
                                
                                <!-- Row 7 -->
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="TELEMATIC" id="acc_telematic">
                                        <label class="form-check-label" for="acc_telematic">Telematic</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="aksesoris[]" value="SPARS ARRESTOR" id="acc_spars_arrestor">
                                        <label class="form-check-label" for="acc_spars_arrestor">Spars Arrestor</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitSpesifikasiBtn">Simpan Spesifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
