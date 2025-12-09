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
            }

            return d;
        };

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
        
        // Verify DataTable instance exists
        if (!dataTableInstance) {
            console.error('❌ DataTable instance is null/undefined!');
            return;
        }
        
        // Setup range change callback
        window[datePickerId + 'OnRangeChange'] = function(startDate, endDate) {
            window.currentDateRange.start = startDate;
            window.currentDateRange.end = endDate;
            
            // Reload DataTable
            if (dataTableInstance && dataTableInstance.ajax) {
                dataTableInstance.ajax.reload();
            } else {
                console.error('❌ DataTable ajax method not available!');
            }
            
            // Call custom callback if provided
            if (typeof onFilterChange === 'function') {
                onFilterChange(startDate, endDate);
            }
        };
        
        // Setup clear callback
        window[datePickerId + 'OnClear'] = function() {
            window.currentDateRange.start = null;
            window.currentDateRange.end = null;
            
            // Reload DataTable
            if (dataTableInstance && dataTableInstance.ajax) {
                dataTableInstance.ajax.reload();
            } else {
                console.error('❌ DataTable ajax method not available!');
            }
            
            // Call custom callback if provided
            if (typeof onFilterChange === 'function') {
                onFilterChange(null, null);
            }
        };
    };
    
})(window, jQuery);
