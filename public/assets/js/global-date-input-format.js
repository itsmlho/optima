/**
 * Global Date Input Formatter
 * Normalizes native date inputs to DD/MM/YYYY display across user locales.
 * Submit value remains YYYY-MM-DD (safe for backend validation).
 */
(function () {
    'use strict';

    var retryCount = 0;
    var maxRetries = 60; // ~6s

    function initDateInputs() {
        if (typeof window.jQuery === 'undefined' || typeof window.flatpickr === 'undefined') {
            retryCount += 1;
            if (retryCount < maxRetries) {
                window.setTimeout(initDateInputs, 100);
            }
            return;
        }

        var dateLocale = (window.flatpickr && flatpickr.l10ns && flatpickr.l10ns.id) ? flatpickr.l10ns.id : 'id';

        // Apply to all native date inputs unless explicitly opted-out.
        $('input[type="date"]')
            .not('[data-native-date]')
            .not('.flatpickr-input')
            .each(function () {
                var el = this;
                if (el._flatpickr) return;

                flatpickr(el, {
                    locale: dateLocale,
                    altInput: true,
                    altFormat: 'd/m/Y',   // display to user
                    dateFormat: 'Y-m-d',  // value submitted to backend
                    allowInput: true,
                    disableMobile: true,
                });
            });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDateInputs);
    } else {
        initDateInputs();
    }
})();

