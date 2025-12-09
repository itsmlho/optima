/**
 * Page Date Filter Helper
 * 
 * Universal helper untuk implementasi date filter di semua halaman OPTIMA
 * Menyediakan API yang konsisten dan mudah digunakan
 * 
 * Dependencies:
 * - jQuery
 * - moment.js
 * - daterangepicker.js
 * - global-daterange.js (untuk inisialisasi picker)
 * - datatable-datefilter-mixin.js (optional, untuk DataTable)
 * 
 * @version 1.0.0
 * @author OPTIMA Team
 */

(function(window, $) {
    'use strict';

    /**
     * Initialize date filter untuk halaman
     * 
     * Pattern penggunaan yang konsisten:
     * 
     * ```javascript
     * initPageDateFilter({
     *     pickerId: 'myPageDateRangePicker',
     *     onInit: function() {
     *         // Load data awal (tanpa filter)
     *         loadStatistics();
     *         loadDataTable();
     *     },
     *     onDateChange: function(startDate, endDate) {
     *         // Reload data dengan filter
     *         loadStatistics(startDate, endDate);
     *         // DataTable otomatis reload jika pakai mixin
     *     },
     *     onDateClear: function() {
     *         // Reload data tanpa filter
     *         loadStatistics();
     *         // DataTable otomatis reload jika pakai mixin
     *     },
     *     debug: true // Optional: enable logging
     * });
     * ```
     * 
     * @param {object} options - Konfigurasi
     * @param {string} options.pickerId - ID dari date range picker element
     * @param {function} options.onInit - Callback saat halaman pertama load
     * @param {function} options.onDateChange - Callback saat date range berubah (params: startDate, endDate)
     * @param {function} options.onDateClear - Callback saat date filter di-clear
     * @param {boolean} options.debug - Enable debug logging (default: false)
     */
    window.initPageDateFilter = function(options) {
        // Validate options
        if (!options || typeof options !== 'object') {
            console.error('❌ initPageDateFilter: options is required');
            return false;
        }

        const pickerId = options.pickerId || 'globalDateRangePicker';
        const debug = options.debug || false;

        // Setup global callbacks untuk date picker
        // Callbacks ini akan dipanggil oleh global-daterange.js
        window[pickerId + 'OnRangeChange'] = function(startDate, endDate) {indow.currentDateRange.start = startDate;
            window.currentDateRange.end = endDate;

            // Call user callback
            if (typeof options.onDateChange === 'function') {
                try {
                    options.onDateChange(startDate, endDate);
                } catch (error) {
                    console.error('❌ Error in onDateChange callback:', error);
                }
            }
        };

        window[pickerId + 'OnClear'] = function() {
            // Clear global state
            window.currentDateRange = window.currentDateRange || {};
            window.currentDateRange.start = null;
            window.currentDateRange.end = null;

            // Call user callback
            if (typeof options.onDateClear === 'function') {
                try {
                    options.onDateClear();
                } catch (error) {
                    console.error('❌ Error in onDateClear callback:', error);
                }
            }
        };

        // Call initial load callback
        if (typeof options.onInit === 'function') {
            try {
                options.onInit();
            } catch (error) {
                console.error('❌ Error in onInit callback:', error);
            }
        }

        return true;
    };

    /**
     * Helper untuk DataTable dengan date filter
     * 
     * Menggabungkan initPageDateFilter dengan DataTable
     * 
     * ```javascript
     * initDataTableWithDateFilter({
     *     pickerId: 'myPageDateRangePicker',
     *     tableId: 'myTable',
     *     tableConfig: {
     *         ajax: { url: '...', type: 'POST' },
     *         columns: [...]
     *     },
     *     onStatisticsLoad: function(startDate, endDate) {
     *         loadStatistics(startDate, endDate);
     *     },
     *     autoCalculateStats: true,  // NEW: Auto-calculate dari data table
     *     statsConfig: {             // NEW: Config untuk auto-calculate
     *         total: '#stat-total',
     *         active: { selector: '#stat-active', filter: row => row.status === 'active' }
     *     },
     *     debug: true
     * });
     * ```
     * 
     * @param {object} options - Konfigurasi
     * @param {string} options.pickerId - ID dari date range picker
     * @param {string} options.tableId - ID dari table element (tanpa #)
     * @param {object} options.tableConfig - DataTable configuration object
     * @param {function} options.onStatisticsLoad - Optional: callback untuk load statistics (AJAX-based)
     * @param {boolean} options.autoCalculateStats - Optional: auto-calculate stats dari data table
     * @param {object} options.statsConfig - Optional: config untuk auto-calculate statistics
     * @param {function} options.onTableReady - Optional: callback setelah table initialized
     * @param {boolean} options.debug - Enable debug logging
     * @returns {object} DataTable instance
     */
    window.initDataTableWithDateFilter = function(options) {
        if (!options || !options.tableId || !options.tableConfig) {
            console.error('❌ initDataTableWithDateFilter: tableId and tableConfig are required');
            return null;
        }

        const pickerId = options.pickerId || 'globalDateRangePicker';
        const tableId = options.tableId;
        const debug = options.debug || false;
        const autoCalculateStats = options.autoCalculateStats || false;
        const statsConfig = options.statsConfig || null;
        let dataTableInstance = null;

        // Function to auto-calculate statistics dari data table
        function autoCalculateStatistics() {
            if (!autoCalculateStats || !statsConfig || !dataTableInstance) {
                return;
            }

            try {
                // Get all filtered data from DataTable
                const filteredData = dataTableInstance.rows({ search: 'applied' }).data().toArray();

                // Calculate each stat based on config
                Object.keys(statsConfig).forEach(statKey => {
                    const config = statsConfig[statKey];
                    
                    // Simple selector (just count all rows)
                    if (typeof config === 'string') {
                        const count = filteredData.length;
                        $(config).text(count);
                    }
                    // Advanced selector with filter function
                    else if (config && config.selector && typeof config.filter === 'function') {
                        const count = filteredData.filter(config.filter).length;
                        $(config.selector).text(count);
                    }
                    // Custom calculator function
                    else if (config && config.selector && typeof config.calculate === 'function') {
                        const value = config.calculate(filteredData);
                        $(config.selector).text(value);
                    }
                });
            } catch (error) {
                console.error('❌ Error auto-calculating statistics:', error);
            }
        }

        // Function to reload statistics (AJAX-based)
        function reloadStatistics() {
            if (typeof options.onStatisticsLoad === 'function') {
                const { start, end } = window.currentDateRange || {};
                try {
                    options.onStatisticsLoad(start, end);
                } catch (error) {
                    console.error('❌ Error loading statistics:', error);
                }
            }
        }
        
        // Combined function to update statistics
        function updateStatistics() {
            // If auto-calculate is enabled, use it
            if (autoCalculateStats && statsConfig) {
                autoCalculateStatistics();
            }
            // Otherwise use AJAX callback if provided
            else {
                reloadStatistics();
            }
        }

        // Initialize page date filter
        initPageDateFilter({
            pickerId: pickerId,
            onInit: function() {
                try {
                    // Apply date filter to DataTable config
                    if (typeof window.applyDateFilterToConfig === 'function') {
                        window.applyDateFilterToConfig(options.tableConfig, pickerId);
                    } else {
                        console.warn('⚠️ applyDateFilterToConfig not found, date filter may not work');
                    }

                    // Initialize DataTable
                    dataTableInstance = $('#' + tableId).DataTable(options.tableConfig);

                    // Setup DataTable date filter (this will override callbacks, so we need to chain)
                    if (typeof window.setupDataTableDateFilter === 'function') {
                        window.setupDataTableDateFilter(dataTableInstance, pickerId, function(startDate, endDate) {
                            updateStatistics();
                        });
                    }

                    // Add drawCallback to auto-calculate statistics after each draw
                    if (autoCalculateStats && statsConfig) {
                        const originalDrawCallback = options.tableConfig.drawCallback;
                        
                        dataTableInstance.on('draw.dt', function(e, settings) {
                            // Call original drawCallback if exists
                            if (typeof originalDrawCallback === 'function') {
                                try {
                                    originalDrawCallback.call(this, settings);
                                } catch (error) {
                                    if (debug) {
                                        console.warn('⚠️ Original drawCallback error:', error);
                                    }
                                }
                            }
                            // Auto-calculate statistics
                            autoCalculateStatistics();
                        });
                    }

                    // Load initial statistics
                    updateStatistics();

                    // Call onTableReady callback
                    if (typeof options.onTableReady === 'function') {
                        options.onTableReady(dataTableInstance);
                    }

                } catch (error) {
                    console.error('❌ Error initializing DataTable:', error);
                }
            },
            onDateChange: function(startDate, endDate) {
                // DataTable automatically reloads via setupDataTableDateFilter
                // Statistics will be reloaded by setupDataTableDateFilter callback
            },
            onDateClear: function() {
                // DataTable automatically reloads via setupDataTableDateFilter
                // Statistics will be reloaded by setupDataTableDateFilter callback
            },
            debug: debug
        });

        return dataTableInstance;
    };

    /**
     * Get current date filter values
     * 
     * @returns {object} { start: string|null, end: string|null }
     */
    window.getCurrentDateFilter = function() {
        return {
            start: (window.currentDateRange && window.currentDateRange.start) || null,
            end: (window.currentDateRange && window.currentDateRange.end) || null
        };
    };

    /**
     * Check if date filter is active
     * 
     * @returns {boolean}
     */
    window.isDateFilterActive = function() {
        const filter = window.getCurrentDateFilter();
        return !!(filter.start && filter.end);
    };

    /**
     * Format date filter untuk display
     * 
     * @returns {string}
     */
    window.formatCurrentDateFilter = function() {
        const filter = window.getCurrentDateFilter();
        if (filter.start && filter.end) {
            return filter.start + ' to ' + filter.end;
        }
        return 'All dates';
    };

})(window, jQuery);
