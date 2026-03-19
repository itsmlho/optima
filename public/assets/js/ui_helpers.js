/**
 * OPTIMA UI Helpers - JavaScript Edition
 * 
 * Provides consistent JavaScript utilities for UI interactions including:
 * - Select2 initialization (standard & AJAX)
 * - Button loading states
 * - Toast notifications
 * - Confirmation dialogs
 * - Form validation helpers
 * 
 * @package OPTIMA
 * @version 1.0.0
 * @created 2026-02-09
 */

/**
 * Initialize Select2 with OPTIMA standard configuration
 * 
 * @param {string} selector - jQuery selector for select element
 * @param {object} options - Custom Select2 options to override defaults
 * @returns {jQuery} Select2 instance
 * 
 * @example
 * // Basic usage
 * initSelect2('#statusSelect');
 * 
 * // With custom options
 * initSelect2('#categorySelect', {
 *     placeholder: 'Choose a category',
 *     allowClear: false
 * });
 */
function initSelect2(selector, options = {}) {
    const defaults = {
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Select an option',
        allowClear: true,
        language: {
            noResults: function() {
                return "No results found";
            },
            searching: function() {
                return "Searching...";
            },
            inputTooShort: function() {
                return "Please enter more characters";
            },
            loadingMore: function() {
                return "Loading more results...";
            },
            maximumSelected: function(args) {
                return "You can only select " + args.maximum + " items";
            }
        }
    };
    
    const config = $.extend({}, defaults, options);
    
    return $(selector).select2(config);
}

/**
 * Initialize Select2 with AJAX data loading
 * 
 * @param {string} selector - jQuery selector
 * @param {string} url - API endpoint URL
 * @param {object} options - Custom options including:
 *   - minimumInputLength: Minimum characters before search (default: 2)
 *   - delay: Search delay in ms (default: 250)
 *   - dataMapper: Function to map API response to Select2 format
 *   - paramMapper: Function to map search params
 * 
 * @example
 * initSelect2Ajax('#customerSelect', '/api/customers/search', {
 *     placeholder: 'Search customer by name or code...',
 *     minimumInputLength: 3
 * });
 * 
 * // With custom data mapping
 * initSelect2Ajax('#userSelect', '/api/users', {
 *     dataMapper: function(data) {
 *         return data.users.map(u => ({
 *             id: u.user_id,
 *             text: u.full_name + ' (' + u.email + ')'
 *         }));
 *     }
 * });
 */
function initSelect2Ajax(selector, url, options = {}) {
    const defaults = {
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Type to search...',
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: url,
            dataType: 'json',
            delay: 250,
            data: function(params) {
                // Default parameter mapping
                const defaultParams = {
                    q: params.term,
                    page: params.page || 1
                };
                
                // Allow custom parameter mapping
                if (options.paramMapper && typeof options.paramMapper === 'function') {
                    return options.paramMapper(params);
                }
                
                return defaultParams;
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                
                // Custom data mapper if provided
                if (options.dataMapper && typeof options.dataMapper === 'function') {
                    return {
                        results: options.dataMapper(data),
                        pagination: {
                            more: data.has_more || false
                        }
                    };
                }
                
                // Default: expect {results: [...], total_count: N} format
                return {
                    results: data.results || data,
                    pagination: {
                        more: (params.page * 30) < (data.total_count || data.length)
                    }
                };
            },
            cache: true
        }
    };
    
    // Remove custom options that are not Select2 native
    const customOptions = {...options};
    delete customOptions.dataMapper;
    delete customOptions.paramMapper;
    
    const config = $.extend(true, {}, defaults, customOptions);
    
    return $(selector).select2(config);
}

/**
 * Show loading state on button with spinner
 * 
 * @param {string} selector - Button selector
 * @param {string} text - Loading text (default: 'Loading...')
 * 
 * @example
 * btnLoading('#saveButton', 'Saving...');
 * 
 * // Later, to reset:
 * btnReset('#saveButton');
 */
function btnLoading(selector, text = 'Loading...') {
    const $btn = $(selector);
    
    // Store original content if not already stored
    if (!$btn.data('original-html')) {
        $btn.data('original-html', $btn.html());
        $btn.data('original-disabled', $btn.prop('disabled'));
    }
    
    // Set loading state
    $btn.prop('disabled', true);
    $btn.html(`
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        ${text}
    `);
}

/**
 * Reset button from loading state to original state
 * 
 * @param {string} selector - Button selector
 * 
 * @example
 * btnReset('#saveButton');
 */
function btnReset(selector) {
    const $btn = $(selector);
    
    // Restore original state
    const originalHtml = $btn.data('original-html');
    const originalDisabled = $btn.data('original-disabled');
    
    if (originalHtml !== undefined) {
        $btn.html(originalHtml);
        $btn.prop('disabled', originalDisabled || false);
        
        // Clean up data
        $btn.removeData('original-html');
        $btn.removeData('original-disabled');
    }
}

/**
 * Show toast notification (requires SweetAlert2 or Bootstrap Toast)
 * 
 * @param {string} message - Notification message
 * @param {string} type - Type: 'success', 'error', 'warning', 'info' (default: 'info')
 * @param {object} options - Additional options:
 *   - title: Toast title
 *   - duration: Display duration in ms (default: 3000)
 *   - position: Toast position (default: 'top-end')
 * 
 * @example
 * showToast('Data saved successfully!', 'success');
 * showToast('Please fix the errors', 'error', {title: 'Validation Failed'});
 */
function showToast(message, type = 'info', options = {}) {
    const defaults = {
        title: '',
        duration: 3000,
        position: 'top-end'
    };
    
    const config = {...defaults, ...options};
    
    // Check if SweetAlert2 is available
    if (typeof Swal !== 'undefined') {
        const iconMap = {
            'success': 'success',
            'error': 'error',
            'warning': 'warning',
            'info': 'info'
        };
        
        const Toast = Swal.mixin({
            toast: true,
            position: config.position,
            showConfirmButton: false,
            timer: config.duration,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
        
        Toast.fire({
            icon: iconMap[type] || 'info',
            title: config.title || message,
            text: config.title ? message : ''
        });
    } else {
        // Fallback to console if SweetAlert2 not available
        console.log(`[${type.toUpperCase()}] ${config.title ? config.title + ': ' : ''}${message}`);
    }
}

/**
 * Show confirmation dialog before action
 * 
 * @param {object} options - Dialog options:
 *   - title: Dialog title (default: 'Are you sure?')
 *   - text: Dialog text
 *   - confirmText: Confirm button text (default: 'Yes')
 *   - cancelText: Cancel button text (default: 'Cancel')
 *   - type: Dialog type: 'warning', 'error', 'info', 'question' (default: 'warning')
 *   - onConfirm: Callback function on confirm
 *   - onCancel: Callback function on cancel (optional)
 * 
 * @example
 * confirmAction({
 *     title: 'Delete Customer?',
 *     text: 'This action cannot be undone',
 *     type: 'error',
 *     confirmText: 'Delete',
 *     onConfirm: function() {
 *         // Delete customer
 *     }
 * });
 */
function confirmAction(options = {}) {
    const defaults = {
        title: 'Are you sure?',
        text: '',
        confirmText: 'Yes',
        cancelText: 'Cancel',
        type: 'warning',
        onConfirm: null,
        onCancel: null
    };
    
    const config = {...defaults, ...options};
    
    if (window.OptimaConfirm && typeof window.OptimaConfirm.generic === 'function') {
        const modalEl = document.getElementById('optimaConfirmModal');
        let confirmed = false;

        // Support onCancel by detecting when the modal is closed without confirming.
        // (OptimaConfirm modal currently only exposes onConfirm, not onCancel.)
        if (modalEl) {
            const onHidden = function() {
                modalEl.removeEventListener('hidden.bs.modal', onHidden);
                if (!confirmed && typeof config.onCancel === 'function') config.onCancel();
            };
            modalEl.addEventListener('hidden.bs.modal', onHidden);
        }

        const icon =
            config.type === 'error' ? 'warning' :
            config.type === 'info' ? 'info' :
            config.type === 'success' ? 'success' :
            config.type;

        window.OptimaConfirm.generic({
            title: config.title,
            messageHtml: config.text,
            icon: icon,
            confirmText: config.confirmText,
            cancelText: config.cancelText,
            confirmButtonColor: '#3085d6',
            onConfirm: function() {
                confirmed = true;
                if (typeof config.onConfirm === 'function') config.onConfirm();
            }
        });
    } else {
        // Fallback to native confirm
        if (confirm(`${config.title}\n${config.text}`)) {
            if (config.onConfirm) config.onConfirm();
        } else {
            if (config.onCancel) config.onCancel();
        }
    }
}

/**
 * Enable client-side form validation with Bootstrap styles
 * 
 * @param {string} formSelector - Form selector
 * @param {object} options - Validation options:
 *   - onSubmit: Callback on valid submit
 *   - onInvalid: Callback on invalid submit
 * 
 * @example
 * enableFormValidation('#myForm', {
 *     onSubmit: function(form) {
 *         // Submit via AJAX
 *         return false; // Prevent default form submit
 *     },
 *     onInvalid: function(form) {
 *         showToast('Please fix the errors', 'error');
 *     }
 * });
 */
function enableFormValidation(formSelector, options = {}) {
    const form = document.querySelector(formSelector);
    
    if (!form) {
        console.error(`Form not found: ${formSelector}`);
        return;
    }
    
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            
            if (options.onInvalid) {
                options.onInvalid(form);
            }
        } else {
            if (options.onSubmit) {
                const result = options.onSubmit(form);
                if (result === false) {
                    event.preventDefault();
                }
            }
        }
        
        form.classList.add('was-validated');
    }, false);
}

/**
 * Clear form validation state
 * 
 * @param {string} formSelector - Form selector
 * 
 * @example
 * clearFormValidation('#myForm');
 */
function clearFormValidation(formSelector) {
    const form = document.querySelector(formSelector);
    
    if (form) {
        form.classList.remove('was-validated');
        
        // Clear all invalid feedback
        form.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        
        form.querySelectorAll('.invalid-feedback').forEach(el => {
            el.textContent = '';
        });
    }
}

/**
 * Show loading overlay on element
 * 
/**
 * Show loading overlay on an element
 * Note: For page-level loading, use #pageLoading in base.php
 * This is ONLY for component-specific loading (cards, modals, etc.)
 * 
 * @param {string} selector - Element selector
 * @param {string} message - Loading message (default: 'Loading...')
 * 
 * @example
 * showLoadingOverlay('#dataCard', 'Loading data...');
 * 
 * // Later:
 * hideLoadingOverlay('#dataCard');
 */
function showLoadingOverlay(selector, message = 'Loading...') {
    const $el = $(selector);
    
    // Skip if element is body or html (use page loading instead)
    if ($el.is('body, html')) {
        console.warn('showLoadingOverlay: Use #pageLoading for page-level loading');
        return;
    }
    
    if ($el.find('.component-loading-overlay').length === 0) {
        const overlay = $(`
            <div class="component-loading-overlay" style="
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(255,255,255,0.9);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 1050;
                border-radius: inherit;
            ">
                <div class="text-center">
                    <div class="spinner-border text-primary mb-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="small text-muted">${message}</div>
                </div>
            </div>
        `);
        
        // Set parent position to relative if not already positioned
        const currentPosition = $el.css('position');
        if (currentPosition === 'static') {
            $el.css('position', 'relative');
        }
        
        $el.append(overlay);
    }
}

/**
 * Hide loading overlay
 * 
 * @param {string} selector - Element selector
 */
function hideLoadingOverlay(selector) {
    $(selector).find('.component-loading-overlay').remove();
}

/**
 * Copy text to clipboard
 * 
 * @param {string} text - Text to copy
 * @param {string} successMessage - Success notification message
 * 
 * @example
 * copyToClipboard('QUO-2026-001', 'Quotation number copied!');
 */
function copyToClipboard(text, successMessage = 'Copied to clipboard!') {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
            showToast(successMessage, 'success');
        }, function(err) {
            console.error('Could not copy text: ', err);
            showToast('Failed to copy', 'error');
        });
    } else {
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        
        try {
            document.execCommand('copy');
            showToast(successMessage, 'success');
        } catch (err) {
            console.error('Fallback copy failed: ', err);
            showToast('Failed to copy', 'error');
        }
        
        document.body.removeChild(textarea);
    }
}

/**
 * Debounce function - delays execution until after wait time
 * 
 * @param {function} func - Function to debounce
 * @param {number} wait - Wait time in ms
 * @returns {function} Debounced function
 * 
 * @example
 * const searchCustomers = debounce(function(query) {
 *     // Perform search AJAX
 * }, 300);
 * 
 * $('#searchInput').on('keyup', function() {
 *     searchCustomers(this.value);
 * });
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Format number with thousand separators
 * 
 * @param {number} num - Number to format
 * @param {number} decimals - Number of decimal places (default: 0)
 * @returns {string} Formatted number
 * 
 * @example
 * formatNumber(1234567); // "1,234,567"
 * formatNumber(1234.567, 2); // "1,234.57"
 */
function formatNumber(num, decimals = 0) {
    return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(num);
}

/**
 * Format currency (Indonesian Rupiah)
 * 
 * @param {number} amount - Amount to format
 * @param {boolean} showSymbol - Show "Rp" symbol (default: true)
 * @returns {string} Formatted currency
 * 
 * @example
 * formatCurrency(1000000); // "Rp 1.000.000"
 * formatCurrency(1500000, false); // "1.500.000"
 */
function formatCurrency(amount, showSymbol = true) {
    const formatted = new Intl.NumberFormat('id-ID').format(amount);
    return showSymbol ? `Rp ${formatted}` : formatted;
}

// Export for ES modules (if needed)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        initSelect2,
        initSelect2Ajax,
        btnLoading,
        btnReset,
        showToast,
        confirmAction,
        enableFormValidation,
        clearFormValidation,
        showLoadingOverlay,
        hideLoadingOverlay,
        copyToClipboard,
        debounce,
        formatNumber,
        formatCurrency
    };
}
