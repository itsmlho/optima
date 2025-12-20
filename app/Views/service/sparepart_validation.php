<!-- Sparepart Validation Modal -->
<div class="modal fade" id="sparepartValidationModal" tabindex="-1" aria-labelledby="sparepartValidationModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-black">
                <h5 class="modal-title" id="sparepartValidationModalLabel">
                    <i class="fas fa-tools me-2"></i>Validasi Sparepart - WO: <span id="sparepart-wo-number">Loading...</span>
                </h5>
                <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

<!-- Custom CSS untuk Bootstrap Select di dalam modal -->
<style>
.modal .bootstrap-select .dropdown-menu {
    z-index: 99999 !important;
    position: absolute !important;
}
.modal .bootstrap-select.show > .dropdown-menu {
    z-index: 99999 !important;
}
.bootstrap-select .dropdown-menu {
    max-height: 300px !important;
    overflow-y: auto !important;
}
</style>
            <div class="modal-body">
                <!-- Info Panel -->
                <div class="alert alert-info">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clipboard-list text-primary me-2"></i>
                                <div>
                                    <strong>Sparepart Digunakan</strong><br>
                                    <small>Validasi sparepart yang direncanakan vs yang benar-benar digunakan</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-plus-square text-success me-2"></i>
                                <div>
                                    <strong>Sparepart Tambahan</strong><br>
                                    <small>Sparepart yang diambil tambahan dari gudang (tidak direncanakan)</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-12 mb-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle text-info me-2"></i>
                                <div>
                                    <strong>Cara Kerja</strong><br>
                                    <small>Centang jika quantity sesuai | Ubah quantity jika berbeda | Tambahkan sparepart tambahan jika ada</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" id="sparepartTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="used-sparepart-tab" data-bs-toggle="tab" data-bs-target="#used-sparepart" type="button" role="tab">
                            <i class="fas fa-check-circle me-1"></i>Validasi Sparepart <span id="used-count" class="badge bg-secondary">0</span>
                        </button>
                    </li>
                </ul>

                <form id="sparepartValidationForm">
                    <input type="hidden" id="sparepart-work-order-id" name="work_order_id">
                    
                    <!-- Tab Content -->
                    <div class="tab-content mt-3" id="sparepartTabContent">
                        <!-- Used Sparepart Tab -->
                        <div class="tab-pane fade show active" id="used-sparepart" role="tabpanel">
                            <!-- Sparepart Digunakan -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-list-check me-2"></i>Validasi Sparepart yang Digunakan</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0" id="usedSparepartTable">
                                            <thead class="table-header">
                                                <tr>
                                                    <th width="8%" class="text-center">✅</th>
                                                    <th width="35%">Nama Sparepart</th>
                                                    <th width="12%" class="text-center">Dibawa</th>
                                                    <th width="15%" class="text-center">Digunakan</th>
                                                    <th width="15%" class="text-center">Status</th>
                                                    <th width="15%">Catatan</th>
                                                </tr>
                                            </thead>
                                            <tbody id="usedSparepartTableBody">
                                                <!-- Dynamic content will be loaded here -->
                                                <tr id="no-used-sparepart">
                                                    <td colspan="6" class="text-center text-muted py-3">
                                                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                                        Tidak ada sparepart yang direncanakan untuk WO ini
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Sparepart Tambahan -->
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-plus-square me-2 text-success"></i>Sparepart Tambahan</h6>
                                    <button type="button" class="btn btn-sm btn-success" id="btn-add-sparepart-row">
                                        <i class="fas fa-plus me-1"></i>Tambah Baris
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0" id="additionalSparepartTable">
                                            <thead class="table-header">
                                                <tr>
                                                    <th width="40%">Nama Sparepart</th>
                                                    <th width="15%" class="text-center">Quantity</th>
                                                    <th width="15%">Satuan</th>
                                                    <th width="20%">Catatan</th>
                                                    <th width="10%" class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="additionalSparepartTableBody">
                                                <!-- Dynamic content will be loaded here -->
                                                <tr id="no-additional-sparepart">
                                                    <td colspan="5" class="text-center text-muted py-3">
                                                        <i class="fas fa-plus-circle fa-2x mb-2"></i><br>
                                                        Belum ada sparepart tambahan. Klik "Tambah Baris" untuk menambah.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-lg-8 col-md-7">
                                    <h6 class="mb-3"><i class="fas fa-clipboard-list me-2 text-primary"></i>Ringkasan Validasi</h6>
                                    <div id="validation-summary">
                                        <small class="text-muted">Data akan muncul setelah validasi sparepart...</small>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-5">
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-success btn-lg" id="btn-save-sparepart-validation">
                                            <i class="fas fa-check me-2"></i>Simpan & Tutup WO
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                            <i class="fas fa-times me-2"></i>Batal
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<script>
$(document).ready(function() {
    console.log('🔧 Sparepart Validation JavaScript loaded');
    
    // Global variables
    let additionalSparepartCounter = 0;
    let sparepartMasterData = [];

    /**
     * Open Sparepart Validation Modal
     */
    window.openSparepartValidationModal = function(workOrderId, woNumber) {
        console.log('📝 Opening sparepart validation modal for WO:', workOrderId, woNumber);
        
        // Reset modal
        resetSparepartValidationModal();
        
        // Set WO information
        $('#sparepart-work-order-id').val(workOrderId);
        $('#sparepart-wo-number').text(woNumber || workOrderId);
        
        // Load sparepart data
        loadSparepartValidationData(workOrderId);
        
        // Show modal
        $('#sparepartValidationModal').modal('show');
    };

    /**
     * Load Sparepart Validation Data
     */
    function loadSparepartValidationData(workOrderId) {
        console.log('🔍 Loading sparepart validation data for WO:', workOrderId);
        
        $.ajax({
            url: '<?= base_url('service/work-orders/get-sparepart-validation-data') ?>',
            type: 'GET',
            data: { work_order_id: workOrderId },
            beforeSend: function() {
               
            },
            success: function(response) {
                console.log('📦 Sparepart validation data received:', response);
                if (response.success) {
                    populateSparepartValidationData(response.data);
                    loadSparepartMasterData();
                } else {
                    showSparepartAlert('error', response.message || 'Gagal memuat data sparepart');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error loading sparepart validation data:', error);
                showSparepartAlert('error', 'Terjadi kesalahan saat memuat data sparepart');
            }
        });
    }

    /**
     * Populate Sparepart Validation Data
     */
    function populateSparepartValidationData(data) {
        console.log('📝 Populating sparepart validation data:', data);
        
        let usedSpareparts = data.used_spareparts || [];
        let additionalSpareparts = data.additional_spareparts || [];
        
        // Populate used spareparts
        populateUsedSpareparts(usedSpareparts);
        
        // Populate additional spareparts
        populateAdditionalSpareparts(additionalSpareparts);
        
        // Update counters
        updateSparepartCounters();
        
        // Update validation summary
        updateValidationSummary();
    }

    /**
     * Populate Used Spareparts Table
     */
    function populateUsedSpareparts(spareparts) {
        let tbody = $('#usedSparepartTableBody');
        tbody.empty();
        
        if (spareparts.length === 0) {
            tbody.append(`
                <tr id="no-used-sparepart">
                    <td colspan="6" class="text-center text-muted py-3">
                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                        Tidak ada sparepart yang direncanakan untuk WO ini
                    </td>
                </tr>
            `);
            return;
        }
        
        spareparts.forEach(function(item, index) {
            let status = getQuantityStatus(item.planned_quantity, item.used_quantity);
            let statusBadge = getStatusBadge(status);
            
            let row = `
                <tr>
                    <td class="text-center align-middle">
                        <input type="checkbox" class="form-check-input sparepart-check" 
                               id="check-sparepart-${item.id}" 
                               data-sparepart-id="${item.id}"
                               ${status === 'sesuai' ? 'checked' : ''}>
                    </td>
                    <td class="align-middle">
                        <div class="d-flex flex-column">
                            <strong class="text-dark">${item.sparepart_name}</strong>
                            <small class="text-muted">${item.sparepart_code || ''}</small>
                        </div>
                        <input type="hidden" name="used_spareparts[${index}][id]" value="${item.id}">
                        <input type="hidden" name="used_spareparts[${index}][sparepart_id]" value="${item.sparepart_id}">
                    </td>
                    <td class="text-center align-middle">
                        <span class="text">${item.planned_quantity}</span>
                    </td>
                   <td class="align-middle">
                        <input type="number" class="form-control text-center quantity-input" 
                               name="used_spareparts[${index}][used_quantity]" 
                               value="0" 
                               min="0" max="999"
                               data-planned="${item.planned_quantity}"
                               data-sparepart-id="${item.id}">
                    </td>
                    <td class="text-center align-middle">
                        <span class="status-badge" id="status-${item.id}">${statusBadge}</span>
                    </td>
                    <td class="align-middle">
                        <input type="text" class="form-control" 
                               name="used_spareparts[${index}][notes]" 
                               value="${item.notes || ''}" 
                               placeholder="Catatan...">
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
        
        // Bind quantity change events
        bindQuantityChangeEvents();
    }

    /**
     * Get Quantity Status
     */
    function getQuantityStatus(planned, used) {
        if (used == planned) return 'sesuai';
        if (used < planned) return 'kurang';
        if (used > planned) return 'lebih';
        return 'unknown';
    }

    /**
     * Get Status Badge
     */
    function getStatusBadge(status) {
        switch(status) {
            case 'sesuai': return '<span class="badge bg-success">✅ Sesuai</span>';
            case 'kurang': return '<span class="badge bg-warning">⚠️ Kurang</span>';
            case 'lebih': return '<span class="badge bg-info">📈 Lebih</span>';
            default: return '<span class="badge bg-secondary">❓ Unknown</span>';
        }
    }

    /**
     * Bind Quantity Change Events
     */
    function bindQuantityChangeEvents() {
        $('.quantity-input').on('input change', function() {
            let sparepartId = $(this).data('sparepart-id');
            let planned = parseInt($(this).data('planned'));
            let used = parseInt($(this).val()) || 0;
            
            let status = getQuantityStatus(planned, used);
            let statusBadge = getStatusBadge(status);
            
            // Update status badge
            $(`#status-${sparepartId}`).html(statusBadge);
            
            // Update checkbox
            let checkbox = $(`#check-sparepart-${sparepartId}`);
            checkbox.prop('checked', status === 'sesuai');
            
            // Update validation summary
            updateValidationSummary();
        });
    }

    /**
     * Populate Additional Spareparts Table
     */
    function populateAdditionalSpareparts(spareparts) {
        let tbody = $('#additionalSparepartTableBody');
        tbody.empty();
        
        if (spareparts.length === 0) {
            tbody.append(`
                <tr id="no-additional-sparepart">
                    <td colspan="5" class="text-center text-muted py-3">
                        <i class="fas fa-plus-circle fa-2x mb-2"></i><br>
                        Belum ada sparepart tambahan. Klik "Tambah Sparepart" untuk menambah.
                    </td>
                </tr>
            `);
            return;
        }
        
        spareparts.forEach(function(item, index) {
            addAdditionalSparepartRow(item, index, false);
        });
    }

    /**
     * Update Sparepart Counters
     */
    function updateSparepartCounters() {
        let usedCount = $('#usedSparepartTableBody tr').not('#no-used-sparepart').length;
        let additionalCount = $('#additionalSparepartTableBody tr').not('#no-additional-sparepart').length;
        
        $('#used-count').text(usedCount + (additionalCount > 0 ? ' + ' + additionalCount + ' tambahan' : ''));
    }

    /**
     * Update Validation Summary
     */
    function updateValidationSummary() {
        let totalPlanned = 0;
        let totalUsed = 0;
        let issues = [];
        
        $('.quantity-input').each(function() {
            let planned = parseInt($(this).data('planned')) || 0;
            let used = parseInt($(this).val()) || 0;
            
            totalPlanned += planned;
            totalUsed += used;
            
            if (used !== planned) {
                let sparepartName = $(this).closest('tr').find('strong').text();
                issues.push(`${sparepartName}: ${planned} → ${used}`);
            }
        });
        
        let additionalCount = $('#additionalSparepartTableBody tr').not('#no-additional-sparepart').length;
        
        let summary = `
            <div class="row">
                <div class="col-md-4">
                    <small><strong>Total Sparepart Dibawa:</strong> ${totalPlanned}</small><br>
                    <small><strong>Total Sparepart DIgunakan:</strong> ${totalUsed}</small>
                </div>
                <div class="col-md-4">
                    <small><strong>Sparepart Tambahan:</strong> ${additionalCount}</small><br>
                    <small><strong>Issues:</strong> ${issues.length}</small>
                </div>
                <div class="col-md-4">
                    ${issues.length > 0 ? '<small class="text-warning">⚠️ ' + issues.slice(0, 2).join(', ') + (issues.length > 2 ? '...' : '') + '</small>' : '<small class="text-success">✅ Semua sesuai</small>'}
                </div>
            </div>
        `;
        
        $('#validation-summary').html(summary);
    }

    /**
     * Reset Sparepart Validation Modal
     */
    function resetSparepartValidationModal() {
        console.log('🔄 Resetting sparepart validation modal');
        
        // Reset form
        $('#sparepartValidationForm')[0].reset();
        
        // Clear tables
        $('#usedSparepartTableBody').empty();
        $('#additionalSparepartTableBody').empty();
        
        // Reset counters
        $('#used-count').text('0');
        
        // Reset additional counter
        additionalSparepartCounter = 0;
        
        // Reset validation summary
        $('#validation-summary').html('<small class="text-muted">Data akan muncul setelah validasi sparepart...</small>');
        
        console.log('✅ Sparepart modal reset completed');
    }

    /**
     * Load Sparepart Master Data
     */
    function loadSparepartMasterData() {
        if (sparepartMasterData.length > 0) return; // Already loaded
        
        $.ajax({
            url: '<?= base_url('service/work-orders/get-sparepart-master') ?>',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    sparepartMasterData = response.data;
                    populateSparepartDropdown();
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error loading sparepart master data:', error);
            }
        });
    }

    /**
     * Populate Sparepart Dropdown
     */
    function populateSparepartDropdown() {
        let select = $('#sparepart-search');
        select.empty().append('<option value="">Pilih Sparepart</option>');
        
        sparepartMasterData.forEach(function(item) {
            select.append(`<option value="${item.id}" data-price="${item.price || 0}">${item.name} (${item.code})</option>`);
        });
    }

    // Event Handlers
    
    /**
     * Add Sparepart Row Button
     */
    $('#btn-add-sparepart-row').on('click', function() {
        loadSparepartMasterData();
        addAdditionalSparepartRow({}, additionalSparepartCounter++, true);
        updateSparepartCounters();
    });

    /**
     * Handle sparepart selection change
     */
    $(document).on('changed.bs.select change', '.sparepart-select', function() {
        let selectedOption = $(this).find('option:selected');
        let sparepartName = selectedOption.text();
        let sparepartId = selectedOption.val();
        let sparepartCode = selectedOption.data('code');
        
        // Update hidden input for sparepart_id
        $(this).closest('tr').find('input[name*="[sparepart_id]"]').val(sparepartId);
        
        updateValidationSummary();
    });

    /**
     * Add Additional Sparepart Row
     */
    function addAdditionalSparepartRow(item, index, isNew = false) {
        // Remove no-data row if exists
        $('#no-additional-sparepart').remove();
        
        // Create dropdown options
        let sparepartOptions = '<option value="">Pilih Sparepart...</option>';
        sparepartMasterData.forEach(function(sparepart) {
            let selected = item.sparepart_id && item.sparepart_id == sparepart.id ? 'selected' : '';
            sparepartOptions += `<option value="${sparepart.id}" data-code="${sparepart.code}" ${selected}>${sparepart.name} (${sparepart.code})</option>`;
        });
        
        let row = `
            <tr data-index="${index}">
                <td class="align-middle">
                    <select class="form-select form-select-sm sparepart-select" 
                            name="additional_spareparts[${index}][sparepart_id]" 
                            id="sparepart-select-${index}" 
                            data-live-search="true" 
                            data-size="10" 
                            title="Pilih Sparepart..." 
                            required>
                        ${sparepartOptions}
                    </select>
                </td>
                <td class="align-middle">
                    <input type="number" class="form-control form-control-sm text-center" 
                           name="additional_spareparts[${index}][quantity]" 
                           value="${item.quantity || 1}" min="1" max="999" required>
                </td>
                <td class="align-middle">
                    <select class="form-select form-select-sm" 
                            name="additional_spareparts[${index}][satuan]" 
                            required>
                        <option value="">Pilih Satuan</option>
                        <option value="PCS" ${item.satuan === 'PCS' ? 'selected' : ''}>PCS</option>
                        <option value="SET" ${item.satuan === 'SET' ? 'selected' : ''}>SET</option>
                        <option value="UNIT" ${item.satuan === 'UNIT' ? 'selected' : ''}>UNIT</option>
                        <option value="METER" ${item.satuan === 'METER' ? 'selected' : ''}>METER</option>
                        <option value="LITER" ${item.satuan === 'LITER' ? 'selected' : ''}>LITER</option>
                        <option value="KG" ${item.satuan === 'KG' ? 'selected' : ''}>KG</option>
                        <option value="BOX" ${item.satuan === 'BOX' ? 'selected' : ''}>BOX</option>
                        <option value="ROLL" ${item.satuan === 'ROLL' ? 'selected' : ''}>ROLL</option>
                    </select>
                </td>
                <td class="align-middle">
                    <input type="text" class="form-control form-control-sm" 
                           name="additional_spareparts[${index}][notes]" 
                           value="${item.notes || ''}" placeholder="Catatan...">
                </td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-additional-sparepart" data-index="${index}" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        $('#additionalSparepartTableBody').append(row);
        
        // Initialize Bootstrap Select for searchable dropdown
        setTimeout(function() {
            // Ensure Bootstrap Select is loaded
            if (typeof $.fn.selectpicker === 'undefined') {
                // Load Bootstrap Select CSS and JS
                if (!$('link[href*="bootstrap-select"]').length) {
                    $('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">').appendTo('head');
                    $('<style>.bootstrap-select .dropdown-menu { font-size: 0.875rem !important; z-index: 99999 !important; } .modal .bootstrap-select .dropdown-menu { z-index: 99999 !important; }</style>').appendTo('head');
                }
                
                $.getScript('https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js', function() {
                    initializeSelectPicker(index);
                });
            } else {
                initializeSelectPicker(index);
            }
        }, 300);
        
        function initializeSelectPicker(index) {
            $(`#sparepart-select-${index}`).selectpicker({
                liveSearch: true,
                size: 8,
                dropupAuto: false,
                style: 'btn-outline-secondary btn-sm',
                width: '100%',
                container: 'body'
            });
            
            // Fix z-index for modal dengan delay
            setTimeout(function() {
                $(`#sparepart-select-${index}`).on('shown.bs.select', function() {
                    $('.bootstrap-select .dropdown-menu').css('z-index', '99999');
                    $(this).find('.dropdown-menu').css('z-index', '99999');
                });
            }, 100);
        }

        
        if (isNew) {
            showSparepartAlert('success', 'Baris sparepart tambahan ditambahkan');
        }
    }

    /**
     * Remove Additional Sparepart
     */
    $(document).on('click', '.remove-additional-sparepart', function() {
        let row = $(this).closest('tr');
        let selectElement = row.find('.sparepart-select');
        
        // Destroy Bootstrap Select instance if exists
        if (typeof $.fn.selectpicker !== 'undefined' && selectElement.hasClass('selectpicker')) {
            selectElement.selectpicker('destroy');
        }
        
        row.remove();
        
        // Add no-data row if table is empty
        if ($('#additionalSparepartTableBody tr').length === 0) {
            $('#additionalSparepartTableBody').append(`
                <tr id="no-additional-sparepart">
                    <td colspan="5" class="text-center text-muted py-3">
                        <i class="fas fa-plus-circle fa-2x mb-2"></i><br>
                        Belum ada sparepart tambahan. Klik "Tambah Baris" untuk menambah.
                    </td>
                </tr>
            `);
        }
        
        updateSparepartCounters();
        updateValidationSummary();
    });

    /**
     * Save Sparepart Validation
     */
    $('#btn-save-sparepart-validation').on('click', function() {
        saveSparepartValidation();
    });

    /**
     * Save Sparepart Validation Function
     */
    function saveSparepartValidation() {
        let workOrderId = $('#sparepart-work-order-id').val();
        
        if (!workOrderId) {
            showSparepartAlert('error', 'Work Order ID tidak ditemukan');
            return;
        }
        
        let formData = $('#sparepartValidationForm').serialize();
        console.log('📋 Sparepart validation data being sent:', formData);
        
        $.ajax({
            url: '<?= base_url('service/work-orders/save-sparepart-validation') ?>',
            type: 'POST',
            data: formData,
            beforeSend: function() {
            },
            success: function(response) {
                console.log('✅ Sparepart validation response:', response);
                if (response.success) {
                    // Get work order number from hidden field
                    let woNumber = $('#sparepart-wo-number').text() || $('#sparepart-work-order-id').val();
                    
                    // Close modal immediately
                    $('#sparepartValidationModal').modal('hide');
                    
                    // Show success SweetAlert with auto-close
                    Swal.fire({
                        icon: 'success',
                        title: 'Work Order Ditutup',
                        text: `Work Order ${woNumber} berhasil di-Close`,
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        willClose: () => {
                            // Refresh work orders table after alert closes
                            if (typeof window.workOrdersTable !== 'undefined' && window.workOrdersTable && typeof window.workOrdersTable.ajax === 'object') {
                                window.workOrdersTable.ajax.reload();
                            } else {
                                window.location.reload();
                            }
                        }
                    });
                } else {
                    showSparepartAlert('error', response.message || 'Gagal menyimpan validasi sparepart');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error saving sparepart validation:', error);
                console.error('❌ Response text:', xhr.responseText);
                showSparepartAlert('error', 'Terjadi kesalahan saat menyimpan validasi sparepart');
            }
        });
    }

    /**
     * Helper function for sparepart alerts
     */
    function showSparepartAlert(type, message) {
        // Use OptimaPro notification system if available
        if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showNotification === 'function') {
            let toastType = type === 'error' ? 'danger' : type;
            OptimaPro.showNotification(message, toastType);
        } else if (typeof window.showNotification === 'function') {
            // Fallback to global notification system
            let toastType = type === 'success' ? 'success' : 
                           type === 'error' ? 'danger' : 'info';
            window.showNotification(message, toastType);
        } else {
            console.log(`[${type.toUpperCase()}] ${message}`);
        }
    }

    // Modal reset when closed
    $('#sparepartValidationModal').on('hidden.bs.modal', function() {
        console.log('📝 Sparepart validation modal closed - resetting form');
        resetSparepartValidationModal();
    });

});
</script>