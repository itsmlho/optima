// @ts-nocheck
/**
 * Multi-select Dropdown with Search for SPK Mechanic Selection
 * Features: 
 * - Role-based filtering (MECHANIC_UNIT_PREP, MECHANIC_FABRICATION, FOREMAN, HELPER)
 * - Multi-selection with max limits (2 mechanics + 2 helpers, except PDI: 2 foremen only)
 * - Search functionality
 * - Visual role indicators
 * - Validation feedback
 */

// Prevent redefinition if class already exists
if (typeof window.SPKMechanicMultiSelect === 'undefined') {
    
window.SPKMechanicMultiSelect = class SPKMechanicMultiSelect {
    constructor(containerId, options = {}) {
        this.containerId = containerId;
        this.container = document.getElementById(containerId);
        this.options = {
            stage: options.stage || 'persiapan_unit', // persiapan_unit, fabrikasi, painting, pdi
            maxMechanics: options.maxMechanics || 2,
            maxHelpers: options.maxHelpers || 2,
            allowedRoles: options.allowedRoles || this.getDefaultRoles(options.stage),
            placeholder: options.placeholder || 'Select mechanics and helpers...',
            searchPlaceholder: options.searchPlaceholder || 'Search by name...',
            ...options
        };
        
        this.selectedItems = new Map(); // {employeeId: {id, name, role, isPrimary}}
        this.allEmployees = [];
        this.filteredEmployees = [];
        this.isOpen = false;
        
        this.init();
    }
    
    getDefaultRoles(stage) {
        const roleMap = {
            'persiapan_unit': ['MECHANIC_UNIT_PREP', 'HELPER'],
            'fabrikasi': ['MECHANIC_FABRICATION', 'HELPER'],  
            'painting': ['MECHANIC_UNIT_PREP', 'MECHANIC_FABRICATION', 'MECHANIC_SERVICE_AREA', 'HELPER'],
            'pdi': ['FOREMAN', 'SUPERVISOR']
        };
        return roleMap[stage] || [];
    }
    
    async init() {
        console.log('🔄 Initializing SPKMechanicMultiSelect for:', this.containerId);
        await this.loadEmployees();
        this.render();
        this.bindEvents();
        console.log('✅ SPKMechanicMultiSelect initialized successfully');
    }
    
    async loadEmployees() {
        try {
            // Use relative URL to avoid PHP template issues
            const response = await fetch(`/optima/public/service/employees/by-roles?roles=${this.options.allowedRoles.join(',')}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',  // Important for CodeIgniter's isAJAX() check
                    'Accept': 'application/json'
                }
            });
            
            // API Response received
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            // API data processed
            
            if (data.success) {
                this.allEmployees = data.data.map(emp => ({
                    id: emp.id,
                    name: emp.staff_name,
                    role: emp.staff_role,
                    roleLabel: this.getRoleLabel(emp.staff_role),
                    roleColor: this.getRoleColor(emp.staff_role)
                }));
                this.filteredEmployees = [...this.allEmployees];
                console.log(`✅ Loaded ${this.allEmployees.length} employees for roles: ${this.options.allowedRoles.join(', ')}`);
            } else {
                console.error('❌ API Error:', data.message);
                throw new Error(data.message || 'Failed to load employees');
            }
        } catch (error) {
            console.error('❌ Error loading employees:', error);
            // Show user-friendly error
            this.showLoadingError(error.message);
        }
    }
    
    getRoleLabel(role) {
        const labels = {
            'MECHANIC_UNIT_PREP': 'Unit Prep',
            'MECHANIC_FABRICATION': 'Fabrication', 
            'MECHANIC_SERVICE_AREA': 'Service Area',
            'FOREMAN': 'Foreman',
            'SUPERVISOR': 'Supervisor',
            'HELPER': 'Helper'
        };
        return labels[role] || role;
    }
    
    getRoleColor(role) {
        const colors = {
            'MECHANIC_UNIT_PREP': 'primary',
            'MECHANIC_FABRICATION': 'info', 
            'MECHANIC_SERVICE_AREA': 'warning',
            'FOREMAN': 'success',
            'SUPERVISOR': 'success',
            'HELPER': 'secondary'
        };
        return colors[role] || 'light';
    }
    
    render() {
        // Rendering component
        
        // Clear any existing content first
        this.container.innerHTML = '';
        
        const html = `
            <div class="spk-multi-select" data-stage="${this.options.stage}">
                <label class="form-label">
                    ${this.getStageLabel()} 
                    <small class="text-muted">
                        ${this.getMaxLimitsLabel()}
                    </small>
                </label>
                
                <div class="multi-select-container">
                    <!-- Selected items display -->
                    <div class="selected-items-container">
                        <div class="selected-items" id="${this.containerId}_selected">
                            <!-- Selected items will be rendered here -->
                        </div>
                        <div class="search-input-container">
                            <input type="text" 
                                   class="form-control search-input" 
                                   placeholder="${this.options.placeholder}"
                                   id="${this.containerId}_search">
                            <i class="fas fa-search search-icon"></i>
                            <i class="fas fa-chevron-down dropdown-icon"></i>
                        </div>
                    </div>
                    
                    <!-- Dropdown options -->
                    <div class="dropdown-options" id="${this.containerId}_dropdown" style="display: none;">
                        <div class="options-container">
                            <!-- Options will be rendered here -->
                        </div>
                    </div>
                </div>
                
                <!-- Validation feedback -->
                <div class="validation-feedback" id="${this.containerId}_feedback"></div>
                
                <!-- Hidden inputs for form submission -->
                <div id="${this.containerId}_hidden_inputs"></div>
            </div>
        `;
        
        this.container.innerHTML = html;
        this.updateSelectedDisplay();
        this.updateDropdownOptions();
    }
    
    getStageLabel() {
        const labels = {
            'persiapan_unit': 'Unit Preparation Team',
            'fabrikasi': 'Fabrication Team',
            'painting': 'Painting Team', 
            'pdi': 'PDI Foreman Team'
        };
        return labels[this.options.stage] || 'Team Selection';
    }
    
    getMaxLimitsLabel() {
        if (this.options.stage === 'pdi') {
            return `(Max ${this.options.maxMechanics} foremen)`;
        } else {
            return `(Max ${this.options.maxMechanics} mechanics, ${this.options.maxHelpers} helpers)`;
        }
    }
    
    updateSelectedDisplay() {
        const selectedContainer = document.getElementById(`${this.containerId}_selected`);
        
        let html = '';
        this.selectedItems.forEach((item, employeeId) => {
            const primaryLabel = item.isPrimary ? '<i class="fas fa-star text-warning" title="Primary"></i>' : '';
            html += `
                <span class="selected-item badge badge-soft-${item.roleColor} me-1 mb-1" data-id="${employeeId}" style="font-size:0.85rem;padding:0.5rem 0.75rem;">
                    ${primaryLabel} ${item.name} <span class="text-muted small">(${item.roleLabel})</span>
                    <i class="fas fa-times ms-1 remove-item" data-id="${employeeId}" style="cursor:pointer;"></i>
                </span>
            `;
        });
        
        selectedContainer.innerHTML = html;
        this.updateHiddenInputs();
        this.validateSelection();
    }
    
    updateDropdownOptions() {
        const dropdown = document.getElementById(`${this.containerId}_dropdown`);
        const container = dropdown.querySelector('.options-container');
        
        let html = '';
        
        if (this.filteredEmployees.length === 0) {
            html = '<div class="no-options">No employees found</div>';
        } else {
            // Group by role for better organization
            const groupedEmployees = this.groupEmployeesByRole(this.filteredEmployees);
            
            Object.keys(groupedEmployees).forEach(role => {
                const roleEmployees = groupedEmployees[role];
                html += `
                    <div class="role-group">
                        <div class="role-header">${this.getRoleLabel(role)}</div>
                        ${roleEmployees.map(emp => `
                            <div class="option-item ${this.selectedItems.has(parseInt(emp.id)) ? 'selected' : ''}" 
                                 data-id="${emp.id}" 
                                 data-role="${emp.role}">
                                <div class="employee-info">
                                    <span class="employee-name">${emp.name}</span>
                                    <span class="badge badge-soft-${emp.roleColor}" style="font-size:0.7rem;">${emp.roleLabel}</span>
                                </div>
                                ${this.selectedItems.has(parseInt(emp.id)) ? '<i class="fas fa-check text-success"></i>' : ''}
                            </div>
                        `).join('')}
                    </div>
                `;
            });
        }
        
        container.innerHTML = html;
    }
    
    groupEmployeesByRole(employees) {
        return employees.reduce((groups, emp) => {
            if (!groups[emp.role]) groups[emp.role] = [];
            groups[emp.role].push(emp);
            return groups;
        }, {});
    }
    
    bindEvents() {
        const searchInput = document.getElementById(`${this.containerId}_search`);
        const dropdown = document.getElementById(`${this.containerId}_dropdown`);
        const container = this.container;
        
        // Binding events
        
        if (!searchInput || !dropdown || !container) {
            console.error('❌ Required elements not found for event binding');
            return;
        }
        
        // Search input events
        searchInput.addEventListener('focus', () => {
            // Search input focused
            this.openDropdown();
        });
        searchInput.addEventListener('input', (e) => this.handleSearch(e.target.value));
        
        // Dropdown toggle on search container click
        const searchContainer = container.querySelector('.search-input-container');
        if (searchContainer) {
            searchContainer.addEventListener('click', (e) => {
                // Container clicked
                this.isOpen ? this.closeDropdown() : this.openDropdown();
            });
        }
        
        // Option selection - use event delegation
        dropdown.addEventListener('click', (e) => {
            e.stopPropagation();
            // Dropdown clicked
            
            const optionItem = e.target.closest('.option-item');
            if (optionItem) {
                console.log('✅ Option item clicked:', optionItem);
                const employeeId = parseInt(optionItem.dataset.id);
                const role = optionItem.dataset.role;
                
                console.log('👤 Selecting employee:', { employeeId, role });
                this.toggleSelection(employeeId, role);
            }
        });
        
        // Remove selected items
        container.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-item')) {
                // Remove button clicked
                const employeeId = parseInt(e.target.dataset.id);
                this.removeSelection(employeeId);
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!container.contains(e.target)) {
                this.closeDropdown();
            }
        });
    }
    
    handleSearch(query) {
        const lowercaseQuery = query.toLowerCase();
        this.filteredEmployees = this.allEmployees.filter(emp => 
            emp.name.toLowerCase().includes(lowercaseQuery) ||
            emp.roleLabel.toLowerCase().includes(lowercaseQuery)
        );
        this.updateDropdownOptions();
    }
    
    toggleSelection(employeeId, role) {
        // Toggling selection
        
        if (this.selectedItems.has(employeeId)) {
            // Removing selection
            this.removeSelection(employeeId);
        } else {
            // Adding selection
            this.addSelection(employeeId, role);
        }
    }
    
    addSelection(employeeId, role) {
        // Adding selection
        // Convert to number to ensure consistent comparison
        const numEmployeeId = parseInt(employeeId);
        const employee = this.allEmployees.find(emp => parseInt(emp.id) === numEmployeeId);
        
        // Searching for employee
        
        if (!employee) {
            console.error('❌ Employee not found:', numEmployeeId);
            // Available employees listed
            return;
        }
        
        // Check limits
        const validation = this.validateNewSelection(employee);
        if (!validation.valid) {
            console.warn('⚠️ Selection validation failed:', validation.message);
            this.showValidationError(validation.message);
            return;
        }
        
        // Determine if this should be primary
        const currentMechanics = Array.from(this.selectedItems.values()).filter(item => item.role !== 'HELPER');
        const isPrimary = role !== 'HELPER' && currentMechanics.length === 0;
        
        // Adding employee to selection
        this.selectedItems.set(numEmployeeId, {
            id: employee.id,
            name: employee.name,
            role: employee.role,
            roleLabel: employee.roleLabel,
            roleColor: employee.roleColor,
            isPrimary: isPrimary
        });
        
        this.updateSelectedDisplay();
        this.updateDropdownOptions();
        this.clearValidationError();
        // Selection updated
    }
    
    removeSelection(employeeId) {
        const removedItem = this.selectedItems.get(employeeId);
        this.selectedItems.delete(employeeId);
        
        // If we removed the primary, reassign primary to first mechanic
        if (removedItem && removedItem.isPrimary) {
            const firstMechanic = Array.from(this.selectedItems.values()).find(item => item.role !== 'HELPER');
            if (firstMechanic) {
                firstMechanic.isPrimary = true;
                this.selectedItems.set(firstMechanic.id, firstMechanic);
            }
        }
        
        this.updateSelectedDisplay();
        this.updateDropdownOptions();
        this.clearValidationError();
    }
    
    validateNewSelection(employee) {
        const currentMechanics = Array.from(this.selectedItems.values()).filter(item => item.role !== 'HELPER');
        const currentHelpers = Array.from(this.selectedItems.values()).filter(item => item.role === 'HELPER');
        
        if (employee.role === 'HELPER') {
            if (currentHelpers.length >= this.options.maxHelpers) {
                return {
                    valid: false,
                    message: `Maximum ${this.options.maxHelpers} helpers allowed`
                };
            }
        } else {
            if (currentMechanics.length >= this.options.maxMechanics) {
                return {
                    valid: false,
                    message: `Maximum ${this.options.maxMechanics} mechanics allowed`
                };
            }
        }
        
        return { valid: true };
    }
    
    validateSelection() {
        const mechanics = Array.from(this.selectedItems.values()).filter(item => item.role !== 'HELPER');
        const helpers = Array.from(this.selectedItems.values()).filter(item => item.role === 'HELPER');
        
        let isValid = true;
        let message = '';
        
        // If no employees are loaded yet, don't show as invalid
        if (this.allEmployees.length === 0) {
            message = 'Loading employees...';
            isValid = true; // Don't mark as invalid while loading
        } else if (mechanics.length === 0) {
            isValid = false;
            const requiredLabel = this.options.stage === 'pdi' ? 'foreman' : 'mechanic';
            message = `At least one ${requiredLabel} must be selected`;
        }
        
        const feedback = document.getElementById(`${this.containerId}_feedback`);
        feedback.className = `validation-feedback ${isValid ? 'valid' : 'invalid'}`;
        feedback.textContent = message;
        
        return isValid;
    }
    
    showLoadingError(message) {
        const feedback = document.getElementById(`${this.containerId}_feedback`);
        feedback.className = 'validation-feedback error';
        feedback.textContent = `Error loading employees: ${message}`;
    }
    
    showValidationError(message) {
        const feedback = document.getElementById(`${this.containerId}_feedback`);
        feedback.className = 'validation-feedback invalid';
        feedback.textContent = message;
        
        setTimeout(() => this.clearValidationError(), 3000);
    }
    
    clearValidationError() {
        const feedback = document.getElementById(`${this.containerId}_feedback`);
        if (feedback.classList.contains('invalid')) {
            feedback.textContent = '';
            feedback.className = 'validation-feedback';
        }
    }
    
    updateHiddenInputs() {
        const hiddenContainer = document.getElementById(`${this.containerId}_hidden_inputs`);
        
        let html = '';
        this.selectedItems.forEach((item, employeeId) => {
            html += `
                <input type="hidden" name="mechanics[${employeeId}][id]" value="${item.id}">
                <input type="hidden" name="mechanics[${employeeId}][role]" value="${item.role}">
                <input type="hidden" name="mechanics[${employeeId}][is_primary]" value="${item.isPrimary ? 1 : 0}">
            `;
        });
        
        // Add summary inputs for easy backend processing
        const mechanicIds = Array.from(this.selectedItems.keys());
        const primaryMechanic = Array.from(this.selectedItems.values()).find(item => item.isPrimary);
        
        html += `
            <input type="hidden" name="selected_mechanic_ids" value="${mechanicIds.join(',')}">
            <input type="hidden" name="primary_mechanic_id" value="${primaryMechanic ? primaryMechanic.id : ''}">
            <input type="hidden" name="mechanics_count" value="${this.selectedItems.size}">
        `;
        
        hiddenContainer.innerHTML = html;
    }
    
    openDropdown() {
        const dropdown = document.getElementById(`${this.containerId}_dropdown`);
        dropdown.style.display = 'block';
        this.isOpen = true;
        this.container.classList.add('open');
    }
    
    closeDropdown() {
        const dropdown = document.getElementById(`${this.containerId}_dropdown`);
        dropdown.style.display = 'none';
        this.isOpen = false;
        this.container.classList.remove('open');
    }
    
    // Public methods
    getSelectedEmployees() {
        return Array.from(this.selectedItems.values());
    }
    
    setSelectedEmployees(employees) {
        this.selectedItems.clear();
        employees.forEach((emp, index) => {
            this.selectedItems.set(emp.id, {
                ...emp,
                isPrimary: emp.isPrimary || index === 0
            });
        });
        this.updateSelectedDisplay();
        this.updateDropdownOptions();
    }
    
    isValid() {
        return this.validateSelection();
    }
    
    reset() {
        this.selectedItems.clear();
        this.updateSelectedDisplay();
        this.updateDropdownOptions();
        this.clearValidationError();
    }
}

// CSS styles for the multi-select component (simplified)
const multiSelectStyles = `
<style>
.spk-multi-select {
    margin-bottom: 15px;
}

.multi-select-container {
    position: relative;
}

.selected-items-container {
    border: 1px solid #ddd;
    border-radius: 4px;
    min-height: 32px;
    padding: 4px 6px;
    background: #fff;
}

.selected-items {
    display: flex;
    flex-wrap: wrap;
    gap: 3px;
    margin-bottom: 3px;
}

.selected-item {
    font-size: 11px !important;
    padding: 2px 6px !important;
    line-height: 1.2;
    white-space: nowrap;
}

.selected-item .remove-item {
    cursor: pointer;
    margin-left: 3px;
    font-size: 10px;
    opacity: 0.8;
}

.selected-item .remove-item:hover {
    opacity: 1;
}

.search-input-container {
    display: flex;
    align-items: center;
}

.search-input {
    border: none;
    outline: none;
    flex: 1;
    padding: 4px 0;
}

.dropdown-options {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    max-height: 250px;
    overflow-y: auto;
    z-index: 1000;
}

.role-header {
    padding: 8px 12px 4px;
    font-size: 11px;
    font-weight: bold;
    color: #666;
    background: #f5f5f5;
    text-transform: uppercase;
}

.option-item {
    padding: 8px 12px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.option-item:hover {
    background: #f0f0f0;
}

.option-item.selected {
    background: #e3f2fd;
    color: #1976d2;
}

.employee-info {
    display: flex;
    align-items: center;
    gap: 8px;
}

.badge-sm {
    font-size: 11px;
}

.no-options {
    padding: 16px;
    text-align: center;
    color: #999;
}

.validation-feedback {
    font-size: 13px;
    margin-top: 4px;
}

.validation-feedback.invalid {
    color: #dc3545;
}

.validation-feedback.error {
    color: #dc3545;
    font-weight: 500;
}
</style>
`;

// Inject CSS
if (!document.getElementById('spk-multiselect-styles')) {
    const styleElement = document.createElement('div');
    styleElement.id = 'spk-multiselect-styles';
    styleElement.innerHTML = multiSelectStyles;
    document.head.appendChild(styleElement);
}

} // End of class existence check wrapper