/**
 * DataTable Date Filter Mixin
 * 
 * Provides date filtering capability for DataTables
 * Works by wrapping the ajax.data function to inject date parameters
 * 
 * Usage:
 * var config = {
 *     ajax: {
 *         url: 'your-url',
 *         type: 'POST'
 *     },
 *     columns: [ ... ]
 * };
 * 
 * // Apply mixin before passing to DataTable
 * applyDateFilterToConfig(config, 'myDatePickerId');
 * var table = $('#myTable').DataTable(config);
 * 
 * // Setup auto-reload callbacks
 * setupDataTableDateFilter(table, 'myDatePickerId', optionalCallback);
 */

(function(window, $) {
    'use strict';

    // Initialize global date range storage
    if (typeof window.currentDateRange === 'undefined') {
        window.currentDateRange = { start: null, end: null };
        console.log('✅ Global date range storage initialized');
    }

    /**
     * Apply date filter capability to DataTable config
     * Wraps the ajax.data function to inject date parameters
     * 
     * @param {object} config - DataTable configuration object
     * @param {string} datePickerId - ID of the date range picker
     * @returns {object} Modified config
     */
    window.applyDateFilterToConfig = function(config, datePickerId) {
        datePickerId = datePickerId || 'globalDateRangePicker';
        
        if (!config.ajax) {
            console.warn('⚠️ No ajax config found, date filter cannot be applied');
            return config;
        }

        // Store original data function if exists
        var originalDataFn = config.ajax.data;

        // Wrap or create data function
        config.ajax.data = function(d) {
            // Call original data function first if it exists
            if (typeof originalDataFn === 'function') {
                originalDataFn(d);
            }

            // Add date filter parameters
            if (window.currentDateRange && 
                window.currentDateRange.start && 
                window.currentDateRange.end) {
                d.start_date = window.currentDateRange.start;
                d.end_date = window.currentDateRange.end;
                
                console.log('📅 DataTable request WITH date filter:', {
                    start_date: d.start_date,
                    end_date: d.end_date
                });
            } else {
                console.log('📅 DataTable request WITHOUT date filter (showing all data)');
            }

            return d;
        };

        console.log('✅ Date filter applied to DataTable config for:', datePickerId);
        return config;
    };

    /**
     * DEPRECATED: Old mixin approach (kept for compatibility)
     * Use applyDateFilterToConfig instead
     */
    window.dataTableDateFilterMixin = function(datePickerId, additionalConfig) {
        console.warn('⚠️ dataTableDateFilterMixin is deprecated, use applyDateFilterToConfig instead');
        return additionalConfig || {};
    };
    
    /**
     * Setup automatic DataTable reload on date range change
     * Call this after DataTable initialization
     * 
     * @param {object} dataTableInstance - The DataTable instance
     * @param {string} datePickerId - ID of the date range picker element
     * @param {function} onFilterChange - Optional callback when filter changes
     */
    window.setupDataTableDateFilter = function(dataTableInstance, datePickerId, onFilterChange) {
        datePickerId = datePickerId || 'globalDateRangePicker';
        
        console.log('🔧 Setting up DataTable date filter for:', datePickerId);
        
        // Verify DataTable instance exists
        if (!dataTableInstance) {
            console.error('❌ DataTable instance is null/undefined!');
            return;
        }
        
        // Setup range change callback
        window[datePickerId + 'OnRangeChange'] = function(startDate, endDate) {
            console.log('📅 Date range changed, reloading DataTable...');
            console.log('   Start:', startDate, 'End:', endDate);
            
            window.currentDateRange.start = startDate;
            window.currentDateRange.end = endDate;
            
            console.log('   Updated global range:', window.currentDateRange);
            
            // Reload DataTable
            if (dataTableInstance && dataTableInstance.ajax) {
                console.log('   Calling DataTable.ajax.reload()...');
                dataTableInstance.ajax.reload();
            } else {
                console.error('   ❌ DataTable ajax method not available!');
            }
            
            // Call custom callback if provided
            if (typeof onFilterChange === 'function') {
                console.log('   Calling custom callback...');
                onFilterChange(startDate, endDate);
            }
        };
        
        // Setup clear callback
        window[datePickerId + 'OnClear'] = function() {
            console.log('✖️ Date filter cleared, reloading DataTable...');
            
            window.currentDateRange.start = null;
            window.currentDateRange.end = null;
            
            console.log('   Cleared global range:', window.currentDateRange);
            
            // Reload DataTable
            if (dataTableInstance && dataTableInstance.ajax) {
                console.log('   Calling DataTable.ajax.reload()...');
                dataTableInstance.ajax.reload();
            } else {
                console.error('   ❌ DataTable ajax method not available!');
            }
            
            // Call custom callback if provided
            if (typeof onFilterChange === 'function') {
                console.log('   Calling custom callback with null dates...');
                onFilterChange(null, null);
            }
        };
        
        console.log('✅ DataTable date filter setup complete for:', datePickerId);
        console.log('   Callbacks registered:', {
            onRangeChange: datePickerId + 'OnRangeChange',
            onClear: datePickerId + 'OnClear'
        });
    };
    
})(window, jQuery);
