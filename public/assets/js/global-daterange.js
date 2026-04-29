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
    }

    let retryCount = 0;
    const maxRetries = 50; // 5 seconds max

    // Wait for DOM, jQuery, moment, and daterangepicker to be ready
    function initializeDateRangePickers() {
        if (typeof $ === 'undefined' || typeof moment === 'undefined' || typeof $.fn.daterangepicker === 'undefined') {
            retryCount++;
            if (retryCount < maxRetries) {
                setTimeout(initializeDateRangePickers, 100);
                return;
            } else {
                console.error('DateRangePicker dependencies failed to load after ' + maxRetries + ' retries');
                return;
            }
        }

        // Find all date range picker inputs
        const dateRangePickers = $('.global-date-range-picker');
        
        if (dateRangePickers.length === 0) {
            return;
        }

        dateRangePickers.each(function() {
            const $element = $(this);
            const pickerId = $element.attr('id') || 'globalDateRangePicker';
            
            // Default daterangepicker options with custom ranges
            const defaultOptions = {
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Bersihkan',
                    applyLabel: 'Terapkan',
                    format: 'DD/MM/YYYY',
                },
                ranges: {
                    'Hari Ini': [moment(), moment()],
                    'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                    '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                    'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                    'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
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

                // Store instance globally for access
                window[pickerId + 'Instance'] = $element.data('daterangepicker');

                // Event: When apply button clicked
                $element.on('apply.daterangepicker', function(ev, picker) {
                    const startDate = picker.startDate;
                    const endDate = picker.endDate;
                    
                    // Update input display
                    $(this).val(startDate.format('DD/MM/YYYY') + ' - ' + endDate.format('DD/MM/YYYY'));
                    
                    // Trigger custom callback if defined
                    const rangeCallback = window[pickerId + 'OnRangeChange'];
                    if (typeof rangeCallback === 'function') {
                        rangeCallback(startDate.format('YYYY-MM-DD'), endDate.format('YYYY-MM-DD'));
                    }
                });

                // Event: When cancel button clicked
                $element.on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                    
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
