/**
 * Global Date Range Picker Initializer
 * Uses daterangepicker.js with custom ranges
 * Can be used across all pages in OPTIMA system
 */

(function() {
    'use strict';

    // Initialize global date range storage immediately
    if (typeof window.currentDateRange === 'undefined') {
        window.currentDateRange = { start: null, end: null };
        console.log('✅ Global date range storage initialized');
    }

    let retryCount = 0;
    const maxRetries = 50; // 5 seconds max

    // Wait for DOM, jQuery, moment, and daterangepicker to be ready
    function initializeDateRangePickers() {
        if (typeof $ === 'undefined' || typeof moment === 'undefined' || typeof $.fn.daterangepicker === 'undefined') {
            retryCount++;
            if (retryCount < maxRetries) {
                console.warn('Dependencies not loaded yet, retrying... (' + retryCount + '/' + maxRetries + ')');
                setTimeout(initializeDateRangePickers, 100);
                return;
            } else {
                console.error('DateRangePicker dependencies failed to load after ' + maxRetries + ' retries');
                return;
            }
        }

        console.log('DateRangePicker loaded successfully, initializing...');

        // Find all date range picker inputs
        const dateRangePickers = $('.global-date-range-picker');
        
        if (dateRangePickers.length === 0) {
            console.log('No date range pickers found on this page');
            return;
        }

        dateRangePickers.each(function() {
            const $element = $(this);
            const pickerId = $element.attr('id') || 'globalDateRangePicker';
            
            // Default daterangepicker options with custom ranges
            const defaultOptions = {
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    applyLabel: 'Apply',
                    format: 'MMM D, YYYY'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                alwaysShowCalendars: true,
                showDropdowns: true
            };

            // Check if custom options exist
            const customOptions = window[pickerId + 'Options'] || {};
            const options = $.extend({}, defaultOptions, customOptions);

            try {
                // Initialize daterangepicker
                $element.daterangepicker(options);
                
                console.log('Date Range Picker initialized:', pickerId);

                // Store instance globally for access
                window[pickerId + 'Instance'] = $element.data('daterangepicker');

                // Event: When apply button clicked
                $element.on('apply.daterangepicker', function(ev, picker) {
                    const startDate = picker.startDate;
                    const endDate = picker.endDate;
                    
                    // Update input display
                    $(this).val(startDate.format('MMM D, YYYY') + ' - ' + endDate.format('MMM D, YYYY'));
                    
                    console.log('Date range selected:', startDate.format('YYYY-MM-DD'), 'to', endDate.format('YYYY-MM-DD'));
                    
                    // Trigger custom callback if defined
                    const rangeCallback = window[pickerId + 'OnRangeChange'];
                    if (typeof rangeCallback === 'function') {
                        rangeCallback(startDate.format('YYYY-MM-DD'), endDate.format('YYYY-MM-DD'));
                    }
                });

                // Event: When cancel button clicked
                $element.on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                    
                    console.log('Date range cleared');
                    
                    // Trigger clear callback if defined
                    const clearCallback = window[pickerId + 'OnClear'];
                    if (typeof clearCallback === 'function') {
                        clearCallback();
                    }
                });

            } catch (error) {
                console.error('Failed to initialize date range picker:', error);
            }
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeDateRangePickers);
    } else {
        initializeDateRangePickers();
    }

})();
