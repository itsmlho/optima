/*!
 * OPTIMA Global Performance Optimization v1.0.0
 * Ultra-fast performance for DataTables, Modals, and Forms
 * Copyright 2024 PT Sarana Mitra Luas Tbk
 */

(function() {
    'use strict';

    console.log('🚀 OPTIMA Global Performance Optimization loaded');

    // GLOBAL PERFORMANCE CONFIGURATION
    window.OPTIMA_PERF = {
        // DataTable settings
        DATATABLE_SEARCH_DELAY: 1200,        // Increased delay for comfortable typing
        DATATABLE_PAGE_LENGTH: 15,
        DATATABLE_PROCESSING_DELAY: 300,      // Slightly longer processing delay
        
        // Modal settings
        MODAL_LAZY_LOAD_DELAY: 150,           // Increased delay for smoother UX
        MODAL_CONTENT_CLEAR_DELAY: 2000,      // Longer delay before clearing
        MODAL_ANIMATION_DURATION: 200,        // Slightly longer animations
        
        // Form settings
        FORM_VALIDATION_DELAY: 1500,          // Longer validation delay
        FORM_SUBMIT_TIMEOUT: 3000,
        
        // Global settings
        NOTIFICATION_DURATION: 4000,          // Longer notification display
        DEBUG_MODE: false
    };

    // UTILITY FUNCTIONS
    window.OptimaPerfUtils = {
        // Enhanced debounce with immediate option
        debounce: function(func, wait, immediate) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    timeout = null;
                    if (!immediate) func.apply(this, args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(this, args);
            };
        },

        // Performance-optimized throttle
        throttle: function(func, limit) {
            let inThrottle;
            return function(...args) {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },

        // Fast DOM ready check
        ready: function(fn) {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', fn);
            } else {
                fn();
            }
        },

        // Performance-optimized notification
        showNotification: function(message, type = 'info', duration = window.OPTIMA_PERF.NOTIFICATION_DURATION) {
            const alertClass = type === 'error' ? 'danger' : type;
            const notification = document.createElement('div');
            notification.className = `alert alert-${alertClass} alert-dismissible fade show position-fixed no-transition`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
            if (duration > 0) {
                setTimeout(() => {
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 100);
                }, duration);
            }

            return notification;
        },

        // Fast element visibility check
        isVisible: function(element) {
            return element.offsetWidth > 0 && element.offsetHeight > 0;
        },

        // Performance logger
        log: function(message, data) {
            if (window.OPTIMA_PERF.DEBUG_MODE) {
                console.log(`[OPTIMA-PERF] ${message}`, data || '');
            }
        }
    };

    // GLOBAL DATATABLE OPTIMIZATION
    window.OptimaDataTableManager = {
        // Enhanced DataTable defaults
        getOptimalDefaults: function() {
            return {
                processing: true,
                deferRender: false, // Show table immediately
                pageLength: window.OPTIMA_PERF.DATATABLE_PAGE_LENGTH,
                lengthMenu: [[10, 15, 25, 50], [10, 15, 25, 50]],
                stateSave: false,
                autoWidth: false,
                search: {
                    smart: false,
                    caseInsensitive: true
                },
                dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
                language: {
                    processing: '<div class="d-flex align-items-center justify-content-center p-3"><div class="spinner-border spinner-border-sm me-2"></div>Loading...</div>',
                    emptyTable: 'No data available',
                    zeroRecords: 'No matching records found',
                    loadingRecords: 'Loading...',
                    search: 'Search:',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    paginate: { "first": "First", "last": "Last", "next": "Next", "previous": "Previous" }
                },
                columnDefs: [{
                    targets: '_all',
                    orderable: true,
                    searchable: true
                }],
                drawCallback: function(settings) {
                    OptimaPerfUtils.log('DataTable drawn', settings.fnRecordsDisplay() + ' records');
                },
                initComplete: function(settings, json) {
                    OptimaPerfUtils.log('DataTable initialized', this.api().table().node().id);
                }
            };
        },

        // Global search optimization
        optimizeSearch: function() {
            const debouncedSearch = OptimaPerfUtils.debounce(function(table, searchTerm) {
                if (table.search() !== searchTerm) {
                    table.search(searchTerm).draw();
                }
            }, window.OPTIMA_PERF.DATATABLE_SEARCH_DELAY);

            $(document).on('keyup.optima-perf', '.dataTables_filter input', function() {
                const table = $(this).closest('.dataTables_wrapper').find('table').DataTable();
                const searchTerm = this.value;
                debouncedSearch(table, searchTerm);
            });

            OptimaPerfUtils.log('DataTable search optimization applied');
        },

        // Auto-optimize existing DataTables
        autoOptimize: function() {
            if (typeof $.fn.DataTable !== 'undefined') {
                $(document).on('init.dt.optima-perf', function(e, settings) {
                    const tableId = settings.nTable.id || 'unnamed';
                    OptimaPerfUtils.log('Auto-optimizing DataTable', tableId);
                    
                    // Force fixed table layout
                    $(settings.nTable).css('table-layout', 'fixed');
                    
                    // Optimize wrapper
                    $(settings.nTableWrapper).addClass('optimize-rendering');
                });
            }
        }
    };

    // GLOBAL MODAL OPTIMIZATION
    window.OptimaModalManager = {
        // Optimize all modals on page
        optimizeAll: function() {
            document.querySelectorAll('.modal').forEach(modal => {
                this.optimizeSingle(modal);
            });
            
            this.setupGlobalEvents();
            OptimaPerfUtils.log('All modals optimized');
        },

        // Optimize single modal
        optimizeSingle: function(modal) {
            modal.setAttribute('data-lazy-loaded', 'false');
            modal.setAttribute('data-optimized', 'true');
            
            // Remove fade class for instant modals
            modal.classList.remove('fade');
            
            // Optimize dialog
            const dialog = modal.querySelector('.modal-dialog');
            if (dialog) {
                dialog.style.transition = 'none';
                dialog.style.transform = 'none';
            }
        },

        // Setup global modal events
        setupGlobalEvents: function() {
            // Optimized show event
            $(document).off('show.bs.modal.optima-perf').on('show.bs.modal.optima-perf', '.modal', function(e) {
                const modal = $(this);
                const modalId = modal.attr('id') || 'unnamed';
                
                OptimaPerfUtils.log('Opening modal', modalId);
                
                // Immediate loading state
                const modalBody = modal.find('.modal-body');
                if (modalBody.length && modalBody.html().trim() === '') {
                    modalBody.html('<div class="text-center p-4"><div class="spinner-border" role="status"></div><p class="mt-2">Loading...</p></div>');
                }
                
                // Lazy load content with comfortable delay
                const dataUrl = modal.attr('data-url');
                if (dataUrl && modal.attr('data-lazy-loaded') === 'false') {
                    setTimeout(() => {
                        this.loadModalContent(modal, dataUrl);
                    }, window.OPTIMA_PERF.MODAL_LAZY_LOAD_DELAY); // 150ms for smoother UX
                }
            });

            // Optimized hide event
            $(document).off('hidden.bs.modal.optima-perf').on('hidden.bs.modal.optima-perf', '.modal', function(e) {
                const modal = $(this);
                
                // Clear content after delay for performance
                setTimeout(() => {
                    const modalBody = modal.find('.modal-body');
                    if (modalBody.children().length > 5) {
                        modalBody.html('<div class="text-center p-2">Content cleared</div>');
                        modal.attr('data-lazy-loaded', 'false');
                    }
                }, window.OPTIMA_PERF.MODAL_CONTENT_CLEAR_DELAY);
            });
        },

        // Load modal content with optimization
        loadModalContent: function(modal, url) {
            const modalBody = modal.find('.modal-body');
            
            $.ajax({
                url: url,
                timeout: 10000,
                cache: false,
                success: function(response) {
                    modalBody.html(response);
                    modal.attr('data-lazy-loaded', 'true');
                    OptimaPerfUtils.log('Modal content loaded', url);
                },
                error: function() {
                    modalBody.html('<div class="alert alert-danger">Failed to load content</div>');
                    OptimaPerfUtils.log('Modal content load failed', url);
                }
            });
        }
    };

    // GLOBAL FORM OPTIMIZATION
    window.OptimaFormManager = {
        // Setup global form optimizations
        optimize: function() {
            this.setupValidation();
            this.setupSubmission();
            OptimaPerfUtils.log('Form optimization applied');
        },

        // Debounced form validation
        setupValidation: function() {
            const debouncedValidation = OptimaPerfUtils.debounce(function(input) {
                input.classList.remove('is-invalid', 'is-valid');
            }, window.OPTIMA_PERF.FORM_VALIDATION_DELAY);

            $(document).on('input.optima-perf', 'form input, form select, form textarea', function() {
                debouncedValidation(this);
            });
        },

        // Optimized form submission
        setupSubmission: function() {
            $(document).on('submit.optima-perf', 'form[data-ajax="true"]', function(e) {
                e.preventDefault();
                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');
                
                // Prevent double submission
                if (submitBtn.prop('disabled')) return false;
                
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true)
                       .html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');
                
                setTimeout(() => {
                    submitBtn.prop('disabled', false).html(originalText);
                }, window.OPTIMA_PERF.FORM_SUBMIT_TIMEOUT);
            });
        }
    };

    // GLOBAL EVENT MANAGER
    window.OptimaEventManager = {
        // Setup optimized global events
        setup: function() {
            // Optimized click delegation
            $(document).off('click.optima-perf').on('click.optima-perf', '[data-action]', function(e) {
                e.preventDefault();
                const action = $(this).data('action');
                const target = $(this).data('target') || $(this).attr('href');
                
                switch(action) {
                    case 'modal':
                        if (target) {
                            const modal = $(target);
                            if (modal.length) {
                                modal.modal('show');
                            }
                        }
                        break;
                    case 'refresh':
                        if (typeof refreshData === 'function') {
                            refreshData();
                        } else {
                            location.reload();
                        }
                        break;
                    case 'delete':
                        if (confirm('Are you sure you want to delete this item?')) {
                            OptimaPerfUtils.log('Delete action', target);
                        }
                        break;
                }
            });

            // Throttled scroll optimization
            const throttledScroll = OptimaPerfUtils.throttle(function() {
                // Handle scroll events efficiently
                document.querySelectorAll('[data-scroll-optimize]').forEach(el => {
                    el.style.willChange = 'auto';
                });
            }, 100);

            window.addEventListener('scroll', throttledScroll);

            OptimaPerfUtils.log('Global events optimized');
        }
    };

    // INITIALIZATION
    OptimaPerfUtils.ready(function() {
        console.log('🚀 Initializing OPTIMA Global Performance...');
        
        // Apply all optimizations
        OptimaDataTableManager.optimizeSearch();
        OptimaDataTableManager.autoOptimize();
        OptimaModalManager.optimizeAll();
        OptimaFormManager.optimize();
        OptimaEventManager.setup();
        
        // Set global DataTable defaults if available
        if (typeof $.fn.DataTable !== 'undefined') {
            $.extend(true, $.fn.dataTable.defaults, OptimaDataTableManager.getOptimalDefaults());
        }

        console.log('✅ OPTIMA Global Performance optimization complete');
        
        // Performance monitoring
        setTimeout(() => {
            const performanceInfo = {
                dom_elements: document.querySelectorAll('*').length,
                dataTables: document.querySelectorAll('.dataTables_wrapper').length,
                modals: document.querySelectorAll('.modal').length,
                memory_used: performance.memory ? Math.round(performance.memory.usedJSHeapSize / 1024 / 1024) + 'MB' : 'N/A'
            };
            
            OptimaPerfUtils.log('Performance Stats', performanceInfo);
        }, 2000);
    });

})();