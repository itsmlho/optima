<script>
// ========================================
// UNIT VERIFICATION SCRIPT
// ========================================
(function() {
    const baseUrl = (typeof window.baseUrl !== 'undefined' && window.baseUrl)
        ? String(window.baseUrl).replace(/\/$/, '')
        : '<?= rtrim(base_url(), '/') ?>';

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
        // Auto-expand semua PO group pada saat halaman dimuat
        $('#unit-list .po-group-header').each(function() {
            const poId = $(this).data('po-id');
            $(this).addClass('open');
            $(`.child-po-${poId}`).slideDown(0);
        });

        $('#unit-list').on('click', '.unit-list-item', function(e) {
            e.preventDefault();
            $('.unit-list-item').removeClass('active');
            $(this).addClass('active');
            const unitData = $(this).data('unit');
            $('#unit-detail-view-container').html(createUnitDetailCard(unitData, {}));
            // Load dropdown options after card is created
            setTimeout(() => loadDropdownOptions(), 100);
        });

        // Event listener untuk submit verifikasi inline (tanpa modal)
        $(document).on('click', '#btn-submit-verification-inline', submitUnitVerificationInline);
        
        // Event listener untuk dropdown lokasi
        $(document).on('change', '#lokasi_unit_inline', checkAllUnitVerifiedInline);
        
        // Event listener untuk checkbox "Sesuai"
        $(document).on('change', '#unitVerificationFormInline .verify-checkbox-sesuai', function() {
            const row = $(this).closest('tr');
            const verifyField = row.find('.verify-field');
            const dbField = row.find('td:nth-child(2) input, td:nth-child(2) textarea');
            const dbValue = dbField.val() || '';
            const tidakSesuaiCheckbox = row.find('.verify-checkbox-tidak-sesuai');
            const poRefEmpty = row.attr('data-po-reference-empty') === '1';
            
            if ($(this).is(':checked')) {
                // Uncheck "Tidak Sesuai" jika "Sesuai" dicentang (mutually exclusive)
                tidakSesuaiCheckbox.prop('checked', false);
                
                // Referensi PO terstruktur kosong: jangan salin kosong / jangan kunci sebelum user mengisi (melengkapi data gudang)
                if (poRefEmpty) {
                    let filled = false;
                    if (verifyField.is('select')) {
                        const v = verifyField.val();
                        filled = v !== '' && v !== null && v !== undefined;
                    } else {
                        const t = String(verifyField.val() || '').trim();
                        filled = t !== '' && t !== '-';
                    }
                    if (!filled) {
                        $(this).prop('checked', false);
                        unitToast('warning', 'Isi kolom Real Lapangan dulu (acuan Spesifikasi vendor + cek fisik), lalu centang Sesuai untuk mengunci baris ini.', 'Lengkapi data');
                        return;
                    }
                    if (verifyField.is('select')) {
                        verifyField.prop('disabled', true);
                    } else {
                        verifyField.prop('readonly', true);
                    }
                    verifyField.css({
                        'background-color': '#f0fdf4',
                        'border-color': '#10b981'
                    });
                    row.css('background-color', '#f0fdf4');
                    checkAllUnitVerifiedInline();
                    return;
                }
                
                if (verifyField.is('select')) {
                    const dbId = row.attr('data-db-id');
                    if (dbId !== undefined && dbId !== '') {
                        verifyField.val(dbId);
                    } else {
                        const $opt = verifyField.find('option').filter(function() {
                            return $(this).text().trim() === String(dbValue).trim();
                        }).first();
                        if ($opt.length) {
                            verifyField.val($opt.val());
                        } else {
                            verifyField.val(dbValue);
                        }
                    }
                    verifyField.prop('disabled', true);
                } else {
                    verifyField.val(dbValue).prop('readonly', true);
                }
                verifyField.css({
                    'background-color': '#f0fdf4',
                    'border-color': '#10b981'
                });
                row.css('background-color', '#f0fdf4');
            } else {
                verifyField.prop('readonly', false);
                if (verifyField.is('select')) {
                    verifyField.prop('disabled', false);
                }
                verifyField.css({
                    'background-color': '#fff',
                    'border-color': '#333'
                });
                row.css('background-color', '');
            }
            checkAllUnitVerifiedInline();
        });
        
        // Event listener untuk checkbox "Tidak Sesuai"
        $(document).on('change', '#unitVerificationFormInline .verify-checkbox-tidak-sesuai', function() {
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
                const hasTidakSesuai = $('#unitVerificationFormInline .verify-checkbox-tidak-sesuai:checked').length > 0;
                if (!hasTidakSesuai) {
                    $('#alasan-reject-container').slideUp(300);
                    $('#alasan_reject_inline').prop('required', false).val('');
                }
            }
            checkAllUnitVerifiedInline();
        });

        $(document).on('input change', '#unitVerificationFormInline .verify-field', function() {
            const row = $(this).closest('tr');
            const verifyField = $(this);
            const poRefEmpty = row.attr('data-po-reference-empty') === '1';
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
            const dbIdRow = row.attr('data-db-id');

            if (poRefEmpty) {
                sesuaiCheckbox.prop('checked', false);
                if (!tidakSesuaiCheckbox.is(':checked')) {
                    if (verifyField.is('select')) {
                        verifyField.prop('disabled', false);
                    } else {
                        verifyField.prop('readonly', false);
                    }
                    verifyField.css({
                        'background-color': '#fff',
                        'border-color': '#333'
                    }).removeAttr('required');
                    row.css('background-color', '');
                }
                const hasTidakSesuai = $('#unitVerificationFormInline .verify-checkbox-tidak-sesuai:checked').length > 0;
                if (!hasTidakSesuai) {
                    $('#alasan-reject-container').slideUp(300);
                    $('#alasan_reject_inline').prop('required', false).val('');
                }
                checkAllUnitVerifiedInline();
                return;
            }

            let differs;
            if (verifyField.is('select')) {
                if (dbIdRow !== undefined && dbIdRow !== '') {
                    differs = String(verifyField.val() || '') !== String(dbIdRow);
                } else {
                    differs = verifyField.find('option:selected').text().trim() !== String(dbValue).trim();
                }
            } else {
                differs = realValue.trim() !== String(dbValue).trim();
            }
            
            if (differs) {
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
                tidakSesuaiCheckbox.prop('checked', false);
                if (!sesuaiCheckbox.is(':checked')) {
                    if (verifyField.is('select')) {
                        verifyField.prop('disabled', false);
                    } else {
                        verifyField.prop('readonly', false);
                    }
                    verifyField.css({
                        'background-color': '#fff',
                        'border-color': '#333'
                    }).removeAttr('required');
                    row.css('background-color', '');
                }
                const hasTidakSesuai = $('#unitVerificationFormInline .verify-checkbox-tidak-sesuai:checked').length > 0;
                if (!hasTidakSesuai) {
                    $('#alasan-reject-container').slideUp(300);
                    $('#alasan_reject_inline').prop('required', false).val('');
                }
            }

            checkAllUnitVerifiedInline();
        });

        // Helper: init atau refresh Select2 pada satu dropdown setelah opsi terisi
        function initSelect2OnDropdown($dropdown) {
            if (!$.fn.select2) return;
            // Ambil modal terdekat sebagai dropdownParent agar z-index aman
            const $modal = $dropdown.closest('.modal');
            const s2opts = {
                width: '100%',
                allowClear: true,
                placeholder: $dropdown.find('option:first').text() || 'Pilih...',
                language: { noResults: function() { return 'Tidak ada pilihan'; } }
            };
            if ($modal.length) s2opts.dropdownParent = $modal;

            if ($dropdown.hasClass('select2-hidden-accessible')) {
                $dropdown.select2('destroy');
            }
            $dropdown.select2(s2opts);

            // Select2 meng-trigger 'change' standar — semua handler existing tetap jalan
            // Tapi kita perlu bridge agar input/change handler di form juga terpanggil
            $dropdown.off('select2:select.whVerify select2:unselect.whVerify')
                .on('select2:select.whVerify select2:unselect.whVerify', function() {
                    $(this).trigger('change');
                });
        }

        // Load dropdown options for all dropdown fields
        function loadDropdownOptions() {
            console.log('🔄 Loading dropdown options...');
            $('#unitVerificationFormInline .verify-dropdown').each(function() {
                const $dropdown = $(this);
                const dropdownType = $dropdown.data('dropdown-type');
                let currentValue = $dropdown.find('option:selected').val();
                const cascadingParent = $dropdown.data('cascading-parent');
                const parentValue = $dropdown.data('parent-value');
                
                // Treat "-" as empty value
                if (currentValue === '-' || currentValue === 'Loading...') {
                    currentValue = '';
                }
                
                if (!dropdownType || $dropdown.data('loaded')) {
                    // Dropdown sudah punya opsi — pastikan Select2 sudah init
                    if (!$dropdown.hasClass('select2-hidden-accessible')) {
                        initSelect2OnDropdown($dropdown);
                    }
                    return;
                }
                
                // Build request data
                const requestData = { field: dropdownType };
                
                // Add parent filter for cascading dropdowns
                if (cascadingParent && parentValue && parentValue !== '-') {
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
                                const optionValue = (option.id !== undefined && option.id !== null) ? String(option.id) : String(optionText);
                                const isSelected = currentValue && (String(option.id) === String(currentValue) || optionValue === String(currentValue));
                                
                                $dropdown.append(
                                    $('<option>', {
                                        value: optionValue,
                                        text: optionText,
                                        selected: isSelected,
                                    })
                                );
                            });
                            
                            // Jika value terpilih bukan ID (mis. teks model dari PO) → coba cocokkan berdasarkan teks
                            const isNumericId = /^-?\d+$/.test(String(currentValue).trim());
                            const allowOrphanOption = dropdownType !== 'fork_master' || isNumericId;
                            const hasMatchingOption = $dropdown.find('option').filter(function() {
                                return String($(this).val()) === String(currentValue);
                            }).length > 0;

                            if (allowOrphanOption && currentValue && currentValue !== '' && currentValue !== '-' && !hasMatchingOption) {
                                if (!isNumericId) {
                                    // coba cocokkan berdasarkan teks (terjadi ketika PO tidak punya model_unit_id)
                                    const textMatch = $dropdown.find('option').filter(function() {
                                        return $(this).text().trim().toLowerCase() === String(currentValue).trim().toLowerCase();
                                    });
                                    if (textMatch.length > 0) {
                                        textMatch.prop('selected', true);
                                        console.log('✅ Text-matched dropdown:', currentValue, '→ ID:', textMatch.val());
                                    } else {
                                        $dropdown.prepend($('<option>', { value: '', text: currentValue + ' (pilih ulang)', selected: true }));
                                        console.warn('⚠️ No text match for:', currentValue, '— user must re-select');
                                    }
                                } else {
                                    $dropdown.prepend($('<option>', { value: currentValue, text: currentValue, selected: true }));
                                }
                            }
                            
                            $dropdown.data('loaded', true);

                            // Init Select2 setelah opsi terisi
                            initSelect2OnDropdown($dropdown);
                            console.log('✅ Select2 init for', dropdownType, 'with', $dropdown.find('option').length - 1, 'options');
                        } else {
                            console.error('❌ Invalid response:', response);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Error loading dropdown options:', error, xhr.responseText);
                    }
                });
            });
        }
        
        $(document).on('change', '#unitVerificationFormInline .verify-dropdown', function() {
            const $dropdown = $(this);
            const fieldName = $dropdown.data('field-name');
            const row = $(this).closest('tr');
            const verifyField = $(this);
            const poRefEmpty = row.attr('data-po-reference-empty') === '1';
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

            if (poRefEmpty) {
                sesuaiCheckbox.prop('checked', false);
                if (!tidakSesuaiCheckbox.is(':checked')) {
                    verifyField.prop('disabled', false).css({
                        'background-color': '#fff',
                        'border-color': '#333'
                    });
                    row.css('background-color', '');
                }
                const hasTidakSesuaiEarly = $('#unitVerificationFormInline .verify-checkbox-tidak-sesuai:checked').length > 0;
                if (!hasTidakSesuaiEarly) {
                    $('#alasan-reject-container').slideUp(300);
                    $('#alasan_reject_inline').prop('required', false).val('');
                }
            }
            
            // Handle cascading dropdowns
            if (fieldName === 'merk') {
                const selectedMerk = verifyField.find('option:selected').text().trim() || realValue;
                const $modelDropdown = $('#verify_model');
                if ($modelDropdown.length) {
                    $modelDropdown.data('loaded', false);
                    $modelDropdown.data('parent-value', selectedMerk);
                    $modelDropdown.find('option:not(:first)').remove();
                    $modelDropdown.append('<option value="">Loading...</option>');
                    setTimeout(() => loadDropdownOptions(), 100);
                }
            } else if (fieldName === 'engine_type') {
                const selectedEngineType = verifyField.find('option:selected').text().trim() || realValue;
                const $modelMesinDropdown = $('#verify_model_mesin');
                if ($modelMesinDropdown.length) {
                    $modelMesinDropdown.data('loaded', false);
                    $modelMesinDropdown.data('parent-value', selectedEngineType);
                    $modelMesinDropdown.find('option:not(:first)').remove();
                    $modelMesinDropdown.append('<option value="">Loading...</option>');
                    setTimeout(() => loadDropdownOptions(), 100);
                }
            }

            if (poRefEmpty) {
                checkAllUnitVerifiedInline();
                return;
            }
            
            const dbIdRow = row.attr('data-db-id');
            let matchesDb;
            if (verifyField.is('select')) {
                if (dbIdRow !== undefined && dbIdRow !== '') {
                    matchesDb = String(verifyField.val() || '') === String(dbIdRow);
                } else {
                    const selText = verifyField.find('option:selected').text().trim();
                    matchesDb = selText === String(dbValue).trim();
                }
            } else {
                matchesDb = String(verifyField.val() || '').trim() === String(dbValue).trim();
            }
            
            if (!matchesDb) {
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
                tidakSesuaiCheckbox.prop('checked', false);
                if (!sesuaiCheckbox.is(':checked')) {
                    verifyField.prop('disabled', false).css({
                        'background-color': '#fff',
                        'border-color': '#333'
                    }).removeAttr('required');
                    row.css('background-color', '');
                }
                const hasTidakSesuai = $('#unitVerificationFormInline .verify-checkbox-tidak-sesuai:checked').length > 0;
                if (!hasTidakSesuai) {
                    $('#alasan-reject-container').slideUp(300);
                    $('#alasan_reject_inline').prop('required', false).val('');
                }
            }

            checkAllUnitVerifiedInline();
        });

        window.loadUnitVerificationDropdowns = loadDropdownOptions;
    });

    window.toggleUnitDropdown = function(element) {
        const poId = $(element).data('po-id');
        $(element).toggleClass('open');
        $(`.child-po-${poId}`).slideToggle(200);
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
                    <tr data-spec-index="${index}" ${spec.fieldName ? `data-field="${spec.fieldName}"` : ''}>
                        <td style="font-weight: 500; background-color: #fafafa; padding: 8px;">${spec.label}${spec.required ? ' <span class="text-danger">*</span>' : ''}</td>
                        <td style="font-family: monospace; background-color: #fff; padding: 8px;"><input type="text" class="form-control form-control-sm" value="${spec.value}" readonly style="border: none; background: transparent; padding: 0;"></td>
                        <td style="background-color: #fff; padding: 8px;">
                            ${spec.required && spec.fieldName ? `
                                <input type="text" class="form-control form-control-sm verify-field" 
                                       id="${fieldId}" 
                                       name="${fieldId}" 
                                       data-field="${spec.fieldName}"
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
                                   class="cursor-pointer">
                        </td>
                    </tr>
                `;
            } else if (spec.required) {
                // Always show required SN fields even if empty
                const fieldId = spec.fieldName || `verify_${spec.label.toLowerCase().replace(/\s+/g, '_')}`;
                tableRows += `
                    <tr data-spec-index="${index}" ${spec.fieldName ? `data-field="${spec.fieldName}"` : ''}>
                        <td style="font-weight: 500; background-color: #fafafa; padding: 8px;">${spec.label} <span class="text-danger">*</span></td>
                        <td style="font-family: monospace; background-color: #fff; padding: 8px;"><input type="text" class="form-control form-control-sm" value="${spec.value || 'Belum ada SN'}" readonly style="border: none; background: transparent; padding: 0;"></td>
                        <td style="background-color: #fff; padding: 8px;">
                            <input type="text" class="form-control form-control-sm verify-field" 
                                   id="${fieldId}" 
                                   name="${fieldId}" 
                                   data-field="${spec.fieldName}"
                                   placeholder="Masukkan ${spec.label.toLowerCase()} real"
                                   required
                                   style="border: 1px solid #333; border-radius: 4px; padding: 4px 8px;">
                        </td>
                        <td class="text-center" style="background-color: #fafafa; padding: 8px;">
                            <input type="checkbox" class="form-check-input verify-checkbox" 
                                   data-spec-index="${index}"
                                   class="cursor-pointer">
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
        
        $('#unitVerificationFormInline tbody tr.verification-data-row:not(.wh-no-verify-check)').filter(function() {
            const $r = $(this);
            return $r.is(':visible') && $r.closest('table').is(':visible');
        }).each(function() {
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
            
            // Jika "Sesuai" dicentang, Real Lapangan harus terisi (dari salinan PO atau dari pelengkapan gudang)
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
        // 3. Jika "Sesuai", field "Real Lapangan" terisi (termasuk setelah melengkapi PO kosong)
        // 4. Lokasi Unit terpilih
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
            OptimaNotify.warning('Pilih lokasi unit terlebih dahulu.', 'Lokasi Wajib');
            return;
        }
        
        let finalStatus = 'Sesuai';
        let fullNotes = [];
        const snData = {};
        const discrepancies = []; // Array untuk menyimpan discrepancy data yang terstruktur
        
        // Collect data from verification table (hanya baris tampil — konsisten dengan package gating)
        form.find('tbody tr.verification-data-row').filter(':visible').each(function() {
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
            
            const fieldName = row.data('field');
            
            if (row.hasClass('wh-no-verify-check')) {
                if (fieldName && (fieldName === 'sn_unit' || (fieldName.startsWith && fieldName.startsWith('sn_')))) {
                    const snVal = (realInput.length ? realInput.val() : '') || '';
                    const t = snVal && snVal.trim() !== '' && snVal !== 'Belum ada SN' ? snVal.trim() : '';
                    if (t) {
                        snData[fieldName] = t;
                    }
                }
                return;
            }
            
            const sesuaiCheckbox = row.find('.verify-checkbox-sesuai');
            const tidakSesuaiCheckbox = row.find('.verify-checkbox-tidak-sesuai');
            
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
            if (fieldName && (fieldName === 'sn_unit' || (fieldName.startsWith && fieldName.startsWith('sn_')))) {
                const snValue = realValue && realValue.trim() !== '' && realValue !== 'Belum ada SN' ? realValue.trim() : '';
                if (snValue) {
                    snData[fieldName] = snValue;
                }
            }
        });
        
        // Pastikan finalStatus ditentukan dengan benar berdasarkan checkbox yang dicentang
        // Cek ulang apakah ada checkbox "Tidak Sesuai" yang dicentang
        let hasTidakSesuai = false;
        form.find('tbody tr.verification-data-row:not(.wh-no-verify-check)').filter(':visible').each(function() {
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

        // Tanpa teks vendor dan tanpa satu pun referensi PO terstruktur (baris fork tidak dihitung) — blok Sesuai
        if (finalStatus === 'Sesuai') {
            const vendorPresent = form.attr('data-vendor-spec-present') === '1';
            let anyStructuredPoRef = false;
            form.find('tbody tr.verification-data-row:not(.wh-no-verify-check)').filter(':visible').each(function () {
                const $tr = $(this);
                if ($tr.data('field') === 'fork_package') {
                    return;
                }
                if ($tr.attr('data-po-reference-empty') === '0') {
                    anyStructuredPoRef = true;
                    return false;
                }
            });
            if (!vendorPresent && !anyStructuredPoRef) {
                OptimaNotify.warning(
                    'Tidak ada teks Spesifikasi vendor dan tidak ada referensi PO terstruktur di tabel. Minta Purchasing melengkapi spesifikasi vendor atau data PO sebelum verifikasi.',
                    'Acuan wajib'
                );
                return;
            }
        }
        
        // Debug: Log collected SN data
        console.log('Collected SN Data:', snData);
        console.log('Final Status:', finalStatus);
        
        // Validate required SN fields untuk status "Sesuai"
        if (finalStatus === 'Sesuai' && (!snData['sn_unit'] || !snData['sn_mesin'])) {
            console.log('Validation failed - Missing SN:', {sn_unit: snData['sn_unit'], sn_mesin: snData['sn_mesin']});
            OptimaNotify.warning('Serial number Unit dan Mesin wajib diisi untuk status Sesuai.', 'SN Wajib');
            return;
        }
        
        // Jika status "Tidak Sesuai", validasi alasan reject
        if (finalStatus === 'Tidak Sesuai') {
            const alasanReject = $('#alasan_reject_inline').val().trim();
            if (!alasanReject) {
                OptimaNotify.warning(
                    'Mohon isi alasan reject/ketidaksesuaian yang ditemukan. Alasan ini diperlukan untuk ditindaklanjuti oleh tim Purchasing.',
                    'Alasan Reject Wajib'
                );
                $('#alasan_reject_inline').focus();
                return;
            }
            // Gabungkan alasan reject dengan notes
            fullNotes.unshift(`Alasan Reject: ${alasanReject}`);
        }
        
        const poUnitFields = {};
        form.find('tbody tr.verification-data-row').filter(':visible').each(function() {
            const mk = $(this).attr('data-merge-key');
            if (!mk) return;
            const row = $(this);
            const sel = row.find('.verify-dropdown');
            const inp = row.find('.verify-field').not('.verify-dropdown');
            if (sel.length) {
                const v = sel.val();
                if (v !== '' && v !== undefined && v !== null) {
                    poUnitFields[mk] = /^-?\d+$/.test(String(v)) ? parseInt(v, 10) : v;
                }
            } else if (inp.length) {
                const v = inp.val();
                if (v !== undefined && v !== null) poUnitFields[mk] = v;
            }
        });
        const accKeys = [];
        form.find('.verify-acc:checked').each(function() {
            accKeys.push($(this).data('acc-key'));
        });
        if (accKeys.length) poUnitFields.unit_accessories = JSON.stringify(accKeys);
        
        // Tampilkan modal konfirmasi sebelum kirim ke database
        showVerificationConfirmation(idUnit, poId, finalStatus, snData, fullNotes, lokasiUnit, discrepancies, poUnitFields);
    }

    function showVerificationConfirmation(idUnit, poId, finalStatus, snData, fullNotes, lokasiUnit, discrepancies = [], poUnitFields = {}) {
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
            if (snData['sn_charger']) summaryHTML += '<li>SN Charger: <code>' + snData['sn_charger'] + '</code></li>';
            if (snData['sn_attachment']) summaryHTML += '<li>SN Attachment: <code>' + snData['sn_attachment'] + '</code></li>';
            if (snData['sn_fork']) summaryHTML += '<li>SN Fork: <code>' + snData['sn_fork'] + '</code></li>';
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
        OptimaConfirm.generic({
            title: 'Konfirmasi Verifikasi Unit',
            html: summaryHTML,
            icon: finalStatus === 'Sesuai' ? 'question' : 'warning',
            confirmText: finalStatus === 'Sesuai' ? 'Ya, Verifikasi Sesuai' : 'Ya, Konfirmasi Tidak Sesuai',
            cancelText: window.lang('cancel'),
            confirmButtonColor: finalStatus === 'Sesuai' ? '#10b981' : '#ef4444',
            onConfirm: function() {
                updateUnitStatusVerifikasi(idUnit, poId, finalStatus, snData, fullNotes.join('; '), lokasiUnit, discrepancies, poUnitFields);
            }
        });
    }

    /** Selaras form PO: DIESEL/GASOLINE tidak memakai baterai & charger listrik. */
    function whDepartemenIsNonElectric(name) {
        const u = String(name || '').trim().toUpperCase();
        return u === 'DIESEL' || u === 'GASOLINE';
    }

    /**
     * Baca package_flags dari PO; jika kosong (data lama / belum dicentang di Purchasing),
     * anggap paket lengkap untuk form verifikasi gudang agar fork + master + aksesoris tetap muncul.
     */
    function whEffectivePackageFlags(d) {
        let flags = [];
        try {
            const p = d.package_flags;
            if (p == null || p === '') {
                flags = [];
            } else if (typeof p === 'string') {
                const j = JSON.parse(p);
                flags = Array.isArray(j) ? j : [];
            } else {
                flags = Array.isArray(p) ? p : [];
            }
        } catch (e) {
            flags = [];
        }
        if (flags.length > 0) {
            return flags;
        }
        const base = ['fork_standard', 'attachment', 'accessories'];
        if (!whDepartemenIsNonElectric(d.nama_departemen)) {
            base.splice(1, 0, 'battery', 'charger');
        }
        return base;
    }

    function createUnitDetailCard(data, options) {
        options = options || {};
        const h = (str) => str ? String(str).replace(/</g, '&lt;') : "-";
        
        function whParsePkgFlags(d) {
            return whEffectivePackageFlags(d);
        }
        function whParseUnitAcc(d) {
            try {
                const p = d.unit_accessories;
                if (p == null || p === '') return [];
                if (typeof p === 'string') {
                    const j = JSON.parse(p);
                    return Array.isArray(j) ? j : [];
                }
                return Array.isArray(p) ? p : [];
            } catch (e) { return []; }
        }
        const pkgFlags = whParsePkgFlags(data);
        const isNonElectricDept = whDepartemenIsNonElectric(data.nama_departemen);
        const unitAccSelected = whParseUnitAcc(data);
        const accOpts = [
            ['main_light','Main / signal lights'],['blue_spot','Blue spot'],['red_line','Red line'],['work_light','Work light'],
            ['rotary_lamp','Rotary lamp'],['back_buzzer','Back buzzer'],['camera_ai','Camera AI'],['camera','Camera'],
            ['sensor_parking','Sensor parking'],['speed_limiter','Speed limiter'],['laser_fork','Laser fork'],
            ['voice_announcer','Voice announcer'],['horn_speaker','Horn speaker'],['horn_klason','Horn klason'],
            ['bio_metric','Bio metric'],['acrylic','Acrylic'],['first_aid_kit','First aid kit'],['safety_belt','Safety belt'],
            ['safety_belt_interlock','Safety belt interlock'],['spark_arrestor','Spark arrestor'],['mirror','Mirror']
        ];
        
        const specMain = [];
        const brandValue = data.brand_name_po || data.merk_unit || data.brand_from_model_table || '-';
        
        if (data.nama_departemen) {
            specMain.push({label: 'Departemen', value: h(data.nama_departemen), fieldName: 'departemen', required: false, dropdownType: 'departemen', mergeKey: '', dbId: data.id_departemen, selectValue: data.id_departemen});
        }
        specMain.push({label: 'Jenis Unit', value: h(data.jenis) || '-', fieldName: 'jenis_unit', required: false, dropdownType: 'tipe_unit', mergeKey: 'tipe_unit_id', dbId: data.tipe_unit_id, selectValue: data.tipe_unit_id});
        specMain.push({label: 'Brand', value: h(brandValue), fieldName: 'merk', required: false, dropdownType: 'merk_unit', cascadingParent: null, mergeKey: 'merk_unit', dbId: '', selectValue: ''});
        specMain.push({label: 'Model', value: h(data.model_unit) || '-', fieldName: 'model', required: false, dropdownType: 'model_unit', cascadingParent: 'merk', parentValue: h(brandValue), mergeKey: 'model_unit_id', dbId: data.model_unit_id, selectValue: data.model_unit_id});
        specMain.push({label: 'Tahun', value: h(data.tahun_po) || '-', fieldName: 'tahun', required: false, dropdownType: null, mergeKey: 'tahun_po', dbId: '', selectValue: data.tahun_po});
        specMain.push({label: 'Kapasitas', value: h(data.kapasitas_unit) || '-', fieldName: 'kapasitas', required: false, dropdownType: 'kapasitas', mergeKey: 'kapasitas_id', dbId: data.kapasitas_id, selectValue: data.kapasitas_id});
        const mastLabel = data.tipe_mast ? (h(data.tipe_mast) + (data.tinggi_mast ? ' (' + h(data.tinggi_mast) + ')' : '')) : '-';
        specMain.push({label: 'Mast Type', value: mastLabel, fieldName: 'mast_type', required: false, dropdownType: 'tipe_mast', mergeKey: 'mast_id', dbId: data.mast_id, selectValue: data.mast_id});
        specMain.push({label: 'Engine Type', value: h(data.merk_mesin) || '-', fieldName: 'engine_type', required: false, dropdownType: 'merk_mesin', cascadingParent: null, mergeKey: '', dbId: '', selectValue: ''});
        specMain.push({label: 'Model Mesin', value: h(data.model_mesin) || '-', fieldName: 'model_mesin', required: false, dropdownType: 'model_mesin', cascadingParent: 'engine_type', parentValue: h(data.merk_mesin) || '-', mergeKey: 'mesin_id', dbId: data.mesin_id, selectValue: data.mesin_id});
        specMain.push({label: 'Tire Type', value: h(data.tipe_ban) || '-', fieldName: 'tire_type', required: false, dropdownType: 'tipe_ban', mergeKey: 'ban_id', dbId: data.ban_id, selectValue: data.ban_id});
        specMain.push({label: 'Wheel Type', value: h(data.tipe_roda) || '-', fieldName: 'wheel_type', required: false, dropdownType: 'jenis_roda', mergeKey: 'roda_id', dbId: data.roda_id, selectValue: data.roda_id});
        specMain.push({label: 'Valve', value: h(data.jumlah_valve) || '-', fieldName: 'valve', required: false, dropdownType: 'valve', mergeKey: 'valve_id', dbId: data.valve_id, selectValue: data.valve_id});
        specMain.push({label: 'Keterangan / catatan PO', value: h(data.keterangan) || '-', fieldName: 'keterangan', required: false, isTextarea: true, dropdownType: null, mergeKey: 'keterangan', dbId: '', selectValue: ''});
        
        const embed = options.embedAccessoryRows || {};
        const embedBat = !!embed.battery;
        const embedChg = !!embed.charger;
        const embedAtt = !!embed.attachment;

        const reqBat = !isNonElectricDept && pkgFlags.includes('battery');
        const reqChg = !isNonElectricDept && pkgFlags.includes('charger');
        const reqAtt = pkgFlags.includes('attachment');

        specMain.push({label: 'Serial Number Unit', value: h(data.serial_number_po) || 'Belum ada SN', fieldName: 'sn_unit', required: true});
        specMain.push({label: 'SN Mast', value: h(data.sn_mast_po) || 'Belum ada SN', fieldName: 'sn_mast', required: true});
        specMain.push({label: 'SN Mesin', value: h(data.sn_mesin_po) || 'Belum ada SN', fieldName: 'sn_mesin', required: true});

        /** Urutan selaras Service → unit_verification: attachment (+SN), baterai (+SN), charger (+SN), fork. */
        const specKomponen = [];
        const whAttLbl = (data.wh_attachment_label && String(data.wh_attachment_label).trim()) ? String(data.wh_attachment_label).trim() : '-';
        const whBatLbl = (data.wh_baterai_label && String(data.wh_baterai_label).trim()) ? String(data.wh_baterai_label).trim() : '-';
        const whChgLbl = (data.wh_charger_label && String(data.wh_charger_label).trim()) ? String(data.wh_charger_label).trim() : '-';
        if (reqAtt && !embedAtt) {
            specKomponen.push({label: 'Attachment (master)', value: whAttLbl === '-' ? '-' : h(whAttLbl), fieldName: 'po_attachment', required: true, noVerifyCheck: false, dropdownType: 'attachment_master', mergeKey: 'attachment_id', dbId: data.attachment_id, selectValue: data.attachment_id});
            specKomponen.push({label: 'SN Attachment', value: h(data.sn_attachment_po) || 'Belum ada SN', fieldName: 'sn_attachment', required: true, noVerifyCheck: false});
        }
        if (reqBat && !embedBat) {
            specKomponen.push({label: 'Tipe baterai (master)', value: whBatLbl === '-' ? '-' : h(whBatLbl), fieldName: 'po_baterai', required: true, noVerifyCheck: false, dropdownType: 'baterai_master', mergeKey: 'baterai_id', dbId: data.baterai_id, selectValue: data.baterai_id});
            specKomponen.push({label: 'SN Baterai', value: h(data.sn_baterai_po) || 'Belum ada SN', fieldName: 'sn_baterai', required: true, noVerifyCheck: false});
        }
        if (reqChg && !embedChg) {
            specKomponen.push({label: 'Tipe charger (master)', value: whChgLbl === '-' ? '-' : h(whChgLbl), fieldName: 'po_charger', required: true, noVerifyCheck: false, dropdownType: 'charger_master', mergeKey: 'charger_id', dbId: data.charger_id, selectValue: data.charger_id});
            specKomponen.push({label: 'SN Charger', value: h(data.sn_charger_po) || 'Belum ada SN', fieldName: 'sn_charger', required: true, noVerifyCheck: false});
        }
        if (pkgFlags.includes('fork_standard')) {
            const fn = data.fork_name_spec || '';
            const fl = data.fork_length_mm != null && data.fork_length_mm !== '' ? String(data.fork_length_mm) : '';
            const forkRefText = fn
                ? (h(fn) + (fl ? ' — ' + fl + ' mm' : ''))
                : '— Bandingkan fork fisik dengan spesifikasi vendor / PI; pilih master fork di kanan —';
            const fid = data.fork_id;
            specKomponen.push({
                label: 'Fork (master)',
                value: forkRefText,
                fieldName: 'fork_package',
                required: true,
                dropdownType: 'fork_master',
                mergeKey: 'fork_id',
                dbId: fid,
                selectValue: fid,
            });
            specKomponen.push({
                label: 'SN Fork',
                value: h(data.sn_fork_po) || 'Belum ada SN',
                fieldName: 'sn_fork',
                required: true,
                noVerifyCheck: false,
            });
        }

        /** True = kolom referensi PO terstruktur kosong; gudang melengkapi dari vendor spec + fisik (bukan ketidaksesuaian otomatis). */
        function whIsPoReferenceEmpty(spec, dbValue) {
            if (spec.noVerifyCheck) {
                return false;
            }
            const v = (dbValue === undefined || dbValue === null) ? '' : String(dbValue).trim();
            const snField = spec.fieldName && (spec.fieldName.startsWith('sn_') || spec.fieldName === 'sn_unit');
            if (snField) {
                return v === '' || v === 'Belum ada SN';
            }
            if (spec.isTextarea) {
                return v === '' || v === '-';
            }
            const idFromDb = spec.dbId !== undefined && spec.dbId !== null && spec.dbId !== '' ? parseInt(String(spec.dbId), 10) : NaN;
            const idFromSel = spec.selectValue !== undefined && spec.selectValue !== null && spec.selectValue !== '' ? parseInt(String(spec.selectValue), 10) : NaN;
            const hasMasterId = (!isNaN(idFromDb) && idFromDb > 0) || (!isNaN(idFromSel) && idFromSel > 0);
            /* Fork: teks PI/vendor tidak bisa disamakan otomatis dengan label dropdown API → perlakukan seperti PO terstruktur kosong sampai ada fork_id. */
            if (spec.fieldName === 'fork_package' || spec.dropdownType === 'fork_master') {
                return !hasMasterId;
            }
            if (spec.dropdownType) {
                if (spec.fieldName === 'merk') {
                    return v === '' || v === '-';
                }
                if (hasMasterId) {
                    return false;
                }
                return v === '' || v === '-';
            }
            return v === '' || v === '-';
        }
        
        // Bangun baris tabel verifikasi (bisa dipakai untuk tabel utama + kartu komponen)
        let whVerifyRowIndex = 0;
        function buildWhVerificationTbodyRows(specs) {
            let html = '';
            specs.forEach((spec) => {
            const index = whVerifyRowIndex++;
            const fieldId = `verify_${spec.fieldName || spec.label.toLowerCase().replace(/\s+/g, '_')}`;
            const checkId = `check_${spec.fieldName || spec.label.toLowerCase().replace(/\s+/g, '_')}`;
            const dbId = `db_${spec.fieldName || spec.label.toLowerCase().replace(/\s+/g, '_')}`;
            
            // Nilai database - untuk Model dan SN fields, tampilkan '-' atau 'Belum ada SN' 
            // (konsisten dengan print packing list)
            let dbValue = '';
            if (spec.value) {
                if (spec.value === 'Belum ada SN') {
                    dbValue = 'Belum ada SN';  // Keep for SN fields
                } else if (spec.value === '-' && (spec.fieldName === 'model' || spec.fieldName === 'model_mesin')) {
                    dbValue = '-';  // Keep '-' for Model fields (show empty model)
                } else if (spec.value !== '-') {
                    dbValue = spec.value;  // Show actual value
                }
                // else: empty string for other '-' values
            }
            
            // Nilai awal "Real Lapangan" selalu sama dengan "Database" (akan berubah jika user edit)
            const realValue = dbValue;
            const poRefEmpty = whIsPoReferenceEmpty(spec, dbValue);
            const rowPoEmptyAttr = ` data-po-reference-empty="${poRefEmpty ? '1' : '0'}"`;
            
            const rowMerge = spec.mergeKey ? ` data-merge-key="${spec.mergeKey}"` : '';
            const rowDbId = (spec.dbId !== undefined && spec.dbId !== null && spec.dbId !== '') ? ` data-db-id="${spec.dbId}"` : '';
            const rowOptionalClass = spec.noVerifyCheck ? ' wh-no-verify-check' : '';
            const optionalVerifyCells = spec.noVerifyCheck
                ? `<td colspan="2" class="text-center text-muted small" style="background-color: #fafafa; padding: 8px; vertical-align: middle;">Opsional — tidak wajib centang Sesuai / Tidak sesuai</td>`
                : `<td class="text-center" style="background-color: #fafafa; padding: 8px; vertical-align: middle;">
                            <input type="checkbox" class="form-check-input verify-checkbox-sesuai cursor-pointer" 
                                   id="${checkId}_sesuai" 
                                   data-field="${spec.fieldName}"
                                   data-row-index="${index}">
                        </td>
                        <td class="text-center" style="background-color: #fafafa; padding: 8px; vertical-align: middle;">
                            <input type="checkbox" class="form-check-input verify-checkbox-tidak-sesuai cursor-pointer" 
                                   id="${checkId}_tidak_sesuai" 
                                   data-field="${spec.fieldName}"
                                   data-row-index="${index}">
                        </td>`;
            
            if (spec.isTextarea) {
                html += `
                    <tr class="verification-data-row${rowOptionalClass}" data-field="${spec.fieldName}"${rowMerge}${rowDbId}${rowPoEmptyAttr}>
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
                        ${optionalVerifyCells}
                    </tr>
                `;
            } else {
                const isSNField = spec.fieldName && (spec.fieldName.startsWith('sn_') || spec.fieldName === 'sn_unit');
                const useDropdown = spec.dropdownType && !isSNField;
                
                let realFieldInput = '';
                if (useDropdown) {
                    const isCascading = spec.cascadingParent && spec.parentValue && spec.parentValue !== '-';
                    const cascadingAttr = isCascading ? `data-cascading-parent="${spec.cascadingParent}" data-parent-value="${spec.parentValue}"` : '';
                    
                    const hasValidValue = realValue && realValue !== '-' && realValue !== '';
                    const selVal = (spec.selectValue !== undefined && spec.selectValue !== null && spec.selectValue !== '') ? String(spec.selectValue) : '';
                    const seedFromId = selVal && dbValue && dbValue !== '-' ? `<option value="${selVal}" selected>${dbValue}</option>` : '';
                    /* fork_master: jangan pakai teks referensi PO sebagai value <option> (bukan ID) — menghindari merge fork_id=0 / opsi palsu */
                    const isForkMaster = spec.dropdownType === 'fork_master';
                    const textSeedOk = hasValidValue && !isForkMaster;
                    
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
                            ${seedFromId || (textSeedOk ? `<option value="${realValue}" selected>${realValue}</option>` : '')}
                        </select>
                    `;
                } else {
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
                
                html += `
                    <tr class="verification-data-row${rowOptionalClass}" data-field="${spec.fieldName}"${rowMerge}${rowDbId}${rowPoEmptyAttr}>
                        <td style="font-weight: 500; background-color: #fafafa; padding: 8px; vertical-align: middle;">${spec.label}${spec.required ? ' <span class="text-danger">*</span>' : ''}</td>
                        <td style="background-color: #fff; padding: 8px;">
                            ${dbFieldInput}
                        </td>
                        <td style="background-color: #fff; padding: 8px;">
                            ${realFieldInput}
                        </td>
                        ${optionalVerifyCells}
                    </tr>
                `;
            }
            });
            return html;
        }

        const tableRows = buildWhVerificationTbodyRows(specMain);
        const komponenTableRows = specKomponen.length ? buildWhVerificationTbodyRows(specKomponen) : '';
        const theadWhVerify = `
                                <tr style="background-color: #f8f9fa;">
                                    <th style="width: 20%; text-align: center; font-weight: bold; border: 1px solid #333; padding: 8px;">Item</th>
                                    <th style="width: 25%; text-align: center; font-weight: bold; border: 1px solid #333; padding: 8px;">Data PO <span class="fw-normal text-muted small d-block">(referensi terstruktur)</span></th>
                                    <th style="width: 25%; text-align: center; font-weight: bold; border: 1px solid #333; padding: 8px;">Real Lapangan</th>
                                    <th style="width: 15%; text-align: center; font-weight: bold; border: 1px solid #333; padding: 8px;">Sesuai</th>
                                    <th style="width: 15%; text-align: center; font-weight: bold; border: 1px solid #333; padding: 8px;">Tidak Sesuai</th>
                                </tr>`;
        const komponenSectionHtml = specKomponen.length
            ? `<div class="card table-card mb-3 border">
                <div class="card-header py-2 bg-light">
                    <h6 class="mb-0 fw-semibold"><i class="fas fa-puzzle-piece me-2"></i>Verifikasi attachment &amp; komponen</h6>
                    <p class="small text-muted mb-0">Terpisah dari tabel unit (selaras urutan Service: attachment → baterai → charger → fork).</p>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-bordered mb-0 table-verification-wh">
                        <thead>${theadWhVerify}</thead>
                        <tbody>${komponenTableRows}</tbody>
                    </table>
                </div>
            </div>`
            : '';
        
        let accHtml = '';
        if (pkgFlags.includes('accessories')) {
            accHtml = '<div class="mb-3 border rounded p-3 bg-light"><label class="form-label fw-semibold mb-2">Aksesoris terpasang <span class="text-muted fw-normal">(kunci quotation; wajib konfirmasi jika PO mencentang paket aksesoris)</span></label><div class="row row-cols-1 row-cols-md-2 g-1 small">';
            accOpts.forEach(function(pair) {
                const key = pair[0];
                const lbl = pair[1];
                const chk = unitAccSelected.indexOf(key) >= 0 ? ' checked' : '';
                accHtml += '<div class="col"><div class="form-check"><input class="form-check-input verify-acc" type="checkbox" data-acc-key="' + key + '" id="wh_acc_' + key + '"' + chk + '><label class="form-check-label" for="wh_acc_' + key + '">' + lbl + '</label></div></div>';
            });
            accHtml += '</div></div>';
        }
        
        const vendorSpecEsc = data.vendor_spec_text ? String(data.vendor_spec_text).replace(/</g, '&lt;').replace(/>/g, '&gt;') : '';
        const vendorBlock =
            '<div class="card mb-3">' +
            '<div class="card-header py-2"><strong>Spesifikasi vendor — paste utuh dari baris PI</strong></div>' +
            '<div class="card-body py-2 small">' +
            (data.vendor_model_code ? '<div class="mb-2"><strong>Kode model pabrik:</strong> ' + h(data.vendor_model_code) + '</div>' : '') +
            '<pre class="form-control wh-vendor-spec-pre mb-0 bg-body-secondary border font-monospace small">' + (vendorSpecEsc || '— Belum diisi saat PO —') + '</pre>' +
            '</div></div>';

        const pkgLabelMap = { fork_standard: 'Fork standar pabrik', battery: 'Baterai', charger: 'Charger', attachment: 'Attachment', accessories: 'Aksesoris' };
        let pkgBadges = '';
        Object.keys(pkgLabelMap).forEach(function (k) {
            const on = pkgFlags.indexOf(k) >= 0;
            pkgBadges += '<span class="badge ' + (on ? 'badge-soft-success' : 'badge-soft-gray') + ' me-1 mb-1">' + pkgLabelMap[k] + ': ' + (on ? 'Ya' : 'Tidak') + '</span>';
        });
        const packageMirrorHtml =
            '<div class="card mb-3">' +
            '<div class="card-header py-2"><strong>Isi paket (seperti saat pembuatan PO)</strong></div>' +
            '<div class="card-body py-2 small">' + pkgBadges +
            (isNonElectricDept
                ? '<div class="form-text mt-2"><i class="fas fa-info-circle text-primary me-1"></i>Departemen <strong>DIESEL</strong> / <strong>GASOLINE</strong>: baterai &amp; charger dari paket listrik tidak relevan.</div>'
                : '') +
            '<div class="form-text mt-2"><strong>Fork:</strong> ukuran/tipe khusus dari PI harus tercermin pada teks spesifikasi vendor di atas.</div>' +
            '</div></div>';

        const advParts = [];
        const mastDisp = data.tipe_mast ? (h(data.tipe_mast) + (data.tinggi_mast ? ' (' + h(data.tinggi_mast) + ')' : '')) : '';
        if (mastDisp) {
            advParts.push('<div class="col-md-6"><strong>Mast:</strong> ' + mastDisp + '</div>');
        }
        if (data.merk_mesin || data.model_mesin) {
            advParts.push('<div class="col-md-6"><strong>Mesin:</strong> ' + h(data.merk_mesin) + ' ' + h(data.model_mesin) + '</div>');
        }
        if (data.tipe_ban) {
            advParts.push('<div class="col-md-6"><strong>Ban:</strong> ' + h(data.tipe_ban) + '</div>');
        }
        if (data.tipe_roda) {
            advParts.push('<div class="col-md-6"><strong>Roda:</strong> ' + h(data.tipe_roda) + '</div>');
        }
        if (data.jumlah_valve) {
            advParts.push('<div class="col-md-6"><strong>Valve:</strong> ' + h(data.jumlah_valve) + '</div>');
        }
        if (pkgFlags.includes('battery') && !isNonElectricDept && data.baterai_id) {
            advParts.push('<div class="col-md-6"><strong>Baterai (master):</strong> ID ' + h(data.baterai_id) + '</div>');
        }
        if (pkgFlags.includes('charger') && !isNonElectricDept && data.charger_id) {
            advParts.push('<div class="col-md-6"><strong>Charger (master):</strong> ID ' + h(data.charger_id) + '</div>');
        }
        if (pkgFlags.includes('attachment') && data.attachment_id) {
            advParts.push('<div class="col-md-6"><strong>Attachment (master):</strong> ID ' + h(data.attachment_id) + '</div>');
        }
        const advancedCardHtml = advParts.length
            ? '<div class="card mb-3"><div class="card-header py-2"><strong>Komponen lanjutan (diisi saat PO)</strong></div><div class="card-body py-2"><div class="row g-2 small">' + advParts.join('') + '</div></div></div>'
            : '';

        const showNonElectricNote = isNonElectricDept && ((reqBat && !embedBat) || (reqChg && !embedChg));
        const nonElectricNoteHtml = showNonElectricNote
            ? '<div class="alert alert-light border mb-3 small py-2"><i class="fas fa-info-circle text-primary me-1"></i><strong>DIESEL / GASOLINE:</strong> Baterai &amp; charger (master + SN) bersifat <strong>opsional</strong>; tidak wajib centang Sesuai/Tidak sesuai pada baris tersebut — selaras dengan asisten PO.</div>'
            : '';

        const whDel = options.whDeliveryId != null && options.whDeliveryId !== '' ? String(options.whDeliveryId) : '';
        const vendorSpecPresent = !!(data.vendor_spec_text && String(data.vendor_spec_text).trim());

        return `
            <form id="unitVerificationFormInline" data-unit-id="${data.id_po_unit}" data-po-id="${data.po_id}" data-wh-delivery-id="${whDel}" data-vendor-spec-present="${vendorSpecPresent ? '1' : '0'}">
                <div class="card table-card animate__animated animate__fadeIn">
                    <div class="card-header p-3" style="background-color: #f5f5f5; border-bottom: 1px solid #ccc;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold m-0" style="font-size: 1rem; color: #000;">
                                <i class="fas fa-clipboard-check me-2"></i>Verifikasi Data Unit: ${h(data.merk_unit)} ${h(data.model_unit)}
                            </h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        ${vendorBlock}
                        ${packageMirrorHtml}
                        ${advancedCardHtml}
                        ${nonElectricNoteHtml}
                        <div class="alert alert-info mb-3" style="font-size: 0.85rem;">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Cara kerja:</strong> 
                            <ul class="mb-0" style="padding-left: 20px;">
                                <li>Jika kolom <strong>Data PO</strong> sudah terisi dan fisik <strong>sama</strong> → isi/cek kolom <strong>Real Lapangan</strong>, lalu centang <strong>Sesuai</strong> (mengunci baris).</li>
                                <li>Jika kolom <strong>Data PO kosong</strong> tetapi ada di <strong>Spesifikasi vendor</strong> / di unit → <strong>lengkapi Real Lapangan</strong> dari cek lapangan, lalu centang <strong>Sesuai</strong>. Ini melengkapi data Purchasing, <strong>bukan</strong> reject otomatis.</li>
                                <li>Jika fisik <strong>tidak cocok</strong> dengan dokumen (PO / vendor / unit) → centang <strong>Tidak Sesuai</strong>, pastikan Real terisi, dan isi <strong>Alasan reject</strong>.</li>
                            </ul>
                            <span class="text-danger fw-bold d-block mt-2">⚠️ Setiap baris wajib punya <strong>Sesuai</strong> atau <strong>Tidak Sesuai</strong>. SN dan master data yang wajib harus terisi agar unit bisa masuk inventori lengkap.</span>
                        </div>
                        
                        <p class="small text-muted mb-2"><strong>Tabel 1 — Data unit:</strong> spesifikasi utama + SN unit / mast / mesin.</p>
                        <table class="table table-sm table-bordered mb-3 table-verification-wh">
                            <thead>${theadWhVerify}</thead>
                            <tbody>
                                ${tableRows || '<tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>'}
                            </tbody>
                        </table>
                        ${komponenSectionHtml}
                        ${accHtml}
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

    function updateUnitStatusVerifikasi(idUnit, poId, status, snData = {}, catatan = '', lokasiUnit = '', discrepancies = [], poUnitFields = {}) {
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
                sn_baterai: snData['sn_baterai'] || '',
                sn_charger: snData['sn_charger'] || '',
                sn_attachment: snData['sn_attachment'] || '',
                sn_fork: snData['sn_fork'] || '',
                po_unit_fields: JSON.stringify(poUnitFields)
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
                $('#btn-submit-verification-inline, #btn-submit-unit-verification').prop('disabled', true).text('Menyimpan...');
            },
            success: function(r) {
                window._verifyingUnit = false;
                $('#btn-submit-verification-inline, #btn-submit-unit-verification').prop('disabled', false).text('Submit Verifikasi');
                
                if (r.statusCode == 200) {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        const mEl = document.getElementById('modalUpdateSN');
                        if (mEl) {
                            const mi = bootstrap.Modal.getInstance(mEl);
                            if (mi) mi.hide();
                        }
                        const whM = document.getElementById('verifyWhPoModal');
                        if (whM) {
                            const wmi = bootstrap.Modal.getInstance(whM);
                            if (wmi) wmi.hide();
                        }
                    } else {
                        $('#modalUpdateSN').modal('hide');
                    }
                    const whDel = $('#unitVerificationFormInline').data('whDeliveryId');
                    if (whDel) {
                        if (typeof window.whPoVerifyAfterUnitSuccess === 'function') {
                            window.whPoVerifyAfterUnitSuccess(whDel, idUnit);
                        } else {
                            $(`#bundle-line-d${whDel}-u${idUnit}`).remove();
                            const sisaPl = $(`.wh-lbl-remain-pl[data-delivery-id="${whDel}"]`).first();
                            let n = parseInt(sisaPl.text(), 10) || 0;
                            if (n > 0) {
                                $(`.wh-lbl-remain-pl[data-delivery-id="${whDel}"]`).text(String(n - 1));
                            }
                            $('#wh-verification-detail-container').html(`
                            <div class="card table-card">
                                <div class="card-body text-center p-5">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <h5 class="text-muted">Verifikasi unit berhasil. Pilih paket lain dari daftar.</h5>
                                </div>
                            </div>
                        `);
                        }
                    } else {
                        let sisaElem = $(`#lbl-remain-po-${poId}`);
                        let sisaCount = parseInt(sisaElem.text(), 10) - 1;
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
                    }
                    unitToast('success', r.message || 'Unit berhasil diverifikasi.');
                } else {
                    unitToast('error', r.message || 'Verifikasi gagal.');
                }
            },
            error: function(xhr, status, error) {
                window._verifyingUnit = false;
                $('#btn-submit-verification-inline, #btn-submit-unit-verification').prop('disabled', false).text('Submit Verifikasi');
                
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
            }
        });
    }

    function whBundleParsePkgFlags(d) {
        return whEffectivePackageFlags(d);
    }

    function whBundleDepartemenIsNonElectric(name) {
        const u = String(name || '').trim().toUpperCase();
        return u === 'DIESEL' || u === 'GASOLINE';
    }

    window.createBundleVerificationCard = function(bundle) {
        const unit = bundle.unit;
        const embed = bundle.embed_accessories || {};
        const acc = bundle.accessories || {};
        const idDelivery = bundle.id_delivery;
        const pkgFlags = whBundleParsePkgFlags(unit);
        const isNonElectricDept = whBundleDepartemenIsNonElectric(unit.nama_departemen);
        const showBat = pkgFlags.includes('battery') && !isNonElectricDept;
        const showChg = pkgFlags.includes('charger') && !isNonElectricDept;
        const showAtt = pkgFlags.includes('attachment');

        const unitOpts = {
            embedAccessoryRows: {
                battery: !!embed.battery && showBat,
                charger: !!embed.charger && showChg,
                attachment: !!embed.attachment && showAtt
            },
            whDeliveryId: idDelivery
        };
        let html = createUnitDetailCard(unit, unitOpts);
        const order = ['attachment', 'charger', 'battery'];
        const titles = { attachment: 'Verifikasi Attachment', charger: 'Verifikasi Charger', battery: 'Verifikasi Baterai' };
        let idx = 0;
        order.forEach(function(key) {
            const row = acc[key];
            if (!row || typeof window.createAttachmentDetailCard !== 'function') return;
            if (key === 'battery' && !showBat) return;
            if (key === 'charger' && !showChg) return;
            if (key === 'attachment' && !showAtt) return;
            const sfx = 'd' + idDelivery + '_u' + (unit.id_po_unit || '') + '_' + key + '_' + (idx++);
            html += '<div class="wh-embed-att-block mt-3 border-top pt-3" data-po-attachment-id="' + row.id_po_attachment + '">';
            html += '<h6 class="fw-bold mb-2">' + titles[key] + '</h6>';
            html += window.createAttachmentDetailCard(row, sfx, { whDeliveryId: idDelivery });
            html += '</div>';
        });
        return '<div class="wh-bundle-verify-root" data-delivery-id="' + idDelivery + '">' + html + '</div>';
    };

    window.createOrphanAttachmentVerificationCard = function(payload) {
        const att = payload.orphan_attachment;
        const idDelivery = payload.id_delivery;
        const sfx = 'orphan_d' + idDelivery + '_a' + (att.id_po_attachment || '');
        let inner = '';
        if (typeof window.createAttachmentDetailCard === 'function') {
            inner = window.createAttachmentDetailCard(att, sfx, { whDeliveryId: idDelivery });
        }
        return '<div class="wh-orphan-verify-root" data-delivery-id="' + idDelivery + '">' + inner + '</div>';
    };
})();
</script>

