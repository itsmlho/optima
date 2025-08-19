/*!
 * Start Bootstrap - SB Admin Pro v6.0.0 (https://startbootstrap.com/theme/sb-admin-pro)
 * Copyright 2013-2021 Start Bootstrap
 * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin-pro/blob/master/LICENSE)
 */

// OPTIMA Asset Management System - SB Admin Pro JavaScript

(function($) {
    "use strict";

    // Start of use strict

    // Initialize everything when DOM is ready
    $(document).ready(function() {
        initializeOptima();
    });

    function initializeOptima() {
        initEventListeners();
        initSidebar();
        initTopbar();
        initComponents();
        initDataTables();
        initTooltips();
        initModals();
        initForms();
        initCharts();
        initNotifications();
        initTheme();
        initSearch();
        initProgressBars();
        initCounters();
        initScrollbars();
        initFileUploads();
        initializeDivisionTheme(); // Add division theme initialization
    }

    /**
     * Initialize event listeners
     */
    initEventListeners() {
        document.addEventListener('DOMContentLoaded', () => {
            this.handleResponsiveDesign();
            this.handleSidebarToggle();
            this.handleDropdowns();
            this.handleTabs();
            this.handleAccordions();
            this.handleFilters();
        });

        window.addEventListener('resize', () => {
            this.handleResponsiveDesign();
        });
    }

    /**
     * Initialize sidebar functionality
     */
    initSidebar() {
        const sidebar = document.querySelector('.pro-sidebar');
        const sidebarToggle = document.querySelector('.pro-topbar-toggle');
        const main = document.querySelector('.pro-main');
        const topbar = document.querySelector('.pro-topbar');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
                
                // Store sidebar state
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                
                // Dispatch custom event
                window.dispatchEvent(new CustomEvent('sidebarToggle', {
                    detail: { collapsed: sidebar.classList.contains('collapsed') }
                }));
            });
        }

        // Restore sidebar state
        const sidebarCollapsed = localStorage.getItem('sidebarCollapsed');
        if (sidebarCollapsed === 'true') {
            sidebar.classList.add('collapsed');
        }

        // Handle submenu toggles
        const toggles = document.querySelectorAll('.pro-nav-toggle');
        toggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                const target = toggle.getAttribute('data-bs-target');
                const submenu = document.querySelector(target);
                
                if (submenu) {
                    const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
                    toggle.setAttribute('aria-expanded', !isExpanded);
                    submenu.classList.toggle('show');
                }
            });
        });
    }

    /**
     * Initialize topbar functionality
     */
    initTopbar() {
        // Handle user dropdown
        const userDropdown = document.querySelector('.pro-user-dropdown');
        if (userDropdown) {
            userDropdown.addEventListener('click', (e) => {
                e.preventDefault();
                const dropdown = userDropdown.nextElementSibling;
                if (dropdown) {
                    dropdown.classList.toggle('show');
                }
            });
        }

        // Handle notifications
        const notificationBtn = document.querySelector('[data-bs-toggle="notifications"]');
        if (notificationBtn) {
            notificationBtn.addEventListener('click', () => {
                this.showNotifications();
            });
        }

        // Handle search
        const searchBtn = document.querySelector('[data-bs-toggle="search"]');
        if (searchBtn) {
            searchBtn.addEventListener('click', () => {
                this.showSearch();
            });
        }
    }

    /**
     * Initialize components
     */
    initComponents() {
        // Initialize Bootstrap components
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

        // Initialize custom components
        this.initTimeline();
        this.initProgressBars();
        this.initCounters();
    }

    /**
     * Initialize DataTables
     */
    initDataTables() {
        if (typeof DataTable !== 'undefined') {
            const tables = document.querySelectorAll('.pro-datatable');
            tables.forEach(table => {
                new DataTable(table, {
                    responsive: true,
                    pageLength: 25,
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    language: {
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "Showing 0 to 0 of 0 entries",
                        infoFiltered: "(filtered from _MAX_ total entries)",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    },
                    dom: '<"row"<"col-md-6"l><"col-md-6"f>><"row"<"col-md-12"t>><"row"<"col-md-5"i><"col-md-7"p>>',
                    buttons: [
                        {
                            extend: 'csv',
                            className: 'btn btn-outline-primary btn-sm'
                        },
                        {
                            extend: 'excel',
                            className: 'btn btn-outline-success btn-sm'
                        },
                        {
                            extend: 'pdf',
                            className: 'btn btn-outline-danger btn-sm'
                        },
                        {
                            extend: 'print',
                            className: 'btn btn-outline-info btn-sm'
                        }
                    ]
                });
            });
        }
    }

    /**
     * Initialize tooltips
     */
    initTooltips() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(tooltipTriggerEl => {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    /**
     * Initialize modals
     */
    initModals() {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.addEventListener('show.bs.modal', (e) => {
                const button = e.relatedTarget;
                if (button) {
                    // Handle dynamic modal content
                    const title = button.getAttribute('data-bs-title');
                    const content = button.getAttribute('data-bs-content');
                    
                    if (title) {
                        const modalTitle = modal.querySelector('.modal-title');
                        if (modalTitle) modalTitle.textContent = title;
                    }
                    
                    if (content) {
                        const modalBody = modal.querySelector('.modal-body');
                        if (modalBody) modalBody.innerHTML = content;
                    }
                }
            });
        });
    }

    /**
     * Initialize forms
     */
    initForms() {
        // Enhanced form validation
        const forms = document.querySelectorAll('.needs-validation');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });

        // Auto-resize textareas
        const textareas = document.querySelectorAll('textarea[data-auto-resize]');
        textareas.forEach(textarea => {
            textarea.addEventListener('input', () => {
                textarea.style.height = 'auto';
                textarea.style.height = textarea.scrollHeight + 'px';
            });
        });

        // Character counter
        const charCounters = document.querySelectorAll('[data-char-counter]');
        charCounters.forEach(input => {
            const maxLength = input.getAttribute('maxlength');
            const counter = document.createElement('div');
            counter.className = 'char-counter text-muted small mt-1';
            counter.textContent = `0/${maxLength}`;
            input.parentNode.appendChild(counter);

            input.addEventListener('input', () => {
                const currentLength = input.value.length;
                counter.textContent = `${currentLength}/${maxLength}`;
                
                if (currentLength > maxLength * 0.8) {
                    counter.classList.add('text-warning');
                } else {
                    counter.classList.remove('text-warning');
                }
            });
        });
    }

    /**
     * Initialize charts
     */
    initCharts() {
        if (typeof Chart !== 'undefined') {
            // Initialize chart defaults
            Chart.defaults.font.family = 'Inter, sans-serif';
            Chart.defaults.color = '#6b7280';
            Chart.defaults.borderColor = '#e5e7eb';
            Chart.defaults.backgroundColor = 'rgba(99, 102, 241, 0.1)';

            // Initialize charts
            const chartElements = document.querySelectorAll('.pro-chart');
            chartElements.forEach(element => {
                const chartType = element.getAttribute('data-chart-type');
                const chartData = JSON.parse(element.getAttribute('data-chart-data'));
                const chartOptions = JSON.parse(element.getAttribute('data-chart-options') || '{}');

                new Chart(element, {
                    type: chartType,
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        ...chartOptions
                    }
                });
            });
        }
    }

    /**
     * Initialize notifications
     */
    initNotifications() {
        this.notificationQueue = [];
        this.notificationContainer = this.createNotificationContainer();
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `pro-notification pro-notification-${type}`;
        notification.innerHTML = `
            <div class="pro-notification-header">
                <h6 class="pro-notification-title">${this.getNotificationTitle(type)}</h6>
                <button class="pro-notification-close" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="pro-notification-body">${message}</div>
        `;

        this.notificationContainer.appendChild(notification);

        // Show notification
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        // Auto-remove notification
        setTimeout(() => {
            this.removeNotification(notification);
        }, duration);

        // Handle close button
        const closeBtn = notification.querySelector('.pro-notification-close');
        closeBtn.addEventListener('click', () => {
            this.removeNotification(notification);
        });

        return notification;
    }

    /**
     * Remove notification
     */
    removeNotification(notification) {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }

    /**
     * Create notification container
     */
    createNotificationContainer() {
        let container = document.querySelector('.pro-notification-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'pro-notification-container';
            container.style.cssText = `
                position: fixed;
                top: 2rem;
                right: 2rem;
                z-index: 1050;
                max-width: 400px;
            `;
            document.body.appendChild(container);
        }
        return container;
    }

    /**
     * Get notification title
     */
    getNotificationTitle(type) {
        const titles = {
            success: 'Success',
            warning: 'Warning',
            danger: 'Error',
            info: 'Information'
        };
        return titles[type] || 'Notification';
    }

    /**
     * Initialize theme
     */
    initTheme() {
        const themeToggle = document.querySelector('[data-bs-toggle="theme"]');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                this.toggleTheme();
            });
        }

        // Load saved theme
        const savedTheme = localStorage.getItem('optima-theme');
        if (savedTheme) {
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        }
    }

    /**
     * Toggle theme
     */
    toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-bs-theme', newTheme);
        localStorage.setItem('optima-theme', newTheme);
        
        // Dispatch custom event
        window.dispatchEvent(new CustomEvent('themeChange', {
            detail: { theme: newTheme }
        }));
    }

    /**
     * Initialize search
     */
    initSearch() {
        const searchInputs = document.querySelectorAll('.pro-search-input');
        searchInputs.forEach(input => {
            const resultsContainer = input.parentNode.querySelector('.pro-search-results');
            let searchTimeout;

            input.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.performSearch(e.target.value, resultsContainer);
                }, 300);
            });

            input.addEventListener('focus', () => {
                if (resultsContainer) {
                    resultsContainer.classList.add('show');
                }
            });

            document.addEventListener('click', (e) => {
                if (!input.contains(e.target) && !resultsContainer.contains(e.target)) {
                    resultsContainer.classList.remove('show');
                }
            });
        });
    }

    /**
     * Perform search
     */
    performSearch(query, resultsContainer) {
        if (!query.trim()) {
            resultsContainer.classList.remove('show');
            return;
        }

        // Simulate search results
        const results = [
            { title: 'Dashboard', subtitle: 'Overview and analytics', url: '/dashboard' },
            { title: 'Users', subtitle: 'User management', url: '/users' },
            { title: 'Settings', subtitle: 'System configuration', url: '/settings' },
            { title: 'Reports', subtitle: 'Data reports and analytics', url: '/reports' }
        ].filter(item => 
            item.title.toLowerCase().includes(query.toLowerCase()) ||
            item.subtitle.toLowerCase().includes(query.toLowerCase())
        );

        resultsContainer.innerHTML = '';
        results.forEach(result => {
            const resultElement = document.createElement('div');
            resultElement.className = 'pro-search-result';
            resultElement.innerHTML = `
                <div class="pro-search-result-title">${result.title}</div>
                <div class="pro-search-result-subtitle">${result.subtitle}</div>
            `;
            resultElement.addEventListener('click', () => {
                window.location.href = result.url;
            });
            resultsContainer.appendChild(resultElement);
        });

        resultsContainer.classList.add('show');
    }

    /**
     * Initialize progress bars
     */
    initProgressBars() {
        const progressBars = document.querySelectorAll('.pro-progress-bar[data-percent]');
        progressBars.forEach(bar => {
            const percent = parseInt(bar.getAttribute('data-percent'));
            const animate = bar.getAttribute('data-animate') !== 'false';
            
            if (animate) {
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = percent + '%';
                }, 100);
            } else {
                bar.style.width = percent + '%';
            }
        });
    }

    /**
     * Initialize counters
     */
    initCounters() {
        const counters = document.querySelectorAll('[data-counter]');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        });

        counters.forEach(counter => {
            observer.observe(counter);
        });
    }

    /**
     * Animate counter
     */
    animateCounter(element) {
        const target = parseInt(element.getAttribute('data-counter'));
        const duration = parseInt(element.getAttribute('data-duration') || '2000');
        const start = 0;
        const increment = target / (duration / 16);
        let current = start;

        const timer = setInterval(() => {
            current += increment;
            element.textContent = Math.floor(current).toLocaleString();
            
            if (current >= target) {
                element.textContent = target.toLocaleString();
                clearInterval(timer);
            }
        }, 16);
    }

    /**
     * Initialize scrollbars
     */
    initScrollbars() {
        if (typeof SimpleBar !== 'undefined') {
            const scrollElements = document.querySelectorAll('.pro-scroll');
            scrollElements.forEach(element => {
                new SimpleBar(element);
            });
        }
    }

    /**
     * Initialize file uploads
     */
    initFileUploads() {
        const fileInputs = document.querySelectorAll('input[type="file"].pro-file-input');
        fileInputs.forEach(input => {
            const dropZone = input.parentNode.querySelector('.pro-drop-zone');
            if (dropZone) {
                this.initDropZone(input, dropZone);
            }
        });
    }

    /**
     * Initialize drop zone
     */
    initDropZone(input, dropZone) {
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            input.files = e.dataTransfer.files;
            this.displayFilePreview(input);
        });

        input.addEventListener('change', () => {
            this.displayFilePreview(input);
        });
    }

    /**
     * Display file preview
     */
    displayFilePreview(input) {
        const previewContainer = input.parentNode.querySelector('.pro-file-preview');
        if (!previewContainer) return;

        previewContainer.innerHTML = '';
        
        Array.from(input.files).forEach(file => {
            const preview = document.createElement('div');
            preview.className = 'pro-file-preview-item';
            preview.innerHTML = `
                <div class="pro-file-info">
                    <i class="fas fa-file"></i>
                    <span>${file.name}</span>
                    <small>${this.formatFileSize(file.size)}</small>
                </div>
                <button type="button" class="pro-file-remove">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            preview.querySelector('.pro-file-remove').addEventListener('click', () => {
                preview.remove();
                // Update file input
                const dt = new DataTransfer();
                Array.from(input.files).forEach(f => {
                    if (f !== file) dt.items.add(f);
                });
                input.files = dt.files;
            });
            
            previewContainer.appendChild(preview);
        });
    }

    /**
     * Format file size
     */
    formatFileSize(bytes) {
        const units = ['B', 'KB', 'MB', 'GB'];
        let size = bytes;
        let unitIndex = 0;
        
        while (size >= 1024 && unitIndex < units.length - 1) {
            size /= 1024;
            unitIndex++;
        }
        
        return `${size.toFixed(1)} ${units[unitIndex]}`;
    }

    /**
     * Handle responsive design
     */
    handleResponsiveDesign() {
        const sidebar = document.querySelector('.pro-sidebar');
        const isMobile = window.innerWidth <= 768;
        
        if (isMobile) {
            sidebar.classList.add('mobile');
        } else {
            sidebar.classList.remove('mobile');
        }
    }

    /**
     * Handle sidebar toggle
     */
    handleSidebarToggle() {
        const overlay = document.querySelector('.pro-sidebar-overlay');
        if (!overlay) {
            const overlayDiv = document.createElement('div');
            overlayDiv.className = 'pro-sidebar-overlay';
            overlayDiv.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            `;
            document.body.appendChild(overlayDiv);
            
            overlayDiv.addEventListener('click', () => {
                document.querySelector('.pro-sidebar').classList.remove('show');
                overlayDiv.style.display = 'none';
            });
        }
    }

    /**
     * Handle dropdowns
     */
    handleDropdowns() {
        const dropdowns = document.querySelectorAll('.dropdown-toggle');
        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('click', (e) => {
                e.preventDefault();
                const menu = dropdown.nextElementSibling;
                if (menu) {
                    menu.classList.toggle('show');
                }
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });
    }

    /**
     * Handle tabs
     */
    handleTabs() {
        const tabLinks = document.querySelectorAll('.pro-tab-link');
        tabLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const target = link.getAttribute('data-bs-target');
                const tabContent = document.querySelector(target);
                
                if (tabContent) {
                    // Remove active class from all tabs
                    document.querySelectorAll('.pro-tab-link').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.pro-tab-content').forEach(c => c.classList.remove('active'));
                    
                    // Add active class to current tab
                    link.classList.add('active');
                    tabContent.classList.add('active');
                }
            });
        });
    }

    /**
     * Handle accordions
     */
    handleAccordions() {
        const accordionButtons = document.querySelectorAll('.pro-accordion-button');
        accordionButtons.forEach(button => {
            button.addEventListener('click', () => {
                const target = button.getAttribute('data-bs-target');
                const content = document.querySelector(target);
                
                if (content) {
                    const isExpanded = button.getAttribute('aria-expanded') === 'true';
                    button.setAttribute('aria-expanded', !isExpanded);
                    button.classList.toggle('collapsed');
                    content.classList.toggle('show');
                }
            });
        });
    }

    /**
     * Handle filters
     */
    handleFilters() {
        const filterToggles = document.querySelectorAll('.pro-filter-toggle');
        filterToggles.forEach(toggle => {
            toggle.addEventListener('click', () => {
                const filterBody = toggle.closest('.pro-filter').querySelector('.pro-filter-body');
                if (filterBody) {
                    filterBody.style.display = filterBody.style.display === 'none' ? 'grid' : 'none';
                }
            });
        });
    }

    /**
     * Initialize timeline
     */
    initTimeline() {
        const timelineItems = document.querySelectorAll('.pro-timeline-item');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateX(0)';
                }
            });
        });

        timelineItems.forEach(item => {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-20px)';
            item.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(item);
        });
    }

    /**
     * Utility methods
     */
    utils = {
        /**
         * Format currency
         */
        formatCurrency(amount, currency = 'USD') {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: currency
            }).format(amount);
        },

        /**
         * Format date
         */
        formatDate(date, options = {}) {
            return new Intl.DateTimeFormat('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                ...options
            }).format(new Date(date));
        },

        /**
         * Format number
         */
        formatNumber(number) {
            return new Intl.NumberFormat('en-US').format(number);
        },

        /**
         * Debounce function
         */
        debounce(func, delay) {
            let timeoutId;
            return function (...args) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => func.apply(this, args), delay);
            };
        },

        /**
         * Throttle function
         */
        throttle(func, delay) {
            let timeoutId;
            let lastExecTime = 0;
            return function (...args) {
                const currentTime = Date.now();
                if (currentTime - lastExecTime > delay) {
                    func.apply(this, args);
                    lastExecTime = currentTime;
                } else {
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(() => {
                        func.apply(this, args);
                        lastExecTime = Date.now();
                    }, delay - (currentTime - lastExecTime));
                }
            };
        },

        /**
         * Copy to clipboard
         */
        copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                this.showNotification('Copied to clipboard!', 'success', 2000);
            });
        },

        /**
         * Generate random ID
         */
        generateId() {
            return Math.random().toString(36).substr(2, 9);
        },

        /**
         * Validate email
         */
        validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        },

        /**
         * Validate phone
         */
        validatePhone(phone) {
            const re = /^[\+]?[1-9][\d]{0,15}$/;
            return re.test(phone);
        }
    };
}

// Initialize Bootstrap Pro
const bootstrapPro = new BootstrapPro();

// Global functions for easy access
window.ProNotification = (message, type, duration) => {
    return bootstrapPro.showNotification(message, type, duration);
};

window.ProUtils = bootstrapPro.utils;

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = BootstrapPro;
}

/**
 * OPTIMA Division Theme Detection and Application
 * Automatically applies division-based themes based on current URL
 * Compatible with templates/pro_layout system
 */
function initializeDivisionTheme() {
    const currentPath = window.location.pathname.toLowerCase();
    const body = document.body;
    
    // Remove any existing division attributes
    body.removeAttribute('data-division');
    body.classList.remove('service-theme', 'admin-theme', 'marketing-theme', 'finance-theme', 'warehouse-theme', 'dashboard-theme', 'reports-theme', 'rental-theme', 'customers-theme', 'system-theme');
    
    // Detect division based on URL path
    if (currentPath.includes('/service/') || currentPath.includes('/service')) {
        body.setAttribute('data-division', 'service');
        body.classList.add('service-theme');
        console.log('🎨 Service theme applied via pro.js');
    } else if (currentPath.includes('/admin/') || currentPath.includes('/admin')) {
        body.setAttribute('data-division', 'admin');
        body.classList.add('admin-theme');
        console.log('🎨 Admin theme applied via pro.js');
    } else if (currentPath.includes('/marketing/') || currentPath.includes('/marketing')) {
        body.setAttribute('data-division', 'marketing');
        body.classList.add('marketing-theme');
        console.log('🎨 Marketing theme applied via pro.js');
    } else if (currentPath.includes('/finance/') || currentPath.includes('/finance')) {
        body.setAttribute('data-division', 'finance');
        body.classList.add('finance-theme');
        console.log('🎨 Finance theme applied via pro.js');
    } else if (currentPath.includes('/warehouse/') || currentPath.includes('/warehouse')) {
        body.setAttribute('data-division', 'warehouse');
        body.classList.add('warehouse-theme');
        console.log('🎨 Warehouse theme applied via pro.js');
    } else if (currentPath.includes('/dashboard/') || currentPath.includes('/dashboard') || currentPath.includes('dashboard')) {
        body.setAttribute('data-division', 'dashboard');
        body.classList.add('dashboard-theme');
        console.log('🎨 Dashboard theme applied via pro.js');
    } else if (currentPath.includes('/reports/') || currentPath.includes('/reports')) {
        body.setAttribute('data-division', 'reports');
        body.classList.add('reports-theme');
        console.log('🎨 Reports theme applied via pro.js');
    } else if (currentPath.includes('/rental/') || currentPath.includes('/rental')) {
        body.setAttribute('data-division', 'rental');
        body.classList.add('rental-theme');
        console.log('🎨 Rental theme applied via pro.js');
    } else if (currentPath.includes('/customers/') || currentPath.includes('/customers')) {
        body.setAttribute('data-division', 'customers');
        body.classList.add('customers-theme');
        console.log('🎨 Customers theme applied via pro.js');
    } else if (currentPath.includes('/system/') || currentPath.includes('/system')) {
        body.setAttribute('data-division', 'system');
        body.classList.add('system-theme');
        console.log('🎨 System theme applied via pro.js');
    }
    
    // Add animation class for smooth transitions
    body.classList.add('theme-transition');
    
    // Show theme application feedback
    if (body.hasAttribute('data-division')) {
        const division = body.getAttribute('data-division');
        setTimeout(() => {
            console.log(`✅ OPTIMA ${division.toUpperCase()} division theme successfully applied!`);
        }, 300);
    }
}

// Initialize theme on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    initializeDivisionTheme();
});

// Re-apply theme on page navigation (for SPA-like behavior)
window.addEventListener('popstate', function() {
    initializeDivisionTheme();
}); 