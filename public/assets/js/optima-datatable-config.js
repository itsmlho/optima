/**
 * OPTIMA DataTable Global Configuration
 * Centralized settings untuk SEMUA DataTables di aplikasi OPTIMA
 * 
 * Usage:
 *   OptimaDataTable.init('#myTable', { ajax: '/api/data' });
 * 
 * Features:
 * - Standardized DataTable initialization
 * - Built-in filter helpers (status, date range, category)
 * - Consistent Bahasa Indonesia language
 * - Automatic tooltip re-initialization after table redraw
 * - Export helpers (Excel, PDF, Print)
 * 
 * Version: 1.0.0
 * Date: February 10, 2026
 */

window.OptimaDataTable = (function() {
    'use strict';

    console.log('🔧 [OPTIMA] optima-datatable-config.js loaded at', new Date().toLocaleTimeString());

    // ============================================
    // CSRF HELPER FUNCTION
    // ============================================
    
    /**
     * Get CSRF token from cookie
     * @returns {string|null} CSRF token value or null if not found
     */
    function getCsrfToken() {
        console.log('🍪 [CSRF] getCsrfToken() called');
        const name = 'csrf_cookie_name=';
        const decodedCookie = decodeURIComponent(document.cookie);
        const ca = decodedCookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) === 0) {
                const token = c.substring(name.length, c.length);
                console.log('✅ [CSRF] Token found in cookie:', token.substring(0, 10) + '...');
                return token;
            }
        }
        console.warn('⚠️ [CSRF] Token not found in cookie');
        console.log('🍪 [CSRF] All cookies:', document.cookie);
        return null;
    }

    // ============================================
    // DEFAULT CONFIGURATION
    // ============================================
    
    const defaultConfig = {
        // Pagination Settings
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        
        // Search Settings
        searchDelay: 400,  // 400ms debounce
        
        // Ordering (Sorting) Settings
        ordering: true,
        order: [[0, 'desc']],  // Default sort first column DESC
        
        // Processing & Loading
        processing: true,
        deferRender: true,
        
        // Server-side Processing (recommended for large datasets)
        serverSide: true,
        
        // AJAX Timeout Configuration (30 seconds)
        ajax: {
            timeout: 30000,  // 30 seconds timeout for AJAX requests
            beforeSend: function(xhr) {
                console.log('🔐 [CSRF] beforeSend called');
                console.log('🔑 [CSRF] window.csrfToken:', window.csrfToken ? window.csrfToken.substring(0, 10) + '...' : 'UNDEFINED');
                // Add CSRF token to request header
                if (window.csrfToken) {
                    xhr.setRequestHeader('X-CSRFToken', window.csrfToken);
                    console.log('✅ [CSRF] X-CSRFToken header set');
                } else {
                    console.error('❌ [CSRF] window.csrfToken is undefined!');
                }
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            },
            data: function(d) {
                console.log('🔐 [CSRF] data function called');
                // Add CSRF token to POST data
                // Try window.csrfToken first (always available), fallback to cookie
                const csrfToken = window.csrfToken || getCsrfToken();
                if (csrfToken) {
                    d.csrf_test_name = csrfToken;
                    console.log('✅ [CSRF] csrf_test_name added to POST data:', csrfToken.substring(0, 10) + '...');
                } else {
                    console.error('❌ [CSRF] No token available (window.csrfToken and cookie both missing)');
                }
                return d;
            },
            error: function(xhr, error, thrown) {
                console.error('❌ DataTables AJAX Error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    error: error,
                    thrown: thrown
                });
                
                // Force hide processing indicator
                $('.dataTables_processing').hide();
                
                // Show user-friendly error message
                const errorMsg = xhr.status === 0 ? 
                    'Koneksi terputus. Silakan cek koneksi internet Anda.' :
                    xhr.status === 403 ?
                    'Akses ditolak. Silakan refresh halaman dan coba lagi.' :
                    xhr.status === 404 ? 
                    'URL tidak ditemukan. Silakan hubungi administrator.' :
                    xhr.status === 500 ? 
                    'Server error. Silakan coba lagi atau hubungi administrator.' :
                    'Gagal memuat data. Silakan refresh halaman.';
                
                if (typeof showNotification === 'function') {
                    showNotification(errorMsg, 'error');
                }
            }
        },
        
        // Language Configuration (Bahasa Indonesia)
        language: {
            decimal: ",",
            thousands: ".",
            search: "Cari:",
            searchPlaceholder: "Ketik untuk mencari...",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(dari total _MAX_ data)",
            infoPostFix: "",
            loadingRecords: "Sedang menyiapkan data...",
            processing: function() {
                // Get BASE_URL dynamically at runtime
                const baseUrl = (typeof window.BASE_URL !== 'undefined' && window.BASE_URL) ? window.BASE_URL : (window.location.origin + '/optima/public/');
                return `
                    <div class="loading-logo">
                        <img src="${baseUrl}assets/images/logo-optima.png" alt="OPTIMA">
                    </div>
                    <div class="loading-text">Sedang menyiapkan data...</div>
                    <div class="loading-subtitle">Mohon tunggu sebentar</div>
                `;
            }(),
            zeroRecords: "Tidak ada data yang sesuai dengan pencarian Anda",
            emptyTable: "Tidak ada data tersedia",
            paginate: {
                first: '<i class="fas fa-angle-double-left"></i>',
                last: '<i class="fas fa-angle-double-right"></i>',
                next: '<i class="fas fa-angle-right"></i>',
                previous: '<i class="fas fa-angle-left"></i>'
            },
            aria: {
                sortAscending: ": aktifkan untuk mengurutkan kolom naik",
                sortDescending: ": aktifkan untuk mengurutkan kolom turun"
            }
        },
        
        // DOM Layout
        dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        
        // Responsive
        responsive: true,
        
        // Auto Width
        autoWidth: false,
        
        // Column Defaults
        columnDefs: [
            {
                targets: -1,  // Last column (Actions)
                orderable: false,
                searchable: false,
                className: 'text-end'
            }
        ],
        
        // Callbacks
        drawCallback: function(settings) {
            // Re-initialize tooltips after table draw
            if (typeof $ !== 'undefined' && $.fn.tooltip) {
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
            
            // Ensure processing indicator is hidden after draw
            $(settings.nTableWrapper).find('.dataTables_processing').hide();
        },
        
        preDrawCallback: function(settings) {
            // Safety timeout - force hide processing after 35 seconds
            const $processing = $(settings.nTableWrapper).find('.dataTables_processing');
            
            if (settings._processingTimeout) {
                clearTimeout(settings._processingTimeout);
            }
            
            settings._processingTimeout = setTimeout(function() {
                console.warn('⚠️ DataTables processing timeout reached - force hiding (this is normal for large datasets)');
                $processing.hide();
                
                // Don't show notification - data might still be loading successfully
                // Only log to console for debugging
            }, 45000); // 45 seconds timeout (increased for production with 2000+ records)
        },
        
        initComplete: function(settings, json) {
            // Add custom classes after initialization
            $(this).addClass('table-initialized');
            
            // Clear processing timeout on completion
            if (settings._processingTimeout) {
                clearTimeout(settings._processingTimeout);
            }
        }
    };

    // ============================================
    // FILTER HELPERS
    // ============================================
    
    const FilterManager = {
        /**
         * Initialize Status Filter Pills
         * 
         * @param {string} tableId - Table selector (#tableId)
         * @param {Array} statuses - Array of status values
         * @param {string} defaultStatus - Default active status
         * @param {number} columnIndex - Column index for status (default: 6)
         */
        initStatusFilter: function(tableId, statuses, defaultStatus = 'all', columnIndex = 6) {
            const $table = $(tableId).DataTable();
            const $pills = $('.status-pill');
            
            $pills.on('click', function() {
                const status = $(this).data('status');
                
                // Update active state
                $pills.removeClass('active');
                $(this).addClass('active');
                
                // Apply filter
                if (status === 'all') {
                    $table.column(columnIndex).search('').draw();
                } else {
                    $table.column(columnIndex).search(status).draw();
                }
            });
            
            // Set default active
            $(`.status-pill[data-status="${defaultStatus}"]`).addClass('active');
        },
        
        /**
         * Initialize Date Range Filter
         * 
         * @param {string} tableId - Table selector (#tableId)
         * @param {string} startDateSelector - Start date input selector
         * @param {string} endDateSelector - End date input selector
         * @param {number} columnIndex - Column index for date (default: 2)
         * @param {string} dateFormat - Date format (default: 'DD-MM-YYYY')
         */
        initDateRangeFilter: function(tableId, startDateSelector, endDateSelector, columnIndex = 2, dateFormat = 'DD-MM-YYYY') {
            const $table = $(tableId).DataTable();
            const $startDate = $(startDateSelector);
            const $endDate = $(endDateSelector);
            
            // Custom filter function
            const filterIndex = $.fn.dataTable.ext.search.length;
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== tableId.replace('#', '')) {
                    return true;
                }
                
                const startDate = $startDate.val();
                const endDate = $endDate.val();
                const dateColumn = data[columnIndex];
                
                if (!startDate && !endDate) {
                    return true;
                }
                
                // Check if moment.js is available
                if (typeof moment !== 'undefined') {
                    const rowDate = moment(dateColumn, dateFormat);
                    
                    if (startDate && !endDate) {
                        return rowDate.isSameOrAfter(moment(startDate));
                    }
                    
                    if (!startDate && endDate) {
                        return rowDate.isSameOrBefore(moment(endDate));
                    }
                    
                    return rowDate.isBetween(moment(startDate), moment(endDate), null, '[]');
                } else {
                    // Fallback to string comparison
                    if (startDate && dateColumn < startDate) return false;
                    if (endDate && dateColumn > endDate) return false;
                    return true;
                }
            });
            
            // Event listeners
            $startDate.add($endDate).on('change', function() {
                $table.draw();
            });
            
            // Store filter index for cleanup
            $table.settings()[0].oInit.filterIndex = filterIndex;
        },
        
        /**
         * Initialize Category/Department Filter
         * 
         * @param {string} tableId - Table selector (#tableId)
         * @param {string} selectSelector - Select dropdown selector
         * @param {number} columnIndex - Column index to filter
         */
        initCategoryFilter: function(tableId, selectSelector, columnIndex) {
            const $table = $(tableId).DataTable();
            const $select = $(selectSelector);
            
            $select.on('change', function() {
                const value = $(this).val();
                
                if (value === 'all' || value === '') {
                    $table.column(columnIndex).search('').draw();
                } else {
                    $table.column(columnIndex).search('^' + value + '$', true, false).draw();
                }
            });
        },
        
        /**
         * Clear All Filters for a specific table
         * 
         * @param {string} tableId - Table selector (#tableId)
         */
        clearAllFilters: function(tableId) {
            const $table = $(tableId).DataTable();
            
            // Clear DataTable search
            $table.search('').columns().search('').draw();
            
            // Clear custom date range filter
            const settings = $table.settings()[0];
            if (settings.oInit.filterIndex !== undefined) {
                $.fn.dataTable.ext.search.splice(settings.oInit.filterIndex, 1);
            }
            
            // Reset filter UI
            $('.status-pill').removeClass('active');
            $('.status-pill[data-status="all"]').addClass('active');
            $('input[type="date"]').val('');
            $('select.filter-select').val('all');
            
            // Redraw table
            $table.draw();
        }
    };

    // ============================================
    // EXPORT HELPERS
    // ============================================
    
    const ExportManager = {
        /**
         * Export Table to Excel (requires DataTables Buttons extension)
         * 
         * @param {string} tableId - Table selector (#tableId)
         * @param {string} filename - Export filename
         */
        toExcel: function(tableId, filename = 'export.xlsx') {
            const $table = $(tableId).DataTable();
            
            // Check if Buttons extension is available
            if ($.fn.DataTable.ext.buttons && $.fn.DataTable.ext.buttons.excelHtml5) {
                $table.button('.buttons-excel').trigger();
            } else {
                console.warn('DataTables Buttons extension (Excel) not loaded');
                alert('Fitur export Excel belum tersedia. Silakan hubungi administrator.');
            }
        },
        
        /**
         * Export Table to PDF (requires DataTables Buttons extension)
         * 
         * @param {string} tableId - Table selector (#tableId)
         * @param {string} filename - Export filename
         */
        toPDF: function(tableId, filename = 'export.pdf') {
            const $table = $(tableId).DataTable();
            
            // Check if Buttons extension is available
            if ($.fn.DataTable.ext.buttons && $.fn.DataTable.ext.buttons.pdfHtml5) {
                $table.button('.buttons-pdf').trigger();
            } else {
                console.warn('DataTables Buttons extension (PDF) not loaded');
                alert('Fitur export PDF belum tersedia. Silakan hubungi administrator.');
            }
        },
        
        /**
         * Print Table
         * 
         * @param {string} tableId - Table selector (#tableId)
         */
        print: function(tableId) {
            // Hide non-printable elements
            $('.dataTables_length, .dataTables_filter, .dataTables_paginate, .dataTables_info, .table-actions, .datatable-filters').addClass('d-print-none');
            
            // Trigger print
            window.print();
        },
        
        /**
         * Copy Table Data to Clipboard
         * 
         * @param {string} tableId - Table selector (#tableId)
         */
        copyToClipboard: function(tableId) {
            const $table = $(tableId).DataTable();
            
            // Check if Buttons extension is available
            if ($.fn.DataTable.ext.buttons && $.fn.DataTable.ext.buttons.copyHtml5) {
                $table.button('.buttons-copy').trigger();
            } else {
                console.warn('DataTables Buttons extension (Copy) not loaded');
                alert('Fitur copy belum tersedia.');
            }
        }
    };

    // ============================================
    // UTILITY HELPERS
    // ============================================
    
    const UtilityHelpers = {
        /**
         * Get all selected row data
         * 
         * @param {string} tableId - Table selector (#tableId)
         * @param {string} checkboxClass - Checkbox class selector (default: '.row-checkbox')
         * @returns {Array} Array of row data objects
         */
        getSelectedRows: function(tableId, checkboxClass = '.row-checkbox') {
            const $table = $(tableId).DataTable();
            const selectedData = [];
            
            $(checkboxClass + ':checked').each(function() {
                const row = $(this).closest('tr');
                const rowData = $table.row(row).data();
                selectedData.push(rowData);
            });
            
            return selectedData;
        },
        
        /**
         * Add row to table dynamically
         * 
         * @param {string} tableId - Table selector (#tableId)
         * @param {object} rowData - Row data object
         * @param {boolean} redraw - Whether to redraw table (default: true)
         */
        addRow: function(tableId, rowData, redraw = true) {
            const $table = $(tableId).DataTable();
            $table.row.add(rowData);
            
            if (redraw) {
                $table.draw();
            }
        },
        
        /**
         * Update existing row
         * 
         * @param {string} tableId - Table selector (#tableId)
         * @param {number} rowIndex - Row index or selector
         * @param {object} rowData - New row data
         */
        updateRow: function(tableId, rowIndex, rowData) {
            const $table = $(tableId).DataTable();
            $table.row(rowIndex).data(rowData).draw();
        },
        
        /**
         * Remove row from table
         * 
         * @param {string} tableId - Table selector (#tableId)
         * @param {number} rowIndex - Row index or selector
         */
        removeRow: function(tableId, rowIndex) {
            const $table = $(tableId).DataTable();
            $table.row(rowIndex).remove().draw();
        },
        
        /**
         * Get total data count (including filtered)
         * 
         * @param {string} tableId - Table selector (#tableId)
         * @returns {object} Object with total and filtered counts
         */
        getDataCount: function(tableId) {
            const $table = $(tableId).DataTable();
            const info = $table.page.info();
            
            return {
                total: info.recordsTotal,
                filtered: info.recordsDisplay,
                currentPage: info.page + 1,
                totalPages: info.pages
            };
        }
    };

    // ============================================
    // MAIN API
    // ============================================
    
    return {
        /**
         * Initialize DataTable with OPTIMA defaults
         * 
         * @param {string} selector - Table selector (#tableId)
         * @param {object} customConfig - Override default config
         * @returns {object} DataTable instance
         */
        init: function(selector, customConfig = {}) {
            // Check if jQuery and DataTables are loaded
            if (typeof $ === 'undefined') {
                console.error('jQuery is required for OptimaDataTable');
                return null;
            }
            
            if (typeof $.fn.DataTable === 'undefined') {
                console.error('DataTables plugin is required for OptimaDataTable');
                return null;
            }
            
            // Save default CSRF functions BEFORE merge (critical!)
            const defaultBeforeSend = defaultConfig.ajax.beforeSend;
            const defaultDataFn = defaultConfig.ajax.data;
            const customBeforeSend = customConfig.ajax && customConfig.ajax.beforeSend;
            const customDataFn = customConfig.ajax && customConfig.ajax.data;
            
            // Merge configs (deep merge) - this might overwrite our functions!
            const config = $.extend(true, {}, defaultConfig, customConfig);
            
            // CRITICAL FIX: Force CSRF functions back after merge
            if (config.ajax) {
                // Always combine beforeSend - CSRF must run first
                if (customBeforeSend && typeof customBeforeSend === 'function') {
                    config.ajax.beforeSend = function(xhr) {
                        // CSRF token (always runs first)
                        if (defaultBeforeSend) {
                            defaultBeforeSend.call(this, xhr);
                        }
                        // Then custom logic
                        return customBeforeSend.call(this, xhr);
                    };
                } else {
                    // No custom beforeSend - force default CSRF
                    config.ajax.beforeSend = defaultBeforeSend;
                }
                
                // Always combine data function - CSRF must be included
                if (customDataFn && typeof customDataFn === 'function') {
                    config.ajax.data = function(d) {
                        // CSRF token in data (always runs first)
                        if (defaultDataFn) {
                            d = defaultDataFn(d) || d;
                        }
                        // Then custom data processing
                        return customDataFn(d);
                    };
                } else {
                    // No custom data function - force default CSRF
                    config.ajax.data = defaultDataFn;
                }
            }
            
            // Initialize DataTable
            try {
                const table = $(selector).DataTable(config);
                
                // Store reference
                this.tables = this.tables || {};
                this.tables[selector] = table;
                
                // Log initialization
                if (config.debug) {
                    console.log('OptimaDataTable initialized:', selector, config);
                }
                
                return table;
            } catch (error) {
                console.error('Error initializing OptimaDataTable:', selector, error);
                return null;
            }
        },
        
        /**
         * Get DataTable instance by selector
         * 
         * @param {string} selector - Table selector (#tableId)
         * @returns {object|null} DataTable instance or null
         */
        get: function(selector) {
            return this.tables && this.tables[selector] 
                ? this.tables[selector] 
                : null;
        },
        
        /**
         * Check if table is initialized
         * 
         * @param {string} selector - Table selector (#tableId)
         * @returns {boolean}
         */
        isInitialized: function(selector) {
            return this.tables && this.tables[selector] !== undefined;
        },
        
        /**
         * Reload table data (AJAX only)
         * 
         * @param {string} selector - Table selector (#tableId)
         * @param {boolean} resetPaging - Whether to reset to page 1 (default: false)
         */
        reload: function(selector, resetPaging = false) {
            const table = this.get(selector);
            if (table && table.ajax) {
                table.ajax.reload(null, resetPaging);
            } else {
                console.warn('Table not found or not using AJAX:', selector);
            }
        },
        
        /**
         * Destroy DataTable instance
         * 
         * @param {string} selector - Table selector (#tableId)
         * @param {boolean} removeAll - Remove all generated elements (default: false)
         */
        destroy: function(selector, removeAll = false) {
            const table = this.get(selector);
            if (table) {
                table.destroy(removeAll);
                delete this.tables[selector];
            }
        },
        
        /**
         * Get default configuration (for reference)
         * 
         * @returns {object} Default config object
         */
        getDefaultConfig: function() {
            return $.extend(true, {}, defaultConfig);
        },
        
        /**
         * Update default configuration globally
         * 
         * @param {object} newDefaults - New default settings
         */
        setDefaultConfig: function(newDefaults) {
            $.extend(true, defaultConfig, newDefaults);
        },
        
        /**
         * Filter Manager - Status, Date Range, Category filters
         */
        filters: FilterManager,
        
        /**
         * Export Manager - Excel, PDF, Print, Copy
         */
        exports: ExportManager,
        
        /**
         * Utility Helpers - Row operations, data counts
         */
        utils: UtilityHelpers,
        
        /**
         * Get version
         */
        version: '1.0.0'
    };
})();

// ============================================
// AUTO-INITIALIZATION FOR SIMPLE TABLES
// ============================================

// Auto-init tables with data-datatable="auto" attribute
$(document).ready(function() {
    $('[data-datatable="auto"]').each(function() {
        const tableId = '#' + $(this).attr('id');
        const ajaxUrl = $(this).data('ajax-url');
        
        if (ajaxUrl) {
            OptimaDataTable.init(tableId, {
                ajax: ajaxUrl
            });
        } else {
            // Client-side table
            OptimaDataTable.init(tableId, {
                serverSide: false
            });
        }
    });
});

// ============================================
// USAGE EXAMPLES (IN COMMENTS)
// ============================================

/*
// ===========================================================
// EXAMPLE 1: Basic Server-Side Table with AJAX
// ===========================================================

OptimaDataTable.init('#customersTable', {
    ajax: '/api/customers/list',
    columns: [
        { data: 'id' },
        { data: 'name' },
        { data: 'email' },
        { data: 'phone' },
        { data: 'status' },
        { data: 'actions', orderable: false }
    ]
});

// ===========================================================
// EXAMPLE 2: Table with Custom Sorting and Page Length
// ===========================================================

OptimaDataTable.init('#invoicesTable', {
    ajax: '/api/invoices/list',
    order: [[3, 'desc']],  // Sort by 4th column (invoice date) DESC
    pageLength: 50,
    columns: [
        { data: 'nomor_invoice' },
        { data: 'customer' },
        { data: 'total_amount' },
        { data: 'invoice_date' },
        { data: 'status' },
        { data: 'actions', orderable: false }
    ]
});

// ===========================================================
// EXAMPLE 3: Table with Status Filter Pills
// ===========================================================

// HTML:
// <div class="datatable-filters">
//   <div class="status-filter-pills">
//     <span class="status-pill active" data-status="all">Semua</span>
//     <span class="status-pill" data-status="DRAFT">Draft</span>
//     <span class="status-pill" data-status="SUBMITTED">Submitted</span>
//     <span class="status-pill" data-status="APPROVED">Approved</span>
//   </div>
// </div>

OptimaDataTable.init('#spkTable', {
    ajax: '/api/spk/list',
    columns: [
        { data: 'nomor_spk' },
        { data: 'jenis_spk' },
        { data: 'pelanggan' },
        { data: 'tanggal' },
        { data: 'pic' },
        { data: 'kontak' },
        { data: 'status' },  // Column index 6
        { data: 'actions', orderable: false }
    ]
});

// Initialize status filter (column index 6)
OptimaDataTable.filters.initStatusFilter('#spkTable', 
    ['DRAFT', 'SUBMITTED', 'APPROVED', 'CANCELLED'],
    'all',
    6  // Status column index
);

// ===========================================================
// EXAMPLE 4: Table with Date Range Filter
// ===========================================================

// HTML:
// <div class="datatable-filters">
//   <div class="filter-group">
//     <label>Tanggal</label>
//     <div class="daterange-filter">
//       <input type="date" id="startDate" class="form-control">
//       <span>-</span>
//       <input type="date" id="endDate" class="form-control">
//     </div>
//   </div>
// </div>

OptimaDataTable.init('#quotationsTable', {
    ajax: '/api/quotations/list',
    columns: [
        { data: 'quotation_number' },
        { data: 'customer' },
        { data: 'tanggal_quotation' },  // Column index 2
        { data: 'total_amount' },
        { data: 'status' },
        { data: 'actions', orderable: false }
    ]
});

// Initialize date range filter (column index 2)
OptimaDataTable.filters.initDateRangeFilter('#quotationsTable', 
    '#startDate', 
    '#endDate',
    2,  // Date column index
    'YYYY-MM-DD'
);

// ===========================================================
// EXAMPLE 5: Table with Category/Department Filter
// ===========================================================

// HTML:
// <div class="datatable-filters">
//   <div class="filter-group">
//     <label>Department</label>
//     <select id="deptFilter" class="filter-select form-select">
//       <option value="all">Semua Department</option>
//       <option value="Marketing">Marketing</option>
//       <option value="Finance">Finance</option>
//       <option value="Service">Service</option>
//     </select>
//   </div>
// </div>

OptimaDataTable.init('#employeesTable', {
    ajax: '/api/employees/list',
    columns: [
        { data: 'employee_id' },
        { data: 'name' },
        { data: 'email' },
        { data: 'department' },  // Column index 3
        { data: 'position' },
        { data: 'actions', orderable: false }
    ]
});

// Initialize category filter (column index 3)
OptimaDataTable.filters.initCategoryFilter('#employeesTable', 
    '#deptFilter', 
    3  // Department column index
);

// ===========================================================
// EXAMPLE 6: Client-Side Table (Small Dataset)
// ===========================================================

OptimaDataTable.init('#smallTable', {
    serverSide: false,  // Client-side processing
    data: [
        { id: 1, name: 'John Doe', email: 'john@example.com' },
        { id: 2, name: 'Jane Smith', email: 'jane@example.com' }
    ],
    columns: [
        { data: 'id' },
        { data: 'name' },
        { data: 'email' }
    ]
});

// ===========================================================
// EXAMPLE 7: Reload Table Data
// ===========================================================

// Reload without resetting page
OptimaDataTable.reload('#customersTable');

// Reload and reset to page 1
OptimaDataTable.reload('#customersTable', true);

// ===========================================================
// EXAMPLE 8: Clear All Filters
// ===========================================================

$('#btnClearFilters').on('click', function() {
    OptimaDataTable.filters.clearAllFilters('#spkTable');
});

// ===========================================================
// EXAMPLE 9: Export Functions
// ===========================================================

// Export to Excel
$('#btnExportExcel').on('click', function() {
    OptimaDataTable.exports.toExcel('#customersTable', 'customers.xlsx');
});

// Export to PDF
$('#btnExportPDF').on('click', function() {
    OptimaDataTable.exports.toPDF('#customersTable', 'customers.pdf');
});

// Print table
$('#btnPrint').on('click', function() {
    OptimaDataTable.exports.print('#customersTable');
});

// ===========================================================
// EXAMPLE 10: Utility Functions
// ===========================================================

// Get selected rows (with checkboxes)
const selectedRows = OptimaDataTable.utils.getSelectedRows('#customersTable');
console.log('Selected:', selectedRows);

// Add new row dynamically
OptimaDataTable.utils.addRow('#customersTable', {
    id: 999,
    name: 'New Customer',
    email: 'new@example.com'
});

// Get data counts
const counts = OptimaDataTable.utils.getDataCount('#customersTable');
console.log('Total:', counts.total, 'Filtered:', counts.filtered);

// ===========================================================
// EXAMPLE 11: Auto-Initialize with HTML Attribute
// ===========================================================

// Just add data-datatable="auto" to table element
// <table id="autoTable" class="table table-striped table-hover" 
//        data-datatable="auto" 
//        data-ajax-url="/api/data/list">
// </table>
// 
// OptimaDataTable will automatically initialize on document ready!

*/
"" 
