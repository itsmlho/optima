<!-- =====================================================
     Modal: Add Unit to Contract (Select2 Redesign)
     ===================================================== -->
<div class="modal fade" id="addUnitModal" tabindex="-1" aria-labelledby="addUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-default">
                <h5 class="modal-title" id="addUnitModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Add Unit to Contract
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Contract Info -->
                <div class="alert alert-info mb-3 py-2">
                    <i class="fas fa-file-contract me-1"></i>
                    <strong>Contract:</strong> <span id="addUnitKontrakInfo">Loading...</span>
                </div>

                <!-- Location Selection -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        Unit Location 
                        <small class="text-muted fw-normal">(select customer location for this unit)</small>
                    </label>
                    <select class="form-select" id="addUnitLocation">
                        <option value="">-- Select Location --</option>
                    </select>
                    <small class="text-muted">Unit will be assigned to selected location</small>
                </div>

                <!-- Dates -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="addUnitTanggalMulai">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" id="addUnitTanggalSelesai">
                    </div>
                </div>

                <hr>

                <!-- Unit Selection with Select2 -->
                <div class="card border-primary mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-search me-1"></i>Select Unit</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 align-items-end">
                            <div class="col-12">
                                <label class="form-label">Search & Select Unit <small class="text-muted">(automatically added to list)</small></label>
                                <select class="form-select" id="addUnitSelect2" style="width: 100%">
                                    <option value="">Type to search units...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Added Units List -->
                <div class="card">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-list me-1"></i>Added Units List</h6>
                        <span class="badge bg-primary" id="addedUnitCount">0</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0" id="addedUnitsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Unit No</th>
                                        <th>Brand / Model</th>
                                        <th class="text-end" style="min-width:160px">Rate/Month</th>
                                        <th class="text-center" width="70">Spare</th>
                                        <th width="40"></th>
                                    </tr>
                                </thead>
                                <tbody id="addedUnitsBody">
                                    <tr id="addedUnitsEmpty">
                                        <td colspan="5" class="text-center text-muted py-3">
                                            <i class="fas fa-inbox me-1"></i>No units added yet
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Total Row -->
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <strong>Total Rental Value:</strong>
                            <strong class="text-success" id="addedTotalValue">Rp 0</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitAddUnit()" id="btnSubmitAddUnit" disabled>
                    <i class="fas fa-save me-1"></i>Save All
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let addUnitData = {
    kontrakId: null,
    units: [],          // [{unit_id, no_unit, merk_model, harga_sewa, harga_default, is_spare}]
    allUnits: [],       // Full list from API
};

// Open modal
function openAddUnitModal(kontrakId, kontrakNo, tanggalMulai, tanggalBerakhir) {
    addUnitData.kontrakId = kontrakId;
    addUnitData.units = [];

    document.getElementById('addUnitKontrakInfo').textContent = kontrakNo + ' (' + (tanggalMulai || '-') + ' to ' + (tanggalBerakhir || '-') + ')';
    $('#addUnitTanggalMulai').val(tanggalMulai || '');
    $('#addUnitTanggalSelesai').val(tanggalBerakhir || '');

    renderAddedUnitsList();

    // Initialize Select2 with AJAX
    initUnitSelect2();

    // Load customer locations
    loadAddUnitLocations();

    $('#addUnitModal').modal('show');
}

// Initialize Select2 for unit selection
function initUnitSelect2() {
    if ($('#addUnitSelect2').hasClass('select2-hidden-accessible')) {
        $('#addUnitSelect2').select2('destroy');
    }

    $('#addUnitSelect2').select2({
        dropdownParent: $('#addUnitModal'),
        placeholder: 'Type unit no, serial number, or brand/model...',
        allowClear: true,
        minimumInputLength: 0,
        ajax: {
            url: function() {
                return BASE_URL + 'marketing/kontrak/getAvailableUnits';
            },
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    kontrak_id: addUnitData.kontrakId,
                    search: params.term || ''
                };
            },
            processResults: function(res) {
                if (!res.success) {
                    console.error('Failed to load units:', res.message);
                    return { results: [] };
                }
                
                console.log('Loaded units:', res.data ? res.data.length : 0);
                addUnitData.allUnits = res.data || [];
                
                var results = (res.data || []).map(function(u) {
                    var alreadyAdded = addUnitData.units.find(function(a) { return a.unit_id == u.id; });
                    return {
                        id: u.id,
                        text: u.label || (u.no_unit + ' - ' + u.merk + ' ' + u.model),
                        unit: u,
                        disabled: !!alreadyAdded || !!u.is_same_contract
                    };
                });
                return { results: results };
            },
            cache: false
        },
        templateResult: formatUnitResult,
        templateSelection: formatUnitSelection
    }).on('select2:open', function() {
        // Auto-trigger search on open to show initial results
        setTimeout(function() {
            $('.select2-search__field').trigger('input');
        }, 100);
    });

    // On selection → auto-add to list immediately
    $('#addUnitSelect2').off('select2:select').on('select2:select', function(e) {
        var data = e.params.data;
        var unit = data.unit;
        if (!unit) return;

        // Confirm if unit is already contracted elsewhere
        if (unit.is_contracted && !unit.is_same_contract) {
            if (!confirm('Unit ' + unit.no_unit + ' is currently active in contract ' + (unit.current_kontrak_no || '-') + '. Continue adding?')) {
                $('#addUnitSelect2').val(null).trigger('change');
                return;
            }
        }

        // Auto-add with default harga
        addUnitData.units.push({
            unit_id: unit.id,
            no_unit: unit.no_unit,
            merk_model: unit.merk + ' ' + unit.model,
            harga_sewa: null,  // null = use default
            harga_default: parseFloat(unit.harga_sewa_bulanan || 0),
            is_spare: 0
        });

        // Clear Select2 so user can pick another
        $('#addUnitSelect2').val(null).trigger('change');

        renderAddedUnitsList();
    });
}

// Format Select2 result item
function formatUnitResult(item) {
    if (item.loading) return $('<span>Loading...</span>');
    if (!item.unit) return item.text;

    var u = item.unit;
    var html = '<div class="d-flex justify-content-between align-items-center">';
    html += '<div>';
    html += '<strong>' + u.no_unit + '</strong>';
    if (u.serial_number) html += ' <small class="text-muted">(' + u.serial_number + ')</small>';
    html += '<br><small class="text-muted">' + u.merk + ' ' + u.model + '</small>';
    if (u.kapasitas) html += ' <small class="text-muted">| ' + u.kapasitas + '</small>';
    html += '</div>';
    html += '<div class="text-end">';

    if (u.is_same_contract) {
        html += '<span class="badge bg-secondary">Already in Contract</span>';
    } else if (u.is_contracted) {
        html += '<span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i>' + (u.current_kontrak_no || 'Active') + '</span>';
    } else {
        html += '<span class="badge bg-success">Available</span>';
    }

    html += '<br><small>' + rupiah(u.harga_sewa_bulanan || 0) + '/month</small>';
    html += '</div></div>';

    return $(html);
}

// Format Select2 selected item
function formatUnitSelection(item) {
    if (!item.unit) return item.text;
    return item.unit.no_unit + ' - ' + item.unit.merk + ' ' + item.unit.model;
}

// Update harga inline
function updateUnitHarga(unitId) {
    var input = document.getElementById('harga_' + unitId);
    if (!input) return;
    var val = input.value;
    var unit = addUnitData.units.find(function(u) { return u.unit_id == unitId; });
    if (!unit) return;
    unit.harga_sewa = (val !== '' ? parseFloat(val) : null);
    updateTotals();
}

// Toggle spare per unit
function toggleUnitSpare(unitId) {
    var unit = addUnitData.units.find(function(u) { return u.unit_id == unitId; });
    if (!unit) return;
    unit.is_spare = unit.is_spare ? 0 : 1;
    if (unit.is_spare) {
        unit.harga_sewa = 0;
    } else {
        unit.harga_sewa = null; // back to default
    }
    renderAddedUnitsList();
}

// Remove unit from list
function removeUnitFromList(unitId) {
    addUnitData.units = addUnitData.units.filter(function(u) { return u.unit_id != unitId; });
    renderAddedUnitsList();
}

// Calculate & update totals only
function updateTotals() {
    var total = 0;
    addUnitData.units.forEach(function(u) {
        var effectiveHarga = u.is_spare ? 0 : (u.harga_sewa !== null ? u.harga_sewa : u.harga_default);
        total += effectiveHarga;
    });
    $('#addedTotalValue').text(rupiah(total));
}

// Render added units list with inline editing
function renderAddedUnitsList() {
    var tbody = $('#addedUnitsBody');
    tbody.empty();

    if (addUnitData.units.length === 0) {
        tbody.html('<tr id="addedUnitsEmpty"><td colspan="5" class="text-center text-muted py-3"><i class="fas fa-inbox me-1"></i>No units added yet</td></tr>');
        $('#addedUnitCount').text('0');
        $('#addedTotalValue').text('Rp 0');
        $('#btnSubmitAddUnit').prop('disabled', true);
        return;
    }

    var total = 0;
    addUnitData.units.forEach(function(u) {
        var effectiveHarga = u.is_spare ? 0 : (u.harga_sewa !== null ? u.harga_sewa : u.harga_default);
        total += effectiveHarga;

        var row = '<tr>';
        row += '<td><strong>' + u.no_unit + '</strong></td>';
        row += '<td><small>' + u.merk_model + '</small></td>';

        // Inline harga input
        row += '<td class="text-end">';
        if (u.is_spare) {
            row += '<span class="text-muted">Rp 0 <small>(spare)</small></span>';
        } else {
            row += '<div class="input-group input-group-sm" style="max-width:160px;margin-left:auto">';
            row += '<span class="input-group-text py-0 px-1" style="font-size:11px">Rp</span>';
            row += '<input type="number" class="form-control form-control-sm py-0" id="harga_' + u.unit_id + '" ';
            row += 'value="' + (u.harga_sewa !== null ? u.harga_sewa : '') + '" ';
            row += 'placeholder="' + u.harga_default + '" ';
            row += 'min="0" step="1000" onchange="updateUnitHarga(' + u.unit_id + ')" style="font-size:12px">';
            row += '</div>';
        }
        row += '</td>';

        // Spare toggle
        row += '<td class="text-center">';
        row += '<div class="form-check form-switch d-flex justify-content-center mb-0">';
        row += '<input type="checkbox" class="form-check-input" role="switch" ' + (u.is_spare ? 'checked' : '') + ' onchange="toggleUnitSpare(' + u.unit_id + ')" title="Spare unit">';
        row += '</div>';
        row += '</td>';

        // Remove
        row += '<td><button class="btn btn-xs btn-outline-danger" onclick="removeUnitFromList(' + u.unit_id + ')" title="Hapus"><i class="fas fa-times"></i></button></td>';
        row += '</tr>';
        tbody.append(row);
    });

    $('#addedUnitCount').text(addUnitData.units.length);
    $('#addedTotalValue').text(rupiah(total));
    $('#btnSubmitAddUnit').prop('disabled', false);
}

// Load customer locations for dropdown
function loadAddUnitLocations() {
    $.ajax({
        url: BASE_URL + 'marketing/kontrak/getAvailableUnits',
        type: 'GET',
        data: { kontrak_id: addUnitData.kontrakId, search: '' },
        success: function(res) {
            console.log('Load locations response:', res);
            
            if (!res.success) {
                console.error('Failed to load locations:', res.message);
                return;
            }
            
            var locationSelect = $('#addUnitLocation');
            locationSelect.empty().append('<option value="">-- Select Location --</option>');
            
            if (res.customer_locations && res.customer_locations.length > 0) {
                console.log('Found customer locations:', res.customer_locations.length);
                res.customer_locations.forEach(function(loc) {
                    locationSelect.append('<option value="' + loc.id + '">' + (loc.location_name || loc.address || 'Location #' + loc.id) + '</option>');
                });
            } else {
                console.warn('No customer locations found for this contract');
                locationSelect.append('<option value="" disabled>No customer locations available</option>');
            }
            
            // Auto-fill dates if available
            if (res.kontrak && !$('#addUnitTanggalMulai').val()) {
                $('#addUnitTanggalMulai').val(res.kontrak.tanggal_mulai || '');
                $('#addUnitTanggalSelesai').val(res.kontrak.tanggal_berakhir || '');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading locations:', error);
            console.error('Response:', xhr.responseText);
        }
    });
}

// Submit all units
function submitAddUnit() {
    if (addUnitData.units.length === 0) {
        alert('Add at least 1 unit');
        return;
    }

    var customerLocationId = $('#addUnitLocation').val();
    var tanggalMulai = $('#addUnitTanggalMulai').val();
    var tanggalSelesai = $('#addUnitTanggalSelesai').val();

    // Build units array for API
    var units = addUnitData.units.map(function(u) {
        return {
            unit_id: u.unit_id,
            harga_sewa: u.harga_sewa,
            is_spare: u.is_spare
        };
    });

    var btn = $('#btnSubmitAddUnit');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Saving...');

    $.ajax({
        url: BASE_URL + 'marketing/kontrak/addUnit',
        type: 'POST',
        contentType: 'application/json',
        headers: {
            'X-CSRF-TOKEN': typeof window.csrfToken !== 'undefined' ? window.csrfToken : ''
        },
        data: JSON.stringify({
            kontrak_id: addUnitData.kontrakId,
            units: units,
            customer_location_id: customerLocationId,
            tanggal_mulai: tanggalMulai,
            tanggal_selesai: tanggalSelesai
        }),
        success: function(res) {
            if (res.success) {
                $('#addUnitModal').modal('hide');
                if (typeof loadUnits === 'function') loadUnits();
                if (typeof alertSwal === 'function') {
                    alertSwal('success', res.message || 'Units successfully added');
                } else {
                    alert(res.message || 'Units successfully added');
                }
                setTimeout(function() { window.location.reload(); }, 1000);
            } else {
                alert(res.message || 'Failed to add units');
            }
        },
        error: function() {
            alert('Error adding units');
        },
        complete: function() {
            btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save All');
        }
    });
}
</script>
