<!-- Quick Add Master Data Modal -->
<div class="modal fade" id="quickAddModal" tabindex="-1" aria-labelledby="quickAddModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title text-muted" id="quickAddModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>
                    <span id="quick-add-title">Add Data</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="quickAddForm">
                    <input type="hidden" id="quick-add-type" name="type">
                    <div id="quick-add-form-fields">
                        <!-- Dynamic form fields will be loaded here -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="btnSaveQuickAdd">
                    <i class="fas fa-save me-1"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Split Button Styling */
.dropdown-toggle-split-custom {
    border-left: 1px solid rgba(255, 255, 255, 0.3) !important;
    padding-left: 0.5rem !important;
    padding-right: 0.5rem !important;
}

.form-select-split {
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
}

.btn-group-select {
    width: 100%;
}

.btn-group-select .form-select {
    flex: 1;
    min-width: 0;
}

.quick-add-dropdown-menu {
    font-size: 0.9rem;
}

.quick-add-dropdown-menu .dropdown-item {
    padding: 0.5rem 1rem;
}

.quick-add-dropdown-menu .dropdown-item i {
    width: 20px;
    text-align: center;
}

/* Loading State */
#btnSaveQuickAdd.loading {
    position: relative;
    color: transparent;
}

#btnSaveQuickAdd.loading::after {
    content: "";
    position: absolute;
    width: 1rem;
    height: 1rem;
    top: 50%;
    left: 50%;
    margin-left: -0.5rem;
    margin-top: -0.5rem;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spinner 0.6s linear infinite;
}

@keyframes spinner {
    to { transform: rotate(360deg); }
}
</style>

<script>
// Quick Add Modal Handler
const QuickAddModal = {
    modal: null,
    currentType: null,
    currentTarget: null,
    currentBrand: null,
    currentDepartemen: null,
    
    init() {
        this.modal = new bootstrap.Modal(document.getElementById('quickAddModal'));
        this.attachEvents();
        this.setupDropdownHandlers();
    },
    
    setupDropdownHandlers() {
        // Handle __ADD_NEW__ option in all dropdowns
        $(document).on('change', 'select[data-master-type]', function(e) {
            const value = $(this).val();
            const type = $(this).data('master-type');
            const selectId = $(this).attr('id');
            
            if (value === '__ADD_NEW__') {
                e.preventDefault();
                e.stopPropagation();
                
                // Reset dropdown to empty
                $(this).val('').trigger('change');
                
                // Get context data
                let brand = null;
                let departemen = null;
                
                if (type === 'model') {
                    const brandSelect = $('#unit_merk');
                    const selectedOption = brandSelect.find('option:selected');
                    brand = selectedOption.data('merk');
                }
                
                if (type === 'jenis_unit') {
                    const deptSelect = $('#unit_departemen');
                    departemen = deptSelect.val();
                }
                
                // Open modal
                QuickAddModal.open(type, selectId, brand, departemen);
            }
        });
    },
    
    attachEvents() {
        // Save button click
        document.getElementById('btnSaveQuickAdd').addEventListener('click', () => {
            this.saveData();
        });
        
        // Form submit
        document.getElementById('quickAddForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveData();
        });
        
        // Reset on modal close
        document.getElementById('quickAddModal').addEventListener('hidden.bs.modal', () => {
            this.reset();
        });
    },
    
    open(type, target, brand = null, departemen = null) {
        this.currentType = type;
        this.currentTarget = target;
        this.currentBrand = brand;
        this.currentDepartemen = departemen;
        
        // Load form configuration
        this.loadFormConfig();
    },
    
    loadFormConfig() {
        const btn = document.getElementById('btnSaveQuickAdd');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Loading...';
        
        let url = '<?= base_url('purchasing/getQuickAddForm') ?>?type=' + this.currentType;
        
        if (this.currentBrand) {
            url += '&brand=' + encodeURIComponent(this.currentBrand);
        }
        if (this.currentDepartemen) {
            url += '&departemen=' + encodeURIComponent(this.currentDepartemen);
        }
        
        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    this.renderForm(response.config, response.additionalData);
                    this.modal.show();
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-save me-1"></i> Save';
                } else {
                    OptimaNotify.error(response.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-save me-1"></i> Save';
                }
            },
            error: (xhr) => {
                OptimaNotify.error('Failed to load form');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save me-1"></i> Save';
            }
        });
    },
    
    renderForm(config, additionalData) {
        document.getElementById('quick-add-title').textContent = 'Add ' + config.title;
        document.getElementById('quick-add-type').value = this.currentType;
        
        let formHtml = '';
        
        config.fields.forEach((field, index) => {
            if (field.type === 'hidden') {
                let value = '';
                if (field.name === 'merk_unit' && additionalData.current_brand) {
                    value = additionalData.current_brand;
                } else if (field.name === 'id_departemen' && additionalData.current_departemen) {
                    value = additionalData.current_departemen;
                }
                formHtml += `<input type="hidden" name="data[${field.name}]" value="${value}">`;
            } else {
                const required = field.required ? 'required' : '';
                const requiredMark = field.required ? '<span class="text-danger">*</span>' : '';
                
                formHtml += `<div class="mb-3">`;
                formHtml += `<label for="field-${field.name}" class="form-label">${field.label} ${requiredMark}</label>`;
                
                if (field.type === 'select') {
                    formHtml += `<select class="form-select" id="field-${field.name}" name="data[${field.name}]" ${required}>`;
                    formHtml += `<option value="">Select ${field.label}...</option>`;
                    
                    // Handle both array of strings and array of objects
                    if (field.options && Array.isArray(field.options)) {
                        field.options.forEach(option => {
                            if (typeof option === 'object' && option.value && option.label) {
                                // Array of objects: {value: 'x', label: 'Label'}
                                formHtml += `<option value="${option.value}">${option.label}</option>`;
                            } else {
                                // Array of strings: ['Option1', 'Option2']
                                formHtml += `<option value="${option}">${option}</option>`;
                            }
                        });
                    }
                    
                    formHtml += `</select>`;
                } else if (field.type === 'textarea') {
                    formHtml += `<textarea class="form-control" id="field-${field.name}" name="data[${field.name}]" rows="3" placeholder="${field.placeholder || ''}" ${required}></textarea>`;
                } else {
                    formHtml += `<input type="${field.type}" class="form-control" id="field-${field.name}" name="data[${field.name}]" placeholder="${field.placeholder || ''}" ${required}>`;
                }
                
                formHtml += `</div>`;
            }
        });
        
        document.getElementById('quick-add-form-fields').innerHTML = formHtml;
        
        // Focus first visible input
        setTimeout(() => {
            const firstInput = document.querySelector('#quick-add-form-fields input:not([type="hidden"]), #quick-add-form-fields select, #quick-add-form-fields textarea');
            if (firstInput) firstInput.focus();
        }, 300);
    },
    
    saveData() {
        const form = document.getElementById('quickAddForm');
        
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        const btn = document.getElementById('btnSaveQuickAdd');
        btn.disabled = true;
        btn.classList.add('loading');
        
        const formData = new FormData(form);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            if (key.startsWith('data[')) {
                const fieldName = key.match(/data\[(.+)\]/)[1];
                data[fieldName] = value;
            }
        }
        
        $.ajax({
            url: '<?= base_url('purchasing/quickAddMasterData') ?>',
            method: 'POST',
            data: {
                type: this.currentType,
                data: data
            },
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    OptimaNotify.success(response.message, 'Success!');
                    
                    this.modal.hide();
                    
                    // Refresh dropdown and select new item
                    this.refreshDropdown(response.data, response.id);
                } else {
                    OptimaNotify.error(response.message);
                }
                
                btn.disabled = false;
                btn.classList.remove('loading');
            },
            error: (xhr) => {
                OptimaNotify.error('An error occurred while saving data');
                btn.disabled = false;
                btn.classList.remove('loading');
            }
        });
    },
    
    refreshDropdown(newData, newId) {
        const targetSelect = document.getElementById(this.currentTarget);
        
        if (!targetSelect) return;
        
        // Get related dropdowns from response if available
        $.ajax({
            url: '<?= base_url('purchasing/refreshDropdownData') ?>',
            method: 'POST',
            data: {
                type: this.currentType,
                brand: this.currentBrand
            },
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    this.updateDropdownOptions(targetSelect, response.data, newId);
                    
                    // If there are related dropdowns, refresh them too
                    if (response.refresh_related && Array.isArray(response.refresh_related)) {
                        response.refresh_related.forEach(relatedId => {
                            if (relatedId !== this.currentTarget) {
                                const relatedSelect = document.getElementById(relatedId);
                                if (relatedSelect) {
                                    // Determine type from element
                                    const relatedType = relatedSelect.getAttribute('data-master-type');
                                    if (relatedType) {
                                        this.refreshRelatedDropdown(relatedSelect, relatedType, newId);
                                    }
                                }
                            }
                        });
                    }
                }
            }
        });
    },
    
    refreshRelatedDropdown(selectElement, type, selectedId) {
        $.ajax({
            url: '<?= base_url('purchasing/refreshDropdownData') ?>',
            method: 'POST',
            data: {
                type: type,
                brand: this.currentBrand
            },
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    this.updateDropdownOptions(selectElement, response.data, selectedId);
                }
            }
        });
    },
    
    updateDropdownOptions(selectElement, data, selectedId) {
        const currentValue = selectElement.value;
        const isSelect2 = $(selectElement).hasClass('select2-hidden-accessible');
        const elementId = selectElement.id;
        
        // Get master type
        const masterType = selectElement.getAttribute('data-master-type');
        
        // Clear all options
        selectElement.innerHTML = '';
        
        // Re-add standard options based on dropdown type
        if (masterType === 'brand' || elementId === 'unit_merk') {
            selectElement.add(new Option('Select Brand...', ''));
            const addNew = new Option('➕ Add New Brand', '__ADD_NEW__');
            addNew.className = 'text-primary fw-bold';
            addNew.style.backgroundColor = '#f0f8ff';
            selectElement.add(addNew);
            selectElement.add(new Option('─────────────', '', true, false)).disabled = true;
            
            // Add data options for brand
            data.forEach(item => {
                const option = new Option(item.merk_unit, item.id_model_unit);
                option.setAttribute('data-merk', item.merk_unit);
                selectElement.add(option);
            });
        } else if (masterType === 'model' || elementId === 'unit_model') {
            selectElement.add(new Option('Select Brand First...', ''));
            
            // Only add options if data exists (brand is selected)
            if (data && data.length > 0) {
                // Change first option text
                selectElement.options[0].text = 'Select Model...';
                
                const addNew = new Option('➕ Add New Model', '__ADD_NEW__');
                addNew.className = 'text-primary fw-bold';
                addNew.style.backgroundColor = '#f0f8ff';
                selectElement.add(addNew);
                selectElement.add(new Option('─────────────', '', true, false)).disabled = true;
                
                // Add data options for model
                data.forEach(item => {
                    const option = new Option(item.model_unit, item.id_model_unit);
                    selectElement.add(option);
                });
                
                // Enable dropdown
                selectElement.disabled = false;
            } else {
                selectElement.disabled = true;
            }
        } else {
            // Generic handling for other dropdowns
            selectElement.add(new Option('Select...', ''));
            
            data.forEach(item => {
                const displayValue = Object.values(item).find(v => typeof v === 'string') || '';
                const idValue = Object.values(item)[0];
                const option = new Option(displayValue, idValue);
                selectElement.add(option);
            });
        }
        
        // Set selected value
        selectElement.value = selectedId || currentValue;
        
        // Refresh Select2 if applicable
        if (isSelect2) {
            $(selectElement).trigger('change.select2');
        }
        
        // Trigger change event
        selectElement.dispatchEvent(new Event('change'));
    },
    
    reset() {
        document.getElementById('quickAddForm').reset();
        document.getElementById('quick-add-form-fields').innerHTML = '';
        this.currentType = null;
        this.currentTarget = null;
        this.currentBrand = null;
        this.currentDepartemen = null;
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    QuickAddModal.init();
});

// Refresh dropdown function
function refreshDropdown(selectId) {
    const selectElement = document.getElementById(selectId);
    const type = selectElement.getAttribute('data-master-type');
    
    if (!type) {
        OptimaNotify.error('Dropdown type not found');
        return;
    }
    
    if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.showLoading === 'function') {
        OptimaPro.showLoading('Refreshing...');
    }
    
    $.ajax({
        url: '<?= base_url('purchasing/refreshDropdownData') ?>',
        method: 'POST',
        data: { type: type },
        dataType: 'json',
        success: (response) => {
            if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.hideLoading === 'function') {
                OptimaPro.hideLoading();
            }
            if (response.success) {
                QuickAddModal.updateDropdownOptions(selectElement, response.data);
                OptimaNotify.success('Data successfully refreshed', 'Success!');
            } else {
                OptimaNotify.error(response.message);
            }
        },
        error: () => {
            if (typeof OptimaPro !== 'undefined' && typeof OptimaPro.hideLoading === 'function') {
                OptimaPro.hideLoading();
            }
            OptimaNotify.error('Failed to refresh data');
        }
    });
}
</script>
