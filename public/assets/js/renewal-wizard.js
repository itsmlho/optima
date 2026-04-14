/**
 * Renewal Wizard JavaScript Controller
 * Handles 5-step contract renewal process
 * Sprint 3: Advanced Features
 */

class RenewalWizard {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 5;
        this.contractData = {};
        this.selectedUnits = [];
        this.rateAdjustments = {};
        
        this.init();
    }
    
    init() {
        this.loadExpiringContracts();
        this.bindEvents();
    }
    
    bindEvents() {
        // Contract selection
        $('#renewalSourceContract').on('change', () => this.loadContractDetails());
        
        // Navigation buttons
        $('#wizardNextBtn').on('click', () => this.nextStep());
        $('#wizardPrevBtn').on('click', () => this.prevStep());
        $('#wizardSubmitBtn').on('click', () => this.submitRenewal());
        
        // Generate contract number
        $('#generateRenewalContractNumber').on('click', () => this.generateContractNumber());
        
        // Calculate duration
        $('#renewal_end_date').on('change', () => this.calculateDuration());
        
        // Unit selection
        $('#selectAllUnits').on('change', (e) => this.toggleAllUnits(e.target.checked));
        
        // Rate increase
        $('#applyRateIncrease').on('change', (e) => {
            $('#rateIncreaseSection').toggle(e.target.checked);
        });
        $('#applyRateBtn').on('click', () => this.applyRateIncrease());
        
        // Add new unit
        $('#addNewUnitBtn').on('click', () => this.addNewUnit());
    }
    
    async loadExpiringContracts() {
        try {
            const response = await fetch(`${BASE_URL}marketing/rental/getExpiringContracts`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            
            const select = $('#renewalSourceContract');
            select.html('<option value="">-- Select contract to renew --</option>');
            
            if (data.success && data.data) {
                data.data.forEach(contract => {
                    const daysRemaining = this.calculateDaysRemaining(contract.end_date);
                    select.append(`
                        <option value="${contract.id}" data-contract='${JSON.stringify(contract)}'>
                            ${contract.no_kontrak} - ${contract.customer_name} (${daysRemaining} days remaining)
                        </option>
                    `);
                });
            }
        } catch (error) {
            console.error('Failed to load expiring contracts:', error);
            this.showError('Failed to load contracts');
        }
    }
    
    loadContractDetails() {
        const selectedOption = $('#renewalSourceContract option:selected');
        if (!selectedOption.val()) {
            $('#contractPreview').hide();
            return;
        }
        
        this.contractData = JSON.parse(selectedOption.attr('data-contract'));
        
        // Populate preview
        $('#preview_contract_number').text(this.contractData.no_kontrak);
        $('#preview_customer').text(this.contractData.customer_name);
        $('#preview_start_date').text(this.formatDate(this.contractData.start_date));
        $('#preview_end_date').text(this.formatDate(this.contractData.end_date));
        $('#preview_total_units').text(this.contractData.total_units || 0);
        $('#preview_contract_value').text(this.formatCurrency(this.contractData.contract_value || 0));
        $('#preview_billing_method').text(this.getBillingMethodLabel(this.contractData.billing_method));
        
        const daysRemaining = this.calculateDaysRemaining(this.contractData.end_date);
        $('#preview_days_remaining').text(`${daysRemaining} days`);
        
        $('#contractPreview').show();
        
        // Set hidden fields
        $('#parent_contract_id').val(this.contractData.id);
        $('#renewal_customer_id').val(this.contractData.customer_id);
        $('#renewal_location_id').val(this.contractData.location_id);
        
        // Load units
        this.loadContractUnits();
    }
    
    async loadContractUnits() {
        try {
            const response = await fetch(`${BASE_URL}marketing/rental/units/${this.contractData.id}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            
            if (data.success && data.data) {
                this.selectedUnits = data.data;
                this.renderUnitsTable();
            }
        } catch (error) {
            console.error('Failed to load contract units:', error);
        }
    }
    
    renderUnitsTable() {
        const tbody = $('#currentUnitsTable tbody');
        tbody.empty();
        
        this.selectedUnits.forEach((unit, index) => {
            tbody.append(`
                <tr data-unit-id="${unit.id}">
                    <td><input type="checkbox" class="unit-checkbox" data-index="${index}" checked></td>
                    <td>${unit.nomor_unit}</td>
                    <td>${unit.tipe_unit || '-'}</td>
                    <td>${this.formatCurrency(unit.monthly_rate || 0)}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-warning" onclick="renewalWizard.replaceUnit(${index})">
                            <i class="fas fa-exchange-alt"></i> Replace
                        </button>
                    </td>
                </tr>
            `);
        });
    }
    
    nextStep() {
        // Validate current step
        if (!this.validateStep(this.currentStep)) {
            return;
        }
        
        if (this.currentStep < this.totalSteps) {
            this.currentStep++;
            this.updateStepDisplay();
            
            // Prepare next step
            if (this.currentStep === 2) {
                this.prepareStep2();
            } else if (this.currentStep === 4) {
                this.prepareStep4();
            } else if (this.currentStep === 5) {
                this.prepareStep5();
            }
        }
    }
    
    prevStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.updateStepDisplay();
        }
    }
    
    updateStepDisplay() {
        // Hide all steps
        $('.wizard-step').hide();
        $(`#step${this.currentStep}`).show();
        
        // Update stepper
        $('.stepper-step').removeClass('active completed');
        for (let i = 1; i < this.currentStep; i++) {
            $(`.stepper-step[data-step="${i}"]`).addClass('completed');
        }
        $(`.stepper-step[data-step="${this.currentStep}"]`).addClass('active');
        
        // Update progress bar
        const progress = ((this.currentStep - 1) / (this.totalSteps - 1)) * 100;
        $('#stepperProgress').css('width', `${progress}%`);
        
        // Update buttons
        $('#wizardPrevBtn').toggle(this.currentStep > 1);
        $('#wizardNextBtn').toggle(this.currentStep < this.totalSteps);
        $('#wizardSubmitBtn').toggle(this.currentStep === this.totalSteps);
    }
    
    prepareStep2() {
        // Auto-calculate start date (next day after old contract ends)
        const endDate = new Date(this.contractData.end_date);
        endDate.setDate(endDate.getDate() + 1);
        $('#renewal_start_date').val(this.formatDateInput(endDate));
        
        // Set billing method from old contract
        $('#renewal_billing_method').val(this.contractData.billing_method || 'CYCLE');
        $('#renewal_rental_type').val(this.contractData.rental_type || 'CONTRACT');
    }
    
    prepareStep4() {
        // Populate rate adjustment table
        const tbody = $('#rateAdjustmentTable tbody');
        tbody.empty();
        
        this.selectedUnits.forEach((unit, index) => {
            const checked = $(`.unit-checkbox[data-index="${index}"]`).is(':checked');
            if (!checked) return;
            
            const oldRate = parseFloat(unit.monthly_rate) || 0;
            const newRate = this.rateAdjustments[unit.id] || oldRate;
            const change = newRate - oldRate;
            const changePercent = oldRate > 0 ? ((change / oldRate) * 100).toFixed(2) : 0;
            
            tbody.append(`
                <tr>
                    <td>${unit.nomor_unit}</td>
                    <td>${this.formatCurrency(oldRate)}</td>
                    <td>${this.formatCurrency(newRate)}</td>
                    <td>
                        <span class="badge ${change > 0 ? 'bg-success' : change < 0 ? 'bg-danger' : 'bg-secondary'}">
                            ${change > 0 ? '+' : ''}${this.formatCurrency(change)} (${changePercent}%)
                        </span>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm custom-rate" 
                               data-unit-id="${unit.id}" value="${newRate}" step="1000" min="0">
                    </td>
                </tr>
            `);
        });
        
        // Bind custom rate inputs
        $('.custom-rate').on('change', (e) => {
            const unitId = $(e.target).data('unit-id');
            this.rateAdjustments[unitId] = parseFloat($(e.target).val());
            this.prepareStep4(); // Refresh
        });
    }
    
    prepareStep5() {
        // Old contract summary
        $('#confirm_old_contract').text(this.contractData.no_kontrak);
        $('#confirm_old_period').text(`${this.formatDate(this.contractData.start_date)} - ${this.formatDate(this.contractData.end_date)}`);
        $('#confirm_old_units').text(this.contractData.total_units || 0);
        $('#confirm_old_value').text(this.formatCurrency(this.contractData.contract_value || 0));
        
        // New contract summary
        $('#confirm_new_contract').text($('#renewal_contract_number').val());
        $('#confirm_new_period').text(`${this.formatDate($('#renewal_start_date').val())} - ${this.formatDate($('#renewal_end_date').val())}`);
        
        const selectedCount = $('.unit-checkbox:checked').length;
        $('#confirm_new_units').text(selectedCount);
        
        const newValue = this.calculateNewContractValue();
        $('#confirm_new_value').text(this.formatCurrency(newValue));
        
        // Changes summary
        const changes = [];
        if (selectedCount !== this.contractData.total_units) {
            changes.push(`<li><i class="fas fa-exchange-alt text-warning me-2"></i>Units changed: ${this.contractData.total_units} → ${selectedCount}</li>`);
        }
        if (newValue !== this.contractData.contract_value) {
            const diff = newValue - this.contractData.contract_value;
            changes.push(`<li><i class="fas fa-dollar-sign text-info me-2"></i>Contract value: ${this.formatCurrency(diff > 0 ? '+' + diff : diff)}</li>`);
        }
        
        if (changes.length === 0) {
            changes.push(`<li><i class="fas fa-check-circle text-success me-2"></i>No changes - all terms carried over</li>`);
        }
        
        $('#changesSummary').html('<ul>' + changes.join('') + '</ul>');
    }
    
    validateStep(step) {
        if (step === 1) {
            if (!$('#renewalSourceContract').val()) {
                this.showError('Please select a contract to renew');
                return false;
            }
        } else if (step === 2) {
            if (!$('#renewal_contract_number').val() || !$('#renewal_end_date').val()) {
                this.showError('Please fill in all required fields');
                return false;
            }
        } else if (step === 3) {
            if ($('.unit-checkbox:checked').length === 0) {
                this.showError('Please select at least one unit');
                return false;
            }
        } else if (step === 5) {
            if (!$('#confirmRenewal').is(':checked')) {
                this.showError('Please confirm the renewal');
                return false;
            }
        }
        return true;
    }
    
    async submitRenewal() {
        const formData = new FormData($('#renewalWizardForm')[0]);
        
        // Add selected units with rates
        const unitsData = [];
        $('.unit-checkbox:checked').each((i, checkbox) => {
            const index = $(checkbox).data('index');
            const unit = this.selectedUnits[index];
            unitsData.push({
                unit_id: unit.id,
                monthly_rate: this.rateAdjustments[unit.id] || unit.monthly_rate
            });
        });
        formData.append('units', JSON.stringify(unitsData));
        
        try {
            const response = await fetch(`${BASE_URL}marketing/rental/createRenewal`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await response.json();
            
            if (data.success) {
                this.showSuccess('Renewal contract created successfully!');
                $('#renewalWizardModal').modal('hide');
                
                // Reload page or table
                if (typeof kontrakTable !== 'undefined') {
                    kontrakTable.ajax.reload();
                } else {
                    location.reload();
                }
            } else {
                this.showError(data.message || 'Failed to create renewal');
            }
        } catch (error) {
            console.error('Renewal submission error:', error);
            this.showError('Failed to submit renewal');
        }
    }
    
    applyRateIncrease() {
        const type = $('#rateIncreaseType').val();
        const value = parseFloat($('#rateIncreaseValue').val());
        
        if (!value || value <= 0) {
            this.showError('Please enter a valid increase value');
            return;
        }
        
        this.selectedUnits.forEach(unit => {
            const oldRate = parseFloat(unit.monthly_rate) || 0;
            let newRate = oldRate;
            
            if (type === 'percentage') {
                newRate = oldRate * (1 + value / 100);
            } else {
                newRate = oldRate + value;
            }
            
            this.rateAdjustments[unit.id] = Math.round(newRate);
        });
        
        this.prepareStep4();
        this.showSuccess('Rate increase applied to all units');
    }
    
    async generateContractNumber() {
        try {
            const response = await fetch(`${BASE_URL}marketing/rental/generateContractNumber`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            
            if (data.success) {
                $('#renewal_contract_number').val(data.contract_number);
            }
        } catch (error) {
            console.error('Failed to generate contract number:', error);
        }
    }
    
    calculateDuration() {
        const start = new Date($('#renewal_start_date').val());
        const end = new Date($('#renewal_end_date').val());
        
        if (start && end && end > start) {
            const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
            $('#renewal_duration').text(days);
        }
    }
    
    calculateNewContractValue() {
        let total = 0;
        $('.unit-checkbox:checked').each((i, checkbox) => {
            const index = $(checkbox).data('index');
            const unit = this.selectedUnits[index];
            const rate = this.rateAdjustments[unit.id] || unit.monthly_rate || 0;
            total += parseFloat(rate);
        });
        return total;
    }
    
    toggleAllUnits(checked) {
        $('.unit-checkbox').prop('checked', checked);
    }
    
    replaceUnit(index) {
        // TODO: Implement unit replacement modal
        alert('Unit replacement feature - to be implemented');
    }
    
    addNewUnit() {
        // TODO: Implement add new unit modal
        alert('Add new unit feature - to be implemented');
    }
    
    // Utility functions
    calculateDaysRemaining(endDate) {
        const end = new Date(endDate);
        const today = new Date();
        const diff = Math.ceil((end - today) / (1000 * 60 * 60 * 24));
        return diff;
    }
    
    formatDate(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
    }
    
    formatDateInput(date) {
        return date.toISOString().split('T')[0];
    }
    
    formatCurrency(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }
    
    getBillingMethodLabel(method) {
        const labels = {
            'CYCLE': '30-Day Rolling Cycle',
            'PRORATE': 'Prorate to Month-End',
            'MONTHLY_FIXED': 'Fixed Monthly Date'
        };
        return labels[method] || method;
    }
    
    showSuccess(message) {
        if (window.OptimaNotify && typeof window.OptimaNotify.success === 'function') {
            window.OptimaNotify.success(message, 'Success');
            return;
        }
        alert(message);
    }
    
    showError(message) {
        if (window.OptimaNotify && typeof window.OptimaNotify.error === 'function') {
            window.OptimaNotify.error(message, 'Error');
            return;
        }
        alert(message);
    }
}

// Initialize on page load
let renewalWizard;
$(document).ready(function() {
    renewalWizard = new RenewalWizard();
});

// Function to open renewal wizard from anywhere
function openRenewalWizard(contractId = null) {
    $('#renewalWizardModal').modal('show');
    
    if (contractId) {
        $('#renewalSourceContract').val(contractId).trigger('change');
    }
}
