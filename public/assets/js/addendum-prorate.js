/**
 * Addendum Prorate Split Calculator
 * Sprint 3: Advanced Features
 * 
 * Handles mid-period rate changes with automatic proration
 * Visualizes billing split into two parts: old rate + new rate
 */

class AddendumProrateCalculator {
    constructor() {
        this.contractData = {};
        this.units = [];
        this.effectiveDate = null;
        this.periodStart = null;
        this.periodEnd = null;
        this.isPreloaded = false;

        this.init();
    }

    init() {
        // loadActiveContracts() is NOT called here — called lazily in list-page mode only
        this.bindEvents();
    }
    
    bindEvents() {
        $('#prorateContractId').on('change', () => this.loadContractData());
        $('#prorateEffectiveDate').on('change', () => this.validateEffectiveDate());
        $('#calculateProrateBtn').on('click', () => this.calculateProrate());
        $('#submitAddendumBtn').on('click', () => this.submitAddendum());
        $('#applyBulkRateChange').on('click', () => this.showBulkRateModal());
        $('#applyBulkBtn').on('click', () => this.applyBulkRateChange());
        
        // Real-time calculation on new rate input
        $(document).on('input', '.new-rate-input', () => this.calculateProrate());
    }
    
    async loadActiveContracts() {
        try {
            const response = await fetch(`${BASE_URL}marketing/rental/get-active-contracts`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();

            const select = $('#prorateContractId');
            select.html('<option value="">-- Select active contract --</option>');

            if (data.success && data.data) {
                data.data.forEach(contract => {
                    select.append(`
                        <option value="${contract.id}" data-contract='${JSON.stringify(contract)}'>
                            ${contract.no_kontrak} - ${contract.customer_name}
                        </option>
                    `);
                });
            }
        } catch (error) {
            console.error('Failed to load active contracts:', error);
        }
    }
    
    async loadContractData() {
        const selectedOption = $('#prorateContractId option:selected');
        if (!selectedOption.val()) {
            $('#currentPeriodCard').hide();
            return;
        }

        this.contractData = JSON.parse(selectedOption.attr('data-contract'));
        // Normalize field names from list-page contract data
        this.contractData.start_date = this.contractData.start_date || this.contractData.tanggal_mulai;
        this.contractData.end_date   = this.contractData.end_date   || this.contractData.tanggal_berakhir;

        // Sync hidden contract_id field
        $('#prorateContractIdHidden').val(this.contractData.id);

        this.calculateCurrentPeriod();
        $('#display_period_start').text(this.formatDate(this.periodStart));
        $('#display_period_end').text(this.formatDate(this.periodEnd));
        const totalDays = this.calculateDays(this.periodStart, this.periodEnd);
        $('#display_total_days').text(`${totalDays} days`);
        $('#currentPeriodCard').show();

        await this.loadContractUnits();
    }
    
    calculateCurrentPeriod() {
        const today = new Date();
        const billingMethod = this.contractData.billing_method || 'CYCLE';
        
        if (billingMethod === 'CYCLE') {
            // 30-day rolling cycle
            // For simplicity, assume current period started at contract start
            const contractStart = new Date(this.contractData.start_date);
            const daysSinceStart = Math.floor((today - contractStart) / (1000 * 60 * 60 * 24));
            const cycleNumber = Math.floor(daysSinceStart / 30);
            
            this.periodStart = new Date(contractStart);
            this.periodStart.setDate(this.periodStart.getDate() + (cycleNumber * 30));
            
            this.periodEnd = new Date(this.periodStart);
            this.periodEnd.setDate(this.periodEnd.getDate() + 29);
            
        } else if (billingMethod === 'PRORATE') {
            // Month-end billing
            this.periodStart = new Date(today.getFullYear(), today.getMonth(), 1);
            this.periodEnd = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            
        } else if (billingMethod === 'MONTHLY_FIXED') {
            // Fixed date billing
            const billingDay = this.contractData.billing_start_date || 1;
            this.periodStart = new Date(today.getFullYear(), today.getMonth(), billingDay);
            
            if (today < this.periodStart) {
                this.periodStart.setMonth(this.periodStart.getMonth() - 1);
            }
            
            this.periodEnd = new Date(this.periodStart);
            this.periodEnd.setMonth(this.periodEnd.getMonth() + 1);
            this.periodEnd.setDate(this.periodEnd.getDate() - 1);
        }
        
        // Store in hidden fields
        $('#hidden_period_start').val(this.formatDateInput(this.periodStart));
        $('#hidden_period_end').val(this.formatDateInput(this.periodEnd));
    }
    
    async loadContractUnits() {
        try {
            const response = await fetch(`${BASE_URL}marketing/rental/units/${this.contractData.id}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();

            if (data.success && data.data) {
                this.units = data.data;
                this.renderUnitsTable();
            }
        } catch (error) {
            console.error('Failed to load contract units:', error);
        }
    }
    
    renderUnitsTable() {
        const tbody = $('#prorateUnitsTable tbody');
        tbody.empty();

        if (!this.units.length) {
            tbody.append('<tr><td colspan="4" class="text-center text-muted py-3">No units found for this contract</td></tr>');
            return;
        }

        this.units.forEach((unit, index) => {
            const currentRate = parseFloat(unit.harga_efektif) || 0;

            tbody.append(`
                <tr>
                    <td><strong>${unit.no_unit}</strong><br><small class="text-muted">${unit.jenis_unit || '-'}</small></td>
                    <td>${this.formatCurrency(currentRate)}</td>
                    <td>
                        <input type="number" class="form-control form-control-sm new-rate-input"
                               data-unit-id="${unit.id_inventory_unit}" data-old-rate="${currentRate}"
                               value="${currentRate}" step="1000" min="0" required>
                    </td>
                    <td>
                        <span class="rate-change-badge" id="change_${unit.id_inventory_unit}">
                            <span class="badge badge-soft-gray"><i class="fas fa-minus me-1"></i>No change</span>
                        </span>
                    </td>
                </tr>
            `);
        });

        this.updateChangeBadges();
    }
    
    updateChangeBadges() {
        $('.new-rate-input').each((i, input) => {
            const unitId  = $(input).data('unit-id');
            const oldRate = parseFloat($(input).data('old-rate'));
            const newRate = parseFloat($(input).val()) || 0;
            const change  = newRate - oldRate;
            const pct     = oldRate > 0 ? ((change / oldRate) * 100).toFixed(1) : '0.0';

            let html, cls;
            if (change > 0) {
                html = `<i class="fas fa-arrow-up me-1"></i>+${this.formatCurrency(change)} (+${pct}%)`;
                cls  = 'badge badge-soft-green';
            } else if (change < 0) {
                html = `<i class="fas fa-arrow-down me-1"></i>${this.formatCurrency(change)} (${pct}%)`;
                cls  = 'badge badge-soft-red';
            } else {
                html = `<i class="fas fa-minus me-1"></i>No change`;
                cls  = 'badge badge-soft-gray';
            }

            $(`#change_${unitId}`).html(`<span class="${cls}">${html}</span>`);
        });
    }
    
    validateEffectiveDate() {
        this.effectiveDate = new Date($('#prorateEffectiveDate').val());
        
        if (this.effectiveDate < this.periodStart || this.effectiveDate > this.periodEnd) {
            alert(`Effective date must be within current billing period (${this.formatDate(this.periodStart)} - ${this.formatDate(this.periodEnd)})`);
            $('#prorateEffectiveDate').val('');
            return false;
        }
        
        return true;
    }
    
    calculateProrate() {
        if (!this.effectiveDate || !this.periodStart || !this.periodEnd) {
            return;
        }
        
        // Update badges first
        this.updateChangeBadges();
        
        // Calculate days
        const daysBeforeAmendment = this.calculateDays(this.periodStart, this.effectiveDate) - 1;
        const daysAfterAmendment = this.calculateDays(this.effectiveDate, this.periodEnd);
        const totalDays = daysBeforeAmendment + daysAfterAmendment;
        
        // Calculate total old and new amounts
        let totalOldAmount = 0;
        let totalNewAmount = 0;
        let totalOldRate = 0;
        let totalNewRate = 0;
        
        $('.new-rate-input').each((i, input) => {
            const oldRate = parseFloat($(input).data('old-rate'));
            const newRate = parseFloat($(input).val()) || 0;
            
            totalOldRate += oldRate;
            totalNewRate += newRate;
            
            // Prorate calculation
            const oldAmount = (oldRate / 30) * daysBeforeAmendment;
            const newAmount = (newRate / 30) * daysAfterAmendment;
            
            totalOldAmount += oldAmount;
            totalNewAmount += newAmount;
        });
        
        // Update timeline visualization
        const oldPercentage = (daysBeforeAmendment / totalDays) * 100;
        const newPercentage = (daysAfterAmendment / totalDays) * 100;
        
        $('#timeline_old_segment').css('width', oldPercentage + '%');
        $('#timeline_new_segment').css('width', newPercentage + '%');
        
        $('#timeline_start_date').text(this.formatDate(this.periodStart));
        $('#timeline_effective_date').text(this.formatDate(this.effectiveDate));
        $('#timeline_end_date').text(this.formatDate(this.periodEnd));
        
        $('#timeline_old_days').text(`${daysBeforeAmendment} days`);
        $('#timeline_new_days').text(`${daysAfterAmendment} days`);
        
        // Update calculation breakdown
        $('#calc_old_days').text(daysBeforeAmendment);
        $('#calc_old_rate').text(this.formatCurrency(totalOldRate));
        $('#calc_old_formula').text(`(${this.formatCurrency(totalOldRate)} / 30) × ${daysBeforeAmendment}`);
        $('#calc_old_amount').text(this.formatCurrency(totalOldAmount));
        
        $('#calc_new_days').text(daysAfterAmendment);
        $('#calc_new_rate').text(this.formatCurrency(totalNewRate));
        $('#calc_new_formula').text(`(${this.formatCurrency(totalNewRate)} / 30) × ${daysAfterAmendment}`);
        $('#calc_new_amount').text(this.formatCurrency(totalNewAmount));
        
        // Update totals
        const totalAmount = totalOldAmount + totalNewAmount;
        const fullMonthOldRate = (totalOldRate / 30) * totalDays;
        const difference = totalAmount - fullMonthOldRate;
        const differencePercent = fullMonthOldRate > 0 ? ((difference / fullMonthOldRate) * 100).toFixed(2) : 0;
        
        $('#total_days').text(`${totalDays} days`);
        $('#total_amount').text(this.formatCurrency(totalAmount));
        $('#comparison_amount').text(this.formatCurrency(fullMonthOldRate));
        
        if (difference > 0) {
            $('#comparison_badge').attr('class', 'badge badge-soft-green').text(`+${differencePercent}% higher`);
        } else if (difference < 0) {
            $('#comparison_badge').attr('class', 'badge badge-soft-red').text(`${differencePercent}% lower`);
        } else {
            $('#comparison_badge').attr('class', 'badge badge-soft-gray').text('Same');
        }
        
        // Show visualization
        $('#prorateSplitVisualization').show();
        $('#submitAddendumBtn').show();
    }
    
    showBulkRateModal() {
        $('#bulkRateChangeModal').modal('show');
    }
    
    applyBulkRateChange() {
        const type = $('#bulkChangeType').val();
        const value = parseFloat($('#bulkChangeValue').val());
        
        if (!value) {
            alert('Please enter a valid value');
            return;
        }
        
        $('.new-rate-input').each((i, input) => {
            const oldRate = parseFloat($(input).data('old-rate'));
            let newRate = oldRate;
            
            if (type === 'percentage') {
                newRate = oldRate * (1 + value / 100);
            } else {
                newRate = oldRate + value;
            }
            
            $(input).val(Math.round(newRate));
        });
        
        $('#bulkRateChangeModal').modal('hide');
        this.calculateProrate();
    }
    
    async submitAddendum() {
        if (!confirm('Create amendment with prorate split?')) {
            return;
        }
        
        const formData = new FormData($('#addendumProrateForm')[0]);
        
        // Collect unit rate changes
        const unitRates = [];
        $('.new-rate-input').each((i, input) => {
            unitRates.push({
                unit_id: $(input).data('unit-id'),
                old_rate: $(input).data('old-rate'),
                new_rate: $(input).val()
            });
        });
        formData.append('unit_rates', JSON.stringify(unitRates));
        
        try {
            const response = await fetch(`${BASE_URL}marketing/kontrak/createProrateAmendment`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await response.json();
            
            if (data.success) {
                alert('Amendment created successfully!');
                $('#addendumProrateModal').modal('hide');
                
                // Reload table if exists
                if (typeof kontrakTable !== 'undefined') {
                    kontrakTable.ajax.reload();
                }
            } else {
                alert('Error: ' + (data.message || 'Failed to create amendment'));
            }
        } catch (error) {
            console.error('Failed to submit amendment:', error);
            alert('Failed to submit amendment');
        }
    }
    
    // Utility functions
    calculateDays(start, end) {
        const startDate = new Date(start);
        const endDate = new Date(end);
        return Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
    }
    
    formatDate(date) {
        if (!date) return '-';
        const d = new Date(date);
        return d.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
    }
    
    formatDateInput(date) {
        return new Date(date).toISOString().split('T')[0];
    }
    
    formatCurrency(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(amount));
    }

    /**
     * Preload a specific contract by ID (detail-page mode).
     * Hides the dropdown, shows read-only contract preview, and loads units.
     */
    async preloadContract(contractId) {
        this.isPreloaded = true;

        try {
            const response = await fetch(`${BASE_URL}marketing/rental/get/${contractId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await response.json();

            if (!result.success || !result.data) {
                this.showError('Gagal memuat data kontrak');
                return;
            }

            const d = result.data;
            this.contractData = {
                ...d,
                start_date: d.tanggal_mulai || d.start_date,
                end_date:   d.tanggal_berakhir || d.end_date,
            };

            // Sync hidden contract_id field used by submitAddendum
            $('#prorateContractIdHidden').val(d.id);

            // Show contract preview
            $('#preview_amend_contract').text(d.no_kontrak);
            $('#preview_amend_customer').text(d.customer_name);
            $('#preview_amend_period').text(
                `${this.formatDate(this.contractData.start_date)} – ${this.formatDate(this.contractData.end_date)}`
            );
            $('#prorateContractPreview').show();
            $('#prorateContractRow').hide();

            // Calculate and show billing period
            this.calculateCurrentPeriod();
            $('#display_period_start').text(this.formatDate(this.periodStart));
            $('#display_period_end').text(this.formatDate(this.periodEnd));
            $('#display_total_days').text(`${this.calculateDays(this.periodStart, this.periodEnd)} days`);
            $('#currentPeriodCard').show();

            // Load units
            await this.loadContractUnits();

        } catch (error) {
            console.error('preloadContract error:', error);
            this.showError('Gagal memuat data kontrak');
        }
    }

    showError(message) {
        if (window.OptimaNotify && typeof window.OptimaNotify.error === 'function') {
            window.OptimaNotify.error(message, 'Error');
            return;
        }
        alert(message);
    }
}

// Initialize
let addendumProrateCalc;
$(document).ready(function() {
    addendumProrateCalc = new AddendumProrateCalculator();
});

// Function to open Change Rate modal from anywhere
function openAddendumProrateCalculator(contractId = null) {
    if (!addendumProrateCalc) {
        addendumProrateCalc = new AddendumProrateCalculator();
    }

    // Reset state
    addendumProrateCalc.contractData  = {};
    addendumProrateCalc.units         = [];
    addendumProrateCalc.effectiveDate = null;
    addendumProrateCalc.periodStart   = null;
    addendumProrateCalc.periodEnd     = null;
    addendumProrateCalc.isPreloaded   = false;

    // Reset UI
    $('#prorateSplitVisualization').hide();
    $('#submitAddendumBtn').hide();
    $('#currentPeriodCard').hide();
    $('#prorateUnitsTable tbody').empty();
    $('#prorateEffectiveDate').val('');
    $('#prorateReason').val('');
    $('#prorateNotes').val('');

    if (contractId) {
        // ── Detail-page mode: preload contract, skip dropdown ────────────────────
        $('#addendumProrateModal').modal('show');
        addendumProrateCalc.preloadContract(contractId);
    } else {
        // ── List-page mode: show dropdown, lazy-load contracts ───────────────
        $('#prorateContractPreview').hide();
        $('#prorateContractRow').show();
        $('#prorateContractId').val('');
        addendumProrateCalc.loadActiveContracts();
        $('#addendumProrateModal').modal('show');
    }
}
