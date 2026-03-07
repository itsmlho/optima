/**
 * ============================================================================
 * OPTIMA Sidebar - CodePen Style Enhancement JavaScript
 * ============================================================================
 * JavaScript untuk mendukung CSS CodePen enhancement:
 * - Toggle collapse/expand
 * - Mobile sidebar overlay
 * - Active menu highlight
 * - Smooth animations
 * 
 * @requires jQuery 3.6+
 * @version 1.0.0
 * @date March 6, 2026
 * ============================================================================
 */

(function($) {
    'use strict';

    /**
     * ========================================================================
     * SIDEBAR CODEPEN ENHANCEMENT CLASS
     * ========================================================================
     */
    class SidebarCodepenEnhance {
        constructor() {
            this.sidebar = $('.sidebar.sidebar-enhanced');
            this.body = $('body');
            this.toggleBtn = null;
            this.mobileToggleBtn = null;
            this.overlay = null;
            this.isCollapsed = false;
            this.isMobile = $(window).width() <= 768;

            this.init();
        }

        /**
         * Initialize enhancement
         */
        init() {
            if (!this.sidebar.length) {
                console.warn('Sidebar tidak ditemukan, CodePen enhancement tidak aktif');
                return;
            }

            this.createToggleButton();
            this.createMobileElements();
            this.attachEventListeners();
            this.initializeActiveMenu();
            this.loadSavedState();

            console.log('✅ Sidebar CodePen Enhancement initialized');
        }

        /**
         * Create toggle button di sidebar header
         */
        createToggleButton() {
            // Cari atau buat toggle button
            let toggleBtn = this.sidebar.find('.sidebar-toggle-btn');
            
            if (!toggleBtn.length) {
                const brand = this.sidebar.find('.sidebar-brand');
                if (brand.length) {
                    toggleBtn = $('<div class="sidebar-toggle-btn"></div>');
                    toggleBtn.html('<div class="sidebar-toggle-burger"></div>');
                    brand.append(toggleBtn);
                }
            }

            this.toggleBtn = toggleBtn;
        }

        /**
         * Create mobile toggle button dan overlay
         */
        createMobileElements() {
            // Mobile toggle button
            if (!$('.nav-mobile-toggle').length) {
                this.mobileToggleBtn = $(`
                    <div class="nav-mobile-toggle">
                        <i class="fas fa-bars"></i>
                    </div>
                `);
                $('body').append(this.mobileToggleBtn);
            } else {
                this.mobileToggleBtn = $('.nav-mobile-toggle');
            }

            // Overlay
            if (!$('.sidebar-overlay').length) {
                this.overlay = $('<div class="sidebar-overlay"></div>');
                $('body').append(this.overlay);
            } else {
                this.overlay = $('.sidebar-overlay');
            }
        }

        /**
         * Attach event listeners
         */
        attachEventListeners() {
            const self = this;

            // Desktop toggle button
            if (this.toggleBtn) {
                this.toggleBtn.off('click').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    self.toggleSidebar();
                });
            }

            // Mobile toggle button
            if (this.mobileToggleBtn) {
                this.mobileToggleBtn.off('click').on('click', function(e) {
                    e.preventDefault();
                    self.toggleMobileSidebar();
                });
            }

            // Overlay click - close sidebar
            if (this.overlay) {
                this.overlay.off('click').on('click', function() {
                    self.closeMobileSidebar();
                });
            }

            // Handle window resize
            $(window).off('resize.sidebarCodepen').on('resize.sidebarCodepen', function() {
                const wasMobile = self.isMobile;
                self.isMobile = $(window).width() <= 768;

                // Reset states ketika breakpoint berubah
                if (wasMobile !== self.isMobile) {
                    if (!self.isMobile) {
                        // Pindah dari mobile ke desktop
                        self.closeMobileSidebar();
                    } else {
                        // Pindah dari desktop ke mobile
                        self.body.removeClass('sidebar-collapsed');
                        self.isCollapsed = false;
                    }
                }
            });

            // Handle menu clicks
            this.sidebar.find('.nav-link').off('click.codepen').on('click.codepen', function() {
                self.setActiveMenu($(this));
                
                // Close mobile sidebar after click
                if (self.isMobile) {
                    setTimeout(() => {
                        self.closeMobileSidebar();
                    }, 300);
                }
            });

            // Handle dropdown toggles
            this.sidebar.find('.nav-dropdown-toggle').off('click.codepen').on('click.codepen', function(e) {
                if (self.isCollapsed && !self.isMobile) {
                    // Expand sidebar ketika click dropdown saat collapsed
                    self.toggleSidebar();
                }
            });
        }

        /**
         * Toggle sidebar collapse/expand (desktop)
         */
        toggleSidebar() {
            if (this.isMobile) return;

            this.isCollapsed = !this.isCollapsed;

            if (this.isCollapsed) {
                this.body.addClass('sidebar-collapsed');
            } else {
                this.body.removeClass('sidebar-collapsed');
            }

            // Save state to localStorage
            this.saveState();

            // Trigger custom event
            $(document).trigger('sidebarToggled', [this.isCollapsed]);
        }

        /**
         * Toggle mobile sidebar
         */
        toggleMobileSidebar() {
            if (!this.isMobile) return;

            if (this.sidebar.hasClass('mobile-show')) {
                this.closeMobileSidebar();
            } else {
                this.openMobileSidebar();
            }
        }

        /**
         * Open mobile sidebar
         */
        openMobileSidebar() {
            this.sidebar.addClass('mobile-show');
            this.overlay.addClass('show');
            this.body.css('overflow', 'hidden');
        }

        /**
         * Close mobile sidebar
         */
        closeMobileSidebar() {
            this.sidebar.removeClass('mobile-show');
            this.overlay.removeClass('show');
            this.body.css('overflow', '');
        }

        /**
         * Set active menu item
         */
        setActiveMenu($menuItem) {
            // Remove active from all items
            this.sidebar.find('.nav-link, .nav-dropdown-item').removeClass('active');

            // Add active to clicked item
            $menuItem.addClass('active');

            // Save active menu to localStorage
            const menuHref = $menuItem.attr('href');
            if (menuHref) {
                localStorage.setItem('optima_active_menu', menuHref);
            }
        }

        /**
         * Initialize active menu berdasarkan current URL
         */
        initializeActiveMenu() {
            const currentPath = window.location.pathname;
            const savedMenu = localStorage.getItem('optima_active_menu');

            // Cari menu yang match dengan current URL
            let $activeMenu = null;

            this.sidebar.find('.nav-link, .nav-dropdown-item').each(function() {
                const href = $(this).attr('href');
                if (href && currentPath.includes(href)) {
                    $activeMenu = $(this);
                    return false; // break
                }
            });

            // Fallback ke saved menu
            if (!$activeMenu && savedMenu) {
                $activeMenu = this.sidebar.find(`[href="${savedMenu}"]`);
            }

            // Set active
            if ($activeMenu && $activeMenu.length) {
                this.setActiveMenu($activeMenu);

                // Expand parent dropdown jika ada
                const $parentDropdown = $activeMenu.closest('.collapse');
                if ($parentDropdown.length) {
                    $parentDropdown.addClass('show');
                }
            }
        }

        /**
         * Save sidebar state ke localStorage
         */
        saveState() {
            localStorage.setItem('optima_sidebar_collapsed', this.isCollapsed ? '1' : '0');
        }

        /**
         * Load saved state dari localStorage
         */
        loadSavedState() {
            if (this.isMobile) return;

            const savedState = localStorage.getItem('optima_sidebar_collapsed');
            if (savedState === '1') {
                this.isCollapsed = true;
                this.body.addClass('sidebar-collapsed');
            }
        }

        /**
         * Public method: Collapse sidebar
         */
        collapse() {
            if (!this.isCollapsed && !this.isMobile) {
                this.toggleSidebar();
            }
        }

        /**
         * Public method: Expand sidebar
         */
        expand() {
            if (this.isCollapsed && !this.isMobile) {
                this.toggleSidebar();
            }
        }

        /**
         * Public method: Destroy enhancement
         */
        destroy() {
            // Remove event listeners
            $(window).off('resize.sidebarCodepen');
            this.sidebar.find('.nav-link').off('click.codepen');
            this.sidebar.find('.nav-dropdown-toggle').off('click.codepen');

            // Remove created elements
            if (this.mobileToggleBtn) this.mobileToggleBtn.remove();
            if (this.overlay) this.overlay.remove();

            // Remove classes
            this.body.removeClass('sidebar-collapsed');
            this.sidebar.removeClass('mobile-show');

            console.log('✅ Sidebar CodePen Enhancement destroyed');
        }
    }

    /**
     * ========================================================================
     * INITIALIZATION
     * ========================================================================
     */
    $(document).ready(function() {
        // Initialize enhancement
        window.sidebarCodepenEnhance = new SidebarCodepenEnhance();

        // Expose public API
        window.SidebarCodepen = {
            toggle: function() {
                window.sidebarCodepenEnhance.toggleSidebar();
            },
            collapse: function() {
                window.sidebarCodepenEnhance.collapse();
            },
            expand: function() {
                window.sidebarCodepenEnhance.expand();
            },
            destroy: function() {
                window.sidebarCodepenEnhance.destroy();
            }
        };
    });

    /**
     * ========================================================================
     * SMOOTH SCROLL TO ACTIVE MENU (Optional Enhancement)
     * ========================================================================
     */
    $(document).on('sidebarToggled', function(e, isCollapsed) {
        // Scroll to active menu item jika ada
        const $activeMenu = $('.sidebar .nav-link.active, .sidebar .nav-dropdown-item.active').first();
        if ($activeMenu.length && !isCollapsed) {
            setTimeout(function() {
                const $navContainer = $('.sidebar-nav');
                const activeOffset = $activeMenu.offset().top;
                const containerOffset = $navContainer.offset().top;
                const scrollPosition = activeOffset - containerOffset - 100;

                $navContainer.animate({
                    scrollTop: scrollPosition
                }, 400);
            }, 350);
        }
    });

})(jQuery);
