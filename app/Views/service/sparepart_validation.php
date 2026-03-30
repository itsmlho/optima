<!-- Sparepart Validation Modal -->
<div class="modal fade" id="sparepartValidationModal" tabindex="-1" aria-labelledby="sparepartValidationModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="sparepartValidationModalLabel">
                    <i class="fas fa-tools me-2"></i>Validasi Sparepart - WO: <span id="sparepart-wo-number">Loading...</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

<!-- Custom CSS untuk Modal Validasi -->
<style>
/* Select2 z-index and positioning fix for modal */
.select2-container {
    z-index: 9999 !important;
}
.select2-dropdown {
    z-index: 99999 !important;
}

/* Fix Select2 dropdown position in modal */
#sparepartValidationModal .select2-container--open .select2-dropdown {
    position: absolute !important;
    top: 100% !important;
    left: 0 !important;
}

/* Ensure Select2 search box shows correctly */
.select2-container--default .select2-search--dropdown .select2-search__field {
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 6px 12px;
}

/* Form switch styling */
.form-check-input {
    cursor: pointer;
}

.form-check-label {
    cursor: pointer;
    user-select: none;
}

/* Badge styling for warehouse/non-warehouse */
.warehouse-badge,
.non-warehouse-badge {
    font-size: 10px;
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.warehouse-badge {
    background-color: #198754 !important;
    color: white !important;
}

.non-warehouse-badge {
    background-color: #ffc107 !important;
    color: #000 !important;
}

/* Table header styling */
.table-header {
    background-color: #f8f9fa;
    font-weight: 600;
}

/* Smooth transitions */
.badge {
    transition: all 0.2s ease-in-out;
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
                            <i class="fas fa-check-circle me-1"></i>Validasi Sparepart <span id="used-count" class="badge badge-soft-gray">0</span>
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
                                                    <th width="6%" class="text-center">✅</th>
                                                    <th width="10%" class="text-center">Type</th>
                                                    <th width="28%">Item Name</th>
                                                    <th width="10%" class="text-center">Dibawa</th>
                                                    <th width="12%" class="text-center">Digunakan</th>
                                                    <th width="14%" class="text-center">Status</th>
                                                    <th width="20%">Catatan</th>
                                                </tr>
                                            </thead>
                                            <tbody id="usedSparepartTableBody">
                                                <!-- Dynamic content will be loaded here -->
                                                <tr id="no-used-sparepart">
                                                    <td colspan="7" class="text-center text-muted py-3">
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
                            <div class="card shadow-sm mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-plus-square me-2"></i>Sparepart Tambahan <small class="text-muted fw-normal">(tidak direncanakan)</small></h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover table-sm" id="additionalSparepartTable" style="table-layout: fixed; width: 100%;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 100px;">Type*</th>
                                                    <th style="width: 280px;">Item Name*</th>
                                                    <th style="width: 80px;">Qty*</th>
                                                    <th style="width: 90px;">Unit*</th>
                                                    <th style="width: 110px;">Source*</th>
                                                    <th style="width: auto;">Notes</th>
                                                    <th style="width: 60px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="additionalSparepartTableBody">
                                                <!-- Dynamic rows -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-success btn-sm" id="btn-add-sparepart-row">
                                            <i class="fas fa-plus"></i> Add Item
                                        </button>
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
                    <td colspan="7" class="text-center text-muted py-3">
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
            
            // Type badge
            let itemType = item.item_type || 'sparepart';
            let typeBadge = '';
            if (itemType === 'tool') {
                typeBadge = '<span class="badge badge-soft-gray" style="font-size: 10px;">🔧 Tool</span>';
            } else {
                typeBadge = '<span class="badge badge-soft-blue" style="font-size: 10px;">⚙ Part</span>';
            }
            
            // Source badge
            let isFromWarehouse = item.is_from_warehouse !== undefined ? parseInt(item.is_from_warehouse) : 1;
            let sourceBadge = '';
            if (isFromWarehouse === 0) {
                sourceBadge = '<br><span class="badge badge-soft-yellow mt-1" style="font-size: 9px;">♻ Non-WH</span>';
            }
            
            let row = `
                <tr>
                    <td class="text-center align-middle">
                        <input type="checkbox" class="form-check-input sparepart-check" 
                               id="check-sparepart-${item.id}" 
                               data-sparepart-id="${item.id}"
                               ${status === 'sesuai' ? 'checked' : ''}>
                    </td>
                    <td class="text-center align-middle">
                        ${typeBadge}
                    </td>
                    <td class="align-middle">
                        <div class="d-flex flex-column">
                            <strong class="text-dark">${item.sparepart_name}${sourceBadge}</strong>
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
            case 'sesuai': return '<span class="badge badge-soft-green">✅ Sesuai</span>';
            case 'kurang': return '<span class="badge badge-soft-yellow">⚠️ Kurang</span>';
            case 'lebih': return '<span class="badge badge-soft-cyan">📈 Lebih</span>';
            default: return '<span class="badge badge-soft-gray">❓ Unknown</span>';
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
        let additionalCount = $('#additionalSparepartTableBody tr').length;
        
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
        
        let additionalCount = $('#additionalSparepartTableBody tr').length;
        
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
        
        // Destroy Select2 instances before clearing
        $('[id^="vadd_sparepart_"]').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });
        
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

    // Event Handlers

    /**
     * Add Sparepart Row Button
     */
    $('#btn-add-sparepart-row').on('click', function() {
        addAdditionalSparepartRow({}, additionalSparepartCounter++, true);
        updateSparepartCounters();
        updateValidationSummary();
    });

    /**
     * Add Additional Sparepart Row — SPK-style AJAX Select2
     */
    function addAdditionalSparepartRow(item, index, isNew = false) {
        // tbody is already empty from .empty() above

        const rc = index;
        let itemType = item.item_type || 'sparepart';
        if (itemType === 'part') itemType = 'sparepart'; // map legacy
        const source = item.source || (parseInt(item.is_from_warehouse) === 0 ? 'BEKAS' : 'WAREHOUSE');
        const satuan = item.satuan || 'PCS';
        const preSelected = (!isNew && item.sparepart_id)
            ? `<option value="${item.sparepart_id}" selected>${item.sparepart_name || item.sparepart_id}</option>`
            : '';

        const row = `
            <tr id="vadd_row_${rc}">
                <td>
                    <!-- Item Type: Sparepart or Tool -->
                    <select class="form-select form-select-sm"
                            name="additional_spareparts[${rc}][item_type]"
                            id="vadd_type_${rc}"
                            onchange="switchVldItemInput(${rc})" required>
                        <option value="sparepart" ${itemType === 'sparepart' ? 'selected' : ''}>
                            <i class="fas fa-cog"></i> Sparepart
                        </option>
                        <option value="tool" ${itemType === 'tool' ? 'selected' : ''}>
                            <i class="fas fa-tools"></i> Tool
                        </option>
                    </select>
                </td>
                <td>
                    <!-- Dynamic Input Container -->
                    <div id="vadd_input_${rc}">
                        <!-- Sparepart Dropdown (Default - active) -->
                        <select class="form-select form-select-sm ${itemType === 'tool' ? 'd-none' : ''}"
                                name="additional_spareparts[${rc}][sparepart_id]"
                                id="vadd_sparepart_${rc}"
                                ${itemType !== 'tool' ? 'required' : ''}>
                            <option value="">-- Select Sparepart --</option>
                            ${preSelected}
                        </select>
                        <!-- Manual Sparepart Input (Hidden by default - NO name attr) -->
                        <input type="text"
                               class="form-control form-control-sm d-none"
                               id="vadd_manual_${rc}"
                               placeholder="Ketik nama sparepart manual"
                               maxlength="255">
                        <!-- Tool Text Input (Hidden by default - NO name attr until activated) -->
                        <input type="text"
                               class="form-control form-control-sm ${itemType !== 'tool' ? 'd-none' : ''}"
                               id="vadd_tool_${rc}"
                               ${itemType === 'tool' ? `name="additional_spareparts[${rc}][item_name_manual]"` : ''}
                               placeholder="e.g., Kunci Inggris 12mm" maxlength="255"
                               value="${item.item_name_manual || ''}"
                               ${itemType === 'tool' ? 'required' : ''}>
                    </div>
                </td>
                <td>
                    <input type="number"
                           class="form-control form-control-sm"
                           name="additional_spareparts[${rc}][quantity]"
                           value="${item.quantity || 1}"
                           min="1"
                           required>
                </td>
                <td>
                    <select class="form-select form-select-sm" name="additional_spareparts[${rc}][satuan]" required>
                        <optgroup label="📦 Barang / Unit">
                            <option value="PCS" ${satuan === 'PCS' ? 'selected' : ''}>PCS</option>
                            <option value="UNIT" ${satuan === 'UNIT' ? 'selected' : ''}>UNIT</option>
                            <option value="SET" ${satuan === 'SET' ? 'selected' : ''}>SET</option>
                            <option value="PASANG" ${satuan === 'PASANG' ? 'selected' : ''}>PASANG</option>
                        </optgroup>
                        <optgroup label="⚖️ Berat">
                            <option value="KG" ${satuan === 'KG' ? 'selected' : ''}>KG</option>
                            <option value="GRAM" ${satuan === 'GRAM' ? 'selected' : ''}>GRAM</option>
                        </optgroup>
                        <optgroup label="📏 Panjang">
                            <option value="METER" ${satuan === 'METER' ? 'selected' : ''}>METER</option>
                            <option value="CM" ${satuan === 'CM' ? 'selected' : ''}>CM</option>
                        </optgroup>
                        <optgroup label="🧴 Volume">
                            <option value="LITER" ${satuan === 'LITER' ? 'selected' : ''}>LITER</option>
                            <option value="ML" ${satuan === 'ML' ? 'selected' : ''}>ML</option>
                        </optgroup>
                    </select>
                </td>
                <td>
                    <!-- Source Type Dropdown -->
                    <select class="form-select form-select-sm"
                            name="additional_spareparts[${rc}][source]"
                            id="vadd_source_${rc}"
                            onchange="toggleVldKanibalFields(${rc})"
                            required>
                        <option value="WAREHOUSE" ${source === 'WAREHOUSE' ? 'selected' : ''}>
                            <i class="fas fa-warehouse"></i> Warehouse
                        </option>
                        <option value="BEKAS" ${source === 'BEKAS' ? 'selected' : ''}>
                            <i class="fas fa-recycle"></i> Bekas
                        </option>
                        <option value="KANIBAL" ${source === 'KANIBAL' ? 'selected' : ''}>
                            <i class="fas fa-exchange-alt"></i> Kanibal
                        </option>
                    </select>
                </td>
                <td>
                    <!-- KANIBAL Fields (Hidden by default) -->
                    <div class="kanibal-fields d-none" id="vadd_kanibal_${rc}">
                        <div class="mb-2">
                            <label class="form-label form-label-sm mb-1">Dari Unit *</label>
                            <select class="form-select form-select-sm"
                                    name="additional_spareparts[${rc}][source_unit_id]"
                                    id="vadd_src_unit_${rc}">
                                <option value="">-- Pilih Unit Sumber --</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label form-label-sm mb-1">Alasan *</label>
                            <textarea class="form-control form-control-sm"
                                      name="additional_spareparts[${rc}][source_notes]"
                                      id="vadd_src_notes_${rc}"
                                      rows="2"
                                      placeholder="Contoh: Unit rusak total"
                                      maxlength="500"></textarea>
                        </div>
                    </div>

                    <!-- Notes for non-KANIBAL -->
                    <div class="non-kanibal-notes" id="vadd_nkanibal_${rc}">
                        <input type="text"
                               class="form-control form-control-sm"
                               name="additional_spareparts[${rc}][notes]"
                               value="${item.notes || ''}"
                               placeholder="Optional notes..."
                               maxlength="255">
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-additional-sparepart">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>`;

        $('#additionalSparepartTableBody').append(row);

        if (itemType !== 'tool') {
            _initValidationSelect2(rc);
        }

        // Populate fields if editing existing data
        if (!isNew && item.source === 'KANIBAL') {
            toggleVldKanibalFields(rc);
        }
    }

    /**
     * Init AJAX Select2 for sparepart dropdown in validation modal
     */
    function _initValidationSelect2(rc) {
        setTimeout(function() {
            const $sel = $(`#vadd_sparepart_${rc}`);
            if ($sel.length && !$sel.hasClass('select2-hidden-accessible')) {
                $sel.select2({
                    placeholder: '-- Ketik untuk cari sparepart --',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#sparepartValidationModal'),
                    minimumInputLength: 2,
                    ajax: {
                        url: '<?= base_url('service/work-orders/search-spareparts') ?>',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) { return { q: params.term, page: params.page || 1 }; },
                        processResults: function(data, params) {
                            params.page = params.page || 1;
                            return { results: data.results, pagination: { more: data.pagination.more } };
                        },
                        cache: true
                    },
                    language: {
                        inputTooShort: function() { return 'Ketik minimal 2 karakter untuk mencari...'; },
                        searching:     function() { return 'Mencari sparepart...'; },
                        noResults:     function() { return 'Tidak ada sparepart ditemukan'; },
                        loadingMore:   function() { return 'Memuat lebih banyak...'; }
                    }
                });
                console.log(`✅ AJAX Select2 initialized for vadd_sparepart_${rc}`);
            }
        }, 150);
    }

    /**
     * Manual Entry Button — appended into Select2 dropdown when it opens
     */
    $(document).on('select2:open', '[id^="vadd_sparepart_"]:not([id$="_manual"])', function() {
        const $select = $(this);
        const rowId = $select.attr('id').replace('vadd_sparepart_', '');
        const $dropdown = $('.select2-dropdown:last');

        $dropdown.find('.sparepart-manual-entry-btn').remove();

        const manualButton = $(`
            <div class="sparepart-manual-entry-btn"
                 style="padding: 12px 15px;
                        cursor: pointer;
                        border: 2px solid #007bff;
                        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
                        text-align: center;
                        font-weight: 600;
                        color: #0d47a1;
                        margin: 0;
                        border-radius: 0;
                        border-left: 0;
                        border-right: 0;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                        transition: all 0.2s ease;">
                <i class="fas fa-pencil-alt me-2"></i>
                <span>📝 Input Manual Sparepart</span>
            </div>
        `);

        manualButton.hover(
            function() { $(this).css({ 'background': 'linear-gradient(135deg, #bbdefb 0%, #90caf9 100%)', 'transform': 'scale(1.02)', 'box-shadow': '0 4px 8px rgba(0,0,0,0.15)' }); },
            function() { $(this).css({ 'background': 'linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%)', 'transform': 'scale(1)', 'box-shadow': '0 2px 4px rgba(0,0,0,0.1)' }); }
        );

        manualButton.on('click', function() {
            $select.select2('close');
            const $manualInput = $(`#vadd_manual_${rowId}`);
            $select.addClass('d-none').removeAttr('name').removeAttr('required');
            if ($select.hasClass('select2-hidden-accessible')) $select.select2('destroy');
            $manualInput.removeClass('d-none')
                        .attr('name', `additional_spareparts[${rowId}][sparepart_id]`)
                        .attr('required', 'required')
                        .focus();
            console.log(`✅ Switched to manual input for vadd row ${rowId}`);
        });

        const $searchContainer = $dropdown.find('.select2-search');
        if ($searchContainer.length > 0) {
            $searchContainer.after(manualButton);
        } else {
            $dropdown.find('.select2-results').prepend(manualButton);
        }
    });

    /**
     * Switch Item Input — toggle between Sparepart dropdown, Manual input, and Tool text input
     */
    window.switchVldItemInput = function(rc) {
        const type        = $(`#vadd_type_${rc}`).val();
        const $drop       = $(`#vadd_sparepart_${rc}`);
        const $manual     = $(`#vadd_manual_${rc}`);
        const $tool       = $(`#vadd_tool_${rc}`);

        if (type === 'tool') {
            // TOOL mode: show text input, hide dropdown & manual
            if ($drop.hasClass('select2-hidden-accessible')) $drop.select2('destroy');
            $drop.addClass('d-none').removeAttr('required').removeAttr('name');
            $manual.addClass('d-none').removeAttr('required').removeAttr('name');
            $tool.removeClass('d-none').attr('required', 'required').attr('name', `additional_spareparts[${rc}][item_name_manual]`);
        } else {
            // SPAREPART mode: show dropdown, hide text inputs
            $tool.addClass('d-none').removeAttr('required').removeAttr('name');
            $manual.addClass('d-none').removeAttr('required').removeAttr('name');
            $drop.removeClass('d-none').attr('required', 'required').attr('name', `additional_spareparts[${rc}][sparepart_id]`);
            if (!$drop.hasClass('select2-hidden-accessible')) _initValidationSelect2(rc);
        }
    };

    /**
     * Toggle KANIBAL Fields — show/hide unit selector and notes when KANIBAL selected
     */
    window.toggleVldKanibalFields = function(rc) {
        const sourceType       = $(`#vadd_source_${rc}`).val();
        const $kanibal         = $(`#vadd_kanibal_${rc}`);
        const $nonKanibal      = $(`#vadd_nkanibal_${rc}`);
        const $srcUnit         = $(`#vadd_src_unit_${rc}`);
        const $srcNotes        = $(`#vadd_src_notes_${rc}`);

        if (sourceType === 'KANIBAL') {
            $kanibal.removeClass('d-none');
            $nonKanibal.addClass('d-none');
            $srcUnit.attr('required', 'required');
            $srcNotes.attr('required', 'required');

            // Populate unit dropdown if not yet populated
            if ($srcUnit.children().length <= 1) {
                if (window.unitsData && Array.isArray(window.unitsData) && window.unitsData.length > 0) {
                    const Ov = window.OptimaUnitSelect2;
                    const useTpl = typeof Ov !== 'undefined' && typeof Ov.optionDataAttributes === 'function';
                    window.unitsData.forEach(function(unit) {
                        const row = Object.assign({}, unit, {
                            id: unit.id_inventory_unit || unit.id,
                            id_inventory_unit: unit.id_inventory_unit,
                            no_unit: unit.no_unit || unit.unit_number,
                            merk: unit.merk_unit || unit.merk,
                            pelanggan: unit.pelanggan || unit.customer_name
                        });
                        if (useTpl) {
                            const attrs = Ov.optionDataAttributes(row);
                            const label = Ov.line1FromRow(Ov.normalizeRow(row));
                            const $opt = $('<option></option>').val(unit.id_inventory_unit).text(label);
                            Object.keys(attrs).forEach(function (k) {
                                const v = attrs[k];
                                if (v !== '' && v != null && v !== false) {
                                    $opt.attr(k, v);
                                }
                            });
                            $srcUnit.append($opt);
                        } else {
                            const unitNumber = unit.no_unit || unit.unit_number;
                            const unitLabel = `${unitNumber} - ${unit.pelanggan || unit.customer_name || 'No Customer'} - ${unit.merk_unit || ''}`;
                            $srcUnit.append(`<option value="${unit.id_inventory_unit}">${unitLabel}</option>`);
                        }
                    });
                    const vcfg = {
                        placeholder: '-- Pilih Unit Sumber --',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#sparepartValidationModal'),
                        language: {
                            noResults:  function() { return 'Unit tidak ditemukan'; },
                            searching:  function() { return 'Mencari...'; }
                        }
                    };
                    if (useTpl) {
                        vcfg.templateResult = function (item) { return Ov.templateResult(item, {}); };
                        vcfg.templateSelection = function (item) { return Ov.templateSelection(item, {}); };
                    }
                    $srcUnit.select2(vcfg);
                } else {
                    console.warn('⚠️ window.unitsData kosong — unit tidak dapat dimuat');
                }
            }

            console.log(`✅ KANIBAL fields shown for vadd row ${rc}`);
        } else {
            $kanibal.addClass('d-none');
            $nonKanibal.removeClass('d-none');
            $srcUnit.removeAttr('required').val('');
            $srcNotes.removeAttr('required').val('');
            console.log(`✅ KANIBAL fields hidden for vadd row ${rc}`);
        }
    };

    /**
     * Remove Additional Sparepart Row
     */
    $(document).on('click', '.remove-additional-sparepart', function() {
        const row = $(this).closest('tr');
        const $sel = row.find('[id^="vadd_sparepart_"]');
        if ($sel.length && $sel.hasClass('select2-hidden-accessible')) {
            $sel.select2('destroy');
        }
        row.remove();
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
        formData += '&<?= csrf_token() ?>=' + encodeURIComponent('<?= csrf_hash() ?>');
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
                    OptimaNotify.success(
                        `Work Order ${woNumber} berhasil di-Close`,
                        'Work Order Ditutup'
                    );
                    
                    // Reload both tables without switching tabs
                    setTimeout(() => {
                        // Reload progress table - WO will disappear from here
                        if (typeof window.progressWorkOrdersTable !== 'undefined' && window.progressWorkOrdersTable) {
                            window.progressWorkOrdersTable.ajax.reload(null, false);
                            console.log('✅ Progress table refreshed - WO removed');
                        }
                        // Reload closed table - WO will appear here
                        if (typeof window.closedWorkOrdersTable !== 'undefined' && window.closedWorkOrdersTable) {
                            window.closedWorkOrdersTable.ajax.reload(null, false);
                            console.log('✅ Closed table refreshed - WO added');
                        }
                    }, 1700);
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