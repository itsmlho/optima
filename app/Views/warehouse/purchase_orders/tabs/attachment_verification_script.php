<script>
// ========================================
// ATTACHMENT VERIFICATION SCRIPT
// ========================================
(function() {
    const baseUrl = '<?= base_url() ?>';
    
    $(document).ready(function() {
        $('#attachment-item-list').on('click', '.item-child-item', function(e) {
            e.preventDefault();
            $('#attachment-item-list .item-child-item').removeClass('active');
            $(this).addClass('active');
            const itemData = $(this).data('item');
            $('#attachment-detail-view-container').html(createAttachmentDetailCard(itemData));
            // Load dropdown options after card is created
            setTimeout(() => loadDropdownOptions(), 100);
        });

        // Event listener untuk submit verifikasi inline (tanpa modal)
        $(document).on('click', '#btn-submit-attachment-verification-inline', submitAttachmentVerificationInline);
        
        // Event listener untuk dropdown lokasi
        $(document).on('change', '#attachment_lokasi_unit_inline', checkAllAttachmentVerifiedInline);
        
        // Event listener untuk checkbox "Sesuai"
        $(document).on('change', '#attachment-detail-view-container .verify-checkbox-sesuai', function() {
            const row = $(this).closest('tr');
            const verifyField = row.find('.verify-field');
            const dbField = row.find('td:nth-child(2) input, td:nth-child(2) textarea, td:nth-child(2) select');
            let dbValue = '';
            if (dbField.is('select')) {
                dbValue = dbField.find('option:selected').text() || dbField.val() || '';
            } else {
                dbValue = dbField.val() || '';
            }
            const tidakSesuaiCheckbox = row.find('.verify-checkbox-tidak-sesuai');
            
            if ($(this).is(':checked')) {
                tidakSesuaiCheckbox.prop('checked', false);
                
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
                if (verifyField.is('select')) {
                    verifyField.prop('disabled', false);
                } else {
                    verifyField.prop('readonly', false);
                }
                verifyField.css({
                    'background-color': '#fff',
                    'border-color': '#333'
                });
                row.css('background-color', '');
            }
            checkAllAttachmentVerifiedInline();
        });
        
        // Event listener untuk checkbox "Tidak Sesuai"
        $(document).on('change', '#attachment-detail-view-container .verify-checkbox-tidak-sesuai', function() {
            const row = $(this).closest('tr');
            const verifyField = row.find('.verify-field');
            const sesuaiCheckbox = row.find('.verify-checkbox-sesuai');
            const isTextarea = verifyField.is('textarea');
            
            if ($(this).is(':checked')) {
                sesuaiCheckbox.prop('checked', false);
                
                if (verifyField.is('select')) {
                    verifyField.prop('disabled', false);
                } else {
                    verifyField.prop('readonly', false);
                }
                verifyField.css({
                    'background-color': '#fef2f2',
                    'border-color': '#ef4444'
                });
                
                if (!isTextarea) {
                    verifyField.focus();
                }
                
                row.css('background-color', '#fef2f2');
                
                if (!verifyField.val() || verifyField.val().trim() === '') {
                    verifyField.val('').attr('required', true);
                } else {
                    verifyField.attr('required', true);
                }
                
                $('#attachment-alasan-reject-container').slideDown(300);
                $('#attachment_alasan_reject_inline').prop('required', true);
            } else {
                verifyField.prop('readonly', false).css({
                    'background-color': '#fff',
                    'border-color': '#333'
                }).removeAttr('required');
                row.css('background-color', '');
                
                const hasTidakSesuai = $('#attachment-detail-view-container .verify-checkbox-tidak-sesuai:checked').length > 0;
                if (!hasTidakSesuai) {
                    $('#attachment-alasan-reject-container').slideUp(300);
                    $('#attachment_alasan_reject_inline').prop('required', false).val('');
                }
            }
            checkAllAttachmentVerifiedInline();
        });
        
        // Event listener untuk input field "Real Lapangan"
        $(document).on('input', '#attachment-detail-view-container .verify-field', function() {
            const row = $(this).closest('tr');
            const verifyField = $(this);
            const dbField = row.find('td:nth-child(2) input, td:nth-child(2) textarea');
            const dbValue = dbField.val() || '';
            const realValue = verifyField.val() || '';
            const sesuaiCheckbox = row.find('.verify-checkbox-sesuai');
            const tidakSesuaiCheckbox = row.find('.verify-checkbox-tidak-sesuai');
            
            if (realValue.trim() !== dbValue.trim()) {
                sesuaiCheckbox.prop('checked', false);
                tidakSesuaiCheckbox.prop('checked', true);
                
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
                
                $('#attachment-alasan-reject-container').slideDown(300);
                $('#attachment_alasan_reject_inline').prop('required', true);
            } else {
                const hasTidakSesuai = $('#attachment-detail-view-container .verify-checkbox-tidak-sesuai:checked').length > 0;
                if (!hasTidakSesuai) {
                    $('#attachment-alasan-reject-container').slideUp(300);
                    $('#attachment_alasan_reject_inline').prop('required', false).val('');
                }
            }
            
            checkAllAttachmentVerifiedInline();
        });
    });

    window.toggleAttachmentDropdown = function(element) {
        const poId = $(element).data('po-id');
        $(element).toggleClass('open');
        $(`.child-po-${poId}`).each(function() {
            $(this).toggleClass('show');
        });
    };

    // Load dropdown options from database
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
                if (dropdownType === 'merk_attachment' && cascadingParent === 'tipe') {
                    requestData.tipe = parentValue;
                } else if (dropdownType === 'model_attachment' && cascadingParent === 'merk') {
                    requestData.merk = parentValue;
                    // Also need tipe if available
                    const tipeValue = $('#verify_tipe').val();
                    if (tipeValue) requestData.tipe = tipeValue;
                } else if (dropdownType === 'tipe_battery' && cascadingParent === 'merk') {
                    requestData.merk = parentValue;
                } else if (dropdownType === 'jenis_battery' && cascadingParent === 'tipe') {
                    requestData.tipe = parentValue;
                    // Also need merk if available
                    const merkValue = $('#verify_merk').val();
                    if (merkValue) requestData.merk = merkValue;
                } else if (dropdownType === 'tipe_charger' && cascadingParent === 'merk') {
                    requestData.merk = parentValue;
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
    
    // Event listener for dropdown change
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
        const dropdownType = $dropdown.data('dropdown-type');
        
        if (dropdownType === 'tipe_attachment' && fieldName === 'tipe') {
            // Attachment: Tipe changed - update Merk dropdown
            const selectedTipe = realValue;
            const $merkDropdown = $('#verify_merk');
            if ($merkDropdown.length && $merkDropdown.data('dropdown-type') === 'merk_attachment') {
                $merkDropdown.data('loaded', false);
                $merkDropdown.data('parent-value', selectedTipe);
                $merkDropdown.find('option:not(:first)').remove();
                $merkDropdown.append('<option value="">Loading...</option>');
                setTimeout(() => loadDropdownOptions(), 100);
            }
        } else if (dropdownType === 'merk_attachment' && fieldName === 'merk') {
            // Attachment: Merk changed - update Model dropdown
            const selectedMerk = realValue;
            const $modelDropdown = $('#verify_model');
            if ($modelDropdown.length && $modelDropdown.data('dropdown-type') === 'model_attachment') {
                $modelDropdown.data('loaded', false);
                $modelDropdown.data('parent-value', selectedMerk);
                $modelDropdown.find('option:not(:first)').remove();
                $modelDropdown.append('<option value="">Loading...</option>');
                setTimeout(() => loadDropdownOptions(), 100);
            }
        } else if (dropdownType === 'merk_battery' && fieldName === 'merk') {
            // Battery: Merk changed - update Tipe dropdown
            const selectedMerk = realValue;
            const $tipeDropdown = $('#verify_tipe');
            if ($tipeDropdown.length && $tipeDropdown.data('dropdown-type') === 'tipe_battery') {
                $tipeDropdown.data('loaded', false);
                $tipeDropdown.data('parent-value', selectedMerk);
                $tipeDropdown.find('option:not(:first)').remove();
                $tipeDropdown.append('<option value="">Loading...</option>');
                setTimeout(() => loadDropdownOptions(), 100);
            }
        } else if (dropdownType === 'tipe_battery' && fieldName === 'tipe') {
            // Battery: Tipe changed - update Jenis dropdown
            const selectedTipe = realValue;
            const $jenisDropdown = $('#verify_jenis');
            if ($jenisDropdown.length && $jenisDropdown.data('dropdown-type') === 'jenis_battery') {
                $jenisDropdown.data('loaded', false);
                $jenisDropdown.data('parent-value', selectedTipe);
                $jenisDropdown.find('option:not(:first)').remove();
                $jenisDropdown.append('<option value="">Loading...</option>');
                setTimeout(() => loadDropdownOptions(), 100);
            }
        } else if (dropdownType === 'merk_charger' && fieldName === 'merk') {
            // Charger: Merk changed - update Tipe dropdown
            const selectedMerk = realValue;
            const $tipeDropdown = $('#verify_tipe');
            if ($tipeDropdown.length && $tipeDropdown.data('dropdown-type') === 'tipe_charger') {
                $tipeDropdown.data('loaded', false);
                $tipeDropdown.data('parent-value', selectedMerk);
                $tipeDropdown.find('option:not(:first)').remove();
                $tipeDropdown.append('<option value="">Loading...</option>');
                setTimeout(() => loadDropdownOptions(), 100);
            }
        }
        
        // Auto-check "Tidak Sesuai" if value changed
        if (realValue !== dbValue && realValue !== '') {
            tidakSesuaiCheckbox.prop('checked', true);
            sesuaiCheckbox.prop('checked', false);
            verifyField.prop('readonly', false).css({
                'background-color': '#fff',
                'border-color': '#333'
            });
            row.css('background-color', '');
        }
        
        checkAllAttachmentVerifiedInline();
    });

    window.prepareAttachmentVerificationModal = function(element) {
        const data = $(element).data('item');
        
        // Debug: Log the data
        console.log('Attachment verification data:', data);
        
        // Tentukan nama item berdasarkan tipe
        let itemName = '';
        if (data.item_type === 'Attachment') {
            itemName = `${data.merk_attachment || 'Unknown'} | ${data.model_attachment || 'Unknown'} - ${data.tipe_attachment || 'Unknown'}`;
        } else if (data.item_type === 'Battery') {
            itemName = `${data.merk_battery || 'Unknown'} | ${data.tipe_battery || 'Unknown'}`;
        } else if (data.item_type === 'Charger') {
            itemName = `${data.merk_charger || 'Unknown'} | ${data.tipe_charger || 'Unknown'}`;
        }
            
        $('#modalAttachmentVerificationLabel').text(`Inspeksi: ${itemName}`);
        $('#attachment_item_id').val(data.id_po_attachment);
        $('#attachment_po_id').val(data.po_id);

        const container = $('#attachment-verification-components');
        container.empty();

        // Tampilkan dropdown lokasi terlebih dahulu
        container.append(createAttachmentLocationDropdownHTML());

        // Tampilkan hanya komponen yang relevan berdasarkan item_type
        if (data.item_type === 'Attachment') {
            container.append(createAttachmentComponentHTML({ 
                id: 'attachment', 
                label: 'Attachment', 
                sn: true, 
                desc: `${data.merk_attachment || 'Unknown'} | ${data.model_attachment || 'Unknown'} - ${data.tipe_attachment || 'Unknown'}`,
                snValue: data.serial_number || ''
            }));
        } else if (data.item_type === 'Battery') {
            container.append(createAttachmentComponentHTML({ 
                id: 'battery', 
                label: 'Baterai', 
                sn: true, 
                desc: `${data.merk_battery || 'Unknown'} | ${data.tipe_battery || 'Unknown'} | ${data.jenis_battery || 'Unknown'}`,
                snValue: data.serial_number || ''
            }));
        } else if (data.item_type === 'Charger') {
            container.append(createAttachmentComponentHTML({ 
                id: 'charger', 
                label: 'Charger', 
                sn: true, 
                desc: `${data.merk_charger || 'Unknown'} | ${data.tipe_charger || 'Unknown'}`,
                snValue: data.serial_number || ''
            }));
        }
        
        $('#modalAttachmentVerification').modal('show');
    };

    function createAttachmentLocationDropdownHTML() {
        return `
            <div class="location-section mb-4" style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
                <div class="d-flex align-items-center mb-3">
                    <div style="width: 36px; height: 36px; background-color: #f3f4f6; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                        <i class="fas fa-map-marker-alt" style="font-size: 1rem; color: #6b7280;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold" style="color: #111827; font-size: 1rem;">Lokasi Penyimpanan</h6>
                        <small class="text-muted" style="font-size: 0.85rem;">Tentukan lokasi penyimpanan item</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <label for="attachment_lokasi_unit" class="form-label fw-semibold" style="color: #374151; font-size: 0.9rem; margin-bottom: 8px;">Pilih Lokasi Penyimpanan</label>
                        <select class="form-select" id="attachment_lokasi_unit" name="attachment_lokasi_unit" required style="border: 1px solid #d1d5db; border-radius: 6px; padding: 8px 12px; font-size: 0.9rem; background-color: #ffffff;">
                            <option value="">-- Pilih Lokasi Penyimpanan --</option>
                            <option value="POS 1">POS 1</option>
                            <option value="POS 2">POS 2</option>
                            <option value="POS 3">POS 3</option>
                            <option value="POS 4">POS 4</option>
                            <option value="POS 5">POS 5</option>
                        </select>
                        <div class="form-text" style="color: #6b7280; font-size: 0.8rem; margin-top: 4px;">
                            Lokasi ini akan digunakan untuk inventory item setelah verifikasi selesai
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

    function createAttachmentComponentHTML(component) {
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
                    <button class="btn btn-outline-secondary" type="button" onclick="editAttachmentSN('${component.id}')" 
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
                        <button type="button" class="btn btn-success btn-verify" onclick="setAttachmentComponentStatus('${component.id}', 'sesuai', this)"
                                style="padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                            Sesuai
                        </button>
                        <button type="button" class="btn btn-danger btn-verify" onclick="setAttachmentComponentStatus('${component.id}', 'tidak-sesuai', this)"
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

    window.editAttachmentSN = function(componentId) {
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

    window.setAttachmentComponentStatus = function(componentId, status, button) {
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
        
        checkAllAttachmentVerified();
    };

    function checkAllAttachmentVerifiedInline() {
        let allVerified = true;
        let allRowsVerified = true;
        
        $('#attachmentVerificationFormInline tbody tr').each(function() {
            const row = $(this);
            const sesuaiCheckbox = row.find('.verify-checkbox-sesuai');
            const tidakSesuaiCheckbox = row.find('.verify-checkbox-tidak-sesuai');
            const verifyField = row.find('.verify-field');
            const isRequired = row.find('td:first').html().includes('<span class="text-danger">*</span>');
            
            const isSesuaiChecked = sesuaiCheckbox.is(':checked');
            const isTidakSesuaiChecked = tidakSesuaiCheckbox.is(':checked');
            
            if (!isSesuaiChecked && !isTidakSesuaiChecked) {
                allRowsVerified = false;
                allVerified = false;
                return false;
            }
            
            if (isTidakSesuaiChecked) {
                if (!verifyField.val() || verifyField.val().trim() === '') {
                    allVerified = false;
                    return false;
                }
            }
            
            if (isSesuaiChecked) {
                if (!verifyField.val() || verifyField.val().trim() === '') {
                    allVerified = false;
                    return false;
                }
            }
        });
        
        const lokasiSelected = $('#attachment_lokasi_unit_inline').val() !== '';
        $('#btn-submit-attachment-verification-inline').prop('disabled', !allVerified || !lokasiSelected);
    }

    function submitAttachmentVerificationInline() {
        if (window._verifyingAttachment) return;
        
        const form = $('#attachmentVerificationFormInline');
        const idItem = form.data('item-id');
        const poId = form.data('po-id');
        const itemType = form.data('item-type');
        const lokasiUnit = $('#attachment_lokasi_unit_inline').val();
        
        if (!lokasiUnit) {
            Swal.fire({icon:'warning', title:'Lokasi Wajib', text:'Pilih lokasi penyimpanan terlebih dahulu.'});
            return;
        }
        
        let finalStatus = 'Sesuai';
        let fullNotes = [];
        const snData = {};
        const discrepancies = [];
        
        form.find('tbody tr').each(function() {
            const row = $(this);
            const label = row.find('td:first').text().replace(/\s*\*/g, '').trim();
            
            const dbInput = row.find('td:nth-child(2) input, td:nth-child(2) textarea');
            const dbValue = dbInput.val() || '';
            
            const realInput = row.find('.verify-field');
            const realValue = realInput.val() || '';
            
            const sesuaiCheckbox = row.find('.verify-checkbox-sesuai');
            const tidakSesuaiCheckbox = row.find('.verify-checkbox-tidak-sesuai');
            const fieldName = row.data('field');
            
            const isSesuai = sesuaiCheckbox.is(':checked');
            const isTidakSesuai = tidakSesuaiCheckbox.is(':checked');
            
            if (isTidakSesuai) {
                finalStatus = 'Tidak Sesuai';
                const dbVal = dbValue ? String(dbValue).trim() : '';
                const realVal = realValue ? String(realValue).trim() : '';
                
                discrepancies.push({
                    field_name: fieldName || label.toLowerCase().replace(/\s+/g, '_'),
                    database_value: dbVal,
                    real_value: realVal
                });
                
                fullNotes.push(`${label}: Database = "${dbVal}", Real = "${realVal}"`);
            }
            
            // Collect SN data
            if (fieldName && (fieldName.includes('sn_') || fieldName === 'sn_attachment' || fieldName === 'sn_battery' || fieldName === 'sn_charger')) {
                const snValue = (realValue && realValue.trim() !== '') ? realValue : dbValue;
                if (snValue && snValue !== 'Belum ada SN' && snValue.trim() !== '') {
                    snData['serial_number'] = snValue.trim();
                }
            }
        });
        
        let hasTidakSesuai = false;
        form.find('tbody tr').each(function() {
            const row = $(this);
            const tidakSesuaiCheckbox = row.find('.verify-checkbox-tidak-sesuai');
            if (tidakSesuaiCheckbox.is(':checked')) {
                hasTidakSesuai = true;
                return false;
            }
        });
        
        if (hasTidakSesuai) {
            finalStatus = 'Tidak Sesuai';
        } else {
            finalStatus = 'Sesuai';
        }
        
        if (finalStatus === 'Sesuai' && !snData['serial_number']) {
            Swal.fire({icon:'warning', title:'SN Wajib', text:'Serial number wajib diisi untuk status Sesuai.'});
            return;
        }
        
        if (finalStatus === 'Tidak Sesuai') {
            const alasanReject = $('#attachment_alasan_reject_inline').val().trim();
            if (!alasanReject) {
                Swal.fire({
                    icon: 'warning', 
                    title: 'Alasan Reject Wajib', 
                    text: 'Mohon isi alasan reject/ketidaksesuaian yang ditemukan.'
                });
                $('#attachment_alasan_reject_inline').focus();
                return;
            }
            fullNotes.unshift(`Alasan Reject: ${alasanReject}`);
        }
        
        showAttachmentVerificationConfirmation(idItem, poId, finalStatus, snData, fullNotes, lokasiUnit, discrepancies);
    }
    
    function showAttachmentVerificationConfirmation(idItem, poId, finalStatus, snData, fullNotes, lokasiUnit, discrepancies = []) {
        let summaryHTML = '<div style="text-align: left; margin-top: 15px;">';
        summaryHTML += '<div style="margin-bottom: 10px;"><strong>Status Verifikasi:</strong> ';
        if (finalStatus === 'Sesuai') {
            summaryHTML += '<span style="color: #10b981; font-weight: bold;">✓ SESUAI</span>';
        } else {
            summaryHTML += '<span style="color: #ef4444; font-weight: bold;">✗ TIDAK SESUAI</span>';
        }
        summaryHTML += '</div>';
        
        summaryHTML += '<div style="margin-bottom: 10px;"><strong>Lokasi Penyimpanan:</strong> <span style="color: #3b82f6;">' + lokasiUnit + '</span></div>';
        
        if (Object.keys(snData).length > 0) {
            summaryHTML += '<div style="margin-bottom: 10px;"><strong>Serial Number:</strong> <code>' + snData['serial_number'] + '</code></div>';
        }
        
        if (finalStatus === 'Tidak Sesuai' && fullNotes.length > 0) {
            summaryHTML += '<div style="margin-bottom: 10px;"><strong>Catatan Ketidaksesuaian:</strong><div style="background: #fef2f2; padding: 10px; border-radius: 5px; margin-top: 5px; font-size: 0.9em; color: #991b1b;">';
            fullNotes.forEach(note => {
                summaryHTML += '<div style="margin-bottom: 5px;">• ' + note + '</div>';
            });
            summaryHTML += '</div></div>';
        }
        
        summaryHTML += '<div style="margin-top: 15px; padding: 10px; background: #f0f9ff; border-left: 4px solid #3b82f6; border-radius: 4px;">';
        if (finalStatus === 'Sesuai') {
            summaryHTML += '<strong>⚠️ Perhatian:</strong> Setelah dikonfirmasi, item akan dimasukkan ke inventory dan status PO akan diupdate.';
        } else {
            summaryHTML += '<strong>⚠️ Perhatian:</strong> Item yang tidak sesuai akan dikirim ke tim Purchasing untuk ditindaklanjuti.';
        }
        summaryHTML += '</div></div>';
        
        Swal.fire({
            title: 'Konfirmasi Verifikasi Item',
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
            }
        }).then((result) => {
            if (result.isConfirmed) {
                updateAttachmentStatusVerifikasi(idItem, poId, finalStatus, snData, fullNotes.join('; '), lokasiUnit, discrepancies);
            }
        });
    }

    function createAttachmentDetailCard(data) {
        const h = (str) => str ? String(str).replace(/</g, '&lt;') : "-";
        
        // Build specification details array (same format as unit verification)
        const specDetails = [];
        
        if (data.item_type === 'Attachment') {
            if (data.tipe_attachment) specDetails.push({label: 'Tipe', value: h(data.tipe_attachment), fieldName: 'tipe', required: false, dropdownType: 'tipe_attachment', cascadingParent: null});
            if (data.merk_attachment) specDetails.push({label: 'Merk', value: h(data.merk_attachment), fieldName: 'merk', required: false, dropdownType: 'merk_attachment', cascadingParent: 'tipe', parentValue: h(data.tipe_attachment)});
            if (data.model_attachment) specDetails.push({label: 'Model', value: h(data.model_attachment), fieldName: 'model', required: false, dropdownType: 'model_attachment', cascadingParent: 'merk', parentValue: h(data.merk_attachment)});
            if (data.serial_number) specDetails.push({label: 'Serial Number', value: h(data.serial_number) || 'Belum ada SN', fieldName: 'sn_attachment', required: true});
        } else if (data.item_type === 'Battery') {
            if (data.merk_battery) specDetails.push({label: 'Merk', value: h(data.merk_battery), fieldName: 'merk', required: false, dropdownType: 'merk_battery', cascadingParent: null});
            if (data.tipe_battery) specDetails.push({label: 'Tipe', value: h(data.tipe_battery), fieldName: 'tipe', required: false, dropdownType: 'tipe_battery', cascadingParent: 'merk', parentValue: h(data.merk_battery)});
            if (data.jenis_battery) specDetails.push({label: 'Jenis', value: h(data.jenis_battery), fieldName: 'jenis', required: false, dropdownType: 'jenis_battery', cascadingParent: 'tipe', parentValue: h(data.tipe_battery)});
            if (data.serial_number) specDetails.push({label: 'Serial Number', value: h(data.serial_number) || 'Belum ada SN', fieldName: 'sn_battery', required: true});
        } else if (data.item_type === 'Charger') {
            if (data.merk_charger) specDetails.push({label: 'Merk', value: h(data.merk_charger), fieldName: 'merk', required: false, dropdownType: 'merk_charger', cascadingParent: null});
            if (data.tipe_charger) specDetails.push({label: 'Tipe', value: h(data.tipe_charger), fieldName: 'tipe', required: false, dropdownType: 'tipe_charger', cascadingParent: 'merk', parentValue: h(data.merk_charger)});
            if (data.serial_number) specDetails.push({label: 'Serial Number', value: h(data.serial_number) || 'Belum ada SN', fieldName: 'sn_charger', required: true});
        }
        
        if (data.keterangan) specDetails.push({label: 'Keterangan', value: h(data.keterangan), fieldName: 'keterangan', required: false, isTextarea: true});
        
        // Build table rows with editable fields (same format as unit)
        let tableRows = '';
        specDetails.forEach((spec, index) => {
            const fieldId = `verify_${spec.fieldName || spec.label.toLowerCase().replace(/\s+/g, '_')}`;
            const checkId = `check_${spec.fieldName || spec.label.toLowerCase().replace(/\s+/g, '_')}`;
            const dbId = `db_${spec.fieldName || spec.label.toLowerCase().replace(/\s+/g, '_')}`;
            
            const dbValue = spec.value && spec.value !== '-' && spec.value !== 'Belum ada SN' ? spec.value : '';
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
                const isSNField = spec.fieldName && (spec.fieldName.includes('sn_') || spec.fieldName === 'sn_attachment' || spec.fieldName === 'sn_battery' || spec.fieldName === 'sn_charger');
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
        
        let itemName = "";
        if (data.item_type === 'Attachment') {
            itemName = `${h(data.merk_attachment)} | ${h(data.model_attachment)} - ${h(data.tipe_attachment)}`;
        } else if (data.item_type === 'Battery') {
            itemName = `${h(data.merk_battery)} | ${h(data.tipe_battery)} | ${h(data.jenis_battery)}`;
        } else if (data.item_type === 'Charger') {
            itemName = `${h(data.merk_charger)} | ${h(data.tipe_charger)}`;
        } else {
            itemName = "Unknown Item";
        }
        
        return `
            <form id="attachmentVerificationFormInline" data-item-id="${data.id_po_attachment}" data-po-id="${data.po_id}" data-item-type="${data.item_type}">
                <div class="card table-card animate__animated animate__fadeIn">
                    <div class="card-header p-3" style="background-color: #f5f5f5; border-bottom: 1px solid #ccc;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold m-0" style="font-size: 1rem; color: #000;">
                                <i class="fas fa-clipboard-check me-2"></i>Verifikasi Data Item: ${itemName}
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
                                <label for="attachment_lokasi_unit_inline" class="form-label fw-semibold">
                                    <i class="fas fa-map-marker-alt me-2"></i>Lokasi Penyimpanan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="attachment_lokasi_unit_inline" name="attachment_lokasi_unit" required>
                                    <option value="">-- Pilih Lokasi Penyimpanan --</option>
                                    <option value="POS 1">POS 1</option>
                                    <option value="POS 2">POS 2</option>
                                    <option value="POS 3">POS 3</option>
                                    <option value="POS 4">POS 4</option>
                                    <option value="POS 5">POS 5</option>
                                </select>
                                <div class="form-text">Lokasi ini akan digunakan untuk inventory item setelah verifikasi selesai</div>
                            </div>
                        </div>
                        
                        <div class="row mb-3" id="attachment-alasan-reject-container" style="display: none;">
                            <div class="col-12">
                                <label for="attachment_alasan_reject_inline" class="form-label fw-semibold">
                                    <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Alasan Reject <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" 
                                          id="attachment_alasan_reject_inline" 
                                          name="attachment_alasan_reject" 
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
                        <button type="button" class="btn btn-success" id="btn-submit-attachment-verification-inline" disabled>
                            <i class="fas fa-check-circle me-2"></i>Submit Verifikasi
                        </button>
                    </div>
                </div>
            </form>`;
    }

    function updateAttachmentStatusVerifikasi(itemId, poId, status, snData = {}, catatan = '', lokasiUnit = '', discrepancies = []) {
        window._verifyingAttachment = true;
        $('#btn-submit-attachment-verification-inline, #btn-submit-attachment-verification').prop('disabled', true);
        
        $.ajax({
            type: "POST",
            url: "<?= base_url('warehouse/purchase-orders/verify-po-attachment') ?>",
            data: {
                id_item: itemId,
                po_id: poId,
                status: status,
                catatan_verifikasi: catatan,
                lokasi_unit: lokasiUnit,
                discrepancies: JSON.stringify(discrepancies),
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                ...snData
            },
            dataType: "JSON",
            beforeSend: () => Swal.showLoading(),
            success: function(response) {
                window._verifyingAttachment = false;
                $('#btn-submit-attachment-verification-inline, #btn-submit-attachment-verification').prop('disabled', false);
                Swal.close();
                if (response.success) {
                    $('#modalAttachmentVerification').modal('hide');
                    Swal.fire('Berhasil!', 'Verifikasi berhasil!', 'success');
                    
                    let sisaElem = $(`#lbl-remain-attachment-po-${poId}`);
                    let sisaCount = parseInt(sisaElem.text()) - 1;
                    sisaElem.text(`${sisaCount} Item`);
                    
                    $(`#list-attachment-item-${itemId}`).fadeOut(500, function() { 
                        $(this).remove(); 
                        if (sisaCount === 0) {
                            $(`[data-po-id="${poId}"]`).fadeOut(500);
                        }
                    });

                    $('#attachment-detail-view-container').html(`
                        <div class="card table-card">
                            <div class="card-body text-center p-5">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h5 class="text-muted">Verifikasi berhasil! Silakan pilih item lain.</h5>
                            </div>
                        </div>
                    `);
                } else {
                    Swal.fire('Error!', response.message || 'Terjadi kesalahan.', 'error');
                }
            },
            error: (xhr) => {
                window._verifyingAttachment = false;
                $('#btn-submit-attachment-verification-inline, #btn-submit-attachment-verification').prop('disabled', false);
                Swal.fire("Error", "Terjadi kesalahan tak terduga.", "error");
                console.error(xhr.responseText);
            }
        });
    }
})();
</script>

