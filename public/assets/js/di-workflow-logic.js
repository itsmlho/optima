/**
 * DI Workflow Logic Frontend Implementation
 * Handles dynamic dropdown interactions based on business rules
 */

class DiWorkflowLogic {
    constructor() {
        this.baseUrl = window.location.origin;
        this.initializeEventListeners();
        this.currentJenisId = null;
        this.currentTujuanId = null;
        this.currentSpkId = null;
    }

    /**
     * Initialize event listeners
     */
    initializeEventListeners() {
        // Jenis Perintah change handler
        $(document).on('change', '#jenisPerintahSelect', (e) => {
            this.handleJenisPerintahChange(e.target.value);
        });

        // Tujuan Perintah change handler
        $(document).on('change', '#tujuanPerintahSelect', (e) => {
            this.handleTujuanPerintahChange(e.target.value);
        });

        // SPK selection change handler
        $(document).on('change', '#spkSelect', (e) => {
            this.handleSpkSelectionChange(e.target.value);
        });

        // Load initial data
        this.loadJenisPerintahKerja();
    }

    /**
     * Load Jenis Perintah Kerja dropdown
     */
    async loadJenisPerintahKerja() {
        try {
            const response = await fetch(`${this.baseUrl}/operational/api/jenis-perintah-kerja`);
            const result = await response.json();

            if (result.success) {
                this.populateJenisPerintahDropdown(result.data);
            } else {
                this.showError('Failed to load jenis perintah kerja');
            }
        } catch (error) {
            console.error('Error loading jenis perintah:', error);
            this.showError('Error loading jenis perintah kerja');
        }
    }

    /**
     * Populate Jenis Perintah dropdown
     */
    populateJenisPerintahDropdown(data) {
        const select = $('#jenisPerintahSelect');
        select.empty().append('<option value="">-- Pilih Jenis Perintah --</option>');
        
        data.forEach(item => {
            select.append(`
                <option value="${item.id}" data-kode="${item.kode}" 
                        title="${item.deskripsi}">
                    ${item.nama}
                </option>
            `);
        });

        // Add helper text
        this.updateHelperText('#jenisPerintahSelect', 
            'Pilih jenis aksi yang akan dilakukan (ANTAR, TARIK, TUKAR, atau RELOKASI)');
    }

    /**
     * Handle Jenis Perintah change
     */
    async handleJenisPerintahChange(jenisId) {
        if (!jenisId) {
            this.resetTujuanPerintahDropdown();
            this.resetSpkDropdown();
            this.resetUnitSelection();
            return;
        }

        this.currentJenisId = jenisId;
        
        // Show loading
        this.showLoading('#tujuanPerintahSelect');
        
        try {
            const response = await fetch(`${this.baseUrl}/operational/api/tujuan-perintah-kerja?jenis_id=${jenisId}`);
            const result = await response.json();

            if (result.success) {
                this.populateTujuanPerintahDropdown(result.data);
                
                // Show workflow info
                this.showWorkflowInfo(jenisId);
            } else {
                this.showError('Failed to load tujuan perintah kerja');
            }
        } catch (error) {
            console.error('Error loading tujuan perintah:', error);
            this.showError('Error loading tujuan perintah kerja');
        }
    }

    /**
     * Populate Tujuan Perintah dropdown
     */
    populateTujuanPerintahDropdown(data) {
        const select = $('#tujuanPerintahSelect');
        select.empty().append('<option value="">-- Pilih Tujuan Perintah --</option>');
        
        data.forEach(item => {
            select.append(`
                <option value="${item.id}" data-kode="${item.kode}" 
                        title="${item.deskripsi}">
                    ${item.nama}
                </option>
            `);
        });

        select.prop('disabled', false);
        
        // Update helper text
        this.updateHelperText('#tujuanPerintahSelect', 
            'Pilih alasan/konteks dari perintah kerja ini');
    }

    /**
     * Handle Tujuan Perintah change - Updated for contract-based selection
     */
    async handleTujuanPerintahChange(tujuanId) {
        if (!tujuanId || !this.currentJenisId) {
            this.resetSpkDropdown();
            this.resetUnitSelection();
            return;
        }

        this.currentTujuanId = tujuanId;
        
        // Show loading
        this.showLoading('#spkSelect');
        
        try {
            // Get jenis code to determine workflow type
            const jenisSelect = $('#jenisPerintahSelect');
            const jenisKode = jenisSelect.find('option:selected').data('kode');
            
            let apiEndpoint = '';
            if (jenisKode === 'TARIK' || jenisKode === 'TUKAR') {
                // Use contract-based endpoint for TARIK/TUKAR
                apiEndpoint = `${this.baseUrl}/operational/api/available-spk-with-units?jenis_id=${this.currentJenisId}&tujuan_id=${tujuanId}`;
            } else {
                // Use regular endpoint for ANTAR/RELOKASI
                apiEndpoint = `${this.baseUrl}/operational/api/available-spk?jenis_id=${this.currentJenisId}&tujuan_id=${tujuanId}`;
            }

            const response = await fetch(apiEndpoint);
            const result = await response.json();

            if (result.success) {
                if (result.workflow_type === 'contract_based') {
                    this.populateContractBasedSpkDropdown(result.data, result.constraints, result.message);
                } else {
                    this.populateSpkDropdown(result.data, result.constraints, result.message);
                }
                this.showConstraintsInfo(result.constraints);
            } else {
                this.showError(result.message || 'Failed to load available SPK');
            }
        } catch (error) {
            console.error('Error loading available SPK:', error);
            this.showError('Error loading available SPK');
        }
    }

    /**
     * Populate SPK dropdown for contract-based operations (TARIK/TUKAR)
     */
    populateContractBasedSpkDropdown(data, constraints, message) {
        const select = $('#spkSelect');
        select.empty().append('<option value="">-- Pilih SPK --</option>');
        
        if (data.length === 0) {
            select.append('<option value="" disabled>Tidak ada SPK yang tersedia</option>');
            this.updateHelperText('#spkSelect', 
                'Tidak ada SPK dengan kontrak aktif yang memenuhi kriteria');
            return;
        }

        data.forEach(item => {
            const contractInfo = item.nomor_kontrak 
                ? `[${item.nomor_kontrak}] ${item.pelanggan}` 
                : `No Contract - ${item.pelanggan || 'Unknown'}`;
                
            const unitInfo = item.available_units_count 
                ? ` (${item.available_units_count} unit tersedia)`
                : ' (0 unit)';
                
            const optionText = `${item.nomor_spk} - ${contractInfo}${unitInfo}`;
            
            select.append(`
                <option value="${item.id}" 
                        data-kontrak-id="${item.kontrak_id}"
                        data-kontrak-status="${item.kontrak_status}"
                        data-total-units="${item.total_units_in_contract}"
                        data-available-units="${item.available_units_count}"
                        title="Status: ${item.kontrak_status || 'No Contract'}, Total Unit: ${item.total_units_in_contract}">
                    ${optionText}
                </option>
            `);
        });

        select.prop('disabled', false);
        
        // Update helper text with contract-specific message
        this.updateHelperText('#spkSelect', message + '. Pilih SPK untuk melihat unit yang tersedia dalam kontrak.');
    }

    /**
     * Populate SPK dropdown with business logic constraints
     */
    populateSpkDropdown(data, constraints, message) {
        const select = $('#spkSelect');
        select.empty().append('<option value="">-- Pilih SPK --</option>');
        
        if (data.length === 0) {
            select.append('<option value="" disabled>Tidak ada SPK yang tersedia</option>');
            this.updateHelperText('#spkSelect', 
                'Tidak ada SPK yang memenuhi kriteria jenis dan tujuan perintah yang dipilih');
            return;
        }

        data.forEach(item => {
            const contractInfo = item.nomor_kontrak 
                ? `[${item.nomor_kontrak}] ${item.pelanggan}` 
                : `No Contract - ${item.pelanggan || 'Unknown'}`;
                
            const optionText = `${item.nomor_spk} - ${contractInfo}`;
            
            select.append(`
                <option value="${item.id}" 
                        data-kontrak-status="${item.kontrak_status}"
                        data-pelanggan="${item.pelanggan}"
                        title="Status: ${item.kontrak_status || 'No Contract'}">
                    ${optionText}
                </option>
            `);
        });

        select.prop('disabled', false);
        
        // Update helper text with message from API
        this.updateHelperText('#spkSelect', message);
    }

    /**
     * Handle SPK selection change - Updated for contract-based operations
     */
    async handleSpkSelectionChange(spkId) {
        if (!spkId || !this.currentJenisId || !this.currentTujuanId) {
            this.resetUnitSelection();
            return;
        }

        this.currentSpkId = spkId;
        
        // Get selected SPK data
        const selectedOption = $('#spkSelect option:selected');
        const kontrakId = selectedOption.data('kontrak-id');
        const jenisSelect = $('#jenisPerintahSelect');
        const jenisKode = jenisSelect.find('option:selected').data('kode');
        
        // Show loading
        this.showLoading('#unitSelection');
        
        try {
            let response;
            
            if ((jenisKode === 'TARIK' || jenisKode === 'TUKAR') && kontrakId) {
                // Use contract units endpoint for TARIK/TUKAR
                response = await fetch(
                    `${this.baseUrl}/operational/api/contract-units?kontrak_id=${kontrakId}&jenis_id=${this.currentJenisId}&tujuan_id=${this.currentTujuanId}`
                );
            } else {
                // Use regular available units endpoint for ANTAR/RELOKASI
                response = await fetch(
                    `${this.baseUrl}/operational/api/available-units?spk_id=${spkId}&jenis_id=${this.currentJenisId}&tujuan_id=${this.currentTujuanId}`
                );
            }

            const result = await response.json();

            if (result.success) {
                if (jenisKode === 'TARIK' || jenisKode === 'TUKAR') {
                    this.populateContractUnitSelection(result.data, result.selection_rules, result.message, result.contract_info);
                } else {
                    this.populateUnitSelection(result.data, result.rules, result.message);
                }
            } else {
                this.showError(result.message || 'Failed to load available units');
            }
        } catch (error) {
            console.error('Error loading available units:', error);
            this.showError('Error loading available units');
        }
    }

    /**
     * Populate unit selection for contract-based operations (TARIK/TUKAR)
     */
    populateContractUnitSelection(data, rules, message, contractInfo) {
        const container = $('#unitSelection');
        container.empty();

        if (data.length === 0) {
            container.html(`
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Tidak ada unit yang tersedia untuk ditarik/ditukar dari kontrak ini.
                </div>
            `);
            container.show();
            return;
        }

        // Add contract information header
        if (contractInfo) {
            container.append(`
                <div class="alert alert-info">
                    <h6><i class="fas fa-file-contract"></i> Informasi Kontrak</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Nomor:</strong> ${contractInfo.nomor_kontrak}<br>
                            <strong>Pelanggan:</strong> ${contractInfo.pelanggan}<br>
                            <strong>Lokasi:</strong> ${contractInfo.lokasi || 'N/A'}
                        </div>
                        <div class="col-md-6">
                            <strong>Periode:</strong> ${contractInfo.tanggal_mulai} - ${contractInfo.tanggal_selesai}<br>
                            <strong>Status:</strong> <span class="badge badge-success">${contractInfo.status}</span><br>
                            <strong>Total Unit Tersedia:</strong> ${data.length}
                        </div>
                    </div>
                </div>
            `);
        }

        // Add selection rules warning
        if (rules && rules.warning) {
            container.append(`
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Perhatian:</strong> ${rules.warning}
                </div>
            `);
        }

        // Add unit selection header
        container.append(`
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">
                    <i class="fas fa-truck"></i> ${rules.description || 'Pilih Unit'}
                </h6>
                <div>
                    <button class="btn btn-sm btn-outline-primary" type="button" id="btnSelectAllContract">
                        Pilih Semua
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" type="button" id="btnClearAllContract">
                        Bersihkan
                    </button>
                </div>
            </div>
        `);

        // Add unit list
        const unitList = $('<div class="contract-unit-list"></div>');
        
        data.forEach(unit => {
            const unitCard = this.createContractUnitCard(unit);
            unitList.append(unitCard);
        });

        container.append(unitList);

        // Add selection counter
        container.append(`
            <div class="mt-3 p-2 bg-light rounded">
                <small class="text-muted">
                    Unit terpilih: <span id="selectedContractUnitsCount" class="font-weight-bold">0</span> dari ${data.length} unit
                </small>
            </div>
        `);

        // Add event listeners for contract unit selection
        this.attachContractUnitEventListeners();

        container.show();
    }

    /**
     * Create contract unit card with enhanced information
     */
    createContractUnitCard(unit) {
        const workflowStatus = unit.current_workflow_status || unit.unit_status;
        const statusColor = this.getUnitStatusColor(workflowStatus);
        
        return $(`
            <div class="contract-unit-card mb-3 p-3 border rounded ${unit.can_be_processed ? '' : 'bg-light'}">
                <div class="form-check">
                    <input class="form-check-input contract-unit-checkbox" type="checkbox" 
                           value="${unit.id_inventory_unit}" 
                           id="contract_unit_${unit.id_inventory_unit}"
                           data-unit-no="${unit.no_unit}"
                           data-current-status="${workflowStatus}"
                           ${unit.can_be_processed ? '' : 'disabled'}>
                    <label class="form-check-label d-block" for="contract_unit_${unit.id_inventory_unit}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>Unit ${unit.no_unit}</strong>
                                <span class="badge badge-${statusColor} ml-2">${workflowStatus}</span>
                                <br>
                                <small class="text-muted">
                                    ${unit.merk_unit} ${unit.model_unit}<br>
                                    Kontrak: ${unit.nomor_kontrak}<br>
                                    Pelanggan: ${unit.pelanggan}<br>
                                    Lokasi: ${unit.lokasi}
                                </small>
                            </div>
                            <div class="text-right">
                                <small class="text-muted">
                                    Mulai: ${unit.tanggal_mulai}<br>
                                    Selesai: ${unit.tanggal_selesai}
                                </small>
                                ${unit.next_status ? `<br><small class="text-info">Next: ${unit.next_status}</small>` : ''}
                            </div>
                        </div>
                        ${!unit.can_be_processed ? '<small class="text-danger">Unit tidak dapat diproses saat ini</small>' : ''}
                    </label>
                </div>
            </div>
        `);
    }

    /**
     * Attach event listeners for contract unit selection
     */
    attachContractUnitEventListeners() {
        // Select all contract units
        $(document).off('click', '#btnSelectAllContract').on('click', '#btnSelectAllContract', () => {
            $('.contract-unit-checkbox:not(:disabled)').prop('checked', true);
            this.updateContractUnitCount();
        });

        // Clear all contract units
        $(document).off('click', '#btnClearAllContract').on('click', '#btnClearAllContract', () => {
            $('.contract-unit-checkbox').prop('checked', false);
            this.updateContractUnitCount();
        });

        // Update counter on checkbox change
        $(document).off('change', '.contract-unit-checkbox').on('change', '.contract-unit-checkbox', () => {
            this.updateContractUnitCount();
        });
    }

    /**
     * Update contract unit selection counter
     */
    updateContractUnitCount() {
        const checkedCount = $('.contract-unit-checkbox:checked').length;
        $('#selectedContractUnitsCount').text(checkedCount);
    }

    /**
     * Get unit status color for UI
     */
    getUnitStatusColor(status) {
        const colors = {
            'TERSEDIA': 'success',
            'DISEWA': 'primary',
            'BEROPERASI': 'info',
            'UNIT_AKAN_DITARIK': 'warning',
            'UNIT_SEDANG_DITARIK': 'warning',
            'UNIT_PULANG': 'secondary',
            'STOCK_ASET': 'success',
            'UNIT_AKAN_DITUKAR': 'warning',
            'UNIT_SEDANG_DITUKAR': 'warning',
            'UNIT_TUKAR_SELESAI': 'secondary',
            'MAINTENANCE': 'danger'
        };

        return colors[status] || 'secondary';
    }

    /**
     * Populate unit selection with business rules
     */
    populateUnitSelection(data, rules, message) {
        const container = $('#unitSelection');
        container.empty();

        if (data.length === 0) {
            container.html(`
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Tidak ada unit yang tersedia untuk SPK ini sesuai dengan jenis dan tujuan perintah.
                </div>
            `);
            return;
        }

        // Add header with message
        container.append(`
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                ${message}
            </div>
        `);

        // Add unit list
        const unitList = $('<div class="unit-list"></div>');
        
        data.forEach(unit => {
            const unitCard = this.createUnitCard(unit, rules);
            unitList.append(unitCard);
        });

        container.append(unitList);
        container.show();
    }

    /**
     * Create unit card with selection checkbox
     */
    createUnitCard(unit, rules) {
        const contractInfo = unit.nomor_kontrak 
            ? `${unit.nomor_kontrak} (${unit.kontrak_status})` 
            : 'No Contract';
            
        return $(`
            <div class="unit-card mb-2 p-3 border rounded">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" 
                           value="${unit.id_inventory_unit}" 
                           id="unit_${unit.id_inventory_unit}"
                           data-unit-no="${unit.no_unit}"
                           data-kontrak-status="${unit.kontrak_status}">
                    <label class="form-check-label d-block" for="unit_${unit.id_inventory_unit}">
                        <strong>Unit ${unit.no_unit}</strong>
                        <br>
                        <small class="text-muted">
                            ${unit.merk_unit} ${unit.model_unit}<br>
                            Kontrak: ${contractInfo}<br>
                            Status: ${unit.status}
                            ${unit.kontrak_lokasi ? `<br>Lokasi: ${unit.kontrak_lokasi}` : ''}
                        </small>
                    </label>
                </div>
            </div>
        `);
    }

    /**
     * Show workflow constraints information
     */
    showConstraintsInfo(constraints) {
        const infoContainer = $('#workflowInfo');
        if (!infoContainer.length) return;

        const messages = [];

        if (constraints.requires_active_contract) {
            messages.push('<i class="fas fa-check text-success"></i> Memerlukan kontrak aktif');
        }

        if (constraints.requires_inactive_contract) {
            messages.push('<i class="fas fa-times text-danger"></i> Memerlukan kontrak non-aktif/habis');
        }

        if (constraints.allows_new_contract) {
            messages.push('<i class="fas fa-plus text-info"></i> Memungkinkan kontrak baru');
        }

        if (constraints.requires_unit_preparation) {
            messages.push('<i class="fas fa-tools text-warning"></i> Memerlukan persiapan unit');
        }

        if (messages.length > 0) {
            infoContainer.html(`
                <div class="alert alert-light border">
                    <h6>Aturan Bisnis:</h6>
                    <ul class="mb-0">
                        ${messages.map(msg => `<li>${msg}</li>`).join('')}
                    </ul>
                </div>
            `);
        }
    }

    /**
     * Show unit selection rules
     */
    showUnitSelectionRules(rules) {
        const rulesContainer = $('#unitRules');
        if (!rulesContainer.length) return;

        const ruleMessages = [];

        if (rules.requires_unit_replacement) {
            ruleMessages.push('Memerlukan penggantian unit (ada unit lama dan baru)');
        }

        if (rules.allows_same_location) {
            ruleMessages.push('Memungkinkan perpindahan dalam lokasi yang sama');
        }

        if (ruleMessages.length > 0) {
            rulesContainer.html(`
                <div class="alert alert-secondary">
                    <h6>Aturan Pemilihan Unit:</h6>
                    <ul class="mb-0">
                        ${ruleMessages.map(msg => `<li>${msg}</li>`).join('')}
                    </ul>
                </div>
            `);
        }
    }

    /**
     * Validate contract-based DI data before submission
     */
    async validateContractDiData(formData) {
        try {
            // Enhanced validation for contract-based operations
            const jenisSelect = $('#jenisPerintahSelect');
            const jenisKode = jenisSelect.find('option:selected').data('kode');
            
            // Additional validation for TARIK/TUKAR
            if (jenisKode === 'TARIK' || jenisKode === 'TUKAR') {
                const selectedUnits = $('.contract-unit-checkbox:checked');
                
                if (selectedUnits.length === 0) {
                    this.showValidationErrors(['Pilih minimal 1 unit untuk diproses']);
                    return false;
                }

                // Add selected units to form data
                formData.selected_contract_units = selectedUnits.map(function() {
                    return {
                        unit_id: $(this).val(),
                        unit_no: $(this).data('unit-no'),
                        current_status: $(this).data('current-status')
                    };
                }).get();
            }

            const response = await fetch(`${this.baseUrl}/operational/api/validate-di-data`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();
            
            if (!result.success) {
                this.showValidationErrors(result.errors || [result.message]);
                return false;
            }

            return true;
        } catch (error) {
            console.error('Validation error:', error);
            this.showError('Validation failed');
            return false;
        }
    }

    /**
     * Process workflow approval for TARIK/TUKAR
     */
    async processWorkflowApproval(diId, stage, jenisPerintah, additionalData = {}) {
        try {
            const data = {
                di_id: diId,
                stage: stage,
                jenis_perintah: jenisPerintah,
                ...additionalData
            };

            // Add unit IDs for TARIK/TUKAR
            if (jenisPerintah === 'TARIK') {
                data.unit_ids = $('.contract-unit-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();
            } else if (jenisPerintah === 'TUKAR') {
                data.old_unit_ids = $('.contract-unit-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();
                data.new_unit_ids = additionalData.new_unit_ids || [];
            }

            const response = await fetch(`${this.baseUrl}/operational/api/process-workflow-approval`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            
            if (result.success) {
                this.showSuccess('Workflow approval processed successfully');
                
                // Refresh unit status display
                if (typeof this.refreshUnitStatus === 'function') {
                    this.refreshUnitStatus();
                }
            } else {
                this.showError(result.message || 'Failed to process workflow approval');
            }

            return result;
        } catch (error) {
            console.error('Workflow approval error:', error);
            this.showError('Failed to process workflow approval');
            return { success: false, message: error.message };
        }
    }

    /**
     * Show workflow stages for TARIK/TUKAR
     */
    showWorkflowStages(jenisPerintah, currentStage = null) {
        const stages = {
            'TARIK': [
                { code: 'DIAJUKAN', name: 'DI Diajukan', description: 'DI diajukan untuk penarikan unit' },
                { code: 'DISETUJUI', name: 'DI Disetujui', description: 'DI disetujui, unit siap ditarik' },
                { code: 'PERSIAPAN_UNIT', name: 'Persiapan', description: 'Persiapan tim dan transportasi' },
                { code: 'DALAM_PERJALANAN', name: 'Dalam Perjalanan', description: 'Tim menuju lokasi pelanggan' },
                { code: 'UNIT_DITARIK', name: 'Unit Ditarik', description: 'Unit berhasil ditarik dari lokasi' },
                { code: 'UNIT_PULANG', name: 'Unit Pulang', description: 'Unit dalam perjalanan kembali' },
                { code: 'SAMPAI_KANTOR', name: 'Sampai Kantor', description: 'Unit sampai di kantor/workshop' },
                { code: 'SELESAI', name: 'Selesai', description: 'Proses penarikan selesai' }
            ],
            'TUKAR': [
                { code: 'DIAJUKAN', name: 'DI Diajukan', description: 'DI diajukan untuk penukaran unit' },
                { code: 'DISETUJUI', name: 'DI Disetujui', description: 'DI disetujui, unit siap ditukar' },
                { code: 'PERSIAPAN_UNIT', name: 'Persiapan', description: 'Persiapan unit baru dan tim' },
                { code: 'DALAM_PERJALANAN', name: 'Dalam Perjalanan', description: 'Tim menuju lokasi pelanggan' },
                { code: 'UNIT_DITUKAR', name: 'Unit Ditukar', description: 'Unit berhasil ditukar' },
                { code: 'UNIT_LAMA_PULANG', name: 'Unit Lama Pulang', description: 'Unit lama dalam perjalanan kembali' },
                { code: 'SAMPAI_KANTOR', name: 'Sampai Kantor', description: 'Unit lama sampai di kantor' },
                { code: 'SELESAI', name: 'Selesai', description: 'Proses penukaran selesai' }
            ]
        };

        const workflowStages = stages[jenisPerintah] || [];
        const container = $('#workflowStages');
        
        if (container.length && workflowStages.length > 0) {
            const stageHtml = workflowStages.map((stage, index) => {
                const isActive = stage.code === currentStage;
                const isCompleted = currentStage && workflowStages.findIndex(s => s.code === currentStage) > index;
                
                return `
                    <div class="workflow-stage ${isActive ? 'active' : ''} ${isCompleted ? 'completed' : ''}">
                        <div class="stage-indicator">
                            <span class="stage-number">${index + 1}</span>
                        </div>
                        <div class="stage-content">
                            <h6 class="stage-name">${stage.name}</h6>
                            <small class="stage-description">${stage.description}</small>
                        </div>
                    </div>
                `;
            }).join('');

            container.html(`
                <div class="workflow-timeline">
                    <h6>Tahapan Workflow ${jenisPerintah}</h6>
                    <div class="stages">
                        ${stageHtml}
                    </div>
                </div>
            `);
        }
    }

    /**
     * Show validation errors
     */
    showValidationErrors(errors) {
        const errorContainer = $('#validationErrors');
        if (!errorContainer.length) return;

        const errorHtml = errors.map(error => `<li>${error}</li>`).join('');
        
        errorContainer.html(`
            <div class="alert alert-danger">
                <h6>Validation Errors:</h6>
                <ul class="mb-0">${errorHtml}</ul>
            </div>
        `).show();
    }

    // ========================================
    // Helper Methods
    // ========================================

    /**
     * Reset Tujuan Perintah dropdown
     */
    resetTujuanPerintahDropdown() {
        const select = $('#tujuanPerintahSelect');
        select.empty().append('<option value="">-- Pilih Jenis Perintah dulu --</option>');
        select.prop('disabled', true);
        this.currentTujuanId = null;
    }

    /**
     * Reset SPK dropdown
     */
    resetSpkDropdown() {
        const select = $('#spkSelect');
        select.empty().append('<option value="">-- Pilih Tujuan Perintah dulu --</option>');
        select.prop('disabled', true);
        this.currentSpkId = null;
    }

    /**
     * Reset unit selection
     */
    resetUnitSelection() {
        $('#unitSelection').empty().hide();
        $('#workflowInfo').empty();
        $('#unitRules').empty();
    }

    /**
     * Show loading state
     */
    showLoading(selector) {
        $(selector).html('<option value="">Loading...</option>').prop('disabled', true);
    }

    /**
     * Update helper text
     */
    updateHelperText(selector, text) {
        const helpElement = $(selector).siblings('.form-text');
        if (helpElement.length) {
            helpElement.text(text);
        }
    }

    /**
     * Show success message
     */
    showSuccess(message) {
        // Implement your success notification system
        console.log('Success:', message);
        
        if (typeof window.showToast === 'function') {
            window.showToast('success', message);
        } else {
            alert('Success: ' + message);
        }
    }

    /**
     * Reset unit selection - Updated for contract units
     */
    resetUnitSelection() {
        $('#unitSelection').empty().hide();
        $('#workflowInfo').empty();
        $('#unitRules').empty();
        $('#workflowStages').empty();
    }

    /**
     * Get selected contract units data
     */
    getSelectedContractUnitsData() {
        return $('.contract-unit-checkbox:checked').map(function() {
            return {
                unit_id: $(this).val(),
                unit_no: $(this).data('unit-no'),
                current_status: $(this).data('current-status')
            };
        }).get();
    }

    /**
     * Check if workflow is contract-based
     */
    isContractBasedWorkflow() {
        const jenisSelect = $('#jenisPerintahSelect');
        const jenisKode = jenisSelect.find('option:selected').data('kode');
        return jenisKode === 'TARIK' || jenisKode === 'TUKAR';
    }

    /**
     * Enhanced form validation for contract-based operations
     */
    validateForm() {
        const errors = [];

        // Basic validation
        if (!$('#jenisPerintahSelect').val()) {
            errors.push('Pilih Jenis Perintah Kerja');
        }

        if (!$('#tujuanPerintahSelect').val()) {
            errors.push('Pilih Tujuan Perintah');
        }

        if (!$('#spkSelect').val()) {
            errors.push('Pilih SPK');
        }

        // Contract-based validation
        if (this.isContractBasedWorkflow()) {
            const selectedUnits = this.getSelectedContractUnitsData();
            if (selectedUnits.length === 0) {
                errors.push('Pilih minimal 1 unit dari kontrak');
            }
        } else {
            // Regular unit validation
            const selectedUnits = $('#unitSelection input:checked');
            if (selectedUnits.length === 0) {
                errors.push('Pilih minimal 1 unit');
            }
        }

        if (errors.length > 0) {
            this.showValidationErrors(errors);
            return false;
        }

        return true;
    }

    /**
     * Get form data for submission
     */
    getFormData() {
        const formData = {
            jenis_perintah_kerja_id: $('#jenisPerintahSelect').val(),
            tujuan_perintah_kerja_id: $('#tujuanPerintahSelect').val(),
            spk_id: $('#spkSelect').val(),
            tanggal_kirim: $('#tanggalKirim').val(),
            catatan: $('#catatan').val()
        };

        if (this.isContractBasedWorkflow()) {
            formData.contract_units = this.getSelectedContractUnitsData();
            formData.workflow_type = 'contract_based';
        } else {
            formData.units = $('#unitSelection input:checked').map(function() {
                return $(this).val();
            }).get();
            formData.workflow_type = 'unit_selection';
        }

        return formData;
    }

    /**
     * Show workflow information
     */
    async showWorkflowInfo(jenisId) {
        // This could be extended to show additional workflow guidance
        const jenisSelect = $('#jenisPerintahSelect');
        const selectedOption = jenisSelect.find('option:selected');
        const jenisKode = selectedOption.data('kode');
        
        // Show contextual help based on jenis
        const helpMessages = {
            'ANTAR': 'Proses pengantaran unit ke lokasi pelanggan. Pastikan unit sudah siap dan telah melalui quality check.',
            'TARIK': 'Proses penarikan unit dari lokasi pelanggan. Koordinasikan dengan pelanggan untuk jadwal yang tepat.',
            'TUKAR': 'Proses penukaran unit. Persiapkan unit pengganti dan jadwalkan pengambilan unit lama.',
            'RELOKASI': 'Proses pemindahan unit antar lokasi. Pastikan lokasi tujuan sudah dikonfirmasi.'
        };

        const helpText = helpMessages[jenisKode] || 'Pilih tujuan perintah untuk melanjutkan.';
        
        const infoContainer = $('#jenisWorkflowInfo');
        if (infoContainer.length) {
            infoContainer.html(`
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    ${helpText}
                </div>
            `);
        }
    }
}

// Initialize when document is ready
$(document).ready(function() {
    window.diWorkflowLogic = new DiWorkflowLogic();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DiWorkflowLogic;
}