<script>
// ========================================
// UNIT VERIFICATION SCRIPT
// ========================================
(function() {
    // Toast helper
    function unitToast(type, message, title = 'Verifikasi Unit') {
        if (typeof window.createOptimaToast === 'function') {
            createOptimaToast({ type, title, message });
        } else if (window.OptimaPro && typeof OptimaPro.showNotification === 'function') {
            OptimaPro.showNotification(message, type === 'success' ? 'success' : 'error');
        } else {
            console.log(`[${type.toUpperCase()}] ${title}: ${message}`);
        }
    }

    $(document).ready(function() {
        $('#unit-list').on('click', '.unit-list-item', function(e) {
            e.preventDefault();
            $('.unit-list-item').removeClass('active');
            $(this).addClass('active');
            const unitData = $(this).data('unit');
            $('#unit-detail-view-container').html(createUnitDetailCard(unitData));
            // Load dropdown options after card is created
            setTimeout(() => loadDropdownOptions(), 100);
        });

        // Event listener untuk submit verifikasi inline (tanpa modal)
        $(document).on('click', '#btn-submit-verification-inline', submitUnitVerificationInline);
        
        // Event listener untuk dropdown lokasi
        $(document).on('change', '#lokasi_unit_inline', checkAllUnitVerifiedInline);
        
        // Event listener untuk checkbox "Sesuai"
        $(document).on('change', '.verify-checkbox-sesuai', function() {
            const row = $(this).closest('tr');
            const verifyField = row.find('.verify-field');
            const dbField = row.find('td:nth-child(2) input, td:nth-child(2) textarea');
            const dbValue = dbField.val() || '';
            const tidakSesuaiCheckbox = row.find('.verify-checkbox-tidak-sesuai');
            
            if ($(this).is(':checked')) {
                // Uncheck "Tidak Sesuai" jika "Sesuai" dicentang (mutually exclusive)
                tidakSesuaiCheckbox.prop('checked', false);
                
                // SELALU samakan dengan nilai database (persis seperti kolom Database)
                if (verifyField.is('select')) {
                    verifyField.val(dbValue).prop('disabled', true);
                } else {
                    verifyField.val(dbValue).prop('readonly', true);
                }
                verifyField.css({
                    'background-color': '#f0fdf4',
                    'border-color': '#10b981'
                });
                row.css('background-color', '#f0fdf4');
            } else {
                verifyField.prop('readonly', false).css({
                    'background-color': '#fff',
                    'border-color': '#333'
                });
                row.css('background-color', '');
            }
            checkAllUnitVerifiedInline();
        });
        
        // Event listener untuk checkbox "Tidak Sesuai"
        $(document).on('change', '.verify-checkbox-tidak-sesuai', function() {
            const row = $(this).closest('tr');
            const verifyField = row.find('.verify-field');
            const sesuaiCheckbox = row.find('.verify-checkbox-sesuai');
            const isTextarea = verifyField.is('textarea');
            
            if ($(this).is(':checked')) {
                // Uncheck "Sesuai" jika "Tidak Sesuai" dicentang (mutually exclusive)
                sesuaiCheckbox.prop('checked', false);
                
                // Field harus bisa diedit dan wajib diisi (dropdown atau text input)
                if (verifyField.is('select')) {
                    verifyField.prop('disabled', false);
                } else {
                    verifyField.prop('readonly', false);
                }
                verifyField.css({
                    'background-color': '#fef2f2',
                    'border-color': '#ef4444'
                });
                
                // Focus hanya untuk input, tidak untuk textarea (karena textarea bisa besar)
                if (!isTextarea) {
                    verifyField.focus();
                }
                
                row.css('background-color', '#fef2f2');
                
                // Jika kosong, kosongkan field agar user harus mengisi
                if (!verifyField.val() || verifyField.val().trim() === '') {
                    verifyField.val('').attr('required', true);
                } else {
                    verifyField.attr('required', true);
                }
                
                // Tampilkan field alasan reject
                $('#alasan-reject-container').slideDown(300);
                $('#alasan_reject_inline').prop('required', true);
            } else {
                verifyField.prop('readonly', false).css({
                    'background-color': '#fff',
                    'border-color': '#333'
                }).removeAttr('required');
                row.css('background-color', '');
                
                // Cek apakah masih ada checkbox "Tidak Sesuai" yang dicentang
                const hasTidakSesuai = $('.verify-checkbox-tidak-sesuai:checked').length > 0;
                if (!hasTidakSesuai) {
                    $('#alasan-reject-container').slideUp(300);
                    $('#alasan_reject_inline').prop('required', false).val('');
                }
            }
            checkAllUnitVerifiedInline();
        });
        
        // Event listener untuk input field "Real Lapangan"
        // Jika ada pengeditan, otomatis check "Tidak Sesuai" dan uncheck "Sesuai"
        $(document).on('input', '.verify-field', function() {
            const row = $(this).closest('tr');
            const verifyField = $(this);
            const dbField = row.find('td:nth-child(2) input, td:nth-child(2) textarea');
            const dbValue = dbField.val() || '';
            const realValue = verifyField.val() || '';
            const sesuaiCheckbox = row.find('.verify-checkbox-sesuai');
            const tidakSesuaiCheckbox = row.find('.verify-checkbox-tidak-sesuai');
            
            // Jika nilai berbeda dengan database, otomatis check "Tidak Sesuai"
            if (realValue.trim() !== dbValue.trim()) {
                // Uncheck "Sesuai" jika ada
                sesuaiCheckbox.prop('checked', false);
                // Check "Tidak Sesuai"
                tidakSesuaiCheckbox.prop('checked', true);
                
                // Update styling untuk "Tidak Sesuai" (dropdown atau text input)
                if (verifyField.is('select')) {
                    verifyField.prop('disabled', false);
                } else {
                    verifyField.prop('readonly', false);
                }
                verifyField.css({
                    'background-color': '#fef2f2',
                    'border-color': '#ef4444'
                }).attr('required', true);
                row.css('background-color', '#fef2f2');
                
                // Tampilkan field alasan reject
                $('#alasan-reject-container').slideDown(300);
                $('#alasan_reject_inline').prop('required', true);
            } else {
                // Jika nilai sama dengan database, bisa check "Sesuai" (tapi tidak otomatis)
                // Biarkan user memilih sendiri
                
                // Cek apakah masih ada checkbox "Tidak Sesuai" yang dicentang
                const hasTidakSesuai = $('.verify-checkbox-tidak-sesuai:checked').length > 0;
                if (!hasTidakSesuai) {
                    $('#alasan-reject-container').slideUp(300);
                    $('#alasan_reject_inline').prop('required', false).val('');
                }
            }
            
            checkAllUnitVerifiedInline();
        });
        
        // Load dropdown options for all dropdown fields
        function loadDropdownOptions() {
            $('.verify-dropdown').each(function() {
                const $dropdown = $(this);
                const dropdownType = $dropdown.data('dropdown-type');
                const currentValue = $dropdown.find('option:selected').val();
                const cascadingParent = $dropdown.data('cascading-parent');
                const parentValue = $dropdown.data('parent-value');
                
                if (!dropdownType || $dropdown.data('loaded')) return;
                
                // Build request data
                const requestData = { field: dropdownType };
                
                // Add parent filter for cascading dropdowns
                if (cascadingParent && parentValue) {
                    if (dropdownType === 'model_unit' && cascadingParent === 'merk') {
                        requestData.merk_unit = parentValue;
                    } else if (dropdownType === 'model_mesin' && cascadingParent === 'engine_type') {
                        requestData.merk_mesin = parentValue;
                    }
                }
                
                $.ajax({
                    url: baseUrl + '/warehouse/purchase-orders/get-unit-verification-options',
                    method: 'GET',
                    data: requestData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.data) {
                            // Clear existing options except the first one
                            $dropdown.find('option:not(:first)').remove();
                            
                            // Add options from API
                            response.data.forEach(function(option) {
                                const optionText = option.text || option.label || option.name || option.id;
                                const optionValue = optionText; // Use text as value for display
                                const isSelected = (optionValue === currentValue || option.id == currentValue);
                                
                                $dropdown.append(
                                    $('<option>', {
                                        value: optionValue,
                                        text: optionText,
                                        selected: isSelected,
                                        'data-id': option.id || optionValue
                                    })
                                );
                            });
                            
                            // If current value not found in options, keep it as selected
                            if (currentValue && !$dropdown.find(`option[value="${currentValue}"]`).length) {
                                $dropdown.prepend(
                                    $('<option>', {
                                        value: currentValue,
                                        text: currentValue,
                                        selected: true
                                    })
                                );
                            }
                            
                            $dropdown.data('loaded', true);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading dropdown options:', error);
                    }
                });
            });
        }
        
        // Event listener for dropdown change (same as text input)
        $(document).on('change', '.verify-dropdown', function() {
            const $dropdown = $(this);
            const fieldName = $dropdown.data('field-name');
            const row = $(this).closest('tr');
            const verifyField = $(this);
            const dbField = row.find('td:nth-child(2) input, td:nth-child(2) textarea, td:nth-child(2) select');
            let dbValue = '';
            if (dbField.is('select')) {
                dbValue = dbField.find('option:selected').text() || dbField.val() || '';
            } else {
                dbValue = dbField.val() || '';
            }
            const realValue = verifyField.val() || '';
            const sesuaiCheckbox = row.find('.verify-checkbox-sesuai');
            const tidakSesuaiCheckbox = row.find('.verify-checkbox-tidak-sesuai');
            
            // Handle cascading dropdowns
            if (fieldName === 'merk') {
                // Brand changed - update Model dropdown
                const selectedMerk = realValue;
                const $modelDropdown = $('#verify_model');
                if ($modelDropdown.length) {
                    $modelDropdown.data('loaded', false);
                    $modelDropdown.data('parent-value', selectedMerk);
                    $modelDropdown.find('option:not(:first)').remove();
                    $modelDropdown.append('<option value="">Loading...</option>');
                    setTimeout(() => loadDropdownOptions(), 100);
                }
            } else if (fieldName === 'engine_type') {
                // Engine Type changed - update Model Mesin dropdown
                const selectedEngineType = realValue;
                const $modelMesinDropdown = $('#verify_model_mesin');
                if ($modelMesinDropdown.length) {
                    $modelMesinDropdown.data('loaded', false);
                    $modelMesinDropdown.data('parent-value', selectedEngineType);
                    $modelMesinDropdown.find('option:not(:first)').remove();
                    $modelMesinDropdown.append('<option value="">Loading...</option>');
                    setTimeout(() => loadDropdownOptions(), 100);
                }
            }
            
            // If value differs from database, auto-check "Tidak Sesuai"
            if (realValue.trim() !== dbValue.trim()) {
                sesuaiCheckbox.prop('checked', false);
                tidakSesuaiCheckbox.prop('checked', true);
                
                verifyField.prop('disabled', false).css({
                    'background-color': '#fef2f2',
                    'border-color': '#ef4444'
                });
                row.css('background-color', '#fef2f2');
                
                // Show alasan reject field
                $('#alasan-reject-container').slideDown(300);
                $('#alasan_reject_inline').prop('required', true);
            } else {
                const hasTidakSesuai = $('.verify-checkbox-tidak-sesuai:checked').length > 0;
                if (!hasTidakSesuai) {
                    $('#alasan-reject-container').slideUp(300);
                    $('#alasan_reject_inline').prop('required', false).val('');
                }
            }
            
            checkAllUnitVerifiedInline();
        });
    });

    window.toggleUnitDropdown = function(element) {
        const poId = $(element).data('po-id');
        $(element).toggleClass('open');
        $(`.child-po-${poId}`).each(function() {
            $(this).toggleClass('show');
        });
    };

    window.prepareUnitVerificationModal = function(element) {
        const data = $(element).data('unit');
        $('#modalUpdateSNLabel').text(`Verifikasi Unit: ${data.merk_unit || 'Unknown'} ${data.model_unit || 'Unknown'}`);
        $('#unit_id').val(data.id_po_unit);
        $('#unit_po_id').val(data.po_id);

        const container = $('#unit-verification-components');
        container.empty();

        // Build specification details array (same format as print packing list and workorder)
        const specDetails = [];
        
        // Add unit specifications
        if (data.nama_departemen) specDetails.push({label: 'Departemen', value: data.nama_departemen, required: false});
        if (data.jenis) specDetails.push({label: 'Jenis Unit', value: data.jenis, required: false});
        if (data.merk_unit) specDetails.push({label: 'Brand', value: data.merk_unit, required: false});
        if (data.model_unit) specDetails.push({label: 'Model', value: data.model_unit, required: false});
        if (data.tahun_po) specDetails.push({label: 'Tahun', value: data.tahun_po, required: false});
        if (data.kapasitas_unit) specDetails.push({label: 'Kapasitas', value: data.kapasitas_unit, required: false});
        if (data.tipe_mast) specDetails.push({label: 'Mast Type', value: data.tipe_mast + (data.tinggi_mast ? ' (' + data.tinggi_mast + ')' : ''), required: false});
        if (data.merk_mesin) specDetails.push({label: 'Engine Type', value: data.merk_mesin, required: false});
        if (data.model_mesin) specDetails.push({label: 'Model Mesin', value: data.model_mesin, required: false});
        if (data.tipe_ban) specDetails.push({label: 'Tire Type', value: data.tipe_ban, required: false});
        if (data.tipe_roda) specDetails.push({label: 'Wheel Type', value: data.tipe_roda, required: false});
        if (data.jumlah_valve) specDetails.push({label: 'Valve', value: data.jumlah_valve, required: false});
        if (data.keterangan) specDetails.push({label: 'Keterangan', value: data.keterangan, required: false});
        
        // Add Serial Numbers (required fields - always show even if empty)
        specDetails.push({label: 'Serial Number', value: data.serial_number_po || 'Belum ada SN', required: true, fieldName: 'sn_unit'});
        specDetails.push({label: 'SN Mast', value: data.sn_mast_po || 'Belum ada SN', required: true, fieldName: 'sn_mast'});
        specDetails.push({label: 'SN Mesin', value: data.sn_mesin_po || 'Belum ada SN', required: true, fieldName: 'sn_mesin'});

        // Tampilkan dropdown lokasi unit terlebih dahulu
        container.append(createLocationDropdownHTML());
        
        // Tampilkan tabel verifikasi (same format as print packing list and workorder)
        container.append(createVerificationTableHTML(specDetails));

        $('#modalUpdateSN').modal('show');
    };
    
    function createVerificationTableHTML(specDetails) {
        let tableRows = '';
        
        specDetails.forEach((spec, index) => {
            if (spec.value && spec.value !== '-' && spec.value !== 'Belum ada SN') {
                const fieldId = spec.fieldName || `verify_${spec.label.toLowerCase().replace(/\s+/g, '_')}`;
                tableRows += `
                    <tr data-spec-index="${index}">
                        <td style="font-weight: 500; background-color: #fafafa; padding: 8px;">${spec.label}${spec.required ? ' <span class="text-danger">*</span>' : ''}</td>
                        <td style="font-family: monospace; background-color: #fff; padding: 8px;"><input type="text" class="form-control form-control-sm" value="${spec.value}" readonly style="border: none; background: transparent; padding: 0;"></td>
                        <td style="background-color: #fff; padding: 8px;">
                            ${spec.required && spec.fieldName ? `
                                <input type="text" class="form-control form-control-sm verify-field" 
                                       id="${fieldId}" 
                                       name="${fieldId}" 
                                       value="${spec.value === 'Belum ada SN' ? '' : spec.value}" 
                                       placeholder="Masukkan ${spec.label.toLowerCase()} real"
                                       ${spec.required ? 'required' : ''}
                                       style="border: 1px solid #333; border-radius: 4px; padding: 4px 8px;">
                            ` : `
                                <input type="text" class="form-control form-control-sm verify-field" 
                                       id="verify_${spec.label.toLowerCase().replace(/\s+/g, '_')}" 
                                       name="verify_${spec.label.toLowerCase().replace(/\s+/g, '_')}" 
                                       placeholder="Masukkan ${spec.label.toLowerCase()} real"
                                       style="border: 1px solid #333; border-radius: 4px; padding: 4px 8px;">
                            `}
                        </td>
                        <td class="text-center" style="background-color: #fafafa; padding: 8px;">
                            <input type="checkbox" class="form-check-input verify-checkbox" 
                                   data-spec-index="${index}"
                                   style="cursor: pointer;">
                        </td>
                    </tr>
                `;
            } else if (spec.required) {
                // Always show required SN fields even if empty
                const fieldId = spec.fieldName || `verify_${spec.label.toLowerCase().replace(/\s+/g, '_')}`;
                tableRows += `
                    <tr data-spec-index="${index}">
                        <td style="font-weight: 500; background-color: #fafafa; padding: 8px;">${spec.label} <span class="text-danger">*</span></td>
                        <td style="font-family: monospace; background-color: #fff; padding: 8px;"><input type="text" class="form-control form-control-sm" value="${spec.value || 'Belum ada SN'}" readonly style="border: none; background: transparent; padding: 0;"></td>
                        <td style="background-color: #fff; padding: 8px;">
                            <input type="text" class="form-control form-control-sm verify-field" 
                                   id="${fieldId}" 
                                   name="${fieldId}" 
                                   placeholder="Masukkan ${spec.label.toLowerCase()} real"
                                   required
                                   style="border: 1px solid #333; border-radius: 4px; padding: 4px 8px;">
                        </td>
                        <td class="text-center" style="background-color: #fafafa; padding: 8px;">
                            <input type="checkbox" class="form-check-input verify-checkbox" 
                                   data-spec-index="${index}"
                                   style="cursor: pointer;">
                        </td>
                    </tr>
                `;
            }
        });
        
        return `
            <div class="verification-table-section mb-4">
                <div class="d-flex align-items-center mb-3">
                    <div style="width: 36px; height: 36px; background-color: #f3f4f6; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                        <i class="fas fa-clipboard-check" style="font-size: 1rem; color: #6b7280;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold" style="color: #111827; font-size: 1rem;">Verifikasi Data Unit</h6>
                        <small class="text-muted" style="font-size: 0.85rem;">Periksa setiap item dan isi data real lapangan jika berbeda</small>
                    </div>
                </div>
                <div class="alert alert-info mb-3" style="font-size: 0.85rem;">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Cara kerja:</strong> Jika data sama dengan database → Centang "Sesuai" | Jika berbeda/kosong → Isi data real di kolom "Real Lapangan"
                    <br>
                    <span class="text-danger fw-bold">⚠️ Field bertanda * wajib diisi</span>
                </div>
                <table class="table table-sm table-bordered mb-0" style="font-size: 0.875rem;">
                    <thead>
                        <tr style="background-color: #f8f9fa;">
                            <th style="width: 25%; text-align: center; font-weight: bold; border: 1px solid #333; padding: 8px;">Item</th>
                            <th style="width: 30%; text-align: center; font-weight: bold; border: 1px solid #333; padding: 8px;">Database</th>
                            <th style="width: 30%; text-align: center; font-weight: bold; border: 1px solid #333; padding: 8px;">Real Lapangan</th>
                            <th style="width: 15%; text-align: center; font-weight: bold; border: 1px solid #333; padding: 8px;">Sesuai</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${tableRows || '<tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>'}
                    </tbody>
                </table>
            </div>
        `;
    }

    function createLocationDropdownHTML() {
        return `
            <div class="location-section mb-4" style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
                <div class="d-flex align-items-center mb-3">
                    <div style="width: 36px; height: 36px; background-color: #f3f4f6; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                        <i class="fas fa-map-marker-alt" style="font-size: 1rem; color: #6b7280;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold" style="color: #111827; font-size: 1rem;">Lokasi Unit</h6>
                        <small class="text-muted" style="font-size: 0.85rem;">Tentukan lokasi penyimpanan unit</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <label for="lokasi_unit" class="form-label fw-semibold" style="color: #374151; font-size: 0.9rem; margin-bottom: 8px;">Pilih Lokasi Unit</label>
                        <select class="form-select" id="lokasi_unit" name="lokasi_unit" required style="border: 1px solid #d1d5db; border-radius: 6px; padding: 8px 12px; font-size: 0.9rem; background-color: #ffffff;">
                            <option value="">-- Pilih Lokasi Penyimpanan --</option>
                            <option value="POS 1">POS 1</option>
                            <option value="POS 2">POS 2</option>
                            <option value="POS 3">POS 3</option>
                            <option value="POS 4">POS 4</option>
                            <option value="POS 5">POS 5</option>
                        </select>
                        <div class="form-text" style="color: #6b7280; font-size: 0.8rem; margin-top: 4px;">
                            Lokasi ini akan digunakan untuk inventory unit setelah verifikasi selesai
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center h-100">
                            <div class="text-center w-100" style="background-color: #f0f9ff; border: 1px solid #bae6fd; border-radius: 6px; padding: 16px;">
                                <i class="fas fa-warehouse mb-2" style="font-size: 1.3rem; color: #0ea5e9;"></i>
                                <div class="fw-semibold" style="font-size: 0.8rem; color: #0369a1;">Inventory Ready</div>
                                <small class="text-muted" style="font-size: 0.75rem;">Siap untuk inventory</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function createUnitComponentHTML(component) {
        const snInputHTML = component.sn ? `
            <div class="sn-input-group mb-3">
                <label class="form-label fw-semibold" style="color: #374151; font-size: 0.9rem; margin-bottom: 6px;">Serial Number</label>
                <div style="display: flex; gap: 8px; align-items: end;">
                    <input type="text" 
                           class="form-control sn-input" 
                           id="sn_${component.id}" 
                           data-component-id="${component.id}"
                           value="${component.snValue || ''}"
                           placeholder="Masukkan serial number..."
                           readonly
                           style="border: 1px solid #d1d5db; border-radius: 6px; padding: 8px 12px; font-size: 0.9rem; background-color: #f9fafb;">
                    <button class="btn btn-outline-secondary" type="button" onclick="editSN('${component.id}')" 
                            style="font-size: 0.8rem; padding: 8px 12px; border-radius: 6px;">
                        Edit
                    </button>
                </div>
            </div>` : '';

        return `
            <div class="verification-component mb-3" data-component="${component.id}" data-status="menunggu" 
                 style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px;">
                <div class="component-header mb-3" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f3f4f6; padding-bottom: 12px;">
                    <div style="display: flex; align-items: center; flex: 1;">
                        <div style="width: 32px; height: 32px; background-color: #f3f4f6; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fas fa-cog" style="font-size: 0.9rem; color: #6b7280;"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold" style="color: #111827; font-size: 1rem;">${component.label}</h6>
                            <small class="text-muted" style="font-size: 0.85rem;">${component.desc}</small>
                        </div>
                    </div>
                    <div class="verification-buttons" style="display: flex; gap: 8px;">
                        <button type="button" class="btn btn-success btn-verify" onclick="setUnitComponentStatus('${component.id}', 'sesuai', this)"
                                style="padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                            Sesuai
                        </button>
                        <button type="button" class="btn btn-danger btn-verify" onclick="setUnitComponentStatus('${component.id}', 'tidak-sesuai', this)"
                                style="padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                            Tidak Sesuai
                        </button>
                    </div>
                </div>
                
                ${snInputHTML}
                
                <div class="note-input-group" style="display: none;">
                    <label class="form-label fw-semibold" style="color: #374151; font-size: 0.9rem; margin-bottom: 6px;">
                        Catatan Ketidaksesuaian
                    </label>
                    <textarea class="form-control note-input" 
                              id="note_${component.id}" 
                              rows="3" 
                              placeholder="Jelaskan ketidaksesuaian yang ditemukan..."
                              style="border: 1px solid #fbbf24; border-radius: 6px; padding: 8px 12px; font-size: 0.9rem; resize: vertical; background-color: #fffbeb;"></textarea>
                    <div class="form-text" style="color: #92400e; font-size: 0.8rem; margin-top: 4px;">
                        Pastikan catatan jelas dan informatif untuk tim perbaikan
                    </div>
                </div>
            </div>
        `;
    }

    window.editSN = function(componentId) {
        const input = $(`#sn_${componentId}`);
        const editBtn = input.siblings('button');
        
        if (input.prop('readonly')) {
            input.prop('readonly', false).focus();
            editBtn.text('Save').removeClass('btn-outline-secondary').addClass('btn-success');
        } else {
            input.prop('readonly', true);
            editBtn.text('Edit').removeClass('btn-success').addClass('btn-outline-secondary');
        }
    };

    window.setUnitComponentStatus = function(componentId, status, button) {
        const component = $(`[data-component="${componentId}"]`);
        component.attr('data-status', status);
        
        // Update button states
        $(button).addClass('active').siblings().removeClass('active');
        
        const snGroup = component.find('.sn-input-group');
        const noteGroup = component.find('.note-input-group');
        
        if (status === 'sesuai') {
            component.css({
                'border-color': '#10b981',
                'background-color': '#f0fdf4'
            });
            
            if (snGroup.length) {
                snGroup.show();
                snGroup.find('input').css({
                    'border-color': '#10b981',
                    'background-color': '#ffffff'
                });
            }
            noteGroup.hide();
            
        } else if (status === 'tidak-sesuai') {
            component.css({
                'border-color': '#ef4444',
                'background-color': '#fef2f2'
            });
            
            snGroup.hide();
            noteGroup.show();
            noteGroup.find('textarea').css({
                'border-color': '#ef4444',
                'background-color': '#ffffff'
            });
        }
        
        checkAllUnitVerified();
    };

    function checkAllUnitVerifiedInline() {
        // Check all rows have either "Sesuai" or "Tidak Sesuai" checked
        let allVerified = true;
        let allRowsVerified = true;
        
        $('#unitVerificationFormInline tbody tr').each(function() {
            const row = $(this);
            const sesuaiCheckbox = row.find('.verify-checkbox-sesuai');
            const tidakSesuaiCheckbox = row.find('.verify-checkbox-tidak-sesuai');
            const verifyField = row.find('.verify-field');
            const isRequired = row.find('td:first').html().includes('<span class="text-danger">*</span>');
            
            // Setiap baris harus punya salah satu checkbox yang dicentang
            const isSesuaiChecked = sesuaiCheckbox.is(':checked');
            const isTidakSesuaiChecked = tidakSesuaiCheckbox.is(':checked');
            
            if (!isSesuaiChecked && !isTidakSesuaiChecked) {
                allRowsVerified = false;
                allVerified = false;
                return false; // break loop
            }
            
            // Jika "Tidak Sesuai" dicentang, field "Real Lapangan" wajib diisi
            if (isTidakSesuaiChecked) {
                if (!verifyField.val() || verifyField.val().trim() === '') {
                    allVerified = false;
                    return false; // break loop
                }
            }
            
            // Jika "Sesuai" dicentang, field harus terisi (otomatis dari database)
            if (isSesuaiChecked) {
                if (!verifyField.val() || verifyField.val().trim() === '') {
                    allVerified = false;
                    return false; // break loop
                }
            }
        });
        
        const lokasiSelected = $('#lokasi_unit_inline').val() !== '';
        
        // Button enabled hanya jika:
        // 1. Semua baris punya checkbox yang dicentang
        // 2. Jika "Tidak Sesuai", field "Real Lapangan" terisi
        // 3. Lokasi Unit terpilih
        $('#btn-submit-verification-inline').prop('disabled', !allVerified || !lokasiSelected);
    }

    function submitUnitVerificationInline() {
        if (window._verifyingUnit) return;
        
        const form = $('#unitVerificationFormInline');
        const idUnit = form.data('unit-id');
        const poId = form.data('po-id');
        const lokasiUnit = $('#lokasi_unit_inline').val();
        
        // Validasi lokasi unit
        if (!lokasiUnit) {
            Swal.fire({icon:'warning', title:'Lokasi Wajib', text:'Pilih lokasi unit terlebih dahulu.'});
            return;
        }
        
        let finalStatus = 'Sesuai';
        let fullNotes = [];
        const snData = {};
        const discrepancies = []; // Array untuk menyimpan discrepancy data yang terstruktur
        
        // Collect data from verification table
        form.find('tbody tr').each(function() {
            const row = $(this);
            const label = row.find('td:first').text().replace(/\s*\*/g, '').trim();
            
            // Ambil nilai database (dari kolom kedua)
            const dbInput = row.find('td:nth-child(2) input, td:nth-child(2) textarea');
            const dbSelect = row.find('td:nth-child(2) select');
            let dbValue = '';
            if (dbSelect.length > 0) {
                dbValue = dbSelect.find('option:selected').text().trim();
            } else if (dbInput.length > 0) {
                dbValue = dbInput.val() || '';
            }
            
            // Ambil nilai real (dari kolom ketiga)
            const realInput = row.find('.verify-field:not(.verify-dropdown)');
            const realSelect = row.find('.verify-dropdown');
            let realValue = '';
            if (realSelect.length > 0) {
                realValue = realSelect.find('option:selected').text().trim();
            } else if (realInput.length > 0) {
                realValue = realInput.val() || '';
            }
            
            const sesuaiCheckbox = row.find('.verify-checkbox-sesuai');
            const tidakSesuaiCheckbox = row.find('.verify-checkbox-tidak-sesuai');
            const fieldName = row.data('field');
            
            // Check status verifikasi
            const isSesuai = sesuaiCheckbox.is(':checked');
            const isTidakSesuai = tidakSesuaiCheckbox.is(':checked');
            
            // Hanya tentukan status berdasarkan checkbox yang dicentang
            // Jika "Tidak Sesuai" dicentang, maka status = "Tidak Sesuai"
            if (isTidakSesuai) {
                finalStatus = 'Tidak Sesuai';
                // Catat perbedaannya jika ada
                const dbVal = dbValue ? String(dbValue).trim() : '';
                const realVal = realValue ? String(realValue).trim() : '';
                
                // Simpan discrepancy untuk semua field yang "Tidak Sesuai" (meskipun nilainya sama)
                // Ini penting untuk tracking bahwa field ini memang tidak sesuai
                discrepancies.push({
                    field_name: fieldName || label.toLowerCase().replace(/\s+/g, '_'),
                    database_value: dbVal,
                    real_value: realVal
                });
                
                // Juga simpan dalam fullNotes untuk ditampilkan di modal
                fullNotes.push(`${label}: Database = "${dbVal}", Real = "${realVal}"`);
            }
            // Jika "Sesuai" dicentang, anggap sesuai (tidak perlu cek perbedaan nilai)
            // Karena saat "Sesuai" dicentang, nilai "Real Lapangan" sudah otomatis disamakan dengan "Database"
            
            // Collect SN data (prioritaskan real value jika ada, jika tidak gunakan db value)
            // SN selalu input text, bukan dropdown
            if (fieldName && (fieldName.includes('sn_') || fieldName === 'sn_unit' || fieldName === 'sn_mast' || fieldName === 'sn_mesin')) {
                // Untuk SN, ambil dari input field langsung (bukan dari text option)
                const snRealInput = row.find('.verify-field[data-field="' + fieldName + '"]');
                const snDbInput = row.find('td:nth-child(2) input[data-field="' + fieldName + '"]');
                const snRealVal = snRealInput.length > 0 ? snRealInput.val() || '' : '';
                const snDbVal = snDbInput.length > 0 ? snDbInput.val() || '' : '';
                const snValue = (snRealVal && snRealVal.trim() !== '') ? snRealVal : snDbVal;
                if (snValue && snValue !== 'Belum ada SN' && snValue.trim() !== '') {
                    snData[fieldName] = snValue.trim();
                }
            }
        });
        
        // Pastikan finalStatus ditentukan dengan benar berdasarkan checkbox yang dicentang
        // Cek ulang apakah ada checkbox "Tidak Sesuai" yang dicentang
        let hasTidakSesuai = false;
        form.find('tbody tr').each(function() {
            const row = $(this);
            const tidakSesuaiCheckbox = row.find('.verify-checkbox-tidak-sesuai');
            if (tidakSesuaiCheckbox.is(':checked')) {
                hasTidakSesuai = true;
                return false; // break loop
            }
        });
        
        // Update finalStatus berdasarkan checkbox yang benar-benar dicentang
        if (hasTidakSesuai) {
            finalStatus = 'Tidak Sesuai';
        } else {
            finalStatus = 'Sesuai';
        }
        
        // Validate required SN fields untuk status "Sesuai"
        if (finalStatus === 'Sesuai' && (!snData['sn_unit'] || !snData['sn_mesin'])) {
            Swal.fire({icon:'warning', title:'SN Wajib', text:'Serial number Unit dan Mesin wajib diisi untuk status Sesuai.'});
            return;
        }
        
        // Jika status "Tidak Sesuai", validasi alasan reject
        if (finalStatus === 'Tidak Sesuai') {
            const alasanReject = $('#alasan_reject_inline').val().trim();
            if (!alasanReject) {
                Swal.fire({
                    icon: 'warning', 
                    title: 'Alasan Reject Wajib', 
                    text: 'Mohon isi alasan reject/ketidaksesuaian yang ditemukan. Alasan ini diperlukan untuk ditindaklanjuti oleh tim Purchasing.'
                });
                $('#alasan_reject_inline').focus();
                return;
            }
            // Gabungkan alasan reject dengan notes
            fullNotes.unshift(`Alasan Reject: ${alasanReject}`);
        }
        
        // Tampilkan modal konfirmasi sebelum kirim ke database
        showVerificationConfirmation(idUnit, poId, finalStatus, snData, fullNotes, lokasiUnit, discrepancies);
    }

    function showVerificationConfirmation(idUnit, poId, finalStatus, snData, fullNotes, lokasiUnit, discrepancies = []) {
        // Siapkan ringkasan data untuk ditampilkan
        let summaryHTML = '<div style="text-align: left; margin-top: 15px;">';
        summaryHTML += '<div style="margin-bottom: 10px;"><strong>Status Verifikasi:</strong> ';
        if (finalStatus === 'Sesuai') {
            summaryHTML += '<span style="color: #10b981; font-weight: bold;">✓ SESUAI</span>';
        } else {
            summaryHTML += '<span style="color: #ef4444; font-weight: bold;">✗ TIDAK SESUAI</span>';
        }
        summaryHTML += '</div>';
        
        summaryHTML += '<div style="margin-bottom: 10px;"><strong>Lokasi Unit:</strong> <span style="color: #3b82f6;">' + lokasiUnit + '</span></div>';
        
        // Tampilkan Serial Numbers yang akan disimpan
        if (Object.keys(snData).length > 0) {
            summaryHTML += '<div style="margin-bottom: 10px;"><strong>Serial Numbers:</strong><ul style="margin: 5px 0; padding-left: 20px;">';
            if (snData['sn_unit']) summaryHTML += '<li>Serial Number: <code>' + snData['sn_unit'] + '</code></li>';
            if (snData['sn_mast']) summaryHTML += '<li>SN Mast: <code>' + snData['sn_mast'] + '</code></li>';
            if (snData['sn_mesin']) summaryHTML += '<li>SN Mesin: <code>' + snData['sn_mesin'] + '</code></li>';
            if (snData['sn_baterai']) summaryHTML += '<li>SN Baterai: <code>' + snData['sn_baterai'] + '</code></li>';
            summaryHTML += '</ul></div>';
        }
        
        // Jika "Tidak Sesuai", tampilkan catatan
        if (finalStatus === 'Tidak Sesuai' && fullNotes.length > 0) {
            summaryHTML += '<div style="margin-bottom: 10px;"><strong>Catatan Ketidaksesuaian:</strong><div style="background: #fef2f2; padding: 10px; border-radius: 5px; margin-top: 5px; font-size: 0.9em; color: #991b1b;">';
            fullNotes.forEach(note => {
                summaryHTML += '<div style="margin-bottom: 5px;">• ' + note + '</div>';
            });
            summaryHTML += '</div></div>';
        }
        
        summaryHTML += '<div style="margin-top: 15px; padding: 10px; background: #f0f9ff; border-left: 4px solid #3b82f6; border-radius: 4px;">';
        if (finalStatus === 'Sesuai') {
            summaryHTML += '<strong>⚠️ Perhatian:</strong> Setelah dikonfirmasi, unit akan dimasukkan ke inventory dan status PO akan diupdate. Pastikan semua data sudah benar.';
        } else {
            summaryHTML += '<strong>⚠️ Perhatian:</strong> Item yang tidak sesuai akan dikirim ke tim Purchasing untuk ditindaklanjuti. Pastikan alasan reject sudah jelas.';
        }
        summaryHTML += '</div></div>';
        
        // Tampilkan modal konfirmasi
        Swal.fire({
            title: 'Konfirmasi Verifikasi Unit',
            html: summaryHTML,
            icon: finalStatus === 'Sesuai' ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonColor: finalStatus === 'Sesuai' ? '#10b981' : '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: finalStatus === 'Sesuai' ? '<i class="fas fa-check-circle me-2"></i>Ya, Verifikasi Sesuai' : '<i class="fas fa-exclamation-triangle me-2"></i>Ya, Konfirmasi Tidak Sesuai',
            cancelButtonText: '<i class="fas fa-times me-2"></i>Batal',
            reverseButtons: true,
            width: '600px',
            customClass: {
                popup: 'text-left'
            },
            didOpen: () => {
                // Styling untuk summary
                const popup = Swal.getPopup();
                if (popup) {
                    popup.style.textAlign = 'left';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika user konfirmasi, baru kirim ke database
                updateUnitStatusVerifikasi(idUnit, poId, finalStatus, snData, fullNotes.join('; '), lokasiUnit, discrepancies);
            } else {
                // Jika user batal, tidak ada yang dilakukan
                console.log('Verifikasi dibatalkan oleh user');
            }
        });
    }

    function createUnitDetailCard(data) {
        const h = (str) => str ? String(str).replace(/</g, '&lt;') : "-";
        
        // Build specification details array (same format as workorder verification)
        const specDetails = [];
        
        // Add unit specifications with dropdown type mapping
        // Mapping: fieldName -> dropdown type (null = text input, 'dropdown' = use dropdown)
        const dropdownFieldMap = {
            'departemen': 'departemen',
            'jenis_unit': 'tipe_unit',
            'merk': 'merk_unit', // Dropdown from merk_unit
            'model': 'model_unit', // Cascading dropdown filtered by merk
            'tahun': null, // Text field (year)
            'kapasitas': 'kapasitas',
            'mast_type': 'tipe_mast',
            'engine_type': 'merk_mesin', // Dropdown from merk_mesin
            'model_mesin': 'model_mesin', // Cascading dropdown filtered by engine_type
            'tire_type': 'tipe_ban',
            'wheel_type': 'jenis_roda',
            'valve': 'valve',
            'keterangan': null // Textarea
        };
        
        if (data.nama_departemen) specDetails.push({label: 'Departemen', value: h(data.nama_departemen), fieldName: 'departemen', required: false, dropdownType: 'departemen'});
        if (data.jenis) specDetails.push({label: 'Jenis Unit', value: h(data.jenis), fieldName: 'jenis_unit', required: false, dropdownType: 'tipe_unit'});
        if (data.merk_unit) specDetails.push({label: 'Brand', value: h(data.merk_unit), fieldName: 'merk', required: false, dropdownType: 'merk_unit', cascadingParent: null});
        if (data.model_unit) specDetails.push({label: 'Model', value: h(data.model_unit), fieldName: 'model', required: false, dropdownType: 'model_unit', cascadingParent: 'merk', parentValue: h(data.merk_unit)});
        if (data.tahun_po) specDetails.push({label: 'Tahun', value: h(data.tahun_po), fieldName: 'tahun', required: false, dropdownType: null});
        if (data.kapasitas_unit) specDetails.push({label: 'Kapasitas', value: h(data.kapasitas_unit), fieldName: 'kapasitas', required: false, dropdownType: 'kapasitas'});
        if (data.tipe_mast) specDetails.push({label: 'Mast Type', value: h(data.tipe_mast) + (data.tinggi_mast ? ' (' + h(data.tinggi_mast) + ')' : ''), fieldName: 'mast_type', required: false, dropdownType: 'tipe_mast'});
        if (data.merk_mesin) specDetails.push({label: 'Engine Type', value: h(data.merk_mesin), fieldName: 'engine_type', required: false, dropdownType: 'merk_mesin', cascadingParent: null});
        if (data.model_mesin) specDetails.push({label: 'Model Mesin', value: h(data.model_mesin), fieldName: 'model_mesin', required: false, dropdownType: 'model_mesin', cascadingParent: 'engine_type', parentValue: h(data.merk_mesin)});
        if (data.tipe_ban) specDetails.push({label: 'Tire Type', value: h(data.tipe_ban), fieldName: 'tire_type', required: false, dropdownType: 'tipe_ban'});
        if (data.tipe_roda) specDetails.push({label: 'Wheel Type', value: h(data.tipe_roda), fieldName: 'wheel_type', required: false, dropdownType: 'jenis_roda'});
        if (data.jumlah_valve) specDetails.push({label: 'Valve', value: h(data.jumlah_valve), fieldName: 'valve', required: false, dropdownType: 'valve'});
        if (data.keterangan) specDetails.push({label: 'Keterangan', value: h(data.keterangan), fieldName: 'keterangan', required: false, isTextarea: true, dropdownType: null});
        
        // Add Serial Numbers (required fields - always show even if empty)
        specDetails.push({label: 'Serial Number', value: h(data.serial_number_po) || 'Belum ada SN', fieldName: 'sn_unit', required: true});
        specDetails.push({label: 'SN Mast', value: h(data.sn_mast_po) || 'Belum ada SN', fieldName: 'sn_mast', required: true});
        specDetails.push({label: 'SN Mesin', value: h(data.sn_mesin_po) || 'Belum ada SN', fieldName: 'sn_mesin', required: true});
        
        // Build table rows with editable fields (like workorder)
        let tableRows = '';
        specDetails.forEach((spec, index) => {
            const fieldId = `verify_${spec.fieldName || spec.label.toLowerCase().replace(/\s+/g, '_')}`;
            const checkId = `check_${spec.fieldName || spec.label.toLowerCase().replace(/\s+/g, '_')}`;
            const dbId = `db_${spec.fieldName || spec.label.toLowerCase().replace(/\s+/g, '_')}`;
            
            // Nilai database
            const dbValue = spec.value && spec.value !== '-' && spec.value !== 'Belum ada SN' ? spec.value : '';
            // Nilai awal "Real Lapangan" selalu sama dengan "Database" (akan berubah jika user edit)
            const realValue = dbValue;
            
            if (spec.isTextarea) {
                tableRows += `
                    <tr data-field="${spec.fieldName}">
                        <td style="font-weight: 500; background-color: #fafafa; padding: 8px; vertical-align: middle;">${spec.label}${spec.required ? ' <span class="text-danger">*</span>' : ''}</td>
                        <td style="background-color: #fff; padding: 8px;">
                            <textarea class="form-control form-control-sm" id="${dbId}" readonly rows="2" style="border: none; background: transparent; padding: 0; resize: none;">${dbValue}</textarea>
                        </td>
                        <td style="background-color: #fff; padding: 8px;">
                            <textarea class="form-control form-control-sm verify-field" 
                                      id="${fieldId}" 
                                      name="${fieldId}" 
                                      rows="2" 
                                      placeholder="Masukkan ${spec.label.toLowerCase()} real"
                                      style="border: 1px solid #333; border-radius: 4px; padding: 4px 8px; resize: vertical;">${realValue}</textarea>
                        </td>
                        <td class="text-center" style="background-color: #fafafa; padding: 8px; vertical-align: middle;">
                            <input type="checkbox" class="form-check-input verify-checkbox-sesuai" 
                                   id="${checkId}_sesuai" 
                                   data-field="${spec.fieldName}"
                                   data-row-index="${index}"
                                   style="cursor: pointer;">
                        </td>
                        <td class="text-center" style="background-color: #fafafa; padding: 8px; vertical-align: middle;">
                            <input type="checkbox" class="form-check-input verify-checkbox-tidak-sesuai" 
                                   id="${checkId}_tidak_sesuai" 
                                   data-field="${spec.fieldName}"
                                   data-row-index="${index}"
                                   style="cursor: pointer;">
                        </td>
                    </tr>
                `;
            } else {
                // Check if this field should use dropdown (not SN fields)
                const isSNField = spec.fieldName && (spec.fieldName.includes('sn_') || spec.fieldName === 'sn_unit' || spec.fieldName === 'sn_mast' || spec.fieldName === 'sn_mesin');
                const useDropdown = spec.dropdownType && !isSNField;
                
                let realFieldInput = '';
                if (useDropdown) {
                    // Check if this is a cascading dropdown
                    const isCascading = spec.cascadingParent && spec.parentValue;
                    const cascadingAttr = isCascading ? `data-cascading-parent="${spec.cascadingParent}" data-parent-value="${spec.parentValue}"` : '';
                    
                    // Create dropdown for non-SN fields
                    realFieldInput = `
                        <select class="form-select form-select-sm verify-field verify-dropdown" 
                                id="${fieldId}" 
                                name="${fieldId}"
                                data-dropdown-type="${spec.dropdownType}"
                                data-field-name="${spec.fieldName}"
                                ${cascadingAttr}
                                ${spec.required ? 'required' : ''}
                                style="border: 1px solid #333; border-radius: 4px; padding: 4px 8px;">
                            <option value="">Pilih ${spec.label}...</option>
                            <option value="${realValue}" selected>${realValue || 'Loading...'}</option>
                        </select>
                    `;
                } else {
                    // Text input for SN fields and other text fields
                    realFieldInput = `
                        <input type="text" class="form-control form-control-sm verify-field" 
                               id="${fieldId}" 
                               name="${fieldId}" 
                               value="${realValue}" 
                               placeholder="Masukkan ${spec.label.toLowerCase()} real"
                               ${spec.required ? 'required' : ''}
                               style="border: 1px solid #333; border-radius: 4px; padding: 4px 8px;">
                    `;
                }
                
                // For Database column, use text input for all fields (readonly)
                const dbFieldInput = `<input type="text" class="form-control form-control-sm" id="${dbId}" value="${dbValue}" readonly style="border: none; background: transparent; padding: 0;">`;
                
                tableRows += `
                    <tr data-field="${spec.fieldName}">
                        <td style="font-weight: 500; background-color: #fafafa; padding: 8px; vertical-align: middle;">${spec.label}${spec.required ? ' <span class="text-danger">*</span>' : ''}</td>
                        <td style="background-color: #fff; padding: 8px;">
                            ${dbFieldInput}
                        </td>
                        <td style="background-color: #fff; padding: 8px;">
                            ${realFieldInput}
                        </td>
                        <td class="text-center" style="background-color: #fafafa; padding: 8px; vertical-align: middle;">
                            <input type="checkbox" class="form-check-input verify-checkbox-sesuai" 
                                   id="${checkId}_sesuai" 
                                   data-field="${spec.fieldName}"
                                   data-row-index="${index}"
                                   style="cursor: pointer;">
                        </td>
                        <td class="text-center" style="background-color: #fafafa; padding: 8px; vertical-align: middle;">
                            <input type="checkbox" class="form-check-input verify-checkbox-tidak-sesuai" 
                                   id="${checkId}_tidak_sesuai" 
                                   data-field="${spec.fieldName}"
                                   data-row-index="${index}"
                                   style="cursor: pointer;">
                        </td>
                    </tr>
                `;
            }
        });
        
        return `
            <form id="unitVerificationFormInline" data-unit-id="${data.id_po_unit}" data-po-id="${data.po_id}">
                <div class="card table-card animate__animated animate__fadeIn">
                    <div class="card-header p-3" style="background-color: #f5f5f5; border-bottom: 1px solid #ccc;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold m-0" style="font-size: 1rem; color: #000;">
                                <i class="fas fa-clipboard-check me-2"></i>Verifikasi Data Unit: ${h(data.merk_unit)} ${h(data.model_unit)}
                            </h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info mb-3" style="font-size: 0.85rem;">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Cara kerja:</strong> 
                            <ul class="mb-0" style="padding-left: 20px;">
                                <li>Jika data <strong>sama</strong> dengan database → Centang <strong>"Sesuai"</strong></li>
                                <li>Jika data <strong>berbeda/kosong</strong> → Centang <strong>"Tidak Sesuai"</strong> dan isi data real di kolom "Real Lapangan"</li>
                            </ul>
                            <span class="text-danger fw-bold d-block mt-2">⚠️ Semua baris harus memiliki checkbox "Sesuai" atau "Tidak Sesuai" yang dicentang. Field bertanda * wajib diisi jika "Tidak Sesuai".</span>
                        </div>
                        
                        <table class="table table-sm table-bordered mb-3" style="font-size: 0.875rem;">
                            <thead>
                                <tr style="background-color: #f8f9fa;">
                                    <th style="width: 20%; text-align: center; font-weight: bold; border: 1px solid #333; padding: 8px;">Item</th>
                                    <th style="width: 25%; text-align: center; font-weight: bold; border: 1px solid #333; padding: 8px;">Database</th>
                                    <th style="width: 25%; text-align: center; font-weight: bold; border: 1px solid #333; padding: 8px;">Real Lapangan</th>
                                    <th style="width: 15%; text-align: center; font-weight: bold; border: 1px solid #333; padding: 8px;">Sesuai</th>
                                    <th style="width: 15%; text-align: center; font-weight: bold; border: 1px solid #333; padding: 8px;">Tidak Sesuai</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows || '<tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>'}
                            </tbody>
                        </table>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="lokasi_unit_inline" class="form-label fw-semibold">
                                    <i class="fas fa-map-marker-alt me-2"></i>Lokasi Unit <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="lokasi_unit_inline" name="lokasi_unit" required>
                                    <option value="">-- Pilih Lokasi Penyimpanan --</option>
                                    <option value="POS 1">POS 1</option>
                                    <option value="POS 2">POS 2</option>
                                    <option value="POS 3">POS 3</option>
                                    <option value="POS 4">POS 4</option>
                                    <option value="POS 5">POS 5</option>
                                </select>
                                <div class="form-text">Lokasi ini akan digunakan untuk inventory unit setelah verifikasi selesai</div>
                            </div>
                        </div>
                        
                        <div class="row mb-3" id="alasan-reject-container" style="display: none;">
                            <div class="col-12">
                                <label for="alasan_reject_inline" class="form-label fw-semibold">
                                    <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Alasan Reject <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" 
                                          id="alasan_reject_inline" 
                                          name="alasan_reject" 
                                          rows="3" 
                                          placeholder="Jelaskan alasan reject/ketidaksesuaian yang ditemukan..."
                                          style="border: 1px solid #fbbf24; border-radius: 6px; padding: 8px 12px; font-size: 0.9rem; resize: vertical; background-color: #fffbeb;"></textarea>
                                <div class="form-text text-warning">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Alasan reject wajib diisi jika ada item yang tidak sesuai. Alasan ini akan dikirim ke tim Purchasing untuk ditindaklanjuti.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button type="button" class="btn btn-success" id="btn-submit-verification-inline" disabled>
                            <i class="fas fa-check-circle me-2"></i>Submit Verifikasi
                        </button>
                    </div>
                </div>
            </form>`;
    }

    function updateUnitStatusVerifikasi(idUnit, poId, status, snData = {}, catatan = '', lokasiUnit = '', discrepancies = []) {
        window._verifyingUnit = true;
        $('#btn-submit-verification-inline, #btn-submit-unit-verification').prop('disabled', true);
        
        $.ajax({
            type: "POST",
            url: "<?= base_url('warehouse/purchase-orders/verify-po-unit') ?>",
            data: {
                id_unit: idUnit,
                po_id: poId,
                status: status,
                catatan_verifikasi: catatan,
                lokasi_unit: lokasiUnit,
                discrepancies: JSON.stringify(discrepancies), // Kirim discrepancy data sebagai JSON string
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                // Map SN data ke format yang diharapkan backend
                sn_unit: snData['sn_unit'] || '',
                sn_mast: snData['sn_mast'] || '',
                sn_mesin: snData['sn_mesin'] || '',
                sn_baterai: snData['sn_baterai'] || ''
            },
            dataType: "JSON",
            beforeSend: function() {
                // Log data yang akan dikirim untuk debugging
                console.log('Sending verification data:', {
                    id_unit: idUnit,
                    po_id: poId,
                    status: status,
                    discrepancies_count: discrepancies.length,
                    discrepancies: discrepancies
                });
                Swal.showLoading();
            },
            success: function(r) {
                window._verifyingUnit = false;
                $('#btn-submit-verification-inline, #btn-submit-unit-verification').prop('disabled', false);
                Swal.close();
                
                if (r.statusCode == 200) {
                    $('#modalUpdateSN').modal('hide');
                    let sisaElem = $(`#lbl-remain-po-${poId}`);
                    let sisaCount = parseInt(sisaElem.text()) - 1;
                    sisaElem.text(`${sisaCount} Unit`);
                    
                    $(`#list-item-${idUnit}`).fadeOut(500, function() { 
                        $(this).remove(); 
                        if (sisaCount === 0) {
                            $(`[data-po-id="${poId}"]`).fadeOut(500);
                        }
                    });
                    
                    $('#unit-detail-view-container').html(`
                        <div class="card table-card">
                            <div class="card-body text-center p-5">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h5 class="text-muted">Verifikasi berhasil! Silakan pilih unit lain.</h5>
                            </div>
                        </div>
                    `);
                    unitToast('success', r.message || 'Unit berhasil diverifikasi.');
                } else {
                    unitToast('error', r.message || 'Verifikasi gagal.');
                    Swal.fire({ icon: 'error', title: 'Error', text: r.message || 'Verifikasi gagal.' });
                }
            },
            error: function(xhr, status, error) {
                window._verifyingUnit = false;
                $('#btn-submit-verification-inline, #btn-submit-unit-verification').prop('disabled', false);
                Swal.close();
                
                // Log error detail untuk debugging
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });
                
                // Coba parse response jika ada
                let errorMessage = 'Terjadi kesalahan tak terduga.';
                try {
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    }
                } catch (e) {
                    console.error('Failed to parse error response:', e);
                }
                
                unitToast('error', errorMessage);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                    footer: 'Status: ' + xhr.status + ' | Error: ' + error
                });
            }
        });
    }
})();
</script>

