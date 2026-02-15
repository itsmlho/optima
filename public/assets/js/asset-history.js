/**
 * Asset History & Timeline Visualization
 * Sprint 3: Advanced Features
 * 
 * Provides comprehensive view of:
 * - Contract timelines with all events
 * - Unit journey across contracts
 * - Renewal chains visualization
 * - Rate change history and trends
 */

class AssetHistoryManager {
    constructor() {
        this.contractData = {};
        this.unitData = {};
        this.renewalChainData = {};
        this.rateHistoryData = [];
        this.rateChart = null;
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadInitialData();
    }
    
    bindEvents() {
        // Contract Timeline View
        $('#historyContractId').on('change', () => this.loadContractTimeline());
        $('#contractEventFilter').on('change', () => this.filterContractEvents());
        
        // Unit Journey View
        $('#historyUnitId').on('change', () => this.loadUnitJourney());
        $('#unitStatusFilter').on('change', () => this.filterUnitJourney());
        
        // Renewal Chains View
        $('#renewalChainId').on('change', () => this.loadRenewalChain());
        $('#renewalDisplayMode').on('change', () => this.renderRenewalChain());
        
        // Rate History View
        $('#rateHistoryContractId, #rateHistoryUnitId, #rateHistoryDateRange').on('change', () => this.loadRateHistory());
        
        // Export
        $('#exportHistoryBtn').on('click', () => this.exportToPDF());
        
        // Tab switching
        $('#historyViewTabs button').on('shown.bs.tab', (e) => this.onTabSwitch(e.target.id));
    }
    
    async loadInitialData() {
        // Only load data if the asset history dropdowns exist on this page
        const hasContractDropdown = $('#historyContractId').length > 0 || 
                                   $('#rateHistoryContractId').length > 0 || 
                                   $('#renewalChainId').length > 0;
        const hasUnitDropdown = $('#historyUnitId').length > 0 || $('#rateHistoryUnitId').length > 0;
        
        if (!hasContractDropdown && !hasUnitDropdown) {
            console.log('Asset history dropdowns not found on this page, skipping data load');
            return;
        }
        
        const promises = [];
        if (hasContractDropdown) promises.push(this.loadContracts());
        if (hasUnitDropdown) promises.push(this.loadUnits());
        
        await Promise.all(promises);
    }
    
    async loadContracts() {
        try {
            const response = await fetch(`${BASE_URL}marketing/kontrak/getAllContracts`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            
            if (data.success && data.data) {
                const contracts = data.data;
                
                // Populate all contract dropdowns
                const dropdowns = ['#historyContractId', '#rateHistoryContractId', '#renewalChainId'];
                dropdowns.forEach(selector => {
                    const $select = $(selector);
                    contracts.forEach(contract => {
                        $select.append(`
                            <option value="${contract.id}">
                                ${contract.no_kontrak} - ${contract.customer_name} (${contract.status})
                            </option>
                        `);
                    });
                });
            }
        } catch (error) {
            console.error('Failed to load contracts:', error);
        }
    }
    
    async loadUnits() {
        try {
            const response = await fetch(`${BASE_URL}unit/getAllUnits`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            
            if (data.success && data.data) {
                const units = data.data;
                
                // Populate unit dropdowns
                const dropdowns = ['#historyUnitId', '#rateHistoryUnitId'];
                dropdowns.forEach(selector => {
                    const $select = $(selector);
                    units.forEach(unit => {
                        $select.append(`
                            <option value="${unit.id}">
                                ${unit.nomor_unit} - ${unit.tipe_unit || 'N/A'}
                            </option>
                        `);
                    });
                });
            }
        } catch (error) {
            console.error('Failed to load units:', error);
        }
    }
    
    /**
     * CONTRACT TIMELINE VIEW
     */
    async loadContractTimeline() {
        const contractId = $('#historyContractId').val();
        if (!contractId) {
            $('#contractTimeline').html(`
                <div class="text-center text-muted py-5">
                    <i class="fas fa-clock fa-3x mb-3"></i>
                    <p>Select a contract to view its timeline</p>
                </div>
            `);
            return;
        }
        
        try {
            const response = await fetch(`${BASE_URL}marketing/kontrak/getContractHistory/${contractId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            
            if (data.success) {
                this.contractData = data.data;
                this.renderContractTimeline();
            }
        } catch (error) {
            console.error('Failed to load contract timeline:', error);
        }
    }
    
    renderContractTimeline() {
        const events = this.contractData.events || [];
        const filter = $('#contractEventFilter').val();
        
        // Filter events
        let filteredEvents = events;
        if (filter !== 'all') {
            filteredEvents = events.filter(e => e.type === filter.slice(0, -1)); // Remove trailing 's'
        }
        
        if (filteredEvents.length === 0) {
            $('#contractTimeline').html(`
                <div class="text-center text-muted py-5">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>No events found for this contract</p>
                </div>
            `);
            return;
        }
        
        // Build timeline HTML
        let html = '<div class="timeline-container"><div class="timeline-line"></div>';
        
        filteredEvents.forEach(event => {
            const icon = this.getEventIcon(event.type);
            const cssClass = event.type;
            
            html += `
                <div class="timeline-event">
                    <div class="timeline-marker ${cssClass}">
                        <i class="fas ${icon}"></i>
                    </div>
                    <div class="timeline-content ${cssClass}" onclick="assetHistory.viewEventDetails(${event.id}, '${event.type}')">
                        <div class="timeline-date">${this.formatDate(event.date)}</div>
                        <div class="timeline-title">${this.getEventTitle(event)}</div>
                        <div class="timeline-description">${event.description || ''}</div>
                        <div class="timeline-meta">
                            ${this.renderEventMeta(event)}
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        $('#contractTimeline').html(html);
    }
    
    filterContractEvents() {
        this.renderContractTimeline();
    }
    
    getEventIcon(type) {
        const icons = {
            'contract': 'fa-file-contract',
            'amendment': 'fa-edit',
            'renewal': 'fa-sync-alt',
            'unit': 'fa-truck',
            'rate': 'fa-dollar-sign'
        };
        return icons[type] || 'fa-circle';
    }
    
    getEventTitle(event) {
        const titles = {
            'contract': `Contract Created: ${event.contract_number}`,
            'amendment': `Amendment: ${event.reason || 'Rate Change'}`,
            'renewal': `Renewal Contract Created`,
            'unit': `Unit Change: ${event.action}`,
            'rate': `Rate Adjustment`
        };
        return titles[event.type] || 'Event';
    }
    
    renderEventMeta(event) {
        let meta = '';
        
        if (event.type === 'amendment' && event.prorate) {
            meta += `
                <div class="timeline-meta-item text-warning">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Prorate Split: ${event.prorate.days_before}d + ${event.prorate.days_after}d</span>
                </div>
            `;
        }
        
        if (event.units_count) {
            meta += `
                <div class="timeline-meta-item text-info">
                    <i class="fas fa-truck"></i>
                    <span>${event.units_count} unit(s)</span>
                </div>
            `;
        }
        
        if (event.total_value) {
            meta += `
                <div class="timeline-meta-item text-success">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>${this.formatCurrency(event.total_value)}</span>
                </div>
            `;
        }
        
        if (event.created_by) {
            meta += `
                <div class="timeline-meta-item text-secondary">
                    <i class="fas fa-user"></i>
                    <span>${event.created_by}</span>
                </div>
            `;
        }
        
        return meta;
    }
    
    /**
     * UNIT JOURNEY VIEW
     */
    async loadUnitJourney() {
        const unitId = $('#historyUnitId').val();
        if (!unitId) {
            $('#unitJourneyTimeline').html(`
                <div class="text-center text-muted py-5">
                    <i class="fas fa-route fa-3x mb-3"></i>
                    <p>Select a unit to view its journey across contracts</p>
                </div>
            `);
            return;
        }
        
        try {
            const response = await fetch(`${BASE_URL}unit/getUnitJourney/${unitId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            
            if (data.success) {
                this.unitData = data.data;
                this.renderUnitJourney();
            }
        } catch (error) {
            console.error('Failed to load unit journey:', error);
        }
    }
    
    renderUnitJourney() {
        const contracts = this.unitData.contracts || [];
        const filter = $('#unitStatusFilter').val();
        
        let filteredContracts = contracts;
        if (filter !== 'all') {
            filteredContracts = contracts.filter(c => {
                if (filter === 'active') return c.status === 'ACTIVE';
                if (filter === 'completed') return c.status === 'COMPLETED';
                return true;
            });
        }
        
        if (filteredContracts.length === 0) {
            $('#unitJourneyTimeline').html(`
                <div class="text-center text-muted py-5">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>No contracts found for this unit</p>
                </div>
            `);
            return;
        }
        
        let html = '<div class="unit-journey-container">';
        
        filteredContracts.forEach((contract, index) => {
            const isActive = contract.status === 'ACTIVE';
            const duration = this.calculateDuration(contract.start_date, contract.end_date);
            const totalRevenue = contract.monthly_rate * duration;
            
            html += `
                <div class="unit-journey-card ${isActive ? 'active' : ''}">
                    <div class="unit-journey-header">
                        <div>
                            <h6 class="mb-1">${contract.no_kontrak}</h6>
                            <small class="text-muted">${contract.customer_name}</small>
                        </div>
                        <span class="badge bg-${this.getStatusColor(contract.status)}">${contract.status}</span>
                    </div>
                    
                    <div class="unit-journey-stats">
                        <div>
                            <small class="text-muted d-block">Period</small>
                            <strong>${this.formatDate(contract.start_date)} - ${this.formatDate(contract.end_date)}</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Monthly Rate</small>
                            <strong>${this.formatCurrency(contract.monthly_rate)}</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Duration</small>
                            <strong>${duration} months</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Total Revenue</small>
                            <strong class="text-success">${this.formatCurrency(totalRevenue)}</strong>
                        </div>
                    </div>
                    
                    ${contract.amendments && contract.amendments.length > 0 ? `
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-edit"></i> ${contract.amendments.length} amendment(s)
                            </small>
                        </div>
                    ` : ''}
                </div>
            `;
        });
        
        html += '</div>';
        
        // Add summary
        const totalContracts = filteredContracts.length;
        const totalRevenue = filteredContracts.reduce((sum, c) => {
            const duration = this.calculateDuration(c.start_date, c.end_date);
            return sum + (c.monthly_rate * duration);
        }, 0);
        
        html += `
            <div class="card bg-light mt-3">
                <div class="card-body">
                    <h6>Unit Journey Summary</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <small class="text-muted d-block">Total Contracts</small>
                            <strong>${totalContracts}</strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Total Revenue Generated</small>
                            <strong class="text-success">${this.formatCurrency(totalRevenue)}</strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Average Monthly Rate</small>
                            <strong>${this.formatCurrency(totalRevenue / (totalContracts || 1))}</strong>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#unitJourneyTimeline').html(html);
    }
    
    filterUnitJourney() {
        this.renderUnitJourney();
    }
    
    /**
     * RENEWAL CHAINS VIEW
     */
    async loadRenewalChain() {
        const contractId = $('#renewalChainId').val();
        if (!contractId) {
            $('#renewalChainVisualization').html(`
                <div class="text-center text-muted py-5">
                    <i class="fas fa-project-diagram fa-3x mb-3"></i>
                    <p>Select a contract to view its renewal chain</p>
                </div>
            `);
            return;
        }
        
        try {
            const response = await fetch(`${BASE_URL}marketing/kontrak/getRenewalChain/${contractId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            
            if (data.success) {
                this.renewalChainData = data.data;
                this.renderRenewalChain();
            }
        } catch (error) {
            console.error('Failed to load renewal chain:', error);
        }
    }
    
    renderRenewalChain() {
        const mode = $('#renewalDisplayMode').val();
        const chain = this.renewalChainData.chain || [];
        
        if (chain.length === 0) {
            $('#renewalChainVisualization').html(`
                <div class="text-center text-muted py-5">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>No renewal chain found</p>
                </div>
            `);
            return;
        }
        
        let html = '';
        
        if (mode === 'tree') {
            html = '<div class="renewal-tree">';
            chain.forEach(contract => {
                const generation = contract.renewal_generation || 0;
                html += `
                    <div class="renewal-node generation-${generation}">
                        ${generation > 0 ? '<div class="renewal-connector"></div>' : ''}
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">
                                    ${generation === 0 ? '<i class="fas fa-star text-warning"></i>' : ''}
                                    ${contract.no_kontrak}
                                    <span class="badge bg-secondary ms-2">Gen ${generation}</span>
                                </h6>
                                <small class="text-muted">${contract.customer_name}</small>
                            </div>
                            <span class="badge bg-${this.getStatusColor(contract.status)}">${contract.status}</span>
                        </div>
                        <div class="mt-2">
                            <small>
                                <i class="fas fa-calendar"></i> ${this.formatDate(contract.start_date)} - ${this.formatDate(contract.end_date)}
                                &nbsp;|&nbsp;
                                <i class="fas fa-truck"></i> ${contract.units_count} unit(s)
                                &nbsp;|&nbsp;
                                <i class="fas fa-money-bill-wave"></i> ${this.formatCurrency(contract.total_value)}
                            </small>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            
        } else if (mode === 'timeline') {
            // Timeline view
            html = '<div class="timeline-container"><div class="timeline-line"></div>';
            chain.forEach(contract => {
                html += `
                    <div class="timeline-event">
                        <div class="timeline-marker renewal">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                        <div class="timeline-content renewal">
                            <div class="timeline-date">${this.formatDate(contract.start_date)}</div>
                            <div class="timeline-title">${contract.no_kontrak}</div>
                            <div class="timeline-meta">
                                <div class="timeline-meta-item">
                                    <span class="badge bg-secondary">Generation ${contract.renewal_generation || 0}</span>
                                </div>
                                <div class="timeline-meta-item">
                                    <i class="fas fa-truck"></i> ${contract.units_count} unit(s)
                                </div>
                                <div class="timeline-meta-item">
                                    <i class="fas fa-money-bill-wave"></i> ${this.formatCurrency(contract.total_value)}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            
        } else if (mode === 'comparison') {
            // Comparison table
            html = `
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Generation</th>
                            <th>Contract Number</th>
                            <th>Period</th>
                            <th>Units</th>
                            <th>Value</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            chain.forEach(contract => {
                html += `
                    <tr>
                        <td><span class="badge bg-secondary">Gen ${contract.renewal_generation || 0}</span></td>
                        <td>${contract.no_kontrak}</td>
                        <td>${this.formatDate(contract.start_date)} - ${this.formatDate(contract.end_date)}</td>
                        <td>${contract.units_count}</td>
                        <td>${this.formatCurrency(contract.total_value)}</td>
                        <td><span class="badge bg-${this.getStatusColor(contract.status)}">${contract.status}</span></td>
                    </tr>
                `;
            });
            
            html += '</tbody></table>';
        }
        
        $('#renewalChainVisualization').html(html);
    }
    
    /**
     * RATE HISTORY VIEW
     */
    async loadRateHistory() {
        const contractId = $('#rateHistoryContractId').val();
        const unitId = $('#rateHistoryUnitId').val();
        const dateRange = $('#rateHistoryDateRange').val();
        
        try {
            const params = new URLSearchParams();
            if (contractId) params.append('contract_id', contractId);
            if (unitId) params.append('unit_id', unitId);
            if (dateRange !== 'all') params.append('days', dateRange);
            
            const response = await fetch(`${BASE_URL}marketing/kontrak/getRateHistory?${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            
            if (data.success) {
                this.rateHistoryData = data.data;
                this.renderRateHistory();
            }
        } catch (error) {
            console.error('Failed to load rate history:', error);
        }
    }
    
    renderRateHistory() {
        const history = this.rateHistoryData || [];
        
        // Render chart
        this.renderRateChart(history);
        
        // Render table
        const tbody = $('#rateHistoryTable tbody');
        tbody.empty();
        
        history.forEach(entry => {
            const change = entry.new_rate - entry.old_rate;
            const changePercent = entry.old_rate > 0 ? ((change / entry.old_rate) * 100).toFixed(2) : 0;
            const changeClass = change > 0 ? 'text-success' : (change < 0 ? 'text-danger' : 'text-secondary');
            const changeIcon = change > 0 ? 'fa-arrow-up' : (change < 0 ? 'fa-arrow-down' : 'fa-minus');
            
            tbody.append(`
                <tr>
                    <td>${this.formatDate(entry.date)}</td>
                    <td><span class="badge bg-info">${entry.event_type}</span></td>
                    <td>${entry.contract_number}</td>
                    <td>${entry.unit_number}</td>
                    <td>${this.formatCurrency(entry.old_rate)}</td>
                    <td>${this.formatCurrency(entry.new_rate)}</td>
                    <td class="${changeClass}">
                        <i class="fas ${changeIcon}"></i>
                        ${this.formatCurrency(Math.abs(change))} (${changePercent}%)
                    </td>
                    <td><small>${entry.reason || '-'}</small></td>
                </tr>
            `);
        });
    }
    
    renderRateChart(history) {
        const canvas = document.getElementById('rateHistoryChart');
        const ctx = canvas.getContext('2d');
        
        // Destroy existing chart
        if (this.rateChart) {
            this.rateChart.destroy();
        }
        
        // Prepare data
        const labels = history.map(h => this.formatDate(h.date));
        const oldRates = history.map(h => h.old_rate);
        const newRates = history.map(h => h.new_rate);
        
        // Create chart
        this.rateChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Old Rate',
                        data: oldRates,
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'New Rate',
                        data: newRates,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }
    
    /**
     * UTILITY FUNCTIONS
     */
    viewEventDetails(eventId, eventType) {
        // TODO: Show detailed modal for event
        console.log('View event details:', eventId, eventType);
    }
    
    onTabSwitch(tabId) {
        if (tabId === 'rateView-tab' && this.rateHistoryData.length > 0) {
            // Re-render chart when switching to rate view
            setTimeout(() => this.renderRateChart(this.rateHistoryData), 100);
        }
    }
    
    exportToPDF() {
        alert('Export to PDF feature coming soon!');
        // TODO: Implement PDF export
    }
    
    formatDate(date) {
        if (!date) return '-';
        const d = new Date(date);
        return d.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
    }
    
    formatCurrency(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(amount));
    }
    
    getStatusColor(status) {
        const colors = {
            'ACTIVE': 'success',
            'DRAFT': 'secondary',
            'COMPLETED': 'info',
            'CANCELLED': 'danger'
        };
        return colors[status] || 'secondary';
    }
    
    calculateDuration(startDate, endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const months = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth());
        return months;
    }
}

// Initialize
let assetHistory;
$(document).ready(function() {
    assetHistory = new AssetHistoryManager();
});

// Function to open modal
function openAssetHistory(contractId = null, unitId = null) {
    $('#assetHistoryModal').modal('show');
    
    if (contractId) {
        $('#historyContractId').val(contractId).trigger('change');
    }
    
    if (unitId) {
        $('#historyUnitId').val(unitId).trigger('change');
        $('#unitView-tab').tab('show');
    }
}
